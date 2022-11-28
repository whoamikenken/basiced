<?php

/**
 * @author Max Consul
 * @copyright 2019
 */

$curr_date = date('Y-m-d');

?>
<style type="text/css">
        .cbox{
            -ms-transform: scale(1.5); /* IE */
            -moz-transform: scale(1.5); /* FF */
            -webkit-transform: scale(1.5); /* Safari and Chrome */
            -o-transform: scale(1.5); /* Opera */
        }
</style>

<div id="content">
    <div class="widgets_area">
        <div class="row">
            <div class="col-md-12">
                <div class="panel animated fadeIn delay-1s">
                   <div class="panel-heading"><h4><b>HR Forms</b></h4></div>
                   <div class="panel-body">
                       <div class="well-content" style="cursor: pointer;">        
                        <button id="apply_doc" type="button" class="btn btn-primary"><i class="icon-plus-sign"></i> &nbsp;New Request</button>
                        <button type="button" id="download_doc" class="btn btn-success" style="float:right;"> &nbsp;Downloadable Forms</button>
                        <br><br>
                        <div class="col-md-12">
                            <div class="col-md-6" style="padding-left: 0px;">
                                <div class="col-md-1" style="padding-right: 0px;padding-left: 0px;margin-top: 1%;">
                                <label style="display: inline;font-weight: 400;">Status: &nbsp;&nbsp;</label>
                                </div>
                                <div class="col-md-4">
                                    <select class="form-control" id="status">
                                        <option value="ALL">All Category</option>
                                        <option value="PENDING">Pending</option>
                                        <option value="APPROVED">Approved</option>
                                        <option value="DISAPPROVED">Disapproved</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <br><br>
                        <!-- <legend style="margin-left: 20px;">History</legend><br> -->
                        <div class="container-fluid" id="docapp_details">             <!-- table data -->
                           
                        </div> 
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="site_url" value="<?= site_url() ?>">
<div class="modal fade" id="m_doc_modal" role="dialog"> <!-- Add Setup --> </div>
<div class="modal fade" id="download_modal" role="dialog"> <!-- Add Setup --> </div>
<div class="modal fade" id="app_modal" role="dialog"> <!-- Apply employee doc --> </div>
  
<script src="<?=base_url()?>js/hr_setup/documents.js"></script>
<script>
    $("#download_doc").on("click", function(){
        $.ajax({
            url: $("#site_url").val() + '/documents_/loadDownloadDocumentModal',
            success:function(response){
                $("#download_modal").modal('toggle');
                $("#download_modal").html(response);
            }
        })
    });
</script>