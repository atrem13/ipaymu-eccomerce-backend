<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::orderBy('name')->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data User',
            'data'    => $users  
        ], 200);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name'   => 'required|unique:users',
            'email' => 'required|unique:users',
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        
        DB::beginTransaction();
        try{
            $request['password'] = $request->email;
            $user = User::create($request->all());
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Data #'.$user->name.' Added Successfully.',
                'data'    => $user  
            ], 201);

        }catch(Exception $ex){
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $ex->getMessage(),
            ], 409);
        }


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::findOrfail($id);
        return response()->json([
            'success' => true,
            'message' => 'Detail Data User',
            'data'    => $user 
        ], 200);

    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = User::findOrfail($request->id);
        $validator = Validator::make($request->all(), [
            'name'   => 'required|unique:users,name,' . $user->id,
            'email' => 'required|unique:users,email,' . $user->id,
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        
        DB::beginTransaction();
        try{

            $user->update($request->all());
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Data Updated Successfully.',
                'data'    => $user  
            ], 201);

        }catch(Exception $ex){
            DB::rollback();return response()->json([
                'success' => false,
                'message' => $ex->getMessage(),
            ], 409);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrfail($id);
        DB::beginTransaction();
        try {
            $user->delete();
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Post Deleted',
            ], 200);
        }catch(Exception $ex){
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Post Not Found',
            ], 404);
        }
    }
}
