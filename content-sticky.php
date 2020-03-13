<?php
/**
 * The Sticky template to display the sticky posts
 *
 * Used for index/archive
 *
 * @package WordPress
 * @subpackage AVVENTURE
 * @since AVVENTURE 1.0
 */

$avventure_columns     = max( 1, min( 3, count( get_option( 'sticky_posts' ) ) ) );
$avventure_post_format = get_post_format();
$avventure_post_format = empty( $avventure_post_format ) ? 'standard' : str_replace( 'post-format-', '', $avventure_post_format );
$avventure_animation   = avventure_get_theme_option( 'blog_animation' );

?><div class="column-1_<?php echo esc_attr( $avventure_columns ); ?>"><article id="post-<?php the_ID(); ?>" 
	<?php post_class( 'post_item post_layout_sticky post_format_' . esc_attr( $avventure_post_format ) ); ?>
	<?php echo ( ! avventure_is_off( $avventure_animation ) ? ' data-animation="' . esc_attr( avventure_get_animation_classes( $avventure_animation ) ) . '"' : '' ); ?>
	>

	<?php
	if ( is_sticky() && is_home() && ! is_paged() ) {
		?>
		<span class="post_label label_sticky"></span>
		<?php
	}

	// Featured image
	avventure_show_post_featured(
		array(
			'thumb_size' => avventure_get_thumb_size( 1 == $avventure_columns ? 'big' : ( 2 == $avventure_columns ? 'med' : 'avatar' ) ),
		)
	);

	if ( ! in_array( $avventure_post_format, array( 'link', 'aside', 'status', 'quote' ) ) ) {
		?>
		<div class="post_header entry-header">
			<?php
			// Post title
			the_title( sprintf( '<h6 class="post_title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h6>' );
			// Post meta
			avventure_show_post_meta( apply_filters( 'avventure_filter_post_meta_args', array(), 'sticky', $avventure_columns ) );
			?>
		</div><!-- .entry-header -->
		<?php
	}
	?>
</article></div>
