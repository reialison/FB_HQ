<?php
	$this->load->view('login2/head');
	$this->load->view('login2/body');
	$this->load->view('login2/foot');
	if(isset($load_js))
		$this->load->view('js/'.$load_js);
?>