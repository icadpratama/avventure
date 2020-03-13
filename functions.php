<?php
/**
 * Theme functions: init, enqueue scripts and styles, include required files and widgets
 *
 * @package WordPress
 * @subpackage AVVENTURE
 * @since AVVENTURE 1.0
 */

if ( ! defined( 'AVVENTURE_THEME_DIR' ) ) {
	define( 'AVVENTURE_THEME_DIR', trailingslashit( get_template_directory() ) );
}
if ( ! defined( 'AVVENTURE_THEME_URL' ) ) {
	define( 'AVVENTURE_THEME_URL', trailingslashit( get_template_directory_uri() ) );
}
if ( ! defined( 'AVVENTURE_CHILD_DIR' ) ) {
	define( 'AVVENTURE_CHILD_DIR', trailingslashit( get_stylesheet_directory() ) );
}
if ( ! defined( 'AVVENTURE_CHILD_URL' ) ) {
	define( 'AVVENTURE_CHILD_URL', trailingslashit( get_stylesheet_directory_uri() ) );
}

//-------------------------------------------------------
//-- Theme init
//-------------------------------------------------------

// Theme init priorities:
// Action 'after_setup_theme'
// 1 - register filters to add/remove lists items in the Theme Options
// 2 - create Theme Options
// 3 - add/remove Theme Options elements
// 5 - load Theme Options. Attention! After this step you can use only basic options (not overriden)
// 9 - register other filters (for installer, etc.)
//10 - standard Theme init procedures (not ordered)
// Action 'wp_loaded'
// 1 - detect override mode. Attention! Only after this step you can use overriden options (separate values for the shop, courses, etc.)

if ( ! function_exists( 'avventure_theme_setup1' ) ) {
	add_action( 'after_setup_theme', 'avventure_theme_setup1', 1 );
	function avventure_theme_setup1() {
		// Make theme available for translation
		// Translations can be filed in the /languages directory
		// Attention! Translations must be loaded before first call any translation functions!
		load_theme_textdomain( 'avventure', AVVENTURE_THEME_DIR . 'languages' );
	}
}

if ( ! function_exists( 'avventure_theme_setup' ) ) {
	add_action( 'after_setup_theme', 'avventure_theme_setup' );
	function avventure_theme_setup() {

		// Set theme content width
		$GLOBALS['content_width'] = apply_filters( 'avventure_filter_content_width', avventure_get_theme_option( 'page_width' ) );

		// Add default posts and comments RSS feed links to head
		add_theme_support( 'automatic-feed-links' );

		// Custom header setup
		add_theme_support(
			'custom-header', array(
				'header-text' => false,
				'video'       => true,
			)
		);

		// Custom logo
		add_theme_support(
			'custom-logo', array(
				'width'       => 250,
				'height'      => 60,
				'flex-width'  => true,
				'flex-height' => true,
			)
		);
		// Custom backgrounds setup
		add_theme_support( 'custom-background', array() );

		// Partial refresh support in the Customize
		add_theme_support( 'customize-selective-refresh-widgets' );

		// Supported posts formats
		add_theme_support( 'post-formats', array( 'gallery', 'video', 'audio', 'link', 'quote', 'image', 'status', 'aside', 'chat' ) );

		// Autogenerate title tag
		add_theme_support( 'title-tag' );

		// Add theme menus
		add_theme_support( 'nav-menus' );

		// Switch default markup for search form, comment form, and comments to output valid HTML5.
		add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

		// Editor custom stylesheet - for user
		add_editor_style(
			array_merge(
				array(
					'css/editor-style.css',
					avventure_get_file_url( 'css/font-icons/css/fontello-embedded.css' ),
				),
				avventure_theme_fonts_for_editor()
			)
		);

		// Register navigation menu
		register_nav_menus(
			array(
				'menu_main'   => esc_html__( 'Main Menu', 'avventure' ),
				'menu_mobile' => esc_html__( 'Mobile Menu', 'avventure' ),
				'menu_footer' => esc_html__( 'Footer Menu', 'avventure' ),
			)
		);

		// Register theme-specific thumb sizes
		add_theme_support( 'post-thumbnails' );
		set_post_thumbnail_size( 370, 0, false );
		$thumb_sizes = avventure_storage_get( 'theme_thumbs' );
		$mult        = avventure_get_theme_option( 'retina_ready', 1 );
		if ( $mult > 1 ) {
			$GLOBALS['content_width'] = apply_filters( 'avventure_filter_content_width', 1170 * $mult );
		}
		foreach ( $thumb_sizes as $k => $v ) {
			add_image_size( $k, $v['size'][0], $v['size'][1], $v['size'][2] );
			if ( $mult > 1 ) {
				add_image_size( $k . '-@retina', $v['size'][0] * $mult, $v['size'][1] * $mult, $v['size'][2] );
			}
		}
		// Add new thumb names
		add_filter( 'image_size_names_choose', 'avventure_theme_thumbs_sizes' );

		// Excerpt filters
		add_filter( 'excerpt_length', 'avventure_excerpt_length' );
		add_filter( 'excerpt_more', 'avventure_excerpt_more' );

		// Comment form
		add_filter( 'comment_form_fields', 'avventure_comment_form_fields' );
		add_filter( 'comment_form_fields', 'avventure_comment_form_agree', 11 );

		// Add required meta tags in the head
		add_action( 'wp_head', 'avventure_wp_head', 0 );

		// Load current page/post customization (if present)
		add_action( 'wp_footer', 'avventure_wp_footer' );
		add_action( 'admin_footer', 'avventure_wp_footer' );

		// Enqueue scripts and styles for frontend
		add_action( 'wp_enqueue_scripts', 'avventure_wp_scripts', 1000 );            // priority 1000 - load styles
																						// before the plugin's support custom styles
																						// (with priority 1100)
																						// and child-theme styles
																						// (with priority 1200)
		add_action( 'wp_enqueue_scripts', 'avventure_wp_styles_child', 1200 );       // priority 1200 - load styles
																						// after the plugin's support custom styles
																						// (with priority 1100)
		add_action( 'wp_enqueue_scripts', 'avventure_wp_styles_responsive', 2000 );  // priority 2000 - load responsive
																						// after all other styles
		add_action( 'wp_footer', 'avventure_localize_scripts' );

		// Add body classes
		add_filter( 'body_class', 'avventure_add_body_classes' );

		// Register sidebars
		add_action( 'widgets_init', 'avventure_register_sidebars' );
	}
}


