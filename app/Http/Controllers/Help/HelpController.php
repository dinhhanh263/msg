<?php

namespace App\Http\Controllers\Help;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Library\CommonFunction;

class HelpController extends Controller
{
	public function __construct()
	{
		// ログインチェック
		CommonFunction::checkLogin();
	}

	public function index() {
		return view('help.index');
	}
}
