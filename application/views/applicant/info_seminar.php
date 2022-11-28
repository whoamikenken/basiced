        <?php

        /**
        * @author Justin
        * @copyright 2016
        */
        $CI =& get_instance();
        $CI->load->model('applicantt');

        $usertype = "EMPLOYEE";
        $applicable_field  = $ptscbox = $pgdcbox = $rescbox = $awardscbox = $schocbox = $semcbox = $twcbox = $resourcecbox = $orgcbox = $cominvolvecbox = $adminfcbox = "";
        $seminar = $pts = $pgd = $researches = $ar = $scho = $scs = $tw = $resource = $org = $community = $administrative = array();

        if(!$applicantId) $applicantId = $CI->applicantt->getApplicantId($lname, $fname, $mname);

        if($applicantId){
          $seminar = $this->db->query("SELECT title,place,`date`,resource_speaker,credit_earn  from applicant_seminar where employeeid='$applicantId'")->result();
          $pgd = $this->db->query("SELECT publication,title,publisher,datef,type,r.level,e.educ_level,r.ID from applicant_pgd e LEFT JOIN reports_item r ON e.educ_level = r.ID where employeeid='$applicantId'")->result();
          $pts = $this->db->query("SELECT title,datef,organizer,venue from applicant_pts where employeeid='$applicantId'")->result();
          $researches = $this->db->query("SELECT date_published,educ_level,title,r.level,r.ID from applicant_researches e LEFT JOIN reports_item r ON e.educ_level = r.ID where employeeid='$applicantId'")->result();
          $ar = $this->db->query("SELECT award, institution, address, datef,r.level,r.ID from applicant_awardsrecog e LEFT JOIN reports_item r ON e.educ_level = r.ID where employeeid='$applicantId'")->result();
          // $scho = $this->db->query("SELECT type_of_scho,gr_agency,prog_study,ins_scho,datef,dateto,r.ID,r.level from applicant_scholarship e LEFT JOIN reports_item r ON e.educ_level = r.ID where employeeid='$applicantId'")->result();
          $scho = array();
          $scs = $this->db->query("SELECT educational_level,school,title,speaker,type,location,address,date_attended,r.level,r.ID from applicant_scs e LEFT JOIN reports_item r ON e.educ_level = r.ID where employeeid='$applicantId'")->result();
          $tw = $this->db->query("SELECT educational_level,school,title,speaker,type,location,address,date_attended,r.ID,r.level from applicant_workshops e LEFT JOIN reports_item r ON e.educ_level = r.ID where employeeid='$applicantId'")->result();
          $resource = $this->db->query("SELECT datef,topic,organizer,e.venue as venue,r.ID,r.level from applicant_resource e LEFT JOIN reports_item r ON e.educ_level = r.ID where employeeid='$applicantId'")->result();
          $org   = $this->db->query("SELECT name_org,datef,position from applicant_proorg where employeeid='$applicantId'")->result();
          $community   = $this->db->query("SELECT school,educational_level,year_grad,honor,ctype from applicant_community where employeeid='$applicantId'")->result();
          $administrative   = $this->db->query("SELECT positionf,department,datef from applicant_administrative where employeeid='$applicantId'")->result();

          $applicable_field   = $this->db->query("SELECT * FROM applicant_applicable_fields WHERE employeeid='$applicantId'");
        if($applicable_field->num_rows > 0){
              $ptscbox            = $applicable_field->row(0)->profTraining;
              $pgdcbox            = $applicable_field->row(0)->profGrowth;
              $rescbox            = $applicable_field->row(0)->researches;
              $awardscbox         = $applicable_field->row(0)->awards;
              $schocbox           = $applicable_field->row(0)->scholarship;
              $semcbox            = $applicable_field->row(0)->seminar;
              $twcbox             = $applicable_field->row(0)->training;
              $resourcecbox       = $applicable_field->row(0)->speakingEngagement;
              $orgcbox            = $applicable_field->row(0)->profOrg;
              $cominvolvecbox     = $applicable_field->row(0)->comInvolvement;
              $adminfcbox         = $applicable_field->row(0)->adminFunctions;
          }
        }

        ?>
        <div class="widgets_area">
        <div class="row">
        <form id="seminarstraining">  
          <input type="hidden" name="job" value="employee/seminar_info">
          <div class="col-md-12">

          <div class="well-content no-search" style="border: 0 !important;">
                <input name="fname" type="hidden" value="<?=$fname?>"/>
                <input name="lname" type="hidden" value="<?=$lname?>"/>
                <input name="mname" type="hidden" value="<?=$mname?>"/>

            <div class='align_right'><label class="text-info">
                    <b>(Click SAVE for each tab you accomplish)</b></label> 
                    <a href="#" class="btn btn-primary" id="savetrainingseminars" forsubmit='false'>Save All Information</a>
            </div>
          <br/>
          <!-- PTS -->
       <div class="panel">
          <div class="panel-heading" style="background-color: #0072c6;"><h4><b>PROFESSIONAL TRAINING AND SEMINARS</b></h4></div>
            <div class="panel-body">
              <div>
                  <input type="checkbox" name="ptscbox" class="applicable-field" <?= ($ptscbox == "0" ? "checked" : "") ?> >
                  <span style="font-style: italic;color:#e49191">&nbsp;Check this box if Not Applicable</span>
              </div>
              <table class="table table-hover" id="ptsinfolist">
                 <thead>
                    <tr>
                       <th>Title</th>
                       <th>Date</th>
                       <th>Organizer</th>
                       <th>Venue</th>
                       <th class="col-md-2">&nbsp;</th>
                    </tr>
                 </thead>
                 <tbody>
    <?
                 if(count($pts)>0)
    {

                      foreach($pts as $sm)
    {
    ?>
                      <tr>
                          <td><?=$sm->title?></td>
                          <td><?=$sm->datef?></td>
                          <td><?=$sm->organizer?></td>
                          <td><?=$sm->venue?></td>
                          <td class="align_center">
                          <a class='btn btn-danger delete_entry'><i class='glyphicon glyphicon-trash'></i></a>
                          </td>
                      </tr>    
    <?                            
        }
        }     
            else
    ?>
                 </tbody>
              </table>
              <a href="#seminarModal" tag="add_pts" data-toggle="modal" class="btn btn-primary"><i class="icon glyphicon glyphicon-plus"></i> Add Training & Seminars</a>
            </div>
      </div>
          <!-------------------- PGD ----------------------->
          <div class="panel animated fadeIn">
            <div class="panel-heading "style="background-color: #0072c6;"><h4><b>Publication</b></h4></div>
               <div class="panel-body">
                 <div class="col-md-12">
                  <input type="checkbox" name="pgdcbox" class="applicable-field" <?= ($pgdcbox == "0" ? "checked" : "") ?> >
                  <span style="font-style: italic;">&nbsp;Check this box if Not Applicable</span>
                  <table class="table table-hover" id="pgdinfolist">
                 <thead>
                    <tr>
                       <th>Type of Publication</th>
                       <th>Title</th>
                       <th>Publisher</th>
                       <th>Date Published</th>
                       <th>Type of Authorship</th>
                       <th class="col-md-2">&nbsp;</th>
                    </tr>
                 </thead>
                 <tbody>
    <?
                 if(count($pgd)>0)
    {
                      foreach($pgd as $sm)
    {
    ?>
                      <tr>
                          <td><?=$sm->publication?></td>
                          <td><?=$sm->title?></td>
                          <td><?=$sm->publisher?></td>
                          <td><?=$sm->type?></td>
                          <td><?=$sm->datef?></td>
                          <td class="align_center">
                          <a class='btn btn-danger delete_entry'><i class='glyphicon glyphicon-trash'></i></a>
                          </td>
                      </tr>    
    <?                            
        }
        }
                 else
    ?>
                 </tbody>
                </table>
                <a href="#seminarModal" tag="add_pgd" data-toggle="modal" class="btn btn-primary"><i class="icon glyphicon glyphicon-plus"></i> Add Publication &amp; Development</a>
                 </div>
               </div>
         </div>
          <!-- AWARDS & RECOGNITION -->
          <div class="panel animated fadeIn">
             <div class="panel-heading "style="background-color: #0072c6;"><h4><b>Awards &amp; Recognition</b></h4></div>
               <div class="panel-body">
                <div class="col-md-12">
                  <input type="checkbox" name="awardscbox" class="applicable-field" <?= ($awardscbox == "0" ? "checked" : "") ?> >
                  <span style="font-style: italic;">&nbsp;Check this box if Not Applicable</span>
                  <table class="table table-hover" id="arinfolist">
                 <thead>
                    <tr>
                       <th>Type of Award</th>
                       <th>Granting Agency</th>
                       <th>Place</th>
                       <th>Date Given</th>
                       <th class="col-md-2">&nbsp;</th>
                    </tr>
                 </thead>
                 <tbody>
    <?
                 if(count($ar)>0)
    {
                      foreach($ar as $sm)
    {
    ?>
                      <tr>
                          <td educar='<?=$sm->ID?>'><?=$sm->level?></td> -->
                          <td><?=$sm->award?></td>
                          <td><?=$sm->institution?></td>
                          <td><?=$sm->address?></td>
                          <td><?=$sm->datef?></td>
                          <td class="align_center">
                          <a class='btn btn-danger delete_entry'><i class='glyphicon glyphicon-trash'></i></a>
                          </td>
                      </tr>    
    <?                            
        }
        }       else
    ?>
                 </tbody>
              </table>
              <a href="#seminarModal" tag="add_ar" data-toggle="modal" class="btn btn-primary"><i class="icon glyphicon glyphicon-plus"></i> Add Awards &amp; Recognition</a>
                </div>
              </div>
           </div>
           <!-- Scholarship -->
           <div class="panel animated fadeIn">
              <div class="panel-heading "style="background-color: #0072c6;"><h4><b>Scholarship</b></h4></div>
                <div class="panel-body">
                  <div class="col-md-12">
                      <input type="checkbox" name="schocbox" class="applicable-field" <?= ($schocbox == "0" ? "checked" : "") ?> >
                      <span style="font-style: italic;">&nbsp;Check this box if Not Applicable</span>
                      <table class="table table-hover" id="schoinfolist">
                      <thead>
                        <tr>
                           <th rowspan="2">Type of Scholarship</th>
                           <th rowspan="2">Granting Agency</th>
                           <th rowspan="2">Program of Study</th>
                           <th rowspan="2">Institution</th>
                           <th colspan="2">Inclusive Dates</th>
                           <th colspan="2">Inclusive Dates</th>
                           <th class="col-md-2">&nbsp;</th>
                        </tr>
                        <tr>
                          <th>From</th>
                          <th>To</th>
                        </tr>
                     </thead>
                     <tbody>
    <?
                 if(count($scho)>0)
    {
                      foreach($scho as $sm)
    {
    ?>
                      <tr>
                          <td><?=$sm->type_of_scho?></td>
                          <td><?=$sm->gr_agency?></td>
                          <td><?=$sm->prog_study?></td>
                          <td><?=$sm->ins_scho?></td>
                          <td><?=$sm->datef?></td>
                          <td><?=$sm->dateto?></td>
                          <td class="align_center">
                          <a class='btn btn-danger delete_entry'><i class='glyphicon glyphicon-trash'></i></a>
                          </td>
                      </tr>    
    <?                            
        }
        }        else
    ?>
                 </tbody>
              </table>
              <a href="#seminarModal" tag="add_scho" data-toggle="modal"><i class="icon glyphicon glyphicon-plus"></i> Add New</a>
                </div>
              </div>
           </div>
         <!-- Professional Involvements -->
        <div class="panel">
          <div class="panel-heading" style="background-color: #0072c6;"><h4><b>PROFESSIONAL INVOLVEMENTS</b></h4></div>
            <div class="panel-body">
              <!-- ------------SPEAKING ENGAGEMENTS/RESOURCE SPEAKER--------------- -->
              <table class="table table-hover" id="resourceinfolist">
                 <thead>
                    <tr colspan="5"><h5><b>Speaking Engagements/Resource Speaker</b></h5></tr>  
                    <tr>
                       <th>Date</th>
                       <th>Topic</th>
                       <th>Organizer</th>
                       <th>Venue</th>
                       <th class="col-md-2">&nbsp;</th>
                    </tr>
                 </thead>
                 <tbody>
    <?
                 if(count($resource)>0)
    {
                      foreach($resource as $sm)
    {
    ?>
                      <tr>
                          <td><?=$sm->datef?></td>
                          <td><?=$sm->topic?></td>
                          <td><?=$sm->organizer?></td>
                          <td><?=$sm->venue?></td>
                          <td class="align_center">
                          <a class='btn btn-danger delete_entry'><i class='glyphicon glyphicon-trash'></i></a>
                          </td>
                      </tr>    
    <?                            
        }
        }else
    ?>
                 </tbody>
              </table>
              <a href="#seminarModal" tag="add_resource" data-toggle="modal" class="btn btn-success"><i class="icon glyphicon glyphicon-plus"></i> Add New</a>
              <hr />
              <div>
                  <input type="checkbox" name="orgcbox" class="applicable-field" <?= ($orgcbox == "0" ? "checked" : "") ?> >
                  <span style="font-style: italic;color:#e49191">&nbsp;Check this box if Not Applicable</span>
              </div>
              <table class="table table-hover" id="orginfolist">
                 <thead>
                    <tr colspan="5"><h5><b>Application and Civic Organization</b></h5></tr>  
                    <tr>
                       <th>Name of Organization</th>
                       <th>Inclusive Dates</th>
                       <th>Position</th>
                       <th class="col-md-2">&nbsp;</th>
                    </tr>
                 </thead>
                 <tbody>
    <?
                 if(count($org)>0)
    {
                      foreach($org as $sm)
    {
    ?>
                      <tr>
                          <td><?=$sm->name_org?></td>
                          <td><?=$sm->datef?></td>
                          <td><?=$sm->position?></td>
                          <td class="align_center">
                          <a class='btn btn-danger delete_entry'><i class='glyphicon glyphicon-trash'></i></a>
                          </td>
                      </tr>    
    <?                            
        }
        }        else
    
    ?>
                              
                 </tbody>
              </table>
              <a href="#seminarModal" tag="add_org" data-toggle="modal" class="btn btn-success"><i class="icon glyphicon glyphicon-plus"></i> Add New</a>
              <hr />
              <div>
                  <input type="checkbox" name="cominvolvecbox" class="applicable-field" <?= ($cominvolvecbox == "0" ? "checked" : "") ?> >
                  <span style="font-style: italic;color:#e49191">&nbsp;Check this box if Not Applicable</span>
              </div>
              <table class="table table-hover" id="communityinfolist">
                 <thead>
                    <tr colspan="5"><h5><b>Membership in Civic Organizations/Community Involvement</b></h5></tr>  
                    <tr>
                       <th>Name of School</th>
                       <th>Education Level</th>
                       <th>Year Graduated</th>
                       <th>Honor</th>
                       <th>Type</th>
                       <th class="col-md-2">&nbsp;</th>
                    </tr>
                 </thead>
                 <tbody>
    <?
                 if(count($community)>0)
    {
                      foreach($community as $sm)
    {
    ?>
                      <tr>
                          <td><?=$sm->school?></td>
                          <td><?=$sm->educational_level?></td>
                          <td><?=$sm->honor?></td>
                          <td><?=$sm->year_grad?></td>
                          <td><?=$sm->ctype?></td>
                          <td class="align_center"> 
                          <a class='btn btn-danger delete_entry'><i class='glyphicon glyphicon-trash'></i></a>
                          </td>
                      </tr>    
    <?                            
                      }
                 }else
    ?>
                         
                 </tbody>
              </table>
              <a href="#seminarModal" tag="add_community" data-toggle="modal" class="btn btn-success"><i class="icon glyphicon glyphicon-plus"></i> Add New</a>
              <hr />
              <div>
                  <input type="checkbox" name="adminfcbox" class="applicable-field" <?= ($adminfcbox == "0" ? "checked" : "") ?> >
                  <span style="font-style: italic;color:#e49191">&nbsp;Check this box if Not Applicable</span>
              </div>
              <table class="table table-hover" id="administrativeinfolist">
                 <thead>
                    <tr colspan="5"><h5><b>Administrative Functions Handled in Poveda</b></h5></tr>  
                    <tr>
                       <th>Position</th>
                       <th>Department</th>
                       <th>Date</th>
                       <th class="col-md-2">&nbsp;</th>
                    </tr>
                 </thead>
                 <tbody>
    <?
                 if(count($administrative)>0){
                      foreach($administrative as $sm){
    ?>
                      <tr>
                          <td><?=$sm->positionf?></td>
                          <td><?=$sm->department?></td>
                          <td><?=$sm->datef?></td>
                          <td class="align_center">
                          <a class='btn btn-danger delete_entry'><i class='glyphicon glyphicon-trash'></i></a>
                          </td>
                      </tr>    
    <?                            
                      }
                 }else
    ?>    
                 </tbody>
              </table>
              <a href="#seminarModal" tag="add_administrative" data-toggle="modal" class="btn btn-success"><i class="icon glyphicon glyphicon-plus"></i> Add New</a>
              <hr />
             </div>
            </div>
          </div>    
          </div>
        </form>
        </div>
        </div>

    <div class="modal fade" id="seminarModal" role="dialog" data-backdrop="static">
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
                  <center><b><h3 tag="title" class="modal-title">Modal Header</h3></b></center>
              </div>
              <div class="modal-body">
                  <div class="row">
                      <div id="display">
                          
                      </div>
                  </div>
              </div>
              <div class="modal-footer">
                  <a type="button" data-dismiss="modal" class="btn btn-danger" id="modalclose">Close</a>
                  <a type="button" class="btn btn-success" id='button_save_modal'>Save changes</a>
              </div>
          </div>

      </div>
    </div>

    <script>


    $("#seminarModal").find("#button_save_modal").addClass("button_save_modal");
    $("#seminarModal").find("#modalclose").addClass("modalclose");

    $(".delete_entry").click(function(){
      $(this).parent().parent().remove();
    });

    $(".pts").click(function(){
      addpts($(this));
    });
    $("a[tag='add_pts']").click(function(){
      addpts("");
    });
    function addpts(obj){
      $("#seminarModal").find("h3[tag='title']").text(obj ? "Edit Data" : "Add New");
      $("#button_save_modal").text("Save");    
      $.ajax({
          url: "<?=site_url('employee_/pts')?>",
          type: "POST",
          success: function(msg){
              var modal_display = $("#seminarModal").find("#display");
              $.when($(modal_display).html(msg)).done(function(){ 
                 if(obj){
                   var tdcur = $(obj).parent().parent();
                   $(tdcur).attr("iscurrent",1);
                   $(modal_display).find("input[name='sm_title']").val(tdcur.find("td:eq(0)").text()); 
                   $(modal_display).find("input[name='sm_datef']").val(tdcur.find("td:eq(1)").text());
                   $(modal_display).find("input[name='sm_organizer']").val(tdcur.find("td:eq(2)").text());
                   $(modal_display).find("select[name='sm_venue']").val(tdcur.find("td:eq(3)").text()); 
                }else{
                   $("#ptsinfolist").find("tr").each(function(){
                     $(this).attr("iscurrent",0); 
                   }) 
                }
              });
          }
      });  
    }
