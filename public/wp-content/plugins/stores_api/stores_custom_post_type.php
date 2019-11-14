<?php

function cptui_register_my_cpts_store() {

    /**
     * Post Type: Stores.
     */

    $labels = [
        "name" => __( "Stores", "twentynineteen" ),
        "singular_name" => __( "Store", "twentynineteen" ),
    ];

    $args = [
        "label" => __( "Stores", "twentynineteen" ),
        "labels" => $labels,
        "description" => "",
        "public" => true,
        "publicly_queryable" => true,
        "show_ui" => true,
        "delete_with_user" => false,
        "show_in_rest" => true,
        "rest_base" => "store",
        "rest_controller_class" => "WP_REST_Posts_Controller",
        "has_archive" => false,
        "show_in_menu" => true,
        "show_in_nav_menus" => true,
        "delete_with_user" => false,
        "exclude_from_search" => false,
        "capability_type" => "post",
        "map_meta_cap" => true,
        "hierarchical" => false,
        "rewrite" => [ "slug" => "store", "with_front" => true ],
        "query_var" => true,
        "supports" => [ "title" ],
    ];

    register_post_type( "store", $args );
}

add_action( 'init', 'cptui_register_my_cpts_store' );


if( function_exists('acf_add_local_field_group') ):

    acf_add_local_field_group(array(
        'key' => 'group_5dcd57618f31b',
        'title' => 'Stores location',
        'fields' => array(
            array(
                'key' => 'field_5dcd576cfdc07',
                'label' => 'longitude',
                'name' => 'longitude',
                'type' => 'text',
                'instructions' => 'Longitude cords',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '13.5432342',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
            ),
            array(
                'key' => 'field_5dcd57b4fdc08',
                'label' => 'Latitude',
                'name' => 'latitude',
                'type' => 'text',
                'instructions' => 'Latitude cords',
                'required' => 1,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '34.542323',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'store',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'acf_after_title',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
    ));

endif;