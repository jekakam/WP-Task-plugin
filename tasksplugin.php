<?php

/**
 * @link              
 * @since             1.0.0
 * Plugin Name:       Tasks
 * Plugin URI:        
 * Description:       Плагин персональных заданий
 * Version:           1.0.0
 * Author:            JK
 * Author URI:        
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */


if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'TASKS_VERSION', '1.0.0' );
// Активация, деактивация и удаление плагина
function activate_tasks() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-tasks-activator.php';
	Tasks_Activator::activate();
}

function deactivate_tasks() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-tasks-deactivator.php';
	Tasks_Deactivator::deactivate();
}

function uninstall_tasks() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-tasks-uninstall.php';
	Tasks_Uninstall::uninstall();
}

register_activation_hook( __FILE__, 'activate_tasks' );
register_deactivation_hook( __FILE__, 'deactivate_tasks' );
register_uninstall_hook( __FILE__, 'uninstall_tasks' );


// Добавляем JS CSS
function tasks_scripts()
{	
	
	wp_enqueue_style('task', plugin_dir_url(__FILE__) . 'assest/css/style.css');	
	
	wp_deregister_script('jquery');
	wp_enqueue_script('jquery', '//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js');
	wp_enqueue_script('task', plugin_dir_url(__FILE__) . 'assest/js/script.js', array('jquery'), null);
	
	
	//if (!is_admin()) {
		wp_enqueue_style('magnific', '//cdn.jsdelivr.net/npm/magnific-popup@1.1.0/dist/magnific-popup.min.css');
		wp_enqueue_style('bootstrap', '//cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css');
		wp_enqueue_script('magnific', '//cdn.jsdelivr.net/npm/magnific-popup@1.1.0/dist/jquery.magnific-popup.min.js', array('jquery'), null );
		wp_enqueue_script('bootstrap', '//cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js');
	//}
	wp_localize_script( 'task', 'ajax',
		array(
			'url' => admin_url('admin-ajax.php')
		)
	);
	
	
}
add_action('wp_enqueue_scripts', 'tasks_scripts', 25);
add_action('admin_enqueue_scripts', 'tasks_scripts', 25);


// Добавляем меню
function _plugin_menu(){
    add_menu_page("Задачи", "Задачи", "manage_options", "tasksplugin", "displayTasks", "dashicons-edit-page");
}
add_action("admin_menu", "_plugin_menu");

// Выведем задачи
function displayTasks(){
    include "displaytasks.php";
}


// Добавляем задачу
add_action( 'wp_ajax_add_task', 'addtask_callback' );
add_action( 'wp_ajax_nopriv_add_task', 'addtask_callback' );

function addtask_callback(){
	global $wpdb;
	$name = $_POST['name'];
	$description = $_POST['description'];
	$datetime = $_POST['datetime'];

	$tablename = $wpdb->prefix."tasksplugin";
	if($name != '' && $description != '' && $datetime != '' ){
		$insert_sql = 'INSERT INTO '.$tablename.'(`name`,`description`,`datetime`, `done`) values("'.$name.'","'.$description.'","'.$datetime.'",0)';
		$wpdb->query($insert_sql);
		echo 'Задача добавлена!';
	} else {
		echo 'Заполните все поля';
	}
	die();
}

// Изменим задачу
add_action( 'wp_ajax_edit_task', 'edittask_callback' );
add_action( 'wp_ajax_nopriv_edit_task', 'edittask_callback' );

function edittask_callback(){
	global $wpdb;
	$name = $_POST['name'];
	$description = $_POST['description'];
	$datetime = $_POST['datetime'];
	$done = $_POST['done'];
	$ids = $_POST['ids'];
	$tablename = $wpdb->prefix."tasksplugin";
	if($name != '' && $description != '' && $datetime != '' && $done != ''){
		$wpdb->update( 
			$tablename, 
			array(
				'name' 			=> $name,
				'description' 	=> $description,
				'datetime' 		=> $datetime,
				'done' 			=> $done,
			), 
			array('id' => $ids), 
			array('%s','%s','%s','%d'),
			array('%d'),
		);
		echo 'Задача отредактирована!';;
	} else {
		echo 'Заполните все поля';
	}
	die();
}


