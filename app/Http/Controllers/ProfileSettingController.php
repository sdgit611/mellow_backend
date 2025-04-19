<?php

namespace App\Http\Controllers;

use App\Enums\Salutation;
use App\Helper\Reply;
use App\Models\EmergencyContact;
use App\Models\User;
use Illuminate\Support\Facades\Http;

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
       
        $response = Http::get('http://127.0.0.1:8000/api/developer-kyc?developerId=311');

        $data = $response->json();

        if($data['status'])
        {
           return $data['kyc']["developer_details"][0] ["dev_id"];
        }
        // var_dump( $data['kyc']["developer_details"][0] ["dev_id"]);
        return $data;
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
