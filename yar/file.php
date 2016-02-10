<?php

namespace yar;

class file {
    public $filename = null;
    public $contents = null;
    
    public function __construct($filename) {
        $this->filename = $filename;
        if (file_exists($filename))
            $this->contents = file_get_contents($filename);
    }
    
    public function save() {
        file_put_contents($this->filename, $this->contents);
    }
}

?>