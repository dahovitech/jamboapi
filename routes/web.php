<?php

use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\FormController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\CollectionsController;
use App\Http\Controllers\MediaLibraryController;
use App\Http\Controllers\CollectionFieldsController;

Route::middleware(['auth'])->get('/', function () {
    return view('app');
});

Route::middleware('auth:web')->prefix('admin')->group(function(){
    Route::get('/user', function () {
        $user = auth()->user();
        return new UserResource($user);
    });

    Route::post('/user/update_name', [UsersController::class, 'updateName']);
    Route::post('/user/update_email', [UsersController::class, 'updateEmail']);
    Route::post('/user/update_password', [UsersController::class, 'updatePassword']);

    Route::prefix('projects')->group(function(){
        Route::get('/', [ProjectsController::class, 'index']);
        Route::post('/', [ProjectsController::class, 'store'])->middleware(['role:super_admin']);
        Route::get('/{id}', [ProjectsController::class, 'show']);
        Route::post('/update/{id}', [ProjectsController::class, 'update']);
        Route::delete('/delete/{id}', [ProjectsController::class, 'delete'])->middleware(['role:super_admin']);

        Route::prefix('settings')->middleware(['role:super_admin'])->group(function(){
            Route::get('/locales/{id}', [ProjectsController::class, 'locales']);
            Route::post('/locales/add/{id}', [ProjectsController::class, 'addLocale']);
            Route::post('/locales/change-default-locale/{id}', [ProjectsController::class, 'changeDefaultLocale']);
            Route::post('/locales/delete-locale/{id}', [ProjectsController::class, 'deleteLocale']);

            Route::get('/users/{id}', [ProjectsController::class, 'users']);
            Route::post('/users/assign/{id}', [ProjectsController::class, 'assignUser']);
            Route::post('/users/remove-user/{id}', [ProjectsController::class, 'removeUser']);
            Route::post('/users/new/{id}', [ProjectsController::class, 'newUser']);

            Route::get('/api/{id}', [ProjectsController::class, 'api']);
            Route::post('/api/new-token/{id}', [ProjectsController::class, 'newToken']);
            Route::post('/api/update-token/{id}', [ProjectsController::class, 'updateToken']);
            Route::post('/api/delete-token/{id}', [ProjectsController::class, 'deleteToken']);
            Route::post('/api/enable_public_access/{id}', [ProjectsController::class, 'enablePublicAPIAccess']);
            Route::post('/api/disable_public_access/{id}', [ProjectsController::class, 'disablePublicAPIAccess']);

            Route::get('/webhooks/{project_id}', [ProjectsController::class, 'webhooks']);
            Route::get('/webhooks/{project_id}/logs/{webhook_id}', [ProjectsController::class, 'webhookLogs']);
            Route::delete('/webhooks/{project_id}/logs/{webhook_id}', [ProjectsController::class, 'deleteWebhookLogs']);
            Route::post('/webhooks/new/{project_id}', [ProjectsController::class, 'newWebhook']);
            Route::post('/webhooks/update/{project_id}', [ProjectsController::class, 'updateWebhook']);
            Route::post('/webhooks/delete/{project_id}', [ProjectsController::class, 'deleteWebhook']);
        });
    });

    Route::prefix('collections')->group(function(){
        Route::get('/project/{id}', [CollectionsController::class, 'project']);
        Route::post('/store/{project_id}', [CollectionsController::class, 'store']);
        Route::post('/update-order/{project_id}', [CollectionsController::class, 'updateOrder']);
        Route::get('/show/{project_id}/{collection_id}', [CollectionsController::class, 'show']);
        Route::post('/update/{project_id}/{collection_id}', [CollectionsController::class, 'update']);
        Route::delete('/delete/{project_id}/{collection_id}', [CollectionsController::class, 'delete']);

        Route::prefix('fields')->group(function(){
            Route::post('/store/{project_id}/{collection_id}', [CollectionFieldsController::class, 'store']);
            Route::post('/update/{project_id}/{collection_id}/{field_id}', [CollectionFieldsController::class, 'update']);
            Route::post('/update-order/{project_id}/{collection_id}', [CollectionFieldsController::class, 'updateOrder']);
            Route::delete('/delete/{project_id}/{collection_id}/{field_id}', [CollectionFieldsController::class, 'delete']);
        });
    });

    Route::prefix('content')->group(function(){
        Route::get('/project/{id}', [ContentController::class, 'project']);
        Route::get('/{project_id}/{collection_id}', [ContentController::class, 'index']);
        Route::get('/new/{project_id}/{collection_id}', [ContentController::class, 'new']);
        Route::post('/store/{project_id}/{collection_id}', [ContentController::class, 'store']);
        Route::get('/edit/{project_id}/{collection_id}/{content_id}', [ContentController::class, 'edit']);
        Route::post('/update/{project_id}/{collection_id}/{content_id}', [ContentController::class, 'update']);
        Route::get('/unpublish/{project_id}/{collection_id}/{content_id}', [ContentController::class, 'unpublish']);
        Route::delete('/move-to-trash/{project_id}/{collection_id}/{content_id}', [ContentController::class, 'moveToTrash']);
        Route::delete('/delete/{project_id}/{collection_id}/{content_id}', [ContentController::class, 'delete']);

        Route::post('/get-selected-records/{project_id}', [ContentController::class, 'getSelectedRecords']);
        Route::post('/get-selected-files/{project_id}', [ContentController::class, 'getSelectedFiles']);

        Route::post('/publish-selected/{project_id}/{collection_id}', [ContentController::class, 'publishSelected']);
        Route::post('/unpublish-selected/{project_id}/{collection_id}', [ContentController::class, 'unPublishSelected']);
        Route::post('/move-to-trash-selected/{project_id}/{collection_id}', [ContentController::class, 'moveToTrashSelected']);
        Route::post('/delete-selected/{project_id}/{collection_id}', [ContentController::class, 'deleteSelected']);
        Route::post('/restore-selected/{project_id}/{collection_id}', [ContentController::class, 'restoreSelected']);

        Route::get('/forms/{project_id}/{collection_id}', [FormController::class, 'forms']);
        Route::post('/forms/{project_id}/{collection_id}', [FormController::class, 'store']);
        Route::post('/forms/save/{project_id}/{collection_id}/{form_id}', [FormController::class, 'save']);
        Route::delete('/forms/delete/{project_id}/{collection_id}/{form_id}', [FormController::class, 'delete']);
    });

    Route::prefix('media')->group(function(){
        Route::get('/get-files/{project_id}', [MediaLibraryController::class, 'getFiles']);
        Route::post('/upload/{project_id}', [MediaLibraryController::class, 'upload']);
        Route::delete('/delete/{project_id}/{file_id}', [MediaLibraryController::class, 'delete']);
        Route::post('/delete-selected/{project_id}', [MediaLibraryController::class, 'deleteSelected']);
        Route::post('/update/{project_id}/{file_id}', [MediaLibraryController::class, 'update']);
    });
});

Route::get('uploads/{dir}/{file}', function($dir, $file){

	$path = 'public/' . $dir . '/' . $file;

    if(Storage::disk('local')->exists($path)){
        return  Storage::response($path);
    }

    abort(404);
});
Route::get('uploads/thumb/{dir}/{file}', function($dir, $file){

	$path = 'public/' . $dir . '/thumbnails/' . $file;

    if(Storage::disk('local')->exists($path)){
        return  Storage::response($path);
    }

    abort(404);
});

Route::get('forms/preview/{form_uuid}', [FormController::class, 'showPreview']);
Route::get('forms/{form_uuid}', [FormController::class, 'showEmbeded']);
Route::post('forms/{form_uuid}', [FormController::class, 'getEmbeded']);
Route::post('forms/submit/{form_uuid}', [FormController::class, 'submit']);
Route::post('forms/{form_uuid}/upload', [FormController::class, 'upload']);

require __DIR__.'/auth.php';
