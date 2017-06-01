<?php
/**
 * Post Types
 *
 * Registers post types and taxonomies.
 *
 * @class     PH_Post_types
 * @version   1.0
 * @package   PublishingHouses/Classes
 * @category  Class
 * @author
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * PH_Post_types Class.
 */
class PH_Post_types {

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_taxonomies' ), 5 );
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 5 );
	}

	/**
	 * Register taxonomies.
	 */
	public static function register_taxonomies() {

		if ( ! is_blog_installed() ) {
			return;
		}

		if ( taxonomy_exists( 'publishing_houses_cities' ) ) {
			return;
		}

		do_action( 'publishing_houses_register_taxonomy' );

		register_taxonomy( 'publishing_houses_cities',
			apply_filters( 'publishing_houses_taxonomy_objects_publishing_house_cities', array( 'publishing_house' ) ),
			apply_filters( 'publishing_houses_taxonomy_args_publishing_house_cities', array(
                'meta_box_cb'           => array( 'PH_meta_boxes', 'taxonomy_single_meta_box' ),
				'hierarchical'          => false,
				'label'                 => __( 'Cities', 'publishing_houses' ),
				'labels' => array(
						'name'              => __( 'Publishing houses cities', 'publishing_houses' ),
						'singular_name'     => __( 'City', 'publishing_houses' ),
						'menu_name'         => _x( 'Cities', 'Admin menu name', 'publishing_houses' ),
						'search_items'      => __( 'Search cities', 'publishing_houses' ),
						'all_items'         => __( 'All cities', 'publishing_houses' ),
						'parent_item'       => __( 'Parent city', 'publishing_houses' ),
						'parent_item_colon' => __( 'Parent city:', 'publishing_houses' ),
						'edit_item'         => __( 'Edit city', 'publishing_houses' ),
						'update_item'       => __( 'Update city', 'publishing_houses' ),
						'add_new_item'      => __( 'Add new city', 'publishing_houses' ),
						'new_item_name'     => __( 'New city name', 'publishing_houses' ),
						'not_found'         => __( 'No cities found', 'publishing_houses' ),
					),
                    'show_ui'               => true,
                    'query_var'             => true,
                    'rewrite'          => array(
                        'slug'         => 'ph_city',
                        'with_front'   => true,
                        'hierarchical' => false,
                    ),
			)   )
		);

        add_action( 'save_post_publishing_house', array( 'PH_meta_boxes', 'save_single_meta_box' ) );

		do_action( 'publishing_houses_after_register_taxonomy' );
	}

	/**
	 * Register post types.
	 */
	public static function register_post_types() {
		if ( ! is_blog_installed() || post_type_exists( 'publishing_house' ) ) {
			return;
		}

		do_action( 'publishing_house_register_post_type' );

		register_post_type( 'publishing_house',
			apply_filters( 'publishing_house_register_post_type_publishing_house',
				array(
					'labels'              => array(
							'name'                  => __( 'Publishing houses', 'publishing_houses' ),
							'singular_name'         => __( 'Publishing house', 'publishing_houses' ),
							'menu_name'             => _x( 'Publishing houses', 'Admin menu name', 'publishing_houses' ),
							'add_new'               => __( 'Add publishing house', 'publishing_houses' ),
							'add_new_item'          => __( 'Add new publishing house', 'publishing_houses' ),
							'edit'                  => __( 'Edit', 'publishing_houses' ),
							'edit_item'             => __( 'Edit publishing house', 'publishing_houses' ),
							'new_item'              => __( 'New publishing house', 'publishing_houses' ),
							'view'                  => __( 'View publishing house', 'publishing_houses' ),
							'view_item'             => __( 'View publishing house', 'publishing_houses' ),
							'search_items'          => __( 'Search publishing house', 'publishing_houses' ),
							'not_found'             => __( 'No publishing houses found', 'publishing_houses' ),
							'not_found_in_trash'    => __( 'No publishing houses found in trash', 'publishing_houses' ),
							'parent'                => __( 'Parent publishing house', 'publishing_houses' ),
							'featured_image'        => __( 'Publishing house image', 'publishing_houses' ),
							'set_featured_image'    => __( 'Set publishing house image', 'publishing_houses' ),
							'remove_featured_image' => __( 'Remove publishing house image', 'publishing_houses' ),
							'use_featured_image'    => __( 'Use as publishing house image', 'publishing_houses' ),
							'insert_into_item'      => __( 'Insert into publishing house', 'publishing_houses' ),
							'uploaded_to_this_item' => __( 'Uploaded to this publishing house', 'publishing_houses' ),
							'filter_items_list'     => __( 'Filter publishing houses', 'publishing_houses' ),
							'items_list_navigation' => __( 'Publishing houses navigation', 'publishing_houses' ),
							'items_list'            => __( 'Publishing houses list', 'publishing_houses' ),
						),
					'description'         => __( 'This is where you can add new publishing house to your store.', 'publishing_houses' ),
					'public'              => false,
					'show_ui'             => true,
					'capability_type'     => 'publishing_house',
					'map_meta_cap'        => true,
					'publicly_queryable'  => true,
					'exclude_from_search' => false,
					'hierarchical'        => false, // Hierarchical causes memory issues - WP loads all records!
					'rewrite'             => array( 'slug' => 'publishing_houses', 'with_front' => true, 'feeds' => false ),
					'query_var'           => true,
					'supports'            => array( 'title', 'thumbnail' ),
					'has_archive'         => 'publishing_houses',
					'show_in_nav_menus'   => true,
					'show_in_rest'        => true,
				)
			)
		);

		do_action( 'publishing_houses_after_register_post_type' );
	}

	/**
	 * Flush rewrite rules.
	 */
	public static function flush_rewrite_rules() {
		flush_rewrite_rules();
	}

	/**
	 * Add Publishing houses Support to Jetpack Omnisearch.
	 */
	public static function support_jetpack_omnisearch() {
		if ( class_exists( 'Jetpack_Omnisearch_Posts' ) ) {
			new Jetpack_Omnisearch_Posts( 'publishing_house' );
		}
	}

	/**
	 * Added publishin house for Jetpack related posts.
	 *
	 * @param  array $post_types
	 * @return array
	 */
	public static function rest_api_allowed_post_types( $post_types ) {
		$post_types[] = 'publishing_house';

		return $post_types;
	}
}

PH_Post_types::init();