//-------------------------------------------------------
//-- Theme scripts and styles
//-------------------------------------------------------

// Load frontend scripts
if ( ! function_exists( 'avventure_wp_scripts' ) ) {
	//Handler of the add_action('wp_enqueue_scripts', 'avventure_wp_scripts', 1000);
	function avventure_wp_scripts() {

		// Enqueue styles
		//------------------------

		// Links to selected fonts
		$links = avventure_theme_fonts_links();
		if ( count( $links ) > 0 ) {
			foreach ( $links as $slug => $link ) {
				wp_enqueue_style( sprintf( 'avventure-font-%s', $slug ), $link, array(), null );
			}
		}

		// Font icons styles must be loaded before main stylesheet
		// This style NEED the theme prefix, because style 'fontello' in some plugin contain different set of characters
		// and can't be used instead this style!
		wp_enqueue_style( 'avventure-icons', avventure_get_file_url( 'css/font-icons/css/fontello-embedded.css' ), array(), null );

		// Load main stylesheet
		$main_stylesheet = AVVENTURE_THEME_URL . 'style.css';
		wp_enqueue_style( 'avventure-main', $main_stylesheet, array(), null );

		// Add custom bg image
		$bg_image = avventure_remove_protocol_from_url( avventure_get_theme_option( 'front_page_bg_image' ), false );
		if ( is_front_page() && ! empty( $bg_image ) && avventure_is_on( avventure_get_theme_option( 'front_page_enabled' ) ) ) {
			// Add custom bg image for the Front page
			wp_add_inline_style( 'avventure-main', 'body.frontpage, body.home-page { background-image:url(' . esc_url( $bg_image ) . ') !important }' );
		} else {
			// Add custom bg image for the body_style == 'boxed'
			$bg_image = avventure_get_theme_option( 'boxed_bg_image' );
			if ( avventure_get_theme_option( 'body_style' ) == 'boxed' && ! empty( $bg_image ) ) {
				wp_add_inline_style( 'avventure-main', '.body_style_boxed { background-image:url(' . esc_url( $bg_image ) . ') !important }' );
			}
		}

		// Custom colors, fonts and other rules
		if ( ! is_customize_preview() && ! isset( $_GET['color_scheme'] ) && avventure_is_off( avventure_get_theme_option( 'debug_mode' ) ) ) {
			wp_enqueue_style( 'avventure-custom', avventure_get_file_url( 'css/__custom.css' ), array(), null );
			if ( avventure_get_theme_setting( 'separate_schemes' ) ) {
				$schemes = avventure_get_sorted_schemes();
				if ( is_array( $schemes ) ) {
					foreach ( $schemes as $scheme => $data ) {
						wp_enqueue_style( "avventure-color-{$scheme}", avventure_get_file_url( "css/__colors_{$scheme}.css" ), array(), null );
					}
				}
			}
		} else {
			wp_add_inline_style( 'avventure-main', avventure_customizer_get_css() );
		}

		// Add post nav background
		avventure_add_bg_in_post_nav();

		// Enqueue scripts
		//------------------------

		// Modernizr will load in head before other scripts and styles
		$need_masonry = ( avventure_storage_get( 'blog_archive' ) === true
							&& in_array( substr( avventure_get_theme_option( 'blog_style' ), 0, 7 ), array( 'gallery', 'portfol', 'masonry' ) ) )
						|| ( is_single()
							&& str_replace( 'post-format-', '', get_post_format() ) == 'gallery' );
		if ( $need_masonry ) {
			wp_enqueue_script( 'modernizr', avventure_get_file_url( 'js/theme-gallery/modernizr.min.js' ), array(), null, false );
		}

		// Superfish Menu
		// Attention! To prevent duplicate this script in the plugin and in the menu, don't merge it!
		wp_enqueue_script( 'superfish', avventure_get_file_url( 'js/superfish/superfish.min.js' ), array( 'jquery' ), null, true );

		// Merged scripts
		if ( avventure_is_off( avventure_get_theme_option( 'debug_mode' ) ) ) {
			wp_enqueue_script( 'avventure-init', avventure_get_file_url( 'js/__scripts.js' ), array( 'jquery' ), null, true );
		} else {
			// Skip link focus
			wp_enqueue_script( 'skip-link-focus-fix', avventure_get_file_url( 'js/skip-link-focus-fix.js' ), null, true );
			// Background video
			$header_video = avventure_get_header_video();
			if ( ! empty( $header_video ) && ! avventure_is_inherit( $header_video ) ) {
				if ( avventure_is_youtube_url( $header_video ) ) {
					wp_enqueue_script( 'tubular', avventure_get_file_url( 'js/jquery.tubular.js' ), array( 'jquery' ), null, true );
				} else {
					wp_enqueue_script( 'bideo', avventure_get_file_url( 'js/bideo.js' ), array(), null, true );
				}
			}
			// Theme scripts
			wp_enqueue_script( 'avventure-utils', avventure_get_file_url( 'js/theme-utils.js' ), array( 'jquery' ), null, true );
			wp_enqueue_script( 'avventure-init', avventure_get_file_url( 'js/theme-init.js' ), array( 'jquery' ), null, true );
		}
		// Load scripts for 'Masonry' layout
		if ( $need_masonry ) {
			wp_enqueue_script( 'imagesloaded' );
			wp_enqueue_script( 'masonry' );
			wp_enqueue_script( 'classie', avventure_get_file_url( 'js/theme-gallery/classie.min.js' ), array(), null, true );
			wp_enqueue_script( 'avventure-gallery-script', avventure_get_file_url( 'js/theme-gallery/theme-gallery.js' ), array(), null, true );
		}
		// Load scripts for 'Portfolio' layout
		if ( avventure_storage_get( 'blog_archive' ) === true
				&& in_array( substr( avventure_get_theme_option( 'blog_style' ), 0, 7 ), array( 'gallery', 'portfol' ) )
				&& ! is_customize_preview() ) {
			wp_enqueue_script( 'jquery-ui-tabs', false, array( 'jquery', 'jquery-ui-core' ), null, true );
		}

		// Comments
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

		// Media elements library
		if ( avventure_get_theme_setting( 'use_mediaelements' ) ) {
			wp_enqueue_style( 'mediaelement' );
			wp_enqueue_style( 'wp-mediaelement' );
			wp_enqueue_script( 'mediaelement' );
			wp_enqueue_script( 'wp-mediaelement' );
		}
	}
}

