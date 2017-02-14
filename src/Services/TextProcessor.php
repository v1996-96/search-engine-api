<?php

namespace Services;

final class TextProcessor extends \Prefab {

    const DICTIONARIES_DIR = "plugins/phpmorphy/dicts/utf-8";
    const LANG = "ru_RU";

    private $morphy = null;
    private $minWordLength = 3;
    private $sanitizingReg = '[^а-я\s]+';

    public $output = null;

    function __construct() {
        $this->morphy = new \phpMorphy(self::DICTIONARIES_DIR, self::LANG, [
            'storage' => PHPMORPHY_STORAGE_FILE,
            'predict_by_suffix' => true, 
            'predict_by_db' => true,
            'graminfo_as_text' => true
        ]);
    }

    public function run($fileContents) {
        if (is_null($fileContents)) {
            $this->output = [];
            return;
        }

        $encoder = \CharacterEncoder\EncoderFactory::create(['utf-8', 'cp1251']);        
        $encoding = $encoder->detectString(substr($fileContents, 0, 200));
        unset($encoder);

        mb_regex_encoding("UTF-8");
        mb_internal_encoding("UTF-8");

        $fileContents = mb_convert_encoding($fileContents, 'UTF-8', $encoding);
        $sanitized = mb_eregi_replace($this->sanitizingReg, '', $fileContents); unset($fileContents);
        $words = explode(' ', $sanitized); unset($sanitized);

        $wordList = [];
        foreach ($words as $word) {
            $trimmed = mb_ereg_replace("(^\s+)|(\s+$)/us", "", $word);
            if ($trimmed !== "" && mb_strlen($trimmed) > $this->minWordLength) {
                array_push($wordList, mb_strtoupper($trimmed)); 
            }
        }
        unset($words);

        $computed = $this->morphy->getBaseForm($wordList);

        $this->output = [];
        foreach ($wordList as $word) {
            if (!isset($computed[$word])) continue;
            $found = $computed[$word];
            if (is_array($found) && count($found) >= 1)
                array_push($this->output, $found[0]);
        }
        unset($wordList);
        unset($computed);
    }

    public function getResult() {
        return $this->output;
    }

    public function getGroupedResult() {
        $grouped = [];
        foreach ($this->output as $word) {
            if (isset($grouped[$word])) {
                $grouped[$word]++;
            } else {
                $grouped[$word] = 1;
            }
        }
        return $grouped;
    }

}