<script>
$(document).ready(function(){
	<?php if($use_js == 'dashBoardJs'): ?>
	startTime();

	$.post(baseUrl+'dashboard/get_dashboard_details',function(data){
		$('#total_transaction').text(data.todayTransNo);
		$('#lsu').html(data.lsu);
		$('#ts').html(data.ts);
	},'json');

	load_trans_chart();
	set_last_gt();
	set_top_ten_menu();
	set_today_sales();
	set_top_ten_toppings();	

	function set_last_gt(){
		var formData = "branch_code="+$("#branch_id").val();
		$('#last-gt').html('<i class="fa fa-refresh fa-spin"></i>');
		$.post(baseUrl+'dashboard/get_last_gt',formData,function(data){
			$('#last-gt').html(data);
		});
	}
	function set_top_ten_menu(){
		$('#top-menu-box').parent().parent().goBoxLoad();
		var formData = "branch_code="+$("#branch_id").val();
		$.post(baseUrl+'dashboard/get_top_menus',formData,function(data){
			$('#top-menu-box').parent().parent().goBoxLoad({load:false});
			$('#top-menu-box').html(data);
		});
	}

	function set_top_ten_toppings(){
		$('#top-topping-box').parent().parent().goBoxLoad();
		var formData = "branch_code="+$("#branch_id").val();
		$.post(baseUrl+'dashboard/get_top_toppings',formData,function(data){
			$('#top-topping-box').parent().parent().goBoxLoad({load:false});
			$('#top-topping-box').html(data);
		});
	}
	function load_trans_chart(){
		// $('#bar-chart').goLoad();
		$('#bars-div').goLoad();
		$.post(baseUrl+'dashboard/summary_orders',function(data){
			// alert(data.orders);
			// var orders = new Array();
			var shift_sales = new Array();
			$.each(data.shift_sales,function(key,val){
				shift_sales.push(val);
			});
			// var bar = new Morris.Bar({
	  //           element: 'bar-chart',
	  //           resize: true,
	  //           data: orders,
	  //           barColors: ["#428BCA", "#00A65A","#F39C12", "#F56954"],
	  //           xkey: 'label',
	  //           ykeys: ['open','settled','cancel','void'],
	  //           labels: ['open','settled','cancel','void'],
	  //           hideHover: 'auto'
	  // 		});
			// $('#bar-chart').goLoad({load:false});
			//DONUT CHART
		    var donut = new Morris.Donut({
		        element: 'sales-chart',
		        resize: true,
		        data:shift_sales,
		        hideHover: 'auto'
		    });
		    // console.log(shift_sales);
			$('#bars-div').html(data.code);
						// $(".knob").knob({
			//     draw: function() {

			//         // "tron" case
			//         if (this.$.data('skin') == 'tron') {

			//             var a = this.angle(this.cv)  // Angle
			//                     , sa = this.startAngle          // Previous start angle
			//                     , sat = this.startAngle         // Start angle
			//                     , ea                            // Previous end angle
			//                     , eat = sat + a                 // End angle
			//                     , r = true;

			//             this.g.lineWidth = this.lineWidth;

			//             this.o.cursor
			//                     && (sat = eat - 0.3)
			//                     && (eat = eat + 0.3);

			//             if (this.o.displayPrevious) {
			//                 ea = this.startAngle + this.angle(this.value);
			//                 this.o.cursor
			//                         && (sa = ea - 0.3)
			//                         && (ea = ea + 0.3);
			//                 this.g.beginPath();
			//                 this.g.strokeStyle = this.previousColor;
			//                 this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sa, ea, false);
			//                 this.g.stroke();
			//             }

			//             this.g.beginPath();
			//             this.g.strokeStyle = r ? this.o.fgColor : this.fgColor;
			//             this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sat, eat, false);
			//             this.g.stroke();

			//             this.g.lineWidth = 2;
			//             this.g.beginPath();
			//             this.g.strokeStyle = this.o.fgColor;
			//             this.g.arc(this.xy, this.xy, this.radius - this.lineWidth + 1 + this.lineWidth * 2 / 3, 0, 2 * Math.PI, false);
			//             this.g.stroke();

			//             return false;
			//         }
			//     }
			// });
			$('.easy-pie-chart .blue').easyPieChart({
                animate: 1000,
                size: 85,
                lineWidth: 3,
                barColor: App.getBrandColor('blue')
            });

            $('.easy-pie-chart .green').easyPieChart({
                animate: 1000,
                size: 85,
                lineWidth: 3,
                barColor: App.getBrandColor('green')
            });

            $('.easy-pie-chart .yellow').easyPieChart({
                animate: 1000,
                size: 85,
                lineWidth: 3,
                barColor: App.getBrandColor('yellow')
            });
	        $('.easy-pie-chart .red').easyPieChart({
	            animate: 1000,
	            size: 85,
	            lineWidth: 3,
	            barColor: App.getBrandColor('red')
	        });



            $('.knob-reload').click(function() {
                $('.knob .number').each(function() {
                    var newValue = Math.floor(100 * Math.random());
                    $(this).data('easyPieChart').update(newValue);
                    $('span', this).text(newValue);
                });
            });
		// });
		},'json');
	}
	function set_tot_transaction(){
		var formData = "branch_code="+$("#branch_id").val();
		$('#total_transaction').html('<i class="fa fa-refresh fa-spin"></i>');
		$.post(baseUrl+'dashboard/get_total_trans',formData,function(data){
			$('#total_transaction').html(data);
		});
	}
	function set_today_sales(){
		var formData = "branch_code="+$("#branch_id").val();
		$('#today_sales').html('<i class="fa fa-refresh fa-spin"></i>');
		$.post(baseUrl+'dashboard/get_sales_today',formData,function(data){
			$('#today_sales').html(data);
		});	
	}
	function clear_chart_data(){
		require.config({
	        paths: {
	            echarts: "js/echarts/"
	        }
	    }), require(["echarts", "echarts/chart/bar", "echarts/chart/chord", "echarts/chart/eventRiver", "echarts/chart/force", "echarts/chart/funnel", "echarts/chart/gauge", "echarts/chart/heatmap", "echarts/chart/k", "echarts/chart/line", "echarts/chart/map", "echarts/chart/pie", "echarts/chart/radar", "echarts/chart/scatter", "echarts/chart/tree", "echarts/chart/treemap", "echarts/chart/venn", "echarts/chart/wordCloud"], function(e) {
	    	var formData = "branch_code="+$("#branch_id").val();	        
	        $.post(baseUrl+'dashboard/chart_data',formData,function(data){
	        // console.log(data);
	        // alert(data);
	        var a = e.init(document.getElementById("echarts_bar"));
	        a.setOption({
	            tooltip: {
	                trigger: "axis"
	            },
	            grid:{
	                x:80,
	                y:10,
	                x2:10,
	                y2:40
	            },
	            
	            calculable: !0,
	            xAxis: [{
	                type: "category",
	                data: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]
	            }],
	            yAxis: [{
	                type: "value",
	                splitArea: {
	                    show: !0
	                }
	            }],
	            series: 
	            data.datas
	        });
	        },'json');       
	    });
	}
	function set_pie_chart(){
        $.post(baseUrl+'dashboard/pie_chart',function(data)
        {
			 var chart = AmCharts.makeChart("chart_7", {
	            "type": "pie",
	            "theme": "light",
	            "path": baseUrl+"assets/global/plugins/amcharts/ammap/images/",
	            "fontFamily": 'Open Sans',
	            
	            "color":    '#888',

	            // "dataProvider": [{
	            //     "country": "Lithuania",
	            //     "value": 260
	            // }, {
	            //     "country": "Ireland",
	            //     "value": 201
	            // }],
	            "dataProvider": JSON.parse(data),
	            "valueField": "value",
	            "titleField": "store",
	            "outlineAlpha": 0.4,
	            "depth3D": 11,
	            "balloonText": "[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>",
	            "angle": 30,
	            "exportConfig": {
	                menuItems: [{
	                    icon: '/lib/3/images/export.png',
	                    format: 'png'
	                }]
	            }
	        });
        });
		 
        // jQuery('.chart_7_chart_input').off().on('input change', function() {
        //     var property = jQuery(this).data('property');
        //     var target = chart;
        //     var value = Number(this.value);
        //     chart.startDuration = 0;

        //     if (property == 'innerRadius') {
        //         value += "%";
        //     }

        //     target[property] = value;
        //     chart.validateNow();
        // });

        // $('#chart_7').closest('.portlet').find('.fullscreen').click(function() {
        //     chart.invalidateSize();
        // });
	}
	function set_category_sales1()
	{	
		$('#chart_5').parent().parent().goBoxLoad({load:true});
		var formData = "branch_code="+$("#branch_id").val();
		$.post(baseUrl+"dashboard/category_sales_chart", formData, function(data){			 			
			$('#chart_5').parent().parent().goBoxLoad({load:false});
			var chart = AmCharts.makeChart("chart_5", {
	            "theme": "light",
	            "type": "serial",
	            "startDuration": 2,

	            "fontFamily": 'Open Sans',
	            
	            "color":    '#888',

	            "dataProvider": JSON.parse(data),
	            // "dataProvider": [{
	            //     "category": "USA",
	            //     "visits": 4025,
	            //     "color": "#FF0F00"
	            // }, {
	            //     "country": "China",
	            //     "visits": 1882,
	            //     "color": "#FF6600"
	            // }, {
	            //     "country": "Japan",
	            //     "visits": 1809,
	            //     "color": "#FF9E01"
	            // }, {
	            //     "country": "Germany",
	            //     "visits": 1322,
	            //     "color": "#FCD202"
	            // }, {
	            //     "country": "UK",
	            //     "visits": 1122,
	            //     "color": "#F8FF01"
	            // }, {
	            //     "country": "France",
	            //     "visits": 1114,
	            //     "color": "#B0DE09"
	            // }, {
	            //     "country": "India",
	            //     "visits": 984,
	            //     "color": "#04D215"
	            // }, {
	            //     "country": "Spain",
	            //     "visits": 711,
	            //     "color": "#0D8ECF"
	            // }, {
	            //     "country": "Netherlands",
	            //     "visits": 665,
	            //     "color": "#0D52D1"
	            // }, {
	            //     "country": "Russia",
	            //     "visits": 580,
	            //     "color": "#2A0CD0"
	            // }, {
	            //     "country": "South Korea",
	            //     "visits": 443,
	            //     "color": "#8A0CCF"
	            // }, {
	            //     "country": "Canada",
	            //     "visits": 441,
	            //     "color": "#CD0D74"
	            // }, {
	            //     "country": "Brazil",
	            //     "visits": 395,
	            //     "color": "#754DEB"
	            // }, {
	            //     "country": "Italy",
	            //     "visits": 386,
	            //     "color": "#DDDDDD"
	            // }, {
	            //     "country": "Australia",
	            //     "visits": 384,
	            //     "color": "#999999"
	            // }, {
	            //     "country": "Taiwan",
	            //     "visits": 338,
	            //     "color": "#333333"
	            // }, {
	            //     "country": "Poland",
	            //     "visits": 328,
	            //     "color": "#000000"
	            // }],
	            "valueAxes": [{
	                "position": "left",
	                "axisAlpha": 0,
	                "gridAlpha": 0
	            }],
	            "graphs": [{
	                "balloonText": "[[category]]: <b>[[value]]</b>",
	                "colorField": "color",
	                "fillAlphas": 0.85,
	                "lineAlpha": 0.1,
	                "type": "column",
	                "topRadius": 1,
	                "valueField": "amount"
	            }],
	            "depth3D": 20,
	            "angle": 20,
	            "chartCursor": {
	                "categoryBalloonEnabled": false,
	                "cursorAlpha": 0,
	                "zoomable": false
	            },
	            "categoryField": "category",
	            "categoryAxis": {
	                "gridPosition": "start",
	                "axisAlpha": 0,
	                "gridAlpha": 0

	            },
	            "exportConfig": {
	                "menuTop": "20px",
	                "menuRight": "20px",
	                "menuItems": [{
	                    "icon": '/lib/3/images/export.png',
	                    "format": 'png'
	                }]
	            }
	        }, 0);
		});

        // jQuery('.chart_5_chart_input').off().on('input change', function() {
        //     var property = jQuery(this).data('property');
        //     var target = chart;
        //     chart.startDuration = 0;

        //     if (property == 'topRadius') {
        //         target = chart.graphs[0];
        //     }

        //     target[property] = this.value;
        //     chart.validateNow();
        // });

        // $('#chart_5').closest('.portlet').find('.fullscreen').click(function() {
        //     chart.invalidateSize();
        // });
	}
	function set_category_sales()
	{			
		App.blockUI({
		                target: '#blockui_category_portlet_body',
		                animate: true
		            });
		var formData = "branch_code="+$("#branch_id").val();
		$.post(baseUrl+"dashboard/category_sales_chart", formData, function(data){
			var chart = AmCharts.makeChart("chart_5", {
	            "type": "serial",
	            "theme": "light",

	            "fontFamily": 'Open Sans',
	            "color":    '#888888',

	            "legend": {
	                "equalWidths": false,
	                "useGraphSettings": true,
	                "valueAlign": "left",
	                "valueWidth": 120
	            },
	            // "dataProvider": [{
	            //     "cat_name": "category 1",
	            //     "distance": 227,
	            //     // "townName": "category 1",
	            //     // "townName2": "New York",
	            //     // "townSize": 25,
	            //     // "latitude": 40.71,
	            //     // "duration": 408
	            // }],
	            "dataProvider": JSON.parse(data),
	            "valueAxes": [{
	                "id": "distanceAxis",
	                "axisAlpha": 0,
	                "gridAlpha": 0,
	                "position": "left",
	                "title": "sales"
	            }, 
	            {
	                "id": "latitudeAxis",
	                "axisAlpha": 0,
	                "gridAlpha": 0,
	                "labelsEnabled": false,
	                "position": "right"
	            }, 
	            {
	                "id": "durationAxis",
	                "duration": "mm",
	                "durationUnits": {
	                	"hh": "m ",
	                    "mm": "min"
	                    // "hh": "h ",
	                    // "mm": "min"
	                },
	                "axisAlpha": 0,
	                "gridAlpha": 0,
	                "inside": true,
	                "position": "right",
	                "title": "date"
	            }],
	            "graphs": [{
	                "alphaField": "alpha",
	                // "balloonText": "[[value]] miles",
	                "balloonText": "[[value]]",
	                "dashLengthField": "dashLength",
	                "fillAlphas": 0.7,
	                // "legendPeriodValueText": "total: [[value.sum]] mi",
	                "legendPeriodValueText": "total: [[value.sum]] ",
	                // "legendValueText": "[[value]] mi",
	                "legendValueText": "[[value]]",
	                "title": "Sales",
	                "type": "column",
	                "valueField": "distance",
	                "valueAxis": "distanceAxis"
	            }, {
	                "balloonText": "latitude:[[value]]",
	                "bullet": "round",
	                "bulletBorderAlpha": 1,
	                "useLineColorForBulletBorder": true,
	                "bulletColor": "#FFFFFF",
	                "bulletSizeField": "townSize",
	                "dashLengthField": "dashLength",
	                "descriptionField": "townName",
	                "labelPosition": "right",
	                "labelText": "[[townName2]]",
	                // "legendValueText": "[[description]]/[[value]]",
	                "legendValueText": "[[description]]",
	                // "title": "latitude/city",
	                "title": "Category",
	                "fillAlphas": 0,
	                "valueField": "latitude",
	                "valueAxis": "latitudeAxis"
	            }, 
	            // {
	            //     "bullet": "square",
	            //     "bulletBorderAlpha": 1,
	            //     "bulletBorderThickness": 1,
	            //     "dashLengthField": "dashLength",
	            //     "legendValueText": "[[value]]",
	            //     "title": "duration",
	            //     "fillAlphas": 0,
	            //     "valueField": "duration",
	            //     "valueAxis": "durationAxis"
	            // }
	            ],
	            "chartCursor": {
	                "categoryBalloonDateFormat": "DD",
	                "cursorAlpha": 0.1,
	                "cursorColor": "#000000",
	                "fullWidth": true,
	                "valueBalloonsEnabled": false,
	                "zoomable": false
	            },
	            // "dataDateFormat": "YYYY-MM-DD",
	            "categoryField": "cat_name",
	            // "categoryAxis": {
	            //     "dateFormats": [{
	            //         "period": "DD",
	            //         "format": "DD"
	            //     }, {
	            //         "period": "WW",
	            //         "format": "MMM DD"
	            //     }, {
	            //         "period": "MM",
	            //         "format": "MMM"
	            //     }, {
	            //         "period": "YYYY",
	            //         "format": "YYYY"
	            //     }],
	            //     "parseDates": true,
	            //     "autoGridCount": false,
	            //     "axisColor": "#555555",
	            //     "gridAlpha": 0.1,
	            //     "gridColor": "#FFFFFF",
	            //     "gridCount": 50
	            // },
	            "exportConfig": {
	                "menuBottom": "20px",
	                "menuRight": "22px",
	                "menuItems": [{
	                    "icon": App.getGlobalPluginsPath() + "amcharts/amcharts/images/export.png",
	                    "format": 'png'
	                }]
	            }
	        });

	        $('#chart_5').closest('.portlet').find('.fullscreen').click(function() {
	            chart.invalidateSize();
	        });
			// window.setTimeout(function() {
                App.unblockUI('#blockui_category_portlet_body');
            // }, 2000);
		});
        // jQuery('.chart_5_chart_input').off().on('input change', function() {
        //     var property = jQuery(this).data('property');
        //     var target = chart;
        //     chart.startDuration = 0;

        //     if (property == 'topRadius') {
        //         target = chart.graphs[0];
        //     }

        //     target[property] = this.value;
        //     chart.validateNow();
        // });

        // $('#chart_5').closest('.portlet').find('.fullscreen').click(function() {
        //     chart.invalidateSize();
        // });
	}
	function set_year_to_date()
	{
		var formData = "branch_code="+$("#branch_id").val();
		$.post(baseUrl+"dashboard/year_to_date_chart", formData, function(data){			
			var chart = AmCharts.makeChart("chart_1", {
	            "type": "serial",
	            "theme": "light",
	            "pathToImages": App.getGlobalPluginsPath() + "amcharts/amcharts/images/",
	            "autoMargins": false,
	            "marginLeft": 30,
	            "marginRight": 8,
	            "marginTop": 10,
	            "marginBottom": 26,

	            "fontFamily": 'Open Sans',            
	            "color":    '#888',
	            
	            // "dataProvider": [{
	            //     "year": 2009,
	            //     "income": 23.5,
	            //     "expenses": 18.1
	            // }, {
	            //     "year": 2014,
	            //     "income": 34.1,
	            //     "expenses": 29.9,
	            //     "dashLengthColumn": 5,
	            //     "alpha": 0.2,
	            //     "additional": "(projection)"
	            // }],
	            "dataProvider": JSON.parse(data),

	            "valueAxes": [{
	                "axisAlpha": 0,
	                "position": "left"
	            }],
	            "startDuration": 1,
	            "graphs": [{
	                "alphaField": "alpha",
	                "balloonText": "<span style='font-size:13px;'>[[title]] in [[category]]:<b>[[value]]</b> [[additional]]</span>",
	                "dashLengthField": "dashLengthColumn",
	                "fillAlphas": 1,
	                // "title": "Income",
	                "title": "Sales",
	                "type": "column",
	                // "valueField": "income"
	                "valueField": "total_sales"
	            }, {
	                "balloonText": "<span style='font-size:13px;'>[[title]] in [[category]]:<b>[[value]]</b> [[additional]]</span>",
	                "bullet": "round",
	                "dashLengthField": "dashLengthLine",
	                "lineThickness": 3,
	                "bulletSize": 7,
	                "bulletBorderAlpha": 1,
	                "bulletColor": "#FFFFFF",
	                "useLineColorForBulletBorder": true,
	                "bulletBorderThickness": 3,
	                "fillAlphas": 0,
	                "lineAlpha": 1,
	                "title": "Year To Date",
	                "valueField": "to_date_sales"
	            }],
	            "categoryField": "year",
	            "categoryAxis": {
	                "gridPosition": "start",
	                "axisAlpha": 0,
	                "tickLength": 0
	            }
	        });
		});

        $('#chart_1').closest('.portlet').find('.fullscreen').click(function() {
            chart.invalidateSize();
        });
	}
	function startTime(){
	    var today = new Date();
	    var h = today.getHours();
	    var m = today.getMinutes();
	    var s = today.getSeconds();
	    var weekday = new Array(7);
	        weekday[0]=  "Sunday";
	        weekday[1] = "Monday";
	        weekday[2] = "Tuesday";
	        weekday[3] = "Wednesday";
	        weekday[4] = "Thursday";
	        weekday[5] = "Friday";
	        weekday[6] = "Saturday";
	    var d = weekday[today.getDay()];

	    var today = moment();
	    var to = today.format('MMMM  D, YYYY');
	    // add a zero in front of numbers<10
	    m = checkTime(m);
	    s = checkTime(s);

	    //Check for PM and AM
	    var day_or_night = (h > 11) ? "PM" : "AM";

	    //Convert to 12 hours system
	    if (h > 12)
	        h -= 12;

	    //Add time to the headline and update every 500 milliseconds
	    $('#box-time').html(h + ":" + m + ":" + s + " " + day_or_night);
	    $('#box-day').html(d);
	    $('#box-date').html(to);
	    setTimeout(function() {
	        startTime();
	    }, 500);
	}
	function checkTime(i){
	    if (i < 10)
	    {
	        i = "0" + i;
	    }
	    return i;
	}
	function set_month_to_month()
	{
		var formData = "branch_id="+$("#branch_id").val()
						  +"&date="+$("#m2m_date").val();
		$.post(baseUrl+"dashboard/month_to_month_gen",formData,function(data){
			$("#m2m-tbl").html("");
			$("#m2m-tbl").html(data.code);				
		}, 'json');
		
		$("#m2m_date").change(function(e){
			var formData = "branch_id="+$("#branch_id").val()
						  +"&date="+$("#m2m_date").val();
			$.post(baseUrl+"dashboard/month_to_month_gen",formData,function(data){
				$("#m2m-tbl").html("");
				$("#m2m-tbl").html(data.code);				
			}, 'json');
		});
	}
	set_year_to_date();
	set_pie_chart();
	set_category_sales();
	$("#branch_id").change(function(e){
		//event.preventDefault();
		set_year_to_date();
		set_last_gt();
		set_tot_transaction();
		set_today_sales();		
		clear_chart_data();
		set_pie_chart();
		set_category_sales();
		set_top_ten_menu();
		set_month_to_month();
		set_top_ten_toppings();
	});
	get_branch_code_html();
	set_month_to_month();
	function get_branch_code_html()
	{
		$("#_branch_code").replaceWith($("#branch_id"));
	}
	<?php endif; ?>
});
</script>