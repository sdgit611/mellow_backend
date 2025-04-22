<?php

namespace App\Http\Controllers;
use App\Http\Requests\SkillRequest;
use App\Models\Company;
use App\Models\EmployeeSkill;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


use Illuminate\Http\Request;

class SkillController extends Controller
{
    public function create()
    {
        return view('profile-settings.ajax.skill.create');
    }
    
    public function store(SkillRequest $request)
    {
        $companies = Company::first();

        EmployeeSkill::insert([
            'company_id' =>  $companies->id,
            'user_id' => Auth::id(),
            'name' => $request->technology,
            'total' => $request->total,
        ]);
        
        
        $skill = User::with('skill')->find(Auth::id());

        $view = view('profile-settings.ajax.skill.skill-list', compact('skill'))->render();

        return response()->json([
            'status' => 'success',
            'message' => __('messages.recordSaved'),
            'view' => $view
        ]);
    }
    
    public function edit($id)
    {
        $skill = EmployeeSkill::where('id', $id)->first();
        return view('profile-settings.ajax.skill.edit', compact('skill'));
    }
    

    public function update(Request $request)
    {
        
        $education = EmployeeSkill::where('id', $request->id)->update([
            'name' => $request->technology,
            'total' => $request->total,
        ]);
        
        $skill = User::with('skill')->find(Auth::id())->skill;

        $view = view('profile-settings.ajax.skill.skill-list', compact('skill'))->render();

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
