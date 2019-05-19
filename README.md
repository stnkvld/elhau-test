Инструкция
----------

Следует установить пакеты composer:
```
composer install
```

Существует консольная команда для запуска сценария сбора информации из объявлений:
```
php yii task/perform-all
```

Если требуется использовать **cron**, то следует воспользоваться командой **crontab -e** и файлом **cron-script.sh**:
```
crontab -e

// В файл crontab указать нужный период запуска скрипта
* * * * * /path-to-project/cron-script.sh
```

*Проект выполняет необходимые функции по парсингу и предоставляет визуальный интерфейс. 
Однако, в нем присутствуют лишние элементы интерфейса и он может быть улучшен путем использования
асинхронных запросов. Страница результата носит исключительно демонстративный характер: вывод 
данных без их должного представления.

