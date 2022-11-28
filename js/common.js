function datereform(val,len){
   return (val.length<len ? "0" + val.length : val); 
}
function reformstring(str,ende){
  var strap = "~@#$%^&*()_+-/=";
  var equi = Array(":curl:",":at:",":num:",":dollar:",":percent:",":roof:",":amp:",":ast:",":opar:",":cpar:",":uscore:",":plus:",":minus:",":fslash:",":equal:");
  
  var tmpcfrom = (ende==1?strap:equi);
  var tmpcto = (ende==1?equi:strap);  
  var tmpstr = str;
  for(var o=0;o<tmpcfrom.length;o++){
    while(tmpstr.indexOf(tmpcfrom[o])!=-1){
        tmpstr = tmpstr.replace(tmpcfrom[o],tmpcto[o]);
    }
  }
  return tmpstr;
}
function docloseshade(ob){
    $("#"+ob).fadeOut(500);
}
function displayloader(uri){
     var form_data = {
             isajax: 1,
         }
        $.ajax({
             url: uri,
             type: "POST",
             data: form_data,
             success: function(msg){ 
                  $("#shadeblock").html(msg);
             }
         }); 
    
}
function exploder(toex,str){
  var temp = str;
  while(temp.indexOf(toex)!=-1) temp = temp.replace(toex,",");   
  return eval("Array(" + temp + ")"); 
}
function dosearchbox(url,id){
    $(id).autoComplete({
		ajax: url,
		postData: {
			hook1: 'Do something on hook1',
			hook2: 1942,
			hook3: 'Do something with hook3'
		},
		postFormat: function(event, ui){
			// Add the current timestamp to each request
			ui.data.requestTimestamp = (new Date()).getTime();

			// Return the data object to be passed with the ajax function
			return ui.data;
		}
	});
}
function centerThis(div) { 
        var winH = $(window).height(); 
        var winW = $(window).width(); 
        var centerDiv = $('#' + div); 
        centerDiv.css('top', winH/2-centerDiv.height()/2); 
        centerDiv.css('left', winW/2-centerDiv.width()/2); 
} 
function doPopulateDate(monthOBJ,dateOBJ,yearOBJ){
  var d = $(dateOBJ).find("option:selected").text();
  var u = 0;
  var selected = false;
      timeA = new Date($(yearOBJ).find("option:selected").text(),
                       $(monthOBJ).find("option:selected").val(),1);
      timeDifference = timeA - 86400000;
      timeB = new Date(timeDifference);
      var daysInMonth = timeB.getDate();

  $(dateOBJ).empty();
  
  for (var i = 1; i <= daysInMonth; i++){ 
        ds = i<10 ? "0"+i : i; 
        $(dateOBJ).append("<option value='"+ds+"'>"+ds+"</option>");
  }
  $(dateOBJ).find("option[value='"+d+"']").attr("selected",true);
}
function numb(o,n){
  var no = o;  
  
  return Number(no).toFixed(n);
}




///<@added Angelica -- added functions checkbox toggle

function toggleIncludeAll(obj,other,isDatatable=false,tbl='',select=''){
    if($(obj).is(':checked')){

        if(isDatatable){
          var oSettings = tbl.fnSettings();
          oSettings._iDisplayLength = -1;
          tbl.fnDraw();

          $(select).val('-1');
        }

        $(other).prop('checked',true); 

    }else{     

        $(other).prop('checked',false);

        if(isDatatable){
          var oSettings = tbl.fnSettings();
          oSettings._iDisplayLength = 10;
          tbl.fnDraw();
          $(select).val('10');
        }
    }
}

function toggleInclude(obj,other){
    if(!$(obj).is(':checked'))     $(other).prop('checked',false);
}

///<@added Angelica -- added functions validate and set value of field to a number

function setNumberOnly(field,isInteger=false){
  if(isInteger){
    var output = $(field).val().replace(/[^0-9]+/g, "");
    
  }else{
    var input = $(field).val().replace(/[^0-9.]+/g, "");
    var output = input.split('.');
    output = output.shift() + (output.length ? '.' + output.join('') : '');
  }
  
  $(field).val(output);
}