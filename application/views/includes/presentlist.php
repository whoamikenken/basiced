<?php if($att_list){ ?>
    <?php foreach($att_list as $row): ?>
        <div class="container-fluid">
            <div class="media">
                <div class="media-left">
                    <img src="<?= $row['user_img']?>" class="img-circle" style="width:50px;height: 70px;">
                </div>
                <div class="media-body">
                    <ul style="list-style-type: none;font-weight: bold;padding-inline-start: 15px !important;">
                        <li><?=$row['fullname']?></li>
                        <li><?= $row['deptid'] ?></li>
                        <li>Time In: <?= $row['timein'] ?></li>
                        <li>Time Out: <?= ($row['timeout']) ? date("g:i A", strtotime($row['timeout'])) : " -- " ?></li>
                    </ul>
                </div>
          </div>
          <hr>
      </div>
    <?php endforeach ?>  
<?php }else{ ?>
    <h5 style="text-align: center;"><i><b>No present employee yet.</b></i></h5>
<?php } ?>