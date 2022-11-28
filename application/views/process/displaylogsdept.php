<?php
    /**
    * @author Justin
    * Copyright 2016
    */
    $datedisplay = "";
    $from_date = $datesetfrom;
    $to_date = $datesetto;
    $empid = $fv;
    $edata = isset($edata)?$edata:"NEW";
    $deptid = $this->employee->getindividualdept($empid);
    $datedisplay = $this->time->createRangeToDisplay($from_date,$to_date);
    $teachingtype = $this->employee->getempteachingtype($empid);

    $data = array('from_date'=>$from_date,'to_date'=>$to_date,'datedisplay'=>$datedisplay,'empid'=>$empid,'edata'=>$edata,'deptid'=>$deptid,'teachingtype'=>$teachingtype);
?>

<style>
#indvtbl tr th,#indvtblnt tr th{
    background-color: #393737;
    color: #d2cf85;
}
</style>

<div class="modal fade" id="myModal1" data-backdrop="static"></div>

<?php
    // $qdate = $this->attcompute->displayDateRange($from_date, $to_date);
    ///< @Angelica pinaghiwalay ko na dalawang report na to
   /* if($teachingtype){  // Teaching
    $this->load->view('process/attendance_report_teaching',$data);
    }else{*/
    $this->load->view('process/attendance_report_nonteaching',$data);
    // }

?>



<script>
var toks = hex_sha512(" ");
$("#applysc").click(function(){  
    $.ajax({
        url      : "<?=site_url("employeemod_/fileconfig")?>",
        type     : "POST",
        data     : {folder:  GibberishAES.enc( "employeemod", toks), view:  GibberishAES.enc("scapply" , toks),dateInitial: GibberishAES.enc($(this).attr('dateInitial') , toks), toks:toks},
        success: function(msg){
            $("#myModal1").html(msg);
        }
    });
});

$("#usesc").click(function(){  
    $.ajax({
        url      : "<?=site_url("employeemod_/fileconfig")?>",
        type     : "POST",
        data     : {folder: GibberishAES.enc("employeemod"  , toks), view: GibberishAES.enc("scapplyuse"  , toks), toks:toks},
        success: function(msg){
            $("#myModal1").html(msg);
        }
    });
});
</script>