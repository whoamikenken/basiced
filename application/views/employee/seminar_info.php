    <style type="text/css">
      #btn_pos{
        float: right;
      }

      .btn-primary{
        margin-right: 10px;
      }
        .tooltip {
  position: relative;
  display: inline-block;
  opacity: 1;
  /*border-bottom: 1px dotted black;*/
}

.tooltip .tooltiptext {
  visibility: hidden;

  background-color: black;
  color: #fff;
  text-align: center;
  border-radius: 6px;
  padding: 5px 0;

  /* Position the tooltip */
  position: absolute;
  z-index: 1;
}
.tooltip .tooltiptext {
  top: -5px;
  left: 105%;
  padding: 5%;
}
.tooltip .tooltiptext::after {
  content: " ";
  position: absolute;
  top: 50%;
  right: 100%; /* To the left of the tooltip */
  margin-top: -5px;
  border-width: 5px;
  border-style: solid;
  border-color: transparent black transparent transparent;
}
.tooltip:hover .tooltiptext {
  visibility: visible;
}

.scrollbar{
   overflow: auto;
   margin-bottom: 10px;
}

  .scrollbar::-webkit-scrollbar {
    width: 10px;
    height: 10px;
  }

  /* Track */
  .scrollbar::-webkit-scrollbar-track {
    box-shadow: inset 0 0 0 grey; 
    border-radius: 10px;
  }
   
  /* Handle */
  .scrollbar::-webkit-scrollbar-thumb {
    background: #0072c6;
    border-radius: 10px;
  }

  /* Handle on hover */
  .scrollbar::-webkit-scrollbar-thumb:hover {
    background: #fadd14; 
  }

  .myInput{
    width: 10% !important;
  }


    </style>


    <?php

    /**
     * @author Justin
     * @copyright 2016
     */

    $CI =& get_instance();
    $CI->load->model('leave_application');

      $usertype = $this->session->userdata("usertype");
      $applicable_field = $ptscbox =$pts_pdp1cbox =$pts_pdp2cbox = $pgdcbox = $rescbox = $awardscbox = $schocbox = $semcbox = $twcbox = $resourcecbox = $orgcbox = $cominvolvecbox = $adminfcbox = "";

     if(isset($empinfo)){
       $empdetails = $empinfo;    
     }else{
       $empinfo = $this->session->userdata("personalinfo"); 
       $empdetails = $empinfo[0];
       $employeeid = $empdetails["employeeid"];
     }
     
     // $seminar = $this->db->query("SELECT * from employee_seminar where employeeid='{$empdetails['employeeid']}'")->result();
     // $pgd = $this->db->query("SELECT publication,title,publisher,datef,type, e.id as id, r.level as level, e.status, e.dra_remarks from employee_pgd e LEFT JOIN reports_item r ON e.publication = r.ID where employeeid='{$empdetails['employeeid']}' ORDER BY datef DESC")->result();
     // $pts = $this->db->query("SELECT b.level AS title, b.level AS venue, a.id as id, a.venue as venue_id, a.title as title_id, a.datef, a.organizer, a.status, a.location, a.dra_remarks FROM employee_pts a LEFT JOIN reports_item b ON b.level = a.venue WHERE employeeid='{$empdetails['employeeid']}' ORDER BY a.datef DESC")->result();
     // $pts_pdp1 = $this->db->query("SELECT DISTINCT b.level AS title, b.level AS venue, a.id as id, a.leave_id, a.is201, a.venue as venue_id, a.title as title_id, a.datef, a.organizer, a.status, a.datet, a.seminar_title, a.location, a.regfee, a.transfee, a.accfee, a.total, a.dra_remarks FROM employee_pts_pdp1 a LEFT JOIN reports_item b ON b.level = a.venue WHERE employeeid='{$empdetails['employeeid']}' ORDER BY a.datef DESC")->result();
     // $pts_pdp2 = $this->db->query("SELECT b.level AS title, b.level AS venue, a.id as id, a.venue as venue_id, a.title as title_id, a.datef, a.organizer, a.status, a.location, a.dra_remarks FROM employee_pts_pdp2 a LEFT JOIN reports_item b ON b.level = a.venue WHERE employeeid='{$empdetails['employeeid']}' ORDER BY a.datef DESC")->result();
     // $pts_pdp3 = $this->db->query("SELECT b.level AS title, b.level AS venue, a.id as id, a.venue as venue_id, a.title as title_id, a.datef, a.organizer, a.status, a.location, a.dra_remarks FROM employee_pts_pdp3 a LEFT JOIN reports_item b ON b.level = a.venue WHERE employeeid='{$empdetails['employeeid']}' ORDER BY a.datef DESC")->result();
     // $researches = $this->db->query("SELECT date_published,educ_level,title,r.level,r.ID as r_id, e.id as id from employee_researches e LEFT JOIN reports_item r ON e.educ_level = r.ID where employeeid='{$empdetails['employeeid']}' ORDER BY date_published")->result();
     
     
     // // echo "<pre>"; print_r($this->db->last_query()); die; 
     // $scho = $this->db->query("SELECT * FROM employee_scholarship WHERE employeeid = '{$empdetails['employeeid']}'")->result();
     // $scs = $this->db->query("SELECT educational_level,school,title,speaker,type,location,address,date_attended,r.level,r.ID from employee_scs e LEFT JOIN reports_item r ON e.educ_level = r.ID where employeeid='{$empdetails['employeeid']}'")->result();
     // $tw = $this->db->query("SELECT educational_level,school,honor,year_graduated,type,location,r.ID,r.level from employee_workshops e LEFT JOIN reports_item r ON e.educ_level = r.ID where employeeid='{$empdetails['employeeid']}'")->result();
    
     
     
    

     $applicable_field   = $this->db->query("SELECT * FROM employee_applicable_fields WHERE employeeid='{$empdetails['employeeid']}'");
    if($applicable_field->num_rows > 0){
      $ptscbox            = $applicable_field->row()->profTraining;
      $pts_pdp1cbox       = $applicable_field->row()->profDevelopment;
      $pts_pdp2cbox       = $applicable_field->row()->profDevelopmentprog;
      $pgdcbox            = $applicable_field->row()->profGrowth;
      $rescbox            = $applicable_field->row()->researches;
      $awardscbox         = $applicable_field->row()->awards;
      $schocbox           = $applicable_field->row()->scholarship;
      $semcbox            = $applicable_field->row()->seminar;
      $twcbox             = $applicable_field->row()->training;
      $resourcecbox       = $applicable_field->row()->speakingEngagement;
      $orgcbox            = $applicable_field->row()->profOrg;
      $cominvolvecbox     = $applicable_field->row()->comInvolvement;
      $adminfcbox         = $applicable_field->row()->adminFunctions;
    }
     
    ?>
    <style>
       {
        font-size: 18px;
      }
    </style>
    <div class="widgets_area animated fadeIn delay-1s">
    <div class="row"><br><br>
    <form id="seminarstraining">  
        <div class="col-md-12">
        <div class="well-content no-search" style="border: 0 !important;">

            <!-- PTS -->
           <div class="panel">
            <div class="panel-heading" style="background-color: #0072c6;"><h4><b>PROFESSIONAL TRAINING AND SEMINARS</b></h4></div>
            <div class="panel-body" id="pts_table" style="background: #fdfdf0;">
                <div>
                    <input type="checkbox" name="ptscbox" id="ptscbox" class="applicable-field" <?= ($ptscbox == "0" ? "checked" : "") ?> >
                    <span style="font-style: italic;color:#e49191">&nbsp;Check this box if Not Applicable</span>
                </div>
                <div class="scrollbar employee_pts_table">
                <table class="table table-hover" id="ptsinfolist">
                   <thead>
                    <tr colspan="5"><h5><b>T/A PTI SPIRITUALITY</b></h5></tr>  
                      <tr width="100%">
                         <th width="10%">Title</th>
                         <th width="10%">Date</th>
                         <th width="10%">Organizer</th>
                         <!-- <th width="10%">Venue</th> -->
                         <th width="10%">Location</th>
                         <th width="10%">Attached File</th>
                         <th width="10%">Data&nbsp;Approval&nbsp;Status</th>
                         <th width="15%">Admin Remarks</th>
                         <th width="15%">&nbsp;</th>
                      </tr>
                   </thead>
                   <tbody id="employee_pts_tbody">     
                     
                   </tbody>
                </table>
              </div>
                <a href="#modal-view" tag="add_pts" data-toggle="modal" class="btn btn-success"><i class="icon glyphicon glyphicon-plus"></i> T/A Pinnacle Spirituality</a>
            </div>
            <!-- ------------------------------pts_pdp1---------------------------------------- -->
             <div class="panel-body" id="pts_pdp1_table" style="background: #fdfdf0;">
                <div>
                    <input type="checkbox" name="pts_pdp1cbox" id="pts_pdp1cbox" class="applicable-field" <?= ($pts_pdp1cbox == "0" ? "checked" : "") ?> >
                    <span style="font-style: italic;color:#e49191">&nbsp;Check this box if Not Applicable</span>
                </div>
                <div class="scrollbar">
                <table class="table table-hover" id="pts_pdp1infolist" width="100%">
                   <thead>
                    <tr colspan="5"><h5><b>PROFESSIONAL DEVELOPMENT PROGRAM</b></h5></tr>  
                      <tr>
                         <!-- <th class="col-md-1" style="display: none;">Title</th> -->
                         <th width="10%">Seminar Title</th>
                         <th width="10%">Location</th>
                         <th width="10%">Date From</th>
                         <th width="10%">Date To</th>
                         <th width="10%">Organizer</th>
                         <!-- <th width="8%">Venue</th>
                         <th width="8%">Registration Fee</th>
                         <th width="8%">Transportation</th>
                         <th width="8%">Accommodation</th>
                         <th width="8%">Total</th> -->
                         <th width="10%">Attached&nbsp;File</th>
                         <th width="10%">Data&nbsp;Approval&nbsp;Status</th>
                         <th width="15%">Admin Remarks</th>
                         <th width="15%">&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;</th>
                      </tr>
                   </thead>
                   <tbody id="employee_pts_pdp1_tbody">

                      
                   </tbody>
                </table>
                </div>
                <a href="#modal-view" tag="add_pts_pdp1" data-toggle="modal" class="btn btn-success"><i class="icon glyphicon glyphicon-plus"></i> Professional Development Program</a>
            </div>
            <!-- -----------------------------------pts_pdp2-------------------------------------------- -->
             <div class="panel-body" id="pts_pdp2_table" style="background: #fdfdf0;">
                <div>
                    <input type="checkbox" name="pts_pdp2cbox" id="pts_pdp2cbox" class="applicable-field" <?= ($pts_pdp2cbox == "0" ? "checked" : "") ?> >
                    <span style="font-style: italic;color:#e49191">&nbsp;Check this box if Not Applicable</span>
                </div>
                <div class="scrollbar">
                <table class="table table-hover" id="pts_pdp2infolist">
                   <thead>
                    <tr colspan="5"><h5><b>PEP DEVELOPMENT PROGRAM</b></h5></tr>  
                      <tr>
                         <th width="15%">Title</th>
                         <th width="10%">Date</th>
                         <th width="10%">Organizer</th>
                         <!-- <th width="10%">Venue</th> -->
                         <th width="10%">Location</th>
                         <th width="15%">Attached File</th>
                         <th width="15%">Data&nbsp;Approval&nbsp;Status</th>
                         <th width="15%">Admin Remarks</th>
                         <th width="15%">&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;</th>
                      </tr>
                   </thead>
                   <tbody id="employee_pts_pdp2_tbody">
                     
                   </tbody>
                </table>
              </div>
                <a href="#modal-view" tag="add_pts_pdp2" data-toggle="modal" class="btn btn-success"><i class="icon glyphicon glyphicon-plus"></i> Pep Development Program</a>
            </div>

            <!-- -----------------------------------pts_pdp3-------------------------------------------- -->
             <div class="panel-body" id="pts_pdp3_table" style="background: #fdfdf0;">
                <div>
                    <input type="checkbox" name="pts_pdp3cbox" id="pts_pdp3cbox" class="applicable-field" <?= ($pts_pdp2cbox == "0" ? "checked" : "") ?> >
                    <span style="font-style: italic;color:#e49191">&nbsp;Check this box if Not Applicable</span>
                </div>
                <div class="scrollbar">
                <table class="table table-hover" id="pts_pdp3infolist">
                   <thead>
                    <tr colspan="5"><h5><b>PSYCHOSOCIAL - CULTURAL</b></h5></tr>  
                      <tr>
                         <th width="15%">Title</th>
                         <th width="10%">Date</th>
                         <th width="15%">Organizer</th>
                         <!-- <th width="20%">Venue</th> -->
                         <!-- <th class="col-md-1">Location</th> -->
                         <th width="10%">Attached File</th>
                         <th width="15%">Data&nbsp;Approval&nbsp;Status</th>
                         <th width="15%">Admin Remarks</th>
                         <th width="15%">&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;</th>
                      </tr>
                   </thead>
                   <tbody id="employee_pts_pdp3_tbody">
                                   
                   </tbody>
                </table>
              </div>
                <a href="#modal-view" tag="add_pts_pdp3" data-toggle="modal" class="btn btn-success"><i class="icon glyphicon glyphicon-plus"></i> Psychosocial - Cultural</a>
            </div>

               <!-- -----------------------------------sem_other-------------------------------------------- -->
             <!-- <div class="panel-body" id="pts_pdp3_table" style="background: #fdfdf0;">
                <div>
                    <input type="checkbox" name="pts_pdp3cbox" id="pts_pdp2cbox" class="applicable-field" <?= ($pts_pdp2cbox == "0" ? "checked" : "") ?> >
                    <span style="font-style: italic;color:#e49191">&nbsp;Check this box if Not Applicable</span>
                </div>
                <table class="table table-hover" id="pts_pdp3infolist">
                   <thead>
                    <tr colspan="5"><h5><b>OTHER</b></h5></tr>  
                      <tr>
                         <th class="col-md-3">Title</th>
                         <th class="col-md-3">Date</th>
                         <th class="col-md-2">Organizer</th>
                         <th class="col-md-2">Venue</th>
                         <th class="col-md-2">&nbsp;</th>
                      </tr>
                   </thead>
                   <tbody>
    <?
                   if(count($seminar)>0){

                        foreach($seminar as $sem){
    ?>
                        <tr id="<?= $sem->id ?>" table="employee_seminar">
                            <td><?=$sem->title?></td>
                            <td><?=$sem->date?></td>
                            <td><?=$sem->resource_speaker?></td>
                            <td relvenue="<?=$sem->place?>"><?=$sem->place?></td>
                        </tr>    
    <?                            
                        }
                   }else{
    ?>
                        <tr>
                            <td colspan="6">No existing data</td>
                        </tr>
    <?                    
                   }
    ?>                        
                   </tbody>
                </table>
                <a href="#modal-view" tag="add_seminar" data-toggle="modal" class="btn btn-success" style="display: none;"><i class="icon glyphicon glyphicon-plus"></i> Add Training & Seminars</a>
            </div> -->

          </div>
          <div class="panel">
            <div class="panel-heading" style="background-color: #0072c6;"><h4><b>PUBLICATION</b></h4></div>
            <div class="panel-body" id="pgd_table" style="background: #fdfdf0;">
                <div>
                    <input type="checkbox" name="pgdcbox" id="pgdcbox" class="applicable-field" <?= ($pgdcbox == "0" ? "checked" : "") ?> >
                    <span style="font-style: italic;color:#e49191">&nbsp;Check this box if Not Applicable</span>
                </div>
                <div class="scrollbar">
                <table class="table table-hover" id="pgdinfolist">
                   <thead>
                      <tr>
                         <th width="10%">Type of Publication</th>
                         <th width="10%">Title</th>
                         <th width="10%">Publisher</th>
                         <th width="10%">Date Published</th>
                         <th width="10%">Type of Authorship</th>
                         <th width="10%">Attached File</th>
                         <th width="10%">Data&nbsp;Approval&nbsp;Status</th>
                         <th width="15%">Admin Remarks</th>
                         <th width="15%">&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;</th>
                      </tr>
                   </thead>
                   <tbody id="employee_pgd_tbody">
                             
                   </tbody>
                </table>
              </div>
                <a href="#modal-view" tag="add_pgd" data-toggle="modal" class="btn btn-success"><i class="icon glyphicon glyphicon-plus"></i> Add Publication</a>
            </div>
          </div>
            
            <!-- Researches -->
          
            
            <!-- AWARDS & RECOGNITION -->
          <div class="panel">
            <div class="panel-heading" style="background-color: #0072c6;"><h4><b>AWARDS & RECOGNITION</b></h4></div>
            <div class="panel-body" id="ar_table" style="background: #fdfdf0;">
                <div>
                    <input type="checkbox" name="awardscbox" id="awardscbox" class="applicable-field" <?= ($awardscbox == "0" ? "checked" : "") ?> >
                    <span style="font-style: italic;color:#e49191">&nbsp;Check this box if Not Applicable</span>
                </div>
                <div class="scrollbar">
                <table class="table table-hover" id="arinfolist">
                   <thead>
                      <tr>
                         <th width="10%">Type of Award</th>
                         <th width="10%">Granting Agency / Org</th>
                         <th width="10%">Place</th>
                         <th width="10%">Date Given</th>
                         <th width="15%">Attached File</th>
                         <th width="15%">Data&nbsp;Approval&nbsp;Status</th>
                         <th width="15%">Admin Remarks</th>
                         <th width="15%" >&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;</th>
                      </tr>
                   </thead>
                   <tbody id="employee_awardsrecog_tbody">
                   
                   </tbody>
                </table>
              </div>
                <a href="#modal-view" tag="add_ar" data-toggle="modal" class="btn btn-success"><i class="icon glyphicon glyphicon-plus"></i> Add Awards &amp; Recognition</a>
            </div>
          </div>
            
             <!-- Scholarship -->
          <div class="panel">
            <div class="panel-heading" style="background-color: #0072c6;"><h4><b>SCHOLARSHIP</b></h4></div>
            <div class="panel-body" id="scho_table" style="background: #fdfdf0;">
                <div>
                    <input type="checkbox" name="schocbox" id="schocbox" class="applicable-field" <?= ($schocbox == "0" ? "checked" : "") ?> >
                    <span style="font-style: italic;color:#e49191">&nbsp;Check this box if Not Applicable</span>
                </div>
                <div class="scrollbar">
                <table class="table table-hover" id="schoinfolist">
                   <thead>
                      <tr>
                         <!-- <th rowspan="2" class="align_center">Type of Publication</th> -->
                         <th rowspan="2">Type of Scholarship</th>
                         <th rowspan="2">Granting Agency</th>
                         <th rowspan="2">Program of Study</th>
                         <th rowspan="2">Institution</th>
                         <th colspan="2" style="text-align: center;border-bottom: 1px solid white;">Inclusive&nbsp;Dates</th>
                         <th colspan="1" style="border-bottom: 1px solid white;">&nbsp;</th>
                         <th colspan="1" style="border-bottom: 1px solid white;">&nbsp;</th>
                         <th width="15%" style="border-bottom: 1px solid white;">&nbsp;</th>
                      </tr>
                      <tr>
                        <th style="text-align: center;">From</th>
                        <th style="text-align: center;">To</th>
                        <th>Attached File</th>
                        <th>Data&nbsp;Approval&nbsp;Status</th>
                        <th>Admin Remarks</th>
                        <th>&nbsp;</th>
                      </tr>
                   </thead>
                   <tbody id="employee_scholarship_tbody">

                        
                   </tbody>
                </table>
              </div>
                <a href="#modal-view" tag="add_scho" data-toggle="modal" class="btn btn-success"><i class="icon glyphicon glyphicon-plus"></i> Add New</a>
            </div>
          </div>
            
            <!-- Professional Involvements -->
          <div class="panel">
            <div class="panel-heading" style="background-color: #0072c6;"><h4><b>PROFESSIONAL INVOLVEMENTS</b></h4></div>
            <div class="panel-body" id="resource_table" style="background: #fdfdf0;">
                <div>
                    <input type="checkbox" name="resourcecbox" id="resourcecbox" class="applicable-field" <?= ($resourcecbox == "0" ? "checked" : "") ?> >
                    <span style="font-style: italic;color:#e49191">&nbsp;Check this box if Not Applicable</span>
                </div>
                <!-- SEMINAR AND TRAINING -->

                <!-- --------------------------- -->
                <div class="scrollbar">
                <table class="table table-hover" id="resourceinfolist">
                   <thead>
                      <tr colspan="5"><h5><b>Speaking Engagements/Resource Speaker</b></h5></tr>  
                      <tr>
                         <th>Date</th>
                         <th>Topic</th>
                         <th>Organizer</th>
                         <th>Venue</th>
                         <th>Attached File</th>
                         <th>Data&nbsp;Approval&nbsp;Status</th>
                         <th>Admin Remarks</th>
                         <th width="15%">&nbsp;</th>
                      </tr>
                   </thead>
                   <tbody id="employee_resource_tbody">
                       
                   </tbody>
                </table>
              </div>
                <a href="#modal-view" tag="add_resource" data-toggle="modal" class="btn btn-success"><i class="icon glyphicon glyphicon-plus"></i> Add New</a>
                <hr />
                </div>
                <div class="panel-body" id="org_table" style="background: #fdfdf0;">
                <div>
                    <input type="checkbox" name="orgcbox" id="orgcbox" class="applicable-field" <?= ($orgcbox == "0" ? "checked" : "") ?> >
                    <span style="font-style: italic;color:#e49191">&nbsp;Check this box if Not Applicable</span>
                </div>
                <div class="scrollbar">
                <table class="table table-hover" id="orginfolist">
                   <thead>
                      <tr colspan="5"><h5><b>Membership in Civic Organization</b></h5></tr>  
                      <tr>
                         <th>Name of Organization</th>
                         <th>Date</th>
                         <th>Position</th>
                         <th>Attached File</th>
                         <th>Data&nbsp;Approval&nbsp;Status</th>
                         <th>Admin Remarks</th>
                         <th class="col-md-2">&nbsp;</th>
                      </tr>
                   </thead>
                   <tbody id="employee_proorg_tbody">
                     
                   </tbody>
                </table>
              </div>
                <a href="#modal-view" tag="add_org" data-toggle="modal" class="btn btn-success"><i class="icon glyphicon glyphicon-plus"></i> Add New</a>
                <hr />
              </div>
              <div class="panel-body" id="community_table" style="background: #fdfdf0;">
                <div>
                    <input type="checkbox" name="cominvolvecbox" id="cominvolvecbox" class="applicable-field" <?= ($cominvolvecbox == "0" ? "checked" : "") ?> >
                    <span style="font-style: italic;color:#e49191">&nbsp;Check this box if Not Applicable</span>
                </div>
                <div class="scrollbar">
                <table class="table table-hover" id="communityinfolist">
                   <thead>
                      <tr colspan="5"><h5><b>Community Involvement</b></h5></tr>  
                      <tr>
                         <th>Name of Organization</th>
                         <!--<th>Education Level</th>-->
                         <th>Date</th>
                         <th>Nature of Involvement</th>
                          <th>Attached File</th>
                         <th>Data&nbsp;Approval&nbsp;Status</th>
                         <th>Admin Remarks</th>
                         <th class="col-md-2">&nbsp;</th>
                      </tr>
                   </thead>
                   <tbody id="employee_community_tbody">
        
                   </tbody>
                </table>
              </div>
                <a href="#modal-view" tag="add_community" data-toggle="modal" class="btn btn-success"><i class="icon glyphicon glyphicon-plus"></i> Add New</a>
                <hr />
              </div>
              <div class="panel-body" id="administrative_table" style="background: #fdfdf0;">
                <div>
                    <input type="checkbox" name="adminfcbox" id="adminfcbox" class="applicable-field" <?= ($adminfcbox == "0" ? "checked" : "") ?> >
                    <span style="font-style: italic;color:#e49191">&nbsp;Check this box if Not Applicable</span>
                </div>
                <div class="scrollbar">
                <table class="table table-hover" id="administrativeinfolist">
                   <thead>
                      <tr colspan="5"><h5><b>Positions Held in Poveda</b></h5></tr>  
                      <tr>
                         <th>Position</th>
                         <th>Department</th>
                         <th>Inclusive Date</th>
                         <th hidden>Attached File</th>
                         <th>Data&nbsp;Approval&nbsp;Status</th>
                         <th>Admin Remarks</th>
                         <th class="col-md-2">&nbsp;</th>
                      </tr>
                   </thead>
                   <tbody id="employee_administrative_tbody">
                     
                   </tbody>
                </table>
              </div>
                <a href="#modal-view" tag="add_administrative" data-toggle="modal" class="btn btn-success"><i class="icon glyphicon glyphicon-plus"></i> Add New</a>
              </div>
                <hr />
            </div>
          </div>

        </div>    
        </div>
    </form>
    </div>
    </div>

