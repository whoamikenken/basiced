    var toks = hex_sha512(" ");
    getPayrollRank();
    $('.add_leclabpay').on('click',function(){
        var add_ = $(this).parent().parent().clone(true);
        add_.find('.lechour').val(0);
        add_.find('.labhour').val(0);
        add_.find('.aimsdept').val('');
        // console.log(add_);
        $(this).closest('tr').find('.wrap_leclabpay').append(add_);
    });

    $('.del_leclabpay').on('click',function(){

        var main_parent_ = $(this).parent().parent().parent();
        var sub_parent_ = $(this).parent().parent();
        var deleted_val = $(sub_parent_).find('.lechour').val() + '~u~' + $(sub_parent_).find('.labhour').val() + '~u~' + $(sub_parent_).find('.aimsdept').val();

        if($(main_parent_).find('.leclab-pay').length > 1){
            if($(sub_parent_).find('.lechour').val() == 0 && $(sub_parent_).find('.labhour').val() == 0 && $(sub_parent_).find('.aimsdept').val() == ''){

            }else{
                var tr_ = $(this).closest('tr');
                var to_update = validateChanges(tr_,true,deleted_val);

                changeStatusTag(to_update,tr_);
            }

            $(sub_parent_).remove();
        }else{
            if($(sub_parent_).find('.lechour').val() == 0 && $(sub_parent_).find('.labhour').val() == 0 && $(sub_parent_).find('.aimsdept').val() == ''){

            }else{
                var tr_ = $(this).closest('tr');
                var to_update = validateChanges(tr_,true,deleted_val);

                changeStatusTag(to_update,tr_);

                $(sub_parent_).find('.lechour').val(0);
                $(sub_parent_).find('.labhour').val(0);
                $(sub_parent_).find('.aimsdept').val('');
            }
        }
    });

    $("input[name=monthly], input[name=semimonthly], input[name=daily], input[name=hourly], input[name=minutely], .schedule, .tax_status, .lechour, .labhour, .aimsdept").on('change',function(){
        var tr_ = $(this).closest('tr');
        var to_update = validateChanges(tr_,false,'');

        var tr_ = $(this).closest('tr');
        var to_update = validateChanges(tr_,false,'');
        var checkmonthly = $(tr_).find('.monthly').val();
        var checksemimonthly = $(tr_).find('.semimonthly').val();
        var checkdaily = $(tr_).find('.daily').val();
        var checkhourly = $(tr_).find('.hourly').val();
        var checkminutely = $(tr_).find('.minutely').val();
        var checkschedule = $(tr_).find('.schedule').val();
        var checktax_status = $(tr_).find('.tax_status').val();

        if(checkmonthly == "0" || checkmonthly == "0.00" || checkmonthly == "" || checksemimonthly == "0" || checksemimonthly == "0.00" || checksemimonthly == "" || checkdaily == "0" || checkdaily == "0.00" || checkdaily == "" || checkhourly == "0" || checkhourly == "0.00" || checkhourly == "" || checkminutely == "0" || checkminutely == "0.00" || checkminutely == "" || checkschedule == "" || checktax_status == ""){
             $(tr_).css({'background-color':'#ff6666'});
        }
        if(checkmonthly != "0" && checkmonthly != "0.00" && checkmonthly != "" && checksemimonthly != "0" &&  checksemimonthly != "0.00" &&  checksemimonthly != "" && checkdaily != "0" &&  checkdaily != "0.00" &&  checkdaily != "" && checkhourly != "0" &&  checkhourly != "0.00" &&  checkhourly != "" && checkminutely != "0" &&  checkminutely != "0.00" &&  checkminutely != "" && checkschedule != "" && checktax_status != ""){
              $(tr_).css({'background-color':'#99ff99'});
              changeStatusTag(to_update,tr_);
              saveBESalary();
              $(tr_).find('.dataStatus').show();
              
        }

        changeStatusTag(to_update,tr_);

    });

    $(".lechour, .labhour, .aimsdept").on('change',function(){
        var tr_ = $(this).closest('tr');
        var to_update = validateChanges(tr_,false,'');

        changeStatusTag(to_update,tr_);
    });

    $("input[name=isFixed]").on('click',function(){
        var tr_ = $(this).closest('tr');
        var to_update = validateChanges(tr_,false,'');

        changeStatusTag(to_update,tr_);
    });

    $(".type").on('change',function(){
        var id = $(this).val();
        var tr_ = $(this).closest('tr');
        getRankByType(id, tr_);
    });

    $(".rank").on('change',function(){
        var daily = 0;
        var hourly = 0;
        var minutely = 0;
        var tr_ = $(this).closest('tr');
        var id = $(this).val();
        var teachingtype = $(this).attr('teachingtype');
        $.ajax({
            url: $("#site_url").val() + "/setup_/getRankBasicRate",
            type: "POST",
            data:{id:GibberishAES.enc(id, toks), toks:toks},
            success:function(basic_rate){
               $(tr_).find('input[name="monthly"]').val(basic_rate).change();
               var college = $(this).attr('iscollege');
               if(college){
                    if(teachingtype == "teaching"){
                        daily = basic_rate / 26.17;
                        hourly = basic_rate / 88.2;
                        minutely = hourly / 60;
                    }else{
                        daily = basic_rate / 26.17;
                        hourly = basic_rate / 91;
                        minutely = hourly / 60;
                    }
               }else{
                    daily = basic_rate / 26.17;
                    hourly = basic_rate / 91;
                    minutely = hourly / 60;
               }

               daily = parseFloat(daily).toFixed(2);
               hourly = parseFloat(hourly).toFixed(2);
               minutely = parseFloat(minutely).toFixed(2);

               $(tr_).find('input[name="semimonthly"]').val(basic_rate/2)
               $(tr_).find('input[name="daily"]').val(daily);
               $(tr_).find('input[name="hourly"]').val(hourly);
               $(tr_).find('input[name="minutely"]').val(minutely);
            }
        });
    });

    /*$("input[name=monthly], input[name=semimonthly], input[name=daily], input[name=hourly], input[name=minutely]").on('keyup',function(){
        var tr_ = $(this).closest('tr');
        $(tr_).find(".rank").val('');
        $(tr_).find(".type").val('');
    });*/

    function getRankByType(id, tr_, onload = false){
        var existing_rank = $(tr_).find(".existing_rank").val();
        // if(!id) return;
        $.ajax({
            url: $("#site_url").val() + "/setup_/getRankByType",
            type: "POST",
            data:{id:GibberishAES.enc(id, toks), toks:toks},
            success:function(response){
                if(!onload) $(tr_).find(".rank").html(response).trigger("liszt:updated").change();
                else $(tr_).find(".rank").html(response).trigger("liszt:updated");
                // console.log(response);
                // console.log(existing_rank);
                // if(existing_rank) $(tr_).find(".rank").val(existing_rank).trigger("liszt:updated");
            }
        });
    }

    function saveBESalary(){

            var form_data = {};
            var hasChanges = false;

            $('.data-list').each(function(){
                if($(this).attr('status-tag') == 'NOTSAVED'){
                    hasChanges = true;

                    employeeid = $(this).attr('employeeid');
                    form_data[employeeid] = {};
                    form_data[employeeid]['eid']            = GibberishAES.enc(employeeid, toks);
                    form_data[employeeid]['isFixed']        = $(this).find("input[name=isFixed]").is(':checked') ? 1 : 0;
                    form_data[employeeid]['monthly']        = GibberishAES.enc($(this).find("input[name=monthly]").val(), toks);
                    form_data[employeeid]['semimonthly']    = GibberishAES.enc($(this).find("input[name=semimonthly]").val(), toks);
                    form_data[employeeid]['daily']          = GibberishAES.enc($(this).find("input[name=daily]").val(), toks);
                    form_data[employeeid]['hourly']         = GibberishAES.enc($(this).find("input[name=hourly]").val(), toks);
                    form_data[employeeid]['minutely']       = GibberishAES.enc($(this).find("input[name=minutely]").val(), toks);
                    form_data[employeeid]['sched']          = GibberishAES.enc($(this).find("select[name=schedule]").val(), toks);
                    form_data[employeeid]['tax_status']     = GibberishAES.enc($(this).find("select[name=tax_status]").val(), toks);
                    form_data[employeeid]['rank']           = GibberishAES.enc($(this).find("select[name=rank]").val(), toks);
                    form_data[employeeid]['type']           = GibberishAES.enc($(this).find("select[name=type]").val(), toks);


                    var leclab_arr = [];
                    var temparr = {};
                    var lechour = labhour = aimsdept = '';

                    $(this).find('.leclab-pay').each(function(){
                        lechour = $(this).find('.lechour').val();
                        labhour = $(this).find('.labhour').val();
                        aimsdept = $(this).find('.aimsdept').val();

                        temparr = {'lechour':lechour,'labhour':labhour,'aimsdept':aimsdept};
                        leclab_arr.push(temparr);
                        temparr = {};
                    });

                    form_data[employeeid]['leclab_arr'] = JSON.stringify(leclab_arr);

                }
            });


            if(!hasChanges){
                $('#errorMsg').html("NO CHANGES WERE MADE.").css('color','red');
                return false;
            }

            $.ajax({
                url : $("#site_url").val() + "/payroll_/saveSalaryBatch",
                type: "POST",
                dataType: 'json',
                data: {form_data:form_data, toks:toks},
                success: function(msg){
                   
                }
            });
        
    }


    $(".lechour").keyup(function(e){
         if (e.keyCode === 9) return false;   
         lechour = $(this).val();
         labhour = Number(lechour) * 0.75;
         $(this).parent().find('.labhour').val(addCommas(labhour));
    });

    $(".labhour").keyup(function(e)
    {
         if (e.keyCode === 9) return false;   
         labhour = $(this).val();
         lechour = (Number(labhour) / 3)*4;
         $(this).parent().find('.lechour').val(addCommas(lechour));
    });

    $("input[name=monthly]").keyup(function(e){
       if (e.keyCode === 9) return false;   

       var tr_ = $(this).closest('tr'); 
       var teachingtype = $(tr_).attr('teachingtype'); 
       var iscollege = $(tr_).attr('iscollege');
       var monthly = $(this).val();

       computeSalary(tr_,monthly,'monthly',teachingtype,iscollege);
    });
    
    $("input[name=semimonthly]").keyup(function(e){
       if (e.keyCode === 9) return false;

       var tr_ = $(this).closest('tr');  
       var teachingtype = $(tr_).attr('teachingtype'); 
       var iscollege = $(tr_).attr('iscollege');
       var monthly = $(this).val() * 2;

       computeSalary(tr_,monthly,'semimonthly',teachingtype,iscollege);
    });

    $("input[name=daily]").keyup(function(e){
       if (e.keyCode === 9) return false; 

       var tr_ = $(this).closest('tr');  
       var teachingtype = $(tr_).attr('teachingtype'); 
       var monthly = getMonthlyFromDaily($(this).val(),teachingtype);
       var iscollege = $(tr_).attr('iscollege');

       computeSalary(tr_,monthly,'monthly',teachingtype,iscollege);
    });
    
    $("input[name=hourly]").keyup(function(e){
       if (e.keyCode === 9) return false; 

       var tr_ = $(this).closest('tr');  
       var teachingtype = $(tr_).attr('teachingtype'); 
       var iscollege = $(tr_).attr('iscollege');
       var daily = $(this).val() * 8;
       var monthly = getMonthlyFromDaily(daily,teachingtype);

       computeSalary(tr_,monthly,'monthly',teachingtype,iscollege);
       
    });
    
    $("input[name=minutely]").keyup(function(e){
       if (e.keyCode === 9) return false; 

       var tr_ = $(this).closest('tr');  
       var teachingtype = $(tr_).attr('teachingtype'); 
       var iscollege = $(tr_).attr('iscollege');
       var daily = $(this).val() * 8 * 60;
       var monthly = getMonthlyFromDaily(daily,teachingtype);

       computeSalary(tr_,monthly,'monthly',teachingtype,iscollege);
       
    });

    $(".chosen").chosen();

    $("#be_salary").dataTable({
        "pagination": "number",
        "oLanguage": {
                         "sEmptyTable":     "No Data Available.."
                     },
        "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]],
        // "scrollY": 1000,
        "scrollX": true
    });

    function getPayrollRank(){
        $("#be_salary").find("tr").each(function(){
            var tr_ = $(this).closest('tr');  
            var type = $(tr_).find('.type').val();
            getRankByType(type, tr_, true); 
        });
    }

    function getMonthlyFromDaily(daily,teachingtype){
        var monthly = 0;
        if(teachingtype == 'teaching') monthly = (daily * 262) / 12;
        else                           monthly = (daily * 314) / 12;
        return monthly;
    }

    function computeSalary(tr_,monthly,input_type='monthly',teachingtype='teaching',college){
        var semimonthly = monthly / 2;
      /*  var college = $("#college").val();
        if(college){
            if(teachingtype == "teaching"){
                daily = monthly / 26.17;
                hourly = monthly / 88.2;
                minutely = hourly / 60;
            }else{
                daily = monthly / 26.17;
                hourly = monthly / 91;
                minutely = hourly / 60;
            }
        }else{
            daily = monthly / 26.17;
            hourly = monthly / 91;
            minutely = hourly / 60;
        }*/

        if(input_type != 'monthly'){
            $(tr_).find('input[name=monthly]').val(monthly.toFixed(2));
        }
        if(input_type != 'semimonthly'){
            $(tr_).find('input[name=semimonthly]').val(semimonthly.toFixed(2));
        }
        if(input_type != 'daily'){
            daily  = floorFigure(Number((monthly*12)/360));
            $(tr_).find('input[name=daily]').val(daily);
        }
        if(input_type != 'hourly'){
            hourly  = floorFigure(Number(daily/8));
            $(tr_).find('input[name=hourly]').val(hourly);
            minutely  = floorFigure(Number((hourly)/60));
            $(tr_).find('input[name=minutely]').val(minutely);
        }

    }


    ///< @Angelica - validations - save only those that are changed
    
    function validateChanges(tr_,isDelBtn=false,deleted_val){
        var to_update = false;

        ///< fixed rate
        var fixedrate_cb = $(tr_).find('input[name=isFixed]');
        var isFixed = $(fixedrate_cb).is(':checked') ? 1 : 0;
        if($(fixedrate_cb).attr('oldvalue') != isFixed) to_update = true;
        else                                            to_update = false;

        if(to_update) return to_update;

        ///< monthly
        var monthly_input = $(tr_).find('input[name=monthly]');
        if($(monthly_input).attr('oldvalue') != $(monthly_input).val()) to_update = true;
        else                                                            to_update = false;

        if(to_update) return to_update;

        ///< schedule
        var schedule = $(tr_).find('select[name=schedule]');
        if($(schedule).attr('oldvalue') != $(schedule).val()) to_update = true;
        else                                                  to_update = false;

        if(to_update) return to_update;

        ///< tax status
        var tax_status = $(tr_).find('select[name=tax_status]');
        if($(tax_status).attr('oldvalue') != $(tax_status).val())   to_update = true;
        else                                                        to_update = false;

        if(to_update) return to_update;

        ///< leclabrate
        var wrap_leclabpay = $(tr_).find('.wrap_leclabpay');
        to_update = validateStatusUpdateLecLabRate(wrap_leclabpay,isDelBtn,deleted_val);

        return to_update;
    }

    function validateStatusUpdateLecLabRate(wrap_leclabpay,isDelBtn,deleted_val){
        var to_update = false;
        var newvalues_list = [];
        var oldvalues_list = [];
        var newvalue = '';
        var oldvalue = '';

        $(wrap_leclabpay).find('.leclab-pay').each(function(){
            newvalue = $(this).find('.lechour').val() + '~u~' + $(this).find('.labhour').val() + '~u~' + $(this).find('.aimsdept').val();
            newvalues_list.push(newvalue);
        });
        $(wrap_leclabpay).find('.old-value').each(function(){
            oldvalues_list.push($(this).val());
        });


        if(isDelBtn){
            if(oldvalues_list.includes(deleted_val)) to_update = true;
        }else{
            for (i = 0; i < oldvalues_list.length; i++) { 
                oldvalue = oldvalues_list[i];

                if(!newvalues_list.includes(oldvalue)) to_update = true;
                if(to_update == true) break;
            }
            for (i = 0; i < newvalues_list.length; i++) { 
                newvalue = newvalues_list[i];

                if(!oldvalues_list.includes(newvalue)) to_update = true;
                if(to_update == true) break;
            }
        }

        return to_update;
    }


    function changeStatusTag(to_update,tr_){
        if(to_update)   updateStatusTag(tr_);
        else            removeStatusTag(tr_);
    }

    function updateStatusTag(tr_){
        $(tr_).find('.dataStatus').hide();
        $(tr_).attr('status-tag','NOTSAVED');
        $(tr_).find('.status-tag').html('NOT SAVED').css('color','red');
    }

    function removeStatusTag(tr_){
        $(tr_).attr('status-tag','');
        $(tr_).find('.status-tag').html('');
    }


    function addCommas(nStr)
    {
        nStr += '';
        x = nStr.split('.');
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        //return x1 + x2;
        return nStr;
    }


    function numbersonly(myfield, e, dec, id)
    {
        var key;
        var keychar;
            
        if (window.event)   key = window.event.keyCode;
        else if (e)         key = e.which;
        else                return true;
        keychar = String.fromCharCode(key);
            
        // control keys
        if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) ) return true;
            
        // numbers
        else if (((id ? "0123456789.- " : "0123456789.").indexOf(keychar) > -1))   return true;
            
        // decimal point jump
        else if (dec && (keychar == "."))
        {
            myfield.form.elements[dec].focus();
            return false;
        }
        else    return false;
    }

    function floorFigure(figure, decimals){
        if (!decimals) decimals = 2;
        var d = Math.pow(10,decimals);
        return (parseInt(figure*d)/d).toFixed(decimals);
    }

