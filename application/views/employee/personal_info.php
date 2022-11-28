<?php
$CI =& get_instance();
$CI->load->model('utils');
$canWriteAccess = $this->session->userdata('canwrite');
$utype = $this->session->userdata('usertype');
$userid = $this->session->userdata("userid");
if($utype == 'EMPLOYEE') $res = $this->menus->loadempmenus(63,$userid,$utype);
else $res = $this->menus->loadmenus(63,$userid,$utype,true);
// echo "<pre>"; print_r($res); die;
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
}else{
  $data['empinfo']=$this->employee->loadallemployee(array("employeeid"=>$employeeid));  
}
$this->session->set_userdata("personalinfo", $data['empinfo']);   
?>
<style>
  .navbar-header{
    font-size: 13.4px;  
  }
  #content.active>div>div>div>div>ul {
    font-size: 15.9px; 
}

@media (min-width: 1427px){
.navbar-header {
    font-size: 9.8px;
}
#content.active>div>div>div>div>ul {
    font-size: 12.1px; 
}
        .panel {
    border: 5px solid #0072c6 !important;
    box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
    margin-bottom: 49px !important;
}
</style>
<div class="inner_content">
<div class="row" style="margin-left: 0px; margin-right: 0px;">
<div class="col-md-12" style="padding-right: 0px">
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
                                <li><a href="#<?=$menuid?>_menuid" data-toggle="tab" style="color:red"><?=strtoupper($title)?></a></li>
                              <? 
                              $count++;
                          }
                      }else{
                          ?>
                            <li><a href="#<?=$menuid?>_menuid" data-toggle="tab" style="color:#9a9a9a; font-weight: bold"><?=strtoupper($title)?></a></li>
                          <?
                          $count++;
                      }
                    endif;
                endforeach;?>
              </ul>
          </div>
          <div class="tab-content">
              <?
              // echo "<pre>"; print_r($res); 
                foreach ($res as $menus) {
                  list($menuid,$root,$link,$title,$status,$arranged,$icon,$comments) = $menus;
                  // echo $link;
                  ?>

                <div class="tab-pane" id="<?=$menuid?>_menuid" ld='<?=$link?>' <?=($canWriteAccess == 0 ? 'style="pointer-events: none"' : $canWriteAccess)?>>
                    <?
                      if($firstload == $menuid) $this->load->view($link,$data);
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
    var toks = hex_sha512(" ");
    var cancontinue = false;
    $(document).ready(function(){
        $('#pinfotab li:first').addClass('active');
        $('.tab-content div:first').addClass('active');
        $.ajax({
            url: "<?=site_url("employee_/checkhasssession")?>",
            type:"POST",
            success: function(msg){
                if($(msg).find("status:eq(0)").text()==0){
                    alert("Session expired, your account will be logged out.");
                    location.reload(); 
                }else{
                    cancontinue = true;
                }
            }
        });
    });

    $("#pinfotab li").click(function(){
        if(cancontinue){
            var obj = $(this).find("a").attr("href");
            refreshtab(obj);
            $("#pinfotab li").removeClass('active');
            $(this).addClass('active');
            $('.widgets_area').hide();
        }else{
            cancontinue = true;
            $("#pinfotab li").removeClass('active');
            $(this).addClass('active');
        }
    });

    function refreshtab(tabn){
        $('.tab-pane').removeClass("active");
        console.log(tabn);
        $(tabn).addClass("active");
        var form_data = { 
          view :  GibberishAES.enc($(tabn).attr("ld"), toks),
          toks:toks
        }
        console.log($(tabn).attr("ld"));
        $.ajax({
                url: "<?=site_url("main/siteportion")?>",
                data: form_data,
                type:"POST",
                success: function(msg){
                    $(tabn).html(msg);
                    canwrite(tabn);
                    // if("<?=$this->session->userdata('canwrite')?>" == 0) $(tabn).css("pointer-events", "none");
                    // else $(tabn).css("pointer-events", "");
                }
            });
      }

      function canwrite(tabn){
          var form_data = { 
            menuid :  GibberishAES.enc($(tabn).attr("id"), toks),
            toks:toks
          }
          $.ajax({
              url: "<?=site_url("main/getWriteAccess")?>",
              data: form_data,
              type:"POST",
              success: function(msg){
                  if(msg == 0) $(tabn).css("pointer-events", "none");
                  else $(tabn).css("pointer-events", "");
              }
          });
      }

      function tableData(view){
          $("#"+view).html("<tr><td style='white-space: nowrap;'><img src='<?=base_url()?>images/loading.gif' />  Loading data, please Wait..</td></tr>");
          $.ajax({
              url: "<?= site_url('extensions_/loadTableData') ?>",
              type: "POST",
              data: {
                employeeid : GibberishAES.enc($("input[name='employeeid']").val(),toks),
                view : GibberishAES.enc(view,toks),
                toks:toks
              },
              success:function(response){
                $("#"+view).html(response);
              }
           });
       }

  
</script>