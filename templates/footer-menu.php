<?php
/**
 * The template to display menu in the footer
 *
 * @package WordPress
 * @subpackage AVVENTURE
 * @since AVVENTURE 1.0.10
 */

// Footer menu
$avventure_menu_footer = avventure_get_nav_menu(
	array(
		'location' => 'menu_footer',
		'class'    => 'sc_layouts_menu sc_layouts_menu_default',
	),'',1
);
if ( ! empty( $avventure_menu_footer ) ) {
	?>
	<div class="footer_menu_wrap">
		<div class="footer_menu_inner">
			<?php avventure_show_layout( $avventure_menu_footer ); ?>
		</div>
	</div>
	<?php
}
