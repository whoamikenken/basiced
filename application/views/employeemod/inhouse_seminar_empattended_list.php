<thead style="background-color: #0072c6;">
    <tr>
        <th class="align_left span1">Employee ID</th>
        <th class="align_left span1">Fullname</th>
        <th class="align_left span1">Log In</th>
        <th class="align_left span1">Log Out</th>
    </tr>
</thead>   
<tbody id="attendees_list">
    <?php foreach ($attendedEmployee as $seminarid => $userid):
            foreach ($userid as $k => $v):
                $login = $logout = '';
                foreach ($v as $value):
                    $fullname = Globals::_e($value['fullname']);
                    $timein = date("h:i:s A", strtotime($value['localtimein']));
                    // $timeout = date("h:i:s A", strtotime($value['timeout']));
                    if($value['log_type'] == 'IN') $login = $timein;
                    else $logout = $timein;
                endforeach;
                ?>
                <tr>
                    <td><?= $k?></td>
                    <td><?= $fullname ?></td>
                    <td><?= $login ?></td>
                    <td><?= $logout ?></td> 
                </tr>
    <?php 
            endforeach;
                endforeach; ?>

</tbody>

<input type="hidden" id="last_query" value="<?= $lastQuery ?>">
<script>
    var adminnotif = 0;
    attendeesAdminNotifCount();
    $("input[name='mar']").change(function(){
        var notifcount = "";
        var id = $(this).attr("idkey");
        $.ajax({
            url: "<?= site_url('seminar_/attendeesMarkread') ?>",
            type: "POST",
            data: {id, id},
            success:function(response){
                $("tr[id='"+ id +"']").find(".mar").attr("disabled", true);
                if("<?=$this->session->userdata('usertype')?>" == "EMPLOYEE"){
                    notifcount = $("#sidebar ul li.active>a, a[aria-expanded='true']").find(".notifdiv").find(".notifcount").find("b").text();
                    notifcount -= 1;
                    if(notifcount <= 0) $("#sidebar ul li.active>a, a[aria-expanded='true']").find(".notifdiv").hide();
                }else{
                    adminnotif = adminnotif;
                    adminnotif -= 1;
                    if(adminnotif <= 0) $("#sidebar ul li.active>a, a[aria-expanded='true']").find(".notifdiv").hide();
                }
            }
        });
    });

    $(".seminarDetails").click(function(){
        loadSeminarDetails($(this).attr("seminarid"));
    })

    function attendeesAdminNotifCount(){
        $.ajax({
            url: "<?= site_url('seminar_/attendeesAdminNotifCount') ?>",
            async: false,
            success:function(response){
                adminnotif = response;
            }
        });
        return adminnotif;
    }

    function loadSeminarDetails(seminarid=''){
        $("#modal-view").find("h3[tag='title']").text("Seminar Details");
        $("#modal-view").find("#button_save_modal").css("display", "none");
        $.ajax({
            url: "<?= site_url('seminar_/seminar_details') ?>",
            type: "POST",
            data: {seminarid:seminarid},
            success:function(res){
                $("#modal-view").find("div[tag='display']").html(res);
            }
        })
    }

    $("#modalclose").click(function(){
        unhidesave();
    })

    document.onkeydown = function(evt) {
        evt = evt || window.event;
        if (evt.keyCode == 27) {
            unhidesave();
        }
    };

    function unhidesave(){
        setTimeout(function(){
            $("#modal-view").find("#button_save_modal").css("display", "unset");
        }, 500)
    }
</script>