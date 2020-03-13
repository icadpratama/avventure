<?php
/**
 * The Front Page template file.
 *
 * @package WordPress
 * @subpackage AVVENTURE
 * @since AVVENTURE 1.0.31
 */

get_header();

// If front-page is a static page
if ( get_option( 'show_on_front' ) == 'page' ) {

	// If Front Page Builder is enabled - display sections
	if ( avventure_is_on( avventure_get_theme_option( 'front_page_enabled' ) ) ) {

		if ( have_posts() ) {
			the_post();
		}

		$avventure_sections = avventure_array_get_keys_by_value( avventure_get_theme_option( 'front_page_sections' ), 1, false );
		if ( is_array( $avventure_sections ) ) {
			foreach ( $avventure_sections as $avventure_section ) {
				get_template_part( apply_filters( 'avventure_filter_get_template_part', 'front-page/section', $avventure_section ), $avventure_section );
			}
		}

		// Else if this page is blog archive
	} elseif ( is_page_template( 'blog.php' ) ) {
		get_template_part( apply_filters( 'avventure_filter_get_template_part', 'blog' ) );

		// Else - display native page content
	} else {
		get_template_part( apply_filters( 'avventure_filter_get_template_part', 'page' ) );
	}

	// Else get index template to show posts
} else {
	get_template_part( apply_filters( 'avventure_filter_get_template_part', 'index' ) );
}

get_footer();
