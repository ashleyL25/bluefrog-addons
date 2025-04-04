<?php
/**
 * Update plugin
 *
 * @see https://github.com/omarabid/Self-Hosted-WordPress-Plugin-repository
 */

/**
 * Class WP_AutoUpdate
 */
class Bluefrog_Addons_AutoUpdate {
    /**
     * The plugin current version
     * @var string
     */
    private $current_version;

    /**
     * The plugin remote update path
     * @var string
     */
    private $update_path;

    /**
     * Plugin Slug (plugin_directory/plugin_file.php)
     * @var string
     */
    private $plugin_slug;

    /**
     * Plugin name (plugin_file)
     * @var string
     */
    private $slug;

    /**
     * License User
     * @var string
     */
    private $license_user;

    /**
     * License Key
     * @var string
     */
    private $license_key;

    /**
     * Initialize a new instance of the WordPress Auto-Update class
     *
     * @param string $current_version
     * @param string $update_path
     * @param string $plugin_slug
     * @param string $license_user
     * @param string $license_key
     */
    public function __construct( $current_version, $update_path, $plugin_slug, $license_user = '', $license_key = '' ) {
        // Set the class public variables
        $this->current_version = $current_version;
        $this->update_path     = $update_path;

        // Set the License
        $this->license_user = $license_user;
        $this->license_key  = $license_key;

        // Set the Plugin Slug
        $this->plugin_slug = $plugin_slug;
        list ( $t1, $t2 ) = explode( '/', $plugin_slug );
        $this->slug = str_replace( '.php', '', $t2 );

        // define the alternative API for updating checking
        add_filter( 'pre_set_site_transient_update_plugins', array( &$this, 'check_update' ) );

        // Define the alternative response for information checking
        add_filter( 'plugins_api', array( &$this, 'check_info' ), 10, 3 );
    }

    /**
     * Add our self-hosted autoupdate plugin to the filter transient
     *
     * @param $transient
     *
     * @return object $transient
     */
    public function check_update( $transient ) {
        if ( empty( $transient->checked ) ) {
            return $transient;
        }
    
        // Get the remote version
        $remote_version = $this->get_remote( 'version' );
    
        // Check if the remote version is valid
        if ( is_null( $remote_version ) || !isset( $remote_version->new_version ) ) {
            error_log( 'Error: Remote version is null or new_version is not set.' );
            return $transient;
        }
    
        // If a newer version is available, add the update
        if ( version_compare( $this->current_version, $remote_version->new_version, '<' ) ) {
            $obj = new stdClass();
            $obj->slug = $this->slug;
            $obj->new_version = $remote_version->new_version;
            $obj->url = $remote_version->url;
            $obj->plugin = $this->plugin_slug;
            $obj->package = $remote_version->package;
            $transient->response[ $this->plugin_slug ] = $obj;
        }
    
        return $transient;
    }
    

    /**
     * Add our self-hosted description to the filter
     *
     * @param boolean $obj
     * @param array   $action
     * @param object  $arg
     *
     * @return bool|object
     */
    public function check_info( $obj, $action, $arg ) {
        if ( ( $action == 'query_plugins' || $action == 'plugin_information' ) && isset( $arg->slug ) && $arg->slug === $this->slug ) {
            $info = $this->get_remote( 'info' );
            $info->sections = (array) $info->sections;

            return $info;
        }

        return $obj;
    }

    /**
     * Return the remote version
     *
     * @param string $action
     *
     * @return object|string
     */
    public function get_remote( $action = '' ) {
        $params = array(
            'timeout' => 45,
            'body' => array(
                'action' => $action,
                'plugin' => $this->slug,
                'license_user' => $this->license_user,
                'license_key' => $this->license_key,
            ),
        );
    
        // Make the POST request
        $request = wp_remote_post( $this->update_path, $params );
    
        if ( is_wp_error( $request ) ) {
            error_log( 'WP Error: ' . $request->get_error_message() );
            return null;
        }
    
        // Check if response is valid
        if ( wp_remote_retrieve_response_code( $request ) !== 200 ) {
            error_log( 'HTTP Error: Invalid response code ' . wp_remote_retrieve_response_code( $request ) );
            return null;
        }
    
        // Decode the response body
        $response_body = wp_remote_retrieve_body( $request );
        $decoded_body = json_decode( $response_body );
    
        if ( json_last_error() !== JSON_ERROR_NONE ) {
            error_log( 'JSON Decode Error: ' . json_last_error_msg() );
            return null;
        }
    
        return $decoded_body;
    }
    
}
