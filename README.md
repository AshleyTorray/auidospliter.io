# 🎉 DEMO Audio spliter app

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
- Install Package

```shell
composer require laravel/breeze --dev
```

- Configure Environment

```shell
cp .env.example .env
```

- Migrate

```
php artisan breeze:install --dark
 
php artisan migrate
npm install
npm run dev
```
- install pacakage via composer
```install composer




### 🏆 Run

- [http://localhost:8000/](http://localhost:8000/) username : `admin` password : `admin`

```shell
php artisan serve
```
