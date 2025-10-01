function baseUrl() {
	var href = window.location.href.split('/');
	return href[0]+'//'+href[2]+'/'+href[3]+'/';
}
var baseUrl = baseUrl();
function isArray(object){
    return object.constructor === Array;
}
function formatNumber(number,dec){

	if(typeof(dec) == 'undefined')
		dec = 2;
    number = number.toFixed(dec) + '';
    x = number.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}
function rMsg(text,type){
	var n = noty({
	       text        : text,
	       type        : type,
	       dismissQueue: true,
	       layout      : 'topRight',
	       theme       : 'defaultTheme',
	       animation	: {
						open: {height: 'toggle'},
						close: {height: 'toggle'},
						easing: 'swing',
						speed: 500 // opening & closing animation speed
					}
	   }).setTimeout(3000);
}
function site_alerts(){
    $.post(baseUrl+'site/site_alerts',function(data){
        var alerts = data.alerts;
        $.each(alerts, function(index,row){
            rMsg(row['text'],row['type']);
        });
    },"json").promise().done(function() {
        $.post(baseUrl+'site/clear_site_alerts');
    });
}
$(document).ready(function(){
    site_alerts();

    //datatable buttons

        // var table = $('.data-table');
        // var oTable = table.dataTable({

        //     // Internationalisation. For more info refer to http://datatables.net/manual/i18n
        //     "language": {
        //         "aria": {
        //             "sortAscending": ": activate to sort column ascending",
        //             "sortDescending": ": activate to sort column descending"
        //         },
        //         "emptyTable": "No data available in table",
        //         "info": "Showing _START_ to _END_ of _TOTAL_ entries",
        //         "infoEmpty": "No entries found",
        //         "infoFiltered": "(filtered1 from _MAX_ total entries)",
        //         "lengthMenu": "_MENU_ entries",
        //         "search": "Search:",
        //         "zeroRecords": "No matching records found"
        //     },

        //     // Or you can use remote translation file
        //     //"language": {
        //     //   url: '//cdn.datatables.net/plug-ins/3cfcc339e89/i18n/Portuguese.json'
        //     //},


        //     buttons: [
        //         { extend: 'print', className: 'btn dark btn-outline' },
        //         { extend: 'copy', className: 'btn red btn-outline' },
        //         { extend: 'pdf', className: 'btn green btn-outline' },
        //         { extend: 'excel', className: 'btn yellow btn-outline ' },
        //         { extend: 'csv', className: 'btn purple btn-outline ' },
        //         { extend: 'colvis', className: 'btn dark btn-outline', text: 'Columns'}
        //     ],

        //     // setup responsive extension: http://datatables.net/extensions/responsive/
        //     responsive: true,

        //     //"ordering": false, disable column ordering 
        //     //"paging": false, disable pagination

        //     "order": [
        //         [0, 'asc']
        //     ],
            
        //     "lengthMenu": [
        //         [5, 10, 15, 20, -1],
        //         [5, 10, 15, 20, "All"] // change per page values here
        //     ],
        //     // set the initial value
        //     "pageLength": 10,    

        //     "dom": "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // horizobtal scrollable datatable

        //     // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
        //     // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js). 
        //     // So when dropdowns used the scrollable div should be removed. 
        //     //"dom": "<'row' <'col-md-12'T>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
        // });
	// $('.data-table').dataTable({
 //        "bPaginate": true,
 //        "bLengthChange": true,
 //        "bFilter": true,
 //        "bSort": true,
 //        "bInfo": true,
 //        "bAutoWidth": false
 //    });
    $('.no-decimal').number(true,0);
    // $('.numbers-only').number();
    $('.numbers-only').keydown(function (event) {

           if (event.shiftKey == true) {
               event.preventDefault();
           }

           if ((event.keyCode >= 48 && event.keyCode <= 57) || 
               (event.keyCode >= 96 && event.keyCode <= 105) || 
               event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 37 ||
               event.keyCode == 39 || event.keyCode == 46 || event.keyCode == 190 || event.keyCode == 110) {

           } else {
               event.preventDefault();
           }

           if($(this).val().indexOf('.') !== -1 && event.keyCode == 190)
               event.preventDefault(); 
           if($(this).val().indexOf('.') !== -1 && event.keyCode == 110)
               event.preventDefault(); 

       });
    $("[data-mask]").inputmask();
    
    $('.pick-date').datetimepicker({
        pickTime: false
    });

    var problem = $('body').attr('problem');
    if (typeof problem !== typeof undefined && problem !== false) {
        // There are unclosed shifts. Close it first before you can start a trasaction
        $('#nav-problem-txt').css({
            'float':'left'
        }).html('<h4 style="color:#555;padding:8px;padding-top:5px;background-color:#FCF8E3;-webkit-border-radius: 5px;-moz-border-radius: 5px;border-radius: 5px;"> <i class="fa fa-warning"></i> Warning! '+problem+'</h3>');
        if($('.new-order-btns').exists()){
            $('.new-order-btns').attr('disabled','disabled');
        }
    }


});
$(function() {
    function clearTableActivity() {
        $.post(baseUrl+'cashier/update_tbl_activity/0/1',function(data){});
    }
    window.onbeforeunload = clearTableActivity;
});
    //Sparkline charts
    var myvalues = [511, 323, 555, 731, 100, 220, 101, 276, 195, 399, 219];
    $('#sparkline-1').sparkline(myvalues, {
        type: 'bar',
        barColor: '#00a65a',
        negBarColor: "#f56954",
        height: '20px'
    });
    myvalues = [15, 19, 20, 22, 55, 30, 58, 27, 19, 30, 21];
    $('#sparkline-2').sparkline(myvalues, {
        type: 'bar',
        barColor: '#00a65a',
        negBarColor: "#f56954",
        height: '20px'
    });
    myvalues = [35, 29, 30, 22, 33, 27, 31, 27, 29, 30, 36];
    $('#sparkline-3').sparkline(myvalues, {
        type: 'bar',
        barColor: '#00a65a',
        negBarColor: "#f56954",
        height: '20px'
    });
    myvalues = [15, 19, 20, 22, 33, -27, -31, 27, 19, 30, 21];
    $('#sparkline-4').sparkline(myvalues, {
        type: 'bar',
        barColor: '#00a65a',
        negBarColor: "#f56954",
        height: '20px'
    });
    myvalues = [15, 19, 20, 22, 33, 27, 31, -27, -19, 30, 21];
    $('#sparkline-5').sparkline(myvalues, {
        type: 'bar',
        barColor: '#00a65a',
        negBarColor: "#f56954",
        height: '20px'
    });
    myvalues = [15, 19, -20, 22, -13, 27, 31, 27, 19, 30, 21];
    $('#sparkline-6').sparkline(myvalues, {
        type: 'bar',
        barColor: '#00a65a',
        negBarColor: "#f56954",
        height: '20px'
    });

    function goTo(url){
        window.location = baseUrl+url;
    }