<?php
/**
 * Created by PhpStorm.
 * User: nightkid
 * Date: 2018/9/11
 * Time: 下午9:38
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function searchByToken(Request $request){
        $token = $request->input("token", '');
        if (empty($token)) {
            return $this->renderErrorJSON();
        }


    }
}