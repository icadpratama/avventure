<?php
/**
 * The template to display custom header from the ThemeREX Addons Layouts
 *
 * @package WordPress
 * @subpackage AVVENTURE
 * @since AVVENTURE 1.0.06
 */

$avventure_header_css   = '';
$avventure_header_image = get_header_image();
$avventure_header_video = avventure_get_header_video();
if ( ! empty( $avventure_header_image ) && avventure_trx_addons_featured_image_override( is_singular() || avventure_storage_isset( 'blog_archive' ) || is_category() ) ) {
	$avventure_header_image = avventure_get_current_mode_image( $avventure_header_image );
}

$avventure_header_id = str_replace( 'header-custom-', '', avventure_get_theme_option( 'header_style' ) );

if( is_404() ) { 
	if ( 'custom' == avventure_get_theme_option( 'header_type_404' ) ) {
		$avventure_header_id = str_replace( 'header-custom-', '', avventure_get_theme_option( 'header_style_404' ) );
	}
}
if ( 0 == (int) $avventure_header_id ) {
	$avventure_header_id = avventure_get_post_id(
		array(
			'name'      => $avventure_header_id,
			'post_type' => defined( 'TRX_ADDONS_CPT_LAYOUTS_PT' ) ? TRX_ADDONS_CPT_LAYOUTS_PT : 'cpt_layouts',
		)
	);
} else {
	$avventure_header_id = apply_filters( 'avventure_filter_get_translated_layout', $avventure_header_id );
}
$avventure_header_meta = get_post_meta( $avventure_header_id, 'trx_addons_options', true );
if ( ! empty( $avventure_header_meta['margin'] ) != '' ) {
	avventure_add_inline_css( sprintf( '.page_content_wrap{padding-top:%s}', esc_attr( avventure_prepare_css_value( $avventure_header_meta['margin'] ) ) ) );
}

?><header class="top_panel top_panel_custom top_panel_custom_<?php echo esc_attr( $avventure_header_id ); ?> top_panel_custom_<?php echo esc_attr( sanitize_title( get_the_title( $avventure_header_id ) ) ); ?>
				<?php
				echo ! empty( $avventure_header_image ) || ! empty( $avventure_header_video )
					? ' with_bg_image'
					: ' without_bg_image';
				if ( '' != $avventure_header_video ) {
					echo ' with_bg_video';
				}
				if ( '' != $avventure_header_image ) {
					echo ' ' . esc_attr( avventure_add_inline_css_class( 'background-image: url(' . esc_url( $avventure_header_image ) . ');' ) );
				}
				if ( is_single() && has_post_thumbnail() ) {
					echo ' with_featured_image';
				}
				if ( avventure_is_on( avventure_get_theme_option( 'header_fullheight' ) ) ) {
					echo ' header_fullheight avventure-full-height';
				}
				if ( ! avventure_is_inherit( avventure_get_theme_option( 'header_scheme' ) ) ) {
					echo ' scheme_' . esc_attr( avventure_get_theme_option( 'header_scheme' ) );
				}
				?>
">
	<?php

	// Background video
	if ( ! empty( $avventure_header_video ) ) {
		get_template_part( apply_filters( 'avventure_filter_get_template_part', 'templates/header-video' ) );
	}

	// Custom header's layout
	do_action( 'avventure_action_show_layout', $avventure_header_id );

	// Header widgets area
	get_template_part( apply_filters( 'avventure_filter_get_template_part', 'templates/header-widgets' ) );

	?>
</header>
