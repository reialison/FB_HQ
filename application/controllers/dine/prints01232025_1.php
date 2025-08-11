<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Prints extends CI_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->helper('dine/print_helper');
        $this->load->helper('core/string_helper');

        $this->load->model('dine/cashier_model');
        $this->load->model('dine/setup_model');
        $this->load->model('dine/menu_model');
        $this->load->model('dine/manager_model');
    }
    public function index(){
        $data['code'] = mainPage();
        $data['add_css'] = array('css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css');
        $data['add_js'] = array('js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js');
        $data['load_js'] = 'dine/prints';
        $data['use_js'] = 'mainPageJS';
        $this->load->view('load',$data);
    }
    ##############
    #### PAGES
        public function date_and_time(){
            $data['code'] = datetimePage();
            $data['load_js'] = 'dine/prints';
            $data['use_js'] = 'datetimeJS';
            $this->load->view('load',$data);
        }
        public function shift_sales(){
            $today = $this->site_model->get_db_now();
            $data['code'] = shiftsPage($today);
            $data['load_js'] = 'dine/prints';
            $data['use_js'] = 'sfhitJS';
            $this->load->view('load',$data);
        }
        public function get_shifts(){
            $date = $this->input->post('calendar');
            $args["DATE(shifts.check_in) = DATE('".date2Sql($date)."') "] = array('use'=>'where','val'=>null,'third'=>false);
            $join['users'] = array('content'=>'shifts.user_id = users.id');
            $select = "shifts.*,users.fname,users.lname,users.mname,users.suffix,users.username";
            $results = $this->site_model->get_tbl('shifts',$args,array('check_in'=>'desc'),$join,true,$select);
            $shifts = array();
            foreach ($results as $res) {
                $in = "";
                if($res->check_in != "")
                    $in = toTime($res->check_in);

                $out = "";
                if($res->check_out != "")
                    $out = toTime($res->check_out);
                $shifts[$res->shift_id] = array(
                                                // 'name'=>$res->fname." ".$res->mname." ".$res->lname." ".$res->suffix,
                                                'name'=>$res->username,
                                                'in'=>$in,
                                                'out'=>$out,
                                                );
            }
            $code = "";
            $this->make->sDiv(array('class'=>'listings'));
                $this->make->sUl();
                foreach ($shifts as $id => $sh) {
                    // $color = 'teal';
                    // if($sh['out'] == "")
                    //     $color = 'orange';
                    // $this->make->sDivCol(6);
                    //     $this->make->sDiv(array('style'=>'margin:5px;'));
                    //     $this->make->sBox('solid',array('ref'=>$id,'id'=>'shift-box-'.$id,'class'=>'shift-box bg-'.$color,'style'=>'cursor:pointer;-webkit-border-radius: 0px;-moz-border-radius: 0px;border-radius: 0px;padding:0px;'));
                    //         $this->make->sBoxBody();
                    //             $this->make->H(5,fa('fa-user')." ".$sh['name']);
                    //             $txt = $sh['in'];
                    //             if($sh['out'] != "")
                    //                 $txt.= " - ".$sh['out'];
                    //             $this->make->H(5,fa('fa-clock-o')." ".$txt);
                    //         $this->make->eBoxBody();
                    //     $this->make->eBox();
                    //     $this->make->eDiv();
                    // $this->make->eDivCol();
                    $this->make->sLi(array('ref'=>$id,'id'=>'shift-box-'.$id,'class'=>'shift-box bg-white','style'=>'cursor:pointer;padding:5px;padding-bottom:15px;padding-top:15px;border-bottom:1px solid #ddd;'));
                        $txt = fa('fa-clock-o')." ".$sh['in'];
                        if($sh['out'] != "")
                            $txt.= " - ".$sh['out'];
                        $this->make->H(5,fa('fa-user')." ".strtoupper($sh['name'])."<span class='pull-right'>".$txt."</span>",array('class'=>'name','style'=>'margin:0;padding:0;'));
                    $this->make->eLi();
                }
                $this->make->eUl();
            $this->make->eDiv();
            $code = $this->make->code();
            echo json_encode(array('code'=>$code,'shifts'=>$shifts));
        }
        public function end_day_sales(){
            $today = date('m/d/Y',strtotime($this->site_model->get_db_now()." -1 days") );
            $data['code'] = dayReadsPage($today);
            $data['load_js'] = 'dine/prints';
            $data['use_js'] = 'dayReadsJS';
            $this->load->view('load',$data);
        }
    ##############
    #### REPORTS
        public function payment_sales_rep($asJson=false){
            $print_str = $this->print_header();
            $user = $this->session->userdata('user');
            $time = $this->site_model->get_db_now();
            $post = $this->set_post();
            $curr = $this->search_current();

            $trans = $this->trans_sales($post['args'],$curr);
            $sales = $trans['sales'];
            $breakdown = $this->payment_breakdown_sales($sales['settled']['ids'],$curr);

            $title_name = "Payment Breakdown Report";
            $print_str .= align_center($title_name,38," ")."\r\n";
            $print_str .= align_center("TERMINAL ".$post['terminal'],38," ")."\r\n";
            $print_str .= append_chars('Printed On','right',11," ").append_chars(": ".date2SqlDateTime($time),'right',19," ")."\r\n";
            $print_str .= append_chars('Printed BY','right',11," ").append_chars(": ".$user['full_name'],'right',19," ")."\r\n";
            $print_str .= "======================================"."\r\n";
            $print_str .= align_center(sql2DateTime($post['from'])." - ".sql2DateTime($post['to']),38," ")."\r\n";
            if($post['employee'] != "All")
                $print_str .= align_center($post['employee'],38," ")."\r\n";

            // $print_str .= "\r\n";
            $all_total = 0;
            $all_ctr = 0;
            foreach ($breakdown['types'] as $ctr => $type) {
                if($type != 'cash'){
                    $ctr = 0;
                    $total = 0;
                    foreach ($breakdown['payments'] as $key => $row) {
                        if($row->payment_type == $type){
                            $ctr++;
                            if($row->amount > $row->to_pay)
                                $amount = $row->to_pay;
                            else
                                $amount = $row->amount;
                            $total += $amount;
                        }
                    }
                    $all_total += $total;
                    $all_ctr += $ctr;
                    $print_str .= "======================================"."\r\n";
                    $print_str .= append_chars(strtoupper($type),'right',18," ").append_chars($ctr,'right',10," ").append_chars(numInt($total).'','left',10," ")."\r\n";
                    $print_str .= "======================================"."\r\n";
                    foreach ($breakdown['payments'] as $key => $row) {
                        if($row->payment_type == $type){
                            $card_num = $row->card_number;
                            $card_type = $row->card_type;
                            if($row->payment_type == 'gc'){
                                $card_type = 'Gift Card';
                                $card_num = $row->reference;
                            }
                            $total_amt = $row->amount;
                            $to_pay_amt = $row->to_pay;
                            $approval_code = $row->approval_code;
                            $order_ref = $row->trans_ref;
                            $print_str .= append_chars(substrwords("Receipt: ",18,""),"right",12," ").$order_ref
                                         .append_chars('',"left",13," ")."\r\n";

                            $print_str .= append_chars(substrwords("Card: ",18,""),"right",12," ").$card_type
                                         .append_chars($card_num,"left",13," ")."\r\n";
                            if($approval_code != ""){
                                $print_str .= append_chars(substrwords("Approval:",18,""),"right",12," ").$approval_code
                                             .append_chars('',"left",13," ")."\r\n";
                            }

                            $print_str .= append_chars(substrwords("Amount: ",18,""),"right",12," ").numInt($total_amt)
                                         .append_chars('',"left",13," ")."\r\n";

                            $print_str .= append_chars(substrwords("To Pay: ",18,""),"right",12," ").numInt($to_pay_amt)
                                         .append_chars('',"left",13," ")."\r\n";
                            $print_str .= "\r\n";
                        }
                    }
                }

            }
            foreach ($breakdown['types'] as $ctr => $type) {
                if($type == 'cash'){
                    $ctr = 0;
                    $total = 0;
                    foreach ($breakdown['payments'] as $key => $row) {
                        if($row->payment_type == $type){
                            $ctr++;
                            if($row->amount > $row->to_pay)
                                $amount = $row->to_pay;
                            else
                                $amount = $row->amount;
                            $total += $amount;
                        }
                    }
                    $all_total += $total;
                    $all_ctr += $ctr;
                    $print_str .= "======================================"."\r\n";
                    $print_str .= append_chars(strtoupper($type),'right',18," ").append_chars($ctr,'right',10," ").append_chars(numInt($total).'','left',10," ")."\r\n";
                    $print_str .= "======================================"."\r\n";
                }
            }
            $print_str .= append_chars(strtoupper('total payments:'),'right',18," ").append_chars($all_ctr,'right',10," ").append_chars(numInt($all_total).'','left',10," ")."\r\n";

            // echo "<pre>".$print_str."</pre>";
            $this->do_print($print_str,$asJson);
        }
        public function gift_card_sales_rep($asJson=false){
            $print_str = $this->print_header();
            $user = $this->session->userdata('user');
            $time = $this->site_model->get_db_now();
            $post = $this->set_post();
            $curr = $this->search_current();

            $trans = $this->trans_sales($post['args'],$curr);
            $sales = $trans['sales'];
            $breakdown = $this->payment_breakdown_sales($sales['settled']['ids'],$curr);

            $title_name = "Gift Cards Breakdown Report";
            $print_str .= align_center($title_name,PAPER_WIDTH," ")."\r\n";
            $print_str .= align_center("TERMINAL ".$post['terminal'],PAPER_WIDTH," ")."\r\n";
            $print_str .= append_chars('Printed On','right',11," ").append_chars(": ".date2SqlDateTime($time),'right',19," ")."\r\n";
            $print_str .= append_chars('Printed BY','right',11," ").append_chars(": ".$user['full_name'],'right',19," ")."\r\n";
            $print_str .= PAPER_LINE."\r\n";
            $print_str .= align_center(sql2DateTime($post['from'])." - ".sql2DateTime($post['to']),38," ")."\r\n";
            if($post['employee'] != "All")
                $print_str .= align_center($post['employee'],38," ")."\r\n";
            $print_str .= PAPER_LINE."\r\n";
            // $print_str .= "\r\n";
            $all_total = 0;
            $all_ctr = 0;
            foreach ($breakdown['types'] as $ctr => $type) {
                if($type == 'gc'){
                    $ctr = 0;
                    $total = 0;
                    $ov_total = 0;
                    foreach ($breakdown['payments'] as $key => $row) {
                        if($row->payment_type == $type){
                            $ctr++;
                            if($row->amount > $row->to_pay)
                                $amount = $row->to_pay;
                            else
                                $amount = $row->amount;
                            $total += $amount;
                        }
                    }
                    foreach ($breakdown['payments'] as $key => $row) {
                        if($row->payment_type == $type){
                            $amount = $row->amount;
                            $ov_total += $amount;
                        }
                    }

                    $all_total += $total;
                    $all_ctr += $ctr;

                    foreach ($breakdown['payments'] as $key => $row) {
                        if($row->payment_type == $type){
                            $card_num = $row->card_number;
                            $card_type = $row->card_type;
                            if($row->payment_type == 'gc'){
                                $card_type = 'Gift Card';
                                $card_num = $row->reference;
                            }
                            $total_amt = $row->amount;
                            $to_pay_amt = $row->to_pay;
                            $approval_code = $row->approval_code;
                            $order_ref = $row->trans_ref;
                            $print_str .= append_chars(substrwords("Receipt: ",18,""),"right",12," ").$order_ref
                                         .append_chars('',"left",13," ")."\r\n";
                            $print_str .= append_chars(substrwords("Card No.: ",18,""),"right",12," ").$card_num
                                         .append_chars('',"left",13," ")."\r\n";

                            if($approval_code != ""){
                                $print_str .= append_chars(substrwords("Approval:",18,""),"right",12," ").$approval_code
                                             .append_chars('',"left",13," ")."\r\n";
                            }

                            $print_str .= append_chars(substrwords("Card Amount: ",18,""),"right",12," ").numInt($total_amt)
                                         .append_chars('',"left",13," ")."\r\n";

                            $print_str .= append_chars(substrwords("Amount Due: ",18,""),"right",12," ").numInt($to_pay_amt)
                                         .append_chars('',"left",13," ")."\r\n";
                            $print_str .= "\r\n";
                        }
                    }
                    $print_str .= PAPER_LINE."\r\n";
                    // $print_str .= append_chars(strtoupper("TOTAL"),'right',18," ").append_chars($ctr,'right',10," ").append_chars(numInt($ov_total).'','left',10," ")."\r\n";
                    $print_str .= append_chars(substrwords('TOTAL',18,""),"right",PAPER_RD_COL_1," ").align_center($ctr,PAPER_RD_COL_2," ")
                        .append_chars(numInt($ov_total),"left",PAPER_RD_COL_3_3," ")."\r\n\r\n";
                    $print_str .= PAPER_LINE."\r\n";
                }

            }

            // echo "<pre>".$print_str."</pre>";
            $this->do_print($print_str,$asJson);
        }
        public function list_sales_rep($asJson=false){
            $print_str = $this->print_header();
            $user = $this->session->userdata('user');
            $time = $this->site_model->get_db_now();
            $post = $this->set_post();
            $curr = $this->search_current();
            $trans = $this->trans_sales($post['args'],$curr);
            $sales = $trans['sales'];
            $settled = $trans['sales']['settled']['orders'];
            usort($settled, function($a, $b) {
                return $a->trans_ref - $b->trans_ref;
            });

            $title_name = "Transactions List";
            if($post['title'] != "")
                $title_name = $post['title'];

            $print_str .= align_center($title_name,PAPER_WIDTH," ")."\r\n";
            $print_str .= align_center("TERMINAL ".$post['terminal'],PAPER_WIDTH," ")."\r\n";
            $print_str .= append_chars('Printed On','right',11," ").append_chars(": ".date2SqlDateTime($time),'right',19," ")."\r\n";
            $print_str .= append_chars('Printed BY','right',11," ").append_chars(": ".$user['full_name'],'right',19," ")."\r\n";
            $print_str .= PAPER_LINE."\r\n";
            $print_str .= align_center(sql2DateTime($post['from'])." - ".sql2DateTime($post['to']),PAPER_WIDTH," ")."\r\n";
            if($post['employee'] != "All")
                $print_str .= align_center($post['employee'],PAPER_WIDTH," ")."\r\n";

            $print_str .= "\r\n";
            // $total_vat = 0;
            // $total_taxable = 0;
            foreach ($settled as $key => $set){
                // $trans_menus = $this->menu_sales($sales_id,$curr);
                $trans_charges = $this->charges_sales($set->sales_id,$curr);
                $trans_discounts = $this->discounts_sales($set->sales_id,$curr);
                $trans_local_tax = $this->local_tax_sales($set->sales_id,$curr);
                $trans_tax = $this->tax_sales($set->sales_id,$curr);
                $trans_no_tax = $this->no_tax_sales($set->sales_id,$curr);
                $trans_zero_rated = $this->zero_rated_sales($set->sales_id,$curr);

                $print_str .= align_center($set->trans_ref,PAPER_WIDTH," ")."\r\n";
                $print_str .= align_center($set->datetime,PAPER_WIDTH," ")."\r\n";

                $net = $set->total_amount;
                $charges = $trans_charges['total'];
                $discounts = $trans_discounts['total'];
                $tax_disc = $trans_discounts['tax_disc_total'];
                $no_tax_disc = $trans_discounts['no_tax_disc_total'];
                $local_tax = $trans_local_tax['total'];

                $tax = $trans_tax['total'];
                $no_tax = $trans_no_tax['total'];

                $zero_rated = $trans_zero_rated['total'];
                $no_tax -= $zero_rated;
                $net_no_adds = ($net)-$charges-$local_tax;

                $taxable = ( ($net_no_adds + $no_tax_disc) - ($tax + $no_tax));
                // $print_str .= append_chars(substrwords('VAT SALES',18,""),"right",23," ")
                //              .append_chars(numInt(($taxable)),"left",13," ")."\r\n";
                $print_str .= append_chars(substrwords('VAT SALES',18,""),"right",PAPER_TOTAL_COL_1," ")
                                     .append_chars(numInt($taxable),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $total_net = $taxable + $no_tax + $zero_rated + $tax;
                $print_str .= append_chars(substrwords('VAT EXEMPT SALES',18,""),"right",PAPER_TOTAL_COL_1," ")
                             .append_chars(numInt(($no_tax)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords('ZERO RATED',13,""),"right",PAPER_TOTAL_COL_1," ")
                             .append_chars(numInt(($zero_rated)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords('VAT',18,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(numInt(($tax)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars("","right",23," ").append_chars("----------","left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords('Total',18,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(numInt(($total_net)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords('Charges',18,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(numInt(($charges)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords('Local Tax',18,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(numInt(($local_tax)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords('Discounts',18,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(numInt(($discounts)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= PAPER_LINE."\r\n";
                $print_str .= append_chars(substrwords('NET SALES',18,""),"right",PAPER_TOTAL_COL_1," ")
                             .append_chars(numInt(($set->total_amount)),"left",PAPER_TOTAL_COL_2," ")."\r\n";

                $print_str .= "\r\n";
                // $total_vat += $tax;
                // $total_taxable += $taxable;
            }
            // $print_str .= append_chars(substrwords('VAT',18,""),"right",23," ")
            //  .append_chars(numInt(($total_vat)),"left",13," ")."\r\n";
            // $print_str .= append_chars(substrwords('TAXABLE',18,""),"right",23," ")
            //  .append_chars(numInt(($total_taxable)),"left",13," ")."\r\n";

            ###########
            $this->do_print($print_str,$asJson);
        }
        public function void_sales_rep($asJson=false){
            $print_str = $this->print_header();
            $user = $this->session->userdata('user');
            $time = $this->site_model->get_db_now();
            $post = $this->set_post();
            $curr = $this->search_current();
            $trans = $this->trans_sales($post['args'],$curr);
            $sales = $trans['sales'];
            $trans_removes = $this->removed_menu_sales($trans['all_ids'],$curr);
            $void = array();
            $cancel = array();
            if(isset($trans['sales']['void']['orders']) && count($trans['sales']['void']['orders']) > 0)
                $void = $trans['sales']['void']['orders'];
            if(isset($trans['sales']['cancel']['orders']) && count($trans['sales']['cancel']['orders']) > 0)
                $cancel = $trans['sales']['cancel']['orders'];

            $title_name = "VOID SALES REPORT";
            $print_str .= align_center($title_name,PAPER_WIDTH," ")."\r\n";
            $print_str .= align_center("TERMINAL ".$post['terminal'],PAPER_WIDTH," ")."\r\n";
            $print_str .= append_chars('Printed On','right',11," ").append_chars(": ".date2SqlDateTime($time),'right',19," ")."\r\n";
            $print_str .= append_chars('Printed BY','right',11," ").append_chars(": ".$user['full_name'],'right',19," ")."\r\n";
            $print_str .= PAPER_LINE."\r\n";
            $print_str .= align_center(sql2DateTime($post['from'])." - ".sql2DateTime($post['to']),38," ")."\r\n";
            if($post['employee'] != "All")
                $print_str .= align_center($post['employee'],38," ")."\r\n";


            $print_str .= "\r\n".append_chars(substrwords('Voided Receipts',18,""),"right",18," ").align_center(null,5," ")
                       .append_chars(null,"left",13," ")."\r\n";
            $print_str .= PAPER_LINE."\r\n";
            $total_void_sales = 0;
            if(count($void) > 0){
                foreach ($void as $v) {
                    $order = $trans['all_orders'];
                    #TATLO
                     //  $print_str .= append_chars(substrwords('TOTAL PAYMENTS',18,""),"right",PAPER_RD_COL_1," ").align_center($pay_qty,PAPER_RD_COL_2," ")
                     //      .append_chars(numInt($payments_total),"left",PAPER_RD_COL_3_3," ")."\r\n\r\n";
                    // $print_str .= append_chars(substrwords($v->trans_ref,18,""),"right",18," ").align_center('',5," ")
                    //          .append_chars(numInt($v->total_amount),"left",13," ")."\r\n";
                    #DALAWA
                    $print_str .= append_chars(substrwords($v->trans_ref,18,""),"right",PAPER_TOTAL_COL_1," ")
                                     .append_chars(numInt($v->total_amount),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                    if(isset($order[$v->void_ref])){
                        $ord = $order[$v->void_ref];
                        $print_str .= append_chars(substrwords("Receipt: ",PAPER_RD_COL_1,""),"right",PAPER_RD_COL_1," ").align_center($ord->trans_ref,PAPER_RD_COL_3_3," ")
                                 .append_chars('',"left",13," ")."\r\n";
                    }
                    if($v->table_name != ""){
                        $print_str .= append_chars(substrwords("Table: ",PAPER_RD_COL_1,""),"right",PAPER_RD_COL_1," ").align_center($v->table_name,PAPER_RD_COL_3_3," ")
                                 .append_chars('',"left",13," ")."\r\n";
                    }
                    $server = $this->manager_model->get_server_details($v->user_id);
                    $cashier = $server[0]->username;
                    $print_str .= append_chars(substrwords("Cashier: ",PAPER_RD_COL_1,""),"right",PAPER_RD_COL_1," ").align_center($cashier,PAPER_RD_COL_3_3," ")
                             .append_chars('',"left",13," ")."\r\n";
                    if(isset($order[$v->void_ref])){
                        $ord = $order[$v->void_ref];
                        $print_str .= append_chars(substrwords("Reason: ",18,""),"right",12," ").align_center('',5," ")
                                 .append_chars('',"left",13," ")."\r\n";
                        $print_str .= append_chars("--".$ord->reason,"right",PAPER_RD_COL_1," ").align_center('',5," ")
                                 .append_chars('',"left",13," ")."\r\n";
                        if($ord->void_user_id != ""){
                            $server = $this->manager_model->get_server_details($ord->void_user_id);
                            $voider = $server[0]->username;
                            $print_str .= append_chars(substrwords("Approved By: ",PAPER_RD_COL_1,""),"right",PAPER_RD_COL_1," ").align_center($voider,PAPER_RD_COL_3_3," ")
                                     .append_chars('',"left",13," ")."\r\n";
                        }
                    }
                    $total_void_sales += $v->total_amount;
                }
            }
            else{
                $print_str .= append_chars(substrwords("No Sales Found.",PAPER_RD_COL_1,""),"right",18," ").align_center('',5," ")."\r\n";
            }
            $print_str .= "-----------------"."\r\n";
            $print_str .= append_chars(substrwords('Total',18,""),"right",PAPER_TOTAL_COL_1," ")
                                     .append_chars(numInt($total_void_sales),"left",PAPER_TOTAL_COL_2," ")."\r\n";

            $print_str .= "\r\n".append_chars(substrwords('Cancelled Orders',18,""),"right",18," ").align_center(null,5," ")
                       .append_chars(null,"left",13," ")."\r\n";
            $print_str .= PAPER_LINE."\r\n";
            $total_void_sales = 0;
            if(count($cancel) > 0){
                foreach ($cancel as $v) {
                    $print_str .= append_chars(substrwords('Order #'.$v->sales_id,18,""),"right",PAPER_TOTAL_COL_1," ")
                                     .append_chars(numInt($v->total_amount),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                    $server = $this->manager_model->get_server_details($v->user_id);
                    $cashier = $server[0]->username;
                    $print_str .= append_chars(substrwords("Cashier: ",PAPER_RD_COL_1,""),"right",PAPER_RD_COL_1," ").align_center($cashier,PAPER_RD_COL_3_3," ")
                             .append_chars('',"left",13," ")."\r\n";

                    if($v->table_name != ""){
                        $print_str .= append_chars(substrwords("Table: ",PAPER_RD_COL_1,""),"right",PAPER_RD_COL_1," ").align_center($v->table_name,PAPER_RD_COL_3_3," ")
                                 .append_chars('',"left",13," ")."\r\n";
                    }
                    $print_str .= append_chars(substrwords("Reason: ",PAPER_RD_COL_1,""),"right",PAPER_RD_COL_1," ").align_center('',5," ")
                             .append_chars('',"left",13," ")."\r\n";
                    $print_str .= append_chars("--".$v->reason,"right",PAPER_RD_COL_1," ").align_center('',5," ")
                             .append_chars('',"left",13," ")."\r\n";
                    if($v->void_user_id != ""){
                        $server = $this->manager_model->get_server_details($v->void_user_id);
                        $voider = $server[0]->username;
                        $print_str .= append_chars(substrwords("Approved By: ",PAPER_RD_COL_1,""),"right",PAPER_RD_COL_1," ").align_center($voider,10," ")
                                 .append_chars('',"left",13," ")."\r\n";
                    }

                    $total_void_sales += $v->total_amount;
                }
            }
            else{
                $print_str .= append_chars(substrwords("No Sales Found. ",PAPER_RD_COL_1,""),"right",18," ").align_center('',5," ")."\r\n";
            }
            $print_str .= "-----------------"."\r\n";
            $print_str .= append_chars(substrwords('Total',18,""),"right",PAPER_TOTAL_COL_1," ")
                                     .append_chars(numInt($total_void_sales),"left",PAPER_TOTAL_COL_2," ")."\r\n";

            $print_str .= "\r\n".append_chars(substrwords('Removed Items',18,""),"right",18," ").align_center(null,5," ")
                       .append_chars(null,"left",13," ")."\r\n";
            $print_str .= "-----------------"."\r\n";

            if(count($trans_removes) > 0){
                foreach ($trans_removes as $v) {
                    $print_str .= append_chars(substrwords("Order #".$v['trans_id'],18,""),"right",PAPER_TOTAL_COL_1," ")
                                     .append_chars(null,"left",PAPER_TOTAL_COL_2," ")."\r\n";
                    $print_str .= append_chars(substrwords("Cashier: ",PAPER_RD_COL_1,""),"right",PAPER_RD_COL_1," ").align_center($v['cashier'],PAPER_RD_COL_3_3," ")
                             .append_chars('',"left",13," ")."\r\n";
                    $print_str .= append_chars("*".(string)$v['item'],"right",PAPER_RD_COL_1," ").align_center($v['cashier'],PAPER_RD_COL_3_3," ")
                             .append_chars('',"left",13," ")."\r\n";
                    $print_str .= " ".urldecode($v['reason'])."\r\n";
                    $print_str .= append_chars(substrwords("Approved By: ",PAPER_RD_COL_1,""),"right",PAPER_RD_COL_1," ").align_center($v['manager'],PAPER_RD_COL_3_3," ")
                             .append_chars('',"left",13," ")."\r\n";

                }
            }
            else{
                $print_str .= append_chars(substrwords("No Menus Found. ",18,""),"right",18," ").align_center('',5," ");
            }
            $this->do_print($print_str,$asJson);
        }
        public function menu_sales_rep($asJson=false){
            $print_str = $this->print_header();
            $user = $this->session->userdata('user');
            $time = $this->site_model->get_db_now();
            $post = $this->set_post();
            $curr = $this->search_current();
            $trans = $this->trans_sales($post['args'],$curr);
            $sales = $trans['sales'];
            $trans_menus = $this->menu_sales($sales['settled']['ids'],$curr);
            $trans_charges = $this->charges_sales($sales['settled']['ids'],$curr);
            $trans_discounts = $this->discounts_sales($sales['settled']['ids'],$curr);
            $trans_local_tax = $this->local_tax_sales($sales['settled']['ids'],$curr);
            $trans_tax = $this->tax_sales($sales['settled']['ids'],$curr);
            $trans_no_tax = $this->no_tax_sales($sales['settled']['ids'],$curr);
            $trans_zero_rated = $this->zero_rated_sales($sales['settled']['ids'],$curr);
            $payments = $this->payment_sales($sales['settled']['ids'],$curr);
            $gross = $trans_menus['gross'];
            $net = $trans['net'];
            $charges = $trans_charges['total'];
            $discounts = $trans_discounts['total'];
            $local_tax = $trans_local_tax['total'];
            $less_vat = (($gross+$charges+$local_tax) - $discounts) - $net;
            if($less_vat < 0)
                $less_vat = 0;

            $tax = $trans_tax['total'];
            $no_tax = $trans_no_tax['total'];
            $zero_rated = $trans_zero_rated['total'];
            $no_tax -= $zero_rated;

            $title_name = "MENU ITEM SALES REPORT";
            $print_str .= align_center($title_name,PAPER_WIDTH," ")."\r\n";
            $print_str .= align_center("TERMINAL ".$post['terminal'],PAPER_WIDTH," ")."\r\n";
            $print_str .= append_chars('Printed On','right',11," ").append_chars(": ".date2SqlDateTime($time),'right',19," ")."\r\n";
            $print_str .= append_chars('Printed BY','right',11," ").append_chars(": ".$user['full_name'],'right',19," ")."\r\n";
            $print_str .= PAPER_LINE."\r\n";
            $print_str .= align_center(sql2DateTime($post['from'])." - ".sql2DateTime($post['to']),PAPER_WIDTH," ")."\r\n";
            if($post['employee'] != "All")
                $print_str .= align_center($post['employee'],PAPER_WIDTH," ")."\r\n";

            $print_str .= "\r\n";
            #CATEGORIES
                $cats = $trans_menus['cats'];
                $menus = $trans_menus['menus'];
                $menu_total = $trans_menus['menu_total'];
                $total_qty = $trans_menus['total_qty'];
                usort($cats, function($a, $b) {
                    return $b['amount'] - $a['amount'];
                });
                foreach ($cats as $cat_id => $ca) {
                    if($ca['qty'] > 0){
                        // $print_str .=
                        //      append_chars($ca['name'],'right',18," ")
                        //     .append_chars(num($ca['qty']),'right',10," ")
                        //     .append_chars(num($ca['amount']).'','left',10," ")."\r\n";
                        $print_str .= append_chars($ca['name'],"right",PAPER_RD_COL_1," ").align_center(num($ca['qty']),PAPER_RD_COL_2," ")
                            .append_chars(num($ca['amount']).'',"left",9," ")."\r\n";
                        $print_str .= PAPER_LINE."\r\n";
                        foreach ($menus as $menu_id => $res) {
                            if($ca['cat_id'] == $res['cat_id']){
                            $print_str .=
                                append_chars($res['name'],'right',PAPER_RD_COL_1," ")
                                .append_chars(num($res['qty']),'right',PAPER_RD_COL_2," ")
                                .append_chars(num( ($res['qty'] / $total_qty) * 100).'%','left',9," ")."\r\n";
                            $print_str .=
                                append_chars(null,'right',PAPER_RD_COL_1," ")
                                .append_chars(num($res['amount']),'right',PAPER_RD_COL_2," ")
                                .append_chars(num( ($res['amount'] / $menu_total) * 100).'%','left',9," ")."\r\n";
                            }
                        }
                    $print_str .= PAPER_LINE."\r\n";
                    }
                }
            #SUBCATEGORIES
            $print_str .= "\r\n";
                $subcats = $trans_menus['sub_cats'];
                $print_str .= PAPER_LINE."\r\n";
                $print_str .= append_chars('SUBCATEGORIES:',"right",18," ").align_center('',5," ")
                             .append_chars('',"left",13," ")."\r\n";
                $print_str .= PAPER_LINE."\r\n";
                $qty = 0;
                $total = 0;
                foreach ($subcats as $id => $val) {
                    $print_str .= append_chars($val['name'],"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                               .append_chars(numInt($val['amount']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $qty += $val['qty'];
                    $total += $val['amount'];
                 }
            #MODIFIERS
            $print_str .= "\r\n";
                $mods = $trans_menus['mods'];
                $print_str .= PAPER_LINE."\r\n";
                $print_str .= append_chars('MODIFIERS:',"right",18," ").align_center('',5," ")
                             .append_chars('',"left",13," ")."\r\n";
                $print_str .= PAPER_LINE."\r\n";
                $qty = 0;
                $total = 0;
                foreach ($mods as $id => $val) {
                    $print_str .= append_chars($val['name'],"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                               .append_chars(numInt($val['total_amt']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $qty += $val['qty'];
                    $total += $val['total_amt'];
                 }

            $print_str .= "\r\n".PAPER_LINE."\r\n";
            $net_no_adds = $net-$charges-$local_tax;
            $print_str .= append_chars(substrwords('TOTAL SALES',18,""),"right",PAPER_TOTAL_COL_1," ")
                         .append_chars(numInt(($net)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            $txt = numInt(($charges));
            if($charges > 0)
                $txt = "(".numInt(($charges)).")";
            $print_str .= append_chars(substrwords('Charges',18,""),"right",PAPER_TOTAL_COL_1," ")
                         .append_chars($txt,"left",PAPER_TOTAL_COL_2," ")."\r\n";

            $txt = numInt(($local_tax));
            if($local_tax > 0)
                $txt = "(".numInt(($local_tax)).")";
            // $print_str .= append_chars(substrwords('Local Tax',18,""),"right",25," ")
            //              .append_chars($txt,"left",13," ")."\r\n";
            $print_str .= append_chars(substrwords('Local Tax',18,""),"right",PAPER_TOTAL_COL_1," ")
                            .append_chars(numInt($txt),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            $print_str .= append_chars(substrwords('Discounts',18,""),"right",PAPER_TOTAL_COL_1," ")
                         .append_chars(numInt(($discounts)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            $print_str .= append_chars(substrwords('LESS VAT',18,""),"right",PAPER_TOTAL_COL_1," ")
                         .append_chars(numInt(($less_vat)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            $print_str .= append_chars(substrwords('GROSS SALES',18,""),"right",PAPER_TOTAL_COL_1," ")
                         .append_chars(numInt(($gross)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            $print_str .= PAPER_LINE."\r\n";

            $this->do_print($print_str,$asJson);
        }
        public function system_sales_rep_peri($asJson=false){
            //periperi
            $print_str = $this->print_header();
            $user = $this->session->userdata('user');
            $time = $this->site_model->get_db_now();
            $post = $this->set_post();
            $curr = $this->search_current();
            // $curr = true;
            $trans = $this->trans_sales($post['args'],$curr);
            $sales = $trans['sales'];
            $trans_menus = $this->menu_sales($sales['settled']['ids'],$curr);
            $trans_charges = $this->charges_sales($sales['settled']['ids'],$curr);
            $trans_discounts = $this->discounts_sales($sales['settled']['ids'],$curr);
            $tax_disc = $trans_discounts['tax_disc_total'];
            $no_tax_disc = $trans_discounts['no_tax_disc_total'];
            $trans_local_tax = $this->local_tax_sales($sales['settled']['ids'],$curr);
            $trans_tax = $this->tax_sales($sales['settled']['ids'],$curr);
            $trans_no_tax = $this->no_tax_sales($sales['settled']['ids'],$curr);
            $trans_zero_rated = $this->zero_rated_sales($sales['settled']['ids'],$curr);
            $payments = $this->payment_sales($sales['settled']['ids'],$curr);



            // $gross = $trans_menus['gross'] - $trans['void'];
            $gross = $trans_menus['gross'];
            $net = $trans['net'];
            $void = $trans['void'];
            $charges = $trans_charges['total'];
            $discounts = $trans_discounts['total'];
            $local_tax = $trans_local_tax['total'];
            $less_vat = (($gross+$charges+$local_tax) - $discounts) - $net;
            // $less_vat  = $no_tax_disc * 0.60;
            if($less_vat < 0)
                $less_vat = 0;

            $tax = $trans_tax['total'];
            $no_tax = $trans_no_tax['total'];
            $zero_rated = $trans_zero_rated['total'];
            $no_tax -= $zero_rated;

            $title_name = "SYSTEM SALES REPORT";
            if($post['title'] != "")
                $title_name = $post['title'];

            $print_str .= align_center($title_name,38," ")."\r\n";
            $print_str .= align_center("TERMINAL ".$post['terminal'],38," ")."\r\n";
            $print_str .= append_chars('Printed On','right',11," ").append_chars(": ".date2SqlDateTime($time),'right',19," ")."\r\n";
            $print_str .= append_chars('Printed BY','right',11," ").append_chars(": ".$user['full_name'],'right',19," ")."\r\n";
            $print_str .= "======================================"."\r\n";
            $print_str .= align_center(sql2DateTime($post['from'])." - ".sql2DateTime($post['to']),38," ")."\r\n";
            if($post['employee'] != "All")
                $print_str .= align_center($post['employee'],38," ")."\r\n";
            $print_str .= "======================================"."\r\n";
            $print_str .= append_chars('SETTLEMENT','right',11," ")."\r\n";
            $print_str .= "--------------------------------------"."\r\n";

            $loc_txt = numInt(($local_tax));
            // if($local_tax > 0)
            //     $loc_txt = "(".numInt(($local_tax)).")";
            $net_no_adds = $net-($charges+$local_tax);
            $nontaxable = $no_tax - $no_tax_disc;
            // $taxable =   ($net_no_adds - ($tax + ($nontaxable+$zero_rated))  );
            $taxable =   ($gross - $discounts - $less_vat - $nontaxable) / 1.12;
            // $taxable =   $discounts;
            $total_net = ($taxable) + ($nontaxable+$zero_rated) + $tax + $local_tax;
            $add_gt = $taxable+$nontaxable+$zero_rated;
            // $taxable = ($net_no_adds - ($tax + $no_tax + $zero_rated));
            $nsss = $taxable +  $nontaxable +  $zero_rated;
            $print_str .= append_chars(substrwords('GROSS SALES',18,""),"right",23," ")
                                     .append_chars(numInt(($gross)),"left",13," ")."\r\n";
            $print_str .= append_chars(substrwords('LESS : TOTAL DISCOUNTS',24,""),"right",24," ")
                                     .append_chars('',"left",13," ")."\r\n";
            #Discounts
            $types = $trans_discounts['types'];
            $qty = 0;
            // $print_str .= append_chars(substrwords('Discounts:',18,""),"right",18," ").align_center(null,5," ")
            //               .append_chars(null,"left",13," ")."\r\n";
            foreach ($types as $code => $val) {
                $print_str .= append_chars(substrwords(ucwords(strtoupper($val['name'])),18,""),"left",18," ").align_center('',5," ")
                              .append_chars('('.Num($val['amount'],2).')',"left",13," ")."\r\n";
                $qty += $val['qty'];
            }
            $print_str .= append_chars(substrwords(ucwords(strtoupper('SEN. VAT EXEMPT')),18,""),"left",18," ").align_center('',5," ")
                              .append_chars('('.numInt($less_vat).")","left",13," ")."\r\n";
            $print_str .= "--------------------------------------"."\r\n";
            $final_gross = $gross + $charges;
            $print_str .= append_chars(substrwords('GROSS NET OF DISCOUNT',23,""),"right",23," ")
                                     .append_chars(numInt(($gross - $discounts - $less_vat)),"left",13," ")."\r\n";
            $print_str .= "--------------------------------------"."\r\n";
            #PAYMENTS
            // $payments_types = $payments['types'];
            // $payments_total = $payments['total'];
            // $pay_qty = 0;
            // // $print_str .= append_chars(substrwords('Payment Breakdown:',18,""),"right",18," ").align_center(null,5," ")
            // //               .append_chars(null,"left",13," ")."\r\n";
            // foreach ($payments_types as $code => $val) {
            //     $print_str .= append_chars(substrwords(ucwords(strtoupper($code)),18,""),"right",18," ").align_center($val['qty'],5," ")
            //                   .append_chars(numInt($val['amount']),"left",13," ")."\r\n";
            //     $pay_qty += $val['qty'];
            // }
            // //$print_str .= "--------------------------------------"."\r\n";
            // $print_str .= append_chars("","right",18," ").align_center("",5," ")
            //               .append_chars('-----------',"left",13," ")."\r\n";
            // $print_str .= append_chars(substrwords('TOTAL PAYMENTS',18,""),"right",18," ").align_center($pay_qty,5," ")
            //               .append_chars(numInt($payments_total),"left",13," ")."\r\n";
            $payments_types = $payments['ov_types'];
            $payments_total = $payments['ov_total'];
            $net_payments_total = $payments['total'];
            $pay_qty = 0;
            $print_str .= append_chars(substrwords('Payment Breakdown:',18,""),"right",18," ").align_center(null,5," ")
                          .append_chars(null,"left",13," ")."\r\n";
            foreach ($payments_types as $code => $val) {
                $print_str .= append_chars(substrwords(ucwords(strtolower($code)),18,""),"right",18," ").align_center($val['qty'],5," ")
                              .append_chars(numInt($val['amount']),"left",13," ")."\r\n";
                $pay_qty += $val['qty'];
            }
            $print_str .= "-----------------"."\r\n";
            $print_str .= append_chars(substrwords('Total Payment',18,""),"right",18," ").align_center($pay_qty,5," ")
                          .append_chars(numInt($payments_total),"left",13," ")."\r\n";
            $print_str .= append_chars(substrwords("GC Excess Amount",18,""),"right",18," ").align_center("",5," ")
                          .append_chars("(".numInt($payments_total - $net_payments_total).")","left",13," ")."\r\n";
            $print_str .= append_chars(substrwords('Net Payment',18,""),"right",18," ").align_center("",5," ")
                          .append_chars(numInt($net_payments_total),"left",13," ")."\r\n";


            $print_str .= "======================================"."\r\n";
            $print_str .= append_chars('SALES ACCOUNTING','right',11," ")."\r\n\r\n";
            //$print_str .= "--------------------------------------"."\r\n";

            $print_str .= append_chars(substrwords('GROSS NET OF DISCOUNT',23,""),"right",23," ")
                                     .append_chars(numInt(($gross - $discounts - $less_vat)),"left",13," ")."\r\n";
            // $print_str .= append_chars(substrwords('ADD : TOTAL CHARGES',23,""),"right",23," ")
            //                          .append_chars('',"left",13," ")."\r\n";
            // $print_str .= append_chars("","right",18," ").align_center("",5," ")
            //               .append_chars('-----------',"left",13," ")."\r\n";
            // $print_str .= append_chars(substrwords('GROSS LESS DISCOUNT',23,""),"right",23," ")
            //                          .append_chars(numInt(($final_gross - $discounts - $less_vat)),"left",13," ")."\r\n";
            //$print_str .= "--------------------------------------"."\r\n";
            // $print_str .= append_chars("","right",18," ").align_center("",5," ")
            //               .append_chars('-----------',"left",13," ")."\r\n";

            #CHARGES
            $types = $trans_charges['types'];
            $qty = 0;
            // $print_str .= append_chars(substrwords('Charges:',18,""),"right",18," ").align_center(null,5," ")
            //               .append_chars(null,"left",13," ")."\r\n";
            $print_str .= append_chars(substrwords('ADD : TOTAL CHARGES',23,""),"right",23," ").align_center('',5," ")
                          .append_chars('',"left",13," ")."\r\n";
            foreach ($types as $code => $val) {
                $print_str .= append_chars(substrwords(ucwords(strtoupper($val['name'])),18,""),"left",18," ").align_center('',5," ")
                              .append_chars(numInt($val['amount']),"left",13," ")."\r\n";
                $qty += $val['qty'];
            }
            $print_str .= append_chars(substrwords(ucwords(strtoupper('Local Tax')),18,""),"left",18," ").align_center('',5," ")
                          .append_chars(numInt($local_tax),"left",13," ")."\r\n";
            // $print_str .= "-----------------"."\r\n";
            // $print_str .= "\r\n";

            $vat_ = $taxable * .12;
            // $print_str .= append_chars(substrwords('VAT',18,""),"right",18," ").align_center('',5," ")
            //               .append_chars(numInt($vat_),"left",13," ")."\r\n";
            $print_str .= "--------------------------------------"."\r\n";
            // $print_str .= append_chars("","right",18," ").align_center("",5," ")
            //               .append_chars('-----------',"left",13," ")."\r\n";
            $gross_net_disc = $gross - $discounts - $less_vat;
            $print_str .= append_chars(substrwords('GROSS NET OF DISCOUNT',23,""),"right",23," ")
                                     .append_chars(numInt(($gross_net_disc + $charges + $local_tax)),"left",13," ")."\r\n";
            $print_str .= append_chars(substrwords('WITH CHARGES',23,""),"right",23," ")
                                     .append_chars('',"left",13," ")."\r\n";
            $print_str .= "======================================"."\r\n";
            $print_str .= append_chars(substrwords('VAT SALES',23,""),"right",23," ")
                                     .append_chars(numInt($taxable),"left",13," ")."\r\n";
            $print_str .= append_chars(substrwords('VAT',23,""),"right",23," ")
                                     .append_chars(numInt($vat_),"left",13," ")."\r\n";
            $print_str .= append_chars(substrwords('VAT EXEMPT SALES',23,""),"right",23," ")
                                     .append_chars(numInt($nontaxable-$zero_rated),"left",13," ")."\r\n";
            $print_str .= append_chars(substrwords('ZERO RATED',23,""),"right",23," ")
                                     .append_chars(numInt($zero_rated),"left",13," ")."\r\n";
            $print_str .= "--------------------------------------"."\r\n";
            $gross_less_disc = $final_gross - $discounts - $less_vat;
            $print_str .= append_chars(substrwords('NET SALES',23,""),"right",23," ")
                                     .append_chars(numInt(($taxable + $nontaxable + $vat_)),"left",13," ")."\r\n";
                                     // .append_chars(numInt(($taxable + $nontaxable + $zero_rated + $vat_)),"left",13," ")."\r\n";
            $print_str .= "======================================"."\r\n";
            $print_str .= append_chars(substrwords('VOID SALES',18,""),"right",23," ")
                         .append_chars(numInt(($void)),"left",13," ")."\r\n";


            // $print_str .= "-----------------"."\r\n";
            // $print_str .= append_chars(substrwords('Total Discounts',18,""),"right",18," ").align_center($qty,5," ")
            //               .append_chars(numInt($discounts),"left",13," ")."\r\n";
            // $print_str .= append_chars(substrwords('VAT EXEMPT',18,""),"right",23," ")
            //                          .append_chars(numInt($less_vat),"left",13," ")."\r\n";
            // $print_str .= "\r\n";


            // $print_str .= append_chars(substrwords('NET SALES',18,""),"right",23," ")
            //              .append_chars(numInt(($nsss)),"left",13," ")."\r\n";
            // $print_str .= append_chars(substrwords('VAT',18,""),"right",23," ")
            //                          .append_chars(numInt($tax),"left",13," ")."\r\n";
            // $print_str .= append_chars(substrwords('Local Tax',18,""),"right",23," ")
            //              .append_chars($loc_txt,"left",13," ")."\r\n";
            // $print_str .= append_chars("","right",23," ").append_chars("-----------","left",13," ")."\r\n";
            // $print_str .= append_chars(substrwords('VOID SALES',18,""),"right",23," ")
            //              .append_chars(numInt(($void)),"left",13," ")."\r\n";



            $print_str .= "\r\n";
            #TRANS COUNT
                $types = $trans['types'];
                $types_total = array();
                $guestCount = 0;
                foreach ($types as $type => $tp) {
                    foreach ($tp as $id => $opt){
                        if(isset($types_total[$type])){
                            $types_total[$type] += $opt->total_amount;

                        }
                        else{
                            $types_total[$type] = $opt->total_amount;
                        }
                        if($opt->guest == 0)
                            $guestCount += 1;
                        else
                            $guestCount += $opt->guest;
                    }
                }
                $print_str .= append_chars(substrwords('Trans Count:',18,""),"right",18," ").align_center('',5," ")
                             .append_chars('',"left",13," ")."\r\n";
                $tc_total  = 0;
                $tc_qty = 0;
                foreach ($types_total as $typ => $tamnt) {
                    $print_str .= append_chars(substrwords($typ,18,""),"right",18," ").align_center(count($types[$typ]),5," ")
                                 .append_chars(numInt($tamnt),"left",13," ")."\r\n";
                    $tc_total += $tamnt;
                    $tc_qty += count($types[$typ]);
                }
                $print_str .= "-----------------"."\r\n";
                $print_str .= append_chars(substrwords('TC Total',18,""),"right",23," ")
                             .append_chars(numInt($tc_total),"left",13," ")."\r\n";
                $print_str .= append_chars(substrwords('GUEST Total',18,""),"right",23," ")
                             .append_chars(numInt($guestCount),"left",13," ")."\r\n";
                if($tc_total == 0 || $tc_qty == 0)
                    $avg = 0;
                else
                    $avg = $tc_total/$tc_qty;
                $print_str .= append_chars(substrwords('AVG Check',18,""),"right",23," ")
                             .append_chars(numInt($avg),"left",13," ")."\r\n";
            $print_str .= "\r\n";
            #CHARGES
                $types = $trans_charges['types'];
                $qty = 0;
                $print_str .= append_chars(substrwords('Charges:',18,""),"right",18," ").align_center(null,5," ")
                              .append_chars(null,"left",13," ")."\r\n";
                foreach ($types as $code => $val) {
                    $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",18," ").align_center($val['qty'],5," ")
                                  .append_chars(numInt($val['amount']),"left",13," ")."\r\n";
                    $qty += $val['qty'];
                }
                $print_str .= "-----------------"."\r\n";
                $print_str .= append_chars(substrwords('Total Charges',18,""),"right",18," ").align_center($qty,5," ")
                              .append_chars(numInt($charges),"left",13," ")."\r\n";
            $print_str .= "\r\n";
            #Discounts
                $types = $trans_discounts['types'];
                $qty = 0;
                $print_str .= append_chars(substrwords('Discounts:',18,""),"right",18," ").align_center(null,5," ")
                              .append_chars(null,"left",13," ")."\r\n";
                foreach ($types as $code => $val) {
                    $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",18," ").align_center($val['qty'],5," ")
                                  .append_chars(numInt($val['amount']),"left",13," ")."\r\n";
                    $qty += $val['qty'];
                }
                $print_str .= "-----------------"."\r\n";
                $print_str .= append_chars(substrwords('Total Discounts',18,""),"right",18," ").align_center($qty,5," ")
                              .append_chars(numInt($discounts),"left",13," ")."\r\n";
                $print_str .= append_chars(substrwords('VAT EXEMPT',18,""),"right",23," ")
                                         .append_chars(numInt($less_vat),"left",13," ")."\r\n";
            $print_str .= "\r\n";
            #PAYMENTS
                ####### OLD ##########
                    // $payments_types = $payments['types'];
                    // $payments_total = $payments['total'];
                    // $pay_qty = 0;
                    // $print_str .= append_chars(substrwords('Payment Breakdown:',18,""),"right",18," ").align_center(null,5," ")
                    //               .append_chars(null,"left",13," ")."\r\n";
                    // foreach ($payments_types as $code => $val) {
                    //     $print_str .= append_chars(substrwords(ucwords(strtolower($code)),18,""),"right",18," ").align_center($val['qty'],5," ")
                    //                   .append_chars(numInt($val['amount']),"left",13," ")."\r\n";
                    //     $pay_qty += $val['qty'];
                    // }
                    // $print_str .= "-----------------"."\r\n";
                    // $print_str .= append_chars(substrwords('Total Payments',18,""),"right",18," ").align_center($pay_qty,5," ")
                    //               .append_chars(numInt($payments_total),"left",13," ")."\r\n";
                ####### OLD ##########
                $payments_types = $payments['ov_types'];
                $payments_total = $payments['ov_total'];
                $net_payments_total = $payments['total'];
                $pay_qty = 0;
                $print_str .= append_chars(substrwords('Payment Breakdown:',18,""),"right",18," ").align_center(null,5," ")
                              .append_chars(null,"left",13," ")."\r\n";
                foreach ($payments_types as $code => $val) {
                    $print_str .= append_chars(substrwords(ucwords(strtolower($code)),18,""),"right",18," ").align_center($val['qty'],5," ")
                                  .append_chars(numInt($val['amount']),"left",13," ")."\r\n";
                    $pay_qty += $val['qty'];
                }
                $print_str .= "-----------------"."\r\n";
                $print_str .= append_chars(substrwords('Total Payment',18,""),"right",18," ").align_center($pay_qty,5," ")
                              .append_chars(numInt($payments_total),"left",13," ")."\r\n";
                $print_str .= append_chars(substrwords("GC Excess Amount",18,""),"right",18," ").align_center("",5," ")
                              .append_chars("(".numInt($payments_total - $net_payments_total).")","left",13," ")."\r\n";
                $print_str .= append_chars(substrwords('Net Payment',18,""),"right",18," ").align_center("",5," ")
                              .append_chars(numInt($net_payments_total),"left",13," ")."\r\n";
            $print_str .= "\r\n";
            #CATEGORIES
                $cats = $trans_menus['cats'];
                $print_str .= append_chars('Menu Categories:',"right",18," ").align_center('',5," ")
                             .append_chars('',"left",13," ")."\r\n";
                $qty = 0;
                $total = 0;
                foreach ($cats as $id => $val) {
                    $print_str .= append_chars(substrwords($val['name'],18,""),"right",18," ").align_center($val['qty'],5," ")
                               .append_chars(numInt($val['amount']),"left",13," ")."\r\n";
                    $qty += $val['qty'];
                    $total += $val['amount'];
                 }
                $print_str .= "-----------------"."\r\n";
                $print_str .= append_chars("SubTotal","right",18," ").align_center($qty,5," ")
                              .append_chars(numInt($total),"left",13," ")."\r\n";
                $print_str .= append_chars("Modifiers Total","right",18," ").align_center('',5," ")
                              .append_chars(numInt($trans_menus['mods_total']),"left",13," ")."\r\n";
                $print_str .= append_chars("Total","right",18," ").align_center('',5," ")
                              .append_chars(numInt($total+$trans_menus['mods_total']),"left",13," ")."\r\n";
            $print_str .= "\r\n";
            #SUBCATEGORIES
                $subcats = $trans_menus['sub_cats'];
                $print_str .= append_chars('Menu Subcategories:',"right",18," ").align_center('',5," ")
                             .append_chars('',"left",13," ")."\r\n";
                $qty = 0;
                $total = 0;
                foreach ($subcats as $id => $val) {
                    $print_str .= append_chars($val['name'],"right",18," ").align_center($val['qty'],5," ")
                               .append_chars(numInt($val['amount']),"left",13," ")."\r\n";
                    $qty += $val['qty'];
                    $total += $val['amount'];
                 }
                $print_str .= "-----------------"."\r\n";
                $print_str .= append_chars("SubTotal","right",18," ").align_center($qty,5," ")
                              .append_chars(numInt($total),"left",13," ")."\r\n";
                $print_str .= append_chars("Modifiers Total","right",18," ").align_center('',5," ")
                              .append_chars(numInt($trans_menus['mods_total']),"left",13," ")."\r\n";
                $print_str .= append_chars("Total","right",18," ").align_center('',5," ")
                              .append_chars(numInt($total+$trans_menus['mods_total']),"left",13," ")."\r\n";


            // $print_str .= "\r\n";
            // $print_str .= "======================================"."\r\n";
            // $print_str .= append_chars(substrwords('VATABLE SALES',18,""),"right",23," ")
            //              .append_chars(numInt(($taxable)),"left",13," ")."\r\n";
            // $print_str .= append_chars(substrwords('VAT EXEMPT SALES',18,""),"right",23," ")
            //              .append_chars(numInt(($nontaxable)),"left",13," ")."\r\n";
            // $print_str .= append_chars(substrwords('ZERO RATED',13,""),"right",23," ")
            //              .append_chars(numInt(($zero_rated)),"left",13," ")."\r\n";
            // $print_str .= append_chars("","right",23," ").append_chars("-----------","left",13," ")."\r\n";
            // $print_str .= append_chars(substrwords('NET SALES',18,""),"right",23," ")
            //                          .append_chars(numInt(($nsss)),"left",13," ")."\r\n";
            $print_str .= "\r\n";
            $print_str .= append_chars(substrwords('Invoice Start: ',18,""),"right",18," ").align_center('',5," ")
                         .append_chars(iSetObj($trans['first_ref'],'trans_ref'),"left",13," ")."\r\n";
            $print_str .= append_chars(substrwords('Invoice End: ',18,""),"right",18," ").align_center('',5," ")
                         .append_chars(iSetObj($trans['last_ref'],'trans_ref'),"left",13," ")."\r\n";
            $print_str .= append_chars(substrwords('Invoice Ctr: ',18,""),"right",18," ").align_center('',5," ")
                         .append_chars($trans['ref_count'],"left",13," ")."\r\n";
            if($title_name == "ZREAD"){
                // $gt = $this->old_grand_total($post['from']);
                $gt = $this->old_grand_net_total($post['from']);
                $print_str .= "\r\n";
                $print_str .= append_chars(substrwords('OLD GT: ',18,""),"right",18," ").align_center('',5," ")
                             // .append_chars(numInt( $gt['old_grand_total'] - ($charges + $local_tax) ),"left",13," ")."\r\n";
                             .append_chars(numInt( $gt['old_grand_total']),"left",13," ")."\r\n";
                $print_str .= append_chars(substrwords('NEW GT: ',18,""),"right",18," ").align_center('',5," ")
                             .append_chars( numInt($gt['old_grand_total']+$net_no_adds)  ,"left",13," ")."\r\n";
                             // .append_chars( numInt($gt['old_grand_total']+$net)  ,"left",13," ")."\r\n";
                             // .append_chars( numInt($gt['old_grand_total']+$gross)  ,"left",13," ")."\r\n";
                $print_str .= append_chars(substrwords('Z READ CTR: ',18,""),"right",18," ").align_center('',5," ")
                             .append_chars( $gt['ctr'] ,"left",13," ")."\r\n";
            }

            $print_str .= "======================================"."\r\n";



            $this->do_print($print_str,$asJson);
        }
        public function system_sales_rep($asJson=false){
            ////hapchan
            $print_str = $this->print_header();
            $user = $this->session->userdata('user');
            $time = $this->site_model->get_db_now();
            $post = $this->set_post();
            $curr = $this->search_current();
            $trans = $this->trans_sales($post['args'],$curr);
            // var_dump($trans['net']); die();
            $sales = $trans['sales'];
            $trans_menus = $this->menu_sales($sales['settled']['ids'],$curr);
            $trans_charges = $this->charges_sales($sales['settled']['ids'],$curr);
            $trans_discounts = $this->discounts_sales($sales['settled']['ids'],$curr);
            $tax_disc = $trans_discounts['tax_disc_total'];
            $no_tax_disc = $trans_discounts['no_tax_disc_total'];
            $trans_local_tax = $this->local_tax_sales($sales['settled']['ids'],$curr);
            $trans_tax = $this->tax_sales($sales['settled']['ids'],$curr);
            $trans_no_tax = $this->no_tax_sales($sales['settled']['ids'],$curr);
            $trans_zero_rated = $this->zero_rated_sales($sales['settled']['ids'],$curr);
            $payments = $this->payment_sales($sales['settled']['ids'],$curr);

            $gross = $trans_menus['gross'];
            
            $net = $trans['net'];
            $void = $trans['void'];
            $charges = $trans_charges['total'];
            $discounts = $trans_discounts['total'];
            $local_tax = $trans_local_tax['total'];
            $less_vat = (($gross+$charges+$local_tax) - $discounts) - $net;
            // $less_vat = $trans_discounts['vat_exempt_total'];

            // echo $gross.'+'.$charges.'+'.$local_tax.' - '.$discounts.' - '.$net;
            // die();

            if($less_vat < 0)
                $less_vat = 0;
            // var_dump($less_vat);

            //para mag tugmam yun payments and netsale
            // $net_sales2 = $gross + $charges - $discounts - $less_vat;
            // $diffs = $net_sales2 - $payments['total'];
            // if($diffs < 1){
            //     $less_vat = $less_vat + $diffs;
            // }
            

            $tax = $trans_tax['total'];
            $no_tax = $trans_no_tax['total'];
            $zero_rated = $trans_zero_rated['total'];
            $no_tax -= $zero_rated;

            $title_name = "SYSTEM SALES REPORT";
            if($post['title'] != "")
                $title_name = $post['title'];

            $print_str .= align_center($title_name,PAPER_WIDTH," ")."\r\n";
            $print_str .= align_center("TERMINAL ".$post['terminal'],PAPER_WIDTH," ")."\r\n";
            $print_str .= append_chars('Printed On','right',11," ").append_chars(": ".date2SqlDateTime($time),'right',19," ")."\r\n";
            $print_str .= append_chars('Printed BY','right',11," ").append_chars(": ".$user['full_name'],'right',19," ")."\r\n";
            $print_str .= PAPER_LINE."\r\n";
            $print_str .= align_center(sql2DateTime($post['from'])." - ".sql2DateTime($post['to']),PAPER_WIDTH," ")."\r\n";
            if($post['employee'] != "All")
                $print_str .= align_center($post['employee'],PAPER_WIDTH," ")."\r\n";
            $print_str .= PAPER_LINE."\r\n";

            $loc_txt = numInt(($local_tax));
            $net_no_adds = $net-($charges+$local_tax);
            $nontaxable = $no_tax - $no_tax_disc;
            $taxable =   ($gross - $discounts - $less_vat - $nontaxable) / 1.12;
            $total_net = ($taxable) + ($nontaxable+$zero_rated) + $tax + $local_tax;
            $add_gt = $taxable+$nontaxable+$zero_rated;
            $nsss = $taxable +  $nontaxable +  $zero_rated;

            #GENERAL
                $print_str .= append_chars(substrwords('GROSS SALES',18,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(numInt($gross),"left",PAPER_TOTAL_COL_2," ")."\r\n";

                $types = $trans_charges['types'];
                $qty = 0;
                foreach ($types as $code => $val) {
                    $amount = $val['amount'];
                    $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(numInt($amount),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                    $qty += $val['qty'];
                }
                $types = $trans_discounts['types'];
                $qty = 0;
                foreach ($types as $code => $val) {
                    $amount = $val['amount'];
                    // if(MALL == 'megamall' && $code == PWDDISC){
                    //     $amount = $val['amount'] / 1.12;
                    // }
                    $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars('-'.Num($amount,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                    $qty += $val['qty'];
                }
                $print_str .= append_chars(substrwords(ucwords(strtoupper('SC/PWD VAT EXEMPT')),18,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars('-'.numInt($less_vat,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars('',"right",18," ").align_center('',PAPER_TOTAL_COL_2," ")
                                  .append_chars('----------',"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $net_sales = $gross + $charges - $discounts - $less_vat;
                $print_str .= append_chars(substrwords(ucwords(strtoupper('NET SALES')),18,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(numInt($net_sales,2),"left",PAPER_TOTAL_COL_2," ")."\r\n\r\n";
            #PAYMENTS
                $payments_types = $payments['types'];
                $payments_total = $payments['total'];
                $pay_qty = 0;
                $print_str .= append_chars(substrwords('Payment Breakdown:',18,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
                              .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
                foreach ($payments_types as $code => $val) {
                    $print_str .= append_chars(substrwords(ucwords(strtoupper($code)),18,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                                  .append_chars(numInt($val['amount']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $pay_qty += $val['qty'];
                }
                $print_str .= append_chars('',"right",18," ").align_center('',PAPER_RD_COL_2," ")
                                  .append_chars('----------',"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= append_chars(substrwords('TOTAL PAYMENTS',18,""),"right",PAPER_RD_COL_1," ").align_center($pay_qty,PAPER_RD_COL_2," ")
                              .append_chars(numInt($payments_total),"left",PAPER_RD_COL_3_3," ")."\r\n\r\n";
            #SUMMARY
                $final_gross = $gross;
                $vat_ = $taxable * .12;
                $print_str .= append_chars(substrwords('VAT SALES',23,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(numInt($taxable),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords('VAT',23,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(numInt($vat_),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords('VAT EXEMPT SALES',23,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(numInt($nontaxable-$zero_rated),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords('ZERO RATED',23,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(numInt($zero_rated),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= PAPER_LINE_SINGLE."\r\n";
                $gross_less_disc = $final_gross - $discounts - $less_vat;
                $print_str .= append_chars(substrwords('NET SALES',23,""),"right",PAPER_TOTAL_COL_1," ")
                                         // .append_chars(numInt(($taxable + $nontaxable + $zero_rated + $vat_)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                                         .append_chars(numInt(($taxable + $nontaxable + $vat_)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= PAPER_LINE."\r\n";
                $print_str .= append_chars(substrwords('VOID SALES',18,""),"right",PAPER_TOTAL_COL_1," ")
                             .append_chars(numInt(($void)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords('Local Tax',18,""),"right",PAPER_TOTAL_COL_1," ")
                             .append_chars($loc_txt,"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= "\r\n";
            #TRANS COUNT
                $types = $trans['types'];
                $types_total = array();
                $guestCount = 0;
                foreach ($types as $type => $tp) {
                    foreach ($tp as $id => $opt){
                        if(isset($types_total[$type])){
                            $types_total[$type] += round($opt->total_amount,2);

                        }
                        else{
                            $types_total[$type] = round($opt->total_amount,2);
                        }
                        if($opt->guest == 0)
                            $guestCount += 1;
                        else
                            $guestCount += $opt->guest;
                    }
                }
                $print_str .= append_chars(substrwords('Trans Count:',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars('',"left",PAPER_RD_COL_3_3," ")."\r\n";
                $tc_total  = 0;
                $tc_qty = 0;
                foreach ($types_total as $typ => $tamnt) {
                    $print_str .= append_chars(substrwords($typ,18,""),"right",PAPER_RD_COL_1," ").align_center(count($types[$typ]),PAPER_RD_COL_2," ")
                                 .append_chars(numInt($tamnt),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $tc_total += $tamnt;
                    $tc_qty += count($types[$typ]);
                }
                $print_str .= "-----------------"."\r\n";
                $print_str .= append_chars(substrwords('TC Total',18,""),"right",PAPER_TOTAL_COL_1," ")
                             .append_chars(numInt($tc_total),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords('GUEST Total',18,""),"right",PAPER_TOTAL_COL_1," ")
                             .append_chars(numInt($guestCount),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                if($tc_total == 0 || $tc_qty == 0)
                    $avg = 0;
                else
                    $avg = $tc_total/$tc_qty;
                $print_str .= append_chars(substrwords('AVG Check',18,""),"right",PAPER_TOTAL_COL_1," ")
                             .append_chars(numInt($avg),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= "\r\n";
            #CHARGES
                $types = $trans_charges['types'];
                $qty = 0;
                $print_str .= append_chars(substrwords('Charges:',18,""),"right",18," ").align_center(null,5," ")
                              .append_chars(null,"left",13," ")."\r\n";
                foreach ($types as $code => $val) {
                    $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                                  .append_chars(numInt($val['amount']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $qty += $val['qty'];
                }
                $print_str .= "-----------------"."\r\n";
                $print_str .= append_chars(substrwords('Total Charges',18,""),"right",PAPER_RD_COL_1," ").align_center($qty,PAPER_RD_COL_2," ")
                              .append_chars(numInt($charges),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= "\r\n";
            #Discounts
                $types = $trans_discounts['types'];
                $qty = 0;
                $print_str .= append_chars(substrwords('Discounts:',18,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
                              .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
                foreach ($types as $code => $val) {
                    $amount = $val['amount'];
                    // if(MALL == 'megamall' && $code == PWDDISC){
                    //     $amount = $val['amount'] / 1.12;
                    // }
                    $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                                  .append_chars(numInt($amount),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $qty += $val['qty'];
                }
                $print_str .= "-----------------"."\r\n";
                $print_str .= append_chars(substrwords('Total Discounts',18,""),"right",PAPER_RD_COL_1," ").align_center($qty,PAPER_RD_COL_2," ")
                              .append_chars(numInt($discounts),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= append_chars(substrwords('VAT EXEMPT',18,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(numInt($less_vat),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= "\r\n";
            #PAYMENTS
                $payments_types = $payments['types'];
                $payments_total = $payments['total'];
                $pay_qty = 0;
                $print_str .= append_chars(substrwords('Payment Breakdown:',18,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
                              .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
                foreach ($payments_types as $code => $val) {
                    $print_str .= append_chars(substrwords(ucwords(strtolower($code)),18,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                                  .append_chars(numInt($val['amount']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $pay_qty += $val['qty'];
                }
                $print_str .= "-----------------"."\r\n";
                $print_str .= append_chars(substrwords('Total Payments',18,""),"right",PAPER_RD_COL_1," ").align_center($pay_qty,PAPER_RD_COL_2," ")
                              .append_chars(numInt($payments_total),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= "\r\n";

                //get all gc with excess

                if($payments['gc_excess']){
                    $print_str .= append_chars(substrwords('GC EXCESS',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                                  .append_chars(numInt($payments['gc_excess']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $print_str .= "\r\n";
                }
            #CATEGORIES
                $cats = $trans_menus['cats'];
                $print_str .= append_chars('Menu Categories:',"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars('',"left",PAPER_RD_COL_3," ")."\r\n";
                $qty = 0;
                $total = 0;
                foreach ($cats as $id => $val) {
                    if($val['qty'] > 0){
                        $print_str .= append_chars(substrwords($val['name'],18,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                                   .append_chars(numInt($val['amount']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                        $qty += $val['qty'];
                        $total += $val['amount'];
                    }
                 }
                $print_str .= "-----------------"."\r\n";
                $cat_total_qty = $qty;
                $print_str .= append_chars("SubTotal","right",PAPER_RD_COL_1," ").align_center($qty,PAPER_RD_COL_2," ")
                              .append_chars(numInt($total),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= append_chars("Modifiers Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                              .append_chars(numInt($trans_menus['mods_total']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                if($trans_menus['item_total'] > 0){
                 $print_str .= append_chars("Retail Items Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                               .append_chars(numInt($trans_menus['item_total']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                }

                $print_str .= append_chars("Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                              .append_chars(numInt($total+$trans_menus['mods_total']+$trans_menus['item_total']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= "\r\n";
            #SUBCATEGORIES
                $subcats = $trans_menus['sub_cats'];
                $print_str .= append_chars('Menu Subcategories:',"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars('',"left",PAPER_RD_COL_3," ")."\r\n";
                $qty = 0;
                $total = 0;
                foreach ($subcats as $id => $val) {
                    $print_str .= append_chars($val['name'],"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                               .append_chars(numInt($val['amount']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $qty += $val['qty'];
                    $total += $val['amount'];
                 }
                $print_str .= "-----------------"."\r\n";
                $print_str .= append_chars("SubTotal","right",PAPER_RD_COL_1," ").align_center($qty,PAPER_RD_COL_2," ")
                              .append_chars(numInt($total),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= append_chars("Modifiers Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                              .append_chars(numInt($trans_menus['mods_total']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= append_chars("Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                              .append_chars(numInt($total+$trans_menus['mods_total']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= "\r\n";
            #FREE MENUS
                $free = $trans_menus['free_menus'];
                $print_str .= append_chars('Free Menus:',"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars('',"left",PAPER_RD_COL_3," ")."\r\n";
                $fm = array();
                foreach ($free as $ms) {
                    if(!isset($fm[$ms->menu_id])){
                        $mn = array();
                        $mn['name'] = $ms->menu_name;
                        $mn['cat_id'] = $ms->cat_id;
                        $mn['qty'] = $ms->qty;
                        $mn['amount'] = $ms->sell_price * $ms->qty;
                        $mn['sell_price'] = $ms->sell_price;
                        $mn['code'] = $ms->menu_code;
                        // $mn['free_user_id'] = $ms->free_user_id;
                        $fm[$ms->menu_id] = $mn;
                    }
                    else{
                        $mn = $fm[$ms->menu_id];
                        $mn['qty'] += $ms->qty;
                        $mn['amount'] += $ms->sell_price * $ms->qty;
                        $fm[$ms->menu_id] = $mn;
                    }
                }
                $qty = 0;
                $total = 0;
                foreach ($fm as $menu_id => $val) {
                    $print_str .= append_chars($val['name'],"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                               .append_chars(($val['qty']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $qty += $val['qty'];
                    $total += $val['amount'];
                }
                $print_str .= "-----------------"."\r\n";
                $print_str .= append_chars("Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                              .append_chars(($qty),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= "\r\n";
                $print_str .= "\r\n";    
            #FOOTER
                $print_str .= append_chars(substrwords('Invoice Start: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars(iSetObj($trans['first_ref'],'trans_ref'),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= append_chars(substrwords('Invoice End: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars(iSetObj($trans['last_ref'],'trans_ref'),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= append_chars(substrwords('Invoice Ctr: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars($trans['ref_count'],"left",PAPER_RD_COL_3_3," ")."\r\n";
                if($title_name == "ZREAD"){
                    $gt = $this->old_grand_net_total($post['from']);
                    $print_str .= "\r\n";
                    $print_str .= append_chars(substrwords('OLD GT: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                                 .append_chars(numInt( $gt['old_grand_total']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $print_str .= append_chars(substrwords('NEW GT: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                                 .append_chars( numInt($gt['old_grand_total']+$net_no_adds)  ,"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $print_str .= append_chars(substrwords('Z READ CTR: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                                 .append_chars( $gt['ctr'] ,"left",PAPER_RD_COL_3_3," ")."\r\n";
                }
                $print_str .= PAPER_LINE."\r\n";
            #MALLS
                if(MALL_ENABLED){
                    ####################################
                    # AYALA
                        if(MALL == 'ayala'){
                            $rawgrossA = numInt($gross + $charges + $void + $local_tax);
                            $vatA = numInt(($rawgrossA  - $discounts - $void  -  $charges - $nontaxable - $local_tax - numInt($less_vat)) * (1/9.333333));
                            $dlySaleA = numInt($rawgrossA - $discounts - $void - $charges - $vatA - $less_vat + $local_tax);

                            $print_str .= align_center("FOR AYALA",PAPER_WIDTH," ")."\r\n";
                            $print_str .= PAPER_LINE_SINGLE."\r\n";
                            $print_str .= append_chars(substrwords('Description',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                                         .append_chars("Qty/Amount","left",PAPER_RD_COL_3_3," ")."\r\n";
                            $print_str .= PAPER_LINE_SINGLE."\r\n";
                            $print_str .= append_chars(substrwords('Daily Sales',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                                         .append_chars(numInt($dlySaleA),"left",PAPER_RD_COL_3_3," ")."\r\n";             
                            $print_str .= append_chars(substrwords('Vat',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                                         .append_chars(numInt($vatA),"left",PAPER_RD_COL_3_3," ")."\r\n";             
                            $print_str .= append_chars(substrwords('Vatable Sales',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                                         .append_chars(numInt($dlySaleA-$nontaxable),"left",PAPER_RD_COL_3_3," ")."\r\n";             
                            $print_str .= append_chars(substrwords('Non-Vatable SALES',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                                         .append_chars(numInt($nontaxable),"left",PAPER_RD_COL_3_3," ")."\r\n";             
                            $print_str .= append_chars(substrwords('Less SC Disc',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                                         .append_chars(numInt($discounts),"left",PAPER_RD_COL_3_3," ")."\r\n";             
                            $print_str .= append_chars(substrwords('Vat Exempt',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                                         .append_chars(numInt($less_vat),"left",PAPER_RD_COL_3_3," ")."\r\n";             
                            $print_str .= append_chars(substrwords('Zero Rated',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                                         .append_chars(numInt($zero_rated),"left",PAPER_RD_COL_3_3," ")."\r\n";             
                            $print_str .= PAPER_LINE_SINGLE."\r\n";
                            $print_str .= append_chars(substrwords('Net Sales',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                                         .append_chars(numInt($dlySaleA+$vatA),"left",PAPER_RD_COL_3_3," ")."\r\n";             
                            $print_str .= append_chars(substrwords('Total Qty Sold',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                                         .append_chars(numInt($cat_total_qty),"left",PAPER_RD_COL_3_3," ")."\r\n";             
                            $print_str .= append_chars(substrwords('Trans Count',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                                         .append_chars(numInt($tc_qty),"left",PAPER_RD_COL_3_3," ")."\r\n";             
                            $print_str .= PAPER_LINE."\r\n";
                        }
                    ####################################
                }    

            $this->do_print($print_str,$asJson);
        }
        public function hourly_sales_rep($asJson=false){
            $print_str = $this->print_header();
            $user = $this->session->userdata('user');
            $time = $this->site_model->get_db_now();
            $post = $this->set_post();
            $curr = $this->search_current();
            $trans = $this->trans_sales($post['args'],$curr);
            $sales = $trans['sales'];
            $net = $trans['net'];

            $title_name = "HOURLY SALES REPORT";
            $print_str .= align_center($title_name,PAPER_WIDTH," ")."\r\n";
            $print_str .= align_center("TERMINAL ".$post['terminal'],PAPER_WIDTH," ")."\r\n";
            $print_str .= append_chars('Printed On','right',11," ").append_chars(": ".date2SqlDateTime($time),'right',19," ")."\r\n";
            $print_str .= append_chars('Printed BY','right',11," ").append_chars(": ".$user['full_name'],'right',19," ")."\r\n";
            $print_str .= PAPER_LINE."\r\n";
            $print_str .= align_center(sql2DateTime($post['from'])." - ".sql2DateTime($post['to']),38," ")."\r\n";
            if($post['employee'] != "All")
                $print_str .= align_center($post['employee'],38," ")."\r\n";



            $ranges = array();
            foreach (unserialize(TIMERANGES) as $ctr => $time) {
                $key = date('H',strtotime($time['FTIME']));
                $ranges[$key] = array('start'=>$time['FTIME'],'end'=>$time['TTIME'],'tc'=>0,'net'=>0);
            }

            $dates = array();
            if(count($sales['settled']['orders']) > 0){
                foreach ($sales['settled']['orders'] as $sales_id => $val) {
                    $dates[date2Sql($val->datetime)]['ranges'] = $ranges;
                }
                foreach ($sales['settled']['orders'] as $sales_id => $val) {
                    if(isset($dates[date2Sql($val->datetime)])){
                        $date_arr = $dates[date2Sql($val->datetime)];
                        $range = $date_arr['ranges'];
                        $H = date('H',strtotime($val->datetime));
                        if(isset($range[$H])){
                            $r = $range[$H];
                            $r['tc'] += 1;
                            $r['net'] += $val->total_amount;
                            $range[$H] = $r;
                        }
                        $dates[date2Sql($val->datetime)]['ranges'] = $range;
                    }
                }
            }

            $ctr = 0;
            foreach ($dates as $date => $val) {
                $print_str .= align_center(sql2Date($date),38," ")."\r\n";
                $ranges = $val['ranges'];
                $print_str .= append_chars(substrwords("",PAPER_RD_COL_1_4,""),"right",PAPER_RD_COL_1_4," ")
                             .append_chars(substrwords('TC',5,""),"right",PAPER_RD_COL_1_2," ")
                             .append_chars(substrwords('NET',10,""),"right",PAPER_RD_COL_3_3," ")
                             .append_chars(substrwords('AVG',10,""),"right",PAPER_RD_COL_3_3," ")."\r\n";
                foreach ($ranges as $key => $ran) {
                    if($ran['tc'] == 0 || $ran['net'] == 0)
                        $avg = 0;
                    else
                        $avg = $ran['net']/$ran['tc'];
                    $ctr += $ran['tc'];
                    $print_str .= append_chars(substrwords($ran['start']."-".$ran['end'],PAPER_RD_COL_1_4,""),"right",PAPER_RD_COL_1_4," ")
                                 .append_chars(substrwords($ran['tc'],5,""),"right",PAPER_RD_COL_1_2," ")
                                 .append_chars(substrwords(numInt($ran['net']),10,""),"right",PAPER_RD_COL_3_3," ")
                                 .append_chars(substrwords(numInt($avg),10,""),"right",PAPER_RD_COL_3_3," ")."\r\n";
                }
                $print_str .= "\r\n";
            }
            $print_str .= PAPER_LINE."\r\n";
            if($ctr == 0 || $net == 0)
                $tavg = 0;
            else
                $tavg = $net/$ctr;
            $print_str .= append_chars(substrwords("TOTAL",18,""),"right",PAPER_RD_COL_1_4," ")
                         .append_chars(substrwords($ctr,12,""),"right",PAPER_RD_COL_1_2," ")
                         .append_chars(substrwords(numInt($net),12,""),"right",PAPER_RD_COL_3_3," ")
                         .append_chars(substrwords(numInt($tavg),12,""),"right",PAPER_RD_COL_3_3," ")."\r\n";
            $print_str .= PAPER_LINE."\r\n";

            $this->do_print($print_str,$asJson);
        }
        public function cash_count_rep($asJson=false){
            $print_str = $this->print_header();
            $user = $this->session->userdata('user');
            $time = $this->site_model->get_db_now();
            $post = $this->set_post();

            $title_name = "Cash Count";
            $print_str .= align_center($title_name,PAPER_WIDTH," ")."\r\n";
            $print_str .= align_center("TERMINAL ".$post['terminal'],PAPER_WIDTH," ")."\r\n";
            $print_str .= append_chars('Printed On','right',11," ").append_chars(": ".date2SqlDateTime($time),'right',19," ")."\r\n";
            $print_str .= append_chars('Printed BY','right',11," ").append_chars(": ".$user['full_name'],'right',19," ")."\r\n";
            $print_str .= PAPER_LINE."\r\n";
            if($post['employee'] != "All")
                $print_str .= align_center($post['employee'],PAPER_WIDTH," ")."\r\n";
            $shit_id = $post['shift_id'];
            $totals = $this->shift_entries($shit_id);
            $cashout_id = $this->shift_cashout($shit_id);
            $print_str = $this->print_cashout_details($print_str,$totals,$cashout_id);

            // echo "<pre style='background-color:#fff'>$print_str</pre>";
            $this->do_print($print_str,$asJson);
        }
        public function print_cashout_details($print_str="",$totals,$cashout_id){
            $cashout_header = $this->cashier_model->get_cashout_header($cashout_id); // returns row
            $cashout_details = $this->cashier_model->get_cashout_details($cashout_id); // returns rows array

            $sum_deps = $sum_withs = 0;

            /* Cash Deposits */
            $print_str .= "Cash Deposits\r\n";
            foreach ($totals['total_deps'] as $k => $dep) {
                $print_str .= append_chars("   ".($k+1),'right',PAPER_DET_COL_3," ").append_chars(date('H:i:s',strtotime($dep->trans_date)),"right",PAPER_RD_COL_MID," ")
                    .append_chars(number_format($dep->amount,2),"left",PAPER_RD_COL_1_4," ")."\r\n";
                $sum_deps += $dep->amount;
            }
            if ($sum_deps > 0)
                $print_str .= append_chars("------------","left",PAPER_WIDTH," ")."\r\n";
            // $print_str .= append_chars("Total Cash Deposits","right",21," ")
            //     .append_chars(number_format($sum_deps,2),"left",15," ")."\r\n\r\n";
            $print_str .= append_chars(substrwords('Total Cash Deposits',18,""),"right",PAPER_TOTAL_COL_1," ")
                                     .append_chars(number_format($sum_deps,2),"left",PAPER_TOTAL_COL_2," ")."\r\n\r\n";

            /* Cash Withdrawals */
            $print_str .= "Cash Withdrawals\r\n";
            foreach ($totals['total_withs'] as $k => $with) {
                // $print_str .= append_chars("   ".($k+1)." ".date('H:i:s',strtotime($with->trans_date)),"right",21," ")
                //     .append_chars(number_format(abs($with->amount),2),"left",15," ")."\r\n";
                $print_str .= append_chars("   ".($k+1),'right',PAPER_DET_COL_3," ").append_chars(date('H:i:s',strtotime($with->trans_date)),"right",PAPER_RD_COL_MID," ")
                    .append_chars(number_format(abs($with->amount),2),"left",PAPER_RD_COL_1_4," ")."\r\n";
                $sum_withs += abs($with->amount);
            }
            if ($sum_withs > 0)
                $print_str .= append_chars("------------","left",PAPER_WIDTH," ")."\r\n";
            // $print_str .= append_chars("Total Cash Withdrawals","right",25," ")
            //     .append_chars(number_format($sum_withs,2),"left",11," ")."\r\n\r\n";
            $print_str .= append_chars(substrwords('Total Cash Withdrawals',18,""),"right",PAPER_TOTAL_COL_1," ")
                                     .append_chars(number_format($sum_withs,2),"left",PAPER_TOTAL_COL_2," ")."\r\n\r\n";


            /* Drawer */
            $print_str .= append_chars("Expected Drawer amount","right",PAPER_TOTAL_COL_1," ").append_chars(number_format($cashout_header->drawer_amount,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            $print_str .= append_chars("Actual Drawer amount","right",PAPER_TOTAL_COL_1," ").append_chars(number_format($cashout_header->count_amount,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            $print_str .= append_chars("-------------","right",PAPER_WIDTH," ")."\r\n";
            $print_str .= append_chars("Variance","right",PAPER_TOTAL_COL_1," ").append_chars(number_format(abs($cashout_header->drawer_amount - $cashout_header->count_amount),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";


            /* Cashout Details */
            $print_str .= "\r\nCashout Breakdown\r\n";
            foreach ($cashout_details as $value) {
                if (!empty($value->denomination))
                    $mid = $value->denomination." X ".($value->total/$value->denomination);
                elseif (!empty($value->reference))
                    $mid = $value->reference." ";
                else $mid = "";

                if($value->type == 'cust-deposits'){
                    $print_str .= append_chars("[".ucwords('deposit')."] ","right",PAPER_TOTAL_COL_1," ").
                        append_chars(number_format($value->total,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                    $print_str .= append_chars("   ".ucwords(mb_strtolower($mid)),"right",PAPER_TOTAL_COL_1," ").
                        append_chars("","left",PAPER_TOTAL_COL_2," ")."\r\n";
                }
                else{
                    // $print_str .= append_chars("[".ucwords($value->type)."] ".$mid,"right",21," ").
                    //     append_chars(number_format($value->total,2),"left",15," ")."\r\n";
                    $print_str .= append_chars("[".ucwords($value->type)."] ".$mid,"right",PAPER_TOTAL_COL_1," ")
                                     .append_chars(number_format($value->total,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                }
            }

            $print_str .= "\r\n".append_chars("","right",PAPER_WIDTH,"-");
            return $print_str;
        }
        public function do_print($print_str=null,$asJson=false){
            if (!$asJson) {
                // echo "<pre style='background-color:#fff'>$print_str</pre>";
                echo json_encode(array("code"=>"<pre style='background-color:#fff'>$print_str</pre>"));
            }
            else{
                $filename = "report.txt";
                $fp = fopen($filename, "w+");
                fwrite($fp,$print_str);
                fclose($fp);

                $batfile = "print.bat";
                $fh1 = fopen($batfile,'w+');
                $root = dirname(BASEPATH);

                fwrite($fh1, "NOTEPAD /P \"".realpath($root."/".$filename)."\"");
                fclose($fh1);
                session_write_close();
                // exec($filename);
                exec($batfile);
                session_start();
                unlink($filename);
                unlink($batfile);
            }
        }
        public function print_header(){
            $this->load->model('dine/setup_model');
            $branch_code = $this->input->post('branch_id') != '' ? $this->input->post('branch_id') : BRANCH_CODE;
            $branch_details = $this->setup_model->get_branch_details($branch_code);
            $branch = array();
            foreach ($branch_details as $bv) {
                $branch = array(
                    'id' => $bv->branch_id,
                    'res_id' => $bv->res_id,
                    'branch_code' => $bv->branch_code,
                    'name' => $bv->branch_name,
                    'branch_desc' => $bv->branch_desc,
                    'contact_no' => $bv->contact_no,
                    'delivery_no' => $bv->delivery_no,
                    'address' => $bv->address,
                    'base_location' => $bv->base_location,
                    'currency' => $bv->currency,
                    'inactive' => $bv->inactive,
                    'tin' => $bv->tin,
                    'machine_no' => $bv->machine_no,
                    'bir' => $bv->bir,
                    'permit_no' => $bv->permit_no,
                    'serial' => $bv->serial,
                    'accrdn' => $bv->accrdn,
                    'email' => $bv->email,
                    'website' => $bv->website,
                    'store_open' => $bv->store_open,
                    'store_close' => $bv->store_close,
                );
            }
            $userdata = $this->session->userdata('user');
            $print_str = "\r\n\r\n";
            $wrap = wordwrap($branch['name'],35,"|#|");
            $exp = explode("|#|", $wrap);
            foreach ($exp as $v) {
                $print_str .= align_center($v,PAPER_WIDTH," ")."\r\n";
            }
            $wrap = wordwrap($branch['address'],35,"|#|");
            $exp = explode("|#|", $wrap);
            foreach ($exp as $v) {
                $print_str .= align_center($v,PAPER_WIDTH," ")."\r\n";
            }
            $print_str .=
             align_center('TIN: '.$branch['tin'],PAPER_WIDTH," ")."\r\n"
            .align_center('ACCRDN: '.$branch['accrdn'],PAPER_WIDTH," ")."\r\n"
            // .$this->align_center('BIR # '.$branch['bir'],42," ")."\r\n"
            .align_center('MIN: '.$branch['machine_no'],PAPER_WIDTH," ")."\r\n"
            // .align_center('SN: '.$branch['serial'],38," ")."\r\n"
            .align_center('PERMIT: '.$branch['permit_no'],PAPER_WIDTH," ")."\r\n";
            $print_str .= PAPER_LINE."\r\n";
            return $print_str;
        }
    ##############
    #### RETURNS
        public function order_box(){
            $post = $this->set_post();
            $curr = $this->search_current();
            $trans = $this->trans_sales($post['args'],$curr);
            $orders = $trans['all_orders'];
            echo count($orders);
        }    
    ##############
    #### FUNCTIONS
        public function set_post($set_range=null,$set_calendar=null,$set_branch_code=null,$set_brand=null){
            $args = array();
            $from = "";
            $to = "";
            $date = "";
            $range = $this->input->post('calendar_range');
            $calendar = $this->input->post('calendar');
            $branch_code = $this->input->post('branch_id');
            // echo $branch_code; die('sss');
            $brand = $this->input->post('brand');
            if($set_range != null )
                $range = $set_range;
            if($set_calendar != null )
                $calendar = $set_calendar;
            if($set_branch_code != null )
                $branch_code = $set_branch_code;
            if($set_brand != null )
                $brand = $set_brand;

            // $range = '2015/10/28 7:00 AM to 2015/10/28 10:00 PM';
            // $calendar = '12/04/2015';
            $title = "";
            if($this->input->post('title'))
                $title = $this->input->post('title');
            // $title = 'ZREAD';
            if($range != ""){
                $daterange = $range;
                $dates = explode(" to ",$daterange);
                $from = date2SqlDateTime($dates[0]);
                $to = date2SqlDateTime($dates[1]);
                $args["trans_sales.datetime  BETWEEN '".$from."' AND '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);
                // $args["trans_sales.branch_code  = '".$branch_code."' "] = array('use'=>'where','val'=>null,'third'=>false);
            }
            if($calendar != ""){
                $date = $calendar;
                $rargs["DATE(read_details.read_date) = DATE('".date2Sql($date)."') "] = array('use'=>'where','val'=>null,'third'=>false);
                $select = "read_details.*";
                $results = $this->site_model->get_tbl('read_details',$rargs,array('scope_from'=>'asc'),"",true,$select);
                // echo $this->site_model->db->last_query();
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
                // echo $from."-".$to;
                // $rargs2["DATE(read_details.read_date) = DATE('".date2Sql($date)."') "] = array('use'=>'where','val'=>null,'third'=>false);
                // $select = "read_details.*";
                // $results2 = $this->site_model->get_tbl('read_details',$rargs2,array('scope_to'=>'desc'),"",true,$select);
                // foreach ($results2 as $res) {
                //     $to = $res->scope_to;
                //     break;
                // }
                if($from != "" && $to != ""){
                    $args["trans_sales.datetime  BETWEEN '".$from."' AND '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);
                    $args["trans_sales.branch_code  = '".$branch_code."' "] = array('use'=>'where','val'=>null,'third'=>false);
                }
                else{
                    $args["DATE(trans_sales.datetime) = DATE('".date2Sql($date)."') "] = array('use'=>'where','val'=>null,'third'=>false);
                    $args["trans_sales.branch_code  = '".$branch_code."' "] = array('use'=>'where','val'=>null,'third'=>false);
                    $from = date('Y-m-d 00:00',strtotime($date));
                    $to = date('Y-m-d 24:00',strtotime($date));
                }

            }
            $emp = "All";
            if($this->input->post('cashier')){
                $args['trans_sales.user_id'] = $this->input->post('cashier');
                $server = $this->manager_model->get_server_details($this->input->post('cashier'));
                $emp = $server[0]->fname." ".$server[0]->mname." ".$server[0]->lname." ".$server[0]->suffix;
            }
            $shift = $this->input->post('shift_id');
            // $shift = 1;
            if($shift != ""){
                $join['users'] = array('content'=>'shifts.user_id = users.id');
                $jargs['shifts.shift_id'] = $shift;
                $select = "shifts.*,users.fname,users.lname,users.mname,users.suffix,users.username";
                $results = $this->site_model->get_tbl('shifts',$jargs,array('check_in'=>'desc'),$join,true,$select);
                $res = $results[0];


                $server = $this->manager_model->get_server_details($res->user_id);
                $emp = $server[0]->fname." ".$server[0]->mname." ".$server[0]->lname." ".$server[0]->suffix;
                $from = date2SqlDateTime($res->check_in);

                if($res->check_out == ""){
                    $today = $this->site_model->get_db_now();
                    $to = date2SqlDateTime($today);
                }
                else
                    $to = date2SqlDateTime($res->check_out);


                $args = array();
                $args['trans_sales.shift_id'] = $shift;
            }

            $branch_code = $this->input->post('branch_id') != '' ? $this->input->post('branch_id') : BRANCH_CODE;
            $branch_details = $this->setup_model->get_branch_details($branch_code);
            $from = date2SqlDateTime($date.' '.$branch_details[0]->store_open);
            $to = date2SqlDateTime($date.' '.$branch_details[0]->store_close);

            if($branch_details[0]->store_close < $branch_details[0]->store_open){
                $to = date('Y-m-d', strtotime($date . ' +1 day')).' '.$branch_details[0]->store_close;
            }

            $terminal = TERMINAL_ID;
            // $args['trans_sales.terminal_id'] = $terminal;
            return array('args'=>$args,'from'=>$from,'to'=>$to,'date'=>$date,'terminal'=>$terminal,"employee"=>$emp,"title"=>$title,"shift_id"=>$shift,'branch_code'=>$branch_code,'brand'=>$brand);
        }
        public function search_current(){
            $today = sql2Date($this->site_model->get_db_now());
            $use = false;
            if($this->input->post('calendar_range')){
                $daterange = $this->input->post('calendar_range');
                $dates = explode(" to ",$daterange);
                $from = sql2Date($dates[0]);
                $to = sql2Date($dates[1]);
                $s_date = strtotime($from);
                $e_date = strtotime($to);
                $date = strtotime($today);
                if($date >= $s_date && $date <= $e_date)
                    $use = true;
            }
            if($this->input->post('calendar')){
                $date = $this->input->post('calendar');
                $from = sql2Date($date);
                if($from == $today){
                    $use = true;
                }
                if($this->input->post('use_curr')){
                    $use = $this->input->post('use_curr');
                }
            }
            $shift = $this->input->post('shift_id');
            if($shift != ""){
               $use = true;
            }
            $branch = $this->input->post('branch_code');
            if($branch != ""){
               $use = true;
            }
            return $use;
        }
        public function trans_sales($args=array(),$curr=false,$branch_code="",$brand=""){
            $total_chit = 0;
            $chit = array();
            $n_results = array();
            // if($curr){
            //     $this->cashier_model->db = $this->load->database('default', TRUE);
            //     $n_results  = $this->cashier_model->get_trans_sales(null,$args);
            // }
            // $this->cashier_model->db = $this->load->database('default', TRUE);
            // $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
            // $args["trans_sales.inactive = 0"] = array('use'=>'where','val'=>null,'third'=>false);
            // echo "<pre>",print_r($args),"</pre>";die();
            $results = $this->cashier_model->get_trans_sales(null,$args,'',null,$branch_code,$brand);
            // echo $this->cashier_model->db->last_query();die();
            $orders = array();
            // echo 'das';
            // echo "<pre>",print_r($results),"</pre>";die();
            // if(count($n_results) > 0){
            //     foreach ($n_results as $nres) {
            //         if(!isset($orders[$nres->sales_id])){
            //             $orders[$nres->sales_id] = $nres;
            //         }
            //     }
            // }
            // if(HIDECHIT){
            //     // if(count($n_results) > 0){
            //     //     foreach ($n_results as $nres) {

            //     //         if($nres->type_id == 10){
            //     //             $this->site_model->db = $this->load->database('default', TRUE);
            //     //             $where = array('sales_id'=>$nres->sales_id);
            //     //             $rest = $this->site_model->get_details($where,'trans_sales_payments');
            //     //             if($rest){
            //     //                 if($rest[0]->payment_type != 'chit'){

            //     //                     if(!isset($orders[$nres->sales_id])){
            //     //                         $orders[$nres->sales_id] = $nres;
            //     //                     }
            //     //                 }else{
            //     //                     $chit[$nres->sales_id] = $nres;
            //     //                 }
            //     //             }else{
            //     //                 if(!isset($orders[$nres->sales_id])){
            //     //                     $orders[$nres->sales_id] = $nres;
            //     //                 }
            //     //             }

            //     //         }else{
            //     //             if(!isset($orders[$nres->sales_id])){
            //     //                 $orders[$nres->sales_id] = $nres;
            //     //             }
            //     //         }
            //     //     }
            //     // }

            //     foreach ($results as $res) {
                    
            //         if($res->type_id == 10){
            //             $this->site_model->db = $this->load->database('default', TRUE);
            //             $where = array('sales_id'=>$res->sales_id,'branch_code'=>$branch_code);

            //             if($brand != ''){
            //                 $where['pos_id'] = $brand;
            //             }

            //             $rest = $this->site_model->get_details($where,'trans_sales_payments',array('payment_id','branch_code','pos_id'));
            //             if($rest){
            //                 if($rest[0]->payment_type != 'chit'){
            //                     if(!isset($orders[$res->sales_id])){
            //                         $orders[$res->pos_id.$res->sales_id] = $res;
            //                     }
            //                 }else{
            //                     $chit[$res->pos_id.$res->sales_id] = $res;
            //                 }
            //             }else{
            //                 if(!isset($orders[$res->pos_id.$res->sales_id])){
            //                     $orders[$res->pos_id.$res->sales_id] = $res;
            //                 }
            //             }
            //         }else{
            //             if(!isset($orders[$res->sales_id])){
            //                 $orders[$res->pos_id.$res->sales_id] = $res;
            //             }
            //         }
            //     }
            // }else{
                // if(count($n_results) > 0){
                //     foreach ($n_results as $nres) {
                //         if(!isset($orders[$nres->sales_id])){
                //             $orders[$nres->sales_id] = $nres;
                //         }
                //     }
                // }
                foreach ($results as $res) {
                    if(!isset($orders[$res->pos_id.'-'.$res->sales_id])){
                        $orders[$res->pos_id.'-'.$res->sales_id] = $res;
                        // $orders[$res->sales_id] = $res;
                    }
                }
            // }
                // echo 'aw'; die();
            $sales = array();
            $all_ids = array();
            $sales['void'] = array();
            $sales['cancel'] = array();
            $sales['settled'] = array();
            $sales['void']['ids'] = array();
            $sales['cancel']['ids'] = array();
            $sales['settled']['ids'] = array();
            $sales['void']['orders'] = array();
            $sales['cancel']['orders'] = array();
            $sales['settled']['orders'] = array();
            $sales['settled']['orders2'] = array();
            $net = 0;
            $void_amount = 0;
            $cancel_amount = 0;
            $void_cnt = 0;
            $cancel_cnt = 0;
            $ewt1_count = 0;
            $ewt2_count = 0;
            $ewt5_count = 0;
            $types = array();
            $ordsnums = array();
            $all_orders = array();
            foreach ($orders as $sales_id => $sale) {
                if($sale->type_id == 10){
                    if($sale->trans_ref != "" && $sale->inactive == 0){                        
                        $sales['settled']['ids'][] = $sales_id;
                        // $net += $sale->total_amount;
                        $net += round($sale->total_amount,2);
                        $types[$sale->type][$sale->sales_id] = $sale;
                        $ordsnums[$sale->trans_ref] = $sale;
                        $sales['settled']['orders'][$sales_id] = $sale;
                        $sales['settled']['orders2'][$sales_id] = $sale;
                        $ewt1_count += $sale->ewt1;
                        $ewt2_count += $sale->ewt2;
                        $ewt5_count += $sale->ewt5;
                    }
                    else if($sale->trans_ref == "" && $sale->inactive == 1){
                        if($sale->void_user_id){
                            $sales['cancel']['ids'][] = $sales_id;
                            $sales['cancel']['orders'][$sales_id] = $sale;
                            $cancel_amount += $sale->total_amount;
                            $cancel_cnt++;
                        }
                    }
                }
                else{
                    $sales['void']['ids'][] = $sales_id;
                    $sales['void']['orders'][$sales_id] = $sale;
                    $void_amount += round($sale->total_amount,2);
                    $void_cnt++;
                }
                $all_ids[] = $sales_id;
                $all_orders[$sales_id] = $sale;
            }
            ksort($ordsnums);
            $first = array_shift(array_slice($ordsnums, 0, 1));
            $last = end($ordsnums);
            $ref_ctr = count($ordsnums);

            if(HIDECHIT){
                // if($curr){
                //     foreach($chit as $key => $vals){

                //         $this->site_model->db = $this->load->database('default', TRUE);
                //         $where = array('sales_id'=>$key);
                //         $results = $this->site_model->get_details($where,'trans_sales_payments');

                //         $total_chit += $results[0]->to_pay;
                //     }
                // }else{
                foreach($chit as $key => $vals){

                    // $this->site_model->db = $this->load->database('default', TRUE);
                    $where = array('sales_id'=>$key);

                    if($brand != ''){
                        $where['pos_id'] = $brand;
                    }
                    $results = $this->site_model->get_details($where,'trans_sales_payments',array('payment_id','branch_code'));

                    $total_chit += $results[0]->to_pay;
                }
                // }
            }
            // echo $cancel_amount;

            return array('all_ids'=>$all_ids,'all_orders'=>$all_orders,'sales'=>$sales,'net'=>$net,'void'=>$void_amount,'types'=>$types,'refs'=>$ordsnums,'first_ref'=>$first,'last_ref'=>$last,'ref_count'=>$ref_ctr,'total_chit'=>$total_chit,'cancel_amount'=>$cancel_amount,'void_cnt'=>$void_cnt,'cancel_cnt'=>$cancel_cnt,'ewt1_count'=>$ewt1_count,'ewt2_count'=>$ewt2_count,'ewt5_count'=>$ewt5_count);
        }
        public function menu_sales($ids=array(),$curr=false,$branch_code="",$brand="",$from="",$to=""){
            $cats = array();
            // $this->site_model->db = $this->load->database('default', TRUE);
            $user = $this->session->userdata('user');
            $sess_id = $user['sess_id'];
    
            if($branch_code){
                $args = array('branch_code'=>$branch_code);
                // if($brand != ''){
                //     $args['brand'] = $brand;
                // }
                $cat_res = $this->site_model->get_tbl('menu_categories',$args);
            }else{
                $cat_res = $this->site_model->get_tbl('menu_categories',array());
            }
            foreach ($cat_res as $ces) {
                $cats[$ces->menu_cat_id] = array('cat_id'=>$ces->menu_cat_id,'name'=>$ces->menu_cat_name,'qty'=>0,'amount'=>0);
            }
             // echo $this->site_model->db->last_query(); die();
            $sub_cats = array();
            // $sales_menu_cat = $this->menu_model->get_menu_subcategories(null,true);
            // foreach ($sales_menu_cat as $rc) {
            //     $sub_cats[$rc->menu_sub_cat_id] = array('name'=>$rc->menu_sub_cat_name,'qty'=>0,'amount'=>0);
            // }

            //sabi ni sir hardcode na food and bev lang ang menu sub cat
            $sub_cats[1] = array('name'=>'FOOD','qty'=>0,'amount'=>0);
            $sub_cats[2] = array('name'=>'BEVERAGE','qty'=>0,'amount'=>0);

            $food_types[0] = array('name'=>'RESTAURANT','qty'=>0,'amount'=>0);
            $food_types[1] = array('name'=>'BAKESHOP','qty'=>0,'amount'=>0);
            $food_types[2] = array('name'=>'BEVERAGE','qty'=>0,'amount'=>0);

            $menu_net_total = 0;
            $menu_qty_total = 0;
            $item_net_total = 0;
            $item_qty_total = 0;
            $menus = array();
            $menu_trans_type = array();
            $free_menus = array();
            $ids_used = array();
            // echo "<pre>",print_r($ids),"</pre>";die();
            if(count($ids) > 0){
                $select = 'trans_sales_menus.*,menus.menu_code,menus.menu_name,menus.cost as sell_price,menus.menu_cat_id as cat_id,menus.menu_sub_cat_id as sub_cat_id, menus.costing, menus.food_type,branch_details.branch_name,trans_sales.type';
                $join = null;
                if($branch_code != ""){
                $join['menus'] = array('content'=>"trans_sales_menus.menu_id = menus.menu_id  AND menus.branch_code = trans_sales_menus.branch_code");
                }else{
                $join['menus'] = array('content'=>'trans_sales_menus.menu_id = menus.menu_id AND trans_sales_menus.branch_code = menus.branch_code');
                }
                $join['branch_details'] = array('content'=>'trans_sales_menus.branch_code = menus.branch_code AND trans_sales_menus.branch_code = branch_details.branch_code');

                $join['trans_sales'.$sess_id.' trans_sales'] = array('content'=>'trans_sales_menus.sales_id = trans_sales.sales_id AND trans_sales_menus.branch_code = trans_sales.branch_code && trans_sales_menus.pos_id = trans_sales.pos_id');
                $n_menu_res = array();
                // if($curr){
                //     $this->site_model->db = $this->load->database('default', TRUE);
                //     $n_menu_res = $this->site_model->get_tbl('trans_sales_menus',array('sales_id'=>$ids,'trans_sales_menus.branch_code'=>$branch_code),array(),$join,true,$select);
                // }
                // $this->site_model->db= $this->load->database('default', TRUE);
                // echo $branch_code; die('eeeee');
                if($branch_code != ""){
                    $args = array('trans_sales_menus.branch_code'=>$branch_code,'trans_sales.inactive'=>0,'type_id'=>10);
                    if($brand != ''){
                        $args['trans_sales_menus.pos_id']=$brand;
                    }

                    $args["concat(trans_sales_menus.pos_id,'-',trans_sales_menus.sales_id)"] = array('use'=>'where_in','val'=>$ids,'third'=>false);
                    $menu_res = $this->site_model->get_tbl('trans_sales'.$sess_id.'_menus trans_sales_menus',$args,array(),$join,true,$select,array('sales_menu_id','branch_code','pos_id'));
                }else{
                    $args = array('trans_sales.inactive'=>0,'type_id'=>10);
                    $args["concat(trans_sales_menus.pos_id,'-',trans_sales_menus.sales_id)"] = array('use'=>'where_in','val'=>$ids,'third'=>false);
                    $menu_res = $this->site_model->get_tbl('trans_sales'.$sess_id.'_menus trans_sales_menus',$args,array('menus.menu_name'=>'asc'),$join,true,$select,array('sales_menu_id','branch_code','pos_id'));
                }                
                // echo $this->site_model->db->last_query(); die();
                // echo "<pre>",print_r($menu_res),"</pre>";die();
                foreach ($menu_res as $ms) {
                    if(!in_array($ms->sales_id, $ids_used)){
                        $ids_used[] = $ms->sales_id;
                    }
                    if(!isset($menus[$ms->branch_code.$ms->menu_id])){
                        $mn = array();
                        $mn['name'] = $ms->menu_name;
                        $mn['branch_code'] = $ms->branch_code;
                        $mn['branch_name'] = $ms->branch_name;
                        $mn['cat_id'] = $ms->cat_id;
                        $mn['menu_id'] = $ms->menu_id;
                        $mn['qty'] = $ms->qty;
                        $mn['sell_price'] = $ms->sell_price;
                        $mn['cost_price'] = $ms->costing;
                        $mn['code'] = $ms->menu_code;
                        $mn['amount'] = $ms->price * $ms->qty;
                        $mn['type'] = $ms->type;
                        $menus[$ms->branch_code.$ms->menu_id] = $mn;
                    }
                    else{
                        $mn = $menus[$ms->branch_code.$ms->menu_id];
                        $mn['qty'] += $ms->qty;
                        $mn['amount'] += $ms->price * $ms->qty;
                        $menus[$ms->branch_code.$ms->menu_id] = $mn;
                    }

                    if(OTHER_MENU_ITEM_SALES){
                        if(!isset($menu_trans_type[$ms->branch_code.$ms->menu_id][$ms->type])){
                            $hmn = array();
                            $hmn['name'] = $ms->menu_name;
                            $mn['branch_code'] = $ms->branch_code;
                            $hmn['cat_id'] = $ms->cat_id;
                            $hmn['menu_id'] = $ms->menu_id;
                            $hmn['qty'] = $ms->qty;
                            $hmn['sell_price'] = $ms->price;
                            $hmn['cost_price'] = $ms->costing;
                            $hmn['code'] = $ms->menu_code;
                            $hmn['amount'] = $ms->price * $ms->qty;
                            $hmn['type'] = $ms->type;
                            $menu_trans_type[$ms->branch_code.$ms->menu_id][$ms->type] = $hmn;
                        }
                        else{
                            $hmn = $menu_trans_type[$ms->branch_code.$ms->menu_id][$ms->type];
                            $hmn['qty'] += $ms->qty;
                            $hmn['amount'] += $ms->price * $ms->qty;
                            $menu_trans_type[$ms->branch_code.$ms->menu_id][$ms->type] = $hmn;
                        }
                    }

                    //pinabago ni sir dapat daw food and bev lang
                    if($ms->sub_cat_id != 2){
                        //food
                        $sub = $sub_cats[1];
                        $sub['qty'] += $ms->qty;
                        $sub['amount'] += $ms->price * $ms->qty;
                        $sub_cats[1] = $sub;
                    }else{
                        //bev
                        $sub = $sub_cats[2];
                        $sub['qty'] += $ms->qty;
                        $sub['amount'] += $ms->price * $ms->qty;
                        $sub_cats[2] = $sub;
                    }

                    //food type
                    if($ms->food_type != 1){

                        if($ms->sub_cat_id == 2){
                            //beverages
                            $ft = $food_types[2];
                            $ft['qty'] += $ms->qty;
                            $ft['amount'] += $ms->price * $ms->qty;
                            $food_types[2] = $ft;
                        }else{
                            //restaurant
                            $ft = $food_types[0];
                            $ft['qty'] += $ms->qty;
                            $ft['amount'] += $ms->price * $ms->qty;
                            $food_types[0] = $ft;

                        }

                    }else{
                        //bakeshop
                        $ft = $food_types[1];
                        $ft['qty'] += $ms->qty;
                        $ft['amount'] += $ms->price * $ms->qty;
                        $food_types[1] = $ft;
                    }
                    // if(isset($sub_cats[$ms->sub_cat_id])){
                    //     $sub = $sub_cats[$ms->sub_cat_id];
                    //     $sub['qty'] += $ms->qty;
                    //     $sub['amount'] += $ms->price * $ms->qty;
                    //     $sub_cats[$ms->sub_cat_id] = $sub;
                    // }
                    if(isset($cats[$ms->cat_id])){
                        $cat = $cats[$ms->cat_id];
                        $cat['qty'] += $ms->qty;
                        $cat['amount'] += $ms->price * $ms->qty;
                        $cats[$ms->cat_id] = $cat;
                    }
                    $menu_net_total += $ms->price * $ms->qty;
                    $menu_qty_total += $ms->qty;
                    if($ms->free_user_id != "" && $ms->free_user_id != 0){
                        $free_menus[] = $ms;
                    }
                }
                    // echo "<pre>",print_r($menu_trans_type),"</pre>";die();
                // if(count($n_menu_res) > 0){
                //     foreach ($n_menu_res as $ms) {
                //         if(!in_array($ms->sales_id, $ids_used)){
                //             if(!isset($menus[$ms->menu_id])){
                //                 $mn = array();
                //                 $mn['name'] = $ms->menu_name;
                //                 $mn['cat_id'] = $ms->cat_id;
                //                 $mn['qty'] = $ms->qty;
                //                 $mn['amount'] = $ms->price * $ms->qty;
                //                 $mn['sell_price'] = $ms->sell_price;
                //                 $mn['cost_price'] = $ms->costing;
                //                 $mn['code'] = $ms->menu_code;
                //                 $menus[$ms->menu_id] = $mn;
                //             }
                //             else{
                //                 $mn = $menus[$ms->menu_id];
                //                 $mn['qty'] += $ms->qty;
                //                 $mn['amount'] += $ms->price * $ms->qty;
                //                 $menus[$ms->menu_id] = $mn;
                //             }
                //             //pinabago ni sir dapat daw food and bev lang
                //             if($ms->sub_cat_id != 2){
                //                 //food
                //                 $sub = $sub_cats[1];
                //                 $sub['qty'] += $ms->qty;
                //                 $sub['amount'] += $ms->price * $ms->qty;
                //                 $sub_cats[1] = $sub;
                //             }else{
                //                 //bev
                //                 $sub = $sub_cats[2];
                //                 $sub['qty'] += $ms->qty;
                //                 $sub['amount'] += $ms->price * $ms->qty;
                //                 $sub_cats[2] = $sub;
                //             }
                //             // if(isset($sub_cats[$ms->sub_cat_id])){
                //             //     $sub = $sub_cats[$ms->sub_cat_id];
                //             //     $sub['qty'] += $ms->qty;
                //             //     $sub['amount'] += $ms->price * $ms->qty;
                //             //     $sub_cats[$ms->sub_cat_id] = $sub;
                //             // }
                //             if(isset($cats[$ms->cat_id])){
                //                 $cat = $cats[$ms->cat_id];
                //                 $cat['qty'] += $ms->qty;
                //                 $cat['amount'] += $ms->price * $ms->qty;
                //                 $cats[$ms->cat_id] = $cat;
                //             }
                //             $menu_net_total += $ms->price * $ms->qty;
                //             $menu_qty_total += $ms->qty;
                //             if($ms->free_user_id != "" && $ms->free_user_id != 0){
                //                 $free_menus[] = $ms;
                //             }
                //         }
                //     }
                // }
            }
            $total_md = 0;
            $total_smd = 0;
            $mids_used = array();
            $mods = array();
            $sub_mods = array();
            $new_mods = array();
            // echo 'asd';die();
            if(count($ids) > 0){
                $n_menu_cat_sale_mods=array();
                // if($curr){
                //     $this->cashier_model->db = $this->load->database('default', TRUE);
                //     $n_menu_cat_sale_mods = $this->cashier_model->get_trans_sales_menu_modifiers(null,array("trans_sales_menu_modifiers.sales_id"=>$ids));
                // }
                // $this->cashier_model->db = $this->load->database('default', TRUE);
                // if($branch_code != ""){
                    $args = array('trans_sales_menu_modifiers.branch_code'=>$branch_code);
                    $args["concat(trans_sales_menu_modifiers.pos_id,'-',trans_sales_menu_modifiers.sales_id)"] = array('use'=>'where_in','val'=>$ids,'third'=>false);
                    if($brand != ''){
                        $args['trans_sales_menu_modifiers.pos_id']=$brand;
                    }
                $menu_cat_sale_mods = $this->cashier_model->get_trans_sales_menu_modifiers(null,$args);                    
                // }else{
                // $menu_cat_sale_mods = $this->cashier_model->get_trans_sales_menu_modifiers(null,array("trans_sales_menu_modifiers.sales_id"=>$ids));
                // }
                foreach ($menu_cat_sale_mods as $res) {
                    if(!in_array($res->sales_id, $mids_used)){
                        $mids_used[] = $res->sales_id;
                    }
                    if(!isset($mods[$res->menu_id][$res->mod_id])){
                        $mod_sub_cat = $this->site_model->get_tbl('modifiers',array('mod_id'=>$res->mod_id),array(),null,true,'mod_sub_cat_id');
                        $mod_sc = $mod_sub_cat[0];

                        $mods[$res->menu_id][$res->mod_id] = array(
                            'name'=>$res->mod_name,
                            'mod_group_name'=>$res->mod_group_name,
                            'menu_id'=>$res->menu_id,
                            'price'=>$res->price,
                            'qty'=>$res->qty,
                            'total_amt'=>$res->qty * $res->price,
                            'sub_cat'=>$mod_sc->mod_sub_cat_id,
                        );

                        $sub_cat = $mod_sc->mod_sub_cat_id;
                        
                    }
                    else{
                        $mod = $mods[$res->menu_id][$res->mod_id];
                        $mod['qty'] += $res->qty;
                        $mod['total_amt'] += $res->qty * $res->price;
                        $mods[$res->menu_id][$res->mod_id] = $mod;

                        $sub_cat = $mod['sub_cat'];
                    }
                    // if(!isset($new_mods[$res->mod_id])){
                    if(!isset($new_mods[$res->menu_id][$res->mod_id])){
                        $mod_sub_cat = $this->site_model->get_tbl('modifiers',array('mod_id'=>$res->mod_id),array(),null,true,'mod_sub_cat_id');
                        $mod_sc = $mod_sub_cat[0];

                        // $new_mods[$res->mod_id] = array(
                        $new_mods[$res->menu_id][$res->mod_id] = array(
                            'name'=>$res->mod_name,
                            'menu_id'=>$res->menu_id,
                            'price'=>$res->price,
                            'qty'=>$res->qty,
                            'total_amt'=>$res->qty * $res->price,
                            'sub_cat'=>$mod_sc->mod_sub_cat_id,
                        );

                        $sub_cat = $mod_sc->mod_sub_cat_id;
                        
                    }
                    else{
                        // $mod = $new_mods[$res->mod_id];
                        $mod = $new_mods[$res->menu_id][$res->mod_id];
                        $mod['qty'] += $res->qty;
                        $mod['total_amt'] += $res->qty * $res->price;
                        // $new_mods[$res->mod_id] = $mod;
                         $new_mods[$res->menu_id][$res->mod_id] = $mod;

                        // $sub_cat = $mod['sub_cat'];
                    }

                    if($sub_cat != 2){
                        //food
                        $sub_cats[1]['amount'] += $res->qty * $res->price;
                    }else{
                        //bev
                        $sub_cats[2]['amount'] += $res->qty * $res->price;
                    }

                }
                // echo "<pre>",print_r($new_mods),"</pre>";die();
                // if(count($n_menu_cat_sale_mods) > 0){
                //     foreach ($n_menu_cat_sale_mods as $res) {
                //         if(!in_array($res->sales_id, $mids_used)){
                //             if(!isset($mods[$res->mod_id])){
                //                 $mods[$res->mod_id] = array(
                //                     'name'=>$res->mod_name,
                //                     'price'=>$res->price,
                //                     'qty'=>$res->qty,
                //                     'total_amt'=>$res->qty * $res->price,
                //                 );
                //             }
                //             else{
                //                 $mod = $mods[$res->mod_id];
                //                 $mod['qty'] += $res->qty;
                //                 $mod['total_amt'] += $res->qty * $res->price;
                //                 $mods[$res->mod_id] = $mod;
                //             }
                //         }
                //     }
                // }
                foreach ($mods as $menu_ids => $vv) {
                    foreach ($vv as $modid => $md) {
                        $total_md += $md['total_amt'];

                        // if(isset($md['submodifiers'])){
                        //     foreach ($md['submodifiers'] as $skey => $svalue) {
                        //         // foreach ($svalue as $mod_sub_id => $vals) {
                        //             $total_smd += $svalue['total_amt'];
                        //         // }
                        //     }
                        // }

                    }
                }

                
                if($branch_code != ""){
                    $args = array('trans_sales_menu_submodifiers.branch_code'=>$branch_code);
                    $args["concat(trans_sales_menu_submodifiers.pos_id,'-',trans_sales_menu_submodifiers.sales_id)"] = array('use'=>'where_in','val'=>$ids,'third'=>false);
                    if($brand != ''){
                        $args['trans_sales_menu_submodifiers.pos_id']=$brand;
                    }
                    $menu_cat_sale_submods = $this->cashier_model->get_trans_sales_menu_submodifiers_prints(null,$args);              
                }else{
                    $menu_cat_sale_submods = $this->cashier_model->get_trans_sales_menu_submodifiers_prints(null,array("trans_sales_menu_submodifiers.sales_id"=>$ids));
                }

                // $sub_mods = array();
                // echo "<pre>",print_r($menu_cat_sale_submods),"</pre>";die();
                foreach ($menu_cat_sale_submods as $subm) {
                    // if($res->mod_id == $subm->mod_id){
                        
                        if(isset($sub_mods[$subm->mod_id][$subm->mod_sub_id])){
                            $row = $sub_mods[$subm->mod_id][$subm->mod_sub_id];
                            $row['total_amt'] += $subm->price * $subm->qty;
                            $row['qty'] += $subm->qty;

                            $sub_mods[$subm->mod_id][$subm->mod_sub_id] = $row;

                            $sub_cat = $row['sub_cat'];

                        }else{
                            $mod_sub_cat = $this->site_model->get_tbl('modifiers',array('mod_id'=>$subm->mod_id),array(),null,true,'mod_sub_cat_id');
                            $mod_sc = $mod_sub_cat[0];

                            $sub_mods[$subm->mod_id][$subm->mod_sub_id] = array(
                                'name'=>$subm->submod_name,
                                'price'=>$subm->price,
                                'qty'=>$subm->qty,
                                'mod_id'=>$subm->mod_id,
                                'total_amt'=>$subm->price * $subm->qty,
                                'sub_cat'=>$mod_sc->mod_sub_cat_id,
                                // 'qty'=>$subm->qty,
                            );

                            $sub_cat = $mod_sc->mod_sub_cat_id;
                        }

                        if($sub_cat != 2){
                            //food
                            $sub_cats[1]['amount'] += $subm->qty * $subm->price;
                        }else{
                            //bev
                            $sub_cats[2]['amount'] += $subm->qty * $subm->price;
                        }

                    // }
                }

                foreach ($sub_mods as $mod_ids => $vv) {
                    foreach ($vv as $smodid => $smd) {
                        $total_smd += $smd['total_amt'];

                        // if(isset($md['submodifiers'])){
                        //     foreach ($md['submodifiers'] as $skey => $svalue) {
                        //         // foreach ($svalue as $mod_sub_id => $vals) {
                        //             $total_smd += $svalue['total_amt'];
                        //         // }
                        //     }
                        // }

                    }
                }
            }
            #ITEMS

            $items = array();
            if(count($ids) > 0){
                $select = 'trans_sales_items.*,items.code as item_code,items.name as item_name,items.cost as item_cost';
                $join = null;
                $join['items'] = array('content'=>"trans_sales_items.item_id = items.item_id AND items.branch_code= trans_sales_items.branch_code");
                $n_item_res = array();
                // if($curr){
                //     $this->site_model->db = $this->load->database('default', TRUE);
                //     $n_item_res = $this->site_model->get_tbl('trans_sales_items',array('sales_id'=>$ids),array(),$join,true,$select);
                // }
                // $this->site_model->db= $this->load->database('default', TRUE);
                if($branch_code != ""){
    // echo 1;die();
                    $args = array('trans_sales_items.branch_code'=>$branch_code);
                    $args["concat(trans_sales_items.pos_id,'-',trans_sales_items.sales_id)"] = array('use'=>'where_in','val'=>$ids,'third'=>false);
                    if($brand != ''){
                        $args['trans_sales_items.pos_id']=$brand;
                    }

                    $item_res = $this->site_model->get_tbl('trans_sales_items',$args,array(),$join,true,$select,array('sales_item_id'));
                }else{
                    // echo 2;die();
                    $args = array();
                    $args["concat(trans_sales_items.pos_id,'-',trans_sales_items.sales_id)"] = array('use'=>'where_in','val'=>$ids,'third'=>false);
                    $item_res = $this->site_model->get_tbl('trans_sales_items',$args,array(),$join,true,$select,array('sales_item_id'));
                }
                 // echo $this->site_model->db->last_query();die();
                
                $itids_used = array();
                
                foreach ($item_res as $ms) {
                    if(!in_array($ms->sales_id, $itids_used)){
                        $itids_used[] = $ms->sales_id;
                    }
                    if(!isset($items[$ms->item_id])){
                        $mn = array();
                        $mn['name'] = $ms->item_name;
                        $mn['qty'] = $ms->qty;
                        $mn['price'] = $ms->price;
                        $mn['code'] = $ms->item_code;
                        $mn['amount'] = $ms->price * $ms->qty;
                        $items[$ms->item_id] = $mn;
                    }
                    else{
                        $mn = $items[$ms->item_id];
                        $mn['qty'] += $ms->qty;
                        $mn['amount'] += $ms->price * $ms->qty;
                        $items[$ms->item_id] = $mn;

                    }
                    $item_net_total += $ms->price * $ms->qty;
                    $item_qty_total += $ms->qty;
                }
                // if(count($n_item_res) > 0){
                //     foreach ($n_item_res as $ms) {
                //         if(!in_array($ms->sales_id, $itids_used)){
                //             if(!isset($items[$ms->item_id])){
                //                 $mn = array();
                //                 $mn['name'] = $ms->item_name;
                //                 $mn['qty'] = $ms->qty;
                //                 $mn['price'] = $ms->price;
                //                 $mn['code'] = $ms->item_code;
                //                 $mn['amount'] = $ms->price * $ms->qty;
                //                 $items[$ms->item_id] = $mn;
                //             }
                //             else{
                //                 $mn = $items[$ms->item_id];
                //                 $mn['qty'] += $ms->qty;
                //                 $mn['amount'] += $ms->price * $ms->qty;
                //                 $items[$ms->item_id] = $mn;
                //             }
                //             $item_net_total += $ms->price * $ms->qty;
                //             $item_qty_total += $ms->qty;
                //         }
                //     }
                // }    

            }
            // echo  $menu_net_total."<br>".$total_md."<br>".$item_net_total."<br>";die();
            // var_dump($menu_net_total+$total_md+$item_net_total); die();
             // echo "<pre>",print_r($menu_trans_type),"</pre>";die();
            return array('gross'=>$menu_net_total+$total_md+$item_net_total+$total_smd,'menu_total'=>$menu_net_total,'total_qty'=>$menu_qty_total,'menus'=>$menus,'cats'=>$cats,'sub_cats'=>$sub_cats,'mods_total'=>$total_md,'mods'=>$mods,'free_menus'=>$free_menus,
                'items'=>$items,'item_total'=>$item_net_total,'item_total_qty'=>$item_qty_total,'submods_total'=>$total_smd,'submods'=>$sub_mods,'food_type'=>$food_types,'menu_trans_type'=>$menu_trans_type,'new_mods'=>$new_mods);
        }

        public function menu_top_sales($ids=array(),$curr=false,$branch_code="",$brand="",$from="",$to=""){
            $cats = array();
            // $this->site_model->db = $this->load->database('default', TRUE);
            if($branch_code){
                $args = array('branch_code'=>$branch_code);
                // if($brand != ''){
                //     $args['brand'] = $brand;
                // }
                $cat_res = $this->site_model->get_tbl('menu_categories',$args);
            }else{
                $cat_res = $this->site_model->get_tbl('menu_categories',array());
            }
            foreach ($cat_res as $ces) {
                $cats[$ces->menu_cat_id] = array('cat_id'=>$ces->menu_cat_id,'name'=>$ces->menu_cat_name,'qty'=>0,'amount'=>0);
            }
             // echo $this->site_model->db->last_query(); die();
            $sub_cats = array();
            // $sales_menu_cat = $this->menu_model->get_menu_subcategories(null,true);
            // foreach ($sales_menu_cat as $rc) {
            //     $sub_cats[$rc->menu_sub_cat_id] = array('name'=>$rc->menu_sub_cat_name,'qty'=>0,'amount'=>0);
            // }

            //sabi ni sir hardcode na food and bev lang ang menu sub cat
            $sub_cats[1] = array('name'=>'FOOD','qty'=>0,'amount'=>0);
            $sub_cats[2] = array('name'=>'BEVERAGE','qty'=>0,'amount'=>0);

            $food_types[0] = array('name'=>'RESTAURANT','qty'=>0,'amount'=>0);
            $food_types[1] = array('name'=>'BAKESHOP','qty'=>0,'amount'=>0);
            $food_types[2] = array('name'=>'BEVERAGE','qty'=>0,'amount'=>0);

            $menu_net_total = 0;
            $menu_qty_total = 0;
            $item_net_total = 0;
            $item_qty_total = 0;
            $menus = array();
            $free_menus = array();
            $ids_used = array();
            // echo "<pre>",print_r($ids),"</pre>";die();
            if(count($ids) > 0){
                $select = 'trans_sales_menus.*,menus.menu_code,menus.menu_name,menus.cost as sell_price,menus.menu_cat_id as cat_id,menus.menu_sub_cat_id as sub_cat_id, menus.costing, menus.food_type,branch_details.branch_name';
                $join = null;
                if($branch_code != ""){
                $join['menus'] = array('content'=>"trans_sales_menus.menu_id = menus.menu_id  AND menus.branch_code = trans_sales_menus.branch_code");
                }else{
                $join['menus'] = array('content'=>'trans_sales_menus.menu_id = menus.menu_id AND trans_sales_menus.branch_code = menus.branch_code');
                }
                $join['branch_details'] = array('content'=>'trans_sales_menus.branch_code = menus.branch_code AND trans_sales_menus.branch_code = branch_details.branch_code');

                $join['trans_sales use index(branch_code)'] = array('content'=>'trans_sales_menus.sales_id = trans_sales.sales_id AND trans_sales_menus.branch_code = trans_sales.branch_code && trans_sales_menus.pos_id = trans_sales.pos_id');
                $n_menu_res = array();
                // if($curr){
                //     $this->site_model->db = $this->load->database('default', TRUE);
                //     $n_menu_res = $this->site_model->get_tbl('trans_sales_menus',array('sales_id'=>$ids,'trans_sales_menus.branch_code'=>$branch_code),array(),$join,true,$select);
                // }
                $this->site_model->db= $this->load->database('default', TRUE);
                if($branch_code != ""){
                    $args = array('trans_sales_menus.sales_id'=>$ids,'trans_sales_menus.branch_code'=>$branch_code,'trans_sales.inactive'=>0,'menus.menu_sub_id !='=>7,'type_id'=>10,'trans_sales.datetime >='=>$from,'trans_sales.datetime <='=>$to);
                    if($brand != ''){
                        $args['trans_sales_menus.pos_id']=$brand;
                    }
                    $menu_res = $this->site_model->get_tbl('trans_sales_menus use index(branch_code)',$args,array(),$join,true,$select,array('sales_menu_id','branch_code','pos_id'));
                }else{
                    $menu_res = $this->site_model->get_tbl('trans_sales_menus use index(branch_code)',array('trans_sales_menus.sales_id'=>$ids,'trans_sales.inactive'=>0,'type_id'=>10),array(),$join,true,$select,array('sales_menu_id','branch_code','pos_id'));
                }                
                // echo $this->site_model->db->last_query(); die();
                // echo "<pre>",print_r($menu_res),"</pre>";die();
                foreach ($menu_res as $ms) {
                    if(!in_array($ms->sales_id, $ids_used)){
                        $ids_used[] = $ms->sales_id;
                    }
                    if(!isset($menus[$ms->branch_code.$ms->menu_id])){
                        $mn = array();
                        $mn['name'] = $ms->menu_name;
                        $mn['branch_code'] = $ms->branch_code;
                        $mn['branch_name'] = $ms->branch_name;
                        $mn['cat_id'] = $ms->cat_id;
                        $mn['qty'] = $ms->qty;
                        $mn['sell_price'] = $ms->sell_price;
                        $mn['cost_price'] = $ms->costing;
                        $mn['code'] = $ms->menu_code;
                        $mn['amount'] = $ms->price * $ms->qty;
                        $menus[$ms->branch_code.$ms->menu_id] = $mn;
                    }
                    else{
                        $mn = $menus[$ms->branch_code.$ms->menu_id];
                        $mn['qty'] += $ms->qty;
                        $mn['amount'] += $ms->price * $ms->qty;
                        $menus[$ms->branch_code.$ms->menu_id] = $mn;
                    }

                    //pinabago ni sir dapat daw food and bev lang
                    if($ms->sub_cat_id != 2){
                        //food
                        $sub = $sub_cats[1];
                        $sub['qty'] += $ms->qty;
                        $sub['amount'] += $ms->price * $ms->qty;
                        $sub_cats[1] = $sub;
                    }else{
                        //bev
                        $sub = $sub_cats[2];
                        $sub['qty'] += $ms->qty;
                        $sub['amount'] += $ms->price * $ms->qty;
                        $sub_cats[2] = $sub;
                    }

                    //food type
                    if($ms->food_type != 1){

                        if($ms->sub_cat_id == 2){
                            //beverages
                            $ft = $food_types[2];
                            $ft['qty'] += $ms->qty;
                            $ft['amount'] += $ms->price * $ms->qty;
                            $food_types[2] = $ft;
                        }else{
                            //restaurant
                            $ft = $food_types[0];
                            $ft['qty'] += $ms->qty;
                            $ft['amount'] += $ms->price * $ms->qty;
                            $food_types[0] = $ft;

                        }

                    }else{
                        //bakeshop
                        $ft = $food_types[1];
                        $ft['qty'] += $ms->qty;
                        $ft['amount'] += $ms->price * $ms->qty;
                        $food_types[1] = $ft;
                    }
                    // if(isset($sub_cats[$ms->sub_cat_id])){
                    //     $sub = $sub_cats[$ms->sub_cat_id];
                    //     $sub['qty'] += $ms->qty;
                    //     $sub['amount'] += $ms->price * $ms->qty;
                    //     $sub_cats[$ms->sub_cat_id] = $sub;
                    // }
                    if(isset($cats[$ms->cat_id])){
                        $cat = $cats[$ms->cat_id];
                        $cat['qty'] += $ms->qty;
                        $cat['amount'] += $ms->price * $ms->qty;
                        $cats[$ms->cat_id] = $cat;
                    }
                    $menu_net_total += $ms->price * $ms->qty;
                    $menu_qty_total += $ms->qty;
                    if($ms->free_user_id != "" && $ms->free_user_id != 0){
                        $free_menus[] = $ms;
                    }
                }
                // if(count($n_menu_res) > 0){
                //     foreach ($n_menu_res as $ms) {
                //         if(!in_array($ms->sales_id, $ids_used)){
                //             if(!isset($menus[$ms->menu_id])){
                //                 $mn = array();
                //                 $mn['name'] = $ms->menu_name;
                //                 $mn['cat_id'] = $ms->cat_id;
                //                 $mn['qty'] = $ms->qty;
                //                 $mn['amount'] = $ms->price * $ms->qty;
                //                 $mn['sell_price'] = $ms->sell_price;
                //                 $mn['cost_price'] = $ms->costing;
                //                 $mn['code'] = $ms->menu_code;
                //                 $menus[$ms->menu_id] = $mn;
                //             }
                //             else{
                //                 $mn = $menus[$ms->menu_id];
                //                 $mn['qty'] += $ms->qty;
                //                 $mn['amount'] += $ms->price * $ms->qty;
                //                 $menus[$ms->menu_id] = $mn;
                //             }
                //             //pinabago ni sir dapat daw food and bev lang
                //             if($ms->sub_cat_id != 2){
                //                 //food
                //                 $sub = $sub_cats[1];
                //                 $sub['qty'] += $ms->qty;
                //                 $sub['amount'] += $ms->price * $ms->qty;
                //                 $sub_cats[1] = $sub;
                //             }else{
                //                 //bev
                //                 $sub = $sub_cats[2];
                //                 $sub['qty'] += $ms->qty;
                //                 $sub['amount'] += $ms->price * $ms->qty;
                //                 $sub_cats[2] = $sub;
                //             }
                //             // if(isset($sub_cats[$ms->sub_cat_id])){
                //             //     $sub = $sub_cats[$ms->sub_cat_id];
                //             //     $sub['qty'] += $ms->qty;
                //             //     $sub['amount'] += $ms->price * $ms->qty;
                //             //     $sub_cats[$ms->sub_cat_id] = $sub;
                //             // }
                //             if(isset($cats[$ms->cat_id])){
                //                 $cat = $cats[$ms->cat_id];
                //                 $cat['qty'] += $ms->qty;
                //                 $cat['amount'] += $ms->price * $ms->qty;
                //                 $cats[$ms->cat_id] = $cat;
                //             }
                //             $menu_net_total += $ms->price * $ms->qty;
                //             $menu_qty_total += $ms->qty;
                //             if($ms->free_user_id != "" && $ms->free_user_id != 0){
                //                 $free_menus[] = $ms;
                //             }
                //         }
                //     }
                // }
            }
            $total_md = 0;
            $total_smd = 0;
            $mids_used = array();
            $mods = array();
            $sub_mods = array();
            if(count($ids) > 0){
                $n_menu_cat_sale_mods=array();
                // if($curr){
                //     $this->cashier_model->db = $this->load->database('default', TRUE);
                //     $n_menu_cat_sale_mods = $this->cashier_model->get_trans_sales_menu_modifiers(null,array("trans_sales_menu_modifiers.sales_id"=>$ids));
                // }
                $this->cashier_model->db = $this->load->database('default', TRUE);
                if($branch_code != ""){
                    $args = array("trans_sales_menu_modifiers.sales_id"=>$ids,'trans_sales_menu_modifiers.branch_code'=>$branch_code);
                    if($brand != ''){
                        $args['trans_sales_menu_modifiers.pos_id']=$brand;
                    }
                $menu_cat_sale_mods = $this->cashier_model->get_trans_sales_menu_modifiers(null,$args);                    
                }else{
                $menu_cat_sale_mods = $this->cashier_model->get_trans_sales_menu_modifiers(null,array("trans_sales_menu_modifiers.sales_id"=>$ids));
                }
                foreach ($menu_cat_sale_mods as $res) {
                    if(!in_array($res->sales_id, $mids_used)){
                        $mids_used[] = $res->sales_id;
                    }
                    if(!isset($mods[$res->mod_id])){
                        $mods[$res->mod_id] = array(
                            'name'=>$res->mod_name,
                            'price'=>$res->price,
                            'qty'=>$res->qty,
                            'total_amt'=>$res->qty * $res->price,
                        );
                    }
                    else{
                        $mod = $mods[$res->mod_id];
                        $mod['qty'] += $res->qty;
                        $mod['total_amt'] += $res->qty * $res->price;
                        $mods[$res->mod_id] = $mod;
                    }
                }
                // if(count($n_menu_cat_sale_mods) > 0){
                //     foreach ($n_menu_cat_sale_mods as $res) {
                //         if(!in_array($res->sales_id, $mids_used)){
                //             if(!isset($mods[$res->mod_id])){
                //                 $mods[$res->mod_id] = array(
                //                     'name'=>$res->mod_name,
                //                     'price'=>$res->price,
                //                     'qty'=>$res->qty,
                //                     'total_amt'=>$res->qty * $res->price,
                //                 );
                //             }
                //             else{
                //                 $mod = $mods[$res->mod_id];
                //                 $mod['qty'] += $res->qty;
                //                 $mod['total_amt'] += $res->qty * $res->price;
                //                 $mods[$res->mod_id] = $mod;
                //             }
                //         }
                //     }
                // }
                foreach ($mods as $modid => $md) {
                    $total_md += $md['total_amt'];
                }

                
                if($branch_code != ""){
                    $args = array("trans_sales_menu_submodifiers.sales_id"=>$ids,'trans_sales_menu_submodifiers.branch_code'=>$branch_code);
                    if($brand != ''){
                        $args['trans_sales_menu_submodifiers.pos_id']=$brand;
                    }
                    $menu_cat_sale_submods = $this->cashier_model->get_trans_sales_menu_submodifiers_prints(null,$args);              
                }else{
                    $menu_cat_sale_submods = $this->cashier_model->get_trans_sales_menu_submodifiers_prints(null,array("trans_sales_menu_submodifiers.sales_id"=>$ids));
                }

                // $sub_mods = array();
                // echo "<pre>",print_r($menu_cat_sale_submods),"</pre>";die();
                foreach ($menu_cat_sale_submods as $subm) {
                    // if($res->mod_id == $subm->mod_id){
                        
                        if(isset($sub_mods[$subm->mod_id][$subm->mod_sub_id])){
                            $row = $sub_mods[$subm->mod_id][$subm->mod_sub_id];
                            $row['total_amt'] += $subm->price * $subm->qty;
                            $row['qty'] += $subm->qty;

                            $sub_mods[$subm->mod_id][$subm->mod_sub_id] = $row;

                        }else{

                            $sub_mods[$subm->mod_id][$subm->mod_sub_id] = array(
                                'name'=>$subm->submod_name,
                                'price'=>$subm->price,
                                'qty'=>$subm->qty,
                                'mod_id'=>$subm->mod_id,
                                'total_amt'=>$subm->price * $subm->qty,
                                // 'qty'=>$subm->qty,
                            );
                        }


                    // }
                }

                foreach ($sub_mods as $mod_ids => $vv) {
                    foreach ($vv as $smodid => $smd) {
                        $total_smd += $smd['total_amt'];

                        // if(isset($md['submodifiers'])){
                        //     foreach ($md['submodifiers'] as $skey => $svalue) {
                        //         // foreach ($svalue as $mod_sub_id => $vals) {
                        //             $total_smd += $svalue['total_amt'];
                        //         // }
                        //     }
                        // }

                    }
                }
            }
            #ITEMS

            $items = array();
            if(count($ids) > 0){
                $select = 'trans_sales_items.*,items.code as item_code,items.name as item_name,items.cost as item_cost';
                $join = null;
                $join['items'] = array('content'=>"trans_sales_items.item_id = items.item_id AND items.branch_code= trans_sales_items.branch_code");
                $n_item_res = array();
                // if($curr){
                //     $this->site_model->db = $this->load->database('default', TRUE);
                //     $n_item_res = $this->site_model->get_tbl('trans_sales_items',array('sales_id'=>$ids),array(),$join,true,$select);
                // }
                $this->site_model->db= $this->load->database('default', TRUE);
                if($branch_code != ""){

                    $args = array('sales_id'=>$ids,'trans_sales_items.branch_code'=>$branch_code);
                    if($brand != ''){
                        $args['trans_sales_items.pos_id']=$brand;
                    }

                    $item_res = $this->site_model->get_tbl('( select * from trans_sales_items group by branch_code,sales_item_id,pos_id )trans_sales_items',$args,array(),$join,true,$select,array('sales_item_id'));
                }else{
                    $item_res = $this->site_model->get_tbl('( select * from trans_sales_items group by branch_code,sales_item_id,pos_id )trans_sales_items',array('sales_id'=>$ids),array(),$join,true,$select,array('sales_item_id'));
                }
                 // echo $this->site_model->db->last_query();die();
                
                $itids_used = array();
                
                foreach ($item_res as $ms) {
                    if(!in_array($ms->sales_id, $itids_used)){
                        $itids_used[] = $ms->sales_id;
                    }
                    if(!isset($items[$ms->item_id])){
                        $mn = array();
                        $mn['name'] = $ms->item_name;
                        $mn['qty'] = $ms->qty;
                        $mn['price'] = $ms->price;
                        $mn['code'] = $ms->item_code;
                        $mn['amount'] = $ms->price * $ms->qty;
                        $items[$ms->item_id] = $mn;
                    }
                    else{
                        $mn = $items[$ms->item_id];
                        $mn['qty'] += $ms->qty;
                        $mn['amount'] += $ms->price * $ms->qty;
                        $items[$ms->item_id] = $mn;

                    }
                    $item_net_total += $ms->price * $ms->qty;
                    $item_qty_total += $ms->qty;
                }
                // if(count($n_item_res) > 0){
                //     foreach ($n_item_res as $ms) {
                //         if(!in_array($ms->sales_id, $itids_used)){
                //             if(!isset($items[$ms->item_id])){
                //                 $mn = array();
                //                 $mn['name'] = $ms->item_name;
                //                 $mn['qty'] = $ms->qty;
                //                 $mn['price'] = $ms->price;
                //                 $mn['code'] = $ms->item_code;
                //                 $mn['amount'] = $ms->price * $ms->qty;
                //                 $items[$ms->item_id] = $mn;
                //             }
                //             else{
                //                 $mn = $items[$ms->item_id];
                //                 $mn['qty'] += $ms->qty;
                //                 $mn['amount'] += $ms->price * $ms->qty;
                //                 $items[$ms->item_id] = $mn;
                //             }
                //             $item_net_total += $ms->price * $ms->qty;
                //             $item_qty_total += $ms->qty;
                //         }
                //     }
                // }    

            }
            // echo  $menu_net_total."<br>".$total_md."<br>".$item_net_total."<br>";die();
            // var_dump($menu_net_total+$total_md+$item_net_total); die();
             // echo "<pre>",print_r($menus),"</pre>";die();
            return array('gross'=>$menu_net_total+$total_md+$item_net_total+$total_smd,'menu_total'=>$menu_net_total,'total_qty'=>$menu_qty_total,'menus'=>$menus,'cats'=>$cats,'sub_cats'=>$sub_cats,'mods_total'=>$total_md,'mods'=>$mods,'free_menus'=>$free_menus,
                'items'=>$items,'item_total'=>$item_net_total,'item_total_qty'=>$item_qty_total,'submods_total'=>$total_smd,'submods'=>$sub_mods,'food_type'=>$food_types);
        }

         public function menu_toppings($ids=array(),$branch_code=""){
            $this->site_model->db = $this->load->database('default', TRUE);
           
            $menus = array();
            if(count($ids) > 0){
                $select = 'trans_sales_menus.*,menus.menu_code,menus.menu_name,menus.cost as sell_price,menus.menu_cat_id as cat_id,menus.menu_sub_cat_id as sub_cat_id, menus.costing';
                $join = null;
                if($branch_code != ""){
                $join['menus'] = array('content'=>"trans_sales_menus.menu_id = menus.menu_id  AND menus.branch_code = trans_sales_menus.branch_code");
                }else{
                $join['menus'] = array('content'=>'trans_sales_menus.menu_id = menus.menu_id AND trans_sales_menus.branch_code = menus.branch_code');
                }
                $n_menu_res = array();
               
                $this->site_model->db= $this->load->database('default', TRUE);
                if($branch_code != ""){
                    $menu_res = $this->site_model->get_tbl('( select * from trans_sales_menus group by branch_code,sales_menu_id,pos_id )trans_sales_menus',array('sales_id'=>$ids,'trans_sales_menus.branch_code'=>$branch_code,'menus.menu_sub_id !='=>7),array(),$join,true,$select);
                }else{
                    $menu_res = $this->site_model->get_tbl('( select * from trans_sales_menus group by branch_code,sales_menu_id,pos_id )trans_sales_menus',array('sales_id'=>$ids),array(),$join,true,$select);
                }                

                foreach ($menu_res as $ms) {
                    if(!isset($menus[$ms->menu_id])){
                        $mn = array();
                        $mn['name'] = $ms->menu_name;
                        $mn['branch_code'] = $ms->branch_code;
                        $mn['cat_id'] = $ms->cat_id;
                        $mn['qty'] = $ms->qty;
                        $mn['sell_price'] = $ms->sell_price;
                        $mn['cost_price'] = $ms->costing;
                        $mn['code'] = $ms->menu_code;
                        $mn['amount'] = $ms->price * $ms->qty;
                        $menus[$ms->menu_id] = $mn;
                    }
                    else{
                        $mn = $menus[$ms->menu_id];
                        $mn['qty'] += $ms->qty;
                        $mn['amount'] += $ms->price * $ms->qty;
                        $menus[$ms->menu_id] = $mn;
                    }                    
                }
                
            }           
            
            return $menus;
        }

        public function charges_sales($ids=array(),$curr=false,$branch_code="",$brand="",$cargs=array()){
            $total_charges = 0;
            $charges = array();
            $ids_used = array();

            $user = $this->session->userdata('user');
            $sess_id = $user['sess_id'];

            if(count($ids) > 0){
                $args["concat(trans_sales_charges.pos_id,'-',trans_sales_charges.sales_id)"] = array('use'=>'where_in','val'=>$ids,'third'=>false);
                $cargs["trans_sales_charges.branch_code"] = $branch_code;

                if($brand != ''){
                    $cargs["trans_sales_charges.pos_id"] = $brand;
                }
                $n_cesults = array();
                // if($curr){
                //     $this->site_model->db = $this->load->database('default', TRUE);
                //     $n_cesults = $this->site_model->get_tbl('trans_sales_charges',$cargs);
                // }
                $join['trans_sales'.$sess_id .' trans_sales'] = array('content'=>'trans_sales_charges.sales_id = trans_sales.sales_id && trans_sales_charges.branch_code = trans_sales.branch_code && trans_sales_charges.pos_id = trans_sales.pos_id','mode'=>'left');

                // $this->site_model->db = $this->load->database('default', TRUE);
                $cesults = $this->site_model->get_tbl('trans_sales'.$sess_id.'_charges trans_sales_charges',$cargs,array(),$join);
                // echo $this->site_model->db->last_query();die();

                foreach ($cesults as $ces) {
                    if(!in_array($ces->sales_id, $ids_used)){
                        $ids_used[] = $ces->sales_id;
                    }
                    if(!isset($charges[$ces->charge_id])){
                        $charges[$ces->charge_id] = array(
                            'amount'=>$ces->amount,
                            'name'=>$ces->charge_name,
                            'code'=>$ces->charge_code,
                            'qty'=>1
                        );
                    }
                    else{
                        $ch = $charges[$ces->charge_id];
                        $ch['amount'] += $ces->amount;
                        $ch['qty'] += 1;
                        $charges[$ces->charge_id] = $ch;
                    }
                    $total_charges += $ces->amount;
                }
                // if(count($n_cesults) > 0){
                //     foreach ($n_cesults as $ces) {
                //         if(!in_array($ces->sales_id, $ids_used)){
                //             if(!isset($charges[$ces->charge_id])){
                //                 $charges[$ces->charge_id] = array(
                //                     'amount'=>round($ces->amount,2),
                //                     'name'=>$ces->charge_name,
                //                     'code'=>$ces->charge_code,
                //                     'qty'=>1
                //                 );
                //             }
                //             else{
                //                 $ch = $charges[$ces->charge_id];
                //                 $ch['amount'] += round($ces->amount,2);
                //                 $ch['qty'] += 1;
                //                 $charges[$ces->charge_id] = $ch;
                //             }
                //             $total_charges += round($ces->amount,2);
                //         }
                //     }
                // }
            }
            return array('total'=>$total_charges,'types'=>$charges);
        }
        public function local_tax_sales($ids=array(),$curr=false,$branch_code="",$brand="",$cargs=array()){
            $total_local_tax = 0;
            $ids_used = array();

            $user = $this->session->userdata('user');
            $sess_id = $user['sess_id'];

            if(count($ids) > 0){
                $cargs["concat(trans_sales_local_tax.pos_id,'-',trans_sales_local_tax.sales_id)"] = array('use'=>'where_in','val'=>$ids,'third'=>false);
                $cargs["trans_sales_local_tax.branch_code"] = $branch_code;

                if($brand != ''){
                    $cargs["trans_sales_local_tax.pos_id"] = $brand;
                }
                $n_cesults = array();
                // if($curr){
                //     $this->site_model->db = $this->load->database('default', TRUE);
                //     $n_cesults = $this->site_model->get_tbl('trans_sales_local_tax',$cargs);
                // }

                $join['trans_sales'.$sess_id.' trans_sales'] = array('content'=>'trans_sales_local_tax.sales_id = trans_sales.sales_id && trans_sales_local_tax.branch_code = trans_sales.branch_code && trans_sales_local_tax.pos_id = trans_sales.pos_id','mode'=>'left');

                // $this->site_model->db = $this->load->database('default', TRUE);
                $cesults = $this->site_model->get_tbl('trans_sales'.$sess_id.'_local_tax trans_sales_local_tax',$cargs,array(),$join);

                foreach ($cesults as $ces) {
                    if(!in_array($ces->sales_id, $ids_used)){
                        $ids_used[] = $ces->sales_id;
                    }
                    $total_local_tax += $ces->amount;
                }
                // if(count($n_cesults) > 0){
                //     foreach ($n_cesults as $ces) {
                //         if(!in_array($ces->sales_id, $ids_used)){
                //             $total_local_tax += $ces->amount;
                //         }
                //     }
                // }
            }
            return array('total'=>$total_local_tax);
        }
        public function tax_sales($ids=array(),$curr=false,$branch_code="",$brand="",$cargs=array()){
            $total_tax = 0;
            $ids_used = array();

            $user = $this->session->userdata('user');
            $sess_id = $user['sess_id'];

            if(count($ids) > 0){
                $cargs["concat(trans_sales_tax.pos_id,'-',trans_sales_tax.sales_id)"] = array('use'=>'where_in','val'=>$ids,'third'=>false);
                $cargs["trans_sales_tax.branch_code"] = $branch_code;

                if($brand != ''){
                    $cargs["trans_sales_tax.pos_id"] = $brand;
                }
                // $n_cesults = array();
                // if($curr){
                    // $this->site_model->db = $this->load->database('default', TRUE);
                    // $n_cesults = $this->site_model->get_tbl('trans_sales_tax',$cargs);
                // }
                $join['trans_sales'.$sess_id. ' trans_sales'] = array('content'=>'trans_sales_tax.sales_id = trans_sales.sales_id && trans_sales_tax.branch_code = trans_sales.branch_code && trans_sales_tax.pos_id = trans_sales.pos_id','mode'=>'left');

                // $this->site_model->db = $this->load->database('default', TRUE);
                $cesults = $this->site_model->get_tbl('trans_sales'.$sess_id.'_tax trans_sales_tax',$cargs,array(),$join);
                foreach ($cesults as $ces) {
                    if(!in_array($ces->sales_id, $ids_used)){
                        $ids_used[] = $ces->sales_id;
                    }
                    $total_tax += $ces->amount;
                }
                // if(count($n_cesults) > 0){
                //     foreach ($n_cesults as $ces) {
                //         if(!in_array($ces->sales_id, $ids_used)){
                //             $total_tax += $ces->amount;
                //         }
                //     }
                // }
            }
            return array('total'=>$total_tax);
        }
        public function no_tax_sales($ids=array(),$curr=false,$branch_code="",$brand="",$cargs=array()){
            $total_no_tax = 0;
            $total_no_tax_round = 0;
            $ids_used = array();

            $user = $this->session->userdata('user');
            $sess_id = $user['sess_id'];

            if(count($ids) > 0){
                $args["concat(trans_sales_no_tax.pos_id,'-',trans_sales_no_tax.sales_id)"] = array('use'=>'where_in','val'=>$ids,'third'=>false);
                $cargs["trans_sales_no_tax.branch_code"] = $branch_code;

                if($brand != ''){
                    $cargs["trans_sales_no_tax.pos_id"] = $brand;
                }
                // $n_cesults = array();
                // if($curr){
                //     $this->site_model->db = $this->load->database('default', TRUE);
                //     $n_cesults = $this->site_model->get_tbl('trans_sales_no_tax',$cargs);
                // }
                // $this->site_model->db = $this->load->database('default', TRUE);

                $join['trans_sales'.$sess_id.' trans_sales'] = array('content'=>'trans_sales_no_tax.sales_id = trans_sales.sales_id && trans_sales_no_tax.branch_code = trans_sales.branch_code && trans_sales_no_tax.pos_id = trans_sales.pos_id','mode'=>'left');
                $cesults = $this->site_model->get_tbl('trans_sales'.$sess_id.'_no_tax trans_sales_no_tax',$cargs,array(),$join);
                foreach ($cesults as $ces) {
                    if(!in_array($ces->sales_id, $ids_used)){
                        $ids_used[] = $ces->sales_id;
                    }
                    $total_no_tax += $ces->amount;
                    $total_no_tax_round += $ces->amount;
                }
                // if(count($n_cesults) > 0){
                //     foreach ($n_cesults as $ces) {
                //         if(!in_array($ces->sales_id, $ids_used)){
                //             $total_no_tax += $ces->amount;
                //             $total_no_tax_round += numInt($ces->amount);
                //         }
                //     }
                // }
            }
            return array('total'=>$total_no_tax);
        }
        public function zero_rated_sales($ids=array(),$curr=false,$branch_code="",$brand="",$cargs=array()){
            $total = 0;
            $ids_used = array();

            $user = $this->session->userdata('user');
            $sess_id = $user['sess_id'];
            
            if(count($ids) > 0){
                 $args["concat(trans_sales_zero_rated.pos_id,'-',trans_sales_zero_rated.sales_id)"] = array('use'=>'where_in','val'=>$ids,'third'=>false);
                $cargs["trans_sales_zero_rated.branch_code"] = $branch_code;

                if($brand != ''){
                    $cargs["trans_sales_zero_rated.pos_id"] = $brand;
                }
                // $n_cesults = array();
                // if($curr){
                //     $this->site_model->db = $this->load->database('default', TRUE);
                //     $n_cesults = $this->site_model->get_tbl('trans_sales_zero_rated',$cargs);
                // }
                // $this->site_model->db = $this->load->database('default', TRUE);

                $join['trans_sales'.$sess_id.' trans_sales'] = array('content'=>'trans_sales_zero_rated.sales_id = trans_sales.sales_id && trans_sales_zero_rated.branch_code = trans_sales.branch_code && trans_sales_zero_rated.pos_id = trans_sales.pos_id','mode'=>'left');
                $cesults = $this->site_model->get_tbl('trans_sales'.$sess_id.'_zero_rated trans_sales_zero_rated',$cargs,array(),$join);

                foreach ($cesults as $ces) {
                    if(!in_array($ces->sales_id, $ids_used)){
                        $ids_used[] = $ces->sales_id;
                    }
                    $total += $ces->amount;
                }
                // if(count($n_cesults) > 0){
                //     foreach ($n_cesults as $ces) {
                //         if(!in_array($ces->sales_id, $ids_used)){
                //             $total += $ces->amount;
                //         }
                //     }
                // }
            }
            return array('total'=>$total);
        }
        // public function discounts_sales($ids=array(),$curr=false,$branch_code="",$brand="",$from="",$to=""){
        //     // echo "<pre>",print_r($ids),"</pre>";die();
        //     $total_disc = 0;
        //     $discounts = array();
        //     $disc_codes = array();
        //     $ids_used = array();
        //     $taxable_disc = 0;
        //     $non_taxable_disc = 0;
        //     $vat_exempt_total = 0;
        //     if(count($ids) > 0){
        //         $n_sales_discs = array();
        //         // if($curr){
        //         //     $this->cashier_model->db = $this->load->database('default', TRUE);
        //         //     $n_sales_discs = $this->cashier_model->get_trans_sales_discounts(null,array("trans_sales_discounts.sales_id"=>$ids,'trans_sales_discounts.branch_code'=>$branch_code));

        //         // }
        //         $this->cashier_model->db = $this->load->database('default', TRUE);
        //         if($branch_code != ""){
        //             $args = array("trans_sales_discounts.sales_id"=>$ids,'trans_sales_discounts.branch_code'=>$branch_code,'trans_sales.datetime >='=>$from,'trans_sales.datetime <='=>$to,'trans_sales.type_id'=>10,'trans_sales.inactive'=>0);
        //             if($brand != ''){
        //                 $args['trans_sales_discounts.pos_id'] = $brand;
        //             }
        //         $sales_discs = $this->cashier_model->get_trans_sales_discounts(null,$args);
        //         }else{
        //         $sales_discs = $this->cashier_model->get_trans_sales_discounts(null,array("trans_sales_discounts.sales_id"=>$ids));

        //         }
        //         echo $this->cashier_model->db->last_query();die();
        //         foreach ($sales_discs as $discs) {
        //             if(!in_array($discs->sales_id, $ids_used)){
        //                 $ids_used[] = $discs->sales_id;
        //             }
        //             if(!isset($disc_codes[$discs->disc_code])){
        //                 $disc_codes[$discs->disc_code] = array('name'=>$discs->disc_name,'qty'=> 1,'amount'=>round($discs->amount,2));
        //             }
        //             else{
        //                 $disc_codes[$discs->disc_code]['qty'] += 1;
        //                 $disc_codes[$discs->disc_code]['amount'] += round($discs->amount,2);
        //             }
        //             $total_disc += $discs->amount;
        //             if($discs->no_tax == 1){
        //                 $non_taxable_disc += $discs->amount;
        //                 // $vat_exempt_total += $discs->vat_ex;
        //             }
        //             else{
        //                 $taxable_disc += $discs->amount;
        //             }
        //         }
        //         // if(count($n_sales_discs) > 0){
        //         //     foreach ($n_sales_discs as $discs) {
        //         //         if(!in_array($discs->sales_id, $ids_used)){
        //         //             if(!isset($disc_codes[$discs->disc_code])){
        //         //                 $disc_codes[$discs->disc_code] = array('name'=>$discs->disc_name,'qty'=> 1,'amount'=>round($discs->amount,2));
        //         //             }
        //         //             else{
        //         //                 $disc_codes[$discs->disc_code]['qty'] += 1;
        //         //                 $disc_codes[$discs->disc_code]['amount'] += round($discs->amount,2);
        //         //             }
        //         //             $total_disc += $discs->amount;
        //         //             if($discs->no_tax == 1){
        //         //                 $non_taxable_disc += round($discs->amount,2);
        //         //             }
        //         //             else{
        //         //                 $taxable_disc += round($discs->amount,2);
        //         //             }
        //         //         }
        //         //     }
        //         // }
        //     }
        //     $discounts['total']=$total_disc;
        //     $discounts['types']=$disc_codes;
        //     $discounts['tax_disc_total']=$taxable_disc;
        //     $discounts['no_tax_disc_total']=$non_taxable_disc;
        //     $discounts['vat_exempt_total']=$vat_exempt_total;
        //     return $discounts;
        // }
        public function discounts_sales($ids=array(),$curr=false,$branch_id=""){
            $total_disc = 0;
            $discounts = array();
            $disc_codes = array();
            $ids_used = array();
            $taxable_disc = 0;
            $non_taxable_disc = 0;


            if(count($ids) > 0){
                // $n_sales_discs = array();
                // if($curr){
                //     $this->cashier_model->db = $this->load->database('default', TRUE);
                //     $n_sales_discs = $this->cashier_model->get_trans_sales_discounts(null,array("trans_sales_discounts.sales_id"=>$ids));
                // }
                // $this->cashier_model->db = $this->load->database('default', TRUE);
                $args = array("trans_sales_discounts.branch_code"=>$branch_id);
                $args["concat(trans_sales_discounts.pos_id,'-',trans_sales_discounts.sales_id)"] = array('use'=>'where_in','val'=>$ids,'third'=>false);
                $sales_discs = $this->cashier_model->get_trans_sales_discounts(null,$args);
                foreach ($sales_discs as $discs) {
                    if(!in_array($discs->sales_id, $ids_used)){
                        $ids_used[] = $discs->sales_id;
                    }
                    if(!isset($disc_codes[$discs->disc_code])){
                        if($discs->no_tax == 0){
                            $disc_codes[$discs->disc_code] = array('name'=>$discs->disc_name,'qty'=> 1,'amount'=>$discs->amount,'sales_id'=>$discs->sales_id,'no_tax'=>$discs->no_tax);
                        }else{
                            $disc_codes[$discs->disc_code] = array('name'=>$discs->disc_name,'qty'=> 1,'amount'=>$discs->amount,'sales_id'=>$discs->sales_id,'no_tax'=>$discs->no_tax);
                        }
                    }
                    else{
                        $disc_codes[$discs->disc_code]['qty'] += 1;
                        $disc_codes[$discs->disc_code]['amount'] += $discs->amount;
                    }
                    $total_disc += $discs->amount;
                    if($discs->no_tax == 1){
                        $non_taxable_disc += $discs->amount;
                    }
                    else{
                        $taxable_disc += $discs->amount;
                    }
                }
                // if(count($n_sales_discs) > 0){
                //     foreach ($n_sales_discs as $discs) {
                //         if(!in_array($discs->sales_id, $ids_used)){
                //             if(!isset($disc_codes[$discs->disc_code])){
                //                 $disc_codes[$discs->disc_code] = array('name'=>$discs->disc_name,'qty'=> 1,'amount'=>$discs->amount);
                //             }
                //             else{
                //                 $disc_codes[$discs->disc_code]['qty'] += 1;
                //                 $disc_codes[$discs->disc_code]['amount'] += $discs->amount;
                //             }
                //             $total_disc += $discs->amount;
                //             if($discs->no_tax == 1){
                //                 $non_taxable_disc += $discs->amount;
                //             }
                //             else{
                //                 $taxable_disc += $discs->amount;
                //             }
                //         }
                //     }
                // }
            }
            $discounts['total']=$total_disc;
            $discounts['types']=$disc_codes;
            $discounts['tax_disc_total']=$taxable_disc;
            $discounts['no_tax_disc_total']=$non_taxable_disc;
            return $discounts;
        }
        public function payment_breakdown_sales($ids=array(),$curr=false){
            $ret = array();
            $total = 0;
            $pays = array();
            $ids_used = array();
            if(count($ids) > 0){
                $n_payments=array();
            }
            $args['trans_sales_payments.sales_id'] = $ids;
            $join['trans_sales'] = array('content'=>'trans_sales_payments.sales_id = trans_sales.sales_id','mode'=>'left');
            $select = "trans_sales_payments.*,trans_sales.sales_id as order_num,trans_sales.trans_ref";
            if($curr){
                $this->site_model->db = $this->load->database('default', TRUE);
                $n_payments = $this->site_model->get_tbl('trans_sales_payments',$args,array('payment_type'=>'asc'),$join,true,$select);
            }
            $this->site_model->db = $this->load->database('default', TRUE);
            $payments = $this->site_model->get_tbl('trans_sales_payments',$args,array('payment_type'=>'asc'),$join,true,$select);
            foreach ($payments as $py) {
                if(!in_array($py->payment_id, $ids_used)){
                    $ids_used[] = $py->payment_id;
                    $pays[$py->payment_id] = $py;
                }
            }
            if(count($n_payments)){
                foreach ($n_payments as $py) {
                    if(!in_array($py->payment_id, $ids_used)){
                        $pays[] = $py;
                    }
                }
            }
            $pay_types = array();
            foreach ($pays as $ctr => $row) {
                if(!in_array($row->payment_type,$pay_types)){
                    $pay_types[] = $row->payment_type;
                }
            }
            return array("payments"=>$pays,"types"=>$pay_types);
        }
        public function payment_sales($ids=array(),$curr=false,$branch_code="",$brand="",$args=array()){
            $ret = array();
            $total = 0;
            $pays = array();

            $ov_total = 0;
            $ov_pays = array();
             $payment_type = array();

            $ids_used = array();
            $gc_excess = 0;
            $cards = array();
            $currency = array();
            if(count($ids) > 0){
                $n_payments=array();
                // if($curr){
                    // $this->cashier_model->db = $this->load->database('default', TRUE);
                    // $n_payments = $this->cashier_model->get_all_trans_sales_payments(null,array("trans_sales_payments.sales_id"=>$ids,"trans_sales_payments.branch_code"=>$branch_code));

                // }
                // $this->cashier_model->db = $this->load->database('default', TRUE);

                $args["concat(trans_sales_payments.pos_id,'-',trans_sales_payments.sales_id)"] = array('use'=>'where_in','val'=>$ids,'third'=>false);
                $args["trans_sales_payments.branch_code"] = $branch_code;
                $args["trans_sales.type_id = 10"] = array('use'=>'where','val'=>null,'third'=>false);
                $args["trans_sales.inactive = 0"] = array('use'=>'where','val'=>null,'third'=>false);

                if($brand != ''){
                    $args["trans_sales_payments.pos_id"] = $brand;
                }

                $payments = $this->cashier_model->get_all_trans_sales_payments(null,$args);
                foreach ($payments as $py) {
                    if(!in_array($py->sales_id, $ids_used)){
                        $ids_used[] = $py->sales_id;
                    }
                    if($py->amount > $py->to_pay)
                        $amount = $py->to_pay;
                    else
                        $amount = $py->amount;
                    if(!isset($pays[$py->payment_type])){
                        $pays[$py->payment_type] = array('qty'=>1,'amount'=>$amount);
                    }
                    else{
                        $pays[$py->payment_type]['qty'] += 1;
                        $pays[$py->payment_type]['amount'] += $amount;
                    }
                    $total += $amount;

                    if($py->payment_type == 'cash'){
                        if($py->card_type != null){

                            if(isset($currency[$py->card_type])){
                                $currency[$py->card_type]['amount'] += $py->card_number;
                                // $cards[$py->card_type]['count'] += 1;
                            }else{
                                $currency[$py->card_type] = array('amount'=>$py->card_number);
                            }

                        }
                    }

                    if($py->payment_type == 'gc'){
                        if($py->amount > $py->to_pay){
                            $excess = $py->amount - $py->to_pay;
                            $gc_excess += $excess;
                        }
                    }

                    if($py->payment_type == 'credit' || $py->payment_type == 'debit'){
                        if(isset($cards[$py->card_type])){
                            $cards[$py->card_type]['amount'] += $amount;
                            $cards[$py->card_type]['count'] += 1;
                        }else{
                            $cards[$py->card_type] = array('amount'=>$amount,'count'=>1);
                        }
                    }

                    $payment_type[] = $py->payment_type;

                }
                // if(count($n_payments) > 0){
                //     foreach ($n_payments as $py) {
                //         if(!in_array($py->sales_id, $ids_used)){
                //             if($py->amount > $py->to_pay)
                //                 $amount = $py->to_pay;
                //             else
                //                 $amount = $py->amount;
                //             if(!isset($pays[$py->payment_type])){
                //                 $pays[$py->payment_type] = array('qty'=>1,'amount'=>$amount);
                //             }
                //             else{
                //                 $pays[$py->payment_type]['qty'] += 1;
                //                 $pays[$py->payment_type]['amount'] += $amount;
                //             }
                //             $total += $amount;

                //             if($py->payment_type == 'gc'){
                //                 if($py->amount > $py->to_pay){
                //                     $excess = $py->amount - $py->to_pay;
                //                     $gc_excess += $excess;
                //                 }
                //             }

                //             if($py->payment_type == 'credit'){
                //                 if(isset($cards[$py->card_type])){
                //                     $cards[$py->card_type]['amount'] += $amount;
                //                     $cards[$py->card_type]['count'] += 1;
                //                 }else{
                //                     $cards[$py->card_type] = array('amount'=>$amount,'count'=>1);
                //                 }
                //             }
                //         }
                //     }
                // }

                //FOR PERI PERI OVER GC
                foreach ($payments as $py) {
                    if(!in_array($py->sales_id, $ids_used)){
                        $ids_used[] = $py->sales_id;
                    }

                    if($py->payment_type == 'gc'){
                        $amount = $py->amount;
                    }
                    else{
                        if($py->amount > $py->to_pay)
                            $amount = $py->to_pay;
                        else
                            $amount = $py->amount;
                    }
                    if(!isset($ov_pays[$py->payment_type])){
                        $ov_pays[$py->payment_type] = array('qty'=>1,'amount'=>$amount);
                    }
                    else{
                        $ov_pays[$py->payment_type]['qty'] += 1;
                        $ov_pays[$py->payment_type]['amount'] += $amount;
                    }
                    $ov_total += $amount;
                }
                if(count($n_payments) > 0){
                    foreach ($n_payments as $py) {
                        if(!in_array($py->sales_id, $ids_used)){

                            if($py->payment_type == 'gc'){
                                $amount = $py->amount;
                            }
                            else{
                                if($py->amount > $py->to_pay)
                                    $amount = $py->to_pay;
                                else
                                    $amount = $py->amount;
                            }
                            if(!isset($ov_pays[$py->payment_type])){
                                $ov_pays[$py->payment_type] = array('qty'=>1,'amount'=>$amount);
                            }
                            else{
                                $ov_pays[$py->payment_type]['qty'] += 1;
                                $ov_pays[$py->payment_type]['amount'] += $amount;
                            }
                            $ov_total += $amount;
                        }
                    }
                }
            }
            // echo "<pre>",print_r($payment_type),"</pre>";die();
            $ret['total'] = $total;
            $ret['types'] = $pays;
            $ret['gc_excess'] = $gc_excess;
            $ret['currency'] = $currency;
            $ret['cards'] = $cards;
            $ret['ov_total'] = $ov_total;
            $ret['ov_types'] = $ov_pays;
            $ret['payment_type'] = $payment_type;

            return $ret;
        }
        public function removed_menu_sales($ids=array(),$curr=false,$branch_code=''){
            $reasons = array();
            if(count($ids) > 0){
                $n_remove_sales = array();
                // if($curr){
                //     $this->cashier_model->db = $this->load->database('default', TRUE);
                //     $n_remove_sales = $this->cashier_model->get_reasons(null,array("reasons.trans_id"=>$ids));
                // }
                // $this->cashier_model->db = $this->load->database('default', TRUE);
                $remove_sales = $this->cashier_model->get_reasons(null,array("reasons.trans_id"=>$ids,'reasons.branch_code'=>$branch_code));
                $ids_used = array();
                foreach ($remove_sales as $res) {
                    if(!in_array($res->id, $ids_used)){
                        $ids_used[] = $res->id;
                    }
                    if(!isset($reasons[$res->id])){
                        $reasons[$res->id] = array('item'=>$res->ref_name,'reason'=>$res->reason,'trans_id'=>$res->trans_id,'manager'=>$res->man_username,'cashier'=>$res->cas_username);
                    }
                }
                // if(count($n_remove_sales) > 0){
                //     foreach ($n_remove_sales as $res) {
                //         if(!in_array($res->id, $ids_used)){
                //             if(!isset($reasons[$res->id])){
                //                 $reasons[$res->id] = array('item'=>$res->ref_name,'reason'=>$res->reason,'trans_id'=>$res->trans_id,'manager'=>$res->man_username,'cashier'=>$res->cas_username);
                //             }
                //         }
                //     }
                // }
            }
            return $reasons;
        }
        public function old_grand_total($date=""){
            $old_grand_total = 0;
            $ctr = 0;
            $this->site_model->db = $this->load->database('default', TRUE);
            $args['trans_sales.datetime <= '] = $date;
            $args['trans_sales.type_id'] = SALES_TRANS;
            $args['trans_sales.inactive'] = 0;
            $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
            $args['trans_sales.pos_id'] = TERMINAL_ID;
            $join['trans_sales'] = array('content'=>'trans_sales_menus.sales_id = trans_sales.sales_id','mode'=>'left');
            // $join['trans_sales_menu_modifiers'] = array('content'=>'trans_sales_menu_modifiers.sales_id = trans_sales.sales_id','mode'=>'left');
            $result = $this->site_model->get_tbl('trans_sales_menus',$args,array(),$join,true,'sum(trans_sales_menus.qty * trans_sales_menus.price) as total');
            if(count($result) > 0){

                $old_grand_total += $result[0]->total;
            }


            $gargs['trans_sales.datetime <= '] = $date;
            $gargs['trans_sales.type_id'] = SALES_TRANS;
            $gargs['trans_sales.inactive'] = 0;
            $gargs["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
            $gargs['trans_sales.pos_id'] = TERMINAL_ID;
            // $join['trans_sales'] = array('content'=>'trans_sales_menus.sales_id = trans_sales.sales_id','mode'=>'left');
            $join['trans_sales'] = array('content'=>'trans_sales_menu_modifiers.sales_id = trans_sales.sales_id','mode'=>'left');
            $result = $this->site_model->get_tbl('trans_sales_menu_modifiers',$gargs,array(),$join,true,'sum(trans_sales_menu_modifiers.qty * trans_sales_menu_modifiers.price) as total');
            // echo $this->site_model->db->last_query();
            if(count($result) > 0){
                $old_grand_total += $result[0]->total;
            }
            $cargs = array('read_type'=>Z_READ,'DATE(read_details.read_date) <= '=>date2Sql($date));
            $cargs['read_details.pos_id'] = TERMINAL_ID;
            $ctrresult = $this->site_model->get_tbl('read_details',$cargs,array(),null,true,'id','read_date',null);
            foreach ($ctrresult as $res) {
                $ctr++;
            }
            // echo var_dump($old_grand_total);
            return array('old_grand_total'=>$old_grand_total,'ctr'=>$ctr);
        }
        public function old_grand_net_total($date="",$add_void=false,$branch_id=""){
            $old_grand_total = 0;
            $ctr = 0;

            // $date = $date.' 04:00:00';

            $args['trans_sales.datetime < '] = $date;
            if(!$add_void)
                $args['trans_sales.type_id'] = SALES_TRANS;
            else{
                $args['trans_sales.type_id'] = array(SALES_TRANS,SALES_VOID_TRANS);
            }
            $args['trans_sales.inactive'] = 0;
            $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
            // $args['trans_sales.terminal_id'] = TERMINAL_ID;
            if(HIDECHIT){    
                $args['trans_sales.sales_id NOT IN (SELECT sales_id from trans_sales_payments where payment_type = "chit" and branch_code = "'.$branch_id.'")'] = array('use'=>'where','val'=>null,'third'=>false);
            }

            if(!empty($branch_id)){
                $args['trans_sales.branch_code'] = $branch_id;
            }
            // $args['trans_sales.terminal_id'] = TERMINAL_ID;

            $this->site_model->db = $this->load->database('default', TRUE);
            $result = $this->site_model->get_tbl('trans_sales use index(branch_code)',$args,array(),null,true,'sum(round(trans_sales.total_amount,2)) as total');
            // echo $this->site_model->db->last_query(); die();
            if(count($result) > 0){
                $old_grand_total += $result[0]->total;
            }

            $true_grand_total = $old_grand_total;
            $hargs['trans_sales.datetime <= '] = $date;
            $hargs['trans_sales.type_id'] = SALES_TRANS;
            $hargs['trans_sales.inactive'] = 0;
            $hargs["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
            // $hargs['trans_sales.pos_id'] = TERMINAL_ID;
            if(HIDECHIT){
                $hargs['trans_sales.sales_id NOT IN (SELECT sales_id from trans_sales_payments where payment_type = "chit" and branch_code = "'.$branch_id.'")'] = array('use'=>'where','val'=>null,'third'=>false);
            }
            if(!empty($branch_id)){

               $hargs['trans_sales.branch_code'] = $branch_id;

            }
            $joinh['trans_sales'] = array('content'=>'trans_sales_charges.sales_id = trans_sales.sales_id AND trans_sales_charges.branch_code = trans_sales.branch_code ','mode'=>'left');

            $this->site_model->db = $this->load->database('default', TRUE);
            $result = $this->site_model->get_tbl('trans_sales_charges use index(branch_code)',$hargs,array(),$joinh,true,'sum(trans_sales_charges.amount) as total_charges');
            // echo $this->site_model->db->last_query();
            if(count($result) > 0){
                $old_grand_total -= $result[0]->total_charges;
            }
            $largs['trans_sales.datetime <= '] = $date;
            $largs['trans_sales.type_id'] = SALES_TRANS;
            $largs['trans_sales.inactive'] = 0;
            $largs["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
            // $largs['trans_sales.pos_id'] = TERMINAL_ID;
            if(HIDECHIT){
                $largs['trans_sales.sales_id NOT IN (SELECT sales_id from trans_sales_payments where payment_type = "chit" and branch_code = "'.$branch_id.'")'] = array('use'=>'where','val'=>null,'third'=>false);
            }
             if(!empty($branch_id)){

               $largs['trans_sales.branch_code'] = $branch_id;

            }
            $joinl['trans_sales'] = array('content'=>'trans_sales_local_tax.sales_id = trans_sales.sales_id AND trans_sales_local_tax.branch_code = trans_sales.branch_code','mode'=>'left');
            $this->site_model->db  = $this->load->database('default', TRUE);
            $result = $this->site_model->get_tbl('trans_sales_local_tax',$largs,array(),$joinl,true,'sum(trans_sales_local_tax.amount) as total_lt');
            if(count($result) > 0){
                $old_grand_total -= $result[0]->total_lt;
            }


            if(MALL_ENABLED && MALL == 'megaworld'){
                //for gc excess
                $gcargs['trans_sales.datetime <= '] = $date;
                $gcargs['trans_sales.type_id'] = SALES_TRANS;
                $gcargs['trans_sales.inactive'] = 0;
                $gcargs["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
                // $gcargs['trans_sales.pos_id'] = TERMINAL_ID;
                $gcargs['trans_sales_payments.payment_type'] = 'gc';
                if(HIDECHIT){
                    $gcargs['trans_sales.sales_id NOT IN (SELECT sales_id from trans_sales_payments where payment_type = "chit" and branch_code = "'.$branch_id.'")'] = array('use'=>'where','val'=>null,'third'=>false);
                }
                $joingc['trans_sales'] = array('content'=>'trans_sales_payments.sales_id = trans_sales.sales_id','mode'=>'left');
                // $joinl['trans_sales_payments'] = array('content'=>'trans_sales_payments.sales_id = trans_sales.sales_id','mode'=>'left');
                $this->site_model->db  = $this->load->database('default', TRUE);
                $result = $this->site_model->get_tbl('trans_sales_payments use index(branch_code)',$gcargs,array(),$joingc,true,'sum(trans_sales_payments.amount) as total_amount, sum(trans_sales_payments.to_pay) as total_topay');
                $gc_excess = 0;
                if(count($result) > 0){
                    $gc_excess = $result[0]->total_amount - $result[0]->total_topay;
                }
                // $old_grand_total += $gc_excess;
                $true_grand_total += $gc_excess;
            }



            if(!empty($branch_id)){
                $cargs = array('read_type'=>Z_READ,'DATE(read_details.read_date) <= '=>date2Sql($date)
                    // ,'branch_code'=>$branch_id
                );
            }else{
                $cargs = array('read_type'=>Z_READ,'DATE(read_details.read_date) <= '=>date2Sql($date));
            }
            // if($this->site_model->db->database == "dinemain")
            //     $cargs['read_details.pos_id'] = TERMINAL_ID;

            $ctrresult = $this->site_model->get_tbl('read_details',$cargs,array(),null,true,'id','read_date',null);
            foreach ($ctrresult as $res) {
                $ctr++;
            }
            return array('old_grand_total'=>$old_grand_total,'true_grand_total'=>$true_grand_total,'ctr'=>$ctr);
        }
        public function shift_cashout($shift_id=""){
            $cashout_id = "";
            $shift = $this->site_model->get_tbl('shifts',array('shift_id'=>$shift_id));
            if(count($shift) > 0){
                $sh = $shift[0];
                if($sh->cashout_id != ""){
                    $cashout_id = $sh->cashout_id;
                }
            }
            ####
            return $cashout_id;
        }
        public function shift_entries($shift_id=""){
            $this->load->model('dine/clock_model');

            $shift = null;
            $total_drops = 0;
            $total_deps = $total_withs = array();
            $total_sales = 0;
            $overAllTotal = 0;

            $entries = $this->clock_model->get_shift_entries(null,array("shift_entries.shift_id"=>$shift_id));
            if(count($entries) > 0){
                foreach ($entries as $res) {
                    $total_drops += $res->amount;

                    if ($res->amount > 0)
                        $total_deps[] = $res;
                    else
                        $total_withs[] = $res;
                }
                $overAllTotal += $total_drops;
            }
            $args = array(
                "trans_sales.type_id"=>SALES_TRANS,
                "trans_sales.shift_id"=>$shift_id,
                "trans_sales.inactive"=>0
            );
            $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
            $trans = $this->cashier_model->get_trans_sales(null,$args);
            if(count($trans) > 0){
                foreach ($trans as $res) {
                    $total_sales += $res->total_paid;
                }
                $overAllTotal += $total_sales;
            }
            return array(
                'total_drops'=>$total_drops,
                'total_deps'=>$total_deps,
                'total_withs'=>$total_withs,
                'total_sales'=>$total_sales,
                'overAllTotal'=>$overAllTotal
            );
        }

        /////for hourlysales report with categories
        public function trans_sales_cat($args=array(),$curr=false){
            $n_results = array();
            // if($curr){
            //     $this->cashier_model->db = $this->load->database('default', TRUE);
            //     $n_results  = $this->cashier_model->get_trans_sales_wcategories(null,$args);
            //     // echo $this->cashier_model->db->last_query();
            // }
            $this->cashier_model->db = $this->load->database('default', TRUE);
            $results = $this->cashier_model->get_trans_sales_wcategories(null,$args,'desc',null);
            // die();
            $orders = array();
            if(count($n_results) > 0){
                foreach ($n_results as $nres) {
                    if(!isset($orders[$nres->sales_id])){
                        $orders[$nres->sales_id] = $nres;
                    }
                }
            }
            foreach ($results as $res) {
                if(!isset($orders[$res->sales_id])){
                    $orders[$res->sales_id] = $res;
                }
            }

            $sales = array();
            $all_ids = array();
            $sales['void'] = array();
            $sales['cancel'] = array();
            $sales['settled'] = array();

            $sales['void']['ids'] = array();
            $sales['cancel']['ids'] = array();
            $sales['settled']['ids'] = array();

            $sales['void']['orders'] = array();
            $sales['cancel']['orders'] = array();
            $sales['settled']['orders'] = array();

            $net = 0;
            $void_amount = 0;
            $types = array();
            $ordsnums = array();
            $all_orders = array();
            foreach ($orders as $sales_id => $sale) {
                if($sale->type_id == 10){
                    if($sale->trans_ref != "" && $sale->inactive == 0){
                        $sales['settled']['ids'][] = $sales_id;
                        $net += round($sale->total_amount,2);
                        $types[$sale->type][$sale->sales_id] = $sale;
                        $ordsnums[$sale->trans_ref] = $sale;
                        $sales['settled']['orders'][$sales_id] = $sale;
                    }
                    else if($sale->trans_ref == "" && $sale->inactive == 1){
                        $sales['cancel']['ids'][] = $sales_id;
                        $sales['cancel']['orders'][$sales_id] = $sale;
                    }
                }
                else{
                    $sales['void']['ids'][] = $sales_id;
                    $sales['void']['orders'][$sales_id] = $sale;
                    $void_amount += round($sale->total_amount,2);
                }
                $all_ids[] = $sales_id;
                $all_orders[$sales_id] = $sale;
            }
            ksort($ordsnums);
            // echo "<pre>",print_r($net),"</pre>";die();
            $first = array_shift(array_slice($ordsnums, 0, 1));
            $last = end($ordsnums);
            $ref_ctr = count($ordsnums);
            return array('all_ids'=>$all_ids,'all_orders'=>$all_orders,'sales'=>$sales,'net'=>$net,'void'=>$void_amount,'types'=>$types,'refs'=>$ordsnums,'first_ref'=>$first,'last_ref'=>$last,'ref_count'=>$ref_ctr);
        }

    public function trans_sales_conso($args=array(),$curr=false,$branch_code=""){
        $this->load->model('core/trans_model');
        $total_chit = $total_producttest = 0;
        $chit = array();
        $producttest = array();
        $n_results = array();
        // if($curr){
        //     $this->cashier_model->db = $this->load->database('default', TRUE);
        //     $n_results  = $this->cashier_model->get_trans_sales(null,$args);
        // }
        // echo "<pre>",print_r($args),"</pre>";die();
        
        // if(CONSOLIDATOR){
        //     $this->cashier_model->db = $this->load->database('main', TRUE);
        // }else{
            $this->cashier_model->db = $this->load->database('default', TRUE);
        // }
        $results = $this->cashier_model->get_trans_sales_rep(null,$args,'desc',null,$branch_code);
        $trans_count = count($results);
        // echo count($results);die();
        // echo "<pre>",print_r($results),"</pre>";die();
        // echo $this->cashier_model->db->last_query(); die();
        $orders = array();
        // if(HIDECHIT){
        //     // if(count($n_results) > 0){
        //     //     foreach ($n_results as $nres) {

        //     //         if($nres->type_id == 10){
        //     //             $this->site_model->db = $this->load->database('default', TRUE);
        //     //             $where = array('sales_id'=>$nres->sales_id);
        //     //             $rest = $this->site_model->get_details($where,'trans_sales_payments');
        //     //             if($rest){
        //     //                 if($rest[0]->payment_type != 'chit'){

        //     //                     if(!isset($orders[$nres->sales_id])){
        //     //                         $orders[$nres->sales_id] = $nres;
        //     //                     }
        //     //                 }else{
        //     //                     $chit[$nres->sales_id] = $nres;
        //     //                 }
        //     //             }else{
        //     //                 if(!isset($orders[$nres->sales_id])){
        //     //                     $orders[$nres->sales_id] = $nres;
        //     //                 }
        //     //             }

        //     //         }else{
        //     //             if(!isset($orders[$nres->sales_id])){
        //     //                 $orders[$nres->sales_id] = $nres;
        //     //             }
        //     //         }
        //     //     }
        //     // }
        //     foreach ($results as $res) {
                
        //         if($res->type_id == 10){
        //             $this->site_model->db = $this->load->database('main', TRUE);
        //             $where = array('sales_id'=>$res->sales_id);
        //             $rest = $this->site_model->get_details($where,'trans_sales_payments');
        //             if($rest){
        //                 if($rest[0]->payment_type != 'chit'){
        //                     if(!isset($orders[$res->sales_id])){
        //                         $orders[$res->sales_id] = $res;
        //                     }
        //                 }else{
        //                     // echo 'aa-';
        //                     $chit[$res->sales_id] = $res;
        //                 }
        //             }else{
        //                 if(!isset($orders[$res->sales_id])){
        //                     $orders[$res->sales_id] = $res;
        //                 }
        //             }
        //         }else{
        //             if(!isset($orders[$res->sales_id])){
        //                 $orders[$res->sales_id] = $res;
        //             }
        //         }
        //     }
        // }else{
        //     // if(count($n_results) > 0){
        //     //     foreach ($n_results as $nres) {
        //     //         if(!isset($orders[$nres->sales_id])){
        //     //             $orders[$nres->sales_id] = $nres;
        //     //         }
        //     //     }
        //     // }
        //     foreach ($results as $res) {
        //         if(!isset($orders[$res->sales_id])){
        //             $orders[$res->sales_id] = $res;
        //         }
        //     }
        // }
        // if(PRODUCT_TEST){
        //     foreach ($results as $res) {
                
        //         if($res->type_id == 10){
        //             $this->site_model->db = $this->load->database('main', TRUE);
        //             $where = array('sales_id'=>$res->sales_id);
        //             $rest = $this->site_model->get_details($where,'trans_sales_payments');
        //             if($rest){
        //                 if($rest[0]->payment_type != 'producttest'){
        //                     if(!isset($orders[$res->sales_id])){
        //                         $orders[$res->sales_id] = $res;
        //                     }
        //                 }else{
        //                     $producttest[$res->sales_id] = $res;
        //                 }
        //             }else{
        //                 if(!isset($orders[$res->sales_id])){
        //                     $orders[$res->sales_id] = $res;
        //                 }
        //             }
        //         }else{
        //             if(!isset($orders[$res->sales_id])){
        //                 $orders[$res->sales_id] = $res;
        //             }
        //         }
        //     }
        // }else{
        //     // if(count($n_results) > 0){
        //     //     foreach ($n_results as $nres) {
        //     //         if(!isset($orders[$nres->sales_id])){
        //     //             $orders[$nres->sales_id] = $nres;
        //     //         }
        //     //     }
        //     // }
        //     foreach ($results as $res) {
        //         if(!isset($orders[$res->sales_id])){
        //             $orders[$res->sales_id] = $res;
        //         }
        //     }
        // }


        foreach ($results as $res) {
            if(!isset($orders[$res->terminal_id."-".$res->sales_id])){
                $orders[$res->terminal_id."-".$res->sales_id] = $res;
            }
        }

        $sales = array();
        $all_ids = array();
        $sales['void'] = array();
        $sales['cancel'] = array();
        $sales['settled'] = array();

        $sales['void']['ids'] = array();
        $sales['cancel']['ids'] = array();
        $sales['settled']['ids'] = array();

        $sales['void']['orders'] = array();
        $sales['cancel']['orders'] = array();
        $sales['settled']['orders'] = array();
        $sales['settled']['orders2'] = array();
        $sales['settled_void']['orders'] = array();

        $net = 0;
        $void_amount = 0;
        $cancel_amount = 0;
        $all_guest = 0;
        $types = array();
        $ordsnums = array();
        $all_orders = array();
        $salesnums = array();
        $customer_count = 0;

        // echo "<pre>",print_r($orders),"</pre>";die();
        
        foreach ($orders as $tsales_id => $sale) {
            $ex = explode('-', $tsales_id);
            $sales_id = $ex[1];
            if($sale->type_id == 10){
                if($sale->trans_ref != "" && $sale->inactive == 0){
                    $sales['settled']['ids'][] = $sales_id;
                    $net += round($sale->total_amount,2);
                    $types[$sale->type][$sale->sales_id] = $sale;
                    $ordsnums[$sale->trans_ref] = $sale;
                    $salesnums[$sale->trans_ref] = $sale;
                    $sales['settled']['orders'][$sales_id] = $sale;
                    $sales['settled']['orders2'][$tsales_id] = $sale;
                    $customer_count += $sale->guest;
                }
                else if($sale->trans_ref == "" && $sale->inactive == 1){
                    if($sale->void_user_id){
                        $sales['cancel']['ids'][] = $sales_id;
                        $sales['cancel']['orders'][$sales_id] = $sale;
                        $cancel_amount += round($sale->total_amount,2);
                    }
                }else if(!empty($sale->trans_ref)  && $sale->inactive == 1){
                     $sales['settled_void']['ids'][] = $sales_id;
                     $sales['settled_void']['orders'][$sales_id] = $sale;
                     $ordsnums[$sale->trans_ref] = $sale;
                }
            }
            else{
                $sales['void']['ids'][] = $sales_id;
                $sales['void']['orders'][$sales_id] = $sale;
                $void_amount += round($sale->total_amount,2);
            }
            $all_ids[] = $sales_id;
            $all_orders[$sales_id] = $sale;
            $all_guest += $sale->guest;
        }
            // echo "<pre>",print_r($all_guest),"</pre>";die();
        ksort($ordsnums);
        $first = array_shift(array_slice($ordsnums, 0, 1));
        $last = end($ordsnums);
        $ref_ctr = count($ordsnums);

        if($orders){
            ksort($ordsnums);
            ksort($salesnums);
            // $first = array_shift(array_slice($ordsnums, 0, 1));
            // $last = end($ordsnums);
            $first_id = array_shift(array_slice($salesnums, 0, 1));
            $last_id = end($salesnums);
            // $ref_ctr = count($ordsnums);
            $id_ctr = count($salesnums);
        }else{
            $n_ref = $this->trans_model->get_next_ref(10);
            $l_id = $this->trans_model->get_last_sales_id();
            $first = $n_ref;
            $last = $n_ref;
            $first_id = $l_id[0]->sales_id + 1;
            $last_id = $l_id[0]->sales_id + 1;
            $ref_ctr = 0;
            $id_ctr = 0;
        }


        if(HIDECHIT){
            // if($curr){
            //     foreach($chit as $key => $vals){

            //         $this->site_model->db = $this->load->database('default', TRUE);
            //         $where = array('sales_id'=>$key);
            //         $results = $this->site_model->get_details($where,'trans_sales_payments');

            //         $total_chit += $results[0]->to_pay;
            //     }
            // }else{

            $chit_args = $args;
            $chit_args['trans_sales_payments.payment_type'] = 'chit';
            $chit_args['trans_sales.type_id'] = SALES_TRANS;
            $chit_args['trans_sales.inactive'] = 0;
            $chit_args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
            // echo "<pre>",print_r($chit_args),"</pre>";die();

            $p_res = $this->cashier_model->get_all_trans_sales_payments(null,$chit_args);
            // echo $this->cashier_model->db->last_query(); die();

            if($p_res){
                foreach($p_res as $vals){

                    // if(CONSOLIDATOR){
                        // $this->site_model->db = $this->load->database('main', TRUE);
                    // }else{
                        $this->site_model->db = $this->load->database('default', TRUE);
                    // }
                    $where = array('sales_id'=>$vals->sales_id);
                    $results = $this->site_model->get_details($where,'trans_sales_payments');

                    $total_chit += $results[0]->to_pay;
                }
            }
            // }
        }
        // echo $total_chit; die();
        // if(PRODUCT_TEST){
        //     $pt_args = $args;
        //     $pt_args['trans_sales_payments.payment_type'] = 'producttest';
        //     $pt_args['trans_sales.type_id'] = SALES_TRANS;
        //     $pt_args['trans_sales.inactive'] = 0;
        //     $pt_args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
        //     // echo "<pre>",print_r($pt_args),"</pre>";die();
        //     $pa_res = $this->cashier_model->get_all_trans_sales_payments(null,$pt_args);

        //     if($pa_res){
        //         foreach($pa_res as $vals){

        //             if(CONSOLIDATOR){
        //                 $this->site_model->db = $this->load->database('main', TRUE);
        //             }else{
        //                 $this->site_model->db = $this->load->database('default', TRUE);
        //             }
        //             $where = array('sales_id'=>$vals->sales_id);
        //             $results = $this->site_model->get_details($where,'trans_sales_payments');

        //             $total_producttest += $results[0]->to_pay;
        //         }
        //     }
        // }

        // echo $total_chit; die('sss');

        return array('all_ids'=>$all_ids,'all_orders'=>$all_orders,'sales'=>$sales,'net'=>$net,'void'=>$void_amount,'types'=>$types,'refs'=>$ordsnums,'first_ref'=>$first,'last_ref'=>$last,'ref_count'=>$ref_ctr,'total_chit'=>$total_chit,'cancel_amount'=>$cancel_amount,'product_test'=>$total_producttest,'all_guest'=>$all_guest,'trans_count'=>$trans_count,'customer_count'=>$customer_count,'first_id'=>$first_id,'last_id'=>$last_id,'id_ctr'=>$id_ctr);
    }
    public function back_xread(){
        $this->load->model('dine/reports_model');
        $this->load->helper('dine/reports_helper');
        $data = $this->syter->spawn('xread');
        $data['page_title'] = 'XREAD';
        $data['code'] = XreadBack();
        $data['add_css'] = array('css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css');
        $data['add_js'] = array('js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js');
        $data['load_js'] = 'dine/prints.php';
        $data['use_js'] = 'xreadBackJs';
        $this->load->view('page',$data);
    }
    public function check_xreads(){
        $this->load->helper('dine/print_helper');
        $this->load->database('default', TRUE);
        $date = $this->input->post('date');
        $user = $this->input->post('user');
        $terminal_id = $this->input->post('terminal_id');
        $json = $this->input->post('json');
        $dates = date2Sql($date);
        $branch_code = $this->input->post('branch_id');
        // echo $test;die();

        $args["trans_sales.user_id"] = $user;
        $args["trans_sales.type !="] = 'mgtfree';
        // if(CONSOLIDATOR){
        //     if($terminal_id != null){
        //         $args['trans_sales.terminal_id'] = $terminal_id;
        //     }
        // }else{
        //     $args['trans_sales.terminal_id'] = TERMINAL_ID;
        // }
             // $args["trans_sales.terminal_id"] = 1;
        $args["trans_sales.datetime  BETWEEN '".$dates." 00:00:01' AND '".$dates." 23:59:59'"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.branch_code  = '".$branch_code."' "] = array('use'=>'where','val'=>null,'third'=>false);
        // $args["trans_sales.shift_id"] = 188;
        // $args["trans_sales.datetime"] = $date;
        // echo $user.' '.$date;die();
        // echo "<pre>",print_r($args),"</pre>";die();
        if($json == 'true'){
            $asjson = true;
        }else{
            $asjson = false;
        }
            // $this->system_sales_rep_backoffice($asjson,$args);
        $details = $this->setup_model->get_branch_details(BRANCH_CODE);
            
        $open_time = $details[0]->store_open;
        $close_time = $details[0]->store_close;
        $to_date = date('Y-m-d', strtotime($dates . ' +1 day'));
        $print_date =$dates." ".$open_time. "  - ".$to_date." ".$close_time;
        // $print_date =$dates." ".$open_time. "  - ".$dates." ".$close_time;
        
            // $this->system_sales_rep_backoffice($asjson,$args,$asjson,$print_date);
            $this->xread_rep_backoffice_aurora(false,$args,$asjson,$print_date);

    }
    public function system_sales_rep_backoffice($asJson=false,$args=array(),$return_print_str=false,$print_date){
         ////hapchan
            ini_set('memory_limit', '-1');
            set_time_limit(3600);
            
            $print_str = $this->print_header();
            $user = $this->session->userdata('user');
            $time = $this->site_model->get_db_now();
            $post = $this->set_post();
            $curr = $this->search_current();
            $trans = $this->trans_sales($args,$curr);
            // var_dump($trans['net']); die();
            $sales = $trans['sales'];
            $trans_menus = $this->menu_sales($sales['settled']['ids'],$curr,BRANCH_CODE);
            $trans_charges = $this->charges_sales($sales['settled']['ids'],$curr,BRANCH_CODE);
            $trans_discounts = $this->discounts_sales($sales['settled']['ids'],$curr);
            $tax_disc = $trans_discounts['tax_disc_total'];
            $no_tax_disc = $trans_discounts['no_tax_disc_total'];
            $trans_local_tax = $this->local_tax_sales($sales['settled']['ids'],$curr,BRANCH_CODE);
            $trans_tax = $this->tax_sales($sales['settled']['ids'],$curr,BRANCH_CODE);
            $trans_no_tax = $this->no_tax_sales($sales['settled']['ids'],$curr,BRANCH_CODE);
            $trans_zero_rated = $this->zero_rated_sales($sales['settled']['ids'],$curr,BRANCH_CODE);
            $payments = $this->payment_sales($sales['settled']['ids'],$curr,BRANCH_CODE);

            $gross = $trans_menus['gross'];
            
            $net = $trans['net'];
            $void = $trans['void'];
            $cancelled = $trans['cancel_amount'];
            $charges = $trans_charges['total'];
            $discounts = $trans_discounts['total'];
            $local_tax = $trans_local_tax['total'];
            // echo $gross.' - '.$charges.' - '.$discounts.' - '.$net; die();
            $less_vat = (($gross+$charges+$local_tax) - $discounts) - $net;
            // $less_vat = $trans_discounts['vat_exempt_total'];

            // echo $gross.'+'.$charges.'+'.$local_tax.' - '.$discounts.' - '.$net;
            // die();

            if($less_vat < 0)
                $less_vat = 0;
            // var_dump($less_vat);

            //para mag tugmam yun payments and netsale
            // $net_sales2 = $gross + $charges - $discounts - $less_vat;
            // $diffs = $net_sales2 - $payments['total'];
            // if($diffs < 1){
            //     $less_vat = $less_vat + $diffs;
            // }
            

            $tax = $trans_tax['total'];
            $no_tax = $trans_no_tax['total'];
            $zero_rated = $trans_zero_rated['total'];
            $no_tax -= $zero_rated;

            $title_name = "XREAD";
            // if($post['title'] != "")
            //     $title_name = $post['title'];

            $print_str .= align_center($title_name,PAPER_WIDTH," ")."\r\n";
            $print_str .= align_center("TERMINAL ".$post['terminal'],PAPER_WIDTH," ")."\r\n";
            $print_str .= append_chars('Printed On','right',11," ").append_chars(": ".date2SqlDateTime($time),'right',19," ")."\r\n";
            $print_str .= append_chars('Printed BY','right',11," ").append_chars(": ".$user['full_name'],'right',19," ")."\r\n";
            $print_str .= PAPER_LINE."\r\n";
            $print_str .= align_center($print_date,PAPER_WIDTH," ")."\r\n";
            if($post['employee'] != "All")
                $print_str .= align_center($post['employee'],PAPER_WIDTH," ")."\r\n";
            $print_str .= PAPER_LINE."\r\n";

            $loc_txt = numInt(($local_tax));
            $net_no_adds = $net-($charges+$local_tax);
            // $nontaxable = $no_tax;
            //binago 9/25/2018 for zreading adjustment of vat exempt equal to the receipt vat exempt
            $nontaxable = $no_tax - $no_tax_disc;
            // echo $gross.' - '.$less_vat.' - '.$nontaxable.' - '.$zero_rated; die();
            // $taxable = ($gross - $less_vat - $nontaxable - $zero_rated) / 1.12;
            // 1.12; binago din para sa adjustment of vat exempt equal to the receipt vat exempt
            // $taxable =   ($gross - $discounts - $less_vat - $nontaxable) / 1.12;
            $taxable =   ($gross - $less_vat - $nontaxable - $zero_rated - $discounts) / 1.12; //change computation conflict for zero rated 10 17 2018
            $total_net = ($taxable) + ($nontaxable+$zero_rated) + $tax + $local_tax;
            $add_gt = $taxable+$nontaxable+$zero_rated;
            $nsss = $taxable +  $nontaxable +  $zero_rated;

            #GENERAL
                $print_str .= append_chars(substrwords('TOTAL SALES',18,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(num($gross + $charges,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords(ucwords(strtoupper('SC/PWD VAT EXEMPT')),18,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars('-'.num($less_vat,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars('',"right",12," ").align_center('',PAPER_TOTAL_COL_2," ")
                                  .append_chars('----------',"left",PAPER_TOTAL_COL_2," ")."\r\n";

                $print_str .= append_chars(substrwords('GROSS SALES',18,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(num($gross + $charges - $less_vat,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";

                // $types = $trans_charges['types'];
                // $qty = 0;
                // foreach ($types as $code => $val) {
                //     $amount = $val['amount'];
                //     $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",PAPER_TOTAL_COL_1," ")
                //                          .append_chars('-'.num($amount,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                //     $qty += $val['qty'];
                // }
                $types = $trans_discounts['types'];
                $qty = 0;
                foreach ($types as $code => $val) {
                    if($code != 'DIPLOMAT'){
                        $amount = $val['amount'];
                        // if(MALL == 'megamall' && $code == PWDDISC){
                        //     $amount = $val['amount'] / 1.12;
                        // }
                        $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",PAPER_TOTAL_COL_1," ")
                                             .append_chars('-'.Num($amount,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                        $qty += $val['qty'];
                    }
                }
                $print_str .= append_chars('',"right",12," ").align_center('',PAPER_TOTAL_COL_2," ")
                                  .append_chars('----------',"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $net_sales = $gross + $charges - $discounts - $less_vat;
                $print_str .= append_chars(substrwords(ucwords(strtoupper('NET SALES')),18,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(num($net_sales,2),"left",PAPER_TOTAL_COL_2," ")."\r\n\r\n";
            #PAYMENTS
                $payments_types = $payments['types'];
                $payments_total = $payments['total'];
                $pay_qty = 0;
            #SUMMARY
                $final_gross = $gross;
                $vat_ = $taxable * .12;
                $print_str .= append_chars(substrwords('VAT SALES',23,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(numInt($taxable),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords('VAT',23,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(numInt($vat_),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords('VAT EXEMPT SALES',23,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(numInt($nontaxable),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                                         // .append_chars(numInt($nontaxable-$zero_rated),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords('ZERO RATED',23,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(numInt($zero_rated),"left",PAPER_TOTAL_COL_2," ")."\r\n\r\n";
                $print_str .= append_chars(substrwords('Payment Breakdown:',18,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
                              .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
                foreach ($payments_types as $code => $val) {
                    $print_str .= append_chars(substrwords(ucwords(strtoupper($code)),18,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                                  .append_chars(numInt($val['amount']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $pay_qty += $val['qty'];
                }
                $print_str .= append_chars('',"right",18," ").align_center('',PAPER_RD_COL_2," ")
                                  .append_chars('----------',"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= append_chars(substrwords('TOTAL PAYMENTS',18,""),"right",PAPER_RD_COL_1," ").align_center($pay_qty,PAPER_RD_COL_2," ")
                              .append_chars(numInt($payments_total),"left",PAPER_RD_COL_3_3," ")."\r\n\r\n";
                $print_str .= PAPER_LINE_SINGLE."\r\n";
                $gross_less_disc = $final_gross - $discounts - $less_vat;
                // $print_str .= append_chars(substrwords('NET SALES',23,""),"right",PAPER_TOTAL_COL_1," ")
                //                          // .append_chars(numInt(($taxable + $nontaxable + $zero_rated + $vat_)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                //                          .append_chars(numInt(($taxable + $nontaxable + $vat_)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                // $print_str .= PAPER_LINE."\r\n";

                if(count($payments['currency']) > 0){
                    $currency = $payments['currency'];
                    $print_str .= append_chars(substrwords('Currency Breakdown:',20,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
                                  .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
                    foreach ($currency as $code => $val) {
                        $print_str .= append_chars(substrwords(ucwords(strtoupper($code)),18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ").append_chars(numInt($val['amount']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                        // $pay_qty += $val['qty'];
                    }
                }

                // $print_str .= PAPER_LINE_SINGLE."\r\n";
                $print_str .= "\r\n\r\n";



                $print_str .= append_chars(substrwords('VOID SALES',18,""),"right",PAPER_TOTAL_COL_1," ")
                             .append_chars(num(($void),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords('CANCELLED TRANS',18,""),"right",PAPER_TOTAL_COL_1," ")
                             .append_chars(num(($cancelled),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";

                $cancelled_order = $this->cancelled_orders();
                $co = $cancelled_order['cancelled_order'];
                $print_str .= append_chars(substrwords('CANCELLED ORDERS',18,""),"right",PAPER_TOTAL_COL_1," ")
                             .append_chars(num(($co),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords('Local Tax',18,""),"right",PAPER_TOTAL_COL_1," ")
                             .append_chars($loc_txt,"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= "\r\n";
            #TRANS COUNT
                $types = $trans['types'];
                $types_total = array();
                $guestCount = 0;
                foreach ($types as $type => $tp) {
                    foreach ($tp as $id => $opt){
                        if(isset($types_total[$type])){
                            $types_total[$type] += round($opt->total_amount,2);

                        }
                        else{
                            $types_total[$type] = round($opt->total_amount,2);
                        }

                        // if($opt->type == 'dinein'){
                        //     $guestCount += $opt->guest;
                        // }
                        if($opt->guest == 0)
                            $guestCount += 1;
                        else
                            $guestCount += $opt->guest;
                    }
                }
                $print_str .= append_chars(substrwords('Trans Count:',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars('',"left",PAPER_RD_COL_3_3," ")."\r\n";
                $tc_total  = 0;
                $tc_qty = 0;
                foreach ($types_total as $typ => $tamnt) {
                    $print_str .= append_chars(substrwords($typ,18,""),"right",PAPER_RD_COL_1," ").align_center(count($types[$typ]),PAPER_RD_COL_2," ")
                                 .append_chars(numInt($tamnt),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $tc_total += $tamnt;
                    $tc_qty += count($types[$typ]);
                }
                $print_str .= "-----------------"."\r\n";
                $print_str .= append_chars(substrwords('TC Total',18,""),"right",PAPER_TOTAL_COL_1," ")
                             .append_chars(numInt($tc_total),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords('GUEST Total',18,""),"right",PAPER_TOTAL_COL_1," ")
                             .append_chars($guestCount,"left",PAPER_TOTAL_COL_2," ")."\r\n";
                // if($tc_total == 0 || $tc_qty == 0)
                //     $avg = 0;
                // else
                //     $avg = $tc_total/$tc_qty;
                if($net_sales){
                    if($guestCount == 0){
                        $avg = 0;
                    }else{
                        $avg = $net_sales/$guestCount;
                    }
                }else{
                    $avg = 0;
                }


                $print_str .= append_chars(substrwords('AVG Check',18,""),"right",PAPER_TOTAL_COL_1," ")
                             .append_chars(numInt($avg),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= "\r\n";
            #CHARGES
                $types = $trans_charges['types'];
                $qty = 0;
                $print_str .= append_chars(substrwords('Charges:',18,""),"right",18," ").align_center(null,5," ")
                              .append_chars(null,"left",13," ")."\r\n";
                foreach ($types as $code => $val) {
                    $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                                  .append_chars(numInt($val['amount']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $qty += $val['qty'];
                }
                $print_str .= "-----------------"."\r\n";
                $print_str .= append_chars(substrwords('Total Charges',18,""),"right",PAPER_RD_COL_1," ").align_center($qty,PAPER_RD_COL_2," ")
                              .append_chars(numInt($charges),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= "\r\n";
            #Discounts
                $types = $trans_discounts['types'];
                $qty = 0;
                $print_str .= append_chars(substrwords('Discounts:',18,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
                              .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
                foreach ($types as $code => $val) {
                    if($code != 'DIPLOMAT'){
                        $amount = $val['amount'];
                        // if(MALL == 'megamall' && $code == PWDDISC){
                        //     $amount = $val['amount'] / 1.12;
                        // }
                        $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                                      .append_chars(numInt($amount),"left",PAPER_RD_COL_3_3," ")."\r\n";
                        $qty += $val['qty'];
                    }
                }
                $print_str .= "-----------------"."\r\n";
                $print_str .= append_chars(substrwords('Total Discounts',18,""),"right",PAPER_RD_COL_1," ").align_center($qty,PAPER_RD_COL_2," ")
                              .append_chars(numInt($discounts),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= append_chars(substrwords('VAT EXEMPT',18,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(numInt($less_vat),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= "\r\n";
            #PAYMENTS
                $payments_types = $payments['types'];
                $payments_total = $payments['total'];
                $pay_qty = 0;
                $print_str .= append_chars(substrwords('Payment Breakdown:',18,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
                              .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
                foreach ($payments_types as $code => $val) {
                    $print_str .= append_chars(substrwords(ucwords(strtolower($code)),18,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                                  .append_chars(numInt($val['amount']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $pay_qty += $val['qty'];
                }
                $print_str .= "-----------------"."\r\n";
                $print_str .= append_chars(substrwords('Total Payments',18,""),"right",PAPER_RD_COL_1," ").align_center($pay_qty,PAPER_RD_COL_2," ")
                              .append_chars(numInt($payments_total),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= "\r\n";

                //card breakdown
                if($payments['cards']){
                    $cards = $payments['cards'];
                    $card_total = 0;
                    $count_total = 0;
                    $print_str .= append_chars(substrwords('Card Breakdown:',18,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
                              .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
                    foreach($cards as $key => $val){
                        $print_str .= append_chars(substrwords($key,18,""),"right",PAPER_RD_COL_1," ").align_center($val['count'],PAPER_RD_COL_2," ")
                                  .append_chars(numInt($val['amount']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                        $card_total += $val['amount'];
                        $count_total += $val['count'];
                    }
                    $print_str .= "-----------------"."\r\n";
                    $print_str .= append_chars(substrwords('Total',18,""),"right",PAPER_RD_COL_1," ").align_center($count_total,PAPER_RD_COL_2," ")
                              .append_chars(numInt($card_total),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    
                    $print_str .= "\r\n";
                }

                //get all gc with excess
                if($payments['gc_excess']){
                    $print_str .= append_chars(substrwords('GC EXCESS',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                                  .append_chars(numInt($payments['gc_excess']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $print_str .= "\r\n";
                }

                //show all sign chit
                // $trans['sales']
                if($trans['total_chit']){
                    $print_str .= append_chars(substrwords('TOTAL CHIT',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                                  .append_chars(numInt($trans['total_chit']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $print_str .= "\r\n";
                }
            #CATEGORIES
                $cats = $trans_menus['cats'];
                $print_str .= append_chars('Menu Categories:',"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars('',"left",PAPER_RD_COL_3," ")."\r\n";
                $qty = 0;
                $total = 0;
                foreach ($cats as $id => $val) {
                    if($val['qty'] > 0){
                        $print_str .= append_chars(substrwords($val['name'],18,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                                   .append_chars(numInt($val['amount']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                        $qty += $val['qty'];
                        $total += $val['amount'];
                    }
                 }
                $print_str .= "-----------------"."\r\n";
                $cat_total_qty = $qty;
                $print_str .= append_chars("SubTotal","right",PAPER_RD_COL_1," ").align_center($qty,PAPER_RD_COL_2," ")
                              .append_chars(numInt($total),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= append_chars("Modifiers Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                              .append_chars(numInt($trans_menus['mods_total']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                 $print_str .= append_chars("SubModifier Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                              .append_chars(numInt($trans_menus['submods_total']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                if($trans_menus['item_total'] > 0){
                 $print_str .= append_chars("Retail Items Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                               .append_chars(numInt($trans_menus['item_total']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                }

                $print_str .= append_chars("Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                              .append_chars(numInt($total+$trans_menus['mods_total']+$trans_menus['item_total']+$trans_menus['submods_total']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= "\r\n";
            #SUBCATEGORIES
                $subcats = $trans_menus['sub_cats'];
                // $print_str .= append_chars('Menu Subcategories:',"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                $print_str .= append_chars('Menu Types:',"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars('',"left",PAPER_RD_COL_3," ")."\r\n";
                $qty = 0;
                $total = 0;
                foreach ($subcats as $id => $val) {
                    $print_str .= append_chars($val['name'],"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                               .append_chars(numInt($val['amount']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $qty += $val['qty'];
                    $total += $val['amount'];
                 }
                $print_str .= "-----------------"."\r\n";
                $print_str .= append_chars("Total","right",PAPER_RD_COL_1," ").align_center($qty,PAPER_RD_COL_2," ")
                              .append_chars(numInt($total),"left",PAPER_RD_COL_3_3," ")."\r\n";
                // $print_str .= append_chars("Modifiers Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                //               .append_chars(numInt($trans_menus['mods_total']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                // $print_str .= append_chars("SubModifier Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                //               .append_chars(numInt($trans_menus['submods_total']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                // $print_str .= append_chars("Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                //               .append_chars(numInt($total+$trans_menus['mods_total']+$trans_menus['submods_total']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= "\r\n";
            #FREE MENUS
                $free = $trans_menus['free_menus'];
                $print_str .= append_chars('Free Menus:',"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars('',"left",PAPER_RD_COL_3," ")."\r\n";
                $fm = array();
                foreach ($free as $ms) {
                    if(!isset($fm[$ms->menu_id])){
                        $mn = array();
                        $mn['name'] = $ms->menu_name;
                        $mn['cat_id'] = $ms->cat_id;
                        $mn['qty'] = $ms->qty;
                        $mn['amount'] = $ms->sell_price * $ms->qty;
                        $mn['sell_price'] = $ms->sell_price;
                        $mn['code'] = $ms->menu_code;
                        // $mn['free_user_id'] = $ms->free_user_id;
                        $fm[$ms->menu_id] = $mn;
                    }
                    else{
                        $mn = $fm[$ms->menu_id];
                        $mn['qty'] += $ms->qty;
                        $mn['amount'] += $ms->sell_price * $ms->qty;
                        $fm[$ms->menu_id] = $mn;
                    }
                }
                $qty = 0;
                $total = 0;
                foreach ($fm as $menu_id => $val) {
                    $print_str .= append_chars($val['name'],"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                               .append_chars(($val['qty']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $qty += $val['qty'];
                    $total += $val['amount'];
                }
                $print_str .= "-----------------"."\r\n";
                $print_str .= append_chars("Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                              .append_chars(($qty),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= "\r\n";
                $print_str .= "\r\n";    
            #FOOTER
                $print_str .= append_chars(substrwords('Invoice Start: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars(iSetObj($trans['first_ref'],'trans_ref'),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= append_chars(substrwords('Invoice End: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars(iSetObj($trans['last_ref'],'trans_ref'),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= append_chars(substrwords('Invoice Ctr: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars($trans['ref_count'],"left",PAPER_RD_COL_3_3," ")."\r\n";
                if($title_name == "ZREAD"){
                    $gt = $this->old_grand_net_total($post['from']);
                    $print_str .= "\r\n";
                    $print_str .= append_chars(substrwords('OLD GT: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                                 .append_chars(numInt( $gt['old_grand_total']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $print_str .= append_chars(substrwords('NEW GT: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                                 .append_chars( numInt($gt['old_grand_total']+$net_no_adds)  ,"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $print_str .= append_chars(substrwords('Z READ CTR: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                                 .append_chars( $gt['ctr'] ,"left",PAPER_RD_COL_3_3," ")."\r\n";
                }
                $print_str .= PAPER_LINE."\r\n";
            #MALLS
                if(MALL_ENABLED){
                    ####################################
                    # AYALA
                        if(MALL == 'ayala'){
                            
                            $branch_details = $this->setup_model->get_branch_details();
                            $branch = array();
                            foreach ($branch_details as $bv) {
                                $branch = array(
                                    'id' => $bv->branch_id,
                                    'res_id' => $bv->res_id,
                                    'branch_code' => $bv->branch_code,
                                    'name' => $bv->branch_name,
                                    'branch_desc' => $bv->branch_desc,
                                    'contact_no' => $bv->contact_no,
                                    'delivery_no' => $bv->delivery_no,
                                    'address' => $bv->address,
                                    'base_location' => $bv->base_location,
                                    'currency' => $bv->currency,
                                    'inactive' => $bv->inactive,
                                    'tin' => $bv->tin,
                                    'machine_no' => $bv->machine_no,
                                    'bir' => $bv->bir,
                                    'permit_no' => $bv->permit_no,
                                    'serial' => $bv->serial,
                                    'accrdn' => $bv->accrdn,
                                    'email' => $bv->email,
                                    'website' => $bv->website,
                                    'store_open' => $bv->store_open,
                                    'store_close' => $bv->store_close,
                                );
                            }


                            $print_str .= align_center("FOR AYALA",PAPER_WIDTH," ")."\r\n";
                            $print_str .= align_center($branch['name'],PAPER_WIDTH," ")."\r\n";
                            $print_str .= align_center($branch['address'],PAPER_WIDTH," ")."\r\n\r\n";
                            $print_str .= align_center("CONSOLIDATED REPORT Z-READ",PAPER_WIDTH," ")."\r\n\r\n";


                            $total_daily_sales = $total_vatA = $total_rawgrossA = $total_discount = $total_refund = $total_void = $total_charge = $total_non_tax = $total_trans_count = $total_guest = 0;


                            $paytype = array();
                            foreach ($payments_types as $code => $val) {
                                if($code != 'credit'){
                                    if(!isset($paytype[$code])){
                                        $paytype[$code] = array('amount'=>$val['amount']);
                                    }else{
                                        $row = $paytype[$code];
                                        $row['amount'] += $val['amount'];
                                        $paytype[$code] = $row;
                                    }
                                }
                                // $print_str .= append_chars(substrwords(ucwords(strtoupper($code)),12,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                                //               .append_chars(num($val['amount'],2),"left",PAPER_RD_COL_3_3," ")."\r\n";
                                // $pay_qty += $val['qty'];
                            }
                            $paycards = array();
                            if($payments['cards']){
                                $cards = $payments['cards'];
                                foreach($cards as $key => $val){
                                    if(!isset($paycards[$key])){
                                        $paycards[$key] = array('amount'=>$val['amount']);
                                    }else{
                                        $row = $paycards[$key];
                                        $row['amount'] += $val['amount'];
                                        $paycards[$key] = $row;
                                    }
                                }
                            }


                            // for server
                            $rawgrossA = numInt($gross + $charges + $void + $local_tax);
                            $vatA = numInt(($rawgrossA  - $discounts - $void  -  $charges - $nontaxable - $local_tax - numInt($less_vat)) * (1/9.333333));
                            $dlySaleA = numInt($rawgrossA - $discounts - $void - $charges - $vatA - $less_vat + $local_tax);
                            // $t_discounts = $discounts+$less_vat;
                            $rawgrossA =  $rawgrossA - $less_vat;
                            $t_discounts = $discounts;



                            $trans_count = 0;
                            $begor = 0;
                            $endor = 0;
                            $first_inv = array();
                            $last_inv = array();
                            $first_ref = 0;
                            $last_ref = 0;
                            $first_val = 0;
                            $last_val = 0;
                            $invs = array();
                            foreach ($trans['all_orders'] as $ord) {
                                if($ord->type_id == SALES_TRANS && $ord->trans_ref != ""){
                                    $ref = $ord->trans_ref;
                                    if (preg_match('/^(\D*?)(\d+)(.*)/', $ref, $result) == 1){
                                        // if($ord->inactive != 1){
                                            list($all, $prefix, $number, $postfix) = $result;
                                            $ref_val = intval($number);
                                            $invs[$ref_val] = array("ref"=>$ord->trans_ref,"val"=>$ref_val);
                                        // }
                                    }
                                }
                            }
                            ksort($invs);
                            // echo "<pre>",print_r($invs),"</pre>";die();
                            $first_inv = reset($invs);
                            $last_inv = end($invs);
                            if(count($first_inv) > 0){
                                $first_ref = $first_inv['ref'];
                                $first_val = $first_inv['val'];
                            }
                            if(count($last_inv) > 0){
                                $last_ref = $last_inv['ref'];
                                $last_val = $last_inv['val'];
                            }
                            if(count($invs) > 0){
                                $trans_count = ($last_val - $first_val) + 1; 
                            }

                            // echo $trans_count; die();
                            //add yun mga value ng server sa totals
                            $total_daily_sales += $dlySaleA;
                            $total_vatA += $vatA;
                            $total_rawgrossA += $rawgrossA;
                            $total_discount += $t_discounts;
                            $total_void += $void;
                            $total_charge += $charges;
                            $total_non_tax += $nontaxable;
                            // $total_trans_count += $tc_qty;
                            $total_trans_count += $trans_count;
                            $total_guest += $guestCount;
                             // echo $total_trans_count;

                            $terminals = $this->setup_model->get_terminals();


                            $print_str .= append_chars(substrwords('Daily Sales',25,""),"right",22," ").align_center('',2," ")
                                         .append_chars(num($total_daily_sales),"left",10," ")."\r\n";
                            $print_str .= append_chars(substrwords('Total Discount',25,""),"right",22," ").align_center('',2," ")
                                         .append_chars(num($total_discount),"left",10," ")."\r\n";
                            $print_str .= append_chars(substrwords('Total Refund',25,""),"right",22," ").align_center('',2," ")
                                         .append_chars(num(0),"left",10," ")."\r\n";
                            $print_str .= append_chars(substrwords('Total Void',25,""),"right",22," ").align_center('',2," ")
                                         .append_chars(num($total_void),"left",10," ")."\r\n";
                            $print_str .= append_chars(substrwords('Total Vat',25,""),"right",22," ").align_center('',2," ")
                                         .append_chars(num($total_vatA),"left",10," ")."\r\n";
                            $print_str .= append_chars(substrwords('Total Service Charge',25,""),"right",22," ").align_center('',2," ").append_chars(num($total_charge),"left",10," ")."\r\n";  


                            // $print_str .= append_chars(substrwords('Vatable Sales',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                            //              .append_chars(num($dlySaleA-$nontaxable),"left",PAPER_RD_COL_3_3," ")."\r\n";             
                            $print_str .= append_chars(substrwords('Total Non Taxable',22,""),"right",22," ").align_center('',2," ")
                                         .append_chars(num($total_non_tax),"left",10," ")."\r\n";
                            $print_str .= append_chars(substrwords('Row Gross',22,""),"right",22," ").align_center('',2," ")
                                         .append_chars(num($total_rawgrossA),"left",10," ")."\r\n";             
                            $print_str .= append_chars(substrwords('Transaction Count',22,""),"right",22," ").align_center('',2," ")
                                         .append_chars($total_trans_count,"left",10," ")."\r\n";
                            $print_str .= append_chars(substrwords('Customer Count',22,""),"right",22," ").align_center('',2," ")
                                         .append_chars($total_guest,"left",10," ")."\r\n";
                            foreach ($paytype as $k => $v) {
                                $print_str .= append_chars(strtoupper($k),"right",22," ").align_center('',2," ")
                                     .append_chars(num($v['amount']),"left",10," ")."\r\n";       
                            }
                            foreach ($paycards as $k => $v) {
                                $print_str .= append_chars(strtoupper($k),"right",22," ").align_center('',2," ")
                                     .append_chars(num($v['amount']),"left",10," ")."\r\n";       
                            }
                            $terminals = $this->setup_model->get_terminals();
                            // echo "<pre>",print_r($terminals),"</pre>";die();
                            foreach ($terminals as $k => $val) {
                                $print_str .= append_chars('BIR PERMIT '.$val->terminal_id,"right",15," ").align_center('',2," ")
                                     .append_chars($val->permit,"left",17," ")."\r\n";
                                $print_str .= append_chars('SERIAL NO. '.$val->terminal_id,"right",15," ").align_center('',2," ")
                                     .append_chars($val->serial,"left",17," ")."\r\n";
                            }           
                            // $print_str .= append_chars(substrwords('Less SC Disc',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                            //              .append_chars(num($discounts),"left",PAPER_RD_COL_3_3," ")."\r\n";             
                            // $print_str .= append_chars(substrwords('Vat Exempt',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                            //              .append_chars(num($less_vat),"left",PAPER_RD_COL_3_3," ")."\r\n";             
                            // $print_str .= append_chars(substrwords('Zero Rated',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                            //              .append_chars(num($zero_rated),"left",PAPER_RD_COL_3_3," ")."\r\n";             
                            // $print_str .= PAPER_LINE_SINGLE."\r\n";
                            // $print_str .= append_chars(substrwords('Net Sales',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                            //              .append_chars(num($dlySaleA+$vatA),"left",PAPER_RD_COL_3_3," ")."\r\n";             
                            // $print_str .= append_chars(substrwords('Total Qty Sold',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                            //              .append_chars(num($cat_total_qty),"left",PAPER_RD_COL_3_3," ")."\r\n";             
                            $print_str .= PAPER_LINE."\r\n";
                            $print_str .= align_center(sql2Date($post['from']),PAPER_WIDTH," ")."\r\n";
                            $print_str .= align_center("END OF REPORT",PAPER_WIDTH," ")."\r\n";


                        }
                    ####################################
                }    
            if ($return_print_str) {
                return $print_str;
            }
            // if($title_name == "ZREAD"){
            //     if($asJson == false){
            //         $this->manager_model->add_event_logs($user['id'],"ZREAD","View");    
            //     }else{
            //         $this->manager_model->add_event_logs($user['id'],"ZREAD","Print");                    
            //     } 
            // }elseif($title_name == "XREAD"){
            //     if($asJson == false){
            //         $this->manager_model->add_event_logs($user['id'],"XREAD","View");    
            //     }else{
            //         $this->manager_model->add_event_logs($user['id'],"XREAD","Print");                    
            //     } 
            // }else{
            //     if($asJson == false){
            //         $this->manager_model->add_event_logs($user['id'],"System Sales","View");    
            //     }else{
            //         $this->manager_model->add_event_logs($user['id'],"System Sales","Print");                    
            //     }
            // } 
            $this->session->set_userdata('pdf_data','<pre>'.$print_str.'</pre>') ; 
            // if(PRINT_VERSION && PRINT_VERSION == 'V2'){
            //     $this->do_print_v2($print_str,$asJson);  
            // }else if(PRINT_VERSION && PRINT_VERSION == 'V3' && $asJson){
            //     echo $this->html_print($print_str);
            // }else{
                $this->do_print($print_str,$asJson);
            // }
            // $this->do_print($print_str,$asJson);
    }
    public function excel_xread($sales_id=null,$noPrint=true){
        // echo "<pre>",print_r($sales_id),"</pre>";die();
        $this->load->library('Excel');
        $sheet = $this->excel->getActiveSheet();
        $filename = 'XREAD Report';
        $styleHeaderCell = array(
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                    ),
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => '3C8DBC')
                    ),
                    'alignment' => array(
                                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    ),
                    'font' => array(
                        'bold' => true,
                        'size' => 14,
                        'color' => array('rgb' => 'FFFFFF'),
                    )
                );
                $styleNum = array(
                    'alignment' => array(
                                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                    ),
                );
                $styleTxt = array(
                    'alignment' => array(
                                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    ),
                );
                $styleTitle = array(
                    'alignment' => array(
                                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    ),
                    'font' => array(
                        'bold' => true,
                        'size' => 16,
                    )
                );
        // ob_start();
        // ob_start();
        $date = $_GET['date'];
        $user = $_GET['user'];
        $branch_code = $_GET['branch_id'];
        // $json = $_GET['json'];
        $dates = date2Sql($date);
        $args["trans_sales.user_id"] = $_GET['user'];
        $args["trans_sales.type !="] = 'mgtfree';
        // if(CONSOLIDATOR){
        //     if($terminal_id != null){
        //         $args['trans_sales.terminal_id'] = $terminal_id;
        //     }
        // }else{
        //     $args['trans_sales.terminal_id'] = TERMINAL_ID;
        // }
             // $args["trans_sales.terminal_id"] = 1;
        $args["trans_sales.datetime  BETWEEN '".$dates." 00:00:01' AND '".$dates." 23:59:59'"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.branch_code  = '".$branch_code."' "] = array('use'=>'where','val'=>null,'third'=>false);
        // if(CONSOLIDATOR){
            // $printss = $this->system_sales_rep_backoffice(false,$args,true);
        $details = $this->setup_model->get_branch_details($branch_code);
            
        $open_time = $details[0]->store_open;
        $close_time = $details[0]->store_close;
        $to_date = date('Y-m-d', strtotime($dates . ' +1 day'));
        $print_date =$dates." ".$open_time. "  - ".$to_date." ".$close_time;
        // $print_date =$dates." ".$open_time. "  - ".$dates." ".$close_time;
            // $this->system_sales_rep_backoffice($asjson,$args,$asjson,$print_date);
            $printss = $this->xread_rep_backoffice_aurora(false,$args,true,$print_date);
            // $printss = $this->print_sales_receipt_justin($sales_id,false,true,false,null,true,1,0,null,true);  
        // }else{

            // $printss = $this->print_sales_receipt_justin($sales_id,false,true,false,null,true,1,0,null,false); 
        // }
            

        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        header('Cache-Control: max-age=0');

        echo $printss;
    }
    public function back_zread(){
        $this->load->model('dine/reports_model');
        $this->load->helper('dine/reports_helper');
        $data = $this->syter->spawn('zread');
        $data['page_title'] = 'ZREAD';
        $data['code'] = ZreadBack();
        $data['add_css'] = array('css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css');
        $data['add_js'] = array('js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js');
        $data['load_js'] = 'dine/prints.php';
        $data['use_js'] = 'zreadBackJs';
        $this->load->view('page',$data);
    }
    public function check_zreads(){
        $this->load->helper('dine/print_helper');
        $this->load->model('core/admin_model');

        $date = $this->input->post('calendar');
        $user = $this->input->post('user');
        $terminal_id = $this->input->post('terminal_id');
        $json = $this->input->post('json');
        $branch_code = $this->input->post('branch_id');
        $dates = date2Sql($date);
        // echo $test;die();

        // $args["trans_sales.user_id"] = $user;
        $args["trans_sales.type !="] = 'mgtfree';
        // if(CONSOLIDATOR){
        //     if($terminal_id != null){
        //         $args['trans_sales.terminal_id'] = $terminal_id;
        //     }
        // }else{
        //     $args['trans_sales.terminal_id'] = TERMINAL_ID;
        // }
             // $args["trans_sales.terminal_id"] = 1;
        $args["trans_sales.datetime  BETWEEN '".$dates." 00:00:01' AND '".$dates." 23:59:59'"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.branch_code  = '".$branch_code."' "] = array('use'=>'where','val'=>null,'third'=>false);
        // $args["trans_sales.shift_id"] = 188;
        // $args["trans_sales.datetime"] = $date;
        // echo $user.' '.$date;die();
        // echo "<pre>",print_r($args),"</pre>";die();
        if($json == 'true'){
            $asjson = true;
        }else{
            $asjson = false;
        }
        $d = $dates." 00:00:01";
        $details = $this->setup_model->get_branch_details(BRANCH_CODE);
            
        $open_time = $details[0]->store_open;
        $close_time = $details[0]->store_close;
        $to_date = date('Y-m-d', strtotime($dates . ' +1 day'));
        $print_date =$dates." ".$open_time. "  - ".$to_date." ".$close_time;
        // $print_date =$dates." ".$open_time. "  - ".$dates." ".$close_time;
        // $this->zread_sales_rep_backoffice($asjson,$args,false,$d,$print_date);

        $this->admin_model->set_temp_trans_sales($branch_code,$dates." ".$open_time,$to_date." ".$close_time);
        $this->zread_sales_rep_backoffice_aurora($asjson,$args,false,$d,$print_date);
        // $this->system_sales_rep_jdmt_aurora($asjson,$args,false,$d,$print_date);

    }
    // public function zread_sales_rep_backoffice($asJson=false,$args=array(),$return_print_str=false,$date){
    //     ////hapchan
    //     ini_set('memory_limit', '-1');
    //     set_time_limit(3600);
        
    //     $print_str = $this->print_header();
    //     $user = $this->session->userdata('user');
    //     $time = $this->site_model->get_db_now();
    //     $post = $this->set_post();
    //     $curr = $this->search_current();
    //     $trans = $this->trans_sales($args,$curr);
    //     // echo "<pre>",print_r($date),"</pre>";die();
    //     // var_dump($trans['net']); die();
    //     $sales = $trans['sales'];
    //     $trans_menus = $this->menu_sales($sales['settled']['ids'],$curr);
    //     $trans_charges = $this->charges_sales($sales['settled']['ids'],$curr);
    //     $trans_discounts = $this->discounts_sales($sales['settled']['ids'],$curr);
    //     $tax_disc = $trans_discounts['tax_disc_total'];
    //     $no_tax_disc = $trans_discounts['no_tax_disc_total'];
    //     $trans_local_tax = $this->local_tax_sales($sales['settled']['ids'],$curr);
    //     $trans_tax = $this->tax_sales($sales['settled']['ids'],$curr);
    //     $trans_no_tax = $this->no_tax_sales($sales['settled']['ids'],$curr);
    //     $trans_zero_rated = $this->zero_rated_sales($sales['settled']['ids'],$curr);
    //     $payments = $this->payment_sales($sales['settled']['ids'],$curr);
    //     $less_v = $this->no_tax_breakdown_sales($sales['settled']['ids'],$curr);
    //     // echo "<pre>",print_r($less_v),"</pre>";die();
    //     $gross = $trans_menus['gross'];
        
    //     $net = $trans['net'];
    //     $void = $trans['void'];
    //     // $refund_amount = $trans['refund_amount'];
    //     // $v_amount = $trans['v_amount'];
    //     $cancelled = $trans['cancel_amount'];
    //     $charges = $trans_charges['total'];
    //     $discounts = $trans_discounts['total'];
    //     $local_tax = $trans_local_tax['total'];
    //     // echo $net;die();
    //     // echo $gross.' - '.$charges.' - '.$discounts.' - '.$net; die();
    //     $less_vat = (($gross+$charges+$local_tax) - $discounts) - $net;
    //     // $less_vat = $trans_discounts['vat_exempt_total'];

    //     // echo $gross.'+'.$charges.'+'.$local_tax.' - '.$discounts.' - '.$net;
    //     // die();

    //     if($less_vat < 0)
    //         $less_vat = 0;
    //     // var_dump($less_vat);

    //     //para mag tugmam yun payments and netsale
    //     // $net_sales2 = $gross + $charges - $discounts - $less_vat;
    //     // $diffs = $net_sales2 - $payments['total'];
    //     // if($diffs < 1){
    //     //     $less_vat = $less_vat + $diffs;
    //     // }
    //     // echo print_r($refund_amount);die();

    //     $tax = $trans_tax['total'];
    //     $no_tax = $trans_no_tax['total'];
    //     $zero_rated = $trans_zero_rated['total'];
    //     $no_tax -= $zero_rated;

    //     $title_name = "ZREAD";
    //     // if($post['title'] != "")
    //     //     $title_name = $post['title'];

    //     $print_str .= align_center($title_name,PAPER_WIDTH," ")."\r\n";
    //     $print_str .= align_center("TERMINAL ".$post['terminal'],PAPER_WIDTH," ")."\r\n";
    //     $print_str .= append_chars('Printed On','right',11," ").append_chars(": ".date2SqlDateTime($time),'right',19," ")."\r\n";
    //     $print_str .= append_chars('Printed BY','right',11," ").append_chars(": ".$user['full_name'],'right',19," ")."\r\n";
    //     // $print_str .= PAPER_LINE."\r\n";
    //     // $print_str .= align_center(sql2DateTime($args['date']),PAPER_WIDTH," ")."\r\n";
    //     // $print_str .= align_center("-",PAPER_WIDTH," ")."\r\n";
    //     // $print_str .= align_center(sql2DateTime($args['date']),PAPER_WIDTH," ")."\r\n";
    //     if($post['employee'] != "All")
    //         $print_str .= align_center($post['employee'],PAPER_WIDTH," ")."\r\n";
    //     $print_str .= PAPER_LINE."\r\n";

    //     $loc_txt = numInt(($local_tax));
    //     $net_no_adds = $net-($charges+$local_tax);
    //     // $nontaxable = $no_tax;
    //     //binago 9/25/2018 for zreading adjustment of vat exempt equal to the receipt vat exempt
    //     $nontaxable = $no_tax - $no_tax_disc;
    //     // echo $gross.' - '.$less_vat.' - '.$nontaxable.' - '.$zero_rated; die();
    //     // $taxable = ($gross - $less_vat - $nontaxable - $zero_rated) / 1.12;
    //     // 1.12; binago din para sa adjustment of vat exempt equal to the receipt vat exempt
    //     // $taxable =   ($gross - $discounts - $less_vat - $nontaxable) / 1.12;
    //     $taxable =   ($gross - $less_vat - $nontaxable - $zero_rated - $discounts) / 1.12; //change computation conflict for zero rated 10 17 2018
    //     $total_net = ($taxable) + ($nontaxable+$zero_rated) + $tax + $local_tax;
    //     $add_gt = $taxable+$nontaxable+$zero_rated;
    //     $nsss = $taxable +  $nontaxable +  $zero_rated;
    //     // echo print_r($less_vat);die();
    //     #GENERAL
    //         $print_str .= append_chars(substrwords('TOTAL SALES',18,""),"right",PAPER_TOTAL_COL_1," ")
    //                                  .append_chars(num($gross + $charges,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
    //         $print_str .= append_chars(substrwords(ucwords(strtoupper('SC/PWD VAT EXEMPT')),18,""),"right",PAPER_TOTAL_COL_1," ")
    //                                  .append_chars('-'.num($less_vat,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
    //         foreach ($less_v['types'] as $lv => $lvat) {
    //             // $slv = 0;
    //             // if($less_vat < $lvat['amount']){
    //             //    $slv = $lvat['amount'] - $less_vat;
    //             //    $tlv = $slv - $lvat['amount'];
    //             // }else{
    //             //    $tlv =  $lvat['amount'];
    //             // }
    //             // echo "<pre>",print_r($lvat['amount']),"</pre>";
    //             $print_str .= append_chars(substrwords(ucwords(strtoupper($lv)),18,"")." ".$lvat['disc_rate']."%","right",PAPER_TOTAL_COL_1," ")
    //                                  .append_chars('-'.num($lvat['amount'],2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
    //         }
    //         // $print_str .= append_chars(substrwords(ucwords(strtoupper('SC/PWD VAT EXEMPT')),18,""),"right",PAPER_TOTAL_COL_1," ")
    //         //                          .append_chars('-'.num($less_vat,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
    //         $print_str .= append_chars('',"right",12," ").align_center('',PAPER_TOTAL_COL_2," ")
    //                           .append_chars('----------',"left",PAPER_TOTAL_COL_2," ")."\r\n";

    //         $print_str .= append_chars(substrwords('GROSS SALES',18,""),"right",PAPER_TOTAL_COL_1," ")
    //                                  .append_chars(num($gross + $charges - $less_vat,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";

    //         // $types = $trans_charges['types'];
    //         // $qty = 0;
    //         // foreach ($types as $code => $val) {
    //         //     $amount = $val['amount'];
    //         //     $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",PAPER_TOTAL_COL_1," ")
    //         //                          .append_chars('-'.num($amount,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
    //         //     $qty += $val['qty'];
    //         // }
    //         $types = $trans_discounts['types'];
    //         $qty = 0;
    //         foreach ($types as $code => $val) {
    //             if($code != 'DIPLOMAT'){
    //                 $amount = $val['amount'];
    //                 // if(MALL == 'megamall' && $code == PWDDISC){
    //                 //     $amount = $val['amount'] / 1.12;
    //                 // }
    //                 // if($val['fix'] == 0){
    //                 // $print_str .= append_chars(substrwords(ucwords(strtoupper($code)),18,"")." ".$val['disc_rate']."%","right",PAPER_TOTAL_COL_1," ")
    //                 //                      .append_chars('-'.Num($amount,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
    //                 // }else{
    //                     $print_str .= append_chars(substrwords(ucwords(strtoupper($code)),18,""),"right",PAPER_TOTAL_COL_1," ")
    //                                      .append_chars('-'.Num($amount,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
    //                 // }
    //                 $qty += $val['qty'];
    //             }
    //         }
    //         $print_str .= append_chars('',"right",12," ").align_center('',PAPER_TOTAL_COL_2," ")
    //                           .append_chars('----------',"left",PAPER_TOTAL_COL_2," ")."\r\n";
    //         $net_sales = $gross + $charges - $discounts - $less_vat;
    //         $print_str .= append_chars(substrwords(ucwords(strtoupper('NET SALES')),18,""),"right",PAPER_TOTAL_COL_1," ")
    //                                  .append_chars(num($net_sales,2),"left",PAPER_TOTAL_COL_2," ")."\r\n\r\n";
    //     #PAYMENTS
    //         $payments_types = $payments['types'];
    //         $payments_total = $payments['total'];
    //         $pay_qty = 0;
    //     #SUMMARY
    //         $final_gross = $gross;
    //         $vat_ = $taxable * .12;
    //         $print_str .= append_chars(substrwords('VAT SALES',23,""),"right",PAPER_TOTAL_COL_1," ")
    //                                  .append_chars(num($taxable,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
    //         $print_str .= append_chars(substrwords('VAT',23,""),"right",PAPER_TOTAL_COL_1," ")
    //                                  .append_chars(num($vat_,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
    //         $print_str .= append_chars(substrwords('VAT EXEMPT SALES',23,""),"right",PAPER_TOTAL_COL_1," ")
    //                                  .append_chars(num($nontaxable,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
    //                                  // .append_chars(numInt($nontaxable-$zero_rated),"left",PAPER_TOTAL_COL_2," ")."\r\n";
    //         $print_str .= append_chars(substrwords('ZERO RATED',23,""),"right",PAPER_TOTAL_COL_1," ")
    //                                  .append_chars(num($zero_rated,2),"left",PAPER_TOTAL_COL_2," ")."\r\n\r\n";
    //         $print_str .= append_chars(substrwords('PAYMENT BREAKDOWN:',18,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
    //                       .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
    //         foreach ($payments_types as $code => $val) {
    //             $print_str .= append_chars(substrwords(ucwords(strtoupper($code)),18,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
    //                           .append_chars(num($val['amount'],2),"left",PAPER_RD_COL_3_3," ")."\r\n";
    //             $pay_qty += $val['qty'];
    //         }
    //         $print_str .= append_chars('',"right",18," ").align_center('',PAPER_RD_COL_2," ")
    //                           .append_chars('----------',"left",PAPER_RD_COL_3_3," ")."\r\n";
    //         $print_str .= append_chars(substrwords('TOTAL PAYMENTS',18,""),"right",PAPER_RD_COL_1," ").align_center($pay_qty,PAPER_RD_COL_2," ")
    //                       .append_chars(num($payments_total,2),"left",PAPER_RD_COL_3_3," ")."\r\n\r\n";
    //         $print_str .= PAPER_LINE_SINGLE."\r\n";
    //         $gross_less_disc = $final_gross - $discounts - $less_vat;
    //         // $print_str .= append_chars(substrwords('NET SALES',23,""),"right",PAPER_TOTAL_COL_1," ")
    //         //                          // .append_chars(numInt(($taxable + $nontaxable + $zero_rated + $vat_)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
    //         //                          .append_chars(numInt(($taxable + $nontaxable + $vat_)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
    //         // $print_str .= PAPER_LINE."\r\n";

    //         // if(count($payments['currency']) > 0){
    //         //     $currency = $payments['currency'];
    //         //     $print_str .= append_chars(substrwords('Currency Breakdown:',20,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
    //         //                   .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
    //         //     foreach ($currency as $code => $val) {
    //         //         $print_str .= append_chars(substrwords(ucwords(strtoupper($code)),18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ").append_chars(num($val['amount'],2),"left",PAPER_RD_COL_3_3," ")."\r\n";
    //         //         // $pay_qty += $val['qty'];
    //         //     }
    //         // }

    //         // $print_str .= PAPER_LINE_SINGLE."\r\n";
    //         $print_str .= "\r\n\r\n";



    //         // $print_str .= append_chars(substrwords('REFUND',18,""),"right",PAPER_TOTAL_COL_1," ")
    //                      // .append_chars(num(($refund_amount),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
    //         // $print_str .= append_chars(substrwords('VOID SALES',18,""),"right",PAPER_TOTAL_COL_1," ")
    //                      // .append_chars(num(($v_amount),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
    //         $print_str .= append_chars(substrwords('CANCELLED TRANS',18,""),"right",PAPER_TOTAL_COL_1," ")
    //                      .append_chars(num(($cancelled),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";

    //         $cancelled_order = $this->cancelled_orders();
    //         $co = $cancelled_order['cancelled_order'];
    //         $print_str .= append_chars(substrwords('CANCELLED ORDERS',18,""),"right",PAPER_TOTAL_COL_1," ")
    //                      .append_chars(num(($co),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
    //         $print_str .= append_chars(substrwords('LOCAL TAX',18,""),"right",PAPER_TOTAL_COL_1," ")
    //                      .append_chars($loc_txt,"left",PAPER_TOTAL_COL_2," ")."\r\n";
    //         $print_str .= "\r\n";
    //     #TRANS COUNT
    //         $types = $trans['types'];
    //         $types_total = array();
    //         $guestCount = 0;
    //         foreach ($types as $type => $tp) {
    //             foreach ($tp as $id => $opt){
    //                 if(isset($types_total[$type])){
    //                     $types_total[$type] += round($opt->total_amount,2);

    //                 }
    //                 else{
    //                     $types_total[$type] = round($opt->total_amount,2);
    //                 }

    //                 // if($opt->type == 'dinein'){
    //                 //     $guestCount += $opt->guest;
    //                 // }
    //                 if($opt->guest == 0)
    //                     $guestCount += 1;
    //                 else
    //                     $guestCount += $opt->guest;
    //             }
    //         }
    //         $print_str .= append_chars(substrwords('TRANS COUNT:',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
    //                      .append_chars('',"left",PAPER_RD_COL_3_3," ")."\r\n";
    //         $tc_total  = 0;
    //         $tc_qty = 0;
    //         foreach ($types_total as $typ => $tamnt) {
    //             $print_str .= append_chars(ucwords($typ),"right",PAPER_RD_COL_1," ").align_center(count($types[$typ]),PAPER_RD_COL_2," ")
    //                          .append_chars(num($tamnt,2),"left",PAPER_RD_COL_3_3," ")."\r\n";
    //             $tc_total += $tamnt;
    //             $tc_qty += count($types[$typ]);
    //         }
    //         $print_str .= "-----------------"."\r\n";
    //         $print_str .= append_chars(substrwords('TC TOTAL',18,""),"right",PAPER_TOTAL_COL_1," ")
    //                      .append_chars(num($tc_total,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
    //         $print_str .= append_chars(substrwords('GUEST TOTAL',18,""),"right",PAPER_TOTAL_COL_1," ")
    //                      .append_chars($guestCount,"left",PAPER_TOTAL_COL_2," ")."\r\n";
    //         // if($tc_total == 0 || $tc_qty == 0)
    //         //     $avg = 0;
    //         // else
    //         //     $avg = $tc_total/$tc_qty;
    //         if($net_sales){
    //             if($guestCount == 0){
    //                 $avg = 0;
    //             }else{
    //                 $avg = $net_sales/$guestCount;
    //             }
    //         }else{
    //             $avg = 0;
    //         }


    //         $print_str .= append_chars(substrwords('AVG CHECK',18,""),"right",PAPER_TOTAL_COL_1," ")
    //                      .append_chars(num($avg,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
    //         $print_str .= "\r\n";
    //     #CHARGES
    //         $types = $trans_charges['types'];
    //         $qty = 0;
    //         $print_str .= append_chars(substrwords('CHARGES:',18,""),"right",18," ").align_center(null,5," ")
    //                       .append_chars(null,"left",13," ")."\r\n";
    //         foreach ($types as $code => $val) {
    //             $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
    //                           .append_chars(num($val['amount'],2),"left",PAPER_RD_COL_3_3," ")."\r\n";
    //             $qty += $val['qty'];
    //         }
    //         $print_str .= "-----------------"."\r\n";
    //         $print_str .= append_chars(substrwords('TOTAL CHARGES',18,""),"right",PAPER_RD_COL_1," ").align_center($qty,PAPER_RD_COL_2," ")
    //                       .append_chars(num($charges,2),"left",PAPER_RD_COL_3_3," ")."\r\n";
    //         $print_str .= "\r\n";
    //     #Discounts
    //         $types = $trans_discounts['types'];
    //         $qty = 0;
    //         $print_str .= append_chars(substrwords('DISCOUNTS:',18,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
    //                       .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
    //         foreach ($types as $code => $val) {
    //             if($code != 'DIPLOMAT'){
    //                 $amount = $val['amount'];
    //                 // if(MALL == 'megamall' && $code == PWDDISC){
    //                 //     $amount = $val['amount'] / 1.12;
    //                 // }
    //                 $print_str .= append_chars(substrwords(ucwords(strtoupper($code)),18,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
    //                               .append_chars(num($amount,2),"left",PAPER_RD_COL_3_3," ")."\r\n";
    //                 $qty += $val['qty'];
    //             }
    //         }
    //         $print_str .= "-----------------"."\r\n";
    //         $print_str .= append_chars(substrwords('TOTAL DISCOUNTS',18,""),"right",PAPER_RD_COL_1," ").align_center($qty,PAPER_RD_COL_2," ")
    //                       .append_chars(num($discounts,2),"left",PAPER_RD_COL_3_3," ")."\r\n";
    //         $print_str .= append_chars(substrwords('VAT EXEMPT',18,""),"right",PAPER_TOTAL_COL_1," ")
    //                                  .append_chars(num($less_vat,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
    //         $print_str .= "\r\n";
    //     #PAYMENTS
    //         $payments_types = $payments['types'];
    //         $payments_total = $payments['total'];
    //         $pay_qty = 0;
    //         $print_str .= append_chars(substrwords('PAYMENT BREAKDOWN:',18,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
    //                       .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
    //         foreach ($payments_types as $code => $val) {
    //             $print_str .= append_chars(substrwords(ucwords(strtolower($code)),18,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
    //                           .append_chars(num($val['amount'],2),"left",PAPER_RD_COL_3_3," ")."\r\n";
    //             $pay_qty += $val['qty'];
    //         }
    //         $print_str .= "-----------------"."\r\n";
    //         $print_str .= append_chars(substrwords('TOTAL PAYMENTS',18,""),"right",PAPER_RD_COL_1," ").align_center($pay_qty,PAPER_RD_COL_2," ")
    //                       .append_chars(num($payments_total,2),"left",PAPER_RD_COL_3_3," ")."\r\n";
    //         $print_str .= "\r\n";

    //         //card breakdown
    //         // if($payments['cards']){
    //         //     $cards = $payments['cards'];
    //         //     $card_total = 0;
    //         //     $count_total = 0;
    //         //     $print_str .= append_chars(substrwords('CARD BREAKDOWN:',18,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
    //         //               .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
    //         //     foreach($cards as $key => $val){
    //         //         $print_str .= append_chars(substrwords($key,18,""),"right",PAPER_RD_COL_1," ").align_center($val['count'],PAPER_RD_COL_2," ")
    //         //                   .append_chars(num($val['amount'],2),"left",PAPER_RD_COL_3_3," ")."\r\n";
    //         //         $card_total += $val['amount'];
    //         //         $count_total += $val['count'];
    //         //     }
    //         //     $print_str .= "-----------------"."\r\n";
    //         //     $print_str .= append_chars(substrwords('TOTAL',18,""),"right",PAPER_RD_COL_1," ").align_center($count_total,PAPER_RD_COL_2," ")
    //         //               .append_chars(num($card_total,2),"left",PAPER_RD_COL_3_3," ")."\r\n";
                
    //         //     $print_str .= "\r\n";
    //         // }

    //         //get all gc with excess
    //         if($payments['gc_excess']){
    //             $print_str .= append_chars(substrwords('GC EXCESS',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
    //                           .append_chars(num($payments['gc_excess'],2),"left",PAPER_RD_COL_3_3," ")."\r\n";
    //             $print_str .= "\r\n";
    //         }

    //         //show all sign chit
    //         // $trans['sales']
    //         if($trans['total_chit']){
    //             $print_str .= append_chars(substrwords('TOTAL CHIT',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
    //                           .append_chars(num($trans['total_chit'],2),"left",PAPER_RD_COL_3_3," ")."\r\n";
    //             $print_str .= "\r\n";
    //         }
            
    //         $print_str .= append_chars(substrwords('Invoice Start: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
    //                      .append_chars(iSetObj($trans['first_ref'],'trans_ref'),"left",PAPER_RD_COL_3_3," ")."\r\n";
    //         $print_str .= append_chars(substrwords('Invoice End: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
    //                      .append_chars(iSetObj($trans['last_ref'],'trans_ref'),"left",PAPER_RD_COL_3_3," ")."\r\n";
    //         $print_str .= append_chars(substrwords('Invoice Ctr: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
    //                      .append_chars($trans['ref_count'],"left",PAPER_RD_COL_3_3," ")."\r\n";
    //         if($title_name == "ZREAD"){
    //             $gt = $this->old_grand_net_total($date);
    //             $print_str .= "\r\n";
    //             $print_str .= append_chars(substrwords('OLD GT: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
    //                          .append_chars(num( $gt['old_grand_total'],2),"left",PAPER_RD_COL_3_3," ")."\r\n";
    //             $print_str .= append_chars(substrwords('NEW GT: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
    //                          .append_chars( num($gt['old_grand_total']+$net_no_adds,2)  ,"left",PAPER_RD_COL_3_3," ")."\r\n";
    //             $print_str .= append_chars(substrwords('Z READ CTR: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
    //                          .append_chars( $gt['ctr'] ,"left",PAPER_RD_COL_3_3," ")."\r\n";
    //         }
    //         $print_str .= PAPER_LINE."\r\n";
    //     #CATEGORIES
    //     //     $cats = $trans_menus['cats'];
    //     //     $print_str .= append_chars('ITEM CATEGORY SUMMARY:',"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
    //     //                  .append_chars('',"left",PAPER_RD_COL_3," ")."\r\n";
    //     //     $qty = 0;
    //     //     $total = 0;
    //     //     foreach ($cats as $id => $val) {
    //     //         if($val['qty'] > 0){
    //     //             $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
    //     //                        .append_chars(num($val['amount'],2),"left",PAPER_RD_COL_3_3," ")."\r\n";
    //     //             $qty += $val['qty'];
    //     //             $total += $val['amount'];
    //     //         }
    //     //      }
    //     //     $print_str .= "-----------------"."\r\n";
    //     //     $cat_total_qty = $qty;
    //     //     $print_str .= append_chars("SUBTOTAL","right",PAPER_RD_COL_1," ").align_center($qty,PAPER_RD_COL_2," ")
    //     //                   .append_chars(num($total,2),"left",PAPER_RD_COL_3_3," ")."\r\n";
    //     //     $print_str .= append_chars("MODIFIERS TOTAL","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
    //     //                   .append_chars(num($trans_menus['mods_total'],2),"left",PAPER_RD_COL_3_3," ")."\r\n";
    //     //      $print_str .= append_chars("SUBMODIFIERS TOTAL","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
    //     //                   .append_chars(num($trans_menus['submods_total'],2),"left",PAPER_RD_COL_3_3," ")."\r\n";
    //     //     if($trans_menus['item_total'] > 0){
    //     //      $print_str .= append_chars("RETAIL ITEMS TOTAL","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
    //     //                    .append_chars(num($trans_menus['item_total'],2),"left",PAPER_RD_COL_3_3," ")."\r\n";
    //     //     }

    //     //     $print_str .= append_chars("TOTAL","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
    //     //                   .append_chars(num($total+$trans_menus['mods_total']+$trans_menus['item_total']+$trans_menus['submods_total'],2),"left",PAPER_RD_COL_3_3," ")."\r\n";
    //     //     $print_str .= "\r\n";
    //     // #SUBCATEGORIES
    //     //     $subcats = $trans_menus['sub_cats'];
    //     //     // $print_str .= append_chars('Menu Subcategories:',"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
    //     //     $print_str .= append_chars('MENU TYPES:',"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
    //     //                  .append_chars('',"left",PAPER_RD_COL_3," ")."\r\n";
    //     //     $qty = 0;
    //     //     $total = 0;
    //     //     foreach ($subcats as $id => $val) {
    //     //         $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
    //     //                    .append_chars(num($val['amount'],2),"left",PAPER_RD_COL_3_3," ")."\r\n";
    //     //         $qty += $val['qty'];
    //     //         $total += $val['amount'];
    //     //      }
    //     //     $print_str .= "-----------------"."\r\n";
    //     //     $print_str .= append_chars("TOTAL","right",PAPER_RD_COL_1," ").align_center($qty,PAPER_RD_COL_2," ")
    //     //                   .append_chars(num($total,2),"left",PAPER_RD_COL_3_3," ")."\r\n";
    //     //     // $print_str .= append_chars("Modifiers Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
    //     //     //               .append_chars(numInt($trans_menus['mods_total']),"left",PAPER_RD_COL_3_3," ")."\r\n";
    //     //     // $print_str .= append_chars("SubModifier Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
    //     //     //               .append_chars(numInt($trans_menus['submods_total']),"left",PAPER_RD_COL_3_3," ")."\r\n";
    //     //     // $print_str .= append_chars("Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
    //     //     //               .append_chars(numInt($total+$trans_menus['mods_total']+$trans_menus['submods_total']),"left",PAPER_RD_COL_3_3," ")."\r\n";
    //     //     $print_str .= "\r\n";
    //     // #FREE MENUS
    //     //     $free = $trans_menus['free_menus'];
    //     //     $print_str .= append_chars('FREE MENUS:',"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
    //     //                  .append_chars('',"left",PAPER_RD_COL_3," ")."\r\n";
    //     //     $fm = array();
    //     //     foreach ($free as $ms) {
    //     //         if(!isset($fm[$ms->menu_id])){
    //     //             $mn = array();
    //     //             $mn['name'] = $ms->menu_name;
    //     //             $mn['cat_id'] = $ms->cat_id;
    //     //             $mn['qty'] = $ms->qty;
    //     //             $mn['amount'] = $ms->sell_price * $ms->qty;
    //     //             $mn['sell_price'] = $ms->sell_price;
    //     //             $mn['code'] = $ms->menu_code;
    //     //             // $mn['free_user_id'] = $ms->free_user_id;
    //     //             $fm[$ms->menu_id] = $mn;
    //     //         }
    //     //         else{
    //     //             $mn = $fm[$ms->menu_id];
    //     //             $mn['qty'] += $ms->qty;
    //     //             $mn['amount'] += $ms->sell_price * $ms->qty;
    //     //             $fm[$ms->menu_id] = $mn;
    //     //         }
    //     //     }
    //     //     $qty = 0;
    //     //     $total = 0;
    //     //     foreach ($fm as $menu_id => $val) {
    //     //         $print_str .= append_chars($val['name'],"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
    //     //                    .append_chars(($val['qty']),"left",PAPER_RD_COL_3_3," ")."\r\n";
    //     //         $qty += $val['qty'];
    //     //         $total += $val['amount'];
    //     //     }
    //     //     $print_str .= "-----------------"."\r\n";
    //     //     $print_str .= append_chars("TOTAL","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
    //     //                   .append_chars(($qty),"left",PAPER_RD_COL_3_3," ")."\r\n";
    //     //     $print_str .= "\r\n";
    //         $print_str .= "\r\n";    
    //     #FOOTER
    //         // $print_str .= append_chars(substrwords('Invoice Start: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
    //         //              .append_chars(iSetObj($trans['first_ref'],'trans_ref'),"left",PAPER_RD_COL_3_3," ")."\r\n";
    //         // $print_str .= append_chars(substrwords('Invoice End: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
    //         //              .append_chars(iSetObj($trans['last_ref'],'trans_ref'),"left",PAPER_RD_COL_3_3," ")."\r\n";
    //         // $print_str .= append_chars(substrwords('Invoice Ctr: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
    //         //              .append_chars($trans['ref_count'],"left",PAPER_RD_COL_3_3," ")."\r\n";
    //         // if($title_name == "ZREAD"){
    //         //     $gt = $this->old_grand_net_total($post['from']);
    //         //     $print_str .= "\r\n";
    //         //     $print_str .= append_chars(substrwords('OLD GT: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
    //         //                  .append_chars(num( $gt['old_grand_total'],2),"left",PAPER_RD_COL_3_3," ")."\r\n";
    //         //     $print_str .= append_chars(substrwords('NEW GT: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
    //         //                  .append_chars( num($gt['old_grand_total']+$net_no_adds,2)  ,"left",PAPER_RD_COL_3_3," ")."\r\n";
    //         //     $print_str .= append_chars(substrwords('Z READ CTR: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
    //         //                  .append_chars( $gt['ctr'] ,"left",PAPER_RD_COL_3_3," ")."\r\n";
    //         // }
    //         // $print_str .= PAPER_LINE."\r\n";
    //     #MALLS
    //         if(MALL_ENABLED){
    //             ####################################
    //             # AYALA
    //                 if(MALL == 'ayala'){
                        
    //                     $branch_details = $this->setup_model->get_branch_details();
    //                     $branch = array();
    //                     foreach ($branch_details as $bv) {
    //                         $branch = array(
    //                             'id' => $bv->branch_id,
    //                             'res_id' => $bv->res_id,
    //                             'branch_code' => $bv->branch_code,
    //                             'name' => $bv->branch_name,
    //                             'branch_desc' => $bv->branch_desc,
    //                             'contact_no' => $bv->contact_no,
    //                             'delivery_no' => $bv->delivery_no,
    //                             'address' => $bv->address,
    //                             'base_location' => $bv->base_location,
    //                             'currency' => $bv->currency,
    //                             'inactive' => $bv->inactive,
    //                             'tin' => $bv->tin,
    //                             'machine_no' => $bv->machine_no,
    //                             'bir' => $bv->bir,
    //                             'permit_no' => $bv->permit_no,
    //                             'serial' => $bv->serial,
    //                             'accrdn' => $bv->accrdn,
    //                             'email' => $bv->email,
    //                             'website' => $bv->website,
    //                             'store_open' => $bv->store_open,
    //                             'store_close' => $bv->store_close,
    //                         );
    //                     }


    //                     $print_str .= align_center("FOR AYALA",PAPER_WIDTH," ")."\r\n";
    //                     $print_str .= align_center($branch['name'],PAPER_WIDTH," ")."\r\n";
    //                     $print_str .= align_center($branch['address'],PAPER_WIDTH," ")."\r\n\r\n";
    //                     $print_str .= align_center("CONSOLIDATED REPORT Z-READ",PAPER_WIDTH," ")."\r\n\r\n";


    //                     $total_daily_sales = $total_vatA = $total_rawgrossA = $total_discount = $total_refund = $total_void = $total_charge = $total_non_tax = $total_trans_count = $total_guest = 0;


    //                     $paytype = array();
    //                     foreach ($payments_types as $code => $val) {
    //                         if($code != 'credit'){
    //                             if(!isset($paytype[$code])){
    //                                 $paytype[$code] = array('amount'=>$val['amount']);
    //                             }else{
    //                                 $row = $paytype[$code];
    //                                 $row['amount'] += $val['amount'];
    //                                 $paytype[$code] = $row;
    //                             }
    //                         }
    //                         // $print_str .= append_chars(substrwords(ucwords(strtoupper($code)),12,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
    //                         //               .append_chars(num($val['amount'],2),"left",PAPER_RD_COL_3_3," ")."\r\n";
    //                         // $pay_qty += $val['qty'];
    //                     }
    //                     $paycards = array();
    //                     if($payments['cards']){
    //                         $cards = $payments['cards'];
    //                         foreach($cards as $key => $val){
    //                             if(!isset($paycards[$key])){
    //                                 $paycards[$key] = array('amount'=>$val['amount']);
    //                             }else{
    //                                 $row = $paycards[$key];
    //                                 $row['amount'] += $val['amount'];
    //                                 $paycards[$key] = $row;
    //                             }
    //                         }
    //                     }


    //                     // for server
    //                     $rawgrossA = numInt($gross + $charges + $void + $local_tax);
    //                     $vatA = numInt(($rawgrossA  - $discounts - $void  -  $charges - $nontaxable - $local_tax - numInt($less_vat)) * (1/9.333333));
    //                     $dlySaleA = numInt($rawgrossA - $discounts - $void - $charges - $vatA - $less_vat + $local_tax);
    //                     // $t_discounts = $discounts+$less_vat;
    //                     $rawgrossA =  $rawgrossA - $less_vat;
    //                     $t_discounts = $discounts;



    //                     $trans_count = 0;
    //                     $begor = 0;
    //                     $endor = 0;
    //                     $first_inv = array();
    //                     $last_inv = array();
    //                     $first_ref = 0;
    //                     $last_ref = 0;
    //                     $first_val = 0;
    //                     $last_val = 0;
    //                     $invs = array();
    //                     foreach ($trans['all_orders'] as $ord) {
    //                         if($ord->type_id == SALES_TRANS && $ord->trans_ref != ""){
    //                             $ref = $ord->trans_ref;
    //                             if (preg_match('/^(\D*?)(\d+)(.*)/', $ref, $result) == 1){
    //                                 // if($ord->inactive != 1){
    //                                     list($all, $prefix, $number, $postfix) = $result;
    //                                     $ref_val = intval($number);
    //                                     $invs[$ref_val] = array("ref"=>$ord->trans_ref,"val"=>$ref_val);
    //                                 // }
    //                             }
    //                         }
    //                     }
    //                     ksort($invs);
    //                     // echo "<pre>",print_r($invs),"</pre>";die();
    //                     $first_inv = reset($invs);
    //                     $last_inv = end($invs);
    //                     if(count($first_inv) > 0){
    //                         $first_ref = $first_inv['ref'];
    //                         $first_val = $first_inv['val'];
    //                     }
    //                     if(count($last_inv) > 0){
    //                         $last_ref = $last_inv['ref'];
    //                         $last_val = $last_inv['val'];
    //                     }
    //                     if(count($invs) > 0){
    //                         $trans_count = ($last_val - $first_val) + 1; 
    //                     }

    //                     // echo $trans_count; die();
    //                     //add yun mga value ng server sa totals
    //                     $total_daily_sales += $dlySaleA;
    //                     $total_vatA += $vatA;
    //                     $total_rawgrossA += $rawgrossA;
    //                     $total_discount += $t_discounts;
    //                     $total_void += $void;
    //                     $total_charge += $charges;
    //                     $total_non_tax += $nontaxable;
    //                     // $total_trans_count += $tc_qty;
    //                     $total_trans_count += $trans_count;
    //                     $total_guest += $guestCount;
    //                      // echo $total_trans_count;

    //                     $terminals = $this->setup_model->get_terminals();


    //                     $print_str .= append_chars(substrwords('Daily Sales',25,""),"right",22," ").align_center('',2," ")
    //                                  .append_chars(num($total_daily_sales,2),"left",10," ")."\r\n";
    //                     $print_str .= append_chars(substrwords('Total Discount',25,""),"right",22," ").align_center('',2," ")
    //                                  .append_chars(num($total_discount,2),"left",10," ")."\r\n";
    //                     $print_str .= append_chars(substrwords('Total Refund',25,""),"right",22," ").align_center('',2," ")
    //                                  .append_chars(num(0,2),"left",10," ")."\r\n";
    //                     $print_str .= append_chars(substrwords('Total Void',25,""),"right",22," ").align_center('',2," ")
    //                                  .append_chars(num($total_void,2),"left",10," ")."\r\n";
    //                     $print_str .= append_chars(substrwords('Total Vat',25,""),"right",22," ").align_center('',2," ")
    //                                  .append_chars(num($total_vatA,2),"left",10," ")."\r\n";
    //                     $print_str .= append_chars(substrwords('Total Service Charge',25,""),"right",22," ").align_center('',2," ").append_chars(num($total_charge),"left",10," ")."\r\n";  


    //                     // $print_str .= append_chars(substrwords('Vatable Sales',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
    //                     //              .append_chars(num($dlySaleA-$nontaxable),"left",PAPER_RD_COL_3_3," ")."\r\n";             
    //                     $print_str .= append_chars(substrwords('Total Non Taxable',22,""),"right",22," ").align_center('',2," ")
    //                                  .append_chars(num($total_non_tax,2),"left",10," ")."\r\n";
    //                     $print_str .= append_chars(substrwords('Row Gross',22,""),"right",22," ").align_center('',2," ")
    //                                  .append_chars(num($total_rawgrossA,2),"left",10," ")."\r\n";             
    //                     $print_str .= append_chars(substrwords('Transaction Count',22,""),"right",22," ").align_center('',2," ")
    //                                  .append_chars($total_trans_count,"left",10," ")."\r\n";
    //                     $print_str .= append_chars(substrwords('Customer Count',22,""),"right",22," ").align_center('',2," ")
    //                                  .append_chars($total_guest,"left",10," ")."\r\n";
    //                     foreach ($paytype as $k => $v) {
    //                         $print_str .= append_chars(strtoupper($k),"right",22," ").align_center('',2," ")
    //                              .append_chars(num($v['amount'],2),"left",10," ")."\r\n";       
    //                     }
    //                     foreach ($paycards as $k => $v) {
    //                         $print_str .= append_chars(strtoupper($k),"right",22," ").align_center('',2," ")
    //                              .append_chars(num($v['amount'],2),"left",10," ")."\r\n";       
    //                     }
    //                     $terminals = $this->setup_model->get_terminals();
    //                     // echo "<pre>",print_r($terminals),"</pre>";die();
    //                     foreach ($terminals as $k => $val) {
    //                         $print_str .= append_chars('BIR PERMIT '.$val->terminal_id,"right",15," ").align_center('',2," ")
    //                              .append_chars($val->permit,"left",17," ")."\r\n";
    //                         $print_str .= append_chars('SERIAL NO. '.$val->terminal_id,"right",15," ").align_center('',2," ")
    //                              .append_chars($val->serial,"left",17," ")."\r\n";
    //                     }           
    //                     // $print_str .= append_chars(substrwords('Less SC Disc',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
    //                     //              .append_chars(num($discounts),"left",PAPER_RD_COL_3_3," ")."\r\n";             
    //                     // $print_str .= append_chars(substrwords('Vat Exempt',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
    //                     //              .append_chars(num($less_vat),"left",PAPER_RD_COL_3_3," ")."\r\n";             
    //                     // $print_str .= append_chars(substrwords('Zero Rated',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
    //                     //              .append_chars(num($zero_rated),"left",PAPER_RD_COL_3_3," ")."\r\n";             
    //                     // $print_str .= PAPER_LINE_SINGLE."\r\n";
    //                     // $print_str .= append_chars(substrwords('Net Sales',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
    //                     //              .append_chars(num($dlySaleA+$vatA),"left",PAPER_RD_COL_3_3," ")."\r\n";             
    //                     // $print_str .= append_chars(substrwords('Total Qty Sold',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
    //                     //              .append_chars(num($cat_total_qty),"left",PAPER_RD_COL_3_3," ")."\r\n";             
    //                     $print_str .= PAPER_LINE."\r\n";
    //                     $print_str .= align_center(sql2Date($post['from']),PAPER_WIDTH," ")."\r\n";
    //                     $print_str .= align_center("END OF REPORT",PAPER_WIDTH," ")."\r\n";


    //                 }
    //             ####################################
    //         }  

    //         if ($return_print_str) {
    //             return $print_str;
    //         }  

    //     // if($title_name == "ZREAD"){
    //     //     if($asJson == false){
    //     //         $this->manager_model->add_event_logs($user['id'],"ZREAD","View");    
    //     //     }else{
    //     //         $this->manager_model->add_event_logs($user['id'],"ZREAD","Print");                    
    //     //     } 
    //     // }elseif($title_name == "XREAD"){
    //     //     if($asJson == false){
    //     //         $this->manager_model->add_event_logs($user['id'],"XREAD","View");    
    //     //     }else{
    //     //         $this->manager_model->add_event_logs($user['id'],"XREAD","Print");                    
    //     //     } 
    //     // }else{
    //     //     if($asJson == false){
    //     //         $this->manager_model->add_event_logs($user['id'],"System Sales","View");    
    //     //     }else{
    //     //         $this->manager_model->add_event_logs($user['id'],"System Sales","Print");                    
    //     //     }
    //     // } 

    //     $this->session->set_userdata('pdf_data','<pre>'.$print_str.'</pre>') ; 
    //     // if(PRINT_VERSION && PRINT_VERSION == 'V2'){
    //     //     $this->do_print_v2($print_str,$asJson);  
    //     // }else if(PRINT_VERSION && PRINT_VERSION == 'V3' && $asJson){
    //     //     echo $this->html_print($print_str);
    //     // }else{
    //         $this->do_print($print_str,$asJson);
    //     // }
    //     // $this->do_print($print_str,$asJson);
    // }
     public function zread_sales_rep_backoffice($asJson=false,$args=array(),$return_print_str=false,$date,$print_date){
        ////hapchan
            ini_set('memory_limit', '-1');
            set_time_limit(3600);
            
            $print_str = $this->print_header();
            $user = $this->session->userdata('user');
            $time = $this->site_model->get_db_now();
            $post = $this->set_post();
            $curr = $this->search_current();
            $trans = $this->trans_sales($args,$curr);
            // var_dump($trans['net']); die();
            $sales = $trans['sales'];
            $trans_menus = $this->menu_sales($sales['settled']['ids'],$curr,BRANCH_CODE);
            $trans_charges = $this->charges_sales($sales['settled']['ids'],$curr,BRANCH_CODE);
            $trans_discounts = $this->discounts_sales($sales['settled']['ids'],$curr,BRANCH_CODE);
            $tax_disc = $trans_discounts['tax_disc_total'];
            $no_tax_disc = $trans_discounts['no_tax_disc_total'];
            $trans_local_tax = $this->local_tax_sales($sales['settled']['ids'],$curr,BRANCH_CODE);
            $trans_tax = $this->tax_sales($sales['settled']['ids'],$curr,BRANCH_CODE);
            $trans_no_tax = $this->no_tax_sales($sales['settled']['ids'],$curr,BRANCH_CODE);
            $trans_zero_rated = $this->zero_rated_sales($sales['settled']['ids'],$curr,BRANCH_CODE);
            $payments = $this->payment_sales($sales['settled']['ids'],$curr,BRANCH_CODE);

            $gross = $trans_menus['gross'];
            
            $net = $trans['net'];
            $void = $trans['void'];
            $cancelled = $trans['cancel_amount'];
            $charges = $trans_charges['total'];
            $discounts = $trans_discounts['total'];
            $local_tax = $trans_local_tax['total'];
            // echo $gross.' - '.$charges.' - '.$discounts.' - '.$net; die();
            $less_vat = (($gross+$charges+$local_tax) - $discounts) - $net;
            // $less_vat = $trans_discounts['vat_exempt_total'];

            // echo $gross.'+'.$charges.'+'.$local_tax.' - '.$discounts.' - '.$net;
            // die();

            if($less_vat < 0)
                $less_vat = 0;
            // var_dump($less_vat);

            //para mag tugmam yun payments and netsale
            // $net_sales2 = $gross + $charges - $discounts - $less_vat;
            // $diffs = $net_sales2 - $payments['total'];
            // if($diffs < 1){
            //     $less_vat = $less_vat + $diffs;
            // }
            

            $tax = $trans_tax['total'];
            $no_tax = $trans_no_tax['total'];
            $zero_rated = $trans_zero_rated['total'];
            $no_tax -= $zero_rated;

            $title_name = "ZREAD";
            // if($post['title'] != "")
            //     $title_name = $post['title'];

            $print_str .= align_center($title_name,PAPER_WIDTH," ")."\r\n";
            $print_str .= align_center("TERMINAL ".$post['terminal'],PAPER_WIDTH," ")."\r\n";
            $print_str .= append_chars('Printed On','right',11," ").append_chars(": ".date2SqlDateTime($time),'right',19," ")."\r\n";
            $print_str .= append_chars('Printed BY','right',11," ").append_chars(": ".$user['full_name'],'right',19," ")."\r\n";
             $print_str .= PAPER_LINE."\r\n";
             $print_str .= align_center($print_date,PAPER_WIDTH," ")."\r\n";
            if($post['employee'] != "All")
                $print_str .= align_center($post['employee'],PAPER_WIDTH," ")."\r\n";
            $print_str .= PAPER_LINE."\r\n";

            $loc_txt = numInt(($local_tax));
            $net_no_adds = $net-($charges+$local_tax);
            // $nontaxable = $no_tax;
            //binago 9/25/2018 for zreading adjustment of vat exempt equal to the receipt vat exempt
            $nontaxable = $no_tax - $no_tax_disc;
            // echo $gross.' - '.$less_vat.' - '.$nontaxable.' - '.$zero_rated; die();
            // $taxable = ($gross - $less_vat - $nontaxable - $zero_rated) / 1.12;
            // 1.12; binago din para sa adjustment of vat exempt equal to the receipt vat exempt
            // $taxable =   ($gross - $discounts - $less_vat - $nontaxable) / 1.12;
            $taxable =   ($gross - $less_vat - $nontaxable - $zero_rated - $discounts) / 1.12; //change computation conflict for zero rated 10 17 2018
            $total_net = ($taxable) + ($nontaxable+$zero_rated) + $tax + $local_tax;
            $add_gt = $taxable+$nontaxable+$zero_rated;
            $nsss = $taxable +  $nontaxable +  $zero_rated;

            #GENERAL
                $print_str .= append_chars(substrwords('TOTAL SALES',18,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(num($gross + $charges,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords(ucwords(strtoupper('SC/PWD VAT EXEMPT')),18,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars('-'.num($less_vat,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars('',"right",12," ").align_center('',PAPER_TOTAL_COL_2," ")
                                  .append_chars('----------',"left",PAPER_TOTAL_COL_2," ")."\r\n";

                $print_str .= append_chars(substrwords('GROSS SALES',18,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(num($gross + $charges - $less_vat,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";

                // $types = $trans_charges['types'];
                // $qty = 0;
                // foreach ($types as $code => $val) {
                //     $amount = $val['amount'];
                //     $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",PAPER_TOTAL_COL_1," ")
                //                          .append_chars('-'.num($amount,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                //     $qty += $val['qty'];
                // }
                $types = $trans_discounts['types'];
                $qty = 0;
                foreach ($types as $code => $val) {
                    if($code != 'DIPLOMAT'){
                        $amount = $val['amount'];
                        // if(MALL == 'megamall' && $code == PWDDISC){
                        //     $amount = $val['amount'] / 1.12;
                        // }
                        $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",PAPER_TOTAL_COL_1," ")
                                             .append_chars('-'.Num($amount,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                        $qty += $val['qty'];
                    }
                }
                $print_str .= append_chars('',"right",12," ").align_center('',PAPER_TOTAL_COL_2," ")
                                  .append_chars('----------',"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $net_sales = $gross + $charges - $discounts - $less_vat;
                $print_str .= append_chars(substrwords(ucwords(strtoupper('NET SALES')),18,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(num($net_sales,2),"left",PAPER_TOTAL_COL_2," ")."\r\n\r\n";
            #PAYMENTS
                $payments_types = $payments['types'];
                $payments_total = $payments['total'];
                $pay_qty = 0;
            #SUMMARY
                $final_gross = $gross;
                $vat_ = $taxable * .12;
                $print_str .= append_chars(substrwords('VAT SALES',23,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(numInt($taxable),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords('VAT',23,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(numInt($vat_),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords('VAT EXEMPT SALES',23,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(numInt($nontaxable),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                                         // .append_chars(numInt($nontaxable-$zero_rated),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords('ZERO RATED',23,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(numInt($zero_rated),"left",PAPER_TOTAL_COL_2," ")."\r\n\r\n";
                $print_str .= append_chars(substrwords('Payment Breakdown:',18,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
                              .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
                foreach ($payments_types as $code => $val) {
                    $print_str .= append_chars(substrwords(ucwords(strtoupper($code)),18,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                                  .append_chars(numInt($val['amount']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $pay_qty += $val['qty'];
                }
                $print_str .= append_chars('',"right",18," ").align_center('',PAPER_RD_COL_2," ")
                                  .append_chars('----------',"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= append_chars(substrwords('TOTAL PAYMENTS',18,""),"right",PAPER_RD_COL_1," ").align_center($pay_qty,PAPER_RD_COL_2," ")
                              .append_chars(numInt($payments_total),"left",PAPER_RD_COL_3_3," ")."\r\n\r\n";
                $print_str .= PAPER_LINE_SINGLE."\r\n";
                $gross_less_disc = $final_gross - $discounts - $less_vat;
                // $print_str .= append_chars(substrwords('NET SALES',23,""),"right",PAPER_TOTAL_COL_1," ")
                //                          // .append_chars(numInt(($taxable + $nontaxable + $zero_rated + $vat_)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                //                          .append_chars(numInt(($taxable + $nontaxable + $vat_)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                // $print_str .= PAPER_LINE."\r\n";

                if(count($payments['currency']) > 0){
                    $currency = $payments['currency'];
                    $print_str .= append_chars(substrwords('Currency Breakdown:',20,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
                                  .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
                    foreach ($currency as $code => $val) {
                        $print_str .= append_chars(substrwords(ucwords(strtoupper($code)),18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ").append_chars(numInt($val['amount']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                        // $pay_qty += $val['qty'];
                    }
                }

                // $print_str .= PAPER_LINE_SINGLE."\r\n";
                $print_str .= "\r\n\r\n";



                $print_str .= append_chars(substrwords('VOID SALES',18,""),"right",PAPER_TOTAL_COL_1," ")
                             .append_chars(num(($void),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords('CANCELLED TRANS',18,""),"right",PAPER_TOTAL_COL_1," ")
                             .append_chars(num(($cancelled),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";

                $cancelled_order = $this->cancelled_orders();
                $co = $cancelled_order['cancelled_order'];
                $print_str .= append_chars(substrwords('CANCELLED ORDERS',18,""),"right",PAPER_TOTAL_COL_1," ")
                             .append_chars(num(($co),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords('Local Tax',18,""),"right",PAPER_TOTAL_COL_1," ")
                             .append_chars($loc_txt,"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= "\r\n";
            #TRANS COUNT
                $types = $trans['types'];
                $types_total = array();
                $guestCount = 0;
                foreach ($types as $type => $tp) {
                    foreach ($tp as $id => $opt){
                        if(isset($types_total[$type])){
                            $types_total[$type] += round($opt->total_amount,2);

                        }
                        else{
                            $types_total[$type] = round($opt->total_amount,2);
                        }

                        // if($opt->type == 'dinein'){
                        //     $guestCount += $opt->guest;
                        // }
                        if($opt->guest == 0)
                            $guestCount += 1;
                        else
                            $guestCount += $opt->guest;
                    }
                }
                $print_str .= append_chars(substrwords('Trans Count:',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars('',"left",PAPER_RD_COL_3_3," ")."\r\n";
                $tc_total  = 0;
                $tc_qty = 0;
                foreach ($types_total as $typ => $tamnt) {
                    $print_str .= append_chars(substrwords($typ,18,""),"right",PAPER_RD_COL_1," ").align_center(count($types[$typ]),PAPER_RD_COL_2," ")
                                 .append_chars(numInt($tamnt),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $tc_total += $tamnt;
                    $tc_qty += count($types[$typ]);
                }
                $print_str .= "-----------------"."\r\n";
                $print_str .= append_chars(substrwords('TC Total',18,""),"right",PAPER_TOTAL_COL_1," ")
                             .append_chars(numInt($tc_total),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords('GUEST Total',18,""),"right",PAPER_TOTAL_COL_1," ")
                             .append_chars($guestCount,"left",PAPER_TOTAL_COL_2," ")."\r\n";
                // if($tc_total == 0 || $tc_qty == 0)
                //     $avg = 0;
                // else
                //     $avg = $tc_total/$tc_qty;
                if($net_sales){
                    if($guestCount == 0){
                        $avg = 0;
                    }else{
                        $avg = $net_sales/$guestCount;
                    }
                }else{
                    $avg = 0;
                }


                $print_str .= append_chars(substrwords('AVG Check',18,""),"right",PAPER_TOTAL_COL_1," ")
                             .append_chars(numInt($avg),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= "\r\n";
            #CHARGES
                $types = $trans_charges['types'];
                $qty = 0;
                $print_str .= append_chars(substrwords('Charges:',18,""),"right",18," ").align_center(null,5," ")
                              .append_chars(null,"left",13," ")."\r\n";
                foreach ($types as $code => $val) {
                    $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                                  .append_chars(numInt($val['amount']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $qty += $val['qty'];
                }
                $print_str .= "-----------------"."\r\n";
                $print_str .= append_chars(substrwords('Total Charges',18,""),"right",PAPER_RD_COL_1," ").align_center($qty,PAPER_RD_COL_2," ")
                              .append_chars(numInt($charges),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= "\r\n";
            #Discounts
                $types = $trans_discounts['types'];
                $qty = 0;
                $print_str .= append_chars(substrwords('Discounts:',18,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
                              .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
                foreach ($types as $code => $val) {
                    if($code != 'DIPLOMAT'){
                        $amount = $val['amount'];
                        // if(MALL == 'megamall' && $code == PWDDISC){
                        //     $amount = $val['amount'] / 1.12;
                        // }
                        $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                                      .append_chars(numInt($amount),"left",PAPER_RD_COL_3_3," ")."\r\n";
                        $qty += $val['qty'];
                    }
                }
                $print_str .= "-----------------"."\r\n";
                $print_str .= append_chars(substrwords('Total Discounts',18,""),"right",PAPER_RD_COL_1," ").align_center($qty,PAPER_RD_COL_2," ")
                              .append_chars(numInt($discounts),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= append_chars(substrwords('VAT EXEMPT',18,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(numInt($less_vat),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= "\r\n";
            #PAYMENTS
                $payments_types = $payments['types'];
                $payments_total = $payments['total'];
                $pay_qty = 0;
                $print_str .= append_chars(substrwords('Payment Breakdown:',18,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
                              .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
                foreach ($payments_types as $code => $val) {
                    $print_str .= append_chars(substrwords(ucwords(strtolower($code)),18,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                                  .append_chars(numInt($val['amount']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $pay_qty += $val['qty'];
                }
                $print_str .= "-----------------"."\r\n";
                $print_str .= append_chars(substrwords('Total Payments',18,""),"right",PAPER_RD_COL_1," ").align_center($pay_qty,PAPER_RD_COL_2," ")
                              .append_chars(numInt($payments_total),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= "\r\n";

                //card breakdown
                if($payments['cards']){
                    $cards = $payments['cards'];
                    $card_total = 0;
                    $count_total = 0;
                    $print_str .= append_chars(substrwords('Card Breakdown:',18,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
                              .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
                    foreach($cards as $key => $val){
                        $print_str .= append_chars(substrwords($key,18,""),"right",PAPER_RD_COL_1," ").align_center($val['count'],PAPER_RD_COL_2," ")
                                  .append_chars(numInt($val['amount']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                        $card_total += $val['amount'];
                        $count_total += $val['count'];
                    }
                    $print_str .= "-----------------"."\r\n";
                    $print_str .= append_chars(substrwords('Total',18,""),"right",PAPER_RD_COL_1," ").align_center($count_total,PAPER_RD_COL_2," ")
                              .append_chars(numInt($card_total),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    
                    $print_str .= "\r\n";
                }

                //get all gc with excess
                if($payments['gc_excess']){
                    $print_str .= append_chars(substrwords('GC EXCESS',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                                  .append_chars(numInt($payments['gc_excess']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $print_str .= "\r\n";
                }

                //show all sign chit
                // $trans['sales']
                if($trans['total_chit']){
                    $print_str .= append_chars(substrwords('TOTAL CHIT',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                                  .append_chars(numInt($trans['total_chit']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $print_str .= "\r\n";
                }
            #CATEGORIES
                $cats = $trans_menus['cats'];
                $print_str .= append_chars('Menu Categories:',"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars('',"left",PAPER_RD_COL_3," ")."\r\n";
                $qty = 0;
                $total = 0;
                foreach ($cats as $id => $val) {
                    if($val['qty'] > 0){
                        $print_str .= append_chars(substrwords($val['name'],18,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                                   .append_chars(numInt($val['amount']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                        $qty += $val['qty'];
                        $total += $val['amount'];
                    }
                 }
                $print_str .= "-----------------"."\r\n";
                $cat_total_qty = $qty;
                $print_str .= append_chars("SubTotal","right",PAPER_RD_COL_1," ").align_center($qty,PAPER_RD_COL_2," ")
                              .append_chars(numInt($total),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= append_chars("Modifiers Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                              .append_chars(numInt($trans_menus['mods_total']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                 $print_str .= append_chars("SubModifier Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                              .append_chars(numInt($trans_menus['submods_total']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                if($trans_menus['item_total'] > 0){
                 $print_str .= append_chars("Retail Items Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                               .append_chars(numInt($trans_menus['item_total']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                }

                $print_str .= append_chars("Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                              .append_chars(numInt($total+$trans_menus['mods_total']+$trans_menus['item_total']+$trans_menus['submods_total']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= "\r\n";
            #SUBCATEGORIES
                $subcats = $trans_menus['sub_cats'];
                // $print_str .= append_chars('Menu Subcategories:',"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                $print_str .= append_chars('Menu Types:',"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars('',"left",PAPER_RD_COL_3," ")."\r\n";
                $qty = 0;
                $total = 0;
                foreach ($subcats as $id => $val) {
                    $print_str .= append_chars($val['name'],"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                               .append_chars(numInt($val['amount']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $qty += $val['qty'];
                    $total += $val['amount'];
                 }
                $print_str .= "-----------------"."\r\n";
                $print_str .= append_chars("Total","right",PAPER_RD_COL_1," ").align_center($qty,PAPER_RD_COL_2," ")
                              .append_chars(numInt($total),"left",PAPER_RD_COL_3_3," ")."\r\n";
                // $print_str .= append_chars("Modifiers Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                //               .append_chars(numInt($trans_menus['mods_total']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                // $print_str .= append_chars("SubModifier Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                //               .append_chars(numInt($trans_menus['submods_total']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                // $print_str .= append_chars("Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                //               .append_chars(numInt($total+$trans_menus['mods_total']+$trans_menus['submods_total']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= "\r\n";
            #FREE MENUS
                $free = $trans_menus['free_menus'];
                $print_str .= append_chars('Free Menus:',"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars('',"left",PAPER_RD_COL_3," ")."\r\n";
                $fm = array();
                foreach ($free as $ms) {
                    if(!isset($fm[$ms->menu_id])){
                        $mn = array();
                        $mn['name'] = $ms->menu_name;
                        $mn['cat_id'] = $ms->cat_id;
                        $mn['qty'] = $ms->qty;
                        $mn['amount'] = $ms->sell_price * $ms->qty;
                        $mn['sell_price'] = $ms->sell_price;
                        $mn['code'] = $ms->menu_code;
                        // $mn['free_user_id'] = $ms->free_user_id;
                        $fm[$ms->menu_id] = $mn;
                    }
                    else{
                        $mn = $fm[$ms->menu_id];
                        $mn['qty'] += $ms->qty;
                        $mn['amount'] += $ms->sell_price * $ms->qty;
                        $fm[$ms->menu_id] = $mn;
                    }
                }
                $qty = 0;
                $total = 0;
                foreach ($fm as $menu_id => $val) {
                    $print_str .= append_chars($val['name'],"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                               .append_chars(($val['qty']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $qty += $val['qty'];
                    $total += $val['amount'];
                }
                $print_str .= "-----------------"."\r\n";
                $print_str .= append_chars("Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                              .append_chars(($qty),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= "\r\n";
                $print_str .= "\r\n";    
            #FOOTER
                $print_str .= append_chars(substrwords('Invoice Start: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars(iSetObj($trans['first_ref'],'trans_ref'),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= append_chars(substrwords('Invoice End: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars(iSetObj($trans['last_ref'],'trans_ref'),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= append_chars(substrwords('Invoice Ctr: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars($trans['ref_count'],"left",PAPER_RD_COL_3_3," ")."\r\n";
                if($title_name == "ZREAD"){
                    $gt = $this->old_grand_net_total($date);
                    $print_str .= "\r\n";
                    $print_str .= append_chars(substrwords('OLD GT: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                                 .append_chars(numInt( $gt['old_grand_total']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $print_str .= append_chars(substrwords('NEW GT: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                                 .append_chars( numInt($gt['old_grand_total']+$net_no_adds)  ,"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $print_str .= append_chars(substrwords('Z READ CTR: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                                 .append_chars( $gt['ctr'] ,"left",PAPER_RD_COL_3_3," ")."\r\n";
                }
                $print_str .= PAPER_LINE."\r\n";
            #MALLS
                if(MALL_ENABLED){
                    ####################################
                    # AYALA
                        if(MALL == 'ayala'){
                            
                            $branch_details = $this->setup_model->get_branch_details();
                            $branch = array();
                            foreach ($branch_details as $bv) {
                                $branch = array(
                                    'id' => $bv->branch_id,
                                    'res_id' => $bv->res_id,
                                    'branch_code' => $bv->branch_code,
                                    'name' => $bv->branch_name,
                                    'branch_desc' => $bv->branch_desc,
                                    'contact_no' => $bv->contact_no,
                                    'delivery_no' => $bv->delivery_no,
                                    'address' => $bv->address,
                                    'base_location' => $bv->base_location,
                                    'currency' => $bv->currency,
                                    'inactive' => $bv->inactive,
                                    'tin' => $bv->tin,
                                    'machine_no' => $bv->machine_no,
                                    'bir' => $bv->bir,
                                    'permit_no' => $bv->permit_no,
                                    'serial' => $bv->serial,
                                    'accrdn' => $bv->accrdn,
                                    'email' => $bv->email,
                                    'website' => $bv->website,
                                    'store_open' => $bv->store_open,
                                    'store_close' => $bv->store_close,
                                );
                            }


                            $print_str .= align_center("FOR AYALA",PAPER_WIDTH," ")."\r\n";
                            $print_str .= align_center($branch['name'],PAPER_WIDTH," ")."\r\n";
                            $print_str .= align_center($branch['address'],PAPER_WIDTH," ")."\r\n\r\n";
                            $print_str .= align_center("CONSOLIDATED REPORT Z-READ",PAPER_WIDTH," ")."\r\n\r\n";


                            $total_daily_sales = $total_vatA = $total_rawgrossA = $total_discount = $total_refund = $total_void = $total_charge = $total_non_tax = $total_trans_count = $total_guest = 0;


                            $paytype = array();
                            foreach ($payments_types as $code => $val) {
                                if($code != 'credit'){
                                    if(!isset($paytype[$code])){
                                        $paytype[$code] = array('amount'=>$val['amount']);
                                    }else{
                                        $row = $paytype[$code];
                                        $row['amount'] += $val['amount'];
                                        $paytype[$code] = $row;
                                    }
                                }
                                // $print_str .= append_chars(substrwords(ucwords(strtoupper($code)),12,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                                //               .append_chars(num($val['amount'],2),"left",PAPER_RD_COL_3_3," ")."\r\n";
                                // $pay_qty += $val['qty'];
                            }
                            $paycards = array();
                            if($payments['cards']){
                                $cards = $payments['cards'];
                                foreach($cards as $key => $val){
                                    if(!isset($paycards[$key])){
                                        $paycards[$key] = array('amount'=>$val['amount']);
                                    }else{
                                        $row = $paycards[$key];
                                        $row['amount'] += $val['amount'];
                                        $paycards[$key] = $row;
                                    }
                                }
                            }


                            // for server
                            $rawgrossA = numInt($gross + $charges + $void + $local_tax);
                            $vatA = numInt(($rawgrossA  - $discounts - $void  -  $charges - $nontaxable - $local_tax - numInt($less_vat)) * (1/9.333333));
                            $dlySaleA = numInt($rawgrossA - $discounts - $void - $charges - $vatA - $less_vat + $local_tax);
                            // $t_discounts = $discounts+$less_vat;
                            $rawgrossA =  $rawgrossA - $less_vat;
                            $t_discounts = $discounts;



                            $trans_count = 0;
                            $begor = 0;
                            $endor = 0;
                            $first_inv = array();
                            $last_inv = array();
                            $first_ref = 0;
                            $last_ref = 0;
                            $first_val = 0;
                            $last_val = 0;
                            $invs = array();
                            foreach ($trans['all_orders'] as $ord) {
                                if($ord->type_id == SALES_TRANS && $ord->trans_ref != ""){
                                    $ref = $ord->trans_ref;
                                    if (preg_match('/^(\D*?)(\d+)(.*)/', $ref, $result) == 1){
                                        // if($ord->inactive != 1){
                                            list($all, $prefix, $number, $postfix) = $result;
                                            $ref_val = intval($number);
                                            $invs[$ref_val] = array("ref"=>$ord->trans_ref,"val"=>$ref_val);
                                        // }
                                    }
                                }
                            }
                            ksort($invs);
                            // echo "<pre>",print_r($invs),"</pre>";die();
                            $first_inv = reset($invs);
                            $last_inv = end($invs);
                            if(count($first_inv) > 0){
                                $first_ref = $first_inv['ref'];
                                $first_val = $first_inv['val'];
                            }
                            if(count($last_inv) > 0){
                                $last_ref = $last_inv['ref'];
                                $last_val = $last_inv['val'];
                            }
                            if(count($invs) > 0){
                                $trans_count = ($last_val - $first_val) + 1; 
                            }

                            // echo $trans_count; die();
                            //add yun mga value ng server sa totals
                            $total_daily_sales += $dlySaleA;
                            $total_vatA += $vatA;
                            $total_rawgrossA += $rawgrossA;
                            $total_discount += $t_discounts;
                            $total_void += $void;
                            $total_charge += $charges;
                            $total_non_tax += $nontaxable;
                            // $total_trans_count += $tc_qty;
                            $total_trans_count += $trans_count;
                            $total_guest += $guestCount;
                             // echo $total_trans_count;

                            $terminals = $this->setup_model->get_terminals();


                            $print_str .= append_chars(substrwords('Daily Sales',25,""),"right",22," ").align_center('',2," ")
                                         .append_chars(num($total_daily_sales),"left",10," ")."\r\n";
                            $print_str .= append_chars(substrwords('Total Discount',25,""),"right",22," ").align_center('',2," ")
                                         .append_chars(num($total_discount),"left",10," ")."\r\n";
                            $print_str .= append_chars(substrwords('Total Refund',25,""),"right",22," ").align_center('',2," ")
                                         .append_chars(num(0),"left",10," ")."\r\n";
                            $print_str .= append_chars(substrwords('Total Void',25,""),"right",22," ").align_center('',2," ")
                                         .append_chars(num($total_void),"left",10," ")."\r\n";
                            $print_str .= append_chars(substrwords('Total Vat',25,""),"right",22," ").align_center('',2," ")
                                         .append_chars(num($total_vatA),"left",10," ")."\r\n";
                            $print_str .= append_chars(substrwords('Total Service Charge',25,""),"right",22," ").align_center('',2," ").append_chars(num($total_charge),"left",10," ")."\r\n";  


                            // $print_str .= append_chars(substrwords('Vatable Sales',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                            //              .append_chars(num($dlySaleA-$nontaxable),"left",PAPER_RD_COL_3_3," ")."\r\n";             
                            $print_str .= append_chars(substrwords('Total Non Taxable',22,""),"right",22," ").align_center('',2," ")
                                         .append_chars(num($total_non_tax),"left",10," ")."\r\n";
                            $print_str .= append_chars(substrwords('Row Gross',22,""),"right",22," ").align_center('',2," ")
                                         .append_chars(num($total_rawgrossA),"left",10," ")."\r\n";             
                            $print_str .= append_chars(substrwords('Transaction Count',22,""),"right",22," ").align_center('',2," ")
                                         .append_chars($total_trans_count,"left",10," ")."\r\n";
                            $print_str .= append_chars(substrwords('Customer Count',22,""),"right",22," ").align_center('',2," ")
                                         .append_chars($total_guest,"left",10," ")."\r\n";
                            foreach ($paytype as $k => $v) {
                                $print_str .= append_chars(strtoupper($k),"right",22," ").align_center('',2," ")
                                     .append_chars(num($v['amount']),"left",10," ")."\r\n";       
                            }
                            foreach ($paycards as $k => $v) {
                                $print_str .= append_chars(strtoupper($k),"right",22," ").align_center('',2," ")
                                     .append_chars(num($v['amount']),"left",10," ")."\r\n";       
                            }
                            $terminals = $this->setup_model->get_terminals();
                            // echo "<pre>",print_r($terminals),"</pre>";die();
                            foreach ($terminals as $k => $val) {
                                $print_str .= append_chars('BIR PERMIT '.$val->terminal_id,"right",15," ").align_center('',2," ")
                                     .append_chars($val->permit,"left",17," ")."\r\n";
                                $print_str .= append_chars('SERIAL NO. '.$val->terminal_id,"right",15," ").align_center('',2," ")
                                     .append_chars($val->serial,"left",17," ")."\r\n";
                            }           
                            // $print_str .= append_chars(substrwords('Less SC Disc',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                            //              .append_chars(num($discounts),"left",PAPER_RD_COL_3_3," ")."\r\n";             
                            // $print_str .= append_chars(substrwords('Vat Exempt',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                            //              .append_chars(num($less_vat),"left",PAPER_RD_COL_3_3," ")."\r\n";             
                            // $print_str .= append_chars(substrwords('Zero Rated',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                            //              .append_chars(num($zero_rated),"left",PAPER_RD_COL_3_3," ")."\r\n";             
                            // $print_str .= PAPER_LINE_SINGLE."\r\n";
                            // $print_str .= append_chars(substrwords('Net Sales',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                            //              .append_chars(num($dlySaleA+$vatA),"left",PAPER_RD_COL_3_3," ")."\r\n";             
                            // $print_str .= append_chars(substrwords('Total Qty Sold',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                            //              .append_chars(num($cat_total_qty),"left",PAPER_RD_COL_3_3," ")."\r\n";             
                            $print_str .= PAPER_LINE."\r\n";
                            $print_str .= align_center(sql2Date($post['from']),PAPER_WIDTH," ")."\r\n";
                            $print_str .= align_center("END OF REPORT",PAPER_WIDTH," ")."\r\n";


                        }
                    ####################################
                }    
            if ($return_print_str) {
                return $print_str;
            }
            // if($title_name == "ZREAD"){
            //     if($asJson == false){
            //         $this->manager_model->add_event_logs($user['id'],"ZREAD","View");    
            //     }else{
            //         $this->manager_model->add_event_logs($user['id'],"ZREAD","Print");                    
            //     } 
            // }elseif($title_name == "XREAD"){
            //     if($asJson == false){
            //         $this->manager_model->add_event_logs($user['id'],"XREAD","View");    
            //     }else{
            //         $this->manager_model->add_event_logs($user['id'],"XREAD","Print");                    
            //     } 
            // }else{
            //     if($asJson == false){
            //         $this->manager_model->add_event_logs($user['id'],"System Sales","View");    
            //     }else{
            //         $this->manager_model->add_event_logs($user['id'],"System Sales","Print");                    
            //     }
            // } 
            $this->session->set_userdata('pdf_data','<pre>'.$print_str.'</pre>') ; 
            // if(PRINT_VERSION && PRINT_VERSION == 'V2'){
            //     $this->do_print_v2($print_str,$asJson);  
            // }else if(PRINT_VERSION && PRINT_VERSION == 'V3' && $asJson){
            //     echo $this->html_print($print_str);
            // }else{
                $this->do_print($print_str,$asJson);
            // }
            // $this->do_print($print_str,$asJson);
    }
    //  public function excel_zread($sales_id=null,$noPrint=true){
    //     // echo "<pre>",print_r($sales_id),"</pre>";die();
    //     $this->load->library('Excel');
    //     $sheet = $this->excel->getActiveSheet();
    //     $filename = 'ZREAD Report';
    //     $styleHeaderCell = array(
    //                 'borders' => array(
    //                     'allborders' => array(
    //                         'style' => PHPExcel_Style_Border::BORDER_THIN
    //                     )
    //                 ),
    //                 'fill' => array(
    //                     'type' => PHPExcel_Style_Fill::FILL_SOLID,
    //                     'color' => array('rgb' => '3C8DBC')
    //                 ),
    //                 'alignment' => array(
    //                                     'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    //                 ),
    //                 'font' => array(
    //                     'bold' => true,
    //                     'size' => 14,
    //                     'color' => array('rgb' => 'FFFFFF'),
    //                 )
    //             );
    //             $styleNum = array(
    //                 'alignment' => array(
    //                                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
    //                 ),
    //             );
    //             $styleTxt = array(
    //                 'alignment' => array(
    //                                 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
    //                 ),
    //             );
    //             $styleTitle = array(
    //                 'alignment' => array(
    //                                     'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    //                 ),
    //                 'font' => array(
    //                     'bold' => true,
    //                     'size' => 16,
    //                 )
    //             );
    //     // ob_start();
    //     // ob_start();
    //     $date = $_GET['date'];
    //     // $user = $_GET['user'];
    //     $terminal_id = $_GET['terminal_id'];
    //     // $json = $_GET['json'];
    //     $dates = date2Sql($date);
    //     // $args["trans_sales.user_id"] = $_GET['user'];
    //     $args["trans_sales.type !="] = 'mgtfree';
    //     $d = $dates." 00:00:01";
    //     $details = $this->setup_model->get_branch_details();
            
    //     $open_time = $details[0]->store_open;
    //     $close_time = $details[0]->store_close;
    //     $to_date = date('Y-m-d', strtotime($dates . ' +1 day'));
    //     $print_date =$dates." ".$open_time. "  - ".$to_date." ".$close_time;
    //     // $print_date =$dates." ".$open_time. "  - ".$dates." ".$close_time;
    //     // if(CONSOLIDATOR){
    //     //     if($terminal_id != null){
    //     //         $args['trans_sales.terminal_id'] = $terminal_id;
    //     //     }
    //     // }else{
    //     //     $args['trans_sales.terminal_id'] = TERMINAL_ID;
    //     // }
    //          // $args["trans_sales.terminal_id"] = 1;
    //     $args["trans_sales.datetime  BETWEEN '".$dates." 00:00:01' AND '".$dates." 23:59:59'"] = array('use'=>'where','val'=>null,'third'=>false);
    //     // if(CONSOLIDATOR){

    //         $printss = $this->zread_sales_rep_backoffice_aurora(false,$args,true,$d,$print_date);
    //         // $printss = $this->print_sales_receipt_justin($sales_id,false,true,false,null,true,1,0,null,true);  
    //     // }else{

    //         // $printss = $this->print_sales_receipt_justin($sales_id,false,true,false,null,true,1,0,null,false); 
    //     // }
            

    //     header('Content-type: application/vnd.ms-excel');
    //     header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
    //     header('Cache-Control: max-age=0');

    //     echo $printss;
    // }
    public function excel_zread($sales_id=null,$noPrint=true){
        // echo "<pre>",print_r($sales_id),"</pre>";die();
        $this->load->library('Excel');
        $sheet = $this->excel->getActiveSheet();
        $filename = 'ZREAD Report';
        $styleHeaderCell = array(
                'borders' => array(
                    'allborders' => array(
                        // 'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
                'fill' => array(
                    // 'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    // 'color' => array('rgb' => '3C8DBC')
                ),
                'alignment' => array(
                                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ),
                'font' => array(
                    'bold' => false,
                    'size' => 12,
                    // 'color' => array('rgb' => 'FFFFFF'),
                )
            );
                $styleNum = array(
                    'alignment' => array(
                                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                    ),
                );
                $styleTxt = array(
                    'alignment' => array(
                                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    ),
                );
                $styleTitle = array(
                    'alignment' => array(
                                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    ),
                    'font' => array(
                        'bold' => true,
                        'size' => 16,
                    )
                );
        // ob_start();
        // ob_start();
        $date = $_GET['calendar'];
        // echo "<pre>",print_r($_GET),"</pre>";die();
        // $user = $_GET['user'];
        $terminal_id = $_GET['terminal_id'];
        $branch_code = $_GET['branch_id'];
        // $json = $_GET['json'];
        $dates = date2Sql($date);
        // $args["trans_sales.user_id"] = $_GET['user'];
        $args["trans_sales.type !="] = 'mgtfree';
        $date = $dates." 00:00:01";
        $to_date = date('Y-m-d', strtotime($date . ' +1 day'));
        $details = $this->setup_model->get_branch_details();
        $this->load->model('core/admin_model');
         
        $open_time = $details[0]->store_open;
        $close_time = $details[0]->store_close;
        $to_date = date('Y-m-d', strtotime($dates . ' +1 day'));
        $print_date =$dates." ".$open_time. "  - ".$to_date." ".$close_time;
        $this->admin_model->set_temp_trans_sales($branch_code,$date." ".$open_time,$to_date." ".$close_time);  
        // $print_date =$dates." ".$open_time. "  - ".$dates." ".$close_time;
        // if(CONSOLIDATOR){
        //     if($terminal_id != null){
        //         $args['trans_sales.terminal_id'] = $terminal_id;
        //     }
        // }else{
        //     $args['trans_sales.terminal_id'] = TERMINAL_ID;
        // }
             // $args["trans_sales.terminal_id"] = 1;
        $args["trans_sales.datetime  BETWEEN '".$dates." 00:00:01' AND '".$dates." 23:59:59'"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.branch_code  = '".$branch_code."' "] = array('use'=>'where','val'=>null,'third'=>false);
        // if(CONSOLIDATOR){
            // echo "<pre>",print_r($args),"</pre>";die();
            // $printss = $this->zread_sales_rep_backoffice_aurora(false,$args,true,$d,$print_date,$branch_code);
            // $printss = $this->print_sales_receipt_justin($sales_id,false,true,false,null,true,1,0,null,true);  
        // }else{

            // $printss = $this->print_sales_receipt_justin($sales_id,false,true,false,null,true,1,0,null,false); 
        // }
        ini_set('memory_limit', '-1');
        set_time_limit(3600);
        
        $print_str = $this->print_header($branch_code);
        $user = $this->session->userdata('user');
        $time = $this->site_model->get_db_now();
        $post = $this->set_post();
        $curr = $this->search_current();
        $trans = $this->trans_sales($args,$curr);
        // var_dump($trans['net']); die();
        $sales = $trans['sales'];
        $trans_menus = $this->menu_sales($sales['settled']['ids'],$curr,$branch_code);
        $trans_charges = $this->charges_sales($sales['settled']['ids'],$curr,$branch_code);
        $trans_discounts = $this->discounts_sales($sales['settled']['ids'],$curr,$branch_code);
        $tax_disc = $trans_discounts['tax_disc_total'];
        $no_tax_disc = $trans_discounts['no_tax_disc_total'];
        $trans_local_tax = $this->local_tax_sales($sales['settled']['ids'],$curr,$branch_code);
        $trans_tax = $this->tax_sales($sales['settled']['ids'],$curr,$branch_code);
        $trans_no_tax = $this->no_tax_sales($sales['settled']['ids'],$curr,$branch_code);
        $trans_zero_rated = $this->zero_rated_sales($sales['settled']['ids'],$curr,$branch_code);
        $payments = $this->payment_sales($sales['settled']['ids'],$curr,$branch_code);

        $gross = $trans_menus['gross'];
        
        $net = $trans['net'];
        $void = $trans['void'];
        $ewt1_count = $trans['ewt1_count'];
        $ewt2_count = $trans['ewt2_count'];
        $ewt5_count = $trans['ewt5_count'];
        $void_cnt = $trans['void_cnt'];
        $cancelled = $trans['cancel_amount'];
        $cancel_cnt = $trans['cancel_cnt'];
        $charges = $trans_charges['total'];
        $discounts = $trans_discounts['total'];
        $local_tax = $trans_local_tax['total'];
        $discounts = 0;
        $types = $trans_discounts['types'];
        foreach ($types as $code => $val) {
            if($code != 'DIPLOMAT'){
                $discounts += round($val['amount'],2);
            }
        }

        // echo $gross.' - '.$charges.' - '.$discounts.' - '.$net; die();
        $ewt = 0;
        $ewt = $ewt1_count + $ewt2_count + $ewt5_count;
        // $less_vat = round(($gross+ ( $charges+$local_tax)) - $discounts)- $ewt - $net;
        // $less_vat = round(($gross+ ( $charges+$local_tax) - $discounts)- $ewt - $net);

        $types = $trans_discounts['types'];
        $pwd = $snr =   $nopwdsnr = 0;
        foreach ($types as $code => $val) {
            if($code == 'SNDISC'){
                // $snr += round($val['amount'],2);
                $snr += $val['amount'];
            }

            if($code == 'PWDISC'){
                // $pwd += round($val['amount'],2);
                $pwd += $val['amount'];
            }

            if($code != 'PWDISC' && $code != 'SNDISC'){
                // $nopwdsnr += round($val['amount'],2);
                $nopwdsnr += $val['amount'];
            }
        }


        $zero_rated = $trans_zero_rated['total'];
        $less_vat_snrpwd = ($snr + $pwd) / 0.20;
        $less_vat = ($less_vat_snrpwd + $trans_zero_rated['total']) * 0.12;
        // $less_vat = (($gross+$charges+$local_tax) - $discounts) - $net;
        $less_vat = (($gross+$charges+$local_tax) - $discounts) - $ewt - $net; 
        $v_exempt =  $less_vat_snrpwd + $zero_rated;
        $tot_disc =  $less_vat_snrpwd + $zero_rated + $nopwdsnr;
        $vatexsales = $v_exempt*.12;
        $net_sales = (($gross + ($charges - $ewt1_count - $ewt2_count - $ewt5_count)) - $discounts) - $less_vat;

        if($less_vat < 0)
            $less_vat = 0;
        
        $tax = $trans_tax['total'];
        $no_tax = $trans_no_tax['total'];
        
        $no_tax -= $zero_rated;
        $loc_txt = numInt(($local_tax));
        $net_no_adds = $net-($charges+$local_tax);
        // $nontaxable = $no_tax;
        //binago 9/25/2018 for zreading adjustment of vat exempt equal to the receipt vat exempt
        $nontaxable = $no_tax - $no_tax_disc;
        // echo $gross.' - '.$less_vat.' - '.$nontaxable.' - '.$zero_rated.' - '.$discounts; die();
        // $taxable = ($gross - $less_vat - $nontaxable - $zero_rated) / 1.12;
        // 1.12; binago din para sa adjustment of vat exempt equal to the receipt vat exempt
        // $taxable =   ($gross - $discounts - $less_vat - $nontaxable) / 1.12;
        $taxable =   ($gross - $less_vat - $nontaxable - $zero_rated - $discounts) / 1.12; //change computation conflict for zero rated 10 17 2018
        $total_net = ($taxable) + ($nontaxable+$zero_rated) + $tax + $local_tax;
        $add_gt = $taxable+$nontaxable+$zero_rated;
        $nsss = $taxable +  $nontaxable +  $zero_rated;


        $final_gross = $gross;
        $vat_ = $taxable * .12;
        $vsales = round($vat_,2) /0.12;
        $vexsales = $tot_disc - $discounts;
        $nsales = $taxable + $vat_ + $vexsales;
        $new_nsales = $vsales + $tot_disc - $discounts;
        $lv = $v_exempt*.12;
        #GENERAL
            $title_name = "ZREAD";
        if($post['title'] != "")
            $title_name = $post['title'];
        $rc=1;
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(30);
        $this->load->model('dine/setup_model');
        $branch_details = $this->setup_model->get_branch_details($branch_code);
        $branch = array();
        foreach ($branch_details as $bv) {
            $branch = array(
                'id' => $bv->branch_id,
                'res_id' => $bv->res_id,
                'branch_code' => $bv->branch_code,
                'name' => $bv->branch_name,
                'branch_desc' => $bv->branch_desc,
                'contact_no' => $bv->contact_no,
                'delivery_no' => $bv->delivery_no,
                'address' => $bv->address,
                'base_location' => $bv->base_location,
                'currency' => $bv->currency,
                'inactive' => $bv->inactive,
                'tin' => $bv->tin,
                'machine_no' => $bv->machine_no,
                'bir' => $bv->bir,
                'permit_no' => $bv->permit_no,
                'serial' => $bv->serial,
                'accrdn' => $bv->accrdn,
                'email' => $bv->email,
                'website' => $bv->website,
                'store_open' => $bv->store_open,
                'store_close' => $bv->store_close,
            );
        }
        $user = $this->session->userdata('user');
        $wrap = wordwrap($branch['name'],35,"|#|");
        $exp = explode("|#|", $wrap);
        foreach ($exp as $v) {
            $sheet->mergeCells('A'.$rc.':C'.$rc);
            $sheet->getCell('A'.$rc)->setValue($v);
            $sheet->getStyle('A'.$rc)->applyFromArray($styleHeaderCell);
            $rc++;
        }
        $wrap = wordwrap($branch['address'],35,"|#|");
        $exp = explode("|#|", $wrap);
        foreach ($exp as $v) {
            $sheet->mergeCells('A'.$rc.':C'.$rc);
            $sheet->getCell('A'.$rc)->setValue($v);
            $sheet->getStyle('A'.$rc)->applyFromArray($styleHeaderCell);
            $rc++;
        }
        $sheet->mergeCells('A'.$rc.':C'.$rc);
        $sheet->getCell('A'.$rc)->setValue('TIN: '.$branch['tin']);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleHeaderCell);
        $rc++;
        $sheet->mergeCells('A'.$rc.':C'.$rc);
        $sheet->getCell('A'.$rc)->setValue('ACCRDN: '.$branch['accrdn']);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleHeaderCell);
        $rc++;
        $sheet->mergeCells('A'.$rc.':C'.$rc);
        $sheet->getCell('A'.$rc)->setValue('MIN: '.$branch['machine_no']);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleHeaderCell);
        $rc++;
        $sheet->mergeCells('A'.$rc.':C'.$rc);
        $sheet->getCell('A'.$rc)->setValue('PERMIT: '.$branch['permit_no']);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleHeaderCell);
        $rc++;
        // return $print_str;
        $sheet->mergeCells('A'.$rc.':C'.$rc);
        $sheet->getCell('A'.$rc)->setValue($title_name);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleHeaderCell);
        $rc++;
        $sheet->mergeCells('A'.$rc.':C'.$rc);
        $sheet->getCell('A'.$rc)->setValue("TERMINAL ".$post['terminal']);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleHeaderCell);
        $rc++;
        $sheet->mergeCells('A'.$rc.':C'.$rc);
        $time = $this->site_model->get_db_now();
        // echo $time;die();
        $sheet->getCell('A'.$rc)->setValue('Printed On :'.date2SqlDateTime($time));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleHeaderCell);
        $rc++;
        $sheet->mergeCells('A'.$rc.':C'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Printed BY :'.$user['full_name']);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleHeaderCell);
        $rc++;
        $sheet->mergeCells('A'.$rc.':C'.$rc);
        $sheet->getCell('A'.$rc)->setValue($print_date);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleHeaderCell);
        $rc++;
        if($post['employee'] != "All"){
            $sheet->mergeCells('A'.$rc.':C'.$rc);
            $sheet->getCell('A'.$rc)->setValue($post['employee']);
            $sheet->getStyle('A'.$rc)->applyFromArray($styleHeaderCell);
            $rc++;
        }


        $sheet->getCell('A'.$rc)->setValue('GROSS SALES');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue(num($gross,2));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        $rc++;
        $types = $trans_charges['types'];
        foreach ($types as $code => $val) {
            $amount = $val['amount'];
            $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtolower($val['name'])),18,""));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('C'.$rc)->setValue(num($amount,2));
            $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
            $rc++;
        }
        $types = $trans_discounts['types'];
        $qty = 0;
        foreach ($types as $code => $val) {
            if($code != 'DIPLOMAT'){
                $amount = $val['amount'];
                $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtolower($val['name'])),18,""));
                $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
                $sheet->getCell('C'.$rc)->setValue('-'.num($amount,2));
                $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
                $rc++;
            }
        }
        $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('SC/PWD VAT EXEMPT')),18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue('-'.num($less_vat,2));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        $rc++;

        if(EWT_DISCOUNT){
            $sheet->getCell('A'.$rc)->setValue(substrwords('EWT 1%',23,""));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('C'.$rc)->setValue(num($ewt1_count,2));
            $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
            $rc++;
            $sheet->getCell('A'.$rc)->setValue(substrwords('EWT 2%',23,""));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('C'.$rc)->setValue(num($ewt2_count,2));
            $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
            $rc++;
            $sheet->getCell('A'.$rc)->setValue(substrwords('EWT 5%',23,""));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('C'.$rc)->setValue(num($ewt5_count,2));
            $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
            $rc++;
        }
        $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('GROSS RECEIPTS')),18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue(num($net_sales,2));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        $rc++;
        $rc++;
        #PAYMENTS
        $payments_types = $payments['types'];
        $payments_total = $payments['total'];
        $pay_qty = 0;
        #SUMMARY
        $sheet->getCell('A'.$rc)->setValue(substrwords('VAT SALES',23,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue(num($taxable,2));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(substrwords('VAT',23,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue(num($vat_,2));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(substrwords('VAT EXEMPT SALES',23,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue(num($vexsales,2));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(substrwords('ZERO RATED',23,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue(num($zero_rated,2));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        $rc++;
        $rc++;
        if(READ_OTHER_MENU_ITEM_SALES){
            $netss = 0;
            $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('GROSS SALES')),18,""));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('C'.$rc)->setValue(num($nsales,2));
            $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
            $rc++;
            $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('VAT 12%')),18,""));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('C'.$rc)->setValue('-'.num($vat_,2));
            $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
            $rc++;
            $rc++;
            $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('NET SALES')),18,""));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('C'.$rc)->setValue(num($new_nsales,2));
            $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
            $rc++;
            $rc++;
            $netss = $nsales - $vat_;
        }else{
            $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('NET SALES')),18,""));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('C'.$rc)->setValue(num($nsales,2));
            $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
            $rc++;
            $rc++;
        }
        $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('Payment Breakdown:')),18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $rc++;
        $vcash = 0;
        foreach ($payments_types as $code => $val) {
            $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper($code)),18,""));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue($val['qty']);
            $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('C'.$rc)->setValue(num($val['amount'],2));
            $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
            $rc++;
            $vcash += $val['amount'];
            $pay_qty += $val['qty'];
        }
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(substrwords('TOTAL PAYMENTS',18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('B'.$rc)->setValue($pay_qty);
        $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue(num($vcash,2));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        $rc++;
        $gross_less_disc = $final_gross - $discounts - $less_vat;
        $rc++;
        if(count($payments['currency']) > 0){
            $currency = $payments['currency'];
            $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('Currency Breakdown:')),18,""));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $rc++;
            foreach ($currency as $code => $val) {
                $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper($code)),18,""));
                $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
                $sheet->getCell('C'.$rc)->setValue(num($val['amount'],2));
                $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
                $rc++;
            }
        }
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('VOID SALES')),18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue(num($void,2));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('VOID SALES COUNT')),18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue(num($void_cnt,2));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('CANCELLED TRANS')),18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue(num($cancelled,2));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('CANCELLED TRANS COUNT')),18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue(num($cancel_cnt,2));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        $rc++;
        $cancelled_order = $this->cancelled_orders($args,array(),$branch_code);
        $co = $cancelled_order['cancelled_order'];
        $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('CANCELLED ORDERS')),18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue(num($co,2));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('CANCELLED ORDER COUNT')),18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue(num($cancelled_order['cancel_count'],2));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('Local Tax')),18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue(num($loc_txt,2));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        $rc++;
        $rc++;
        #TRANS COUNT
        $types = $trans['types'];
        $types_total = array();
        $guestCount = 0;
        foreach ($types as $type => $tp) {
            foreach ($tp as $id => $opt){
                if(isset($types_total[$type])){
                    $types_total[$type] += round($opt->total_amount,2);

                }
                else{
                    $types_total[$type] = round($opt->total_amount,2);
                }
                if($opt->guest == 0)
                    $guestCount += 1;
                else
                    $guestCount += $opt->guest;
            }
        }
        $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('Trans Count:')),18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $rc++;
        $tc_total  = 0;
        $tc_qty = 0;
        foreach ($types_total as $typ => $tamnt) {
            $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper($typ)),18,""));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue(count($types[$typ]));
            $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('C'.$rc)->setValue(num($tamnt,2));
            $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
            $rc++;
            $tc_total += $tamnt;
            $tc_qty += count($types[$typ]);
        }
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('TC Total')),18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue(num($tc_total,2));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('GUEST Total')),18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue(num($guestCount,2));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        $rc++;

        if($net_sales){
            if($guestCount == 0){
                $avg = 0;
            }else{
                $avg = $net_sales/$guestCount;
            }
        }else{
            $avg = 0;
        }

        $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('AVG Check')),18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue(num($avg,2));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        $rc++;
        $rc++;

        #CHARGES
        $types = $trans_charges['types'];
        $qty = 0;
        $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('Charges:')),18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $rc++;
        foreach ($types as $code => $val) {
            $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper($val['name'])),18,""));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue($val['qty']);
            $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('C'.$rc)->setValue(num($val['amount'],2));
            $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
            $rc++;
            $qty += $val['qty'];
        }
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('Total Charges')),18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('B'.$rc)->setValue($qty);
        $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue(num($charges,2));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        $rc++;
        $rc++;
        #Discounts
        $types = $trans_discounts['types'];
        $qty = 0;
        $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('Discounts:')),18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $rc++;
        foreach ($types as $code => $val) {
            if($code != 'DIPLOMAT'){
                $amount = $val['amount'];
                $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper($val['name'])),18,""));
                $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
                $sheet->getCell('B'.$rc)->setValue($val['qty']);
                $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
                $sheet->getCell('C'.$rc)->setValue(num($amount,2));
                $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
                $rc++;
                $qty += $val['qty'];
            }
        }
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('Total Discounts')),18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('B'.$rc)->setValue($qty);
        $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue(num($discounts,2));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('VAT EXEMPT')),18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue(num($less_vat,2));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        $rc++;
        $rc++;
        #PAYMENTS
        $payments_types = $payments['types'];
        $payments_total = $payments['total'];
        $pay_qty = 0;
        $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('Payment Breakdown:')),18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $rc++;
        $vcash = 0;
        foreach ($payments_types as $code => $val) {
            $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper($code)),18,""));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue($val['qty']);
            $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('C'.$rc)->setValue(num($val['amount'],2));
            $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
            $rc++;
            $vcash += $val['amount'];
            $pay_qty += $val['qty'];
        }
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(substrwords('TOTAL PAYMENTS',18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('B'.$rc)->setValue($pay_qty);
        $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue(num($vcash,2));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        $rc++;
         //card breakdown
        if($payments['cards']){
            $cards = $payments['cards'];
            $card_total = 0;
            $count_total = 0;
            $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('Card Breakdown:')),18,""));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $rc++;
            foreach($cards as $key => $val){
                $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper($key)),18,""));
                $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
                $sheet->getCell('B'.$rc)->setValue($val['count']);
                $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
                $sheet->getCell('C'.$rc)->setValue(num($val['amount'],2));
                $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
                $rc++;
                $card_total += $val['amount'];
                $count_total += $val['count'];
            }
            $rc++;
            $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('Total')),18,""));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue($count_total);
            $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('C'.$rc)->setValue(num($card_total,2));
            $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
            $rc++;
        }

        //get all gc with excess
        if($payments['gc_excess']){
            $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('GC EXCESS')),18,""));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue($payments['gc_excess']);
            $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
            $rc++;
        }
        //show all sign chit
        // $trans['sales']
        if($trans['total_chit']){
            $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('TOTAL CHIT')),18,""));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue($trans['total_chit']);
            $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
            $rc++;
        }
        #CATEGORIES
        $rc++;
        $cats = $trans_menus['cats'];
        $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('Menu Categories:')),18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $rc++;
        $qty = 0;
        $total = 0;
        foreach ($cats as $id => $val) {
            if($val['qty'] > 0){
                $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper($val['name'])),18,""));
                $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
                $sheet->getCell('B'.$rc)->setValue($val['qty']);
                $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
                $sheet->getCell('C'.$rc)->setValue(num($val['amount'],2));
                $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
                $rc++;
                $qty += $val['qty'];
                $total += $val['amount'];
            }
         }
        $cat_total_qty = $qty;
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('SubTotal')),18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('B'.$rc)->setValue($qty);
        $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue(num($total,2));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('Modifiers Total')),18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue(num($trans_menus['mods_total'],2));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('SubModifier Total')),18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue(num($trans_menus['submods_total'],2));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        $rc++;
        if($trans_menus['item_total'] > 0){
            $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('Retail Items Total')),18,""));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('C'.$rc)->setValue(num($trans_menus['item_total'],2));
            $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
            $rc++;
        }
        
        $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('Total')),18,""));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue(num($total+$trans_menus['mods_total']+$trans_menus['item_total']+$trans_menus['submods_total'],2));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        $rc++;

        #SUBCATEGORIES
        $subcats = $trans_menus['sub_cats'];
        $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('Menu Types:')),18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $rc++;
        $qty = 0;
        $total = 0;
        foreach ($subcats as $id => $val) {

            $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper($val['name'])),18,""));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue($val['qty']);
            $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('C'.$rc)->setValue(num($val['amount'],2));
            $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
            $rc++;
            $qty += $val['qty'];
            $total += $val['amount'];
         }
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('Total')),18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('B'.$rc)->setValue($qty);
        $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue(num($total,2));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        $rc++;
        #FREE MENUS
        $free = $trans_menus['free_menus'];
        $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('Free Menus:')),18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $rc++;
        $fm = array();
        foreach ($free as $ms) {
            if(!isset($fm[$ms->menu_id])){
                $mn = array();
                $mn['name'] = $ms->menu_name;
                $mn['cat_id'] = $ms->cat_id;
                $mn['qty'] = $ms->qty;
                $mn['amount'] = $ms->sell_price * $ms->qty;
                $mn['sell_price'] = $ms->sell_price;
                $mn['code'] = $ms->menu_code;
                $fm[$ms->menu_id] = $mn;
            }
            else{
                $mn = $fm[$ms->menu_id];
                $mn['qty'] += $ms->qty;
                $mn['amount'] += $ms->sell_price * $ms->qty;
                $fm[$ms->menu_id] = $mn;
            }
        }
        $qty = 0;
        $total = 0;
        foreach ($fm as $menu_id => $val) {
            $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper($val['name'])),18,""));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue($val['qty']);
            $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('C'.$rc)->setValue(num($val['amount'],2));
            $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
            $rc++;
            $qty += $val['qty'];
            $total += $val['amount'];
        }
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('Total')),18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('B'.$rc)->setValue($qty);
        $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue(num($total,2));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        $rc++;
        #FOOTER
        $sheet->getCell('A'.$rc)->setValue(substrwords('Invoice Start: ',18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue(iSetObj($trans['first_ref'],'trans_ref'));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(substrwords('Invoice End: ',18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue(iSetObj($trans['last_ref'],'trans_ref'));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(substrwords('Invoice Ctr: ',18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue($trans['ref_count']);
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(substrwords('First Trans No.: ',18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue((int)iSetObj($trans['first_ref'],'trans_ref'));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(substrwords('Last Trans No.: ',18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue((int)iSetObj($trans['last_ref'],'trans_ref'));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        $rc++;
        if($title_name == "ZREAD"){
            $gt = $this->old_grand_net_total($date,false,$branch_code);
            // $print_str .= "\r\n";
            $sheet->getCell('A'.$rc)->setValue(substrwords('OLD GT: ',18,""));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('C'.$rc)->setValue($gt['old_grand_total']);
            $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
            $rc++;
            $sheet->getCell('A'.$rc)->setValue(substrwords('NEW GT: ',18,""));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('C'.$rc)->setValue($gt['old_grand_total']+$net_no_adds);
            $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
            $rc++;
            $sheet->getCell('A'.$rc)->setValue(substrwords('Z READ CTR:',18,""));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('C'.$rc)->setValue($gt['ctr']);
            $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
            $rc++;
        }
         $rc++;


        // header('Content-type: application/vnd.ms-excel');
        // header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        // header('Cache-Control: max-age=0');
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $objWriter->save('php://output');
        // echo $printss;
    }
    public function discs_rep(){
        $data = $this->syter->spawn('sales_rep');        
        $data['page_title'] = fa('fa-money')."Discounts Report";
        $data['code'] = DiscRep();
        $data['add_css'] = array('css/morris/morris.css','css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css');
        $data['add_js'] = array('js/plugins/morris/morris.min.js','js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js');
        $data['page_no_padding'] = false;
        $data['sideBarHide'] = false;
        $data['load_js'] = 'dine/prints';
        $data['use_js'] = 'DiscRepJS';
        $this->load->view('page',$data);
    }
    public function disc_rep_gen()
    {
        $this->load->model('dine/setup_model');
        
        // $this->load->model("dine/menu_model");
        
        $setup = $this->setup_model->get_details(BRANCH_CODE);
        $set = $setup[0];
        start_load(0);
        $disc_id = $this->input->post("disc_id");        
        $daterange = $this->input->post("calendar_range");        
        $dates = explode(" to ",$daterange);
        
        // if(CONSOLIDATOR){
        //     $this->menu_model->db = $this->load->database('main', TRUE);
        // }else{
            $this->cashier_model->db = $this->load->database('default', TRUE);
        // }
        $from = date2SqlDateTime($dates[0]);        
        $to = date2SqlDateTime(date('Y-m-d', strtotime($dates[1] . ' +1 day')). " ".$set->store_open);

        $terminal_id = null;
        // if(CONSOLIDATOR){
        //     $this->cashier_model->db = $this->load->database('main', TRUE);
        //     $terminal_id = $this->input->post("terminal_id");
        // }         


        $args = array();

        $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.inactive = 0"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.type != "] = 'mgtfree';
        if($disc_id){
            $args["trans_sales_discounts.disc_id"] =$disc_id;
        }
        // $args["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);

        // if(CONSOLIDATOR){
        //     if($terminal_id != null){
        //         $args['trans_sales.terminal_id'] = $terminal_id;
        //     }
        // }else{
        //     $args['trans_sales.terminal_id'] = TERMINAL_ID;
        // }

        $args_date["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);

        $trans = $this->cashier_model->get_trans_sales_discounts_backoffice(null, $args);  
        // echo $this->cashier_model->db->last_query(); die();
        // $trans = $this->menu_model->get_voided_cat_sales_rep($from, $to, $menu_cat_id);  
        // $trans_ret = $this->menu_model->get_voided_cat_sales_rep_retail($from, $to, "");  
        
        $trans_count = count($trans);
        // $trans_count_ret = count($trans_ret);
        $counter = 0;

        $this->make->sDiv();
            $this->make->sTable(array("id"=>"main-tbl", 'class'=>'table reportTBL sortable'));
                $this->make->sTableHead();
                    $this->make->sRow();
                        $this->make->th('Date');
                        $this->make->th('Name');
                        $this->make->th('Discount Name');
                        $this->make->th('ID No.');
                        $this->make->th('OR No.');
                        $this->make->th('Sales (Net of VAT)');
                        $this->make->th('Discounts');
                        // $this->make->th('Margin');
                    $this->make->eRow();
                $this->make->eTableHead();
                $this->make->sTableBody();
                    $tot_qty = 0;
                    $tot_vat_sales = 0;
                    $tot_vat = 0;
                    $tot_gross = 0;
                    $tot_sales_prcnt = 0;
                    $tot_cost = 0;
                    $tot_margin = 0;
                    $tot_amt = 0;
                    $tot_cost_prcnt = 0;
                    // foreach ($trans as $v) {
                    //     $tot_gross += $v->gross;
                    //     $tot_cost += $v->cost;
                    // }
                    foreach ($trans as $res) {
                        $this->make->sRow();
                            $this->make->td($res->tdate);
                            $this->make->td($res->name);
                            $this->make->td($res->disc_name);
                            $this->make->td($res->code);
                            $this->make->td($res->tref);
                            $this->make->td(num($res->total_amount), array("style"=>"text-align:right"));                                                    
                            $this->make->td(num($res->amount,2), array("style"=>"text-align:right"));                         
                        $this->make->eRow();

                        $tot_gross += $res->total_amount;
                        $tot_amt += $res->amount;

                        $counter++;
                        $progress = ($counter / $trans_count) * 100;
                        update_load(num($progress));

                    }    
                    $this->make->sRow();
                        $this->make->th('');
                        $this->make->th("", array("style"=>"text-align:right"));
                        $this->make->th("", array("style"=>"text-align:right"));
                        $this->make->th("", array("style"=>"text-align:right"));
                        $this->make->th("", array("style"=>"text-align:right"));
                        $this->make->th(num($tot_gross), array("style"=>"text-align:right"));
                        $this->make->th(num($tot_amt), array("style"=>"text-align:right"));                    
                    $this->make->eRow();                                 
                $this->make->eTableBody();
            $this->make->eTable();
        $this->make->eDiv();
        update_load(100);
        $code = $this->make->code();
        $json['code'] = $code;        
        $json['tbl_vals'] = $trans;
        $json['dates'] = $this->input->post('calendar_range');
        echo json_encode($json);
    }
    public function disc_rep_pdf()
    {
        // Include the main TCPDF library (search for installation path).
        require_once( APPPATH .'third_party/tcpdf.php');
        $this->load->model("dine/setup_model");
        date_default_timezone_set('Asia/Manila');

        // create new PDF document
        $pdf = new TCPDF("L", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('iPOS');
        $pdf->SetTitle('Discounts Report');
        $pdf->SetSubject('');
        $pdf->SetKeywords('');

        // set default header data
        $setup = $this->setup_model->get_details(BRANCH_CODE);
        $set = $setup[0];
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $set->branch_name, $set->address);

        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        // ---------------------------------------------------------
        $this->load->model('dine/setup_model');
        // $this->load->database('main', TRUE);
        // if(CONSOLIDATOR){
        //     $this->menu_model->db =
         $this->load->database('default', TRUE);
        // }
        // $this->load->model("dine/menu_model");
        start_load(0);

        // set font
        $pdf->SetFont('helvetica', 'B', 11);

        // add a page
        $pdf->AddPage();
        
        $disc_id = $_GET['disc_id'];        
        $daterange = $_GET['calendar_range'];        
        $dates = explode(" to ",$daterange);

        $from = date2SqlDateTime($dates[0]);        
        $to = date2SqlDateTime(date('Y-m-d', strtotime($dates[1] . ' +1 day')). " ".$set->store_open);
        $terminal_id = null;
        // if(CONSOLIDATOR){
        //     $this->cashier_model->db = $this->load->database('main', TRUE);
        //     $terminal_id = $_GET['terminal_id'];
        // }         


        $args = array();

        $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.inactive = 0"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.type != "] = 'mgtfree';
        if($disc_id){
            $args["trans_sales_discounts.disc_id"] =$disc_id;
        }
        // $args["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);

        // if(CONSOLIDATOR){
        //     if($terminal_id != null){
        //         $args['trans_sales.terminal_id'] = $terminal_id;
        //     }
        // }else{
        //     $args['trans_sales.terminal_id'] = TERMINAL_ID;
        // }

        $args_date["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);

        $trans = $this->cashier_model->get_trans_sales_discounts_backoffice(null, $args);  
        // $trans = $this->menu_model->get_voided_cat_sales_rep($from, $to, $menu_cat_id);
        // $trans_ret = $this->menu_model->get_voided_cat_sales_rep_retail($from, $to, "");        

        if($disc_id){
            $pdf->SetFont('helvetica', 'B', 24);
             $pdf->Write(0,$trans[0]->disc_name, '', 0, 'C', true, 0, false, false, 0);
            $pdf->SetLineStyle(array('width' => 0.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => 'black'));
            $pdf->Cell(267, 0, '', 'T', 0, 'C');
            $pdf->ln(0.9); 
        // $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $set->branch_name, $set->address);
            
        }    
        $pdf->SetFont('helvetica', 'B', 12);    
        $pdf->Write(0, 'Discounts Report', '', 0, 'L', true, 0, false, false, 0);
        $pdf->SetLineStyle(array('width' => 0.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => 'black'));
        $pdf->Cell(267, 0, '', 'T', 0, 'C');
        $pdf->ln(0.9);  
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Write(0, 'Report Period:    ', '', 0, 'L', false, 0, false, false, 0);
        $pdf->Write(0, $daterange, '', 0, 'L', false, 0, false, false, 0);
        $pdf->setX(200);
        $pdf->Write(0, 'Report Generated:    '.(new \DateTime())->format('Y-m-d H:i:s'), '', 0, 'L', true, 0, false, false, 0);
        $pdf->Write(0, 'Transaction Time:    ', '', 0, 'L', false, 0, false, false, 0);
        $pdf->setX(200);
        $user = $this->session->userdata('user');
        $pdf->Write(0, 'Generated by:    '.$user["full_name"], '', 0, 'L', true, 0, false, false, 0);        
        $pdf->ln(1);      
        $pdf->Cell(267, 0, '', 'T', 0, 'C');
        $pdf->ln();              

        // -----------------------------------------------------------------------------
        $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => 'black'));
        $pdf->Cell(40, 0, 'Date', 'B', 0, 'L');        
        $pdf->Cell(50, 0, 'Name', 'B', 0, 'L');            
        $pdf->Cell(50, 0, 'Discount Name', 'B', 0, 'L');            
        $pdf->Cell(30, 0, 'ID No.', 'B', 0, 'L');        
        $pdf->Cell(30, 0, 'OR No.', 'B', 0, 'L');        
        $pdf->Cell(30, 0, 'Sales (Net of VAT)', 'B', 0, 'R');        
        $pdf->Cell(30, 0, 'Discounts', 'B', 0, 'R');        
        // $pdf->Cell(32, 0, 'Margin', 'B', 0, 'R');        
        $pdf->ln();                  

        // GRAND TOTAL VARIABLES
        $tot_qty = 0;
        $tot_gross = 0;
        $tot_sales_prcnt = 0;
        $tot_cost = 0;
        $tot_cost_prcnt = 0; 
        $tot_margin = 0;
        $counter = 0;
        $progress = 0;
        $tot_amt = 0;
        $trans_count = count($trans);

        // foreach ($trans as $val) {
        //     $tot_gross += $val->gross;
        //     $tot_cost += $val->cost;
        // }

        foreach ($trans as $k => $v) {
            $pdf->Cell(40, 0, $v->tdate, '', 0, 'L');        
            $pdf->Cell(50, 0, $v->name, '', 0, 'L');        
            $pdf->Cell(50, 0, $v->disc_name, '', 0, 'L');        
            $pdf->Cell(30, 0, $v->code, '', 0, 'L');        
            $pdf->Cell(30, 0, $v->tref, '', 0, 'L');        
            $pdf->Cell(30, 0, num($v->total_amount), '', 0, 'R');          
            $pdf->Cell(30, 0, num($v->amount), '', 0, 'R');        
                 
            $pdf->ln();                

            $tot_gross += $v->total_amount;
            $tot_amt += $v->amount;

            $counter++;
            $progress = ($counter / $trans_count) * 100;
            update_load(num($progress));              
        }

        update_load(100);

        $pdf->Cell(40, 0, "", 'T', 0, 'L');        
        $pdf->Cell(50, 0, "", 'T', 0, 'L');           
        $pdf->Cell(50, 0, "", 'T', 0, 'L');        
        $pdf->Cell(30, 0, "", 'T', 0, 'L');        
        $pdf->Cell(30, 0, "", 'T', 0, 'L');        
        $pdf->Cell(30, 0, num($tot_gross), 'T', 0, 'R'); 
        $pdf->Cell(30, 0, num($tot_amt), 'T', 0, 'R'); 

        

        //Close and output PDF document
        $pdf->Output('disc_report.pdf', 'I');

        //============================================================+
        // END OF FILE
        //============================================================+   
    }
    public function disc_rep_excel()
    {
        // if(CONSOLIDATOR){
            $this->menu_model->db = $this->load->database('default', TRUE);
        // }
        $this->load->model("dine/menu_model");
        date_default_timezone_set('Asia/Manila');
        $this->load->library('Excel');
        $sheet = $this->excel->getActiveSheet();
        $filename = 'Discounts Report';
        $rc=1;
        #GET VALUES
        start_load(0);

        $setup = $this->setup_model->get_details(BRANCH_CODE);
        $set = $setup[0];

        update_load(10);
        sleep(1);
        
        $disc_id = $_GET['disc_id'];        
        $daterange = $_GET['calendar_range'];        
        $dates = explode(" to ",$daterange);

        $from = date2SqlDateTime($dates[0]);        
        $to = date2SqlDateTime(date('Y-m-d', strtotime($dates[1] . ' +1 day')). " ".$set->store_open);
        $terminal_id = null;
        // if(CONSOLIDATOR){
        //     $this->cashier_model->db = $this->load->database('main', TRUE);
        //     $terminal_id = $_GET['terminal_id'];
        // }         


        $args = array();

        $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.inactive = 0"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.type != "] = "mgtfree";
        if($disc_id){
            $args["trans_sales_discounts.disc_id"] =$disc_id;
        }
        // $args["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);

        // if(CONSOLIDATOR){
        //     if($terminal_id != null){
        //         $args['trans_sales.terminal_id'] = $terminal_id;
        //     }
        // }else{
        //     $args['trans_sales.terminal_id'] = TERMINAL_ID;
        // }

        $args_date["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);

        $trans = $this->cashier_model->get_trans_sales_discounts_backoffice(null, $args);  

        $styleHeaderCell = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '3C8DBC')
            ),
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'font' => array(
                'bold' => true,
                'size' => 12,
                'color' => array('rgb' => 'FFFFFF'),
            )
        );
        $styleNum = array(
            'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
            ),
        );
        $styleTxt = array(
            'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
        );
        $styleCenter = array(
            'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
        );
        $styleTitle = array(
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'font' => array(
                'bold' => true,
                'size' => 12,
            )
        );
        $styleBoldLeft = array(
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
            'font' => array(
                'bold' => true,
                'size' => 12,
            )
        );
        $styleBoldRight = array(
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
            ),
            'font' => array(
                'bold' => true,
                'size' => 12,
            )
        );
        $styleBoldCenter = array(
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'font' => array(
                'bold' => true,
                'size' => 12,
            )
        );
        
        $headers = array('Date', 'Name','ID No.','OR No.','Sales (Net of VAT)','Discounts','Discounts Name');
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(25);
        $sheet->getColumnDimension('F')->setWidth(25);
        $sheet->getColumnDimension('G')->setWidth(25);
        

        $sheet->mergeCells('A'.$rc.':H'.$rc);
        $sheet->getCell('A'.$rc)->setValue($set->branch_name);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        $rc++;

        $sheet->mergeCells('A'.$rc.':G'.$rc);
        $sheet->getCell('A'.$rc)->setValue($set->address);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        $rc++;

        if($disc_id){
            $styleBoldCenters = array(
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'font' => array(
                'bold' => true,
                'size' => 24,
            )
        );
            // $args["trans_sales_discounts.disc_id"] =$disc_id;
            $sheet->mergeCells('A'.$rc.':H'.$rc);
            $sheet->getCell('A'.$rc)->setValue($trans[0]->disc_name);
            $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldCenters);
            $rc++;
        }

        $sheet->mergeCells('A'.$rc.':G'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Discounts Report');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $rc++;

        $sheet->mergeCells('A'.$rc.':D'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Report Period: '.$daterange);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->mergeCells('E'.$rc.':G'.$rc);
        $sheet->getCell('E'.$rc)->setValue('Report Generated: '.(new \DateTime())->format('Y-m-d H:i:s'));
        $sheet->getStyle('E'.$rc)->applyFromArray($styleNum);
        $rc++;

        $sheet->mergeCells('A'.$rc.':D'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Transaction Time:');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $user = $this->session->userdata('user');
        $sheet->mergeCells('E'.$rc.':G'.$rc);
        $sheet->getCell('F'.$rc)->setValue('Generated by:    '.$user["full_name"]);
        $sheet->getStyle('F'.$rc)->applyFromArray($styleNum);
        $rc++;

        $col = 'A';
        foreach ($headers as $txt) {
            $sheet->getCell($col.$rc)->setValue($txt);
            $sheet->getStyle($col.$rc)->applyFromArray($styleHeaderCell);
            $col++;
        }
        $rc++;                          

        // GRAND TOTAL VARIABLES
        $tot_qty = 0;
        $tot_gross = 0;
        $tot_sales_prcnt = 0;
        $tot_cost = 0;
        $tot_cost_prcnt = 0; 
        $tot_margin = 0;
        $counter = 0;
        $progress = 0;
         $tot_amt = 0;
        $trans_count = count($trans);
        // foreach ($trans as $val) {
        //     $tot_gross += $val->gross;
        //     $tot_cost += $val->cost;
        //     $tot_margin += $val->gross - $val->cost;
        // }
        // foreach ($trans_mod as $vv) {
        //     $tot_mod_gross += $vv->mod_gross;
        // }

        foreach ($trans as $k => $v) {
            $sheet->getCell('A'.$rc)->setValue($v->tdate);
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue($v->name);
            $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('C'.$rc)->setValue($v->code);     
            $sheet->getStyle('C'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('D'.$rc)->setValue($v->tref);     
            $sheet->getStyle('D'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('E'.$rc)->setValue($v->total_amount);                     
            $sheet->getStyle('E'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('F'.$rc)->setValue($v->amount);     
            $sheet->getStyle('F'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('G'.$rc)->setValue($v->disc_name);     
            $sheet->getStyle('G'.$rc)->applyFromArray($styleTxt);      

            // Grand Total
            $tot_gross += $v->total_amount;
            $tot_amt += $v->amount;

            $counter++;
            $progress = ($counter / $trans_count) * 100;
            update_load(num($progress));   
            $rc++;           
        }

        
        $sheet->getCell('E'.$rc)->setValue($tot_gross);     
        $sheet->getStyle('E'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->getCell('F'.$rc)->setValue($tot_amt);     
        $sheet->getStyle('F'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $rc++; 

        
        update_load(100);        
       
        ob_end_clean();
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $objWriter->save('php://output');

        //============================================================+
        // END OF FILE
        //============================================================+   
    }
    public function no_tax_breakdown_sales($ids=array(),$curr=false,$terminal_id=null,$args=array()){
        $total_disc = 0;
        $discounts = array();
        $less_v = array();
        $disc_codes = array();
        $ids_used = array();
        $taxable_disc = 0;
        $non_taxable_disc = 0;
        $vat_exempt_total = 0;
        $sales_disc_count = 0;
        $les_v = 0;
        // $ids = 1;
        if(count($ids) > 0){
            $n_sales_discs = array();
            // if(CONSOLIDATOR){
            //     $this->cashier_model->db = $this->load->database('main', TRUE);
            //     if($terminal_id != null){
            //         $sales_discs = $this->cashier_model->get_active_receipt_discounts_no_tax(null,array("trans_sales_discounts.sales_id"=>$ids,'trans_sales_discounts.pos_id'=>$terminal_id));
            //         // $count_sales_sc = $this->cashier_model->get_trans_sales_discounts_sc(null,array("trans_sales_discounts.sales_id"=>$ids,'trans_sales_discounts.pos_id'=>$terminal_id));
            //     }else{

            //         $args["trans_sales.type_id = 10"] = array('use'=>'where','val'=>null,'third'=>false);
            //         $args["trans_sales.inactive = 0"] = array('use'=>'where','val'=>null,'third'=>false);
            //         $args["trans_sales_discounts.sales_id"] = array('use'=>'where_in','val'=>$ids,'third'=>false);

            //         // $sales_discs = $this->cashier_model->get_trans_sales_discounts(null,array("trans_sales_discounts.sales_id"=>$ids));
            //         $sales_discs = $this->cashier_model->get_active_receipt_discounts_no_tax(null,$args);
            //         // $count_sales_sc = $this->cashier_model->get_trans_sales_discounts_sc(null,array("trans_sales_discounts.sales_id"=>$ids));
            //         // $count_sales_sc = $this->cashier_model->get_trans_sales_discounts_sc(null,$args);
            //     }
            // }else{
                $this->cashier_model->db = $this->load->database('default', TRUE);
                $sales_discs = $this->cashier_model->get_active_receipt_discounts_no_tax(null,array("trans_sales_discounts.sales_id"=>$ids));
                // $count_sales_sc = $this->cashier_model->get_trans_sales_discounts_sc(null,array("trans_sales_discounts.sales_id"=>$ids));
                // echo $this->cashier_model->db->last_query();die();
            // }
            // $sales_disc_count = count($count_sales_sc);

            // echo "<pre>",print_r($sales_discs),"</pre>";die();

            $per_item = false;
            foreach ($sales_discs as $discs) {
                if($discs->items != ""){
                    $per_item = true;
                }
            }
            // var_dump($per_item); die();
            foreach ($sales_discs as $discs) {
                if($discs->disc_rate == 0){
                    
                    if($per_item){
                        if(!isset($disc_codes[$discs->disc_code])){

                            $where = array('sales_id'=>$discs->sales_id,'line_id'=>$discs->items);
                            $dd = $this->site_model->get_details($where,'trans_sales_menus');

                            $les_v = (($dd[0]->price*$dd[0]->qty)/1.12)*.12;
                            $disc_codes[$discs->disc_code] = array('name'=>$discs->disc_name,'qty'=> 1,'amount'=>round($les_v,2),'disc_rate'=> $discs->disc_rate);
                        }
                        else{
                            $where = array('sales_id'=>$discs->sales_id,'line_id'=>$discs->items);
                            $dd = $this->site_model->get_details($where,'trans_sales_menus');

                            $les_v = (($dd[0]->price*$dd[0]->qty)/1.12)*.12;
                            // $disc_codes[$discs->disc_code]['qty'] += 1;
                            $disc_codes[$discs->disc_code]['amount'] += round($les_v,2);
                            // $disc_codes[$discs->disc_code]['disc_rate'] += $discs->disc_rate;
                        }
                        
                    }else{
                        $tgross = $discs->total_amount;

                        $where = array('id'=>1);
                        $set_det = $this->site_model->get_details($where,'settings');

                        // if($counter['type'] != 'dinein' && $counter['type'] != 'mcb' && $dc->disc_code == 'SNDISC' && $divi > $set_det[0]->ceiling_amount && $set_det[0]->ceiling_amount > 0){
                        $ceiling_disc = false;
                        if($discs->disc_code == DISCOUNT_CEILING1 || $discs->disc_code == DISCOUNT_CEILING2){
                            $ceiling_disc = true;
                        }

                        if($ceiling_disc && $discs->total_amount > $set_det[0]->ceiling_amount && $set_det[0]->ceiling_amount > 0){
                            $tgross = $set_det[0]->ceiling_amount;
                        }

                        if(!isset($disc_codes[$discs->disc_code])){

                            $les_v = ($tgross/1.12)*.12;
                            $disc_codes[$discs->disc_code] = array('name'=>$discs->disc_name,'qty'=> 1,'amount'=>round($les_v,2),'disc_rate'=> $discs->disc_rate);
                        }
                        else{

                            $les_v = ($tgross/1.12)*.12;
                            // $disc_codes[$discs->disc_code]['qty'] += 1;
                            $disc_codes[$discs->disc_code]['amount'] += round($les_v,2);
                            // $disc_codes[$discs->disc_code]['disc_rate'] += $discs->disc_rate;
                        }
                    }


                }else{
                    if(!in_array($discs->sales_id, $ids_used)){
                        $ids_used[] = $discs->sales_id;
                    }
                    if(!isset($disc_codes[$discs->disc_code])){
                        // $les_v = (($discs->total_gross/$discs->guest)/1.12)*.12;
                        $drate = $discs->disc_rate / 100;
                        $les_v = ($discs->amount / $drate)*0.12;
                        $disc_codes[$discs->disc_code] = array('name'=>$discs->disc_name,'qty'=> 1,'amount'=>round($les_v,2),'disc_rate'=> $discs->disc_rate);
                    }
                    else{
                        // $les_v = (($discs->total_gross/$discs->guest)/1.12)*.12;
                        $les_v = ($discs->amount / $drate)*0.12;
                        // $disc_codes[$discs->disc_code]['qty'] += 1;
                        $disc_codes[$discs->disc_code]['amount'] += round($les_v,2);
                        // $disc_codes[$discs->disc_code]['disc_rate'] += $discs->disc_rate;
                    }
                    $total_disc += $discs->total_amount;
                    if($discs->no_tax == 1){
                        $non_taxable_disc += round($discs->total_amount,2);
                    }
                    else{
                        $taxable_disc += round($discs->total_amount,2);
                    }
                }
            }
        }
        // $discounts['total']=$total_disc;
        $less_v['types']=$disc_codes;
        // echo "<pre>",print_r($less_v),"</pre>";die();
        // $discounts['tax_disc_total']=$taxable_disc;
        // $discounts['no_tax_disc_total']=$non_taxable_disc;
        // $discounts['vat_exempt_total']=$vat_exempt_total;
        // $discounts['sales_disc_count']=$sales_disc_count;
        return $less_v;
    }
    public function set_pdf_data()
    {
       $this->session->set_userdata('pdf_data',$this->input->post('pdf_data')) ;
    } 

    public function sales_pdf_print()
    {

        $data = $this->session->userdata('pdf_data');
        // echo "<pre>", print_r($data), "</pre>"; die();
        // die();

        // include(dirname(__FILE__)."/cashier.php");
        // Include the main TCPDF library (search for installation path).
        require_once( APPPATH .'third_party/tcpdf.php');
        $this->load->model("dine/setup_model");
        date_default_timezone_set('Asia/Manila');

        // create new PDF document

        $pdf = new TCPDF("P", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('iPOS');
        $pdf->SetTitle('Sales Report');
        $pdf->SetSubject('');
        $pdf->SetKeywords('');

        // set default header data
        $setup = $this->setup_model->get_details(1);
        $set = $setup[0];

        // $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $set->branch_name, $set->address);


        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins

        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);


        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        $pdf->AddPage();



        $data = $this->session->userdata('pdf_data');

        $pdf->writeHTML($data, true, false, true, false, '');
        
        $pdf->Output('xread.pdf', 'I');
    }
     public function sales_pdf_printz()
    {

        $data = $this->session->userdata('pdf_data');
        // echo "<pre>", print_r($data), "</pre>"; die();
        // die();

        // include(dirname(__FILE__)."/cashier.php");
        // Include the main TCPDF library (search for installation path).
        require_once( APPPATH .'third_party/tcpdf.php');
        $this->load->model("dine/setup_model");
        date_default_timezone_set('Asia/Manila');

        // create new PDF document

        $pdf = new TCPDF("P", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('iPOS');
        $pdf->SetTitle('Sales Report');
        $pdf->SetSubject('');
        $pdf->SetKeywords('');

        // set default header data
        $setup = $this->setup_model->get_details(1);
        $set = $setup[0];

        // $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $set->branch_name, $set->address);


        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins

        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);


        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        $pdf->AddPage();



        $data = $this->session->userdata('pdf_data');

        $pdf->writeHTML($data, true, false, true, false, '');
        
        $pdf->Output('zread.pdf', 'I');
    }
     public function cancelled_orders($arr_post=array(),$arr_curr=array()){
            // $print_str = $this->print_header();
            $user = $this->session->userdata('user');
            $time = $this->site_model->get_db_now();
            $post = $this->set_post();
            $curr = $this->search_current();
            $trans = $this->trans_sales($arr_post);
            $branch_code = $post['branch_code'];
            // $trans = $this->trans_sales($post['args'],$curr);
            $sales = $trans['sales'];
            $trans_removes = $this->removed_menu_sales($trans['all_ids'],$curr,$branch_code);
            // echo print_r($trans_removes);die();
            $total_cancel = 0;
            $cancel_count = 0;
            if(count($trans_removes) > 0){
                foreach ($trans_removes as $v) {
                    
                    $exp = explode(' - ', $v['item']);
                    // $type = $rea['type'];
                    // $name = $exp[1];
                    // $qtys = explode(':', $exp[2]);
                    // $qty = $qtys[1];
                    if (array_key_exists(3,$exp)){
                        $price = $exp[3];
                    }
                    else{
                        $price = 0; 
                    }
                    $on_id = null;
                    $menuLine = explode(' ',$exp[0]);

                    $total_cancel += $price;
                    $cancel_count++;

                }
            }

            return array('cancelled_order'=>$total_cancel,'cancel_count'=>$cancel_count);


        }
    // public function cancelled_orders(){
    //         // $print_str = $this->print_header();
    //         $user = $this->session->userdata('user');
    //         $time = $this->site_model->get_db_now();
    //         $post = $this->set_post();
    //         $curr = $this->search_current();
    //         $trans = $this->trans_sales($post['args'],$curr);
    //         $sales = $trans['sales'];
    //         $trans_removes = $this->removed_menu_sales($trans['all_ids'],$curr);
    //         $total_cancel = 0;
    //         $cancel_count = 0;
    //         if(count($trans_removes) > 0){
    //             foreach ($trans_removes as $v) {
                    
    //                 $exp = explode(' - ', $v['item']);
    //                 // $type = $rea['type'];
    //                 // $name = $exp[1];
    //                 // $qtys = explode(':', $exp[2]);
    //                 // $qty = $qtys[1];
    //                 if (array_key_exists(3,$exp)){
    //                     $price = $exp[3];
    //                 }
    //                 else{
    //                     $price = 0; 
    //                 }
    //                 $on_id = null;
    //                 $menuLine = explode(' ',$exp[0]);

    //                 $total_cancel += $price;
    //                 $cancel_count++;

    //             }
    //         }

    //         return array('cancelled_order'=>$total_cancel,'cancel_count'=>$cancel_count);


    //     }
    public function zread_sales_rep_backoffice_aurora($asJson=false,$args=array(),$return_print_str=false,$date,$print_date){
        ////hapchan
            ini_set('memory_limit', '-1');
            set_time_limit(3600);
            
            $print_str = $this->print_header();
            $user = $this->session->userdata('user');
            $time = $this->site_model->get_db_now();
            $post = $this->set_post();
            $curr = $this->search_current();
            $trans = $this->trans_sales($args,$curr);
            // var_dump($post); die();
            $sales = $trans['sales'];
            $trans_menus = $this->menu_sales($sales['settled']['ids'],$curr,$post['branch_code']);
            $trans_charges = $this->charges_sales($sales['settled']['ids'],$curr,$post['branch_code']);
            $trans_discounts = $this->discounts_sales($sales['settled']['ids'],$curr,$post['branch_code']);
            $tax_disc = $trans_discounts['tax_disc_total'];
            $no_tax_disc = $trans_discounts['no_tax_disc_total'];
            $trans_local_tax = $this->local_tax_sales($sales['settled']['ids'],$curr,$post['branch_code']);
            $trans_tax = $this->tax_sales($sales['settled']['ids'],$curr,$post['branch_code']);
            $trans_no_tax = $this->no_tax_sales($sales['settled']['ids'],$curr,$post['branch_code']);
            $trans_zero_rated = $this->zero_rated_sales($sales['settled']['ids'],$curr,$post['branch_code']);
            $payments = $this->payment_sales($sales['settled']['ids'],$curr,$post['branch_code']);
            $gross = $trans_menus['gross'];
            
            $net = $trans['net'];
            $void = $trans['void'];
            $cancelled = $trans['cancel_amount'];
            $charges = $trans_charges['total'];
            $discounts = $trans_discounts['total'];
            $local_tax = $trans_local_tax['total'];
            // echo $gross.' - '.$charges.' - '.$discounts.' - '.$net; die();
            $less_vat = (($gross+$charges+$local_tax) - $discounts) - $net;
            // $less_vat = $trans_discounts['vat_exempt_total'];

            // echo $gross.'+'.$charges.'+'.$local_tax.' - '.$discounts.' - '.$net;
            // die();

            if($less_vat < 0)
                $less_vat = 0;
            // var_dump($less_vat);

            //para mag tugmam yun payments and netsale
            // $net_sales2 = $gross + $charges - $discounts - $less_vat;
            // $diffs = $net_sales2 - $payments['total'];
            // if($diffs < 1){
            //     $less_vat = $less_vat + $diffs;
            // }
            

            $tax = $trans_tax['total'];
            $no_tax = $trans_no_tax['total'];
            $zero_rated = $trans_zero_rated['total'];
            $no_tax -= $zero_rated;

            $title_name = "SYSTEM SALES REPORT";
            if($post['title'] != "")
                $title_name = $post['title'];

            $print_str .= align_center($title_name,PAPER_WIDTH," ")."\r\n";
            $print_str .= align_center("TERMINAL ".$post['terminal'],PAPER_WIDTH," ")."\r\n";
            $print_str .= append_chars('Printed On','right',11," ").append_chars(": ".date2SqlDateTime($time),'right',19," ")."\r\n";
            $print_str .= append_chars('Printed BY','right',11," ").append_chars(": ".$user['full_name'],'right',19," ")."\r\n";
            $print_str .= PAPER_LINE."\r\n";
            $print_str .= align_center(sql2DateTime($post['from'])." - ".sql2DateTime($post['to']),PAPER_WIDTH," ")."\r\n";
            if($post['employee'] != "All")
                $print_str .= align_center($post['employee'],PAPER_WIDTH," ")."\r\n";
            $print_str .= PAPER_LINE."\r\n";

            $loc_txt = numInt(($local_tax));
            $net_no_adds = $net-($charges+$local_tax);
            // $nontaxable = $no_tax;
            //binago 9/25/2018 for zreading adjustment of vat exempt equal to the receipt vat exempt
            $nontaxable = $no_tax - $no_tax_disc;
            // echo $gross.' - '.$less_vat.' - '.$nontaxable.' - '.$zero_rated; die();
            // $taxable = ($gross - $less_vat - $nontaxable - $zero_rated) / 1.12;
            // 1.12; binago din para sa adjustment of vat exempt equal to the receipt vat exempt
            // $taxable =   ($gross - $discounts - $less_vat - $nontaxable) / 1.12;
            $taxable =   ($gross - $less_vat - $nontaxable - $zero_rated - $discounts) / 1.12; //change computation conflict for zero rated 10 17 2018
            $total_net = ($taxable) + ($nontaxable+$zero_rated) + $tax + $local_tax;
            $add_gt = $taxable+$nontaxable+$zero_rated;
            $nsss = $taxable +  $nontaxable +  $zero_rated;

            #GENERAL
                $print_str .= append_chars(substrwords('TOTAL SALES',18,""),"right",21," ")
                                         .append_chars(num($gross + $charges,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords(ucwords(strtoupper('SC/PWD VAT EXEMPT')),18,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars('-'.num($less_vat,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars('',"right",12," ").align_center('',PAPER_TOTAL_COL_2," ")
                                  .append_chars('----------',"left",PAPER_TOTAL_COL_2," ")."\r\n";

                $print_str .= append_chars(substrwords('GROSS SALES',18,""),"right",21," ")
                                         .append_chars(num($gross + $charges - $less_vat,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";

                // $types = $trans_charges['types'];
                // $qty = 0;
                // foreach ($types as $code => $val) {
                //     $amount = $val['amount'];
                //     $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",PAPER_TOTAL_COL_1," ")
                //                          .append_chars('-'.num($amount,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                //     $qty += $val['qty'];
                // }
                $types = $trans_discounts['types'];
                $qty = 0;
                foreach ($types as $code => $val) {
                    if($code != 'DIPLOMAT'){
                        $amount = $val['amount'];
                        // if(MALL == 'megamall' && $code == PWDDISC){
                        //     $amount = $val['amount'] / 1.12;
                        // }
                        $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",21," ")
                                             .append_chars('-'.Num($amount,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                        $qty += $val['qty'];
                    }
                }
                $print_str .= append_chars('',"right",12," ").align_center('',PAPER_TOTAL_COL_2," ")
                                  .append_chars('----------',"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $net_sales = $gross + $charges - $discounts - $less_vat;
                $print_str .= append_chars(substrwords(ucwords(strtoupper('NET SALES')),18,""),"right",21," ")
                                         .append_chars(num($net_sales,2),"left",PAPER_TOTAL_COL_2," ")."\r\n\r\n";
            #PAYMENTS
                $payments_types = $payments['types'];
                $payments_total = $payments['total'];
                $pay_qty = 0;
            #SUMMARY
                $final_gross = $gross;
                $vat_ = $taxable * .12;
                $print_str .= append_chars(substrwords('VAT SALES',23,""),"right",21," ")
                                         .append_chars(num($taxable,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords('VAT',23,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(num($vat_,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                // if(IS_VATABLE_STORE){
                    $print_str .= append_chars(substrwords('VAT EXEMPT SALES',23,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(num($nontaxable,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";

                // }else{
                //     $print_str .= append_chars(substrwords('NONVAT SALES',23,""),"right",PAPER_TOTAL_COL_1," ")
                //                          .append_chars(num($nontaxable,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                // }
                                         // .append_chars(numInt($nontaxable-$zero_rated),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords('ZERO RATED',23,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(num($zero_rated,2),"left",PAPER_TOTAL_COL_2," ")."\r\n\r\n";
                $print_str .= append_chars(substrwords('Payment Breakdown:',18,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
                              .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
                foreach ($payments_types as $code => $val) {
                    $print_str .= append_chars(substrwords(ucwords(strtoupper($code)),18,""),"right",12," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                                  .append_chars(num($val['amount'],2),"left",16," ")."\r\n";
                    $pay_qty += $val['qty'];
                }
                $print_str .= append_chars('',"right",18," ").align_center('',PAPER_RD_COL_2," ")
                                  .append_chars('----------',"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= append_chars(substrwords('TOTAL PAYMENTS',18,""),"right",14," ").align_center($pay_qty,PAPER_RD_COL_2," ")
                              .append_chars(num($payments_total,2),"left",14," ")."\r\n\r\n";
                $print_str .= PAPER_LINE_SINGLE."\r\n";
                $gross_less_disc = $final_gross - $discounts - $less_vat;
                // $print_str .= append_chars(substrwords('NET SALES',23,""),"right",PAPER_TOTAL_COL_1," ")
                //                          // .append_chars(numInt(($taxable + $nontaxable + $zero_rated + $vat_)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                //                          .append_chars(numInt(($taxable + $nontaxable + $vat_)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                // $print_str .= PAPER_LINE."\r\n";

                if(count($payments['currency']) > 0){
                    $currency = $payments['currency'];
                    $print_str .= append_chars(substrwords('Currency Breakdown:',20,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
                                  .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
                    foreach ($currency as $code => $val) {
                        $print_str .= append_chars(substrwords(ucwords(strtoupper($code)),18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ").append_chars(num($val['amount'],2),"left",PAPER_RD_COL_3_3," ")."\r\n";
                        // $pay_qty += $val['qty'];
                    }
                }

                // $print_str .= PAPER_LINE_SINGLE."\r\n";
                $print_str .= "\r\n\r\n";



                $print_str .= append_chars(substrwords('VOID SALES',18,""),"right",PAPER_TOTAL_COL_1," ")
                             .append_chars(num(($void),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords('CANCELLED TRANS',18,""),"right",PAPER_TOTAL_COL_1," ")
                             .append_chars(num(($cancelled),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";

                $cancelled_order = $this->cancelled_orders();
                $co = $cancelled_order['cancelled_order'];
                $print_str .= append_chars(substrwords('CANCELLED ORDERS',18,""),"right",PAPER_TOTAL_COL_1," ")
                             .append_chars(num(($co),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords('Local Tax',18,""),"right",PAPER_TOTAL_COL_1," ")
                             .append_chars($loc_txt,"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= "\r\n";
            #TRANS COUNT
                $types = $trans['types'];
                $types_total = array();
                $guestCount = 0;
                foreach ($types as $type => $tp) {
                    foreach ($tp as $id => $opt){
                        if(isset($types_total[$type])){
                            $types_total[$type] += round($opt->total_amount,2);

                        }
                        else{
                            $types_total[$type] = round($opt->total_amount,2);
                        }

                        // if($opt->type == 'dinein'){
                        //     $guestCount += $opt->guest;
                        // }
                        if($opt->guest == 0)
                            $guestCount += 1;
                        else
                            $guestCount += $opt->guest;
                    }
                }
                $print_str .= append_chars(substrwords('Trans Count:',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars('',"left",PAPER_RD_COL_3_3," ")."\r\n";
                $tc_total  = 0;
                $tc_qty = 0;
                foreach ($types_total as $typ => $tamnt) {
                    $print_str .= append_chars(substrwords($typ,18,""),"right",12," ").align_center(count($types[$typ]),PAPER_RD_COL_2," ")
                                 .append_chars(num($tamnt,2),"left",16," ")."\r\n";
                    $tc_total += $tamnt;
                    $tc_qty += count($types[$typ]);
                }
                $print_str .= "-----------------"."\r\n";
                $print_str .= append_chars(substrwords('TC Total',18,""),"right",21," ")
                             .append_chars(num($tc_total,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords('GUEST Total',18,""),"right",PAPER_TOTAL_COL_1," ")
                             .append_chars($guestCount,"left",PAPER_TOTAL_COL_2," ")."\r\n";
                // if($tc_total == 0 || $tc_qty == 0)
                //     $avg = 0;
                // else
                //     $avg = $tc_total/$tc_qty;
                if($net_sales){
                    if($guestCount == 0){
                        $avg = 0;
                    }else{
                        $avg = $net_sales/$guestCount;
                    }
                }else{
                    $avg = 0;
                }


                $print_str .= append_chars(substrwords('AVG Check',18,""),"right",PAPER_TOTAL_COL_1," ")
                             .append_chars(num($avg,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= "\r\n";
            #CHARGES
                $types = $trans_charges['types'];
                $qty = 0;
                $print_str .= append_chars(substrwords('Charges:',18,""),"right",18," ").align_center(null,5," ")
                              .append_chars(null,"left",13," ")."\r\n";
                foreach ($types as $code => $val) {
                    $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                                  .append_chars(num($val['amount'],2),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $qty += $val['qty'];
                }
                $print_str .= "-----------------"."\r\n";
                $print_str .= append_chars(substrwords('Total Charges',18,""),"right",PAPER_RD_COL_1," ").align_center($qty,PAPER_RD_COL_2," ")
                              .append_chars(num($charges,2),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= "\r\n";
            #Discounts
                $types = $trans_discounts['types'];
                $qty = 0;
                $print_str .= append_chars(substrwords('Discounts:',18,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
                              .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
                foreach ($types as $code => $val) {
                    if($code != 'DIPLOMAT'){
                        $amount = $val['amount'];
                        // if(MALL == 'megamall' && $code == PWDDISC){
                        //     $amount = $val['amount'] / 1.12;
                        // }
                        $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",14," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                                      .append_chars(num($amount,2),"left",14," ")."\r\n";
                        $qty += $val['qty'];
                    }
                }
                $print_str .= "-----------------"."\r\n";
                $print_str .= append_chars(substrwords('Total Discounts',18,""),"right",14," ").align_center($qty,PAPER_RD_COL_2," ")
                              .append_chars(num($discounts,2),"left",13," ")."\r\n";
                $print_str .= append_chars(substrwords('VAT EXEMPT',18,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(num($less_vat,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= "\r\n";
            #PAYMENTS
                $payments_types = $payments['types'];
                $payments_total = $payments['total'];
                $pay_qty = 0;
                $print_str .= append_chars(substrwords('Payment Breakdown:',18,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
                              .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
                foreach ($payments_types as $code => $val) {
                    $print_str .= append_chars(substrwords(ucwords(strtolower($code)),18,""),"right",12," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                                  .append_chars(num($val['amount'],2),"left",16," ")."\r\n";
                    $pay_qty += $val['qty'];
                }
                $print_str .= "-----------------"."\r\n";
                $print_str .= append_chars(substrwords('Total Payments',18,""),"right",14," ").align_center($pay_qty,PAPER_RD_COL_2," ")
                              .append_chars(num($payments_total,2),"left",14," ")."\r\n";
                $print_str .= "\r\n";

                //card breakdown
                if($payments['cards']){
                    $cards = $payments['cards'];
                    $card_total = 0;
                    $count_total = 0;
                    $print_str .= append_chars(substrwords('Card Breakdown:',18,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
                              .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
                    foreach($cards as $key => $val){
                        $print_str .= append_chars(substrwords($key,18,""),"right",12," ").align_center($val['count'],PAPER_RD_COL_2," ")
                                  .append_chars(num($val['amount'],2),"left",16," ")."\r\n";
                        $card_total += $val['amount'];
                        $count_total += $val['count'];
                    }
                    $print_str .= "-----------------"."\r\n";
                    $print_str .= append_chars(substrwords('Total',18,""),"right",12," ").align_center($count_total,PAPER_RD_COL_2," ")
                              .append_chars(num($card_total,2),"left",16," ")."\r\n";
                    
                    $print_str .= "\r\n";
                }

                //get all gc with excess
                if($payments['gc_excess']){
                    $print_str .= append_chars(substrwords('GC EXCESS',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                                  .append_chars(num($payments['gc_excess'],2),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $print_str .= "\r\n";
                }

                //show all sign chit
                // $trans['sales']
                if($trans['total_chit']){
                    $print_str .= append_chars(substrwords('TOTAL CHIT',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                                  .append_chars(num($trans['total_chit'],2),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $print_str .= "\r\n";
                }
            #CATEGORIES
                $cats = $trans_menus['cats'];
                $print_str .= append_chars('Menu Categories:',"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars('',"left",PAPER_RD_COL_3," ")."\r\n";
                $qty = 0;
                $total = 0;
                foreach ($cats as $id => $val) {
                    if($val['qty'] > 0){
                        $print_str .= append_chars(substrwords($val['name'],18,""),"right",15," ").align_center($val['qty'],5," ")
                                   .append_chars(num($val['amount'],2),"left",13," ")."\r\n";
                        $qty += $val['qty'];
                        $total += $val['amount'];
                    }
                 }
                $print_str .= "-----------------"."\r\n";
                $cat_total_qty = $qty;
                $print_str .= append_chars("SubTotal","right",12," ").align_center($qty,PAPER_RD_COL_2," ")
                              .append_chars(num($total,2),"left",16," ")."\r\n";
                $print_str .= append_chars("Modifiers Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                              .append_chars(num($trans_menus['mods_total'],2),"left",PAPER_RD_COL_3_3," ")."\r\n";
                 $print_str .= append_chars("SubModifier Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                              .append_chars(num($trans_menus['submods_total'],2),"left",PAPER_RD_COL_3_3," ")."\r\n";
                if($trans_menus['item_total'] > 0){
                 $print_str .= append_chars("Retail Items Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                               .append_chars(num($trans_menus['item_total'],2),"left",PAPER_RD_COL_3_3," ")."\r\n";
                }

                $print_str .= append_chars("Total","right",12," ").align_center('',PAPER_RD_COL_2," ")
                              .append_chars(num($total+$trans_menus['mods_total']+$trans_menus['item_total']+$trans_menus['submods_total'],2),"left",16," ")."\r\n";
                $print_str .= "\r\n";
            #SUBCATEGORIES
                $subcats = $trans_menus['sub_cats'];
                // $print_str .= append_chars('Menu Subcategories:',"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                $print_str .= append_chars('Menu Types:',"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars('',"left",PAPER_RD_COL_3," ")."\r\n";
                $qty = 0;
                $total = 0;
                foreach ($subcats as $id => $val) {
                    $print_str .= append_chars($val['name'],"right",12," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                               .append_chars(num($val['amount'],2),"left",16," ")."\r\n";
                    $qty += $val['qty'];
                    $total += $val['amount'];
                 }
                $print_str .= "-----------------"."\r\n";
                $print_str .= append_chars("Total","right",12," ").align_center($qty,PAPER_RD_COL_2," ")
                              .append_chars(num($total,2),"left",16," ")."\r\n";
                // $print_str .= append_chars("Modifiers Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                //               .append_chars(numInt($trans_menus['mods_total']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                // $print_str .= append_chars("SubModifier Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                //               .append_chars(numInt($trans_menus['submods_total']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                // $print_str .= append_chars("Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                //               .append_chars(numInt($total+$trans_menus['mods_total']+$trans_menus['submods_total']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= "\r\n";
            #FREE MENUS
                $free = $trans_menus['free_menus'];
                $print_str .= append_chars('Free Menus:',"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars('',"left",PAPER_RD_COL_3," ")."\r\n";
                $fm = array();
                foreach ($free as $ms) {
                    if(!isset($fm[$ms->menu_id])){
                        $mn = array();
                        $mn['name'] = $ms->menu_name;
                        $mn['cat_id'] = $ms->cat_id;
                        $mn['qty'] = $ms->qty;
                        $mn['amount'] = $ms->sell_price * $ms->qty;
                        $mn['sell_price'] = $ms->sell_price;
                        $mn['code'] = $ms->menu_code;
                        // $mn['free_user_id'] = $ms->free_user_id;
                        $fm[$ms->menu_id] = $mn;
                    }
                    else{
                        $mn = $fm[$ms->menu_id];
                        $mn['qty'] += $ms->qty;
                        $mn['amount'] += $ms->sell_price * $ms->qty;
                        $fm[$ms->menu_id] = $mn;
                    }
                }
                $qty = 0;
                $total = 0;
                foreach ($fm as $menu_id => $val) {
                    $print_str .= append_chars($val['name'],"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                               .append_chars(($val['qty']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $qty += $val['qty'];
                    $total += $val['amount'];
                }
                $print_str .= "-----------------"."\r\n";
                $print_str .= append_chars("Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                              .append_chars(($qty),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= "\r\n";
                $print_str .= "\r\n";    
            #FOOTER
                $print_str .= append_chars(substrwords('Invoice Start: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars(iSetObj($trans['first_ref'],'trans_ref'),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= append_chars(substrwords('Invoice End: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars(iSetObj($trans['last_ref'],'trans_ref'),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= append_chars(substrwords('Invoice Ctr: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars($trans['ref_count'],"left",PAPER_RD_COL_3_3," ")."\r\n";
                if($title_name == "ZREAD"){
                    $gt = $this->old_grand_net_total($post['from']);
                    $print_str .= "\r\n";
                    $print_str .= append_chars(substrwords('OLD GT: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                                 .append_chars(numInt( $gt['old_grand_total']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $print_str .= append_chars(substrwords('NEW GT: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                                 .append_chars( numInt($gt['old_grand_total']+$net_no_adds)  ,"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $print_str .= append_chars(substrwords('Z READ CTR: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                                 .append_chars( $gt['ctr'] ,"left",PAPER_RD_COL_3_3," ")."\r\n";
                }
                $print_str .= PAPER_LINE."\r\n";
            #MALLS
                if(MALL_ENABLED){
                    ####################################
                    # AYALA
                        if(MALL == 'ayala'){
                            
                            $branch_details = $this->setup_model->get_branch_details();
                            $branch = array();
                            foreach ($branch_details as $bv) {
                                $branch = array(
                                    'id' => $bv->branch_id,
                                    'res_id' => $bv->res_id,
                                    'branch_code' => $bv->branch_code,
                                    'name' => $bv->branch_name,
                                    'branch_desc' => $bv->branch_desc,
                                    'contact_no' => $bv->contact_no,
                                    'delivery_no' => $bv->delivery_no,
                                    'address' => $bv->address,
                                    'base_location' => $bv->base_location,
                                    'currency' => $bv->currency,
                                    'inactive' => $bv->inactive,
                                    'tin' => $bv->tin,
                                    'machine_no' => $bv->machine_no,
                                    'bir' => $bv->bir,
                                    'permit_no' => $bv->permit_no,
                                    'serial' => $bv->serial,
                                    'accrdn' => $bv->accrdn,
                                    'email' => $bv->email,
                                    'website' => $bv->website,
                                    'store_open' => $bv->store_open,
                                    'store_close' => $bv->store_close,
                                );
                            }


                            $print_str .= align_center("FOR AYALA",PAPER_WIDTH," ")."\r\n";
                            $print_str .= align_center($branch['name'],PAPER_WIDTH," ")."\r\n";
                            $print_str .= align_center($branch['address'],PAPER_WIDTH," ")."\r\n\r\n";
                            $print_str .= align_center("CONSOLIDATED REPORT Z-READ",PAPER_WIDTH," ")."\r\n\r\n";


                            $total_daily_sales = $total_vatA = $total_rawgrossA = $total_discount = $total_refund = $total_void = $total_charge = $total_non_tax = $total_trans_count = $total_guest = 0;


                            $paytype = array();
                            foreach ($payments_types as $code => $val) {
                                if($code != 'credit'){
                                    if(!isset($paytype[$code])){
                                        $paytype[$code] = array('amount'=>$val['amount']);
                                    }else{
                                        $row = $paytype[$code];
                                        $row['amount'] += $val['amount'];
                                        $paytype[$code] = $row;
                                    }
                                }
                                // $print_str .= append_chars(substrwords(ucwords(strtoupper($code)),12,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                                //               .append_chars(num($val['amount'],2),"left",PAPER_RD_COL_3_3," ")."\r\n";
                                // $pay_qty += $val['qty'];
                            }
                            $paycards = array();
                            if($payments['cards']){
                                $cards = $payments['cards'];
                                foreach($cards as $key => $val){
                                    if(!isset($paycards[$key])){
                                        $paycards[$key] = array('amount'=>$val['amount']);
                                    }else{
                                        $row = $paycards[$key];
                                        $row['amount'] += $val['amount'];
                                        $paycards[$key] = $row;
                                    }
                                }
                            }


                            // for server
                            $rawgrossA = numInt($gross + $charges + $void + $local_tax);
                            $vatA = numInt(($rawgrossA  - $discounts - $void  -  $charges - $nontaxable - $local_tax - numInt($less_vat)) * (1/9.333333));
                            $dlySaleA = numInt($rawgrossA - $discounts - $void - $charges - $vatA - $less_vat + $local_tax);
                            // $t_discounts = $discounts+$less_vat;
                            $rawgrossA =  $rawgrossA - $less_vat;
                            $t_discounts = $discounts;



                            $trans_count = 0;
                            $begor = 0;
                            $endor = 0;
                            $first_inv = array();
                            $last_inv = array();
                            $first_ref = 0;
                            $last_ref = 0;
                            $first_val = 0;
                            $last_val = 0;
                            $invs = array();
                            foreach ($trans['all_orders'] as $ord) {
                                if($ord->type_id == SALES_TRANS && $ord->trans_ref != ""){
                                    $ref = $ord->trans_ref;
                                    if (preg_match('/^(\D*?)(\d+)(.*)/', $ref, $result) == 1){
                                        // if($ord->inactive != 1){
                                            list($all, $prefix, $number, $postfix) = $result;
                                            $ref_val = intval($number);
                                            $invs[$ref_val] = array("ref"=>$ord->trans_ref,"val"=>$ref_val);
                                        // }
                                    }
                                }
                            }
                            ksort($invs);
                            // echo "<pre>",print_r($invs),"</pre>";die();
                            $first_inv = reset($invs);
                            $last_inv = end($invs);
                            if(count($first_inv) > 0){
                                $first_ref = $first_inv['ref'];
                                $first_val = $first_inv['val'];
                            }
                            if(count($last_inv) > 0){
                                $last_ref = $last_inv['ref'];
                                $last_val = $last_inv['val'];
                            }
                            if(count($invs) > 0){
                                $trans_count = ($last_val - $first_val) + 1; 
                            }

                            // echo $trans_count; die();
                            //add yun mga value ng server sa totals
                            $total_daily_sales += $dlySaleA;
                            $total_vatA += $vatA;
                            $total_rawgrossA += $rawgrossA;
                            $total_discount += $t_discounts;
                            $total_void += $void;
                            $total_charge += $charges;
                            $total_non_tax += $nontaxable;
                            // $total_trans_count += $tc_qty;
                            $total_trans_count += $trans_count;
                            $total_guest += $guestCount;
                             // echo $total_trans_count;

                            $terminals = $this->setup_model->get_terminals();


                            $print_str .= append_chars(substrwords('Daily Sales',25,""),"right",22," ").align_center('',2," ")
                                         .append_chars(num($total_daily_sales),"left",10," ")."\r\n";
                            $print_str .= append_chars(substrwords('Total Discount',25,""),"right",22," ").align_center('',2," ")
                                         .append_chars(num($total_discount),"left",10," ")."\r\n";
                            $print_str .= append_chars(substrwords('Total Refund',25,""),"right",22," ").align_center('',2," ")
                                         .append_chars(num(0),"left",10," ")."\r\n";
                            $print_str .= append_chars(substrwords('Total Void',25,""),"right",22," ").align_center('',2," ")
                                         .append_chars(num($total_void),"left",10," ")."\r\n";
                            $print_str .= append_chars(substrwords('Total Vat',25,""),"right",22," ").align_center('',2," ")
                                         .append_chars(num($total_vatA),"left",10," ")."\r\n";
                            $print_str .= append_chars(substrwords('Total Service Charge',25,""),"right",22," ").align_center('',2," ").append_chars(num($total_charge),"left",10," ")."\r\n";  


                            // $print_str .= append_chars(substrwords('Vatable Sales',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                            //              .append_chars(num($dlySaleA-$nontaxable),"left",PAPER_RD_COL_3_3," ")."\r\n";             
                            $print_str .= append_chars(substrwords('Total Non Taxable',22,""),"right",22," ").align_center('',2," ")
                                         .append_chars(num($total_non_tax),"left",10," ")."\r\n";
                            $print_str .= append_chars(substrwords('Row Gross',22,""),"right",22," ").align_center('',2," ")
                                         .append_chars(num($total_rawgrossA),"left",10," ")."\r\n";             
                            $print_str .= append_chars(substrwords('Transaction Count',22,""),"right",22," ").align_center('',2," ")
                                         .append_chars($total_trans_count,"left",10," ")."\r\n";
                            $print_str .= append_chars(substrwords('Customer Count',22,""),"right",22," ").align_center('',2," ")
                                         .append_chars($total_guest,"left",10," ")."\r\n";
                            foreach ($paytype as $k => $v) {
                                $print_str .= append_chars(strtoupper($k),"right",22," ").align_center('',2," ")
                                     .append_chars(num($v['amount']),"left",10," ")."\r\n";       
                            }
                            foreach ($paycards as $k => $v) {
                                $print_str .= append_chars(strtoupper($k),"right",22," ").align_center('',2," ")
                                     .append_chars(num($v['amount']),"left",10," ")."\r\n";       
                            }
                            $terminals = $this->setup_model->get_terminals();
                            // echo "<pre>",print_r($terminals),"</pre>";die();
                            foreach ($terminals as $k => $val) {
                                $print_str .= append_chars('BIR PERMIT '.$val->terminal_id,"right",15," ").align_center('',2," ")
                                     .append_chars($val->permit,"left",17," ")."\r\n";
                                $print_str .= append_chars('SERIAL NO. '.$val->terminal_id,"right",15," ").align_center('',2," ")
                                     .append_chars($val->serial,"left",17," ")."\r\n";
                            }           
                            // $print_str .= append_chars(substrwords('Less SC Disc',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                            //              .append_chars(num($discounts),"left",PAPER_RD_COL_3_3," ")."\r\n";             
                            // $print_str .= append_chars(substrwords('Vat Exempt',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                            //              .append_chars(num($less_vat),"left",PAPER_RD_COL_3_3," ")."\r\n";             
                            // $print_str .= append_chars(substrwords('Zero Rated',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                            //              .append_chars(num($zero_rated),"left",PAPER_RD_COL_3_3," ")."\r\n";             
                            // $print_str .= PAPER_LINE_SINGLE."\r\n";
                            // $print_str .= append_chars(substrwords('Net Sales',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                            //              .append_chars(num($dlySaleA+$vatA),"left",PAPER_RD_COL_3_3," ")."\r\n";             
                            // $print_str .= append_chars(substrwords('Total Qty Sold',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                            //              .append_chars(num($cat_total_qty),"left",PAPER_RD_COL_3_3," ")."\r\n";             
                            $print_str .= PAPER_LINE."\r\n";
                            $print_str .= align_center(sql2Date($post['from']),PAPER_WIDTH," ")."\r\n";
                            $print_str .= align_center("END OF REPORT",PAPER_WIDTH," ")."\r\n";


                        }
                    ####################################
                } 
                if ($return_print_str) {
                return $print_str;
            }    

            // if($title_name == "ZREAD"){
            //     if($asJson == false){
            //         $this->manager_model->add_event_logs($user['id'],"ZREAD","View");    
            //     }else{
            //         $this->manager_model->add_event_logs($user['id'],"ZREAD","Print");                    
            //     } 
            // }elseif($title_name == "XREAD"){
            //     if($asJson == false){
            //         $this->manager_model->add_event_logs($user['id'],"XREAD","View");    
            //     }else{
            //         $this->manager_model->add_event_logs($user['id'],"XREAD","Print");                    
            //     } 
            // }else{
            //     if($asJson == false){
            //         $this->manager_model->add_event_logs($user['id'],"System Sales","View");    
            //     }else{
            //         $this->manager_model->add_event_logs($user['id'],"System Sales","Print");                    
            //     }
            // }
            $this->session->set_userdata('pdf_data','<pre>'.$print_str.'</pre>') ;   
            // if(PRINT_VERSION && PRINT_VERSION == 'V2'){
            //     $this->do_print_v2($print_str,$asJson);  
            // }else if(PRINT_VERSION && PRINT_VERSION == 'V3' && $asJson){
            //     echo $this->html_print($print_str);
            // }else{
                $this->do_print($print_str,$asJson);

            // $gross = $trans_menus['gross'];
            
            // $net = $trans['net'];
            // $void = $trans['void'];
            // $void_cnt = $trans['void_cnt'];
            // $cancelled = $trans['cancel_amount'];
            // $cancel_cnt = $trans['cancel_cnt'];
            // $charges = $trans_charges['total'];
            // $discounts = $trans_discounts['total'];
            // $local_tax = $trans_local_tax['total'];
            // $ewt1_count = $trans['ewt1_count'];
            // $ewt2_count = $trans['ewt2_count'];
            // $ewt5_count = $trans['ewt5_count'];
            // $ewt = 0;
            // $ewt = $ewt1_count + $ewt2_count + $ewt5_count;
            // // echo $gross.' - '.$charges.' - '.$discounts.' - '.$net; die();

            // $types = $trans_discounts['types'];
            // $pwd = $snr = 0;
            // foreach ($types as $code => $val) {
            //     if($code == 'SNDISC'){
            //         $snr += $val['amount'];
            //     }

            //     if($code == 'PWDISC'){
            //         $pwd += $val['amount'];
            //     }
            // }

            // $less_vat_snrpwd = ($snr + $pwd) / 0.20;
            // $less_vat = ($less_vat_snrpwd + $trans_zero_rated['total']) * 0.12;


            // // $less_vat = (($gross+$charges+$local_tax) - $discounts) - $ewt - $net;
            // // $less_vat = $trans_discounts['vat_exempt_total'];

            // // echo $gross.'+'.$charges.'+'.$local_tax.' - '.$discounts.' - '.$net;
            // // die();

            // if($less_vat < 0)
            //     $less_vat = 0;
            // // var_dump($less_vat);

            // //para mag tugmam yun payments and netsale
            // // $net_sales2 = $gross + $charges - $discounts - $less_vat;
            // // $diffs = $net_sales2 - $payments['total'];
            // // if($diffs < 1){
            // //     $less_vat = $less_vat + $diffs;
            // // }
            

            // $tax = $trans_tax['total'];
            // $no_tax = $trans_no_tax['total'];
            // $zero_rated = $trans_zero_rated['total'];
            // $no_tax -= $zero_rated;

            // $title_name = "ZREAD";
            // if($post['title'] != "")
            //     $title_name = $post['title'];

            // $print_str .= align_center($title_name,PAPER_WIDTH," ")."\r\n";
            // $print_str .= align_center("TERMINAL ".$post['terminal'],PAPER_WIDTH," ")."\r\n";
            // $print_str .= append_chars('Printed On','right',11," ").append_chars(": ".date2SqlDateTime($time),'right',19," ")."\r\n";
            // $print_str .= append_chars('Printed BY','right',11," ").append_chars(": ".$user['full_name'],'right',19," ")."\r\n";
            // $print_str .= PAPER_LINE."\r\n";
            // $print_str .= align_center($print_date,PAPER_WIDTH," ")."\r\n";
            // if($post['employee'] != "All")
            //     $print_str .= align_center($post['employee'],PAPER_WIDTH," ")."\r\n";
            // $print_str .= PAPER_LINE."\r\n";

            // $loc_txt = numInt(($local_tax));
            // $net_no_adds = $net-($charges+$local_tax);
            // // $nontaxable = $no_tax;
            // //binago 9/25/2018 for zreading adjustment of vat exempt equal to the receipt vat exempt
            // $nontaxable = $no_tax - $no_tax_disc;
            // // echo $gross.' - '.$less_vat.' - '.$nontaxable.' - '.$zero_rated; die();
            // // $taxable = ($gross - $less_vat - $nontaxable - $zero_rated) / 1.12;
            // // 1.12; binago din para sa adjustment of vat exempt equal to the receipt vat exempt
            // // $taxable =   ($gross - $discounts - $less_vat - $nontaxable) / 1.12;
            // $taxable =   ($gross - $less_vat - $nontaxable - $zero_rated - $discounts) / 1.12; //change computation conflict for zero rated 10 17 2018
            // $total_net = ($taxable) + ($nontaxable+$zero_rated) + $tax + $local_tax;
            // $add_gt = $taxable+$nontaxable+$zero_rated;
            // $nsss = $taxable +  $nontaxable +  $zero_rated;

            // #GENERAL
            //     $print_str .= append_chars(substrwords('GROSS SALES',18,""),"right",PAPER_TOTAL_COL_1," ")
            //                              .append_chars(num($gross,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                

            //     // $print_str .= append_chars(substrwords('TOTAL SALES',18,""),"right",PAPER_TOTAL_COL_1," ")
            //     //                          .append_chars(num($gross + $charges,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     // $print_str .= append_chars(substrwords(ucwords(strtoupper('SC/PWD VAT EXEMPT')),18,""),"right",PAPER_TOTAL_COL_1," ")
            //     //                          .append_chars('-'.num($less_vat,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     // $print_str .= append_chars('',"right",12," ").align_center('',PAPER_TOTAL_COL_2," ")
            //     //                   .append_chars('----------',"left",PAPER_TOTAL_COL_2," ")."\r\n";

            //     // $print_str .= append_chars(substrwords('GROSS SALES',18,""),"right",PAPER_TOTAL_COL_1," ")
            //     //                          .append_chars(num($gross + $charges - $less_vat,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";

            //     $types = $trans_charges['types'];
            //     // $qty = 0;
            //     // echo "<pre>",print_r($types),"</pre>";die();
            //     foreach ($types as $code => $val) {
            //         $amount = $val['amount'];
            //         $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",PAPER_TOTAL_COL_1," ")
            //                              .append_chars(num($amount,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //         // $qty += $val['qty'];
            //     }
            //     $types = $trans_discounts['types'];
            //     $qty = 0;
            //     foreach ($types as $code => $val) {
            //         if($code != 'DIPLOMAT'){
            //             $amount = $val['amount'];
            //             // if(MALL == 'megamall' && $code == PWDDISC){
            //             //     $amount = $val['amount'] / 1.12;
            //             // }
            //             $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",PAPER_TOTAL_COL_1," ")
            //                                  .append_chars('-'.Num($amount,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //             // $qty += $val['qty'];
            //         }
            //     }
            //     $print_str .= append_chars(substrwords(ucwords(strtoupper('SC/PWD VAT EXEMPT')),18,""),"right",PAPER_TOTAL_COL_1," ")
            //                              .append_chars('-'.num($less_vat,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     if(EWT_DISCOUNT){
            //     $print_str .= append_chars(substrwords('EWT 1%',23,""),"right",PAPER_TOTAL_COL_1," ")
            //                              .append_chars(num(numInt($ewt1_count)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     $print_str .= append_chars(substrwords('EWT 2%',23,""),"right",PAPER_TOTAL_COL_1," ")
            //                              .append_chars(num(numInt($ewt2_count)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     $print_str .= append_chars(substrwords('EWT 5%',23,""),"right",PAPER_TOTAL_COL_1," ")
            //                              .append_chars(num(numInt($ewt5_count)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     }
            //     $print_str .= append_chars('',"right",12," ").align_center('',PAPER_TOTAL_COL_2," ")
            //                       .append_chars('----------',"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     $net_sales = (($gross + ($charges - $ewt1_count - $ewt2_count - $ewt5_count)) - $discounts) - $less_vat;
            //     $print_str .= append_chars(substrwords(ucwords(strtoupper('GROSS RECEIPTS')),18,""),"right",PAPER_TOTAL_COL_1," ")
            //                              .append_chars(num($net_sales,2),"left",PAPER_TOTAL_COL_2," ")."\r\n\r\n";
            //     // $print_str .= append_chars(substrwords(ucwords(strtoupper('NET SALES')),18,""),"right",PAPER_TOTAL_COL_1," ")
            //                              // .append_chars(num($net_sales,2),"left",PAPER_TOTAL_COL_2," ")."\r\n\r\n";
            // #PAYMENTS
            //     $payments_types = $payments['types'];
            //     $payments_total = $payments['total'];
            //     $pay_qty = 0;
            // #SUMMARY
            //     $final_gross = $gross;
            //     $vat_ = $taxable * .12;
            //     $print_str .= append_chars(substrwords('VAT SALES',23,""),"right",PAPER_TOTAL_COL_1," ")
            //                              .append_chars(num(numInt($taxable)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     $print_str .= append_chars(substrwords('VAT',23,""),"right",PAPER_TOTAL_COL_1," ")
            //                              .append_chars(num(numInt($vat_)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     $print_str .= append_chars(substrwords('VAT EXEMPT SALES',23,""),"right",PAPER_TOTAL_COL_1," ")
            //                              .append_chars(num(numInt($nontaxable + $zero_rated)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //                              // .append_chars(numInt($nontaxable-$zero_rated),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     $print_str .= append_chars(substrwords('ZERO RATED',23,""),"right",PAPER_TOTAL_COL_1," ")
            //                              .append_chars(num(numInt($zero_rated)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     $print_str .= append_chars('',"right",12," ").align_center('',PAPER_TOTAL_COL_2," ")
            //                       .append_chars('----------',"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     // $net_sales = $gross + $charges - $discounts - $less_vat;
            //     $nsales = $taxable + $vat_ + $nontaxable + $zero_rated;
            //     // $print_str .= append_chars(substrwords(ucwords(strtoupper('NET SALES')),18,""),"right",PAPER_TOTAL_COL_1," ")
            //                              // .append_chars(num($nsales,2),"left",PAPER_TOTAL_COL_2," ")."\r\n\r\n";
            //     if(OTHER_MENU_ITEM_SALES){
            //         $netss = 0;
            //         $print_str .= append_chars(substrwords(ucwords(strtoupper('GROSS SALES')),18,""),"right",PAPER_TOTAL_COL_1," ")
            //                              .append_chars(num($nsales,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //         $print_str .= append_chars(substrwords('VAT 12%',23,""),"right",PAPER_TOTAL_COL_1," ")
            //                              .append_chars(num('-'.numInt($vat_)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //         $netss = $nsales - $vat_;
            //         $print_str .= append_chars('',"right",12," ").align_center('',PAPER_TOTAL_COL_2," ")
            //                       .append_chars('----------',"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //         $print_str .= append_chars(substrwords('NET SALES',23,""),"right",PAPER_TOTAL_COL_1," ")
            //                              .append_chars(num(numInt($netss)),"left",PAPER_TOTAL_COL_2," ")."\r\n\r\n";
            //     }else{
            //         $print_str .= append_chars(substrwords(ucwords(strtoupper('NET SALES')),18,""),"right",PAPER_TOTAL_COL_1," ")
            //                              .append_chars(num($nsales,2),"left",PAPER_TOTAL_COL_2," ")."\r\n\r\n";
            //     }

            //     $net_pay_variance = 0;
            //     if($net_sales != $payments_total){
            //         $net_pay_variance = $net_sales - $payments_total;
            //     }
                

            //     $print_str .= append_chars(substrwords('Payment Breakdown:',18,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
            //                   .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
            //     foreach ($payments_types as $code => $val) {
            //         if($code == 'cash'){
            //             $print_str .= append_chars(substrwords(ucwords(strtoupper($code)),18,""),"right",12," ").align_center($val['qty'],11," ")
            //                           .append_chars(num(numInt($val['amount'] + $net_pay_variance)),"left",11," ")."\r\n";
            //         }else{
            //             $print_str .= append_chars(substrwords(ucwords(strtoupper($code)),18,""),"right",12," ").align_center($val['qty'],11," ")
            //                           .append_chars(num(numInt($val['amount'])),"left",11," ")."\r\n";
            //         }

            //         // $print_str .= append_chars(substrwords(ucwords(strtoupper($code)),18,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
            //                       // .append_chars(num(numInt($val['amount'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //         $pay_qty += $val['qty'];
            //     }
            //     $print_str .= append_chars('',"right",18," ").align_center('',PAPER_RD_COL_2," ")
            //                       .append_chars('----------',"left",PAPER_RD_COL_3_3," ")."\r\n";
            //     $print_str .= append_chars(substrwords('TOTAL PAYMENTS',18,""),"right",PAPER_RD_COL_1," ").align_center($pay_qty,PAPER_RD_COL_2," ")
            //                   .append_chars(num(numInt($payments_total + $net_pay_variance)),"left",PAPER_RD_COL_3_3," ")."\r\n\r\n";
            //     $print_str .= PAPER_LINE_SINGLE."\r\n";
            //     $gross_less_disc = $final_gross - $discounts - $less_vat;
            //     // $print_str .= append_chars(substrwords('NET SALES',23,""),"right",PAPER_TOTAL_COL_1," ")
            //     //                          // .append_chars(numInt(($taxable + $nontaxable + $zero_rated + $vat_)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     //                          .append_chars(numInt(($taxable + $nontaxable + $vat_)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     // $print_str .= PAPER_LINE."\r\n";

            //     if(count($payments['currency']) > 0){
            //         $currency = $payments['currency'];
            //         $print_str .= append_chars(substrwords('Currency Breakdown:',20,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
            //                       .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
            //         foreach ($currency as $code => $val) {
            //             $print_str .= append_chars(substrwords(ucwords(strtoupper($code)),18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ").append_chars(num(numInt($val['amount'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //             // $pay_qty += $val['qty'];
            //         }
            //     }

            //     // $print_str .= PAPER_LINE_SINGLE."\r\n";
            //     $print_str .= "\r\n\r\n";



            //     $print_str .= append_chars(substrwords('VOID SALES',18,""),"right",PAPER_TOTAL_COL_1," ")
            //                  .append_chars(num(($void),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     $print_str .= append_chars(substrwords('VOID SALES COUNT',25,""),"right",PAPER_TOTAL_COL_1," ")
            //                  .append_chars($void_cnt,"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     $print_str .= append_chars(substrwords('CANCELLED TRANS',18,""),"right",PAPER_TOTAL_COL_1," ")
            //                  .append_chars(num(($cancelled),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     $print_str .= append_chars(substrwords('CANCELLED TRANS COUNT',25,""),"right",PAPER_TOTAL_COL_1," ")
            //                  .append_chars($cancel_cnt,"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     // $user = $this->input->post('user');
            //     $branch_code = $this->input->post('branch_id');
            //     // $args = array();;
            //     // if($user != ""){
            //         // $args['trans_sales.user_id'] = $user;
            //         $args['trans_sales.branch_code'] = $branch_code;
            //     // }
            //     $cancelled_order = $this->cancelled_orders($args);
            //     // $cancelled_order = $this->cancelled_orders();
            //     $co = $cancelled_order['cancelled_order'];
            //     $print_str .= append_chars(substrwords('CANCELLED ORDERS',18,""),"right",PAPER_TOTAL_COL_1," ")
            //                  .append_chars(num(($co),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     $print_str .= append_chars(substrwords('CANCELLED ORDER COUNT',25,""),"right",PAPER_TOTAL_COL_1," ")
            //                  .append_chars($cancelled_order['cancel_count'],"left",PAPER_TOTAL_COL_2," ")."\r\n";

            //     $print_str .= append_chars(substrwords('Local Tax',18,""),"right",PAPER_TOTAL_COL_1," ")
            //                  .append_chars($loc_txt,"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     $print_str .= "\r\n";

            // #TRANS COUNT
            //     $types = $trans['types'];
            //     $types_total = array();
            //     $guestCount = 0;
            //     foreach ($types as $type => $tp) {
            //         foreach ($tp as $id => $opt){
            //             if(isset($types_total[$type])){
            //                 $types_total[$type] += round($opt->total_amount,2);

            //             }
            //             else{
            //                 $types_total[$type] = round($opt->total_amount,2);
            //             }

            //             // if($opt->type == 'dinein'){
            //             //     $guestCount += $opt->guest;
            //             // }
            //             if($opt->guest == 0)
            //                 $guestCount += 1;
            //             else
            //                 $guestCount += $opt->guest;
            //         }
            //     }
            //     $print_str .= append_chars(substrwords('Trans Count:',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                  .append_chars('',"left",PAPER_RD_COL_3_3," ")."\r\n";
            //     $tc_total  = 0;
            //     $tc_qty = 0;
            //     foreach ($types_total as $typ => $tamnt) {
            //         $print_str .= append_chars(substrwords($typ,18,""),"right",PAPER_RD_COL_1," ").align_center(count($types[$typ]),PAPER_RD_COL_2," ")
            //                      .append_chars(num(numInt($tamnt)),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //         $tc_total += $tamnt;
            //         $tc_qty += count($types[$typ]);
            //     }
            //     $print_str .= "-----------------"."\r\n";
            //     $print_str .= append_chars(substrwords('TC Total',18,""),"right",PAPER_TOTAL_COL_1," ")
            //                  .append_chars(num(numInt($tc_total)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     $print_str .= append_chars(substrwords('GUEST Total',18,""),"right",PAPER_TOTAL_COL_1," ")
            //                  .append_chars($guestCount,"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     // if($tc_total == 0 || $tc_qty == 0)
            //     //     $avg = 0;
            //     // else
            //     //     $avg = $tc_total/$tc_qty;
            //     if($net_sales){
            //         if($guestCount == 0){
            //             $avg = 0;
            //         }else{
            //             $avg = $net_sales/$guestCount;
            //         }
            //     }else{
            //         $avg = 0;
            //     }


            //     $print_str .= append_chars(substrwords('AVG Check',18,""),"right",PAPER_TOTAL_COL_1," ")
            //                  .append_chars(num(numInt($avg)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     $print_str .= "\r\n";
            // #CHARGES
            //     $types = $trans_charges['types'];
            //     $qty = 0;
            //     $print_str .= append_chars(substrwords('Charges:',18,""),"right",18," ").align_center(null,5," ")
            //                   .append_chars(null,"left",13," ")."\r\n";
            //     foreach ($types as $code => $val) {
            //         $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
            //                       .append_chars(num(numInt($val['amount'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //         $qty += $val['qty'];
            //     }
            //     $print_str .= "-----------------"."\r\n";
            //     $print_str .= append_chars(substrwords('Total Charges',18,""),"right",PAPER_RD_COL_1," ").align_center($qty,PAPER_RD_COL_2," ")
            //                   .append_chars(num(numInt($charges)),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //     $print_str .= "\r\n";
            // #Discounts
            //     $types = $trans_discounts['types'];
            //     $qty = 0;
            //     $print_str .= append_chars(substrwords('Discounts:',18,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
            //                   .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
            //     foreach ($types as $code => $val) {
            //         if($code != 'DIPLOMAT'){
            //             $amount = $val['amount'];
            //             // if(MALL == 'megamall' && $code == PWDDISC){
            //             //     $amount = $val['amount'] / 1.12;
            //             // }
            //             $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
            //                           .append_chars(num(numInt($amount)),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //             $qty += $val['qty'];
            //         }
            //     }
            //     $print_str .= "-----------------"."\r\n";
            //     $print_str .= append_chars(substrwords('Total Discounts',18,""),"right",PAPER_RD_COL_1," ").align_center($qty,PAPER_RD_COL_2," ")
            //                   .append_chars(num(numInt($discounts)),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //     $print_str .= append_chars(substrwords('VAT EXEMPT',18,""),"right",PAPER_TOTAL_COL_1," ")
            //                              .append_chars(num(numInt($less_vat)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     $print_str .= "\r\n";
            // #PAYMENTS
            //     $payments_types = $payments['types'];
            //     $payments_total = $payments['total'];
            //     $pay_qty = 0;
            //     $print_str .= append_chars(substrwords('Payment Breakdown:',18,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
            //                   .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
            //     foreach ($payments_types as $code => $val) {
            //         $print_str .= append_chars(substrwords(ucwords(strtolower($code)),18,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
            //                       .append_chars(num(numInt($val['amount'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //         $pay_qty += $val['qty'];
            //     }
            //     $print_str .= "-----------------"."\r\n";
            //     $print_str .= append_chars(substrwords('Total Payments',18,""),"right",PAPER_RD_COL_1," ").align_center($pay_qty,PAPER_RD_COL_2," ")
            //                   .append_chars(num(numInt($payments_total)),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //     $print_str .= "\r\n";

            //     //card breakdown
            //     if($payments['cards']){
            //         $cards = $payments['cards'];
            //         $card_total = 0;
            //         $count_total = 0;
            //         $print_str .= append_chars(substrwords('Card Breakdown:',18,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
            //                   .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
            //         foreach($cards as $key => $val){
            //             $print_str .= append_chars(substrwords($key,18,""),"right",PAPER_RD_COL_1," ").align_center($val['count'],PAPER_RD_COL_2," ")
            //                       .append_chars(num(numInt($val['amount'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //             $card_total += $val['amount'];
            //             $count_total += $val['count'];
            //         }
            //         $print_str .= "-----------------"."\r\n";
            //         $print_str .= append_chars(substrwords('Total',18,""),"right",PAPER_RD_COL_1," ").align_center($count_total,PAPER_RD_COL_2," ")
            //                   .append_chars(num(numInt($card_total)),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    
            //         $print_str .= "\r\n";
            //     }

            //     //get all gc with excess
            //     if($payments['gc_excess']){
            //         $print_str .= append_chars(substrwords('GC EXCESS',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                       .append_chars(num(numInt($payments['gc_excess'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //         $print_str .= "\r\n";
            //     }

            //     //show all sign chit
            //     // $trans['sales']
            //     if($trans['total_chit']){
            //         $print_str .= append_chars(substrwords('TOTAL CHIT',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                       .append_chars(num(numInt($trans['total_chit'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //         $print_str .= "\r\n";
            //     }
            // #CATEGORIES
            //     $cats = $trans_menus['cats'];
            //     $print_str .= append_chars('Menu Categories:',"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                  .append_chars('',"left",PAPER_RD_COL_3," ")."\r\n";
            //     $qty = 0;
            //     $total = 0;
            //     foreach ($cats as $id => $val) {
            //         if($val['qty'] > 0){
            //             $print_str .= append_chars(substrwords($val['name'],18,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
            //                        .append_chars(num(numInt($val['amount'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //             $qty += $val['qty'];
            //             $total += $val['amount'];
            //         }
            //      }
            //     $print_str .= "-----------------"."\r\n";
            //     $cat_total_qty = $qty;
            //     $print_str .= append_chars("SubTotal","right",PAPER_RD_COL_1," ").align_center($qty,PAPER_RD_COL_2," ")
            //                   .append_chars(num(numInt($total)),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //     $print_str .= append_chars("Modifiers Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                   .append_chars(num(numInt($trans_menus['mods_total'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //      $print_str .= append_chars("SubModifier Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                   .append_chars(num(numInt($trans_menus['submods_total'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //     if($trans_menus['item_total'] > 0){
            //      $print_str .= append_chars("Retail Items Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                    .append_chars(num(numInt($trans_menus['item_total'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //     }

            //     $print_str .= append_chars("Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                   .append_chars(num(numInt($total+$trans_menus['mods_total']+$trans_menus['item_total']+$trans_menus['submods_total'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //     $print_str .= "\r\n";
            // #SUBCATEGORIES
            //     $subcats = $trans_menus['sub_cats'];
            //     // $print_str .= append_chars('Menu Subcategories:',"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //     $print_str .= append_chars('Menu Types:',"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                  .append_chars('',"left",PAPER_RD_COL_3," ")."\r\n";
            //     $qty = 0;
            //     $total = 0;
            //     foreach ($subcats as $id => $val) {
            //         $print_str .= append_chars($val['name'],"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
            //                    .append_chars(num(numInt($val['amount'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //         $qty += $val['qty'];
            //         $total += $val['amount'];
            //      }
            //     $print_str .= "-----------------"."\r\n";
            //     $print_str .= append_chars("Total","right",PAPER_RD_COL_1," ").align_center($qty,PAPER_RD_COL_2," ")
            //                   .append_chars(num(numInt($total)),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //     // $print_str .= append_chars("Modifiers Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //     //               .append_chars(numInt($trans_menus['mods_total']),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //     // $print_str .= append_chars("SubModifier Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //     //               .append_chars(numInt($trans_menus['submods_total']),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //     // $print_str .= append_chars("Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //     //               .append_chars(numInt($total+$trans_menus['mods_total']+$trans_menus['submods_total']),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //     $print_str .= "\r\n";
            // #FREE MENUS
            //     $free = $trans_menus['free_menus'];
            //     $print_str .= append_chars('Free Menus:',"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                  .append_chars('',"left",PAPER_RD_COL_3," ")."\r\n";
            //     $fm = array();
            //     foreach ($free as $ms) {
            //         if(!isset($fm[$ms->menu_id])){
            //             $mn = array();
            //             $mn['name'] = $ms->menu_name;
            //             $mn['cat_id'] = $ms->cat_id;
            //             $mn['qty'] = $ms->qty;
            //             $mn['amount'] = $ms->sell_price * $ms->qty;
            //             $mn['sell_price'] = $ms->sell_price;
            //             $mn['code'] = $ms->menu_code;
            //             // $mn['free_user_id'] = $ms->free_user_id;
            //             $fm[$ms->menu_id] = $mn;
            //         }
            //         else{
            //             $mn = $fm[$ms->menu_id];
            //             $mn['qty'] += $ms->qty;
            //             $mn['amount'] += $ms->sell_price * $ms->qty;
            //             $fm[$ms->menu_id] = $mn;
            //         }
            //     }
            //     $qty = 0;
            //     $total = 0;
            //     foreach ($fm as $menu_id => $val) {
            //         $print_str .= append_chars($val['name'],"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                    .append_chars(($val['qty']),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //         $qty += $val['qty'];
            //         $total += $val['amount'];
            //     }
            //     $print_str .= "-----------------"."\r\n";
            //     $print_str .= append_chars("Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                   .append_chars(($qty),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //     $print_str .= "\r\n";
            //     $print_str .= "\r\n";    
            // #FOOTER
            //     $print_str .= append_chars(substrwords('Invoice Start: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                  .append_chars(iSetObj($trans['first_ref'],'trans_ref'),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //     $print_str .= append_chars(substrwords('Invoice End: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                  .append_chars(iSetObj($trans['last_ref'],'trans_ref'),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //     $print_str .= append_chars(substrwords('Invoice Ctr: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                  .append_chars($trans['ref_count'],"left",PAPER_RD_COL_3_3," ")."\r\n";
            //     if(OTHER_MENU_ITEM_SALES){
            //         $print_str .= append_chars(substrwords('First Trans No.: ',28,""),"right",19," ").align_center('',4," ")
            //                  .append_chars((int)iSetObj($trans['first_ref'],'trans_ref'),"left",10," ")."\r\n";
            //         $print_str .= append_chars(substrwords('Last Trans No.: ',28,""),"right",18," ").align_center('',5," ")
            //                      .append_chars((int)iSetObj($trans['last_ref'],'trans_ref'),"left",10," ")."\r\n";
            //     }
            //     if($title_name == "ZREAD"){
            //         $gt = $this->old_grand_net_total($date);
            //         $print_str .= "\r\n";
            //         $print_str .= append_chars(substrwords('OLD GT: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                      .append_chars(num(numInt( $gt['old_grand_total'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //         $print_str .= append_chars(substrwords('NEW GT: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                      .append_chars( num(numInt($gt['old_grand_total']+$net_no_adds))  ,"left",PAPER_RD_COL_3_3," ")."\r\n";
            //         $print_str .= append_chars(substrwords('Z READ CTR: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                      .append_chars( $gt['ctr'] ,"left",PAPER_RD_COL_3_3," ")."\r\n";
            //     }
            //     $print_str .= PAPER_LINE."\r\n";
            // #MALLS
            //     if(MALL_ENABLED){
            //         ####################################
            //         # AYALA
            //             if(MALL == 'ayala'){
                            
            //                 $branch_details = $this->setup_model->get_branch_details();
            //                 $branch = array();
            //                 foreach ($branch_details as $bv) {
            //                     $branch = array(
            //                         'id' => $bv->branch_id,
            //                         'res_id' => $bv->res_id,
            //                         'branch_code' => $bv->branch_code,
            //                         'name' => $bv->branch_name,
            //                         'branch_desc' => $bv->branch_desc,
            //                         'contact_no' => $bv->contact_no,
            //                         'delivery_no' => $bv->delivery_no,
            //                         'address' => $bv->address,
            //                         'base_location' => $bv->base_location,
            //                         'currency' => $bv->currency,
            //                         'inactive' => $bv->inactive,
            //                         'tin' => $bv->tin,
            //                         'machine_no' => $bv->machine_no,
            //                         'bir' => $bv->bir,
            //                         'permit_no' => $bv->permit_no,
            //                         'serial' => $bv->serial,
            //                         'accrdn' => $bv->accrdn,
            //                         'email' => $bv->email,
            //                         'website' => $bv->website,
            //                         'store_open' => $bv->store_open,
            //                         'store_close' => $bv->store_close,
            //                     );
            //                 }


            //                 $print_str .= align_center("FOR AYALA",PAPER_WIDTH," ")."\r\n";
            //                 $print_str .= align_center($branch['name'],PAPER_WIDTH," ")."\r\n";
            //                 $print_str .= align_center($branch['address'],PAPER_WIDTH," ")."\r\n\r\n";
            //                 $print_str .= align_center("CONSOLIDATED REPORT Z-READ",PAPER_WIDTH," ")."\r\n\r\n";


            //                 $total_daily_sales = $total_vatA = $total_rawgrossA = $total_discount = $total_refund = $total_void = $total_charge = $total_non_tax = $total_trans_count = $total_guest = 0;


            //                 $paytype = array();
            //                 foreach ($payments_types as $code => $val) {
            //                     if($code != 'credit'){
            //                         if(!isset($paytype[$code])){
            //                             $paytype[$code] = array('amount'=>$val['amount']);
            //                         }else{
            //                             $row = $paytype[$code];
            //                             $row['amount'] += $val['amount'];
            //                             $paytype[$code] = $row;
            //                         }
            //                     }
            //                     // $print_str .= append_chars(substrwords(ucwords(strtoupper($code)),12,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
            //                     //               .append_chars(num($val['amount'],2),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //                     // $pay_qty += $val['qty'];
            //                 }
            //                 $paycards = array();
            //                 if($payments['cards']){
            //                     $cards = $payments['cards'];
            //                     foreach($cards as $key => $val){
            //                         if(!isset($paycards[$key])){
            //                             $paycards[$key] = array('amount'=>$val['amount']);
            //                         }else{
            //                             $row = $paycards[$key];
            //                             $row['amount'] += $val['amount'];
            //                             $paycards[$key] = $row;
            //                         }
            //                     }
            //                 }


            //                 // for server
            //                 $rawgrossA = numInt($gross + $charges + $void + $local_tax);
            //                 $vatA = numInt(($rawgrossA  - $discounts - $void  -  $charges - $nontaxable - $local_tax - numInt($less_vat)) * (1/9.333333));
            //                 $dlySaleA = numInt($rawgrossA - $discounts - $void - $charges - $vatA - $less_vat + $local_tax);
            //                 // $t_discounts = $discounts+$less_vat;
            //                 $rawgrossA =  $rawgrossA - $less_vat;
            //                 $t_discounts = $discounts;



            //                 $trans_count = 0;
            //                 $begor = 0;
            //                 $endor = 0;
            //                 $first_inv = array();
            //                 $last_inv = array();
            //                 $first_ref = 0;
            //                 $last_ref = 0;
            //                 $first_val = 0;
            //                 $last_val = 0;
            //                 $invs = array();
            //                 foreach ($trans['all_orders'] as $ord) {
            //                     if($ord->type_id == SALES_TRANS && $ord->trans_ref != ""){
            //                         $ref = $ord->trans_ref;
            //                         if (preg_match('/^(\D*?)(\d+)(.*)/', $ref, $result) == 1){
            //                             // if($ord->inactive != 1){
            //                                 list($all, $prefix, $number, $postfix) = $result;
            //                                 $ref_val = intval($number);
            //                                 $invs[$ref_val] = array("ref"=>$ord->trans_ref,"val"=>$ref_val);
            //                             // }
            //                         }
            //                     }
            //                 }
            //                 ksort($invs);
            //                 // echo "<pre>",print_r($invs),"</pre>";die();
            //                 $first_inv = reset($invs);
            //                 $last_inv = end($invs);
            //                 if(count($first_inv) > 0){
            //                     $first_ref = $first_inv['ref'];
            //                     $first_val = $first_inv['val'];
            //                 }
            //                 if(count($last_inv) > 0){
            //                     $last_ref = $last_inv['ref'];
            //                     $last_val = $last_inv['val'];
            //                 }
            //                 if(count($invs) > 0){
            //                     $trans_count = ($last_val - $first_val) + 1; 
            //                 }

            //                 // echo $trans_count; die();
            //                 //add yun mga value ng server sa totals
            //                 $total_daily_sales += $dlySaleA;
            //                 $total_vatA += $vatA;
            //                 $total_rawgrossA += $rawgrossA;
            //                 $total_discount += $t_discounts;
            //                 $total_void += $void;
            //                 $total_charge += $charges;
            //                 $total_non_tax += $nontaxable;
            //                 // $total_trans_count += $tc_qty;
            //                 $total_trans_count += $trans_count;
            //                 $total_guest += $guestCount;
            //                  // echo $total_trans_count;

            //                 $terminals = $this->setup_model->get_terminals();


            //                 $print_str .= append_chars(substrwords('Daily Sales',25,""),"right",22," ").align_center('',2," ")
            //                              .append_chars(num($total_daily_sales),"left",10," ")."\r\n";
            //                 $print_str .= append_chars(substrwords('Total Discount',25,""),"right",22," ").align_center('',2," ")
            //                              .append_chars(num($total_discount),"left",10," ")."\r\n";
            //                 $print_str .= append_chars(substrwords('Total Refund',25,""),"right",22," ").align_center('',2," ")
            //                              .append_chars(num(0),"left",10," ")."\r\n";
            //                 $print_str .= append_chars(substrwords('Total Void',25,""),"right",22," ").align_center('',2," ")
            //                              .append_chars(num($total_void),"left",10," ")."\r\n";
            //                 $print_str .= append_chars(substrwords('Total Vat',25,""),"right",22," ").align_center('',2," ")
            //                              .append_chars(num($total_vatA),"left",10," ")."\r\n";
            //                 $print_str .= append_chars(substrwords('Total Service Charge',25,""),"right",22," ").align_center('',2," ").append_chars(num($total_charge),"left",10," ")."\r\n";  


            //                 // $print_str .= append_chars(substrwords('Vatable Sales',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                 //              .append_chars(num($dlySaleA-$nontaxable),"left",PAPER_RD_COL_3_3," ")."\r\n";             
            //                 $print_str .= append_chars(substrwords('Total Non Taxable',22,""),"right",22," ").align_center('',2," ")
            //                              .append_chars(num($total_non_tax),"left",10," ")."\r\n";
            //                 $print_str .= append_chars(substrwords('Row Gross',22,""),"right",22," ").align_center('',2," ")
            //                              .append_chars(num($total_rawgrossA),"left",10," ")."\r\n";             
            //                 $print_str .= append_chars(substrwords('Transaction Count',22,""),"right",22," ").align_center('',2," ")
            //                              .append_chars($total_trans_count,"left",10," ")."\r\n";
            //                 $print_str .= append_chars(substrwords('Customer Count',22,""),"right",22," ").align_center('',2," ")
            //                              .append_chars($total_guest,"left",10," ")."\r\n";
            //                 foreach ($paytype as $k => $v) {
            //                     $print_str .= append_chars(strtoupper($k),"right",22," ").align_center('',2," ")
            //                          .append_chars(num($v['amount']),"left",10," ")."\r\n";       
            //                 }
            //                 foreach ($paycards as $k => $v) {
            //                     $print_str .= append_chars(strtoupper($k),"right",22," ").align_center('',2," ")
            //                          .append_chars(num($v['amount']),"left",10," ")."\r\n";       
            //                 }
            //                 $terminals = $this->setup_model->get_terminals();
            //                 // echo "<pre>",print_r($terminals),"</pre>";die();
            //                 foreach ($terminals as $k => $val) {
            //                     $print_str .= append_chars('BIR PERMIT '.$val->terminal_id,"right",15," ").align_center('',2," ")
            //                          .append_chars($val->permit,"left",17," ")."\r\n";
            //                     $print_str .= append_chars('SERIAL NO. '.$val->terminal_id,"right",15," ").align_center('',2," ")
            //                          .append_chars($val->serial,"left",17," ")."\r\n";
            //                 }           
            //                 // $print_str .= append_chars(substrwords('Less SC Disc',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                 //              .append_chars(num($discounts),"left",PAPER_RD_COL_3_3," ")."\r\n";             
            //                 // $print_str .= append_chars(substrwords('Vat Exempt',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                 //              .append_chars(num($less_vat),"left",PAPER_RD_COL_3_3," ")."\r\n";             
            //                 // $print_str .= append_chars(substrwords('Zero Rated',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                 //              .append_chars(num($zero_rated),"left",PAPER_RD_COL_3_3," ")."\r\n";             
            //                 // $print_str .= PAPER_LINE_SINGLE."\r\n";
            //                 // $print_str .= append_chars(substrwords('Net Sales',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                 //              .append_chars(num($dlySaleA+$vatA),"left",PAPER_RD_COL_3_3," ")."\r\n";             
            //                 // $print_str .= append_chars(substrwords('Total Qty Sold',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                 //              .append_chars(num($cat_total_qty),"left",PAPER_RD_COL_3_3," ")."\r\n";             
            //                 $print_str .= PAPER_LINE."\r\n";
            //                 $print_str .= align_center(sql2Date($post['from']),PAPER_WIDTH," ")."\r\n";
            //                 $print_str .= align_center("END OF REPORT",PAPER_WIDTH," ")."\r\n";


            //             }
            //         ####################################
            //     }    

            // if ($return_print_str) {
            //     return $print_str;
            // }  
            // // if($title_name == "ZREAD"){
            // //     if($asJson == false){
            // //         $this->manager_model->add_event_logs($user['id'],"ZREAD","View");    
            // //     }else{
            // //         $this->manager_model->add_event_logs($user['id'],"ZREAD","Print");                    
            // //     } 
            // // }elseif($title_name == "XREAD"){
            // //     if($asJson == false){
            // //         $this->manager_model->add_event_logs($user['id'],"XREAD","View");    
            // //     }else{
            // //         $this->manager_model->add_event_logs($user['id'],"XREAD","Print");                    
            // //     } 
            // // }else{
            // //     if($asJson == false){
            // //         $this->manager_model->add_event_logs($user['id'],"System Sales","View");    
            // //     }else{
            // //         $this->manager_model->add_event_logs($user['id'],"System Sales","Print");                    
            // //     }
            // // }  
            // // if(PRINT_VERSION && PRINT_VERSION == 'V2'){
            // //     $this->do_print_v2($print_str,$asJson);  
            // // }else if(PRINT_VERSION && PRINT_VERSION == 'V3' && $asJson){
            // //     echo $this->html_print($print_str);
            // // }else{
            //     $this->session->set_userdata('pdf_data','<pre>'.$print_str.'</pre>') ; 
            //     $this->do_print($print_str,$asJson);
            // }
            // $this->do_print($print_str,$asJson);
    }
    public function xread_rep_backoffice_aurora($asJson=false,$args=array(),$return_print_str=false,$print_date){
          ////hapchan
            ini_set('memory_limit', '-1');
            set_time_limit(3600);
            
            $print_str = $this->print_header();
            $user = $this->session->userdata('user');
            $time = $this->site_model->get_db_now();
            $post = $this->set_post();
            $curr = $this->search_current();
            $trans = $this->trans_sales($args,$curr);
            // var_dump($trans['net']); die();
            $sales = $trans['sales'];
            $trans_menus = $this->menu_sales($sales['settled']['ids'],$curr,$post['branch_code']);
            $trans_charges = $this->charges_sales($sales['settled']['ids'],$curr,$post['branch_code']);
            $trans_discounts = $this->discounts_sales($sales['settled']['ids'],$curr,$post['branch_code']);
            $tax_disc = $trans_discounts['tax_disc_total'];
            $no_tax_disc = $trans_discounts['no_tax_disc_total'];
            $trans_local_tax = $this->local_tax_sales($sales['settled']['ids'],$curr,$post['branch_code']);
            $trans_tax = $this->tax_sales($sales['settled']['ids'],$curr,$post['branch_code']);
            $trans_no_tax = $this->no_tax_sales($sales['settled']['ids'],$curr,$post['branch_code']);
            $trans_zero_rated = $this->zero_rated_sales($sales['settled']['ids'],$curr,$post['branch_code']);
            $payments = $this->payment_sales($sales['settled']['ids'],$curr,$post['branch_code']);

            // $gross = $trans_menus['gross'];
            
            // $net = $trans['net'];
            // $void = $trans['void'];
            // $void_cnt = $trans['void_cnt'];
            // $cancelled = $trans['cancel_amount'];
            // $cancel_cnt = $trans['cancel_cnt'];
            // $charges = $trans_charges['total'];
            // $discounts = $trans_discounts['total'];
            // $local_tax = $trans_local_tax['total'];
            // $ewt1_count = $trans['ewt1_count'];
            // $ewt2_count = $trans['ewt2_count'];
            // $ewt5_count = $trans['ewt5_count'];

            // $ewt = 0;
            // $ewt = $ewt1_count + $ewt2_count + $ewt5_count;
            // // echo $gross.' - '.$charges.' - '.$discounts.' - '.$net; die();

            // $types = $trans_discounts['types'];
            // $pwd = $snr = 0;
            // foreach ($types as $code => $val) {
            //     if($code == 'SNDISC'){
            //         $snr += $val['amount'];
            //     }

            //     if($code == 'PWDISC'){
            //         $pwd += $val['amount'];
            //     }
            // }



            // // $less_vat = (($gross+$charges+$local_tax) - $discounts) - $ewt - $net; 
            // $less_vat_snrpwd = ($snr + $pwd) / 0.20;
            // $less_vat = ($less_vat_snrpwd + $trans_zero_rated['total']) * 0.12;


            // // $less_vat = (($gross+$charges+$local_tax) - $discounts) -$ewt - $net;
            // // $less_vat = $trans_discounts['vat_exempt_total'];

            // // echo $gross.'+'.$charges.'+'.$local_tax.' - '.$discounts.' - '.$net;
            // // die();

            // if($less_vat < 0)
            //     $less_vat = 0;
            // // var_dump($less_vat);

            // //para mag tugmam yun payments and netsale
            // // $net_sales2 = $gross + $charges - $discounts - $less_vat;
            // // $diffs = $net_sales2 - $payments['total'];
            // // if($diffs < 1){
            // //     $less_vat = $less_vat + $diffs;
            // // }
            

            // $tax = $trans_tax['total'];
            // $no_tax = $trans_no_tax['total'];
            // $zero_rated = $trans_zero_rated['total'];
            // $no_tax -= $zero_rated;

            // $title_name = "XREAD";
            // if($post['title'] != "")
            //     $title_name = $post['title'];

            // $print_str .= align_center($title_name,PAPER_WIDTH," ")."\r\n";
            // $print_str .= align_center("TERMINAL ".$post['terminal'],PAPER_WIDTH," ")."\r\n";
            // $print_str .= append_chars('Printed On','right',11," ").append_chars(": ".date2SqlDateTime($time),'right',19," ")."\r\n";
            // $print_str .= append_chars('Printed BY','right',11," ").append_chars(": ".$user['full_name'],'right',19," ")."\r\n";
            // $print_str .= PAPER_LINE."\r\n";
            // $print_str .= align_center($print_date,PAPER_WIDTH," ")."\r\n";
            // if($post['employee'] != "All")
            //     $print_str .= align_center($post['employee'],PAPER_WIDTH," ")."\r\n";
            // $print_str .= PAPER_LINE."\r\n";

            // $loc_txt = numInt(($local_tax));
            // $net_no_adds = $net-($charges+$local_tax);
            // // $nontaxable = $no_tax;
            // //binago 9/25/2018 for zreading adjustment of vat exempt equal to the receipt vat exempt
            // $nontaxable = $no_tax - $no_tax_disc;
            // // echo $gross.' - '.$less_vat.' - '.$nontaxable.' - '.$zero_rated; die();
            // // $taxable = ($gross - $less_vat - $nontaxable - $zero_rated) / 1.12;
            // // 1.12; binago din para sa adjustment of vat exempt equal to the receipt vat exempt
            // // $taxable =   ($gross - $discounts - $less_vat - $nontaxable) / 1.12;
            // $taxable =   ($gross - $less_vat - $nontaxable - $zero_rated - $discounts) / 1.12; //change computation conflict for zero rated 10 17 2018
            // $total_net = ($taxable) + ($nontaxable+$zero_rated) + $tax + $local_tax;
            // $add_gt = $taxable+$nontaxable+$zero_rated;
            // $nsss = $taxable +  $nontaxable +  $zero_rated;

            // #GENERAL
            //     $print_str .= append_chars(substrwords('GROSS SALES',18,""),"right",PAPER_TOTAL_COL_1," ")
            //                              .append_chars(num($gross,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                

            //     // $print_str .= append_chars(substrwords('TOTAL SALES',18,""),"right",PAPER_TOTAL_COL_1," ")
            //     //                          .append_chars(num($gross + $charges,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     // $print_str .= append_chars(substrwords(ucwords(strtoupper('SC/PWD VAT EXEMPT')),18,""),"right",PAPER_TOTAL_COL_1," ")
            //     //                          .append_chars('-'.num($less_vat,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     // $print_str .= append_chars('',"right",12," ").align_center('',PAPER_TOTAL_COL_2," ")
            //     //                   .append_chars('----------',"left",PAPER_TOTAL_COL_2," ")."\r\n";

            //     // $print_str .= append_chars(substrwords('GROSS SALES',18,""),"right",PAPER_TOTAL_COL_1," ")
            //     //                          .append_chars(num($gross + $charges - $less_vat,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";

            //     $types = $trans_charges['types'];
            //     // $qty = 0;
            //     foreach ($types as $code => $val) {
            //         $amount = $val['amount'];
            //         $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",PAPER_TOTAL_COL_1," ")
            //                              .append_chars(num($amount,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //         // $qty += $val['qty'];
            //     }
            //     $types = $trans_discounts['types'];
            //     $qty = 0;
            //     foreach ($types as $code => $val) {
            //         if($code != 'DIPLOMAT'){
            //             $amount = $val['amount'];
            //             // if(MALL == 'megamall' && $code == PWDDISC){
            //             //     $amount = $val['amount'] / 1.12;
            //             // }
            //             $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",PAPER_TOTAL_COL_1," ")
            //                                  .append_chars('-'.Num($amount,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //             // $qty += $val['qty'];
            //         }
            //     }
            //     $print_str .= append_chars(substrwords(ucwords(strtoupper('SC/PWD VAT EXEMPT')),18,""),"right",PAPER_TOTAL_COL_1," ")
            //                              .append_chars('-'.num($less_vat,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     if(EWT_DISCOUNT){
            //     $print_str .= append_chars(substrwords('EWT 1%',23,""),"right",PAPER_TOTAL_COL_1," ")
            //                              .append_chars(num(numInt($ewt1_count)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     $print_str .= append_chars(substrwords('EWT 2%',23,""),"right",PAPER_TOTAL_COL_1," ")
            //                              .append_chars(num(numInt($ewt2_count)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     $print_str .= append_chars(substrwords('EWT 5%',23,""),"right",PAPER_TOTAL_COL_1," ")
            //                              .append_chars(num(numInt($ewt5_count)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     }
            //     $print_str .= append_chars('',"right",12," ").align_center('',PAPER_TOTAL_COL_2," ")
            //                       .append_chars('----------',"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     $net_sales = (($gross + ($charges - $ewt1_count - $ewt2_count - $ewt5_count)) - $discounts) - $less_vat; 
            //     $print_str .= append_chars(substrwords(ucwords(strtoupper('GROSS RECEIPTS')),18,""),"right",PAPER_TOTAL_COL_1," ")
            //                              .append_chars(num($net_sales,2),"left",PAPER_TOTAL_COL_2," ")."\r\n\r\n";
            //     // $print_str .= append_chars(substrwords(ucwords(strtoupper('NET SALES')),18,""),"right",PAPER_TOTAL_COL_1," ")
            //                              // .append_chars(num($net_sales,2),"left",PAPER_TOTAL_COL_2," ")."\r\n\r\n";
            // #PAYMENTS
            //     $payments_types = $payments['types'];
            //     $payments_total = $payments['total'];
            //     $pay_qty = 0;
            // #SUMMARY
            //     $final_gross = $gross;
            //     $vat_ = $taxable * .12;
            //     $print_str .= append_chars(substrwords('VAT SALES',23,""),"right",PAPER_TOTAL_COL_1," ")
            //                              .append_chars(num(numInt($taxable)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     $print_str .= append_chars(substrwords('VAT',23,""),"right",PAPER_TOTAL_COL_1," ")
            //                              .append_chars(num(numInt($vat_)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     $print_str .= append_chars(substrwords('VAT EXEMPT SALES',23,""),"right",PAPER_TOTAL_COL_1," ")
            //                              .append_chars(num(numInt($nontaxable + $zero_rated)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //                              // .append_chars(numInt($nontaxable-$zero_rated),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     $print_str .= append_chars(substrwords('ZERO RATED',23,""),"right",PAPER_TOTAL_COL_1," ")
            //                              .append_chars(num(numInt($zero_rated)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     $print_str .= append_chars('',"right",12," ").align_center('',PAPER_TOTAL_COL_2," ")
            //                       .append_chars('----------',"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     // $net_sales = $gross + $charges - $discounts - $less_vat;
            //     $nsales = $taxable + $vat_ + $nontaxable + $zero_rated;
            //     if(OTHER_MENU_ITEM_SALES){
            //         $netss = 0;
            //         $print_str .= append_chars(substrwords(ucwords(strtoupper('GROSS SALES')),18,""),"right",PAPER_TOTAL_COL_1," ")
            //                              .append_chars(num($nsales,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //         $print_str .= append_chars(substrwords('VAT 12%',23,""),"right",PAPER_TOTAL_COL_1," ")
            //                              .append_chars(num('-'.numInt($vat_)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //         $netss = $nsales - $vat_;
            //         $print_str .= append_chars('',"right",12," ").align_center('',PAPER_TOTAL_COL_2," ")
            //                       .append_chars('----------',"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //         $print_str .= append_chars(substrwords('NET SALES',23,""),"right",PAPER_TOTAL_COL_1," ")
            //                              .append_chars(num(numInt($netss)),"left",PAPER_TOTAL_COL_2," ")."\r\n\r\n";
            //     }else{
            //         $print_str .= append_chars(substrwords(ucwords(strtoupper('NET SALES')),18,""),"right",PAPER_TOTAL_COL_1," ")
            //                              .append_chars(num($nsales,2),"left",PAPER_TOTAL_COL_2," ")."\r\n\r\n";
            //     }
            //     // $print_str .= append_chars(substrwords(ucwords(strtoupper('NET SALES')),18,""),"right",PAPER_TOTAL_COL_1," ")
            //                              // .append_chars(num($nsales,2),"left",PAPER_TOTAL_COL_2," ")."\r\n\r\n";
                

            //     $net_pay_variance = 0;
            //     if($net_sales != $payments_total){
            //         $net_pay_variance = $net_sales - $payments_total;
            //     }

            //     $print_str .= append_chars(substrwords('Payment Breakdown:',18,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
            //                   .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
            //     foreach ($payments_types as $code => $val) {
            //         if($code == 'cash'){
            //             $print_str .= append_chars(substrwords(ucwords(strtoupper($code)),18,""),"right",12," ").align_center($val['qty'],11," ")
            //                           .append_chars(num(numInt($val['amount'] + $net_pay_variance)),"left",11," ")."\r\n";
            //         }else{
            //             $print_str .= append_chars(substrwords(ucwords(strtoupper($code)),18,""),"right",12," ").align_center($val['qty'],11," ")
            //                           .append_chars(num(numInt($val['amount'])),"left",11," ")."\r\n";
            //         }


            //         // $print_str .= append_chars(substrwords(ucwords(strtoupper($code)),18,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
            //                       // .append_chars(num(numInt($val['amount'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //         $pay_qty += $val['qty'];
            //     }
            //     $print_str .= append_chars('',"right",18," ").align_center('',PAPER_RD_COL_2," ")
            //                       .append_chars('----------',"left",PAPER_RD_COL_3_3," ")."\r\n";
            //     $print_str .= append_chars(substrwords('TOTAL PAYMENTS',18,""),"right",PAPER_RD_COL_1," ").align_center($pay_qty,PAPER_RD_COL_2," ")
            //                   .append_chars(num(numInt($payments_total + $net_pay_variance)),"left",PAPER_RD_COL_3_3," ")."\r\n\r\n";
            //     $print_str .= PAPER_LINE_SINGLE."\r\n";
            //     $gross_less_disc = $final_gross - $discounts - $less_vat;
            //     // $print_str .= append_chars(substrwords('NET SALES',23,""),"right",PAPER_TOTAL_COL_1," ")
            //     //                          // .append_chars(numInt(($taxable + $nontaxable + $zero_rated + $vat_)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     //                          .append_chars(numInt(($taxable + $nontaxable + $vat_)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     // $print_str .= PAPER_LINE."\r\n";

            //     if(count($payments['currency']) > 0){
            //         $currency = $payments['currency'];
            //         $print_str .= append_chars(substrwords('Currency Breakdown:',20,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
            //                       .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
            //         foreach ($currency as $code => $val) {
            //             $print_str .= append_chars(substrwords(ucwords(strtoupper($code)),18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ").append_chars(num(numInt($val['amount'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //             // $pay_qty += $val['qty'];
            //         }
            //     }

            //     // $print_str .= PAPER_LINE_SINGLE."\r\n";
            //     $print_str .= "\r\n\r\n";



            //     $print_str .= append_chars(substrwords('VOID SALES',18,""),"right",PAPER_TOTAL_COL_1," ")
            //                  .append_chars(num(($void),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     $print_str .= append_chars(substrwords('VOID SALES COUNT',25,""),"right",PAPER_TOTAL_COL_1," ")
            //                  .append_chars($void_cnt,"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     $print_str .= append_chars(substrwords('CANCELLED TRANS',18,""),"right",PAPER_TOTAL_COL_1," ")
            //                  .append_chars(num(($cancelled),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     $print_str .= append_chars(substrwords('CANCELLED TRANS COUNT',25,""),"right",PAPER_TOTAL_COL_1," ")
            //                  .append_chars($cancel_cnt,"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     $user = $this->input->post('user');
            //     $branch_code = $this->input->post('branch_id');
            //     $args = array();;
            //     if($user != ""){
            //         $args['trans_sales.user_id'] = $user;
            //         $args['trans_sales.branch_code'] = $branch_code;
            //     }
            //     $cancelled_order = $this->cancelled_orders($args);
            //     $co = $cancelled_order['cancelled_order'];

            //     $print_str .= append_chars(substrwords('CANCELLED ORDERS',18,""),"right",PAPER_TOTAL_COL_1," ")
            //                  .append_chars(num(($co),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     $print_str .= append_chars(substrwords('CANCELLED ORDER COUNT',25,""),"right",PAPER_TOTAL_COL_1," ")
            //                  .append_chars($cancelled_order['cancel_count'],"left",PAPER_TOTAL_COL_2," ")."\r\n";

            //     $print_str .= append_chars(substrwords('Local Tax',18,""),"right",PAPER_TOTAL_COL_1," ")
            //                  .append_chars($loc_txt,"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     $print_str .= "\r\n";

            // #TRANS COUNT
            //     $types = $trans['types'];
            //     $types_total = array();
            //     $guestCount = 0;
            //     foreach ($types as $type => $tp) {
            //         foreach ($tp as $id => $opt){
            //             if(isset($types_total[$type])){
            //                 $types_total[$type] += round($opt->total_amount,2);

            //             }
            //             else{
            //                 $types_total[$type] = round($opt->total_amount,2);
            //             }

            //             // if($opt->type == 'dinein'){
            //             //     $guestCount += $opt->guest;
            //             // }
            //             if($opt->guest == 0)
            //                 $guestCount += 1;
            //             else
            //                 $guestCount += $opt->guest;
            //         }
            //     }
            //     $print_str .= append_chars(substrwords('Trans Count:',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                  .append_chars('',"left",PAPER_RD_COL_3_3," ")."\r\n";
            //     $tc_total  = 0;
            //     $tc_qty = 0;
            //     foreach ($types_total as $typ => $tamnt) {
            //         $print_str .= append_chars(substrwords($typ,18,""),"right",PAPER_RD_COL_1," ").align_center(count($types[$typ]),PAPER_RD_COL_2," ")
            //                      .append_chars(num(numInt($tamnt)),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //         $tc_total += $tamnt;
            //         $tc_qty += count($types[$typ]);
            //     }
            //     $print_str .= "-----------------"."\r\n";
            //     $print_str .= append_chars(substrwords('TC Total',18,""),"right",PAPER_TOTAL_COL_1," ")
            //                  .append_chars(num(numInt($tc_total)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     $print_str .= append_chars(substrwords('GUEST Total',18,""),"right",PAPER_TOTAL_COL_1," ")
            //                  .append_chars($guestCount,"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     // if($tc_total == 0 || $tc_qty == 0)
            //     //     $avg = 0;
            //     // else
            //     //     $avg = $tc_total/$tc_qty;
            //     if($net_sales){
            //         if($guestCount == 0){
            //             $avg = 0;
            //         }else{
            //             $avg = $net_sales/$guestCount;
            //         }
            //     }else{
            //         $avg = 0;
            //     }


            //     $print_str .= append_chars(substrwords('AVG Check',18,""),"right",PAPER_TOTAL_COL_1," ")
            //                  .append_chars(num(numInt($avg)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     $print_str .= "\r\n";
            // #CHARGES
            //     $types = $trans_charges['types'];
            //     $qty = 0;
            //     $print_str .= append_chars(substrwords('Charges:',18,""),"right",18," ").align_center(null,5," ")
            //                   .append_chars(null,"left",13," ")."\r\n";
            //     foreach ($types as $code => $val) {
            //         $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
            //                       .append_chars(num(numInt($val['amount'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //         $qty += $val['qty'];
            //     }
            //     $print_str .= "-----------------"."\r\n";
            //     $print_str .= append_chars(substrwords('Total Charges',18,""),"right",PAPER_RD_COL_1," ").align_center($qty,PAPER_RD_COL_2," ")
            //                   .append_chars(num(numInt($charges)),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //     $print_str .= "\r\n";
            // #Discounts
            //     $types = $trans_discounts['types'];
            //     $qty = 0;
            //     $print_str .= append_chars(substrwords('Discounts:',18,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
            //                   .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
            //     foreach ($types as $code => $val) {
            //         if($code != 'DIPLOMAT'){
            //             $amount = $val['amount'];
            //             // if(MALL == 'megamall' && $code == PWDDISC){
            //             //     $amount = $val['amount'] / 1.12;
            //             // }
            //             $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
            //                           .append_chars(num(numInt($amount)),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //             $qty += $val['qty'];
            //         }
            //     }
            //     $print_str .= "-----------------"."\r\n";
            //     $print_str .= append_chars(substrwords('Total Discounts',18,""),"right",PAPER_RD_COL_1," ").align_center($qty,PAPER_RD_COL_2," ")
            //                   .append_chars(num(numInt($discounts)),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //     $print_str .= append_chars(substrwords('VAT EXEMPT',18,""),"right",PAPER_TOTAL_COL_1," ")
            //                              .append_chars(num(numInt($less_vat)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //     $print_str .= "\r\n";
            // #PAYMENTS
            //     $payments_types = $payments['types'];
            //     $payments_total = $payments['total'];
            //     $pay_qty = 0;
            //     $print_str .= append_chars(substrwords('Payment Breakdown:',18,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
            //                   .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
            //     foreach ($payments_types as $code => $val) {
            //         $print_str .= append_chars(substrwords(ucwords(strtolower($code)),18,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
            //                       .append_chars(num(numInt($val['amount'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //         $pay_qty += $val['qty'];
            //     }
            //     $print_str .= "-----------------"."\r\n";
            //     $print_str .= append_chars(substrwords('Total Payments',18,""),"right",PAPER_RD_COL_1," ").align_center($pay_qty,PAPER_RD_COL_2," ")
            //                   .append_chars(num(numInt($payments_total)),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //     $print_str .= "\r\n";

            //     //card breakdown
            //     if($payments['cards']){
            //         $cards = $payments['cards'];
            //         $card_total = 0;
            //         $count_total = 0;
            //         $print_str .= append_chars(substrwords('Card Breakdown:',18,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
            //                   .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
            //         foreach($cards as $key => $val){
            //             $print_str .= append_chars(substrwords($key,18,""),"right",PAPER_RD_COL_1," ").align_center($val['count'],PAPER_RD_COL_2," ")
            //                       .append_chars(num(numInt($val['amount'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //             $card_total += $val['amount'];
            //             $count_total += $val['count'];
            //         }
            //         $print_str .= "-----------------"."\r\n";
            //         $print_str .= append_chars(substrwords('Total',18,""),"right",PAPER_RD_COL_1," ").align_center($count_total,PAPER_RD_COL_2," ")
            //                   .append_chars(num(numInt($card_total)),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    
            //         $print_str .= "\r\n";
            //     }

            //     //get all gc with excess
            //     if($payments['gc_excess']){
            //         $print_str .= append_chars(substrwords('GC EXCESS',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                       .append_chars(num(numInt($payments['gc_excess'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //         $print_str .= "\r\n";
            //     }

            //     //show all sign chit
            //     // $trans['sales']
            //     if($trans['total_chit']){
            //         $print_str .= append_chars(substrwords('TOTAL CHIT',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                       .append_chars(num(numInt($trans['total_chit'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //         $print_str .= "\r\n";
            //     }
            // #CATEGORIES
            //     $cats = $trans_menus['cats'];
            //     $print_str .= append_chars('Menu Categories:',"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                  .append_chars('',"left",PAPER_RD_COL_3," ")."\r\n";
            //     $qty = 0;
            //     $total = 0;
            //     foreach ($cats as $id => $val) {
            //         if($val['qty'] > 0){
            //             $print_str .= append_chars(substrwords($val['name'],18,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
            //                        .append_chars(num(numInt($val['amount'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //             $qty += $val['qty'];
            //             $total += $val['amount'];
            //         }
            //      }
            //     $print_str .= "-----------------"."\r\n";
            //     $cat_total_qty = $qty;
            //     $print_str .= append_chars("SubTotal","right",PAPER_RD_COL_1," ").align_center($qty,PAPER_RD_COL_2," ")
            //                   .append_chars(num(numInt($total)),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //     $print_str .= append_chars("Modifiers Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                   .append_chars(num(numInt($trans_menus['mods_total'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //      $print_str .= append_chars("SubModifier Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                   .append_chars(num(numInt($trans_menus['submods_total'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //     if($trans_menus['item_total'] > 0){
            //      $print_str .= append_chars("Retail Items Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                    .append_chars(num(numInt($trans_menus['item_total'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //     }

            //     $print_str .= append_chars("Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                   .append_chars(num(numInt($total+$trans_menus['mods_total']+$trans_menus['item_total']+$trans_menus['submods_total'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //     $print_str .= "\r\n";
            // #SUBCATEGORIES
            //     $subcats = $trans_menus['sub_cats'];
            //     // $print_str .= append_chars('Menu Subcategories:',"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //     $print_str .= append_chars('Menu Types:',"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                  .append_chars('',"left",PAPER_RD_COL_3," ")."\r\n";
            //     $qty = 0;
            //     $total = 0;
            //     foreach ($subcats as $id => $val) {
            //         $print_str .= append_chars($val['name'],"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
            //                    .append_chars(num(numInt($val['amount'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //         $qty += $val['qty'];
            //         $total += $val['amount'];
            //      }
            //     $print_str .= "-----------------"."\r\n";
            //     $print_str .= append_chars("Total","right",PAPER_RD_COL_1," ").align_center($qty,PAPER_RD_COL_2," ")
            //                   .append_chars(num(numInt($total)),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //     // $print_str .= append_chars("Modifiers Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //     //               .append_chars(numInt($trans_menus['mods_total']),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //     // $print_str .= append_chars("SubModifier Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //     //               .append_chars(numInt($trans_menus['submods_total']),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //     // $print_str .= append_chars("Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //     //               .append_chars(numInt($total+$trans_menus['mods_total']+$trans_menus['submods_total']),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //     $print_str .= "\r\n";
            // #FREE MENUS
            //     $free = $trans_menus['free_menus'];
            //     $print_str .= append_chars('Free Menus:',"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                  .append_chars('',"left",PAPER_RD_COL_3," ")."\r\n";
            //     $fm = array();
            //     foreach ($free as $ms) {
            //         if(!isset($fm[$ms->menu_id])){
            //             $mn = array();
            //             $mn['name'] = $ms->menu_name;
            //             $mn['cat_id'] = $ms->cat_id;
            //             $mn['qty'] = $ms->qty;
            //             $mn['amount'] = $ms->sell_price * $ms->qty;
            //             $mn['sell_price'] = $ms->sell_price;
            //             $mn['code'] = $ms->menu_code;
            //             // $mn['free_user_id'] = $ms->free_user_id;
            //             $fm[$ms->menu_id] = $mn;
            //         }
            //         else{
            //             $mn = $fm[$ms->menu_id];
            //             $mn['qty'] += $ms->qty;
            //             $mn['amount'] += $ms->sell_price * $ms->qty;
            //             $fm[$ms->menu_id] = $mn;
            //         }
            //     }
            //     $qty = 0;
            //     $total = 0;
            //     foreach ($fm as $menu_id => $val) {
            //         $print_str .= append_chars($val['name'],"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                    .append_chars(($val['qty']),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //         $qty += $val['qty'];
            //         $total += $val['amount'];
            //     }
            //     $print_str .= "-----------------"."\r\n";
            //     $print_str .= append_chars("Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                   .append_chars(($qty),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //     $print_str .= "\r\n";
            //     $print_str .= "\r\n";    
            // #FOOTER
            //     $print_str .= append_chars(substrwords('Invoice Start: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                  .append_chars(iSetObj($trans['first_ref'],'trans_ref'),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //     $print_str .= append_chars(substrwords('Invoice End: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                  .append_chars(iSetObj($trans['last_ref'],'trans_ref'),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //     $print_str .= append_chars(substrwords('Invoice Ctr: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                  .append_chars($trans['ref_count'],"left",PAPER_RD_COL_3_3," ")."\r\n";
            //     if(OTHER_MENU_ITEM_SALES){
            //         $print_str .= append_chars(substrwords('First Trans No.: ',28,""),"right",19," ").align_center('',4," ")
            //                  .append_chars((int)iSetObj($trans['first_ref'],'trans_ref'),"left",10," ")."\r\n";
            //         $print_str .= append_chars(substrwords('Last Trans No.: ',28,""),"right",18," ").align_center('',5," ")
            //                      .append_chars((int)iSetObj($trans['last_ref'],'trans_ref'),"left",10," ")."\r\n";
            //     }
            //     if($title_name == "ZREAD"){
            //         $gt = $this->old_grand_net_total($post['from']);
            //         $print_str .= "\r\n";
            //         $print_str .= append_chars(substrwords('OLD GT: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                      .append_chars(num(numInt( $gt['old_grand_total'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //         $print_str .= append_chars(substrwords('NEW GT: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                      .append_chars( num(numInt($gt['old_grand_total']+$net_no_adds))  ,"left",PAPER_RD_COL_3_3," ")."\r\n";
            //         $print_str .= append_chars(substrwords('Z READ CTR: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                      .append_chars( $gt['ctr'] ,"left",PAPER_RD_COL_3_3," ")."\r\n";
            //     }
            //     $print_str .= PAPER_LINE."\r\n";
            // #MALLS
            //     if(MALL_ENABLED){
            //         ####################################
            //         # AYALA
            //             if(MALL == 'ayala'){
                            
            //                 $branch_details = $this->setup_model->get_branch_details();
            //                 $branch = array();
            //                 foreach ($branch_details as $bv) {
            //                     $branch = array(
            //                         'id' => $bv->branch_id,
            //                         'res_id' => $bv->res_id,
            //                         'branch_code' => $bv->branch_code,
            //                         'name' => $bv->branch_name,
            //                         'branch_desc' => $bv->branch_desc,
            //                         'contact_no' => $bv->contact_no,
            //                         'delivery_no' => $bv->delivery_no,
            //                         'address' => $bv->address,
            //                         'base_location' => $bv->base_location,
            //                         'currency' => $bv->currency,
            //                         'inactive' => $bv->inactive,
            //                         'tin' => $bv->tin,
            //                         'machine_no' => $bv->machine_no,
            //                         'bir' => $bv->bir,
            //                         'permit_no' => $bv->permit_no,
            //                         'serial' => $bv->serial,
            //                         'accrdn' => $bv->accrdn,
            //                         'email' => $bv->email,
            //                         'website' => $bv->website,
            //                         'store_open' => $bv->store_open,
            //                         'store_close' => $bv->store_close,
            //                     );
            //                 }


            //                 $print_str .= align_center("FOR AYALA",PAPER_WIDTH," ")."\r\n";
            //                 $print_str .= align_center($branch['name'],PAPER_WIDTH," ")."\r\n";
            //                 $print_str .= align_center($branch['address'],PAPER_WIDTH," ")."\r\n\r\n";
            //                 $print_str .= align_center("CONSOLIDATED REPORT Z-READ",PAPER_WIDTH," ")."\r\n\r\n";


            //                 $total_daily_sales = $total_vatA = $total_rawgrossA = $total_discount = $total_refund = $total_void = $total_charge = $total_non_tax = $total_trans_count = $total_guest = 0;


            //                 $paytype = array();
            //                 foreach ($payments_types as $code => $val) {
            //                     if($code != 'credit'){
            //                         if(!isset($paytype[$code])){
            //                             $paytype[$code] = array('amount'=>$val['amount']);
            //                         }else{
            //                             $row = $paytype[$code];
            //                             $row['amount'] += $val['amount'];
            //                             $paytype[$code] = $row;
            //                         }
            //                     }
            //                     // $print_str .= append_chars(substrwords(ucwords(strtoupper($code)),12,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
            //                     //               .append_chars(num($val['amount'],2),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //                     // $pay_qty += $val['qty'];
            //                 }
            //                 $paycards = array();
            //                 if($payments['cards']){
            //                     $cards = $payments['cards'];
            //                     foreach($cards as $key => $val){
            //                         if(!isset($paycards[$key])){
            //                             $paycards[$key] = array('amount'=>$val['amount']);
            //                         }else{
            //                             $row = $paycards[$key];
            //                             $row['amount'] += $val['amount'];
            //                             $paycards[$key] = $row;
            //                         }
            //                     }
            //                 }


            //                 // for server
            //                 $rawgrossA = numInt($gross + $charges + $void + $local_tax);
            //                 $vatA = numInt(($rawgrossA  - $discounts - $void  -  $charges - $nontaxable - $local_tax - numInt($less_vat)) * (1/9.333333));
            //                 $dlySaleA = numInt($rawgrossA - $discounts - $void - $charges - $vatA - $less_vat + $local_tax);
            //                 // $t_discounts = $discounts+$less_vat;
            //                 $rawgrossA =  $rawgrossA - $less_vat;
            //                 $t_discounts = $discounts;



            //                 $trans_count = 0;
            //                 $begor = 0;
            //                 $endor = 0;
            //                 $first_inv = array();
            //                 $last_inv = array();
            //                 $first_ref = 0;
            //                 $last_ref = 0;
            //                 $first_val = 0;
            //                 $last_val = 0;
            //                 $invs = array();
            //                 foreach ($trans['all_orders'] as $ord) {
            //                     if($ord->type_id == SALES_TRANS && $ord->trans_ref != ""){
            //                         $ref = $ord->trans_ref;
            //                         if (preg_match('/^(\D*?)(\d+)(.*)/', $ref, $result) == 1){
            //                             // if($ord->inactive != 1){
            //                                 list($all, $prefix, $number, $postfix) = $result;
            //                                 $ref_val = intval($number);
            //                                 $invs[$ref_val] = array("ref"=>$ord->trans_ref,"val"=>$ref_val);
            //                             // }
            //                         }
            //                     }
            //                 }
            //                 ksort($invs);
            //                 // echo "<pre>",print_r($invs),"</pre>";die();
            //                 $first_inv = reset($invs);
            //                 $last_inv = end($invs);
            //                 if(count($first_inv) > 0){
            //                     $first_ref = $first_inv['ref'];
            //                     $first_val = $first_inv['val'];
            //                 }
            //                 if(count($last_inv) > 0){
            //                     $last_ref = $last_inv['ref'];
            //                     $last_val = $last_inv['val'];
            //                 }
            //                 if(count($invs) > 0){
            //                     $trans_count = ($last_val - $first_val) + 1; 
            //                 }

            //                 // echo $trans_count; die();
            //                 //add yun mga value ng server sa totals
            //                 $total_daily_sales += $dlySaleA;
            //                 $total_vatA += $vatA;
            //                 $total_rawgrossA += $rawgrossA;
            //                 $total_discount += $t_discounts;
            //                 $total_void += $void;
            //                 $total_charge += $charges;
            //                 $total_non_tax += $nontaxable;
            //                 // $total_trans_count += $tc_qty;
            //                 $total_trans_count += $trans_count;
            //                 $total_guest += $guestCount;
            //                  // echo $total_trans_count;

            //                 $terminals = $this->setup_model->get_terminals();


            //                 $print_str .= append_chars(substrwords('Daily Sales',25,""),"right",22," ").align_center('',2," ")
            //                              .append_chars(num($total_daily_sales),"left",10," ")."\r\n";
            //                 $print_str .= append_chars(substrwords('Total Discount',25,""),"right",22," ").align_center('',2," ")
            //                              .append_chars(num($total_discount),"left",10," ")."\r\n";
            //                 $print_str .= append_chars(substrwords('Total Refund',25,""),"right",22," ").align_center('',2," ")
            //                              .append_chars(num(0),"left",10," ")."\r\n";
            //                 $print_str .= append_chars(substrwords('Total Void',25,""),"right",22," ").align_center('',2," ")
            //                              .append_chars(num($total_void),"left",10," ")."\r\n";
            //                 $print_str .= append_chars(substrwords('Total Vat',25,""),"right",22," ").align_center('',2," ")
            //                              .append_chars(num($total_vatA),"left",10," ")."\r\n";
            //                 $print_str .= append_chars(substrwords('Total Service Charge',25,""),"right",22," ").align_center('',2," ").append_chars(num($total_charge),"left",10," ")."\r\n";  


            //                 // $print_str .= append_chars(substrwords('Vatable Sales',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                 //              .append_chars(num($dlySaleA-$nontaxable),"left",PAPER_RD_COL_3_3," ")."\r\n";             
            //                 $print_str .= append_chars(substrwords('Total Non Taxable',22,""),"right",22," ").align_center('',2," ")
            //                              .append_chars(num($total_non_tax),"left",10," ")."\r\n";
            //                 $print_str .= append_chars(substrwords('Row Gross',22,""),"right",22," ").align_center('',2," ")
            //                              .append_chars(num($total_rawgrossA),"left",10," ")."\r\n";             
            //                 $print_str .= append_chars(substrwords('Transaction Count',22,""),"right",22," ").align_center('',2," ")
            //                              .append_chars($total_trans_count,"left",10," ")."\r\n";
            //                 $print_str .= append_chars(substrwords('Customer Count',22,""),"right",22," ").align_center('',2," ")
            //                              .append_chars($total_guest,"left",10," ")."\r\n";
            //                 foreach ($paytype as $k => $v) {
            //                     $print_str .= append_chars(strtoupper($k),"right",22," ").align_center('',2," ")
            //                          .append_chars(num($v['amount']),"left",10," ")."\r\n";       
            //                 }
            //                 foreach ($paycards as $k => $v) {
            //                     $print_str .= append_chars(strtoupper($k),"right",22," ").align_center('',2," ")
            //                          .append_chars(num($v['amount']),"left",10," ")."\r\n";       
            //                 }
            //                 $terminals = $this->setup_model->get_terminals();
            //                 // echo "<pre>",print_r($terminals),"</pre>";die();
            //                 foreach ($terminals as $k => $val) {
            //                     $print_str .= append_chars('BIR PERMIT '.$val->terminal_id,"right",15," ").align_center('',2," ")
            //                          .append_chars($val->permit,"left",17," ")."\r\n";
            //                     $print_str .= append_chars('SERIAL NO. '.$val->terminal_id,"right",15," ").align_center('',2," ")
            //                          .append_chars($val->serial,"left",17," ")."\r\n";
            //                 }           
            //                 // $print_str .= append_chars(substrwords('Less SC Disc',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                 //              .append_chars(num($discounts),"left",PAPER_RD_COL_3_3," ")."\r\n";             
            //                 // $print_str .= append_chars(substrwords('Vat Exempt',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                 //              .append_chars(num($less_vat),"left",PAPER_RD_COL_3_3," ")."\r\n";             
            //                 // $print_str .= append_chars(substrwords('Zero Rated',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                 //              .append_chars(num($zero_rated),"left",PAPER_RD_COL_3_3," ")."\r\n";             
            //                 // $print_str .= PAPER_LINE_SINGLE."\r\n";
            //                 // $print_str .= append_chars(substrwords('Net Sales',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                 //              .append_chars(num($dlySaleA+$vatA),"left",PAPER_RD_COL_3_3," ")."\r\n";             
            //                 // $print_str .= append_chars(substrwords('Total Qty Sold',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //                 //              .append_chars(num($cat_total_qty),"left",PAPER_RD_COL_3_3," ")."\r\n";             
            //                 $print_str .= PAPER_LINE."\r\n";
            //                 $print_str .= align_center(sql2Date($post['from']),PAPER_WIDTH," ")."\r\n";
            //                 $print_str .= align_center("END OF REPORT",PAPER_WIDTH," ")."\r\n";


            //             }
            //         ####################################
            //     } 
            // if ($return_print_str) {
            //     return $print_str;
            // }   

            // // if($title_name == "ZREAD"){
            // //     if($asJson == false){
            // //         $this->manager_model->add_event_logs($user['id'],"ZREAD","View");    
            // //     }else{
            // //         $this->manager_model->add_event_logs($user['id'],"ZREAD","Print");                    
            // //     } 
            // // }elseif($title_name == "XREAD"){
            // //     if($asJson == false){
            // //         $this->manager_model->add_event_logs($user['id'],"XREAD","View");    
            // //     }else{
            // //         $this->manager_model->add_event_logs($user['id'],"XREAD","Print");                    
            // //     } 
            // // }else{
            // //     if($asJson == false){
            // //         $this->manager_model->add_event_logs($user['id'],"System Sales","View");    
            // //     }else{
            // //         $this->manager_model->add_event_logs($user['id'],"System Sales","Print");                    
            // //     }
            // // }  
            // // if(PRINT_VERSION && PRINT_VERSION == 'V2'){
            // //     $this->do_print_v2($print_str,$asJson);  
            // // }else if(PRINT_VERSION && PRINT_VERSION == 'V3' && $asJson){
            // //     echo $this->html_print($print_str);
            // // }else{
            //     $this->session->set_userdata('pdf_data','<pre>'.$print_str.'</pre>') ; 
            //     $this->do_print($print_str,$asJson);
            // // }
            // // $this->do_print($print_str,$asJson);
            $gross = $trans_menus['gross'];
            
            $net = $trans['net'];
            $void = $trans['void'];
            $cancelled = $trans['cancel_amount'];
            $charges = $trans_charges['total'];
            $discounts = $trans_discounts['total'];
            $local_tax = $trans_local_tax['total'];
            // echo $gross.' - '.$charges.' - '.$discounts.' - '.$net; die();
            $less_vat = (($gross+$charges+$local_tax) - $discounts) - $net;
            // $less_vat = $trans_discounts['vat_exempt_total'];

            // echo $gross.'+'.$charges.'+'.$local_tax.' - '.$discounts.' - '.$net;
            // die();

            if($less_vat < 0)
                $less_vat = 0;
            // var_dump($less_vat);

            //para mag tugmam yun payments and netsale
            // $net_sales2 = $gross + $charges - $discounts - $less_vat;
            // $diffs = $net_sales2 - $payments['total'];
            // if($diffs < 1){
            //     $less_vat = $less_vat + $diffs;
            // }
            

            $tax = $trans_tax['total'];
            $no_tax = $trans_no_tax['total'];
            $zero_rated = $trans_zero_rated['total'];
            $no_tax -= $zero_rated;

            $title_name = "SYSTEM SALES REPORT";
            if($post['title'] != "")
                $title_name = $post['title'];

            $print_str .= align_center($title_name,PAPER_WIDTH," ")."\r\n";
            $print_str .= align_center("TERMINAL ".$post['terminal'],PAPER_WIDTH," ")."\r\n";
            $print_str .= append_chars('Printed On','right',11," ").append_chars(": ".date2SqlDateTime($time),'right',19," ")."\r\n";
            $print_str .= append_chars('Printed BY','right',11," ").append_chars(": ".$user['full_name'],'right',19," ")."\r\n";
            $print_str .= PAPER_LINE."\r\n";
            $print_str .= align_center(sql2DateTime($post['from'])." - ".sql2DateTime($post['to']),PAPER_WIDTH," ")."\r\n";
            if($post['employee'] != "All")
                $print_str .= align_center($post['employee'],PAPER_WIDTH," ")."\r\n";
            $print_str .= PAPER_LINE."\r\n";

            $loc_txt = numInt(($local_tax));
            $net_no_adds = $net-($charges+$local_tax);
            // $nontaxable = $no_tax;
            //binago 9/25/2018 for zreading adjustment of vat exempt equal to the receipt vat exempt
            $nontaxable = $no_tax - $no_tax_disc;
            // echo $gross.' - '.$less_vat.' - '.$nontaxable.' - '.$zero_rated; die();
            // $taxable = ($gross - $less_vat - $nontaxable - $zero_rated) / 1.12;
            // 1.12; binago din para sa adjustment of vat exempt equal to the receipt vat exempt
            // $taxable =   ($gross - $discounts - $less_vat - $nontaxable) / 1.12;
            $taxable =   ($gross - $less_vat - $nontaxable - $zero_rated - $discounts) / 1.12; //change computation conflict for zero rated 10 17 2018
            $total_net = ($taxable) + ($nontaxable+$zero_rated) + $tax + $local_tax;
            $add_gt = $taxable+$nontaxable+$zero_rated;
            $nsss = $taxable +  $nontaxable +  $zero_rated;

            #GENERAL
                $print_str .= append_chars(substrwords('TOTAL SALES',18,""),"right",21," ")
                                         .append_chars(num($gross + $charges,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords(ucwords(strtoupper('SC/PWD VAT EXEMPT')),18,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars('-'.num($less_vat,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars('',"right",12," ").align_center('',PAPER_TOTAL_COL_2," ")
                                  .append_chars('----------',"left",PAPER_TOTAL_COL_2," ")."\r\n";

                $print_str .= append_chars(substrwords('GROSS SALES',18,""),"right",21," ")
                                         .append_chars(num($gross + $charges - $less_vat,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";

                // $types = $trans_charges['types'];
                // $qty = 0;
                // foreach ($types as $code => $val) {
                //     $amount = $val['amount'];
                //     $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",PAPER_TOTAL_COL_1," ")
                //                          .append_chars('-'.num($amount,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                //     $qty += $val['qty'];
                // }
                $types = $trans_discounts['types'];
                $qty = 0;
                foreach ($types as $code => $val) {
                    if($code != 'DIPLOMAT'){
                        $amount = $val['amount'];
                        // if(MALL == 'megamall' && $code == PWDDISC){
                        //     $amount = $val['amount'] / 1.12;
                        // }
                        $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",21," ")
                                             .append_chars('-'.Num($amount,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                        $qty += $val['qty'];
                    }
                }
                $print_str .= append_chars('',"right",12," ").align_center('',PAPER_TOTAL_COL_2," ")
                                  .append_chars('----------',"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $net_sales = $gross + $charges - $discounts - $less_vat;
                $print_str .= append_chars(substrwords(ucwords(strtoupper('NET SALES')),18,""),"right",21," ")
                                         .append_chars(num($net_sales,2),"left",PAPER_TOTAL_COL_2," ")."\r\n\r\n";
            #PAYMENTS
                $payments_types = $payments['types'];
                $payments_total = $payments['total'];
                $pay_qty = 0;
            #SUMMARY
                $final_gross = $gross;
                $vat_ = $taxable * .12;
                $print_str .= append_chars(substrwords('VAT SALES',23,""),"right",21," ")
                                         .append_chars(num($taxable,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords('VAT',23,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(num($vat_,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                // if(IS_VATABLE_STORE){
                    $print_str .= append_chars(substrwords('VAT EXEMPT SALES',23,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(num($nontaxable,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";

                // }else{
                //     $print_str .= append_chars(substrwords('NONVAT SALES',23,""),"right",PAPER_TOTAL_COL_1," ")
                //                          .append_chars(num($nontaxable,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                // }
                                         // .append_chars(numInt($nontaxable-$zero_rated),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords('ZERO RATED',23,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(num($zero_rated,2),"left",PAPER_TOTAL_COL_2," ")."\r\n\r\n";
                $print_str .= append_chars(substrwords('Payment Breakdown:',18,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
                              .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
                foreach ($payments_types as $code => $val) {
                    $print_str .= append_chars(substrwords(ucwords(strtoupper($code)),18,""),"right",12," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                                  .append_chars(num($val['amount'],2),"left",16," ")."\r\n";
                    $pay_qty += $val['qty'];
                }
                $print_str .= append_chars('',"right",18," ").align_center('',PAPER_RD_COL_2," ")
                                  .append_chars('----------',"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= append_chars(substrwords('TOTAL PAYMENTS',18,""),"right",14," ").align_center($pay_qty,PAPER_RD_COL_2," ")
                              .append_chars(num($payments_total,2),"left",14," ")."\r\n\r\n";
                $print_str .= PAPER_LINE_SINGLE."\r\n";
                $gross_less_disc = $final_gross - $discounts - $less_vat;
                // $print_str .= append_chars(substrwords('NET SALES',23,""),"right",PAPER_TOTAL_COL_1," ")
                //                          // .append_chars(numInt(($taxable + $nontaxable + $zero_rated + $vat_)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                //                          .append_chars(numInt(($taxable + $nontaxable + $vat_)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                // $print_str .= PAPER_LINE."\r\n";

                if(count($payments['currency']) > 0){
                    $currency = $payments['currency'];
                    $print_str .= append_chars(substrwords('Currency Breakdown:',20,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
                                  .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
                    foreach ($currency as $code => $val) {
                        $print_str .= append_chars(substrwords(ucwords(strtoupper($code)),18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ").append_chars(num($val['amount'],2),"left",PAPER_RD_COL_3_3," ")."\r\n";
                        // $pay_qty += $val['qty'];
                    }
                }

                // $print_str .= PAPER_LINE_SINGLE."\r\n";
                $print_str .= "\r\n\r\n";



                $print_str .= append_chars(substrwords('VOID SALES',18,""),"right",PAPER_TOTAL_COL_1," ")
                             .append_chars(num(($void),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords('CANCELLED TRANS',18,""),"right",PAPER_TOTAL_COL_1," ")
                             .append_chars(num(($cancelled),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";

                $cancelled_order = $this->cancelled_orders();
                $co = $cancelled_order['cancelled_order'];
                $print_str .= append_chars(substrwords('CANCELLED ORDERS',18,""),"right",PAPER_TOTAL_COL_1," ")
                             .append_chars(num(($co),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords('Local Tax',18,""),"right",PAPER_TOTAL_COL_1," ")
                             .append_chars($loc_txt,"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= "\r\n";
            #TRANS COUNT
                $types = $trans['types'];
                $types_total = array();
                $guestCount = 0;
                foreach ($types as $type => $tp) {
                    foreach ($tp as $id => $opt){
                        if(isset($types_total[$type])){
                            $types_total[$type] += round($opt->total_amount,2);

                        }
                        else{
                            $types_total[$type] = round($opt->total_amount,2);
                        }

                        // if($opt->type == 'dinein'){
                        //     $guestCount += $opt->guest;
                        // }
                        if($opt->guest == 0)
                            $guestCount += 1;
                        else
                            $guestCount += $opt->guest;
                    }
                }
                $print_str .= append_chars(substrwords('Trans Count:',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars('',"left",PAPER_RD_COL_3_3," ")."\r\n";
                $tc_total  = 0;
                $tc_qty = 0;
                foreach ($types_total as $typ => $tamnt) {
                    $print_str .= append_chars(substrwords($typ,18,""),"right",12," ").align_center(count($types[$typ]),PAPER_RD_COL_2," ")
                                 .append_chars(num($tamnt,2),"left",16," ")."\r\n";
                    $tc_total += $tamnt;
                    $tc_qty += count($types[$typ]);
                }
                $print_str .= "-----------------"."\r\n";
                $print_str .= append_chars(substrwords('TC Total',18,""),"right",21," ")
                             .append_chars(num($tc_total,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords('GUEST Total',18,""),"right",PAPER_TOTAL_COL_1," ")
                             .append_chars($guestCount,"left",PAPER_TOTAL_COL_2," ")."\r\n";
                // if($tc_total == 0 || $tc_qty == 0)
                //     $avg = 0;
                // else
                //     $avg = $tc_total/$tc_qty;
                if($net_sales){
                    if($guestCount == 0){
                        $avg = 0;
                    }else{
                        $avg = $net_sales/$guestCount;
                    }
                }else{
                    $avg = 0;
                }


                $print_str .= append_chars(substrwords('AVG Check',18,""),"right",PAPER_TOTAL_COL_1," ")
                             .append_chars(num($avg,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= "\r\n";
            #CHARGES
                $types = $trans_charges['types'];
                $qty = 0;
                $print_str .= append_chars(substrwords('Charges:',18,""),"right",18," ").align_center(null,5," ")
                              .append_chars(null,"left",13," ")."\r\n";
                foreach ($types as $code => $val) {
                    $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                                  .append_chars(num($val['amount'],2),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $qty += $val['qty'];
                }
                $print_str .= "-----------------"."\r\n";
                $print_str .= append_chars(substrwords('Total Charges',18,""),"right",PAPER_RD_COL_1," ").align_center($qty,PAPER_RD_COL_2," ")
                              .append_chars(num($charges,2),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= "\r\n";
            #Discounts
                $types = $trans_discounts['types'];
                $qty = 0;
                $print_str .= append_chars(substrwords('Discounts:',18,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
                              .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
                foreach ($types as $code => $val) {
                    if($code != 'DIPLOMAT'){
                        $amount = $val['amount'];
                        // if(MALL == 'megamall' && $code == PWDDISC){
                        //     $amount = $val['amount'] / 1.12;
                        // }
                        $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",14," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                                      .append_chars(num($amount,2),"left",14," ")."\r\n";
                        $qty += $val['qty'];
                    }
                }
                $print_str .= "-----------------"."\r\n";
                $print_str .= append_chars(substrwords('Total Discounts',18,""),"right",14," ").align_center($qty,PAPER_RD_COL_2," ")
                              .append_chars(num($discounts,2),"left",13," ")."\r\n";
                $print_str .= append_chars(substrwords('VAT EXEMPT',18,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(num($less_vat,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= "\r\n";
            #PAYMENTS
                $payments_types = $payments['types'];
                $payments_total = $payments['total'];
                $pay_qty = 0;
                $print_str .= append_chars(substrwords('Payment Breakdown:',18,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
                              .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
                foreach ($payments_types as $code => $val) {
                    $print_str .= append_chars(substrwords(ucwords(strtolower($code)),18,""),"right",12," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                                  .append_chars(num($val['amount'],2),"left",16," ")."\r\n";
                    $pay_qty += $val['qty'];
                }
                $print_str .= "-----------------"."\r\n";
                $print_str .= append_chars(substrwords('Total Payments',18,""),"right",14," ").align_center($pay_qty,PAPER_RD_COL_2," ")
                              .append_chars(num($payments_total,2),"left",14," ")."\r\n";
                $print_str .= "\r\n";

                //card breakdown
                if($payments['cards']){
                    $cards = $payments['cards'];
                    $card_total = 0;
                    $count_total = 0;
                    $print_str .= append_chars(substrwords('Card Breakdown:',18,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
                              .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
                    foreach($cards as $key => $val){
                        $print_str .= append_chars(substrwords($key,18,""),"right",12," ").align_center($val['count'],PAPER_RD_COL_2," ")
                                  .append_chars(num($val['amount'],2),"left",16," ")."\r\n";
                        $card_total += $val['amount'];
                        $count_total += $val['count'];
                    }
                    $print_str .= "-----------------"."\r\n";
                    $print_str .= append_chars(substrwords('Total',18,""),"right",12," ").align_center($count_total,PAPER_RD_COL_2," ")
                              .append_chars(num($card_total,2),"left",16," ")."\r\n";
                    
                    $print_str .= "\r\n";
                }

                //get all gc with excess
                if($payments['gc_excess']){
                    $print_str .= append_chars(substrwords('GC EXCESS',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                                  .append_chars(num($payments['gc_excess'],2),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $print_str .= "\r\n";
                }

                //show all sign chit
                // $trans['sales']
                if($trans['total_chit']){
                    $print_str .= append_chars(substrwords('TOTAL CHIT',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                                  .append_chars(num($trans['total_chit'],2),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $print_str .= "\r\n";
                }
            #CATEGORIES
                $cats = $trans_menus['cats'];
                $print_str .= append_chars('Menu Categories:',"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars('',"left",PAPER_RD_COL_3," ")."\r\n";
                $qty = 0;
                $total = 0;
                foreach ($cats as $id => $val) {
                    if($val['qty'] > 0){
                        $print_str .= append_chars(substrwords($val['name'],18,""),"right",15," ").align_center($val['qty'],5," ")
                                   .append_chars(num($val['amount'],2),"left",13," ")."\r\n";
                        $qty += $val['qty'];
                        $total += $val['amount'];
                    }
                 }
                $print_str .= "-----------------"."\r\n";
                $cat_total_qty = $qty;
                $print_str .= append_chars("SubTotal","right",12," ").align_center($qty,PAPER_RD_COL_2," ")
                              .append_chars(num($total,2),"left",16," ")."\r\n";
                $print_str .= append_chars("Modifiers Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                              .append_chars(num($trans_menus['mods_total'],2),"left",PAPER_RD_COL_3_3," ")."\r\n";
                 $print_str .= append_chars("SubModifier Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                              .append_chars(num($trans_menus['submods_total'],2),"left",PAPER_RD_COL_3_3," ")."\r\n";
                if($trans_menus['item_total'] > 0){
                 $print_str .= append_chars("Retail Items Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                               .append_chars(num($trans_menus['item_total'],2),"left",PAPER_RD_COL_3_3," ")."\r\n";
                }

                $print_str .= append_chars("Total","right",12," ").align_center('',PAPER_RD_COL_2," ")
                              .append_chars(num($total+$trans_menus['mods_total']+$trans_menus['item_total']+$trans_menus['submods_total'],2),"left",16," ")."\r\n";
                $print_str .= "\r\n";
            #SUBCATEGORIES
                $subcats = $trans_menus['sub_cats'];
                // $print_str .= append_chars('Menu Subcategories:',"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                $print_str .= append_chars('Menu Types:',"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars('',"left",PAPER_RD_COL_3," ")."\r\n";
                $qty = 0;
                $total = 0;
                foreach ($subcats as $id => $val) {
                    $print_str .= append_chars($val['name'],"right",12," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                               .append_chars(num($val['amount'],2),"left",16," ")."\r\n";
                    $qty += $val['qty'];
                    $total += $val['amount'];
                 }
                $print_str .= "-----------------"."\r\n";
                $print_str .= append_chars("Total","right",12," ").align_center($qty,PAPER_RD_COL_2," ")
                              .append_chars(num($total,2),"left",16," ")."\r\n";
                // $print_str .= append_chars("Modifiers Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                //               .append_chars(numInt($trans_menus['mods_total']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                // $print_str .= append_chars("SubModifier Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                //               .append_chars(numInt($trans_menus['submods_total']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                // $print_str .= append_chars("Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                //               .append_chars(numInt($total+$trans_menus['mods_total']+$trans_menus['submods_total']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= "\r\n";
            #FREE MENUS
                $free = $trans_menus['free_menus'];
                $print_str .= append_chars('Free Menus:',"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars('',"left",PAPER_RD_COL_3," ")."\r\n";
                $fm = array();
                foreach ($free as $ms) {
                    if(!isset($fm[$ms->menu_id])){
                        $mn = array();
                        $mn['name'] = $ms->menu_name;
                        $mn['cat_id'] = $ms->cat_id;
                        $mn['qty'] = $ms->qty;
                        $mn['amount'] = $ms->sell_price * $ms->qty;
                        $mn['sell_price'] = $ms->sell_price;
                        $mn['code'] = $ms->menu_code;
                        // $mn['free_user_id'] = $ms->free_user_id;
                        $fm[$ms->menu_id] = $mn;
                    }
                    else{
                        $mn = $fm[$ms->menu_id];
                        $mn['qty'] += $ms->qty;
                        $mn['amount'] += $ms->sell_price * $ms->qty;
                        $fm[$ms->menu_id] = $mn;
                    }
                }
                $qty = 0;
                $total = 0;
                foreach ($fm as $menu_id => $val) {
                    $print_str .= append_chars($val['name'],"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                               .append_chars(($val['qty']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $qty += $val['qty'];
                    $total += $val['amount'];
                }
                $print_str .= "-----------------"."\r\n";
                $print_str .= append_chars("Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                              .append_chars(($qty),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= "\r\n";
                $print_str .= "\r\n";    
            #FOOTER
                $print_str .= append_chars(substrwords('Invoice Start: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars(iSetObj($trans['first_ref'],'trans_ref'),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= append_chars(substrwords('Invoice End: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars(iSetObj($trans['last_ref'],'trans_ref'),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= append_chars(substrwords('Invoice Ctr: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars($trans['ref_count'],"left",PAPER_RD_COL_3_3," ")."\r\n";
                if($title_name == "ZREAD"){
                    $gt = $this->old_grand_net_total($post['from']);
                    $print_str .= "\r\n";
                    $print_str .= append_chars(substrwords('OLD GT: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                                 .append_chars(numInt( $gt['old_grand_total']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $print_str .= append_chars(substrwords('NEW GT: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                                 .append_chars( numInt($gt['old_grand_total']+$net_no_adds)  ,"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $print_str .= append_chars(substrwords('Z READ CTR: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                                 .append_chars( $gt['ctr'] ,"left",PAPER_RD_COL_3_3," ")."\r\n";
                }
                $print_str .= PAPER_LINE."\r\n";
            #MALLS
                if(MALL_ENABLED){
                    ####################################
                    # AYALA
                        if(MALL == 'ayala'){
                            
                            $branch_details = $this->setup_model->get_branch_details();
                            $branch = array();
                            foreach ($branch_details as $bv) {
                                $branch = array(
                                    'id' => $bv->branch_id,
                                    'res_id' => $bv->res_id,
                                    'branch_code' => $bv->branch_code,
                                    'name' => $bv->branch_name,
                                    'branch_desc' => $bv->branch_desc,
                                    'contact_no' => $bv->contact_no,
                                    'delivery_no' => $bv->delivery_no,
                                    'address' => $bv->address,
                                    'base_location' => $bv->base_location,
                                    'currency' => $bv->currency,
                                    'inactive' => $bv->inactive,
                                    'tin' => $bv->tin,
                                    'machine_no' => $bv->machine_no,
                                    'bir' => $bv->bir,
                                    'permit_no' => $bv->permit_no,
                                    'serial' => $bv->serial,
                                    'accrdn' => $bv->accrdn,
                                    'email' => $bv->email,
                                    'website' => $bv->website,
                                    'store_open' => $bv->store_open,
                                    'store_close' => $bv->store_close,
                                );
                            }


                            $print_str .= align_center("FOR AYALA",PAPER_WIDTH," ")."\r\n";
                            $print_str .= align_center($branch['name'],PAPER_WIDTH," ")."\r\n";
                            $print_str .= align_center($branch['address'],PAPER_WIDTH," ")."\r\n\r\n";
                            $print_str .= align_center("CONSOLIDATED REPORT Z-READ",PAPER_WIDTH," ")."\r\n\r\n";


                            $total_daily_sales = $total_vatA = $total_rawgrossA = $total_discount = $total_refund = $total_void = $total_charge = $total_non_tax = $total_trans_count = $total_guest = 0;


                            $paytype = array();
                            foreach ($payments_types as $code => $val) {
                                if($code != 'credit'){
                                    if(!isset($paytype[$code])){
                                        $paytype[$code] = array('amount'=>$val['amount']);
                                    }else{
                                        $row = $paytype[$code];
                                        $row['amount'] += $val['amount'];
                                        $paytype[$code] = $row;
                                    }
                                }
                                // $print_str .= append_chars(substrwords(ucwords(strtoupper($code)),12,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                                //               .append_chars(num($val['amount'],2),"left",PAPER_RD_COL_3_3," ")."\r\n";
                                // $pay_qty += $val['qty'];
                            }
                            $paycards = array();
                            if($payments['cards']){
                                $cards = $payments['cards'];
                                foreach($cards as $key => $val){
                                    if(!isset($paycards[$key])){
                                        $paycards[$key] = array('amount'=>$val['amount']);
                                    }else{
                                        $row = $paycards[$key];
                                        $row['amount'] += $val['amount'];
                                        $paycards[$key] = $row;
                                    }
                                }
                            }


                            // for server
                            $rawgrossA = numInt($gross + $charges + $void + $local_tax);
                            $vatA = numInt(($rawgrossA  - $discounts - $void  -  $charges - $nontaxable - $local_tax - numInt($less_vat)) * (1/9.333333));
                            $dlySaleA = numInt($rawgrossA - $discounts - $void - $charges - $vatA - $less_vat + $local_tax);
                            // $t_discounts = $discounts+$less_vat;
                            $rawgrossA =  $rawgrossA - $less_vat;
                            $t_discounts = $discounts;



                            $trans_count = 0;
                            $begor = 0;
                            $endor = 0;
                            $first_inv = array();
                            $last_inv = array();
                            $first_ref = 0;
                            $last_ref = 0;
                            $first_val = 0;
                            $last_val = 0;
                            $invs = array();
                            foreach ($trans['all_orders'] as $ord) {
                                if($ord->type_id == SALES_TRANS && $ord->trans_ref != ""){
                                    $ref = $ord->trans_ref;
                                    if (preg_match('/^(\D*?)(\d+)(.*)/', $ref, $result) == 1){
                                        // if($ord->inactive != 1){
                                            list($all, $prefix, $number, $postfix) = $result;
                                            $ref_val = intval($number);
                                            $invs[$ref_val] = array("ref"=>$ord->trans_ref,"val"=>$ref_val);
                                        // }
                                    }
                                }
                            }
                            ksort($invs);
                            // echo "<pre>",print_r($invs),"</pre>";die();
                            $first_inv = reset($invs);
                            $last_inv = end($invs);
                            if(count($first_inv) > 0){
                                $first_ref = $first_inv['ref'];
                                $first_val = $first_inv['val'];
                            }
                            if(count($last_inv) > 0){
                                $last_ref = $last_inv['ref'];
                                $last_val = $last_inv['val'];
                            }
                            if(count($invs) > 0){
                                $trans_count = ($last_val - $first_val) + 1; 
                            }

                            // echo $trans_count; die();
                            //add yun mga value ng server sa totals
                            $total_daily_sales += $dlySaleA;
                            $total_vatA += $vatA;
                            $total_rawgrossA += $rawgrossA;
                            $total_discount += $t_discounts;
                            $total_void += $void;
                            $total_charge += $charges;
                            $total_non_tax += $nontaxable;
                            // $total_trans_count += $tc_qty;
                            $total_trans_count += $trans_count;
                            $total_guest += $guestCount;
                             // echo $total_trans_count;

                            $terminals = $this->setup_model->get_terminals();


                            $print_str .= append_chars(substrwords('Daily Sales',25,""),"right",22," ").align_center('',2," ")
                                         .append_chars(num($total_daily_sales),"left",10," ")."\r\n";
                            $print_str .= append_chars(substrwords('Total Discount',25,""),"right",22," ").align_center('',2," ")
                                         .append_chars(num($total_discount),"left",10," ")."\r\n";
                            $print_str .= append_chars(substrwords('Total Refund',25,""),"right",22," ").align_center('',2," ")
                                         .append_chars(num(0),"left",10," ")."\r\n";
                            $print_str .= append_chars(substrwords('Total Void',25,""),"right",22," ").align_center('',2," ")
                                         .append_chars(num($total_void),"left",10," ")."\r\n";
                            $print_str .= append_chars(substrwords('Total Vat',25,""),"right",22," ").align_center('',2," ")
                                         .append_chars(num($total_vatA),"left",10," ")."\r\n";
                            $print_str .= append_chars(substrwords('Total Service Charge',25,""),"right",22," ").align_center('',2," ").append_chars(num($total_charge),"left",10," ")."\r\n";  


                            // $print_str .= append_chars(substrwords('Vatable Sales',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                            //              .append_chars(num($dlySaleA-$nontaxable),"left",PAPER_RD_COL_3_3," ")."\r\n";             
                            $print_str .= append_chars(substrwords('Total Non Taxable',22,""),"right",22," ").align_center('',2," ")
                                         .append_chars(num($total_non_tax),"left",10," ")."\r\n";
                            $print_str .= append_chars(substrwords('Row Gross',22,""),"right",22," ").align_center('',2," ")
                                         .append_chars(num($total_rawgrossA),"left",10," ")."\r\n";             
                            $print_str .= append_chars(substrwords('Transaction Count',22,""),"right",22," ").align_center('',2," ")
                                         .append_chars($total_trans_count,"left",10," ")."\r\n";
                            $print_str .= append_chars(substrwords('Customer Count',22,""),"right",22," ").align_center('',2," ")
                                         .append_chars($total_guest,"left",10," ")."\r\n";
                            foreach ($paytype as $k => $v) {
                                $print_str .= append_chars(strtoupper($k),"right",22," ").align_center('',2," ")
                                     .append_chars(num($v['amount']),"left",10," ")."\r\n";       
                            }
                            foreach ($paycards as $k => $v) {
                                $print_str .= append_chars(strtoupper($k),"right",22," ").align_center('',2," ")
                                     .append_chars(num($v['amount']),"left",10," ")."\r\n";       
                            }
                            $terminals = $this->setup_model->get_terminals();
                            // echo "<pre>",print_r($terminals),"</pre>";die();
                            foreach ($terminals as $k => $val) {
                                $print_str .= append_chars('BIR PERMIT '.$val->terminal_id,"right",15," ").align_center('',2," ")
                                     .append_chars($val->permit,"left",17," ")."\r\n";
                                $print_str .= append_chars('SERIAL NO. '.$val->terminal_id,"right",15," ").align_center('',2," ")
                                     .append_chars($val->serial,"left",17," ")."\r\n";
                            }           
                            // $print_str .= append_chars(substrwords('Less SC Disc',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                            //              .append_chars(num($discounts),"left",PAPER_RD_COL_3_3," ")."\r\n";             
                            // $print_str .= append_chars(substrwords('Vat Exempt',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                            //              .append_chars(num($less_vat),"left",PAPER_RD_COL_3_3," ")."\r\n";             
                            // $print_str .= append_chars(substrwords('Zero Rated',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                            //              .append_chars(num($zero_rated),"left",PAPER_RD_COL_3_3," ")."\r\n";             
                            // $print_str .= PAPER_LINE_SINGLE."\r\n";
                            // $print_str .= append_chars(substrwords('Net Sales',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                            //              .append_chars(num($dlySaleA+$vatA),"left",PAPER_RD_COL_3_3," ")."\r\n";             
                            // $print_str .= append_chars(substrwords('Total Qty Sold',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                            //              .append_chars(num($cat_total_qty),"left",PAPER_RD_COL_3_3," ")."\r\n";             
                            $print_str .= PAPER_LINE."\r\n";
                            $print_str .= align_center(sql2Date($post['from']),PAPER_WIDTH," ")."\r\n";
                            $print_str .= align_center("END OF REPORT",PAPER_WIDTH," ")."\r\n";


                        }
                    ####################################
                }   

            if ($return_print_str) {
                return $print_str;
            }  

            // if($title_name == "ZREAD"){
            //     if($asJson == false){
            //         $this->manager_model->add_event_logs($user['id'],"ZREAD","View");    
            //     }else{
            //         $this->manager_model->add_event_logs($user['id'],"ZREAD","Print");                    
            //     } 
            // }elseif($title_name == "XREAD"){
            //     if($asJson == false){
            //         $this->manager_model->add_event_logs($user['id'],"XREAD","View");    
            //     }else{
            //         $this->manager_model->add_event_logs($user['id'],"XREAD","Print");                    
            //     } 
            // }else{
            //     if($asJson == false){
            //         $this->manager_model->add_event_logs($user['id'],"System Sales","View");    
            //     }else{
            //         $this->manager_model->add_event_logs($user['id'],"System Sales","Print");                    
            //     }
            // }
            $this->session->set_userdata('pdf_data','<pre>'.$print_str.'</pre>') ;   
            // if(PRINT_VERSION && PRINT_VERSION == 'V2'){
            //     $this->do_print_v2($print_str,$asJson);  
            // }else if(PRINT_VERSION && PRINT_VERSION == 'V3' && $asJson){
            //     echo $this->html_print($print_str);
            // }else{
                $this->do_print($print_str,$asJson);
    }
    public function system_sales_rep_jdmt_aurora($asJson=false,$args=array(),$return_print_str=false,$date,$print_date){
        ////hapchan
        ini_set('memory_limit', '-1');
        set_time_limit(3600);
        
        $print_str = $this->print_header();
        $user = $this->session->userdata('user');
        $time = $this->site_model->get_db_now();
        $post = $this->set_post();
        $curr = $this->search_current();
        $trans = $this->trans_sales($args,$curr);
        // var_dump($trans['net']); die();
        $sales = $trans['sales'];
        $trans_menus = $this->menu_sales($sales['settled']['ids'],$curr);
        $trans_charges = $this->charges_sales($sales['settled']['ids'],$curr);
        $trans_discounts = $this->discounts_sales($sales['settled']['ids'],$curr);
        $tax_disc = $trans_discounts['tax_disc_total'];
        $no_tax_disc = $trans_discounts['no_tax_disc_total'];
        $trans_local_tax = $this->local_tax_sales($sales['settled']['ids'],$curr);
        $trans_tax = $this->tax_sales($sales['settled']['ids'],$curr);
        $trans_no_tax = $this->no_tax_sales($sales['settled']['ids'],$curr);
        $trans_zero_rated = $this->zero_rated_sales($sales['settled']['ids'],$curr);
        $payments = $this->payment_sales($sales['settled']['ids'],$curr);

        $gross = $trans_menus['gross'];
        
        $net = $trans['net'];
        $void = $trans['void'];
        $void_cnt = $trans['void_cnt'];
        $cancelled = $trans['cancel_amount'];
        $cancel_cnt = $trans['cancel_cnt'];
        $charges = $trans_charges['total'];
        $discounts = $trans_discounts['total'];
        $local_tax = $trans_local_tax['total'];
        // echo $gross.' - '.$charges.' - '.$discounts.' - '.$net; die();
        $less_vat = (($gross+$charges+$local_tax) - $discounts) - $net;
        // $less_vat = $trans_discounts['vat_exempt_total'];

        // echo $gross.'+'.$charges.'+'.$local_tax.' - '.$discounts.' - '.$net;
        // die();

        if($less_vat < 0)
            $less_vat = 0;
        // var_dump($less_vat);

        //para mag tugmam yun payments and netsale
        // $net_sales2 = $gross + $charges - $discounts - $less_vat;
        // $diffs = $net_sales2 - $payments['total'];
        // if($diffs < 1){
        //     $less_vat = $less_vat + $diffs;
        // }
        

        $tax = $trans_tax['total'];
        $no_tax = $trans_no_tax['total'];
        $zero_rated = $trans_zero_rated['total'];
        $no_tax -= $zero_rated;

        $title_name = "ZREAD";
        if($post['title'] != "")
            $title_name = $post['title'];

        $print_str .= align_center($title_name,PAPER_WIDTH," ")."\r\n";
        $print_str .= align_center("TERMINAL ".$post['terminal'],PAPER_WIDTH," ")."\r\n";
        $print_str .= append_chars('Printed On','right',11," ").append_chars(": ".date2SqlDateTime($time),'right',19," ")."\r\n";
        $print_str .= append_chars('Printed BY','right',11," ").append_chars(": ".$user['full_name'],'right',19," ")."\r\n";
        $print_str .= PAPER_LINE."\r\n";
        $print_str .= align_center($print_date,PAPER_WIDTH," ")."\r\n";
        if($post['employee'] != "All")
            $print_str .= align_center($post['employee'],PAPER_WIDTH," ")."\r\n";
        $print_str .= PAPER_LINE."\r\n";

        $loc_txt = numInt(($local_tax));
        $net_no_adds = $net-($charges+$local_tax);
        // $nontaxable = $no_tax;
        //binago 9/25/2018 for zreading adjustment of vat exempt equal to the receipt vat exempt
        $nontaxable = $no_tax - $no_tax_disc;
        // echo $gross.' - '.$less_vat.' - '.$nontaxable.' - '.$zero_rated; die();
        // $taxable = ($gross - $less_vat - $nontaxable - $zero_rated) / 1.12;
        // 1.12; binago din para sa adjustment of vat exempt equal to the receipt vat exempt
        // $taxable =   ($gross - $discounts - $less_vat - $nontaxable) / 1.12;
        $taxable =   ($gross - $less_vat - $nontaxable - $zero_rated - $discounts) / 1.12; //change computation conflict for zero rated 10 17 2018
        $total_net = ($taxable) + ($nontaxable+$zero_rated) + $tax + $local_tax;
        $add_gt = $taxable+$nontaxable+$zero_rated;
        $nsss = $taxable +  $nontaxable +  $zero_rated;

        #GENERAL
            $print_str .= append_chars(substrwords('GROSS SALES',18,""),"right",PAPER_TOTAL_COL_1," ")
                                     .append_chars(num($gross + $charges,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            

            // $print_str .= append_chars(substrwords('TOTAL SALES',18,""),"right",PAPER_TOTAL_COL_1," ")
            //                          .append_chars(num($gross + $charges,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            // $print_str .= append_chars(substrwords(ucwords(strtoupper('SC/PWD VAT EXEMPT')),18,""),"right",PAPER_TOTAL_COL_1," ")
            //                          .append_chars('-'.num($less_vat,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            // $print_str .= append_chars('',"right",12," ").align_center('',PAPER_TOTAL_COL_2," ")
            //                   .append_chars('----------',"left",PAPER_TOTAL_COL_2," ")."\r\n";

            // $print_str .= append_chars(substrwords('GROSS SALES',18,""),"right",PAPER_TOTAL_COL_1," ")
            //                          .append_chars(num($gross + $charges - $less_vat,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            $types = $trans_discounts['types'];
            $qty = 0;
            $other_disc = 0;
            foreach ($types as $code => $val) {
                if($code != 'DIPLOMAT'){

                    if($code == 'SNDISC'){
                        $amount = $val['amount'];
                        // if(MALL == 'megamall' && $code == PWDDISC){
                        //     $amount = $val['amount'] / 1.12;
                        // }
                        $print_str .= append_chars(substrwords('20% Senior Disc',30,""),"right",PAPER_TOTAL_COL_1," ")
                                             .append_chars('-'.Num($amount,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                    }elseif($code == 'PWDISC'){
                        $amount = $val['amount'];
                        // if(MALL == 'megamall' && $code == PWDDISC){
                        //     $amount = $val['amount'] / 1.12;
                        // }
                        $print_str .= append_chars(substrwords('PWD Disc',30,""),"right",PAPER_TOTAL_COL_1," ")
                                             .append_chars('-'.Num($amount,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                    }else{
                        $other_disc += $val['amount'];
                    }
                    // $qty += $val['qty'];
                }
            }
            if($other_disc != 0){
                $print_str .= append_chars(substrwords('Total Other Disc',30,""),"right",PAPER_TOTAL_COL_1," ")
                                             .append_chars('-'.Num($other_disc,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            }
            $print_str .= append_chars(substrwords(ucwords('Total VAT Exempt'),18,""),"right",PAPER_TOTAL_COL_1," ")
                                     .append_chars('-'.num($less_vat,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";

            $types = $trans_charges['types'];
            // $qty = 0;
            foreach ($types as $code => $val) {
                $amount = $val['amount'];
                $print_str .= append_chars(substrwords(ucwords($val['name']),18,""),"right",PAPER_TOTAL_COL_1," ")
                                     .append_chars(num($amount,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                // $qty += $val['qty'];
            }


            $print_str .= append_chars('',"right",12," ").align_center('',PAPER_TOTAL_COL_2," ")
                              .append_chars('----------',"left",PAPER_TOTAL_COL_2," ")."\r\n";
            $net_sales = $gross + $charges - $discounts - $less_vat;
            $print_str .= append_chars(substrwords(ucwords('Net Sales'),18,""),"right",PAPER_TOTAL_COL_1," ")
                                     .append_chars(num($net_sales,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            $vat_ = $taxable * .12;
            $print_str .= append_chars(substrwords(ucwords('12% VAT'),18,""),"right",PAPER_TOTAL_COL_1," ")
                                     .append_chars(num($vat_,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";  
            $print_str .= append_chars('',"right",12," ").align_center('',PAPER_TOTAL_COL_2," ")
                              .append_chars('----------',"left",PAPER_TOTAL_COL_2," ")."\r\n";  
            $print_str .= append_chars(substrwords(ucwords('Net Sales w/o VAT'),30,""),"right",PAPER_TOTAL_COL_1," ")
                                     .append_chars(num($net_sales - $vat_,2),"left",PAPER_TOTAL_COL_2," ")."\r\n\r\n";                     
            // $print_str .= append_chars(substrwords(ucwords(strtoupper('NET SALES')),18,""),"right",PAPER_TOTAL_COL_1," ")
                                     // .append_chars(num($net_sales,2),"left",PAPER_TOTAL_COL_2," ")."\r\n\r\n";
        #PAYMENTS
            $payments_types = $payments['types'];
            $payments_total = $payments['total'];
            $pay_qty = 0;
        #SUMMARY
            $final_gross = $gross;
            // $print_str .= append_chars(substrwords('VAT SALES',23,""),"right",PAPER_TOTAL_COL_1," ")
            //                          .append_chars(num(numInt($taxable)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            // $print_str .= append_chars(substrwords('VAT',23,""),"right",PAPER_TOTAL_COL_1," ")
            //                          .append_chars(num(numInt($vat_)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            // $print_str .= append_chars(substrwords('VAT EXEMPT SALES',23,""),"right",PAPER_TOTAL_COL_1," ")
            //                          .append_chars(num(numInt($nontaxable)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            //                          // .append_chars(numInt($nontaxable-$zero_rated),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            // $print_str .= append_chars(substrwords('ZERO RATED',23,""),"right",PAPER_TOTAL_COL_1," ")
            //                          .append_chars(num(numInt($zero_rated)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            // $print_str .= append_chars('',"right",12," ").align_center('',PAPER_TOTAL_COL_2," ")
            //                   .append_chars('----------',"left",PAPER_TOTAL_COL_2," ")."\r\n";
            // // $net_sales = $gross + $charges - $discounts - $less_vat;
            // $nsales = $taxable + $vat_ + $nontaxable + $zero_rated;
            // $print_str .= append_chars(substrwords(ucwords(strtoupper('NET SALES')),18,""),"right",PAPER_TOTAL_COL_1," ")
            //                          .append_chars(num($nsales,2),"left",PAPER_TOTAL_COL_2," ")."\r\n\r\n";

            // $print_str .= append_chars(substrwords('Payment Breakdown:',18,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
                          // .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
            // foreach ($payments_types as $code => $val) {
            //     $print_str .= append_chars(substrwords(ucwords(strtoupper($code)),18,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
            //                   .append_chars(num(numInt($val['amount'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //     $pay_qty += $val['qty'];
            // }
            // $print_str .= append_chars('',"right",18," ").align_center('',PAPER_RD_COL_2," ")
            //                   .append_chars('----------',"left",PAPER_RD_COL_3_3," ")."\r\n";
            // $print_str .= append_chars(substrwords('TOTAL PAYMENTS',18,""),"right",PAPER_RD_COL_1," ").align_center($pay_qty,PAPER_RD_COL_2," ")
            //               .append_chars(num(numInt($payments_total)),"left",PAPER_RD_COL_3_3," ")."\r\n\r\n";

            $vriance = 0;
            if($payments_total != $net_sales){
                $vriance = $net_sales - $payments_total;
            }

            $payments_total += $vriance;


            $print_str .= append_chars(substrwords(ucwords('Begun Total'),18,""),"right",PAPER_TOTAL_COL_1," ")
                                     .append_chars(num($net_sales,2),"left",PAPER_TOTAL_COL_2," ")."\r\n"; 
            $print_str .= append_chars(substrwords(ucwords('Paid Total'),18,""),"right",PAPER_TOTAL_COL_1," ")
                                     .append_chars(num($payments_total,2),"left",PAPER_TOTAL_COL_2," ")."\r\n"; 
            $print_str .= append_chars('',"right",12," ").align_center('',PAPER_TOTAL_COL_2," ")
                              .append_chars('----------',"left",PAPER_TOTAL_COL_2," ")."\r\n";  
            $print_str .= append_chars(substrwords(ucwords('Outstanding'),18,""),"right",PAPER_TOTAL_COL_1," ")
                                     .append_chars(num($net_sales - $payments_total,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            

            $print_str .= PAPER_LINE_SINGLE."\r\n\r\n";

             #SUBCATEGORIES
            $subcats = $trans_menus['sub_cats'];
            // $print_str .= append_chars('Menu Subcategories:',"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            $print_str .= append_chars('F&B Sales Report',"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                         .append_chars('',"left",PAPER_RD_COL_3," ")."\r\n";
            $qty = 0;
            $total = 0;
            foreach ($subcats as $id => $val) {
                // $print_str .= append_chars($val['name'],"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                           // .append_chars(num(numInt($val['amount'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= append_chars(substrwords(ucwords($val['name']),30,""),"right",PAPER_TOTAL_COL_1," ")
                                     .append_chars(num($val['amount'],2),"left",PAPER_TOTAL_COL_2," ")."\r\n"; 

                $qty += $val['qty'];
                $total += $val['amount'];
             }
            // $print_str .= "-----------------"."\r\n";
            $print_str .= append_chars('',"right",12," ").align_center('',PAPER_TOTAL_COL_2," ")
                              .append_chars('----------',"left",PAPER_TOTAL_COL_2," ")."\r\n"; 
            // $print_str .= append_chars("Total","right",PAPER_RD_COL_1," ").align_center($qty,PAPER_RD_COL_2," ")
            //               .append_chars(num(numInt($total)),"left",PAPER_RD_COL_3_3," ")."\r\n";
            $print_str .= append_chars(substrwords(ucwords('Total'),30,""),"right",PAPER_TOTAL_COL_1," ")
                                     .append_chars(num($total,2),"left",PAPER_TOTAL_COL_2," ")."\r\n"; 

            // $print_str .= append_chars(substrwords('VAT SALES',23,""),"right",PAPER_TOTAL_COL_1," ")
                                     // .append_chars(num(numInt($taxable)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            $print_str .= append_chars(substrwords('12% VAT',23,""),"right",PAPER_TOTAL_COL_1," ")
                                     .append_chars("-".num(numInt($vat_)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            $print_str .= append_chars(substrwords('VAT Exempt',23,""),"right",PAPER_TOTAL_COL_1," ")
                                     .append_chars("-".num(numInt($nontaxable)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                                     // .append_chars(numInt($nontaxable-$zero_rated),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            $print_str .= append_chars(substrwords('Diplomat Exempt',23,""),"right",PAPER_TOTAL_COL_1," ")
                                     .append_chars("-".num(numInt($zero_rated)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            $print_str .= append_chars(substrwords('Total Discount',23,""),"right",PAPER_TOTAL_COL_1," ")
                                     .append_chars("-".num(numInt($discounts)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            

            $print_str .= append_chars('',"right",12," ").align_center('',PAPER_TOTAL_COL_2," ")
                              .append_chars('----------',"left",PAPER_TOTAL_COL_2," ")."\r\n"; 

            $fblessvat = $total - $vat_ - $nontaxable - $zero_rated - $discounts;
            $print_str .= append_chars(substrwords('F&B Sales Less VAT',23,""),"right",PAPER_TOTAL_COL_1," ")
                                     .append_chars(num(numInt($fblessvat)),"left",PAPER_TOTAL_COL_2," ")."\r\n\r\n";


            $print_str .= append_chars(substrwords('Payment Details',30,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
                          .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
            foreach ($payments_types as $code => $val) {
                // $print_str .= append_chars(substrwords(ucwords(strtoupper($code)),18,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                //               .append_chars(num(numInt($val['amount'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
                if($code == 'cash'){
                    $print_str .= append_chars(substrwords($code,23,""),"right",PAPER_TOTAL_COL_1," ")
                                .append_chars(num(numInt($val['amount'] + $vriance)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                }else{
                    $print_str .= append_chars(substrwords($code,23,""),"right",PAPER_TOTAL_COL_1," ")
                                .append_chars(num(numInt($val['amount'])),"left",PAPER_TOTAL_COL_2," ")."\r\n";  
                }

                // $pay_qty += $val['qty'];
            }
            $print_str .= append_chars('',"right",12," ").align_center('',PAPER_TOTAL_COL_2," ")
                              .append_chars('----------',"left",PAPER_TOTAL_COL_2," ")."\r\n"; 
            $print_str .= append_chars(substrwords(ucwords('Total Payments'),18,""),"right",PAPER_TOTAL_COL_1," ")
                                     .append_chars(num($payments_total,2),"left",PAPER_TOTAL_COL_2," ")."\r\n\r\n"; 


            //card breakdown
            if($payments['cards']){
                $cards = $payments['cards'];
                $card_total = 0;
                $count_total = 0;
                $print_str .= append_chars(substrwords('Card Breakdown:',18,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
                          .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
                foreach($cards as $key => $val){
                    // $print_str .= append_chars(substrwords($key,18,""),"right",PAPER_RD_COL_1," ").align_center($val['count'],PAPER_RD_COL_2," ")
                    //           .append_chars(num(numInt($val['amount'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $print_str .= append_chars(substrwords(ucwords($key),18,""),"right",PAPER_TOTAL_COL_1," ")
                                     .append_chars(num($val['amount'],2),"left",PAPER_TOTAL_COL_2," ")."\r\n"; 
                    $card_total += $val['amount'];
                    $count_total += $val['count'];
                }
                $print_str .= append_chars('',"right",12," ").align_center('',PAPER_TOTAL_COL_2," ")
                              .append_chars('----------',"left",PAPER_TOTAL_COL_2," ")."\r\n"; 
                $print_str .= append_chars(substrwords(ucwords('Total'),18,""),"right",PAPER_TOTAL_COL_1," ")
                                     .append_chars(num($card_total,2),"left",PAPER_TOTAL_COL_2," ")."\r\n"; 
                
                $print_str .= "\r\n";
            }


            #Discounts
            $types = $trans_discounts['types'];
            $qty = 0;
            $print_str .= append_chars(substrwords('Discounts',18,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
                          .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
            foreach ($types as $code => $val) {
                if($code != 'DIPLOMAT'){

                    if($code == 'SNDISC'){
                        $amount = $val['amount'];
                        // if(MALL == 'megamall' && $code == PWDDISC){
                        //     $amount = $val['amount'] / 1.12;
                        // }
                        $print_str .= append_chars(substrwords('20% Senior Disc',30,""),"right",PAPER_TOTAL_COL_1," ")
                                             .append_chars(Num($amount,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                    }elseif($code == 'PWDISC'){
                        $amount = $val['amount'];
                        // if(MALL == 'megamall' && $code == PWDDISC){
                        //     $amount = $val['amount'] / 1.12;
                        // }
                        $print_str .= append_chars(substrwords('PWD Disc',30,""),"right",PAPER_TOTAL_COL_1," ")
                                             .append_chars(Num($amount,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                    }
                    // $qty += $val['qty'];
                }
            }

            $print_str .= "\r\n";
            $print_str .= append_chars(substrwords('Other Discounts',23,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
                          .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
            $print_str .= append_chars(substrwords('Breakdown',23,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
                          .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
            $t_other_disc = 0;
            foreach ($types as $code => $val) {
                if($code != 'DIPLOMAT'){

                    if($code == 'SNDISC'){
                        
                    }elseif($code == 'PWDISC'){
                        
                    }else{
                        $amount = $val['amount'];
                        // if(MALL == 'megamall' && $code == PWDDISC){
                        //     $amount = $val['amount'] / 1.12;
                        // }
                        $print_str .= append_chars(substrwords($val['name'],30,""),"right",PAPER_TOTAL_COL_1," ")
                                             .append_chars(Num($amount,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";

                        $t_other_disc += $amount;
                    }
                    // $qty += $val['qty'];
                }
            }
            
            
            $print_str .= append_chars('',"right",12," ").align_center('',PAPER_TOTAL_COL_2," ")
                              .append_chars('----------',"left",PAPER_TOTAL_COL_2," ")."\r\n"; 
            $print_str .= append_chars(substrwords('Total Other Disc',30,""),"right",PAPER_TOTAL_COL_1," ")
                                             .append_chars(Num($t_other_disc,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            $print_str .= "\r\n";


             #TRANS COUNT
            $types = $trans['types'];
            $types_total = array();
            $guestCount = 0;
            foreach ($types as $type => $tp) {
                foreach ($tp as $id => $opt){
                    if(isset($types_total[$type])){
                        $types_total[$type] += round($opt->total_amount,2);

                    }
                    else{
                        $types_total[$type] = round($opt->total_amount,2);
                    }

                    // if($opt->type == 'dinein'){
                    //     $guestCount += $opt->guest;
                    // }
                    if($opt->guest == 0)
                        $guestCount += 1;
                    else
                        $guestCount += $opt->guest;
                }
            }
            $print_str .= append_chars(substrwords('Transaction Count',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                         .append_chars('',"left",PAPER_RD_COL_3_3," ")."\r\n";
            $tc_total  = 0;
            $tc_qty = 0;
            foreach ($types_total as $typ => $tamnt) {
                $print_str .= append_chars(substrwords($typ,18,""),"right",PAPER_RD_COL_1," ").align_center(count($types[$typ]),PAPER_RD_COL_2," ")
                             .append_chars(num(numInt($tamnt)),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $tc_total += $tamnt;
                $tc_qty += count($types[$typ]);
            }
            $print_str .= "-----------------"."\r\n";
            $print_str .= append_chars(substrwords('TC Total',18,""),"right",PAPER_TOTAL_COL_1," ")
                         .append_chars(num(numInt($tc_total)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            $print_str .= append_chars(substrwords('GUEST Total',18,""),"right",PAPER_TOTAL_COL_1," ")
                         .append_chars($guestCount,"left",PAPER_TOTAL_COL_2," ")."\r\n";
            // if($tc_total == 0 || $tc_qty == 0)
            //     $avg = 0;
            // else
            //     $avg = $tc_total/$tc_qty;
            if($net_sales){
                if($guestCount == 0){
                    $avg = 0;
                }else{
                    $avg = $net_sales/$guestCount;
                }
            }else{
                $avg = 0;
            }


            $print_str .= append_chars(substrwords('AVG Check',18,""),"right",PAPER_TOTAL_COL_1," ")
                         .append_chars(num(numInt($avg)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            $print_str .= "\r\n";



            /////////////END JDMT



            // $gross_less_disc = $final_gross - $discounts - $less_vat;
            // // $print_str .= append_chars(substrwords('NET SALES',23,""),"right",PAPER_TOTAL_COL_1," ")
            // //                          // .append_chars(numInt(($taxable + $nontaxable + $zero_rated + $vat_)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            // //                          .append_chars(numInt(($taxable + $nontaxable + $vat_)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            // // $print_str .= PAPER_LINE."\r\n";

            // if(count($payments['currency']) > 0){
            //     $currency = $payments['currency'];
            //     $print_str .= append_chars(substrwords('Currency Breakdown:',20,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
            //                   .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
            //     foreach ($currency as $code => $val) {
            //         $print_str .= append_chars(substrwords(ucwords(strtoupper($code)),18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ").append_chars(num(numInt($val['amount'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //         // $pay_qty += $val['qty'];
            //     }
            // }

            // $print_str .= PAPER_LINE_SINGLE."\r\n";
            $print_str .= "\r\n\r\n";



            $print_str .= append_chars(substrwords('VOID SALES',18,""),"right",PAPER_TOTAL_COL_1," ")
                         .append_chars(num(($void),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            $print_str .= append_chars(substrwords('VOID SALES COUNT',25,""),"right",PAPER_TOTAL_COL_1," ")
                         .append_chars($void_cnt,"left",PAPER_TOTAL_COL_2," ")."\r\n";
            $print_str .= append_chars(substrwords('CANCELLED TRANS',18,""),"right",PAPER_TOTAL_COL_1," ")
                         .append_chars(num(($cancelled),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            $print_str .= append_chars(substrwords('CANCELLED TRANS COUNT',25,""),"right",PAPER_TOTAL_COL_1," ")
                         .append_chars($cancel_cnt,"left",PAPER_TOTAL_COL_2," ")."\r\n";

            $cancelled_order = $this->cancelled_orders();
            $co = $cancelled_order['cancelled_order'];
            $print_str .= append_chars(substrwords('CANCELLED ORDERS',18,""),"right",PAPER_TOTAL_COL_1," ")
                         .append_chars(num(($co),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            $print_str .= append_chars(substrwords('CANCELLED ORDER COUNT',25,""),"right",PAPER_TOTAL_COL_1," ")
                         .append_chars($cancelled_order['cancel_count'],"left",PAPER_TOTAL_COL_2," ")."\r\n";

            $print_str .= append_chars(substrwords('Local Tax',18,""),"right",PAPER_TOTAL_COL_1," ")
                         .append_chars($loc_txt,"left",PAPER_TOTAL_COL_2," ")."\r\n";
            $print_str .= "\r\n";

        // #TRANS COUNT
        //     $types = $trans['types'];
        //     $types_total = array();
        //     $guestCount = 0;
        //     foreach ($types as $type => $tp) {
        //         foreach ($tp as $id => $opt){
        //             if(isset($types_total[$type])){
        //                 $types_total[$type] += round($opt->total_amount,2);

        //             }
        //             else{
        //                 $types_total[$type] = round($opt->total_amount,2);
        //             }

        //             // if($opt->type == 'dinein'){
        //             //     $guestCount += $opt->guest;
        //             // }
        //             if($opt->guest == 0)
        //                 $guestCount += 1;
        //             else
        //                 $guestCount += $opt->guest;
        //         }
        //     }
        //     $print_str .= append_chars(substrwords('Trans Count:',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
        //                  .append_chars('',"left",PAPER_RD_COL_3_3," ")."\r\n";
        //     $tc_total  = 0;
        //     $tc_qty = 0;
        //     foreach ($types_total as $typ => $tamnt) {
        //         $print_str .= append_chars(substrwords($typ,18,""),"right",PAPER_RD_COL_1," ").align_center(count($types[$typ]),PAPER_RD_COL_2," ")
        //                      .append_chars(num(numInt($tamnt)),"left",PAPER_RD_COL_3_3," ")."\r\n";
        //         $tc_total += $tamnt;
        //         $tc_qty += count($types[$typ]);
        //     }
        //     $print_str .= "-----------------"."\r\n";
        //     $print_str .= append_chars(substrwords('TC Total',18,""),"right",PAPER_TOTAL_COL_1," ")
        //                  .append_chars(num(numInt($tc_total)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
        //     $print_str .= append_chars(substrwords('GUEST Total',18,""),"right",PAPER_TOTAL_COL_1," ")
        //                  .append_chars($guestCount,"left",PAPER_TOTAL_COL_2," ")."\r\n";
        //     // if($tc_total == 0 || $tc_qty == 0)
        //     //     $avg = 0;
        //     // else
        //     //     $avg = $tc_total/$tc_qty;
        //     if($net_sales){
        //         if($guestCount == 0){
        //             $avg = 0;
        //         }else{
        //             $avg = $net_sales/$guestCount;
        //         }
        //     }else{
        //         $avg = 0;
        //     }


        //     $print_str .= append_chars(substrwords('AVG Check',18,""),"right",PAPER_TOTAL_COL_1," ")
        //                  .append_chars(num(numInt($avg)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
        //     $print_str .= "\r\n";
        #CHARGES
            $types = $trans_charges['types'];
            $qty = 0;
            $print_str .= append_chars(substrwords('Charges:',18,""),"right",18," ").align_center(null,5," ")
                          .append_chars(null,"left",13," ")."\r\n";
            foreach ($types as $code => $val) {
                $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                              .append_chars(num(numInt($val['amount'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $qty += $val['qty'];
            }
            $print_str .= "-----------------"."\r\n";
            $print_str .= append_chars(substrwords('Total Charges',18,""),"right",PAPER_RD_COL_1," ").align_center($qty,PAPER_RD_COL_2," ")
                          .append_chars(num(numInt($charges)),"left",PAPER_RD_COL_3_3," ")."\r\n";
            $print_str .= "\r\n";
        #Discounts
            $types = $trans_discounts['types'];
            $qty = 0;
            $print_str .= append_chars(substrwords('Discounts:',18,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
                          .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
            foreach ($types as $code => $val) {
                if($code != 'DIPLOMAT'){
                    $amount = $val['amount'];
                    // if(MALL == 'megamall' && $code == PWDDISC){
                    //     $amount = $val['amount'] / 1.12;
                    // }
                    $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                                  .append_chars(num(numInt($amount)),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $qty += $val['qty'];
                }
            }
            $print_str .= "-----------------"."\r\n";
            $print_str .= append_chars(substrwords('Total Discounts',18,""),"right",PAPER_RD_COL_1," ").align_center($qty,PAPER_RD_COL_2," ")
                          .append_chars(num(numInt($discounts)),"left",PAPER_RD_COL_3_3," ")."\r\n";
            $print_str .= append_chars(substrwords('VAT EXEMPT',18,""),"right",PAPER_TOTAL_COL_1," ")
                                     .append_chars(num(numInt($less_vat)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            $print_str .= "\r\n";
        #PAYMENTS
            $payments_types = $payments['types'];
            $payments_total = $payments['total'];
            $pay_qty = 0;
            $print_str .= append_chars(substrwords('Payment Breakdown:',18,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
                          .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
            foreach ($payments_types as $code => $val) {
                $print_str .= append_chars(substrwords(ucwords(strtolower($code)),18,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                              .append_chars(num(numInt($val['amount'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $pay_qty += $val['qty'];
            }
            $print_str .= "-----------------"."\r\n";
            $print_str .= append_chars(substrwords('Total Payments',18,""),"right",PAPER_RD_COL_1," ").align_center($pay_qty,PAPER_RD_COL_2," ")
                          .append_chars(num(numInt($payments_total)),"left",PAPER_RD_COL_3_3," ")."\r\n";
            $print_str .= "\r\n";

            // //card breakdown
            // if($payments['cards']){
            //     $cards = $payments['cards'];
            //     $card_total = 0;
            //     $count_total = 0;
            //     $print_str .= append_chars(substrwords('Card Breakdown:',18,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
            //               .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
            //     foreach($cards as $key => $val){
            //         $print_str .= append_chars(substrwords($key,18,""),"right",PAPER_RD_COL_1," ").align_center($val['count'],PAPER_RD_COL_2," ")
            //                   .append_chars(num(numInt($val['amount'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //         $card_total += $val['amount'];
            //         $count_total += $val['count'];
            //     }
            //     $print_str .= "-----------------"."\r\n";
            //     $print_str .= append_chars(substrwords('Total',18,""),"right",PAPER_RD_COL_1," ").align_center($count_total,PAPER_RD_COL_2," ")
            //               .append_chars(num(numInt($card_total)),"left",PAPER_RD_COL_3_3," ")."\r\n";
                
            //     $print_str .= "\r\n";
            // }

            //get all gc with excess
            if($payments['gc_excess']){
                $print_str .= append_chars(substrwords('GC EXCESS',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                              .append_chars(num(numInt($payments['gc_excess'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= "\r\n";
            }

            //show all sign chit
            // $trans['sales']
            if($trans['total_chit']){
                $print_str .= append_chars(substrwords('TOTAL CHIT',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                              .append_chars(num(numInt($trans['total_chit'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= "\r\n";
            }
        #CATEGORIES
            $cats = $trans_menus['cats'];
            $print_str .= append_chars('Menu Categories:',"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                         .append_chars('',"left",PAPER_RD_COL_3," ")."\r\n";
            $qty = 0;
            $total = 0;
            foreach ($cats as $id => $val) {
                if($val['qty'] > 0){
                    $print_str .= append_chars(substrwords($val['name'],18,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                               .append_chars(num(numInt($val['amount'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $qty += $val['qty'];
                    $total += $val['amount'];
                }
             }
            $print_str .= "-----------------"."\r\n";
            $cat_total_qty = $qty;
            $print_str .= append_chars("SubTotal","right",PAPER_RD_COL_1," ").align_center($qty,PAPER_RD_COL_2," ")
                          .append_chars(num(numInt($total)),"left",PAPER_RD_COL_3_3," ")."\r\n";
            $print_str .= append_chars("Modifiers Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                          .append_chars(num(numInt($trans_menus['mods_total'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
             $print_str .= append_chars("SubModifier Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                          .append_chars(num(numInt($trans_menus['submods_total'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
            if($trans_menus['item_total'] > 0){
             $print_str .= append_chars("Retail Items Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                           .append_chars(num(numInt($trans_menus['item_total'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
            }

            $print_str .= append_chars("Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                          .append_chars(num(numInt($total+$trans_menus['mods_total']+$trans_menus['item_total']+$trans_menus['submods_total'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
            $print_str .= "\r\n";
            // #SUBCATEGORIES
            // $subcats = $trans_menus['sub_cats'];
            // // $print_str .= append_chars('Menu Subcategories:',"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            // $print_str .= append_chars('Menu Types:',"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //              .append_chars('',"left",PAPER_RD_COL_3," ")."\r\n";
            // $qty = 0;
            // $total = 0;
            // foreach ($subcats as $id => $val) {
            //     $print_str .= append_chars($val['name'],"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
            //                .append_chars(num(numInt($val['amount'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //     $qty += $val['qty'];
            //     $total += $val['amount'];
            //  }
            // $print_str .= "-----------------"."\r\n";
            // $print_str .= append_chars("Total","right",PAPER_RD_COL_1," ").align_center($qty,PAPER_RD_COL_2," ")
            //               .append_chars(num(numInt($total)),"left",PAPER_RD_COL_3_3," ")."\r\n";
            // $print_str .= append_chars("Modifiers Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //               .append_chars(numInt($trans_menus['mods_total']),"left",PAPER_RD_COL_3_3," ")."\r\n";
            // $print_str .= append_chars("SubModifier Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //               .append_chars(numInt($trans_menus['submods_total']),"left",PAPER_RD_COL_3_3," ")."\r\n";
            // $print_str .= append_chars("Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
            //               .append_chars(numInt($total+$trans_menus['mods_total']+$trans_menus['submods_total']),"left",PAPER_RD_COL_3_3," ")."\r\n";
            $print_str .= "\r\n";
        #FREE MENUS
            $free = $trans_menus['free_menus'];
            $print_str .= append_chars('Free Menus:',"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                         .append_chars('',"left",PAPER_RD_COL_3," ")."\r\n";
            $fm = array();
            foreach ($free as $ms) {
                if(!isset($fm[$ms->menu_id])){
                    $mn = array();
                    $mn['name'] = $ms->menu_name;
                    $mn['cat_id'] = $ms->cat_id;
                    $mn['qty'] = $ms->qty;
                    $mn['amount'] = $ms->sell_price * $ms->qty;
                    $mn['sell_price'] = $ms->sell_price;
                    $mn['code'] = $ms->menu_code;
                    // $mn['free_user_id'] = $ms->free_user_id;
                    $fm[$ms->menu_id] = $mn;
                }
                else{
                    $mn = $fm[$ms->menu_id];
                    $mn['qty'] += $ms->qty;
                    $mn['amount'] += $ms->sell_price * $ms->qty;
                    $fm[$ms->menu_id] = $mn;
                }
            }
            $qty = 0;
            $total = 0;
            foreach ($fm as $menu_id => $val) {
                $print_str .= append_chars($val['name'],"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                           .append_chars(($val['qty']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $qty += $val['qty'];
                $total += $val['amount'];
            }
            $print_str .= "-----------------"."\r\n";
            $print_str .= append_chars("Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                          .append_chars(($qty),"left",PAPER_RD_COL_3_3," ")."\r\n";
            $print_str .= "\r\n";
            $print_str .= "\r\n";    
        #FOOTER
            $print_str .= append_chars(substrwords('Invoice Start: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                         .append_chars(iSetObj($trans['first_ref'],'trans_ref'),"left",PAPER_RD_COL_3_3," ")."\r\n";
            $print_str .= append_chars(substrwords('Invoice End: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                         .append_chars(iSetObj($trans['last_ref'],'trans_ref'),"left",PAPER_RD_COL_3_3," ")."\r\n";
            $print_str .= append_chars(substrwords('Invoice Ctr: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                         .append_chars($trans['ref_count'],"left",PAPER_RD_COL_3_3," ")."\r\n";
            if($title_name == "ZREAD"){
                $gt = $this->old_grand_net_total($date);
                $print_str .= "\r\n";
                $print_str .= append_chars(substrwords('OLD GT: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars(num(numInt( $gt['old_grand_total'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= append_chars(substrwords('NEW GT: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars( num(numInt($gt['old_grand_total']+$net_no_adds))  ,"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= append_chars(substrwords('Z READ CTR: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars( $gt['ctr'] ,"left",PAPER_RD_COL_3_3," ")."\r\n";
            }
            $print_str .= PAPER_LINE."\r\n";
        #MALLS
            if(MALL_ENABLED){
                ####################################
                # AYALA
                    if(MALL == 'ayala'){
                        
                        $branch_details = $this->setup_model->get_branch_details();
                        $branch = array();
                        foreach ($branch_details as $bv) {
                            $branch = array(
                                'id' => $bv->branch_id,
                                'res_id' => $bv->res_id,
                                'branch_code' => $bv->branch_code,
                                'name' => $bv->branch_name,
                                'branch_desc' => $bv->branch_desc,
                                'contact_no' => $bv->contact_no,
                                'delivery_no' => $bv->delivery_no,
                                'address' => $bv->address,
                                'base_location' => $bv->base_location,
                                'currency' => $bv->currency,
                                'inactive' => $bv->inactive,
                                'tin' => $bv->tin,
                                'machine_no' => $bv->machine_no,
                                'bir' => $bv->bir,
                                'permit_no' => $bv->permit_no,
                                'serial' => $bv->serial,
                                'accrdn' => $bv->accrdn,
                                'email' => $bv->email,
                                'website' => $bv->website,
                                'store_open' => $bv->store_open,
                                'store_close' => $bv->store_close,
                            );
                        }


                        $print_str .= align_center("FOR AYALA",PAPER_WIDTH," ")."\r\n";
                        $print_str .= align_center($branch['name'],PAPER_WIDTH," ")."\r\n";
                        $print_str .= align_center($branch['address'],PAPER_WIDTH," ")."\r\n\r\n";
                        $print_str .= align_center("CONSOLIDATED REPORT Z-READ",PAPER_WIDTH," ")."\r\n\r\n";


                        $total_daily_sales = $total_vatA = $total_rawgrossA = $total_discount = $total_refund = $total_void = $total_charge = $total_non_tax = $total_trans_count = $total_guest = 0;


                        $paytype = array();
                        foreach ($payments_types as $code => $val) {
                            if($code != 'credit'){
                                if(!isset($paytype[$code])){
                                    $paytype[$code] = array('amount'=>$val['amount']);
                                }else{
                                    $row = $paytype[$code];
                                    $row['amount'] += $val['amount'];
                                    $paytype[$code] = $row;
                                }
                            }
                            // $print_str .= append_chars(substrwords(ucwords(strtoupper($code)),12,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                            //               .append_chars(num($val['amount'],2),"left",PAPER_RD_COL_3_3," ")."\r\n";
                            // $pay_qty += $val['qty'];
                        }
                        $paycards = array();
                        if($payments['cards']){
                            $cards = $payments['cards'];
                            foreach($cards as $key => $val){
                                if(!isset($paycards[$key])){
                                    $paycards[$key] = array('amount'=>$val['amount']);
                                }else{
                                    $row = $paycards[$key];
                                    $row['amount'] += $val['amount'];
                                    $paycards[$key] = $row;
                                }
                            }
                        }


                        // for server
                        $rawgrossA = numInt($gross + $charges + $void + $local_tax);
                        $vatA = numInt(($rawgrossA  - $discounts - $void  -  $charges - $nontaxable - $local_tax - numInt($less_vat)) * (1/9.333333));
                        $dlySaleA = numInt($rawgrossA - $discounts - $void - $charges - $vatA - $less_vat + $local_tax);
                        // $t_discounts = $discounts+$less_vat;
                        $rawgrossA =  $rawgrossA - $less_vat;
                        $t_discounts = $discounts;



                        $trans_count = 0;
                        $begor = 0;
                        $endor = 0;
                        $first_inv = array();
                        $last_inv = array();
                        $first_ref = 0;
                        $last_ref = 0;
                        $first_val = 0;
                        $last_val = 0;
                        $invs = array();
                        foreach ($trans['all_orders'] as $ord) {
                            if($ord->type_id == SALES_TRANS && $ord->trans_ref != ""){
                                $ref = $ord->trans_ref;
                                if (preg_match('/^(\D*?)(\d+)(.*)/', $ref, $result) == 1){
                                    // if($ord->inactive != 1){
                                        list($all, $prefix, $number, $postfix) = $result;
                                        $ref_val = intval($number);
                                        $invs[$ref_val] = array("ref"=>$ord->trans_ref,"val"=>$ref_val);
                                    // }
                                }
                            }
                        }
                        ksort($invs);
                        // echo "<pre>",print_r($invs),"</pre>";die();
                        $first_inv = reset($invs);
                        $last_inv = end($invs);
                        if(count($first_inv) > 0){
                            $first_ref = $first_inv['ref'];
                            $first_val = $first_inv['val'];
                        }
                        if(count($last_inv) > 0){
                            $last_ref = $last_inv['ref'];
                            $last_val = $last_inv['val'];
                        }
                        if(count($invs) > 0){
                            $trans_count = ($last_val - $first_val) + 1; 
                        }

                        // echo $trans_count; die();
                        //add yun mga value ng server sa totals
                        $total_daily_sales += $dlySaleA;
                        $total_vatA += $vatA;
                        $total_rawgrossA += $rawgrossA;
                        $total_discount += $t_discounts;
                        $total_void += $void;
                        $total_charge += $charges;
                        $total_non_tax += $nontaxable;
                        // $total_trans_count += $tc_qty;
                        $total_trans_count += $trans_count;
                        $total_guest += $guestCount;
                         // echo $total_trans_count;

                        $terminals = $this->setup_model->get_terminals();


                        $print_str .= append_chars(substrwords('Daily Sales',25,""),"right",22," ").align_center('',2," ")
                                     .append_chars(num($total_daily_sales),"left",10," ")."\r\n";
                        $print_str .= append_chars(substrwords('Total Discount',25,""),"right",22," ").align_center('',2," ")
                                     .append_chars(num($total_discount),"left",10," ")."\r\n";
                        $print_str .= append_chars(substrwords('Total Refund',25,""),"right",22," ").align_center('',2," ")
                                     .append_chars(num(0),"left",10," ")."\r\n";
                        $print_str .= append_chars(substrwords('Total Void',25,""),"right",22," ").align_center('',2," ")
                                     .append_chars(num($total_void),"left",10," ")."\r\n";
                        $print_str .= append_chars(substrwords('Total Vat',25,""),"right",22," ").align_center('',2," ")
                                     .append_chars(num($total_vatA),"left",10," ")."\r\n";
                        $print_str .= append_chars(substrwords('Total Service Charge',25,""),"right",22," ").align_center('',2," ").append_chars(num($total_charge),"left",10," ")."\r\n";  


                        // $print_str .= append_chars(substrwords('Vatable Sales',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                        //              .append_chars(num($dlySaleA-$nontaxable),"left",PAPER_RD_COL_3_3," ")."\r\n";             
                        $print_str .= append_chars(substrwords('Total Non Taxable',22,""),"right",22," ").align_center('',2," ")
                                     .append_chars(num($total_non_tax),"left",10," ")."\r\n";
                        $print_str .= append_chars(substrwords('Row Gross',22,""),"right",22," ").align_center('',2," ")
                                     .append_chars(num($total_rawgrossA),"left",10," ")."\r\n";             
                        $print_str .= append_chars(substrwords('Transaction Count',22,""),"right",22," ").align_center('',2," ")
                                     .append_chars($total_trans_count,"left",10," ")."\r\n";
                        $print_str .= append_chars(substrwords('Customer Count',22,""),"right",22," ").align_center('',2," ")
                                     .append_chars($total_guest,"left",10," ")."\r\n";
                        foreach ($paytype as $k => $v) {
                            $print_str .= append_chars(strtoupper($k),"right",22," ").align_center('',2," ")
                                 .append_chars(num($v['amount']),"left",10," ")."\r\n";       
                        }
                        foreach ($paycards as $k => $v) {
                            $print_str .= append_chars(strtoupper($k),"right",22," ").align_center('',2," ")
                                 .append_chars(num($v['amount']),"left",10," ")."\r\n";       
                        }
                        $terminals = $this->setup_model->get_terminals();
                        // echo "<pre>",print_r($terminals),"</pre>";die();
                        foreach ($terminals as $k => $val) {
                            $print_str .= append_chars('BIR PERMIT '.$val->terminal_id,"right",15," ").align_center('',2," ")
                                 .append_chars($val->permit,"left",17," ")."\r\n";
                            $print_str .= append_chars('SERIAL NO. '.$val->terminal_id,"right",15," ").align_center('',2," ")
                                 .append_chars($val->serial,"left",17," ")."\r\n";
                        }           
                        // $print_str .= append_chars(substrwords('Less SC Disc',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                        //              .append_chars(num($discounts),"left",PAPER_RD_COL_3_3," ")."\r\n";             
                        // $print_str .= append_chars(substrwords('Vat Exempt',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                        //              .append_chars(num($less_vat),"left",PAPER_RD_COL_3_3," ")."\r\n";             
                        // $print_str .= append_chars(substrwords('Zero Rated',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                        //              .append_chars(num($zero_rated),"left",PAPER_RD_COL_3_3," ")."\r\n";             
                        // $print_str .= PAPER_LINE_SINGLE."\r\n";
                        // $print_str .= append_chars(substrwords('Net Sales',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                        //              .append_chars(num($dlySaleA+$vatA),"left",PAPER_RD_COL_3_3," ")."\r\n";             
                        // $print_str .= append_chars(substrwords('Total Qty Sold',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                        //              .append_chars(num($cat_total_qty),"left",PAPER_RD_COL_3_3," ")."\r\n";             
                        $print_str .= PAPER_LINE."\r\n";
                        $print_str .= align_center(sql2Date($post['from']),PAPER_WIDTH," ")."\r\n";
                        $print_str .= align_center("END OF REPORT",PAPER_WIDTH," ")."\r\n";


                    }
                ####################################
            }    

        if ($return_print_str) {
            return $print_str;
        }  
        // if($title_name == "ZREAD"){
        //     if($asJson == false){
        //         $this->manager_model->add_event_logs($user['id'],"ZREAD","View");    
        //     }else{
        //         $this->manager_model->add_event_logs($user['id'],"ZREAD","Print");                    
        //     } 
        // }elseif($title_name == "XREAD"){
        //     if($asJson == false){
        //         $this->manager_model->add_event_logs($user['id'],"XREAD","View");    
        //     }else{
        //         $this->manager_model->add_event_logs($user['id'],"XREAD","Print");                    
        //     } 
        // }else{
        //     if($asJson == false){
        //         $this->manager_model->add_event_logs($user['id'],"System Sales","View");    
        //     }else{
        //         $this->manager_model->add_event_logs($user['id'],"System Sales","Print");                    
        //     }
        // }  
        // if(PRINT_VERSION && PRINT_VERSION == 'V2'){
        //     $this->do_print_v2($print_str,$asJson);  
        // }else if(PRINT_VERSION && PRINT_VERSION == 'V3' && $asJson){
        //     echo $this->html_print($print_str);
        // }else{
        $this->session->set_userdata('pdf_data','<pre>'.$print_str.'</pre>') ; 
        $this->do_print($print_str,$asJson);
        // }
        // $this->do_print($print_str,$asJson);
    }
    public function back_system_sales(){
        $this->load->model('dine/reports_model');
        $this->load->helper('dine/reports_helper');
        $data = $this->syter->spawn('system_sales');
        $data['page_title'] = 'System Sales';
        $data['code'] = SystemSalesBack();
        $data['add_css'] = array('css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css');
        $data['add_js'] = array('js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js');
        $data['load_js'] = 'dine/prints.php';
        $data['use_js'] = 'SysSalesBackJs';
        $this->load->view('page',$data);
    }
    public function check_system_sales(){
        $this->load->helper('dine/print_helper');

        $date = $this->input->post('date');
        $user = $this->input->post('user');
        $terminal_id = $this->input->post('terminal_id');
        $json = $this->input->post('json');
        $branch_code = $this->input->post('branch_id');
        $daterange = $this->input->post("calendar_range");        
        $dates = explode(" to ",$daterange);
        $from = date2SqlDateTime($dates[0]);        
        $to = date2SqlDateTime($dates[1]);
        // $dates = date2Sql($date);
        // echo $test;die();

        // $args["trans_sales.user_id"] = $user;
        $args["trans_sales.type !="] = 'mgtfree';
        $args["trans_sales.datetime  BETWEEN '".$from."' AND '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);
        // $args["trans_sales.datetime  BETWEEN '".$dates." 00:00:01' AND '".$dates." 23:59:59'"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.branch_code  = '".$branch_code."' "] = array('use'=>'where','val'=>null,'third'=>false);
        if($json == 'true'){
            $asjson = true;
        }else{
            $asjson = false;
        }
        // $d = $dates." 00:00:01";
        $details = $this->setup_model->get_branch_details($branch_code);
            
        $open_time = $details[0]->store_open;
        $close_time = $details[0]->store_close;
        // $print_date =$dates." ".$open_time. "  - ".$dates." ".$close_time;
        $print_date =$from. "  - ".$to;
        // $this->zread_sales_rep_backoffice($asjson,$args,false,$d,$print_date);
        $this->system_sales_sales_rep_backoffice_aurora($asjson,$args,false,$from,$print_date,$branch_code);
        // $this->system_sales_rep_jdmt_aurora($asjson,$args,false,$d,$print_date);

    }
    public function system_sales_sales_rep_backoffice_aurora($asJson=false,$args=array(),$return_print_str=false,$date,$print_date,$branch_code=''){
        ////hapchan
            ini_set('memory_limit', '-1');
            set_time_limit(3600);
            
            $print_str = $this->print_header();
            $user = $this->session->userdata('user');
            $time = $this->site_model->get_db_now();
            $post = $this->set_post();
            $curr = $this->search_current();
            $trans = $this->trans_sales($args,$curr);
            // var_dump($trans['net']); die();
            $sales = $trans['sales'];
            $trans_menus = $this->menu_sales($sales['settled']['ids'],$curr,$branch_code);
            $trans_charges = $this->charges_sales($sales['settled']['ids'],$curr,$branch_code);
            $trans_discounts = $this->discounts_sales($sales['settled']['ids'],$curr,$branch_code);
            $tax_disc = $trans_discounts['tax_disc_total'];
            $no_tax_disc = $trans_discounts['no_tax_disc_total'];
            $trans_local_tax = $this->local_tax_sales($sales['settled']['ids'],$curr,$branch_code);
            $trans_tax = $this->tax_sales($sales['settled']['ids'],$curr,$branch_code);
            $trans_no_tax = $this->no_tax_sales($sales['settled']['ids'],$curr,$branch_code);
            $trans_zero_rated = $this->zero_rated_sales($sales['settled']['ids'],$curr,$branch_code);
            $payments = $this->payment_sales($sales['settled']['ids'],$curr,$branch_code);

            $gross = $trans_menus['gross'];
            
            $net = $trans['net'];
            $void = $trans['void'];
            $ewt1_count = $trans['ewt1_count'];
            $ewt2_count = $trans['ewt2_count'];
            $ewt5_count = $trans['ewt5_count'];
            $void_cnt = $trans['void_cnt'];
            $cancelled = $trans['cancel_amount'];
            $cancel_cnt = $trans['cancel_cnt'];
            $charges = $trans_charges['total'];
            $discounts = $trans_discounts['total'];
            $local_tax = $trans_local_tax['total'];
            $discounts = 0;
            $types = $trans_discounts['types'];
            foreach ($types as $code => $val) {
                if($code != 'DIPLOMAT'){
                    $discounts += round($val['amount'],2);
                }
            }

            $range = $this->input->post('calendar_range');
            $calendar = $this->input->post('calendar');
            $days = "";
            // $use_curr = $this->input->post('use_curr');
            // echo $use_curr;exit();
            if($range != null )
                $range = $range;
            if($calendar != null )
                $calendar = $calendar;

            $date1 = $date2 = "";
            if($range != ""){
                $daterange = $range;
                $dates = explode(" to ",$daterange);
                $from = date2SqlDateTime($dates[0]);
                $to = date2SqlDateTime($dates[1]);

                $diff = abs(strtotime($to) - strtotime($from));

                $years = floor($diff / (365*60*60*24));
                $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
                $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
                // echo $years.'-'.$months.'-'.$days;

            }

            if($calendar != ""){
                $date = date2Sql($calendar);
                $details = $this->setup_model->get_branch_details();
            
                $open_time = $details[0]->store_open;
                $close_time = $details[0]->store_close;
                $from = date2SqlDateTime($date." ".$open_time);
                $oa = date('a',strtotime($open_time));
                $ca = date('a',strtotime($close_time));
                $to = date2SqlDateTime($date." ".$close_time);
                if($oa == $ca){
                    $to = date('Y-m-d H:i:s',strtotime($to . "+1 days"));
                }

                $diff = abs(strtotime($to) - strtotime($from));

                $years = floor($diff / (365*60*60*24));
                $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
                $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

            }
            // echo $gross.' - '.$charges.' - '.$discounts.' - '.$net; die();
            $ewt = 0;
            $ewt = $ewt1_count + $ewt2_count + $ewt5_count;
            // $less_vat = round(($gross+ ( $charges+$local_tax)) - $discounts)- $ewt - $net;
            // $less_vat = round(($gross+ ( $charges+$local_tax) - $discounts)- $ewt - $net);

            $types = $trans_discounts['types'];
            $pwd = $snr =   $nopwdsnr = 0;
            foreach ($types as $code => $val) {
                if($code == 'SNDISC'){
                    $snr += round($val['amount'],2);
                }

                if($code == 'PWDISC'){
                    $pwd += round($val['amount'],2);
                }

                if($code != 'PWDISC' && $code != 'SNDISC'){
                    $nopwdsnr += round($val['amount'],2);
                }
            }


            $zero_rated = $trans_zero_rated['total'];
            $less_vat_snrpwd = ($snr + $pwd) / 0.20;
            // $less_vat = ($less_vat_snrpwd + $trans_zero_rated['total']) * 0.12;
            // $less_vat = (($gross+$charges+$local_tax) - $discounts) - $net;
            $less_vat = (($gross+$charges+$local_tax) - $discounts) - $ewt - $net; 
            $v_exempt =  $less_vat_snrpwd + $zero_rated;
            $tot_disc =  $less_vat_snrpwd + $zero_rated + $nopwdsnr;
            $lv = $v_exempt*.12;
            // $less_vat = round(($pre_total - ($order['amount'] - $total_charges + $local_tax_amt ) ) - $total_discounts,2);
            // $less_vat = $trans_discounts['vat_exempt_total'];

            $vatexsales = round($v_exempt,2)*.12;
            // echo $gross.'+'.$discounts.' - '.$vatexsales;
            // die();

            // $lessvat_variance = 0;
            // if($vatexsales != $less_vat){
            //     $lessvat_variance = $vatexsales - $less_vat;
            // }
            $net_sales = (($gross + ($charges - $ewt1_count - $ewt2_count - $ewt5_count)) - $discounts) - $vatexsales;

            if($less_vat < 0)
                $less_vat = 0;
            if($lv < 0)
                $lv = 0;
            // var_dump($less_vat);

            //para mag tugmam yun payments and netsale
            // $net_sales2 = $gross + $charges - $discounts - $less_vat;
            // $diffs = $net_sales2 - $payments['total'];
            // if($diffs < 1){
            //     $less_vat = $less_vat + $diffs;
            // }
            

            $tax = $trans_tax['total'];
            $no_tax = $trans_no_tax['total'];
            
            $no_tax -= $zero_rated;

            

            $loc_txt = numInt(($local_tax));
            $net_no_adds = $net-($charges+$local_tax);
            // $nontaxable = $no_tax;
            //binago 9/25/2018 for zreading adjustment of vat exempt equal to the receipt vat exempt
            $nontaxable = $no_tax - $no_tax_disc;
            // echo $gross.' - '.$less_vat.' - '.$nontaxable.' - '.$zero_rated.' - '.$discounts; die();
            // $taxable = ($gross - $less_vat - $nontaxable - $zero_rated) / 1.12;
            // 1.12; binago din para sa adjustment of vat exempt equal to the receipt vat exempt
            // $taxable =   ($gross - $discounts - $less_vat - $nontaxable) / 1.12;
            $taxable =   ($gross - $less_vat - $nontaxable - $zero_rated - $discounts) / 1.12; //change computation conflict for zero rated 10 17 2018
            $total_net = ($taxable) + ($nontaxable+$zero_rated) + $tax + $local_tax;
            $add_gt = $taxable+$nontaxable+$zero_rated;
            $nsss = $taxable +  $nontaxable +  $zero_rated;


            $final_gross = $gross;
            $vat_ = $taxable * .12;
            $vsales = round($vat_,2) /0.12;
            $vexsales = $tot_disc - $discounts;
            $nsales = $taxable + $vat_ + $vexsales;
            $new_nsales = $vsales + $tot_disc - $discounts;

            
            $net_sales_variance = 0;
            if($new_nsales != $nsales){
                $net_sales_variance = ($new_nsales + round($vat_,2)) - $nsales;
            }

            $types = $trans_charges['types'];
            $tc_amount = 0;
            // foreach ($types as $code => $val) {
            //     $tc_amount += $val['amount'];
            // }
            foreach ($types as $code => $val) {
                if($days > 1){
                    $tc_amount += round($val['amount'],2);
                }else{
                    $tc_amount += $val['amount'];
                }
            }
            // $lessvat_variance = 0;
            $lessvat_variance =   round($vsales,2) + round($vat_,2) + round($tot_disc,2) + round($tc_amount,2);
            $new_total_payment = 0;
            $payments_types = $payments['types'];
            foreach ($payments_types as $code => $val) {
                $new_total_payment += $val['amount'];
            }
            $total_pay_disc = 0;
            $variance = 0;
            $total_pay_disc = $new_total_payment + $discounts;
            if($lessvat_variance != $total_pay_disc){
                $variance = $lessvat_variance - round($total_pay_disc,2);
            }

            $vpay = $var_net_sales = 0;
            foreach ($payments_types as $code => $val) {

                if($code == 'cash'){
                    $vpay += $val['amount'] + $variance;
                }else{
                    $vpay += $val['amount'];
                }
            }

            if($net_sales != $vpay){
                $var_net_sales = $vpay - $net_sales;
            }
            #GENERAL
                $title_name = "SYSTEM SALES REPORT";
            if($post['title'] != "")
                $title_name = $post['title'];

                $print_str .= align_center($title_name,PAPER_WIDTH," ")."\r\n";
                $print_str .= align_center("TERMINAL ".$post['terminal'],PAPER_WIDTH," ")."\r\n";
                $print_str .= append_chars('Printed On','right',11," ").append_chars(": ".date2SqlDateTime($time),'right',19," ")."\r\n";
                $print_str .= append_chars('Printed BY','right',11," ").append_chars(": ".$user['full_name'],'right',19," ")."\r\n";
                $print_str .= PAPER_LINE."\r\n";
                $print_str .= align_center(sql2DateTime($post['from'])." - ".sql2DateTime($post['to']),PAPER_WIDTH," ")."\r\n";
                if($post['employee'] != "All")
                    $print_str .= align_center($post['employee'],PAPER_WIDTH," ")."\r\n";
                $print_str .= PAPER_LINE."\r\n";

                $print_str .= append_chars(substrwords('GROSS SALES',18,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(num($gross,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                

                // $print_str .= append_chars(substrwords('TOTAL SALES',18,""),"right",PAPER_TOTAL_COL_1," ")
                //                          .append_chars(num($gross + $charges,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                // $print_str .= append_chars(substrwords(ucwords(strtoupper('SC/PWD VAT EXEMPT')),18,""),"right",PAPER_TOTAL_COL_1," ")
                //                          .append_chars('-'.num($less_vat,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                // $print_str .= append_chars('',"right",12," ").align_center('',PAPER_TOTAL_COL_2," ")
                //                   .append_chars('----------',"left",PAPER_TOTAL_COL_2," ")."\r\n";

                // $print_str .= append_chars(substrwords('GROSS SALES',18,""),"right",PAPER_TOTAL_COL_1," ")
                //                          .append_chars(num($gross + $charges - $less_vat,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";

                // $qty = 0;
                $types = $trans_charges['types'];
                foreach ($types as $code => $val) {
                    $amount = $val['amount'];
                    $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(num($amount,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                    // $qty += $val['qty'];
                }
                $types = $trans_discounts['types'];
                $qty = 0;
                foreach ($types as $code => $val) {
                    if($code != 'DIPLOMAT'){
                        $amount = $val['amount'];
                        // if(MALL == 'megamall' && $code == PWDDISC){
                        //     $amount = $val['amount'] / 1.12;
                        // }
                        $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",PAPER_TOTAL_COL_1," ")
                                             .append_chars('-'.Num($amount,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                        // $qty += $val['qty'];
                    }
                }
                
                $print_str .= append_chars(substrwords(ucwords(strtoupper('SC/PWD VAT EXEMPT')),18,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars('-'.num($lv,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                if(EWT_DISCOUNT){
                $print_str .= append_chars(substrwords('EWT 1%',23,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(num(numInt($ewt1_count)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords('EWT 2%',23,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(num(numInt($ewt2_count)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords('EWT 5%',23,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(num(numInt($ewt5_count)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                }
                $print_str .= append_chars('',"right",12," ").align_center('',PAPER_TOTAL_COL_2," ")
                                  .append_chars('----------',"left",PAPER_TOTAL_COL_2," ")."\r\n";

                $print_str .= append_chars(substrwords(ucwords(strtoupper('GROSS RECEIPTS')),18,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(num($net_sales + $var_net_sales,2),"left",PAPER_TOTAL_COL_2," ")."\r\n\r\n";
                // $print_str .= append_chars(substrwords(ucwords(strtoupper('NET SALES')),18,""),"right",PAPER_TOTAL_COL_1," ")
                                         // .append_chars(num($net_sales,2),"left",PAPER_TOTAL_COL_2," ")."\r\n\r\n";
            #PAYMENTS
                $payments_types = $payments['types'];
                $payments_total = $payments['total'];
                $pay_qty = 0;
            #SUMMARY


                $print_str .= append_chars(substrwords('VAT SALES',23,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(num($vsales),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords('VAT',23,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(num($vat_),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords('VAT EXEMPT SALES',23,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(num($vexsales),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                                         // .append_chars(num($nontaxable + $zero_rated),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                                         // .append_chars(numInt($nontaxable-$zero_rated),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords('ZERO RATED',23,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(num(numInt($zero_rated)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                // $print_str .= append_chars(substrwords('EWT',23,""),"right",PAPER_TOTAL_COL_1," ")
                //                          .append_chars(num(numInt($ewt1_count)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars('',"right",12," ").align_center('',PAPER_TOTAL_COL_2," ")
                                  .append_chars('----------',"left",PAPER_TOTAL_COL_2," ")."\r\n";
                // $net_sales = $gross + $charges - $discounts - $less_vat;
                // $nsales = $taxable + $vat_ + $nontaxable + $zero_rated;
                
                if(OTHER_MENU_ITEM_SALES){
                    $netss = 0;
                    $print_str .= append_chars(substrwords(ucwords(strtoupper('GROSS SALES')),18,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(num($nsales + $net_sales_variance,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                    $print_str .= append_chars(substrwords('VAT 12%',23,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(num('-'.numInt($vat_)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                    $netss = $nsales - $vat_;
                    $print_str .= append_chars('',"right",12," ").align_center('',PAPER_TOTAL_COL_2," ")
                                  .append_chars('----------',"left",PAPER_TOTAL_COL_2," ")."\r\n";
                    $print_str .= append_chars(substrwords('NET SALES',23,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(num($new_nsales),"left",PAPER_TOTAL_COL_2," ")."\r\n\r\n";
                }else{
                    $print_str .= append_chars(substrwords(ucwords(strtoupper('NET SALES')),18,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(num($nsales,2),"left",PAPER_TOTAL_COL_2," ")."\r\n\r\n";
                }

                $net_pay_variance = $new_v_compute = $payment_w_disc = $pay_variance = $camount = $pay_amount = 0;
                if($net_sales != $payments_total){
                    // $net_pay_variance = $net_sales - $payments_total;
                    $net_pay_variance = round($net_sales,2) - $payments_total;
                    $new_v_compute = round($vat_,2) + $vsales + $v_exempt;

                    foreach ($payments_types as $code => $val) {
                        if($code == 'cash'){
                            $pay_amount += numInt($val['amount']) + $net_pay_variance;
                        }else{
                            $pay_amount += numInt($val['amount']);
                        }
                    }
                    $payment_w_disc = $pay_amount + $discounts;

                    $ctypes = $trans_charges['types'];
                    foreach ($ctypes as $code => $val) {
                        $camount += $val['amount'];
                    }

                    $pay_variance =  round($payment_w_disc,2) - round($new_v_compute,2) - $camount;
                }

                

                $print_str .= append_chars(substrwords('Payment Breakdown:',18,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
                              .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
                $vcash = 0;
                foreach ($payments_types as $code => $val) {

                    if($code == 'cash'){
                        $print_str .= append_chars(substrwords(ucwords(strtoupper($code)),18,""),"right",12," ").align_center($val['qty'],11," ")
                                      .append_chars(num($val['amount'] + $variance),"left",11," ")."\r\n";
                        $vcash += $val['amount'] + $variance;
                    }else{
                        $print_str .= append_chars(substrwords(ucwords(strtoupper($code)),18,""),"right",12," ").align_center($val['qty'],11," ")
                                      .append_chars(num(numInt($val['amount'])),"left",11," ")."\r\n";
                        $vcash += $val['amount'];
                    }

                    $pay_qty += $val['qty'];
                }
                $print_str .= append_chars('',"right",12," ").align_center('',PAPER_RD_COL_2," ")
                                  .append_chars('----------',"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= append_chars(substrwords('TOTAL PAYMENTS',18,""),"right",9," ").align_center($pay_qty,9," ")
                              .append_chars(num(numInt($vcash)),"left",10," ")."\r\n\r\n";
                $print_str .= PAPER_LINE_SINGLE."\r\n";
                $gross_less_disc = $final_gross - $discounts - $less_vat;
                // $print_str .= append_chars(substrwords('NET SALES',23,""),"right",PAPER_TOTAL_COL_1," ")
                //                          // .append_chars(numInt(($taxable + $nontaxable + $zero_rated + $vat_)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                //                          .append_chars(numInt(($taxable + $nontaxable + $vat_)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                // $print_str .= PAPER_LINE."\r\n";

                if(count($payments['currency']) > 0){
                    $currency = $payments['currency'];
                    $print_str .= append_chars(substrwords('Currency Breakdown:',20,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
                                  .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
                    foreach ($currency as $code => $val) {
                        $print_str .= append_chars(substrwords(ucwords(strtoupper($code)),18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ").append_chars(num(numInt($val['amount'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
                        // $pay_qty += $val['qty'];
                    }
                }

                // $print_str .= PAPER_LINE_SINGLE."\r\n";
                $print_str .= "\r\n\r\n";



                $print_str .= append_chars(substrwords('VOID SALES',18,""),"right",PAPER_TOTAL_COL_1," ")
                             .append_chars(num(($void),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords('VOID SALES COUNT',25,""),"right",PAPER_TOTAL_COL_1," ")
                             .append_chars($void_cnt,"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords('CANCELLED TRANS',18,""),"right",PAPER_TOTAL_COL_1," ")
                             .append_chars(num(($cancelled),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords('CANCELLED TRANS COUNT',25,""),"right",PAPER_TOTAL_COL_1," ")
                             .append_chars($cancel_cnt,"left",PAPER_TOTAL_COL_2," ")."\r\n";

                $cancelled_order = $this->cancelled_orders($args);
                $co = $cancelled_order['cancelled_order'];
                $print_str .= append_chars(substrwords('CANCELLED ORDERS',18,""),"right",PAPER_TOTAL_COL_1," ")
                             .append_chars(num(($co),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords('CANCELLED ORDER COUNT',25,""),"right",PAPER_TOTAL_COL_1," ")
                             .append_chars($cancelled_order['cancel_count'],"left",PAPER_TOTAL_COL_2," ")."\r\n";

                $print_str .= append_chars(substrwords('Local Tax',18,""),"right",PAPER_TOTAL_COL_1," ")
                             .append_chars($loc_txt,"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= "\r\n";

            #TRANS COUNT
                $types = $trans['types'];
                $types_total = array();
                $guestCount = 0;
                foreach ($types as $type => $tp) {
                    foreach ($tp as $id => $opt){
                        if(isset($types_total[$type])){
                            $types_total[$type] += round($opt->total_amount,2);

                        }
                        else{
                            $types_total[$type] = round($opt->total_amount,2);
                        }

                        // if($opt->type == 'dinein'){
                        //     $guestCount += $opt->guest;
                        // }
                        if($opt->guest == 0)
                            $guestCount += 1;
                        else
                            $guestCount += $opt->guest;
                    }
                }
                $print_str .= append_chars(substrwords('Trans Count:',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars('',"left",PAPER_RD_COL_3_3," ")."\r\n";
                $tc_total  = 0;
                $tc_qty = 0;
                foreach ($types_total as $typ => $tamnt) {
                    $print_str .= append_chars(strtoupper($typ),"right",11," ").align_center(count($types[$typ]),11," ")
                                 .append_chars(num(numInt($tamnt)),"left",11," ")."\r\n";
                    $tc_total += $tamnt;
                    $tc_qty += count($types[$typ]);
                }
                $print_str .= "-----------------"."\r\n";
                $print_str .= append_chars(substrwords('TC Total',18,""),"right",PAPER_TOTAL_COL_1," ")
                             .append_chars(num(numInt($tc_total + $net_pay_variance + $variance)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords('GUEST Total',18,""),"right",PAPER_TOTAL_COL_1," ")
                             .append_chars($guestCount,"left",PAPER_TOTAL_COL_2," ")."\r\n";
                // if($tc_total == 0 || $tc_qty == 0)
                //     $avg = 0;
                // else
                //     $avg = $tc_total/$tc_qty;
                if($net_sales){
                    if($guestCount == 0){
                        $avg = 0;
                    }else{
                        $avg = $net_sales/$guestCount;
                    }
                }else{
                    $avg = 0;
                }


                $print_str .= append_chars(substrwords('AVG Check',18,""),"right",PAPER_TOTAL_COL_1," ")
                             .append_chars(num(numInt($avg)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= "\r\n";
            #CHARGES
                $types = $trans_charges['types'];
                $qty = 0;
                $print_str .= append_chars(substrwords('Charges:',18,""),"right",18," ").align_center(null,5," ")
                              .append_chars(null,"left",13," ")."\r\n";
                foreach ($types as $code => $val) {
                    $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                                  .append_chars(num(numInt($val['amount'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $qty += $val['qty'];
                }
                $print_str .= "-----------------"."\r\n";
                $print_str .= append_chars(substrwords('Total Charges',18,""),"right",10," ").align_center($qty,10," ")
                              .append_chars(num(numInt($charges)),"left",10," ")."\r\n";
                $print_str .= "\r\n";
            #Discounts
                $types = $trans_discounts['types'];
                $qty = 0;
                $print_str .= append_chars(substrwords('Discounts:',18,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
                              .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
                foreach ($types as $code => $val) {
                    if($code != 'DIPLOMAT'){
                        $amount = $val['amount'];
                        // if(MALL == 'megamall' && $code == PWDDISC){
                        //     $amount = $val['amount'] / 1.12;
                        // }
                        $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",14," ").align_center($val['qty'],9," ")
                                      .append_chars(num(numInt($amount)),"left",10," ")."\r\n";
                        $qty += $val['qty'];
                    }
                }
                $print_str .= "-----------------"."\r\n";
                $print_str .= append_chars(substrwords('Total Discounts',18,""),"right",9," ").align_center($qty,9," ")
                              .append_chars(num(numInt($discounts)),"left",9," ")."\r\n";
                $print_str .= append_chars(substrwords('VAT EXEMPT',18,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(num(numInt($less_vat)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= "\r\n";
            #PAYMENTS
                $payments_types = $payments['types'];
                $payments_total = $payments['total'];
                $pay_qty = 0;
                // $print_str .= append_chars(substrwords('Payment Breakdown:',18,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
                //               .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
                // foreach ($payments_types as $code => $val) {
                //     $print_str .= append_chars(substrwords(ucwords(strtolower($code)),18,""),"right",11," ").align_center($val['qty'],11," ")
                //                   .append_chars(num(numInt($val['amount'])),"left",11," ")."\r\n";
                //     $pay_qty += $val['qty'];
                // }
                $net_pay_variance = $new_v_compute = $payment_w_disc = $pay_variance = $camount = $pay_amount = 0;
                if($net_sales != $payments_total){
                    // $net_pay_variance = $net_sales - $payments_total;
                    $net_pay_variance = round($net_sales,2) - $payments_total;
                    $new_v_compute = round($vat_,2) + $vsales + $v_exempt;

                    foreach ($payments_types as $code => $val) {
                        if($code == 'cash'){
                            $pay_amount += numInt($val['amount']) + $net_pay_variance;
                        }else{
                            $pay_amount += numInt($val['amount']);
                        }
                    }
                    $payment_w_disc = $pay_amount + $discounts;

                    $ctypes = $trans_charges['types'];
                    foreach ($ctypes as $code => $val) {
                        $camount += $val['amount'];
                    }

                    $pay_variance =  round($payment_w_disc,2) - round($new_v_compute,2) - $camount;
                }

                

                $print_str .= append_chars(substrwords('Payment Breakdown:',18,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
                              .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
                $vcash = 0;
                foreach ($payments_types as $code => $val) {

                    if($code == 'cash'){
                        $print_str .= append_chars(substrwords(ucwords(strtoupper($code)),18,""),"right",12," ").align_center($val['qty'],11," ")
                                      .append_chars(num($val['amount'] + $variance),"left",11," ")."\r\n";
                        $vcash += $val['amount'] + $variance;
                    }else{
                        $print_str .= append_chars(substrwords(ucwords(strtoupper($code)),18,""),"right",12," ").align_center($val['qty'],11," ")
                                      .append_chars(num(numInt($val['amount'])),"left",11," ")."\r\n";
                        $vcash += $val['amount'];
                    }

                    $pay_qty += $val['qty'];
                }
                $print_str .= "-----------------"."\r\n";
                $print_str .= append_chars(substrwords('Total Payments',18,""),"right",9," ").align_center($pay_qty,9," ")
                              .append_chars(num(numInt($vcash)),"left",10," ")."\r\n";
                $print_str .= "\r\n";

                //card breakdown
                if($payments['cards']){
                    $cards = $payments['cards'];
                    $card_total = 0;
                    $count_total = 0;
                    $print_str .= append_chars(substrwords('Card Breakdown:',18,""),"right",9," ").align_center(null,9," ")
                              .append_chars(null,"left",10," ")."\r\n";
                    foreach($cards as $key => $val){
                        $print_str .= append_chars(substrwords($key,18,""),"right",11," ").align_center($val['count'],11," ")
                                  .append_chars(num(numInt($val['amount'])),"left",11," ")."\r\n";
                        $card_total += $val['amount'];
                        $count_total += $val['count'];
                    }
                    $print_str .= "-----------------"."\r\n";
                    $print_str .= append_chars(substrwords('Total',18,""),"right",PAPER_RD_COL_1," ").align_center($count_total,PAPER_RD_COL_2," ")
                              .append_chars(num(numInt($card_total)),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    
                    $print_str .= "\r\n";
                }

                //get all gc with excess
                if($payments['gc_excess']){
                    $print_str .= append_chars(substrwords('GC EXCESS',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                                  .append_chars(num(numInt($payments['gc_excess'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $print_str .= "\r\n";
                }

                //show all sign chit
                // $trans['sales']
                if($trans['total_chit']){
                    $print_str .= append_chars(substrwords('TOTAL CHIT',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                                  .append_chars(num(numInt($trans['total_chit'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $print_str .= "\r\n";
                }
            #CATEGORIES
                $cats = $trans_menus['cats'];
                $print_str .= append_chars('Menu Categories:',"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars('',"left",PAPER_RD_COL_3," ")."\r\n";
                $qty = 0;
                $total = 0;
                foreach ($cats as $id => $val) {
                    if($val['qty'] > 0){
                        $print_str .= append_chars(substrwords($val['name'],18,""),"right",15," ").align_center($val['qty'],8," ")
                                   .append_chars(num(numInt($val['amount'])),"left",11," ")."\r\n";
                        $qty += $val['qty'];
                        $total += $val['amount'];
                    }
                 }
                $print_str .= "-----------------"."\r\n";
                $cat_total_qty = $qty;
                $print_str .= append_chars("SubTotal","right",PAPER_RD_COL_1," ").align_center($qty,PAPER_RD_COL_2," ")
                              .append_chars(num(numInt($total)),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= append_chars("Modifiers Total","right",9," ").align_center('',9," ")
                              .append_chars(num(numInt($trans_menus['mods_total'])),"left",10," ")."\r\n";
                 $print_str .= append_chars("SubModifier Total","right",8," ").align_center('',8," ")
                              .append_chars(num(numInt($trans_menus['submods_total'])),"left",9," ")."\r\n";
                if($trans_menus['item_total'] > 0){
                 $print_str .= append_chars("Retail Items Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                               .append_chars(num(numInt($trans_menus['item_total'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
                }

                $print_str .= append_chars("Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                              .append_chars(num(numInt($total+$trans_menus['mods_total']+$trans_menus['item_total']+$trans_menus['submods_total'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= "\r\n";
            #SUBCATEGORIES
                $subcats = $trans_menus['sub_cats'];
                // $print_str .= append_chars('Menu Subcategories:',"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                $print_str .= append_chars('Menu Types:',"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars('',"left",PAPER_RD_COL_3," ")."\r\n";
                $qty = 0;
                $total = 0;
                foreach ($subcats as $id => $val) {
                    $print_str .= append_chars($val['name'],"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                               .append_chars(num(numInt($val['amount'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $qty += $val['qty'];
                    $total += $val['amount'];
                 }
                $print_str .= "-----------------"."\r\n";
                $print_str .= append_chars("Total","right",PAPER_RD_COL_1," ").align_center($qty,PAPER_RD_COL_2," ")
                              .append_chars(num(numInt($total)),"left",PAPER_RD_COL_3_3," ")."\r\n";
                // $print_str .= append_chars("Modifiers Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                //               .append_chars(numInt($trans_menus['mods_total']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                // $print_str .= append_chars("SubModifier Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                //               .append_chars(numInt($trans_menus['submods_total']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                // $print_str .= append_chars("Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                //               .append_chars(numInt($total+$trans_menus['mods_total']+$trans_menus['submods_total']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= "\r\n";
            #FREE MENUS
                $free = $trans_menus['free_menus'];
                $print_str .= append_chars('Free Menus:',"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars('',"left",PAPER_RD_COL_3," ")."\r\n";
                $fm = array();
                foreach ($free as $ms) {
                    if(!isset($fm[$ms->menu_id])){
                        $mn = array();
                        $mn['name'] = $ms->menu_name;
                        $mn['cat_id'] = $ms->cat_id;
                        $mn['qty'] = $ms->qty;
                        $mn['amount'] = $ms->sell_price * $ms->qty;
                        $mn['sell_price'] = $ms->sell_price;
                        $mn['code'] = $ms->menu_code;
                        // $mn['free_user_id'] = $ms->free_user_id;
                        $fm[$ms->menu_id] = $mn;
                    }
                    else{
                        $mn = $fm[$ms->menu_id];
                        $mn['qty'] += $ms->qty;
                        $mn['amount'] += $ms->sell_price * $ms->qty;
                        $fm[$ms->menu_id] = $mn;
                    }
                }
                $qty = 0;
                $total = 0;
                foreach ($fm as $menu_id => $val) {
                    $print_str .= append_chars(substrwords($val['name'],18,""),"right",18," ").align_center($val['qty'],8," ")
                               .append_chars(num($val['amount'],2),"left",9," ")."\r\n";
                    $qty += $val['qty'];
                    $total += $val['amount'];
                }
                $print_str .= "-----------------"."\r\n";
                $print_str .= append_chars("Total","right",15," ").align_center($qty,PAPER_RD_COL_2," ")
                              .append_chars(num($total,2),"left",10," ")."\r\n";
                $print_str .= "\r\n";
                $print_str .= "\r\n";    
            #FOOTER
                $print_str .= append_chars(substrwords('Invoice Start: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars(iSetObj($trans['first_ref'],'trans_ref'),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= append_chars(substrwords('Invoice End: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars(iSetObj($trans['last_ref'],'trans_ref'),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= append_chars(substrwords('Invoice Ctr: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars($trans['ref_count'],"left",PAPER_RD_COL_3_3," ")."\r\n";

                if(OTHER_MENU_ITEM_SALES){
                    $print_str .= append_chars(substrwords('First Trans No.: ',28,""),"right",10," ").align_center('',4," ")
                             .append_chars((int)iSetObj($trans['first_ref'],'trans_ref'),"left",5," ")."\r\n";
                    $print_str .= append_chars(substrwords('Last Trans No.: ',28,""),"right",10," ").align_center('',5," ")
                                 .append_chars((int)iSetObj($trans['last_ref'],'trans_ref'),"left",5," ")."\r\n";
                }
                if($title_name == "ZREAD"){
                    $gt = $this->old_grand_net_total($date);
                    $print_str .= "\r\n";
                    $print_str .= append_chars(substrwords('OLD GT: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                                 .append_chars(num(numInt( $gt['old_grand_total'])),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $print_str .= append_chars(substrwords('NEW GT: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                                 .append_chars( num(numInt($gt['old_grand_total']+$net_no_adds))  ,"left",PAPER_RD_COL_3_3," ")."\r\n";
                    $print_str .= append_chars(substrwords('Z READ CTR: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                                 .append_chars( $gt['ctr'] ,"left",PAPER_RD_COL_3_3," ")."\r\n";
                }
                $print_str .= PAPER_LINE."\r\n";
            #MALLS
                if(MALL_ENABLED){
                    ####################################
                    # AYALA
                        if(MALL == 'ayala'){
                            
                            $branch_details = $this->setup_model->get_branch_details();
                            $branch = array();
                            foreach ($branch_details as $bv) {
                                $branch = array(
                                    'id' => $bv->branch_id,
                                    'res_id' => $bv->res_id,
                                    'branch_code' => $bv->branch_code,
                                    'name' => $bv->branch_name,
                                    'branch_desc' => $bv->branch_desc,
                                    'contact_no' => $bv->contact_no,
                                    'delivery_no' => $bv->delivery_no,
                                    'address' => $bv->address,
                                    'base_location' => $bv->base_location,
                                    'currency' => $bv->currency,
                                    'inactive' => $bv->inactive,
                                    'tin' => $bv->tin,
                                    'machine_no' => $bv->machine_no,
                                    'bir' => $bv->bir,
                                    'permit_no' => $bv->permit_no,
                                    'serial' => $bv->serial,
                                    'accrdn' => $bv->accrdn,
                                    'email' => $bv->email,
                                    'website' => $bv->website,
                                    'store_open' => $bv->store_open,
                                    'store_close' => $bv->store_close,
                                );
                            }


                            $print_str .= align_center("FOR AYALA",PAPER_WIDTH," ")."\r\n";
                            $print_str .= align_center($branch['name'],PAPER_WIDTH," ")."\r\n";
                            $print_str .= align_center($branch['address'],PAPER_WIDTH," ")."\r\n\r\n";
                            $print_str .= align_center("CONSOLIDATED REPORT Z-READ",PAPER_WIDTH," ")."\r\n\r\n";


                            $total_daily_sales = $total_vatA = $total_rawgrossA = $total_discount = $total_refund = $total_void = $total_charge = $total_non_tax = $total_trans_count = $total_guest = 0;


                            $paytype = array();
                            foreach ($payments_types as $code => $val) {
                                if($code != 'credit'){
                                    if(!isset($paytype[$code])){
                                        $paytype[$code] = array('amount'=>$val['amount']);
                                    }else{
                                        $row = $paytype[$code];
                                        $row['amount'] += $val['amount'];
                                        $paytype[$code] = $row;
                                    }
                                }
                                // $print_str .= append_chars(substrwords(ucwords(strtoupper($code)),12,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                                //               .append_chars(num($val['amount'],2),"left",PAPER_RD_COL_3_3," ")."\r\n";
                                // $pay_qty += $val['qty'];
                            }
                            $paycards = array();
                            if($payments['cards']){
                                $cards = $payments['cards'];
                                foreach($cards as $key => $val){
                                    if(!isset($paycards[$key])){
                                        $paycards[$key] = array('amount'=>$val['amount']);
                                    }else{
                                        $row = $paycards[$key];
                                        $row['amount'] += $val['amount'];
                                        $paycards[$key] = $row;
                                    }
                                }
                            }


                            // for server
                            $rawgrossA = numInt($gross + $charges + $void + $local_tax);
                            $vatA = numInt(($rawgrossA  - $discounts - $void  -  $charges - $nontaxable - $local_tax - numInt($less_vat)) * (1/9.333333));
                            $dlySaleA = numInt($rawgrossA - $discounts - $void - $charges - $vatA - $less_vat + $local_tax);
                            // $t_discounts = $discounts+$less_vat;
                            $rawgrossA =  $rawgrossA - $less_vat;
                            $t_discounts = $discounts;



                            $trans_count = 0;
                            $begor = 0;
                            $endor = 0;
                            $first_inv = array();
                            $last_inv = array();
                            $first_ref = 0;
                            $last_ref = 0;
                            $first_val = 0;
                            $last_val = 0;
                            $invs = array();
                            foreach ($trans['all_orders'] as $ord) {
                                if($ord->type_id == SALES_TRANS && $ord->trans_ref != ""){
                                    $ref = $ord->trans_ref;
                                    if (preg_match('/^(\D*?)(\d+)(.*)/', $ref, $result) == 1){
                                        // if($ord->inactive != 1){
                                            list($all, $prefix, $number, $postfix) = $result;
                                            $ref_val = intval($number);
                                            $invs[$ref_val] = array("ref"=>$ord->trans_ref,"val"=>$ref_val);
                                        // }
                                    }
                                }
                            }
                            ksort($invs);
                            // echo "<pre>",print_r($invs),"</pre>";die();
                            $first_inv = reset($invs);
                            $last_inv = end($invs);
                            if(count($first_inv) > 0){
                                $first_ref = $first_inv['ref'];
                                $first_val = $first_inv['val'];
                            }
                            if(count($last_inv) > 0){
                                $last_ref = $last_inv['ref'];
                                $last_val = $last_inv['val'];
                            }
                            if(count($invs) > 0){
                                $trans_count = ($last_val - $first_val) + 1; 
                            }

                            // echo $trans_count; die();
                            //add yun mga value ng server sa totals
                            $total_daily_sales += $dlySaleA;
                            $total_vatA += $vatA;
                            $total_rawgrossA += $rawgrossA;
                            $total_discount += $t_discounts;
                            $total_void += $void;
                            $total_charge += $charges;
                            $total_non_tax += $nontaxable;
                            // $total_trans_count += $tc_qty;
                            $total_trans_count += $trans_count;
                            $total_guest += $guestCount;
                             // echo $total_trans_count;

                            $terminals = $this->setup_model->get_terminals();


                            $print_str .= append_chars(substrwords('Daily Sales',25,""),"right",22," ").align_center('',2," ")
                                         .append_chars(num($total_daily_sales),"left",10," ")."\r\n";
                            $print_str .= append_chars(substrwords('Total Discount',25,""),"right",22," ").align_center('',2," ")
                                         .append_chars(num($total_discount),"left",10," ")."\r\n";
                            $print_str .= append_chars(substrwords('Total Refund',25,""),"right",22," ").align_center('',2," ")
                                         .append_chars(num(0),"left",10," ")."\r\n";
                            $print_str .= append_chars(substrwords('Total Void',25,""),"right",22," ").align_center('',2," ")
                                         .append_chars(num($total_void),"left",10," ")."\r\n";
                            $print_str .= append_chars(substrwords('Total Vat',25,""),"right",22," ").align_center('',2," ")
                                         .append_chars(num($total_vatA),"left",10," ")."\r\n";
                            $print_str .= append_chars(substrwords('Total Service Charge',25,""),"right",22," ").align_center('',2," ").append_chars(num($total_charge),"left",10," ")."\r\n";  


                            // $print_str .= append_chars(substrwords('Vatable Sales',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                            //              .append_chars(num($dlySaleA-$nontaxable),"left",PAPER_RD_COL_3_3," ")."\r\n";             
                            $print_str .= append_chars(substrwords('Total Non Taxable',22,""),"right",22," ").align_center('',2," ")
                                         .append_chars(num($total_non_tax),"left",10," ")."\r\n";
                            $print_str .= append_chars(substrwords('Row Gross',22,""),"right",22," ").align_center('',2," ")
                                         .append_chars(num($total_rawgrossA),"left",10," ")."\r\n";             
                            $print_str .= append_chars(substrwords('Transaction Count',22,""),"right",22," ").align_center('',2," ")
                                         .append_chars($total_trans_count,"left",10," ")."\r\n";
                            $print_str .= append_chars(substrwords('Customer Count',22,""),"right",22," ").align_center('',2," ")
                                         .append_chars($total_guest,"left",10," ")."\r\n";
                            foreach ($paytype as $k => $v) {
                                $print_str .= append_chars(strtoupper($k),"right",22," ").align_center('',2," ")
                                     .append_chars(num($v['amount']),"left",10," ")."\r\n";       
                            }
                            foreach ($paycards as $k => $v) {
                                $print_str .= append_chars(strtoupper($k),"right",22," ").align_center('',2," ")
                                     .append_chars(num($v['amount']),"left",10," ")."\r\n";       
                            }
                            $terminals = $this->setup_model->get_terminals();
                            // echo "<pre>",print_r($terminals),"</pre>";die();
                            foreach ($terminals as $k => $val) {
                                $print_str .= append_chars('BIR PERMIT '.$val->terminal_id,"right",15," ").align_center('',2," ")
                                     .append_chars($val->permit,"left",17," ")."\r\n";
                                $print_str .= append_chars('SERIAL NO. '.$val->terminal_id,"right",15," ").align_center('',2," ")
                                     .append_chars($val->serial,"left",17," ")."\r\n";
                            }           
                            // $print_str .= append_chars(substrwords('Less SC Disc',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                            //              .append_chars(num($discounts),"left",PAPER_RD_COL_3_3," ")."\r\n";             
                            // $print_str .= append_chars(substrwords('Vat Exempt',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                            //              .append_chars(num($less_vat),"left",PAPER_RD_COL_3_3," ")."\r\n";             
                            // $print_str .= append_chars(substrwords('Zero Rated',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                            //              .append_chars(num($zero_rated),"left",PAPER_RD_COL_3_3," ")."\r\n";             
                            // $print_str .= PAPER_LINE_SINGLE."\r\n";
                            // $print_str .= append_chars(substrwords('Net Sales',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                            //              .append_chars(num($dlySaleA+$vatA),"left",PAPER_RD_COL_3_3," ")."\r\n";             
                            // $print_str .= append_chars(substrwords('Total Qty Sold',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                            //              .append_chars(num($cat_total_qty),"left",PAPER_RD_COL_3_3," ")."\r\n";             
                            $print_str .= PAPER_LINE."\r\n";
                            $print_str .= align_center(sql2Date($post['from']),PAPER_WIDTH," ")."\r\n";
                            $print_str .= align_center("END OF REPORT",PAPER_WIDTH," ")."\r\n";


                        }
                    ####################################
                }
                if ($return_print_str) {
                return $print_str;
            }    
            $this->session->set_userdata('pdf_data','<pre>'.$print_str.'</pre>') ; 
            $this->do_print($print_str,$asJson);
    }
    public function excel_system_sales($sales_id=null,$noPrint=true){
        // echo "<pre>",print_r($sales_id),"</pre>";die();
        ob_start();
        $this->load->library('Excel');
        $sheet = $this->excel->getActiveSheet();
        $filename = 'System Sales Report';
        $styleHeaderCell = array(
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                    ),
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => '3C8DBC')
                    ),
                    'alignment' => array(
                                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    ),
                    'font' => array(
                        'bold' => true,
                        'size' => 14,
                        'color' => array('rgb' => 'FFFFFF'),
                    )
                );
                $styleNum = array(
                    'alignment' => array(
                                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                    ),
                );
                $styleTxt = array(
                    'alignment' => array(
                                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    ),
                );
                $styleTitle = array(
                    'alignment' => array(
                                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    ),
                    'font' => array(
                        'bold' => true,
                        'size' => 16,
                    )
                );
        // ob_start();
        // ob_start();
        // $date = $_GET['date'];
        // $user = $_GET['user'];
        $terminal_id = $_GET['terminal_id'];
        $branch_code = $_GET['branch_id'];
        // $json = $_GET['json'];
        // $dates = date2Sql($date);
        // $args["trans_sales.user_id"] = $_GET['user'];
        $args["trans_sales.type !="] = 'mgtfree';
        // $d = $dates." 00:00:01";
        // $details = $this->setup_model->get_branch_details();
            
        // $open_time = $details[0]->store_open;
        // $close_time = $details[0]->store_close;
        // $print_date =$dates." ".$open_time. "  - ".$dates." ".$close_time;
        $daterange = $_GET['calendar_range'];        
        $dates = explode(" to ",$daterange);
        $from = date2SqlDateTime($dates[0]);        
        $to = date2SqlDateTime($dates[1]);
        $print_date =$from. "  - ".$to;
        // $dates = date2Sql($date);
        // echo $test;die();

        // $args["trans_sales.user_id"] = $user;
        $args["trans_sales.type !="] = 'mgtfree';
        $args["trans_sales.datetime  BETWEEN '".$from."' AND '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);
        // $args["trans_sales.datetime  BETWEEN '".$dates." 00:00:01' AND '".$dates." 23:59:59'"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.branch_code  = '".$branch_code."' "] = array('use'=>'where','val'=>null,'third'=>false);
        
            $printss = $this->system_sales_sales_rep_backoffice_aurora(false,$args,true,$from,$print_date,$branch_code);
           
            echo $printss;
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        header('Cache-Control: max-age=0');
        // $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        // $objWriter->save('php://output');
        // ob_end_clean();
    }
    public function menu_item_sales()
    {
        $data = $this->syter->spawn('menu_item_sales');        
        $data['page_title'] = fa('fa-money')." Menu Item Sales Report";
        $data['code'] = menuItemSalesRep();
        $data['add_css'] = array('css/morris/morris.css','css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css');
        $data['add_js'] = array('js/plugins/morris/morris.min.js','js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js');
        $data['page_no_padding'] = false;
        $data['sideBarHide'] = false;
        $data['load_js'] = 'dine/prints';
        $data['use_js'] = 'menuItemSalesRepJS';
        $this->load->view('page',$data);
    }
    public function check_menu_item_sales(){
        $this->load->helper('dine/print_helper');
        $this->load->model('core/admin_model');
        $this->load->database('default', TRUE);
        $date = $this->input->post('date');
        $user = $this->input->post('user');
        $terminal_id = $this->input->post('terminal_id');
        $json = $this->input->post('json');
        $dates = date2Sql($date);
        $branch_code = $this->input->post('branch_id');
        $daterange = $this->input->post("calendar_range"); 
        $dates = explode(" to ",$daterange);
        $from = date2SqlDateTime($dates[0]);
        $to = date2SqlDateTime($dates[1]);
        // echo $test;die();

        // $args["trans_sales.user_id"] = $user;
        $args["trans_sales.type !="] = 'mgtfree';
        $args["trans_sales.datetime  BETWEEN '".$from."' AND '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);
        // $args["trans_sales.datetime  BETWEEN '".$dates." 00:00:01' AND '".$dates." 23:59:59'"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.branch_code  = '".$branch_code."' "] = array('use'=>'where','val'=>null,'third'=>false);
        if($json == 'true'){
            $asjson = true;
        }else{
            $asjson = false;
        }
        $details = $this->setup_model->get_branch_details(BRANCH_CODE);
            
        // $open_time = $details[0]->store_open;
        // $close_time = $details[0]->store_close;
        // $print_date =$dates." ".$open_time. "  - ".$dates." ".$close_time;
        // $print_date =$from. "  - ".$to;
            // echo $asjson;exit();
        $open_time = $details[0]->store_open;
        $close_time = $details[0]->store_close;
        // $to_date = date('Y-m-d', strtotime($to . ' +1 day'));
        // echo $to_date;die();
        $print_date =$from." ".$open_time. "  - ".$to." ".$close_time;
        // echo $print_date;die();
            $this->admin_model->set_temp_trans_sales($branch_code,$from." ".$open_time,$to." ".$close_time);
            $this->menu_item_sales_backoffice(false,$args,$asjson,$print_date);

    }
    public function menu_item_sales_backoffice($asJson=false,$args=array(),$return_print_str=false,$print_date){
          ////hapchan
            ini_set('memory_limit', '-1');
            set_time_limit(3600);
            
            $print_str = $this->print_header();
            $user = $this->session->userdata('user');
            $time = $this->site_model->get_db_now();
            $post = $this->set_post();
            $curr = $this->search_current();
            $trans = $this->trans_sales($args,$curr);
            // var_dump($trans['net']); die();
            $sales = $trans['sales'];
            $trans_menus = $this->menu_sales($sales['settled']['ids'],$curr,$post['branch_code']);
            $trans_charges = $this->charges_sales($sales['settled']['ids'],$curr,$post['branch_code']);
            $trans_discounts = $this->discounts_sales($sales['settled']['ids'],$curr,$post['branch_code']);
            $tax_disc = $trans_discounts['tax_disc_total'];
            $no_tax_disc = $trans_discounts['no_tax_disc_total'];
            $trans_local_tax = $this->local_tax_sales($sales['settled']['ids'],$curr,$post['branch_code']);
            $trans_tax = $this->tax_sales($sales['settled']['ids'],$curr,$post['branch_code']);
            $trans_no_tax = $this->no_tax_sales($sales['settled']['ids'],$curr,$post['branch_code']);
            $trans_zero_rated = $this->zero_rated_sales($sales['settled']['ids'],$curr,$post['branch_code']);
            $payments = $this->payment_sales($sales['settled']['ids'],$curr,$post['branch_code']);
            // echo 'haha';exit();
            $gross = $trans_menus['gross'];
            $net = $trans['net'];
            $charges = $trans_charges['total'];
            $discounts = $trans_discounts['total'];
            $local_tax = $trans_local_tax['total'];
            $ewt1_count = $trans['ewt1_count'];
            $ewt2_count = $trans['ewt2_count'];
            $ewt5_count = $trans['ewt5_count'];
            $ewt = 0;
            $ewt = $ewt1_count + $ewt2_count + $ewt5_count;

            $types = $trans_discounts['types'];
             $pwd = $snr =   $nopwdsnr = 0;
            foreach ($types as $code => $val) {
                if($code == 'SNDISC'){
                    $snr += round($val['amount'],2);
                }

                if($code == 'PWDISC'){
                    $pwd += round($val['amount'],2);
                }

                if($code != 'PWDISC' && $code != 'SNDISC'){
                    $nopwdsnr += round($val['amount'],2);
                }
            }



            // $less_vat = (($gross+$charges+$local_tax) - $discounts) - $ewt - $net; 
            $less_vat_snrpwd = ($snr + $pwd) / 0.20;
            $less_vat = ($less_vat_snrpwd + $trans_zero_rated['total']) * 0.12;
            // $less_vat = (($gross+$charges+$local_tax) - $discounts) - $ewt - $net;
            if($less_vat < 0)
                $less_vat = 0;

            $tax = $trans_tax['total'];
            $no_tax = $trans_no_tax['total'];
            $zero_rated = $trans_zero_rated['total'];
            $no_tax -= $zero_rated;

            $title_name = "MENU ITEM SALES REPORT";
            $print_str .= align_center($title_name,PAPER_WIDTH," ")."\r\n";
            $print_str .= align_center("TERMINAL ".$post['terminal'],PAPER_WIDTH," ")."\r\n";
            $print_str .= append_chars('Printed On','right',11," ").append_chars(": ".date2SqlDateTime($time),'right',19," ")."\r\n";
            $print_str .= append_chars('Printed BY','right',11," ").append_chars(": ".$user['full_name'],'right',19," ")."\r\n";
            $print_str .= PAPER_LINE."\r\n";
            $print_str .= align_center($print_date,PAPER_WIDTH," ")."\r\n";
            // $print_str .= align_center(sql2DateTime($post['from'])." - ".sql2DateTime($post['to']),PAPER_WIDTH," ")."\r\n";
            // if($post['employee'] != "All")
                // $print_str .= align_center($post['employee'],PAPER_WIDTH," ")."\r\n";

            $print_str .= "\r\n";
            #CATEGORIES
                $cats = $trans_menus['cats'];
                $menus = $trans_menus['menus'];
                $menu_trans_type = $trans_menus['menu_trans_type'];
                $menu_total = $trans_menus['menu_total'];
                $total_qty = $trans_menus['total_qty'];
                usort($cats, function($a, $b) {
                    return $b['amount'] - $a['amount'];
                });
                foreach ($cats as $cat_id => $ca) {
                    if($ca['qty'] > 0){
                        // $print_str .=
                        //      append_chars($ca['name'],'right',18," ")
                        //     .append_chars(num($ca['qty']),'right',10," ")
                        //     .append_chars(num($ca['amount']).'','left',10," ")."\r\n";
                        // $print_str .= append_chars($ca['name'],"right",PAPER_RD_COL_1," ").align_center(num($ca['qty']),PAPER_RD_COL_2," ")
                            // .append_chars(num($ca['amount']).'',"left",9," ")."\r\n";
                        $print_str .= append_chars(substr($ca['name'],0,20),"right",15," ").append_chars($ca['qty'],'right',5," ")
                            .append_chars(num($ca['amount']).'',"left",13," ")."\r\n";
                        $print_str .= PAPER_LINE."\r\n";
                        if(OTHER_MENU_ITEM_SALES){
                                
                                $qtyarray = array();
                                $qtotarray = array();
                                $srparray = array();
                            foreach ($menus as $menu_id => $res) {
                                // echo "<pre>",print_r($res),"</pre>";die();
                                if($ca['cat_id'] == $res['cat_id']){
                                $aqty = 0;
                                $aqty2 = 0;
                                $allqty = 0;
                                $aqtotal = 0;
                                $aqtotal2 = 0;
                                $alltotal = 0;
                                $qtotal = 0;
                                
                                $qty = 0;
                                $qty2 = 0;
                                
                                $price = 0;
                                $aqty = 0;
                                $aqty2 = 0;
                                $print_str .=
                                    append_chars('['.$res['code']."] ".substr($res['name'],0,35),'right',20," ")
                                    ."\r\n";
                                    foreach ($menu_trans_type as $ttype => $mval) {
                                        foreach ($mval as $type => $mnu) {
                                            if($res['menu_id'] == $mnu['menu_id']){
                                            //     // echo "<pre>",print_r($mnu),"</pre>";die();
                                                if($type == 'dinein'){
                                                    $qty = $mnu['qty'];
                                                    $aqty += $qty;
                                                    $qtotal = $mnu['amount'];
                                                    $aqtotal += $qtotal;
                                                    $price = $mnu['sell_price'];
                                                }
                                            }
                                        }
                                    }
                                    // echo "<pre>",print_r($qty),"</pre>";die();
                                    if($qty > 0){
                                        $print_str .=
                                            append_chars('','right',2," ")
                                            .append_chars(substr('DINEIN',0,25),'right',13," ")
                                            .append_chars($qty,'right',5," ")
                                            .append_chars(num($qtotal,2),'right',10," ")
                                            .append_chars($price,'right',5," ")
                                            ."\r\n";
                                    }
                                    $where = array();
                                    $t_det = $this->site_model->get_details($where,'transaction_types');
                                    foreach ($menu_trans_type as $ttype => $mval) {
                                        foreach ($mval as $otype => $mnu) {
                                                $mmm = strtoupper($mnu['type']);
                                            foreach ($t_det as $transs => $transval) {
                                                // echo "<pre>",print_r($transval->trans_name. " ". $mmm),"</pre>";die();
                                                if($res['menu_id'] == $mnu['menu_id'] && $transval->trans_name == $mmm){
                                                    $mnarray[$mmm] = array('qty'=>$mnu['qty'],'amount'=>$mnu['amount'],'sell_price'=>$mnu['sell_price']);
                                                    // $qtotarray[$mmm] = $mnu['amount'];
                                                    // $srparray[$mmm] = $mnu['sell_price'];
                                                    $qty2 = 0;
                                                    $amount = 0;
                                                    $srp = 0;
                                                    foreach ($mnarray as $qrk => $qr) {
                                                        // echo "<pre>",print_r($mnarray),"</pre>";
                                                        if($qrk == $transval->trans_name){
                                                            $qty2 = $qr['qty'];
                                                            $amount = $qr['amount'];
                                                            $srp = $qr['sell_price'];
                                                            $aqty2 += $qty2;
                                                            $aqtotal2 += $amount;
                                                            // $aqtotal2 += $srp;
                                                            
                                                            $print_str .=
                                                            append_chars('','right',2," ")
                                                            .append_chars(substr($transval->trans_name,0,25),'right',13," ")
                                                            .append_chars($qty2,'right',5," ")
                                                            .append_chars(num($amount,2),'right',10," ")
                                                            .append_chars($srp,'right',5," ")
                                                            ."\r\n";
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        
                                    }
                                    
                                    $allqty = $aqty2 + $aqty;
                                    // echo "<pre>",print_r($aqty2." ".$aqty),"</pre>";die();
                                    $alltotal = $aqtotal + $aqtotal2;
                                    $print_str .= PAPER_LINE_SINGLE."\r\n";
                                    $print_str .=
                                                    append_chars('','right',2," ")
                                                    .append_chars(substr('TOTAL',0,25),'right',13," ")
                                                    .append_chars($allqty,'right',5," ")
                                                    .append_chars(num($alltotal,2),'right',10," ")
                                                    // .append_chars($allqty,'right',5," ")
                                                    ."\r\n";


                                    foreach ($trans_menus['mods'] as $mnu_id => $mods) {
                                        // echo "<pre>",print_r($trans_menus['mods']),"</pre>";die();
                                        $where = array();
                                        $mdetails = "";
                                        $allmodqty = 0;
                                        $allmodprice = 0;
                                        $allmodtotal = 0;
                                        $modprice = 0;
                                        $mod_det = $this->site_model->get_details($where,'modifier_groups');
                                        if($mnu_id == $res['menu_id']){
                                            foreach ($mod_det as $mod_d => $modval) {
                                                foreach ($mods as $mod_id => $val) {
                                                    if($modval->name == $val['mod_group_name']){
                                                        $mdetails = $val['mod_group_name'];
                                                    }
                                                }
                                            }
                                            $print_str .= append_chars('','right',2," ")
                                                        .append_chars($mdetails,"right",15," ")."\r\n";
                                            foreach ($mods as $mod_id => $val) {
                                                $allmodqty += $val['qty'];
                                                $allmodprice += $val['total_amt'];
                                                $modprice = $val['price'];
                                                $print_str .= append_chars('','right',2," ")
                                                        .append_chars('*'.substr($val['name'],0,12),"right",13," ").append_chars($val['qty'],'right',4," ")
                                                       .append_chars(numInt($val['total_amt']),"left",5," ")."\r\n";
                                                foreach($trans_menus['submods'] as $m_id => $mval){
                                                    if($mod_id == $m_id){
                                                        foreach ($mval as $sub_id => $sval) {
                                                            $print_str .= append_chars('     '.$sval['name'],"right",18," ").append_chars($sval['qty'],'right',5," ")
                                                            .append_chars(numInt($sval['total_amt']),"left",5," ")."\r\n";
                                                        }

                                                    }

                                                }
                                            }
                                            $print_str .= PAPER_LINE_SINGLE."\r\n";
                                            $print_str .= append_chars('','right',2," ")
                                                        .append_chars('Total',"right",13," ")
                                                        .append_chars($allmodqty,'right',5," ")
                                                        .append_chars(num($allmodprice,2),'right',5," ")
                                                        // .append_chars($modprice,'right',5," ")
                                                        ."\r\n";
                                        } 
                                    }
                                }
                            }  
                        }else{
                            foreach ($menus as $menu_id => $res) {
                                if($ca['cat_id'] == $res['cat_id']){
                                $print_str .=
                                    append_chars(substr($res['name'],0,25),'right',25," ")
                                    .append_chars($res['qty'],'right',5," ")
                                    .append_chars(num($res['amount']),'left',8," ")
                                    ."\r\n";
                                // $print_str .=
                                //     append_chars(null,'right',PAPER_RD_COL_1," ")
                                //     .append_chars(num($res['amount']),'right',PAPER_RD_COL_2," ")
                                //     // .append_chars(num( ($res['amount'] / $menu_total) * 100).'%','left',9," ")
                                //     ."\r\n";

                                    foreach ($trans_menus['mods'] as $mnu_id => $mods) {
                                        if($mnu_id == $res['menu_id']){
                                            foreach ($mods as $mod_id => $val) {
                                                $print_str .= append_chars('*'.$val['name'],"right",15," ").append_chars($val['qty'],'right',5," ")
                                                       .append_chars(numInt($val['total_amt']),"left",8," ")."\r\n";

                                                // if(isset($val['submodifiers'])){
                                                    foreach($trans_menus['submods'] as $m_id => $mval){

                                                        if($mod_id == $m_id){

                                                            foreach ($mval as $sub_id => $sval) {
                                                                # code...
                                                                $print_str .= append_chars('     '.$sval['name'],"right",15," ").append_chars($sval['qty'],'right',5," ")
                                                                .append_chars(numInt($sval['total_amt']),"left",8," ")."\r\n";
                                                            }

                                                        }

                                                    }
                                                // }
                                            }
                                        } 
                                     }
                                }
                            }
                        }
                    $print_str .= PAPER_LINE."\r\n";
                    }
                }
            #SUBCATEGORIES
            $print_str .= "\r\n";
                $subcats = $trans_menus['sub_cats'];
                $print_str .= PAPER_LINE."\r\n";
                $print_str .= append_chars('SUBCATEGORIES:',"right",25," ").align_center('',5," ")
                             .append_chars('',"left",15," ")."\r\n";
                $print_str .= PAPER_LINE."\r\n";
                $qty = 0;
                $total = 0;
                foreach ($subcats as $id => $val) {
                    $print_str .= append_chars($val['name'],"right",20," ").append_chars($val['qty'],'right',5," ")
                               .append_chars(num($val['amount'],2),"left",13," ")."\r\n";
                    $qty += $val['qty'];
                    $total += $val['amount'];
                 }
            // #MODIFIERS
            // $print_str .= "\r\n";
            //     $mods = $trans_menus['mods'];
            //     $print_str .= PAPER_LINE."\r\n";
            //     $print_str .= append_chars('MODIFIERS:',"right",18," ").align_center('',5," ")
            //                  .append_chars('',"left",13," ")."\r\n";
            //     $print_str .= PAPER_LINE."\r\n";
            //     $qty = 0;
            //     $total = 0;
            //     foreach ($mods as $id => $val) {
            //         $print_str .= append_chars($val['name'],"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
            //                    .append_chars(numInt($val['total_amt']),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //         $qty += $val['qty'];
            //         $total += $val['total_amt'];
            //      }
            if(OTHER_MENU_ITEM_SALES){
                $print_str .= "\r\n".PAPER_LINE."\r\n";
                $net_no_adds = $net-$charges-$local_tax;
                $print_str .= append_chars(substrwords('GROSS SALES',20,""),"right",15," ")
                             .append_chars(num($gross,2),"left",23," ")."\r\n";
                $txt = numInt(($charges));
                if($charges > 0)
                    $txt = num($charges,2);
                $print_str .= append_chars(substrwords('Charges Sales',20,""),"right",15," ")
                             .append_chars($txt,"left",23," ")."\r\n";

                $txt = numInt(($local_tax));
                if($local_tax > 0)
                    $txt = "(".numInt(($local_tax)).")";

                // $print_str .= append_chars(substrwords('Local Tax',18,""),"right",25," ")
                //              .append_chars($txt,"left",13," ")."\r\n";
                $print_str .= append_chars(substrwords('Local Tax',20,""),"right",15," ")
                                .append_chars(num($txt,2),"left",23," ")."\r\n";
                $print_str .= append_chars(substrwords('LESS Discounts',20,""),"right",15," ")
                             .append_chars('-'.num($discounts,2),"left",23," ")."\r\n";
                $print_str .= append_chars(substrwords('LESS VAT',20,""),"right",15," ")
                             .append_chars('-'.num($less_vat,2),"left",23," ")."\r\n";
                if(EWT_DISCOUNT){
                    $print_str .= append_chars(substrwords('LESS EWT 1%',23,""),"right",15," ")
                                 .append_chars('-'.num(numInt($ewt1_count)),"left",23," ")."\r\n";
                    $print_str .= append_chars(substrwords('LESS EWT 2%',23,""),"right",15," ")
                                 .append_chars('-'.num(numInt($ewt2_count)),"left",23," ")."\r\n";
                    $print_str .= append_chars(substrwords('LESS EWT 5%',23,""),"right",15," ")
                                 .append_chars('-'.num(numInt($ewt5_count)),"left",23," ")."\r\n";
                }
                $range = $this->input->post('calendar_range');
                $calendar = $this->input->post('calendar');
                if($range != null )
                    $range = $range;
                if($calendar != null )
                    $calendar = $calendar;

                $date1 = $date2 = "";
                if($range != ""){
                    $daterange = $range;
                    $dates = explode(" to ",$daterange);
                    $from = date2SqlDateTime($dates[0]);
                    $to = date2SqlDateTime($dates[1]);

                    $diff = abs(strtotime($to) - strtotime($from));

                    $years = floor($diff / (365*60*60*24));
                    $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
                    $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
                    // echo $years.'-'.$months.'-'.$days;

                }
                if($calendar != ""){
                    $date = date2Sql($calendar);
                    $details = $this->setup_model->get_branch_details();
                
                    $open_time = $details[0]->store_open;
                    $close_time = $details[0]->store_close;
                    $from = date2SqlDateTime($date." ".$open_time);
                    $oa = date('a',strtotime($open_time));
                    $ca = date('a',strtotime($close_time));
                    $to = date2SqlDateTime($date." ".$close_time);
                    if($oa == $ca){
                        $to = date('Y-m-d H:i:s',strtotime($to . "+1 days"));
                    }

                    $diff = abs(strtotime($to) - strtotime($from));

                    $years = floor($diff / (365*60*60*24));
                    $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
                    $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

                }
                $types = $trans_discounts['types'];
                $pwd = $snr =   $nopwdsnr = 0;
                foreach ($types as $code => $val) {
                    if($code == 'SNDISC'){
                        $snr += round($val['amount'],2);
                    }

                    if($code == 'PWDISC'){
                        $pwd += round($val['amount'],2);
                    }

                    if($code != 'PWDISC' && $code != 'SNDISC'){
                        $nopwdsnr += round($val['amount'],2);
                    }
                }


                $zero_rated = $trans_zero_rated['total'];
                $less_vat_snrpwd = ($snr + $pwd) / 0.20;
                $less_vat = ($less_vat_snrpwd + $trans_zero_rated['total']) * 0.12;
                // $less_vat = (($gross+$charges+$local_tax) - $discounts) - $net;
                $less_vat = (($gross+$charges+$local_tax) - $discounts) - $ewt - $net; 
                $v_exempt =  $less_vat_snrpwd + $zero_rated;
                $tot_disc =  $less_vat_snrpwd + $zero_rated + $nopwdsnr;
                $no_tax_disc = $trans_discounts['no_tax_disc_total'];
                $no_tax = $trans_no_tax['total'];
                            
                $no_tax -= $zero_rated;
                $nontaxable = $no_tax - $no_tax_disc;
                $taxable =   ($gross - $less_vat - $nontaxable - $zero_rated - $discounts) / 1.12;
                $vat_ = $taxable * .12;
                $vsales = round($vat_,2) /0.12;
                $types = $trans_charges['types'];
                $tc_amount = 0;
                // foreach ($types as $code => $val) {
                //     $tc_amount += $val['amount'];
                // }
                foreach ($types as $code => $val) {
                    if($days > 1){
                        $tc_amount += round($val['amount'],2);
                    }else{
                        $tc_amount += $val['amount'];
                    }
                }
                $lessvat_variance =   round($vsales,2) + round($vat_,2) + round($tot_disc,2) + round($tc_amount,2);
                $new_total_payment = 0;
                $payments_types = $payments['types'];
                foreach ($payments_types as $code => $val) {
                    $new_total_payment += $val['amount'];
                }
                $total_pay_disc = 0;
                $variance = 0;
                $total_pay_disc = $new_total_payment + $discounts;
                if($lessvat_variance != $total_pay_disc){
                    $variance = $lessvat_variance - round($total_pay_disc,2);
                }
                $net_sales = (($gross + $variance + ($charges - $ewt1_count - $ewt2_count - $ewt5_count)) - $discounts) - $less_vat;
                $print_str .= append_chars(substrwords('TOTAL SALES',20,""),"right",15," ")
                             .append_chars(num($net_sales,2),"left",23," ")."\r\n";
                $print_str .= PAPER_LINE."\r\n";
            }else{
                $print_str .= "\r\n".PAPER_LINE."\r\n";
                $net_no_adds = $net-$charges-$local_tax;
                $print_str .= append_chars(substrwords('TOTAL SALES',20,""),"right",15," ")
                             .append_chars(num($net,2),"left",23," ")."\r\n";
                $txt = numInt(($charges));
                if($charges > 0)
                    $txt = "(".num($charges,2).")";
                $print_str .= append_chars(substrwords('Charges',20,""),"right",15," ")
                             .append_chars($txt,"left",23," ")."\r\n";

                $txt = numInt(($local_tax));
                if($local_tax > 0)
                    $txt = "(".numInt(($local_tax)).")";
                // $print_str .= append_chars(substrwords('Local Tax',18,""),"right",25," ")
                //              .append_chars($txt,"left",13," ")."\r\n";
                $print_str .= append_chars(substrwords('Local Tax',20,""),"right",15," ")
                                .append_chars(num($txt,2),"left",23," ")."\r\n";
                $print_str .= append_chars(substrwords('Discounts',20,""),"right",15," ")
                             .append_chars(num($discounts,2),"left",23," ")."\r\n";
                $print_str .= append_chars(substrwords('LESS VAT',20,""),"right",15," ")
                             .append_chars(num($less_vat,2),"left",23," ")."\r\n";
                $print_str .= append_chars(substrwords('GROSS SALES',20,""),"right",15," ")
                             .append_chars(num($gross,2),"left",23," ")."\r\n";
                $print_str .= PAPER_LINE."\r\n";
            }
            if ($return_print_str) {
                return $print_str;
            } 

            // if($asJson == false){
            //     $this->manager_model->add_event_logs($user['id'],"Menu Item Sales","View");    
            // }else{
            //     $this->manager_model->add_event_logs($user['id'],"Menu Item Sales","Print");                    
            // }
            // $this->session->set_userdata('pdf_data','<pre>'.$print_str.'</pre>') ;
            // if(PRINT_VERSION && PRINT_VERSION == 'V2'){
            //     $this->do_print_v2($print_str,$asJson);  
            // }else if(PRINT_VERSION && PRINT_VERSION == 'V3' && $asJson){
            //     echo $this->html_print($print_str);
            // }else{
            //     $this->do_print($print_str,$asJson);
            // }
            $this->session->set_userdata('pdf_data','<pre>'.$print_str.'</pre>') ; 
            $this->do_print($print_str,$asJson);
    }
    public function excel_menu_item_sales($sales_id=null,$noPrint=true){
        // echo "<pre>",print_r($sales_id),"</pre>";die();
        $this->load->library('Excel');
        $sheet = $this->excel->getActiveSheet();
        $filename = 'Menu Item Sales Report';
        $styleHeaderCell = array(
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                    ),
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => '3C8DBC')
                    ),
                    'alignment' => array(
                                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    ),
                    'font' => array(
                        'bold' => true,
                        'size' => 14,
                        'color' => array('rgb' => 'FFFFFF'),
                    )
                );
                $styleNum = array(
                    'alignment' => array(
                                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                    ),
                );
                $styleTxt = array(
                    'alignment' => array(
                                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    ),
                );
                $styleTitle = array(
                    'alignment' => array(
                                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    ),
                    'font' => array(
                        'bold' => true,
                        'size' => 16,
                    )
                );
        // ob_start();
        // ob_start();
        // $date = $_GET['date'];
        $user = $_GET['user'];
        $branch_code = $_GET['branch_id'];
        // $json = $_GET['json'];
        // $dates = date2Sql($date);
        // $args["trans_sales.user_id"] = $_GET['user'];
        $args["trans_sales.type !="] = 'mgtfree';
        $daterange =  $_GET['calendar_range']; 
        $dates = explode(" to ",$daterange);
        $from = date2SqlDateTime($dates[0]);
        $to = date2SqlDateTime($dates[1]);

        $args["trans_sales.datetime  BETWEEN '".$from."' AND '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);
        // if(CONSOLIDATOR){
        //     if($terminal_id != null){
        //         $args['trans_sales.terminal_id'] = $terminal_id;
        //     }
        // }else{
        //     $args['trans_sales.terminal_id'] = TERMINAL_ID;
        // }
             // $args["trans_sales.terminal_id"] = 1;
        // $args["trans_sales.datetime  BETWEEN '".$dates." 00:00:01' AND '".$dates." 23:59:59'"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.branch_code  = '".$branch_code."' "] = array('use'=>'where','val'=>null,'third'=>false);
        // if(CONSOLIDATOR){
            // $printss = $this->system_sales_rep_backoffice(false,$args,true);
        $details = $this->setup_model->get_branch_details($branch_code);
            
        $open_time = $details[0]->store_open;
        $close_time = $details[0]->store_close;
        // $print_date =$dates." ".$open_time. "  - ".$dates." ".$close_time;
        $print_date = $from. "  - ".$to;
            // $this->system_sales_rep_backoffice($asjson,$args,$asjson,$print_date);
            $printss = $this->menu_item_sales_backoffice(false,$args,true,$print_date);
            // $printss = $this->print_sales_receipt_justin($sales_id,false,true,false,null,true,1,0,null,true);  
        // }else{

            // $printss = $this->print_sales_receipt_justin($sales_id,false,true,false,null,true,1,0,null,false); 
        // }
            

        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        header('Cache-Control: max-age=0');

        echo $printss;
    }
    // public function menuitem_sales_rep_gen()
    // {
        
    //     $user = $this->session->userdata('user');
    //     $time = $this->site_model->get_db_now();

    //     $daterange = $this->input->post("calendar_range");        
    //     $branch_code = $this->input->post("branch_id");        
    //     $dates = explode(" to ",$daterange);
    //     $from = date2SqlDateTime($dates[0]);        
    //     $to = date2SqlDateTime($dates[1]);  

    //     $terminal_id = null;
    //     // if(CONSOLIDATOR){
    //     //     $terminal_id = $this->input->post("terminal_id");
    //     // }     

    //     // $this->cashier_model->db = $this->load->database('main', TRUE);
    //     $args = array();

    //     $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
    //     $args["trans_sales.inactive = 0"] = array('use'=>'where','val'=>null,'third'=>false);
    //     $args["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);
    //     $args["trans_sales.branch_code  = '".$branch_code."' "] = array('use'=>'where','val'=>null,'third'=>false);
    //     $print_str = $this->print_header();
    //     $user = $this->session->userdata('user');
    //     $time = $this->site_model->get_db_now();
    //     $post = $this->set_post();
    //     $curr = $this->search_current();
    //     $trans = $this->trans_sales($args,$curr);
    //     // var_dump($trans['net']); die();
    //     $sales = $trans['sales'];
    //     $trans_menus = $this->menu_sales($sales['settled']['ids'],$curr,$post['branch_code']);
    //     $trans_charges = $this->charges_sales($sales['settled']['ids'],$curr,$post['branch_code']);
    //     $trans_discounts = $this->discounts_sales($sales['settled']['ids'],$curr,$post['branch_code']);
    //     $tax_disc = $trans_discounts['tax_disc_total'];
    //     $no_tax_disc = $trans_discounts['no_tax_disc_total'];
    //     $trans_local_tax = $this->local_tax_sales($sales['settled']['ids'],$curr,$post['branch_code']);
    //     $trans_tax = $this->tax_sales($sales['settled']['ids'],$curr,$post['branch_code']);
    //     $trans_no_tax = $this->no_tax_sales($sales['settled']['ids'],$curr,$post['branch_code']);
    //     $trans_zero_rated = $this->zero_rated_sales($sales['settled']['ids'],$curr,$post['branch_code']);
    //     $payments = $this->payment_sales($sales['settled']['ids'],$curr,$post['branch_code']);
    //     $gross = $trans_menus['gross'];
    //     $net = $trans['net'];
    //     $charges = $trans_charges['total'];
    //     $discounts = $trans_discounts['total'];
    //     $local_tax = $trans_local_tax['total'];
    //     $ewt1_count = $trans['ewt1_count'];
    //     $ewt2_count = $trans['ewt2_count'];
    //     $ewt5_count = $trans['ewt5_count']; 
    //     $ewt = 0;
    //     $ewt = $ewt1_count + $ewt2_count + $ewt5_count;
    //     $less_vat = (($gross+$charges+$local_tax) - $discounts)  - $ewt - $net;
    //     if($less_vat < 0)
    //         $less_vat = 0;

    //     $tax = $trans_tax['total'];
    //     $no_tax = $trans_no_tax['total'];
    //     $zero_rated = $trans_zero_rated['total'];
    //     $no_tax -= $zero_rated;

    //     $cats = $trans_menus['cats'];
    //     $menus = $trans_menus['menus'];
    //     $menu_total = $trans_menus['menu_total'];
    //     $total_qty = $trans_menus['total_qty'];
    //     $menu_trans_type = $trans_menus['menu_trans_type'];
    //     usort($cats, function($a, $b) {
    //         return $b['amount'] - $a['amount'];
    //     });

    //      // echo "<pre>", print_r($menu_trans_type), "</pre>"; die();
    //     $this->make->sDiv();
    //         $this->make->sTable(array("id"=>"main-tbl", 'class'=>'table reportTBL sortable'));
    //             $this->make->sTableHead();
    //                 $this->make->sRow();
    //                     if(OTHER_MENU_ITEM_SALES){
    //                         $this->make->th('Menu Name');
    //                         $this->make->th('Qty');
    //                         $this->make->th('Total');
    //                         $this->make->th('Price');
    //                     }else{
    //                         $this->make->th('Menu Name');
    //                         $this->make->th('Qty');
    //                         $this->make->th('Total'); 
    //                     }
    //                 $this->make->eRow();
    //             $this->make->eTableHead();
    //             $this->make->sTableBody();
    //                 foreach ($cats as $cat_id => $ca) {
    //                     if($ca['qty'] > 0){

    //                         $this->make->sRow(array("style"=>"background-color:yellow;"));
    //                             $this->make->td($ca['name']);
    //                             $this->make->td($ca['qty'], array("style"=>"text-align:right"));                                    
    //                             $this->make->td(num($ca['amount']), array("style"=>"text-align:right"));  
    //                             if(OTHER_MENU_ITEM_SALES){
    //                              $this->make->td('');    
    //                             }                          
    //                         $this->make->eRow();
    //                         if(OTHER_MENU_ITEM_SALES){
                                
    //                             $qtyarray = array();
    //                             $qtotarray = array();
    //                             $srparray = array();
    //                         foreach ($menus as $menu_id => $res) {
    //                                 // echo "<pre>", print_r($res), "</pre>"; die(); 
    //                             if($ca['cat_id'] == $res['cat_id']){
    //                             $aqty = 0;
    //                             $aqty2 = 0;
    //                             $allqty = 0;
    //                             $aqtotal = 0;
    //                             $aqtotal2 = 0;
    //                             $alltotal = 0;
    //                             $qtotal = 0;
                                
    //                             $qty = 0;
    //                             $qty2 = 0;
                                
    //                             $price = 0;
    //                             $aqty = 0;
    //                             $aqty2 = 0;
    //                                 $this->make->sRow();
    //                                     $this->make->td('['.$res['code']."] ".substr($res['name'],0,35),array('colspan'=>4,'style'=>'font-weight: 800;'));
    //                                 $this->make->eRow();
    //                                 foreach ($menu_trans_type as $ttype => $mval) {
    //                                     foreach ($mval as $type => $mnu) {
    //                                     // echo "<pre>", print_r($res['menu_id']), "</pre>"; die();                           
    //                                         if($res['menu_id'] == $mnu['menu_id']){
    //                                             if($type == 'dinein'){
    //                                                 $qty = $mnu['qty'];
    //                                                 $aqty += $qty;
    //                                                 $qtotal = $mnu['amount'];
    //                                                 $aqtotal += $qtotal;
    //                                                 $price = $mnu['sell_price'];
    //                                             }
    //                                         }
    //                                     }
    //                                 }
    //                                 if($qty > 0){
    //                                     $this->make->sRow();
    //                                         $this->make->td('DINEIN');                           
    //                                         $this->make->td($qty, array("style"=>"text-align:right"));                            
    //                                         $this->make->td(num($qtotal,2), array("style"=>"text-align:right"));                            
    //                                         $this->make->td(num($price,2), array("style"=>"text-align:right"));                           
    //                                     $this->make->eRow();
    //                                 }
    //                                 $where = array();
    //                                 $t_det = $this->site_model->get_details($where,'transaction_types');
    //                                 foreach ($menu_trans_type as $ttype => $mval) {
    //                                     foreach ($mval as $otype => $mnu) {
    //                                             $mmm = strtoupper($mnu['type']);
    //                                         foreach ($t_det as $transs => $transval) {
    //                                             if($res['menu_id'] == $mnu['menu_id'] && $transval->trans_name == $mmm){
    //                                                 $mnarray[$mmm] = array('qty'=>$mnu['qty'],'amount'=>$mnu['amount'],'sell_price'=>$mnu['sell_price']);
    //                                                 $qty2 = 0;
    //                                                 $amount = 0;
    //                                                 $srp = 0;
    //                                                 foreach ($mnarray as $qrk => $qr) {
    //                                                     if($qrk == $transval->trans_name){
    //                                                         $qty2 = $qr['qty'];
    //                                                         $amount = $qr['amount'];
    //                                                         $srp = $qr['sell_price'];
    //                                                         $aqty2 += $qty2;
    //                                                         $aqtotal2 += $amount;
    //                                                         $this->make->sRow();
    //                                                             $this->make->td(substr($transval->trans_name,0,25));                           
    //                                                             $this->make->td($qty2, array("style"=>"text-align:right"));                             
    //                                                             $this->make->td(num($amount,2), array("style"=>"text-align:right"));                            
    //                                                             $this->make->td(num($srp,2), array("style"=>"text-align:right"));                            
    //                                                         $this->make->eRow();
    //                                                     }
    //                                                 }
    //                                             }
    //                                         }
    //                                     }
                                        
    //                                 }
                                    
    //                                 $allqty = $aqty2 + $aqty;
    //                                 $alltotal = $aqtotal + $aqtotal2;
    //                                 $this->make->sRow(array('style'=>'background-color:#348FE2'));
    //                                     $this->make->td('TOTAL',array('style'=>'font-weight: 600;'));                           
    //                                     $this->make->td($allqty, array("style"=>"text-align:right"));                            
    //                                     $this->make->td(num($alltotal,2), array("style"=>"text-align:right"));                            
    //                                     $this->make->td('');                           
    //                                 $this->make->eRow();

    //                                 foreach ($trans_menus['mods'] as $mnu_id => $val) {
    //                                     $where = array();
    //                                     $mdetails = "";
    //                                     $allmodqty = 0;
    //                                     $allmodprice = 0;
    //                                     $allmodtotal = 0;
    //                                     $modprice = 0;
    //                                     $mod_det = $this->site_model->get_details($where,'modifier_groups');
    //                                     if($mnu_id == $res['menu_id']){
    //                                         foreach ($mod_det as $mod_d => $modval) {
    //                                             // foreach ($mods as $val) {
    //                                             // echo "<pre>", print_r($val), "</pre>"; die();    
    //                                                 if($modval->name == $val['mod_group_name']){
    //                                                     $mdetails = $val['mod_group_name'];
    //                                                 }
    //                                             // }
    //                                         }
    //                                         $this->make->sRow();
    //                                             $this->make->td($mdetails,array());                                                       
    //                                             $this->make->td('');                           
    //                                             $this->make->td('');                           
    //                                             $this->make->td('');                           
    //                                         $this->make->eRow();
    //                                         // foreach ($mods as $mod_id => $val) {
    //                                             $allmodqty += $val['qty'];
    //                                             $allmodprice += $val['total_amt'];
    //                                             $modprice = $val['price'];
    //                                             $this->make->sRow();
    //                                                 $this->make->td('*'.substr($val['name'],0,12),array());                                                       
    //                                                 $this->make->td($val['qty'], array("style"=>"text-align:right"));                             
    //                                                 $this->make->td(num($val['total_amt']), array("style"=>"text-align:right"));                            
    //                                                 $this->make->td('');                           
    //                                             $this->make->eRow();
    //                                             foreach($trans_menus['submods'] as $m_id => $mval){
    //                                                 if($val['mod_id'] == $m_id){
    //                                                     foreach ($mval as $sub_id => $sval) {
    //                                                         $this->make->sRow();
    //                                                             $this->make->td('&nbsp;&nbsp;&nbsp;'.substr($sval['name'],0,12),array());
    //                                                             $this->make->td($sval['qty'], array("style"=>"text-align:right"));                             
    //                                                             $this->make->td(num($sval['total_amt']), array("style"=>"text-align:right"));                             
    //                                                             $this->make->td('');                           
    //                                                         $this->make->eRow();
    //                                                     }

    //                                                 }

    //                                             }
    //                                         // }
    //                                         $this->make->sRow();
    //                                             $this->make->td('Total Modifiers',array());
    //                                             $this->make->td($allmodqty, array("style"=>"text-align:right"));                               
    //                                             $this->make->td(num($allmodprice,2), array("style"=>"text-align:right"));                             
    //                                             $this->make->td('');                           
    //                                         $this->make->eRow();
    //                                     } 
    //                                 }
    //                             }
    //                         }  
    //                     }else{


    //                         foreach ($menus as $menu_id => $res) {
    //                             if($ca['cat_id'] == $res['cat_id']){

    //                                 $this->make->sRow();
    //                                     $this->make->td('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;['.$res['code'].']'.$res['name']);
    //                                     $this->make->td($res['qty'], array("style"=>"text-align:right"));                                 
    //                                     $this->make->td(num($res['amount']), array("style"=>"text-align:right"));                            
    //                                 $this->make->eRow();

    //                                 foreach ($trans_menus['mods'] as $mnu_id => $mods) {
    //                                     if($mnu_id == $res['menu_id']){
    //                                         foreach ($mods as $mod_id => $val) {
    //                                             $where = array('mod_id'=>$mod_id);
    //                                             $dd = $this->site_model->get_details($where,'modifiers');

    //                                             $this->make->sRow();
    //                                                 $this->make->td('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;*['.$dd[0]->mod_code.']'.$val['name']);
    //                                                 $this->make->td($val['qty'], array("style"=>"text-align:right"));                                
    //                                                 $this->make->td(num($val['total_amt']), array("style"=>"text-align:right"));                            
    //                                             $this->make->eRow();

    //                                             foreach($trans_menus['submods'] as $m_id => $mval){

    //                                                 if($mod_id == $m_id){

    //                                                     foreach ($mval as $sub_id => $sval) {
    //                                                         $this->make->sRow();
    //                                                             $this->make->td('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;*['.$dd[0]->mod_code.']'.$sval['name']);
    //                                                             $this->make->td($sval['qty'], array("style"=>"text-align:right"));                          
    //                                                             $this->make->td(num($sval['total_amt']), array("style"=>"text-align:right"));                            
    //                                                         $this->make->eRow();
    //                                                     }

    //                                                 }

    //                                             }

    //                                         }
    //                                     } 
    //                                  }
    //                             }
    //                         }
    //                     }
    //                 // $print_str .= PAPER_LINE."\r\n";
    //                 }
    //             }
    //             $this->make->eTableBody();
    //         $this->make->eTable();
    //     $this->make->eDiv();
    //     if(!OTHER_MENU_ITEM_SALES){
    //         $this->make->sDiv();
    //             $this->make->sTable(array("id"=>"mains-tbl", 'class'=>'table reportTBL sortable'));
    //                 $subcats = $trans_menus['food_type'];
    //                 $qty = 0;
    //                 $total = 0;
    //                 $this->make->sTableHead();
    //                     $this->make->sRow();
    //                         $this->make->th('FOOD TYPE', array("style"=>"text-align:center","colspan"=>2));
    //                         // $this->make->th('Qty');
    //                         // // $this->make->th('VAT Sales');
    //                         // // $this->make->th('VAT');
    //                         // $this->make->th('Total');
    //                         // $this->make->th('Sales (%)');
    //                         // $this->make->th('Cost');
    //                         // $this->make->th('Cost (%)');
    //                         // $this->make->th('Margin');
    //                     $this->make->eRow();
    //                 $this->make->eTableHead();
    //                 foreach ($subcats as $id => $val) {
    //                     // $print_str .= append_chars($val['name'],"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
    //                     //            .append_chars(numInt($val['amount']),"left",PAPER_RD_COL_3_3," ")."\r\n";
    //                     $this->make->sRow();
    //                         $this->make->td($val['name']);
    //                         $this->make->td(numInt($val['amount']), array("style"=>"text-align:right"));                            
    //                         // $this->make->td(num($res->vat_sales), array("style"=>"text-align:right"));                            
    //                         // $this->make->td(num($res->vat), array("style"=>"text-align:right"));                            
    //                         // $this->make->td(num($ca['amount']), array("style"=>"text-align:right"));                            
    //                     $this->make->eRow();
    //                     $qty += $val['qty'];
    //                     $total += $val['amount'];
    //                 }
    //             $this->make->eTable();
    //         $this->make->eDiv();
    //     }
    //     $subcats = $trans_menus['sub_cats'];
    //     $this->make->sDiv();
    //         $this->make->sTable(array("id"=>"mains-tbl", 'class'=>'table reportTBL sortable'));
    //             $qty = 0;
    //             $total = 0;
    //             $this->make->sTableHead();
    //                 $this->make->sRow();
    //                     $this->make->th('SUBCATEGORIES', array("style"=>"text-align:center","colspan"=>2));
    //                 $this->make->eRow();
    //             $this->make->eTableHead();
    //             foreach ($subcats as $id => $val) {
    //                 $this->make->sRow();
    //                     $this->make->td($val['name']);
    //                     $this->make->td(numInt($val['amount']), array("style"=>"text-align:right"));                           
    //                 $this->make->eRow();
    //                 $qty += $val['qty'];
    //                 $total += $val['amount'];
    //             }
    //         $this->make->eTable();
    //     $this->make->eDiv();

    //     $this->make->sDiv();
    //         $this->make->sTable(array("id"=>"mains-tbl", 'class'=>'table reportTBL sortable'));
    //             $this->make->sRow();
    //                 $this->make->td('GROSS SALES');
    //                 $this->make->td(num($gross,2), array("style"=>"text-align:right"));                           
    //             $this->make->eRow();
    //             $this->make->sRow();
    //                  $txt = $charges;
    //                 if($charges > 0)
    //                     $txt = "(".num($charges,2).")";
    //                 $net_no_adds = $net-$charges-$local_tax;
    //                 $this->make->td('Charges');
    //                 $this->make->td($txt, array("style"=>"text-align:right"));                           
    //             $this->make->eRow();
    //             $this->make->sRow();
    //                 $txt = $local_tax;
    //                 if($local_tax > 0)
    //                     $txt = "(".$local_tax.")";
    //                 $this->make->td('Local Tax');
    //                 $this->make->td($txt, array("style"=>"text-align:right"));                           
    //             $this->make->eRow();
    //             $this->make->sRow();
    //                 $this->make->td('Discounts');
    //                 $this->make->td(num($discounts,2), array("style"=>"text-align:right"));                           
    //             $this->make->eRow();
    //             $this->make->sRow();
    //                 $this->make->td('LESS VAT');
    //                 $this->make->td(num($less_vat,2), array("style"=>"text-align:right"));                           
    //             $this->make->eRow();
    //             if(EWT_DISCOUNT){
    //                 $this->make->sRow();
    //                     $this->make->td('LESS EWT 1%');
    //                     $this->make->td('-'.num(numInt($ewt1_count)), array("style"=>"text-align:right"));                           
    //                 $this->make->eRow();
    //                 $this->make->sRow();
    //                     $this->make->td('LESS EWT 2%');
    //                     $this->make->td('-'.num(numInt($ewt2_count)), array("style"=>"text-align:right"));                           
    //                 $this->make->eRow();
    //                 $this->make->sRow();
    //                     $this->make->td('LESS EWT 5%');
    //                     $this->make->td('-'.num(numInt($ewt5_count)), array("style"=>"text-align:right"));                           
    //                 $this->make->eRow();
    //             }
    //             $this->make->sRow();
    //                 $this->make->td('TOTAL SALES');
    //                 $this->make->td(num($net,2), array("style"=>"text-align:right"));                           
    //             $this->make->eRow();
    //         $this->make->eTable();
    //     $this->make->eDiv();


    //     update_load(100);
    //     $code = $this->make->code();
    //     $json['code'] = $code;        
    //     $json['tbl_vals'] = $trans;
    //     $json['dates'] = $this->input->post('calendar_range');
    //     echo json_encode($json);
    // }

    public function menuitem_sales_rep_gen_excel(){
        $this->load->library('Excel');
        $sheet = $this->excel->getActiveSheet();

        $terminal_id = null;
        // if(CONSOLIDATOR){
        //     if($_GET['terminal_id']){
        //         $terminal_id = $_GET['terminal_id'];
        //     }
        // }

        $daterange = $_GET['calendar_range'];        
        $branch_code = $_GET['branch_id'];        
        $dates = explode(" to ",$daterange);
        // $from = date2SqlDateTime($dates[0]);
        // $to = date2SqlDateTime($dates[1]);
        $from = date2SqlDateTime($dates[0]);        
        $to = date2SqlDateTime($dates[1]); 

        $args = array();
        $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.inactive = 0"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.branch_code  = '".$branch_code."' "] = array('use'=>'where','val'=>null,'third'=>false);
        // $args["trans_sales.type != '".MGTPROMO."'"] = array('use'=>'where','val'=>null,'third'=>false);
        // $args["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);

        // if(CONSOLIDATOR){
        //     if($terminal_id != null){
        //         $args['trans_sales.terminal_id'] = $terminal_id;
        //     }
        // }else{
        //     $args['trans_sales.terminal_id'] = TERMINAL_ID;
        // }

        $args_date["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);

        $ddate = sql2Date($dates[0]);
        $filename = 'Menu Item Sales Report '.$ddate;
        $rc=1;
        #GET VALUES
            start_load(0);
            $print_str = $this->print_header();
            $user = $this->session->userdata('user');
            $time = $this->site_model->get_db_now();
            $post = $this->set_post();
            $curr = $this->search_current();
            $trans = $this->trans_sales($args,$curr);
            // var_dump($trans['net']); die();
            $sales = $trans['sales'];
            $trans_menus = $this->menu_sales($sales['settled']['ids'],$curr,$post['branch_code']);
            $trans_charges = $this->charges_sales($sales['settled']['ids'],$curr,$post['branch_code']);
            $trans_discounts = $this->discounts_sales($sales['settled']['ids'],$curr,$post['branch_code']);
            $tax_disc = $trans_discounts['tax_disc_total'];
            $no_tax_disc = $trans_discounts['no_tax_disc_total'];
            $trans_local_tax = $this->local_tax_sales($sales['settled']['ids'],$curr,$post['branch_code']);
            $trans_tax = $this->tax_sales($sales['settled']['ids'],$curr,$post['branch_code']);
            $trans_no_tax = $this->no_tax_sales($sales['settled']['ids'],$curr,$post['branch_code']);
            $trans_zero_rated = $this->zero_rated_sales($sales['settled']['ids'],$curr,$post['branch_code']);
            $payments = $this->payment_sales($sales['settled']['ids'],$curr,$post['branch_code']);

            $gross = $trans_menus['gross']; 
            $net = $trans['net'];
            $charges = $trans_charges['total']; 
            $discounts = $trans_discounts['total']; 
            $local_tax = $trans_local_tax['total']; 
            $ewt1_count = $trans['ewt1_count'];
            $ewt2_count = $trans['ewt2_count'];
            $ewt5_count = $trans['ewt5_count'];
            $ewt = 0;
            $ewt = $ewt1_count + $ewt2_count + $ewt5_count;
            $less_vat = (($gross+$charges+$local_tax) - $discounts) - $ewt - $net;
            if($less_vat < 0)
                $less_vat = 0;
            $tax = $trans_tax['total'];
            $no_tax = $trans_no_tax['total'];
            $zero_rated = $trans_zero_rated['total'];
            $no_tax -= $zero_rated;
            update_load(55);
            $cats = $trans_menus['cats'];                 
            $menus = $trans_menus['menus'];
            $menu_total = $trans_menus['menu_total'];
            $total_qty = $trans_menus['total_qty'];
            $menu_trans_type = $trans_menus['menu_trans_type'];
            update_load(60);
            usort($cats, function($a, $b) {
                return $b['amount'] - $a['amount'];
            });
            update_load(80);
        $styleHeaderCell = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '3C8DBC')
            ),
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'font' => array(
                'bold' => true,
                'size' => 14,
                'color' => array('rgb' => 'FFFFFF'),
            )
        );
        $styleNum = array(
            'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
            ),
        );
        $styleTxt = array(
            'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
        );
        $styleTitle = array(
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'font' => array(
                'bold' => true,
                'size' => 16,
            )
        );
        $styleNumC = array(
            'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'f5f755')
            ),
        );
        $styleTxtC = array(
            'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'f5f755')
            ),
        );
        if(OTHER_MENU_ITEM_SALES){
            $headers = array('Menu Name','Quantity','Total','Price');
        }else{
            $headers = array('Menu Name','Quantity','Total');
        }
        $sheet->getColumnDimension('A')->setWidth(60);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(30);
        // $sheet->getColumnDimension('E')->setWidth(20);
        // $sheet->getColumnDimension('F')->setWidth(20);
        // $sheet->getColumnDimension('G')->setWidth(20);
        // $sheet->getColumnDimension('H')->setWidth(20);
        // $sheet->getColumnDimension('I')->setWidth(20);


        $sheet->mergeCells('A'.$rc.':C'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Menu Item Sales Report');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        $rc++;
        
        $dates = explode(" to ",$_GET['calendar_range']);
        $from = sql2DateTime($dates[0]);
        $to = sql2DateTime($dates[1]);
        $sheet->getCell('A'.$rc)->setValue('Date From: '.$from);
        $sheet->mergeCells('A'.$rc.':C'.$rc);
        $rc++;

        $sheet->getCell('A'.$rc)->setValue('Date To: '.$to);
        $sheet->mergeCells('A'.$rc.':C'.$rc);
        $rc++;
        $col = 'A';
        foreach ($headers as $txt) {
            $sheet->getCell($col.$rc)->setValue($txt);
            $sheet->getStyle($col.$rc)->applyFromArray($styleHeaderCell);
            $col++;
        }

        $rc++;

        foreach ($cats as $cat_id => $ca) {
            if($ca['qty'] > 0){
                $sheet->getCell('A'.$rc)->setValue($ca['name']);
                $sheet->getStyle('A'.$rc)->applyFromArray($styleTxtC);
                $sheet->getCell('B'.$rc)->setValue($ca['qty']);
                $sheet->getStyle('B'.$rc)->applyFromArray($styleNumC);
                $sheet->getCell('C'.$rc)->setValue($ca['amount']);
                $sheet->getStyle('C'.$rc)->applyFromArray($styleNumC);
                $rc++;
                if(OTHER_MENU_ITEM_SALES){
                            
                    $qtyarray = array();
                    $qtotarray = array();
                    $srparray = array();
                    foreach ($menus as $menu_id => $res) {
                        if($ca['cat_id'] == $res['cat_id']){
                        $aqty = 0;
                        $aqty2 = 0;
                        $allqty = 0;
                        $aqtotal = 0;
                        $aqtotal2 = 0;
                        $alltotal = 0;
                        $qtotal = 0;
                        
                        $qty = 0;
                        $qty2 = 0;
                        
                        $price = 0;
                        $aqty = 0;
                        $aqty2 = 0;
                            $sheet->getCell('A'.$rc)->setValue('['.$res['code']."] ".substr($res['name'],0,35));
                            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
                            $rc++;
                            // $pdf->SetFont('helvetica', 'B', 9);  
                            // $pdf->Cell(100, 0, '['.$res['code']."] ".substr($res['name'],0,35), '', 0, 'L');
                            // $pdf->ln();
                            // $pdf->SetFont('helvetica', '', 9);  
                            foreach ($menu_trans_type as $ttype => $mval) {
                                foreach ($mval as $type => $mnu) {
                                    if($res['menu_id'] == $mnu['menu_id']){
                                        if($type == 'dinein'){
                                            $qty = $mnu['qty'];
                                            $aqty += $qty;
                                            $qtotal = $mnu['amount'];
                                            $aqtotal += $qtotal;
                                            $price = $mnu['sell_price'];
                                        }
                                    }
                                }
                            }
                            if($qty > 0){
                                $sheet->getCell('A'.$rc)->setValue('DINEIN');
                                $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
                                $sheet->getCell('B'.$rc)->setValue(num($qty));
                                $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
                                $sheet->getCell('C'.$rc)->setValue(num($qtotal));
                                $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
                                $sheet->getCell('D'.$rc)->setValue(num($price));
                                $sheet->getStyle('D'.$rc)->applyFromArray($styleNum);
                                $rc++;
                                // $pdf->Cell(60, 0, 'DINEIN', '', 0, 'L');
                                // $pdf->Cell(40, 0, $qty, '', 0, 'R');
                                // $pdf->Cell(40, 0, num($qtotal), '', 0, 'R');
                                // $pdf->Cell(40, 0, num($price), '', 0, 'R');
                                // $pdf->ln();
                            }
                            $where = array();
                            $t_det = $this->site_model->get_details($where,'transaction_types');
                            foreach ($menu_trans_type as $ttype => $mval) {
                                foreach ($mval as $otype => $mnu) {
                                        $mmm = strtoupper($mnu['type']);
                                    foreach ($t_det as $transs => $transval) {
                                        if($res['menu_id'] == $mnu['menu_id'] && $transval->trans_name == $mmm){
                                            $mnarray[$mmm] = array('qty'=>$mnu['qty'],'amount'=>$mnu['amount'],'sell_price'=>$mnu['sell_price']);
                                            $qty2 = 0;
                                            $amount = 0;
                                            $srp = 0;
                                            foreach ($mnarray as $qrk => $qr) {
                                                if($qrk == $transval->trans_name){
                                                    $qty2 = $qr['qty'];
                                                    $amount = $qr['amount'];
                                                    $srp = $qr['sell_price'];
                                                    $aqty2 += $qty2;
                                                    $aqtotal2 += $amount;
                                                    $sheet->getCell('A'.$rc)->setValue(substr($transval->trans_name,0,25));
                                                    $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
                                                    $sheet->getCell('B'.$rc)->setValue(num($qty2));
                                                    $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
                                                    $sheet->getCell('C'.$rc)->setValue(num($amount));
                                                    $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
                                                    $sheet->getCell('D'.$rc)->setValue(num($srp));
                                                    $sheet->getStyle('D'.$rc)->applyFromArray($styleNum);
                                                    $rc++;
                                                    // $pdf->Cell(60, 0, substr($transval->trans_name,0,25), '', 0, 'L');
                                                    // $pdf->Cell(40, 0, $qty2, '', 0, 'R');
                                                    // $pdf->Cell(40, 0, num($amount), '', 0, 'R');
                                                    // $pdf->Cell(40, 0, num($srp), '', 0, 'R');
                                                    // $pdf->ln();
                                                }
                                            }
                                        }
                                    }
                                }    
                            }
                            $allqty = $aqty2 + $aqty;
                            $alltotal = $aqtotal + $aqtotal2;

                            $sheet->getCell('A'.$rc)->setValue('TOTAL');
                            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
                            $sheet->getCell('B'.$rc)->setValue(num($allqty));
                            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
                            $sheet->getCell('C'.$rc)->setValue(num($alltotal));
                            $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
                            // $sheet->getCell('D'.$rc)->setValue(num($srp));
                            // $sheet->getStyle('D'.$rc)->applyFromArray($styleNumC);
                            $rc++;

                            // $pdf->Cell(60, 0, 'TOTAL', '', 0, 'L');
                            // $pdf->Cell(40, 0, $allqty, '', 0, 'R');
                            // $pdf->Cell(40, 0, num($alltotal), '', 0, 'R');
                            // $pdf->Cell(40, 0, '', '', 0, 'R');
                            // $pdf->ln();

                            foreach ($trans_menus['mods'] as $mnu_id => $val) {
                                $where = array();
                                $mdetails = "";
                                $allmodqty = 0;
                                $allmodprice = 0;
                                $allmodtotal = 0;
                                $modprice = 0;
                                $mod_det = $this->site_model->get_details($where,'modifier_groups');
                                if($mnu_id == $res['menu_id']){
                                    foreach ($mod_det as $mod_d => $modval) {
                                        if($modval->name == $val['mod_group_name']){
                                            $mdetails = $val['mod_group_name'];
                                        }
                                    }
                                    // $pdf->Cell(100, 0, $mdetails, '', 0, 'L');
                                    // $pdf->ln();
                                    $sheet->getCell('A'.$rc)->setValue($mdetails);
                                    $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
                                    $rc++;
                                    $allmodqty += $val['qty'];
                                    $allmodprice += $val['total_amt'];
                                    $modprice = $val['price'];

                                    $sheet->getCell('A'.$rc)->setValue('*'.substr($val['name'],0,12));
                                    $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
                                    $sheet->getCell('B'.$rc)->setValue(num($val['qty']));
                                    $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
                                    $sheet->getCell('C'.$rc)->setValue(num($val['total_amt']));
                                    $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
                                    // $sheet->getCell('D'.$rc)->setValue(num($srp));
                                    // $sheet->getStyle('D'.$rc)->applyFromArray($styleNumC);
                                    $rc++;
                                    // $pdf->Cell(60, 0, '*'.substr($val['name'],0,12), '', 0, 'L');
                                    // $pdf->Cell(40, 0, $val['qty'], '', 0, 'R');
                                    // $pdf->Cell(40, 0, num($val['total_amt']), '', 0, 'R');
                                    // $pdf->Cell(40, 0, '', '', 0, 'R');
                                    // $pdf->ln();
                                    foreach($trans_menus['submods'] as $m_id => $mval){
                                        if($val['mod_id'] == $m_id){
                                            foreach ($mval as $sub_id => $sval) {
                                                // $pdf->Cell(60, 0, '&nbsp;&nbsp;&nbsp;'.substr($sval['name'],0,12), '', 0, 'L');
                                                // $pdf->Cell(40, 0, $sval['qty'], '', 0, 'R');
                                                // $pdf->Cell(40, 0, num($sval['total_amt']), '', 0, 'R');
                                                // $pdf->Cell(40, 0, '', '', 0, 'R');
                                                // $pdf->ln();
                                                $sheet->getCell('A'.$rc)->setValue('&nbsp;&nbsp;&nbsp;'.substr($sval['name'],0,12));
                                                $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
                                                $sheet->getCell('B'.$rc)->setValue(num($sval['qty']));
                                                $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
                                                $sheet->getCell('C'.$rc)->setValue(num($sval['total_amt']));
                                                $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
                                                // $sheet->getCell('D'.$rc)->setValue(num($srp));
                                                // $sheet->getStyle('D'.$rc)->applyFromArray($styleNumC);
                                                $rc++;
                                            }
                                        }
                                    }
                                    $sheet->getCell('A'.$rc)->setValue('Total Modifiers');
                                    $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
                                    $sheet->getCell('B'.$rc)->setValue(num($allmodqty));
                                    $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
                                    $sheet->getCell('C'.$rc)->setValue(num($allmodprice));
                                    $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
                                    // $sheet->getCell('D'.$rc)->setValue(num($srp));
                                    // $sheet->getStyle('D'.$rc)->applyFromArray($styleNumC);
                                    $rc++;
                                    // $pdf->Cell(60, 0, 'Total Modifiers', '', 0, 'L');
                                    // $pdf->Cell(40, 0, $allmodqty, '', 0, 'R');
                                    // $pdf->Cell(40, 0, num($allmodprice), '', 0, 'R');
                                    // $pdf->Cell(40, 0, '', '', 0, 'R');
                                    // $pdf->ln();
                                } 
                            }
                        }
                    }  
                }else{
                    foreach ($menus as $menu_id => $res) {
                        if($ca['cat_id'] == $res['cat_id']){
                            $sheet->getCell('A'.$rc)->setValue('        ['.$res['code'].']'.$res['name']);
                            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
                            $sheet->getCell('B'.$rc)->setValue($res['qty']);
                            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
                            $sheet->getCell('C'.$rc)->setValue($res['amount']);
                            $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
                            $rc++;

                            foreach ($trans_menus['mods'] as $mnu_id => $val) {
                                if($mnu_id == $res['menu_id']){
                                    // foreach ($mods as $mod_id => $val) {

                                        $where = array('mod_id'=>$mod_id);
                                        $dd = $this->site_model->get_details($where,'modifiers');

                                        $sheet->getCell('A'.$rc)->setValue('               *['.$dd[0]->mod_code.']'.$val['name']);
                                        // ['.$dd[0]->mod_code.']'.$val['name']);
                                        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
                                        $sheet->getCell('B'.$rc)->setValue($val['qty']);
                                        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
                                        $sheet->getCell('C'.$rc)->setValue($val['total_amt']);
                                        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
                                        $rc++;

                                        foreach($trans_menus['submods'] as $m_id => $mval){

                                            if($mod_id == $m_id){

                                                foreach ($mval as $sub_id => $sval) {
                                                    $sheet->getCell('A'.$rc)->setValue('               *['.$dd[0]->mod_code.']'.$sval['name']);
                                                    // ['.$dd[0]->mod_code.']'.$val['name']);
                                                    $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
                                                    $sheet->getCell('B'.$rc)->setValue($sval['qty']);
                                                    $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
                                                    $sheet->getCell('C'.$rc)->setValue($sval['total_amt']);
                                                    $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
                                                    $rc++;
                                                }

                                            }

                                        }
                                    // }
                                }
                            }
                        }
                    }
                }
            }
        }
        $rc++;
        $rc++;

        $subcats = $trans_menus['sub_cats'];
        $qty = 0;
        $total = 0;
        $sheet->mergeCells('A'.$rc.':B'.$rc);
        $sheet->getCell('A'.$rc)->setValue('SUBCATEGORIES');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleHeaderCell);
        $rc++;
        foreach ($subcats as $id => $val) {
            $sheet->getCell('A'.$rc)->setValue($val['name']);
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue($val['amount']);
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
            $rc++;
        }
        if(!OTHER_MENU_ITEM_SALES){
            $subcats = $trans_menus['food_type'];
            $qty = 0;
            $total = 0;
            $sheet->mergeCells('A'.$rc.':B'.$rc);
            $sheet->getCell('A'.$rc)->setValue('FOOD TYPE');
            $sheet->getStyle('A'.$rc)->applyFromArray($styleHeaderCell);
            $rc++;
            foreach ($subcats as $id => $val) {
                $sheet->getCell('A'.$rc)->setValue($val['name']);
                $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
                $sheet->getCell('B'.$rc)->setValue($val['amount']);
                $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
                $rc++;
            }
        }


        // foreach ($menus as $res) {
        //         $sheet->getCell('A'.$rc)->setValue($res['code']);
        //         $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        //         $sheet->getCell('B'.$rc)->setValue($res['name']);
        //         $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
        //         $sheet->getCell('C'.$rc)->setValue($res['sell_price']);
        //         $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        //         $sheet->getCell('D'.$rc)->setValue($res['qty']);     
        //         $sheet->getStyle('D'.$rc)->applyFromArray($styleNum);
        //         $sheet->getCell('E'.$rc)->setValue(num( ($res['qty'] / $total_qty) * 100 ).'%');     
        //         $sheet->getStyle('E'.$rc)->applyFromArray($styleNum);
        //         $sheet->getCell('F'.$rc)->setValue(num($res['amount']));     
        //         $sheet->getStyle('F'.$rc)->applyFromArray($styleNum);
        //         $sheet->getCell('G'.$rc)->setValue(num( ($res['amount'] / $menu_total) * 100 ).'%');
        //         $sheet->getStyle('G'.$rc)->applyFromArray($styleNum);
        //         $sheet->getCell('H'.$rc)->setValue($res['cost_price']);
        //         $sheet->getStyle('H'.$rc)->applyFromArray($styleNum);
        //         $sheet->getCell('I'.$rc)->setValue($res['cost_price'] * $res['qty']);
        //         $sheet->getStyle('I'.$rc)->applyFromArray($styleNum);

        //     $rc++;
        // } 
        
        $rc++;
        $sheet->getCell('A'.$rc)->setValue('GROSS SALES');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('B'.$rc)->setValue(num($gross,2));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $sheet->getStyle('A'.$rc.':B'.$rc)->applyFromArray(array('font'=>array('bold' => true,'size'=>13)));

        $rc++;
        $txt = $charges;
        if($charges > 0)
            $txt = "(".num($charges,2).")";
        $net_no_adds = $net-$charges-$local_tax;
        $sheet->getCell('A'.$rc)->setValue('Charges');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('B'.$rc)->setValue($txt);
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $sheet->getStyle('A'.$rc.':B'.$rc)->applyFromArray(array('font'=>array('bold' => true,'size'=>13)));

        $rc++;
        $txt = $local_tax;
        if($local_tax > 0)
            $txt = "(".$local_tax.")";
        $sheet->getCell('A'.$rc)->setValue('Local Tax');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('B'.$rc)->setValue($txt);
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $sheet->getStyle('A'.$rc.':B'.$rc)->applyFromArray(array('font'=>array('bold' => true,'size'=>13)));

        $rc++;
        $sheet->getCell('A'.$rc)->setValue('LESS Discounts');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('B'.$rc)->setValue('-'.num($discounts,2));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $sheet->getStyle('A'.$rc.':B'.$rc)->applyFromArray(array('font'=>array('bold' => true,'size'=>13)));

        $rc++;
        $sheet->getCell('A'.$rc)->setValue('LESS VAT');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('B'.$rc)->setValue('-'.num($less_vat,2));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $sheet->getStyle('A'.$rc.':B'.$rc)->applyFromArray(array('font'=>array('bold' => true,'size'=>13)));

        if(EWT_DISCOUNT){
            $sheet->getCell('A'.$rc)->setValue('LESS EWT 1%');
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue('-'.num(numInt($ewt1_count)));
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
            $sheet->getStyle('A'.$rc.':B'.$rc)->applyFromArray(array('font'=>array('bold' => true,'size'=>13)));
            $rc++;
            $sheet->getCell('A'.$rc)->setValue('LESS EWT 2%');
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue('-'.num(numInt($ewt2_count)));
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
            $sheet->getStyle('A'.$rc.':B'.$rc)->applyFromArray(array('font'=>array('bold' => true,'size'=>13)));
            $rc++;
            $sheet->getCell('A'.$rc)->setValue('LESS EWT 5%');
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue('-'.num(numInt($ewt5_count)));
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
            $sheet->getStyle('A'.$rc.':B'.$rc)->applyFromArray(array('font'=>array('bold' => true,'size'=>13)));
            $rc++;

        }

        $rc++;
        $net_no_adds = $net-$charges-$local_tax;
        $sheet->getCell('A'.$rc)->setValue('TOTAL SALES');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('B'.$rc)->setValue(num($net,2));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $sheet->getStyle('A'.$rc.':B'.$rc)->applyFromArray(array('font'=>array('bold' => true,'size'=>13)));

        


        // $mods_total = $trans_menus['mods_total'];
        // if($mods_total > 0){
        //     $sheet->getCell('A'.$rc)->setValue('Total Modifiers Sale: ');
        //     $sheet->getCell('B'.$rc)->setValue(num($mods_total));
        //     $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        //     $rc++;
        // }
        // $net_no_adds = $net-$charges-$local_tax;
        // $sheet->getCell('A'.$rc)->setValue('Total Sales: ');
        // $sheet->getCell('B'.$rc)->setValue(num($net));
        // $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        // $rc++;
        // $txt = numInt(($charges));
        // if($charges > 0)
        //     $txt = "(".numInt(($charges)).")";
        // $sheet->getCell('A'.$rc)->setValue('Total Charges: ');
        // $sheet->getCell('B'.$rc)->setValue($txt);
        // $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        // $rc++;
        // $txt = numInt(($local_tax));
        // if($local_tax > 0)
        //     $txt = "(".numInt(($local_tax)).")";
        // $sheet->getCell('A'.$rc)->setValue('Total Local Tax: ');
        // $sheet->getCell('B'.$rc)->setValue($txt);
        // $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        // $rc++;
        // $sheet->getCell('A'.$rc)->setValue('Total Discounts: ');
        // $sheet->getCell('B'.$rc)->setValue(num($discounts));
        // $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        // $rc++;
        // $sheet->getCell('A'.$rc)->setValue('Total VAT EXEMPT: ');
        // $sheet->getCell('B'.$rc)->setValue(num($less_vat));
        // $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        // $rc++;
        // $sheet->getCell('A'.$rc)->setValue('Total Gross Sales: ');
        // $sheet->getCell('B'.$rc)->setValue(num($gross));
        // $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        // $rc++;
        
        update_load(100);
        // if (ob_get_contents())
        // ob_end_clean();
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $objWriter->save('php://output');
    }
    public function menuitem_sales_rep_pdf()
    {
        // Include the main TCPDF library (search for installation path).
        require_once( APPPATH .'third_party/tcpdf.php');
        $this->load->model("dine/setup_model");
        date_default_timezone_set('Asia/Manila');

        // create new PDF document
        $pdf = new TCPDF("P", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('iPOS');
        $pdf->SetTitle('Menu Item Sales Report');
        $pdf->SetSubject('');
        $pdf->SetKeywords('');

        // set default header data
        $branch_code = $_GET['branch_id'];   
        $setup = $this->setup_model->get_details($branch_code);
        $set = $setup[0];
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $set->branch_name, $set->address);

        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        // ---------------------------------------------------------
        $this->load->model('dine/setup_model');
        // $this->load->database('main', TRUE);
        // $this->menu_model->db = $this->load->database('main', TRUE);
        $this->load->model("dine/menu_model");
        start_load(0);

        // set font
        $pdf->SetFont('helvetica', 'B', 11);

        // add a page
        $pdf->AddPage();
        
        // $menu_cat_id = $_GET['menu_cat_id'];        
        // $daterange = $_GET['calendar_range'];        
        // $dates = explode(" to ",$daterange);
        // $from = date2SqlDateTime($dates[0]);
        // $to = date2SqlDateTime($dates[1]);
        // $from = date2SqlDateTime($dates[0]. " ".$set->store_open);        
        // $to = date2SqlDateTime(date('Y-m-d', strtotime($dates[1] . ' +1 day')). " ".$set->store_open);
        // $trans = $this->menu_model->get_cat_sales_rep($from, $to, $menu_cat_id);
        // $trans_ret = $this->menu_model->get_cat_sales_rep_retail($from, $to, "");
        // $trans_mod = $this->menu_model->get_mod_cat_sales_rep($from, $to, $menu_cat_id);
        // $trans_payment = $this->menu_model->get_payment_date($from, $to);
        // echo $this->db->last_query();die();     

        $terminal_id = null;
        // if(CONSOLIDATOR){
        //     if($_GET['terminal_id']){
        //         $terminal_id = $_GET['terminal_id'];
        //     }
        // }

        $daterange = $_GET['calendar_range'];        
        $dates = explode(" to ",$daterange);
        // $from = date2SqlDateTime($dates[0]);
        // $to = date2SqlDateTime($dates[1]);
        $from = date2SqlDateTime($dates[0]);        
        $to = date2SqlDateTime($dates[1]); 

        $args = array();
        $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.inactive = 0"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.branch_code  = '".$branch_code."' "] = array('use'=>'where','val'=>null,'third'=>false);
        // $args["trans_sales.type != '".MGTPROMO."'"] = array('use'=>'where','val'=>null,'third'=>false);
        // $args["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);

        // if(CONSOLIDATOR){
        //     if($terminal_id != null){
        //         $args['trans_sales.terminal_id'] = $terminal_id;
        //     }
        // }else{
        //     $args['trans_sales.terminal_id'] = TERMINAL_ID;
        // }       

        // $args_date["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);
        // $post = $this->set_post($_GET['calendar_range']);
        $curr = false;

        $print_str = $this->print_header();
        $user = $this->session->userdata('user');
        $time = $this->site_model->get_db_now();
        $post = $this->set_post();
        $curr = $this->search_current();
        $trans = $this->trans_sales($args,$curr);
        // var_dump($trans['net']); die();
        $sales = $trans['sales'];
        $trans_menus = $this->menu_sales($sales['settled']['ids'],$curr,$post['branch_code']);
        $trans_charges = $this->charges_sales($sales['settled']['ids'],$curr,$post['branch_code']);
        $trans_discounts = $this->discounts_sales($sales['settled']['ids'],$curr,$post['branch_code']);
        $tax_disc = $trans_discounts['tax_disc_total'];
        $no_tax_disc = $trans_discounts['no_tax_disc_total'];
        $trans_local_tax = $this->local_tax_sales($sales['settled']['ids'],$curr,$post['branch_code']);
        $trans_tax = $this->tax_sales($sales['settled']['ids'],$curr,$post['branch_code']);
        $trans_no_tax = $this->no_tax_sales($sales['settled']['ids'],$curr,$post['branch_code']);
        $trans_zero_rated = $this->zero_rated_sales($sales['settled']['ids'],$curr,$post['branch_code']);
        $payments = $this->payment_sales($sales['settled']['ids'],$curr,$post['branch_code']);

        $gross = $trans_menus['gross']; 
        $net = $trans['net'];
        $charges = $trans_charges['total']; 
        $discounts = $trans_discounts['total']; 
        $local_tax = $trans_local_tax['total'];
        $ewt1_count = $trans['ewt1_count'];
        $ewt2_count = $trans['ewt2_count'];
        $ewt5_count = $trans['ewt5_count'];
        $ewt = 0;
        $ewt = $ewt1_count + $ewt2_count + $ewt5_count;
        $less_vat = (($gross+$charges+$local_tax) - $discounts) - $ewt - $net;
        if($less_vat < 0)
            $less_vat = 0;
        $tax = $trans_tax['total'];
        $no_tax = $trans_no_tax['total'];
        $zero_rated = $trans_zero_rated['total'];
        $no_tax -= $zero_rated;
        update_load(55);
        $cats = $trans_menus['cats'];                 
        $menus = $trans_menus['menus'];
        $menu_total = $trans_menus['menu_total'];
        $total_qty = $trans_menus['total_qty'];
        $menu_trans_type = $trans_menus['menu_trans_type'];
        update_load(60);
        usort($cats, function($a, $b) {
            return $b['amount'] - $a['amount'];
        });



        $pdf->Write(0, 'Menu Item Sales Report', '', 0, 'L', true, 0, false, false, 0);
        $pdf->SetLineStyle(array('width' => 0.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => 'black'));
        // $pdf->Cell(267, 0, '', 'T', 0, 'C');
        $pdf->ln(0.9);      
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Write(0, 'Report Period:    ', '', 0, 'L', false, 0, false, false, 0);
        $pdf->Write(0, $_GET['calendar_range'], '', 0, 'L', false, 0, false, false, 0);
        $pdf->setX(120);
        $pdf->Write(0, 'Report Generated:    '.(new \DateTime())->format('Y-m-d H:i:s'), '', 0, 'L', true, 0, false, false, 0);
        // $pdf->Write(0, 'Transaction Time:    ', '', 0, 'L', false, 0, false, false, 0);
        // $pdf->setX(120);
        $user = $this->session->userdata('user');
        $pdf->Write(0, 'Generated by:    '.$user["full_name"], '', 0, 'L', true, 0, false, false, 0);        
        $pdf->ln(1);      
        $pdf->Cell(180, 0, '', 'T', 0, 'C');
        $pdf->ln();              

        // echo "<pre>", print_r($trans), "</pre>";die();

        // -----------------------------------------------------------------------------
        $pdf->SetFont('helvetica', 'B', 10);
        // $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => 'black'));
        if(OTHER_MENU_ITEM_SALES){
            $pdf->Cell(60, 0, 'Menu Name', 'B', 0, 'L');        
            $pdf->Cell(40, 0, 'Quantity', 'B', 0, 'R');        
            $pdf->Cell(40, 0, 'Total', 'B', 0, 'R');
            $pdf->Cell(40, 0, 'Price', 'B', 0, 'R');
        }else{
            $pdf->Cell(100, 0, 'Menu Name', 'B', 0, 'L');        
            $pdf->Cell(40, 0, 'Quantity', 'B', 0, 'R');        
            $pdf->Cell(40, 0, 'Total', 'B', 0, 'R');
        }
        $pdf->ln();        

        // $pdf->SetFont('helvetica', '', 9);       
        foreach ($cats as $cat_id => $ca) {
            if($ca['qty'] > 0){
                // $pdf->ln(9);
                $pdf->Cell(180, 0, '', 'T', 0, 'C');
                $pdf->ln();  
                $pdf->SetFont('helvetica', 'B', 10);
                // $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => 'black'));
                $pdf->Cell(60, 0, $ca['name'], '', 0, 'L');
                $pdf->Cell(40, 0, $ca['qty'], '', 0, 'R');
                $pdf->Cell(40, 0, num($ca['amount']), '', 0, 'R');
                $pdf->ln();
                $pdf->Cell(180, 0, '', 'T', 0, 'C');
                $pdf->ln();
                if(OTHER_MENU_ITEM_SALES){
                            
                    $qtyarray = array();
                    $qtotarray = array();
                    $srparray = array();
                    foreach ($menus as $menu_id => $res) {
                        if($ca['cat_id'] == $res['cat_id']){
                        $aqty = 0;
                        $aqty2 = 0;
                        $allqty = 0;
                        $aqtotal = 0;
                        $aqtotal2 = 0;
                        $alltotal = 0;
                        $qtotal = 0;
                        
                        $qty = 0;
                        $qty2 = 0;
                        
                        $price = 0;
                        $aqty = 0;
                        $aqty2 = 0;
                            $pdf->SetFont('helvetica', 'B', 9);  
                            $pdf->Cell(100, 0, '['.$res['code']."] ".substr($res['name'],0,35), '', 0, 'L');
                            $pdf->ln();
                            $pdf->SetFont('helvetica', '', 9);  
                            foreach ($menu_trans_type as $ttype => $mval) {
                                foreach ($mval as $type => $mnu) {
                                    if($res['menu_id'] == $mnu['menu_id']){
                                        if($type == 'dinein'){
                                            $qty = $mnu['qty'];
                                            $aqty += $qty;
                                            $qtotal = $mnu['amount'];
                                            $aqtotal += $qtotal;
                                            $price = $mnu['sell_price'];
                                        }
                                    }
                                }
                            }
                            if($qty > 0){
                                $pdf->Cell(60, 0, 'DINEIN', '', 0, 'L');
                                $pdf->Cell(40, 0, $qty, '', 0, 'R');
                                $pdf->Cell(40, 0, num($qtotal), '', 0, 'R');
                                $pdf->Cell(40, 0, num($price), '', 0, 'R');
                                $pdf->ln();
                            }
                            $where = array();
                            $t_det = $this->site_model->get_details($where,'transaction_types');
                            foreach ($menu_trans_type as $ttype => $mval) {
                                foreach ($mval as $otype => $mnu) {
                                        $mmm = strtoupper($mnu['type']);
                                    foreach ($t_det as $transs => $transval) {
                                        if($res['menu_id'] == $mnu['menu_id'] && $transval->trans_name == $mmm){
                                            $mnarray[$mmm] = array('qty'=>$mnu['qty'],'amount'=>$mnu['amount'],'sell_price'=>$mnu['sell_price']);
                                            $qty2 = 0;
                                            $amount = 0;
                                            $srp = 0;
                                            foreach ($mnarray as $qrk => $qr) {
                                                if($qrk == $transval->trans_name){
                                                    $qty2 = $qr['qty'];
                                                    $amount = $qr['amount'];
                                                    $srp = $qr['sell_price'];
                                                    $aqty2 += $qty2;
                                                    $aqtotal2 += $amount;

                                                    $pdf->Cell(60, 0, substr($transval->trans_name,0,25), '', 0, 'L');
                                                    $pdf->Cell(40, 0, $qty2, '', 0, 'R');
                                                    $pdf->Cell(40, 0, num($amount), '', 0, 'R');
                                                    $pdf->Cell(40, 0, num($srp), '', 0, 'R');
                                                    $pdf->ln();
                                                }
                                            }
                                        }
                                    }
                                }    
                            }
                            $allqty = $aqty2 + $aqty;
                            $alltotal = $aqtotal + $aqtotal2;

                            $pdf->Cell(60, 0, 'TOTAL', '', 0, 'L');
                            $pdf->Cell(40, 0, $allqty, '', 0, 'R');
                            $pdf->Cell(40, 0, num($alltotal), '', 0, 'R');
                            $pdf->Cell(40, 0, '', '', 0, 'R');
                            $pdf->ln();

                            foreach ($trans_menus['mods'] as $mnu_id => $val) {
                                $where = array();
                                $mdetails = "";
                                $allmodqty = 0;
                                $allmodprice = 0;
                                $allmodtotal = 0;
                                $modprice = 0;
                                $mod_det = $this->site_model->get_details($where,'modifier_groups');
                                if($mnu_id == $res['menu_id']){
                                    foreach ($mod_det as $mod_d => $modval) {
                                        if($modval->name == $val['mod_group_name']){
                                            $mdetails = $val['mod_group_name'];
                                        }
                                    }
                                    $pdf->Cell(100, 0, $mdetails, '', 0, 'L');
                                    $pdf->ln();
                                    $allmodqty += $val['qty'];
                                    $allmodprice += $val['total_amt'];
                                    $modprice = $val['price'];

                                    $pdf->Cell(60, 0, '*'.substr($val['name'],0,12), '', 0, 'L');
                                    $pdf->Cell(40, 0, $val['qty'], '', 0, 'R');
                                    $pdf->Cell(40, 0, num($val['total_amt']), '', 0, 'R');
                                    $pdf->Cell(40, 0, '', '', 0, 'R');
                                    $pdf->ln();
                                    foreach($trans_menus['submods'] as $m_id => $mval){
                                        if($val['mod_id'] == $m_id){
                                            foreach ($mval as $sub_id => $sval) {
                                                $pdf->Cell(60, 0, '&nbsp;&nbsp;&nbsp;'.substr($sval['name'],0,12), '', 0, 'L');
                                                $pdf->Cell(40, 0, $sval['qty'], '', 0, 'R');
                                                $pdf->Cell(40, 0, num($sval['total_amt']), '', 0, 'R');
                                                $pdf->Cell(40, 0, '', '', 0, 'R');
                                                $pdf->ln();
                                            }
                                        }
                                    }
                                    $pdf->Cell(60, 0, 'Total Modifiers', '', 0, 'L');
                                    $pdf->Cell(40, 0, $allmodqty, '', 0, 'R');
                                    $pdf->Cell(40, 0, num($allmodprice), '', 0, 'R');
                                    $pdf->Cell(40, 0, '', '', 0, 'R');
                                    $pdf->ln();
                                } 
                            }
                        }
                    }  
                }else{

                    foreach ($menus as $menu_id => $res) {
                        if($ca['cat_id'] == $res['cat_id']){

                            $pdf->Cell(100, 0, '        ['.$res['code'].']'.$res['name'], '', 0, 'L');
                            $pdf->Cell(40, 0, $res['qty'], '', 0, 'R');
                            $pdf->Cell(40, 0, num($res['amount']), '', 0, 'R');
                            $pdf->ln();

                            foreach ($trans_menus['mods'] as $mnu_id => $val) {
                                if($mnu_id == $res['menu_id']){

                                        $where = array('mod_id'=>$val['mod_id']);
                                        $dd = $this->site_model->get_details($where,'modifiers');

                                        $pdf->Cell(100, 0, '               *['.$dd[0]->mod_code.']'.$val['name'], '', 0, 'L');
                                        $pdf->Cell(40, 0, $val['qty'], '', 0, 'R');
                                        $pdf->Cell(40, 0, num($val['total_amt']), '', 0, 'R');
                                        $pdf->ln();

                                        foreach($trans_menus['submods'] as $m_id => $mval){

                                            if($mod_id == $m_id){

                                                foreach ($mval as $sub_id => $sval) {
                                                    $pdf->Cell(100, 0, '               *['.$dd[0]->mod_code.']'.$sval['name'], '', 0, 'L');
                                                    $pdf->Cell(40, 0, $sval['qty'], '', 0, 'R');
                                                    $pdf->Cell(40, 0, num($sval['total_amt']), '', 0, 'R');
                                                    $pdf->ln();
                                                }

                                            }

                                        }
                                    // }
                                }
                            }
                        }
                    }
                }
            }
        }  

        $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => 'black'));
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(180, 0, 'SUBCATEGORIES', 'B', 0, 'L');         
        $pdf->ln(); 
        $pdf->SetFont('helvetica', '', 9);

        $subcats = $trans_menus['sub_cats'];
        $qty = 0;
        $total = 0;
        foreach ($subcats as $id => $val) {
            // $sheet->getCell('A'.$rc)->setValue($val['name']);
            // $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            // $sheet->getCell('B'.$rc)->setValue($val['amount']);
            // $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
            // $rc++;
            $pdf->Cell(90, 0, $val['name'], '', 0, 'L');
            $pdf->Cell(90, 0, num($val['amount']), '', 0, 'R');
            // $pdf->Cell(40, 0, num($ca['amount']), '', 0, 'R');
            $pdf->ln();             
        }
        

        $pdf->ln(10);
        if(!OTHER_MENU_ITEM_SALES){
            $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => 'black'));
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(180, 0, 'FOOD TYPE', 'B', 0, 'L');         
            $pdf->ln(); 
            $pdf->SetFont('helvetica', '', 9);

            $subcats = $trans_menus['food_type'];
            $qty = 0;
            $total = 0;
            foreach ($subcats as $id => $val) {
                $pdf->Cell(90, 0, $val['name'], '', 0, 'L');
                $pdf->Cell(90, 0, num($val['amount']), '', 0, 'R');
                // $pdf->Cell(40, 0, num($ca['amount']), '', 0, 'R');
                $pdf->ln();             
            }
        }

        $pdf->ln(9);  
        $pdf->SetFont('helvetica', 'B', 10);

        $pdf->Cell(90, 0, 'GROSS SALES', '', 0, 'L');
        $pdf->Cell(90, 0, num($gross), '', 0, 'R');
        $pdf->ln();
        
        

        $txt = num($charges);
        if($charges > 0)
            $txt = "(".num($charges,2).")";
        $net_no_adds = $net-$charges-$local_tax;
        $pdf->Cell(90, 0, 'Charges', '', 0, 'L');
        $pdf->Cell(90, 0, $txt, '', 0, 'R');
        $pdf->ln(); 
    

        $txt = num($local_tax);
        if($local_tax > 0)
            $txt = "(".$local_tax.")";
        $pdf->Cell(90, 0, 'Local Tax', '', 0, 'L');
        $pdf->Cell(90, 0, $txt, '', 0, 'R');
        $pdf->ln(); 

        $pdf->Cell(90, 0, 'LESS Discounts', '', 0, 'L');
        $pdf->Cell(90, 0, '-'.num($discounts), '', 0, 'R');
        $pdf->ln();

        $pdf->Cell(90, 0, 'LESS VAT', '', 0, 'L');
        $pdf->Cell(90, 0, '-'.num($less_vat), '', 0, 'R');
        $pdf->ln();

        if(EWT_DISCOUNT){
            $pdf->Cell(90, 0, substrwords('LESS EWT 1%',23,""), '', 0, 'L');
            $pdf->Cell(90, 0, '-'.num(numInt($ewt1_count)), '', 0, 'R');
            $pdf->ln();
            $pdf->Cell(90, 0, substrwords('LESS EWT 2%',23,""), '', 0, 'L');
            $pdf->Cell(90, 0, '-'.num(numInt($ewt2_count)), '', 0, 'R');
            $pdf->ln();
            $pdf->Cell(90, 0, substrwords('LESS EWT 5%',23,""), '', 0, 'L');
            $pdf->Cell(90, 0, '-'.num(numInt($ewt5_count)), '', 0, 'R');
            $pdf->ln();
        }

        $net_no_adds = $net-$charges-$local_tax;
        $pdf->Cell(90, 0, 'TOTAL SALES', '', 0, 'L');
        $pdf->Cell(90, 0, num($net), '', 0, 'R');
        // $pdf->Cell(40, 0, num($ca['amount']), '', 0, 'R');
        $pdf->ln(); 

        
        

        // -----------------------------------------------------------------------------
         update_load(100);
        //Close and output PDF document
        $pdf->Output('menu_items_sales_report.pdf', 'I');

        //============================================================+
        // END OF FILE
        //============================================================+   
    }
    public function back_mod_sales_rep(){
        $this->load->model('dine/reports_model');
        $this->load->helper('dine/reports_helper');
        $data = $this->syter->spawn('mod_sales');
        $data['page_title'] = 'MENU MODIFIERS SALES REPORT';
        $data['code'] = ModSalesBack();
        $data['add_css'] = array('css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css');
        $data['add_js'] = array('js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js');
        $data['load_js'] = 'dine/prints.php';
        $data['use_js'] = 'ModSalesBackJs';
        $this->load->view('page',$data);
    }
    public function check_mod_sales_rep(){
        $this->load->helper('dine/print_helper');
        $this->load->model('core/admin_model');
        $date = $this->input->post('calendar');
        $user = $this->input->post('user');
        $terminal_id = $this->input->post('terminal_id');
        $json = $this->input->post('json');
        $dates = date2Sql($date);
        // echo $test;die();
        $branch_id = $this->input->post('branch_id');
        $daterange = $this->input->post("calendar_range"); 
        // echo $daterange;die();
        $dates = explode(" to ",$daterange);
        $from = date2SqlDateTime($dates[0]);
        $to = date2SqlDateTime($dates[1]);

        // $args["trans_sales.user_id"] = $user;
        $args["trans_sales.type !="] = 'mgtfree';
        // $args["trans_sales.type !="] = 'chit';
        // if(CONSOLIDATOR){
        //     if($terminal_id != null){
        //         $args['trans_sales.terminal_id'] = $terminal_id;
        //     }
        // }else{
        //     $args['trans_sales.terminal_id'] = TERMINAL_ID;
        // }
             // $args["trans_sales.terminal_id"] = 1;
        $args["trans_sales.datetime  BETWEEN '".$from."' AND '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);
        // $args["trans_sales.datetime  BETWEEN '".$dates." 00:00:01' AND '".$dates." 23:59:59'"] = array('use'=>'where','val'=>null,'third'=>false);
        // $args["trans_sales.branch_code  = '".$branch_code."' "] = array('use'=>'where','val'=>null,'third'=>false);
        // echo "<pre>",print_r($args),"</pre>";die();
        if($branch_id){
            $args['trans_sales.branch_code'] = $branch_id;
        }
        if($json == 'true'){
            $asjson = true;
        }else{
            $asjson = false;
        }
        if($branch_id){
            $branch = $branch_id;
        }else{
            $branch = BRANCH_CODE;
        }
        
        $details = $this->setup_model->get_branch_details($branch);     
        $open_time = $details[0]->store_open;
        $close_time = $details[0]->store_close;
        // $to_date = date('Y-m-d', strtotime($dates . ' +1 day'));
        // $to_dates = date('Y-m-d', strtotime($date . ' +1 day'));
        // echo $branch_id.'---'.$date."- ".$open_time."--".$to_dates."- ".$close_time;die();
        // $this->admin_model->set_temp_trans_sales($branch_id,$dates." ".$open_time,$to_date." ".$close_time);
        $this->admin_model->set_temp_trans_sales($branch_id,$from." ".$open_time,$to." ".$close_time);
        //  echo 'testss';die();
        // $print_date =$dates." ".$open_time. "  - ".$to_date." ".$close_time;
        $print_date = $from. "  - ".$to;
        $this->mod_sales_rep_backoffice($asjson,$args,$asjson,$print_date,$branch_id); 
    }
    public function mod_sales_rep_backoffice($asJson=false,$args=array(),$return_print_str=false,$date,$print_date,$branch_code=null){
        ////hapchan
        ini_set('memory_limit', '-1');
        set_time_limit(3600);
        // echo 'test';die();
        $print_str = $this->print_header($branch_code);
        $user = $this->session->userdata('user');
        $time = $this->site_model->get_db_now();
        $post = $this->set_post();
        $curr = $this->search_current();
        $trans = $this->trans_sales($args,$curr);
        // var_dump($trans['net']); die();
        $sales = $trans['sales'];
        $trans_menus = $this->menu_sales($sales['settled']['ids'],$curr,$branch_code);
        $trans_charges = $this->charges_sales($sales['settled']['ids'],$curr,$branch_code);
        $trans_discounts = $this->discounts_sales($sales['settled']['ids'],$curr,$branch_code);
        $tax_disc = $trans_discounts['tax_disc_total'];
        $no_tax_disc = $trans_discounts['no_tax_disc_total'];
        $trans_local_tax = $this->local_tax_sales($sales['settled']['ids'],$curr,$branch_code);
        $trans_tax = $this->tax_sales($sales['settled']['ids'],$curr,$branch_code);
        $trans_no_tax = $this->no_tax_sales($sales['settled']['ids'],$curr,$branch_code);
        $trans_zero_rated = $this->zero_rated_sales($sales['settled']['ids'],$curr,$branch_code);
        $payments = $this->payment_sales($sales['settled']['ids'],$curr,$branch_code);
        
        $gross = $trans_menus['gross'];
        $net = $trans['net'];
        $charges = $trans_charges['total'];
        $discounts = $trans_discounts['total'];
        $local_tax = $trans_local_tax['total'];
        $less_vat = (($gross+$charges+$local_tax) - $discounts) - $net;
        if($less_vat < 0)
            $less_vat = 0;

        $tax = $trans_tax['total'];
        $no_tax = $trans_no_tax['total'];
        $zero_rated = $trans_zero_rated['total'];
        $no_tax -= $zero_rated;

        $title_name = "MENU MODIFIERS SALES REPORT";
        $print_str .= align_center($title_name,PAPER_WIDTH," ")."\r\n";
        $print_str .= align_center("TERMINAL ".$post['terminal'],PAPER_WIDTH," ")."\r\n";
        $print_str .= append_chars('Printed On','right',11," ").append_chars(": ".date2SqlDateTime($time),'right',19," ")."\r\n";
        $print_str .= append_chars('Printed BY','right',11," ").append_chars(": ".$user['full_name'],'right',19," ")."\r\n";
        $print_str .= PAPER_LINE."\r\n";
        $print_str .= align_center($print_date,PAPER_WIDTH," ")."\r\n";
        if($post['employee'] != "All")
            $print_str .= align_center($post['employee'],PAPER_WIDTH," ")."\r\n";

        $print_str .= "\r\n";
        #CATEGORIES
            $cats = $trans_menus['cats'];
            $menus = $trans_menus['menus'];
            $menu_total = $trans_menus['menu_total'];
            $total_qty = $trans_menus['total_qty'];
            usort($cats, function($a, $b) {
                return $b['amount'] - $a['amount'];
            });
            // foreach ($cats as $cat_id => $ca) {
                // if($ca['qty'] > 0){
                    // $print_str .= append_chars(substrwords($ca['name'],15,""),"right",15," ").append_chars($ca['qty'],'right',PAPER_RD_COL_3," ")
                        // .append_chars(num($ca['amount']).'',"left",9," ")."\r\n";
                    $print_str .= PAPER_LINE."\r\n";
                    $qtotal = $atotal = 0;
                    foreach ($menus as $menu_id => $res) {
                        // if($ca['cat_id'] == $res['cat_id']){
                        $print_str .=
                                    append_chars(substrwords($res['name'],15,""),'right',15," ")
                                    .append_chars($res['qty'],'right',PAPER_RD_COL_3," ")
                                    .append_chars(num($res['amount']),'left',9," ")
                                    ."\r\n";
                                    $qtotal += $res['qty'];
                                    $atotal += $res['amount'];
                            // foreach ($trans_menus['mods'] as $mnu_id => $mods) {
                            //     if($mnu_id == $res['menu_id']){
                            //         foreach ($mods as $mod_id => $val) {
                            //             $print_str .= append_chars('*'.$val['name'],"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                            //                    .append_chars(numInt($val['total_amt']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                            //                 foreach($trans_menus['submods'] as $m_id => $mval){

                            //                     if($mod_id == $m_id){

                            //                         foreach ($mval as $sub_id => $sval) {
                            //                             # code...
                            //                             $print_str .= append_chars('     '.$sval['name'],"right",PAPER_RD_COL_1," ").align_center($sval['qty'],PAPER_RD_COL_2," ")
                            //                             .append_chars(numInt($sval['total_amt']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                            //                         }

                            //                     }

                            //                 }
                            //         }
                            //     } 
                            //  }
                        // }
                    }
                    $print_str .= "\r\n";
                    $print_str .=
                                    append_chars(substrwords('Total',15,""),'right',15," ")
                                    .append_chars($qtotal,'right',PAPER_RD_COL_3," ")
                                    .append_chars(num($atotal),'left',9," ")
                                    ."\r\n";
                // $print_str .= PAPER_LINE."\r\n";
                // }
            // }
        
        // #MODIFIERS
        $print_str .= "\r\n";
            $mods = $trans_menus['new_mods'];
            $print_str .= PAPER_LINE."\r\n";
            $print_str .= append_chars('MODIFIERS:',"right",18," ").align_center('',5," ")
                         .append_chars('',"left",13," ")."\r\n";
            $print_str .= PAPER_LINE."\r\n";
            $qty = 0;
            $total = 0;
            // echo "<pre>",print_r($mods),"</pre>";die();
            foreach ($mods as $id => $val) {
            
                // foreach ($v as $val) {
                    # code...
                $print_str .= append_chars($val['name'],"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                           .append_chars(numInt($val['total_amt']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $qty += $val['qty'];
                $total += $val['total_amt'];
                // }
             }
        #SUBCATEGORIES
        $print_str .= "\r\n";
            $subcats = $trans_menus['sub_cats'];
            $print_str .= PAPER_LINE."\r\n";
            $print_str .= append_chars('SUBCATEGORIES:',"right",18," ").align_center('',5," ")
                         .append_chars('',"left",13," ")."\r\n";
            $print_str .= PAPER_LINE."\r\n";
            $qty = 0;
            $total = 0;
            foreach ($subcats as $id => $val) {
                $print_str .= append_chars($val['name'],"right",14," ").align_center($val['qty'],9," ")
                           .append_chars(numInt($val['amount']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                $qty += $val['qty'];
                $total += $val['amount'];
             }

        $print_str .= "\r\n".PAPER_LINE."\r\n";
        $net_no_adds = $net-$charges-$local_tax;
        $print_str .= append_chars(substrwords('TOTAL SALES',18,""),"right",PAPER_TOTAL_COL_1," ")
                     .append_chars(numInt(($net)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
        $txt = numInt(($charges));
        if($charges > 0)
            $txt = "(".numInt(($charges)).")";
        $print_str .= append_chars(substrwords('Charges',18,""),"right",PAPER_TOTAL_COL_1," ")
                     .append_chars($txt,"left",PAPER_TOTAL_COL_2," ")."\r\n";

        $txt = numInt(($local_tax));
        if($local_tax > 0)
            $txt = "(".numInt(($local_tax)).")";
        // $print_str .= append_chars(substrwords('Local Tax',18,""),"right",25," ")
        //              .append_chars($txt,"left",13," ")."\r\n";
        $print_str .= append_chars(substrwords('Local Tax',18,""),"right",PAPER_TOTAL_COL_1," ")
                        .append_chars(numInt($txt),"left",PAPER_TOTAL_COL_2," ")."\r\n";
        $print_str .= append_chars(substrwords('Discounts',18,""),"right",PAPER_TOTAL_COL_1," ")
                     .append_chars(numInt(($discounts)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
        $print_str .= append_chars(substrwords('LESS VAT',18,""),"right",PAPER_TOTAL_COL_1," ")
                     .append_chars(numInt(($less_vat)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
        $print_str .= append_chars(substrwords('GROSS SALES',18,""),"right",PAPER_TOTAL_COL_1," ")
                     .append_chars(numInt(($gross)),"left",PAPER_TOTAL_COL_2," ")."\r\n";
        $print_str .= PAPER_LINE."\r\n";
        if ($return_print_str) {
            return $print_str;
            } 
        // if($asJson == false){
        //     $this->manager_model->add_event_logs($user['id'],"Menu Item Sales","View");    
        // }else{
        //     $this->manager_model->add_event_logs($user['id'],"Menu Item Sales","Print");                    
        // }
            
            $this->session->set_userdata('pdf_data','<pre>'.$print_str.'</pre>') ;   
            // if(PRINT_VERSION && PRINT_VERSION == 'V2'){
            //     $this->do_print_v2($print_str,$asJson);  
            // }else if(PRINT_VERSION && PRINT_VERSION == 'V3' && $asJson){
            //     echo $this->html_print($print_str);
            // }else{
            $this->do_print($print_str,$asJson);
    }
    public function excel_mod_sales($sales_id=null,$noPrint=true){
        // echo "<pre>",print_r($sales_id),"</pre>";die();
        $this->load->library('Excel');
        $sheet = $this->excel->getActiveSheet();
        $filename = 'MENU MODIFIERS SALES REPORT';
        $styleHeaderCell = array(
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN
                        )
                    ),
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => '3C8DBC')
                    ),
                    'alignment' => array(
                                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    ),
                    'font' => array(
                        'bold' => true,
                        'size' => 14,
                        'color' => array('rgb' => 'FFFFFF'),
                    )
                );
                $styleNum = array(
                    'alignment' => array(
                                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                    ),
                );
                $styleTxt = array(
                    'alignment' => array(
                                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    ),
                );
                $styleTitle = array(
                    'alignment' => array(
                                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    ),
                    'font' => array(
                        'bold' => true,
                        'size' => 16,
                    )
                );
        // ob_start();
        // ob_start();
        // $date = $_GET['calendar'];
        // $user = $_GET['user'];
        $terminal_id = $_GET['terminal_id'];
        // $json = $_GET['json'];
        // $dates = date2Sql($date);
        $branch_code = $_GET['branch_id'];
        $daterange = $_GET['calendar_range']; 
        $dates = explode(" to ",$daterange);
        $from = date2SqlDateTime($dates[0]);
        $to = date2SqlDateTime($dates[1]);
        // $args["trans_sales.user_id"] = $_GET['user'];
        $args["trans_sales.type !="] = 'mgtfree';
        // if(CONSOLIDATOR){
        //     if($terminal_id != null){
        //         $args['trans_sales.terminal_id'] = $terminal_id;
        //     }
        // }else{
        //     $args['trans_sales.terminal_id'] = TERMINAL_ID;
        // }
             // $args["trans_sales.terminal_id"] = 1;
        // $args["trans_sales.datetime  BETWEEN '".$dates." 00:00:01' AND '".$dates." 23:59:59'"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.branch_code  = '".$branch_code."' "] = array('use'=>'where','val'=>null,'third'=>false);
        // if(CONSOLIDATOR){
            $details = $this->setup_model->get_branch_details();
            
            $open_time = $details[0]->store_open;
            $close_time = $details[0]->store_close;
            $print_date = $from. "  - ".$to;
            // $print_date =$dates." ".$open_time. "  - ".$dates." ".$close_time;
            // $to_date = date('Y-m-d', strtotime($dates . ' +1 day'));
            // echo print_r($args);die();
            $this->load->model('core/admin_model');
            // $this->admin_model->set_temp_trans_sales($branch_code,$dates." ".$open_time,$to_date." ".$close_time);
            $this->admin_model->set_temp_trans_sales($branch_code,$from." ".$open_time,$to." ".$close_time);
            $printss = $this->mod_sales_rep_backoffice(false,$args,true,$print_date,$branch_code);
            // $printss = $this->print_sales_receipt_justin($sales_id,false,true,false,null,true,1,0,null,true);  
        // }else{

            // $printss = $this->print_sales_receipt_justin($sales_id,false,true,false,null,true,1,0,null,false); 
        // }
            // echo $printss;

        // header('Content-type: application/vnd.ms-excel');
        // header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        // header('Cache-Control: max-age=0');
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        header('Cache-Control: max-age=0');

        echo $printss;
    }
    public function mod_sales_pdf_print()
    {

        $data = $this->session->userdata('pdf_data');
        // echo "<pre>", print_r($data), "</pre>"; die();
        // die();

        // include(dirname(__FILE__)."/cashier.php");
        // Include the main TCPDF library (search for installation path).
        require_once( APPPATH .'third_party/tcpdf.php');
        $this->load->model("dine/setup_model");
        date_default_timezone_set('Asia/Manila');

        // create new PDF document

        $pdf = new TCPDF("P", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('iPOS');
        $pdf->SetTitle('Sales Report');
        $pdf->SetSubject('');
        $pdf->SetKeywords('');

        // set default header data
        $setup = $this->setup_model->get_details(1);
        $set = $setup[0];

        // $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $set->branch_name, $set->address);


        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins

        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);


        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        $pdf->AddPage();



        $data = $this->session->userdata('pdf_data');

        $pdf->writeHTML($data, true, false, true, false, '');
        
        $pdf->Output('mod_sales.pdf', 'I');
    }  
   
}