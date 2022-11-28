    $("#generate").click(function(){
        var cutoff = $("#att_cutoff").val();
        var teachingtype = $("#tnt").val();
        var employeeid = $("select[name='employeeid']").val();
        window.open($("#site_url").val() + "/forms/generateConfirmedAttendance" + "?cutoff="+cutoff+"&teachingtype="+teachingtype+"&employeeid="+employeeid); 
    });

    $("#finalize").click(function(){
    	var teachingtype = $("#tnt").val();
    	var cutoff = $("#att_cutoff").val();
    	cutoff = cutoff.split(',');
	    $('.pdata').each(function() {
	        var eid = $(this).find(".pdataid").text();
	        $.ajax({ 
	            url      : $("#site_url").val() + "/employeemod_/loadmodelfunc",
	            type     : "POST",
	            data     : {
	                            model: "payrollconfirm",
	                            tnt  : teachingtype,
	                            dfrom: cutoff[0],
	                            dto  : cutoff[1],
	                            eid  : eid
	                        },
	            success  : function(msg){
	                var data = $.parseJSON(msg);
	                if(data[0])  $(".pdata,.pdept").remove();    
	                $("#finalize").hide();
	                $("#cmsg").text(data[1]);
	                
	            }
	        });
	    });
	});