<?php

/**
 * @author Justin
 * @copyright 2014
 */

function css(){
    
    $css = '
<style>
    @page{
        margin-top: 5.5cm;
        odd-header-name: html_Header;
        odd-footer-name: html_Footer;
    }
    .img{
    margin: 0;
    padding: 1px;
    border: 5px solid gray;    
    }
    img{
    margin-left: 300px;
    }        
    table{
        border-collapse: collapse;
        height: 100%;
        width: 100%;
    }
    
    th {
        font-family: "Trebuchet MS", Arial, Verdana;
        font-size: 15px;
        border-bottom-width: 1px;
        border-bottom-style: solid;
        border-bottom-color: #CDC1A7;
        border-top-width: 1px;
        border-top-style: solid;
        border-top-color: #CDC1A7;
        width: 20%;
    }
    
    td{
        margin-left: 10px;
        text-align: left;
        vertical-align: bottom;
        font-size: 15px;
        //width: 17%;
        width: 20%;
        text-align: center;
    }
    
    h3{
     text-align: center;   
     color: black;
     vertical-align: bottom;
    }
    h2{
     text-align: center;   
     color: black;
     vertical-align: bottom;
    }
    /* DIVISIONS */
    .header{
    padding: 1px;
   // border: 5px solid gray;
    margin: 0; 
    }
    .body{
    padding: 1px;
   // border: 5px solid gray;
    margin: 0; 
    }
    .content{
    padding: 1px;
   // border: 5px solid gray;
    margin: 0; 
    }
    
</style>
';
return $css;
}

?>