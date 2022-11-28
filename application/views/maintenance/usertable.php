<table class="table table-striped table-bordered table-hover" id="user_datatable">
    <thead style="background-color: #0072c6;">
      <tr>
        <th>Action</th>
        <th>User Name</th>
        <th>Full Name</th>
      </tr>
    </thead>   
    <tbody>
    	 <?php 
       if(count($records) > 0){
       foreach($records as $row): ?>
        <tr>
            <td class="align_center">
                <div class="btn-group">
                  <a class="btn btn-info user" href="#modal-view" tag="access_d" data-toggle="modal" userid="<?=$row['id']?>"><i class="glyphicon glyphicon-list-alt"></i></a>
                  <a class="btn btn-info user" href="#modal-view" tag="access_m" data-toggle="modal" userid="<?=$row['id']?>"><i class="glyphicon glyphicon-envelope"></i></a>
                  <a class="btn btn-info user" href="#modal-view" tag="edit_d" data-toggle="modal" userid="<?=$row['id']?>"><i class="glyphicon glyphicon-edit"></i></a>
                  <a class="btn btn-danger user" href="#" tag="delete_d" userid="<?=$row['id']?>"><i class="glyphicon glyphicon-trash"></i></a>
                  <a class="btn btn-primary user" href="#payslip-view" tag="edit_ppassword" data-toggle="modal" userid="<?=$row['id']?>" <?= ($row['type'] == "ADMIN")? "disabled='true'":"" ?>><i class="glyphicon glyphicon-edit"></i></a>
                  <a class="btn btn-danger user" href="#" tag="unlock" userid="<?=$row['username']?>" <?= ($row['locked'] >= 5)? "":"style='display:none'" ?>><i class="glyphicon glyphicon-lock"></i></a>
                </div>
            </td>
            <td><?=Globals::_e($row['username'])?></td>
            <td><?=Globals::_e($row['fullname'])?></td>
        </tr>
        <?php endforeach;
        } ?>
    </tbody>
</table>

