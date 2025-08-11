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
<title>Shell - mobile and tablet web app template</title>
<link href='//fonts.googleapis.com/css?family=Source+Sans+Pro:400,300,700,900' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="<?= base_url().'css/themes/default/jquery.mobile-1.4.5.css'; ?>">
<link type="text/css" rel="stylesheet" href="<?= base_url().'css/style2.css'; ?>" />
<link type="text/css" rel="stylesheet" href="<?= base_url().'css/style2.min.css'; ?>" />
<link type="text/css" rel="stylesheet" href="<?= base_url().'css/colors/yellow2.css'; ?>" />
<link type="text/css" rel="stylesheet" href="<?= base_url().'css/swipebox.css'; ?>" />
<link type="text/css" rel="stylesheet" href="<?= base_url().'css/bootstrap.css'; ?>" />
<link type="text/css" rel="stylesheet" href="<?= base_url().'css/sweetalert.css'; ?>" />
<link type="text/css" rel="stylesheet" href="<?= base_url().'css/toastr.min.css'; ?>" />

<link href="css/font-awesome/font-awesome.min.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?= base_url().'js/slick/slick.css'; ?>">
<link rel="stylesheet" href="<?= base_url().'js/jcarousel/jcarousel.responsive.css'; ?>">
<!-- <link rel="stylesheet" href="//cdn.jsdelivr.net/jquery.slick/1.6.0/slick.css"> -->
<script src="<?= base_url().'js/jquery.min.js'; ?>"></script>

<script src="<?= base_url().'js/jquery.mobile-1.4.5.min.js'; ?>"></script>


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
            var response = JSON.parse(resp);
            if(response.length > 0){
                $.each(response,function(i,e){
                    toastr.success('PUMP NUMBER: '+e.pump_number+ ' , PLATE NUMBER: '+ e.plate_number, 'Order has been dispatched!',{timeOut: 10000})
                });
            }
        }
      })
    }

</script>

</head>
<!-- <!DOCTYPE html>
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
<title>Shell - mobile and tablet web app template</title>
<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,300,700,900' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="<?= base_url().'css/themes/default/jquery.mobile-1.4.5.css'; ?>">
<link type="text/css" rel="stylesheet" href="<?= base_url().'css/bootstrap.css'; ?>" />
<link type="text/css" rel="stylesheet" href="<?= base_url().'css/style2.css'; ?>" />
<link type="text/css" rel="stylesheet" href="<?= base_url().'css/style2.min.css'; ?>" />
<link type="text/css" rel="stylesheet" href="<?= base_url().'css/colors/yellow2.css'; ?>" />
<link type="text/css" rel="stylesheet" href="<?= base_url().'css/swipebox.css'; ?>" />
<link type="text/css" rel="stylesheet" href="<?= base_url().'css/sweetalert.css'; ?>" />
<link type="text/css" rel="stylesheet" href="<?= base_url().'css/toastr.min.css'; ?>" />

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?= base_url().'js/slick/slick.css'; ?>">
<script src="<?= base_url().'js/jquery.min.js'; ?>"></script>

<link rel="stylesheet" href="<?= base_url().'js/jcarousel/jcarousel.responsive.css'; ?>">
</head> -->