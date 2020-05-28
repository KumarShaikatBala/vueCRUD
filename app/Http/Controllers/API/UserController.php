<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Image;
use File;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    public function index()
    {
        return User::latest()->paginate(10);
    }


    public function store(Request $request)
    {
        //return $request;
        $this->validate($request,[
            'name' =>'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6'
        ]);
        return User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'bio' => $request['bio'],
            'type' => $request['type'],
            'password' => Hash::make($request['password']),
        ]);
    }

    public function profile()
    {

        return auth('api')->user();
    }
    public function updateProfile(Request $request)
    {
        $user=auth('api')->user();
        $this->validate($request,[
            'name' =>'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => 'sometimes|required|min:6'
        ]);


        $currentPhoto=$user->photo;
        if ($request->photo!=$currentPhoto) {

            $image = $request->get('photo');
            $name = time().'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
            \Image::make($request->photo)->save('img/'.$name);

            $request->merge(['photo'=>$name]);

            $image_path ='img/'.$currentPhoto;

            if(File::exists($image_path))
            {
                File::delete($image_path);

            }




        }


        if (!empty($request->password)){
            $request->merge(['password'=>Hash::make($request['password'])]);
        }


$user->update($request->all());
    }




    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user=User::findOrFail($id);
        $this->validate($request,[
            'name' =>'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id
        ]);
        $user->update(
            $request->all()
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::destroy($id);
    }
}
