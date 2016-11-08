<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Generates all SSS output
 *
 * @since 	1.0.0
 *
 * @package	Simple Slick Sliders
 * @author 	Nick Diego
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class SSS_Output {

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

        add_action( 'wp', array( $this, 'output_init' ), 1 );

		add_action( 'sss_shortcode_print_slider', array( $this, 'print_slider' ), 10, 2 );
    }

    /**
     * Prints our content blocks on the frontend is a series of tests are passed
     *
     * @since 1.0.0
     */
	public function output_init() {


		// Get all of the Global Content Blocks
		$all_sliders = get_posts( array(
			'post_type'        => 'simple-slick-sliders',
			'post_status'  	   => 'publish',
			'numberposts'      => -1,     // We want all global blocks
			'suppress_filters' => false   // For WPML compatibility
		) );

		if ( ! empty( $all_sliders ) ) {

            // We have active content blocks so enqueue the needed stypes and scripts
            add_action( 'wp_enqueue_scripts', array( $this, 'output_scripts_styles' ) );

            // Also load our global custom CSS if there is any...
            // add_action( 'wp_head', array( $this, 'print_global_custom_css' ), 10 );
		}

	}


    /**
     * Loads styles and scripts for our content blocks
     *
     * @since 1.0.0
     */
    public function output_scripts_styles() {

        // Check to see if default css is globally disabled
        //$global_disable_default_css = blox_get_option( 'disable_default_css', '' );
        /*
        if ( empty( $global_disable_default_css ) ) {

            // Load the Blox default frontend styles.
            wp_register_style( $this->base->plugin_slug . '-default-styles', plugins_url( 'assets/css/default.css', $this->base->file ), array(), $this->base->version );
            wp_enqueue_style( $this->base->plugin_slug . '-default-styles' );
        }

        // Fire a hook to load in custom metabox scripts and styles.
        do_action( 'blox_frontend_main_scripts_styles' );

        // Get all active content types, strip out any duplicates
        $active_content_types = array_unique( $this->active_content_types );

        // Now that critical scripts and styles have been enqueued, conditionally load content specific scripts and styles
        foreach ( $active_content_types as $type ) {
            do_action( 'blox_frontend_' . $type . '_scripts_styles' );
        }*/

        // Load slick js
        wp_enqueue_script( $this->base->plugin_slug . '-slick-scripts', plugins_url( 'assets/slick/slick.min.js', $this->base->file ), array( 'jquery' ), $this->base->version );

    	// Load base slick styles
        wp_register_style( $this->base->plugin_slug . '-slick-styles', plugins_url( 'assets/slick/slick.css', $this->base->file ), array(), $this->base->version );
        wp_enqueue_style( $this->base->plugin_slug . '-slick-styles' );

        // Load default slick theme
        wp_register_style( $this->base->plugin_slug . '-slick-default-theme', plugins_url( 'assets/slick/slick-theme.css', $this->base->file ), array(), $this->base->version );
        wp_enqueue_style( $this->base->plugin_slug . '-slick-default-theme' );

    }


	/**
	 * Shortcode: Print page title
     *
     * @since 1.0.0
     *
     * @param array $atts  An array shortcode attributes
     */
	public function print_slider( $slider_id, $output_type ) {

        $slider_data = get_post_meta( $slider_id, '_sss_slider_data', true );

        if ( empty( $slider_data ) ) {
            return;
        }

        $slider_type = $slider_data['slider']['slider_type'];  // @todo need to disable if not enabled slider type

        ?>

        <div id="sss-slider-<?php echo $slider_id; ?>" class="sss-container">

            <?php do_action( 'sss_print_slider_' . $slider_type, $slider_id, $slider_data, $output_type ); ?>

        </div>

        <?php

        // Print the custom slider styles to the page
        // NOTE cant seem to call on wp_hook because adding the shortcode happens after that hook LOOK INTO ALTERNATIVE
        $styles = $this->print_slider_styles( $slider_id, $slider_data, $output_type );

        if ( ! empty( $styles) ) {
            echo $styles; // @todo minimize this output
        }

        // Use the handy action storage class to print the slide JS in the footer
        add_action( 'wp_footer', array( new SSS_Output_Action_Storage( array( $slider_id, $slider_data, $output_type ) ), 'print_slider_controls' ), 100, 1 );
	}


    /**
	 * Print required slider js in the footer
     *
     * @since 1.0.0
     *
     * @param string $slider_id  The id of the givien slider
     * @param array $slider_data Array of all slider data
     * @param string $output_type Determines how the slider is being output, shortcode, function etc.
     */
	public function print_slider_controls( $slider_id, $slider_data, $output_type ) {

        // @todo, move this over to control.php
        // echo '<script type="text/javascript" id="ss">';
        // do_action( 'sss_print_slider_controls', $slider_id, $slider_data, $output_type );
        // echo '</script>';

        ?>
        <script type="text/javascript">

            jQuery( '#sss-slider-<?php echo $slider_id;?>' ).slick({
                accessibility: true,
                adaptiveHeight: false,
                autoplay: true,
                autoplaySpeed: 4000,
                arrows: true,
                asNavFor: '',
                //appendArrows: '',
                //appendDots: '',
                prevArrow: '<button type="button" class="slick-prev">Previous</button>',
                nextArrow: '<button type="button" class="slick-next">Next</button>',
                centerMode: false,
                centerPadding: '50px',
                cssEase: 'ease',
                customPaging: '',
                dots: false,
                dotsClass: 'slick-dots',
                draggable: true,
                fade: false,
                focusOnSelect: false,
                easing: 'linear',
                edgeFriction: 0.15,
                infinite: true,
                initialSlide: 0,
                lazyLoad: 'ondemand', // Accepts 'ondemand' or 'progressive'
                mobileFirst: false,
                pauseOnFocus: true,
                pauseOnHover: true,
                pauseOnDotsHover: false,
                respondTo: 'window', // Can be 'window', 'slider' or 'min' (the smaller of the two)
                responsive: '',
                rows: 1,
                slide: '',
                slidesPerRow: 1,
                slidesToShow: 1,
                slidesToScroll: 1,
                speed: 300,
                swipe: true,
                swipeToSlide: false,
                touchMove: true,
                touchThreshold: 5,
                useCSS: true,
                useTransform: true,
                variableWidth: false,
                vertical: false,
                rtl: false,
                waitForAnimate: true,
                zIndex: 1000,
            });
        </script>
        <?php
    }


    /**
     * Print required slider css in the header
     *
     * @since 1.0.0
     *
     * @param string $slider_id  The id of the givien slider
     * @param array $slider_data Array of all slider data
     * @param string $output_type Determines how the slider is being output, shortcode, function etc.
     */
    public function print_slider_styles( $slider_id, $slider_data, $output_type ) {

        echo '<style type="text/css" id="sss-styles-' . $slider_id . '">';
        do_action( 'sss_print_slider_styles', $slider_id, $slider_data, $output_type );
        echo '</style>';
    }

    /**
     * Helper function for retrieving the available content types.
     *
     * @since 1.0.0
     *
     * @return array Array of image size data.
     */
    public function minify_string() {

        $instance = Blox_Common::get_instance();
        return $instance->minify_string();
    }

    /**
     * Returns the singleton instance of the class.
     *
     * @since 1.0.0
     *
     * @return object The class object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof SSS_Output ) ) {
            self::$instance = new SSS_Output();
        }

        return self::$instance;
    }
}

// Load the class.
$sss_output = SSS_Output::get_instance();
