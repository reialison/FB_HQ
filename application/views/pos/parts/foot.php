<script src="<?= base_url().'js/app/jquery.validate.min.js'; ?>" type="text/javascript"></script>
<script type="text/javascript" src="<?= base_url().'js/app/email.js'; ?>"></script>
<script type="text/javascript" src="<?= base_url().'js/app/jquery.swipebox.js'; ?>"></script>
<script src="<?= base_url().'js/app/jquery.mobile-custom.js'; ?>"></script>
<script src="<?= base_url().'js/app/jquery-ui.js'; ?>"></script>
<script src="<?= base_url().'js/app/jquery.prettyphoto.js'; ?>"></script>
<script src="<?= base_url().'js/app/sweetalert.min.js'; ?>"></script>
<script src="<?= base_url().'js/app/jquery.jcarousel.min.js'; ?>"></script>
<script src="<?= base_url().'js/app/jcarousel.responsive.js"'; ?>"></script>
<script src="<?= base_url().'js/app/jquery.searchable-1.1.0.min.js"'; ?>"></script>
<script src="<?= base_url().'js/app/slick.js' ?>"></script>
<script src="<?= base_url().'js/app/toastr.min.js' ?>"></script>
<script src="<?= base_url().'js/app/bootstrap.min.js' ?>"></script>

<!-- <script src="//cdn.jsdelivr.net/jquery.slick/1.6.0/slick.min.js"></script> -->
<script src="<?= base_url().'js/app/main.js'; ?>"></script>
<script>
 function baseUrl() {
 	var href = window.location.href.split('/');
 	return href[0]+'//'+href[2]+'/'+href[3]+'/';
 }
 function play_notif(filename,multiplier){

		var filename = baseUrl()+'img/'+filename;
			      var times = multiplier;
		            var loop = setInterval(repeat, 500);

		        function repeat() {
		            times--;
		            if (times === 0) {
		                clearInterval(loop);
		            }

		            var audio = document.createElement("audio");
		            audio.src = filename;

		            audio.play();
		        }

		        repeat();    
 }
</script>
<!-- <script src="<?= base_url().'js/jcarousel/jquery.js"'; ?>"></script> -->
</body>
</html>
