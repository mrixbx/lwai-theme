<?php
/**
 * The header for our theme
 *
 * @package LWAI
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<!-- Google Fonts preconnect for faster font loading -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

	<?php
	/* ── SEO: Description, Canonical, Open Graph, Twitter Card ── */
	$seo_image = '';
	if ( is_singular() ) {
		$seo_title = get_the_title();
		$seo_desc  = has_excerpt() ? get_the_excerpt() : wp_trim_words( get_the_content(), 30 );
		$seo_url   = get_permalink();
		$og_type   = is_page() ? 'website' : 'article';
		if ( has_post_thumbnail() ) {
			$_img = wp_get_attachment_image_src( get_post_thumbnail_id(), 'hero' );
			if ( $_img ) { $seo_image = $_img[0]; }
		}
	} elseif ( is_category() ) {
		$seo_title = single_cat_title( '', false ) . ' | ' . get_bloginfo( 'name' );
		$seo_desc  = category_description() ?: get_bloginfo( 'description' );
		$seo_url   = get_pagenum_link( max( 1, (int) get_query_var( 'paged' ) ) );
		$og_type   = 'website';
	} elseif ( is_author() ) {
		$seo_title = get_the_author_meta( 'display_name', get_queried_object_id() ) . ' | ' . get_bloginfo( 'name' );
		$seo_desc  = get_the_author_meta( 'description', get_queried_object_id() ) ?: get_bloginfo( 'description' );
		$seo_url   = get_author_posts_url( get_queried_object_id() );
		$og_type   = 'website';
	} else {
		$seo_title = get_bloginfo( 'name' );
		$seo_desc  = get_bloginfo( 'description' );
		$seo_url   = is_front_page() ? home_url( '/' ) : get_pagenum_link( max( 1, (int) get_query_var( 'paged' ) ) );
		$og_type   = 'website';
	}
	$seo_desc = substr( wp_strip_all_tags( $seo_desc ), 0, 155 );
	?>
	<?php if ( $seo_desc ) : ?>
	<meta name="description" content="<?php echo esc_attr( $seo_desc ); ?>">
	<?php endif; ?>
	<link rel="canonical" href="<?php echo esc_url( $seo_url ); ?>">

	<!-- Open Graph -->
	<meta property="og:site_name"   content="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
	<meta property="og:type"        content="<?php echo esc_attr( $og_type ); ?>">
	<meta property="og:title"       content="<?php echo esc_attr( $seo_title ); ?>">
	<meta property="og:description" content="<?php echo esc_attr( $seo_desc ); ?>">
	<meta property="og:url"         content="<?php echo esc_url( $seo_url ); ?>">
	<?php if ( $seo_image ) : ?>
	<meta property="og:image"        content="<?php echo esc_url( $seo_image ); ?>">
	<meta property="og:image:width"  content="1200">
	<meta property="og:image:height" content="675">
	<?php endif; ?>

	<!-- Twitter Card -->
	<meta name="twitter:card"        content="summary_large_image">
	<meta name="twitter:title"       content="<?php echo esc_attr( $seo_title ); ?>">
	<meta name="twitter:description" content="<?php echo esc_attr( $seo_desc ); ?>">
	<?php if ( $seo_image ) : ?>
	<meta name="twitter:image"       content="<?php echo esc_url( $seo_image ); ?>">
	<?php endif; ?>

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Mobile Sticky Bar (fixed, visible only on mobile) -->
<div class="mobile-sticky-bar">
	<a class="mobile-site-title" href="<?php echo esc_url( home_url( '/' ) ); ?>">
		<?php echo esc_html( get_bloginfo( 'name' ) ); ?>
	</a>
	<button class="mobile-menu-toggle-sticky" aria-label="<?php esc_attr_e( 'Open Menu', 'lwai' ); ?>" aria-expanded="false">&#9776;</button>
</div>

