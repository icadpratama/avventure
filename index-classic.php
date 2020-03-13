<?php
/**
 * The template for homepage posts with "Classic" style
 *
 * @package WordPress
 * @subpackage AVVENTURE
 * @since AVVENTURE 1.0
 */

avventure_storage_set( 'blog_archive', true );

get_header();

if ( have_posts() ) {

	avventure_blog_archive_start();

	$avventure_classes    = 'posts_container '
						. ( substr( avventure_get_theme_option( 'blog_style' ), 0, 7 ) == 'classic'
							? 'columns_wrap columns_padding_bottom'
							: 'masonry_wrap'
							);
	$avventure_stickies   = is_home() ? get_option( 'sticky_posts' ) : false;
	$avventure_sticky_out = avventure_get_theme_option( 'sticky_style' ) == 'columns'
							&& is_array( $avventure_stickies ) && count( $avventure_stickies ) > 0 && get_query_var( 'paged' ) < 1;
	if ( $avventure_sticky_out ) {
		?>
		<div class="sticky_wrap columns_wrap">
		<?php
	}
	if ( ! $avventure_sticky_out ) {
		if ( avventure_get_theme_option( 'first_post_large' ) && ! is_paged() && ! in_array( avventure_get_theme_option( 'body_style' ), array( 'fullwide', 'fullscreen' ) ) ) {
			the_post();
			get_template_part( apply_filters( 'avventure_filter_get_template_part', 'content', 'excerpt' ), 'excerpt' );
		}

		?>
		<div class="<?php echo esc_attr( $avventure_classes ); ?>">
		<?php
	}
	while ( have_posts() ) {
		the_post();
		if ( $avventure_sticky_out && ! is_sticky() ) {
			$avventure_sticky_out = false;
			?>
			</div><div class="<?php echo esc_attr( $avventure_classes ); ?>">
			<?php
		}
		$avventure_part = $avventure_sticky_out && is_sticky() ? 'sticky' : 'classic';
		get_template_part( apply_filters( 'avventure_filter_get_template_part', 'content', $avventure_part ), $avventure_part );
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
