<?php if($bdaylist){ ?>
    <?php foreach($bdaylist as $row): ?>
        <div class="container-fluid">
            <div class="media">
                <div class="media-left">
                    <img src="<?= $row['user_img']?>" class="img-circle" style="width:70px;height: 70px;">
                </div>
                <div class="media-body">
                    <ul style="list-style-type: none;font-weight: bold;padding-inline-start: 15px !important;">
                        <li style="visibility: hidden;">...</li>
                        <li><?=$row['fullname']?></li>
                        <li><?= $row['deptid'] ?></li>
                    </ul>
                </div>
          </div>
          <hr>
      </div>
    <?php endforeach ?>  
<?php }else{ ?>
    <h5 style="text-align: center;"><i><b>No birthday celebrants today.</b></i></h5>
<?php } ?>