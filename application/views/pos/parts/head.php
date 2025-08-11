<?php //echo base_url(); die(); ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, minimal-ui">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<link rel="apple-touch-icon" href="images/apple-touch-icon.png" />
<link rel="apple-touch-startup-image" media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2)" href="images/apple-touch-startup-image-640x1096.png">
<meta name="author" content="SINDEVO.COM" />
<meta name="description" content="biotic - mobile and tablet web app template" />
<meta name="keywords" content="mobile css template, mobile html template, jquery mobile template, mobile app template, html5 mobile design, mobile design" />
<title>Mobile POS</title>
<link href='//fonts.googleapis.com/css?family=Source+Sans+Pro:400,300,700,900' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="<?= base_url().'css/app/jquery.mobile-1.4.5.min.css'; ?>">
<link type="text/css" rel="stylesheet" href="<?= base_url().'css/app/style.css'; ?>" />
<link type="text/css" rel="stylesheet" href="<?= base_url().'css/app/style.min.css'; ?>" />
<link type="text/css" rel="stylesheet" href="<?= base_url().'css/app/yellow.css'; ?>" />
<link type="text/css" rel="stylesheet" href="<?= base_url().'css/app/swipebox.css'; ?>" />
<link type="text/css" rel="stylesheet" href="<?= base_url().'css/app/bootstrap.css'; ?>" />
<link type="text/css" rel="stylesheet" href="<?= base_url().'css/app/sweetalert.css'; ?>" />
<link type="text/css" rel="stylesheet" href="<?= base_url().'css/app/toastr.min.css'; ?>" />
<link type="text/css" rel="stylesheet" href="<?= base_url().'css/app/bootstrap.min.css'; ?>" />

<link href="<?= base_url().'css/app/font-awesome.min.css'; ?>" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?= base_url().'css/app/slick.css'; ?>">
<link rel="stylesheet" href="<?= base_url().'css/app/jcarousel.responsive.css'; ?>">
<!-- <link rel="stylesheet" href="//cdn.jsdelivr.net/jquery.slick/1.6.0/slick.css"> -->
<script src="<?= base_url().'js/app/jquery.min.js'; ?>"></script>

<script src="<?= base_url().'js/app/jquery.mobile-1.4.5.min.js'; ?>"></script>


<script>
// $.noConflict();
</script>

<script>
	setInterval(function(){ get_order_status(); }, 3000);

    // get_order_status();
    function get_order_status(){
        $.post("<?= base_url().'app/check_order_status/'?>", {} ,function(resp){
        console.log(resp);
    
        if(resp){
        	if(IsJsonString(resp)){    		
	            var response = JSON.parse(resp);
	            if(response.length > 0){
	               play_notif('dispatched.mp3',2);

	                $.each(response,function(i,e){
	                    toastr.success('', 'Order has been dispatched!',{timeOut: 128000})
	                });
	            }
        	}
        }
      });

       $.post("<?= base_url().'app/check_order_received_status/'?>", {} ,function(resp){
        // console.log(resp);
    
        if(resp){
        	if(IsJsonString(resp)){    		
	            var response = JSON.parse(resp);
	            if(response.length > 0){
	            	 play_notif('received.mp3',2);
	                $.each(response,function(i,e){
	                    toastr.info('', 'Order has been received!',{timeOut: 128000})
	                });
	            }
        	}
        }
      })
    }


    function IsJsonString(str) {
	    try {
	        JSON.parse(str);
	    } catch (e) {
	        return false;
	    }
	    return true;
	}


</script>

</head>