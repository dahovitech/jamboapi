<?php
/*
 * This file is part of the JamboapiCMS package.
 *
 * (c) Prudence ASSOGBA <prudence@dahovi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Http\Resources;

use App\Models\Project;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $project = Project::find($this->project_id);

        $media = [
            'id' => $this->id,
            'file_name' => $this->name,
            'full_url' => $this->full_url,
        ];

        $image_types = ['jpg', 'jpeg', 'png', 'bmp', 'gif', 'webp'];
        if(in_array($this->type, $image_types)){
            $media['thumb'] = $this->full_url_thumb;
        }

        $media['caption'] = $this->caption;
        $media['size'] = $this->size;

        if(in_array($this->type, $image_types)){
            $media['width'] = $this->width;
            $media['height'] = $this->height;
        }

        return $media;
    }
}
