INSTALLATION
------------

If you do not have [Composer](http://getcomposer.org/), you may install it by following the instructions
at [getcomposer.org](http://getcomposer.org/doc/00-intro.md#installation-nix).

After you install the application, you have to conduct the following steps to initialize
the installed application. You only need to do these once for all.

1. Run `composer global require "fxp/composer-asset-plugin:~1.0.0"`
2. Check requirements (run `requirements.php`)
3. Create a new database and adjust the `components['db']` configuration in `config/common-local.php` accordingly.
4. Apply migrations with console command `yii migrate`. This will create tables needed for the application to work.
5. Generate rbac config with console command `yii rbac/init`.
6. Set document roots of your Web server like: `/path/to/yii-application/web/`

Also check and edit the other files in the `config/` directory to customize your application.

CronTab
-------

```
*/1 * * * * /path/to/yii-application/yii ping
```