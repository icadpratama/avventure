<?php
/**
 * The Gallery template to display posts
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
$avventure_image       = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );

?><article id="post-<?php the_ID(); ?>" 
									<?php
									post_class(
										'post_item'
										. ' post_layout_portfolio'
										. ' post_layout_gallery'
										. ' post_layout_gallery_' . esc_attr( $avventure_columns )
										. ' post_format_' . esc_attr( $avventure_post_format )
										. ( ! empty( $avventure_template_args['slider'] ) ? ' slider-slide swiper-slide' : '' )
									);
									echo ( ! avventure_is_off( $avventure_animation ) && empty( $avventure_template_args['slider'] ) ? ' data-animation="' . esc_attr( avventure_get_animation_classes( $avventure_animation ) ) . '"' : '' );
									?>
	data-size="
	<?php
	if ( ! empty( $avventure_image[1] ) && ! empty( $avventure_image[2] ) ) {
		echo intval( $avventure_image[1] ) . 'x' . intval( $avventure_image[2] );}
	?>
	"
	data-src="
	<?php
	if ( ! empty( $avventure_image[0] ) ) {
		echo esc_url( $avventure_image[0] );}
	?>
	"
>
<?php

	// Sticky label
if ( is_sticky() && ! is_paged() ) {
	?>
		<span class="post_label label_sticky"></span>
		<?php
}

	// Featured image
	$avventure_image_hover = 'icon';  // !empty($avventure_template_args['hover']) && !avventure_is_inherit($avventure_template_args['hover']) ? $avventure_template_args['hover'] : avventure_get_theme_option('image_hover');
if ( in_array( $avventure_image_hover, array( 'icons', 'zoom' ) ) ) {
	$avventure_image_hover = 'dots';
}
	$avventure_components = avventure_array_get_keys_by_value( avventure_get_theme_option( 'meta_parts' ) );
	$avventure_counters   = avventure_array_get_keys_by_value( avventure_get_theme_option( 'counters' ) );
	avventure_show_post_featured(
		array(
			'hover'         => $avventure_image_hover,
			'singular'      => false,
			'no_links'      => ! empty( $avventure_template_args['no_links'] ),
			'thumb_size'    => avventure_get_thumb_size( strpos( avventure_get_theme_option( 'body_style' ), 'full' ) !== false || $avventure_columns < 3 ? 'masonry-big' : 'masonry' ),
			'thumb_only'    => true,
			'show_no_image' => true,
			'post_info'     => '<div class="post_details">'
							. '<h2 class="post_title">'
								. ( empty( $avventure_template_args['no_links'] )
									? '<a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a>'
									: esc_html( get_the_title() )
									)
							. '</h2>'
							. '<div class="post_description">'
								. ( ! empty( $avventure_components )
									? avventure_show_post_meta(
										apply_filters(
											'avventure_filter_post_meta_args', array(
												'components' => $avventure_components,
												'counters' => $avventure_counters,
												'seo'      => false,
												'echo'     => false,
											), $avventure_blog_style[0], $avventure_columns
										)
									)
									: ''
									)
								. ( empty( $avventure_template_args['hide_excerpt'] )
									? '<div class="post_description_content">' . get_the_excerpt() . '</div>'
									: ''
									)
								. ( empty( $avventure_template_args['no_links'] )
									? '<a href="' . esc_url( get_permalink() ) . '" class="theme_button post_readmore"><span class="post_readmore_label">' . esc_html__( 'Learn more', 'avventure' ) . '</span></a>'
									: ''
									)
							. '</div>'
						. '</div>',
		)
	);
	?>
	</article>
