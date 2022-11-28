<?php

/**
 * @author Justin
 * @copyright 2015
 */

?>
<div id="content"> <!-- Content start -->
<div class="widgets_area">
<div class="row">
    <div class="col-md-12">
        <div class="well blue">
            <div class="well-header">
                <h5>HR Documents</h5>
                <ul>
                    <li class="color_pick"><a href="#"><i class="glyphicon glyphicon-th"></i></a>
                        <ul>
                            <li><a class="blue set_color" href="#"></a></li>
                            <li><a class="light_blue set_color" href="#"></a></li>
                            <li><a class="grey set_color" href="#"></a></li>
                            <li><a class="pink set_color" href="#"></a></li>
                            <li><a class="red set_color" href="#"></a></li>
                            <li><a class="orange set_color" href="#"></a></li>
                            <li><a class="yellow set_color" href="#"></a></li>
                            <li><a class="green set_color" href="#"></a></li>
                            <li><a class="dark_green set_color" href="#"></a></li>
                            <li><a class="turq set_color" href="#"></a></li>
                            <li><a class="dark_turq set_color" href="#"></a></li>
                            <li><a class="purple set_color" href="#"></a></li>
                            <li><a class="violet set_color" href="#"></a></li>
                            <li><a class="dark_blue set_color" href="#"></a></li>
                            <li><a class="dark_red set_color" href="#"></a></li>
                            <li><a class="brown set_color" href="#"></a></li>
                            <li><a class="black set_color" href="#"></a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="well-content">
                <table class="table table-striped table-bordered table-hover datatable">
                    <thead>
                        <tr>
                            <th>Document</th>
                            <th>Download File</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?
                        foreach($this->extras->hrDocx() as $row){
                        ?>
                            <tr>
                                <td><?=$row->title?></td>
                                <!-- <td><img src="<?=site_url('forms/loadForm')?>?form=dlfile&fiename=<?=$row->title?>" width="10%" height="10%" /></td> -->
                                <td><a href="<?=site_url('forms/loadForm')?>?form=dlfile&filename=<?=$row->title?>"><img src="<?=base_url()?>images/dlfile.png" alt="Documents" height="-15" width="100" /></a></td>
                                <!-- <td><a href="<?=site_url('forms/loadForm')?>?form=dlfile&filename=<?=$row->title?>"><i class="icon icon-download"></i><img src="" /></a></td> -->
                            </tr>
                        <?}?>
                    </tbody>
                </table>
            </div>
       </div>
    </div>
</div>
</div>
</div>
<script>
</script>