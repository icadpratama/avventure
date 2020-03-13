<?php
/**
 * The template to display the copyright info in the footer
 *
 * @package WordPress
 * @subpackage AVVENTURE
 * @since AVVENTURE 1.0.10
 */

// Copyright area
?> 
<div class="footer_copyright_wrap
<?php
if ( ! avventure_is_inherit( avventure_get_theme_option( 'copyright_scheme' ) ) ) {
	echo ' scheme_' . esc_attr( avventure_get_theme_option( 'copyright_scheme' ) );
}
?>
				">
	<div class="footer_copyright_inner">
		<div class="content_wrap">
			<div class="copyright_text">
			<?php
				$avventure_copyright = avventure_get_theme_option( 'copyright' );
			if ( ! empty( $avventure_copyright ) ) {
				// Replace {{Y}} or {Y} with the current year
				$avventure_copyright = str_replace( array( '{{Y}}', '{Y}' ), date( 'Y' ), $avventure_copyright );
				// Replace {{...}} and ((...)) on the <i>...</i> and <b>...</b>
				$avventure_copyright = avventure_prepare_macros( $avventure_copyright );
				// Display copyright
				echo wp_kses_post( nl2br( $avventure_copyright ) );
			}
			?>
			</div>
		</div>
	</div>
</div>
