<?php


class GM_Loader {

    private static $namespace;

    private static $path;


    public static function register( $ns = null, $path = '' ) {
        self::$namespace = $ns;
        self::$path = $path;
        \spl_autoload_register( array( __CLASS__, 'load' ) );
    }


    public static function load( $name ) {
        $name = \ltrim( $name, '/\\' );
        $namearray = \explode( '\\', $name );
        if ( \array_shift( $namearray ) !== self::$namespace ) return;
        $file = \str_replace( '\\', \DIRECTORY_SEPARATOR, $name ) . '.php';
        require \trailingslashit( self::$path ) . $file;
    }
}