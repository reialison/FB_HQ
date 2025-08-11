<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once (dirname(__FILE__) . "/prints.php");
class Promo extends Prints {
	public function __construct(){
		parent::__construct();
        $this->load->helper('dine/promo_helper');
        $this->load->helper('core/string_helper');
        $this->load->model('site/site_model');
    }
    public function free_menu(){
        $this->load->helper('site/site_forms_helper');
        $data = $this->syter->spawn('promo_free');
        $th = array('ID','Name','Description','Status','');
        $data['code'] = create_rtable('promo_free','pf_id','main-tbl',$th,'promo/free_menu_search',false,'list');
        $data['load_js'] = 'dine/promos.php';
        $data['use_js'] = 'promoFreeListJs';
        $data['page_no_padding'] = true;
        $this->load->view('page',$data);
    }
    public function free_menu_form($id=null){
        $data = $this->syter->spawn('promo_free');
        $data['page_title'] = 'Free Promo';
        $prm = array();
        $mms = array();
        $fms = array();
        $fmq = array();
        $menus = array();
        $json = array();
        if($id != null){
            $promo = $this->site_model->get_tbl('promo_free',array('pf_id'=>$id));        
            if(count($promo) > 0){
                $prm = $promo[0];
                $data['page_subtitle'] = $prm->name;                
                
                if($prm->has_menu_id){
                    $mms = explode(',',$prm->has_menu_id); 
                    $menus = array_merge($menus,$mms);
                }
                $promo_menus = $this->site_model->get_tbl('promo_free_menus',array('pf_id'=>$id));        
                foreach ($promo_menus as $pfm) {
                    $menus[] = $pfm->menu_id;
                    $fms[] = $pfm->menu_id;
                    $fmq[$pfm->menu_id] = $pfm->qty;
                }
                $args['menus.menu_id'] = $menus;
                $join["menu_categories"] = array('content'=>"menus.menu_cat_id = menu_categories.menu_cat_id");
                $result = $this->site_model->get_tbl('menus',$args,array(),$join,true,'menus.*,menu_categories.menu_cat_name');
                foreach ($result as $res) {
                    $json[$res->menu_id] = array(
                        "id"=>$res->menu_id,   
                        "name"=>ucwords(strtolower($res->menu_name)),   
                        "title"=>"[".$res->menu_code."] ".ucwords(strtolower($res->menu_name)),   
                        "desc"=>ucwords(strtolower($res->menu_short_desc)),   
                        "subtitle"=>ucwords(strtolower($res->menu_cat_name)),   
                        "caption"=>"PHP ".num($res->cost),
                        "date_reg"=>sql2Date($res->reg_date),
                        "inactive"=>($res->inactive == 0 ? 'No' : 'Yes'),
                    );
                }    
                $images = $this->site_model->get_image(null,null,'menus',array('images.img_ref_id'=>$menus)); 
                foreach ($images as $res) {
                    if(isset($json[$res->img_ref_id])){
                        $js = $json[$res->img_ref_id];
                        $js['image'] = $res->img_path;
                        $json[$res->img_ref_id] = $js;
                    }
                }
            }
        }
        $data['code'] = freePromoForm($prm,$json,$mms,$fms,$fmq);
        $data['load_js'] = 'dine/promos.php';
        $data['use_js'] = 'promoFreeFormJs';
        $data['sideBarHide'] = true;
        $data['page_no_padding'] = true;
        $this->load->view('page',$data);
    }
    public function free_menu_db(){
        $must_menus = $this->input->post('must_menus');
        $free_menus = $this->input->post('free_menus');
        $free_qty = $this->input->post('free_qty');
        $mms="";
        if(count($must_menus) > 0){
            foreach ($must_menus as $mid) {
                $mms .= $mid.",";
            }
            $mms = substr($mms,0,-1);
        }
        $items = array(
            "name"=>$this->input->post('name'),
            "description"=>$this->input->post('description'),
            "amount"=>$this->input->post('amount'),
            "sched_id"=>$this->input->post('sched_id'),
            "has_menu_id"=>$mms,
            "inactive"=>(int)$this->input->post('inactive')
        );
        if($this->input->post('pf_id')){
            $id = $this->input->post('pf_id');
            $this->site_model->update_tbl('promo_free','pf_id',$items,$id);
            $this->site_model->delete_tbl('promo_free_menus',array('pf_id'=>$id));
            $details = array();
            foreach ($free_menus as $ctr => $mid) {
                $details[] = array(
                    "pf_id" => $id,
                    "menu_id"=> $mid,
                    "qty"=> $free_qty[$ctr]
                );
            }
            if(count($details) > 0)
                $this->site_model->add_tbl_batch('promo_free_menus',$details);
            ### MAIN MODEL UPDATE
            $this->site_model->db = $this->load->database('main', TRUE);
            $items['pf_id'] = $id;
            $this->site_model->update_tbl('promo_free','pf_id',$items,$id);
            $this->site_model->delete_tbl('promo_free_menus',array('pf_id'=>$id));
            if(count($details) > 0)
                $this->site_model->add_tbl_batch('promo_free_menus',$details);
            $act = 'update';
            $msg = 'Updated Promo '.$this->input->post('name');
        }
        else{
            $id = $this->site_model->add_tbl('promo_free',$items);
            $details = array();
            foreach ($free_menus as $ctr => $mid) {
                $details[] = array(
                    "pf_id" => $id,
                    "menu_id"=> $mid,
                    "qty"=> $free_qty[$ctr]
                );
            }
            if(count($details) > 0)
                $this->site_model->add_tbl_batch('promo_free_menus',$details);
            ### MAIN MODEL UPDATE
            $this->site_model->db = $this->load->database('main', TRUE);
            $items['pf_id'] = $id;
            $this->site_model->add_tbl('promo_free',$items);
            if(count($details) > 0)
                $this->site_model->add_tbl_batch('promo_free_menus',$details);
            $act = 'add';
            $msg = 'Added  new Promo '.$this->input->post('name');
        }
        site_alert($msg,'success');
        // echo json_encode(array("id"=>$id,"desc"=>$this->input->post('name'),"act"=>$act,'msg'=>$msg));
    }
    public function get_promo_free($id=null,$asJson=true){
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
        if(isset($post['name'])){
            $lk = $post['name'];
            $args["(promo_free.name like '%".$lk."%')"] = array('use'=>'where','val'=>"",'third'=>false);
        }
        if(isset($post['description'])){
            $lk = $post['description'];
            $args["(promo_free.description like '%".$lk."%')"] = array('use'=>'where','val'=>"",'third'=>false);
        }
        if(isset($post['inactive'])){
            $args['promo_free.inactive'] = array('use'=>'where','val'=>$post['inactive']);
        }
        $url =    'promo/free_menu';
        $table =  'promo_free';
        $select = '*';
        $join =   null;
        $count = $this->site_model->get_tbl($table,$args,array(),$join,true,$select,null,null,true);
        $page = paginate($url,$count,$total_rows,$pagi);
        $items = $this->site_model->get_tbl($table,$args,array(),$join,true,$select,null,$page['limit']);
        $json = array();
        if(count($items) > 0){
            $ids = array();
            foreach ($items as $res) {
                $link = $this->make->A(fa('fa-edit fa-lg'),base_url().'promo/free_menu_form/'.$res->pf_id,array('return'=>'true'));
                $json[] = array(
                    "id"=>$res->pf_id,   
                    "title"=>ucwords(strtolower($res->name)),   
                    "desc"=>ucwords(strtolower($res->description)),   
                    "inactive"=>($res->inactive == 0 ? 'Active' : 'Inactive'),
                    "link"=>$link
                );
            }
        }
        echo json_encode(array('rows'=>$json,'page'=>$page['code'],'post'=>$post));
    }
    public function free_menu_search(){
        $data['code'] = freePromoSearch();
        $this->load->view('load',$data);
    }
}