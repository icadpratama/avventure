<div class="front_page_section front_page_section_contacts<?php
			$avventure_scheme = avventure_get_theme_option( 'front_page_contacts_scheme' );
if ( ! avventure_is_inherit( $avventure_scheme ) ) {
	echo ' scheme_' . esc_attr( $avventure_scheme );
}
			echo ' front_page_section_paddings_' . esc_attr( avventure_get_theme_option( 'front_page_contacts_paddings' ) );
?>"
		<?php
		$avventure_css      = '';
		$avventure_bg_image = avventure_get_theme_option( 'front_page_contacts_bg_image' );
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
	$avventure_anchor_icon = avventure_get_theme_option( 'front_page_contacts_anchor_icon' );
	$avventure_anchor_text = avventure_get_theme_option( 'front_page_contacts_anchor_text' );
if ( ( ! empty( $avventure_anchor_icon ) || ! empty( $avventure_anchor_text ) ) && shortcode_exists( 'trx_sc_anchor' ) ) {
	echo do_shortcode(
		'[trx_sc_anchor id="front_page_section_contacts"'
									. ( ! empty( $avventure_anchor_icon ) ? ' icon="' . esc_attr( $avventure_anchor_icon ) . '"' : '' )
									. ( ! empty( $avventure_anchor_text ) ? ' title="' . esc_attr( $avventure_anchor_text ) . '"' : '' )
									. ']'
	);
}
?>
	<div class="front_page_section_inner front_page_section_contacts_inner
	<?php
	if ( avventure_get_theme_option( 'front_page_contacts_fullheight' ) ) {
		echo ' avventure-full-height sc_layouts_flex sc_layouts_columns_middle';
	}
	?>
			"
			<?php
			$avventure_css      = '';
			$avventure_bg_mask  = avventure_get_theme_option( 'front_page_contacts_bg_mask' );
			$avventure_bg_color_type = avventure_get_theme_option( 'front_page_contacts_bg_color_type' );
			if ( 'custom' == $avventure_bg_color_type ) {
				$avventure_bg_color = avventure_get_theme_option( 'front_page_contacts_bg_color' );
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
		<div class="front_page_section_content_wrap front_page_section_contacts_content_wrap content_wrap">
			<?php

			// Title and description
			$avventure_caption     = avventure_get_theme_option( 'front_page_contacts_caption' );
			$avventure_description = avventure_get_theme_option( 'front_page_contacts_description' );
			if ( ! empty( $avventure_caption ) || ! empty( $avventure_description ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
				// Caption
				if ( ! empty( $avventure_caption ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
					?>
					<h2 class="front_page_section_caption front_page_section_contacts_caption front_page_block_<?php echo ! empty( $avventure_caption ) ? 'filled' : 'empty'; ?>">
					<?php
						echo wp_kses_post( $avventure_caption );
					?>
					</h2>
					<?php
				}

				// Description
				if ( ! empty( $avventure_description ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
					?>
					<div class="front_page_section_description front_page_section_contacts_description front_page_block_<?php echo ! empty( $avventure_description ) ? 'filled' : 'empty'; ?>">
					<?php
						echo wp_kses_post( wpautop( $avventure_description ) );
					?>
					</div>
					<?php
				}
			}

			// Content (text)
			$avventure_content = avventure_get_theme_option( 'front_page_contacts_content' );
			$avventure_layout  = avventure_get_theme_option( 'front_page_contacts_layout' );
			if ( 'columns' == $avventure_layout && ( ! empty( $avventure_content ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) ) {
				?>
				<div class="front_page_section_columns front_page_section_contacts_columns columns_wrap">
					<div class="column-1_3">
				<?php
			}

			if ( ( ! empty( $avventure_content ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) ) {
				?>
				<div class="front_page_section_content front_page_section_contacts_content front_page_block_<?php echo ! empty( $avventure_content ) ? 'filled' : 'empty'; ?>">
				<?php
					echo wp_kses_post( $avventure_content );
				?>
				</div>
				<?php
			}

			if ( 'columns' == $avventure_layout && ( ! empty( $avventure_content ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) ) {
				?>
				</div><div class="column-2_3">
				<?php
			}

			// Shortcode output
			$avventure_sc = avventure_get_theme_option( 'front_page_contacts_shortcode' );
			if ( ! empty( $avventure_sc ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
				?>
				<div class="front_page_section_output front_page_section_contacts_output front_page_block_<?php echo ! empty( $avventure_sc ) ? 'filled' : 'empty'; ?>">
				<?php
					avventure_show_layout( do_shortcode( $avventure_sc ) );
				?>
				</div>
				<?php
			}

			if ( 'columns' == $avventure_layout && ( ! empty( $avventure_content ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) ) {
				?>
				</div></div>
				<?php
			}
			?>

		</div>
	</div>
</div>
