<?php

namespace App\Jobs;

use App\Contracts\Events\DiscordMessageEvent;
use App\Jobs\Middleware\RateLimited;
use App\Notifications\DiscordNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class SendDiscordNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The event.
     *
     * @var \App\Contracts\Events\DiscordMessageEvent
     */
    protected $event;

    /**
     * Create a new job instance.
     *
     * @param \App\Contracts\Events\DiscordMessageEvent $event
     * @return void
     */
    public function __construct(DiscordMessageEvent $event)
    {
        $this->event = $event;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Notification::route('discord', $this->event->getDiscordChannel())
            ->notify(new DiscordNotification($this->event->getDiscordMessage()));
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array
     */
    public function middleware()
    {
        return [new RateLimited];
    }

    /**
     * Determine the time at which the job should timeout.
     *
     * @return \DateTime
     */
    public function retryUntil()
    {
        return now()->addMinutes(15);
    }
}
