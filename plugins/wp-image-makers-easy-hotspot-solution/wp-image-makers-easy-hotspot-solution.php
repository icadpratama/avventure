<?php
/* WP Image Makers support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'avventure_wp_image_markers_theme_setup9' ) ) {
    add_action( 'after_setup_theme', 'avventure_wp_image_markers_theme_setup9', 9 );
    function avventure_wp_image_markers_theme_setup9() {

        if ( is_admin() ) {
            add_filter( 'avventure_filter_tgmpa_required_plugins', 'avventure_wp_image_markers_tgmpa_required_plugins' );
        }
    }
}

// Filter to add in the required plugins list
if ( ! function_exists( 'avventure_wp_image_markers_tgmpa_required_plugins' ) ) {
    //Handler of the add_filter('avventure_filter_tgmpa_required_plugins',  'avventure_wp_image_markers_tgmpa_required_plugins');
    function avventure_wp_image_markers_tgmpa_required_plugins( $list = array() ) {
        if ( avventure_storage_isset( 'required_plugins', 'wp-image-makers-easy-hotspot-solution' ) ) {
            $path = avventure_get_file_dir( 'plugins/wp-image-makers-easy-hotspot-solution/wp-image-makers-easy-hotspot-solution.zip' );
            if ( ! empty( $path ) || avventure_get_theme_setting( 'tgmpa_upload' ) ) {
                 $list[] = array(
                    'name'     => avventure_storage_get_array( 'required_plugins', 'wp-image-makers-easy-hotspot-solution' ),
                    'slug'     => 'wp-image-makers-easy-hotspot-solution',
                    'source'   => ! empty( $path ) ? $path : 'upload://wp-image-makers-easy-hotspot-solution.zip',
                    'required' => false,
                );
            }
        }
        return $list;
    }
}

// Check if plugin installed and activated
if ( ! function_exists( 'avventure_exists_wp_image_markers' ) ) {
    function avventure_exists_wp_image_markers() {
        return class_exists( 'WPIM' );
    }
}


// Set plugin's specific importer options
if ( !function_exists( 'trx_addons_wp_image_markers_importer_set_options' ) ) {
    if (is_admin()) add_filter( 'trx_addons_filter_importer_options',   'trx_addons_wp_image_markers_importer_set_options' );
    function trx_addons_wp_image_markers_importer_set_options($options=array()) {
        if ( avventure_exists_wp_image_markers() && in_array('wp-image-makers-easy-hotspot-solution', $options['required_plugins']) ) {
            $options['additional_options'][]    = 'wpim_css';                 // Add slugs to export options for this plugin

            if (is_array($options['files']) && count($options['files']) > 0) {
                foreach ($options['files'] as $k => $v) {
                    $options['files'][$k]['file_with_wp_image_markers_easy_hotspot_solution'] = str_replace('name.ext', 'wp-image-makers-easy-hotspot-solution.txt', $v['file_with_']);
                }
            }
        }
        return $options;
    }
}