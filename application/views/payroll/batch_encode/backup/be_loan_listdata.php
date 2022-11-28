
<?
  $CI =&get_instance();
  $CI->load->model('utils');
  if (count($list) > 0) {
    foreach ($list as $employeeid => $detail) { 
      $can_edit = (isset($detail['can_edit'])) ? (($detail['can_edit']) ? "" : "disabled" ) : "";
      $loanbase = isset($detail['loanbase'])?$detail['loanbase']:"";
?>           
  <tr class="data-list" employeeid="<?=$employeeid?>" status-tag=''>
    <td><?=$employeeid?></td>
    <td><?=$detail['fullname']?></td>
    <td hidden><select name='baseon' class="span11" oldvalue='<?=$loanbase?>'><?=$CI->utils->basedon($loanbase)?></select></td>
    <td style="text-align: center;">
      <div class="input-append date span10"   data-date="<?=isset($detail['deductiondate'])?$detail['deductiondate']:""?>" data-date-format="yyyy-mm-dd">
      <input class="align_center required span11" type="text" name="ddatefrom" value="<?=isset($detail['deductiondate'])?$detail['deductiondate']:""?>" oldvalue="<?=isset($detail['deductiondate'])?$detail['deductiondate']:""?>" <?=$can_edit?>>
      <span class="add-on"><i class="icon-calendar"></i></span>
      </div>        
    </td>
    <td>
      <input type="text" class="span11 align_right startingbalance" name="startingbalance" value="<?=isset($detail['startingbalance'])?$detail['startingbalance']:""?>" oldvalue="<?=isset($detail['startingbalance'])?$detail['startingbalance']:""?>" <?=$can_edit?>>
    </td>
    <td>
      <input type="text" class="span11 align_right currentbalance" name="currentbalance"  value="<?=isset($detail['currentbalance'])?$detail['currentbalance']:""?> " oldvalue="<?=isset($detail['currentbalance'])?$detail['currentbalance']:""?>" <?=$can_edit?>>
    </td>
    <td>
      <input type="text" class="span11 align_right nocutoff" name="nocutoff"  value="<?=isset($detail['nocutoff'])?$detail['nocutoff']:""?>" oldvalue="<?=isset($detail['nocutoff'])?$detail['nocutoff']:""?>" <?=$can_edit?>>
    </td>
    <td><input type="text" class="span11 align_right amount" name="amount"  value="<?=isset($detail['amount'])?$detail['amount']:""?>" oldvalue="<?=isset($detail['amount'])?$detail['amount']:""?>" <?=$can_edit?>></td>

    <td style="text-align: center;">
      <select class="span11 chosen align_left" name="schedule" id="schedule" oldvalue="<?=isset($detail['schedule'])?$detail['schedule']:""?>" <?=$can_edit?>>
        <?=$this->payrolloptions->payschedule(isset($detail['schedule'])?$detail['schedule']:"");?>
          
      </select>
    </td>

    <td style="text-align: center;">
      <select class="span11 chosen align_left" name="period" id="period" oldvalue="<?=isset($detail['period']) ? $detail['period'] : "" ?>" <?=$can_edit?>>
        <?=(isset($detail['period'])) ? $this->payrolloptions->quarter($detail['period'],FALSE,$detail['schedule']) : "" ?>
      </select>
    </td>

    <td class="status-tag align_center"></td>

    <!-- mcu-hyperion 21657 -->
    <td class="edit-tag align_center">
      <a class="btn green" tag="delete" employeeid="<?=$employeeid?>"><i class="icon-trash"></i></a>
    </td>
  </tr>
<?  
    }

  }else{
?>
  <tr>
    <td colspan="11">No Record Found...</td>
  </tr>                           
<?
  }
?>

