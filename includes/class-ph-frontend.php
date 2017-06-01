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

class PH_Frontend {

    public static function init() {

        add_action( 'the_post', array( 'PH_Frontend', 'get_products' ) );

        add_filter( 'template_include', array('PH_Frontend', 'page_template') );
    }

    public function page_template( $page_template )
    {
        //global $wp_query;
        //var_dump( $wp_query );
        if ( is_post_type_archive( 'publishing_house' ) ) {
            $page_template = PH_ABSPATH . '/templates/publishing-houses.php';
        }
        if ( is_singular( 'publishing_house' ) ) {
            $page_template = PH_ABSPATH . '/templates/publishing-house.php';
        }
        return $page_template;
    }

    public function get_products( $post ) {

        $post->products = get_posts( array(
            'post_type' => 'product',
                'meta_query' => array(
                    array(
                        'key' => '_publishing_houses',
                        'value' => $post->ID
                    )
                ) ) );

        return $post;

    }


}

PH_Frontend::init();