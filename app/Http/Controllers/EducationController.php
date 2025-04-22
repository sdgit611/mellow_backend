<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\EducationStoreRequest;
use App\Models\Education;
use App\Models\User;
use App\Models\EmployeeDetails;
use Illuminate\Support\Facades\Auth;

class EducationController extends Controller
{
    public function create()
    {
        return view('profile-settings.ajax.education.create');
    }
    
    public function store(EducationStoreRequest $request)
    {
        
        $employeeDetails = EmployeeDetails::where('user_id', Auth::id())->first();
        
        $education = new Education();
        $education->employee_details_id = $employeeDetails->id;
        $education->user_id = Auth::id();
        $education->degree = $request->degree;
        $education->collage_name = $request->institution;
        $education->passing_year = $request->year;
        $education->percentage = $request->percentage;
        $education->save();
        
        $educations = User::with('educations')->find(Auth::id())->educations;

        $view = view('profile-settings.ajax.education.education-list', compact('educations'))->render();

        return response()->json([
            'status' => 'success',
            'message' => __('messages.recordSaved'),
            'view' => $view
        ]);
    }
    
    public function edit($id)
    {
        $education = Education::where('id', $id)->first();
        return view('profile-settings.ajax.education.edit', compact('education'));
    }
    

    public function update(EducationStoreRequest $request)
    {
        
        $education = Education::where('id', $request->id)->update([
            'degree' => $request->degree,
            'collage_name' => $request->institution,
            'passing_year' => $request->year,
            'percentage' => $request->percentage,
        ]);
        
        $educations = User::with('educations')->find(Auth::id())->educations;

        $view = view('profile-settings.ajax.education.education-list', compact('educations'))->render();

        return response()->json([
            'status' => 'success',
            'message' => __('messages.recordSaved'),
            'view' => $view
        ]);
    }

    public function destroy($id)
    {
        $education = Education::findOrFail($id);
        $education->delete();
    
        return response()->json([
            'status' => 'success',
            'message' => __('messages.deleteSuccess')
        ]);
    }
}
