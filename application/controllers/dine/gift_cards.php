<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Gift_cards extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('dine/gift_cards_model');
		$this->load->model('site/site_model');
		$this->load->helper('dine/gift_cards_helper');
	}
	public function index(){
        $this->load->helper('site/site_forms_helper');
		$data = $this->syter->spawn('gift_cards');
		$data['page_title'] = fa('icon-present')." Gift Cheques";
        // $menus = $this->menu_model->get_menus();
        $th = array('Card Number','Is Used?','');
        $data['code'] = create_rtable('gift','gc_id','gift-tbl',$th,'gift_cards/filter_gift_card',false,'list');
  		$data['load_js'] = 'dine/gift_cards.php';
        $data['use_js'] = 'listFormJs';
        $data['add_css'] = 'css/wowdash.css';
        $data['page_no_padding'] = true;
        $data['sideBarHide'] = true;
        $this->load->view('page',$data);
    }
    public function get_gc($id=null,$asJson=true){
    	// die("asdaas");
        $this->load->helper('site/pagination_helper');
        $pagi = null;
        $args = array();
        $total_rows = 1000;
        if($this->input->post('pagi'))
            $pagi = $this->input->post('pagi');
        $post = array();
        
        if(count($this->input->post()) > 0){
            $post = $this->input->post();
        }
        if($this->input->post('card_no')){
            $lk  =$this->input->post('card_no');
            $args["(gift_cards.card_no like '%".$lk."%')"] = array('use'=>'where','val'=>"",'third'=>false);
        }
        if($this->input->post('inactive')){
            $args['gift_cards.inactive'] = array('use'=>'where','val'=>$this->input->post('inactive'));
        }

        $join = null;
        $count = $this->site_model->get_tbl('gift_cards',$args,array(),$join,true,'gift_cards.*',null,null,true);
        $page = paginate('gift_cards/get_gc',$count,$total_rows,$pagi);
        $disc = $this->site_model->get_tbl('gift_cards',$args,array(),$join,true,'gift_cards.*',null,$page['limit']);
        $json = array();
        if(count($disc) > 0){
            $ids = array();
            foreach ($disc as $res) {
		        // echo "<pre>",print_r($res),"</pre>";die();
                
                $link = $this->make->A(fa('fa-edit fa-lg').' Edit','#',array('class'=>'btn btn-outline-success-600 radius-8 px-20 py-11 align-items-center gap-2','id'=>'edit-'.$res->gc_id,'ref'=>$res->gc_id,'return'=>'true'));
                $json[$res->gc_id] = array(      
                    "card_no"=>$res->card_no,
                    "inactive_1"=>($res->inactive == 0 ? 'No' : 'Yes'),
                    "link"=>$link
                );
                $ids[] = $res->gc_id;
            }
        }
         // echo "<pre>",print_r($json),"</pre>";die();
        echo json_encode(array('rows'=>$json,'page'=>$page['code'],'post'=>$post));
    }
	public function gift_cards_setup($gc_id = null)
	{
		$data = $this->syter->spawn();

		if (is_null($gc_id)){
			$data['page_title'] = fa('icon-present fa-fw')." Add New Gift Card";
		}else {
			$gc = $this->gift_cards_model->get_gift_cards($gc_id);
			$gc = $gc[0];
			if (!empty($gc->gc_id)) {
				// $data['page_title'] = fa('fa-user fa-fw')." ".iSetObj($gc,'lname'.', '.'fname');
				$data['page_title'] = fa('icon-present fa-fw')." ".iSetObj($gc,'card_no');
				// if (!empty($gc->update_date))
					// $data['page_subtitle'] = "Last updated ".$gc->update_date;

			} else {
				header('Location:'.base_url().'gift_cards/gift_cards_setup');
			}
		}

		$data['code'] = gift_cards_form_container($gc_id);
		$data['load_js'] = "dine/gift_cards.php";
		$data['use_js'] = "giftCardFormContainerJs";

		$this->load->view('page',$data);
	}
	public function gift_cards_load($gc_id = null)
	{
		$details = array();
		if (!is_null($gc_id))
			$item = $this->gift_cards_model->get_gift_cards($gc_id);
		if (!empty($item))
			$details = $item[0];

		$data['code'] = gift_cards_details_form($details,$gc_id);
		$data['load_js'] = "dine/gift_cards.php";
		$data['use_js'] = "giftCardDetailsJs";
		$this->load->view('load',$data);
	}
	public function gift_cards_details_db()
	{
		// if (!$this->input->post())
			// header("Location:".base_url()."items");

		$items = array(
            'description_id' => $this->input->post('description_id'),
            'brand_id' => $this->input->post('brand_id'),
			'card_no' => $this->input->post('card_no'),
			'amount' => $this->input->post('amount'),
			'inactive' => (int)$this->input->post('inactive'),
		);

		if ($this->input->post('gc_id')) {
			$id = $this->input->post('gc_id');
			$this->gift_cards_model->update_gift_cards($items,$id);
			$msg = "Updated Gift Card";
		} else {
			$id = $this->gift_cards_model->add_gift_cards($items);
			$msg = "Added New Gift Card";
		}

		echo json_encode(array('id'=>$id,'msg'=>$msg));
	}
	#gift cards menu
    public function cashier_gift_cards(){
        $this->load->model('site/site_model');
        $this->load->model('dine/gift_cards_model');
		$this->load->helper('core/on_screen_key_helper');
        $this->load->helper('dine/gift_cards_helper');
        $data = $this->syter->spawn(null);
        $data['code'] = giftCardsPage();
        $data['add_css'] = array('css/pos.css','css/onscrkeys.css','css/virtual_keyboard.css', 'css/cashier.css');	
		$data['add_js'] = array('js/on_screen_keys.js','js/jquery.keyboard.extension-navigation.min.js','js/jquery.keyboard.min.js');
        $data['load_js'] = 'dine/gift_cards.php';
        $data['use_js'] = 'giftCardsJs';
        $data['noNavbar'] = true; /*Hides the navbar. Comment-out this line to display the navbar.*/
        $this->load->view('cashier',$data);
    }
	public function load_gift_cards_details()
	{
		$details = array();
		// $cardno = $this->input->post('cardno');
		$cardno = str_replace('-', '', $this->input->post('cardno'));
		
		if (!is_null($cardno))
			$item = $this->gift_cards_model->get_gift_card_info($cardno);
		if (!empty($item))
			$details = $item[0];

		$data['code'] = gift_cards_details_form($details,$details->gc_id);
		$data['load_js'] = "dine/gift_cards.php";
		$data['use_js'] = "giftCardDetailsJs";
		$this->load->view('load',$data);
	}
	public function gift_cards_list()
	{
		$gift_cards = $this->gift_cards_model->get_gift_cards();

		$data['code'] = giftCardsList($gift_cards);
		$data['load_js'] = "dine/gift_cards.php";
		$data['use_js'] = "giftCardDetailsJs";
		$this->load->view('load',$data);
	}
	public function validate_card_number(){
		$cardno = $this->input->post('cardno');
		$gc_count = 0;
		
		$gc_count = $this->gift_cards_model->get_all_gift_card_count($cardno);
		$gc_det = $gc_count[0];
		
		if(empty($cardno)){
			echo "empty";
		}else if($gc_det->total_count == 0){
			echo "none";
		}else if($gc_det->total_count > 0){
			echo "success||".$cardno;
		}

	}
	 public function upload_excel_form(){
        $data['code'] = giftCheckUploadForm();
        $this->load->view('load',$data);
    }
	 public function filter_gift_card(){
        $data['code'] = giftFilterForm();
        $this->load->view('load',$data);
    }

    public function upload_temp($temp_file_name,$upload_file='menu_excel'){
        $error = "";
        $file  = "";
        $path  = './uploads/temp/';
        $config['upload_path']          = $path;
        // $config['allowed_types']        = 'xls|xlsx';
        $config['allowed_types']        = '*';
        $config['file_name']            = $temp_file_name;
        $config['overwrite']            = true;
        $this->load->library('upload', $config);
        $allowed_files = array('.xls','.xlsx','.csv');
        if (!$this->upload->do_upload($upload_file)){
            $error = $this->upload->display_errors();
        }
        else{
            $fileData = $this->upload->data('file_name');
            if(in_array($fileData['file_ext'],$allowed_files)){
                $file = $fileData['file_name'];
            }
            else{
                $error = 'File is not allowed';
                unlink($path.$fileData['file_name']);
            }
        }           
        return array('file'=>$path."".$file,'error'=>$error);    
    }

     public function upload_excel_db(){
        $this->load->model('dine/main_model');
        $this->load->model('dine/setup_model');
        $temp = $this->upload_temp('menu_excel_temp');
        if($temp['error'] == ""){
            // echo 'dasda';die();
            $now = $this->site_model->get_db_now('sql');
            $this->load->library('excel');
            $obj = PHPExcel_IOFactory::load($temp['file']);
            $sheet = $obj->getActiveSheet()->toArray(null,true,true,true);
            $count = count($sheet);
            $start = 1;
            $rows = array();
            for($i=$start;$i<=$count;$i++){
                if($sheet[$i]["B"] != ""){
                    $rows[] = array(
                        "card_no"         => $sheet[$i]["A"],
                        "amount"        => $sheet[$i]["B"],
                        "description"        => $sheet[$i]["C"],
                        "brand"        => $sheet[$i]["D"],
                        // "branch_code"        => $sheet[$i]["E"],
                    );
                }
            }
            // echo "<pre>",print_r($rows),"</pre>";die();
            if(count($rows) > 0){
                // $dflt_schedule = 1;                
                #################################################################################################################################
                // ### INACTIVE ALL
                //     $this->site_model->update_tbl('menu_categories',array(),array('inactive'=>1));
                //     $this->main_model->update_trans_tbl('menu_categories',array(),array('inactive'=>1));
                //     $this->site_model->update_tbl('menu_subcategories',array(),array('inactive'=>1));
                //     $this->main_model->update_trans_tbl('menu_subcategories',array(),array('inactive'=>1));
                //     $this->site_model->update_tbl('menus',array(),array('inactive'=>1));
                //     $this->main_model->update_trans_tbl('menus',array(),array('inactive'=>1));
                //     $this->site_model->update_tbl('modifier_groups',array(),array('inactive'=>1));
                //     $this->main_model->update_trans_tbl('modifier_groups',array(),array('inactive'=>1));
                //     $this->site_model->update_tbl('modifiers',array(),array('inactive'=>1));
                //     $this->main_model->update_trans_tbl('modifiers',array(),array('inactive'=>1));
                // #################################################################################################################################
                // ### INSERT CATEGORIES
                    $ins_gift_cards = array();
                    foreach ($rows as $ctr => $row) {
                        if(!isset($ins_categories[$row['card_no']]) && $row['card_no'] !='gift check no'){
                            $brand = $this->setup_model->get_brands(null,$row['branch_code'],$row['brand']);
                            $card_no = $row['card_no'];
                            $gc_id = $this->gift_cards_model->get_last_gc_id($row['branch_code'],$brand ? $brand[0]->id:null);


                            $ins_gift_cards[$row['card_no']] = array(
                                'gc_id' => $gc_id+$ctr,
                                'card_no' => strtoupper($card_no),
                                'amount' => $row['amount'],
                                // 'brand_id'=>$brand ? $brand[0]->id:0,
                                'description_id'=>$row['description'],
                                'brand_id'=>$row['brand'],
                                'branch_code'=>$row['branch_code'],
                                'pos_id'=>$brand ? $brand[0]->id:0
                                // 'reg_date'      => $now,
                            );
                        }
                    }
                    $this->site_model->add_tbl_batch_ignore('gift_cards',$ins_gift_cards);
               
                #################################################################################################################################
            }
            unlink($temp['file']);
            site_alert('Gift cards successfully uploaded','success');
        }
        else{
            site_alert($temp['error'],"error");
        }
        redirect(base_url()."gift_cards", 'refresh'); 
    }

    public function download_template(){
    // 	 $data = file_get_contents('php://output'); 
    // $name = 'data.csv';
    	// $this->load->helper('download');
    	$file = FCPATH.'uploads/temp/gift_card.csv';
    	// echo $file;//die();
    	$name = 'gift_card.csv';
    	// $test = force_download($file, NULL);
    	// var_dump($test);
		// echo filesize($file);die();
    	header('Content-Description: File Transfer');
		header('Content-Type: application/csv');
		header('Content-Disposition: attachment; filename='.$name);
		header('Expires: 0');
		header('Content-Transfer-Encoding: binary');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file));
		readfile($file); // push it out
    }
}