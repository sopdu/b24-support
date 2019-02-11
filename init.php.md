---
description: local/php_interface/init.php
---

# init.php

#### Подключение файла с классами  ticketTask.php

файл с классами вынесен из init.php что бы не загружать его в local/class 

```php
require_once ($_SERVER["DOCUMENT_ROOT"].'/local/class/ticketTask.php');
```

#### Запускает класс newTicket при создании нового тикета в модуле "Техподдержка"

```php
AddEventHandler("support", "OnAfterTicketAdd", array("newTicket", "main"));
```

#### Запускает класс upTicket при обновлении тикета в модуле "Техподдержка"

```php
AddEventHandler("support", "OnAfterTicketUpdate", array("upTicket", "main"));
```

#### Запускает класс upTask при изменении задачи в модуле "Задачи"

```php
AddEventHandler("tasks", "OnTaskUpdate", array("upTask", "main"));
```

#### Запускает класс commentTask при добавлении комментария в задаче

```php
AddEventHandler("forum", "onBeforeMessageAdd", array("commentTask", "main"));
```

#### Запускает класс toLead при регистрации нового пользователя

```php
AddEventHandler("main", "OnAfterUserAdd", array("toLead", "main"));
```

