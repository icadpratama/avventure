<?php
/**
 * The template to display the widgets area in the header
 *
 * @package WordPress
 * @subpackage AVVENTURE
 * @since AVVENTURE 1.0
 */

// Header sidebar
$avventure_header_name    = avventure_get_theme_option( 'header_widgets' );
$avventure_header_present = ! avventure_is_off( $avventure_header_name ) && is_active_sidebar( $avventure_header_name );
if ( $avventure_header_present ) {
	avventure_storage_set( 'current_sidebar', 'header' );
	$avventure_header_wide = avventure_get_theme_option( 'header_wide' );
	ob_start();
	if ( is_active_sidebar( $avventure_header_name ) ) {
		dynamic_sidebar( $avventure_header_name );
	}
	$avventure_widgets_output = ob_get_contents();
	ob_end_clean();
	if ( ! empty( $avventure_widgets_output ) ) {
		$avventure_widgets_output = preg_replace( "/<\/aside>[\r\n\s]*<aside/", '</aside><aside', $avventure_widgets_output );
		$avventure_need_columns   = strpos( $avventure_widgets_output, 'columns_wrap' ) === false;
		if ( $avventure_need_columns ) {
			$avventure_columns = max( 0, (int) avventure_get_theme_option( 'header_columns' ) );
			if ( 0 == $avventure_columns ) {
				$avventure_columns = min( 6, max( 1, substr_count( $avventure_widgets_output, '<aside ' ) ) );
			}
			if ( $avventure_columns > 1 ) {
				$avventure_widgets_output = preg_replace( '/<aside([^>]*)class="widget/', '<aside$1class="column-1_' . esc_attr( $avventure_columns ) . ' widget', $avventure_widgets_output );
			} else {
				$avventure_need_columns = false;
			}
		}
		?>
		<div class="header_widgets_wrap widget_area<?php echo ! empty( $avventure_header_wide ) ? ' header_fullwidth' : ' header_boxed'; ?>">
			<div class="header_widgets_inner widget_area_inner">
				<?php
				if ( ! $avventure_header_wide ) {
					?>
					<div class="content_wrap">
					<?php
				}
				if ( $avventure_need_columns ) {
					?>
					<div class="columns_wrap">
					<?php
				}
				do_action( 'avventure_action_before_sidebar' );
				avventure_show_layout( $avventure_widgets_output );
				do_action( 'avventure_action_after_sidebar' );
				if ( $avventure_need_columns ) {
					?>
					</div>	<!-- /.columns_wrap -->
					<?php
				}
				if ( ! $avventure_header_wide ) {
					?>
					</div>	<!-- /.content_wrap -->
					<?php
				}
				?>
			</div>	<!-- /.header_widgets_inner -->
		</div>	<!-- /.header_widgets_wrap -->
		<?php
	}
}
