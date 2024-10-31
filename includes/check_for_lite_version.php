<?php
function psn_is_plugin_active_for_network( $plugin ) {
    if ( ! is_multisite() ) {
        return false;
    }

    $plugins = get_site_option( 'active_sitewide_plugins' );
    if ( isset( $plugins[ $plugin ] ) ) {
        return true;
    }

    return false;
}

function psn_is_plugin_active( $plugin ) {
    return in_array( $plugin, (array) get_option( 'active_plugins', array() ), true ) || psn_is_plugin_active_for_network( $plugin );
}

function checkIfLiteVersionIsEnabled() {

    $liteVersion = 'post-status-notifier-lite/post-status-notifier-lite.php';

    if (psn_is_plugin_active($liteVersion)) {
        add_action('plugins_loaded', function () {
            deactivate_plugins(plugin_basename(__FILE__));
        });
        wp_die(sprintf('
                The plugin cannot be activated because the plugin "Post Status Notifier Lite" is still activated. <br>
                Please deactivate "Post Status Notifier Lite" and try again.<br><br>
                <a href="%s">Back to plugin administration</a>
            ', admin_url('plugins.php')));
    }
}

checkIfLiteVersionIsEnabled();