<style type="text/css">
    #detailedAttendance_next{
        color: #0e5384 !important;
        padding-left: 24px;
        margin-right: 10px;
    }
    td{
        text-align: center !important;
    }
    #detailedAttendance_wrapper{
        padding-left: 5%;
    }
    #detailedAttendance{
        width: 95% !important;
    }
</style>

<?php if($att_list){ ?>
    <table class="table table-bordered table-condensed" id="detailedAttendance">
        <thead id="head">
            <th></th>
            <th></th>                            
        </thead>
        <tbody>
            <?php foreach($att_list as $row): ?>
               <?php if($label == "Present"){ ?>
                <tr>
                    <td align="center">
                        <div class="imageDiv" usergender="<?= $row['gender'] ?>" userage="<?= $row['age'] ?>" imagediv="<?= $row['employeeid'] ?>" id="img_<?= $row['employeeid'] ?>">
                        </div>
                    </td>
                    <td align="center">
                        <ul style="list-style-type: none;font-weight: bold;padding-inline-start: 15px !important;">
                            <li><?=$row['fullname']?></li>
                            <li><?= $row['deptid'] ?></li>
                            <li>Time In: <?= $row['timein'] ?></li>
                            <li>Time Out: <?= $row['timeout'] ?></li>
                        </ul>
                    </td>
                </tr>
                <?php } else if($label == "On Time"){ ?>
                    <tr>
                        <td align="center">
                            <div class="imageDiv" usergender="<?= $row['gender'] ?>" userage="<?= $row['age'] ?>" imagediv="<?= $row['employeeid'] ?>" id="img_<?= $row['employeeid'] ?>">
                            </div>
                        </td>
                        <td align="center">
                            <ul style="list-style-type: none;font-weight: bold;padding-inline-start: 15px !important;">
                                <li><?=$row['fullname']?></li>
                                <li><?= $row['deptid'] ?></li>
                                <li>Time In: <?= $row['timein'] ?> || Time Out: <?= ($row['timeout']) ? date("g:i A", strtotime($row['timeout'])) : " -- " ?></li>
                            </ul>
                        </td>
                    </tr>
                <?php } else if($label == "Absent"){ ?>
                    <tr>
                        <td align="center">
                            <div class="imageDiv" usergender="<?= $row['gender'] ?>" userage="<?= $row['age'] ?>" imagediv="<?= $row['employeeid'] ?>" id="img_<?= $row['employeeid'] ?>">
                            </div>
                        </td>
                        <td align="center">
                            <ul style="list-style-type: none;font-weight: bold;padding-inline-start: 15px !important;">
                                <li><?=$row['fullname']?></li>
                                <li><?= $row['deptid'] ?></li>
                            </ul>
                        </td>
                    </tr>
                <?php } else if($label == "Flexible"){ ?>
                    <tr>
                        <td align="center">
                            <div class="imageDiv" usergender="<?= $row['gender'] ?>" userage="<?= $row['age'] ?>" imagediv="<?= $row['employeeid'] ?>" id="img_<?= $row['employeeid'] ?>">
                            </div>
                        </td>
                        <td align="center">
                            <ul style="list-style-type: none;font-weight: bold;padding-inline-start: 15px !important;">
                                <li><?=$row['fullname']?></li>
                                <li><?= $row['deptid'] ?></li>
                            </ul>
                        </td>
                    </tr>
                <?php } else if($label == "Part-time"){ ?>
                    <tr>
                        <td align="center">
                            <div class="imageDiv" usergender="<?= $row['gender'] ?>" userage="<?= $row['age'] ?>" imagediv="<?= $row['employeeid'] ?>" id="img_<?= $row['employeeid'] ?>">
                            </div>
                        </td>
                        <td align="center">
                            <ul style="list-style-type: none;font-weight: bold;padding-inline-start: 15px !important;">
                                <li><?=$row['fullname']?></li>
                                <li><?= $row['deptid'] ?></li>
                            </ul>
                        </td>
                    </tr>
                <?php } else if($label == "Late"){ ?>
                    <tr>
                        <td align="center">
                            <div class="imageDiv" usergender="<?= $row['gender'] ?>" userage="<?= $row['age'] ?>" imagediv="<?= $row['employeeid'] ?>" id="img_<?= $row['employeeid'] ?>">
                                
                            </div>
                        </td>
                        <td align="center">
                            <ul style="list-style-type: none;font-weight: bold;padding-inline-start: 15px !important;">
                                <li><?=$row['fullname']?></li>
                                <li><?= $row['deptid'] ?></li>
                                <li>Time In: <?= $row['timein'] ?></li>
                                <li>Time Out: <?= ($row['timeout']) ? date("g:i A", strtotime($row['timeout'])) : " -- " ?></li>
                            </ul>
                        </td>
                    </tr>
                <?php }elseif($label == "Leave/OB"){ ?>
                    <tr>
                        <td align="center">
                            <div class="imageDiv" usergender="<?= $row['gender'] ?>" userage="<?= $row['age'] ?>" imagediv="<?= $row['employeeid'] ?>" id="img_<?= $row['employeeid'] ?>">
                                
                            </div>
                        </td>
                        <td align="center">
                            <ul style="list-style-type: none;font-weight: bold;padding-inline-start: 15px !important;">
                                <li><?=$row['fullname']?></li>
                                <li><?= $row['deptid'] ?></li>
                            </ul>
                        </td>
                    </tr>
                 <?php }elseif($label == "On Holiday"){ ?>
                    <tr>
                        <td align="center">
                            <div class="imageDiv" usergender="<?= $row['gender'] ?>" userage="<?= $row['age'] ?>" imagediv="<?= $row['employeeid'] ?>" id="img_<?= $row['employeeid'] ?>">
                                
                            </div>
                        </td>
                        <td align="center">
                            <ul style="list-style-type: none;font-weight: bold;padding-inline-start: 15px !important;">
                                <li><?=$row['fullname']?></li>
                                <li><?= $row['deptid'] ?></li>
                            </ul>
                        </td>
                    </tr>
                <?php } ?>
            <?php endforeach ?>  
        </tbody>
    </table>
<?php }else{ ?>
    <div>
        <center><label style="padding: 5%;">No date presented.</label></center>
    </div>
    <hr>
<?php } ?>
<script type="text/javascript">
$(document).ready(function(){
    $("#head").hide();
    var table = $('#detailedAttendance').DataTable({
        responsive: true,
        bFilter: false, 
        bInfo: false,
        bLengthChange: false,
        "language": {
          "emptyTable": "No Data!"
        },
        "iDisplayLength": 8,
        "drawCallback": function(settings) {
           loadImage();
           $("#detailedAttendance_paginate").parent().removeClass("col-sm-7").addClass("col-sm-12");
           $("#detailedAttendance_paginate").parent().prev().remove();
           $("#detailedAttendance_paginate").css("text-align", "center");
        }
    });
});

function loadImage(){
    $(".imageDiv").each(function(){
        var id = $(this).attr("imagediv");
        var gender = $(this).attr("usergender");
        var age = $(this).attr("userage");
        $.ajax({
            type: "POST",
            url: "<?= site_url('employee_/loadImage') ?>",
            data: {id:id, gender:gender, age:age},
            success:function(res){
                $("#img_"+id).html(res);
            }
        })
    })
}
</script>