<!--     <div class="modal fade" id="success_modal3" role="dialog">
        <div class="modal-dialog modal-sm" style="top: 35%;">
            <div class="modal-content">
                <div class="modal-body" style="margin-bottom: 0px;">
                    <p style="color:green;font-weight: bold;">Your information has been saved.</p>
                </div>
            </div>
        </div>
    </div> -->

    <script>

      $(document).ready(function(){
        tableData('employee_resource_tbody');
        tableData('employee_pts_pdp1_tbody');
        tableData('employee_pts_pdp2_tbody');
        tableData('employee_pts_pdp3_tbody');
        tableData('employee_pgd_tbody');
        tableData('employee_awardsrecog_tbody');
        tableData('employee_scholarship_tbody');
        tableData('employee_proorg_tbody');
        tableData('employee_community_tbody');
        tableData('employee_administrative_tbody');

        loadTable('employee_pts_table'); 

        if($("#ptscbox").prop('checked')){
          $("#pts_table a").attr('disabled', true);
          $("#pts_table a").css('pointer-events', 'none');
        }
        else{
          $("#pts_table a").attr('disabled', false);
          $("#pts_table a").css('pointer-events', '');
        }

        if($("#pts_pdp1cbox").prop('checked')){
          $("#pts_pdp1_table a").attr('disabled', true);
          $("#pts_pdp1_table a").css('pointer-events', 'none');
        }
        else{
          $("#pts_pdp1_table a").attr('disabled', false);
          $("#pts_pdp1_table a").css('pointer-events', '');
        }

        if($("#pts_pdp2cbox").prop('checked')){
          $("#pts_pdp2_table a").attr('disabled', true);
          $("#pts_pdp2_table a").css('pointer-events', 'none');
        }
        else{
          $("#pts_pdp2_table a").attr('disabled', false);
          $("#pts_pdp2_table a").css('pointer-events', '');
        }

        if($("#pts_pdp3cbox").prop('checked')){
          $("#pts_pdp3_table a").attr('disabled', true);
          $("#pts_pdp3_table a").css('pointer-events', 'none');
        }
        else{
          $("#pts_pdp3_table a").attr('disabled', false);
          $("#pts_pdp3_table a").css('pointer-events', '');
        }

        if($("#pgdcbox").prop('checked')){
          $("#pgd_table a").attr('disabled', true);
          $("#pgd_table a").css('pointer-events', 'none');
        }
        else{
          $("#pgd_table a").attr('disabled', false);
          $("#pgd_table a").css('pointer-events', '');
        }

        if($("#adminfcbox").prop('checked')){
          $("#administrative_table a").attr('disabled', true);
          $("#administrative_table a").css('pointer-events', 'none');
        }
        else{
          $("#administrative_table a").attr('disabled', false);
          $("#administrative_table a").css('pointer-events', '');
        }

        if($("#cominvolvecbox").prop('checked')){
          $("#community_table a").attr('disabled', true);
          $("#community_table a").css('pointer-events', 'none');
        }
        else{
          $("#community_table a").attr('disabled', false);
          $("#community_table a").css('pointer-events', '');
        }

        if($("#orgcbox").prop('checked')){
          $("#org_table a").attr('disabled', true);
          $("#org_table a").css('pointer-events', 'none');
        }
        else{
          $("#org_table a").attr('disabled', false);
          $("#org_table a").css('pointer-events', '');
        }

        if($("#resourcecbox").prop('checked')){
          $("#resource_table a").attr('disabled', true);
          $("#resource_table a").css('pointer-events', 'none');
        }
        else{
          $("#resource_table a").attr('disabled', false);
          $("#resource_table a").css('pointer-events', '');
        }

        if($("#schocbox").prop('checked')){
          $("#scho_table a").attr('disabled', true);
          $("#scho_table a").css('pointer-events', 'none');
        }
        else{
          $("#scho_table a").attr('disabled', false);
          $("#scho_table a").css('pointer-events', '');
        }

        if($("#awardscbox").prop('checked')){
          $("#ar_table a").attr('disabled', true);
          $("#ar_table a").css('pointer-events', 'none');
        }
        else{
          $("#ar_table a").attr('disabled', false);
          $("#ar_table a").css('pointer-events', '');
        }

      });
      $(".update_status").click(function(){
    var current_status = $(this).text();
    // if(current_status == "APPROVED"){
    //     alert("This information is already APPROVED!");
    //     return;
    // }
    var table = $(this).closest("tr").attr("table");
    var id = $(this).closest("tr").attr("id");
    var status = updateTableStatus(table, id);
    // $(this).text(status)
    $(this).html(status + " <span class='tooltiptext tooltiptext_"+id+"_"+table+"'>"+status+"</span>")
    if(status == 'APPROVED') $(this).removeClass("btn-danger").addClass("btn-success");
    else $(this).removeClass("btn-success").addClass("btn-danger");
});
      function updateTableStatus(table, id){
    var approverid = $("#approverid").val();
    var status = "";
    $.ajax({
        url: "<?= site_url('employee_/updateTableStatus') ?>",
        type:"POST",
        data: {table: table, id: id, approverid:approverid},
        async: false,
        success:function(response){
          status = response;
        }
    });

    return status;
}

    $(".delete_entry").click(function(){
        $(this).parent().parent().remove();
    });

    $("#ptscbox, #pts_pdp1cbox, #pts_pdp2cbox,#pts_pdp3cbox, #pgdcbox, #adminfcbox, #cominvolvecbox, #orgcbox, #resourcecbox, #schocbox, #awardscbox").click(function(){
        if($("#ptscbox").prop('checked')){ 
          var cb1 = 0;
          $("#pts_table a").attr('disabled', true);
          $("#pts_table a").css('pointer-events', 'none');
        }
        else{
          var cb1 = 1;
          $("#pts_table a").attr('disabled', false);
          $("#pts_table a").css('pointer-events', '');
        }

        if($("#pts_pdp1cbox").prop('checked')){
          var cb2 = 0;
          $("#pts_pdp1_table a").attr('disabled', true);
          $("#pts_pdp1_table a").css('pointer-events', 'none');
        }
        else{
          var cb2 = 1;
          $("#pts_pdp1_table a").attr('disabled', false);
          $("#pts_pdp1_table a").css('pointer-events', '');
        }

        if($("#pts_pdp2cbox").prop('checked')){
          var cb3 = 0;
          $("#pts_pdp2_table a").attr('disabled', true);
          $("#pts_pdp2_table a").css('pointer-events', 'none');
        }
        else{
          var cb3 = 1;
          $("#pts_pdp2_table a").attr('disabled', false);
          $("#pts_pdp2_table a").css('pointer-events', '');
        }

        if($("#pts_pdp3cbox").prop('checked')){
          var cb3 = 0;
          $("#pts_pdp3_table a").attr('disabled', true);
          $("#pts_pdp3_table a").css('pointer-events', 'none');
        }
        else{
          var cb3 = 1;
          $("#pts_pdp3_table a").attr('disabled', false);
          $("#pts_pdp3_table a").css('pointer-events', '');
        }

        if($("#pgdcbox").prop('checked')){
          var cb4 = 0;
          $("#pgd_table a").attr('disabled', true);
          $("#pgd_table a").css('pointer-events', 'none');
        }
        else{
          var cb4 = 1;
          $("#pgd_table a").attr('disabled', false);
          $("#pgd_table a").css('pointer-events', '');
        }

        if($("#adminfcbox").prop('checked')){
          var cb5 = 0;
          $("#administrative_table a").attr('disabled', true);
          $("#administrative_table a").css('pointer-events', 'none');
        }
        else{
          var cb5 = 1;
          $("#administrative_table a").attr('disabled', false);
          $("#administrative_table a").css('pointer-events', '');
        }

        if($("#cominvolvecbox").prop('checked')){
          var cb6 = 0;
          $("#community_table a").attr('disabled', true);
          $("#community_table a").css('pointer-events', 'none');
        }
        else{
          var cb6 = 1;
          $("#community_table a").attr('disabled', false);
          $("#community_table a").css('pointer-events', '');
        }

        if($("#orgcbox").prop('checked')){
          var cb7 = 0;
          $("#org_table a").attr('disabled', true);
          $("#org_table a").css('pointer-events', 'none');
        }
        else{
          var cb7 = 1;
          $("#org_table a").attr('disabled', false);
          $("#org_table a").css('pointer-events', '');
        }

        if($("#resourcecbox").prop('checked')){
          var cb8 = 0;
          $("#resource_table a").attr('disabled', true);
          $("#resource_table a").css('pointer-events', 'none');
        }
        else{
          var cb8 = 1;
          $("#resource_table a").attr('disabled', false);
          $("#resource_table a").css('pointer-events', '');
        }

        if($("#schocbox").prop('checked')){
          var cb9 = 0;
          $("#scho_table a").attr('disabled', true);
          $("#scho_table a").css('pointer-events', 'none');
        }
        else{
          var cb9 = 1;
          $("#scho_table a").attr('disabled', false);
          $("#scho_table a").css('pointer-events', '');
        }

        if($("#awardscbox").prop('checked')){
          var cb10 = 0;
          $("#ar_table a").attr('disabled', true);
          $("#ar_table a").css('pointer-events', 'none');
        }
        else{
          var cb10 = 1;
          $("#ar_table a").attr('disabled', false);
          $("#ar_table a").css('pointer-events', '');
        }

        $.ajax({
            url: "<?= site_url('applicant/checkbox') ?>",
            type: "POST",
            data: {
              employeeid : $("input[name='employeeid']").val(),
              profTraining : cb1,
              profDevelopment : cb2,
              profDevelopmentprog: cb3,
              profGrowth : cb4,
              adminFunctions : cb5,
              comInvolvement : cb6,
              profOrg : cb7,
              speakingEngagement : cb8,
              scholarship : cb9,
              awards : cb10
            },
            dataType: "json",
            success:function(response){
            }
         });
    });
    // -------------------------PTS START------------------------------------------------

    // $(".pts").click(function(){
    //     addpts($(this));
    // });

    $("a[tag='add_pts']").click(function(){
        addpts("");
    });

    
    

    // $(".update_status").click(function(){
    //     var current_status = $(this).text();
    //     if(current_status == "APPROVED"){
    //         alert("This information is already APPROVED!");
    //         return;
    //     }
    //     var table = $(this).closest("tr").attr("table");
    //     var id = $(this).closest("tr").attr("id");
    //     var status = updateTableStatus(table, id);
    //     $(this).text(status);
    //     $(this).removeClass("btn-danger");
    //     $(this).addClass("btn-success");
    // });

    function loadSuccessModal(msg){
        // if(!$('#success_modal').is(':visible')){
        //     $("#success_modal").modal("show");
        //     setTimeout(function(){ $("#success_modal").modal("hide"); }, 800);
        // }

        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: msg,
            showConfirmButton: true,
            timer: 1000
        })
    }

    // function updateTableStatus(table, id){
    //     var status = "";
    //     $.ajax({
    //         url: "<?= site_url('employee_/updateTableStatus') ?>",
    //         type:"POST",
    //         data: {table: table, id: id},
    //         async: false,
    //         success:function(response){
    //             if(response) status = "APPROVED";
    //             else status = "PENDING";
    //         }
    //     });

    //     return status;
    // }

    function addpts(obj, tbl_id=""){
        $("#modal-view").find("h3[tag='title']").text(obj ? ucwords("T/A PTI SPIRITUALITY") : ucwords("T/A PTI SPIRITUALITY"));
        $("#button_save_modal").text("Save");    
        $.ajax({
            url: "<?=site_url('employee_/pts')?>",
            type: "POST",
            success: function(msg){
                var modal_display = $("#modal-view").find("div[tag='display']");
                $.when($(modal_display).html(msg)).done(function(){ 
                   if(obj){
                     var tdcur = $(obj).parent().parent().parent();
                     $(tdcur).attr("iscurrent",1);
                     $(modal_display).find("select[name='sm_title']").val(tdcur.find("td:eq(0)").attr("reltitle")).trigger('chosen:updated');
                     $(modal_display).find("input[name='sm_other_title']").val(tdcur.find("td:eq(0)").attr("other_title"));
                     $(modal_display).find("input[name='sm_datef']").val(tdcur.find("td:eq(1)").text());
                     $(modal_display).find("input[name='sm_organizer']").val(tdcur.find("td:eq(2)").text());
                     // $(modal_display).find("select[name='sm_venue']").val(tdcur.find("td:eq(3)").attr("relvenue")).trigger('chosen:updated');
                     $(modal_display).find("input[name='sm_location']").val(tdcur.find("td:eq(3)").text());
                     // console.log(tdcur.find("td:eq(5)").find("a").text());
                     if("<?=$usertype?>" == "EMPLOYEE" && tdcur.find("td:eq(5)").find("a").text().includes("APPROVED")){
                        $(modal_display).find("select[name='sm_title']").parent().parent().css("display", "none");
                        $(modal_display).find("input[name='sm_datef']").parent().parent().parent().css("display", "none");
                        $(modal_display).find("input[name='sm_organizer']").parent().parent().css("display", "none");
                        $(modal_display).find("input[name='sm_location']").parent().parent().css("display", "none");
                        $(modal_display).find("#file_uploaded").parent().parent().css("margin-top", "20px");
                     }
                     $(modal_display).find("#file_uploaded").attr("file", tdcur.find("td:eq(4)").find("a").attr("content")).attr("mime", tdcur.find("td:eq(4)").find("a").attr("mime"));
                     $(modal_display).find("input[name='tbl_id']").val(tbl_id);
                     if("<?=$usertype?>" == "ADMIN"){
                    $(modal_display).find("input[name='dra_remarks']").val(tdcur.find("td:eq(6)").text());
                    $(modal_display).find("#draremarks").css("display", "block");
                 }
              }else{
                if("<?=$usertype?>" == "ADMIN"){
                    $(modal_display).find("#draremarks").css("display", "block");
                 }
                     $("#ptsinfolist").find("tr").each(function(){
                       $(this).attr("iscurrent",0); 
                     }) 
                  }
                  $(".modalclose").click(function(){
                        $("#ptsinfolist").find("tr").each(function(){
                        $(this).attr("iscurrent",0);
                });
            });
                });
            }
        });  
    }

