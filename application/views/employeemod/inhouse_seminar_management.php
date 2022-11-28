<style type="text/css">
    
    .pointer{
        cursor:pointer;
    }
    .panel {
        border: 5px solid #0072c6 !important;
        box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
        margin-bottom: 49px !important;
    }
    .panel > .panel-heading {
        background-color: #0072c6;
        color: black;
    }

</style>
<?php
    $usertype = $this->session->userdata("usertype");
    $CI =& get_instance();
    $CI->load->model('seminar');
    $seminarlist = $CI->seminar->seminarDetails();
    $seminarexist = array();
?>
<div id="content">
    <div class="widgets_area">
        <div class="row">
            <div class="col-md-12">
                <div class="form-inline">
                    <span><b>Sort by:</b></span>
                    <input type="radio" name="isgoing" value="1" style="display:inline !important;" > Going
                    <input type="radio" name="isgoing" value="0" style="display:inline !important;" checked> Not Available
                    <input type="radio" name="isgoing" value="2" style="display:inline !important;"> Seminar Attendees
                    <a href="#modal-view" data-toggle='modal' style="font-weight:bold;text-decoration: underline;font-size:1.2em;margin-right: 10px; float: right"  id="annualSeminarReport">Annual Seminar Report</a>
                </div>
                <div class="panel">
                    <div class="panel-heading"><h4><b>Inhouse seminar attendees list</b></h4></div>
                    <div class="panel-body" id="empList">
                        <?php 

                        if($usertype == "ADMIN"): ?>
                            <div class="col-md-12" style="margin: 20px;">
                                <div class="field">
                                    <label class="col-md-1 align_right">Seminar:</label>
                                    <div class="col-md-5">
                                        <select class="chosen" id="seminarType">
                                            <option value="">Select All</option>
                                            <?php foreach($seminarlist as $value): 
                                                if($value['level'] && !in_array($value['id'], $seminarexist)){ 
                                                    array_push($seminarexist, $value['id']);
                                                 ?>
                                                <option value="<?= Globals::_e($value['level']) ?>" tbl_id="<?= Globals::_e($value['id']) ?>"><?= Globals::_e($value['level'])." ".date('F j, Y',strtotime($value['date_from']))." - ".date('F j, Y',strtotime($value['date_to'])); ?></option>
                                            <?php } endforeach ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="field generateField">
                                    <label class="col-md-1 align_right">Format:</label>
                                    <div class="col-md-2">
                                        <select class="chosen" id="reportformat">
                                            <option value="PDF">PDF</option>
                                            <option value="EXCEL">EXCEL</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="field generateField">
                                    <div class="col-md-2">
                                        <a href="#" class="btn btn-success" id="generateSeminar">Generate</a>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        <table id="user_datatable" class="table table-striped table-bordered table-hover">
                            
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>           

<script>
    var toks = hex_sha512(" ");
    loadSeminarAttendees();
    $("input[name='isgoing']").change(function(){
        var isgoing = $("input[name='isgoing']:checked").val();
        if(isgoing != 2){
            $(".generateField").fadeIn();
            loadSeminarAttendees($("#seminarType").val());
        } 
        else{
            // $(".generateField").fadeOut();
            loadSeminarEmpAttendedList($("#seminarType").val());
        } 
       
    });

    $("#annualSeminarReport").click(function(){
        $("#modal-view").find("h3[tag='title']").text("Annual Seminar Report");
        $("#button_save_modal").text("Generate");
        $.ajax({
            url: "<?=site_url('seminar_/annualSeminarReport')?>",
            success:function(response){
                $("#modal-view").find("div[tag='display']").html(response);
            }
        })  
    });

    

    $("#seminarType").change(function(){
        var isgoing = $("input[name='isgoing']:checked").val();
        if(isgoing != 2) loadSeminarAttendees($(this).val());
        else loadSeminarEmpAttendedList($(this).val());
    });

    $("#generateSeminar").click(function(){
        var isgoing = $("input[name='isgoing']:checked").val();
        /*if(isgoing != 2){*/
        var form_data = "?";
            form_data += "&isgoing="+GibberishAES.enc($("input[name='isgoing']:checked").val(), toks);
            form_data += "&seminartype="+ GibberishAES.enc($("#seminarType").val(), toks);
            form_data += "&seminarTitle="+ GibberishAES.enc($("#seminarType option:selected").text() , toks);
            form_data += "&seminarid="+ GibberishAES.enc($("#seminarType option:selected").attr('tbl_id') , toks);
            form_data += "&q="+GibberishAES.enc($("#last_query").val(), toks);
            form_data += "&toks="+toks;
        var encodedData = encodeURIComponent(window.btoa(form_data));
        // var form_data = "isgoing="+ GibberishAES.enc($("input[name='isgoing']:checked").val(), toks)+"&seminartype="+ GibberishAES.enc($("#seminarType").val(), toks)+"&seminarTitle="+ GibberishAES.enc($("#seminarType option:selected").text() , toks)+"&q="+GibberishAES.enc($("#last_query").val(), toks)+"&toks="+toks;
        // var form_data = "isgoing="+$("input[name='isgoing']:checked").val()+"&seminartype="+$("#seminarType").val()+"&seminarTitle="+$("#seminarType option:selected").text()+"&q="+$("#last_query").val();
        var report_type = $("#reportformat").val();
        if(report_type == "PDF"){
            window.open("<?=site_url("seminar_/seminarAttendeesPFDReport")?>?formdata="+encodedData,"try");
        }else{
            form_data += "&view=reports_excel/employeelistperofficexls"; 
            window.open("<?=site_url("seminar_/seminarAttendeesEXCELReport")?>?formdata="+encodedData,"try"); 
        }
        /*}else{
            Swal.fire({
                icon: 'warning',
                title: 'Oooops!',
                text: 'Something went wrong! Module will reload shortly..',
                showConfirmButton: true,
                timer: 2000
            })
            setTimeout(function(){
                location.reload();
            }, 2000)
        }*/
            

    })

    function loadSeminarAttendees(seminartype=""){
        var isgoing = $("input[name='isgoing']:checked").val();
        $.ajax({
            url: "<?=site_url('seminar_/seminarAttendeesList')?>",
            type: "POST",
            data: {isgoing:  GibberishAES.enc(isgoing , toks), seminartype: GibberishAES.enc(seminartype , toks), toks:toks},
            success:function(response){
                $("#user_datatable").html(response);
            }            
        })
    }

    function loadSeminarEmpAttendedList(seminartype=""){
        $.ajax({
            url: "<?=site_url('seminar_/seminarEmpAttendedList')?>",
            type: "POST",
            data: {seminartype: GibberishAES.enc(seminartype , toks), seminarid: GibberishAES.enc($("#seminarType option:selected").attr('tbl_id'), toks), toks:toks},
            success:function(response){
                $("#user_datatable").html(response);
            }            
        })
    }

    $(".chosen").chosen();
</script>