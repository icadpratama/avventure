<?php
/**
 * The template to display default site footer
 *
 * @package WordPress
 * @subpackage AVVENTURE
 * @since AVVENTURE 1.0.10
 */

$avventure_footer_id = str_replace( 'footer-custom-', '', avventure_get_theme_option( 'footer_style' ) );
if ( 0 == (int) $avventure_footer_id ) {
	$avventure_footer_id = avventure_get_post_id(
		array(
			'name'      => $avventure_footer_id,
			'post_type' => defined( 'TRX_ADDONS_CPT_LAYOUTS_PT' ) ? TRX_ADDONS_CPT_LAYOUTS_PT : 'cpt_layouts',
		)
	);
} else {
	$avventure_footer_id = apply_filters( 'avventure_filter_get_translated_layout', $avventure_footer_id );
}
$avventure_footer_meta = get_post_meta( $avventure_footer_id, 'trx_addons_options', true );
if ( ! empty( $avventure_footer_meta['margin'] ) != '' ) {
	avventure_add_inline_css( sprintf( '.page_content_wrap{padding-bottom:%s}', esc_attr( avventure_prepare_css_value( $avventure_footer_meta['margin'] ) ) ) );
}
?>
<footer class="footer_wrap footer_custom footer_custom_<?php echo esc_attr( $avventure_footer_id ); ?> footer_custom_<?php echo esc_attr( sanitize_title( get_the_title( $avventure_footer_id ) ) ); ?>
						<?php
						if ( ! avventure_is_inherit( avventure_get_theme_option( 'footer_scheme' ) ) ) {
							echo ' scheme_' . esc_attr( avventure_get_theme_option( 'footer_scheme' ) );
						}
						?>
						">
	<?php
	// Custom footer's layout
	do_action( 'avventure_action_show_layout', $avventure_footer_id );
	?>
</footer><!-- /.footer_wrap -->
