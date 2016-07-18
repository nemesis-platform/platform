# Nemesis Team Challenge Platform
© 2013-2016 Pavel Batanov <pavel@batanov.me> https://github.com/scaytrase

## Требования
### PHP
Для корректной работы платформы требуется PHP 5.5+ и следующие модули

* Curl
* Imagick
* Intl
* JSON
* MySQL
* XDebug

### MySQL Server
Для хранения данных требуется MySQL Server 5.5+

### Composer
Для работы с зависимостиями требуется Composer:
    
    wget -N -q https://getcomposer.org/composer.phar -O composer.phar
    chmod +x composer.phar
    cp composer.phar /usr/local/bin/composer

### Phing
Для автоматизации разветывания используется Phing

    wget -N -q http://www.phing.info/get/phing-latest.phar -O phing.phar
    chmod +x phing.phar
    cp phing.phar /usr/local/bin/phing

## Установка
Для разветывания платформы нужно разместить данный исходный код в папке, недоступной из браузера. После этого веб-сервер 
должен быть настроен так, чтобы целевой путь указывал на папку `web/`

После этого нужно запустить развертывание проекта командой

    phing build.prod.deploy
    
В результате данной операции будут установлены все завимисости, сброшен и перегенерирован кэш, скомпилированы и установлены
все ресурсы (LESS, CSS, JS и пр.)

