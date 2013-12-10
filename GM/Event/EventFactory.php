<?php
namespace GM\Event;


/**
 * Create events objects from arguments
 *
 * @package GMEvents
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3
 * @author Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @version 0.1.0
 */
class EventFactory implements EventFactoryInterface {

    /**
     * @var GM\Event\EventItem
     */
    private $item;

    /**
     * Array of registered event objects
     *
     * @var array $events
     */
    private $events = array();

    /**
     * Helper array to map events to related hooks
     *
     * @var array $events
     */
    private $hooks = array();

    /**
     * Helper array used for default priority assignment
     *
     * @var array $pri
     */
    private $pri = array();


    function __construct( EventItem $i ) {
        $this->item = $i;
    }


    function factory( $id, $hook, $data = array() ) {
        if ( \array_key_exists( $id, $this->events ) ) return;
        if ( ! is_string( $hook ) || empty( $hook ) ) return;
        $e = clone $this->item;
        $data = $this->prepare_data( $data, $e );
        if ( ! empty( $data ) ) return $e->set( $id, $hook, $data );
    }


    function register( EventItem $e ) {
        $this->events[$e->id] = $e;
        $this->hooks[$e->id] = $e->hook;
        if ( ! isset( $this->pri[$e->hook] ) || ( $this->pri[$e->hook] < $e->pri ) ) {
            $this->pri[$e->hook] = $e->priority;
        }
    }


    function get_event_objs( $ids ) {
        $events = array();
        foreach ( (array) $ids as $id ) {
            $e = $this->events[$id];
            $events[$e->priority] = $e;
        }
        ksort( $events );
        return $events;
    }


    function get( $w = 'events' ) {
        if ( isset( $this->$w ) ) return $this->$w;
    }


    function delete( $id = '' ) {
        if ( isset( $this->events[$id] ) ) unset( $this->events[$id] );
        if ( isset( $this->hooks[$id] ) ) unset( $this->hooks[$id] );
    }


    private function prepare_data( $data, $e ) {
        if ( ! isset( $data['cb'] ) || ! \is_callable( $data['cb'] ) ) return;
        if ( empty( $data['priority'] ) || ! \is_int( $data['priority'] ) ) {
            $data['priority'] = isset( $this->pri[$e->hook] ) ? $this->pri[$e->hook] + 1 : 10;
        }
        if ( empty( $data['numargs'] ) || ! \is_int( $data['numargs'] ) ) $data['numargs'] = 1;
        if ( ! \in_array( $data['type'], array( 'action', 'filter' ), true ) ) {
            $data['type'] = 'action';
        }
        if ( ! \is_int( $data['times'] ) || $data['times'] < 0 ) $data['times'] = 0;
        return $data;
    }
}