<body onload="hideall()">
</body>

<?php

$locationsT = array();
$locations = array();
$lines = file('/var/www/AjmanLandProperty/protected/data/locations.csv', FILE_IGNORE_NEW_LINES);

foreach ($lines as $key => $value)
{
    $locationsT[] = str_getcsv($value);
    
}
foreach($locationsT as $key=>$value)
{
	$locations[] = $value[0];

}

?>


<?php

	$this->menu=array(
		array('label'=>'قائمة ملكيات', 'url'=>array('index')),
		array('label'=>'إدارة الملكيات', 'url'=>array('admin')),
	);
?>

<h1 align="right">ملكية جديدة</h1>

<script type='text/javascript'> //on loading the page, hides all elements.
	function hideall()
	{
		$("#LandInfo").hide();
		$("#DeedInfo").hide();
		$("#newland").html("");
		$("#createdeed").hide();		
	}
</script>
<div align="right">
إبحث عن سند أو ادخل رقم سند جديد
<?php
				$url = $this->createUrl("DeedMaster/landsfind");
                $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                    'name'=>'LandID',
                    'source'=>$url,
                    //'source'=>$customerNames, //came from the controller.. the array we constructed of all names, arabic and english
                    // additional javascript options for the autocomplete plugin
                    'options'=>array(
                        'minLength'=>'3',
                    ),
                    'htmlOptions'=>array(
                        'style'=>'height:20px;'
                    ),
                ));
            ?>
<?php
/*
	$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
		'name'=>'LandID',
		'source'=>$LandIDs, 
		'options'=>array('minLength'=>'1',),
		'htmlOptions'=>array('style'=>'height:20px;'),
	));*/
?>
<input type="button" value="حدث" id="makenew">
</div>
<p id="newland" align="right"></p>

<script type='text/javascript'>
	var results = "";
    $("#LandID").bind('dblclick',function() {
	
		var LandID = $("#LandID").val();
		var jLandID = JSON.stringify(LandID);	 
		$.post(
			'<?php echo $this->createUrl("DeedMaster/LandInfo")?>', 
			{ data: jLandID },
			function(data) 
			{
		        result = JSON.parse(data); 
				dealwithresults(result); 
			}
		);
		
	});
	
	function dealwithresults(results)
	{
		if (results.activedeeds!=0)
		{

			for (i in results.activedeeds)
			{

				var deedtable = "<table align='right'><tr><td>رقم ألملكية: <a href='/AjmanLandProperty/index.php/deedMaster/" +results.activedeeds[i].DeedID+"'</a>"+results.activedeeds[i].DeedID+"</td><td>:ملاحظات "+results.activedeeds[i].Remarks+"</td><td>تاريخ الإنشاء: "+results.activedeeds[i].DateCreated+"</td></tr>";
								
				for (j in results.deedDetails)
				{
					deedtable += "<tr><td>"+results.deedDetails[j]+"</td></tr>";
				}
				deedtable +="</table>";
				$('#DeedInfo').html(deedtable);
			}
			$('#LandInfo').show();
			$('#DeedInfo').show();
			$('#newland').html("هناك سند موجودة من قبل!! من فضلك ادخل أرض أخرى.");
			$('#createdeed').hide();
			
			$('#LocationID').val(results.lands[0].LocationID);
			$('#location').val(results.lands[0].location);
			$('#Plot_No').val(results.lands[0].Plot_No);
			$('#Piece').val(results.lands[0].Piece);
			$('#Land_Type').val(results.lands[0].Land_Type);
			$('#length').val(results.lands[0].length);
			$('#width').val(results.lands[0].width);
			$('#TotalArea').val(results.lands[0].TotalArea);
			$('#AreaUnit').val(results.lands[0].AreaUnit);
			$('#North').val(results.lands[0].North);
			$('#South').val(results.lands[0].South);
			$('#East').val(results.lands[0].East);
			$('#West').val(results.lands[0].West);
			$('#Remarks').val(results.lands[0].Remarks);
			
			
		}
		else if(results.lands!=0)
		{
			$('#LandInfo').show();
			$('#DeedInfo').hide();
			$('#newland').html("");
			$('#createdeed').show();
			console.log('land does exist though');
			$('#LocationID').val(results.lands[0].LocationID);
			$('#location').val(results.lands[0].location);
			$('#Plot_No').val(results.lands[0].Plot_No);
			$('#Piece').val(results.lands[0].Piece);
			$('#Land_Type').val(results.lands[0].Land_Type);
			$('#length').val(results.lands[0].length);
			$('#width').val(results.lands[0].width);
			$('#TotalArea').val(results.lands[0].TotalArea);
			$('#AreaUnit').val(results.lands[0].AreaUnit);
			$('#North').val(results.lands[0].North);
			$('#South').val(results.lands[0].South);
			$('#East').val(results.lands[0].East);
			$('#West').val(results.lands[0].West);
			$('#Remarks').val(results.lands[0].Remarks);
			
		}
		else if(results.errors="Land ID not in database")
		{
			$('#LandInfo').show();
			$('#DeedInfo').hide();
			$('#createdeed').show();
			$('#newland').html("الأرض الجديدة، أدخل معلومات جديدة");
			$('#LocationID').val("");
			$('#location').val("");
			$('#Plot_No').val("");
			$('#Piece').val("");
			$('#Land_Type').val("");
			$('#length').val("");
			$('#width').val("");
			$('#TotalArea').val("");
			$('#AreaUnit').val("متر مربع");
			$('#North').val("");
			$('#South').val("");
			$('#East').val("");
			$('#West').val("");
			$('#Remarks').val("");
			
			console.log("land doesn't exist");
		}
		else
			console.log("something wrong!");
	}
	$("#makenew").click(function() {
		console.log('clicked');
		console.log(result);
		console.log(result.activedeeds[i].DeedID);
		
		var r=confirm("Are you sure?");
		if (r==true)
  		{
		
			 $.post(
                        '<?php echo $this->createUrl("DeedMaster/canceldeed")?>', 
                        { data: result.activedeeds[i].DeedID },
                        function(data) 
                        {
                                var e = jQuery.Event("dblclick");
                                $("#LandID").trigger(e);

                        }
                );


  		}
		else
 		{
  			x="You pressed Cancel!";
  		}		
	});
