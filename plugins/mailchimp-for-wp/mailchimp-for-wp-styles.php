<?php
// Add plugin-specific colors and fonts to the custom CSS
if ( ! function_exists( 'avventure_mailchimp_get_css' ) ) {
	add_filter( 'avventure_filter_get_css', 'avventure_mailchimp_get_css', 10, 2 );
	function avventure_mailchimp_get_css( $css, $args ) {

		if ( isset( $css['fonts'] ) && isset( $args['fonts'] ) ) {
			$fonts         = $args['fonts'];
			$css['fonts'] .= <<<CSS
form.mc4wp-form .mc4wp-form-fields input[type="email"] {
	{$fonts['input_font-family']}
	{$fonts['input_font-size']}
	{$fonts['input_font-weight']}
	{$fonts['input_font-style']}
	{$fonts['input_line-height']}
	{$fonts['input_text-decoration']}
	{$fonts['input_text-transform']}
	{$fonts['input_letter-spacing']}
}
form.mc4wp-form .mc4wp-form-fields input[type="submit"] {
	{$fonts['button_font-family']}
	{$fonts['button_font-size']}
	{$fonts['button_font-weight']}
	{$fonts['button_font-style']}
	{$fonts['button_line-height']}
	{$fonts['button_text-decoration']}
	{$fonts['button_text-transform']}
	{$fonts['button_letter-spacing']}
}

CSS;
		}

		if ( isset( $css['colors'] ) && isset( $args['colors'] ) ) {
			$colors         = $args['colors'];
			$css['colors'] .= <<<CSS

form.mc4wp-form .mc4wp-alert {
	background-color: {$colors['text_link']};
	border-color: {$colors['text_hover']};
	color: {$colors['inverse_text']};
}
form.mc4wp-form .mc4wp-alert a {
	color: {$colors['inverse_link']} !important;	
}
form.mc4wp-form .mc4wp-alert a:hover {
	color: {$colors['inverse_hover']} !important;	
}
.mailchimp_form input,
.mailchimp_form input::placeholder{
	background: {$colors['text_link']} !important;
	border-color: {$colors['bg_color']} !important;
	color: {$colors['bg_color']} !important;
}
.mailchimp_form button{
	background: {$colors['bg_color']} !important;
	border-color: {$colors['bg_color']};
	color: {$colors['text_dark']};
}
.mailchimp_form button:hover{
	background: {$colors['text_hover2']} !important;
	border-color: {$colors['text_hover2']};
	color: {$colors['text_dark']};
}

form.mc4wp-form .mcfwp-agree-input > span a:hover {
    color: {$colors['text_dark']};
}
form.mc4wp-form .mcfwp-agree-input > span a {
    color: {$colors['inverse_text']};
}
.mc4wp-form-fields label > input[type="checkbox"]:checked + span:before {
	color: {$colors['text_link2']};
}

.light-mailchimp form.mc4wp-form .mc4wp-alert {
	background-color: {$colors['text_link']};
	border-color: {$colors['text_hover']};
	color: {$colors['inverse_text']};
}
.light-mailchimp form.mc4wp-form .mc4wp-alert a {
	color: {$colors['inverse_link']} !important;	
}
.light-mailchimp form.mc4wp-form .mc4wp-alert a:hover {
	color: {$colors['inverse_hover']} !important;	
}
.light-mailchimp .mailchimp_form input,
.light-mailchimp .mailchimp_form input::placeholder{
	background: {$colors['bg_color']} !important;
	border-color: {$colors['bd_color']} !important;
	color: {$colors['text']} !important;
}
.light-mailchimp .mailchimp_form button{
	background: {$colors['text_link']} !important;
	border-color: {$colors['text_link']};
	color: {$colors['text_dark']};
}
.light-mailchimp .mailchimp_form button:hover{
	background: {$colors['text_hover']} !important;
	border-color: {$colors['text_hover']};
	color: {$colors['text_dark']};
}

.light-mailchimp form.mc4wp-form .mcfwp-agree-input > span a:hover {
    color: {$colors['text_dark']};
}
.light-mailchimp form.mc4wp-form .mcfwp-agree-input > span a {
    color: {$colors['text']};
}



CSS;
		}

		return $css;
	}
}

