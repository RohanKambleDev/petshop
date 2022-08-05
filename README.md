<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://rohutech.com/wp-content/uploads/2022/08/laravel-petshop-api-transparent.png" width="300"></a></p>

# About Laravel PetShop API
This is a API first approach application built to manage a petshop business.
These API's can be consumed by any front end tech stack be it in react, angular or offcourse any mobile app.
Currently the Application is built over Laravel Framework version 9, and it was last tested on laravel version 9.22.1

## Github repo
https://github.com/rohu2187/petshop

## Live Swagger Documentation
https://petshop.rohutech.com/api/documentation

### Features covered
- Migrations
- Seeders
- Facades
- Service Providers
- Middleware
- Created Service for JWT
- Implemented Feature Tests
- Independent Request classes for each routes
- Swagger Documentation to see the API's in action

### Packages used
1. To generate Swagger Documentation - https://github.com/DarkaOnLine/L5-Swagger
2. To create JWT tokens - https://github.com/lcobucci/jwt

### Steps to Set up on local machine

1. Get the application files
   1. download the zip or clone it from github - https://github.com/rohu2187/petshop 
   2. put it at `<your-sites-or-htdocs-folder-path>` from where you can load the application
2. Create a DB in mysql DB with name "petshop"
3. run `php artisan migrate`
4. run `php artisan db:seed`
5. run `php artisan serve`
6. To access Swagger Documentation on local
    1.  can be accessed at `<application-url>`/api/documentation, 
    2.  so if are using `php serve`, the url would be http://127.0.0.1:8000/api/documentation

### Run Feature Tests

1. To run all the tests as once run following command
   1. `php artisan test`