function deletePTS(obj, tbl_id = ""){
    var table = "";
    var userid = "";
    if($("input[name='applicantId']").val()){
        table = "applicant_pts";
        userid = $("input[name='applicantId']").val();
    }
    else{
        table = "employee_pts"; 
        userid = $("input[name='employeeid']").val();
    }
    $.ajax({
        url: $("#site_url").val() + "/employee_/deleteData",
        type: "POST",
        data: {table: GibberishAES.enc(table , toks), tbl_id: GibberishAES.enc( tbl_id, toks), employeeid:  GibberishAES.enc(userid , toks), toks:toks},
        dataType: "JSON",
        success: function(msg){  
          loadTable('employee_pts_table'); 
         Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: 'Successfully deleted data!',
              showConfirmButton: true,
              timer: 1000
          }) 
        }
    });  
}
    // -------------------------PTS_PDP1 START------------------------------------------------

    $("a[tag='add_pts_pdp1']").click(function(){
        addpts_pdp1("");
    });


    

   

    function addpts_pdp1(obj, tbl_id=""){
        $("#modal-view").find("h3[tag='title']").text(obj ? ucwords("PROFESSIONAL DEVELOPMENT PROGRAM") : ucwords("PROFESSIONAL DEVELOPMENT PROGRAM"));
        $("#button_save_modal").text("Save");    
        $.ajax({
            url: "<?=site_url('employee_/pts_pdp1')?>",
            type: "POST",
            success: function(msg){
                var modal_display = $("#modal-view").find("div[tag='display']");
                $.when($(modal_display).html(msg)).done(function(){ 
                   if(obj){
                     var tdcur = $(obj).parent().parent().parent();
                     $(tdcur).attr("iscurrent",1);
                     // $(modal_display).find("select[name='sm_title']").val(tdcur.find("td:eq(0)").attr("reltitle")).trigger('chosen:updated');
                     $(modal_display).find("input[name='sm_semtitle']").val(tdcur.find("td:eq(0)").text());
                     $(modal_display).find("input[name='sm_location']").val(tdcur.find("td:eq(1)").text());
                     $(modal_display).find("input[name='sm_datef']").val(tdcur.find("td:eq(2)").text());
                     $(modal_display).find("input[name='sm_datet']").val(tdcur.find("td:eq(3)").text());
                     $(modal_display).find("input[name='sm_organizer']").val(tdcur.find("td:eq(4)").text());
                     // $(modal_display).find("select[name='sm_venue']").val(tdcur.find("td:eq(5)").attr("relvenue"));
                     // $(modal_display).find("input[name='sm_registration']").val(tdcur.find("td:eq(6)").text());
                     // $(modal_display).find("input[name='sm_transportation']").val(tdcur.find("td:eq(7)").text());
                     // $(modal_display).find("input[name='sm_accommodation']").val(tdcur.find("td:eq(8)").text());
                     // $(modal_display).find("input[name='sm_total']").val(tdcur.find("td:eq(9)").text());
                     if("<?=$usertype?>" == "EMPLOYEE" && tdcur.find("td:eq(6)").find("a").text().includes("APPROVED")){
                        $(modal_display).find("input[name='sm_semtitle']").parent().parent().css("display", "none");
                        $(modal_display).find("input[name='sm_location']").parent().parent().css("display", "none");
                        $(modal_display).find("input[name='sm_datef']").parent().parent().parent().css("display", "none");
                        $(modal_display).find("input[name='sm_datet']").parent().parent().parent().css("display", "none");
                        $(modal_display).find("input[name='sm_organizer']").parent().parent().css("display", "none");
                        $(modal_display).find("#file_uploaded").parent().parent().css("margin-top", "20px");
                     }
                     $(modal_display).find("#file_uploaded").attr("file", tdcur.find("td:eq(5)").find("a").attr("content")).attr("mime", tdcur.find("td:eq(5)").find("a").attr("mime"));
                     $(modal_display).find("input[name='tbl_id']").val(tbl_id);
                     if("<?=$usertype?>" == "ADMIN"){
                    $(modal_display).find("input[name='dra_remarks']").val(tdcur.find("td:eq(7)").text());
                    $(modal_display).find("#draremarks").css("display", "block");
                 }
              }else{
                if("<?=$usertype?>" == "ADMIN"){
                    $(modal_display).find("#draremarks").css("display", "block");
                 }
                     $("#pts_pdp1infolist").find("tr").each(function(){
                       $(this).attr("iscurrent",0); 
                     }) 
                  }
                  $(".modalclose").click(function(){
                        $("#pts_pdp1infolist").find("tr").each(function(){
                        $(this).attr("iscurrent",0);
                });
            });
                });
            }
        });  
    }

