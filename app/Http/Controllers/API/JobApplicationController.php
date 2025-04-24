<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Recruit\Entities\RecruitJobApplication;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class JobApplicationController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        try {
            // $validator = Validator::make($request->all(), [
            //     'job_id' => 'required|integer',
            //     'full_name' => 'required|string|max:255',
            //     'email' => 'required|email|max:255',
            //     'phone' => 'required|string|max:20',
            //     'gender' => 'nullable|in:male,female,other',
            //     'date_of_birth' => 'nullable|date_format:d-m-Y',
            //     'source' => 'required|integer',
            //     'cover_letter' => 'nullable|string',
            //     'location_id' => 'nullable|integer',
            //     'total_experience' => 'nullable|numeric',
            //     'current_location' => 'nullable|string|max:255',
            //     'current_ctc' => 'nullable|numeric',
            //     'currenct_ctc_rate' => 'nullable|string|max:50',
            //     'expected_ctc' => 'nullable|numeric',
            //     'expected_ctc_rate' => 'nullable|string|max:50',
            //     'notice_period' => 'nullable|string|max:100',
            //     'status_id' => 'nullable|integer',
            //     // Removed file validation for now
            //     // 'photo' => 'nullable|image|max:2048',
            //     // 'resume' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            // ]);

            // if ($validator->fails()) {
            //     return response()->json([
            //         'success' => false,
            //         'errors' => $validator->errors()
            //     ], 422);
            // }

            $jobApp = new RecruitJobApplication();
            $jobApp->recruit_job_id = $request->job_id;
            $jobApp->full_name = $request->full_name;
            $jobApp->email = $request->email;
            $jobApp->phone = $request->phone;
            $jobApp->gender = $request->gender;

            if (!empty($request->date_of_birth)) {
                $jobApp->date_of_birth = Carbon::createFromFormat('d-m-Y', $request->date_of_birth)->format('Y-m-d');
            }

            $jobApp->application_source_id = $request->source;
            $jobApp->cover_letter = $request->cover_letter;
            $jobApp->location_id = $request->location_id;
            $jobApp->total_experience = $request->total_experience;
            $jobApp->current_location = $request->current_location;
            $jobApp->current_ctc = $request->current_ctc;
            $jobApp->currenct_ctc_rate = $request->currenct_ctc_rate;
            $jobApp->expected_ctc = $request->expected_ctc;
            $jobApp->expected_ctc_rate = $request->expected_ctc_rate;
            $jobApp->notice_period = $request->notice_period;
            $jobApp->recruit_application_status_id = $request->status_id;
            $jobApp->application_sources = 'api';
            $jobApp->column_priority = 0;
            $jobApp->company_id = 1;
            $jobApp->save();

            return response()->json([
                'success' => true,
                'message' => 'Job application submitted successfully.'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
