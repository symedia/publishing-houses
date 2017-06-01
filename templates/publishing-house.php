<?php

/*
 * Writen by Gregory V Lominoga (Gromodar)
 * E-Mail: lominogagv@gmail.com
 * Produced by Symedia studio
 * http://symedia.ru
 * E-Mail: info@symedia.ru
 */


get_header(); ?>

<div class="wrap">
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php
				/* Start the Loop */
				while ( have_posts() ) : the_post();
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <?php
                        if ( is_sticky() && is_home() ) :
                            echo twentyseventeen_get_svg( array( 'icon' => 'thumb-tack' ) );
                        endif;
                    ?>
                    <header class="entry-header">
                        <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
                    </header><!-- .entry-header -->
                    <ul>
                <?php

                $products = $post->products;

                foreach ( $products as $product ):
                ?>
                    <li><a href="<?php echo get_permalink($product); ?>"><?php echo $product->post_title ?></a>
                <?php
                endforeach;

				endwhile; // End of the loop.
			?>
                    </ul>
		</main><!-- #main -->
	</div><!-- #primary -->
	<?php get_sidebar(); ?>
</div><!-- .wrap -->

<?php get_footer();