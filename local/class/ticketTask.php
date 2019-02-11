<?php
class ilsCore {
    public function dump($value){
        $filePath = $_SERVER["DOCUMENT_ROOT"].'/MyDump.txt';
        $file = fopen($filePath, "w");
        fwrite($file, print_r($value, 1));
        fclose();
        return;
    }
}

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
}
?>