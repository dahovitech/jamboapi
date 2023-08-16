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

use App\Models\Project;
use App\Models\Collection;
use Illuminate\Http\Request;
use App\Models\CollectionField;
use Illuminate\Validation\Rule;
use Spatie\Permission\Exceptions\UnauthorizedException;

class CollectionFieldsController extends Controller
{
    /**
     * Store a new field
     *
     * @param int $project_id
     * @param int $collection_id
     * @param \Illuminate\Http\Request $request
     * @return \App\Models\CollectionField
     */
    public function store($project_id, $collection_id, Request $request){
        $project = Project::findOrFail($project_id);

        $user = auth()->user();
        if(!$user->isSuperAdmin() && !$user->hasRole('admin'.$project->id)){
            throw UnauthorizedException::forRoles(['admin'.$project->id]);
        }

        $collection = Collection::where('project_id', $project->id)->where('id', $collection_id)->firstOrFail();

        $request->validate([
            'label' => 'required',
            'name' => ['required', Rule::unique('collection_fields')->where(function ($query) use ($collection) {
                return $query->where('collection_id', $collection->id);
            })],
            'options.enumeration' => 'required_if:type,enumeration',
            'options.relation.collection' => 'required_if:type,relation',
            'validations.charcount.type' => 'required',
            'validations.charcount.min' => 'nullable|numeric',
            'validations.charcount.max' => 'nullable|numeric',
        ],[
            'options.enumeration.required_if' => "Add at least one option to enumeration list",
            'options.relation.collection.required_if' => "Select a collection",
            'validations.charcount.type.required' => "This field is required",
            'validations.charcount.min.numeric' => "Must be numeric",
            'validations.charcount.max.numeric' => "Must be numeric",
        ]);

        $charcountErrors = [];
        $req_charcount =  $request->get('validations')['charcount'];
        if($req_charcount['status']){
            if($req_charcount['type'] == 'Between'){
                if(!isset($req_charcount['min']) || $req_charcount['min'] == "" || $req_charcount['min'] == null){
                    $charcountErrors['errors']['validations.charcount.min'] = ['Enter a value'];
                }
                if(!isset($req_charcount['max']) || $req_charcount['max'] == "" || $req_charcount['max'] == null){
                    $charcountErrors['errors']['validations.charcount.max'] = ['Enter a value'];
                }
                if(isset($req_charcount['min']) && isset($req_charcount['max'])){
                    if($req_charcount['min'] > $req_charcount['max']){
                        $charcountErrors['errors']['validations.charcount.min'] = ['Must be less than max'];
                    }
                }
            } elseif($req_charcount['type'] == 'Min'){
                if(!isset($req_charcount['min']) || $req_charcount['min'] == "" || $req_charcount['min'] == null){
                    $charcountErrors['errors']['validations.charcount.min'] = ['Enter a value'];
                }
            } elseif($req_charcount['type'] == 'Max'){
                if(!isset($req_charcount['max']) || $req_charcount['max'] == "" || $req_charcount['max'] == null){
                    $charcountErrors['errors']['validations.charcount.max'] = ['Enter a value'];
                }
            }
        }
        if(count($charcountErrors) !== 0){
            return response($charcountErrors, 422);
        }

        $validations = $request->get('validations');
        if(isset($request->get('options')['repeatable']) && $request->get('options')['repeatable']){
            $validations['unique'] = ['status' => false];
        }

        $field = CollectionField::create([
            'type' => $request->get('type'),
            'label' => $request->get('label'),
            'name' => $request->get('name'),
            'description' => $request->get('description'),
            'placeholder' => $request->get('placeholder'),
            'options' => json_encode($request->get('options')),
            'validations' => json_encode($validations),
            'project_id' => $project->id,
            'collection_id' => $collection->id,
        ]);

        $field->order = $field->id;
        $field->save();

        return response($field, 200);
    }

