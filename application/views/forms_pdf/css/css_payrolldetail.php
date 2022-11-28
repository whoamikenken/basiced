<?php

/**
 * @author Justin
 * @copyright 2015
 */

function css(){
    $css = '
<style>
    @page{
        margin-top: 3.09cm;
        odd-header-name: html_Header;
        odd-footer-name: html_Footer;
    }  
    table{
        border-collapse: collapse;
        height: 100%;
        width: 100%;
    }
    th {
        font-family: "Trebuchet MS", Arial, Verdana;
        font-size: 15px;
        /*
        border-bottom-width: 1px;
        border-bottom-style: solid;
        border-bottom-color: #CDC1A7;
        border-top-width: 1px;
        border-top-style: solid;
        border-top-color: #CDC1A7;
        */
        width: 10%;
    }
    td{
        margin-left: 10px;
        text-align: left;
        vertical-align: bottom;
        font-size: 12px;
        width: 20%;
        text-align: center;
        border: 1px solid black;
    }
    /* DIVISIONS */
    .header{
    text-align: left;
    padding: 1px;
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