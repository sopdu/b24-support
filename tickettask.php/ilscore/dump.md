# dump \(public\)

#### Использование

```php
ilsCore::dump($value);
```

Класс сохраняет значение переменной $value в файле в /MyDump.txt 

{% hint style="danger" %}
Значение $value не должно быть пустым
{% endhint %}

```php
public function dump($value){
        $filePath = $_SERVER["DOCUMENT_ROOT"].'/MyDump.txt';
        $file = fopen($filePath, "w");
        fwrite($file, print_r($value, 1));
        fclose();
        return;
}
```

