<?php
date_default_timezone_set('Asia/Jakarta');
class MY_Controller extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
    }

    function fb($array)
	{
		$code = isset($array["statusCode"]) ? $array["statusCode"] : 500;
		http_response_code($code);
		echo json_encode($array);
		die();
	}
}
?>
