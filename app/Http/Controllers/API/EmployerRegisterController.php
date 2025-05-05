<?php

namespace App\Http\Controllers\API;

use Modules\Recruit\Entities\ApplicationSource;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Models\EmployeeDetails;
use Modules\Payroll\Entities\EmployeePayrollCycle;
use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\RoleUser;
use App\Models\Team;
use App\Models\Company;
use App\Models\User;
use App\Models\Role;
use Carbon\Carbon;
use DB;
use App\Models\PermissionType;
use Modules\Recruit\Entities\Recruiter;

class EmployerRegisterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $response = Http::withoutVerifying()->get('http://gulbug.com/staging/mellowacademy_new/api/employer-register');

        //     // Check response status
        //     if (!$response->successful()) {
        //         Log::error('API call failed', ['status' => $response->status(), 'body' => $response->body()]);
        //         return response()->json(['error' => 'Failed to fetch data'], 500);
        //     }
                
        //     // Decode response
        //     $data = json_decode($response->body());
        //     if (!$data || !isset($data->data)) {
        //         Log::error('Invalid API response structure', ['response' => $response->body()]);
        //         return response()->json(['error' => 'Invalid data format'], 500);
        //     }
                
        //     $data = $data->data;
                
        //     // Create Company
        //     $company = Company::create([
        //         'company_name' => $data->company_name ?? '',
        //         'app_name' => $data->company_name ?? '',
        //         'company_email' => $data->email ?? '',
        //         'company_phone' => $data->phone ?? '',
        //         'address' => $data->address ?? '',
        //         'before_days' => 0,
        //         'after_days' => 0,
        //         'allow_client_signup' => 0,
        //         'admin_client_signup_approval' => 0,
        //     ]);
                
        //     // Create User
        //     $user = User::create([
        //         'company_id' => $company->id,
        //         'name' => trim(($data->fname ?? '') . ' ' . ($data->lname ?? '')),
        //         'email' => $data->email,
        //         'password' => Hash::make($data->show_password),
        //         'locale' => 'en',
        //         'status' => 'active',
        //         'login' => 'active',
        //         'dark_theme' => '0',
        //         'rtl' => '0',
        //         'admin_approval' => '1',
        //     ]);
                
        //      // Create EmployeeDetails
        //     $employeeDetails = EmployeeDetails::create([
        //         'company_id' => $company->id,
        //         'user_id' => $user->id,
        //         'address' => $data->address ?? '',
        //         'joining_date' => Carbon::now(),
        //         'date_of_birth' => Carbon::now(),
        //     ]);
            
        //     $allPermissions = Permission::orderBy('id')->get()->pluck('id')->toArray();
    
        //     foreach ($allPermissions as $permission) 
        //     {
        //         $user->permissionTypes()->attach([$permission => ['permission_type_id' => PermissionType::ALL]]);
        //     }
    
        //     // Create RoleUser
        //     for($i=1; $i<=2; $i++)
        //     {
        //         $roleUser = RoleUser::create([
        //             'user_id' => $user->id,
        //             'role_id' => $i,
        //         ]);
        //     }
    
        //     // Create ApplicationSource
        //     $arr = ['LinkedIn', 'Facebook', 'Instagram', 'Twitter', 'Other'];
    
        //     for($i=0; $i<5; $i++)
        //     {
        //         $applicationSource = ApplicationSource::create([
        //             'company_id' => $company->id,
        //             'application_source' => $arr[$i],
        //         ]);
        //     }
    
        //     // Create EmployeePayrollCycle
        //     // EmployeePayrollCycle::create([
        //     //     'company_id' => $company->id,
        //     //     'payroll_cycle_id' => 1,
        //     //     'user_id' => $user->id,
        //     // ]);
    
        //     // Create 
        //     for($i=1; $i<=5; $i++)
        //     {
        //         DB::table('recruit_jobboard_settings')->insert([
        //             'user_id' => $user->id,
        //             'recruit_application_status_id' => $i,
        //             'collapsed' => 0,
        //         ]);
        //     }
            
        //     $team =  Team::groupBy('team_name')->get();
        //     foreach($team as $val)
        //     {
        //         Team::create([
        //             'company_id' => $company->id,
        //             'team_name' => $val,
        //         ]);
        //     }
          
        //     Recruiter::create([
        //         'company_id' => $company->id,
        //         'user_id' => $user->id,
        //         'status' => 'enabled',
        //         'added_by' => $user->id,
        //     ]);
            
        
            // Create Company
            $company = Company::create([
                'company_name' => $request->company_name ?? '',
                'app_name' => $request->company_name ?? '',
                'company_email' => $request->email ?? '',
                'company_phone' => $request->phone ?? '',
                'address' => $request->address ?? '',
                'before_days' => 0,
                'after_days' => 0,
                'allow_client_signup' => 0,
                'admin_client_signup_approval' => 0,
            ]);
                
            // Create User
            $user = User::create([
                'company_id' => $company->id,
                'name' => trim(($request->fname ?? '') . ' ' . ($request->lname ?? '')),
                'email' => $request->email,
                'password' => Hash::make($request->show_password),
                'locale' => 'en',
                'status' => 'active',
                'login' => 'active',
                'dark_theme' => '0',
                'rtl' => '0',
                'admin_approval' => '1',
            ]);
                
             // Create EmployeeDetails
            $employeeDetails = EmployeeDetails::create([
                'company_id' => $company->id,
                'user_id' => $user->id,
                'address' => $request->address ?? '',
                'joining_date' => Carbon::now(),
                'date_of_birth' => Carbon::now(),
            ]);
            
            $allPermissions = Permission::orderBy('id')->get()->pluck('id')->toArray();
    
            foreach ($allPermissions as $permission) 
            {
                $user->permissionTypes()->attach([$permission => ['permission_type_id' => PermissionType::ALL]]);
            }
    
            // Create RoleUser
            for($i=1; $i<=2; $i++)
            {
                $roleUser = RoleUser::create([
                    'user_id' => $user->id,
                    'role_id' => $i,
                ]);
            }
    
            // Create ApplicationSource
            $arr = ['LinkedIn', 'Facebook', 'Instagram', 'Twitter', 'Other'];
    
            for($i=0; $i<5; $i++)
            {
                $applicationSource = ApplicationSource::create([
                    'company_id' => $company->id,
                    'application_source' => $arr[$i],
                ]);
            }
    
            // Create EmployeePayrollCycle
            // EmployeePayrollCycle::create([
            //     'company_id' => $company->id,
            //     'payroll_cycle_id' => 1,
            //     'user_id' => $user->id,
            // ]);
    
            // Create 
            for($i=1; $i<=5; $i++)
            {
                DB::table('recruit_jobboard_settings')->insert([
                    'user_id' => $user->id,
                    'recruit_application_status_id' => $i,
                    'collapsed' => 0,
                ]);
            }
            
            $team =  Team::groupBy('team_name')->get();
            
            foreach($team as $val)
            {
                Team::create([
                    'company_id' => $company->id,
                    'team_name' => $val->team_name,
                ]);
            }
          
            Recruiter::create([
                'company_id' => $company->id,
                'user_id' => $user->id,
                'status' => 'enabled',
                'added_by' => $user->id,
            ]);
            
             $locations = explode(',', $request->location);
            foreach ($locations as $loc) 
            {
                if(!empty($loc))
                {
                     DB::table('company_addresses')->insert([
                        'company_id' => $company->id,
                        'address' => trim($loc),
                        'is_default' => 1,
                        'location' => trim($loc),
                    ]);
                }
               
            }
            
            return response()->json(['success' => true, 'user_id' => $user->id]);
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
    
    // all data fetched its working fine by shankar
