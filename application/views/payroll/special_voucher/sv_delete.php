 <?php

  $category = $type;
  $account_name = $code = '';
  if($type == "income" || $type == "deduction" || $type = "loan") $code = 'id';
  else if($type == "regdeduction") $code = 'code_deduction';
  else if($type == "witholdingtax") $account_name = "Witholdingtax";

  $account_name ='';
  if($category != "witholdingtax"){
      if($category == "income"){
          foreach($income as $key => $setup_val){
              if($setup_val[$code] == $account) $account_name = $setup_val['description'];
          }
      }
      else if($category == "deduction"){
          foreach($deduction as $key => $setup_val){
              if($setup_val[$code] == $account) $account_name = $setup_val['description'];
          }
      }
      else if($category == "loan"){
          foreach($loan as $key => $setup_val){
              if($setup_val[$code] == $account) $account_name = $setup_val['description'];
          }
      }
      else if($category == "regdeduction"){
          foreach($regdeduction as $key => $setup_val){
              if($key == $account) $account_name = $setup_val;
          }
      }
  }

?>
<div class="container">
    <p  style="padding-left: 100px;">Are you sure you want to delete this <?= strtoupper($category) ?>: <b><?= isset($account) ? $account : "" ?></b>?</p>
</div> 
  <script>

    $("#modalclose").click(function(){
        if("<?= $category != 'witholdingtax'?>"){
          data = {
            "delete_employeeid" : "<?= $employeeid ?>",
            "delete_account" : "<?= isset($account) ? $account : "" ?>" ,
            "delete_type" : "<?= $category ?>"
          };
        }else{
           data = {
            "delete_employeeid" : "<?= $employeeid ?>",
            "delete_type" : "<?= $category ?>"
          };
        }

        $.ajax({
          url : "<?= site_url('extensions_/validateDeleteVoucher')?>",
          type : "POST",
          data : data,
          success:function(response){
            alert(response);
            $('#modal-view').modal('hide');
            getEncodedHistory();
            $("#modalclose").unbind(); 
          }
        });

    });

  </script>