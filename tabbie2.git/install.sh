#!/usr/bin/env bash

composer install -n
php init --env=Docker
chown www-data:www-data ./frontend/runtime ./frontend/web/assets
php yii migrate/up --interactive=0