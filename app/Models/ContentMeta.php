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
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContentMeta extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'content_meta';

    protected $fillable = ['project_id', 'collection_id', 'content_id', 'field_name', 'value'];

    protected $casts = [
        'project_id' => 'integer',
        'collection_id' => 'integer',
        'content_id' => 'integer',
    ];
}
