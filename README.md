<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://rohutech.com/wp-content/uploads/2022/08/laravel-petshop-api-transparent.png" width="300"></a></p>

## About Laravel PetShop API

Its a PetShop where you can have access to thousands of products for your Pet built over Laravel Framework version 9

## Github repo
    https://github.com/rohu2187/petshop
    

## DEMO
    Live Swagger Documentation - https://petshop.rohutech.com/api/documentation

# Steps to Set up on local machine

1. Get the application files
   1. download the zip or clone it from github - https://github.com/rohu2187/petshop 
   2. put it at <your-sites-or-htdocs-folder-path> from where you can load the application
2. Create a DB in mysql DB with name "petshop"
3. run php artisan migrate
4. run php artisan db:seed
5. run php artisan serve
6.  To access Swagger Documentation on local
    1.  can be done at api/documentation, so if using php serve, the url would be http://127.0.0.1:8000/api/documentation