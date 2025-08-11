<?php
	class download extends CI_Controller{

		public function __construct(){
			parent::__construct();
	    }

	    function index(){

	    }

		function get_file($param){
			$this->load->model('dine/download_model');

			$get_code = $this->download_model->get_code($param);

			if($get_code){
				redirect('uploads/reports/'.$get_code[0]->file_name);
			}
		}
	}
?>