// Load child-theme stylesheet (if different) after all styles (with priorities 1000 and 1100)
if ( ! function_exists( 'avventure_wp_styles_child' ) ) {
	//Handler of the add_action('wp_enqueue_scripts', 'avventure_wp_styles_child', 1200);
	function avventure_wp_styles_child() {
		$main_stylesheet  = AVVENTURE_THEME_URL . 'style.css';
		$child_stylesheet = AVVENTURE_CHILD_URL . 'style.css';
		if ( $child_stylesheet != $main_stylesheet ) {
			wp_enqueue_style( 'avventure-child', $child_stylesheet, array( 'avventure-main' ), null );
		}
	}
}

// Add variables to the scripts in the frontend
if ( ! function_exists( 'avventure_localize_scripts' ) ) {
	//Handler of the add_action('wp_footer', 'avventure_localize_scripts');
	function avventure_localize_scripts() {

		$video = avventure_get_header_video();

		wp_localize_script(
			'avventure-init', 'AVVENTURE_STORAGE', apply_filters(
				'avventure_filter_localize_script', array(
					// AJAX parameters
					'ajax_url'            => esc_url( admin_url( 'admin-ajax.php' ) ),
					'ajax_nonce'          => esc_attr( wp_create_nonce( admin_url( 'admin-ajax.php' ) ) ),

					// Site base url
					'site_url'            => get_home_url(),
					'theme_url'           => AVVENTURE_THEME_URL,

					// Site color scheme
					'site_scheme'         => sprintf( 'scheme_%s', avventure_get_theme_option( 'color_scheme' ) ),

					// User logged in
					'user_logged_in'      => is_user_logged_in() ? true : false,

					// Window width to switch the site header to the mobile layout
					'mobile_layout_width' => 767,
					'mobile_device'       => wp_is_mobile(),

					// Sidemenu options
					'menu_side_stretch'   => avventure_get_theme_option( 'menu_side_stretch' ) > 0 ? true : false,
					'menu_side_icons'     => avventure_get_theme_option( 'menu_side_icons' ) > 0 ? true : false,

					// Video background
					'background_video'    => avventure_is_from_uploads( $video ) ? $video : '',

					// Video and Audio tag wrapper
					'use_mediaelements'   => avventure_get_theme_setting( 'use_mediaelements' ) ? true : false,

					// Current mode
					'admin_mode'          => false,

					// Strings for translation
					'msg_ajax_error'      => esc_html__( 'Invalid server answer!', 'avventure' ),
				)
			)
		);
	}
}

