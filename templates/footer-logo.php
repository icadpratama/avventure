<?php
/**
 * The template to display the site logo in the footer
 *
 * @package WordPress
 * @subpackage AVVENTURE
 * @since AVVENTURE 1.0.10
 */

// Logo
if ( avventure_is_on( avventure_get_theme_option( 'logo_in_footer' ) ) ) {
	$avventure_logo_image = avventure_get_logo_image( 'footer' );
	$avventure_logo_text  = get_bloginfo( 'name' );
	if ( ! empty( $avventure_logo_image ) || ! empty( $avventure_logo_text ) ) {
		?>
		<div class="footer_logo_wrap">
			<div class="footer_logo_inner">
				<?php
				if ( ! empty( $avventure_logo_image ) ) {
					$avventure_attr = avventure_getimagesize( $avventure_logo_image );
					echo '<a href="' . esc_url( home_url( '/' ) ) . '">'
							. '<img src="' . esc_url( $avventure_logo_image ) . '"'
								. ' class="logo_footer_image"'
								. ' alt="' . esc_attr__( 'Site logo', 'avventure' ) . '"'
								. ( ! empty( $avventure_attr[3] ) ? ' ' . wp_kses_data( $avventure_attr[3] ) : '' )
							. '>'
						. '</a>';
				} elseif ( ! empty( $avventure_logo_text ) ) {
					echo '<h1 class="logo_footer_text">'
							. '<a href="' . esc_url( home_url( '/' ) ) . '">'
								. esc_html( $avventure_logo_text )
							. '</a>'
						. '</h1>';
				}
				?>
			</div>
		</div>
		<?php
	}
}
