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

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Webhook extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'description',
        'url',
        'secret',
        'collection_ids',
        'events',
        'sources',
        'payload',
        'status',
        'created_by',
    ];

    protected $casts = [
        'project_id' => 'integer',
        'collection_ids' => 'array',
        'events' => 'array',
        'sources' => 'array',
        'payload' => 'boolean',
        'status' => 'boolean',
    ];

    public function project()
    {
        return $this->belongsTo('App\Models\Project');
    }

    public function collections()
    {
        return $this->belongsToMany('App\Models\Collection', 'webhook_collections')->select(['name']);
    }

    public function logs(){
        return $this->hasMany('App\Models\WebhookLog', 'webhook_id');
    }
}