function deletepts_pdp1(obj, tbl_id = ""){
    var table = "";
    var userid = "";
    if($("input[name='applicantId']").val()){
        table = "applicant_pts_pdp1";
        userid = $("input[name='applicantId']").val();
    }
    else{
        table = "employee_pts_pdp1"; 
        userid = $("input[name='employeeid']").val();
    }
    $.ajax({
        url: $("#site_url").val() + "/employee_/deleteData",
        type: "POST",
        data: {table: GibberishAES.enc(table , toks), tbl_id: GibberishAES.enc( tbl_id, toks), employeeid:  GibberishAES.enc(userid , toks), toks:toks},
        dataType: "JSON",
        success: function(msg){   
          tableData('employee_pts_pdp1_tbody');
          Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: 'Successfully deleted data!',
              showConfirmButton: true,
              timer: 1000
          }) 
        }
    });  
}

// -------------------------PTS_PDP2 START------------------------------------------------
    $("a[tag='add_pts_pdp3']").click(function(){
        addpts_pdp3("");
    });

    function addpts_pdp3(obj, tbl_id=""){
        $("#modal-view").find("h3[tag='title']").text(obj ? ucwords("PSYCHOSOCIAL - CULTURAL") : ucwords("PSYCHOSOCIAL - CULTURAL"));
        $("#button_save_modal").text("Save");    
        $.ajax({
            url: "<?=site_url('employee_/pts_pdp3')?>",
            type: "POST",
            success: function(msg){
                var modal_display = $("#modal-view").find("div[tag='display']");
                $.when($(modal_display).html(msg)).done(function(){ 
                   if(obj){
                     var tdcur = $(obj).parent().parent().parent();
                     $(tdcur).attr("iscurrent",1);
                     $(modal_display).find("select[name='sm_title']").val(tdcur.find("td:eq(0)").attr("reltitle")).trigger('chosen:updated');
                     $(modal_display).find("input[name='sm_other_title']").val(tdcur.find("td:eq(0)").attr("other_title"));
                     $(modal_display).find("input[name='sm_datef']").val(tdcur.find("td:eq(1)").text());
                     $(modal_display).find("input[name='sm_organizer']").val(tdcur.find("td:eq(2)").text());
                     // $(modal_display).find("select[name='sm_venue']").val(tdcur.find("td:eq(3)").attr("relvenue")).trigger('chosen:updated');
                     // $(modal_display).find("input[name='sm_location']").val(tdcur.find("td:eq(4)").text());
                     if("<?=$usertype?>" == "EMPLOYEE" && tdcur.find("td:eq(4)").find("a").text().includes("APPROVED")){
                        $(modal_display).find("select[name='sm_title']").parent().parent().css("display", "none");
                        $(modal_display).find("input[name='sm_datef']").parent().parent().parent().css("display", "none");
                        $(modal_display).find("input[name='sm_organizer']").parent().parent().css("display", "none");
                        $(modal_display).find("#file_uploaded").parent().parent().css("margin-top", "20px");
                     }
                     $(modal_display).find("#file_uploaded").attr("file", tdcur.find("td:eq(3)").find("a").attr("content")).attr("mime", tdcur.find("td:eq(3)").find("a").attr("mime"));
                     $(modal_display).find("input[name='tbl_id']").val(tbl_id);
                     if("<?=$usertype?>" == "ADMIN"){
                    $(modal_display).find("input[name='dra_remarks']").val(tdcur.find("td:eq(5)").text());
                    $(modal_display).find("#draremarks").css("display", "block");
                 }
              }else{
                if("<?=$usertype?>" == "ADMIN"){
                    $(modal_display).find("#draremarks").css("display", "block");
                 }
                     $("#pts_pdp3infolist").find("tr").each(function(){
                       $(this).attr("iscurrent",0); 
                     }) 
                  }
                  $(".modalclose").click(function(){
                        $("#pts_pdp3infolist").find("tr").each(function(){
                        $(this).attr("iscurrent",0);
                });
            });
                });
            }
        });  
    }

    function deletepts_pdp3(obj, tbl_id = ""){
        var table = "";
        var userid = "";
        if($("input[name='applicantId']").val()){
            table = "applicant_pts_pdp3";
            userid = $("input[name='applicantId']").val();
        }
        else{
            table = "employee_pts_pdp3"; 
            userid = $("input[name='employeeid']").val();
        }
        $.ajax({
            url: $("#site_url").val() + "/employee_/deleteData",
            type: "POST",
            data: {table: GibberishAES.enc(table , toks), tbl_id: GibberishAES.enc( tbl_id, toks), employeeid:  GibberishAES.enc(userid , toks), toks:toks},
            dataType: "JSON",
            success: function(msg){   
              tableData('employee_pts_pdp3_tbody');
              Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: 'Successfully deleted data!',
              showConfirmButton: true,
              timer: 1000
          }) 
            }
        });  
    }

    // -------------------------PTS_PDP2 START------------------------------------------------

    $("a[tag='add_pts_pdp2']").click(function(){
        addpts_pdp2("");
    });

    function addpts_pdp2(obj, tbl_id=""){
        $("#modal-view").find("h3[tag='title']").text(obj ? ucwords("PEP DEVELOPMENT PROGRAM") : ucwords("PEP DEVELOPMENT PROGRAM"));
        $("#button_save_modal").text("Save");    
        $.ajax({
            url: "<?=site_url('employee_/pts_pdp2')?>",
            type: "POST",
            success: function(msg){
                var modal_display = $("#modal-view").find("div[tag='display']");
                $.when($(modal_display).html(msg)).done(function(){ 
                   if(obj){
                     var tdcur = $(obj).parent().parent().parent();
                     $(tdcur).attr("iscurrent",1);
                     $(modal_display).find("select[name='sm_title']").val(tdcur.find("td:eq(0)").attr("reltitle")).trigger('chosen:updated');
                     $(modal_display).find("input[name='sm_other_title']").val(tdcur.find("td:eq(0)").attr("other_title"));
                     $(modal_display).find("input[name='sm_datef']").val(tdcur.find("td:eq(1)").text());
                     $(modal_display).find("input[name='sm_organizer']").val(tdcur.find("td:eq(2)").text());
                     // $(modal_display).find("select[name='sm_venue']").val(tdcur.find("td:eq(3)").attr("relvenue")).trigger('chosen:updated');
                     $(modal_display).find("input[name='sm_location']").val(tdcur.find("td:eq(3)").text());
                     if("<?=$usertype?>" == "EMPLOYEE" && tdcur.find("td:eq(5)").find("a").text().includes("APPROVED")){
                        $(modal_display).find("select[name='sm_title']").parent().parent().css("display", "none");
                        $(modal_display).find("input[name='sm_datef']").parent().parent().parent().css("display", "none");
                        $(modal_display).find("input[name='sm_organizer']").parent().parent().css("display", "none");
                        $(modal_display).find("input[name='sm_location']").parent().parent().css("display", "none");
                        $(modal_display).find("#file_uploaded").parent().parent().css("margin-top", "20px");
                     }
                     $(modal_display).find("#file_uploaded").attr("file", tdcur.find("td:eq(4)").find("a").attr("content")).attr("mime", tdcur.find("td:eq(4)").find("a").attr("mime"));
                     $(modal_display).find("input[name='tbl_id']").val(tbl_id);
                     if("<?=$usertype?>" == "ADMIN"){
                    $(modal_display).find("input[name='dra_remarks']").val(tdcur.find("td:eq(6)").text());
                    $(modal_display).find("#draremarks").css("display", "block");
                 }
              }else{
                if("<?=$usertype?>" == "ADMIN"){
                    $(modal_display).find("#draremarks").css("display", "block");
                 }
                     $("#pts_pdp2infolist").find("tr").each(function(){
                       $(this).attr("iscurrent",0); 
                     }) 
                  }
                  $(".modalclose").click(function(){
                        $("#pts_pdp2infolist").find("tr").each(function(){
                        $(this).attr("iscurrent",0);
                });
            });
                });
            }
        });  
    }

