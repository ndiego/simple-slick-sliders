<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Creates the slides tab and loads in all the available options
 *
 * @since 1.0.0
 *
 * @package	Simple Slick Sliders
 * @author 	Nick Diego
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class SSS_Slider {

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

		// Setup content settings
		add_filter( 'sss_metabox_tabs', array( $this, 'add_slider_tab' ), 4 );
		add_action( 'sss_get_metabox_tab_slider', array( $this, 'get_metabox_tab_slider' ), 10, 4 );
		add_filter( 'sss_save_metabox_tab_slider', array( $this, 'save_metabox_tab_slider' ), 10, 3 );

		// Run content check to make sure all slider types are available, otherwise print messages
		add_action( 'sss_tab_container_before', array( $this, 'slider_type_check' ), 10, 5 );

		// Add the admin column data for global blocks
		//add_filter( 'blox_admin_column_titles', array( $this, 'admin_column_title' ), 1, 1 );
		//add_action( 'blox_admin_column_data_content', array( $this, 'admin_column_data' ), 10, 2 );

		// Make admin column sortable
		//add_filter( 'manage_edit-blox_sortable_columns', array( $this, 'admin_column_sortable' ), 5 );
        //add_filter( 'request', array( $this, 'admin_column_orderby' ) );
    }


	/* Add the Content tab
     *
     * @since 1.0.0
     *
     * @param array $tab An array of the tabs available
     */
	public function add_slider_tab( $tabs ) {

		$tabs['slider'] = array(
			'title' => __( 'Slides', 'simple-slick-sliders' ),
			'scope' => 'all'  // all, local, or global
		);

		return $tabs;
	}


    /**
     * Creates the slider settings fields
     *
     * @since 1.0.0
     *
     * @param array $data         An array of all slider data
     * @param string $name_id 	  The prefix for saving each setting
     * @param string $get_id  	  The prefix for retrieving each setting
     * @param bool $global	      The slider state (Currently unused)
     */
	public function get_metabox_tab_slider( $data = null, $name_id, $get_id, $global ) {

		// Indicates where the content settings are saved
		$name_prefix = "sss_slider_data[slider]";
		$get_prefix = ! empty( $data['slider'] ) ? $data['slider'] : null;

		// Get the content for the content tab
		$this->slider_settings( $name_id, $name_prefix, $get_prefix, $global );
    }


    /**
     * Creates all of the fields for our block content
     *
     * @since 1.0.0
     *
     * @param int $id             The block id
     * @param string $name_prefix The prefix for saving each setting
     * @param string $get_prefix  The prefix for retrieving each setting
     * @param bool $global	      Determines if the content being loaded for local or global blocks
     */
    public function slider_settings( $id, $name_prefix, $get_prefix, $global ) {
    	?>
    	<table class="form-table sss-slider-type-container">
			<tbody>
				<tr>
					<th scope="row"><?php _e( 'Slider Type' ); ?><span class="icon-stop2"></span></th>
					<td>
						<select name="<?php echo $name_prefix; ?>[slider_type]" id="sss_slider_type" class="sss-slider-type">
							<?php foreach ( $this->get_slider_types() as $type => $title ) { ?>
								<option value="<?php echo $type; ?>" <?php echo ! empty( $get_prefix['slider_type'] ) ? selected( $get_prefix['slider_type'], $type ) : ''; ?>><?php echo $title; ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
			</tbody>
		</table>

		<?php
		foreach ( $this->get_slider_types() as $type => $title ) {

			// Get all the available slider options
			do_action( 'sss_get_slider_' . $type, $id, $name_prefix, $get_prefix, $global );
		}
		?>
    	<?php
    }


    /**
	 * Saves all of the slider settings
     *
     * @since 1.0.0
     *
     * @param int $post_id        The global block id or the post/page/custom post-type id corresponding to the local block
     * @param string $name_prefix The prefix for saving each setting
     * @param bool $global        The slider state
     *
     * @return array $settings    Return an array of updated settings
     */
	public function save_metabox_tab_slider( $post_id, $name_prefix, $global ) {

		$settings = array();

		$settings['slider_type'] = esc_attr( $name_prefix['slider_type'] );

		foreach ( $this->get_slider_types() as $type => $title ) {

			$name_prefix = ! empty( $_POST['sss_slider_data']['slider'][$type] ) ? $_POST['sss_slider_data']['slider'][$type] : '';
			$settings[$type] = apply_filters( 'sss_save_slider_' . $type, $name_prefix, $post_id, true );

		}

		update_post_meta( $post_id, '_sss_slider_type', $settings['slider_type'] );

		return $settings;
	}


    /**
     * Helper function. If the current content option is no longer available (ie the Extension was deactivated), provide a notice
     *
     * @since 1.0.0
     *
     * @param string $tab     Name of the tab to show the message on
     * @param array $data     String of all setting data associated with the current block
     * @param string $name_id The content blocks id (might be random id if a local block that was added via ajax)
     * @param string $get_id  The content blocks id (might be random id if a local block that was added via ajax)
     * @param bool $global    Indicates if the block is global
     */
	public function slider_type_check( $tab, $data, $name_id, $get_id, $global ) {

		// Only display content check error on the content tab
		if ( $tab == 'slider' ) {

			$data = ! empty( $data ) ? $data : array();

            if ( isset($data['slider']) ) {
                $set_slider_type = $data['slider']['slider_type'];
            }



			$available_slider_types = $this->get_slider_types();

			if ( isset( $set_slider_type ) && ! array_key_exists( $set_slider_type, $available_slider_types ) ) {
				?>
				<div class="sss-alert sss-alert-error narrow">
					<?php echo sprintf( __( 'The slider type of this Slick Slider is currently set to %1$s, which no longer exists. Therefore, this slider is currently not visible on your site. ', 'simple-slick-sliders' ), '<strong>' . $set_slider_type . '</strong>' ); ?>
				</div>
				<?php
			}
		}
	}


    /**
     * Add admin column for global blocks
     *
     * @param string $post_id
     * @param array $block_data
     */
    public function admin_column_title( $columns ) {
    	$columns['content'] = __( 'Content', 'simple-slick-sliders' );
    	return $columns;
    }


    /**
     * Print the admin column data for global blocks.
     *
     * @param string $post_id
     * @param array $block_data
     */
    public function admin_column_data( $post_id, $slider_data ) {
    	if (! empty( $slider_data['content']['content_type'] ) ) {
    		$content   = ucfirst( esc_attr( $slider_data['content']['content_type'] ) );
    		$meta_data = esc_attr( $slider_data['content']['content_type'] );
		} else {
			$content   = '<span style="color:#a00;font-style:italic;">' . __( 'Error', 'simple-slick-sliders' ) . '</span>';
			$meta_data = '';
		}

		echo $content;

		// Save our content meta values separately for sorting
		update_post_meta( $post_id, '_sss_slider_content_type', $meta_data );
	}


	/**
     * Tell Wordpress that the content column is sortable
     *
     * @since 1.0.0
     *
     * @param array $vars  Array of query variables
     */
	public function admin_column_sortable( $sortable_columns ) {
		$sortable_columns[ 'content' ] = 'content';
		return $sortable_columns;
	}


	/**
     * Tell Wordpress how to sort the content column
     *
     * @since 1.0.0
     *
     * @param array $vars  Array of query variables
     */
	public function admin_column_orderby( $vars ) {

		if ( isset( $vars['orderby'] ) && 'content' == $vars['orderby'] ) {
			$vars = array_merge( $vars, array(
				'meta_key' => '_sss_slider_content_type',
				'orderby' => 'meta_value'
			) );
		}

		return $vars;
	}


    /**
     * Helper function for retrieving the available content types.
     *
     * @since 1.0.0
     *
     * @return array Array of all content types.
     */
    public function get_slider_types() {

        $instance = SSS_Common::get_instance();
        return $instance->get_slider_types();
    }


    /**
     * Returns the singleton instance of the class.
     *
     * @since 1.0.0
     *
     * @return object The class object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof SSS_Slider ) ) {
            self::$instance = new SSS_Slider();
        }

        return self::$instance;
    }
}

// Load the content class.
$blox_content = SSS_Slider::get_instance();
