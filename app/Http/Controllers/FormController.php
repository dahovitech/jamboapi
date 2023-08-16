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

use App\Models\Collection;
use App\Models\Content;
use App\Models\ContentMeta;
use App\Models\Form;
use App\Models\Media;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Image;
use Spatie\Permission\Exceptions\UnauthorizedException;

class FormController extends Controller
{
    /**
     * Get project and collection for forms
     *
     * @param  int  $project_id
     * @param  int  $collection_id
     * @return \App\Models\Project
     * @return \App\Models\Collection
     * @return \App\Models\Form
     */
    public function forms($project_id, $collection_id)
    {
        $project = Project::with('collections')->findOrFail($project_id);

        $user = auth()->user();
        if (! $user->isSuperAdmin() && ! $user->hasRole('admin'.$project->id) && ! $user->hasRole('editor'.$project->id)) {
            throw UnauthorizedException::forRoles(['admin'.$project->id]);
        }

        $collection = Collection::with(['fields'])->where('project_id', $project->id)->where('id', $collection_id)->firstOrFail();

        $data['project'] = $project;
        $data['collection'] = $collection;
        $data['forms'] = Form::where('project_id', $project->id)->where('collection_id', $collection->id)->get();

        return $data;
    }

    /**
     * Store a new form
     *
     * @param  int  $project_id
     * @param  int  $collection_id
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Models\Form
     */
    public function store($project_id, $collection_id, Request $request)
    {
        $project = Project::findOrFail($project_id);

        $user = auth()->user();
        if (! $user->isSuperAdmin() && ! $user->hasRole('admin'.$project->id) && ! $user->hasRole('editor'.$project->id)) {
            throw UnauthorizedException::forRoles(['admin'.$project->id]);
        }

        $collection = Collection::with(['fields'])->where('project_id', $project->id)->where('id', $collection_id)->firstOrFail();

        $request->validate([
            'name' => 'required|max:255',
        ]);

        $form = Form::create([
            'name' => $request->get('name'),
            'description' => $request->get('description'),
            'project_id' => $project->id,
            'collection_id' => $collection->id,
        ]);

        return response($form, 200);
    }

    public function save($project_id, $collection_id, $form_id, Request $request)
    {
        $project = Project::findOrFail($project_id);

        $user = auth()->user();
        if (! $user->isSuperAdmin() && ! $user->hasRole('admin'.$project->id) && ! $user->hasRole('editor'.$project->id)) {
            throw UnauthorizedException::forRoles(['admin'.$project->id]);
        }

        $form = Form::findOrFail($form_id);

        $form->name = $request->get('name');
        $form->description = $request->get('description');
        $form->submit_btn_text = $request->get('submit_btn_text');
        $form->fields = json_encode($request->get('fields'));
        $form->save();

        return $request->all();
    }

    public function delete($project_id, $collection_id, $form_id)
    {
        $project = Project::findOrFail($project_id);

        $user = auth()->user();
        if (! $user->isSuperAdmin() && ! $user->hasRole('admin'.$project->id) && ! $user->hasRole('editor'.$project->id)) {
            throw UnauthorizedException::forRoles(['admin'.$project->id]);
        }

        $form = Form::findOrFail($form_id);
        $form->delete();

        return response('', 200);
    }

    public function showPreview($uuid, Request $request)
    {
        $route = $request->route();

        $form = Form::where('uuid', $uuid)->firstOrFail();
        $embed_url = env('APP_URL').'/forms/'.$uuid;

        return view('forms.preview', compact(['form', 'embed_url']));
    }

    public function showEmbeded($uuid, Request $request)
    {
        $route = $request->route();

        $form = Form::where('uuid', $uuid)->firstOrFail();

        return view('forms.embeded', compact(['form']));
    }

    public function getEmbeded($uuid)
    {
        $data['form'] = Form::where('uuid', $uuid)->firstOrFail();

        $data['upload_max_filesize'] = \App\Jamboapi\JamboapiHelpers::getUploadMaxFileSize();

        return $data;
    }

