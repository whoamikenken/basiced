<style>
    .cbox{
        -ms-transform: scale(1.5); /* IE */
        -moz-transform: scale(1.5); /* FF */
        -webkit-transform: scale(1.5); /* Safari and Chrome */
        -o-transform: scale(1.5); /* Opera */
    }
</style>
<div class="container" style="width: 100%;">
    <form id="philhealth_share">
        <input type="hidden" name="id" value="<?=$id?>">
        <input type="hidden" name="created_by" value="<?=$username?>">
        <div class="form-group">
            <div class="field">
                <!-- <b>Salary Range:</b> &nbsp;&nbsp; <input class="cbox" value="range" type="checkbox"/> &nbsp;&nbsp;&nbsp;
                <b>Minimum:</b> &nbsp;&nbsp; <input class="cbox" value="min" type="checkbox"/> &nbsp;&nbsp;&nbsp;
                <b>Maximum:</b> &nbsp;&nbsp; <input class="cbox" value="max" type="checkbox"/> &nbsp;&nbsp;&nbsp;
                <h3>Pills</h3> -->
                <ul class="nav nav-pills" style="cursor: pointer;">
                    <li type="range" class="phil_type active"><a>Salary range</a></li>
                    <li type="min" class="phil_type"><a>Minimum</a></li>
                    <li type="max" class="phil_type"><a>Maximum</a></li>
                </ul>
            </div>
        </div>
        <div class="form-group" id="min_div">
            <label class="field_name align_right">Minimum Salary</label>
            <div class="field">
                <input class="form-control" id="min_salary" name="min_salary" type="number" value="<?=$min_salary?>"/>
            </div>
        </div>
        <div class="form-group" id="max_div">
            <label class="field_name align_right">Maximum Salary</label>
            <div class="field">
                <input class="form-control" id="max_salary" name="max_salary" type="number" value="<?=$max_salary?>"/>
            </div>
        </div>
       <div class="form-group" id="per_div">
            <label class="field_name align_right">Percentage</label>
            <div class="field">
                <input class="form-control" id="percentage" name="percentage" type="number" value="<?=$percentage?>"/>
            </div>
        </div>
        <div class="form-group" id="def_div">
            <label class="field_name align_right">Default Amount</label>
            <div class="field">
                <input class="form-control" id="def_amount" name="def_amount" type="number" value="<?=$def_amount?>"/>
            </div>
        </div>
        <div id="msg_header" style="display: none;">
            <strong></strong><span></span>
        </div>
    </form>
</div>

<script>
    $("#button_save_modal").unbind("click").click(function(){
        var isValid = true;
        var formdata = $("#philhealth_share").serialize();
        var min_salary = $("input[name='min_salary']").val();
        var max_salary = $("input[name='max_salary']").val();
        var percentage = $("input[name='percentage']").val();
        var def_amount = $("input[name='def_amount']").val();
        var type = $(".cbox").val();
        if($(".cbox"). prop("checked") == false){
            alertMessage("Select a type of setup.");
            return;
        }
        if(type == "range"){
            if(min_salary > max_salary){
                alertMessage("Wrong input of minimum salary and maximum salary.");
                return;
            }
        }
        if(type == "min"){
            if(!min_salary || !def_amount){
                alertMessage("All fields are required");
                return;
            }
        }
        if(type == "max"){
            if(!max_salary || !def_amount){
                alertMessage("All fields are required");
                return;
            }
        }
        $.ajax({
            url:"<?=site_url("payroll_/savePhilhealthShare")?>",
            type:"POST",
            data:formdata,
            success: function(msg){
                if(msg){
                    Swal.fire({
                        icon: 'Success',
                        title: 'Success!',
                        text: "Successfully saved philhealth setup!",
                        showConfirmButton: true,
                        timer: 2000
                    }); 
                    empshare_setup();
                }else{
                    alertMessage("Failed saved philheath setup!.");
                    return;
                }
                $("#modalclose").click();
            }
         });
    });

    $(".phil_type").click(function(){
        $("li").removeClass("active");
        $(this).addClass("active");
        validateSetup($(this).attr("type"));
    });

    function validateSetup(type){
        if(type == "min"){
            /*show*/
            $("#min_div").show();
            $("#def_div").show();
            /*hide*/
            $("#per_div").hide();
            $("#max_div").hide();
        }else if(type == "max"){
            /*show*/
            $("#max_div").show();
            $("#def_div").show();
            /*hide*/
            $("#per_div").hide();
            $("#min_div").hide();
        }else{
            /*show*/
            $("#min_div").show();
            $("#max_div").show();
            $("#per_div").show();
            /*hide*/
            $("#def_div").hide();
        }
    }

    function alertMessage(msg){
        $("#msg_header").removeClass("alert alert-success");
        $("#msg_header").addClass("alert alert-danger");
        $("#msg_header").find("strong").text("Failed! ");
        $("#msg_header").find("span").text(msg);
        $("#msg_header").fadeIn().fadeIn("slow").fadeOut(5000);
    }

</script>