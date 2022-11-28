var toks = hex_sha512(" ");

$(document).on('keyup change',"input[name=currentbalance],input[name=nocutoff],input[name=startingbalance],input[name=ddatefrom],select[name=period],input[name=skip_loan]",function(e){
    var tr_ = $(this).closest('tr');
    getLoanAmount(tr_);    /*get loan amount*/
    var checkcurrentbalance = $(tr_).find('.currentbalance').val();
    var checknocutoff = $(tr_).find('.nocutoff').val();
    var checkddatefrom = $(tr_).find('.datete').val();
    var checkamount = $(tr_).find('.amount').val();
    var checkstartingbalance = $(tr_).find('.startingbalance').val();
    var period = $(tr_).find('.period').val();

    if((checkcurrentbalance == "" || checknocutoff == "" || checkddatefrom == "" || checkamount == "" || checkstartingbalance == "" || period == "")){
        $(tr_).find('td').css({'background-color':'#ff6666'});
    }
    if((checkcurrentbalance == 0 || checknocutoff == 0 || checkddatefrom == 0 || checkamount == 0 || checkstartingbalance == 0 || period == 0)){
        $(tr_).find('td').css({'background-color':'#ff6666'});
    }
    if((checkcurrentbalance != "" && checknocutoff != "" && checkddatefrom != "" && checkamount != "" && checkstartingbalance != "" && period != "")){
        $(tr_).find('td').css({'background-color':'#99ff99'});
        saveBELoan(tr_);
    }

});

$('#be_loan tbody').on('change', '.currentbalance, .amount, .startingbalance', function () {
    var amount = $(this).val();
    var new_amount = 0;
    if(amount % 1 != 0) new_amount = amount;
    else new_amount = parseInt(amount).toFixed(2);
    $(this).val(new_amount);
});

$(".clearInput").click(function(){
    const swalWithBootstrapButtons = Swal.mixin({
     customClass: {
       confirmButton: 'btn btn-success',
       cancelButton: 'btn btn-danger'
     },
     buttonsStyling: false
   })

   swalWithBootstrapButtons.fire({
     title: 'Are you sure?',
     text: "Do you really want to clear this row?",
     icon: 'warning',
     showCancelButton: true,
     confirmButtonText: 'Yes, proceed!',
     cancelButtonText: 'No, cancel!',
     reverseButtons: true
   }).then((result) => {
     if (result.value) {
        var tr_id = $(this).closest("tr");
        $(tr_id).find(".baseon").val('');
        $(tr_id).find(".ddatefrom").val('');
        $(tr_id).find(".startingbalance").val('');
        $(tr_id).find(".currentbalance").val('');
        $(tr_id).find(".nocutoff").val('');
        $(tr_id).find(".amount").val('');
        $(tr_id).find(".period").val('');
        saveBELoan(tr_id);
     } else if (
       result.dismiss === Swal.DismissReason.cancel
     ) {
       swalWithBootstrapButtons.fire(
         'Cancelled',
         'loan data is safe.',
         'error'
       )
     }
   });
    
});

$("a[tag='delete']").unbind('click').click(function(){
    var id = $(this).attr('base_id');
    var employeeid = $(this).attr('employeeid');

    $.ajax({
        url : $("#site_url").val() + "/loan_/showDeleteLoanModal",
        type : "POST",
        data : {
            toks: toks,
            id : GibberishAES.enc(id, toks),
            employeeid : GibberishAES.enc("<?=$employeeid?>", toks),
            is_batch_encode : GibberishAES.enc(true, toks)
        },
        success : function(content){
            $('#be_modal').find('.modal-body').html(content);
            $('#be_modal').modal('show');
            $("#button_save_modal").show();
        }
    });    
});

$("#ClrLoan").click(function(){
    const swalWithBootstrapButtons = Swal.mixin({
     customClass: {
       confirmButton: 'btn btn-success',
       cancelButton: 'btn btn-danger'
     },
     buttonsStyling: false
   })

   swalWithBootstrapButtons.fire({
     title: 'Are you sure?',
     text: "Do you really want to clear all 0 cutoff loan?",
     icon: 'warning',
     showCancelButton: true,
     confirmButtonText: 'Yes, proceed!',
     cancelButtonText: 'No, cancel!',
     reverseButtons: true
   }).then((result) => {
     if (result.value) {
        var formdata = { toks:toks,tblname:GibberishAES.enc("employee_loan", toks) };
        $.ajax({
            url : $("#site_url").val() + "/payroll_/clearZeros",
            type : "POST",
            data : formdata,
            success : function(respond){
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Successfully clear 0 cutoff.',
                    showConfirmButton: true,
                    timer: 2000
                });
                loadBatchEncodeEmployee();
            }
        });
     } else if (
       result.dismiss === Swal.DismissReason.cancel
     ) {
       swalWithBootstrapButtons.fire(
         'Cancelled',
         'loan data is safe.',
         'error'
       )
     }
   });
});


