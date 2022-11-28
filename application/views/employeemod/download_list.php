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
            <center><b><h3 tag="title" class="modal-title" style="font-family: 'Poppins';">Available Forms</h3></b></center>
        </div>
        <div class="modal-body">
			<table class="table table-striped table-bordered table-hover" id="downloadableTable">
				<thead>
					<tr style="background-color: #0072c6;">
						<th>Description</th>
						<th>Download</th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach($files as $file){
						foreach ($file as $key => $value) $file->$key = Globals::_e($value);
						?>
						<tr>
							<td><?php echo $file->description; ?></td>
							<td class="align_center"><a href="<?php echo base_url().'index.php/documents_/download/'.$file->id; ?>" class="btn btn-primary" style="border-radius: 50%;"><i class="icon-download"></i></a></td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>

<script type="text/javascript">
	$(document).ready(function () {
        var table = $('#downloadableTable').DataTable({
        });
        new $.fn.dataTable.FixedHeader(table);
    });
</script>