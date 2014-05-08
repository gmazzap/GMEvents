This plugin was written a s a proof of concept for the blog post linked below. It is not intended to be used in real world. If you are interested in something similar have a look to [Striatum](https://github.com/Giuseppe-Mazzapica/Striatum).

_______________

WordPress plugin API has some lacks when used with OOP: even if adding or triggering events is super-easy thanks to `add_action` \ `add_filter` and `do_action` / `apply_filters` removing and event or debug it is not as easy:

See following example:

    // An hook in a object
    class aClass {
      function __construct() {
        add_action( 'init', array($this, 'foo') ); // can you remove this?
      }
      function foo() {
        do_something();
      }
    }
    
    add_action('init', function() { do_something(); } ); // and this?
     
    // For both previous examples if I want to remove the actions
    remove_action('init', ???? ); // replace the '?'...
    
    
The `GM\Event` package is an OOP component built upon the WordPress core plugin API, but allow to attach named events, i.e. events with a predictable unique identifier, and for that reason, easily removable and editable.

Being an OOP package, all events are objects where properties are all the WordPress event informations (callable, priority, accepted args) plus some additional ones:

 - the unique identifier "id" property
 - "type" property to distinguish from actions and filters
 - "times" property that allow to fire the events a certain number of times, creating self-removing events
 - "debug" property that is filled with result from `debug_backtrace` but only if `WP_DEBUG` is true

Package contains 2 other classes:

 - `GM\Event\Event` that is the "front controller" for the package, responsible to attach, detach and fire all events
 - `GM\Event\EventFactory` that is responsible to "generate" and save event objects

Package is **not** a full plugin, but is a component that can be used in every project (plugin, theme). All classes make use of dependency injection, so if some complex project make use of a DI container, implement `GM\Event` package will be super easy.

Even if is not strictly necessary, is highly recommend to use the `GM\Event\Event` class as a singleton service, and using a DI container will also help in that.

----------

See the [blog post](http://gm.zoomlab.it/2013/long-standing-issue-oop-wp-hooks-solution/)

----------

# Usage #

### Bootstrap ###

First thing needed is require all the package files or, of course, use an autoloader. The package is **full `PSR-0` compliant**.

    require_once '/path/to/package/GM/Event/EventFactoryInterface.php';
    require_once '/path/to/package/GM/Event/EventInterface.php';
    require_once '/path/to/package/GM/Event/EventItem.php';
    require_once '/path/to/package/GM/Event/EventFactory.php';
    require_once '/path/to/package/GM/Event/Event.php';
    // autoloader is better, isn't it?

After that is possible to instantiate the `GM\Event\Event` taking care of dependencies. Then launch the `setup()` method on the instance:

    $item = new GM\Event\EventItem;
    $factory = new GM\Event\EventFactory( $item );
    $event = new GM\Event\Event( $factory );
    $event->setup();

### Attaching Events ###

    $data = array(
    'cb' => function( $foo, $bar ) { echo 'Foo is ' . $foo . ' and Bar is'  $bar; }, // event callable
      'priority' => 20,
      'numargs' => 2, // accepted args
      'times' => 1 // this event will run once
    );
    // event is the event instance created in the previous code block
    $testevent = $event->attach( 'myplugin.eventid', 'wp_loaded', $data );
    
    // debug (enable WP_DEBUG for additional info)
    echo $testevent;
    
### Detaching Events ###

    $event->detach( 'myplugin.eid');

### Replacing Event Callable ###

    $event->replace( 'myplugin.eid', function($foo, $bar) { echo "Don't like {$foo} nor {$bar}"; });

----------

# Examples: the plugin #

In "Examples" folder of repository there is a WordPress plugin that make use of the package. Probably it is not very useful in production sites, but I added it to demonstrate how to implement the package.
What this plugin does is:

 - add an autoloader for package classes
 - instantiate the `GM\Event\Event` class as singleton service
 - add 4 wrapper functions for event class methods and are modeled on WordPress plugin api functions

The functions are (self-explainatory names, I hope):

 - `add_named_action`
 - `add_named_filter`
 - `replace_named_hook`
 - `remove_named_hook`

### Plugin usage examples ###

    // add an event called 'myplugin.init', on 'init' hook that run the 'bar' method on $foo object
    
    $event1 = add_named_action('myplugin.init', 'init', array(new fooClass(), 'bar') );
    
    
    
    // like the previous, but specifying priority and number of accepeted args
    
    $event2 = add_named_action('myplugin.init', 'init', array(new fooClass(), 'bar'), 999, 2 );
    
    
    
    // like the previous, but force the action to be runned only once (last parameter)
    
    $event3 = add_named_action('myplugin.init', 'init', array(new fooClass(), 'bar'), 999, 2, 1 );
    
    
    
    // the previous can be wrote, with identical result, as
    
    $event3 = add_named_action('myplugin.init', 'init', array(new fooClass(), 'bar'), 999, 2)->run_once();
    
    
    
    // use a filter with a closure
    
    $event4 = add_named_filter('myplugin.title', 'the_title', function( $title ) {
      return strtoupper($title);
    });
    
    
    
    // Remove the previous closure filter. Easy, isn't it?
    
    remove_named_hook('myplugin.title');
    
    
    
    // Replace the callable, no matter if previous callable was a function, an object method or a closure
    
    replace_named_hook('myplugin.title', function($title) { return strtolower($title); } );
    
    
    
    // And if one want only the first 3 titles uppper cased?
    
    $event5 = add_named_filter('myplugin.casetitles', 'the_title', function( $title ) {
      return strtoupper($title);
    })->run(3);
    
    
    
    // Creative use of replace_named_hook: alternate styling for posts
    
    function post_even( $classes ) {
      replace_named_hook('mytheme.post_class', 'post_odd' ); // swap the filter
      $classes[] = 'even';
      return $classes;
    }
    
    function post_odd( $classes ) {
      replace_named_hook('mytheme.post_class', 'post_even' ); // swap the filter
      $classes[] = 'odd';
      return $classes;
    }
    
    add_named_filter('mytheme.post_class', 'post_class', 'post_even'); // start with even
    
    
    
    // EventItem has a magic __toString method for runtime easy debug
    
    $event6 = add_named_action('myplugin.footer', 'wp_footer', 'fooCallback' );
    
    echo $event6;
    
    // With WP_DEBUG set to TRUE the previous echo will output (wrapped in <pre> tag) something like
    
    GM\Event\EventItem Object
    (
        [id] => myplugin.footer
        [hook] => wp_footer
        [callable] => fooCallback
        [priority] => 10
        [numargs] => 1
        [type] => action
        [times] => 0
        [debug] => Array
            (
                [0] => Array
                    (
                        [file] => the/wordpress/path/wp-content/plugins/GMEvent/src/GM/Event/EventFactory.php
                        [line] => 52
                        [function] => set
                        [class] => GM\Event\EventItem
                        [type] => ->
                    )
    
                [1] => Array
                    (
                        [file] => the/wordpress/path/wp-content/plugins/GMEvent/src/GM/Event/Event.php
                        [line] => 57
                        [function] => factory
                        [class] => GM\Event\EventFactory
                        [type] => ->
                    )
    
                [2] => Array
                    (
                        [file] => the/wordpress/path/wp-content/plugins/GMEvent/src/GM/Event/helpers.php
                        [line] => 23
                        [function] => attach
                        [class] => GM\Event\Event
                        [type] => ->
                    )
    
                [3] => Array
                    (
                        [file] => the/wordpress/path/wp-content/plugins/test.php
                        [line] => 12
                        [function] => add_named_action
                    )
    
                [4] => Array
                    (
                        [file] => the/wordpress/path/wp-settings.php
                        [line] => 203
                        [args] => Array
                            (
                                [0] => D:\htdocs\wptest\wp-content\plugins\Test\test.php
                            )
    
                        [function] => include_once
                    )
    
            )
    
    )

 
 
 
 
