#!/usr/bin/env bash

LARAVEL='/var/www/html'

if cd releases ; then
    LAST=`readlink -f ../current` 
    LAST=${LAST##*/}

    echo 'Removing old releases but one last for backup'
    ls | grep -v ${LAST} | xargs rm -rfv
else
    echo "Releases folder not found, creating one"
    mkdir releases && cd releases
fi 

DATESTAMP=`date +"%Y-%m-%d-%H-%M-%S"`

git clone --depth 1 'git@github.com:proscholy/api.regenschori.cz.git' ${LARAVEL}/releases/${DATESTAMP} || 
    git clone --depth 1 'https://github.com/proscholy/api.regenschori.cz.git' ${LARAVEL}/releases/${DATESTAMP}

if cd ${DATESTAMP} ; then 
    git reset --hard master

    echo "Linking storage directory"
    rm -rf storage
    ln -nfs ${LARAVEL}/storage ${LARAVEL}/releases/${DATESTAMP}/storage

    echo "Modifying access rights for production for: storage, bootstrap/cache"
    chgrp -R www-data storage bootstrap/cache
    chmod -R ug+rwx storage bootstrap/cache

    cp ${LARAVEL}/.env ${LARAVEL}/releases/${DATESTAMP}/.env

    echo "Installing composer and yarn"
    composer install --optimize-autoloader --no-dev
    composer dump-auto

    yarn install
    yarn run production

    rm -rf node_modules

    php artisan down --message="Probíhá aktualizace zpěvníku na novou verzi. Zkuste to později" --retry=60
    php artisan config:cache
    php artisan route:cache
    php artisan cache:clear
    php artisan view:clear
    php artisan migrate --force
    php artisan lighthouse:clear-cache
    php artisan lighthouse:cache
    php artisan up

    echo "Resetting OPcache"
    curl nginx/reset-cache

    ln -nfs ${LARAVEL}/releases/${DATESTAMP} ${LARAVEL}/current
else
    echo 'Cloning not successful, aborting the deploy'
fi

cd ${LARAVEL}