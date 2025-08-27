<?php

declare(strict_types=1);

namespace App\Constants\Config;

class ServiceConstants
{
    final public const ADMIN_DISCORD_CHANNEL_QUALIFIED = 'services.discord.admin_discord_channel';
    final public const DB_UPDATES_DISCORD_CHANNEL_QUALIFIED = 'services.discord.db_updates_discord_channel';
    final public const SUBMISSIONS_DISCORD_CHANNEL_QUALIFIED = 'services.discord.submissions_discord_channel';

    final public const OPENAI_BEARER_TOKEN = 'services.openai.token';

    final public const ANILIST_CLIENT_ID = 'services.anilist.client_id';
    final public const ANILIST_CLIENT_SECRET = 'services.anilist.client_secret';
    final public const ANILIST_REDIRECT_URI = 'services.anilist.redirect_uri';

    final public const MAL_CLIENT_ID = 'services.mal.client_id';
    final public const MAL_CLIENT_SECRET = 'services.mal.client_secret';
    final public const MAL_REDIRECT_URI = 'services.mal.redirect_uri';
}
