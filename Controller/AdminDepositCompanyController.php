<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User, App\UserGovernmentId, App\Country, App\DepositCompany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use File, URL, Hash;
use Datatables;

class AdminDepositCompanyController extends Controller
{
    public function list_deposit_company(Request $request)
    {
        return view("admin.views.deposit-companies.list_deposit_company");
    }
    public function list_fetch_deposit_company(Request $request)
    {
        $deposit_companies = DB::table('deposit_companies')
            ->join('deposit_companies_statuses', 'deposit_companies.id', '=', 'deposit_companies_statuses.deposit_company_id')
            ->join('countries', 'deposit_companies_statuses.country_id', '=', 'countries.id')
            ->select('deposit_companies.*', 'deposit_companies_statuses.status as dcs_status', 
                DB::raw('GROUP_CONCAT(deposit_companies_statuses.country_id) as country_ids, GROUP_CONCAT(countries.country_name SEPARATOR ", ") as country_names'))
            ->groupBy("deposit_companies.id")
            ->get();

        return Datatables::of($deposit_companies)
            ->addIndexColumn()
            ->editColumn("logo_file_storage_location", function($deposit_companies) {
                return '<img src="'.URL::to('/')."/img/".$deposit_companies->logo_file_storage_location.'" style="width: 100px; height: 100px;" onerror=this.onerror=null;this.src="'.URL::to('/').'/public/uploads/default/100_no_img.jpg">';
            })
            ->editColumn("action", function($deposit_companies) {
                return '<div class="btn-group">'.
                    '<button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.__('message_lang.BTN_CHANGE_STATUS').'</button>'.
                    '<div class="dropdown-menu dropdown-menu-right">'.
                    
                    '<a class="dropdown-item badge-success" href="javascript:void(0);" onclick="change_status(this);" data-status="'.config('constants.deposit_company_status.ACTIVE').'" data-id="'.$deposit_companies->id.'">'.config('constants.deposit_company_status.ACTIVE').'</a>'.

                    '<a class="dropdown-item badge-secondary" href="javascript:void(0);" onclick="change_status(this);" data-status="'.config('constants.deposit_company_status.INACTIVE').'" data-id="'.$deposit_companies->id.'">'.config('constants.deposit_company_status.INACTIVE').'</a>'.
                    
                    '</div>'.
                    
                    '</div> &nbsp;'.

                    '<a href="'.route('admin-edit-deposit-company', ['deposit_company_id' => base64_encode($deposit_companies->id)]).'" class="btn btn-info" role="button">'.__('message_lang.BTN_EDIT').'</a> &nbsp;'.
                    
                    '';
            })
            ->editColumn("dcs_status", function($deposit_companies) {
                if($deposit_companies->dcs_status == config('constants.deposit_company_status.ACTIVE'))
                {
                    return '<span class="badge badge-success">'.config('constants.deposit_company_status.ACTIVE').'</span>';
                }
                else if($deposit_companies->dcs_status == config('constants.deposit_company_status.INACTIVE'))
                {
                    return '<span class="badge badge-secondary">'.config('constants.deposit_company_status.INACTIVE').'</span>';
                }
                else
                {
                    return '-';
                }
            })
            ->rawColumns(["action", "dcs_status", "logo_file_storage_location"])
            ->make(true);
    }
    public function add_deposit_company(Request $request)
    {
        $countries = Country::get();
        return view("admin.views.deposit-companies.crud_deposit_company", ["countries" => $countries]);
    }
    public function add_deposit_company_post(Request $request)
    {
        $this->validate(request(), [
            'country_id' => ['required', 'array', 'min:1'],
            'company_name' => ['required', 'string', 'max:255'],
            'max_rec_amt' => ['required','numeric'],

            'logo_file_storage_location' => 'required',
            'logo_file_storage_location' => 'max:2000',
        ],[
            'country_id.required' => 'Please select country.',
            'logo_file_storage_location.required' => 'Please upload a deposit company logo.',
            'logo_file_storage_location.max' => 'The deposit company logo max file size should be 2MB.',
        ]);

        if($request->hasFile('logo_file_storage_location') && $request->file('logo_file_storage_location')){  }
        else
        {
            return redirect()->back()->with('error', __('message_lang.PLS_SEL_DPST_CO_LOGO'))->withInput();
        }

        $country_id = $request->country_id;
        $company_name = $request->company_name;
        $max_rec_amt = $request->max_rec_amt;

        $crud_data = array(
            'company_name' => $company_name,
            'max_rec_amt' => $max_rec_amt,
            'status' => 'Active',
            'created_at' => date('Y-m-d H:i:s'),
        );

        $last_inserted_id = DB::table('deposit_companies')->insertGetId($crud_data);

        if($last_inserted_id > 0)
        {
            for($i=0; $i < count($country_id); $i++)
            {
                $crud_data = array(
                    'country_id' => $country_id[$i],
                    'deposit_company_id' => $last_inserted_id,
                    'status' => 'Active',
                    'created_at' => date('Y-m-d H:i:s')
                );

                DB::table('deposit_companies_statuses')->insert($crud_data);
            }

            $url = '';
            
            if($request->hasFile('logo_file_storage_location') && $request->file('logo_file_storage_location'))
            {
                // Start upload profile image

                $file = $request->file('logo_file_storage_location');
                $filenameWithExtension = $request->file('logo_file_storage_location')->getClientOriginalName();
                $filename = pathinfo($filenameWithExtension, PATHINFO_FILENAME);
                $extension = $request->file('logo_file_storage_location')->getClientOriginalExtension();
                $filenameToStore = time()."_".$filename.'.'.$extension;

                $folder_to_upload = public_path().'/img/';

                if (!File::exists($folder_to_upload)) {
                    File::makeDirectory($folder_to_upload, 0777, true, true);
                }

                $file->move("img/", $filenameToStore);
                $url = URL::to('/')."/img/".$filenameToStore;

                $crud_data_deposit_company = array(
                    'logo_file_storage_location' => $filenameToStore,
                );

                $res_user_update = DB::table('deposit_companies')->where('id', $last_inserted_id)->update($crud_data_deposit_company);

                // End upload profile image
            }

            return redirect()->route('admin-deposit-companies')->with('success', __('message_lang.DPST_CO_ADDED_SUCCESSFULLY'));
        }
        else
        {
            return redirect()->back()->with('error', __('message_lang.FAILED_TO_ADD_DPST_CO'))->withInput();
        }
    }
    public function edit_deposit_company(Request $request, $deposit_company_id)
    {
        $countries = Country::get();

        $deposit_company_id = base64_decode($deposit_company_id);

        $deposit_company = DB::table('deposit_companies')
            ->join('deposit_companies_statuses', 'deposit_companies.id', '=', 'deposit_companies_statuses.deposit_company_id')
            ->join('countries', 'deposit_companies_statuses.country_id', '=', 'countries.id')
            ->where("deposit_companies.id", $deposit_company_id)
            ->select('deposit_companies.*', 
                DB::raw('GROUP_CONCAT(deposit_companies_statuses.country_id) as country_ids, GROUP_CONCAT(countries.country_name SEPARATOR ", ") as country_names'))
            ->groupBy("deposit_companies.id")
            ->first();

        // echo "<pre>";
        // print_r($deposit_company);
        // exit;

        return view("admin.views.deposit-companies.crud_deposit_company", ["countries" => $countries, "deposit_company" => $deposit_company, "deposit_company_id" => $deposit_company_id]);
    }
    public function update_deposit_company_post(Request $request, $id)
    {
        // _pre($request->all());
        // exit;

        $this->validate(request(), [
            'country_id' => ['required', 'array', 'min:1'],
            'company_name' => ['required', 'string', 'max:255'],
            'max_rec_amt' => ['required','numeric'],
        ],[
            'country_id.required' => 'Please select country.'
        ]);
        
        if($request->hasFile('logo_file_storage_location') && $request->file('logo_file_storage_location'))
        {
            $this->validate(request(), [
                'logo_file_storage_location' => 'max:2000',
            ],[
                'logo_file_storage_location.max' => 'The deposit company logo max file size should be 2MB.',
            ]);
        }
        
        $country_id = $request->country_id;
        $company_name = $request->company_name;
        $max_rec_amt = $request->max_rec_amt;

        $crud_data = array(
            'company_name' => $company_name,
            'max_rec_amt' => $max_rec_amt,
            'updated_at' => date('Y-m-d H:i:s'),
        );

        $deposit_company_update_response = DB::table('deposit_companies')->where('id', $id)->limit(1)->update($crud_data);

        if($deposit_company_update_response)
        {   
            if($request->hasFile('logo_file_storage_location') && $request->file('logo_file_storage_location'))
            {
                // Start upload profile image

                $file = $request->file('logo_file_storage_location');
                $filenameWithExtension = $request->file('logo_file_storage_location')->getClientOriginalName();
                $filename = pathinfo($filenameWithExtension, PATHINFO_FILENAME);
                $extension = $request->file('logo_file_storage_location')->getClientOriginalExtension();
                $filenameToStore = time()."_".$filename.'.'.$extension;

                $folder_to_upload = public_path().'/img/';

                if (!File::exists($folder_to_upload)) {
                    File::makeDirectory($folder_to_upload, 0777, true, true);
                }

                $file->move("img/", $filenameToStore);
                $url = URL::to('/')."/img/".$filenameToStore;

                // $deposit_company = DepositCompany::find($id);
                // GENERATES ERROR IN DepositCompany MODEL get_account_number_attribute

                $deposit_company = DB::table('deposit_companies')->where('id', $id)->first();

                if($deposit_company && !empty($deposit_company) && !empty($deposit_company->logo_file_storage_location))
                {
                    $file_path = public_path().'/img/'.$deposit_company->logo_file_storage_location;
                    @unlink($file_path);
                }

                $crud_data_deposit_company = array(
                    'logo_file_storage_location' => $filenameToStore,
                );

                $res_user_update = DB::table('deposit_companies')->where('id', $id)->update($crud_data_deposit_company);

                // End upload profile image
            }

            DB::table('deposit_companies_statuses')->where('deposit_company_id', $id)->delete();

            for($i=0; $i < count($country_id); $i++)
            {
                $crud_data = array(
                    'country_id' => $country_id[$i],
                    'deposit_company_id' => $id,
                    'status' => 'Active',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                );

                DB::table('deposit_companies_statuses')->insert($crud_data);
            }

            return redirect()->route('admin-deposit-companies')->with('success', __('message_lang.DPST_CO_UPDATED_SUCCESSFULLY'))->withInput();
        }
        else
        {
            return redirect()->back()->with('error', __('message_lang.FAILED_TO_UPDATE_DPST_CO'))->withInput();
        }
    }

    public function change_deposit_company_status(Request $request)
    {
        if(!$request->ajax())
        {
            exit('No direct script access allowed');
        }

        if(!empty($request->all()))
        {
            $status = $request->status;
            $id = $request->id;

            $crud_data = array(
                'status' => $status
            );

            $update_result = DB::table('deposit_companies_statuses')
                ->where('deposit_company_id', $id)
                ->update($crud_data);

            if($update_result)
            {
                // _set_flashdata('S', 'Status Changed Successfully');
                echo json_encode(array("status" => "success"));
                exit;
            }
            else
            {
                echo json_encode(array("status" => "failed"));
                exit;
            }
        }
        else
        {
            echo json_encode(array("status" => "failed"));
            exit;
        }
    }
}
