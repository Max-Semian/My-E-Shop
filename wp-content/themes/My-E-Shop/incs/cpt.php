<?php

add_action('init', function () {

    register_post_type('slider', array(
        'labels' => array(
            'name' => __('slider', 'My-E-Shop'),
            'singular_name' => __('Slider', 'My-E-Shop'),
            'add_new' => __('Add new slide', 'My-E-Shop'),
            'add_new_item' => __('New Slide', 'My-E-Shop'),
            'edit_item' => __('Edit', 'My-E-Shop'),
            'new_item' => __('New Slide', 'My-E-Shop'),
            'view_item' => __('View', 'My-E-Shop'),
            'menu_name' => __('Slide', 'My-E-Shop'),
            'all_items' => __('All Slides', 'My-E-Shop'),
        ),
        'public' => true,
        'supports' => array('title', 'editor', 'thumbnail', ),
        'menu_icon' => 'dashicons-format-gallery',
        'show_in_rest' => true,
    ));
} );