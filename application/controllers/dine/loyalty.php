<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Loyalty extends CI_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->model('dine/customers_model');
		$this->load->model('site/site_model');
		$this->load->helper('dine/loyalty_helper');
		$this->load->model('dine/main_model');
		$this->load->model('dine/setup_model');
		$this->load->helper('dine/print_helper');
        $this->load->helper('core/string_helper');
		$this->load->model('core/trans_model');
        $this->load->helper('site/site_forms_helper');
	}
    public function index(){
        $data = $this->syter->spawn('customers');        
        $data['code'] = loyaltyPage();
        $data['add_css'] = array('css/cashier.css','css/virtual_keyboard.css');
        $data['add_js'] = array('js/jquery.keyboard.extension-navigation.min.js','js/jquery.keyboard.min.js');

        $data['load_js'] = 'dine/loyalty.php';
        $data['use_js'] = 'loyaltyJs';

        $data['noNavbar'] = true;
        $this->load->view('cashier',$data);
    }
    public function cards_list(){
        $data = $this->syter->spawn('customers');
        $th = array('Code','Customer','Total Points','Reg Date');

        $code = create_rtable('loyalty_cards','card_id','loyalty_cards-tbl',$th,'loyalty/search_card_form');
        $data['code'] = "<div id ='manager'>".$code."</div>";
        $data['load_js'] = 'dine/loyalty.php';
        $data['use_js'] = 'cardsListJs';
        // $data['add_css'] = 'css/cashier.css';
        // $data['noNavbar'] = true; /*Hides the navbar. Comment-out this line to display the navbar.*/
        $this->load->view('load',$data);
    } 
    public function get_cards(){
        $this->load->helper('site/pagination_helper');
        $pagi = null;
        $args = array();
        $total_rows = 30;
        if($this->input->post('pagi'))
            $pagi = $this->input->post('pagi');
        $post = array();
        if(count($this->input->post()) > 0){
            $post = $this->input->post();
        }
        
        if($this->input->post('cust_name')){
            $lk  =$this->input->post('cust_name');
            $args["(customers.fname like '%".$lk."%' OR customers.mname like '%".$lk."%' OR customers.lname like '%".$lk."%' OR customers.suffix like '%".$lk."%')"] = array('use'=>'where','val'=>"",'third'=>false);
        }
        if($this->input->post('code')){
            $lk  =$this->input->post('code');
            $args["(loyalty_cards.code like '%".$lk."%')"] = array('use'=>'where','val'=>"",'third'=>false);
        }
        $select = "loyalty_cards.*,customers.cust_id,customers.fname,customers.mname,customers.lname,customers.suffix";
        $order['loyalty_cards.reg_date'] = 'desc';
        $group = null;
        $join['customers'] = array('content'=>'loyalty_cards.cust_id = customers.cust_id');
        $count = $this->site_model->get_tbl('loyalty_cards',$args,null,$join,true,$select,$group,null,true);
        $page = paginate('loyalty/get_cards',$count,$total_rows,$pagi);
        $items = $this->site_model->get_tbl('loyalty_cards',$args,$order,$join,true,$select,$group,$page['limit']);

        $json = array();
        if(count($items) > 0){
            $ids = array();
            foreach ($items as $res) {
                $link = "";
                $json[] = array(
                    "code"=>$res->code,   
                    "title"=>$res->fname." ".$res->mname." ".$res->lname." ".$res->suffix,   
                    "points"=>numInt($res->points),
                    "date_reg"=>sql2Date($res->reg_date),
                );
            }
        }
        echo json_encode(array('rows'=>$json,'page'=>$page['code'],'post'=>$post));
    }
    public function search_card_form(){
        $data['code'] = cardsSearchForm();
        $this->load->view('load',$data);
    }   
    public function cards(){
        $data['code'] = cardsPage();
        $data['load_js'] = 'dine/loyalty.php';
        $data['use_js'] = 'addCardJs';
        $this->load->view('load',$data);
    }
    public function add_card_db(){
        $user = sess('user');
        $cust_id = $this->input->post('cust_id');
        $card_no = $this->trans_model->get_next_ref(LOYALTY_CARD);
        $this->trans_model->db->trans_start();
            $items = array('cust_id'=>$cust_id,'code'=>$card_no,'reg_user_id'=>$user['id']);
            $card_id = $this->site_model->add_tbl('loyalty_cards',$items,array('reg_date'=>'NOW()'));
            $this->trans_model->save_ref(LOYALTY_CARD,$card_no);
        $this->trans_model->db->trans_complete();
        site_alert('Added New Card.','success');
        echo json_encode(array('card_id'=>$card_id));
    }
    public function card_view($card_id = null){
        $data = $this->syter->spawn('customers');
        $det = array();
        $join['customers'] = array('content'=>'loyalty_cards.cust_id = customers.cust_id');
        $select = "loyalty_cards.*,
                   customers.fname,customers.mname,customers.lname,customers.suffix,
                   customers.phone,customers.email 
                   ";
        $details = $this->site_model->get_tbl('loyalty_cards',array('card_id'=>$card_id),array(),$join,true,$select);
        if($details){
            $det = $details[0];
        }
        $data['code'] = cardsViewPage($det);
        $data['load_js'] = 'dine/loyalty.php';
        $data['use_js'] = 'addCardViewJs';
        $data['add_css'] = array('css/cashier.css','css/virtual_keyboard.css');
        $data['add_js'] = array('js/jquery.keyboard.extension-navigation.min.js','js/jquery.keyboard.min.js');
        $data['noNavbar'] = true;
        $this->load->view('cashier',$data);
    }

}