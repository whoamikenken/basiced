/*autoload this functions*/
var toks = hex_sha512(" ");
var ProvID = $("#provaddr").val();
var munid = $("#cityaddr").val();
var regCode = $("#regaddr").val();
var brgyid = $("#brgyid").val();
var permaReg = $("#permaRegion").val();
var permaProv = $("#permaProvince").val();
var permaMun = $("#permaMunicipality").val();
var permaBar = $("#permaBrgy").val();
var empshift_updated = '';
processEmployeeAge(true);
loadEmpHistoryTable();

$(document).ready(function(){
    // $('form input[name="passport_expiration"]').prop("disabled", true);
    // $('form input[name="prc_expiration"]').prop("disabled", true);
    $('form input[type="submit"]').prop("disabled", true);
    $("#modal-view").find("#button_save_modal").addClass("button_save_modal");
    $("#modal-view").find("#modalclose").addClass("modalclose");
    $("#get").addClass("notEmpty");
    $('#saveShiftSched').hide();
    // $("#aimsdepartment").hide();
    if($("#aimcheckbox").prop('checked')){
      $("#aimsdepartment").show();
    }
    else{
      $("#aimsdepartment").hide();
    }
  //$("#tinno").inputmask("mask", {"mask": "999-999-999"});
    //$("#sssno").inputmask("mask", {"mask": "9999-9999999-9"});
    $("#citytelno").inputmask("mask", {"mask": "+(999) 999-99-99"});

    $("#mobile").inputmask("mask", {"mask": "9999-9999999"});
    // $("#landline").inputmask("mask", {"mask": "9-999-9999"});
    $("#spouse_contact").inputmask("mask", {"mask": " 9999-9999999"});

    $('.chosen-select').chosen();

    ImmiFields(true);
    loadCityAndProvinces();
    loadPermanentCityAndProvinces();
    $.validator.setDefaults({
      debug: true,
      success: "valid",
      ignore: ":hidden:not(.chzn-done)",
      errorPlacement: function(error, element) {
            if (element.hasClass('chosen')) {
                error.insertAfter(element.next('.chzn-container'));
            }else if(element.hasClass('yesno') || element.hasClass('applicable-field')){
                error.insertBefore(element.parents("div").eq(0));
            }else{
                error.insertAfter(element);
            }
        }
    });
    
    if($("#regaddr").val() == $("#permaRegion").val() && $("#provaddr").val() == $("#permaProvince").val() && $("#cityaddr").val() == $("#permaMunicipality").val() && $("#addr").val() == $("#permaAddress").val()
        && $("#zip_code").val() == $("#permaZipcode").val() && $("#barangay").val() == $("#permaBarangay").val() && $("#addr").val() != ""){
        if($("#usertype").val() == "ADMIN"){
            $("#field_terms").attr('checked', true);
            permaAddressLocked();
        }
        else{
            $("#field_terms").attr('checked', true);
            checkboxLocked();
            permaAddressLocked();
        }
    }

    // if($("#regaddr").val() != "" && $("#provaddr").val() != "" && $("#cityaddr").val() != "" && $("#addr").val() != ""
    //     && $("#zip_code").val() != "" && $("#barangay").val() != ""){
    //         checkboxUnlocked();
    // }   
    // else{
    //         checkboxLocked();
    //     }


    $.validator.addMethod("loginRegex", function(value, element) {
        return this.optional(element) || /^[a-z0-9\s]+$/i.test(value);
    }, "Username must contain only letters, numbers.");


    $('#date_active, #dateresigned, #bdate, .datepos, .dateemployed, .date').datetimepicker({
        format: 'YYYY-MM-DD'
    });

    $("a[tag='add_legit']").click(function(){
        addlegit("");
    });

    $("select[name='emptype']").change(function(){
        var emptype  = $(this).val();
        var empshift = $("select[name='empshift']").val();
        call_shiftschedule(emptype, empshift);  
    });

    ///< for separate saving of shift schedule and effectivity date
    var empshift    = $("#empshift").val();
    var date_active = $("#date_active").val();
    var prev_date_active = date_active;


    $('#prc_expiration').datetimepicker({format: 'YYYY-MM-DD'}).on('dp.change', function (event) {
        if($('input[name=prc_expiration]').val() != prc_expiration ){
            $('#prc_alert').hide();
        }else{
            $('#prc_alert').show();
        }
    });

     $('#passport_expiration').datetimepicker({format: 'YYYY-MM-DD'}).on('dp.change', function (event) {
        if($('input[name=passport_expiration]').val() != passport_expiration ){
            $('#passport_alert').hide();
        }else{
            $('#passport_alert').show();
        }
    });

     if($("#childcBox").prop('checked')){
      $("#table_family a").attr('disabled', true);
      $("#table_family a").css('pointer-events', 'none');
    }
    else{
      $("#table_family a").attr('disabled', false);
      $("#table_family a").css('pointer-events', '');
    }

    if($("#eciBox").prop('checked')){
      $("#table_econtact a").attr('disabled', true);
      $("#table_econtact a").css('pointer-events', 'none');
    }
    else{
      $("#table_econtact a").attr('disabled', false);
      $("#table_econtact a").css('pointer-events', '');
    }

});


    

$("#addr").keyup(function(){
    if($("#addr").val() != ""){
            checkboxUnlocked();
    }   
    else{
            checkboxLocked();
        }
})

$(".passport").keyup(function(){
    if($(this).val() != ""){
        $('form input[name="passport_expiration"]').prop("disabled", false);
        $("#passport_alert").show();
    }
    else if($(this).val() == ""){
        $('form input[name="passport_expiration"]').prop("disabled", true);
        $('form input[name="passport_expiration"]').val("");
        $('form input[name="passport_expiration"]').blur();
        $("#passport_alert").hide();
    }
});

$(".prc").keyup(function(){
    if($(this).val() != ""){
        // $('form input[name="prc_expiration"]').prop("disabled", false);
        $("#prc_alert").show();
    }
    else if($(this).val() == ""){
        // $('form input[name="prc_expiration"]').prop("disabled", true);
        $('form input[name="passport_expiration"]').val("");
        $('form input[name="prc_expiration"]').blur();
        $("#prc_alert").hide();
    }
});

// $("#regaddr, #provaddr, #cityaddr, #addr, #zip_code, #barangay").on("change", function(){
//         if($("#regaddr").val() != "" && $("#provaddr").val() != "" && $("#cityaddr").val() != "" && $("#addr").val() != ""
//         && $("#zip_code").val() != "" && $("#barangay").val() != ""){
//         checkboxUnlocked();
//     }
//         else{
//             checkboxLocked();
//         }
//     });

$(".applicable-field").click(function(){
    if($(this).prop("checked") == true){
        $('form input[type="submit"]').prop("disabled", false);
    }
    else if($(this).prop("checked") == false){
        $('form input[type="submit"]').prop("disabled", true);
    }
});
  
$(".uploadPhoto").click(function(){
    var modalTitle = $(this).attr("modalTitle");
    var filename = $(this).attr("filename");
    $("#modal-view").find(".modal-footer").html('<button type="button" data-dismiss="modal" class="btn btn-danger modalclose" id="modalclose">Close</button> <button type="button" class="btn btn-success button_save_modal" id="button_save_modal">Save</button>');
    $("#modal-view").find("h3[tag='title']").text(modalTitle);
    $("#button_save_modal").text(modalTitle);
    $.ajax({
        url: $("#site_url").val() + "/employee_/uploadPhoto",
        type: "POST",
        data: {filename:filename},
        success: function(msg){
            $("#modal-view").find("div[tag='display']").html(msg);
        }
    });
});

    

