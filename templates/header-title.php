<?php
/**
 * The template to display the page title and breadcrumbs
 *
 * @package WordPress
 * @subpackage AVVENTURE
 * @since AVVENTURE 1.0
 */

// Page (category, tag, archive, author) title

if ( avventure_need_page_title() ) {
	avventure_sc_layouts_showed( 'title', true );
	avventure_sc_layouts_showed( 'postmeta', true );
	?>
	<div class="top_panel_title sc_layouts_row sc_layouts_row_type_normal">
		<div class="content_wrap">
			<div class="sc_layouts_column sc_layouts_column_align_center">
				<div class="sc_layouts_item">
					<div class="sc_layouts_title sc_align_center">
						<?php
						// Post meta on the single post
						if ( is_single() ) {
							?>
							<div class="sc_layouts_title_meta">
							<?php
								avventure_show_post_meta(
									apply_filters(
										'avventure_filter_post_meta_args', array(
											'components' => avventure_array_get_keys_by_value( avventure_get_theme_option( 'meta_parts' ) ),
											'counters'   => avventure_array_get_keys_by_value( avventure_get_theme_option( 'counters' ) ),
											'seo'        => avventure_is_on( avventure_get_theme_option( 'seo_snippets' ) ),
										), 'header', 1
									)
								);
							?>
							</div>
							<?php
						}

						// Blog/Post title
						?>
						<div class="sc_layouts_title_title">
							<?php
							$avventure_blog_title           = avventure_get_blog_title();
							$avventure_blog_title_text      = '';
							$avventure_blog_title_class     = '';
							$avventure_blog_title_link      = '';
							$avventure_blog_title_link_text = '';
							if ( is_array( $avventure_blog_title ) ) {
								$avventure_blog_title_text      = $avventure_blog_title['text'];
								$avventure_blog_title_class     = ! empty( $avventure_blog_title['class'] ) ? ' ' . $avventure_blog_title['class'] : '';
								$avventure_blog_title_link      = ! empty( $avventure_blog_title['link'] ) ? $avventure_blog_title['link'] : '';
								$avventure_blog_title_link_text = ! empty( $avventure_blog_title['link_text'] ) ? $avventure_blog_title['link_text'] : '';
							} else {
								$avventure_blog_title_text = $avventure_blog_title;
							}
							?>
							<h1 itemprop="headline" class="sc_layouts_title_caption<?php echo esc_attr( $avventure_blog_title_class ); ?>">
								<?php
								$avventure_top_icon = avventure_get_category_icon();
								if ( ! empty( $avventure_top_icon ) ) {
									$avventure_attr = avventure_getimagesize( $avventure_top_icon );
									?>
									<img src="<?php echo esc_url( $avventure_top_icon ); ?>" alt="<?php esc_attr_e( 'Site icon', 'avventure' ); ?>"
										<?php
										if ( ! empty( $avventure_attr[3] ) ) {
											avventure_show_layout( $avventure_attr[3] );
										}
										?>
									>
									<?php
								}
								echo wp_kses_data( $avventure_blog_title_text );
								?>
							</h1>
							<?php
							if ( ! empty( $avventure_blog_title_link ) && ! empty( $avventure_blog_title_link_text ) ) {
								?>
								<a href="<?php echo esc_url( $avventure_blog_title_link ); ?>" class="theme_button theme_button_small sc_layouts_title_link"><?php echo esc_html( $avventure_blog_title_link_text ); ?></a>
								<?php
							}

							// Category/Tag description
							if ( is_category() || is_tag() || is_tax() ) {
								the_archive_description( '<div class="sc_layouts_title_description">', '</div>' );
							}

							?>
						</div>
						<?php

						// Breadcrumbs
						?>
						<div class="sc_layouts_title_breadcrumbs">
							<?php
							do_action( 'avventure_action_breadcrumbs' );
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}
?>
