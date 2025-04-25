<?php

namespace App\Http\Controllers;

use Modules\Recruit\Entities\RecruitJobApplication;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PayEmployeeController extends Controller
{
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $updated = RecruitJobApplication::where('id', $request->id)->update([
                'payment_amount' => $request->amount,
                'payment_date' => Carbon::now(),
                'payment_status' => "Success",
                'recruit_application_status_id' => "6",
                'term_and_conditional' => 1,
            ]);
    
            // If everything is good, commit the transaction
            DB::commit();
    
            return response()->json([
                'success' => $updated ? true : false,
                'message' => $updated ? 'Payment info updated successfully.' : 'No record found or update failed.',
            ]);
        } catch (\Exception $e) {
            // Something went wrong — roll back the transaction
            DB::rollBack();
    
            return response()->json([
                'success' => false,
                'message' => 'Transaction failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
