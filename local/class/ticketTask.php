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
}
?>