<?
    $empDefcount = 0;
    $username = $this->session->userdata('username');
    $utype = $this->session->userdata('usertype');
    $deptid = $this->extras->getHeadOffice($username);
    $empDefcount = $this->employeemod->employeedeficiencynotif('','',$deptid,'',true)->num_rows();
    // echo "<pre>"; print_r($this->db->last_query()); die;
    $employee = $this->employee->loadallemployee('','','','','','');
    $depthead = 0;
?>
<style type="text/css">
       .panel {
    border: 5px solid #0072c6 !important;
    box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
    margin-bottom: 49px !important;
}
 #pyear_chosen{
          width: 100px !important;
          padding-bottom: 5px;
        }
</style>
<input type="hidden" id="department" value="<?= $deptid ?>">
<input type="hidden" id="userid" value="<?= $username ?>">
<input type="hidden" id="usertype" value="<?= $utype ?>">
<input type="hidden" id="depthead" value="<?= $depthead ?>">

<div id="content"> 
    <?php if($depthead > 0){ ?><!-- Content start -->
    <div class="navBtn" style="margin: 20px; float: left;">
        <button type="button" id="loe" class="btn btn-warning" style="color: black" isActive="1"><b>List of Employees</b></button>&emsp;
        <button type="button" id="def" class="btn btn-secondary" isActive="0"><b>List of Clearance</b></button>
    </div>
   <div style='float:right;padding-right:1%;padding-top:2%;font-size: 10px;'>
  <!-- <span style="font-size: 12px;font-weight: bold;">Year</span> &nbsp; <select class="chosen" id="pyear" style="width: 100px;"><?=$this->payrolloptions->periodyear();?></select>&nbsp; -->
    <a href='#modal-view' data-toggle='modal' style="font-weight:bold;text-decoration: underline;font-size:1.5em;margin-bottom: 10px;margin-right: 5px;" id="printDeficiencyReport">Print Clearance Report</a>
<?

    if($empDefcount !=0)
    {?>
        |<a href='#modal-view' data-toggle='modal' style="font-weight:bold;text-decoration: underline;font-size:1.5em" id="deficiencyUpdates">Clearance Updates 
        <div class='notifdiv'><i class='glyphicon glyphicon-bell large' style='color:black'></i><span class='notifcount'><b><?=$empDefcount?></b></span></div>
        </a>
    <?}
?>
</div>
<div class="widgets_area">
    <div class="row" id="listofemp">
        <div class="col-md-12">
            <div class="panel animated fadeIn delay-1s">
               <div class="panel-heading" style="background-color: #0072c6;"><h4><b>List of Employees</b></h4></div>

               <div class="panel-body">
                   <table class="table table-striped table-bordered table-hover" id="ListTable">
                        <thead style="background-color: #0072c6;">
                            <tr>
                                <th class="col-md-1">#</th>
                                <th class="sorting_asc">Employee</th>
                                <th>Fullname</th>
                                <th>Type</th>
                                <th>Department</th>
                            </tr>
                        </thead>
                        <tbody id="employeelist">
                        <?
                            if(count($employee)>0){
                                $o=1;
                                foreach($employee as $row){
                                    if($username != $row['employeeid']):
                                ?>
                                  <tr employeeid='<?=$row['employeeid']?>' positionid='<?=$row['position']?>' deptid='<?=$row['deptid']?>' style="cursor: pointer;">
                                    <td><?=$o?></td>
                                    <td><?=$row['employeeid']?></td>
                                    <td id="tdFName"><?=($row['lname'] . ", " . $row['fname'] . ($row['mname']!="" ? " " . substr($row['mname'],0,1) . "." : ""))?></td>
                                    <td><?=$this->extensions->getEmployeeTeachingType($row['employeeid'])?></td>
                                    <td><?= $this->extras->getemployeedepartment($row['deptid']) ?></td>
                                  </tr>
                                <?  
                                    endif;
                                $o++;  
                                }
                            }
                        ?>                                        
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row" id="deficiency">
        <div class="col-md-12">
            <div class="panel animated fadeIn delay-1s">
               <div class="panel-heading" style="background-color: #0072c6;"><h4><strong>Clearance</strong></h4></div>
               <div class="panel-body" id="data_table">
                   
                </div>
            </div>
        </div>
    </div>
</div>  
<?php }else{ ?>  
    <div class="widgets_area">
            <div class="row">
                <div class="col-md-12" id="a_history">
<!--                     <div class="panel animated fadeIn delay-1s">
                       <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Employee Clearance History</b></h4></div>
                       <div class="panel-body">
                          <div   style="padding-bottom: 31px;"></div>
                        </div>
                    </div> -->
                </div>
            </div>
       </div>
<?php } ?>
</div>
          

