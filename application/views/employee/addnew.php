<?php

/**
 * @author Aaron P. Ruanto
 * @copyright 2013
 */
$post_limits = $this->input->post("limit") ? $this->input->post("limit") : 0;

/** RESET DEDUCTION SESSION */
$this->session->unset_userdata("deductions");
$this->session->set_userdata("deductions",array());

$this->session->unset_userdata("income");
$this->session->set_userdata("income",array());

$this->session->unset_userdata("salary_base");

$employeeid = $this->input->post("employeeid");

if($employeeid){
    # echo $employeeid;
 /** pass employee information to an array */
    $employee = $this->employee->loadallemployee(array("employeeid"=>$employeeid));
    $this->session->set_userdata("personalinfo",$employee);
    $this->session->set_userdata("salary_base",$this->employee->loadfieldemployee("income_base",$employeeid));
    
 /** pass deduction to an array */   
    $deductions = $this->employee->loademployee_deduction($employeeid);
    $this->session->set_userdata("deductions",$deductions);
 /** pass income to an array */   
    $income = $this->employee->loademployee_income($employeeid);
    $this->session->set_userdata("income",$income);
    
}else{
    $this->session->unset_userdata("personalinfo");   
}
# echo $this->employee->loadfieldemployee("income_base",$employeeid);
?>
<ul class="minitab">
    <li class="selected"><a href="#" view='employee/personal_info'>PERSONAL INFORMATION</a></li>
<?
# if($employeeid){
?>
    <li><a href="#" view='employee/salary_info'>SALARY</a></li>
    <li><a href="#" view='employee/deduction_info'>DEDUCTIONS</a></li>
    <li><a href="#" view='employee/income_info'>INCOME</a></li>
    <li><a href="#" view='employee/loans_info'>LOANS</a></li>
    <li><a href="#" view='employee/rate_info'>RATES</a></li>
    <li><a href="#" view='employee/schedule_info'>SCHEDULE</a></li>
