<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {
	public function __construct(){
		parent::__construct();
        $this->load->model('dine/main_model');
        $this->load->model('dine/cashier_model');
        $this->load->model('site/site_model');
	}
    // public function sales_to_main($date_from=null,$date_to=null){
	public function sales_to_main($shts = array()){
        $args = array();
        $orders = array();
        $cols = array();
        // if($date_from != null)
        //    $args['trans_sales.datetime >='] = $date_from;
        // if($date_to != null)
        //    $args['trans_sales.datetime <='] = $date_to;
        
        if(count($shts) > 0){
            $args =  array(
                    "trans_sales.shift_id" => array('use'=>'where_in','val'=>$shts,'third'=>false)
            );
            $result = $this->cashier_model->get_just_trans_sales(
                null,
                $args,
                'asc'
            );
            $orders = $result->result();
            $cols = $result->list_fields();
        }
        $prints = "";
        $sales = array();
        $sales_ids = array();
        if(count($orders) > 0 ){
            foreach ($orders as $ord) {
                $row = array();
                $sales_ids[] = $ord->sales_id;
                foreach ($cols as $col) {
                    $row[$col] = $ord->$col;
                }
                $row['pos_id'] = TERMINAL_ID;
                $sales[] = $row;
            }

            $args['trans_sales.sales_id'] = $sales_ids;
            $args['trans_sales.pos_id'] = TERMINAL_ID;
            $this->main_model->delete_trans_tbl_batch('trans_sales',$args);
            $this->main_model->add_trans_sales_batch($sales);

            $tbl['trans_sales_charges'] = $this->cashier_model->get_trans_sales_charges(null,array('sales_id'=>$sales_ids));
            $tbl['trans_sales_items'] = $this->cashier_model->get_trans_sales_items(null,array('sales_id'=>$sales_ids));
            $tbl['trans_sales_discounts'] = $this->cashier_model->get_trans_sales_discounts(null,array('sales_id'=>$sales_ids));
            $tbl['trans_sales_menu_modifiers'] = $this->cashier_model->get_trans_sales_menu_modifiers(null,array('sales_id'=>$sales_ids));
            $tbl['trans_sales_menus'] = $this->cashier_model->get_trans_sales_menus(null,array('sales_id'=>$sales_ids));
            $tbl['trans_sales_no_tax'] = $this->cashier_model->get_trans_sales_no_tax(null,array('sales_id'=>$sales_ids));
            $tbl['trans_sales_payments'] = $this->cashier_model->get_trans_sales_payments(null,array('sales_id'=>$sales_ids));
            $tbl['trans_sales_tax'] = $this->cashier_model->get_trans_sales_tax(null,array('sales_id'=>$sales_ids));
            $tbl['trans_sales_zero_rated'] = $this->cashier_model->get_trans_sales_zero_rated(null,array('sales_id'=>$sales_ids));
            $tbl['trans_sales_local_tax'] = $this->cashier_model->get_trans_sales_local_tax(null,array('sales_id'=>$sales_ids));
            $tbl['reasons'] = $this->cashier_model->get_just_reasons($sales_ids);
            // echo var_dump($tbl['trans_sales_local_tax'])
            $details = array();
            foreach ($tbl as $table => $row) {
                $cl = $this->site_model->get_tbl_cols($table);
                unset($cl[0]);
                $dets = array();
                foreach ($row as $r) {
                    $det = array();
                    foreach ($cl as $c) {
                        $det[$c] = $r->$c;
                    }
                    $det['pos_id'] = TERMINAL_ID;
                    $dets[] = $det;
                }####
                $details[$table]=$dets;
            }
            #
            foreach ($details as $table => $tbl_rows) {
                if(count($tbl_rows) > 0){
                    $bargs = array();
                    if($table == 'reasons'){
                        $bargs[$table.'.trans_id'] = $sales_ids;                        
                    }
                    else
                        $bargs[$table.'.sales_id'] = $sales_ids;                        
                    $bargs[$table.'.pos_id'] = TERMINAL_ID;
                    $this->main_model->delete_trans_tbl_batch($table,$bargs);
                    $this->main_model->add_trans_tbl_batch($table,$tbl_rows);
                }
            }
            #
        }
    }
    public function shifts_to_main($shift_id=null){
        $this->load->model('dine/clock_model');
        $table = 'shifts';
        $shift = $this->clock_model->get_user_shift($shift_id);
        if(count($shift) > 0){
            $shift_cols = $this->site_model->get_tbl_cols($table);
            $det = array();
            foreach ($shift as $sh) {
                foreach ($shift_cols as $col) {
                    $det[$col] = $sh->$col;
                }
                $det['pos_id'] = TERMINAL_ID;
            }
            $cashout_id = $det['cashout_id'];
            $xread_id = $det['xread_id'];
            $args = array();
            $args[$table.'.shift_id'] = $shift_id;
            $args[$table.'.pos_id'] = TERMINAL_ID;
            $this->main_model->delete_trans_tbl_batch($table,$args);
            $this->main_model->add_trans_tbl($table,$det);
            ##
            ##ENTRIES 
            ###########
                $args = array();
                $args['shift_id'] = $shift_id;
                $entries = $this->clock_model->get_just_shift_entries(null,$args);
                $table = 'shift_entries';
                if(count($entries) > 0){
                    $entries_cols = $this->site_model->get_tbl_cols($table);
                    $rows = array();
                    foreach ($entries as $en) {
                        $row = array();
                        foreach ($entries_cols as $col) {
                            $row[$col] = $en->$col;
                        }
                        $row['pos_id'] = TERMINAL_ID;
                        $rows[] = $row;
                    }####
                    $args = array();
                    $args['shift_id'] = $shift_id;
                    $args['pos_id'] = TERMINAL_ID;
                    $this->main_model->delete_trans_tbl_batch($table,$args);
                    $this->main_model->add_trans_tbl_batch($table,$rows);
                }
            ##
            ##CASHOUT 
            ###########    
                if($cashout_id != null){
                    $args = array();
                    $args['cashout_id'] = $cashout_id;
                    $cashout_entries = $this->clock_model->get_cashout_entries(null,$args);
                    $table = 'cashout_entries';
                    if(count($cashout_entries) > 0){
                        $cashout_entries_cols = $this->site_model->get_tbl_cols($table);
                        $rows = array();
                        foreach ($cashout_entries as $en) {
                            $row = array();
                            foreach ($cashout_entries_cols as $col) {
                                $row[$col] = $en->$col;
                            }
                            $row['pos_id'] = TERMINAL_ID;
                            $rows[] = $row;
                        }####
                        $args = array();
                        $args['cashout_id'] = $cashout_id;
                        $args['pos_id'] = TERMINAL_ID;
                        $this->main_model->delete_trans_tbl_batch($table,$args);
                        $this->main_model->add_trans_tbl_batch($table,$rows);
                        #####
                        $table = 'cashout_details';
                        $args = array();
                        $args[$table.'.cashout_id'] = $cashout_id;
                        $cashout_details = $this->clock_model->get_cashout_details(null,$args);
                        if(count($cashout_details) > 0){
                            $cashout_details_cols = $this->site_model->get_tbl_cols($table);     
                            $cashout_details_cols[0] = 'cashout_detail_id';
                            $rows = array();
                            foreach ($cashout_details as $en) {
                                $row = array();
                                foreach ($cashout_details_cols as $col) {
                                    if($col == 'cashout_detail_id'){
                                        $row[$col] = $en->id;
                                    }
                                    else
                                        $row[$col] = $en->$col;
                                }
                                $row['pos_id'] = TERMINAL_ID;
                                $rows[] = $row;
                            }####
                            $args = array();
                            $args['cashout_id'] = $cashout_id;
                            $args['pos_id'] = TERMINAL_ID;
                            $this->main_model->delete_trans_tbl_batch($table,$args);
                            $this->main_model->add_trans_tbl_batch($table,$rows);
                            #####

                        }    
                        ###
                    }
                }
            ##
            ##X READ 
            ###########
                if($xread_id != null){
                    $this->reads_to_main($xread_id);
                }       
        }
        ###############
    }
    public function reads_to_main($read_id=null){
        $args = array();
        $args['id'] = $read_id;
        $read_details = $this->cashier_model->get_reads(null,$args);
        $table = 'read_details';
        if(count($read_details) > 0){
            $read_details_cols = $this->site_model->get_tbl_cols($table);     
            $read_details_cols[0] = 'read_id';
            $rows = array();
            foreach ($read_details as $en) {
                $row = array();
                foreach ($read_details_cols as $col) {
                    if($col == 'read_id'){
                        $row[$col] = $en->id;
                    }
                    else
                        $row[$col] = $en->$col;
                }
                $row['pos_id'] = TERMINAL_ID;
                $rows[] = $row;
            }####
            $args = array();
            $args['read_id'] = $read_id;
            $args['pos_id'] = TERMINAL_ID;
            $this->main_model->delete_trans_tbl_batch($table,$args);
            $this->main_model->add_trans_tbl_batch($table,$rows);
            #####
        }
    }
    public function remove_recent_data($date_from=null,$date_to=null){
        if($date_from != null || $date_to != null){
            $args = array(
                    // 'trans_sales.inactive' => 0,
                    // 'trans_sales.type_id' => SALES_TRANS,
                    // "trans_sales.trans_ref  IS NOT NULL" => array('use'=>'where','val'=>null,'third'=>false)
                );
            if($date_from != null)
                $args['trans_sales.datetime >='] = $date_from;
            if($date_to != null)
                $args['trans_sales.datetime <='] = $date_to;

            $result = $this->cashier_model->get_trans_sales(
                null,
                $args,
                'asc'
            );
            if(count($result) > 0 ){
                $sales = array();
                $sales_ids = array();
                foreach ($result as $ord) {
                    $sales_ids[] = $ord->sales_id;
                }
                $tbls = array(
                    'trans_sales',
                    'trans_sales_charges',
                    'trans_sales_items',
                    'trans_sales_discounts',
                    'trans_sales_menu_modifiers',
                    'trans_sales_menus',
                    'trans_sales_no_tax',
                    'trans_sales_payments',
                    'trans_sales_tax',
                    'trans_sales_zero_rated',
                    'trans_sales_local_tax',
                    'reasons',
                );
                
                foreach ($tbls as $tbl) {
                    if($tbl == 'reasons')
                        $this->site_model->delete_tbl($tbl,array('trans_id'=>$sales_ids));
                    else                        
                        $this->site_model->delete_tbl($tbl,array('sales_id'=>$sales_ids));
                }
            }    
        }
        #
    }  
    public function remove($datetime){
        $this->remove_recent_data(null,$datetime);
    }  
    public function import(){
        // $this->sales_to_main('2016-04-22 12:00:54','2016-04-22 21:10:44');
        $this->sales_to_main(array(16,17));
        update_load(40,'importing reads');
        ##############################################################
            $read_details = $this->cashier_model->get_reads();
            $table = 'read_details';
            if(count($read_details) > 0){
                $read_details_cols = $this->site_model->get_tbl_cols($table);     
                $read_details_cols[0] = 'read_id';
                $rows = array();
                foreach ($read_details as $en) {
                    $row = array();
                    foreach ($read_details_cols as $col) {
                        if($col == 'read_id'){
                            $row[$col] = $en->id;
                        }
                        else
                            $row[$col] = $en->$col;
                    }
                    $row['pos_id'] = TERMINAL_ID;
                    $rows[] = $row;
                }####
                $args = array();
                $args['pos_id'] = TERMINAL_ID;
                $this->main_model->delete_trans_tbl_batch($table,$args);
                $this->main_model->add_trans_tbl_batch($table,$rows);
                #####
            }
        update_load(70,'importing shifts');
        ##############################################################
            $this->load->model('dine/clock_model');
            $table = 'shifts';
            $shift = $this->clock_model->get_user_shift();
            if(count($shift) > 0){
                $shift_cols = $this->site_model->get_tbl_cols($table);
                $shifts = array();
                foreach ($shift as $sh) {
                    $det = array();
                    foreach ($shift_cols as $col) {
                        $det[$col] = $sh->$col;
                    }
                    $det['pos_id'] = TERMINAL_ID;
                    $shifts[] = $det;
                    $cashout_id = $det['cashout_id'];
                    ######################################################################
                    ###ENTRIES
                    ######################################################################
                        $args = array();
                        $shift_id = $sh->shift_id;
                        $args['shift_id'] = $shift_id;
                        $entries = $this->clock_model->get_just_shift_entries(null,$args);
                        $table = 'shift_entries';
                        if(count($entries) > 0){
                            $entries_cols = $this->site_model->get_tbl_cols($table);
                            $rows = array();
                            foreach ($entries as $en) {
                                $row = array();
                                foreach ($entries_cols as $col) {
                                    $row[$col] = $en->$col;
                                }
                                $row['pos_id'] = TERMINAL_ID;
                                $rows[] = $row;
                            }####
                            $args = array();
                            $args['shift_id'] = $shift_id;
                            $args['pos_id'] = TERMINAL_ID;
                            $this->main_model->delete_trans_tbl_batch($table,$args);
                            $this->main_model->add_trans_tbl_batch($table,$rows);
                        }
                     ######################################################################
                     ###CASHOUT
                     ######################################################################
                        if($cashout_id != null){
                            $args = array();
                            $args['cashout_id'] = $cashout_id;
                            $cashout_entries = $this->clock_model->get_cashout_entries(null,$args);
                            $table = 'cashout_entries';
                            if(count($cashout_entries) > 0){
                                $cashout_entries_cols = $this->site_model->get_tbl_cols($table);
                                $rows = array();
                                foreach ($cashout_entries as $en) {
                                    $row = array();
                                    foreach ($cashout_entries_cols as $col) {
                                        $row[$col] = $en->$col;
                                    }
                                    $row['pos_id'] = TERMINAL_ID;
                                    $rows[] = $row;
                                }####
                                $args = array();
                                $args['cashout_id'] = $cashout_id;
                                $args['pos_id'] = TERMINAL_ID;
                                $this->main_model->delete_trans_tbl_batch($table,$args);
                                $this->main_model->add_trans_tbl_batch($table,$rows);
                                #####
                                $table = 'cashout_details';
                                $args = array();
                                $args[$table.'.cashout_id'] = $cashout_id;
                                $cashout_details = $this->clock_model->get_cashout_details(null,$args);
                                if(count($cashout_details) > 0){
                                    $cashout_details_cols = $this->site_model->get_tbl_cols($table);     
                                    $cashout_details_cols[0] = 'cashout_detail_id';
                                    $rows = array();
                                    foreach ($cashout_details as $en) {
                                        $row = array();
                                        foreach ($cashout_details_cols as $col) {
                                            if($col == 'cashout_detail_id'){
                                                $row[$col] = $en->id;
                                            }
                                            else
                                                $row[$col] = $en->$col;
                                        }
                                        $row['pos_id'] = TERMINAL_ID;
                                        $rows[] = $row;
                                    }####
                                    $args = array();
                                    $args['cashout_id'] = $cashout_id;
                                    $args['pos_id'] = TERMINAL_ID;
                                    $this->main_model->delete_trans_tbl_batch($table,$args);
                                    $this->main_model->add_trans_tbl_batch($table,$rows);
                                    #####

                                }    
                                ###
                            }
                        }   

                }
                // $cashout_id = $det['cashout_id'];
                // $xread_id = $det['xread_id'];
                $args = array();
                $table = 'shifts';
                $args[$table.'.pos_id'] = TERMINAL_ID;
                $this->main_model->delete_trans_tbl_batch($table,$args);
                $this->main_model->add_trans_tbl_batch($table,$shifts);
            }
        update_load(100);
        # 
    } 
    public function do_import(){
        start_load(10,'importing trans sales');
        $this->import();
        // $this->remove('2015-05-25 22:00:00');
    }
    public function sales_to_main_query($date_from=null,$date_to=null){
        $args = array();
        // if($date_from != null)
        //    $args['trans_sales.datetime >='] = $date_from;
        // if($date_to != null)
        //    $args['trans_sales.datetime <='] = $date_to;

        // $args['trans_sales.shift_id'] = 196;
        $args['trans_sales.shift_id'] = array(6);
        $result = $this->cashier_model->get_just_trans_sales(
            null,
            $args,
            'asc'
        );
        // echo $this->cashier_model->db->last_query();
        $print = "";
        $orders = $result->result();
        $cols = $result->list_fields();
        $sales = array();
        $sales_ids = array();
        if(count($orders) > 0 ){
            $check = "IN(";
            foreach ($orders as $ord) {
                $check .= $ord->sales_id.",";
            }
            $check = substr($check,0,-1).")";
            // echo "SALES_ID CHECK ---> ".$check.");<br><br>";
            // echo count($orders)."<br><br>";

            $print .= "DELETE FROM trans_sales WHERE sales_id ".$check.";\r\n";

            foreach ($orders as $ord) {
                $row = array();
                $sales_ids[] = $ord->sales_id;
                foreach ($cols as $col) {
                    $row[$col] = $ord->$col;
                }
                $row['pos_id'] = TERMINAL_ID;
                $sales[] = $row;
                $print .= $this->db->insert_string('trans_sales',$row).";\r\n";
            }

            $args['trans_sales.sales_id'] = $sales_ids;
            $args['trans_sales.pos_id'] = TERMINAL_ID;
            // $this->main_model->delete_trans_tbl_batch('trans_sales',$args);
            // $this->main_model->add_trans_sales_batch($sales);

            $tbl['trans_sales_charges'] = $this->cashier_model->get_trans_sales_charges(null,array('sales_id'=>$sales_ids));
            $tbl['trans_sales_items'] = $this->cashier_model->get_trans_sales_items(null,array('sales_id'=>$sales_ids));
            $tbl['trans_sales_discounts'] = $this->cashier_model->get_trans_sales_discounts(null,array('sales_id'=>$sales_ids));
            $tbl['trans_sales_menu_modifiers'] = $this->cashier_model->get_trans_sales_menu_modifiers(null,array('sales_id'=>$sales_ids));
            $tbl['trans_sales_menus'] = $this->cashier_model->get_trans_sales_menus(null,array('sales_id'=>$sales_ids));
            $tbl['trans_sales_no_tax'] = $this->cashier_model->get_trans_sales_no_tax(null,array('sales_id'=>$sales_ids));
            $tbl['trans_sales_payments'] = $this->cashier_model->get_trans_sales_payments(null,array('sales_id'=>$sales_ids));
            $tbl['trans_sales_tax'] = $this->cashier_model->get_trans_sales_tax(null,array('sales_id'=>$sales_ids));
            $tbl['trans_sales_zero_rated'] = $this->cashier_model->get_trans_sales_zero_rated(null,array('sales_id'=>$sales_ids));
            $tbl['trans_sales_local_tax'] = $this->cashier_model->get_trans_sales_local_tax(null,array('sales_id'=>$sales_ids));
            $tbl['reasons'] = $this->cashier_model->get_just_reasons($sales_ids);

            foreach ($tbl as $table_name => $rows) {
                if($table_name == "reasons")
                   $print .= "DELETE FROM ".$table_name." WHERE trans_id ".$check.";\r\n";
                else
                   $print .= "DELETE FROM ".$table_name." WHERE sales_id ".$check.";\r\n";
            }

            $details = array();
            foreach ($tbl as $table => $row) {
                $cl = $this->site_model->get_tbl_cols($table);
                unset($cl[0]);
                $dets = array();
                foreach ($row as $r) {
                    $det = array();
                    foreach ($cl as $c) {
                        $det[$c] = $r->$c;
                    }
                    $det['pos_id'] = TERMINAL_ID;
                    $dets[] = $det;
                    $print .= $this->db->insert_string($table,$det).";\r\n";
                }####
                $details[$table]=$dets;
            }
            #
            foreach ($details as $table => $tbl_rows) {
                if(count($tbl_rows) > 0){
                    $bargs = array();
                    if($table == 'reasons'){
                        $bargs[$table.'.trans_id'] = $sales_ids;                        
                    }
                    else
                        $bargs[$table.'.sales_id'] = $sales_ids;                        
                    $bargs[$table.'.pos_id'] = TERMINAL_ID;
                    // $this->main_model->delete_trans_tbl_batch($table,$bargs);
                    // $this->main_model->add_trans_tbl_batch($table,$tbl_rows);
                }
            }
            #
        }
        echo "<pre>".$print."</pre>";
    }
}