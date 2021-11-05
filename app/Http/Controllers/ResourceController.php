<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\Resource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class ResourceController extends Controller
{
    public function index() {
        try {
            $resources = Resource::all();

            return response()->json(compact('resources'));
        } catch (Exception $e) {
            return response()->json([
                'status' => 'Error', 
                'message' => $ex->getMessage()], 
                500
            );
        }
    }

    public function store(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'code'      => 'required|unique:resources',
                'name'      => 'required|string|max:255',
                'qty'       => 'required|integer',
                'unit'      => 'required|string|max:255',
                'brand'     => 'required|string|max:255',
                'desc'      => 'string|max:255',
                'created_by'=> 'required|integer'
            ]);
    
            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }
    
            $resource = Resource::create([
                'code'          => $request->code,
                'name'          => $request->name,
                'qty'           => $request->qty,
                'unit'          => $request->unit,
                'brand'         => $request->brand,
                'desc'          => $request->desc,
                'created_by'    => $request->created_by,
                'created_at'    => Carbon::now(),
            ]);

            return response()->json([
                'status'    => 'Success', 
                'message'   => 'Resource Created',
                'resource'  => $resource
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'Error', 
                'message' => $ex->getMessage()], 
                500
            );
        }
    }

    public function show($id) {
        try { 
            $resource = Resource::findOrFail($id);

            return response()->json([
                'status'    => 'Success', 
                'message'   => 'Resource Showed',
                'resource'  => $resource
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'Error', 
                'message' => $ex->getMessage()], 
                500
            );
        }
    }

    public function update(Request $request, $id) {
        try {
            $validator = Validator::make($request->all(), [
                'code'      => 'required|unique:resources,id,'.$request->code,
                'name'      => 'required|string|max:255',
                'qty'       => 'required|integer',
                'unit'      => 'required|string|max:255',
                'brand'     => 'required|string|max:255',
                'desc'      => 'string|max:255',
                'updated_by'=> 'required|integer'
            ]);
    
            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }
    
            $resource = Resource::where('id', $id)->update([
                'code'          => $request->code,
                'name'          => $request->name,
                'qty'           => $request->qty,
                'unit'          => $request->unit,
                'brand'         => $request->brand,
                'desc'          => $request->desc,
                'updated_by'    => $request->updated_by,
                'updated_at'    => Carbon::now(),
            ]);

            return response()->json([
                'status'    => 'Success', 
                'message'   => 'Resource Updated',
                'resource'  => Resource::findOrFail($id)
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'Error', 
                'message' => $ex->getMessage()], 
                500
            );
        }
    }

    public function delete(Request $request, $id) {
        try {
            $validator = Validator::make($request->all(), [
                'user_delete' => 'required|integer',
            ]);
    
            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }

            $user = Resource::findOrFail($id);
            $user->deleted_at = Carbon::now();
            $user->deleted_by = $request->user_delete;
            $user->update();

            return response()->json([
                'status'    => 'Success', 
                'message'   => 'Deleted user',
                'user'      => $user
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'Error', 
                'message' => $ex->getMessage()], 
                500
            );
        }
    }
}
