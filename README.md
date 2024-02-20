# 🎉 Audio spliter app

This project is needed for spliting the audio files and removing the noises. Also, This app is made by using the FFMpeg PHP pacakge and Excel libraries. This app implements the funcitons as follow:
1. access excel log data from iiko.com server
2. on FTP server, analyze the audio files and decrease the noise and split them per each person.
3. everything runs automatically/manually.

![version](https://img.shields.io/badge/version-1.0-blue)
![rating](https://img.shields.io/badge/rating-★★★★★-yellow)
![uptime](https://img.shields.io/badge/uptime-100%25-brightgreen)

### 🚀 Setup

 1.update apt, install Apache Web Server, allow the Apache in firewell

```shell
$ sudo apt update
$ sudo apt install apache2
$ sudo ufw allow in "Apache"
$ sudo apt install mysql-server
```
2. Traffic on port 80 is now allowed through the firewall. When prompted, confirm installation by typing Y, and then ENTER.
   First, open up the MySQL prompt:
```shell
$ sudo mysql
```
Next, you have to configure user authorization in MySql database

```shell
mysql>ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY ''
mysql>exit
$ sudo mysql_secure_installation
```
3.  Composer requires php-cli in order to execute PHP scripts in the command line, and unzip to extract zipped archives.
4.  We’ll install these dependencies now.

```shell
$ sudo apt install php php-cli php-common php-mbstring php-xml php-zip php-mysql php-pgsql php-sqlite3 php-json php-bcmath php-gd php-tokenizer php-xmlwrite
$sudo apt update
```

First, update the package manager cache by running:
- Install Package

```shell
$ sudo apt install curl php-cli php-mbstring git unzip
$ curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=compose
$ composer require laravel/breeze --dev
```

- Configure Apache and Environment

```shell
$ sudo nano /etc/apache2/sites-available/audiospliter.conf
```
 Replace your-project-name with the actual name of your project.

Add the following content to the configuration file:
```shell
<VirtualHost *:80>
    ServerName 109.71.12.115
    DocumentRoot /var/www/html/audiospliter/public
    <Directory /var/www/html/audiospliter>
        AllowOverride All
    </Directory>
</VirtualHost>
```
Replace your-domain-or-ip with your actual domain name or server IP address.

Enable the Apache rewrite module:
```shell
$ sudo a2enmod rewrite
$ sudo a2ensite audiospliter.conf
$ sudo systemctl restart apache2
```
Add the following content to the server block:
```
server {
    listen 80;
    server_name 109.71.12.115;
    root /var/www/html/audiospliter/public;
    index index.php;
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
    }
    location ~ /\.ht {
        deny all;
    }
}
```
Enable the Nginx server block:
```shell
$ sudo ln -s /etc/nginx/sites-available/audiospliter /etc/nginx/sites-enabled/
$ sudo nginx -t
$ sudo systemctl restart nginx
```
- Migrate

```
$ cp .env.example .env
$ php artisan breeze:install --dark
$ php artisan key:generate
$ php artisan migrate
$ npm install
$ npm run dev
$ sudo chown -R www-data:www-data /var/www/html/audiospliter/storage
$ sudo chmod -R 775 /var/www/html/audiospliter/storage
```
- install composer & install pacakage via composer
```shell
$ composer require
```
- move the audio_new, excellog directories to root of project
```shell
$ php artisan serve
```

### 🏆 Run

- [http://audiospliter/](audiospliter/)

