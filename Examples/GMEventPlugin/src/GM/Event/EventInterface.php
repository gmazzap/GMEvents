<?php
namespace GM\Event;


/**
 * @package GMEvents
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPLv3
 * @author Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @version 0.1.0
 */
interface EventInterface {


    /**
     * Attach a named action
     *
     * @param string $id event id
     * @param string $hook hook name
     * @param array $data event data
     * @return null
     * @access public
     */
    function attach( $id, $hook, $data = array() );


    /**
     * Attach a named filter
     *
     * @param string $id event id
     * @param string $hook hook name
     * @param array $data event data
     * @return null
     * @access public
     */
    function attach_filter( $id, $hook, $data = array() );


    /**
     * Remove a named event (both actions and filters)
     *
     * @param string $id the event to remove
     * @return null
     * @access public
     */
    function detach( $id = '' );


    /**
     * Replace the callable of an existing event
     *
     * @param string $id the target event id
     * @param callable $callable the new callable for the event
     * @return null
     * @access public
     */
    function replace( $id = '', $callable = '' );


    /**
     * Get all the registered events
     *
     * @return array all the events objects
     * @access public
     */
    function get_all();


    /**
     * Get a single event
     *
     * @param string $id the event to retrieve
     * @return null|GM\Event\EventItem
     * @access public
     */
    function get( $id = '' );


    /**
     * Get all events hooking a specific hook
     *
     * @param string $hook the hook to retrieve
     * @return array
     * @access public
     */
    function get_hooking( $hook = '' );


    /**
     * Fire the registered events on 'all' hook
     *
     * @return null
     * @access public
     */
    function fire();


    /**
     * All filter hooks call this function that run the proper callable looking into an helper
     * static variable. Being a static function is easy to remove
     *
     * @static
     * @return mixed the result of event callable
     * @access public
     */
    static function filter_proxy();


    /**
     * All action hooks ccall this function that run the proper callable looking into an helper
     * static variable. Being a static function is easy to remove
     *
     * @static
     * @return null
     * @access public
     */
    static function action_proxy();
}