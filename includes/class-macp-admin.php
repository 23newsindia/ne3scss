<?php
class MACP_Admin {
    // ... (previous code remains the same)

    public function render_settings_page() {
        if (!current_user_can('manage_options')) return;

        if (isset($_POST['macp_save_settings'])) {
            check_admin_referer('macp_save_settings_nonce');
            
            $options = [
                'macp_enable_redis',
                'macp_enable_html_cache',
                'macp_enable_gzip',
                'macp_minify_html',
                'macp_minify_css',
                'macp_minify_js',
                'macp_remove_unused_css',
                'macp_process_external_css'
            ];

            foreach ($options as $option) {
                update_option($option, isset($_POST[$option]) ? 1 : 0);
            }

            // Save CSS exclusions
            if (isset($_POST['macp_css_safelist'])) {
                $safelist = array_filter(array_map('trim', explode("\n", $_POST['macp_css_safelist'])));
                MACP_CSS_Config::save_safelist($safelist);
            }

            if (isset($_POST['macp_css_excluded_patterns'])) {
                $patterns = array_filter(array_map('trim', explode("\n", $_POST['macp_css_excluded_patterns'])));
                MACP_CSS_Config::save_excluded_patterns($patterns);
            }
        }

        $settings = [
            'redis' => get_option('macp_enable_redis', 1),
            'html_cache' => get_option('macp_enable_html_cache', 1),
            'gzip' => get_option('macp_enable_gzip', 1),
            'minify_html' => get_option('macp_minify_html', 0),
            'minify_css' => get_option('macp_minify_css', 0),
            'minify_js' => get_option('macp_minify_js', 0),
            'remove_unused_css' => get_option('macp_remove_unused_css', 0),
            'process_external_css' => get_option('macp_process_external_css', 0)
        ];

        include MACP_PLUGIN_DIR . 'templates/admin-page.php';
        include MACP_PLUGIN_DIR . 'templates/css-exclusions.php';
    }

    public function render_debug_page() {
        if (!current_user_can('manage_options')) return;
        
        require_once MACP_PLUGIN_DIR . 'includes/class-macp-debug-utility.php';
        $status = MACP_Debug_Utility::check_plugin_status();
        
        include MACP_PLUGIN_DIR . 'templates/debug-page.php';
    }
}