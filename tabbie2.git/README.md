Tabbie2 - Source Code
===================================

This is a Yii 2 application best for developing complex Web applications with multiple tiers.
The structure includes three tiers:
```
- front end    What the user sees and uses
- back end     What the tabbie admin uses and makes analysis and entering variables
- console      What the webserver uses to execute cronjobs
```
each of which
is a separate Yii application.

The template is designed to work in a team development environment. It supports
deploying the application in different environments.


DIRECTORY STRUCTURE
-------------------

```
algorithms
    algorithms/          contains the different algorithms that a user can choose from
common
    config/              contains shared configurations
    mail/                contains view files for e-mails
    models/              contains model classes used in both backend and frontend
console
    config/              contains console configurations
    controllers/         contains console controllers (commands)
    migrations/          contains database migrations
    models/              contains console-specific model classes
    runtime/             contains files generated during runtime
backend
    assets/              contains application assets such as JavaScript and CSS
    config/              contains backend configurations
    controllers/         contains Web controller classes
    models/              contains backend-specific model classes
    runtime/             contains files generated during runtime
    views/               contains view files for the Web application
    web/                 contains the entry script and Web resources
frontend
    assets/              contains application assets such as JavaScript and CSS
    config/              contains frontend configurations
    controllers/         contains Web controller classes
    models/              contains frontend-specific model classes
    runtime/             contains files generated during runtime
    views/               contains view files for the Web application
    web/                 contains the entry script and Web resources
    widgets/             contains frontend widgets
vendor/                  contains dependent 3rd-party packages
environments/            contains environment-based overrides
tests                    contains various tests for the advanced application
    codeception/         contains tests developed with Codeception PHP Testing Framework
```


REQUIREMENTS
------------

The minimum requirement by this application template that your Web server supports PHP 5.4.0.


GETTING STARTED
---------------

After you install the application, you have to conduct the following steps to initialize
the installed application. You only need to do these once for all.

1. Install composer (https://getcomposer.org)
2. Open terminal and go to the /tabbie2.git folder.
3. run `composer global require "fxp/composer-asset-plugin:~1.0.0"`
4. run `composer update` to load the vendor directory - this will take some time
5. Run command `php init` to initialize the application with a specific environment.
6. Create a new database and adjust the configuration in `common/config/main-local.php` accordingly.
7. Apply migrations with console command `yii migrate`. This will create tables needed for the application to work.

To login into the application, you need to first sign up, with any of your email address, username and password.
Then, you can login into the application with same email address and password at any time.