// END PTS -----------------------------------------
    $(".pgd").click(function(){
      addpgd($(this));
    });
    $("a[tag='add_pgd']").click(function(){
      addpgd("");
    });
    function addpgd(obj){
      $("#seminarModal").find("h3[tag='title']").text(obj ? "Edit Data" : "Add New");
      $("#button_save_modal").text("Save");    
      $.ajax({
          url: "<?=site_url('employee_/pgd')?>",
          type: "POST",
          success: function(msg){
              var modal_display = $("#seminarModal").find("#display");
              $.when($(modal_display).html(msg)).done(function(){ 
                 if(obj){
                   var tdcur = $(obj).parent().parent();
                   $(tdcur).attr("iscurrent",1);
                   $(modal_display).find("input[name='sm_publication']").val(tdcur.find("td:eq(0)").text()); 
                   $(modal_display).find("input[name='sm_title']").val(tdcur.find("td:eq(1)").text());
                   $(modal_display).find("input[name='sm_publisher']").val(tdcur.find("td:eq(2)").text());
                   $(modal_display).find("input[name='sm_date']").val(tdcur.find("td:eq(3)").text());
                   $(modal_display).find("input[name='sm_type']").val(tdcur.find("td:eq(4)").text());
                }else{
                   $("#pgdinfolist").find("tr").each(function(){
                     $(this).attr("iscurrent",0); 
                   }) 
                }
              });
          }
      });  
    }
