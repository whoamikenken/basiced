<div class="panel-body">
    <table class="table table-striped table-bordered table-hover" id="retirementTable" width="100%">
        <thead>
            <tr style="background: #0072c6">
                <th>Employee ID</th>
                <th>Employee Name</th>
                <th>Department</th>
                <th>Office</th>
                <th>Date of Birth</th>
                <th>Age</th>
                <th>Start Date</th>
                <th class="align_center" width="1%">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
        	<?php foreach ($tblData as $value): ?>
            <tr>
                <td><?=$value['employeeid']?></td> 
                <td><?=$value['fullname']?></td> 
                <td><?=$this->extras->getemployeedepartment($value['deptid'])?></td> 
                <td><?=$this->extras->getemployeeoffice($value['office'])?></td> 
                <td><?=date("F d, Y", strtotime($value['bdate']))?></td> 
                <td><?=$value['age']?></td> 
                <td><?=date("F d, Y", strtotime($value['dateposition']))?></td> 
                <td>
                  <div style="padding-right: 3%;padding-left: 3%; ">
                      <a class='btn btn-primary editBtn' href='#modal-view' data-toggle='modal' employeeid="<?=$value['employeeid'] ?>" office="<?=$value['office'] ?>" dept="<?=$value['deptid'] ?>" estat="<?=$value['employmentstat'] ?>" datepos="<?=$value['dateposition'] ?>" pos="<?=$value['positionid'] ?>"><i class='glyphicon glyphicon-edit'></i></a>
                  </div>
                </td>           
            </tr>
        	<?php endforeach;  ?>
        </tbody>
    </table>
</div>
<script type="text/javascript">
var toks = hex_sha512(" ");
$(document).ready(function() {
    $('#retirementTable').DataTable( {
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
    } );
} );

$("#retirementTable").delegate(".editBtn", "click", function(){
    $("#modal-view").find(".modal-footer").html('<button type="button" data-dismiss="modal" class="btn btn-danger modalclose" id="modalclose">Close</button> <button type="button" class="btn btn-success" id="button_save_retirement">Save</button>');
        
        var employeeid      =  $(this).attr("employeeid"),
            management      = $(this).attr('mgmt'),
            deptid          = $(this).attr('dept'),
            office          = $(this).attr('office'),
            employmentstat  = $(this).attr('estat'),
            position        = $(this).attr('pos'),
            datepos         = $(this).attr('datepos'),
            dateresigned    = '',
            resigned_reason = '';

        $("#modal-view").find("h3[tag='title']").text("Edit Employment Status");
        $("#button_save_modal").text("Save");
        var form_data = {
            employeeid:  GibberishAES.enc(employeeid , toks),
            management:  GibberishAES.enc(management , toks),
            deptid:  GibberishAES.enc( deptid, toks), 
            office:  GibberishAES.enc(office , toks),
            employmentstat:  GibberishAES.enc(employmentstat , toks),
            position:  GibberishAES.enc( position, toks),
            datepos:  GibberishAES.enc( datepos, toks),
            dateresigned:  GibberishAES.enc(dateresigned , toks),
            resigned_reason:  GibberishAES.enc(resigned_reason , toks),
            folder :  GibberishAES.enc("employee" , toks),
            page   :  GibberishAES.enc( "estat_modal", toks),
            retirement   :  GibberishAES.enc( "yes", toks),
            toks:toks
        };
        $.ajax({
            url: $("#site_url").val() + "/employee_/viewModal",
            type: "POST",
            data: form_data,
            success: function(msg){
                $("#modal-view").find("div[tag='display']").html(msg);
            }
        }); 
});
</script>