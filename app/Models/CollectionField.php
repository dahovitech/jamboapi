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

class CollectionField extends Model
{

    protected $table = 'collection_fields';

    protected $fillable = ['type', 'label', 'name', 'description', 'placeholder', 'options', 'validations', 'project_id', 'collection_id', 'order'];

    protected $casts = [
        'project_id' => 'integer',
        'collection_id' => 'integer',
    ];

    public function project(){
        return $this->belongsTo('App\Models\Project', 'project_id');
    }

    public function collection(){
        return $this->belongsTo('App\Models\Collection', 'collection_id');
    }
}
