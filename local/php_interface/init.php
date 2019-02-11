<?php
#require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once ($_SERVER["DOCUMENT_ROOT"].'/local/class/Core.php');
require_once ($_SERVER["DOCUMENT_ROOT"].'/local/class/all.php');
require_once ($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/sopdu.remindingtask/class.php');

/** sopdu support */

require_once ($_SERVER["DOCUMENT_ROOT"].'/local/class/ticketTask.php');

// при создании нового тикета в модуле Техподдержки
AddEventHandler("support", "OnAfterTicketAdd", array("newTicket", "main"));

// при обновлении тикета в тех поддержке
AddEventHandler("support", "OnAfterTicketUpdate", array("upTicket", "main"));

// при обновлении задачи
AddEventHandler("tasks", "OnTaskUpdate", array("upTask", "main"));

// при добавлении комментария в задаче
AddEventHandler("forum", "onBeforeMessageAdd", array("commentTask", "main"));

// при регистрации пользователя
AddEventHandler("main", "OnAfterUserAdd", array("toLead", "main"));

/** end sopdu support */

AddEventHandler("crm", "OnAfterCrmLeadAdd", array("crmLead", "postAfterAddLead"));
AddEventHandler("crm", "OnBeforeCrmLeadUpdate", array("crmLead", "postAfterCloseLead"));
AddEventHandler("tasks", "OnTaskAdd", array("remindigtaskInit", "Add"));
AddEventHandler("tasks", "OnTaskAdd", array("dealCalendar", "addEvent"));
AddEventHandler("tasks", "OnTaskUpdate", array("dealCalendar", "EventUpdateTask"));
//AddEventHandler("main", "OnProlog", array("dealCalendar", "deadEvent"));


AddEventHandler("support", "OnAfterTicketAdd", array("supportTack", "get"));


?>