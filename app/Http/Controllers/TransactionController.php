<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\Transaction;
use App\Models\ResTrans;
use App\Models\Resource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function index() {
        try {
            $transactions = Transaction::all();

            return response()->json(compact('transactions'));
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
                'type'      => 'required|integer',
                'name'      => 'required|string|max:255',
                'created_by'=> 'required|integer',
                'qty'       => 'required|integer',
                'product_id'=> 'required|integer'
            ]);
    
            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }
    
            $transaction = Transaction::create([
                'name'          => $request->name,
                'type'          => $request->type,
                'trans_date'    => Carbon::now(),
                'created_by'    => $request->created_by,
                'created_at'    => Carbon::now()
            ]);

            $resTrans = ResTrans::create([
                'product_id'    => $request->product_id,
                'trans_id'      => $transaction->id,
                'qty'           => $request->qty,
                'created_by'    => $request->created_by,
                'created_at'    => Carbon::now()
            ]);

            if ($request->type == 1) {
                $resource               = Resource::findOrFail($request->product_id);
                $resource->qty          = (int) $resource->qty + (int) $request->qty;
                $resource->updated_at   = Carbon::now();
                $resource->updated_by   = $request->created_by;
                $resource->update();
            } elseif ($request->type == 0) {
                $resource               = Resource::findOrFail($request->product_id);
                $resource->qty          = (int) $resource->qty - (int) $request->qty;
                $resource->updated_at   = Carbon::now();
                $resource->updated_by   = $request->created_by;
                $resource->update();
            }

            return response()->json(compact('transaction', 'resource'));
        } catch (Exception $e) {
            return response()->json([
                'status' => 'Error', 
                'message' => $ex->getMessage()], 
                500
            );
        }
    }

    public function history($id, $product_id) {
        try {
            $transaction = Transaction::findOrFail($id);
            $resource = ResTrans::where('product_id',$product_id)->get();

            return response()->json([
                'transaction_name'  => $transaction->name,
                'resource_trans'    => $resource
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'Error', 
                'message' => $ex->getMessage()], 
                500
            );
        } 
    }
}
