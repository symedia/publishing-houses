<?php
/*
Plugin Name: Publishing houses
Plugin URI: https://github.com/symedia/publishing-houses
Description: Publishing houses for shop of comics.
Version: 1.0
Author: Gregory V Lominoga (Gromodar)
Author URI: http://symedia.ru
E-Mail: info@symedia.ru
GitHub Plugin URI: https://github.com/symedia/publishing-houses
*/

/*  Copyright 2017 Gregory V Lominoga (email: lominogagv@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'PublishingHouses' ) ) :

/**
 * Main PublishingHouses Class.
 *
 * @class PublishingHouses
 * @version	1.0
 */
final class PublishingHouses {

    /**
	 * PublishingHouses version.
	 *
	 * @var string
	 */
	public $version = '1.0';

	/**
	 * The single instance of the class.
	 *
	 * @var PublishingHouses
	 */
	protected static $_instance = null;

	/**
	 * Main PublishingHouses Instance.
	 *
	 * Ensures only one instance of PublishingHouses is loaded or can be loaded.
	 *
	 * @static
	 * @return PublishingHouses - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * PublishingHouses Constructor.
	 */
	public function __construct() {

        $this->define( 'PH_ABSPATH', dirname( __FILE__ ) . '/' );

		$this->includes();
		$this->init_hooks();

		do_action( 'publishing_houses_loaded' );
	}

	/**
	 * Define constant if not already set.
	 *
	 * @param  string $name
	 * @param  string|bool $value
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Hook into actions and filters.
	 */
	private function init_hooks() {
		register_activation_hook( __FILE__, array( 'PH_Install', 'install' ) );
		add_action( 'init', array( $this, 'init' ), 0 );
	}

	/**
	 * Init WooCommerce when WordPress Initialises.
	 */
	public function init() {
		// Before init action.
		do_action( 'before_publishin_houses_init' );

		// Classes/actions loaded for the frontend and for ajax requests.
		if ( $this->is_request( 'frontend' ) ) {

		}
		// Init action.
		do_action( 'publishing_house_init' );
	}

	/**
	 * What type of request is this?
	 *
	 * @param  string $type admin, ajax, cron or frontend.
	 * @return bool
	 */
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin' :
				return is_admin();
			case 'ajax' :
				return defined( 'DOING_AJAX' );
			case 'cron' :
				return defined( 'DOING_CRON' );
			case 'frontend' :
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}

	/**
	 * Include required files used in admin and on the frontend.
	 */
	public function includes() {

    	include_once( PH_ABSPATH . 'includes/class-ph-autoloader.php' );
        include_once( PH_ABSPATH . 'includes/class-ph-meta-boxes.php' );
        include_once( PH_ABSPATH . 'includes/class-ph-post-types.php' );
    	include_once( PH_ABSPATH . 'includes/class-ph-install.php' );
    	include_once( PH_ABSPATH . 'includes/class-ph-admin.php' );
    	include_once( PH_ABSPATH . 'includes/class-ph-frontend.php' );
    }



}

endif;

/**
 * Main instance of PublishingHouses.
 *
 * Returns the main instance of PH to prevent the need to use globals.
 *
 * @return PublishingHouses
 */
function PH() {
	return PublishingHouses::instance();
}

// Global for backwards compatibility.
$GLOBALS['publishing_houses'] = PH();