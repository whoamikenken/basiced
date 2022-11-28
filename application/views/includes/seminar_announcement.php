<style>

    #details_part{
        float:left;
        width:60%;
    }
    .modal-body{
        padding: 0px !important;
        margin-bottom: 0px !important;
    }

    .announce_text{
        font-size: 300%;
        line-height: 120%;
        color:white;
        font-weight: bold;
        font-family: avenir;
        text-shadow: -1px 0 black, 0 1px black, 1px 0 black, 0 -1px black;
    }

    .design_butt{
        border-radius: 30px/10px;
    }

</style>
<?php if(isset($record)): ?>
    <div id="details_part">
        <h5 style="font-size: 140%;font-weight: bold;margin-left: 5%;">Seminar Announcement</h5>
        <hr>
        <?php foreach($record as $records): ?>
        <div class="container-fluid">
            <ul>
                <li style="font-weight: bold;">Seminar Category  &nbsp; : &nbsp;<?=$seminarList[$records["category"]]?> </li>
            </ul>

            <ul>
                <li style="font-weight: bold;">Seminar Title &nbsp; : &nbsp;<?=$this->extensions->reportCodeDescription($records["workshop"])?></li>
            </ul>

            <ul>
                <li style="font-weight: bold;">Location  &nbsp; : &nbsp;<?=$records["location"]?></li>
            </ul>
            <br><hr>
        </div>
        <?php endforeach ?>
    </div>
<?php endif ?>