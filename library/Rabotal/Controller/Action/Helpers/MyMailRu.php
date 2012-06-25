<?php

class Rabotal_Controller_Action_Helpers_MyMailRu {
    static public function signServerServer(array $request_params, $secret_key) {
        ksort($request_params);
        $params = '';
        foreach ($request_params as $key => $value) {
            $params .= "$key=$value";
        }
        return md5($params . $secret_key);
    }
}
