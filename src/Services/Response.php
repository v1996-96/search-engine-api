<?php

namespace Services;

final class Response extends \Prefab {

    public function send($data, $code = 200, $finalize = true) {
        http_response_code($code);
		header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        if ($finalize) die;
    }

}