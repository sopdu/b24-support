# addTask \(private\)

```php
self::addTask($name, $lastName, $group)
```

Функция ставит задачу ответственному о том, что зарегистрировался новый пользователь и надо заполнить лид связавшись с ним.

```php
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
```



