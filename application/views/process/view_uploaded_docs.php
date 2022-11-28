<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <div class="media">
                    <div class="media-left">
                        <img src="<?=base_url()?>images/school_logo.png" class="media-object" style="width:60px">
                    </div>
                    <div class="media-body" style=" font-weight: bold;padding-top: 10px; font-family: Avenir;">
                        <h4 class="media-heading"  style="font-size: 18px !important"><b>Pinnacle Technologies Inc.</b></h4>
                        <p style="font-family:Avenir; margin-top: -1%; font-size: 16px !important; font-weight: 300 !important">D`Great</p>
                    </div>
                </div>
            <center><h3 class="modal-title"><?= $description ?></h3></center>
        </div>
        
            <div class="modal-body" style="padding: 0px;">
                <div class="col-md-12" style="align-content: center; margin-top: 5%">
                    <img src="<?= $imgpath ?>" onerror="this.onerror=null; this.style.display = 'none'" style="width: 100%; height: auto; ">
                    <?php 
                        $images = array('jpeg', 'png', 'jpg', 'tiff', 'gif', 'eps', 'raw');
                        if (!in_array($fileExtension, $images)):
                    ?>
                        <a href="<?= $imgpath ?>" style="font-size: 15px; margin-left: 32%;color: blue;text-decoration: underline;">Click here to download file.</a>
                    <?php endif; ?>
                </div>
            </div>
        <div class="modal-footer" style="border-top: 0px;">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>