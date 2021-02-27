#!/usr/bin/env bash

php artisan elastic:create-index "App\Elastic\SongLyricIndexConfigurator"
php artisan elastic:create-index "App\Elastic\AuthorIndexConfigurator"

php artisan elastic:update-mapping "App\SongLyric"
php artisan elastic:update-mapping "App\Author"

php artisan scout:import "App\SongLyric"
php artisan scout:import "App\Author"