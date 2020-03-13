<?php
/**
 * The Portfolio template to display the content
 *
 * Used for index/archive/search.
 *
 * @package WordPress
 * @subpackage AVVENTURE
 * @since AVVENTURE 1.0
 */

$avventure_template_args = get_query_var( 'avventure_template_args' );
if ( is_array( $avventure_template_args ) ) {
	$avventure_columns    = empty( $avventure_template_args['columns'] ) ? 2 : max( 2, $avventure_template_args['columns'] );
	$avventure_blog_style = array( $avventure_template_args['type'], $avventure_columns );
} else {
	$avventure_blog_style = explode( '_', avventure_get_theme_option( 'blog_style' ) );
	$avventure_columns    = empty( $avventure_blog_style[1] ) ? 2 : max( 2, $avventure_blog_style[1] );
}
$avventure_post_format = get_post_format();
$avventure_post_format = empty( $avventure_post_format ) ? 'standard' : str_replace( 'post-format-', '', $avventure_post_format );
$avventure_animation   = avventure_get_theme_option( 'blog_animation' );

?><article id="post-<?php the_ID(); ?>" 
									<?php
									post_class(
										'post_item'
										. ' post_layout_portfolio'
										. ' post_layout_portfolio_' . esc_attr( $avventure_columns )
										. ' post_format_' . esc_attr( $avventure_post_format )
										. ( is_sticky() && ! is_paged() ? ' sticky' : '' )
										. ( ! empty( $avventure_template_args['slider'] ) ? ' slider-slide swiper-slide' : '' )
									);
									echo ( ! avventure_is_off( $avventure_animation ) && empty( $avventure_template_args['slider'] ) ? ' data-animation="' . esc_attr( avventure_get_animation_classes( $avventure_animation ) ) . '"' : '' );
									?>
	>
<?php

	// Sticky label
if ( is_sticky() && ! is_paged() ) {
	?>
		<span class="post_label label_sticky"></span>
		<?php
}

	$avventure_image_hover = ! empty( $avventure_template_args['hover'] ) && ! avventure_is_inherit( $avventure_template_args['hover'] )
								? $avventure_template_args['hover']
								: avventure_get_theme_option( 'image_hover' );
	// Featured image
	avventure_show_post_featured(
		array(
			'singular'      => false,
			'hover'         => $avventure_image_hover,
			'no_links'      => ! empty( $avventure_template_args['no_links'] ),
			'thumb_size'    => avventure_get_thumb_size(
				strpos( avventure_get_theme_option( 'body_style' ), 'full' ) !== false || $avventure_columns < 3
								? 'masonry-big'
				: 'masonry'
			),
			'show_no_image' => true,
			'class'         => 'dots' == $avventure_image_hover ? 'hover_with_info' : '',
			'post_info'     => 'dots' == $avventure_image_hover ? '<div class="post_info">' . esc_html( get_the_title() ) . '</div>' : '',
		)
	);
	?>
</article>