<div class="modal fade" id="payslip-view" role="dialog" data-backdrop="static">
  <div class="modal-dialog modal-md">

    <div class="modal-content" >
      <div class="modal-header" >
        <div class="media">
          <div class="media-left">
            <img src="<?=base_url()?>images/school_logo.png" class="media-object" style="width:60px">
          </div>
          <div class="media-body" style=" font-weight: bold;padding-top: 10px; font-family: Avenir;">
            <h4 class="media-heading"  style="font-size: 18px !important"><b>Pinnacle Technologies Inc.</b></h4>
            <p style="font-family:Avenir; margin-top: -1%; font-size: 16px !important; font-weight: 300 !important">D`Great</p>
          </div>
        </div>
        <center><b><h3 tag="title" class="modal-title" style="font-family: Avenir;">Modal Header</h3></b></center>
      </div>
      <div class="modal-body">
        <div class="row">
              <div tag='display'>
              </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn btn-danger modalclose" id="modalclose">Close</button>
        <button type="button" class="btn btn-success" id='save_payslip'>Save</button>
        <div id='leaveloading' style="display: none;"><img class='pull-right' src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..</div>
      </div>
    </div>

  </div>
</div>    

<script>
  var toks = hex_sha512(" ");
if("<?=$this->session->userdata('canwrite')?>" == 0) $(".btn").css("pointer-events", "none");
else $(".btn").css("pointer-events", "");
$(function(){
var table = $('#user_datatable').DataTable({
    });
    new $.fn.dataTable.FixedHeader( table );

// codes here 
$("#user_datatable").on("click", ".user", function(e){
var uid = $(this).attr("userid");
if($(this).attr('tag') == "access_d"){
  dotoggleuseraccess("User Access",{uid: GibberishAES.enc(uid , toks), toks:toks});
}else if($(this).attr('tag') == "access_m"){
   hrmngmnt("HR Message",{uid: GibberishAES.enc(uid , toks), toks:toks});
}else if($(this).attr('tag') == "edit_d"){
   dotoggleuserinfo("Edit User",{job: GibberishAES.enc("edit" , toks),uid: GibberishAES.enc( uid, toks), toks:toks});
}else if($(this).attr('tag') == "edit_ppassword"){
  if ($(this).attr('disabled')) {
    e.stopPropagation();
    Swal.fire({
        icon: 'warning',
        title: 'Warning!',
        text: 'Employee User Only',
        showConfirmButton: true,
        timer: 1000
    })
  }else{
   dotoggleuserinfoppassword("Edit Payslip Password",{job:GibberishAES.enc( "editpayslipPassword" , toks),uid: GibberishAES.enc(uid , toks), toks:toks});
  }
}else if($(this).attr('tag') == "delete_d"){
        const swalWithBootstrapButtons = Swal.mixin({
          customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
          },
          buttonsStyling: false
        })

        swalWithBootstrapButtons.fire({
          title: 'Are you sure?',
          text: "You won't be able to revert this!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, proceed!',
          cancelButtonText: 'No, cancel!',
          reverseButtons: true
        }).then((result) => {
          if (result.value) {
            var uid = $(this).attr("userid");
             $.ajax({
                 url:"<?=site_url("maintenance_/saveuser")?>",
                 data: {uid: GibberishAES.enc(uid , toks),job: GibberishAES.enc("delete" , toks), toks:toks},
                 type: "POST",
                 success: function(msg){
                  Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'User has been deleted successfully.',
                        showConfirmButton: true,
                        timer: 1000
                  })
                  setTimeout(function() {
                    user_setup();
                  }, 1500); 
                 }
             });
          } else if (
            result.dismiss === Swal.DismissReason.cancel
          ) {
            swalWithBootstrapButtons.fire(
              'Cancelled',
              'Application is safe.',
              'error'
            )
          }
        })
  }else if($(this).attr('tag') == "unlock"){
        const swalWithBootstrapButtons = Swal.mixin({
          customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
          },
          buttonsStyling: false
        })

        swalWithBootstrapButtons.fire({
          title: 'Account Locked',
          text: "Are you sure you want to unlock account?",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes',
          cancelButtonText: 'No',
          reverseButtons: true
        }).then((result) => {
          if (result.value) {
            var username = $(this).attr("userid");
            $.ajax({
                url: "<?=site_url("main/unlockAccount")?>",
                type: "POST",
                data: { username:  GibberishAES.enc(username , toks), key:  GibberishAES.enc( "", toks), toks:toks},
                success: function(response) {
                  if (response == "success") {
                    Swal.fire({
                        icon: 'success',
                        title: 'Unlocked!',
                        text: 'Account has been unlocked successfully.',
                        showConfirmButton: true,
                        timer: 2000
                    })
                    setTimeout(function() {
                      user_setup();
                    }, 2500); 
                  }
                }
            });
          } else if (
            result.dismiss === Swal.DismissReason.cancel
          ) {
            swalWithBootstrapButtons.fire(
              'Cancelled',
              'Account is still locked.',
              'error'
            )
          }
        })
  }
});
    
    $("#adduser").click(function(){  
       dotoggleuserinfo("New User",{job: GibberishAES.enc("new", toks), toks:toks});
    });
    function dotoggleuserinfo(title,data){
       $("#modal-view").find("h3[tag='title']").html(title); 
       $("#modal-view").find("div[tag='display']").html("Loading, please wait...");
       $("#button_save_modal").text("Save");
       $.ajax({
           url:"<?=site_url("maintenance_/addnewuser")?>",
           data: data,
           type: "POST",
           success: function(msg){
              $("#modal-view").find("div[tag='display']").html(msg);
           }
       }); 
    }
     function dotoggleuserinfoppassword(title,data){
       $("#payslip-view").find("h3[tag='title']").html(title); 
       $("#payslip-view").find("div[tag='display']").html("Loading, please wait...");
       $.ajax({
           url:"<?=site_url("maintenance_/editpayslipPassword")?>",
           data: data,
           type: "POST",
           success: function(msg){
              $("#payslip-view").find("div[tag='display']").html(msg);
           }
       }); 
    }
    function dotoggleuseraccess(title,data){
       $("#modal-view").find("h3[tag='title']").html(title); 
       $(".modal-dialog").removeClass("modal-md").addClass("modal-lg");
       $("#button_save_modal").text("Save");
       $.ajax({
           url:"<?=site_url("maintenance_/useraccess")?>",
           data: data,
           type: "POST",
           success: function(msg){
              $("#modal-view").find("div[tag='display']").html(msg);
           }
       }); 
    }
    function hrmngmnt(title,data){
       $("#modal-view").find("h3[tag='title']").html(title); 
       $("#modal-view").find("div[tag='display']").html("Loading, please wait...");
       $("#button_save_modal").text("Save");
       $.ajax({
           url:"<?=site_url("maintenance_/hrmngmnt")?>",
           data: data,
           type: "POST",
           success: function(msg){
              $("#modal-view").find("div[tag='display']").html(msg);
           }
       }); 
    }
});



</script>