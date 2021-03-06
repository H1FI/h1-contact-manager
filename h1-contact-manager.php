<?php
/*
Plugin Name: H1 Contact Manager
Plugin URI: http://h1.fi
Description: Add and manage contacts
Version: 0.3
Author: Daniel Koskinen / H1
Author URI: http://h1.fi
License: GPL2
*/
/*  Copyright 2012-2015  Daniel Koskinen (email : dani@h1.fi)

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
register_activation_hook( __FILE__, 'h1cm_activate' );

/**
 * Constants
 */
if ( !defined( 'H1CM_LABEL' ) )
    define( 'H1CM_LABEL', 'contact' );
if ( !defined( 'H1CM_PREFIX' ) )
    define( 'H1CM_PREFIX', '_' );

/**
 * Hook our functions into WP hooks
 */
add_action( 'init', 'h1cm_init' );
if ( is_admin() ) {
    h1cm_admin();
}

/**
 * Tasks to run on activation
 * @return void
 */
function h1cm_activate() {
    /**
     * Initialize post types and taxonomies,
     * so they are available when flushing rewrites
     */
    h1cm_init();
    /**
     * Flushing rewrites ensures our post type & taxonomy slugs will work.
     */
    flush_rewrite_rules();
}
/**
 * Initialize the plugin
 * 
 * @return void
 */
function h1cm_init() {
    load_plugin_textdomain( 'h1cm', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

    /**
     * Include custom post type definition
     */
    require_once( 'h1cm-post-type.php' );
    h1cm_register_post_types();
    h1cm_register_taxonomies();

    /**
     * Do actions on save (clone custom fields into title & content)
     */
    add_action( 'wp_insert_post', 'h1cm_update_post', 10, 2 );

    /**
     * Modify the_content and the_title on individual contact items
     */
    require_once( 'h1cm-views.php' );
    add_filter( 'the_content', 'h1cm_entry_content' );
}

/**
 * Admin init
 */
function h1cm_admin() {
    /**
     * Include admin customizations (meta fields etc)
     */
    require_once( 'h1cm-admin.php' );

    /**
     * Register meta boxes, support both Meta Box by Rilwis and Custom Meta Boxes by Humanmade
     */
    add_filter( 'rwmb_meta_boxes', 'h1cm_register_meta_boxes' );
    add_filter( 'cmb_meta_boxes', 'h1cm_register_meta_boxes' );

    /**
     * Move "Set featured image" box and modify text on post edit screen
     */
    add_action( 'do_meta_boxes', 'h1cm_move_featured_image_box' );
    add_filter( 'admin_post_thumbnail_html', 'h1cm_post_thumbnail_html', 10, 2 );
    add_filter( 'media_view_strings', 'h1cm_media_strings', 10, 2 );
}
