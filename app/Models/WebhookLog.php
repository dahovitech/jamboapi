<?php
/*
 * This file is part of the JamboapiCMS package.
 *
 * (c) Prudence ASSOGBA <prudence@dahovi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookLog extends Model
{
    protected $fillable = [
        'project_uuid',
        'webhook_id',
        'action',
        'url',
        'status',
        'request',
        'response',
    ];

    protected $casts = [
        'webhook_id' => 'integer',
    ];
}
