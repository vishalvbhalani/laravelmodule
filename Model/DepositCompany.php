<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class DepositCompany extends Model
{
	protected $fillable = [
        'status'
    ];
    
    protected $appends = array('account_number');

    public function get_account_number_attribute($user_id, $deposit_company_id)
    {
		$user_deposit_accounts_info = DB::table('user_deposit_accounts')
			->where('user_id', $user_id)
			->where('deposit_company_id', $deposit_company_id)
			->where('verification_code', '')
			->first();

		if($user_deposit_accounts_info)
		{
			$account_number = $user_deposit_accounts_info->account_number;
		}
		else
		{
			$account_number = "";
		}

        return $account_number;
    }
}