<?php
// var_dump(base_url().$image);die();

 // echo phpinfo();die();
// echo $image;
  if(empty($image) && isset($image)) {

      echo base_url().'img/noimage.jpg';
  }

  else {
  		$path = base_url().$image;
  		$type = pathinfo(base_url().$image, PATHINFO_EXTENSION);
		$data = file_get_contents($path);

		// echo $type;die();

		if($type  == 'jpg'){
			$type = 'jpeg';
		}
		$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
  // header('Content-type: image/'.$type);
  	// echo "aaa";die();
  	 // echo base_url().$image;
      // echo "<img src='".base_url().$image."'>";
echo $base64;
// die();



  }

?>