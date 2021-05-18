<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;


class CustomValidateServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
    	Validator::extend('space', function ($attribute, $value, $parameters, $validator) {
    		if( mb_ereg_match("^(\s|　)+$", $value) ){
    			return false;
    		}else{
    			return true;
    		}
    	});
    }
}
