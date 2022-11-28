<?php

/**
 * @author Justin
 * @2016
 */
 $readonly = false;
 $description = "";
 $credits = "";
 $up = $upt = $boff = $findir = $pres = "";
 $q = $w = $e = $r = $t = $y = $c = $cp = "";
 if($code){
     $sql = $this->db->query("select * from code_request_form WHERE code_request='{$code}'");
     if($sql->num_rows()>0){
        $description = $sql->row(0)->description;
        $credits = $sql->row(0)->credits;
        $up = $sql->row(0)->univphy;
        $upt = $sql->row(0)->univphyt;
        $boff = $sql->row(0)->budgetoff;
        $findir = $sql->row(0)->financedir;
        $pres = $sql->row(0)->president;
        $q = $sql->row()->dhseq;
        $w = $sql->row()->hhseq;
        $c = $sql->row()->chseq;
        $cp = $sql->row()->cpseq;
        $e = $sql->row()->upseq;
        $r = $sql->row()->boseq;
        $t = $sql->row()->fdseq;
        $y = $sql->row()->pseq;
     }
     $readonly = " readonly";
 }
 $cdate = date("Y-m-d");
?>
<div class="container" style="width: 80%;">
  <form id="form_leave" class="form-horizontal">
    <input type="hidden" name="isedit" value="<?=($code) ? '1' : '0'?>">
    <div class="form-group">
        <label class="field_name align_right">Code</label>
        <div class="field">
            <input class="form-control input-sm" id="mh_code" name="mh_code" type="text" value="<?=$code?>" <?=$readonly?>/>
        </div>
    </div>
    <div class="form-group">
        <label class="field_name align_right">Description</label>
        <div class="field">
            <input class="form-control" id="mh_description" name="mh_description" type="text" value="<?=$description?>"/>
        </div>
    </div>
    <div class="form-group" hidden>
        <label class="field_name align_right">Leave Credits</label>
        <div class="field">
            <input class="form-control" id="mh_credits" name="mh_credits" type="number" value="<?=$credits?>"/>
        </div>
    </div>
    <div class="form-group">
        <label class="field_name align_right"><strong>Approved By:</strong></label>
    </div>
    <div class="form-group">
        <label class="field_name align_right">Area coordinator / Immediate Supervisor</label>
        <div class="field">
            <input type="text" class="form-control seqinput" id="chseq" size="5" value="<?=$c?>" maxlength="5" />
        </div>
    </div>
    <div class="form-group">
        <label class="field_name align_right">Department Head / Vice Principal</label>
        <div class="field">
            <input type="text" class="form-control seqinput" id="dhseq" size="5" value="<?=$q?>" maxlength="5" <?=$code=='OBS'?$readonly:""?>/>
        </div>
    </div>
    <div class="form-group">
        <label class="field_name align_right">HR Director</label>
        <div class="field">
            <input type="text" class="form-control seqinput" id="hhseq" size="5" value="<?=$w?>" maxlength="5" />
        </div>
    </div>
   <!--  <div class="form-group">
        <label class="field_name align_right">Campus Principal</label>
        <div class="field">
            <input type="text" class="form-control seqinput" id="cpseq" size="5" value="<?=$cp?>" maxlength="5" />
        </div>
    </div> -->
    <!-- <div class="form-group">
        <label class="field_name align_right">Univ. Physician</label>
        <div class="field">
            <input type="text" class="form-control seqinput" id="upseq" size="5" value="<?=$e?>" maxlength="5" /><br>
            <select class="chosen" name="mh_univphy" id="mh_univphy" ><?=$this->extras->showEmployee($up);?></select>
        </div>
        <div class="field" style="margin-top: 5px;">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <select class="chosen" name="mh_univphytwo" id="mh_univphytwo" ><?=$this->extras->showEmployee($upt);?></select>
        </div>
    </div>
    <div class="form-group">
        <label class="field_name align_right">Budget Officer</label>
        <div class="field">
            <input type="text" class="form-control seqinput" id="boseq" size="5" value="<?=$r?>" maxlength="5" /><br>
            <select class="chosen" name="mh_boff" id="mh_boff"><?=$this->extras->showEmployee($boff);?></select>
        </div>
    </div>
    <div class="form-group">
        <label class="field_name align_right">Finance Director</label>
        <div class="field">
            <input type="text" class="form-control seqinput" id="fdseq" size="5" value="<?=$t?>" maxlength="5" /><br>
            <select class="chosen" name="mh_findir" id="mh_findir"><?=$this->extras->showEmployee($findir);?></select>
        </div>
    </div>
    <div class="form-group">
        <label class="field_name align_right">President</label>
        <div class="field">
            <input type="text" class="form-control seqinput" id="pseq" size="5" value="<?=$y?>" maxlength="5" /><br>
            <select class="chosen" name="mh_president" id="mh_president"><?=$this->extras->showEmployee($pres);?></select>
        </div>
    </div> -->
  </form>
</div>
<script>
var toks = hex_sha512(" ");
$(".seqinput").blur(function(){
    $(this).removeClass("seqinput");
    var curr_count = 0;
    var input_seq = parseInt($(this).val());
    $(".seqinput").each(function(){
        var seq_count = parseInt($(this).val());
        if(seq_count){
            if(parseInt(seq_count) > curr_count) curr_count = parseInt(seq_count);
        }
    });
    $(this).addClass("seqinput");
    if(curr_count+1 > 1){
        if(curr_count + 1 != input_seq){
            $(this).val("");
        }
    }
    if(curr_count == 0 && $(this).val() != 1){
        $(this).val("");
    }
});

