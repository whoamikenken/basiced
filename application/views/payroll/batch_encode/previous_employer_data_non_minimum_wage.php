<!-- get count of gross income config -->
<?php
    $gross_income = count($gross_income_config['allconfig']);
?>
<br>
<table class="table table-bordered" id="tabledata" style="width:100%;">
<tag id="fixedday" style="display:none;">0<tag>
<br>
   <thead>
        <tr valign="center" style="background-color: #000000; color: white;">
            <th colspan="10" style="text-align: center;">GROSS COMPENSATION INCOME</th>
        </tr>

        <tr valign="center" style="background-color: #000000; color: white;">
            <th colspan="10" style="text-align: center;">PREVIOUS EMPLOYER</th>
        </tr>

        <tr valign="center" style="background-color: #000000; color: white;">
            <th colspan="7" style="text-align: center;">NON TAXABLE</th>
            <th colspan="3" style="text-align: center;">TAXABLE</th>
        </tr>

        <tr valign="center" style="background-color: #000000; color: white;">
             <th style="text-align: center;">Employee ID</th>
             <th style="text-align: center;">Fullname</th>

            <?php foreach($gross_income_config['nonminimum'] as $key => $config): ?>
                <th id="<?= $config['description']?>" style="text-align: center;"><?= $config['description']?></th>
            <?php endforeach ?>
        </tr>
    </thead>
    <tbody id="tablebody">
            <!-- foreach to get all minimum wage employee -->
           
               <?php foreach($employee_records as $empid => $data): ?>
                    <?php foreach($data as $key => $employee): ?>
                        <tr id="row-<?= $empid?>" employeeid='<?= $empid ?>'>  
                            <td><p type="text" class="empid" id="empid"><?= $empid ?></td>
                            <td><?= $key ?></td>
                                <?php foreach($employee as $code => $value): ?>
                                    <?php if($value) {?>
                                        <td style="float:center;"><input type="text" class="<?=$code?>" id="<?=$code?>" value="<?= $value?>"></td>
                                    <?php }?>
                                <?php endforeach ?>
                            <?php if(empty($value)) {?>
                                <?php foreach($gross_income_config['nonminimum'] as $code => $value): ?>
                                    <td style="float:center;"><input type="text" class="<?=$value['code']?>" id="<?=$value['code']?>" value=""></td>
                                <?php endforeach ?>
                            <?php }?>
                        </tr>
                  <?php endforeach ?>
                <?php endforeach ?>
    </tbody>
</table>
 <script type="text/javascript">
        $j(document).ready(function(){
            var payroll_table;

            if ( $.fn.DataTable.isDataTable('#tabledata') ) {
              $j('#tabledata').DataTable().destroy();
              oTable.fnAdjustColumnSizing();
            }

            setTimeout(function(){
                      payroll_table = $("#tabledata").dataTable({
                    "sPaginationType": "full_numbers",
                    "oLanguage": {
                                     "sEmptyTable":     "No Data Available.."
                                 },
                    "aLengthMenu": [[10], [10]],
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
            $("#tabledata").on("mouseleave mouseover","tr.even, tr.odd",function(e){
                // console.log(e);
                var i = $(this).index();
                var type = e.type=="mouseover";

                $(this).toggleClass("active",type);
                //left Table or fixed columns
                $(".DTFC_Cloned > tbody").find("tr").eq(i).toggleClass("active",type);
                //right Table
                $("#tabledata > tbody").find("tr").eq(i).toggleClass("active",type);
             });
    });
     
     $("#tabledata").find("input").change(function(){
        // variable
        var isValid = true;
        var inputAmounts = {};

        var tr_id = $(this).closest('tr').attr('id');
        <?php foreach($gross_income_config['nonminimum'] as $key => $config): ?>
            var <?= $config['code']?> = $("tr[id='"+ tr_id +"']").find(".<?= $config['code']?>").val();
        <?php endforeach ?>

        $("#tabledata").find("#tablebody").find($("tr[id='"+ tr_id +"']")).find("input").each(function() {
            var element = $(this);
            if (element.val() == "") {
                isValid = false;
            }
            else{
                isValid = true;
            }

            inputAmounts[$(this).attr("class")] = $(this).val();
            var split_tr = tr_id.split('-');
            inputAmounts['empid'] = split_tr[1];   
            inputAmounts['year'] = $("#pyear").val();
            inputAmounts['fixedday'] = $("#fixedday").text();         
        }); 
        if(isValid){
            $("tr[id='"+ tr_id +"']").css({'background-color':'#99ff99'});
            saveGrossIncome(inputAmounts);
        } 
        else{
            $("tr[id='"+ tr_id +"']").css({'background-color':'#ff6666'});
        }     
    });

    function saveGrossIncome(inputAmounts){
        $.ajax({
            type:"POST",
            url:"<?= site_url('payroll_/saveGrossIncomeOfMinWageEmployee')?>",
            data:inputAmounts,
            success:function(response){
                console.log(response);
            }
        });
     }

$(".chosen").chosen();
$(".chzn-select").chosen();
 </script>