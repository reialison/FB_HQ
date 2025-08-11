<?php
function splashPage($now=null){
	$CI =& get_instance();
		$CI->make->span('',array('id'=>'test'));
		$CI->make->sDiv(array('id'=>'manager'));		
			$CI->make->sDiv(array('id'=>'splashLoad'));		
	
			$CI->make->eDiv();
		$CI->make->eDiv();
	return $CI->make->code();
}
function commercialPage($splashes=array()){
	$CI =& get_instance();
		foreach ($splashes as $res) {
			$src = base_url().$res->img_path;
			$CI->make->hidden('splash-'.$res->img_id,$src,array('class'=>'splash-imgs'));
		}
		$CI->make->sDiv(array('class'=>'splash-img-div'));	
		$CI->make->eDiv();		
	return $CI->make->code();
}
function commercialPage2($splashes=array()){
	$CI =& get_instance();
		$CI->make->sDivRow();
			$CI->make->sDivCol(12);
				$CI->make->sDiv(array('style'=>'margin:30px;'));	
					$CI->make->sBox('default',array('class'=>'box-solid'));
						$CI->make->sBoxBody();
							// $img = array(
							// 		array('url'=>base_url().'img/splashPages/splashPage1.png','params'=>array('style'=>'height:500px;width:750px;')),
							// 		array('url'=>base_url().'img/splashPages/splashPage2.png','params'=>array('style'=>'height:500px;width:750px;')),
							// 		array('url'=>base_url().'img/splashPages/splashPage3.png','params'=>array('style'=>'height:500px;width:750px;')),
							// 		array('url'=>base_url().'img/splashPages/splashPage4.png','params'=>array('style'=>'height:500px;width:750px;')),
							// 		array('url'=>base_url().'img/splashPages/splashPage5.png','params'=>array('style'=>'height:500px;width:750px;')),
							// 		array('url'=>base_url().'img/splashPages/splashPage6.png','params'=>array('style'=>'height:500px;width:750px;'))
							// );
							// $CI->make->carousel('carousel',$img);
							foreach ($splashes as $res) {
								$src ="data:image/jpeg;base64,".base64_encode($res->img_blob);
								$img[] = array(
									'url'=>$src,'params'=>array('style'=>'height:500px;width:750px;')
								);
							}
							if(count($splashes) > 0){
								$CI->make->carousel('carousel',$img);
							}
						$CI->make->eBoxBody();
					$CI->make->eBox();
				$CI->make->eDiv();
			$CI->make->eDiv();
			$CI->make->eDivCol();
		$CI->make->eDivRow();		
	return $CI->make->code();
}
function transactionPage($splashes=array()){
	$CI =& get_instance();
		$CI->make->sDivRow();
			$CI->make->sDivCol(7);
				$CI->make->sDiv(array('style'=>'margin:10px;margin-top:60px;'));
				$CI->make->sBox('default',array('class'=>'box-solid'));
					$CI->make->sBoxBody();
						// $img = array(
						// 		array('url'=>base_url().'img/splashPages/splashPage1.png','params'=>array('style'=>'height:500px;width:750px;')),
						// 		array('url'=>base_url().'img/splashPages/splashPage2.png','params'=>array('style'=>'height:500px;width:750px;')),
						// 		array('url'=>base_url().'img/splashPages/splashPage3.png','params'=>array('style'=>'height:500px;width:750px;')),
						// 		array('url'=>base_url().'img/splashPages/splashPage4.png','params'=>array('style'=>'height:500px;width:750px;')),
						// 		array('url'=>base_url().'img/splashPages/splashPage5.png','params'=>array('style'=>'height:500px;width:750px;')),
						// 		array('url'=>base_url().'img/splashPages/splashPage6.png','params'=>array('style'=>'height:500px;width:750px;'))
						// );
						
						// $CI->make->carousel('carousel',$img);
						// foreach ($splashes as $res) {
						// 	$src = base_url().$res->img_path;
						// 	$img[] = array(
						// 		'url'=>$src,'params'=>array('style'=>'height:auto;width:100%;')
						// 	);
						// }
						// if(count($splashes) > 0){
						// 	$CI->make->carousel('carousel',$img);
						// }
						foreach ($splashes as $res) {
							$src = base_url().$res->img_path;
							$CI->make->hidden('splash-'.$res->img_id,$src,array('class'=>'splash-imgs'));
						}
						$CI->make->sDiv(array('class'=>'splash-img-div'));	
						$CI->make->eDiv();

					$CI->make->eBoxBody();
				$CI->make->eBox();
				$CI->make->eDiv();

			$CI->make->eDivCol();
			$CI->make->sDivCol(5);
				$CI->make->sDiv(array('style'=>'margin:10px;padding:0px;margin-left:0px;'));
					$CI->make->sDiv(array('style'=>'background-color:#F4EDE0;height:610px;'));
						$CI->make->H(3,'Type',array('id'=>'trans-header','class'=>'receipt text-center text-uppercase','style'=>'padding-top:10px;font-size:28px;'));
						$CI->make->H(5,'TIME',array('id'=>'trans-datetime','class'=>'receipt text-center','style'=>'padding-top:5px;'));
						$CI->make->sDiv(array('style'=>'margin-left:10px;margin-right:10px;'));
							$CI->make->append('<hr>');
						$CI->make->eDiv();
						$CI->make->sDiv(array('id'=>'transBody','class'=>'listings','style'=>'height:380px;font-size:16px;'));

						$CI->make->eDiv();
						// $CI->make->sDiv(array('style'=>'margin-left:10px;margin-right:10px;'));
						// 	$CI->make->append('<hr>');
						// $CI->make->eDiv();
						$CI->make->sDiv(array('class'=>'foot-det','style'=>'background-color:#91bd09;padding:32px;'));
							$CI->make->H(3,'TOTAL: <span id="total-txt">0.00</span>',array('class'=>'receipt text-center','style'=>'font-size:50px;'));
							$CI->make->H(5,'DISCOUNTS: <span id="discount-txt">0.00</span>',array('class'=>'receipt text-center'));
						$CI->make->eDiv();
					$CI->make->eDiv();
				$CI->make->eDiv();
			$CI->make->eDivCol();
		$CI->make->eDivRow();		
	return $CI->make->code();
}
