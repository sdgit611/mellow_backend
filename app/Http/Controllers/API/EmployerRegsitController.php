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
    // public function employerRegister(Request $request)
    // {
    //     // Inline validation
    //     $validator = Validator::make($request->all(), [
    //         'company_name' => 'required|string|max:255',
    //         'full_name' => 'required|string|max:255',
    //         'email' => 'required|email|unique:users,email',
    //         'password' => 'required|string|min:8|confirmed',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => 'error',
    //             'errors' => $validator->errors()
    //         ], 422);
    //     }

    //     DB::beginTransaction();
    //     try {
    //         // Company setup
    //         $setting = Company::firstOrCreate();
    //         $setting->company_name = $request->company_name;
    //         $setting->app_name = $request->company_name;
    //         $setting->timezone = 'Asia/Kolkata';
    //         $setting->date_picker_format = 'dd-mm-yyyy';
    //         $setting->moment_format = 'DD-MM-YYYY';
    //         $setting->rounded_theme = 1;
    //         $setting->save();

    //         // Create admin user
    //         $user = new User();
    //         $user->name = $request->full_name;
    //         $user->email = $request->email;
    //         $user->password = bcrypt($request->password);
    //         $user->company_id = $setting->id;
    //         $user->save();

    //         // Create employee profile
    //         $employee = new EmployeeDetails();
    //         $employee->user_id = $user->id;
    //         $employee->employee_id = $user->id;
    //         $employee->company_id = $setting->id;
    //         $employee->save();

    //         // Add universal search entry
    //         $search = new UniversalSearch();
    //         $search->searchable_id = $user->id;
    //         $search->title = $user->name;
    //         $search->route_name = 'employees.show';
    //         $search->save();

    //         // Attach roles
    //         $adminRole = Role::where('company_id', $setting->id)->where('name', 'admin')->first();
    //         $employeeRole = Role::where('company_id', $setting->id)->where('name', 'employee')->first();
    //         $user->roles()->attach([$adminRole->id, $employeeRole->id]);

    //         // Attach all permissions
    //         $allPermissions = Permission::orderBy('id')->pluck('id');
    //         foreach ($allPermissions as $permissionId) {
    //             $user->permissionTypes()->attach([
    //                 $permissionId => ['permission_type_id' => PermissionType::ALL]
    //             ]);
    //         }

    //         DB::commit();

    //         // Login and return token
    //         Auth::login($user);
    //         $token = $user->createToken('auth_token')->plainTextToken;

    //         return response()->json([
    //             'message' => __('messages.signupSuccess'),
    //             'user' => $user,
    //             'token' => $token
    //         ], 201);

    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         return response()->json([
    //             'message' => 'Error occurred during setup.',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }
    
    public function employerRegister(StoreRequest $request)
{
    $addPermission = user()->permission('add_employees');
    abort_403(!in_array($addPermission, ['all', 'added']));

    DB::beginTransaction();
    try {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->mobile = $request->mobile;
        $user->country_id = $request->country;
        $user->salutation = $request->salutation;
        $user->country_phonecode = $request->country_phonecode;
        $user->gender = $request->gender;
        $user->locale = $request->locale;

        if ($request->has('login')) {
            $user->login = $request->login;
        }

        if ($request->has('email_notifications')) {
            $user->email_notifications = $request->email_notifications ? 1 : 0;
        }

        if ($request->hasFile('image')) {
            Files::deleteFile($user->image, 'avatar');
            $user->image = Files::uploadLocalOrS3($request->image, 'avatar', 300);
        }

        if ($request->has('telegram_user_id')) {
            $user->telegram_user_id = $request->telegram_user_id;
        }

        $user->save();

        $tags = json_decode($request->tags);

        if (!empty($tags)) {
            foreach ($tags as $tag) {
                $skillData = Skill::firstOrCreate(['name' => $tag->value]);

                $skill = new EmployeeSkill();
                $skill->user_id = $user->id;
                $skill->skill_id = $skillData->id;
                $skill->save();
            }
        }

        if ($user->id) {
            $employee = new EmployeeDetails();
            $employee->user_id = $user->id;
            $this->employeeData($request, $employee);
            $employee->save();

            if ($request->custom_fields_data) {
                $employee->updateCustomFieldData($request->custom_fields_data);
            }
        }

        $employeeRole = Role::where('name', 'employee')->first();
        $user->attachRole($employeeRole);

        if ($employeeRole->id != $request->role) {
            $otherRole = Role::where('id', $request->role)->first();
            $user->attachRole($otherRole);
        }

        $user->assignUserRolePermission($request->role);
        $this->logSearchEntry($user->id, $user->name, 'employees.show', 'employee');

        DB::commit();

    } catch (TransportException $e) {
        DB::rollBack();

        return response()->json([
            'status' => 'error',
            'message' => 'SMTP not configured. Visit Settings -> Notification settings. ' . $e->getMessage(),
            'error_type' => 'smtp_error'
        ], 500);
    } catch (\Exception $e) {
        DB::rollBack();
        logger($e->getMessage());

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to create employee. ' . $e->getMessage()
        ], 500);
    }

    return response()->json([
        'status' => 'success',
        'message' => __('messages.recordSaved'),
        'redirect_url' => route('employees.index')
    ]);
}

}
