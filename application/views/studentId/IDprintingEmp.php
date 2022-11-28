<?php
// Kennedy

require_once(APPPATH."constants.php");

$extracol = "";

$id = $records['0']->employeeid;
$campus = $records['0']->campusid;
$lname = $records['0']->lname;
$fname = $records['0']->fname;
$mname = $records['0']->mname;
$dateemploy = $records['0']->dateemployed;
$img = $records['0']->content;
$position = $records['0']->description;

if ($img) {
    $image = '<img src="data:image/jpeg;base64,'.base64_encode($img).'"/>';
}else $image = "<img style='border-radius: 15px' src='".base_url()."images/no_image.gif'/>";

$custom_layout = array('55.98', '85.60');   // standard ID Card Size :  mm size : 55.98 ×  85.60 mm - inch size: (3.370 × 2.125 in)
$pdf = new mpdf('P',$custom_layout,'','UTF-8',0,0,.4,0);

$style =    "<style>
                /* FRONT PAGE */
            .fcontainer{
                    width: 100%;
                    height: 100%;
                    text-align: center;
                    background-image: url('".base_url()."images/employeeidF.png');
                    background-repeat: no-repeat;
                    background-size: 100% 100%;
                    /*background-clip: content-box;*/
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
                }
             
                .img{
                    height: 20.1%;
                    border-radius: 15px;
                }
                .pimg{
                    width: 50.5%;
                    height: 30.6%;
                    text-align: center;
                    margin-left: 24.9%;
                    margin-right: auto;
                    border-radius: 2px;
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
                    background-image: url('".base_url()."images/employeeidB.jpg');
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
                    padding-top: 4.5%;
                    display:block;
                    width: 100%;
                    height:80px;
            }
            .content{
                    padding-top: 5.5%;
                    display:block;
                    width: 100%;
                    height:80px;
                    text-align: center;
            }
            .foot{
                    padding-top: 12.5%;
                    display:block;
                    width: 100%;
                    height:50px;
                    text-align: center;
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
      <span style='font-size: 19px;margin-top: 5px;'><b>$lname<b></span><br>";
     
$info .= "        
            <span class='baseFont' style='font-size: 13.5px;'>".$fname." ".$mname."</span><br/>
            <span class='baseFont' style='font-size: 10px;'>$position</span><br><br><br>
            <span class='baseFont' style='font-size: 8px;'>ID NUMBER: ".$id."</span>
        </div>
    </div>  
  </div>      
</div>          
";

/* BACK PAGE */
$info .= "
<div class='bcontainer'>
    <div class='bheader'>
            <span class='baseFont' style='font-size: 8px;color:#04038c;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Date Hired: ".$dateemploy."</span><br>
            <span class='baseFont' style='font-size: 8.5px;color:#04038c;text-align: justify;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;This card must be worn PROMINENTLY</span>
            <span class='baseFont' style='font-size: 8.5px;color:#04038c;text-align: justify;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;at ALL TIMES while on campus.</span><br><br>
            <span class='baseFont' style='font-size: 8.5px;color:#04038c;text-align: justify;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Non- Transferrable & will be confiscated</span>
            <span class='baseFont' style='font-size: 8.5px;color:#04038c;text-align: justify;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;if found in another's posession.</span><br><br>
            <span class='baseFont' style='font-size: 8.5px;color:#04038c;text-align: center;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;If lost, stolen, or damaged, report immediately.&nbsp;&nbsp;</span>
            <span class='baseFont' style='font-size: 8.5px;color:#04038c;text-align: center;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;A fee will be charged for its replacement.</span><br><br>
            <span class='baseFont' style='font-size: 8.5px;color:#04038c;text-align: center;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;If found, please return this card to:</span>
    </div> 
    <div class='content'>
            <span class='baseFont' style='font-size: 9px;color:#04038c;text-align: center;'>".$campus. "&nbsp;&nbsp;&nbsp;CAMPUS</span>
            <span class='baseFont' style='font-size: 7.5px;color:#04038c;text-align: center;'Contact Number: 046 402 00 52</span>
    </div>  
    <div class='foot'>

            <span class='baseFont' style='font-size: 7.5px;color:#04038c;padding-top:5%;'>School Director</span>
    </div> 
</div> 
";
$pdf->WriteHTML($info);

$pdf->Output();
?>



