
<style type="text/css">
        .panel {
    border: 5px solid #0072c6 !important;
    box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
    margin-bottom: 49px !important;
}
.swal2-cancel{
    margin-right: 20px;
}
</style>

<div id="content">
    <div class="widgets_area" >
        <div class="row">
            <div class="col-md-12">
                <div class="panel animated fadeIn delay-1s">
                   <div class="panel-heading" style="background-color: #0072c6;"><h4><strong>Concerned Office</strong></h4></div>
                   <div class="panel-body" id="deptDeficiency">
                       
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="widgets_area" style="margin-top: 0px; padding-top: 0px;">
        <div class="row">
            <div class="col-md-12">
                <div class="panel animated fadeIn delay-1s">
                   <div class="panel-heading" style="background-color: #0072c6;"><h4><strong>Clearance</strong></h4></div>
                   <div class="panel-body" id="data_table">
                       
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
loaddeficiencydata();
loadDeptDeficiency();
function loaddeficiencydata(){
    $.ajax({
        url: "<?= site_url('deficiency_/loadDeficiency')?>",
        success:function(response){
            $("#data_table").html(response);
        }
    });
}

$(document).on("click", '#del-submit', function(){
    var infotype = "code_deficiency";
    var id = $(this).attr('tagkey');
    $.ajax({
        url: "<?=site_url('deficiency_/deleteRow')?>",
        type: "POST",
        data: {id:id, infotype:infotype},
        success: function(msg){
            loaddeficiencydata();
            $(".del-close").click();    
        }
    });
});

function deleteDeficiency(id){
    var infotype = "code_deficiency";
    $.ajax({
        url: "<?=site_url('deficiency_/deleteRow')?>",
        type: "POST",
        data: {id:id, infotype:infotype},
        success: function(msg){
            loaddeficiencydata();
            Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: 'Clearance has been deleted successfully.',
              showConfirmButton: true,
              timer: 1000
          }) 
        }
    });
}

function loadDeptDeficiency(){
    $.ajax({
        url: "<?= site_url('deficiency_/loadDeptDeficiency')?>",
        success:function(response){
            $("#deptDeficiency").html(response);
        }
    });
}

$(document).on('click', '#delDeptid', function(){ 
    var id = $(this).attr('tagkey');
    var did = $(this).attr('did');
    $.ajax({
        url: "<?=site_url('deficiency_/deleteDeptDeficiency')?>",
        type: "POST",
        data: {id:id, did:did},
        success: function(msg){
            loadDeptDeficiency();
            $(".del-close").click();    
        }
    });
});

function deleteDeficiencyDept(id='', did=''){
    $.ajax({
        url: "<?=site_url('deficiency_/deleteDeptDeficiency')?>",
        type: "POST",
        data: {id:id, did:did},
        success: function(msg){
            if(msg == "InUse"){
                Swal.fire({ 
                  icon: 'warning',
                  title: 'Warning!',
                  text: 'Concerned department is currently in use, it cannot be deleted.',
                  showConfirmButton: true,
                  timer: 1000
                }) 
            }else{
                Swal.fire({ 
                  icon: 'success',
                  title: 'Success!',
                  text: 'Concerned department has been deleted successfully.',
                  showConfirmButton: true,
                  timer: 1000
                }) 
                loadDeptDeficiency();

            }
              
        }
    });
}
    
</script>