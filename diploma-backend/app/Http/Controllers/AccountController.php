<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AccountPicture;
use App\Models\User;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    public function initialAccountPicture(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'color' => 'required|string',
            'user_id' => 'required|int|unique:account_pictures',
        ]);

        if($validator->fails()){
            return response()->json([], 422);
        }

        $picture = AccountPicture::make([
            'url' => null,
            'color' => $req->color,
            'user_id' => $req->user_id,
        ]);

        $picture->save();

        return response()->json([
            'account_picture' => $picture,
        ], 201);
    }

    public function getProfilePic($id)
    {
        if($id == null)
        {
            return response()->json([
                'message'=>'Failed validator.'
            ], 422);
        }
        $picture = AccountPicture::where('user_id', $id)->first();

        if(!$picture)
        {
            return response()->json([
                'message'=>"There is no account avatar with that id."
            ], 422);
        }

        $url = $picture->url;

        return response()->file(storage_path('app/'.$url));
    }

    public function testGetImage()
    {
        return response()->file(storage_path('app/profile_pics/download.jpg'));
    }

    public function updateProfileName(Request $req) 
    {
        $validator = Validator::make($req->all(), [
            'user_id' => 'required|integer',
            'name' => "required|string"
        ]);

        if($validator->fails()){
            return response()->json([], 422);
        }

        $user_id = $req->user_id;
        $name = $req->name;

        $user = User::where('id', $user_id)->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        $user->name = $name;
        $user->save();

        return response()->json([
            'user' => $user,
        ], 201);
    }

    public function uploadProfilePic(Request $req)
    {
        $validator = Validator::make($req->all(),[
            'user_id' => 'required|integer',
            'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg'
        ]);

        if($validator->fails()){
            return response()->json([
                'message'=>'Failed validator.'
            ], 422);
        }

        if ($req->hasFile('image')) {
            $user_id = $req->user_id;
            $imageFile = $req->file('image');
            $imageName = $imageFile->getClientOriginalName();
    
            $imagePath = Storage::putFileAs('profile_pics', $imageFile, $imageName);
    
            $profilePic = AccountPicture::where('user_id', $user_id)->first();
    
            if (!$profilePic) {
                $profilePic = new AccountPicture();
                $profilePic->user_id = $user_id;
            }
    
            $profilePic->url = $imagePath;
            $profilePic->color = '';
            $profilePic->save();

            $file = Storage::get($profilePic->url);

            return response()->json([
                'message'=>"Picture uploaded successfully."
            ], 201);
        }
    
        return response()->json([
            'message' => 'Profile picture upload failed',
        ], 400);
    }
}
