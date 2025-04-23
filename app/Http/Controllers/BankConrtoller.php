<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBankDetailRequest;
use App\Models\BankDetail;
use App\Models\EmployeeDetails;
use Illuminate\Support\Facades\Auth;

class BankController extends Controller
{
    public function store(Request $request)
    {
        $employee = EmployeeDetails::where('user_id', Auth::id())->first();

        if (!$employee) {
            return response()->json([
                'status' => 'error',
                'message' => 'Employee not found.',
            ], 404);
        }

        BankDetail::updateOrCreate(
            ['employee_details_id' => $employee->id],
            [
                'branch_name' => $request->bank_number, // ✅ matches input name
                'account_name' => $request->account_name,
                'account_number' => $request->account_number,
                'ifsc_code' => $request->ifsc_code,
                'micr_number' => $request->micr_number,
                'account_type' => $request->account_type,
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => __('messages.recordSaved'),
        ]);
    }

}