$('[name=terms]').click(function(){
    if($(this).prop("checked") == true){
        var region= $("#region").val();
        var addr= $("#addr").val();
        var province=$("#selProvince").val();
        var barangay=$("#barangay").val();
        var municipality=$("#selMunicipality").val();
        var zip_code=$("#zip_code").val();
        $("#permaAddress").val(addr);
        $("#permaZipcode").val(zip_code);
        $("#permaRegionselect").val(region).trigger("chosen:updated");
        permaAddressLocked();
        loadPermanentCityAndProvinces(1,region, province, barangay, municipality);
        var employeeid = $("#employeeid").val();
        var cAddr = [region, addr, province, barangay, municipality, zip_code];
        var cName = ['permaRegion', 'permaAddress', 'permaProvince', 'permaBarangay', 'permaMunicipality', 'permaZipcode'];
        jQuery.each(cAddr, function(a, caddr){
            jQuery.each(cName, function(n, cname){
                if(a == n){
                    var formdata = {
                    toks:toks,
                    column: GibberishAES.enc(cname, toks),
                    value: GibberishAES.enc(caddr, toks),
                    employeeid: GibberishAES.enc(employeeid, toks)
                };
                $.ajax({
                    url: $("#site_url").val() + "/employee_/updateEmployeeInformation",
                    data: formdata,
                    type: "POST",
                    success:function(response){
                        loadSuccessModal();
                    }
                });
                }
            });
        });
    }
    else if($(this).prop("checked") == false){
        $("#permaAddress").val("");
        $("#permaRegionselect").val("").trigger("chosen:updated");
        $("#permaProvince").val("").trigger("chosen:updated");
        $("#permaBarangay").val("").trigger("chosen:updated");
        $("#permaMunicipality").val("").trigger("chosen:updated");
        $("#permaZipcode").val("");
        permaAddressUnlocked();
        UnloadPermaCityAndProvinces();
        var employeeid = $("#employeeid").val();
        var cAddr = [region, addr, province, barangay, municipality, zip_code];
        var cName = ['permaRegion', 'permaAddress', 'permaProvince', 'permaBarangay', 'permaMunicipality', 'permaZipcode'];
        jQuery.each(cAddr, function(a, caddr){
            jQuery.each(cName, function(n, cname){
                if(a == n){
                    var formdata = {
                    toks:toks,
                    column: GibberishAES.enc(cname, toks),
                    value: GibberishAES.enc("", toks),
                    employeeid: GibberishAES.enc(employeeid, toks)
                };
                $.ajax({
                    url: $("#site_url").val() + "/employee_/updateEmployeeInformation",
                    data: formdata,
                    type: "POST",
                    success:function(response){
                        loadSuccessModal();
                    }
                });
                }
            });
        });

    }
});

function checkboxLocked(){
        $("#field_terms").css('pointer-events', 'none');
}

function checkboxUnlocked(){
        $("#field_terms").css('pointer-events', '');
}

function permaAddressUnlocked(){
    $("#permaAddress").prop("readonly", false);
    $("#permaRegions").css('pointer-events', '');
    $("#permaProvinces").css('pointer-events', '');
    $("#permaBarangay").css('pointer-events', '');
    $("#permaMunicipalitys").css('pointer-events', '');
    $("#permaZipcode").prop("readonly", false);
}

function permaAddressLocked(){
    $("#permaAddress").prop("readonly", true);
    $("#permaRegions").css('pointer-events', 'none');
    $("#permaProvinces").css('pointer-events', 'none');
    $("#permaBarangay").prop("readonly", true);
    $("#permaMunicipalitys").css({'pointer-events': 'none','opacity': '0.5 !important', 'cursor': 'default'});
    $("#permaZipcode").prop("readonly", true);
}

$("#childcBox, #eciBox").change(function(){
    var cb1 = $("#childcBox").prop('checked') == true ? 0 : 1 ;
    var cb2 = $("#eciBox").prop('checked') == true ? 0 :1 ;
    if($("#childcBox").prop('checked')){ 
      $("#table_family a").attr('disabled', true);
      $("#table_family a").css('pointer-events', 'none');
    }
    else{
      $("#table_family a").attr('disabled', false);
      $("#table_family a").css('pointer-events', '');
    }

    if($("#eciBox").prop('checked')){ 
      $("#ECI_table a").attr('disabled', true);
      $("#ECI_table a").css('pointer-events', 'none');
    }
    else{
      $("#ECI_table a").attr('disabled', false);
      $("#ECI_table a").css('pointer-events', '');
    }

    $.ajax({
        url: $("#site_url").val() + "/applicant/personalDataCheckbox",
        type: "POST",
        data: {
          employeeid : GibberishAES.enc($("#employeeid").val(),toks),
          children : GibberishAES.enc(cb1,toks),
          emergencyContact : GibberishAES.enc(cb2,toks),
          toks:toks
        },
        dataType: "json",
        success:function(response){
        }
     });
});



$('input[type=text]').each(function(){
    if ($(this).val()){
      $(this).addClass("notEmpty");
    }
});

if (!$('#get').data('value') == 0){
    $('#get').addClass("notEmpty");
    var text = $('#'+$('#get').data('value')).text();
    $('#get').text(text);
}

$('select[name=date_active]').on('change',function(){
    if($('input[name=date_active]').val() != date_active || $('select[name=date_active]').val() != date_active){
        $('#saveShiftSched').show();
    }else{
        $('#saveShiftSched').hide();

    }     
});

$('#date_active').datetimepicker({format: 'YYYY-MM-DD'}).on('dp.change', function (event) {
       if($('input[name=date_active]').val() != date_active || $('select[name=empshift]').val() != empshift){
        $('#saveShiftSched').show();
        $('#saveShiftSched').delay(2000).fadeOut(2000);

    }else{
        $('#saveShiftSched').hide();

    }
});
// if($("input[name='resigned_reason']").val() != ""){
//     $('#currentDateres').show();
// }else{
//     $('#currentDateres').hide();
// }
 
$("#clearResigned").on('click',function(){
    $('#currentDateres').hide();
    $("#DateofSep").val('');
    var resigned = $("input[name='dateresigned']");
    var reason = $("input[name='resigned_reason']");
    reason.val("");
    resigned.val("");     
    $('form input[name="resigned_reason"]').blur();
});

$("select[name='regionaladdr']").change(function(){
    clearOtherData('regionaladdr');
    getProvince(true);
});

$("select[name='provaddr']").change(function(){
    clearOtherData('provaddr');
    getMunicipality(true);
});


$("select[name='cityaddr']").change(function(){
    clearOtherData('cityaddr');
    getBarangay(true);
    getZipCode($("select[name='cityaddr'] option:selected").html());
});

$("select[name='barangay']").change(function(){
    getZipCode($("select[name='barangay'] option:selected").html(), $("select[name='cityaddr'] option:selected").html());
});

$("select[name='permaRegion']").change(function() {
    clearOtherData('permaRegion');
    getPermaProvince();
});

$("select[name='permaProvince']").change(function(){
    clearOtherData('permaProvince');
    getPermaMunicipality();
});

$("select[name='permaMunicipality']").change(function(){
    clearOtherData('permaMunicipality');
    getPermaBarangay(true);
    getPermaZipCode($("select[name='permaMunicipality'] option:selected").html());
});

