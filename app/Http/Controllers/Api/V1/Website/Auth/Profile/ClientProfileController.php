<?php

namespace App\Http\Controllers\Api\V1\Website\Auth\Profile;

use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Models\Client\Client;
use App\Models\Client\ClientUser;
use App\Http\Controllers\Controller;
use App\Services\Upload\UploadService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Http\Resources\Client\website\AllProfileResource;
use App\Http\Requests\Client\ClientUser\UpdateProfileUserRequest;

class ClientProfileController extends Controller implements HasMiddleware
{
    protected $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('auth:client'),
        ];
    }
    //show
    public function show(Request $request)
    {
       return ApiResponse::success( new AllProfileResource($request->user()));
    }
    //update
    public function update(UpdateProfileUserRequest $request)
    {
        $authUser = $request->user();
        $userData = $request->validated();
        $avatarPath = null;
        if(isset($userData['avatar']) && $userData['avatar'] instanceof UploadedFile){
            $avatarPath =  $this->uploadService->uploadFile($userData['avatar'],'clientAvatars');
        }
        $authUser->name = $userData['name']??'';
        $authUser->email = $userData['email']??'';
        if($avatarPath){
            Storage::disk('public')->delete($authUser->getRawOriginal('avatar'));
        }
        $authUser->avatar = $avatarPath;
        $authUser->save();

        $clientId = ClientUser::find($authUser->id)->client_id;
        $client = Client::find($clientId);
        $client->name = $userData['name']??'';
        $client->save();

        return ApiResponse::success([], __('crud.updated'));

    }
}
