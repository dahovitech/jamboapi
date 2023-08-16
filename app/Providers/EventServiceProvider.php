<?php
/*
 * This file is part of the JamboapiCMS package.
 *
 * (c) Prudence ASSOGBA <prudence@dahovi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        \App\Events\ContentCreated::class => [
            \App\Listeners\ProcessWebhooks::class
        ],
        \App\Events\ContentUpdated::class => [
            \App\Listeners\ProcessWebhooks::class
        ],
        \App\Events\ContentTrashed::class => [
            \App\Listeners\ProcessWebhooks::class
        ],
        \App\Events\ContentDeleted::class => [
            \App\Listeners\ProcessWebhooks::class
        ],
        \App\Events\ContentPublished::class => [
            \App\Listeners\ProcessWebhooks::class
        ],
        \App\Events\ContentUnpublished::class => [
            \App\Listeners\ProcessWebhooks::class
        ],
        \App\Events\ContentRestored::class => [
            \App\Listeners\ProcessWebhooks::class
        ],
        \App\Events\FormSubmitted::class => [
            \App\Listeners\ProcessWebhooks::class
        ],

        \Spatie\WebhookServer\Events\WebhookCallSucceededEvent::class => [
            \App\Listeners\WebhookCallSucceededListener::class,
        ],
        \Spatie\WebhookServer\Events\FinalWebhookCallFailedEvent::class => [
            \App\Listeners\FinalWebhookCallFailedListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
