<?php
$empid = $fullname='';
$date = "~~~";
#print_r($this->extras->showrequestform()); 
$loopcounter = $num_rows = 0;
$query = $this->db->query("SELECT a.*,CONCAT(b.lname,', ',b.fname,' ',b.mname) AS fullname FROM employee_schedule_adjustment a INNER JOIN employee b ON(a.`employeeid` = b.`employeeid`) where a.employeeid='{$employeeid}'");
if ($query->num_rows()>0) {
  $empid = $query->row(0)->employeeid;
  $fullname = $query->row(0)->fullname;
}
else
{
  $employeeinfo = $this->db->query("SELECT employeeid,CONCAT(lname,', ',fname,' ',mname) as fullname FROM employee WHERE employeeid ='$employeeid'");
  $empid = $employeeinfo->row(0)->employeeid;
  $fullname = $employeeinfo->row(0)->fullname; 
}

?>
<?if($chkbox == "chkemp"){?>
 <h4><b><?=Globals::_e($empid)." - ".Globals::_e($fullname)?></b></h4>
<a class="btn btn-primary" href="#modal-view" data-toggle='modal' id="addadjustment"><i class="glyphicon glyphicon-plus-sign"></i> Add Adjustment</a>
<a class="btn btn-primary pull-right" href="#modal-view" data-toggle='modal' id="addremarks"><i class="glyphicon glyphicon-plus-sign"></i> New Remarks</a>
<div class="well-content" style='border: transparent !important;'><br />
<table id="adjustment_datatable" class="table table-striped table-bordered table-hover datatable" >
    <thead style="background-color: #0072c6;">
      <tr >
        <th  class="col-md-2 align_center">Details</th>
        <th class="col-md-2 align_center">Date</th>
        <!-- <th class="col-md-2">Start Time</th>
        <th class="col-md-2">End Time</th> -->
        <th class="col-md-3 align_center">Remarks</th>
        <th class="col-md-2 align_center">Edited By</th>
        <th class="col-md-2 align_center">Timestamp</th>
      </tr>
    </thead>  
      <tbody>
        <!-- Updated by Justin (with e) -->
        <?
        foreach ($query->result() as $key){
          // echo'<pre>'var_dump($key);
          // $remarks = $this->extras->findRemarks($key->remarks);
          $key->employeeid = Globals::_e($key->employeeid);
          if($date != $key->cdate){
            $loopcounter = 1;
            $cdate = $key->cdate;
            $num_rows = $this->db->query("SELECT a.*,CONCAT(b.lname,', ',b.fname,' ',b.mname) AS fullname FROM employee_schedule_adjustment a INNER JOIN employee b ON(a.`employeeid` = b.`employeeid`) where a.employeeid='{$employeeid}' AND a.cdate = '$cdate'")->num_rows();
            }
            
            
            if($loopcounter == $num_rows){
        ?>
          <tr>
              <td class="align_center"><a class="btn blue shows" href="#modal-view" data-toggle='modal' id="<?=$key->id?>" eid="<?=$key->employeeid?>" class='<?=$key->employeeid?>'><i class="icon-eye-open"></i></a></td>
              <td class="align_center"><?=Globals::_e($key->cdate)?></td>
              <td class="align_center"><?=$key->remarks?Globals::_e($this->extras->findRemarks($key->remarks)):Globals::_e($key->remarks)?></td>
              <td class="align_center"><?=Globals::_e($key->editedby)?></td>
              <td class="align_center"><?=Globals::_e($key->timestamp)?></td>
          </tr>
        <?}
        $loopcounter++;
        $date = $key->cdate;
      }?>
      </tbody>
</table>
</div>
<?}else if($chkbox == "chkdate"){?>
<div class="well-content" style='border: transparent !important;'>
<table id="adjustment_datatable" class="table table-striped table-bordered table-hover datatable">
    <thead>
      <tr>
        <th class="col-md-1" style="text-align:center;">Adjustment</th>
        <th class="col-md-7">Name</th>
      </tr>
    </thead>  
      <tbody></tbody>
</table>
</div>
<?}else{?>
<div class="well-content" style='border: transparent !important;'>
<table id="adjustment_datatable" class="table table-striped table-bordered table-hover datatable">
    <thead>
      <tr>
        <th class="col-md-1" style="text-align:center;">Adjustment</th>
        <th class="col-md-7">Name</th>
        <th class="col-md-2">Start Time</th>
        <th class="col-md-2">End Time</th>
      </tr>
    </thead>  
      <tbody></tbody>
</table>
</div>
<?}?>
<script>
var toks = hex_sha512(" ");
if("<?=$this->session->userdata('canwrite')?>" == 0) $("#addadjustment, #addremarks").css("pointer-events", "none");
else $("#addadjustment, #addremarks").css("pointer-events", "");

