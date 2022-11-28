$(document).ready(function() {
    // if($("input[name='process_category']").val() != "regdeduc") loadBatchEncodeTypeSelection();
    if ($("#categ").val() == "Loan") $(".loan_part").show();
    else $(".loan_part").hide();

    var dateto = $(this).val();
    var code_categ = $("select[name='process_category']").val();
    /*if (code_categ == "deduction") $("input[name='process_nocutoff']").attr("disabled", "disabled");
    else $("input[name='process_nocutoff']").removeAttr("disabled");*/

});

$(".date").datetimepicker({
    format: "YYYY-MM-DD"
});
$(".chosen").chosen();

$("#process_category").change(function(){
    loadBatchEncodeTypeSelection();
});

$("#save_batch_encode").click(function() {
    if (!$("select[name='code_categ']").val() && $("select[name='process_category']").val() != "salary") {
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Please fill-up required fields.',
            showConfirmButton: true,
            timer: 2000
        });
        return;
    }

    if (!$("select[name='process_salary_schedule']").val() && $("select[name='process_category']").val() == "salary") {
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Please fill-up required fields.',
            showConfirmButton: true,
            timer: 2000
        });
        return;
    }
    
    /*var loading = $("#loading").html();
    $("#save_load").html(loading).show();*/
    $("#batchsaving").hide();
    $("#batchloading").show();
    var func = $("#function").val();
    var form_data = $('#batch_process_form').serialize();

    $.ajax({
        url: $("#site_url").val() + "/batch_encode_/" + func,
        type: "POST",
        data: form_data,
        dataType: "json",
        success: function(response) {
            var msg = "Successfully saved " + response.success_count + ". Failed to save " + response.failed_count;
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: msg,
                showConfirmButton: true,
                timer: 2000
            });
            $("#encode_process").modal('toggle');
        }
    });
});

$("input[name='process_current_balance'], input[name='process_nocutoff'], input[name='process_starting_balance']").bind("change keyup input", function() {
    if ($("input[name='process_category']").val() == "loan") {
        var amount = 0;
        var starting_balance = $("input[name='process_starting_balance']").val();
        var current_balance = $("input[name='process_current_balance']").val();
        var no_cutoff = ($("input[name='process_nocutoff']").val()) ? $("input[name='process_nocutoff']").val() : 0;

        if (starting_balance && current_balance && no_cutoff) {
            if (starting_balance < current_balance) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: 'Invalid input of balance.',
                    showConfirmButton: true,
                    timer: 2000
                });
                return;
            }
            if (current_balance && no_cutoff) amount = current_balance / no_cutoff;
            if (amount <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: 'Invalid number of Cut-off.',
                    showConfirmButton: true,
                    timer: 2000
                });
                return;
            }
        }

        $("input[name='process_amount']").val(amount);
    }
});

function loadBatchEncodeTypeSelection() {
    $.ajax({
        url: $("#site_url").val() + "/batch_encode_/loadBatchEncodeTypeSelection",
        type: "POST",
        data: { code: $("select[name='process_category']").val() },
        success: function(response) {
            $("select[name='code_categ']").html(response).trigger("chosen:updated");
        }
    });
}