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
	/* ── Open Graph & Twitter Card ── */
	if ( is_singular() ) :
		$og_title = get_the_title();
		$og_desc  = has_excerpt() ? get_the_excerpt() : wp_trim_words( get_the_content(), 30 );
		$og_desc  = substr( wp_strip_all_tags( $og_desc ), 0, 160 );
		$og_url   = get_permalink();
		$og_image = '';
		if ( has_post_thumbnail() ) {
			$img = wp_get_attachment_image_src( get_post_thumbnail_id(), 'hero' );
			if ( $img ) { $og_image = $img[0]; }
		}
	?>
	<meta property="og:type"        content="article">
	<meta property="og:title"       content="<?php echo esc_attr( $og_title ); ?>">
	<meta property="og:description" content="<?php echo esc_attr( $og_desc ); ?>">
	<meta property="og:url"         content="<?php echo esc_url( $og_url ); ?>">
	<?php if ( $og_image ) : ?>
	<meta property="og:image"       content="<?php echo esc_url( $og_image ); ?>">
	<meta name="twitter:image"      content="<?php echo esc_url( $og_image ); ?>">
	<?php endif; ?>
	<meta name="twitter:card"        content="summary_large_image">
	<meta name="twitter:title"       content="<?php echo esc_attr( $og_title ); ?>">
	<meta name="twitter:description" content="<?php echo esc_attr( $og_desc ); ?>">
	<?php else : ?>
	<meta property="og:type"        content="website">
	<meta property="og:title"       content="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
	<meta property="og:description" content="<?php echo esc_attr( get_bloginfo( 'description' ) ); ?>">
	<meta property="og:url"         content="<?php echo esc_url( home_url( '/' ) ); ?>">
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
				<h1 class="site-title display-3 fw-normal mb-0" style="font-family: var(--ff-branding); line-height: 1;"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="text-decoration-none"><?php bloginfo( 'name' ); ?></a></h1>
				<?php
			endif;
			?>
		</div><!-- .site-branding -->

		<!-- Primary navigation -->
		<div id="sticky-nav-wrapper" class="sticky-top py-2">
			<div class="container d-flex align-items-center justify-content-center position-relative">

				<!-- Mini Logo (hidden initially, shown on scroll) -->
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="sticky-logo d-none position-absolute start-0 text-white text-decoration-none fw-bold" style="font-family: var(--ff-branding); font-size: 1.5rem; letter-spacing: -1px;">
					<?php bloginfo( 'name' ); ?>
				</a>

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
			</div>
		</div>

		<script>
			document.addEventListener("DOMContentLoaded", function() {
				var navWrapper = document.getElementById("sticky-nav-wrapper");
				var stickyLogo = navWrapper ? navWrapper.querySelector(".sticky-logo") : null;

				window.addEventListener("scroll", function() {
					if (!navWrapper) return;
					if (window.scrollY > 150) {
						navWrapper.classList.add("shadow");
						if (stickyLogo) stickyLogo.classList.remove("d-none");
					} else {
						navWrapper.classList.remove("shadow");
						if (stickyLogo) stickyLogo.classList.add("d-none");
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
