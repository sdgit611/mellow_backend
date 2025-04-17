<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Company;
use App\Models\EmployeeDetails;
use App\Models\Role;
use App\Models\Permission;
use App\Models\PermissionType;
use App\Models\UniversalSearch;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AccountSetupRequest;
use App\Helper\Reply;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class EmployerRegsitController extends Controller
{
    public function employerRegister(Request $request)
    {
        // Inline validation
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Company setup
            $setting = Company::firstOrCreate();
            $setting->company_name = $request->company_name;
            $setting->app_name = $request->company_name;
            $setting->timezone = 'Asia/Kolkata';
            $setting->date_picker_format = 'dd-mm-yyyy';
            $setting->moment_format = 'DD-MM-YYYY';
            $setting->rounded_theme = 1;
            $setting->save();

            // Create admin user
            $user = new User();
            $user->name = $request->full_name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->company_id = $setting->id;
            $user->save();

            // Create employee profile
            $employee = new EmployeeDetails();
            $employee->user_id = $user->id;
            $employee->employee_id = $user->id;
            $employee->company_id = $setting->id;
            $employee->save();

            // Add universal search entry
            $search = new UniversalSearch();
            $search->searchable_id = $user->id;
            $search->title = $user->name;
            $search->route_name = 'employees.show';
            $search->save();

            // Attach roles
            $adminRole = Role::where('company_id', $setting->id)->where('name', 'admin')->first();
            $employeeRole = Role::where('company_id', $setting->id)->where('name', 'employee')->first();
            $user->roles()->attach([$adminRole->id, $employeeRole->id]);

            // Attach all permissions
            $allPermissions = Permission::orderBy('id')->pluck('id');
            foreach ($allPermissions as $permissionId) {
                $user->permissionTypes()->attach([
                    $permissionId => ['permission_type_id' => PermissionType::ALL]
                ]);
            }

            DB::commit();

            // Login and return token
            Auth::login($user);
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => __('messages.signupSuccess'),
                'user' => $user,
                'token' => $token
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Error occurred during setup.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
