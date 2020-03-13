<?php
/**
 * The template to display the logo or the site name and the slogan in the Header
 *
 * @package WordPress
 * @subpackage AVVENTURE
 * @since AVVENTURE 1.0
 */

$avventure_args = get_query_var( 'avventure_logo_args' );

// Site logo
$avventure_logo_type   = isset( $avventure_args['type'] ) ? $avventure_args['type'] : '';
$avventure_logo_image  = avventure_get_logo_image( $avventure_logo_type );
$avventure_logo_text   = avventure_is_on( avventure_get_theme_option( 'logo_text' ) ) ? get_bloginfo( 'name' ) : '';
$avventure_logo_slogan = get_bloginfo( 'description', 'display' );
if ( ! empty( $avventure_logo_image ) || ! empty( $avventure_logo_text ) ) {
	?><a class="sc_layouts_logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
		<?php
		if ( ! empty( $avventure_logo_image ) ) {
			if ( empty( $avventure_logo_type ) && function_exists( 'the_custom_logo' ) && (int) $avventure_logo_image > 0 ) {
				the_custom_logo();
			} else {
				$avventure_attr = avventure_getimagesize( $avventure_logo_image );
				echo '<img src="' . esc_url( $avventure_logo_image ) . '" alt="' . esc_attr( $avventure_logo_text ) . '"' . ( ! empty( $avventure_attr[3] ) ? ' ' . wp_kses_data( $avventure_attr[3] ) : '' ) . '>';
			}
		} else {
			avventure_show_layout( avventure_prepare_macros( $avventure_logo_text ), '<span class="logo_text">', '</span>' );
			avventure_show_layout( avventure_prepare_macros( $avventure_logo_slogan ), '<span class="logo_slogan">', '</span>' );
		}
		?>
	</a>
	<?php
}
