<?php
$CI =& get_instance();
$CI->load->model('applicantt');
$counter = 1;
	

?>
<div class="panel-body">
    <table class="table table-striped table-bordered table-hover" id="applicantHistoryTable">
        <thead>
            <tr style="background-color: #0072c6">
                <th class="col-md-1">#</th>
                <th>Applicant ID</th>
                <th class="sorting_asc">Name</th>
                <th>Position Applied</th>
                <th>Applicant Status</th>
                <th>Date Processed</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($applicantlist as $value) {
                $fullname = $value['lname'].', '.$value['fname'].' '.$value['mname'];
                $current_status = $CI->applicantt->getLatestStatus($value['applicantId']);
                ?>
                    <tr employeeid="<?=$value['applicantId']?>" positionid="<?=$value['positionApplied']?>" redtag="<?=$value['redtag']?>" remarks="<?=$value['redTagRemarks']?>" code_status="<?=$value['code_status']?>" style="cursor: pointer;">
                        <td><?= $counter ?></td>
                        <td><?= $value['applicantId'] ?></td>
                        <td><?= $fullname ?></td>
                        <td><?= $value['description'] ?></td>
                        <td>
                            <?php foreach($CI->applicantt->getApplicantSetup($value['isteaching']) as $stat_list): ?>
                                <?php if($stat_list['id'] == $current_status) echo $stat_list['description'];  ?>
                            <?php endforeach ?>
                        </td>
                        <td><?= $value['timestamp'] ?></td>   
                    </tr>
                <?php
                $counter++;
            }
            ?>
        </tbody>
    </table>
</div>
<script>
	$("#applicantHistoryTable").DataTable();

    $("#applicantHistoryTable").delegate("td", "click", function(){
        var employeeid = $(this).closest("tr").attr("employeeid");
        var positionid = $(this).closest("tr").attr("positionid");
        var redtag = $(this).closest("tr").attr("redtag");
        var remarks = $(this).closest("tr").attr("remarks");
        var code_status = $(this).closest("tr").attr("code_status");

        var form_data = {
            job : "edit",
            applicantId : employeeid,
            positionid : positionid,
            redtag : redtag,
            remarks : remarks,
            code_status : code_status,
            view: "applicant/applicant_info",
            applicant_status: $(this).closest("tr").find(".status_list").val()
        }; 
        $.ajax({
            url : "<?=site_url("main/siteportion")?>",
            type: "POST",
            data: form_data,
            success: function(msg){
                $("#content").html(msg);
            }
        });
    }); 
</script>