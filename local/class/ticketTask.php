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
}
?>