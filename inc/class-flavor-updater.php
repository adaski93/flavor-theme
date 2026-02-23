<?php
/**
 * GitHub-based auto-updater for the Flavor theme.
 *
 * Checks GitHub Releases API for new versions and integrates
 * with the WordPress theme update system so updates appear
 * in Dashboard → Updates just like any wp.org theme.
 *
 * @package Flavor
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Flavor_Theme_Updater {

    /** @var string  GitHub owner/repo, e.g. "adaski93/flavor-theme" */
    private $repo;

    /** @var string  Current theme version. */
    private $version;

    /** @var string  Theme slug (directory name). */
    private $slug;

    /** @var object|null  Cached release data for this request. */
    private $release = null;

    /** @var string  Transient key for caching. */
    private $transient_key = 'flavor_theme_github_release';

    /**
     * @param string $github_repo  "owner/repo" on GitHub.
     */
    public function __construct( $github_repo ) {
        $this->repo    = $github_repo;
        $theme         = wp_get_theme( 'flavor' );
        $this->version = $theme->get( 'Version' );
        $this->slug    = 'flavor';

        add_filter( 'pre_set_site_transient_update_themes', array( $this, 'check_update' ) );
        add_filter( 'site_transient_update_themes',         array( $this, 'check_update' ) );
        add_filter( 'upgrader_post_install',                array( $this, 'post_install' ), 10, 3 );
    }

    /**
     * Fetch the latest release from GitHub API (cached in transient for 6 hours).
     *
     * @param bool $force  Skip cache.
     * @return object|false
     */
    private function get_latest_release( $force = false ) {
        if ( ! $force && $this->release !== null ) {
            return $this->release;
        }

        if ( ! $force ) {
            $cached = get_transient( $this->transient_key );
            if ( $cached !== false ) {
                $this->release = $cached;
                return $this->release;
            }
        }

        $url = 'https://api.github.com/repos/' . $this->repo . '/releases/latest';

        $response = wp_remote_get( $url, array(
            'timeout' => 15,
            'headers' => array(
                'Accept'     => 'application/vnd.github.v3+json',
                'User-Agent' => 'WordPress/' . get_bloginfo( 'version' ) . '; ' . home_url(),
            ),
        ) );

        if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
            $this->release = false;
            return false;
        }

        $body = json_decode( wp_remote_retrieve_body( $response ) );

        if ( empty( $body->tag_name ) ) {
            $this->release = false;
            return false;
        }

        $this->release = $body;
        set_transient( $this->transient_key, $body, 6 * HOUR_IN_SECONDS );

        return $this->release;
    }

    /**
     * @param string $tag
     * @return string
     */
    private function tag_to_version( $tag ) {
        return ltrim( preg_replace( '/^[^0-9]*/', '', $tag ), 'v' );
    }

    /**
     * @param object $release
     * @return string
     */
    private function get_zip_url( $release ) {
        if ( ! empty( $release->assets ) ) {
            foreach ( $release->assets as $asset ) {
                if ( substr( $asset->name, -4 ) === '.zip' ) {
                    return $asset->browser_download_url;
                }
            }
        }
        return $release->zipball_url;
    }

    /**
     * Tell WordPress a new theme version is available.
     *
     * @param object $transient
     * @return object
     */
    public function check_update( $transient ) {
        if ( ! is_object( $transient ) ) {
            $transient = new stdClass();
        }

        $release = $this->get_latest_release();
        if ( ! $release ) {
            return $transient;
        }

        $remote_version = $this->tag_to_version( $release->tag_name );

        if ( version_compare( $remote_version, $this->version, '>' ) ) {
            if ( ! isset( $transient->response ) || ! is_array( $transient->response ) ) {
                $transient->response = array();
            }
            $transient->response[ $this->slug ] = array(
                'theme'       => $this->slug,
                'new_version' => $remote_version,
                'url'         => $release->html_url,
                'package'     => $this->get_zip_url( $release ),
            );
        }

        return $transient;
    }

    /**
     * After upgrade, rename the extracted directory to match the theme slug.
     * Handles nested folders (e.g. flavor/flavor/) from GitHub ZIP structure.
     *
     * @param bool  $response
     * @param array $hook_extra
     * @param array $result
     * @return array
     */
    public function post_install( $response, $hook_extra, $result ) {
        if ( ! isset( $hook_extra['theme'] ) || $hook_extra['theme'] !== $this->slug ) {
            return $result;
        }

        global $wp_filesystem;

        $dest       = untrailingslashit( $result['destination'] );
        $theme_root = get_theme_root();
        $proper_dir = $theme_root . '/' . $this->slug;

        // Check for nested folder: dest/flavor/style.css
        $nested = $dest . '/' . $this->slug;
        if ( $wp_filesystem->is_dir( $nested ) && $wp_filesystem->exists( $nested . '/style.css' ) ) {
            // Move nested contents up: nested → temp → proper
            $tmp = $theme_root . '/' . $this->slug . '-tmp-' . time();
            $wp_filesystem->move( $nested, $tmp );
            $wp_filesystem->delete( $dest, true );
            if ( $wp_filesystem->is_dir( $proper_dir ) ) {
                $wp_filesystem->delete( $proper_dir, true );
            }
            $wp_filesystem->move( $tmp, $proper_dir );
            $result['destination'] = $proper_dir;
        } elseif ( $dest !== $proper_dir ) {
            // Standard case: just rename
            if ( $wp_filesystem->is_dir( $proper_dir ) ) {
                $wp_filesystem->delete( $proper_dir, true );
            }
            $wp_filesystem->move( $dest, $proper_dir );
            $result['destination'] = $proper_dir;
        }

        // Clear cache.
        delete_transient( $this->transient_key );

        // Re-activate.
        if ( wp_get_theme()->get_stylesheet() === $this->slug ) {
            switch_theme( $this->slug );
        }

        return $result;
    }
}