$("select[name='permaBarangay']").change(function(){
    getPermaZipCode($("select[name='permaBarangay'] option:selected").html(), $("select[name='permaMunicipality'] option:selected").html());
});

function clearOtherData(data){
    var employeeid = $("#empid").val();
    $.ajax({
        url: $("#site_url").val() + "/employee_/loadExtrasFunction",
        type: "POST",
        data: {
          toks:toks,
          employeeid : GibberishAES.enc(employeeid, toks),
          changeaddr : GibberishAES.enc(data, toks),
          fnctn: GibberishAES.enc("changeaddr", toks)
        },
        success: function(msg){
        }
    });
}

function getProvince(ischange=false){
    var regCode = $("select[name='regionaladdr']").val();
    if(ischange){
        ProvID = "";
    }
    $.ajax({
        url: $("#site_url").val() + "/employee_/loadExtrasFunction",
        type: "POST",
        data: {
          provid :  GibberishAES.enc(ProvID , toks),
          regCode:  GibberishAES.enc(regCode , toks),
          fnctn:  GibberishAES.enc("provincelist" , toks),
          toks:toks
        },
        success: function(msg){
           $("select[name='provaddr']").html(msg).trigger("chosen:updated");
           $("select[name='cityaddr']").html('<option value="">Choose a municipality ...</option>').trigger("chosen:updated");
           $("select[name='barangay']").html('<option value="">Choose a barangay ...</option>').trigger("chosen:updated");
           $("#zip_code").val("");
        }
    });
}

function getMunicipality(ischange=false){
    var ProvID = $("select[name='provaddr']").val();
    if(ischange){
        munid = "";
    }
    $.ajax({
        url: $("#site_url").val() + "/employee_/loadExtrasFunction",
        type: "POST",
        data: {
          munid :  GibberishAES.enc(munid , toks),
          ProvID:  GibberishAES.enc( ProvID, toks),
          fnctn:  GibberishAES.enc("municipalitylist" , toks),
          toks:toks
        },
        success: function(msg){
           $("select[name='cityaddr']").html(msg).trigger("chosen:updated");
           $("select[name='barangay']").html('<option value="">Choose a barangay ...</option>').trigger("chosen:updated");
        }
    });
}

function getBarangay(ischange=false){
    var munid = $("select[name='cityaddr']").val();
    if(ischange){
        brgyid = "";
    }
    $.ajax({
        url: $("#site_url").val() + "/employee_/loadExtrasFunction",
        type: "POST",
        data: {
          brgyid :  GibberishAES.enc( brgyid, toks),
          munid:  GibberishAES.enc(munid , toks),
          fnctn:  GibberishAES.enc( "barangaylist", toks),
          toks:toks
        },
        success: function(msg){
           $("select[name='barangay']").html(msg).trigger("chosen:updated");
        }
    });
}

function getPermaBarangay(ischange=false){
    var munid = $("select[name='permaMunicipality']").val();
    if(ischange){
        brgyid = "";
    }
    $.ajax({
        url: $("#site_url").val() + "/employee_/loadExtrasFunction",
        type: "POST",
        data: {
          brgyid :  GibberishAES.enc(permaBar , toks),
          munid:  GibberishAES.enc(munid , toks),
          fnctn:  GibberishAES.enc( "barangaylist", toks),
          toks:toks
        },
        success: function(msg){
           $("select[name='permaBarangay']").html(msg).trigger("chosen:updated");
        }
    });
}

function getPermaProvince(){
    var permaReg = $("select[name='permaRegion']").val();

    $.ajax({
        url: $("#site_url").val() + "/employee_/loadExtrasFunction",
        type: "POST",
        data: {
          provid :  GibberishAES.enc(permaProv , toks),
          regCode:  GibberishAES.enc(permaReg , toks),
          fnctn:  GibberishAES.enc("provincelist", toks),
          toks:toks
        },
        success: function(msg){
           $("select[name='permaProvince']").html(msg).trigger("chosen:updated");
           $("select[name='permaMunicipality']").html('<option value="">Choose a municipality ...</option>').trigger("chosen:updated");
           $("select[name='permaBarangay']").html('<option value="">Choose a barangay ...</option>').trigger("chosen:updated");
           $("#permaZipcode").val("");
        }
    });
}

function getZipCode(place, mun=''){
    var provCode = $("#selProvince").val();
    var munCode = $("#selMunicipality").val();
    var zipRes = 0;
    $.ajax({
        url: $("#site_url").val() + "/employee_/getZipCode",
        type: "POST",
        data: {place: GibberishAES.enc( place, toks), mun: GibberishAES.enc( mun, toks), provCode: GibberishAES.enc( provCode, toks), munCode: GibberishAES.enc(munCode , toks), toks:toks},
        success:function(res){
            if(res){
                $("#zip_code").val(res);
                zipRes = res;
            } 
        }
    });
    setTimeout(function(){
        if(zipRes != 0){
            var employeeid = $("#employeeid").val();
            var formdata = {
                column:  GibberishAES.enc('zip_code' , toks),
                value:  GibberishAES.enc( zipRes, toks),
                employeeid:  GibberishAES.enc( employeeid, toks),
                toks:toks
            }
            $.ajax({
                url: $("#site_url").val() + "/employee_/updateEmployeeInformation",
                data: formdata,
                type: "POST",
                success:function(response){
                    loadSuccessModal();
                }
            });
        }
    }, 500)
}

function getPermaZipCode(place, mun=''){
    var provCode = $("#permaProvince").val();
    var munCode = $("#permaMunicipality").val();
    var zipPermaRes = 0;
    $.ajax({
        url: $("#site_url").val() + "/employee_/getZipCode",
        type: "POST",
        data: {place: GibberishAES.enc(place , toks), mun: GibberishAES.enc(mun , toks), provCode: GibberishAES.enc(provCode , toks), munCode: GibberishAES.enc( munCode, toks), toks:toks},
        success:function(res){
            if(res){
                $("#permaZipcode").val(res);
                zipPermaRes = res;
            } 
        }
    });
    setTimeout(function(){
        if(zipPermaRes != 0){
            var employeeid = $("#employeeid").val();
            var formdata = {
                column:  GibberishAES.enc('permaZipcode' , toks),
                value:  GibberishAES.enc( zipPermaRes, toks),
                employeeid:  GibberishAES.enc( employeeid, toks),
                toks:toks
            }
            $.ajax({
                url: $("#site_url").val() + "/employee_/updateEmployeeInformation",
                data: formdata,
                type: "POST",
                success:function(response){
                    loadSuccessModal();
                }
            });
        }
    }, 500)
}

function getPermaMunicipality(){
    var permaProv = $("select[name='permaProvince']").val();
    $.ajax({
        url: $("#site_url").val() + "/employee_/loadExtrasFunction",
        type: "POST",
        data: {
          munid :  GibberishAES.enc( permaMun, toks),
          ProvID:  GibberishAES.enc(permaProv , toks),
          fnctn:  GibberishAES.enc( "municipalitylist", toks),
          toks:toks
        },
        success: function(msg){
           $("select[name='permaMunicipality']").html(msg).trigger("chosen:updated");
           $("select[name='permaBarangay']").html('<option value="">Choose a barangay ...</option>').trigger("chosen:updated");
        }
    });
}

$(".aims").click(function () {
    if ($(this).is(":checked")) {
        $("#save_emp_aims").click();
        // $(".aimsdepartment").show();
        // $("#add_employee_aims").modal("toggle");
    }else{
        // $(".aimsdepartment").hide();
    }
});

