<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once (dirname(__FILE__) . "/prints.php");
class Fixer extends Prints {
	public function __construct(){
		parent::__construct();
		$this->load->model('dine/cashier_model');
        $this->load->helper('core/string_helper');
        $this->load->helper('dine/login_helper');
        $this->load->model('site/site_model');
    }
    public function xread_sales($shift_id){
        #CHANGE DB
            $this->site_model->db = $this->load->database('main', TRUE);
        $orders = $this->site_model->get_tbl('trans_sales',array('shift_id'=>$shift_id));
        $count = count($orders);
        echo "Trans count: ".$count."<br>";
        if($count > 0){
            $this->make->sTable();
            $this->make->sRow();
                $this->make->td("Sales ID",array('style'=>'width:80px;'));
                $this->make->td("Receipt #",array('style'=>'width:100px;'));
                $this->make->td("Total",array('style'=>'width:150px;'));
                $this->make->td("Datetime",array('style'=>'width:150px;'));
                $this->make->td("Status");
            $this->make->eRow();
            $total = 0;
            foreach ($orders as $trans) {
                $this->make->sRow();
                    $this->make->td($trans->sales_id);
                    $this->make->td($trans->trans_ref);
                    $this->make->td($trans->total_amount);
                    $this->make->td($trans->datetime);
                    $status = "Sale";
                    if($trans->void_ref != '')
                        $status = "Voided";
                    else if($trans->inactive == 1 && $trans->void_ref == "")
                        $status = "cancelled";
                    $this->make->td($status);
                    
                $this->make->eRow();   
                $total += $trans->total_amount;     
            }
            $this->make->eTable();
        }
        echo "Trans TOTAL: ".$total."<br>";
        echo $this->make->code();
    }
    public function zread_sales(){
        $date = "2016-03-02";
        $rargs["DATE(read_details.read_date) = DATE('".date2Sql($date)."') "] = array('use'=>'where','val'=>null,'third'=>false);
        $select = "read_details.*";
        $results = $this->site_model->get_tbl('read_details',$rargs,array('scope_from'=>'asc'),"",true,$select);
        $args = array();
        $from = "";
        $to = "";
        $datetimes = array();
        foreach ($results as $res) {
            $datetimes[] = $res->scope_from;
            $datetimes[] = $res->scope_to;
            // break;
        }
        usort($datetimes, function($a, $b) {
          $ad = new DateTime($a);
          $bd = new DateTime($b);
          if ($ad == $bd) {
            return 0;
          }
          return $ad > $bd ? 1 : -1;
        });
        foreach ($datetimes as $dt) {
            $from = $dt;
            break;
        }    
        foreach ($datetimes as $dt) {
            $to = $dt;
        }    
        if($from != "" && $to != ""){
            $args["trans_sales.datetime  BETWEEN '".$from."' AND '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);
        }
        else{
            $args["DATE(trans_sales.datetime) = DATE('".date2Sql($date)."') "] = array('use'=>'where','val'=>null,'third'=>false);
            $from = date('Y-m-d 00:00',strtotime($date));
            $to = date('Y-m-d 24:00',strtotime($date));
        }
        #CHANGE DB
            $this->site_model->db = $this->load->database('main', TRUE);
        

        $orders = $this->site_model->get_tbl('trans_sales',$args);

        $count = count($orders);
        echo "Trans count: ".$count."<br>";
        $total = 0;
        if($count > 0){
            $this->make->sTable();
            $this->make->sRow();
                $this->make->td("Sales ID",array('style'=>'width:80px;'));
                $this->make->td("Receipt #",array('style'=>'width:100px;'));
                $this->make->td("Total",array('style'=>'width:150px;'));
                $this->make->td("Datetime",array('style'=>'width:150px;'));
               $this->make->td("Status");
            $this->make->eRow();
            foreach ($orders as $trans) {
                $this->make->sRow();
                    $this->make->td($trans->sales_id);
                    $this->make->td($trans->trans_ref);
                    $this->make->td($trans->total_amount);
                    $this->make->td($trans->datetime);
                    $status = "Sale";
                    if($trans->void_ref != '')
                        $status = "Voided";
                    else if($trans->inactive == 1 && $trans->void_ref == "")
                        $status = "cancelled";
                    $this->make->td($status);
                $this->make->eRow();        
                 $total += $trans->total_amount;     
            }
            $this->make->eTable();
        }
        echo "Trans TOTAL: ".$total."<br>";
        echo $this->make->code();
    }
    public function menu_random_total(){
        $menus = $this->site_model->get_tbl('menus',array('menu_cat_id'=>'1'));
        $rand_menus = array();
        $ids = array();
        foreach ($menus as $res) {
            $rand_menus[$res->menu_id] = $res->cost;
            $ids[$res->menu_id] = $res->menu_id;
        }

        // echo var_dump($ids);
        // return false;
        $find_total = 1820;
        $find_qty   = 20;
        $used_menu = array();
        $qty = 0;
        $total = 0;
        // while ($find_qty != $qty) {
            while ($find_total != $total) {
                $mn = array_rand($ids);
                $cost = $rand_menus[$mn];
                $used_menu[$mn] = $cost; 
                $total += $cost; 
                $qty++;
                if($total > $find_total){
                    $total = 0;
                    $used_menu = array();
                    $qty = 0;
                }
            }
        // }
        echo $total."<br>";
        echo var_dump($used_menu)."<br><br>";


    }
}