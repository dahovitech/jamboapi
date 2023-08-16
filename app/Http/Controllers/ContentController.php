<?php
/*
 * This file is part of the JamboapiCMS package.
 *
 * (c) Prudence ASSOGBA <prudence@dahovi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Media;
use App\Models\Content;
use App\Models\Project;
use App\Models\Collection;
use App\Models\ContentMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Exceptions\UnauthorizedException;

class ContentController extends Controller
{
    /**
     * Get project by id
     *
     * @param int $id
     * @return \App\Models\Project
     */
    public function project($id){
        $project = Project::with('collections')->findOrFail($id);

        $user = auth()->user();
        if(!$user->isSuperAdmin() && !$user->hasRole('admin'.$project->id) && !$user->hasRole('editor'.$project->id)){
            throw UnauthorizedException::forRoles(['admin'.$project->id]);
        }

        return $project;
    }

    /**
     * Get content list
     *
     * @param int $project_id
     * @param int $collection_id
     * @param \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function index($project_id, $collection_id, Request $request){
        $project = Project::with('collections')->findOrFail($project_id);

        $user = auth()->user();
        if(!$user->isSuperAdmin() && !$user->hasRole('admin'.$project->id) && !$user->hasRole('editor'.$project->id)){
            throw UnauthorizedException::forRoles(['admin'.$project->id]);
        }

        $collection = Collection::with(['fields'])->where('project_id', $project_id)->where('id', $collection_id)->firstOrFail();

        foreach ($collection->fields as $field) {
            $field->validations = json_decode($field->validations);
            $field->options = json_decode($field->options);
        }

        $data['collection'] = $collection;

        $content_items = Content::with(['meta', 'form'])->where('collection_id', $collection_id);

        if($request->get('search') != ''){
            $q = $request->get('search');
            $meta =  ContentMeta::where('value', 'LIKE', "%$q%")->get(['content_id']);

            $content_items = $content_items->whereIn('id', $meta);
        }

        $orderBy = $request->has('orderBy') ? $request->get('orderBy') : 'created_at';
        $criteria = $request->has('cr') ? $request->get('cr') : 'ASC';
        $each = $request->has('each') ? $request->get('each') : 15;

        if($request->get('sbm')){
            $content_items = $content_items->orderBy(
                ContentMeta::select('value')
                    ->whereColumn('content_meta.content_id', 'content.id')
                    ->where('field_name', $orderBy)
                    ->latest()
                    ->take(1),
                    $criteria
            );
        } else {
            if($orderBy == 'created_by' || $orderBy == 'updated_by' || $orderBy == 'published_by'){
                $content_items = $content_items->orderBy(
                    User::select('email')
                        ->whereColumn('users.id', 'content.'.$orderBy)
                        ->latest()
                        ->take(1),
                        $criteria
                );
            } else {
                $content_items = $content_items->orderBy($orderBy, $criteria);
            }
        }


        $count1 = clone $content_items;
        $count2 = clone $content_items;
        $count3 = clone $content_items;
        $count4 = clone $content_items;

        if($request->get('getItems') == 'all'){
            $content_items = $content_items->paginate($each);
        } elseif($request->get('getItems') == 'published'){
            $content_items = $content_items->whereNotNull('published_at')->paginate($each);
        } elseif($request->get('getItems') == 'draft'){
            $content_items = $content_items->whereNull('published_at')->paginate($each);
        } elseif($request->get('getItems') == 'trashed'){
            $content_items = $content_items->with(['meta' => function($q){ $q->withTrashed(); }])->onlyTrashed()->paginate($each);
        }

        foreach ($content_items as $content) {
            $content->created_by = User::find($content->created_by);
            $content->updated_by = User::find($content->updated_by);
            $content->published_by = User::find($content->published_by);
        }

        $data['content'] = $content_items;

        $totalCount = $count1->count();
        $published = $count2->whereNotNull('published_at')->count();
        $draft = $count3->whereNull('published_at')->count();
        $trashed = $count4->onlyTrashed()->count();

        $data['totalCount'] = $totalCount;
        $data['published'] = $published;
        $data['draft'] = $draft;
        $data['trashed'] = $trashed;

        $data['project'] = $project;
        $data['forms'] = \App\Models\Form::where('project_id', $project->id)->where('collection_id', $collection_id)->count();

        return $data;
    }

    /**
     * Get project and collection for new content
     *
     * @param int $project_id
     * @param int $collection_id
     * @return \App\Models\Project
     * @return \App\Models\Collection
     */
    public function new($project_id, $collection_id){
        $project = Project::with('collections')->findOrFail($project_id);

        $project->s3 = false;
        //Check if AWS S3 has been configured
        if(config('filesystems.disks.s3.key') && config('filesystems.disks.s3.secret') && config('filesystems.disks.s3.region') && config('filesystems.disks.s3.bucket')){
            $project->s3 = true;
        }

        $user = auth()->user();
        if(!$user->isSuperAdmin() && !$user->hasRole('admin'.$project->id) && !$user->hasRole('editor'.$project->id)){
            throw UnauthorizedException::forRoles(['admin'.$project->id]);
        }

        $collection = Collection::with(['fields'])->where('project_id', $project->id)->where('id', $collection_id)->firstOrFail();

        $data['project'] = $project;
        $data['collection'] = $collection;

        return $data;
    }

    /**
     * Store a new content
     *
     * @param int $project_id
     * @param int $collection_id
     * @param \Illuminate\Http\Request  $request
     * @return \App\Models\Content
     */
    public function store($project_id, $collection_id, Request $request){
        $project = Project::findOrFail($project_id);

        $user = auth()->user();
        if(!$user->isSuperAdmin() && !$user->hasRole('admin'.$project->id) && !$user->hasRole('editor'.$project->id)){
            throw UnauthorizedException::forRoles(['admin'.$project->id]);
        }

        $collection = Collection::with(['fields'])->where('project_id', $project->id)->where('id', $collection_id)->firstOrFail();

        $rules = [];
        $messages = [];

        //Validations
        foreach ($collection->fields as $field) {
            $validations = json_decode($field->validations);
            $options = json_decode($field->options);

            if($validations->required->status){
                if(isset($options->repeatable) && $options->repeatable) {
                    $repeatableField = $request->get('data')[$field->name];
                    foreach($repeatableField as $rf_key => $rf_value){
                        $rules['data.'.$field->name.'.*.value'][] = 'required';
                        $messages['data.'.$field->name.'.*.value'.'.required'] = 'The '.$field->name.' field is required.';

                        if($validations->required->message != null){
                            $messages['data.'.$field->name.'.*.value'.'.required'] = $validations->required->message;
                        }
                    }
                } else {
                    $rules['data.'.$field->name][] = 'required';
                    $messages['data.'.$field->name.'.required'] = 'The '.$field->name.' field is required.';

                    if($validations->required->message != null){
                        $messages['data.'.$field->name.'.required'] = $validations->required->message;
                    }
                }
            }

            if($field->type == "email"){
                if(isset($options->repeatable) && $options->repeatable) {
                    $repeatableField = $request->get('data')[$field->name];
                    foreach($repeatableField as $rf_key => $rf_value){
                        $rules['data.'.$field->name.'.*.value'][] = 'nullable';
                        $rules['data.'.$field->name.'.*.value'][] = 'email';
                        $messages['data.'.$field->name.'.*.value'.'.email'] = 'The '.$field->name.' must be a valid email address.';
                    }
                } else {
                    $rules['data.'.$field->name][] = 'email';
                    $messages['data.'.$field->name.'.email'] = 'The '.$field->name.' must be a valid email address.';
                }
            }
            if($field->type == "number"){
                if(isset($options->repeatable) && $options->repeatable) {
                    $repeatableField = $request->get('data')[$field->name];
                    foreach($repeatableField as $rf_key => $rf_value){
                        $rules['data.'.$field->name.'.*.value'][] = 'nullable';
                        $rules['data.'.$field->name.'.*.value'][] = 'numeric';
                        $messages['data.'.$field->name.'.*.value'.'.numeric'] = 'The '.$field->name.' must be numeric.';
                    }
                } else {
                    $rules['data.'.$field->name][] = 'numeric';
                    $messages['data.'.$field->name.'.numeric'] = 'The '.$field->name.' must be numeric.';
                }
            }
            if ($field->type == 'color') {
                if(isset($options->repeatable) && $options->repeatable) {
                    $repeatableField = $request->get('data')[$field->name];
                    foreach($repeatableField as $rf_key => $rf_value){
                        $rules['data.'.$field->name.'.*.value'][] = 'nullable';
                        $rules['data.'.$field->name.'.*.value'][] = 'color';
                        $messages['data.'.$field->name.'.*.value'.'.color'] = 'The '.$field->name.' must be a color string.';
                    }
                } else {
                    $rules['data.'.$field->name][] = 'color';
                    $messages['data.'.$field->name.'.color'] = 'The '.$field->name.' must be a color string.';
                }
            }

            if($validations->charcount->status){
                if($validations->charcount->type == "Between"){
                    if(isset($options->repeatable) && $options->repeatable) {
                        $repeatableField = $request->get('data')[$field->name];
                        foreach($repeatableField as $rf_key => $rf_value){
                            $rules['data.'.$field->name.'.*.value'][] = 'between:'.$validations->charcount->min.','.$validations->charcount->max;
                            $messages['data.'.$field->name.'.*.value'.'.between'] = 'The '.$field->name.' must be between '.$validations->charcount->min.' and '.$validations->charcount->max;

                            if($field->type != 'number'){
                                $messages['data.'.$field->name.'.*.value'.'.between'] .= ' characters.';
                            }
                        }
                    } else {
                        $rules['data.'.$field->name][] = 'between:'.$validations->charcount->min.','.$validations->charcount->max;
                        $messages['data.'.$field->name.'.between'] = 'The '.$field->name.' must be between '.$validations->charcount->min.' and '.$validations->charcount->max;

                        if($field->type != 'number'){
                            $messages['data.'.$field->name.'.between'] .= ' characters.';
                        }
                    }
                } elseif($validations->charcount->type == "Min") {
                    if(isset($options->repeatable) && $options->repeatable) {
                        $repeatableField = $request->get('data')[$field->name];
                        foreach($repeatableField as $rf_key => $rf_value){
                            $rules['data.'.$field->name.'.*.value'][] = 'min:'.$validations->charcount->min;
                            $messages['data.'.$field->name.'.*.value'.'.min'] = 'The '.$field->name.' must be at least '.$validations->charcount->min;

                            if($field->type != 'number'){
                                $messages['data.'.$field->name.'.*.value'.'.min'] .= ' characters.';
                            }
                        }
                    } else {
                        $rules['data.'.$field->name][] = 'min:'.$validations->charcount->min;
                        $messages['data.'.$field->name.'.min'] = 'The '.$field->name.' must be at least '.$validations->charcount->min;

                        if($field->type != 'number'){
                            $messages['data.'.$field->name.'.min'] .= ' characters.';
                        }
                    }
                } elseif($validations->charcount->type == "Max") {
                    if(isset($options->repeatable) && $options->repeatable) {
                        $repeatableField = $request->get('data')[$field->name];
                        foreach($repeatableField as $rf_key => $rf_value){
                            $rules['data.'.$field->name.'.*.value'][] = 'max:'.$validations->charcount->max;
                            $messages['data.'.$field->name.'.*.value'.'.max'] = 'The '.$field->name.' may not be greater than '.$validations->charcount->max;

                            if($field->type != 'number'){
                                $messages['data.'.$field->name.'.*.value'.'.max'] .= ' characters.';
                            }
                        }
                    } else {
                        $rules['data.'.$field->name][] = 'max:'.$validations->charcount->max;
                        $messages['data.'.$field->name.'.max'] = 'The '.$field->name.' may not be greater than '.$validations->charcount->max;

                        if($field->type != 'number'){
                            $messages['data.'.$field->name.'.max'] .= ' characters.';
                        }
                    }
                }
            }
        }

        Validator::extend('color', function ($attribute, $value, $parameters, $validator) {
            $color_regex = "/(#(?:[0-9a-f]{2}){2,4}$|(#[0-9a-f]{3}$)|(rgb|hsl)a?\((-?\d*\.?\d*+%?[,\s]+){2,3}\s*[\d\.]+%?\)$|black$|silver$|gray$|whitesmoke$|maroon$|red$|purple$|fuchsia$|green$|lime$|olivedrab$|yellow$|navy$|blue$|teal$|aquamarine$|orange$|aliceblue$|antiquewhite$|aqua$|azure$|beige$|bisque$|blanchedalmond$|blueviolet$|brown$|burlywood$|cadetblue$|chartreuse$|chocolate$|coral$|cornflowerblue$|cornsilk$|crimson$|currentcolor$|darkblue$|darkcyan$|darkgoldenrod$|darkgray$|darkgreen$|darkgrey$|darkkhaki$|darkmagenta$|darkolivegreen$|darkorange$|darkorchid$|darkred$|darksalmon$|darkseagreen$|darkslateblue$|darkslategray$|darkslategrey$|darkturquoise$|darkviolet$|deeppink$|deepskyblue$|dimgray$|dimgrey$|dodgerblue$|firebrick$|floralwhite$|forestgreen$|gainsboro$|ghostwhite$|goldenrod$|gold$|greenyellow$|grey$|honeydew$|hotpink$|indianred$|indigo$|ivory$|khaki$|lavenderblush$|lavender$|lawngreen$|lemonchiffon$|lightblue$|lightcoral$|lightcyan$|lightgoldenrodyellow$|lightgray$|lightgreen$|lightgrey$|lightpink$|lightsalmon$|lightseagreen$|lightskyblue$|lightslategray$|lightslategrey$|lightsteelblue$|lightyellow$|limegreen$|linen$|mediumaquamarine$|mediumblue$|mediumorchid$|mediumpurple$|mediumseagreen$|mediumslateblue$|mediumspringgreen$|mediumturquoise$|mediumvioletred$|midnightblue$|mintcream$|mistyrose$|moccasin$|navajowhite$|oldlace$|olive$|orangered$|orchid$|palegoldenrod$|palegreen$|paleturquoise$|palevioletred$|papayawhip$|peachpuff$|peru$|pink$|plum$|powderblue$|rosybrown$|royalblue$|saddlebrown$|salmon$|sandybrown$|seagreen$|seashell$|sienna$|skyblue$|slateblue$|slategray$|slategrey$|snow$|springgreen$|steelblue$|tan$|thistle$|tomato$|transparent$|turquoise$|violet$|wheat$|white$|yellowgreen$|rebeccapurple$)/i";

            return preg_match($color_regex, $value);
        });

        $validator = Validator::make($request->all(), $rules, $messages);
        $validator->validate();

        $uniqueErrors = [];

        //Unique validation
        foreach ($collection->fields as $field) {
            $validations = json_decode($field->validations);
            if($validations->unique->status){
                if(isset($request->get('data')[$field->name])){
                    $data = ContentMeta::where('collection_id', $collection->id)->where('field_name', $field->name)->where('value', $request->get('data')[$field->name])->count();

                    if($data !== 0){
                        $uniqueErrors['errors']['data.'.$field->name] = ['The '.$field->name.' has already been taken.'];

                        if($validations->unique->message != null){
                            $uniqueErrors['errors']['data.'.$field->name] = [$validations->unique->message];
                        }
                    }
                }
            }
        }
        if(count($uniqueErrors) !== 0){
            return response($uniqueErrors, 422);
        }

        $content = Content::create([
            'project_id' => $project->id,
            'collection_id' => $collection->id,
            'locale' => $request->get('locale'),
            'created_by' => auth()->user()->id,
            'published_at' => $request->get('published') ? now() : null,
            'published_by' => $request->get('published') ? auth()->user()->id : null
        ]);

        $content_data = $request->get('data');

        foreach ($content_data as $key => $value) {
            $val = $value;

            foreach ($collection->fields as $field) {
                if($field->name == $key){
                    $field_type = $field->type;
                    $field_options = json_decode($field->options);
                }
            }

            if(!empty($value)){
                if($field_type == 'password'){
                    $val = Hash::make($value);
                }
                if ($field_type == 'enumeration') {
                    if (isset($field_options->multiple) && $field_options->multiple && is_array($value)) {
                        $str = '';
                        foreach ($value as $vv) {
                            $str .= $vv.',';
                        }
                        $val = rtrim($str, ',');
                    } else {
                        $val = $value;
                    }
                }
                if($field_type == 'media'){
                    $str = '';
                    foreach ($value as $file) {
                        $str .= $file.',';
                    }
                    $val = rtrim($str, ',');
                }
                if($field_type == 'relation'){
                    $str = '';
                    foreach ($value as $relation) {
                        $str .= $relation.',';
                    }
                    $val = rtrim($str, ',');
                }
                if($field_type == 'json'){
                    $val = json_encode($value);
                }

                if(isset($field_options->repeatable) && $field_options->repeatable){
                    foreach($value as $rf_item){
                        if(!empty($rf_item['value'])){
                            $content_meta = ContentMeta::create([
                                'project_id' => $project->id,
                                'collection_id' => $collection->id,
                                'content_id' => $content->id,
                                'field_name' => $key,
                                'value' => $rf_item['value']
                            ]);
                        }
                    }
                } else {
                    $content_meta = ContentMeta::create([
                        'project_id' => $project->id,
                        'collection_id' => $collection->id,
                        'content_id' => $content->id,
                        'field_name' => $key,
                        'value' => $val
                    ]);
                }
            }
        }

        \App\Events\ContentCreated::dispatch([
            'source' => 'User',
            'content' => $content
        ]);

        return response($content, 200);
    }

    /**
     * Get content by id for editing
     *
     * @param int $project_id
     * @param int $collection_id
     * @param int $content_id
     * @return mixed
     */
    public function edit($project_id, $collection_id, $content_id){
        $project = Project::with('collections')->findOrFail($project_id);

        $user = auth()->user();
        if(!$user->isSuperAdmin() && !$user->hasRole('admin'.$project->id) && !$user->hasRole('editor'.$project->id)){
            throw UnauthorizedException::forRoles(['admin'.$project->id]);
        }

        $collection = Collection::with(['fields'])->where('project_id', $project->id)->where('id', $collection_id)->firstOrFail();
        $content = Content::with('meta')->where('project_id', $project->id)->where('collection_id', $collection->id)->where('id', $content_id)->firstOrFail();

        $data['project'] = $project;
        $data['collection'] = $collection;
        $data['content'] = $content;

        return $data;
    }

    /**
     * Update content
     *
     * @param int $project_id
     * @param int $collection_id
     * @param int $content_id
     * @param \Illuminate\Http\Request  $request
     * @return void
     */
    public function update($project_id, $collection_id, $content_id, Request $request){
        $project = Project::findOrFail($project_id);

        $user = auth()->user();
        if(!$user->isSuperAdmin() && !$user->hasRole('admin'.$project->id) && !$user->hasRole('editor'.$project->id)){
            throw UnauthorizedException::forRoles(['admin'.$project->id]);
        }

        $collection = Collection::with(['fields'])->where('project_id', $project->id)->where('id', $collection_id)->firstOrFail();
        $content = Content::with('meta')->where('project_id', $project->id)->where('collection_id', $collection->id)->where('id', $content_id)->firstOrFail();

        $rules = [];
        $messages = [];

        //Validations
        foreach ($collection->fields as $field) {
            $validations = json_decode($field->validations);
            $options = json_decode($field->options);

            if($validations->required->status){
                if(isset($options->repeatable) && $options->repeatable) {
                    $repeatableField = $request->get('data')[$field->name];
                    foreach($repeatableField as $rf_key => $rf_value){
                        $rules['data.'.$field->name.'.*.value'][] = 'required';
                        $messages['data.'.$field->name.'.*.value'.'.required'] = 'The '.$field->name.' field is required.';

                        if($validations->required->message != null){
                            $messages['data.'.$field->name.'.*.value'.'.required'] = $validations->required->message;
                        }
                    }
                } else {
                    $rules['data.'.$field->name][] = 'required';
                    $messages['data.'.$field->name.'.required'] = 'The '.$field->name.' field is required.';

                    if($validations->required->message != null){
                        $messages['data.'.$field->name.'.required'] = $validations->required->message;
                    }
                }
            }

            if($field->type == "email"){
                if(isset($options->repeatable) && $options->repeatable) {
                    $repeatableField = $request->get('data')[$field->name];
                    foreach($repeatableField as $rf_key => $rf_value){
                        $rules['data.'.$field->name.'.*.value'][] = 'nullable';
                        $rules['data.'.$field->name.'.*.value'][] = 'email';
                        $messages['data.'.$field->name.'.*.value'.'.email'] = 'The '.$field->name.' must be a valid email address.';
                    }
                } else {
                    $rules['data.'.$field->name][] = 'email';
                    $messages['data.'.$field->name.'.email'] = 'The '.$field->name.' must be a valid email address.';
                }
            }
            if($field->type == "number"){
                if(isset($options->repeatable) && $options->repeatable) {
                    $repeatableField = $request->get('data')[$field->name];
                    foreach($repeatableField as $rf_key => $rf_value){
                        $rules['data.'.$field->name.'.*.value'][] = 'nullable';
                        $rules['data.'.$field->name.'.*.value'][] = 'numeric';
                        $messages['data.'.$field->name.'.*.value'.'.numeric'] = 'The '.$field->name.' must be numeric.';
                    }
                } else {
                    $rules['data.'.$field->name][] = 'numeric';
                    $messages['data.'.$field->name.'.numeric'] = 'The '.$field->name.' must be numeric.';
                }
            }
            if ($field->type == 'color') {
                if(isset($options->repeatable) && $options->repeatable) {
                    $repeatableField = $request->get('data')[$field->name];
                    foreach($repeatableField as $rf_key => $rf_value){
                        $rules['data.'.$field->name.'.*.value'][] = 'nullable';
                        $rules['data.'.$field->name.'.*.value'][] = 'color';
                        $messages['data.'.$field->name.'.*.value'.'.color'] = 'The '.$field->name.' must be a color string.';
                    }
                } else {
                    $rules['data.'.$field->name][] = 'color';
                    $messages['data.'.$field->name.'.color'] = 'The '.$field->name.' must be a color string.';
                }
            }

            if($validations->charcount->status){
                if($validations->charcount->type == "Between"){
                    if(isset($options->repeatable) && $options->repeatable) {
                        $repeatableField = $request->get('data')[$field->name];
                        foreach($repeatableField as $rf_key => $rf_value){
                            $rules['data.'.$field->name.'.*.value'][] = 'between:'.$validations->charcount->min.','.$validations->charcount->max;
                            $messages['data.'.$field->name.'.*.value'.'.between'] = 'The '.$field->name.' must be between '.$validations->charcount->min.' and '.$validations->charcount->max;

                            if($field->type != 'number'){
                                $messages['data.'.$field->name.'.*.value'.'.between'] .= ' characters.';
                            }
                        }
                    } else {
                        $rules['data.'.$field->name][] = 'between:'.$validations->charcount->min.','.$validations->charcount->max;
                        $messages['data.'.$field->name.'.between'] = 'The '.$field->name.' must be between '.$validations->charcount->min.' and '.$validations->charcount->max;

                        if($field->type != 'number'){
                            $messages['data.'.$field->name.'.between'] .= ' characters.';
                        }
                    }
                } elseif($validations->charcount->type == "Min") {
                    if(isset($options->repeatable) && $options->repeatable) {
                        $repeatableField = $request->get('data')[$field->name];
                        foreach($repeatableField as $rf_key => $rf_value){
                            $rules['data.'.$field->name.'.*.value'][] = 'min:'.$validations->charcount->min;
                            $messages['data.'.$field->name.'.*.value'.'.min'] = 'The '.$field->name.' must be at least '.$validations->charcount->min;

                            if($field->type != 'number'){
                                $messages['data.'.$field->name.'.*.value'.'.min'] .= ' characters.';
                            }
                        }
                    } else {
                        $rules['data.'.$field->name][] = 'min:'.$validations->charcount->min;
                        $messages['data.'.$field->name.'.min'] = 'The '.$field->name.' must be at least '.$validations->charcount->min;

                        if($field->type != 'number'){
                            $messages['data.'.$field->name.'.min'] .= ' characters.';
                        }
                    }
                } elseif($validations->charcount->type == "Max") {
                    if(isset($options->repeatable) && $options->repeatable) {
                        $repeatableField = $request->get('data')[$field->name];
                        foreach($repeatableField as $rf_key => $rf_value){
                            $rules['data.'.$field->name.'.*.value'][] = 'max:'.$validations->charcount->max;
                            $messages['data.'.$field->name.'.*.value'.'.max'] = 'The '.$field->name.' may not be greater than '.$validations->charcount->max;

                            if($field->type != 'number'){
                                $messages['data.'.$field->name.'.*.value'.'.max'] .= ' characters.';
                            }
                        }
                    } else {
                        $rules['data.'.$field->name][] = 'max:'.$validations->charcount->max;
                        $messages['data.'.$field->name.'.max'] = 'The '.$field->name.' may not be greater than '.$validations->charcount->max;

                        if($field->type != 'number'){
                            $messages['data.'.$field->name.'.max'] .= ' characters.';
                        }
                    }
                }
            }
        }

        Validator::extend('color', function ($attribute, $value, $parameters, $validator) {
            $color_regex = "/(#(?:[0-9a-f]{2}){2,4}$|(#[0-9a-f]{3}$)|(rgb|hsl)a?\((-?\d*\.?\d*+%?[,\s]+){2,3}\s*[\d\.]+%?\)$|black$|silver$|gray$|whitesmoke$|maroon$|red$|purple$|fuchsia$|green$|lime$|olivedrab$|yellow$|navy$|blue$|teal$|aquamarine$|orange$|aliceblue$|antiquewhite$|aqua$|azure$|beige$|bisque$|blanchedalmond$|blueviolet$|brown$|burlywood$|cadetblue$|chartreuse$|chocolate$|coral$|cornflowerblue$|cornsilk$|crimson$|currentcolor$|darkblue$|darkcyan$|darkgoldenrod$|darkgray$|darkgreen$|darkgrey$|darkkhaki$|darkmagenta$|darkolivegreen$|darkorange$|darkorchid$|darkred$|darksalmon$|darkseagreen$|darkslateblue$|darkslategray$|darkslategrey$|darkturquoise$|darkviolet$|deeppink$|deepskyblue$|dimgray$|dimgrey$|dodgerblue$|firebrick$|floralwhite$|forestgreen$|gainsboro$|ghostwhite$|goldenrod$|gold$|greenyellow$|grey$|honeydew$|hotpink$|indianred$|indigo$|ivory$|khaki$|lavenderblush$|lavender$|lawngreen$|lemonchiffon$|lightblue$|lightcoral$|lightcyan$|lightgoldenrodyellow$|lightgray$|lightgreen$|lightgrey$|lightpink$|lightsalmon$|lightseagreen$|lightskyblue$|lightslategray$|lightslategrey$|lightsteelblue$|lightyellow$|limegreen$|linen$|mediumaquamarine$|mediumblue$|mediumorchid$|mediumpurple$|mediumseagreen$|mediumslateblue$|mediumspringgreen$|mediumturquoise$|mediumvioletred$|midnightblue$|mintcream$|mistyrose$|moccasin$|navajowhite$|oldlace$|olive$|orangered$|orchid$|palegoldenrod$|palegreen$|paleturquoise$|palevioletred$|papayawhip$|peachpuff$|peru$|pink$|plum$|powderblue$|rosybrown$|royalblue$|saddlebrown$|salmon$|sandybrown$|seagreen$|seashell$|sienna$|skyblue$|slateblue$|slategray$|slategrey$|snow$|springgreen$|steelblue$|tan$|thistle$|tomato$|transparent$|turquoise$|violet$|wheat$|white$|yellowgreen$|rebeccapurple$)/i";

            return preg_match($color_regex, $value);
        });

        $validator = Validator::make($request->all(), $rules, $messages);
        $validator->validate();

        $uniqueErrors = [];

        //Unique validation
        foreach ($collection->fields as $field) {
            $validations = json_decode($field->validations);
            if($validations->unique->status){
                if(isset($request->get('data')[$field->name])){
                    $data = ContentMeta::where('content_id', '!=', $content->id)->where('collection_id', $collection->id)->where('field_name', $field->name)->where('value', $request->get('data')[$field->name])->count();

                    if($data !== 0){
                        $uniqueErrors['errors']['data.'.$field->name] = ['The '.$field->name.' has already been taken.'];

                        if($validations->unique->message != null){
                            $uniqueErrors['errors']['data.'.$field->name] = [$validations->unique->message];
                        }
                    }
                }
            }
        }
        if(count($uniqueErrors) !== 0){
            return response($uniqueErrors, 422);
        }

        if ($content->published_at === null && $request->get('published')) {
            \App\Events\ContentPublished::dispatch([
                'source' => 'User',
                'content' => $content
            ]);
        }

        $content->update([
            'locale' => $request->get('locale'),
            'updated_by' => auth()->user()->id,
            'published_at' => $request->get('published') ? now() : null,
            'published_by' => $request->get('published') ? auth()->user()->id : null
        ]);

        $content_data = $request->get('data');

        foreach ($content_data as $key => $value) {
            $val = $value;

            foreach ($collection->fields as $field) {
                if($field->name == $key){
                    $field_type = $field->type;
                    $field_options = json_decode($field->options);
                }
            }

            if($field_type == 'password'){
                $password = ContentMeta::where('content_id', $content->id)->where('field_name', $key)->first();

                if(!$password){
                    $val = Hash::make($value);
                } else {
                    if(empty($value)){
                        $val = $password->value;
                    } else {
                        $val = Hash::make($value);
                    }
                }
            }
            if ($field_type == 'enumeration') {
                if (isset($field_options->multiple) && $field_options->multiple && is_array($value)) {
                    $str = '';
                    foreach ($value as $vv) {
                        $str .= $vv.',';
                    }
                    $val = rtrim($str, ',');
                } else {
                    $val = $value;
                }
            }
            if($field_type == 'media'){
                $str = '';
                foreach ($value as $file) {
                    $str .= $file.',';
                }
                $val = rtrim($str, ',');
            }
            if($field_type == 'relation'){
                $str = '';
                foreach ($value as $relation) {
                    $str .= $relation.',';
                }
                $val = rtrim($str, ',');
            }
            if($field_type == 'json'){
                $val = json_encode($value);
            }

            if(isset($field_options->repeatable) && $field_options->repeatable){
                $deleteContentMeta = ContentMeta::where('content_id', $content->id)->whereIn('id', $request->get('deleted'))->forceDelete();

                foreach($value as $rf_item){
                    if(!empty($rf_item['value'])){
                        $content_meta = ContentMeta::where('id', $rf_item['id'])->where('field_name', $key)->first();

                        if($content_meta){
                            if(isset($rf_item['deleted']) && $rf_item['deleted']){
                                $content_meta->forceDelete();
                            } else {
                                $content_meta->update([
                                    'value' => $rf_item['value']
                                ]);
                            }
                        } else {
                            $content_meta = ContentMeta::create([
                                'project_id' => $content->project_id,
                                'collection_id' => $content->collection_id,
                                'content_id' => $content->id,
                                'field_name' => $key,
                                'value' => $rf_item['value']
                            ]);
                        }
                    } else {
                        //If value is empty, delete the content meta
                        $content_meta = ContentMeta::where('id', $rf_item['id'])->where('field_name', $key)->first();
                        if($content_meta){
                            $content_meta->forceDelete();
                        }
                    }
                }
            } else {
                $content_meta = ContentMeta::where('content_id', $content->id)->where('field_name', $key)->first();

                if($content_meta){
                    $content_meta->update([
                        'value' => $val
                    ]);
                } else {
                    if(!empty($value)){
                        $content_meta = ContentMeta::create([
                            'project_id' => $content->project_id,
                            'collection_id' => $content->collection_id,
                            'content_id' => $content->id,
                            'field_name' => $key,
                            'value' => $val
                        ]);
                    }
                }
            }
        }

        \App\Events\ContentUpdated::dispatch([
            'source' => 'User',
            'content' => $content
        ]);
    }

    /**
     * Unpublish a content
     *
     * @param int $project_id
     * @param int $collection_id
     * @param int $content_id
     * @return \Illuminate\Http\Response
     */
    public function unpublish($project_id, $collection_id, $content_id){
        $project = Project::findOrFail($project_id);

        $user = auth()->user();
        if(!$user->isSuperAdmin() && !$user->hasRole('admin'.$project->id) && !$user->hasRole('editor'.$project->id)){
            throw UnauthorizedException::forRoles(['admin'.$project->id]);
        }

        $content = Content::where('project_id', $project->id)->where('collection_id', $collection_id)->where('id', $content_id)->firstOrFail();

        $content->published_at = null;
        $content->published_by = null;

        $content->save();

        \App\Events\ContentUnpublished::dispatch([
            'source' => 'User',
            'content' => $content
        ]);

        return response([], 200);
    }

    /**
     * Move content to the trash (softdelete)
     *
     * @param int $project_id
     * @param int $collection_id
     * @param int $content_id
     * @return \Illuminate\Http\Response
     */
    public function moveToTrash($project_id, $collection_id, $content_id){
        $project = Project::findOrFail($project_id);

        $user = auth()->user();
        if(!$user->isSuperAdmin() && !$user->hasRole('admin'.$project->id) && !$user->hasRole('editor'.$project->id)){
            throw UnauthorizedException::forRoles(['admin'.$project->id]);
        }

        $content = Content::where('project_id', $project->id)->where('collection_id', $collection_id)->where('id', $content_id)->firstOrFail();

        $content->meta()->delete();

        if($content->delete()){
            \App\Events\ContentTrashed::dispatch([
                'source' => 'User',
                'content' => $content
            ]);

            return response([], 200);
        } else {
            return response([], 404);
        }
    }

    /**
     * Delete content
     *
     * @param int $project_id
     * @param int $collection_id
     * @param int $content_id
     * @return \Illuminate\Http\Response
     */
    public function delete($project_id, $collection_id, $content_id){
        $project = Project::findOrFail($project_id);

        $user = auth()->user();
        if(!$user->isSuperAdmin() && !$user->hasRole('admin'.$project->id) && !$user->hasRole('editor'.$project->id)){
            throw UnauthorizedException::forRoles(['admin'.$project->id]);
        }

        $content = Content::where('project_id', $project->id)->where('collection_id', $collection_id)->where('id', $content_id)->firstOrFail();

        $content->meta()->forceDelete();

        if($content->forceDelete()){
            \App\Events\ContentDeleted::dispatch([
                'source' => 'User',
                'content' => [
                    'project_id' => $project->id,
                    'collection_id' => $collection_id,
                    'item_id' => $content_id,
                ]
            ]);

            return response([], 200);
        } else {
            return response([], 404);
        }
    }

    /**
     * Get multiple content by id
     *
     * @param int $project_id
     * @param \Illuminate\Http\Request $request
     * @return \App\Models\Collection
     * @return \App\Models\Content
     */
    public function getSelectedRecords($project_id, Request $request){
        $project = Project::findOrFail($project_id);

        $user = auth()->user();
        if(!$user->isSuperAdmin() && !$user->hasRole('admin'.$project->id) && !$user->hasRole('editor'.$project->id)){
            throw UnauthorizedException::forRoles(['admin'.$project->id]);
        }

        $selected = $request->get('data')['selected'];
        $collection_id = $request->get('data')['collection_id'];

        $data['collection'] = Collection::with('fields')->where('project_id', $project->id)->where('id', $collection_id)->first();
        $data['content'] = Content::with(['meta'])->where('project_id', $project->id)->whereIn('id', $selected)->get();

        return $data;
    }

    /**
     * Get multiple files by id
     *
     * @param int $project_id
     * @param \Illuminate\Http\Request $request
     * @return \App\Models\Media
     */
    public function getSelectedFiles($project_id, Request $request){
        $project = Project::findOrFail($project_id);

        $user = auth()->user();
        if(!$user->isSuperAdmin() && !$user->hasRole('admin'.$project->id) && !$user->hasRole('editor'.$project->id)){
            throw UnauthorizedException::forRoles(['admin'.$project->id]);
        }

        $media = Media::where('project_id', $project->id)->whereIn('id', $request->get('data'))->get();

        return $media;
    }

    /**
     * Publish multiple content
     *
     * @param int $project_id
     * @param int $collection_id
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function publishSelected($project_id, $collection_id, Request $request){
        $project = Project::findOrFail($project_id);

        $user = auth()->user();
        if(!$user->isSuperAdmin() && !$user->hasRole('admin'.$project->id) && !$user->hasRole('editor'.$project->id)){
            throw UnauthorizedException::forRoles(['admin'.$project->id]);
        }

        $ids = $request->get('selected');

        foreach($ids as $id){
            $content = Content::where('project_id', $project->id)->where('collection_id', $collection_id)->where('id', $id)->first();

            if($content && $content->published_at == null){
                $content->published_at = now();
                $content->published_by = auth()->user()->id;
                $content->save();

                \App\Events\ContentPublished::dispatch([
                    'source' => 'User',
                    'content' => $content
                ]);
            }
        }

        return response([], 200);
    }

    /**
     * Unpublish multiple content
     *
     * @param int $project_id
     * @param int $collection_id
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function unPublishSelected($project_id, $collection_id, Request $request){
        $project = Project::findOrFail($project_id);

        $user = auth()->user();
        if(!$user->isSuperAdmin() && !$user->hasRole('admin'.$project->id) && !$user->hasRole('editor'.$project->id)){
            throw UnauthorizedException::forRoles(['admin'.$project->id]);
        }

        $ids = $request->get('selected');

        foreach($ids as $id){
            $content = Content::where('project_id', $project->id)->where('collection_id', $collection_id)->where('id', $id)->first();

            if($content && $content->published_at != null){
                $content->published_at = null;
                $content->published_by = null;
                $content->save();

                \App\Events\ContentUnpublished::dispatch([
                    'source' => 'User',
                    'content' => $content
                ]);
            }
        }

        return response([], 200);
    }

    /**
     * Move multiple content to the trash
     *
     * @param int $project_id
     * @param int $collection_id
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function moveToTrashSelected($project_id, $collection_id, Request $request){
        $project = Project::findOrFail($project_id);

        $user = auth()->user();
        if(!$user->isSuperAdmin() && !$user->hasRole('admin'.$project->id) && !$user->hasRole('editor'.$project->id)){
            throw UnauthorizedException::forRoles(['admin'.$project->id]);
        }

        $ids = $request->get('selected');

        foreach($ids as $id){
            $content = Content::where('project_id', $project->id)->where('collection_id', $collection_id)->where('id', $id)->first();

            if($content){
                $content->meta()->delete();
                $content->delete();

                \App\Events\ContentTrashed::dispatch([
                    'source' => 'User',
                    'content' => $content
                ]);
            }
        }

        return response([], 200);
    }

    /**
     * Delete multiple content
     *
     * @param int $project_id
     * @param int $collection_id
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function deleteSelected($project_id, $collection_id, Request $request){
        $project = Project::findOrFail($project_id);

        $user = auth()->user();
        if(!$user->isSuperAdmin() && !$user->hasRole('admin'.$project->id) && !$user->hasRole('editor'.$project->id)){
            throw UnauthorizedException::forRoles(['admin'.$project->id]);
        }

        $ids = $request->get('selected');

        foreach($ids as $id){
            $content = Content::withTrashed()->where('project_id', $project->id)->where('collection_id', $collection_id)->where('id', $id)->first();

            if($content){
                $content->meta()->forceDelete();
                $content->forceDelete();

                \App\Events\ContentDeleted::dispatch([
                    'source' => 'User',
                    'content' => [
                        'project_id' => $project->id,
                        'collection_id' => $collection_id,
                        'item_id' => $id,
                    ]
                ]);
            }
        }

        return response([], 200);
    }

    /**
     * Restore multiple content
     *
     * @param int $project_id
     * @param int $collection_id
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function restoreSelected($project_id, $collection_id, Request $request){
        $project = Project::findOrFail($project_id);

        $user = auth()->user();
        if(!$user->isSuperAdmin() && !$user->hasRole('admin'.$project->id) && !$user->hasRole('editor'.$project->id)){
            throw UnauthorizedException::forRoles(['admin'.$project->id]);
        }

        $ids = $request->get('selected');

        foreach($ids as $id){
            $content = Content::onlyTrashed()->where('project_id', $project->id)->where('collection_id', $collection_id)->where('id', $id)->first();

            if($content){
                $content->meta()->restore();
                $content->restore();

                \App\Events\ContentRestored::dispatch([
                    'source' => 'User',
                    'content' => $content
                ]);
            }
        }

        return response([], 200);
    }
}
