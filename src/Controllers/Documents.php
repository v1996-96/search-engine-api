<?php

namespace Controllers;

class Documents extends BaseController {

    const FILES_PATH = "files";
    const MB = 1048576;

    function __construct($f3) {
        $this->initialize($f3);
    }

    public function get() {
        $model = new \Models\Document($this->db);
        
        if (isset($_GET['all']) && (int)$_GET['all'] === 1) {
            $data = $model->getAll();
            $this->response->send(array(
                "list" => $data
            ));
        }

        if (!isset($_GET['limit']))
            $this->error->throwError(4001);

        $offset = isset($_GET['offset']) ? $_GET['offset'] : 0;
        $data = $model->getMultiple($_GET['limit'], $offset);
        $total_count = $model->getMultipleCount();

        $this->response->send(array(
            "list" => $data,
            "offset" => $offset,
            "limit" => $_GET['limit'],
            "total_count" => $total_count
        ));
    }

    public function post() {
        if (!isset($_POST['title']) ||
            !isset($_POST['description']))
            $this->error->throwError(4001);
        
        if (!isset($_FILES['file']))
            $this->error->throwError(4002);

        if (isset($_FILES['file']) &&
            $_FILES['file']['size'] == 0)
            $this->error->throwError(4002);

        $size = $this->f3->exists("FILES_MAX_SIZE") ? $this->f3->get("FILES_MAX_SIZE") : 3;
        if ($_FILES['file']['size'] > $size*self::MB)
            $this->error->throwError(4004);

        $extensions = array("pdf", "txt");
        $fileExt = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION); 
        if (!in_array($fileExt, $extensions))
            $this->error->throwError(4003);

        $newFile = "";
        $counter = 0;
        do {
            $newFile = self::FILES_PATH . "/" . $this->random(10) . "." . $fileExt;
            $counter++;
        } while(file_exists($newFile) && $counter < 10);
        if ($counter >= 10)
            $this->error->throwError(5001);

        $loaded = @move_uploaded_file($_FILES['file']['tmp_name'], $newFile);

        if ($loaded === false)
            $this->error->throwError(5001);

        $model = new \Models\Document($this->db);
        $id = $model->create($_POST['title'], $_POST['description'], $newFile, $fileExt);
        if ($id === -1)
            $this->error->throwError(5002);
        
        $this->response->send(array(
            "success" => 1, 
            "id" => (int)$id 
        ));
    }

    private function random($length) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}