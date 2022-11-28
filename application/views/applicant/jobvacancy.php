<? $this->load->view('applicant/header'); ?>
<link rel="stylesheet" type="text/css" href="<?=base_url()?>css/login.css">
<div class="col-md-11" style="margin-top: 2%;min-width: 98%;">
    <div class="panel animated fadeIn" style="border: 5px solid #c3c3c1 !important;box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;margin-bottom: 49px !important;">
       <div class="panel-heading" style="background-color:black;color:#fff;padding-bottom: 0%;border-bottom: 1px solid transparent;">
        <center>
            <table>
                <tr>
                    <td><img src="<?=base_url()?>images/school_logo.png" align="left" class="media-object" style="width:150px;margin-left: 9%;margin-top: 39px;"></td>
                    <td style="width: 55%;"><h3 style="margin-bottom: 8%;color: white"><b>Job Vacancies</b></h4></td>
                </tr>
            </table>
                <div class="wrap">
                   <div class="search" style="-webkit-box-sizing: unset!important;box-sizing: unset!important;box-sizing: inherit;">
                        <input type="text" class="searchTerm" id="srcJob" placeholder="What are you looking for?">
                        <button class="btn btn-success" style="margin-top: 0.2%;width: 9.5%;height: 63%;font-size: 19px;border-color: white;background-color: teal;" id="searchJob">Search</button>
                   </div>
                   <h4 style="text-align: left;"><b><span id="available_jobs"></span> Available Job</b></h4>
                </div>
        </center>
        </div>
           <div class="panel-body" id="data_table" style="overflow: auto !important;">

           </div>
    </div>
</div>

<div class="modal fade" id="signup_modal" role="dialog" data-backdrop="static">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <div class="media">
          <div class="media-left">
            <img src="<?=base_url()?>images/school_logo.png" class="media-object" style="width:60px">
          </div>
          <div class="media-body" style="font-weight: bold;padding-top: 10px;">
            <h4 class="media-heading">Pinnacle Technologies Inc.</h4>
            <p>D`Great</p>
          </div>
        </div>
        <center><b><h3 tag="title" class="modal-title">FILL UP FORM TO PROCEED</h3></b></center>
      </div>
      <div class="modal-body">
        <div class="row" style="margin-right: -15px;margin-left: 11px;">
              <div id='display'>
                
              </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn btn-danger" id="modalclose">Close</button>
      </div>
    </div>

  </div>
</div>


<script src="<?=base_url()?>jsbstrap/library/jquery.backstretch.min.js"></script>
<script>
  var toks = hex_sha512(" ");
  $(document).ready(function(){
    JobTable('');
    countAvailableJobs();
    checkJobAvailability();
  });

/*$('#srcJob').keyup(function(){
var word = $(this).val();
JobTable(word);
});*/

    function checkJobAvailability(){
        $.ajax({
            url: "<?= site_url('applicant/checkjobs')?>",
            success:function(response){
                console.log(response);
            }
        });
    }

    function JobTable(word){
      var string = word;
        $.ajax({
            type: 'POST',
            data:{string: GibberishAES.enc( string, toks), toks:toks},
            url: "<?= site_url('applicant/loadJobTable')?>",
            success:function(response){
                $("#data_table").html(response);
            }
        });
    }

  function loadDescription(positionid){
      $.ajax({
        url   : "<?=site_url("applicant/getJobDescription")?>",
        type  : "POST",
        data  : {'positionid':positionid}
      });
  }

  $('.chosen').chosen();
</script>
<script>
  $(document).ready(function(){
      var positionid = $('#position').val();
      loadQualification(positionid);

  });


  $('#position').on('change',function(){
      loadQualification($(this).val());
  });




  function loadQualification(positionid){
      $.ajax({
        url   : "<?=site_url("applicant/getJobQualification")?>",
        type  : "POST",
        data  : {'positionid': GibberishAES.enc( positionid, toks),toks:toks},
        success : function(msg){
            $('#signup').html(msg);
        }

      });
  }

  function countAvailableJobs(){
    $.ajax({
      url: "<?=site_url('applicant/countAvailableJobs')?>",
      success:function(response){
        $("#available_jobs").text(response);
      }
    });
  }

  $("#applynow").click(function(){
    
    $.ajax({
        url : "<?= site_url('applicant/applicantSignupForm') ?>",
        success:function(response){
            $("#signup_modal").html(response);
            $("#signup_modal").modal('toggle');
        }
    });
  });

  $('.chosen').chosen();
</script>