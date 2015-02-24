<?php
/******************************************************************************************

 Enables easy activation of plugins
  
 Plugin Name:     America Plugin Manager
 Description:     Plugin to allow easy install and activation of america plugins
 Version:         0.0.1
 Author:          Office of Design, Bureau of International Information Programs
 License:         GPL-2.0+
 
 ****************************************************************************************** */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

 class America_Plugin_Manager {

    const VERSION = '0.0.1';

    function bootstrap() {
        register_activation_hook( __FILE__,             array( $this, 'apm_activate' ) );
    }

    function toggle_plugin() {

    // Full path to WordPress from the root
    $wordpress_path = '/full/path/to/wordpress/';

    // Absolute path to plugins dir
    $plugin_path = $wordpress_path.'wp-content/plugins/';

    // Absolute path to your specific plugin
    $my_plugin = $plugin_path.'my_plugin/my_plugin.php';

    // Check to see if plugin is already active
    if( is_plugin_active($my_plugin) ) {

        // Deactivate plugin
        // Note that deactivate_plugins() will also take an
        // array of plugin paths as a parameter instead of
        // just a single string.
        deactivate_plugins( $my_plugin );
    }
    else {

        // Activate plugin
        activate_plugin( $my_plugin );
    }
}
    
 }