$("#be_loan").dataTable({
    "pagination": "number",
    "oLanguage": {
                     "sEmptyTable":     "No Data Available.."
                 },
    "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]],
    // "scrollY": 1000,
    "scrollX": true
});

$('#be_loan').on('draw.dt', function () { 
    $(".date").datetimepicker({
        format: "YYYY-MM-DD"
    });

    $(".clearRow").unbind().click(function(){
        var tr_id = $(this).closest('tr').attr('id');
        var codeLoan = $("#").val();
        $("tr[id='"+ tr_id +"']").find("#datefrom").val("");
        $("tr[id='"+ tr_id +"']").find(".amount").val("");
        $("tr[id='"+ tr_id +"']").find(".dateto").val("");
        $("tr[id='"+ tr_id +"']").find(".nocutoff").val("");
        $("tr[id='"+ tr_id +"']").find(".cutoff_period").val("");
        deleteBELoan(tr_id, codeLoan);
    });
});

$(".clearRow").unbind().click(function(){
    var tr_id = $(this).closest('tr').attr('id');
    var codeLoan = $("#").val();
    $("tr[id='"+ tr_id +"']").find("#datefrom").val("");
    $("tr[id='"+ tr_id +"']").find(".amount").val("");
    $("tr[id='"+ tr_id +"']").find(".dateto").val("");
    $("tr[id='"+ tr_id +"']").find(".nocutoff").val("");
    $("tr[id='"+ tr_id +"']").find(".cutoff_period").val("");
    deleteBELoan(tr_id, codeLoan);
});
$(".date").datetimepicker({
    format: "YYYY-MM-DD"
});
function saveBELoan(id){
    var is_skip_loan = "";
    if($(id).find('input[name="skip_loan"]').is(':checked')) is_skip_loan = "1";
    else is_skip_loan = "0";
    var form_data = {};
    employeeid = $(id).attr('employeeid');
    form_data[employeeid] = {};
    form_data[employeeid]['loan'] = GibberishAES.enc($("select[name=code_type]").val(), toks); 
    form_data[employeeid]['baseon'] = GibberishAES.enc($(id).find("select[name=baseon]").val(), toks); 
    form_data[employeeid]['deductiondate'] = GibberishAES.enc($(id).find("input[name=ddatefrom]").val(), toks);
    form_data[employeeid]['startingbalance'] = GibberishAES.enc($(id).find("input[name=startingbalance]").val(), toks);
    form_data[employeeid]['currentbalance'] = GibberishAES.enc($(id).find("input[name=currentbalance]").val(), toks); 
    form_data[employeeid]['nocutoff'] = GibberishAES.enc($(id).find("input[name=nocutoff]").val(), toks); 
    form_data[employeeid]['amount'] = GibberishAES.enc($(id).find("input[name=amount]").val(), toks); 
    form_data[employeeid]['schedule'] = GibberishAES.enc("semimonthly", toks); 
    form_data[employeeid]['cutoff_period'] = GibberishAES.enc($(id).find("select[name=period]").val(), toks); 
    form_data[employeeid]['skip_loan'] = GibberishAES.enc(is_skip_loan, toks); 
    var period = GibberishAES.enc($(id).find("select[name=period]").val(), toks);
    $.ajax({
        url: $("#site_url").val() + "/payroll_/saveLoanBatch",
        type:"POST",
        data:{form_data:form_data, period:period,toks:toks},
        dataType:"JSON",
        success:function(msg){
        }
    });
}
function getLoanAmount(id){
    var currentbalance = $(id).find("input[name=currentbalance]").val();
    var nocutoff = $(id).find("input[name=nocutoff]").val();
    if(currentbalance && nocutoff){
        var amount = currentbalance / nocutoff;
        amount = amount.toFixed(2);
        if(isNaN(amount) || !isFinite(amount)) amount = 0;
        $(id).find("input[name=amount]").val(amount);
    }
}