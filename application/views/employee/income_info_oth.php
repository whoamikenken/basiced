<br>
<br>
<br>
<br>
<div class="widgets_area">
    <div class="panel">
        <div class="panel-heading"><h4><b>Employee Other Income</b></h4></div>
        <div class="panel-body align_center">   
            <div id="info"></div>   
        </div>
    </div>
</div>


<script>
$(document).ready(function(){
   loadotherincomeinfo();
});
function loadotherincomeinfo()
{
    $("#info").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
    $("#info").html("<h1>qweq</h1>");
     $.ajax({
        url     :   "<?=site_url("main/otherincome")?>",
        type    :   "POST",
        data    :   {view:"other_income"},
        success :   function(msg){
            $("#info").html(msg);
        }
    });
}
$("#addincomeoth").click(function(){
    $("#modal-view").find("h3[tag='title']").text("Add Income");  
    var form_data = {
        view: "employee/addincomeoth"
    };
    $.ajax({
        url: "<?=site_url('main/siteportion')?>",
        type: "POST",
        data: form_data,
        success: function(msg){
            $("#modal-view").find("div[tag='display']").html(msg);
        }
    });
});
</script>