<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require_once dirname(__FILE__) . '/tcpdf/tcpdf.php';

/**
* Integrating TCPDF to CodeIgniter
*/
class PdfCreator_tcpdf extends TCPDF{
	
	function __construct(){
		parent::__construct();
	}
}

/* End of file PdfCreator.php*/
/* Location: ./application/libraries/PdfCreator.php*/