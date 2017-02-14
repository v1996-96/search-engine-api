<?php

namespace Services;

final class FileReader extends \Prefab {

    private $output = null;

    function __construct($url, $ext) {
        if (!file_exists($url)) return;

        switch ($ext) {
            case 'txt': $this->readTXT($url); break;
            case 'pdf': $this->readPDF($url); break;
            default: $this->output = null;
        }
    }

    public function getText() {
        return $this->output;
    }

    private function readTXT($url) {
        $this->output = @file_get_contents($url);
    }
    
    private function readPDF($url) {
        $pdf = new \PdfToText($url);
		$this->output = $pdf->Text;
    }
    
}