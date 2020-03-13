<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package WordPress
 * @subpackage AVVENTURE
 * @since AVVENTURE 1.0
 */

if ( avventure_sidebar_present() ) {
	ob_start();
	$avventure_sidebar_name = avventure_get_theme_option( 'sidebar_widgets' );
	avventure_storage_set( 'current_sidebar', 'sidebar' );
	if ( is_active_sidebar( $avventure_sidebar_name ) ) {
		dynamic_sidebar( $avventure_sidebar_name );
	}
	$avventure_out = trim( ob_get_contents() );
	ob_end_clean();
	if ( ! empty( $avventure_out ) ) {
		$avventure_sidebar_position = avventure_get_theme_option( 'sidebar_position' );
		?>
		<div class="sidebar widget_area
			<?php
			echo esc_attr( $avventure_sidebar_position );
			if ( ! avventure_is_inherit( avventure_get_theme_option( 'sidebar_scheme' ) ) ) {
				echo ' scheme_' . esc_attr( avventure_get_theme_option( 'sidebar_scheme' ) );
			}
			?>
		" role="complementary">
			<div class="sidebar_inner">
				<?php
				do_action( 'avventure_action_before_sidebar' );
				avventure_show_layout( preg_replace( "/<\/aside>[\r\n\s]*<aside/", '</aside><aside', $avventure_out ) );
				do_action( 'avventure_action_after_sidebar' );
				?>
			</div><!-- /.sidebar_inner -->
		</div><!-- /.sidebar -->
		<?php
	}
}
