<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreRequest;
use App\Models\User;
use App\Models\EmployeeDetails;
use App\Models\Skill;
use App\Models\EmployeeSkill;
use App\Models\Role;
use App\Helper\Files;
use App\Helper\Reply;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Mailer\Exception\TransportException;
use Laravel\Sanctum\HasApiTokens;


class EmployeeApiController extends Controller
{
    public function store(Request $request)
    {
        // Optional: Check if authenticated user has permission
        // $addPermission = auth()->user()->permission('add_employees');
        // abort_403(!in_array($addPermission, ['all', 'added']));
        $addPermission = 'all'; 
        // Inline Validation
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'salutation' => 'nullable|string',
            'mobile' => 'nullable|string',
            'country' => 'required|exists:countries,id',
            'country_phonecode' => 'nullable|string',
            'gender' => 'nullable|in:male,female,other',
            'locale' => 'nullable|string|max:5',
            'role' => 'required|exists:roles,id',
            'joining_date' => 'nullable|date_format:d-m-Y',
            'image' => 'nullable|image|max:2048',
            'tags' => 'nullable|string', // JSON string
        ]);

        DB::beginTransaction();

        try {
            // Create User
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
            $user->company_id = $request->company_id;
            $user->login = $request->login ?? 'enable';
            $user->email_notifications = $request->email_notifications === 'yes' ? 1 : 0;

            // Profile Image
            if ($request->hasFile('image')) {
                Files::deleteFile($user->image, 'avatar');
                $user->image = Files::uploadLocalOrS3($request->file('image'), 'avatar', 300);
            }

            if ($request->filled('telegram_user_id')) {
                $user->telegram_user_id = $request->telegram_user_id;
            }

            $user->save();

            // Tags -> Skills
            if ($request->filled('tags')) {
                $tags = json_decode($request->tags);
                if (!empty($tags)) {
                    foreach ($tags as $tag) {
                        $skillData = Skill::firstOrCreate(['name' => $tag->value]);
                        EmployeeSkill::create([
                            'user_id' => $user->id,
                            'skill_id' => $skillData->id
                        ]);
                    }
                }
            }

            // Employee Details
            $employee = new EmployeeDetails();
            $employee->user_id = $user->id;
            $this->employeeData($request, $employee);
            $employee->save();

            // Custom Fields
            if ($request->filled('custom_fields_data')) {
                $employee->updateCustomFieldData($request->custom_fields_data);
            }

            // Roles
            $employeeRole = Role::where('name', 'employee')->first();
            $user->attachRole($employeeRole);

            if ($employeeRole->id != $request->role) {
                $user->attachRole(Role::find($request->role));
            }

            // Permissions
            $user->assignUserRolePermission($request->role);

            // Log
            // $this->logSearchEntry($user->id, $user->name, 'employees.show', 'employee');

            DB::commit();

            // Sanctum Token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Employee created successfully',
                'user' => $user,
                'token' => $token,
            ], 201);

        } catch (\Symfony\Component\Mailer\Exception\TransportExceptionInterface $e) {
            DB::rollback();
            return response()->json([
                'message' => 'SMTP Error: ' . $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            DB::rollback();
            logger()->error($e);
            return response()->json([
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function employeeData($request, $employee): void
    {
        // Example: Ensure that required fields are present in the request or set defaults
        $employee->employee_id = $request->employee_id ?? 'EMP' . rand(1000, 9999); // Fallback to a random employee ID if missing
        $employee->address = $request->address ?? 'Not Provided'; // Default if address is missing
        $employee->hourly_rate = $request->hourly_rate ?? 0; // Default hourly rate if missing
        $employee->slack_username = $request->slack_username ?? null; // Default to null if missing
        $employee->department_id = $request->department ?? null; // Default to null if department is missing
        $employee->designation_id = $request->designation ?? null; // Default to null if designation is missing
        $employee->company_address_id = $request->company_address ?? null; // Default to null if company address is missing
        $employee->reporting_to = $request->reporting_to ?? null; // Default to null if reporting manager is missing
        $employee->about_me = $request->about_me ?? 'No information provided'; // Default text if about_me is missing

        // Safely format dates using helper, only if they are provided
        $employee->joining_date = $request->filled('joining_date') ? companyToYmd($request->joining_date) : null;
        $employee->date_of_birth = $request->filled('date_of_birth') ? companyToYmd($request->date_of_birth) : null;
        $employee->probation_end_date = $request->filled('probation_end_date') ? companyToYmd($request->probation_end_date) : null;
        $employee->notice_period_start_date = $request->filled('notice_period_start_date') ? companyToYmd($request->notice_period_start_date) : null;
        $employee->notice_period_end_date = $request->filled('notice_period_end_date') ? companyToYmd($request->notice_period_end_date) : null;
        $employee->marriage_anniversary_date = $request->filled('marriage_anniversary_date') ? companyToYmd($request->marriage_anniversary_date) : null;
        $employee->internship_end_date = $request->filled('internship_end_date') ? companyToYmd($request->internship_end_date) : null;
        $employee->contract_end_date = $request->filled('contract_end_date') ? companyToYmd($request->contract_end_date) : null;

        $employee->marital_status = $request->marital_status ?? 'Single'; // Default to 'Single' if missing
        $employee->employment_type = $request->employment_type ?? 'Full-time'; // Default to 'Full-time' if missing

        // Default calendar view
        $employee->calendar_view = 'task,events,holiday,tickets,leaves,follow_ups';
    }
   

}