<?php

namespace App\Http\Controllers;

use Modules\Recruit\Entities\RecruitJobApplication;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\EmployeeDetails;
use App\Models\Skill;
use App\Models\EmployeeSkill;
use App\Models\Role;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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

        if ($updated) {
            $application = RecruitJobApplication::find($request->id);

            $user = new User();
            $user->name = $application->full_name;
            $user->email = $application->email;
            $user->mobile = $application->phone;
            $user->password = bcrypt('default@123');

            if ($request->has('login')) {
                $user->login = "enable";
            }

            $req = 'yes';
            $user->email_notifications = ($req === 'yes') ? 1 : 0;

            $user->save();

            $token = Password::broker()->createToken($user);
            $resetUrl = url('/reset-password/' . $token . '?email=' . urlencode($user->email));

            $htmlContent = "
                <p>🎉 <strong>Congratulations!</strong> Your account has been created successfully.</p>
                <p>You can reset your password and start using our service by clicking the link below:</p>
                <p><a href='{$resetUrl}' target='_blank'>{$resetUrl}</a></p>
            ";

            Mail::send([], [], function ($message) use ($user, $htmlContent) {
                $message->to($user->email)
                    ->subject('Welcome to Our Platform - Set Your Password')
                    ->html($htmlContent); 
            });
            // Add Skills
            $skillsJson = json_encode([
                ["value" => "Vue.js"],
                ["value" => "Laravel"],
                ["value" => "HTML"],
            ]);
            $tags = json_decode($skillsJson);

            foreach ($tags as $tag) {
                $skillName = $tag->value;
                $skillData = Skill::whereRaw('LOWER(name) = ?', [strtolower($skillName)])->first();

                if ($skillData) {
                    $skill = new EmployeeSkill();
                    $skill->user_id = $user->id;
                    $skill->skill_id = $skillData->id;
                    $skill->save();
                }
            }

            // Save Employee Details
            $employee = new EmployeeDetails();
            $employee->user_id = $user->id;
            $employee->save();

            // Assign Role
            $employeeRole = Role::where('name', 'employee')->first();
            $user->attachRole($employeeRole);

            if (!empty($application->role_id) && $application->role_id != $employeeRole->id) {
                $otherRole = Role::where('id', $application->role_id)->first();
                if ($otherRole) {
                    $user->attachRole($otherRole);
                }
            }

            $user->assignUserRolePermission($application->role_id ?? $employeeRole->id);
        }

        DB::commit();
        return response()->json([
            'success' => $updated ? true : false,
            'message' => $updated ? 'Payment info updated successfully.' : 'No record found or update failed.',
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Transaction failed: ' . $e->getMessage(),
        ], 500);
    }
}

}
