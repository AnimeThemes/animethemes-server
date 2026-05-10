<p align="center">
<a href="https://github.com/AnimeThemes/animethemes-server/actions/workflows/test.yml"><img src="https://github.com/AnimeThemes/animethemes-server/actions/workflows/test.yml/badge.svg?branch=main" alt="tests"></a>
<a href="https://github.com/AnimeThemes/animethemes-server/actions/workflows/static-analysis.yml"><img src="https://github.com/AnimeThemes/animethemes-server/actions/workflows/static-analysis.yml/badge.svg?branch=main" alt="static-analysis"></a>
<a href="https://github.com/AnimeThemes/animethemes-server/actions/workflows/graphql.yml"><img src="https://github.com/AnimeThemes/animethemes-server/actions/workflows/graphql.yml/badge.svg?branch=main" alt="graphql"></a>
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
- [Pre Setup](#pre-setup)
  - [PHP](#php)
- [Setup](#setup)
  - [Laravel Herd](#laravel-herd)
  - [Docker](#docker)
  - [Running](#running)
- [Extra Configuration](#extra-configuration)
  - [Feature Flags](#feature-flags)
  - [Users](#users)
  - [Search](#search)
  - [Local Storage](#local-storage)
- [Contributing](#contributing)
- [Resources](#resources)

## Prerequisites

* [Laravel Herd](https://herd.laravel.com/) or [Docker](https://www.docker.com/)
* MySQL 8+ or Docker
* [composer](https://getcomposer.org/download/) or Docker

Docker will setup PHP, MySQL and Typesense for you. If you are on Windows, use the [WSL](https://learn.microsoft.com/windows/wsl/install) terminal.

Laravel Herd will setup PHP 8.5 for you. You should download and setup MySQL and Typesense manually.

## Pre Setup

### PHP

You can skip this if you are using Docker.

In order to accept video uploads, we should ensure that php will accept requests of adequate sizes.

Set `post_max_size` to `200M`.

Set `upload_max_filesize` to `200M`.

## Setup

### Laravel Herd

```bash
# Clone the repository
git clone git@github.com:AnimeThemes/animethemes-server.git
cd animethemes-server

# Create the database
mysql -h localhost -u root -p -e "CREATE DATABASE IF NOT EXISTS ``animethemes`` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Install dependencies, import dumps, migrate the database and run seeders
composer setup

# Generate an application key
php artisan key:generate

# Run the following in the project directory:
herd link admin.animethemes.test
herd link animethemes.test
herd link api.animethemes.test
herd link graphql.animethemes.test
```

### Docker

```bash
# Clone the repository
git clone git@github.com:AnimeThemes/animethemes-server.git
cd animethemes-server

cp .env.example-sail .env

# Install Composer dependencies using a throwaway container
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs

# Start sail
./vendor/bin/sail up -d

# Generate an application key
./vendor/bin/sail artisan key:generate

# Import dumps, migrate the database and run seeders
./vendor/bin/sail artisan db:sync --drop
```

You can optionally configure a shell alias following the [official Sail guide](https://laravel.com/docs/13.x/sail#configuring-a-shell-alias).
The following instruction assume you have configured a shell alias. If not you need to replace `sail` with `./vendor/bin/sail`.

Open the following file and paste the contents there:

For Windows/WSL: `C:\Windows\System32\drivers\etc\hosts`

For Linux: `/etc/hosts`

```
127.0.0.1 admin.animethemes.test
127.0.0.1 animethemes.test
127.0.0.1 api.animethemes.test
127.0.0.1 graphql.animethemes.test
```

Restart the container:

```bash
sail restart
sail artisan optimize
```

### Running

If all went well, AnimeThemes should be live at `http://animethemes.test`.

## Extra Configuration

### Feature Flags

Features that require external services are disabled by default. Here we will review the configuration options for enabling additional features.

For example, if we want to enable video streams, we need to set the `App\Features\AllowVideoStreams` value on DB to `true`. We recommend setting up a local archive for the `videos_local` disk.

### Users

```sh
# Open the terminal of tinker
php artisan tinker
# or through Docker
sail artisan tinker

# Create the user
$user = User::factory()->create(['name' => 'User Name', 'email' => 'example@example.com', 'password' => 'password', 'email_verified_at' => now()]);

# It is useful to create a user with the Admin role with permissions to all actions
# Assign the Admin role to the user
$user->assignRole('Admin');
```

### Search

You can skip this if you are using Docker.

If we want to enable scout, we need to configure a search engine.

Change the `SCOUT_DRIVER` variable in `.env` to "typesense". Add additional configuration like host and port.

Import models into our indices using:

```sh
# Import Models with a seeder
php artisan scout:import-all
```

### Local Storage

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

# Contributing

Please review the [**Contributing Guide**](https://github.com/AnimeThemes/animethemes-server/wiki/Contributing) in the wiki for detailed instructions.

# Resources

Please make use of the #api channel in the [**Discord Server**](https://discordapp.com/invite/m9zbVyQ) for questions pertaining to the AnimeThemes database or API.