$("#selNationality").change(function() {
    ImmiFields(($("#selNationality").val() > 1) ? false : true);
});

if($("#bplace").val() == "")    $("#print_out").hide();
else                            $("#print_out").show();

$(".editrelation").click(function(){
   addlegit($(this)); 
});



$("a[name='backlist']").click(function(){
    location.reload();
});


$('#empshift').on('change',function(e){
     $('#saveShiftSched').hide();   

    var newempshift = $('select[name=empshift]').val();
    var newdateactive = $('input[name=date_active]').val();
    var prev_date_active = $('input[name=date_active]').val();

    $.ajax({
        url: $("#site_url").val() + "/employee_/saveShiftSchedule",
        type: "POST",
        dataType : 'JSON',
        data: {
            employeeid : GibberishAES.enc($("#employeeid").val()  , toks),
            tnt        :  GibberishAES.enc($("#teachingtype").val() , toks),
            empshift   :  GibberishAES.enc( newempshift, toks),
            date_active:  GibberishAES.enc(newdateactive , toks),
            prev_date_active:  GibberishAES.enc(prev_date_active , toks),
            toks:toks
        },
        success: function(msg){
            $('#saveShiftSchedMsg').removeAttr('hidden');
            $('#saveShiftSchedMsg').show();
            if(msg.err_code==0){
                empshift = newempshift;
                date_active = newdateactive;
                prev_date_active = newdateactive;
                $("#saveShiftSchedMsg").html(msg.msg).css('color','green').delay(5000).fadeOut();
            }else{
                $('#saveShiftSched').removeAttr('hidden');
                $("#saveShiftSchedMsg").html(msg.msg).css('color','red').delay(5000).fadeOut();
            }
        }
    });

});


$(".teachingtype").click(function(){
    if ($(".teachingtype").val() =="teaching" ) {
        $(".aims").prop('checked',this.checked);
        if ($(".aims").is(":checked")) {
            
            $(".aimsdepartment").show();

        } else {
            
            $(".aimsdepartment").hide();
        }
    }

});

$(".aims").on('change', function() {
    $(".aims").not(this).prop('checked', false);
});

$(".tload").on('change', function() {
    $(".tload").not(this).prop('checked', false);
});

$(".teachingtype").on('change', function() {
    $(".teachingtype").not(this).prop('checked', false); 
});

$(".isactive").on('change', function() {
    $(".isactive").not(this).prop('checked', false);
});

$(".echildren").click(function(){
    addchildren($(this), $(this).attr("tbl_id"));
});

$(".delete_entry").click(function(){
    var mtable = $("#childrenlist").find("tbody");
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
        if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colspan='6'>No existing data</td></tr>");
        $(this).parent().parent().parent().remove();
        deletechildren($(this), $(this).attr("tbl_id"));
      } else if (
        result.dismiss === Swal.DismissReason.cancel
      ) {
        swalWithBootstrapButtons.fire(
          'Cancelled',
          'Data is safe.',
          'error'
        )
      }
    })
});

$(".deleterelation").click(function(){
    var mtable = $("#emergencycontactlist").find("tbody");
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
        if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colspan='6'>No existing data</td></tr>");
        $(this).parent().parent().parent().remove();
        deleterelation($(this), $(this).attr("tbl_id"));
      } else if (
        result.dismiss === Swal.DismissReason.cancel
      ) {
        swalWithBootstrapButtons.fire(
          'Cancelled',
          'Data is safe.',
          'error'
        )
      }
    })
    
});

$("a[tag='add_children']").click(function(){
    addchildren("");
});



$("a[tag='add_skill']").click(function(){
    addskill("");
});



$(".yesno").click(function(){
    var attname = $(this).attr("name");if($("input[name='"+attname+"']").prop("checked"))  
    $("input[name='"+attname+"']").not(this).prop("checked",false);
});

$("input[name='bdate']").blur(function(){
    dob = new Date($(this).val());
    var employeeid = $("#employeeid").val();
    var today = new Date();
    if(dob == 'Invalid Date'){
        $('#age').val(0);
        var formdata = {
                column:  GibberishAES.enc('age' , toks),
                value: GibberishAES.enc( 0 , toks),
                employeeid:  GibberishAES.enc(employeeid , toks),
                toks:toks
            };
        $.ajax({
            url: $("#site_url").val() + "/employee_/updateEmployeeInformation",
            data: formdata,
            type: "POST",
            success:function(response){
                loadSuccessModal();
            }
        });
    }
    else{
        var age = Math.floor((today-dob) / (365.25 * 24 * 60 * 60 * 1000));
        $('#age').val(age);
        var formdata = {
                column: GibberishAES.enc('age' , toks),
                value:  GibberishAES.enc(age , toks),
                employeeid:  GibberishAES.enc(employeeid , toks),
                toks:toks
            };
        $.ajax({
            url: $("#site_url").val() + "/employee_/updateEmployeeInformation",
            data: formdata,
            type: "POST",
            success:function(response){
                loadSuccessModal();
            }
        });
    }
    
});

// if($("#civil_status option:selected").text() == "MARRIED" || $("#civil_status option:selected").text() == "Married") $("#spouse,#occupation,#spouse_contact").attr("disabled",false).css("background-color","transparent");
// else $("#spouse,#occupation,#spouse_contact").val("").attr("disabled",true).css("background-color","#EEEEEE");

$("#civil_status").change(function(){
    // alert($("#civil_status option:selected").text());
    if($("#civil_status option:selected").text() == "MARRIED" || $("#civil_status option:selected").text() == "Married"){
        $("#spouse,#occupation,#spouse_contact").val("").attr("disabled",false).css("background-color","transparent");
        $("#spouseDetails").css("display", "block");
    }
    else{
        $("#spouse,#occupation,#spouse_contact").val("").attr("disabled",true).css("background-color","#EEEEEE");
        $("#spouse").focus();
        $("#spouseDetails").css("display", "none");
    }
});

$("input[type='checkbox']").click(function(){
    var name = $(this).attr("name");
    if(name == "healthcbox"){
        if($(this).val() == 2)  $("#txthealth").attr("disabled",true).css("background","#EEEEEE").val("");
        else                    $("#txthealth").attr("disabled",false).css("background","transparent").val("");
    }else if(name == "operationcbox"){
        if($(this).val() == 1)  $("#txtoperation,#txtoperationdate").attr("disabled",false).css("background","transparent").val("");
        else                    $("#txtoperation,#txtoperationdate").attr("disabled",true).css("background","#EEEEEE").val("");
    }else if(name == "medhiscbox"){
        if($(this).val() == 1)  $("#txtmedhis").attr("disabled",false).css("background","transparent").val("");
        else{           
            $("#txtmedhis").attr("disabled",true).css("background","#EEEEEE").val("");
        }
    }
});

                

