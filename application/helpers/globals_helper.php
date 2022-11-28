<?php 
/**
 * @author Justin
 * @copyright 2016
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Application specific global variables
class Globals
{
    public static function seturl(){
        #return "http://localhost/codeigniter/rest_server";
    }
    
    public static function getValue(){
        return 50000;
    }

    public static function pf($string){
    	$return = var_dump("<pre>", $string);
    	return $return;
    }

    public static function getBEDDepartments(){
        return array('ELEM','HS','SHS','BED','ACAD');
    }

    public static function getUserAccess(){
      return array("teaching" => "Teaching", "nonteaching" => "Non Teaching", "student" => "Student");
    }

    public static function birthOrder(){
        return array("1" => "Eldest", "2" => "Second", "3" => "Third", "4" => "Fourth", "5" => "Fifth", "6" => "Sixth", "7" => "Seventh", "8" => "Eighth","9" => "Ninth", "10" => "Tenth", "11" => "Eleventh", "12" => "Twelfth", "13" => "Thirteenth", "14" => "Forteenth", "15" => "Fifteenth");
    }

    public static function getBatchEncodeCategory(){
      return array(""=>"Choose Category","salary"=>"Salary","deduction"=>"Deduction","income"=>"Income","loan"=>"Loans", "loan_adj"=>"Loan Adjustment", "regdeduc"=>"Reglementary Deduction", "regpayment"=>"Reglementary Payment", "prevdata" => "Previous Employer Data");
    }

    public static function getBatchEncodeCategory2(){
      return array(""=>"Choose Category","salary"=>"Salary","deduction"=>"Deduction","income"=>"Income","loan"=>"Loans", "loan_adj"=>"Loan Adjustment", "regdeduc"=>"Reglementary Deduction");
    }

    public static function documentStatusList(){
        return array("PENDING"=>"PENDING","PROCESS"=>"ON PROCESS","APPROVED"=>"APPROVED","DISAPPROVED"=>"DISAPPROVED");
    }

    public static function idxConfig(){
      return array("M" => 1, "T" => 2, "W" => 3, "TH" => 4, "F" => 5, "S" => 6, "SUN" => 7);
    }

    public static function monthList(){
        return array('01' => "January",'02' => "February",'03' => "March",'04' => "April",'05' => "May",'06' => "June",'07' => "July",'08' => "August",'09' => "September",'10' => "October",'11' => "November",'12' => "December");
    }

    public static function seminarList(){
        return array("PTS_PDP"=>"T/A POVEDA SPIRITUALITY", "PTS_PDP1"=>"PROFESSIONAL DEVELOPMENT PROGRAM", "PTS_PDP2"=>"PEP DEVELOPMENT PROGRAM", "PTS_PDP3"=>"PSYCOSOCIAL - CULTURAL");
    }

    public static function inhouseSeminarList(){
        return array("employee_pts"=>"T/A POVEDA SPIRITUALITY", "employee_pts_pdp1"=>"PROFESSIONAL DEVELOPMENT PROGRAM", "employee_pts_pdp2"=>"PEP DEVELOPMENT PROGRAM", "employee_pts_pdp3"=>"PSYCOSOCIAL - CULTURAL");
    }

    // public static function dataRequestApprovalList(){
    //     return array('employee_family' => "Family Members",
    //                 'employee_emergencyContact' => "Emergency Contact Information",
    //                 'employee_education'=>"Educational Background",
    //                 'employee_eligibilities'=>"Eligibility",
    //                 'employee_subj_competent_to_teach'=>"Subjects Competent To Teach",
    //                 'employee_work_history_related'=>"Work Experience Outside Poveda",
    //                 'employee_pts'=>"T/A Poveda Spirituality",
    //                 'employee_pts_pdp1'=>"Professional Development Program",
    //                 'employee_pts_pdp2'=>"PEP Development",
    //                 'employee_pts_pdp3'=>"Psychosocial",
    //                 'employee_pgd'=>"Publication",
    //                 'employee_awardsrecog'=>"Awards & Recognition",
    //                 'employee_scholarship'=>"Scholarship",
    //                 'employee_resource'=>"Speaking Engagements",
    //                 'employee_proorg'=>"Membership in Civic Organization",
    //                 'employee_community'=>"Community Involvement",
    //                 'employee_administrative'=>"Position Held in Poveda");
    // }

    public static function dataRequestApprovalList(){
        return array(
                        'employee_awardsrecog'=>"Awards & Recognition",
                        'employee_community'=>"Community Involvement",
                        'employee_education'=>"Educational Background",
                        'employee_eligibilities'=>"Eligibility",
                        'employee_emergencyContact' => "Emergency Contact Information",
                        'employee_family' => "Family Members",
                        'employee_proorg'=>"Membership in Civic Organization",
                        'employee_pts_pdp2'=>"PEP Development",
                        'employee_administrative'=>"Position Held in Poveda",
                        'employee_pts_pdp1'=>"Professional Development Program",
                        'employee_pts_pdp3'=>"Psychosocial",
                        'employee_pgd'=>"Publication",
                        'employee_scholarship'=>"Scholarship",
                        'employee_resource'=>"Speaking Engagements",
                        'employee_subj_competent_to_teach'=>"Subjects Competent To Teach",
                        'employee_pts'=>"T/A Poveda Spirituality",
                        'employee_work_history_related'=>"Work Experience Outside Poveda",
                    );
    }

    public static function reportsItemTableList(){
        return array('EB' => "employee_education",
                    'E' => "employee_eligibilities",
                    // 'OC'=>"Educational Background",
                    'ECT'=>"employee_emergencyContact",
                    'PTS'=>"employee_pts",
                    // 'PTS_PDP'=>"Work Experience Outside Poveda",
                    'PTS_PDP1'=>"employee_pts_pdp1",
                    'PTS_PDP2'=>"employee_pts_pdp2",
                    'PTS_PDP3'=>"employee_pts_pdp3",
                    // 'PGD'=>"employee_pgd",
                    // 'R'=>"Publication",
                    'AR'=>"employee_awardsrecog",
                    'PUB'=>"employee_pgd",
                    'S'=>"employee_scholarship",
                    // 'PI'=>"Membership in Civic Organization",
                    // 'TW'=>"Community Involvement",
                    // 'SE'=>"employee_resource",
                    // 'MAPO'=>"Speaking Engagements",
                    // 'AFH'=>"employee_administrative",
                    'SCTT'=>"employee_subj_competent_to_teach"
                    // 'SCTTS'=>"Position Held in Poveda",
                    // 'LD'=>"Position Held in Poveda");
                    );

    }

    public static function employmentYearList($attendees=''){
		$option = "";
		for($x=1; $x <= 100; $x++){
            if($attendees){
                if(in_array($x, $attendees)) $option .= "<option value = '".$x."' selected>Year ".$x."</option>";
                else $option .= "<option value = '".$x."'>Year ".$x."</option>";
            }else{
                $option .= "<option value = '".$x."'>Year ".$x."</option>";
            }
		}
		return $option;
    }

    public static function convertFormDataToArray($formdata){
        $data_arr = array();
        $formdata = str_replace(';', '', $formdata);
        $formdata = explode("&", $formdata);
        foreach($formdata as $row){
            list($key, $value) = explode("=", $row);
            $key = str_replace(";", "", $key);
            if($key != "undefined") $data_arr[$key] = $value;
        }

        return $data_arr;
    }

    public static function convertMime($mime) {
        $mime_map = array(
            'video/3gpp2'                                                               => '3g2',
            'video/3gp'                                                                 => '3gp',
            'video/3gpp'                                                                => '3gp',
            'application/x-compressed'                                                  => '7zip',
            'audio/x-acc'                                                               => 'aac',
            'audio/ac3'                                                                 => 'ac3',
            'application/postscript'                                                    => 'ai',
            'audio/x-aiff'                                                              => 'aif',
            'audio/aiff'                                                                => 'aif',
            'audio/x-au'                                                                => 'au',
            'video/x-msvideo'                                                           => 'avi',
            'video/msvideo'                                                             => 'avi',
            'video/avi'                                                                 => 'avi',
            'application/x-troff-msvideo'                                               => 'avi',
            'application/macbinary'                                                     => 'bin',
            'application/mac-binary'                                                    => 'bin',
            'application/x-binary'                                                      => 'bin',
            'application/x-macbinary'                                                   => 'bin',
            'image/bmp'                                                                 => 'bmp',
            'image/x-bmp'                                                               => 'bmp',
            'image/x-bitmap'                                                            => 'bmp',
            'image/x-xbitmap'                                                           => 'bmp',
            'image/x-win-bitmap'                                                        => 'bmp',
            'image/x-windows-bmp'                                                       => 'bmp',
            'image/ms-bmp'                                                              => 'bmp',
            'image/x-ms-bmp'                                                            => 'bmp',
            'application/bmp'                                                           => 'bmp',
            'application/x-bmp'                                                         => 'bmp',
            'application/x-win-bitmap'                                                  => 'bmp',
            'application/cdr'                                                           => 'cdr',
            'application/coreldraw'                                                     => 'cdr',
            'application/x-cdr'                                                         => 'cdr',
            'application/x-coreldraw'                                                   => 'cdr',
            'image/cdr'                                                                 => 'cdr',
            'image/x-cdr'                                                               => 'cdr',
            'zz-application/zz-winassoc-cdr'                                            => 'cdr',
            'application/mac-compactpro'                                                => 'cpt',
            'application/pkix-crl'                                                      => 'crl',
            'application/pkcs-crl'                                                      => 'crl',
            'application/x-x509-ca-cert'                                                => 'crt',
            'application/pkix-cert'                                                     => 'crt',
            'text/css'                                                                  => 'css',
            'text/x-comma-separated-values'                                             => 'csv',
            'text/comma-separated-values'                                               => 'csv',
            'application/vnd.msexcel'                                                   => 'csv',
            'application/x-director'                                                    => 'dcr',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'   => 'docx',
            'application/x-dvi'                                                         => 'dvi',
            'message/rfc822'                                                            => 'eml',
            'application/x-msdownload'                                                  => 'exe',
            'video/x-f4v'                                                               => 'f4v',
            'audio/x-flac'                                                              => 'flac',
            'video/x-flv'                                                               => 'flv',
            'image/gif'                                                                 => 'gif',
            'application/gpg-keys'                                                      => 'gpg',
            'application/x-gtar'                                                        => 'gtar',
            'application/x-gzip'                                                        => 'gzip',
            'application/mac-binhex40'                                                  => 'hqx',
            'application/mac-binhex'                                                    => 'hqx',
            'application/x-binhex40'                                                    => 'hqx',
            'application/x-mac-binhex40'                                                => 'hqx',
            'text/html'                                                                 => 'html',
            'image/x-icon'                                                              => 'ico',
            'image/x-ico'                                                               => 'ico',
            'image/vnd.microsoft.icon'                                                  => 'ico',
            'text/calendar'                                                             => 'ics',
            'application/java-archive'                                                  => 'jar',
            'application/x-java-application'                                            => 'jar',
            'application/x-jar'                                                         => 'jar',
            'image/jp2'                                                                 => 'jp2',
            'video/mj2'                                                                 => 'jp2',
            'image/jpx'                                                                 => 'jp2',
            'image/jpm'                                                                 => 'jp2',
            'image/jpeg'                                                                => 'jpeg',
            'image/pjpeg'                                                               => 'jpeg',
            'application/x-javascript'                                                  => 'js',
            'application/json'                                                          => 'json',
            'text/json'                                                                 => 'json',
            'application/vnd.google-earth.kml+xml'                                      => 'kml',
            'application/vnd.google-earth.kmz'                                          => 'kmz',
            'text/x-log'                                                                => 'log',
            'audio/x-m4a'                                                               => 'm4a',
            'application/vnd.mpegurl'                                                   => 'm4u',
            'audio/midi'                                                                => 'mid',
            'application/vnd.mif'                                                       => 'mif',
            'video/quicktime'                                                           => 'mov',
            'video/x-sgi-movie'                                                         => 'movie',
            'audio/mpeg'                                                                => 'mp3',
            'audio/mpg'                                                                 => 'mp3',
            'audio/mpeg3'                                                               => 'mp3',
            'audio/mp3'                                                                 => 'mp3',
            'video/mp4'                                                                 => 'mp4',
            'video/mpeg'                                                                => 'mpeg',
            'application/oda'                                                           => 'oda',
            'audio/ogg'                                                                 => 'ogg',
            'video/ogg'                                                                 => 'ogg',
            'application/ogg'                                                           => 'ogg',
            'application/x-pkcs10'                                                      => 'p10',
            'application/pkcs10'                                                        => 'p10',
            'application/x-pkcs12'                                                      => 'p12',
            'application/x-pkcs7-signature'                                             => 'p7a',
            'application/pkcs7-mime'                                                    => 'p7c',
            'application/x-pkcs7-mime'                                                  => 'p7c',
            'application/x-pkcs7-certreqresp'                                           => 'p7r',
            'application/pkcs7-signature'                                               => 'p7s',
            'application/pdf'                                                           => 'pdf',
            'application/octet-stream'                                                  => 'pdf',
            'application/x-x509-user-cert'                                              => 'pem',
            'application/x-pem-file'                                                    => 'pem',
            'application/pgp'                                                           => 'pgp',
            'application/x-httpd-php'                                                   => 'php',
            'application/php'                                                           => 'php',
            'application/x-php'                                                         => 'php',
            'text/php'                                                                  => 'php',
            'text/x-php'                                                                => 'php',
            'application/x-httpd-php-source'                                            => 'php',
            'image/png'                                                                 => 'png',
            'image/x-png'                                                               => 'png',
            'application/powerpoint'                                                    => 'ppt',
            'application/vnd.ms-powerpoint'                                             => 'ppt',
            'application/vnd.ms-office'                                                 => 'ppt',
            'application/msword'                                                        => 'ppt',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
            'application/x-photoshop'                                                   => 'psd',
            'image/vnd.adobe.photoshop'                                                 => 'psd',
            'audio/x-realaudio'                                                         => 'ra',
            'audio/x-pn-realaudio'                                                      => 'ram',
            'application/x-rar'                                                         => 'rar',
            'application/rar'                                                           => 'rar',
            'application/x-rar-compressed'                                              => 'rar',
            'audio/x-pn-realaudio-plugin'                                               => 'rpm',
            'application/x-pkcs7'                                                       => 'rsa',
            'text/rtf'                                                                  => 'rtf',
            'text/richtext'                                                             => 'rtx',
            'video/vnd.rn-realvideo'                                                    => 'rv',
            'application/x-stuffit'                                                     => 'sit',
            'application/smil'                                                          => 'smil',
            'text/srt'                                                                  => 'srt',
            'image/svg+xml'                                                             => 'svg',
            'application/x-shockwave-flash'                                             => 'swf',
            'application/x-tar'                                                         => 'tar',
            'application/x-gzip-compressed'                                             => 'tgz',
            'image/tiff'                                                                => 'tiff',
            'text/plain'                                                                => 'txt',
            'text/x-vcard'                                                              => 'vcf',
            'application/videolan'                                                      => 'vlc',
            'text/vtt'                                                                  => 'vtt',
            'audio/x-wav'                                                               => 'wav',
            'audio/wave'                                                                => 'wav',
            'audio/wav'                                                                 => 'wav',
            'application/wbxml'                                                         => 'wbxml',
            'video/webm'                                                                => 'webm',
            'audio/x-ms-wma'                                                            => 'wma',
            'application/wmlc'                                                          => 'wmlc',
            'video/x-ms-wmv'                                                            => 'wmv',
            'video/x-ms-asf'                                                            => 'wmv',
            'application/xhtml+xml'                                                     => 'xhtml',
            'application/excel'                                                         => 'xl',
            'application/msexcel'                                                       => 'xls',
            'application/x-msexcel'                                                     => 'xls',
            'application/x-ms-excel'                                                    => 'xls',
            'application/x-excel'                                                       => 'xls',
            'application/x-dos_ms_excel'                                                => 'xls',
            'application/xls'                                                           => 'xls',
            'application/x-xls'                                                         => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'         => 'xlsx',
            'application/vnd.ms-excel'                                                  => 'xlsx',
            'application/xml'                                                           => 'xml',
            'text/xml'                                                                  => 'xml',
            'text/xsl'                                                                  => 'xsl',
            'application/xspf+xml'                                                      => 'xspf',
            'application/x-compress'                                                    => 'z',
            'application/x-zip'                                                         => 'zip',
            'application/zip'                                                           => 'zip',
            'application/x-zip-compressed'                                              => 'zip',
            'application/s-compressed'                                                  => 'zip',
            'multipart/x-zip'                                                           => 'zip',
            'text/x-scriptzsh'                                                          => 'zsh',
        );

        return isset($mime_map[$mime]) === true ? $mime_map[$mime] : false;
    }

    public static function applicantForm(){
        return array("applicant_education", "applicant_eligibilities", "applicant_subj_competent_to_teach", "applicant_credentials", "applicant_workshops");
    }

    public static function apiUrl(){
        return "http://202.57.49.107:6271";
    }

    public static function is_connect_internet(){
        $connected = @fsockopen("www.google.com", 80); 
                                            //website, port  (try 80 or 443)
        if ($connected){
            $is_conn = true; //action when connected
            fclose($connected);
        }else{
            $is_conn = false; //action in connection failure
        }
        return $is_conn;

    }

     public static function selectYear(){
        $return = '';
        for($x = date("Y"); $x >= 1970; $x--){
            $sel_str = $selected == $x ? 'selected' : '';
            $return .= "<option value='$x' $sel_str>$x</option>";
        }
        return $return;
    }

    public static function selectSchoolYear($selected='', $existing=array()){
        $return = $syStart = '';
        for($x = date("Y"); $x >= 1970; $x--){
            $syExist = '';
            if($syStart != ''){
                $sy = $x.'-'.$syStart;
                foreach ($existing as $key => $value) {
                    if($selected !== $value['sy']){
                        if($value['sy'] === $sy) $syExist = 'sy';
                    }
                }
                if($syExist != 'sy'){
                    if($selected == $sy)  $return .= "<option value='$sy' selected>$sy</option>";
                    else $return .= "<option value='$sy'  $syExist>$sy</option>";
                }
            }
            $syStart = $x; 
        }
        return $return;
    }

    public static function pd($data, $isdump=false){
		if($isdump){
			echo "<pre>"; var_dump($data);
		}else{
			echo "<pre>"; print_r($data);
		}
	}

    public static function _e($string){
        if(!is_array($string)) return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    public static function _array_XHEP($array){
        $data = array();
        foreach($array as $key => $val){
            $data[$key] = GLOBALS::_e($val);
        }

        return $data;
    }

    public static function result_XHEP($query){
        $data = array();
        foreach ($query as $key => $value) {
            foreach ($value as $keyy => $vall) {
                $data[$key]->$keyy = GLOBALS::_e($vall);
            }
        }
        return $data;
    }

    public static function resultarray_XHEP($query){
        $data = array();
        foreach ($query as $key => $value) {
            foreach ($value as $keyy => $vall) {
                $data[$key][$keyy] = GLOBALS::_e($vall);
            }
        }
        return $data;
    }

    public static function XHEP($query){
        foreach ($query->row(0) as $key => $value) {
            $data[$key] = GLOBALS::_e($value);
        }
        return $data;
    }

}