<?php
/*
Plugin Name: Springboard For Gravity Forms Add-On (Forum One)
Plugin URI: https://github.com/forumone/wordpress-springboard-gravity-forms
Description: An add-on to integrate Gravity Forms with the Springboard API and submit forms data.
Version: 1.0.1
Author: Forum One
Author URI: http://forumone.com/
*/

define( 'GF_TO_SPRINGBOARD_VERSION', '1.0.1' );

add_action( 'gform_loaded', array( 'GF_To_Springboard_Bootstrap', 'load' ), 5 );

class GF_To_Springboard_Bootstrap {

    public static function load() {

        if ( ! method_exists( 'GFForms', 'include_addon_framework' ) ) {
            return;
        }

        require_once( 'class-gfspringboard.php' );

        GFAddOn::register( 'GFSpringboard' );
    }

}

function gf_gfspringboard() {
    return GFSpringboard::get_instance();
}