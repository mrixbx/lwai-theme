<?php
/**
 * The template for displaying all single posts
 *
 * @package LWAI
 */

get_header();
?>

	<!-- Reading progress bar -->
	<div id="reading-progress" aria-hidden="true"></div>

	<main id="primary" class="site-main">

		<?php
		while ( have_posts() ) :
			the_post();
			?>

				<article id="post-<?php the_ID(); ?>" <?php post_class( 'nyt-article' ); ?>>

				<!-- ── ARTICLE HERO HEADER ── -->
				<div class="article-hero-header">
					<div class="article-hero-text narrow-constraint">

						<!-- Category kicker -->
						<?php
						$cats = get_the_category();
						if ( $cats ) :
							?>
							<a href="<?php echo esc_url( get_category_link( $cats[0]->term_id ) ); ?>" class="cat-label">
								<?php echo esc_html( $cats[0]->name ); ?>
							</a>
						<?php endif; ?>

						<!-- Headline -->
						<?php the_title( '<h1 class="article-title">', '</h1>' ); ?>

						<!-- Deck / standfirst -->
						<?php if ( has_excerpt() ) : ?>
							<p class="article-dek"><?php echo esc_html( get_the_excerpt() ); ?></p>
						<?php endif; ?>

						<!-- Share tools -->
						<div class="article-share-tools">
							<button class="share-btn" onclick="if(navigator.share){navigator.share({title:document.title,url:location.href})}" >
								<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true">
									<path d="M18 16c-.79 0-1.5.31-2.03.81L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92 1.61 0 2.92-1.31 2.92-2.92s-1.31-2.92-2.92-2.92z"/>
								</svg>
								Share
							</button>
						</div>

					</div><!-- .article-hero-text -->

					<!-- Hero image (full-width below text) -->
					<?php if ( has_post_thumbnail() ) : ?>
						<figure class="article-hero-image">
							<?php the_post_thumbnail( 'hero', array(
								'class'   => 'hero-img img-fluid mx-auto d-block',
								'loading' => 'eager',
								'alt'     => the_title_attribute( array( 'echo' => false ) ),
							) ); ?>
							<?php
							$caption = wp_get_attachment_caption( get_post_thumbnail_id() );
							if ( $caption ) :
								echo '<figcaption class="media-caption">' . esc_html( $caption ) . '</figcaption>';
							endif;
							?>
						</figure>
					<?php endif; ?>

				</div><!-- .article-hero-header -->

				<!-- ── ARTICLE BODY ── -->
				<div class="site-container single-post-container">

					<!-- Byline + date -->
					<div class="article-meta-block narrow-constraint">
						<div class="article-byline">
							<span class="byline-name">By <?php the_author_posts_link(); ?></span>
						</div>
						<time class="article-date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
							Published <?php echo esc_html( get_the_date( 'M. j, Y' ) ); ?>
						</time>
						<?php if ( get_the_modified_date( 'c' ) !== get_the_date( 'c' ) ) : ?>
						<time class="article-modified-date" datetime="<?php echo esc_attr( get_the_modified_date( 'c' ) ); ?>">
							Updated <?php echo esc_html( get_the_modified_date( 'M. j, Y' ) ); ?>
						</time>
						<?php endif; ?>
					</div>

					<!-- Article content -->
					<div class="article-content narrow-constraint">
						<?php
						the_content();
						wp_link_pages( array(
							'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'lwai' ),
							'after'  => '</div>',
						) );
						?>
					</div><!-- .article-content -->

					<!-- Author Box -->
					<div class="post-author-box narrow-constraint border-top border-bottom py-4 my-5" style="border-color: var(--cl-gray-mid) !important;">
						<div class="d-flex align-items-center mb-3">
							<div class="author-avatar me-3">
								<?php echo get_avatar( get_the_author_meta( 'ID' ), 60, '', '', array( 'class' => 'rounded-circle' ) ); ?>
							</div>
							<h3 class="author-name mb-0 fs-5 fw-bold" style="font-family: var(--ff-ui);">
								By <?php the_author_posts_link(); ?>
							</h3>
						</div>
						<?php if ( get_the_author_meta( 'description' ) ) : ?>
							<div class="author-info">
								<p class="author-bio mb-0 text-secondary" style="font-size: 0.95rem; line-height: 1.6;">
									<?php echo wp_kses_post( get_the_author_meta( 'description' ) ); ?>
								</p>
							</div>
						<?php endif; ?>
					</div><!-- .post-author-box -->

				</div><!-- .site-container -->

			</article><!-- #post -->

			<!-- ── POST NAVIGATION ── -->
			<div class="site-container single-post-container">
				<?php
				$prev_post = get_previous_post();
				$next_post = get_next_post();
				if ( $prev_post || $next_post ) :
					?>
					<div class="post-navigation narrow-constraint">
						<div class="nav-links">
							<?php if ( $prev_post ) : ?>
								<a href="<?php echo esc_url( get_permalink( $prev_post->ID ) ); ?>" class="nav-previous">
									<span class="nav-label">&laquo; Previous Article</span>
									<div class="nav-content">
										<?php echo get_the_post_thumbnail( $prev_post->ID, 'thumbnail', array( 'loading' => 'lazy', 'class' => 'img-fluid' ) ); ?>
										<span class="nav-title"><?php echo esc_html( get_the_title( $prev_post->ID ) ); ?></span>
									</div>
								</a>
							<?php else : ?>
								<div class="nav-previous empty"></div>
							<?php endif; ?>

							<?php if ( $next_post ) : ?>
								<a href="<?php echo esc_url( get_permalink( $next_post->ID ) ); ?>" class="nav-next">
									<span class="nav-label">Next Article &raquo;</span>
									<div class="nav-content">
										<?php echo get_the_post_thumbnail( $next_post->ID, 'thumbnail', array( 'loading' => 'lazy', 'class' => 'img-fluid' ) ); ?>
										<span class="nav-title"><?php echo esc_html( get_the_title( $next_post->ID ) ); ?></span>
									</div>
								</a>
							<?php else : ?>
								<div class="nav-next empty"></div>
							<?php endif; ?>
						</div>
					</div><!-- .post-navigation -->
				<?php endif; ?>
			</div>

			<!-- ── RELATED POSTS ── -->
			<?php
			$post_cats   = wp_get_post_categories( get_the_ID() );
			$primary_cat = $cats ? $cats[0]->name : '';
			if ( ! empty( $post_cats ) ) :
				$related = new WP_Query( array(
					'category__in'   => $post_cats,
					'posts_per_page' => 3,
					'post__not_in'   => array( get_the_ID() ),
					'orderby'        => 'date',
					'order'          => 'DESC',
					'no_found_rows'  => true,
				) );

				if ( $related->have_posts() ) :
					?>
					<section class="related-posts site-container single-post-container">
						<div class="narrow-constraint">
							<h3 class="section-title">
								More<?php if ( $primary_cat ) : ?> in <?php echo esc_html( $primary_cat ); ?><?php endif; ?>
							</h3>
							<div class="row row-cols-1 row-cols-md-3 g-3">
								<?php while ( $related->have_posts() ) : $related->the_post(); ?>
									<div class="col">
										<article class="related-post-card">
											<?php if ( has_post_thumbnail() ) : ?>
												<a href="<?php the_permalink(); ?>" class="d-block">
													<?php the_post_thumbnail( 'compact-thumb', array( 'loading' => 'lazy', 'class' => 'img-fluid w-100' ) ); ?>
												</a>
											<?php endif; ?>

											<?php
											$rcats = get_the_category();
											if ( $rcats ) :
												echo '<a href="' . esc_url( get_category_link( $rcats[0]->term_id ) ) . '" class="cat-label">' . esc_html( $rcats[0]->name ) . '</a>';
											endif;
											?>
											<?php the_title( '<h4 class="entry-title"><a href="' . esc_url( get_permalink() ) . '">', '</a></h4>' ); ?>
										</article>
									</div>
								<?php endwhile; wp_reset_postdata(); ?>
							</div>
						</div>
					</section>
					<?php
				endif;
			endif;
			?>

		<?php endwhile; ?>

	</main><!-- #primary -->

<?php
get_footer();
