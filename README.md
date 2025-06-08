# Разработка SSO-решения с использованием Laravel

## Описание:
Реализация PHP SSO Server с примером микросервиса, который верифицирует пользователей по access token без обращения к SSO серверу для проверки. 
Соблюдены основные требования:

> 1. SSO-сервер:  
   ○ Реализовать REST API для:  
   ■ Авторизации пользователя с выдачей Access и Refresh Tokens.  
   ■ Обновления пользователю Access Token с использованием Refresh
   Token.  
   ○ Использовать JWT (JSON Web Token) для Access Tokens. Содержит минимально
   необходимую информацию (например, user_id, roles, exp).  
   ○ Генерировать Refresh Tokens в виде UUID и хранить их в Redis.  
   ○ Хранить базовую информацию о пользователе и креденциалы в базе данных  
   ○ Обеспечить обработку ошибок по авторизации и валидации токенов  
> 2. API для микро-сервиса:  
   ○ Разработать middleware для микросервисов, которое:  
   ■ Проверяет AccessToken подпись JWT и срок его действия, без
   обращения к серверу SSO!  
   ■ Передает информацию о пользователе (из JWT) в обработчик запроса,
   если токен валиден.
> 3. Redis:  
   ○ Использовать Redis для Хранения Refresh Tokens:  
   ○ Реализовать стратегии для очистки истекших токенов.
> 4. Nats jetstream  
   ○ Для асинхронной трансляции уведомлений об успешной авторизации/выдаче
   токенов на конкретный топик и сервер. Разворачивать сервер Nats не нужно.
   Используется только PHP-клиент
> 5. OAuth2:  
   ○ Поддержать авторизацию через сторонние OAuth2-провайдеры (Google).
> 6. Docker (опционально):  
   ○ Настроить Docker-контейнер для SSO-сервера


## 1. Конфигурация проекта
Скопировать .env.example в .env, заполнить переменные данными

## 2. Запуск контейнеров и миграции
```bash
(cd laravel_php_sso && docker-compose up -d) && (cd micro_service && docker-compose up -d)
(cd laravel_php_sso && docker-compose exec sso-server php artisan migrate) && (cd micro_service && docker compose exec microservice-app php artisan migrate)
```


## 3. Коллекции Postman
Для удобства проверки в корне проекта есть коллекции [documentation/Collection.postman_collection.json](documentation/Collection.postman_collection.json) для SSO Server и микросервиса

## 4. Тесты
Feature тесты для проверки генерации access и refresh tokens, регистрации:
```bash
 cd laravel_php_sso && docker-compose exec sso-server php artisan test --env=testing 
 ```

## 5. Примеры

### 1. Register
![Register](/documentation/img.png)  
![img.png](/documentation/response_reregister.png)
### 2. Login
![Login](/documentation/img_1.png)  
![response_login](/documentation/response_login.png)
### 3. Refresh
![Refresh](/documentation/img_2.png)  
![response_refresh](/documentation/response_refresh.png)
### 4. Logout
![Logout](/documentation/img_3.png)  
![response_logout](/documentation/response_logout.png)
### 5. Google
![Google](/documentation/img_4.png)  
![response_google](/documentation/response_google.png)
### 6. Create orders
![Create orders](/documentation/img_5.png)  
![response_create_order](/documentation/response_create_order.png)
### 7. List orders
![List orders](/documentation/img_7.png)  
![response_list_orders](/documentation/response_list_orders.png)
### 8. Show order
![Show order](/documentation/img_6.png)  
![response_show_order](/documentation/response_show_order.png)
### 9. Delete order
![9. Delete order](/documentation/img_8.png)  
![response_delete_order](/documentation/response_delete_order.png)