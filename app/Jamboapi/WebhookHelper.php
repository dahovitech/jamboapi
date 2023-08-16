<?php
/*
 * This file is part of the JamboapiCMS package.
 *
 * (c) Prudence ASSOGBA <prudence@dahovi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Jamboapi;

use App\Http\Resources\ContentResource;
use App\Models\Webhook;
use Spatie\WebhookServer\WebhookCall;

class WebhookHelper
{
    public static function processWebhooks($content, $event, $source)
    {

        $collection_id = $content['collection_id'];
        $webhooks = Webhook::where('status', 1)
                            ->whereJsonContains('events', $event)
                            ->whereJsonContains('sources', $source)
                            ->whereHas('collections', function ($query) use ($collection_id) {
                                $query->where('collection_id', $collection_id);
                            })->get();

        foreach ($webhooks as $wh) {
            $webhookCall = WebhookCall::create();
            $webhookCall->url($wh->url);

            if ($wh->secret === null) {
                $webhookCall->doNotSign();
            } else {
                $webhookCall->useSecret($wh->secret);
            }

            if ($wh->payload) {
                $project = \App\Models\Project::find($content['project_id']);
                $collection = \App\Models\Collection::find($content['collection_id']);

                $payload = [
                    'action' => $event,
                    'source' => $source,
                    'project_id' => $project->uuid,
                    'collection' => $collection->name,
                    'collection_slug' => $collection->slug,
                    'webhook_id' => $wh->id,
                ];

                if ($event == 'content.deleted') {
                    $payload['item_id'] = $content['item_id'];
                } else {
                    $payload['item'] = json_decode(json_encode(new ContentResource($content)));
                }

                if ($event == 'form.submitted') {
                    $payload['form_id'] = $content['form_id'];
                }

                $webhookCall->payload($payload);
            }

            $webhookCall->dispatch();
        }
    }
}
