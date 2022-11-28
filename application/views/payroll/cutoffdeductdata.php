<?php

/**
 * @author Justin
 * @copyright 2015
 */
 
?>
<div id="content"> <!-- Content start -->
    <div class="widgets_area">
        <div class="row">  
            <div class="col-md-12">
                <div class="well blue">
                    <div class="well-header">
                        <h5>Processed Deduction Cut-Off</h5>
                        <ul>
                            <li class="color_pick"><a href="#"><i class="glyphicon glyphicon-th"></i></a>
                                <ul>
                                    <li><a class="blue set_color" href="#"></a></li>
                                    <li><a class="light_blue set_color" href="#"></a></li>
                                    <li><a class="grey set_color" href="#"></a></li>
                                    <li><a class="pink set_color" href="#"></a></li>
                                    <li><a class="red set_color" href="#"></a></li>
                                    <li><a class="orange set_color" href="#"></a></li>
                                    <li><a class="yellow set_color" href="#"></a></li>
                                    <li><a class="green set_color" href="#"></a></li>
                                    <li><a class="dark_green set_color" href="#"></a></li>
                                    <li><a class="turq set_color" href="#"></a></li>
                                    <li><a class="dark_turq set_color" href="#"></a></li>
                                    <li><a class="purple set_color" href="#"></a></li>
                                    <li><a class="violet set_color" href="#"></a></li>
                                    <li><a class="dark_blue set_color" href="#"></a></li>
                                    <li><a class="dark_red set_color" href="#"></a></li>
                                    <li><a class="brown set_color" href="#"></a></li>
                                    <li><a class="black set_color" href="#"></a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="well-content">
                        <div class="form_row no-search">
                            <label class="field_name align_right col-md-1">Deduction Cut-Off Date</label>
                            <div class="field">
                                <select class="chosen col-md-4" id="deduccutoffdate"><?=$this->payrolloptions->displaycutoffdeducdata();?></select>
                            </div>
                        </div>
                    <div id="cutoffdeductlist"></div><br />
                    </div>
                    <div class="modal fade" id="myModal" data-backdrop="static"></div>
                </div>
            </div>
        </div>        
    </div>        
</div>
<script>
$("#deduccutoffdate").change(function(){
   if($(this).val() != ""){
   $("#cutoffdeductlist").show().html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
   $.ajax({
       url      :   "<?=site_url("payroll_/payrollconfig")?>",
       type     :   "POST",
       data     :   {
                        cdate       :   $(this).val(),
                        isProcessed :   true,
                        view        :   "cutoffdeductlist"
                    },
       success  :   function(msg){
        $("#cutoffdeductlist").html(msg);
       }
    }); 
   }else{
    $("#cutoffdeductlist").hide();
   }
});


$(".chosen").chosen();
</script>