# BaksDev Auth Vk

[![Version](https://img.shields.io/badge/version-7.3.0-blue)](https://github.com/baks-dev/auth-vk/releases)
![php 8.4+](https://img.shields.io/badge/php-min%208.4-red.svg)
[![packagist](https://img.shields.io/badge/packagist-green)](https://packagist.org/packages/baks-dev/auth-vk)

Модуль авторизации пользователя в Vk

## Установка

``` bash
$ composer require baks-dev/auth-vk
```

## Создание приложения
1. Перейти по ссылке "Мои приложения"
   https://id.vk.com/about/business/go/accounts/256569/apps
   выбрать Web, и ввеcти название приложения.
2. Далее указать "Базовый домен" и "Доверенный Redirect URL" и нажать "Создать приложение".
3. В настройках приложения получить Client ID.
5. Указать параметры VK_CLIENT_ID (значение Client ID) и VK_CODE_VERIFIER в .env.
6. Для генерации значения для VK_CODE_VERIFIER можно использовать https://tonyxu-io.github.io/pkce-generator/
7. Документация для интеграции доступна по ссылке https://id.vk.com/about/business/go/docs/ru/vkid/latest/vk-id/connection/start-integration/auth-without-sdk/auth-without-sdk-web

## Дополнительно

Установка конфигурации и файловых ресурсов:

``` bash
$ php bin/console baks:assets:install
```

Изменения в схеме базы данных с помощью миграции

``` bash
$ php bin/console doctrine:migrations:diff

$ php bin/console doctrine:migrations:migrate
```

## Тестирование

``` bash
$ php bin/phpunit --group=auth-vk
```

## Лицензия ![License](https://img.shields.io/badge/MIT-green)

The MIT License (MIT). Обратитесь к [Файлу лицензии](LICENSE.md) за дополнительной информацией.

