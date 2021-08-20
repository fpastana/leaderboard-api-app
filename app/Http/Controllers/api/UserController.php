<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $user = User::orderBy('points', 'desc')->with('addresses')->get();

        return response()->json($user);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|unique:App\Models\User,email|max:255',
            'age' => 'required|integer',
            'points' => 'required|integer',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 412);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'age' => $request->age,
            'points' => 0,
            'password' => Hash::make('password'),
        ]);

        return response()->json($user, 200);
    }

    public function destroy($id)
    {
        $user = User::where('id', $id)
        ->delete();

        return response()->json($user);
    }

    public function addPoint(Request $request, $id)
    {
        $request->request->add(['id' => $request->id]);

        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:App\Models\User,id',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 412);
        }

        $user = User::where('id', $id)->first();

        $points = $user->points + 1;

        $updatedUser = User::where('id', $id)
        ->update([
            'points' => $points
        ]);

        return response()->json($updatedUser, 200);
    }

    public function subPoint(Request $request, $id)
    {
        $request->request->add(['id' => $request->id]);

        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:App\Models\User,id',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 412);
        }

        $user = User::where('id', $id)->first();

        $points = $user->points - 1;

        $updatedUser = User::where('id', $id)
        ->update([
            'points' => $points
        ]);

        return response()->json($updatedUser, 200);
    }
}
