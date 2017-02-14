<?php

namespace Services;

final class Error extends \Prefab {

    private $errorList = null;
    private $lang = null;

    function __construct($errFilePath, $lang) {
        if (file_exists($errFilePath)) {
            $file = @file_get_contents($errFilePath);
            if ($file !== false) {
                $this->errorList = json_decode($file, true);
                $this->lang = $lang;
            }
        }
    }

    public function throw($errcode, $finalize = true) {
        if (!is_null($this->errorList) &&
            !is_array($errcode) &&
            isset($this->errorList[ $errcode ]) &&
            isset($this->errorList[ $errcode ]["message"][ $this->lang ])) {

            $data = [
                "errcode" => $errcode,
                "status" => $this->errorList[ $errcode ]["status"],
                "message" => $this->errorList[ $errcode ]["message"][ $this->lang ]
            ];
            $code = $this->errorList[ $errcode ]["status"];
        } elseif(is_array($errcode) &&
                 isset($errcode["errcode"]) &&
                 isset($errcode["status"]) &&
                 isset($errcode["message"])) {
                    
            $data = [
                "errcode" => $errcode["errcode"],
                "status" => $errcode["status"],
                "message" => $errcode["message"]
            ];
            $code = $errcode["status"];
        } else {

            $data = [
                "errcode" => $errcode,
                "status" => 500,
                "message" => "Unknown exception"
            ];
            $code = 500;
        }

        Response::instance()->send($data, $code, $finalize);
    }

}