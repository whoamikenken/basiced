<?php
/**
 * @author Angelica
 * @copyright 2018
 *
 */
?>

<div>
	<br>
	
	<table class="table table-striped table-bordered table-hover" id="longevity_include_table" >
		<thead>
			<th>Employee ID</th>
			<th>Name</th>
			<th class="align_center noSort">Is Included<br>
				<input type="checkbox" id="include_all" class="double-sized-cb">
			</th>
		</thead>
		<tbody>
			<? if($emplist){ 
					foreach ($emplist as $key => $row) {
						
			?>
						<tr>
							<td><?=$row->employeeid?></td>
							<td><?=$row->fullname?></td>
							<td class="align_center">
	                            <input type="checkbox" name="include_emp" class="double-sized-cb" employeeid="<?=$row->employeeid?>" <?=$row->isIncluded?' checked':''?> >
	                        </td>
						</tr>

			<? 		} 

				}

			?>
		</tbody>
	</table>
</div>
<br>
<a href="#" class="btn btn-danger pull-right" id="save_included">&nbsp;Save&nbsp;</a>
<br>

<script>
	$("#longevity_include_table").dataTable({
        "bPaginate": false,
        "oLanguage": {
                         "sEmptyTable":     "No Data Available.."
                     },
        "aoColumnDefs": [ 
                { "bSortable": false, "aTargets": [ 'noSort' ] }
                ]
    });

    ///< select employees to include

    $('#include_all').on('click',function(){

      if($(this).is(':checked')) $('input[name=include_emp]').prop('checked',true); 
      else 	$('input[name=include_emp]').prop('checked',false);
    });

    $('input[name=include_emp]').on('click',function(){
      if(!$(this).is(':checked'))     $('#include_all').prop('checked',false);
    });

    $("#save_included").click(function(){

        var emplist = [];

        $('input[name=include_emp]:checked').each(function(){
            emplist.push($(this).attr('employeeid'));
        });

        $.ajax({
        	type:"POST",
        	url:"<?=site_url("payroll_/saveLongevityEmpIncluded")?>",
        	dataType : 'JSON',
        	data: {'emplist':emplist},
        	success:function(msg){
        		$('#modal-view').find('.modal-header, #button_save_modal').hide();

        		var data_failed = msg.data_failed;
        		var failed = '';
        		for (var key in data_failed) {
        		    failed += data_failed[key] + ", ";
        		}
        		if(failed) failed = failed.substring(0, failed.length-2);
        		else failed = 'NONE';

        		if(msg.err_code == 0){
        		  
        		  if(failed == 'NONE') $('#modal-view').find('.modal-body').html(msg.msg+'<br>'+'Success count: '+msg.success_count+'<br>'+'Data insert failed: '+failed).css({'color':'green','font-size':'15px','font-weight':'bold'});
        		  else{
        		    $('#modal-view').find('.modal-body').html(msg.msg+'<br>'+'Success count: '+msg.success_count+'<br>').css({'color':'green','font-size':'15px','font-weight':'bold'});
        		    $('#modal-view').find('.modal-body').append('<span style="color:red;">Data insert failed: '+failed+'</span>');
        		  }                  
        		}else{
        		  $('#modal-view').find('.modal-body').html(msg.msg+'<br>'+'Success count: '+msg.success_count+'<br>'+'Data insert failed: '+failed).css({'color':'red','font-size':'15px','font-weight':'bold'});
        		}

        		$('#modal-view').modal('show');
        		$('#loading').attr('hidden',true);
        	}
        });

     });

</script>