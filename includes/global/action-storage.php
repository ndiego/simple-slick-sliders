<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Action Storage class. A very clever way to pass parameters to existing Wordpress actions that traditionally do not accept parameters.
 *
 * @since 	1.0.0
 *
 * @package	Simple Slick Sliders
 * @author  Nick Diego (w/ guidance from toscho: http://wordpress.stackexchange.com/questions/45901/passing-a-parameter-to-filter-and-action-functions)
 * @license	http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class SSS_Output_Action_Storage {

    /**
     * Filled by __construct(). Used by __call().
     *
     * @type mixed Any type you need.
     */
    private $values;


    /**
     * Stores the values for later use.
     *
     * @param  mixed $values
     */
    public function __construct( $values ) {
        $this->values = $values;
    }


    /**
     * Catches all function calls except __construct().
     *
     * Be aware: Even if the function is called with just one string as an
     * argument it will be sent as an array.
     *
     * @param  string $callback Function name
     * @param  array  $arguments
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function __call( $callback, $arguments ) {

        $output = new SSS_Output;

        if ( is_callable( array( $output, $callback ) ) ) {
            return call_user_func_array( array( $output, $callback ), $this->values );
		}

        // Wrong function called.
        throw new InvalidArgumentException( sprintf( 'File: %1$s<br>Line %2$d<br>Not callable: %3$s', __FILE__, __LINE__, print_r( $callback, TRUE ) ) );
    }
}
