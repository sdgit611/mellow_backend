<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\RegistrationRequest;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\EmployeeDetails;
use Illuminate\Http\Request;
use App\Models\{ Company, Role, RoleUser, User };
use Carbon\Carbon;


class RegistrationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return "okay";
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RegistrationRequest $request)
    {
        $company = Company::first();

        if (!$company) {
            return response()->json(['error' => 'No company found.'], 404);
        }
    
        $role = Role::where('name', 'employee')->first();
    
        if (!$role) {
            return response()->json(['error' => 'Role "employee" not found.'], 404);
        }
    
        // Start DB transaction to ensure data consistency
        DB::beginTransaction();
    
        try {
            // Create user
            $user = User::create([
                'company_id' => $company->id,
                'name'       => $request->name,
                'email'      => $request->email,
                'password'   => Hash::make($request->password),
                'gender'     => $request->gender ?? null,
            ]);
    
            // Assign role
            RoleUser::create([
                'user_id' => $user->id,
                'role_id' => $role->id,
            ]);
    
            // Save employee details
            EmployeeDetails::create([
                'company_id'            => $company->id,
                'user_id'               => $user->id,
                'employee_profile_name' => $request->profile,
                'joining_date' => $request->joining_date ? Carbon::parse($request->joining_date) : Carbon::now(),
            ]);
    
            DB::commit();
    
            return response()->json([
                'message' => 'Employee registered successfully.',
                'user' => $user
            ], 201);
    
        } catch (\Exception $e) {

            // undo all changes
            DB::rollBack();
    
            return response()->json([
                'error' => 'Something went wrong.',
                'details' => $e->getMessage()
            ], 500);
        }

        return $user;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
