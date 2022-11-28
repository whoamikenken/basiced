<style type="text/css">
    .panel {
        border: 5px solid #0072c6 !important;
        box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
        margin-bottom: 49px !important;
    }
</style>
<div id="content">
    <div class="widgets_area">
        <div class="panel animated fadeIn">
            <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Accomplishment Reports</b></h4></div>
            <div class="panel-body">
                <div style="display: flex;">
                    <label class="field_name">Date From</label>
                    <div style="width: 50%;">
                        <div class="col-md-5">
                            <div class='input-group date' id="datesetfrom" data-date="" data-date-format="yyyy-mm-dd">
                                <input type='text' class="form-control" size="16" name="date" id="date" type="text" value=""/>
                                <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                        <span class="col-md-1">&nbsp;<b>Employee<br>&nbsp;</span>
                        <div class="col-md-5">
                            <select class="chosen" name="employee" id="employee"></select>
                        </div>
                    </div>
                </div><br>
                <div id="data_table">
                    
                </div>
            </div>
        </div>
    </div>   
</div>

<script>
    accomplishmentLists();
    employeeList();

    $(".date").datetimepicker({
        format: "YYYY-MM-DD"
    });

    $(".chosen").chosen();

    $("#employee").change(function(){
        accomplishmentLists();
    });

    $("#date").blur(function(){
        accomplishmentLists();
    });

    function accomplishmentLists(){
        $.ajax({
            url: "<?= site_url('gate_/accomplishmentLists')?>",
            type: "POST",
            data: {
                employee: $("#employee").val(),
                date: $("#date").val()
            },
            success:function(response){
                $("#data_table").html(response);
            }
        });
    }

    function employeeList(){
        $.ajax({
            url: "<?= site_url('gate_/activeEmployeeLists')?>",
            success:function(response){
                $("#employee").html(response).trigger("chosen:updated");
            }
        });
    }

</script>