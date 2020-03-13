<?php
/**
 * The template for homepage posts with "Portfolio" style
 *
 * @package WordPress
 * @subpackage AVVENTURE
 * @since AVVENTURE 1.0
 */

avventure_storage_set( 'blog_archive', true );

get_header();

if ( have_posts() ) {

	avventure_blog_archive_start();

	$avventure_stickies   = is_home() ? get_option( 'sticky_posts' ) : false;
	$avventure_sticky_out = avventure_get_theme_option( 'sticky_style' ) == 'columns'
							&& is_array( $avventure_stickies ) && count( $avventure_stickies ) > 0 && get_query_var( 'paged' ) < 1;

	// Show filters
	$avventure_cat          = avventure_get_theme_option( 'parent_cat' );
	$avventure_post_type    = avventure_get_theme_option( 'post_type' );
	$avventure_taxonomy     = avventure_get_post_type_taxonomy( $avventure_post_type );
	$avventure_show_filters = avventure_get_theme_option( 'show_filters' );
	$avventure_tabs         = array();
	if ( ! avventure_is_off( $avventure_show_filters ) ) {
		$avventure_args           = array(
			'type'         => $avventure_post_type,
			'child_of'     => $avventure_cat,
			'orderby'      => 'name',
			'order'        => 'ASC',
			'hide_empty'   => 1,
			'hierarchical' => 0,
			'taxonomy'     => $avventure_taxonomy,
			'pad_counts'   => false,
		);
		$avventure_portfolio_list = get_terms( $avventure_args );
		if ( is_array( $avventure_portfolio_list ) && count( $avventure_portfolio_list ) > 0 ) {
			$avventure_tabs[ $avventure_cat ] = esc_html__( 'All', 'avventure' );
			foreach ( $avventure_portfolio_list as $avventure_term ) {
				if ( isset( $avventure_term->term_id ) ) {
					$avventure_tabs[ $avventure_term->term_id ] = $avventure_term->name;
				}
			}
		}
	}
	if ( count( $avventure_tabs ) > 0 ) {
		$avventure_portfolio_filters_ajax   = true;
		$avventure_portfolio_filters_active = $avventure_cat;
		$avventure_portfolio_filters_id     = 'portfolio_filters';
		?>
		<div class="portfolio_filters avventure_tabs avventure_tabs_ajax">
			<ul class="portfolio_titles avventure_tabs_titles">
				<?php
				foreach ( $avventure_tabs as $avventure_id => $avventure_title ) {
					?>
					<li><a href="<?php echo esc_url( avventure_get_hash_link( sprintf( '#%s_%s_content', $avventure_portfolio_filters_id, $avventure_id ) ) ); ?>" data-tab="<?php echo esc_attr( $avventure_id ); ?>"><?php echo esc_html( $avventure_title ); ?></a></li>
					<?php
				}
				?>
			</ul>
			<?php
			$avventure_ppp = avventure_get_theme_option( 'posts_per_page' );
			if ( avventure_is_inherit( $avventure_ppp ) ) {
				$avventure_ppp = '';
			}
			foreach ( $avventure_tabs as $avventure_id => $avventure_title ) {
				$avventure_portfolio_need_content = $avventure_id == $avventure_portfolio_filters_active || ! $avventure_portfolio_filters_ajax;
				?>
				<div id="<?php echo esc_attr( sprintf( '%s_%s_content', $avventure_portfolio_filters_id, $avventure_id ) ); ?>"
					class="portfolio_content avventure_tabs_content"
					data-blog-template="<?php echo esc_attr( avventure_storage_get( 'blog_template' ) ); ?>"
					data-blog-style="<?php echo esc_attr( avventure_get_theme_option( 'blog_style' ) ); ?>"
					data-posts-per-page="<?php echo esc_attr( $avventure_ppp ); ?>"
					data-post-type="<?php echo esc_attr( $avventure_post_type ); ?>"
					data-taxonomy="<?php echo esc_attr( $avventure_taxonomy ); ?>"
					data-cat="<?php echo esc_attr( $avventure_id ); ?>"
					data-parent-cat="<?php echo esc_attr( $avventure_cat ); ?>"
					data-need-content="<?php echo ( false === $avventure_portfolio_need_content ? 'true' : 'false' ); ?>"
				>
					<?php
					if ( $avventure_portfolio_need_content ) {
						avventure_show_portfolio_posts(
							array(
								'cat'        => $avventure_id,
								'parent_cat' => $avventure_cat,
								'taxonomy'   => $avventure_taxonomy,
								'post_type'  => $avventure_post_type,
								'page'       => 1,
								'sticky'     => $avventure_sticky_out,
							)
						);
					}
					?>
				</div>
				<?php
			}
			?>
		</div>
		<?php
	} else {
		avventure_show_portfolio_posts(
			array(
				'cat'        => $avventure_cat,
				'parent_cat' => $avventure_cat,
				'taxonomy'   => $avventure_taxonomy,
				'post_type'  => $avventure_post_type,
				'page'       => 1,
				'sticky'     => $avventure_sticky_out,
			)
		);
	}

	avventure_blog_archive_end();

} else {

	if ( is_search() ) {
		get_template_part( apply_filters( 'avventure_filter_get_template_part', 'content', 'none-search' ), 'none-search' );
	} else {
		get_template_part( apply_filters( 'avventure_filter_get_template_part', 'content', 'none-archive' ), 'none-archive' );
	}
}

get_footer();
