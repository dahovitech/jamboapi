<?php
/*
 * This file is part of the JamboapiCMS package.
 *
 * (c) Prudence ASSOGBA <prudence@dahovi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Listeners;

use Spatie\WebhookServer\Events\FinalWebhookCallFailedEvent;

class FinalWebhookCallFailedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  FinalWebhookCallFailedEvent  $event
     * @return void
     */
    public function handle(FinalWebhookCallFailedEvent $event)
    {
        $log = [
            'project_uuid' => $event->payload['project_id'],
            'webhook_id' => $event->payload['webhook_id'],
            'action' => $event->payload['action'],
            'url' => $event->webhookUrl,
            'status' => 'failed',
            'request' => json_encode($event->payload),
            'response' => $event->response != null ? $event->response->getBody()->getContents() : '',
        ];
        \App\Models\WebhookLog::create($log);
    }
}
