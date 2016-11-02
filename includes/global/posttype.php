<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Posttype class.
 *
 * @since 	1.0.0
 *
 * @package	Simple Slick Sliders
 * @author 	Nick Diego
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class SSS_Posttype {

    /**
     * Holds the class object.
     *
     * @since 1.0.0
     *
     * @var object
     */
    public static $instance;


    /**
     * Path to the file.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $file = __FILE__;


    /**
     * Holds the base class object.
     *
     * @since 1.0.0
     *
     * @var object
     */
    public $base;


    /**
     * Primary class constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {

        // Load the base class object.
        $this->base = Simple_Slick_Sliders_Main::get_instance();

        // Build the labels for the post type.
		$labels = apply_filters( 'sss_post_type_labels',
			array(
				'name'               => __( 'Simple Slick Sliders', 'simple-slick-sliders' ),
				'singular_name'      => __( 'Slider', 'simple-slick-sliders' ),
				'add_new'            => __( 'Add New', 'simple-slick-sliders' ),
				'add_new_item'       => __( 'Add New Slider', 'simple-slick-sliders' ),
				'edit_item'          => __( 'Edit Slider', 'simple-slick-sliders' ),
				'new_item'           => __( 'New Slider', 'simple-slick-sliders' ),
				'view_item'          => __( 'View Slider', 'simple-slick-sliders' ),
				'search_items'       => __( 'Search Sliders', 'simple-slick-sliders' ),
				'not_found'          => __( 'No sliders found.', 'simple-slick-sliders' ),
				'not_found_in_trash' => __( 'No sliders found in trash.', 'simple-slick-sliders' ),
				'parent_item_colon'  => '',
				'all_items'          => __( 'All Sliders', 'simple-slick-sliders' ),
				'menu_name'          => __( 'Slick Sliders', 'simple-slick-sliders' ),
			)
		);

		// Build out the post type arguments.
		$args = apply_filters( 'sss_post_type_args',
			array(
				'labels'              => $labels,
				'public'              => false,
				'exclude_from_search' => true,
				'show_ui'             => true,
				'show_in_admin_bar'   => false,
				'rewrite'             => false,
				'query_var'           => false,
				'menu_position'       => apply_filters( 'sss_post_type_menu_position', 248 ),
				'supports'            => array( 'title' )
			)
		);

		// Register the post type with WordPress.
		register_post_type( 'simple-slick-sliders', $args );

		// Check if the curent user has permission the manage global blocks, and remove the blocks from the admin if they don't
		add_action( 'admin_head', array( $this, 'global_permissions' ) );

		// Load global admin css.
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
    }


	/**
	 * Global Admin Styles
	 *
	 * Loads the CSS for the Blox admin styles including the Blox admin icon.
	 *
	 * @since 1.0.0
	 */
	public function admin_styles() {

	    // Load necessary admin styles
        wp_register_style( 'sss-admin-styles', plugins_url( 'assets/css/admin.css', $this->base->file ), array(), $this->base->version );
        wp_enqueue_style( 'sss-admin-styles' );

        // Fire a hook to load styles to the admin
        do_action( 'sss_admin_styles' );
	}


    /**
     * Global Admin Scripts
     *
     * Loads the JS for the Blox admin styles including quick edit functionality.
     *
     * @since 1.3.0
     */
    public function admin_scripts( $hook ) {

        // Only load our quickedit js on the edit pages for global blocks
        if ( 'edit.php' === $hook && isset( $_GET['post_type'] ) && 'simple-slick-sliders' === $_GET['post_type'] ) {
            // Load necessary admin scripts
            wp_register_script( 'sss-quickedit-scripts', plugins_url( 'assets/js/quickedit.js', $this->base->file ), array(), $this->base->version );
           	wp_enqueue_script( 'sss-quickedit-scripts' );
        }

        // Fire a hook to load scripts to the admin
        do_action( 'sss_admin_scripts' );
    }


    /**
     * Removes the global block options if the user does not have the required permissions
     *
     * @since 1.0.0
     */
    public function global_permissions() {

		// Get the global block permissions
		$global_permissions = blox_get_option( 'global_permissions', 'manage_options' );

		$global_permissions = ! empty( $global_permissions ) ? $global_permissions : 'manage_options';

		if ( ! current_user_can( $global_permissions ) ) {
			remove_menu_page( 'edit.php?post_type=simple-slick-sliders' );
		}
    }


    /**
     * Returns the singleton instance of the class.
     *
     * @since 1.0.0
     *
     * @return object The class object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof SSS_Posttype ) ) {
            self::$instance = new SSS_Posttype();
        }

        return self::$instance;
    }
}

// Load the posttype class.
$blox_posttype = SSS_Posttype::get_instance();
