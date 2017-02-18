<?php

namespace Controllers;

class Search extends BaseController {

    function __construct($f3) {
        $this->initialize($f3);
    }

    public function get() {
        if (!isset($_GET['query']))
            $this->error->throwError(4001);

        if (strlen($_GET['query']) < 3)
            $this->error->throwError(4005);

        $processor = \Services\TextProcessor::instance();
        $processor->run($_GET['query']);

        $queryTokens = array();
        foreach ($processor->getGroupedResult() as $word => $count) {
            $queryTokens[] = $count . "_" . $word;
        }

        if (count($queryTokens) > 0) {
            $model = new \Models\Query($this->db);
            $response = $model->searchByVector($_GET['query'], implode("||", $queryTokens), count($queryTokens));
        } else {
            $response = array();
        }

        $this->response->send(array(
            "list" => $response
        ));
    }

}