<div class="col-md-5" style="padding-left: 0px;">
    <div class='input-group date' id="datesetfrompicker" data-date="<?=$dfrom?>" data-date-format="yyyy-mm-dd" disabled="">
        <input type='text' class="form-control" size="16" name="datesetfrom" type="text" value="<?=$dfrom?>" autcomplete="off"/>
        <span class="input-group-addon">
            <span class="glyphicon glyphicon-calendar"></span>
        </span>
    </div>
</div>
<div class="col-md-5" style="margin-left: 8px;">
    <div class="input-group date" id="datesettopicker" data-date="<?=$dto?>" data-date-format="yyyy-mm-dd" disabled="">
        <input type='text' class="form-control" size="16" name="datesetto" type="text" value="<?=$dto?>" autcomplete="off" />
        <span class="input-group-addon">
            <span class="glyphicon glyphicon-calendar"></span>
        </span>
    </div>
</div>

<script>
    countDaysWithinSchedule($("input[name=datesetfrom]").val(), $("input[name=datesetto]").val()); ///< checks employee schedule first for applicable number of days
    countDays($("input[name=datesetfrom]").val(), $("input[name=datesetto]").val());
    var toks = hex_sha512(" ");
    $('input[name=datesetfrom],input[name=datesetto],select[name=employee]').blur(function(){
        var d1 = new Date($("input[name='datesetfrom']").val());
        var d2 = new Date($("input[name='datesetto']").val());
        if(d1 > d2){
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: "Please fill-up a valid date.",
                showConfirmButton: true,
                timer: 2000
            })
            $(this).val("");
            return;
        }
        // displayStartEndTime();
    });

    $('input[name=tfrom],input[name=tto]').blur(function(){
        var tfrom = convert_to_24h($("input[name='tfrom']").val());
        var tto = convert_to_24h($("input[name='tto']").val());
        if(tfrom > tto){
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: "Please fill-up a valid time.",
                showConfirmButton: true,
                timer: 2000
            })
            $(this).val("");
            return;
        }
    });
    
    getLeaveDates();
    function countDaysWithinSchedule(start, end){
        $("#loadingdays").show().html("<img src='<?=base_url()?>images/loading.gif' />");
        $.ajax({
           url      :   "<?=site_url("leave_/countDaysWithinSchedule")?>",
           type     :   "POST",
           data     :   {
                            toks:toks,
                            start : GibberishAES.enc(start, toks), 
                            end : GibberishAES.enc(end, toks),
                            withpay : GibberishAES.enc($("select[name='withpay']").val(), toks),
                            leavetype : GibberishAES.enc($("select[name='ltype']").val(), toks)

                            // added by justin (with e) for ica-hyperion 21194
                            <?if($isAdmin){?>
                            , empID : GibberishAES.enc($("select[name='employee']").val(), toks)
                            <?}?>
                            // end for ica-hyperion 21194
                        },
           success  :   function(days){
            $("input[name='ndays']").val(days);
            $("#loadingdays").hide();
           }
        });
    }

    function countDays(start, end){
        $("#loadingdays").show().html("<img src='<?=base_url()?>images/loading.gif' />");
        $.ajax({
           url      :   "<?=site_url("leave_/countDaysWithinSchedule")?>",
           type     :   "POST",
           data     :   {
                            alldays : true, 
                            toks:toks,
                            start : GibberishAES.enc(start, toks), 
                            end : GibberishAES.enc(end, toks),
                            leavetype : GibberishAES.enc($("select[name='ltype']").val(), toks)

                            // added by justin (with e) for ica-hyperion 21194
                            <?if($isAdmin){?>
                            , empID : GibberishAES.enc($("select[name='employee']").val(), toks)
                            <?}?>
                            // end for ica-hyperion 21194
                        },
           success  :   function(days){
            $("#nodays").val(days);
            $("#loadingdays").hide();
           }
        });
    }

    function getLeaveDates(){
        if("<?=$leavetype?>" == "VL" && "<?=$this->session->userdata('usertype')?>" != "ADMIN"){
            Date.prototype.addDays = function(days) {
                var date = new Date(this.valueOf());
                date.setDate(date.getDate() + days);
                return date;
            }

            var date = new Date();

            $("#datesetfrompicker,#datesettopicker, .date").datetimepicker({
                format: "YYYY-MM-DD",
                minDate:date.addDays(3)
            });
        }else{
            $("#datesetfrompicker,#datesettopicker, .date").datetimepicker({
                format: "YYYY-MM-DD"
            });
        }
        checkSchedAffected();
    }

    $("input[name='datesetfrom']").blur(function(){
        checkSchedAffected();
    });

    $("#datesetfrompicker").on("dp.change", function (e) {
        if($('input[name=ishalfday]').is(":checked")){}
        else{
            var start = $(this).find("input").val(),
                end   = $("#datesettopicker").find("input").val();
            countDaysWithinSchedule(start, end); ///< checks employee schedule first for applicable number of days
            countDays(start, end);
        }
    });

    $("#datesettopicker").on("dp.change", function (e) {
        var end = $(this).find("input").val(),
            start   = $("#datesetfrompicker").find("input").val();
        countDaysWithinSchedule(start, end); ///< checks employee schedule first for applicable number of days
        countDays(start, end);
    });

    if("<?=isset($isdetails) ? '1' : ''?>"){
        $("input[name='datesetfrom'], input[name='datesetto']").attr("disabled", "true");
    }

</script>