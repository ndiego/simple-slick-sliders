<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * The main metabox class which creates all admin metaboxes
 *
 * @since 	1.0.0
 *
 * @package	Simple Slick Sliders
 * @author 	Nick Diego
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public Licenses
 */
class SSS_Metaboxes {

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

        // Load metabox assets.
        add_action( 'admin_enqueue_scripts', array( $this, 'metabox_styles' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'metabox_scripts' ) );

        // Add the add block ajax action
		//add_action( 'wp_ajax_blox_add_block', array( $this, 'get_content_blocks' ) );

        // Load the metabox hooks and filters.
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 100 );

        // Add action to save metabox config options.
        //add_action( 'save_post', array( $this, 'local_blocks_save_meta' ), 10, 2 );
        add_action( 'save_post', array( $this, 'global_slider_save_meta' ), 10, 2 );
    }

    /**
     * Loads styles for our metaboxes.
     *
     * @since 1.0.0
     *
     * @return null Return early if not on the proper screen.
     */
    public function metabox_styles() {

        if ( isset( get_current_screen()->base ) && 'post' !== get_current_screen()->base ) {
            return;
        }

        // Load necessary metabox styles
        wp_register_style( $this->base->plugin_slug . '-metabox-styles', plugins_url( 'assets/css/metabox.css', $this->base->file ), array(), $this->base->version );
        wp_enqueue_style( $this->base->plugin_slug . '-metabox-styles' );

        // If on an Blox post type, add custom CSS for hiding specific things.
        if ( isset( get_current_screen()->post_type ) && 'simple-slick-sliders' == get_current_screen()->post_type ) {
            add_action( 'admin_head', array( $this, 'global_admin_css' ) );
        }

        // Fire a hook to load in custom metabox styles.
        do_action( 'sss_metabox_styles' );
    }


    /**
     * Hides unnecessary data in the Publish metabox on global post type screens.
     *
     * @since 1.0.0
     */
    public function global_admin_css() {

        ?>
        <style type="text/css">.misc-pub-section:not(.misc-pub-post-status) { display: none; }</style>
        <?php

        // Fire action for CSS on global Blox post type screens.
        do_action( 'sss_global_admin_css' );
    }


    /**
     * Loads scripts for our metaboxes.
     *
     * @since 1.0.0
     *
     * @global int $id      The current post ID.
     * @global object $post The current post object..
     * @return null         Return early if not on the proper screen.
     */
    public function metabox_scripts( $hook ) {

        global $id, $post;

        if ( isset( get_current_screen()->base ) && 'post' !== get_current_screen()->base ) {
            return;
        }

        // Set the post_id for localization. Not used currently, but keep for future use....
        // $post_id = isset( $post->ID ) ? $post->ID : (int) $id;

        // Load necessary metabox scripts
        wp_register_script( $this->base->plugin_slug . '-metabox-scripts', plugins_url( 'assets/js/metabox.js', $this->base->file ), array( 'jquery-ui-sortable' ), $this->base->version );
       	wp_enqueue_script( $this->base->plugin_slug . '-metabox-scripts' );

       	// Used for adding local blocks via ajax
        wp_localize_script(
        	$this->base->plugin_slug . '-metabox-scripts',
        	'sss_localize_metabox_scripts',
        	array(
        		'slideshow_media_title'		=> __( 'Choose or Upload an Image(s)', 'simple-slick-sliders' ),
        		'slideshow_media_button'	=> __( 'Insert Image(s)', 'simple-slick-sliders' ),
        		'slideshow_details'			=> __( 'Details', 'simple-slick-sliders' ),
        		'slideshow_remove'			=> __( 'Remove', 'simple-slick-sliders' ),
        		'slideshow_confirm_remove' 	=> __( 'Are you sure you want to remove this image from the slideshow? This action cannot be undone.', 'simple-slick-sliders' ),
        	)
        );

        // Allow the use of the media uploader on global blocks pages
        wp_enqueue_media( 'simple-slick-sliders' );

        // Fire a hook to load custom metabox scripts.
        do_action( 'sss_metabox_scripts' );
    }


    /**
     * Creates metaboxes for both local and global blocks
     *
     * @since 1.0.0
     */
    public function add_meta_boxes() {

		global $typenow;

		// Add the global block metabox
		if ( $typenow == 'simple-slick-sliders' ) {

			// Remove all unnecessary metaboxes, ones not added by this plugin
        	$this->remove_all_the_metaboxes();

            add_meta_box( 'global_slider_metabox', __( 'Slider Settings', 'blox' ), array( $this, 'global_slider_metabox_callback' ), 'simple-slick-sliders', 'normal', 'low' );
		}
    }


    /**
     * Removes all the metaboxes except the ones needed on the global blox custom post type
     * This function was authored Thomas Griffin, thanks!
     *
     * @since 1.0.0
     *
     * @global array $wp_meta_boxes Array of registered metaboxes.
     */
    public function remove_all_the_metaboxes() {

        global $wp_meta_boxes;

        // The post type to target
        $post_type  = 'simple-slick-sliders';

        // These are the metabox IDs we want to keep on the page
        $pass_over  = array( 'submitdiv', 'simple-slick-sliders' );

        // Check all metabox contexts
        $contexts   = array( 'normal', 'advanced', 'side' );

        // Check all metabox priorities
        $priorities = array( 'high', 'core', 'default', 'low' );

        // Loop through and target each context
        foreach ( $contexts as $context ) {

            // Now loop through each priority and start the purging process
            foreach ( $priorities as $priority ) {
                if ( isset( $wp_meta_boxes[$post_type][$context][$priority] ) ) {
                    foreach ( (array) $wp_meta_boxes[$post_type][$context][$priority] as $id => $metabox_data ) {

                        // If the metabox ID to pass over matches the ID given, remove it from the array and continue.
                        if ( in_array( $id, $pass_over ) ) {
                            unset( $pass_over[$id] );
                            continue;
                        }

                        // Otherwise, loop through the pass_over IDs and if we have a match, continue.
                        foreach ( $pass_over as $to_pass ) {
                            if ( preg_match( '#^' . $id . '#i', $to_pass ) ) {
                                continue;
                            }
                        }

                        // If we reach this point, remove the metabox completely.
                        unset( $wp_meta_boxes[$post_type][$context][$priority][$id] );
                    }
                }
            }
        }
    }


    /**
     * Callback for displaying content in the registered metabox.
     *
     * @since 1.0.0
     *
     * @param object $post The current post object.
     */
	public function global_slider_metabox_callback( $post ) {

		$slider_data = get_post_meta( $post->ID, '_sss_slider_data', true );

        echo print_r($slider_data);

		wp_nonce_field( 'sss_global_sliders', 'sss_global_sliders' );

		$data = $slider_data;
		$get_id = $name_id = $post->ID;
		?>

		<div class="sss-settings-tabs global">
			<ul class="sss-tab-navigation">
			<?php
				foreach( $this->metabox_tabs() as $tab => $tab_settings ) {

					if ( $tab_settings['scope'] == 'all' || $tab_settings['scope'] == 'global' ) {
					?>
					<li class="<?php echo $tab == 'slider' ? 'current' : ''; ?>"><a href="#sss_tab_<?php echo esc_attr( $tab ); ?>"><?php echo esc_attr( $tab_settings['title'] ); ?></a></li>
					<?php
					}

				}
			?>
			</ul>
			<div class="sss-tabs-container">

				<?php foreach( $this->metabox_tabs() as $tab => $tab_settings ) {

					if ( $tab_settings['scope'] == 'all' || $tab_settings['scope'] == 'global' ) {
					?>

					<div id="sss_tab_<?php echo $tab; ?>" class="sss-tab-content">
						<?php
							do_action( 'sss_tab_container_before', $tab, $data, $name_id, $get_id, true );
							do_action( 'sss_get_metabox_tab_' . $tab, $data, $name_id, $get_id, true );
							do_action( 'sss_tab_container_after', $tab, $data, $name_id, $get_id, true );
						?>
					</div>

					<?php
					}
				} ?>

			</div>
		</div>

		<?php

		// A hook to add any content modals that are needed, last parameter indicates if the block is global
		do_action( 'sss_metabox_modals', true );
	}


	 /**
	 * Save all global content blocks
	 *
	 * @since 1.0.0
	 *
	 * @param string $post_id The id of the global content block
	 */
	public function global_slider_save_meta( $post_id ) {

		if ( ! isset( $_POST['sss_global_sliders'] ) || ! wp_verify_nonce( $_POST['sss_global_sliders'], 'sss_global_sliders' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		if ( isset( $_POST[ 'sss_slider_data' ] ) ) {

			$settings = array();

			foreach ( $this->metabox_tabs() as $tab => $title ) {

				if ( isset( $_POST['sss_slider_data'][$tab] ) ) {

					$name_prefix = $_POST['sss_slider_data'][$tab];

					$settings[$tab] = apply_filters( 'sss_save_metabox_tab_' . $tab, null, $name_prefix, true );
				}
			}

			update_post_meta( $post_id, '_sss_slider_data', $settings );

		} else {
			delete_post_meta( $post_id, '_sss_slider_data' );
		}
	}


    /**
     * Returns an array of all the blox metabox tabs
     *
     * @since 1.0.0
     *
     * @return array Array of metabox tabs
     */
    public function metabox_tabs() {

        $tabs = array();

        return apply_filters( 'sss_metabox_tabs', $tabs );
    }


    /**
     * Returns the singleton instance of the class.
     *
     * @since 1.0.0
     *
     * @return object The class object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof SSS_Metaboxes ) ) {
            self::$instance = new SSS_Metaboxes();
        }

        return self::$instance;
    }
}

// Load the metabox class
$blox_metaboxes = SSS_Metaboxes::get_instance();
