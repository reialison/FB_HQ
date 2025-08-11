<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once (dirname(__FILE__) . "/reads.php");
class Megaworld extends Reads {
	public function __construct(){
		parent::__construct();
		$this->load->helper('dine/megaworld_helper');
		$this->load->model('dine/cashier_model');
		$this->load->model('core/trans_model');
	}
	public function index(){
        $data = $this->syter->spawn(null);
        $data['code'] = megaworldPage();
        $data['add_css'] = array('css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css');
        $data['add_js'] = array('js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js');
        $data['load_js'] = 'dine/megaworld.php';
        $data['use_js'] = 'mainPageJs';
        $this->load->view('load',$data);
	}
	public function daily_files(){
		$data = $this->syter->spawn(null);
		$data['code'] = dailyFilesPage();
		$data['add_css'] = array('css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css');
		$data['add_js'] = array('js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js');
		$data['load_js'] = 'dine/megaworld.php';
		$data['use_js'] = 'dailyFilesJS';
		$this->load->view('load',$data);
	}
	public function settings(){
		$data = $this->syter->spawn(null);
		$objs = $this->site_model->get_tbl('megaworld');
		$obj = array();
		if(count($objs) > 0){
			$obj = $objs[0];			
		}
		$data['code'] = settingsPage($obj);
		$data['add_css'] = array('css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css');
		$data['add_js'] = array('js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js');
		$data['load_js'] = 'dine/megaworld.php';
		$data['use_js'] = 'settingsJS';
		$this->load->view('load',$data);	
	}
	public function settings_db(){
		$this->load->model('dine/main_model');
		$items = array(
			'tenant_code' => $this->input->post('tenant_code'),
			'sales_type' => $this->input->post('sales_type'),
			'file_path'   => $this->input->post('file_path'),
		);
		$id = $this->input->post('megaworld');
		$this->site_model->update_tbl('megaworld','id',$items,$id);
		$this->main_model->update_tbl('megaworld','id',$items,$id);
		$msg = "Updated Settings.";
		echo json_encode(array('id'=>$id,'msg'=>$msg));
	}
	public function get_file(){
		$date = $this->input->post('file_date');
		
		$rargs["DATE(read_details.read_date) = DATE('".date2Sql($date)."') "] = array('use'=>'where','val'=>null,'third'=>false);
		$rargs["read_type"] = 2;
		$select = "read_details.*";
		$this->site_model->db = $this->load->database('default', TRUE);
		$results = $this->site_model->get_tbl('read_details',$rargs,array('scope_from'=>'asc'),"",true,$select);
		$ctr = 1;
		foreach ($results as $res) {
			$zread = $res->id;
			$ctr = $res->ctr;
		}

		$mon = array('01'=>'1','02'=>'2','03'=>'3','04'=>'4','05'=>'5','06'=>'6','07'=>'7','08'=>'8','09'=>'9','10'=>'A','11'=>'B','12'=>'C');
		$mall_db = $this->site_model->get_tbl('megaworld');
		$mall = array('tenant_code'=>$mall_db[0]->tenant_code,'sales_type'=>$mall_db[0]->sales_type);
		$file_path = filepathisize($mall_db[0]->file_path);
		$year = date('Y',strtotime($date));
        $file_path .= $year."/";
        $eod = $this->old_grand_net_total($date);
		$sales_file = $file_path."S".substr($mall['tenant_code'],0,4).TERMINAL_NUMBER.$ctr.".".$mon[date('m',strtotime($date))].date('d',strtotime($date));
		$hourly_file = $file_path."H".substr($mall['tenant_code'],0,4).TERMINAL_NUMBER.$ctr.".".$mon[date('m',strtotime($date))].date('d',strtotime($date));
		$disc_file = $file_path."D".substr($mall['tenant_code'],0,4).TERMINAL_NUMBER.$ctr.".".$mon[date('m',strtotime($date))].date('d',strtotime($date));
		if(file_exists($sales_file)){
			$fh = fopen($sales_file, 'r');
			$theData = fread($fh, filesize($sales_file));
			fclose($fh);
			$sales = "<pre>".$theData."</pre>";
		}
		else{
			$sales = "<center> file not found. </center>";
		}
		if(file_exists($hourly_file)){
			$fh = fopen($hourly_file, 'r');
			$theData = fread($fh, filesize($hourly_file));
			fclose($fh);
			$hour = "<pre>".$theData."</pre>";
		}
		else{
			$hour = "<center> file not found. </center>";
		}
		if(file_exists($disc_file)){
			if(filesize($disc_file) > 0){
				$fh = fopen($disc_file, 'r');
				$theData = fread($fh, filesize($disc_file));
				fclose($fh);
				$disc = "<pre>".$theData."</pre>";
			}else{
				$disc = "<pre>  </pre>";
			}


			
		}
		else{
			$disc = "<center> file not found. </center>";
		}
		echo json_encode(array('sales'=>$sales,'hour'=>$hour,'disc'=>$disc));
	}
	public function regen_file(){
		$date = $this->input->post('file_date');
		$rargs["DATE(read_details.read_date) = DATE('".date2Sql($date)."') "] = array('use'=>'where','val'=>null,'third'=>false);
		$rargs["read_type"] = 2;
		$select = "read_details.*";
		$this->site_model->db = $this->load->database('default', TRUE);
		$results = $this->site_model->get_tbl('read_details',$rargs,array('scope_from'=>'asc'),"",true,$select);
		$zread = 0;
		foreach ($results as $res) {
			$zread = $res->id;
			$read_date = $res->read_date;
		}
		if($zread != 0){
			$reg = $this->megaworld_file($zread,true);
			echo json_encode(array('msg'=>$reg['msg'],'error'=>$reg['error']));
		}
		else{
			echo json_encode(array('msg'=>'No zread found on this date.','error'=>1));
		}
	}
}