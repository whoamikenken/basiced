</div>
<!-- Start Displaying here -->
<?
$this->load->view("includes/modalview");
$this->load->view("includes/dtr-modal");
?>

<?if($upload_file){?>
<!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
<script src="<?=base_url()?>jsbstrap/library/file_upload/vendor/jquery.ui.widget.js"></script>
<!-- The Templates plugin is included to render the upload/download listings -->
<script src="<?=base_url()?>jsbstrap/library/file_upload/blueimp/tmpl.min.js"></script>
<!-- The Load Image plugin is included for the preview images and image resizing functionality -->
<script src="<?=base_url()?>jsbstrap/library/file_upload/blueimp/load-image.min.js"></script>
<!-- The Canvas to Blob plugin is included for image resizing functionality -->
<script src="<?=base_url()?>jsbstrap/library/file_upload/blueimp/canvas-to-blob.min.js"></script>
<!-- Bootstrap JS is not required, but included for the responsive demo navigation -->
<script src="<?=base_url()?>jsbstrap/library/file_upload/blueimp/bootstrap.min.js"></script>
<!-- blueimp Gallery script -->
<script src="<?=base_url()?>jsbstrap/library/file_upload/blueimp-gallery.min.js"></script>
<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
<script src="<?=base_url()?>jsbstrap/library/file_upload/jquery.iframe-transport.js"></script>
<!-- The basic File Upload plugin -->
<script src="<?=base_url()?>jsbstrap/library/file_upload/jquery.fileupload.js"></script>
<!-- The File Upload processing plugin -->
<script src="<?=base_url()?>jsbstrap/library/file_upload/jquery.fileupload-process.js"></script>
<!-- The File Upload image preview & resize plugin -->
<script src="<?=base_url()?>jsbstrap/library/file_upload/jquery.fileupload-image.js"></script>
<!-- The File Upload audio preview plugin -->
<script src="<?=base_url()?>jsbstrap/library/file_upload/jquery.fileupload-audio.js"></script>
<!-- The File Upload video preview plugin -->
<script src="<?=base_url()?>jsbstrap/library/file_upload/jquery.fileupload-video.js"></script>
<!-- The File Upload validation plugin -->
<script src="<?=base_url()?>jsbstrap/library/file_upload/jquery.fileupload-validate.js"></script>
<!-- The File Upload user interface plugin -->
<script src="<?=base_url()?>jsbstrap/library/file_upload/jquery.fileupload-ui.js"></script>
<!-- The main application script -->
<script src="<?=base_url()?>jsbstrap/library/file_upload/app.js"></script>
<script src="<?=base_url()?>jsbstrap/library/file_upload/main.js"></script>
<?}?>
<!-- <script src="<?=base_url()?>jsbstrap/library/jquery.backstretch.min.js"></script> -->

<!-- The javascript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="<?=base_url()?>js/sweetalert.js"></script>
<script src="<?=base_url()?>jsbstrap/library/bootstrap-modal.js"></script>
<script src="<?=base_url()?>jsbstrap/library/bootstrap-modalmanager.js"></script>
<script src="<?=base_url();?>js/common.js" type="text/javascript" charset="utf-8"></script>
<script>

   var toks = hex_sha512(" ");

   
   $("table[class='submenus'] td").click(function(){
      var site = $(this).attr("site");
      var root = $(this).attr("root");
      var menuid = $(this).attr("menuid");
      var titlebar = $(this).text();

      $("#mainform").attr("action","<?=site_url("main/site")?>");
      $("input[name='sitename']").val(site);
      $("input[name='rootid']").val(root);
      $("input[name='menuid']").val(menuid);
      $("input[name='titlebar']").val(titlebar);
      if(site) $("#mainform").submit();
   });

   function openWindowWithPost(url, data) {
       var form = document.createElement("form");
       form.target = "_blank";
       form.method = "POST";
       form.action = url;
       form.style.display = "none";

       for (var key in data) {
           var input = document.createElement("input");
           input.type = "hidden";
           input.name = key;
           input.value = data[key];
           form.appendChild(input);
       }

       document.body.appendChild(form);
       form.submit();
       document.body.removeChild(form);
   }

   function viewloader(form_data){
      $.ajax({
         url: "<?=site_url('main/siteportion')?>",
         type: "POST",
         data: form_data,
         success: function(msg){
            $("#contentview").html(msg); 
         }
      });   
   }

   

	function validateForm(form){
		var iscontinue = true;
		form.find("input.isrequired, textarea.isrequired").each(function(){
			if(!$(this).val()){
				$(this).css("border", "1px solid red").focus();
				$(this).siblings(".req-mark").show();
				iscontinue = false;
				return false;
			}else{
				iscontinue = true;
				$(this).siblings(".req-mark").hide();
				$(this).css("border", "1px solid #ccc");
			}
		});
		if(!iscontinue) return;
			form.find(".chosen.isrequired, .chosen-select.isrequired").each(function(){
			if(!$(this).val()){
				$(this).siblings(".chosen-container").css("border", "1px solid red");
				$(this).siblings(".req-mark").show();
				iscontinue = false;
				return false;
			}else{
            iscontinue = true;
            $(this).siblings(".req-mark").hide();
            $(this).css("border", "1px solid #ccc");
         } 
		});

		return iscontinue;
	}

   $('.chosen-select').chosen();
</script>

<?php if(!($this->session->sess_read() && $this->session->userdata("logged_in"))){?>

          <script>
            $('#fusername').attr("value","");  
            $('#fpassword').attr("value","");
            $('#fusername').focus(); 
          </script>
<?}?>

</body>
</html>