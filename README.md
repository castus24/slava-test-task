# Laravel Тестовове задание

## Установка

### Требования

- PHP 8.0^
- Laravel 10
- MySQL 5.7+ (или другой поддерживаемый драйвер базы данных)

### Установка

1. Клонируйте репозиторий:

   ```
   git clone https://github.com/castus24/slava-test-task.git

2. Установите зависимости с помощью Composer:

   ```bash
   composer install

3. Скопируйте файл .env.example в .env и настройте параметры подключения к базе данных:
   В .env установите настройки Mail для получения по почте пароля пользователя.
   Работать он будет используя очереди. Установите QUEUE_CONNECTION=database.

   ```
   .env.example .env
   ```

4. Сгенерируйте ключ приложения:

   ```bash
    php artisan key:generate
   ```

5. Создайте миграцию для таблицы очередей.

   ```bash
   php artisan queue:table
   ```

6. Запустите миграции для сидера и для создания таблиц:

   ```bash
   php artisan migrate --seed
   ```
7. Запустите cервер artisan и npm, а также воркер для очередей:

   ```
   php artisan serve
   ```
   ```
   php artisan queue:work
   ```

## Использование (например через Postman)

### Отправьте запрос на http://localhost:8000/api/upload и прикерпите xlsx файл для загркузки данных из Excel в базу.
### При удачной закрузке логи ошибок будут сохранены в result.txt в корне проекта.
### Отправьте запрос на http://localhost:8000/api/rows для получения данных из базы.







