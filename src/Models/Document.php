<?php

namespace Models;

class Document {
    
    private $db = null;

    function __construct($db) {
        $this->db = $db;
    }

    public function getAll() {
        return $this->db->exec('SELECT * FROM Documents ORDER BY create_time');
    }

    public function getMultiple($limit, $offset = 0) {
        return $this->db->exec('SELECT * FROM Documents ORDER BY create_time LIMIT '.(int)$offset.','.(int)$limit);
    }

    public function getMultipleCount() {
        $response = $this->db->exec('SELECT COUNT(id) as total_count FROM Documents');

        return $response ? $response[0]['total_count'] : 0;
    }

    public function get($id) {
        $response = $this->db->exec('SELECT * FROM Documents WHERE id = :id', ["id" => (int)$id]);

        return $response ? $response[0] : false;
    }

    public function create($title, $desc, $url, $ext) {
        $this->db->begin();
        $this->db->exec('INSERT INTO Documents (title, description, url, ext, create_time, indexed) 
                         VALUES (:title, :description, :url, :ext, :create_time, :indexed)', 
                         ["title" => $title, "description" => $desc, "url" => $url, "ext" => $ext,
                          "create_time" => date("Y-m-d H:i:s"), "indexed" => 0 ]);
        $id = $this->db->exec('SELECT LAST_INSERT_ID() as id');
        $this->db->commit();

        return $id ? $id[0]["id"] : -1;
    }

    public function insertToken($word, $doc_id) {
        return $this->db->exec('CALL insert_token(:word, :id)', 
                                [ "word" => $word, "id" => (int)$doc_id ]);
    }

    public function setIndexed($id, $indexed) {
        return $this->db->exec('UPDATE Documents SET indexed = :indexed WHERE id = :id',
                                [ "id" => (int)$id, "indexed" => (int)(bool)$indexed ]);
    }

}