<?php

namespace Models;

class Query {

    private $db = null;

    function __construct($db) {
        $this->db = $db;
    }

    public function searchByVector($queryText, $queryTokens, $queryTokensCount) {
        return $this->db->exec("CALL search_by_vector(:queryText, :queryTokens, :queryTokensCount)", 
                                array(
                                    "queryText" => $queryText,
                                    "queryTokens" => $queryTokens,
                                    "queryTokensCount" => $queryTokensCount
                                ));
    }
}