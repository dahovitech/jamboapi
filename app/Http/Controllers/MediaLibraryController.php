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

use Image;
use App\Models\Media;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaLibraryController extends Controller
{

    /**
     * Get project files
     *
     * @param int $project_id
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function getFiles($project_id, Request $request){
        $project = Project::with('collections')->findOrFail($project_id);

        $user = auth()->user();
        if(!$user->isSuperAdmin() && !$user->hasRole('admin'.$project->id) && !$user->hasRole('editor'.$project->id)){
            throw UnauthorizedException::forRoles(['admin'.$project->id]);
        }

        $data['project'] = $project;

        $data['media'] = Media::where('project_id', $project->id)->when($request->get('search'), function($q)use($request){
            $searchItem = $request->get('search');
            return $q->where('name', 'LIKE', "%$searchItem%");
        })->orderBy('created_at', 'DESC')->orderBy('id', 'DESC')->paginate(24);

        $php_post_max_size = $this->return_bytes(ini_get('post_max_size'));
        $php_upload_max_filesize = $this->return_bytes(ini_get('upload_max_filesize'));
        $env_max_file_size = $this->return_bytes(env('MAX_FILE_SIZE', ));

        if($php_post_max_size < $php_upload_max_filesize){
            $max_file_size = $php_post_max_size;
        } else {
            if($php_upload_max_filesize < $env_max_file_size){
                $max_file_size = $php_upload_max_filesize;
            } else {
                $max_file_size = $env_max_file_size;
            }
        }

        $data['upload_max_filesize'] = $max_file_size;

        return $data;
    }

    /**
     * Return size in bytes
     *
     * @param string $val
     * @return int $val
    */
    private function return_bytes ($val) {
        if(empty($val))return 0;

        $val = trim($val);

        preg_match('#([0-9]+)[\s]*([a-z]+)#i', $val, $matches);

        $last = '';
        if(isset($matches[2])){
            $last = $matches[2];
        }

        if(isset($matches[1])){
            $val = (int) $matches[1];
        }

        switch (strtolower($last))
        {
            case 'g':
            case 'gb':
                $val *= 1024;
            case 'm':
            case 'mb':
                $val *= 1024;
            case 'k':
            case 'kb':
                $val *= 1024;
        }

        return (int) $val;
    }

    /**
     * Upload a file
     *
     * @param int $project_id
     * @param \Illuminate\Http\Request $request
     * @return \App\Models\Media
     */
    public function upload($project_id, Request $request){
        $project = Project::findOrFail($project_id);

        $user = auth()->user();
        if(!$user->isSuperAdmin() && !$user->hasRole('admin'.$project->id) && !$user->hasRole('editor'.$project->id)){
            throw UnauthorizedException::forRoles(['admin'.$project->id]);
        }

        if($request->has('file')){
            $file = $request->file('file');

            $file_name = $this->renameFile($file->getClientOriginalName(), $project->uuid, $file, $project->disk);

            Storage::disk($project->disk)->putFileAs('public/'.$project->uuid, $request->file('file'), $file_name);

            $extension = $file->getClientOriginalExtension();

            $image_types = ['jpg', 'jpeg', 'png', 'bmp', 'gif', 'webp'];
            if(in_array($extension, $image_types)){
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
     * @param string $file_name
     * @param uuid $project_uuid
     * @param file $file
     * @param string $disk
     * @return string $file_name
     */
    private function renameFile($file_name, $project_uuid, $file, $disk){

        $path = 'public/'.$project_uuid.'/'.$file_name;

        $i = 1;
        while(Storage::disk($disk)->exists($path)){
            $name = explode('.', $file->getClientOriginalName());
            $file_name = $name[0] . '('. $i .')' . '.' . $file->getClientOriginalExtension();
            $path = 'public/'.$project_uuid.'/'.$file_name;
            $i++;
        }

        return $file_name;
    }

    /**
     * Delete a file
     *
     * @param int $project_id
     * @param int $file_id
     * @return \Illuminate\Http\Response
     */
    public function delete($project_id, $file_id, Request $request){
        $project = Project::findOrFail($project_id);

        $user = auth()->user();
        if(!$user->isSuperAdmin() && !$user->hasRole('admin'.$project->id) && !$user->hasRole('editor'.$project->id)){
            throw UnauthorizedException::forRoles(['admin'.$project->id]);
        }

        $file = Media::where('project_id', $project->id)->where('id', $file_id)->firstOrFail();

        $original = 'public/'.$project->uuid.'/'.$file->name;
        if(Storage::disk($file->disk)->exists($original)){
            Storage::disk($file->disk)->delete($original);
        }

        $thumb = 'public/'.$project->uuid.'/thumbnails/'.$file->name;
        if(Storage::disk($file->disk)->exists($thumb)){
            Storage::disk($file->disk)->delete($thumb);
        }

        $file->delete();

        return response([], 200);
    }

    /**
     * Delete multiple files
     *
     * @param int $project_id
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function deleteSelected($project_id, Request $request){
        $project = Project::findOrFail($project_id);

        $user = auth()->user();
        if(!$user->isSuperAdmin() && !$user->hasRole('admin'.$project->id) && !$user->hasRole('editor'.$project->id)){
            throw UnauthorizedException::forRoles(['admin'.$project->id]);
        }

        foreach ($request->get('files') as $file) {
            $file = Media::where('project_id', $project->id)->where('id', $file)->first();

            if($file){
                $original = 'public/'.$project->uuid.'/'.$file->name;
                if(Storage::disk($file->disk)->exists($original)){
                    Storage::disk($file->disk)->delete($original);
                }

                $thumb = 'public/'.$project->uuid.'/thumbnails/'.$file->name;
                if(Storage::disk($file->disk)->exists($thumb)){
                    Storage::disk($file->disk)->delete($thumb);
                }

                $file->delete();
            }
        }
    }

    /**
     * Update a file
     *
     * @param int project_id
     * @param int file_id
     * @param \Illuminate\Http\Request $request
     * @return \App\Models\Media
     */
    public function update($project_id, $file_id, Request $request){
        $project = Project::findOrFail($project_id);

        $user = auth()->user();
        if(!$user->isSuperAdmin() && !$user->hasRole('admin'.$project->id) && !$user->hasRole('editor'.$project->id)){
            throw UnauthorizedException::forRoles(['admin'.$project->id]);
        }

        $media = Media::where('project_id', $project->id)->where('id', $file_id)->firstOrFail();

        $path = 'public/'.$project->uuid.'/'.$media->name;
        $file = Storage::disk($media->disk)->url($path);

        $ext = pathinfo($path, PATHINFO_EXTENSION);

        if($media->name == $request->name.'.'.$ext){
            $media->caption = $request->get('caption');
            $media->save();

            return response($media, 200);
        } else {
            $old_path = 'public/'.$project->uuid.'/'.$media->name;
            $ext = pathinfo($old_path, PATHINFO_EXTENSION);

            $file_name = $request->get('name').'.'.$ext;
            $new_path = 'public/'.$project->uuid.'/'.$file_name;
            $name = pathinfo($new_path, PATHINFO_FILENAME);

            $i = 1;
            while(Storage::disk($media->disk)->exists($new_path)){
                $file_name = $name . '('. $i .')' . '.' . $ext;
                $new_path = 'public/'.$project->uuid.'/'.$file_name;
                $i++;
            }

            $old_thumb_path = 'public/'.$project->uuid.'/thumbnails/'.$media->name;
            Storage::disk($media->disk)->move($old_path, $new_path);

            $image_types = ['jpg', 'jpeg', 'png', 'bmp', 'gif', 'webp'];
            if(in_array($ext, $image_types)){
                $new_thumb_path = 'public/'.$project->uuid.'/thumbnails/'.$file_name;
                Storage::disk($media->disk)->move($old_thumb_path, $new_thumb_path);
            }

            $media->name = $file_name;
            $media->caption = $request->get('caption');
            $media->save();

            return response($media, 200);
        }
    }
}