// Load responsive styles (priority 2000 - load it after main styles and plugins custom styles)
if ( ! function_exists( 'avventure_wp_styles_responsive' ) ) {
	//Handler of the add_action('wp_enqueue_scripts', 'avventure_wp_styles_responsive', 2000);
	function avventure_wp_styles_responsive() {
		wp_enqueue_style( 'avventure-responsive', avventure_get_file_url( 'css/responsive.css' ), array(), null );
	}
}

//  Add meta tags and inline scripts in the header for frontend
if ( ! function_exists( 'avventure_wp_head' ) ) {
	//Handler of the add_action('wp_head',	'avventure_wp_head', 1);
	function avventure_wp_head() {
		?>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<meta name="format-detection" content="telephone=no">
		<link rel="profile" href="http://gmpg.org/xfn/11">
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
		<?php
	}
}

// Add theme specified classes to the body
if ( ! function_exists( 'avventure_add_body_classes' ) ) {
	//Handler of the add_filter( 'body_class', 'avventure_add_body_classes' );
	function avventure_add_body_classes( $classes ) {
		$classes[] = 'body_tag';    // Need for the .scheme_self
		$classes[] = 'scheme_' . esc_attr( avventure_get_theme_option( 'color_scheme' ) );

		$blog_mode = avventure_storage_get( 'blog_mode' );
		$classes[] = 'blog_mode_' . esc_attr( $blog_mode );
		$classes[] = 'body_style_' . esc_attr( avventure_get_theme_option( 'body_style' ) );

		if ( in_array( $blog_mode, array( 'post', 'page' ) ) ) {
			$classes[] = 'is_single';
		} else {
			$classes[] = ' is_stream';
			$classes[] = 'blog_style_' . esc_attr( avventure_get_theme_option( 'blog_style' ) );
			if ( avventure_storage_get( 'blog_template' ) > 0 ) {
				$classes[] = 'blog_template';
			}
		}

		if ( avventure_sidebar_present() ) {
			$classes[] = 'sidebar_show sidebar_' . esc_attr( avventure_get_theme_option( 'sidebar_position' ) );
		} else {
			$classes[] = 'sidebar_hide';
			if ( avventure_is_on( avventure_get_theme_option( 'expand_content' ) ) ) {
				$classes[] = 'expand_content';
			}
		}

		if ( avventure_is_on( avventure_get_theme_option( 'remove_margins' ) ) ) {
			$classes[] = 'remove_margins';
		}

		$bg_image = avventure_get_theme_option( 'front_page_bg_image' );
		if ( is_front_page() && avventure_is_on( avventure_get_theme_option( 'front_page_enabled' ) ) && ! empty( $bg_image ) ) {
			$classes[] = 'with_bg_image';
		}

		$classes[] = 'trx_addons_' . esc_attr( avventure_exists_trx_addons() ? 'present' : 'absent' );

		$classes[] = 'header_type_' . esc_attr( avventure_get_theme_option( 'header_type' ) );
		$classes[] = 'header_style_' . esc_attr( 'default' == avventure_get_theme_option( 'header_type' ) ? 'header-default' : avventure_get_theme_option( 'header_style' ) );
		$classes[] = 'header_position_' . esc_attr( avventure_get_theme_option( 'header_position' ) );

		$menu_style = avventure_get_theme_option( 'menu_style' );
		$classes[]  = 'menu_style_' . esc_attr( $menu_style ) . ( in_array( $menu_style, array( 'left', 'right' ) ) ? ' menu_style_side' : '' );
		$classes[]  = 'no_layout';

		return $classes;
	}
}