// END OF PGD ----------------------------------
    $(".researches").click(function(){
      addresearches($(this));
    });
    $("a[tag='add_researches']").click(function(){
      addresearches("");
    });
    function addresearches(obj){
      $("#seminarModal").find("h3[tag='title']").text(obj ? "Edit Data" : "Add New");
      $("#button_save_modal").text("Save");    
      $.ajax({
          url: "<?=site_url('employee_/researches')?>",
          type: "POST",
          success: function(msg){
              var modal_display = $("#seminarModal").find("#display");
              $.when($(modal_display).html(msg)).done(function(){ 
                 if(obj){
                   var tdcur = $(obj).parent().parent();
                   $(tdcur).attr("iscurrent",1);
                   $(modal_display).find("input[name='eb_date']").val(tdcur.find("td:eq(0)").text()); 
                   $(modal_display).find("select[name='eb_level']").val(tdcur.find("td:eq(1)").text());
                   $(modal_display).find("input[name='eb_yeargrad']").val(tdcur.find("td:eq(2)").text());
                   $(modal_display).find("input[name='eb_honor']").val(tdcur.find("td:eq(3)").text());
                }else{
                   $("#researchesinfolist").find("tr").each(function(){
                     $(this).attr("iscurrent",0); 
                   }) 
                }
              });
          }
      });  
    }
