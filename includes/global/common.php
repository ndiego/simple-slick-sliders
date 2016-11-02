<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Common class.
 *
 * @since 	1.0.0
 *
 * @package	Simple Slick Sliders
 * @author 	Nick Diego
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class SSS_Common {

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
        
    }


    /**
     * Helper function for retrieving image sizes.
     *
     * @since 1.0.0
     *
     * @global array $_wp_additional_image_sizes Array of registered image sizes.
     * @return array                             Array of slider size data.
     */
    public function get_image_sizes() {

        $sizes = array(
            array(
                'value'  => 'full',
                'name'   => __( 'Default (Original Image Size)', 'simple-slick-sliders' ),
                'width'  => 0,
                'height' => 0
            )
        );

        global $_wp_additional_image_sizes;

        $wp_sizes = get_intermediate_image_sizes();

        foreach ( (array) $wp_sizes as $size ) {
			if ( isset( $_wp_additional_image_sizes[$size] ) ) {
				$width 	= absint( $_wp_additional_image_sizes[$size]['width'] );
				$height = absint( $_wp_additional_image_sizes[$size]['height'] );
			} else {
				$width	= absint( get_option( $size . '_size_w' ) );
				$height	= absint( get_option( $size . '_size_h' ) );
			}

			if ( ! $width && ! $height ) {
				$sizes[] = array(
				    'value'  => $size,
				    'name'   => ucwords( str_replace( array( '-', '_' ), ' ', $size ) ),
				    'width'  => 0,
				    'height' => 0
				);
			} else {
			    $sizes[] = array(
				    'value'  => $size,
				    'name'   => ucwords( str_replace( array( '-', '_' ), ' ', $size ) ) . ' (' . $width . ' &#215; ' . $height . ')',
				    'width'  => $width,
				    'height' => $height
				);
            }
		}

        $sizes[] = array(
            'value'  => 'custom',
            'name'   => __( 'Custom', 'simple-slick-sliders' ),
            'width'  => 0,
            'height' => 0
        );

        return apply_filters( 'sss_image_sizes', $sizes );
    }


	/**
     * Helper function for retrieving all available content types.
     *
     * @since 1.0.0
     *
     * @return array Array of all available content types.
     */
    public function get_slider_types() {

    	$slider_types = array();

    	return apply_filters( 'sss_slider_type', $slider_types );
    }


    /**
     * Helper method for retrieving the content defaults
     *
     * @since 1.2.0
     *
     * @return array Array of all enabled content types
     */
    public function push_content_defaults() {

    }


    /**
     * Helper method to minify a string of data. Courtesy of Thomas Griffin (Solilquy)
     *
     * @since 1.0.0
     *
     * @param string $string  String of data to minify.
     * @return string $string Minified string of data.
     */
    public function minify_string( $string ) {

        $clean = preg_replace( '/((?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:\/\/.*))/', '', $string );
        $clean = str_replace( array( "\r\n", "\r", "\t", "\n", '  ', '    ', '     ' ), '', $clean );
        return apply_filters( 'sss_minified_string', $clean, $string );
    }


    /**
     * Returns the singleton instance of the class.
     *
     * @since 1.0.0
     *
     * @return object The class object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof SSS_Common ) ) {
            self::$instance = new SSS_Common();
        }

        return self::$instance;

    }

}

// Load the common class.
$SSS_Common = SSS_Common::get_instance();
