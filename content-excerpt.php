<?php
/**
 * The default template to display the content
 *
 * Used for index/archive/search.
 *
 * @package WordPress
 * @subpackage AVVENTURE
 * @since AVVENTURE 1.0
 */

$avventure_template_args = get_query_var( 'avventure_template_args' );
if ( is_array( $avventure_template_args ) ) {
	$avventure_columns    = empty( $avventure_template_args['columns'] ) ? 2 : max( 2, $avventure_template_args['columns'] );
	$avventure_blog_style = array( $avventure_template_args['type'], $avventure_columns );
	if ( ! empty( $avventure_template_args['slider'] ) ) {
		?><div class="slider-slide swiper-slide">
		<?php
	} elseif ( $avventure_columns > 1 ) {
		?>
		<div class="column-1_<?php echo esc_attr( $avventure_columns ); ?>">
		<?php
	}
}
$avventure_expanded    = ! avventure_sidebar_present() && avventure_is_on( avventure_get_theme_option( 'expand_content' ) );
$avventure_post_format = get_post_format();
$avventure_post_format = empty( $avventure_post_format ) ? 'standard' : str_replace( 'post-format-', '', $avventure_post_format );
$avventure_animation   = avventure_get_theme_option( 'blog_animation' );

?>
<article id="post-<?php the_ID(); ?>" 
	<?php post_class( 'post_item post_layout_excerpt post_format_' . esc_attr( $avventure_post_format ) ); ?>
	<?php echo ( ! avventure_is_off( $avventure_animation ) && empty( $avventure_template_args['slider'] ) ? ' data-animation="' . esc_attr( avventure_get_animation_classes( $avventure_animation ) ) . '"' : '' ); ?>
	>
	<?php

	// Featured image
	$avventure_hover = ! empty( $avventure_template_args['hover'] ) && ! avventure_is_inherit( $avventure_template_args['hover'] )
						? $avventure_template_args['hover']
						: avventure_get_theme_option( 'image_hover' );
	avventure_show_post_featured(
		array(
			'singular'   => false,
			'no_links'   => ! empty( $avventure_template_args['no_links'] ),
			'hover'      => $avventure_hover,
			'thumb_size' => avventure_get_thumb_size( strpos( avventure_get_theme_option( 'body_style' ), 'full' ) !== false ? 'full' : ( $avventure_expanded ? 'huge' : 'big' ) ),
            'show_categories' => true,
            )
	);

	if ( (get_post_type() === 'give_forms') && function_exists('give_get_currency') ) {
	    avventure_show_post_meta(array( 'components' => 'date' ) );
	    
	    $get_post_meta_give_forms = get_post_meta( get_the_id());
	    if ( isset($get_post_meta_give_forms['_give_form_earnings']) && isset($get_post_meta_give_forms['_give_set_goal']) ) {
            if ($get_post_meta_give_forms['_give_form_earnings'][0] > 0 && round($get_post_meta_give_forms['_give_set_goal'][0]) > 0) {
                $get_post_meta_give_forms_percent = ( $get_post_meta_give_forms['_give_form_earnings'][0] / round($get_post_meta_give_forms['_give_set_goal'][0])) * 100;
            } else {
                $get_post_meta_give_forms_percent = 0;
            }
            $output = esc_html__("Group Goal: ", 'avventure')
                    . round($get_post_meta_give_forms['_give_set_goal'][0]) . ' ' . give_get_currency()
                    . '  /  '
                    . esc_html__("Raised: ", 'avventure')
                    . $get_post_meta_give_forms['_give_form_earnings'][0] . ' ' . give_get_currency()
                    . ' (' . round($get_post_meta_give_forms_percent) . '%)' ;
            avventure_show_layout( $output,'<div class="post_give_form_info">', '</div>');
	    }
    }
	
	// Title and post meta
	if ( get_the_title() != '' ) {
		?>
		<div class="post_header entry-header">
			<?php

            // Sticky label
            if ( is_sticky() && ! is_paged() ) {
                ?>
                <span class="post_label_title label_sticky_title"><?php esc_html_e('sticky post', 'avventure'); ?></span>
                <?php
            }
            
			do_action( 'avventure_action_before_post_title' );

			// Post title
			if ( empty( $avventure_template_args['no_links'] ) ) {
				the_title( sprintf( '<h4 class="post_title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h4>' );
			} else {
				the_title( '<h4 class="post_title entry-title">', '</h4>' );
			}

			do_action( 'avventure_action_before_post_meta' );

			// Post meta
			$avventure_components = avventure_array_get_keys_by_value( avventure_get_theme_option( 'meta_parts' ) );
			$avventure_counters   = avventure_array_get_keys_by_value( avventure_get_theme_option( 'counters' ) );

			if ( ! empty( $avventure_components ) && ! in_array( $avventure_hover, array( 'border', 'pull', 'slide', 'fade' ) ) ) {
				avventure_show_post_meta(
					apply_filters(
						'avventure_filter_post_meta_args', array(
							'components' => $avventure_components,
							'counters'   => $avventure_counters,
							'seo'        => false,
						), 'excerpt', 1
					)
				);
			}
			?>
		</div><!-- .post_header -->
		<?php
	}

	// Post content
	if ( empty( $avventure_template_args['hide_excerpt'] ) ) {

		?>
		<div class="post_content entry-content">
		<?php
		if ( avventure_get_theme_option( 'blog_content' ) == 'fullpost' ) {
			// Post content area
			?>
				<div class="post_content_inner">
				<?php
				the_content( '' );
				?>
				</div>
				<?php
				// Inner pages
				wp_link_pages(
					array(
						'before'      => '<div class="page_links"><span class="page_links_title">' . esc_html__( 'Pages:', 'avventure' ) . '</span>',
						'after'       => '</div>',
						'link_before' => '<span>',
						'link_after'  => '</span>',
						'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'avventure' ) . ' </span>%',
						'separator'   => '<span class="screen-reader-text">, </span>',
					)
				);
		} else {
			// Post content area
			?>
				<div class="post_content_inner">
				<?php
				if ( has_excerpt() ) {
						the_excerpt();
				} elseif ( strpos( get_the_content( '!--more' ), '!--more' ) !== false ) {
						the_content( '' );
				} elseif ( in_array( $avventure_post_format, array( 'link', 'aside', 'status' ) ) ) {
										the_content();
				} elseif ( 'quote' == $avventure_post_format ) {
					$quote = avventure_get_tag( get_the_content(), '<blockquote>', '</blockquote>' );
					if ( ! empty( $quote ) ) {
						avventure_show_layout( wpautop( $quote ) );
					} else {
						the_excerpt();
					}
				} elseif ( substr( get_the_content(), 0, 4 ) != '[vc_' ) {
					the_excerpt();
				}
				?>
				</div>
				<?php
				// More button
				if ( empty( $avventure_template_args['no_links'] ) && ! in_array( $avventure_post_format, array( 'link', 'aside', 'status', 'quote' ) ) ) {
                    if ( get_post_type() !== 'give_forms' ) {
					    ?><p><a class="more-link" href="<?php echo esc_url( get_permalink() ); ?>"><?php esc_html_e( 'Read more', 'avventure' ); ?></a></p><?php
                    } else {
                        ?><div class="sc_blogger_item_button"><a class="sc_button sc_button_default sc_button_size_normal sc_button_icon_right" href="<?php echo esc_url( get_permalink() ); ?>">
                            <span class="sc_button_text"><span class="sc_button_title"><?php echo esc_html__("Donate", 'avventure'); ?></span></span>
                        </a></div><?php
                    }
				}
		}
		?>
		</div><!-- .entry-content -->
		<?php
	}
	?>
	</article>
<?php

if ( is_array( $avventure_template_args ) ) {
	if ( ! empty( $avventure_template_args['slider'] ) || $avventure_columns > 1 ) {
		?>
		</div>
		<?php
	}
}