// END OF RESEARCH -------------------------------
    $(".ar").click(function(){
      addar($(this));
    });
    $("a[tag='add_ar']").click(function(){
      addar("");
    });
    function addar(obj){
      $("#seminarModal").find("h3[tag='title']").text(obj ? "Edit Data" : "Add New");
      $("#button_save_modal").text("Save");    
      $.ajax({
          url: "<?=site_url('employee_/ar')?>",
          type: "POST",
          success: function(msg){
              var modal_display = $("#seminarModal").find("#display");
              $.when($(modal_display).html(msg)).done(function(){ 
                 if(obj){
                   var tdcur = $(obj).parent().parent();
                   $(tdcur).attr("iscurrent",1);
                   $(modal_display).find("input[name='ar_award']").val(tdcur.find("td:eq(0)").text()); 
                   $(modal_display).find("input[name='ar_institution']").val(tdcur.find("td:eq(1)").text());
                   $(modal_display).find("input[name='ar_address']").val(tdcur.find("td:eq(2)").text());
                   $(modal_display).find("input[name='ar_datef']").val(tdcur.find("td:eq(3)").text());
                }else{
                   $("#arinfolist").find("tr").each(function(){
                     $(this).attr("iscurrent",0); 
                   }) 
                }
              });
          }
      });  
    }
