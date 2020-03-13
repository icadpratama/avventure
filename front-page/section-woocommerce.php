<div class="front_page_section front_page_section_woocommerce<?php
			$avventure_scheme = avventure_get_theme_option( 'front_page_woocommerce_scheme' );
if ( ! avventure_is_inherit( $avventure_scheme ) ) {
	echo ' scheme_' . esc_attr( $avventure_scheme );
}
			echo ' front_page_section_paddings_' . esc_attr( avventure_get_theme_option( 'front_page_woocommerce_paddings' ) );
?>"
		<?php
		$avventure_css      = '';
		$avventure_bg_image = avventure_get_theme_option( 'front_page_woocommerce_bg_image' );
		if ( ! empty( $avventure_bg_image ) ) {
			$avventure_css .= 'background-image: url(' . esc_url( avventure_get_attachment_url( $avventure_bg_image ) ) . ');';
		}
		if ( ! empty( $avventure_css ) ) {
			echo ' style="' . esc_attr( $avventure_css ) . '"';
		}
		?>
>
<?php
	// Add anchor
	$avventure_anchor_icon = avventure_get_theme_option( 'front_page_woocommerce_anchor_icon' );
	$avventure_anchor_text = avventure_get_theme_option( 'front_page_woocommerce_anchor_text' );
if ( ( ! empty( $avventure_anchor_icon ) || ! empty( $avventure_anchor_text ) ) && shortcode_exists( 'trx_sc_anchor' ) ) {
	echo do_shortcode(
		'[trx_sc_anchor id="front_page_section_woocommerce"'
									. ( ! empty( $avventure_anchor_icon ) ? ' icon="' . esc_attr( $avventure_anchor_icon ) . '"' : '' )
									. ( ! empty( $avventure_anchor_text ) ? ' title="' . esc_attr( $avventure_anchor_text ) . '"' : '' )
									. ']'
	);
}
?>
	<div class="front_page_section_inner front_page_section_woocommerce_inner
	<?php
	if ( avventure_get_theme_option( 'front_page_woocommerce_fullheight' ) ) {
		echo ' avventure-full-height sc_layouts_flex sc_layouts_columns_middle';
	}
	?>
			"
			<?php
			$avventure_css      = '';
			$avventure_bg_mask  = avventure_get_theme_option( 'front_page_woocommerce_bg_mask' );
			$avventure_bg_color_type = avventure_get_theme_option( 'front_page_woocommerce_bg_color_type' );
			if ( 'custom' == $avventure_bg_color_type ) {
				$avventure_bg_color = avventure_get_theme_option( 'front_page_woocommerce_bg_color' );
			} elseif ( 'scheme_bg_color' == $avventure_bg_color_type ) {
				$avventure_bg_color = avventure_get_scheme_color( 'bg_color' );
			} else {
				$avventure_bg_color = '';
			}
			if ( ! empty( $avventure_bg_color ) && $avventure_bg_mask > 0 ) {
				$avventure_css .= 'background-color: ' . esc_attr(
					1 == $avventure_bg_mask ? $avventure_bg_color : avventure_hex2rgba( $avventure_bg_color, $avventure_bg_mask )
				) . ';';
			}
			if ( ! empty( $avventure_css ) ) {
				echo ' style="' . esc_attr( $avventure_css ) . '"';
			}
			?>
	>
		<div class="front_page_section_content_wrap front_page_section_woocommerce_content_wrap content_wrap woocommerce">
			<?php
			// Content wrap with title and description
			$avventure_caption     = avventure_get_theme_option( 'front_page_woocommerce_caption' );
			$avventure_description = avventure_get_theme_option( 'front_page_woocommerce_description' );
			if ( ! empty( $avventure_caption ) || ! empty( $avventure_description ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
				// Caption
				if ( ! empty( $avventure_caption ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
					?>
					<h2 class="front_page_section_caption front_page_section_woocommerce_caption front_page_block_<?php echo ! empty( $avventure_caption ) ? 'filled' : 'empty'; ?>">
					<?php
						echo wp_kses_post( $avventure_caption );
					?>
					</h2>
					<?php
				}

				// Description (text)
				if ( ! empty( $avventure_description ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
					?>
					<div class="front_page_section_description front_page_section_woocommerce_description front_page_block_<?php echo ! empty( $avventure_description ) ? 'filled' : 'empty'; ?>">
					<?php
						echo wp_kses_post( wpautop( $avventure_description ) );
					?>
					</div>
					<?php
				}
			}

			// Content (widgets)
			?>
			<div class="front_page_section_output front_page_section_woocommerce_output list_products shop_mode_thumbs">
			<?php
				$avventure_woocommerce_sc = avventure_get_theme_option( 'front_page_woocommerce_products' );
			if ( 'products' == $avventure_woocommerce_sc ) {
				$avventure_woocommerce_sc_ids      = avventure_get_theme_option( 'front_page_woocommerce_products_per_page' );
				$avventure_woocommerce_sc_per_page = count( explode( ',', $avventure_woocommerce_sc_ids ) );
			} else {
				$avventure_woocommerce_sc_per_page = max( 1, (int) avventure_get_theme_option( 'front_page_woocommerce_products_per_page' ) );
			}
				$avventure_woocommerce_sc_columns = max( 1, min( $avventure_woocommerce_sc_per_page, (int) avventure_get_theme_option( 'front_page_woocommerce_products_columns' ) ) );
				echo do_shortcode(
					"[{$avventure_woocommerce_sc}"
									. ( 'products' == $avventure_woocommerce_sc
											? ' ids="' . esc_attr( $avventure_woocommerce_sc_ids ) . '"'
											: '' )
									. ( 'product_category' == $avventure_woocommerce_sc
											? ' category="' . esc_attr( avventure_get_theme_option( 'front_page_woocommerce_products_categories' ) ) . '"'
											: '' )
									. ( 'best_selling_products' != $avventure_woocommerce_sc
											? ' orderby="' . esc_attr( avventure_get_theme_option( 'front_page_woocommerce_products_orderby' ) ) . '"'
												. ' order="' . esc_attr( avventure_get_theme_option( 'front_page_woocommerce_products_order' ) ) . '"'
											: '' )
									. ' per_page="' . esc_attr( $avventure_woocommerce_sc_per_page ) . '"'
									. ' columns="' . esc_attr( $avventure_woocommerce_sc_columns ) . '"'
					. ']'
				);
				?>
			</div>
		</div>
	</div>
</div>
