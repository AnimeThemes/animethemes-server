<p align="center">
<a href="https://github.com/paranarimasu/AnimeThemes/actions"><img src="https://github.com/paranarimasu/AnimeThemes/workflows/tests/badge.svg?branch=wiki" alt="tests"></a>
<a href="https://github.styleci.io/repos/111264405?branch=wiki"><img src="https://github.styleci.io/repos/111264405/shield?branch=wiki" alt="StyleCI"></a>
<a href="https://discordapp.com/invite/m9zbVyQ"><img src="https://img.shields.io/discord/354388306580078594.svg?label=&logo=discord&logoColor=ffffff&color=7389D8&labelColor=6A7EC2"></a>
</p>

[**AnimeThemes.moe**](https://animethemes.moe/) is a hosting solution for [**/r/AnimeThemes**](https://www.reddit.com/r/AnimeThemes/), a simple and consistent repository of anime opening and ending themes.

# Staff

* paranarimasu ([Github](https://github.com/paranarimasu))
* ProWeebDev ([Github](https://github.com/ProWeebDev))
* Gaporigo ([Github](https://github.com/Gaporigo))

# Requirements

AnimeThemes runs on Laravel 8.x and has the same requirements

* A web server
* `PHP >= 7.3.0`
* `composer`
* `npm`

# Installation

* Clone the repository
* Create/edit the `.env` file, providing database and S3 storage credentials
* Run `composer install` to download application packages
* Run `npm install` to download JavaScript packages
* Run `npm run dev` to install vendor JavaScript
* Run `php artisan migrate --seed` to initialize the database