$(".edit_estat_history").unbind().click(function(){
$("#modal-view").find(".modal-footer").html('<button type="button" data-dismiss="modal" class="btn btn-danger modalclose" id="modalclose">Close</button> <button type="button" class="btn btn-success button_save_modal" id="button_save_modal">Save</button>');
    
    var employeeid      = $("#employeeid").val(),
        management      = $(this).attr('mgmt'),
        deptid          = $(this).attr('dept'),
        office          = $(this).attr('office'),
        employmentstat  = $(this).attr('estat'),
        position        = $(this).attr('pos'),
        datepos         = $(this).attr('datepos');
        dateresigned    = $(this).attr('dateresigned');
        resigned_reason = $(this).attr('resigned_reason');

    $("#modal-view").find("h3[tag='title']").text("Edit Employment Status");
    $("#button_save_modal").text("Save");
    var form_data = {
        employeeid:  GibberishAES.enc(employeeid , toks),
        management:  GibberishAES.enc(management , toks),
        deptid:  GibberishAES.enc( deptid, toks), 
        office:  GibberishAES.enc(office , toks),
        employmentstat:  GibberishAES.enc(employmentstat , toks),
        position:  GibberishAES.enc( position, toks),
        datepos:  GibberishAES.enc( datepos, toks),
        dateresigned:  GibberishAES.enc(dateresigned , toks),
        resigned_reason:  GibberishAES.enc(resigned_reason , toks),
        folder :  GibberishAES.enc("employee" , toks),
        page   :  GibberishAES.enc( "estat_modal", toks),
        toks:toks
    };
    $.ajax({
        url: $("#site_url").val() + "/employee_/viewModal",
        type: "POST",
        data: form_data,
        success: function(msg){
            $("#modal-view").find("div[tag='display']").html(msg);
        }
    });  
});


$(document).on("click", '#del-submit', function(){
    var id = $(this).attr('tagkey');
    $("#modal-view").find("div[tag='display']").html("<h3>Deleting...</h3>");
    $.ajax({
        url: $("#site_url").val() + "/employee_/deleteEStatHistory",
        type: "POST",
        data: {estatid:GibberishAES.enc(id , toks), toks:toks},
        dataType: 'JSON',
        success: function(msg){
            $("#modal-view").find("div[tag='display']").html(msg.msg);
            $('.del-close').click();
            if(msg.err_code == 0){
                console.log(msg.msg);
                $('a[dstatid="'+id+'"').parent().parent().parent().remove();

            }else{
                console.log(msg.msg);
            }
        }
    });
});
var sss_exist = 0;
///< to remove error messages
$('.chosen, input').on('change', function(){
    $(this).nextAll('.error:first').hide();
});

$(".disabled").each(function(){ 
    $(this).attr("disabled",true).css("background","#EEEEEE").val("");  
});

$('input').on('input', function(){
    $(this).css('border-color','#8f8f8f').nextAll('.error:first').hide();
});

$("input, .chosen-select").on("change , blur", function() {
    // if($(this).parent('#personal_info').length > 0){
    //     return;
    // }
    var div = $(this).parent("div").last();
    // console.log(div);
    var column_name = $(this).attr("name");
    var column_value = $(this).val();
    var employeeid = $("#employeeid").val();
    var bank = $(this).attr("bank");
    if(column_name == 'empshift') column_value = empshift_updated;
    console.log(empshift_updated); 
    if(column_name == "tnt") return;
    if($("#aimcheckbox").prop('checked') && column_name == "aimcheckbox") column_value = 1;
    else{ 
        if(column_name == "aimcheckbox") column_value = 0;
    }

    if(column_name == "spouse_contact") column_value = $("input[name='spouse_contact']").val().replace("+63-", "");

    if(column_name != null && column_value != null && column_value != "" && employeeid != null && bank != null){
        var formdata = {
            column:  GibberishAES.enc(column_name , toks),
            value:  GibberishAES.enc( column_value, toks),
            employeeid:  GibberishAES.enc(employeeid , toks),
            bank:  GibberishAES.enc(bank , toks),
            toks:toks
        };
    }
    else{
        var formdata = {
            column:  GibberishAES.enc(column_name , toks),
            value:  GibberishAES.enc( column_value, toks),
            employeeid:  GibberishAES.enc(employeeid , toks),
            toks:toks
        };
    }
    
    $.ajax({
        url: $("#site_url").val() + "/employee_/updateEmployeeInformation",
        data: formdata,
        type: "POST",
        success:function(response){
            if(sss_exist == 0){
                if(response === 'exist!'){
                    if(bank){
                        $("input[bank="+bank+"]").css('border', '2px solid red').text('');
                        $("."+bank).text("This number is already used."); 
                    }else{
                       $("#"+column_name).css('border', '2px solid red').text('');
                       $("."+column_name).text("This number is already used."); 
                    }
                    sss_exist++;
                }else{
                    if (response == "EmailExist") {
                        $("#emailExistWork").show();
                        $("#snackbar").text("Email Existing");
                        $("#snackbar").css("background-color", "red");
                        setTimeout(function() {
                        $("#snackbar").text("Your information has been saved.");
                        $("#snackbar").css("background-color", "#333");
                        },   1800);
                    }
                    loadSuccessModal(column_name,bank);
                }
            }
        }
    });
    setTimeout(function(){
        if(bank){
            $("input[bank="+bank+"]").css('border', '1px solid #ccc');
            $("."+bank).text(""); 
        }else{
            $("#"+column_name).css('border', '1px solid #ccc');
            $("."+column_name).text(""); 
        }
    }, 5000);


    var allcard_data = {
        "PersonType": "E",
        "employeeid" : $("input[name='employeeid']").val(),
        "fname": $("input[name='fname']").val(),
        "mname": $("input[name='mname']").val(),
        "lname": $("input[name='lname']").val(),
        "deptid": $("#emphistory").find("#currentDept").text(),
        "positionid": $("#emphistory").find("#currentPos").text()
    };
    var api_col = ["employeeid", "fname", "mname", "lname", "currentDept", "currentPos"];
    if(api_col.includes(column_name)){
        $.ajax({
            url: $("#site_url").val() + "/employee_/updateEmployeeInfoApi",
            data: allcard_data,
            type: "POST",
            success:function(response){
            }
        });
    }

    sss_exist = 0;
});


setTimeout(
    function() {
        $(".widgets_area").removeClass("animated fadeIn");
    }, 2000
);

$(".update_status").click(function(){
    var current_status = $(this).text();
    // if(current_status == "APPROVED"){
    //     alert("This information is already APPROVED!");
    //     return;
    // }
    var table = $(this).closest("tr").attr("table");
    var id = $(this).closest("tr").attr("id");
    var status = updateTableStatus(table, id);
    $(this).html(status + " <span class='tooltiptext tooltiptext_"+id+"_"+table+"'>"+status+"</span>")
    // $(this).text(status)
    if(status == 'APPROVED') $(this).removeClass("btn-danger").addClass("btn-success");
    else $(this).removeClass("btn-success").addClass("btn-danger");
});

$(".tooltip").hover(function(){
    var id = $(this).attr('id');
    var table = $(this).attr('table');
    loadStatusHistory(id, table);
  });

$("#save_emp_aims").click(function(){
    var formdata = $("#form_employee_aims").serialize();
    $.ajax({
        type: "POST",
        url: $("#site_url").val() + "/employee_/addEmployeeToAims",
        data:formdata,
        dataType: "json",
        success:function(response){
            $("#aimcheckbox").attr("disabled", true);
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: response.msg,
                showConfirmButton: true,
                timer: 1000
            });
            // $(".nav-tabs > li.active > a, .nav-tabs > li.active > a:hover, .nav-tabs > li.active > a:focus").click();
            $("#add_employee_aims").hide();
        }
    });

});

function loadStatusHistory(id, table){
    $.ajax({
        url: $("#site_url").val() + "/employee_/loadStatusHistory",
        type: "POST",
        data: {id: GibberishAES.enc(id , toks), table: GibberishAES.enc(table , toks), toks:toks},
        success: function(history){ 
            if(history != '')  $(".tooltiptext_"+id+"_"+table).html(history);
            else $(".tooltiptext_"+id+"_"+table).html("No History");
        }
    });
  }