</script>
<input type="hidden" id="LocationID">
<div id="DeedInfo" align="right">
	Deeds here? 
</div>

<div id="LandInfo">
	<table>
		
		<tr>
		<td>المنطقة: <?php
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                    'name'=>'location',
                    //'model'=>$model,
                    //'attribute'=>'Nationality',
                    'source'=>$locations, //came from the controller.. the array we constructed of all names, arabic and english
                    // additional javascript options for the autocomplete plugin
                    'options'=>array(
                        'minLength'=>'1',
                    ),
                    'htmlOptions'=>array(
                        'style'=>'height:20px;'
                    ),
                ));
		?></td>
		<td>الحوض: <input type='text' id='Plot_No' size=1px></td>
		<td>نوع العقار: <input type='text' id='Land_Type' size=3px></td>
		<td>القطعة: <input type='text' id='Piece' size=3px></td>
		</tr>
		
		<tr><td>الطوول: <input type='text' id='length' size=2px></td>
		<td>العرض: <input type='text' id='width' size=2px></td></tr>
		
		<tr><td>المساحة: <input type='text' id='TotalArea' size=3px></td>
		<td>وحدة المساحة: <input type='text' id='AreaUnit' size=5px></td></tr>
		
		<tr><td>شمالا:<input type='text' id='North'></td>
		<td>جنوبا: <input type='text' id='South'></td>
		<td>شرقا: <input type='text' id='East'></td>
		<td>غربا: <input type='text' id='West'></td></tr>
		
		<tr><td colspan='4'>الملاحظات للارض:<input type='text' id='Remarks'></td></tr>
	</table>
</div>
<style>
    #buyernotfound
    {
        color:red;
        display:none;
    }
    
    .removeitem
    {
        float:left;
        cursor:pointer;
        width:20px;
        background-image: url(/AjmanLandProperty/images/delete.png);
        background-repeat: no-repeat;
        background-position: center;
    }

	#form-message
	{
		display:none;
	}
   
</style>

<div id="createdeed" align="right">
	<br><br>
	التاريخ: <input type='text' id='date' value=<?= date("d/m/Y"); ?> size=7px>
	التاريخ (هجري): <input type='text' id='HijriDate' size=7px>
	الملاحظات للملكية: <input type='text' id='deedRemarks' size=25px>
	<h3><br><br>اسم المالك الجديد</h3>

	<?php
		$url = $this->createUrl("contractsMaster/auto");
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
			'name'=>'buyers',
			'source'=>$url,
			//'source'=>$customerNames, //came from the controller.. the array we constructed of all names, arabic and english
			// additional javascript options for the autocomplete plugin
			'options'=>array(
				'minLength'=>'4',
			),
			'htmlOptions'=>array(
				'style'=>'height:20px;'
			),
		));
	?>
	
	<?php
/*
		$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
			'name'=>'buyers',
			'source'=>$customerNames, //came from the controller.. the array we constructed of all names, arabic and english
			// additional javascript options for the autocomplete plugin
			'options'=>array(
				'minLength'=>'4',
			),
			'htmlOptions'=>array(
				'style'=>'height:20px;'
			),
		));*/
	?>
	
	<input type="button" value="اضافة" id="addbuyer"/>
	<div style="clear:both"></div>
	<div id="buyernotfound">الاسم غير موجود هل تريد اضافة اسم  جديد؟<input type="button" value="نعم" onclick="yesnewcustomer()"></div>
	<div style="clear:both"></div>
	<div id="buyerscontainer">
			<div class="onebuyer">
				<div>
					<div style='float:right;width:150px;'>الاسم</div>
					<div style='float:right;'>النسبة</div>
				</div>
				<div style='clear:both;'></div>
			</div>

	</div>
	<br>
	<input type='button' id='save' value='حفظ'>
</div>





