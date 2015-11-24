INSTALLATION
------------

### Install via Composer

If you do not have [Composer](http://getcomposer.org/), you may install it by following the instructions
at [getcomposer.org](http://getcomposer.org/doc/00-intro.md#installation-nix).

You can then install the application using the following command:

```
git clone git@bitbucket.org:ezoterik/ping_logger.git
composer global require "fxp/composer-asset-plugin:~1.0.0"
cd ping_logger
composer install
```

GETTING STARTED
---------------

After you install the application, you have to conduct the following steps to initialize
the installed application. You only need to do these once for all.

1. Check requirements (run `requirements.php`)
2. Run command `init` to initialize the application with a specific environment.
3. Create a new database and adjust the `components['db']` configuration in `config/common-local.php` accordingly.
4. Apply migrations with console command `yii migrate` or upload exists database dump. This will create tables needed for the application to work.
5. Set document roots of your Web server like: `/path/to/yii-application/web/`

Also check and edit the other files in the `config/` directory to customize your application.

CronTab
-------

```
*/1 * * * * /path/to/yii-application/yii ping
```

Example of update bash script
-----------------------------

```
#!/bin/bash
cd /path/to/yii-application
git checkout -f
git pull --rebase
composer install --no-dev --prefer-dist
./yii migrate/up --interactive=0
touch ./assets/app
touch ./assets/monitor-box
./yii cache/flush-all
```