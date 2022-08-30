<?php
/**
 * Template part for displaying posts with excerpts
 *
 * Used in Search Results and for Recent Posts in Front Page panels.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<h1 class="post-entry-title">
		<a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
	</h1>

	<?php if ( 'post' === get_post_type() ) : ?>

			<div class="post-before-content">

				<?php if ( !is_single() && get_the_title() === '' ) : ?>

					<a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark">
						<div class="postdate">
							<div class="day">
								<?php echo get_the_date( 'd' ); ?>
							</div>
							<div class="month">
								<?php echo get_the_date( 'M' ); ?>
							</div>
							<div class="year">
								<?php echo get_the_date( 'Y' ); ?>
							</div>
						</div>
					</a>
			
				<?php else : ?>

						<div class="postdate">
							<div class="day">
								<?php echo get_the_date( 'd' ); ?>
							</div>
							<div class="month">
								<?php echo get_the_date( 'M' ); ?>
							</div>
							<div class="year">
								<?php echo get_the_date( 'Y' ); ?>
							</div>
						</div>
					
				<?php endif; ?>
		
			</div><!-- .post-before-content -->

	<?php endif; ?>

	<div class="post-content">
		<?php the_excerpt(); ?>
	</div><!-- .content -->

	<div class="post-after-content">

		<?php if ( 'post' === get_post_type() ) : ?>

				<span class="icon author-icon">
					<?php the_author_posts_link(); ?>
				</span><!-- .author-icon -->

		<?php endif; ?>

		<?php if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) : ?>

					<span class="icon comments-icon">
						<?php comments_popup_link(__( 'No Comments', 'zenearth' ), __( '1 Comment', 'zenearth' ), __( '% Comments', 'zenearth' ), '', __( 'Comments are closed.', 'zenearth' )); ?>
					</span><!-- .comments-icon -->
		
		<?php endif; ?>

		<?php if ( ! post_password_required() ) : ?>

					<?php if ( has_category() ) : ?>
							<span class="icon category-icon"><?php the_category( '</span><span class="icon category-icon">' ) ?></span>
					<?php endif; ?>
					
					<?php if ( has_tag() ) : ?>
								
								<?php the_tags( '<span class="icon tags-icon">', '</span><span class="icon tags-icon">', '</span>' ); ?>
								
					<?php endif; ?>

		<?php endif; // ! post_password_required() ?>

		<?php edit_post_link( __( 'Edit', 'zenearth' ), '<span class="edit-icon">', '</span>' ); ?>

	</div><!-- .after-content -->
	
	<div class="separator">
	</div>

</article><!-- #post-## -->
