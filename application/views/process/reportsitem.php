<?php

/**
 * @author P4
 * @copyright 2017
 */

$curr_date = date('Y-m-d');
?>
<style type="text/css">
table.dataTable thead .sorting_asc:after {
    display: none;
}
table.dataTable thead .sorting:after {
    display: none;
}
</style>
<div id="content">
  <div class="widgets_area">
  <div class="row">
  <form id='educbackgroundform'>
    <div class="col-md-12">
      <div class="well-content no-search" style="border: 0 !important;">
        <div class="panel animated fadeIn delay-1s">
            <div class="panel-heading" style="background-color: #0072c6;"><h4><b>ADMINISTRATIVE FUNCTIONS HANDLED</b></h4></div>
                <div class="panel-body">
                <div id="afh_data" ></div>
            </div>
        </div>

        <div class="panel animated fadeIn delay-1s">
            <div class="panel-heading" style="background-color: #0072c6;"><h4><b>AWARDS & RECOGNITION</b></h4></div>
                <div class="panel-body">
                <div id="ar_data" ></div>
            </div>
        </div>

        <div class="panel animated fadeIn delay-1s">
            <div class="panel-heading" style="background-color: #0072c6;"><h4><b>EDUCATIONAL BACKGROUND</b></h4></div>
                <div class="panel-body">
                <div id="eb_data" ></div>
            </div>
        </div>

        <div class="panel animated fadeIn delay-1s">
            <div class="panel-heading" style="background-color: #0072c6;"><h4><b>ELIGIBILITY</b></h4></div>
                <div class="panel-body">
                <div id="e_data" ></div>
            </div>
        </div>

        <div class="panel animated fadeIn delay-1s">
            <div class="panel-heading" style="background-color: #0072c6;"><h4><b>EMERGENCY CONTACT TYPE</b></h4></div>
                <div class="panel-body">
                <div id="ect_data" ></div>
            </div>
        </div>

        <div class="panel animated fadeIn delay-1s">
            <div class="panel-heading" style="background-color: #0072c6;"><h4><b>LEAVE CATEGORY</b></h4></div>
                <div class="panel-body">
                <div id="leave_data">
                </div>
            </div>
        </div>


        <div class="panel animated fadeIn delay-1s">
            <div class="panel-heading" style="background-color: #0072c6;"><h4><b>MEMBERSHIP OR AFFILIATION IN PROFESSIONAL ORGANIZATION</b></h4></div>
                <div class="panel-body">
                <div id="mapo_data" ></div>
            </div>
        </div>

        <div class="panel animated fadeIn delay-1s">
            <div class="panel-heading" style="background-color: #0072c6;"><h4><b>OTHER CREDENTIALS</b></h4></div>
                <div class="panel-body">
                <div id="oc_data" ></div>
            </div>
        </div>

        <div class="panel animated fadeIn delay-1s">
            <div class="panel-heading" style="background-color: #0072c6;"><h4><b>PEP DEVELOPMENT PROGRAM</b></h4></div>
                <div class="panel-body">
                <div id="pts_pdp2_data" ></div>
            </div>
        </div>

        <div class="panel animated fadeIn delay-1s">
            <div class="panel-heading" style="background-color: #0072c6;"><h4><b>PROFESSIONAL DEVELOPMENT PROGRAM</b></h4></div>
                <div class="panel-body">
                <div id="pts_pdp1_data" ></div>
            </div>
        </div>

        <div class="panel animated fadeIn delay-1s">
            <div class="panel-heading" style="background-color: #0072c6;"><h4><b>PROFESSIONAL INVOLVEMENTS</b></h4></div>
                <div class="panel-body">
                <div id="pi_data" ></div>
            </div>
        </div>


        <div class="panel animated fadeIn delay-1s">
            <div class="panel-heading" style="background-color: #0072c6;"><h4><b>PROFESSIONAL GROWTH AND DEVELOPMENT</b></h4></div>
                <div class="panel-body">
                <div id="pgd_data" ></div>
            </div>
        </div>

        <div class="panel animated fadeIn delay-1s">
            <div class="panel-heading" style="background-color: #0072c6;"><h4><b>PSYCHOSOCIAL - CULTURAL</b></h4></div>
                <div class="panel-body">
                <div id="pts_pdp3_data" ></div>
            </div>
        </div>

        <div class="panel animated fadeIn delay-1s">
            <div class="panel-heading" style="background-color: #0072c6;"><h4><b>PUBLICATION</b></h4></div>
                <div class="panel-body">
                <div id="pub_data" ></div>
            </div>
        </div>

        <div class="panel animated fadeIn delay-1s">
            <div class="panel-heading" style="background-color: #0072c6;"><h4><b>RESEARCHES</b></h4></div>
                <div class="panel-body">
                <div id="r_data" ></div>
            </div>
        </div>

        <div class="panel animated fadeIn delay-1s">
            <div class="panel-heading" style="background-color: #0072c6;"><h4><b>SCHOLARSHIP</b></h4></div>
                <div class="panel-body">
                <div id="s_data" ></div>
            </div>
        </div>

        <div class="panel animated fadeIn delay-1s">
            <div class="panel-heading" style="background-color: #0072c6;"><h4><b>SCHOOL YEAR</b></h4></div>
                <div class="panel-body">
                <div id="sy_data">
                </div>
            </div>
        </div>

        <div class="panel animated fadeIn delay-1s">
            <div class="panel-heading" style="background-color: #0072c6;"><h4><b>SPEAKING ENGAGEMENTS/RESOURCE SPEAKER</b></h4></div>
                <div class="panel-body">
                <div id="se_data" ></div>
            </div>
        </div>

        <div class="panel animated fadeIn delay-1s">
            <div class="panel-heading" style="background-color: #0072c6;"><h4><b>SUBJECT COMPETENT TO TEACH</b></h4></div>
                <div class="panel-body">
                <div id="sctt_data" ></div>
            </div>
        </div>
        

         <div class="panel animated fadeIn delay-1s">
            <div class="panel-heading" style="background-color: #0072c6;"><h4><b>TA/POVEDA SPIRITUAL and SPIRITUAL FORMATION PROGRAM</b></h4></div>
                <div class="panel-body">
                <div id="pts_pdp_data" ></div>
            </div>
        </div>

        <div class="panel animated fadeIn delay-1s">
            <div class="panel-heading" style="background-color: #0072c6;"><h4><b>TRAINING/WORKSHOPS (Related to Field of Expertise)</b></h4></div>
                <div class="panel-body">
                <div id="tw_data" ></div>
            </div>
        </div>
         
        <div class="panel animated fadeIn delay-1s">
            <div class="panel-heading" style="background-color: #0072c6;"><h4><b>VENUE</b></h4></div>
                <div class="panel-body">
                <div id="pts_data" ></div>
            </div>
        </div>
        
       
        
        
        <!-- <div class="panel animated fadeIn delay-1s">
            <div class="panel-heading" style="background-color: #0072c6;"><h4><b>SUBJECT COMPETENT TO TEACH</b></h4></div>
                <div class="panel-body">
                <div id="sctts_data" ></div>
            </div>
        </div> -->
        
        
        
        
        
        
        

        
      </div>
    </div>
  </form> 
  </div>
  </div> 
