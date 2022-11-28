<?php 
    $isteaching = $this->employee->getempteachingtype($employeeid);
    $ratebased = $this->payroll->getEmployeeRateBased($employeeid);  
?>

<form id="frmapproved">
<input name="employeeid" value="<?=$employeeid?>" type='hidden'>
<tag id="<?= $isteaching ?>"></tag>
<div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <table width="100%">
                    <tr>
                        <td rowspan="2" width="70px"><img src="<?=base_url()?>/images/school_logo.jpg" /></td>
                        <td><h4 class="modal-title"><strong><?=$this->extras->school_name()?></strong></h4></td>
                    </tr>
                    <tr>
                        <td><strong>Other Income Form</strong></td>
                    </tr>
                </table>
            </div>
            <div class="modal-body">
                <div class="form_row">
                    <label class="field_name align_right" style="line-height: 5px;">Employee No :</label>
                    <div class="field">
                        <span style="line-height: 5px;" name="employeeid" value="<?=$employeeid?>"><b><?=$employeeid?></b></span>
                    </div>
                </div><br>
                <div class="form_row">
                    <label class="field_name align_right" style="line-height: 5px;">FullName : </label>
                    <div class="field">
                        <span style="line-height: 5px;"><b><?=$this->employee->getfullname($employeeid)?></b></span>
                    </div>
                </div><br>
                <div class="form_row">
                    <label class="field_name align_right" style="line-height: 5px;">Monthly :</label>
                    <div class="field">
                        <input align='right' style="text-align: right;" type="text" class="monthlys" name="monthlys" value='<?=$monthly?>' onkeypress="return numbersonly(this)" >
                    </div>
                </div><br>
                <div class="form_row">
                    <label class="field_name align_right" style="line-height: 5px;">Daily :</label>
                    <div class="field">
                       <input type="text" style="text-align: right;" class="daily" name="daily" value='<?=$daily?>'>
                    </div>
                </div><br>
                <div class="form_row">
                    <label class="field_name align_right" style="line-height: 5px;">Hourly :</label>
                    <div class="field">
                       <input type="text" style="text-align: right;" class="hourly" name="hourly" value='<?=$hourly?>'>
                    </div>
                </div><br>
                <div class="form_row">
                    <label class="field_name align_right">Effective Date :</label>
                    <div class="field">
                        <div class="input-group date" id="edate" data-date="<?=$dateEffective?>" data-date-format="yyyy-mm-dd">
                            <input class="align_center" size="16" class='efdate' name="efdate" type="text" value="<?=$dateEffective?>" readonly>
                            <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                        </div>
                    </div>
                </div>
                         <div class="form_row">
                    <label class="field_name align_right">Date End :</label>
                    <div class="field">
                        <div class="input-group date" id="edate" data-date="<?=$dateEnd?>" data-date-format="yyyy-mm-dd">
                            <input class="align_center" size="16" class='efdate'   name="edate" type="text" value="<?=$dateEnd?>" >
                            <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                        </div>
                    </div>
                </div>
                <!-- ///< For change schedule on specific dates only -->

            </div>
            <div class="modal-footer">
                <div id="loading" hidden=""></div>
                <div id="saving">
                  
                    <button type="button" id="save" class="btn btn-danger">Save</button>
                 
                    <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
</form>
<script>
    $(document).on('keydown',".hourly,.daily,.monthly",function(e)
    {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl+A, Command+A
            (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
             // Allow: home, end, left, right, down, up
            (e.keyCode >= 35 && e.keyCode <= 40)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });

        $(".monthlys").keyup(function(e){
            if (e.keyCode === 9) return false; 
            var empType = $("tag").attr("id");
            if(<?= $ratebased ?> == "teaching"){
                workingdays      =   261;
            }else{
                workingdays      =  313;
            }
            monthly = $(this).val();
            daily  = Number(((monthly*2)*12)/workingdays); 
            daily  = parseFloat(daily).toFixed(2);
            $(".daily").val(addCommas(daily));
            
            hourly  = Number((daily)/8);                     // STATIC daily salary divided by total no. of workhours   
            hourly  = parseFloat(hourly).toFixed(2);  
            $(".hourly").val(addCommas(hourly));
        });
        
        function addCommas(nStr)
        {
            nStr += '';
            x = nStr.split('.');
            x1 = x[0];
            x2 = x.length > 1 ? '.' + x[1] : '';
            var rgx = /(\d+)(\d{3})/;
            while (rgx.test(x1)) {
                x1 = x1.replace(rgx, '$1' + ',' + '$2');
            }
            //return x1 + x2;
            return nStr;
        }

        function numbersonly(myfield, e, dec, id)
        {
            var key;
            var keychar;
                
            if (window.event)   key = window.event.keyCode;
            else if (e)         key = e.which;
            else                return true;
            keychar = String.fromCharCode(key);
                
            // control keys
            if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) ) return true;
                
            // numbers
            else if (((id ? "0123456789.- " : "0123456789.").indexOf(keychar) > -1))   return true;
                
            // decimal point jump
            else if (dec && (keychar == "."))
            {
                myfield.form.elements[dec].focus();
                return false;
            }
            else    return false;
        }


    $('.date').datepicker({
       autoclose: true,
       todayBtn : true
    });

    $("#save").unbind('click').click(function()
    {
        var formdata = {
            employeeid : "<?=$employeeid?>",
            otherIncome : "<?=$otherIncome?>",
            monthly : $(".monthlys").val(),
            daily : $(".daily").val(),
            hourly : $(".hourly").val(),
            efdate : $("input[name='efdate']").val(),
            edate : $("input[name='edate']").val()
        }
        console.log(formdata);
        $.ajax({
            url: "<?=site_url("process_/saveEditedOtherIncome")?>",
            type:"POST",
            data:formdata,
            dataType:"JSON",
            success:function(msg)
            {
                if (msg.err_code == 2) {
                    alert(msg.msg);
                   $("select[name='othincome_drop']").trigger("change");
                   $("#close").click();
                }
                else
                {
                    alert(msg.msg);
                }
            }
        });

    });
</script>