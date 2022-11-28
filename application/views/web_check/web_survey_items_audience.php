<?php if ($audience == "all"){ ?>
    <div class="col-md-8 col-md-offset-2">
        <div class="list-group">
            <h2 class="list-group-item list-group-item-success" style="text-align: center;">All Employee</h2>
        </div>
    </div>
<?php }else{ ?>
    <div class="col-md-12">
        <table class="table table-striped table-bordered table-hover" id="audienceTable" >
            <thead>
                <tr>
                    <th align="center">Employee ID</th>
                    <th align="center">Employee Name</th>
                </tr>
            </thead>
            <tbody id="employeelist" style="cursor: pointer;">
                <?php foreach(explode(",", $audience) as $row): 
                    ?>
                    <tr>
                        <td align="center"><?=$row?></td>
                        <td align="center"><?= $this->extensions->getEmployeeName($row)?></td>
                    </tr>
                <?php endforeach ?>  
            </tbody>
        </table>
    </div>
    
    <script>
        $(document).ready(function(){
            var table = $('#audienceTable').DataTable();
            new $.fn.dataTable.FixedHeader( table );
        });
    </script>
<?php } ?>