// END OF AWARD AND RECOGNITION-------------------
    $(".scho").click(function(){
      addscho($(this));
    });
    $("a[tag='add_scho']").click(function(){
      addscho("");
    });
    function addscho(obj){
      $("#seminarModal").find("h3[tag='title']").text(obj ? "Edit Data" : "Add New");
      $("#button_save_modal").text("Save");    
      $.ajax({
          url: "<?=site_url('employee_/scho')?>",
          type: "POST",
          success: function(msg){
              var modal_display = $("#seminarModal").find("#display");
              $.when($(modal_display).html(msg)).done(function(){ 
                 if(obj){
                   var tdcur = $(obj).parent().parent();
                   $(tdcur).attr("iscurrent",1);
                   $(modal_display).find("select[name='sm_type_type_of_scho']").val(tdcur.find("td:eq(0)").text());
                   $(modal_display).find("input[name='sm_gr_agency']").val(tdcur.find("td:eq(1)").text());
                   $(modal_display).find("input[name='sm_prog_study']").val(tdcur.find("td:eq(2)").text());
                   $(modal_display).find("input[name='sm_ins_scho']").val(tdcur.find("td:eq(3)").text());
                   $(modal_display).find("input[name='sm_datef']").val(tdcur.find("td:eq(4)").text());
                   $(modal_display).find("input[name='sm_date_to']").val(tdcur.find("td:eq(5)").text());

                }else{
                   $("#schoinfolist").find("tr").each(function(){
                     $(this).attr("iscurrent",0); 
                   }) 
                }
              });
          }
      });  
    }
// END OF SHOOLARSHIP ---------------------------------
    $(".scs").click(function(){
      addscs($(this));
    });
    $("a[tag='add_scs']").click(function(){
      addscs("");
    });
    function addscs(obj){
      $("#seminarModal").find("h3[tag='title']").text(obj ? "Edit Data" : "Add New");
      $("#button_save_modal").text("Save");    
      $.ajax({
          url: "<?=site_url('employee_/scs')?>",
          type: "POST",
          success: function(msg){
              var modal_display = $("#seminarModal").find("#display");
              $.when($(modal_display).html(msg)).done(function(){ 
                 if(obj){
                   var tdcur = $(obj).parent().parent();
                   $(tdcur).attr("iscurrent",1);
                   $(modal_display).find("option[value='"+tdcur.find("td:eq(0)").text().trim()+"']").attr("selected", "selected").trigger("liszt:updated");
                   $(modal_display).find(":radio[value="+tdcur.find("td:eq(1)").text()+"]").attr("checked", true);
                   $(modal_display).find("input[name='sm_date']").val(tdcur.find("td:eq(2)").text()); 
                   $(modal_display).find("input[name='eb_title']").val(tdcur.find("td:eq(3)").text());
                   $(modal_display).find("option[value="+tdcur.find("td:eq(4)").text()+"]").attr("selected", "selected").trigger("liszt:updated");
                   $(modal_display).find("input[name='eb_address']").val(tdcur.find("td:eq(5)").text());
                   $(modal_display).find("input[name='eb_speaker']").val(tdcur.find("td:eq(6)").text());
                }else{
                   $("#scsinfolist").find("tr").each(function(){
                     $(this).attr("iscurrent",0); 
                   }) 
                }
              });
          }
      });  
    }
// EBD OF SCS ------------------------
    $(".tw").click(function(){
      addtw($(this));
    });
    $("a[tag='add_tw']").click(function(){
      addtw("");
    });
    function addtw(obj){
      $("#seminarModal").find("h3[tag='title']").text(obj ? "Edit Data" : "Add New");
      $("#button_save_modal").text("Save");    
      $.ajax({
          url: "<?=site_url('employee_/tw')?>",
          type: "POST",
          success: function(msg){
              var modal_display = $("#seminarModal").find("#display");
              $.when($(modal_display).html(msg)).done(function(){ 
                 if(obj){
                   var tdcur = $(obj).parent().parent();
                   $(tdcur).attr("iscurrent",1);
                   $(modal_display).find("option[value='"+tdcur.find("td:eq(0)").text().trim()+"']").attr("selected", "selected").trigger("liszt:updated");
                   $(modal_display).find(":radio[value="+tdcur.find("td:eq(1)").text()+"]").attr("checked", true);
                   $(modal_display).find("input[name='sm_date']").val(tdcur.find("td:eq(2)").text()); 
                   $(modal_display).find("input[name='eb_title']").val(tdcur.find("td:eq(3)").text());
                   $(modal_display).find("option[value="+tdcur.find("td:eq(4)").text()+"]").attr("selected", "selected").trigger("liszt:updated");
                   $(modal_display).find("input[name='eb_address']").val(tdcur.find("td:eq(5)").text());
                   $(modal_display).find("input[name='eb_speaker']").val(tdcur.find("td:eq(6)").text());
                }else{
                   $("#twinfolist").find("tr").each(function(){
                     $(this).attr("iscurrent",0); 
                   }) 
                }
              });
          }
      });  
    }
