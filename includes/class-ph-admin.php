<?php

/*
 * Writen by Gregory V Lominoga (Gromodar)
 * E-Mail: lominogagv@gmail.com
 * Produced by Symedia studio
 * http://symedia.ru
 * E-Mail: info@symedia.ru
 */

class PH_Admin {


    public function __construct() {

        add_action( 'admin_init', 'woo_check' );

		add_filter( 'manage_publishing_house_posts_columns', array( $this, 'publishing_houses_columns' ) );
		add_filter( 'manage_product_posts_columns', array( $this, 'woo_product_columns' ) );

		add_action( 'manage_publishing_house_posts_custom_column', array( $this, 'render_publishing_houses_columns' ), 2 );
		add_action( 'manage_product_posts_custom_column', array( $this, 'render_woo_product_columns' ), 2 );

    }

    public function woo_product_columns( $existing_columns ) {
//		if ( empty( $existing_columns ) && ! is_array( $existing_columns ) ) {
//			$existing_columns = array();
//		}

		unset( $existing_columns['featured'], $existing_columns['product_type'], $existing_columns['date'] );

		$existing_columns['publishing_house']  = __( 'Pub. house', 'publishing houses' );

		$existing_columns['featured']     = '<span class="wc-featured parent-tips" data-tip="' . esc_attr__( 'Featured', 'woocommerce' ) . '">' . __( 'Featured', 'woocommerce' ) . '</span>';
		$existing_columns['product_type'] = '<span class="wc-type parent-tips" data-tip="' . esc_attr__( 'Type', 'woocommerce' ) . '">' . __( 'Type', 'woocommerce' ) . '</span>';
		$existing_columns['date']         = __( 'Date', 'woocommerce' );

		return $existing_columns;
    }

    public function render_woo_product_columns( $column ) {
		global $post, $the_product;

		if ( empty( $the_product ) || $the_product->get_id() != $post->ID ) {
			$the_product = wc_get_product( $post );
		}

		// Only continue if we have a product.
		if ( empty( $the_product ) ) {
			return;
		}

        if ( $column === 'publishing_house' ) {

            $publishing_house_id = get_post_meta($post->ID, 'publishing_house', true);

            $publishing_house_object = get_post($publishing_house_id);

            echo $publishing_house_object->post_title;
        }
    }

	/**
	 * Define custom columns for publishing houses.
	 * @param  array $existing_columns
	 * @return array
	 */
	public function publishing_houses_columns( $existing_columns ) {
		if ( empty( $existing_columns ) && ! is_array( $existing_columns ) ) {
			$existing_columns = array();
		}

        unset( $existing_columns['date'] );

		$existing_columns['count_products']         = __( 'Products', 'publishing_houses' );
        $existing_columns['date']         = __( 'Date', 'woocommerce' );

        return $existing_columns;

	}

	/**
	 * Ouput custom columns for publishing houses.
	 *
	 * @param string $column
	 */
	public function render_publishing_houses_columns( $column ) {
		global $post;

        if ( $column == 'count_products' ) {

            $products_objects_array = new WP_Query( array(
                'post_type' => 'product',
                'meta_query' => array(
                    array(
                        'key' => '_publishing_houses',
                        'value' => $post->ID
                    )
                )
            ) );


            echo $products_objects_array->found_posts;
        }
	}

    public function woo_check() {
        if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
            add_action('admin_notices', function() {
                ?>
                <div class="error"> <p>Error! Please, activate Woocommerce plugin for work with Publishing houses plugin!</p></div>
                <?php
            } );
        }
    }

}

new PH_Admin();