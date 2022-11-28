<?php
// Kennedy

require_once(APPPATH."constants.php");

$extracol = "";

$ids = explode(",",$id);

$custom_layout = array('55.98', '85.70');   // standard ID Card Size :  mm size : 55.98 ×  85.60 mm - inch size: (3.370 × 2.125 in)
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
foreach($ids as $record){
    $data = $this->studentt->getDataStd($record);

                foreach($data as $row){

                    if ($row->content) {
                        $image = '<img src="data:image/jpeg;base64,'.base64_encode($row->content).'"/>';
                    }else $image = "<img style='border-radius: 15px' src='".base_url()."images/no_image.gif'/>";

                    /* FRONT PAGE */
                    $info .= $style."
                    <div class='fcontainer' >
                        
                        <div class='header'></div>
                        <div class='img'></div>
                        <div class='pimg'>
                            $image
                        </div>
                        
                        
                        <div class='idlabels'>
                          <span class='baseFont' style='font-size: 8px;margin-top: 5px;'>S.Y : ". $row->sy ."</span><br>";
                         
                    $info .= "        
                                <span class='baseFont' style='font-size: 19px;margin-top: 5px;'><b>".$row->lname."<b></span><br>
                                <span class='baseFont' style='font-size: 14px;margin-top: 30px;'>".$row->fname." ".$row->mname[0].".</span><br>
                                <span class='baseFont' style='font-size: 9px;'>".$row->section."</span><br><br>
                                <span class='baseFont' style='font-size: 14px;'>ID NUMBER: ".$row->studentid."</span>
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
                }
}
$pdf->WriteHTML($info);

$pdf->Output();
?>



