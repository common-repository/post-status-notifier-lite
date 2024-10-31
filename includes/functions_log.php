<?php
if (!function_exists('psn_log_debug')) {
    function psn_log_debug($title, $message = null)
    {
        do_action('psn_log_debug', $title, $message);
    }
}