function ucwords(str){
  // str.toLowerCase();
  str = str.toLowerCase().replace(/\b[a-z]/g, function(letter) {
    return letter.toUpperCase();
  });
  return(str);
}

function deletepts_pdp2(obj, tbl_id = ""){
    var table = "";
    var userid = "";
    if($("input[name='applicantId']").val()){
        table = "applicant_pts_pdp2";
        userid = $("input[name='applicantId']").val();
    }
    else{
        table = "employee_pts_pdp2"; 
        userid = $("input[name='employeeid']").val();
    }
    $.ajax({
        url: $("#site_url").val() + "/employee_/deleteData",
        type: "POST",
        data: {table: GibberishAES.enc(table , toks), tbl_id: GibberishAES.enc( tbl_id, toks), employeeid:  GibberishAES.enc(userid , toks), toks:toks},
        dataType: "JSON",
        success: function(msg){   
          tableData('employee_pts_pdp2_tbody');
          Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: 'Successfully deleted data!',
              showConfirmButton: true,
              timer: 1000
          }) 
        }
    });  
}
    // -------------------------PGD START------------------------------------------------

    $("a[tag='add_pgd']").click(function(){
        addPGD("");
    });

   
    function addPGD(obj, tbl_id=""){
        $("#modal-view").find("h3[tag='title']").text(obj ? "Edit Publication" : "Add Publication");
        $("#button_save_modal").text("Save");    
        $.ajax({
            url: "<?=site_url('employee_/pgd')?>",
            type: "POST",
            success: function(msg){
                var modal_display = $("#modal-view").find("div[tag='display']");
                $.when($(modal_display).html(msg)).done(function(){ 
                   if(obj){
                     var tdcur = $(obj).parent().parent().parent();
                     $(tdcur).attr("iscurrent",1);
                     $(modal_display).find("select[name='sm_publication']").val(tdcur.find("td:eq(0)").attr("relpublication")).trigger('chosen:updated');
                     $(modal_display).find("input[name='sm_title']").val(tdcur.find("td:eq(1)").text());
                     $(modal_display).find("input[name='sm_publisher']").val(tdcur.find("td:eq(2)").text());
                     $(modal_display).find("input[name='sm_type']").val(tdcur.find("td:eq(4)").text());
                     $(modal_display).find("input[name='sm_date']").val(tdcur.find("td:eq(3)").text());
                     if("<?=$usertype?>" == "EMPLOYEE" && tdcur.find("td:eq(6)").find("a").text().includes("APPROVED")){
                        $(modal_display).find("select[name='sm_publication']").parent().parent().css("display", "none");
                        $(modal_display).find("input[name='sm_date']").parent().parent().parent().css("display", "none");
                        $(modal_display).find("input[name='sm_title']").parent().parent().css("display", "none");
                        $(modal_display).find("input[name='sm_publisher']").parent().parent().css("display", "none");
                        $(modal_display).find("input[name='sm_type']").parent().parent().css("display", "none");
                        $(modal_display).find("#file_uploaded").parent().parent().css("margin-top", "20px");
                     }
                     $(modal_display).find("#file_uploaded").attr("file", tdcur.find("td:eq(5)").find("a").attr("content")).attr("mime", tdcur.find("td:eq(5)").find("a").attr("mime"));
                     
                     $(modal_display).find("input[name='tbl_id']").val(tbl_id);
                     if("<?=$usertype?>" == "ADMIN"){
                    $(modal_display).find("input[name='dra_remarks']").val(tdcur.find("td:eq(7)").text());
                    $(modal_display).find("#draremarks").css("display", "block");
                 }
              }else{
                if("<?=$usertype?>" == "ADMIN"){
                    $(modal_display).find("#draremarks").css("display", "block");
                 }
                     $("#pgdinfolist").find("tr").each(function(){
                       $(this).attr("iscurrent",0); 
                     }) 
                  }
                  $(".modalclose").click(function(){
                        $("#pgdinfolist").find("tr").each(function(){
                        $(this).attr("iscurrent",0);
                });
            });
                });
            }
        });  
    }

