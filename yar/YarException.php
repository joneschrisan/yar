<?php

namespace yar;

class YarException extends \Exception {
    
    public function __construct($message, $code = 0, $pervious = null) {
        parent::__construct($message, $code, $pervious);
    }
    
}

?>