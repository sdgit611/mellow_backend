<?php

namespace App\Http\Controllers;

use App\Enums\Salutation;
use App\Helper\Reply;
use App\Models\EmergencyContact;
use App\Models\BankDetail;
use App\Models\EmployeeDetails;
use App\Models\Education;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class ProfileSettingController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.profileSettings';
        $this->activeSettingMenu = 'profile_settings';
    }

    public function index()
    {
        $response = Http::get(env('API_APP_URL') . '/developer-kyc?developerId=311');

        $data = $response->json();

        $userId = Auth::id();

        if (!empty($data['status']) && !empty($data['kyc']['developer_details'][0])) 
        {
            
            $developer = $data['kyc']['developer_details'][0];
            $project = $data['kyc']['developer_project_details'];


            $userDetails = User::updateOrCreate(
                ['id' => $userId],
                [
                    'mobile' => $developer['phone'] ?? null,
                ]
            );

            // Update or Create EmployeeDetails
            $employeeDetails = EmployeeDetails::updateOrCreate(
                ['user_id' => $userId],
                [
                    'developer_id' => $developer['dev_id'] ?? null,
                    'job' => $developer['job'] ?? null,
                    'total_hours' => $developer['total_hours'] ?? null,
                    'hourly_rate' => $developer['perhr'] ?? null,
                    'rating' => $developer['rating'] ?? null,
                    'about_me' => $developer['description'] ?? null,
                    'skills_description' => $developer['skills'] ?? null,
                    'completed_job' => $developer['completed_job'] ?? null,
                ]
            );

            // Update or Create BankDetail
            BankDetail::updateOrCreate(
                ['employee_details_id' => $employeeDetails->id],
                [
                    'branch_name' => $developer['branch_name'] ?? null,
                    'account_name' => $developer['acct_name'] ?? null,
                    'account_number' => $developer['account_number'] ?? null,
                    'ifsc_code' => $developer['ifc_code'] ?? null,
                    'micr_number' => $developer['micr_number'] ?? null,
                    'passbook' => $developer['passbook'] ?? null,
                    'account_type' => $developer['account_Type'] ?? null,
                ]
            );

            // Update or Create Education
            Education::updateOrCreate(
                ['employee_details_id' => $employeeDetails->id],
                [
                    'collage_name' => $developer['clg_name'] ?? null,
                    'degree' => $developer['degree'] ?? null,
                    'percentage' => $developer['percentage'] ?? null,
                    'passing_year' => $developer['passing_year'] ?? null,
                ]
            );

            if($developer['skills'] != null || $developer['skills'] != '')
            {
                $skills = explode(",", $developer['skills']);

                foreach($skills as $val)
                {
                    $skill = Skill::where('name', $val);
                    if($skill->count() != 0)
                    {
                        $skill = $skill->first();
                        EmployeeSkill::updateOrCreate(
                            ['company_id' => $employeeDetails->company_id , 'user_id' => $userId, 'skill_id' =>  $skill->id],
                            [
                                'company_id' => $developer['clg_name'] ?? null,
                                'user_id ' => Auth::id(),
                                'skill_id' => $skill->id,
                                'total' => $developer['total'] ?? null,
                            ]
                        );
                    }
                }
            }

            if($project != null || $project != '')
            {

                foreach($project as $val)
                {
                    // Update or Create Project Detail
                    projectDetail::updateOrCreate(
                        ['employee_details_id' => $employeeDetails->id, 'pro_id' =>  $developer['dev_id'] ],
                        [
                            'screenshot_image' => $val['screenshot_image'] ?? null,
                            'project_link' => $val['project_link'] ?? null,
                        ]
                    );
                }
            }

        }
        
        $tab = request('tab');

        if (session()->has('clientContact') && session('clientContact')) {
            $this->user = User::findOrFail(session('clientContact')->client_id);
        }else{
            $this->user = User::findOrFail(user()->id);
        }

        $viewDocumentPermission = user()->permission('view_documents');
        $viewClientDocumentPermission = user()->permission('view_client_document');

        $this->countries = countries();
        $this->salutations = Salutation::cases();

        $this->user->load('educations');


        switch ($tab) {

        case 'emergency-contacts':
            $this->contacts = EmergencyContact::where('user_id', user()->id)->get();
            $this->view = 'profile-settings.ajax.emergency-contacts';
            break;

        case 'documents':
            if (in_array('client', user_roles())) {
                abort_403(($viewClientDocumentPermission == 'none'));
                $this->view = 'profile-settings.ajax.client.index';
            }
            else {
                abort_403(($viewDocumentPermission == 'none'));
                $this->view = 'profile-settings.ajax.employee.index';
            }

        case 'education':
            $this->educations = $this->user->educations;
            $this->view = 'profile-settings.ajax.education.index';
            break;
        
        case 'bank_details':
            $this->view = 'profile-settings.ajax.bank_details.index';
            break;
        
        case 'skill':
            $this->skill = $this->user->skill;
            $this->view = 'profile-settings.ajax.skill.index';
            break;

        default:
            $this->view = 'profile-settings.ajax.profile';
            break;
        }

        $this->activeTab = $tab ?: 'profile';

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();
            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle, 'activeTab' => $this->activeTab]);
        }

        return view('profile-settings.index', $this->data);
    }

}
