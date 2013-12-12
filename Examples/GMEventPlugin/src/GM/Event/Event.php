<?php
namespace GM\Event;


/**
 * Event manager service. Attach/Detach/Fire events
 *
 * @package GMEvents
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3
 * @author Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @version 0.1.0
 */
class Event implements EventInterface {

    /**
     * @var GM\Event\EventFactory $factory
     */
    private $factory;

    /**
     * @staticvar array $proxy Helper var to store events to fire
     */
    private static $proxy = array();

    /**
     * @staticvar array $counts Helper var to count how many time an event is fired on page
     */
    private static $counts = array();


    /**
     * The constructor. Dependency injection and no more
     *
     * @param GM\Event\EventFactory $f
     * @access public
     */
    function __construct( EventFactory $f ) {
        $this->factory = $f;
    }


    function setup() {
        \add_action( 'all', array( $this, 'fire' ), 1 );
    }


    function attach( $id, $hook, $data = array() ) {
        if ( ! \is_string( $id ) || \array_key_exists( $id, $this->factory->get() ) ) return;
        $def = array(
            'cb'       => null,
            'priority' => null,
            'numargs'  => 1,
            'type'     => 'action',
            'times'    => 0
        );
        $data = \wp_parse_args( $data, $def );
        $e = $this->factory->factory( $id, $hook, $data );
        if ( $e instanceof EventItem ) $this->factory->register( $e );
    }


    function attach_filter( $id, $hook, $data = array() ) {
        if ( ! \is_array( $data ) ) return;
        $data['type'] = 'filter';
        $this->attach( $id, $hook, $data );
    }


    function detach( $id = '' ) {
        if ( \array_key_exists( $id, $this->factory->get() ) ) {
            $e = $this->get( $id );
            $action = $e->type === 'filter' ? 'filter_proxy' : 'action_proxy';
            unset( $GLOBALS['wp_filter'][$e->hook][$e->priority][__CLASS__ . '::' . $action] );
            if ( empty( $GLOBALS['wp_filter'][$e->hook][$e->priority] ) ) {
                unset( $GLOBALS['wp_filter'][$e->hook][$e->priority] );
            }
            $this->factory->delete( $id );
        }
    }


    function replace( $id = '', $callable = '' ) {
        if ( \array_key_exists( $id, $this->factory->get() ) && \is_callable( $callable ) ) {
            $e = $this->get( $id );
            $e->callable = $callable;
        }
    }


    function get_all() {
        return $this->factory->get();
    }


    function get( $id = '' ) {
        $all = $this->factory->get();
        return ( isset( $all[$id] ) ) ? $all[$id] : null;
    }


    function get_hooking( $hook = '' ) {
        $hooked = \array_keys( $this->factory->get( 'hooks' ), $hook, true );
        return ( empty( $hooked ) ) ? FALSE : $this->factory->get_event_objs( $hooked );
    }


    function fire() {
        $args = func_get_args();
        $f = \array_shift( $args );
        if ( ! empty( $f ) && $f === \current_filter() ) {
            $hooks = $this->get_hooking( $f );
            if ( ! \is_array( $hooks ) || empty( $hooks ) ) return;
            self::$proxy[$f] = $hooks;
            $this->parse_hooks( $f );
        }
    }


    static function filter_proxy() {
        $first = \func_get_arg( 0 );
        $e = self::to_parse();
        if ( ! \is_object( $e ) ) return $first;
        $args = \array_slice( \func_get_args(), 0, $e->numargs );
        return \call_user_func_array( $e->callable, $args );
    }


    static function action_proxy() {
        $e = self::to_parse();
        if ( \is_object( $e ) ) {
            $args = \array_slice( \func_get_args(), 0, $e->numargs );
            \call_user_func_array( $e->callable, $args );
        }
    }


    private static function to_parse() {
        $f = \current_filter();
        if ( empty( $f ) ) return;
        return \array_shift( self::$proxy[$f] );
    }


    private function parse_hooks( $tag ) {
        foreach ( self::$proxy[$tag] as $i => $e ) {
            if ( ! isset( self::$counts[$e->id] ) ) self::$counts[$e->id] = 0;
            self::$counts[$e->id] ++;
            if ( isset( $e->times ) && (int) $e->times > 0 && ($e->times < self::$counts[$e->id]) ) {
                unset( self::$proxy[$tag][$i] );
                $this->detach( $e->id );
                continue;
            }
            if ( $e->type === 'filter' ) {
                \add_filter( $tag, array( __CLASS__, 'filter_proxy' ), $e->priority, $e->numargs );
            } else {
                \add_action( $tag, array( __CLASS__, 'action_proxy' ), $e->priority, $e->numargs );
            }
        }
    }
}