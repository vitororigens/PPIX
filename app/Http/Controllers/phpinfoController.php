<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class phpinfoController extends Controller
{
    public function dalepuxa(){
        phpinfo();
    }

    public function teste(){

        return $resposta = [
            'TESTE' => 'ONLINE',
            'STATUS' => '200'
        ];
        
    }
}