<?
# }
?>    
</ul>
<div class="minitabcontent" style="text-align: left;">
</div>
<script>
var lims = Number("<?=$post_limits?>");
$("ul[class='minitab'] li a").click(function(){
   var objlink = $(this);
   var link = $(this).attr("view");
   var lastlink = '';
   $("ul[class='minitab'] li").each(function(){
      if($(this).attr("class")=="selected") lastlink = $(this).find("a:eq(0)").attr("view");
   });
  
   updateinformation(lastlink,function(){
     $("ul[class='minitab'] li").each(function(){
          $(this).attr("class", "");
     });
     $(objlink).parent().attr("class", "selected");
     doloadviewminitab(link);
   }); 
});
function doloadviewminitab(link){
   $("div[class='minitabcontent']").html("<img src='<?=base_url()."images/loading.gif"?>'> Loading, please wait..");
   centerThis("shadeblock"); 
   $.ajax({
        url: "<?=site_url('main/siteportion')?>",
        type: "POST",
        data: {view: link},
        success: function(msg){
            $("div[class='minitabcontent']").html(msg);
            centerThis("shadeblock");
        }
    }); 
}
function updateinformation(lastlink,funchere){
  
   var cancontinue = true; 
   switch(lastlink){
     case "employee/personal_info":
          if($("#employeeid").val()==""){
            alert("Employee ID is required.");
            $("#employeeid").focus();
            return;
          }else if($("#lname").val()==""){
            alert("Last name is required.");
            $("#lname").focus();
            return;
          }else if($("#fname").val()==""){
            alert("First name is required.");
            $("#fname").focus();
            return;
          }
        
        var form_data = {
            job : lastlink,
            employeeid: $("#employeeid").val(),
            lname: $("#lname").val(),
            fname: $("#fname").val(),
            mname: $("#mname").val(),
            cityaddr: $("#cityaddr").val(),
            months_b: $("#months_b").val(),
            days_b: $("#days_b").val(),
            years_b: $("#years_b").val(),
            gender: $("#gender").val(),
            emptype: $("#emptype").val(),
            employmentstat: $("#employmentstat").val(),
            bplace: $("#bplace").val(),
            mobile: $("#mobile").val(),
            citytelno: $("#citytelno").val(),
            email: $("#email").val(),
            maxregular: $("#maxregular").val(),
            maxparttime: $("#maxparttime").val(),
            month_employed_b: $("#month_employed_b").val(),
            days_employed_b: $("#days_employed_b").val(),
            years_employed_b: $("#years_employed_b").val()
            };
     break;
     case "employee/salary_info":
        var form_data = {
              job : lastlink,
              salary_type:$("#salary_type").val(),
              mon1: $("table[class='input_details'] input[tag='1'][tp='mon']").val(),
              mon2: $("table[class='input_details'] input[tag='2'][tp='mon']").val(),
              mon3: $("table[class='input_details'] input[tag='3'][tp='mon']").val(),
              dail1: $("table[class='input_details'] input[tag='1'][tp='dail']").val(),
              dail2: $("table[class='input_details'] input[tag='2'][tp='dail']").val(),
              dail3: $("table[class='input_details'] input[tag='3'][tp='dail']").val(),
              hr1: $("table[class='input_details'] input[tag='1'][tp='hr']").val(),
              hr2: $("table[class='input_details'] input[tag='2'][tp='hr']").val(),
              hr3: $("table[class='input_details'] input[tag='3'][tp='hr']").val(),
              min1: $("table[class='input_details'] input[tag='1'][tp='min']").val(),
              min2: $("table[class='input_details'] input[tag='2'][tp='min']").val(),
              min3: $("table[class='input_details'] input[tag='3'][tp='min']").val()
            };
     break;
     case "employee/deduction_info":
       
        var form_data = {
            job : lastlink
            };  
     break;
     case "employee/income_info":
        var form_data = {
            job : lastlink
            };  
     break;
     case "employee/schedule_info":
        var timesched = "";
           
           $("tr[tag='sched']").each(function(){
             if(cancontinue){
             /** from time */
             var ftime = reform_string($(this).find("td:eq(2)").find("select:eq(0)").val(),2,"0","left") + ":" + reform_string($(this).find("td:eq(2)").find("select:eq(1)").val(),2,"0","left") + ":00 " + $(this).find("td:eq(2)").find("select:eq(2)").val();
             /** to time */
             var ttime = reform_string($(this).find("td:eq(3)").find("select:eq(0)").val(),2,"0","left") + ":" + reform_string($(this).find("td:eq(3)").find("select:eq(1)").val(),2,"0","left") + ":00 " + $(this).find("td:eq(3)").find("select:eq(2)").val();
             /** type */
             var ttype = $(this).find("td:eq(4)").find("select:eq(0)").val();
             
             
             /** validate time */
             if(validate_time(ftime) && validate_time(ttime)){
                timesched += timesched!='' ? "|" : "";
                timesched += $(this).attr("code"); /** DAY OF WEEK */
                timesched += "~u~";
                timesched += ftime + "-" + ttime; /** START TIME TO END TIME */
                timesched += "~u~";
                timesched += ttype; /** TYPE */
             }else if(validate_time(ftime) && !validate_time(ttime)){
                alert("Invalid time");
                cancontinue = false;
             }else if(!validate_time(ftime) && validate_time(ttime)){
                alert("Invalid time");
                cancontinue = false;
             } 
             }
           });
          var form_data = {
            job : lastlink,
            timesched: timesched
          };      
     break;
   }
  
   if(cancontinue){
 
   $.ajax({
     url: "<?=site_url("employee_/validateinfo")?>",
     type: "POST",
     data:form_data,
     success: function(msg){
        var vmessage = $(msg).find("message:eq(0)").text();
        var vstatus = $(msg).find("status:eq(0)").text();
        /** Display message */
          if(!$.isFunction(funchere)) alert(vmessage);
        /** 
          Status Legend
           1 : Information saved.
           2 : Problem saving.
         */
        if(vstatus==1){
            if($.isFunction(funchere)) funchere();
            else pagerefresher(lims); 
        }else return;
     }
   });
   }
}

doloadviewminitab($("ul[class='minitab'] li[class='selected'] a").attr("view"));
</script>