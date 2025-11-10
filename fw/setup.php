<?php
require_once 'core/framework.class.php';

/*
 * Init and override
 */
$fw = new FW(
	array(
		'queue'			=> ['jquery', 'aos', 'match-height', 'slick', 'chosen', 'fancybox', 'sticky', 'cookie', 'scrollMagic', 'scrollMagicDebug'],
		'js_files'		=> ['lib/turn.min.js', 'main.js', 'cardgame.js'],
		'css_files'		=> ['main.css'],
		'options_pages' => ['General', 'Boutique'],
	)
);

/*
 * Register strings
 */
$fw->register_strings([
	['kantaloup', 'no-account-yet', 'General'],
	['kantaloup', 'Merci à nos partenaires', 'General'],
	['kantaloup', 'Lire la vidéo', 'General'],
	['kantaloup', 'Ajoutez %s à votre commande pout obtenir une carte cadeau de 25$', 'General'],
	['kantaloup', "Ajoute %s livre à ta commande et le montant de 24,95$ équivalant à un livre à couverture souple sera automatiquement déduit de ta facture totale.", 'General'],
	['kantaloup', "Ajoute %s livres à ta commande et le montant de 24,95$ équivalant à un livre à couverture souple sera automatiquement déduit de ta facture totale.", 'General'],
	['kantaloup', "En savoir plus", 'General'],
	['kantaloup', "Jours", 'General'],
	['kantaloup', "Heures", 'General'],
	['kantaloup', "Minutes", 'General'],

	['kantaloup', 'sign-in', 'Header'],

	['kantaloup', 'discover-this', 'Blogue'],
	['kantaloup', 'recent-articles', 'Blogue'],
	['kantaloup', 'filter-by-category', 'Blogue'],
	['kantaloup', 'most-popular', 'Blogue'],
	['kantaloup', 'see-all-articles', 'Blogue'],
	['kantaloup', 'published-on', 'Blogue'],
	['kantaloup', 'read-more', 'Blogue'],
	['kantaloup', 'interested-articles', 'Blogue'],
	['kantaloup', 'instagram', 'Blogue'],
	['kantaloup', 'news-instagram', 'Blogue'],

	['kantaloup', 'copyright', 'Footer'],
	['kantaloup', 'web-dev-by', 'Footer'],
	['kantaloup', 'ig-creative', 'Footer'],
	['kantaloup', 'delivery-condition', 'Footer'],

	['kantaloup', 'email', 'Formulaire'],
	['kantaloup', 'password', 'Formulaire'],
	['kantaloup', 'login', 'Formulaire'],
	['kantaloup', 'signup', 'Formulaire'],
	['kantaloup', 'reset-password', 'Formulaire'],
	['kantaloup', 'forgot-password', 'Formulaire'],
	['kantaloup', 'lost-password', 'Formulaire'],
	['kantaloup', 'no-account-yet', 'Formulaire'],
	['kantaloup', 'have-account', 'Formulaire'],
	['kantaloup', 'new-password', 'Formulaire'],
	['kantaloup', 'confirm-new-password', 'Formulaire'],

	['kantaloup', 'Nous vous enverrons votre code promotionnel!', 'Code promotionnel'],
	['kantaloup', '* Vous avez 24h pour utiliser votre code', 'Code promotionnel'],
	['kantaloup', 'Voir le code', 'Code promotionnel'],
	['kantaloup', 'Monlivre10', 'Code promotionnel'],

	['kantaloup', 'product-not-there', 'item'],
	['kantaloup', 'write-us', 'item'],
	['kantaloup', 'add-to', 'item'],
	['kantaloup', 'price', 'item'],
	['kantaloup', 'your-choice', 'item'],

	['kantaloup', 'delete', 'Cart'],
	['kantaloup', 'ajouter-livre', 'Cart'],
	['kantaloup', "Passer au paiement", 'Cart'],
	['kantaloup', "Rafraichir la page", 'Cart'],
	['kantaloup', "Nous éprouvons présentement des difficulté avec l'affichage des produits dans le panier. Si vous ne voyez pas le produits que vous venez d'ajouter, rafraichissez la page ou passez à l'étape suivante pour voir la liste à jour de votre panier.", 'Cart'],

	['kantaloup', 'back-to-shop', 'WooCommerce'],
	['kantaloup', 'edit-book', 'WooCommerce'],
	['kantaloup', 'weve-applied-your-daycare-pricing', 'WooCommerce'],
	['kantaloup', 'view-product-preview', 'WooCommerce'],
	['kantaloup', 'free-shipping', 'WooCommerce'],
	['kantaloup', 'giftcard-75', 'WooCommerce'],
	['kantaloup', 'giftcard-100', 'WooCommerce'],
	['kantaloup', 'xmas-book-promo', 'WooCommerce'],
	['kantaloup', 'livre-gratuit', 'WooCommerce'],
	['kantaloup', 'livre-gratuit', 'WooCommerce'],

	['kantaloup', 'see-categories', 'Shop'],

	['kantaloup', 'Commencer l\'activité!', 'Workshop'],
	['kantaloup', 'Aperçu', 'Workshop'],
	['kantaloup', 'Choisir', 'Workshop'],
	['kantaloup', 'Oups! Ce n\'est pas la bonne image. Essaie de nouveau.', 'Workshop'],
	['kantaloup', 'Bravo!', 'Workshop'],
	['kantaloup', 'Continuer', 'Workshop'],
	['kantaloup', 'Vérifier mon livre', 'Workshop'],
	['kantaloup', 'J\'ai vérifié mon livre', 'Workshop'],
	['kantaloup', 'Félicitations! Ton livre a été ajouté au panier!', 'Workshop'],
]);

/*
 * Register CPT's
 */
$fw->register_cpt('faq', [
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
],
[
	'menu_icon'			=> 'dashicons-editor-help',
	'public'             => true,
	'publicly_queryable' => true,
	'show_ui'            => true,
	'show_in_menu'       => true,
	'query_var'          => true,
	'rewrite'            => array( 'slug' => 'faq' ),
	'capability_type'    => 'post',
	'has_archive'        => false,
	'hierarchical'       => false,
	'menu_position'      => null,
	'supports'           => array( 'title', 'author', 'editor'  )
]);

$fw->register_cpt('partner', [
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
],
[
	'menu_icon'			=> 'dashicons-share',
	'public'             => true,
	'publicly_queryable' => true,
	'show_ui'            => true,
	'show_in_menu'       => true,
	'query_var'          => true,
	'rewrite'            => array( 'slug' => 'partenaire' ),
	'capability_type'    => 'post',
	'has_archive'        => false,
	'hierarchical'       => false,
	'menu_position'      => null,
	'supports'           => array( 'title', 'author', 'editor', 'thumbnail'  )
]);

$fw->register_cpt('prix',
	array(
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
	array(
		'menu_icon'			=> 'dashicons-awards',
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'prix' ),
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title')
	)
);

/*
 * Register taxonomies
 */
$fw->register_taxonomies('faq-category', 'faq', [
	'label' => 'Categories',
	'rewrite' => array( 'slug' => 'faq-category' ),
	'hierarchical' => true
]);


/*
 * Register nav menus
 */
$fw->register_nav_menus([
	'primary' => 'Navigation primaire',
	'footer' => 'Navigation footer',
]);

