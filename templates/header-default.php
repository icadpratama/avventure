<?php
/**
 * The template to display default site header
 *
 * @package WordPress
 * @subpackage AVVENTURE
 * @since AVVENTURE 1.0
 */

$avventure_header_css   = '';
$avventure_header_image = get_header_image();
$avventure_header_video = avventure_get_header_video();
if ( ! empty( $avventure_header_image ) && avventure_trx_addons_featured_image_override( is_singular() || avventure_storage_isset( 'blog_archive' ) || is_category() ) ) {
	$avventure_header_image = avventure_get_current_mode_image( $avventure_header_image );
}

?><header class="top_panel top_panel_default
	<?php
	echo ! empty( $avventure_header_image ) || ! empty( $avventure_header_video ) ? ' with_bg_image' : ' without_bg_image';
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

	// Main menu
	if ( avventure_get_theme_option( 'menu_style' ) == 'top' ) {
		get_template_part( apply_filters( 'avventure_filter_get_template_part', 'templates/header-navi' ) );
	}

	// Mobile header
	if ( avventure_is_on( avventure_get_theme_option( 'header_mobile_enabled' ) ) ) {
		get_template_part( apply_filters( 'avventure_filter_get_template_part', 'templates/header-mobile' ) );
	}

	// Page title and breadcrumbs area
	get_template_part( apply_filters( 'avventure_filter_get_template_part', 'templates/header-title' ) );

	// Header widgets area
	get_template_part( apply_filters( 'avventure_filter_get_template_part', 'templates/header-widgets' ) );

	// Display featured image in the header on the single posts
	// Comment next line to prevent show featured image in the header area
	// and display it in the post's content
	get_template_part( apply_filters( 'avventure_filter_get_template_part', 'templates/header-single' ) );

	?>
</header>
