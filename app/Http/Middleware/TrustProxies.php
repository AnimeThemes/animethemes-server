<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Fideloper\Proxy\TrustProxies as Middleware;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TrustProxies.
 */
class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * @var array|string|null
     */
    protected $proxies = '*';

    /**
     * The headers that should be used to detect proxies.
     *
     * @var null|string|int
     */
    protected $headers = Request::HEADER_X_FORWARDED_FOR;
}
