<?php
// Kennedy

require_once(APPPATH."constants.php");

$extracol = "";

$id = $records['0']->studentid;
$sy = $records['0']->sy;
$lname = $records['0']->lname;
$fname = $records['0']->fname;
$mname = $records['0']->mname;
$yearlevel = $records['0']->yearlevel;
$img = $records['0']->content;
$section = $records['0']->section;

if ($img) {
    $image = '<img src="data:image/jpeg;base64,'.base64_encode($img).'"/>';
}else $image = "<img style='border-radius: 15px' src='".base_url()."images/no_image.gif'/>";

$custom_layout = array('55.98', '85.60');   // standard ID Card Size :  mm size : 55.98 ×  85.60 mm - inch size: (3.370 × 2.125 in)
$pdf = new mpdf('P',$custom_layout,'','UTF-8',0,0,0,0);

$style =    "<style>
                /* FRONT PAGE */
            .fcontainer{
                    width: 100%;
                    height: 100%;
                    text-align: center;
                    background-image: url('".base_url()."images/studentF.png');
                    background-repeat: no-repeat;
                    background-size: 100% 100%;
                    color: white;
                }
                .header{
                    margin-left: 35px;
                    height: 3%;
                }
                .idlabels{
                    margin-left: auto;
                    margin-right: auto;
                    padding-top: 2.5%;
                    display:block;
                    width: 70%;
                    height:80px;
                    text-align:center;
                }
                .spacing{
                    display:block;
                    width:100%;
                    height:10px;
                    overflow:scroll;
                    text-align:center;
                    border: 1px solid black;
                }
             
                .img{
                    height: 20.1%;
                    border-radius: 15px;
                }
                .pimg{
                    width: 50.2%;
                    height: 30.6%;
                    text-align: center;
                    margin-left: 25.1%;
                    margin-right: auto;
                }
             
                .fname{
                    font-family:  'arialrounded';
                    font-style: normal;
                    font-variant: normal;
                    /*font-size: 14px;
                    line-height: 16px;*/
                    /*height: 48px;*/
                    color: red;
                    /*border: 1px solid black;*/
                }

           /* BACK PAGE */
            .bcontainer{
                    width: 100%;
                    height: 100%;                                    
                    background-image: url('".base_url()."images/studentB.png');
                    background-repeat: no-repeat;
                    background-size: 100% 100%;                
                }
            .btitle{
                    font-family: 'oldenglishtext';
                    font-size:   11;
                    text-align: center;
                    color: black;
                }
            .bheader{
                margin-top: 5px;
                height: 177.5px;
            }
            .bregular{
                margin-left: 22px;
                margin-right: 15px;
                font-size:    8px;
                text-align: justify;
                text-justify: inter-word;
                font-family: 'arialblack';
                font-weight: bold;
             }

             .baseFont { 
                margin-top: 20px;
                font-family: Futura,Trebuchet MS,Arial,sans-serif; 
            }
             
             .space{
                height: 8px;
             }
            </style>";

/* FRONT PAGE */
$info = $style."
<div class='fcontainer' >
    
    <div class='header'></div>
    <div class='img'></div>
    <div class='pimg'>
        $image
    </div>
    
    
    <div class='idlabels'>
      <span class='baseFont' style='font-size: 8px;margin-top: 5px;'>S.Y : ". $sy ."</span><br>";
     
$info .= "        
            <span class='baseFont' style='font-size: 19px;margin-top: 5px;'><b>$lname<b></span><br>
            <span class='baseFont' style='font-size: 14px;margin-top: 30px;'>".$fname." ".$mname[0].".</span><br>
            <span class='baseFont' style='font-size: 8px;'>$section</span><br><br>
            <span class='baseFont' style='font-size: 14px;'>ID NUMBER: ".$id."</span>
        </div>
    </div>  
  </div>      
</div>          
";

/* BACK PAGE */
$info .= "
<div class='bcontainer'>
    <div class='bheader'>

    </div>
    <div class='content'>

    </div>    
</div> 
";
$pdf->WriteHTML($info);

$pdf->Output();
?>



