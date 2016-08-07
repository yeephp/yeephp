<?php 

namespace Yee\Libraries\Curl;



class response {
    public $headers = array();
    public $status_code;
    public $text = '';

    public function __construct($status_code, $headers, $text) {
        $this->status_code = $status_code;
        $this->headers = $headers;
        $this->text = $text;
    }

    public function __toString() {
        return $this->text;
    }
    public function json() {
        return json_encode($this->text);
    }
}