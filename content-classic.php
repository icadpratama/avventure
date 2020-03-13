<?php
/**
 * The Classic template to display the content
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
$avventure_expanded   = ! avventure_sidebar_present() && avventure_is_on( avventure_get_theme_option( 'expand_content' ) );
$avventure_animation  = avventure_get_theme_option( 'blog_animation' );
$avventure_components = avventure_array_get_keys_by_value( avventure_get_theme_option( 'meta_parts' ) );
$avventure_counters   = avventure_array_get_keys_by_value( avventure_get_theme_option( 'counters' ) );

$avventure_post_format = get_post_format();
$avventure_post_format = empty( $avventure_post_format ) ? 'standard' : str_replace( 'post-format-', '', $avventure_post_format );

?><div class="
<?php
if ( ! empty( $avventure_template_args['slider'] ) ) {
	echo ' slider-slide swiper-slide';
} else {
	echo ( 'classic' == $avventure_blog_style[0] ? 'column' : 'masonry_item masonry_item' ) . '-1_' . esc_attr( $avventure_columns );
}
?>
"><article id="post-<?php the_ID(); ?>" 
	<?php
		post_class(
			'post_item post_format_' . esc_attr( $avventure_post_format )
					. ' post_layout_classic post_layout_classic_' . esc_attr( $avventure_columns )
					. ' post_layout_' . esc_attr( $avventure_blog_style[0] )
					. ' post_layout_' . esc_attr( $avventure_blog_style[0] ) . '_' . esc_attr( $avventure_columns )
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

	// Featured image
	$avventure_hover = ! empty( $avventure_template_args['hover'] ) && ! avventure_is_inherit( $avventure_template_args['hover'] )
						? $avventure_template_args['hover']
						: avventure_get_theme_option( 'image_hover' );
	avventure_show_post_featured(
		array(
			'thumb_size' => avventure_get_thumb_size(
				'classic' == $avventure_blog_style[0]
						? ( strpos( avventure_get_theme_option( 'body_style' ), 'full' ) !== false
								? ( $avventure_columns > 2 ? 'big' : 'huge' )
								: ( $avventure_columns > 2
									? ( $avventure_expanded ? 'med' : 'small' )
									: ( $avventure_expanded ? 'big' : 'med' )
									)
							)
						: ( strpos( avventure_get_theme_option( 'body_style' ), 'full' ) !== false
								? ( $avventure_columns > 2 ? 'masonry-big' : 'full' )
								: ( $avventure_columns <= 2 && $avventure_expanded ? 'masonry-big' : 'masonry' )
							)
			),
			'hover'      => $avventure_hover,
			'no_links'   => ! empty( $avventure_template_args['no_links'] ),
			'singular'   => false,
		)
	);

	if ( ! in_array( $avventure_post_format, array( 'link', 'aside', 'status', 'quote' ) ) ) {
		?>
		<div class="post_header entry-header">
			<?php
			do_action( 'avventure_action_before_post_title' );

			// Post title
			if ( empty( $avventure_template_args['no_links'] ) ) {
				the_title( sprintf( '<h4 class="post_title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h4>' );
			} else {
				the_title( '<h4 class="post_title entry-title">', '</h4>' );
			}

			do_action( 'avventure_action_before_post_meta' );

			// Post meta
			if ( ! empty( $avventure_components ) && ! in_array( $avventure_hover, array( 'border', 'pull', 'slide', 'fade' ) ) ) {
				avventure_show_post_meta(
					apply_filters(
						'avventure_filter_post_meta_args', array(
							'components' => $avventure_components,
							'counters'   => $avventure_counters,
							'seo'        => false,
						), $avventure_blog_style[0], $avventure_columns
					)
				);
			}

			do_action( 'avventure_action_after_post_meta' );
			?>
		</div><!-- .entry-header -->
		<?php
	}
	?>

	<div class="post_content entry-content">
	<?php
	if ( empty( $avventure_template_args['hide_excerpt'] ) ) {
		?>
			<div class="post_content_inner">
			<?php
			if ( has_excerpt() ) {
				the_excerpt();
			} elseif ( strpos( get_the_content( '!--more' ), '!--more' ) !== false ) {
				the_content( '' );
			} elseif ( in_array( $avventure_post_format, array( 'link', 'aside', 'status' ) ) ) {
				the_content();
			} elseif ( 'quote' == $avventure_post_format ) {
				$quote = avventure_get_tag( get_the_content(), '<blockquote>', '</blockquote>' );
				if ( ! empty( $quote ) ) {
					avventure_show_layout( wpautop( $quote ) );
				} else {
					the_excerpt();
				}
			} elseif ( substr( get_the_content(), 0, 4 ) != '[vc_' ) {
				the_excerpt();
			}
			?>
			</div>
			<?php
	}
		// Post meta
	if ( in_array( $avventure_post_format, array( 'link', 'aside', 'status', 'quote' ) ) ) {
		if ( ! empty( $avventure_components ) ) {
			avventure_show_post_meta(
				apply_filters(
					'avventure_filter_post_meta_args', array(
						'components' => $avventure_components,
						'counters'   => $avventure_counters,
					), $avventure_blog_style[0], $avventure_columns
				)
			);
		}
	}
		// More button
	if ( false && empty( $avventure_template_args['no_links'] ) && ! in_array( $avventure_post_format, array( 'link', 'aside', 'status', 'quote' ) ) ) {
		?>
			<p><a class="more-link" href="<?php echo esc_url( get_permalink() ); ?>"><?php esc_html_e( 'Read more', 'avventure' ); ?></a></p>
			<?php
	}
	?>
	</div><!-- .entry-content -->

</article></div>
