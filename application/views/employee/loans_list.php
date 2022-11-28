<?
  /**
  * @author justin (with e)
  * @copyright 2018
  */

  $CI =& get_instance();
?>
<style>
  #modal-view{
    margin-left: auto!important;
    margin-right: auto!important;
  }

  .modal-lg{
    width: 45%;
  }

  .scrollbar{
   overflow: auto;
   margin-bottom: 10px;
}

  .scrollbar::-webkit-scrollbar {
    width: 10px;
    height: 10px;
  }

  /* Track */
  .scrollbar::-webkit-scrollbar-track {
    box-shadow: inset 0 0 0 grey; 
    border-radius: 10px;
  }
   
  /* Handle */
  .scrollbar::-webkit-scrollbar-thumb {
    background: #0072c6;
    border-radius: 10px;
  }

  /* Handle on hover */
  .scrollbar::-webkit-scrollbar-thumb:hover {
    background: #fadd14; 
  }

</style>
<a href="#modal-view" data-toggle="modal" id="show_modal" hidden></a>
<?if ($is_edit_display):?>
<br><br>
<div class="form_row">
  <button type="button" class="btn btn-primary" tag="add" value="new"><span class="glyphicon glyphicon-plus-sign"></span> Add Loan</button>
  <br/>
  <br/>
</div>
<?endif;?>
<div class="form_row">
  <div class="scrollbar">
  <table class="table table-striped table-bordered table-hover" id="loan_tbl" width="100%">
    <thead style="background-color: #0072c6;">
      <tr>
        <th width="3%">#</th>
        <th width="10%">Type</th>
        <th width="6.67%">Deduction Date</th>
        <th width="6.67%">Loan Amount</th>
        <th width="6.67%">Remaining Balance</th>
        <th width="6.67%">Amount Per Cut-Off</th>
        <th width="6.67%">Total Amount</th>
        <th width="6.67%">Remaining Cut-Off</th>
        <th width="6.67%">No. of Cut-Off</th>
        <th width="6.67%">Schedule</th>
        <th width="6.67%">Period</th>
        <th width="6.67%">Hold</th>
        <th width="4.67%">View Payments</th>
        
        <?if ($is_edit_display):?>
        <th width="6.96%">&nbsp;</th>
        <?endif;?>

      </tr>
    </thead>
    <tbody id="loanlist">
    <?
    foreach ($loan_list as $idx => $info){
      $skip_loan = $CI->loan->checkIfSkipInLoanPayment($employeeid, $info['code_loan']);
    ?>
      <tr>
        <td class="align_right"><?=($idx + 1)?></td>
        <td class="align_left"><?=$info["type"]?></td>
        <td class="align_center"><?=$info["start_date"]?></td>
        <td class="align_right"><?=$info["start_balance"]?></td>
        <td class="align_right"><?=$info["remain_balance"]?></td>
        <td class="align_right"><?=$info["amount"]?></td>
        <td class="align_right"><?=$info["total_amount"]?></td>
        <td class="align_center"><?=$info["remain_cutoff"]?></td>
        <td class="align_center"><?=$info["no_cutoff"]?></td>
        <td class="align_center"><?=$info["schedule"]?></td>
        <td class="align_center"><?=$info["period"]?></td>
        <td class="status-tag align_center stats" style="width: 60px;"><input type="checkbox" name="skip_loan" class="double-sized-cb" employeeid="<?= $employeeid ?>" code_loan="<?=$info["code_loan"]?>" <?= $skip_loan ? "checked" : "" ?> ></td>

        <td class="align_center">
          <button type="button" class="btn btn-primary" style="background-color:  #00b3b3;" tag="view" value="<?=$info["id"]?>">
            <span class="icon icon-eye-open"></span>
          </button>
        </td>
        <?if ($is_edit_display):?>
        <td class="align_center">
        <?
          if($info["is_able_edit"]): 
        ?>
          <button type="button" class="btn btn-info" style="width: 40px;" tag="edit" value="<?=$info["id"]?>">
            <span class="icon glyphicon glyphicon-edit"></span>
          </button>
        <?
          endif;
        ?>
          <button type="button" class="btn btn-danger" style="width: 40px;" tag="delete" value="<?=$info["id"]?>">
            <span class="icon glyphicon glyphicon-trash"></span>
          </button>
        </td>
        <?endif;?>        

      </tr>
    <?
    }
    ?>
    </tbody>
  </table>
</div>
</div>

<script type="text/javascript">
var toks = hex_sha512(" ");
$(document).ready(function(){
    var table = $('#loan_tbl').DataTable({
    });
    new $.fn.dataTable.FixedHeader( table );
});

function viewLoanModal(title, content){
  $("#show_modal").click();
  $("#button_save_modal").show();
  $("#button_save_modal").html('Save');
  $("#modal-view").find("h3[tag='title']").text(title);
  $("#modal-view").find(".modal-dialog").removeClass("modal-md").addClass("modal-lg");
  $("#modal-view").find("div[tag='display']").html(content); 
}

$("button[tag='add'], button[tag='edit']").unbind('click').click(function(){
  var id = this.value;
  var title = ($(this).attr("tag") == "add") ? "Add Loan" : "Edit Loan";

  $.ajax({
    url : "<?=site_url("loan_/showAddEditLoanModal")?>",
    type : "POST",
    data : {
            id :  GibberishAES.enc(id , toks),
            employeeid :  GibberishAES.enc("<?=$employeeid?>" , toks),
            toks:toks
           },
    success : function(content){
      viewLoanModal(title, content);
    }
  });
});

$("button[tag='view']").unbind('click').click(function(){
  var id = this.value;

  $.ajax({
    url : "<?=site_url("loan_/viewLoanPayment")?>",
    type : "POST",
    data : {
             id :  GibberishAES.enc(id , toks),
            employeeid :  GibberishAES.enc("<?=$employeeid?>" , toks),
            toks:toks
           },
    success : function(content){
      viewLoanModal("View Payment", content);
    }
  });
});

$("button[tag='delete']").unbind('click').click(function(){
  var id = this.value;

  $.ajax({
    url : "<?=site_url("loan_/showDeleteLoanModal")?>",
    type : "POST",
    data : {
             id :  GibberishAES.enc(id , toks),
            employeeid :  GibberishAES.enc("<?=$employeeid?>" , toks),
            toks:toks
           },
    success : function(content){
      viewLoanModal("Delete Loan", content);
    }
  });
});

$('input[type="checkbox"]').click(function(){
  var status = '';
  var employeeid = $(this).attr('employeeid');
  var code_loan = $(this).attr('code_loan');
  if($(this).prop("checked")) status = "YES";
  else status = "NO";
  
  changeLoanStatus(employeeid, code_loan, status);
});

function changeLoanStatus(employeeid, code_loan, status){
  if(!employeeid || !code_loan || !status){
    alert("Some fields are missing! ");
    return;
  }

  $.ajax({
    url: "<?= site_url('loan_/validateSkippingLoan') ?>",
    type: "POST",
    data: {
      employeeid:  GibberishAES.enc(employeeid , toks),
      code_loan:  GibberishAES.enc(code_loan , toks),
      status:  GibberishAES.enc(status , toks),
      toks:toks
    },
    success:function(response){
      alert(response);
    }
  });

}

</script>