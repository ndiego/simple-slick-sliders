<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Creates the controls tab and loads in all the available options
 *
 * @since 	1.0.0
 *
 * @package	Simple Slick Sliders
 * @author 	Nick Diego
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class SSS_Controls {

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

		// Setup style settings
		add_filter( 'sss_metabox_tabs', array( $this, 'add_tab' ), 30 );
		//add_action( 'sss_get_metabox_tab_controls', array( $this, 'get_metabox_tab_controls' ), 10, 4 );
		//add_filter( 'sss_save_metabox_tab_controls', array( $this, 'save_metabox_tab_controls' ), 10, 3 );

        add_action( 'sss_print_slider_controls', array( $this, 'print_slider_controls' ), 10, 3 );
    }


	/**
	 * Add the Controls tab
     *
     * @since 1.0.0
     *
     * @param array $tab  An array of the tabs available
     * @return array $tab The updated tabs array
     */
	public function add_tab( $tabs ) {

		$tabs['controls'] = array(
			'title' => __( 'Controls', 'simple-slick-sliders' ),
			'scope' => 'all'  // all, local, or global
		);

		return $tabs;
	}


	/**
     * Creates the style settings fields
     *
     * @since 1.0.0
     *
     * @param array $data         An array of all block data
     * @param string $name_id 	  The prefix for saving each setting
     * @param string $get_id  	  The prefix for retrieving each setting
     * @param bool $global	      The block state
     */
	public function get_metabox_tab_controls( $data = null, $name_id, $get_id, $global ) {

		// Indicates where the style settings are saved
		$name_prefix = "sss_content_blocks_data[style]";
		$get_prefix = ! empty( $data['style'] ) ? $data['style'] : null;

		// Get the content for the style tab
		$this->control_settings( $get_id, $name_prefix, $get_prefix, $global );
    }



    /**
     * Creates all of the fields for our style settings
     *
     * @since 1.0.0
     *
     * @param int $id             The block id
     * @param string $name_prefix The prefix for saving each setting
     * @param string $get_prefix  The prefix for retrieving each setting
     * @param bool $global	      Determines if the content being loaded for local or global blocks
     */
    public function control_settings( $id, $name_prefix, $get_prefix, $global ) {

    	// Check is default sss CSS is globally disabled
    	$global_disable_default_css = '';

    	// Get the type of block we are working with
		$block_scope = $global ? 'global' : 'local';

		?>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row"><?php _e( 'Custom Block Classes', 'sss' ); ?></th>
					<td>
						<input type="text" class="sss-half-text" name="<?php echo $name_prefix; ?>[custom_classes]" value="<?php echo ! empty( $get_prefix['custom_classes'] ) ? esc_attr( $get_prefix['custom_classes'] ) : ''; ?>" placeholder="e.g. class-one class-two"/>
						<div class="sss-description">
							<?php _e( 'Enter a space separated list of custom CSS classes to add to this content block.', 'sss' ); ?>
						</div>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Custom Block CSS', 'sss' ); ?></th>
					<td>
						<textarea class="sss-textarea-code" name="<?php echo $name_prefix; ?>[custom_css]" rows="6" placeholder="<?php echo 'e.g. #sss_' . $block_scope . '_' . $id . ' { border: 1px solid green; }'; ?>"><?php echo ! empty( $get_prefix['custom_css'] ) ? esc_html( $get_prefix['custom_css'] ) : ''; ?></textarea>
						<div class="sss-description">
							<?php echo __( 'All custom CSS for this block should begin with ', 'sss' ) . '<code>#sss_' . $block_scope . '_' . $id . '</code>. ' . sprintf( __( 'Otherwise the custom CSS could interfere with other content blocks. For reference on content block frontend markup, please refer to the %1$ssss Documentation%2$s.', 'sss' ), '<a href="https://www.ssswp.com/documentation/frontend-markup/?utm_source=sss&utm_medium=plugin&utm_content=style-tab-links&utm_campaign=sss_Plugin_Links" title="' . __( 'sss Documentation', 'sss' ) . '" target="_blank"target="_blank">', '</a>' ); ?>
						</div>
					</td>
				</tr>
				<tr class="<?php echo ! empty( $global_disable_default_css ) ? 'sss-hidden' : '';?>">
					<th scope="row"><?php _e( 'Disable Default CSS', 'sss' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="<?php echo $name_prefix; ?>[disable_default_css]" value="1" <?php ! empty( $get_prefix['disable_default_css'] ) ? checked( esc_attr( $get_prefix['disable_default_css'] ) ) : ''; ?> />
							<?php _e( 'Check to disable all default styles on this block', 'sss' ); ?>
						</label>
						<span class="sss-help-text-icon">
							<a href="#" class="dashicons dashicons-editor-help" onclick="helpIcon.toggleHelp(this);return false;"></a>
						</span>
						<div class="sss-help-text top">
							<?php echo __( 'sss includes default CSS to provide minimal styling. This option will remove all default styling from this block, which can be useful when custom CSS is used extensively.', 'sss' ); ?>
						</div>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Enable Wrap', 'sss' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="<?php echo $name_prefix; ?>[enable_wrap]" value="1" <?php ! empty( $get_prefix['enable_wrap'] ) ? checked( esc_attr( $get_prefix['enable_wrap'] ) ) : ''; ?> />
							<?php echo sprintf( __( 'Check to include the %1$swrap%2$s CSS selector in the block markup.', 'sss' ), '<code>', '</code>' ); ?>
						</label>
						<span class="sss-help-text-icon">
							<a href="#" class="dashicons dashicons-editor-help" onclick="helpIcon.toggleHelp(this);return false;"></a>
						</span>
						<div class="sss-help-text top">
							<?php _e( 'Many Genesis child themes use this selector and enabling it can assist with block styling.', 'sss' ); ?>
						</div>
					</td>
				</tr>
                <tr class="sss-slideshow-option sss-content-slideshow-builtin">
                    <th scope="row"><?php _e( 'Builtin Settings' ); ?></th>
                    <td>
                        <div class="sss-standard-settings">
                            <select name="<?php echo $name_prefix; ?>[slideshow][builtin][settings][animation]" id="sss_builtin_settings_animation">
                                <option value="slide" <?php echo ! empty( $get_prefix['slideshow']['builtin']['settings']['animation'] ) ? selected( $get_prefix['slideshow']['builtin']['settings']['animation'], 'slide' ) : 'selected'; ?> ><?php _e( 'Slide', 'sss' ); ?></option>
                                <option value="fade" <?php echo ! empty( $get_prefix['slideshow']['builtin']['settings']['animation'] ) ? selected( $get_prefix['slideshow']['builtin']['settings']['animation'], 'fade' ) : ''; ?> ><?php _e( 'Fade', 'sss' ); ?></option>
                            </select>
                            <label for="sss_builtin_settings_animation"><?php _e( 'Slideshow Animation', 'sss' ); ?></label><br>
                            <input type="text" name="<?php echo $name_prefix; ?>[slideshow][builtin][settings][slideshowSpeed]" id="sss_builtin_settings_slideshowSpeed" class="sss-small-text" value="<?php if ( ! empty( $get_prefix['slideshow']['builtin']['settings']['slideshowSpeed'] ) && is_numeric( $get_prefix['slideshow']['builtin']['settings']['slideshowSpeed'] ) ) { echo esc_attr( $get_prefix['slideshow']['builtin']['settings']['slideshowSpeed'] ); } else { echo '7000'; } ?>" />
                            <label for="sss_builtin_settings_slideshowSpeed"><?php _e( 'Slideshow Speed (milliseconds)', 'sss' ); ?></label><br>
                            <input type="text" name="<?php echo $name_prefix; ?>[slideshow][builtin][settings][animationSpeed]" id="sss_builtin_settings_animationSpeed" class="sss-small-text" value="<?php if ( ! empty( $get_prefix['slideshow']['builtin']['settings']['animationSpeed'] ) && is_numeric( $get_prefix['slideshow']['builtin']['settings']['animationSpeed'] ) ) { echo esc_attr( $get_prefix['slideshow']['builtin']['settings']['animationSpeed'] ); } else { echo '600'; } ?>" />
                            <label for="sss_builtin_settings_animationSpeed"><?php _e( 'Animation Speed (milliseconds)', 'sss' ); ?></label>
                        </div>
                        <div class="sss-advanced-settings">
                            <label><input type="checkbox" name="<?php echo $name_prefix; ?>[slideshow][builtin][settings][slideshow]" id="sss_builtin_settings_slideshow" value="1" <?php ! empty( $get_prefix['slideshow']['builtin']['settings']['slideshow'] ) ? checked( $get_prefix['slideshow']['builtin']['settings']['slideshow'] ) : ''; ?>> <?php _e( 'Start Slideshow Automatically', 'sss' ); ?></label><br>
                            <label><input type="checkbox" name="<?php echo $name_prefix; ?>[slideshow][builtin][settings][animationLoop]" id="sss_builtin_settings_animationLoop" value="1" <?php ! empty( $get_prefix['slideshow']['builtin']['settings']['animationLoop'] ) ? checked( $get_prefix['slideshow']['builtin']['settings']['animationLoop'] ) : ''; ?>> <?php _e( 'Loop Slideshow', 'sss' ); ?></label><br>
                            <label><input type="checkbox" name="<?php echo $name_prefix; ?>[slideshow][builtin][settings][pauseOnHover]" id="sss_builtin_settings_pauseOnHover" value="1" <?php ! empty( $get_prefix['slideshow']['builtin']['settings']['pauseOnHover'] ) ? checked( $get_prefix['slideshow']['builtin']['settings']['pauseOnHover'] ) : ''; ?>> <?php _e( 'Enable Pause On Hover', 'sss' ); ?></label><br>
                            <label><input type="checkbox" name="<?php echo $name_prefix; ?>[slideshow][builtin][settings][smoothHeight]" id="sss_builtin_settings_smoothHeight" value="1" <?php ! empty( $get_prefix['slideshow']['builtin']['settings']['smoothHeight'] ) ? checked( $get_prefix['slideshow']['builtin']['settings']['smoothHeight'] ) : 'checked'; ?>> <?php _e( 'Enable Slideshow Height Resizing', 'sss' ); ?></label><br>
                            <label><input type="checkbox" name="<?php echo $name_prefix; ?>[slideshow][builtin][settings][directionNav]" id="sss_builtin_settings_directionNav" value="1" <?php ! empty( $get_prefix['slideshow']['builtin']['settings']['directionNav'] ) ? checked( $get_prefix['slideshow']['builtin']['settings']['directionNav'] ) : ''; ?>> <?php _e( 'Disable Directional Navigation (i.e. arrows)', 'sss' ); ?></label><br>
                            <label><input type="checkbox" name="<?php echo $name_prefix; ?>[slideshow][builtin][settings][controlNav]" id="sss_builtin_settings_controlNav" value="1" <?php ! empty( $get_prefix['slideshow']['builtin']['settings']['controlNav'] ) ? checked( $get_prefix['slideshow']['builtin']['settings']['controlNav'] ) : ''; ?>> <?php _e( 'Disable Control Navigation (i.e. dots)', 'sss' ); ?></label><br>
                            <label><input type="checkbox" name="<?php echo $name_prefix; ?>[slideshow][builtin][settings][caption]" id="sss_builtin_settings_caption" value="1" <?php ! empty( $get_prefix['slideshow']['builtin']['settings']['caption'] ) ? checked( $get_prefix['slideshow']['builtin']['settings']['caption'] ) : ''; ?>> <?php _e( 'Disable Captions ', 'sss' ); ?></label><br>
                        </div>
                    </td>
                </tr>
				<?php do_action( 'sss_style_settings', $id, $name_prefix, $get_prefix, $global ); ?>

			</tbody>
		</table>
		<?php
	}


    /**
	 * Saves all of the style settings
     *
     * @since 1.0.0
     *
     * @param int $post_id        The global block id or the post/page/custom post-type id corresponding to the local block
     * @param string $name_prefix The prefix for saving each setting
     * @param bool $global        The block state
     *
     * @return array $settings    Return an array of updated settings
     */
    public function save_metabox_tab_controls( $post_id, $name_prefix, $global ) {

		$settings = array();

		$settings['custom_classes'] 	 = trim( strip_tags( $name_prefix['custom_classes'] ) );
		$settings['custom_css']     	 = isset( $name_prefix['custom_css'] ) ? trim( esc_html( $name_prefix['custom_css'] ) ) : '';
		$settings['enable_wrap']    	 = isset( $name_prefix['enable_wrap'] ) ? 1 : 0;
		$settings['disable_default_css'] = isset( $name_prefix['disable_default_css'] ) ? 1 : 0;


        // Save the builtin settings
        $settings['builtin']['settings']['animation']      	= esc_attr( $name_prefix['builtin']['settings']['animation'] );
        $settings['builtin']['settings']['slideshowSpeed'] 	= absint( $name_prefix['builtin']['settings']['slideshowSpeed'] );
        $settings['builtin']['settings']['animationSpeed'] 	= absint( $name_prefix['builtin']['settings']['animationSpeed'] );
        $settings['builtin']['settings']['slideshow'] 		= isset( $name_prefix['builtin']['settings']['slideshow'] ) ? 1 : 0;
        $settings['builtin']['settings']['animationLoop'] 	= isset( $name_prefix['builtin']['settings']['animationLoop'] ) ? 1 : 0;
        $settings['builtin']['settings']['pauseOnHover'] 	= isset( $name_prefix['builtin']['settings']['pauseOnHover'] ) ? 1 : 0;
        $settings['builtin']['settings']['smoothHeight'] 	= isset( $name_prefix['builtin']['settings']['smoothHeight'] ) ? 1 : 0;
        $settings['builtin']['settings']['directionNav'] 	= isset( $name_prefix['builtin']['settings']['directionNav'] ) ? 1 : 0;
        $settings['builtin']['settings']['controlNav']		= isset( $name_prefix['builtin']['settings']['controlNav'] ) ? 1 : 0;
        $settings['builtin']['settings']['caption']  		= isset( $name_prefix['builtin']['settings']['caption'] ) ? 1 : 0;

		return apply_filters( 'sss_save_style_settings', $settings, $post_id, $name_prefix, $global );
	}


   /* Print all of slick controls (js)
    *
    * @since 1.0.0
    *
    * @param string $slider_id  The id of the givien slider
    * @param array $slider_data Array of all slider data
    * @param string $output_type Determines how the slider is being output, shortcode, function etc.
    */
   public function print_slider_controls( $slider_id, $slider_data, $output_type ) {
       ?>
           jQuery(".sss-container").slick();
       <?php
   }

    /**
     * Returns the singleton instance of the class.
     *
     * @since 1.0.0
     *
     * @return object The class object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof SSS_Controls ) ) {
            self::$instance = new SSS_Controls();
        }

        return self::$instance;
    }
}

// Load the style class.
$sss_style = SSS_Controls::get_instance();