// END OF TW -------------------------------
    $(".resource").click(function(){
      addresource($(this));
    });
    $("a[tag='add_resource']").click(function(){
      addresource("");
    });
    function addresource(obj){
      $("#seminarModal").find("h3[tag='title']").text(obj ? "Edit Data" : "Add New");
      $("#button_save_modal").text("Save");    
      $.ajax({
          url: "<?=site_url('employee_/resource')?>",
          type: "POST",
          success: function(msg){
              var modal_display = $("#seminarModal").find("#display");
              $.when($(modal_display).html(msg)).done(function(){ 
                 if(obj){
                   var tdcur = $(obj).parent().parent();
                   $(tdcur).attr("iscurrent",1);
                   $(modal_display).find("input[name='sm_datef']").val(tdcur.find("td:eq(0)").text()); 
                   $(modal_display).find("select[name='sm_topic']").val(tdcur.find("td:eq(1)").text());
                   $(modal_display).find("input[name='sm_organizer']").val(tdcur.find("td:eq(2)").text());
                   $(modal_display).find("input[name='sm_venue']").val(tdcur.find("td:eq(3)").text());
                }else{
                   $("#resourceinfolist").find("tr").each(function(){
                     $(this).attr("iscurrent",0); 
                   }) 
                }
              });
          }
      });  
    }
// END OF RESOURCE-----------------------------------------------
    $(".org").click(function(){
      addorg($(this));
    });
    $("a[tag='add_org']").click(function(){
      addorg("");
    });
    function addorg(obj){
      $("#seminarModal").find("h3[tag='title']").text(obj ? "Edit Data" : "Add New");
      $("#button_save_modal").text("Save");    
      $.ajax({
          url: "<?=site_url('employee_/org')?>",
          type: "POST",
          success: function(msg){
              var modal_display = $("#seminarModal").find("#display");
              $.when($(modal_display).html(msg)).done(function(){ 
                 if(obj){
                   var tdcur = $(obj).parent().parent();
                   $(tdcur).attr("iscurrent",1);
                   $(modal_display).find("input[name='sm_name_org']").val(tdcur.find("td:eq(0)").text()); 
                   $(modal_display).find("input[name='sm_date']").val(tdcur.find("td:eq(1)").text());
                   $(modal_display).find("input[name='sm_position']").val(tdcur.find("td:eq(2)").text());
                }else{
                   $("#orginfolist").find("tr").each(function(){
                     $(this).attr("iscurrent",0); 
                   }) 
                }
              });
          }
      });  
    }
// END OF ORG ---------------------------------
    $(".community").click(function(){
      addcommunity($(this));
    });
    $("a[tag='add_community']").click(function(){
      addcommunity("");
    });
    function addcommunity(obj){
      $("#seminarModal").find("h3[tag='title']").text(obj ? "Edit Data" : "Add New");
      $("#button_save_modal").text("Save");    
      $.ajax({
          url: "<?=site_url('employee_/community')?>",
          type: "POST",
          success: function(msg){
              var modal_display = $("#seminarModal").find("#display");
              $.when($(modal_display).html(msg)).done(function(){ 
                 if(obj){
                   var tdcur = $(obj).parent().parent();
                   $(tdcur).attr("iscurrent",1);
                   $(modal_display).find("input[name='sm_school']").val(tdcur.find("td:eq(0)").text()); 
                   $(modal_display).find("select[name='sm_educational_level']").val(tdcur.find("td:eq(1)").text());
                   $(modal_display).find("input[name='sm_year_grad']").val(tdcur.find("td:eq(2)").text());
                   $(modal_display).find("input[name='sm_honor']").val(tdcur.find("td:eq(3)").text());
                   $(modal_display).find("input[name='sm_ctype']").val(tdcur.find("td:eq(4)").text());
                }else{
                   $("#communityinfolist").find("tr").each(function(){
                     $(this).attr("iscurrent",0); 
                   }) 
                }
              });
          }
      });  
    }
// END OF COMMUNITY-------------------------
    $(".administrative").click(function(){
      addadministrative($(this));
    });
    $("a[tag='add_administrative']").click(function(){
      addadministrative("");
    });
    function addadministrative(obj){
      $("#seminarModal").find("h3[tag='title']").text(obj ? "Edit Data" : "Add New");
      $("#button_save_modal").text("Save");    
      $.ajax({
          url: "<?=site_url('employee_/administrative')?>",
          type: "POST",
          success: function(msg){
              var modal_display = $("#seminarModal").find("#display");
              $.when($(modal_display).html(msg)).done(function(){ 
                 if(obj){
                   var tdcur = $(obj).parent().parent();
                   $(tdcur).attr("iscurrent",1);
                   $(modal_display).find("input[name='sm_position']").val(tdcur.find("td:eq(0)").text()); 
                   $(modal_display).find("select[name='sm_department']").val(tdcur.find("td:eq(1)").text());
                   $(modal_display).find("input[name='sm_datef']").val(tdcur.find("td:eq(2)").text());
                }else{
                   $("#administrativeinfolist").find("tr").each(function(){
                     $(this).attr("iscurrent",0); 
                   }) 
                }
              });
          }
      });  
    }
