<?php
/**
 * Plugin Name: Simple Slick Sliders
 * Plugin URI:  https://www.nickdiego.com/plugins/simple-slick-sliders
 * Description: Simply add Slick powered Sliders to your WordPress website
 * Author:      Nick Diego
 * Author URI:  http://www.nickdiego.com
 * Version:     0.1.0
 * Text Domain: simple-slick-sliders
 * Domain Path: languages
 *
 * Blox is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Blox is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Blox. If not, visit <http://www.gnu.org/licenses/>.
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Main plugin class.
 *
 * @since 1.0.0
 *
 * @package	Simple Slick Sliders
 * @author 	Nick Diego
 */
class Simple_Slick_Sliders_Main {

    /**
     * Holds the class object.
     *
     * @since 1.0.0
     *
     * @var object
     */
    public static $instance;

    /**
     * Plugin version, used for cache-busting of style and script file references.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $version = '0.1.0';

    /**
     * The name of the plugin.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $plugin_name = 'Simple Slick Sliders';

    /**
     * The unique slug of the plugin.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $plugin_slug = 'simple-slick-sliders';

    /**
     * Plugin file.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $file = __FILE__;

    /**
     * Primary class constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {

        // Fire a hook before the class is setup.
        do_action( 'sss_pre_init' );

        // Load the plugin textdomain.
        add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

        // Load the plugin.
        add_action( 'init', array( $this, 'init' ), 0 );

        // Add additional links to the plugin's row on the admin plugin page
        //add_filter( 'plugin_action_links', array( $this, 'plugin_action_links' ), 10, 2 );
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
    }


    /**
     * Loads the plugin textdomain for translation.
     *
     * @since 1.0.0
     */
    public function load_textdomain() {

        load_plugin_textdomain( 'simple-slick-sliders', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }


    /**
     * Loads the plugin into WordPress.
     *
     * @since 1.0.0
     */
    public function init() {

        // Run hook once the plugin has been initialized.
        do_action( 'sss_init' );

       	// Plugin utility classes
        require plugin_dir_path( __FILE__ ) . 'includes/global/posttype.php';
		require plugin_dir_path( __FILE__ ) . 'includes/global/common.php';

		// Settings class
		//require plugin_dir_path( __FILE__ ) . 'includes/global/settings.php';

        // Slideshow content types
        require plugin_dir_path( __FILE__ ) . 'includes/global/slider-type/standard.php';

        // Load admin only components.
        if ( is_admin() ) {

			// Main admin classes
			//require plugin_dir_path( __FILE__ ) . 'includes/admin/posttype.php';

            // Metabox class
            require plugin_dir_path( __FILE__ ) . 'includes/admin/metaboxes.php';

            require plugin_dir_path( __FILE__ ) . 'includes/admin/slider.php';
            //require plugin_dir_path( __FILE__ ) . 'includes/admin/controls.php';

        }

        // Load frontend only components.
        if ( ! is_admin() ) {

        	// Class for generating all frontend markup
			//require plugin_dir_path( __FILE__ ) . 'includes/frontend/frontend.php';
        }
    }


    /**
	 * Adds link to General Settings page in plugin row action links
	 *
	 * @since 1.0.0
	 *
	 * @param array $links  Already defined action links
	 * @param string $file  Plugin file path and name being processed
	 * @return array $links The new array of action links
	 */
	public function plugin_action_links( $links, $file ) {
		$settings_link = '<a href="' . admin_url( 'edit.php?post_type=simple-slick-sliders&page=sss-settings' ) . '">' . __( 'Settings', 'simple-slick-sliders' ) . '</a>';

		if ( $file == 'simple-slick-sliders/simple-slick-sliders.php' ) {
			array_unshift( $links, $settings_link );
		}

		return $links;
	}


	/**
	 * Adds additional links to the plugin row meta links
	 *
	 * @since 1.0.0
	 *
	 * @param array $links   Already defined meta links
	 * @param string $file   Plugin file path and name being processed
	 * @return array $links  The new array of meta links
	 */
	public function plugin_row_meta( $links, $file ) {

		// If we are not on the correct plugin, abort
		if ( $file != 'simple-slick-sliders/simple-slick-sliders.php' ) {
			return $links;
		}

		$docs_link = esc_url( add_query_arg( array(
				'utm_source'   => 'simple-slick-sliders',
				'utm_medium'   => 'plugin',
				'utm_campaign' => 'sss_Plugin_Links',
				'utm_content'  => 'plugins-page-link'
			), 'https://www.nickdiego.com/plugins/simple-slick-sliders' )
		);

		$new_links = array(
			'<a href="' . $docs_link . '" target="_blank">' . esc_html__( 'Documentation', 'simple-slick-sliders' ) . '</a>',
		);

		$links = array_merge( $links, $new_links );

		return $links;
	}


    /**
     * Returns the singleton instance of the class.
     *
     * @since 1.0.0
     *
     * @return object The class object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Simple_Slick_Sliders_Main ) ) {
            self::$instance = new Simple_Slick_Sliders_Main();
        }

        return self::$instance;

    }
}

// Load the main plugin class.
$blox_main = Simple_Slick_Sliders_Main::get_instance();
