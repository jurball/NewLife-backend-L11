# NewLife Rest api

**Описание:**
Облачное хранилище с возможностью разграничения прав доступа к файлам. (Laravel 11.x)

## Функции

### Для авторизованных пользователей

- возможность сброса авторизации (logout)
- работа с файлами
  - загрузка
  - редактирование
  - удаление
- разграничение прав доступа к файлам

### Для неавторизованных пользователей

- авторизация
- регистрация

<!--End NewLife Rest api-->

# Как установить?

### Зависимости

- PHP 8.2 и выше
- Extension pdo_mysql (зависимость php.ini)
- Composer installer
- Laravel 11.x

## Установка

### Шаг 1.

```bash
   git clone https://github.com/jurball/newLifeApiLaravel.git
```

### Шаг 2.

```bash
  cd app
```

### Шаг 3. Установка пакетов

```bash
  composer install
```

или

```bash
  php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
  php composer-setup.php
  php -r "unlink('composer-setup.php');"
  php composer.phar install
```

### Шаг 4. Конфигурация .env (скопировать из .env.example и вставить .env)

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_user
DB_PASSWORD=your_password
```

### Шаг 5.

```bash
  php artisan config:clear
  php artisan route:clear
```

### Шаг 6. Важно
```bash
  php artisan key:generate
  php artisan scribe:generate
  php artisan migrate
```

## Миграции

```bash
  php artisan migrate
```

Для отката

```bash
  php artisan migrate:rollback
```

## Запуск

```bash
  php artisan serve --host=127.0.0.1 --port=8000
```
После генерации **scribe** доступна ссылка `http://127.0.0.1:8000/docs`

<!--End Manual Installation-->

# Сервер