</div>
<div class="modal fade" id="myModal" data-backdrop="static"></div>

<script>
var toks = hex_sha512(" ");


$(document).ready(function(){
  var categories = {
    'EB'  :'eb_data',
    'E'   :'e_data',
    'OC'   :'oc_data',
    'ECT'   :'ect_data',
    'PTS' :'pts_data',
    'PTS_PDP' :'pts_pdp_data',
    'PTS_PDP1' :'pts_pdp1_data',
    'PTS_PDP2' :'pts_pdp2_data',
    'PTS_PDP3' :'pts_pdp3_data',
    'PGD' :'pgd_data',
    'R'   :'r_data',
    'AR'  :'ar_data',
    'PUB'  :'pub_data',
    'S'   :'s_data',
    'PI'  :'pi_data',
    'TW'  :'tw_data',
    'SE'  :'se_data',
    'MAPO':'mapo_data',
    'AFH' :'afh_data',
    'SCTT': 'sctt_data',
    'SCTTS': 'sctts_data',
    'LD': 'leave_data'
  };

    loadSchoolYearData();

    for (var key in categories){
        loadreportsitemdata(categories[key],key);
    }

    // loadreportsitemdata();

});


function loadSchoolYearData(){
    $("#sy_data").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
    $.ajax({
      url: "<?= site_url("reportsitem_/loadSchoolYearData") ?>",
      success: function(res){
        $("#sy_data").html(res);
        if("<?=$this->session->userdata('canwrite')?>" == 0) $("#sy_data").find(".btn, a").css("pointer-events", "none");
        else $("#sy_data").find(".btn").css("pointer-events", "");
      }
    })
}

