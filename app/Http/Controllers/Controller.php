<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function renderJSON($data = [], $ret = 0, $msg = 'ok') {
        $res = [
            'ret' => $ret,
            'msg' => $msg,
            'data' => []
        ];
        if ($data || $data === 0) {
            $res['data'] = $data;
        }
        return response()->json($res);
    }

    public function renderErrorJSON($msg = '', $data = [], $code = -1) {
        return $this->renderJSON($data, $code, $msg);
    }

}
