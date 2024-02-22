<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $users = User::all();
            return response()->json($users);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    try{
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', 
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
    
        $imagePath = uniqid() . '.' . request('avatar')->extension();
        request('avatar')->storeAs('public/avatar', $imagePath);
        // dd($request);
    
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'avatar' => $imagePath, 
        ]);
    
        return response()->json($user, 201);
    }catch(\Exception $e){
        return response()->json(['message' => $e->getMessage()], 500);
    }
}

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        try{
            return response()->json($user);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
{
    try{

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'email' => 'string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'string|min:8',
            'avatar' => 'nullable|image', 
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
    
        $fileName = null;

        if ($request->hasFile('avatar')) {
            if ($user->image && Storage::disk('public')->exists('avatar/' . $user->avatar)) {
                Storage::disk('public')->delete('avatar/' . $user->avatar);
            }
            // Stockez la nouvelle image et obtenez son nom de fichier
            $imagePath = $request->file('avatar')->store('avatar', 'public');
            // Extraire le nom du fichier de $imagePath
            $fileName = basename($imagePath);
        } else {
            $fileName = $user->avatar; // Gardez l'ancienne image si aucune nouvelle image n'est 
        }
    
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'avatar' => $fileName ? $fileName : $user->avatar,
        ]);
    
        return response()->json($user, 200);
    }catch(\Exception $e){
        return response()->json(['message' => $e->getMessage()], 500);
    }
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try{
            if ($user->avatar && Storage::disk('public')->exists('avatar/'.$user->avatar)) {
                Storage::disk('public')->delete('avatar/'.$user->avatar);
            }
            $user->delete();
            return response()->json(null, 204);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