    /**
     * Upload a file
     *
     * @param  int  $project_id
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Models\Media
     */
    public function upload($uuid, Request $request)
    {
        $form = Form::where('uuid', $uuid)->firstOrFail();

        $project = Project::findOrFail($form->project_id);

        $max_file_size = \App\Jamboapi\JamboapiHelpers::getUploadMaxFileSize();
        $supported_mimes = 'png,gif,jpeg,jpg,bmp';
        $request->validate([
            'file' => 'required|file|max:'.($max_file_size / 1024).'|mimes:'.$supported_mimes,
        ]);

        if ($request->has('file')) {
            $file = $request->file('file');

            $file_name = $this->renameFile($file->getClientOriginalName(), $project->uuid, $file, $project->disk);

            Storage::disk($project->disk)->putFileAs('public/'.$project->uuid, $request->file('file'), $file_name);

            $extension = $file->getClientOriginalExtension();

            $image_types = ['png', 'gif', 'jpeg', 'jpg', 'bmp'];
            if (in_array($extension, $image_types)) {
                $thumb = Image::make($file)->resize(null, 600, function ($constraint) {
                    $constraint->aspectRatio();
                })->encode($extension);

                Storage::disk($project->disk)->put('public/'.$project->uuid.'/thumbnails/'.$file_name, $thumb);
            }

            $image = getimagesize($file);

            $new_file = Media::create([
                'project_id' => $project->id,
                'name' => $file_name,
                'type' => $file->getClientOriginalExtension(),
                'size' => $file->getSize(),
                'width' => isset($image[0]) ? $image[0] : null,
                'height' => isset($image[1]) ? $image[1] : null,
                'disk' => $project->disk,
            ]);

            return response($new_file, 200);
        }
    }

    /**
     * Rename a file
     *
     * @param  string  $file_name
     * @param  uuid  $project_uuid
     * @param  file  $file
     * @param  string  $disk
     * @return string $file_name
     */
    private function renameFile($file_name, $project_uuid, $file, $disk)
    {
        $path = $project_uuid.'/'.$file_name;

        $i = 1;
        while (Storage::disk($disk)->exists($path)) {
            $name = explode('.', $file->getClientOriginalName());
            $file_name = $name[0].'('.$i.')'.'.'.$file->getClientOriginalExtension();
            $path = $project_uuid.'/'.$file_name;
            $i++;
        }

        return $file_name;
    }

    public function submit($uuid, Request $request)
    {
        $form = Form::where('uuid', $uuid)->firstOrFail();

        $project = Project::findOrFail($form->project_id);
        $collection = Collection::with(['fields'])->where('project_id', $project->id)->where('id', $form->collection_id)->first();

        $rules = [];
        $messages = [];

        $form_fields = json_decode($form->fields);

        //Validations
        foreach ($form_fields as $field) {
            $validations = $field->validations;
            $options = $field->options;

            if ($validations->required->status) {
                if ($field->type !== 'slug') {
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

        //Unique validations
        foreach ($form_fields as $field) {
            $validations = $field->validations;
            if ($validations->unique->status) {
                if (isset($request->get('data')[$field->name])) {
                    $data = ContentMeta::where('collection_id', $collection->id)->where('field_name', $field->name)->where('value', $request->get('data')[$field->name])->count();

                    if ($data !== 0) {
                        $uniqueErrors['errors']['data.'.$field->name] = ['The '.$field->name.' has already been taken.'];

                        if ($validations->unique->message != null) {
                            $uniqueErrors['errors']['data.'.$field->name] = [$validations->unique->message];
                        }
                    }
                }
            }
        }
        if (count($uniqueErrors) !== 0) {
            return response($uniqueErrors, 422);
        }

        $content_create_data = [
            'project_id' => $project->id,
            'collection_id' => $collection->id,
            'locale' => $project->default_locale,
            'form_id' => $form->id,
        ];

        $content = Content::create($content_create_data);

        $content_data = $request->get('data');

        foreach ($collection->fields as $field) {
            if ($field->type == 'slug') {
                $field_options = json_decode($field->options);
                if ($request->has('data.'.$field_options->slug->field)) {
                    $slug = Str::slug($request->get('data')[$field_options->slug->field]);

                    $content_meta = ContentMeta::create([
                        'project_id' => $project->id,
                        'collection_id' => $collection->id,
                        'content_id' => $content->id,
                        'field_name' => $field->name,
                        'value' => $slug,
                    ]);
                }
            }
        }

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

        \App\Events\FormSubmitted::dispatch([
            'source' => 'User',
            'content' => $content
        ]);

        return response([], 200);
    }
}