var ulist;
var chkbox = "";

$(".shows").click(function()
{
  var id = GibberishAES.enc($(this).attr('id'), toks)
  var eid = GibberishAES.enc($(this).attr('eid'), toks)
  viewAdjustment(id,eid);
});
// new function for view addjustment
// justin (with e)
function viewAdjustment(id,eid){
  
  $("#modal-view").find("h3[tag='title']").html("View Adjustment"); 
  $("#modal-view").find("div[tag='display']").html("<img src='<?=base_url()?>images/loading.gif' /> Loading, please wait...");
  $("#button_save_modal").hide();
  $.ajax({
           url:"<?=site_url("process_/viewNewAdjustment")?>",
           data: {
                   bID : id,
                   eid : eid,
                   toks: toks
                 },
           type: "POST",
           success: function(msg){
              $("#modal-view").find("div[tag='display']").html(msg);
           }
        }); 
}
// end new function for view addjustment

if("<?=$chkbox?>" == "chkemp"){
$(function(){
    /*ulist = $('#adjustment_datatable').dataTable({
        "sPaginationType": "bootstrap",
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": "<?=site_url("process_/dbadjustmentlist/{$employeeid}")?>",
        "sDom": "<'tableHeader'<l><'clearfix'f>r>t<'tableFooter'<i><'clearfix'p>>",
        "iDisplayLength": 15,
        "asSorting": [[ 1, "desc" ]],
        "aoColumns": [
    		{ "bSortable": false },
    		{ "bSortable": true },
            { "bSortable": true },
            { "bSortable": true },
            { "bSortable": true },
            { "bSortable": true }
    	],
        "sServerMethod": "POST",
        "fnDrawCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull) {
        // codes here 
        
            $("a[tag='edit_d']").click(function(){
               var adateid = $(this).attr("adateid");
               dotoggleadjustment("Edit Adjustment",{job:"edit",adateid:adateid,uid:"<?=$employeeid?>"});
            });
            $("a[tag='delete_d']").click(function(){
               var ans = confirm("Are you sure you want to continue?");
               if(ans){ 
               var adateid = $(this).attr("adateid");
               $.ajax({
                   url:"<?=site_url("process_/saverequest")?>",
                   data: {uid:"<?=$employeeid?>",job:1,adateid:adateid},
                   type: "POST",
                   success: function(msg){
                     ulist.fnDraw();
                   }
               });
               }
            });
            
        }
    }); */

    // new ulist by justin (with e)
    ulist = $("#adjustment_datatable").dataTable({
              "pagingType": "full_numbers",
              "oLanguage": {
                               "sEmptyTable":     "No Data Available.."
                           },
              "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]],
              "sDom": "<'tableHeader'<l><'clearfix'f>r>t<'tableFooter'<i><'clearfix'p>>",
              "asSorting": [[ 1, "desc" ]],
              "aoColumns": [
                              { "bSortable": false },
                              { "bSortable": true },
                              { "bSortable": true },
                              { "bSortable": true },
                              { "bSortable": true }
                           ]
            });
    // end of new ulist
       $("#addadjustment").click(function(){ 
       $('.savebatch').hide();
       $(".req,.saves").hide();
       $("#savedata,.savedata").hide();

       dotoggleadjustment("Add Adjustment",{job:GibberishAES.enc("new", toks),adateid:"",uid:GibberishAES.enc("<?=$employeeid?>", toks), toks:toks});
    });
       $("#addremarks").unbind('click').click(function()
    {
       $("#savedata,.savedata").hide();
       $('.savebatch').hide();
       $(".req,.saves").hide();
       $("#modal-view").find("h3[tag='title']").html("Add Remarks"); 
       $("#modal-view").find("div[tag='display']").html("Loading, please wait...");
       $("#button_save_modal").text("Save");
       $("#button_save_modal").hide();
       $("#modal-view").find('.modal-footer').append("<a href='#' class='btn btn-success saves' id='save'>Save</a>");
        $.ajax({
           url:"<?=site_url("process_/addnewremarks")?>",
           type: "POST",
           success: function(msg){
              $("#modal-view").find("div[tag='display']").html(msg);
           }
       }); 
    });

  $(document).on('click','.save',function()
  {
     $("#savedata,.savedata").hide();
    $(".req").hide();
    if ($("#code").val() == "") {
      $("#modal-view").find(".modal-footer").append("<span class='req pull-left' style='color:red'>Code is required!</span>");
    } 
    else if ($("#desc").val() == "") {
      $("#modal-view").find(".modal-footer").append("<span class='req pull-left'  style='color:red'>Description is required!</span>");
    }
    else
    {
        $.ajax({
                  url     :   "<?=site_url("process_/saveRemarks")?>",
                  type    :   "POST",
                  data    :   $("#form_remarks").serialize(),
                  success : function(msg){
                    if (msg == "This code was already taken!") {
                      Swal.fire({
                          icon: 'warning',
                          title: 'Warning!',
                          text: msg,
                          showConfirmButton: true,
                          timer: 1000
                      })
                    }
                    else
                    {
                      Swal.fire({
                          icon: 'success',
                          title: 'Success!',
                          text: msg,
                          showConfirmButton: true,
                          timer: 1000
                      })
                      $(".grey").click();
                    }


                  }
                });
    }
  });
    function dotoggleadjustment(title,data){
       $("#modal-view").find("h3[tag='title']").html(title); 
       $("#modal-view").find("div[tag='display']").html("Loading, please wait...");
       $("#button_save_modal").text("Save");
       $("#button_save_modal").show();
       $.ajax({
           url:"<?=site_url("process_/addnewadjustment")?>",
           data: data,
           type: "POST",
           success: function(msg){
              $("#modal-view").find("div[tag='display']").html(msg);
           }
       }); 
    }
    
});    
}else if("<?=$chkbox?>" == "chkdate"){
    //alert("<?=$dto?>");
    $(function(){
    ulist = $('#adjustment_datatable').dataTable({
        "sPaginationType": "bootstrap",
        "bServerSide": true,
        "sAjaxSource": "<?=site_url("process_/dbadjustmentlistall/{$dto}")?>",
        "sDom": "<'tableHeader'<l><'clearfix'f>r>t<'tableFooter'<i><'clearfix'p>>",
        "iDisplayLength": 15,
        "asSorting": [[ 1, "desc" ]],
        "aoColumns": [
    		{ "bSortable": false },
    		{ "bSortable": true }
    	], 
        "sServerMethod": "POST",
        "fnDrawCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull) {
        // codes here 
            $("a[tag='add_d']").click(function(){
                var uid = $(this).attr("aempid");
                //alert(uid);
               dotoggleadjustment("Add Adjustment",{job:"new",uid:uid,chkbox:"<?=$chkbox?>",dto:"<?=$dto?>"});
            });
        }
    }); 
    function dotoggleadjustment(title,data){
       $("#modal-view").find("h3[tag='title']").html(title); 
       $("#modal-view").find("div[tag='display']").html("Loading, please wait...");
       $("#button_save_modal").text("Save");
       $("#button_save_modal").show();
       $.ajax({
           url:"<?=site_url("process_/addnewadjustment")?>",
           data: data,
           type: "POST",
           success: function(msg){
            //alert(msg);
              $("#modal-view").find("div[tag='display']").html(msg);
           }
       }); 
    }
    
});    
}else{
    //alert("<?=$dtoedit?>"+"<?=$chkbox?>");
    $(function(){
    ulist = $('#adjustment_datatable').dataTable({
        "sPaginationType": "bootstrap",
        "bServerSide": true,
        "sAjaxSource": "<?=site_url("process_/dbadjustmentlistedit/{$dtoedit}")?>",
        "sDom": "<'tableHeader'<l><'clearfix'f>r>t<'tableFooter'<i><'clearfix'p>>",
        "iDisplayLength": 15,
        "asSorting": [[ 1, "desc" ]],
        "aoColumns": [
    		{ "bSortable": false },
    		{ "bSortable": true },
            { "bSortable": true },
            { "bSortable": true }
    	],
        "sServerMethod": "POST",
        "fnDrawCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull) {
        // codes here 
            $("a[tag='edit_d']").click(function(){
                var adateid = $(this).attr("adateid");
                var uid = $(this).attr("uid");                            
               dotoggleadjustment("Add Adjustment",{job:"edit",uid:uid,adateid:adateid,chkbox:"<?=$chkbox?>"});
            });
            $("a[tag='delete_d']").click(function(){
               var ans = confirm("Are you sure you want to continue?");
               if(ans){ 
               var adateid = $(this).attr("adateid");
               var uid = $(this).attr("uid");
               $.ajax({
                   url:"<?=site_url("process_/saverequest")?>",
                   data: {job:1,adateid:adateid,uid:uid},
                   type: "POST",
                   success: function(msg){
                     ulist.fnDraw();
                   }
               });
               }
            });
        }
    }); 
    function dotoggleadjustment(title,data){
       $("#modal-view").find("h3[tag='title']").html(title); 
       $("#modal-view").find("div[tag='display']").html("Loading, please wait...");
       $("#button_save_modal").text("Save");
       $("#button_save_modal").show();
       $.ajax({
           url:"<?=site_url("process_/addnewadjustment")?>",
           data: data,
           type: "POST",
           success: function(msg){
            //alert(msg);
              $("#modal-view").find("div[tag='display']").html(msg);
           }
       }); 
    }
    
});   
}
$(".chosen").chosen();
</script>