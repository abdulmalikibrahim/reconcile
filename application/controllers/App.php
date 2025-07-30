<?php
class App extends MY_Controller {

    public function index()
    {
        $data["content"] = "index";
        $data["js"] = "index";
        $this->load->view("templates/index",$data);    
    }
}
