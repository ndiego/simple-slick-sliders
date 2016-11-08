<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Creates the main SSS shortcode
 *
 * @since 	1.0.0
 *
 * @package	Simple Slick Sliders
 * @author 	Nick Diego
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class SSS_Shortcode {

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

		add_shortcode( 'simple-slick-sliders', array( $this, 'main_shortcode' ) );
    }


	/**
	 * Shortcode: Print page title
     *
     * @since 1.0.0
     *
     * @param array $atts  An array shortcode attributes
     */
	public function main_shortcode( $atts ) {

		$atts = shortcode_atts( array(
			'id' => '',
		), $atts );

        $slider_id = $atts['id'];

		if ( empty( $slider_id ) ) {
			return;
		}

        // We need to use output buffering here to ensure the slider content is contained in the wrapper div
        ob_start();
        do_action( 'sss_shortcode_print_slider', $slider_id, 'shortcode' );
        $output = ob_get_clean();

        return $output;
	}


    /**
     * Returns the singleton instance of the class.
     *
     * @since 1.0.0
     *
     * @return object The class object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof SSS_Shortcode ) ) {
            self::$instance = new SSS_Shortcode();
        }

        return self::$instance;
    }
}

// Load the class.
$sss_shortcode = SSS_Shortcode::get_instance();
