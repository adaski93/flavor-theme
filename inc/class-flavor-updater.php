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

    /**
     * @param string $github_repo  "owner/repo" on GitHub.
     */
    public function __construct( $github_repo ) {
        $this->repo    = $github_repo;
        $theme         = wp_get_theme( 'flavor' );
        $this->version = $theme->get( 'Version' );
        $this->slug    = 'flavor';

        add_filter( 'pre_set_site_transient_update_themes', array( $this, 'check_update' ) );
        add_filter( 'upgrader_post_install',                array( $this, 'post_install' ), 10, 3 );
    }

    /**
     * Fetch the latest release from GitHub API (cached per request).
     *
     * @return object|false
     */
    private function get_latest_release() {
        if ( $this->release !== null ) {
            return $this->release;
        }

        $url = 'https://api.github.com/repos/' . $this->repo . '/releases/latest';

        $response = wp_remote_get( $url, array(
            'timeout' => 10,
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
        if ( empty( $transient->checked ) ) {
            return $transient;
        }

        $release = $this->get_latest_release();
        if ( ! $release ) {
            return $transient;
        }

        $remote_version = $this->tag_to_version( $release->tag_name );

        if ( version_compare( $remote_version, $this->version, '>' ) ) {
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

        $proper_dir = get_theme_root() . '/' . $this->slug;
        $wp_filesystem->move( $result['destination'], $proper_dir );
        $result['destination'] = $proper_dir;

        // Re-activate.
        if ( wp_get_theme()->get_stylesheet() === $this->slug ) {
            switch_theme( $this->slug );
        }

        return $result;
    }
}