// Load current page/post customization (if present)
if ( ! function_exists( 'avventure_wp_footer' ) ) {
	//Handler of the add_action('wp_footer', 'avventure_wp_footer');
	//and add_action('admin_footer', 'avventure_wp_footer');
	function avventure_wp_footer() {
		// Add header zoom
		$header_zoom = max( 0.3, min( 2, (float) avventure_get_theme_option( 'header_zoom' ) ) );
		if ( 1 != $header_zoom ) {
			avventure_add_inline_css( ".sc_layouts_title_title{font-size:{$header_zoom}em}" );
		}
		// Add logo zoom
		$logo_zoom = max( 0.3, min( 2, (float) avventure_get_theme_option( 'logo_zoom' ) ) );
		if ( 1 != $logo_zoom ) {
			avventure_add_inline_css( ".custom-logo-link,.sc_layouts_logo{font-size:{$logo_zoom}em}" );
		}
		// Put inline styles to the output
		$css = avventure_get_inline_css();
		if ( ! empty( $css ) ) {
			wp_enqueue_style( 'avventure-inline-styles', avventure_get_file_url( 'css/__inline.css' ), array(), null );
			wp_add_inline_style( 'avventure-inline-styles', $css );
		}
	}
}


//-------------------------------------------------------
//-- Sidebars and widgets
//-------------------------------------------------------

// Register widgetized areas
if ( ! function_exists( 'avventure_register_sidebars' ) ) {
	// Handler of the add_action('widgets_init', 'avventure_register_sidebars');
	function avventure_register_sidebars() {
		$sidebars = avventure_get_sidebars();
		if ( is_array( $sidebars ) && count( $sidebars ) > 0 ) {
			$cnt = 0;
			foreach ( $sidebars as $id => $sb ) {
				$cnt++;
				register_sidebar(
					apply_filters( 'avventure_filter_register_sidebar',
						array(
							'name'          => $sb['name'],
							'description'   => $sb['description'],
							// Translators: Add the sidebar number to the id
							'id'            => ! empty( $id ) ? $id : sprintf( 'theme_sidebar_%d', $cnt),
							'before_widget' => '<aside id="%1$s" class="widget %2$s">',
							'after_widget'  => '</aside>',
							'before_title'  => '<h5 class="widget_title">',
							'after_title'   => '</h5>',
						)
					)
				);
			}
		}
	}
}

