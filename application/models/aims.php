<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Aims extends CI_Model {

    var $base_convert;
    var $passarr;
    var $method;

    function __construct( $base=8 ){
        $this->base_convert = $base;
    }

    public function saveEmployeeToAims($empinfo){
        $dbname = "";
        if($_SERVER["HTTP_HOST"] == "192.168.2.97") $dbname = "Poveda";
        else if($_SERVER["HTTP_HOST"] == "poveda.pinnacle.com.ph" && strpos($_SERVER["REQUEST_URI"], 'hristrng') !== false) $dbname = "Training";
        else if($_SERVER["HTTP_HOST"] == "poveda.pinnacle.com.ph" && strpos($_SERVER["REQUEST_URI"], 'hris') !== false) $dbname = "Poveda";
        extract($empinfo);
        $q_exists = $this->db->query("SELECT * FROM $dbname.tblFacultyProfile WHERE FCode = '$employeeid' ")->num_rows();
        if(!$q_exists){
            $q_save = $this->db->query("INSERT INTO $dbname.tblFacultyProfile (FCode, LName, FName, MName) VALUES ('$employeeid', '$lastname', '$firstname', '$middlename')");
            $salt = $this->generateSalt();
            $password = $this->hashString($lastname, $salt);
            $fullname = $lastname.",".$firstname." ".$middlename;
            $this->db->query("INSERT INTO $dbname.tblUserAcct(username,PASSWORD,salt,Fullname,UserType,LogInName) VALUES ('$employeeid','$password','$salt','$fullname',3,'$employeeid');");
        }else{
            return 2;
        }
        // echo $this->db->last_query(); die;
        return $q_save;
    }

    public function checkIfAlreadyInAims($employeeid){
        $dbname = "";
        if($_SERVER["HTTP_HOST"] == "192.168.2.97") $dbname = "Poveda";
        else if($_SERVER["HTTP_HOST"] == "poveda.pinnacle.com.ph" && strpos($_SERVER["REQUEST_URI"], 'hristrng') !== false) $dbname = "Training";
        else if($_SERVER["HTTP_HOST"] == "poveda.pinnacle.com.ph" && strpos($_SERVER["REQUEST_URI"], 'hris') !== false) $dbname = "Poveda";
        return  $this->db->query("SELECT * FROM $dbname.tblFacultyProfile WHERE FCode = '$employeeid' ")->num_rows();
    }

    public function hashString( $string, $salt=FALSE, $oldmethod=true ){
        if( !$salt ) $salt = $this->generateSalt();
        $this->method = $oldmethod?'old':'new';
        if($oldmethod){
            /** This part is for old school, not case sensitive, but in order for all the users to be able to login, this method is required */
            $passarr = array( 'salt'=>$salt );
            $newstr = $this->convert( $string.$salt );
            $newstr = hash( 'sha1', $newstr.$salt ); 
            $newstr = $this->convert( $newstr );
            $newstr = hash( 'md5', $newstr );
            $newstr = hash( 'sha256', $newstr );
            $passarr['hash'] = $newstr;
            $this->passarr = $passarr;
        }else{
            /** This part is for case sensitive, make sure that the tblUserAcct has a field name `method` (old,new) */
            $passarr = array( 'salt'=>$salt );
            $newstr = $this->convert( $string.$salt );
            $newstr = hash( 'sha1', $newstr.$salt ); 
            $newstr = $this->convert( $newstr );
            $newstr = hash( 'md5', $newstr );
            $newstr = hash( 'sha256', $newstr );
            $passarr['hash'] = $newstr;
            $this->passarr = $passarr;
        }
        return $newstr;
    }
    
    #public function generateSalt(){
    public function generateSalt(){
        $salt = uniqid();
        return $salt;
    }
    
    #private function convert( $string ){
    public function convert( $string ){
        if($this->method=='new'){
          $newstr = $this->base_convert_x($string);

          # $newstr = base_convert($string, 32, 8);
        }else $newstr = base_convert($string, 32, $this->base_convert);
        return $newstr;
    }
    
    
    public function base_convert_x( $_number='', $_frBase=10, $_toBase=62 ) {

    #   Today's Date - C74 - convert a string (+ve integer) from any arbitrary base to any arbitrary base, up to base 62, using  0-9,A-Z,a-z
    #
    #   Usage :   echo base_convert_x( 123456789012345, 10, 32 );

      $_10to62 =  array(
        '0'  => '0', '1'  => '1', '2'  => '2', '3'  => '3', '4'  => '4', '5'  => '5', '6'  => '6', '7'  => '7', '8'  => '8', '9'  => '9', '00' => '0', '01' => '1', '02' => '2', '03' => '3', '04' => '4', '05' => '5', '06' => '6', '07' => '7',
        '10' => 'A', '11' => 'B', '12' => 'C', '13' => 'D', '14' => 'E', '15' => 'F', '16' => 'G', '17' => 'H', '18' => 'I', '19' => 'J', '20' => 'K', '21' => 'L', '22' => 'M', '23' => 'N', '24' => 'O', '25' => 'P', '26' => 'Q', '27' => 'R',
        '30' => 'U', '31' => 'V', '32' => 'W', '33' => 'X', '34' => 'Y', '35' => 'Z', '36' => 'a', '37' => 'b', '38' => 'c', '39' => 'd', '40' => 'e', '41' => 'f', '42' => 'g', '43' => 'h', '44' => 'i', '45' => 'j', '46' => 'k', '47' => 'l',
        '50' => 'o', '51' => 'p', '52' => 'q', '53' => 'r', '54' => 's', '55' => 't', '56' => 'u', '57' => 'v', '58' => 'w', '59' => 'x', '60' => 'y', '61' => 'z'  );

      $_62to10 =  array(
        '0' => '00', '1' => '01', '2' => '02', '3' => '03', '4' => '04', '5' => '05', '6' => '06', '7' => '07', '8' => '08', '9' => '09', 'A' => '10', 'B' => '11', 'C' => '12', 'D' => '13', 'E' => '14', 'F' => '15', 'G' => '16', 'H' => '17',
        'K' => '20', 'L' => '21', 'M' => '22', 'N' => '23', 'O' => '24', 'P' => '25', 'Q' => '26', 'R' => '27', 'S' => '28', 'T' => '29', 'U' => '30', 'V' => '31', 'W' => '32', 'X' => '33', 'Y' => '34', 'Z' => '35', 'a' => '36', 'b' => '37',
        'e' => '40', 'f' => '41', 'g' => '42', 'h' => '43', 'i' => '44', 'j' => '45', 'k' => '46', 'l' => '47', 'm' => '48', 'n' => '49', 'o' => '50', 'p' => '51', 'q' => '52', 'r' => '53', 's' => '54', 't' => '55', 'u' => '56', 'v' => '57',
        'y' => '60', 'z' => '61' );

    #   ---- First convert from frBase to base-10

        $_in_b10        =   0;
        $_pwr_of_frB    =   1;                        #  power of from base, eg. 1, 8, 64, 512
        $_chars         =   str_split( $_number );    #  split input # into chars
        $_str_len       =   strlen( $_number );
        $_pos           =   0;

        while     (  $_pos++ < $_str_len )  {
            $_char          =   $_chars[$_str_len - $_pos];
            $_in_b10       +=   (( $_62to10[$_char] ) * $_pwr_of_frB);
            $_pwr_of_frB   *=   $_frBase;
        }
          #echo "<pre>";
          #echo $_in_b10; 
    #   ---- Now convert from base-10 to toBase
        $gdec           = explode('.',number_format($_in_b10,5,'.',''));
        $_dividend      =   $gdec[0];         #  name dividend easier to follow below
        $_in_toB        =   '';                       #  number string in toBase

        while     ( $_dividend > 0 )        {
            $dex        = explode('.',number_format($_dividend / $_toBase, 5, '.', ''));
            $_quotient  =   $dex[0];    #  eg. 789 / 62  =  12  ( C in base 62 )
            $_remainder =   ''  .  ( $_dividend % $_toBase );   #  789 % 62  =  45  ( j in base 62 )
            $_in_toB    =   $_10to62[$_remainder] . $_in_toB;   #  789  (in base 10)  =    Cj  (in base 62)
            $_dividend  =   $_quotient;                         #  new dividend is the quotient from base division
        }
        
        if  ( $_in_toB  ==  '' )
              $_in_toB  =   '0';

        return    $_in_toB;                           #  base $_toBase string
    }

}