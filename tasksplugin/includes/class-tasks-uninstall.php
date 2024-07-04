<?php

/**
 * plugin uninstall
 */
class Tasks_Uninstall {
	public static function uninstall() {
		// проверяем права пользователя
        if (!current_user_can('delete_plugins')) {
            return;
        }
		// удалим таблицу для заданий
		global $wpdb;
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}tasksplugin" );
	}
}
