<?php
/**
 * The default template for displaying content
 *
 * Used for single, index, archive, and search contents.
 *
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php if ( is_single() ) : ?>

			<h1 class="entry-title">
				<?php the_title(); ?>
			</h1>

	<?php else : ?>
	
			<h1 class="post-entry-title">
				<a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
			</h1>
	
	<?php endif; ?>

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

	</div><!-- .before-content -->

	<?php if ( is_single() ) : ?>

				<div class="post-content">
					<?php
						if ( has_post_thumbnail() ) :

							the_post_thumbnail();

						endif;
						
						the_content( __( 'Read More...', 'zenearth') );

						wp_link_pages( array(
							'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'zenearth' ) . '</span>',
							'after'       => '</div>',
							'link_before' => '<span>',
							'link_after'  => '</span>',
						  ) );
					?>
				</div><!-- .content -->

	<?php else : ?>

				<div class="post-content">
					<?php if ( has_post_thumbnail() ) : ?>
								
								<a href="<?php echo esc_url( get_permalink() ); ?>" title="<?php the_title_attribute(); ?>">
									<?php the_post_thumbnail(); ?>
								</a>
								
					<?php endif;

						  the_content( __( 'Read More', 'zenearth') );
					?>
				</div><!-- .content -->

	<?php endif; ?>

	<div class="post-after-content">

		<span class="icon author-icon">
			<?php the_author_posts_link(); ?>
		</span><!-- .author-icon -->

		<?php if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) : ?>

					<span class="icon comments-icon">
						<?php comments_popup_link(__( 'No Comments', 'zenearth' ), __( '1 Comment', 'zenearth' ), __( '% Comments', 'zenearth' ), '', __( 'Comments are closed.', 'zenearth' )); ?>
					</span><!-- .comments-icon -->
		
		<?php endif; ?>

		<?php if ( ! post_password_required() ) : ?>

					<?php if ( has_category() ) : ?>
							<span class="icon category-icon"><?php the_category( '</span><span class="icon category-icon">' ); ?></span>
					<?php endif; ?>
					
					<?php if ( has_tag() ) : ?>

							<?php the_tags( '<span class="icon tags-icon">', '</span><span class="icon tags-icon">', '</span>' ); ?>
								
					<?php endif; ?>

		<?php endif; // ! post_password_required() ?>

		<?php edit_post_link( __( 'Edit', 'zenearth' ), '<span class="edit-icon">', '</span>' ); ?>

	</div><!-- .after-content -->
	
	<?php if ( !is_single() ) : ?>
			<div class="separator">
			</div>
	<?php endif; ?>
</article><!-- #post-## -->