// Return theme specific widgetized areas
if ( ! function_exists( 'avventure_get_sidebars' ) ) {
	function avventure_get_sidebars() {
		$list = apply_filters(
			'avventure_filter_list_sidebars', array(
				'sidebar_widgets'       => array(
					'name'        => esc_html__( 'Sidebar Widgets', 'avventure' ),
					'description' => esc_html__( 'Widgets to be shown on the main sidebar', 'avventure' ),
				),
				'header_widgets'        => array(
					'name'        => esc_html__( 'Header Widgets', 'avventure' ),
					'description' => esc_html__( 'Widgets to be shown at the top of the page (in the page header area)', 'avventure' ),
				),
				'above_page_widgets'    => array(
					'name'        => esc_html__( 'Top Page Widgets', 'avventure' ),
					'description' => esc_html__( 'Widgets to be shown below the header, but above the content and sidebar', 'avventure' ),
				),
				'above_content_widgets' => array(
					'name'        => esc_html__( 'Above Content Widgets', 'avventure' ),
					'description' => esc_html__( 'Widgets to be shown above the content, near the sidebar', 'avventure' ),
				),
				'below_content_widgets' => array(
					'name'        => esc_html__( 'Below Content Widgets', 'avventure' ),
					'description' => esc_html__( 'Widgets to be shown below the content, near the sidebar', 'avventure' ),
				),
				'below_page_widgets'    => array(
					'name'        => esc_html__( 'Bottom Page Widgets', 'avventure' ),
					'description' => esc_html__( 'Widgets to be shown below the content and sidebar, but above the footer', 'avventure' ),
				),
				'footer_widgets'        => array(
					'name'        => esc_html__( 'Footer Widgets', 'avventure' ),
					'description' => esc_html__( 'Widgets to be shown at the bottom of the page (in the page footer area)', 'avventure' ),
				),
			)
		);
		return $list;
	}
}


//-------------------------------------------------------
//-- Theme fonts
//-------------------------------------------------------

// Return links for all theme fonts
if ( ! function_exists( 'avventure_theme_fonts_links' ) ) {
	function avventure_theme_fonts_links() {
		$links = array();

		/*
		Translators: If there are characters in your language that are not supported
		by chosen font(s), translate this to 'off'. Do not translate into your own language.
		*/
		$google_fonts_enabled = ( 'off' !== _x( 'on', 'Google fonts: on or off', 'avventure' ) );
		$custom_fonts_enabled = ( 'off' !== _x( 'on', 'Custom fonts (included in the theme): on or off', 'avventure' ) );

		if ( ( $google_fonts_enabled || $custom_fonts_enabled ) && ! avventure_storage_empty( 'load_fonts' ) ) {
			$load_fonts = avventure_storage_get( 'load_fonts' );
			if ( count( $load_fonts ) > 0 ) {
				$google_fonts = '';
				foreach ( $load_fonts as $font ) {
					$url = '';
					if ( $custom_fonts_enabled && empty( $font['styles'] ) ) {
						$slug = avventure_get_load_fonts_slug( $font['name'] );
						$url  = avventure_get_file_url( sprintf( 'css/font-face/%s/stylesheet.css', $slug ) );
						if ( ! empty( $url ) ) {
							$links[ $slug ] = $url;
						}
					}
					if ( $google_fonts_enabled && empty( $url ) ) {
						// Attention! Using '%7C' instead '|' damage loading second+ fonts
						$google_fonts .= ( $google_fonts ? '|' : '' )
										. str_replace( ' ', '+', $font['name'] )
										. ':'
										. ( empty( $font['styles'] ) ? '400,400italic,700,700italic' : $font['styles'] );
					}
				}
				if ( $google_fonts && $google_fonts_enabled ) {
					$links['google_fonts'] = sprintf( '%s://fonts.googleapis.com/css?family=%s&subset=%s', avventure_get_protocol(), $google_fonts, avventure_get_theme_option( 'load_fonts_subset' ) );
				}
			}
		}
		return $links;
	}
}

// Return links for WP Editor
if ( ! function_exists( 'avventure_theme_fonts_for_editor' ) ) {
	function avventure_theme_fonts_for_editor() {
		$links = array_values( avventure_theme_fonts_links() );
		if ( is_array( $links ) && count( $links ) > 0 ) {
			for ( $i = 0; $i < count( $links ); $i++ ) {
				$links[ $i ] = str_replace( ',', '%2C', $links[ $i ] );
			}
		}
		return $links;
	}
}


//-------------------------------------------------------
//-- The Excerpt
//-------------------------------------------------------
if ( ! function_exists( 'avventure_excerpt_length' ) ) {
	function avventure_excerpt_length( $length ) {
		return max( 1, avventure_get_theme_option( 'excerpt_length' ) );
	}
}

if ( ! function_exists( 'avventure_excerpt_more' ) ) {
	function avventure_excerpt_more( $more ) {
		return '&hellip;';
	}
}


