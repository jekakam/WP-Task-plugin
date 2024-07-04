<?php

/**
 * plugin activation
 */
class Tasks_Activator {
	public static function activate() {
		// проверяем права пользователя
		if (!current_user_can('activate_plugins')) {
			return;
		}
		// создадим таблицу для заданий
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$tablename = $wpdb->prefix . "tasksplugin";

		$sql = "CREATE TABLE $tablename (
					id int(11) NOT NULL AUTO_INCREMENT,
					name varchar(255) NOT NULL,
					description text NOT NULL,
					done tinyint(1) NOT NULL,
					datetime datetime NOT NULL,
					PRIMARY KEY (id) USING BTREE
		) $charset_collate;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
}


