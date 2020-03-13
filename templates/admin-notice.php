<?php
/**
 * The template to display Admin notices
 *
 * @package WordPress
 * @subpackage AVVENTURE
 * @since AVVENTURE 1.0.1
 */

$avventure_theme_obj = wp_get_theme();
?>
<div class="avventure_admin_notice avventure_welcome_notice update-nag">
	<?php
	// Theme image
	$avventure_theme_img = avventure_get_file_url( 'screenshot.jpg' );
	if ( '' != $avventure_theme_img ) {
		?>
		<div class="avventure_notice_image"><img src="<?php echo esc_url( $avventure_theme_img ); ?>" alt="<?php esc_attr_e( 'Theme screenshot', 'avventure' ); ?>"></div>
		<?php
	}

	// Title
	?>
	<h3 class="avventure_notice_title">
		<?php
		echo esc_html(
			sprintf(
				// Translators: Add theme name and version to the 'Welcome' message
				esc_html__( 'Welcome to %1$s v.%2$s', 'avventure' ),
				$avventure_theme_obj->name . ( AVVENTURE_THEME_FREE ? ' ' . esc_html__( 'Free', 'avventure' ) : '' ),
				$avventure_theme_obj->version
			)
		);
		?>
	</h3>
	<?php

	// Description
	?>
	<div class="avventure_notice_text">
		<p class="avventure_notice_text_description">
			<?php
			echo str_replace( '. ', '.<br>', wp_kses_data( $avventure_theme_obj->description ) );
			?>
		</p>
		<p class="avventure_notice_text_info">
			<?php
			echo wp_kses_data( __( 'Attention! Plugin "ThemeREX Addons" is required! Please, install and activate it!', 'avventure' ) );
			?>
		</p>
	</div>
	<?php

	// Buttons
	?>
	<div class="avventure_notice_buttons">
		<?php
		// Link to the page 'About Theme'
		?>
		<a href="<?php echo esc_url( admin_url() . 'themes.php?page=avventure_about' ); ?>" class="button button-primary"><i class="dashicons dashicons-nametag"></i> 
			<?php
			echo esc_html__( 'Install plugin "ThemeREX Addons"', 'avventure' );
			?>
		</a>
		<?php		
		// Dismiss this notice
		?>
		<a href="#" class="avventure_hide_notice"><i class="dashicons dashicons-dismiss"></i> <span class="avventure_hide_notice_text"><?php esc_html_e( 'Dismiss', 'avventure' ); ?></span></a>
	</div>
</div>