function updateTableStatus(table, id){
    var approverid = $("#approverid").val();
    var status = "";
    $.ajax({
        url: $("#site_url").val() + "/employee_/updateTableStatus",
        type:"POST",
        data: {table:  GibberishAES.enc( table, toks), id:  GibberishAES.enc(id , toks), approverid: GibberishAES.enc(approverid , toks), toks:toks},
        async: false,
        success:function(response){
          status = response;
        }
    });
    return status;
}

function deletefamily(obj, tbl_id = ""){
    var table = "";
    var userid = "";
    if($("input[name='applicantId']").val()){
        table = "applicant_children";
        userid = $("input[name='applicantId']").val();
    }
    else{
        table = "employee_family"; 
        userid = $("input[name='employeeid']").val();
    }
    $.ajax({
        url: $("#site_url").val() + "/employee_/deleteData",
        type: "POST",
        data: {table: GibberishAES.enc(table , toks), tbl_id: GibberishAES.enc( tbl_id, toks), employeeid:  GibberishAES.enc(userid , toks), toks:toks},
        dataType: "JSON",
        success: function(msg){ 
            loadTable('employee_family_table');
            Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: 'Successfully deleted!',
              showConfirmButton: true,
              timer: 1000
          })
        }
    });  
}





function loadCityAndProvinces(){
    $.ajax({
        url: $("#site_url").val() + "/employee_/loadExtrasFunction",
        type: "POST",
        data: {
          provid :  GibberishAES.enc(ProvID , toks),
          regCode:  GibberishAES.enc( regCode, toks),
          fnctn:  GibberishAES.enc("provincelist" , toks),
          toks:toks
        },
        success: function(msg){
           $("select[name='provaddr']").html(msg).trigger("chosen:updated");
           // $("select[name='municipality']").html('<option value="">Choose a municipality ...</option>').trigger("chosen:updated");
        }
    });

    $.ajax({
        url: $("#site_url").val() + "/employee_/loadExtrasFunction",
        type: "POST",
        data: {
          munid :  GibberishAES.enc( munid, toks),
          ProvID:  GibberishAES.enc(ProvID , toks),
          fnctn:  GibberishAES.enc("municipalitylist" , toks),
          toks:toks
        },
        success: function(msg){
           $("select[name='cityaddr']").html(msg).trigger("chosen:updated");           
        }
    });

     $.ajax({
            url: $("#site_url").val() + "/employee_/loadExtrasFunction",
            type: "POST",
            data: {
              brgyid :  GibberishAES.enc(brgyid , toks),
              munid:  GibberishAES.enc(munid , toks),
              fnctn:  GibberishAES.enc("barangaylist" , toks),
              toks:toks
            },
            success: function(msg){
                $("select[name='barangay']").html(msg).trigger("chosen:updated");
            }
        });
}


// function loadPermanentCityAndProvinces(){
//     $.ajax({
//         url: $("#site_url").val() + "/employee_/loadExtrasFunction",
//         type: "POST",
//         data: {
//           provid : permaProv,
//           regCode: permaReg ,
//           fnctn: "provincelist"
//         },
//         success: function(msg){
//            $("select[name='permaProvince']").html(msg).trigger("chosen:updated");
//            // $("select[name='municipality']").html('<option value="">Choose a municipality ...</option>').trigger("chosen:updated");
//         }
//     });

//     $.ajax({
//         url: $("#site_url").val() + "/employee_/loadExtrasFunction",
//         type: "POST",
//         data: {
//           munid : permaMun,
//           ProvID: permaProv ,
//           fnctn: "municipalitylist"
//         },
//         success: function(msg){
//            $("select[name='permaMunicipality']").html(msg).trigger("chosen:updated");
//         }
//     });
// }

function loadPermanentCityAndProvinces(terms='', region='', province='', barangay='', municipality=''){
    if(terms){
        permaProv = province;
        permaReg = region;
        permaMun = municipality;
        permaBar =barangay;
    }
    $.ajax({
        url: $("#site_url").val() + "/employee_/loadExtrasFunction",
        type: "POST",
        data: {
          provid :  GibberishAES.enc(permaProv , toks),
          regCode:   GibberishAES.enc(permaReg , toks),
          fnctn:  GibberishAES.enc( "provincelist", toks), 
          toks:toks
        },
        success: function(msg){
           $("select[name='permaProvince']").html(msg).trigger("chosen:updated");
           // $("select[name='municipality']").html('<option value="">Choose a municipality ...</option>').trigger("chosen:updated");
        }
    });

    $.ajax({
        url: $("#site_url").val() + "/employee_/loadExtrasFunction",
        type: "POST",
        data: {
          munid :  GibberishAES.enc(permaMun , toks),
          ProvID:  GibberishAES.enc(permaProv , toks),
          fnctn:  GibberishAES.enc("municipalitylist" , toks),
          toks:toks
        },
        success: function(msg){
            $("select[name='permaMunicipality']").html(msg).trigger("chosen:updated");
        }
    });

    $.ajax({
            url: $("#site_url").val() + "/employee_/loadExtrasFunction",
            type: "POST",
            data: {
              brgyid :  GibberishAES.enc(permaBar , toks),
              munid:  GibberishAES.enc(permaMun , toks),
              fnctn:  GibberishAES.enc("barangaylist" , toks),
              toks:toks
            },
            success: function(msg){
                $("select[name='permaBarangay']").html(msg).trigger("chosen:updated");
            }
        });

   
}

function UnloadPermaCityAndProvinces(){
    $("select[name='permaProvince']").html('<option value="">Choose a province ...</option>').trigger("chosen:updated");
    $("select[name='permaMunicipality']").html('<option value="">Choose a municipality ...</option>').trigger("chosen:updated");
    $("select[name='permaBarangay']").html('<option value="">Choose a barangay ...</option>').trigger("chosen:updated");
}

function addlegit(obj){
    $("#modal-view").find("h3[tag='title']").text(obj ? "Edit Legitimation Relations" : "Add Legitimation Relations");  
    $.ajax({
        url: $("#site_url").val() + "/employee_/legitimate",
        type: "POST",
        success: function(msg){
            var modal_display = $("#modal-view").find("div[tag='display']");
            $.when($(modal_display).html(msg)).done(function(){ 
               if(obj){
                 var tdcur = $(obj).parent().parent().parent();
                 $(tdcur).attr("iscurrent",1);
                 $(modal_display).find("input[name='lr_name']").val(tdcur.find("td:eq(0)").text()); 
                 //$(modal_display).find("input[name='lr_relationship']").val(tdcur.find("td:eq(1)").text());
                 $(modal_display).find("select[name='lr_relationship']").val(tdcur.find("td:eq(1)").attr("reldata")).trigger('chosen:updated');
                 $(modal_display).find("input[name='lr_address']").val(tdcur.find("td:eq(2)").text());
                 $(modal_display).find("input[name='lr_contactno']").val(tdcur.find("td:eq(3)").text()); 
                 $(modal_display).find("input[name='birthdate_lr']").val(tdcur.find("td:eq(4)").text());
                 $(modal_display).find("input[name='birthdate_lr']").parent().attr("data-date",tdcur.find("td:eq(4)").text());
                 $(modal_display).find("input[name='legit_lr']").prop("checked",tdcur.find("td:eq(5)").text()=="YES");
              }else{
                 $("#emergencycontactlist").find("tr").each(function(){
                   $(this).attr("iscurrent",0); 
                 }) 
              }
            });
        }
    });  
}
function call_shiftschedule(emptype,empshift){
    $.ajax({
        url: $("#site_url").val() + "/employee_/call_shiftschedule",
        type: "POST",
        data: {
          emptype:  GibberishAES.enc( emptype, toks),
          empshift:  GibberishAES.enc( empshift, toks),
          toks:toks
        },
        success: function(msg){
           $("select[name='empshift']").html(msg).trigger("chosen:updated");
           
        }
    });

    $.ajax({
        url: $("#site_url").val() + "/employee_/batchScheduleDateActive",
        type: "POST",
        data: {
          emptype:  GibberishAES.enc( emptype, toks),
          empshift:  GibberishAES.enc( empshift, toks),
          toks:toks  
        },
        success: function(response){
           $("input[name='date_active']").val(response);
           $("#date_active").parent("div").after('<span class="glyphicon glyphicon-ok-circle " id="date_active_saved" style="color: green; font-size: 20px; position: absolute; margin-top: 1.6%; margin-left: -1%;"><p style="font-family: "Poppins", sans-serif; font-size: 12px; margin-top: -45%; margin-left: 50%; font-weight: 600; color: green"></p></span>');
           setTimeout(function(){ $("#date_active_saved").fadeOut("slow") }, 1000);
            setTimeout(function(){ $("#date_active_saved").remove(); }, 2000);
           saveempshift(emptype, empshift);
        }
    });

}

