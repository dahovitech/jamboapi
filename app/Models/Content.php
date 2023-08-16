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
use Illuminate\Database\Eloquent\SoftDeletes;

class Content extends Model
{
    use SoftDeletes;

    protected $table = "content";

    protected $fillable = ['project_id', 'collection_id', 'locale', 'form_id', 'created_by', 'updated_by', 'published_at', 'published_by'];

    protected $casts = [
        'project_id' => 'integer',
        'collection_id' => 'integer',
        'form_id' => 'integer',
    ];

    protected $hidden = ['deleted_at'];

    public function meta(){
        return $this->hasMany('App\Models\ContentMeta');
    }

    public function collection(){
        return $this->belongsTo('App\Models\Collection');
    }

    public function form(){
        return $this->belongsTo('App\Models\Form');
    }
}
