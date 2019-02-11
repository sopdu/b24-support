# getLead \(private\)

```php
self::getLead($name, $lastName);
```

Функция проверяет существует-ли лид где имя и фамилия совпадает с теми, что указал пользователь при регистрации.

> **N** - пользователя не существует

> **Y** - пользователь существует

```php
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
```

{% hint style="warning" %}
Функция **private** используется только в классе **toLead**
{% endhint %}

