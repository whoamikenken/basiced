<!DOCTYPE html>
<html lang="en">

<head>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<meta name="author" content="Angelica Arangco" />
    <!-- <link rel='stylesheet' type='text/css' href='<?=base_url();?>css/jquery.auto-complete.css' /> -->
    <!-- <link rel='stylesheet' type='text/css' href='<?=base_url();?>css/jquery-ui.css' /> -->
    <!-- The styles -->
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.4.2/chosen.css">
    <link href="<?=base_url()?>css/bstrap/stylesheet.css" rel="stylesheet">
    <link href="<?=base_url()?>icon/font-awesome.css" rel="stylesheet">
    <link href="<?=base_url()?>css/Animate.css" rel="stylesheet">

    <link href="<?=base_url()?>css/applicant.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs/jszip-2.5.0/dt-1.10.18/af-2.3.3/b-1.5.6/b-colvis-1.5.6/b-flash-1.5.6/b-html5-1.5.6/b-print-1.5.6/cr-1.5.0/fc-3.2.5/fh-3.1.4/kt-2.5.0/r-2.2.2/rg-1.1.0/rr-1.2.4/sc-2.0.0/sl-1.3.0/datatables.min.css"/>

    <link rel="shortcut icon" href="<?=base_url()?>css/img/pinnacle.png">
    
	<title>Applicant Portal</title>
    
    <script src="<?=base_url()?>jsbstrap/jquery-1.10.2.js"></script>
    <!-- <script src="<?=base_url()?>jsbstrap/jquery-ui-1.10.3.js"></script> -->
    <!-- <script type='text/javascript' src='<?=base_url();?>js/jquery.auto-complete.js'></script> -->
    <!-- <script src="<?=base_url()?>jsbstrap/library/chosen.jquery.min.js"></script>     -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.4.2/chosen.jquery.js"></script>
    <script src="<?=base_url()?>jsbstrap/bootstrap.js"></script>
    <script src="<?=base_url()?>jsbstrap/library/jquery.validate.js"></script>
    <script src="https://kit.fontawesome.com/ca22069803.js"></script>
    <script src="https://unpkg.com/zipcodes-ph@1.1.2/build/index.umd.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs/jszip-2.5.0/dt-1.10.18/af-2.3.3/b-1.5.6/b-colvis-1.5.6/b-flash-1.5.6/b-html5-1.5.6/b-print-1.5.6/cr-1.5.0/fc-3.2.5/fh-3.1.4/kt-2.5.0/r-2.2.2/rg-1.1.0/rr-1.2.4/sc-2.0.0/sl-1.3.0/datatables.min.js"></script>
    <script src="<?=base_url()?>js/gate/sha512.js"></script>
    <script src="<?=base_url()?>js/gibberish-aes-1.0.0.min.js"></script>

    <style>
        @import url(https://fonts.googleapis.com/css?family=Open+Sans);
        * {
            -webkit-box-sizing: unset!important; 
            -moz-box-sizing: unset!important;
             box-sizing: unset!important; 
        }
        .search {
          width: 100%!important;
          position: relative!important;
          height: 48px;
        }

        .searchTerm {
          float: left;
          width: 100%;
          border: 3px solid  #8fa0a2;
          padding: 5px;
          height: 33px;
          border-radius: 5px;
          outline: none;
          font-size: 20px;
          color: #8fa0a2;
        }

        .searchTerm:focus{
          color:  #000000;
        }

        .searchButton {
          position: absolute;  
          right: -50px;
          width: 40px;
          height: 36px;
          border: 1px solid  #8fa0a2;
          background:  #8fa0a2;
          text-align: center;
          color: #fff;
          border-radius: 5px;
          cursor: pointer;
          font-size: 20px;
        }

        /*Resize the wrap to see the search bar change!*/
        .wrap{
          width: 59%;
          position: relative;
          top: 21%;
          left: 33%;
          transform: translate(-50%, -50%);
        }
        .search > input {
        max-width: 80%!important;
        }
        .modal.fade.in {
            top: 7%!important;
        }
        .form-control{
            width: 89%;
        }
    </style>
</head>


