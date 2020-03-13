<?php
/* Woocommerce support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 1 - register filters, that add/remove lists items for the Theme Options
if ( ! function_exists( 'avventure_woocommerce_theme_setup1' ) ) {
	add_action( 'after_setup_theme', 'avventure_woocommerce_theme_setup1', 1 );
	function avventure_woocommerce_theme_setup1() {

		// Theme-specific parameters for WooCommerce
		add_theme_support(
			'woocommerce', array(
				// Image width for thumbnails gallery
				'gallery_thumbnail_image_width' => 150,

												// Image width for the catalog images
												// Attention! If you set this parameter - WooCommerce hide relative control from Customizer
												//'thumbnail_image_width' => 300,

												// Image width for the single product image
												// Attention! If you set this parameter - WooCommerce hide relative control from Customizer
												//'single_image_width' => 600
			)
		);

		// Next setting from the WooCommerce 3.0+ enable built-in image zoom on the single product page
		add_theme_support( 'wc-product-gallery-zoom' );

		// Next setting from the WooCommerce 3.0+ enable built-in image slider on the single product page
		add_theme_support( 'wc-product-gallery-slider' );

		// Next setting from the WooCommerce 3.0+ enable built-in image lightbox on the single product page
		add_theme_support( 'wc-product-gallery-lightbox' );

		add_filter( 'avventure_filter_list_sidebars', 'avventure_woocommerce_list_sidebars' );
		add_filter( 'avventure_filter_list_posts_types', 'avventure_woocommerce_list_post_types' );

		// Detect if WooCommerce support 'Product Grid' feature
		$product_grid = avventure_exists_woocommerce() && function_exists( 'wc_get_theme_support' ) ? wc_get_theme_support( 'product_grid' ) : false;
		add_theme_support( 'wc-product-grid-enable', isset( $product_grid['min_columns'] ) && isset( $product_grid['max_columns'] ) );
	}
}

// Theme init priorities:
// 3 - add/remove Theme Options elements
if ( ! function_exists( 'avventure_woocommerce_theme_setup3' ) ) {
	add_action( 'after_setup_theme', 'avventure_woocommerce_theme_setup3', 3 );
	function avventure_woocommerce_theme_setup3() {
		if ( avventure_exists_woocommerce() ) {

			// Section 'WooCommerce'
			avventure_storage_set_array_before(
				'options', 'fonts', array_merge(
					array(
						'shop'               => array(
							'title'      => esc_html__( 'Shop', 'avventure' ),
							'desc'       => wp_kses_data( __( 'Select theme-specific parameters to display the shop pages', 'avventure' ) ),
							'priority'   => 80,
							'expand_url' => esc_url( avventure_woocommerce_get_shop_page_link() ),
							'type'       => 'section',
						),

						'products_info_shop' => array(
							'title'  => esc_html__( 'Products list', 'avventure' ),
							'desc'   => '',
							'qsetup' => esc_html__( 'General', 'avventure' ),
							'type'   => 'info',
						),
						'shop_mode'          => array(
							'title'   => esc_html__( 'Shop style', 'avventure' ),
							'desc'    => wp_kses_data( __( 'Select style for the products list. Attention! If the visitor has already selected the list type at the top of the page - his choice is remembered and has priority over this option', 'avventure' ) ),
							'std'     => 'thumbs',
							'options' => array(
								'thumbs' => esc_html__( 'Grid', 'avventure' ),
								'list'   => esc_html__( 'List', 'avventure' ),
							),
							'qsetup'  => esc_html__( 'General', 'avventure' ),
							'type'    => 'select',
						),
					),
					! get_theme_support( 'wc-product-grid-enable' )
					? array(
						'posts_per_page_shop' => array(
							'title' => esc_html__( 'Products per page', 'avventure' ),
							'desc'  => wp_kses_data( __( 'How many products should be displayed on the shop page. If empty - use global value from the menu Settings - Reading', 'avventure' ) ),
							'std'   => '',
							'type'  => 'text',
						),
						'blog_columns_shop'   => array(
							'title'      => esc_html__( 'Grid columns', 'avventure' ),
							'desc'       => wp_kses_data( __( 'How many columns should be used for the shop products in the grid view (from 2 to 4)?', 'avventure' ) ),
							'dependency' => array(
								'shop_mode' => array( 'thumbs' ),
							),
							'std'        => 2,
							'options'    => avventure_get_list_range( 2, 4 ),
							'type'       => 'select',
						),
					)
					: array(),
					array(
						'shop_hover'              => array(
							'title'   => esc_html__( 'Hover style', 'avventure' ),
							'desc'    => wp_kses_data( __( 'Hover style on the products in the shop archive', 'avventure' ) ),
							'std'     => 'shop',
							'options' => apply_filters(
								'avventure_filter_shop_hover', array(
									'none'         => esc_html__( 'None', 'avventure' ),
									'shop'         => esc_html__( 'Icons', 'avventure' ),
									'shop_buttons' => esc_html__( 'Buttons', 'avventure' ),
								)
							),
							'qsetup'  => esc_html__( 'General', 'avventure' ),
							'type'    => 'select',
						),

						'single_info_shop'        => array(
							'title' => esc_html__( 'Single product', 'avventure' ),
							'desc'  => '',
							'type'  => 'info',
						),
						'stretch_tabs_area'       => array(
							'title' => esc_html__( 'Stretch tabs area', 'avventure' ),
							'desc'  => wp_kses_data( __( 'Stretch area with tabs on the single product to the screen width if the sidebar is hidden', 'avventure' ) ),
							'std'   => 1,
							'type'  => 'checkbox',
						),
						'show_related_posts_shop' => array(
							'title'  => esc_html__( 'Show related products', 'avventure' ),
							'desc'   => wp_kses_data( __( "Show section 'Related products' on the single product page", 'avventure' ) ),
							'std'    => 1,
							'type'   => 'checkbox',
						),
						'related_posts_shop'      => array(
							'title'      => esc_html__( 'Related products', 'avventure' ),
							'desc'       => wp_kses_data( __( 'How many related products should be displayed on the single product page?', 'avventure' ) ),
							'dependency' => array(
								'show_related_posts_shop' => array( 1 ),
							),
							'std'        => 3,
							'options'    => avventure_get_list_range( 1, 9 ),
							'type'       => 'select',
						),
						'related_columns_shop'    => array(
							'title'      => esc_html__( 'Related columns', 'avventure' ),
							'desc'       => wp_kses_data( __( 'How many columns should be used to output related products on the single product page?', 'avventure' ) ),
							'dependency' => array(
								'show_related_posts_shop' => array( 1 ),
							),
							'std'        => 3,
							'options'    => avventure_get_list_range( 1, 4 ),
							'type'       => 'select',
						),
					),
					avventure_options_get_list_cpt_options( 'shop' )
				)
			);
		}
	}
}


// Move section 'Shop' inside the section 'WooCommerce' in the Customizer (if WooCommerce 3.3+ is installed)
if ( ! function_exists( 'avventure_woocommerce_customizer_register_controls' ) ) {
	add_action( 'customize_register', 'avventure_woocommerce_customizer_register_controls', 100 );
	function avventure_woocommerce_customizer_register_controls( $wp_customize ) {
		if ( avventure_exists_woocommerce() ) {
			$panel = $wp_customize->get_panel( 'woocommerce' );
			$sec   = $wp_customize->get_section( 'shop' );
			if ( is_object( $panel ) && is_object( $sec ) ) {
				$sec->panel    = 'woocommerce';
				$sec->title    = esc_html__( 'Theme-specific options', 'avventure' );
				$sec->priority = 100;
			}
		}
	}
}

// Set theme-specific default columns number in the new WooCommerce after switch theme
if ( ! function_exists( 'avventure_woocommerce_action_switch_theme' ) ) {
	add_action( 'after_switch_theme', 'avventure_woocommerce_action_switch_theme' );
	function avventure_woocommerce_action_switch_theme() {
		if ( avventure_exists_woocommerce() ) {
			update_option( 'woocommerce_catalog_columns', apply_filters( 'avventure_filter_woocommerce_columns', 3 ) );
		}
	}
}

// Set theme-specific default columns number in the new WooCommerce after plugin activation
if ( ! function_exists( 'avventure_woocommerce_action_activated_plugin' ) ) {
	add_action( 'activated_plugin', 'avventure_woocommerce_action_activated_plugin', 10, 2 );
	function avventure_woocommerce_action_activated_plugin( $plugin, $network_activation ) {
		if ( 'woocommerce/woocommerce.php' == $plugin ) {
			update_option( 'woocommerce_catalog_columns', apply_filters( 'avventure_filter_woocommerce_columns', 3 ) );
		}
	}
}


// Add section 'Products' to the Front Page option
if ( ! function_exists( 'avventure_woocommerce_front_page_options' ) ) {
	if ( ! AVVENTURE_THEME_FREE ) {
		add_filter( 'avventure_filter_front_page_options', 'avventure_woocommerce_front_page_options' );
	}
	function avventure_woocommerce_front_page_options( $options ) {
		if ( avventure_exists_woocommerce() ) {

			$options['front_page_sections']['std']    .= ( ! empty( $options['front_page_sections']['std'] ) ? '|' : '' ) . 'woocommerce=1';
			$options['front_page_sections']['options'] = array_merge(
				$options['front_page_sections']['options'],
				array(
					'woocommerce' => esc_html__( 'Products', 'avventure' ),
				)
			);
			$options                                   = array_merge(
				$options, array(

					// Front Page Sections - WooCommerce
					'front_page_woocommerce'               => array(
						'title'    => esc_html__( 'Products', 'avventure' ),
						'desc'     => '',
						'priority' => 200,
						'type'     => 'section',
					),
					'front_page_woocommerce_layout_info'   => array(
						'title' => esc_html__( 'Layout', 'avventure' ),
						'desc'  => '',
						'type'  => 'info',
					),
					'front_page_woocommerce_fullheight'    => array(
						'title'   => esc_html__( 'Full height', 'avventure' ),
						'desc'    => wp_kses_data( __( 'Stretch this section to the window height', 'avventure' ) ),
						'std'     => 0,
						'refresh' => false,
						'type'    => 'checkbox',
					),
					'front_page_woocommerce_paddings'      => array(
						'title'   => esc_html__( 'Paddings', 'avventure' ),
						'desc'    => wp_kses_data( __( 'Select paddings inside this section', 'avventure' ) ),
						'std'     => 'medium',
						'options' => avventure_get_list_paddings(),
						'refresh' => false,
						'type'    => 'switch',
					),
					'front_page_woocommerce_heading_info'  => array(
						'title' => esc_html__( 'Title', 'avventure' ),
						'desc'  => '',
						'type'  => 'info',
					),
					'front_page_woocommerce_caption'       => array(
						'title'   => esc_html__( 'Section title', 'avventure' ),
						'desc'    => '',
						'refresh' => false, //'.front_page_section_woocommerce .front_page_section_woocommerce_caption',
						'std'     => wp_kses_data( __( 'This text can be changed in the section "Products"', 'avventure' ) ),
						'type'    => 'text',
					),
					'front_page_woocommerce_description'   => array(
						'title'   => esc_html__( 'Description', 'avventure' ),
						'desc'    => wp_kses_data( __( "Short description after the section's title", 'avventure' ) ),
						'refresh' => false, //'.front_page_section_woocommerce .front_page_section_woocommerce_description',
						'std'     => wp_kses_data( __( 'This text can be changed in the section "Products"', 'avventure' ) ),
						'type'    => 'textarea',
					),
					'front_page_woocommerce_products_info' => array(
						'title' => esc_html__( 'Products parameters', 'avventure' ),
						'desc'  => '',
						'type'  => 'info',
					),
					'front_page_woocommerce_products'      => array(
						'title'   => esc_html__( 'Type of the products', 'avventure' ),
						'desc'    => '',
						'std'     => 'products',
						'options' => array(
							'recent_products'       => esc_html__( 'Recent products', 'avventure' ),
							'featured_products'     => esc_html__( 'Featured products', 'avventure' ),
							'top_rated_products'    => esc_html__( 'Top rated products', 'avventure' ),
							'sale_products'         => esc_html__( 'Sale products', 'avventure' ),
							'best_selling_products' => esc_html__( 'Best selling products', 'avventure' ),
							'product_category'      => esc_html__( 'Products from categories', 'avventure' ),
							'products'              => esc_html__( 'Products by IDs', 'avventure' ),
						),
						'type'    => 'select',
					),
					'front_page_woocommerce_products_categories' => array(
						'title'      => esc_html__( 'Categories', 'avventure' ),
						'desc'       => esc_html__( 'Comma separated category slugs. Used only with "Products from categories"', 'avventure' ),
						'dependency' => array(
							'front_page_woocommerce_products' => array( 'product_category' ),
						),
						'std'        => '',
						'type'       => 'text',
					),
					'front_page_woocommerce_products_per_page' => array(
						'title' => esc_html__( 'Per page', 'avventure' ),
						'desc'  => wp_kses_data( __( 'How many products will be displayed on the page. Attention! For "Products by IDs" specify comma separated list of the IDs', 'avventure' ) ),
						'std'   => 3,
						'type'  => 'text',
					),
					'front_page_woocommerce_products_columns' => array(
						'title' => esc_html__( 'Columns', 'avventure' ),
						'desc'  => wp_kses_data( __( 'How many columns will be used', 'avventure' ) ),
						'std'   => 3,
						'type'  => 'text',
					),
					'front_page_woocommerce_products_orderby' => array(
						'title'   => esc_html__( 'Order by', 'avventure' ),
						'desc'    => wp_kses_data( __( 'Not used with Best selling products', 'avventure' ) ),
						'std'     => 'date',
						'options' => array(
							'date'  => esc_html__( 'Date', 'avventure' ),
							'title' => esc_html__( 'Title', 'avventure' ),
						),
						'type'    => 'switch',
					),
					'front_page_woocommerce_products_order' => array(
						'title'   => esc_html__( 'Order', 'avventure' ),
						'desc'    => wp_kses_data( __( 'Not used with Best selling products', 'avventure' ) ),
						'std'     => 'desc',
						'options' => array(
							'asc'  => esc_html__( 'Ascending', 'avventure' ),
							'desc' => esc_html__( 'Descending', 'avventure' ),
						),
						'type'    => 'switch',
					),
					'front_page_woocommerce_color_info'    => array(
						'title' => esc_html__( 'Colors and images', 'avventure' ),
						'desc'  => '',
						'type'  => 'info',
					),
					'front_page_woocommerce_scheme'        => array(
						'title'   => esc_html__( 'Color scheme', 'avventure' ),
						'desc'    => wp_kses_data( __( 'Color scheme for this section', 'avventure' ) ),
						'std'     => 'inherit',
						'options' => array(),
						'refresh' => false,
						'type'    => 'switch',
					),
					'front_page_woocommerce_bg_image'      => array(
						'title'           => esc_html__( 'Background image', 'avventure' ),
						'desc'            => wp_kses_data( __( 'Select or upload background image for this section', 'avventure' ) ),
						'refresh'         => '.front_page_section_woocommerce',
						'refresh_wrapper' => true,
						'std'             => '',
						'type'            => 'image',
					),
					'front_page_woocommerce_bg_color_type'  => array(
						'title'   => esc_html__( 'Background color', 'avventure' ),
						'desc'    => wp_kses_data( __( 'Background color for this section', 'avventure' ) ),
						'std'     => AVVENTURE_THEME_FREE ? 'custom' : 'none',
						'refresh' => false,
						'options' => array(
							'none'            => esc_html__( 'None', 'avventure' ),
							'scheme_bg_color' => esc_html__( 'Scheme bg color', 'avventure' ),
							'custom'          => esc_html__( 'Custom', 'avventure' ),
						),
						'type'    => 'switch',
					),
					'front_page_woocommerce_bg_color'       => array(
						'title'      => esc_html__( 'Custom color', 'avventure' ),
						'desc'       => wp_kses_data( __( 'Custom background color for this section', 'avventure' ) ),
						'std'        => AVVENTURE_THEME_FREE ? '#000' : '',
						'refresh'    => false,
						'dependency' => array(
							'front_page_woocommerce_bg_color_type' => array( 'custom' ),
						),
						'type'       => 'color',
					),
					'front_page_woocommerce_bg_mask'       => array(
						'title'   => esc_html__( 'Background mask', 'avventure' ),
						'desc'    => wp_kses_data( __( 'Use Background color as section mask with specified opacity. If 0 - mask is not used', 'avventure' ) ),
						'std'     => 1,
						'max'     => 1,
						'step'    => 0.1,
						'refresh' => false,
						'type'    => 'slider',
					),
					'front_page_woocommerce_anchor_info'   => array(
						'title' => esc_html__( 'Anchor', 'avventure' ),
						'desc'  => wp_kses_data( __( 'You can select icon and/or specify a text to create anchor for this section and show it in the side menu (if selected in the section "Header - Menu".', 'avventure' ) )
									. '<br>'
									. wp_kses_data( __( 'Attention! Anchors available only if plugin "ThemeREX Addons is installed and activated!', 'avventure' ) ),
						'type'  => 'info',
					),
					'front_page_woocommerce_anchor_icon'   => array(
						'title' => esc_html__( 'Anchor icon', 'avventure' ),
						'desc'  => '',
						'std'   => '',
						'type'  => 'icon',
					),
					'front_page_woocommerce_anchor_text'   => array(
						'title' => esc_html__( 'Anchor text', 'avventure' ),
						'desc'  => '',
						'std'   => '',
						'type'  => 'text',
					),
				)
			);
		}
		return $options;
	}
}

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'avventure_woocommerce_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'avventure_woocommerce_theme_setup9', 9 );
	function avventure_woocommerce_theme_setup9() {

		add_filter( 'avventure_filter_merge_styles', 'avventure_woocommerce_merge_styles' );
		add_filter( 'avventure_filter_merge_styles_responsive', 'avventure_woocommerce_merge_styles_responsive' );
		add_filter( 'avventure_filter_merge_scripts', 'avventure_woocommerce_merge_scripts' );

		if ( avventure_exists_woocommerce() ) {
			add_action( 'wp_enqueue_scripts', 'avventure_woocommerce_frontend_scripts', 1100 );
			add_filter( 'avventure_filter_get_post_info', 'avventure_woocommerce_get_post_info' );
			add_filter( 'avventure_filter_post_type_taxonomy', 'avventure_woocommerce_post_type_taxonomy', 10, 2 );
			add_action( 'avventure_action_override_theme_options', 'avventure_woocommerce_override_theme_options' );
			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
			if ( ! is_admin() ) {
				add_filter( 'avventure_filter_detect_blog_mode', 'avventure_woocommerce_detect_blog_mode' );
				add_filter( 'avventure_filter_get_post_categories', 'avventure_woocommerce_get_post_categories' );
				add_filter( 'avventure_filter_allow_override_header_image', 'avventure_woocommerce_allow_override_header_image' );
				add_filter( 'avventure_filter_get_blog_title', 'avventure_woocommerce_get_blog_title' );
				add_action( 'avventure_action_before_post_meta', 'avventure_woocommerce_action_before_post_meta' );
				add_action( 'pre_get_posts', 'avventure_woocommerce_pre_get_posts' );
				add_filter( 'avventure_filter_localize_script', 'avventure_woocommerce_localize_script' );
			}
		}
		if ( is_admin() ) {
			add_filter( 'avventure_filter_tgmpa_required_plugins', 'avventure_woocommerce_tgmpa_required_plugins' );
		}

		// Add wrappers and classes to the standard WooCommerce output
		if ( avventure_exists_woocommerce() ) {

			// Remove WOOC sidebar
			remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

			// Remove link around product item
			remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );

			// Remove add_to_cart button
			//remove_action('woocommerce_after_shop_loop_item',			'woocommerce_template_loop_add_to_cart', 10);

			// Remove link around product category
			remove_action( 'woocommerce_before_subcategory', 'woocommerce_template_loop_category_link_open', 10 );
			remove_action( 'woocommerce_after_subcategory', 'woocommerce_template_loop_category_link_close', 10 );

			// Open main content wrapper - <article>
			remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
			add_action( 'woocommerce_before_main_content', 'avventure_woocommerce_wrapper_start', 10 );
			// Close main content wrapper - </article>
			remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
			add_action( 'woocommerce_after_main_content', 'avventure_woocommerce_wrapper_end', 10 );

			// Close header section
			add_action( 'woocommerce_after_main_content', 'avventure_woocommerce_archive_description', 1 );
			add_action( 'woocommerce_before_shop_loop', 'avventure_woocommerce_archive_description', 5 );
			add_action( 'woocommerce_no_products_found', 'avventure_woocommerce_archive_description', 5 );

			// Add theme specific search form
			add_filter( 'get_product_search_form', 'avventure_woocommerce_get_product_search_form' );

			// Change text on 'Add to cart' button
			add_filter( 'woocommerce_product_add_to_cart_text', 'avventure_woocommerce_add_to_cart_text' );
			add_filter( 'woocommerce_product_single_add_to_cart_text', 'avventure_woocommerce_add_to_cart_text' );

			// Wrap 'Add to cart' button
			add_filter( 'woocommerce_loop_add_to_cart_link', 'avventure_woocommerce_add_to_cart_link', 10, 3 );

			// Add list mode buttons
			add_action( 'woocommerce_before_shop_loop', 'avventure_woocommerce_before_shop_loop', 10 );

			// Show 'No products' if no search results and display mode 'both'
			add_action( 'woocommerce_after_shop_loop', 'avventure_woocommerce_after_shop_loop', 1 );

			// Set columns number for the products loop
			if ( ! get_theme_support( 'wc-product-grid-enable' ) ) {
				add_filter( 'loop_shop_columns', 'avventure_woocommerce_loop_shop_columns' );
				add_filter( 'post_class', 'avventure_woocommerce_loop_shop_columns_class' );
				add_filter( 'product_cat_class', 'avventure_woocommerce_loop_shop_columns_class', 10, 3 );
			}
			// Open product/category item wrapper
			add_action( 'woocommerce_before_subcategory_title', 'avventure_woocommerce_item_wrapper_start', 9 );
			add_action( 'woocommerce_before_shop_loop_item_title', 'avventure_woocommerce_item_wrapper_start', 9 );
			// Close featured image wrapper and open title wrapper
			add_action( 'woocommerce_before_subcategory_title', 'avventure_woocommerce_title_wrapper_start', 20 );
			add_action( 'woocommerce_before_shop_loop_item_title', 'avventure_woocommerce_title_wrapper_start', 20 );

			// Add tags before title
			add_action( 'woocommerce_before_shop_loop_item_title', 'avventure_woocommerce_title_tags', 30 );

			// Wrap product title to the link
			add_action( 'the_title', 'avventure_woocommerce_the_title' );
			// Wrap category title to the link
			// Old way: before WooCommerce 3.2.2
			//add_filter(	'woocommerce_shop_loop_subcategory_title',  'avventure_woocommerce_shop_loop_subcategory_title', 9, 1);
			// New way: WooCommerce 3.2.2+
			add_action( 'woocommerce_before_subcategory_title', 'avventure_woocommerce_before_subcategory_title', 22, 1 );
			add_action( 'woocommerce_after_subcategory_title', 'avventure_woocommerce_after_subcategory_title', 2, 1 );

			// Close title wrapper and add description in the list mode
			add_action( 'woocommerce_after_shop_loop_item_title', 'avventure_woocommerce_title_wrapper_end', 7 );
			add_action( 'woocommerce_after_subcategory_title', 'avventure_woocommerce_title_wrapper_end2', 10 );
			// Close product/category item wrapper
			add_action( 'woocommerce_after_subcategory', 'avventure_woocommerce_item_wrapper_end', 20 );
			add_action( 'woocommerce_after_shop_loop_item', 'avventure_woocommerce_item_wrapper_end', 20 );

			// Add product ID into product meta section (after categories and tags)
			add_action( 'woocommerce_product_meta_end', 'avventure_woocommerce_show_product_id', 10 );

			// Set columns number for the product's thumbnails
			add_filter( 'woocommerce_product_thumbnails_columns', 'avventure_woocommerce_product_thumbnails_columns' );

			// Wrap price (WooCommerce use priority 10 to output price)
			add_action( 'woocommerce_after_shop_loop_item_title', 'avventure_woocommerce_price_wrapper_start', 9 );
			add_action( 'woocommerce_after_shop_loop_item_title', 'avventure_woocommerce_price_wrapper_end', 11 );

			// Decorate price
			add_filter( 'woocommerce_get_price_html', 'avventure_woocommerce_get_price_html' );

			// Add 'Out of stock' label
			add_action( 'avventure_action_woocommerce_item_featured_link_start', 'avventure_woocommerce_add_out_of_stock_label' );

			// Detect current shop mode
			if ( ! is_admin() ) {
				$shop_mode = avventure_get_value_gpc( 'avventure_shop_mode' );
				if ( empty( $shop_mode ) && avventure_check_theme_option( 'shop_mode' ) ) {
					$shop_mode = avventure_get_theme_option( 'shop_mode' );
				}
				if ( empty( $shop_mode ) ) {
					$shop_mode = 'thumbs';
				}
				avventure_storage_set( 'shop_mode', $shop_mode );
			}
		}
	}
}

// Theme init priorities:
// Action 'wp'
// 1 - detect override mode. Attention! Only after this step you can use overriden options (separate values for the shop, courses, etc.)
if ( ! function_exists( 'avventure_woocommerce_theme_setup_wp' ) ) {
	add_action( 'wp', 'avventure_woocommerce_theme_setup_wp' );
	function avventure_woocommerce_theme_setup_wp() {
		if ( avventure_exists_woocommerce() ) {
			// Set columns number for the related products
			if ( (int) avventure_get_theme_option( 'show_related_posts' ) == 0 || (int) avventure_get_theme_option( 'related_posts' ) == 0 ) {
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
			} else {
				add_filter( 'woocommerce_output_related_products_args', 'avventure_woocommerce_output_related_products_args' );
				add_filter( 'woocommerce_related_products_columns', 'avventure_woocommerce_related_products_columns' );
			}
		}
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'avventure_woocommerce_tgmpa_required_plugins' ) ) {
	//Handler of the add_filter('avventure_filter_tgmpa_required_plugins',	'avventure_woocommerce_tgmpa_required_plugins');
	function avventure_woocommerce_tgmpa_required_plugins( $list = array() ) {
		if ( avventure_storage_isset( 'required_plugins', 'woocommerce' ) ) {
			$list[] = array(
				'name'     => avventure_storage_get_array( 'required_plugins', 'woocommerce' ),
				'slug'     => 'woocommerce',
				'required' => false,
			);
		}
		return $list;
	}
}


// Check if WooCommerce installed and activated
if ( ! function_exists( 'avventure_exists_woocommerce' ) ) {
	function avventure_exists_woocommerce() {
		return class_exists( 'Woocommerce' );
		//return function_exists('is_woocommerce');
	}
}

// Return true, if current page is any woocommerce page
if ( ! function_exists( 'avventure_is_woocommerce_page' ) ) {
	function avventure_is_woocommerce_page() {
		$rez = false;
		if ( avventure_exists_woocommerce() ) {
			$rez = is_woocommerce() || is_shop() || is_product() || is_product_category() || is_product_tag() || is_product_taxonomy() || is_cart() || is_checkout() || is_account_page();
		}
		return $rez;
	}
}

// Detect current blog mode
if ( ! function_exists( 'avventure_woocommerce_detect_blog_mode' ) ) {
	//Handler of the add_filter( 'avventure_filter_detect_blog_mode', 'avventure_woocommerce_detect_blog_mode' );
	function avventure_woocommerce_detect_blog_mode( $mode = '' ) {
		if ( is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy() ) {
			$mode = 'shop';
		} elseif ( is_product() || is_cart() || is_checkout() || is_account_page() ) {
			$mode = 'shop'; //'shop_single';
		}
		return $mode;
	}
}

// Override options with stored page meta on 'Shop' pages
if ( ! function_exists( 'avventure_woocommerce_override_theme_options' ) ) {
	//Handler of the add_action( 'avventure_action_override_theme_options', 'avventure_woocommerce_override_theme_options');
	function avventure_woocommerce_override_theme_options() {
		// Remove ' || is_product()' from the condition in the next row
		// if you don't need to override theme options from the page 'Shop' on single products
		if ( is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy() || is_product() ) {
			$id = avventure_woocommerce_get_shop_page_id();
			if ( 0 < $id ) {
				avventure_storage_set( 'options_meta', get_post_meta( $id, 'avventure_options', true ) );
			}
		}
	}
}

// Return current page title
if ( ! function_exists( 'avventure_woocommerce_get_blog_title' ) ) {
	//Handler of the add_filter( 'avventure_filter_get_blog_title', 'avventure_woocommerce_get_blog_title');
	function avventure_woocommerce_get_blog_title( $title = '' ) {
		if ( ! avventure_exists_trx_addons() && avventure_exists_woocommerce() && avventure_is_woocommerce_page() && is_shop() ) {
			$id    = avventure_woocommerce_get_shop_page_id();
			$title = $id ? get_the_title( $id ) : esc_html__( 'Shop', 'avventure' );
		}
		return $title;
	}
}


// Return taxonomy for current post type
if ( ! function_exists( 'avventure_woocommerce_post_type_taxonomy' ) ) {
	//Handler of the add_filter( 'avventure_filter_post_type_taxonomy',	'avventure_woocommerce_post_type_taxonomy', 10, 2 );
	function avventure_woocommerce_post_type_taxonomy( $tax = '', $post_type = '' ) {
		if ( 'product' == $post_type ) {
			$tax = 'product_cat';
		}
		return $tax;
	}
}

// Return true if page title section is allowed
if ( ! function_exists( 'avventure_woocommerce_allow_override_header_image' ) ) {
	//Handler of the add_filter( 'avventure_filter_allow_override_header_image', 'avventure_woocommerce_allow_override_header_image' );
	function avventure_woocommerce_allow_override_header_image( $allow = true ) {
		return is_product() ? false : $allow;
	}
}

// Return shop page ID
if ( ! function_exists( 'avventure_woocommerce_get_shop_page_id' ) ) {
	function avventure_woocommerce_get_shop_page_id() {
		return get_option( 'woocommerce_shop_page_id' );
	}
}

// Return shop page link
if ( ! function_exists( 'avventure_woocommerce_get_shop_page_link' ) ) {
	function avventure_woocommerce_get_shop_page_link() {
		$url = '';
		$id  = avventure_woocommerce_get_shop_page_id();
		if ( $id ) {
			$url = get_permalink( $id );
		}
		return $url;
	}
}

// Show categories of the current product
if ( ! function_exists( 'avventure_woocommerce_get_post_categories' ) ) {
	//Handler of the add_filter( 'avventure_filter_get_post_categories', 		'avventure_woocommerce_get_post_categories');
	function avventure_woocommerce_get_post_categories( $cats = '' ) {
		if ( get_post_type() == 'product' ) {
			$cats = avventure_get_post_terms( ', ', get_the_ID(), 'product_cat' );
		}
		return $cats;
	}
}

// Add 'product' to the list of the supported post-types
if ( ! function_exists( 'avventure_woocommerce_list_post_types' ) ) {
	//Handler of the add_filter( 'avventure_filter_list_posts_types', 'avventure_woocommerce_list_post_types');
	function avventure_woocommerce_list_post_types( $list = array() ) {
		$list['product'] = esc_html__( 'Products', 'avventure' );
		return $list;
	}
}

// Show price of the current product in the widgets and search results
if ( ! function_exists( 'avventure_woocommerce_get_post_info' ) ) {
	//Handler of the add_filter( 'avventure_filter_get_post_info', 'avventure_woocommerce_get_post_info');
	function avventure_woocommerce_get_post_info( $post_info = '' ) {
		if ( get_post_type() == 'product' ) {
			global $product;
			$price_html = $product->get_price_html();
			if ( ! empty( $price_html ) ) {
				$post_info = '<div class="post_price product_price price">' . trim( $price_html ) . '</div>' . $post_info;
			}
		}
		return $post_info;
	}
}

// Show price of the current product in the search results streampage
if ( ! function_exists( 'avventure_woocommerce_action_before_post_meta' ) ) {
	//Handler of the add_action( 'avventure_action_before_post_meta', 'avventure_woocommerce_action_before_post_meta');
	function avventure_woocommerce_action_before_post_meta() {
		if ( ! is_single() && get_post_type() == 'product' ) {
			global $product;
			$price_html = $product->get_price_html();
			if ( ! empty( $price_html ) ) {
				?><div class="post_price product_price price"><?php avventure_show_layout( $price_html ); ?></div>
				<?php
			}
		}
	}
}

// Enqueue WooCommerce custom styles
if ( ! function_exists( 'avventure_woocommerce_frontend_scripts' ) ) {
	//Handler of the add_action( 'wp_enqueue_scripts', 'avventure_woocommerce_frontend_scripts', 1100 );
	function avventure_woocommerce_frontend_scripts() {
		//if (avventure_is_woocommerce_page())
		if ( avventure_is_on( avventure_get_theme_option( 'debug_mode' ) ) ) {
			$avventure_url = avventure_get_file_url( 'plugins/woocommerce/woocommerce.js' );
			if ( '' != $avventure_url ) {
				wp_enqueue_script( 'woocommerce', $avventure_url, array( 'jquery' ), null, true );
			}
		}
	}
}

// Merge custom styles
if ( ! function_exists( 'avventure_woocommerce_merge_styles' ) ) {
	//Handler of the add_filter('avventure_filter_merge_styles', 'avventure_woocommerce_merge_styles');
	function avventure_woocommerce_merge_styles( $list ) {
		if ( avventure_exists_woocommerce() ) {
			$list[] = 'plugins/woocommerce/_woocommerce.scss';
		}
		return $list;
	}
}


// Merge responsive styles
if ( ! function_exists( 'avventure_woocommerce_merge_styles_responsive' ) ) {
	//Handler of the add_filter('avventure_filter_merge_styles_responsive', 'avventure_woocommerce_merge_styles_responsive');
	function avventure_woocommerce_merge_styles_responsive( $list ) {
		if ( avventure_exists_woocommerce() ) {
			$list[] = 'plugins/woocommerce/_woocommerce-responsive.scss';
		}
		return $list;
	}
}

// Merge custom scripts
if ( ! function_exists( 'avventure_woocommerce_merge_scripts' ) ) {
	//Handler of the add_filter('avventure_filter_merge_scripts', 'avventure_woocommerce_merge_scripts');
	function avventure_woocommerce_merge_scripts( $list ) {
		if ( avventure_exists_woocommerce() ) {
			$list[] = 'plugins/woocommerce/woocommerce.js';
		}
		return $list;
	}
}



// Add WooCommerce specific items into lists
//------------------------------------------------------------------------

// Add sidebar
if ( ! function_exists( 'avventure_woocommerce_list_sidebars' ) ) {
	//Handler of the add_filter( 'avventure_filter_list_sidebars', 'avventure_woocommerce_list_sidebars' );
	function avventure_woocommerce_list_sidebars( $list = array() ) {
		$list['woocommerce_widgets'] = array(
			'name'        => esc_html__( 'WooCommerce Widgets', 'avventure' ),
			'description' => esc_html__( 'Widgets to be shown on the WooCommerce pages', 'avventure' ),
		);
		return $list;
	}
}




// Decorate WooCommerce output: Loop
//------------------------------------------------------------------------

// Add query vars to set products per page
if ( ! function_exists( 'avventure_woocommerce_pre_get_posts' ) ) {
	//Handler of the add_action( 'pre_get_posts', 'avventure_woocommerce_pre_get_posts' );
	function avventure_woocommerce_pre_get_posts( $query ) {
		if ( ! $query->is_main_query() ) {
			return;
		}
		if ( $query->get( 'wc_query' ) == 'product_query' ) {
			$ppp = get_theme_mod( 'posts_per_page_shop', 0 );
			if ( $ppp > 0 ) {
				$query->set( 'posts_per_page', $ppp );
			}
		}
	}
}


// Before main content
if ( ! function_exists( 'avventure_woocommerce_wrapper_start' ) ) {
	//remove_action( 'woocommerce_before_main_content', 'avventure_woocommerce_wrapper_start', 10);
	//Handler of the add_action('woocommerce_before_main_content', 'avventure_woocommerce_wrapper_start', 10);
	function avventure_woocommerce_wrapper_start() {
		if ( is_product() || is_cart() || is_checkout() || is_account_page() ) {
			?>
			<article class="post_item_single post_type_product">
			<?php
		} else {
			?>
			<div class="list_products shop_mode_<?php echo esc_attr( ! avventure_storage_empty( 'shop_mode' ) ? avventure_storage_get( 'shop_mode' ) : 'thumbs' ); ?>">
				<div class="list_products_header">
			<?php
			avventure_storage_set( 'woocommerce_list_products_header', true );
		}
	}
}

// After main content
if ( ! function_exists( 'avventure_woocommerce_wrapper_end' ) ) {
	//remove_action( 'woocommerce_after_main_content', 'avventure_woocommerce_wrapper_end', 10);
	//Handler of the add_action('woocommerce_after_main_content', 'avventure_woocommerce_wrapper_end', 10);
	function avventure_woocommerce_wrapper_end() {
		if ( is_product() || is_cart() || is_checkout() || is_account_page() ) {
			?>
			</article><!-- /.post_item_single -->
			<?php
		} else {
			?>
			</div><!-- /.list_products -->
			<?php
		}
	}
}

// Close header section
if ( ! function_exists( 'avventure_woocommerce_archive_description' ) ) {
	//Handler of the add_action('woocommerce_after_main_content', 'avventure_woocommerce_archive_description', 1);
	//Handler of the add_action( 'woocommerce_before_shop_loop',	'avventure_woocommerce_archive_description', 5 );
	//Handler of the add_action( 'woocommerce_no_products_found',	'avventure_woocommerce_archive_description', 5 );
	function avventure_woocommerce_archive_description() {
		if ( avventure_storage_get( 'woocommerce_list_products_header' ) ) {
			?>
			</div><!-- /.list_products_header -->
			<?php
			avventure_storage_set( 'woocommerce_list_products_header', false );
			remove_action( 'woocommerce_after_main_content', 'avventure_woocommerce_archive_description', 1 );
		} elseif ( ! is_singular() ) {
			get_template_part( apply_filters( 'avventure_filter_get_template_part', 'content', 'none-search' ), 'none-search' );
		}
	}
}

// Add list mode buttons
if ( ! function_exists( 'avventure_woocommerce_before_shop_loop' ) ) {
	//Handler of the add_action( 'woocommerce_before_shop_loop', 'avventure_woocommerce_before_shop_loop', 10 );
	function avventure_woocommerce_before_shop_loop() {
		?>
		<div class="avventure_shop_mode_buttons"><form action="<?php echo esc_url( avventure_get_current_url() ); ?>" method="post"><input type="hidden" name="avventure_shop_mode" value="<?php echo esc_attr( avventure_storage_get( 'shop_mode' ) ); ?>" /><a href="#" class="woocommerce_thumbs icon-th" title="<?php esc_attr_e( 'Show products as thumbs', 'avventure' ); ?>"></a><a href="#" class="woocommerce_list icon-th-list" title="<?php esc_attr_e( 'Show products as list', 'avventure' ); ?>"></a></form></div><!-- /.avventure_shop_mode_buttons -->
		<?php
	}
}

// Show 'No products' if no search results and display mode 'both'
if ( ! function_exists( 'avventure_woocommerce_after_shop_loop' ) ) {
	//Handler of the add_action( 'woocommerce_after_shop_loop', 'avventure_woocommerce_after_shop_loop', 1 );
	function avventure_woocommerce_after_shop_loop() {
		if ( ! have_posts() && 'products' != woocommerce_get_loop_display_mode() ) {
			wc_get_template( 'loop/no-products-found.php' );
		}
	}
}

// Number of columns for the shop streampage
if ( ! function_exists( 'avventure_woocommerce_loop_shop_columns' ) ) {
	//Handler of the add_filter( 'loop_shop_columns', 'avventure_woocommerce_loop_shop_columns' );
	function avventure_woocommerce_loop_shop_columns( $cols ) {
		return max( 2, min( 4, avventure_get_theme_option( 'blog_columns' ) ) );
	}
}

// Add column class into product item in shop streampage
if ( ! function_exists( 'avventure_woocommerce_loop_shop_columns_class' ) ) {
	//Handler of the add_filter( 'post_class', 'avventure_woocommerce_loop_shop_columns_class' );
	//Handler of the add_filter( 'product_cat_class', 'avventure_woocommerce_loop_shop_columns_class', 10, 3 );
	function avventure_woocommerce_loop_shop_columns_class( $classes, $class = '', $cat = '' ) {
		global $woocommerce_loop;
		if ( is_product() ) {
			if ( ! empty( $woocommerce_loop['columns'] ) ) {
				$classes[] = ' column-1_' . esc_attr( $woocommerce_loop['columns'] );
			}
		} elseif ( is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy() ) {
			$classes[] = ' column-1_' . esc_attr( max( 2, min( 4, avventure_get_theme_option( 'blog_columns' ) ) ) );
		}
		return $classes;
	}
}


// Open item wrapper for categories and products
if ( ! function_exists( 'avventure_woocommerce_item_wrapper_start' ) ) {
	//Handler of the add_action( 'woocommerce_before_subcategory_title', 'avventure_woocommerce_item_wrapper_start', 9 );
	//Handler of the add_action( 'woocommerce_before_shop_loop_item_title', 'avventure_woocommerce_item_wrapper_start', 9 );
	function avventure_woocommerce_item_wrapper_start( $cat = '' ) {
		avventure_storage_set( 'in_product_item', true );
		$hover = avventure_get_theme_option( 'shop_hover' );
		?>
		<div class="post_item post_layout_<?php echo esc_attr( is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy() ? avventure_storage_get( 'shop_mode' ) : 'thumbs' ); ?>">
			<div class="post_featured hover_<?php echo esc_attr( $hover ); ?>">
				<?php do_action( 'avventure_action_woocommerce_item_featured_start' ); ?>
				<a href="<?php echo esc_url( is_object( $cat ) ? get_term_link( $cat->slug, 'product_cat' ) : get_permalink() ); ?>">
				<?php
				do_action( 'avventure_action_woocommerce_item_featured_link_start' );
	}
}

// Open item wrapper for categories and products
if ( ! function_exists( 'avventure_woocommerce_open_item_wrapper' ) ) {
	//Handler of the add_action( 'woocommerce_before_subcategory_title', 'avventure_woocommerce_title_wrapper_start', 20 );
	//Handler of the add_action( 'woocommerce_before_shop_loop_item_title', 'avventure_woocommerce_title_wrapper_start', 20 );
	function avventure_woocommerce_title_wrapper_start( $cat = '' ) {
				do_action( 'avventure_action_woocommerce_item_featured_link_end' );
		?>
				</a>
				<?php
				$hover = avventure_get_theme_option( 'shop_hover' );
				if ( 'none' != $hover ) {
					?>
					<div class="mask"></div>
					<?php
					avventure_hovers_add_icons( $hover, array( 'cat' => $cat ) );
				}
				do_action( 'avventure_action_woocommerce_item_featured_end' );
				?>
			</div><!-- /.post_featured -->
			<div class="post_data">
				<div class="post_data_inner">
					<div class="post_header entry-header">
					<?php
					do_action( 'avventure_action_woocommerce_item_header_start' );
	}
}


// Display product's tags before the title
if ( ! function_exists( 'avventure_woocommerce_title_tags' ) ) {
	//Handler of the add_action( 'woocommerce_before_shop_loop_item_title', 'avventure_woocommerce_title_tags', 30 );
	function avventure_woocommerce_title_tags() {
		global $product;
		avventure_show_layout( wc_get_product_tag_list( $product->get_id(), ', ', '<div class="post_tags product_tags">', '</div>' ) );
	}
}

// Wrap product title to the link
if ( ! function_exists( 'avventure_woocommerce_the_title' ) ) {
	//Handler of the add_filter( 'the_title', 'avventure_woocommerce_the_title' );
	function avventure_woocommerce_the_title( $title ) {
		if ( avventure_storage_get( 'in_product_item' ) && get_post_type() == 'product' ) {
			$title = '<a href="' . esc_url(get_permalink()) . '">' . esc_html( $title ) . '</a>';
		}
		return $title;
	}
}

// Wrap category title to the link: open tag
if ( ! function_exists( 'avventure_woocommerce_before_subcategory_title' ) ) {
	//Handler of the add_action( 'woocommerce_before_subcategory_title', 'avventure_woocommerce_before_subcategory_title', 10, 1 );
	function avventure_woocommerce_before_subcategory_title( $cat ) {
		if ( avventure_storage_get( 'in_product_item' ) && is_object( $cat ) ) {
			?>
			<a href="<?php echo esc_url( get_term_link( $cat->slug, 'product_cat' ) ); ?>">
			<?php
		}
	}
}

// Wrap category title to the link: close tag
if ( ! function_exists( 'avventure_woocommerce_after_subcategory_title' ) ) {
	//Handler of the add_action( 'woocommerce_after_subcategory_title', 'avventure_woocommerce_after_subcategory_title', 10, 1 );
	function avventure_woocommerce_after_subcategory_title( $cat ) {
		if ( avventure_storage_get( 'in_product_item' ) && is_object( $cat ) ) {
			?>
			</a>
			<?php
		}
	}
}

// Add excerpt in output for the product in the list mode
if ( ! function_exists( 'avventure_woocommerce_title_wrapper_end' ) ) {
	//Handler of the add_action( 'woocommerce_after_shop_loop_item_title', 'avventure_woocommerce_title_wrapper_end', 7);
	function avventure_woocommerce_title_wrapper_end() {
			do_action( 'avventure_action_woocommerce_item_header_end' );
		?>
			</div><!-- /.post_header -->
		<?php
		if ( avventure_storage_get( 'shop_mode' ) == 'list' && ( is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy() ) && ! is_product() ) {
			?>
			<div class="post_content entry-content"><?php avventure_show_layout( get_the_excerpt() ); ?></div>
			<?php
		}
	}
}

// Add excerpt in output for the product in the list mode
if ( ! function_exists( 'avventure_woocommerce_title_wrapper_end2' ) ) {
	//Handler of the add_action( 'woocommerce_after_subcategory_title', 'avventure_woocommerce_title_wrapper_end2', 10 );
	function avventure_woocommerce_title_wrapper_end2( $category ) {
			do_action( 'avventure_action_woocommerce_item_header_end' );
		?>
			</div><!-- /.post_header -->
		<?php
		if ( avventure_storage_get( 'shop_mode' ) == 'list' && is_shop() && ! is_product() ) {
			?>
			<div class="post_content entry-content"><?php avventure_show_layout( $category->description ); ?></div><!-- /.post_content -->
			<?php
		}
	}
}

// Close item wrapper for categories and products
if ( ! function_exists( 'avventure_woocommerce_close_item_wrapper' ) ) {
	//Handler of the add_action( 'woocommerce_after_subcategory', 'avventure_woocommerce_item_wrapper_end', 20 );
	//Handler of the add_action( 'woocommerce_after_shop_loop_item', 'avventure_woocommerce_item_wrapper_end', 20 );
	function avventure_woocommerce_item_wrapper_end( $cat = '' ) {
		?>
				</div><!-- /.post_data_inner -->
			</div><!-- /.post_data -->
		</div><!-- /.post_item -->
		<?php
		avventure_storage_set( 'in_product_item', false );
	}
}

// Change text on 'Add to cart' button
if ( ! function_exists( 'avventure_woocommerce_add_to_cart_text' ) ) {
	//Handler of the add_filter( 'woocommerce_product_add_to_cart_text',		'avventure_woocommerce_add_to_cart_text' );
	//Handler of the add_filter( 'woocommerce_product_single_add_to_cart_text','avventure_woocommerce_add_to_cart_text' );
	function avventure_woocommerce_add_to_cart_text( $text = '' ) {
		return esc_html__( 'Buy now', 'avventure' );
	}
}


// Wrap 'Add to cart' button
if ( ! function_exists( 'avventure_woocommerce_add_to_cart_link' ) ) {
	//Handler of the add_filter( 'woocommerce_loop_add_to_cart_link', 'avventure_woocommerce_add_to_cart_link', 10, 2 );
	function avventure_woocommerce_add_to_cart_link( $html, $product = false, $args = array() ) {
		return avventure_is_off( avventure_get_theme_option( 'shop_hover' ) ) ? sprintf( '<div class="add_to_cart_wrap">%s</div>', $html ) : $html;
	}
}


// Add label 'out of stock'
if ( ! function_exists( 'avventure_woocommerce_add_out_of_stock_label' ) ) {
	//Handler of the add_action( 'avventure_action_woocommerce_item_featured_link_start', 'avventure_woocommerce_add_out_of_stock_label' );
	function avventure_woocommerce_add_out_of_stock_label() {
		global $product;
		if ( is_object( $product ) && get_post_type() == 'product' && ( ! $product->is_in_stock() || ! $product->is_purchasable() ) ) {
			?>
			<span class="outofstock_label"><?php esc_html_e( 'Out of stock', 'avventure' ); ?></span>
			<?php
		}
	}
}


// Wrap price - start (WooCommerce use priority 10 to output price)
if ( ! function_exists( 'avventure_woocommerce_price_wrapper_start' ) ) {
	//Handler of the add_action( 'woocommerce_after_shop_loop_item_title',	'avventure_woocommerce_price_wrapper_start', 9);
	function avventure_woocommerce_price_wrapper_start() {
		if ( avventure_storage_get( 'shop_mode' ) == 'thumbs' && ( is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy() ) && ! is_product() ) {
			global $product;
			$price_html = $product->get_price_html();
			if ( '' != $price_html ) {
				?>
				<div class="price_wrap">
				<?php
			}
		}
	}
}


// Wrap price - start (WooCommerce use priority 10 to output price)
if ( ! function_exists( 'avventure_woocommerce_price_wrapper_end' ) ) {
	//Handler of the add_action( 'woocommerce_after_shop_loop_item_title',	'avventure_woocommerce_price_wrapper_end', 11);
	function avventure_woocommerce_price_wrapper_end() {
		if ( avventure_storage_get( 'shop_mode' ) == 'thumbs' && ( is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy() ) && ! is_product() ) {
			global $product;
			$price_html = $product->get_price_html();
			if ( '' != $price_html ) {
				?>
				</div><!-- /.price_wrap -->
				<?php
			}
		}
	}
}


// Decorate price
if ( ! function_exists( 'avventure_woocommerce_get_price_html' ) ) {
	//Handler of the add_filter(    'woocommerce_get_price_html',	'avventure_woocommerce_get_price_html' );
	function avventure_woocommerce_get_price_html( $price = '' ) {
		if ( ! is_admin() && ! empty( $price ) ) {
			$sep = get_option( 'woocommerce_price_decimal_sep' );
			if ( empty( $sep ) ) {
				$sep = '.';
			}
			$price = preg_replace( '/([0-9,]+)(\\' . trim( $sep ) . ')([0-9]{2})/', '\\1<span class="decimals_separator">\\2</span><span class="decimals">\\3</span>', $price );
		}
		return $price;
	}
}



// Decorate WooCommerce output: Single product
//------------------------------------------------------------------------

// Add WooCommerce specific vars into localize array
if ( ! function_exists( 'avventure_woocommerce_localize_script' ) ) {
	//Handler of the add_filter( 'avventure_filter_localize_script','avventure_woocommerce_localize_script' );
	function avventure_woocommerce_localize_script( $arr ) {
		$arr['stretch_tabs_area'] = ! avventure_sidebar_present() ? avventure_get_theme_option( 'stretch_tabs_area' ) : 0;
		return $arr;
	}
}

// Add Product ID for the single product
if ( ! function_exists( 'avventure_woocommerce_show_product_id' ) ) {
	//Handler of the add_action( 'woocommerce_product_meta_end', 'avventure_woocommerce_show_product_id', 10);
	function avventure_woocommerce_show_product_id() {
		$authors = wp_get_post_terms( get_the_ID(), 'pa_product_author' );
		if ( is_array( $authors ) && count( $authors ) > 0 ) {
			echo '<span class="product_author">' . esc_html__( 'Author: ', 'avventure' );
			$delim = '';
			foreach ( $authors as $author ) {
				echo  esc_html( $delim ) . '<span>' . esc_html( $author->name ) . '</span>';
				$delim = ', ';
			}
			echo '</span>';
		}
		echo '<span class="product_id">' . esc_html__( 'Product ID: ', 'avventure' ) . '<span>' . get_the_ID() . '</span></span>';
	}
}

// Number columns for the product's thumbnails
if ( ! function_exists( 'avventure_woocommerce_product_thumbnails_columns' ) ) {
	//Handler of the add_filter( 'woocommerce_product_thumbnails_columns', 'avventure_woocommerce_product_thumbnails_columns' );
	function avventure_woocommerce_product_thumbnails_columns( $cols ) {
		return 4;
	}
}

// Set products number for the related products
if ( ! function_exists( 'avventure_woocommerce_output_related_products_args' ) ) {
	//Handler of the add_filter( 'woocommerce_output_related_products_args', 'avventure_woocommerce_output_related_products_args' );
	function avventure_woocommerce_output_related_products_args( $args ) {
		$args['posts_per_page'] = (int) avventure_get_theme_option( 'show_related_posts' )
										? max( 0, min( 9, avventure_get_theme_option( 'related_posts' ) ) )
										: 0;
		$args['columns']        = max( 1, min( 4, avventure_get_theme_option( 'related_columns' ) ) );
		return $args;
	}
}

// Set columns number for the related products
if ( ! function_exists( 'avventure_woocommerce_related_products_columns' ) ) {
	//Handler of the add_filter('woocommerce_related_products_columns', 'avventure_woocommerce_related_products_columns' );
	function avventure_woocommerce_related_products_columns( $columns ) {
		$columns = max( 1, min( 4, avventure_get_theme_option( 'related_columns' ) ) );
		return $columns;
	}
}



// Decorate WooCommerce output: Widgets
//------------------------------------------------------------------------

// Search form
if ( ! function_exists( 'avventure_woocommerce_get_product_search_form' ) ) {
	//Handler of the add_filter( 'get_product_search_form', 'avventure_woocommerce_get_product_search_form' );
	function avventure_woocommerce_get_product_search_form( $form ) {
		return '
		<form role="search" method="get" class="search_form" action="' . esc_url( home_url( '/' ) ) . '">
			<input type="text" class="search_field" placeholder="' . esc_attr__( 'Search for products &hellip;', 'avventure' ) . '" value="' . get_search_query() . '" name="s" /><button class="search_button" type="submit">' . esc_html__( 'Search', 'avventure' ) . '</button>
			<input type="hidden" name="post_type" value="product" />
		</form>
		';
	}
}

// Add plugin-specific colors and fonts to the custom CSS
if ( avventure_exists_woocommerce() ) {
	require_once AVVENTURE_THEME_DIR . 'plugins/woocommerce/woocommerce-styles.php'; }
?>
