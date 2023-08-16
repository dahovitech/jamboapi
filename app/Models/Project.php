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

use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasApiTokens, HasFactory;

    protected $table = "projects";

    protected $fillable = ['name', 'description', 'default_locale', 'locales', 'disk', 'public_api'];

    protected $hidden = ['deleted_at'];

    protected $casts = [
        'public_api' => 'boolean'
    ];

    protected  static  function  boot(){
        parent::boot();

        static::creating(function  ($model)  {
            $model->uuid = (string) Str::uuid()->getHex();
        });
    }

    public function collections(){
        return $this->hasMany('App\Models\Collection')->orderBy('order', 'ASC');
    }

    public function fields(){
        return $this->hasMany('App\Models\CollectionField');
    }

    public function content(){
        return $this->hasMany('App\Models\Content');
    }

    public function meta(){
        return $this->hasMany('App\Models\ContentMeta');
    }

    public function media(){
        return $this->hasMany('App\Models\Media');
    }

    public function webhooks()
    {
        return $this->hasMany('App\Models\Webhook');
    }

    public function webhook_logs(){
        return $this->hasMany('App\Models\WebhookLog', 'project_uuid', 'uuid');
    }

    public function forms(){
        return $this->hasMany('App\Models\Form');
    }
}
