<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once (realpath(dirname(__FILE__) . '/..')."/dine/prints.php");
class Search extends Prints {
	var $data = null;
    public function __construct(){
        parent::__construct();        
    }
    public function menus(){
        $reference = $this->input->post('q');
        $len = strlen($reference);
        if($len >= 2){
            $lk = $reference;
            $args["menus.menu_name like '%".$lk."%' OR menus.menu_code like '%".$lk."%'"] = array('use'=>'where','val'=>"",'third'=>false);
            $group = 'menus.menu_code';
            $items = $this->site_model->get_tbl('menus',$args,array(),null,true,'*',$group);
            $json_array = array();
            foreach ($items as $res) {
                $json_array[] = array(
                    'Id'=>$res->menu_id,
                    'Text'=>$res->menu_name,
                );
            }
            echo json_encode($json_array);
        }
    }
    public function menus_cat(){
        $reference = $this->input->post('q');
        $lk = $reference;
        $args["menus.menu_name like '%".$lk."%' OR menus.menu_code like '%".$lk."%' OR menu_categories.menu_cat_name like '%".$lk."%'"] = array('use'=>'where','val'=>"",'third'=>false);
        $joinTables['menu_categories'] = array('content'=>'menu_categories.menu_cat_id = menus.menu_cat_id');
        $items = $this->site_model->get_tbl('menus',$args,array(),$joinTables);
        $json_array = array();
        foreach ($items as $res) {
            $json_array[] = array(
                'Id'=>$res->menu_id,
                'Text'=>'['.$res->menu_cat_name.'] '.$res->menu_name,
            );
        }
        echo json_encode($json_array);
    }
    public function items(){
        $reference = $this->input->post('q');
        $lk = $reference;
        $args["items.name like '%".$lk."%' OR items.barcode like '%".$lk."%'"] = array('use'=>'where','val'=>"",'third'=>false);
        $items = $this->site_model->get_tbl('items',$args,array());
        $json_array = array();
        foreach ($items as $res) {
            $json_array[] = array(
                'Id'=>$res->item_id,
                'Branch'=>$res->branch_code,
                'Text'=>$res->name,
            );
        }
        echo json_encode($json_array);
    }
    public function modifiers(){
        $reference = $this->input->post('q');

        $lk = $reference;
        $args["modifiers.name like '%".$lk."%'"] = array('use'=>'where','val'=>"",'third'=>false);
        $args["inactive"] = 0;
        $items = $this->site_model->get_tbl('modifiers',$args,array());
        
        $json_array = array();
        foreach ($items as $res) {
            $json_array[] = array(
                'Id'=>$res->mod_id,
                'Text'=>$res->name,
            );
        }
        echo json_encode($json_array);
    }
    public function modifiersGroup(){
        $reference = $this->input->post('q');

        $lk = $reference;
        $args["modifier_groups.name like '%".$lk."%'"] = array('use'=>'where','val'=>"",'third'=>false);
        $args["inactive"] = 0;
        $items = $this->site_model->get_tbl('modifier_groups',$args,array());
        
        $json_array = array();
        foreach ($items as $res) {
            $json_array[] = array(
                'Id'=>$res->mod_group_id,
                'Text'=>$res->name,
            );
        }
        echo json_encode($json_array);
    }
    public function branch(){
        $reference = $this->input->post('q');

        $lk = $reference;
        $args["branch_details.branch_code like '%".$lk."%' OR branch_details.branch_name like '%".$lk."%' OR branch_details.branch_desc like '%".$lk."%'"] = array('use'=>'where','val'=>"",'third'=>false);
        $args["inactive"] = 0;
        $items = $this->site_model->get_tbl('branch_details',$args,array());
        
        $json_array = array();
        foreach ($items as $res) {
            $json_array[] = array(
                'Id'=>$res->branch_id,
                'Text'=>$res->branch_name,
            );
        }
        echo json_encode($json_array);
    }
}