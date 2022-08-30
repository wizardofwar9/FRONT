<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the "body-content-wrapper" div and all content after.
 *
 */
?>
			<a href="#" class="scrollup"></a>

			<footer id="ftr-main">

				<div id="footer-content-wrapper">

					<?php get_sidebar( 'footer' ); ?>

					<nav id="footer-menu">
                        <?php wp_nav_menu( array( 'theme_location' => 'footer', ) ); ?>
                    </nav>

					<div class="clear">
					</div>

				</div><!-- #footer-content-wrapper -->
			</footer><!-- #ftr-main -->
			<div id="footer-bottom-area">
				<div id="footer-bottom-content-wrapper">
					<div id="copyright">
						<p>
							<?php
							 		$footerText = get_theme_mod('zenearth_footer_copyright', null);

									if ( !empty( $footerText ) ) :

										echo esc_html( $footerText ) . ' | ';	

									endif;
							?>

						 	<a href="<?php echo esc_url( 'https://zentemplates.com/product/zenearth' ); ?>"
						 		title="<?php esc_attr_e( 'ZenEarth Theme', 'zenearth' ); ?>">
								<?php esc_html_e('ZenEarth Theme', 'zenearth'); ?>
							</a> 
							<?php
								/* translators: %s: WordPress name */
								printf( __( 'Powered by %s', 'zenearth' ), 'WordPress' ); ?>
						</p>
					</div><!-- #copyright -->
				</div>
			</div>

		</div><!-- #body-content-wrapper -->
		<?php wp_footer(); ?>
	</body>
</html>