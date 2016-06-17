<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MX_Controller {

	
	function __construct()
    {
        parent::__construct();


        if($_SERVER['REMOTE_ADDR']!='85.72.61.177'){

        	die('Page does not exist');

        }
    }

	
	public function index($pass=null){
		$this->dashboard($pass);
	}
	public function dashboard($pass=null){


		/*if($pass!='efi' && $pass!='george' && $pass!='alex' && $pass!='evans'){

			die("<a href='https://www.etd.gr'>Welcome, Click Here Please</a>");
			
		}*/

		$data['title'] = 'Dashboard';

		$this->load->view('templates/header',$data);
		$this->load->view('home', $data);
		$this->load->view('templates/footer',$data);

			

		
	}


	public function xmlUploadUpdate(){



		$config["upload_path"]="./files/uploads";
		$config["allowed_types"]="xml";
		$this->load->library('upload', $config);

		if (!file_exists('files')) {
		    mkdir('files', 0777, true);
			}

		if (!file_exists('files/uploads')) {
		    mkdir('files/uploads', 0777, true);
			}	


		if($this->upload->do_upload('file')){


			$data = $this->upload->data();
			
			$filename = $data['file_name'];

			//$table =  $_POST['cat'];
			echo Modules::run("categories/updateItem", $filename, $_POST['cat']);

		}else{
			echo 'Πρόβλημα με το αρχείο. Δοκιμάστε Ξανά.';
		}



	}





}




?>