<?php
/* WP Image Makers support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'avventure_trx_socials_theme_setup9' ) ) {
    add_action( 'after_setup_theme', 'avventure_trx_socials_theme_setup9', 9 );
    function avventure_trx_socials_theme_setup9() {

        if ( is_admin() ) {
            add_filter( 'avventure_filter_tgmpa_required_plugins', 'avventure_trx_socials_tgmpa_required_plugins' );
        }
    }
}

// Filter to add in the required plugins list
if ( ! function_exists( 'avventure_trx_socials_tgmpa_required_plugins' ) ) {
    //Handler of the add_filter('avventure_filter_tgmpa_required_plugins', 'avventure_trx_socials_tgmpa_required_plugins');
    function avventure_trx_socials_tgmpa_required_plugins( $list = array() ) {
        if ( avventure_storage_isset( 'required_plugins', 'trx_socials' ) ) {
            $path = avventure_get_file_dir( 'plugins/trx_socials/trx_socials.zip' );
            if ( !empty( $path ) || avventure_get_theme_setting( 'tgmpa_upload' ) ) {
                $list[] = array(
                    'name'     => avventure_storage_get_array( 'required_plugins', 'trx_socials' ),
                    'slug'     => 'trx_socials',
                    'source'   => !empty( $path ) ? $path : 'upload://trx_socials.zip',
                    'required' => false,
                );
            }
        }
        return $list;
    }
}

// Set plugin's specific importer options
if ( !function_exists( 'trx_addons_trx_socials_importer_set_options' ) ) {
    if (is_admin()) add_filter( 'trx_addons_filter_importer_options',   'trx_addons_trx_socials_importer_set_options' );
    function trx_addons_trx_socials_importer_set_options($options=array()) {
        if ( avventure_storage_isset( 'required_plugins', 'trx_socials' ) ) {
            $options['additional_options'][]    = 'trx_socials_api_instagram_access_token';                 // Add slugs to export options for this plugin

            if (is_array($options['files']) && count($options['files']) > 0) {
                foreach ($options['files'] as $k => $v) {
                    $options['files'][$k]['file_with_trx_socials'] = str_replace('name.ext', 'trx_socials.txt', $v['file_with_']);
                }
            }
        }
        return $options;
    }
}

