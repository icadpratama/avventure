<div class="front_page_section front_page_section_testimonials<?php
			$avventure_scheme = avventure_get_theme_option( 'front_page_testimonials_scheme' );
if ( ! avventure_is_inherit( $avventure_scheme ) ) {
	echo ' scheme_' . esc_attr( $avventure_scheme );
}
			echo ' front_page_section_paddings_' . esc_attr( avventure_get_theme_option( 'front_page_testimonials_paddings' ) );
?>"
		<?php
		$avventure_css      = '';
		$avventure_bg_image = avventure_get_theme_option( 'front_page_testimonials_bg_image' );
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
	$avventure_anchor_icon = avventure_get_theme_option( 'front_page_testimonials_anchor_icon' );
	$avventure_anchor_text = avventure_get_theme_option( 'front_page_testimonials_anchor_text' );
if ( ( ! empty( $avventure_anchor_icon ) || ! empty( $avventure_anchor_text ) ) && shortcode_exists( 'trx_sc_anchor' ) ) {
	echo do_shortcode(
		'[trx_sc_anchor id="front_page_section_testimonials"'
									. ( ! empty( $avventure_anchor_icon ) ? ' icon="' . esc_attr( $avventure_anchor_icon ) . '"' : '' )
									. ( ! empty( $avventure_anchor_text ) ? ' title="' . esc_attr( $avventure_anchor_text ) . '"' : '' )
									. ']'
	);
}
?>
	<div class="front_page_section_inner front_page_section_testimonials_inner
	<?php
	if ( avventure_get_theme_option( 'front_page_testimonials_fullheight' ) ) {
		echo ' avventure-full-height sc_layouts_flex sc_layouts_columns_middle';
	}
	?>
			"
			<?php
			$avventure_css      = '';
			$avventure_bg_mask  = avventure_get_theme_option( 'front_page_testimonials_bg_mask' );
			$avventure_bg_color_type = avventure_get_theme_option( 'front_page_testimonials_bg_color_type' );
			if ( 'custom' == $avventure_bg_color_type ) {
				$avventure_bg_color = avventure_get_theme_option( 'front_page_testimonials_bg_color' );
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
		<div class="front_page_section_content_wrap front_page_section_testimonials_content_wrap content_wrap">
			<?php
			// Caption
			$avventure_caption = avventure_get_theme_option( 'front_page_testimonials_caption' );
			if ( ! empty( $avventure_caption ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
				?>
				<h2 class="front_page_section_caption front_page_section_testimonials_caption front_page_block_<?php echo ! empty( $avventure_caption ) ? 'filled' : 'empty'; ?>"><?php echo wp_kses_post( $avventure_caption ); ?></h2>
				<?php
			}

			// Description (text)
			$avventure_description = avventure_get_theme_option( 'front_page_testimonials_description' );
			if ( ! empty( $avventure_description ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
				?>
				<div class="front_page_section_description front_page_section_testimonials_description front_page_block_<?php echo ! empty( $avventure_description ) ? 'filled' : 'empty'; ?>"><?php echo wp_kses_post( wpautop( $avventure_description ) ); ?></div>
				<?php
			}

			// Content (widgets)
			?>
			<div class="front_page_section_output front_page_section_testimonials_output">
			<?php
			if ( is_active_sidebar( 'front_page_testimonials_widgets' ) ) {
				dynamic_sidebar( 'front_page_testimonials_widgets' );
			} elseif ( current_user_can( 'edit_theme_options' ) ) {
				if ( ! avventure_exists_trx_addons() ) {
					avventure_customizer_need_trx_addons_message();
				} else {
					avventure_customizer_need_widgets_message( 'front_page_testimonials_caption', 'ThemeREX Addons - Testimonials' );
				}
			}
			?>
			</div>
		</div>
	</div>
</div>
