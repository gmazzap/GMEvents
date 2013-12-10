<?php
/**
 * Plugin Name: GM Events
 * Description: Extend WP plugin API to use named events that are easy to remove, to replace and to debug.
 * Version: 0.1.0
 * Author: Giuseppe Mazzapica
 * Author URI: http://gm.zoomlab.it
 * License: GPLv3
 */
/*  Copyright 2013 Giuseppe Mazzapica  (email : <giuseppe.mazzapica@gmail.com>)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */


/**
 * Return an instance of plugin service class
 *
 * @return GM\Event\Event
 * @access public
 */
function gmevent_setup() {
    static $event = NULL;
    if ( is_null( $event ) ) {
        require_once( plugin_dir_path( __FILE__ ) . 'GM_Loader.php' );
        GM_Loader::register( 'GM', plugin_dir_path( __FILE__ ) . 'src' );
        $item = new GM\Event\EventItem;
        $factory = new GM\Event\EventFactory( $item );
        $event = new GM\Event\Event( $factory );
        $event->setup();
    }
    return $event;
}
gmevent_setup();

require_once( plugin_dir_path( __FILE__ ) . 'helpers.php' );
