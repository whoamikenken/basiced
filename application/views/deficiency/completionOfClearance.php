<style>

.modal{
    left: 0;
    right: 0;
    margin: auto;
}
#clearanceHistory th{
    background: #018136;
    font-size: 14px;
    width: 10% !important;
     text-align: center;
     color:white;
}


</style>

<div class="modal-dialog modal-md">
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
            <center><b><h3 tag="title" class="modal-title">Completion of Employee Clearance</h3></b></center>
        </div>
        <div class="modal-body">
          <form>
            <input type="hidden" name="table_id" value="<?=$tbl_id?>">
                <div class="form-group">
                <label class="col-sm-4 control-label">Remarks</label>
                  <div class="col-sm-7">
                    <input type="text" name="remarks" class="form-control" value=""/>
                  </div>
                </div>
                <br><br>
                <div class="form-group">
                <label class="col-sm-4 control-label">Completion Date</label>
                <div class="col-sm-7">
                    <div class='input-group date comdate' data-date="" data-date-format="yyyy-mm-dd">
                        <input type='text' class="form-control" size="16" name="comdate" value="<?=date('Y-m-d')?>"/>
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>
              </div>
                <br><br>

          </form>
              
        </div>
        <div class="modal-footer">
            <div id="loading" hidden=""></div>
            <div id="saving">
                <button type="button" data-dismiss="modal" class="btn btn-danger" id="modalcloses">Close</button>
                <button type="button" class="btn btn-success" id='button_save_modals'>Save</button>
            </div>  
        </div>
    </div>
</div>
<script type="text/javascript">
    $(".date").datetimepicker({
        format: "YYYY-MM-DD"
    });

    $("#button_save_modals").click(function(){
      if(!$("input[name='comdate']").val()){
        Swal.fire({
              icon: 'warning',
              title: 'Warning!',
              text: 'Completion Date is required!',
              showConfirmButton: true,
              timer: 1000
          })
        return;
      }

      if(!$("input[name='remarks']").val()){
        Swal.fire({
              icon: 'warning',
              title: 'Warning!',
              text: 'Remarks is required!',
              showConfirmButton: true,
              timer: 1000
          })
        return;
      }

      $.ajax({
            url: "<?php echo  site_url('deficiency_/saveCompletionOfClearance') ?>",
            type: "POST",
            data: {
              tbl_id:  GibberishAES.enc( $("input[name='table_id']").val(), toks),
              comdate:  GibberishAES.enc($("input[name='comdate']").val() , toks),
              remarks:  GibberishAES.enc( $("input[name='remarks']").val(), toks),
              toks:toks
            },
            success:function(response){
              Swal.fire({
                  icon: 'success',
                  title: 'Success!',
                  text: 'Remarks is required!',
                  showConfirmButton: true,
                  timer: 1000
              })
              $('#modal-views').modal('hide');
              loadUnderEmployee();
            }
          });

    })
</script>