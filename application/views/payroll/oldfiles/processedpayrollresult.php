<table class="table table-striped table-bordered table-hover datatable" id="dble">
    <thead>
        <?
        if($dept != ""){
        ?>
        <tr>
            <th colspan="11" class="align_center"><?=$departments[$dept]?></td>
        </tr>
        <?
        }
        ?>
        <tr>
        <!-- <?=$countincome;?> -->
            <th colspan='2' class="align_center">Information</td>
            <th colspan='<?=2+($countincome);?>' class="align_center">Earnings</td>
            <th colspan='10' class="align_center">Deductions</td>
        </tr>
        <tr class="ititle">
            <th class="align_center" height='40'>Employee Id</th>
            <th class="align_center">Name</th>
            <th class="align_center">Regular Pay</th>
            
            <?
            if($this->payrolloptions->incometitlep('',$schedule,$quarter,$sdate,$edate)){
                $incomes = $this->payrolloptions->incometitlep('',$schedule,$quarter,$sdate,$edate);
                foreach($incomes as $k => $row){

                    if($row){
                        $incomes[$k] = array("id"=>$row,"description"=>$this->payrolloptions->incomedesc($row));
                    ?>
                    <?php if ($incomes[$k]['description'] != "" || $incomes[$k]['description']!=null): ?>
                       <th class="align_center incometype"><?=$incomes[$k]['description']?></th> <!-- INCOME TITLE--> 
                    <?php endif ?>
                       
                    <?
                    }
                }
            }
            ?>
            
            <th class="align_center">Overtime</th>
            <th class="align_center">WithHolding Tax</th>
            
            <?
            if($this->payrolloptions->deducttitlep('',$schedule,$quarter,$sdate,$edate)){
                foreach($this->payrolloptions->deducttitlep('',$schedule,$quarter,$sdate,$edate) as $row){
                    if($row){
                    ?>
                        <th class="align_center fixdeductiontype"> <?=$row?></th> <!-- FIXED DEDUCTIONS TITLE-->
                    <?
                    }
                }
            }
            ?>
            
            <?   
            if($this->payrolloptions->loantitlep('',$schedule,$quarter,$sdate,$edate)){
                $loan = $this->payrolloptions->loantitlep('',$schedule,$quarter,$sdate,$edate);
                foreach($loan as $k => $row){
                    $loan[$k] = array("id"=>$row,"description"=>$this->payrolloptions->loandesc($row));
                    if($row){
                    ?>
                        <th class="align_center loantype" ><?=$loan[$k]["description"]?></th> <!-- LOANS TITLE-->
                    <?
                    }
                }
            }
            ?>
            
            <?
            if($this->payrolloptions->deducttitleothp('',$schedule,$quarter,$sdate,$edate)){
                $deductionsOthers = $this->payrolloptions->deducttitleothp('',$schedule,$quarter,$sdate,$edate);

                foreach($deductionsOthers as $k => $row){
                    if($row){
                        $deductionsOthers[$k] = array("id"=>$row,"description"=>$this->payrolloptions->deductiondesc($row));
                    ?>
                        <th class="align_center deductiontype"><?=$deductionsOthers[$k]["description"]?></th> <!-- OTHER DEDUCTIONS TITLE-->
                    <?
                    }
                }
            }
            ?>
            
            <th class="align_center">Tardy <?=date('M',strtotime($this->payrolloptions->dtrdeductdisplay('',$schedule,$quarter,$sdate,$edate,'tardy',true)));?></th>
            <th class="align_center">EA <?=date('M',strtotime($this->payrolloptions->dtrdeductdisplay('',$schedule,$quarter,$sdate,$edate,'tardy',true)));?></th>
            <th class="align_center">Edited By</th>
            <th class="align_center"></th>
        </tr>
    </thead>
    <tbody>  
            <?
                $query = $this->payroll->loadAllEmpbyDeptProcessed($dept,$employeeid,$schedule,$sdate,$edate,$campus);
                foreach($query as $row){
                    $empid = $row->employeeid;
                    $regpay =  $row->regpay;
                    $dependents = $row->dependents;
                    $editedby = $row->editedby;
                    // $count = 0;
                ?>
                    <tr class="idata">
                        <td class="align_center"><?=$row->employeeid?></td>
                        <td class="align_center"><?=$row->fullname?></td>
                        <td class="align_center"><?=$regpay?></td>
                        
                        <?
                        if($this->payrolloptions->incometitlep('',$schedule,$quarter,$sdate,$edate)){
                            $arr_payroll = $this->payrolloptions->deducttitleothp($empid,$schedule,$quarter,$sdate,$edate);
                            ($arr_payroll !== "")?  $arr_payroll = $arr_payroll[0] : $arr_payroll = "0"  ; 

                            
                           echo "<td class='align_center'>".$arr_payroll."</td>";
                        }
                        ?>
                        
                        <td class="align_center"><?=$this->payrolloptions->dtrdeductdisplay($empid,$schedule,$quarter,$sdate,$edate,'overtime');?></td>  <!-- Overtime -->
                        <td class="align_center"><?=$this->payrolloptions->dtrdeductdisplay($empid,$schedule,$quarter,$sdate,$edate,'withholdingtax');?></td>  <!-- WITHHOLDING TAX -->
                        
                        <?
                        if($this->payrolloptions->deducttitlep('',$schedule,$quarter,$sdate,$edate)){
                             $arr_payroll = $this->payrolloptions->deducttitlep($empid,$schedule,$quarter,$sdate,$edate);
                            ($arr_payroll !== "")?  $arr_payroll = $arr_payroll[0] : $arr_payroll = "0"  ; 
                           echo "<td class='align_center'>".$arr_payroll."</td>";
                        }
                        ?>
                        
                        <?
                        if($this->payrolloptions->loantitlep('',$schedule,$quarter,$sdate,$edate)){
                            $arr_payroll = $this->payrolloptions->deducttitleothp($empid,$schedule,$quarter,$sdate,$edate);
                            ($arr_payroll !== "")?  $arr_payroll = $arr_payroll[0] : $arr_payroll = "0"  ; 

                            
                           echo "<td class='align_center'>".$arr_payroll."</td>";
                        }
                        ?>
                        
                        <?
                        if($this->payrolloptions->deducttitleothp('',$schedule,$quarter,$sdate,$edate)){
                             $arr_payroll = $this->payrolloptions->deducttitleothp($empid,$schedule,$quarter,$sdate,$edate);
                            ($arr_payroll !== "")?  $arr_payroll = $arr_payroll[0] : $arr_payroll = "0"  ; 

                            
                           echo "<td class='align_center'>".$arr_payroll."</td>";
                        }
                        ?>
                        
                        <td class="align_center"><?=$this->payrolloptions->dtrdeductdisplay($empid,$schedule,$quarter,$sdate,$edate,'tardy');?></td>  <!-- Tardy DTR DEDUCTIONS -->
                        <td class="align_center"><?=$this->payrolloptions->dtrdeductdisplay($empid,$schedule,$quarter,$sdate,$edate,'absents');?></td>  <!-- Excess Absents DTR DEDUCTIONS -->
                        <td class="align_center"><?=strtoupper($editedby)?></td>
                        <td class="align_center"><a class='btn grey edit_data glyphicon glyphicon-edit' id="<?=$this->payrolloptions->dtrdeductdisplay($empid,$schedule,$quarter,$sdate,$edate,'id');?>" data-toggle="modal" data-target="#myModal"></a></td>
                    </tr>
                <?
                }
            ?>
    </tbody>
</table>
