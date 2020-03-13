<?php
/* Give (donation forms) support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'avventure_give_theme_setup9' ) ) {
    add_action( 'after_setup_theme', 'avventure_give_theme_setup9', 9 );
    function avventure_give_theme_setup9() {

        add_filter( 'avventure_filter_merge_styles', 'avventure_give_merge_styles' );

        if ( is_admin() ) {
            add_filter( 'avventure_filter_tgmpa_required_plugins', 'avventure_give_tgmpa_required_plugins' );
        }
    }
}

// Filter to add in the required plugins list
if ( ! function_exists( 'avventure_give_tgmpa_required_plugins' ) ) {
    //Handler of the add_filter('avventure_filter_tgmpa_required_plugins', 'avventure_give_tgmpa_required_plugins');
    function avventure_give_tgmpa_required_plugins( $list = array() ) {
        if ( avventure_storage_isset( 'required_plugins', 'give' ) ) {
            $list[] = array(
                'name'     => avventure_storage_get_array( 'required_plugins', 'give' ),
                'slug'     => 'give',
                'required' => false,
            );
        }
        return $list;
    }
}

// Check if plugin installed and activated
if ( ! function_exists( 'avventure_exists_give' ) ) {
    function avventure_exists_give() {
        return class_exists( 'Give' );
    }
}

// Merge custom styles
if ( ! function_exists( 'avventure_give_merge_styles' ) ) {
    //Handler of the add_filter('avventure_filter_merge_styles', 'avventure_give_merge_styles');
    function avventure_give_merge_styles( $list ) {
        if ( avventure_exists_give() ) {
            $list[] = 'plugins/give/_give.scss';
        }
        return $list;
    }
}

// Add plugin-specific colors and fonts to the custom CSS
if ( avventure_exists_give() ) {
    require_once AVVENTURE_THEME_DIR . 'plugins/give/give-styles.php'; }