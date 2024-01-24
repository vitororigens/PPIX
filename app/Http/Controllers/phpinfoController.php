<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class phpinfoController extends Controller
{
    public function dalepuxa(){
        phpinfo();
    }
}
