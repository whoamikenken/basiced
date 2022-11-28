
 ///< @author Angelica
 ///< for schedule input defaults
 ///< tardy start = starttime + 6 mins, absent start = starttime + 2hrs , early_d = endtime - 2hrs

$(document).on('change',"input[name='fromtime'], input[name='totime']",function(){
      var time = $(this).val();
      var parent = $(this).parent().parent().parent();
      setTimeDefault($(this).attr('name'), time, parent);
});

function setTimeDefault(type, value, parent){
    var hms = value; // your input string
    var a = hms.split(':'); // split it at the colons
    var b = a[1] ? a[1].split(' ') : [0];
    var am = b[1] ? (b[1] == 'AM' ? true : false) : true;
    var pm = b[1] ? (b[1] == 'PM' ? true : false) : false;

    if(pm && a[0]!=12)              a[0] = (+a[0]) + 12 ;
    if(am && a[0]==12)  a[0] = (+a[0]) - 12 ;

    var seconds;
    var newtime;
    var target;
    var plustardy;
    var plusabsent;
    var minusearlyd;

    if(type == 'fromtime'){
        var dow = parent.attr('dayofweek');

        if(a[0]=='0' && b[0]=='00'){
          plustardy = 0;
          plusabsent = 0;
        }else{
          if(parent.is( "tr[dayofweek="+dow+"]:first" ))  plustardy = 16 * 60;
          else                                            plustardy = 30 * 60;
          
          plusabsent = (60 * 60 * 2);
        }


        seconds = (+a[0]) * 60 * 60 + (+b[0]) * 60 +plustardy; 
        newtime = toHHMMSS(seconds);
        target = parent.children().find("input[name='tardy_f']");
        if(newtime.length == 8) target.val(newtime);

        seconds = (+a[0]) * 60 * 60 + (+b[0]) * 60 + plusabsent; 
        newtime = toHHMMSS(seconds);
        target = parent.children().find("input[name='absent_f']");
        if(newtime.length == 8) target.val(newtime);

    }else if(type == 'totime'){
      // console.log(a[0] + ' // ' + b[0]);

        if(a[0]=='0' && b[0]=='00'){
          minusearlyd = 0;
        }else{
          minusearlyd = (60 * 60 * 2);
        }

        seconds = (+a[0]) * 60 * 60 + (+b[0]) * 60 - minusearlyd; 
        newtime = toHHMMSS(seconds);
        target = parent.children().find("input[name='early_d']");
        if(newtime.length == 8) target.val(newtime);

    }
        
}

function toHHMMSS (seconds) {
  var sec_num = parseInt(seconds, 10); // don't forget the second param
  var hours = Math.floor(sec_num / 3600);
  var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
  var seconds = sec_num - (hours * 3600) - (minutes * 60);
  var ampm = hours >= 12 ? 'PM' : 'AM';

  hours = hours % 12;
  hours = hours ? hours : 12; // the hour '0' should be '12'
  hours = hours < 10 ? '0'+hours : hours;
  minutes = minutes < 10 ? '0'+minutes : minutes;
  seconds = seconds < 10 ? '0'+seconds : seconds;

  return hours+':'+minutes+ ' ' + ampm;
}
 
 ///< end of schedule input defaults

if (typeof schedarr === 'undefined') {
  var schedarr = [];
}



