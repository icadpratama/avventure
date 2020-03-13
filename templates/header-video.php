<?php
/**
 * The template to display the background video in the header
 *
 * @package WordPress
 * @subpackage AVVENTURE
 * @since AVVENTURE 1.0.14
 */
$avventure_header_video = avventure_get_header_video();
$avventure_embed_video  = '';
if ( ! empty( $avventure_header_video ) && ! avventure_is_from_uploads( $avventure_header_video ) ) {
	if ( avventure_is_youtube_url( $avventure_header_video ) && preg_match( '/[=\/]([^=\/]*)$/', $avventure_header_video, $matches ) && ! empty( $matches[1] ) ) {
		?><div id="background_video" data-youtube-code="<?php echo esc_attr( $matches[1] ); ?>"></div>
		<?php
	} else {
		global $wp_embed;
		if ( false && is_object( $wp_embed ) ) {
			$avventure_embed_video = do_shortcode( $wp_embed->run_shortcode( '[embed]' . trim( $avventure_header_video ) . '[/embed]' ) );
			$avventure_embed_video = avventure_make_video_autoplay( $avventure_embed_video );
		} else {
			$avventure_header_video = str_replace( '/watch?v=', '/embed/', $avventure_header_video );
			$avventure_header_video = avventure_add_to_url(
				$avventure_header_video, array(
					'feature'        => 'oembed',
					'controls'       => 0,
					'autoplay'       => 1,
					'showinfo'       => 0,
					'modestbranding' => 1,
					'wmode'          => 'transparent',
					'enablejsapi'    => 1,
					'origin'         => home_url(),
					'widgetid'       => 1,
				)
			);
			$avventure_embed_video  = '<iframe src="' . esc_url( $avventure_header_video ) . '" width="1170" height="658" allowfullscreen="0" frameborder="0"></iframe>';
		}
		?>
		<div id="background_video"><?php avventure_show_layout( $avventure_embed_video ); ?></div>
		<?php
	}
}
