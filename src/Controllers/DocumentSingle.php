<?php

namespace Controllers;

class DocumentSingle extends BaseController {

    function __construct($f3) {
        $this->initialize($f3);
    }

    public function post() {
        if (!$this->f3->exists('PARAMS.id'))
            $this->error->throw(4041);

        $id = (int)$this->f3->get('PARAMS.id');
        $model = new \Models\Document($this->db);
        $document = $model->get($id);

        if ($document === false || is_null($document))
            $this->error->throw(4041);

        set_time_limit(0);

        // There we get file contents and process words to get words base form
        $file = \Services\FileReader::instance($document['url'], $document['ext']);
        $processor = \Services\TextProcessor::instance();
        $processor->run($file->getText()); unset($file);
        
        // Saving words base form and the link between doc and word to DB
        foreach ($processor->getResult() as $token) {
            $model->insertToken($token, $id);
        }

        // Updating indexed status
        $model->setIndexed($id, true);

        $this->response->send([
            "success" => 1, 
            "id" => (int)$id 
        ]);
    }

}