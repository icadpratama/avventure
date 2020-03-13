<?php
/* Contact Form 7 support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'avventure_cf7_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'avventure_cf7_theme_setup9', 9 );
	function avventure_cf7_theme_setup9() {

		add_filter( 'avventure_filter_merge_scripts', 'avventure_cf7_merge_scripts' );
		add_filter( 'avventure_filter_merge_styles', 'avventure_cf7_merge_styles' );

		if ( avventure_exists_cf7() ) {
			add_action( 'wp_enqueue_scripts', 'avventure_cf7_frontend_scripts', 1100 );
		}

		if ( is_admin() ) {
			add_filter( 'avventure_filter_tgmpa_required_plugins', 'avventure_cf7_tgmpa_required_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'avventure_cf7_tgmpa_required_plugins' ) ) {
	//Handler of the add_filter('avventure_filter_tgmpa_required_plugins',	'avventure_cf7_tgmpa_required_plugins');
	function avventure_cf7_tgmpa_required_plugins( $list = array() ) {
		if ( avventure_storage_isset( 'required_plugins', 'contact-form-7' ) ) {
			// CF7 plugin
			$list[] = array(
				'name'     => avventure_storage_get_array( 'required_plugins', 'contact-form-7' ),
				'slug'     => 'contact-form-7',
				'required' => false,
			);
			// CF7 extension - datepicker
			if ( ! AVVENTURE_THEME_FREE ) {
				$params = array(
					'name'     => esc_html__( 'Contact Form 7 Datepicker', 'avventure' ),
					'slug'     => 'contact-form-7-datepicker',
					'required' => false,
				);
				$path   = avventure_get_file_dir( 'plugins/contact-form-7/contact-form-7-datepicker.zip' );
				if ( '' != $path ) {
					$params['source'] = $path;
				}
				$list[] = $params;
			}
		}
		return $list;
	}
}



// Check if cf7 installed and activated
if ( ! function_exists( 'avventure_exists_cf7' ) ) {
	function avventure_exists_cf7() {
		return class_exists( 'WPCF7' );
	}
}

// Enqueue custom scripts
if ( ! function_exists( 'avventure_cf7_frontend_scripts' ) ) {
	//Handler of the add_action( 'wp_enqueue_scripts', 'avventure_cf7_frontend_scripts', 1100 );
	function avventure_cf7_frontend_scripts() {
		if ( avventure_exists_cf7() ) {
			if ( avventure_is_on( avventure_get_theme_option( 'debug_mode' ) ) ) {
				$avventure_url = avventure_get_file_url( 'plugins/contact-form-7/contact-form-7.js' );
				if ( '' != $avventure_url ) {
					wp_enqueue_script( 'cf7', $avventure_url, array( 'jquery' ), null, true );
				}
			}
		}
	}
}

// Merge custom scripts
if ( ! function_exists( 'avventure_cf7_merge_scripts' ) ) {
	//Handler of the add_filter('avventure_filter_merge_scripts', 'avventure_cf7_merge_scripts');
	function avventure_cf7_merge_scripts( $list ) {
		if ( avventure_exists_cf7() ) {
			$list[] = 'plugins/contact-form-7/contact-form-7.js';
		}
		return $list;
	}
}

// Merge custom styles
if ( ! function_exists( 'avventure_cf7_merge_styles' ) ) {
	//Handler of the add_filter('avventure_filter_merge_styles', 'avventure_cf7_merge_styles');
	function avventure_cf7_merge_styles( $list ) {
		if ( avventure_exists_cf7() ) {
			$list[] = 'plugins/contact-form-7/_contact-form-7.scss';
		}
		return $list;
	}
}