function deletePGD(obj, tbl_id = ""){
    var table = "";
    var userid = "";
    if($("input[name='applicantId']").val()){
        table = "applicant_pgd";
        userid = $("input[name='applicantId']").val();
    }
    else{
        table = "employee_pgd"; 
        userid = $("input[name='employeeid']").val();
    }
    $.ajax({
        url: $("#site_url").val() + "/employee_/deleteData",
        type: "POST",
        data: {table: GibberishAES.enc(table , toks), tbl_id: GibberishAES.enc( tbl_id, toks), employeeid:  GibberishAES.enc(userid , toks), toks:toks},
        dataType: "JSON",
        success: function(msg){  
          tableData('employee_pgd_tbody');
        Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: 'Successfully deleted data!',
              showConfirmButton: true,
              timer: 1000
          })  
        }
    });  
}
// -------------------------------------------------AWARDS---------------------------------------------

    // $(".researches").click(function(){
    //     addresearches($(this));
    // });
    // $("a[tag='add_researches']").click(function(){
    //     addresearches("");
    // });
    // function addresearches(obj){
    //     $("#modal-view").find("h3[tag='title']").text(obj ? "Edit Awards & Recognition" : "Add Awards & Recognition");
    //     $("#button_save_modal").text("Save");    
    //     $.ajax({
    //         url: "<?=site_url('employee_/researches')?>",
    //         type: "POST",
    //         success: function(msg){
    //             var modal_display = $("#modal-view").find("div[tag='display']");
    //             $.when($(modal_display).html(msg)).done(function(){ 
    //                if(obj){
    //                  var tdcur = $(obj).parent().parent();
    //                  $(tdcur).attr("iscurrent",1);
    //                  $(modal_display).find("input[name='eb_date']").val(tdcur.find("td:eq(0)").text()); 
    //                  $(modal_display).find("select[name='eb_level']").val(tdcur.find("td:eq(1)").text());
    //                  $(modal_display).find("input[name='eb_yeargrad']").val(tdcur.find("td:eq(2)").text());
    //                  $(modal_display).find("input[name='eb_honor']").val(tdcur.find("td:eq(3)").text());
    //               }else{
    //                  $("#researchesinfolist").find("tr").each(function(){
    //                    $(this).attr("iscurrent",0); 
    //                  }) 
    //               }
    //             });
    //         }
    //     });  
    // }
    
    $("a[tag='add_ar']").click(function(){
        addAward("");
    });

    

    function addAward(obj, tbl_id=""){
        $("#modal-view").find("h3[tag='title']").text(obj ? "Edit Awards & Recognition" : "Add Awards & Recognition");
        $("#button_save_modal").text("Save");    
        $.ajax({
            url: "<?=site_url('employee_/ar')?>",
            type: "POST",
            success: function(msg){
                var modal_display = $("#modal-view").find("div[tag='display']");
                $.when($(modal_display).html(msg)).done(function(){ 
                   if(obj){
                     var tdcur = $(obj).parent().parent().parent();
                     $(tdcur).attr("iscurrent",1);
                     // $(modal_display).find("select[name='ar_award']").val(tdcur.find("td:eq(0)").attr("relaward")).trigger('chosen:updated');
                     $(modal_display).find("input[name='ar_award']").val(tdcur.find("td:eq(0)").text());
                     $(modal_display).find("input[name='ar_instituition']").val(tdcur.find("td:eq(1)").text());
                     $(modal_display).find("input[name='ar_address']").val(tdcur.find("td:eq(2)").text());
                     $(modal_display).find("input[name='ar_datef']").val(tdcur.find("td:eq(3)").text());
                     $(modal_display).find("#file_uploaded").attr("file", tdcur.find("td:eq(4)").find("a").attr("content")).attr("mime", tdcur.find("td:eq(4)").find("a").attr("mime"));
                     if("<?=$usertype?>" == "EMPLOYEE" && tdcur.find("td:eq(5)").find("a").text().includes("APPROVED")){
                        $(modal_display).find("input[name='ar_datef']").parent().parent().parent().css("display", "none");
                        $(modal_display).find("input[name='ar_award']").parent().parent().css("display", "none");
                        $(modal_display).find("input[name='ar_address']").parent().parent().css("display", "none");
                        $(modal_display).find("input[name='ar_instituition']").parent().parent().css("display", "none");
                        $(modal_display).find("#file_uploaded").parent().parent().css("margin-top", "20px");
                     }
                     $(modal_display).find("input[name='tbl_id']").val(tbl_id);
                     if("<?=$usertype?>" == "ADMIN"){
                    $(modal_display).find("input[name='dra_remarks']").val(tdcur.find("td:eq(6)").text());
                    $(modal_display).find("#draremarks").css("display", "block");
                 }
              }else{
                if("<?=$usertype?>" == "ADMIN"){
                    $(modal_display).find("#draremarks").css("display", "block");
                 }
                     $("#arinfolist").find("tr").each(function(){
                       $(this).attr("iscurrent",0); 
                     }) 
                  }
                  $(".modalclose").click(function(){
                        $("#arinfolist").find("tr").each(function(){
                        $(this).attr("iscurrent",0);
                });
            });
                });
            }
        });  
    }

function deleteAWARD(obj, tbl_id = ""){
    var table = "";
    var userid = "";
    if($("input[name='applicantId']").val()){
        table = "applicant_awardsrecog";
        userid = $("input[name='applicantId']").val();
    }
    else{
        table = "employee_awardsrecog"; 
        userid = $("input[name='employeeid']").val();
    }
    $.ajax({
        url: $("#site_url").val() + "/employee_/deleteData",
        type: "POST",
        data: {table: GibberishAES.enc(table , toks), tbl_id: GibberishAES.enc( tbl_id, toks), employeeid:  GibberishAES.enc(userid , toks), toks:toks},
        dataType: "JSON",
        success: function(msg){ 
        tableData('employee_awardsrecog_tbody');  
          Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: 'Successfully deleted data!',
              showConfirmButton: true,
              timer: 1000
          }) 
        }
    });  
}
// --------------------------------------------Scholarship--------------------------------------------
    $("a[tag='add_scho']").click(function(){
        addScho("");
    });
    

    function addScho(obj, tbl_id=""){
        $("#modal-view").find("h3[tag='title']").text(obj ? "Edit Scholarship" : "Add Scholarship");
        $("#button_save_modal").text("Save");    
        $.ajax({
            url: "<?=site_url('employee_/scho')?>",
            type: "POST",
            success: function(msg){
                var modal_display = $("#modal-view").find("div[tag='display']");
                $.when($(modal_display).html(msg)).done(function(){ 
                   if(obj){
                     var tdcur = $(obj).parent().parent().parent();
                     $(tdcur).attr("iscurrent",1);
                     // $(modal_display).find("select[name='sm_type_of_scho']").val(tdcur.find("td:eq(0)").attr("relscho")).trigger('chosen:updated');
                     $(modal_display).find("input[name='sm_type_of_scho']").val(tdcur.find("td:eq(0)").text());
                     $(modal_display).find("input[name='sm_gr_agency']").val(tdcur.find("td:eq(1)").text());
                     $(modal_display).find("input[name='sm_prog_study']").val(tdcur.find("td:eq(2)").text());
                     $(modal_display).find("input[name='sm_ins_scho']").val(tdcur.find("td:eq(3)").text());
                     $(modal_display).find("input[name='sm_datef']").val(tdcur.find("td:eq(4)").text());
                     $(modal_display).find("input[name='sm_datef_to']").val(tdcur.find("td:eq(5)").text());
                     if("<?=$usertype?>" == "EMPLOYEE" && tdcur.find("td:eq(7)").find("a").text().includes("APPROVED")){
                        $(modal_display).find("input[name='sm_datef']").parent().parent().parent().parent().css("display", "none");
                        $(modal_display).find("input[name='sm_ins_scho']").parent().parent().css("display", "none");
                        $(modal_display).find("input[name='sm_prog_study']").parent().parent().css("display", "none");
                        $(modal_display).find("input[name='sm_gr_agency']").parent().parent().css("display", "none");
                        $(modal_display).find("input[name='sm_type_of_scho']").parent().parent().css("display", "none");
                        $(modal_display).find("#file_uploaded").parent().parent().css("margin-top", "20px");
                     }
                     $(modal_display).find("#file_uploaded").attr("file", tdcur.find("td:eq(6)").find("a").attr("content")).attr("mime", tdcur.find("td:eq(6)").find("a").attr("mime"));
                     $(modal_display).find("input[name='tbl_id']").val(tbl_id);
                     if("<?=$usertype?>" == "ADMIN"){
                    $(modal_display).find("input[name='dra_remarks']").val(tdcur.find("td:eq(8)").text());
                    $(modal_display).find("#draremarks").css("display", "block");
                 }
              }else{
                if("<?=$usertype?>" == "ADMIN"){
                    $(modal_display).find("#draremarks").css("display", "block");
                 }
                     $("#schoinfolist").find("tr").each(function(){
                       $(this).attr("iscurrent",0); 
                     }) 
                  }
                  $(".modalclose").click(function(){
                        $("#schoinfolist").find("tr").each(function(){
                        $(this).attr("iscurrent",0);
                });
            });
                });
            }
        });  
    }

function deleteScho(obj, tbl_id = ""){
    var table = "";
    var userid = "";
    if($("input[name='applicantId']").val()){
        table = "applicant_scholarship";
        userid = $("input[name='applicantId']").val();
    }
    else{
        table = "employee_scholarship"; 
        userid = $("input[name='employeeid']").val();
    }
    $.ajax({
        url: $("#site_url").val() + "/employee_/deleteData",
        type: "POST",
        data: {table: GibberishAES.enc(table , toks), tbl_id: GibberishAES.enc( tbl_id, toks), employeeid:  GibberishAES.enc(userid , toks), toks:toks},
        dataType: "JSON",
        success: function(msg){   
          tableData('employee_scholarship_tbody');
          Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: 'Successfully deleted data!',
              showConfirmButton: true,
              timer: 1000
          }) 
        }
    });  
}


    $(".scs").click(function(){
        addscs($(this));
    });
    $("a[tag='add_scs']").click(function(){
        addscs("");
    });
    function addscs(obj){
        $("#modal-view").find("h3[tag='title']").text(obj ? "Edit Data" : "Add New");
        $("#button_save_modal").text("Save");    
        $.ajax({
            url: "<?=site_url('employee_/scs')?>",
            type: "POST",
            success: function(msg){
                var modal_display = $("#modal-view").find("div[tag='display']");
                $.when($(modal_display).html(msg)).done(function(){ 
                   if(obj){
                     var tdcur = $(obj).parent().parent();
                     $(tdcur).attr("iscurrent",1);
                     // $(modal_display).find("input[name='eb_school']").val(tdcur.find("td:eq(0)").text()); 
                     // $(modal_display).find(":radio[value="+tdcur.find("td:eq(6)").text()+"]").attr("checked", true);
                     // $(modal_display).find("select[name='sccLocation']").val(tdcur.find("td:eq(3)").text());
                     $(modal_display).find("option[value="+tdcur.find("td:eq(3)").text()+"]").attr("selected", "selected").trigger("liszt:updated");
                     $(modal_display).find("input[name='eb_honor']").val(tdcur.find("td:eq(2)").text());
                     $(modal_display).find("input[name='eb_speaker']").val(tdcur.find("td:eq(5)").text());
                     $(modal_display).find("input[name='eb_address']").val(tdcur.find("td:eq(4)").text());
                     $(modal_display).find("input[name='sm_date']").val(tdcur.find("td:eq(1)").text());
                    $(modal_display).find("option[value="+tdcur.find("td:eq(0)").text()+"]").attr("selected", "selected").trigger("liszt:updated");
                     
                  }else{
                     $("#scsinfolist").find("tr").each(function(){
                       $(this).attr("iscurrent",0); 
                     }) 
                  }
                });
            }
        });  
    }

    $(".tw").click(function(){
        addtw($(this));
    });
    $("a[tag='add_tw']").click(function(){
        addtw("");
    });
    function addtw(obj){
        $("#modal-view").find("h3[tag='title']").text(obj ? "Edit Data" : "Add New");
        $("#button_save_modal").text("Save");    
        $.ajax({
            url: "<?=site_url('employee_/tw')?>",
            type: "POST",
            success: function(msg){
                var modal_display = $("#modal-view").find("div[tag='display']");
                $.when($(modal_display).html(msg)).done(function(){ 
                   if(obj){
                     var tdcur = $(obj).parent().parent();
                     $(tdcur).attr("iscurrent",1);
                     $(modal_display).find("input[name='eb_school']").val(tdcur.find("td:eq(0)").text()); 
                     // $(modal_display).find("select[name='eb_level']").val(tdcur.find("td:eq(1)").text());
                     // $(modal_display).find("option[value="+tdcur.find("td:eq(1)").text()+"]").attr("selected", "selected").trigger("liszt:updated");
                     $(modal_display).find("input[name='eb_yeargrad']").val(tdcur.find("td:eq(2)").text());
                     $(modal_display).find("input[name='eb_honor']").val(tdcur.find("td:eq(3)").text());
                     $(modal_display).find(":radio[value="+tdcur.find("td:eq(4)").text()+"]").attr("checked", true);
                     $(modal_display).find("option[value="+tdcur.find("td:eq(5)").text()+"]").attr("selected", "selected").trigger("liszt:updated");
                  }else{
                     $("#twinfolist").find("tr").each(function(){
                       $(this).attr("iscurrent",0); 
                     }) 
                  }
                });
            }
        });  
    }

    $("a[tag='add_resource']").click(function(){
        addResource("");
    });
   
    function addResource(obj, tbl_id=""){
        $("#modal-view").find("h3[tag='title']").text(obj ? "Edit Speaking Engagement" : "Add Speaking Engagement");
        $("#button_save_modal").text("Save");    
        $.ajax({
            url: "<?=site_url('employee_/resource')?>",
            type: "POST",
            success: function(msg){
                var modal_display = $("#modal-view").find("div[tag='display']");
                $.when($(modal_display).html(msg)).done(function(){ 
                   if(obj){
                     var tdcur = $(obj).parent().parent().parent();
                     $(tdcur).attr("iscurrent",1);
                     $(modal_display).find("input[name='sm_datef']").val(tdcur.find("td:eq(0)").text());
                     $(modal_display).find("input[name='sm_topic']").val(tdcur.find("td:eq(1)").text());
                     $(modal_display).find("input[name='sm_organizer']").val(tdcur.find("td:eq(2)").text());
                     $(modal_display).find("input[name='sm_venue']").val(tdcur.find("td:eq(3)").text());
                     if("<?=$usertype?>" == "EMPLOYEE" && tdcur.find("td:eq(5)").find("a").text().includes("APPROVED")){
                        $(modal_display).find("input[name='sm_datef']").parent().parent().parent().css("display", "none");
                        $(modal_display).find("input[name='sm_topic']").parent().parent().css("display", "none");
                        $(modal_display).find("input[name='sm_organizer']").parent().parent().css("display", "none");
                        $(modal_display).find("input[name='sm_venue']").parent().parent().css("display", "none");
                        $(modal_display).find("#file_uploaded").parent().parent().css("margin-top", "20px");
                     }
                     $(modal_display).find("#file_uploaded").attr("file", tdcur.find("td:eq(4)").find("a").attr("content")).attr("mime", tdcur.find("td:eq(4)").find("a").attr("mime"));
                     $(modal_display).find("input[name='tbl_id']").val(tbl_id);
                     if("<?=$usertype?>" == "ADMIN"){
                    $(modal_display).find("input[name='dra_remarks']").val(tdcur.find("td:eq(6)").text());
                    $(modal_display).find("#draremarks").css("display", "block");
                 }
              }else{
                if("<?=$usertype?>" == "ADMIN"){
                    $(modal_display).find("#draremarks").css("display", "block");
                 }
                     $("#resourceinfolist").find("tr").each(function(){
                       $(this).attr("iscurrent",0); 
                     }) 
                  }
                  $(".modalclose").click(function(){
                        $("#resourceinfolist").find("tr").each(function(){
                        $(this).attr("iscurrent",0);
                });
            });
                });
            }
        });  
    }

