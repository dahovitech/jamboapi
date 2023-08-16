<?php
/*
 * This file is part of the JamboapiCMS package.
 *
 * (c) Prudence ASSOGBA <prudence@dahovi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Http\Controllers\API;

use App\Models\Project;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;

class ProjectsController extends Controller {

    /**
     * Get project
     *
     * @param string $uuid
     * @return \App\Http\Resources\ProjectResource
     */
    public function show($uuid){
        $project = Project::where('uuid', $uuid)->first();

        if(!$project){
            return response(['error' => 'Project not found!'], 404);
        }
        if(!$project->public_api && !auth('sanctum')->check()){
            return response()->json(['message' => 'Unauthenticated.']);
        }

        return new ProjectResource($project);
    }
}
