<?php
/**
 *
 *
 * @author      Timo Reith <timo@ifeelweb.de>
 * @version     $Id: Rules.php 535 2023-06-25 15:46:48Z timoreithde $
 * @copyright   Copyright (c) ifeelweb.de
 * @package     Psn_Admin
 */
class Psn_Admin_ListTable_RulesMetabox extends Psn_Admin_ListTable_Rules
{
    public function _column_name($item, $classes, $data, $primary)
    {
        $attributes = "class='$classes' $data";

        $link = '#';
        if (!empty($item['id'])) {
            $link = sprintf(admin_url('options-general.php?page=post-status-notifier&controller=rules&appaction=edit&id=%d'), $item['id']);
        }

        echo "<td $attributes>";
        echo '<a href="'. $link .'">' . $this->column_default( $item, 'name' ) . '</a>';
        echo '</td>';
    }
}
