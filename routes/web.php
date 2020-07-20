<?php
/* Start Admin Panel Routes */

Route::group(['middleware' => ['adminpanellanguage', 'admin']], function() {

	Route::get('admin_panel_language/{lang}', function($lang){
		\Session::put('adminLocale', $lang);
		return redirect()->back();
	})->middleware('adminpanellanguage');

    Route::get("/admin/deposit-companies/", "AdminDepositCompanyController@list_deposit_company")->name('admin-deposit-companies');
    Route::post("/admin/deposit-companies/list-fetch-deposit-company", "AdminDepositCompanyController@list_fetch_deposit_company")->name('admin-list-fetch-deposit-company');
    Route::get("/admin/deposit-companies/add-deposit-company", "AdminDepositCompanyController@add_deposit_company")->name('admin-add-deposit-company');
    Route::post("/admin/deposit-companies/add-deposit-company-post", "AdminDepositCompanyController@add_deposit_company_post")->name('admin-add-deposit-company-post');
    Route::get("/admin/deposit-companies/edit-deposit-company/{deposit_company_id}", "AdminDepositCompanyController@edit_deposit_company")->name('admin-edit-deposit-company');
    Route::post("/admin/deposit-companies/update-deposit-company-post/{deposit_company_id}", "AdminDepositCompanyController@update_deposit_company_post")->name('admin-update-deposit-company-post');
    Route::post("/admin/deposit-companies/change-deposit-company-status", "AdminDepositCompanyController@change_deposit_company_status")->name('change-deposit-company-status');
});

Route::group(["middleware"=>"adminpanellanguage"], function() {

	Route::get('admin_panel_language/{lang}', function($lang){
		\Session::put('adminLocale', $lang);
		return redirect()->back();
	})->middleware('adminpanellanguage');

	Route::get("/admin/login", "AdminController@adminLoginForm")->name("adminlogin");
	Route::post("/admin/check-login", "AdminController@checkUserLogin")->name("checklogin");
	Route::get("/admin/logout", "AdminController@logout")->name("adminlogout");

});

/* End Admin Panel Routes */