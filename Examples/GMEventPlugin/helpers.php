<?php
if ( ! function_exists( 'add_named_action' ) ) {


    /**
     * The plugin replacements for add_action. Attach an event to an hook assiging an unique
     * identifier that allow for removal or replacement. Return the event object for easy debug
     * or method chaining
     *
     * @param string $id event uniquue identifier
     * @param string $tag hook name
     * @param callable $cb callable
     * @param int $priority priority
     * @param int $numargs accepted args
     * @param int $times number of times the event should run
     * @return Brain\Services\Event\EventItem the just attached event object
     */
    function add_named_action( $id = '', $tag = '', $cb = '', $priority = 10, $numargs = 1,
        $times = 0 ) {
        if ( ! is_string( $id ) || empty( $id ) ) return false;
        $e = gmevent_setup();
        $data = compact( 'cb', 'priority', 'numargs', 'times' );
        $e->attach( $id, $tag, $data );
        return $e->get( $id );
    }
}

if ( ! function_exists( 'add_named_filter' ) ) {


    /**
     * The plugin replacements for add_filter. Attach a filter to an hook assiging an unique
     * identifier that allow for removal or replacement. Return the event object for easy debug
     * or method chaining
     *
     * @param string $id event uniquue identifier
     * @param string $tag hook name
     * @param callable $cb callable
     * @param int $priority priority
     * @param int $numargs accepted args
     * @param int $times number of times the event should run
     * @return Brain\Services\Event\EventItem the just attached event object
     */
    function add_named_filter( $id = '', $tag = '', $cb = '', $priority = 10, $numargs = 1,
        $times = 0 ) {
        if ( ! is_string( $id ) || empty( $id ) ) return false;
        $e = gmevent_setup();
        $data = compact( 'cb', 'priority', 'numargs', 'times' );
        $e->attach_filter( $id, $tag, $data );
        return $e->get( $id );
    }
}

if ( ! function_exists( 'replace_named_hook' ) ) {


    /**
     * Replace the callable of a registered event
     *
     * @param string $id event id to be replaced
     * @param callable $callable new callable to assign
     * @return Brain\Services\Event\EventItem the just updated event object
     */
    function replace_named_hook( $id = '', $callable = '' ) {
        if ( ! is_string( $id ) || empty( $id ) || ! is_callable( $callable ) ) return false;
        $e = gmevent_setup();
        $e->replace( $id, $callable );
        return $e->get( $id );
    }
}

if ( ! function_exists( 'remove_named_hook' ) ) {


    /**
     * Remove a registered event. Is used for both actions and filters, so is a replacements for
     * remove_action and remove_filter.
     *
     * @param string $id event to be removed
     * @return null
     */
    function remove_named_hook( $id ) {
        if ( ! is_string( $id ) || empty( $id ) ) return false;
        gmevent_setup()->detach( $id );
    }
}