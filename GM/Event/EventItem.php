<?php
namespace GM\Event;


/**
 * Event item object
 *
 * @package GMEvents
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3
 * @author Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @version 0.1.0
 */
class EventItem {

    /**
     * @var string $id event unique identifier
     */
    public $id;

    /**
     * @var string $hook hook name
     */
    public $hook;

    /**
     * @var callable $callable event callable
     */
    public $callable;

    /**
     * @var int $priority event priority
     */
    public $priority;

    /**
     * @var int $numargs number of arguments accepted by event callable
     */
    public $numargs;

    /**
     * @var string $type event type: can be 'action' or 'filter'
     */
    public $type;

    /**
     * @var int $times how many times for a page the event should be fired
     */
    public $times;

    /**
     * @var array $debug debug backtrace for events. empty if WP_DEBUG is false
     */
    public $debug;


    /**
     * Magic method to get object properties
     *
     * @param string $name property name
     * @return mixed property value
     */
    function __get( $name ) {
        if ( isset( $this->$name ) ) return $this->$name;
    }


    /**
     * Magic method to print event information when object is echoed
     *
     * @return string evend data
     * @access public
     */
    function __toString() {
        $out = '<pre>';
        \ob_start();
        \print_r( $this );
        $out .= \ob_get_clean();
        return $out .= '</pre>';
    }


    /**
     * Setup event properties staring form an array of data
     *
     * @param string $id event id
     * @param string $hook hook name
     * @param array $data event data
     * @return GM\Event\EventItem
     * @access public
     */
    function set( $id, $hook, $data = array() ) {
        if ( ! \is_string( $id ) || ! \is_string( $hook ) ) return;
        if ( ! isset( $data['cb'] ) || ! \is_callable( $data['cb'] ) ) return;
        $this->id = $id;
        $this->hook = $hook;
        $this->callable = $data['cb'];
        foreach ( array( 'priority', 'numargs', 'type', 'times' ) as $n ) {
            $this->$n = $data[$n];
        }
        if ( \defined( 'WP_DEBUG' ) && \WP_DEBUG ) {
            $this->debug = \debug_backtrace( \DEBUG_BACKTRACE_IGNORE_ARGS, 5 );
        }
        return $this;
    }


    /**
     * Shortucut method to set the times property to 1
     *
     * @return GM\Event\EventItem
     * @access public
     */
    function run_once() {
        $this->times = 1;
        return $this;
    }


    /**
     * Set the $times property to a given number
     *
     * @param int $n times to setup for the event
     * @return GM\Event\EventItem
     * @access public
     */
    function run( $n = 0 ) {
        $this->times = intval( $n );
        return $this;
    }
}