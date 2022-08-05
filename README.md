<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://rohutech.com/wp-content/uploads/2022/08/laravel-petshop-api-transparent.png" width="400"></a></p>

## About Laravel PetShop API

Its a PetShop where you can have access to thousands of products for your Pet built over Laravel Framework version 9

## Set up Steps

# Add following to - /usr/local/etc/httpd/extra/httpd-vhosts.conf
<VirtualHost *:80>
    DocumentRoot "/Users/rohankamble/Sites/life/buckhill/petshop/public"
    ServerName petshop.test
    ServerAlias petshop.test
    ErrorLog "/usr/local/var/log/petshop.test-error.log"
    CustomLog "/usr/local/var/log/petshop.test-access.log" common
    SetEnv APPLICATION_ENV "development"
    <Directory "/Users/rohankamble/Sites/life/buckhill/petshop/public">
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

# Add following to - /private/etc/hosts
127.0.0.1 petshop.test

# create a DB in mysql DB with name petshop

# run php artisan migrate

# run php artisan db:seed