<div class="modal-dialog">

  <!-- Modal content-->
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
            <center><b><h3 tag="title" class="modal-title" style="font-family: 'Poppins';">Available Documents</h3></b></center>
        </div>
        <div class="modal-body">
            <table class="table table-striped table-bordered table-hover datatable" id="doc_rec">
                <thead>
                    <tr style="background-color: #0072c6;font-weight: bold; color:black;">
                        <td class="align_center" style="font-weight: bold;">Action</td>
                        <td class="align_center" style="font-weight: bold;">Code</td>
                        <td class="align_center">Document Description</td>
                    </tr>
                </thead>
                <tbody id="tbl_data">
                    
                </tbody>
            </table>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-success" id="add_rec">Add new</button>
        </div>
    </div>
</div>

<script src="<?=base_url()?>js/hr_setup/documents.js">