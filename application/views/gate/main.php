<?
/**
* @author justin (with e)
* @copyright 2018
*/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
?>
<div class="form_row">
	<div class="gate-header">
		ATTENDANCE MANAGEMENT SYSTEM
	</div>
</div>

<div class="form_row">&nbsp;</div>

<div class="form_row">
	<div class="login-gate">
		<table width="100%" cellspacing="15" cellpadding="15" border="0">
			<tbody>
				<tr>
					<!-- left side -->
					<td width="25%" valign="top" rowspan="5">
						<table style="width: 100%;" border="0">
                    		<tbody>
                    			<tr>
			                        <td class="text-center">
			                            <img src="<?=base_url()?>images/ICAlogo.bmp" class="img-user" align="middle">
			                        </td>
			                    </tr>

		                    	<tr><td>&nbsp;</td></tr>

			                    <tr>
			                        <td class="text-center">
			                        	<form id="frm-attendance-log">

		                        		<input type="hidden" name="access" value="<?=$allowed?>">
			                        	<input type="hidden" name="username" value="<?=$username?>">
			                        	<input type="hidden" name="time">

			                            <input type="password" class="text-center" style="width: 100%; height:60px; font-size: 25px;" name="userid" value="" placeholder="" autocomplete="false">

			                            </form>
			                        </td>
			                    </tr>  

			                    <tr><td>&nbsp;</td></tr>
			                    
			                    <tr hidden>
			                        <td>
			                            <span id="container-alert">
			                            	<div class="alert alert-success">
			                            		<strong>Closed!</strong>
			                            	</div>
			                            </span>
			                        </td>
			                    </tr>
		                </tbody>
            		</table>
					</td>

					<!-- right side -->
					<td valign="top">
						<table width="100%" border="0">
							<tbody>
								<tr>
			                        <td class="new-login opacity">
			                            <div class="clock">
			                                <div id="Date"><?=date("D d F Y")?></div>
			                                <br><br>
			                                <ul>
			                                    <li class="hours" id="hours">00</li>
			                                    <li id="point">:</li>
			                                    <li class="minute" id="min">00</li>
			                                    <li id="point">:</li>
			                                    <li class="sec" id="sec">00</li>
			                                    <li>&nbsp;</li>
			                                    <li class="apm" id="periods">AM</li>
			                                </ul> 
			                            </div> 
			                        </td>
			                    </tr>

			                    <tr><td>&nbsp;</td></tr>

			                    <tr>
			                        <td class="new-login opacity" style="vertical-align: middle; text-align: center; height: 60px;">
			                            <div style="font-size: 30px;" class="text-default" id="container-response">
			                            	Good day, please sign in
			                            </div>
			                        </td>
			                    </tr>

			                    <tr><td>&nbsp;</td></tr>

			                    <tr>
			                    	<td>
			                    		<table width="100%" class="tbl-gate-result">
			                    			<thead>
			                    				<tr>
			                    					<th width="20%">Date</th>
			                    					<th width="15%">Time</th>
			                    					<th width="30%">Name</th>
			                    					<th width="15%">Type</th>
			                    					<th width="20%">User</th>
			                    				</tr>
			                    			</thead>

			                    			<tbody>
			                    			</tbody>

			                    			<tfoot>
			                    				<tr class="no-data">
			                    					<td colspan="5">
			                    						No data available.
			                    					</td>
			                    				</tr>
			                    			</tfoot>

			                    		</table>
			                    	</td>
			                    </tr>
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<script type="text/javascript">
	function loadLogAttendanceHistory(){
		$.ajax({
			url : "<?=site_url("gate_/showLogAttendanceHistory")?>",
			type : "POST",
			data : { username : $("input[name='username']").val() },
			success : function(response){
				$(".tbl-gate-result").find("tfoot").hide();
				$(".tbl-gate-result").find("tbody").html(response);
			}
		});
	}

	function showCurrentDate(){
		$.ajax({
			url : "<?=site_url("gate_/getCurrentDate")?>",
			type : "POST",
			data : {},
			success : function(response){
				$("#Date").html(response);
			}
		});
	}

	function setToDefaultDisplay(){
		setTimeout(function(){
			$(".text-default").html("Good day, please sign in");
			$(".text-default").attr('class', 'text-default');
			$(".img-user").attr("src", "<?=base_url()?>images/ICAlogo.bmp");
		}, 2000);

		setTimeout(function(){
			$("input[name='userid']").val("");
			$("input[name='userid']").removeAttr("readonly");
			$("input[name='userid']").focus();
		}, 1000);
	}

	function doReconnect(){
		setInterval(function(){
			$.ajax({
				url : "<?=site_url("gate_/testConnection")?>",
				type : "POST",
				data : {},
				async : false,
				success : function(error){
					location.reload();
				},
				error : function(response, ajax_option, error_alert){
				}
			});
		}, 5000);
	}

	$("#frm-attendance-log").submit(function(event){
		if(!$("input[name='userid']").val()){
			alert("User ID is required.");
			return;
		}
		
		var time = $(".hours").html() +":"+ $(".minute").html() +":"+ $(".sec").html() +" "+ $(".apm").html();
		$("input[name='time']").val(time);
		
		var formdata = $(this).serialize();
		$.ajax({
			url : "<?=site_url("gate_/logUserAttendance")?>",
			type : "POST",
			data : formdata,
			dataType : "json",
			success : function(response){
				response.message = decodeURIComponent(escape(response.message));
				console.log(response);
				$(".text-default").html(response.message.replace('Ã‘', 'Ñ'));
				$(".text-default").attr('class', 'text-default '+ ((response.is_error) ? "error" : "success"));
				if(!response.is_error) $(".img-user").attr("src", response.img_src);

				$("input[name='userid']").attr("readonly","true");
				setToDefaultDisplay();
				loadLogAttendanceHistory();
			},
			error : function(response, ajax_option, error_alert){
				$(".text-default").html("No Internet");
				$(".text-default").attr('class', 'text-default error');

				doReconnect();
			}
		});
		event.preventDefault();
	});

	var xmlHttp;
	function srvTime(){
	    try {
	        //FF, Opera, Safari, Chrome
	        xmlHttp = new XMLHttpRequest();
	    }
	    catch (err1) {
	        //IE
	        try {
	            xmlHttp = new ActiveXObject('Msxml2.XMLHTTP');
	        }
	        catch (err2) {
	            try {
	                xmlHttp = new ActiveXObject('Microsoft.XMLHTTP');
	            }
	            catch (eerr3) {
	                //AJAX not supported, use CPU time.
	                alert("AJAX not supported");
	            }
	        }
	    }
	    xmlHttp.open('HEAD',window.location.href.toString(),false);
	    xmlHttp.setRequestHeader("Content-Type", "text/html");
	    xmlHttp.send('');
	    return xmlHttp.getResponseHeader("Date");
	} 
	var st = srvTime(); 
	
	$(document).ready(function(){
		setInterval(function(){
            var date_time = new Date();
            [time, apm] = date_time.toLocaleTimeString().split(" ");
            [hour, min, sec] = time.trim().split(":");

            hour = (hour < 10) ? "0"+ hour : hour;
            $(".hours").html(hour);
            $(".minute").html(min);
            $(".sec").html(sec);
            $(".apm").html(apm);

            if(hour == 12) showCurrentDate();
        }, 1000);

		loadLogAttendanceHistory();
		$("input[name='userid']").focus();
	});
</script>