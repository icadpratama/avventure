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
<div class="avventure_admin_notice avventure_rate_notice update-nag">
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
	<h3 class="avventure_notice_title"><a href="<?php echo esc_url( avventure_storage_get( 'theme_download_url' ) ); ?>" target="_blank">
		<?php
		echo esc_html(
			sprintf(
				// Translators: Add theme name and version to the 'Welcome' message
				esc_html__( 'Rate our theme "%s", please', 'avventure' ),
				$avventure_theme_obj->name . ( AVVENTURE_THEME_FREE ? ' ' . esc_html__( 'Free', 'avventure' ) : '' )
			)
		);
		?>
	</a></h3>
	<?php

	// Description
	?>
	<div class="avventure_notice_text">
		<p><?php echo wp_kses_data( __( 'We are glad you chose our WP theme for your website. You’ve done well customizing your website and we hope that you’ve enjoyed working with our theme.', 'avventure' ) ); ?></p>
		<p><?php echo wp_kses_data( __( 'It would be just awesome if you spend just a minute of your time to rate our theme or the customer service you’ve received from us.', 'avventure' ) ); ?></p>
		<p class="avventure_notice_text_info"><?php echo wp_kses_data( __( '* We love receiving 5-star ratings, because our CEO Henry Rise gives $5 to homeless dog shelter for every 5-star rating we get! Save the planet with us!', 'avventure' ) ); ?></p>
	</div>
	<?php

	// Buttons
	?>
	<div class="avventure_notice_buttons">
		<?php
		// Link to the theme download page
		?>
		<a href="<?php echo esc_url( avventure_storage_get( 'theme_download_url' ) ); ?>" class="button button-primary" target="_blank"><i class="dashicons dashicons-star-filled"></i> 
			<?php
			// Translators: Add theme name
			echo esc_html( sprintf( esc_html_e( 'Rate theme %s', 'avventure' ), $avventure_theme_obj->name ) );
			?>
		</a>
		<?php
		// Link to the theme support
		?>
		<a href="<?php echo esc_url( avventure_storage_get( 'theme_support_url' ) ); ?>" class="button" target="_blank"><i class="dashicons dashicons-sos"></i> 
			<?php
			esc_html_e( 'Support', 'avventure' );
			?>
		</a>
		<?php
		// Link to the theme documentation
		?>
		<a href="<?php echo esc_url( avventure_storage_get( 'theme_doc_url' ) ); ?>" class="button" target="_blank"><i class="dashicons dashicons-book"></i> 
			<?php
			esc_html_e( 'Documentation', 'avventure' );
			?>
		</a>
		<?php
		// Dismiss
		?>
		<a href="#" class="avventure_hide_notice"><i class="dashicons dashicons-dismiss"></i> <span class="avventure_hide_notice_text"><?php esc_html_e( 'Dismiss', 'avventure' ); ?></span></a>
	</div>
</div>
