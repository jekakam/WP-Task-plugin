<?php

/**
 * plugin deactivation
 */
class Tasks_Deactivator {
	public static function deactivate() {
		 // проверяем права пользователя
		if (!current_user_can('deactivate_plugins')) {
			return;
		}
		
	}
}