function loadreportsitemdata(display,category,){
    var label = {
    'eb_data'     : 'Educational Level',
    'pts_data'   : 'Venue',
    'pts_pdp_data' : 'Title',
    'pts_pdp1_data'  : 'Title',
    'pts_pdp2_data'  : 'Title',
    'pts_pdp3_data'  : 'Title',
    'ect_data'   : 'Type',
    'e_data'     : 'Exam Description',
    'oc_data'    : 'Other Credentials',
    'sctt_data'    : 'Subject Competent to Teach',
    'pgd_data'   : 'Professional Growth and Development',
    'r_data'     : 'Researches',
    'ar_data'    : 'Award',
    'pub_data'    : 'Type of Publication',
    's_data'     : 'Type of Scholarship',
    'pi_data'    : 'Professional Involvements',
    'tw_data'    : 'Training/Workshop',
    'se_data'    : 'Speaking Engagements/Resource Speaker',
    'mapo_data'    : 'Membership',
    'afh_data'   : 'Administrative Functions Handled',
    'leave_data' : 'Leave Category'
    };
    var label_desc = "";
    $.each(label, function (value, description) {
        if(display == value){
            label_desc = description.toUpperCase();
        }
    })
   $("#"+display).html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
   $.ajax({
      url      :   "<?=site_url("reportsitem_/loadreportsitemdata")?>",
      type     :   "POST",
      data     :   {category: GibberishAES.enc( category, toks),displaydata: GibberishAES.enc( display, toks), toks:toks},
      // data     :   {stat : stat, cnoti : cnoti},
      success  :   function(msg){
       
       $("#"+display).html(msg);
       $("#"+display).find("#th_label").text(label_desc);
       if("<?=$this->session->userdata('canwrite')?>" == 0) $("#"+display).find(".btn, a").css("pointer-events", "none");
       else $("#"+display).find(".btn").css("pointer-events", "");
      }
   });
}
/*function loadreportsitemdata(){
   $("#eb_data").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
   $.ajax({
      url      :   "<?=site_url("reportsitem_/loadreportsitemdata")?>",
      type     :   "POST",
      data     :   {category:'EB'},
      // data     :   {stat : stat, cnoti : cnoti},
      success  :   function(msg){
       $("#eb_data").html(msg);
      }
   });
}*/
function loadreportsitemelig(){
   $("#eb_data").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
   $.ajax({
      url      :   "<?=site_url("reportsitem_/loadreportsitemelig")?>",
      type     :   "POST",
      data     :   {category: GibberishAES.enc( 'E', toks), toks:toks},
      // data     :   {stat : stat, cnoti : cnoti},
      success  :   function(msg){
       $("#e_data").html(msg);
       if("<?=$this->session->userdata('canwrite')?>" == 0) $("#e_data").find(".btn").css("pointer-events", "none");
       else $("#e_data").find(".btn").css("pointer-events", "");
      }
   });
}

function loadreportsitemothercred(){
   $("#eb_data").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
   $.ajax({
      url      :   "<?=site_url("reportsitem_/loadreportsitemothercred")?>",
      type     :   "POST",
      data     :   {category: GibberishAES.enc( 'E', toks), toks:toks},
      success  :   function(msg){
       $("#oc_data").html(msg);
       if("<?=$this->session->userdata('canwrite')?>" == 0) $("#oc_data").find(".btn").css("pointer-events", "none");
       else $("#oc_data").find(".btn").css("pointer-events", "");
      }
   });
}

function loadreportsitemscho(){
   $("#eb_data").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
   $.ajax({
      url      :   "<?=site_url("reportsitem_/loadreportsitemscho")?>",
      type     :   "POST",
      data     :   {category: GibberishAES.enc( 'E', toks), toks:toks},
      success  :   function(msg){
       $("#s_data").html(msg);
       if("<?=$this->session->userdata('canwrite')?>" == 0) $("#s_data").find(".btn").css("pointer-events", "none");
       else $("#s_data").find(".btn").css("pointer-events", "");
      }
   });
}


