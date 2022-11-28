<?php

/**
 * @author Kennedy
 * @copyright 2019
 */
$from = date("Y-m-d"); 
$to = date("Y-m-d");
?>

<input type="hidden" name="tbl_id">
<input type="hidden" name="isApplicant" id="isApplicant" value="<?= $applicant ?>">
<form id="form_workhistory" class="form-horizontal">

  <div class="form-group">
  <label class="col-sm-4 control-label">Position Held</label>
    <div class="col-sm-7">
      <input type="text" name="wh_position" class="form-control upperCase" value=""/>
    </div>
  </div> 

  <div class="form-group">
  <label class="col-sm-4 control-label">Employer</label>
    <div class="col-sm-7">
      <input type="text" name="wh_company" class="form-control upperCase" value=""/>
    </div>
  </div> 

<!--   <div class="form-group">
  <label class="col-sm-4 control-label">Address</label>
    <div class="col-sm-7">
      <input type="text" name="wh_address" class="form-control" value=""/>
    </div>
  </div> 

  <div class="form-group">
  <label class="col-sm-4 control-label">Contact Number</label>
    <div class="col-sm-7">
      <input type="text" name="wh_contact" class="form-control" value=""/>
    </div>
  </div> 

  <div class="form-group">
  <label class="col-sm-4 control-label">Salary</label>
    <div class="col-sm-7">
      <input type="text" name="wh_salary" class="form-control" value=""/>
    </div>
  </div>  -->

 <div class="form-group">
  <label class="col-sm-4 control-label">Inclusive Years</label>
    <div class="col-sm-7">
      <input type="text" name="wh_remarks" class="form-control upperCase" value="" oninput="this.value = this.value.replace(/[^0-9.]-''/g, '').replace(/(\..*)\./g, '$1');"/>
    </div>
  </div> 

  <!-- <div class="form-group">
  <label class="col-sm-4 control-label">Salary</label>
    <div class="col-sm-7">
      <input type="text" name="wh_salary" class="form-control upperCase wh_salary" value=""/>
    </div>
  </div> -->

  <div class="form-group">
  <label class="col-sm-4 control-label">Reason For Leaving</label>
    <div class="col-sm-7">
      <input type="text" name="wh_reason" class="form-control upperCase" value=""/>
    </div>
  </div> 

</form>

<div id="msg_header" style="display:none;">
  <strong></strong> <span></span>
</div>

<script>
  var toks = hex_sha512(" ");
  $('.wh_salary').on('input', function () {
    
    var value = $(this).val();
    
    if ((value !== '') && (value.indexOf('.') === -1)) {
        
        $(this).val(Math.max(Math.min(value, 1000000), -1000000));
    }
});
$('.wh_salary').change(function(){
  var salary = formatMoney($(this).val(), 2, ".", ",");

  $(this).val(salary);
})
$('.wh_salary').keydown(function (event) {


        if (event.shiftKey == true) {
            event.preventDefault();
        }

        if ((event.keyCode >= 48 && event.keyCode <= 57) || 
            (event.keyCode >= 96 && event.keyCode <= 105) || 
            event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 37 ||
            event.keyCode == 39 || event.keyCode == 46 || event.keyCode == 190) {

        } else {
            event.preventDefault();
        }

        if($(this).val().indexOf('.') !== -1 && event.keyCode == 190)
            event.preventDefault(); 
        //if a decimal has been added, disable the "."-button

    });

function formatMoney(amount, decimalCount = 2, decimal = ".", thousands = ",") {
  try {
    decimalCount = Math.abs(decimalCount);
    decimalCount = isNaN(decimalCount) ? 2 : decimalCount;

    const negativeSign = amount < 0 ? "-" : "";

    let i = parseInt(amount = Math.abs(Number(amount) || 0).toFixed(decimalCount)).toString();
    let j = (i.length > 3) ? i.length % 3 : 0;

    return negativeSign + (j ? i.substr(0, j) + thousands : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) + (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : "");
  } catch (e) {
    console.log(e)
  }
};

