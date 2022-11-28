<?php
/**
 * @author justin (with e)
 * @copyright 2018
 */
?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <div class="media">
                <div class="media-left">
                    <img src="<?=base_url()?>images/school_logo.png" class="media-object" style="width:60px">
                </div>
                <div class="media-body" style="font-weight: bold;padding-top: 10px;">
                    <h4 class="media-heading">Pinnacle Technologies Inc.</h4>
                    <p>D`Great</p>
                </div>
            </div>
            <center><b><h3 tag="title" class="modal-title">Result</h3></b></center>
        </div>
        <div class="modal-body" id="leave_app_view">
        <?php if(isset($error)){ ?>
            <?if(count($error) == 0):?>
                <p class="good "><?=$success?></p>
            
            <?else:?>
                <p class="bad">Failed to Saved!..</p>            
                <?
                foreach($error as $code => $msg){
                ?>
                <p class="bad">* <?=$code?> - <?=$msg?></p>
                <?    
                }
                ?>
            <?endif;?>
        <?php }else{
            ?>
                <p class="good "><?=$success?></p>
            <?php
        } ?>
        </div>
        <div class="modal-footer">
            <div id="loading" hidden=""></div>
            <div id="saving">
                <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