$(document).on("click","a[tag='add_eb']",function(){
    var category = $(this).attr('category');
    var displaydata = $(this).attr('displaydata');
    var desc = {
    'eb_data'     : 'Educational Background',
    'pts_data'   : 'Venue',
    'pts_pdp_data' : 'PSSFP',
    'pts_pdp1_data'  : 'Professional Development Program',
    'pts_pdp2_data'  : 'Pep Development Program',
    'pts_pdp3_data'  : 'Psychosocial - Cultural',
    'ect_data'   : 'Emergency Contact Type',
    'e_data'     : 'Eligibility',
    'oc_data'    : 'Other Credentials',
    'sctt_data'    : 'Subject Competent to Teach',
    'pgd_data'   : 'Professional Growth and Development',
    'r_data'     : 'Researches',
    'ar_data'    : 'Awards & Recognition',
    'pub_data'    : 'Publication',
    's_data'     : 'Scholarship',
    'pi_data'    : 'Professional Involvements',
    'tw_data'    : 'Training/Workshop',
    'se_data'    : 'Speaking Engagements/Resource Speaker',
    'mapo_data'    : 'Membership',
    'afh_data'   : 'Administrative Functions Handled',
    'leave_data' : 'Leave Category'
    };

    var label = {
    'eb_data'     : 'Educational Level',
    'pts_data'   : 'Venue',
    'pts_pdp_data' : 'Title',
    'pts_pdp1_data'  : 'Title',
    'pts_pdp2_data'  : 'Title',
    'pts_pdp3_data'  : 'Title',
    'ect_data'   : 'Type',
    'e_data'     : 'Exam Description',
    'oc_data'    : 'Other Credentials',
    'sctt_data'    : 'Subject Competent to Teach',
    'pgd_data'   : 'Professional Growth and Development',
    'r_data'     : 'Researches',
    'ar_data'    : 'Award',
    'pub_data'    : 'Type of Publication',
    's_data'     : 'Type of Scholarship',
    'pi_data'    : 'Professional Involvements',
    'tw_data'    : 'Training/Workshop',
    'se_data'    : 'Speaking Engagements/Resource Speaker',
    'mapo_data'    : 'Membership',
    'afh_data'   : 'Administrative Functions Handled',
    'leave_data' : 'Leave Category'
    };

    $("#modal-view").find("div[tag='display']").html("");
    var title_desc = "";
    var label_desc = "";
    $.each(desc, function (value, description) {
        if(displaydata == value){
            title_desc = "Add "+description;
        }
    }) 

    $.each(label, function (value, description) {
        if(displaydata == value){
            label_desc = description;
        }
    })
    $("#modal-view").find("h3[tag='title']").text(title_desc); 
    $("#button_save_modal").text("Save");    

    $.ajax({

        url: "<?=site_url('reportsitem_/educbackgrounditems')?>",
        type: "POST",
        data : {naction:'add',category : category,displaydata:displaydata},
        success: function(msg){
            $("#modal-view").find("div[tag='display']").html(msg);
            $("#modal-view").find("#desc_label").text(label_desc);

        }
    });  
});

