 <?php
 $today = date("Y-m-d");

 $qholidays = $this->extras->showHol();
 echo "<pre>";print_r($this->db->last_query());die;
    $CI =& get_instance();
    $CI->load->model('announcements');
    $a_list = $CI->announcements->getAnnouncements(""); 
  $count = 0;
 ?>
 <?foreach($a_list as $row){
                   $qdate = $this->extras->holDate($row->datefrom,$row->dateto);
                   foreach($qdate as $rowd){
                     $getmonth = date("m",strtotime($rowd->dte));
                     $getyear = date("Y",strtotime($rowd->dte));
                                 if ($today > $row->datefrom  AND $getmonth == $month AND $getyear == $year) {
                                    $count++;
                                     
                                   ?>
                                   <div class="eventsize">
                                       <a href="#" class="postcard-link">
                                       <div class="postcard-left">
                                           <div class="showcdes1">
                                               <p><?=date("l",strtotime($rowd->dte))?></p>
                                               <p style="font-weight: bold !important;font-size: 21px;"><?=date("d",strtotime($rowd->dte))?></p>
                                               <p style="color: yellow;"><span><?=date("F",strtotime($rowd->dte))?></span> <span><?=date("Y",strtotime($rowd->dte))?></span></p>
                                           </div>
                                           <div class="postcard-text1">
                                              
                                               <span style="font-size: 12px;">
                                                   <!-- <p style="color: red;">         CONCERNED DEPARTMENT:                                               </p>
                                                   <p style="color: black;">       <b>&nbsp;<?=$this->extras->getemployeedepartment($row->deptid)?></b></p> -->
                                                   <p style="color: red; "> <b> VENUE:</b> &nbsp;<span style="color: black;font-size:10px;"><?=ucwords(strtolower($row->venue))?><span> </p>
                                                   
                                                   <p style="color: red; "><b>TIME:</b>                                                            
                                                   <span style="color: black;font-size:10px;">&nbsp;<?
                                                                                       $timedisp = date("h:i a",strtotime($row->timefrom)) . " - " . date("h:i a",strtotime($row->timeto));
                                                                                       echo ucwords(strtolower($timedisp));
                                                                                       ?></span>  </p>
                                                   <p style="color: red; "><b>EVENT:</b>  <span style="font-size:12px;color:black;text-indent:20px "><?=ucwords(strtolower($row->event))?></span></p>
                                               </span>
                                           </div>
                                       </div>
                                       </a><hr class="clearfix">
                                   </div>
                               <? 
                               }
                               }
                               }   
                               ?>
                           
                               <?foreach($qholidays as $row){

                                   $qdate = $this->extras->holDate($row->date_from,$row->date_to);
                                   foreach($qdate as $rowd){
                                     $getmonth = date("m",strtotime($rowd->dte));
                                     $getyear = date("Y",strtotime($rowd->dte));
                                     if ($today > $rowd->dte  AND $getmonth == $month AND $getyear == $year) {
                                     $count++;

                                   ?>
                                   <div class="eventsize">
                                       <a href="#" class="postcard-link">
                                       <div class="postcard-left">
                                           <div class="showcdes">
                                               <p><?=date("l",strtotime($rowd->dte))?></p>
                                               <p style="font-weight: bold !important;font-size: 21px;"><?=date("d",strtotime($rowd->dte))?></p>
                                               <p style="color: yellow;"><span><?=date("F",strtotime($rowd->dte))?></span> 
                                               <span><?=date("Y",strtotime($rowd->dte))?></span></p>
                                           </div>
                                           <div class="postcard-text" >
                                               <h3><?#=ucwords(strtolower($row['event']))?></h3>
                                              <p class="event-text" style="font-size: 8.5px;">
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
                           
                                   echo "<div class='alert alert-danger text-align:center' style='height:10%;width:95%;background:rgba(223, 215, 227, 0.8); box-shadow: 10px 10px 10px #888888;'><span class='icon icon-exclamation-sign' aria-hidden='true'> No Record Found!</span></div>";
                                  }   
                               ?>
                  