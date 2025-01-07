# NewLife Rest api

**Описание:**
Мега супер пупер (потом напишу описание)

---
## Функции

### Технологии стэка

### Для авторизованных пользователей

### Для неавторизованных пользователей

### Структура

---
# Как установить?

### Зависимости
- PHP 8.2 и выше
- Extension pdo_mysql (зависимость php.ini)
- Composer installer
- Laravel 11.x
- Node.js и npm (необязательно)

## Установка

### Шаг 1. Установка репозитория
```bash
   git clone https://github.com/jurball/newLifeApiLaravel.git
```

### Шаг 2. Директория app
```bash
  cd app
```
 
### Шаг 3. Установка пакетов через composer
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

### Шаг 4. Очистите кэш
```bash
  php artisan config:clear
  php artisan route:clear
```

## Конфигурация 

Если есть только .env.example его переменуйте в ".env". Настроте только эти параметры
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_user
DB_PASSWORD=your_password
```

## Миграция Базы Данных

Для миграции
```bash
  php artisan migrate
```

Для отката
```bash
  php artisan migrate:rollback
```

## Запуск

```bash
  php artisan serv --host=127.0.0.1
```

---
## Тестирования эндпоинтов
### Users
#### **Authentication**
- **Endpoint:** `POST /authorization/`
- **Description:** Login user into account.
- **Headers**
    ```headers
    Content-Type: application/json
- **Request Body:**
    ```json
  {
    "email": "user@example.com",
    "password": "E1x",
  }
  