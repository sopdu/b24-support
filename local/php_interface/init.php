<?php
#require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once ($_SERVER["DOCUMENT_ROOT"].'/local/class/Core.php');
require_once ($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/sopdu.remindingtask/class.php');

/** sopdu b24-support */

// Подключение файла с классами
require_once ($_SERVER["DOCUMENT_ROOT"].'/local/class/ticketTask.php');

// Запускает класс newTicket при создании нового тикета в модуле "Техподдержка"
AddEventHandler("support", "OnAfterTicketAdd", array("newTicket", "main"));

// Запускаеться класс upTicket при обновлении тикета в модуле "Техподдержка"
AddEventHandler("support", "OnAfterTicketUpdate", array("upTicket", "main"));

// Запускается класс upTask при изменении задачи в модуле "Задачи"
AddEventHandler("tasks", "OnTaskUpdate", array("upTask", "main"));

// Запускается класс commentTask при добавлении комментария в задаче
AddEventHandler("forum", "onBeforeMessageAdd", array("commentTask", "main"));

// Запускаеться класс toLead при регистрации нового пользователя
AddEventHandler("main", "OnAfterUserAdd", array("toLead", "main"));

/** end sopdu b24-support */


AddEventHandler("tasks", "OnTaskAdd", array("remindigtaskInit", "Add"));
AddEventHandler("tasks", "OnTaskAdd", array("dealCalendar", "addEvent"));
AddEventHandler("tasks", "OnTaskUpdate", array("dealCalendar", "EventUpdateTask"));
//AddEventHandler("main", "OnProlog", array("dealCalendar", "deadEvent"));


AddEventHandler("support", "OnAfterTicketAdd", array("supportTack", "get"));
?>