// END OF ADMINISTRATIVE ---------------------------
        $.validator.setDefaults({
        debug: true,
        success: "valid",
        ignore: ":hidden:not(.chzn-done)",
        errorPlacement: function(error, element) {
              if (element.hasClass('chosen')) {
                  error.insertAfter(element.next('.chzn-container'));
              }else if(element.hasClass('yesno') || element.hasClass('applicable-field')){
                  error.insertBefore(element.parents("div").eq(0));
              }else{
                  error.insertAfter(element);
              }
          }
        });

        $("#savetrainingseminars,#submitApplication").click(function(){
              var pts = "";
             $("#ptsinfolist").find("tbody tr").each(function(){
               if($(this).find("td").length>1){
                   pts += (pts?"|":"");
                   /*pgd += $(this).find("td:eq(0)").text();*/
                   pts+= $(this).find("td:eq(0)").text();
                   pts+= "~u~";
                   pts+= $(this).find("td:eq(1)").text();
                   pts+= "~u~";
                   pts+= $(this).find("td:eq(2)").text();
                   pts+= "~u~";
                   pts+= $(this).find("td:eq(3)").text();
                   
               }
             });
              /** PGD */
             var pgd = "";
             $("#pgdinfolist").find("tbody tr").each(function(){
               if($(this).find("td").length>1){
                   pgd += (pgd?"|":"");
                   /*pgd += $(this).find("td:eq(0)").text();*/
                   pgd += $(this).find("td:eq(0)").attr('educpgd');
                   pgd += "~u~";
                   pgd += $(this).find("td:eq(1)").text();
                   pgd += "~u~";
                   pgd += $(this).find("td:eq(2)").text();
                   pgd += "~u~";
                   pgd += $(this).find("td:eq(3)").text();
                   pgd += "~u~";
                   pgd += $(this).find("td:eq(4)").text();
               }
             });
             /** Researches */
             var researches = "";
             $("#researchesinfolist").find("tbody tr").each(function(){
               if($(this).find("td").length>1){
                   researches += (researches?"|":"");
                   researches += $(this).find("td:eq(0)").text();
                  
                   researches += "~u~";
                   /*researches += $(this).find("td:eq(1)").text();*/
                   researches += $(this).find("td:eq(1)").attr('educr');
                   researches += "~u~";
                   researches += $(this).find("td:eq(2)").text();
                   researches += "~u~";
                   researches += $(this).find("td:eq(3)").text();
               }
             });
             /** Awards & Recognition */
             var ar = "";
             $("#arinfolist").find("tbody tr").each(function(){
               if($(this).find("td").length>1){
                   ar += (ar?"|":"");
                   ar += $(this).find("td:eq(0)").text();
                   // ar += $(this).find("td:eq(0)").attr('educar');
                   ar += "~u~";
                   ar += $(this).find("td:eq(1)").text();
                   ar += "~u~";
                   ar += $(this).find("td:eq(2)").text();
                   ar += "~u~";
                   ar += $(this).find("td:eq(3)").text();
               }
             });
             
             /** Scholarship */
             var scho = "";
             $("#schoinfolist").find("tbody tr").each(function(){
               if($(this).find("td").length>1){
                   scho += (scho?"|":"");
                   scho += $(this).find("td:eq(0)").text();
                   /*scho += $(this).find("td:eq(0)").attr('educscho');*/
                   scho += "~u~";
                   scho += $(this).find("td:eq(1)").text();
                   scho += "~u~";
                   scho += $(this).find("td:eq(2)").text();
                   scho += "~u~";
                   scho += $(this).find("td:eq(3)").text();
                   scho += "~u~";
                   scho += $(this).find("td:eq(4)").text();
                   scho += "~u~";
                   scho += $(this).find("td:eq(5)").text();
               }
             });
             
             /** Seminar/Conventions/Conferences(Related to field of expertise) */
             var scs = "";
             $("#scsinfolist").find("tbody tr").each(function(){
               if($(this).find("td").length>1){
                   scs += (scs?"|":"");
                   // scs += $(this).find("td:eq(0)").text();
                   // scs += "~u~";
                  /* scs += $(this).find("td:eq(1)").text();*/
                   scs += $(this).find("td:eq(0)").attr('educpi');
                   scs += "~u~";
                   scs += $(this).find("td:eq(1)").text();
                   scs += "~u~";
                   scs += $(this).find("td:eq(2)").text();
                   scs += "~u~";
                   scs += $(this).find("td:eq(3)").text();
                   scs += "~u~";
                   scs += $(this).find("td:eq(4)").text();
                   scs += "~u~";
                   scs += $(this).find("td:eq(5)").text();
                   scs += "~u~";
                   scs += $(this).find("td:eq(6)").text();
               }
             });
             
             /** Trainings/Workshops (Related to field ot expertise) */
             var tw = "";
             $("#twinfolist").find("tbody tr").each(function(){
               if($(this).find("td").length>1){
                   tw += (tw?"|":"");
                   // tw += $(this).find("td:eq(0)").text();
                   // tw += "~u~";
                   tw += $(this).find("td:eq(0)").attr('eductw');
                   /*tw += $(this).find("td:eq(1)").text();*/
                   tw += "~u~";
                   tw += $(this).find("td:eq(1)").text();
                   tw += "~u~";
                   tw += $(this).find("td:eq(2)").text();
                   tw += "~u~";
                   tw += $(this).find("td:eq(3)").text();
                   tw += "~u~";
                   tw += $(this).find("td:eq(4)").text();
                   tw += "~u~";
                   tw += $(this).find("td:eq(5)").text();
                   tw += "~u~";
                   tw += $(this).find("td:eq(6)").text();
               }
             });
             /** Speaking Engagements/Resource Speaker */
             var resource = "";
             $("#resourceinfolist").find("tbody tr").each(function(){
               if($(this).find("td").length>1){
                   resource += (resource?"|":"");
                   // resource += $(this).find("td:eq(0)").text();
                   // resource += "~u~";
                   resource += $(this).find("td:eq(0)").attr('educse');
                  /* resource += $(this).find("td:eq(1)").text();*/
                   resource += "~u~";
                   resource += $(this).find("td:eq(1)").text();
                   resource += "~u~";
                   resource += $(this).find("td:eq(2)").text();
                   resource += "~u~";
                   resource += $(this).find("td:eq(3)").text();
               }
             });
             
             /** Membership or Affiliation in Professional Organization */
             var org = "";
             $("#orginfolist").find("tbody tr").each(function(){
               if($(this).find("td").length>1){
                   org += (org?"|":"");
                   org += $(this).find("td:eq(0)").text();
                   org += "~u~";
                   org += $(this).find("td:eq(1)").attr('educmapo');
                   /*org += $(this).find("td:eq(1)").text();*/
                   org += "~u~";
                   org += $(this).find("td:eq(2)").text();
                   org += "~u~";
                   org += $(this).find("td:eq(3)").text();
                   org += "~u~";
                   org += $(this).find("td:eq(4)").text();
               }
             });
             
             /** Membership in Civic Organizations/Community Involvement */
             var community = "";
             $("#communityinfolist").find("tbody tr").each(function(){
               if($(this).find("td").length>1){
                   community += (community?"|":"");
                   community += $(this).find("td:eq(0)").text();
                   community += "~u~";
                   community += $(this).find("td:eq(1)").text();
                   community += "~u~";
                   community += $(this).find("td:eq(2)").text();
                   community += "~u~";
                   community += $(this).find("td:eq(3)").text();
                   community += "~u~";
                   community += $(this).find("td:eq(4)").text();
                   community += "~u~";
                   community += $(this).find("td:eq(5)").text();
               }
             });

             /** Administrative Functions Handled */
             var administrative = "";
             $("#administrativeinfolist").find("tbody tr").each(function(){
               if($(this).find("td").length>1){
                   administrative += (administrative?"|":"");
                   administrative += $(this).find("td:eq(0)").text();
                   administrative += "~u~";
                   /*administrative += $(this).find("td:eq(1)").text();*/
                   administrative += $(this).find("td:eq(1)").attr('educafh');
                   administrative += "~u~";
                   administrative += $(this).find("td:eq(2)").text();
                   administrative += "~u~";
                   administrative += $(this).find("td:eq(3)").text();
                   administrative += "~u~";
                   administrative += $(this).find("td:eq(4)").text();
                   administrative += "~u~";
                   administrative += $(this).find("td:eq(5)").text();
               }
             });

             var usertype = "<?=$usertype?>";
             var $validator;
             if(usertype == "EMPLOYEE"){
                $validator = $("#seminarstraining").validate({
                     rules: {
                       ptscbox         :{required: {depends: function(element) {return (pts          == "");}}},
                        pgdcbox         :{required: {depends: function(element) {return (pgd          == "");}}},
                        rescbox         :{required: {depends: function(element) {return (researches   == "");}}},
                        awardscbox      :{required: {depends: function(element) {return (ar           == "");}}},
                        schocbox        :{required: {depends: function(element) {return (scho         == "");}}},
                        semcbox         :{required: {depends: function(element) {return (scs          == "");}}},
                        twcbox          :{required: {depends: function(element) {return (tw           == "");}}},
                        resourcecbox    :{required: {depends: function(element) {return (resource     == "");}}},
                        orgcbox         :{required: {depends: function(element) {return (org          == "");}}},
                        cominvolvecbox  :{required: {depends: function(element) {return (community    == "");}}},
                        adminfcbox      :{required: {depends: function(element) {return (administrative == "");}}},
                  
                    }
                });
                $( "input[name=ptscbox]"         ).rules( "add", { required: { depends: function(element) { return (pts           == ""); }}});
                $( "input[name=pgdcbox]"         ).rules( "add", { required: { depends: function(element) { return (pgd           == ""); }}});
                $( "input[name=rescbox]"         ).rules( "add", { required: { depends: function(element) { return (researches    == ""); }}});
                $( "input[name=awardscbox]"      ).rules( "add", { required: { depends: function(element) { return (ar            == ""); }}});
                $( "input[name=schocbox]"        ).rules( "add", { required: { depends: function(element) { return (scho          == ""); }}});
                $( "input[name=semcbox]"         ).rules( "add", { required: { depends: function(element) { return (scs           == ""); }}});
                $( "input[name=twcbox]"          ).rules( "add", { required: { depends: function(element) { return (tw            == ""); }}});
                $( "input[name=resourcecbox]"    ).rules( "add", { required: { depends: function(element) { return (resource      == ""); }}});
                $( "input[name=orgcbox]"         ).rules( "add", { required: { depends: function(element) { return (org           == ""); }}});
                $( "input[name=cominvolvecbox]"  ).rules( "add", { required: { depends: function(element) { return (community     == ""); }}});
                $( "input[name=adminfcbox]"      ).rules( "add", { required: { depends: function(element) { return (administrative == ""); }}});
              }  

              if($("#seminarstraining").valid()){
                  var form_data = $("#seminarstraining").serialize();
                      form_data += "&job=employee/seminar_info";
                      form_data += "&pts="+pts;  
                      form_data += "&pgd="+pgd;  
                      form_data += "&researches="+researches;
                      form_data += "&ar="+ar;
                      form_data += "&scho="+scho;
                      form_data += "&scs="+scs;
                      form_data += "&tw="+tw;
                      form_data += "&resource="+resource;
                      form_data += "&org="+org;
                      form_data += "&community="+community;
                      form_data += "&administrative="+administrative;
                      form_data += "&applicantId="+$('input[name=applicantId]').val();
                      form_data += "&forsubmit="+$(this).attr('forsubmit');
                      // console.log(form_data);
                  $.ajax({
                     url: "<?=site_url("applicant/validateApplicantInfo")?>",
                     data : form_data,
                      dataType: 'JSON',
                     type : "POST",
                     success:function(msg){
                        $("#alertModal").modal('toggle');
                        $("#alertModal").find(".success").show();
                        $("#alertModal").find(".success-title").show();

                        $("#alertModal").find(".failed").hide();
                        $("#alertModal").find(".failed-title").hide();
                        $('input[name=applicantId]').val(msg.applicantId);
                        cancontinue = true;
                        $("#isrequired").hide();
                     }
                  }); 
              }else {
                 $("#alertModal").modal('toggle');
                 $("#alertModal").find(".failed").show();
                 $("#alertModal").find(".failed-title").show();
                 $("#alertModal").find(".success").hide();
                 $("#alertModal").find(".success-title").hide();
                 $validator.focusInvalid();
                 $( "input[name=ptscbox], input[name=pgdcbox], input[name=rescbox], input[name=awardscbox], input[name=schocbox], input[name=semcbox], input[name=twcbox], input[name=resourcecbox], input[name=orgcbox], input[name=cominvolvecbox], input[name=adminfcbox]" ).rules( "remove" );
                 return false;
              }
            
        });


    

        $('.chosen').chosen();


        </script>