<?php
/**
 * The front page template file
 *
 * @package LWAI
 */

get_header();
?>

	<main id="primary" class="site-main">

		<!-- ═══════════════════════════════════════════════════════════════
		     TOP HERO GRID  (WSJ Inspired Layout)
		     ═══════════════════════════════════════════════════════════════ -->
		<div class="row pt-3 pb-4 mb-4">

			<!-- LEFT/CENTER WRAPPER (Lead + Secondary) -->
			<div class="col-lg-9 pe-lg-4 border-end-lg">
				<div class="row">
					
					<!-- MAIN LEAD STORY (col-md-8) -->
					<div class="col-md-8 pe-md-4 border-end-md">
						<?php
						$center_query = new WP_Query( array(
							'category_name'       => 'breaking',
							'posts_per_page'      => 1,
							'ignore_sticky_posts' => true,
						) );
						// Fallback to latest post
						if ( ! $center_query->have_posts() ) {
							$center_query = new WP_Query( array(
								'posts_per_page'      => 1,
								'ignore_sticky_posts' => true,
							) );
						}
						if ( $center_query->have_posts() ) :
							while ( $center_query->have_posts() ) : $center_query->the_post();
								?>
								<article class="mb-4">
									<?php
									$mcats = get_the_category();
									if ( $mcats ) {
										echo '<a href="' . esc_url( get_category_link( $mcats[0]->term_id ) ) . '" class="text-danger small fw-bold text-uppercase text-decoration-none mb-2 d-block" style="font-family: var(--ff-ui); letter-spacing: 1px;">' . esc_html( $mcats[0]->name ) . '</a>';
									}
									?>
									<h2 class="display-4 fw-bold mb-3" style="font-family: 'Playfair Display', Georgia, serif; line-height: 1.1;"><a href="<?php echo esc_url( get_permalink() ); ?>" class="text-dark text-decoration-none hover-primary"><?php the_title(); ?></a></h2>
									<div class="fs-5 text-secondary mb-3" style="font-family: var(--ff-body); line-height: 1.5;"><?php echo wp_trim_words( get_the_excerpt(), 25, '...' ); ?></div>
									<span class="small text-muted text-uppercase fw-bold mb-3 d-block" style="font-family: var(--ff-ui); font-size: 11px;">By <?php the_author(); ?></span>
									<?php if ( has_post_thumbnail() ) : ?>
										<a href="<?php echo esc_url( get_permalink() ); ?>" class="d-block mt-3">
											<?php the_post_thumbnail( 'hero', array( 'class' => 'img-fluid w-100 object-fit-cover', 'style' => 'max-height: 500px;', 'loading' => 'eager' ) ); ?>
										</a>
									<?php endif; ?>
								</article>
								<?php
							endwhile;
							wp_reset_postdata();
						endif; ?>
					</div>

					<!-- SECONDARY STORIES (col-md-4) -->
					<div class="col-md-4 ps-md-4 pt-4 pt-md-0">
						<?php
						$sec_query = new WP_Query( array(
							'posts_per_page'      => 3,
							'offset'              => 1,
							'ignore_sticky_posts' => true,
						) );
						if ( $sec_query->have_posts() ) :
							$sec_count = 0;
							while ( $sec_query->have_posts() ) : $sec_query->the_post();
								?>
								<article class="<?php echo ($sec_count < 2) ? 'border-bottom border-secondary-subtle pb-3 mb-3' : ''; ?>">
									<?php
									$scats = get_the_category();
									if ( $scats ) {
										echo '<a href="' . esc_url( get_category_link( $scats[0]->term_id ) ) . '" class="text-danger small fw-bold text-uppercase text-decoration-none mb-1 d-block" style="font-family: var(--ff-ui); font-size: 10px;">' . esc_html( $scats[0]->name ) . '</a>';
									}
									?>
									<h3 class="h5 fw-bold mb-2" style="font-family: 'Playfair Display', Georgia, serif; line-height: 1.25;"><a href="<?php echo esc_url( get_permalink() ); ?>" class="text-dark text-decoration-none hover-primary"><?php the_title(); ?></a></h3>
									<div class="small text-muted lh-sm mb-2" style="font-family: var(--ff-body);"><?php echo wp_trim_words( get_the_excerpt(), 15, '...' ); ?></div>
									<span class="small text-muted" style="font-family: var(--ff-ui); font-size: 11px;">By <?php the_author(); ?></span>
								</article>
								<?php
								$sec_count++;
							endwhile;
							wp_reset_postdata();
						endif;
						?>
					</div>

				</div>
			</div><!-- .col-lg-9 -->

			<!-- RIGHT COLUMN: Trending / What's News -->
			<div class="col-lg-3 ps-lg-4 pt-4 pt-lg-0">
				<h4 class="small fw-bold border-bottom border-2 border-dark pb-2 mb-3" style="font-family: var(--ff-ui); letter-spacing: 0.5px; text-transform: uppercase;">Trending</h4>
				<?php
				$right_query = new WP_Query( array(
					'category_name'       => 'ai-news',
					'posts_per_page'      => 5,
					'ignore_sticky_posts' => true,
				) );
				if ( ! $right_query->have_posts() ) {
					$right_query = new WP_Query( array(
						'posts_per_page'      => 5,
						'orderby'             => 'date',
						'order'               => 'DESC',
						'ignore_sticky_posts' => true,
					) );
				}
				if ( $right_query->have_posts() ) :
					$right_count = 0;
					while ( $right_query->have_posts() ) : $right_query->the_post(); ?>
					<article class="d-flex gap-3 align-items-start <?php echo ($right_count < 4) ? 'border-bottom border-secondary-subtle pb-3 mb-3' : ''; ?>">
						<div class="flex-grow-1">
							<h3 class="h6 fw-bold mb-1" style="font-family: var(--ff-heading); line-height: 1.3;"><a href="<?php echo esc_url( get_permalink() ); ?>" class="text-dark text-decoration-none hover-primary"><?php the_title(); ?></a></h3>
							<span class="text-muted" style="font-family: var(--ff-ui); font-size: 11px;"><?php echo get_the_date( 'g:i A' ); ?></span>
						</div>
						<?php if ( has_post_thumbnail() ) : ?>
							<div class="flex-shrink-0" style="width: 70px;">
								<a href="<?php echo esc_url( get_permalink() ); ?>" class="d-block">
									<?php the_post_thumbnail( 'compact-thumb', array( 'class' => 'img-fluid object-fit-cover', 'style' => 'height: 70px; width: 70px; border-radius: 2px;', 'loading' => 'lazy' ) ); ?>
								</a>
							</div>
						<?php endif; ?>
					</article>
					<?php 
					$right_count++;
					endwhile;
					wp_reset_postdata();
				endif; ?>
			</div><!-- .col-lg-3 -->

		</div><!-- .row -->

		<!-- ═══════════════════════════════════════════════════════════════
		     CATEGORY SECTIONS (NYT Modular Style)
		     ═══════════════════════════════════════════════════════════════ -->
		<?php
		$sections = array(
			'AI & Technology'     => 'ai-technology',
			'Events'              => 'events',
			'Sectors & Use Cases' => 'sectors-use-cases',
			'AI Business'         => 'ai-business',
			'AI Models'           => 'ai-models',
		);

		foreach ( $sections as $section_title => $section_slug ) :
			$section_posts = get_posts( array(
				'category_name'       => $section_slug,
				'posts_per_page'      => 7,
				'ignore_sticky_posts' => true,
			) );

			// Pad with random posts if fewer than 7
			$count = count( $section_posts );
			if ( $count < 7 ) {
				$exclude_ids = wp_list_pluck( $section_posts, 'ID' );
				$padding     = get_posts( array(
					'posts_per_page'      => 7 - $count,
					'orderby'             => 'rand',
					'post__not_in'        => $exclude_ids,
					'ignore_sticky_posts' => true,
				) );
				$section_posts = array_merge( $section_posts, $padding );
			}

			if ( empty( $section_posts ) ) { continue; }
			?>
			<section class="border-top border-dark border-3 pt-3 mb-5">
				<h2 class="h6 fw-bold text-uppercase border-bottom pb-2 mb-4" style="font-family: var(--ff-ui); letter-spacing: 1px;"><?php echo esc_html( $section_title ); ?></h2>

				<div class="row">

					<!-- Left col: Lead + split pair + sub-feature -->
					<div class="col-lg-8 pe-lg-4 border-end-lg">

						<?php /* Post 0 — Horizontal Feature */
						if ( isset( $section_posts[0] ) ) {
							global $post; $post = $section_posts[0]; setup_postdata( $post );
							$pcats = get_the_category();
							?>
							<article class="row mb-4 align-items-start">
								<div class="col-md-7 order-2 order-md-1 mt-3 mt-md-0">
									<?php if ( $pcats ) { echo '<a href="' . esc_url( get_category_link( $pcats[0]->term_id ) ) . '" class="text-danger fw-bold small text-uppercase text-decoration-none mb-2 d-block" style="font-family: var(--ff-ui); font-size: 11px;">' . esc_html( $pcats[0]->name ) . '</a>'; } ?>
									<h3 class="display-6 fw-bold mb-3" style="font-family: var(--ff-heading); line-height: 1.1;"><a href="<?php echo esc_url( get_permalink() ); ?>" class="text-dark text-decoration-none hover-primary"><?php the_title(); ?></a></h3>
									<div class="fs-5 text-secondary mb-3" style="font-family: var(--ff-body); line-height: 1.5;"><?php echo wp_trim_words( get_the_excerpt(), 30, '...' ); ?></div>
									<span class="small text-muted" style="font-family: var(--ff-ui); font-size: 11px;">By <?php the_author(); ?></span>
								</div>
								<?php if ( has_post_thumbnail() ) : ?>
									<div class="col-md-5 order-1 order-md-2">
										<a href="<?php echo esc_url( get_permalink() ); ?>" class="d-block">
											<?php the_post_thumbnail( 'card-thumb', array( 'class' => 'img-fluid w-100 object-fit-cover', 'loading' => 'lazy' ) ); ?>
										</a>
									</div>
								<?php endif; ?>
							</article>
						<?php } ?>

						<?php /* Posts 1 & 2 — Side-by-side split */
						if ( isset( $section_posts[1] ) || isset( $section_posts[2] ) ) { ?>
							<hr class="text-secondary opacity-25 my-4">
							<div class="row">
								<?php for ( $i = 1; $i <= 2; $i++ ) :
									if ( isset( $section_posts[$i] ) ) :
										global $post; $post = $section_posts[$i]; setup_postdata( $post );
										$pcats = get_the_category();
										$kicker = ( $i === 1 ) ? 'Analysis' : 'News';
										?>
										<div class="col-md-6 <?php echo ($i === 1) ? 'border-end-md pe-md-4 pb-4 pb-md-0 border-bottom border-md-bottom-0' : 'ps-md-4 pt-4 pt-md-0'; ?>">
											<article>
												<span class="text-danger fw-bold small text-uppercase mb-2 d-block" style="font-family: var(--ff-ui); font-size: 11px;"><?php echo esc_html( $kicker ); ?></span>
												<h3 class="h4 fw-bold mb-2" style="font-family: var(--ff-heading); line-height: 1.2;"><a href="<?php echo esc_url( get_permalink() ); ?>" class="text-dark text-decoration-none hover-primary"><?php the_title(); ?></a></h3>
												<div class="text-secondary mb-2 lh-sm" style="font-family: var(--ff-body);"><?php echo wp_trim_words( get_the_excerpt(), 18, '...' ); ?></div>
												<span class="small text-muted" style="font-family: var(--ff-ui); font-size: 11px;">By <?php the_author(); ?></span>
											</article>
										</div>
									<?php endif;
								endfor; ?>
							</div>
						<?php } ?>

						<?php /* Post 3 — Horizontal sub-feature */
						if ( isset( $section_posts[3] ) ) {
							global $post; $post = $section_posts[3]; setup_postdata( $post ); ?>
							<hr class="text-secondary opacity-25 my-4">
							<article class="row align-items-center">
								<div class="col-sm-8 order-2 order-sm-1 mt-3 mt-sm-0">
									<h3 class="h4 fw-bold mb-2" style="font-family: var(--ff-heading); line-height: 1.2;"><a href="<?php echo esc_url( get_permalink() ); ?>" class="text-dark text-decoration-none hover-primary"><?php the_title(); ?></a></h3>
									<div class="text-secondary mb-2 lh-sm" style="font-family: var(--ff-body);"><?php echo wp_trim_words( get_the_excerpt(), 22, '...' ); ?></div>
									<span class="small text-muted" style="font-family: var(--ff-ui); font-size: 11px;">By <?php the_author(); ?></span>
								</div>
								<?php if ( has_post_thumbnail() ) : ?>
									<div class="col-sm-4 order-1 order-sm-2">
										<a href="<?php echo esc_url( get_permalink() ); ?>" class="d-block">
											<?php the_post_thumbnail( 'card-thumb', array( 'class' => 'img-fluid w-100 object-fit-cover', 'loading' => 'lazy' ) ); ?>
										</a>
									</div>
								<?php endif; ?>
							</article>
						<?php } ?>

					</div><!-- .col-lg-8 -->

					<!-- Right col: Stacked feature + compact pair -->
					<div class="col-lg-4 ps-lg-4 mt-5 mt-lg-0 border-top border-lg-0 pt-4 pt-lg-0">

						<?php /* Post 4 — Stacked story */
						if ( isset( $section_posts[4] ) ) {
							global $post; $post = $section_posts[4]; setup_postdata( $post ); ?>
							<article class="mb-4 pb-4 border-bottom border-secondary-subtle">
								<?php if ( has_post_thumbnail() ) : ?>
									<a href="<?php echo esc_url( get_permalink() ); ?>" class="d-block mb-3">
										<?php the_post_thumbnail( 'card-thumb', array( 'class' => 'img-fluid w-100 object-fit-cover', 'loading' => 'lazy' ) ); ?>
									</a>
								<?php endif; ?>
								<h3 class="h3 fw-bold mb-2" style="font-family: var(--ff-heading); line-height: 1.15;"><a href="<?php echo esc_url( get_permalink() ); ?>" class="text-dark text-decoration-none hover-primary"><?php the_title(); ?></a></h3>
								<div class="text-secondary mb-2 lh-sm" style="font-family: var(--ff-body);"><?php echo wp_trim_words( get_the_excerpt(), 15, '...' ); ?></div>
								<span class="small text-muted" style="font-family: var(--ff-ui); font-size: 11px;">By <?php the_author(); ?></span>
							</article>
						<?php } ?>

						<?php /* Posts 5 & 6 — Compact pairs */
						if ( isset( $section_posts[5] ) || isset( $section_posts[6] ) ) { ?>
							<div class="row">
								<?php for ( $i = 5; $i <= 6; $i++ ) :
									if ( isset( $section_posts[$i] ) ) :
										global $post; $post = $section_posts[$i]; setup_postdata( $post ); ?>
										<div class="col-12 <?php echo ($i === 5 && isset($section_posts[6])) ? 'border-bottom border-secondary-subtle pb-3 mb-3' : ''; ?>">
											<article class="d-flex gap-3 align-items-center">
												<div class="flex-grow-1">
													<h4 class="h6 fw-bold mb-1" style="font-family: var(--ff-heading); line-height: 1.2;"><a href="<?php echo esc_url( get_permalink() ); ?>" class="text-dark text-decoration-none hover-primary"><?php the_title(); ?></a></h4>
													<span class="text-muted" style="font-family: var(--ff-ui); font-size: 11px;">By <?php the_author(); ?></span>
												</div>
												<?php if ( has_post_thumbnail() ) : ?>
													<div class="flex-shrink-0" style="width: 100px;">
														<a href="<?php echo esc_url( get_permalink() ); ?>" class="d-block">
															<?php the_post_thumbnail( 'compact-thumb', array( 'class' => 'img-fluid object-fit-cover', 'style' => 'height: 70px; border-radius: 2px;', 'loading' => 'lazy' ) ); ?>
														</a>
													</div>
												<?php endif; ?>
											</article>
										</div>
									<?php endif;
								endfor; ?>
							</div>
						<?php } ?>

					</div><!-- .col-lg-4 -->

				</div><!-- .row -->
			</section>
			<?php
			wp_reset_postdata();
		endforeach;
		?>

	</main><!-- #primary -->

<?php
get_footer();