**_API-prefix:_** _апи префикс отсутствует_ (for example **http://{{host}}/registration**)

Тестовый аккаунт (после миграции выполнить команду для создания тестового аккаунта `$ php artisan db:seed`)

- email: test@gmail.com
- password: test

## Общие требования сервера

Функционал авторизованного пользователя не должен быть доступен гостю.

### Login failed

При попытке доступа гостя к защищенным авторизацией функциям системы во всех запросах возвращает ответ следующего вида:

- Status: 403
- Headers
  - Content-Type: application/json
- Body

```json
{
  "message": "Login failed"
}
```

### Forbidden for you

При попытке доступа авторизованным пользователем к функциям недоступным для него во всех запросах возвращает ответ следующего вида:

- Status: 403
- Headers
  - Content-Type: application/json
- Body

```json
{
  "message": "Forbidden for you"
}
```

### Not Found 404

При попытке получить не существующий возвращает ответ следующего вида:

- Status: 404
- Headers
  - Content-Type: application/json
- Body

```json
{
  "message": "Not found"
}
```

### Validation fail 422

В случае ошибок связанных с валидацией данных во всех запросах возвращает следующее тело ответа:

- Status: 422
- Headers
  - Content-Type: application/json
- Body

```json
{
  "success": false,
  "message": {
    "phone": ["field phone can not be blank"],
    "password": ["field password can not be blank"]
  }
}
```

## Эндпоинты

### 1. **_POST_** {{host}}/registration Регистрация

Request

- Method: **_POST_**
- Headers
  - Content-Type: application/json
- Body

```json
{
  "email": "admin@admin.ru",
  "password": "Qa1",
  "first_name": "name",
  "last_name": "last_name"
}
```

Response

- Status: 200
- Body

```json
{
  "success": true,
  "message": "Success",
  "token": "you_token"
}
```

### 2. **_POST_** {{host}}/authorization Авторизация

Request

- Method: **_POST_**
- Headers
  - Content-Type: application/json
- Body

```json
{
  "email": "admin@admin.ru",
  "password": "Qa1"
}
```

Response

- Status: 200
- Body

```json
{
  "success": true,
  "message": "Success",
  "token": "you_token"
}
```

### 3. **_GET_** {{host}} /logout Сброс токена

Request

- Method: **_GET_**

Response

- Status: 200
- Body

```json
{
  "success": true,
  "message": "Logout"
}
```

### 4. **_POST_** /files Загрузить файл

Request

- Method: **_POST_**
- Headers
  - Content-Type: application/json
- Body

```
FormData:
    “files”:<массив с файлами>
```

Response

- Status: 200
- Body

```json
[
  {
    "success": true,
    "message": "Success",
    "name": "Имя загруженного файла",
    "url": "{{host}}/files/qweasd1234",
    "file_id": "qweasd1234"
  },
  {
    "success": false,
    "message": "File not loaded",
    "name": "Имя НЕ загруженного файла"
  }
]
```

### 5. **_PATCH_** /files/<file_id> Редактировать файл

Request

- Method: **_PATCH_**
- Headers
  - Content-Type: application/json
- Body

```json
{
  "name": "new Name"
}
```

Response

- Status: 200
- Body

```json
{
  "success": true,
  "message": "Renamed"
}
```

### 6. **_DELETE_** /files/<file_id> Удалить файл

Request

- Method: **_DELETE_**
- Headers
  - Content-Type: application/json

Response

- Status: 200
- Body

```json
{
  "success": true,
  "message": "File already deleted"
}
```

### 7. **_GET_** /files/<file_id> Скачать файл

Request

- Method: **_GET_**
- Headers
  - Content-Type: application/json

Response

- Status: 200

```
Браузеру отдается файл для скачивания
```

### 8. **_POST_** /files/<file_id>/access Добавления прав доступа

Request

- Method: **_POST_**
- Headers
  - Content-Type: application/json
- Body

```json
{
  "email": "user@user.ru"
}
```

Response

- Status: 200
- Body

```json
[
  {
    "fullname": "name last_name",
    "email": "admin@admin.ru",
    "type": "author"
  },
  {
    "fullname": "user last_name",
    "email": "user@user.ru",
    "type": "co-author"
  }
]
```

### 9. **_DELETE_** /files/<file_id>/access Удаление прав доступа

Request

- Method: **_DELETE_**
- Headers
  - Content-Type: application/json
- Body

```json
{
  "email": "user@user.ru"
}
```

Response

- Status: 200
- Body

```json
[
  {
    "fullname": "name last_name",
    "email": "admin@admin.ru",
    "type": "author"
  }
]
```

### 10. **_GET_** /files/disk Просмотр файлов пользователя

Request

- Method: **_GET_**
- Headers
  - Content-Type: application/json

Response

- Status: 200
- Body

```json
[
  {
    "file_id": "qweasd1234",
    "name": "Имя файла",
    "url": "{{host}}/files/qweasd1234",
    "accesses": [
      {
        "fullname": "name last_name",
        "email": "admin@admin.ru",
        "type": "author"
      },
      {
        "fullname": "user last_name",
        "email": "user@user.ru",
        "type": "co-author"
      }
    ]
  },
  {
    "file_id": "aaaaaaaaaa",
    "name": "Имя файла 1",
    "url": "{{host}}/files/aaaaaaaaaa",
    "accesses": [
      {
        "fullname": "name last_name",
        "email": "admin@admin.ru",
        "type": "author"
      }
    ]
  }
]
```

### 11. **_GET_** /files/shared Просмотр файлов, к которым имеет доступ пользователь

Request

- Method: **_GET_**
- Headers
  - Content-Type: application/json

Response

- Status: 200
- Body

```json
[
  {
    "file_id": "qweasd1234",
    "name": "Имя файла",
    "url": "{{host}}/files/qweasd1234"
  },
  {
    "file_id": "aaaaaaaaaa",
    "name": "Имя файла 2",
    "url": "{{host}}/files/aaaaaaaaaa"
  }
]
```

<!--End Server-->

# Тестирования
Перед тестированием создать тестового пользователя с помощью команды `php artisan db:seed`

### NewlifeTest
Для выполнения тестов выполнить команду `php artisan test`. Всего тестов 5:
- Проверить формат Not Found
- Проверить формат Login failed
- Проверить на наличие всех таблиц в базе данных
- Проверить создан ли тестовый пользователь
- Тест на добавления и удаления

# Laravel App 

Раздел описывает:
- Контроллеры
- Промежуточное ПО
- Модели

## Контроллеры
- AuthController - Базовый контроллер для регистрации, авторизации и сброса токена пользователя.
- FileController - Базовый CRUD контроллер для загрузки файлов на сервер.

## Промежуточное ПО

- AuthMiddleware
- CheckAccessFileUserMiddleware
- DeniedMiddleware
- JsonAccept

### AuthMiddleware
Проверяет авторизован пользователь? Защищает защищенные маршруты.

### CheckAccessFileUserMiddleware
Проверяет на соавтора, если соавтор то чтения файла разрешен (применен для маршрута **GET /files/<file_id>** )

### DeniedMiddleware
Проверяет на существования ресурса (если нет ресурса отдаст 404),
проверяет также на доступность файла пользователя.

### JsonAccept
Устанавливает Accept на application/json по умолчанию, то есть отдать тело 
запроса в виде JSON. Используется глобально всего маршрута приложения.

## Модели
- User - модель пользователя 
- Files - модель файлов пользователя
- FileAccess - модель доступные файлы для пользователя