//-------------------------------------------------------
//-- Comments
//-------------------------------------------------------

// Comment form fields order
if ( ! function_exists( 'avventure_comment_form_fields' ) ) {
	// Handler of the add_filter('comment_form_fields', 'avventure_comment_form_fields');
	function avventure_comment_form_fields( $comment_fields ) {
		if ( avventure_get_theme_setting( 'comment_after_name' ) ) {
			$keys = array_keys( $comment_fields );
			if ( 'comment' == $keys[0] ) {
				$comment_fields['comment'] = array_shift( $comment_fields );
			}
		}
		return $comment_fields;
	}
}

// Add checkbox with "I agree ..."
if ( ! function_exists( 'avventure_comment_form_agree' ) ) {
	// Handler of the add_filter('comment_form_fields', 'avventure_comment_form_agree', 11);
	function avventure_comment_form_agree( $comment_fields ) {
		$privacy_text = avventure_get_privacy_text();
		if ( ! empty( $privacy_text ) ) {
			$comment_fields['i_agree_privacy_policy'] = avventure_single_comments_field(
				array(
					'form_style'        => 'default',
					'field_type'        => 'checkbox',
					'field_req'         => '',
					'field_icon'        => '',
					'field_value'       => '1',
					'field_name'        => 'i_agree_privacy_policy',
					'field_title'       => $privacy_text,
				)
			);
		}
		return $comment_fields;
	}
}



//-------------------------------------------------------
//-- Thumb sizes
//-------------------------------------------------------
if ( ! function_exists( 'avventure_theme_thumbs_sizes' ) ) {
	//Handler of the add_filter( 'image_size_names_choose', 'avventure_theme_thumbs_sizes' );
	function avventure_theme_thumbs_sizes( $sizes ) {
		$thumb_sizes = avventure_storage_get( 'theme_thumbs' );
		$mult        = avventure_get_theme_option( 'retina_ready', 1 );
		foreach ( $thumb_sizes as $k => $v ) {
			$sizes[ $k ] = $v['title'];
			if ( $mult > 1 ) {
				$sizes[ $k . '-@retina' ] = $v['title'] . ' ' . esc_html__( '@2x', 'avventure' );
			}
		}
		return $sizes;
	}
}



//-------------------------------------------------------
//-- Include theme (or child) PHP-files
//-------------------------------------------------------

require_once AVVENTURE_THEME_DIR . 'includes/utils.php';
require_once AVVENTURE_THEME_DIR . 'includes/storage.php';

require_once AVVENTURE_THEME_DIR . 'includes/lists.php';
require_once AVVENTURE_THEME_DIR . 'includes/wp.php';

if ( is_admin() ) {
	require_once AVVENTURE_THEME_DIR . 'includes/tgmpa/class-tgm-plugin-activation.php';
	require_once AVVENTURE_THEME_DIR . 'includes/admin.php';
}

require_once AVVENTURE_THEME_DIR . 'theme-options/theme-customizer.php';

require_once AVVENTURE_THEME_DIR . 'front-page/front-page-options.php';

require_once AVVENTURE_THEME_DIR . 'theme-specific/theme-tags.php';
require_once AVVENTURE_THEME_DIR . 'theme-specific/theme-hovers/theme-hovers.php';
require_once AVVENTURE_THEME_DIR . 'theme-specific/theme-about/theme-about.php';

// Free themes support
if ( AVVENTURE_THEME_FREE ) {
	require_once AVVENTURE_THEME_DIR . 'theme-specific/theme-about/theme-upgrade.php';
}

// Plugins support
$avventure_required_plugins = avventure_storage_get( 'required_plugins' );
if ( is_array( $avventure_required_plugins ) ) {
	foreach ( $avventure_required_plugins as $plugin_slug => $plugin_name ) {
		$plugin_slug = avventure_esc( $plugin_slug );
		$plugin_path = AVVENTURE_THEME_DIR . sprintf( 'plugins/%s/%s.php', $plugin_slug, $plugin_slug );
		if ( file_exists( $plugin_path ) ) {
			require_once $plugin_path; }
	}
}


// Theme skins support
if ( defined( 'AVVENTURE_ALLOW_SKINS' ) && AVVENTURE_ALLOW_SKINS && file_exists( AVVENTURE_THEME_DIR . 'skins/skins.php' ) ) {
	require_once AVVENTURE_THEME_DIR . 'skins/skins.php';
}