<?php
/**
 * The template for homepage posts with "Excerpt" style
 *
 * @package WordPress
 * @subpackage AVVENTURE
 * @since AVVENTURE 1.0
 */

avventure_storage_set( 'blog_archive', true );

get_header();

if ( have_posts() ) {

	avventure_blog_archive_start();

	?><div class="posts_container">
		<?php

		$avventure_stickies   = is_home() ? get_option( 'sticky_posts' ) : false;
		$avventure_sticky_out = avventure_get_theme_option( 'sticky_style' ) == 'columns'
								&& is_array( $avventure_stickies ) && count( $avventure_stickies ) > 0 && get_query_var( 'paged' ) < 1;
		if ( $avventure_sticky_out ) {
			?>
			<div class="sticky_wrap columns_wrap">
			<?php
		}
		while ( have_posts() ) {
			the_post();
			if ( $avventure_sticky_out && ! is_sticky() ) {
				$avventure_sticky_out = false;
				?>
				</div>
				<?php
			}
			$avventure_part = $avventure_sticky_out && is_sticky() ? 'sticky' : 'excerpt';
			get_template_part( apply_filters( 'avventure_filter_get_template_part', 'content', $avventure_part ), $avventure_part );
		}
		if ( $avventure_sticky_out ) {
			$avventure_sticky_out = false;
			?>
			</div>
			<?php
		}

		?>
	</div>
	<?php

	avventure_show_pagination();

	avventure_blog_archive_end();

} else {

	if ( is_search() ) {
		get_template_part( apply_filters( 'avventure_filter_get_template_part', 'content', 'none-search' ), 'none-search' );
	} else {
		get_template_part( apply_filters( 'avventure_filter_get_template_part', 'content', 'none-archive' ), 'none-archive' );
	}
}

get_footer();
