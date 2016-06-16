<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Extract extends MX_Controller {

	

	public function xml($table=null)
	{	
		//if(!$table){redirect('https://etd.gr/','refresh');}

		$this->load->model('extract_model');
		
		if($this->extract_model->xml($table)){

			
			echo '<a class="btn btn-block btn-success btn-md"
			 target="_blank" href="https://etd.gr/xml/files/'.$table.'.xml">Προβολή</a>';
		}else{
			echo 'Σφάλμα';
		}



			
	
	}

	
}




?> 