//     public function getUserData(Request $request)
// {
//     // Validate request
//     $request->validate([
//         'email' => 'required|email'
//     ]);

//     // Step 1: Fetch user with roles by email
//     $user = User::with('roles')->where('email', $request->email)->first();

//     // Step 2: Handle user not found
//     if (!$user) {
//         return response()->json([
//             'status' => 'error',
//             'message' => 'User not found'
//         ], 404);
//     }

//     // Step 3: Get user's company_id
//     $companyId = $user->company_id;

//     // Step 4: Fetch the first non-admin/client role for that company (e.g., employee)
//     $role = Role::where('company_id', $companyId)
//         ->whereNotIn('name', ['admin', 'client'])
//         ->first(); // If multiple, use ->get()

//     // Step 5: Return user with matched role id
//     return response()->json([
//         'status' => 'success',
//         'user' => $user,
//         'matched_role' => $role ? [
//             'id' => $role->id,
//             'name' => $role->name
//         ] : null,
//         'user_roles' => $user->roles->map(function ($r) {
//             return ['id' => $r->id, 'name' => $r->name];
//         })
//     ], 200);
// }

public function getUserData(Request $request)
{
    // Validate request
    $request->validate([
        'email' => 'required|email'
    ]);

    // Step 1: Fetch user with roles by email
    $user = User::with('roles')->where('email', $request->email)->first();

    // Step 2: Handle user not found
    if (!$user) {
        return response()->json([
            'status' => 'error',
            'message' => 'User not found'
        ], 404);
    }

    // Step 3: Get user's company_id
    $companyId = $user->company_id;

    // Step 4: Fetch the first non-admin/client role for that company (e.g., employee)
    $role = Role::where('company_id', $companyId)
        ->whereNotIn('name', ['admin', 'client'])
        ->first(); // This assumes there's a matching 'employee' role

    // Step 5: Return only the matched role (id and name)
    if ($role) {
        return response()->json([
            'status' => 'success',
                'employer_data'=>$user,
                'role_id' => $role->id,
                'role_name' => $role->name
        ], 200);
    } else {
        return response()->json([
            'status' => 'error',
            'message' => 'No valid role found for this user'
        ], 404);
    }
}




}
