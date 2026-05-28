<?php
/**
 * The template for displaying category archive pages
 *
 * @package LWAI
 */

get_header();
?>

	<main id="primary" class="site-main py-4">

		<!-- Category Header -->
		<header class="page-header mb-5 border-top border-bottom border-dark border-2 py-3 text-center">
			<h1 class="page-title display-5 fw-bold m-0" style="font-family: var(--ff-heading); letter-spacing: 0.5px;">
				<?php echo esc_html( single_cat_title( '', false ) ); ?>
			</h1>
			<?php
			$category_description = category_description();
			if ( ! empty( $category_description ) ) {
				echo '<div class="archive-description text-secondary mt-3 mx-auto" style="max-width: 600px; font-family: var(--ff-body); font-size: 1.1rem;">' . wp_kses_post( $category_description ) . '</div>';
			}
			?>
		</header>

		<?php if ( have_posts() ) : ?>

			<div class="row">

				<!-- Main Content Column -->
				<div class="col-lg-9 pe-lg-4 border-end-lg">

					<?php
					$post_count  = 0;
					$grid_opened = false;

					while ( have_posts() ) :
						the_post();
						$post_count++;

						// First post on every page: large lead treatment
						if ( 1 === $post_count ) :
							?>
							<article class="mb-5 pb-5 border-bottom border-secondary-subtle">
								<div class="row align-items-center">
									<div class="col-md-6 order-2 order-md-1 mt-4 mt-md-0">
										<?php
										$lcats = get_the_category();
										if ( $lcats ) {
											echo '<a href="' . esc_url( get_category_link( $lcats[0]->term_id ) ) . '" class="cat-label d-inline-block mb-2">' . esc_html( $lcats[0]->name ) . '</a>';
										}
										?>
										<h2 class="display-5 fw-bold mb-3" style="font-family: var(--ff-heading); line-height: 1.15;">
											<a href="<?php echo esc_url( get_permalink() ); ?>" class="text-dark text-decoration-none hover-primary"><?php the_title(); ?></a>
										</h2>
										<div class="fs-5 text-secondary mb-3" style="font-family: var(--ff-body); line-height: 1.5;">
											<?php echo wp_trim_words( get_the_excerpt(), 30, '...' ); ?>
										</div>
										<span class="small fw-bold text-uppercase text-muted" style="font-family: var(--ff-ui); font-size: 11px;">
											By <?php the_author(); ?> &middot; <?php echo esc_html( get_the_date( 'M. j, Y' ) ); ?>
										</span>
									</div>
									<?php if ( has_post_thumbnail() ) : ?>
										<div class="col-md-6 order-1 order-md-2">
											<a href="<?php echo esc_url( get_permalink() ); ?>" class="d-block">
												<?php the_post_thumbnail( 'large', array(
													'class'   => 'img-fluid w-100 object-fit-cover',
													'loading' => 'eager',
													'alt'     => the_title_attribute( array( 'echo' => false ) ),
												) ); ?>
											</a>
										</div>
									<?php endif; ?>
								</div>
							</article>

							<?php
							// Open the grid for all subsequent posts
							echo '<div class="row row-cols-1 row-cols-md-2 g-4 mb-4">';
							$grid_opened = true;

						else : // Posts 2–9: card grid ?>

							<div class="col">
								<article class="h-100 d-flex flex-column pb-4 border-bottom border-secondary-subtle">
									<?php if ( has_post_thumbnail() ) : ?>
										<a href="<?php echo esc_url( get_permalink() ); ?>" class="d-block mb-3">
											<?php the_post_thumbnail( 'card-thumb', array(
												'class'   => 'img-fluid w-100 object-fit-cover',
												'style'   => 'height: 200px;',
												'loading' => 'lazy',
												'alt'     => the_title_attribute( array( 'echo' => false ) ),
											) ); ?>
										</a>
									<?php endif; ?>
									<?php
									$gcats = get_the_category();
									if ( $gcats ) {
										echo '<a href="' . esc_url( get_category_link( $gcats[0]->term_id ) ) . '" class="cat-label d-inline-block mb-2">' . esc_html( $gcats[0]->name ) . '</a>';
									}
									?>
									<h3 class="h4 fw-bold mb-2 flex-grow-0" style="font-family: var(--ff-heading); line-height: 1.25;">
										<a href="<?php echo esc_url( get_permalink() ); ?>" class="text-dark text-decoration-none hover-primary"><?php the_title(); ?></a>
									</h3>
									<div class="text-secondary mb-3 flex-grow-1" style="font-family: var(--ff-body); font-size: 0.95rem;">
										<?php echo wp_trim_words( get_the_excerpt(), 18, '...' ); ?>
									</div>
									<span class="small text-muted text-uppercase fw-bold" style="font-family: var(--ff-ui); font-size: 11px;">
										By <?php the_author(); ?> &middot; <?php echo esc_html( get_the_date( 'M. j, Y' ) ); ?>
									</span>
								</article>
							</div>

						<?php endif; ?>
					<?php endwhile; ?>

					<?php if ( $grid_opened ) : ?>
						</div><!-- .row.grid -->
					<?php endif; ?>

					<!-- Pagination -->
					<div class="mt-5 border-top border-dark border-2 pt-3">
						<?php
						the_posts_pagination( array(
							'mid_size'  => 2,
							'prev_text' => '&larr; Previous',
							'next_text' => 'Next &rarr;',
						) );
						?>
					</div>

				</div><!-- .col-lg-9 -->

				<!-- Sidebar: Most Popular -->
				<div class="col-lg-3 ps-lg-4 pt-5 pt-lg-0">

					<h2 class="h6 fw-bold text-uppercase border-bottom border-dark border-2 pb-2 mb-4" style="font-family: var(--ff-ui); letter-spacing: 0.5px;">Most Popular</h2>

					<?php
					$sidebar_query = new WP_Query( array(
						'posts_per_page'      => 5,
						'orderby'             => 'comment_count',
						'ignore_sticky_posts' => true,
					) );
					if ( ! $sidebar_query->have_posts() || $sidebar_query->post_count < 3 ) {
						$sidebar_query = new WP_Query( array(
							'posts_per_page'      => 5,
							'orderby'             => 'date',
							'order'               => 'DESC',
							'ignore_sticky_posts' => true,
						) );
					}

					if ( $sidebar_query->have_posts() ) :
						$sc = 0;
						while ( $sidebar_query->have_posts() ) : $sidebar_query->the_post();
							$sc++;
							?>
							<div class="d-flex gap-3 align-items-start <?php echo ( $sc < 5 ) ? 'mb-4 pb-4 border-bottom border-secondary-subtle' : 'mb-2'; ?>">

								<!-- Rank number -->
								<span class="fw-bold flex-shrink-0" style="font-size: 1.75rem; line-height: 1; color: var(--ft-border); font-family: var(--ff-heading); min-width: 28px; text-align: center;"><?php echo $sc; ?></span>

								<!-- Text -->
								<div class="flex-grow-1 min-w-0">
									<?php
									$scats = get_the_category();
									if ( $scats ) {
										echo '<a href="' . esc_url( get_category_link( $scats[0]->term_id ) ) . '" class="cat-label d-inline-block mb-1">' . esc_html( $scats[0]->name ) . '</a>';
									}
									?>
									<h3 class="h6 fw-bold mb-1 lh-sm" style="font-family: var(--ff-heading);">
										<a href="<?php echo esc_url( get_permalink() ); ?>" class="text-dark text-decoration-none hover-primary"><?php the_title(); ?></a>
									</h3>
									<span class="text-muted" style="font-family: var(--ff-ui); font-size: 11px;"><?php echo esc_html( get_the_date( 'M. j, Y' ) ); ?></span>
								</div>

								<!-- Thumbnail -->
								<?php if ( has_post_thumbnail() ) : ?>
									<a href="<?php echo esc_url( get_permalink() ); ?>" class="flex-shrink-0 d-block" style="width: 72px;">
										<?php the_post_thumbnail( 'compact-thumb', array(
											'class' => 'img-fluid object-fit-cover rounded-1',
											'style' => 'width: 72px; height: 56px;',
											'alt'   => the_title_attribute( array( 'echo' => false ) ),
										) ); ?>
									</a>
								<?php endif; ?>

							</div>
							<?php
						endwhile;
						wp_reset_postdata();
					endif;
					?>

				</div><!-- .col-lg-3 -->

			</div><!-- .row -->

		<?php else : ?>

			<div class="text-center py-5">
				<h2 style="font-family: var(--ff-heading);"><?php esc_html_e( 'Nothing Found', 'lwai' ); ?></h2>
				<p class="text-secondary" style="font-family: var(--ff-body);"><?php esc_html_e( 'It seems we can\'t find what you\'re looking for. Perhaps searching can help.', 'lwai' ); ?></p>
				<?php get_search_form(); ?>
			</div>

		<?php endif; ?>

	</main><!-- #primary -->

<?php
get_footer();
