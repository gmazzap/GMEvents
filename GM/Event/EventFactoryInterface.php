<?php
namespace GM\Event;


/**
 * @package GMEvents
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3
 * @author Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @version 0.1.0
 */
interface EventFactoryInterface {


    /**
     * Create an event object and setup it according to arguments received
     *
     * @param string $id event id
     * @param string $hook hook name
     * @param array $data event data
     * @return null|GM\Event\EventItem
     * @access public
     */
    function factory( $id, $hook, $data = array() );


    /**
     * Save an newly created event object and its hook in the helper arrays
     *
     * @param GM\Event\EventItem $e
     * @return null
     * @access public
     */
    function register( EventItem $e );
}