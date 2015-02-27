<?php
/******************************************************************************************

 Enables easy activation of plugins
  
 Plugin Name:     America Plugin Manager
 Description:     Plugin to allow easy install and activation of america plugins
 Version:         0.0.1
 Author:          Office of Design, Bureau of International Information Programs
 License:         GPL-2.0+
 Text Domain:     america
 Domain Path:     /languages
 
 ****************************************************************************************** */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

 class America_Plugin_Manager {

    const VERSION   = '0.0.1';
        
    /** single instance of class */
    private static $instance    = null;
    private $america_plugins    = array();

    /**
     * Creates or returns an instance of this class.
     *
     * @return  single instance of class
     */
    public static function apm_get_instance() {
 
        if ( null == self::$instance ) {
            self::$instance = new self;
        }
 
        return self::$instance;
    } 

    public function apm_bootstrap() {
        register_activation_hook( __FILE__,  array( $this, 'apm_activate' ) );

        add_action( 'init',                         array( $this, 'apm_init' ) );
        add_action( 'admin_init',                   array( $this, 'apm_admin_init' ) );
        add_action( 'admin_menu',                   array( $this, 'apm_register_settings_page' ) );
        add_action( 'wp_ajax_apm_activate_plugin',  array( $this, 'ajax_handler' ) );
    }

    // Internationalization
    function apm_load_plugin_textdomain () {
        load_plugin_textdomain ( 'america', FALSE, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

    function apm_activate() {
    }

    public function apm_register_settings_page() {
        // create new top-level menu
        add_menu_page( 'America', 'America', 'manage_options', __FILE__, array( $this, 'apm_settings_page' ), '', 6 );
    }


    public function apm_settings_page() {
        //Set Your Nonce
        $ajax_nonce = wp_create_nonce( "my-special-string" );
        ?>

        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $("a[data-action]").on("click", function ( event ) {
                   event.preventDefault();

                   var plugin = $(this).attr("data-action");

                $.post (
                    ajaxurl, 
                    {
                        'action': 'apm_activate_plugin',
                        'data':   'plugin'
                    }, 
                    function( response ){
                        console.log(response);
                    }
                );
            });
            });
        </script>

        <div class="wrap">
            <h2>Activate America Plugins</h2>
            <div><ul>
                <?php
                    foreach ( $this->america_plugins as $plugin ) {
                        $plugin = $plugin['name'];
                        echo "<a href='#' data-action='$plugin'>$plugin</a><br>";
                    }
                ?>
            </ul></div>
            
           
        </div><!-- wrap -->
        <?php
    }

    public function apm_init() {
        $this->apm_load_plugin_textdomain();
    }

    public function apm_admin_init() {
        $this->america_plugins = array (
            'america-developer' => array(
                'name'          => esc_html__( 'America Developer', 'america' ),
                'active'        => class_exists( 'America_Publication_Post_Type' ),
            ),
            'america_publication_post_type' => array(
                'name'          => esc_html__( 'America Publication Post Type', 'america' ),
                'active'        => class_exists( 'America_Publication_Post_Type' ),
            ),
        );
    }

    public function ajax_handler( $action ) {
        $response = json_encode( $_POST );
        $path =  plugin_dir_path( __FILE__ )  . 'america_publication_post_type/america_publication_post_type.php';
        activate_plugin($path);
        
       
        // if ( empty( $_POST['path'] ) )
        //     die( __( 'ERROR: No slug was passed to the AJAX callback.', 'a8c-developer' ) );

        // check_ajax_referer( 'a8c_developer_activate_plugin_' . $_POST['path'] );

        // if ( ! current_user_can( 'activate_plugins' ) )
        //     die( __( 'ERROR: You lack permissions to activate plugins.', 'a8c-developer' ) );

        // $activate_result = activate_plugin( $_POST['path'] );

        // if ( is_wp_error( $activate_result ) )
        //     die( sprintf( __( 'ERROR: Failed to activate plugin: %s', 'a8c-developer' ), $activate_result->get_error_message() ) );

        // exit( '1' );
    }

    public function apm_toggle_plugin () {

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
        } else {

            // Activate plugin
            activate_plugin( $my_plugin );
        }
    }
    
 }

 $apm_manger = America_Plugin_Manager::apm_get_instance();
 $apm_manger->apm_bootstrap();
