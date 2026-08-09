<?php

/**
 * Custom Post Types Configuration
 * 
 * Registers custom post types for the theme
 */

add_action('init', function () {
	register_post_type('faq', array(
		'labels' => array(
			'name'               => 'FAQ',
			'singular_name'      => 'FAQ',
			'add_new'            => 'Ajouter',
			'add_new_item'       => 'Ajouter',
			'edit_item'          => 'Modifier',
			'new_item'           => 'Nouveau',
			'all_items'          => 'Tous',
			'view_item'          => 'Voir',
			'search_items'       => 'Recherche',
			'not_found'          => 'Introuvable',
			'not_found_in_trash' => 'Introuvable dans la corbeille',
			'parent_item_colon'  => '',
			'menu_name'          => 'FAQ',
		),
		'menu_icon'            => 'dashicons-editor-help',
		'public'               => true,
		'publicly_queryable'   => true,
		'show_ui'              => true,
		'show_in_menu'         => true,
		'query_var'            => true,
		'rewrite'              => array('slug' => 'faq'),
		'capability_type'      => 'post',
		'has_archive'          => false,
		'hierarchical'         => false,
		'menu_position'        => null,
		'supports'             => array('title', 'author', 'editor'),
	));
	
	register_post_type('partner', array(
		'labels' => array(
			'name'               => 'Partenaires',
			'singular_name'      => 'Partenaires',
			'add_new'            => 'Ajouter',
			'add_new_item'       => 'Ajouter',
			'edit_item'          => 'Modifier',
			'new_item'           => 'Nouveau',
			'all_items'          => 'Tous',
			'view_item'          => 'Voir',
			'search_items'       => 'Recherche',
			'not_found'          => 'Introuvable',
			'not_found_in_trash' => 'Introuvable dans la corbeille',
			'parent_item_colon'  => '',
			'menu_name'          => 'Partenaires',
		),
		'menu_icon'            => 'dashicons-share',
		'public'               => true,
		'publicly_queryable'   => true,
		'show_ui'              => true,
		'show_in_menu'         => true,
		'query_var'            => true,
		'rewrite'              => array( 'slug' => 'partenaire' ),
		'capability_type'      => 'post',
		'has_archive'          => false,
		'hierarchical'         => false,
		'menu_position'        => null,
		'supports'             => array( 'title', 'author', 'editor', 'thumbnail' )
	));
	
	register_post_type('prix', array(
		'labels' => array(
			'name'               => 'Prix',
			'singular_name'      => 'Prix',
			'add_new'            => 'Ajouter',
			'add_new_item'       => 'Ajouter',
			'edit_item'          => 'Modifier',
			'new_item'           => 'Nouveau',
			'all_items'          => 'Tous',
			'view_item'          => 'Voir',
			'search_items'       => 'Recherche',
			'not_found'          => 'Introuvable',
			'not_found_in_trash' => 'Introuvable dans la corbeille',
			'parent_item_colon'  => '',
			'menu_name'          => 'Prix',
		),
		'menu_icon'            => 'dashicons-awards',
		'public'               => true,
		'publicly_queryable'   => true,
		'show_ui'              => true,
		'show_in_menu'         => true,
		'query_var'            => true,
		'rewrite'              => array( 'slug' => 'prix' ),
		'capability_type'      => 'post',
		'has_archive'          => false,
		'hierarchical'         => false,
		'menu_position'        => null,
		'supports'             => array( 'title' )
	));

    register_taxonomy('faq-category', 'faq', array(
        'label' => 'Categories',
        'rewrite' => array('slug' => 'faq-category'),
        'hierarchical' => true
    ));
});

