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

use App\Models\Content;
use App\Models\Project;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Exceptions\UnauthorizedException;

class CollectionsController extends Controller
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
        if(!$user->isSuperAdmin() && !$user->hasRole('admin'.$project->id)){
            throw UnauthorizedException::forRoles(['admin'.$project->id]);
        }

        return $project;
    }

    /**
     * Store a new collection
     * 
     * @param int $project_id
     * @param \Illuminate\Http\Request  $request
     * @return \App\Models\Collection
     */
    public function store($project_id, Request $request){
        $project = Project::findOrFail($project_id);

        $user = auth()->user();
        if(!$user->isSuperAdmin() && !$user->hasRole('admin'.$project->id)){
            throw UnauthorizedException::forRoles(['admin'.$project->id]);
        }

        $request->validate([
            'name' => 'required',
            'slug' => ['required','not_in:project-media', Rule::unique('collections')->where(function ($query) use ($project) {
                return $query->where('project_id', $project->id);
            })]
        ],[
            'slug.not_in' => 'This is a reserved slug. Type a different slug.'
        ]);

        $collection = Collection::create([
            'name' => $request->get('name'),
            'slug' => $request->get('slug'),
            'project_id' => $project->id,
        ]);

        $collection->order = $collection->id;
        $collection->save();

        return response($collection, 200);
    }

    /**
     * Update collection list order
     * 
     * @param int $project_id
     * @param \Illuminate\Http\Request  $request
     * @return void
     */
    public function updateOrder($project_id, Request $request){
        $project = Project::findOrFail($project_id);

        $user = auth()->user();
        if(!$user->isSuperAdmin() && !$user->hasRole('admin'.$project->id)){
            throw UnauthorizedException::forRoles(['admin'.$project->id]);
        }

        foreach($request->all() as $item){
            $collection =  Collection::where('project_id', $project->id)->where('id', $item['id'])->firstOrFail();

            $collection->order = $item['order'];
            $collection->save();
        }
    }

    /**
     * Get collection by id
     * 
     * @param int $project_id
     * @param int $collection_id
     * @return \App\Models\Project
     * @return \App\Models\Collection
     */
    public function show($project_id, $collection_id){
        $project = Project::with('collections')->findOrFail($project_id);

        $user = auth()->user();
        if(!$user->isSuperAdmin() && !$user->hasRole('admin'.$project->id)){
            throw UnauthorizedException::forRoles(['admin'.$project->id]);
        }

        $collection = Collection::with(['project', 'project.collections', 'project.collections.fields', 'fields'])
                                    ->where('project_id', $project->id)
                                    ->where('id', $collection_id)->firstOrFail();

        foreach ($collection->fields as $field) {
            $field->validations = json_decode($field->validations);
            $field->options = json_decode($field->options);
        }

        $data['project'] = $project;
        $data['collection'] = $collection;

        return $data;
    }

    /**
     * Update collection
     * 
     * @param int $project_id
     * @param int $collection_id
     * @return \App\Models\Collection
     */
    public function update($project_id, $collection_id, Request $request){
        $project = Project::findOrFail($project_id);

        $user = auth()->user();
        if(!$user->isSuperAdmin() && !$user->hasRole('admin'.$project->id)){
            throw UnauthorizedException::forRoles(['admin'.$project->id]);
        }

        $collection = Collection::where('project_id', $project->id)->where('id', $collection_id)->firstOrFail();

        $request->validate([
            'name' => 'required',
            'slug' => ['required', 'not_in:project-media', Rule::unique('collections')->where(function ($query) use ($project) {
                return $query->where('project_id', $project->id);
            })->ignore($collection->id)]
        ], [
            'slug.not_in' => 'This is a reserved slug. Type a different slug.'
        ]);

        $collection->update([
            'name' => $request->get('name'),
            'slug' => $request->get('slug'),
        ]);

        return response($collection, 200);
    }

    /** 
     * Delete collection
     * 
     * @param int $project_id
     * @param int $collection_id
     * @return \Illuminate\Http\Response
     */
    public function delete($project_id, $collection_id){
        $project = Project::findOrFail($project_id);

        $user = auth()->user();
        if(!$user->isSuperAdmin() && !$user->hasRole('admin'.$project->id)){
            throw UnauthorizedException::forRoles(['admin'.$project->id]);
        }

        $collection = Collection::where('project_id', $project->id)->where('id', $collection_id)->firstOrFail();

        $collection->fields()->delete();
        $collection->content()->forceDelete();
        $collection->meta()->forceDelete();

        if($collection->delete()){
            return response([], 200);
        } else {
            return response([], 404);
        }
    }
}
