

<div id="well-header align_center" class="info">   
       
</div>

<script>


$(document).ready(function(){
       loadpayrollinfo();
    });
function loadpayrollinfo()
{
    $(".info").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
     $.ajax({
            url     :   "<?=site_url("payroll_/payrollview")?>",
            type    :   "POST",
            data    :   {view:"payroll_information"},
            success :   function(msg){
                $(".info").html(msg);
            }
        });
}
</script>