$(document).on("click","a[tag='edit_eb']",function(){
    var param = $(this).attr('edit_eb');
    var category = $(this).attr('category');
    var displaydata = $(this).attr('displaydata');
    var desc = {
    'eb_data'     : 'Educational Background',
    'pts_data'   : 'Venue',
    'pts_pdp_data' : 'PSSFP',
    'pts_pdp1_data'  : 'Professional Development Program',
    'pts_pdp2_data'  : 'Pep Development Program',
    'pts_pdp3_data'  : 'Psychosocial - Cultural',
    'ect_data'   : 'Emergency Contact Type',
    'e_data'     : 'Eligibility',
    'oc_data'    : 'Other Credentials',
    'sctt_data'    : 'Subject Competent to Teach',
    'pgd_data'   : 'Professional Growth and Development',
    'r_data'     : 'Researches',
    'ar_data'    : 'Awards & Recognition',
    'pub_data'    : 'Publication',
    's_data'     : 'Scholarship',
    'pi_data'    : 'Professional Involvements',
    'tw_data'    : 'Training/Workshop',
    'se_data'    : 'Speaking Engagements/Resource Speaker',
    'mapo_data'    : 'Membership',
    'afh_data'   : 'Administrative Functions Handled',
    'leave_data' : 'Leave Category'
    };

    var label = {
    'eb_data'     : 'Educational Level',
    'pts_data'   : 'Venue',
    'pts_pdp_data' : 'Title',
    'pts_pdp1_data'  : 'Title',
    'pts_pdp2_data'  : 'Title',
    'pts_pdp3_data'  : 'Title',
    'ect_data'   : 'Type',
    'e_data'     : 'Exam Description',
    'oc_data'    : 'Other Credentials',
    'sctt_data'    : 'Subject Competent to Teach',
    'pgd_data'   : 'Professional Growth and Development',
    'r_data'     : 'Researches',
    'ar_data'    : 'Award',
    'pub_data'    : 'Type of Publication',
    's_data'     : 'Type of Scholarship',
    'pi_data'    : 'Professional Involvements',
    'tw_data'    : 'Training/Workshop',
    'se_data'    : 'Speaking Engagements/Resource Speaker',
    'mapo_data'    : 'Membership',
    'afh_data'   : 'Administrative Functions Handled',
    'leave_data' : 'Leave Category'
    };

    
    var title_desc = "";
    var label_desc = "";
    $.each(desc, function (value, description) {
        if(displaydata == value){
            title_desc = "Edit "+description;
        }
    }) 
    $.each(label, function (value, description) {
        if(displaydata == value){
            label_desc = description;
        }
    })
    $("#modal-view").find("h3[tag='title']").text(title_desc);
    $("#button_save_modal").text("Save");   
    $.ajax({
        url: "<?=site_url('reportsitem_/getreportsitemdata')?>",
        type: "POST",
        data : {editvalue :  GibberishAES.enc( param, toks), naction: GibberishAES.enc( 'edit', toks),displaydata: GibberishAES.enc(displaydata , toks),category: GibberishAES.enc( category, toks), toks:toks},
        success: function(msg){ 
           $("#modal-view").find("div[tag='display']").html(msg);
           $("#modal-view").find("#desc_label").text(label_desc);
        }
    });  
});

$(document).on("click","a[tag='delete_eb']",function(){
    var param = $(this).attr('delete_eb');
    var category = $(this).attr('category');
    var displaydata = $(this).attr('displaydata');
    var desc = {
    'eb_data'     : 'Educational Background',
    'pts_data'   : 'Venue',
    'pts_pdp_data' : 'PSSFP',
    'pts_pdp1_data'  : 'Professional Development Program',
    'pts_pdp2_data'  : 'Pep Development Program',
    'pts_pdp3_data'  : 'Psychosocial - Cultural',
    'ect_data'   : 'Emergency Contact Type',
    'e_data'     : 'Eligibility',
    'oc_data'    : 'Other Credentials',
    'sctt_data'    : 'Subject Competent to Teach',
    'pgd_data'   : 'Professional Growth and Development',
    'r_data'     : 'Researches',
    'ar_data'    : 'Awards & Recognition',
    'pub_data'    : 'Publication',
    's_data'     : 'Scholarship',
    'pi_data'    : 'Professional Involvements',
    'tw_data'    : 'Training/Workshop',
    'se_data'    : 'Speaking Engagements/Resource Speaker',
    'mapo_data'    : 'Membership',
    'afh_data'   : 'Administrative Functions Handled',
    'leave_data' : 'Leave Category'
    };

    var title_desc = "";
    $.each(desc, function (value, description) {
        if(displaydata == value){
            title_desc = description;
        }
    }) 
    // console.log(category + ' ' + displaydata);
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
                    $.ajax({
                        url: "<?=site_url('reportsitem_/deletereportsitemdata')?>",
                        type: "POST",
                        data : {editvalue : GibberishAES.enc( param, toks), naction: GibberishAES.enc( 'delete', toks),displaydata: GibberishAES.enc( displaydata, toks),category: GibberishAES.enc( category, toks),reportdesc:GibberishAES.enc( title_desc, toks), toks:toks},
                        success: function(msg){
                            if(msg == "used"){
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Warning!',
                                    text: title_desc+' is being used.',
                                    showConfirmButton: true,
                                    timer: 1500
                                })
                            }else{
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: title_desc+' has been deleted successfully.',
                                    showConfirmButton: true,
                                    timer: 1500
                                })
                                setTimeout(function(){
                                    $("#"+displaydata).html(msg);
                                    if("<?=$this->session->userdata('canwrite')?>" == 0) $("#"+displaydata).find(".btn").css("pointer-events", "none");
                                    else $("#"+displaydata).find(".btn").css("pointer-events", "");
                                },1500)
                            }
                        }
                    });
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
      
});




$("#dfrom,#ldfrom,#ldto").datepicker({
    autoclose: true,
    todayBtn : true
});
$(".chosen").chosen();
</script> 
