<?php
/**
 * The template to display the socials in the footer
 *
 * @package WordPress
 * @subpackage AVVENTURE
 * @since AVVENTURE 1.0.10
 */


// Socials
if ( avventure_is_on( avventure_get_theme_option( 'socials_in_footer' ) ) ) {
	$avventure_output = avventure_get_socials_links();
	if ( '' != $avventure_output ) {
		?>
		<div class="footer_socials_wrap socials_wrap">
			<div class="footer_socials_inner">
				<?php avventure_show_layout( $avventure_output ); ?>
			</div>
		</div>
		<?php
	}
}