<div id="page" class="container my-3">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'lwai' ); ?></a>

	<header id="masthead" class="border-top border-dark border-3 pt-3 mb-4">

		<!-- Top bar: date + actions -->
		<div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3" style="font-family: var(--ff-ui); font-size: 0.85rem;">
			<div class="text-secondary fw-semibold">
				<?php echo esc_html( date_i18n( 'l, F j, Y' ) ); ?>
			</div>

			<div class="text-secondary d-none d-md-block">
				<?php
				$desc = get_bloginfo( 'description', 'display' );
				if ( $desc || is_customize_preview() ) :
					echo '<span class="site-description">' . esc_html( $desc ) . '</span>';
				endif;
				?>
			</div>

			<div class="d-flex align-items-center gap-2">
				<!-- Search toggle button -->
				<button class="search-toggle btn btn-link text-dark p-1 rounded-circle" aria-label="<?php esc_attr_e( 'Search', 'lwai' ); ?>">
					<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true">
						<circle cx="11" cy="11" r="6" stroke="currentColor" stroke-width="2" fill="none"/>
						<line x1="16.5" y1="16.5" x2="21" y2="21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
					</svg>
				</button>
			</div>
		</div><!-- .site-header-top -->

		<!-- Site branding -->
		<div class="text-center my-4">
			<?php
			if ( has_custom_logo() ) :
				the_custom_logo();
			else :
				?>
				<?php if ( is_front_page() ) : ?>
				<h1 class="site-title display-3 fw-normal mb-0" style="font-family: var(--ff-branding); line-height: 1;"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="text-decoration-none"><?php bloginfo( 'name' ); ?></a></h1>
				<?php else : ?>
				<p class="site-title display-3 fw-normal mb-0" style="font-family: var(--ff-branding); line-height: 1;"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="text-decoration-none"><?php bloginfo( 'name' ); ?></a></p>
				<?php endif; ?>
				<?php
			endif;
			?>
		</div><!-- .site-branding -->

		<!-- Primary navigation -->
		<div id="sticky-nav-wrapper" class="sticky-top py-2">
			<div class="container d-flex align-items-center justify-content-between">

				<!-- Left: Site name — hidden initially, fades in on scroll -->
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="sticky-logo text-white text-decoration-none fw-bold" style="font-family: var(--ff-branding); font-size: 1.5rem; letter-spacing: -1px;">
					<?php bloginfo( 'name' ); ?>
				</a>

				<!-- Right: Nav links + Search icon -->
				<div class="d-flex align-items-center gap-3">
					<nav id="site-navigation" class="site-navigation align-items-center" aria-label="<?php esc_attr_e( 'Primary Navigation', 'lwai' ); ?>">
						<?php
						wp_nav_menu( array(
							'theme_location' => 'primary',
							'container'      => false,
							'menu_class'     => 'nav-menu d-flex justify-content-center gap-4 flex-wrap list-unstyled m-0 text-uppercase fw-bold',
							'fallback_cb'    => false,
						) );
						?>
					</nav>

					<button class="search-toggle btn btn-link text-white p-1 rounded-circle d-none d-md-flex align-items-center" aria-label="<?php esc_attr_e( 'Search', 'lwai' ); ?>">
						<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true">
							<circle cx="11" cy="11" r="6" stroke="currentColor" stroke-width="2" fill="none"/>
							<line x1="16.5" y1="16.5" x2="21" y2="21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
						</svg>
					</button>
				</div>
			</div>
		</div>

		<script>
			document.addEventListener("DOMContentLoaded", function() {
				var navWrapper = document.getElementById("sticky-nav-wrapper");
				var stickyLogo = navWrapper ? navWrapper.querySelector(".sticky-logo") : null;

				window.addEventListener("scroll", function() {
					if (!navWrapper) return;
					if (window.scrollY > 80) {
						navWrapper.classList.add("shadow");
						if (stickyLogo) stickyLogo.classList.add("is-visible");
					} else {
						navWrapper.classList.remove("shadow");
						if (stickyLogo) stickyLogo.classList.remove("is-visible");
					}
				});
			});
		</script>

	</header><!-- #masthead -->

	<!-- Search Overlay -->
	<div class="search-overlay" role="search">
		<div class="search-overlay-inner">
			<button class="search-close" aria-label="<?php esc_attr_e( 'Close Search', 'lwai' ); ?>">&times;</button>
			<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<input type="search" name="s" placeholder="<?php esc_attr_e( 'Search...', 'lwai' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>">
			</form>
		</div>
	</div><!-- .search-overlay -->
