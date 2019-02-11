# getMaxIdLead \(private\)

```php
selt::getMaxIdLead();
```

Функция получает id последнего лида

```php
private function getMaxIdLead(){
    global $DB;
    $zapros = $DB->Query("
        SELECT * FROM b_crm_lead WHERE ID=(SELECT MAX(ID) FROM b_crm_lead);
    ");
    return $zapros->Fetch();
}
```