function deleteResource(obj, tbl_id = ""){
    var table = "";
    var userid = "";
    if($("input[name='applicantId']").val()){
        table = "applicant_resource";
        userid = $("input[name='applicantId']").val();
    }
    else{
        table = "employee_resource"; 
        userid = $("input[name='employeeid']").val();
    }
    $.ajax({
        url: $("#site_url").val() + "/employee_/deleteData",
        type: "POST",
        data: {table: GibberishAES.enc(table , toks), tbl_id: GibberishAES.enc( tbl_id, toks), employeeid:  GibberishAES.enc(userid , toks), toks:toks},
        dataType: "JSON",
        success: function(msg){  
        tableData('employee_resource_tbody'); 
          Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: 'Successfully deleted data!',
              showConfirmButton: true,
              timer: 1000
          }) 
        }
    });  
}


    $("a[tag='add_org']").click(function(){
        addOrg("");
    });
    

    function addOrg(obj, tbl_id=""){
        $("#modal-view").find("h3[tag='title']").text(obj ? "Edit Application and Civic Organization" : "Add Application and Civic Organization");
        $("#button_save_modal").text("Save");    
        $.ajax({
            url: "<?=site_url('employee_/org')?>",
            type: "POST",
            success: function(msg){
                var modal_display = $("#modal-view").find("div[tag='display']");
                $.when($(modal_display).html(msg)).done(function(){ 
                   if(obj){
                     var tdcur = $(obj).parent().parent().parent();
                     $(tdcur).attr("iscurrent",1);
                     $(modal_display).find("input[name='sm_name_org']").val(tdcur.find("td:eq(0)").text());
                     $(modal_display).find("input[name='sm_date']").val(tdcur.find("td:eq(1)").text());
                     $(modal_display).find("input[name='sm_position']").val(tdcur.find("td:eq(2)").text());
                     if("<?=$usertype?>" == "EMPLOYEE" && tdcur.find("td:eq(4)").find("a").text().includes("APPROVED")){
                        $(modal_display).find("input[name='sm_date']").parent().parent().parent().css("display", "none");
                        $(modal_display).find("input[name='sm_name_org']").parent().parent().css("display", "none");
                        $(modal_display).find("input[name='sm_position']").parent().parent().css("display", "none");
                        $(modal_display).find("#file_uploaded").parent().parent().css("margin-top", "20px");
                     }
                     $(modal_display).find("#file_uploaded").attr("file", tdcur.find("td:eq(3)").find("a").attr("content")).attr("mime", tdcur.find("td:eq(3)").find("a").attr("mime"));
                     $(modal_display).find("input[name='tbl_id']").val(tbl_id);
                     if("<?=$usertype?>" == "ADMIN"){
                    $(modal_display).find("input[name='dra_remarks']").val(tdcur.find("td:eq(5)").text());
                    $(modal_display).find("#draremarks").css("display", "block");
                 }
              }else{
                if("<?=$usertype?>" == "ADMIN"){
                    $(modal_display).find("#draremarks").css("display", "block");
                 }
                     $("#orginfolist").find("tr").each(function(){
                       $(this).attr("iscurrent",0); 
                     }) 
                  }
                  $(".modalclose").click(function(){
                        $("#orginfolist").find("tr").each(function(){
                        $(this).attr("iscurrent",0);
                });
            });
                });
            }
        });  
    }

function deleteOrg(obj, tbl_id = ""){
    var table = "";
    var userid = "";
    if($("input[name='applicantId']").val()){
        table = "applicant_proorg";
        userid = $("input[name='applicantId']").val();
    }
    else{
        table = "employee_proorg"; 
        userid = $("input[name='employeeid']").val();
    }
    $.ajax({
        url: $("#site_url").val() + "/employee_/deleteData",
        type: "POST",
        data: {table: GibberishAES.enc(table , toks), tbl_id: GibberishAES.enc( tbl_id, toks), employeeid:  GibberishAES.enc(userid , toks), toks:toks},
        dataType: "JSON",
        success: function(msg){   
          tableData('employee_proorg_tbody');
          Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: 'Successfully deleted data!',
              showConfirmButton: true,
              timer: 1000
          }) 
        }
    });  
}

    $("a[tag='add_community']").click(function(){
        addCommunity("");
    });
    

    function addCommunity(obj, tbl_id=""){
        $("#modal-view").find("h3[tag='title']").text(obj ? "Edit Community Involvement" : "Add Community Involvement");
        $("#button_save_modal").text("Save");    
        $.ajax({
            url: "<?=site_url('employee_/community')?>",
            type: "POST",
            success: function(msg){
                var modal_display = $("#modal-view").find("div[tag='display']");
                $.when($(modal_display).html(msg)).done(function(){ 
                   if(obj){
                     var tdcur = $(obj).parent().parent().parent();
                     $(tdcur).attr("iscurrent",1);
                     $(modal_display).find("input[name='sm_school']").val(tdcur.find("td:eq(0)").text());
                     // $(modal_display).find("select[name='sm_educational_level']").val(tdcur.find("td:eq(1)").attr("releduclevel")).trigger('chosen:updated');
                     $(modal_display).find("input[name='sm_year_grad']").val(tdcur.find("td:eq(1)").text());
                     $(modal_display).find("input[name='sm_honor']").val(tdcur.find("td:eq(2)").text());
                     if("<?=$usertype?>" == "EMPLOYEE" && tdcur.find("td:eq(4)").find("a").text().includes("APPROVED")){
                        $(modal_display).find("input[name='sm_school']").parent().parent().css("display", "none");
                        $(modal_display).find("input[name='sm_year_grad']").parent().parent().parent().css("display", "none");
                        $(modal_display).find("input[name='sm_honor']").parent().parent().css("display", "none");
                        $(modal_display).find("#file_uploaded").parent().parent().css("margin-top", "20px");
                     }
                     $(modal_display).find("#file_uploaded").attr("file", tdcur.find("td:eq(3)").find("a").attr("content")).attr("mime", tdcur.find("td:eq(3)").find("a").attr("mime"));
                     // $(modal_display).find("input[name='sm_ctype']").val(tdcur.find("td:eq(4)").text());
                     $(modal_display).find("input[name='tbl_id']").val(tbl_id);
                     if("<?=$usertype?>" == "ADMIN"){
                    $(modal_display).find("input[name='dra_remarks']").val(tdcur.find("td:eq(5)").text());
                    $(modal_display).find("#draremarks").css("display", "block");
                 }
              }else{
                if("<?=$usertype?>" == "ADMIN"){
                    $(modal_display).find("#draremarks").css("display", "block");
                 }
                     $("#communityinfolist").find("tr").each(function(){
                       $(this).attr("iscurrent",0); 
                     }) 
                  }
                  $(".modalclose").click(function(){
                        $("#communityinfolist").find("tr").each(function(){
                        $(this).attr("iscurrent",0);
                });
            });
                });
            }
        });  
    }

