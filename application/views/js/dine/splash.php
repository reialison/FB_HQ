<script>
$(document).ready(function(){
   <?php if($use_js == 'splashJs'): ?>
        $('#splashLoad').rLoad({url:baseUrl+'splash/commercial'});
        setInterval(function() {
            $.post(baseUrl+'splash/check_trans', function (data) {
                // $("#test").text(data.ctr);
                if(data.ctr > 0){
                    window.location = baseUrl+'splash/transactions';
                }
            },'json');
        }, 1000);
   <?php elseif($use_js == 'splashComJs'): ?> 
        var height = $(document).height();
        var currentBackground = 0;
        var backgrounds = [];
        var ctr = 0;
        $('.splash-imgs').each(function(){
            backgrounds[ctr] = $(this).val();
            ctr++;
        });
        $('.splash-img-div').height(height).css({
            "background": "url("+backgrounds[0]+")  no-repeat center top",
            "-webkit-background-size": "cover",
            "-moz-background-size": "cover",
            "-o-background-size": "cover",
            "background-size": "cover"
        });
        if(backgrounds.length > 1){
        	setTimeout(changeBackground, 5000);  
        }
        function changeBackground() {
            currentBackground++;
            if(currentBackground > (ctr-1) ) currentBackground = 0;
            $('.splash-img-div').fadeOut(1500,function() {
                $('.splash-img-div').css({
                    "background": "url("+backgrounds[currentBackground]+")  no-repeat center top",
                    "-webkit-background-size": "cover",
                    "-moz-background-size": "cover",
                    "-o-background-size": "cover",
                    "background-size": "cover"
                });
                $('.splash-img-div').fadeIn(1500);
            });
            setTimeout(changeBackground, 5000);
        }
   <?php elseif($use_js == 'splashTransJs'): ?> 
        var height = $(document).height();
        var width = $(document).width();
        var currentBackground = 0;
        var backgrounds = [];
        var ctr = 0;
        $('.splash-imgs').each(function(){
            backgrounds[ctr] = $(this).val();
            ctr++;
        });
        $('.splash-img-div').height(height-260).width(width-652).css({
            "background": "url("+backgrounds[0]+")  no-repeat center top",
            "-webkit-background-size": "cover",
            "-moz-background-size": "cover",
            "-o-background-size": "cover",
            "background-size": "cover",
            
        });

        setTimeout(changeBackground, 5000);  
        function changeBackground() {
            currentBackground++;
            if(currentBackground > (ctr-1) ) currentBackground = 0;
            $('.splash-img-div').fadeOut(1500,function() {
                $('.splash-img-div').css({
                    "background": "url("+backgrounds[currentBackground]+")  no-repeat center top",
                    "-webkit-background-size": "contain",
                    "-moz-background-size": "contain",
                    "-o-background-size": "contain",
                    "background-size": "contain",
                    
                });
                $('.splash-img-div').fadeIn(1500);
            });
            setTimeout(changeBackground, 5000);
        }

        setInterval(function() {
            $.post(baseUrl+'splash/check_trans', function (data) {
                // $("#test").text(data.ctr);
                if(data.ctr == 0){
                    window.location = baseUrl+'splash';
                }
            },'json');
            transTotal();
            get_trans();
        }, 1000);
        function get_trans(){
            $.post(baseUrl+'splash/get_counter', function (data) {
                var head = data.counter;
                $('#trans-header').html(head.type);
                $('#trans-datetime').html(head.datetime);
                $('#transBody').html(data.code);
                $('#transBody').perfectScrollbar({suppressScrollX: true});
            },'json');
        }
        function transTotal(){
            $.post(baseUrl+'cashier/total_trans',function(data){
                var total = data.total;
                var discount = data.discount;
                $("#total-txt").number(total,2);
                $("#discount-txt").number(discount,2);
            },'json');
        }
   <?php endif; ?>
});
</script>