$("#dhseq,#hhseq,#chseq,#cpseq,#dpseq,#upseq,#boseq,#fdseq,#pseq").on("blur",function(){

    if ($(this).val() == "") {
        $(this).val("0");
    }
});

function seqChecker(){
  var field = ["dhseq", "hhseq", "chseq", "cpseq", "upseq", "boseq", "fdseq", "pseq"];
  var seq = [];
  var isSeqCorrect = true;

  for(i in field){
    seq[i] = $("#"+ field[i]).val();
  }

  var maxSeq = Math.max(...seq);
  for(i = 1; i <= maxSeq; i++){
    if(seq.indexOf(""+i) == -1){
      isSeqCorrect = false;
      break;
    }

    var count_seq = 0;
    for(k in seq){
      if(count_seq >= 2){
        isSeqCorrect = false;
        break;
      }

      if(i == seq[k]) count_seq += 1;
    }
  }

  return isSeqCorrect;
}
    $("#button_save_modal").unbind("click").click(function(){
        var isSeqCorrect = seqChecker();
        if(!isSeqCorrect){
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: "Please correct the sequence of the approver.",
                showConfirmButton: true,
                timer: 2000
            });
            return;
        }

        $.ajax({
            url      :   "<?=site_url("maintenance_/leavedatevalidity")?>",
            type     :   "POST",
            data     :   {
                dfrom: GibberishAES.enc($("input[name='datefrom']").val(), toks),
                dto: GibberishAES.enc($("input[name='dateto']").val(), toks),
                ltype: GibberishAES.enc($("#mh_type").val(), toks),
                toks:toks
            },
            success  :   function(msg){
                // if(msg == "0"){
                    if($("#form_leave").valid()){
                        $.ajax({
                            url:"<?=site_url("maintenance_/save_other_request")?>",
                            type:"POST",
                            data:{
                                // isedit: $("input[name='isedit']").val(),
                                // code: $("input[name='mh_code']").val(),
                                // description: $("input[name='mh_description']").val(),
                                // credits: $("input[name='mh_credits']").val(),
                                // /*
                                // dfrom: $("input[name='datefrom']").val(),
                                // dto: $("input[name='dateto']").val(),
                                // ltype: $("#mh_type").val()
                                // */
                                // dhseq: $("#dhseq").val(),
                                // hhseq: $("#hhseq").val(),
                                // chseq: $("#chseq").val(),
                                // cpseq: $("#cpseq").val(),
                                // upseq: $("#upseq").val(),
                                // boseq: $("#boseq").val(),
                                // fdseq: $("#fdseq").val(),
                                // pseq : $("#pseq").val(),
                                // bo   : $("#mh_boff").val(),
                                // up   : $("#mh_univphy").val(),
                                // upt  : $("#mh_univphytwo").val(),
                                // fd   : $("#mh_findir").val(),
                                // pres : $("#mh_president").val()
                                isedit:  GibberishAES.enc($("input[name='isedit']").val() , toks),
                                code:  GibberishAES.enc($("input[name='mh_code']").val() , toks),
                                description: GibberishAES.enc($("input[name='mh_description']").val()  , toks),
                                credits:  GibberishAES.enc( $("input[name='mh_credits']").val(), toks),
                                   
                                dhseq:  GibberishAES.enc($("#dhseq").val() , toks),
                                hhseq:  GibberishAES.enc($("#hhseq").val() , toks),
                                chseq:  GibberishAES.enc( $("#chseq").val(), toks),
                                cpseq:  GibberishAES.enc($("#cpseq").val() , toks),
                                upseq:  GibberishAES.enc($("#upseq").val() , toks),
                                boseq:  GibberishAES.enc($("#boseq").val() , toks),
                                fdseq:  GibberishAES.enc($("#fdseq").val() , toks),
                                pseq :  GibberishAES.enc($("#pseq").val() , toks),
                                bo   : GibberishAES.enc( $("#mh_boff").val() , toks),
                                up   :  GibberishAES.enc($("#mh_univphy").val() , toks),
                                upt  :  GibberishAES.enc($("#mh_univphytwo").val() , toks),
                                fd   :  GibberishAES.enc($("#mh_findir").val() , toks),
                                pres :  GibberishAES.enc($("#mh_president").val() , toks),
                                toks:toks
                            },
                            dataType: "json",
                            success: function(response){
                                if(response.code == 1){
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success!',
                                        text: response.msg,
                                        showConfirmButton: true,
                                        timer: 2000
                                    });
                                    $("#modalclose").click();
                                    $(".nav-tabs > li.active > a, .nav-tabs > li.active > a:hover, .nav-tabs > li.active > a:focus").click(); 
                                }else{
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Warning!',
                                        text: response.msg,
                                        showConfirmButton: true,
                                        timer: 2000
                                    });
                                }
                            }
                        });
                    // }else {
                    //     $validator.focusInvalid();
                    //     return false;
                    // }
                }else{
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning!',
                        text: 'The date you set has a conflict.',
                        showConfirmButton: true,
                        timer: 1000
                    })
                    return false;
                }
            }
        });
    });

$(".chosen").chosen();
</script>