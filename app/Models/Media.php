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
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Media extends Model
{
    use HasFactory;

    protected $table = 'media';

    protected $fillable = ['project_id', 'name', 'type', 'size', 'width', 'height', 'caption', 'disk'];

    protected $casts = [
        'project_id' => 'integer',
        'size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
    ];

    public function getFullUrlAttribute($value) {
        if($this->disk === 'local'){
            return env('APP_URL').'/uploads/'.$this->project->uuid.'/'.$this->name;
        } elseif($this->disk === 's3') {
            return Storage::disk('s3')->url('public/'.$this->project->uuid.'/'.$this->name);
        }
    }
    public function getFullUrlThumbAttribute($value) {
        if($this->disk === 'local'){
            return env('APP_URL').'/uploads/thumb/'.$this->project->uuid.'/'.$this->name;
        } elseif($this->disk === 's3') {
            return Storage::disk('s3')->url('public/'.$this->project->uuid.'/thumbnails/'.$this->name);
        }
    }

    protected $appends = ['full_url', 'full_url_thumb'];

    public function project() {
        return $this->belongsTo(Project::class);
    }
}
