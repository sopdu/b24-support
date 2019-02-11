# addLead \(private\)

```php
self::addLead($name, $lastName, $group);
```

Функция проверяет есть в контактах или лидах человек с таким именем, что указал при регистрации с помощью функций [getContact](getcontact-private.md) и [getLead](getlead-private.md). Если человека не находит, то создает лид. Если находит, то ни чего не делает.

```php
private function addLead($name, $lastName, $group){
    global $DB;
    if(
        (self::getContact($name, $lastName) == 'N' ||
        self::getLead($name, $lastName) == 'N') and
        $group == 7
    ){
        $DB->Query("
            insert into b_crm_lead(
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
```

{% hint style="info" %}
id группы для тех кто регистрируется самостоятельно 7
{% endhint %}

