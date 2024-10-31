<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 *
 * @author    Timo Reith <timo@ifeelweb.de>
 * @version   $Id: Cron.php 2044844 2019-03-05 21:18:19Z worschtebrot $
 * @package
 */
class IfwPsn_Wp_Helper_Cron 
{
    /**
     * @var null|array
     */
    protected static $_allSchedules;

    /**
     * @return array
     */
    public static function getAllSchedules()
    {
        if (self::$_allSchedules === null) {
            $result = array();

            if (function_exists('wp_get_schedules')) {
                foreach (wp_get_schedules() as $k => $v) {
                    $result[$k] = $v['display'];
                }
            }

            self::$_allSchedules = $result;
        }

        return self::$_allSchedules;
    }

    /**
     * @param $key
     * @return null|string
     */
    public static function getScheduleDisplay($key)
    {
        $result = $key;
        foreach(self::getAllSchedules() as $k => $v) {
            if ($key == $k) {
                $result = $v;
                break;
            }
        }
        return $result;
    }
}