// Обновим список задач
add_action( 'wp_ajax_reload', 'reload_callback' );
add_action( 'wp_ajax_nopriv_reload', 'reload_callback' );

function reload_callback(){
	$task = do_shortcode('[show_tasks]');
	echo $task;
	die();
}

// Удалим задачу
add_action( 'wp_ajax_detetetask', 'detetetask_callback' );
add_action( 'wp_ajax_nopriv_detetetask', 'detetetask_callback' );

function detetetask_callback(){
	global $wpdb;
	$tablename = $wpdb->prefix."tasksplugin";
	$ids = $_POST['ids'];
	$res = $wpdb->query("DELETE FROM ".$tablename." WHERE id=".$ids);
	echo $res;
	die();
}

// Зарегистрируем шорткод для фронта
add_shortcode('show_tasks', 'show_tasks_front');
function show_tasks_front(){
	global $wpdb;
	$tablename = $wpdb->prefix."tasksplugin";
	ob_start();
	echo '
		<div id="t_list">
		<h1>Список задач</h1>
		<p><a href="#" class="button button-primary addtask">Добавить задачу</a></p>
		<div class="white-popup mfp-hide" id="addform">
			<h3>Добавить задачу</h3>
			<form method="post" action="" id="adds">
				<div class="msg"></div>
				<table>
					<tr>
						<td>Название</td>
						<td><input type="text" name="name"></td>
					</tr>
					<tr>
						<td>Описание</td>
						<td><textarea type="text" name="description"></textarea></td>
					</tr>
					 <tr class="datetime">
						<td>Дата/Время</td>
						<td><input type="datetime-local" id="date" name="datetime"/></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td><a href="#" id="add">Добавить</a></td>
					</tr>
				</table>
				<input type="hidden" name="action" value="add_task">
			</form>
		</div>
		<div id="edit-popup" class="white-popup mfp-hide">
			<h3>Редактировать задачу</h3>
			<form method="post" action="" id="editform">
				<div class="msg"></div>
				<table>
					<tr>
						<td>Название</td>
						<td><input type="text" name="name"></td>
					</tr>
					<tr>
						<td>Описание</td>
						<td><textarea type="text" name="description"></textarea></td>
					</tr>
					 <tr class="datetime">
						<td>Дата/Время</td>
						<td><input type="datetime-local" id="date" name="datetime"/></td>
					</tr>
					<tr>
						<td>Статус</td>
						<td>
							<select name="done">
								<option value="0" selected="selected">В работе</option>
								<option value="1">Выполнено</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td><a href="#" id="edit">Редактировать</a></td>
					</tr>
				</table>
				<input type="hidden" name="action" value="edit_task">
				<input type="hidden" name="ids" value="">
			</form>
		</div>
		<table class="table table-hover table-striped">
			<thead>
				<tr>
					<th>№</th>
					<th>Название</th>
					<th>Описание</th>
					<th>Дата/Время</th>
					<th>Состояние</th>
					<th></th>
				</tr>
			</thead>
		<tbody>';	
		// Получаем записи, если они есть, выводим
		$tasks = $wpdb->get_results("SELECT * FROM ".$tablename." order by datetime asc");
		if(count($tasks) > 0){
			$count = 1;
			foreach($tasks as $item){
				$id = $item->id;
				$name = $item->name;
				$description = $item->description;
				$datetime = $item->datetime;
				if ($item->done) {
					$done = 'Выполнено';
				} else {
					$done = 'В работе';
				}	
				echo "<tr data-id='id".$id."'>
					<td>".$count."</td>
					<td class='name'>".$name."</td>
					<td class='desc'>".$description."</td>
					<td class='date'>".$datetime."</td>
					<td class='done'>".$done."</td>
					<td class='but'><a href='#' data-id='".$id."' class='deltask'>Удалить</a> | <a href='#edit-popup' class='edittask' data-id='".$id."'>Редактировать</a></td>
					</tr>";
				$count++;
			}
		}else{
			echo "<tr><td colspan='6'>Нет задач</td></tr>";
		}
	echo '</tbody></table></div>';
	return ob_get_clean();	
}
