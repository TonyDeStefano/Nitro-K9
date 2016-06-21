<?php

/**
 * Plugin Name: Nitro K9
 * Plugin URI: https://www.spokanewp.com/portfolio
 * Description: A custom plugin for NitroK9.com
 * Author: Spokane WordPress Development
 * Author URI: http://www.spokanewp.com
 * Version: 1.0.0
 * Text Domain: nitro-k9
 *
 * Copyright 2016 Spokane WordPress Development
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 */

require_once ( 'classes/NitroK9/Controller.php' );
require_once ( 'classes/NitroK9/PriceGroup.php' );
require_once ( 'classes/NitroK9/Price.php' );
require_once ( 'classes/NitroK9/Entry.php' );
require_once ( 'classes/NitroK9/Pet.php' );
require_once ( 'classes/NitroK9/Owner.php' );
require_once ( 'classes/NitroK9/SubmissionsTable.php' );

$nitro_k9_controller = new \NitroK9\Controller;

/* activate */
register_activation_hook( __FILE__, array( $nitro_k9_controller, 'activate' ) );

/* enqueue js and css */
add_action( 'init', array( $nitro_k9_controller, 'init' ) );

/* custom post type */
add_action( 'init', array( $nitro_k9_controller, 'custom_post_type' ) );

/* capture form post */
add_action ( 'init', array( $nitro_k9_controller, 'form_capture' ) );

/* register shortcode */
add_shortcode ( 'nitro_k9', array( $nitro_k9_controller, 'short_code' ) );

/* admin stuff */
if (is_admin() )
{
	/* Add main menu and sub-menus */
	add_action( 'admin_menu', array( $nitro_k9_controller, 'admin_menus') );

	/* register settings */
	add_action( 'admin_init', array( $nitro_k9_controller, 'register_settings' ) );

	/* admin scripts */
	add_action( 'admin_init', array( $nitro_k9_controller, 'admin_scripts' ) );

	/* custom items for custom post type */
	add_filter('gettext', array( $nitro_k9_controller, 'custom_enter_title' ) );
	add_action( 'admin_init', array( $nitro_k9_controller, 'extra_ty_email_meta' ) );
	add_action( 'save_post', array( $nitro_k9_controller, 'save_ty_email_post' ) );
	add_filter( 'manage_nitro_k9_ty_email_posts_columns', array( $nitro_k9_controller, 'add_new_columns' ) );
	add_action( 'manage_posts_custom_column' , array( $nitro_k9_controller, 'custom_columns' ) );
}