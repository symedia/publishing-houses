<?php

/*
 * Writen by Gregory V Lominoga (Gromodar)
 * E-Mail: lominogagv@gmail.com
 * Produced by Symedia studio
 * http://symedia.ru
 * E-Mail: info@symedia.ru
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 *
 */
class PH_Install {

	/**
	 * Hook in tabs.
	 */
	public static function init() {
        add_action( 'init', array( __CLASS__, 'install' ), 5 );
	}

	/**
	 * Install WC.
	 */
	public static function install() {
		global $wpdb;

		if ( ! is_blog_installed() ) {
			return;
		}

		if ( ! defined( 'PH_INSTALLING' ) ) {
			define( 'PH_INSTALLING', true );
		}

		self::create_capabilities();

		// Register post types
		PH_Post_types::register_post_types();
		PH_Post_types::register_taxonomies();

		self::create_terms();

		// Flush rules after install
		do_action( 'publishing_houses_flush_rewrite_rules' );

		// Trigger action
		do_action( 'publishing_houses_installed' );
	}

	/**
	 * Create capabilities.
	 */
	public static function create_capabilities() {
		global $wp_roles;

		if ( ! class_exists( 'WP_Roles' ) ) {
			return;
		}

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}

		add_role( 'publishing_house', __( 'Publishing house', 'publishing_houses' ),
                array(
			'read' 					=> true,
		) );

		$capabilities = self::get_capabilities();

		foreach ( $capabilities as $cap_group ) {
			foreach ( $cap_group as $cap ) {
				$wp_roles->add_cap( 'administrator', $cap );
				$wp_roles->add_cap( 'publishing_house', $cap );
			}
		}
	}

	/**
	 * Get capabilities for Publishing houses - these are assigned to admin manager during installation or reset.
	 *
	 * @return array
	 */
	 private static function get_capabilities() {
		$capabilities = array();

		$capabilities['publishing_house'] = array(
			'publishing_house',
		);

		$capability_types = array( 'publishing_house' );

		foreach ( $capability_types as $capability_type ) {

			$capabilities[ $capability_type ] = array(
				// Post type
				"edit_{$capability_type}",
				"read_{$capability_type}",
				"delete_{$capability_type}",
				"edit_others_{$capability_type}",
				"publish_{$capability_type}",
				"read_private_{$capability_type}",
				"create_posts{$capability_type}",

				// Terms
				"manage_{$capability_type}_terms",
				"edit_{$capability_type}_terms",
				"delete_{$capability_type}_terms",
				"assign_{$capability_type}_terms",
			);
		}

		return $capabilities;
	}

	/**
	 * Add the default terms for PH taxonomies - publishing houses.
	 */
	public static function create_terms() {
		$taxonomies = array(
			'publishing_houses_cities' => array(
				'new_york'    => 'New York',
				'los_angeles' => 'Los Angeles',
				'chicago'     => 'Chicago',
				'houston'     => 'Houston',
			),
		);

		foreach ( $taxonomies as $taxonomy => $terms ) {
			foreach ( $terms as $term_slug => $term_name ) {
				if ( ! get_term_by( 'name', $term_name, $taxonomy ) ) {
					wp_insert_term( $term_name, $taxonomy, array( 'slug' => $term_slug ) );
				}
			}
		}
	}

}

PH_Install::init();