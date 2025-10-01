<?php

declare(strict_types=1);

namespace App\Constants\Config;

class ServiceConstants
{
    final public const string ADMIN_DISCORD_CHANNEL_QUALIFIED = 'services.discord.admin_discord_channel';
    final public const string DB_UPDATES_DISCORD_CHANNEL_QUALIFIED = 'services.discord.db_updates_discord_channel';
    final public const string SUBMISSIONS_DISCORD_CHANNEL_QUALIFIED = 'services.discord.submissions_discord_channel';

    final public const string OPENAI_BEARER_TOKEN = 'services.openai.token';

    final public const string ANILIST_CLIENT_ID = 'services.anilist.client_id';
    final public const string ANILIST_CLIENT_SECRET = 'services.anilist.client_secret';
    final public const string ANILIST_REDIRECT_URI = 'services.anilist.redirect_uri';

    final public const string MAL_CLIENT_ID = 'services.mal.client_id';
    final public const string MAL_CLIENT_SECRET = 'services.mal.client_secret';
    final public const string MAL_REDIRECT_URI = 'services.mal.redirect_uri';
}
