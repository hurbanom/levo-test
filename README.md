# Larabank
Este repositorio contiene el desarrollo de prueba llamado Larabank

## Stack

 - Laravel 8.75
 - PHP >= 7.4.x
 - Composer
 - Base de Datos PostgreSQL >= 9.x o MySQL >= 8.x
 - Redis >= 6.0.6 *(para el procesamiento de Queues)*

## Instalación

El sitio está construido en Laravel, por lo que antes de clonar el repositorio es necesario asegurarnos que tenemos instalado [Composer](https://getcomposer.org/) en nuestra computadora.

>    ***Clonar el repositorio***   
>    ``` git clone git@github.com:hurbanom/levo-test.git ```
>    
>    ***Instalar sus dependencias***
>    `composer install`
>    
>    ***Crear la base de datos en PostgreSQL / MySQL***
>    
>    ***Generar el archivo .env y agregar las variables de entorno necesarias***
>    *Variables para el procesamiento de colas (Redis en este caso)*
>
>    `QUEUE_CONNECTION=redis`
>
>    `REDIS_HOST=`
>
>    `REDIS_PASSWORD=`
>
>    `REDIS_PORT=`
>
>    
>    *Variables para el envío de correo (Mailtrap en este caso, que sirve para testear el envío de correos)*
>
>    `MAIL_MAILER=`
>
>    `MAIL_HOST=`
>
>    `MAIL_PORT=`
>
>    `MAIL_USERNAME=`
>
>    `MAIL_PASSWORD=`
>
>    `MAIL_ENCRYPTION=`
>     
>    *Variables para la conexión a la base de datos*
>    
>    `DB_CONNECTION=`
>    
>    `DB_HOST=`
>    
>    `DB_PORT=`
>    
>    `DB_DATABASE=`
>    
>    `DB_USERNAME=`
>    
>    `DB_PASSWORD=`
>    
>   ***Generar la Application Key***
>    `cp .env.example .env`
>    
>    `php artisan key:generate`
>    *Verificar que en el archivo .env exista la variable APP_KEY*   
>    
>    ***Ejecutar los migrates***
>    `php artisan migrate`
>
> ***Iniciar el Proyecto***
>  `php artisan serve`
>  
>  Si todo sale bien podemos acceder a nuestro proyecto en   la URL http://127.0.0.1:8000

## ¿Qué hace? y ¿Cómo lo hace?

Se cuenta con 3 endpoints disponibles para poder realizar las pruebas, La URL base para pruebas locales es http://127.0.0.1:8000

| Endpoint | Tipo | Descripción |
|--|--|--|
|{url_base}/api/crearCuenta  | POST | Permite generar una nueva cuenta para poder efectuar depositos y retiros de dinero  |
|{url_base}/api/depositar  | POST | Permite realizar el depósito de fondos a la cuenta  |
|{url_base}/api/retirar | POST | Permite realizar el retiro de dinero de la cuenta.|

Notas:
- En caso de intentar realizar un retiro que deja la cuenta en 0 la operación se bloquea.
- En caso de realizar más de 3 intentos de retiro los cuales dejan saldo deudor se realiza el envío de un email con una oferta de préstamo.
- En caso de rebasar el limite diario de $10,000 se envía un correo con las operaciones de las últimas 48 horas al director del banco y en caso de tratarse de un retiro este se bloquea.

## Ejemplos de las peticiones

Para consultar el consumo de los endpoints está disponible la documentación con ejemplos de peticiones:

https://documenter.getpostman.com/view/228994/Uyxbpowe

## Pruebas Unitarias

Para ejecutar las pruebas es necesario ejecutar el siguiente comando:
`php artisan test`

Si se requiere generar el reporte se tiene que ejecutar el comando agregando la opción  *--coverage-html reports/*
`php artisan test --coverage-html reports/`
