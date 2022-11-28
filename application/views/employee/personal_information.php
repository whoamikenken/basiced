<?php
$CI =& get_instance();
$CI->load->model('utils');

$utype = $this->session->userdata('usertype');
$userid = $this->session->userdata("userid");

if($utype == 'EMPLOYEE') $res = $this->menus->loadempmenus(63,$userid,$utype);
else $res = $this->menus->loadmenus(63,$userid,$utype,true);

$data['title']='';
if($job=="new"){
   $tarrs = array(
     "employeeid"=>"",
     "employeecode"=>"",
     "lname"=>"",
     "fname"=>"",
     "mname"=>"",
     "cityaddr"=>"",
     "months_b"=>"",
     "days_b"=>"",
     "years_b"=>"",
     "gender"=>"",
     "emptype"=>"",
     "empshift"=>"",
     "civil_status"=>"",
     "employmentstat"=>"",
     "bplace"=>"",
     "mobile"=>"",
     "citytelno"=>"",
     "email"=>"",
     "deptid"=>"",
     "maxregular"=>"",
     "maxparttime"=>"",
     "month_employed_b"=>"",
     "days_employed_b"=>"",
     "years_employed_b"=>"",
     "income_base"=>"",
     "legitimate_relations"=>""
   );  
  $data['empinfo']=array($tarrs);  
  $employeeid = '';  
}else{
  $data['empinfo']=$this->employee->loadallemployee(array("employeeid"=>$employeeid));  
}
$this->session->set_userdata("personalinfo", $data['empinfo']);   
?>
<div class="inner_content">
<div class="row">
<div class="col-md-12">
    <div class="well blue">
      <div class="well-content no_padding" style="border: 0 !important;">
              <div class="navbar-header">
                  <ul class="nav nav-tabs" id="pinfotab">
                    <?
                    ///< modified @angelica
                    ///< dynamic generation of my profile tabs
                    $count=0;$firstload='';
            
                    foreach ($res as $menus) :
                      list($menuid,$root,$link,$title,$status,$arranged,$icon,$comments) = $menus;
                      if($utype<>"SUPER ADMIN" && $utype<>"ADMIN")
                      {
                        $notInclude = array(113,114,115,116,117,130);
                        if(in_array($menuid,$notInclude))
                        {
                          continue;
                        }
                      }
                        $read = 'YES';
                        if($utype<>"SUPER ADMIN") $read = $CI->utils->getUserAccess('read',$userid, $menuid);
                        if($read=='YES' && $count==0) $firstload = $menuid;
                        if($read == 'YES'): 
                          ///< for payroll access
                          if($menuid == 113) {

                              if($utype=="SUPER ADMIN" || $CI->utils->getUserAccessPayroll($userid, $employeeid)){
                                  ?>
                                    <li><a href="#<?=$menuid?>" data-toggle="tab" style="color:red"><?=strtoupper($title)?></a></li>
                                  <? 
                                  $count++;
                              }
                          }else{
                              ?>
                                <li><a href="#<?=$menuid?>" data-toggle="tab" style="color:#9a9a9a; font-weight: bold"><?=strtoupper($title)?></a></li>
                              <?
                              $count++;
                          }
                        endif;
                    endforeach;?>
                  </ul>
              </div>
              <div class="tab-content">
                  <?
                    foreach ($res as $menus) {
                      list($menuid,$root,$link,$title,$status,$arranged,$icon,$comments) = $menus;?>
                        <div class="tab-pane" id="<?=$menuid?>" ld='<?=$link?>'>
                          <?
                            if($firstload == $menuid) {$this->load->view($link,$data);}
                          ?>
                        </div>
                  <?}?>
              </div>
      </div>
    </div>
</div>
</div>
</div>


<script>
var newJob = "";
<?
  if($job == "new") echo "newJob='new';";
?>
var cancontinue = false;
var message = "";
var fndEditMsg = "";
var fromTab = "#110";
$(document).ready(function(){
    $('#pinfotab li:first').addClass('active');
    $('.tab-content div:first').addClass('active');
    $.ajax({
            url: "<?=site_url("employee_/checkhasssession")?>",
            type:"POST",
            success: function(msg){
                if($(msg).find("status:eq(0)").text()==0){
                   message = $(msg).find("message:eq(0)").text();  
                }else{
                   cancontinue = true;
                };
            }
        });
});
function refreshtab(tabn){
    var form_data = { 
      view : $(tabn).attr("ld")
    };

    $.ajax({
            url: "<?=site_url("main/siteportion")?>",
            data: form_data,
            type:"POST",
            success: function(msg){
                $(tabn).html(msg);
            }
        });
}

$("#pinfotab li").click(function(){
  var obj = $(this).find("a").attr("href");
  if(!cancontinue && fndEditMsg == "")alert(message);
  else{
    refreshtab(obj);
  }  
  return cancontinue;
});
</script>