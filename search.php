<?php
/**
 * The template for displaying search results
 *
 * @package LWAI
 */

get_header();
?>

	<main id="primary" class="site-main py-4">

		<header class="page-header mb-5 border-top border-bottom border-dark border-2 py-3">
			<h1 class="page-title display-6 fw-bold m-0" style="font-family: var(--ff-heading);">
				<?php
				/* translators: %s: search query */
				printf( esc_html__( 'Search results for: %s', 'lwai' ), '<span class="search-query">' . esc_html( get_search_query() ) . '</span>' );
				?>
			</h1>
		</header>

		<?php if ( have_posts() ) : ?>

			<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
				<?php while ( have_posts() ) : the_post(); ?>
					<div class="col">
						<article class="h-100 d-flex flex-column pb-4">
							<?php if ( has_post_thumbnail() ) : ?>
								<a href="<?php the_permalink(); ?>" class="d-block mb-3">
									<?php the_post_thumbnail( 'card-thumb', array(
										'class'   => 'img-fluid w-100 object-fit-cover',
										'loading' => 'lazy',
										'alt'     => the_title_attribute( array( 'echo' => false ) ),
									) ); ?>
								</a>
							<?php endif; ?>

							<?php
							$scats = get_the_category();
							if ( $scats ) {
								echo '<a href="' . esc_url( get_category_link( $scats[0]->term_id ) ) . '" class="cat-label">' . esc_html( $scats[0]->name ) . '</a>';
							}
							?>

							<h2 class="h4 fw-bold mb-2" style="font-family: var(--ff-heading); line-height: 1.25;">
								<a href="<?php the_permalink(); ?>" class="text-dark text-decoration-none hover-primary"><?php the_title(); ?></a>
							</h2>

							<div class="text-secondary mb-3 flex-grow-1" style="font-family: var(--ff-body);">
								<?php echo wp_trim_words( get_the_excerpt(), 18, '...' ); ?>
							</div>

							<span class="small text-muted text-uppercase fw-bold" style="font-family: var(--ff-ui); font-size: 11px;">
								By <?php the_author(); ?> &middot; <?php echo esc_html( get_the_date() ); ?>
							</span>
						</article>
					</div>
				<?php endwhile; ?>
			</div>

			<div class="mt-4 border-top border-dark border-2 pt-3">
				<?php the_posts_pagination( array(
					'mid_size'  => 2,
					'prev_text' => '&larr; Previous',
					'next_text' => 'Next &rarr;',
				) ); ?>
			</div>

		<?php else : ?>

			<div class="text-center py-5">
				<p class="fs-5 text-secondary mb-4" style="font-family: var(--ff-body);">
					<?php esc_html_e( 'No results found. Try a different search term.', 'lwai' ); ?>
				</p>
				<?php get_search_form(); ?>
			</div>

		<?php endif; ?>

	</main><!-- #primary -->

<?php
get_footer();
