# getContact \(private\)

```php
self::getContact($name, $lastName);
```

 Функция проверяет существует-ли контакт где имя и фамилия совпадает с теми, что указал пользователь при регистрации.

> **N** - пользователя не существует

> **Y** - пользователь существует

```php
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
```

{% hint style="warning" %}
Функция **private** используется только в классе **toLead**
{% endhint %}

