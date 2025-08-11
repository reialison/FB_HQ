<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once (dirname(__FILE__)."/prints.php");
class Reporting extends Prints {
    var $data = null;
    public function __construct(){
        parent::__construct();
        $this->load->model('dine/cashier_model');  
        $this->load->model('core/admin_model');       
        $this->load->helper('dine/reporting_helper');         
    }
    public function menus_rep(){
        $data = $this->syter->spawn('menu_sales_rep');
        $data['page_title'] = fa('icon-book-open')." Menu Sales Report";
        $data['code'] = menusRep();
        $data['add_css'] = array('css/morris/morris.css','css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css');
        $data['add_js'] = array('js/plugins/morris/morris.min.js','js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js');
        $data['page_no_padding'] = true;
        $data['sideBarHide'] = true;
        $data['load_js'] = 'dine/reporting';
        $data['use_js'] = 'menusRepJS';
        $this->load->view('page',$data);
    }
    public function menus_rep_gen(){
        start_load(0);
        // $_POST['calendar_range'] = '2022/01/01 12:00 AM to 2022/01/20 12:00 AM';
        // $_POST['branch_code'] = 'MAX_MAIN';
        // $_POST['brand'] = 1; 
        $post = $this->set_post();
        // $post = array('args'=>array(),'from'=>"2018-09-10",'to'=>"2018-09-15",'date'=>"2018/09/10 12:00 AM to 2018/09/14 12:00 AM",'terminal'=>"","employee"=>"","title"=>"","shift_id"=>"",'branch_code'=>"PauliPos");

        // $trans_menus = $this->menu_sales(array(1),array(),'MAX_MAIN',1);
        // print_r($trans_menus);
        // exit;
        $daterange = $this->input->post("calendar_range");        
        $dates = explode(" to ",$daterange);
        $from = date2SqlDateTime($dates[0]);        
        $to = date2SqlDateTime($dates[1]);  

        $conso_args = array();
        $conso_args["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);
        // echo print_r($conso_args);die();
        $curr = $this->search_current();
        update_load(10);
        $trans = $this->trans_sales($post['args'],$curr,$post['branch_code'],$post['brand'],$conso_args);
        $sales = $trans['sales'];
        update_load(15);
        $trans_menus = $this->menu_sales($sales['settled']['ids'],$curr,$post['branch_code'],$post['brand'],$from,$to);//print_r($trans_menus);exit;
        update_load(20);
        $trans_charges = $this->charges_sales($sales['settled']['ids'],$curr,$post['branch_code'],$post['brand'],$conso_args);
        update_load(25);
        $trans_discounts = $this->discounts_sales($sales['settled']['ids'],$curr,$post['branch_code'],$post['brand'],$from,$to);        
        // echo '<pre>', print_r($trans_discounts), '</pre>';die();
        update_load(30);
        $trans_local_tax = $this->local_tax_sales($sales['settled']['ids'],$curr,$post['branch_code'],$post['brand'],$conso_args);
        update_load(35);
        $trans_tax = $this->tax_sales($sales['settled']['ids'],$curr,$post['branch_code'],$post['brand'],$conso_args);
        update_load(40);
        $trans_no_tax = $this->no_tax_sales($sales['settled']['ids'],$curr,$post['branch_code'],$post['brand'],$conso_args);
        update_load(45);
        $trans_zero_rated = $this->zero_rated_sales($sales['settled']['ids'],$curr,$post['branch_code'],$post['brand'],$conso_args);
        update_load(50);
        $payments = $this->payment_sales($sales['settled']['ids'],$curr,$post['branch_code'],$post['brand'],$conso_args);
        update_load(53);
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
        
        update_load(55);
        $cats = $trans_menus['cats'];                 
        $menus = $trans_menus['menus'];
        $subcats = $trans_menus['sub_cats'];      
        $menu_total = $trans_menus['menu_total'];
        $total_qty = $trans_menus['total_qty'];
        update_load(60);
        usort($menus, function($a, $b) {
            return $b['amount'] - $a['amount'];
        });
        update_load(80);
        $this->make->sDiv();
            $this->make->sTable(array('class'=>'table reportTBL'));
                $this->make->sTableHead();
                    $this->make->sRow();
                        if($post['branch_code'] == ""){
                        $this->make->th('Branch');                            
                        }
                        $this->make->th('Code');
                        $this->make->th('Name');
                        $this->make->th('Category');
                        $this->make->th('Price (SRP)');
                        $this->make->th('QTY');
                        $this->make->th('QTY AVG');
                        $this->make->th('Sales');
                        $this->make->th('Sales AVG');
                        $this->make->th('Cost');
                        $this->make->th('Total Cost');
                    $this->make->eRow();
                $this->make->eTableHead();
                $this->make->sTableBody();
                    foreach ($menus as $res) {
                        $this->make->sRow();
                            if($post['branch_code'] == ""){
                            $this->make->td($res['branch_name']);                            
                            }
                            $this->make->td($res['code']);
                            $this->make->td($res['name']);
                            $this->make->td($cats[$res['cat_id']]['name']);
                            $this->make->td($res['sell_price']);
                            $this->make->td($res['qty']);
                            $this->make->td( num( ($res['qty'] / $total_qty) * 100 ).'%' );
                            $this->make->td( num($res['amount']) );
                            $this->make->td( num( ($res['amount'] / $menu_total) * 100 ).'%' );
                            $this->make->td($res['cost_price']);
                            $this->make->td($res['cost_price'] * $res['qty']);
                        $this->make->eRow();
                    }    
                    $this->make->sRow();
                        if($post['branch_code'] == ""){                            
                        $this->make->th('');
                        }
                        $this->make->th('');
                        $this->make->th('');
                        $this->make->th('');
                        $this->make->th('Total');
                        $this->make->th($total_qty);
                        $this->make->th('Total');
                        $this->make->th(num($menu_total));
                        $this->make->th('');
                        $this->make->th('');
                        $this->make->th('');
                    $this->make->eRow();             
                    // $mods_total = $trans_menus['mods_total'];
                    // if($mods_total > 0){
                    //     $this->make->sRow();
                    //         $this->make->td('Total Modifiers Sale',array('colspan'=>'6') );
                    //         $this->make->td( num($mods_total));
                    //     $this->make->eRow();
                    // }
                    // $net_no_adds = $net-$charges-$local_tax;
                    // $this->make->sRow();
                    //     $this->make->td('Total Sales');
                    //     $this->make->td( num($net));
                    //     $this->make->td('',array('colspan'=>'6') );
                    // $this->make->eRow();
                    // $txt = numInt(($charges));
                    // if($charges > 0)
                    //     $txt = "(".numInt(($charges)).")";
                    // $this->make->sRow();
                    //     $this->make->td('Total Charges');
                    //     $this->make->td( $txt );
                    //     $this->make->td('',array('colspan'=>'6') );
                    // $this->make->eRow();
                    // $txt = numInt(($local_tax));
                    // if($local_tax > 0)
                    //     $txt = "(".numInt(($local_tax)).")";
                    // $this->make->sRow();
                    //     $this->make->td('Total Local Tax' );
                    //     $this->make->td( $txt );
                    //     $this->make->td('',array('colspan'=>'6') );
                    // $this->make->eRow();   
                    // $this->make->sRow();
                    //     $this->make->td('Total Discounts');
                    //     $this->make->td( num($discounts) );
                    //     $this->make->td('',array('colspan'=>'6') );
                    // $this->make->eRow();
                    // $this->make->sRow();
                    //     $this->make->td('Total VAT EXEMPT');
                    //     $this->make->td( num($less_vat) );
                    //     $this->make->td('',array('colspan'=>'6') );
                    // $this->make->eRow();
                    // $this->make->sRow();
                    //     $this->make->td('Total Gross Sales');
                    //     $this->make->td( num($gross) );
                    //     $this->make->td('',array('colspan'=>'6') );
                    // $this->make->eRow();
                $this->make->eTableBody();
            $this->make->eTable();
            $this->make->sDivRow();
                $this->make->sDivCol(4);
                    $this->make->sTable(array('class'=>'table reportTBL','style'=>'margin-top:10px;'));
                        $this->make->sTableHead();
                            $this->make->sRow();
                                $this->make->th('Category');
                                $this->make->th('QTY');
                                $this->make->th('Amount');
                            $this->make->eRow();
                        $this->make->eTableHead();
                        $this->make->sTableBody();
                            foreach ($cats as $cat_id => $ca) {
                                if($ca['amount'] > 0){
                                    $this->make->sRow();
                                        $this->make->td($ca['name']);
                                        $this->make->td($ca['qty']);
                                        $this->make->td($ca['amount']);
                                    $this->make->eRow();
                                }
                            }    
                        $this->make->eTableBody();
                    $this->make->eTable();
                $this->make->eDivCol();
                $this->make->sDivCol(4);
                    $this->make->sTable(array('class'=>'table reportTBL','style'=>'margin-top:10px;'));
                        $this->make->sTableHead();
                            $this->make->sRow();
                                $this->make->th('Types');
                                $this->make->th('QTY');
                                $this->make->th('Amount');
                            $this->make->eRow();
                        $this->make->eTableHead();
                        $this->make->sTableBody();
                            foreach ($subcats as $id => $val) {
                                if($val['amount'] > 0){
                                    $this->make->sRow();
                                        $this->make->td($val['name']);
                                        $this->make->td($val['qty']);
                                        $this->make->td($val['amount']);
                                    $this->make->eRow();
                                }
                            }
                        $this->make->eTableBody();
                    $this->make->eTable();
                $this->make->eDivCol();
                $this->make->sDivCol(4);
                    $this->make->sTable(array('class'=>'table reportTBL','style'=>'margin-top:10px;'));
                        $this->make->sTableBody();
                            $mods_total = $trans_menus['mods_total'];
                            if($mods_total > 0){
                                $this->make->sRow();
                                    $this->make->td('Total Modifiers Sale');
                                    $this->make->td( num($mods_total));
                                $this->make->eRow();
                            }

                            $submods_total = $trans_menus['submods_total'];
                            if($submods_total > 0){
                                $this->make->sRow();
                                    $this->make->td('Total Sub Modifiers Sale');
                                    $this->make->td( num($submods_total));
                                $this->make->eRow();
                            }
                            $this->make->sRow();
                                $this->make->td('Total Sales');
                                $this->make->td(num($net));
                            $this->make->eRow();
                            $this->make->sRow();
                                $txt = numInt(($charges));
                                if($charges > 0)
                                    $txt = "(".numInt(($charges)).")";
                                $this->make->td('Total Charges');
                                $this->make->td($txt);
                            $this->make->eRow();
                            $this->make->sRow();
                                $txt = numInt(($local_tax));
                                if($local_tax > 0)
                                    $txt = "(".numInt(($local_tax)).")";
                                $this->make->td('Total Local Tax');
                                $this->make->td($txt);
                            $this->make->eRow();
                            $this->make->sRow();
                                $this->make->td('Total Discounts');
                                $this->make->td(num($discounts));
                            $this->make->eRow();
                            $this->make->sRow();
                                $this->make->td('Total VAT EXEMPT');
                                $this->make->td(num($less_vat));
                            $this->make->eRow();
                            $this->make->sRow();
                                $this->make->td('Total Gross Sales');
                                $this->make->td(num($gross));
                            $this->make->eRow();
                        $this->make->eTableBody();
                    $this->make->eTable();
                $this->make->eDivCol();
            $this->make->eDivRow();


        $this->make->eDiv();
        update_load(100);
        $code = $this->make->code();
        $json['code'] = $code;
        $json['tbl_vals'] = '';
        $json['dates'] = $this->input->post('calendar_range');
        echo json_encode($json);
    }
    public function menus_rep_excel(){
        $this->load->library('Excel');
        $sheet = $this->excel->getActiveSheet();
        $filename = 'Menu Sales Report';
        $rc=1;
        #GET VALUES
            start_load(0);
            $post = $this->set_post($_GET['calendar_range'],null,$_GET['branch_code'],$_GET['brand']);
            $daterange = $_GET['calendar_range'];        
            $dates = explode(" to ",$daterange);
            $from = date2SqlDateTime($dates[0]);        
            $to = date2SqlDateTime($dates[1]);  

        $conso_args = array();
        $conso_args["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);
            $curr = true;
            update_load(10);
            $trans = $this->trans_sales($post['args'],$curr,$_GET['branch_code'],$_GET['brand'],$conso_args);
            $sales = $trans['sales'];
            update_load(15);
            $trans_menus = $this->menu_sales($sales['settled']['ids'],$curr,$_GET['branch_code'],$_GET['brand'],$from,$to);
            // echo "<pre>",print_r($post),"</pre>";die();
            update_load(20);
            $trans_charges = $this->charges_sales($sales['settled']['ids'],$curr,$_GET['branch_code'],$_GET['brand'],$conso_args);
            update_load(25);
            $trans_discounts = $this->discounts_sales($sales['settled']['ids'],$curr,$_GET['branch_code'],$_GET['brand'],$from,$to);
            update_load(30);
            $trans_local_tax = $this->local_tax_sales($sales['settled']['ids'],$curr,$_GET['branch_code'],$_GET['brand'],$conso_args);
            update_load(35);
            $trans_tax = $this->tax_sales($sales['settled']['ids'],$curr,$_GET['branch_code'],$_GET['brand'],$conso_args);
            update_load(40);
            $trans_no_tax = $this->no_tax_sales($sales['settled']['ids'],$curr,$_GET['branch_code'],$_GET['brand'],$conso_args);
            update_load(45);
            $trans_zero_rated = $this->zero_rated_sales($sales['settled']['ids'],$curr,$_GET['branch_code'],$_GET['brand'],$conso_args);
            update_load(50);
            $payments = $this->payment_sales($sales['settled']['ids'],$curr,$_GET['branch_code'],$_GET['brand'],$conso_args);
            update_load(53);
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
            update_load(55);
            $cats = $trans_menus['cats'];                 
            $menus = $trans_menus['menus'];
            $menu_total = $trans_menus['menu_total'];
            $total_qty = $trans_menus['total_qty'];
            update_load(60);
            usort($menus, function($a, $b) {
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
        
        $headers = array('Code','Name','Price (SRP)','QTY','QTY(AVG)','Amount','Amount(AVG)','Cost','Total Cost');
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(50);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(20);


        $sheet->mergeCells('A'.$rc.':I'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Menu Sales Report');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        $rc++;
        
        $dates = explode(" to ",$_GET['calendar_range']);
        $from = sql2DateTime($dates[0]);
        $to = sql2DateTime($dates[1]);
        $sheet->getCell('A'.$rc)->setValue('Date From: '.$from);
        $sheet->mergeCells('A'.$rc.':I'.$rc);
        $rc++;

        $sheet->getCell('A'.$rc)->setValue('Date To: '.$to);
        $sheet->mergeCells('A'.$rc.':I'.$rc);
        $rc++;
        $branch_code = $_GET['branch_code'];
        $sheet->getCell('A'.$rc)->setValue('Branch: '.$branch_code);
        $sheet->mergeCells('A'.$rc.':I'.$rc);
        $rc++;

        $brand = $_GET['brand'];
        $brd = $this->setup_model->get_brands($brand);

        if($brd){
             $sheet->getCell('A'.$rc)->setValue('Brand: '.$brd[0]->brand_name);
            $sheet->mergeCells('A'.$rc.':I'.$rc);
            $rc++;
        }

        $col = 'A';
        foreach ($headers as $txt) {
            $sheet->getCell($col.$rc)->setValue($txt);
            $sheet->getStyle($col.$rc)->applyFromArray($styleHeaderCell);
            $col++;
        }

        $rc++;
        // echo "<pre>",print_r($post),"</pre>";die();
        foreach ($menus as $res) {
                $sheet->getCell('A'.$rc)->setValue($res['code']);
                $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
                $sheet->getCell('B'.$rc)->setValue($res['name']);
                $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
                $sheet->getCell('C'.$rc)->setValue($res['sell_price']);
                $sheet->getStyle('C'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                $sheet->getCell('D'.$rc)->setValue($res['qty']);     
                $sheet->getStyle('D'.$rc)->applyFromArray($styleNum);
                $sheet->getCell('E'.$rc)->setValue(num( ($res['qty'] / $total_qty) * 100 ).'%');     
                $sheet->getStyle('E'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                $sheet->getCell('F'.$rc)->setValue($res['amount']);     
                $sheet->getStyle('F'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                $sheet->getCell('G'.$rc)->setValue(num( ($res['amount'] / $menu_total) * 100 ).'%');
                $sheet->getStyle('G'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                $sheet->getCell('H'.$rc)->setValue($res['cost_price']);
                $sheet->getStyle('H'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                $sheet->getCell('I'.$rc)->setValue($res['cost_price'] * $res['qty']);
                $sheet->getStyle('I'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

            $rc++;
        } 
        $rc++;


        $mods_total = $trans_menus['mods_total'];
        if($mods_total > 0){
            $sheet->getCell('A'.$rc)->setValue('Total Modifiers Sale: ');
            $sheet->getCell('B'.$rc)->setValue($mods_total);
            $sheet->getStyle('B'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $rc++;
        }

        $submods_total = $trans_menus['submods_total'];
        if($submods_total > 0){
            $sheet->getCell('A'.$rc)->setValue('Total Sub Modifiers Sale: ');
            $sheet->getCell('B'.$rc)->setValue($submods_total);
            $sheet->getStyle('B'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $rc++;
        }

        $net_no_adds = $net-$charges-$local_tax;
        $sheet->getCell('A'.$rc)->setValue('Total Sales: ');
        $sheet->getCell('B'.$rc)->setValue($net);
        $sheet->getStyle('B'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $rc++;
        $txt = $charges;
        if($charges > 0)
            $txt = "(".$charges.")";
        $sheet->getCell('A'.$rc)->setValue('Total Charges: ');
        $sheet->getCell('B'.$rc)->setValue($txt);
        $sheet->getStyle('B'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $rc++;
        $txt = numInt(($local_tax));
        if($local_tax > 0)
            $txt = "(".numInt(($local_tax)).")";
        $sheet->getCell('A'.$rc)->setValue('Total Local Tax: ');
        $sheet->getCell('B'.$rc)->setValue($txt);
        $sheet->getStyle('B'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue('Total Discounts: ');
        $sheet->getCell('B'.$rc)->setValue($discounts);
        $sheet->getStyle('B'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue('Total VAT EXEMPT: ');
        $sheet->getCell('B'.$rc)->setValue($less_vat);
        $sheet->getStyle('B'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue('Total Gross Sales: ');
        $sheet->getCell('B'.$rc)->setValue($gross);
        $sheet->getStyle('B'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $rc++;
        
        update_load(100);

        if (ob_get_contents())
        ob_end_clean();
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $objWriter->save('php://output');
    }

    public function menus_rep_pdf()
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
        $pdf->SetTitle('Menus Sales Report');
        $pdf->SetSubject('');
        $pdf->SetKeywords('');
        $branch_id = $_GET['branch_code'];
        $brand = "";
        // set default header data
        $setup = $this->setup_model->get_details($branch_id);
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
        $pdf->AddPage();
        // ---------------------------------------------------------
        $this->load->model('dine/setup_model');
        // $this->load->database('main', TRUE);
        start_load(0);
            // $post = $this->set_post($_GET['calendar_range']);
            $curr = false;

            // $terminal_id = null;
            // if(CONSOLIDATOR){
            //     $this->cashier_model->db = $this->load->database('main', TRUE);
            //     $terminal_id = $_GET['terminal_id'];
            // }    

            // $daterange = $_GET['calendar_range'];        
            // $dates = explode(" to ",$daterange);
            // // $from = date2SqlDateTime($dates[0]);
            // // $to = date2SqlDateTime($dates[1]);
            // $from = date2SqlDateTime($dates[0]);        
            // $to = date2SqlDateTime($dates[1]); 

            // $args = array();

            // $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
            // $args["trans_sales.inactive = 0"] = array('use'=>'where','val'=>null,'third'=>false);
            // $args["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);
            // // $args["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);

            // if(CONSOLIDATOR){
            //     if($terminal_id != null){
            //         $args['trans_sales.terminal_id'] = $terminal_id;
            //     }
            //     $conso_args["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);
            // }else{
            //     $args['trans_sales.terminal_id'] = TERMINAL_ID;
            // }
            $post = $this->set_post($_GET['calendar_range'],null,$_GET['branch_code'],$_GET['brand']);
            $daterange = $_GET['calendar_range'];        
            $dates = explode(" to ",$daterange);
            $from = date2SqlDateTime($dates[0]);        
            $to = date2SqlDateTime($dates[1]);  

            $conso_args = array();
            $conso_args["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);
            $curr = true;

            $curr = true;
            update_load(10);
            $trans = $this->trans_sales($post['args'],$curr,$_GET['branch_code'],$_GET['brand'],$conso_args);
            $sales = $trans['sales'];
            update_load(15);
            $trans_menus = $this->menu_sales($sales['settled']['ids'],$curr,$_GET['branch_code'],$_GET['brand'],$from,$to);
            // echo "<pre>",print_r($post),"</pre>";die();
            update_load(20);
            $trans_charges = $this->charges_sales($sales['settled']['ids'],$curr,$_GET['branch_code'],$_GET['brand'],$conso_args);
            update_load(25);
            $trans_discounts = $this->discounts_sales($sales['settled']['ids'],$curr,$_GET['branch_code'],$_GET['brand'],$from,$to);
            update_load(30);
            $trans_local_tax = $this->local_tax_sales($sales['settled']['ids'],$curr,$_GET['branch_code'],$_GET['brand'],$conso_args);
            update_load(35);
            $trans_tax = $this->tax_sales($sales['settled']['ids'],$curr,$_GET['branch_code'],$_GET['brand'],$conso_args);
            update_load(40);
            $trans_no_tax = $this->no_tax_sales($sales['settled']['ids'],$curr,$_GET['branch_code'],$_GET['brand'],$conso_args);
            update_load(45);
            $trans_zero_rated = $this->zero_rated_sales($sales['settled']['ids'],$curr,$_GET['branch_code'],$_GET['brand'],$conso_args);
            update_load(50);
            $payments = $this->payment_sales($sales['settled']['ids'],$curr,$_GET['branch_code'],$_GET['brand'],$conso_args);
            update_load(53);
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
            update_load(55);
            $cats = $trans_menus['cats'];   
            $subcats = $trans_menus['sub_cats'];               
            $menus = $trans_menus['menus'];
            $menu_total = $trans_menus['menu_total'];
            $total_qty = $trans_menus['total_qty'];
            update_load(60);
            usort($menus, function($a, $b) {
                return $b['amount'] - $a['amount'];
            });
            // update_load(80);                 


        $pdf->Write(0, 'Menus Sales Report', '', 0, 'L', true, 0, false, false, 0);
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

        // echo "<pre>", print_r($menus), "</pre>";die();

        // -----------------------------------------------------------------------------
        $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => 'black'));
        $pdf->Cell(25, 0, 'Code', 'B', 0, 'L');        
        $pdf->Cell(40, 0, 'Name', 'B', 0, 'L');        
        $pdf->Cell(32, 0, 'Category', 'B', 0, 'L');        
        $pdf->Cell(25, 0, 'Price (SRP)', 'B', 0, 'R');        
        $pdf->Cell(25, 0, 'QTY', 'B', 0, 'R');        
        $pdf->Cell(25, 0, 'QTY AVG', 'B', 0, 'R');        
        $pdf->Cell(25, 0, 'Sales', 'B', 0, 'R');        
        $pdf->Cell(25, 0, 'Sales AVG', 'B', 0, 'R');        
        $pdf->Cell(25, 0, 'Cost', 'B', 0, 'R');        
        $pdf->Cell(25, 0, 'Total Cost', 'B', 0, 'R');        
        $pdf->ln();                  

                            
        foreach ($menus as $v) {
            $pdf->Cell(25, 0, $v['code'], '', 0, 'L');        
            $pdf->Cell(40, 0, $v['name'], '', 0, 'L');           
            $pdf->Cell(32, 0, $cats[$v['cat_id']]['name'], '', 0, 'L');        
            $pdf->Cell(25, 0, $v['sell_price'], '', 0, 'R');        
            $pdf->Cell(25, 0, $v['qty'], '', 0, 'R');        
            $pdf->Cell(25, 0, num(($v['qty'] / $total_qty) * 100 ).'%', '', 0, 'R');                    
            $pdf->Cell(25, 0, num($v['amount']), '', 0, 'R');        
            $pdf->Cell(25, 0, num(($v['amount'] / $menu_total) * 100 ).'%', '', 0, 'R');        
            $pdf->Cell(25, 0, num($v['cost_price']), '', 0, 'R');        
            $pdf->Cell(25, 0, num($v['cost_price'] * $v['qty']), '', 0, 'R');        
            $pdf->ln();                

            
        }
             $pdf->Cell(122, 0, 'Total', '', 0, 'R'); 
             $pdf->Cell(25, 0, $total_qty, '', 0, 'R'); 
             $pdf->Cell(25, 0, 'Total', '', 0, 'R'); 
             $pdf->Cell(25, 0, num($menu_total), '', 0, 'R'); 
             $pdf->ln(); 

            $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => 'black'));
            $pdf->Cell(25, 0, 'Category', 'B', 0, 'L');        
            $pdf->Cell(40, 0, 'QTY', 'B', 0, 'R');        
            $pdf->Cell(32, 0, 'Amount', 'B', 0, 'R');  
            $pdf->ln();
            foreach ($cats as $cat_id => $ca) {
                if($ca['amount'] > 0){
                    $pdf->Cell(25, 0, $ca['name'], '', 0, 'L');        
                    $pdf->Cell(40, 0, $ca['qty'], '', 0, 'R');  
                    $pdf->Cell(32, 0, $ca['amount'], '', 0, 'R');  
                    $pdf->ln();
                }
            }  
            $pdf->ln();
            $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => 'black'));
            $pdf->Cell(25, 0, 'Types', 'B', 0, 'L');        
            $pdf->Cell(40, 0, 'QTY', 'B', 0, 'R');        
            $pdf->Cell(32, 0, 'Amount', 'B', 0, 'R');  
            $pdf->ln();

            foreach ($subcats as $id => $val) {
                if($val['amount'] > 0){
                    $pdf->Cell(25, 0, $val['name'], '', 0, 'L');        
                    $pdf->Cell(40, 0, $val['qty'], '', 0, 'R');  
                    $pdf->Cell(32, 0, $val['amount'], '', 0, 'R');  
                    $pdf->ln();
                }
            }
            $pdf->ln();

            $mods_total = $trans_menus['mods_total'];
            if($mods_total > 0){
                $pdf->Cell(40, 0, 'Total Modifiers Sale', '', 0, 'L'); 
                $pdf->Cell(40, 0, num($mods_total), '', 0, 'R'); 
                $pdf->ln();
            }
            $submods_total = $trans_menus['submods_total'];
            if($submods_total > 0){
                $pdf->Cell(40, 0, 'Total Submodifiers Sale', '', 0, 'L'); 
                $pdf->Cell(40, 0, num($submods_total), '', 0, 'R'); 
                $pdf->ln();
            }
            $pdf->Cell(40, 0, 'Total Sale', '', 0, 'L'); 
            $pdf->Cell(40, 0, num($net), '', 0, 'R'); 
            $pdf->ln();
            $txt = numInt(($charges));
            if($charges > 0)
                $txt = "(".numInt(($charges)).")";
                $pdf->Cell(40, 0, 'Total Charges', '', 0, 'L'); 
                $pdf->Cell(40, 0, $txt, '', 0, 'R'); 
                $pdf->ln();
            $txt = numInt(($local_tax));
            if($local_tax > 0)
                $txt = "(".numInt(($local_tax)).")";
            $pdf->Cell(40, 0, 'Total Local Tax', '', 0, 'L'); 
            $pdf->Cell(40, 0, $txt, '', 0, 'R'); 
            $pdf->ln();
            $pdf->Cell(40, 0, 'Total Discounts', '', 0, 'L'); 
            $pdf->Cell(40, 0, num($discounts), '', 0, 'R'); 
            $pdf->ln();
            $pdf->Cell(40, 0, 'Total VAT EXEMPT', '', 0, 'L'); 
            $pdf->Cell(40, 0, num($less_vat), '', 0, 'R'); 
            $pdf->ln();
            $pdf->Cell(40, 0, 'Total Gross Sales', '', 0, 'L'); 
            $pdf->Cell(40, 0, num($gross), '', 0, 'R'); 
            $pdf->ln();



        update_load(100);  


        // -----------------------------------------------------------------------------

        //Close and output PDF document
        // ob_end_clean();
        $pdf->Output('menus_report.pdf', 'I');

        //============================================================+
        // END OF FILE
        //============================================================+   
    }

    //////hourly sales rep
    public function hourly_rep(){
        $data = $this->syter->spawn('hourly_rep');
        $data['page_title'] = fa('icon-doc')." Hourly Sales Report";
        $data['code'] = hourlyRep();
        $data['add_css'] = array('css/morris/morris.css','css/wowdash.css','css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css');
        $data['add_js'] = array('js/plugins/morris/morris.min.js','js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js');
        $data['page_no_padding'] = true;
        $data['sideBarHide'] = true;
        $data['load_js'] = 'dine/reporting';
        $data['use_js'] = 'hourlyRepJS';
        $this->load->view('page',$data);
    }

    public function check_hourly_sales_v1(){
        $this->load->helper('dine/reports_helper');

        $date = $this->input->post('calendar_range');
        $branch_id = $this->input->post('branch_id');
        $brand = $this->input->post('brand');
        // $user = $this->input->post('user');
        $json = $this->input->post('json');

        $datesx = explode(" to ",$date);
        // $date_from = (empty($dates[0]) ? date('Y-m-d') : date('Y-m-d',strtotime($dates[0])));
        // $date_to = (empty($dates[1]) ? date('Y-m-d') : date('Y-m-d',strtotime($dates[1])));
        $date_from = $datesx[0];
        $date_to = $datesx[1];


        // echo $date_from.' -- '.$date_to;

        $args = array();
        // if($user)
        //     $args["trans_sales.user_id"] = array('use'=>'where','val'=>$user,'third'=>false);

        $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.inactive = 0"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.datetime between '".date('Y-m-d H:i:s',strtotime($date_from))."' and '".date('Y-m-d H:i:s',strtotime($date_to))."'"] = array('use'=>'where','val'=>null,'third'=>false);
        // $args["trans_sales.branch_id"] = array('use'=>'where','val'=>$branch_id,'third'=>false);
        if($branch_id !=''){
           $args["trans_sales.branch_code= '".$branch_id."'"] = array('use'=>'where','val'=>null,'third'=>false);
        }

        if($brand !=''){
            $args["trans_sales.pos_id = ".$brand] = array('use'=>'where','val'=>null,'third'=>false);
        }

        $post = $this->set_post();
        $curr = $this->search_current();
        $trans = $this->trans_sales($args,$curr,$branch_id);
        $sales = $trans['sales'];

        // var_dump($sales); die();
        // $get_trans = $this->cashier_model->get_trans_sales(null,$args,'asc');
        // $unserialize = unserialize(TIMERANGES);
        // var_dump($unserialize); die();

        $ranges = array();
        foreach (unserialize(TIMERANGES) as $ctr => $time) {
            $key = date('H',strtotime($time['FTIME']));
            $ranges[$key] = array('start'=>$time['FTIME'],'end'=>$time['TTIME'],'tc'=>0,'net'=>0);
            // $ranges[$key] = array();
        }

        $dates = array();
        if(count($sales['settled']['orders2']) > 0){
            foreach ($sales['settled']['orders2'] as $sales_id => $val) {
                $dates[date2Sql($val->datetime)]['ranges'] = $ranges;
            }
            foreach ($sales['settled']['orders2'] as $sales_id => $val) {
            // echo "<pre>",print_r($val),"</pre>";
            // die();
                $branch[] = $val->branch_code;
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
            
            // $this->print_drawer_details($get_trans,$date,$user,$asjson);
            $this->make->sDiv();
                $this->make->sTable(array('class'=>'table'));
                    $this->make->sTableHead();
                        $this->make->sRow(array('class'=>'bg-blue'));
                            // $this->make->th('Branch');
                            $this->make->th('Time');
                            $this->make->th('Total Count');
                            $this->make->th('Net');
                            // $this->make->th('Trans Time');
                            $this->make->th('Average');
                            // $this->make->th('Category');
                            // $this->make->th('Price (SRP)');
                            // $this->make->th('QTY');
                            // $this->make->th('QTY AVG');
                            // $this->make->th('Sales');
                            // $this->make->th('Sales AVG');
                        $this->make->eRow();
                    $this->make->eTableHead();
                    $this->make->sTableBody();
                    $txt = '';
                    // foreach($dates as $key1 => $v1){
                    //     // $this->make->sRow();
                    //     //     $this->make->th(sql2Date($key1),array('colspan'=>4));
                    //     // $this->make->eRow();
                    //     //$txt .= sql2Date($key1);
                    //     foreach($v1 as $key2 => $v2){

                    //         foreach($v2 as $key3 => $v3){
                    //             $id = (int) $key3;
                    //             $times = $unserialize[$id];
                    //             $txt = $key1.' &nbsp;'.$times['FTIME'].' to '.$times['TTIME'];
                    //             $this->make->sRow();
                    //                 $this->make->th($txt,array('colspan'=>3));
                    //             $this->make->eRow();
                    //             foreach($v3 as $key4 => $v4){
                    //                 $this->make->sRow();
                    //                     $this->make->th($v4['time']);
                    //                     $this->make->th($v4['reference']);
                    //                     $this->make->th($v4['net']);
                    //                 $this->make->eRow();
                    //             }
                    //             // var_dump($times);
                    //             // $this->make->sRow();
                    //             //     $this->make->th($key3);
                    //             // $this->make->eRow();
                    //         }

                    //     }

                    // }
                    $ctr = 0;
                    $gtavg = $gtctr = $gtnet = 0;

                    foreach($dates as $key1 => $v1){
                        $this->make->sRow(array('class'=>''));
                            $this->make->th(sql2Date($key1),array('colspan'=>4,'style'=>'text-align:center;'));
                        $this->make->eRow();

                        $ranges = $v1['ranges'];
                        //$txt .= sql2Date($key1);
                        $tavg = 0;
                        $tctr = 0;
                        $tnet = 0;
                        foreach($ranges as $key2 => $ran){
                            // echo "<pre>",print_r($branch),"</pre>";
                            // die();
                            if($ran['tc'] == 0 || $ran['net'] == 0)
                                $avg = 0;
                            else
                                $avg = $ran['net']/$ran['tc'];
                            $ctr += $ran['tc'];

                            $this->make->sRow();
                                // $this->make->th($branch_th);   
                                $this->make->th($ran['start']."-".$ran['end']);
                                $this->make->th($ran['tc']);
                                $this->make->th(numInt($ran['net']));
                                $this->make->th(numInt($avg));
                            $this->make->eRow();
                            $tctr += $ran['tc'];
                            $tnet += $ran['net'];
                            // if($ctr == 0 || $ran['net'] == 0)
                            //     $tavg = 0;
                            // else
                            //     $tavg += $ran['net']/$ctr;

                        }
                        $gtctr += $tctr;
                        $gtnet += $tnet;

                    }
                    $gtavg = $gtnet/$gtctr;
                    $this->make->sRow();
                        $this->make->th('TOTAL');
                        $this->make->th($gtctr);
                        $this->make->th(numInt($gtnet));
                        $this->make->th(numInt($gtavg));
                    $this->make->eRow();
                    $this->make->eTableBody();
                $this->make->eTable();
            $this->make->eDiv();

            $code = $this->make->code();

            // echo $code;
            echo json_encode(array("code"=>$code));
            
        }
        else{
            $error = "There is no sales found.";
            echo json_encode(array("code"=>"<pre style='background-color:#fff'>$error</pre>"));
        }
        // var_dump($dates);
    }

    public function check_hourly_sales(){
        $this->load->helper('dine/reports_helper');

        $date = $this->input->post('calendar_range');
        $branch_id = $this->input->post('branch_id');
        $brand = $this->input->post('brand');
        // $user = $this->input->post('user');
        $json = $this->input->post('json');

        $daterange = $this->input->post("calendar_range");        
        $dates = explode(" to ",$daterange);
        $from = date2SqlDateTime($dates[0]);        
        $to = date2SqlDateTime($dates[1]);
        $asjson = false;
        // $args["trans_sales.type !="] = 'mgtfree';
        // $args["trans_sales.datetime  BETWEEN '".$from."' AND '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);
        // $args["trans_sales.branch_code  = '".$branch_code."' "] = array('use'=>'where','val'=>null,'third'=>false);
        // if($json == 'true'){
        //     $asjson = true;
        // }else{
        //     $asjson = false;
        // }
        // $details = $this->setup_model->get_branch_details($branch_code);
            
        // $open_time = $details[0]->store_open;
        // $close_time = $details[0]->store_close;

        $d = $from;
        $print_date =$from. "  - ".$to;
        $from = date2Sql($dates[0]);        
        $to = date2Sql($dates[1]);
        $args = array();
        $args["trans_date  >= '".$from."' AND trans_date <= '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);
        $args['branch_code'] = $branch_id;       

        // if($terminal_id != '' && is_int($terminal_id)){
        //     $args['pos_id'] = $terminal_id;
        // }

        $post = $this->set_post();
        $curr = $this->search_current();
        $zread = $this->cashier_model->get_store_zread(null,$args,false);

        // var_dump($sales); die();
        // $get_trans = $this->cashier_model->get_trans_sales(null,$args,'asc');
        // $unserialize = unserialize(TIMERANGES);
        // var_dump($unserialize); die();

        $ranges = array();
        foreach (unserialize(TIMERANGES) as $ctr => $time) {
            $key = date('H',strtotime($time['FTIME']));
            $ranges[$key] = array('start'=>$time['FTIME'],'end'=>$time['TTIME'],'tc'=>0,'net'=>0);
            // $ranges[$key] = array();
        }

        $dates = array();
        if(count($zread) > 0){
            
            
            // $this->print_drawer_details($get_trans,$date,$user,$asjson);
            $this->make->sDiv();
                $this->make->sTable(array('class'=>'table'));
                    $this->make->sTableHead();
                        $this->make->sRow(array('class'=>'bg-blue'));
                            // $this->make->th('Branch');
                            $this->make->th('Time');
                            $this->make->th('Total Count');
                            $this->make->th('Net');
                            // $this->make->th('Trans Time');
                            $this->make->th('Average');
                            // $this->make->th('Category');
                            // $this->make->th('Price (SRP)');
                            // $this->make->th('QTY');
                            // $this->make->th('QTY AVG');
                            // $this->make->th('Sales');
                            // $this->make->th('Sales AVG');
                        $this->make->eRow();
                    $this->make->eTableHead();
                    $this->make->sTableBody();
                    $txt = '';
                    // foreach($dates as $key1 => $v1){
                    //     // $this->make->sRow();
                    //     //     $this->make->th(sql2Date($key1),array('colspan'=>4));
                    //     // $this->make->eRow();
                    //     //$txt .= sql2Date($key1);
                    //     foreach($v1 as $key2 => $v2){

                    //         foreach($v2 as $key3 => $v3){
                    //             $id = (int) $key3;
                    //             $times = $unserialize[$id];
                    //             $txt = $key1.' &nbsp;'.$times['FTIME'].' to '.$times['TTIME'];
                    //             $this->make->sRow();
                    //                 $this->make->th($txt,array('colspan'=>3));
                    //             $this->make->eRow();
                    //             foreach($v3 as $key4 => $v4){
                    //                 $this->make->sRow();
                    //                     $this->make->th($v4['time']);
                    //                     $this->make->th($v4['reference']);
                    //                     $this->make->th($v4['net']);
                    //                 $this->make->eRow();
                    //             }
                    //             // var_dump($times);
                    //             // $this->make->sRow();
                    //             //     $this->make->th($key3);
                    //             // $this->make->eRow();
                    //         }

                    //     }

                    // }
                    $ctr = 0;
                    $gtavg = $gtctr = $gtnet = 0;

                    foreach($zread as $zr){
                        $dt = json_decode($zr->hourly_sales,true);

                        foreach($dt as $key1 => $v1){
                            $this->make->sRow(array('class'=>''));
                            $this->make->th(sql2Date($key1),array('colspan'=>4,'style'=>'text-align:center;'));
                            $this->make->eRow();

                            $ranges = $v1['ranges'];
                            //$txt .= sql2Date($key1);
                            $tavg = 0;
                            $tctr = 0;
                            $tnet = 0;
                            foreach($ranges as $key2 => $ran){
                                // echo "<pre>",print_r($branch),"</pre>";
                                // die();
                                if($ran['tc'] == 0 || $ran['net'] == 0)
                                    $avg = 0;
                                else
                                    $avg = $ran['net']/$ran['tc'];
                                $ctr += $ran['tc'];

                                $this->make->sRow();
                                    // $this->make->th($branch_th);   
                                    $this->make->th($ran['start']."-".$ran['end']);
                                    $this->make->th($ran['tc']);
                                    $this->make->th(numInt($ran['net']));
                                    $this->make->th(numInt($avg));
                                $this->make->eRow();
                                $tctr += $ran['tc'];
                                $tnet += $ran['net'];
                                // if($ctr == 0 || $ran['net'] == 0)
                                //     $tavg = 0;
                                // else
                                //     $tavg += $ran['net']/$ctr;

                            }
                            $gtctr += $tctr;
                            $gtnet += $tnet;                            
                        }
                        
                    }
                    // foreach($dates as $key1 => $v1){
                        

                    // }
                    $gtavg = $gtctr > 0 ? $gtnet/$gtctr : 0;
                    $this->make->sRow();
                        $this->make->th('TOTAL');
                        $this->make->th($gtctr);
                        $this->make->th(numInt($gtnet));
                        $this->make->th(numInt($gtavg));
                    $this->make->eRow();
                    $this->make->eTableBody();
                $this->make->eTable();
            $this->make->eDiv();

            $code = $this->make->code();

            // echo $code;
            echo json_encode(array("code"=>$code));
            
        }
        else{
            $error = "There is no sales found.";
            echo json_encode(array("code"=>"<pre style='background-color:#fff'>$error</pre>"));
        }
        // var_dump($dates);
    }

    public function check_hourly_sales_excel_v1(){
        $this->load->library('Excel');
        $sheet = $this->excel->getActiveSheet();
        $filename = 'Hourly Sales Report';
        $rc=1;
        #GET VALUES
        start_load(0);
            // $post = $this->set_post($_GET['calendar_range']);

        $branch_id = $_GET['branch_id'];
        $brand = $_GET['brand'];    
        $date = $_GET['calendar_range'];

        $datesx = explode(" to ",$date);
        // $date_from = (empty($dates[0]) ? date('Y-m-d') : date('Y-m-d',strtotime($dates[0])));
        // $date_to = (empty($dates[1]) ? date('Y-m-d') : date('Y-m-d',strtotime($dates[1])));
        $date_from = $datesx[0];
        $date_to = $datesx[1];

        update_load(10);
        sleep(1);

        $args = array();
        $brand_txt = '';

        // if($user)
        //     $args["trans_sales.user_id"] = array('use'=>'where','val'=>$user,'third'=>false);

        $args["trans_date  >= '".$from."' AND trans_date <= '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);
        

        if($branch_id !=''){
           $args['branch_code'] = $branch_id;
        }

        $post = $this->set_post($_GET['calendar_range']);
        $curr = true;
        $zread = $this->cashier_model->get_store_zread(null,$args,false);        

        // $get_trans = $this->cashier_model->get_trans_sales(null,$args,'asc');

        update_load(20);
        sleep(1);

        $ranges = array();
        foreach (unserialize(TIMERANGES) as $ctr => $time) {
            $key = date('H',strtotime($time['FTIME']));
            $ranges[$key] = array('start'=>$time['FTIME'],'end'=>$time['TTIME'],'tc'=>0,'net'=>0);
            // $ranges[$key] = array();
        }

        update_load(30);
        sleep(1);

        // echo "<pre>",print_r($ranges),"</pre>";
        // die();
        $dates = array();
        if(count($sales['settled']['orders2']) > 0){
            foreach ($sales['settled']['orders2'] as $sales_id => $val) {
                $dates[date2Sql($val->datetime)]['ranges'] = $ranges;
            }
            foreach ($sales['settled']['orders2'] as $sales_id => $val) {
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

        update_load(60);
        sleep(1);        


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
                'size' => 16,
            )
        );
        
        $headers = array('Time','Total Count','Net','Average');
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        // $sheet->getColumnDimension('E')->setWidth(20);
        // $sheet->getColumnDimension('F')->setWidth(20);
        // $sheet->getColumnDimension('G')->setWidth(20);


        $sheet->mergeCells('A'.$rc.':D'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Hourly Sales Report');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        $rc++;        

        if($branch_id != ''){
            $sheet->mergeCells('A'.$rc.':D'.$rc);
            $sheet->getCell('A'.$rc)->setValue('Branch:' . $branch_id. $brand_txt);
            $rc++;
        }

        $brd = $this->setup_model->get_brands($brand);
        if($brd){
            $sheet->getCell('A'.$rc)->setValue('Brand:' . $brd[0]->brand_name);
            $sheet->mergeCells('A'.$rc.':D'.$rc);
            $rc++;
        }        
        
        // $dates = explode(" to ",$_GET['date']);
        $from = sql2DateTime($date_from);
        $to = sql2DateTime($date_to);
        $sheet->getCell('A'.$rc)->setValue('Date From: '.$from);
        $sheet->mergeCells('A'.$rc.':D'.$rc);
        $rc++;

        $sheet->getCell('A'.$rc)->setValue('Date To: '.$to);
        $sheet->mergeCells('A'.$rc.':G'.$rc);
        $rc++;
        $col = 'A';
        foreach ($headers as $txt) {
            $sheet->getCell($col.$rc)->setValue($txt);
            $sheet->getStyle($col.$rc)->applyFromArray($styleHeaderCell);
            $col++;
        }

        $rc++;
        $ctr = 0;
        $gtavg = $gtctr = $gtnet = 0;
        update_load(80);
        sleep(1);
        foreach($dates as $key1 => $v1){
            $sheet->getCell('A'.$rc)->setValue(sql2Date($key1));
            $sheet->mergeCells('A'.$rc.':D'.$rc);
            $sheet->getStyle('A'.$rc)->applyFromArray($styleCenter);
            $rc++;

            $ranges = $v1['ranges'];
            //$txt .= sql2Date($key1);
            $tavg = 0;
            $tctr = 0;
            $tnet = 0;
            foreach($ranges as $key2 => $ran){
                if($ran['tc'] == 0 || $ran['net'] == 0)
                    $avg = 0;
                else
                    $avg = $ran['net']/$ran['tc'];
                $ctr += $ran['tc'];

                $sheet->getCell('A'.$rc)->setValue($ran['start']."-".$ran['end']);
                $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
                $sheet->getCell('B'.$rc)->setValue($ran['tc']);
                $sheet->getStyle('B'.$rc)->applyFromArray($styleCenter);
                $sheet->getCell('C'.$rc)->setValue(numInt($ran['net']));
                $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
                $sheet->getCell('D'.$rc)->setValue(numInt($avg));     
                $sheet->getStyle('D'.$rc)->applyFromArray($styleNum);

                // $this->make->sRow();
                //     $this->make->th($ran['start']."-".$ran['end']);
                //     $this->make->th($ran['tc']);
                //     $this->make->th(numInt($ran['net']));
                //     $this->make->th(numInt($avg));
                // $this->make->eRow();
                $tctr += $ran['tc'];
                $tnet += $ran['net'];
                // $tavg += $avg;
                $rc++;
                // if($ctr == 0 || $ran['net'] == 0)
                //     $tavg = 0;
                // else
                //     $tavg += $ran['net']/$ctr;

            }
            
            $gtctr += $tctr;
            $gtnet += $tnet;

        }
        $gtavg = $gtnet/$gtctr;
        $sheet->getCell('A'.$rc)->setValue('TOTAL');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('B'.$rc)->setValue($gtctr);
        $sheet->getStyle('B'.$rc)->applyFromArray($styleCenter);
        $sheet->getCell('C'.$rc)->setValue(numInt($gtnet));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        $sheet->getCell('D'.$rc)->setValue(numInt($gtavg));     
        $sheet->getStyle('D'.$rc)->applyFromArray($styleNum);
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

        //     $rc++;
        // } 
        // $rc++;

        
        update_load(100);

        if (ob_get_contents())
        ob_end_clean();
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $objWriter->save('php://output');
    }

    public function check_hourly_sales_excel(){
        $this->load->library('Excel');
        $sheet = $this->excel->getActiveSheet();
        $filename = 'Hourly Sales Report';
        $rc=1;
        #GET VALUES
        start_load(0);
            // $post = $this->set_post($_GET['calendar_range']);

        $details = $this->setup_model->get_branch_details();
         
        $open_time = $details[0]->store_open;
        $close_time = $details[0]->store_close;

        $branch_id = $_GET['branch_id'];
        $brand = $_GET['brand'];    
        $date = $_GET['calendar_range'];

        $datesx = explode(" to ",$date);
        $date_from = (empty($datesx[0]) ? date('Y-m-d') : date('Y-m-d',strtotime($datesx[0])));
        $date_to = (empty($datesx[1]) ? date('Y-m-d') : date('Y-m-d',strtotime($datesx[1])));
        // $date_from = $datesx[0];
        // $date_to = $datesx[1];

        update_load(10);
        sleep(1);

        $args = array();
        $brand_txt = '';

        // if($user)
        //     $args["trans_sales.user_id"] = array('use'=>'where','val'=>$user,'third'=>false);

        $args["trans_date  >= '".$date_from."' AND trans_date <= '".$date_to."'"] = array('use'=>'where','val'=>null,'third'=>false);
        

        if($branch_id !=''){
           $args['branch_code'] = $branch_id;
        }

        $post = $this->set_post($_GET['calendar_range']);
        $curr = true;
        $zread = $this->cashier_model->get_store_zread(null,$args,false);        
        // echo $this->db->last_query();exit;
        // $get_trans = $this->cashier_model->get_trans_sales(null,$args,'asc');

        update_load(20);
        sleep(1);

        $ranges = array();
        foreach (unserialize(TIMERANGES) as $ctr => $time) {
            $key = date('H',strtotime($time['FTIME']));
            $ranges[$key] = array('start'=>$time['FTIME'],'end'=>$time['TTIME'],'tc'=>0,'net'=>0);
            // $ranges[$key] = array();
        }

        update_load(30);
        sleep(1);

        // echo "<pre>",print_r($ranges),"</pre>";
        // die();

        update_load(60);
        sleep(1);        


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
                'size' => 16,
            )
        );
        
        $headers = array('Time','Total Count','Net','Average');
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        // $sheet->getColumnDimension('E')->setWidth(20);
        // $sheet->getColumnDimension('F')->setWidth(20);
        // $sheet->getColumnDimension('G')->setWidth(20);


        $sheet->mergeCells('A'.$rc.':D'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Hourly Sales Report');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        $rc++;        

        if($branch_id != ''){
            $sheet->mergeCells('A'.$rc.':D'.$rc);
            $sheet->getCell('A'.$rc)->setValue('Branch:' . $branch_id. $brand_txt);
            $rc++;
        }

        $brd = $this->setup_model->get_brands($brand);
        if($brd){
            $sheet->getCell('A'.$rc)->setValue('Brand:' . $brd[0]->brand_name);
            $sheet->mergeCells('A'.$rc.':D'.$rc);
            $rc++;
        }        
        
        // $dates = explode(" to ",$_GET['date']);
        $from = sql2Date($date_from);
        $to = sql2Date($date_to);
        $sheet->getCell('A'.$rc)->setValue('Date From: '.$from . ' '. $open_time);
        $sheet->mergeCells('A'.$rc.':D'.$rc);
        $rc++;

        if($close_time < $open_time){
            $to = sql2Date(date('Y-m-d', strtotime($to . ' +1 day')));
        }

        $sheet->getCell('A'.$rc)->setValue('Date To: '.$to . ' '. $close_time);
        $sheet->mergeCells('A'.$rc.':G'.$rc);
        $rc++;
        $col = 'A';
        foreach ($headers as $txt) {
            $sheet->getCell($col.$rc)->setValue($txt);
            $sheet->getStyle($col.$rc)->applyFromArray($styleHeaderCell);
            $col++;
        }

        $rc++;
        $ctr = 0;
        $gtavg = $gtctr = $gtnet = 0;
        update_load(80);
        sleep(1);

        if($zread){
            foreach ($zread as $zr) {
                $dt = json_decode($zr->hourly_sales,true);

                foreach($dt as $key1 => $v1){
                    $sheet->getCell('A'.$rc)->setValue(sql2Date($key1));
                    $sheet->mergeCells('A'.$rc.':D'.$rc);
                    $sheet->getStyle('A'.$rc)->applyFromArray($styleCenter);
                    $rc++;

                    $ranges = $v1['ranges'];
                    //$txt .= sql2Date($key1);
                    $tavg = 0;
                    $tctr = 0;
                    $tnet = 0;
                    foreach($ranges as $key2 => $ran){
                        if($ran['tc'] == 0 || $ran['net'] == 0)
                            $avg = 0;
                        else
                            $avg = $ran['net']/$ran['tc'];
                        $ctr += $ran['tc'];

                        $sheet->getCell('A'.$rc)->setValue($ran['start']."-".$ran['end']);
                        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
                        $sheet->getCell('B'.$rc)->setValue($ran['tc']);
                        $sheet->getStyle('B'.$rc)->applyFromArray($styleCenter);
                        $sheet->getCell('C'.$rc)->setValue(numInt($ran['net']));
                        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
                        $sheet->getCell('D'.$rc)->setValue(numInt($avg));     
                        $sheet->getStyle('D'.$rc)->applyFromArray($styleNum);

                        // $this->make->sRow();
                        //     $this->make->th($ran['start']."-".$ran['end']);
                        //     $this->make->th($ran['tc']);
                        //     $this->make->th(numInt($ran['net']));
                        //     $this->make->th(numInt($avg));
                        // $this->make->eRow();
                        $tctr += $ran['tc'];
                        $tnet += $ran['net'];
                        // $tavg += $avg;
                        $rc++;
                        // if($ctr == 0 || $ran['net'] == 0)
                        //     $tavg = 0;
                        // else
                        //     $tavg += $ran['net']/$ctr;

                    }
                    
                    $gtctr += $tctr;
                    $gtnet += $tnet;

                    $gtavg = $gtctr > 0 ? $gtnet/$gtctr : 0;
                    $sheet->getCell('A'.$rc)->setValue('TOTAL');
                    $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
                    $sheet->getCell('B'.$rc)->setValue($gtctr);
                    $sheet->getStyle('B'.$rc)->applyFromArray($styleCenter);
                    $sheet->getCell('C'.$rc)->setValue(numInt($gtnet));
                    $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
                    $sheet->getCell('D'.$rc)->setValue(numInt($gtavg));     
                    $sheet->getStyle('D'.$rc)->applyFromArray($styleNum);
                }
            }
        }

        
        update_load(100);

        if (ob_get_contents())
        ob_end_clean();
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $objWriter->save('php://output');
    }
    
        // New Report
    // Created by: Rod
    public function sales_rep()
    {
        $data = $this->syter->spawn('sales_rep');        
        $data['page_title'] = fa('fa-money')." Sales Report";
        $data['code'] = salesRep();
        $data['add_css'] = array('css/morris/morris.css','css/wowdash.css','css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css','assets/global/plugins/select2/css/select2.min.css','assets/global/plugins/select2/css/select2-bootstrap.min.css');
        $data['add_js'] = array('js/plugins/morris/morris.min.js','js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js','assets/global/plugins/select2/js/select2.full.min.js','assets/pages/scripts/components-select2.min.js');
        $data['page_no_padding'] = false;
        $data['sideBarHide'] = false;
        $data['load_js'] = 'dine/reporting';
        $data['use_js'] = 'salesRepJS';
        $this->load->view('page',$data);
    }
    public function sales_rep_gen()
    {
        $this->load->model('dine/setup_model');
        // $this->load->database('main', TRUE);
        $this->load->model("dine/menu_model");
        $menu_cat_id = null;
        $branch_id = $this->input->post('branch_id');
        $menu_cat_raw = $this->input->post("menu_cat_id");
        // $brand = $this->input->post("brand");
        $brand = "";
        if($menu_cat_raw !=""){
            $raw_data = explode("--",$menu_cat_raw);
            $menu_cat_id = $raw_data[0];
            $branch_id = $raw_data[1];
        }
        $setup = $this->setup_model->get_details($branch_id);
        $set = $setup[0];
        start_load(0);
        // echo $branch_id;die();
        $daterange = $this->input->post("calendar_range");        
        // $daterange = "05/30/2019 to 05/30/2019";        
        $dates = explode(" to ",$daterange);
        // $from = date2SqlDateTime($dates[0]);
        // $to = date2SqlDateTime($dates[1]);
        // $date = $this->input->post("date");
        // echo "<pre>",print_r($raw_data),"</pre>";die();
        $this->menu_model->db = $this->load->database('default', TRUE);
        // $from = date2SqlDateTime($dates[0]);        
        $from = date2SqlDateTime($dates[0]);        
        // $to = date2SqlDateTime($dates[1]);
        $to = date2SqlDateTime($dates[1]);
    
        // echo "asdf";die();
        $trans = $this->menu_model->get_cat_sales_rep($from, $to, $menu_cat_id, $branch_id,$brand);  
        // echo $this->menu_model->db->last_query(); die();           
        $trans_ret = array();
        // $trans_ret = $this->menu_model->get_cat_sales_rep_retail($from, $to, $menu_cat_id, $branch_id,$brand);
        // $trans_mod = $this->menu_model->get_menu_modifer_sales_rep($from, $to, $menu_cat_id,$branch_id,$brand); 
        // echo "<pre>", print_r($trans_mod), "</pre>"; die();
        $trans_count = count($trans);
        // $trans_count_ret = count($trans_ret);
        $counter = 0;
        $this->make->sDiv(array('class'=>'card'));
            $this->make->sDiv(array('class'=>'"card-body'));
                $this->make->sTable(array("id"=>"main-tbl", 'class'=>'table striped-table mb-0'));
                    $this->make->sTableHead();
                        $this->make->sRow(array('class'=>'table-header'));
                            if($branch_id == ''){
                                $this->make->th('Branch', array('class'=>'th'));
                            }
                            $this->make->th('Category', array('class'=>'th'));
                            $this->make->th('Qty', array('class'=>'th'));
                            // $this->make->th('VAT Sales');
                            // $this->make->th('VAT');
                            $this->make->th('Gross', array('class'=>'th'));
                            $this->make->th('Sales (%)', array('class'=>'th'));
                            $this->make->th('Cost', array('class'=>'th'));
                            $this->make->th('Cost (%)', array('class'=>'th'));
                            $this->make->th('Margin', array('class'=>'th'));
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
                        $tot_cost_prcnt = 0;
                        foreach ($trans as $res) {
                            if($counter % 500 == 0){
                                set_time_limit(60);  
                            }

                            $tot_gross += $res->gross;
                            $tot_cost += $res->cost;

                            $this->make->sRow(array("class"=>"table-data-row"));
                                if($branch_id == ''){
                                    $this->make->td($res->branch_namee,array("class"=>"table-data-name"));
                                }
                                $this->make->td($res->menu_cat_namee,array("class"=>"table-data"));
                                $this->make->td(num($res->qty), array("style"=>"text-align:right","class"=>"table-data"));                           
                                $this->make->td(num($res->gross), array("style"=>"text-align:right","class"=>"table-data"));                            
                                if($tot_gross > 0){
                                    $this->make->td(num($res->gross / $tot_gross * 100). "%", array("style"=>"text-align:right","class"=>"table-data"));
                                }else{
                                    $this->make->td(num(100). "%", array("style"=>"text-align:right","class"=>"table-data"));
                                }
                                $this->make->td(num($res->cost), array("style"=>"text-align:right","class"=>"table-data"));                            
                                if($tot_cost != 0){
                                    $this->make->td(num($res->cost / $tot_cost * 100). "%", array("style"=>"text-align:right","class"=>"table-data"));
                                }else{
                                    $this->make->td("0.00%", array("style"=>"text-align:right","class"=>"table-data"));                                
                                }
                                $this->make->td(num($res->gross - $res->cost), array("style"=>"text-align:right","class"=>"table-data"));                         
                            $this->make->eRow();

                            // Grand Total
                            $tot_qty += $res->qty;
                            $tot_vat_sales += $res->vat_sales;
                            $tot_vat += $res->vat;
                            $tot_sales_prcnt = 0;
                            $tot_margin += $res->gross - $res->cost;
                            $tot_cost_prcnt = 0;

                            $counter++;
                            $progress = ($counter / $trans_count) * 100;
                            update_load(num($progress));

                        }    
                        $this->make->sRow(array("class"=>"table-data-grand-total "));
                            $this->make->th('Grand Total', array("style"=>"text-align:center","class"=>"td"));
                            if($branch_id == ''){
                                $this->make->th('  ');
                            }
                            $this->make->th('<span class="bg-success-focus text-success-main px-32 py-4 rounded-pill fw-medium ">'.num($tot_qty).'</span>', array("style"=>"text-align:center","class"=>"td "));
                            $this->make->th('<span class="bg-success-focus text-success-main px-32 py-4 rounded-pill fw-medium ">'.num($tot_gross).'</span>', array("style"=>"text-align:center","class"=>"td"));
                            $this->make->th("", array("style"=>"text-align:center","class"=>""));
                            $this->make->th('<span class="bg-success-focus text-success-main px-32 py-4 rounded-pill fw-medium ">'.num($tot_cost).'</span>', array("style"=>"text-align:center","class"=>"td"));
                            $this->make->th("", array("style"=>"text-align:center","class"=>""));                                                
                            $this->make->th('<span class="bg-success-focus text-success-main px-32 py-4 rounded-pill fw-medium ">'.num($tot_margin).'</span>', array("style"=>"text-align:center","class"=>"td"));                     
                        $this->make->eRow();                                 
                    $this->make->eTableBody();
                $this->make->eTable();
            $this->make->eDiv();
        $this->make->eDiv();

        // $this->make->append('<center><h4>Menu Modifiers</h4></center>');
        // $this->make->sDiv();
        //     $this->make->sTable(array("id"=>"main-tbl", 'class'=>'table reportTBL sortable'));
        //         $this->make->sTableHead();
        //             $this->make->sRow();
        //                 if($branch_id == ''){
        //                     $this->make->th('Branch');
        //                 }
        //                 $this->make->th('Category');
        //                 $this->make->th('Qty');
        //                 $this->make->th('Gross');
        //                 $this->make->th('Sales (%)');
        //                 $this->make->th('Cost');
        //                 $this->make->th('Cost (%)');
        //                 $this->make->th('Margin');
        //             $this->make->eRow();
        //         $this->make->eTableHead();
        //         $this->make->sTableBody();
        //             $tot_qty = 0;
        //             $tot_vat_sales = 0;
        //             $tot_vat = 0;
        //             $tot_gross = 0;
        //             $tot_sales_prcnt = 0;
        //             $tot_cost = 0;
        //             $tot_margin = 0;
        //             $tot_cost_prcnt = 0;
        //             foreach ($trans_mod as $res) {
        //                  if($counter % 500 == 0){
        //                     set_time_limit(60);  
        //                 }
        //                 // echo 'das';
        //                 $tot_gross += $res->gross;

        //                 $this->make->sRow();
        //                     if($branch_id == ''){
        //                         $this->make->td($res->branch_code);
        //                     }
        //                     $this->make->td($res->modifier_name);
        //                     $this->make->td(num($res->qty), array("style"=>"text-align:right"));                           
        //                     $this->make->td(num($res->gross), array("style"=>"text-align:right"));                            
        //                     $this->make->td(num($res->gross / $tot_gross * 100). "%", array("style"=>"text-align:right"));
        //                     $this->make->td(num(0), array("style"=>"text-align:right"));                            
        //                     if($tot_cost != 0){
        //                         $this->make->td(num($res->cost / $tot_cost * 100). "%", array("style"=>"text-align:right"));
        //                     }else{
        //                         $this->make->td("0.00%", array("style"=>"text-align:right"));                                
        //                     }
        //                     $this->make->td(num($res->gross - 0), array("style"=>"text-align:right"));                       
        //                 $this->make->eRow();

        //                  // Grand Total
        //                 $tot_qty += $res->qty;
        //                 $tot_vat_sales += $res->vat_sales;
        //                 $tot_vat += $res->vat;
        //                 $tot_sales_prcnt = 0;
        //                 $tot_margin += $res->gross - 0;
        //                 $tot_cost_prcnt = 0;

        //                 $counter++;
        //                 $progress = ($counter / $trans_count) * 100;
        //                 update_load(num($progress));

        //             }    
        //             $this->make->sRow();
        //                 $this->make->th('Grand Total');
        //                 if($branch_id == ''){
        //                     $this->make->th('  ');
        //                 }
        //                 $this->make->th(num($tot_qty), array("style"=>"text-align:right"));
        //                 // $this->make->th(num($tot_vat_sales), array("style"=>"text-align:right"));
        //                 // $this->make->th(num($tot_vat), array("style"=>"text-align:right"));
        //                 $this->make->th(num($tot_gross), array("style"=>"text-align:right"));
        //                 $this->make->th("", array("style"=>"text-align:right"));
        //                 $this->make->th(num($tot_cost), array("style"=>"text-align:right"));
        //                 $this->make->th("", array("style"=>"text-align:right"));                                                
        //                 $this->make->th(num($tot_margin), array("style"=>"text-align:right"));                     
        //             $this->make->eRow();                                 
        //         $this->make->eTableBody();
        //     $this->make->eTable();
        // $this->make->eDiv();

        // $this->make->append('<center><h4>Retail Items</h4></center>');
        // $this->make->sDiv();
        //     $this->make->sTable(array("id"=>"main-tbl", 'class'=>'table reportTBL sortable'));
        //         $this->make->sTableHead();
        //             $this->make->sRow();
        //                 if($branch_id == ''){
        //                     $this->make->th('Branch');
        //                 }
        //                 $this->make->th('Category');
        //                 $this->make->th('Qty');
        //                 // $this->make->th('VAT Sales');
        //                 // $this->make->th('VAT');
        //                 $this->make->th('Gross');
        //                 $this->make->th('Sales (%)');
        //                 $this->make->th('Cost');
        //                 $this->make->th('Cost (%)');
        //                 $this->make->th('Margin');
        //             $this->make->eRow();
        //         $this->make->eTableHead();
        //         $this->make->sTableBody();
        //             $tot_qty = 0;
        //             $tot_vat_sales = 0;
        //             $tot_vat = 0;
        //             $tot_gross = 0;
        //             $tot_sales_prcnt = 0;
        //             $tot_cost = 0;
        //             $tot_margin = 0;
        //             $tot_cost_prcnt = 0;
        //             // foreach ($trans_ret as $v) {
        //             //     $tot_gross += $v->gross;
        //                 // $tot_cost += $v->cost;
        //             // }
        //             foreach ($trans_ret as $res) {
        //                  if($counter % 500 == 0){
        //                     set_time_limit(60);  
        //                 }

        //                 $tot_gross += $res->gross;

        //                 $this->make->sRow();
        //                     if($branch_id == ''){
        //                         $this->make->td($res->branch_code);
        //                     }
        //                     $this->make->td($res->name);
        //                     $this->make->td(num($res->qty), array("style"=>"text-align:right"));                            
        //                     // $this->make->td(num($res->vat_sales), array("style"=>"text-align:right"));                            
        //                     // $this->make->td(num($res->vat), array("style"=>"text-align:right"));                            
        //                     $this->make->td(num($res->gross), array("style"=>"text-align:right"));                            
        //                     $this->make->td(num($res->gross / $tot_gross * 100). "%", array("style"=>"text-align:right"));                            
        //                     // $this->make->td(num($res->cost), array("style"=>"text-align:right"));                            
        //                     $this->make->td(num(0), array("style"=>"text-align:right"));                            
        //                     if($tot_cost != 0){
        //                         $this->make->td(num($res->cost / $tot_cost * 100). "%", array("style"=>"text-align:right"));
        //                     }else{
        //                         $this->make->td("0.00%", array("style"=>"text-align:right"));                                
        //                     }
        //                     $this->make->td(num($res->gross - 0), array("style"=>"text-align:right"));                         
        //                     // $this->make->td(num($res->gross - $res->cost), array("style"=>"text-align:right"));                         
        //                 $this->make->eRow();

        //                  // Grand Total
        //                 $tot_qty += $res->qty;
        //                 $tot_vat_sales += $res->vat_sales;
        //                 $tot_vat += $res->vat;
        //                 // $tot_gross += $res->gross;
        //                 $tot_sales_prcnt = 0;
        //                 // $tot_cost += $res->cost;
        //                 // $tot_margin += $res->gross - $res->cost;
        //                 $tot_margin += $res->gross - 0;
        //                 $tot_cost_prcnt = 0;

        //                 $counter++;
        //                 $progress = ($counter / $trans_count) * 100;
        //                 update_load(num($progress));

        //             }    
        //             $this->make->sRow();
        //                 $this->make->th('Grand Total');
        //                 if($branch_id == ''){
        //                     $this->make->th('  ');
        //                 }
        //                 $this->make->th(num($tot_qty), array("style"=>"text-align:right"));
        //                 // $this->make->th(num($tot_vat_sales), array("style"=>"text-align:right"));
        //                 // $this->make->th(num($tot_vat), array("style"=>"text-align:right"));
        //                 $this->make->th(num($tot_gross), array("style"=>"text-align:right"));
        //                 $this->make->th("", array("style"=>"text-align:right"));
        //                 $this->make->th(num($tot_cost), array("style"=>"text-align:right"));
        //                 $this->make->th("", array("style"=>"text-align:right"));                                                
        //                 $this->make->th(num($tot_margin), array("style"=>"text-align:right"));                     
        //             $this->make->eRow();                                 
        //         $this->make->eTableBody();
        //     $this->make->eTable();
        // $this->make->eDiv();

        header_remove('Set-Cookie');
        update_load(100);
        $code = $this->make->code();
        $json['code'] = $code;        
        $json['tbl_vals'] = $trans;
        $json['dates'] = $this->input->post('calendar_range');
        echo json_encode($json);
    }
    public function sales_rep_pdf()
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
        $pdf->SetTitle('Sales Report');
        $pdf->SetSubject('');
        $pdf->SetKeywords('');

        // set default header data
        $branch_id = $_GET['branch_id'];
        $brand = "";
        // $brand = $_GET['brand'];
        $setup = $this->setup_model->get_details($branch_id);
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
        $this->menu_model->db = $this->load->database('default', TRUE);
        $this->load->model("dine/menu_model");
        start_load(0);

        // set font
        $pdf->SetFont('helvetica', 'B', 11);

        // add a page
        $pdf->AddPage();
        
        $menu_cat_id = $_GET['menu_cat_id'];        
        $daterange = $_GET['calendar_range'];        
        $dates = explode(" to ",$daterange);
        // $from = date2SqlDateTime($dates[0]);
        // $to = date2SqlDateTime($dates[1]);
        $from = date2SqlDateTime($dates[0]);        
        $to = date2SqlDateTime($dates[1]);
       
        // echo $branch_id;die();   
        $trans = $this->menu_model->get_cat_sales_rep($from, $to, $menu_cat_id, $branch_id,$brand);
        // $trans_mod = $this->menu_model->get_menu_modifer_sales_rep($from, $to, $menu_cat_id,$branch_id,$brand); 
        
        // echo $this->db->last_query();die();                  

        $brd = $this->setup_model->get_brands($brand);

        if($brd){
            $pdf->Write(0, $brd[0]->brand_name, '', 0, 'L', true, 0, false, false, 0);
            $pdf->ln(0.9);
        }

        $pdf->Write(0, 'Sales Report', '', 0, 'L', true, 0, false, false, 0);
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

        // echo "<pre>", print_r($trans), "</pre>";die();

        // -----------------------------------------------------------------------------
        $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => 'black'));
        if($branch_id == ''){
            $pdf->cell(23, 0, 'Branch', 'B', 0, 'L');
            $pdf->Cell(20, 0, 'Category', 'B', 0, 'L');        
        }
        else{
            $pdf->Cell(43, 0, 'Category', 'B', 0, 'L');
        }
        $pdf->Cell(32, 0, 'Qty', 'B', 0, 'R');        
        // $pdf->Cell(32, 0, 'VAT Sales', 'B', 0, 'C');        
        // $pdf->Cell(32, 0, 'VAT', 'B', 0, 'C');        
        $pdf->Cell(32, 0, 'Gross', 'B', 0, 'R');        
        $pdf->Cell(32, 0, 'Sales (%)', 'B', 0, 'R');        
        $pdf->Cell(32, 0, 'Cost', 'B', 0, 'R');        
        $pdf->Cell(32, 0, 'Cost (%)', 'B', 0, 'R');        
        $pdf->Cell(64, 0, 'Margin', 'B', 0, 'R');        
        $pdf->ln();                  

        // GRAND TOTAL VARIABLES
        $tot_qty = 0;
        $tot_vat_sales = 0;
        $tot_vat = 0;
        $tot_gross = 0;
        $tot_sales_prcnt = 0;
        $tot_cost = 0;
        $tot_cost_prcnt = 0; 
        $tot_margin = 0;
        $counter = 0;
        $progress = 0;
        $trans_count = count($trans);
        // echo print_r($trans);die();
        foreach ($trans as $val) {
            $tot_gross += $val->gross;
            $tot_cost += $val->cost;
        }
        foreach ($trans as $k => $v) {
            if($branch_id == ""){
             $pdf->cell(23,0,$v->branch_name,0,0,'L',0, '', 1); //auto scaling cell
                $pdf->Cell(20, 0, $v->menu_cat_name, '', 0, 'L');        
            }
            else{
                $pdf->Cell(43, 0, $v->menu_cat_name, '', 0, 'L');
            }
            $pdf->Cell(32, 0, num($v->qty), '', 0, 'R');        
            // $pdf->Cell(32, 0, num($v->vat_sales), '', 0, 'R');        
            // $pdf->Cell(32, 0, num($v->vat), '', 0, 'R');        
            $pdf->Cell(32, 0, num($v->gross), '', 0, 'R');        
            $pdf->Cell(32, 0, num($v->gross / $tot_gross * 100)."%", '', 0, 'R');        
            $pdf->Cell(32, 0, num($v->cost), '', 0, 'R');                    
            if($tot_cost != 0){
                $pdf->Cell(32, 0, num($v->cost / $tot_cost * 100)."%", '', 0, 'R');                        
            }else{
                $pdf->Cell(32, 0, "0.00%", '', 0, 'R');                                        
            }
            $pdf->Cell(64, 0, num($v->gross - $v->cost), '', 0, 'R');        
            $pdf->ln();                

            // Grand Total
            $tot_qty += $v->qty;
            // $tot_vat_sales += $v->vat_sales;
            // $tot_vat += $v->vat;
            // $tot_gross += $v->gross;
            $tot_sales_prcnt = 0;
            // $tot_cost += $v->cost;
            $tot_margin += $v->gross - $v->cost;
            $tot_cost_prcnt = 0;

            $counter++;
            $progress = ($counter / $trans_count) * 100;
            update_load(num($progress));              
        }
        update_load(100);        
        $pdf->Cell(43, 0, "Grand Total", 'T', 0, 'L');        
        $pdf->Cell(32, 0, num($tot_qty), 'T', 0, 'R');        
        // $pdf->Cell(32, 0, num($tot_vat_sales), 'T', 0, 'R');        
        // $pdf->Cell(32, 0, num($tot_vat), 'T', 0, 'R');        
        $pdf->Cell(32, 0, num($tot_gross), 'T', 0, 'R');        
        $pdf->Cell(32, 0, "", 'T', 0, 'R');        
        $pdf->Cell(32, 0, num($tot_cost), 'T', 0, 'R');        
        $pdf->Cell(32, 0, "", 'T', 0, 'R'); 
        $pdf->Cell(64, 0, num($tot_margin), 'T', 0, 'R'); 
        $pdf->ln();    

        // hide muna ayaw mag generate
        // // trans menu modifier
        // $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => 'black'));
        // if($branch_id == ''){
        //     $pdf->cell(23, 0, 'Branch', 'B', 0, 'L');
        //     $pdf->Cell(20, 0, 'Modifier', 'B', 0, 'L');        
        // }
        // else{
        //     $pdf->Cell(43, 0, 'Modifier', 'B', 0, 'L');
        // }
        // $pdf->Cell(32, 0, 'Qty', 'B', 0, 'R');        
        // // $pdf->Cell(32, 0, 'VAT Sales', 'B', 0, 'C');        
        // // $pdf->Cell(32, 0, 'VAT', 'B', 0, 'C');        
        // $pdf->Cell(32, 0, 'Gross', 'B', 0, 'R');        
        // $pdf->Cell(32, 0, 'Sales (%)', 'B', 0, 'R');        
        // $pdf->Cell(32, 0, 'Cost', 'B', 0, 'R');        
        // $pdf->Cell(32, 0, 'Cost (%)', 'B', 0, 'R');        
        // $pdf->Cell(64, 0, 'Margin', 'B', 0, 'R');        
        // $pdf->ln();                  

        // // GRAND TOTAL VARIABLES
        // $tot_qty_mod = 0;
        // $tot_gross_mod = 0;
        // $tot_cost_mod = 0;
        // $tot_margin_mod = 0;
        // $counter = 0;
        // $progress = 0;
        // $trans_count = count($trans_mod);
        // // echo print_r($trans);die();
        // foreach ($trans_mod as $val) {
        //     $tot_gross_mod += $val->gross;
        //     $tot_cost_mod += $val->cost;
        // }
        // foreach ($trans_mod as $k => $v2) {
        //     if($branch_id == ""){
        //      $pdf->cell(23,0,$v2->branch_code,0,0,'L',0, '', 1); //auto scaling cell
        //         $pdf->Cell(20, 0, $v2->modifier_name, '', 0, 'L');        
        //     }
        //     else{
        //         $pdf->Cell(43, 0, $v2->modifier_name, '', 0, 'L');
        //     }
        //     $pdf->Cell(32, 0, num($v2->qty), '', 0, 'R');        
        //     $pdf->Cell(32, 0, num($v2->gross), '', 0, 'R');        
        //     $pdf->Cell(32, 0, num($v2->gross / $tot_gross_mod * 100)."%", '', 0, 'R');        
        //     $pdf->Cell(32, 0, num($v2->cost), '', 0, 'R');                    
        //     if($tot_cost_mod != 0){
        //         $pdf->Cell(32, 0, num($v2->cost / $tot_cost_mod * 100)."%", '', 0, 'R');                        
        //     }else{
        //         $pdf->Cell(32, 0, "0.00%", '', 0, 'R');                                        
        //     }
        //     $pdf->Cell(64, 0, num($v2->gross - 0), '', 0, 'R');        
        //     $pdf->ln();                

        //     // Grand Total
        //     $tot_qty_mod += $v2->qty;
        //     $tot_margin_mod += $v2->gross - 0;

        //     $counter++;
        //     $progress = ($counter / $trans_count) * 100;
        //     update_load(num($progress));              
        // }
        // update_load(100);        
        // $pdf->Cell(43, 0, "Grand Total", 'T', 0, 'L');        
        // $pdf->Cell(32, 0, num($tot_qty_mod), 'T', 0, 'R');      
        // $pdf->Cell(32, 0, num($tot_gross_mod), 'T', 0, 'R');        
        // $pdf->Cell(32, 0, "", 'T', 0, 'R');        
        // $pdf->Cell(32, 0, num($tot_cost_mod), 'T', 0, 'R');        
        // $pdf->Cell(32, 0, "", 'T', 0, 'R'); 
        // $pdf->Cell(64, 0, num($tot_margin_mod), 'T', 0, 'R'); 



        ///////////fpr payments
        // $this->cashier_model->db = $this->load->database('default', TRUE);
        // $args = array();
        // // if($user)
        // //     $args["trans_sales.user_id"] = array('use'=>'where','val'=>$user,'third'=>false);

        // $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
        // $args["trans_sales.inactive = 0"] = array('use'=>'where','val'=>null,'third'=>false);
        // $args["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);
        // if($branch_id !=''){
        //     $args += array('trans_sales.branch_code'=>$branch_id);
        // }

        // if($brand !=''){
        //     $args += array('trans_sales.pos_id'=>$brand);
        // }
        // // $args["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);

        // // $terminal = TERMINAL_ID;
        // // $args['trans_sales.terminal_id'] = $terminal;
        // // if($menu_cat_id != 0){
        // //     $args["menu_categories.menu_cat_id"] = array('use'=>'where','val'=>$menu_cat_id,'third'=>false);
        // // }


        // // $post = $this->set_post();
        // // $curr = $this->search_current();

        // $conso_args = array();
        // $conso_args["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);


        // $curr = false;
        // $trans = $this->trans_sales($args,$curr);
        // // $trans = $this->trans_sales_cat($args,false);
        // $sales = $trans['sales'];

        // $trans_menus = $this->menu_sales($sales['settled']['ids'],$curr,$branch_id,$brand,$from,$to);
        // $trans_charges = $this->charges_sales($sales['settled']['ids'],$curr,$branch_id,$brand,$conso_args);
        // $trans_discounts = $this->discounts_sales($sales['settled']['ids'],$curr,$branch_id,$brand,$from,$to);
        // $tax_disc = $trans_discounts['tax_disc_total'];
        // $no_tax_disc = $trans_discounts['no_tax_disc_total'];
        // $trans_local_tax = $this->local_tax_sales($sales['settled']['ids'],$curr,$branch_id,$brand,$conso_args);
        // $trans_tax = $this->tax_sales($sales['settled']['ids'],$curr,$branch_id,$brand,$conso_args);
        // $trans_no_tax = $this->no_tax_sales($sales['settled']['ids'],$curr,$branch_id,$brand,$conso_args);
        // $trans_zero_rated = $this->zero_rated_sales($sales['settled']['ids'],$curr,$branch_id,$brand,$conso_args);
        // $payments = $this->payment_sales($sales['settled']['ids'],$curr,$branch_id,$brand,$conso_args);

        // $gross = $trans_menus['gross'];

        // $net = $trans['net'];
        // $void = $trans['void'];
        // $charges = $trans_charges['total'];
        // $discounts = $trans_discounts['total'];
        // $local_tax = $trans_local_tax['total'];
        // $less_vat = (($gross+$charges+$local_tax) - $discounts) - $net;

        // if($less_vat < 0)
        //     $less_vat = 0;


        // $tax = $trans_tax['total'];
        // $no_tax = $trans_no_tax['total'];
        // $zero_rated = $trans_zero_rated['total'];
        // $no_tax -= $zero_rated;

        // $loc_txt = numInt(($local_tax));
        // $net_no_adds = $net-($charges+$local_tax);
        // $nontaxable = $no_tax - $no_tax_disc;
        // $taxable =   ($gross - $discounts - $less_vat - $nontaxable) / 1.12;
        // $total_net = ($taxable) + ($nontaxable+$zero_rated) + $tax + $local_tax;
        // $add_gt = $taxable+$nontaxable+$zero_rated;
        // $nsss = $taxable +  $nontaxable +  $zero_rated;

        // $vat_ = $taxable * .12;

        // $pdf->ln(7);
        // $pdf->Cell(30, 0, 'GROSS', '', 0, 'L');
        // $pdf->Cell(35, 0, num($tot_gross), '', 0, 'R');
        // $pdf->ln();
        // $pdf->Cell(30, 0, 'VAT SALES', '', 0, 'L');
        // $pdf->Cell(35, 0, num($taxable), '', 0, 'R');
        // $pdf->ln();
        // $pdf->Cell(30, 0, 'VAT', '', 0, 'L');
        // $pdf->Cell(35, 0, num($vat_), '', 0, 'R');
        // $pdf->ln();
        // $pdf->Cell(30, 0, 'VAT EXEMPT SALES', '', 0, 'L');
        // $pdf->Cell(35, 0, num($nontaxable-$zero_rated), '', 0, 'R');
        // $pdf->ln();
        // $pdf->Cell(30, 0, 'ZERO RATED', '', 0, 'L');
        // $pdf->Cell(35, 0, num($zero_rated), '', 0, 'R');


        // //MENU SUB CAT
        // $pdf->ln(7);
        // $pdf->SetFont('helvetica', 'B', 9);
        // $pdf->Cell(30, 0, strtoupper('Sub Categories'), '', 0, 'L');
        // $pdf->Cell(35, 0, strtoupper('Amount'), '', 0, 'R');
        // $pdf->SetFont('helvetica', '', 9);

        // $subcats = $trans_menus['sub_cats'];
        // $qty = 0;
        // $total = 0;
        // foreach ($subcats as $id => $val) {
        //     $pdf->ln();
        //     $pdf->Cell(30, 0, strtoupper($val['name']), '', 0, 'L');
        //     $pdf->Cell(35, 0, num($val['amount']), '', 0, 'R');
        //     $total += $val['amount'];
        // }

        // // if($tot_gross_ret != 0){
        // //     $pdf->ln();
        // //     $pdf->Cell(30, 0, 'RETAIL', '', 0, 'L');
        // //     $pdf->Cell(35, 0, num($tot_gross_ret), '', 0, 'R');
        // //     $total += $tot_gross_ret;
        // // }

        // $pdf->ln();
        // $pdf->SetFont('helvetica', 'B', 9);
        // $pdf->Cell(30, 0,'SUBTOTAL ', 'T', 0, 'L');
        // $pdf->SetFont('helvetica', '', 9);
        // $pdf->Cell(35, 0, num($total), 'T', 0, 'R');

        // // numInt($trans_menus['mods_total'])
        // $pdf->ln();
        // // $pdf->SetFont('helvetica', 'B', 9);
        // $pdf->Cell(30, 0,'MODIFIERS TOTAL ', '', 0, 'L');
        // $pdf->SetFont('helvetica', '', 9);
        // $pdf->Cell(35, 0, num($trans_menus['mods_total']), '', 0, 'R');

        // $pdf->ln();
        // $pdf->Cell(30, 0,'SUB MODIFIERS TOTAL ', '', 0, 'L');
        // $pdf->SetFont('helvetica', '', 9);
        // $pdf->Cell(35, 0, num($trans_menus['submods_total']), '', 0, 'R');

        // $pdf->ln();
        // $pdf->SetFont('helvetica', 'B', 9);
        // $pdf->Cell(30, 0,'TOTAL ', 'T', 0, 'L');
        // $pdf->SetFont('helvetica', '', 9);
        // $pdf->Cell(35, 0, num($total + $trans_menus['mods_total']+$trans_menus['submods_total']), 'T', 0, 'R');


        // //DISCOUNTS
        // $pdf->ln(7);
        // $pdf->SetFont('helvetica', 'B', 9);
        // $pdf->Cell(60, 0, strtoupper('Discount'), '', 0, 'L');
        // $pdf->Cell(35, 0, strtoupper('Amount'), '', 0, 'R');
        // $pdf->SetFont('helvetica', '', 9);

        // $types = $trans_discounts['types'];
        // foreach ($types as $code => $val) {
        //     $pdf->ln();
        //     $pdf->Cell(60, 0, strtoupper($val['name']), '', 0, 'L');
        //     $pdf->Cell(35, 0, num($val['amount']), '', 0, 'R');
            
        // }

        // $pdf->ln();
        // $pdf->SetFont('helvetica', 'B', 9);
        // $pdf->Cell(60, 0,'TOTAL ', 'T', 0, 'L');
        // $pdf->SetFont('helvetica', '', 9);
        // $pdf->Cell(35, 0, num($discounts), 'T', 0, 'R');
        // $pdf->ln();
        // $pdf->SetFont('helvetica', 'B', 9);
        // $pdf->Cell(60, 0,'VAT EXEMPT ', '', 0, 'L');
        // $pdf->SetFont('helvetica', '', 9);
        // $pdf->Cell(35, 0, num($less_vat), '', 0, 'R');


        // //CAHRGES
        // $pdf->ln(7);
        // $pdf->SetFont('helvetica', 'B', 9);
        // $pdf->Cell(30, 0, strtoupper('Charges'), '', 0, 'L');
        // $pdf->Cell(35, 0, strtoupper('Amount'), '', 0, 'R');
        // $pdf->SetFont('helvetica', '', 9);
        // // $pdf->ln();

        // $types = $trans_charges['types'];
        // foreach ($types as $code => $val) {
        //     $pdf->ln();
        //     $pdf->Cell(30, 0, strtoupper($val['name']), '', 0, 'L');
        //     $pdf->Cell(35, 0, num($val['amount']), '', 0, 'R');
            
        // }
           
        // $pdf->ln();
        // $pdf->SetFont('helvetica', 'B', 9);
        // $pdf->Cell(30, 0,'TOTAL ', 'T', 0, 'L');
        // $pdf->SetFont('helvetica', '', 9);
        // $pdf->Cell(35, 0, num($charges), 'T', 0, 'R');


        // //PAYMENTS
        // $pdf->ln(7);
        // $pdf->SetFont('helvetica', 'B', 9);
        // $pdf->Cell(30, 0, strtoupper('Payment Mode'), '', 0, 'L');
        // $pdf->Cell(35, 0, strtoupper('Payment Amount'), '', 0, 'R');
        // $pdf->SetFont('helvetica', '', 9);
        // // $pdf->ln();


        // $payments_types = $payments['types'];
        // $payments_total = $payments['total'];
        // foreach ($payments_types as $code => $val) {
        // $pdf->ln();
        //     $pdf->Cell(30, 0, strtoupper($code), '', 0, 'L');
        //     $pdf->Cell(35, 0, num($val['amount']), '', 0, 'R');
            
        // }
           
        // $pdf->ln();
        // $pdf->SetFont('helvetica', 'B', 9);
        // $pdf->Cell(30, 0,'TOTAL ', 'T', 0, 'L');
        // $pdf->SetFont('helvetica', '', 9);
        // $pdf->Cell(35, 0, num($payments_total), 'T', 0, 'R');


        // if($trans['total_chit']){
        //     // $print_str .= append_chars(substrwords('TOTAL CHIT',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
        //     //               .append_chars(numInt($trans['total_chit']),"left",PAPER_RD_COL_3_3," ")."\r\n";
        //     // $print_str .= "\r\n";
        //     $pdf->ln(7);
        //     $pdf->SetFont('helvetica', 'B', 9);
        //     $pdf->Cell(30, 0,'TOTAL CHIT ', '', 0, 'L');
        //     $pdf->SetFont('helvetica', '', 9);
        //     $pdf->Cell(35, 0, num($trans['total_chit']), '', 0, 'R');
        // }



        // $types = $trans['types'];
        // $types_total = array();
        // $guestCount = 0;
        // foreach ($types as $type => $tp) {
        //     foreach ($tp as $id => $opt){
        //         if(isset($types_total[$type])){
        //             $types_total[$type] += round($opt->total_amount,2);

        //         }
        //         else{
        //             $types_total[$type] = round($opt->total_amount,2);
        //         }
        //         if($opt->guest == 0)
        //             $guestCount += 1;
        //         else
        //             $guestCount += $opt->guest;
        //     }
        // }
        // hide muna ayaw mag generate

        // $pdf->ln(7);
        // $pdf->SetFont('helvetica', 'B', 9);
        // $pdf->Cell(30, 0,'TRANS COUNT ', '', 0, 'L');
        // $tc_total  = 0;
        // $tc_qty = 0;
        // foreach ($types_total as $typ => $tamnt) {
        //     $pdf->SetFont('helvetica', '', 9);
        //     $pdf->ln();
        //     $pdf->Cell(30, 0, strtoupper($typ), '', 0, 'L');
        //     $pdf->Cell(20, 0, count($types[$typ]), '', 0, 'L');
        //     $pdf->Cell(25, 0, num($tamnt), '', 0, 'R');
        //     $tc_total += $tamnt;
        //     $tc_qty += count($types[$typ]);
        // }
        // $pdf->ln();
        // $pdf->SetFont('helvetica', 'B', 9);
        // $pdf->Cell(50, 0, 'TC TOTAL', 'T', 0, 'L');
        //     $pdf->SetFont('helvetica', '', 9);
        // // $pdf->Cell(20, 0, count($types[$typ]), '', 0, 'L');
        // $pdf->Cell(25, 0, num($tc_total), 'T', 0, 'R');


        

        // -----------------------------------------------------------------------------

        // $tbl = '<table cellspacing="0" cellpadding="1">';
        // $tbl .= "<tr>";
        // $tbl .= "<th style='width:60px;'>Category</th>"; 
        // $tbl .= "<th>Qty</th>";
        // $tbl .= "<th>VAT Sales</th>";
        // $tbl .= "<th>VAT</th>";
        // $tbl .= "<th>Gross</th>";
        // $tbl .= "<th>Sales (%)</th>";
        // $tbl .= "<th>Cost</th>";
        // $tbl .= "<th>Cost (%)</th>";
        // $tbl .= "</tr>";
        // foreach ($trans as $k => $v) {
        //     $tbl .= "<tr>";
        //         $tbl .= '<td>'.$v->menu_cat_name."</td>";             
        //         $tbl .= "<td style='text-align:right;'>".num($v->qty)."</td>";             
        //         $tbl .= "<td style='text-align:right;'>".num($v->vat_sales)."</td>"; 
        //         $tbl .= "<td style='text-align:right;'>".num($v->vat)."</td>";                 
        //         $tbl .= "<td style='text-align:right;'>".num($v->gross)."</td>";                 
        //         $tbl .= "<td style='text-align:right;'>".num(0)."</td>";                 
        //         $tbl .= "<td style='text-align:right;'>".num($v->cost)."</td>";                 
        //         $tbl .= "<td style='text-align:right;'>".num(0)."</td>";                 
        //     $tbl .= "</tr>";         
        // }
        // $tbl .= "</table>";
        
        // $pdf->writeHTML($tbl, true, false, false, false, '');

        // -----------------------------------------------------------------------------

        //Close and output PDF document
        $pdf->Output('sales_report.pdf', 'I');

        //============================================================+
        // END OF FILE
        //============================================================+   
    }
    public function menu_sales_rep_gen()
    {
        $this->load->model('dine/setup_model');
        // $this->load->database('main', TRUE);
        $this->menu_model->db = $this->load->database('default', TRUE);
        $this->load->model("dine/menu_model");
        $branch_id = $this->input->post('branch_id');
        // $brand = $this->input->post('brand');
        $brand = "";
        $setup = $this->setup_model->get_details($branch_id);
        $set = $setup[0];
        start_load(0);
        $menu_cat_id = $this->input->post("menu_cat_id");        
        $daterange = $this->input->post("calendar_range");        
        $dates = explode(" to ",$daterange);
        // $from = date2SqlDateTime($dates[0]);
        // $to = date2SqlDateTime($dates[1]);
        // $date = $this->input->post("date");
        $from = date2SqlDateTime($dates[0]);   
        // echo 'hahaha';     
        $to = date2SqlDateTime($dates[1]);
        // echo $dates[0];die();
        $trans = $this->menu_model->get_menu_sales_rep($from, $to, $menu_cat_id,$branch_id,$brand);
        // echo $this->menu_model->db->last_query();die();
        $trans_ret = array();
        // $trans_ret = $this->menu_model->get_menu_sales_rep_retail($from, $to, "",$branch_id,$brand);
        // $trans_mod = $this->menu_model->get_menu_modifer_sales_rep($from, $to, $menu_cat_id,$branch_id,$brand); 
                        
        // echo "<pre>", print_r($trans), "</pre>";
        $trans_count = count($trans);
        $counter = 0;
        $this->make->sDiv(array('class'=>'card'));
            $this->make->sTable(array('class'=>'table striped-table mb-0'));
                $this->make->sTableHead();
                    $this->make->sRow(array('class'=>'table-header'));
                        if($branch_id == ''){
                            $this->make->th('Branch',array('class'=>'th'));
                        }
                        $this->make->th('Menu',array('class'=>'th'));
                        $this->make->th('Category',array('class'=>'th'));
                        $this->make->th('Qty',array('class'=>'th'));
                        $this->make->th('Gross',array('class'=>'th'));
                        $this->make->th('Sales (%)',array('class'=>'th'));
                        $this->make->th('Cost',array('class'=>'th'));
                        $this->make->th('Cost (%)',array('class'=>'th'));
                        $this->make->th('Margin',array('class'=>'th'));
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
                    $tot_cost_prcnt = 0;
                    foreach ($trans as $val) {
                        $tot_gross += $val->gross;
                        $tot_cost += $val->cost;
                        $tot_margin += $val->gross - $val->cost;
                    }
                    foreach ($trans as $res) {
                        $this->make->sRow(array("class"=>"table-data-row"));
                            if($branch_id == ''){
                                $this->make->td($res->branch_code,array("class"=>"table-data-name"));
                            }
                            $this->make->td($res->menu_name,array("style"=>"text-align:center","class"=>"table-data"));  
                            $this->make->td($res->menu_cat_name,array("style"=>"text-align:center","class"=>"table-data"));  
                            $this->make->td(num($res->qty),array("style"=>"text-align:center","class"=>"table-data"));                           
                            // $this->make->td(num($res->vat_sales), array("style"=>"text-align:right"));                            
                            // $this->make->td(num($res->vat), array("style"=>"text-align:right"));                            
                            $this->make->td(num($res->gross),array("style"=>"text-align:center","class"=>"table-data"));                             
                            if($tot_gross != 0){
                                $this->make->td(num($res->gross / $tot_gross * 100)."%",array("style"=>"text-align:center","class"=>"table-data"));                                
                            }else{
                                $this->make->td("0.00%",array("style"=>"text-align:center","class"=>"table-data"));                                
                            }
                            $this->make->td(num($res->cost),array("style"=>"text-align:center","class"=>"table-data"));                            
                            if($tot_cost != 0){
                                $this->make->td(num($res->cost / $tot_cost * 100)."%",array("style"=>"text-align:center","class"=>"table-data"));  
                            }else{
                                $this->make->td("0.00%",array("style"=>"text-align:center","class"=>"table-data"));                                
                            }
                            $this->make->td(num($res->gross - $res->cost),array("style"=>"text-align:center","class"=>"table-data"));  

                        $this->make->eRow();

                        /// Grand Total
                        $tot_qty += $res->qty;
                        // $tot_vat_sales += $res->vat_sales;
                        // $tot_vat += $res->vat;
                        // $tot_gross += $res->gross;
                        $tot_sales_prcnt = 0;
                        // $tot_cost += $res->cost;
                        $tot_cost_prcnt = 0;

                        $counter++;
                        $progress = ($counter / $trans_count) * 100;
                        update_load(num($progress));

                    }     
                    $this->make->sRow(array("class"=>"table-data-grand-total "));
                        $this->make->th('Grand Total', array("style"=>"text-align:center","class"=>"td"));
                        if($branch_id == ''){
                            $this->make->th('  ');
                        }                        
                        $this->make->th('  ');
                        $this->make->th('<span class="bg-success-focus text-success-main px-32 py-4 rounded-pill fw-medium ">'.num($tot_qty).'</span>', array("style"=>"text-align:center","class"=>"td "));
                        $this->make->th('<span class="bg-success-focus text-success-main px-32 py-4 rounded-pill fw-medium">'.num($tot_gross).'</span>', array("style"=>"text-align:center","class"=>"td"));
                        $this->make->th("", array("style"=>"text-align:center","class"=>"td"));
                        $this->make->th('<span class="bg-success-focus text-success-main px-32 py-4 rounded-pill fw-medium ">'.num($tot_cost).'</span>', array("style"=>"text-align:center","class"=>"td"));
                        $this->make->th("", array("style"=>"text-align:center","class"=>"td"));                                                
                        $this->make->th('<span class="bg-success-focus text-success-main px-32 py-4 rounded-pill fw-medium ">'.num($tot_margin).'</span>', array("style"=>"text-align:center","class"=>"td"));                     
                    $this->make->eRow();                                 
                $this->make->eTableBody();
            $this->make->eTable();
        $this->make->eDiv();

        // $this->make->append('<center><h4>Menu Modifiers</h4></center>');
        // $this->make->sDiv();
        //     $this->make->sTable(array('class'=>'table reportTBL'));
        //         $this->make->sTableHead();
        //             $this->make->sRow();
        //                 if($branch_id == ''){
        //                     $this->make->th('Branch');
        //                 }
        //                 $this->make->th('Menu');
        //                 // $this->make->th('Category');
        //                 $this->make->th('Qty');
        //                 // $this->make->th('VAT Sales');
        //                 // $this->make->th('VAT');
        //                 $this->make->th('Gross');
        //                 $this->make->th('Sales (%)');
        //                 $this->make->th('Cost');
        //                 $this->make->th('Cost (%)');
        //                 $this->make->th('Margin');
        //             $this->make->eRow();
        //         $this->make->eTableHead();
        //         $this->make->sTableBody();
        //             $tot_qty = 0;
        //             $tot_vat_sales = 0;
        //             $tot_vat = 0;
        //             $tot_gross = 0;
        //             $tot_sales_prcnt = 0;
        //             $tot_cost = 0;
        //             $tot_margin = 0;
        //             $tot_cost_prcnt = 0;
        //             foreach ($trans_mod as $val) {
        //                 $tot_gross += $val->gross;
        //                 // $tot_cost += $val->cost;
        //                 $tot_margin += $val->gross - 0;
        //             }
        //             foreach ($trans_mod as $res) {
        //                 $this->make->sRow();
        //                     if($branch_id == ''){
        //                         $this->make->td($res->branch_code);
        //                     }
        //                     $this->make->td($res->modifier_name);
        //                     // $this->make->td($res->cat_name);
        //                     $this->make->td(num($res->qty), array("style"=>"text-align:right"));                            
        //                     // $this->make->td(num($res->vat_sales), array("style"=>"text-align:right"));                            
        //                     // $this->make->td(num($res->vat), array("style"=>"text-align:right"));                            
        //                     $this->make->td(num($res->gross), array("style"=>"text-align:right"));                            
        //                     if($tot_gross != 0){
        //                         $this->make->td(num($res->gross / $tot_gross * 100)."%", array("style"=>"text-align:right"));                                
        //                     }else{
        //                         $this->make->td("0.00%", array("style"=>"text-align:right"));                                
        //                     }
        //                     $this->make->td(num(0), array("style"=>"text-align:right"));                            
        //                     if($tot_cost != 0){
        //                         $this->make->td(num($res->cost / $tot_cost * 100)."%", array("style"=>"text-align:right"));                            
        //                     }else{
        //                         $this->make->td("0.00%", array("style"=>"text-align:right"));                                
        //                     }
        //                     $this->make->td(num($res->gross - 0), array("style"=>"text-align:right")); 

        //                 $this->make->eRow();

        //                 /// Grand Total
        //                 $tot_qty += $res->qty;
        //                 // $tot_vat_sales += $res->vat_sales;
        //                 // $tot_vat += $res->vat;
        //                 // $tot_gross += $res->gross;
        //                 $tot_sales_prcnt = 0;
        //                 // $tot_cost += $res->cost;
        //                 $tot_cost_prcnt = 0;

        //                 // $counter++;
        //                 // $progress = ($counter / $trans_count) * 100;
        //                 // update_load(num($progress));

        //             }     
        //             $this->make->sRow();
        //                 $this->make->th('Grand Total');
        //                 // $this->make->th("");
        //                 $this->make->th("");
        //                 $this->make->th(num($tot_qty), array("style"=>"text-align:right"));
        //                 // $this->make->th(num($tot_vat_sales), array("style"=>"text-align:right"));
        //                 // $this->make->th(num($tot_vat), array("style"=>"text-align:right"));
        //                 $this->make->th(num($tot_gross), array("style"=>"text-align:right"));
        //                 $this->make->th("", array("style"=>"text-align:right"));
        //                 $this->make->th(num($tot_cost), array("style"=>"text-align:right"));
        //                 $this->make->th("", array("style"=>"text-align:right"));                        
        //                 $this->make->th(num($tot_margin), array("style"=>"text-align:right"));                          
        //             $this->make->eRow();                                 
        //         $this->make->eTableBody();
        //     $this->make->eTable();
        // $this->make->eDiv();
        
        // $this->make->append('<center><h4>Retail Items</h4></center>');
        // $this->make->sDiv();
        //     $this->make->sTable(array('class'=>'table reportTBL'));
        //         $this->make->sTableHead();
        //             $this->make->sRow();
        //                 if($branch_id == ''){
        //                     $this->make->th('Branch');
        //                 }
        //                 $this->make->th('Menu');
        //                 $this->make->th('Category');
        //                 $this->make->th('Qty');
        //                 // $this->make->th('VAT Sales');
        //                 // $this->make->th('VAT');
        //                 $this->make->th('Gross');
        //                 $this->make->th('Sales (%)');
        //                 $this->make->th('Cost');
        //                 $this->make->th('Cost (%)');
        //                 $this->make->th('Margin');
        //             $this->make->eRow();
        //         $this->make->eTableHead();
        //         $this->make->sTableBody();
        //             $tot_qty = 0;
        //             $tot_vat_sales = 0;
        //             $tot_vat = 0;
        //             $tot_gross = 0;
        //             $tot_sales_prcnt = 0;
        //             $tot_cost = 0;
        //             $tot_margin = 0;
        //             $tot_cost_prcnt = 0;
        //             foreach ($trans_ret as $val) {
        //                 $tot_gross += $val->gross;
        //                 // $tot_cost += $val->cost;
        //                 $tot_margin += $val->gross - 0;
        //             }
        //             foreach ($trans_ret as $res) {
        //                 $this->make->sRow();
        //                     if($branch_id == ''){
        //                         $this->make->td($res->branch_code);
        //                     }
        //                     $this->make->td($res->item_name);
        //                     $this->make->td($res->cat_name);
        //                     $this->make->td(num($res->qty), array("style"=>"text-align:right"));                            
        //                     // $this->make->td(num($res->vat_sales), array("style"=>"text-align:right"));                            
        //                     // $this->make->td(num($res->vat), array("style"=>"text-align:right"));                            
        //                     $this->make->td(num($res->gross), array("style"=>"text-align:right"));                            
        //                     if($tot_gross != 0){
        //                         $this->make->td(num($res->gross / $tot_gross * 100)."%", array("style"=>"text-align:right"));                                
        //                     }else{
        //                         $this->make->td("0.00%", array("style"=>"text-align:right"));                                
        //                     }
        //                     $this->make->td(num(0), array("style"=>"text-align:right"));                            
        //                     if($tot_cost != 0){
        //                         $this->make->td(num($res->cost / $tot_cost * 100)."%", array("style"=>"text-align:right"));                            
        //                     }else{
        //                         $this->make->td("0.00%", array("style"=>"text-align:right"));                                
        //                     }
        //                     $this->make->td(num($res->gross - 0), array("style"=>"text-align:right")); 

        //                 $this->make->eRow();

        //                 /// Grand Total
        //                 $tot_qty += $res->qty;
        //                 // $tot_vat_sales += $res->vat_sales;
        //                 // $tot_vat += $res->vat;
        //                 // $tot_gross += $res->gross;
        //                 $tot_sales_prcnt = 0;
        //                 // $tot_cost += $res->cost;
        //                 $tot_cost_prcnt = 0;

        //                 // $counter++;
        //                 // $progress = ($counter / $trans_count) * 100;
        //                 // update_load(num($progress));

        //             }     
        //             $this->make->sRow();
        //                 $this->make->th('Grand Total');
        //                 if($branch_id == ''){
        //                     $this->make->th("");
        //                 }
        //                 $this->make->th("");
        //                 $this->make->th(num($tot_qty), array("style"=>"text-align:right"));
        //                 // $this->make->th(num($tot_vat_sales), array("style"=>"text-align:right"));
        //                 // $this->make->th(num($tot_vat), array("style"=>"text-align:right"));
        //                 $this->make->th(num($tot_gross), array("style"=>"text-align:right"));
        //                 $this->make->th("", array("style"=>"text-align:right"));
        //                 $this->make->th(num($tot_cost), array("style"=>"text-align:right"));
        //                 $this->make->th("", array("style"=>"text-align:right"));                        
        //                 $this->make->th(num($tot_margin), array("style"=>"text-align:right"));                          
        //             $this->make->eRow();                                 
        //         $this->make->eTableBody();
        //     $this->make->eTable();
        // $this->make->eDiv();

        update_load(100);
        $code = $this->make->code();
        $json['code'] = $code;        
        $json['tbl_vals'] = $trans;
        $json['dates'] = $this->input->post('calendar_range');
        echo json_encode($json);
    }
    public function menu_sales_rep_pdf()
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
        $pdf->SetTitle('Sales Report');
        $pdf->SetSubject('');
        $pdf->SetKeywords('');

        // set default header data
        
        $branch_id = $_GET['branch_id'];
        // $brand = $_GET['brand']; 
        $brand = ""; 

        $setup = $this->setup_model->get_details($branch_id);
        $set = $setup[0];
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "Menu Sales Report", $set->address);

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

        // $this->load->database('main', TRUE);
        $this->menu_model->db = $this->load->database('default', TRUE);
        $this->load->model("dine/menu_model");
        start_load(0);

        // set font
        $pdf->SetFont('helvetica', 'B', 11);

        // add a page
        $pdf->AddPage();
        
        $menu_cat_id = $_GET['menu_cat_id'];        
        $daterange = $_GET['calendar_range'];        
        $dates = explode(" to ",$daterange);

        // $from = date2SqlDateTime($dates[0]);
        // $to = date2SqlDateTime($dates[1]);
        $from = date2SqlDateTime($dates[0]);        
        $to = date2SqlDateTime($dates[1]);
        $trans = $this->menu_model->get_menu_sales_rep($from, $to, $menu_cat_id, $branch_id,$brand);
        // $trans_mod = $this->menu_model->get_menu_modifer_sales_rep($from, $to, $menu_cat_id,$branch_id,$brand);
        // $trans_payment = $this->menu_model->get_payment_date($from, $to);

        $brd = $this->setup_model->get_brands($brand);

        if($brd){
            $pdf->Write(0, $brd[0]->brand_name, '', 0, 'L', true, 0, false, false, 0);
            $pdf->ln(0.9);
        }                  

        $pdf->Write(0, 'Sales Report', '', 0, 'L', true, 0, false, false, 0);
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

        // echo "<pre>", print_r($trans), "</pre>";die();

        // -----------------------------------------------------------------------------
        $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => 'black'));
        if(!$branch_id){
             $pdf->Cell(30, 0, 'Branch', 'B', 0, 'L');        
             $pdf->Cell(45, 0, 'Menu', 'B', 0, 'L');  
             $pdf->Cell(18, 0, 'Category', 'B', 0, 'L');        
        }else{
             $pdf->Cell(55, 0, 'Menu', 'B', 0, 'L'); 
             $pdf->Cell(38, 0, 'Category', 'B', 0, 'L');        
        }
              
        $pdf->Cell(25, 0, 'Qty', 'B', 0, 'C');        
        // $pdf->Cell(25, 0, 'VAT Sales', 'B', 0, 'C');        
        // $pdf->Cell(25, 0, 'VAT', 'B', 0, 'C');        
        $pdf->Cell(25, 0, 'Gross', 'B', 0, 'C');        
        $pdf->Cell(25, 0, 'Sales (%)', 'B', 0, 'C');        
        $pdf->Cell(25, 0, 'Cost', 'B', 0, 'C');        
        $pdf->Cell(25, 0, 'Cost (%)', 'B', 0, 'C'); 
         $pdf->Cell(50, 0, 'Margin', 'B', 0, 'R');        
        $pdf->ln();                  

        // GRAND TOTAL VARIABLES
        $tot_qty = 0;
        $tot_vat_sales = 0;
        $tot_vat = 0;
        $tot_gross = 0;
        $tot_mod_gross = 0;
        $tot_sales_prcnt = 0;
        $tot_cost = 0;
        $tot_cost_prcnt = 0; 
        $tot_margin = 0; 
        $counter = 0;
        $progress = 0;
        $trans_count = count($trans);
        foreach ($trans as $val) {
            $tot_gross += $val->gross;
            $tot_cost += $val->cost;
            $tot_margin += $val->gross - $val->cost;
        }

        foreach ($trans as $k => $v) {
            if(!$branch_id){
                $pdf->Cell(30, 0, $v->branch_code, '', 0, 'L');        
                $pdf->Cell(45, 0, $v->menu_name, '', 0, 'L');        
                $pdf->Cell(18, 0, $v->menu_cat_name, '', 0, 'L');        

            }else{
                $pdf->Cell(55, 0, $v->menu_name, '', 0, 'L');        
                $pdf->Cell(38, 0, $v->menu_cat_name, '', 0, 'L'); 
            }
            $pdf->Cell(25, 0, num($v->qty), '', 0, 'R');        
            $pdf->Cell(25, 0, num($v->gross), '', 0, 'R');        
            if($tot_gross != 0){
                $pdf->Cell(25, 0, num($v->gross / $tot_gross * 100)."%", '', 0, 'R');                        
            }else{
                $pdf->Cell(25, 0, "0.00%", '', 0, 'R');                                        
            }
            $pdf->Cell(25, 0, num($v->cost), '', 0, 'R');        
            if($tot_cost != 0){
                $pdf->Cell(25, 0, num($v->cost / $tot_cost * 100)."%", '', 0, 'R');                    
            }else{
                $pdf->Cell(25, 0, "0.00%", '', 0, 'R');                                    
            }
            $pdf->Cell(50, 0, num($v->gross - $v->cost), '', 0, 'R');                    
            $pdf->ln();                

            // Grand Total
            $tot_qty += $v->qty;
            // $tot_vat_sales += $v->vat_sales;
            // $tot_vat += $v->vat;
            // $tot_gross += $v->gross;
            $tot_sales_prcnt = 0;
            // $tot_cost += $v->cost;
            $tot_cost_prcnt = 0;

            $counter++;
            $progress = ($counter / $trans_count) * 100;
            update_load(num($progress));              
        }
        update_load(100);        
        $pdf->Cell(55, 0, "Grand Total", 'T', 0, 'L');        
        $pdf->Cell(38, 0, "", 'T', 0, 'L');        
        $pdf->Cell(25, 0, num($tot_qty), 'T', 0, 'R');        
        // $pdf->Cell(25, 0, num($tot_vat_sales), 'T', 0, 'R');        
        // $pdf->Cell(25, 0, num($tot_vat), 'T', 0, 'R');        
        $pdf->Cell(25, 0, num($tot_gross), 'T', 0, 'R');        
        $pdf->Cell(25, 0, "", 'T', 0, 'R');        
        $pdf->Cell(25, 0, num($tot_cost), 'T', 0, 'R');        
        $pdf->Cell(25, 0, "", 'T', 0, 'R');        
        $pdf->Cell(50, 0, num($tot_margin), 'T', 0, 'R');
        $pdf->ln();   

        // $pdf->ln(7);                
        // $pdf->Cell(267, 0, 'Menu Modifiers', '', 0, 'C');
        // $pdf->ln();
        // $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => 'black'));
        // if(!$branch_id){
        //     $pdf->Cell(38, 0, 'Branch', 'B', 0, 'L');        
        //     $pdf->Cell(55, 0, 'Modifier', 'B', 0, 'L');        
        // }else{
        //     $pdf->Cell(93, 0, 'Modifier', 'B', 0, 'L');        
        // }
        // // $pdf->Cell(38, 0, 'Category', 'B', 0, 'L');        
        // $pdf->Cell(25, 0, 'Qty', 'B', 0, 'C');        
        // $pdf->Cell(25, 0, 'VAT Sales', 'B', 0, 'C');        
        // $pdf->Cell(25, 0, 'VAT', 'B', 0, 'C');        
        // $pdf->Cell(25, 0, 'Gross', 'B', 0, 'C');        
        // $pdf->Cell(25, 0, 'Sales (%)', 'B', 0, 'C');        
        // $pdf->Cell(25, 0, 'Cost', 'B', 0, 'C');        
        // $pdf->Cell(25, 0, 'Cost (%)', 'B', 0, 'C');        
        // $pdf->ln();                  

        // // GRAND TOTAL VARIABLES
        // $tot_qty = 0;
        // $tot_vat_sales = 0;
        // $tot_vat = 0;
        // $tot_gross = 0;
        // $tot_sales_prcnt = 0;
        // $tot_cost = 0;
        // $tot_cost_prcnt = 0; 
        // $counter = 0;
        // $progress = 0;
        // $trans_count = count($trans_mod);

        // foreach ($trans_mod as $k => $v) {
        //     if(!$branch_id){
        //         $pdf->Cell(38, 0, $v->branch_code, '', 0, 'L');         
        //         $pdf->Cell(55, 0, $v->modifier_name, '', 0, 'L');         
        //     }else{
        //         $pdf->Cell(93, 0, $v->modifier_name, '', 0, 'L');  
        //     }
        //     // $pdf->Cell(93, 0, $v->modifier_name, '', 0, 'L');        
        //     // $pdf->Cell(38, 0, $v->menu_cat_name, '', 0, 'L');        
        //     $pdf->Cell(25, 0, num($v->qty), '', 0, 'R');        
        //     $pdf->Cell(25, 0, num($v->vat_sales), '', 0, 'R');        
        //     $pdf->Cell(25, 0, num($v->vat), '', 0, 'R');        
        //     $pdf->Cell(25, 0, num($v->gross), '', 0, 'R');        
        //     $pdf->Cell(25, 0, num(0), '', 0, 'R');        
        //     $pdf->Cell(25, 0, num($v->cost), '', 0, 'R');        
        //     $pdf->Cell(25, 0, num(0), '', 0, 'R');                    
        //     $pdf->ln();                

        //     // Grand Total
        //     $tot_qty += $v->qty;
        //     $tot_vat_sales += $v->vat_sales;
        //     $tot_vat += $v->vat;
        //     $tot_gross += $v->gross;
        //     $tot_sales_prcnt = 0;
        //     $tot_cost += $v->cost;
        //     $tot_cost_prcnt = 0;

        //     $counter++;
        //     $progress = ($counter / $trans_count) * 100;
        //     update_load(num($progress));              
        // }
        // update_load(100);        
        // $pdf->Cell(55, 0, "Grand Total", 'T', 0, 'L');        
        // $pdf->Cell(38, 0, "", 'T', 0, 'L');        
        // $pdf->Cell(25, 0, num($tot_qty), 'T', 0, 'R');        
        // $pdf->Cell(25, 0, num($tot_vat_sales), 'T', 0, 'R');        
        // $pdf->Cell(25, 0, num($tot_vat), 'T', 0, 'R');        
        // $pdf->Cell(25, 0, num($tot_gross), 'T', 0, 'R');        
        // $pdf->Cell(25, 0, num(0), 'T', 0, 'R');        
        // $pdf->Cell(25, 0, num($tot_cost), 'T', 0, 'R');        
        // $pdf->Cell(25, 0, num(0), 'T', 0, 'R'); 



        ///////////fpr payments

        // $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
        // $args["trans_sales.inactive = 0"] = array('use'=>'where','val'=>null,'third'=>false);
        // $args["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);
        // if($branch_id !=''){
        //     $args += array('trans_sales.branch_code'=>$branch_id);
        // }


        // $post = $this->set_post();
        // $conso_args = array();
        // $conso_args["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);

        // $curr = false;
        // $trans = $this->trans_sales($args,$curr,$branch_id,$brand);
        // $sales = $trans['sales'];

        // $trans_menus = $this->menu_sales($sales['settled']['ids'],$curr,$branch_id,$brand,$from,$to);
        // $trans_charges = $this->charges_sales($sales['settled']['ids'],$curr,$branch_id,$brand,$conso_args);
        // $trans_discounts = $this->discounts_sales($sales['settled']['ids'],$curr,$branch_id,$brand,$from,$to);
        // $tax_disc = $trans_discounts['tax_disc_total'];
        // $no_tax_disc = $trans_discounts['no_tax_disc_total'];
        // $trans_local_tax = $this->local_tax_sales($sales['settled']['ids'],$curr,$branch_id,$brand,$conso_args);
        // $trans_tax = $this->tax_sales($sales['settled']['ids'],$curr,$branch_id,$brand,$conso_args);
        // $trans_no_tax = $this->no_tax_sales($sales['settled']['ids'],$curr,$branch_id,$brand,$conso_args);
        // $trans_zero_rated = $this->zero_rated_sales($sales['settled']['ids'],$curr,$branch_id,$brand,$conso_args);
        // $payments = $this->payment_sales($sales['settled']['ids'],$curr,$branch_id,$brand,$conso_args);

        // $gross = $trans_menus['gross'];

        // $net = $trans['net'];
        // $void = $trans['void'];
        // $charges = $trans_charges['total'];
        // $discounts = $trans_discounts['total'];
        // $local_tax = $trans_local_tax['total'];
        // $less_vat = (($gross+$charges+$local_tax) - $discounts) - $net;

        // if($less_vat < 0)
        //     $less_vat = 0;

        // $tax = $trans_tax['total'];
        // $no_tax = $trans_no_tax['total'];
        // $zero_rated = $trans_zero_rated['total'];
        // $no_tax -= $zero_rated;

        // $loc_txt = numInt(($local_tax));
        // $net_no_adds = $net-($charges+$local_tax);
        // $nontaxable = $no_tax - $no_tax_disc;
        // $taxable =   ($gross - $discounts - $less_vat - $nontaxable) / 1.12;
        // $total_net = ($taxable) + ($nontaxable+$zero_rated) + $tax + $local_tax;
        // $add_gt = $taxable+$nontaxable+$zero_rated;
        // $nsss = $taxable +  $nontaxable +  $zero_rated;

        // $vat_ = $taxable * .12;

        // $pdf->ln(7);
        // $pdf->Cell(30, 0, 'GROSS', '', 0, 'L');
        // $pdf->Cell(35, 0, num($tot_gross), '', 0, 'R');
        // $pdf->ln();
        // $pdf->Cell(30, 0, 'VAT SALES', '', 0, 'L');
        // $pdf->Cell(35, 0, num($taxable), '', 0, 'R');
        // $pdf->ln();
        // $pdf->Cell(30, 0, 'VAT', '', 0, 'L');
        // $pdf->Cell(35, 0, num($vat_), '', 0, 'R');
        // $pdf->ln();
        // $pdf->Cell(30, 0, 'VAT EXEMPT SALES', '', 0, 'L');
        // $pdf->Cell(35, 0, num($nontaxable-$zero_rated), '', 0, 'R');
        // $pdf->ln();
        // $pdf->Cell(30, 0, 'ZERO RATED', '', 0, 'L');
        // $pdf->Cell(35, 0, num($zero_rated), '', 0, 'R');

        //MENU SUB CAT
        // $pdf->ln(7);
        // $pdf->SetFont('helvetica', 'B', 9);
        // $pdf->Cell(30, 0, strtoupper('Sub Categories'), '', 0, 'L');
        // $pdf->Cell(35, 0, strtoupper('Amount'), '', 0, 'R');
        // $pdf->SetFont('helvetica', '', 9);

        // $subcats = $trans_menus['sub_cats'];
        // $qty = 0;
        // $total = 0;
        // foreach ($subcats as $id => $val) {
        //     $pdf->ln();
        //     $pdf->Cell(30, 0, strtoupper($val['name']), '', 0, 'L');
        //     $pdf->Cell(35, 0, num($val['amount']), '', 0, 'R');
        //     $total += $val['amount'];
        // }

        // $pdf->ln();
        // $pdf->SetFont('helvetica', 'B', 9);
        // $pdf->Cell(30, 0,'SUBTOTAL ', 'T', 0, 'L');
        // $pdf->SetFont('helvetica', '', 9);
        // $pdf->Cell(35, 0, num($total), 'T', 0, 'R');

        // $pdf->ln();
        // $pdf->Cell(30, 0,'MODIFIERS TOTAL ', '', 0, 'L');
        // $pdf->SetFont('helvetica', '', 9);
        // $pdf->Cell(35, 0, num($trans_menus['mods_total']), '', 0, 'R');

        // $pdf->ln();
        // $pdf->Cell(30, 0,'SUB MODIFIERS TOTAL ', '', 0, 'L');
        // $pdf->SetFont('helvetica', '', 9);
        // $pdf->Cell(35, 0, num($trans_menus['submods_total']), '', 0, 'R');

        // $pdf->ln();
        // $pdf->SetFont('helvetica', 'B', 9);
        // $pdf->Cell(30, 0,'TOTAL ', 'T', 0, 'L');
        // $pdf->SetFont('helvetica', '', 9);
        // $pdf->Cell(35, 0, num($total + $trans_menus['mods_total'] + $trans_menus['submods_total']), 'T', 0, 'R');


        //DISCOUNTS
        // $pdf->ln(7);
        // $pdf->SetFont('helvetica', 'B', 9);
        // $pdf->Cell(60, 0, strtoupper('Discount'), '', 0, 'L');
        // $pdf->Cell(35, 0, strtoupper('Amount'), '', 0, 'R');
        // $pdf->SetFont('helvetica', '', 9);

        // $types = $trans_discounts['types'];
        // foreach ($types as $code => $val) {
        //     $pdf->ln();
        //     $pdf->Cell(60, 0, strtoupper($val['name']), '', 0, 'L');
        //     $pdf->Cell(35, 0, num($val['amount']), '', 0, 'R');
            
        // }

        // $pdf->ln();
        // $pdf->SetFont('helvetica', 'B', 9);
        // $pdf->Cell(60, 0,'TOTAL ', 'T', 0, 'L');
        // $pdf->SetFont('helvetica', '', 9);
        // $pdf->Cell(35, 0, num($discounts), 'T', 0, 'R');
        // $pdf->ln();
        // $pdf->SetFont('helvetica', 'B', 9);
        // $pdf->Cell(60, 0,'VAT EXEMPT ', '', 0, 'L');
        // $pdf->SetFont('helvetica', '', 9);
        // $pdf->Cell(35, 0, num($less_vat), '', 0, 'R');


        //CAHRGES
        // $pdf->ln(7);
        // $pdf->SetFont('helvetica', 'B', 9);
        // $pdf->Cell(30, 0, strtoupper('Charges'), '', 0, 'L');
        // $pdf->Cell(35, 0, strtoupper('Amount'), '', 0, 'R');
        // $pdf->SetFont('helvetica', '', 9);

        // $types = $trans_charges['types'];
        // foreach ($types as $code => $val) {
        //     $pdf->ln();
        //     $pdf->Cell(30, 0, strtoupper($val['name']), '', 0, 'L');
        //     $pdf->Cell(35, 0, num($val['amount']), '', 0, 'R');
            
        // }
           
        // $pdf->ln();
        // $pdf->SetFont('helvetica', 'B', 9);
        // $pdf->Cell(30, 0,'TOTAL ', 'T', 0, 'L');
        // $pdf->SetFont('helvetica', '', 9);
        // $pdf->Cell(35, 0, num($charges), 'T', 0, 'R');


        //PAYMENTS
        // $pdf->ln(7);
        // $pdf->SetFont('helvetica', 'B', 9);
        // $pdf->Cell(30, 0, strtoupper('Payment Mode'), '', 0, 'L');
        // $pdf->Cell(35, 0, strtoupper('Payment Amount'), '', 0, 'R');
        // $pdf->SetFont('helvetica', '', 9);


        // $payments_types = $payments['types'];
        // $payments_total = $payments['total'];
        // foreach ($payments_types as $code => $val) {
        // $pdf->ln();
        //     $pdf->Cell(30, 0, strtoupper($code), '', 0, 'L');
        //     $pdf->Cell(35, 0, num($val['amount']), '', 0, 'R');
            
        // }
           
        // $pdf->ln();
        // $pdf->SetFont('helvetica', 'B', 9);
        // $pdf->Cell(30, 0,'TOTAL ', 'T', 0, 'L');
        // $pdf->SetFont('helvetica', '', 9);
        // $pdf->Cell(35, 0, num($payments_total), 'T', 0, 'R');

        // if($trans['total_chit']){
        //     $pdf->ln(7);
        //     $pdf->SetFont('helvetica', 'B', 9);
        //     $pdf->Cell(30, 0,'TOTAL CHIT ', '', 0, 'L');
        //     $pdf->SetFont('helvetica', '', 9);
        //     $pdf->Cell(35, 0, num($trans['total_chit']), '', 0, 'R');
        // }



        // $types = $trans['types'];
        // $types_total = array();
        // $guestCount = 0;
        // foreach ($types as $type => $tp) {
        //     foreach ($tp as $id => $opt){
        //         if(isset($types_total[$type])){
        //             $types_total[$type] += round($opt->total_amount,2);

        //         }
        //         else{
        //             $types_total[$type] = round($opt->total_amount,2);
        //         }
        //         if($opt->guest == 0)
        //             $guestCount += 1;
        //         else
        //             $guestCount += $opt->guest;
        //     }
        // }

        // -----------------------------------------------------------------------------

        //Close and output PDF document
        $pdf->Output('sales_report.pdf', 'I');

        //============================================================+
        // END OF FILE
        //============================================================+   
    }
    
    public function dtr_rep()
    {
        $data = $this->syter->spawn('sales_rep');        
        $data['page_title'] = fa('fa-clock-o')." DTR";
        $data['code'] = dtrRep();
        $data['add_css'] = array('css/morris/morris.css','css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css');
        $data['add_js'] = array('js/plugins/morris/morris.min.js','js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js');
        $data['page_no_padding'] = false;
        $data['sideBarHide'] = false;
        $data['load_js'] = 'dine/reporting';
        $data['use_js'] = 'dtrRepJS';
        $this->load->view('page',$data);
    }
    public function dtr_rep_gen()
    {        
        $this->load->model("dine/reports_model");
        $this->load->database('default', TRUE);
        $this->load->model("dine/menu_model");
               
        $daterange = $this->input->post("calendar_range");       
        $start_time = $this->input->post("start_time");       
        $end_time = $this->input->post("end_time");       
        $dates = explode(" to ",$daterange);
        $from = date2SqlDateTime($dates[0]. " ". $start_time);
        $to = date2SqlDateTime($dates[1]. " ". $end_time);                
        $trans = $this->reports_model->get_dtr($from, $to);
        // echo "<pre>", print_r($trans), "</pre>";
        $trans_count = count($trans);
        $counter = 0;
        $this->make->sDiv();
            $this->make->sTable(array('class'=>'table reportTBL'));
                $this->make->sTableHead();
                    $this->make->sRow();
                        $this->make->th('Date Time');
                        $this->make->th('Type');
                        $this->make->th('Employee Name');
                        $this->make->th('Emp Code');                        
                    $this->make->eRow();
                $this->make->eTableHead();
                $this->make->sTableBody();
                    foreach ($trans as $res) {
                        $this->make->sRow();
                            $this->make->td($res->datetime);
                            $this->make->td($res->type);
                            $this->make->td($res->fname. " ".$res->mname." ".$res->lname);
                            $this->make->td($res->username);                            
                        $this->make->eRow();

                        $counter++;
                        $progress = ($counter / $trans_count) * 100;
                        update_load(num($progress));

                    }    
                                           
                $this->make->eTableBody();
            $this->make->eTable();
        $this->make->eDiv();

       $this->make->sDiv();
        $this->make->sTable(array('class'=>'table reportTBL'));
             $trans2 = $this->reports_model->get_total_hours($from, $to);                         
                $this->make->sTableHead();
                    $this->make->sRow();
                        $this->make->th('Total Hours:');
                        $this->make->th('');
                    $this->make->eRow();                        
                $this->make->eTableHead();
                $this->make->sTableBody();                
                    foreach ($trans2 as $res) {
                        $this->make->sRow();
                            $this->make->td($res["fname"]. " ".$res["mname"]." ".$res["lname"]);
                            $this->make->td($res["time_to_sec"]);                            
                        $this->make->eRow();                        
                    }    
                                           
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
    public function dtr_rep_pdf()
    {
        // Include the main TCPDF library (search for installation path).
        require_once( APPPATH .'third_party/tcpdf.php');
        $this->load->model('dine/setup_model');     
        date_default_timezone_set('Asia/Manila');   

        // create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('iPOS');
        $pdf->SetTitle('DTR');
        $pdf->SetSubject('');
        $pdf->SetKeywords('');

        // set default header data
        $setup = $this->setup_model->get_details(1);
        $set = $setup[0];
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "BARCINO RETAIL CORPORATION", $set->address);

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
        $this->load->model("dine/reports_model");
        $this->load->database('default', TRUE);
        $this->load->model("dine/menu_model");
               
        start_load(0);

        // set font
        $pdf->SetFont('helvetica', 'B', 11);

        // add a page
        $pdf->AddPage();
        
        $daterange = $this->input->get("calendar_range");       
        $start_time = $this->input->get("start_time");       
        $end_time = $this->input->get("end_time");       
        $dates = explode(" to ",$daterange);
        $from = date2SqlDateTime($dates[0]. " ". $start_time);
        $to = date2SqlDateTime($dates[1]. " ". $end_time);                        
        // $date = $this->input->post("date");
        // $from = date2SqlDateTime($date. " ".$set->store_open);        
        // $to = date2SqlDateTime(date('Y-m-d', strtotime($date . ' +1 day')). " ".$set->store_close);
        $trans = $this->reports_model->get_dtr($from, $to);
        $total_hours = $this->reports_model->get_total_hours($from, $to); 
        
        $pdf->Write(0, 'EMPLOYEE DTR REPORT', '', 0, 'L', true, 0, false, false, 0);
        $pdf->SetLineStyle(array('width' => 0.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => 'black'));
        $pdf->Cell(180, 0, '', 'T', 0, 'C');
        $pdf->ln(0.9);      
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Write(0, 'Report Period:    ', '', 0, 'L', false, 0, false, false, 0);
        $pdf->Write(0, $daterange, '', 0, 'L', false, 0, false, false, 0);
        $pdf->setX(100);
        $pdf->Write(0, 'Report Generated:    '.(new \DateTime())->format('Y-m-d H:i:s'), '', 0, 'L', true, 0, false, false, 0);
        $pdf->Write(0, 'Transaction Time:    ', '', 0, 'L', false, 0, false, false, 0);
        $pdf->setX(100);
        $user = $this->session->userdata('user');
        $pdf->Write(0, 'Generated by:    '.$user["full_name"], '', 0, 'L', true, 0, false, false, 0);        
        $pdf->ln(1);      
        $pdf->Cell(180, 0, '', 'T', 0, 'C');
        $pdf->ln();              

        // echo "<pre>", print_r($trans), "</pre>";die();
           
        // -----------------------------------------------------------------------------
        $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => 'black'));
        $pdf->Cell(43, 0, 'Date Time', 'B', 0, 'L');        
        $pdf->Cell(40, 0, 'Type', 'B', 0, 'L');        
        $pdf->Cell(65, 0, 'Employee Name', 'B', 0, 'L');        
        $pdf->Cell(32, 0, 'Emp Code', 'B', 0, 'L');        
        $pdf->ln();                  

        // GRAND TOTAL VARIABLES
        $counter = 0;
        $progress = 0;
        $trans_count = count($trans);        
              
        foreach ($trans as $k => $v) {
            $pdf->Cell(43, 0, $v->datetime, '', 0, 'L');        
            $pdf->Cell(40, 0, $v->type, '', 0, 'L');        
            $pdf->Cell(65, 0, $v->fname. " ".$v->mname." ".$v->lname, '', 0, 'L');        
            $pdf->Cell(32, 0, $v->username, '', 0, 'L');                    
            $pdf->ln();                

            $counter++;
            $progress = ($counter / $trans_count) * 100;
            update_load(num($progress));              
        }
        update_load(100);        
        $pdf->Cell(180, 0, "Total Hours", 'T', 0, 'L');
        $pdf->ln();
        foreach ($total_hours as $k => $v) {                
            $pdf->Cell(43, 0, $v["fname"]. " ".$v["mname"]." ".$v["lname"], '', 0, 'L');        
            $pdf->Cell(40, 0, $v["time_to_sec"], '', 0, 'L');                    
            $pdf->ln();                
        }        

        // -----------------------------------------------------------------------------

        //Close and output PDF document
        $pdf->Output('dtr_report.pdf', 'I');

        //============================================================+
        // END OF FILE
        //============================================================+   
    }
    // End of New Report


    //New Hourly Sales Jed
    public function hourly_sales_rep_gen(){
         $this->load->helper('dine/reports_helper');
        $json = $this->input->post('json');

        $branch_id = $this->input->post("branch_id");        
        $setup = $this->setup_model->get_details($branch_id);
        $set = $setup[0];
        start_load(0);
        $menu_cat_id = $this->input->post("menu_cat_id");        
        $daterange = $this->input->post("calendar_range");
        $dates = explode(" to ",$daterange);
        $from = date2SqlDateTime($dates[0]." ".$set->store_open);
        $to_plus = date('Y-m-d',strtotime($dates[1] . "+1 days"));
        $to = date2SqlDateTime($to_plus." ".$set->store_open);


        // echo $from.' -- '.$to;
        // die();

        $args = array();
        // if($user)
        // echo "<pre>",print_r($branch_id),"</pre>";die();
        //     $args["trans_sales.user_id"] = array('use'=>'where','val'=>$user,'third'=>false);

        $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.inactive = 0"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);

        if($menu_cat_id != 0){
            $args["menu_categories.menu_cat_id"] = array('use'=>'where','val'=>$menu_cat_id,'third'=>false);
        }
        if($branch_id !=''){
            $args += array('trans_sales.branch_code'=>$branch_id);
        }

        $post = $this->set_post();
        $curr = $this->search_current();
        // echo "<pre>",print_r($args),"<pre>";die();
        $trans = $this->trans_sales_cat($args,$curr);
        $sales = $trans['sales'];

        // $payments = $this->payment_sales($sales['settled']['ids'],$curr);
        // var_dump($sales); die();
        // $get_trans = $this->cashier_model->get_trans_sales(null,$args,'asc');
        // $unserialize = unserialize(TIMERANGES);
        // var_dump($unserialize); die();
        update_load(10);
        $ranges = array();
        foreach (unserialize(TIMERANGES) as $ctr => $time) {
            $key = date('H',strtotime($time['FTIME']));
            $ranges[$key] = array('start'=>$time['FTIME'],'end'=>$time['TTIME'],'tc'=>0,'net'=>0,'tg'=>0,'gross'=>0,'charges'=>0,'discounts'=>0,'vsales'=>0,'vat'=>0);
            // $ranges[$key] = array();
        }

        // echo "<pre>",print_r($ranges),"</pre>";
        // die();
        $dates = array();
        if(count($sales['settled']['orders']) > 0){
            foreach ($sales['settled']['orders'] as $sales_id => $val) {
                $dates[date2Sql($val->datetime)]['ranges'] = $ranges;
            }
            // $this->cashier_model->db = $this->load->database('default', TRUE);
            // echo "<pre>",print_r($sales['settled']['orders']),"</pre>";die();
            foreach ($sales['settled']['orders'] as $sales_id => $val) {
                if(isset($dates[date2Sql($val->datetime)])){
                    $date_arr = $dates[date2Sql($val->datetime)];
                    $range = $date_arr['ranges'];
                    $H = date('H',strtotime($val->datetime));
                    if(isset($range[$H])){
                        // $discnt = 0;
                        // $dargs["trans_sales_discounts.sales_id"] = array('use'=>'where','val'=>$val->sales_id,'third'=>false);
                        // $d_res  = $this->cashier_model->get_trans_sales_discounts_group2(null,$dargs);
                        // // var_dump($d_res);
                        // if($d_res){
                        //     $discnt = $d_res[0]->total;
                        //     // echo $discnt;
                        // }
                        // $chrgs = 0;
                        // $cargs["trans_sales_charges.sales_id"] = array('use'=>'where','val'=>$val->sales_id,'third'=>false);
                        // $c_res  = $this->cashier_model->get_trans_sales_charges(null,$cargs);
                        // // echo $this->cashier_model->db->last_query();
                        // if($c_res){
                        //     $chrgs = $c_res[0]->amount;
                        //     // echo $chrgs;
                        // }

                        $disc = array("trans_sales_discounts.sales_id"=>$sales_id);
                        $tax = array("trans_sales_tax.sales_id"=>$sales_id);
                        $no_tax = array("trans_sales_no_tax.sales_id"=>$sales_id);
                        $zero_rated = array("trans_sales_zero_rated.sales_id"=>$sales_id);
                        $charges = array("trans_sales_charges.sales_id"=>$sales_id);
                        $local_tax = array("trans_sales_local_tax.sales_id"=>$sales_id);

                        if($branch_id != ''){
                            $disc['trans_sales_discounts.branch_code'] = $branch_id;
                            $tax['trans_sales_tax.branch_code'] = $branch_id;
                            $no_tax['trans_sales_no_tax.branch_code'] = $branch_id;
                            $zero_rated['trans_sales_zero_rated.branch_code'] = $branch_id;
                            $charges['trans_sales_charges.branch_code'] = $branch_id;
                            $local_tax['trans_sales_local_tax.branch_code'] = $branch_id;
                        }
                        
                        $sales_discs = $this->cashier_model->get_trans_sales_discounts(null,$disc);
                        $sales_tax = $this->cashier_model->get_trans_sales_tax(null,$tax);
                        $sales_no_tax = $this->cashier_model->get_trans_sales_no_tax(null,$no_tax);
                        $sales_zero_rated = $this->cashier_model->get_trans_sales_zero_rated(null,$zero_rated);
                        $sales_charges = $this->cashier_model->get_trans_sales_charges(null,$charges);
                        $sales_local_tax = $this->cashier_model->get_trans_sales_local_tax(null,$local_tax);

                        $discnt = 0;
                        $total_discounts_non_vat = 0;
                        foreach($sales_discs as $dc){
                            $discnt += $dc->amount;
                            if($dc->no_tax == 1){
                                $total_discounts_non_vat += $dc->amount;
                            }
                        }

                        $tax = 0;
                        foreach ($sales_tax as $tx) {
                            $tax += $tx->amount;
                        }
                        $no_tax = 0;
                        foreach ($sales_no_tax as $nt) {
                            $no_tax += $nt->amount;
                        }
                        $zero_rated = 0;
                        foreach ($sales_zero_rated as $zt) {
                            $zero_rated += $zt->amount;
                        }

                        if($zero_rated > 0){
                            $no_tax = 0;
                        }


                        $local_tax = 0;
                        foreach ($sales_local_tax as $lt) {
                            $local_tax += $lt->amount;
                        }
                        $chrgs = 0;
                        foreach ($sales_charges as $ch) {
                            $chrgs += $ch->amount;
                        }


                        $vat_sales = ( ( ( $val->total_amount - ($chrgs + $local_tax) ) - $tax)  - $no_tax + $total_discounts_non_vat ) - $zero_rated;

                        $r = $range[$H];
                        $r['tc'] += 1;
                        $r['net'] += $val->total_amount;
                        $r['gross'] += $val->menu_total;
                        $r['charges'] += $chrgs;
                        $r['discounts'] += $discnt;
                        $r['vsales'] += $vat_sales;
                        $r['vat'] += $tax;
                        $r['tg'] += $val->guest;
                        $range[$H] = $r;
                    }
                    $dates[date2Sql($val->datetime)]['ranges'] = $range;
                }
            }

            update_load(40);
            
            // $this->print_drawer_details($get_trans,$date,$user,$asjson);
            $this->make->sDiv();
                $this->make->sTable(array('id'=>'main-tbl','class'=>'table'));
                    $this->make->sTableHead();
                        $this->make->sRow(array('class'=>'reportTBL'));
                            $this->make->th('Time');
                            $this->make->th('Guest Count');
                            $this->make->th('Gross Sales');
                            $this->make->th('Vat Sales');
                            $this->make->th('VAT');
                            $this->make->th('Charges');
                            $this->make->th('Discounts');
                            $this->make->th('Average(%)');
                        $this->make->eRow();
                    $this->make->eTableHead();
                    $this->make->sTableBody();
                    $txt = '';
                    
                    $ctr = 0;
                    $gtavg = $gtctr = $gtnet = 0;
                    foreach($dates as $key1 => $v1){
                        $this->make->sRow(array('class'=>''));
                            $this->make->th(sql2Date($key1),array('colspan'=>8,'style'=>'text-align:center;'));
                        $this->make->eRow();

                        $ranges = $v1['ranges'];
                        //$txt .= sql2Date($key1);
                        $tavg = 0;
                        $tctr = 0;
                        $tnet = 0;
                        $tgc = 0;
                        $tdisc = 0;
                        $tcharges = 0;
                        $tvsales = 0;
                        $ttax = 0;
                        $tgross = 0;
                        $counter = 0;
                        $rcount = count($ranges);
                        foreach($ranges as $key2 => $ran){
                            if($ran['tc'] == 0 || $ran['net'] == 0)
                                $avg = 0;
                            else
                                $avg = $ran['net']/$ran['tc'];
                            $ctr += $ran['tc'];

                            $this->make->sRow();
                                // $this->make->th($ran['start']."-".$ran['end']);
                                $this->make->th(date('h:i A',strtotime($ran['start'])));
                                $this->make->th($ran['tg']);
                                $this->make->th(numInt($ran['gross']), array("style"=>"text-align:right"));
                                $this->make->th(numInt($ran['vsales']), array("style"=>"text-align:right"));
                                $this->make->th(numInt($ran['vat']), array("style"=>"text-align:right"));
                                $this->make->th(numInt($ran['charges']), array("style"=>"text-align:right"));
                                $this->make->th(numInt($ran['discounts']), array("style"=>"text-align:right"));
                                $this->make->th(numInt($avg), array("style"=>"text-align:right"));
                            $this->make->eRow();
                            $tctr += $ran['tc'];
                            $tnet += $ran['net'];
                            $tgc += $ran['tg'];
                            $tdisc += $ran['discounts'];
                            $tcharges += $ran['charges'];
                            $tvsales += $ran['vsales'];
                            $ttax += $ran['vat'];
                            $tgross += $ran['gross'];
                            // if($ctr == 0 || $ran['net'] == 0)
                            //     $tavg = 0;
                            // else
                            //     $tavg += $ran['net']/$ctr;

                            $counter++;
                            $progress = ($counter / $rcount) * 100;
                            // update_load(num($progress));

                        }
                        update_load(80);
                        $gtctr += $tctr;
                        $gtnet += $tnet;

                    }
                    $gtavg = $gtnet/$gtctr;
                    $this->make->sRow(array('class'=>'reportTBL'));
                        $this->make->th('TOTAL');
                        $this->make->th($tgc);
                        $this->make->th(numInt($tgross), array("style"=>"text-align:right"));
                        $this->make->th(numInt($tvsales), array("style"=>"text-align:right"));
                        $this->make->th(numInt($ttax), array("style"=>"text-align:right"));
                        $this->make->th(numInt($tcharges), array("style"=>"text-align:right"));
                        $this->make->th(numInt($tdisc), array("style"=>"text-align:right"));
                        $this->make->th(numInt($gtavg), array("style"=>"text-align:right"));
                        // $this->make->th(numInt($gtavg));
                    $this->make->eRow();
                    $this->make->eTableBody();
                $this->make->eTable();
            $this->make->eDiv();

            update_load(100);

            // echo $code;
            // echo json_encode(array("code"=>$code));
            $code = $this->make->code();
            $json['code'] = $code;
            // $json['tbl_vals'] = $menus;
            // $json['dates'] = $this->input->post('calendar_range');
            echo json_encode($json);
            
        }
        else{
            update_load(100);
            $error = "There is no sales found.";
            echo json_encode(array("code"=>"<pre style='background-color:#fff'>$error</pre>"));
        }
        // var_dump($dates);
    }

     public function hourly_sales_rep_pdf()
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
        $pdf->SetTitle('Hourly Sales Report');
        $pdf->SetSubject('');
        $pdf->SetKeywords('');

        // set default header data
        $branch_id = $_GET['branch_id'];             
        $setup = $this->setup_model->get_details($branch_id);
        $set = $setup[0];
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "BARCINO RETAIL CORPORATION", $set->address);

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

        $this->load->database('default', TRUE);
        $this->load->model("dine/menu_model");
        start_load(0);

        // set font
        $pdf->SetFont('helvetica', 'B', 11);

        // add a page
        $pdf->AddPage();
        $menu_cat_id = $_GET['menu_cat_id'];        
        $daterange = $_GET['calendar_range'];        
        $dates = explode(" to ",$daterange);
        $from = date2SqlDateTime($dates[0]." ".$set->store_open);
        $to_plus = date('Y-m-d',strtotime($dates[1] . "+1 days"));
        $to = date2SqlDateTime($to_plus." ".$set->store_open);
        $trans_payment = $this->menu_model->get_payment_date($from, $to);


        // echo $from.' -- '.$to;
        // die();
        $pdf->Write(0, 'Hourly Sales Report', '', 0, 'L', true, 0, false, false, 0);
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

        // echo "<pre>", print_r($trans), "</pre>";die();

        // -----------------------------------------------------------------------------
        $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => 'black'));
        $pdf->Cell(43, 0, 'Time', 'B', 0, 'L');        
        $pdf->Cell(32, 0, 'Guest Count', 'B', 0, 'L');        
        $pdf->Cell(32, 0, 'Gross Sales', 'B', 0, 'C');        
        $pdf->Cell(32, 0, 'VAT Sales', 'B', 0, 'C');        
        $pdf->Cell(32, 0, 'VAT', 'B', 0, 'C');        
        $pdf->Cell(32, 0, 'Charges', 'B', 0, 'C');        
        $pdf->Cell(32, 0, 'Discounts', 'B', 0, 'C');       
        $pdf->Cell(32, 0, 'Average (%)', 'B', 0, 'C');        
        // $pdf->Cell(24, 0, 'Average (%)', 'B', 0, 'C');        
        $pdf->ln();                  

        $args = array();
        // if($user)
        //     $args["trans_sales.user_id"] = array('use'=>'where','val'=>$user,'third'=>false);

        $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.inactive = 0"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);

        if($menu_cat_id != 0){
            $args["menu_categories.menu_cat_id"] = array('use'=>'where','val'=>$menu_cat_id,'third'=>false);
        }        
        if($branch_id !=''){
            // $args["trans_sales.branch_code = ".$branch_id] = array('use'=>'where','val'=>null,'third'=>false);
            $args += array('trans_sales.branch_code'=>$branch_id);
        }
        $post = $this->set_post();
        $curr = $this->search_current();
        $trans = $this->trans_sales_cat($args,$curr);
        $sales = $trans['sales'];

        $payments = $this->payment_sales($sales['settled']['ids'],$curr);

        // var_dump($sales); die();
        // $get_trans = $this->cashier_model->get_trans_sales(null,$args,'asc');
        // $unserialize = unserialize(TIMERANGES);
        // var_dump($unserialize); die();

        $ranges = array();
        foreach (unserialize(TIMERANGES) as $ctr => $time) {
            $key = date('H',strtotime($time['FTIME']));
            $ranges[$key] = array('start'=>$time['FTIME'],'end'=>$time['TTIME'],'tc'=>0,'net'=>0,'tg'=>0,'gross'=>0,'charges'=>0,'discounts'=>0,'vsales'=>0,'vat'=>0);
            // $ranges[$key] = array();
        }

        update_load(20);

        // echo "<pre>",print_r($ranges),"</pre>";
        // die();
        $dates = array();
        if(count($sales['settled']['orders']) > 0){
            foreach ($sales['settled']['orders'] as $sales_id => $val) {
                $dates[date2Sql($val->datetime)]['ranges'] = $ranges;
            }
            // $this->cashier_model->db = $this->load->database('default', TRUE);
            foreach ($sales['settled']['orders'] as $sales_id => $val) {
                if(isset($dates[date2Sql($val->datetime)])){
                    $date_arr = $dates[date2Sql($val->datetime)];
                    $range = $date_arr['ranges'];
                    $H = date('H',strtotime($val->datetime));
                    if(isset($range[$H])){
                        // $discnt = 0;
                        // $dargs["trans_sales_discounts.sales_id"] = array('use'=>'where','val'=>$val->sales_id,'third'=>false);
                        // $d_res  = $this->cashier_model->get_trans_sales_discounts_group2(null,$dargs);
                        // // var_dump($d_res);
                        // if($d_res){
                        //     $discnt = $d_res[0]->total;
                        //     // echo $discnt;
                        // }
                        // $chrgs = 0;
                        // $cargs["trans_sales_charges.sales_id"] = array('use'=>'where','val'=>$val->sales_id,'third'=>false);
                        // $c_res  = $this->cashier_model->get_trans_sales_charges(null,$cargs);
                        // // echo $this->cashier_model->db->last_query();
                        // if($c_res){
                        //     $chrgs = $c_res[0]->amount;
                        //     // echo $chrgs;
                        // }

                        $disc = array("trans_sales_discounts.sales_id"=>$sales_id);
                        $tax = array("trans_sales_tax.sales_id"=>$sales_id);
                        $no_tax = array("trans_sales_no_tax.sales_id"=>$sales_id);
                        $zero_rated = array("trans_sales_zero_rated.sales_id"=>$sales_id);
                        $charges = array("trans_sales_charges.sales_id"=>$sales_id);
                        $local_tax = array("trans_sales_local_tax.sales_id"=>$sales_id);

                        if($branch_id != ''){
                            $disc['trans_sales_discounts.branch_code'] = $branch_id;
                            $tax['trans_sales_tax.branch_code'] = $branch_id;
                            $no_tax['trans_sales_no_tax.branch_code'] = $branch_id;
                            $zero_rated['trans_sales_zero_rated.branch_code'] = $branch_id;
                            $charges['trans_sales_charges.branch_code'] = $branch_id;
                            $local_tax['trans_sales_local_tax.branch_code'] = $branch_id;
                        }

                        $sales_discs = $this->cashier_model->get_trans_sales_discounts(null,$disc);
                        $sales_tax = $this->cashier_model->get_trans_sales_tax(null,$tax);
                        $sales_no_tax = $this->cashier_model->get_trans_sales_no_tax(null,$no_tax);
                        $sales_zero_rated = $this->cashier_model->get_trans_sales_zero_rated(null,$zero_rated);
                        $sales_charges = $this->cashier_model->get_trans_sales_charges(null,$charges);
                        $sales_local_tax = $this->cashier_model->get_trans_sales_local_tax(null,$local_tax);

                        $discnt = 0;
                        $total_discounts_non_vat = 0;
                        foreach($sales_discs as $dc){
                            $discnt += $dc->amount;
                            if($dc->no_tax == 1){
                                $total_discounts_non_vat += $dc->amount;
                            }
                        }

                        $tax = 0;
                        foreach ($sales_tax as $tx) {
                            $tax += $tx->amount;
                        }
                        $no_tax = 0;
                        foreach ($sales_no_tax as $nt) {
                            $no_tax += $nt->amount;
                        }
                        $zero_rated = 0;
                        foreach ($sales_zero_rated as $zt) {
                            $zero_rated += $zt->amount;
                        }

                        if($zero_rated > 0){
                            $no_tax = 0;
                        }


                        $local_tax = 0;
                        foreach ($sales_local_tax as $lt) {
                            $local_tax += $lt->amount;
                        }
                        $chrgs = 0;
                        foreach ($sales_charges as $ch) {
                            $chrgs += $ch->amount;
                        }


                        $vat_sales = ( ( ( $val->total_amount - ($chrgs + $local_tax) ) - $tax)  - $no_tax + $total_discounts_non_vat ) - $zero_rated;

                        $r = $range[$H];
                        $r['tc'] += 1;
                        $r['net'] += $val->total_amount;
                        $r['gross'] += $val->menu_total;
                        $r['charges'] += $chrgs;
                        $r['discounts'] += $discnt;
                        $r['vsales'] += $vat_sales;
                        $r['vat'] += $tax;
                        $r['tg'] += $val->guest;
                        $range[$H] = $r;
                    }
                    $dates[date2Sql($val->datetime)]['ranges'] = $range;
                }
            }

            update_load(30);

            $ctr = 0;
            $gtavg = $gtctr = $gtnet = 0;
            $strt = "";
            foreach($dates as $key1 => $v1){
                // $this->make->sRow(array('class'=>''));
                //     $this->make->th(sql2Date($key1),array('colspan'=>8,'style'=>'text-align:center;'));
                // $this->make->eRow();

                if($strt == ""){
                    $pdf->ln(2);
                    $pdf->Cell(267, 0, sql2Date($key1), '', 0, 'C');
                    $pdf->ln(5);
                }else{
                    $pdf->AddPage();
                    $pdf->SetFont('helvetica', 'B', 11);
                    $pdf->Write(0, 'Hourly Sales Report', '', 0, 'L', true, 0, false, false, 0);
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

                    // echo "<pre>", print_r($trans), "</pre>";die();

                    // -----------------------------------------------------------------------------
                    $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => 'black'));
                    $pdf->Cell(43, 0, 'Time', 'B', 0, 'L');        
                    $pdf->Cell(32, 0, 'Guest Count', 'B', 0, 'L');        
                    $pdf->Cell(32, 0, 'Gross Sales', 'B', 0, 'C');        
                    $pdf->Cell(32, 0, 'VAT Sales', 'B', 0, 'C');        
                    $pdf->Cell(32, 0, 'VAT', 'B', 0, 'C');        
                    $pdf->Cell(32, 0, 'Charges', 'B', 0, 'C');        
                    $pdf->Cell(32, 0, 'Discounts', 'B', 0, 'C');       
                    $pdf->Cell(32, 0, 'Average (%)', 'B', 0, 'C');        
                    // $pdf->Cell(24, 0, 'Average (%)', 'B', 0, 'C');        
                    $pdf->ln();

                    $pdf->ln(2);
                    $pdf->Cell(267, 0, sql2Date($key1), '', 0, 'C');
                    $pdf->ln(3);
                }

                update_load(40);


                $ranges = $v1['ranges'];
                //$txt .= sql2Date($key1);
                $tavg = 0;
                $tctr = 0;
                $tnet = 0;
                $tgc = 0;
                $tdisc = 0;
                $tcharges = 0;
                $tvsales = 0;
                $ttax = 0;
                $tgross = 0;
                $counter = 0;
                $rcount = count($ranges);
                foreach($ranges as $key2 => $ran){
                    if($ran['tc'] == 0 || $ran['net'] == 0)
                        $avg = 0;
                    else
                        $avg = $ran['net']/$ran['tc'];
                    $ctr += $ran['tc'];

                    // $this->make->sRow();
                    //     // $this->make->th($ran['start']."-".$ran['end']);
                    //     $this->make->th(date('h:i A',strtotime($ran['start'])));
                    //     $this->make->th($ran['tg']);
                    //     $this->make->th(numInt($ran['gross']), array("style"=>"text-align:right"));
                    //     $this->make->th(numInt($ran['vsales']), array("style"=>"text-align:right"));
                    //     $this->make->th(numInt($ran['vat']), array("style"=>"text-align:right"));
                    //     $this->make->th(numInt($ran['charges']), array("style"=>"text-align:right"));
                    //     $this->make->th(numInt($ran['discounts']), array("style"=>"text-align:right"));
                    //     $this->make->th(numInt($avg), array("style"=>"text-align:right"));
                    // $this->make->eRow();

                    // $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => 'black'));
                    $pdf->Cell(43, 0, date('h:i A',strtotime($ran['start'])), '', 0, 'L');        
                    $pdf->Cell(32, 0, $ran['tg'], '', 0, 'L');        
                    $pdf->Cell(32, 0, num($ran['gross']), '', 0, 'R');        
                    $pdf->Cell(32, 0, num($ran['vsales']), '', 0, 'R');        
                    $pdf->Cell(32, 0, num($ran['vat']), '', 0, 'R');        
                    $pdf->Cell(32, 0, num($ran['charges']), '', 0, 'R');        
                    $pdf->Cell(32, 0, num($ran['discounts']), '', 0, 'R');        
                    $pdf->Cell(32, 0, num($avg), '', 0, 'R');        
                    
                    $pdf->ln();  



                    $tctr += $ran['tc'];
                    $tnet += $ran['net'];
                    $tgc += $ran['tg'];
                    $tdisc += $ran['discounts'];
                    $tcharges += $ran['charges'];
                    $tvsales += $ran['vsales'];
                    $ttax += $ran['vat'];
                    $tgross += $ran['gross'];
                    $tavg += $avg;
                    // if($ctr == 0 || $ran['net'] == 0)
                    //     $tavg = 0;
                    // else
                    //     $tavg += $ran['net']/$ctr;

                    $counter++;
                    $progress = ($counter / $rcount) * 100;
                    // update_load(num($progress));

                }

                update_load(70);
                $gtctr += $tctr;
                $gtnet += $tnet;
                $pdf->ln(3);

                $pdf->Cell(43, 0, 'TOTAL', 'T', 0, 'L');        
                $pdf->Cell(32, 0, $tgc, 'T', 0, 'L');        
                $pdf->Cell(32, 0, num($tgross), 'T', 0, 'R');        
                $pdf->Cell(32, 0, num($tvsales), 'T', 0, 'R');        
                $pdf->Cell(32, 0, num($ttax), 'T', 0, 'R');        
                $pdf->Cell(32, 0, num($tcharges), 'T', 0, 'R');        
                $pdf->Cell(32, 0, num($tdisc), 'T', 0, 'R');        
                $pdf->Cell(32, 0, num($tavg), 'T', 0, 'R');

                $strt = $key1;

                // $pdf->ln(10);
                // foreach ($trans_payment as $key => $val) {
                // $pdf->ln();
                //     $pdf->Cell(25, 0, strtoupper($val->payment_type), '', 0, 'L');
                //     if($val->payment_type == 'gc' || $val->payment_type == 'credit' ){        
                //         $pdf->Cell(15, 0, num($val->total_amount_gc), '', 0, 'R');
                //     }else{
                //         $pdf->Cell(15, 0, num($val->total_to_pay), '', 0, 'R');
                //     }

                // } 

            }

            update_load(90);

            // $payments_types = $payments['types'];
            // $payments_total = $payments['total'];
            // $pdf->ln(6);
            // // $pays = array();
            // // $total = 0;
            // // foreach ($payments_types as $py) {
            // //     // if(!in_array($py->sales_id, $ids_used)){
            // //     //     $ids_used[] = $py->sales_id;
            // //     // }
            // //     if($py->amount > $py->to_pay)
            // //         $amount = $py->to_pay;
            // //     else
            // //         $amount = $py->amount;
            // //     if(!isset($pays[$py->payment_type])){
            // //         $pays[$py->payment_type] = array('qty'=>1,'amount'=>$amount);
            // //     }
            // //     else{
            // //         $pays[$py->payment_type]['qty'] += 1;
            // //         $pays[$py->payment_type]['amount'] += $amount;
            // //     }
            // //     $total += $amount;
            // // }

            // $pdf->SetFont('helvetica', 'B', 9);
            // $pdf->Cell(30, 0, strtoupper('Payment Mode'), '', 0, 'L');
            // $pdf->Cell(35, 0, strtoupper('Payment Amount'), '', 0, 'C');
            // $pdf->SetFont('helvetica', '', 9);
            // // $pdf->ln();

            // foreach ($payments_types as $code => $val) {
            // $pdf->ln();
            //     $pdf->Cell(25, 0, strtoupper($code), '', 0, 'L');
            //     $pdf->Cell(35, 0, num($val['amount']), '', 0, 'R');
            //     // $print_str .= append_chars(substrwords(ucwords(strtoupper($code)),18,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
            //                   // .append_chars(numInt($val['amount']),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //     // $pay_qty += $val['qty'];
            // }
            //     // $pdf->SetLineStyle(array('width' => 0.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => 'black'));
            // // $pdf->ln();
            // // $pdf->Cell(60, 0, '', 'T', 0, 'C');
            // $pdf->ln();
            // $pdf->SetFont('helvetica', 'B', 9);
            // $pdf->Cell(25, 0,'TOTAL ', 'T', 0, 'L');
            // $pdf->SetFont('helvetica', '', 9);
            // $pdf->Cell(35, 0, num($payments_total), 'T', 0, 'R');


        }else{
            $pdf->ln(2);
                $pdf->Cell(267, 0, 'No Sales Found', '', 0, 'C');
            $pdf->ln(5);
        }

        update_load(100);
        //Close and output PDF document
        $pdf->Output('hourly_report.pdf', 'I');

        //============================================================+
        // END OF FILE
        //============================================================+   
    }


    public function hourly_sales_rep_excel(){
        date_default_timezone_set('Asia/Manila');
        $this->load->library('Excel');
        $sheet = $this->excel->getActiveSheet();
        $filename = 'Hourly Sales Report';
        $rc=1;
        #GET VALUES
        start_load(0);
            // $post = $this->set_post($_GET['calendar_range']);
        $branch_id = $_GET['branch_id'];        
        $setup = $this->setup_model->get_details($branch_id);
        $set = $setup[0];
        $menu_cat_id = $_GET['menu_cat_id'];        
        $daterange = $_GET['calendar_range'];        
        $dates = explode(" to ",$daterange);
        $from = date2SqlDateTime($dates[0]." ".$set->store_open);
        $to_plus = date('Y-m-d',strtotime($dates[1] . "+1 days"));
        $to = date2SqlDateTime($to_plus." ".$set->store_open);
        $trans_payment = $this->menu_model->get_payment_date($from, $to);

        update_load(10);
        sleep(1);

        $args = array();
        // if($user)
        //     $args["trans_sales.user_id"] = array('use'=>'where','val'=>$user,'third'=>false);

        $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.inactive = 0"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);

        if($menu_cat_id != 0){
            $args["menu_categories.menu_cat_id"] = array('use'=>'where','val'=>$menu_cat_id,'third'=>false);
        }        
        if($branch_id !=''){
            $args += array('trans_sales.branch_code'=>$branch_id);
        }

        $post = $this->set_post();
        $curr = $this->search_current();
        $trans = $this->trans_sales_cat($args,$curr);
        $sales = $trans['sales'];

        $payments = $this->payment_sales($sales['settled']['ids'],$curr);

        // $get_trans = $this->cashier_model->get_trans_sales(null,$args,'asc');

        update_load(15);
        sleep(1);

        $ranges = array();
        foreach (unserialize(TIMERANGES) as $ctr => $time) {
            $key = date('H',strtotime($time['FTIME']));
            $ranges[$key] = array('start'=>$time['FTIME'],'end'=>$time['TTIME'],'tc'=>0,'net'=>0,'tg'=>0,'gross'=>0,'charges'=>0,'discounts'=>0,'vsales'=>0,'vat'=>0);
            // $ranges[$key] = array();
        }

        update_load(20);
        sleep(1);
        // echo "<pre>",print_r($ranges),"</pre>";
        // die();
        $dates = array();
        if(count($sales['settled']['orders']) > 0){
            foreach ($sales['settled']['orders'] as $sales_id => $val) {
                $dates[date2Sql($val->datetime)]['ranges'] = $ranges;
            }
            // $this->cashier_model->db = $this->load->database('default', TRUE);
            foreach ($sales['settled']['orders'] as $sales_id => $val) {
                if(isset($dates[date2Sql($val->datetime)])){
                    $date_arr = $dates[date2Sql($val->datetime)];
                    $range = $date_arr['ranges'];
                    $H = date('H',strtotime($val->datetime));
                    if(isset($range[$H])){
                        // $discnt = 0;
                        // $dargs["trans_sales_discounts.sales_id"] = array('use'=>'where','val'=>$val->sales_id,'third'=>false);
                        // $d_res  = $this->cashier_model->get_trans_sales_discounts_group2(null,$dargs);
                        // // var_dump($d_res);
                        // if($d_res){
                        //     $discnt = $d_res[0]->total;
                        //     // echo $discnt;
                        // }
                        // $chrgs = 0;
                        // $cargs["trans_sales_charges.sales_id"] = array('use'=>'where','val'=>$val->sales_id,'third'=>false);
                        // $c_res  = $this->cashier_model->get_trans_sales_charges(null,$cargs);
                        // // echo $this->cashier_model->db->last_query();
                        // if($c_res){
                        //     $chrgs = $c_res[0]->amount;
                        //     // echo $chrgs;
                        // }

                        $disc = array("trans_sales_discounts.sales_id"=>$sales_id);
                        $tax = array("trans_sales_tax.sales_id"=>$sales_id);
                        $no_tax = array("trans_sales_no_tax.sales_id"=>$sales_id);
                        $zero_rated = array("trans_sales_zero_rated.sales_id"=>$sales_id);
                        $charges = array("trans_sales_charges.sales_id"=>$sales_id);
                        $local_tax = array("trans_sales_local_tax.sales_id"=>$sales_id);

                        if($branch_id != ''){
                            $disc['trans_sales_discounts.branch_code'] = $branch_id;
                            $tax['trans_sales_tax.branch_code'] = $branch_id;
                            $no_tax['trans_sales_no_tax.branch_code'] = $branch_id;
                            $zero_rated['trans_sales_zero_rated.branch_code'] = $branch_id;
                            $charges['trans_sales_charges.branch_code'] = $branch_id;
                            $local_tax['trans_sales_local_tax.branch_code'] = $branch_id;
                        }

                        $sales_discs = $this->cashier_model->get_trans_sales_discounts(null,$disc);
                        $sales_tax = $this->cashier_model->get_trans_sales_tax(null,$tax);
                        $sales_no_tax = $this->cashier_model->get_trans_sales_no_tax(null,$no_tax);
                        $sales_zero_rated = $this->cashier_model->get_trans_sales_zero_rated(null,$zero_rated);
                        $sales_charges = $this->cashier_model->get_trans_sales_charges(null,$charges);
                        $sales_local_tax = $this->cashier_model->get_trans_sales_local_tax(null,$local_tax);

                        $discnt = 0;
                        $total_discounts_non_vat = 0;
                        foreach($sales_discs as $dc){
                            $discnt += $dc->amount;
                            if($dc->no_tax == 1){
                                $total_discounts_non_vat += $dc->amount;
                            }
                        }

                        $tax = 0;
                        foreach ($sales_tax as $tx) {
                            $tax += $tx->amount;
                        }
                        $no_tax = 0;
                        foreach ($sales_no_tax as $nt) {
                            $no_tax += $nt->amount;
                        }
                        $zero_rated = 0;
                        foreach ($sales_zero_rated as $zt) {
                            $zero_rated += $zt->amount;
                        }

                        if($zero_rated > 0){
                            $no_tax = 0;
                        }


                        $local_tax = 0;
                        foreach ($sales_local_tax as $lt) {
                            $local_tax += $lt->amount;
                        }
                        $chrgs = 0;
                        foreach ($sales_charges as $ch) {
                            $chrgs += $ch->amount;
                        }


                        $vat_sales = ( ( ( $val->total_amount - ($chrgs + $local_tax) ) - $tax)  - $no_tax + $total_discounts_non_vat ) - $zero_rated;

                        $r = $range[$H];
                        $r['tc'] += 1;
                        $r['net'] += $val->total_amount;
                        $r['gross'] += $val->menu_total;
                        $r['charges'] += $chrgs;
                        $r['discounts'] += $discnt;
                        $r['vsales'] += $vat_sales;
                        $r['vat'] += $tax;
                        $r['tg'] += $val->guest;
                        $range[$H] = $r;
                    }
                    $dates[date2Sql($val->datetime)]['ranges'] = $range;
                }
            }

            update_load(30);
            sleep(1);

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
            
            $headers = array('Time','Guest Count','Gross Sales','Vat Sales','VAT','Charges','Discounts','Average %');
            $sheet->getColumnDimension('A')->setWidth(20);
            $sheet->getColumnDimension('B')->setWidth(20);
            $sheet->getColumnDimension('C')->setWidth(20);
            $sheet->getColumnDimension('D')->setWidth(20);
            $sheet->getColumnDimension('E')->setWidth(20);
            $sheet->getColumnDimension('F')->setWidth(20);
            $sheet->getColumnDimension('G')->setWidth(20);
            $sheet->getColumnDimension('H')->setWidth(20);


            $sheet->mergeCells('A'.$rc.':H'.$rc);
            $sheet->getCell('A'.$rc)->setValue('BARCINO RETAIL CORPORATION');
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
            $rc++;

            $sheet->mergeCells('A'.$rc.':H'.$rc);
            $sheet->getCell('A'.$rc)->setValue($set->address);
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
            $rc++;

            $sheet->mergeCells('A'.$rc.':H'.$rc);
            $sheet->getCell('A'.$rc)->setValue('Hourly Sales Report');
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $rc++;

            $sheet->mergeCells('A'.$rc.':D'.$rc);
            $sheet->getCell('A'.$rc)->setValue('Report Period: '.$daterange);
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->mergeCells('E'.$rc.':H'.$rc);
            $sheet->getCell('E'.$rc)->setValue('Report Generated: '.(new \DateTime())->format('Y-m-d H:i:s'));
            $sheet->getStyle('E'.$rc)->applyFromArray($styleNum);
            $rc++;

            $sheet->mergeCells('A'.$rc.':D'.$rc);
            $sheet->getCell('A'.$rc)->setValue('Transaction Time:');
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $user = $this->session->userdata('user');
            $sheet->mergeCells('E'.$rc.':H'.$rc);
            $sheet->getCell('E'.$rc)->setValue('Generated by:    '.$user["full_name"]);
            $sheet->getStyle('E'.$rc)->applyFromArray($styleNum);
            $rc++;

            $col = 'A';
            foreach ($headers as $txt) {
                $sheet->getCell($col.$rc)->setValue($txt);
                $sheet->getStyle($col.$rc)->applyFromArray($styleHeaderCell);
                $col++;
            }
            $rc++;
            $ctr = 0;
            $gtavg = $gtctr = $gtnet = 0;
            $strt = "";

            foreach($dates as $key1 => $v1){

                $sheet->mergeCells('A'.$rc.':H'.$rc);
                $sheet->getCell('A'.$rc)->setValue(sql2Date($key1));
                $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
                $rc++;


                $ranges = $v1['ranges'];
                //$txt .= sql2Date($key1);
                $tavg = 0;
                $tctr = 0;
                $tnet = 0;
                $tgc = 0;
                $tdisc = 0;
                $tcharges = 0;
                $tvsales = 0;
                $ttax = 0;
                $tgross = 0;
                $counter = 0;
                $rcount = count($ranges);
                foreach($ranges as $key2 => $ran){
                    if($ran['tc'] == 0 || $ran['net'] == 0)
                        $avg = 0;
                    else
                        $avg = $ran['net']/$ran['tc'];
                    $ctr += $ran['tc'];

                    // $this->make->sRow();
                    //     // $this->make->th($ran['start']."-".$ran['end']);
                    //     $this->make->th(date('h:i A',strtotime($ran['start'])));
                    //     $this->make->th($ran['tg']);
                    //     $this->make->th(numInt($ran['gross']), array("style"=>"text-align:right"));
                    //     $this->make->th(numInt($ran['vsales']), array("style"=>"text-align:right"));
                    //     $this->make->th(numInt($ran['vat']), array("style"=>"text-align:right"));
                    //     $this->make->th(numInt($ran['charges']), array("style"=>"text-align:right"));
                    //     $this->make->th(numInt($ran['discounts']), array("style"=>"text-align:right"));
                    //     $this->make->th(numInt($avg), array("style"=>"text-align:right"));
                    // $this->make->eRow();

                    // $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => 'black'));
                    // $pdf->Cell(43, 0, date('h:i A',strtotime($ran['start'])), '', 0, 'L');        
                    // $pdf->Cell(32, 0, $ran['tg'], '', 0, 'L');        
                    // $pdf->Cell(32, 0, num($ran['gross']), '', 0, 'R');        
                    // $pdf->Cell(32, 0, num($ran['vsales']), '', 0, 'R');        
                    // $pdf->Cell(32, 0, num($ran['vat']), '', 0, 'R');        
                    // $pdf->Cell(32, 0, num($ran['charges']), '', 0, 'R');        
                    // $pdf->Cell(32, 0, num($ran['discounts']), '', 0, 'R');        
                    // $pdf->Cell(32, 0, num($avg), '', 0, 'R');        
                    
                    // $pdf->ln();  
                    $sheet->getCell('A'.$rc)->setValue(date('h:i A',strtotime($ran['start'])));
                    $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
                    $sheet->getCell('B'.$rc)->setValue($ran['tg']);
                    $sheet->getStyle('B'.$rc)->applyFromArray($styleCenter);
                    $sheet->getCell('C'.$rc)->setValue(numInt($ran['gross']));
                    $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
                    $sheet->getCell('D'.$rc)->setValue(numInt($ran['vsales']));     
                    $sheet->getStyle('D'.$rc)->applyFromArray($styleNum);
                    $sheet->getCell('E'.$rc)->setValue(numInt($ran['vat']));     
                    $sheet->getStyle('E'.$rc)->applyFromArray($styleNum);
                    $sheet->getCell('F'.$rc)->setValue(numInt($ran['charges']));     
                    $sheet->getStyle('F'.$rc)->applyFromArray($styleNum);
                    $sheet->getCell('G'.$rc)->setValue(numInt($ran['discounts']));     
                    $sheet->getStyle('G'.$rc)->applyFromArray($styleNum);
                    $sheet->getCell('H'.$rc)->setValue(numInt($avg));     
                    $sheet->getStyle('H'.$rc)->applyFromArray($styleNum);


                    $rc++;
                    $tctr += $ran['tc'];
                    $tnet += $ran['net'];
                    $tgc += $ran['tg'];
                    $tdisc += $ran['discounts'];
                    $tcharges += $ran['charges'];
                    $tvsales += $ran['vsales'];
                    $ttax += $ran['vat'];
                    $tgross += $ran['gross'];
                    $tavg += $avg;
                    // if($ctr == 0 || $ran['net'] == 0)
                    //     $tavg = 0;
                    // else
                    //     $tavg += $ran['net']/$ctr;

                    $counter++;
                    $progress = ($counter / $rcount) * 100;
                    // update_load(num($progress));

                }
                $gtctr += $tctr;
                $gtnet += $tnet;

                update_load(80);
                sleep(1);
                // $pdf->ln(3);

                // $pdf->Cell(43, 0, 'TOTAL', 'T', 0, 'L');        
                // $pdf->Cell(32, 0, $tgc, 'T', 0, 'L');        
                // $pdf->Cell(32, 0, num($tgross), 'T', 0, 'R');        
                // $pdf->Cell(32, 0, num($tvsales), 'T', 0, 'R');        
                // $pdf->Cell(32, 0, num($ttax), 'T', 0, 'R');        
                // $pdf->Cell(32, 0, num($tcharges), 'T', 0, 'R');        
                // $pdf->Cell(32, 0, num($tdisc), 'T', 0, 'R');        
                // $pdf->Cell(32, 0, '', 'T', 0, 'R');

                $sheet->getCell('A'.$rc)->setValue('TOTAL');
                $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
                $sheet->getCell('B'.$rc)->setValue($tgc);
                $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldCenter);
                $sheet->getCell('C'.$rc)->setValue(numInt($tgross));
                $sheet->getStyle('C'.$rc)->applyFromArray($styleBoldRight);
                $sheet->getCell('D'.$rc)->setValue(numInt($tvsales));     
                $sheet->getStyle('D'.$rc)->applyFromArray($styleBoldRight);
                $sheet->getCell('E'.$rc)->setValue(numInt($ttax));     
                $sheet->getStyle('E'.$rc)->applyFromArray($styleBoldRight);
                $sheet->getCell('F'.$rc)->setValue(numInt($tcharges));     
                $sheet->getStyle('F'.$rc)->applyFromArray($styleBoldRight);
                $sheet->getCell('G'.$rc)->setValue(numInt($tdisc));     
                $sheet->getStyle('G'.$rc)->applyFromArray($styleBoldRight);
                $sheet->getCell('H'.$rc)->setValue(numInt($tavg));     
                $sheet->getStyle('H'.$rc)->applyFromArray($styleBoldRight);
                $rc++;

            }
            // $payments_types = $payments['types'];
            // $payments_total = $payments['total'];
            // // $pays = array();
            // // $total = 0;
            // // foreach ($trans_payment as $py) {
            // //     // if(!in_array($py->sales_id, $ids_used)){
            // //     //     $ids_used[] = $py->sales_id;
            // //     // }
            // //     if($py->amount > $py->to_pay)
            // //         $amount = $py->to_pay;
            // //     else
            // //         $amount = $py->amount;
            // //     if(!isset($pays[$py->payment_type])){
            // //         $pays[$py->payment_type] = array('qty'=>1,'amount'=>$amount);
            // //     }
            // //     else{
            // //         $pays[$py->payment_type]['qty'] += 1;
            // //         $pays[$py->payment_type]['amount'] += $amount;
            // //     }
            // //     $total += $amount;
            // // }

            // $rc++;
            // $sheet->getCell('A'.$rc)->setValue('Payment Mode');
            // $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
            // $sheet->getCell('B'.$rc)->setValue('Payment Amount');
            // $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldLeft);
            // $rc++;

            // foreach ($payments_types as $code => $val) {
            //     // $pdf->ln();
            //     $sheet->getCell('A'.$rc)->setValue(strtoupper($code));
            //     $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            //     // $pdf->Cell(25, 0, strtoupper($code), '', 0, 'L');
            //     $sheet->getCell('B'.$rc)->setValue(numInt($val['amount']));
            //     $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
            //     // $pdf->Cell(15, 0, numInt($val['amount']), '', 0, 'L');
            //     // $print_str .= append_chars(substrwords(ucwords(strtoupper($code)),18,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
            //                   // .append_chars(numInt($val['amount']),"left",PAPER_RD_COL_3_3," ")."\r\n";
            //     // $pay_qty += $val['qty'];
            //     $rc++;
            // }
            // // $pdf->ln();

        
            // $sheet->getCell('A'.$rc)->setValue('TOTAL');
            // $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
            // $sheet->getCell('B'.$rc)->setValue(numInt($payments_total));
            // $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

        
        }else{
            $rc++;
            $sheet->mergeCells('A'.$rc.':H'.$rc);
            $sheet->getCell('A'.$rc)->setValue('No Sales Found');
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        } 
        
        update_load(100);
        // ob_end_clean();
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $objWriter->save('php://output');
    }

   public function menu_sales_rep_excel()
    {
         // $this->load->database('main', TRUE);
        $this->menu_model->db = $this->load->database('default', TRUE);
        $this->load->model("dine/menu_model");
        date_default_timezone_set('Asia/Manila');
        $this->load->library('Excel');
        $sheet = $this->excel->getActiveSheet();
        $filename = 'Menu Sales Report';
        $rc=1;
        #GET VALUES
        start_load(0);
            // $post = $this->set_post($_GET['calendar_range']);        
        
        $menu_cat_id = $_GET['menu_cat_id'];        
        $daterange = $_GET['calendar_range'];
        $branch_id = $_GET['branch_id']; 
        // $brand = $_GET['brand'];       
        $brand = "";       
        $dates = explode(" to ",$daterange);

        $setup = $this->setup_model->get_details($branch_id);
        $set = $setup[0];

        update_load(10);
        sleep(1);

        // $from = date2SqlDateTime($dates[0]);
        // $to = date2SqlDateTime($dates[1]);
        $from = date2SqlDateTime($dates[0]);        
        $to = date2SqlDateTime($dates[1]);
        $trans = $this->menu_model->get_menu_sales_rep($from, $to, $menu_cat_id, $branch_id,$brand);
        $trans_ret = array();
        // $trans_mod = $this->menu_model->get_mod_menu_sales_rep($from, $to, $menu_cat_id, $branch_id,$brand);

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
        if($branch_id == ""){
         $headers = array('Menu','Category', 'Qty','Gross','Sales (%)','Cost','Cost (%)', 'Margin','Branch');
        
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(20);
        }else{
             $headers = array('Menu','Category', 'Qty','Gross','Sales (%)','Cost','Cost (%)', 'Margin');
        
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        }


        $sheet->mergeCells('A'.$rc.':H'.$rc);
        $sheet->getCell('A'.$rc)->setValue($set->branch_name);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        $rc++;

        $sheet->mergeCells('A'.$rc.':H'.$rc);
        $sheet->getCell('A'.$rc)->setValue($set->address);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        $rc++;

        $brd = $this->setup_model->get_brands($brand);

        if($brd){
            $sheet->mergeCells('A'.$rc.':H'.$rc);
            $sheet->getCell('A'.$rc)->setValue($brd[0]->brand_name);
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $rc++;
        }

        $sheet->mergeCells('A'.$rc.':H'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Sales Report');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $rc++;

        $sheet->mergeCells('A'.$rc.':E'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Report Period: '.$daterange);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->mergeCells('F'.$rc.':H'.$rc);
        $sheet->getCell('F'.$rc)->setValue('Report Generated: '.(new \DateTime())->format('Y-m-d H:i:s'));
        $sheet->getStyle('F'.$rc)->applyFromArray($styleNum);
        $rc++;

        $sheet->mergeCells('A'.$rc.':E'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Transaction Time:');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $user = $this->session->userdata('user');
        $sheet->mergeCells('F'.$rc.':H'.$rc);
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
        $tot_vat_sales = 0;
        $tot_vat = 0;
        $tot_gross = 0;
        $tot_mod_gross = 0;
        $tot_sales_prcnt = 0;
        $tot_cost = 0;
        $tot_cost_prcnt = 0; 
        $tot_margin = 0;
        $counter = 0;
        $progress = 0;
        $trans_count = count($trans) + 10;
        foreach ($trans as $val) {
            $tot_gross += $val->gross;
            $tot_cost += $val->cost;
            $tot_margin += $val->gross - $val->cost;
        }
        // foreach ($trans_mod as $vv) {
        //     $tot_mod_gross += $vv->mod_gross;
        // }

        foreach ($trans as $k => $v) {
            // $pdf->Cell(55, 0, $v->menu_name, '', 0, 'L');        
            // $pdf->Cell(38, 0, $v->menu_cat_name, '', 0, 'L');        
            // $pdf->Cell(25, 0, num($v->qty), '', 0, 'R');        
            // $pdf->Cell(25, 0, num($v->vat_sales), '', 0, 'R');        
            // $pdf->Cell(25, 0, num($v->vat), '', 0, 'R');        
            // $pdf->Cell(25, 0, num($v->gross), '', 0, 'R');        
            // $pdf->Cell(25, 0, num(0), '', 0, 'R');        
            // $pdf->Cell(25, 0, num($v->cost), '', 0, 'R');        
            // $pdf->Cell(25, 0, num(0), '', 0, 'R');                    
            // $pdf->ln(); 

            $sheet->getCell('A'.$rc)->setValue($v->menu_name);
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue($v->menu_cat_name);
            $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('C'.$rc)->setValue(num($v->qty));
            $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
            // $sheet->getCell('D'.$rc)->setValue(num($v->vat_sales));     
            // $sheet->getStyle('D'.$rc)->applyFromArray($styleNum);
            // $sheet->getCell('E'.$rc)->setValue(num($v->vat));     
            // $sheet->getStyle('E'.$rc)->applyFromArray($styleNum);
            $sheet->getCell('D'.$rc)->setValue(num($v->gross));     
            $sheet->getStyle('D'.$rc)->applyFromArray($styleNum);
            if($tot_gross != 0){
                $sheet->getCell('E'.$rc)->setValue(num($v->gross / $tot_gross * 100)."%");                     
            }else{
                $sheet->getCell('E'.$rc)->setValue("0.00%");                                     
            }
            $sheet->getStyle('E'.$rc)->applyFromArray($styleNum);
            $sheet->getCell('F'.$rc)->setValue(num($v->cost));     
            $sheet->getStyle('F'.$rc)->applyFromArray($styleNum);
            if($tot_cost != 0){
                $sheet->getCell('G'.$rc)->setValue(num($v->cost / $tot_cost * 100)."%");     
            }else{
                $sheet->getCell('G'.$rc)->setValue("0.00%");                     
            }
            $sheet->getStyle('G'.$rc)->applyFromArray($styleNum);
            $sheet->getCell('H'.$rc)->setValue(num($v->gross - $v->cost));     
            $sheet->getStyle('H'.$rc)->applyFromArray($styleNum);
            if($branch_id == ""){
                $sheet->getCell('I'.$rc)->setValue(num($v->branch_code));     
                $sheet->getStyle('I'.$rc)->applyFromArray($styleNum);
            }               

            // Grand Total
            $tot_qty += $v->qty;
            // $tot_vat_sales += $v->vat_sales;
            // $tot_vat += $v->vat;
            // $tot_gross += $v->gross;
            $tot_sales_prcnt = 0;
            // $tot_cost += $v->cost;
            $tot_cost_prcnt = 0;

            $counter++;
            $progress = ($counter / $trans_count) * 100;
            update_load(num($progress)); 
            $rc++;             
        }
        $sheet->getCell('A'.$rc)->setValue('Grand Total');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue("");
        $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('C'.$rc)->setValue(num($tot_qty));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleBoldRight);
        // $sheet->getCell('D'.$rc)->setValue(num($tot_vat_sales));     
        // $sheet->getStyle('D'.$rc)->applyFromArray($styleBoldRight);
        // $sheet->getCell('E'.$rc)->setValue(num($tot_vat));     
        // $sheet->getStyle('E'.$rc)->applyFromArray($styleBoldRight);
        $sheet->getCell('D'.$rc)->setValue(num($tot_gross));     
        $sheet->getStyle('D'.$rc)->applyFromArray($styleBoldRight);
        $sheet->getCell('E'.$rc)->setValue();     
        $sheet->getStyle('E'.$rc)->applyFromArray($styleBoldRight);
        $sheet->getCell('F'.$rc)->setValue(num($tot_cost));     
        $sheet->getStyle('F'.$rc)->applyFromArray($styleBoldRight);
        $sheet->getCell('G'.$rc)->setValue();     
        $sheet->getStyle('G'.$rc)->applyFromArray($styleBoldRight);
        $sheet->getCell('H'.$rc)->setValue(num($tot_margin));     
        $sheet->getStyle('H'.$rc)->applyFromArray($styleBoldRight);
        $rc++; 

        //retail
        $tot_gross_ret = 0;
        // if(count($trans_ret) > 0){
        //     $rc++; 
        //     $col = 'A';
        //     $sheet->mergeCells('A'.$rc.':G'.$rc);
        //     $sheet->getCell('A'.$rc)->setValue('Retail Items');
        //     $sheet->getStyle('A'.$rc)->applyFromArray($styleCenter);
        //     $rc++;

        //     foreach ($headers as $txt) {
        //         $sheet->getCell($col.$rc)->setValue($txt);
        //         $sheet->getStyle($col.$rc)->applyFromArray($styleHeaderCell);
        //         $col++;
        //     }
        //     $rc++;               
                    

        //     // GRAND TOTAL VARIABLES
        //     $tot_qty = 0;
        //     $tot_vat_sales = 0;
        //     $tot_vat = 0;
        //     $tot_mod_gross = 0;
        //     $tot_sales_prcnt = 0;
        //     $tot_cost = 0;
        //     $tot_cost_prcnt = 0; 
        //     $tot_margin = 0;
        //     $counter = 0;
        //     $progress = 0;
        //     $trans_count = count($trans) + 10;
        //     foreach ($trans_ret as $val) {
        //         $tot_gross_ret += $val->gross;
        //         // $tot_cost += $val->cost;
        //         $tot_margin += $val->gross - 0;
        //     }

        //     foreach ($trans_ret as $k => $v) {
        //         // $pdf->Cell(55, 0, $v->menu_name, '', 0, 'L');        
        //         // $pdf->Cell(38, 0, $v->menu_cat_name, '', 0, 'L');        
        //         // $pdf->Cell(25, 0, num($v->qty), '', 0, 'R');        
        //         // $pdf->Cell(25, 0, num($v->vat_sales), '', 0, 'R');        
        //         // $pdf->Cell(25, 0, num($v->vat), '', 0, 'R');        
        //         // $pdf->Cell(25, 0, num($v->gross), '', 0, 'R');        
        //         // $pdf->Cell(25, 0, num(0), '', 0, 'R');        
        //         // $pdf->Cell(25, 0, num($v->cost), '', 0, 'R');        
        //         // $pdf->Cell(25, 0, num(0), '', 0, 'R');                    
        //         // $pdf->ln(); 

        //         $sheet->getCell('A'.$rc)->setValue($v->item_name);
        //         $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        //         $sheet->getCell('B'.$rc)->setValue($v->cat_name);
        //         $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
        //         $sheet->getCell('C'.$rc)->setValue(num($v->qty));
        //         $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        //         // $sheet->getCell('D'.$rc)->setValue(num($v->vat_sales));     
        //         // $sheet->getStyle('D'.$rc)->applyFromArray($styleNum);
        //         // $sheet->getCell('E'.$rc)->setValue(num($v->vat));     
        //         // $sheet->getStyle('E'.$rc)->applyFromArray($styleNum);
        //         $sheet->getCell('D'.$rc)->setValue(num($v->gross));     
        //         $sheet->getStyle('D'.$rc)->applyFromArray($styleNum);
        //         if($tot_gross != 0){
        //             $sheet->getCell('E'.$rc)->setValue(num($v->gross / $tot_gross * 100)."%");                     
        //         }else{
        //             $sheet->getCell('E'.$rc)->setValue("0.00%");                                     
        //         }
        //         $sheet->getStyle('E'.$rc)->applyFromArray($styleNum);
        //         $sheet->getCell('F'.$rc)->setValue(num(0));     
        //         $sheet->getStyle('F'.$rc)->applyFromArray($styleNum);
        //         if($tot_cost != 0){
        //             $sheet->getCell('G'.$rc)->setValue(num($v->cost / $tot_cost * 100)."%");     
        //         }else{
        //             $sheet->getCell('G'.$rc)->setValue("0.00%");                     
        //         }
        //         $sheet->getStyle('G'.$rc)->applyFromArray($styleNum);
        //         $sheet->getCell('H'.$rc)->setValue(num($v->gross - 0));     
        //         $sheet->getStyle('H'.$rc)->applyFromArray($styleNum);               

        //         // Grand Total
        //         $tot_qty += $v->qty;
        //         // $tot_vat_sales += $v->vat_sales;
        //         // $tot_vat += $v->vat;
        //         // $tot_gross += $v->gross;
        //         $tot_sales_prcnt = 0;
        //         // $tot_cost += $v->cost;
        //         $tot_cost_prcnt = 0;

        //         // $counter++;
        //         // $progress = ($counter / $trans_count) * 100;
        //         // update_load(num($progress)); 
        //         $rc++;             
        //     }
        //     $sheet->getCell('A'.$rc)->setValue('Grand Total');
        //     $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        //     $sheet->getCell('B'.$rc)->setValue("");
        //     $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldLeft);
        //     $sheet->getCell('C'.$rc)->setValue(num($tot_qty));
        //     $sheet->getStyle('C'.$rc)->applyFromArray($styleBoldRight);
        //     // $sheet->getCell('D'.$rc)->setValue(num($tot_vat_sales));     
        //     // $sheet->getStyle('D'.$rc)->applyFromArray($styleBoldRight);
        //     // $sheet->getCell('E'.$rc)->setValue(num($tot_vat));     
        //     // $sheet->getStyle('E'.$rc)->applyFromArray($styleBoldRight);
        //     $sheet->getCell('D'.$rc)->setValue(num($tot_gross_ret));     
        //     $sheet->getStyle('D'.$rc)->applyFromArray($styleBoldRight);
        //     $sheet->getCell('E'.$rc)->setValue();     
        //     $sheet->getStyle('E'.$rc)->applyFromArray($styleBoldRight);
        //     $sheet->getCell('F'.$rc)->setValue(num($tot_cost));     
        //     $sheet->getStyle('F'.$rc)->applyFromArray($styleBoldRight);
        //     $sheet->getCell('G'.$rc)->setValue();     
        //     $sheet->getStyle('G'.$rc)->applyFromArray($styleBoldRight);
        //     $sheet->getCell('H'.$rc)->setValue(num($tot_margin));     
        //     $sheet->getStyle('H'.$rc)->applyFromArray($styleBoldRight);
        //     $rc++; 
        // }

        // //menu modifier
        // $tot_gross_ret = 0;
        // if(count($trans_menu_mod) > 0){
        //     $rc++; 
        //     if($branch_id == ""){
        //         $headers = array('Modifier', 'Qty','Gross','Sales (%)','Cost','Cost (%)', 'Margin','Branch');
        //     }
        //     else{
        //         $headers = array('Modifier', 'Qty','Gross','Sales (%)','Cost','Cost (%)', 'Margin');
        //     }
        //     $col = 'A';
        //     $sheet->mergeCells('A'.$rc.':G'.$rc);
        //     $sheet->getCell('A'.$rc)->setValue('Menu Modifiers');
        //     $sheet->getStyle('A'.$rc)->applyFromArray($styleCenter);
        //     $rc++;

        //     foreach ($headers as $txt) {
        //         $sheet->getCell($col.$rc)->setValue($txt);
        //         $sheet->getStyle($col.$rc)->applyFromArray($styleHeaderCell);
        //         $col++;
        //     }
        //     $rc++;               
                    

        //     // GRAND TOTAL VARIABLES
        //     $tot_qty = 0;
        //     $tot_vat_sales = 0;
        //     $tot_vat = 0;
        //     $tot_mod_gross = 0;
        //     $tot_sales_prcnt = 0;
        //     $tot_cost = 0;
        //     $tot_cost_prcnt = 0; 
        //     $tot_margin = 0;
        //     $counter = 0;
        //     $progress = 0;
        //     $trans_count = count($trans_menu_mod) + 10;
        //     foreach ($trans_menu_mod as $val) {
        //         $tot_gross_ret += $val->gross;
        //         // $tot_cost += $val->cost;
        //         $tot_margin += $val->gross - 0;
        //     }

        //     foreach ($trans_menu_mod as $k => $v) {

        //         $sheet->getCell('A'.$rc)->setValue($v->modifier_name);
        //         $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        //         // $sheet->getCell('B'.$rc)->setValue($v->cat_name);
        //         // $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
        //         $sheet->getCell('B'.$rc)->setValue(num($v->qty));
        //         $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        //         // $sheet->getCell('D'.$rc)->setValue(num($v->vat_sales));     
        //         // $sheet->getStyle('D'.$rc)->applyFromArray($styleNum);
        //         // $sheet->getCell('E'.$rc)->setValue(num($v->vat));     
        //         // $sheet->getStyle('E'.$rc)->applyFromArray($styleNum);
        //         $sheet->getCell('C'.$rc)->setValue(num($v->gross));     
        //         $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        //         if($tot_gross != 0){
        //             $sheet->getCell('D'.$rc)->setValue(num($v->gross / $tot_gross * 100)."%");                     
        //         }else{
        //             $sheet->getCell('D'.$rc)->setValue("0.00%");                                     
        //         }
        //         $sheet->getStyle('D'.$rc)->applyFromArray($styleNum);
        //         $sheet->getCell('E'.$rc)->setValue(num(0));     
        //         $sheet->getStyle('E'.$rc)->applyFromArray($styleNum);
        //         if($tot_cost != 0){
        //             $sheet->getCell('F'.$rc)->setValue(num($v->cost / $tot_cost * 100)."%");     
        //         }else{
        //             $sheet->getCell('F'.$rc)->setValue("0.00%");                     
        //         }
        //         $sheet->getStyle('F'.$rc)->applyFromArray($styleNum);
        //         $sheet->getCell('G'.$rc)->setValue(num($v->gross - 0));     
        //         $sheet->getStyle('G'.$rc)->applyFromArray($styleNum);
        //         if($branch_id == ""){
        //             $sheet->getCell('H'.$rc)->setValue(num($v->branch_code));     
        //             $sheet->getStyle('H'.$rc)->applyFromArray($styleNum);
        //         }               

        //         // Grand Total
        //         $tot_qty += $v->qty;
        //         // $tot_vat_sales += $v->vat_sales;
        //         // $tot_vat += $v->vat;
        //         // $tot_gross += $v->gross;
        //         $tot_sales_prcnt = 0;
        //         // $tot_cost += $v->cost;
        //         $tot_cost_prcnt = 0;

        //         // $counter++;
        //         // $progress = ($counter / $trans_count) * 100;
        //         // update_load(num($progress)); 
        //         $rc++;             
        //     }
        //     $sheet->getCell('A'.$rc)->setValue('Grand Total');
        //     $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        //     // $sheet->getCell('B'.$rc)->setValue("");
        //     // $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldLeft);
        //     $sheet->getCell('B'.$rc)->setValue(num($tot_qty));
        //     $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldRight);
        //     // $sheet->getCell('D'.$rc)->setValue(num($tot_vat_sales));     
        //     // $sheet->getStyle('D'.$rc)->applyFromArray($styleBoldRight);
        //     // $sheet->getCell('E'.$rc)->setValue(num($tot_vat));     
        //     // $sheet->getStyle('E'.$rc)->applyFromArray($styleBoldRight);
        //     $sheet->getCell('C'.$rc)->setValue(num($tot_gross_ret));     
        //     $sheet->getStyle('C'.$rc)->applyFromArray($styleBoldRight);
        //     $sheet->getCell('D'.$rc)->setValue();     
        //     $sheet->getStyle('D'.$rc)->applyFromArray($styleBoldRight);
        //     $sheet->getCell('E'.$rc)->setValue(num($tot_cost));     
        //     $sheet->getStyle('E'.$rc)->applyFromArray($styleBoldRight);
        //     $sheet->getCell('F'.$rc)->setValue();     
        //     $sheet->getStyle('F'.$rc)->applyFromArray($styleBoldRight);
        //     $sheet->getCell('G'.$rc)->setValue(num($tot_margin));     
        //     $sheet->getStyle('G'.$rc)->applyFromArray($styleBoldRight);
        //     $rc++; 
        // }
        

        ///////////fpr payments
        // $this->cashier_model->db = $this->load->database('default', TRUE);
        // $args = array();

        // $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
        // $args["trans_sales.inactive = 0"] = array('use'=>'where','val'=>null,'third'=>false);
        // $args["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);



        // $post = $this->set_post();
        // $curr = false;
        // $trans = $this->trans_sales($args,$curr,$branch_id,$brand);
        // $sales = $trans['sales'];

        // $conso_args = array();
        // $conso_args["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);


        // $trans_menus = $this->menu_sales($sales['settled']['ids'],$curr,$branch_id,$brand,$from,$to);
        // $trans_charges = $this->charges_sales($sales['settled']['ids'],$curr,$branch_id,$brand,$conso_args);
        // $trans_discounts = $this->discounts_sales($sales['settled']['ids'],$curr,$branch_id,$brand,$from,$to);
        // $tax_disc = $trans_discounts['tax_disc_total'];
        // $no_tax_disc = $trans_discounts['no_tax_disc_total'];
        // $trans_local_tax = $this->local_tax_sales($sales['settled']['ids'],$curr,$branch_id,$brand,$conso_args);
        // $trans_tax = $this->tax_sales($sales['settled']['ids'],$curr,$branch_id,$brand,$conso_args);
        // $trans_no_tax = $this->no_tax_sales($sales['settled']['ids'],$curr,$branch_id,$brand,$conso_args);
        // $trans_zero_rated = $this->zero_rated_sales($sales['settled']['ids'],$curr,$branch_id,$brand,$conso_args);
        // $payments = $this->payment_sales($sales['settled']['ids'],$curr,$branch_id,$brand,$conso_args);

        // $gross = $trans_menus['gross'];

        // $net = $trans['net'];
        // $void = $trans['void'];
        // $charges = $trans_charges['total'];
        // $discounts = $trans_discounts['total'];
        // $local_tax = $trans_local_tax['total'];
        // $less_vat = (($gross+$charges+$local_tax) - $discounts) - $net;

        // if($less_vat < 0)
        //     $less_vat = 0;


        // $tax = $trans_tax['total'];
        // $no_tax = $trans_no_tax['total'];
        // $zero_rated = $trans_zero_rated['total'];
        // $no_tax -= $zero_rated;

        // $loc_txt = numInt(($local_tax));
        // $net_no_adds = $net-($charges+$local_tax);
        // $nontaxable = $no_tax;
        // $taxable = ($gross - $less_vat - $nontaxable - $zero_rated) / 1.12;
        // $total_net = ($taxable) + ($nontaxable+$zero_rated) + $tax + $local_tax;
        // $add_gt = $taxable+$nontaxable+$zero_rated;
        // $nsss = $taxable +  $nontaxable +  $zero_rated;

        // $vat_ = $taxable * .12;

        // $rc++;
        // $sheet->getCell('A'.$rc)->setValue('GROSS');
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        // $sheet->getCell('B'.$rc)->setValue(num($tot_gross  + $tot_gross_ret));
        // $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        // $rc++;
        // $sheet->getCell('A'.$rc)->setValue('VAT SALES');
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        // $sheet->getCell('B'.$rc)->setValue(num($taxable));
        // $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        // $rc++;
        // $sheet->getCell('A'.$rc)->setValue('VAT');
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        // $sheet->getCell('B'.$rc)->setValue(num($vat_));
        // $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        // $rc++;
        // $sheet->getCell('A'.$rc)->setValue('VAT EXEMPT SALES');
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        // $sheet->getCell('B'.$rc)->setValue(num($nontaxable-$zero_rated));
        // $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        // $rc++;
        // $sheet->getCell('A'.$rc)->setValue('ZERO RATED');
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        // $sheet->getCell('B'.$rc)->setValue(num($zero_rated));
        // $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

        // //MENU SUB CAT
        // $rc++; $rc++;
        // $sheet->getCell('A'.$rc)->setValue('SUB CATEGORIES');
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        // $sheet->getCell('B'.$rc)->setValue('AMOUNT');
        // $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldRight);

        // $subcats = $trans_menus['sub_cats'];
        // $total = 0;
        // foreach ($subcats as $id => $val) {
        //     $rc++;
        //     $sheet->getCell('A'.$rc)->setValue(strtoupper($val['name']));
        //     $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        //     $sheet->getCell('B'.$rc)->setValue(num($val['amount']));
        //     $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        //     $total += $val['amount'];
        // }
        // if($tot_gross_ret != 0){
        //     $rc++;
        //     $sheet->getCell('A'.$rc)->setValue(strtoupper('RETAIL'));
        //     $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        //     $sheet->getCell('B'.$rc)->setValue(num($tot_gross_ret));
        //     $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        //     $total += $tot_gross_ret;
        // }
        // $rc++;
        // $sheet->getCell('A'.$rc)->setValue(strtoupper('SubTotal'));
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        // $sheet->getCell('B'.$rc)->setValue(num($total));
        // $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

        // $rc++;
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        // $sheet->getCell('A'.$rc)->setValue(strtoupper('MODIFIERS TOTAL'));
        // $sheet->getCell('B'.$rc)->setValue(num($trans_menus['mods_total']));
        // $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

        // $rc++;
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        // $sheet->getCell('A'.$rc)->setValue(strtoupper('SUB MODIFIERS TOTAL'));
        // $sheet->getCell('B'.$rc)->setValue(num($trans_menus['submods_total']));
        // $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

        // $rc++;
        // $sheet->getCell('A'.$rc)->setValue(strtoupper('TOTAL'));
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        // $sheet->getCell('B'.$rc)->setValue(num($total + $trans_menus['mods_total'] + $trans_menus['submods_total']));
        // $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);


        //DISCOUNTS
        // $rc++; $rc++;
        // $sheet->getCell('A'.$rc)->setValue('DISCOUNT');
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        // $sheet->getCell('B'.$rc)->setValue('AMOUNT');
        // $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldRight);

        // $types = $trans_discounts['types'];
        // foreach ($types as $code => $val) {
        //     $rc++;
        //     $sheet->getCell('A'.$rc)->setValue(strtoupper($val['name']));
        //     $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        //     $sheet->getCell('B'.$rc)->setValue(num($val['amount']));
        //     $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
            
        // }

        // $rc++;
        // $sheet->getCell('A'.$rc)->setValue(strtoupper('Total'));
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        // $sheet->getCell('B'.$rc)->setValue(num($discounts));
        // $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        // $rc++;
        // $sheet->getCell('A'.$rc)->setValue(strtoupper('VAT EXEMPT'));
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        // $sheet->getCell('B'.$rc)->setValue(num($less_vat));
        // $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);


        // //CAHRGES
        // $rc++; $rc++;
        // $sheet->getCell('A'.$rc)->setValue('CHARGES');
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        // $sheet->getCell('B'.$rc)->setValue('AMOUNT');
        // $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldRight);

        // $types = $trans_charges['types'];
        // foreach ($types as $code => $val) {
        //     $rc++;
        //     $sheet->getCell('A'.$rc)->setValue(strtoupper($val['name']));
        //     $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        //     $sheet->getCell('B'.$rc)->setValue(num($val['amount']));
        //     $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
            
        // }
        // $rc++;
        // $sheet->getCell('A'.$rc)->setValue(strtoupper('Total'));
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        // $sheet->getCell('B'.$rc)->setValue(num($charges));
        // $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

        // //PAYMENTS
        // $rc++; $rc++;
        // $sheet->getCell('A'.$rc)->setValue('PAYMENT MODE');
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        // $sheet->getCell('B'.$rc)->setValue('PAYMENT AMOUNT');
        // $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldRight);

        // $payments_types = $payments['types'];
        // $payments_total = $payments['total'];
        // foreach ($payments_types as $code => $val) {
        //     $rc++;
        //     $sheet->getCell('A'.$rc)->setValue(strtoupper($code));
        //     $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        //     $sheet->getCell('B'.$rc)->setValue(num($val['amount']));
        //     $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        // }

        // $rc++;
        // $sheet->getCell('A'.$rc)->setValue(strtoupper('Total'));
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        // $sheet->getCell('B'.$rc)->setValue(num($payments_total));
        // $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);


        // if($trans['total_chit']){
        //     $rc++; $rc++;
        //     $sheet->getCell('A'.$rc)->setValue(strtoupper('Total Chit'));
        //     $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        //     $sheet->getCell('B'.$rc)->setValue(num($trans['total_chit']));
        //     $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        // }


        // $types = $trans['types'];
        // $types_total = array();
        // $guestCount = 0;
        // foreach ($types as $type => $tp) {
        //     foreach ($tp as $id => $opt){
        //         if(isset($types_total[$type])){
        //             $types_total[$type] += round($opt->total_amount,2);

        //         }
        //         else{
        //             $types_total[$type] = round($opt->total_amount,2);
        //         }
        //         if($opt->guest == 0)
        //             $guestCount += 1;
        //         else
        //             $guestCount += $opt->guest;
        //     }
        // }
        // $rc++;
        

        update_load(100);        
        // ob_end_clean();
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $objWriter->save('php://output');
        

        //============================================================+
        // END OF FILE
        //============================================================+   
    }

    public function sales_rep_excel()
    {
        $this->menu_model->db = $this->load->database('default', TRUE);
        $this->load->model("dine/menu_model");
        date_default_timezone_set('Asia/Manila');
        $this->load->library('Excel');
        $sheet = $this->excel->getActiveSheet();
        $filename = ' Sales Report';
        $rc=1;
        #GET VALUES
        $branch_id = $_GET['branch_id'];
        $brand = ""; 
        // $brand = $_GET['brand']; 

        start_load(0);
            // $post = $this->set_post($_GET['calendar_range']);
        $setup = $this->setup_model->get_details($branch_id);
        if(isset($setup[0]))
        {
            $set = $setup[0];            
            $store_open = $set->store_open;
        }else{
            $store_open = "00:00:00";
        }

        update_load(10);
        sleep(1);
        
        $menu_cat_id = $_GET['menu_cat_id'];        
        $daterange = $_GET['calendar_range'];        
        $dates = explode(" to ",$daterange);
        // $from = date2SqlDateTime($dates[0]);
        // $to = date2SqlDateTime($dates[1]);
        $from = date2SqlDateTime($dates[0]);        
        $to = date2SqlDateTime($dates[1]);
        // $branch_id = $this->input->post('branch_id');
          
        $trans = $this->menu_model->get_cat_sales_rep($from, $to, $menu_cat_id,$branch_id,$brand); 
        // echo $this->menu_model->db->last_query();
        // die();
        // echo ' haha';die(); 
        // $trans_ret = $this->menu_model->get_cat_sales_rep_retail($from, $to, "",$branch_id,$brand);   
        // $trans_mod = $this->menu_model->get_mod_cat_sales_rep($from, $to, $menu_cat_id,$branch_id,$brand); 
        // $trans_payment = $this->menu_model->get_payment_date($from, $to,$branch_id,$brand); 
        // $trans_menu_mod = $this->menu_model->get_menu_modifer_sales_rep($from, $to, $menu_cat_id,$branch_id,$brand);  

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
        if($branch_id == ""){
            $headers = array('Category', 'Qty','Gross','Sales (%)','Cost','Cost (%)', 'Margin','Branch');
        }
        else{
            $headers = array('Category', 'Qty','Gross','Sales (%)','Cost','Cost (%)', 'Margin');
        }
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        // $sheet->getColumnDimension('H')->setWidth(20);


        $sheet->mergeCells('A'.$rc.':H'.$rc);
        $sheet->getCell('A'.$rc)->setValue($set->branch_name);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        $rc++;

        $sheet->mergeCells('A'.$rc.':H'.$rc);
        $sheet->getCell('A'.$rc)->setValue($set->address);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        $rc++;

        $brd = $this->setup_model->get_brands($brand);

        if($brd){
            $sheet->mergeCells('A'.$rc.':H'.$rc);
            $sheet->getCell('A'.$rc)->setValue($brd[0]->brand_name);
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $rc++;
        }

        $sheet->mergeCells('A'.$rc.':H'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Sales Report');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $rc++;

        $sheet->mergeCells('A'.$rc.':D'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Report Period: '.$daterange);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->mergeCells('E'.$rc.':H'.$rc);
        $sheet->getCell('E'.$rc)->setValue('Report Generated: '.(new \DateTime())->format('Y-m-d H:i:s'));
        $sheet->getStyle('E'.$rc)->applyFromArray($styleNum);
        $rc++;

        $sheet->mergeCells('A'.$rc.':D'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Transaction Time:');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $user = $this->session->userdata('user');
        $sheet->mergeCells('E'.$rc.':H'.$rc);
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
        $tot_vat_sales = 0;
        $tot_vat = 0;
        $tot_gross = 0;
        $tot_mod_gross = 0;
        $tot_sales_prcnt = 0;
        $tot_cost = 0;
        $tot_cost_prcnt = 0; 
        $tot_margin = 0;
        $counter = 0;
        $progress = 0;
        $trans_count = count($trans);
        foreach ($trans as $val) {
            $tot_gross += $val->gross;
            $tot_cost += $val->cost;
            $tot_margin += $val->gross - $val->cost;
        }
        // foreach ($trans_mod as $vv) {
        //     $tot_mod_gross += $vv->mod_gross;
        // }

        foreach ($trans as $k => $v) {
            $sheet->getCell('A'.$rc)->setValue($v->menu_cat_name);
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue($v->qty);
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
            // $sheet->getCell('C'.$rc)->setValue(num($v->vat_sales));
            // $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
            // $sheet->getCell('D'.$rc)->setValue(num($v->vat));     
            // $sheet->getStyle('D'.$rc)->applyFromArray($styleNum);
            $sheet->getCell('C'.$rc)->setValue($v->gross);     
            $sheet->getStyle('C'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('D'.$rc)->setValue(num($v->gross / $tot_gross * 100)."%");     
            $sheet->getStyle('D'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            if($tot_cost != 0){
                $sheet->getCell('E'.$rc)->setValue($v->cost);                     
            }else{
                $sheet->getCell('E'.$rc)->setValue(0);                                     
            }
            $sheet->getStyle('E'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            // $sheet->getCell('F'.$rc)->setValue(num($v->cost / $tot_cost * 100)."%");
            if($tot_cost != 0){
                $sheet->getCell('F'.$rc)->setValue(num($v->cost / $tot_cost * 100)."%");     
            
            }else{
                $sheet->getCell('F'.$rc)->setValue('0.00%');                                     
            }     
            $sheet->getStyle('F'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('G'.$rc)->setValue(($v->gross - $v->cost));     
            $sheet->getStyle('G'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            if($branch_id == ""){
                $sheet->getCell('H'.$rc)->setValue($v->branch_name);
                $sheet->getStyle('H'.$rc)->applyFromArray($styleTxt);
            }      
            // Grand Total
            $tot_qty += $v->qty;
            // $tot_vat_sales += $v->vat_sales;
            // $tot_vat += $v->vat;
            // $tot_gross += $v->gross;
            $tot_sales_prcnt = 0;
            // $tot_cost += $v->cost;
            $tot_cost_prcnt = 0;

            $counter++;
            $progress = ($counter / $trans_count) * 100;
            update_load(num($progress));   
            $rc++;           
        }

        $sheet->getCell('A'.$rc)->setValue('Grand Total');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($tot_qty));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldRight);
        // $sheet->getCell('C'.$rc)->setValue(num($tot_vat_sales));     
        // $sheet->getStyle('C'.$rc)->applyFromArray($styleBoldRight);
        // $sheet->getCell('D'.$rc)->setValue(num($tot_vat));     
        // $sheet->getStyle('D'.$rc)->applyFromArray($styleBoldRight);
        $sheet->getCell('C'.$rc)->setValue($tot_gross);     
        $sheet->getStyle('C'.$rc)->applyFromArray($styleBoldRight)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->getCell('D'.$rc)->setValue("");     
        $sheet->getStyle('D'.$rc)->applyFromArray($styleBoldRight);
        $sheet->getCell('E'.$rc)->setValue($tot_cost);     
        $sheet->getStyle('E'.$rc)->applyFromArray($styleBoldRight)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->getCell('F'.$rc)->setValue("");     
        $sheet->getStyle('F'.$rc)->applyFromArray($styleBoldRight);
        $sheet->getCell('G'.$rc)->setValue($tot_margin);     
        $sheet->getStyle('G'.$rc)->applyFromArray($styleBoldRight)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $rc++; 
        // hide muna ayaw mag generate
        // //retail
        // $tot_gross_ret = 0;
        // if(count($trans_ret) > 0){

        //     $rc++; 
        //     $col = 'A';
        //     $sheet->mergeCells('A'.$rc.':G'.$rc);
        //     $sheet->getCell('A'.$rc)->setValue('Menu Modifiers');
        //     $sheet->getStyle('A'.$rc)->applyFromArray($styleCenter);
        //     $rc++;

        //     foreach ($headers as $txt) {
        //         $sheet->getCell($col.$rc)->setValue($txt);
        //         $sheet->getStyle($col.$rc)->applyFromArray($styleHeaderCell);
        //         $col++;
        //     }
        //     $rc++;                          

        //     // GRAND TOTAL VARIABLES
        //     $tot_qty = 0;
        //     $tot_vat_sales = 0;
        //     $tot_vat = 0;
        //     $tot_mod_gross = 0;
        //     $tot_sales_prcnt = 0;
        //     $tot_cost = 0;
        //     $tot_cost_prcnt = 0; 
        //     $tot_margin = 0;
        //     $counter = 0;
        //     $progress = 0;
        //     $trans_count = count($trans_ret);
        //     foreach ($trans_ret as $val) {
        //         $tot_gross_ret += $val->gross; 
        //         $tot_margin += $val->gross - 0;
        //     }

        //     foreach ($trans_ret as $k => $v) {
        //         $sheet->getCell('A'.$rc)->setValue($v->name);
        //         $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        //         $sheet->getCell('B'.$rc)->setValue($v->qty);
        //         $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        //         // $sheet->getCell('C'.$rc)->setValue(num($v->vat_sales));
        //         // $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        //         // $sheet->getCell('D'.$rc)->setValue(num($v->vat));     
        //         // $sheet->getStyle('D'.$rc)->applyFromArray($styleNum);
        //         $sheet->getCell('C'.$rc)->setValue(num($v->gross));     
        //         $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        //         $sheet->getCell('D'.$rc)->setValue(num($v->gross / $tot_gross * 100)."%");     
        //         $sheet->getStyle('D'.$rc)->applyFromArray($styleNum);
        //         $sheet->getStyle('E'.$rc)->applyFromArray($styleNum);
        //         $sheet->getCell('E'.$rc)->setValue(num(0));                                     
        //         if($tot_cost != 0){
        //             $sheet->getCell('F'.$rc)->setValue(num($v->cost / $tot_cost * 100)."%");     
        //         }else{
        //             $sheet->getCell('F'.$rc)->setValue('0.00%');                     
        //         }
        //         $sheet->getStyle('F'.$rc)->applyFromArray($styleNum);
        //         $sheet->getCell('G'.$rc)->setValue(num($v->gross - 0));     
        //         $sheet->getStyle('G'.$rc)->applyFromArray($styleNum);       

        //         // Grand Total
        //         $tot_qty += $v->qty;
        //         // $tot_vat_sales += $v->vat_sales;
        //         // $tot_vat += $v->vat;
        //         // $tot_gross += $v->gross;
        //         $tot_sales_prcnt = 0;
        //         // $tot_cost += $v->cost;
        //         $tot_cost_prcnt = 0;

        //         // $counter++;
        //         // $progress = ($counter / $trans_count) * 100;
        //         // update_load(num($progress));   
        //         $rc++;           
        //     }

        //     $sheet->getCell('A'.$rc)->setValue('Grand Total');
        //     $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        //     $sheet->getCell('B'.$rc)->setValue(num($tot_qty));
        //     $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldRight);
        //     // $sheet->getCell('C'.$rc)->setValue(num($tot_vat_sales));     
        //     // $sheet->getStyle('C'.$rc)->applyFromArray($styleBoldRight);
        //     // $sheet->getCell('D'.$rc)->setValue(num($tot_vat));     
        //     // $sheet->getStyle('D'.$rc)->applyFromArray($styleBoldRight);
        //     $sheet->getCell('C'.$rc)->setValue(num($tot_gross_ret));     
        //     $sheet->getStyle('C'.$rc)->applyFromArray($styleBoldRight);
        //     $sheet->getCell('D'.$rc)->setValue("");     
        //     $sheet->getStyle('D'.$rc)->applyFromArray($styleBoldRight);
        //     $sheet->getCell('E'.$rc)->setValue(num($tot_cost));     
        //     $sheet->getStyle('E'.$rc)->applyFromArray($styleBoldRight);
        //     $sheet->getCell('F'.$rc)->setValue("");     
        //     $sheet->getStyle('F'.$rc)->applyFromArray($styleBoldRight);
        //     $sheet->getCell('G'.$rc)->setValue(num($tot_margin));     
        //     $sheet->getStyle('G'.$rc)->applyFromArray($styleBoldRight);
        //     $rc++; 
        // }

        // // //menu modifier
        // // $tot_gross_mod = 0;
        // // if(count($trans_menu_mod) > 0){
        // //     if($branch_id == ""){
        // //         $headers = array('Modifier', 'Qty','Gross','Sales (%)','Cost','Cost (%)', 'Margin','Branch');
        // //     }
        // //     else{
        // //         $headers = array('Modifier', 'Qty','Gross','Sales (%)','Cost','Cost (%)', 'Margin');
        // //     }
        // //     $rc++; 
        // //     $col = 'A';
        // //     $sheet->mergeCells('A'.$rc.':G'.$rc);
        // //     $sheet->getCell('A'.$rc)->setValue('Menu Modifiers');
        // //     $sheet->getStyle('A'.$rc)->applyFromArray($styleCenter);
        // //     $rc++;

        // //     foreach ($headers as $txt) {
        // //         $sheet->getCell($col.$rc)->setValue($txt);
        // //         $sheet->getStyle($col.$rc)->applyFromArray($styleHeaderCell);
        // //         $col++;
        // //     }
        // //     $rc++;                          

        // //     // GRAND TOTAL VARIABLES
        // //     $tot_qty = 0;
        // //     $tot_vat_sales = 0;
        // //     $tot_vat = 0;
        // //     $tot_mod_gross = 0;
        // //     $tot_sales_prcnt = 0;
        // //     $tot_cost = 0;
        // //     $tot_cost_prcnt = 0; 
        // //     $tot_margin = 0;
        // //     $counter = 0;
        // //     $progress = 0;
        // //     $trans_count = count($trans_menu_mod);
        // //     foreach ($trans_menu_mod as $val) {
        // //         $tot_gross_mod += $val->gross; 
        // //         $tot_margin += $val->gross - 0;
        // //     }

        // //     foreach ($trans_menu_mod as $k => $v) {
        // //         $sheet->getCell('A'.$rc)->setValue($v->modifier_name);
        // //         $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        // //         $sheet->getCell('B'.$rc)->setValue($v->qty);
        // //         $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        // //         // $sheet->getCell('C'.$rc)->setValue(num($v->vat_sales));
        // //         // $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        // //         // $sheet->getCell('D'.$rc)->setValue(num($v->vat));     
        // //         // $sheet->getStyle('D'.$rc)->applyFromArray($styleNum);
        // //         $sheet->getCell('C'.$rc)->setValue(num($v->gross));     
        // //         $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        // //         $sheet->getCell('D'.$rc)->setValue(num($v->gross / $tot_gross * 100)."%");     
        // //         $sheet->getStyle('D'.$rc)->applyFromArray($styleNum);
        // //         $sheet->getStyle('E'.$rc)->applyFromArray($styleNum);
        // //         $sheet->getCell('E'.$rc)->setValue(num(0));                                     
        // //         if($tot_cost != 0){
        // //             $sheet->getCell('F'.$rc)->setValue(num($v->cost / $tot_cost * 100)."%");     
        // //         }else{
        // //             $sheet->getCell('F'.$rc)->setValue('0.00%');                     
        // //         }
        // //         $sheet->getStyle('F'.$rc)->applyFromArray($styleNum);
        // //         $sheet->getCell('G'.$rc)->setValue(num($v->gross - 0));     
        // //         $sheet->getStyle('G'.$rc)->applyFromArray($styleNum);
        // //         if($branch_id == ""){
        // //             $sheet->getCell('H'.$rc)->setValue(num($v->branch_code));     
        // //             $sheet->getStyle('H'.$rc)->applyFromArray($styleNum);
        // //         }       

        // //         // Grand Total
        // //         $tot_qty += $v->qty;
        // //         // $tot_vat_sales += $v->vat_sales;
        // //         // $tot_vat += $v->vat;
        // //         // $tot_gross += $v->gross;
        // //         $tot_sales_prcnt = 0;
        // //         // $tot_cost += $v->cost;
        // //         $tot_cost_prcnt = 0;

        // //         // $counter++;
        // //         // $progress = ($counter / $trans_count) * 100;
        // //         // update_load(num($progress));   
        // //         $rc++;           
        // //     }

        // //     $sheet->getCell('A'.$rc)->setValue('Grand Total');
        // //     $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        // //     $sheet->getCell('B'.$rc)->setValue(num($tot_qty));
        // //     $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldRight);
        // //     // $sheet->getCell('C'.$rc)->setValue(num($tot_vat_sales));     
        // //     // $sheet->getStyle('C'.$rc)->applyFromArray($styleBoldRight);
        // //     // $sheet->getCell('D'.$rc)->setValue(num($tot_vat));     
        // //     // $sheet->getStyle('D'.$rc)->applyFromArray($styleBoldRight);
        // //     $sheet->getCell('C'.$rc)->setValue(num($tot_gross_mod));     
        // //     $sheet->getStyle('C'.$rc)->applyFromArray($styleBoldRight);
        // //     $sheet->getCell('D'.$rc)->setValue("");     
        // //     $sheet->getStyle('D'.$rc)->applyFromArray($styleBoldRight);
        // //     $sheet->getCell('E'.$rc)->setValue(num($tot_cost));     
        // //     $sheet->getStyle('E'.$rc)->applyFromArray($styleBoldRight);
        // //     $sheet->getCell('F'.$rc)->setValue("");     
        // //     $sheet->getStyle('F'.$rc)->applyFromArray($styleBoldRight);
        // //     $sheet->getCell('G'.$rc)->setValue(num($tot_margin));     
        // //     $sheet->getStyle('G'.$rc)->applyFromArray($styleBoldRight);
        // //     $rc++; 
        // // }


        // ///////////fpr payments
        // $this->cashier_model->db = $this->load->database('default', TRUE);
        // $args = array();
        // // if($user)
        // //     $args["trans_sales.user_id"] = array('use'=>'where','val'=>$user,'third'=>false);

        // $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
        // $args["trans_sales.inactive = 0"] = array('use'=>'where','val'=>null,'third'=>false);
        // $args["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);

        // // if($menu_cat_id != 0){
        // //     $args["menu_categories.menu_cat_id"] = array('use'=>'where','val'=>$menu_cat_id,'third'=>false);
        // // }


        // $post = $this->set_post();

        // $conso_args = array();
        // $conso_args["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);

        // // $curr = $this->search_current();
        // $curr = false;
        // $trans = $this->trans_sales($args,$curr,$branch_id,$brand);
        // $sales = $trans['sales'];

        // $trans_menus = $this->menu_sales($sales['settled']['ids'],$curr,$branch_id,$brand,$from,$to);
        // $trans_charges = $this->charges_sales($sales['settled']['ids'],$curr,$branch_id,$brand,$conso_args);
        // $trans_discounts = $this->discounts_sales($sales['settled']['ids'],$curr,$branch_id,$brand,$from,$to);
        // $tax_disc = $trans_discounts['tax_disc_total'];
        // $no_tax_disc = $trans_discounts['no_tax_disc_total'];
        // $trans_local_tax = $this->local_tax_sales($sales['settled']['ids'],$curr,$branch_id,$brand,$conso_args);
        // $trans_tax = $this->tax_sales($sales['settled']['ids'],$curr,$branch_id,$brand,$from,$to);
        // $trans_no_tax = $this->no_tax_sales($sales['settled']['ids'],$curr,$branch_id,$brand,$conso_args);
        // $trans_zero_rated = $this->zero_rated_sales($sales['settled']['ids'],$curr,$branch_id,$brand,$conso_args);
        // $payments = $this->payment_sales($sales['settled']['ids'],$curr,$branch_id,$brand,$conso_args);

        // $gross = $trans_menus['gross'];

        // $net = $trans['net'];
        // $void = $trans['void'];
        // $charges = $trans_charges['total'];
        // $discounts = $trans_discounts['total'];
        // $local_tax = $trans_local_tax['total'];
        // $less_vat = (($gross+$charges+$local_tax) - $discounts) - $net;

        // if($less_vat < 0)
        //     $less_vat = 0;

        // $tax = $trans_tax['total'];
        // $no_tax = $trans_no_tax['total'];
        // $zero_rated = $trans_zero_rated['total'];
        // $no_tax -= $zero_rated;

        // $loc_txt = numInt(($local_tax));
        // $net_no_adds = $net-($charges+$local_tax);
        // $nontaxable = $no_tax;
        // $taxable = ($gross - $less_vat - $nontaxable - $zero_rated) / 1.12;
        // $total_net = ($taxable) + ($nontaxable+$zero_rated) + $tax + $local_tax;
        // $add_gt = $taxable+$nontaxable+$zero_rated;
        // $nsss = $taxable +  $nontaxable +  $zero_rated;

        // $vat_ = $taxable * .12;

        // $rc++;
        // // echo $tot_gross + $tot_mod_gross  + $tot_gross_ret;die();
        // $sheet->getCell('A'.$rc)->setValue('GROSS');
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        // $sheet->getCell('B'.$rc)->setValue(num($tot_gross + $tot_mod_gross + $tot_gross_ret));
        // $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        // $rc++;
        // $sheet->getCell('A'.$rc)->setValue('VAT SALES');
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        // $sheet->getCell('B'.$rc)->setValue(num($taxable));
        // $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        // $rc++;
        // $sheet->getCell('A'.$rc)->setValue('VAT');
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        // $sheet->getCell('B'.$rc)->setValue(num($vat_));
        // $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        // $rc++;
        // $sheet->getCell('A'.$rc)->setValue('VAT EXEMPT SALES');
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        // $sheet->getCell('B'.$rc)->setValue(num($nontaxable-$zero_rated));
        // $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        // $rc++;
        // $sheet->getCell('A'.$rc)->setValue('ZERO RATED');
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        // $sheet->getCell('B'.$rc)->setValue(num($zero_rated));
        // $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

        // // //MENU SUB CAT
        // $rc++; $rc++;
        // $sheet->getCell('A'.$rc)->setValue('SUB CATEGORIES');
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        // $sheet->getCell('B'.$rc)->setValue('AMOUNT');
        // $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldRight);

        // $subcats = $trans_menus['sub_cats'];
        // $total = 0;
        // foreach ($subcats as $id => $val) {
        //     $rc++;
        //     $sheet->getCell('A'.$rc)->setValue(strtoupper($val['name']));
        //     $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        //     $sheet->getCell('B'.$rc)->setValue(num($val['amount']));
        //     $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        //     $total += $val['amount'];
        // }
        // if($tot_gross_ret != 0){
        //     $rc++;
        //     $sheet->getCell('A'.$rc)->setValue(strtoupper('RETAIL'));
        //     $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        //     $sheet->getCell('B'.$rc)->setValue(num($tot_gross_ret));
        //     $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        //     $total += $tot_gross_ret;
        // }
        // $rc++;
        // $sheet->getCell('A'.$rc)->setValue(strtoupper('SubTotal'));
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        // $sheet->getCell('B'.$rc)->setValue(num($total));
        // $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

        // $rc++;
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        // $sheet->getCell('A'.$rc)->setValue(strtoupper('MODIFIERS TOTAL'));
        // $sheet->getCell('B'.$rc)->setValue(num($trans_menus['mods_total']));
        // $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

        // $rc++;
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        // $sheet->getCell('A'.$rc)->setValue(strtoupper('SUB MODIFIERS TOTAL'));
        // $sheet->getCell('B'.$rc)->setValue(num($trans_menus['submods_total']));
        // $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

        // $rc++;
        // $sheet->getCell('A'.$rc)->setValue(strtoupper('TOTAL'));
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        // $sheet->getCell('B'.$rc)->setValue(num($total + $trans_menus['mods_total'] + $trans_menus['submods_total']));
        // $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

        // //DISCOUNTS
        // $rc++; $rc++;
        // $sheet->getCell('A'.$rc)->setValue('DISCOUNT');
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        // $sheet->getCell('B'.$rc)->setValue('AMOUNT');
        // $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldRight);

        // $types = $trans_discounts['types'];
        // foreach ($types as $code => $val) {
        //     $rc++;
        //     $sheet->getCell('A'.$rc)->setValue(strtoupper($val['name']));
        //     $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        //     $sheet->getCell('B'.$rc)->setValue(num($val['amount']));
        //     $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
            
        // }

        // $rc++;
        // $sheet->getCell('A'.$rc)->setValue(strtoupper('Total'));
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        // $sheet->getCell('B'.$rc)->setValue(num($discounts));
        // $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        // $rc++;
        // $sheet->getCell('A'.$rc)->setValue(strtoupper('VAT EXEMPT'));
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        // $sheet->getCell('B'.$rc)->setValue(num($less_vat));
        // $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);


        // // //CAHRGES
        // $rc++; $rc++;
        // $sheet->getCell('A'.$rc)->setValue('CHARGES');
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        // $sheet->getCell('B'.$rc)->setValue('AMOUNT');
        // $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldRight);

        // $types = $trans_charges['types'];
        // foreach ($types as $code => $val) {
        //     $rc++;
        //     $sheet->getCell('A'.$rc)->setValue(strtoupper($val['name']));
        //     $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        //     $sheet->getCell('B'.$rc)->setValue(num($val['amount']));
        //     $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
            
        // }
        // $rc++;
        // $sheet->getCell('A'.$rc)->setValue(strtoupper('Total'));
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        // $sheet->getCell('B'.$rc)->setValue(num($charges));
        // $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

        // // //PAYMENTS
        // $rc++; $rc++;
        // $sheet->getCell('A'.$rc)->setValue('PAYMENT MODE');
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        // $sheet->getCell('B'.$rc)->setValue('PAYMENT AMOUNT');
        // $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldRight);

        // $payments_types = $payments['types'];
        // $payments_total = $payments['total'];
        // foreach ($payments_types as $code => $val) {
        //     $rc++;
        //     $sheet->getCell('A'.$rc)->setValue(strtoupper($code));
        //     $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        //     $sheet->getCell('B'.$rc)->setValue(num($val['amount']));
        //     $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        // }

        // $rc++;
        // $sheet->getCell('A'.$rc)->setValue(strtoupper('Total'));
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        // $sheet->getCell('B'.$rc)->setValue(num($payments_total));
        // $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

        // if($trans['total_chit']){
        //     $rc++; $rc++;
        //     $sheet->getCell('A'.$rc)->setValue(strtoupper('Total Chit'));
        //     $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        //     $sheet->getCell('B'.$rc)->setValue(num($trans['total_chit']));
        //     $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        // }


        // $types = $trans['types'];
        // $types_total = array();
        // $guestCount = 0;
        // foreach ($types as $type => $tp) {
        //     foreach ($tp as $id => $opt){
        //         if(isset($types_total[$type])){
        //             $types_total[$type] += round($opt->total_amount,2);

        //         }
        //         else{
        //             $types_total[$type] = round($opt->total_amount,2);
        //         }
        //         if($opt->guest == 0)
        //             $guestCount += 1;
        //         else
        //             $guestCount += $opt->guest;
        //     }
        // }
        // $rc++;
        // hide muna ayaw mag generate
        // $rc++;
        // $sheet->getCell('A'.$rc)->setValue(strtoupper('Trans Count'));
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        // $tc_total  = 0;
        // $tc_qty = 0;
        // foreach ($types_total as $typ => $tamnt) {
        //     $rc++;
        //     $sheet->getCell('A'.$rc)->setValue(strtoupper($typ));
        //     $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        //     $sheet->getCell('B'.$rc)->setValue(count($types[$typ]));
        //     $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        //     $sheet->getCell('C'.$rc)->setValue(num($tamnt));
        //     $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        //     $tc_total += $tamnt;
        //     $tc_qty += count($types[$typ]);
        // }
        // $rc++;
        // $sheet->getCell('A'.$rc)->setValue(strtoupper('TC TOTAL'));
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        // $sheet->getCell('C'.$rc)->setValue(num($tc_total));
        // $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);

        
        update_load(100);        
       
        if (ob_get_contents())
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

    // Month to Month and Year to date Report
    // Created by Rod
    public function month_to_month_rep()
    {
        $data = $this->syter->spawn('mom_report');        
        $data['page_title'] = fa('fa-money')." Month to Month Report";
        $data['code'] = MonthToMonthReportUi();
        $data['add_css'] = array('css/morris/morris.css','css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css','assets/global/plugins/select2/css/select2.min.css','assets/global/plugins/select2/css/select2-bootstrap.min.css');
        $data['add_js'] = array('js/plugins/morris/morris.min.js','js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js','assets/global/plugins/select2/js/select2.full.min.js','assets/pages/scripts/components-select2.min.js');
        $data['page_no_padding'] = false;
        $data['sideBarHide'] = false;
        $data['load_js'] = 'dine/reporting';
        $data['use_js'] = 'momJs';
        $this->load->view('page',$data);
    }
    public function month_to_month_gen()
    {
        $this->load->model('dine/setup_model');        
        $this->load->model("dine/menu_model");        
        
        $branch_id = $this->input->post('branch_id');
        $brand = $this->input->post('brand');        
        start_load(0);
        
        $date = $this->input->post("date");                
        $this->menu_model->db = $this->load->database('default', TRUE);
        // $date = '2018-04-30';
        $to = date("Y-m-d", strtotime($date));                
        $from = date('Y-m-01', strtotime($to . ' -2 months'));        
        
        $param = array();
        for ($i=date("Y-m", strtotime($from)); $i <= date("Y-m", strtotime($to)); $i = date('Y-m', strtotime($i . ' +1 months'))) { 
            $param[date("m", strtotime($i."-01"))] = array("from"=>$i."-01", "to"=>date("Y-m-t", strtotime($i."-01")));            
        }        
        $this->session->set_userdata("param", $param);
        $branch_details = $this->menu_model->get_branch_details($branch_id);                      
        $this->session->set_userdata("branch_details", $branch_details);
        $trans = array();
        $counter = 0;
        $this->make->sDiv();
            $this->make->sTable(array("id"=>"main-tbl", 'class'=>'table reportTBL sortable'));
                $this->make->sTableHead();                
                    $this->make->sRow();
                    $this->make->th("Branch");
                    foreach ($param as $k => $v) {
                        $monthNum  = $k;
                        $dateObj   = DateTime::createFromFormat('!m', $monthNum);
                        $monthName = $dateObj->format('F');
                        $this->make->th($monthName);                                                
                    }                                   
                    $this->make->eRow();
                $this->make->eTableHead();                

                $this->make->sTableBody();

                $brand_txt = '';

                $brd = $this->setup_model->get_brands($brand);
                if($brd){
                    $brand_txt = '/' . $brd[0]->brand_name;
                }

                $total = array();
                foreach ($branch_details as $key => $vbranch) {
                    $this->make->sRow();
                        $this->make->td($vbranch->branch_name . $brand_txt);
                        foreach ($param as $k => $v) {
                            $amt = 0;
                            $sales = $this->menu_model->get_monthly_sales($v["from"], $v["to"], $vbranch->branch_code,$brand);
                            if(!empty($sales)){                                
                                   $this->make->td(num($sales[0]->amt), array("style"=>"text-align:right"));
                                   $amt += $sales[0]->amt;
                            }else{
                                   $this->make->td("0.00", array("style"=>"text-align:right"));                                
                            }                        
                            if(array_key_exists($k, $total)){
                                $total[$k] += $amt;
                            }else{
                                $total[$k] = $amt;                                
                            }
                        }
                    $this->make->eRow();
                }
                $this->make->sRow();
                    $this->make->th("Total");           
                    foreach ($total as $key => $value) 
                    {                        
                        $this->make->th(num($value), array("style"=>"text-align:right"));                                    
                    }         
                $this->make->eRow();
                $this->make->eTableBody();
            $this->make->eTable();
        $this->make->eDiv();
        update_load(100);
        $code = $this->make->code();
        $json['code'] = $code;        
        $json['tbl_vals'] = $trans;
        $json['dates'] = $this->input->post('date');
        echo json_encode($json);
    }
    public function mom_rep_pdf()
    {
        // Include the main TCPDF library (search for installation path).
        require_once( APPPATH .'third_party/tcpdf.php');
        $this->load->model('dine/setup_model');     
        date_default_timezone_set('Asia/Manila');   

        // create new PDF document
        $pdf = new TCPDF("L", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('iPOS');
        $pdf->SetTitle('MoM Report');
        $pdf->SetSubject('');
        $pdf->SetKeywords('');

        // set default header data
        // $setup = $this->setup_model->get_details(1);
        // $set = $setup[0];
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "Month To Month Report", "");

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
               
        start_load(0);

        // set font
        $pdf->SetFont('helvetica', 'B', 11);

        // add a page
        $pdf->AddPage();
        
        $date = $this->input->get("date");                               
        $this->load->model("dine/menu_model");        
                
        $this->menu_model->db = $this->load->database('default', TRUE);
        
        $pdf->Write(0, 'MONTH ON MONTH REPORT', '', 0, 'L', true, 0, false, false, 0);
        $pdf->SetLineStyle(array('width' => 0.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => 'black'));
        $pdf->Cell(260, 0, '', 'T', 0, 'C');
        $pdf->ln(0.9);      
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Write(0, 'Report Period:    ', '', 0, 'L', false, 0, false, false, 0);
        $pdf->Write(0, $date, '', 0, 'L', false, 0, false, false, 0);
        $pdf->setX(210);
        $pdf->Write(0, 'Report Generated:    '.(new \DateTime())->format('Y-m-d H:i:s'), '', 0, 'L', true, 0, false, false, 0);
        // $pdf->Write(0, 'Transaction Time:    ', '', 0, 'L', false, 0, false, false, 0);
        $pdf->setX(210);
        $user = $this->session->userdata('user');
        $pdf->Write(0, 'Generated by:    '.$user["full_name"], '', 0, 'L', true, 0, false, false, 0);        
        $pdf->ln(1);      
        $pdf->Cell(260, 0, '', 'T', 0, 'C');
        $pdf->ln();              

        // echo "<pre>", print_r($trans), "</pre>";die();
           
        // -----------------------------------------------------------------------------
        $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => 'black'));        
        $param = $this->session->userdata("param");
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(43, 0, "Branch", 'B', 0, 'L');
        foreach ($param as $k => $v) {
            $monthNum  = $k;
            $dateObj   = DateTime::createFromFormat('!m', $monthNum);
            $monthName = $dateObj->format('F');            
            $pdf->Cell(43, 0, $monthName, 'B', 0, 'C');
        }              
        $pdf->ln();     
        $pdf->SetFont('helvetica', '', 9);

        $brand = $this->input->get('brand');

        $brand_txt = '';

        $brd = $this->setup_model->get_brands($brand);
        if($brd){
            $brand_txt = '/' . $brd[0]->brand_name;
        }

        $total = array();
        $branch_details = $this->session->userdata("branch_details");
        $counter = 0;
        $progress = 0;
        $trans_count = count($branch_details);
        foreach ($branch_details as $key => $vbranch) 
        {                           
            $pdf->Cell(43, 0, $vbranch->branch_name.$brand_txt, '', 0, 'L');
            foreach ($param as $k => $v) {
                $amt = 0;
                $sales = $this->menu_model->get_monthly_sales($v["from"], $v["to"], $vbranch->branch_code,$brand);
                if(!empty($sales)){                                                       
                       $pdf->Cell(43, 0, num($sales[0]->amt), '', 0, 'R');
                       $amt += $sales[0]->amt;
                }else{
                       $pdf->Cell(43, 0, num(0), '', 0, 'R');                       
                }                        
                if(array_key_exists($k, $total)){
                    $total[$k] += $amt;
                }else{
                    $total[$k] = $amt;                                
                }
            }            
            $pdf->ln();   
            $counter++;
            $progress = ($counter / $trans_count) * 100;
            update_load(num($progress));     
        }

        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(43, 0, "Total", 'B', 0, 'L');          
        foreach ($total as $key => $value) 
        {                        
            $pdf->Cell(43, 0, num($value), 'B', 0, 'R');                                   
        }                 

        
        update_load(100);                

        // -----------------------------------------------------------------------------

        //Close and output PDF document
        $pdf->Output('mom_report.pdf', 'I');

        //============================================================+
        // END OF FILE
        //============================================================+   
    }
    public function mom_rep_excel()
    {

        date_default_timezone_set('Asia/Manila');
        $this->load->library('Excel');
        $sheet = $this->excel->getActiveSheet();
        $filename = 'Month on Month Report';
        $rc=1;
        #GET VALUES
        start_load(0);         
        sleep(1);        

        $date = $this->input->get("date"); 
        $brand = $this->input->get("brand");                               
        $this->load->model("dine/menu_model");        
                
        $this->menu_model->db = $this->load->database('default', TRUE);

        $param = $this->session->userdata("param");
        $branch_details = $this->session->userdata("branch_details");
       
        
        if(count($branch_details) > 0){            

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
            
            $sheet->getColumnDimension('A')->setWidth(20);
            $sheet->getColumnDimension('B')->setWidth(20);
            $sheet->getColumnDimension('C')->setWidth(20);
            $sheet->getColumnDimension('D')->setWidth(20);
            $sheet->getColumnDimension('E')->setWidth(20);
            $sheet->getColumnDimension('F')->setWidth(20);
            $sheet->getColumnDimension('G')->setWidth(20);
            $sheet->getColumnDimension('H')->setWidth(20);


            $sheet->mergeCells('A'.$rc.':H'.$rc);
            $sheet->getCell('A'.$rc)->setValue('');
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
            $rc++;

            $sheet->mergeCells('A'.$rc.':H'.$rc);
            $sheet->getCell('A'.$rc)->setValue("");
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
            $rc++;

            $sheet->mergeCells('A'.$rc.':H'.$rc);
            $sheet->getCell('A'.$rc)->setValue('MONTH ON MONTH REPORT');
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $rc++;

            $sheet->mergeCells('A'.$rc.':D'.$rc);
            $sheet->getCell('A'.$rc)->setValue('Report Period: '.$date);
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->mergeCells('E'.$rc.':H'.$rc);
            $sheet->getCell('E'.$rc)->setValue('Report Generated: '.(new \DateTime())->format('Y-m-d H:i:s'));
            $sheet->getStyle('E'.$rc)->applyFromArray($styleNum);
            $rc++;

            $sheet->mergeCells('A'.$rc.':D'.$rc);
            $sheet->getCell('A'.$rc)->setValue('Transaction Time:');
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $user = $this->session->userdata('user');
            $sheet->mergeCells('E'.$rc.':H'.$rc);
            $sheet->getCell('E'.$rc)->setValue('Generated by:    '.$user["full_name"]);
            $sheet->getStyle('E'.$rc)->applyFromArray($styleNum);
            $rc++;

            $col = 'A';            
            $sheet->getCell($col.$rc)->setValue("Branch");
            $sheet->getStyle($col.$rc)->applyFromArray($styleHeaderCell);
            $col++;
            foreach ($param as $k => $v) {
                $monthNum  = $k;
                $dateObj   = DateTime::createFromFormat('!m', $monthNum);
                $monthName = $dateObj->format('F');
                $sheet->getCell($col.$rc)->setValue($monthName);
                $sheet->getStyle($col.$rc)->applyFromArray($styleHeaderCell);
                $col++;                
            }   
            $rc++;
            $counter = 0;
            $progress = 0;
            $trans_count = count($branch_details);
            $total = array();

            $brand_txt = '';

            $brd = $this->setup_model->get_brands($brand);
            if($brd){
                $brand_txt = '/' . $brd[0]->brand_name;
            }

            foreach ($branch_details as $key => $vbranch) 
            {                               
                $sheet->getCell('A'.$rc)->setValue($vbranch->branch_name.$brand_txt);
                $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
                $col="B";
                foreach ($param as $k => $v) {
                    $amt = 0;
                    $sales = $this->menu_model->get_monthly_sales($v["from"], $v["to"], $vbranch->branch_code,$brand);
                    if(!empty($sales)){                                                                                       
                            $sheet->getCell($col.$rc)->setValue($sales[0]->amt);
                            $sheet->getStyle($col.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                            $amt += $sales[0]->amt;
                    }else{
                           $sheet->getCell($col.$rc)->setValue(0);
                           $sheet->getStyle($col.$rc)->applyFromArray($styleTxt);
                    }                        
                    if(array_key_exists($k, $total)){
                        $total[$k] += $amt;
                    }else{
                        $total[$k] = $amt;                                
                    }
                    $col++;
                }            
                $rc++;
                $counter++;
                $progress = ($counter / $trans_count) * 100;
                update_load(num($progress));     
            }
            $col = "A";
            $sheet->getCell($col.$rc)->setValue("Total");
            $sheet->getStyle($col.$rc)->applyFromArray($styleHeaderCell);                                  
            $col++;
            foreach ($total as $key => $value) 
            {                       
                $sheet->getCell($col.$rc)->setValue($value);
                $sheet->getStyle($col.$rc)->applyFromArray($styleHeaderCell)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);                                  
                $col++;
            }  
            
            // $rc++;
            // $ctr = 0;
            // $gtavg = $gtctr = $gtnet = 0;
            // $strt = "";

            // foreach($dates as $key1 => $v1){

            //     $sheet->mergeCells('A'.$rc.':H'.$rc);
            //     $sheet->getCell('A'.$rc)->setValue(sql2Date($key1));
            //     $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
            //     $rc++;


            //     $ranges = $v1['ranges'];
            //     //$txt .= sql2Date($key1);
            //     $tavg = 0;
            //     $tctr = 0;
            //     $tnet = 0;
            //     $tgc = 0;
            //     $tdisc = 0;
            //     $tcharges = 0;
            //     $tvsales = 0;
            //     $ttax = 0;
            //     $tgross = 0;
            //     $counter = 0;
            //     $rcount = count($ranges);
            //     foreach($ranges as $key2 => $ran){
            //         if($ran['tc'] == 0 || $ran['net'] == 0)
            //             $avg = 0;
            //         else
            //             $avg = $ran['net']/$ran['tc'];
            //         $ctr += $ran['tc'];
      
                    
            //         // $pdf->ln();  
            //         $sheet->getCell('A'.$rc)->setValue(date('h:i A',strtotime($ran['start'])));
            //         $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            //         $sheet->getCell('B'.$rc)->setValue($ran['tg']);
            //         $sheet->getStyle('B'.$rc)->applyFromArray($styleCenter);
            //         $sheet->getCell('C'.$rc)->setValue(numInt($ran['gross']));
            //         $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
            //         $sheet->getCell('D'.$rc)->setValue(numInt($ran['vsales']));     
            //         $sheet->getStyle('D'.$rc)->applyFromArray($styleNum);
            //         $sheet->getCell('E'.$rc)->setValue(numInt($ran['vat']));     
            //         $sheet->getStyle('E'.$rc)->applyFromArray($styleNum);
            //         $sheet->getCell('F'.$rc)->setValue(numInt($ran['charges']));     
            //         $sheet->getStyle('F'.$rc)->applyFromArray($styleNum);
            //         $sheet->getCell('G'.$rc)->setValue(numInt($ran['discounts']));     
            //         $sheet->getStyle('G'.$rc)->applyFromArray($styleNum);
            //         $sheet->getCell('H'.$rc)->setValue(numInt($avg));     
            //         $sheet->getStyle('H'.$rc)->applyFromArray($styleNum);


            //         $rc++;
            //         $tctr += $ran['tc'];
            //         $tnet += $ran['net'];
            //         $tgc += $ran['tg'];
            //         $tdisc += $ran['discounts'];
            //         $tcharges += $ran['charges'];
            //         $tvsales += $ran['vsales'];
            //         $ttax += $ran['vat'];
            //         $tgross += $ran['gross'];
            //         // if($ctr == 0 || $ran['net'] == 0)
            //         //     $tavg = 0;
            //         // else
            //         //     $tavg += $ran['net']/$ctr;

            //         $counter++;
            //         $progress = ($counter / $rcount) * 100;
            //         // update_load(num($progress));

            //     }
            //     $gtctr += $tctr;
            //     $gtnet += $tnet;

            //     update_load(80);
            //     sleep(1);

            //     $sheet->getCell('A'.$rc)->setValue('TOTAL');
            //     $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
            //     $sheet->getCell('B'.$rc)->setValue($tgc);
            //     $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldCenter);
            //     $sheet->getCell('C'.$rc)->setValue(numInt($tgross));
            //     $sheet->getStyle('C'.$rc)->applyFromArray($styleBoldRight);
            //     $sheet->getCell('D'.$rc)->setValue(numInt($tvsales));     
            //     $sheet->getStyle('D'.$rc)->applyFromArray($styleBoldRight);
            //     $sheet->getCell('E'.$rc)->setValue(numInt($ttax));     
            //     $sheet->getStyle('E'.$rc)->applyFromArray($styleBoldRight);
            //     $sheet->getCell('F'.$rc)->setValue(numInt($tcharges));     
            //     $sheet->getStyle('F'.$rc)->applyFromArray($styleBoldRight);
            //     $sheet->getCell('G'.$rc)->setValue(numInt($tdisc));     
            //     $sheet->getStyle('G'.$rc)->applyFromArray($styleBoldRight);
            //     $sheet->getCell('H'.$rc)->setValue('');     
            //     $sheet->getStyle('H'.$rc)->applyFromArray($styleBoldRight);
            //     $rc++;

            // }
        
        }else{
            $rc++;
            $sheet->mergeCells('A'.$rc.':H'.$rc);
            $sheet->getCell('A'.$rc)->setValue('No Sales Found');
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        } 
        
        update_load(100);
        // ob_end_clean();
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $objWriter->save('php://output');    
    }

    public function year_to_date_rep()
    {
        $data = $this->syter->spawn('ytd_report');        
        $data['page_title'] = fa('fa-money')." Year to Date Report";
        $data['code'] = YearToDateReportUi();
        $data['add_css'] = array('css/morris/morris.css','css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css','assets/global/plugins/select2/css/select2.min.css','assets/global/plugins/select2/css/select2-bootstrap.min.css');
        $data['add_js'] = array('js/plugins/morris/morris.min.js','js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js','assets/global/plugins/select2/js/select2.full.min.js','assets/pages/scripts/components-select2.min.js');
        $data['page_no_padding'] = false;
        $data['sideBarHide'] = false;
        $data['load_js'] = 'dine/reporting';
        $data['use_js'] = 'ytdJs';
        $this->load->view('page',$data);   
    }
    public function year_to_date_gen()
    {
        $this->load->model('dine/setup_model');        
        $this->load->model("dine/menu_model");        
        
        $branch_id = $this->input->post('branch_id');
        $brand = $this->input->post('brand');        
        start_load(0);
        
        $from = $this->input->post("from");                
        $to = $this->input->post("to");                        
        $this->menu_model->db = $this->load->database('default', TRUE);        
        $from = date('Y-01-01', strtotime($from.'-01-01'));        
        $to = date("Y-m-d", strtotime($to.date("-m-d")));                                
        $this->session->set_userdata("from", date("Y", strtotime($from)));
        $this->session->set_userdata("to", date("Y", strtotime($to)));
        $param = array();
        for ($i=date("Y", strtotime($from)); $i <= date("Y", strtotime($to)); $i++) { 
            // $param[date("Y", strtotime($i."-01"))] = array("from"=>$i."-01-01", "to"=>date("Y-12-t", strtotime($i."-12-31")));            
            $param[date("Y", strtotime($i."-01"))] = array("from"=>$i."-01-01", "to"=>date("Y-m-d", strtotime($i.date("-12-t"))));            
        }        
        $this->session->set_userdata("param", $param);
        $branch_details = $this->menu_model->get_branch_details($branch_id);                      
        $this->session->set_userdata("branch_details", $branch_details);
        $trans = array();
        $counter = 0;
        $this->make->sDiv();
            $this->make->sTable(array("id"=>"main-tbl", 'class'=>'table reportTBL sortable'));
                $this->make->sTableHead();                
                    $this->make->sRow();
                    $this->make->th("Branch");
                    foreach ($param as $k => $v) {
                        $yearNum  = $k;
                        $dateObj   = DateTime::createFromFormat('!Y', $yearNum);
                        $yearName = $dateObj->format('Y');
                        $this->make->th($yearName);                    
                    }                                   
                    $this->make->eRow();
                $this->make->eTableHead();                  
                
                $this->make->sTableBody();
                $total = array();
                foreach ($branch_details as $key => $vbranch) {
                    $this->make->sRow();
                        $this->make->td($vbranch->branch_name);
                        foreach ($param as $k => $v) {
                            $amt = 0;
                            $sales = $this->menu_model->get_yearly_sales($v["from"], $v["to"], $vbranch->branch_code,$brand);
                            if(!empty($sales)){                                
                                   $this->make->td(num($sales[0]->amt), array("style"=>"text-align:right"));
                                   $amt += $sales[0]->amt;
                            }else{
                                   $this->make->td("0.00", array("style"=>"text-align:right"));                                
                            }                        
                            if(array_key_exists($k, $total)){
                                $total[$k] += $amt;
                            }else{
                                $total[$k] = $amt;                                
                            }
                        }
                    $this->make->eRow();
                }
                $this->make->sRow();
                    $this->make->th("Total");           
                    foreach ($total as $key => $value) 
                    {                        
                        $this->make->th(num($value), array("style"=>"text-align:right"));                                    
                    }         
                $this->make->eRow();
                $this->make->eTableBody();
            $this->make->eTable();
        $this->make->eDiv();
        update_load(100);
        $code = $this->make->code();
        $json['code'] = $code;        
        $json['tbl_vals'] = $trans;
        $json['dates'] = $this->input->post('date');
        echo json_encode($json);
    }
    public function ytd_rep_pdf()
    {
        // Include the main TCPDF library (search for installation path).
        require_once( APPPATH .'third_party/tcpdf.php');
        $this->load->model('dine/setup_model');     
        date_default_timezone_set('Asia/Manila');   

        // create new PDF document
        $pdf = new TCPDF("L", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('iPOS');
        $pdf->SetTitle('YTD Report');
        $pdf->SetSubject('');
        $pdf->SetKeywords('');

        // set default header data
        // $setup = $this->setup_model->get_details(1);
        // $set = $setup[0];
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "Year To Date Report", "");

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
               
        start_load(0);

        // set font
        $pdf->SetFont('helvetica', 'B', 11);

        // add a page
        $pdf->AddPage();
        
        $date = $this->input->get("from")." - ".$this->input->get("to");                               
        $this->load->model("dine/menu_model");        
                
        $this->menu_model->db = $this->load->database('default', TRUE);
        
        $pdf->Write(0, 'YEAR TO DATE REPORT', '', 0, 'L', true, 0, false, false, 0);
        $pdf->SetLineStyle(array('width' => 0.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => 'black'));
        $pdf->Cell(260, 0, '', 'T', 0, 'C');
        $pdf->ln(0.9);      
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Write(0, 'Report Period:    ', '', 0, 'L', false, 0, false, false, 0);
        $pdf->Write(0, $date, '', 0, 'L', false, 0, false, false, 0);
        $pdf->setX(210);
        $pdf->Write(0, 'Report Generated:    '.(new \DateTime())->format('Y-m-d H:i:s'), '', 0, 'L', true, 0, false, false, 0);
        // $pdf->Write(0, 'Transaction Time:    ', '', 0, 'L', false, 0, false, false, 0);
        $pdf->setX(210);
        $user = $this->session->userdata('user');
        $pdf->Write(0, 'Generated by:    '.$user["full_name"], '', 0, 'L', true, 0, false, false, 0);        
        $pdf->ln(1);      
        $pdf->Cell(260, 0, '', 'T', 0, 'C');
        $pdf->ln();              

        // echo "<pre>", print_r($trans), "</pre>";die();
           
        // -----------------------------------------------------------------------------
        $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => 'black'));        
        $param = $this->session->userdata("param");
        $brand = $_GET['brand'];
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(43, 0, "Branch", 'B', 0, 'L');
        foreach ($param as $k => $v) {
            $yearNum  = $k;
            $dateObj   = DateTime::createFromFormat('!Y', $yearNum);
            $yearName = $dateObj->format('Y');            
            $pdf->Cell(43, 0, $yearName, 'B', 0, 'C');
        }              
        $pdf->ln();     
        $pdf->SetFont('helvetica', '', 9);

        $total = array();
        $branch_details = $this->session->userdata("branch_details");
        $counter = 0;
        $progress = 0;
        $trans_count = count($branch_details);

        $brand_txt = '';

        $brd = $this->setup_model->get_brands($brand);
        if($brd){
            $brand_txt = '/' . $brd[0]->brand_name;
        }

        foreach ($branch_details as $key => $vbranch) 
        {                           
            $pdf->Cell(43, 0, $vbranch->branch_name . $brand_txt, '', 0, 'L');
            foreach ($param as $k => $v) {
                $amt = 0;
                $sales = $this->menu_model->get_yearly_sales($v["from"], $v["to"], $vbranch->branch_code,$brand);
                if(!empty($sales)){                                                       
                       $pdf->Cell(43, 0, num($sales[0]->amt), '', 0, 'R');
                       $amt += $sales[0]->amt;
                }else{
                       $pdf->Cell(43, 0, num(0), '', 0, 'R');                       
                }                        
                if(array_key_exists($k, $total)){
                    $total[$k] += $amt;
                }else{
                    $total[$k] = $amt;                                
                }
            }            
            $pdf->ln();   
            $counter++;
            $progress = ($counter / $trans_count) * 100;
            update_load(num($progress));     
        }

        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(43, 0, "Total", 'B', 0, 'L');          
        foreach ($total as $key => $value) 
        {                        
            $pdf->Cell(43, 0, num($value), 'B', 0, 'R');                                   
        }                 

        
        update_load(100);                

        // -----------------------------------------------------------------------------

        //Close and output PDF document
        $pdf->Output('ytd_report.pdf', 'I');

        //============================================================+
        // END OF FILE
        //============================================================+   
    }
    public function ytd_rep_excel()
    {

        date_default_timezone_set('Asia/Manila');
        $this->load->library('Excel');
        $sheet = $this->excel->getActiveSheet();
        $filename = 'Year To Date Report';
        $rc=1;
        #GET VALUES
        start_load(0);         
        sleep(1);        

        $date = $this->input->get("from")." - ".$this->input->get("to");                               
        $this->load->model("dine/menu_model");        
                
        $this->menu_model->db = $this->load->database('default', TRUE);

        $param = $this->session->userdata("param");
        $brand = $_GET['brand'];
        $branch_details = $this->session->userdata("branch_details");
        
        if(count($branch_details) > 0){            

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
            $styleHeaderCellNum = array(
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
                                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
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
            
            $sheet->getColumnDimension('A')->setWidth(20);
            $sheet->getColumnDimension('B')->setWidth(20);
            $sheet->getColumnDimension('C')->setWidth(20);
            $sheet->getColumnDimension('D')->setWidth(20);
            $sheet->getColumnDimension('E')->setWidth(20);
            $sheet->getColumnDimension('F')->setWidth(20);
            $sheet->getColumnDimension('G')->setWidth(20);
            $sheet->getColumnDimension('H')->setWidth(20);


            $sheet->mergeCells('A'.$rc.':H'.$rc);
            $sheet->getCell('A'.$rc)->setValue('');
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
            $rc++;

            $sheet->mergeCells('A'.$rc.':H'.$rc);
            $sheet->getCell('A'.$rc)->setValue("");
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
            $rc++;

            $sheet->mergeCells('A'.$rc.':H'.$rc);
            $sheet->getCell('A'.$rc)->setValue('YEAR TO DATE REPORT');
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $rc++;

            $sheet->mergeCells('A'.$rc.':D'.$rc);
            $sheet->getCell('A'.$rc)->setValue('Report Period: '.$date);
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->mergeCells('E'.$rc.':H'.$rc);
            $sheet->getCell('E'.$rc)->setValue('Report Generated: '.(new \DateTime())->format('Y-m-d H:i:s'));
            $sheet->getStyle('E'.$rc)->applyFromArray($styleNum);
            $rc++;

            // $sheet->mergeCells('A'.$rc.':D'.$rc);
            // $sheet->getCell('A'.$rc)->setValue('Transaction Time:');
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $user = $this->session->userdata('user');
            $sheet->mergeCells('E'.$rc.':H'.$rc);
            $sheet->getCell('E'.$rc)->setValue('Generated by:    '.$user["full_name"]);
            $sheet->getStyle('E'.$rc)->applyFromArray($styleNum);
            $rc++;      

            $col = 'A';            
            $sheet->getCell($col.$rc)->setValue("Branch");
            $sheet->getStyle($col.$rc)->applyFromArray($styleHeaderCell);
            $col++;
            foreach ($param as $k => $v) {
                $yearNum  = $k;
                $dateObj   = DateTime::createFromFormat('!Y', $yearNum);
                $yearName = $dateObj->format('Y');
                $sheet->getCell($col.$rc)->setValue($yearName);
                $sheet->getStyle($col.$rc)->applyFromArray($styleHeaderCell);
                $col++;                
            }   
            $rc++;
            $counter = 0;
            $progress = 0;
            $trans_count = count($branch_details);
            $total = array();
            $branch_details = $this->session->userdata("branch_details");

            $brd = $this->setup_model->get_brands($brand);
            $brand_txt = '';
            if($brd){
                $brand_txt = '/' . $brd[0]->brand_name;
            }

            foreach ($branch_details as $key => $vbranch) 
            {                               
                $sheet->getCell('A'.$rc)->setValue($vbranch->branch_name.$brand_txt);
                $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
                $col="B";
                foreach ($param as $k => $v) {
                    $amt = 0;
                    $sales = $this->menu_model->get_yearly_sales($v["from"], $v["to"], $vbranch->branch_code,$brand);
                    if(!empty($sales)){                                                                                       
                            $sheet->getCell($col.$rc)->setValue($sales[0]->amt);
                            $sheet->getStyle($col.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1); 
                            $amt += $sales[0]->amt;
                    }else{
                           $sheet->getCell($col.$rc)->setValue(0);
                           $sheet->getStyle($col.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1); 
                    }                        
                    if(array_key_exists($k, $total)){
                        $total[$k] += $amt;
                    }else{
                        $total[$k] = $amt;                                
                    }
                    $col++;
                }            
                $rc++;
                $counter++;
                $progress = ($counter / $trans_count) * 100;
                update_load(num($progress));     
            }

            $col = "A";
            $sheet->getCell($col.$rc)->setValue("Total");
            $sheet->getStyle($col.$rc)->applyFromArray($styleHeaderCell);                                  
            $col++;
            foreach ($total as $key => $value) 
            {                       
                $sheet->getCell($col.$rc)->setValue($value);
                $sheet->getStyle($col.$rc)->applyFromArray($styleHeaderCellNum)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1); ;                                  
                $col++;
            }  
        
        }else{
            $rc++;
            $sheet->mergeCells('A'.$rc.':H'.$rc);
            $sheet->getCell('A'.$rc)->setValue('No Sales Found');
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        } 
        
        update_load(100);
        // ob_end_clean();
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $objWriter->save('php://output');    
    }
    // End

    public function item_sales_ui()
    {
        $data = $this->syter->spawn('sales_rep');        
        $data['page_title'] = fa('fa-money')."Item Sales Report";
        $data['code'] = itemRep();
        $data['add_css'] = array('css/morris/morris.css','css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css');
        $data['add_js'] = array('js/plugins/morris/morris.min.js','js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js');
        $data['page_no_padding'] = false;
        $data['sideBarHide'] = false;
        $data['load_js'] = 'dine/reporting';
        $data['use_js'] = 'itemRepJS';
        $this->load->view('page',$data);
    }

    public function item_sales_rep_gen()
    {
        $this->load->model('dine/setup_model');
        // $this->load->database('main', TRUE);
        $this->load->model("dine/menu_model");
        
        $setup = $this->setup_model->get_details(1);
        if(!empty($setup)){
            $set = $setup[0];            
            $store_open = $set->store_open;            
        }else{
            $store_open = "00:00:00";
        }
        start_load(0);
        $menu_cat_id = $this->input->post("menu_cat_id");        
        $daterange = $this->input->post("calendar_range");        
        // $daterange = "09/10/2018 to 09/12/2018";        
        $branch_code = $this->input->post("branch_id");        
        // $branch_code = "PauliPos";
        $dates = explode(" to ",$daterange);        
        // $from = date2SqlDateTime($dates[0]);
        // $to = date2SqlDateTime($dates[1]);
        // $date = $this->input->post("date");
        // $this->menu_model->db = $this->load->database('main', TRUE);
        // $dates[0] = '2018-09-10';
        // $dates[1] = '2018-09-10';
        $from = date2SqlDateTime($dates[0]. " ".$store_open);        
        $to = date2SqlDateTime(date('Y-m-d', strtotime($dates[1] . ' +1 day')). " ".$store_open);
        
        $trans = $this->menu_model->get_item_sales($from, $to, $branch_code);
        // echo $this->db->last_query();die();
        // $trans_mod = $this->menu_model->get_mod_cat_sales_rep($from, $to, "", $branch_code);
        // echo '<pre>', print_r($trans_mod), '</pre>';die();
        $trans_count = count($trans);
        $counter = 0;
        $this->make->sDiv();
            $this->make->sTable(array("id"=>"main-tbl", 'class'=>'table reportTBL sortable'));
                $this->make->sTableHead();
                    $this->make->sRow();
                        $this->make->th('Branch');
                        $this->make->th('Transaction Date');
                        $this->make->th('Item Code');
                        $this->make->th('Item Description');                        
                        $this->make->th('Category');
                        $this->make->th('Subc Category');
                        $this->make->th('Quantity Sold');                        
                        $this->make->th('Selling Price');
                        $this->make->th('Total');                        
                    $this->make->eRow();
                $this->make->eTableHead();
                $this->make->sTableBody();
                    $tot_qty = 0;                                        
                    $total = 0;   
                    $tot_vat_sales = 0;
                    $tot_vat = 0;
                    $tot_gross = 0;
                    $tot_mod_gross = 0;
                    $tot_sales_prcnt = 0;
                    $tot_cost = 0;
                    $tot_cost_prcnt = 0; 
                    $tot_margin = 0;
                    $counter = 0;
                    $progress = 0;
                    $trans_count = count($trans);
                    // echo print_r($trans);die();
                    
                    // foreach ($trans_mod as $vv) {
                    //     $tot_mod_gross += $vv->mod_gross;
                    // }                                          
                    
                    foreach ($trans as $res) {
                        $tot_gross += $res->item_gross;
                        $tot_cost += 0;

                        if($counter % 500 == 0){
                            set_time_limit(60);
                        }

                        $this->make->sRow();
                            $this->make->td($res->branch_code);
                            $this->make->td(sql2Date($res->date));
                            $this->make->td($res->code);
                            $this->make->td($res->item_name);                            
                            $this->make->td($res->cat_name);                                                       
                            $this->make->td($res->sub_cat_name);                                                       
                            $this->make->td(num($res->tot_qty), array("style"=>"text-align:right"));                                                        
                            $this->make->td(num($res->price), array("style"=>"text-align:right"));                            
                            $this->make->td(num($res->tot_qty * $res->price), array("style"=>"text-align:right"));                            
                        $this->make->eRow();

                        // Grand Total
                        $tot_qty += $res->tot_qty;                        
                        $total += $res->tot_qty * $res->price;                        

                        $counter++;
                        $progress = ($counter / $trans_count) * 100;
                        update_load(num($progress));

                    }    
                    $this->make->sRow();
                        $this->make->th('Grand Total');
                        $this->make->th("", array("style"=>"text-align:right"));                        
                        $this->make->th("", array("style"=>"text-align:right"));
                        $this->make->th("", array("style"=>"text-align:right"));
                        $this->make->th("", array("style"=>"text-align:right"));
                        $this->make->th("", array("style"=>"text-align:right"));
                        $this->make->th(num($tot_qty), array("style"=>"text-align:right"));
                        $this->make->th("", array("style"=>"text-align:right"));                                                
                        $this->make->th(num($total), array("style"=>"text-align:right"));                        
                    $this->make->eRow();                                 

                    $this->make->sRow();
                        ///////////fpr payments
                        // $this->cashier_model->db = $this->load->database('main', TRUE);
                        $args = array();
                        // if($user)
                        //     $args["trans_sales.user_id"] = array('use'=>'where','val'=>$user,'third'=>false);

                        $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
                        $args["trans_sales.inactive = 0"] = array('use'=>'where','val'=>null,'third'=>false);
                        $args["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);
                        // $args["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);

                        // $terminal = TERMINAL_ID;
                        // $args['trans_sales.terminal_id'] = $terminal;
                        // if($menu_cat_id != 0){
                        //     $args["menu_categories.menu_cat_id"] = array('use'=>'where','val'=>$menu_cat_id,'third'=>false);
                        // }


                        // $post = $this->set_post();
                        // $curr = $this->search_current();
                        $curr = false;
                        $trans = $this->trans_sales($args,$curr,$branch_code);
                        // $trans = $this->trans_sales_cat($args,false);
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

                        $loc_txt = numInt(($local_tax));
                        $net_no_adds = $net-($charges+$local_tax);
                        $nontaxable = $no_tax - $no_tax_disc;
                        $taxable =   ($gross - $discounts - $less_vat - $nontaxable) / 1.12;
                        $total_net = ($taxable) + ($nontaxable+$zero_rated) + $tax + $local_tax;
                        $add_gt = $taxable+$nontaxable+$zero_rated;
                        $nsss = $taxable +  $nontaxable +  $zero_rated;

                        $vat_ = $taxable * .12;

                        $breakdown = $this->menu_model->get_item_sales_breakdown($from, $to, $branch_code);
                        $taxable = 0;
                        $vat_ = 0;
                        $nontaxable = 0;
                        $zero_rated = 0;

                        if($breakdown){
                            $taxable = $tot_gross - $breakdown[0]->vat_exempt-$breakdown[0]->vat;
                            $vat_ = $breakdown[0]->vat;
                            $nontaxable = $breakdown[0]->vat_exempt;
                        }

                        $this->make->sRow();                        
                            $this->make->td("GROSS");
                            $this->make->td(num($tot_gross + $tot_mod_gross),array("style"=>"text-align:right"));                        
                        $this->make->sRow();
                        $this->make->sRow();                        
                            $this->make->td("VAT SALES");
                            $this->make->td(num($taxable),array("style"=>"text-align:right"));                        
                        $this->make->sRow();
                        $this->make->sRow();                        
                            $this->make->td("VAT");
                            $this->make->td(num($vat_),array("style"=>"text-align:right"));                        
                        $this->make->sRow();
                        $this->make->sRow();                        
                            $this->make->td("VAT EXEMPT SALES");
                            $this->make->td(num($nontaxable-$zero_rated),array("style"=>"text-align:right"));                        
                        $this->make->sRow();
                        $this->make->sRow();                        
                            $this->make->td("ZERO RATED");
                            $this->make->td(num($zero_rated),array("style"=>"text-align:right"));                        
                        $this->make->sRow();
                        
                    $this->make->eRow();

                $this->make->eTableBody();
            $this->make->eTable();
        $this->make->eDiv();
        update_load(100);
        $code = $this->make->code();
        $json['code'] = $code;        
        $json['tbl_vals'] = $trans;
        $json['dates'] = $this->input->post('calendar_range');

        header_remove('Set-Cookie');

        echo json_encode($json);
    }

    public function item_sales_rep_excel()
    {
        // $this->menu_model->db = $this->load->database('main', TRUE);
        $this->load->model("dine/menu_model");
        date_default_timezone_set('Asia/Manila');
        $this->load->library('Excel');

        $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
        $cacheSettings = ['memoryCacheSize' => '128MB'];
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

        $sheet = $this->excel->getActiveSheet();
        $filename = 'Item Sales Report';
        $rc=1;
        #GET VALUES
        start_load(0);
            // $post = $this->set_post($_GET['calendar_range']);
        $setup = $this->setup_model->get_details(1);
        if(!empty($setup)){
            $set = $setup[0];            
            $store_open = $set->store_open;
        }else{
            $store_open = "00:00:00";            
        }

        // update_load(10);
        sleep(1);
        
        $daterange = $_GET['calendar_range'];                      
        $dates = explode(" to ",$daterange);        
        // $from = date2SqlDateTime($dates[0]);
        // $to = date2SqlDateTime($dates[1]);
        $branch_code = $this->input->post("branch_id");         
        $from = date2SqlDateTime($dates[0]. " ".$store_open);        
        $to = date2SqlDateTime(date('Y-m-d', strtotime($dates[1] . ' +1 day')). " ".$store_open);        
        $trans = $this->menu_model->get_item_sales($from, $to, $branch_code); 
        $trans_mod = $this->menu_model->get_mod_cat_sales_rep($from, $to, "", $branch_code);       

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
        
        $headers = array('Branch', 'Transaction Date', 'Item Code','Item Description','Category','Sub Category','Quantity Sold','Price', 'Total');
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(20);


        $sheet->mergeCells('A'.$rc.':H'.$rc);
        // $sheet->getCell('A'.$rc)->setValue($branch_code);
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        $rc++;

        $sheet->mergeCells('A'.$rc.':G'.$rc);
        // $sheet->getCell('A'.$rc)->setValue($set->address);        
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        $rc++;

        $sheet->mergeCells('A'.$rc.':G'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Sales Report');
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
        $total = 0;
        $tot_vat_sales = 0;
        $tot_vat = 0;
        $tot_gross = 0;
        $tot_mod_gross = 0;
        $tot_sales_prcnt = 0;
        $tot_cost = 0;
        $tot_cost_prcnt = 0; 
        $tot_margin = 0;
        $counter = 0;
        $progress = 0;
        $trans_count = count($trans);
        // echo print_r($trans);die();
       
        foreach ($trans_mod as $vv) {
            $tot_mod_gross += $vv->mod_gross;
        }        

        foreach ($trans as $k => $v) {
            $tot_gross += $v->item_gross;
            $tot_cost += 0;

            if($counter % 500 == 0){
                set_time_limit(60);
            }


            $sheet->getCell('A'.$rc)->setValue($v->branch_code);
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue($v->date);
            $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('C'.$rc)->setValue($v->code);
            $sheet->getStyle('C'.$rc)->applyFromArray($styleTxt);            
            $sheet->getCell('D'.$rc)->setValue(num($v->item_name));     
            $sheet->getStyle('D'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('E'.$rc)->setValue($v->cat_name);     
            $sheet->getStyle('E'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('F'.$rc)->setValue($v->sub_cat_name);     
            $sheet->getStyle('F'.$rc)->applyFromArray($styleTxt);            
            $sheet->getCell('G'.$rc)->setValue(num($v->tot_qty));                                 
            $sheet->getStyle('G'.$rc)->applyFromArray($styleNum);
            $sheet->getCell('H'.$rc)->setValue(num($v->price));     
            $sheet->getStyle('H'.$rc)->applyFromArray($styleNum);
            $sheet->getCell('I'.$rc)->setValue(num($v->tot_qty * $v->price));     
            $sheet->getStyle('I'.$rc)->applyFromArray($styleNum);       

            // Grand Total
            $tot_qty += $v->tot_qty;
            $total += $v->tot_qty * $v->price;
            
            $tot_sales_prcnt = 0;
            // $tot_cost += $v->cost;
            $tot_cost_prcnt = 0;

            $counter++;
            $progress = ($counter / $trans_count) * 100;
            update_load(num($progress));   
            $rc++;           
        }

        $sheet->getCell('A'.$rc)->setValue('Grand Total');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('F'.$rc)->setValue(num($tot_qty));
        $sheet->getStyle('F'.$rc)->applyFromArray($styleBoldRight);
        $sheet->getCell('H'.$rc)->setValue(num($total));     
        $sheet->getStyle('H'.$rc)->applyFromArray($styleBoldRight);
        $rc++;

        ///////////fpr payments
        // $this->cashier_model->db = $this->load->database('main', TRUE);
        $args = array();
        // if($user)
        //     $args["trans_sales.user_id"] = array('use'=>'where','val'=>$user,'third'=>false);

        $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.inactive = 0"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);

        // if($menu_cat_id != 0){
        //     $args["menu_categories.menu_cat_id"] = array('use'=>'where','val'=>$menu_cat_id,'third'=>false);
        // }


        $post = $this->set_post();
        // $curr = $this->search_current();
        $curr = false;
        $trans = $this->trans_sales($args,$curr);
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

        $loc_txt = numInt(($local_tax));
        $net_no_adds = $net-($charges+$local_tax);
        $nontaxable = $no_tax - $no_tax_disc;
        $taxable =   ($gross - $discounts - $less_vat - $nontaxable) / 1.12;
        $total_net = ($taxable) + ($nontaxable+$zero_rated) + $tax + $local_tax;
        $add_gt = $taxable+$nontaxable+$zero_rated;
        $nsss = $taxable +  $nontaxable +  $zero_rated;

        $vat_ = $taxable * .12;

        $breakdown = $this->menu_model->get_item_sales_breakdown($from, $to, $branch_code);
        $taxable = 0;
        $vat_ = 0;
        $nontaxable = 0;
        $zero_rated = 0;

        if($breakdown){
            $taxable = $tot_gross - $breakdown[0]->vat_exempt-$breakdown[0]->vat;
            $vat_ = $breakdown[0]->vat;
            $nontaxable = $breakdown[0]->vat_exempt;
        }

        $rc++;
        $sheet->getCell('A'.$rc)->setValue('GROSS');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($tot_gross + $tot_mod_gross));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue('VAT SALES');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($taxable));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue('VAT');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($vat_));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue('VAT EXEMPT SALES');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($nontaxable-$zero_rated));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue('ZERO RATED');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($zero_rated));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

        // //MENU SUB CAT
        $rc++; $rc++;
        $sheet->getCell('A'.$rc)->setValue('SUB CATEGORIES');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue('AMOUNT');
        $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldRight);

        $subcats = $trans_menus['sub_cats'];
        $total = 0;
        foreach ($subcats as $id => $val) {
            $rc++;
            $sheet->getCell('A'.$rc)->setValue(strtoupper($val['name']));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue(num($val['amount']));
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
            $total += $val['amount'];
        }
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('SubTotal'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($total));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

        $rc++;
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('A'.$rc)->setValue(strtoupper('MODIFIERS TOTAL'));
        $sheet->getCell('B'.$rc)->setValue(num($trans_menus['mods_total']));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('TOTAL'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($total + $trans_menus['mods_total']));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

        //DISCOUNTS
        $rc++; $rc++;
        $sheet->getCell('A'.$rc)->setValue('DISCOUNT');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue('AMOUNT');
        $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldRight);

        $types = $trans_discounts['types'];
        foreach ($types as $code => $val) {
            $rc++;
            $sheet->getCell('A'.$rc)->setValue(strtoupper($val['name']));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue(num($val['amount']));
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
            
        }

        $less_vat = $breakdown[0]->exempt_discount;

        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('Total'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($discounts));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('VAT EXEMPT'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($less_vat));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);


        // //CAHRGES
        $rc++; $rc++;
        $sheet->getCell('A'.$rc)->setValue('CHARGES');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue('AMOUNT');
        $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldRight);

        $types = $trans_charges['types'];
        foreach ($types as $code => $val) {
            $rc++;
            $sheet->getCell('A'.$rc)->setValue(strtoupper($val['name']));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue(num($val['amount']));
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
            
        }
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('Total'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($charges));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

        // //PAYMENTS
        $rc++; $rc++;
        $sheet->getCell('A'.$rc)->setValue('PAYMENT MODE');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue('PAYMENT AMOUNT');
        $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldRight);

        $payments_types = $payments['types'];
        $payments_total = $payments['total'];

        $pay_amount = $tot_gross + $charges - $discounts - $less_vat;
        foreach ($payments_types as $code => $val) {
            $rc++;

            if($pay_amount > $val['amount']){
                $sheet->getCell('A'.$rc)->setValue(strtoupper($code));
                $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
                $sheet->getCell('B'.$rc)->setValue(num($val['amount']));
                $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
            }else{
                $sheet->getCell('A'.$rc)->setValue(strtoupper($code));
                $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
                $sheet->getCell('B'.$rc)->setValue(num($pay_amount));
                $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
            }           
             
            $pay_amount -= $val['amount'];
           
        }

        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('Total'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        // $sheet->getCell('B'.$rc)->setValue(num($payments_total));
        $sheet->getCell('B'.$rc)->setValue(num($tot_gross + $charges - $discounts - $less_vat));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

        if($trans['total_chit']){
            $rc++; $rc++;
            $sheet->getCell('A'.$rc)->setValue(strtoupper('Total Chit'));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
            $sheet->getCell('B'.$rc)->setValue(num($trans['total_chit']));
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
        }


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
        $rc++;
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('Trans Count'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $tc_total  = 0;
        $tc_qty = 0;

        $pay_amount = $tot_gross + $charges - $discounts - $less_vat;
        foreach ($types_total as $typ => $tamnt) {
            $rc++;
            $sheet->getCell('A'.$rc)->setValue(strtoupper($typ));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue(count($types[$typ]));
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);

            if($pay_amount > $tamnt){
                $sheet->getCell('C'.$rc)->setValue(num($tamnt));
                $pay_amount -= $tamnt;
            }else{
                $sheet->getCell('C'.$rc)->setValue(num($pay_amount));
            }

            $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
            $tc_total += $tamnt;
            $tc_qty += count($types[$typ]);
        }
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(strtoupper('TC TOTAL'));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        // $sheet->getCell('C'.$rc)->setValue(num($tc_total));
        $sheet->getCell('C'.$rc)->setValue(num($tot_gross + $charges - $discounts - $less_vat));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        
        update_load(100);        
       
        if (ob_get_contents())
        ob_end_clean();

        header_remove('Set-Cookie');

        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $objWriter->save('php://output');

        //============================================================+
        // END OF FILE
        //============================================================+   
    }
    public function item_sales_rep_pdf()
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
        $pdf->SetTitle('Item Sales Report');
        $pdf->SetSubject('');
        $pdf->SetKeywords('');

        // set default header data
        $branch_code = $this->input->post("branch_id");
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
        
        $daterange = $_GET['calendar_range'];        
        $dates = explode(" to ",$daterange);        
        $from = date2SqlDateTime($dates[0]. " ".$set->store_open);        
        $to = date2SqlDateTime(date('Y-m-d', strtotime($dates[1] . ' +1 day')). " ".$set->store_open);
        $trans = $this->menu_model->get_item_sales($from, $to);        
        // $trans_mod = $this->menu_model->get_mod_cat_sales_rep($from, $to, "");


        $pdf->Write(0, 'Item Sales Report', '', 0, 'L', true, 0, false, false, 0);
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

        // echo "<pre>", print_r($trans), "</pre>";die();

        // -----------------------------------------------------------------------------
        $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => 'black'));
        $pdf->Cell(25, 0, 'Branch', 'B', 0, 'L');        
        $pdf->Cell(25, 0, 'Trans Date', 'B', 0, 'L');        
        $pdf->Cell(30, 0, 'Item Code', 'B', 0, 'L');                
        $pdf->Cell(85, 0, 'Item Description', 'B', 0, 'L');        
        $pdf->Cell(25, 0, 'Category', 'B', 0, 'L');        
        $pdf->Cell(16, 0, 'Sub Cat', 'B', 0, 'L');        
        $pdf->Cell(20, 0, 'Qty Sold', 'B', 0, 'R');        
        $pdf->Cell(20, 0, 'Selling Price', 'B', 0, 'R');        
        $pdf->Cell(20, 0, 'Total', 'B', 0, 'R');        
        $pdf->ln();                  

        // GRAND TOTAL VARIABLES
        $tot_qty = 0;
        $total = 0;
        $tot_vat_sales = 0;
        $tot_vat = 0;
        $tot_gross = 0;
        $tot_mod_gross = 0;
        $tot_sales_prcnt = 0;
        $tot_cost = 0;
        $tot_cost_prcnt = 0; 
        $tot_margin = 0;
        $counter = 0;
        $progress = 0;
        $trans_count = count($trans);

       
        foreach ($trans as $k => $v) {
            $tot_gross += $v->item_gross;
            $tot_cost += 0;

            if($counter % 500 == 0){
                set_time_limit(60);
            }

            $pdf->Cell(25, 0, sql2Date($v->date), '', 0, 'L');        
            $pdf->Cell(30, 0, $v->code, '', 0, 'L'); 

            if (strlen($v->item_name)) {
                $width = 110;
                $font_size = 9;
                do {
                    $font_size -= 0.5;
                    $string_font_width = $pdf->GetStringWidth(ucwords(strtolower($v->item_name)),'helvetica','',$font_size) + 3; # +4 Not exactly sure but works
                } while ($string_font_width > $width);

                // $pdf->SetFont('dejavb','',$font_size);
                $pdf->SetFont('helvetica', '', $font_size);
               
            }                   
            $pdf->Cell(110, 0, $v->item_name, '', 0, 'L');        
            $pdf->SetFont('helvetica', '', 9);

            if (strlen($v->cat_name)) {
                $width = 25;
                $font_size = 9;
                do {
                    $font_size -= 0.5;
                    $string_font_width = $pdf->GetStringWidth(ucwords(strtolower($v->cat_name)),'helvetica','',$font_size) + 3; # +4 Not exactly sure but works
                } while ($string_font_width > $width);

                // $pdf->SetFont('dejavb','',$font_size);
                $pdf->SetFont('helvetica', '', $font_size);
               
            }
            $pdf->Cell(25, 0, $v->cat_name, '', 0, 'L');
            $pdf->SetFont('helvetica', '', 9);

            if (strlen($v->sub_cat_name)) {
                $width = 16;
                $font_size = 9;
                do {
                    $font_size -= 0.5;
                    $string_font_width = $pdf->GetStringWidth(ucwords(strtolower($v->sub_cat_name)),'helvetica','',$font_size) + 3; # +4 Not exactly sure but works
                } while ($string_font_width > $width);

                // $pdf->SetFont('dejavb','',$font_size);
                $pdf->SetFont('helvetica', '', $font_size);
               
            }
            $pdf->Cell(16, 0, $v->sub_cat_name, '', 0, 'L');
            $pdf->SetFont('helvetica', '', 9);

            $pdf->Cell(20, 0, num($v->tot_qty), '', 0, 'R');                                
            $pdf->Cell(20, 0, num($v->price), '', 0, 'R');                                    
            $pdf->Cell(20, 0, num($v->tot_qty * $v->price), '', 0, 'R');        
            $pdf->ln();                

            // Grand Total
            $tot_qty += $v->tot_qty;
            $total += $v->tot_qty * $v->price;    

           
            // echo print_r($trans);die();
            // foreach ($trans_mod as $vv) {
            //     $tot_mod_gross += $vv->mod_gross;
            // }

            $counter++;
            $progress = ($counter / $trans_count) * 100;
            update_load(num($progress));              
        }
        update_load(100);        
        $pdf->Cell(25, 0, "Grand Total", 'T', 0, 'L');        
        $pdf->Cell(25, 0, "", 'T', 0, 'L');        
        $pdf->Cell(30, 0, "", 'T', 0, 'R');                
        $pdf->Cell(85, 0, "", 'T', 0, 'R');        
        $pdf->Cell(25, 0, "", 'T', 0, 'R');        
        $pdf->Cell(16, 0, "", 'T', 0, 'R');        
        $pdf->Cell(20, 0, num($tot_qty), 'T', 0, 'R');        
        $pdf->Cell(20, 0, "", 'T', 0, 'R'); 
        $pdf->Cell(20, 0, num($total), 'T', 0, 'R'); 

        ///////////fpr payments
        // $this->cashier_model->db = $this->load->database('main', TRUE);
        $args = array();
        // if($user)
        //     $args["trans_sales.user_id"] = array('use'=>'where','val'=>$user,'third'=>false);

        $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.inactive = 0"] = array('use'=>'where','val'=>null,'third'=>false);
        $args["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);
        // $args["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);

        // $terminal = TERMINAL_ID;
        // $args['trans_sales.terminal_id'] = $terminal;
        // if($menu_cat_id != 0){
        //     $args["menu_categories.menu_cat_id"] = array('use'=>'where','val'=>$menu_cat_id,'third'=>false);
        // }


        // $post = $this->set_post();
        // $curr = $this->search_current();
        $curr = false;
        $trans = $this->trans_sales($args,$curr);
        // $trans = $this->trans_sales_cat($args,false);
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

        $loc_txt = numInt(($local_tax));
        $net_no_adds = $net-($charges+$local_tax);
        $nontaxable = $no_tax - $no_tax_disc;
        $taxable =   ($gross - $discounts - $less_vat - $nontaxable) / 1.12;
        $total_net = ($taxable) + ($nontaxable+$zero_rated) + $tax + $local_tax;
        $add_gt = $taxable+$nontaxable+$zero_rated;
        $nsss = $taxable +  $nontaxable +  $zero_rated;

        $vat_ = $taxable * .12;

        $breakdown = $this->menu_model->get_item_sales_breakdown($from, $to, $branch_code);
        $taxable = 0;
        $vat_ = 0;
        $nontaxable = 0;
        $zero_rated = 0;

        if($breakdown){
            $taxable = $tot_gross - $breakdown[0]->vat_exempt-$breakdown[0]->vat;
            $vat_ = $breakdown[0]->vat;
            $nontaxable = $breakdown[0]->vat_exempt;
        }

        $pdf->ln(7);
        $pdf->Cell(30, 0, 'GROSS', '', 0, 'L');
        $pdf->Cell(35, 0, num($tot_gross + $tot_mod_gross), '', 0, 'R');
        $pdf->ln();
        $pdf->Cell(30, 0, 'VAT SALES', '', 0, 'L');
        $pdf->Cell(35, 0, num($taxable), '', 0, 'R');
        $pdf->ln();
        $pdf->Cell(30, 0, 'VAT', '', 0, 'L');
        $pdf->Cell(35, 0, num($vat_), '', 0, 'R');
        $pdf->ln();
        $pdf->Cell(30, 0, 'VAT EXEMPT SALES', '', 0, 'L');
        $pdf->Cell(35, 0, num($nontaxable-$zero_rated), '', 0, 'R');
        $pdf->ln();
        $pdf->Cell(30, 0, 'ZERO RATED', '', 0, 'L');
        $pdf->Cell(35, 0, num($zero_rated), '', 0, 'R');


        //MENU SUB CAT
        $pdf->ln(7);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(30, 0, strtoupper('Sub Categories'), '', 0, 'L');
        $pdf->Cell(35, 0, strtoupper('Amount'), '', 0, 'R');
        $pdf->SetFont('helvetica', '', 9);

        $subcats = $trans_menus['sub_cats'];
        $qty = 0;
        $total = 0;
        foreach ($subcats as $id => $val) {
            $pdf->ln();
            $pdf->Cell(30, 0, strtoupper($val['name']), '', 0, 'L');
            $pdf->Cell(35, 0, num($val['amount']), '', 0, 'R');
            $total += $val['amount'];
        }

        $pdf->ln();
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(30, 0,'SUBTOTAL ', 'T', 0, 'L');
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell(35, 0, num($total), 'T', 0, 'R');

        // numInt($trans_menus['mods_total'])
        $pdf->ln();
        // $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(30, 0,'MODIFIERS TOTAL ', '', 0, 'L');
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell(35, 0, num($trans_menus['mods_total']), '', 0, 'R');

        $pdf->ln();
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(30, 0,'TOTAL ', 'T', 0, 'L');
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell(35, 0, num($total + $trans_menus['mods_total']), 'T', 0, 'R');


        //DISCOUNTS
        $pdf->ln(7);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(60, 0, strtoupper('Discount'), '', 0, 'L');
        $pdf->Cell(35, 0, strtoupper('Amount'), '', 0, 'R');
        $pdf->SetFont('helvetica', '', 9);

        $types = $trans_discounts['types'];
        foreach ($types as $code => $val) {
            $pdf->ln();
            $pdf->Cell(60, 0, strtoupper($val['name']), '', 0, 'L');
            $pdf->Cell(35, 0, num($val['amount']), '', 0, 'R');
            
        }

        $less_vat = $breakdown[0]->exempt_discount;

        $pdf->ln();
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(60, 0,'TOTAL ', 'T', 0, 'L');
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell(35, 0, num($discounts), 'T', 0, 'R');
        $pdf->ln();
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(60, 0,'VAT EXEMPT ', '', 0, 'L');
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell(35, 0, num($less_vat), '', 0, 'R');


        //CAHRGES
        $pdf->ln(7);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(30, 0, strtoupper('Charges'), '', 0, 'L');
        $pdf->Cell(35, 0, strtoupper('Amount'), '', 0, 'R');
        $pdf->SetFont('helvetica', '', 9);
        // $pdf->ln();

        $types = $trans_charges['types'];
        foreach ($types as $code => $val) {
            $pdf->ln();
            $pdf->Cell(30, 0, strtoupper($val['name']), '', 0, 'L');
            $pdf->Cell(35, 0, num($val['amount']), '', 0, 'R');
            
        }
           
        $pdf->ln();
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(30, 0,'TOTAL ', 'T', 0, 'L');
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell(35, 0, num($charges), 'T', 0, 'R');


        //PAYMENTS
        $pdf->ln(7);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(30, 0, strtoupper('Payment Mode'), '', 0, 'L');
        $pdf->Cell(35, 0, strtoupper('Payment Amount'), '', 0, 'R');
        $pdf->SetFont('helvetica', '', 9);
        // $pdf->ln();


        $payments_types = $payments['types'];
        $payments_total = $payments['total'];

        $pay_amount = $tot_gross + $charges - $discounts - $less_vat;
        foreach ($payments_types as $code => $val) {
        $pdf->ln();
            $pdf->Cell(30, 0, strtoupper($code), '', 0, 'L');
            if($pay_amount > $val['amount']){
                $pdf->Cell(35, 0, num($val['amount']), '', 0, 'R');
                $pay_amount -= $val['amount'];
            }else{
                $pdf->Cell(35, 0, num($pay_amount), '', 0, 'R');
            }    
            
        }
           
        $pdf->ln();
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(30, 0,'TOTAL ', 'T', 0, 'L');
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell(35, 0, num($tot_gross + $charges - $discounts - $less_vat), 'T', 0, 'R');


        // if($trans['total_chit']){
        //     // $print_str .= append_chars(substrwords('TOTAL CHIT',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
        //     //               .append_chars(numInt($trans['total_chit']),"left",PAPER_RD_COL_3_3," ")."\r\n";
        //     // $print_str .= "\r\n";
        //     $pdf->ln(7);
        //     $pdf->SetFont('helvetica', 'B', 9);
        //     $pdf->Cell(30, 0,'TOTAL CHIT ', '', 0, 'L');
        //     $pdf->SetFont('helvetica', '', 9);
        //     $pdf->Cell(35, 0, num($trans['total_chit']), '', 0, 'R');
        // }



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

        $pdf->ln(7);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(30, 0,'TRANS COUNT ', '', 0, 'L');
        $tc_total  = 0;
        $tc_qty = 0;

        $pay_amount = $tot_gross + $charges - $discounts - $less_vat;
        foreach ($types_total as $typ => $tamnt) {
            $pdf->SetFont('helvetica', '', 9);
            $pdf->ln();
            $pdf->Cell(30, 0, strtoupper($typ), '', 0, 'L');
            $pdf->Cell(20, 0, count($types[$typ]), '', 0, 'L');
            if($pay_amount > $tamnt){
                $pdf->Cell(25, 0, num($tamnt), '', 0, 'R');
                $pay_amount -= $tamnt;
            }else{
                $pdf->Cell(25, 0, num($pay_amount), '', 0, 'R');
            }
            $tc_total += $tamnt;
            $tc_qty += count($types[$typ]);
        }
        $pdf->ln();
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(50, 0, 'TC TOTAL', 'T', 0, 'L');
            $pdf->SetFont('helvetica', '', 9);
        // $pdf->Cell(20, 0, count($types[$typ]), '', 0, 'L');
        // $pdf->Cell(25, 0, num($tc_total), 'T', 0, 'R');
        $pdf->Cell(25, 0, num($tot_gross + $charges - $discounts - $less_vat), 'T', 0, 'R');
        

        // -----------------------------------------------------------------------------

        //Close and output PDF document
        header_remove('Set-Cookie');

        $pdf->Output('item_sales_report.pdf', 'I');

        //============================================================+
        // END OF FILE
        //============================================================+   
    }

     public function void_sales_rep(){
        $data = $this->syter->spawn('sales_rep');        
        $data['page_title'] = fa('fa-money')." Voided Sales Report";
        $data['code'] = voidedSalesRep();
        $data['add_css'] = array('css/morris/morris.css','css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css','assets/global/plugins/select2/css/select2.min.css','assets/global/plugins/select2/css/select2-bootstrap.min.css');
        $data['add_js'] = array('js/plugins/morris/morris.min.js','js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js','assets/global/plugins/select2/js/select2.full.min.js','assets/pages/scripts/components-select2.min.js');
        $data['page_no_padding'] = false;
        $data['sideBarHide'] = false;
        $data['load_js'] = 'dine/reporting';
        $data['use_js'] = 'voidSalesRepJS';
        $this->load->view('page',$data);
    }

    public function voided_sales_rep_gen()
    {
        $this->load->model('dine/setup_model');
        $this->load->model("dine/menu_model");
        $menu_cat_id = null;
        $branch_id = $this->input->post('branch_id');
        $brand = $this->input->post('brand');
        $menu_cat_raw = $this->input->post("menu_cat_id");        
        if($menu_cat_raw !=""){
            $raw_data = explode("--",$menu_cat_raw);
            $menu_cat_id = $raw_data[0];
            $branch_id = $raw_data[1];
        }
        $setup = $this->setup_model->get_details($branch_id);
        $set = $setup[0];
        start_load(0);

        $daterange = $this->input->post("calendar_range");   
        $dates = explode(" to ",$daterange);

        $this->menu_model->db = $this->load->database('default', TRUE);
        $from = date2SqlDateTime($dates[0]. " ".$set->store_open);        
        $to = date2SqlDateTime(date('Y-m-d', strtotime($dates[1] . ' +1 day')). " ".$set->store_open);
    
        $trans = $this->menu_model->get_voided_cat_sales_rep($from, $to, $menu_cat_id, $branch_id,$brand);  
        $trans_ret = $this->menu_model->get_voided_cat_sales_rep_retail($from, $to, $menu_cat_id, $branch_id,$brand);

        $trans_count = count($trans);
        $trans_count_ret = count($trans_ret);
        $counter = 0;
        $this->make->sDiv();
            $this->make->sTable(array("id"=>"main-tbl", 'class'=>'table reportTBL sortable'));
                $this->make->sTableHead();
                    $this->make->sRow();
                        if($branch_id == ''){
                            $this->make->th('Branch');
                        }
                        $this->make->th('Category');
                        $this->make->th('Qty');
                        $this->make->th('Gross');
                        $this->make->th('Sales (%)');
                        $this->make->th('Cost');
                        $this->make->th('Cost (%)');
                        $this->make->th('Margin');
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
                    $tot_cost_prcnt = 0;
                    foreach ($trans as $v) {
                        $tot_gross += $v->gross;
                        $tot_cost += $v->cost;
                    }
                    foreach ($trans as $res) {
                        $this->make->sRow();
                            if($branch_id == ''){
                                $this->make->td($res->branch_name);
                            }
                            $this->make->td($res->menu_cat_name);
                            $this->make->td(num($res->qty), array("style"=>"text-align:right"));                         
                            $this->make->td(num($res->gross), array("style"=>"text-align:right"));                            
                            $this->make->td(num($res->gross / $tot_gross * 100). "%", array("style"=>"text-align:right"));                            
                            $this->make->td(num($res->cost), array("style"=>"text-align:right"));                            
                            if($tot_cost != 0){
                                $this->make->td(num($res->cost / $tot_cost * 100). "%", array("style"=>"text-align:right"));
                            }else{
                                $this->make->td("0.00%", array("style"=>"text-align:right"));                                
                            }
                            $this->make->td(num($res->gross - $res->cost), array("style"=>"text-align:right"));                         
                        $this->make->eRow();

                         // Grand Total
                        $tot_qty += $res->qty;
                        $tot_vat_sales += $res->vat_sales;
                        $tot_vat += $res->vat;
                        $tot_sales_prcnt = 0;
                        $tot_margin += $res->gross - $res->cost;
                        $tot_cost_prcnt = 0;

                        $counter++;
                        $progress = ($counter / $trans_count) * 100;
                        update_load(num($progress));

                    }    
                    $this->make->sRow();
                        $this->make->th('Grand Total');
                        if($branch_id == ''){
                            $this->make->th('  ');
                        }
                        $this->make->th(num($tot_qty), array("style"=>"text-align:right"));
                        $this->make->th(num($tot_gross), array("style"=>"text-align:right"));
                        $this->make->th("", array("style"=>"text-align:right"));
                        $this->make->th(num($tot_cost), array("style"=>"text-align:right"));
                        $this->make->th("", array("style"=>"text-align:right"));                                                
                        $this->make->th(num($tot_margin), array("style"=>"text-align:right"));                     
                    $this->make->eRow();                                 
                $this->make->eTableBody();
            $this->make->eTable();
        $this->make->eDiv();
        $this->make->append('<center><h4>Retail Items</h4></center>');
        $this->make->sDiv();
            $this->make->sTable(array("id"=>"main-tbl", 'class'=>'table reportTBL sortable'));
                $this->make->sTableHead();
                    $this->make->sRow();
                        if($branch_id == ''){
                            $this->make->th('Branch');
                        }
                        $this->make->th('Category');
                        $this->make->th('Qty');
                        $this->make->th('Gross');
                        $this->make->th('Sales (%)');
                        $this->make->th('Cost');
                        $this->make->th('Cost (%)');
                        $this->make->th('Margin');
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
                    $tot_cost_prcnt = 0;
                    foreach ($trans_ret as $v) {
                        $tot_gross += $v->gross;
                    }
                    foreach ($trans_ret as $res) {
                        $this->make->sRow();
                            if($branch_id == ''){
                                $this->make->td($res->branch_code);
                            }
                            $this->make->td($res->name);
                            $this->make->td(num($res->qty), array("style"=>"text-align:right"));                       
                            $this->make->td(num($res->gross), array("style"=>"text-align:right"));                            
                            $this->make->td(num($res->gross / $tot_gross * 100). "%", array("style"=>"text-align:right"));                                       
                            $this->make->td(num(0), array("style"=>"text-align:right"));                            
                            if($tot_cost != 0){
                                $this->make->td(num($res->cost / $tot_cost * 100). "%", array("style"=>"text-align:right"));
                            }else{
                                $this->make->td("0.00%", array("style"=>"text-align:right"));                                
                            }
                            $this->make->td(num($res->gross - 0), array("style"=>"text-align:right"));                                            
                        $this->make->eRow();

                         // Grand Total
                        $tot_qty += $res->qty;
                        $tot_vat_sales += $res->vat_sales;
                        $tot_vat += $res->vat;
                        $tot_sales_prcnt = 0;
                        $tot_margin += $res->gross - 0;
                        $tot_cost_prcnt = 0;

                        $counter++;
                        $progress = ($counter / $trans_count) * 100;
                        update_load(num($progress));

                    }    
                    $this->make->sRow();
                        $this->make->th('Grand Total');
                        if($branch_id == ''){
                            $this->make->th('  ');
                        }
                        $this->make->th(num($tot_qty), array("style"=>"text-align:right"));
                        $this->make->th(num($tot_gross), array("style"=>"text-align:right"));
                        $this->make->th("", array("style"=>"text-align:right"));
                        $this->make->th(num($tot_cost), array("style"=>"text-align:right"));
                        $this->make->th("", array("style"=>"text-align:right"));                                                
                        $this->make->th(num($tot_margin), array("style"=>"text-align:right"));                     
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

    public function voided_sales_rep_pdf()
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
        $pdf->SetTitle('Voided Sales Report');
        $pdf->SetSubject('');
        $pdf->SetKeywords('');

        // set default header data
        $branch_id = $_GET['branch_id'];
        $brand = $_GET['brand'];
        $setup = $this->setup_model->get_details($branch_id);
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
        $this->menu_model->db = $this->load->database('default', TRUE);
        $this->load->model("dine/menu_model");
        start_load(0);

        // set font
        $pdf->SetFont('helvetica', 'B', 11);

        // add a page
        $pdf->AddPage();
        
        $menu_cat_id = $_GET['menu_cat_id'];        
        $daterange = $_GET['calendar_range'];        
        $dates = explode(" to ",$daterange);

        $from = date2SqlDateTime($dates[0]. " ".$set->store_open);        
        $to = date2SqlDateTime(date('Y-m-d', strtotime($dates[1] . ' +1 day')). " ".$set->store_open);
         
        $trans = $this->menu_model->get_voided_cat_sales_rep($from, $to, $menu_cat_id, $branch_id,$brand);
        $trans_ret = $this->menu_model->get_voided_cat_sales_rep_retail($from, $to, "", $branch_id,$brand);

        $brd = $this->setup_model->get_brands($brand);
        if($brd){
            $pdf->Write(0, $brd[0]->brand_name, '', 0, 'L', true, 0, false, false, 0);            
            $pdf->ln(0.9);    
        }

        $pdf->Write(0, 'Voided Sales Report', '', 0, 'L', true, 0, false, false, 0);
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
        if($branch_id == ''){
            $pdf->cell(23, 0, 'Branch', 'B', 0, 'L');
            $pdf->Cell(75, 0, 'Category', 'B', 0, 'L');        
        }
        else{
            $pdf->Cell(75, 0, 'Category', 'B', 0, 'L');
        }
        $pdf->Cell(32, 0, 'Qty', 'B', 0, 'R');         
        $pdf->Cell(32, 0, 'Gross', 'B', 0, 'R');        
        $pdf->Cell(25, 0, 'Sales (%)', 'B', 0, 'R');        
        $pdf->Cell(32, 0, 'Cost', 'B', 0, 'R');        
        $pdf->Cell(16, 0, 'Cost (%)', 'B', 0, 'R');        
        $pdf->Cell(32, 0, 'Margin', 'B', 0, 'R');        
        $pdf->ln();                  

        // GRAND TOTAL VARIABLES
        $tot_qty = 0;
        $tot_vat_sales = 0;
        $tot_vat = 0;
        $tot_gross = 0;
        $tot_sales_prcnt = 0;
        $tot_cost = 0;
        $tot_cost_prcnt = 0; 
        $tot_margin = 0;
        $counter = 0;
        $progress = 0;
        $trans_count = count($trans);
        // echo print_r($trans);die();
        foreach ($trans as $val) {
            $tot_gross += $val->gross;
            $tot_cost += $val->cost;
        }
        foreach ($trans as $k => $v) {
            if($branch_id == ""){
             $pdf->cell(23,0,$v->branch_name,0,0,'L',0, '', 1); //auto scaling cell
                $pdf->Cell(75, 0, $v->menu_cat_name, '', 0, 'L');        
            }
            else{
                $pdf->Cell(75, 0, $v->menu_cat_name, '', 0, 'L');
            }
            $pdf->Cell(32, 0, num($v->qty), '', 0, 'R');            
            $pdf->Cell(32, 0, num($v->gross), '', 0, 'R');        
            $pdf->Cell(25, 0, num($v->gross / $tot_gross * 100)."%", '', 0, 'R');        
            $pdf->Cell(32, 0, num($v->cost), '', 0, 'R');                    
            if($tot_cost != 0){
                $pdf->Cell(16, 0, num($v->cost / $tot_cost * 100)."%", '', 0, 'R');                        
            }else{
                $pdf->Cell(16, 0, "0.00%", '', 0, 'R');                                        
            }
            $pdf->Cell(32, 0, num($v->gross - $v->cost), '', 0, 'R');        
            $pdf->ln();                

            // Grand Total
            $tot_qty += $v->qty;
            $tot_sales_prcnt = 0;
            $tot_margin += $v->gross - $v->cost;
            $tot_cost_prcnt = 0;

            $counter++;
            $progress = ($counter / $trans_count) * 100;
            update_load(num($progress));              
        }
        update_load(100);        
        $pdf->Cell(98, 0, "Grand Total", 'T', 0, 'L');        
        $pdf->Cell(32, 0, num($tot_qty), 'T', 0, 'R');           
        $pdf->Cell(32, 0, num($tot_gross), 'T', 0, 'R');        
        $pdf->Cell(25, 0, "", 'T', 0, 'R');        
        $pdf->Cell(32, 0, num($tot_cost), 'T', 0, 'R');        
        $pdf->Cell(16, 0, "", 'T', 0, 'R'); 
        $pdf->Cell(32, 0, num($tot_margin), 'T', 0, 'R'); 

         $pdf->ln(10);  

         // -----------------------------------------------------------------------------
         if($trans_ret){
              $pdf->Write(0, 'Retail Items', '', 0, 'C', true, 0, false, false, 0);
        $pdf->SetLineStyle(array('width' => 0.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => 'black'));
        $pdf->Cell(267, 0, '', 'T', 0, 'C');
        $pdf->ln(0.9);      
        }   

        if($branch_id == ''){
            $pdf->cell(23, 0, 'Branch', 'B', 0, 'L');
            $pdf->Cell(75, 0, 'Category', 'B', 0, 'L');        
        }
        else{
            $pdf->Cell(75, 0, 'Category', 'B', 0, 'L');
        }       

        $pdf->Cell(32, 0, 'Qty', 'B', 0, 'R');         
        $pdf->Cell(32, 0, 'Gross', 'B', 0, 'R');        
        $pdf->Cell(25, 0, 'Sales (%)', 'B', 0, 'R');        
        $pdf->Cell(32, 0, 'Cost', 'B', 0, 'R');        
        $pdf->Cell(16, 0, 'Cost (%)', 'B', 0, 'R');        
        $pdf->Cell(32, 0, 'Margin', 'B', 0, 'R');        
        $pdf->ln();               

        // GRAND TOTAL VARIABLES
        $tot_qty = 0;
        $tot_vat_sales = 0;
        $tot_vat = 0;
        $tot_gross = 0;
        $tot_sales_prcnt = 0;
        $tot_cost = 0;
        $tot_cost_prcnt = 0; 
        $tot_margin = 0;
        $counter = 0;
        $progress = 0;
        $trans_count = count($trans_ret);
        // echo print_r($trans);die();
        foreach ($trans_ret as $val) {
            $tot_gross += $val->gross;
            $tot_cost += $val->cost;
        }
        foreach ($trans_ret as $k => $v) {
            if($branch_id == ""){
             $pdf->cell(23,0,$v->branch_code,0,0,'L',0, '', 1); //auto scaling cell
                $pdf->Cell(75, 0, $v->name, '', 0, 'L');        
            }
            else{
                $pdf->Cell(75, 0, $v->name, '', 0, 'L');
            }
            $pdf->Cell(32, 0, num($v->qty), '', 0, 'R');            
            $pdf->Cell(32, 0, num($v->gross), '', 0, 'R');        
            $pdf->Cell(25, 0, num($v->gross / $tot_gross * 100)."%", '', 0, 'R');        
            $pdf->Cell(32, 0, num($v->cost), '', 0, 'R');                    
            if($tot_cost != 0){
                $pdf->Cell(16, 0, num($v->cost / $tot_cost * 100)."%", '', 0, 'R');                        
            }else{
                $pdf->Cell(16, 0, "0.00%", '', 0, 'R');                                        
            }
            $pdf->Cell(32, 0, num($v->gross - $v->cost), '', 0, 'R');        
            $pdf->ln();                

            // Grand Total
            $tot_qty += $v->qty;
            $tot_sales_prcnt = 0;
            $tot_margin += $v->gross - $v->cost;
            $tot_cost_prcnt = 0;

            $counter++;
            $progress = ($counter / $trans_count) * 100;
            update_load(num($progress));              
        }
        update_load(100);        
        $pdf->Cell(98, 0, "Grand Total", 'T', 0, 'L');        
        $pdf->Cell(32, 0, num($tot_qty), 'T', 0, 'R');           
        $pdf->Cell(32, 0, num($tot_gross), 'T', 0, 'R');        
        $pdf->Cell(25, 0, "", 'T', 0, 'R');        
        $pdf->Cell(32, 0, num($tot_cost), 'T', 0, 'R');        
        $pdf->Cell(16, 0, "", 'T', 0, 'R'); 
        $pdf->Cell(32, 0, num($tot_margin), 'T', 0, 'R'); 


       

        // -----------------------------------------------------------------------------

        //Close and output PDF document
        $pdf->Output('void_sales_report.pdf', 'I');

        //============================================================+
        // END OF FILE
        //============================================================+   
    }

    public function voided_sales_rep_excel()
    {
        $this->menu_model->db = $this->load->database('default', TRUE);
        $this->load->model("dine/menu_model");
        date_default_timezone_set('Asia/Manila');
        $this->load->library('Excel');
        $sheet = $this->excel->getActiveSheet();
        $filename = 'Voided Sales Report';
        $rc=1;
        #GET VALUES
        start_load(0);
        $branch_id = $_GET['branch_id'];
        $brand = $_GET['brand']; 
            // $post = $this->set_post($_GET['calendar_range']);
        $setup = $this->setup_model->get_details($branch_id);
        if(isset($setup[0]))
        {
            $set = $setup[0];            
            $store_open = $set->store_open;
        }else{
            $store_open = "00:00:00";
        }

        update_load(10);
        sleep(1);
        
        $menu_cat_id = $_GET['menu_cat_id'];        
        $daterange = $_GET['calendar_range'];        
        $dates = explode(" to ",$daterange);

        $from = date2SqlDateTime($dates[0]. " ".$store_open);        
        $to = date2SqlDateTime(date('Y-m-d', strtotime($dates[1] . ' +1 day')). " ".$store_open);        
          
        $trans = $this->menu_model->get_voided_cat_sales_rep($from, $to, $menu_cat_id,$branch_id,$brand);  
        $trans_ret = $this->menu_model->get_voided_cat_sales_rep_retail($from, $to, "", $branch_id,$brand);   

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
        if($branch_id == ""){
            $headers = array('Category', 'Qty','Gross','Sales (%)','Cost','Cost (%)', 'Margin','Branch');
        }
        else{
            $headers = array('Category', 'Qty','Gross','Sales (%)','Cost','Cost (%)', 'Margin');
        }
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);

        $sheet->mergeCells('A'.$rc.':H'.$rc);
        $sheet->getCell('A'.$rc)->setValue($set->branch_name);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        $rc++;

        $sheet->mergeCells('A'.$rc.':H'.$rc);
        $sheet->getCell('A'.$rc)->setValue($set->address);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        $rc++;

        $brd = $this->setup_model->get_brands($brand);

        if($brd){
            $sheet->mergeCells('A'.$rc.':H'.$rc);
            $sheet->getCell('A'.$rc)->setValue($brd[0]->brand_name);
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $rc++;
        }

        $sheet->mergeCells('A'.$rc.':H'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Voided Sales Report');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $rc++;

        $sheet->mergeCells('A'.$rc.':D'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Report Period: '.$daterange);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->mergeCells('E'.$rc.':H'.$rc);
        $sheet->getCell('E'.$rc)->setValue('Report Generated: '.(new \DateTime())->format('Y-m-d H:i:s'));
        $sheet->getStyle('E'.$rc)->applyFromArray($styleNum);
        $rc++;

        $sheet->mergeCells('A'.$rc.':D'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Transaction Time:');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $user = $this->session->userdata('user');
        $sheet->mergeCells('E'.$rc.':H'.$rc);
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
        $tot_vat_sales = 0;
        $tot_vat = 0;
        $tot_gross = 0;
        $tot_mod_gross = 0;
        $tot_sales_prcnt = 0;
        $tot_cost = 0;
        $tot_cost_prcnt = 0; 
        $tot_margin = 0;
        $counter = 0;
        $progress = 0;
        $trans_count = count($trans);
        foreach ($trans as $val) {
            $tot_gross += $val->gross;
            $tot_cost += $val->cost;
            $tot_margin += $val->gross - $val->cost;
        }
        // foreach ($trans_mod as $vv) {
        //     $tot_mod_gross += $vv->mod_gross;
        // }

        foreach ($trans as $k => $v) {
            $sheet->getCell('A'.$rc)->setValue($v->menu_cat_name);
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue($v->qty);
            $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
            $sheet->getCell('C'.$rc)->setValue($v->gross);     
            $sheet->getStyle('C'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1); 
            $sheet->getCell('D'.$rc)->setValue(num($v->gross / $tot_gross * 100)."%");     
            $sheet->getStyle('D'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1); 
            if($tot_cost != 0){
                $sheet->getCell('E'.$rc)->setValue($v->cost);                     
            }else{
                $sheet->getCell('E'.$rc)->setValue(num(0));                                     
            }
            $sheet->getStyle('E'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1); 
            if($tot_cost != 0){
                $sheet->getCell('F'.$rc)->setValue(num($v->cost / $tot_cost * 100)."%");     
            
            }else{
                $sheet->getCell('F'.$rc)->setValue('0.00%');                                     
            }     
            $sheet->getStyle('F'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1); 
            $sheet->getCell('G'.$rc)->setValue(($v->gross - $v->cost));     
            $sheet->getStyle('G'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1); 
            if($branch_id == ""){
                $sheet->getCell('H'.$rc)->setValue($v->branch_name);
                $sheet->getStyle('H'.$rc)->applyFromArray($styleTxt);
            }      
            // Grand Total
            $tot_qty += $v->qty;
            $tot_sales_prcnt = 0;
            $tot_cost_prcnt = 0;

            $counter++;
            $progress = ($counter / $trans_count) * 100;
            update_load(num($progress));   
            $rc++;           
        }

        $sheet->getCell('A'.$rc)->setValue('Grand Total');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('B'.$rc)->setValue(num($tot_qty));
        $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldRight);
        $sheet->getCell('C'.$rc)->setValue($tot_gross);     
        $sheet->getStyle('C'.$rc)->applyFromArray($styleBoldRight)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->getCell('D'.$rc)->setValue("");     
        $sheet->getStyle('D'.$rc)->applyFromArray($styleBoldRight);
        $sheet->getCell('E'.$rc)->setValue($tot_cost);     
        $sheet->getStyle('E'.$rc)->applyFromArray($styleBoldRight)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->getCell('F'.$rc)->setValue("");     
        $sheet->getStyle('F'.$rc)->applyFromArray($styleBoldRight);
        $sheet->getCell('G'.$rc)->setValue($tot_margin);     
        $sheet->getStyle('G'.$rc)->applyFromArray($styleBoldRight)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1); 
        $rc++; 

        //retail
        $tot_gross_ret = 0;
        if(count($trans_ret) > 0){

            $rc++; 
            $col = 'A';
            $sheet->mergeCells('A'.$rc.':G'.$rc);
            $sheet->getCell('A'.$rc)->setValue('Retail Items');
            $sheet->getStyle('A'.$rc)->applyFromArray($styleCenter);
            $rc++;

            foreach ($headers as $txt) {
                $sheet->getCell($col.$rc)->setValue($txt);
                $sheet->getStyle($col.$rc)->applyFromArray($styleHeaderCell);
                $col++;
            }
            $rc++;                          

            // GRAND TOTAL VARIABLES
            $tot_qty = 0;
            $tot_vat_sales = 0;
            $tot_vat = 0;
            $tot_mod_gross = 0;
            $tot_sales_prcnt = 0;
            $tot_cost = 0;
            $tot_cost_prcnt = 0; 
            $tot_margin = 0;
            $counter = 0;
            $progress = 0;
            $trans_count = count($trans);
            foreach ($trans_ret as $val) {
                $tot_gross_ret += $val->gross; 
                $tot_margin += $val->gross - 0;
            }

            foreach ($trans_ret as $k => $v) {
                $sheet->getCell('A'.$rc)->setValue($v->name);
                $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
                $sheet->getCell('B'.$rc)->setValue($v->qty);
                $sheet->getStyle('B'.$rc)->applyFromArray($styleNum);
                $sheet->getCell('C'.$rc)->setValue($v->gross);     
                $sheet->getStyle('C'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1); 
                $sheet->getCell('D'.$rc)->setValue(num($v->gross / $tot_gross_ret * 100)."%");     
                $sheet->getStyle('D'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1); 
                $sheet->getStyle('E'.$rc)->applyFromArray($styleNum);
                $sheet->getCell('E'.$rc)->setValue(num(0));                                     
                if($tot_cost != 0){
                    $sheet->getCell('F'.$rc)->setValue(num($v->cost / $tot_cost * 100)."%");     
                }else{
                    $sheet->getCell('F'.$rc)->setValue('0.00%');                     
                }
                $sheet->getStyle('F'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1); 
                $sheet->getCell('G'.$rc)->setValue(($v->gross - 0));     
                $sheet->getStyle('G'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1); 

                if($branch_id == ""){
                    $sheet->getCell('H'.$rc)->setValue($v->branch_code);
                    $sheet->getStyle('H'.$rc)->applyFromArray($styleTxt);
                }        

                // Grand Total
                $tot_qty += $v->qty;
                $tot_sales_prcnt = 0;
                $tot_cost_prcnt = 0; 
                $rc++;           
            }

            $sheet->getCell('A'.$rc)->setValue('Grand Total');
            $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
            $sheet->getCell('B'.$rc)->setValue(num($tot_qty));
            $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldRight);
            $sheet->getCell('C'.$rc)->setValue($tot_gross_ret);     
            $sheet->getStyle('C'.$rc)->applyFromArray($styleBoldRight)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1); 
            $sheet->getCell('D'.$rc)->setValue("");     
            $sheet->getStyle('D'.$rc)->applyFromArray($styleBoldRight);
            $sheet->getCell('E'.$rc)->setValue($tot_cost);     
            $sheet->getStyle('E'.$rc)->applyFromArray($styleBoldRight)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1); 
            $sheet->getCell('F'.$rc)->setValue("");     
            $sheet->getStyle('F'.$rc)->applyFromArray($styleBoldRight);
            $sheet->getCell('G'.$rc)->setValue($tot_margin);     
            $sheet->getStyle('G'.$rc)->applyFromArray($styleBoldRight)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $rc++; 
        }
        
        update_load(100);        
       
        if (ob_get_contents())
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

    public function issues_stamp_rep(){
        $data = $this->syter->spawn('issues_stamp_rep');        
        $data['page_title'] = fa('fa-money')." Issues Stamp Report";
        $data['code'] = issuesStampRep();
        $data['add_css'] = array('css/morris/morris.css','css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css','assets/global/plugins/select2/css/select2.min.css','assets/global/plugins/select2/css/select2-bootstrap.min.css');
        $data['add_js'] = array('js/plugins/morris/morris.min.js','js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js','assets/global/plugins/select2/js/select2.full.min.js','assets/pages/scripts/components-select2.min.js');
        $data['page_no_padding'] = false;
        $data['sideBarHide'] = false;
        $data['load_js'] = 'dine/reporting';
        $data['use_js'] = 'issuesRepJS';
        $this->load->view('page',$data);
    }

    public function issues_stamp_rep_gen(){
        $this->load->model('dine/setup_model');
        $this->load->model("dine/menu_model");
        
        start_load(0);     
        $year = $this->input->post("year");             
        $branch_id = $this->input->post("branch_id"); 

        $trans = $this->menu_model->get_issues_stamp($year, $branch_id);


        $trans_count = count($trans['list']);
        $counter = 0;
        $total = 0;
        $total_amt = 0;

        $date_qty = array();
        $date_amt = array();

        if($trans_count > 0){
            $this->make->sDiv();
                $this->make->sTable(array("id"=>"main-tbl", 'class'=>'table reportTBL sortable'));
                    $this->make->sTableHead();
                        $this->make->sRow();
                            $this->make->th('Branch');
                            foreach($trans['dates'] as $each){
                                $this->make->th(date('F',strtotime($each)));
                                $date_qty[$each] = 0;
                                $date_amt[$each] = 0;
                            }
                            $this->make->th('Grand Total');                        
                        $this->make->eRow();
                    $this->make->eTableHead();
                    $this->make->sTableBody();                
                                                              
                        
                        foreach ($trans['list'] as $res) {
                            $tot_qty = 0;

                            $this->make->sRow();
                                $this->make->td($res['branch_code']);

                                foreach($trans['dates'] as $each){
                                    $this->make->td($res[$each]['qty'], array("style"=>"text-align:right"));

                                    $date_qty[$each] += $res[$each]['qty'];
                                    $date_amt[$each] += $res[$each]['amount'];

                                    $tot_qty += $res[$each]['qty'];
                                    $total += $res[$each]['qty'];
                                    $total_amt += $res[$each]['amount'];
                                }
                                 
                                $this->make->td($tot_qty, array("style"=>"text-align:right")); 
                            $this->make->eRow();
                           
                            $counter++;
                            $progress = ($counter / $trans_count) * 100;
                            update_load(num($progress));

                        }    
                        $this->make->sRow();
                            $this->make->th('Grand Total');
                            foreach($trans['dates'] as $each){
                                 $this->make->th($date_qty[$each], array("style"=>"text-align:right"));
                            }                                                
                            $this->make->th($total, array("style"=>"text-align:right"));                        
                        $this->make->eRow();

                        // $this->make->sRow();
                        //     $this->make->th('Unit Cost');
                        //     foreach($trans['dates'] as $each){
                        //          $this->make->th(numInt($date_amt[$each]/$date_qty[$each]), array("style"=>"text-align:right"));
                        //     }                                                
                        //     $this->make->th(numInt($total_amt/$total), array("style"=>"text-align:right"));                        
                        // $this->make->eRow();  

                        // $this->make->sRow();
                        //     $this->make->th('Total Cost');
                        //     foreach($trans['dates'] as $each){
                        //          $this->make->th(numInt($date_amt[$each]), array("style"=>"text-align:right"));
                        //     }                                                
                        //     $this->make->th(numInt($total_amt), array("style"=>"text-align:right"));                        
                        // $this->make->eRow();                                 

                    $this->make->eTableBody();
                $this->make->eTable();
            $this->make->eDiv();
        }else{
            $this->make->sDiv();
                $this->make->sTable(array("id"=>"main-tbl", 'class'=>'table reportTBL sortable'));
                    $this->make->sTableHead();
                        $this->make->sRow();
                            $this->make->th('Branch');                            
                            $this->make->th('Grand Total');                        
                        $this->make->eRow();
                    $this->make->eTableHead();
                    $this->make->sTableBody();                
                                                              
                        $this->make->sRow();
                                $this->make->td('No records found.',array('colspan'=>2));
                        $this->make->eRow();
                    $this->make->eTableBody();
                $this->make->eTable();
            $this->make->eDiv();
        }

        update_load(100);
        $code = $this->make->code();
        $json['code'] = $code;        
        $json['tbl_vals'] = $trans;
        $json['dates'] = $this->input->post('year');
        echo json_encode($json);
    }

     public function issues_stamp_rep_pdf()
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
        $pdf->SetTitle('Issues Stamp Report');
        $pdf->SetSubject('');
        $pdf->SetKeywords('');

        // set default header data
        $branch_id = $_GET['branch_id'];
        $setup = $this->setup_model->get_details($branch_id);
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
        $this->menu_model->db = $this->load->database('default', TRUE);
        $this->load->model("dine/menu_model");
        start_load(0);

        // set font
        $pdf->SetFont('helvetica', 'B', 11);

        // add a page
        $pdf->AddPage();
               
        $year = $_GET['year'];        
        $branch_id = $this->input->post('branch_id');
          
        $trans = $this->menu_model->get_issues_stamp($year, $branch_id);

        $trans_count = count($trans['list']);
        $counter = 0;
        $total = 0;
        $total_amt = 0;

        $date_qty = array();
        $date_amt = array();


        $pdf->Write(0, 'Issues Stamp Report', '', 0, 'L', true, 0, false, false, 0);
        $pdf->SetLineStyle(array('width' => 0.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => 'black'));
        $pdf->Cell(267, 0, '', 'T', 0, 'C');
        $pdf->ln(0.9);      
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Write(0, 'Report Period:    ', '', 0, 'L', false, 0, false, false, 0);
        $pdf->Write(0, $year, '', 0, 'L', false, 0, false, false, 0);
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
        
        $pdf->cell(23, 0, 'Branch', 'B', 0, 'L');   

        if($trans_count > 0){
            foreach($trans['dates'] as $each){
                $pdf->Cell(32, 0, date('F',strtotime($each)), 'B', 0, 'R');      
                $date_qty[$each] = 0;
                $date_amt[$each] = 0;
            }
            $pdf->Cell(32, 0, 'Grand Total', 'B', 0, 'R');      
       
            $pdf->ln();                  

            foreach ($trans['list'] as $res) {
                $tot_qty = 0;

                $pdf->Cell(23, 0, $res['branch_code'], '', 0, 'L'); 

                foreach($trans['dates'] as $each){
                    $pdf->Cell(32, 0, $res[$each]['qty'], '', 0, 'R'); 
                    $date_qty[$each] += $res[$each]['qty'];
                    $date_amt[$each] += $res[$each]['amount'];

                    $tot_qty += $res[$each]['qty'];
                    $total += $res[$each]['qty'];
                    $total_amt += $res[$each]['amount'];
                }
                
                $pdf->Cell(32, 0, $tot_qty, '', 0, 'R'); 

                $pdf->ln();
               
                $counter++;
                $progress = ($counter / $trans_count) * 100;
                update_load(num($progress));

            }    
            $pdf->ln();
           
            update_load(100);        
            $pdf->Cell(23, 0, "Grand Total", 'T', 0, 'L'); 
            foreach($trans['dates'] as $each){
                $pdf->Cell(32, 0, $date_qty[$each], 'T', 0, 'R');
            }        
            $pdf->Cell(32, 0, $total, 'T', 0, 'R');      

            // $pdf->ln();   

            // $pdf->Cell(23, 0, "Unit Cost", 'T', 0, 'L'); 
            // foreach($trans['dates'] as $each){
            //     $pdf->Cell(32, 0, numInt($date_amt[$each]/$date_qty[$each]), 'T', 0, 'R');
            // }        
            // $pdf->Cell(32, 0, numInt($total_amt/$total), 'T', 0, 'R'); 

            // $pdf->ln();   

            // $pdf->Cell(23, 0, "Total Cost", 'T', 0, 'L'); 
            // foreach($trans['dates'] as $each){
            //     $pdf->Cell(32, 0, numInt($date_amt[$each]), 'T', 0, 'R');
            // }        
            // $pdf->Cell(32, 0, numInt($total_amt), 'T', 0, 'R');  
        }else{
            $pdf->Cell(32, 0, 'Grand Total', 'B', 0, 'R');
            $pdf->ln();
            $pdf->Cell(23, 0, 'No records found.', '', 0, 'L'); 
            
            update_load(100);
        }  

        //Close and output PDF document
        $pdf->Output('issues_stamp_report.pdf', 'I');

        //============================================================+
        // END OF FILE
        //============================================================+   
    }

    public function issues_stamp_rep_excel()
    {
        $this->menu_model->db = $this->load->database('default', TRUE);
        $this->load->model("dine/menu_model");
        date_default_timezone_set('Asia/Manila');
        $this->load->library('Excel');
        $sheet = $this->excel->getActiveSheet();
        $filename = 'Issues Stamp Report';
        $rc=1;
        #GET VALUES
        
        update_load(10);
        sleep(1);
           
        $year = $_GET['year'];
        $branch_id = $_GET['branch_id']; 

        $setup = $this->setup_model->get_details($branch_id);
        $set = $setup[0];  

        $trans = $this->menu_model->get_issues_stamp($year,$branch_id); 

        $trans_count = count($trans['list']);
        $counter = 0;
        $total = 0;
        $total_amt = 0;

        $date_qty = array();
        $date_amt = array(); 

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
        
        $headers = array('Branch');

        if($trans_count > 0){
            foreach($trans['dates'] as $each){
                array_push($headers, date('F',strtotime($each)));   
                $date_qty[$each] = 0;
            }
        }        

        array_push($headers, 'Grand Total'); 
        
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);


        $sheet->mergeCells('A'.$rc.':N'.$rc);
        $sheet->getCell('A'.$rc)->setValue($set->branch_name);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        $rc++;

        $sheet->mergeCells('A'.$rc.':N'.$rc);
        $sheet->getCell('A'.$rc)->setValue($set->address);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        $rc++;

        $sheet->mergeCells('A'.$rc.':N'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Issues Stamp Report');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $rc++;

        $sheet->mergeCells('A'.$rc.':C'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Report Period: '.$daterange);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->mergeCells('D'.$rc.':F'.$rc);
        $sheet->getCell('D'.$rc)->setValue('Report Generated: '.(new \DateTime())->format('Y-m-d H:i:s'));
        $sheet->getStyle('D'.$rc)->applyFromArray($styleTxt);
        $rc++;

        $sheet->mergeCells('A'.$rc.':D'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Transaction Time:');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $user = $this->session->userdata('user');
        $sheet->mergeCells('E'.$rc.':H'.$rc);
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

        if($trans_count > 0){
            foreach ($trans['list'] as $k => $v) {
                $sheet->getCell('A'.$rc)->setValue($v['branch_code']);
                $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);

                $col = 'B';

                foreach($trans['dates'] as $each){
                    $sheet->getCell($col.$rc)->setValue($v[$each]['qty']);
                    $sheet->getStyle($col.$rc)->applyFromArray($styleNum);

                    $date_qty[$each] += $v[$each]['qty'];
                    $date_amt[$each] += $v[$each]['amount'];

                    $tot_qty += $v[$each]['qty'];
                    $total += $v[$each]['qty'];
                    $total_amt += $v[$each]['amount'];

                    $col++;                
                }

                $sheet->getCell($col.$rc)->setValue($tot_qty);
                $sheet->getStyle($col.$rc)->applyFromArray($styleNum);

                $counter++;
                $progress = ($counter / $trans_count) * 100;
                update_load(num($progress));   

                $rc++;           
            }

            $sheet->getCell('A'.$rc)->setValue('Grand Total');
            $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);

            $col = 'B';

            foreach($trans['dates'] as $each){
                $sheet->getCell($col.$rc)->setValue($date_qty[$each]);
                $sheet->getStyle($col.$rc)->applyFromArray($styleNum);

                $col++;
            }

            $sheet->getCell($col.$rc)->setValue($total);
            $sheet->getStyle($col.$rc)->applyFromArray($styleNum);

            // $rc++;
            // $col = 'B';

            // $sheet->getCell('A'.$rc)->setValue('Unit Cost');
            // $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);

            // foreach($trans['dates'] as $each){
            //     $sheet->getCell($col.$rc)->setValue($date_amt[$each]/$date_qty[$each]);
            //     $sheet->getStyle($col.$rc)->applyFromArray($styleNum);

            //     $col++;
            // }

            // $sheet->getCell($col.$rc)->setValue($total_amt/$total);
            // $sheet->getStyle($col.$rc)->applyFromArray($styleNum);

            // $rc++;
            // $col = 'B';

            // $sheet->getCell('A'.$rc)->setValue('Total Cost');
            // $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);

            // foreach($trans['dates'] as $each){
            //     $sheet->getCell($col.$rc)->setValue($date_amt[$each]);
            //     $sheet->getStyle($col.$rc)->applyFromArray($styleNum);

            //     $col++;
            // } 

            // $sheet->getCell($col.$rc)->setValue($total_amt);
            // $sheet->getStyle($col.$rc)->applyFromArray($styleNum);
        }else{
            $sheet->getCell('A'.$rc)->setValue('No records found.');
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        }       
        
        update_load(100);        
       
        if (ob_get_contents())
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

     public function promo_rep(){
        $data = $this->syter->spawn('promo_rep');        
        $data['page_title'] = fa('fa-money')." Promo Redemption Report";
        $data['code'] = promoRep();
        $data['add_css'] = array('css/morris/morris.css','css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css','assets/global/plugins/select2/css/select2.min.css','assets/global/plugins/select2/css/select2-bootstrap.min.css');
        $data['add_js'] = array('js/plugins/morris/morris.min.js','js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js','assets/global/plugins/select2/js/select2.full.min.js','assets/pages/scripts/components-select2.min.js');
        $data['page_no_padding'] = false;
        $data['sideBarHide'] = false;
        $data['load_js'] = 'dine/reporting';
        $data['use_js'] = 'promoRepJS';
        $this->load->view('page',$data);
    }

    public function promo_rep_gen(){
        $this->load->model('dine/setup_model');
        $this->load->model("dine/reports_model");
        $this->load->model("dine/menu_model");
    
        start_load(0);     

        $daterange = $this->input->post("calendar_range");
        $dates = explode(" to ",$daterange);
        
        $this->menu_model->db = $this->load->database('default', TRUE);

        $from = date2SqlDateTime($dates[0]);        
        $to = date2SqlDateTime(date('Y-m-d', strtotime($dates[1] . ' +1 day')));             
        $discount_id = $this->input->post("discount_id");  

        $trans = $this->reports_model->get_promo($from, $to, $discount_id);


        $trans_count = count($trans);
        $counter = 0;
        $total = 0;
        $total_amt = 0;

        $branches = array();
        $dates = array();

        if($trans_count > 0){
            $this->make->sDiv();
                $this->make->sTable(array("id"=>"main-tbl", 'class'=>'table reportTBL sortable'));
                    $this->make->sTableHead();
                        $this->make->sRow();
                            $this->make->th('Date');
                            foreach ($trans as $val) {
                                if(!in_array($val->branch_code, $branches)){
                                    $branches[] = $val->branch_code;
                                    $this->make->th($val->branch_code);
                                }

                                if(!in_array($val->date, $dates)){
                                    $dates[] = $val->date;                                
                                }
                            }                       
                        $this->make->eRow();
                    $this->make->eTableHead();
                    $this->make->sTableBody();

                        foreach($dates as $date){
                            $this->make->sRow();
                                $this->make->td(sql2Date($date));
                                
                                foreach($branches as $i){
                                    $exist = false;
                                    foreach($trans as $val){
                                        if($val->branch_code == $i && $val->date == $date){
                                            $this->make->td($val->qty,array('style'=>'text-align:right'));
                                            $exist = true;

                                            break; 
                                        }                        
                                    }

                                    if(!$exist){
                                         $this->make->td('0',array('style'=>'text-align:right')); 
                                    }
                                }
                                
                             $this->make->eRow();

                            $counter++;
                            $progress = ($counter / $trans_count) * 100;
                            update_load(num($progress));
                        }                                
                                                              
                        
                    
                        // $this->make->sRow();
                        //     $this->make->th('Grand Total');
                        //     foreach($trans['dates'] as $each){
                        //          $this->make->th($date_qty[$each], array("style"=>"text-align:right"));
                        //     }                                                
                        //     $this->make->th($total, array("style"=>"text-align:right"));                        
                        // $this->make->eRow();

                        // $this->make->sRow();
                        //     $this->make->th('Unit Cost');
                        //     foreach($trans['dates'] as $each){
                        //          $this->make->th(numInt($date_amt[$each]/$date_qty[$each]), array("style"=>"text-align:right"));
                        //     }                                                
                        //     $this->make->th(numInt($total_amt/$total), array("style"=>"text-align:right"));                        
                        // $this->make->eRow();  

                        // $this->make->sRow();
                        //     $this->make->th('Total Cost');
                        //     foreach($trans['dates'] as $each){
                        //          $this->make->th(numInt($date_amt[$each]), array("style"=>"text-align:right"));
                        //     }                                                
                        //     $this->make->th(numInt($total_amt), array("style"=>"text-align:right"));                        
                        // $this->make->eRow();                                 

                    $this->make->eTableBody();
                $this->make->eTable();
            $this->make->eDiv();
        }else{
            $this->make->sDiv();
                $this->make->sTable(array("id"=>"main-tbl", 'class'=>'table reportTBL sortable'));
                    $this->make->sTableHead();
                        $this->make->sRow();
                            $this->make->th('Date');                     
                        $this->make->eRow();
                    $this->make->eTableHead();
                    $this->make->sTableBody();                
                                                              
                        $this->make->sRow();
                                $this->make->td('No records found.');
                        $this->make->eRow();
                    $this->make->eTableBody();
                $this->make->eTable();
            $this->make->eDiv();
        }

        update_load(100);
        $code = $this->make->code();
        $json['code'] = $code;        
        $json['tbl_vals'] = $trans;
        $json['dates'] = $daterange;
        echo json_encode($json);
    }

    public function promo_rep_pdf()
    {
         // Include the main TCPDF library (search for installation path).
        require_once( APPPATH .'third_party/tcpdf.php');
        $this->load->model("dine/setup_model");
        $this->load->model("dine/reports_model");

        date_default_timezone_set('Asia/Manila');

        // create new PDF document
        $pdf = new TCPDF("L", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('iPOS');
        $pdf->SetTitle('Promo Redemption Report');
        $pdf->SetSubject('');
        $pdf->SetKeywords('');

        // set default header data
        $setup = $this->setup_model->get_details('');
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
        $this->menu_model->db = $this->load->database('default', TRUE);
        $this->load->model("dine/menu_model");
        start_load(0);

        // set font
        $pdf->SetFont('helvetica', 'B', 11);

        // add a page
        $pdf->AddPage();
               
        $daterange = $_GET["calendar_range"];        
        $dates = explode(" to ",$daterange);

        $discount_id = $_GET["discount_id"];

        $from = date2SqlDateTime($dates[0]. " ".$set->store_open);        
        $to = date2SqlDateTime(date('Y-m-d', strtotime($dates[1] . ' +1 day')));
          
        $trans = $this->reports_model->get_promo($from, $to, $discount_id);

        $trans_count = count($trans);
        $counter = 0;
        $total = 0;
        $total_amt = 0;

        $branches = array();
        $dates = array();

        $pdf->Write(0, 'Promo Redemption Report', '', 0, 'L', true, 0, false, false, 0);
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
        
        $discount_id = $discount_id == '' ? 'All' : $discount_id; 
        $pdf->Write(0, 'Promo/Discount:    ', '', 0, 'L', false, 0, false, false, 0);
        $pdf->Write(0, $discount_id, '', 0, 'L', false, 0, false, false, 0);
        $pdf->setX(200);        
        $pdf->ln(5);      
        $pdf->Cell(267, 0, '', 'T', 0, 'C');
        $pdf->ln();              


        // -----------------------------------------------------------------------------
        $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => 'black'));
        
        $pdf->cell(40, 0, 'Date', 'B', 0, 'L'); 

        foreach ($trans as $val) {
            if(!in_array($val->branch_code, $branches)){
                $branches[] = $val->branch_code;
                $pdf->cell(30, 0, $val->branch_code, 'B', 0, 'R');
            }

            if(!in_array($val->date, $dates)){
                $dates[] = $val->date;                                
            }
        }  

        $pdf->ln();   

        if($trans_count > 0){
            foreach($dates as $date){
                $pdf->Cell(40, 0, sql2Date($date), '', 0, 'L');
                    
                    foreach($branches as $i){
                        $exist = false;
                        foreach($trans as $val){
                            if($val->branch_code == $i && $val->date == $date){
                                $pdf->Cell(30, 0, $val->qty, '', 0, 'R');
                                $exist = true;

                                break; 
                            }                        
                        }

                        if(!$exist){
                             $pdf->Cell(23, 0, '0', '', 0, 'R');
                        }
                    }
                    
                    $pdf->ln(); 
       
                    $counter++;
                    $progress = ($counter / $trans_count) * 100;
                    update_load(num($progress));
            }      
            $pdf->ln();
           
            update_load(100);        
            // $pdf->Cell(23, 0, "Grand Total", 'T', 0, 'L'); 
            // foreach($trans['dates'] as $each){
            //     $pdf->Cell(32, 0, $date_qty[$each], 'T', 0, 'R');
            // }        
            // $pdf->Cell(32, 0, $total, 'T', 0, 'R');      

            // $pdf->ln();   

            // $pdf->Cell(23, 0, "Unit Cost", 'T', 0, 'L'); 
            // foreach($trans['dates'] as $each){
            //     $pdf->Cell(32, 0, numInt($date_amt[$each]/$date_qty[$each]), 'T', 0, 'R');
            // }        
            // $pdf->Cell(32, 0, numInt($total_amt/$total), 'T', 0, 'R'); 

            // $pdf->ln();   

            // $pdf->Cell(23, 0, "Total Cost", 'T', 0, 'L'); 
            // foreach($trans['dates'] as $each){
            //     $pdf->Cell(32, 0, numInt($date_amt[$each]), 'T', 0, 'R');
            // }        
            // $pdf->Cell(32, 0, numInt($total_amt), 'T', 0, 'R');  
        }else{
            $pdf->Cell(50, 0, 'No records found.', '', 0, 'L'); 
            
            update_load(100);
        }  

        //Close and output PDF document
        $pdf->Output('issues_stamp_report.pdf', 'I');

        //============================================================+
        // END OF FILE
        //============================================================+   
    }

    public function promo_rep_excel()
    {
        $this->menu_model->db = $this->load->database('default', TRUE);
        $this->load->model("dine/menu_model");
        $this->load->model("dine/reports_model");

        date_default_timezone_set('Asia/Manila');
        $this->load->library('Excel');
        $sheet = $this->excel->getActiveSheet();
        $filename = 'Promo Redemption Report';
        $rc=1;
        #GET VALUES
        start_load(0);
        $setup = $this->setup_model->get_details('');
        $set = $setup[0];

        update_load(10);
        sleep(1);
           
        $daterange = $_GET["calendar_range"];        
        $dates = explode(" to ",$daterange);

        $discount_id = $_GET["discount_id"];

        $from = date2SqlDateTime($dates[0]);        
        $to = date2SqlDateTime(date('Y-m-d', strtotime($dates[1] . ' +1 day')));
          
        $trans = $this->reports_model->get_promo($from, $to, $discount_id);

        $trans_count = count($trans);
        $counter = 0;
        $total = 0;
        $total_amt = 0;

        $branches = array();
        $dates = array(); 

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
        
        $headers = array('Date');

        foreach ($trans as $val) {
            if(!in_array($val->branch_code, $branches)){
                $branches[] = $val->branch_code;
                $headers[] = $val->branch_code;
            }

            if(!in_array($val->date, $dates)){
                $dates[] = $val->date;                                
            }
        }        
        
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);


        $sheet->mergeCells('A'.$rc.':N'.$rc);
        $sheet->getCell('A'.$rc)->setValue($set->branch_name);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        $rc++;

        $sheet->mergeCells('A'.$rc.':N'.$rc);
        $sheet->getCell('A'.$rc)->setValue($set->address);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        $rc++;

        $sheet->mergeCells('A'.$rc.':N'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Sales Report');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $rc++;

        $sheet->mergeCells('A'.$rc.':C'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Report Period: '.$daterange);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->mergeCells('D'.$rc.':F'.$rc);
        $sheet->getCell('D'.$rc)->setValue('Report Generated: '.(new \DateTime())->format('Y-m-d H:i:s'));
        $sheet->getStyle('D'.$rc)->applyFromArray($styleTxt);
        $rc++;

        $sheet->mergeCells('A'.$rc.':C'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Transaction Time:');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $user = $this->session->userdata('user');
        $sheet->mergeCells('D'.$rc.':F'.$rc);
        $sheet->getCell('D'.$rc)->setValue('Generated by:    '.$user["full_name"]);
        $sheet->getStyle('D'.$rc)->applyFromArray($styleTxt);
        $rc++;

        $discount_id = $discount_id == '' ? 'All' : $discount_id;

        $sheet->mergeCells('A'.$rc.':C'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Promo/Discount: ' . $discount_id);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $rc++;

        $col = 'A';
        foreach ($headers as $txt) {
            $sheet->getCell($col.$rc)->setValue($txt);
            $sheet->getStyle($col.$rc)->applyFromArray($styleHeaderCell);
            $col++;
        }
        $rc++;  

        if($trans_count > 0){
            foreach($dates as $date){
                $sheet->getCell('A'.$rc)->setValue(sql2Date($date));
                $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);

                    $col = 'B';
                    foreach($branches as $i){
                        $exist = false;
                        foreach($trans as $val){
                            if($val->branch_code == $i && $val->date == $date){
                                 $sheet->getCell($col.$rc)->setValue($val->qty);
                                 $sheet->getStyle($col.$rc)->applyFromArray($styleNum);
                                $exist = true;

                                break; 
                            }                        
                        }

                        if(!$exist){
                             $sheet->getCell($col.$rc)->setValue('0');
                             $sheet->getStyle($col.$rc)->applyFromArray($styleNum);
                        }

                        $col++;
                    }
                    
                    $rc++;
       
                    $counter++;
                    $progress = ($counter / $trans_count) * 100;
                    update_load(num($progress));
            }  

            // $sheet->getCell('A'.$rc)->setValue('Grand Total');
            // $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);

            // $col = 'B';

            // foreach($trans['dates'] as $each){
            //     $sheet->getCell($col.$rc)->setValue($date_qty[$each]);
            //     $sheet->getStyle($col.$rc)->applyFromArray($styleNum);

            //     $col++;
            // }

            // $sheet->getCell($col.$rc)->setValue($total);
            // $sheet->getStyle($col.$rc)->applyFromArray($styleNum);

        }else{
            $sheet->getCell('A'.$rc)->setValue('No records found.');
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        }       
        
        update_load(100);        
       
        if (ob_get_contents())
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
       public function cashier_report(){
        $this->load->model('dine/reports_model');
        $this->load->helper('dine/reports_helper');
        $data = $this->syter->spawn('monthly');
        $data['page_title'] = "Cashier's Report";
        $data['code'] = cashierUi();
        $data['add_css'] = array('css/morris/morris.css','css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css');
        $data['add_js'] = array('js/plugins/morris/morris.min.js','js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js');
        $data['load_js'] = 'dine/reporting.php';
        $data['use_js'] = 'cashierJS';
        $this->load->view('page',$data);
    }
    public function get_cashier_reports(){
        ini_set('memory_limit', '-1');
        set_time_limit(3600);
        sess_clear('date_array');
        sess_clear('month_date');
        $this->load->helper('dine/reports_helper');
        $this->load->model('dine/clock_model'); 
        // $date = date("Y-m-d", strtotime($date));

        
        $date = $this->input->post('date');
        $cashier = $this->input->post('cashier');
        $branch_id = $this->input->post('branch_id');
        $json = $this->input->post('json');

        if($json == 'true'){
            $asjson = true;
        }else{
            $asjson = false;
        }

        // $now = sql2Date($this->site_model->get_db_now('sql'));
        // if(strtotime($date) < strtotime($now)){
        //     $this->db = $this->load->database('main', TRUE);
        // }
        $get_shift = $this->clock_model->get_old_shift(date2Sql($date),$cashier);

        if(count($get_shift) > 0){
            // $shift_out = $get_shift[0]->cashout_id;
            // $this->print_drawer_details($shift_out,$date,$user,$asjson);

             update_load(10);
            // sleep(1);
            $load = 10;

            $details = $this->setup_model->get_branch_details();
                
            $open_time = $details[0]->store_open;
            $close_time = $details[0]->store_close;

                  
            $date_array = array();
            
            $post = $this->set_post(null,$date);
            $trans = $this->trans_sales($post['args']);
            
            $sales = $trans['sales'];
            $trans_menus = $this->menu_sales($sales['settled']['ids']);
            $trans_charges = $this->charges_sales($sales['settled']['ids']);
            $trans_discounts = $this->discounts_sales($sales['settled']['ids']);
            $tax_disc = $trans_discounts['tax_disc_total'];
            $no_tax_disc = $trans_discounts['no_tax_disc_total'];
            $trans_local_tax = $this->local_tax_sales($sales['settled']['ids']);
            $trans_tax = $this->tax_sales($sales['settled']['ids']);
            $trans_no_tax = $this->no_tax_sales($sales['settled']['ids']);
            $trans_zero_rated = $this->zero_rated_sales($sales['settled']['ids']);
            
            $gross = $trans_menus['gross'];
            
            $net = $trans['net'];
            $void = $trans['void'];
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
            $loc_txt = numInt(($local_tax));
            $net_no_adds = $net-($charges+$local_tax);
            
            $nontaxable = $no_tax - $no_tax_disc;
            
            $taxable = ($gross - $less_vat - $nontaxable - $zero_rated - $discounts) / 1.12;
            $total_net = ($taxable) + ($nontaxable+$zero_rated) + $tax + $local_tax;
            $add_gt = $taxable+$nontaxable+$zero_rated;
            $nsss = $taxable +  $nontaxable +  $zero_rated;
            
            $vat_ = $taxable * .12;

            $net_sales = $gross + $charges + $vat_ - $discounts - $less_vat;
            
            $pos_start = date2SqlDateTime($date." ".$open_time);
            $oa = date('a',strtotime($open_time));
            $ca = date('a',strtotime($close_time));
            $pos_end = date2SqlDateTime($date." ".$close_time);
            if($oa == $ca){
                $pos_end = date('Y-m-d H:i:s',strtotime($pos_end . "+1 days"));
            }

            $gt = $this->old_grand_net_total($pos_start);

            $types = $trans_discounts['types'];
            
            $sndisc = 0;
            $pwdisc = 0;
            $othdisc = 0;
            foreach ($types as $code => $val) {
                $amount = $val['amount'];
                if($code == 'PWDISC'){
                    
                    $pwdisc = $val['amount'];
                }elseif($code == 'SNDISC'){
                    $sndisc = $val['amount'];
                }else{
                    $othdisc += $val['amount'];
                }
               
            }
           
            $date_array[$date] = array(
                'cr_beg'=>iSetObj($trans['first_ref'],'trans_ref'),
                'cr_end'=>iSetObj($trans['last_ref'],'trans_ref'),
                'cr_count'=>$trans['ref_count'],
                'beg'=>$gt['old_grand_total'],
                'new'=>$gt['old_grand_total']+$net_no_adds,
                'ctr'=>$gt['ctr'],
                'vatsales'=>$taxable,
                'vatex'=>$nontaxable,
                'zero_rated'=>$zero_rated,
                'vat'=>$vat_,
                'net_sales'=>$net_sales,
                'pwdisc'=>$pwdisc,
                'sndisc'=>$sndisc,
                'othdisc'=>$othdisc,
                'lessvat'=>$less_vat,
                'gross'=>$gross,
                'charges'=>$charges,
                
            );
        
           
            update_load(75);
           
            $cashier = $this->cashier_model->get_cashout_header($get_shift[0]->cashout_id);
            $this->session->set_userdata('cashier_report',$cashier);
            $this->session->set_userdata('cashier_array',$date_array);
            $this->session->set_userdata('month_date',$date);

            
        }
        else{
            $error = "There is no shift found.";
            echo json_encode(array("code"=>"<pre style='background-color:#fff'>$error</pre>"));
        }
       
        update_load(100);
       
        
    }

    public function cashier_report_gen()
    {
        $this->load->model('dine/reports_model');         
        $cashier_array = $this->session->userData('cashier_array');
        $post = $this->input->post();
        // echo "<pre>",print_r($post),"</pre>";die();
        $results = $this->reports_model->get_trans_sales_per_cashier($post);
        $results_cashier = $this->reports_model->get_trans_sales_per_cashier($post, true);
        $receipt_discounts = $this->reports_model->get_receipt_discounts($post);
        $receipt_discounts_sales = $this->reports_model->get_receipt_discount_sales($post);
        $receipt_discount_payment_sales = $this->reports_model->get_receipt_discount_sales_per_payment($post);
        $total_charge_array = $total_cash_array = array('gross_cashier'=>0,'gross_fvn_cashier'=>0,
                                    'gross_wl_cashier'=>0,'gross_wln_cashier'=>0,
                                    'gross_sc_cashier'=>0,'gross_ft_tax_cashier'=>0,'gross_line_total_cashier'=>0);
        // echo "<pre>",print_r($receipt_discount_payment_sales),"</pre>";die();
         // $total_charge_array['gross_cashier'] += $gross_cashier; 
         //                        $total_charge_array['gross_fvn_cashier'] += $gross_fvn_cashier;
         //                        $total_charge_array['gross_wl_cashier'] += $gross_wl_cashier;
         //                        $total_charge_array['gross_wln_cashier'] += $gross_wln_cashier;
         //                        $total_charge_array['gross_sc_cashier'] += $gross_sc_cashier;
         //                        $total_charge_array['gross_ft_tax_cashier'] += $gross_ft_tax_cashier;
                               
         //                        $total_charge_array['gross_line_total_cashier'] += $gross_line_total_cashier;
                               

         // = $total_cash_array = array();
        $row_receipt_count  = count($receipt_discounts);
        // echo "<pre>",print_r($receipt_discount_payment_sales),"</pre>";die();
        $this->make->sDiv(array("style"=>"margin-top:58px;"));
        $less_adjustment_non_taxable = 0;
        $has_record = false;
        // $this->make->append('<p>No available data based on filters.</p>');
         if(!empty($results)){
            $has_record = true;
            $this->make->sTable(array("id"=>"main-tbl", 'class'=>'table reportTBL sortable ','width'=>'100%'));
                $this->make->sTableHead();
                    // $this->make->sRow();
                    //     $this->make->th('');
                    //     $this->make->th('',array('rowspan'=>'2'));
                    //     $this->make->th('',array('rowspan'=>'2'));
                    //     $this->make->th('',array('rowspan'=>'2'));
                    //     $this->make->th('',array('rowspan'=>'2'));
                    //     $this->make->th('',array('rowspan'=>'2'));
                    //     $this->make->th('',array('rowspan'=>'2'));
                    //     // $this->make->th('',array('rowspan'=>'2'));
                    //     $this->make->th('Discount',array('rowspan'=>'2','colspan'=>$row_receipt_count));
                    //     $this->make->th("",array('rowspan'=>'2'));
                    //     $this->make->th('',array('rowspan'=>'2'));
                    // $this->make->eRow();
                         $this->make->sRow();
                        $this->make->th('TOTAL Sales/Reading',array('class'=>"bold"));
                        $this->make->th('Food (V)',array('class'=>"bold"));
                        $this->make->th('Food (NV)',array('class'=>"bold"));
                        $this->make->th('W&L (V)',array('class'=>"bold"));
                        $this->make->th('W&L (NV)',array('class'=>"bold"));
                        $this->make->th('S. Charge',array('class'=>"bold"));
                        $this->make->th('F-Tax',array('class'=>"bold"));
                        // $this->make->td('W&L-Tax ',array());

                        // for($d = 0 ; $d < $row_receipt_count ; $d++ ){
                        foreach($receipt_discounts as $disc_id=>$receipt_disc){
                             $this->make->th($receipt_disc['disc_code'],array('class'=>"bold"));

 
                        }
                        // echo "<pre>",print_r($receipt_discounts_sales),"</pre>";die();
                         foreach($receipt_discounts_sales as $disc_id=>$receipt_disc_sales){
                            if($receipt_discounts[$disc_id]['no_tax'] == '0'){
                               $less_adjustment_non_taxable +=  $receipt_disc_sales['disc_amount'];
                                        
                            }
                        }

                   
                        
                       

                        // $this->make->td('Discount',array('rowspan'=>'2'));
                        $this->make->th("Others",array('class'=>"bold"));
                        $this->make->th("Totals&nbsp;&nbsp;&nbsp;&nbsp;",array('class'=>"bold"));
                    $this->make->eRow();

                     
                $this->make->eTableHead();
                $this->make->sTableBody();                    

                    $rows = array('Gross','Less:Adjustments','Net','Others: Add/(Less)','Total','Less:Charge Account','HSBC','Personal Account','Quick Delivery','Gift Certificate','Others');

                    $vatsales_total = 0;
                    $vatex_total = 0;
                    $vat_total = 0;
                    $total_discount = 0;
                    $total_charges = 0;
                    $total_row = 0;
                    $less_adjustment_total_line1 =  $less_adjustment_total_line2 = 0;
                    $less_adjustment_taxable = 0;

                    $ctr = 0;    
                    $ctr_d=1;              
                    $fl_vatable_total = $wl_vatable_total = $fl_non_vatable_total = $wl_non_vatable_total = $service_charge_total = $fl_tax_total = $wl_tax_total = $discount_total = $f_net_tax_total = $less_adjustment_non_taxable_cashier = 0;
                  
                       // $this->make->sRow();
                       //  $this->make->td('TOTAL Sales/Reading',array('class'=>"bold"));
                       //  $this->make->td('Food (V)',array('class'=>"bold"));
                       //  $this->make->td('Food (NV)',array('class'=>"bold"));
                       //  $this->make->td('W&L (V)',array('class'=>"bold"));
                       //  $this->make->td('W&L (NV)',array('class'=>"bold"));
                       //  $this->make->td('S. Charge',array('class'=>"bold"));
                       //  $this->make->td('F-Tax',array('class'=>"bold"));
                       //  // $this->make->td('W&L-Tax ',array());

                       //  // for($d = 0 ; $d < $row_receipt_count ; $d++ ){
                       //  foreach($receipt_discounts as $disc_id=>$receipt_disc){
                       //       $this->make->td($receipt_disc['disc_name'],array('class'=>"bold"));

 
                       //  }
                        //  foreach($receipt_discounts_sales as $disc_id=>$receipt_disc_sales){
                        //     if($receipt_discounts[$disc_id]['no_tax'] == '0'){
                        //        $less_adjustment_non_taxable +=  $receipt_disc_sales['disc_amount'];
                                        
                        //     }
                        // }

                   
                        
                        $less_adjustment_no_tax = $less_adjustment_non_taxable * BASE_TAX; 
                        // $less_adjustment_no_tax_cashier = $less_adjustment_non_taxable_cashier * BASE_TAX;
                        $f_net_tax_total -=  $less_adjustment_no_tax ;

                        // $this->make->td('Discount',array('rowspan'=>'2'));
                    //     $this->make->td("Others",array('class'=>"bold"));
                    //     $this->make->td('Totals',array('class'=>"bold"));
                    // $this->make->eRow();
               
                    foreach ($results as $date=>$vals) {  
                        $gross_sales = $vals['fl_amount'] + $vals['wl_amount'] + $vals['fli_amount'];
                        $total = $vals['fl_vatable'] + $vals['fl_non_vatable'] + $vals['wl_vatable'] + $vals['wl_non_vatable'] + $vals['vat'] + $vals['service_charge']; //-  ($vals['trans_discount']);
                        $discount = $vals['trans_discount'];

                        $fl_vatable_total += $vals['fl_vatable'];
                        $fl_non_vatable_total += $vals['fl_non_vatable'] ;
                        $wl_vatable_total += $vals['wl_vatable'];
                        $wl_non_vatable_total += $vals['wl_non_vatable'] ;
                        $fl_tax_total += $vals['fl_tax'];
                        $wl_tax_total += $vals['wl_tax'];
                        $total_charges += $vals['service_charge'];                  
                        $total_discount += $vals['trans_discount'];
                        // $vat_exempt = $vals['wl_non_vatable'] +  $vals['fl_non_vatable'];
                        $total_row += $total;
                        // $f_net_tax_total += $vals['vat'];
                        // $f_net_tax_total -= $vat_exempt;
                        $gross_fl_vat = ($vals['fl_amount'] + $vals['fli_amount'] ) /1.12;
                        $gross_wl_vat = $vals['wl_amount']/1.12;
                        $gross_fl_wl_vat = $gross_wl_vat + $gross_fl_vat;
                        $gross_vat = ($gross_fl_wl_vat) * BASE_TAX;
                        $gross_total = $gross_fl_wl_vat + $gross_vat;
                        $net_fl_vat = $gross_fl_vat -$vals['fl_non_vatable'];
                        $net_wl_vat = $gross_wl_vat -$vals['wl_non_vatable'];
                        $vat_exempt = $vals['vat_exempt'] - $less_adjustment_no_tax;
                        $f_net_tax_total = $gross_vat -  $vat_exempt - $less_adjustment_no_tax ;

                        $this->make->sRow();
                            $this->make->td($rows[$ctr]);
                            $this->make->td(num($gross_fl_vat), array("style"=>"text-align:right"));
                            $this->make->td("", array("style"=>"text-align:right"));
                            $this->make->td(num($gross_wl_vat), array("style"=>"text-align:right"));
                            $this->make->td("", array("style"=>"text-align:right"));
                            $this->make->td(num($vals['service_charge']), array("style"=>"text-align:right"));
                            $this->make->td(num($gross_vat), array("style"=>"text-align:right"));
                            // $this->make->td(num($vals['wl_tax']), array("style"=>"text-align:right"));
                            $this->make->td("", array("style"=>"text-align:right"));
                             $this->make->td("", array("style"=>"text-align:right"));
                              $this->make->td("", array("style"=>"text-align:right"));
                               $this->make->td("", array("style"=>"text-align:right"));
                                $this->make->td("", array("style"=>"text-align:right"));
                                 $this->make->td("", array("style"=>"text-align:right"));
                            $this->make->td(num(0), array("style"=>"text-align:right"));
                            $this->make->td(num($gross_total), array("style"=>"text-align:right"));
                        $this->make->eRow();

                         $this->make->sRow();
                            $this->make->td($rows[1]);
                            $this->make->td("", array("style"=>"text-align:right"));
                            $this->make->td("", array("style"=>"text-align:right"));
                            $this->make->td("", array("style"=>"text-align:right"));
                            $this->make->td("", array("style"=>"text-align:right"));
                            $this->make->td("", array("style"=>"text-align:right"));
                            $this->make->td('('.$less_adjustment_no_tax.')', array("style"=>"text-align:right"));
                            // $this->make->td("", array("style"=>"text-align:right"));
                            $less_adjustment_total_line1 += $less_adjustment_no_tax;
                            if(!empty($receipt_discounts_sales)){
                                foreach($receipt_discounts_sales as $disc_id => $receipt_disc_sales){
                                    $this->make->td('('.num($receipt_disc_sales['disc_amount']).')', array("style"=>"text-align:right"));
                                    $less_adjustment_total_line1 += $receipt_disc_sales['disc_amount'];
                                   

                                }

                            }
                            // $this->make->td(num($vals['trans_discount'] * -1), array("style"=>"text-align:right"));
                            $this->make->td(num(0), array("style"=>"text-align:right"));
                            $this->make->td('('.num($less_adjustment_total_line1).')', array("style"=>"text-align:right"));
                        $this->make->eRow();
                        
                         $this->make->sRow();
                            $this->make->td($rows[1]);
                            $this->make->td("", array("style"=>"text-align:right"));
                            $this->make->td("", array("style"=>"text-align:right"));
                            $this->make->td("", array("style"=>"text-align:right"));
                            $this->make->td("", array("style"=>"text-align:right"));
                            $this->make->td("", array("style"=>"text-align:right"));
                            $this->make->td('('.num($vat_exempt).')', array("style"=>"text-align:right"));
                            // $this->make->td("", array("style"=>"text-align:right"));

                            if(!empty($receipt_discounts_sales)){
                                foreach($receipt_discounts_sales as $receipt_disc_sales){
                                    $this->make->td('', array("style"=>"text-align:right"));
                                        
                                }

                            }
                            // $this->make->td(num($vals['trans_discount'] * -1), array("style"=>"text-align:right"));
                            $this->make->td(num(0), array("style"=>"text-align:right"));
                            $this->make->td('('.num($vat_exempt).')', array("style"=>"text-align:right"));
                        $this->make->eRow();
                        $this->make->sRow();
                        $this->make->td($rows[2]);
                            $this->make->td( num($net_fl_vat), array("style"=>"text-align:right"));
                            $this->make->td( num($vals['fl_non_vatable']), array("style"=>"text-align:right"));
                            $this->make->td(num($net_wl_vat), array("style"=>"text-align:right"));
                            $this->make->td(num($vals['wl_non_vatable']), array("style"=>"text-align:right"));
                            $this->make->td(num($vals['service_charge']), array("style"=>"text-align:right"));
                            $this->make->td(num($f_net_tax_total), array("style"=>"text-align:right"));
                            // $this->make->td("", array("style"=>"text-align:right"));

                           if(!empty($receipt_discounts_sales)){
                                foreach($receipt_discounts_sales as $receipt_disc_sales){
                                    $this->make->td('('.num($receipt_disc_sales['disc_amount']).')', array("style"=>"text-align:right"));
                                        
                                }

                            }
                            // $this->make->td(num($vals['trans_discount'] * -1), array("style"=>"text-align:right"));

                            $net_total_total = $gross_total- $less_adjustment_total_line1 - $vat_exempt;

                            $this->make->td(num(0), array("style"=>"text-align:right"));
                            $this->make->td(num($net_total_total), array("style"=>"text-align:right"));
                        $this->make->eRow();
                         $this->make->sRow();
                         $this->make->td($rows[3]);
                            $this->make->td('', array("style"=>"text-align:right"));
                            $this->make->td('', array("style"=>"text-align:right"));
                            $this->make->td('', array("style"=>"text-align:right"));
                            $this->make->td('', array("style"=>"text-align:right"));
                            $this->make->td('', array("style"=>"text-align:right"));
                            $this->make->td('', array("style"=>"text-align:right"));
                            // $this->make->td("", array("style"=>"text-align:right"));

                           if(!empty($receipt_discounts_sales)){
                                foreach($receipt_discounts_sales as $receipt_disc_sales){
                                    $this->make->td('', array("style"=>"text-align:right"));
                                        
                                }

                            }
                            // $this->make->td(num($vals['trans_discount'] * -1), array("style"=>"text-align:right"));


                            $this->make->td('', array("style"=>"text-align:right"));
                            $this->make->td('', array("style"=>"text-align:right"));
                        $this->make->eRow();
                        $this->make->sRow();
                            $this->make->td($rows[4]);
                            $this->make->td('', array("style"=>"text-align:right"));
                            $this->make->td('', array("style"=>"text-align:right"));
                            $this->make->td('', array("style"=>"text-align:right"));
                            $this->make->td('', array("style"=>"text-align:right"));
                            $this->make->td('', array("style"=>"text-align:right"));
                            $this->make->td('', array("style"=>"text-align:right"));
                            // $this->make->td("", array("style"=>"text-align:right"));

                           if(!empty($receipt_discounts_sales)){
                                foreach($receipt_discounts_sales as $receipt_disc_sales){
                                    $this->make->td('', array("style"=>"text-align:right"));
                                        
                                }

                            }
                            // $this->make->td(num($vals['trans_discount'] * -1), array("style"=>"text-align:right"));


                            $this->make->td('', array("style"=>"text-align:right"));
                            $this->make->td('', array("style"=>"text-align:right"));
                        $this->make->eRow();
                        $this->make->sRow();
                            $this->make->td($rows[5]);
                            $this->make->td('', array("style"=>"text-align:right"));
                            $this->make->td('', array("style"=>"text-align:right"));
                            $this->make->td('', array("style"=>"text-align:right"));
                            $this->make->td('', array("style"=>"text-align:right"));
                            $this->make->td('', array("style"=>"text-align:right"));
                            $this->make->td('', array("style"=>"text-align:right"));
                            // $this->make->td("", array("style"=>"text-align:right"));

                           if(!empty($receipt_discounts_sales)){
                                foreach($receipt_discounts_sales as $receipt_disc_sales){
                                    $this->make->td('', array("style"=>"text-align:right"));
                                        
                                }

                            }
                            // $this->make->td(num($vals['trans_discount'] * -1), array("style"=>"text-align:right"));


                            $this->make->td('', array("style"=>"text-align:right"));
                            $this->make->td('', array("style"=>"text-align:right"));
                        $this->make->eRow();
                           // echo "<pre>receipt_discount_payment_sales: ",print_r($receipt_discount_payment_sales),"</pre>";die();
                        $receipt_discount_payment_arr = array();
                        foreach($receipt_discount_payment_sales as $disc_id=>$payment_type_raw){
                            foreach($payment_type_raw as $payment_type => $p_details){
                                if(isset($receipt_discount_payment_arr[$payment_type]) ){
                                   $receipt_discount_payment_arr[$payment_type] +=  $p_details['disc_amount'];
                                            
                                }else{
                                    if(isset($p_details['disc_amount'])){

                                        $receipt_discount_payment_arr[$payment_type] = $p_details['disc_amount'];
                                    }
                                }
                            }
                        }

                                                // echo "<pre>",print_r($receipt_discount_payment_arr),"</pre>";die();

                      
                        $ctr++;
                    }    

                    // if($ctr < 11){
                    //     foreach($rows as $each){
                    //         $this->make->sRow();
                    //             $this->make->td($each, array('colspan'=>'11'));
                    //         $this->make->sRow();

                    //         $ctr++;
                    //     }
                        
                    // }  

                    // echo "<pre>",print_r($total_charge_array),"</pre>";
                    //                     echo "<pre>",print_r($total_cash_array),"</pre>";

                    // die();
                      foreach($results_cashier as $res_cash){
                            if(!empty($res_cash['card_type'])){
                                $payment_type_label = ucwords($res_cash['payment_type'].' - '.$res_cash['card_type']);
                                $p_type_label = $res_cash['payment_type'].' - '.$res_cash['card_type'];
                            }else{
                                $payment_type_label = ucwords($res_cash['payment_type']);
                                $p_type_label = $res_cash['payment_type'];

                            }
                            $gross_fv_cashier = $res_cash['fl_amount'] + $res_cash['fli_amount'];
                            $gross_cashier = ($gross_fv_cashier / 1.12) - $res_cash['fl_non_vatable'] ;
                            $gross_fvn_cashier = $res_cash['fl_non_vatable'];
                            $gross_wl_cashier = ($res_cash['wl_amount']/1.12) -$res_cash['wl_non_vatable'] ;
                            $gross_wln_cashier = $res_cash['wl_non_vatable'] ;
                            $gross_sc_cashier = $res_cash['service_charge'] ;
                            $less_adjustment_no_tax_cashier = $gross_line_total_cashier= 0;
                            if(isset($receipt_discount_payment_arr[$p_type_label])){
                                $less_adjustment_no_tax_cashier = $receipt_discount_payment_arr[$p_type_label] * BASE_TAX;
                            }
                            $gross_ft_tax_cashier = ((($gross_fv_cashier/1.12)+($res_cash['wl_amount']/1.12)) * .12) -  ($res_cash['vat_exempt']-$less_adjustment_no_tax_cashier) - $less_adjustment_no_tax_cashier ;

                            $gross_discount_cashier = 0;
                            // $f_net_tax_total = (($gross_fv_cashier+$gross_wl_cashier) * .12) -  $res_cash['vat_exempt'] - $less_adjustment_no_tax_cashier ;
                            // $gross_ft_tax_cashier;die();
                            if( $res_cash['payment_type'] !='cash'){
                                $this->make->sRow();

                                    $this->make->td($payment_type_label);
                                    $this->make->td(num($gross_cashier), array("style"=>"text-align:right"));
                                    $this->make->td(num($gross_fvn_cashier), array("style"=>"text-align:right"));
                                    $this->make->td(num($gross_wl_cashier), array("style"=>"text-align:right"));
                                    $this->make->td(num($gross_wln_cashier), array("style"=>"text-align:right"));
                                    $this->make->td(num($gross_sc_cashier), array("style"=>"text-align:right"));
                                    $this->make->td(num($gross_ft_tax_cashier), array("style"=>"text-align:right"));
                                    // $this->make->td("", array("style"=>"text-align:right"));

                                foreach($receipt_discounts_sales as $rdisc_id=>$receipt_disc_sales){

                                    if(isset($receipt_discount_payment_sales[$rdisc_id][$res_cash['payment_type']])){

                                        $gross_discount_cashier += $receipt_discount_payment_sales[$rdisc_id]['disc_amount'];
                                        if(isset( $total_charge_array['gross_discount_cashier'][$rdisc_id])){

                                                $total_charge_array['gross_discount_cashier'][$rdisc_id] += $receipt_discount_payment_sales[$rdisc_id]['disc_amount'];
                                                $this->make->td('('.num($receipt_discount_payment_sales[$rdisc_id]['disc_amount']) . ')', array("style"=>"text-align:right"));

                                        }else{
                                            if(isset($receipt_discount_payment_sales[$rdisc_id]['disc_amount'])){
                                                $total_charge_array['gross_discount_cashier'][$rdisc_id] = $receipt_discount_payment_sales[$rdisc_id]['disc_amount'];
                                                $this->make->td('('.num($receipt_discount_payment_sales[$rdisc_id]['disc_amount'] ). ')', array("style"=>"text-align:right"));

                                            }else{
                                                $total_charge_array['gross_discount_cashier'][$rdisc_id] = 0;
                                              $this->make->td(0, array("style"=>"text-align:right"));


                                            }
                                        }
                                        
                                        // $total_charge_array['gross_discount_cashier'][$rdisc_id] += $receipt_discount_payment_sales[$rdisc_id]['disc_amount'];

                                    }else{
                                        $this->make->td(0, array("style"=>"text-align:right"));

                                    }

                                }
                                // $this->make->td(num($vals['trans_discount'] * -1), array("style"=>"text-align:right"));

                                $gross_line_total_cashier = $gross_cashier + $gross_fvn_cashier +  $gross_wl_cashier + $gross_wln_cashier + $gross_sc_cashier + $gross_ft_tax_cashier - $gross_discount_cashier;
                                $this->make->td(0, array("style"=>"text-align:right"));
                                $this->make->td(num($gross_line_total_cashier), array("style"=>"text-align:right"));
                                $this->make->eRow();

                                $total_charge_array['gross_cashier'] += $gross_cashier; 
                                $total_charge_array['gross_fvn_cashier'] += $gross_fvn_cashier;
                                $total_charge_array['gross_wl_cashier'] += $gross_wl_cashier;
                                $total_charge_array['gross_wln_cashier'] += $gross_wln_cashier;
                                $total_charge_array['gross_sc_cashier'] += $gross_sc_cashier;
                                $total_charge_array['gross_ft_tax_cashier'] += $gross_ft_tax_cashier;
                               
                                $total_charge_array['gross_line_total_cashier'] += $gross_line_total_cashier;
                               

                            }else{
                                foreach($receipt_discounts_sales as $rdisc_id=>$receipt_disc_sales){

                                    if(isset($receipt_discount_payment_sales[$rdisc_id])) {

                                        // $this->make->td('('.num($receipt_discount_payment_sales[$rdisc_id][$p_type_label]['disc_amount']) . ')', array("style"=>"text-align:right"));
                                        $gross_discount_cashier += $receipt_discount_payment_sales[$rdisc_id][$res_cash['payment_type']]['disc_amount'];
                                        if(isset( $total_cash_array['gross_discount_cashier'][$rdisc_id])){

                                                $total_cash_array['gross_discount_cashier'][$rdisc_id] += $receipt_discount_payment_sales[$rdisc_id][$res_cash['payment_type']]['disc_amount'];
                                        }else{
                                            if(isset( $receipt_discount_payment_sales[$rdisc_id][$res_cash['payment_type']]['disc_amount'])){
                                                $total_cash_array['gross_discount_cashier'][$rdisc_id] = $receipt_discount_payment_sales[$rdisc_id][$res_cash['payment_type']]['disc_amount'];
                                            }else{
                                                $total_cash_array['gross_discount_cashier'][$rdisc_id] = 0;

                                            }
                                        }

                                    }else{
                                        // $this->make->td(0, array("style"=>"text-align:right"));

                                    }

                                }
                                $total_cash_array['gross_cashier'] += $gross_cashier; 
                                $total_cash_array['gross_fvn_cashier'] += $gross_fvn_cashier;
                                $total_cash_array['gross_wl_cashier'] += $gross_wl_cashier;
                                $total_cash_array['gross_wln_cashier'] += $gross_wln_cashier;
                                $total_cash_array['gross_sc_cashier'] += $gross_sc_cashier;
                                $total_cash_array['gross_ft_tax_cashier'] += $gross_ft_tax_cashier;
                                // $total_cash_array['gross_discount_cashier'] += $gross_discount_cashier;
                                $total_cash_array['gross_line_total_cashier'] += $gross_line_total_cashier;
                            }


                        }

                    $this->make->sRow();
                        $this->make->td('Total Charges Sales');
                        if(isset($total_charge_array['gross_cashier'])){
                             $this->make->td(num($total_charge_array['gross_cashier']), array("style"=>"text-align:right"));
                            $this->make->td(num($total_charge_array['gross_fvn_cashier']), array("style"=>"text-align:right"));
                            $this->make->td(num($total_charge_array['gross_wl_cashier']), array("style"=>"text-align:right"));
                            $this->make->td(num($total_charge_array['gross_wln_cashier']), array("style"=>"text-align:right"));
                            $this->make->td(num( $total_charge_array['gross_sc_cashier'] ), array("style"=>"text-align:right"));      
                            $this->make->td(num( $total_charge_array['gross_ft_tax_cashier']), array("style"=>"text-align:right"));
                             foreach($receipt_discounts_sales as $rdisc_id=>$receipt_disc_sales){
                                if(isset( $total_charge_array['gross_discount_cashier'][$rdisc_id])){

                                    $this->make->td('('.num(  $total_charge_array['gross_discount_cashier'][$rdisc_id]) .')', array("style"=>"text-align:right"));
                                }else{
                                    $this->make->td(num(  0), array("style"=>"text-align:right"));

                                }

                             }
                            // $this->make->th(num(0), array("style"=>"text-align:right"));
                            // $this->make->th(num(0), array("style"=>"text-align:right"));
                             $this->make->td(num(0), array("style"=>"text-align:right"));
                            $this->make->td(num($total_charge_array['gross_line_total_cashier']), array("style"=>"text-align:right"));
                        }else{
                              $this->make->td(num(0), array("style"=>"text-align:right"));
                            $this->make->td(num(0), array("style"=>"text-align:right"));
                            $this->make->td(num(0), array("style"=>"text-align:right"));
                            $this->make->td(num(0), array("style"=>"text-align:right"));
                            $this->make->td(num(0), array("style"=>"text-align:right"));      
                            $this->make->td(num(0), array("style"=>"text-align:right"));
                              foreach($receipt_discounts_sales as $rdisc_id=>$receipt_disc_sales){
                               
                                    $this->make->td(num(  0), array("style"=>"text-align:right"));


                             }
                            // $this->ma
                            $this->make->td(num(0), array("style"=>"text-align:right"));
                            $this->make->td(num(0), array("style"=>"text-align:right"));
              
                        }
                      
                    $this->make->eRow(); 
                    $this->make->sRow();
                        $this->make->td('Total Cash Sales');
                        // echo "<pre>",print_r($total_cash_array),"</pre>";die();
                      if(isset($total_cash_array['gross_cashier'])){
                            $t_disc_total = 0;
                            $this->make->td(num($total_cash_array['gross_cashier']), array("style"=>"text-align:right"));
                            $this->make->td(num($total_cash_array['gross_fvn_cashier']), array("style"=>"text-align:right"));
                            $this->make->td(num($total_cash_array['gross_wl_cashier']), array("style"=>"text-align:right"));
                            $this->make->td(num($total_cash_array['gross_wln_cashier']), array("style"=>"text-align:right"));
                            $this->make->td(num( $total_cash_array['gross_sc_cashier'] ), array("style"=>"text-align:right"));      
                            $this->make->td(num( $total_cash_array['gross_ft_tax_cashier']), array("style"=>"text-align:right"));
                             foreach($receipt_discounts_sales as $rdisc_id=>$receipt_disc_sales){
                                if(isset( $total_cash_array['gross_discount_cashier'][$rdisc_id])){
                                    $t_disc_total += $total_cash_array['gross_discount_cashier'][$rdisc_id];
                                    $this->make->td('('.num(  $total_cash_array['gross_discount_cashier'][$rdisc_id]) . ')', array("style"=>"text-align:right"));
                                }else{
                                    $this->make->td(num(  0), array("style"=>"text-align:right"));

                                }

                             }
                            // $this->make->th(num(0), array("style"=>"text-align:right"));
                            // $this->make->th(num(0), array("style"=>"text-align:right"));
                             $this->make->td(num(0), array("style"=>"text-align:right"));
                             $total_cash_array_total = $total_cash_array['gross_cashier'] + $total_cash_array['gross_fvn_cashier'] + $total_cash_array['gross_wl_cashier'] +$total_cash_array['gross_wln_cashier'] +$total_cash_array['gross_sc_cashier'] + $total_cash_array['gross_ft_tax_cashier'] - $t_disc_total;
                            $this->make->td(num($total_cash_array_total), array("style"=>"text-align:right"));
                        }else{
                              $this->make->td(num(0), array("style"=>"text-align:right"));
                            $this->make->td(num(0), array("style"=>"text-align:right"));
                            $this->make->td(num(0), array("style"=>"text-align:right"));
                            $this->make->td(num(0), array("style"=>"text-align:right"));
                            $this->make->td(num(0), array("style"=>"text-align:right"));      
                            $this->make->td(num(0), array("style"=>"text-align:right"));
                              foreach($receipt_discounts_sales as $rdisc_id=>$receipt_disc_sales){
                               
                                    $this->make->td(num(  0), array("style"=>"text-align:right"));


                             }
                            // $this->ma
                            $this->make->td(num(0), array("style"=>"text-align:right"));
                            $this->make->td(num(0), array("style"=>"text-align:right"));
              
                        }
                      
                    $this->make->eRow(); 

                $this->make->eTableBody();
            $this->make->eTable();
        }else{
            $this->make->append('No records found.');
        }
        $this->make->eDiv();        
        
        update_load(80);

        $details = $this->setup_model->get_branch_details(false,$this->input->post('branch_id'));
        $code = $this->make->code();
        $json['code'] = $code;      
        $json['has_record'] = $has_record;  
        $json['dates'] = $this->input->post('date');
        $json['cashier_name'] = $this->reports_model->get_user($this->input->post('cashier'));
        $json['branch_name'] = $details[0]->branch_name;
        $json['address'] = $details[0]->address;
        update_load(100);
        echo json_encode($json);
    }

    public function cashier_report_pdf()
    {
        // Include the main TCPDF library (search for installation path).
        require_once( APPPATH .'third_party/tcpdf.php');
        $this->load->model("dine/setup_model");
        date_default_timezone_set('Asia/Manila');

        $date = $this->input->post();
        $cashier = $this->input->post('cashier');
        // $post

        $results = $this->reports_model->get_trans_sales_per_cashier($post);
        $results_cashier = $this->reports_model->get_trans_sales_per_cashier($post, true);
        $receipt_discounts = $this->reports_model->get_receipt_discounts();
        $receipt_discounts_sales = $this->reports_model->get_receipt_discount_sales($post);
        $receipt_discount_payment_sales = $this->reports_model->get_receipt_discount_sales_per_payment($post);
        $total_charge_array = $total_cash_array = array('gross_cashier'=>0,'gross_fvn_cashier'=>0,
                                    'gross_wl_cashier'=>0,'gross_wln_cashier'=>0,
                                    'gross_sc_cashier'=>0,'gross_ft_tax_cashier'=>0,'gross_line_total_cashier'=>0);
        // create new PDF document
        $pdf = new TCPDF("P", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('iPOS');
        $pdf->SetTitle('Cashier Report');
        $pdf->SetSubject('');
        $pdf->SetKeywords('');

        // set default header data
        $setup = $this->setup_model->get_details(1);
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
        $this->menu_model->db = $this->load->database('main', TRUE);
        $this->load->model("dine/menu_model");
        start_load(0);

        // set font
        $pdf->SetFont('helvetica', 'B', 11);

        // add a page
        $pdf->AddPage();
        
        $cashier = $this->session->userData('cashier_report');
        $cashier_array = $this->session->userData('cashier_array');
        $month_date = $this->session->userData('month_date');

        $pdf->SetFont('helvetica','',7);

        $pdf->Write(0, "Cashier's Report", '', 0, 'L', true, 0, false, false, 0);
        $pdf->SetLineStyle(array('width' => 0.6, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => 'black'));
        $pdf->Cell(180, 0, '', 'T', 0, 'C');
        $pdf->ln(0.9);      
        $pdf->Write(0, 'Cashier:    ', '', 0, 'L', false, 0, false, false, 0);
        $pdf->Write(0, $cashier ? $cashier->username : '', '', 0, 'L', false, 0, false, false, 0);
        $pdf->setX(10);
        $pdf->Write(0, '', '', 0, 'L', true, 0, false, false, 0);

        $pdf->Write(0, 'Report Period:    ', '', 0, 'L', false, 0, false, false, 0);
        $pdf->Write(0, $month_date, '', 0, 'L', false, 0, false, false, 0);
        $pdf->setX(130);
        $pdf->Write(0, 'Report Generated:    '.(new \DateTime())->format('Y-m-d H:i:s'), '', 0, 'L', true, 0, false, false, 0);

        $shift = $cashier ? $cashier->check_in . ' - ' . $cashier->check_out : '';
        $pdf->Write(0, 'Transaction Time:    '.$shift, '', 0, 'L', false, 0, false, false, 0);
        $pdf->setX(130);
        $user = $this->session->userdata('user');
        $pdf->Write(0, 'Generated by:    '.$user["full_name"], '', 0, 'L', true, 0, false, false, 0);        
        $pdf->ln(1);      
        $pdf->Cell(180, 0, '', 'T', 0, 'C');
        $pdf->ln();              

         $pdf->SetFont('helvetica','',7);

        // echo "<pre>", print_r($trans), "</pre>";die();
        
        // -----------------------------------------------------------------------------
        $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => 'black'));
        $pdf->Cell(25, 0, 'Total Sales Reading', 'B', 0, 'L');        
        $pdf->Cell(15, 0, 'Food (V)', 'B', 0, 'R');        
        $pdf->Cell(15, 0, 'Food (NV)', 'B', 0, 'R');        
        $pdf->Cell(15, 0, 'W&L (V)', 'B', 0, 'R');        
        $pdf->Cell(15, 0, 'W&L (NV)', 'B', 0, 'R');        
        $pdf->Cell(15, 0, 'S. Charge', 'B', 0, 'R');        
        $pdf->Cell(15, 0, 'F-Tax', 'B', 0, 'R');  
        $pdf->Cell(15, 0, 'W&L-Tax', 'B', 0, 'R');
        $pdf->Cell(15, 0, 'Discount', 'B', 0, 'R');  
        $pdf->Cell(15, 0, 'Others', 'B', 0, 'R');  
        $pdf->Cell(20, 0, 'Total', 'B', 0, 'R');            
       
        $pdf->ln(); 

        $rows = array('Gross','Less:Adjustments','Net','Others: Add/(Less)','Total','Less:Charge Account','HSBC','Personal Account','Quick Delivery','Gift Certificate','Others'); 

        foreach($rows as $each){
            $pdf->Cell(10, 0, $each, '', 0, 'L');
            $pdf->ln();
        }

        $pdf->setY(48);

        $vatsales_total = 0;
        $vatex_total = 0;
        $vat_total = 0;
        $total_discount = 0;
        $total_charges = 0;
        $total_row = 0;
        
        foreach ($cashier_array as $date => $vals) { 
            $total = $vals['vatsales'] + $vals['vatex'] + $vals['zero_rated'] + $vals['vat'] + $vals['charges'] -  ($vals['sndisc'] + $vals['pwdisc'] + $vals['lessvat'] + $vals['othdisc']);
            $discount = $vals['sndisc'] + $vals['pwdisc'] + $vals['lessvat'] + $vals['othdisc'];

            $vatsales_total += $vals['vatsales'];
            $vatex_total += $vals['vatex'] + $vals['zero_rated'];
            $vat_total += $vals['vat'];
            $total_charges += $vals['charges'];                  
            $total_discount += $discount;
            $total_row += $total;

            $pdf->Cell(25, 0, '', '', 0, 'R');
            $pdf->Cell(15, 0, num($vals['vatsales']), '', 0, 'R');    
            $pdf->Cell(15, 0, num($vals['vatex'] + $vals['zero_rated']), '', 0, 'R');        
            $pdf->Cell(15, 0, num(0), '', 0, 'R');
            $pdf->Cell(15, 0, num(0), '', 0, 'R');
            $pdf->Cell(15, 0, num($vals['charges']), '', 0, 'R');
            $pdf->Cell(15, 0, num($vals['vat']), '', 0, 'R');
            $pdf->Cell(15, 0, num(0), '', 0, 'R'); 
            $pdf->Cell(15, 0, num($discount * -1), '', 0, 'R'); 
            $pdf->Cell(15, 0, num(0), '', 0, 'R'); 
            $pdf->Cell(20, 0, num($total), '', 0, 'R');    

            $pdf->ln();               
        }

        update_load(100);

        $pdf->setY(83); 

        // update_load(100);        

        $pdf->Cell(25, 0, "Total Charge Sales", 'T', 0, 'L');        
        $pdf->Cell(15, 0, num(0), 'T', 0, 'R');            
        $pdf->Cell(15, 0, num(0), 'T', 0, 'R');        
        $pdf->Cell(15, 0, num(0), 'T', 0, 'R');        
        $pdf->Cell(15, 0, num(0), 'T', 0, 'R');
        $pdf->Cell(15, 0, num(0), 'T', 0, 'R'); 
        $pdf->Cell(15, 0, num(0), 'T', 0, 'R'); 
        $pdf->Cell(15, 0, num(0), 'T', 0, 'R'); 
        $pdf->Cell(15, 0, num(0), 'T', 0, 'R'); 
        $pdf->Cell(15, 0, num(0), 'T', 0, 'R'); 
        $pdf->Cell(20, 0, num(0), 'T', 0, 'R');

        $pdf->ln();

        $pdf->Cell(25, 0, "Total Cash Sales", 'T', 0, 'L');        
        $pdf->Cell(15, 0, num($vatsales_total), 'T', 0, 'R');            
        $pdf->Cell(15, 0, num($vatex_total), 'T', 0, 'R');        
        $pdf->Cell(15, 0, num(0), 'T', 0, 'R');        
        $pdf->Cell(15, 0, num(0), 'T', 0, 'R');
        $pdf->Cell(15, 0, num($total_charges), 'T', 0, 'R'); 
        $pdf->Cell(15, 0, num($vat_total), 'T', 0, 'R'); 
        $pdf->Cell(15, 0, num(0), 'T', 0, 'R'); 
        $pdf->Cell(15, 0, num($total_discount * -1), 'T', 0, 'R'); 
        $pdf->Cell(15, 0, num(0), 'T', 0, 'R');
        $pdf->Cell(20, 0, num($total_row), 'T', 0, 'R'); 

        // -----------------------------------------------------------------------------

        //Close and output PDF document
        $pdf->Output('cashier_report.pdf', 'I');

        //============================================================+
        // END OF FILE
        //============================================================+   
    }

    public function cashier_report_excel(){
        //diretso excel na
        
        $this->load->library('Excel');
        $cashier = $this->session->userData('cashier_report');
        $cashier_array = $this->session->userData('cashier_array');
        $month_date = $this->session->userData('month_date');
        $sheet = $this->excel->getActiveSheet();
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

        $rn = 1;
        $sheet->mergeCells('A'.$rn.':M'.$rn);
        $sheet->getCell('A'.$rn)->setValue($branch['name']);
        $sheet->getStyle('A'.$rn)->applyFromArray($styleTitle);
        $rn++;

        $sheet->mergeCells('A'.$rn.':M'.$rn);
        $sheet->getCell('A'.$rn)->setValue($branch['address']);
        $sheet->getStyle('A'.$rn)->applyFromArray($styleTitle);
        $rn++;

        $sheet->mergeCells('A'.$rn.':G'.$rn);
        $sheet->getCell('A'.$rn)->setValue("Cashier's Report");
        $sheet->getStyle('A'.$rn)->applyFromArray($styleTxt);
        $rn++;

        $sheet->mergeCells('A'.$rn.':D'.$rn);
        $username = $cashier ? $cashier->username : '';
        $sheet->getCell('A'.$rn)->setValue('Cashier: '.$username);

        $rn++;
        $sheet->mergeCells('A'.$rn.':D'.$rn);
        $sheet->getCell('A'.$rn)->setValue('Report Period: '.$month_date);
        $sheet->getStyle('A'.$rn)->applyFromArray($styleTxt);
        $sheet->mergeCells('G'.$rn.':M'.$rn);
        $sheet->getCell('G'.$rn)->setValue('Report Generated: '.(new \DateTime())->format('Y-m-d H:i:s'));
     
        $rn++;

        $sheet->mergeCells('A'.$rn.':D'.$rn);
        $shift = $cashier ? $cashier->check_in . ' - ' . $cashier->check_out : '';
        $sheet->getCell('A'.$rn)->setValue('Transaction Time: '.$shift);
        $sheet->getStyle('A'.$rn)->applyFromArray($styleTxt);
        $user = $this->session->userdata('user');
        $sheet->mergeCells('G'.$rn.':M'.$rn);
        $sheet->getCell('G'.$rn)->setValue('Generated by:    '.$user["full_name"]);
        $sheet->getStyle('G'.$rn)->applyFromArray($styleTxt);
        
        $rn = 8;
        $sheet->getCell('A'.$rn)->setValue('Total Sales/Reading');
        $sheet->getCell('B'.$rn)->setValue('Food(V)');
        $sheet->getCell('C'.$rn)->setValue('Food(NV)');
        $sheet->getCell('D'.$rn)->setValue('W&L(V)');
        $sheet->getCell('E'.$rn)->setValue('W&L(V)');
        $sheet->getCell('F'.$rn)->setValue('S. Charge');
        $sheet->getCell('G'.$rn)->setValue('F-Tax');
        $sheet->getCell('H'.$rn)->setValue('W&L-Tax');
        $sheet->getCell('I'.$rn)->setValue('Discount');
        $sheet->getCell('J'.$rn)->setValue('Others');
        $sheet->getCell('K'.$rn)->setValue('Total');

        $sheet->getStyle("A".$rn.":K8")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A'.$rn.':'.'K8')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getStyle('A'.$rn.':'.'K8')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $sheet->getStyle('A'.$rn.':'.'K8')->getFill()->getStartColor()->setRGB('29bb04');
        $sheet->getStyle('A1:'.'K8')->getFont()->setBold(true);

        $rn = 9;

        $rows = array('Gross','Less:Adjustments','Net','Others: Add/(Less)','Total','Less:Charge Account','HSBC','Personal Account','Quick Delivery','Gift Certificate','Others'); 

        foreach($rows as $each){
            $sheet->getCell('A'.$rn)->setValue($each);
            $rn++;
        }

        $rn = 9;
        
        if($cashier_array){
            $vatsales_total = 0;
            $vatex_total = 0;
            $vat_total = 0;
            $total_discount = 0;
            $total_charges = 0;
            $total_row = 0;
            foreach($cashier_array as $date => $vals){
                $sheet->getCell('A'.$rn)->setValue(date('d',strtotime($date)));
                
                if($vals['vatsales']){
                    $total = $vals['vatsales'] + $vals['vatex'] + $vals['zero_rated'] + $vals['vat'] + $vals['charges'] -  ($vals['sndisc'] + $vals['pwdisc'] + $vals['lessvat'] + $vals['othdisc']);
                    $discount = $vals['sndisc'] + $vals['pwdisc'] + $vals['lessvat'] + $vals['othdisc'];

                    $vatsales_total += $vals['vatsales'];
                    $vatex_total += $vals['vatex'] + $vals['zero_rated'];
                    $vat_total += $vals['vat'];
                    $total_charges += $vals['charges'];                  
                    $total_discount += $discount;
                    $total_row += $total;

                    $sheet->getStyle('B'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $sheet->getCell('B'.$rn)->setValue($vals['vatsales']);
                    $sheet->getStyle('C'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $sheet->getCell('C'.$rn)->setValue($vals['vatex'] + $vals['zero_rated']);
                    $sheet->getStyle('D'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $sheet->getCell('D'.$rn)->setValue(0);                       
                    $sheet->getStyle('E'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $sheet->getCell('E'.$rn)->setValue(0);
                    $sheet->getStyle('F'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $sheet->getCell('F'.$rn)->setValue($vals['charge']);
                     $sheet->getStyle('G'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $sheet->getCell('G'.$rn)->setValue($vals['vat']);
                    $sheet->getStyle('H'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $sheet->getCell('H'.$rn)->setValue(0);
                    $sheet->getStyle('I'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $sheet->getCell('I'.$rn)->setValue($discount * -1);
                    $sheet->getStyle('J'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $sheet->getCell('J'.$rn)->setValue(0);
                    $sheet->getStyle('K'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $sheet->getCell('K'.$rn)->setValue($total);
                }
                $rn++;
            }

            $rn = 20;

            $sheet->getStyle('A'.$rn.':R'.$rn)->getFont()->setBold(true);
            $sheet->getCell('A'.$rn)->setValue('Total Charge Sales');
            $sheet->getStyle('B'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('B'.$rn)->setValue(0);
            $sheet->getStyle('C'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('C'.$rn)->setValue(0);
            $sheet->getStyle('D'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('D'.$rn)->setValue(0);                
            $sheet->getStyle('E'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('E'.$rn)->setValue(0);
            $sheet->getStyle('F'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('F'.$rn)->setValue(0);
            $sheet->getStyle('G'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('G'.$rn)->setValue(0);
            $sheet->getStyle('H'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('H'.$rn)->setValue(0);
            $sheet->getStyle('I'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('I'.$rn)->setValue(0);
            $sheet->getStyle('J'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('J'.$rn)->setValue(0);
            $sheet->getStyle('K'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('K'.$rn)->setValue(0);

            $rn++;
            $sheet->getStyle('A'.$rn.':R'.$rn)->getFont()->setBold(true);
            $sheet->getCell('A'.$rn)->setValue('Total Cash Sales');
            $sheet->getStyle('B'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('B'.$rn)->setValue($vatsales_total);
            $sheet->getStyle('C'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('C'.$rn)->setValue($vatex_total);
            $sheet->getStyle('D'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('D'.$rn)->setValue(0);                
            $sheet->getStyle('E'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('E'.$rn)->setValue(0);
            $sheet->getStyle('F'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('F'.$rn)->setValue($total_charges);
            $sheet->getStyle('G'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('G'.$rn)->setValue($vat_total);
            $sheet->getStyle('H'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('H'.$rn)->setValue(0);
            $sheet->getStyle('I'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('I'.$rn)->setValue($total_discount * -1);
            $sheet->getStyle('J'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('J'.$rn)->setValue(0);
            $sheet->getStyle('K'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('K'.$rn)->setValue($total_row);
        }
      

        if (ob_get_contents()) 
            ob_end_clean();
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename=cashier('.date('Y_m_d').').xls');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $objWriter->save('php://output');
    }

    public function charge_sales_summary_report(){
        $this->load->model('dine/reports_model');
        $this->load->helper('dine/reports_helper');
        $data = $this->syter->spawn('monthly');
        $data['page_title'] = "Charge Sales Summary Report";
        $data['code'] = cashierUi();
        $data['add_css'] = array('css/morris/morris.css','css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css');
        $data['add_js'] = array('js/plugins/morris/morris.min.js','js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js');
        $data['load_js'] = 'dine/reporting.php';
        $data['use_js'] = 'cashierChargeJS';
        $this->load->view('page',$data);
    }

    public function cashier_charge_report_gen()
    {
        $this->load->model('dine/reports_model');         
        $cashier_array = $this->session->userData('cashier_array');
        $post = $this->input->post();
        $branch_id = $post['branch_id'];
        // echo "<pre>",print_r($post),"</pre>";die();
        $results = $this->reports_model->get_trans_sales_credit_charges($post);
        $receipt_discounts = $this->reports_model->get_receipt_discounts($post);
        $receipt_discounts_sales = $this->reports_model->get_receipt_discount_sales_per_payment($post,true);
        // echo "<pre>",print_r($receipt_discounts_sales),"</pre>";die();
        // $results_cashier = $this->reports_model->get_trans_sales_per_cashier($post, true);
        // $receipt_discounts = $this->reports_model->get_receipt_discounts();
        // $receipt_discounts_sales = $this->reports_model->get_receipt_discount_sales($post);
        // $receipt_discount_payment_sales = $this->reports_model->get_receipt_discount_sales_per_payment($post);
        // $total_charge_array = $total_cash_array = array('gross_cashier'=>0,'gross_fvn_cashier'=>0,
        //                             'gross_wl_cashier'=>0,'gross_wln_cashier'=>0,
        //                             'gross_sc_cashier'=>0,'gross_ft_tax_cashier'=>0,'gross_line_total_cashier'=>0);

         // $total_charge_array['gross_cashier'] += $gross_cashier; 
         //                        $total_charge_array['gross_fvn_cashier'] += $gross_fvn_cashier;
         //                        $total_charge_array['gross_wl_cashier'] += $gross_wl_cashier;
         //                        $total_charge_array['gross_wln_cashier'] += $gross_wln_cashier;
         //                        $total_charge_array['gross_sc_cashier'] += $gross_sc_cashier;
         //                        $total_charge_array['gross_ft_tax_cashier'] += $gross_ft_tax_cashier;
                               
         //                        $total_charge_array['gross_line_total_cashier'] += $gross_line_total_cashier;
                               

         // = $total_cash_array = array();
        $row_receipt_count  = count($receipt_discounts);
        // echo "<pre>",print_r($receipt_discount_payment_sales),"</pre>";die();
        $this->make->sDiv();
            $this->make->sTable(array("id"=>"main-tbl", 'class'=>'table reportTBL sortable '));
                $this->make->sTableHead();
                    // $this->make->sRow();
                    //     $this->make->th('');
                    //     $this->make->th('',array('rowspan'=>'2'));
                    //     $this->make->th('',array('rowspan'=>'2'));
                    //     $this->make->th('',array('rowspan'=>'2'));
                    //     $this->make->th('',array('rowspan'=>'2'));
                    //     $this->make->th('',array('rowspan'=>'2'));
                    //     $this->make->th('',array('rowspan'=>'2'));
                    //     // $this->make->th('',array('rowspan'=>'2'));
                    //     $this->make->th('Discount',array('rowspan'=>'2','colspan'=>$row_receipt_count));
                    //     $this->make->th("",array('rowspan'=>'2'));
                    //     $this->make->th('',array('rowspan'=>'2'));
                    // $this->make->eRow();
                         $this->make->sRow();
                        $this->make->th('Customer/ Credit Card',array('class'=>"bold"));
                            $this->make->th('Receipt #',array('class'=>"bold"));
                        $this->make->th('Cash Sales',array('class'=>"bold"));
                        $this->make->th('Food (V)',array('class'=>"bold"));
                        $this->make->th('Food (NV)',array('class'=>"bold"));
                        $this->make->th('W&L (V)',array('class'=>"bold"));
                        $this->make->th('W&L (NV)',array('class'=>"bold"));
                        $this->make->th('S. Charge',array('class'=>"bold"));
                        $this->make->th('SC Adj',array('class'=>"bold"));
                        $this->make->th('VAT',array('class'=>"bold"));
                        $this->make->th('VAT Adj.',array('class'=>"bold"));
                        // $this->make->td('W&L-Tax ',array());

                        // for($d = 0 ; $d < $row_receipt_count ; $d++ ){
                        foreach($receipt_discounts as $disc_id=>$receipt_disc){
                             $this->make->th($receipt_disc['disc_code'],array('class'=>"bold"));

 
                        }
                         $less_adjustment_non_taxable = 0;
                        if(!empty($receipt_discounts_sales)){
                            
                            foreach($receipt_discounts_sales as $disc_id=>$receipt_disc_sales){

                                if(isset($receipt_discounts[$disc_id]) && $receipt_discounts[$disc_id]['no_tax'] == '0'){


                                  // echo "adsadsad: ".print_r($receipt_disc_sales),"</pre>";
                                    // if(isset($receipt_discounts[$disc_id]) ){

                                         $less_adjustment_non_taxable +=  $receipt_disc_sales['disc_amount'];
                                    // }
                                            
                                }
                            }
                        }

                        $this->make->th("Totals&nbsp;&nbsp;&nbsp;&nbsp;",array('class'=>"bold"));
                   
                        
                       

                        // $this->make->td('Discount',array('rowspan'=>'2'));
                    $this->make->eRow();

                     
                $this->make->eTableHead();
                $this->make->sTableBody();                    


                    $vatsales_total = 0;
                    $vatex_total = 0;
                    $vat_total = 0;
                    $total_discount = 0;
                    $total_charges = 0;
                    $total_row = 0;
                    $less_adjustment_total_line1 =  $less_adjustment_total_line2 = 0;
                    $less_adjustment_taxable = 0;

                    $ctr = 0;    
                    $ctr_d=1;              
                    $fl_vatable_total = $wl_vatable_total = $fl_non_vatable_total = $wl_non_vatable_total = $service_charge_total = $fl_tax_total = $wl_tax_total = $discount_total = $f_net_tax_total   = $less_adjustment_non_taxable_cashier = 0;
                  
                       // $this->make->sRow();
                       //  $this->make->td('TOTAL Sales/Reading',array('class'=>"bold"));
                       //  $this->make->td('Food (V)',array('class'=>"bold"));
                       //  $this->make->td('Food (NV)',array('class'=>"bold"));
                       //  $this->make->td('W&L (V)',array('class'=>"bold"));
                       //  $this->make->td('W&L (NV)',array('class'=>"bold"));
                       //  $this->make->td('S. Charge',array('class'=>"bold"));
                       //  $this->make->td('F-Tax',array('class'=>"bold"));
                       //  // $this->make->td('W&L-Tax ',array());

                       //  // for($d = 0 ; $d < $row_receipt_count ; $d++ ){
                       //  foreach($receipt_discounts as $disc_id=>$receipt_disc){
                       //       $this->make->td($receipt_disc['disc_name'],array('class'=>"bold"));

 
                       //  }
                        //  foreach($receipt_discounts_sales as $disc_id=>$receipt_disc_sales){
                        //     if($receipt_discounts[$disc_id]['no_tax'] == '0'){
                        //        $less_adjustment_non_taxable +=  $receipt_disc_sales['disc_amount'];
                                        
                        //     }
                        // }

                   
                        
                        $less_adjustment_no_tax = $less_adjustment_non_taxable * BASE_TAX; 
                        // $less_adjustment_no_tax_cashier = $less_adjustment_non_taxable_cashier * BASE_TAX;
                        $f_net_tax_total -=  $less_adjustment_no_tax ;

                        // $this->make->td('Discount',array('rowspan'=>'2'));
                    //     $this->make->td("Others",array('class'=>"bold"));
                    //     $this->make->td('Totals',array('class'=>"bold"));
                    // $this->make->eRow();
                    $cur_card_type="";
                    $total_based = $grand_total = array();
                    $grand_total = array('gross_fl_vat'=>0,'fl_non_vatable_total'=>0,
                                'gross_wl_vat'=>0,'wl_non_vatable_total'=>0,'service_charge'=>0,'gross_vat'=>0,'gross_total'=>0);

                    if(!empty($results)){

                        foreach ($results as $date=>$vals) {  
                            $gross_sales = $vals['fl_amount'] + $vals['wl_amount'] + $vals['fli_amount'];
                            $total = $vals['fl_vatable'] + $vals['fl_non_vatable'] + $vals['wl_vatable'] + $vals['wl_non_vatable'] + $vals['vat'] + $vals['service_charge']; //-  ($vals['trans_discount']);
                            $discount = $vals['trans_discount'];

                            $fl_vatable_total += $vals['fl_vatable'];
                            $fl_non_vatable_total += $vals['fl_non_vatable'] ;
                            $wl_vatable_total += $vals['wl_vatable'];
                            $wl_non_vatable_total += $vals['wl_non_vatable'] ;
                            $fl_tax_total += $vals['fl_tax'];
                            $wl_tax_total += $vals['wl_tax'];
                            $total_charges += $vals['service_charge'];                  
                            $total_discount += $vals['trans_discount'];
                            // $vat_exempt = $vals['wl_non_vatable'] +  $vals['fl_non_vatable'];
                            $total_row += $total;
                            // $f_net_tax_total += $vals['vat'];
                            // $f_net_tax_total -= $vat_exempt;
                            $gross_fl_vat = ($vals['fl_amount'] + $vals['fli_amount'] ) /1.12;
                            $gross_wl_vat = $vals['wl_amount']/1.12;
                            $gross_fl_wl_vat = $gross_wl_vat + $gross_fl_vat;
                            $gross_vat = ($gross_fl_wl_vat) * BASE_TAX;
                            $gross_total = $gross_fl_wl_vat + $gross_vat;
                            $net_fl_vat = $gross_fl_vat -$vals['fl_non_vatable'];
                            $net_wl_vat = $gross_wl_vat -$vals['wl_non_vatable'];
                            $vat_exempt = $vals['vat_exempt'] - $less_adjustment_no_tax;
                            $f_net_tax_total = $gross_vat -  $vat_exempt - $less_adjustment_no_tax ;
                            $cur_type = $vals['payment_type']."-".$vals['card_type'];
                           

                            if($cur_card_type != $cur_type){
                                

                                if($cur_card_type != ""){
                                    $this->make->sRow();
                                        $this->make->td("TOTAL",array("style"=>"font-weight:bold"));
                                        $this->make->td("", array("style"=>"text-align:right"));
                                        $this->make->td("", array("style"=>"text-align:right"));
                                        $this->make->td(num($total_based[$cur_card_type]['gross_fl_vat']) , array("style"=>"text-align:right;font-weight:bold"));
                                        $this->make->td(num($total_based[$cur_card_type]['fl_non_vatable_total']), array("style"=>"text-align:right;font-weight:bold"));
                                        $this->make->td(num($total_based[$cur_card_type]['gross_wl_vat']), array("style"=>"text-align:right;font-weight:bold"));
                                        $this->make->td(num($total_based[$cur_card_type]['wl_non_vatable_total']), array("style"=>"text-align:right;font-weight:bold"));

                                        $this->make->td(num($total_based[$cur_card_type]['service_charge']), array("style"=>"text-align:right;font-weight:bold"));
                                        $this->make->td("", array("style"=>"text-align:right"));
                                        $this->make->td(num($total_based[$cur_card_type]['gross_vat']), array("style"=>"text-align:right;font-weight:bold"));
                                        $this->make->td("", array("style"=>"text-align:right"));
                                        if(!empty($receipt_discounts)){
                                            foreach($receipt_discounts as $disc_id => $receipt_disc_sales){
                                                if(isset($total_based[$cur_card_type]['discs'][$disc_id])){

                                                    $this->make->td('('.num($total_based[$cur_card_type]['discs'][$disc_id]).')', array("style"=>"text-align:right;font-weight:bold"));
                                                    
                                                }else{
                                                    $this->make->td(0, array("style"=>"text-align:right;font-weight:bold"));

                                                }

                                               

                                            }

                                        }
                                        $this->make->td(num($total_based[$cur_card_type]['gross_total']), array("style"=>"text-align:right;font-weight:bold"));
                                      
                                    $this->make->eRow();

                                }

                                $this->make->sRow();
                                    $this->make->td(ucwords($cur_type), array("style"=>"font-weight:bold"));
                                     $this->make->td("", array("style"=>"text-align:right"));
                                        $this->make->td("", array("style"=>"text-align:right"));
                                        $this->make->td("", array("style"=>"text-align:right"));
                                        $this->make->td("", array("style"=>"text-align:right"));
                                        $this->make->td("", array("style"=>"text-align:right"));
                                        $this->make->td("", array("style"=>"text-align:right"));
                                        // $this->make->td(num($vals['wl_tax']), array("style"=>"text-align:right"));
                                        $this->make->td("", array("style"=>"text-align:right"));
                                        $this->make->td("", array("style"=>"text-align:right"));
                                        $this->make->td("", array("style"=>"text-align:right"));
                                        $this->make->td("", array("style"=>"text-align:right"));
                                        $this->make->td("", array("style"=>"text-align:right"));
                                        $this->make->td("", array("style"=>"text-align:right"));
                                        $this->make->td("", array("style"=>"text-align:right"));
                                        $this->make->td("", array("style"=>"text-align:right"));
                                        $this->make->td("", array("style"=>"text-align:right"));
                                        $this->make->td("", array("style"=>"text-align:right"));
                                        $this->make->td("", array("style"=>"text-align:right"));

                                $this->make->eRow();
                                 $total_based[$cur_type] = array('gross_fl_vat'=>0,'fl_non_vatable_total'=>0,
                                    'gross_wl_vat'=>0,'wl_non_vatable_total'=>0,'service_charge'=>0,'gross_vat'=>0,'gross_total'=>0);

                            }

                            $this->make->sRow();
                                $this->make->td($vals['card_number']);
                                $this->make->td($vals['trans_ref'], array("style"=>"text-align:right"));
                                $this->make->td("", array("style"=>"text-align:right"));
                                $this->make->td(num($gross_fl_vat), array("style"=>"text-align:right"));
                                $this->make->td(num($vals['fl_non_vatable']), array("style"=>"text-align:right"));
                                $this->make->td(num($gross_wl_vat), array("style"=>"text-align:right"));
                                $this->make->td(num($vals['wl_non_vatable']), array("style"=>"text-align:right"));
                                // $this->make->td(num($vals['wl_tax']), array("style"=>"text-align:right"));
                                $this->make->td(num($vals['service_charge']), array("style"=>"text-align:right"));
                                $this->make->td("", array("style"=>"text-align:right"));
                                $this->make->td(num($gross_vat), array("style"=>"text-align:right"));
                                $this->make->td("", array("style"=>"text-align:right"));
                                 if(!empty($receipt_discounts)){
                                    foreach($receipt_discounts as $disc_id => $receipt_disc_sales){
                                        if(isset($receipt_discounts_sales[$vals['trans_ref']][$cur_type][$disc_id])){

                                            $this->make->td('('.num($receipt_discounts_sales[$vals['trans_ref']][$cur_type][$disc_id]['disc_amount']).')', array("style"=>"text-align:right"));
                                            if(isset($total_based[$cur_type]['discs'][$disc_id])){
                                              $total_based[$cur_type]['discs'][$disc_id] += $receipt_discounts_sales[$vals['trans_ref']][$cur_type][$disc_id]['disc_amount'];
                                            }else{
                                              $total_based[$cur_type]['discs'][$disc_id] = $receipt_discounts_sales[$vals['trans_ref']][$cur_type][$disc_id]['disc_amount'];
             
                                            }

                                            if(isset($grand_total['discs'][$disc_id])){
                                                 $grand_total['discs'][$disc_id] += $receipt_discounts_sales[$vals['trans_ref']][$cur_type][$disc_id]['disc_amount'];
                                            }else{
                                                $grand_total['discs'][$disc_id] = $receipt_discounts_sales[$vals['trans_ref']][$cur_type][$disc_id]['disc_amount'];
                                            }
                                        }else{
                                            $this->make->td(0, array("style"=>"text-align:right"));

                                        }

                                        if(isset($receipt_disc_sales['disc_amount'])){

                                                $less_adjustment_total_line1 += $receipt_disc_sales['disc_amount'];
                                        }
                                       

                                    }

                                }
                                $this->make->td(num($gross_total), array("style"=>"text-align:right"));


                               $total_based[$cur_type]['gross_fl_vat'] += $gross_fl_vat;
                                $total_based[$cur_type]['fl_non_vatable_total'] += $fl_non_vatable_total;
                                $total_based[$cur_type]['gross_wl_vat'] += $gross_wl_vat;
                                $total_based[$cur_type]['wl_non_vatable_total'] += $wl_non_vatable_total;
                                $total_based[$cur_type]['service_charge'] += $vals['service_charge'];
                                 $total_based[$cur_type]['gross_vat'] += $gross_vat;
                                 $total_based[$cur_type]['gross_total'] += $gross_total;


                                $grand_total['gross_fl_vat'] +=  $gross_fl_vat;
                                $grand_total['fl_non_vatable_total'] += $fl_non_vatable_total;
                                $grand_total['gross_wl_vat'] += $gross_wl_vat;
                                $grand_total['wl_non_vatable_total'] += $wl_non_vatable_total;
                                $grand_total['service_charge'] += $vals['service_charge'];
                                $grand_total['gross_vat'] +=  $gross_vat;
                                $grand_total['gross_total'] +=  $gross_total;
                            $this->make->eRow();

                          
                             

                          // echo "<pre>",print_r($receipt_discount_payment_arr),"</pre>";die();

                          $cur_card_type = $cur_type;
                        }    
                         $this->make->sRow();
                                        $this->make->td("TOTAL",array("style"=>"font-weight:bold"));
                                        $this->make->td("", array("style"=>"text-align:right"));
                                        $this->make->td("", array("style"=>"text-align:right"));
                                        $this->make->td(num($total_based[$cur_card_type]['gross_fl_vat']) , array("style"=>"text-align:right;font-weight:bold"));
                                        $this->make->td(num($total_based[$cur_card_type]['fl_non_vatable_total']), array("style"=>"text-align:right;font-weight:bold"));
                                        $this->make->td(num($total_based[$cur_card_type]['gross_wl_vat']), array("style"=>"text-align:right;font-weight:bold"));
                                        $this->make->td(num($total_based[$cur_card_type]['wl_non_vatable_total']), array("style"=>"text-align:right;font-weight:bold"));

                                        $this->make->td(num($total_based[$cur_card_type]['service_charge']), array("style"=>"text-align:right;font-weight:bold"));
                                        $this->make->td("", array("style"=>"text-align:right"));
                                        $this->make->td(num($total_based[$cur_card_type]['gross_vat']), array("style"=>"text-align:right;font-weight:bold"));
                                        $this->make->td("", array("style"=>"text-align:right"));
                                        if(!empty($receipt_discounts)){
                                            foreach($receipt_discounts as $disc_id => $receipt_disc_sales){
                                                if(isset($total_based['discs'][$disc_id])){

                                                    $this->make->td('('.num($total_based[$cur_card_type]['discs'][$disc_id]).')', array("style"=>"text-align:right;font-weight:bold"));
                                                    
                                                }else{
                                                    $this->make->td(0, array("style"=>"text-align:right;font-weight:bold"));

                                                }

                                               

                                            }

                                        }
                                        $this->make->td(num($total_based[$cur_card_type]['gross_total']), array("style"=>"text-align:right;font-weight:bold"));
                                      
                         $this->make->eRow();
                    }




                      $this->make->sRow();
                            $this->make->td("&nbsp;",array("style"=>"font-weight:bold"));
                            $this->make->td("", array("style"=>"text-align:right"));
                            $this->make->td("", array("style"=>"text-align:right"));
                            $this->make->td("" , array("style"=>"text-align:right"));
                            $this->make->td("", array("style"=>"text-align:right"));
                            $this->make->td("", array("style"=>"text-align:right"));
                            $this->make->td("", array("style"=>"text-align:right"));

                            $this->make->td("", array("style"=>"text-align:right"));
                            $this->make->td("", array("style"=>"text-align:right"));
                            $this->make->td("", array("style"=>"text-align:right"));
                            $this->make->td("", array("style"=>"text-align:right"));
                            if(!empty($receipt_discounts)){
                                foreach($receipt_discounts as $disc_id => $receipt_disc_sales){
                                    if(isset($total_based['discs'][$disc_id])){

                                        $this->make->td("", array("style"=>"text-align:right"));
                                                
                                    }else{
                                        $this->make->td("", array("style"=>"text-align:right"));

                                   }
                                          
                                }

                             }
                            $this->make->td("", array("style"=>"text-align:right"));
                                  
                     $this->make->eRow();
                      $this->make->sRow();
                                    $this->make->td("GRAND TOTAL",array("style"=>"font-weight:bold"));
                                    $this->make->td("", array("style"=>"text-align:right"));
                                    $this->make->td("", array("style"=>"text-align:right"));
                                    $this->make->td(num($grand_total['gross_fl_vat']) , array("style"=>"text-align:right;font-weight:bold"));
                                    $this->make->td(num($grand_total['fl_non_vatable_total']), array("style"=>"text-align:right;font-weight:bold"));
                                    $this->make->td(num($grand_total['gross_wl_vat']), array("style"=>"text-align:right;font-weight:bold"));
                                    $this->make->td(num($grand_total['wl_non_vatable_total']), array("style"=>"text-align:right;font-weight:bold"));

                                    $this->make->td(num($grand_total['service_charge']), array("style"=>"text-align:right;font-weight:bold"));
                                    $this->make->td("", array("style"=>"text-align:right"));
                                    $this->make->td(num($grand_total['gross_vat']), array("style"=>"text-align:right;font-weight:bold"));
                                    $this->make->td("", array("style"=>"text-align:right"));
                                    if(!empty($receipt_discounts)){
                                        foreach($receipt_discounts as $disc_id => $receipt_disc_sales){
                                            if(isset($grand_total['discs'][$disc_id])){

                                                $this->make->td('('.num($grand_total['discs'][$disc_id]).')', array("style"=>"text-align:right;font-weight:bold"));
                                                
                                            }else{
                                                $this->make->td(0, array("style"=>"text-align:right;font-weight:bold"));

                                            }

                                           

                                        }

                                    }
                                    $this->make->td(num($grand_total['gross_total']), array("style"=>"text-align:right;font-weight:bold"));
                                  
                     $this->make->eRow();
                      
                        
                $this->make->eTableBody();
            $this->make->eTable();
        $this->make->eDiv();        
        
        update_load(80);

        $details = $this->setup_model->get_branch_details($branch_id);
        // echo $this->db->last_query();
        // echo $branch_id;die();
        // echo "<pre>",print_r($details),"</pre>";die();
        $code = $this->make->code();
        $json['code'] = $code;        
        $json['dates'] = $this->input->post('date');
        $json['cashier_name'] = $this->reports_model->get_user($this->input->post('cashier'));
        $json['branch_name'] = $details[0]->branch_name;
        $json['address'] = $details[0]->address;
        update_load(100);
        echo json_encode($json);
    }
    public function food_server_rep()
    {
        $data = $this->syter->spawn('sales_rep');        
        $data['page_title'] = fa('fa-money')." Food Server Report";
        $data['code'] = food_rep();
        $data['add_css'] = array('css/morris/morris.css','css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css');
        $data['add_js'] = array('js/plugins/morris/morris.min.js','js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js');
        $data['page_no_padding'] = false;
        $data['sideBarHide'] = false;
        $data['load_js'] = 'dine/reporting';
        $data['use_js'] = 'foodserverJS';
        $this->load->view('page',$data);
    }
    public function food_server_gen()
    {
        $this->load->model('dine/setup_model');
        $this->load->model("dine/menu_model");
        $branch_code = $this->input->post("branch_id");        
        $setup = $this->setup_model->get_details($branch_code);
        $set = $setup[0];
        start_load(0);    
        $daterange = $this->input->post("calendar_range");
        $cashier = $this->input->post("cashier");
        $dates = explode(" to ",$daterange);
        $from = date2SqlDateTime($dates[0]. " ".$set->store_open);        
        $to = date2SqlDateTime(date('Y-m-d', strtotime($dates[1] . ' +1 day')). " ".$set->store_open);
        $trans = $this->menu_model->get_food_server_rep($from, $to,$cashier,$branch_code);
        $tot_sales = 0;

        $this->make->sDiv();
            $this->make->sTable(array("id"=>"main-tbl", 'class'=>'table reportTBL sortable'));
                $this->make->sTableHead();
                    $this->make->sRow();
                        $this->make->th('Branch Code');
                        $this->make->th('Food Server');
                        $this->make->th('Transaction');
                        $this->make->th('Total Amount');
                    $this->make->eRow();
                $this->make->eTableHead();
                $this->make->sTableBody();
                    
                    foreach ($trans as $res) {
                        $service_charge_total = $modifiers_total = $disc_total = $vat_total =  0;
                        $this->make->sRow();
                            $this->make->td($res->trans_branch);
                            $this->make->td($res->fname." ".$res->lname);
                            $this->make->td($res->qty_sale);
                            $this->make->td(num($res->total_sales), array("style"=>"text-align:right"));
                        $this->make->eRow();
                        $det_raw = $this->menu_model->get_food_server_det($res->sales_id,$res->trans_branch);
                        $this->make->sRow();
                            $this->make->th('',array("colspan"=>"2"));
                            $this->make->th('Menu');
                            $this->make->th('Price');
                        $this->make->eRow();
                        foreach ($det_raw as $k => $det) {
                            $this->make->sRow();
                                $this->make->td("",array("colspan"=>"2"));
                                $this->make->td($det->menu_name." (".$det->qty.")");
                                $this->make->td(num($det->price), array("style"=>"text-align:right"));
                            $this->make->eRow();
                        }
                        (!empty($res->charges_amount)) ? $service_charge_total = $res->charges_amount : $service_charge_total = 0 ;
                        (!empty($res->disc_amount)) ? $disc_total = $res->disc_amount : $disc_total = 0 ;
                        (!empty($res->tax)) ? $$vat_total = $res->tax : $vat_total = 0;
                        $modifiers_total = $this->menu_model->get_modifiers_sale($res->sales_id,$res->trans_branch);

                        $this->make->sRow();
                            $this->make->td("", array("colspan"=>"2"));
                            $this->make->td("Service Charge");
                            $this->make->td(num($service_charge_total), array("style"=>"text-align:right"));
                        $this->make->eRow();
                        $this->make->sRow();
                            $this->make->td("", array("colspan"=>"2"));
                            $this->make->td("Modifiers");
                            $this->make->td(num($modifiers_total), array("style"=>"text-align:right"));
                        $this->make->eRow();
                        $this->make->sRow();
                            $this->make->td("", array("colspan"=>"2"));
                            $this->make->td("Discount");
                            $this->make->td("(".num($disc_total).")", array("style"=>"text-align:right"));
                        $this->make->eRow();
                        $this->make->sRow();
                            $this->make->td("", array("colspan"=>"2"));
                            $this->make->td("VAT Exempt");
                            $this->make->td("(".num($vat_total).")", array("style"=>"text-align:right"));
                        $this->make->eRow();
                        $tot_sales += $res->total_sales;
                    }    
                    $this->make->sRow();
                        $this->make->th('Grand Total',array("colspan"=>"3"));
                        $this->make->th(num($tot_sales), array("style"=>"text-align:right"));
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
    public function food_server_pdf()
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
        $pdf->SetTitle('Food Server Report');
        $pdf->SetSubject('');
        $pdf->SetKeywords('');

        // set default header data
        
        $branch_code = $_GET['branch_id'];
        $cashier = $_GET['cashier'];
        $setup = $this->setup_model->get_details($branch_code);
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

        // ---------------------------------------------------------
        $this->load->model('dine/setup_model');
        $this->load->model("dine/menu_model");
        start_load(0);

        // set font
        $pdf->SetFont('helvetica', 'B', 11);

        // add a page
        $pdf->AddPage();
        $daterange = $_GET['calendar_range'];        
        $dates = explode(" to ",$daterange);
        // $from = date2SqlDateTime($dates[0]);
        // $to = date2SqlDateTime($dates[1]);
        $from = date2SqlDateTime($dates[0]. " ".$set->store_open);        
        $to = date2SqlDateTime(date('Y-m-d', strtotime($dates[1] . ' +1 day')). " ".$set->store_open);
        $trans = $this->menu_model->get_food_server_rep($from, $to,$cashier,$branch_code);

        $pdf->Write(0, 'Sales Report', '', 0, 'L', true, 0, false, false, 0);
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

        // echo "<pre>", print_r($trans), "</pre>";die();

        // -----------------------------------------------------------------------------
        $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => 'black'));
        $pdf->Cell(75, 0, 'Branch Code', 'B', 0, 'L');
        $pdf->Cell(75, 0, 'Food Server', 'B', 0, 'L');
        $pdf->Cell(32, 0, 'Transaction', 'B', 0, 'R');
        $pdf->Cell(32, 0, 'Total Amount', 'B', 0, 'R');                
        $pdf->ln();                  

        // GRAND TOTAL VARIABLES
        $tot_sales = 0;
        $trans_count = count($trans);
        foreach ($trans as $k => $v) {
            $pdf->Cell(75, 0, $v->trans_branch, '', 0, 'L'); 
            $pdf->Cell(75, 0, $v->fname." ".$v->lname, '', 0, 'L');
            $pdf->Cell(32, 0, num($v->qty_sale), '', 0, 'R');
            $pdf->Cell(32, 0, num($v->total_sales), '', 0, 'R');
            $pdf->ln();  
            $pdf->Cell(150, 0, '', 'B', 0, 'L');
            $pdf->Cell(32, 0, 'Menu', 'B', 0, 'R');        
            $pdf->Cell(32, 0, 'Price', 'B', 0, 'R'); 
            
            $det_raw = $this->menu_model->get_food_server_det($v->sales_id,$v->trans_branch);
            foreach ($det_raw as $k => $det) {
                $pdf->ln();  
                $pdf->Cell(150, 0, "", '', 0, 'L');        
                $pdf->Cell(32, 0, $det->menu_name." (".$det->qty.")" , '', 0, 'L');        
                $pdf->Cell(32, 0, num($det->price), '', 0, 'R');

            }
            (!empty($v->charges_amount)) ? $service_charge_total = $v->charges_amount : $service_charge_total = 0 ;
            (!empty($v->disc_amount)) ? $disc_total = $v->disc_amount : $disc_total = 0 ;
            (!empty($v->tax)) ? $vat_total = $v->tax : $vat_total = 0;
            $modifiers_total = $this->menu_model->get_modifiers_sale($v->sales_id,$v->trans_branch);

            $pdf->SetFont('helvetica', 'BI', 9);
            $pdf->ln();  
            $pdf->Cell(150, 0, '', '', 0, 'L');
            $pdf->Cell(32, 0, 'Service Charge', '', 0, 'L');        
            $pdf->Cell(32, 0, num($service_charge_total), '', 0, 'R'); 
            $pdf->ln();  
            $pdf->Cell(150, 0, '', '', 0, 'L');
            $pdf->Cell(32, 0, 'Modifiers', '', 0, 'L');        
            $pdf->Cell(32, 0, num($modifiers_total), '', 0, 'R'); 
            $pdf->ln();  
            $pdf->Cell(150, 0, '', '', 0, 'L');
            $pdf->Cell(32, 0, 'Discount', '', 0, 'L');        
            $pdf->Cell(32, 0, "(".num($disc_total).")", '', 0, 'R'); 
            $pdf->ln();  
            $pdf->Cell(150, 0, '', '', 0, 'L');
            $pdf->Cell(32, 0, 'VAT Exempt', '', 0, 'L');        
            $pdf->Cell(32, 0, "(".num($vat_total).")", '', 0, 'R'); 

            $pdf->SetFont('helvetica', '', 9);
            $pdf->ln();  
            $tot_sales += $v->total_sales;                       
            $pdf->ln();
        }

        update_load(100);      
        $pdf->Cell(182, 0, "Grand Total", 'T', 0, 'L');         
        $pdf->Cell(32, 0, num($tot_sales), 'T', 0, 'R'); 
        $pdf->Output('sales_report.pdf', 'I');

        //============================================================+
        // END OF FILE
        //============================================================+   
    }
    public function foodserver_excel()
    {

        $this->load->model("dine/menu_model");
        date_default_timezone_set('Asia/Manila');
        $this->load->library('Excel');
        $sheet = $this->excel->getActiveSheet();
        $filename = 'Food Server Sales';
        $rc=1;
        #GET VALUES
        start_load(0);
            // $post = $this->set_post($_GET['calendar_range']);
        $branch_code = $_GET['branch_id'];
        $cashier = $_GET['cashier'];
        $setup = $this->setup_model->get_details($branch_code);
        $set = $setup[0];

        update_load(10);
        sleep(1);
        
        $menu_cat_id = $_GET['menu_cat_id'];        
        $daterange = $_GET['calendar_range'];        
        $dates = explode(" to ",$daterange);
        $from = date2SqlDateTime($dates[0]. " ".$set->store_open);        
        $to = date2SqlDateTime(date('Y-m-d', strtotime($dates[1] . ' +1 day')). " ".$set->store_open);
        $trans = $this->menu_model->get_food_server_rep($from, $to);
        // $trans = $this->menu_model->get_cat_sales_rep($from, $to, $menu_cat_id);
        // $trans_ret = $this->menu_model->get_cat_sales_rep_retail($from, $to, "");   
        // $trans_mod = $this->menu_model->get_mod_cat_sales_rep($from, $to, $menu_cat_id);
        // $trans_payment = $this->menu_model->get_payment_date($from, $to);   

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
        $styleHeaderCell2 = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'C5D9F1')
            ),
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'font' => array(
                'bold' => true,
                'size' => 12,
                'color' => array('rgb' => '000000'),
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
        $styleItalicLeft = array(
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
            'font' => array(
                'bold' => true,
                'italic' => true,
                'size' => 12,
            )
        );
        $styleItalicRight = array(
            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
            ),
            'font' => array(
                'bold' => true,
                'italic' => true,
                'size' => 12,
            )
        );
        
        $headers = array('Branch Code','Food Server','QTY', 'Sale');
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);



        $sheet->mergeCells('A'.$rc.':G'.$rc);
        $sheet->getCell('A'.$rc)->setValue($set->branch_name);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        $rc++;

        $sheet->mergeCells('A'.$rc.':G'.$rc);
        $sheet->getCell('A'.$rc)->setValue($set->address);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        $rc++;

        $sheet->mergeCells('A'.$rc.':G'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Sales Report');
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
        // $sheet->getCell('A'.$rc)->setValue('Transaction Time:');
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $user = $this->session->userdata('user');
        $sheet->mergeCells('E'.$rc.':G'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Generated by:    '.$user["full_name"]);
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
        $tot_sales = 0;

        foreach ($trans as $k => $v) {
            $sheet->getCell('A'.$rc)->setValue($v->trans_branch);
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue($v->fname." ".$v->lname);
            $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('C'.$rc)->setValue($v->qty_sale);
            $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
            $sheet->getCell('D'.$rc)->setValue(num($v->total_sales));
            $sheet->getStyle('D'.$rc)->applyFromArray($styleNum);
            $det_raw = $this->menu_model->get_food_server_det($v->sales_id,$v->trans_branch);
            $rc++;

            $sheet->getCell('C'.$rc)->setValue("Menu");
            $sheet->getCell('D'.$rc)->setValue("Price");
            $sheet->getStyle('A'.$rc)->applyFromArray($styleHeaderCell2);
            $sheet->getStyle('B'.$rc)->applyFromArray($styleHeaderCell2);
            $sheet->getStyle('C'.$rc)->applyFromArray($styleHeaderCell2);
            $sheet->getStyle('D'.$rc)->applyFromArray($styleHeaderCell2);
            foreach ($det_raw as $k => $det) {
                $rc++;
                $sheet->getCell('C'.$rc)->setValue($det->menu_name." (".$det->qty.")");
                $sheet->getStyle('C'.$rc)->applyFromArray($styleTxt);
                $sheet->getCell('D'.$rc)->setValue($det->price);
                $sheet->getStyle('D'.$rc)->applyFromArray($styleNum);
            }
            $rc++;
            (!empty($v->charges_amount)) ? $service_charge_total = $v->charges_amount : $service_charge_total = 0 ;
            (!empty($v->disc_amount)) ? $disc_total = $v->disc_amount : $disc_total = 0 ;
            (!empty($v->tax)) ? $vat_total = $v->tax : $vat_total = 0;
            $modifiers_total = $this->menu_model->get_modifiers_sale($v->sales_id,$v->trans_branch);

            $sheet->getCell('C'.$rc)->setValue("Service Charge");
            $sheet->getCell('D'.$rc)->setValue(num($service_charge_total));
            $sheet->getStyle('C'.$rc)->applyFromArray($styleItalicLeft);
            $sheet->getStyle('D'.$rc)->applyFromArray($styleItalicRight);
            $rc++;
            $sheet->getCell('C'.$rc)->setValue("Modifiers");
            $sheet->getCell('D'.$rc)->setValue(num($modifiers_total));
            $sheet->getStyle('C'.$rc)->applyFromArray($styleItalicLeft);
            $sheet->getStyle('D'.$rc)->applyFromArray($styleItalicRight);
            $rc++;
            $sheet->getCell('C'.$rc)->setValue("Discount");
            $sheet->getCell('D'.$rc)->setValue("(".num($disc_total).")");
            $sheet->getStyle('C'.$rc)->applyFromArray($styleItalicLeft);
            $sheet->getStyle('D'.$rc)->applyFromArray($styleItalicRight);
            $rc++;
            $sheet->getCell('C'.$rc)->setValue("VAT Exempt");
            $sheet->getCell('D'.$rc)->setValue("(".num($vat_total).")");
            $sheet->getStyle('C'.$rc)->applyFromArray($styleItalicLeft);
            $sheet->getStyle('D'.$rc)->applyFromArray($styleItalicRight);
            $tot_sales += $v->total_sales;
            $rc++;
            $rc++;      
        }

        $sheet->getCell('A'.$rc)->setValue('Grand Total');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('D'.$rc)->setValue(num($tot_sales));
        $sheet->getStyle('D'.$rc)->applyFromArray($styleBoldRight);
        $rc++; 

        update_load(100);        
       
        if (ob_get_contents())
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


    public function free_rep(){
        $data = $this->syter->spawn('promo_rep');        
        $data['page_title'] = fa('fa-money')." Free Redemption Report";
        $data['code'] = freeRep();
        $data['add_css'] = array('css/morris/morris.css','css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css','assets/global/plugins/select2/css/select2.min.css','assets/global/plugins/select2/css/select2-bootstrap.min.css');
        $data['add_js'] = array('js/plugins/morris/morris.min.js','js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js','assets/global/plugins/select2/js/select2.full.min.js','assets/pages/scripts/components-select2.min.js');
        $data['page_no_padding'] = false;
        $data['sideBarHide'] = false;
        $data['load_js'] = 'dine/reporting';
        $data['use_js'] = 'freeRepJS';
        $this->load->view('page',$data);
    }

    public function free_rep_gen(){
        $this->load->model('dine/setup_model');
        $this->load->model("dine/reports_model");
        $this->load->model("dine/menu_model");
    
        start_load(0);     

        $daterange = $this->input->post("calendar_range");
        $dates = explode(" to ",$daterange);
        
        $this->menu_model->db = $this->load->database('default', TRUE);

        $from = date2SqlDateTime($dates[0]);        
        $to = date2SqlDateTime(date('Y-m-d', strtotime($dates[1] . ' +1 day')));             
        $discount_id = $this->input->post("discount_id");  

        $trans = $this->reports_model->get_free($from, $to);
        // echo "<pre>",print_r($trans),"</pre>";die();

        $trans_count = count($trans);
        $counter = 0;
        $total = array();
        $total_amt = 0;

        $branches = array();
        $dates = array();
        $total = array();
        if($trans_count > 0){
            $this->make->sDiv();
                $this->make->sTable(array("id"=>"main-tbl", 'class'=>'table reportTBL sortable'));
                    $this->make->sTableHead();
                        $this->make->sRow();
                            // $this->make->th('Date');
                            $this->make->th('Free Reason');
                            // $this->make->th('Count');
                            foreach ($trans['branches_arr'] as $val) {
                                 $this->make->th($val);

                            //     if(!in_array($val->branch_code, $branches)){
                       
                            }                       
                        $this->make->eRow();
                    $this->make->eTableHead();
                    $this->make->sTableBody();

                        // foreach($dates as $date){
                                // $this->make->td(sql2Date($date));
                                
                                    foreach($trans['free_reason'] as $val){
                                        $this->make->sRow();
                                            $this->make->td(ucwords($val),array('style'=>'text-align:right'));

                                             foreach($trans['branches_arr'] as $i){
                                                if(isset($trans['result'][$i][$val])){
                                                    if(isset($total[$i])){
                                                        $total[$i] += $trans['result'][$i][$val];
                                                    }else{
                                                        $total[$i] = $trans['result'][$i][$val];
                                                    }
                                                    $this->make->td($trans['result'][$i][$val],array('style'=>'text-align:right'));
                                                }else{
                                                    if(isset($total[$i])){
                                                            $total[$i] = $total[$i];
                                                    }else{
                                                            $total[$i] = 0;
                                                    }

                                                     $this->make->td('0',array('style'=>'text-align:right;')); 
                                                }


                                            }
                                                           
                                        $this->make->eRow();
                                     }
                                     $this->make->sRow();
                                         $this->make->td('Total',array('style'=>'text-align:right;font-weight:bold;')); 
                                         foreach($trans['branches_arr'] as $i){
                                            $this->make->td($total[$i] ,array('style'=>'text-align:right;font-weight:bold;')); 
                                        }
                                     $this->make->eRow();
                                

                            // $counter++;
                            $progress = ($counter / $trans_count) * 100;
                            update_load(num($progress));
                                                  

                    $this->make->eTableBody();
                $this->make->eTable();
            $this->make->eDiv();
        }else{
            $this->make->sDiv();
                $this->make->sTable(array("id"=>"main-tbl", 'class'=>'table reportTBL sortable'));
                    $this->make->sTableHead();
                        $this->make->sRow();
                            $this->make->th('Date');                     
                        $this->make->eRow();
                    $this->make->eTableHead();
                    $this->make->sTableBody();                
                                                              
                        $this->make->sRow();
                                $this->make->td('No records found.');
                        $this->make->eRow();
                    $this->make->eTableBody();
                $this->make->eTable();
            $this->make->eDiv();
        }

        update_load(100);
        $code = $this->make->code();
        $json['code'] = $code;        
        $json['tbl_vals'] = $trans;
        $json['dates'] = $daterange;
        echo json_encode($json);
    }

    public function free_rep_pdf()
    {
         // Include the main TCPDF library (search for installation path).
        require_once( APPPATH .'third_party/tcpdf.php');
        $this->load->model("dine/setup_model");
        $this->load->model("dine/reports_model");

        date_default_timezone_set('Asia/Manila');

        // create new PDF document
        $pdf = new TCPDF("L", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('iPOS');
        $pdf->SetTitle('Free Redemption Report');
        $pdf->SetSubject('');
        $pdf->SetKeywords('');

        // set default header data
        $setup = $this->setup_model->get_details('');
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
        $this->menu_model->db = $this->load->database('default', TRUE);
        $this->load->model("dine/menu_model");
        start_load(0);

        // set font
        $pdf->SetFont('helvetica', 'B', 11);

        // add a page
        $pdf->AddPage();
               
        $daterange = $_GET["calendar_range"];        
        $dates = explode(" to ",$daterange);

        $discount_id = $_GET["discount_id"];

        $from = date2SqlDateTime($dates[0]. " ".$set->store_open);        
        $to = date2SqlDateTime(date('Y-m-d', strtotime($dates[1] . ' +1 day')));
          
        $trans = $this->reports_model->get_free($from, $to);

        $trans_count = count($trans);
        $counter = 0;
        $total = array();
        $total_amt = 0;

        $branches = array();
        $dates = array();

        $pdf->Write(0, 'Free Redemption Report', '', 0, 'L', true, 0, false, false, 0);
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
        
        $discount_id = $discount_id == '' ? 'All' : $discount_id; 
        // $pdf->Write(0, 'Free Reason', '', 0, 'L', false, 0, false, false, 0);
        // foreach ($trans['branches_arr'] as $val) {
        //     $pdf->Write(0, $val, '', 0, 'L', false, 0, false, false, 0);
        //                     //     if(!in_array($val->branch_code, $branches)){
                       
        // }  
        // $pdf->Write(0, $discount_id, '', 0, 'L', false, 0, false, false, 0);
        $pdf->setX(200);        
        $pdf->ln(5);      
        $pdf->Cell(267, 0, '', 'T', 0, 'C');
        $pdf->ln();              


        // -----------------------------------------------------------------------------
        $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => 'black'));
        $pdf->SetFont('helvetica','B',11); 

        $pdf->cell(40, 0, 'Free Reason', 'B', 0, 'L'); 
            foreach ($trans['branches_arr'] as $val) {
                $pdf->cell(40, 0, $val, 'B', 0, 'L'); 
                       
            }  

        $pdf->SetFont('helvetica','',10); 

            // echo "<pre>",print_r($trans['branches_arr']),"</pre>";die();
            foreach($trans['free_reason'] as $val){
                $pdf->ln();     
                $pdf->cell(40, 0, ucwords($val), 'B', 0, 'L'); 

                foreach($trans['branches_arr'] as $i){
                    if(isset($trans['result'][$i][$val])){
                            // var_dump($trans['result'][$i][$val]);die();
                        if(isset($total[$i])){
                            $total[$i] += $trans['result'][$i][$val];
                        }else{
                            $total[$i] = $trans['result'][$i][$val];
                        }
                        $pdf->cell(40, 0, $trans['result'][$i][$val], 'B', 0, 'L'); 
                    }else{
                        if(isset($total[$i])){
                            $total[$i] = $total[$i];
                        }else{
                            $total[$i] = 0;
                        }
                        $pdf->cell(40, 0,0, 'B', 0, 'L');
                    }
                }
                                                                           
            }
             $pdf->ln();  
             $pdf->SetFont('helvetica','B',11); 
             $pdf->cell(40, 0, 'Total', 'B', 0, 'L'); 
             // echo "<pre>",print_r($total),"</pre>";die();
              foreach($trans['branches_arr'] as $i){
                if(isset($total[$i])){

                 $pdf->cell(40, 0, $total[$i], 'B', 0, 'L'); 
                }else{
                    $pdf->cell(40, 0, 0, 'B', 0, 'L'); 
                }
              }   
        // foreach ($trans as $val) {
        //     if(!in_array($val->branch_code, $branches)){
        //         $branches[] = $val->branch_code;
        //         $pdf->cell(30, 0, $val->branch_code, 'B', 0, 'R');
        //     }

        //     if(!in_array($val->date, $dates)){
        //         $dates[] = $val->date;                                
        //     }
        // }  

        $pdf->ln();   

        if($trans_count > 0){
        
            $pdf->ln();
           
          
        }else{
            $pdf->Cell(50, 0, 'No records found.', '', 0, 'L'); 
            
            update_load(100);
        }  

        //Close and output PDF document
        $pdf->Output('free_redemption_report.pdf', 'I');

        //============================================================+
        // END OF FILE
        //============================================================+   
    }

    public function free_rep_excel()
    {
        $this->menu_model->db = $this->load->database('default', TRUE);
        $this->load->model("dine/menu_model");
        $this->load->model("dine/reports_model");

        date_default_timezone_set('Asia/Manila');
        $this->load->library('Excel');
        $sheet = $this->excel->getActiveSheet();
        $filename = 'Promo Redemption Report';
        $rc=1;
        #GET VALUES
        start_load(0);
        $setup = $this->setup_model->get_details('');
        $set = $setup[0];

        update_load(10);
        sleep(1);
           
        $daterange = $_GET["calendar_range"];        
        $dates = explode(" to ",$daterange);

        $discount_id = $_GET["discount_id"];

        $from = date2SqlDateTime($dates[0]);        
        $to = date2SqlDateTime(date('Y-m-d', strtotime($dates[1] . ' +1 day')));
          
        $trans = $this->reports_model->get_free($from, $to);

        $trans_count = count($trans);
        $counter = 0;
        $total = array();
        $total_amt = 0;

        $branches = array();
        $dates = array(); 

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
        
        $headers = array('Free Reason');
        $headers = array_merge($headers,$trans['branches_arr']);
        // foreach ($trans as $val) {
        //     if(!in_array($val->branch_code, $branches)){
        //         $branches[] = $val->branch_code;
        //         $headers[] = $val->branch_code;
        //     }

        //     if(!in_array($val->date, $dates)){
        //         $dates[] = $val->date;                                
        //     }
        // }        
        
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);


        // $sheet->mergeCells('A'.$rc.':N'.$rc);
        // $sheet->getCell('A'.$rc)->setValue($set->branch_name);
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        // $rc++;

        // $sheet->mergeCells('A'.$rc.':N'.$rc);
        // $sheet->getCell('A'.$rc)->setValue($set->address);
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        // $rc++;

        $sheet->mergeCells('A'.$rc.':N'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Free Redemption Report');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $rc++;

        $sheet->mergeCells('A'.$rc.':C'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Report Period: '.$daterange);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->mergeCells('D'.$rc.':F'.$rc);
        $sheet->getCell('D'.$rc)->setValue('Report Generated: '.(new \DateTime())->format('Y-m-d H:i:s'));
        $sheet->getStyle('D'.$rc)->applyFromArray($styleTxt);
        $rc++;

        $sheet->mergeCells('A'.$rc.':C'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Transaction Time:');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $user = $this->session->userdata('user');
        $sheet->mergeCells('D'.$rc.':F'.$rc);
        $sheet->getCell('D'.$rc)->setValue('Generated by:    '.$user["full_name"]);
        $sheet->getStyle('D'.$rc)->applyFromArray($styleTxt);
        $rc++;

        // $discount_id = $discount_id == '' ? 'All' : $discount_id;

        // $sheet->mergeCells('A'.$rc.':C'.$rc);
        // $sheet->getCell('A'.$rc)->setValue('Promo/Discount: ' . $discount_id);
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        // $rc++;

        $col = 'A';
        foreach ($headers as $txt) {
            $sheet->getCell($col.$rc)->setValue($txt);
            $sheet->getStyle($col.$rc)->applyFromArray($styleHeaderCell);
            $col++;
        }
        $rc++;  

        if($trans_count > 0){
          

                      foreach($trans['free_reason'] as $val){
                            $col = 'A';
                            $sheet->getCell($col.$rc)->setValue(ucwords($val));
                            $sheet->getStyle($col.$rc)->applyFromArray($styleNum);
                             $col = 'B';

                            foreach($trans['branches_arr'] as $i){
                                if(isset($trans['result'][$i][$val])){
                                    if(isset($total[$i])){
                                        $total[$i] += $trans['result'][$i][$val];
                                    }else{
                                        $total[$i] = $trans['result'][$i][$val];
                                    }
                                    $sheet->getCell($col.$rc)->setValue($trans['result'][$i][$val]);
                                    $sheet->getStyle($col.$rc)->applyFromArray($styleNum);
                                }else{
                                    $sheet->getCell($col.$rc)->setValue(0);
                                    $sheet->getStyle($col.$rc)->applyFromArray($styleNum);
                                }

                                $col++;
                            }
                        $rc++;                                 
                     }
                     $col = 'A';
                     $sheet->getCell($col.$rc)->setValue('Total');
                     $sheet->getStyle($col.$rc)->applyFromArray($styleBoldRight);
                     $col = 'B';
                     foreach($trans['branches_arr'] as $i){
                        $sheet->getCell($col.$rc)->setValue($total[$i]);
                        $sheet->getStyle($col.$rc)->applyFromArray($styleBoldRight); 
                         $col++;
                     }

       
                    $counter++;
                    $progress = ($counter / $trans_count) * 100;
                    update_load(num($progress));
          

        }else{
            $sheet->getCell('A'.$rc)->setValue('No records found.');
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        }       
        
        update_load(100);        
       
        if (ob_get_contents())
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


    public function brand_rep()
    {
        $data = $this->syter->spawn('brand_rep');        
        $data['page_title'] = fa('fa-money')." Brand Report";
        $data['code'] = brandRep();
        $data['add_css'] = array('css/morris/morris.css','css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css','assets/global/plugins/select2/css/select2.min.css','assets/global/plugins/select2/css/select2-bootstrap.min.css');
        $data['add_js'] = array('js/plugins/morris/morris.min.js','js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js','assets/global/plugins/select2/js/select2.full.min.js','assets/pages/scripts/components-select2.min.js');
        $data['page_no_padding'] = false;
        $data['sideBarHide'] = false;
        $data['load_js'] = 'dine/reporting';
        $data['use_js'] = 'brandRepJS';
        $this->load->view('page',$data);
    }

    public function brand_sales_rep_gen()
    {
        $this->load->model('dine/setup_model');
        $this->load->model('site/site_model');
        // $this->load->database('main', TRUE);
        $this->menu_model->db = $this->load->database('default', TRUE);
        $this->load->model("dine/menu_model");
        $branch_id = $this->input->post('branch_id');
        $brand = $this->input->post('brand');
        $setup = $this->setup_model->get_details($branch_id);
        $set = $setup[0];
        start_load(0);
        $menu_cat_id = $this->input->post("menu_cat_id");        
        $daterange = $this->input->post("calendar_range");        
        $dates = explode(" to ",$daterange);
        // $from = date2SqlDateTime($dates[0]);
        // $to = date2SqlDateTime($dates[1]);
        // $date = $this->input->post("date");
        $from = date2SqlDateTime($dates[0]. " ".$set->store_open);        
        $to = date2SqlDateTime(date('Y-m-d', strtotime($dates[1] . ' +1 day')). " ".$set->store_open);
        // echo $brand; die();
        $trans = $this->menu_model->get_menu_sales_rep_brand($from, $to, $menu_cat_id,$branch_id,$brand);
        $trans_ret = $this->menu_model->get_menu_sales_rep_retail($from, $to, "",$branch_id);
        $trans_mod = $this->menu_model->get_menu_modifer_sales_rep_brand($from, $to, $menu_cat_id,$branch_id,$brand); 

        // echo "<pre>", print_r($trans), "</pre>"; die();
        $brand_array = array();

        foreach ($trans as $kk) {
            // if(isset($brand_array[$brand])){

            // }

            $brand_array[$kk->brand][$kk->menu_id] = array(
                'menu_name'=>$kk->menu_name,
                'menu_cat_name'=>$kk->menu_cat_name,
                'qty'=>$kk->qty,
                'gross'=>$kk->gross,
                'cost'=>$kk->cost,
                'branch_code'=>$kk->branch_code,
            );
        }


                        
        $trans_count = count($trans) + count($trans_ret) + count($trans_mod);
        $counter = 0;

        foreach ($brand_array as $brnd => $value) {

            $where = array('id'=>$brnd);
            $br = $this->site_model->get_details($where,'brands');
            $brand_name = $br[0]->brand_name;
            // if($brnd == 'max'){
            //     $brand_name = 'MAX';
            // }elseif($brnd == 'yellowcab'){
            //     $brand_name = 'YELLOW CAB';
            // }elseif($brnd == 'krispykreme'){
            //     $brand_name = 'KRISPY KREME';
            // }elseif($brnd == 'jambajuice'){
            //     $brand_name = 'JAMBA JUICE';
            // }elseif($brnd == 'pancakehouse'){
            //     $brand_name = 'PANCAKE HOUSE';
            // }

            $this->make->append('<center><h4>'.$brand_name.'</h4></center>');
            $this->make->sDiv();
                $this->make->sTable(array('class'=>'table reportTBL'));
                    $this->make->sTableHead();
                        $this->make->sRow();
                            if($branch_id == ''){
                                $this->make->th('Branch');
                            }
                            $this->make->th('Menu');
                            $this->make->th('Category');
                            $this->make->th('Qty');
                            // $this->make->th('VAT Sales');
                            // $this->make->th('VAT');
                            $this->make->th('Gross');
                            $this->make->th('Sales (%)');
                            $this->make->th('Cost');
                            $this->make->th('Cost (%)');
                            $this->make->th('Margin');
                        $this->make->eRow();
                    $this->make->eTableHead();
                    $this->make->sTableBody();
                        $tot_qty = 0;
                        $tot_vat_sales = 0;
                        $tot_vat = 0;
                        $tot_gross = 0;
                        $tot_gross1 = 0;
                        $tot_sales_prcnt = 0;
                        $tot_cost = 0;
                        $tot_cost1 = 0;
                        $tot_margin = 0;
                        $tot_margin1 = 0;
                        $tot_cost_prcnt = 0;
                        foreach ($brand_array as $br => $key1) {
                            foreach ($key1 as $menu_ids => $vals) {
                                $tot_gross += $vals['gross'];
                                $tot_cost += $vals['cost'];
                                $tot_margin += $vals['gross'] - $vals['cost'];
                            }
                        }
                        foreach ($value as $men_id => $res) {
                            $this->make->sRow();
                                if($branch_id == ''){
                                    $this->make->td($res['branch_code']);
                                }
                                $this->make->td($res['menu_name']);
                                $this->make->td($res['menu_cat_name']);
                                $this->make->td(num($res['qty']), array("style"=>"text-align:right"));                            
                                // $this->make->td(num($res->vat_sales), array("style"=>"text-align:right"));                            
                                // $this->make->td(num($res->vat), array("style"=>"text-align:right"));                            
                                $this->make->td(num($res['gross']), array("style"=>"text-align:right"));                            
                                if($tot_gross != 0){
                                    $this->make->td(num($res['gross'] / $tot_gross * 100)."%", array("style"=>"text-align:right"));                                
                                }else{
                                    $this->make->td("0.00%", array("style"=>"text-align:right"));                                
                                }
                                $this->make->td(num($res['cost']), array("style"=>"text-align:right"));                            
                                if($tot_cost != 0){
                                    $this->make->td(num($res['cost'] / $tot_cost * 100)."%", array("style"=>"text-align:right"));                            
                                }else{
                                    $this->make->td("0.00%", array("style"=>"text-align:right"));                                
                                }
                                $this->make->td(num($res['gross'] - $res['cost']), array("style"=>"text-align:right")); 

                            $this->make->eRow();

                            /// Grand Total
                            $tot_qty += $res['qty'];
                            // $tot_vat_sales += $res->vat_sales;
                            // $tot_vat += $res->vat;
                            $tot_gross1 += $res['gross'];
                            $tot_sales_prcnt = 0;
                            $tot_cost1 += $res['cost'];
                            $tot_cost_prcnt = 0;
                            $tot_margin1 += $res['gross'] - $res['cost'];

                            $counter++;
                            $progress = ($counter / $trans_count) * 100;
                            update_load(num($progress));

                        }     
                        $this->make->sRow();
                            $this->make->th('Grand Total');
                            if($branch_id == ''){
                                $this->make->th('  ');
                            }                        
                            $this->make->th('  ');
                            $this->make->th(num($tot_qty), array("style"=>"text-align:right"));
                            // $this->make->th(num($tot_vat_sales), array("style"=>"text-align:right"));
                            // $this->make->th(num($tot_vat), array("style"=>"text-align:right"));
                            $this->make->th(num($tot_gross1), array("style"=>"text-align:right"));
                            $this->make->th("", array("style"=>"text-align:right"));
                            $this->make->th(num($tot_cost1), array("style"=>"text-align:right"));
                            $this->make->th("", array("style"=>"text-align:right"));                        
                            $this->make->th(num($tot_margin1), array("style"=>"text-align:right"));                          
                        $this->make->eRow();                                 
                    $this->make->eTableBody();
                $this->make->eTable();

            $this->make->eDiv();

        }

        update_load(100);
        $code = $this->make->code();
        $json['code'] = $code;        
        $json['tbl_vals'] = $trans;
        $json['dates'] = $this->input->post('calendar_range');
        echo json_encode($json);
    }

    public function brand_sales_rep_excel()
    {
         // $this->load->database('main', TRUE);
        $this->menu_model->db = $this->load->database('default', TRUE);
        $this->load->model("dine/menu_model");
        date_default_timezone_set('Asia/Manila');
        $this->load->library('Excel');
        $sheet = $this->excel->getActiveSheet();
        $filename = 'Brand Report';
        $rc=1;
        #GET VALUES
        start_load(0);
            // $post = $this->set_post($_GET['calendar_range']);       

        update_load(10);
        sleep(1);
        
        $menu_cat_id = $_GET['menu_cat_id'];        
        $daterange = $_GET['calendar_range'];
        $branch_id = $_GET['branch_id'];        
        $brand = $_GET['brand'];        
        $dates = explode(" to ",$daterange);

        $setup = $this->setup_model->get_details($branch_id);
        $set = $setup[0];
        // $from = date2SqlDateTime($dates[0]);
        // $to = date2SqlDateTime($dates[1]);
        $from = date2SqlDateTime($dates[0]. " ".$set->store_open);        
        $to = date2SqlDateTime(date('Y-m-d', strtotime($dates[1] . ' +1 day')). " ".$set->store_open);
        $trans = $this->menu_model->get_menu_sales_rep_brand($from, $to, $menu_cat_id, $branch_id, $brand);
        $trans_ret = $this->menu_model->get_menu_sales_rep_retail($from, $to, "",$branch_id); 
        $trans_mod = $this->menu_model->get_menu_modifer_sales_rep_brand($from, $to, $menu_cat_id, $branch_id,$brand);
        // $trans_payment = $this->menu_model->get_payment_date($from, $to); 
        // $trans_menu_mod = $this->menu_model->get_menu_modifer_sales_rep($from, $to, $menu_cat_id,$branch_id);  


        // echo "<pre>", print_r($trans), "</pre>"; die();
        $brand_array = array();

        foreach ($trans as $kk) {
            // if(isset($brand_array[$brand])){

            // }
            $brand_array[$kk->brand][$kk->menu_id] = array(
                'menu_name'=>$kk->menu_name,
                'menu_cat_name'=>$kk->menu_cat_name,
                'qty'=>$kk->qty,
                'gross'=>$kk->gross,
                'cost'=>$kk->cost,
                'branch_code'=>$kk->branch_code,
            );
        }



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
        if($branch_id == ""){
            $headers = array('Menu','Category', 'Qty','Gross','Sales (%)','Cost','Cost (%)', 'Margin','Branch');
        
            $sheet->getColumnDimension('A')->setWidth(20);
            $sheet->getColumnDimension('B')->setWidth(20);
            $sheet->getColumnDimension('C')->setWidth(20);
            $sheet->getColumnDimension('D')->setWidth(20);
            $sheet->getColumnDimension('E')->setWidth(20);
            $sheet->getColumnDimension('F')->setWidth(20);
            $sheet->getColumnDimension('G')->setWidth(20);
            $sheet->getColumnDimension('H')->setWidth(20);
            $sheet->getColumnDimension('I')->setWidth(20);
        }else{
            $headers = array('Menu','Category', 'Qty','Gross','Sales (%)','Cost','Cost (%)', 'Margin');
        
            $sheet->getColumnDimension('A')->setWidth(20);
            $sheet->getColumnDimension('B')->setWidth(20);
            $sheet->getColumnDimension('C')->setWidth(20);
            $sheet->getColumnDimension('D')->setWidth(20);
            $sheet->getColumnDimension('E')->setWidth(20);
            $sheet->getColumnDimension('F')->setWidth(20);
            $sheet->getColumnDimension('G')->setWidth(20);
            $sheet->getColumnDimension('H')->setWidth(20);
        }


        $sheet->mergeCells('A'.$rc.':H'.$rc);
        $sheet->getCell('A'.$rc)->setValue($set->branch_name);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        $rc++;

        $sheet->mergeCells('A'.$rc.':H'.$rc);
        $sheet->getCell('A'.$rc)->setValue($set->address);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        $rc++;

        $brd = $this->setup_model->get_brands($brand);
        if($brd){
            $sheet->mergeCells('A'.$rc.':H'.$rc);
            $sheet->getCell('A'.$rc)->setValue($brd[0]->brand_name);
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $rc++;
        }

        $sheet->mergeCells('A'.$rc.':H'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Brand Report');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $rc++;

        $sheet->mergeCells('A'.$rc.':E'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Report Period: '.$daterange);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->mergeCells('F'.$rc.':H'.$rc);
        $sheet->getCell('F'.$rc)->setValue('Report Generated: '.(new \DateTime())->format('Y-m-d H:i:s'));
        $sheet->getStyle('F'.$rc)->applyFromArray($styleNum);
        $rc++;

        $sheet->mergeCells('A'.$rc.':E'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Transaction Time:');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $user = $this->session->userdata('user');
        $sheet->mergeCells('F'.$rc.':H'.$rc);
        $sheet->getCell('F'.$rc)->setValue('Generated by:    '.$user["full_name"]);
        $sheet->getStyle('F'.$rc)->applyFromArray($styleNum);
        $rc++;
        $rc++;



        foreach ($brand_array as $brnd => $value) {
            $brand_name = '';
            if($brnd == 'max'){
                $brand_name = 'MAX';
            }elseif($brnd == 'yellowcab'){
                $brand_name = 'YELLOW CAB';
            }elseif($brnd == 'krispykreme'){
                $brand_name = 'KRISPY KREME';
            }elseif($brnd == 'jambajuice'){
                $brand_name = 'JAMBA JUICE';
            }elseif($brnd == 'pancakehouse'){
                $brand_name = 'PANCAKE HOUSE';
            }

            $col = 'A';
            $sheet->mergeCells('A'.$rc.':G'.$rc);
            $sheet->getCell('A'.$rc)->setValue($brand_name);
            $sheet->getStyle('A'.$rc)->applyFromArray($styleCenter);
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
            $tot_vat_sales = 0;
            $tot_vat = 0;
            $tot_gross = 0;
            $tot_gross1 = 0;
            $tot_mod_gross = 0;
            $tot_sales_prcnt = 0;
            $tot_cost = 0;
            $tot_cost1 = 0;
            $tot_cost_prcnt = 0; 
            $tot_margin = 0;
            $tot_margin1 = 0;
            $counter = 0;
            $progress = 0;
            $trans_count = count($trans) + 10;
            foreach ($brand_array as $br => $key1) {
                foreach ($key1 as $menu_ids => $vals) {
                    $tot_gross += $vals['gross'];
                    $tot_cost += $vals['cost'];
                    $tot_margin += $vals['gross'] - $vals['cost'];
                }
            }
            // foreach ($trans_mod as $vv) {
            //     $tot_mod_gross += $vv->mod_gross;
            // }

            foreach ($value as $men_id => $res) {

                $sheet->getCell('A'.$rc)->setValue($res['menu_name']);
                $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
                $sheet->getCell('B'.$rc)->setValue($res['menu_cat_name']);
                $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
                $sheet->getStyle('C'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                $sheet->getCell('C'.$rc)->setValue(num($res['qty']));
                // $sheet->getCell('D'.$rc)->setValue(num($v->vat_sales));     
                // $sheet->getStyle('D'.$rc)->applyFromArray($styleNum);
                // $sheet->getCell('E'.$rc)->setValue(num($v->vat));     
                // $sheet->getStyle('E'.$rc)->applyFromArray($styleNum);
                $sheet->getStyle('D'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                $sheet->getCell('D'.$rc)->setValue($res['gross']);     
                $sheet->getStyle('E'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                if($tot_gross != 0){
                    $sheet->getCell('E'.$rc)->setValue(num($res['gross'] / $tot_gross * 100)."%");                     
                }else{
                    $sheet->getCell('E'.$rc)->setValue("0.00%");                                     
                }
                $sheet->getStyle('F'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                $sheet->getCell('F'.$rc)->setValue($res['cost']);     
                $sheet->getStyle('G'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                if($tot_cost != 0){
                    $sheet->getCell('G'.$rc)->setValue(num($res['cost'] / $tot_cost * 100)."%");     
                }else{
                    $sheet->getCell('G'.$rc)->setValue("0.00%");                     
                }
                $sheet->getStyle('H'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                $sheet->getCell('H'.$rc)->setValue($res['gross'] - $res['cost']);     
                if($branch_id == ""){
                    $sheet->getStyle('I'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $sheet->getCell('I'.$rc)->setValue($res['branch_code']);     
                }               

                // Grand Total
                $tot_qty += $res['qty'];
                // $tot_vat_sales += $v->vat_sales;
                // $tot_vat += $v->vat;
                $tot_gross1 += $res['gross'];
                $tot_sales_prcnt = 0;
                $tot_cost1 += $res['cost'];
                $tot_cost_prcnt = 0;
                $tot_margin1 += $res['gross'] - $res['cost'];

                $counter++;
                $progress = ($counter / $trans_count) * 100;
                update_load(num($progress)); 
                $rc++;            

            }
            $sheet->getCell('A'.$rc)->setValue('Grand Total');
            $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
            $sheet->getCell('B'.$rc)->setValue("");
            $sheet->getStyle('B'.$rc)->applyFromArray($styleBoldLeft);
            $sheet->getStyle('C'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('C'.$rc)->setValue($tot_qty);
            // $sheet->getCell('D'.$rc)->setValue(num($tot_vat_sales));     
            // $sheet->getStyle('D'.$rc)->applyFromArray($styleBoldRight);
            // $sheet->getCell('E'.$rc)->setValue(num($tot_vat));     
            // $sheet->getStyle('E'.$rc)->applyFromArray($styleBoldRight);
            $sheet->getStyle('D'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('D'.$rc)->setValue($tot_gross1);     
            $sheet->getCell('E'.$rc)->setValue();     
            $sheet->getStyle('E'.$rc)->applyFromArray($styleBoldRight);
            $sheet->getStyle('F'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('F'.$rc)->setValue($tot_cost1);     
            $sheet->getCell('G'.$rc)->setValue();     
            $sheet->getStyle('G'.$rc)->applyFromArray($styleBoldRight);
            $sheet->getStyle('H'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('H'.$rc)->setValue($tot_margin1);     
            $rc++; 


        }  

        update_load(100);        

        if (ob_get_contents())
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

    public function brand_sales_rep_pdf()
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
        $pdf->SetTitle('Sales Report');
        $pdf->SetSubject('');
        $pdf->SetKeywords('');

        // set default header data
        
        $branch_id = $_GET['branch_id'];        
        $brand = $_GET['brand'];        
        $setup = $this->setup_model->get_details($branch_id);
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

        // $this->load->database('main', TRUE);
        $this->menu_model->db = $this->load->database('default', TRUE);
        $this->load->model("dine/menu_model");
        // start_load(0);

        // set font
        $pdf->SetFont('helvetica', 'B', 11);

        // add a page
        $pdf->AddPage();
        
        $menu_cat_id = $_GET['menu_cat_id'];        
        $daterange = $_GET['calendar_range'];        
        $dates = explode(" to ",$daterange);

        // $from = date2SqlDateTime($dates[0]);
        // $to = date2SqlDateTime($dates[1]);
        $from = date2SqlDateTime($dates[0]. " ".$set->store_open);        
        $to = date2SqlDateTime(date('Y-m-d', strtotime($dates[1] . ' +1 day')). " ".$set->store_open);
        // $trans = $this->menu_model->get_menu_sales_rep($from, $to, $menu_cat_id, $branch_id);
        // $trans_mod = $this->menu_model->get_menu_modifer_sales_rep($from, $to, $menu_cat_id,$branch_id);
        // $trans_payment = $this->menu_model->get_payment_date($from, $to);  

        $trans = $this->menu_model->get_menu_sales_rep_brand($from, $to, $menu_cat_id, $branch_id, $brand);
        $trans_ret = $this->menu_model->get_menu_sales_rep_retail($from, $to, "",$branch_id); 
        $trans_mod = $this->menu_model->get_menu_modifer_sales_rep_brand($from, $to, $menu_cat_id, $branch_id,$brand);
        // $trans_payment = $this->menu_model->get_payment_date($from, $to); 
        // $trans_menu_mod = $this->menu_model->get_menu_modifer_sales_rep($from, $to, $menu_cat_id,$branch_id);  


        // echo "<pre>", print_r($trans), "</pre>"; die();
        $brand_array = array();

        foreach ($trans as $kk) {
            // if(isset($brand_array[$brand])){

            // }
            $brand_array[$kk->brand][$kk->menu_id] = array(
                'menu_name'=>$kk->menu_name,
                'menu_cat_name'=>$kk->menu_cat_name,
                'qty'=>$kk->qty,
                'gross'=>$kk->gross,
                'cost'=>$kk->cost,
                'branch_code'=>$kk->branch_code,
            );
        }

        $brd = $this->setup_model->get_brands($brand);
        if($brd){
            $pdf->Write(0, $brd[0]->brand_name, '', 0, 'L', true, 0, false, false, 0);
            $pdf->ln(0.9);
        }

        $pdf->Write(0, 'Brand Report', '', 0, 'L', true, 0, false, false, 0);
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

        // echo "<pre>", print_r($trans), "</pre>";die();


        foreach ($brand_array as $brnd => $value) {
            $brand_name = '';
            if($brnd == 'max'){
                $brand_name = 'MAX';
            }elseif($brnd == 'yellowcab'){
                $brand_name = 'YELLOW CAB';
            }elseif($brnd == 'krispykreme'){
                $brand_name = 'KRISPY KREME';
            }elseif($brnd == 'jambajuice'){
                $brand_name = 'JAMBA JUICE';
            }elseif($brnd == 'pancakehouse'){
                $brand_name = 'PANCAKE HOUSE';
            }

            $pdf->Cell(55, 0, $brand_name, '', 0, 'L');        
            // $pdf->Cell(38, 0, "", 'T', 0, 'L');        
            // $pdf->Cell(25, 0, num($tot_qty), 'T', 0, 'R');        
            // // $pdf->Cell(25, 0, num($tot_vat_sales), 'T', 0, 'R');        
            // // $pdf->Cell(25, 0, num($tot_vat), 'T', 0, 'R');        
            // $pdf->Cell(25, 0, num($tot_gross), 'T', 0, 'R');        
            // $pdf->Cell(25, 0, "", 'T', 0, 'R');        
            // $pdf->Cell(25, 0, num($tot_cost), 'T', 0, 'R');        
            // $pdf->Cell(25, 0, "", 'T', 0, 'R');        
            // $pdf->Cell(50, 0, num($tot_margin), 'T', 0, 'R');
            $pdf->ln();   

            $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => 'black'));
            if(!$branch_id){
                 $pdf->Cell(30, 0, 'Branch', 'B', 0, 'L');        
                 $pdf->Cell(45, 0, 'Menu', 'B', 0, 'L');  
                 $pdf->Cell(18, 0, 'Category', 'B', 0, 'L');        
            }else{
                 $pdf->Cell(55, 0, 'Menu', 'B', 0, 'L'); 
                 $pdf->Cell(38, 0, 'Category', 'B', 0, 'L');        
            }
                  
            $pdf->Cell(25, 0, 'Qty', 'B', 0, 'C');        
            // $pdf->Cell(25, 0, 'VAT Sales', 'B', 0, 'C');        
            // $pdf->Cell(25, 0, 'VAT', 'B', 0, 'C');        
            $pdf->Cell(25, 0, 'Gross', 'B', 0, 'C');        
            $pdf->Cell(25, 0, 'Sales (%)', 'B', 0, 'C');        
            $pdf->Cell(25, 0, 'Cost', 'B', 0, 'C');        
            $pdf->Cell(25, 0, 'Cost (%)', 'B', 0, 'C'); 
             $pdf->Cell(50, 0, 'Margin', 'B', 0, 'R');        
            $pdf->ln();                  

            // GRAND TOTAL VARIABLES
            $tot_qty = 0;
            $tot_vat_sales = 0;
            $tot_vat = 0;
            $tot_gross = 0;
            $tot_gross1 = 0;
            $tot_mod_gross = 0;
            $tot_sales_prcnt = 0;
            $tot_cost = 0;
            $tot_cost1 = 0;
            $tot_cost_prcnt = 0; 
            $tot_margin = 0; 
            $tot_margin1 = 0; 
            $counter = 0;
            $progress = 0;
            $trans_count = count($trans);
            foreach ($brand_array as $br => $key1) {
                foreach ($key1 as $menu_ids => $vals) {
                    $tot_gross += $vals['gross'];
                    $tot_cost += $vals['cost'];
                    $tot_margin += $vals['gross'] - $vals['cost'];
                }
            }

            foreach ($value as $men_id => $v) {
                if(!$branch_id){
                    $pdf->Cell(30, 0, $v['branch_code'], '', 0, 'L');        
                    $pdf->Cell(45, 0, $v['menu_name'], '', 0, 'L');        
                    $pdf->Cell(18, 0, $v['menu_cat_name'], '', 0, 'L');        

                }else{
                    $pdf->Cell(55, 0, $v['menu_name'], '', 0, 'L');        
                    $pdf->Cell(38, 0, $v['menu_cat_name'], '', 0, 'L'); 
                }
                $pdf->Cell(25, 0, num($v['qty']), '', 0, 'R');        
                $pdf->Cell(25, 0, num($v['gross']), '', 0, 'R');        
                if($tot_gross != 0){
                    $pdf->Cell(25, 0, num($v['gross'] / $tot_gross * 100)."%", '', 0, 'R');                        
                }else{
                    $pdf->Cell(25, 0, "0.00%", '', 0, 'R');                                        
                }
                $pdf->Cell(25, 0, num($v['cost']), '', 0, 'R');        
                if($tot_cost != 0){
                    $pdf->Cell(25, 0, num($v['cost'] / $tot_cost * 100)."%", '', 0, 'R');                    
                }else{
                    $pdf->Cell(25, 0, "0.00%", '', 0, 'R');                                    
                }
                $pdf->Cell(50, 0, num($v['gross'] - $v['cost']), '', 0, 'R');                    
                $pdf->ln();                

                // Grand Total
                $tot_qty += $v['qty'];
                // $tot_vat_sales += $v->vat_sales;
                // $tot_vat += $v->vat;
                $tot_gross1 += $v['gross'];
                $tot_sales_prcnt = 0;
                $tot_cost1 += $v['cost'];
                $tot_cost_prcnt = 0;
                $tot_margin1 += $v['gross'] - $v['cost'];

                $counter++;
                $progress = ($counter / $trans_count) * 100;
                // update_load(num($progress));              
            }

            $pdf->Cell(55, 0, "Grand Total", 'T', 0, 'L');        
            $pdf->Cell(38, 0, "", 'T', 0, 'L');        
            $pdf->Cell(25, 0, num($tot_qty), 'T', 0, 'R');        
            // $pdf->Cell(25, 0, num($tot_vat_sales), 'T', 0, 'R');        
            // $pdf->Cell(25, 0, num($tot_vat), 'T', 0, 'R');        
            $pdf->Cell(25, 0, num($tot_gross1), 'T', 0, 'R');        
            $pdf->Cell(25, 0, "", 'T', 0, 'R');        
            $pdf->Cell(25, 0, num($tot_cost1), 'T', 0, 'R');        
            $pdf->Cell(25, 0, "", 'T', 0, 'R');        
            $pdf->Cell(50, 0, num($tot_margin1), 'T', 0, 'R');
            $pdf->ln();   
            $pdf->ln();   


        }

        // -----------------------------------------------------------------------------
        // $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => 'black'));
        // if(!$branch_id){
        //      $pdf->Cell(30, 0, 'Branch', 'B', 0, 'L');        
        //      $pdf->Cell(45, 0, 'Menu', 'B', 0, 'L');  
        //      $pdf->Cell(18, 0, 'Category', 'B', 0, 'L');        
        // }else{
        //      $pdf->Cell(55, 0, 'Menu', 'B', 0, 'L'); 
        //      $pdf->Cell(38, 0, 'Category', 'B', 0, 'L');        
        // }
              
        // $pdf->Cell(25, 0, 'Qty', 'B', 0, 'C');        
        // // $pdf->Cell(25, 0, 'VAT Sales', 'B', 0, 'C');        
        // // $pdf->Cell(25, 0, 'VAT', 'B', 0, 'C');        
        // $pdf->Cell(25, 0, 'Gross', 'B', 0, 'C');        
        // $pdf->Cell(25, 0, 'Sales (%)', 'B', 0, 'C');        
        // $pdf->Cell(25, 0, 'Cost', 'B', 0, 'C');        
        // $pdf->Cell(25, 0, 'Cost (%)', 'B', 0, 'C'); 
        //  $pdf->Cell(50, 0, 'Margin', 'B', 0, 'R');        
        // $pdf->ln();                  

        // // GRAND TOTAL VARIABLES
        // $tot_qty = 0;
        // $tot_vat_sales = 0;
        // $tot_vat = 0;
        // $tot_gross = 0;
        // $tot_mod_gross = 0;
        // $tot_sales_prcnt = 0;
        // $tot_cost = 0;
        // $tot_cost_prcnt = 0; 
        // $tot_margin = 0; 
        // $counter = 0;
        // $progress = 0;
        // $trans_count = count($trans);
        // foreach ($trans as $val) {
        //     $tot_gross += $val->gross;
        //     $tot_cost += $val->cost;
        //     $tot_margin += $val->gross - $val->cost;
        // }

        // foreach ($trans as $k => $v) {
        //     if(!$branch_id){
        //         $pdf->Cell(30, 0, $v->branch_code, '', 0, 'L');        
        //         $pdf->Cell(45, 0, $v->menu_name, '', 0, 'L');        
        //         $pdf->Cell(18, 0, $v->menu_cat_name, '', 0, 'L');        

        //     }else{
        //         $pdf->Cell(55, 0, $v->menu_name, '', 0, 'L');        
        //         $pdf->Cell(38, 0, $v->menu_cat_name, '', 0, 'L'); 
        //     }
        //     $pdf->Cell(25, 0, num($v->qty), '', 0, 'R');        
        //     $pdf->Cell(25, 0, num($v->gross), '', 0, 'R');        
        //     if($tot_gross != 0){
        //         $pdf->Cell(25, 0, num($v->gross / $tot_gross * 100)."%", '', 0, 'R');                        
        //     }else{
        //         $pdf->Cell(25, 0, "0.00%", '', 0, 'R');                                        
        //     }
        //     $pdf->Cell(25, 0, num($v->cost), '', 0, 'R');        
        //     if($tot_cost != 0){
        //         $pdf->Cell(25, 0, num($v->cost / $tot_cost * 100)."%", '', 0, 'R');                    
        //     }else{
        //         $pdf->Cell(25, 0, "0.00%", '', 0, 'R');                                    
        //     }
        //     $pdf->Cell(50, 0, num($v->gross - $v->cost), '', 0, 'R');                    
        //     $pdf->ln();                

        //     // Grand Total
        //     $tot_qty += $v->qty;
        //     // $tot_vat_sales += $v->vat_sales;
        //     // $tot_vat += $v->vat;
        //     // $tot_gross += $v->gross;
        //     $tot_sales_prcnt = 0;
        //     // $tot_cost += $v->cost;
        //     $tot_cost_prcnt = 0;

        //     $counter++;
        //     $progress = ($counter / $trans_count) * 100;
        //     update_load(num($progress));              
        // }
        // update_load(100);        
        // $pdf->Cell(55, 0, "Grand Total", 'T', 0, 'L');        
        // $pdf->Cell(38, 0, "", 'T', 0, 'L');        
        // $pdf->Cell(25, 0, num($tot_qty), 'T', 0, 'R');        
        // // $pdf->Cell(25, 0, num($tot_vat_sales), 'T', 0, 'R');        
        // // $pdf->Cell(25, 0, num($tot_vat), 'T', 0, 'R');        
        // $pdf->Cell(25, 0, num($tot_gross), 'T', 0, 'R');        
        // $pdf->Cell(25, 0, "", 'T', 0, 'R');        
        // $pdf->Cell(25, 0, num($tot_cost), 'T', 0, 'R');        
        // $pdf->Cell(25, 0, "", 'T', 0, 'R');        
        // $pdf->Cell(50, 0, num($tot_margin), 'T', 0, 'R');
        // $pdf->ln();   

        
        
        // $pdf->writeHTML($tbl, true, false, false, false, '');

        // -----------------------------------------------------------------------------

        //Close and output PDF document
        $pdf->Output('brand_report.pdf', 'I');

        //============================================================+
        // END OF FILE
        //============================================================+   
    }

    public function gc_rep(){
        $data = $this->syter->spawn('gc_sales_rep');
        $data['page_title'] = fa('icon-book-open')." Gift Cheque Sales Report";
        $data['code'] = gcRep();
        $data['add_css'] = array('css/morris/morris.css','css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css');
        $data['add_js'] = array('js/plugins/morris/morris.min.js','js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js');
        $data['page_no_padding'] = true;
        $data['sideBarHide'] = true;
        $data['load_js'] = 'dine/reporting';
        $data['use_js'] = 'gcRepJS';
        $this->load->view('page',$data);
    }

    public function gc_rep_gen()
    {
        $this->load->model('dine/setup_model');
        // $this->load->database('main', TRUE);
        $this->load->model("dine/gift_cards_model");
        $menu_cat_id = null;
        $branch_id = $this->input->post('branch_id');
        $brand = $this->input->post('brand');      
        
        $setup = $this->setup_model->get_details($branch_id);
        $set = $setup[0];
        start_load(0);
        // echo $branch_id;die();
        $daterange = $this->input->post("calendar_range");        
        // $daterange = "05/30/2019 to 05/30/2019";        
        $dates = explode(" to ",$daterange);
        // $from = date2SqlDateTime($dates[0]);
        // $to = date2SqlDateTime($dates[1]);
        // $date = $this->input->post("date");
        // echo "<pre>",print_r($raw_data),"</pre>";die();
        $this->menu_model->db = $this->load->database('default', TRUE);
        $from = date2SqlDateTime($dates[0]. " ".$set->store_open);        
        $to = date2SqlDateTime(date('Y-m-d', strtotime($dates[1] . ' +1 day')). " ".$set->store_open);
    
        // echo "asdf";die(); 
        $trans_ret = $this->gift_cards_model->get_gift_cards_rep_retail($from, $to,  $branch_id,$brand);
        // echo $this->db->last_query(); die();             
        // echo "<pre>", print_r($trans_mod), "</pre>"; die();
        $trans_count = count($trans_ret);
        // $trans_count_ret = count($trans_ret);
        $counter = 0;
    
        $brand_txt = '';

        $brd = $this->setup_model->get_brands($brand);
        if($brd){
            $brand_txt = '/' . $brd[0]->brand_name;
        }

        $this->make->sDiv();
            $this->make->sTable(array("id"=>"main-tbl", 'class'=>'table reportTBL sortable'));
                $this->make->sTableHead();
                    $this->make->sRow();
                        if($branch_id == ''){
                            $this->make->th('Branch');
                        }
                        $this->make->th('Acknowlegement Receipt');
                        $this->make->th('GC Description');
                        $this->make->th('GC From');
                        $this->make->th('GC To');
                        $this->make->th('Qty');
                        $this->make->th('Amount');
                        $this->make->th('Total');
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
                    $tot_cost_prcnt = 0;
                    // foreach ($trans_ret as $v) {
                    //     $tot_gross += $v->gross;
                        // $tot_cost += $v->cost;
                    // }
                    foreach ($trans_ret as $res) {
                         if($counter % 500 == 0){
                            set_time_limit(60);  
                        }

                        $tot_gross += $res->gross;

                        $this->make->sRow();
                            if($branch_id == ''){
                                $this->make->td($res->branch_name.$brand_txt);
                            }
                            $this->make->td($res->ref);
                            $this->make->td($res->description_id);
                            $this->make->td($res->gc_from);
                            $this->make->td($res->gc_to);
                            $this->make->td(num($res->qty), array("style"=>"text-align:right"));                               
                            $this->make->td(num($res->price), array("style"=>"text-align:right"));                            
                            $this->make->td(num($res->gross), array("style"=>"text-align:right"));     
                        $this->make->eRow();

                         // Grand Total
                        $tot_qty += $res->qty;

                        $counter++;
                        $progress = ($counter / $trans_count) * 100;
                        update_load(num($progress));

                    }    
                    $this->make->sRow();
                        $this->make->th('Grand Total');
                        if($branch_id == ''){
                            $this->make->th('  ');
                        }
                        $this->make->th('  ');
                        $this->make->th('  ');
                        $this->make->th('  ');
                        $this->make->th(num($tot_qty), array("style"=>"text-align:right"));
                        $this->make->th("", array("style"=>"text-align:right"));
                        $this->make->th(num($tot_gross), array("style"=>"text-align:right"));                     
                    $this->make->eRow();                                 
                $this->make->eTableBody();
            $this->make->eTable();
        $this->make->eDiv();

        header_remove('Set-Cookie');
        update_load(100);
        $code = $this->make->code();
        $json['code'] = $code;        
        $json['tbl_vals'] = $trans_ret;
        $json['dates'] = $this->input->post('calendar_range');
        echo json_encode($json);
    }

    public function gc_rep_pdf()
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
        $pdf->SetTitle('Gift Cheque Sales Report');
        $pdf->SetSubject('');
        $pdf->SetKeywords('');

        // set default header data
        $branch_id = $_GET['branch_id'];
        $setup = $this->setup_model->get_details($branch_id);
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
        $this->menu_model->db = $this->load->database('default', TRUE);
        $this->load->model("dine/gift_cards_model");
        start_load(0);

        // set font
        $pdf->SetFont('helvetica', 'B', 11);

        // add a page
        $pdf->AddPage();
                
        $branch_id = $_GET['branch_id'];
        $brand = $_GET['brand'];
        $daterange = $_GET['calendar_range'];        
        $dates = explode(" to ",$daterange);
        // $from = date2SqlDateTime($dates[0]);
        // $to = date2SqlDateTime($dates[1]);
        $from = date2SqlDateTime($dates[0]. " ".$set->store_open);        
        $to = date2SqlDateTime(date('Y-m-d', strtotime($dates[1] . ' +1 day')). " ".$set->store_open);
       


        $pdf->Write(0, 'Gift Cheque Sales Report', '', 0, 'L', true, 0, false, false, 0);
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

        // echo "<pre>", print_r($trans), "</pre>";die();

        // -----------------------------------------------------------------------------
        $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => 'black'));
        
        $pdf->cell(23, 0, 'Branch', 'B', 0, 'L'); 
        $pdf->Cell(40, 0, 'Acknowlegement Receipt', 'B', 0, 'L');   
        $pdf->Cell(32, 0, 'GC Description', 'B', 0, 'L');
        $pdf->Cell(32, 0, 'GC From', 'B', 0, 'L');
        $pdf->Cell(32, 0, 'GC To', 'B', 0, 'L');        
        $pdf->Cell(32, 0, 'Qty', 'B', 0, 'R');        
        $pdf->Cell(32, 0, 'Amount', 'B', 0, 'R');        
        $pdf->Cell(41, 0, 'Total', 'B', 0, 'R');          
        $pdf->ln();             

        $trans = $this->gift_cards_model->get_gift_cards_rep_retail($from,$to,$branch_id,$brand);  

        // GRAND TOTAL VARIABLES
        $tot_qty = 0;
        $tot_vat_sales = 0;
        $tot_vat = 0;
        $tot_gross = 0;
        $tot_sales_prcnt = 0;
        $tot_cost = 0;
        $tot_cost_prcnt = 0; 
        $tot_margin = 0;
        $counter = 0;
        $progress = 0;
        $trans_count = count($trans);

        $brd = $this->setup_model->get_brands($brand);
        if($brd){
            $brand_txt = '/' . $brd[0]->brand_name;
        }
       
        foreach ($trans as $k => $v) {
            $tot_gross += $v->gross;
             $pdf->cell(23,0,$v->branch_name.$brand_txt,0,0,'L',0, '', 1);  
            
            $pdf->cell(40,0,$v->ref,0,0,'L',0, '', 1);
            $pdf->cell(32,0,$v->description_id,0,0,'L',0, '', 1);
            $pdf->cell(32,0,$v->gc_from,0,0,'L',0, '', 1);
            $pdf->cell(32,0,$v->gc_to,0,0,'L',0, '', 1);
            $pdf->Cell(32, 0, num($v->qty), '', 0, 'R');        
            $pdf->Cell(32, 0, num($v->price), '', 0, 'R');          
            $pdf->Cell(41, 0, num($v->gross), '', 0, 'R');            
              
            $pdf->ln();                

            // Grand Total
            $tot_qty += $v->qty;
         

            $counter++;
            $progress = ($counter / $trans_count) * 100;
            update_load(num($progress));              
        }
    
        $pdf->Cell(23, 0, "Grand Total", 'T', 0, 'L');
        $pdf->Cell(40, 0, "", 'T', 0, 'R');
        $pdf->Cell(32, 0, "", 'T', 0, 'R'); 
        $pdf->Cell(32, 0, "", 'T', 0, 'R'); 
        $pdf->Cell(32, 0, "", 'T', 0, 'R'); 
                      
        $pdf->Cell(32, 0, num($tot_qty), 'T', 0, 'R'); 
        $pdf->Cell(32, 0, "", 'T', 0, 'R');         
        $pdf->Cell(41, 0, num($tot_gross), 'T', 0, 'R');        
        
        update_load(100);

        //Close and output PDF document
        $pdf->Output('gift_check_sales_report.pdf', 'I');

        //============================================================+
        // END OF FILE
        //============================================================+   
    }

    public function gc_rep_excel()
    {
        $this->menu_model->db = $this->load->database('default', TRUE);
        $this->load->model("dine/gift_cards_model");
        date_default_timezone_set('Asia/Manila');
        $this->load->library('Excel');
        $sheet = $this->excel->getActiveSheet();
        $filename = 'Gift Cheque Sales Report';
        $rc=1;
        #GET VALUES
        start_load(0);
            // $post = $this->set_post($_GET['calendar_range']);
        

        update_load(10);
        sleep(1);
               
        $daterange = $_GET['calendar_range'];        
        $dates = explode(" to ",$daterange);
        // $from = date2SqlDateTime($dates[0]);
        // $to = date2SqlDateTime($dates[1]);
        
        // $branch_id = $this->input->post('branch_id');
        $branch_id = $_GET['branch_id'];
        $brand = $_GET['brand'];    
          
        
        $setup = $this->setup_model->get_details($branch_id);
        if(isset($setup[0]))
        {
            $set = $setup[0];            
            $store_open = $set->store_open;
        }else{
            $store_open = "00:00:00";
        }

        $from = date2SqlDateTime($dates[0]. " ".$store_open);        
        $to = date2SqlDateTime(date('Y-m-d', strtotime($dates[1] . ' +1 day')). " ".$store_open);

        $trans = $this->gift_cards_model->get_gift_cards_rep_retail($from, $to, $branch_id,$brand); 

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
        // if($branch_id == ""){
            $headers = array('Branch Code', 'GC Description','GC From','GC To','Qty','Amount','Total');
        // }
        // else{
        //     $headers = array('GC Description','Qty','Amount','Total');
        // }
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        // $sheet->getColumnDimension('H')->setWidth(20);

        $sheet->mergeCells('A'.$rc.':H'.$rc);
        $sheet->getCell('A'.$rc)->setValue($set->address);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        $rc++;

        $sheet->mergeCells('A'.$rc.':H'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Gift Cheque Sales Report');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $rc++;

        $sheet->mergeCells('A'.$rc.':D'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Report Period: '.$daterange);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->mergeCells('E'.$rc.':H'.$rc);
        $sheet->getCell('E'.$rc)->setValue('Report Generated: '.(new \DateTime())->format('Y-m-d H:i:s'));
        $sheet->getStyle('E'.$rc)->applyFromArray($styleNum);
        $rc++;

        $sheet->mergeCells('A'.$rc.':D'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Transaction Time:');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $user = $this->session->userdata('user');
        $sheet->mergeCells('E'.$rc.':H'.$rc);
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
        $tot_vat_sales = 0;
        $tot_vat = 0;
        $tot_gross = 0;
        $tot_mod_gross = 0;
        $tot_sales_prcnt = 0;
        $tot_cost = 0;
        $tot_cost_prcnt = 0; 
        $tot_margin = 0;
        $counter = 0;
        $progress = 0;
        $trans_count = count($trans);
        
        // foreach ($trans_mod as $vv) {
        //     $tot_mod_gross += $vv->mod_gross;
        // }

        $brd = $this->setup_model->get_brands($brand);
        if($brd){
            $brand_txt = '/' . $brd[0]->brand_name;
        }

        foreach ($trans as $k => $v) {
            $tot_gross += $v->gross;
            $sheet->getCell('A'.$rc)->setValue($v->branch_name.$brand_txt);
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            
            
            $sheet->getCell('B'.$rc)->setValue($v->description_id);
            $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);

            $sheet->getCell('C'.$rc)->setValue($v->gc_from);
            $sheet->getStyle('C'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('D'.$rc)->setValue($v->gc_to);
            $sheet->getStyle('D'.$rc)->applyFromArray($styleTxt);
            // $sheet->getCell('C'.$rc)->setValue(num($v->vat_sales));
            // $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
            // $sheet->getCell('D'.$rc)->setValue(num($v->vat));     
            // $sheet->getStyle('D'.$rc)->applyFromArray($styleNum);
            $sheet->getCell('E'.$rc)->setValue(num($v->qty));     
            $sheet->getStyle('E'.$rc)->applyFromArray($styleNum);
            $sheet->getCell('F'.$rc)->setValue(num($v->price));     
            $sheet->getStyle('F'.$rc)->applyFromArray($styleNum);

            $sheet->getCell('G'.$rc)->setValue(num($v->gross)); 
            
            $sheet->getStyle('G'.$rc)->applyFromArray($styleNum);
            
            $tot_qty += $v->qty;
            // $tot_vat_sales += $v->vat_sales;
            // $tot_vat += $v->vat;
            // $tot_gross += $v->gross;
            $tot_sales_prcnt = 0;
            // $tot_cost += $v->cost;
            $tot_cost_prcnt = 0;

            $counter++;
            $progress = ($counter / $trans_count) * 100;
            update_load(num($progress));   
            $rc++;           
        }

        $sheet->getCell('A'.$rc)->setValue('Grand Total');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleBoldLeft);
        $sheet->getCell('E'.$rc)->setValue(num($tot_qty));
        $sheet->getStyle('E'.$rc)->applyFromArray($styleBoldRight);
        // $sheet->getCell('C'.$rc)->setValue(num($tot_vat_sales));     
        // $sheet->getStyle('C'.$rc)->applyFromArray($styleBoldRight);
        // $sheet->getCell('D'.$rc)->setValue(num($tot_vat));     
        // $sheet->getStyle('D'.$rc)->applyFromArray($styleBoldRight);
        $sheet->getCell('G'.$rc)->setValue(num($tot_gross));     
        $sheet->getStyle('G'.$rc)->applyFromArray($styleBoldRight);
        // $sheet->getCell('D'.$rc)->setValue("");     
       
        
        update_load(100);        
       
        if (ob_get_contents())
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

    function test(){
        $this->load->model("dine/reports_model");

        $zread_amount = $this->reports_model->check_trans_zread('2021-10-01','2021-10-01','MAX');

        echo $zread_amount;
    }

     public function branch_sales_upload(){
        $data = $this->syter->spawn('branch_sales_upload_status_rep');
        $data['page_title'] = fa('icon-book-open')." Branch Sales Upload Status Report";
        $data['code'] = salesUploadRep();
        $data['add_css'] = array('css/morris/morris.css','css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css');
        $data['add_js'] = array('js/plugins/morris/morris.min.js','js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js');
        $data['page_no_padding'] = true;
        $data['sideBarHide'] = true;
        $data['load_js'] = 'dine/reporting';
        $data['use_js'] = 'branchSalesStatusRepJS';
        $this->load->view('page',$data);
    }

    public function branch_sales_upload_gen()
    {
        $this->load->model('dine/setup_model');
        // $this->load->database('main', TRUE);
        $this->load->model("dine/reports_model");
        $menu_cat_id = null;
        $branch_id = $this->input->post('branch_id'); 
        $brand = $this->input->post('brand');      
        
        
        start_load(0);
        // echo $branch_id;die();
        $daterange = $this->input->post("calendar_range");        
        // $daterange = "10/01/2021 to 10/07/2021";        
        $dates = explode(" to ",$daterange);
        
        $this->menu_model->db = $this->load->database('default', TRUE);
        $from = date('Y-m-d',strtotime($dates[0]));        
        $to = date('Y-m-d',strtotime($dates[1]));
    
        
        $counter = 0;

        $brand_txt = '';

        $brd = $this->setup_model->get_brands($brand);
        if($brd){
            $brand_txt = '/' . $brd[0]->brand_name;
        }

        $this->make->sDiv();
            $this->make->sTable(array("id"=>"main-tbl", 'class'=>'table reportTBL sortable'));
                $this->make->sTableHead();
                    $this->make->sRow();
                    $this->make->th('Branch');
                        while($from <= $to){
                            $this->make->th(date('m/d/Y',strtotime($from)));

                            $from = date('Y-m-d', strtotime($from . ' +1 day'));

                        }
                        
                    $this->make->eRow();
                $this->make->eTableHead();
                $this->make->sTableBody();

        $branches = $this->menu_model->get_branch_details($branch_id);

        foreach($branches as $branch){
            $from = date('Y-m-d',strtotime($dates[0]));

            $setup = $this->setup_model->get_details($branch->branch_code);
            $set = $setup[0];

            $this->make->sRow();
                $this->make->td($branch->branch_name.$brand_txt);
                while($from <= $to){            
                    $start = date2SqlDateTime($from. " ".$set->store_open);
                    $end = date2SqlDateTime(date('Y-m-d', strtotime($from . ' +1 day')). " ".$set->store_open);
                    
                    $trans_amount = $this->reports_model->check_trans_zread($start, $end,  $branch->branch_code,$brand);
                    $zread_amount = $this->reports_model->get_zread_amount($from, $branch->branch_code,$brand);

                     $status = $trans_amount . ''.$zread_amount;
                    $status = 'Not Successful';

                    if($trans_amount == 0){
                        $status = 'No Sale';
                    }
                    else if(round($trans_amount,2) == round($zread_amount,2)){
                        $status = 'Successful';
                    }

                    $this->make->td($status);

                     $from = date('Y-m-d', strtotime($from . ' +1 day'));           
                          
                }
            $this->make->eRow();   
        }

                                      
                $this->make->eTableBody();
            $this->make->eTable();
        $this->make->eDiv();

        header_remove('Set-Cookie');
        update_load(100);
        $code = $this->make->code();
        $json['code'] = $code;        
        $json['tbl_vals'] = '';
        $json['dates'] = $this->input->post('calendar_range');
        echo json_encode($json);
    }

     public function branch_sales_upload_pdf()
    {
         // Include the main TCPDF library (search for installation path).
        require_once( APPPATH .'third_party/tcpdf.php');
        $this->load->model("dine/setup_model");
        date_default_timezone_set('Asia/Manila');

        $this->load->model("dine/reports_model");

        // create new PDF document
        $pdf = new TCPDF("L", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('iPOS');
        $pdf->SetTitle('Branch Sales Upload Status Report');
        $pdf->SetSubject('');
        $pdf->SetKeywords('');

        // set default header data
        $branch_id = $_GET['branch_id'];
        $brand = $_GET['brand'];

        $setup = $this->setup_model->get_details($branch_id);
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
        $this->menu_model->db = $this->load->database('default', TRUE);
        $this->load->model("dine/gift_cards_model");
        start_load(0);

        // set font
        $pdf->SetFont('helvetica', 'B', 11);

        // add a page
        $pdf->AddPage();
                
        $branch_id = $_GET['branch_id'];
        $daterange = $_GET['calendar_range'];        
        $dates = explode(" to ",$daterange);
        // $from = date2SqlDateTime($dates[0]);
        // $to = date2SqlDateTime($dates[1]);
        $this->menu_model->db = $this->load->database('default', TRUE);
        $from = date('Y-m-d',strtotime($dates[0]));        
        $to = date('Y-m-d',strtotime($dates[1]));
       


        $pdf->Write(0, 'Branch Sales Upload Status Report', '', 0, 'L', true, 0, false, false, 0);
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

        // echo "<pre>", print_r($trans), "</pre>";die();

        // -----------------------------------------------------------------------------
        $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => 'black'));
        
        $pdf->cell(50, 0, 'Branch', 'B', 0, 'L'); 
        
        while($from <= $to){
            $this->make->th(date('m/d/Y',strtotime($from)));

             $pdf->cell(30, 0, date('m/d/Y',strtotime($from)), 'B', 0, 'L'); 

            $from = date('Y-m-d', strtotime($from . ' +1 day'));

        }         
        $pdf->ln();                   

        $branches = $this->menu_model->get_branch_details($branch_id); 

        $brand_txt = '';

        $brd = $this->setup_model->get_brands($brand);
        if($brd){
            $brand_txt = '/' . $brd[0]->brand_name;
        }

        foreach($branches as $branch){
            $from = date('Y-m-d',strtotime($dates[0]));

            $setup = $this->setup_model->get_details($branch->branch_code);
            $set = $setup[0];

            $pdf->cell(50,0,$branch->branch_name.$brand_txt,0,0,'L',0, '', 1);  
            while($from <= $to){            
                $start = date2SqlDateTime($from. " ".$set->store_open);
                $end = date2SqlDateTime(date('Y-m-d', strtotime($from . ' +1 day')). " ".$set->store_open);
                
                $trans_amount = $this->reports_model->check_trans_zread($start, $end,  $branch->branch_code,$brand);
                $zread_amount = $this->reports_model->get_zread_amount($from, $branch->branch_code,$brand);

                 $status = $trans_amount . ''.$zread_amount;
                $status = 'Not Successful';

                if($trans_amount == 0){
                    $status = 'No Sale';
                }
                else if(round($trans_amount,2) == round($zread_amount,2)){
                    $status = 'Successful';
                }

                $pdf->cell(30,0,$status,0,0,'L',0, '', 1);

                 $from = date('Y-m-d', strtotime($from . ' +1 day'));           
                      
            }  
            $pdf->ln();
        }

         
        
        update_load(100);

        //Close and output PDF document
        $pdf->Output('branch_sales_upload_report.pdf', 'I');

        //============================================================+
        // END OF FILE
        //============================================================+   
    }

    public function branch_sales_upload_excel()
    {
        $this->menu_model->db = $this->load->database('default', TRUE);
        $this->load->model("dine/reports_model");
        date_default_timezone_set('Asia/Manila');
        $this->load->library('Excel');
        $sheet = $this->excel->getActiveSheet();
        $filename = 'Branch Sales Upload Status Report';
        $rc=1;
        #GET VALUES
        start_load(0);
            // $post = $this->set_post($_GET['calendar_range']);
        $setup = $this->setup_model->get_details(1);
        if(isset($setup[0]))
        {
            $set = $setup[0];            
            $store_open = $set->store_open;
        }else{
            $store_open = "00:00:00";
        }

        update_load(10);
        sleep(1);
    
        $branch_id = $_GET['branch_id'];
        $brand = $_GET['brand'];
        $daterange = $_GET['calendar_range'];        
        $dates = explode(" to ",$daterange);
        // $from = date2SqlDateTime($dates[0]);
        // $to = date2SqlDateTime($dates[1]);
         $this->menu_model->db = $this->load->database('default', TRUE);
        $from = date('Y-m-d',strtotime($dates[0]));        
        $to = date('Y-m-d',strtotime($dates[1]));  
        

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
        
        $headers = array('Branch Code');

         while($from <= $to){
            $headers[] = date('m/d/Y',strtotime($from));

            $from = date('Y-m-d', strtotime($from . ' +1 day'));

        }         
        
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        // $sheet->getColumnDimension('H')->setWidth(20);


        $sheet->mergeCells('A'.$rc.':H'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Branch Sales Upload Status Report');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $rc++;

        $sheet->mergeCells('A'.$rc.':D'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Report Period: '.$daterange);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->mergeCells('E'.$rc.':H'.$rc);
        $sheet->getCell('E'.$rc)->setValue('Report Generated: '.(new \DateTime())->format('Y-m-d H:i:s'));
        $sheet->getStyle('E'.$rc)->applyFromArray($styleNum);
        $rc++;

        $sheet->mergeCells('A'.$rc.':D'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Transaction Time:');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $user = $this->session->userdata('user');
        $sheet->mergeCells('E'.$rc.':H'.$rc);
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

        $branches = $this->menu_model->get_branch_details($branch_id);

        $brd = $this->setup_model->get_brands($brand);
        if($brd){
            $brand_txt = '/' . $brd[0]->brand_name;
        }

        foreach($branches as $branch){
            $letter = 'B';
            $from = date('Y-m-d',strtotime($dates[0]));
            
            $setup = $this->setup_model->get_details($branch->branch_code);
            $set = $setup[0];

           $sheet->getCell('A'.$rc)->setValue($branch->branch_name.$brand_txt);
           $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);

                while($from <= $to){            
                    $start = date2SqlDateTime($from. " ".$set->store_open);
                    $end = date2SqlDateTime(date('Y-m-d', strtotime($from . ' +1 day')). " ".$set->store_open);
                    
                    $trans_amount = $this->reports_model->check_trans_zread($start, $end,  $branch->branch_code,$brand);
                    $zread_amount = $this->reports_model->get_zread_amount($from, $branch->branch_code,$brand);

                     $status = $trans_amount . ''.$zread_amount;
                    $status = 'Not Successful';

                    if($trans_amount == 0){
                        $status = 'No Sale';
                    }
                    else if(round($trans_amount,2) == round($zread_amount,2)){
                        $status = 'Successful';
                    }

                     $sheet->getCell($letter.$rc)->setValue($status);
                    $sheet->getStyle($letter.$rc)->applyFromArray($styleTxt);

                     $from = date('Y-m-d', strtotime($from . ' +1 day'));  

                     ++$letter;
                          
                }
            
            $rc++;  
        }  
       
        
        update_load(100);        
        
        if (ob_get_contents())
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

    public function collection_rep(){
        $data = $this->syter->spawn('collection_rep');
        $data['page_title'] = fa('icon-doc')." Collection Report";
        $data['code'] = collectionRep();
        $data['add_css'] = array('css/morris/morris.css','css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css');
        $data['add_js'] = array('js/plugins/morris/morris.min.js','js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js');
        $data['page_no_padding'] = true;
        $data['sideBarHide'] = true;
        $data['load_js'] = 'dine/reporting';
        $data['use_js'] = 'collectionRepJS';
        $this->load->view('page',$data);
    }

    public function collection_rep_gen()
    {
        $this->load->model('dine/setup_model');
        // $this->load->database('main', TRUE);
        $this->load->model("dine/collection_model");
        $menu_cat_id = null;
        $branch_id = $this->input->post('branch_id');
        $brand = $this->input->post('brand');      
        
        $setup = $this->setup_model->get_details($branch_id);
        $set = $setup[0];
        start_load(0);
        // echo $branch_id;die();
        $daterange = $this->input->post("calendar_range");        
        // $daterange = "05/30/2019 to 05/30/2019";        
        $dates = explode(" to ",$daterange);
        // $from = date2SqlDateTime($dates[0]);
        // $to = date2SqlDateTime($dates[1]);
        // $date = $this->input->post("date");
        // echo "<pre>",print_r($raw_data),"</pre>";die();
        $this->menu_model->db = $this->load->database('default', TRUE);
        $from = date2SqlDateTime($dates[0]. " ".$set->store_open);        
        $to = date2SqlDateTime(date('Y-m-d', strtotime($dates[1] . ' +1 day')). " ".$set->store_open);
    
        // echo "asdf";die(); 
        $trans_ret = $this->collection_model->get_collections_report($from, $to,  $branch_id,$brand);
        // echo $this->db->last_query(); die();             
        // echo "<pre>", print_r($trans_mod), "</pre>"; die();
        $trans_count = count($trans_ret);
        // $trans_count_ret = count($trans_ret);
        $counter = 0;
    
        $brand_txt = '';

        $brd = $this->setup_model->get_brands($brand);
        if($brd){
            $brand_txt = '/' . $brd[0]->brand_name;
        }

        $this->make->sDiv();
            $this->make->sTable(array("id"=>"main-tbl", 'class'=>'table reportTBL sortable'));
                $this->make->sTableHead();
                    $this->make->sRow();
                        if($branch_id == ''){
                            $this->make->th('Branch');
                        }
                        $this->make->th('Deposit Date');
                        $this->make->th('Reference');
                        $this->make->th('Bank Name');
                        $this->make->th('Bank Account Name');                        
                        $this->make->th('Sales Date');
                        $this->make->th('Sales Amount');
                        $this->make->th('Deposit Amount');
                        $this->make->th('Remarks');
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
                    $tot_cost_prcnt = 0;
                    // foreach ($trans_ret as $v) {
                    //     $tot_gross += $v->gross;
                        // $tot_cost += $v->cost;
                    // }
                    foreach ($trans_ret as $res) {
                         if($counter % 500 == 0){
                            set_time_limit(60);  
                        }


                        $this->make->sRow();
                            if($branch_id == ''){
                                $this->make->td($res->branch_name.$brand_txt);
                            }
                            $this->make->td(sql2Date($res->deposit_date));
                            $this->make->td($res->reference);
                            $this->make->td($res->bank_name);
                            $this->make->td($res->bank_account_name);
                            $this->make->td(sql2Date($res->sales_date));
                            $this->make->td(num($res->sales_amount), array("style"=>"text-align:right"));                               
                            $this->make->td(num($res->deposit_amount), array("style"=>"text-align:right"));                            
                            $this->make->td($res->remarks);     
                        $this->make->eRow();

                      

                        $counter++;
                        $progress = ($counter / $trans_count) * 100;
                        update_load(num($progress));

                    }    
                                                     
                $this->make->eTableBody();
            $this->make->eTable();
        $this->make->eDiv();

        header_remove('Set-Cookie');
        update_load(100);
        $code = $this->make->code();
        $json['code'] = $code;        
        $json['tbl_vals'] = $trans_ret;
        $json['dates'] = $this->input->post('calendar_range');
        echo json_encode($json);
    }

    public function collection_rep_pdf()
    {
         // Include the main TCPDF library (search for installation path).
        require_once( APPPATH .'third_party/tcpdf.php');
        $this->load->model("dine/setup_model");
        $this->load->model("dine/collection_model");
        date_default_timezone_set('Asia/Manila');

        // create new PDF document
        $pdf = new TCPDF("L", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('iPOS');
        $pdf->SetTitle('Collection Report');
        $pdf->SetSubject('');
        $pdf->SetKeywords('');

        // set default header data
        $branch_id = $_GET['branch_id'];
        $setup = $this->setup_model->get_details($branch_id);
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
        $this->menu_model->db = $this->load->database('default', TRUE);
        $this->load->model("dine/gift_cards_model");
        start_load(0);

        // set font
        $pdf->SetFont('helvetica', 'B', 11);

        // add a page
        $pdf->AddPage();
                
        $branch_id = $_GET['branch_id'];
        $brand = $_GET['brand'];
        $daterange = $_GET['calendar_range'];        
        $dates = explode(" to ",$daterange);
        // $from = date2SqlDateTime($dates[0]);
        // $to = date2SqlDateTime($dates[1]);
        $from = date2SqlDateTime($dates[0]. " ".$set->store_open);        
        $to = date2SqlDateTime(date('Y-m-d', strtotime($dates[1] . ' +1 day')). " ".$set->store_open);
       


        $pdf->Write(0, 'Collection Report', '', 0, 'L', true, 0, false, false, 0);
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

        // echo "<pre>", print_r($trans), "</pre>";die();

        // -----------------------------------------------------------------------------
        $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => 'black'));
        
        $pdf->cell(30, 0, 'Branch', 'B', 0, 'L'); 
        $pdf->Cell(20, 0, 'Deposit Date', 'B', 0, 'L');
        $pdf->Cell(30, 0, 'Reference', 'B', 0, 'L');   
        $pdf->Cell(32, 0, 'Bank Name', 'B', 0, 'L');
        $pdf->Cell(32, 0, 'Bank Accunt Name', 'B', 0, 'L');
        $pdf->Cell(20, 0, 'Sales Date', 'B', 0, 'L');        
        $pdf->Cell(32, 0, 'Sales Amount', 'B', 0, 'R');        
        $pdf->Cell(32, 0, 'Deposited Amount', 'B', 0, 'R');        
        $pdf->Cell(41, 0, 'Remarks', 'B', 0, 'L');          
        $pdf->ln();             

        $trans = $this->collection_model->get_collections_report($from,$to,$branch_id,$brand);  

        // GRAND TOTAL VARIABLES
        $counter = 0;
        $progress = 0;
        $trans_count = count($trans);

        $brd = $this->setup_model->get_brands(null,$branch_id);
        $brand_txt = '';
        
       
        foreach ($trans as $k => $v) {
            if($brand == '' && count($brd) > 1){
                $brand_txt = '/' . $v->brand_code;
            }

            $pdf->cell(30,0,$v->branch_code.$brand_txt,0,0,'L',0, '', 1);  
            
            $pdf->cell(20,0,sql2Date($v->deposit_date),0,0,'L',0, '', 1);
            $pdf->cell(30,0,$v->reference,0,0,'L',0, '', 1);
            $pdf->cell(32,0,$v->bank_name,0,0,'L',0, '', 1);
            $pdf->cell(32,0,$v->bank_account_name,0,0,'L',0, '', 1);
            $pdf->cell(20,0,sql2Date($v->sales_date),0,0,'L',0, '', 1);
            $pdf->Cell(32, 0, num($v->sales_amount), '', 0, 'R');        
            $pdf->Cell(32, 0, num($v->deposit_amount), '', 0, 'R');          
            $pdf->Cell(41, 0, $v->remarks, '', 0, 'L');            
              
            $pdf->ln();         

            $counter++;
            $progress = ($counter / $trans_count) * 100;
            update_load(num($progress));              
        }
            
        
        update_load(100);

        //Close and output PDF document
        $pdf->Output('collection_report.pdf', 'I');

        //============================================================+
        // END OF FILE
        //============================================================+   
    }

    public function collection_rep_excel()
    {
        $this->menu_model->db = $this->load->database('default', TRUE);
        $this->load->model("dine/collection_model");
        date_default_timezone_set('Asia/Manila');
        $this->load->library('Excel');
        $sheet = $this->excel->getActiveSheet();
        $filename = 'collection Report';
        $rc=1;
        #GET VALUES
        start_load(0);
            // $post = $this->set_post($_GET['calendar_range']);
        $setup = $this->setup_model->get_details(1);
        if(isset($setup[0]))
        {
            $set = $setup[0];            
            $store_open = $set->store_open;
        }else{
            $store_open = "00:00:00";
        }

        update_load(10);
        sleep(1);
               
        $daterange = $_GET['calendar_range'];        
        $dates = explode(" to ",$daterange);
        // $from = date2SqlDateTime($dates[0]);
        // $to = date2SqlDateTime($dates[1]);
        $from = date2SqlDateTime($dates[0]. " ".$store_open);        
        $to = date2SqlDateTime(date('Y-m-d', strtotime($dates[1] . ' +1 day')). " ".$store_open);
        // $branch_id = $this->input->post('branch_id');
        $branch_id = $_GET['branch_id'];
        $brand = $_GET['brand'];    
        $trans = $this->collection_model->get_collections_report($from, $to, $branch_id,$brand);   
        

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
        // if($branch_id == ""){
            $headers = array('Branch Code', 'Deposit Date','Reference','Bank Name','Bank Account Name','Sales Date','Sales Amount','Deposit Amount','Remarks');
        // }
        // else{
        //     $headers = array('GC Description','Qty','Amount','Total');
        // }
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        // $sheet->getColumnDimension('H')->setWidth(20);

        $sheet->mergeCells('A'.$rc.':H'.$rc);
        $sheet->getCell('A'.$rc)->setValue($set->address);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        $rc++;

        $sheet->mergeCells('A'.$rc.':H'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Collection Report');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $rc++;

        $sheet->mergeCells('A'.$rc.':D'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Report Period: '.$daterange);
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->mergeCells('E'.$rc.':H'.$rc);
        $sheet->getCell('E'.$rc)->setValue('Report Generated: '.(new \DateTime())->format('Y-m-d H:i:s'));
        $sheet->getStyle('E'.$rc)->applyFromArray($styleNum);
        $rc++;

        $sheet->mergeCells('A'.$rc.':D'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Transaction Time:');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $user = $this->session->userdata('user');
        $sheet->mergeCells('E'.$rc.':H'.$rc);
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
        
        $counter = 0;
        $progress = 0;
        $trans_count = count($trans);
        
        // foreach ($trans_mod as $vv) {
        //     $tot_mod_gross += $vv->mod_gross;
        // }

        $brd = $this->setup_model->get_brands(null,$branch_id);
        $brand_txt = '';

        foreach ($trans as $k => $v) {
            if($brand == '' && count($brd) > 1){
                $brand_txt = '/' . $v->brand_code;
            }
            
            $sheet->getCell('A'.$rc)->setValue($v->branch_code.$brand_txt);
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            
            
            $sheet->getCell('B'.$rc)->setValue(date2Sql($v->deposit_date));
            $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);

            $sheet->getCell('C'.$rc)->setValue($v->reference);
            $sheet->getStyle('C'.$rc)->applyFromArray($styleTxt);

            $sheet->getCell('D'.$rc)->setValue($v->bank_name);
            $sheet->getStyle('D'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('E'.$rc)->setValue($v->bank_account_name);
            $sheet->getStyle('E'.$rc)->applyFromArray($styleTxt);
            // $sheet->getCell('C'.$rc)->setValue(num($v->vat_sales));
            // $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
            // $sheet->getCell('D'.$rc)->setValue(num($v->vat));     
            // $sheet->getStyle('D'.$rc)->applyFromArray($styleNum);
            $sheet->getCell('F'.$rc)->setValue(date2Sql($v->sales_date));     
            $sheet->getStyle('F'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('G'.$rc)->setValue(num($v->sales_amount));     
            $sheet->getStyle('G'.$rc)->applyFromArray($styleNum);

            $sheet->getCell('H'.$rc)->setValue(num($v->deposit_amount));            
            $sheet->getStyle('H'.$rc)->applyFromArray($styleNum);

            $sheet->getCell('I'.$rc)->setValue($v->remarks);     
            $sheet->getStyle('I'.$rc)->applyFromArray($styleTxt);
            
            $tot_qty += $v->qty;
            // $tot_vat_sales += $v->vat_sales;
            // $tot_vat += $v->vat;
            // $tot_gross += $v->gross;
            $tot_sales_prcnt = 0;
            // $tot_cost += $v->cost;
            $tot_cost_prcnt = 0;

            $counter++;
            $progress = ($counter / $trans_count) * 100;
            update_load(num($progress));   
            $rc++;           
        }

        
        update_load(100);        
       
        if (ob_get_contents())
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

    public function menus_rep_hourly(){
        $data = $this->syter->spawn('menu_sales_rep');
        $data['page_title'] = fa('icon-book-open')." Menu Hourly Sales Report";
        $data['code'] = menusRepHourly();
        $data['add_css'] = array('css/morris/morris.css','css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css');
        $data['add_js'] = array('js/plugins/morris/morris.min.js','js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js');
        $data['page_no_padding'] = true;
        $data['sideBarHide'] = true;
        $data['load_js'] = 'dine/reporting';
        $data['use_js'] = 'menusRepHrJS';
        $this->load->view('page',$data);
    }

    public function menus_hourly_rep_gen(){
        start_load(0);
        $this->load->model('dine/setup_model');

        $branch = $this->input->post("branch_code");        
        $daterange = $this->input->post("calendar_range");        
        $dates = explode(" to ",$daterange);
        // $from = date2SqlDateTime($dates[0]);
        // $to = date2SqlDateTime($dates[1]);
        // $date = $this->input->post("date");
        $from = date2SqlDateTime($dates[0]);        
        $to = date2SqlDateTime($dates[1]);        
        // $to = date2SqlDateTime(date('Y-m-d', strtotime($dates[1] . ' +1 day')). " ".$set->store_open);
        update_load(10);
        // $this->menu_model->db = $this->load->database('main', TRUE);
        $trans = $this->menu_model->get_menu_sales_rep_hourly($from, $to, $branch);

        // echo "<pre>", print_r($trans), "</pre>"; die();
        // die('asfsdf');

        $datas = array();

        if($trans){

            // echo 'weeee'; die();
            $tran_ctr = 0;
            $t_salesid = '';
            foreach ($trans as $det => $v) {
                // if($tran_ctr == 0){
                //     $t_salesid = $v->sales_id;
                // }
                if($t_salesid != $v->ter_sales_id){
                    //unang pasok sa sales_id
                    //get charges
                    $charges = 0;
                    $span_charge = '';
                    $args2 = array();
                    $args2["sales_id"] = $v->sales_id;
                    $args2["pos_id"] = $v->ter_id;
                    $args2["branch_code"] = $branch;
                    $chrg = $this->site_model->get_tbl('trans_sales_charges',$args2,array(),array(),true,'*');

                    if($chrg){
                        $span_charge = 'span';
                        foreach($chrg as $cid =>$vv){
                            $charges += $vv->amount;
                        }
                    }

                    //get discounts
                    $span_disc = '';
                    $lessvat = 0;
                    $net_sales = 0;
                    $discounts = 0;
                    $is_line_disc = false;
                    $disc_line_id = '';
                    $disc_name = '';
                    $join = array();
                    $join['receipt_discounts'] = array('content'=>'receipt_discounts.disc_id = trans_sales_discounts.disc_id and receipt_discounts.branch_code = trans_sales_discounts.branch_code');
                    $args2 = array();
                    $args2["sales_id"] = $v->sales_id;
                    $args2["pos_id"] = $v->ter_id;
                    $args2["trans_sales_discounts.branch_code"] = $branch;
                    $disc = $this->site_model->get_tbl('trans_sales_discounts',$args2,array(),$join,true,'trans_sales_discounts.*, disc_name');

                    // die('fff');
                    if($disc){
                        foreach($disc as $did =>$vv){
                            $disc_line_id = $vv->items;
                            if($disc_name != ''){
                                $disc_name .= ", ".$vv->disc_name;
                            }else{
                                $disc_name .= $vv->disc_name;
                            }
                            $discounts += $vv->amount;
                        }

                        if($disc_line_id != ''){
                            $is_line_disc = true;
                        }else{
                            // get the vat exempt and netsales
                            $span_disc = 'span';
                            if($vv->no_tax == 1){
                                $args3["sales_id"] = $v->sales_id;
                                $args3["pos_id"] = $v->ter_id;
                                $args3["branch_code"] = $branch;
                                $menus = $this->site_model->get_tbl('trans_sales_menus',$args3,array(),array(),true,'*');
                                $tgross = 0;
                                foreach($menus as $mid => $vm){
                                    $tgross += $vm->qty * $vm->price;
                                }

                                $lessvat = ($tgross / 1.12) * 0.12;
                                $net_sales = $tgross - $discounts - $lessvat;

                            }
                        }

                        if($is_line_disc){
                            //per line discount
                            $discounts = 0;
                            $lessvat = 0;
                            $disc_name = '';
                            $disc_line_id = '';
                            $join = array();
                            $join['receipt_discounts'] = array('content'=>'receipt_discounts.disc_id = trans_sales_discounts.disc_id and receipt_discounts.branch_code = trans_sales_discounts.branch_code');
                            $args2 = array();
                            $args2["sales_id"] = $v->sales_id;
                            $args2["pos_id"] = $v->ter_id;
                            $args2["items"] = $v->line_id;
                            $args2["trans_sales_discounts.branch_code"] = $branch;
                            $disc2 = $this->site_model->get_tbl('trans_sales_discounts',$args2,array(),$join,true,'trans_sales_discounts.*, disc_name');
                            // if($disc2){
                                foreach($disc2 as $did2 =>$vv2){
                                    if($disc_name != ''){
                                        $disc_name .= ", ".$vv2->disc_name;
                                    }else{
                                        $disc_name .= $vv2->disc_name;
                                    }
                                    $disc_line_id = $vv2->items;
                                    $discounts = $vv2->amount;
                                    if($vv2->no_tax == 1){
                                        $lessvat = ($v->gross/1.12) * 0.12;
                                    }
                                    $net_sales = $v->gross - $lessvat - $discounts;
                                }
                            // }else{
                            //     $disc_line_id = '';
                            // }

                        }
                    }



                }else{
                    //2nd pasok and so on

                    // die('ssss');
                   
                    if($charges != 0){
                        $charges = 0;
                    }

                    if($discounts != 0 && $is_line_disc == false){
                        $discounts = 0;
                        // $lessvat = 'span';
                        // $net_sales = 'span';
                    }else{
                        // if($is_line_disc != ''){
                            $discounts = 0;
                            $lessvat = 0;
                            $disc_name = '';
                            $disc_line_id = '';
                            $join = array();
                            $join['receipt_discounts'] = array('content'=>'receipt_discounts.disc_id = trans_sales_discounts.disc_id and receipt_discounts.branch_code = trans_sales_discounts.branch_code');
                            $args2 = array();
                            $args2["sales_id"] = $v->sales_id;
                            $args2["items"] = $v->line_id;
                            $args2["pos_id"] = $v->ter_id;
                            $args2["trans_sales_discounts.branch_code"] = $branch;
                            $disc2 = $this->site_model->get_tbl('trans_sales_discounts',$args2,array(),$join,true,'trans_sales_discounts.*, disc_name');

                            foreach($disc2 as $did2 =>$vv2){
                                if($disc_name != ''){
                                    $disc_name .= ", ".$vv2->disc_name;
                                }else{
                                    $disc_name .= $vv2->disc_name;
                                }
                                $disc_line_id = $vv2->items;
                                $discounts = $vv2->amount;
                                if($vv2->no_tax == 1){
                                    $lessvat = ($v->gross/1.12) * 0.12;
                                }
                                $net_sales = $v->gross - $lessvat - $discounts;
                            }
                        // }
                    }

                }

                $args3 = array();
                $args3["sales_id"] = $v->sales_id;
                $args3["pos_id"] = $v->ter_id;
                $args3["branch_code"] = $branch;
                $group = 'sales_id,payment_type';
                $pays = $this->site_model->get_tbl('trans_sales_payments',$args3,array(),array(),true,'*',$group);
                // $pays = $this->site_model->get_tbl('trans_sales_payments',$args3,array(),array(),true,'*');

                $pay_type = '';
                foreach ($pays as $pid => $p) {
                    if($pay_type != ''){
                        $pay_type .= ', '.$p->payment_type;
                    }else{
                        $pay_type .= $p->payment_type;
                    }

                }

                $remark = 'VALID';
                if($v->inactive == 1){
                    $remark = 'VOIDED';
                }

                if($v->guest == 0){
                    $guest = 1;
                }else{
                    $guest = $v->guest;
                }

                $setup = $this->setup_model->get_details($branch);
                $set = $setup[0];
                
                $datas[$v->ter_sales_id][] = array(
                    'datetime'=>$v->datetime,
                    'terminal_code'=>$v->terminal_code,
                    'outlet'=>$set->branch_name,
                    'customer'=>'',
                    'trans_ref'=>$v->trans_ref,
                    'menu_code'=>$v->menu_code,
                    'menu_name'=>$v->menu_name,
                    'qty'=>$v->qty,
                    'price'=>$v->mprice,
                    'gross'=>$v->gross,
                    'trans_type'=>$v->trans_type,
                    'guest'=>$guest,
                    'sales_id'=>$v->sales_id,
                    'charges'=>$charges,
                    'remarks'=>$remark,
                    'line_id'=>$v->line_id,
                    'discount'=>$discounts,
                    'disc_line_id'=>$disc_line_id,
                    'disc_name'=>$disc_name,
                    'lessvat'=>$lessvat,
                    'net_sales'=>$net_sales,
                    'span_disc'=>$span_disc,
                    'span_charge'=>$span_charge,
                    'payment_type'=>$pay_type,
                    'ter_sales_id'=>$v->ter_sales_id,
                );

                $t_salesid = $v->ter_sales_id;
                // $tran_ctr++;
            }
        }

        // echo "<pre>", print_r(count($datas[4])), "</pre>"; die();
        // echo "<pre>", print_r($datas), "</pre>"; die();


        // $post = $this->set_post();
        // $curr = $this->search_current();
        // $trans = $this->trans_sales($post['args'],$curr);
        // $sales = $trans['sales'];
        update_load(15);
        // $trans_menus = $this->menu_sales($sales['settled']['ids'],$curr);
        // update_load(20);
        // $trans_charges = $this->charges_sales($sales['settled']['ids'],$curr);
        // update_load(25);
        // $trans_discounts = $this->discounts_sales($sales['settled']['ids'],$curr);
        // update_load(30);
        // $trans_local_tax = $this->local_tax_sales($sales['settled']['ids'],$curr);
        // update_load(35);
        // $trans_tax = $this->tax_sales($sales['settled']['ids'],$curr);
        // update_load(40);
        // $trans_no_tax = $this->no_tax_sales($sales['settled']['ids'],$curr);
        // update_load(45);
        // $trans_zero_rated = $this->zero_rated_sales($sales['settled']['ids'],$curr);
        // update_load(50);
        // $payments = $this->payment_sales($sales['settled']['ids'],$curr);
        // update_load(53);
        // $gross = $trans_menus['gross']; 
        // $net = $trans['net'];
        // $charges = $trans_charges['total']; 
        // $discounts = $trans_discounts['total']; 
        // $local_tax = $trans_local_tax['total']; 
        // $less_vat = (($gross+$charges+$local_tax) - $discounts) - $net;
        // if($less_vat < 0)
        //     $less_vat = 0;
        // $tax = $trans_tax['total'];
        // $no_tax = $trans_no_tax['total'];
        // $zero_rated = $trans_zero_rated['total'];
        // $no_tax -= $zero_rated;
        
        // update_load(55);
        // $cats = $trans_menus['cats'];                 
        // $menus = $trans_menus['menus'];
        // $subcats = $trans_menus['sub_cats'];      
        // $menu_total = $trans_menus['menu_total'];
        // $total_qty = $trans_menus['total_qty'];
        // update_load(60);
        // usort($menus, function($a, $b) {
        //     return $b['amount'] - $a['amount'];
        // });
        update_load(80);
        $this->make->sDiv(array('style'=>'overflow:auto;height:800px;'));
            $this->make->sTable(array('class'=>'table reportTBL'));
                $this->make->sTableHead();
                    $this->make->sRow();
                        $this->make->th('No.');
                        $this->make->th('Time');
                        $this->make->th('Sales Date');
                        $this->make->th('Terminal Code');
                        $this->make->th('Outlet');
                        $this->make->th('OR No.');
                        $this->make->th('Customer Name');
                        $this->make->th('SKU Code');
                        $this->make->th('Sku Description');
                        $this->make->th('Qty');
                        $this->make->th('Price');
                        $this->make->th('Gross Sales');
                        $this->make->th('Type of Disc.');
                        $this->make->th('Discount Amount');
                        $this->make->th('Vat Duduct');
                        $this->make->th('Net Sales');
                        $this->make->th('Mode of Payment');
                        $this->make->th('Trans Type');
                        $this->make->th('No. Of Guest');
                        $this->make->th('Charges');
                        $this->make->th('Remarks');
                        // $this->make->th('Remarks');
                    $this->make->eRow();
                $this->make->eTableHead();
                $this->make->sTableBody();
                    $ctr = 1;
                    foreach ($datas as $sid => $trans) {
                        foreach ($trans as $res => $v) {
                            $this->make->sRow();
                                $this->make->td($ctr);
                                $this->make->td(date('h:i:s A',strtotime($v['datetime'])));
                                $this->make->td(date('m/d/Y',strtotime($v['datetime'])));
                                $this->make->td($v['terminal_code']);
                                $this->make->td($v['outlet']);
                                $this->make->td($v['trans_ref']);
                                $this->make->td($v['customer']);
                                $this->make->td($v['menu_code']);
                                $this->make->td($v['menu_name']);
                                // $this->make->td($cats[$res['cat_id']]['name']);
                                // $this->make->td($res['sell_price']);
                                $this->make->td($v['qty']);
                                // $this->make->td('');
                                $this->make->td(num($v['gross']));
                                $this->make->td(num($v['gross']));
                                

                                if($v['span_disc'] == 'span'){
                                    if($v['discount'] != 0){
                                        //discount whole transaction
                                        $rowspan = count($datas[$v['ter_sales_id']]);
                                        $this->make->td($v['disc_name'],array('rowspan'=>'','style'=>'vertical-align:middle;'));
                                        $this->make->td(num($v['discount']),array('rowspan'=>'','style'=>'vertical-align:middle;'));
                                        $this->make->td(num($v['lessvat']),array('rowspan'=>'','style'=>'vertical-align:middle;'));
                                        $this->make->td(num($v['net_sales']),array('rowspan'=>'','style'=>'vertical-align:middle;'));
                                    }else{
                                        $this->make->td($v['disc_name']);
                                        $this->make->td(num($v['discount']));
                                        $this->make->td(num($v['lessvat']));
                                        $this->make->td(num($v['net_sales']));
                                    }
                                    //     //per line disc
                                    //     if($v['discount'] == "span" || $v['discount'] != 0){
                                    //         // $this->make->td(num($v['discount']));

                                    //     }else{
                                    //         $this->make->td($v['disc_name']);
                                    //         $this->make->td(num($v['discount']));
                                    //         $this->make->td(num($v['lessvat']));
                                    //         $this->make->td(num($v['net_sales']));
                                    //     }
                                        

                                    // }
                                }else{
                                    if($v['discount'] != 0){
                                        $this->make->td($v['disc_name']);
                                        $this->make->td(num($v['discount']));
                                        $this->make->td(num($v['lessvat']));
                                        $this->make->td(num($v['net_sales']));

                                    }else{

                                        $this->make->td('');
                                        $this->make->td('0');
                                        $this->make->td('0');
                                        $this->make->td(num($v['gross']));
                                    }
                                }
                                // $net_sales
                                // $this->make->td(num($v->gross));
                                $this->make->td($v['payment_type']);
                                $this->make->td($v['trans_type']);
                                $this->make->td($v['guest']);

                                if($v['span_charge'] == 'span'){
                                    if($v['charges'] != 0){
                                        //discount whole transaction
                                        $rowspan = count($datas[$v['ter_sales_id']]);
                                        $this->make->td(num($v['charges']),array('rowspan'=>$rowspan,'style'=>'vertical-align:middle;'));
                                    }
                                }else{
                                    $this->make->td('0');
                                }


                                $this->make->td($v['remarks']);
                                // $this->make->td( num( ($res['qty'] / $total_qty) * 100 ).'%' );
                                // $this->make->td( num($res['amount']) );
                                // $this->make->td( num( ($res['amount'] / $menu_total) * 100 ).'%' );
                                // $this->make->td($res['cost_price']);
                                // $this->make->td($res['cost_price'] * $res['qty']);
                            $this->make->eRow();

                            $ctr++;
                        }
                    }    
                    
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

    public function menus_rep_hrly_excel(){
        $this->load->model('dine/setup_model');
        $this->load->library('Excel');
        $sheet = $this->excel->getActiveSheet();
        $filename = 'Menu Hourly Sales Report';
        $rc=1;
        #GET VALUES
        start_load(0);

        $branch = $_GET['branch_code'];
        $daterange = $_GET['calendar_range'];      
        $dates = explode(" to ",$daterange);
        // $from = date2SqlDateTime($dates[0]);
        // $to = date2SqlDateTime($dates[1]);
        // $date = $this->input->post("date");
        $from = date2SqlDateTime($dates[0]);        
        $to = date2SqlDateTime($dates[1]);        
        // $to = date2SqlDateTime(date('Y-m-d', strtotime($dates[1] . ' +1 day')). " ".$set->store_open);
        update_load(10);
        $trans = $this->menu_model->get_menu_sales_rep_hourly($from, $to, $branch);


        $datas = array();

        if($trans){
            $tran_ctr = 0;
            $t_salesid = '';
            foreach ($trans as $det => $v) {

                // if($tran_ctr == 0){
                //     $t_salesid = $v->sales_id;
                // }
                if($t_salesid != $v->ter_sales_id){
                    //unang pasok sa sales_id
                    //get charges
                    $charges = 0;
                    $span_charge = '';
                    $args2 = array();
                    $args2["sales_id"] = $v->sales_id;
                    $args2["pos_id"] = $v->ter_id;
                    $args2["branch_code"] = $branch;
                    // var_dump($args2); die();
                    $chrg = $this->site_model->get_tbl('trans_sales_charges',$args2,array(),array(),true,'*');

                    // die('sssssd');
                    if($chrg){
                        $span_charge = 'span';
                        foreach($chrg as $cid =>$vv){
                            $charges += $vv->amount;
                        }
                    }


                    //get discounts
                    $span_disc = '';
                    $lessvat = 0;
                    $net_sales = 0;
                    $discounts = 0;
                    $is_line_disc = false;
                    $disc_line_id = '';
                    $disc_name = '';
                    $join = array();
                    $join['receipt_discounts'] = array('content'=>'receipt_discounts.disc_id = trans_sales_discounts.disc_id and receipt_discounts.branch_code = trans_sales_discounts.branch_code');
                    $args2 = array();
                    $args2["sales_id"] = $v->sales_id;
                    $args2["pos_id"] = $v->ter_id;
                    $args2["trans_sales_discounts.branch_code"] = $branch;
                    $disc = $this->site_model->get_tbl('trans_sales_discounts',$args2,array(),$join,true,'trans_sales_discounts.*, disc_name');

                    if($disc){
                        foreach($disc as $did =>$vv){
                            $disc_line_id = $vv->items;
                            if($disc_name != ''){
                                $disc_name .= ", ".$vv->disc_name;
                            }else{
                                $disc_name .= $vv->disc_name;
                            }
                            $discounts += $vv->amount;
                        }

                        if($disc_line_id != ''){
                            $is_line_disc = true;
                        }else{
                            // get the vat exempt and netsales
                            $span_disc = 'span';
                            if($vv->no_tax == 1){
                                $args3["sales_id"] = $v->sales_id;
                                $args3["pos_id"] = $v->ter_id;
                                $args3["branch_code"] = $branch;
                                $menus = $this->site_model->get_tbl('trans_sales_menus',$args3,array(),array(),true,'*');
                                $tgross = 0;
                                foreach($menus as $mid => $vm){
                                    $tgross += $vm->qty * $vm->price;
                                }

                                $lessvat = ($tgross / 1.12) * 0.12;
                                $net_sales = $tgross - $discounts - $lessvat;

                            }
                        }

                        if($is_line_disc){
                            //per line discount
                            $discounts = 0;
                            $lessvat = 0;
                            $disc_name = '';
                            $disc_line_id = '';
                            $join = array();
                            $join['receipt_discounts'] = array('content'=>'receipt_discounts.disc_id = trans_sales_discounts.disc_id and receipt_discounts.branch_code = trans_sales_discounts.branch_code');
                            $args2 = array();
                            $args2["sales_id"] = $v->sales_id;
                            $args2["items"] = $v->line_id;
                            $args2["pos_id"] = $v->ter_id;
                            $args2["trans_sales_discounts.branch_code"] = $branch;
                            $disc2 = $this->site_model->get_tbl('trans_sales_discounts',$args2,array(),$join,true,'trans_sales_discounts.*, disc_name');
                            // if($disc2){
                                foreach($disc2 as $did2 =>$vv2){
                                    if($disc_name != ''){
                                        $disc_name .= ", ".$vv2->disc_name;
                                    }else{
                                        $disc_name .= $vv2->disc_name;
                                    }
                                    $disc_line_id = $vv2->items;
                                    $discounts = $vv2->amount;
                                    if($vv2->no_tax == 1){
                                        $lessvat = ($v->gross/1.12) * 0.12;
                                    }
                                    $net_sales = $v->gross - $lessvat - $discounts;
                                }
                            // }else{
                            //     $disc_line_id = '';
                            // }

                        }
                    }



                }else{
                    //2nd pasok and so on
                   
                    if($charges != 0){
                        $charges = 0;
                    }

                    if($discounts != 0 && $is_line_disc == false){
                        $discounts = 0;
                        // $lessvat = 'span';
                        // $net_sales = 'span';
                    }else{
                        // if($is_line_disc != ''){
                            $discounts = 0;
                            $lessvat = 0;
                            $disc_name = '';
                            $disc_line_id = '';
                            $join = array();
                            $join['receipt_discounts'] = array('content'=>'receipt_discounts.disc_id = trans_sales_discounts.disc_id and receipt_discounts.branch_code = trans_sales_discounts.branch_code');
                            $args2 = array();
                            $args2["sales_id"] = $v->sales_id;
                            $args2["items"] = $v->line_id;
                            $args2["pos_id"] = $v->ter_id;
                            $args2["trans_sales_discounts.branch_code"] = $branch;
                            $disc2 = $this->site_model->get_tbl('trans_sales_discounts',$args2,array(),$join,true,'trans_sales_discounts.*, disc_name');

                            foreach($disc2 as $did2 =>$vv2){
                                if($disc_name != ''){
                                    $disc_name .= ", ".$vv2->disc_name;
                                }else{
                                    $disc_name .= $vv2->disc_name;
                                }
                                $disc_line_id = $vv2->items;
                                $discounts = $vv2->amount;
                                if($vv2->no_tax == 1){
                                    $lessvat = ($v->gross/1.12) * 0.12;
                                }
                                $net_sales = $v->gross - $lessvat - $discounts;
                            }
                        // }
                    }

                }

                $args3 = array();
                $args3["sales_id"] = $v->sales_id;
                $args3["pos_id"] = $v->ter_id;
                $args3["branch_code"] = $branch;
                $group = 'sales_id,payment_type';
                $pays = $this->site_model->get_tbl('trans_sales_payments',$args3,array(),array(),true,'*',$group);
                // $pays = $this->site_model->get_tbl('trans_sales_payments',$args3,array(),array(),true,'*');

                $pay_type = '';
                foreach ($pays as $pid => $p) {
                    if($pay_type != ''){
                        $pay_type .= ', '.$p->payment_type;
                    }else{
                        $pay_type .= $p->payment_type;
                    }

                }

                $remark = 'VALID';
                if($v->inactive == 1){
                    $remark = 'VOIDED';
                }

                if($v->guest == 0){
                    $guest = 1;
                }else{
                    $guest = $v->guest;
                }

                $setup = $this->setup_model->get_details($branch);
                $set = $setup[0];
                
                $datas[$v->ter_sales_id][] = array(
                    'datetime'=>$v->datetime,
                    'terminal_code'=>$v->terminal_code,
                    'outlet'=>$set->branch_name,
                    'customer'=>'',
                    'trans_ref'=>$v->trans_ref,
                    'menu_code'=>$v->menu_code,
                    'menu_name'=>$v->menu_name,
                    'qty'=>$v->qty,
                    'price'=>$v->mprice,
                    'gross'=>$v->gross,
                    'trans_type'=>$v->trans_type,
                    'guest'=>$guest,
                    'sales_id'=>$v->sales_id,
                    'charges'=>$charges,
                    'remarks'=>$remark,
                    'line_id'=>$v->line_id,
                    'discount'=>$discounts,
                    'disc_line_id'=>$disc_line_id,
                    'disc_name'=>$disc_name,
                    'lessvat'=>$lessvat,
                    'net_sales'=>$net_sales,
                    'span_disc'=>$span_disc,
                    'span_charge'=>$span_charge,
                    'payment_type'=>$pay_type,
                    'ter_sales_id'=>$v->ter_sales_id,
                );

                $t_salesid = $v->ter_sales_id;
                // $tran_ctr++;
            }
        }


        update_load(15);
            
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
        $styleNumMerge = array(
            'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );
        $styleTxt = array(
            'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            ),
        );
        $styleTxtMerge = array(
            'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
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

        // $this->make->th('No.');
        // $this->make->th('Time');
        // $this->make->th('Sales Date');
        // $this->make->th('Terminal Code');
        // $this->make->th('Outlet');
        // $this->make->th('OR No.');
        // $this->make->th('Customer Name');
        // $this->make->th('SKU Code');
        // $this->make->th('Sku Description');
        // $this->make->th('Qty');
        // $this->make->th('Price');
        // $this->make->th('Gross Sales');
        // $this->make->th('Type of Disc.');
        // $this->make->th('Vat Duduct');
        // $this->make->th('Net Sales');
        // $this->make->th('Trans Type');
        // $this->make->th('No. Of Guest');
        // $this->make->th('Remarks');
        
        $headers = array('No.','Time','Sales Date','Terminal Code','Outlet','OR No.','Customer Name','SKU Code','SKU Description','Qty','Price','Gross Sales','Type of Disc.','Discount Amount','Vat Deduct','Net Sales','Mode of Payment','Transaction Type','No. of Guest','Charges','Remarks');
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(20);
        $sheet->getColumnDimension('J')->setWidth(20);
        $sheet->getColumnDimension('K')->setWidth(20);
        $sheet->getColumnDimension('L')->setWidth(20);
        $sheet->getColumnDimension('M')->setWidth(20);
        $sheet->getColumnDimension('N')->setWidth(20);
        $sheet->getColumnDimension('O')->setWidth(20);
        $sheet->getColumnDimension('P')->setWidth(20);
        $sheet->getColumnDimension('Q')->setWidth(20);
        $sheet->getColumnDimension('R')->setWidth(20);
        $sheet->getColumnDimension('S')->setWidth(20);
        $sheet->getColumnDimension('T')->setWidth(20);
        $sheet->getColumnDimension('U')->setWidth(20);


        $sheet->mergeCells('A'.$rc.':I'.$rc);
        $sheet->getCell('A'.$rc)->setValue('Menu Hourly Sales Report');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTitle);
        $rc++;
        
        $dates = explode(" to ",$_GET['calendar_range']);
        $from = sql2DateTime($dates[0]);
        $to = sql2DateTime($dates[1]);
        $sheet->getCell('A'.$rc)->setValue('Date From: '.$from);
        $sheet->mergeCells('A'.$rc.':I'.$rc);
        $rc++;

        $sheet->getCell('A'.$rc)->setValue('Date To: '.$to);
        $sheet->mergeCells('A'.$rc.':I'.$rc);
        $rc++;

        $sheet->getCell('A'.$rc)->setValue('TO CHOOKS!');
        $sheet->mergeCells('A'.$rc.':I'.$rc);
        $rc++;
        $col = 'A';
        foreach ($headers as $txt) {
            $sheet->getCell($col.$rc)->setValue($txt);
            $sheet->getStyle($col.$rc)->applyFromArray($styleHeaderCell);
            $col++;
        }

        $rc++;
        
        $ctr = 1;
        foreach ($datas as $sid => $trans) {
            foreach ($trans as $res => $v) {
                // $this->make->sRow();
                // $datas[$v->sales_id][] = array(
                //     'datetime'=>$v->datetime,
                //     'terminal_code'=>'T00001',
                //     'outlet'=>'Chooks',
                //     'customer'=>'',
                //     'trans_ref'=>$v->trans_ref,
                //     'menu_code'=>$v->menu_code,
                //     'menu_name'=>$v->menu_name,
                //     'qty'=>$v->qty,
                //     'price'=>$v->mprice,
                //     'gross'=>$v->gross,
                //     'trans_type'=>$v->trans_type,
                //     'guest'=>$v->guest,
                //     'sales_id'=>$v->sales_id,
                //     'charges'=>$charges,
                //     'remarks'=>$remark,
                //     'line_id'=>$v->line_id,
                //     'discount'=>$discounts,
                //     'disc_line_id'=>$disc_line_id,
                //     'disc_name'=>$disc_name,
                //     'lessvat'=>$lessvat,
                //     'net_sales'=>$net_sales,
                //     'span_disc'=>$span_disc,
                //     'span_charge'=>$span_charge,
                // );


                $sheet->getCell('A'.$rc)->setValue($ctr);
                $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
                $sheet->getCell('B'.$rc)->setValue(date('h:i:s A',strtotime($v['datetime'])));
                $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
                $sheet->getCell('C'.$rc)->setValue(date('m/d/Y',strtotime($v['datetime'])));
                $sheet->getStyle('C'.$rc)->applyFromArray($styleTxt);
                $sheet->getCell('D'.$rc)->setValue($v['terminal_code']);
                $sheet->getStyle('D'.$rc)->applyFromArray($styleTxt);
                $sheet->getCell('E'.$rc)->setValue($v['outlet']);
                $sheet->getStyle('E'.$rc)->applyFromArray($styleTxt);
                $sheet->getCell('F'.$rc)->setValue('`'.$v['trans_ref']);
                $sheet->getStyle('F'.$rc)->applyFromArray($styleTxt);
                $sheet->getCell('G'.$rc)->setValue($v['customer']);
                $sheet->getStyle('G'.$rc)->applyFromArray($styleTxt);
                $sheet->getCell('H'.$rc)->setValue($v['menu_code']);
                $sheet->getStyle('H'.$rc)->applyFromArray($styleTxt);
                $sheet->getCell('I'.$rc)->setValue($v['menu_name']);
                $sheet->getStyle('I'.$rc)->applyFromArray($styleTxt);
                $sheet->getCell('J'.$rc)->setValue($v['qty']);
                $sheet->getStyle('J'.$rc)->applyFromArray($styleTxt);
                $sheet->getCell('K'.$rc)->setValue($v['gross']);
                $sheet->getStyle('K'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                $sheet->getCell('L'.$rc)->setValue($v['gross']);
                $sheet->getStyle('L'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

                if($v['span_disc'] == 'span'){
                    if($v['discount'] != 0){
                        //discount whole transaction
                        $rowspan = count($datas[$v['ter_sales_id']]);
                        // $this->make->td($v['disc_name'],array('rowspan'=>$rowspan,'style'=>'vertical-align:middle;'));
                        // $this->make->td(num($v['discount']),array('rowspan'=>$rowspan,'style'=>'vertical-align:middle;'));
                        // $this->make->td(num($v['lessvat']),array('rowspan'=>$rowspan,'style'=>'vertical-align:middle;'));
                        // $this->make->td(num($v['net_sales']),array('rowspan'=>$rowspan,'style'=>'vertical-align:middle;'));

                        $merge_ct = $rc + $rowspan - 1;

                        $sheet->mergeCells('M'.$rc.':M'.$merge_ct);
                        $sheet->getCell('M'.$rc)->setValue($v['disc_name']);
                        $sheet->getStyle('M'.$rc)->applyFromArray($styleTxtMerge);
                        $sheet->mergeCells('N'.$rc.':N'.$merge_ct);
                        $sheet->getCell('N'.$rc)->setValue($v['discount']);
                        $sheet->getStyle('N'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                        $sheet->mergeCells('O'.$rc.':O'.$merge_ct);
                        $sheet->getCell('O'.$rc)->setValue($v['lessvat']);
                        $sheet->getStyle('O'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                        $sheet->mergeCells('P'.$rc.':P'.$merge_ct);
                        $sheet->getCell('P'.$rc)->setValue($v['net_sales']);
                        $sheet->getStyle('P'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);


                    }
                   
                }else{
                    if($v['discount'] != 0){
                        // $this->make->td($v['disc_name']);
                        // $this->make->td(num($v['discount']));
                        // $this->make->td(num($v['lessvat']));
                        // $this->make->td(num($v['net_sales']));

                        $sheet->getCell('M'.$rc)->setValue($v['disc_name']);
                        $sheet->getStyle('M'.$rc)->applyFromArray($styleTxt);
                        $sheet->getCell('N'.$rc)->setValue($v['discount']);
                        $sheet->getStyle('N'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                        $sheet->getCell('O'.$rc)->setValue($v['lessvat']);
                        $sheet->getStyle('O'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                        $sheet->getCell('P'.$rc)->setValue($v['net_sales']);
                        $sheet->getStyle('P'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

                    }else{

                        // $this->make->td('');
                        // $this->make->td('0');
                        // $this->make->td('0');
                        // $this->make->td(num($v['gross']));

                        $sheet->getCell('M'.$rc)->setValue('');
                        $sheet->getStyle('M'.$rc)->applyFromArray($styleTxt);
                        $sheet->getCell('N'.$rc)->setValue(num(0));
                        $sheet->getStyle('N'.$rc)->applyFromArray($styleNum);
                        $sheet->getCell('O'.$rc)->setValue(num(0));
                        $sheet->getStyle('O'.$rc)->applyFromArray($styleNum);
                        $sheet->getCell('P'.$rc)->setValue($v['gross']);
                        $sheet->getStyle('P'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    }
                }

                $sheet->getCell('Q'.$rc)->setValue($v['payment_type']);
                $sheet->getStyle('Q'.$rc)->applyFromArray($styleTxt);
                $sheet->getCell('R'.$rc)->setValue($v['trans_type']);
                $sheet->getStyle('R'.$rc)->applyFromArray($styleTxt);
                $sheet->getCell('S'.$rc)->setValue($v['guest']);
                $sheet->getStyle('S'.$rc)->applyFromArray($styleTxt);

                if($v['span_charge'] == 'span'){
                    if($v['charges'] != 0){
                        //discount whole transaction
                        $rowspan = count($datas[$v['ter_sales_id']]);
                        // $this->make->td(num($v['charges']),array('rowspan'=>$rowspan,'style'=>'vertical-align:middle;'));
                        $merge_ct = $rc + $rowspan - 1;

                        $sheet->mergeCells('T'.$rc.':T'.$merge_ct);
                        $sheet->getCell('T'.$rc)->setValue($v['charges']);
                        $sheet->getStyle('T'.$rc)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    }
                }else{
                    $sheet->getCell('T'.$rc)->setValue(num(0));
                    $sheet->getStyle('T'.$rc)->applyFromArray($styleNum);
                }


                $sheet->getCell('U'.$rc)->setValue($v['remarks']);
                $sheet->getStyle('U'.$rc)->applyFromArray($styleTxt);

                $rc++;
                $ctr++;
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
        // ob_end_clean();
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $objWriter->save('php://output');
    }
    public function ejournal_rep(){
        $data = $this->syter->spawn('menu_sales_rep');
        $data['page_title'] = fa('icon-book-open')." BIR E-Sales Report";
        $data['code'] = esalesRep();
        $data['add_css'] = array('css/morris/morris.css','css/wowdash.css','css/datepicker/datepicker.css','css/daterangepicker/daterangepicker-bs3.css');
        $data['add_js'] = array('js/plugins/morris/morris.min.js','js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js');
        $data['page_no_padding'] = true;
        $data['sideBarHide'] = true;
        $data['load_js'] = 'dine/reporting';
        $data['use_js'] = 'eSalesRepJS';
        $this->load->view('page',$data);
    }

    public function get_esales_reports(){        
        ini_set('memory_limit', '-1');
        set_time_limit(3600);
        // sess_clear('month_array');
        // sess_clear('month_date');
        $this->load->helper('dine/reports_helper');
        // $month = $this->input->post('month');
        // $year = $this->input->post('year');
        // $json = $this->input->post('json');
        // $start_month = date($year.'-'.$month.'-01');
        // $end_month = date("Y-m-t", strtotime($start_month));
        // $month_date = array('text'=>sql2Date($start_month).' to '.sql2Date($end_month),'month_year'=>$start_month);
        update_load(10);

        $date = $this->input->post('calendar_range_2');
        // echo $date; die();
        // $user = $this->input->post('user');
        // $json = $this->input->post('json');

        $datesx = explode(" to ",$date);
        // $date_from = (empty($dates[0]) ? date('Y-m-d') : date('Y-m-d',strtotime($dates[0])));
        // $date_to = (empty($dates[1]) ? date('Y-m-d') : date('Y-m-d',strtotime($dates[1])));
        $start_month = $datesx[0];
        $end_month = $datesx[1];

        // sleep(1);
        $load = 10;
        // echo print_r($_POST);die();
        $branch_code = $this->input->post("branch_code");
        $terminal_id = $this->input->post("terminal_id");
        $details = $this->setup_model->get_branch_details($branch_code);
        // echo print_r($details);die();
        $open_time = $details[0]->store_open;
        $close_time = $details[0]->store_close;

        // $terminal_id = null;
        // if(CONSOLIDATOR){
        //     $terminal_id = $this->input->post("terminal_id");
        // }

        $this->make->sDiv();
            $this->make->sTable(array('class'=>'table striped-table mb-0'));
                $this->make->sTableHead();
                    $this->make->sRow(array('class'=>'table-header'));
                        $this->make->th('Date',array('class'=>'th'));
                        $this->make->th('Registerno',array('class'=>'th'));
                        $this->make->th('Invoice Start',array('class'=>'th'));
                        $this->make->th('Invoice End',array('class'=>'th'));
                        $this->make->th('Total Sales',array('class'=>'th'));
                        $this->make->th('Vatable Sales',array('class'=>'th'));
                        $this->make->th('Vat Exempt',array('class'=>'th'));
                        $this->make->th('Zero Rated',array('class'=>'th'));
                        $this->make->th('12% VAT',array('class'=>'th'));
                    $this->make->eRow();
                $this->make->eTableHead();



        $month_array = array();
        while (strtotime($start_month) <= strtotime($end_month)) {
            ini_set('memory_limit', '-1');
        set_time_limit(3600);
            // echo "$start_month\n";
            // $post = $this->set_post(null,$start_month);
            // echo "<pre>",print_r($post['args']),"</pre>";die();

            $pos_start = date2SqlDateTime($start_month." ".$open_time);
            $oa = date('a',strtotime($open_time));
            $ca = date('a',strtotime($close_time));
            $pos_end = date2SqlDateTime($start_month." ".$close_time);
            if($oa == $ca){
                $pos_end = date('Y-m-d H:i:s',strtotime($pos_end . "+1 days"));
            }

            // $this->cashier_model->db = $this->load->database('main', TRUE);
            $args = array();

            $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
            $args["trans_sales.inactive = 0"] = array('use'=>'where','val'=>null,'third'=>false);
            $args["trans_sales.datetime between '".$pos_start."' and '".$pos_end."'"] = array('use'=>'where','val'=>null,'third'=>false);
            // $args["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);
            if($terminal_id != null){
                    $args['trans_sales.pos_id'] = $terminal_id;
                }
            // if(CONSOLIDATOR){
            //     if($terminal_id != null){
            //         $args['trans_sales.terminal_id'] = $terminal_id;
            //     }
            // }else{
            //     $args['trans_sales.terminal_id'] = TERMINAL_ID;
            // }
            // echo print_r($args);die();
            $this->admin_model->set_temp_trans_sales($branch_code,$pos_start,$pos_end);

            $curr = false;
            $trans = $this->trans_sales($args,false,$branch_code);
            $sales = $trans['sales'];
            // echo "<pre>",print_r($sales['settled']['ids']),"</pre>";
            $trans_menus = $this->menu_sales($sales['settled']['ids'],$curr,$branch_code,$terminal_id);
            $trans_charges = $this->charges_sales($sales['settled']['ids'],$curr,$branch_code,$terminal_id);
            // $trans_discounts = $this->discounts_sales($sales['settled']['ids'],false,$terminal_id);
            $trans_discounts = $this->discounts_sales($sales['settled']['ids'],$curr,$branch_code,$terminal_id);
            $tax_disc = $trans_discounts['tax_disc_total'];
            $no_tax_disc = $trans_discounts['no_tax_disc_total'];
            $trans_local_tax = $this->local_tax_sales($sales['settled']['ids'],$curr,$branch_code,$terminal_id);
            $trans_tax = $this->tax_sales($sales['settled']['ids'],$curr,$branch_code,$terminal_id);
            $trans_no_tax = $this->no_tax_sales($sales['settled']['ids'],$curr,$branch_code,$terminal_id);
            $trans_zero_rated = $this->zero_rated_sales($sales['settled']['ids'],$curr,$branch_code,$terminal_id);
            // $payments = $this->payment_sales($sales['settled']['ids']);
            $gross = $trans_menus['gross'];

            
            $net = $trans['net'];
            $void = $trans['void'];
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
            $loc_txt = numInt(($local_tax));
            $net_no_adds = $net-($charges+$local_tax);
            // $nontaxable = $no_tax;
            $nontaxable = $no_tax - $no_tax_disc;
            // $taxable =   ($gross - $discounts - $less_vat - $nontaxable) / 1.12;
            $taxable = ($gross - $less_vat - $nontaxable - $zero_rated - $discounts) / 1.12;
            // echo "<pre>",print_r($taxable),"</pre>";die();
            $total_net = ($taxable) + ($nontaxable+$zero_rated) + $tax + $local_tax;
            $add_gt = $taxable+$nontaxable+$zero_rated;
            $nsss = $taxable +  $nontaxable +  $zero_rated;
            // $net_sales = $gross + $charges - $discounts - $less_vat;
            //pinapaalis ni sir yun charges sa net sales kagaya nun nasa zread 8 20 2018
            $net_sales = $gross - $discounts - $less_vat;
            // $final_gross = $gross;
            $vat_ = $taxable * .12;
            
            $pos_start = date2SqlDateTime($start_month." ".$open_time);
            $oa = date('a',strtotime($open_time));
            $ca = date('a',strtotime($close_time));
            $pos_end = date2SqlDateTime($start_month." ".$close_time);
            if($oa == $ca){
                $pos_end = date('Y-m-d H:i:s',strtotime($pos_end . "+1 days"));
            }

            // $gt = $this->old_grand_net_total($pos_start);

            $types = $trans_discounts['types'];
            // $qty = 0;
            $sndisc = 0;
            $pwdisc = 0;
            $othdisc = 0;
            foreach ($types as $code => $val) {
                $amount = $val['amount'];
                if($code == 'PWDISC'){
                    // $amount = $val['amount'] / 1.12;
                    $pwdisc = $val['amount'];
                }elseif($code == 'SNDISC'){
                    $sndisc = $val['amount'];
                }else{
                    $othdisc += $val['amount'];
                }
                // $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",PAPER_TOTAL_COL_1," ")
                //                      .append_chars('-'.Num($amount,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                // $qty += $val['qty'];
            }
            // echo $pwdisc; die();
            // $month_array[$start_month] = array(
            //     'cr_beg'=>iSetObj($trans['first_ref'],'trans_ref'),
            //     'cr_end'=>iSetObj($trans['last_ref'],'trans_ref'),
            //     'cr_count'=>$trans['ref_count'],
            //     'beg'=>$gt['old_grand_total'],
            //     'new'=>$gt['old_grand_total']+$net_no_adds,
            //     'ctr'=>$gt['ctr'],
            //     'vatsales'=>$taxable,
            //     'vatex'=>$nontaxable,
            //     'zero_rated'=>$zero_rated,
            //     'vat'=>$vat_,
            //     'net_sales'=>$net_sales,
            //     'pwdisc'=>$pwdisc,
            //     'sndisc'=>$sndisc,
            //     'othdisc'=>$othdisc,
            //     'lessvat'=>$less_vat,
            //     'gross'=>$gross,
            //     'charges'=>$charges,
            //     // 'senior'=>
            // );

            
                        $this->make->sTableBody();
                            // foreach ($menus as $res) {
                            $this->make->sRow(array('class'=>'table-data-row'));
                                $this->make->td(sql2Date($start_month),array('class'=>'table-data-name','style'=>'text-align:center;'));
                                $this->make->td('1',array('class'=>'table-data','style'=>'text-align:center;'));
                                $this->make->td(iSetObj($trans['first_ref'],'trans_ref'),array('class'=>'table-data','style'=>'text-align:center;'));
                                $this->make->td(iSetObj($trans['last_ref'],'trans_ref'),array('class'=>'table-data','style'=>'text-align:center;'));
                                $this->make->td(num($net_sales),array('class'=>'table-data','style'=>'text-align:center;'));
                                $this->make->td(num($taxable),array('class'=>'table-data','style'=>'text-align:center;'));
                                $this->make->td(num($nontaxable),array('class'=>'table-data','style'=>'text-align:center;'));
                                $this->make->td(num($zero_rated),array('class'=>'table-data','style'=>'text-align:center;'));
                                $this->make->td(num($vat_),array('class'=>'table-data','style'=>'text-align:center;'));
                                // $this->make->td($res['cost_price'] * $res['qty']);
                            $this->make->eRow();
                            // }    
                            // $this->make->sRow();
                            //     $this->make->th('');
                            //     $this->make->th('');
                            //     $this->make->th('');
                            //     $this->make->th('Total');
                            //     $this->make->th($total_qty);
                            //     $this->make->th('Total');
                            //     $this->make->th(num($menu_total));
                            //     $this->make->th('');
                            //     $this->make->th('');
                            //     $this->make->th('');
                            // $this->make->eRow();             
                        $this->make->eTableBody();

            $load += 2;
            update_load($load);
            // sleep(1);
            $start_month = date("Y-m-d", strtotime("+1 day", strtotime($start_month)));
        }
            $this->make->eTable();
        $this->make->eDiv();
        // var_dump($month_array);
        // die();
        // $trans_menus = $this->menu_sales($sales['settled']['ids'],$curr);
        // $trans_charges = $this->charges_sales($sales['settled']['ids'],$curr);
        update_load(90);
        // sleep(1);
        // $this->session->set_userdata('month_array',$month_array);
        // $this->session->set_userdata('month_date',$month_date);
        // //diretso excel na
        // $this->load->library('Excel');
        // $sheet = $this->excel->getActiveSheet();
        // $sheet->getCell('A1')->setValue('Point One Integrated Solutions Inc.');
        update_load(100);
        $code = $this->make->code();
        $json['code'] = $code;
        // $json['tbl_vals'] = $menus;
        // $json['dates'] = $this->input->post('calendar_range');
        echo json_encode($json);
        
    }

    public function e_sales_excel(){
        //diretso excel na
        ini_set('memory_limit', '-1');
        set_time_limit(3600);
        // sess_clear('month_array');
        // sess_clear('month_date');
        // $this->load->helper('dine/reports_helper');
        // $month = $this->input->post('month');
        // $year = $this->input->post('year');
        // $json = $this->input->post('json');
        // $start_month = date($year.'-'.$month.'-01');
        // $end_month = date("Y-m-t", strtotime($start_month));
        // $month_date = array('text'=>sql2Date($start_month).' to '.sql2Date($end_month),'month_year'=>$start_month);
        // update_load(10);
        // $date = $this->set_post($_GET['calendar_range']);
        $date = $this->input->get('calendar_range_2');
        // $date = $this->input->post('calendar_range_2');
        // echo $date; die();
        // $user = $this->input->post('user');
        // $json = $this->input->post('json');

        $datesx = explode(" to ",$date);
        // $date_from = (empty($dates[0]) ? date('Y-m-d') : date('Y-m-d',strtotime($dates[0])));
        // $date_to = (empty($dates[1]) ? date('Y-m-d') : date('Y-m-d',strtotime($dates[1])));
        $start_month = $datesx[0];
        $end_month = $datesx[1];

        // sleep(1);
        $load = 10;
        $branch_code = $this->input->get("branch_code");
        $details = $this->setup_model->get_branch_details($branch_code);
            
        $open_time = $details[0]->store_open;
        $close_time = $details[0]->store_close;

        $terminal_id = $this->input->get('terminal_id');
        // if(CONSOLIDATOR){
        //     $terminal_id = $this->input->get("terminal_id");
        // }
        
        $this->load->library('Excel');
        // $month_array = $this->session->userData('month_array');
        // $month_date = $this->session->userData('month_date');
        $sheet = $this->excel->getActiveSheet();
        $branch_details = $this->setup_model->get_branch_details();
        $branch = array();
        // echo "<pre>",print_r($month_array),"</pre>";die();
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
        $sheet->mergeCells('A1:D1');
        $sheet->getCell('A1')->setValue('BIR E-Sales Report');
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getCell('A2')->setValue('Print Datetime');
        $sheet->getCell('B2')->setValue(date('m/d/Y H:i:s A'));

        $sheet->mergeCells('E1:I1');
        $sheet->getCell('E1')->setValue('Condition Between');
        $sheet->mergeCells('E2:I2');
        $sheet->getCell('E2')->setValue($date);

        // $sheet->setCellValueExplicit('A3', 'TIN #'.$branch['tin'], PHPExcel_Cell_DataType::TYPE_STRING);
        // $sheet->setCellValueExplicit('A4', 'ACCRDN #'.$branch['accrdn'], PHPExcel_Cell_DataType::TYPE_STRING);
        // $sheet->setCellValueExplicit('A5', 'PERMIT #'.$branch['permit_no'], PHPExcel_Cell_DataType::TYPE_STRING);
        // $sheet->setCellValueExplicit('A6', 'SN #'.$branch['serial'], PHPExcel_Cell_DataType::TYPE_STRING);
        // $sheet->getCell('A7')->setValue($branch['machine_no']);
        // $sheet->setCellValueExplicit('A7', 'MIN #'.$branch['machine_no'], PHPExcel_Cell_DataType::TYPE_STRING);
        // $sheet->getCell('A8')->setValue('Monthly Sales Report');
        // $sheet->getCell('A9')->setValue($month_date['text']);
        $rn = 4;
        // $sheet->mergeCells('A10:A11');
        $sheet->getCell('A'.$rn)->setValue('Date');
        $sheet->getCell('B'.$rn)->setValue('Registerno');
        $sheet->getCell('C'.$rn)->setValue('Invoice Start');
        $sheet->getCell('D'.$rn)->setValue('Invoice End');
        $sheet->getCell('E'.$rn)->setValue('Total Sales');
        $sheet->getCell('F'.$rn)->setValue('Vatable Sales');
        $sheet->getCell('G'.$rn)->setValue('Vat Exempt Sales');
        $sheet->getCell('H'.$rn)->setValue('Zero Rated');
        $sheet->getCell('I'.$rn)->setValue('12% VAT');
        $sheet->getStyle("A".$rn.":I".$rn)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        // $sheet->getStyle('A'.$rn.':'.'I'.$rn)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        // $sheet->getStyle('A'.$rn.':'.'R11')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        // $sheet->getStyle('A'.$rn.':'.'R11')->getFill()->getStartColor()->setRGB('29bb04');
        $sheet->getStyle('A'.$rn.':I'.$rn)->getFont()->setBold(true);
        $rn = 5;
        $s_total_sales = $s_total_vs = $s_total_ve = $s_total_zr = $s_total_vat = 0;
        while (strtotime($start_month) <= strtotime($end_month)) {
            // echo "$start_month\n";
            // $post = $this->set_post(null,$start_month);
            // echo "<pre>",print_r($post['args']),"</pre>";die();

            $pos_start = date2SqlDateTime($start_month." ".$open_time);
            $oa = date('a',strtotime($open_time));
            $ca = date('a',strtotime($close_time));
            $pos_end = date2SqlDateTime($start_month." ".$close_time);
            if($oa == $ca){
                $pos_end = date('Y-m-d H:i:s',strtotime($pos_end . "+1 days"));
            }

            // $this->cashier_model->db = $this->load->database('main', TRUE);
            $args = array();

            $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
            $args["trans_sales.inactive = 0"] = array('use'=>'where','val'=>null,'third'=>false);
            $args["trans_sales.datetime between '".$pos_start."' and '".$pos_end."'"] = array('use'=>'where','val'=>null,'third'=>false);
            // $args["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);

            // if(CONSOLIDATOR){
                if($terminal_id != null){
                    $args['trans_sales.pos_id'] = $terminal_id;
                }
            // }else{
            //     $args['trans_sales.terminal_id'] = TERMINAL_ID;
            // }


            $curr = false;

            $this->admin_model->set_temp_trans_sales($branch_code,$pos_start,$pos_end);

            $trans = $this->trans_sales($args,false,$branch_code);
            $sales = $trans['sales'];
            // echo "<pre>",print_r($sales['settled']['ids']),"</pre>";
            $trans_menus = $this->menu_sales($sales['settled']['ids'],$curr,$branch_code);
            $trans_charges = $this->charges_sales($sales['settled']['ids'],$curr,$branch_code);
            // $trans_discounts = $this->discounts_sales($sales['settled']['ids'],false,$terminal_id);
            $trans_discounts = $this->discounts_sales($sales['settled']['ids'],$curr,$branch_code);
            $tax_disc = $trans_discounts['tax_disc_total'];
            $no_tax_disc = $trans_discounts['no_tax_disc_total'];
            $trans_local_tax = $this->local_tax_sales($sales['settled']['ids'],$curr,$branch_code);
            $trans_tax = $this->tax_sales($sales['settled']['ids'],$curr,$branch_code);
            $trans_no_tax = $this->no_tax_sales($sales['settled']['ids'],$curr,$branch_code);
            $trans_zero_rated = $this->zero_rated_sales($sales['settled']['ids'],$curr,$branch_code);
            // $payments = $this->payment_sales($sales['settled']['ids']);
            $gross = $trans_menus['gross'];
            
            $net = $trans['net'];
            $void = $trans['void'];
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
            $loc_txt = numInt(($local_tax));
            $net_no_adds = $net-($charges+$local_tax);
            // $nontaxable = $no_tax;
            $nontaxable = $no_tax - $no_tax_disc;
            // $taxable =   ($gross - $discounts - $less_vat - $nontaxable) / 1.12;
            $taxable = ($gross - $less_vat - $nontaxable - $zero_rated - $discounts) / 1.12;
            $total_net = ($taxable) + ($nontaxable+$zero_rated) + $tax + $local_tax;
            $add_gt = $taxable+$nontaxable+$zero_rated;
            $nsss = $taxable +  $nontaxable +  $zero_rated;
            // $net_sales = $gross + $charges - $discounts - $less_vat;
            //pinapaalis ni sir yun charges sa net sales kagaya nun nasa zread 8 20 2018
            $net_sales = $gross - $discounts - $less_vat;
            // $final_gross = $gross;
            $vat_ = $taxable * .12;
            
            $pos_start = date2SqlDateTime($start_month." ".$open_time);
            $oa = date('a',strtotime($open_time));
            $ca = date('a',strtotime($close_time));
            $pos_end = date2SqlDateTime($start_month." ".$close_time);
            if($oa == $ca){
                $pos_end = date('Y-m-d H:i:s',strtotime($pos_end . "+1 days"));
            }

            // $gt = $this->old_grand_net_total($pos_start);

            $types = $trans_discounts['types'];
            // $qty = 0;
            $sndisc = 0;
            $pwdisc = 0;
            $othdisc = 0;
            foreach ($types as $code => $val) {
                $amount = $val['amount'];
                if($code == 'PWDISC'){
                    // $amount = $val['amount'] / 1.12;
                    $pwdisc = $val['amount'];
                }elseif($code == 'SNDISC'){
                    $sndisc = $val['amount'];
                }else{
                    $othdisc += $val['amount'];
                }
                // $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",PAPER_TOTAL_COL_1," ")
                //                      .append_chars('-'.Num($amount,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                // $qty += $val['qty'];
            }
            // echo $pwdisc; die();
            // $month_array[$start_month] = array(
            //     'cr_beg'=>iSetObj($trans['first_ref'],'trans_ref'),
            //     'cr_end'=>iSetObj($trans['last_ref'],'trans_ref'),
            //     'cr_count'=>$trans['ref_count'],
            //     'beg'=>$gt['old_grand_total'],
            //     'new'=>$gt['old_grand_total']+$net_no_adds,
            //     'ctr'=>$gt['ctr'],
            //     'vatsales'=>$taxable,
            //     'vatex'=>$nontaxable,
            //     'zero_rated'=>$zero_rated,
            //     'vat'=>$vat_,
            //     'net_sales'=>$net_sales,
            //     'pwdisc'=>$pwdisc,
            //     'sndisc'=>$sndisc,
            //     'othdisc'=>$othdisc,
            //     'lessvat'=>$less_vat,
            //     'gross'=>$gross,
            //     'charges'=>$charges,
            //     // 'senior'=>
            // );

            $sheet->getCell('A'.$rn)->setValue(sql2Date($start_month));
            $sheet->getCell('B'.$rn)->setValue(1);
            $sheet->getCell('C'.$rn)->setValue(iSetObj($trans['first_ref'],'trans_ref'));
            $sheet->getCell('D'.$rn)->setValue(iSetObj($trans['last_ref'],'trans_ref'));
            $sheet->getStyle('E'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('E'.$rn)->setValue($net_sales);
            $sheet->getStyle('F'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('F'.$rn)->setValue($taxable);
            $sheet->getStyle('G'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('G'.$rn)->setValue($nontaxable);
            $sheet->getStyle('H'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('H'.$rn)->setValue($zero_rated);
            $sheet->getStyle('I'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getCell('I'.$rn)->setValue($vat_);

            $s_total_sales += $net_sales;
            $s_total_vs += $taxable;
            $s_total_ve += $nontaxable; 
            $s_total_zr += $zero_rated; 
            $s_total_vat += $vat_;
                            

            $load += 2;
            $rn++;
            update_load($load);
            // sleep(1);
            $start_month = date("Y-m-d", strtotime("+1 day", strtotime($start_month)));
        }

        $rn++;
        $sheet->getStyle('A'.$rn.':I'.$rn)->getFont()->setBold(true);
        $sheet->getCell('D'.$rn)->setValue('Summary');
        $sheet->getStyle('E'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->getCell('E'.$rn)->setValue($s_total_sales);
        $sheet->getStyle('F'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->getCell('F'.$rn)->setValue($s_total_vs);
        $sheet->getStyle('G'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->getCell('G'.$rn)->setValue($s_total_ve);
        $sheet->getStyle('H'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->getCell('H'.$rn)->setValue($s_total_zr);
        $sheet->getStyle('I'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->getCell('I'.$rn)->setValue($s_total_vat);

        $rn++;
        $rn++;
        $sheet->getStyle('A'.$rn.':I'.$rn)->getFont()->setBold(true);
        $sheet->getCell('D'.$rn)->setValue('Grand Total');
        $sheet->getStyle('E'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->getCell('E'.$rn)->setValue($s_total_sales);
        $sheet->getStyle('F'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->getCell('F'.$rn)->setValue($s_total_vs);
        $sheet->getStyle('G'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->getCell('G'.$rn)->setValue($s_total_ve);
        $sheet->getStyle('H'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->getCell('H'.$rn)->setValue($s_total_zr);
        $sheet->getStyle('I'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->getCell('I'.$rn)->setValue($s_total_vat);

        if (ob_get_contents()) 
            ob_end_clean();
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename=esales.xls');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $objWriter->save('php://output');
    }
    public function esales_pdf(){
        //diretso excel na
        ini_set('memory_limit', '-1');
        set_time_limit(3600);
        

        require_once( APPPATH .'third_party/tcpdf.php');
        $this->load->model("dine/setup_model");
        date_default_timezone_set('Asia/Manila');

        // create new PDF document
        $pdf = new TCPDF("L", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('iPOS');
        $pdf->SetTitle('E-Sales Report');
        $pdf->SetSubject('');
        $pdf->SetKeywords('');

        // set default header data
        // $setup = $this->setup_model->get_details(1);
        // $set = $setup[0];
        // $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $set->branch_name, $set->address);

        // // set header and footer fonts
        // $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        // $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // // set default monospaced font
        // $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // // set margins
        // $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        // $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        // $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // // set auto page breaks
        // $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // // set image scale factor
        // $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // // set some language-dependent strings (optional)
        // if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
        //     require_once(dirname(__FILE__).'/lang/eng.php');
        //     $pdf->setLanguageArray($l);
        // }


        // update_load(10);
        // $date = $this->set_post($_GET['calendar_range']);
        $date = $this->input->get('calendar_range_2');
        $branch_code = $this->input->get("branch_code");
        // $date = $this->input->post('calendar_range_2');
        // echo $date; die();
        // $user = $this->input->post('user');
        // $json = $this->input->post('json');

        $datesx = explode(" to ",$date);
        // $date_from = (empty($dates[0]) ? date('Y-m-d') : date('Y-m-d',strtotime($dates[0])));
        // $date_to = (empty($dates[1]) ? date('Y-m-d') : date('Y-m-d',strtotime($dates[1])));
        $start_month = $datesx[0];
        $end_month = $datesx[1];


        $details = $this->setup_model->get_branch_details($branch_code);
            
        $open_time = $details[0]->store_open;
        $close_time = $details[0]->store_close;

        $terminal_id = $this->input->get("terminal_id");
        // if(CONSOLIDATOR){
        //     $terminal_id = $this->input->get("terminal_id");
        // }

        // set font
        $pdf->SetFont('helvetica', 'B', 12);

        // add a page
        $pdf->AddPage();

        $pdf->ln(2);
        $pdf->SetFont('helvetica', 'B', 27);
        $pdf->cell(150,0,'BIR E-Sales Report',0,0,'L');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->cell(100,0,'Condition Between '.$date,0,0,'L');
        $pdf->ln(11);
        $pdf->cell(150,0,'Print Datetime '.date('m/d/Y H:i:s A'),0,0,'L');

        $pdf->ln(13);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->cell(30,0,'Date','B',0,'L');
        $pdf->cell(30,0,'RegisterNo','B',0,'L');
        $pdf->cell(30,0,'Invoice Start','B',0,'R');
        $pdf->cell(30,0,'Invoice End','B',0,'C');
        $pdf->cell(30,0,'Total Sales','B',0,'R');
        $pdf->cell(30,0,'Vatable Sales','B',0,'R');
        $pdf->cell(30,0,'Vat Exempt','B',0,'R');
        $pdf->cell(30,0,'Zero Rated','B',0,'R');
        $pdf->cell(30,0,'12% VAT','B',0,'R');
        $pdf->ln(6);
        $pdf->cell(45,0,'',0,0,'L');
        $pdf->cell(30,0,'RegisterNo : 1',0,0,'L');
        $pdf->ln(6);

        $s_total_sales = $s_total_vs = $s_total_ve = $s_total_zr = $s_total_vat = 0;
        while (strtotime($start_month) <= strtotime($end_month)) {
            // echo "$start_month\n";
            // $post = $this->set_post(null,$start_month);
            // echo "<pre>",print_r($post['args']),"</pre>";die();
            $pos_start = date2SqlDateTime($start_month." ".$open_time);
            $oa = date('a',strtotime($open_time));
            $ca = date('a',strtotime($close_time));
            $pos_end = date2SqlDateTime($start_month." ".$close_time);
            if($oa == $ca){
                $pos_end = date('Y-m-d H:i:s',strtotime($pos_end . "+1 days"));
            }

            // $this->cashier_model->db = $this->load->database('main', TRUE);
            $args = array();

            $args["trans_sales.trans_ref  IS NOT NULL"] = array('use'=>'where','val'=>null,'third'=>false);
            $args["trans_sales.inactive = 0"] = array('use'=>'where','val'=>null,'third'=>false);
            $args["trans_sales.datetime between '".$pos_start."' and '".$pos_end."'"] = array('use'=>'where','val'=>null,'third'=>false);
            // $args["trans_sales.datetime between '".$from."' and '".$to."'"] = array('use'=>'where','val'=>null,'third'=>false);

            // if(CONSOLIDATOR){
                if($terminal_id != null){
                    $args['trans_sales.pos_id'] = $terminal_id;
                }
            // }else{
            //     $args['trans_sales.terminal_id'] = TERMINAL_ID;
            // }


            $curr = false;

            $this->admin_model->set_temp_trans_sales($branch_code,$pos_start,$pos_end);

            $trans = $this->trans_sales($args,false,$branch_code);
            $sales = $trans['sales'];
            // echo "<pre>",print_r($sales['settled']['ids']),"</pre>";
            $trans_menus = $this->menu_sales($sales['settled']['ids'],$curr,$branch_code,$terminal_id);
            $trans_charges = $this->charges_sales($sales['settled']['ids'],$curr,$branch_code,$terminal_id);
            // $trans_discounts = $this->discounts_sales($sales['settled']['ids'],false,$terminal_id);
            $trans_discounts = $this->discounts_sales($sales['settled']['ids'],$curr,$branch_code,$terminal_id);
            $tax_disc = $trans_discounts['tax_disc_total'];
            $no_tax_disc = $trans_discounts['no_tax_disc_total'];
            $trans_local_tax = $this->local_tax_sales($sales['settled']['ids'],$curr,$branch_code,$terminal_id);
            $trans_tax = $this->tax_sales($sales['settled']['ids'],$curr,$branch_code,$terminal_id);
            $trans_no_tax = $this->no_tax_sales($sales['settled']['ids'],$curr,$branch_code,$terminal_id);
            $trans_zero_rated = $this->zero_rated_sales($sales['settled']['ids'],$curr,$branch_code,$terminal_id);
            // $payments = $this->payment_sales($sales['settled']['ids']);
            $gross = $trans_menus['gross'];
            
            $net = $trans['net'];
            $void = $trans['void'];
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
            $loc_txt = numInt(($local_tax));
            $net_no_adds = $net-($charges+$local_tax);
            // $nontaxable = $no_tax;
            $nontaxable = $no_tax - $no_tax_disc;
            // $taxable =   ($gross - $discounts - $less_vat - $nontaxable) / 1.12;
            $taxable = ($gross - $less_vat - $nontaxable - $zero_rated - $discounts) / 1.12;
            $total_net = ($taxable) + ($nontaxable+$zero_rated) + $tax + $local_tax;
            $add_gt = $taxable+$nontaxable+$zero_rated;
            $nsss = $taxable +  $nontaxable +  $zero_rated;
            // $net_sales = $gross + $charges - $discounts - $less_vat;
            //pinapaalis ni sir yun charges sa net sales kagaya nun nasa zread 8 20 2018
            $net_sales = $gross - $discounts - $less_vat;
            // $final_gross = $gross;
            $vat_ = $taxable * .12;
            
            $pos_start = date2SqlDateTime($start_month." ".$open_time);
            $oa = date('a',strtotime($open_time));
            $ca = date('a',strtotime($close_time));
            $pos_end = date2SqlDateTime($start_month." ".$close_time);
            if($oa == $ca){
                $pos_end = date('Y-m-d H:i:s',strtotime($pos_end . "+1 days"));
            }

            // $gt = $this->old_grand_net_total($pos_start);

            $types = $trans_discounts['types'];
            // $qty = 0;
            $sndisc = 0;
            $pwdisc = 0;
            $othdisc = 0;
            foreach ($types as $code => $val) {
                $amount = $val['amount'];
                if($code == 'PWDISC'){
                    // $amount = $val['amount'] / 1.12;
                    $pwdisc = $val['amount'];
                }elseif($code == 'SNDISC'){
                    $sndisc = $val['amount'];
                }else{
                    $othdisc += $val['amount'];
                }
                // $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",PAPER_TOTAL_COL_1," ")
                //                      .append_chars('-'.Num($amount,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                // $qty += $val['qty'];
            }
            
            // $pdf->ln(25);
            $pdf->SetFont('helvetica', '', 9);
            $pdf->cell(30,0,sql2Date($start_month),0,0,'L');
            $pdf->cell(30,0,'1',0,0,'L');
            $pdf->cell(30,0,iSetObj($trans['first_ref'],'trans_ref'),0,0,'R');
            $pdf->cell(30,0,iSetObj($trans['last_ref'],'trans_ref'),0,0,'C');
            $pdf->cell(30,0,num($net_sales),0,0,'R');
            $pdf->cell(30,0,num($taxable),0,0,'R');
            $pdf->cell(30,0,num($nontaxable),0,0,'R');
            $pdf->cell(30,0,num($zero_rated),0,0,'R');
            $pdf->cell(30,0,num($vat_),0,0,'R');


            // $sheet->getCell('A'.$rn)->setValue($start_month);
            // $sheet->getCell('B'.$rn)->setValue(1);
            // $sheet->getCell('C'.$rn)->setValue(iSetObj($trans['first_ref'],'trans_ref'));
            // $sheet->getCell('D'.$rn)->setValue(iSetObj($trans['last_ref'],'trans_ref'));
            // $sheet->getStyle('E'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            // $sheet->getCell('E'.$rn)->setValue($net_sales);
            // $sheet->getStyle('F'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            // $sheet->getCell('F'.$rn)->setValue($taxable);
            // $sheet->getStyle('G'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            // $sheet->getCell('G'.$rn)->setValue($nontaxable);
            // $sheet->getStyle('H'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            // $sheet->getCell('H'.$rn)->setValue($zero_rated);
            // $sheet->getStyle('I'.$rn)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            // $sheet->getCell('I'.$rn)->setValue($vat_);

            $s_total_sales += $net_sales;
            $s_total_vs += $taxable;
            $s_total_ve += $nontaxable; 
            $s_total_zr += $zero_rated; 
            $s_total_vat += $vat_;
                            

            // $load += 2;
            // $rn++;
            // update_load($load);
            // sleep(1);
            $pdf->ln(6);
            $start_month = date("Y-m-d", strtotime("+1 day", strtotime($start_month)));
        }

        $pdf->ln(5);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->cell(30,0,'',0,0,'L');
        $pdf->cell(30,0,'',0,0,'L');
        $pdf->cell(30,0,'',0,0,'R');
        $pdf->cell(30,0,'Summary',0,0,'C');
        $pdf->cell(30,0,num($s_total_sales),0,0,'R');
        $pdf->cell(30,0,num($s_total_vs),0,0,'R');
        $pdf->cell(30,0,num($s_total_ve),0,0,'R');
        $pdf->cell(30,0,num($s_total_zr),0,0,'R');
        $pdf->cell(30,0,num($s_total_vat),0,0,'R');

        $pdf->ln(10);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->cell(30,0,'',0,0,'L');
        $pdf->cell(30,0,'',0,0,'L');
        $pdf->cell(30,0,'',0,0,'R');
        $pdf->cell(30,0,'Grand Total',0,0,'C');
        $pdf->cell(30,0,num($s_total_sales),0,0,'R');
        $pdf->cell(30,0,num($s_total_vs),0,0,'R');
        $pdf->cell(30,0,num($s_total_ve),0,0,'R');
        $pdf->cell(30,0,num($s_total_zr),0,0,'R');
        $pdf->cell(30,0,num($s_total_vat),0,0,'R');

        //Close and output PDF document
        // ob_end_clean();
        $pdf->Output('esales_report.pdf', 'I');

        //============================================================+
        // END OF FILE
        //============================================================+          
    }

    public function transite_rep(){
        $this->load->model('dine/reports_model');
        $this->load->helper('dine/reports_helper');
        $data = $this->syter->spawn('transite');
        $data['page_title'] = 'Transight POS Report';
        $data['code'] = transiteRepPage();
        $data['add_css'] = array('css/datepicker/datepicker.css','css/wowdash.css','css/daterangepicker/daterangepicker-bs3.css');
        $data['add_js'] = array('js/plugins/datepicker/bootstrap-datepicker.js','js/plugins/daterangepicker/daterangepicker.js');
        $data['load_js'] = 'dine/reporting.php';
        $data['use_js'] = 'transiteRepJs';
        $this->load->view('page',$data);
    }

    public function transite_rep_gen($asJson=false){
        ////hapchan
            ini_set('memory_limit', '-1');
            set_time_limit(3600);
            
            $print_str = $this->print_header();
            $user = $this->session->userdata('user');
            $time = $this->site_model->get_db_now();
            $trans_date = date('Y-m-d',strtotime($this->input->post('calendar')));
            $branch_code = $this->input->post('branch_id');
            $terminal = $this->input->post('terminal');
            $employee = $this->input->post('employee');
            // var_dump($post); die();
            $sales = $this->site_model->get_tbl('transite_sales',array('trans_date'=>$trans_date,'branch_code'=>$branch_code));

            if(!$sales){
                echo json_encode(array('code'=>'NO AVAILABLE RECORD'));
                exit;
            }

            $sales_discounts = $this->site_model->get_tbl('transite_sales_discounts',array('transite_id'=>$sales[0]->transite_id));
            $sales_menus = $this->site_model->get_tbl('transite_sales_menus',array('transite_id'=>$sales[0]->transite_id));
            $sales_menu_modifiers = $this->site_model->get_tbl('transite_sales_menu_modifiers',array('transite_id'=>$sales[0]->transite_id));
            $sales_payments = $this->site_model->get_tbl('transite_sales_payments',array('transite_id'=>$sales[0]->transite_id));
            
            $title_name = "Transight POS SALES REPORT";
            // if($post['title'] != "")
                // $title_name = $post['title'];

            $print_str .= align_center($title_name,PAPER_WIDTH," ")."\r\n";
            $print_str .= align_center("TERMINAL ".$terminal,PAPER_WIDTH," ")."\r\n";
            $print_str .= append_chars('Printed On','right',11," ").append_chars(": ".date2SqlDateTime($time),'right',19," ")."\r\n";
            $print_str .= append_chars('Printed BY','right',11," ").append_chars(": ".$user['full_name'],'right',19," ")."\r\n";
            $print_str .= PAPER_LINE."\r\n";
            $print_str .= align_center(sql2DateTime($trans_date),PAPER_WIDTH," ")."\r\n";
            if($employee != "All")
                $print_str .= align_center($employee,PAPER_WIDTH," ")."\r\n";
            $print_str .= PAPER_LINE."\r\n";

            

            #GENERAL
            $print_str .= append_chars(substrwords('TOTAL SALES',18,""),"right",21," ")
                                     .append_chars(num($sales[0]->gross_sales,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            $print_str .= append_chars(substrwords(ucwords(strtoupper('SC/PWD VAT EXEMPT')),18,""),"right",PAPER_TOTAL_COL_1," ")
                                     .append_chars('-'.num($sales[0]->vat_exempt,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
            $print_str .= append_chars('',"right",12," ").align_center('',PAPER_TOTAL_COL_2," ")
                              .append_chars('----------',"left",PAPER_TOTAL_COL_2," ")."\r\n";

            $print_str .= append_chars(substrwords('GROSS SALES',18,""),"right",21," ")
                                         .append_chars(num($sales[0]->gross_sales  - $sales[0]->vat_exempt,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";

                // $types = $trans_charges['types'];
                // $qty = 0;
                // foreach ($types as $code => $val) {
                //     $amount = $val['amount'];
                //     $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",PAPER_TOTAL_COL_1," ")
                //                          .append_chars('-'.num($amount,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                //     $qty += $val['qty'];
                // }
                $types = $sales_discounts;
                $qty = 0;
                foreach ($types as $code => $val) {
                    if($val->name != 'DIPLOMAT'){
                        $amount = $val->total_amount;
                        // if(MALL == 'megamall' && $code == PWDDISC){
                        //     $amount = $val['amount'] / 1.12;
                        // }
                        $print_str .= append_chars(substrwords(ucwords(strtolower($val->name)),18,""),"right",21," ")
                                             .append_chars('-'.Num($amount,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                        $qty += $val->qty;
                    }
                }
                $print_str .= append_chars('',"right",12," ").align_center('',PAPER_TOTAL_COL_2," ")
                                  .append_chars('----------',"left",PAPER_TOTAL_COL_2," ")."\r\n";
                
                $print_str .= append_chars(substrwords(ucwords(strtoupper('NET SALES')),18,""),"right",21," ")
                                         .append_chars(num($sales[0]->net_sales,2),"left",PAPER_TOTAL_COL_2," ")."\r\n\r\n";
            #PAYMENTS
                
                $pay_qty = 0;
            #SUMMARY
                $print_str .= append_chars(substrwords('VAT SALES',23,""),"right",21," ")
                                         .append_chars(num($sales[0]->vat_sales,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords('VAT',23,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(num($sales[0]->vat,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                // if(IS_VATABLE_STORE){
                    $print_str .= append_chars(substrwords('VAT EXEMPT SALES',23,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(num($sales[0]->vat_exempt_sales,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";

                // }else{
                //     $print_str .= append_chars(substrwords('NONVAT SALES',23,""),"right",PAPER_TOTAL_COL_1," ")
                //                          .append_chars(num($nontaxable,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                // }
                                         // .append_chars(numInt($nontaxable-$zero_rated),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords('ZERO RATED',23,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(num(0,2),"left",PAPER_TOTAL_COL_2," ")."\r\n\r\n";
                $print_str .= append_chars(substrwords('Payment Breakdown:',18,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
                              .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";

                $payments_total = 0;
                foreach ($sales_payments as $code => $val) {
                    $print_str .= append_chars(substrwords(ucwords(strtoupper($val->code)),18,""),"right",12," ").align_center($val->qty,PAPER_RD_COL_2," ")
                                  .append_chars(num($val->total_amount,2),"left",16," ")."\r\n";
                    $pay_qty += $val->qty;
                    $payments_total += $val->total_amount;
                }
                $print_str .= append_chars('',"right",18," ").align_center('',PAPER_RD_COL_2," ")
                                  .append_chars('----------',"left",PAPER_RD_COL_3_3," ")."\r\n";
                $print_str .= append_chars(substrwords('TOTAL PAYMENTS',18,""),"right",14," ").align_center($pay_qty,PAPER_RD_COL_2," ")
                              .append_chars(num($payments_total,2),"left",14," ")."\r\n\r\n";
                $print_str .= PAPER_LINE_SINGLE."\r\n";
                

                $print_str .= "\r\n\r\n";



                // $print_str .= append_chars(substrwords('VOID SALES',18,""),"right",PAPER_TOTAL_COL_1," ")
                //              .append_chars(num(($void),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                // $print_str .= append_chars(substrwords('CANCELLED TRANS',18,""),"right",PAPER_TOTAL_COL_1," ")
                //              .append_chars(num(($cancelled),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";

                $co = 0;
                $print_str .= append_chars(substrwords('CANCELLED ORDERS',18,""),"right",PAPER_TOTAL_COL_1," ")
                             .append_chars(num(($co),2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= append_chars(substrwords('Local Tax',18,""),"right",PAPER_TOTAL_COL_1," ")
                             .append_chars(0,"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= "\r\n";
            #TRANS COUNT
                // $types = $trans['types'];
                // $types_total = array();
                // $guestCount = 0;
                // foreach ($types as $type => $tp) {
                //     foreach ($tp as $id => $opt){
                //         if(isset($types_total[$type])){
                //             $types_total[$type] += round($opt->total_amount,2);

                //         }
                //         else{
                //             $types_total[$type] = round($opt->total_amount,2);
                //         }

                //         if($opt->guest == 0)
                //             $guestCount += 1;
                //         else
                //             $guestCount += $opt->guest;
                //     }
                // }
                // $print_str .= append_chars(substrwords('Trans Count:',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                //              .append_chars('',"left",PAPER_RD_COL_3_3," ")."\r\n";
                // $tc_total  = 0;
                // $tc_qty = 0;
                // foreach ($types_total as $typ => $tamnt) {
                //     $print_str .= append_chars(substrwords($typ,18,""),"right",12," ").align_center(count($types[$typ]),PAPER_RD_COL_2," ")
                //                  .append_chars(num($tamnt,2),"left",16," ")."\r\n";
                //     $tc_total += $tamnt;
                //     $tc_qty += count($types[$typ]);
                // }
                // $print_str .= "-----------------"."\r\n";
                // $print_str .= append_chars(substrwords('TC Total',18,""),"right",21," ")
                //              .append_chars(num($tc_total,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                // $print_str .= append_chars(substrwords('GUEST Total',18,""),"right",PAPER_TOTAL_COL_1," ")
                //              .append_chars($guestCount,"left",PAPER_TOTAL_COL_2," ")."\r\n";
               
                // if($net_sales){
                //     if($guestCount == 0){
                //         $avg = 0;
                //     }else{
                //         $avg = $net_sales/$guestCount;
                //     }
                // }else{
                //     $avg = 0;
                // }


                // $print_str .= append_chars(substrwords('AVG Check',18,""),"right",PAPER_TOTAL_COL_1," ")
                //              .append_chars(num($avg,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                // $print_str .= "\r\n";
            #CHARGES
                // $types = $trans_charges['types'];
                // $qty = 0;
                // $print_str .= append_chars(substrwords('Charges:',18,""),"right",18," ").align_center(null,5," ")
                //               .append_chars(null,"left",13," ")."\r\n";
                // foreach ($types as $code => $val) {
                //     $print_str .= append_chars(substrwords(ucwords(strtolower($val['name'])),18,""),"right",PAPER_RD_COL_1," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                //                   .append_chars(num($val['amount'],2),"left",PAPER_RD_COL_3_3," ")."\r\n";
                //     $qty += $val['qty'];
                // }
                // $print_str .= "-----------------"."\r\n";
                // $print_str .= append_chars(substrwords('Total Charges',18,""),"right",PAPER_RD_COL_1," ").align_center($qty,PAPER_RD_COL_2," ")
                //               .append_chars(num($charges,2),"left",PAPER_RD_COL_3_3," ")."\r\n";
                // $print_str .= "\r\n";
            #Discounts
                $types = $sales_discounts;
                $qty = 0;
                $print_str .= append_chars(substrwords('Discounts:',18,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
                              .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
                $total_discount = 0;

                foreach ($types as $code => $val) {
                    if($val->name != 'DIPLOMAT'){
                        $amount = $val->total_amount;
                        // if(MALL == 'megamall' && $code == PWDDISC){
                        //     $amount = $val['amount'] / 1.12;
                        // }
                        $print_str .= append_chars(substrwords(ucwords(strtolower($val->name)),18,""),"right",14," ").align_center($val->qty,PAPER_RD_COL_2," ")
                                      .append_chars(num($amount,2),"left",14," ")."\r\n";
                        $qty += $val->qty;
                        $total_discount += $val->total_amount;
                    }
                }
                $print_str .= "-----------------"."\r\n";
                $print_str .= append_chars(substrwords('Total Discounts',18,""),"right",14," ").align_center($qty,PAPER_RD_COL_2," ")
                              .append_chars(num($total_discount,2),"left",13," ")."\r\n";
                $print_str .= append_chars(substrwords('VAT EXEMPT',18,""),"right",PAPER_TOTAL_COL_1," ")
                                         .append_chars(num($sales[0]->vat_exempt,2),"left",PAPER_TOTAL_COL_2," ")."\r\n";
                $print_str .= "\r\n";
            #PAYMENTS
                $pay_qty = 0;
                $payments_total = 0;
                $print_str .= append_chars(substrwords('Payment Breakdown:',18,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
                              .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
                foreach ($sales_payments as $code => $val) {
                    $print_str .= append_chars(substrwords(ucwords(strtolower($val->code)),18,""),"right",12," ").align_center($val->qty,PAPER_RD_COL_2," ")
                                  .append_chars(num($val->total_amount,2),"left",16," ")."\r\n";
                    $pay_qty += $val->qty;
                    $payments_total += $val->total_amount;

                }
                $print_str .= "-----------------"."\r\n";
                $print_str .= append_chars(substrwords('Total Payments',18,""),"right",14," ").align_center($pay_qty,PAPER_RD_COL_2," ")
                              .append_chars(num($payments_total,2),"left",14," ")."\r\n";
                $print_str .= "\r\n";

                //card breakdown
                // if($payments['cards']){
                //     $cards = $payments['cards'];
                //     $card_total = 0;
                //     $count_total = 0;
                //     $print_str .= append_chars(substrwords('Card Breakdown:',18,""),"right",PAPER_RD_COL_1," ").align_center(null,PAPER_RD_COL_2," ")
                //               .append_chars(null,"left",PAPER_RD_COL_3," ")."\r\n";
                //     foreach($cards as $key => $val){
                //         $print_str .= append_chars(substrwords($key,18,""),"right",12," ").align_center($val['count'],PAPER_RD_COL_2," ")
                //                   .append_chars(num($val['amount'],2),"left",16," ")."\r\n";
                //         $card_total += $val['amount'];
                //         $count_total += $val['count'];
                //     }
                //     $print_str .= "-----------------"."\r\n";
                //     $print_str .= append_chars(substrwords('Total',18,""),"right",12," ").align_center($count_total,PAPER_RD_COL_2," ")
                //               .append_chars(num($card_total,2),"left",16," ")."\r\n";
                    
                //     $print_str .= "\r\n";
                // }

                //get all gc with excess
                // if($payments['gc_excess']){
                //     $print_str .= append_chars(substrwords('GC EXCESS',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                //                   .append_chars(num($payments['gc_excess'],2),"left",PAPER_RD_COL_3_3," ")."\r\n";
                //     $print_str .= "\r\n";
                // }

                //show all sign chit
                // $trans['sales']
                // if($trans['total_chit']){
                //     $print_str .= append_chars(substrwords('TOTAL CHIT',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                //                   .append_chars(num($trans['total_chit'],2),"left",PAPER_RD_COL_3_3," ")."\r\n";
                //     $print_str .= "\r\n";
                // }
            #CATEGORIES
                // $cats = $trans_menus['cats'];
                $print_str .= append_chars('Menus:',"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars('',"left",PAPER_RD_COL_3," ")."\r\n";
                $qty = 0;
                $total = 0;
                foreach ($sales_menus as $id => $val) {
                    if($val->qty > 0){
                        $print_str .= append_chars(substrwords($val->menu_name,18,""),"right",15," ").align_center($val->qty,5," ")
                                   .append_chars(num($val->total_amount,2),"left",13," ")."\r\n";
                        $qty += $val->qty;
                        $total += $val->total_amount;
                    }
                 }
                $print_str .= "-----------------"."\r\n";
                $cat_total_qty = $qty;
                $print_str .= append_chars("SubTotal","right",12," ").align_center($qty,PAPER_RD_COL_2," ")
                              .append_chars(num($total,2),"left",16," ")."\r\n";

                $print_str .= "\r\n";

                $qty = 0;
                $total_mod = 0;

                $print_str .= append_chars('Menu Modifiers:',"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                             .append_chars('',"left",PAPER_RD_COL_3," ")."\r\n";

                foreach ($sales_menu_modifiers as $id => $val) {
                    if($val->qty > 0){
                        $print_str .= append_chars(substrwords($val->mod_code,18,""),"right",15," ").align_center($val->qty,5," ")
                                   .append_chars(num($val->total_amount,2),"left",13," ")."\r\n";
                        $qty += $val->qty;
                        $total_mod += $val->total_amount;
                    }
                 }
                $print_str .= "-----------------"."\r\n";
                $cat_total_qty = $qty;
                
                $print_str .= append_chars("Modifiers SubTotal","right",12," ").align_center($qty,PAPER_RD_COL_2," ")
                              .append_chars(num($total_mod,2),"left",16," ")."\r\n";
                 $print_str .= append_chars("SubModifier Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                              .append_chars(num(0,2),"left",PAPER_RD_COL_3_3," ")."\r\n";
                

                $print_str .= append_chars("Total","right",12," ").align_center('',PAPER_RD_COL_2," ")
                              .append_chars(num($total+$total_mod,2),"left",16," ")."\r\n";
                $print_str .= "\r\n";
            #SUBCATEGORIES
                // $subcats = $trans_menus['sub_cats'];
                // $print_str .= append_chars('Menu Types:',"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                //              .append_chars('',"left",PAPER_RD_COL_3," ")."\r\n";
                // $qty = 0;
                // $total = 0;
                // foreach ($subcats as $id => $val) {
                //     $print_str .= append_chars($val['name'],"right",12," ").align_center($val['qty'],PAPER_RD_COL_2," ")
                //                .append_chars(num($val['amount'],2),"left",16," ")."\r\n";
                //     $qty += $val['qty'];
                //     $total += $val['amount'];
                //  }
                // $print_str .= "-----------------"."\r\n";
                // $print_str .= append_chars("Total","right",12," ").align_center($qty,PAPER_RD_COL_2," ")
                //               .append_chars(num($total,2),"left",16," ")."\r\n";
              
               $print_str .= "\r\n";
            #FREE MENUS
                // $free = $trans_menus['free_menus'];
                // $print_str .= append_chars('Free Menus:',"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                //              .append_chars('',"left",PAPER_RD_COL_3," ")."\r\n";
                // $fm = array();
                // foreach ($free as $ms) {
                //     if(!isset($fm[$ms->menu_id])){
                //         $mn = array();
                //         $mn['name'] = $ms->menu_name;
                //         $mn['cat_id'] = $ms->cat_id;
                //         $mn['qty'] = $ms->qty;
                //         $mn['amount'] = $ms->sell_price * $ms->qty;
                //         $mn['sell_price'] = $ms->sell_price;
                //         $mn['code'] = $ms->menu_code;
                //         // $mn['free_user_id'] = $ms->free_user_id;
                //         $fm[$ms->menu_id] = $mn;
                //     }
                //     else{
                //         $mn = $fm[$ms->menu_id];
                //         $mn['qty'] += $ms->qty;
                //         $mn['amount'] += $ms->sell_price * $ms->qty;
                //         $fm[$ms->menu_id] = $mn;
                //     }
                // }
                // $qty = 0;
                // $total = 0;
                // foreach ($fm as $menu_id => $val) {
                //     $print_str .= append_chars($val['name'],"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                //                .append_chars(($val['qty']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                //     $qty += $val['qty'];
                //     $total += $val['amount'];
                // }
                // $print_str .= "-----------------"."\r\n";
                // $print_str .= append_chars("Total","right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                //               .append_chars(($qty),"left",PAPER_RD_COL_3_3," ")."\r\n";
                // $print_str .= "\r\n";
                // $print_str .= "\r\n";    
            #FOOTER
                // $print_str .= append_chars(substrwords('Invoice Start: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                //              .append_chars(iSetObj($trans['first_ref'],'trans_ref'),"left",PAPER_RD_COL_3_3," ")."\r\n";
                // $print_str .= append_chars(substrwords('Invoice End: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                //              .append_chars(iSetObj($trans['last_ref'],'trans_ref'),"left",PAPER_RD_COL_3_3," ")."\r\n";
                // $print_str .= append_chars(substrwords('Invoice Ctr: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                //              .append_chars($trans['ref_count'],"left",PAPER_RD_COL_3_3," ")."\r\n";
                if($title_name == "ZREAD"){
                    // $gt = $this->old_grand_net_total($post['from']);
                    // $print_str .= "\r\n";
                    // $print_str .= append_chars(substrwords('OLD GT: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                    //              .append_chars(numInt( $gt['old_grand_total']),"left",PAPER_RD_COL_3_3," ")."\r\n";
                    // $print_str .= append_chars(substrwords('NEW GT: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                    //              .append_chars( numInt($gt['old_grand_total']+$net_no_adds)  ,"left",PAPER_RD_COL_3_3," ")."\r\n";
                    // $print_str .= append_chars(substrwords('Z READ CTR: ',18,""),"right",PAPER_RD_COL_1," ").align_center('',PAPER_RD_COL_2," ")
                    //              .append_chars( $gt['ctr'] ,"left",PAPER_RD_COL_3_3," ")."\r\n";
                }
                $print_str .= PAPER_LINE."\r\n";
            
                if ($asJson) {
                return $print_str;
            }    

            $this->session->set_userdata('pdf_data','<pre>'.$print_str.'</pre>') ;   
            // if(PRINT_VERSION && PRINT_VERSION == 'V2'){
            //     $this->do_print_v2($print_str,$asJson);  
            // }else if(PRINT_VERSION && PRINT_VERSION == 'V3' && $asJson){
            //     echo $this->html_print($print_str);
            // }else{
                $this->do_print($print_str,$asJson);

    }

    public function excel_transite($sales_id=null,$noPrint=true){
        // echo "<pre>",print_r($sales_id),"</pre>";die();
        $this->load->library('Excel');
        $sheet = $this->excel->getActiveSheet();
        $filename = 'Transight Report';
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
        $trans_date = date('Y-m-d',strtotime($date));
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
       
        ini_set('memory_limit', '-1');
        set_time_limit(3600);
        
        $print_str = $this->print_header($branch_code);
        $user = $this->session->userdata('user');
        $time = $this->site_model->get_db_now();
        $post = $this->set_post();
        $curr = $this->search_current();
        $trans = $this->trans_sales($args,$curr);
        // var_dump($trans['net']); die();
        $sales = $this->site_model->get_tbl('transite_sales',array('trans_date'=>$trans_date,'branch_code'=>$branch_code));

        $sales_discounts = $this->site_model->get_tbl('transite_sales_discounts',array('transite_id'=>$sales[0]->transite_id));
        $sales_menus = $this->site_model->get_tbl('transite_sales_menus',array('transite_id'=>$sales[0]->transite_id));
        $sales_menu_modifiers = $this->site_model->get_tbl('transite_sales_menu_modifiers',array('transite_id'=>$sales[0]->transite_id));
        $sales_payments = $this->site_model->get_tbl('transite_sales_payments',array('transite_id'=>$sales[0]->transite_id));
            
        
        #GENERAL
            $title_name = "TRANSIGHT POS SALES REPORT";
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


        $sheet->getCell('A'.$rc)->setValue('TOTAL SALES');
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue(num($sales[0]->gross_sales,2));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        $rc++;
       
        $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('SC/PWD VAT EXEMPT')),18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue('-'.num($sales[0]->vat_exempt,2));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        $rc++;

        $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('GROSS SALES')),18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue(num($sales[0]->gross_sales  - $sales[0]->vat_exempt,2));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        $rc++;
        $rc++;
       
         $types = $sales_discounts;
        $qty = 0;
        foreach ($types as $code => $val) {
            if($val->name != 'DIPLOMAT'){
                $amount = $val->total_amount;
                // if(MALL == 'megamall' && $code == PWDDISC){
                //     $amount = $val['amount'] / 1.12;
                // }
               $qty += $val->qty;

                $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtolower($val->name)),18,""));
                $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
                $sheet->getCell('C'.$rc)->setValue(num($amount,2));
                $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
                $rc++;
            }
        }
        $sheet->getCell('A'.$rc)->setValue(substrwords('NET SALES',23,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue(num($sales[0]->net_sales,2));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        $rc++;
            
        $pay_qty = 0;
        #SUMMARY
        $sheet->getCell('A'.$rc)->setValue(substrwords('VAT SALES',23,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue(num($sales[0]->vat_sales,2));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(substrwords('VAT',23,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue(num($sales[0]->vat,2));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(substrwords('VAT EXEMPT SALES',23,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue(num($sales[0]->vat_exempt_sales,2));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        
        $rc++;
        $rc++;
        
        $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('Payment Breakdown:')),18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $rc++;
        $payments_total = 0;
        $pay_qty = 0;
        foreach ($sales_payments as $code => $val) {
            $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper($val->code)),18,""));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue($val->qty);
            $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('C'.$rc)->setValue(num($val->total_amount,2));
            $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
            $rc++;
            $pay_qty += $val->qty;
            $payments_total += $val->total_amount;
        }
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(substrwords('TOTAL PAYMENTS',18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('B'.$rc)->setValue($pay_qty);
        $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue(num($payments_total,2));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        $rc++;
       
        // $rc++;
        // $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('VOID SALES')),18,""));
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        // $sheet->getCell('C'.$rc)->setValue(num($void,2));
        // $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        // $rc++;
        // $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('VOID SALES COUNT')),18,""));
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        // $sheet->getCell('C'.$rc)->setValue(num($void_cnt,2));
        // $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        // $rc++;
        // $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('CANCELLED TRANS')),18,""));
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        // $sheet->getCell('C'.$rc)->setValue(num($cancelled,2));
        // $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        // $rc++;
        // $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('CANCELLED TRANS COUNT')),18,""));
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        // $sheet->getCell('C'.$rc)->setValue(num($cancel_cnt,2));
        // $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        // $rc++;
        // $cancelled_order = $this->cancelled_orders($args,array(),$branch_code);
        $co = 0; // $cancelled_order['cancelled_order'];
        $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('CANCELLED ORDERS')),18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue(num($co,2));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        // $rc++;
        // $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('CANCELLED ORDER COUNT')),18,""));
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        // $sheet->getCell('C'.$rc)->setValue(num($cancelled_order['cancel_count'],2));
        // $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        // $rc++;
        // $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('Local Tax')),18,""));
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        // $sheet->getCell('C'.$rc)->setValue(num($loc_txt,2));
        // $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        $rc++;
        $rc++;
        #TRANS COUNT
        // $types = $trans['types'];
        // $types_total = array();
        // $guestCount = 0;
        // foreach ($types as $type => $tp) {
        //     foreach ($tp as $id => $opt){
        //         if(isset($types_total[$type])){
        //             $types_total[$type] += round($opt->total_amount,2);

        //         }
        //         else{
        //             $types_total[$type] = round($opt->total_amount,2);
        //         }
        //         if($opt->guest == 0)
        //             $guestCount += 1;
        //         else
        //             $guestCount += $opt->guest;
        //     }
        // }
        // $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('Trans Count:')),18,""));
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        // $rc++;
        // $tc_total  = 0;
        // $tc_qty = 0;
        // foreach ($types_total as $typ => $tamnt) {
        //     $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper($typ)),18,""));
        //     $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        //     $sheet->getCell('B'.$rc)->setValue(count($types[$typ]));
        //     $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
        //     $sheet->getCell('C'.$rc)->setValue(num($tamnt,2));
        //     $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        //     $rc++;
        //     $tc_total += $tamnt;
        //     $tc_qty += count($types[$typ]);
        // }
        // $rc++;
        // $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('TC Total')),18,""));
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        // $sheet->getCell('C'.$rc)->setValue(num($tc_total,2));
        // $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        // $rc++;
        // $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('GUEST Total')),18,""));
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        // $sheet->getCell('C'.$rc)->setValue(num($guestCount,2));
        // $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        // $rc++;

        // if($net_sales){
        //     if($guestCount == 0){
        //         $avg = 0;
        //     }else{
        //         $avg = $net_sales/$guestCount;
        //     }
        // }else{
        //     $avg = 0;
        // }

        // $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('AVG Check')),18,""));
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        // $sheet->getCell('C'.$rc)->setValue(num($avg,2));
        // $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        // $rc++;
        // $rc++;

        // #CHARGES
        // $types = $trans_charges['types'];
        // $qty = 0;
        // $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('Charges:')),18,""));
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        // $rc++;
        // foreach ($types as $code => $val) {
        //     $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper($val['name'])),18,""));
        //     $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        //     $sheet->getCell('B'.$rc)->setValue($val['qty']);
        //     $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
        //     $sheet->getCell('C'.$rc)->setValue(num($val['amount'],2));
        //     $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        //     $rc++;
        //     $qty += $val['qty'];
        // }
        // $rc++;
        // $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('Total Charges')),18,""));
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        // $sheet->getCell('B'.$rc)->setValue($qty);
        // $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
        // $sheet->getCell('C'.$rc)->setValue(num($charges,2));
        // $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        // $rc++;
        // $rc++;
        #Discounts
        $types = $sales_discounts;
        $qty = 0;
        $total_discount = 0;
        $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('Discounts:')),18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $rc++;
        foreach ($types as $code => $val) {
            if($code != 'DIPLOMAT'){
                $amount = $val->total_amount;
                $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper($val->name)),18,""));
                $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
                $sheet->getCell('B'.$rc)->setValue($val->qty);
                $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
                $sheet->getCell('C'.$rc)->setValue(num($amount,2));
                $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
                $rc++;
                $qty += $val->qty;
                $total_discount += $amount;
            }
        }
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('Total Discounts')),18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('B'.$rc)->setValue($qty);
        $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue(num($total_discount,2));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('VAT EXEMPT')),18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue(num($sales[0]->vat_exempt,2));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        $rc++;
        $rc++;
        #PAYMENTS
       
        $payments_total = 0;
        $pay_qty = 0;
        $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('Payment Breakdown:')),18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $rc++;
        $vcash = 0;
        foreach ($sales_payments as $code => $val) {
            $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper($val->code)),18,""));
            $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('B'.$rc)->setValue($val->qty);
            $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
            $sheet->getCell('C'.$rc)->setValue(num($val->total_amount,2));
            $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
            $rc++;
            $payments_total += $val->total_amount;
            $pay_qty += $val->qty;
        }
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(substrwords('TOTAL PAYMENTS',18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('B'.$rc)->setValue($pay_qty);
        $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue(num($payments_total,2));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        $rc++;
         //card breakdown
        // if($payments['cards']){
        //     $cards = $payments['cards'];
        //     $card_total = 0;
        //     $count_total = 0;
        //     $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('Card Breakdown:')),18,""));
        //     $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        //     $rc++;
        //     foreach($cards as $key => $val){
        //         $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper($key)),18,""));
        //         $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        //         $sheet->getCell('B'.$rc)->setValue($val['count']);
        //         $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
        //         $sheet->getCell('C'.$rc)->setValue(num($val['amount'],2));
        //         $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        //         $rc++;
        //         $card_total += $val['amount'];
        //         $count_total += $val['count'];
        //     }
        //     $rc++;
        //     $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('Total')),18,""));
        //     $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        //     $sheet->getCell('B'.$rc)->setValue($count_total);
        //     $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
        //     $sheet->getCell('C'.$rc)->setValue(num($card_total,2));
        //     $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        //     $rc++;
        // }

        //get all gc with excess
        // if($payments['gc_excess']){
        //     $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('GC EXCESS')),18,""));
        //     $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        //     $sheet->getCell('B'.$rc)->setValue($payments['gc_excess']);
        //     $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
        //     $rc++;
        // }
        //show all sign chit
        // $trans['sales']
        // if($trans['total_chit']){
        //     $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('TOTAL CHIT')),18,""));
        //     $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        //     $sheet->getCell('B'.$rc)->setValue($trans['total_chit']);
        //     $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
        //     $rc++;
        // }
        #CATEGORIES
        $rc++;
        
        $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('Menus:')),18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $rc++;
        $qty = 0;
        $total = 0;
        foreach ($sales_menus as $id => $val) {
            if($val->qty > 0){
                $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper($val->menu_name)),18,""));
                $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
                $sheet->getCell('B'.$rc)->setValue($val->qty);
                $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
                $sheet->getCell('C'.$rc)->setValue(num($val->total_amount,2));
                $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
                $rc++;
                $qty += $val->qty;
                $total += $val->total_amount;
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

         $rc++;
        
        $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('Menu Modifiers:')),18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $rc++;
        $qty = 0;
        $total_mod = 0;
        foreach ($sales_menu_modifiers as $id => $val) {
            if($val->qty > 0){
                $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper($val->mod_code)),18,""));
                $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
                $sheet->getCell('B'.$rc)->setValue($val->qty);
                $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
                $sheet->getCell('C'.$rc)->setValue(num($val->total_amount,2));
                $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
                $rc++;
                $qty += $val->qty;
                $total_mod += $val->total_amount;
            }
         }
        $cat_total_qty = $qty;
        $rc++;
        $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('Modifiers SubTotal')),18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('B'.$rc)->setValue($qty);
        $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue(num($total,2));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);

         $rc++;
        $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('SubModifier SubTotal')),18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('B'.$rc)->setValue('');
        $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue(num(0,2));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);

         $rc++;
        $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('Total')),18,""));
        $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('B'.$rc)->setValue('');
        $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
        $sheet->getCell('C'.$rc)->setValue(num($total+$total_mod,2));
        $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);

        $rc++;

        // $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('Modifiers Total')),18,""));
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        // $sheet->getCell('C'.$rc)->setValue(num($trans_menus['mods_total'],2));
        // $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        // $rc++;
        // $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('SubModifier Total')),18,""));
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        // $sheet->getCell('C'.$rc)->setValue(num($trans_menus['submods_total'],2));
        // $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        // $rc++;
        // if($trans_menus['item_total'] > 0){
        //     $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('Retail Items Total')),18,""));
        //     $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        //     $sheet->getCell('C'.$rc)->setValue(num($trans_menus['item_total'],2));
        //     $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        //     $rc++;
        // }
        
        // $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('Total')),18,""));
        // $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
        // $sheet->getCell('C'.$rc)->setValue(num($total+$trans_menus['mods_total']+$trans_menus['item_total']+$trans_menus['submods_total'],2));
        // $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        // $rc++;

        // #SUBCATEGORIES
        // $subcats = $trans_menus['sub_cats'];
        // $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('Menu Types:')),18,""));
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        // $rc++;
        // $qty = 0;
        // $total = 0;
        // foreach ($subcats as $id => $val) {

        //     $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper($val['name'])),18,""));
        //     $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        //     $sheet->getCell('B'.$rc)->setValue($val['qty']);
        //     $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
        //     $sheet->getCell('C'.$rc)->setValue(num($val['amount'],2));
        //     $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        //     $rc++;
        //     $qty += $val['qty'];
        //     $total += $val['amount'];
        //  }
        // $rc++;
        // $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('Total')),18,""));
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        // $sheet->getCell('B'.$rc)->setValue($qty);
        // $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
        // $sheet->getCell('C'.$rc)->setValue(num($total,2));
        // $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        // $rc++;
        // #FREE MENUS
        // $free = $trans_menus['free_menus'];
        // $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('Free Menus:')),18,""));
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        // $rc++;
        // $fm = array();
        // foreach ($free as $ms) {
        //     if(!isset($fm[$ms->menu_id])){
        //         $mn = array();
        //         $mn['name'] = $ms->menu_name;
        //         $mn['cat_id'] = $ms->cat_id;
        //         $mn['qty'] = $ms->qty;
        //         $mn['amount'] = $ms->sell_price * $ms->qty;
        //         $mn['sell_price'] = $ms->sell_price;
        //         $mn['code'] = $ms->menu_code;
        //         $fm[$ms->menu_id] = $mn;
        //     }
        //     else{
        //         $mn = $fm[$ms->menu_id];
        //         $mn['qty'] += $ms->qty;
        //         $mn['amount'] += $ms->sell_price * $ms->qty;
        //         $fm[$ms->menu_id] = $mn;
        //     }
        // }
        // $qty = 0;
        // $total = 0;
        // foreach ($fm as $menu_id => $val) {
        //     $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper($val['name'])),18,""));
        //     $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        //     $sheet->getCell('B'.$rc)->setValue($val['qty']);
        //     $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
        //     $sheet->getCell('C'.$rc)->setValue(num($val['amount'],2));
        //     $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        //     $rc++;
        //     $qty += $val['qty'];
        //     $total += $val['amount'];
        // }
        // $rc++;
        // $sheet->getCell('A'.$rc)->setValue(substrwords(ucwords(strtoupper('Total')),18,""));
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        // $sheet->getCell('B'.$rc)->setValue($qty);
        // $sheet->getStyle('B'.$rc)->applyFromArray($styleTxt);
        // $sheet->getCell('C'.$rc)->setValue(num($total,2));
        // $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        // $rc++;
        // #FOOTER
        // $sheet->getCell('A'.$rc)->setValue(substrwords('Invoice Start: ',18,""));
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        // $sheet->getCell('C'.$rc)->setValue(iSetObj($trans['first_ref'],'trans_ref'));
        // $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        // $rc++;
        // $sheet->getCell('A'.$rc)->setValue(substrwords('Invoice End: ',18,""));
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        // $sheet->getCell('C'.$rc)->setValue(iSetObj($trans['last_ref'],'trans_ref'));
        // $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        // $rc++;
        // $sheet->getCell('A'.$rc)->setValue(substrwords('Invoice Ctr: ',18,""));
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        // $sheet->getCell('C'.$rc)->setValue($trans['ref_count']);
        // $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        // $rc++;
        // $sheet->getCell('A'.$rc)->setValue(substrwords('First Trans No.: ',18,""));
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        // $sheet->getCell('C'.$rc)->setValue((int)iSetObj($trans['first_ref'],'trans_ref'));
        // $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        // $rc++;
        // $sheet->getCell('A'.$rc)->setValue(substrwords('Last Trans No.: ',18,""));
        // $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        // $sheet->getCell('C'.$rc)->setValue((int)iSetObj($trans['last_ref'],'trans_ref'));
        // $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        // $rc++;
        // if($title_name == "ZREAD"){
        //     $gt = $this->old_grand_net_total($date,false,$branch_code);
        //     // $print_str .= "\r\n";
        //     $sheet->getCell('A'.$rc)->setValue(substrwords('OLD GT: ',18,""));
        //     $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        //     $sheet->getCell('C'.$rc)->setValue($gt['old_grand_total']);
        //     $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        //     $rc++;
        //     $sheet->getCell('A'.$rc)->setValue(substrwords('NEW GT: ',18,""));
        //     $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        //     $sheet->getCell('C'.$rc)->setValue($gt['old_grand_total']+$net_no_adds);
        //     $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        //     $rc++;
        //     $sheet->getCell('A'.$rc)->setValue(substrwords('Z READ CTR:',18,""));
        //     $sheet->getStyle('A'.$rc)->applyFromArray($styleTxt);
        //     $sheet->getCell('C'.$rc)->setValue($gt['ctr']);
        //     $sheet->getStyle('C'.$rc)->applyFromArray($styleNum);
        //     $rc++;
        // }
        //  $rc++;


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

}