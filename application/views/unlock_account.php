
<style type="text/css">
  
</style>
<!DOCTYPE html>
<html>
<head>
    <title>HYPERION</title>
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>css/login.css">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/md5.js"></script>
    <style type="text/css">
        .school-name {
            font-family: BOOK ANTIQUA, sans-serif;
            text-transform: uppercase;
            font-size: 16px;
            font-weight: 700;
        }
        span.login-header-description {
            font-family: BOOK ANTIQUA, sans-serif;
            font-size: 18px;
            float: center;
            margin-top: -5px;
            font-weight: 700;
        }
    </style>
</head>
<body class="bg">
    
<input type="hidden" id="info" value="<?= $status ?>">
<input type="hidden" id="key" value="<?= $key ?>">
<input type="hidden" id="username" value="<?= $userinfo ?>">
<input type="hidden" id="site_url" value="<?= site_url() ?>">
<script src="<?=base_url()?>jsbstrap/jquery-1.10.2.js"></script>
<script src="<?=base_url()?>jsbstrap/bootstrap.js"></script>
<script src="<?=base_url()?>js/sweetalert.js"></script>

<script type="text/javascript">
var status = $("#info").val();

if (status == "Expired") {
    Swal.fire({
        icon: 'warning',
        title: 'Warning!',
        text: 'Unlocking ling has been expired please request again.',
        showConfirmButton: true,
        timer: 4000
    })
    setTimeout(function() {
        window.location.href = "<?=base_url()?>";
    }, 4500);
}else if(status == "Read"){
    Swal.fire({
        icon: 'warning',
        title: 'Warning!',
        text: 'This request has already been used please request again.',
        showConfirmButton: true,
        timer: 4000
    })
    setTimeout(function() {
        window.location.href = "<?=base_url()?>";
    }, 4500);
}else if(status == "Unlocked"){
    Swal.fire({
        icon: 'info',
        title: 'Account Unlocked',
        text: 'Account is already unlocked you will be redirected to login page.',
        showConfirmButton: true,
        timer: 4000
    })
    setTimeout(function() {
        window.location.href = "<?=base_url()?>";
    }, 4500);
}else if(status == "Unlock"){
    $.ajax({
        url: $("#site_url").val() + "/main/unlockAccount",
        type: "POST",
        data: { username: $("#username").val(), key: $("#key").val()},
        dataType: "json",
        success: function(response) {
            
        }
    });
    Swal.fire({
        icon: 'Success',
        title: 'Account Unlocked',
        text: 'Account successfully unlocked you will be redirected to login page.',
        showConfirmButton: true,
        timer: 4000
    })
    setTimeout(function() {
        window.location.href = "<?=base_url()?>";
    }, 4500);
}
</script>

         
    

