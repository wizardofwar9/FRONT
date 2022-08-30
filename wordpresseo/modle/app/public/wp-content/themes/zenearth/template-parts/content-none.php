<?php
/**
 * The template part for displaying a message that posts cannot be found
 *
 */
?>

<article>

	<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>

			<h1><?php esc_html_e( 'Oh no! Article not found! 404 error!', 'zenearth' ); ?></h1>
	
	<?php elseif ( is_search() ) : ?>

			<h1><?php esc_html_e( 'No Results Found!', 'zenearth' ); ?></h1>
			<?php get_search_form(); ?>

	<?php else : ?>

			<p><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'zenearth' ); ?></p>
			<?php get_search_form(); ?>

	<?php endif; ?>

</article>