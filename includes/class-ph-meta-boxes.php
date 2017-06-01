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

class PH_meta_boxes {

    public static function init() {
        add_action( 'woocommerce_product_options_advanced', array( 'PH_meta_boxes', 'publishing_houses_meta_box_product' ) );
        add_action( 'woocommerce_process_product_meta', array( 'PH_meta_boxes', 'publishing_houses_meta_box_product_add' ) );
    }


    public static function taxonomy_single_meta_box( $post, $box = array() ) {

        if ( ! isset( $box['args'] ) || ! is_array( $box['args'] ) ) {
            post_categories_meta_box( $post, $box );
            return;
        }
        $args = $box['args'];
        $tax_name = esc_attr( $args['taxonomy'] );

        $terms_objects_array_checked
                = wp_get_object_terms( $post->ID, $tax_name, array( 'orderby' => 'name', 'order' => 'ASC' ) );
        $name  = '';
        if ( ! is_wp_error( $terms_objects_array_checked )
                && count( $terms_objects_array_checked ) ) {
            $term_object_checked = array_shift($terms_objects_array_checked);
            $name = $term_object_checked->name;
        }

        $terms_objects_array = get_terms( $tax_name, array( 'hide_empty' => false ) );

        foreach ( $terms_objects_array as $term_object ) :
        ?>

		<label title='<?php esc_attr_e( $term_object->name ); ?>'>
		    <input type="radio" name="<?php echo $tax_name ?>" value="<?php esc_attr_e( $term_object->term_id ); ?>" <?php checked( $term_object->name, $name ); ?>>
			<span><?php esc_html_e( $term_object->name ); ?></span>
        </label><br>

        <?php

        endforeach;
    }

    /**
     * Save meta box results.
     *
     * @param int $post_id The ID of the post that's being saved.
     */
    public static function save_single_meta_box( $post_id ) {

        if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE  ) ) {
            return;
        }

        $checked_term_id = filter_input(INPUT_POST, 'publishing_houses_cities', FILTER_SANITIZE_NUMBER_INT );

        if ( empty( $checked_term_id ) ) {
            return;
        }

        $term = get_term_by( 'id', $checked_term_id, 'publishing_houses_cities' );
        if ( ! empty( $term ) && ! is_wp_error( $term ) ) {
            wp_set_object_terms( $post_id, $term->term_id, 'publishing_houses_cities', false );
        }
    }


    public function publishing_houses_meta_box_product() {

        global $post;
        ?>
        <div class="options_group">
        <?php

        $publishing_house_id = get_post_meta($post->ID, '_publishing_houses', true);

        $field = array(
            'id'      => '_publishing_houses',
            'value'   => $publishing_house_id,
            'label'   => __( 'Publishing houses', 'publishing_houses' ),
        );

        $publishing_houses_posts_array = get_posts(array(
            'post_type' => 'publishing_house',
            'numberposts' => -1
        ));
        $field['options'][] = '';
        foreach ( $publishing_houses_posts_array as $publishing_houses_post )  {
            $field['options'][$publishing_houses_post->ID] = $publishing_houses_post->post_title;
        }
        woocommerce_wp_select( $field );
        ?>
        </div>
        <?php

    }

    public function publishing_houses_meta_box_product_add( $post_id ) {

        // Проверяем авто-сохранение
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )  {
            return;
        }
        // Проверяем права доступа
        if ( 'publishing_house' == $_POST['post_type'] ) {
            if ( ! current_user_can( 'edit_page', $post_id ) )  {
                return $post_id;
            } elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
                return $post_id;
            }
        }
        $meta_name = '_publishing_houses';

        $old = get_post_meta( $post_id, $meta_name, true ); // Получаем старые данные (если они есть), для сверки
        $new = $_POST[$meta_name];
        if ( $new && $new != $old ) {  // Если данные новые
            update_post_meta( $post_id, $meta_name, $new ); // Обновляем данные
        } elseif ( '' == $new && $old ) {
            delete_post_meta( $post_id, $meta_name, $old ); // Если данных нету, удаляем мету.
        }

    }

}

PH_meta_boxes::init();