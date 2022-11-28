<style type="text/css">
  .card .card-title{
    font-weight: 600;
  }
</style>
<?php 
$num = 1;
 ?>
 <div class="row">
 <h4 class="left"><span class="black-text">Latest Jobs in Poveda</span></h4>
  <ul class="pagination left" style="margin-top: 1.5%;">
    <li class="waves-effect chevron_left">
        <a href="#!"><i class="material-icons">chevron_left</i></a>
    </li>
    <?php for($k = 0;$k < $total_page; $k++){ ?>
        <?php if ($num == $page_no){ ?>
            <li class="active yellow darken-2 pageNum" page="<?= $num ?>"><a href="#!" class="black-text" ><?= $num ?></a></li>
        <?php }else{ ?>
            <li class="waves-effect black-text pageNum" page="<?= $num ?>"><a href="#!" class="black-text" ><?= $num ?></a></li>
        <?php } ?>
        <?php $num++ ?>
    <?php } ?>
    <li class="waves-effect chevron_right">
        <a href="#!" ><i class="material-icons">chevron_right</i></a>
    </li>
</ul>
</div>
<div class="row">
<?php foreach ($record as $key => $value): ?>
    <?php 
    $fileExtension = explode(".", $value->filename);
    $count = count($record);
    $offsetDiv = "";
    if ($count == 1) {
        $offsetDiv = "offset-m4";
    }elseif($count == 2){
        $offsetDiv = "offset-m2";
    }elseif($count == 3){
        $offsetDiv = "offset-m1";
    }
    ?>
    <div class="col s12 m3 <?= $offsetDiv ?>">
        <div class="card">
        <div class="card-image waves-effect waves-block waves-light">
          <img src="data:image/<?= $fileExtension[0] ?>;base64, <?= $value->file ?>" alt="" />
        </div>
        <div class="card-content">
                  <span class="card-title activator grey-text text-darken-4" style="font-weight: 500;font-weight: bold"><?= $value->title ?><i class="material-icons right">more_vert</i></span>
                  <a class="waves-effect waves-light btn yellow darken-2 black-text applynow" tag='<?= $value->title ?>' course='<?= $this->extensions->getCourseDescription($value->course)?>' subject='<?= $this->extensions->getSubjectDescription($value->subject) ?>' site='<?=site_url('applicant/validate')?>' style="font-weight: bold"><i class="material-icons left">assignment_ind</i>Apply</a>
                  <a class="waves-effect waves-light btn teal white-text viewInfo" tagKey='<?= $value->positionid ?>' style="font-weight: bold"><i class="material-icons left">assignment</i>View Details</a>
        </div>
        <div class="card-reveal">
          <span class="card-title grey-text text-darken-4"><?= $value->title ?><i class="material-icons right">close</i></span>
          <p><?= $value->description ?></p>
        </div>
      </div>
    </div>
<?php endforeach ?>
</div>

<script>
var toks = hex_sha512(" ");

$(document).ready(function(){
    $("#jobtable_filter").parent().parent().hide();
});

  $("#searchJobBtn").on("click", function() {
    JobTable($("#searchJob").val(), 1);
  });

  $("#clearSearchbtn").on("click", function() {
    $("#searchJob").val('');
    JobTable('', 1);
  });

  $(".pageNum").click(function(){
      JobTable($("#searchJob").val(), $(this).attr('page'));
  });

  $(".chevron_right").click(function(){
      var num = Number($(".active").attr('page')) + 1;
      if (num == "<?= $num ?>") {
        $(this).addClass('disabled');
      }else{
        JobTable($("#searchJob").val(), num);
      }
  });

  $(".chevron_left").click(function(){
      var num = Number($(".active").attr('page')) - 1;
      if (num != 0) {
        JobTable($("#searchJob").val(), num);
      }else{
        $(this).addClass('disabled');
      }
  });

  $(".applynow").click(function(){
    $('#modal1').modal('open');
        var selected = $(this).attr("tag");
        var course = $(this).attr("course");
        var subject = $(this).attr("subject");
        var isteaching = "";
        if(course != "--" && subject != "--") isteaching = true;
        $.ajax({
            type: 'post',
            data: {
                selected: GibberishAES.enc( selected, toks),
                course: GibberishAES.enc(course , toks),
                subject: GibberishAES.enc( subject, toks),
                isteaching: GibberishAES.enc( isteaching, toks),
                toks:toks
            },
            url : "<?= site_url('applicant/applicantSignupForm') ?>",
            success:function(response){
                $("#applicantContent").html(response);
                
            }
        });
      });

  $(".viewInfo").click(function(){
    var tagKey = $(this).attr("tagKey");
    if(!tagKey){
        alert("No details available.");
        return;
    }

    $.ajax({
        type: "POST",
        url: "<?= site_url('applicant/loadFile') ?>",
        data: {id:tagKey},
        success:function(file){
            objectURL = URL.createObjectURL(b64toBlob(file, 'application/pdf')) + '#toolbar=0&navpanes=0&scrollbar=0';
            window.open(objectURL);
        }
    })
  });

  $("#login_here").click(function(){
    $.ajax({
        type: "POST",
        url: "<?= site_url('applicant/loginForm') ?>",
        data: {positionid:$("#positionid").val()},
        success:function(msg){
            if(msg){
              $("#applicantContent").html(msg);
              $("#login_here_div, #logsubmit").hide();
              $("#logsubmitexisting").show();
            }
        }
    })
  })


  function b64toBlob(b64Data, contentType) {
        var byteCharacters = atob(b64Data)
        var byteArrays = []
        for (let offset = 0; offset < byteCharacters.length; offset += 512) {
            var slice = byteCharacters.slice(offset, offset + 512),
                byteNumbers = new Array(slice.length)
            for (let i = 0; i < slice.length; i++) {
                byteNumbers[i] = slice.charCodeAt(i)
            }
            var byteArray = new Uint8Array(byteNumbers)

            byteArrays.push(byteArray)
        }

        var blob = new Blob(byteArrays, { type: contentType })
        return blob
    }
</script>