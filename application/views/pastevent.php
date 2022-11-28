 <?php
 $today = date("Y-m-d");
$months = date("m");

 $qholidays = $this->extras->showHolPast($month,$year);

    $CI =& get_instance();
    $CI->load->model('announcements');
    $a_list = $CI->announcements->getAnnouncements("","",$month,$year); 
    // echo "<pre>";print_r($this->db->last_query());die;
  $count = 0;
 ?>
 <?foreach($a_list as $row){
                   
                     $getmonthTo = date("M",strtotime($row->dateto));
                     $getmonthFrom = date("M",strtotime($row->datefrom));
                     $getdayTo = date("d",strtotime($row->dateto));
                     $getdayFrom = date("d",strtotime($row->datefrom));
                     $displayMonth = ($getmonthTo == $getmonthFrom)? $getmonthTo: $getmonthFrom." - ".$getmonthTo;
                     $displayDay = ($getdayTo == $getdayFrom)? $getdayTo: $getdayFrom." - ".$getdayTo;
                     // echo "<pre>";print_r($displayMonth);die;
                                 if ($today > $row->datefrom) {
                                    $count++; 
                                   ?>
                                   <div class="eventsize">
                                       <a href="#" class="postcard-link">
                                       <div class="postcard-left">
                                           <div class="showcdes1" style="color: white; background-color: #337ab7;">
                                               <p style="color: white;"><?=date("l",strtotime($row->dateto))?></p>
                                               <p style="color: white;font-weight: bold !important;font-size: 21px;"><?=$displayDay?></p>
                                               <p style="color: white;"><span><?=$displayMonth?></span> <span><?=date("Y",strtotime($row->dateto))?></span></p>
                                           </div>
                                           <div class="postcard-text">
                                                <p class="event-text" style="font-size: 8.5px;">
                                                     <p style="color: red;"><b>VENUE:</b></p>
                                                     <p style="color: black;">&nbsp;<?=ucwords(strtolower($row->venue))?></p>
                                                     <p style="color: red;"><b>TIME:</b></p>
                                                     <p style="color: black;">&nbsp;&nbsp;<?
                                                                                           $timedisp = date("h:i a",strtotime($row->timefrom)) . " - " . date("h:i a",strtotime($row->timeto));
                                                                                           echo ucwords(strtoupper($timedisp));
                                                                                           ?></p>
                                                     <p style="color: red;"><b>EVENT:</b></p>
                                                     <p style="color: black;">&nbsp;&nbsp;<?=ucwords(strtolower($row->event))?></p>
                                                 </p>
                                           </div>
                                       </div>
                                       </a><hr class="clearfix">
                                   </div>
                               <? 
                               }
                           }   

                           
                               ?>
                           
                               <?foreach($qholidays as $row){

                                   $qdate = $this->extras->holDate($row->date_from,$row->date_to);
                                   $username = $this->session->userdata("username");
                                   $deptid = $this->extensions->getEmployeeDeparment($username);
                                   $campus = $this->extensions->getEmployeeDeparment($username);
                                   $teachingtype = $this->extensions->getEmployeeTeachingType($username);
                                   $wholeHoliday = $this->attcompute->isHolidayNew($username,$row->date_from,$deptid,$campus,"",$teachingtype="");
                                   $halfHoliday = $this->attcompute->isHolidayNew($username,$row->date_from,$deptid,$campus,"on",$teachingtype="");

                                     // echo "<pre>";print_r($this->db->last_qury());die;
                                   foreach($qdate as $rowd){

                                     $getmonth = date("m",strtotime($rowd->dte));
                                     $getyear = date("Y",strtotime($rowd->dte));
                                     $count++;
                                     if ( $wholeHoliday || $halfHoliday) {

                                   ?>
                                   <div class="eventsize">
                                       <a href="#" class="postcard-link">
                                       <div class="postcard-left">
                                           <div class="showcdes1" style="color: white; background-color: #337ab7;">
                                               <p style="color: white;"><?=date("l",strtotime($rowd->dte))?></p>
                                               <p style="color: white;font-weight: bold !important;font-size: 21px;"><?=date("d",strtotime($rowd->dte))?></p>
                                               <p style="color: white;"><span><?=date("F",strtotime($rowd->dte))?></span> 
                                               <span><?=date("Y",strtotime($rowd->dte))?></span></p>
                                           </div>
                                           <div class="postcard-text" >
                                              <h3><?#=ucwords(strtolower($row['event']))?></h3>
                                              <p style="font-size: 8.5px;">
                                                   <p style="color: red;"><b>Holiday Name:</b></p>
                                                   <p style="color: black;">&nbsp;<?=$row->hdescription?></p><br />
                                                   <p style="color: red;"><b>Holiday Type:</b></p>
                                                   <p style="color: black;">&nbsp;<?=$row->description?></p>
                                               </p>
                                           </div>
                                       </div>
                                       </a><hr class="clearfix">
                                   </div>
                               <?
                             }
                               }
                             }
                             if ($count <= 0  ) {
                         
                                 echo "<div class='alert alert-danger text-align:center' style='height:10%;width:98%;background:#337ab7; box-shadow: 10px 10px 10px #888888;color:white;margin-left: 14px;'><i class='glyphicon glyphicon-exclamation-sign' style='color:white;'></i>  No Record Found</div>";
                                }   
                             ?>
                  