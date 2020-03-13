<article <?php post_class( 'post_item_single post_item_404' ); ?>>
	<div class="post_content">
		<h1 class="page_title"><?php //esc_html_e( '404', 'avventure' ); ?>4<span class="icon-island"></span>4</h1>
		<div class="page_info">
			<h1 class="page_subtitle"><?php esc_html_e( 'Adventure Is Out There', 'avventure' ); ?></h1>
			<p class="page_description"><?php echo wp_kses_post( __( "We're sorry, but something went wrong.", 'avventure' ) ); ?></p>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="go_home theme_button"><?php esc_html_e( 'Homepage', 'avventure' ); ?></a>
		</div>
	</div>
</article>