function saveempshift(emptype, empshift){
    var employeeid = $("#employeeid").val();
    $.ajax({
        url: $("#site_url").val() + "/employee_/saveempshift",
        type: "POST",
        data: {
          emptype:  GibberishAES.enc( emptype, toks),
          empshift:  GibberishAES.enc( empshift, toks),
          employeeid: GibberishAES.enc( employeeid, toks),
          toks:toks
        },
        success: function(response){
            saveShiftSched(response);
            $("#empshift").parent("div").after('<span class="glyphicon glyphicon-ok-circle " id="empshift_saved" style="color: green; font-size: 20px; position: absolute; margin-top: 1.6%; margin-left: -1%;"><p style="font-family: "Poppins", sans-serif; font-size: 12px; margin-top: -45%; margin-left: 50%; font-weight: 600; color: green"></p></span>');
        }
    });
    setTimeout(function(){ $("#empshift_saved").fadeOut("slow") }, 1000);
    setTimeout(function(){ $("#empshift_saved").remove(); }, 2000);

    
        
}

function saveShiftSched(newempshift){
    $('#saveShiftSched').hide();   

    var newdateactive = $('input[name=date_active]').val();
    var prev_date_active = $('input[name=date_active]').val();

        $.ajax({
            url: $("#site_url").val() + "/employee_/saveShiftSchedule",
            type: "POST",
            dataType : 'JSON',
            data: {
                employeeid :  GibberishAES.enc($("#employeeid").val() , toks),
                tnt        :  GibberishAES.enc($("#teachingtype").val() , toks),
                empshift   :  GibberishAES.enc(newempshift , toks),
                date_active:  GibberishAES.enc(newdateactive , toks),
                prev_date_active:  GibberishAES.enc(prev_date_active , toks),
                toks:toks
            },
            success: function(msg){
                // $('#saveShiftSchedMsg').removeAttr('hidden');
                // $('#saveShiftSchedMsg').show();
                if(msg.err_code==0){
                    empshift = newempshift;
                    date_active = newdateactive;
                    prev_date_active = newdateactive;
                    // $("#saveShiftSchedMsg").html(msg.msg).css('color','green').delay(5000).fadeOut();
                }else{
                    // $('#saveShiftSched').removeAttr('hidden');
                    // $("#saveShiftSchedMsg").html(msg.msg).css('color','red').delay(5000).fadeOut();
                }
            }
        });
}

function ImmiFields (abled) {
    $("#txtPassport").attr("disabled",abled);
    $("#txtVisa").attr("disabled",abled);
    $("#txtICARD").attr("disabled",abled);
    $("#txtCNR").attr("disabled",abled);
}



function addchildren(obj, tbl_id = ""){
    $("#modal-view").find("h3[tag='title']").text(obj ? "Edit Children" : "Add Children");
    $("#button_save_modal").text("Save");  
    $.ajax({
        url: $("#site_url").val() + "/employee_/echildren",
        type: "POST",
        success: function(msg){
            var modal_display = $("#modal-view").find("div[tag='display']");
            $.when($(modal_display).html(msg)).done(function(){ 
               if(obj){
                 var tdcur = $(obj).parent().parent().parent();
                 $(tdcur).attr("iscurrent",1);
                 $(modal_display).find("input[name='eb_name']").val(tdcur.find("td:eq(0)").text()); 
                 $(modal_display).find("select[name='eb_gender']").val(tdcur.find("td:eq(1)").attr("reldata"));
                 $(modal_display).find("input[name='eb_b_order']").val(tdcur.find("td:eq(2)").text());
                 $(modal_display).find("input[name='eb_dob']").val(tdcur.find("td:eq(3)").text());
                 $(modal_display).find("input[name='eb_age']").val(tdcur.find("td:eq(4)").text());
                 $(modal_display).find("input[name='tbl_id']").val(tbl_id);
              }else{
                 $("#childrenlist").find("tr").each(function(){
                   $(this).attr("iscurrent",0); 
                 }) 
              }
            }); 
        }
    });  
}


function addskill(obj){
    $("#modal-view").find("h3[tag='title']").text(obj ? "Edit Skill" : "Add Skill");
    $("#button_save_modal").text("Save");  
    $.ajax({
        url: $("#site_url").val() + "/employee_/eSkill",
        type: "POST",
        success: function(msg){
            var modal_display = $("#modal-view").find("div[tag='display']");
            $.when($(modal_display).html(msg)).done(function(){ 
               if(obj){
                 var tdcur = $(obj).parent().parent();
                 $(tdcur).attr("iscurrent",1);
                 $(modal_display).find("input[name='eb_name']").val(tdcur.find("td:eq(0)").text()); 
                 $(modal_display).find("input[name='eb_yearOfUse']").val(tdcur.find("td:eq(1)").text());
                 $(modal_display).find("input[name='eb_level']").val(tdcur.find("td:eq(2)").text());
                }else{
                 $("#skilllist").find("tr").each(function(){
                   $(this).attr("iscurrent",0); 
                 }) 
              }
            }); 
        }
    });  
}

function processEmployeeAge(isload=false){
    if(!isload){
        dob = new Date($("input[name='bdate']").val());
        var employeeid = $("#employeeid").val();
        var today = new Date();
        if(dob == 'Invalid Date'){
            $('#age').val(0);
            var formdata = {
                    column:  GibberishAES.enc('age' , toks),
                    value:  GibberishAES.enc(0 , toks),
                    employeeid:  GibberishAES.enc( employeeid, toks),
                    toks:toks
                };
            $.ajax({
                url: $("#site_url").val() + "/employee_/updateEmployeeInformation",
                data: formdata,
                type: "POST",
                success:function(response){
                    loadSuccessModal(column);
                }
            });
        }
        else{
            var age = Math.floor((today-dob) / (365.25 * 24 * 60 * 60 * 1000));
            $('#age').val(age);
            var formdata = {
                    column:  GibberishAES.enc('age' , toks),
                    value:  GibberishAES.enc(age , toks),
                    employeeid:  GibberishAES.enc(employeeid , toks),
                    toks:toks
                };
            $.ajax({
                url: $("#site_url").val() + "/employee_/updateEmployeeInformation",
                data: formdata,
                type: "POST",
                success:function(response){
                    loadSuccessModal(column);
                }
            });
        }

        $("#age").val(age);
    }else{
        dob = new Date($("input[name='bdate']").val());
        var employeeid = $("#employeeid").val();
        var today = new Date();
        var age = Math.floor((today-dob) / (365.25 * 24 * 60 * 60 * 1000));
        $('#age').val(age);
        var formdata = {
                column:  GibberishAES.enc('age' , toks),
                value:  GibberishAES.enc(age , toks),
                employeeid:  GibberishAES.enc(employeeid , toks),
                toks:toks
            };
        $.ajax({
            url: $("#site_url").val() + "/employee_/updateEmployeeInformation",
            data: formdata,
            type: "POST",
            success:function(response){
            }
        });
    }
}

