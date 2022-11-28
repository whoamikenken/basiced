<?php
$datetoday = date("Y-m-d");

    $auditTrail = $this->db->query("SELECT a.id,a.userid ,b.username,CONCAT(b.lastname,', ',b.firstname,' ',b.`middlename`) AS fullname ,menuid,title, que, dtime
    FROM tbltrail a
    LEFT JOIN user_info b ON a.userid = b.id
    LEFT JOIN menus c ON c.`menu_id`=a.`menuid`
    WHERE dtime>CURRENT_DATE LIMIT 200
    ORDER BY a.id desc")->result();

    
?>
<style type="text/css">
        .panel {
    border: 5px solid #0072c6 !important;
    box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
    margin-bottom: 49px !important;
}
</style>
<div id="content">

    <div class="widgets_area">
        <div class="row">
            <div class="col-md-12">
              <div class="panel animated fadeIn">
                   <div class="panel-heading" style="background-color: #0072c6;"><h4><strong>Audit Trail</strong></h4></div>
                        <div class="panel-body">
                            <div class="form_row col-md-12" style="padding-top: 10px;">
                                <div class="col-md-2" style="width: 11.15%;">
                                    <label class="field_name align_right">Date&nbsp;From</label>
                                </div>
                                <div class="field col-md-7" style="margin-left: -70px;">
                                    <div class="form-group col-md-5">
                                        <div class='input-group date' id='datesetfrom' data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                                          <input type='text' class="form-control" size="16" name="datesetfrom" type="text" value="<?=$datetoday?>"/>
                                          <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                          </span>
                                        </div>
                                    </div>
                                    <div class="col-md-1" style="width: 5.333333%;">
                                        <label class="field_name align_center">To</label>
                                    </div>
                                    <div class="col-md-5">
                                        <div class='input-group date' id='datesetto' data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                                          <input type='text' class="form-control"  size="16" name="datesetto" type="text" value="<?=$datetoday?>" />
                                          <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                          </span>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                                <div class="form-row col-md-12" style="margin-top: -21px;">
                                    <div class="col-md-1">
                                        <label class="field_name align_right">&nbsp;</label>
                                    </div>
                                    <div class="col-md-2" style="padding-left: 0px;">
                                        &nbsp;<a href="#" class="btn btn-primary" id="search">Search</a>
                                    </div>
                                    
                                </div> 
                            </div>
                        </div>
                    </div>
                </div>
                <div id="removeAni" class="panel animated fadeIn delay-1s">
                   <div class="panel-heading" style="background-color: #0072c6;"><h4><strong>History</strong></h4></div>
                   <div class="panel-body" id="trailhistory" style="padding-bottom: 50px;"></div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        loadHistory('','');
    });

    setTimeout(
      function() 
      {
        $("#removeAni").removeClass("animated fadeIn delay-1s");
      }, 2000);

    $('#search').on('click',function(){
        var dfrom = $('input[name=datesetfrom]').val(),
            dto   = $('input[name=datesetto]').val();
        loadHistory(dfrom,dto);
    });

    $("#datesetfrom,#datesetto").datetimepicker({
       format: 'YYYY-MM-DD'
    });

    function loadHistory(dfrom,dto){
        $("#trailhistory").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
        $.ajax({
            url     : "<?=site_url("utils_/loadAuditTrailHistory");?>",
            type    : "POST",
            data    : {dfrom:dfrom,dto:dto},
            success : function(msg){
                $('#trailhistory').html(msg);
            }
        });
    }
    
</script>