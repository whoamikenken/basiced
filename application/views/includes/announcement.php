<style type="text/css">
    hr {
    margin-top: 10px;
    margin-bottom: 13px;
}
</style>
<?php if($announce_list){ ?>
    <?php foreach($announce_list as $row): ?>
        <div class="container-fluid">
            <ul style="list-style-type: none;font-weight: bold;padding-inline-start: 15px !important;">
                <li>Activity : <?=Globals::_e($row['event'])?></li>
                <li>Location : <?= Globals::_e($row['venue']) ?></li>
                <li>Time : <?= Globals::_e($row['time']) ?></li>
                <li>Concerned Department : <?= $row['department'] ?> </li>
            </ul>
            <hr>
      </div>
    <?php endforeach ?> 
<?php } ?>

<?php if($holiday_list){ ?>
    <?php foreach($holiday_list as $row): ?>
        <div class="container-fluid">
            <ul style="list-style-type: none;font-weight: bold;padding-inline-start: 15px !important;">
                <li>Holiday : <?=Globals::_e($row['event'])?></li>
                <li>Location : <?= Globals::_e($row['venue']) ?></li>
                <li>Type : <?= Globals::_e($row['teaching_type']) ?></li>
                <li>Holiday Type : <?= Globals::_e($row['type']) ?></li>
                <li>Affected Office : <?= $row['department'] ?> </li>
                
            </ul>
            <hr>
      </div>
    <?php endforeach ?> 
<?php } ?>

<?php if(empty($announce_list) && empty($holiday_list)){ ?>
    <h5 style="text-align: center;"><i><b>No available activity today.</b></i></h5>
<?php } ?>


<script type="text/javascript">
    $('.annoucenentDetails').click(function(){
      var id = $(this).attr("code");
      $.ajax({
        url: "<?=site_url("announcements_/getDeptDetails")?>",
        type: 'POST',
        data : {id: GibberishAES.enc(id, toks), toks:toks},
        success: function(msg){
            swal.fire({title:"", html: msg});
        }
      }); 
    });

    $('.holidayDetails').click(function(){
      var id = $(this).attr("code");
      $.ajax({
        url: "<?=site_url("announcements_/getIncludedDepartmentInHoliday")?>",
        type: 'POST',
        data : {id: GibberishAES.enc(id, toks), toks:toks},
        success: function(msg){
            swal.fire({title:"", html: msg});
        }
      }); 
    });
</script>