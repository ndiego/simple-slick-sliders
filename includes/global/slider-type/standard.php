<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Creates the slideshow content section within the content tab and loads in all available options
 *
 * @since 	1.0.0
 *
 * @package	sss
 * @author 	Nick Diego
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class SSS_Slider_Standard {

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

		add_filter( 'sss_slider_type', array( $this, 'add_slider' ), 16 );
		add_action( 'sss_get_slider_standard', array( $this, 'get_slider' ), 10, 4 );
		add_filter( 'sss_save_slider_standard', array( $this, 'save_slider' ), 10, 3 );
		add_action( 'sss_print_slider_standard', array( $this, 'print_slider' ), 10, 4 );

		// Add the slideshow modal to the admin page
        add_action( 'sss_metabox_modals', array( $this, 'add_slider_modal' ), 10, 1 );

    	// Add required slideshow scripts to the front-end
    	add_action( 'sss_frontend_standard_scripts_styles', array( $this, 'standard_scripts_styles' ) );
    }


	/**
	 * Add required scripts for the standard to the front-end
     *
     * @since 1.0.0
     */
	public function standard_scripts_styles() {

		// Load flexslider js
        wp_enqueue_script( $this->base->plugin_slug . '-flexslider-scripts', plugins_url( 'assets/plugins/flexslider/jquery.flexslider-min.js', $this->base->file ), array( 'jquery' ), $this->base->version );

		// Load base flexslider styles
        wp_register_style( $this->base->plugin_slug . '-flexslider-styles', plugins_url( 'assets/plugins/flexslider/flexslider.css', $this->base->file ), array(), $this->base->version );
        wp_enqueue_style( $this->base->plugin_slug . '-flexslider-styles' );
	}


	/**
	 * Enable the "slideshow" content option in the plugin
     *
     * @since 1.0.0
     *
     * @param array $content_types  An array of the content types available
     */
	public function add_slider( $slider_types ) {
		$slider_types['standard'] = __( 'Standard', 'simple-slick-sliders' );
		return $slider_types;
	}


	/**
	 * Prints all of the image ralated settings fields
     *
     * @since 1.0.0
     *
     * @param int $id             The block id
     * @param string $name_prefix The prefix for saving each setting
     * @param string $get_prefix  The prefix for retrieving each setting
     * @param bool $global        The block state
     */
	public function get_slider( $id, $name_prefix, $get_prefix, $global ) {
		?>

		<!-- Slideshow Settings -->
		<table class="form-table sss-content-slideshow">
			<tbody>
				<tr class="sss-slideshow-option sss-content-slideshow-builtin">
					<th scope="row"><?php _e( 'Add Slides', 'simple-slick-sliders' ); ?></th>
					<td>
                        <select>
                            <option value="image"><?php _e( 'Add Slides', 'simple-slick-sliders' ); ?></option>
						<input type="submit" class="button button-primary" name="sss_slideshow_upload_button" id="sss_slideshow_upload_button" value="<?php _e( 'Select Image(s)'); ?>" onclick="sss_builtinSlideshowUpload.uploader('<?php echo $name_prefix; ?>'); return false;" /> &nbsp;



					</td>
				</tr>


				<?php

				// Load settings for any additional slideshows we might want to add
				do_action( 'sss_additional_slideshow_options', $name_prefix, $get_prefix );

				?>

			</tbody>
		</table>
        <div class="sss-slide-container-outer">
            <ul class="sss-slider-container" style="overflow:hidden;">

            <?php if ( ! empty( $get_prefix['standard']['slides'] ) ) { ?>

                <?php foreach ( $get_prefix['standard']['slides'] as $key => $slides ) { ?>
                    <li id="<?php echo $key; ?>" class="sss-slideshow-item" >
                        <div class="sss-slide-container">
                            <img src="<?php echo isset( $slides['image']['id'] ) ? wp_get_attachment_thumb_url( esc_attr( $slides['image']['id'] ) ) : ''; ?>" alt="<?php echo esc_attr( $slides['image']['alt'] ); ?>" />
                        </div>
                        <input type="text" class="slide-type sss-force-hidden" name="<?php echo $name_prefix; ?>[standard][slides][<?php echo $key; ?>][slide_type]" value="image" /> <!-- possibly more slide types in the future -->

                        <input type="text" class="slide-image-id sss-force-hidden" name="<?php echo $name_prefix; ?>[standard][slides][<?php echo $key; ?>][image][id]" value="<?php echo isset( $slides['image']['id'] ) ? esc_attr( $slides['image']['id'] ) : ''; ?>" />
                        <input type="text" class="slide-image-url sss-force-hidden" name="<?php echo $name_prefix; ?>[standard][slides][<?php echo $key; ?>][image][url]" value="<?php echo esc_attr( $slides['image']['url'] ); ?>" />
                        <input type="text" class="slide-image-title sss-force-hidden" name="<?php echo $name_prefix; ?>[standard][slides][<?php echo $key; ?>][image][title]" value="<?php echo isset( $slides['image']['title'] ) ? esc_attr( $slides['image']['title'] ) : ''; ?>" />
                        <input type="text" class="slide-image-alt sss-force-hidden" name="<?php echo $name_prefix; ?>[standard][slides][<?php echo $key; ?>][image][alt]" value="<?php echo isset( $slides['image']['alt'] ) ? esc_attr( $slides['image']['alt'] ) : ''; ?>" />
                        <input type="checkbox" class="slide-image-link-enable sss-force-hidden" name="<?php echo $name_prefix; ?>[standard][slides][<?php echo $key; ?>][image][link][enable]" value="1" <?php ! empty( $slides['image']['link']['enable'] ) ? checked( $slides['image']['link']['enable'] ) : ''; ?> />
                        <input type="text" class="slide-image-link-url sss-force-hidden" name="<?php echo $name_prefix; ?>[standard][slides][<?php echo $key; ?>][image][link][url]" value="<?php echo ! empty( $slides['image']['link']['url'] ) ? esc_attr( $slides['image']['link']['url'] ) : 'http://'; ?>" />
                        <input type="text" class="slide-image-link-title sss-force-hidden" name="<?php echo $name_prefix; ?>[standard][slides][<?php echo $key; ?>][image][link][title]" value="<?php echo ! empty( $slides['image']['link']['title'] ) ? esc_attr( $slides['image']['link']['title'] ) : ''; ?>" />
                        <input type="checkbox" class="slide-image-link-target sss-force-hidden" name="<?php echo $name_prefix; ?>[standard][slides][<?php echo $key; ?>][image][link][target]" value="1" <?php ! empty( $slides['image']['link']['target'] ) ? checked( $slides['image']['link']['target'] ) : ''; ?> />
                        <input type="text" class="slide-image-caption sss-force-hidden" name="<?php echo $name_prefix; ?>[standard][slides][<?php echo $key; ?>][image][caption]" value="<?php echo isset( $slides['image']['caption'] ) ? esc_attr( $slides['image']['caption'] ) : ''; ?>" />
                        <input type="text" class="slide-image-classes sss-force-hidden" name="<?php echo $name_prefix; ?>[standard][slides][<?php echo $key; ?>][image][classes]" value="<?php echo ! empty( $slides['image']['classes'] ) ? esc_attr( $slides['image']['classes'] ) : ''; ?>" />

                        <div class="sss-slide-details-container">
                            <a class="sss-slide-details" href="#sss_slide_details"><?php _e( 'Details', 'simple-slick-sliders' );?></a><a class="sss-slide-remove" href="#"><?php _e( 'Remove', 'simple-slick-sliders' );?></a>
                        </div>
                    </li>
                <?php } ?>

                <?php } else { ?>
                        <li class="sss-filler" >
                            <div class="sss-filler-container"></div>
                            <div class="sss-filler-text">
                                <span><?php _e( 'Details', 'simple-slick-sliders' );?></span><span class="right"><?php _e( 'Remove', 'simple-slick-sliders' );?></span>
                            </div>
                        </li>
                <?php } ?>
            </ul>
        </div>
		<?php
	}


	/**
	 * Saves all of the slideshow ralated settings
     *
     * @since 1.0.0
     *
     * @param string $name_prefix The prefix for saving each setting (this brings ...['slideshow'] with it)
     * @param int $id             The block id
     * @param bool $global        The block state
     *
     * @return array $settings    Return an array of updated slideshow settings
     */
	public function save_slider( $name_prefix, $id, $global ) {

		$settings = array();

		// Save the builtin slides
		if ( isset( $name_prefix['slides'] ) ){
			foreach ( $name_prefix['slides'] as $key => $slides ) {

				// Only slide type currently (v1.0.0) is "image"
				$settings['slides'][$key]['slide_type'] 			 = 'image';

				$settings['slides'][$key]['image']['id'] 			 = trim( strip_tags( $name_prefix['slides'][$key]['image']['id'] ) );
				$settings['slides'][$key]['image']['url']    		 = esc_url( $name_prefix['slides'][$key]['image']['url'] );
				$settings['slides'][$key]['image']['title']    		 = trim( strip_tags( $name_prefix['slides'][$key]['image']['title'] ) );
				$settings['slides'][$key]['image']['alt'] 	   		 = trim( strip_tags( $name_prefix['slides'][$key]['image']['alt'] ) );
				$settings['slides'][$key]['image']['caption']  		 = wp_kses_post( $name_prefix['slides'][$key]['image']['caption'] );
				$settings['slides'][$key]['image']['link']['enable'] = isset( $name_prefix['slides'][$key]['image']['link']['enable'] ) ? 1 : 0;
				$settings['slides'][$key]['image']['link']['url']	 = isset( $name_prefix['slides'][$key]['image']['link']['url'] ) ? ( $name_prefix['slides'][$key]['image']['link']['url'] == 'http://' ? '' : esc_url( $name_prefix['slides'][$key]['image']['link']['url'] ) ) : '';
				$settings['slides'][$key]['image']['link']['title']	 = isset( $name_prefix['slides'][$key]['image']['link']['title'] ) ? trim( strip_tags( $name_prefix['slides'][$key]['image']['link']['title'] ) ) : '';
				$settings['slides'][$key]['image']['link']['target'] = isset( $name_prefix['slides'][$key]['image']['link']['target'] ) ? 1 : 0;
				$settings['slides'][$key]['image']['classes'] 	 	 = trim( strip_tags( $name_prefix['slides'][$key]['image']['classes'] ) );
			}
		} else {
			$settings['slides'] = '';
		}

		return $settings;
	}


	/**
	 * Adds the builtin slideshow modal to the page
	 *
	 * @since 1.0.0
	 *
	 * @param bool $global The block state
     */
	public function add_slider_modal( $global ) {
		?>
		<!--Slideshow Image Settings Modal-->
		<div id="sss_slide_details" class='sss-hidden sss-modal' title="<?php _e( 'Image Details', 'sss' );?>">

			<!-- Header -->
			<div class="sss-modal-titlebar">
				<span class="sss-modal-title"><?php _e( 'Slide Details', 'sss' ); ?></span>
				<button type="button" class="sss-modal-close" title="<?php _e( 'Close' );?>">
					<span class="sss-modal-close-icon"></span>
					<span class="sss-modal-close-text"><?php _e( 'Close', 'sss' ); ?></span>
				</button>
			</div>

			<input type="text" class="modal-slide-id sss-force-hidden" value="" />

			<!-- Body -->
			<div class="sss-form-container">
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row"><?php _e( 'Image Title', 'sss' ); ?></th>
							<td>
								<input type="text" class="modal-slide-image-title" value="" />
							</td>
						</tr>
						<tr>
							<th scope="row"><?php _e( 'Image Alt Text', 'sss' ); ?></th>
							<td>
								<input type="text" class="modal-slide-image-alt" value="" />
							</td>
						</tr>
						<tr>
							<th scope="row"><?php _e( 'Image Link', 'sss' ); ?></th>
							<td>
								<label class="sss-image-link-enable">
									<input type="checkbox" class="modal-slide-image-link-enable" value="1" />
									<?php _e( 'Check to enable', 'sss' ); ?>
								</label>
								<div class="sss-image-link">
									<label class="sss-subtitle">
										<span><?php _e( 'URL', 'sss' ); ?></span>
										<input type="text" class="modal-slide-image-link-url" value="" />
									</label>
									<label class="sss-subtitle">
										<span><?php _e( 'Title', 'sss' ); ?></span>
										<input type="text" class="modal-slide-image-link-title" value="" />
									</label>
									<label>
										<input type="checkbox" class="modal-slide-image-link-target" value="1" />
										<?php _e( 'Open link in new window/tab', 'sss' ); ?>
									</label>
								</div>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php _e( 'Slide Caption', 'sss' ); ?></th>
							<td>
								<textarea class="modal-slide-image-caption sss-textarea-code" type="text" rows="3" ></textarea>
								<div class="sss-description">
									<?php _e( 'Only basic HTML is accepted.', 'sss' ); ?>
								</div>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php _e( 'Slide CSS Classes', 'sss' ); ?></th>
							<td>
								<input type="text" class="modal-slide-image-classes" value="" />
								<div class="sss-description">
									<?php _e( 'Enter a space separated list of custom CSS classes to add to this image slide.', 'sss' ); ?>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<!-- Footer -->
			<div class="sss-modal-buttonpane">
				<button id="sss-apply-details" type="button" class="button button-primary sss-modal-button">
					<?php _e( 'Apply Settings', 'sss' ); ?>
				</button>
			</div>

		</div> <!-- end sss_slide_details -->
		<?php
	}


	/**
	 * Prints all of the slider content
     *
     * @since 1.0.0
     *
     * @param int $slider_id      The slider id
     * @param string $slider_data Array of all slider data
     * @param string $type        How are we printing the slider, shortcode, function etc.
     */
	public function print_slider( $slider_id, $slider_data, $type ) {

		// Array of additional CSS classes
		$classes = array();

        $background = true;
        $background_class = 'background';

        foreach ( $slider_data['slider']['standard']['slides'] as $key => $data ) {

            if ( $background ) {
                $background_image = 'background-image: url(' . $data['image']['url'] . ');';
            }
            ?>
            <div id="<?php echo $key; ?>" class="sss-slide <?php echo $data['slide_type'] . ' ' . $background_class; ?>" style="<?php echo $background_image;?>">
                <?php
                // Get our image link if enabled
                if ( ! empty( $data['image']['link']['url'] ) && $data['image']['link']['enable'] ) {
                    $target = $data['image']['link']['target'] == 1 ? '_blank' : '_self';
                    $link_start = '<a href="' . $data['image']['link']['url'] . '" target="' . $target . '" title="' . $data['image']['link']['title'] . '">';
                    $link_end   = '</a>';
                } else {
                    $link_start = '';
                    $link_end   = '';
                }

                echo $link_start;
                if ( ! $background ) {
                ?>
                <img src="<?php echo ! empty( $data['image']['url'] ) ? esc_attr( $data['image']['url'] ) : ''; ?>" alt="<?php echo ! empty( $data['image']['alt'] ) ? esc_attr( $data['image']['alt'] ) : ''; ?>" title="<?php echo ! empty( $data['image']['title'] ) ? esc_attr( $data['image']['title'] ) : ''; ?>" />
                <?php
                }
                echo $link_end; ?>

                <?php if ( ! empty( $data['image']['caption'] ) ) {  ?>

                    <div class="sss-caption-container">
                        <div class="sss-caption-wrap">
                            <?php echo wp_kses_post( $data['image']['caption'] ); ?>
                        </div>
                    </div>
                <?php }  ?>

            </div>
            <?php
        }


		/* Check to make sure slides have been added to the builtin slideshow
		if ( ! empty( $content_data['slideshow'] ) ) { ?>
			<div class="sss-slideshow-container builtin flexslider <?php echo implode( ' ', apply_filters( 'sss_content_slideshow_classes', $classes ) ); ?>">
				<ul class="sss-slideshow-wrap slides">

					<?php foreach ( $content_data['slideshow']['slides'] as $key => $slides ) { ?>
						<li id="<?php echo $key; ?>" class="sss-slideshow-item <?php echo $slides['slide_type']; ?> <?php echo $slides['image']['classes']; ?>" >
							<?php
								// Get our image link if enabled
								if ( ! empty( $slides['image']['link']['url'] ) && $slides['image']['link']['enable'] ) {
									$target = $slides['image']['link']['target'] == 1 ? '_blank' : '_self';
									$link_start = '<a href="' . $slides['image']['link']['url'] . '" target="' . $target . '" title="' . $slides['image']['link']['title'] . '">';
									$link_end   = '</a>';
								} else {
									$link_start = '';
									$link_end   = '';
								}
							?>

							<?php echo $link_start; ?>
								<img src="<?php echo ! empty( $slides['image']['url'] ) ? esc_attr( $slides['image']['url'] ) : ''; ?>" alt="<?php echo ! empty( $slides['image']['alt'] ) ? esc_attr( $slides['image']['alt'] ) : ''; ?>" title="<?php echo ! empty( $slides['image']['title'] ) ? esc_attr( $slides['image']['title'] ) : ''; ?>" />
							<?php echo $link_end; ?>
							<?php if ( empty( $content_data['slideshow']['settings']['caption'] ) && ! empty( $slides['image']['caption'] ) ) {  ?>
								<div class="sss-caption-container">
									<div class="sss-caption-wrap">
										<?php echo wp_kses_post( $slides['image']['caption'] ); ?>
									</div>
								</div>
							<?php }  ?>

						</li>
					<?php } ?>
				</ul>
			</div>

			<script type="text/javascript">
				jQuery(document).ready(function($){

					// Set all of our slider settings
					$(window).load(function() {
						$('#sss_<?php echo $block_scope . "_" . $block_id;?> .sss-slideshow-container.builtin').flexslider({
							animation: "<?php echo ! empty( $content_data['slideshow']['settings']['animation'] ) ? $content_data['slideshow']['settings']['animation'] : 'fade'; ?>",
							animationLoop: <?php echo ! empty( $content_data['slideshow']['settings']['animationLoop'] ) ? 'true' : 'false'; ?>,
							slideshow: <?php echo ! empty( $content_data['slideshow']['settings']['slideshow'] ) ? 'true' : 'false'; ?>,
							pauseOnHover: <?php echo ! empty( $content_data['slideshow']['settings']['pauseOnHover'] ) ? 'true' : 'false'; ?>,
							directionNav: <?php echo ! empty( $content_data['slideshow']['settings']['directionNav'] ) ? 'false' : 'true'; ?>,
							controlNav: <?php echo ! empty( $content_data['slideshow']['settings']['controlNav'] ) ? 'false' : 'true'; ?>,
							slideshowSpeed: <?php echo ! empty( $content_data['slideshow']['settings']['slideshowSpeed'] ) ? esc_attr( $content_data['slideshow']['settings']['slideshowSpeed'] ) : 7000; ?>,
							animationSpeed: <?php echo ! empty( $content_data['slideshow']['settings']['animationSpeed'] ) ? esc_attr( $content_data['slideshow']['settings']['animationSpeed'] ) : 600; ?>,
							smoothHeight: <?php echo ! empty( $content_data['slideshow']['settings']['smoothHeight'] ) ? 'true' : 'false'; ?>,

							//after: function(){
								//if ( $( '.flex-active-slide' ).hasClass( 'dark' ) ) { alert('true'); } else { alert( 'false')};
							//}
						});
					});

				});
			</script>

		<?php } else { ?>
			<div class="media-error">
				<p><?php _e( 'You haven\'t added any slides to the slideshow!' ); ?></p>
			</div>
		<?php }

        */
	}







    /**
     * Returns the singleton instance of the class.
     *
     * @since 1.0.0
     *
     * @return object The class object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof SSS_Slider_Standard ) ) {
            self::$instance = new SSS_Slider_Standard();
        }

        return self::$instance;
    }
}

// Load the slideshow content class.
$sss_slider_standard = SSS_Slider_Standard::get_instance();
