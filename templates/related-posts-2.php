<?php
/**
 * The template 'Style 2' to displaying related posts
 *
 * @package WordPress
 * @subpackage AVVENTURE
 * @since AVVENTURE 1.0
 */

$avventure_link        = get_permalink();
$avventure_post_format = get_post_format();
$avventure_post_format = empty( $avventure_post_format ) ? 'standard' : str_replace( 'post-format-', '', $avventure_post_format );
?><div id="post-<?php the_ID(); ?>" 
	<?php post_class( 'related_item related_item_style_2 post_format_' . esc_attr( $avventure_post_format ) ); ?>>
						<?php
						avventure_show_post_featured(
							array(
								'thumb_size'    => apply_filters( 'avventure_filter_related_thumb_size', avventure_get_thumb_size( (int) avventure_get_theme_option( 'related_posts' ) == 1 ? 'huge' : 'big' ) ),
								'show_no_image' => avventure_get_theme_setting( 'allow_no_image' ),
								'singular'      => false,
							)
						);
						?>
	<div class="post_header entry-header">
	<?php
	if ( in_array( get_post_type(), array( 'post', 'attachment' ) ) ) {
		?>
			<span class="post_date"><a href="<?php echo esc_url( $avventure_link ); ?>"><?php echo wp_kses_data( avventure_get_date() ); ?></a></span>
			<?php
	}
	?>
		<h6 class="post_title entry-title"><a href="<?php echo esc_url( $avventure_link ); ?>"><?php the_title(); ?></a></h6>
	</div>
</div>
