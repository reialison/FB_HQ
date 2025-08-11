<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Trans extends CI_Controller {
	var $data = null;
    public function void_form($trans_id=null){
       $this->load->helper('dine/trans_helper');
       $data['code'] = voidForm();
       $this->load->view('load',$data);
    }
}