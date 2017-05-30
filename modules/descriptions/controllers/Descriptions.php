<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Descriptions extends MX_Controller {

	
 
	public function index()
	{	 
		$data['title'] = 'Περιγραφή Προϊόντων';
		$data['basic_templates'] = Modules::run('crud/get','char_blocks_basic');
		$data['specific_templates'] = Modules::run('crud/get','char_blocks_specific');
		$this->load->view('templates/header' ,$data);
		$this->load->view('descriptions');
		$this->load->view('templates/footer');
	} 

	public function add()
	{
		$data['title'] = 'Νέα Περιγραφή';
		$data['categories'] = Modules::run('categories/fullCategoriesArray');
		
		$this->load->view('templates/header' ,$data);
		$this->load->view('add');
		$this->load->view('templates/footer');
	}


	public function getChars($type, $value){
		$return = '';
		if($type == "categories"){
			$this->session->set_userdata('table',$value);
			$result = $this->db->query("SELECT `COLUMN_NAME` 
			FROM `INFORMATION_SCHEMA`.`COLUMNS` 
			WHERE `TABLE_SCHEMA`='etd67140_xml'
			    AND `TABLE_NAME`='$value';");
			
			$excludes = array(	'id','sku','product_number','brand','title','model','description','warranty','year_warranty','doa','volumetric_weight','shipping_class','etd_title','skroutz_title','supplier_product_url','product_url','product_url_pdf','support_tel','support_url','new_item');
			foreach ($result->result() as $key => $value) {

				if(!in_array($value->COLUMN_NAME, $excludes))
					$return .= '<option  onclick=""  value="'.$value->COLUMN_NAME.'">'.ucfirst($value->COLUMN_NAME).'</option>';

			}
			
			
		}else{
			$result = $this->db->query("SELECT DISTINCT (".$value.") from ".$this->session->table." ORDER BY ".$value." ASC");
			foreach ($result->result() as $key => $char) {
				if($char->$value!='')
					$return .= '<option  onclick=""  value="'.$char->$value.'">'.ucfirst($char->$value).'</option>';

			}
		}

			echo $return;


	}
	public function addBasic(){


		if(empty($this->input->post())){
			exit('Stop Submitting!!!');
		}
		
		$this->form_validation->set_rules('category', 'Κατηγορία', 'trim|required');
		$this->form_validation->set_rules('char', 'Τύπος Χαρακτηριστικού', 'trim|required');
		$this->form_validation->set_rules('char_spec', 'Χαρακτηριστικό', 'trim|required');
		$this->form_validation->set_rules('title', 'Τίτλος', 'trim|required');
		$this->form_validation->set_rules('description', 'Περιγραφή', 'trim|required');
	    //$this->form_validation->set_rules('image', 'Φωτογραφία', 'trim|required');

	    $file_name1 = preg_replace("/[^a-zA-Z0-9-_]/", "", $this->input->post('char'));
	    $file_name2 = preg_replace("/[^a-zA-Z0-9-_]/", "", $this->input->post('char_spec'));
	    $file_name = $file_name1.'_'.$file_name2;

	    $config['upload_path']          = './images/descriptions/';
        $config['allowed_types']        = 'jpeg|jpg|png';
        $config['file_name']			= $file_name;
        $config['max_size']             = '1000KB';
        $config['max_width']            = 1024;
        $config['max_height']           = 768;

        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload('image'))
        {
                $error = array('error' => $this->upload->display_errors());
                print_r($error);

                //$this->load->view('upload_form', $error);
        }
        else
        {
                $data = array('upload_data' => $this->upload->data());

                //$this->load->view('upload_success', $data);
        }

	    if($this->form_validation->run()){
	    	echo "success"; 
	    
	    }else{
	    	$this->add();
	    }


		


	}



	public function ifExistsBasic($table, $type, $char){
			$where = array('category' => $table,
							'char' => $type,
							'char_spec' => $char );

			$result = Modules::run('crud/get', 'char_blocks_basic', $where);
			if($result){
				echo 'error';
			}
			else{
				echo 'ok';
			}
	}

	public function do_upload()
        {
                $config['upload_path']          = './uploads/descriptions';
                $config['allowed_types']        = 'jpeg|jpg|png';
                $config['max_size']             = 1000;
                $config['max_width']            = 1024;
                $config['max_height']           = 768;

                $this->load->library('upload', $config);

                if ( ! $this->upload->do_upload('userfile'))
                {
                        $error = array('error' => $this->upload->display_errors());

                        $this->load->view('upload_form', $error);
                }
                else
                {
                        $data = array('upload_data' => $this->upload->data());

                        $this->load->view('upload_success', $data);
                }
        }
}

?> 