<script type="text/javascript">


  // for mcu-hyperion
  $("a[tag='delete']").unbind('click').click(function(){
    var tag = $(this).attr('tag');
    var employeeid = $(this).attr('employeeid');
    
    alert(tag +" - "+ employeeid +" - " + $("#loan").val());
  });

  $("select[name='schedule']").change(function(){
    var sel_row = $(this).parent().parent();

    $.ajax({
        url: "<?=site_url('payroll_/loadquarterforsched')?>",
        type: "POST",
        data: {
          schedule  :   $(this).val(), 
          model     :   "quarter"
        },
        success: function(msg){
           sel_row.find("select[name='period']").html(msg).trigger("liszt:updated");
        }
    });
  });


  $j(document).ready(function(){
    var payroll_table;

    if ( $.fn.DataTable.isDataTable('#dble') ) {
      $('#dble').DataTable().destroy();
    }

    setTimeout(function(){
        payroll_table = $("#dble").dataTable({
            "sPaginationType": "full_numbers",
            "oLanguage": {
                             "sEmptyTable":     "No Data Available.."
                         },
            "aLengthMenu": [[-1,10, 20], ["All", 10, 20]],
            "aoColumnDefs": [ 
                    { "bSortable": false, "aTargets": [ 'noSort' ] }
                    ],
            scrollY:        "400px",
            scrollX:        true,
            scrollCollapse: true,
            paging:         true,
            fixedHeader: true,
            fixedColumns:   {
                leftColumns: 2
            }
        });

        $j(".DTFC_LeftBodyLiner").css({"overflow-y":"hidden","overflow-x":"hidden"});
        $j(".DTFC_RightBodyWrapper").hide();
    },0);

    ///< for hovering Table Row(tr)
    $("#dble").on("mouseleave mouseover","tr.even, tr.odd",function(e){
        // console.log(e);
        var i = $(this).index();
        var type = e.type=="mouseover";

        $(this).toggleClass("active",type);
        //left Table or fixed columns
        $(".DTFC_Cloned > tbody").find("tr").eq(i).toggleClass("active",type);
        //right Table
        $("#dble > tbody").find("tr").eq(i).toggleClass("active",type);
     });

  });
  
  $("input[name=currentbalance],input[name=nocutoff],input[name=ddatefrom],input[name=amount]").on('change',function(){

    var tr_ = $(this).closest('tr');
    var to_update = validateChanges(tr_,false,'');

    changeStatusTag(to_update,tr_);

  });


  $(document).on('change',"input[name=currentbalance],input[name=nocutoff],input[name=ddatefrom],input[name=amount]",function()
  {
    var tr_ = $(this).closest('tr');
    var to_update = validateChanges(tr_,false,'');

    changeStatusTag(to_update,tr_);
  });


  $(".startingbalance,select[name=schedule],select[name=period],select[name='baseon']").on('change',function(){

      var tr_ = $(this).closest('tr');
      var to_update = validateChanges1(tr_,false,'');

      changeStatusTag(to_update,tr_);

  });

  function changeStatusTag(to_update,tr_){
    if(to_update)   updateStatusTag(tr_);
    else            removeStatusTag(tr_);
  }

  function updateStatusTag(tr_){
      $(tr_).attr('status-tag','NOTSAVED');
      $(tr_).find('.status-tag').html('NOT SAVED').css('color','red');
  }

  function removeStatusTag(tr_){
      $(tr_).attr('status-tag','');
      $(tr_).find('.status-tag').html('');
  }


  function validateChanges(tr_,isDelBtn=false,deleted_val){
      var to_update = false;

      //< current
      var current = $(tr_).find('input[name=currentbalance]');
      if($(current).attr('oldvalue') != $(current).val()) to_update = true;
      else                                                to_update = false;

      if(to_update) return to_update;

      // ///< amount
      var amount = $(tr_).find('input[name=amount]');
      if($(amount).attr('oldvalue') != $(amount).val()) to_update = true;
      else                                              to_update = false;

      if(to_update) return to_update;

       ///< cutoff
      var cutoff = $(tr_).find('input[name=nocutoff]');
      if($(cutoff).attr('oldvalue') != $(cutoff).val()) to_update = true;
      else                                              to_update = false;
     
      return to_update;
      alert(to_update);
  }

  function validateChanges1(tr_,isDelBtn=false,deleted_val){
      var to_update = false;
     
      // console.log($(starting).attr('oldvalue') != $(starting).val());
      ///< baseon
      var baseon = $(tr_).find('select[name=baseon]');
      if($(baseon).attr('oldvalue') != $(baseon).val())   to_update = true;
      else                                                to_update = false;
      // console.log($(baseon).attr('oldvalue') != $(baseon).val());
      if(to_update) return to_update;
     
      ///< schedule
      var schedule = $(tr_).find('select[name=schedule]');
      if($(schedule).attr('oldvalue') != $(schedule).val())   to_update = true;
      else                                                    to_update = false;
      if(to_update) return to_update;
     
      ///< period
      var period = $(tr_).find('select[name=period]');
      if($(period).attr('oldvalue') != $(period).val())   to_update = true;
      else                                                    to_update = false;
      if(to_update) return to_update;

       //< starting
      var starting = $(tr_).find('.startingbalance');
      if($(starting).attr('oldvalue') != $(starting).val()) to_update = true;
      else                                                  to_update = false;    
      if(to_update) return to_update;
      
      return to_update;
  }
    
  $('.date').datepicker({
     autoclose: true,
     todayBtn : true
  });

</script>





  