function deleteCommunity(obj, tbl_id = ""){
    var table = "";
    var userid = "";
    if($("input[name='applicantId']").val()){
        table = "applicant_community";
        userid = $("input[name='applicantId']").val();
    }
    else{
        table = "employee_community"; 
        userid = $("input[name='employeeid']").val();
    }
    $.ajax({
        url: $("#site_url").val() + "/employee_/deleteData",
        type: "POST",
        data: {table: GibberishAES.enc(table , toks), tbl_id: GibberishAES.enc( tbl_id, toks), employeeid:  GibberishAES.enc(userid , toks), toks:toks},
        dataType: "JSON",
        success: function(msg){   
          tableData('employee_community_tbody');
          Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: 'Successfully deleted data!',
              showConfirmButton: true,
              timer: 1000
          }) 
        }
    });  
}

    $("a[tag='add_administrative']").click(function(){
        addAdministrative("");
    });
  

    function addAdministrative(obj, tbl_id=""){
        $("#modal-view").find("h3[tag='title']").text(obj ? "Edit Position Held in Poveda" : "Add Position Held in Poveda");
        $("#button_save_modal").text("Save");    
        $.ajax({
            url: "<?=site_url('employee_/administrative')?>",
            type: "POST",
            success: function(msg){
                var modal_display = $("#modal-view").find("div[tag='display']");
                $.when($(modal_display).html(msg)).done(function(){ 
                   if(obj){
                     var tdcur = $(obj).parent().parent().parent();
                     $(tdcur).attr("iscurrent",1);
                     $(modal_display).find("input[name='sm_positionf']").val(tdcur.find("td:eq(0)").text());
                     $(modal_display).find("input[name='sm_department']").val(tdcur.find("td:eq(1)").text());
                     $(modal_display).find("input[name='sm_datef']").val(tdcur.find("td:eq(2)").text());
                     if("<?=$usertype?>" == "EMPLOYEE" && tdcur.find("td:eq(3)").find("a").text().includes("APPROVED")){
                        $(modal_display).find("input[name='sm_positionf']").parent().parent().css("display", "none");
                        $(modal_display).find("input[name='sm_department']").parent().parent().css("display", "none");
                        $(modal_display).find("input[name='sm_datef']").parent().parent().parent().css("display", "none");
                        // $(modal_display).find("#file_uploaded").parent().parent().css("margin-top", "20px");
                     }
                     // $(modal_display).find("#file_uploaded").attr("file", tdcur.find("td:eq(3)").find("a").attr("content")).attr("mime", tdcur.find("td:eq(3)").find("a").attr("mime"));
                     $(modal_display).find("input[name='tbl_id']").val(tbl_id);
                     if("<?=$usertype?>" == "ADMIN"){
                    $(modal_display).find("input[name='dra_remarks']").val(tdcur.find("td:eq(4)").text());
                    $(modal_display).find("#draremarks").css("display", "block");
                 }
              }else{
                if("<?=$usertype?>" == "ADMIN"){
                    $(modal_display).find("#draremarks").css("display", "block");
                 }
                     $("#administrativeinfolist").find("tr").each(function(){
                       $(this).attr("iscurrent",0); 
                     }) 
                  }
                  $(".administrativeinfolist").click(function(){
                        $("#orginfolist").find("tr").each(function(){
                        $(this).attr("iscurrent",0);
                });
            });
                });
            }
        });  
    }

function deleteAdministrative(obj, tbl_id = ""){
    var table = "";
    var userid = "";
    if($("input[name='applicantId']").val()){
        table = "applicant_administrative";
        userid = $("input[name='applicantId']").val();
    }
    else{
        table = "employee_administrative"; 
        userid = $("input[name='employeeid']").val();
    }
    $.ajax({
        url: $("#site_url").val() + "/employee_/deleteData",
        type: "POST",
        data: {table: GibberishAES.enc(table , toks), tbl_id: GibberishAES.enc( tbl_id, toks), employeeid:  GibberishAES.enc(userid , toks), toks:toks},
        dataType: "JSON",
        success: function(msg){  
        tableData('employee_administrative_tbody'); 
          Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: 'Successfully deleted data!',
              showConfirmButton: true,
              timer: 1000
          }) 
        }
    });  
}

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

    // save here training & seminars
    function saveTrainingSeminarsWorkshops(){
           /** PTS */
           var pts = "";
           $("#ptsinfolist").find("tbody tr").each(function(){
             if($(this).find("td").length>1){
                 pts += (pts?"|":"");
                 pts+= $(this).find("td:eq(0)").text();
                 pts+= "~u~";
                 pts+= $(this).find("td:eq(1)").text();
                 pts+= "~u~";
                 pts+= $(this).find("td:eq(2)").text();
                 pts+= "~u~";
                 pts+= $(this).find("td:eq(3)").text();
                 
             }
           });

            /** PTS_pdp1 */
           var pts_pdp1 = "";
           $("#pts_pdp1infolist").find("tbody tr").each(function(){
             if($(this).find("td").length>1){
                 pts_pdp1 += (pts_pdp1?"|":"");
                 pts_pdp1+= $(this).find("td:eq(0)").text();
                 pts_pdp1+= "~u~";
                 pts_pdp1+= $(this).find("td:eq(1)").text();
                 pts_pdp1+= "~u~";
                 pts_pdp1+= $(this).find("td:eq(2)").text();
                 pts_pdp1+= "~u~";
                 pts_pdp1+= $(this).find("td:eq(3)").text();
             }
           });

            /** PTS_pdp2 */
           var pts_pdp2 = "";
           $("#pts_pdp2infolist").find("tbody tr").each(function(){
             if($(this).find("td").length>1){
                 pts_pdp2 += (pts_pdp2?"|":"");
                 pts_pdp2+= $(this).find("td:eq(0)").text();
                 pts_pdp2+= "~u~";
                 pts_pdp2+= $(this).find("td:eq(1)").text();
                 pts_pdp2+= "~u~";
                 pts_pdp2+= $(this).find("td:eq(2)").text();
                 pts_pdp2+= "~u~";
                 pts_pdp2+= $(this).find("td:eq(3)").text();
             }
           });

              /** PGD */
           var pgd = "";
           $("#pgdinfolist").find("tbody tr").each(function(){
             if($(this).find("td").length>1){
                 pgd += (pgd?"|":"");
                 /*pgd += $(this).find("td:eq(0)").text();*/
                 pgd += $(this).find("td:eq(0)").text();
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
                 /*ar += $(this).find("td:eq(0)").text();*/
                 ar += $(this).find("td:eq(0)").attr('educar');
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
                 scs += $(this).find("td:eq(0)").text();
                 scs += "~u~";
                /* scs += $(this).find("td:eq(1)").text();*/
                 scs += $(this).find("td:eq(1)").text();
                 scs += "~u~";
                 scs += $(this).find("td:eq(2)").text();
                 scs += "~u~";
                 scs += $(this).find("td:eq(3)").text();
                 scs += "~u~";
                 scs += $(this).find("td:eq(4)").text();
                 scs += "~u~";
                 scs += $(this).find("td:eq(5)").text();
             }
           });
           
           /** Trainings/Workshops (Related to field ot expertise) */
           var tw = "";
           $("#twinfolist").find("tbody tr").each(function(){
             if($(this).find("td").length>1){
                 tw += (tw?"|":"");
                 tw += $(this).find("td:eq(0)").text();
                 tw += "~u~";
                 tw += $(this).find("td:eq(1)").attr('eductw');
                 /*tw += $(this).find("td:eq(1)").text();*/
                 tw += "~u~";
                 tw += $(this).find("td:eq(2)").text();
                 tw += "~u~";
                 tw += $(this).find("td:eq(3)").text();
                 tw += "~u~";
                 tw += $(this).find("td:eq(4)").text();
                 tw += "~u~";
                 tw += $(this).find("td:eq(5)").text();
             }
           });
           /** Speaking Engagements/Resource Speaker */
           var resource = "";
           $("#resourceinfolist").find("tbody tr").each(function(){
             if($(this).find("td").length>1){
                 resource += (resource?"|":"");
                 resource += $(this).find("td:eq(0)").text();
                 resource += "~u~";
                 resource += $(this).find("td:eq(1)").text();
                /* resource += $(this).find("td:eq(1)").text();*/
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
                 org += $(this).find("td:eq(1)").text();
                 /*org += $(this).find("td:eq(1)").text();*/
                 org += "~u~";
                 org += $(this).find("td:eq(2)").text();
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
             }
           });
           
           /** Administrative Functions Handled */
           var administrative = "";
           $("#administrativeinfolist").find("tbody tr").each(function(){
             if($(this).find("td").length>1){
                 administrative += (administrative?"|":"");
                 administrative += $(this).find("td:eq(0)").text();
                 administrative += "~u~";
                 /*administrative += $(this).find("td:eq(1)").text('educafh');*/
                 administrative += $(this).find("td:eq(1)").text();
                 administrative += "~u~";
                 administrative += $(this).find("td:eq(2)").text();
             }
           });

           var usertype = "<?=$usertype?>";
           var $validator;
           if(usertype == "EMPLOYEE"){
              $validator = $("#seminarstraining").validate({
                   rules: {
                      ptscbox         :{required: {depends: function(element) {return (pts          == "");}}},
                      pts_pdp1cbox         :{required: {depends: function(element) {return (pts_pdp1          == "");}}},
                      pts_pdp2cbox         :{required: {depends: function(element) {return (pts_pdp2          == "");}}},

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
              $( "input[name=pts_pdp1cbox]"         ).rules( "add", { required: { depends: function(element) { return (pts_pdp1           == ""); }}});
              $( "input[name=pts_pdp2cbox]"         ).rules( "add", { required: { depends: function(element) { return (pts_pdp2           == ""); }}});
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
                    form_data += "&pts_pdp1="+pts_pdp1;
                    form_data += "&pts_pdp2="+pts_pdp2;
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
                $.ajax({
                   url: "<?=site_url("employee_/validateinfo")?>",
                   data : form_data,
                   type : "POST",
                   success:function(msg){
                     // alert($(msg).find("message:eq(0)").text());
                     Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: $(msg).find("message:eq(0)").text(),
                        showConfirmButton: true,
                        timer: 1000
                    }) 
                   }
                }); 
            }else {
              Swal.fire({
                        icon: 'warning',
                        title: 'Warning!',
                        text: "Please complete all required fields",
                        showConfirmButton: true,
                        timer: 1000
                    }) 
              $validator.focusInvalid();
              $( "input[name=ptscbox],input[name=pts_pdp1cbox],input[name=pts_pdp2cbox], input[name=pgdcbox], input[name=rescbox], input[name=awardscbox], input[name=schocbox], input[name=semcbox], input[name=twcbox], input[name=resourcecbox], input[name=orgcbox], input[name=cominvolvecbox], input[name=adminfcbox]" ).rules( "remove" );
              cancontinue = false;
              return false;
            }
    }

    $(".tooltip").hover(function(){
    var id = $(this).attr('id');
    var table = $(this).attr('table');
    loadStatusHistory(id, table);
  });

  function loadStatusHistory(id, table){
    $.ajax({
        url: $("#site_url").val() + "/employee_/loadStatusHistory",
        type: "POST",
        data: {id:id, table:table},
        success: function(history){ 
            if(history != '')  $(".tooltiptext_"+id+"_"+table).html(history);
            else $(".tooltiptext_"+id+"_"+table).html("No History");
        }
    });
  }

    $(".savetrainingseminars").click(function(){
          saveTrainingSeminarsWorkshops();
    });
    $('.chosen').chosen();

     setTimeout(
      function() 
      {
        $(".widgets_area").removeClass("animated fadeIn");
      }, 2000);

$("#ptsinfolist, #pts_pdp1infolist, #pts_pdp2infolist, #pts_pdp3infolist, #pgdinfolist, #arinfolist, #schoinfolist, #resourceinfolist, #orginfolist, #communityinfolist, #administrativeinfolist").delegate(".filename", "click", function(){
    var trid = $(this).closest("tr");
      var data = $(trid).find(".filename").attr("content");
      var mime = $(trid).find(".filename").attr("mime");
      openFile(data, mime);
      
});
  function openFile(data, mime){
      if(data){
      var objectURL = URL.createObjectURL(b64toBlob(data, mime)) + '#toolbar=0&navpanes=0&scrollbar=0';
        window.open(objectURL);
      }else{
        var file_url = $(this).attr("content");
        window.open(file_url);
      }
  }
  function b64toBlob(b64Data, contentType) {
        var byteCharacters = atob(b64Data)
        var byteArrays = []
        for (let offset = 0; offset < byteCharacters.length; offset += 512) {
            var slice = byteCharacters.slice(offset, offset + 512),
                byteNumbers = new Array(slice.length)
            for (let i = 0; i < slice.length; i++) {
                byteNumbers[i] = slice.charCodeAt(i)
            }
            var byteArray = new Uint8Array(byteNumbers)

            byteArrays.push(byteArray)
        }

        var blob = new Blob(byteArrays, { type: contentType })
        return blob
  }
      
    </script>