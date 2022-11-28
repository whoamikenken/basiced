<?php
	//Added (6-2-2017)
    $CI =& get_instance();
    $CI->load->model('disciplinary_action');
    $offense_types = $CI->disciplinary_action->getOffensesTypes();
    $sanction = $CI->disciplinary_action->getSanctions();
?>
<style type="text/css">
        .panel {
    border: 5px solid #0072c6 !important;
    box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
    margin-bottom: 49px !important;
}
</style>
<div id="content">
    <div class="widgets_area">
        <div class="row">
            <div class="col-md-12">
               <div class="panel animated fadeIn delay-1s">
                   <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Type of Offenses</b></h4></div>
                   <div class="panel-body">
                       <table class="table table-striped table-bordered table-hover" id="typeOffenseTable">
                            <thead>
                                <tr>
                                    <th>
                                        <a class="btn btn-primary addbtnoffenses" href="#modal-view" data-toggle="modal" ><i class="glyphicon glyphicon-plus-sign"></i><span class="" style="font-family: Tahoma;"> Add New </span></a>
                                    </th>
                                </tr>                            
                                <tr style="background-color: #0072c6;">
                                    <th width='10%' class="align_center"><b>Actions</b></th>
                                    <th><b>Code</b></th>
                                    <th><b>Description</b></th>
                                </tr>
                            </thead>
                            <tbody>
								<? foreach( $offense_types as $each ): ?>
                                <tr>
                                    <td class="align_center">
                                        <a id="<?=$each->code;?>" class="btn btn-info editbtnoffenses" href="#modal-view" data-toggle="modal" ><i class="glyphicon glyphicon-edit"></i></a>
                                        <?php if($each->code != "ET" && $each->code != "EA"): ?>
                                            <a id="<?=$each->code;?>" class="btn btn-danger delbtnoffenses"><i class="glyphicon glyphicon-trash"></i></a>
                                        <?php endif; ?>
                                    </td>
                                    <td><?=$each->code;?></td>
                                    <td><?=$each->description;?></td>
                                </tr>
                                <? endforeach; ?>
                            </tbody>
                        </table>
					</div>
                </div>
				<div class="panel animated fadeIn delay-1s">
                   <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Disciplinary Sanction</b></h4></div>
                   <div class="panel-body">
                       <table class="table table-striped table-bordered table-hover" id="disciplinaryTable">
                            <thead>
                                <tr>
                                    <th>
                                        <a class="btn btn-primary addbtnsanction" href="#modal-view" data-toggle="modal" ><i class="glyphicon glyphicon-plus-sign"></i><span class="" style="font-family: Tahoma;"> Add New </span></a>
                                    </th>
                                </tr>                            
                                <tr style="background-color: #0072c6;">
                                    <th width='10%' class="align_center"><b>Actions</b></th>
                                    <th><b>Code</b></th>
                                    <th><b>Description</b></th>
                                </tr>
                            </thead>
                            <tbody>
								<? foreach( $sanction as $each ): ?>
                                <tr>
                                    <td class="align_center">
                                        <a id="<?=$each->code;?>" class="btn btn-info editbtnsanction" href="#modal-view" data-toggle="modal" ><i class="glyphicon glyphicon-edit"></i></a>
                                        <a id="<?=$each->code;?>" class="btn btn-danger delbtnsanction"><i class="glyphicon glyphicon-trash"></i></a>
                                    </td>
                                    <td><?=$each->code;?></td>
                                    <td><?=$each->description;?></td>
                                </tr>
                                <? endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var toks = hex_sha512(" ");
    $(document).ready(function(){
        $('#typeOffenseTable').DataTable();
        $('#disciplinaryTable').DataTable();
    });

    $(".addbtnoffenses,.editbtnoffenses").click(function(){
        var infotype = "code_disciplinary_action_offense_type";
        var code = "";
        if($(this).attr("id")) code = $(this).attr("id");
        
        $("#modal-view").find("h3[tag='title']").text(code ? "Edit Offenses Type" : "Add Offenses Type");
        $("#button_save_modal").text("Save");
        var form_data = {
            info_type:  GibberishAES.enc(infotype , toks),
            action:  GibberishAES.enc( code, toks),
            func_type:  GibberishAES.enc("offense" , toks),
            toks:toks
        };
        $.ajax({
            url: "<?=site_url('disciplinary_action_/viewForm')?>",
            type: "POST",
            data: form_data,
            success: function(msg){
                $("#modal-view").find("div[tag='display']").html(msg);
            }
        });  
    });
	
	$(".delbtnoffenses").click(function(){
        const swalWithBootstrapButtons = Swal.mixin({
          customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
          },
          buttonsStyling: false
        })

        swalWithBootstrapButtons.fire({
          title: 'Are you sure?',
          text: "You won't be able to revert this!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, proceed!',
          cancelButtonText: 'No, cancel!',
          reverseButtons: true
        }).then((result) => {
          if (result.value) {
            var code = $(this).attr("id");
            var infotype = "code_disciplinary_action_offense_type";
            $.ajax({
                url: "<?=site_url('disciplinary_action_/deleteRow')?>",
                type: "POST",
                data: {code: GibberishAES.enc( code, toks), infotype: GibberishAES.enc( infotype, toks), toks:toks},
                success: function(msg){
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'Offenses has been deleted successfully.',
                        showConfirmButton: true,
                        timer: 1000
                    })
        
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                }
            });
          } else if (
            result.dismiss === Swal.DismissReason.cancel
          ) {
            swalWithBootstrapButtons.fire(
              'Cancelled',
              'Data is safe.',
              'error'
            )
          }
        }) 
	});
	
	$(".addbtnsanction,.editbtnsanction").click(function(){
        var infotype = "code_disciplinary_action_sanction";
        var code = "";
        if($(this).attr("id")) code = $(this).attr("id");
        $("#modal-view").find("h3[tag='title']").text(code ? "Edit Disciplinary Sanction" : "Add Disciplinary Sanction");
        $("#button_save_modal").text("Save");
        var form_data = {
            info_type:  GibberishAES.enc(infotype , toks),
            action:  GibberishAES.enc( code, toks),
            func_type:  GibberishAES.enc("sanction" , toks),
            toks:toks
        };
        $.ajax({
            url: "<?=site_url('disciplinary_action_/viewForm')?>",
            type: "POST",
            data: form_data,
            success: function(msg){
                $("#modal-view").find("div[tag='display']").html(msg);
            }
        });  
    });
	
	$(".delbtnsanction").click(function(){
        const swalWithBootstrapButtons = Swal.mixin({
          customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
          },
          buttonsStyling: false
        })

        swalWithBootstrapButtons.fire({
          title: 'Are you sure?',
          text: "You won't be able to revert this!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, proceed!',
          cancelButtonText: 'No, cancel!',
          reverseButtons: true
        }).then((result) => {
          if (result.value) {
            var code = $(this).attr("id");
            var infotype = "code_disciplinary_action_sanction";
            $.ajax({
                url: "<?=site_url('disciplinary_action_/deleteRow')?>",
                type: "POST",
                data: {code: GibberishAES.enc( code, toks), infotype: GibberishAES.enc( infotype, toks), toks:toks},
                success: function(msg){
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'Sanction  has been deleted successfully.',
                        showConfirmButton: true,
                        timer: 1000
                    })
        
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                }
            }); 
          } else if (
            result.dismiss === Swal.DismissReason.cancel
          ) {
            swalWithBootstrapButtons.fire(
              'Cancelled',
              'Data is safe.',
              'error'
            )
          }
        }) 

	});
    
    if("<?=$this->session->userdata('canwrite')?>" == 0) $(".btn").css("pointer-events", "none");
    else $(".btn").css("pointer-events", "");
    
</script>