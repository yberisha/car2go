<?php

require_once 'vendor/autoload.php';

//! Register SCSS and JS scripts.
function enqueue_theme_assets() {
    wp_enqueue_style('front_style', get_stylesheet_directory_uri() . '/assets/dist/css/front.css', [], wp_get_theme(get_template())->Version);
    wp_enqueue_script('front_script', get_stylesheet_directory_uri() . '/assets/dist/js/front.js', [], wp_get_theme(get_template())->Version, true);
    wp_enqueue_script('front_admin_script', get_stylesheet_directory_uri() . '/assets/dist/js/admin/app.js', [], wp_get_theme(get_template())->Version, true);
}
add_action('wp_enqueue_scripts', 'enqueue_theme_assets');

// Remove the editor from the 'post' post type
function remove_editor_from_post() {
    remove_post_type_support('post', 'editor');
}
add_action('init', 'remove_editor_from_post');

// Remove the editor from the 'page' post type
function remove_editor_from_page() {
    remove_post_type_support('page', 'editor');
}
add_action('init', 'remove_editor_from_page');

//! Check if acf field plugin is active
include_once(ABSPATH . 'wp-admin/includes/plugin.php');
if (!is_plugin_active('advanced-custom-fields-pro/acf.php')) {
    if (is_admin()) {
        echo "<h2 style='text-align: center; color:red;'>Warning: You need to have ACF Pro Activated!</h2>";
    }
}

add_filter('acf/settings/save_json', function($path) {
    $path = get_template_directory() . '/acf-json';
    return $path;
});

add_filter('acf/settings/load_json', function($paths) {
    $paths[] = get_template_directory() . '/acf-json';
    return $paths;
});

// Register navigation menus
add_action('after_setup_theme', function () {
    register_nav_menus([
        'header-menu' => __('Header Menu', 'my-theme'),
    ]);
});

// Add menu to Timber context
add_filter('timber/context', function ($context) {
    $context['menu'] = [
        'items' => Timber::get_menu('header-menu')->items,
    ];
    return $context;
});

if (function_exists('acf_add_options_page')) {
    acf_add_options_page(array(
        'page_title' => 'Header Settings',
        'menu_title' => 'Header Settings',
        'menu_slug' => 'header-settings',
        'capability' => 'edit_posts',
        'redirect' => false
    ));
}

add_filter('timber/context', function($context) {
    $context['header'] = [
        'menu_items' => Timber::get_menu('header-menu')->items,
        'site_name' => get_bloginfo('name'),
        'home_url' => home_url(),
        'logo' => get_field('header_logo', 'option'),
    ];
    return $context;
});