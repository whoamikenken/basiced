<?php

/**
 * @author Max Consul
 * @copyright 2019
 */
 
 $hc = "";
 $hc = $this->payroll->displayHeadCashier();
?>

<style>
  #income_main_acct{
    font-size: 14px;
    font-style: italic;
    font-weight: bold;
    color: #FFF;
    text-decoration: underline;
    padding-right: 20px; 
  }
  #income_main_acct:hover{
    font-style: normal;
    color: #E1BEE7;
  }
        .panel {
    border: 5px solid #0072c6 !important;
    box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
    margin-bottom: 49px !important;
}
</style>

<div id="content"> <!-- Content start -->
    <div class="widgets_area">
        <div class="panel">
            <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Rank Description</b></h4></div>
            <div class="panel-body">
                <!-- Type Config  -->
                <button class="btn btn-primary new_setup" categ="type" style="margin-left: 1.7%;">Add new</button>
                <div id="configtype" class="panel-body">   
                </div>
                <!-- End  -->
            </div>    

        </div>
        <div class="panel">
            <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Ranking Level</b></h4></div>

            <div class="panel-body">
                <!-- Type Config  -->
                <button class="btn btn-primary new_setup" categ="rank" style="margin-left: 1.7%;">Add new</button>
                <div id="configrank" class="panel-body">
                </div>

            </div>    

        </div>
        <div class="panel">
            
            <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Position Level</b></h4></div>

            <div class="panel-body">
                <button class="btn btn-primary new_setup" categ="set" style="margin-left: 1.7%;">Add new</button>
                <div id="configset" class="panel-body">
                </div>
                <!-- End  -->
            </div>    

        </div>          
    </div>
</div>
<div id="add_setup" class="modal fade" role="dialog">

    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <div class="media">
                    <div class="media-left">
                        <img src="<?=base_url()?>images/school_logo.png" class="media-object" style="width:60px">
                    </div>
                    <div class="media-body" style="font-weight: bold;padding-top: 10px;">
                        <h4 class="media-heading" style="font-family: Avenir;"><b>Pinnacle Technologies Inc.</b></h4>
                        <p style="font-family: Avenir;  margin-top: -1.5%;">D`Great</p>
                    </div>
                </div>
                <center><b><h3 class="modal-title"></h3></b></center>
            </div>
            <div class="modal-body">
                <label style="margin-left: 8%"><b>Description: </b></label>
                <input type="hidden" id="code">
                <input type="text" class="form-control" id="description" style="float: right; margin-right: 10%; width: 60%;">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="save_setup">Save</button>
            </div>
        </div>
    </div>
    
</div>

<!-- <div id="deletemodal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <div class="modal-content">
          <div class="modal-header">
            <div class="media">
                <div class="media-left">
                    <img src="<?=base_url()?>images/school_logo.png" class="media-object" style="width:60px">
                </div>
                <div class="media-body" style="font-weight: bold;padding-top: 10px;">
                    <h4 class="media-heading" style="font-family: Avenir;"><b>Pinnacle Technologies Inc.</b></h4>
                    <p style="font-family: Avenir;  margin-top: -1.5%;">D`Great</p>
                </div>
            </div>
            <center><b><h4 tag="title" class="modal-title" id="tag"></h4></b></center>
          </div>
          <div class="modal-body align_center" >
            <p>Are you sure you want to remove <span id="managerank_id"></span></p>
            
          </div>
          <div class="modal-footer">
            <button type="button" id="deleteconfig" class="btn btn-danger" data-dismiss="modal">Yes</button>
            <button type="button" class="btn btn-success" data-dismiss="modal">No</button>
            
        </div>
        </div>
        
    </div>
</div> -->

<script>
    var toks = hex_sha512(" ");
    var categ = "";
    $(document).ready(function(){
        loadtypeconfig();          //  load type config
        loadrankconfig();          //  load rank config
        loadsetconfig();           //  load set config
    });

   // type config
    function loadtypeconfig(){
        $("#configtype").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
        $.ajax({
           url      :   "<?=site_url("setup_/rankConfigType")?>",
           success  :   function(msg){
            $("#configtype").html(msg);
            if("<?=$this->session->userdata('canwrite')?>" == 0) $("#configtype").find(".btn").css("pointer-events", "none");
            else $("#configtype").find(".btn").css("pointer-events", "");
           }
        });
    }

   // type config
    function loadrankconfig(){
        $("#configrank").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
        $.ajax({
           url      :   "<?=site_url("setup_/rankConfig")?>",
           success  :   function(msg){
            $("#configrank").html(msg);
            if("<?=$this->session->userdata('canwrite')?>" == 0) $("#configrank").find(".btn").css("pointer-events", "none");
            else $("#configrank").find(".btn").css("pointer-events", "");
           }
        });
    }

       // type config
    function loadsetconfig(){
        $("#configset").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
        $.ajax({
           url      :   "<?=site_url("setup_/rankConfigSet")?>",
           success  :   function(msg){
            $("#configset").html(msg);
            if("<?=$this->session->userdata('canwrite')?>" == 0) $("#configset").find(".btn").css("pointer-events", "none");
            else $("#configset").find(".btn").css("pointer-events", "");
           }
        });
    }

    $(".new_setup").click(function(){
        $("#description, #code").val('');
        categ = $(this).attr('categ');
        $(".modal-title").text('Add ' + categ.charAt(0).toUpperCase() + categ.slice(1) + ' Config' );
        $("#add_setup").modal('toggle');
    });

    $("#save_setup").click(function(){
        var description = $("#description").val();
        var code = $("#code").val();
        // console.log(description,code);
        $.ajax({
            url: "<?= site_url('setup_/validatePayrollRankSetup') ?>",
            type: "POST",
            data: {
                toks:toks,
                description: GibberishAES.enc(description, toks), 
                code:GibberishAES.enc(code, toks), 
                categ: GibberishAES.enc(categ, toks)
            },
            success:function(response){
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response,
                    showConfirmButton: true,
                    timer: 1000
                })
                $("#add_setup").modal('toggle');
                if(categ == 'type') loadtypeconfig();          //  load type config
                if(categ == 'rank') loadrankconfig();          //  load rank config
                if(categ == 'set') loadsetconfig();           //  load set config
            }
        });
    });

    // $("#deleteconfig").click(function(){
    //     var id = $(this).attr("deleteID");
    //     var categ = $(this).attr("deletecategory");

    //     $.ajax({
    //         url: "<?= site_url('setup_/deletePayrollRankSetup') ?>",
    //         type: "POST",
    //         data: {id:id, categ:categ},
    //         success:function(response){
    //             alert(response);
    //             loadtypeconfig();          
    //             loadrankconfig();       
    //             loadsetconfig(); 
    //         }
    //     });
    // });
    

    // $("#rank_table").dataTable({
    //     "pagination": "number",
    //     "oLanguage": {
    //                      "sEmptyTable":     "No Data Available.."
    //                  },
    //     "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]],
    //     "pageLength": 10,
    //     "scrollY": 200,
    //     "scrollX": true
    // });

    // $("#set_table").dataTable({
    //     "pagination": "number",
    //     "oLanguage": {
    //                      "sEmptyTable":     "No Data Available.."
    //                  },
    //     "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]],
    //     "pageLength": 10,
    //     "scrollY": 200,
    //     "scrollX": true
    // });

    if("<?=$this->session->userdata('canwrite')?>" == 0) $(".btn").css("pointer-events", "none");
    else $(".btn").css("pointer-events", "");

</script>
