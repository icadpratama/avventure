<?php
/**
 * The Header: Logo and main menu
 *
 * @package WordPress
 * @subpackage AVVENTURE
 * @since AVVENTURE 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js
									<?php
										// Class scheme_xxx need in the <html> as context for the <body>!
										echo ' scheme_' . esc_attr( avventure_get_theme_option( 'color_scheme' ) );
									?>
										">
<head>
	<?php wp_head(); ?>
</head>

<body <?php	body_class(); ?>>

	<?php do_action( 'avventure_action_before_body' ); ?>

	<div class="body_wrap">

		<div class="page_wrap <?php
            if ( !empty(avventure_get_theme_option( 'side_button_text' )) && !empty(avventure_get_theme_option( 'side_button_link' )) ) {
                echo esc_attr('with-side-button');
            } ?>">
			<?php
			// Desktop header
			$avventure_header_type = avventure_get_theme_option( 'header_type' );
			if ( 'custom' == $avventure_header_type && ! avventure_is_layouts_available() ) {
				$avventure_header_type = 'default';
			}
			get_template_part( apply_filters( 'avventure_filter_get_template_part', "templates/header-{$avventure_header_type}" ) );

			// Side menu
			if ( in_array( avventure_get_theme_option( 'menu_style' ), array( 'left', 'right' ) ) ) {
				get_template_part( apply_filters( 'avventure_filter_get_template_part', 'templates/header-navi-side' ) );
			}

			// Mobile menu
			get_template_part( apply_filters( 'avventure_filter_get_template_part', 'templates/header-navi-mobile' ) );
			?>

			<div class="page_content_wrap">

				<?php if ( avventure_get_theme_option( 'body_style' ) != 'fullscreen' ) { ?>
				<div class="content_wrap">
				<?php } ?>

					<?php
					// Widgets area above page content
					avventure_create_widgets_area( 'widgets_above_page' );
					?>

					<div class="content">
						<?php
						// Widgets area inside page content
						avventure_create_widgets_area( 'widgets_above_content' );
