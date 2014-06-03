<?php

if( file_exists( $auto = dirname(__DIR__).'/vendor/autoload.php' ) ) {
    require_once( $auto );
} elseif( file_exists( $auto = dirname(dirname(dirname(__DIR__))).'/autoload.php' ) ) {
    require_once( $auto );
}