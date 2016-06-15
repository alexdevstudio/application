<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Emails extends MX_Controller {

	

	public function send($s, $m, $to = null)
	{	
		if(!$to){

			$to = "alex@etd.gr";

		}		
				$config = Array(       
		            
		            'mailtype'  => 'html',
		            'charset'   => 'UTF-8'
		        );

				$this->load->library('email',$config);

				

				$this->email->from('eshop@etd.gr', 'XML Generator');
				$this->email->to($to);
				

				$this->email->subject($s);
				$this->email->message($m);

				$this->email->send();
			

		
	}
		





}




?> 