$("a[tag='copy_sched']").click(function(){  var obj = $(this).parent().parent().parent();   copytime(obj);
    $("a[tag='copy_sched']").each(function(){   $(this).css({"color":"","background-color":""});    });
    $(this).css({"color":"#D10303","background-color":"#BABABA"});
});
$("a[tag='paste_sched']").click(function(){ var obj = $(this).parent().parent().parent();   pastetime(obj); });


 ///< @Angelica for schedule copy and paste per day

 function copytime(obj){
     if(schedarr.length > 0)  schedarr = [];

     var schedarr_temp = [];
     $('tr[dayofweek='+obj.attr('dayofweek')+']').each(function(){
       var from          = $(this).find("input[name='fromtime']").val();
       var to            = $(this).find("input[name='totime']").val();
       var lec  = $(this).find("input[name='leclab']:checked").val();
       // var lec           = '';
       var tardy_f       = $(this).find("input[name='tardy_f']").val();
       var absent_f      = $(this).find("input[name='absent_f']").val();
       var early_d       = $(this).find("input[name='early_d']").val();

       if(from != '' || to != '' || lec != undefined || tardy_f != '' || absent_f != '' || early_d != ''){
           schedarr_temp = {
             'fromtime'  :from,
             'totime'    :to,
             'schedtype' :lec,
             'tardy_f'   :tardy_f,
             'absent_f'  :absent_f,
             'early_d'   :early_d,
           };
           schedarr.push(schedarr_temp);
       }
     });
     // console.log(schedarr);

     // schedarr.push({
     //     'fromtime'  :obj.find("input[name='fromtime']").val(),
     //     'totime'    :obj.find("input[name='totime']").val(),
     //     'schedtype' :obj.find("input[name='leclab']:checked").val(),
     // });  
 }
 function pastetime(obj){

     var schedarr_orig       = [],
         schedarr_orig_temp  = [];
     
     $('tr[dayofweek='+obj.attr('dayofweek')+']').each(function(){
         var from = $(this).find("input[name='fromtime']").val();
         var to   = $(this).find("input[name='totime']").val();
         var lec  = $(this).find("input[name='leclab']:checked").val();
         // var lec           = '';
         var tardy_f       = $(this).find("input[name='tardy_f']").val();
         var absent_f      = $(this).find("input[name='absent_f']").val();
         var early_d       = $(this).find("input[name='early_d']").val();

         if(from != '' || to != '' || lec != undefined || tardy_f != '' || absent_f != '' || early_d != ''){
             schedarr_orig_temp = {
               'fromtime'  :from,
               'totime'    :to,
               'schedtype' :lec,
               'tardy_f'   :tardy_f,
               'absent_f'  :absent_f,
               'early_d'   :early_d,
             };
             schedarr_orig.push(schedarr_orig_temp);
         }

         $(this).find("a[tag=delete_sched]").click();
     });
     // console.log(schedarr_orig);
     if(schedarr_orig.length == 0){
       if(schedarr.length > 0){
         obj.find("input[name='fromtime']").val(schedarr[0]['fromtime']);
         obj.find("input[name='totime']").val(schedarr[0]['totime']);
         obj.find("input[name='leclab']").each(function(){
             if($(this).val() == schedarr[0]['schedtype'])   $(this).prop("checked",true);   
             else                                            $(this).removeAttr("checked");
         });
         obj.find("input[name='tardy_f']").val(schedarr[0]['tardy_f']);
         obj.find("input[name='absent_f']").val(schedarr[0]['absent_f']);
         obj.find("input[name='early_d']").val(schedarr[0]['early_d']);

         if(schedarr.length > 1){
             for (var i = schedarr.length - 1; i >= 1; i--) {
                 $(obj).find("a[tag=add_sched]").click();
                 $(obj).next(':first').find("input[name='fromtime']").val(schedarr[i]['fromtime']);
                 $(obj).next(':first').find("input[name='totime']").val(schedarr[i]['totime']);
                 $(obj).next(':first').find("input[name='leclab']").each(function(){
                     if($(this).val() == schedarr[i]['schedtype'])   $(this).prop("checked",true);   
                     else                                            $(this).removeAttr("checked");
                 });
                 $(obj).next(':first').find("input[name='tardy_f']").val(schedarr[i]['tardy_f']);
                 $(obj).next(':first').find("input[name='absent_f']").val(schedarr[i]['absent_f']);
                 $(obj).next(':first').find("input[name='early_d']").val(schedarr[i]['early_d']);
             }
         }
       }
     }else if(schedarr_orig.length > 0){
       if(schedarr.length > 0){
         for (var i = schedarr.length - 1; i >= 0; i--) {
             $(obj).find("a[tag=add_sched]").click();
             $(obj).next(':first').find("input[name='fromtime']").val(schedarr[i]['fromtime']);
             $(obj).next(':first').find("input[name='totime']").val(schedarr[i]['totime']);
             $(obj).next(':first').find("input[name='leclab']").each(function(){
                 if($(this).val() == schedarr[i]['schedtype'])   $(this).prop("checked",true);   
                 else                                            $(this).removeAttr("checked");
             });
             $(obj).next(':first').find("input[name='tardy_f']").val(schedarr[i]['tardy_f']);
             $(obj).next(':first').find("input[name='absent_f']").val(schedarr[i]['absent_f']);
             $(obj).next(':first').find("input[name='early_d']").val(schedarr[i]['early_d']);
         }
       }
     }

     if(schedarr_orig.length == 1){
       obj.find("input[name='fromtime']").val(schedarr_orig[0]['fromtime']);
       obj.find("input[name='totime']").val(schedarr_orig[0]['totime']);
       obj.find("input[name='leclab']").each(function(){
           if($(this).val() == schedarr_orig[0]['schedtype'])   $(this).prop("checked",true);   
           else                                            $(this).removeAttr("checked");
       });
       obj.find("input[name='tardy_f']").val(schedarr_orig[0]['tardy_f']);
       obj.find("input[name='absent_f']").val(schedarr_orig[0]['absent_f']);
       obj.find("input[name='early_d']").val(schedarr_orig[0]['early_d']);
     }else if(schedarr_orig.length == 0){

     }

     if(schedarr_orig.length > 1){
       for (var i = schedarr_orig.length - 1; i > 0; i--) {
           $(obj).find("a[tag=add_sched]").click();
           $(obj).next(':first').find("input[name='fromtime']").val(schedarr_orig[i]['fromtime']);
           $(obj).next(':first').find("input[name='totime']").val(schedarr_orig[i]['totime']);
           $(obj).next(':first').find("input[name='leclab']").each(function(){
               if($(this).val() == schedarr_orig[i]['schedtype'])   $(this).prop("checked",true);   
               else                                            $(this).removeAttr("checked");
           });
           $(obj).next(':first').find("input[name='tardy_f']").val(schedarr_orig[i]['tardy_f']);
           $(obj).next(':first').find("input[name='absent_f']").val(schedarr_orig[i]['absent_f']);
           $(obj).next(':first').find("input[name='early_d']").val(schedarr_orig[i]['early_d']);
       }
     }


     // obj.find("input[name='fromtime']").val(schedarr[0]['fromtime']);
     // obj.find("input[name='totime']").val(schedarr[0]['totime']);
     // obj.find("input[name='leclab']").each(function(){
     //     if($(this).val() == schedarr[0]['schedtype'])   $(this).prop("checked",true);   
     //     else                                            $(this).removeAttr("checked");
     // });
 }

 ///< end of schedule copy and paste per day