function loadEmpHistoryTable(){
    var employeeid = $("#employeeid").val();
    var deptid = $("#departmentEH").val();
    var office = $("#officeEH").val();
    var employmentstat = $("#employmentstatEH").val();
    var position = $("#positionEH").val();
    var datepos = $("#dateposEH").val();
    var dateres = $("#dateresEH").val();
    var formdata = {
        employeeid:  GibberishAES.enc(employeeid , toks),
        deptid:  GibberishAES.enc(deptid , toks),
        office:  GibberishAES.enc(office , toks),
        employmentstat:  GibberishAES.enc(employmentstat , toks),
        position:  GibberishAES.enc(position , toks),
        datepos:  GibberishAES.enc(datepos , toks),
        dateres:  GibberishAES.enc(dateres , toks),
        toks:toks
    };
    $.ajax({
        url: $("#site_url").val() + "/employee_/historyTable",
        data: formdata,
        type: "POST",
        success:function(response){
            $("#emphistory").html(response);
        }
    });
}

function loadSuccessModal(column_name, isbank=''){
    // if(!$('.success_modal').is(':visible')){
    //     $(".success_modal").modal("show");
    //     setTimeout(function(){ $(".success_modal").modal("hide"); }, 800);
    // }

    if(column_name == "nationalityid") column_name = "selNationality";
    if(column_name == "citizenid") column_name = "citizenship";
    if(column_name == "religionid") column_name = "religion";
    if(column_name == "spouse_name") column_name = "spouse";
    if(column_name == "regionaladdr") column_name = "region";
    if(column_name == "provaddr") column_name = "selProvince";
    if(column_name == "cityaddr") column_name = "selMunicipality";
    if(column_name == "permaRegion") column_name = "permaRegionselect";
    if(column_name == "dateemployed" ){
        $("#"+column_name+"_saveAlert").after('<span class="glyphicon glyphicon-ok-circle " id="'+column_name+'_saved" style="color: green; font-size: 20px; position: absolute; margin-top: 1.6%; margin-left: 2%;"><p style="font-family: "Poppins", sans-serif; font-size: 12px; margin-top: -45%; margin-left: 50%; font-weight: 600; color: green"></p></span>');
    }else if(column_name == "prc_expiration" || column_name == "passport_expiration" || column_name == "date_active" || column_name == "bdate"){
        $("#"+column_name+"_saveAlert").after('<span class="glyphicon glyphicon-ok-circle " id="'+column_name+'_saved" style="color: green; font-size: 20px; position: absolute; margin-top: 1.6%; margin-left: 5%;"><p style="font-family: "Poppins", sans-serif; font-size: 12px; margin-top: -45%; margin-left: 50%; font-weight: 600; color: green"></p></span>');
    }
    else if(column_name == "dateresigned"){
        $("#clearResigned").after('<span class="glyphicon glyphicon-ok-circle " id="'+column_name+'_saved" style="color: green; font-size: 20px; position: absolute; margin-top: 3%; margin-left: 28%;"><p style="font-family: "Poppins", sans-serif; font-size: 12px; margin-top: -45%; margin-left: 50%; font-weight: 600; color: green"></p></span>');
    }else if(column_name == "teachingtype" || column_name == "isactive"){
        $("#"+column_name).parent("div").parent("div").after('<span class="glyphicon glyphicon-ok-circle " id="'+column_name+'_saved" style="color: green; font-size: 20px; position: absolute; margin-top: 1%; margin-left: -15%;"><p style="font-family: "Poppins", sans-serif; font-size: 12px; margin-top: -45%; margin-left: 50%; font-weight: 600; color: green"></p></span>');
    }else if(column_name == "resigned_reason"){
        $("#"+column_name).parent("div").after('<span class="glyphicon glyphicon-ok-circle " id="'+column_name+'_saved" style="color: green; font-size: 20px; position: absolute; margin-top: 1%; margin-left: 2%;"><p style="font-family: "Poppins", sans-serif; font-size: 12px; margin-top: -45%; margin-left: 50%; font-weight: 600; color: green"></p></span>');
    }else if(column_name == "aimcheckbox"){
        $("#"+column_name).parent("div").after('<span class="glyphicon glyphicon-ok-circle " id="'+column_name+'_saved" style="color: green; font-size: 20px; position: absolute; margin-top: 0%; margin-left: -70%;"><p style="font-family: "Poppins", sans-serif; font-size: 12px; margin-top: -45%; margin-left: 50%; font-weight: 600; color: green"></p></span>');
    }else if(isbank){
        column_name = isbank;
        $("input[bank='"+isbank+"']").after('<span class="glyphicon glyphicon-ok-circle " id="'+column_name+'_saved" style="color: green; font-size: 20px; position: absolute; margin-top: 1%; margin-left: 30%;"><p style="font-family: "Poppins", sans-serif; font-size: 12px; margin-top: -45%; margin-left: 50%; font-weight: 600; color: green"></p></span>')
    }else{
        $("#"+column_name).parent("div").after('<span class="glyphicon glyphicon-ok-circle " id="'+column_name+'_saved" style="color: green; font-size: 20px; position: absolute; margin-top: 1.6%; margin-left: -1%;"><p style="font-family: "Poppins", sans-serif; font-size: 12px; margin-top: -45%; margin-left: 50%; font-weight: 600; color: green"></p></span>');
    }
    // var x = document.getElementById("snackbar");
    // x.className = "show";
    // setTimeout(function(){ x.className = x.className.replace("show", ""); }, 1500);
    setTimeout(function(){ $("#"+column_name+"_saved").fadeOut("slow") }, 1000);
    setTimeout(function(){ $("#"+column_name+"_saved").remove(); }, 2000);
}

function loadUploadedPhoto(){
    var employeeid = $("#employeeid").val();
    var formdata = {
        employeeid:  GibberishAES.enc(employeeid , toks),
        toks:toks
    };
    $.ajax({
        url: $("#site_url").val() + "/employee_/loadUploadedPhoto",
        data: formdata,
        type: "POST",
        success: function(response) {
            $(".elfinderimg").removeAttr("src").attr("src", response).css("border", "2px solid #a1a1a1");
        }
    });
}

function loadTable(view){
  $("."+view).html("<div style='white-space: nowrap;'>Loading data, please Wait..</div>");
  $.ajax({
    url: $("#site_url").val() + "/extensions_/loadTableDatas",
    type: "POST",
    data: {
      employeeid : GibberishAES.enc($("#employeeid").val(),toks),
      view : GibberishAES.enc(view,toks),
      toks:toks
    },
    success:function(response){
      $("."+view).html(response);
    }
 });
}