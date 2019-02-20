<?php
/** Подключаем необходимые битриксовские модули */
CModule::IncludeModule("tasks");            // модуль Задачи
CModule::IncludeModule("support");          // модуль Техническая поддержка
CModule::IncludeModule("forum");            // модуль Форум
CModule::IncludeModule("socialnetwork");    // модуль Социальная сеть
CModule::IncludeModule("blog");             // модуль Блог
CModule::IncludeModule("im");               // модуль Мессенджер
CModule::IncludeModule("main");             // модуль Главный
CModule::IncludeModule("iblock");           // модуль Информационные блоки
CModule::IncludeModule("crm");              // модуль CRM
use Bitrix\Main\Mail\Event;                 // модуль Почтовых уведомлений


/** Техническая функция для отладки */
class Dump {
    public function main($value){
        $filePath = $_SERVER["DOCUMENT_ROOT"].'/MyDump.txt';
        $file = fopen($filePath, "w");
        fwrite($file, print_r($value, 1));
        fclose();
        return;
    }
}

/** Проверяет есть-ли человек в CRM, если нет, то создаем */
class toLead {
    private function getContact($name, $lastName){
        global $DB;
        $zapros = $DB->Query("
            select ID from b_crm_contact where NAME = '".$name."' and LAST_NAME = '".$lastName."'
        ");
        if($zapros->Fetch()){
            return 'Y';
        } else {
            return 'N';
        }
    }
    private function getLead($name, $lastName){
        global $DB;
        $zapros = $DB->Query("
            select ID from b_crm_lead where NAME = '".$name."' and LAST_NAME = '".$lastName."'
        ");
        if($zapros->Fetch()){
            return 'Y';
        } else {
            return 'N';
        }
    }
    private function addLead($name, $lastName, $group){
        global $DB;
        if(
            (self::getContact($name, $lastName) == 'N' ||
                self::getLead($name, $lastName) == 'N') and
            $group == 7
        ){
            $DB->Query("
                insert into b_crm_lead
                (
                  DATE_CREATE, 
                  DATE_MODIFY, 
                  CREATED_BY_ID, 
                  MODIFY_BY_ID, 
                  ASSIGNED_BY_ID, 
                  OPENED, 
                  STATUS_ID, 
                  SOURCE_DESCRIPTION, 
                  TITLE,
                  FULL_NAME, 
                  NAME, 
                  LAST_NAME, 
                  COMMENTS
                ) value (
                  'NOW()', 
                  'NOW()', 
                  '1', 
                  '1', 
                  '1', 
                  'Y', 
                  'NEW', 
                  'Форма регистрации ЛК ТП',
                  '".$lastName." ".$name.": Заяка на регистрацию ЛК ТП', 
                  '".$lastName." ".$name."', 
                  '".$name."', 
                  '".$lastName."', 
                  'Подана заявка на регистрацию личного кабинета Технической Поддержки от: ".$lastName." ".$name."'
                )
            ");
            return;
        } else {
            return;
        }
    }
    private function getMaxIdLead(){
        global $DB;
        $zapros = $DB->Query("
            SELECT * FROM b_crm_lead WHERE ID=(SELECT MAX(ID) FROM b_crm_lead);
        ");
        return $zapros->Fetch();
    }
    private function addTask($name, $lastName, $group){
        if($group == 7){
            CModule::IncludeModule("tasks");
            $obTask = new CTasks;
            $obTask->Add(
                array(
                    "TITLE"                 =>  'В тех поддержке зарегистрировался новый пользователь',
                    "DESCRIPTION"           =>  'Зарегистрировался новый поьзователь в тех поддержке: <strong>'.$name.' '.$lastName.'</strong>. На основании его создал лид',
                    "AUDITORS"              =>  array(8),
                    "ACCOMPLICES"           =>  array(8),
                    "ALLOW_TIME_TRACKING"   =>  'Y',
                    "TAGS"                  =>  'Регистрация в тех поддержке',
                    "ALLOW_CHANGE_DEADLINE" =>  'Y',
                    "TASK_CONTROL"          =>  'Y',
                    "RESPONSIBLE_ID"        =>  9
                )
            );
        }
        return;
    }
    private function addNotify(){
        CAdminNotify::Add(
            array(
                "MESSAGE"   =>  'Зарегистрировался новый пользователь технической поддержки: '.self::getMaxIdLead()["FULL_NAME"].'. <a target="_blank" href="http://194.87.244.74/crm/lead/details/'.self::getMaxIdLead()["ID"].'">Перейти к списку</a>'
            )
        );
        CIMNotify::Add(
            array(
                "FROM_USER_ID" => 1,
                "TO_USER_ID" => 7,
                "NOTIFY_TYPE" => IM_NOTIFY_SYSTEM,
                "NOTIFY_MODULE" => "im",
                "NOTIFY_TAG"    => 'support',
                "NOTIFY_MESSAGE" => 'Зарегистрировался новый пользователь технической поддержки: '.self::getMaxIdLead()["FULL_NAME"].'. <a target="_blank" href="http://194.87.244.74/crm/lead/details/'.self::getMaxIdLead()["ID"].'/">Перейти</a>'
            ));
        return;
    }
    private function getGroupExtranet($elementGroupID){
        return CIBlockElement::GetProperty(
            33,
            $elementGroupID,
            array(),
            array()
        )
            ->Fetch()["VALUE"];
    }

    private function addSocNet($user){
        $arFields = array(
            "TITLE" => 'Зарегистрировался новый пользователь технической поддержки',
            "DETAIL_TEXT" => self::getMaxIdLead()["FULL_NAME"].' [URL=/crm/lead/details/'.self::getMaxIdLead()["ID"].'/]Перейти в лид[/URL]',
            #"BLOG_ID" => 1, //ID отправителя
            "BLOG_ID" => 1,
            "AUTHOR_ID" => 1, //ID блога, в котором будет запись
            #"AUTHOR_ID" => $groupId,
            "DATE_PUBLISH" => date('d.m.Y H:i'),
            "PUBLISH_STATUS" => BLOG_PUBLISH_STATUS_PUBLISH,
            "ENABLE_TRACKBACK" => 'N',
            "ENABLE_COMMENTS" => 'Y',
            "GROUP_ID"  => self::getGroupExtranet($user["UF_EXTRGROUP"]),
            "CATEGORY_ID" => self::getGroupExtranet($user["UF_EXTRGROUP"])
        );
        $ID = CBlogPost::Add($arFields);
        $arEvent = array (
            #"ID" => $groupId,
            'EVENT_ID'     => 'blog_post',
            '=LOG_DATE'    => 'now()',
            'TITLE_TEMPLATE' => '#USER_NAME# добавил(а) сообщение "#TITLE#" в блог',
            'TITLE'    => "Зарегистрировался новый пользователь технической поддержки",
            'MESSAGE'  => 'Зарегистрировался пользователь '.self::getMaxIdLead()["FULL_NAME"].' [URL=/crm/lead/details/'.self::getMaxIdLead()["ID"].'/]Перейти в лид[/URL]',
            'TEXT_MESSAGE'  => 'Зарегистрировался пользователь '.self::getMaxIdLead()["FULL_NAME"].' [URL=/crm/lead/details/'.self::getMaxIdLead()["ID"].'/]Перейти в лид[/URL]',
            'MODULE_ID'     => 'blog',
            'CALLBACK_FUNC' => false,
            #'SOURCE_ID'     => 27,
            'SOURCE_ID'     => $ID,
            'ENABLE_COMMENTS'  => 'Y',
            'RATING_TYPE_ID'   => 'BLOG_POST',
            #'RATING_ENTITY_ID' => $newID,
            'ENTITY_TYPE' => 'U',
            #'ENTITY_ID'   => '1',
            'ENTITY_ID'   => self::getGroupExtranet($user["UF_EXTRGROUP"]),
            'USER_ID'     => '1',
            'URL' => '/company/personal/user/1/blog/'.$ID.'/',
            "GROUP_ID"  => self::getGroupExtranet($user["UF_EXTRGROUP"]),
            "CATEGORY_ID" => self::getGroupExtranet($user["UF_EXTRGROUP"]),

        );
        $eventID = CSocNetLog::Add($arEvent);
        // Выдает права
        CSocNetLogRights::Add ( $eventID, array ("G3") );
        // Отправляет уведомление о новом сообщении
        #  CSocNetLog::SendEvent ( $eventID, 'SONET_NEW_EVENT' );
        return;
    }
    private function extranetGroup($user){
        CSocNetUserToGroup::Add(
            array(
                "USER_ID"               =>  $user["ID"],
                "GROUP_ID"              =>  self::getGroupExtranet($user["UF_EXTRGROUP"]),
                "ROLE"                  =>  SONET_ROLES_USER,
                "=DATE_CREATE"          =>  $GLOBALS["DB"]->CurrentTimeFunction(),
                "=DATE_UPDATE"          =>  $GLOBALS["DB"]->CurrentTimeFunction(),
                "INITIATED_BY_TYPE"     =>  SONET_INITIATED_BY_GROUP,
                "INITIATED_BY_USER_ID"  =>  1,
                "MESSAGE"               =>  'Новый пользователь'
            )
        );
        return;
    }
    public function main(&$arFields){
        self::addLead(
            $arFields["NAME"],
            $arFields["LAST_NAME"],
            $arFields["GROUP_ID"][0]
        );
        self::addNotify();
        self::addSocNet($arFields);
        self::extranetGroup($arFields);
        /* Не создаем задачу о том, что создан новый лид
        self::addTask(
            $arFields["NAME"],
            $arFields["LAST_NAME"],
            $arFields["GROUP_ID"][0]
        );
        */
        return;
    }
}

/** Опперации при создании тикета */
class newTicket {
    private function getTask($ticketID){
        $zapros = CTasks::GetList(
            array(),
            array(),
            array(),
            array()
        );
        while ($row = $zapros->Fetch()){
            $exp = explode(': ', $row["NAME"]);
            $exp = explode('_', $exp[0]);
            if($exp[1] == $ticketID){
                $resultZapros[] = $row;
            }
        }
        if(empty($resultZapros)) {
            $result = 0;
        } else {
            $result = 1;
        }
        return $result;
    }
    private function getTicket($ticketID){
        $zapros = CTicket::GetByID($ticketID, "ru", "N")->Fetch();
        return $zapros;
    }
    private function getGroup($author){
        global $DB;
        $groupGroupName = CIBlockElement::GetByID(CUser::GetByID($author)->Fetch()["UF_EXTRGROUP"])->Fetch()["NAME"];
        $zapros = $DB->Query("
            select ID from b_sonet_group where NAME = '".$groupGroupName."'
        ");
        return $zapros->Fetch()["ID"];
    }
    private function addTask($ticketID, $ticketMessage, $author){
        CModule::IncludeModule("tasks");
        if(self::getTask($ticketID) == 0){
            $getTicket = self::getTicket($ticketID);
            if($getTicket["CRITICALITY_ID"] == 4){
                $critical = 0;
                $criticalText = '<b>Критичность:</b> Низкая';
            } elseif($getTicket["CRITICALITY_ID"] == 5){
                $critical = 1;
                $criticalText = '<b>Критичность:</b> Средняя';
            } elseif($getTicket["CRITICALITY_ID"] == 6){
                $critical = 3;
                $criticalText = '<b>Критичность:</b> Высокая';
            } else {
                $critical = '';
            }
            $addToMessage = '
                <br /><br />'.$criticalText.'
                <br /><br /><br />
                _______________________________________________________
                <br /><br />
                Что бы ответить пользователю начните комментарий с символов:<br />
                ~|toUser|~
                <br />
                <strong>Например: </strong>~|toUser|~ Услуги по технической поддержки оказаны.<br />
                
            ';
            $obTask = new CTasks;
            $obTask->Add(
                array(
                    "TITLE"                 =>  'Ticket_'.$ticketID.': '.$getTicket["TITLE"],
                    "DESCRIPTION"           =>  $ticketMessage.$addToMessage,
                    "PRIORITY"              =>  $critical,
                    #"ACCOMPLICES"           =>  array(8),
                    "AUDITORS"              =>  array(8),
                    "ALLOW_TIME_TRACKING"   =>  'Y',
                    "TAGS"                  =>  'Тикет тех поддержки',
                    "ALLOW_CHANGE_DEADLINE" =>  'Y',
                    "TASK_CONTROL"          =>  'Y',
                    "RESPONSIBLE_ID"        =>  $getTicket["RESPONSIBLE_USER_ID"],
                    "GROUP_ID"              =>  self::getGroup($author)
                )
            );
        }
        return;
    }
    public function main(&$arFields){
        if(self::getTask($arFields["ID"]) == 0){
            self::addTask(
                $arFields["ID"],
                $arFields["MESSAGE"],
                $arFields["MESSAGE_AUTHOR_USER_ID"]
            );
        }
        return;
    }
}

class newTask {

    function main(){

        return;
    }
}

/** Операции при изменении тикета */
class upTicket {
    private function getTaskID($data){
        $zapros = CTasks::GetList(
            array(),
            array(),
            array("ID", "TITLE"),
            array()
        );
        while($row = $zapros->Fetch()){
            $expA = explode(":", $row["TITLE"]);
            $expB = explode("_", $expA[0]);
            if($expB[1] == $data["ID"]){
                $result = $row["ID"];
            }
        }
        return $result;
    }
    private function getTask($task_id){
        return CTasks::GetByID($task_id)->Fetch();
    }
    private function addComment($forumID, $topicID, $message){
        global $USER;
        $addArray = array(
            "POST_MESSAGE"  =>  '[COLOR=#ca2c92]Пользователь поддержки:[/COLOR]<br />'.$message,
            "FORUM_ID"      =>  $forumID,
            "TOPIC_ID"      =>  $topicID,
            "NEW_TOPIC"     =>  'N',
            "AUTHOR_ID"     =>  $USER->GetID(),
            "POST_DATE"     =>  date('Y-m-d H:i:s'),
            "APPROVED"      =>  'Y',
            "AUTHOR_NAME"   =>  $USER->GetFullName()
        );
        CForumMessage::Add($addArray);
        return;
    }
    private function getTicket($ticketID){
        $zapros = CTicket::GetByID($ticketID)->Fetch();
        return $zapros;
    }
    private function openCloceTask($taskID, $status){
        global $USER;
        if($status == 2){
            $dateRes = '';
            $mess = 'открыл';
            $color = 'ee1d24';
        }
        if($status == 5){
            $dateRes = date('d.m.Y H:i.s');
            $mess = 'закрыл';
            $color = '00a650';
        }
        $upTask= new CTasks;
        $upTask->Update(
            $taskID,
            array(
                "STATUS"        =>  $status,
                "CLOSED_DATE"   =>  $dateRes,
            )
        );
        $addArray = array(
            "POST_MESSAGE"  =>  '[COLOR=#ca2c92]Пользователь поддержки[/COLOR] [B][COLOR=#'.$color.']'.$mess.'[/COLOR][/B] обращение в [B]'.date('d.m.Y H:i:s').'[/B]',
            "FORUM_ID"      =>  self::getTask($taskID)["FORUM_ID"],
            "TOPIC_ID"      =>  self::getTask($taskID)["FORUM_TOPIC_ID"],
            "NEW_TOPIC"     =>  'N',
            "AUTHOR_ID"     =>  $USER->GetID(),
            "POST_DATE"     =>  date('Y-m-d H:i:s'),
            "APPROVED"      =>  'Y',
            "AUTHOR_NAME"   =>  $USER->GetFullName()
        );
        CForumMessage::Add($addArray);
        return;
    }
    public function main(&$arFields){
        if(!empty($arFields["MESSAGE"])) {
            self::addComment(
                self::getTask(self::getTaskID($arFields))["FORUM_ID"],
                self::getTask(self::getTaskID($arFields))["FORUM_TOPIC_ID"],
                $arFields["MESSAGE"]
            );
        }
        if($arFields["CLOSE"] == 'Y' and self::getTask(self::getTaskID($arFields))["STATUS"] != 5){
            self::openCloceTask(self::getTaskID($arFields), 5);
        }
        if($arFields["CLOSE"] == 'N' and self::getTask(self::getTaskID($arFields))["STATUS"] != 2){
            self::openCloceTask(self::getTaskID($arFields), 2);
        }
        return;
    }
}

class commentTask {
    private function getTicketID($comment){
        $exp = explode("_", $comment["XML_ID"]);
        $zapros = CTasks::GetByID($exp[1])->Fetch();
        $expTitleA = explode(':', $zapros["TITLE"]);
        $expTitleB = explode('_', $expTitleA[0]);
        return $expTitleB[1];
    }
    private function getComment($comment){
        $exp = explode('|~', $comment["POST_MESSAGE"]);
        if($exp[0] == '~|toUser'){
            $result = array(
                "message"   => $exp[1],
                "author"    => $comment["AUTHOR_NAME"],
                "author_id" => $comment["USER_ID"],
                "ticker_id" => self::getTicketID($comment)
            );
        }
        return $result;
    }
    private function addMessInTicket($comment){
        if(!empty(self::getComment($comment))) {
            $mess = '';
            CTicket::Set(
                array(
                    "MESSAGE"                   => self::getComment($comment)["message"],
                    "MESSAGE_AUTHOR_USER_ID"    => self::getComment($comment)["author_id"]
                ),
                $mess,
                self::getComment($comment)["ticker_id"],
                "N"
            );
        }
        return;
    }
    public function main(&$arFields){
        if(!empty($arFields)){
            self::addMessInTicket($arFields);
        }
        return;
    }
}

/** Операции при изменении задачи */
class upTask {
    private function getTask($taskID){
        $zapros = CTasks::GetByID($taskID)->Fetch();
        return $zapros;
    }
    private function getTaskStatus($taskID){
        if($taskID["STATUS"] == 2){
            $closeTask = 'N';
        }
        if($taskID["STATUS"] == 5){
            $closeTask = 'Y';
        }
        return $closeTask;
    }
    private function getTicketID($taskID){
        $expA = explode(':', $taskID["TITLE"]);
        $expВ = explode('_', $expA[0]);
        return $expВ[1];
    }
    private function upTicket($taskID){
        CTicket::Set(
            array(
                "CLOSE" => self::getTaskStatus($taskID)
            ),
            self::getTicketID($taskID)
        );
        return;
    }
    public function main(&$arFields){
        self::upTicket($arFields);
    }
}

class addContact {
    private function getUser($user){
        $zapros = CUser::GetList(
            ($by="personal_country"),
            ($order="desc"),
            array(
                "NAME" => $user["FULL_NAME"]
            )
        )
            ->Fetch();
        $result = array(
            "ID"    =>  $zapros["ID"],
            "EMAIL" =>  $zapros["EMAIL"]
        );
        return $result;
    }
    private function activateUser($data){
        $user = new CUser;
        $user->Update(
            self::getUser($data)["ID"],
            array(
                "ACTIVE" => 'Y'
            )
        );
        return;
    }
    private function sendMail($data){
        Event::send(array(
            "EVENT_NAME" => "ilsSupport",
            "LID" => "s1",
            "C_FIELDS" => array(
                "EMAIL" => self::getUser($data)["EMAIL"],
                "USER_ID" => self::getUser($data)["ID"]
            ),
        ));
        return;
    }
    public function main(&$arFields){
        self::activateUser($arFields);
        self::sendMail($arFields);
        return;
    }
}