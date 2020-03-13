<?php
/**
 * The Footer: widgets area, logo, footer menu and socials
 *
 * @package WordPress
 * @subpackage AVVENTURE
 * @since AVVENTURE 1.0
 */

						// Widgets area inside page content
						avventure_create_widgets_area( 'widgets_below_content' );
?>				
					</div><!-- </.content> -->

					<?php
					// Show main sidebar
					get_sidebar();

					// Widgets area below page content
					avventure_create_widgets_area( 'widgets_below_page' );

					$avventure_body_style = avventure_get_theme_option( 'body_style' );
					if ( 'fullscreen' != $avventure_body_style ) {
						?>
						</div><!-- </.content_wrap> -->
						<?php
					}
					?>
			</div><!-- </.page_content_wrap> -->

			<?php
			// Footer
			$avventure_footer_type = avventure_get_theme_option( 'footer_type' );
			if ( 'custom' == $avventure_footer_type && ! avventure_is_layouts_available() ) {
				$avventure_footer_type = 'default';
			}
			get_template_part( apply_filters( 'avventure_filter_get_template_part', "templates/footer-{$avventure_footer_type}" ) );
			?>

            <?php
            $avventure_side_button_text = avventure_get_theme_option( 'side_button_text' );
            $avventure_side_button_link = avventure_get_theme_option( 'side_button_link' );
            if ( !empty($avventure_side_button_link ) && !empty($avventure_side_button_text) ) {
                ?><a class="sc_button_side" href="<?php echo esc_url($avventure_side_button_link); ?>">
                <span class="sc_button_text"><span class="sc_button_title"><?php avventure_show_layout($avventure_side_button_text); ?></span></span>
                </a><?php
            }
            ?>

		</div><!-- /.page_wrap -->

	</div><!-- /.body_wrap -->

	<?php wp_footer(); ?>

</body>
</html>