<script>
var toks = hex_sha512(" ");
$(document).ready(function(){


    var depthead = $("#depthead").val();
    if(depthead == 0){
        loadDeficiencyHistorys($("#userid").val(), "def", "head");
    }else{
        loaddeficiencydata();
         var table = $('#ListTable').DataTable({
        });
        new $.fn.dataTable.FixedHeader( table );
    }

});
function loadDeficiencyHistorys(employeeid, def='', head=''){
    $("#a_history").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
    $.ajax({
        url      :   "<?=site_url("deficiency_/loadDeficiencyHistory")?>",
        type     :   "POST",
        data     :   {employeeid :  GibberishAES.enc(employeeid , toks), def: GibberishAES.enc(def , toks), head: GibberishAES.enc(head , toks), toks:toks},
        success  :   function(msg){
            $("#a_history").html(msg);
            if("<?=$this->session->userdata('canwrite')?>" == 0) $("#a_history").find(".btn").css("pointer-events", "none");
            else $("#a_history").find(".btn").css("pointer-events", "");
        }
    });
}
$("#loe").click(function(){
    var isActive = $(this).attr("isActive");
    if(isActive != "1"){
        $(this).removeClass().removeAttr('isActive').addClass("btn btn-warning").attr('isActive', '1');
        $("#def").removeClass().removeAttr('isActive').addClass("btn btn-secondary").attr('isActive', '0');
        $("#deficiency").fadeOut('slow');
        setTimeout(function(){
            $("#listofemp").fadeIn('slow');
            $("#printBtn").fadeIn('slow');
        }, 500)
        
    }
})

$("#def").click(function(){
    var isActive = $(this).attr("isActive");
    if(isActive != "1"){
        $(this).removeClass().removeAttr('isActive').addClass("btn btn-warning").attr('isActive', '1');
        $("#loe").removeClass().removeAttr('isActive').addClass("btn btn-secondary").attr('isActive', '0');
        $("#deficiency").fadeIn('slow');
        $("#listofemp").fadeOut('slow');
        $("#printBtn").fadeOut('slow');
    }
})

$("#employeelist tr").click(function(){
   // console.log($(this).find('td#tdFName').html());

   if($(this).attr("employeeid")){
       var form_data = {
        deptDef :  GibberishAES.enc(true, toks),
        employeeid : GibberishAES.enc($(this).attr("employeeid") , toks) ,
        positionid :  GibberishAES.enc($(this).attr("positionid"), toks),
        fname :  GibberishAES.enc($(this).find('td#tdFName').html(), toks),
        deptid :  GibberishAES.enc($(this).attr("deptid") , toks),
        concerneddept :  GibberishAES.enc($("#department").val() , toks),
        view:  GibberishAES.enc("deficiency/add_emp_deficiency.php", toks),
        toks:toks
       }; 
       $.ajax({
          url : "<?=site_url("main/siteportion")?>",
          type: "POST",
          data: form_data,
          success: function(msg){
            $("#content").html(msg);
          }
       });
   }
});  
$('select').chosen();

$("#deficiencyUpdates").click(function(){
    $("#modal-view").find("h3[tag='title']").text("List of Clearance Update");
    $("#modal-view").find(".modal-dialog").removeClass("modal-md").addClass("modal-lg");
    $("#button_save_modal").text("Generate");
    var form_data = {
        folder :  GibberishAES.enc("deficiency", toks),
        page   :  GibberishAES.enc("matured_list", toks),
        toks:toks
    };
    $.ajax({
        url: "<?=site_url('employee_/viewModal')?>",
        type: "POST",
        data: form_data,
        success: function(msg){
            $("#modal-view").find("div[tag='display']").html(msg);
        }
    });  
});

$("#printDeficiencyReport").click(function(){
    $("#modal-view").find("h3[tag='title']").text("Clearance Report");
    $("#button_save_modal").text("Generate");
    var form_data = {
        folder :  GibberishAES.enc("deficiency", toks),
        page   :  GibberishAES.enc("deficiency_report", toks),
        toks:toks
    };
    $.ajax({
        url: "<?=site_url('employee_/viewModal')?>",
        type: "POST",
        data: form_data,
        success: function(msg){
            $("#modal-view").find("div[tag='display']").html(msg);
        }
    });  
});

function loaddeficiencydata(empside=''){
    var deptid = $("#department").val();
    $.ajax({
        url: "<?= site_url('deficiency_/loadDeficiency')?>",
        type: "POST",
        data: {deptid: GibberishAES.enc( deptid, toks),toks:toks},
        success:function(response){
            $("#data_table").html(response);
            if(empside == ''){
                $("#deficiency").fadeOut('slow');
            }
        }
    });
}

$(document).on("click", '#del-submit', function(){
    var infotype = "code_deficiency";
    var id = $(this).attr('tagkey');
    $.ajax({
        url: "<?=site_url('deficiency_/deleteRow')?>",
        type: "POST",
        data: {id: GibberishAES.enc( id, toks), infotype: GibberishAES.enc(infotype, toks),toks:toks},
        success: function(msg){
            loaddeficiencydata('empside');
            $(".del-close").click();    
        }
    });
});
</script>