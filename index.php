<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 *
 * @package LWAI
 */

get_header();
?>

	<main id="primary" class="site-main">

		<?php if ( have_posts() ) : ?>

			<div class="news-grid">
				
				<?php
				$post_count = 0;
				
				// Group posts into columns for NYT feel
				// NYT often has a 2-1 layout or 1-2-1 layout on large screens. 
				// We'll use CSS grid for this, but structure the HTML properly.

				while ( have_posts() ) :
					the_post();
					$post_count++;
					
					// First post gets 'story-lead' class for larger styling
					$is_lead = ( 1 === $post_count ) ? 'story-lead' : '';
					?>

					<article id="post-<?php the_ID(); ?>" <?php post_class( 'story ' . $is_lead ); ?>>
						
						<header class="entry-header">
							<?php
							if ( is_singular() ) :
								the_title( '<h1 class="entry-title">', '</h1>' );
							else :
								the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
							endif;
							?>

							<?php if ( 'post' === get_post_type() ) : ?>
								<div class="entry-meta">
									<span class="byline">By <?php the_author_posts_link(); ?></span>
								</div><!-- .entry-meta -->
							<?php endif; ?>
						</header><!-- .entry-header -->

						<?php lwai_post_thumbnail(); ?>

						<div class="entry-summary">
							<?php the_excerpt(); ?>
						</div><!-- .entry-summary -->

					</article><!-- #post-<?php the_ID(); ?> -->

				<?php endwhile; ?>
				
			</div><!-- .news-grid -->

			<?php
			the_posts_navigation(
				array(
					'prev_text' => esc_html__( 'Older Posts', 'lwai' ),
					'next_text' => esc_html__( 'Newer Posts', 'lwai' ),
				)
			);

		else :

			echo '<p>' . esc_html__( 'No news stories found.', 'lwai' ) . '</p>';

		endif;
		?>

	</main><!-- #primary -->

<?php
get_footer();
