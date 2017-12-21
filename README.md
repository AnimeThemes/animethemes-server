[AnimeThemes.moe](https://animethemes.moe/) is a hosting solution for [/r/AnimeThemes](https://www.reddit.com/r/AnimeThemes/), a simple and consistent repository of anime opening and ending themes.

# Staff

* paranarimasu ([Github](https://github.com/paranarimasu))
* ProWeebDev ([Github](https://github.com/ProWeebDev))
* Gaporigo ([Github](https://github.com/Gaporigo))

# Requirements

AnimeThemes runs on Laravel 5.5 and has the same requirements

* A web server
* `PHP >= 7.0.0`
* `composer`
* `npm`

# Installation

* Clone the repository
* Create/edit the `.env` file, providing database and S3 storage credentials
* Run `composer install` to download application packages
* Run `npm install` to download JavaScript packages
* Run `npm run dev` to install vendor JavaScript
* Run `php artisan migrate --seed` to initialize the database