$(".button_save_modal").unbind("click").click(function(){
 var tbl_id = "";
 var table = "";
 var isApplicant = $("#isApplicant").val();
 if($("input[name='applicantId']")) table = "applicant_work_history_unrelated";
 else table = "employee _work_history_unrelated"; 

 var $validator = $("#form_workhistory").validate({
        rules: {
            eb_level: {
              required: true
            },
            wh_position: {
              required: true,
              minlength: 2
            },
            wh_company: {
              required: true,
              minlength: 2
            },
            // wh_address: {
            //   required: true,
            //   minlength: 2
            // },
            // wh_contact: {
            //   required: true,
            //   minlength: 2
            // },
            // wh_salary: {
            //   required: true,
            //   minlength: 2
            // },
            wh_reason: {
              required: true,
              minlength: 2
            },
            wh_remarks: {
              required: true,
              minlength: 2
            }
        }
    });
    
   if($("#form_workhistory").valid()){
      var cobj = "";
      $("#workhistorylistunrelated").find("tbody tr").each(function(){
        if($(this).attr("iscurrent")==1) cobj = $(this);
        
      });              
      if(cobj){
         $(cobj).find("td:eq(0)").text($("input[name='wh_position']").val());
         $(cobj).find("td:eq(1)").text($("input[name='wh_company']").val());                                    
         // $(cobj).find("td:eq(2)").text($("input[name='wh_address']").val());
         // $(cobj).find("td:eq(3)").text($("input[name='wh_contact']").val());
         // $(cobj).find("td:eq(4)").text($("input[name='wh_salary']").val());
         // $(cobj).find("td:eq(2)").text($("input[name='wh_datefrom']").val());
         // $(cobj).find("td:eq(3)").text($("input[name='wh_dateto']").val());
         $(cobj).find("td:eq(2)").text($("input[name='wh_remarks']").val());
         if(isApplicant == "yes"){
          $(cobj).find("td:eq(4)").text($("input[name='wh_reason']").val());
         }else{
            $(cobj).find("td:eq(3)").text($("input[name='wh_salary']").val());
           $(cobj).find("td:eq(4)").text($("input[name='wh_reason']").val());
         }
           

         /*save/update data first*/
         $.ajax({
            url: "<?= site_url('applicant/saveApplicantFilledForm') ?>",
            type: "POST",
            data: {
              table:  GibberishAES.enc( "applicant_work_history_unrelated", toks), 
              tbl_id: GibberishAES.enc( $("input[name='tbl_id']").val() , toks),
              employeeid: GibberishAES.enc($("input[name='applicantId']").val()  , toks),
              position:  GibberishAES.enc($("input[name='wh_position']").val() , toks),
              company:  GibberishAES.enc($("input[name='wh_company']").val() , toks),
              salary:  GibberishAES.enc($("input[name='wh_salary']").val() , toks),
              remarks: GibberishAES.enc($("input[name='wh_remarks']").val()  , toks),
              reason: GibberishAES.enc($("input[name='wh_reason']").val()  , toks),
              toks:toks
              // address: $("input[name='wh_address']").val(),
              // contactnumber: $("input[name='wh_contact']").val(),
              // salary: $("input[name='wh_salary']").val(),
            },
            dataType: "json",
            // async: false,
            success:function(response){
              if(response.status == "success"){
                 tbl_id = response.tbl_id;
                 Swal.fire({
                          icon: 'success',
                          title: 'Success!',
                          text: 'Successfully updated data.',
                          showConfirmButton: true,
                          timer: 1000
                      })
                 $(".modalclose").click();
                 return false;
              }else{
                $("#msg_header").addClass("alert alert-danger");
                $("#msg_header").find("strong").text("Failed! ");
                $("#msg_header").find("span").text(response.msg);
                $("#msg_header").fadeIn().fadeIn("slow").fadeOut(5000);
                return;
              }
            }
         });

      }else{       

         /*save/update data first*/
         $.ajax({
            url: "<?= site_url('applicant/saveApplicantFilledForm') ?>",
            type: "POST",
            data: {
              table:  GibberishAES.enc( "applicant_work_history_unrelated", toks), 
              tbl_id: GibberishAES.enc( $("input[name='tbl_id']").val() , toks),
              employeeid: GibberishAES.enc($("input[name='applicantId']").val()  , toks),
              position:  GibberishAES.enc($("input[name='wh_position']").val() , toks),
              company:  GibberishAES.enc($("input[name='wh_company']").val() , toks),
              salary:  GibberishAES.enc($("input[name='wh_salary']").val() , toks),
              remarks: GibberishAES.enc($("input[name='wh_remarks']").val()  , toks),
              reason: GibberishAES.enc($("input[name='wh_reason']").val()  , toks),
              toks:toks
              // address: $("input[name='wh_address']").val(),
              // contactnumber: $("input[name='wh_contact']").val(),
              // salary: $("input[name='wh_salary']").val(),
            },
            dataType: "json",
            // async: false,
            success:function(response){
              if(response.status == "success"){
                 tbl_id = response.tbl_id;
                 Swal.fire({
                          icon: 'success',
                          title: 'Success!',
                          text: 'Successfully added data.',
                          showConfirmButton: true,
                          timer: 1000
                      })
                 $(".modalclose").click();
                 return false;
              }else{
                $("#msg_header").addClass("alert alert-danger");
                $("#msg_header").find("strong").text("Failed! ");
                $("#msg_header").find("span").text(response.msg);
                $("#msg_header").fadeIn().fadeIn("slow").fadeOut(5000);
                return;
              }
            }
         });

         var mtable = $("#workhistorylistunrelated").find("tbody");
         if($(mtable).find("tr:first").find("td").length==1) $(mtable).html("");
         var ntr = $("<tr></tr>");
         if(isApplicant == "yes"){
             $(ntr).append("<td>"+$("input[name='wh_position']").val().toUpperCase()+"</td>");
             $(ntr).append("<td>"+$("input[name='wh_company']").val().toUpperCase()+"</td>");
             $(ntr).append("<td>"+$("input[name='wh_remarks']").val().toUpperCase()+"</td>");
             // $(ntr).append("<td>"+$("input[name='wh_salary']").val().toUpperCase()+"</td>");
             $(ntr).append("<td>"+$("input[name='wh_reason']").val().toUpperCase()+"</td>");
         }else{
             $(ntr).append("<td>"+$("input[name='wh_position']").val()+"</td>");
             $(ntr).append("<td>"+$("input[name='wh_company']").val()+"</td>");
             $(ntr).append("<td>"+$("input[name='wh_remarks']").val()+"</td>");
             $(ntr).append("<td>"+$("input[name='wh_salary']").val()+"</td>");
             $(ntr).append("<td>"+$("input[name='wh_reason']").val()+"</td>");
         }
             
         // $(ntr).append("<td>"+$("input[name='wh_address']").val()+"</td>");
         // $(ntr).append("<td>"+$("input[name='wh_contact']").val()+"</td>");
         // $(ntr).append("<td>"+$("input[name='wh_salary']").val()+"</td>");
         // $(ntr).append("<td class='align_center'>"+$("input[name='wh_datefrom']").val()+"</td>"); 
         // $(ntr).append("<td class='align_center'>"+$("input[name='wh_dateto']").val()+"</td>");
         
         var mtd = $("<td class='align_center'></td>");
         $("<div style='float: right'><a class='btn btn-warning delete_workhistoryunrelated'  tbl_id="+tbl_id+"><i class='glyphicon glyphicon-trash'></i></a></div>").click(function(){
            if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colspan='6'>No existing data</td></tr>");
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, proceed!',
                cancelButtonText: 'No, cancel!',
                reverseButtons: true
            }).then((result) => {
              if (result.value) {
                $(this).parent().parent().remove();
                delete_workhistory($(this), tbl_id); 
              } else if (
                result.dismiss === Swal.DismissReason.cancel
              ) {
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Data is safe.',
                        'error'
                    )
                }
            })
            
         }).appendTo($(mtd));
         $("<div style='float: right'><a class='btn btn-info edit_workhistoryunrelated' href='#educationModal' tbl_id="+tbl_id+" style='margin-right: 10px;' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a></div>").click(function(){
            addworkhistoryunrelated($(this).children(), tbl_id);
         }).appendTo($(mtd));    
         $(ntr).append($(mtd));
         $(ntr).appendTo($("#workhistorylistunrelated").find("tbody"));      
      }  
      $(".modalclose").click();
       
       return false;
   }else {
       $validator.focusInvalid();
       return false;
   }
});
$(".date").datetimepicker({
    format: "YYYY-MM-DD"
});
$('.chosen').chosen();
</script>