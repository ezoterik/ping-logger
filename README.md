GETTING STARTED
---------------

After you install the application, you have to conduct the following steps to initialize
the installed application. You only need to do these once for all.

1. `composer install --no-dev --prefer-dist`
2. Run command `init` to initialize the application with a specific environment.
3. Check requirements (run `php ./requirements.php`)
4. Create a new database and adjust the configuration in `config/db-local.php` accordingly.
5. Apply migrations with console command `yii migrate` or upload exists database dump. This will create tables needed for the application to work.
6. Set document roots of your Web server like: `/path/to/yii-application/web/`

Also check and edit the other files in the `config/` directory to customize your application.


Cron
----

```
*/1 * * * * /app/yii ping
```


Интернационализация
-------------------

Для обновления файлов перевода интерфейса нужно выполнить эту команду (внутри виртуалки):

```
/app/yii message /app/messages/config.php
```


Запуск тестов
-------------

Для запуска всех тестов нужно перейти в директорию `/app` и запустить тесты:

```
cd /app
vendor/bin/codecept run
```

В момент разработки конкретных объектов можно запускать только определенные тесты

```
cd /app
vendor/bin/codecept run -- unit
vendor/bin/codecept run -- unit modules/InputDataCest
```


Example of update bash script
-----------------------------

```
#!/bin/bash
cd /path/to/yii-application
git checkout -f
git pull --rebase
composer install --no-dev --prefer-dist
/app/yii migrate/up --interactive=0
touch /app/assets/app
touch /app/assets/monitor-box
/app/yii cache/flush-all
```

[![Yii2](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](http://www.yiiframework.com/)