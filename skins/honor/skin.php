<?php
/**
 * Skins support: Main skin file for the skin 'Default'
 *
 * Setup skin-dependent fonts and colors, load scripts and styles,
 * and other operations that affect the appearance and behavior of the theme
 * when the skin is activated
 *
 * @package WordPress
 * @subpackage AVVENTURE
 * @since AVVENTURE 1.0.46
 */


// Theme init priorities:
// 3 - add/remove Theme Options elements
if ( ! function_exists( 'avventure_skin_theme_setup3' ) ) {
	add_action( 'after_setup_theme', 'avventure_skin_theme_setup3', 3 );
	function avventure_skin_theme_setup3() {
		// ToDo: Add / Modify theme options, required plugins, etc.
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'avventure_skin_tgmpa_required_plugins' ) ) {
	add_filter( 'avventure_filter_tgmpa_required_plugins', 'avventure_skin_tgmpa_required_plugins' );
	function avventure_skin_tgmpa_required_plugins( $list = array() ) {
		// ToDo: Check if plugin is in the 'required_plugins' and add his parameters to the TGMPA-list
		//       Replace 'skin-specific-plugin-slug' to the real slug of the plugin
		if ( avventure_storage_isset( 'required_plugins', 'skin-specific-plugin-slug' ) ) {
			$list[] = array(
				'name'     => avventure_storage_get_array( 'required_plugins', 'skin-specific-plugin-slug' ),
				'slug'     => 'skin-specific-plugin-slug',
				'required' => false,
			);
		}
		return $list;
	}
}

// Enqueue skin-specific styles and scripts
if ( ! function_exists( 'avventure_skin_frontend_scripts' ) ) {
	add_action( 'wp_enqueue_scripts', 'avventure_skin_frontend_scripts', 1100 );
	function avventure_skin_frontend_scripts() {
		if ( avventure_is_on( avventure_get_theme_option( 'debug_mode' ) ) ) {
			$avventure_url = avventure_get_file_url( AVVENTURE_SKIN_DIR . 'skin.js' );
			if ( '' != $avventure_url ) {
				wp_enqueue_script( 'avventure-skin-' . esc_attr( AVVENTURE_SKIN_NAME ), $avventure_url, array( 'jquery' ), null, true );
			}
		}
	}
}

// Merge custom styles
if ( ! function_exists( 'avventure_skin_merge_styles' ) ) {
	add_filter( 'avventure_filter_merge_styles', 'avventure_skin_merge_styles' );
	function avventure_skin_merge_styles( $list ) {
		if ( avventure_get_file_dir( AVVENTURE_SKIN_DIR . '_skin.scss' ) != '' ) {
			$list[] = AVVENTURE_SKIN_DIR . '_skin.scss';
		}
		return $list;
	}
}


// Merge responsive styles
if ( ! function_exists( 'avventure_skin_merge_styles_responsive' ) ) {
	add_filter( 'avventure_filter_merge_styles_responsive', 'avventure_skin_merge_styles_responsive' );
	function avventure_skin_merge_styles_responsive( $list ) {
		if ( avventure_get_file_dir( AVVENTURE_SKIN_DIR . '_skin-responsive.scss' ) != '' ) {
			$list[] = AVVENTURE_SKIN_DIR . '_skin-responsive.scss';
		}
		return $list;
	}
}

// Merge custom scripts
if ( ! function_exists( 'avventure_skin_merge_scripts' ) ) {
	add_filter( 'avventure_filter_merge_scripts', 'avventure_skin_merge_scripts' );
	function avventure_skin_merge_scripts( $list ) {
		if ( avventure_get_file_dir( AVVENTURE_SKIN_DIR . 'skin.js' ) != '' ) {
			$list[] = AVVENTURE_SKIN_DIR . 'skin.js';
		}
		return $list;
	}
}


// Add slin-specific colors and fonts to the custom CSS
require_once AVVENTURE_THEME_DIR . AVVENTURE_SKIN_DIR . 'skin-styles.php';

