<p align="center">
<a href="https://github.com/AnimeThemes/animethemes-server/actions/workflows/test.yml"><img src="https://github.com/AnimeThemes/animethemes-server/workflows/Tests/badge.svg?branch=main" alt="tests"></a>
<a href="https://github.com/AnimeThemes/animethemes-server/actions/workflows/static-analysis.yml"><img src="https://github.com/AnimeThemes/animethemes-server/workflows/Static%20Analysis/badge.svg?branch=main" alt="static-analysis"></a>
<a href="https://github.styleci.io/repos/111264405?branch=main"><img src="https://github.styleci.io/repos/111264405/shield?branch=main" alt="StyleCI"></a>
<a href="https://discordapp.com/invite/m9zbVyQ"><img src="https://img.shields.io/discord/354388306580078594.svg?label=&logo=discord&logoColor=ffffff&color=7389D8&labelColor=6A7EC2"></a>
<a href="https://github.com/AnimeThemes/animethemes-server/blob/main/LICENSE"><img src="https://img.shields.io/github/license/AnimeThemes/animethemes-server"></a>
<a href="https://reddit.com/r/AnimeThemes"><img src="https://img.shields.io/reddit/subreddit-subscribers/AnimeThemes?style=social"></a>
<a href="https://x.com/AnimeThemesMoe"><img src="https://img.shields.io/twitter/follow/AnimeThemesMoe?style=social"></a>
</p>

[**AnimeThemes**](https://animethemes.moe/) is a simple and consistent repository of anime opening and ending themes. We provide direct links to high quality WebMs of your favorite OPs and EDs for your listening and discussion needs.

This is the repository for the server application that is responsible for AnimeThemes.moe resource management, API, and other services related to serving the AnimeThemes database.

This project is powered by [**Laravel**](https://laravel.com/), a PHP framework for web artisans.

# Installation

- [Prerequisites](#prerequisites)
- [Setup](#setup)
  - [Web Server](#web-server)
  - [Database](#database)
  - [PHP](#php)
- [Configuration](#configuration)
- [Elasticsearch](#elasticsearch)
- [Users](#users)
- [Local Storage](#local-storage)
- [Running](#running)

## Prerequisites

* [Laravel Herd](https://herd.laravel.com/) or a webserver such as [Apache](https://httpd.apache.org/download.cgi) or
[Nginx](https://www.nginx.com/resources/wiki/start/topics/tutorials/install/)
* PHP 8.5
* MySQL
* [composer](https://getcomposer.org/download/) for vendor dependencies

A LAMP stack, such as [XAMPP](https://www.apachefriends.org/download.html), can
also be used to set up Apache, MySQL, and PHP.

Alternatively, you may use [Laravel Herd](https://herd.laravel.com), which
provides a simple local development environment with PHP and a web server.

## Setup

```bash
# Clone the repository
git clone git@github.com:AnimeThemes/animethemes-server.git
cd animethemes-server

# Copy the contents of the `.env.example` file to a new file `.env`.
cp .env.example .env

# Install vendor dependencies
composer install

# Set a value for `APP_KEY`
php artisan key:generate

# Import dumps automatically, migrate the database and run seeders
php artisan db:sync
```

### Web Server

Next, we will configure our web server [here](/AnimeThemes/animethemes-server/wiki/Server-Setup) to serve the application.

If you are using a local development environment such as **Laravel Herd**, the web server and PHP runtime are already configured for you, and you can skip most of the manual web server setup steps.

### PHP

#### Required Configuration in `php.ini`

We should ensure that we have the following extensions enabled for php.

`fileinfo` - Needed to detect MIME type of files during seeding.

`gd` - Needed to fake image files for testing.

`pdo_mysql` - Needed to use MySQL.

In order to accept video uploads, we should ensure that php will accept requests of adequate sizes.

Set `post_max_size` to `200M`.

Set `upload_max_filesize` to `200M`.

### Database

Here we will create the database for our development environment. We will assume that we have installed MySQL.

**Remark:** Version 5.7+ is required.

**Remark:** Ensure that the `pdo_mysql` php extension is enabled in your `php.ini`.

If we have installed mysql on our server, we simply create the database using the MySQL command-line client.

## Configuration

Features that require external services are disabled by default. Here we will review the configuration options for enabling additional features.

Development needs will vary depending on the work being done. The list of custom configuration options can be found [here](/AnimeThemes/animethemes-server/wiki/Configuration) for review.

If we want to enable video streams, we need to set the `App\Features\AllowVideoStreams` value on DB to `true`. We recommend setting up a local archive for the `videos_local` disk.

If we want to enable discord notifications, we need to set the `allow_discord_notifications` value on DB to `true`. We will need to configure a [Queue](/AnimeThemes/animethemes-server/wiki/Configuration#queue) to process the dispatched events through a worker. Finally, we will need to create a [Discord application](https://discord.com/developers/applications) and register it `config/services.php`.

## Users

```sh
# Open the terminal of tinker
php artisan tinker

# Create the user
$user = App\Models\Auth\User::factory()->create(['name' => 'User Name', 'email' => 'example@example.com', 'password' => 'password', 'email_verified_at' => now()]);

# It is useful to create a user with the Admin role with permissions to all actions
# Assign the Admin role to the user
$user->assignRole('Admin');
```

## Elasticsearch

If we want to enable scout, we need to configure [Elasticsearch](/AnimeThemes/animethemes-server/wiki/Elasticsearch).

If we have configured Elasticsearch, migrate and import models into our indices using:

```sh
# Run the elastic migrations
php artisan elastic:migrate

# Import Models with a seeder
php artisan db:seed --class="Database\Seeders\Scout\ImportModelsSeeder"
```

## Local Storage

We are not required to set up s3 buckets in order to interact with media. We have the option to configure local filesystems that we can stream audio/video from and download scripts/dumps from.

Configure local filesystem disks in `.env`

```sh
AUDIO_DISK_DEFAULT=audios_local
AUDIO_DISKS=audios_local
...
DUMP_DISK=dumps_local
...
IMAGE_DISK=images_local
...
VIDEO_DISK_DEFAULT=videos_local
VIDEO_DISKS=videos_local
...
SCRIPT_DISK=scripts_local

```

By default, app storage directories will be used to store media. External directories can be specified as the root if media is stored elsewhere.

Remark: It is recommended to include a `.gitignore` at the root directory of the filesystem so that media files are not indexed by git.

```sh
AUDIO_DISK_ROOT="E:\\animethemes-audios\\"
...
DUMP_DISK_ROOT="E:\\animethemes-db-dumps\\"
...
IMAGE_DISK_ROOT="E:\\animethemes-images\\"
...
SCRIPT_DISK_ROOT="E:\\animethemes-scripts\\"
...
VIDEO_DISK_ROOT="E:\\animethemes-videos\\"
```

Create symbolic links to target storage directories.

```php
php artisan storage:link
```

## Running

After installation, restart the web server to apply the configuration.

If all went well, AnimeThemes should be live at `http://animethemes.test` (or whatever set the server name is set to).

# Contributing

Please review the [**Contributing Guide**](https://github.com/AnimeThemes/animethemes-server/wiki/Contributing) in the wiki for detailed instructions.

# Resources

Please make use of the #api channel in the [**Discord Server**](https://discordapp.com/invite/m9zbVyQ) for questions pertaining to the AnimeThemes database or API.
