<?php
/**
 * The template for displaying category pages (FT Work & Careers inspired)
 *
 * @package LWAI
 */

get_header();
?>

	<main id="primary" class="site-main py-4">

		<?php lwai_breadcrumb(); ?>

		<!-- Category Header -->
		<header class="page-header mb-5 border-top border-bottom border-dark border-2 py-3 text-center">
			<?php
			$category = get_queried_object();
			?>
			<h1 class="page-title display-5 fw-bold m-0" style="font-family: var(--ff-heading); letter-spacing: 0.5px;">
				<?php echo esc_html( single_cat_title( '', false ) ); ?>
			</h1>
			<?php
			$category_description = category_description();
			if ( ! empty( $category_description ) ) {
				echo '<div class="archive-description text-secondary mt-3 mx-auto" style="max-width: 600px; font-family: var(--ff-body); font-size: 1.1rem;">' . wp_kses_post( $category_description ) . '</div>';
			}
			?>
		</header><!-- .page-header -->

		<?php if ( have_posts() ) : ?>

			<div class="row">
				<!-- Main Content Column -->
				<div class="col-lg-9 pe-lg-4 border-end-lg">
					
					<?php
					$post_count = 0;
					while ( have_posts() ) :
						the_post();
						$post_count++;
						
						// The very first post gets a larger 'lead' treatment
						if ( 1 === $post_count && ! is_paged() ) :
						?>
							<article class="mb-5 pb-5 border-bottom border-secondary-subtle">
								<div class="row align-items-center">
									<div class="col-md-6 order-2 order-md-1 mt-4 mt-md-0">
										<h2 class="display-5 fw-bold mb-3" style="font-family: var(--ff-heading); line-height: 1.15;"><a href="<?php echo esc_url( get_permalink() ); ?>" class="text-dark text-decoration-none hover-primary"><?php the_title(); ?></a></h2>
										<div class="fs-5 text-secondary mb-3" style="font-family: var(--ff-body); line-height: 1.5;"><?php echo wp_trim_words( get_the_excerpt(), 30, '...' ); ?></div>
										<span class="small fw-bold text-uppercase text-muted" style="font-family: var(--ff-ui); font-size: 11px;">By <?php the_author(); ?></span>
									</div>
									<?php if ( has_post_thumbnail() ) : ?>
										<div class="col-md-6 order-1 order-md-2">
											<a href="<?php echo esc_url( get_permalink() ); ?>" class="d-block">
												<?php the_post_thumbnail( 'large', array( 'class' => 'img-fluid w-100 object-fit-cover', 'loading' => 'eager', 'alt' => the_title_attribute( array( 'echo' => false ) ) ) ); ?>
											</a>
										</div>
									<?php endif; ?>
								</div>
							</article>
							
							<!-- Grid for remaining posts -->
							<div class="row row-cols-1 row-cols-md-2 g-4 mb-4">
						<?php else : ?>
							
							<div class="col">
								<article class="h-100 d-flex flex-column pb-4 mb-2 <?php echo ($post_count > 1) ? 'border-bottom border-secondary-subtle' : ''; ?>">
									<?php if ( has_post_thumbnail() ) : ?>
										<a href="<?php echo esc_url( get_permalink() ); ?>" class="d-block mb-3">
											<?php the_post_thumbnail( 'medium', array( 'class' => 'img-fluid w-100 object-fit-cover', 'style' => 'height: 200px;', 'loading' => 'lazy' ) ); ?>
										</a>
									<?php endif; ?>
									<h3 class="h4 fw-bold mb-2 flex-grow-0" style="font-family: var(--ff-heading); line-height: 1.25;"><a href="<?php echo esc_url( get_permalink() ); ?>" class="text-dark text-decoration-none hover-primary"><?php the_title(); ?></a></h3>
									<div class="text-secondary mb-3 flex-grow-1" style="font-family: var(--ff-body);"><?php echo wp_trim_words( get_the_excerpt(), 18, '...' ); ?></div>
									<span class="small fw-bold text-uppercase text-muted" style="font-family: var(--ff-ui); font-size: 11px;">By <?php the_author(); ?></span>
								</article>
							</div>

						<?php endif; ?>
					<?php endwhile; ?>
					
					<?php if ( $post_count > 1 || is_paged() ) : ?>
						</div><!-- .row (grid) -->
					<?php endif; ?>

					<!-- Pagination -->
					<div class="mt-4 border-top border-dark border-2 pt-3">
						<?php
						the_posts_pagination(
							array(
								'mid_size'  => 2,
								'prev_text' => '&larr; Previous',
								'next_text' => 'Next &rarr;',
								'class'     => 'pagination justify-content-center',
							)
						);
						?>
					</div>

				</div><!-- .col-lg-9 -->

				<!-- Right Sidebar Column -->
				<div class="col-lg-3 ps-lg-4 pt-5 pt-lg-0">
					<h4 class="small fw-bold text-uppercase border-bottom border-dark border-2 pb-2 mb-4" style="font-family: var(--ff-ui); letter-spacing: 0.5px;">Most Popular</h4>
					<?php
					// Display popular/random posts in the sidebar
					$sidebar_query = new WP_Query( array(
						'posts_per_page'      => 5,
						'orderby'             => 'comment_count',
						'ignore_sticky_posts' => true,
					) );
					
					// Fallback to random if no comments
					if ( ! $sidebar_query->have_posts() || $sidebar_query->post_count < 3 ) {
						$sidebar_query = new WP_Query( array(
							'posts_per_page'      => 5,
							'orderby'             => 'rand',
							'ignore_sticky_posts' => true,
						) );
					}

					if ( $sidebar_query->have_posts() ) :
						$sc = 0;
						while ( $sidebar_query->have_posts() ) : $sidebar_query->the_post(); ?>
						<article class="mb-4 <?php echo ($sc < 4) ? 'pb-3 border-bottom border-secondary-subtle' : ''; ?>">
							<?php
							$scats = get_the_category();
							if ( $scats ) {
								echo '<a href="' . esc_url( get_category_link( $scats[0]->term_id ) ) . '" class="text-danger small fw-bold text-uppercase text-decoration-none mb-1 d-block" style="font-family: var(--ff-ui); font-size: 10px;">' . esc_html( $scats[0]->name ) . '</a>';
							}
							?>
							<h3 class="h6 fw-bold mb-1" style="font-family: var(--ff-heading); line-height: 1.3;"><a href="<?php echo esc_url( get_permalink() ); ?>" class="text-dark text-decoration-none hover-primary"><?php the_title(); ?></a></h3>
							<span class="text-muted" style="font-family: var(--ff-ui); font-size: 11px;"><?php echo get_the_date(); ?></span>
						</article>
						<?php 
						$sc++;
						endwhile;
						wp_reset_postdata();
					endif; ?>
				</div><!-- .col-lg-3 -->

			</div><!-- .row -->

		<?php else : ?>

			<div class="row">
				<div class="col-12 text-center py-5">
					<h2 style="font-family: var(--ff-heading);"><?php esc_html_e( 'Nothing Found', 'lwai' ); ?></h2>
					<p style="font-family: var(--ff-body);"><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'lwai' ); ?></p>
					<?php get_search_form(); ?>
				</div>
			</div>

		<?php endif; ?>

	</main><!-- #primary -->

<?php
get_footer();
