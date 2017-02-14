<?php

namespace Controllers;

abstract class BaseController {

    protected $f3 = null;
    protected $db = null;
    protected $lang = null;
    protected $error = null;
    protected $response = null;

    protected function initialize($f3) {
        $this->f3 = $f3;
        
        if ($f3->exists('DB_HOST') && 
            $f3->exists('DB_NAME') && 
            $f3->exists('DB_USER') && 
            $f3->exists('DB_PWD')) {
            $this->db = new \DB\SQL(
                'mysql:host='.$f3->get('DB_HOST').';port=3306;dbname='.$f3->get('DB_NAME'),
                $f3->get('DB_USER'), $f3->get('DB_PWD')
            );
        } else throw new \Exception("DB connection credentials were not provided");

        $this->lang = $f3->exists('LANG') ? $f3->get('LANG') : "en";

        $this->error = \Services\Error::instance();
        $this->response = \Services\Response::instance();
    }

}