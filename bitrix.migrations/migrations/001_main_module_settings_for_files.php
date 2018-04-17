<?php

/**
 *
 * Миграция включает галочки в главном модуле у настроек "Быстрая отдача файлов через Nginx", "Переместить весь Javascript в конец страницы", "Создавать сжатую копию объединенных CSS и JS файлов" и выключает у "Объединять CSS файлы"
 *
 */

define('BX_BUFFER_USED', true);
define('NO_KEEP_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS', true);
define('NO_AGENT_STATISTIC', true);
define('STOP_STATISTICS', true);
define('SITE_ID', 's1');

$_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__.'/../../');

require $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(0);
ignore_user_abort(true);

while (ob_get_level()){
	ob_end_flush();
}

/*
 * Выше находится стандартная служебная часть сценария миграции, не требует комментариев.
 * Далее создается индивидуальный сценарий, требует комментариев выполнения.
 *
 * Во время выполнения миграции необходимо предусмотреть проверку состояния миграции,
 * для того чтобы избежать дублирования данных. То есть на одной копии проекта миграция не должна
 * запускаться дважды.
 *
 * При выводе, для переноса строк используется и <br> и PHP_EOL!
 */

if (COption::SetOptionString("main", "bx_fast_download", "Y")){
	echo sprintf('Настройка "%s" успешно изменена <br>'.PHP_EOL, 'Быстрая отдача файлов через Nginx');
}else{
	echo sprintf('Настройку "%s" не удалось измененить <br>'.PHP_EOL, 'Быстрая отдача файлов через Nginx');
}
if (COption::SetOptionString("main", "move_js_to_body", "Y")){
	echo sprintf('Настройка "%s" успешно изменена <br>'.PHP_EOL, 'Переместить весь Javascript в конец страницы');
}else{
	echo sprintf('Настройку "%s" не удалось измененить <br>'.PHP_EOL, 'Переместить весь Javascript в конец страницы');
}
if (COption::SetOptionString("main", "compres_css_js_files", "Y")){
	echo sprintf('Настройка "%s" успешно изменена <br>'.PHP_EOL, 'Создавать сжатую копию объединенных CSS и JS файлов');
}else{
	echo sprintf('Настройку "%s" не удалось измененить <br>'.PHP_EOL, 'Создавать сжатую копию объединенных CSS и JS файлов');
}
if (COption::SetOptionString("main", "optimize_css_files", "N")){
	echo sprintf('Настройка "%s" успешно изменена <br>'.PHP_EOL, 'Объединять CSS файлы');
}else{
	echo sprintf('Настройку "%s" не удалось измененить <br>'.PHP_EOL, 'Объединять CSS файлы');
}

/*
 * Нет закрывающего тега '?>'
 * Обязательно пустая строка в конце (файл заканчивается переносом на новую строку)
 */
