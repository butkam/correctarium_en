# teledit

Красным выделены нереализованные блоки.

Этот PNG совсем не прост. Он был рожден благодаря экспорту «png with xml» в [Draw.io](https://www.draw.io/). Помните про это, когда захотите обновить схему.

![telegram](https://cloud.githubusercontent.com/assets/1920639/11452388/e2cca56c-95ee-11e5-8832-f19d4097d3c3.png)

Все обновления от телеграма попадают в Bot->processUpdate($update) и только во время приема платежа точка входа Bot->processPaymentResult().

## Установка через composer

В файл `composer.json` добавить текущий репозиторий:

````
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/nomorespellingerrors/teledit"
    }
],
"require": {
    ...,
    "nomorespellingerrors/teledit": "*"
}
````

Потом запустить: `php composer.phar update`.

## Разработка

[![Codacy Badge](https://api.codacy.com/project/badge/67233ff2f8c74bf2b55ca98d736991ae)](https://www.codacy.com/app/terehov-alexander-serg/teledit)

Важные мелочи:

* Все примеры данных получены из реальных тестов - на конец ноября 2015 им можно доверять и пользоваться при дебаге.
* Для тестирования кода в боевых условиях удобнее всего развернуть у себя фейкового бота, мы делали все на живую и получилось плохо.
* Все тесты класса User требуют интернета и подключаются к parse.com, потому работают долго. Перед тестами очистите все данные из тестового приложения (данные для подключения в tests/config_tests.ini).

Разработано в философии TDD и без браузера, но с юнит-тестами. Для разработки в этом стиле рекомендую работать в PHPStorm, настройка:

![phpunit-configuration](https://cloud.githubusercontent.com/assets/1920639/10717321/e047bd38-7b5c-11e5-9850-7d991eb8308e.png)
