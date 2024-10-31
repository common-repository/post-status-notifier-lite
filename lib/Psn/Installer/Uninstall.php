<?php
/**
 * Executes on plugin uninstall
 *
 * @author      Timo Reith <timo@ifeelweb.de>
 * @copyright   Copyright (c) ifeelweb.de
 * @version     $Id: Uninstall.php 3137084 2024-08-17 17:27:18Z worschtebrot $
 * @package     Psn_Installer
 */
class Psn_Installer_Uninstall implements IfwPsn_Wp_Plugin_Installer_UninstallInterface
{
    /** (non-PHPdoc)
     * @see IfwPsn_Wp_Plugin_Installer_UninstallInterface::execute()
     */
    public static function execute($pm, $networkwide = false)
    {
        // handle the DB
        if (IfwPsn_Wp_Proxy_Blog::isMultisite() && $networkwide == true) {

            // multisite installation
            $currentBlogId = IfwPsn_Wp_Proxy_Blog::getBlogId();

            foreach (IfwPsn_Wp_Proxy_Blog::getMultisiteBlogIds() as $blogId) {

                IfwPsn_Wp_Proxy_Blog::switchToBlog($blogId);
                self::_dropTable($pm);
            }
            IfwPsn_Wp_Proxy_Blog::switchToBlog($currentBlogId);

        } else {
            // single blog installation
            self::_dropTable($pm);
        }

        // remove update action
        if ($pm->getConfig()->plugin->autoupdate == 1) {
            remove_action('in_plugin_update_message-' . $pm->getPathinfo()->getFilenamePath(), array($pm->getBootstrap()->getUpdateManager(), 'onPluginUpdateMessage'));
            remove_filter('pre_set_site_transient_update_plugins', array($pm->getBootstrap()->getUpdateManager(), 'checkForPremiumUpdate'));
            if ($pm->isPremium()) {
                remove_filter('plugins_api', array($pm->getBootstrap()->getUpdateManager(), 'getPluginInfo'));
            }
        }
    }

    /**
     * @param IfwPsn_Wp_Plugin_Manager $pm
     */
    protected static function _dropTable($pm)
    {
        global $wpdb;

        if ($pm->isPremium() ||
            !$pm->isPremium() && !IfwPsn_Wp_Proxy_Blog::isPluginActive('post-status-notifier/post-status-notifier.php')
        ) {
            // only delete rules table if it's the Premium version
            // or it's the Lite version and the Premium version is not activated
            $wpdb->query('DROP TABLE IF EXISTS `'. $wpdb->prefix .'psn_rules`');
        }
    }
}