    /**
     * Update field
     *
     * @param int $project_id
     * @param int $collection_id
     * @param int $field_id
     * @param \Illuminate\Http\Request $request
     * @return \App\Models\CollectionField
     */
    public function update($project_id, $collection_id, $field_id, Request $request){
        $project = Project::findOrFail($project_id);

        $user = auth()->user();
        if(!$user->isSuperAdmin() && !$user->hasRole('admin'.$project->id)){
            throw UnauthorizedException::forRoles(['admin'.$project->id]);
        }

        $collection = Collection::where('project_id', $project->id)->where('id', $collection_id)->firstOrFail();

        $field = CollectionField::where('project_id', $project->id)
                                ->where('collection_id', $collection->id)
                                ->where('id', $field_id)->firstOrFail();

        $request->validate([
            'label' => 'required',
            'name' => ['required', Rule::unique('collection_fields')->where(function ($query) use ($project, $collection) {
                return $query->where('project_id', $project->id)->where('collection_id', $collection->id);
            })->ignore($field->id)],
            'options.enumeration' => 'required_if:type,enumeration',
            'options.relation.collection' => 'required_if:type,relation',
            'validations.charcount.type' => 'required',
            'validations.charcount.min' => 'nullable|numeric',
            'validations.charcount.max' => 'nullable|numeric',
        ],[
            'options.enumeration.required_if' => "Add at least one option to enumeration list",
            'options.relation.collection.required_if' => "Select a collection",
            'validations.charcount.type.required' => "This field is required",
            'validations.charcount.min.numeric' => "Must be numeric",
            'validations.charcount.max.numeric' => "Must be numeric",
        ]);

        $charcountErrors = [];
        $req_charcount =  $request->get('validations')['charcount'];
        if($req_charcount['status']){
            if($req_charcount['type'] == 'Between'){
                if(!isset($req_charcount['min']) || $req_charcount['min'] == "" || $req_charcount['min'] == null){
                    $charcountErrors['errors']['validations.charcount.min'] = ['Enter a value'];
                }
                if(!isset($req_charcount['max']) || $req_charcount['max'] == "" || $req_charcount['max'] == null){
                    $charcountErrors['errors']['validations.charcount.max'] = ['Enter a value'];
                }
                if(isset($req_charcount['min']) && isset($req_charcount['max'])){
                    if($req_charcount['min'] > $req_charcount['max']){
                        $charcountErrors['errors']['validations.charcount.min'] = ['Must be less than max'];
                    }
                }
            } elseif($req_charcount['type'] == 'Min'){
                if(!isset($req_charcount['min']) || $req_charcount['min'] == "" || $req_charcount['min'] == null){
                    $charcountErrors['errors']['validations.charcount.min'] = ['Enter a value'];
                }
            } elseif($req_charcount['type'] == 'Max'){
                if(!isset($req_charcount['max']) || $req_charcount['max'] == "" || $req_charcount['max'] == null){
                    $charcountErrors['errors']['validations.charcount.max'] = ['Enter a value'];
                }
            }
        }
        if(count($charcountErrors) !== 0){
            return response($charcountErrors, 422);
        }

        $validations = $request->get('validations');
        if(isset($request->get('options')['repeatable']) && $request->get('options')['repeatable']){
            $validations['unique'] = ['status' => false];
        }

        $field->update([
            'type' => $request->get('type'),
            'label' => $request->get('label'),
            'name' => $request->get('name'),
            'description' => $request->get('description'),
            'placeholder' => $request->get('placeholder'),
            'options' => json_encode($request->get('options')),
            'validations' => json_encode($validations),
        ]);

        return response($field, 200);
    }

    /**
     * Update field list order
     *
     * @param int $project_id
     * @param int $collection_id
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function updateOrder($project_id, $collection_id, Request $request){
        $project = Project::findOrFail($project_id);

        $user = auth()->user();
        if(!$user->isSuperAdmin() && !$user->hasRole('admin'.$project->id)){
            throw UnauthorizedException::forRoles(['admin'.$project->id]);
        }

        $collection = Collection::where('project_id', $project->id)->where('id', $collection_id)->firstOrFail();

        foreach($request->all() as $item){
            if($field = CollectionField::where('project_id', $project->id)->where('collection_id', $collection->id)->find($item['id'])){
                $field->order = $item['order'];
                $field->save();
            }
        }
    }

    /**
     * Delete field
     *
     * @param int $project_id
     * @param int $collection_id
     * @param int $field_id
     * @return \Illuminate\Http\Response
     */
    public function delete($project_id, $collection_id, $field_id){
        $project = Project::findOrFail($project_id);

        $user = auth()->user();
        if(!$user->isSuperAdmin() && !$user->hasRole('admin'.$project->id)){
            throw UnauthorizedException::forRoles(['admin'.$project->id]);
        }

        $collection = Collection::where('project_id', $project->id)->where('id', $collection_id)->firstOrFail();

        $field = CollectionField::where('project_id', $project->id)->where('collection_id', $collection->id)->where('id', $field_id)->firstOrFail();

        if($field->delete()){
            return response([], 200);
        } else {
            return response([], 404);
        }
    }
}
