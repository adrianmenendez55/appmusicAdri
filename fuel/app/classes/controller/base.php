<?php

use Firebase\JWT\JWT;

class Controller_Base extends Controller_Rest
{
	protected function respuesta($code, $message, $data = [])
    {
        $json = $this->response(array(
            'code' => $code,
            'message' =>  $message,
            'data' => $data
        ));
        return $json;
    }
}