# addNotify \(private\)

```php
self::addNotify();
```

Функция выводит уведомления о том, что зарегистрировался новый пользователь.

> ```php
> CAdminNotify::Add(); // Выводит сообщение в админскую панель уведомлений
> CIMNotify::Add(); // Выводит сообщение в "Колокольчик"
> ```

```php
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
```



