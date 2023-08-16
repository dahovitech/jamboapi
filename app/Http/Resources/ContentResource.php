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

use App\Models\Media;
use App\Models\Content;
use App\Models\Collection;
use App\Http\Resources\MediaResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ContentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $content = [
            'id' => $this->id,
            'locale' => $this->locale,
        ];

        if($this->created_at !== null){
            $content['created_at'] = $this->created_at->toDateTimeString();
        }
        if($this->updated_at !== null){
            $content['updated_at'] = $this->updated_at->toDateTimeString();
        }
        if($this->published_at !== null){
            $content['published_at'] = $this->published_at;
        }

        $collection = $this->collection;
        $meta = $this->meta;

        foreach ($collection->fields as $field) {
            foreach ($meta as $m) {
                if($field->name == $m->field_name){
                    $options = json_decode($field->options);

                    if(!@$options->hiddenInAPI){
                        if(isset($options->repeatable) && $options->repeatable) {
                            if($field->type == 'number'){
                                $content[$m->field_name][] = (float)$m->value;
                            } else {
                                $content[$m->field_name][] = $m->value;
                            }
                        } else {
                            if($field->type == 'boolean'){
                                $content[$m->field_name] = $m->value == 1 ? true : false;
                            } elseif($field->type == 'password'){
                            } elseif($field->type == 'number'){
                                $content[$m->field_name] = (float)$m->value;
                            } elseif($field->type == 'media'){
                                if($options->media->type == 1){
                                    $media = Media::where('project_id', $this->project_id)->where('id', (int)$m->value)->first();
                                    $content[$m->field_name] = new MediaResource($media);
                                } else {
                                    $files_arr = explode(',', $m->value);
                                    $media = Media::where('project_id', $this->project_id)->whereIn('id', $files_arr)->get();
                                    $content[$m->field_name] = MediaResource::collection($media);
                                }
                            } elseif($field->type == 'relation'){
                                $relation_collection = $options->relation->collection;

                                if($options->relation->type == 1){
                                    $relation = Content::with('meta')->where('project_id', $this->project_id)
                                                    ->where('collection_id', $relation_collection)
                                                    ->whereNotNull('published_at')
                                                    ->where('id', $m->value);

                                    $selectFields = ['id', 'project_id', 'collection_id', 'locale'];

                                    if($this->created_at !== null){
                                        $selectFields[] = 'created_at';
                                    }
                                    if($this->updated_at !== null){
                                        $selectFields[] = 'updated_at';
                                    }
                                    if($this->published_at !== null){
                                        $selectFields[] = 'published_at';
                                    }
                                    $relation = $relation->select($selectFields)->first();

                                    $content[$m->field_name] = new ContentResource($relation);
                                } else {
                                    $relation_arr = explode(',', $m->value);

                                    $relation = Content::with('meta')->where('project_id', $this->project_id)
                                                    ->where('collection_id', $relation_collection)
                                                    ->whereIn('id', $relation_arr)
                                                    ->whereNotNull('published_at');

                                    $selectFields = ['id', 'project_id', 'collection_id', 'locale'];

                                    if($this->created_at !== null){
                                        $selectFields[] = 'created_at';
                                    }
                                    if($this->updated_at !== null){
                                        $selectFields[] = 'updated_at';
                                    }
                                    if($this->published_at !== null){
                                        $selectFields[] = 'published_at';
                                    }
                                    $relation = $relation->select($selectFields)->get();

                                    $content[$m->field_name] = ContentResource::collection($relation);
                                }
                            } else {
                                $content[$m->field_name] = $m->value;
                            }
                        }
                    }
                }
            }
        }

        return $content;
    }
}
