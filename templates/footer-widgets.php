<?php
/**
 * The template to display the widgets area in the footer
 *
 * @package WordPress
 * @subpackage AVVENTURE
 * @since AVVENTURE 1.0.10
 */

// Footer sidebar
$avventure_footer_name    = avventure_get_theme_option( 'footer_widgets' );
$avventure_footer_present = ! avventure_is_off( $avventure_footer_name ) && is_active_sidebar( $avventure_footer_name );
if ( $avventure_footer_present ) {
	avventure_storage_set( 'current_sidebar', 'footer' );
	$avventure_footer_wide = avventure_get_theme_option( 'footer_wide' );
	ob_start();
	if ( is_active_sidebar( $avventure_footer_name ) ) {
		dynamic_sidebar( $avventure_footer_name );
	}
	$avventure_out = trim( ob_get_contents() );
	ob_end_clean();
	if ( ! empty( $avventure_out ) ) {
		$avventure_out          = preg_replace( "/<\\/aside>[\r\n\s]*<aside/", '</aside><aside', $avventure_out );
		$avventure_need_columns = true;   //or check: strpos($avventure_out, 'columns_wrap')===false;
		if ( $avventure_need_columns ) {
			$avventure_columns = max( 0, (int) avventure_get_theme_option( 'footer_columns' ) );
			if ( 0 == $avventure_columns ) {
				$avventure_columns = min( 4, max( 1, substr_count( $avventure_out, '<aside ' ) ) );
			}
			if ( $avventure_columns > 1 ) {
				$avventure_out = preg_replace( '/<aside([^>]*)class="widget/', '<aside$1class="column-1_' . esc_attr( $avventure_columns ) . ' widget', $avventure_out );
			} else {
				$avventure_need_columns = false;
			}
		}
		?>
		<div class="footer_widgets_wrap widget_area<?php echo ! empty( $avventure_footer_wide ) ? ' footer_fullwidth' : ''; ?> sc_layouts_row sc_layouts_row_type_normal">
			<div class="footer_widgets_inner widget_area_inner">
				<?php
				if ( ! $avventure_footer_wide ) {
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
				avventure_show_layout( $avventure_out );
				do_action( 'avventure_action_after_sidebar' );
				if ( $avventure_need_columns ) {
					?>
					</div><!-- /.columns_wrap -->
					<?php
				}
				if ( ! $avventure_footer_wide ) {
					?>
					</div><!-- /.content_wrap -->
					<?php
				}
				?>
			</div><!-- /.footer_widgets_inner -->
		</div><!-- /.footer_widgets_wrap -->
		<?php
	}
}