<script type='text/javascript'>
	
	
	
	function yesnewcustomer()
	{
		window.open("/AjmanLandProperty/index.php/customerMaster/create?id=1");
		console.log("yes");
	}
	 $("#addbuyer").click(function() {
		console.log('we are in');
		var buyersname = $("#buyers").val();
		console.log(buyersname);
		if(!buyersname) {return;}
		var paramJSON = JSON.stringify(buyersname);
		console.log(paramJSON);
		var url = '<?php echo $this->createUrl("contractsMaster/Searchbuyers"); ?>';
		console.log(url);
		$.post(
			url,
			{ data: paramJSON },
			function(data)
			{
				if(data=="error")
				{
						$("#buyernotfound").css("display", "block");
				}
				else
				{
						$("#buyernotfound").css("display", "none");
						 $("#buyers").val("");
						var customerResult = JSON.parse(data);
						console.log(customerResult);
						insertBuyer(customerResult);
						initRemoveItem();
				}				            
				console.log('out of the data functio'); 
			}
		);

    });
    
    function insertBuyer(customer)
    {
            var checker = "onebuyer" + customer.CustomerID;
            if($("#"+checker).html()) {return;}

            var html = "";
            html += " <div class='onebuyer' id='onebuyer"+customer.CustomerID+"'> ";
            html += "       <div> ";
            html += "           <div style='float:right;width:150px;' id='cname"+customer.CustomerID+"'><a target='_blank' href='/AjmanLandProperty/index.php/CustomerMaster/update/"+customer.CustomerID+"'>" +customer.CustomerNameArabic+"</a></div> ";
            html += "           <div style='float:right;'><input type='text' size='2' id='onebuyershare"+customer.CustomerID+"'/></div> ";
            html += "          <div  style='float:right;' class='removeitem' id='" + customer.CustomerID+ "'>&nbsp;</div>";
            html += "       </div> ";
            html += "       <div style='clear:both;'></div> ";
            html += " </div> ";
            $("#buyerscontainer").append(html);
    }
    
    function initRemoveItem()
    {
            $(".removeitem").click(function() {
                    var container = $(this).parent().parent(); 
                    //console.log($(container).attr("id"));
                    $(container).remove();
            });
 
            
    }
    
    
    $("#save").click(function() {
		
		var equals="no";
		var total=0;
		var buyers  = new Array();
		var landid = $("#LandID").val();
		var LocationID = $('#LocationID').val();
		var location = 	$('#location').val();
		var Plot_No =$('#Plot_No').val();
		var Piece = $('#Piece').val();
		var Land_Type =	$('#Land_Type').val();
		var len = $('#length').val();
		var width =	$('#width').val();
		var TotalArea =	$('#TotalArea').val();
		var AreaUnit =	$('#AreaUnit').val();
		var North =	$('#North').val();
		var South =	$('#South').val();
		var East = $('#East').val();
		var West = $('#West').val();
		var Remarks =$('#Remarks').val();
		var CreatedDate = $('#date').val();
		var HijriDate = $('#HijriDate').val();
		var deedRemarks = $('#deedRemarks').val();
		
		$(".onebuyer").each(function() {
                if($(this).attr("id"))
                {
                        var buyerid = $(this).attr("id");
                        buyerid = buyerid.replace("onebuyer", "");
                        var inputshare = "onebuyershare" + buyerid;
                        var sharevalue = $("#"+inputshare).val();
                        var cname = $("#cname"+buyerid).html();
                        var onebuyer = {
                                    buyerid:buyerid,
                                    shareval:sharevalue,
                                    cname:cname,
									};
                        buyers.push(onebuyer);
                        sharevalue=sharevalue.replace('%','');
                        total += +(sharevalue.replace(/,/,'.'));
                        console.log(total);
                }
        });   
        
        if (total>100 || total<100)
        {
			if (total==111)
			{
				var equals = "yes";
				
			}
			else
			{
				alert('تأكد تحديد حصة أو حدد الجانبين في الوكيل أو الوسيط');
				return;
			}
			
		}
		
        var params = {
			buyers: buyers,
			landid: landid,
			LocationID: LocationID,
			location: location,
			Plot_No: Plot_No,
			Piece: Piece,
			Land_Type: Land_Type,
			len: len,
			width: width,
			TotalArea: TotalArea,
			AreaUnit: AreaUnit,
			North: North,
			South: South,
			East: East,
			West: West,
			Remarks: Remarks,
			CreatedDate: CreatedDate,
			HijriDate: HijriDate,
			deedRemarks: deedRemarks,
			equals: equals,
		}
		
		//console.log(params);
		
		var paramJSON = JSON.stringify(params);	
        $.post(
		            '<?php echo $this->createUrl("DeedMaster/SaveDeed")?>',
		            { data: paramJSON },
		            function(data)
		            {
                        var result = JSON.parse(data);
                        if (result.slice(0,7)=="success")
                        {
							
							var url = '<?php echo $this->createUrl("deedMaster/Print/"); ?>';
							url+="/"+result.slice(7,result.length);
							var DeedPrint = window.open(url);
						    
						    window.location ='<?php echo $this->createUrl("deedMaster/admin")?>';
						    
							
						}
						else
						{
							console.log(result);
						}
					}
				)
        
	});
	
    
</script>