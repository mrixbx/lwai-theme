<?php
/**
 * The footer for our theme
 *
 * @package LWAI
 */
?>

</div><!-- #page -->

<footer id="colophon" style="font-family: var(--ff-ui);">

	<!-- (a) Top: centred site name -->
	<div class="footer-divider py-4 text-center">
		<p class="display-6 mb-0" style="font-family: var(--ff-branding);">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="footer-site-name text-decoration-none">
				<?php echo esc_html( get_bloginfo( 'name' ) ); ?>
			</a>
		</p>
	</div>

	<!-- (b) Middle: footer navigation grid -->
	<div class="container py-4 footer-divider">
		<div class="row justify-content-center">
			<div class="col-md-10">
				<?php
				wp_nav_menu( array(
					'theme_location' => 'footer',
					'container'      => false,
					'depth'          => 1,
					'fallback_cb'    => false,
					'menu_class'     => 'footer-nav-menu',
				) );
				?>
			</div>
		</div>
	</div>

	<!-- (c) Bottom bar: copyright + back to top -->
	<div class="container py-3 footer-divider d-flex flex-column flex-md-row justify-content-between align-items-center">
		<span class="small footer-copyright mb-2 mb-md-0">
			<?php
			$footer_text = get_theme_mod(
				'lwai_footer_text',
				'&copy; ' . date_i18n( 'Y' ) . ' ' . get_bloginfo( 'name' ) . '. All rights reserved.'
			);
			echo wp_kses_post( $footer_text );
			?>
		</span>
		<a class="small footer-back-top fw-bold text-uppercase text-decoration-none" style="letter-spacing: 0.05em;" href="#page">Back to top &uarr;</a>
	</div>

	<!-- Floating back-to-top button -->
	<button id="back-to-top" class="rounded-circle shadow-lg position-fixed" style="bottom: 24px; right: 16px; width: 44px; height: 44px; display: none; z-index: 1050; align-items: center; justify-content: center; border: none; cursor: pointer;" aria-label="<?php esc_attr_e( 'Back to top', 'lwai' ); ?>">&#8593;</button>

</footer><!-- #colophon -->

<?php wp_footer(); ?>

</body>
</html>
