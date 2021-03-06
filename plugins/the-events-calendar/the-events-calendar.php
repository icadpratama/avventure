<?php
/* Tribe Events Calendar support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 1 - register filters, that add/remove lists items for the Theme Options
if ( ! function_exists( 'avventure_tribe_events_theme_setup1' ) ) {
	add_action( 'after_setup_theme', 'avventure_tribe_events_theme_setup1', 1 );
	function avventure_tribe_events_theme_setup1() {
		add_filter( 'avventure_filter_list_sidebars', 'avventure_tribe_events_list_sidebars' );
	}
}

// Theme init priorities:
// 3 - add/remove Theme Options elements
if ( ! function_exists( 'avventure_tribe_events_theme_setup3' ) ) {
	add_action( 'after_setup_theme', 'avventure_tribe_events_theme_setup3', 3 );
	function avventure_tribe_events_theme_setup3() {
		if ( avventure_exists_tribe_events() ) {

			// Section 'Tribe Events'
			avventure_storage_merge_array(
				'options', '', array_merge(
					array(
						'events' => array(
							'title' => esc_html__( 'Events', 'avventure' ),
							'desc'  => wp_kses_data( __( 'Select parameters to display the events pages', 'avventure' ) ),
							'type'  => 'section',
						),
					),
					avventure_options_get_list_cpt_options( 'events' )
				)
			);
		}
	}
}

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'avventure_tribe_events_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'avventure_tribe_events_theme_setup9', 9 );
	function avventure_tribe_events_theme_setup9() {

		add_filter( 'avventure_filter_merge_styles', 'avventure_tribe_events_merge_styles' );
		add_filter( 'avventure_filter_merge_styles_responsive', 'avventure_tribe_events_merge_styles_responsive' );

		if ( avventure_exists_tribe_events() ) {
			add_action( 'wp_enqueue_scripts', 'avventure_tribe_events_frontend_scripts', 1100 );
			add_filter( 'avventure_filter_post_type_taxonomy', 'avventure_tribe_events_post_type_taxonomy', 10, 2 );
			if ( ! is_admin() ) {
				add_filter( 'avventure_filter_detect_blog_mode', 'avventure_tribe_events_detect_blog_mode' );
				add_filter( 'avventure_filter_get_post_categories', 'avventure_tribe_events_get_post_categories' );
				add_filter( 'avventure_filter_get_post_date', 'avventure_tribe_events_get_post_date' );
			}
		}
		if ( is_admin() ) {
			add_filter( 'avventure_filter_tgmpa_required_plugins', 'avventure_tribe_events_tgmpa_required_plugins' );
		}

	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'avventure_tribe_events_tgmpa_required_plugins' ) ) {
	//Handler of the add_filter('avventure_filter_tgmpa_required_plugins',	'avventure_tribe_events_tgmpa_required_plugins');
	function avventure_tribe_events_tgmpa_required_plugins( $list = array() ) {
		if ( avventure_storage_isset( 'required_plugins', 'the-events-calendar' ) ) {
			$list[] = array(
				'name'     => avventure_storage_get_array( 'required_plugins', 'the-events-calendar' ),
				'slug'     => 'the-events-calendar',
				'required' => false,
			);
		}
		return $list;
	}
}


// Remove 'Tribe Events' section from Customizer
if ( ! function_exists( 'avventure_tribe_events_customizer_register_controls' ) ) {
	add_action( 'customize_register', 'avventure_tribe_events_customizer_register_controls', 100 );
	function avventure_tribe_events_customizer_register_controls( $wp_customize ) {
		$wp_customize->remove_panel( 'tribe_customizer' );
	}
}


// Check if Tribe Events is installed and activated
if ( ! function_exists( 'avventure_exists_tribe_events' ) ) {
	function avventure_exists_tribe_events() {
		return class_exists( 'Tribe__Events__Main' );
	}
}

// Return true, if current page is any tribe_events page
if ( ! function_exists( 'avventure_is_tribe_events_page' ) ) {
	function avventure_is_tribe_events_page() {
		$rez = false;
		if ( avventure_exists_tribe_events() ) {
			if ( ! is_search() ) {
				$rez = tribe_is_event() || tribe_is_event_query() || tribe_is_event_category() || tribe_is_event_venue() || tribe_is_event_organizer();
			}
		}
		return $rez;
	}
}

// Detect current blog mode
if ( ! function_exists( 'avventure_tribe_events_detect_blog_mode' ) ) {
	//Handler of the add_filter( 'avventure_filter_detect_blog_mode', 'avventure_tribe_events_detect_blog_mode' );
	function avventure_tribe_events_detect_blog_mode( $mode = '' ) {
		if ( avventure_is_tribe_events_page() ) {
			$mode = 'events';
		}
		return $mode;
	}
}

// Return taxonomy for current post type
if ( ! function_exists( 'avventure_tribe_events_post_type_taxonomy' ) ) {
	//Handler of the add_filter( 'avventure_filter_post_type_taxonomy',	'avventure_tribe_events_post_type_taxonomy', 10, 2 );
	function avventure_tribe_events_post_type_taxonomy( $tax = '', $post_type = '' ) {
		if ( avventure_exists_tribe_events() && Tribe__Events__Main::POSTTYPE == $post_type ) {
			$tax = Tribe__Events__Main::TAXONOMY;
		}
		return $tax;
	}
}

// Show categories of the current event
if ( ! function_exists( 'avventure_tribe_events_get_post_categories' ) ) {
	//Handler of the add_filter( 'avventure_filter_get_post_categories', 		'avventure_tribe_events_get_post_categories');
	function avventure_tribe_events_get_post_categories( $cats = '' ) {
		if ( get_post_type() == Tribe__Events__Main::POSTTYPE ) {
			$cats = avventure_get_post_terms( ', ', get_the_ID(), Tribe__Events__Main::TAXONOMY );
		}
		return $cats;
	}
}

// Return date of the current event
if ( ! function_exists( 'avventure_tribe_events_get_post_date' ) ) {
	//Handler of the add_filter( 'avventure_filter_get_post_date', 'avventure_tribe_events_get_post_date');
	function avventure_tribe_events_get_post_date( $dt = '' ) {
		if ( get_post_type() == Tribe__Events__Main::POSTTYPE ) {
			$dt = tribe_events_event_schedule_details( get_the_ID(), '', '' );
		}
		return $dt;
	}
}

// Enqueue Tribe Events custom scripts and styles
if ( ! function_exists( 'avventure_tribe_events_frontend_scripts' ) ) {
	//Handler of the add_action( 'wp_enqueue_scripts', 'avventure_tribe_events_frontend_scripts', 1100 );
	function avventure_tribe_events_frontend_scripts() {
		if ( avventure_is_tribe_events_page() ) {
			//wp_deregister_style( 'tribe-events-custom-jquery-styles' );
		}
	}
}

// Merge custom styles
if ( ! function_exists( 'avventure_tribe_events_merge_styles' ) ) {
	//Handler of the add_filter('avventure_filter_merge_styles', 'avventure_tribe_events_merge_styles');
	function avventure_tribe_events_merge_styles( $list ) {
		if ( avventure_exists_tribe_events() ) {
			$list[] = 'plugins/the-events-calendar/_the-events-calendar.scss';
		}
		return $list;
	}
}


// Merge responsive styles
if ( ! function_exists( 'avventure_tribe_events_merge_styles_responsive' ) ) {
	//Handler of the add_filter('avventure_filter_merge_styles_responsive', 'avventure_tribe_events_merge_styles_responsive');
	function avventure_tribe_events_merge_styles_responsive( $list ) {
		if ( avventure_exists_tribe_events() ) {
			$list[] = 'plugins/the-events-calendar/_the-events-calendar-responsive.scss';
		}
		return $list;
	}
}



// Add Tribe Events specific items into lists
//------------------------------------------------------------------------

// Add sidebar
if ( ! function_exists( 'avventure_tribe_events_list_sidebars' ) ) {
	//Handler of the add_filter( 'avventure_filter_list_sidebars', 'avventure_tribe_events_list_sidebars' );
	function avventure_tribe_events_list_sidebars( $list = array() ) {
		$list['tribe_events_widgets'] = array(
			'name'        => esc_html__( 'Tribe Events Widgets', 'avventure' ),
			'description' => esc_html__( 'Widgets to be shown on the Tribe Events pages', 'avventure' ),
		);
		return $list;
	}
}


// Add plugin-specific colors and fonts to the custom CSS
if ( avventure_exists_tribe_events() ) {
	require_once AVVENTURE_THEME_DIR . 'plugins/the-events-calendar/the-events-calendar-styles.php'; }

