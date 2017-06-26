<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Descriptions extends MX_Controller {

	public function __construct()
    {
        parent::__construct();
        
        $this->load->model('Descriptions_model');
       // $this->load->model('crud/Crud_model');
    }

	
 
	public function index()
	{	 
		$data['title'] = 'Περιγραφή Προϊόντων';
		$data['basic_templates'] = Modules::run('crud/get','char_blocks_basic');
		$data['specific_templates'] = Modules::run('crud/get','char_blocks_specific');
		$this->load->view('templates/header' ,$data);
		$this->load->view('descriptions');
		$this->load->view('templates/footer');
	} 

	public function add($table='basic')
	{
		if ($this->input->server('REQUEST_METHOD') === 'POST')
		{
			$file_name0 = preg_replace("/[^a-zA-Z0-9-_]/", "", $this->input->post('category'));
			$file_name1 = preg_replace("/[^a-zA-Z0-9-_]/", "", $this->input->post('char'));
		    $file_name2 = preg_replace("/[^a-zA-Z0-9-_]/", "", $this->input->post('char_spec'));
		    $file_name = $file_name0.'-'.$file_name1.'_'.$file_name2;

		    $config['upload_path']          = './images/descriptions/';
	        $config['allowed_types']        = 'jpeg|jpg|png';
	        $config['file_name']			= $file_name;
	        $config['max_size']             = '1000KB';
	        $config['max_width']            = 1024;
	        $config['max_height']           = 768;
	        $config['overwrite']     		= TRUE;

	        $this->load->library('upload', $config);

	        if($this->upload->do_upload('image'))
            {
            	$data = array('upload_data' => $this->upload->data());
                $image_name = $data['upload_data']['file_name'];

                $this->form_validation->set_rules('category', 'Κατηγορία', 'trim|required');
				$this->form_validation->set_rules('char', 'Τύπος Χαρακτηριστικού', 'trim|required');
				$this->form_validation->set_rules('char_spec', 'Χαρακτηριστικό', 'trim|required');
				$this->form_validation->set_rules('title', 'Τίτλος', 'trim|required');
				$this->form_validation->set_rules('description', 'Περιγραφή', 'trim|required');
				$this->form_validation->set_rules('text_color', 'Χρώμα Κειμένου', 'trim|required');
				$this->form_validation->set_rules('background_color', 'Χρώμα Πλαισίου', 'trim|required');
				//$this->form_validation->set_rules('image', 'Φωτογραφία', 'trim|required');
				$this->form_validation->set_rules('important', 'Προτεραιότητα', 'trim|required');
				if($table == 'specific')
				{
					$this->form_validation->set_rules('sku', 'SKU', 'trim|required');
					$this->form_validation->set_rules('brand', 'Brand', 'trim|required');
				}

				$this->form_validation->set_error_delimiters('<div class="alert alert-danger"><span class="close" data-dismiss="alert">&times</span><strong>', '</strong></div>');

				 if($this->form_validation->run()){
				 	$data_to_store = array(
                        'category' => $this->input->post('category'),
                        'char' => $this->input->post('char'),
                        'char_spec' => $this->input->post('char_spec'),
                        'title' => $this->input->post('title'),
                        'description' => $this->input->post('description'),
                        'text_color' => $this->input->post('text_color'),
                        'background_color' => $this->input->post('background_color'),
                        'image' =>  $image_name,
                        'important' => $this->input->post('important')
                    );
                    if($table == 'specific')
					{
						$data_to_store ['sku'] = $this->input->post('sku');
						$data_to_store ['brand'] = $this->input->post('brand');
					}

                    //if($this->Crud_model->insert('char_blocks_basic', $data_to_store))
                    if($this->Descriptions_model->store_chars($table, $data_to_store))
                    {
                    	$FlashData['Message']= 'Τα χαρακτηριστικά <strong>"'. $data_to_store['category'] .'-'.$data_to_store['char'] .'-'.$data_to_store['char_spec'].'"</strong> αποθηκεύτηκε με επιτυχία.';
                        $FlashData['type'] = 'success';
                    }else{
                        $FlashData['Message']= '<strong>Kάτι πήγε λάθος!</strong> Ελέγξτε τα στοιχεία και δοκιμάστε πάλι.';
                        $FlashData['type'] = 'danger';
                    }
                    $this->session->set_flashdata('flash_message', $FlashData);
			    
			    }

            }
	        else 
	        {
	                $FlashData['Message']= '<strong>Η φωτογραφία δεν αποθηκεύτηκε</strong>'.$this->upload->display_errors();
	                $FlashData['type'] = 'danger';
	                $this->session->set_flashdata('flash_message', $FlashData);

	                $error = array('error' => $this->upload->display_errors());
	        }
		}

		$data['title'] = 'Νέα Περιγραφή';
		$data['categories'] = Modules::run('categories/fullCategoriesArray');
		$data['table'] = $table;
		
		$this->load->view('templates/header' ,$data);
		$this->load->view('add');
		$this->load->view('templates/footer');
	}

	public function update()
    {
        $id = $this->uri->segment(4);
        $table = $this->uri->segment(3);

        if ($this->input->server('REQUEST_METHOD') === 'POST')
        {
        	/*echo 'Test Image:';
        	print_r($this->input->post('image'));
        	exit();*/
        	$update_image = false;
        	$image_name ='';
        	if($this->input->post('image') != '')
        	{
	        	$file_name0 = preg_replace("/[^a-zA-Z0-9-_]/", "", $this->input->post('category'));
	        	$file_name1 = preg_replace("/[^a-zA-Z0-9-_]/", "", $this->input->post('char'));
			    $file_name2 = preg_replace("/[^a-zA-Z0-9-_]/", "", $this->input->post('char_spec'));
			    $file_name = $file_name0.'-'.$file_name1.'_'.$file_name2;

			    $config['upload_path']          = './images/descriptions/';
		        $config['allowed_types']        = 'jpeg|jpg|png';
		        $config['file_name']			= $file_name;
		        $config['max_size']             = '1000KB';
		        $config['max_width']            = 1024;
		        $config['max_height']           = 768;
		        $config['overwrite']     		= TRUE;

		        $this->load->library('upload', $config);

		        if($this->upload->do_upload('image') )
	            {
            		$data = array('upload_data' => $this->upload->data());
                	$image_name = $data['upload_data']['file_name'];

                	$update_image = true;
	            }

			}

	        if($update_image || $this->input->post('image') == '')
            {

                $this->form_validation->set_rules('category', 'Κατηγορία', 'trim|required');
				$this->form_validation->set_rules('char', 'Τύπος Χαρακτηριστικού', 'trim|required');
				$this->form_validation->set_rules('char_spec', 'Χαρακτηριστικό', 'trim|required');
				$this->form_validation->set_rules('title', 'Τίτλος', 'trim|required');
				$this->form_validation->set_rules('description', 'Περιγραφή', 'trim|required');
				$this->form_validation->set_rules('text_color', 'Χρώμα Κειμένου', 'trim|required');
				$this->form_validation->set_rules('background_color', 'Χρώμα Πλαισίου', 'trim|required');
				//$this->form_validation->set_rules('image', 'Φωτογραφία', 'trim|required');
				$this->form_validation->set_rules('important', 'Προτεραιότητα', 'trim|required');

				$this->form_validation->set_error_delimiters('<div class="alert alert-danger"><span class="close" data-dismiss="alert">&times</span><strong>', '</strong></div>');

				 if($this->form_validation->run()){
				 	$data_to_store = array(
                        'category' => $this->input->post('category'),
                        'char' => $this->input->post('char'),
                        'char_spec' => $this->input->post('char_spec'),
                        'title' => $this->input->post('title'),
                        'description' => $this->input->post('description'),
                        'text_color' => $this->input->post('text_color'),
                        'background_color' => $this->input->post('background_color'),
                        'image' =>  $image_name,
                        'important' => $this->input->post('important')
                    );
                    if(!$update_image)
                    	unset($data_to_store['image']);

                    //if($this->Crud_model->insert('char_blocks_basic', $data_to_store))
                    if($this->Descriptions_model->store_chars($table, $data_to_store, $id))
                    {
                    	$FlashData['Message']= 'Τα χαρακτηριστικά <strong>"'. $data_to_store['category'] .'-'.$data_to_store['char'] .'-'.$data_to_store['char_spec'].'"</strong> ενημερώθηκε με επιτυχία.';
                        $FlashData['type'] = 'success';
                    }else{
                        $FlashData['Message']= '<strong>Kάτι πήγε λάθος!</strong> Ελέγξτε τα στοιχεία και δοκιμάστε πάλι.';
                        $FlashData['type'] = 'danger';
                    }
                    $this->session->set_flashdata('flash_message', $FlashData);
			    
			    }

            }
	        else 
	        {
	                $FlashData['Message']= '<strong>Η φωτογραφία δεν αποθηκεύτηκε</strong>'.$this->upload->display_errors();
	                $FlashData['type'] = 'danger';
	                $this->session->set_flashdata('flash_message', $FlashData);

	                $error = array('error' => $this->upload->display_errors());
	        }

        }

        $data['title'] = 'Νέα Περιγραφή';
        $data['chars_data'] = $this->Descriptions_model->get_chars_by_id($table, $id);
        $data['table'] = $table;
        $data['id'] = $id;

		$data['categories'] = Modules::run('categories/fullCategoriesArray');
		$table_full = $data['chars_data'][0]['category'];

		$data['category_char'] = $this->getCategoryChars($table_full, 'list_chars');
		$data['category_char_spec'] = $this->getCategoryChars($table_full, $data['chars_data'][0]['char']);
		
		
		$this->load->view('templates/header' ,$data);
		$this->load->view('edit');
		$this->load->view('templates/footer');

    }

    public function getCategoryChars($category, $char){
    	if($char == 'list_chars')
    	{
    		$result = $this->db->query("SELECT `COLUMN_NAME` 
				FROM `INFORMATION_SCHEMA`.`COLUMNS` 
				WHERE `TABLE_SCHEMA`='etd67140_xml'
			    AND `TABLE_NAME`='$category';");

    		$excludes = array(	'id','sku','product_number','brand','title','model','description','warranty','year_warranty','doa','volumetric_weight','shipping_class','etd_title','skroutz_title','supplier_product_url','product_url','product_url_pdf','support_tel','support_url','new_item');

    		$char_type = array();
    		foreach ($result->result() as $key => $value) {
	    		if(!in_array($value->COLUMN_NAME, $excludes))
	    			$char_type[$value->COLUMN_NAME] = $value->COLUMN_NAME;
	    	}

    		return $char_type;
    	}
    	else
    	{
    		$result =  $this->db->query("SELECT DISTINCT (".$char.") from ".$category." ORDER BY ".$char." ASC");

    		$char_type_spec = array();

    		foreach ($result->result_array() as $key => $value) {

	    			$char_type_spec[$value[$char]] = $value[$char];
	    	}

    		return $char_type_spec;
    	}
    }


	public function getChars($type, $value){
		//$return = '';
		$return = '<option value="">----</option>';
		if($type == "categories"){
			$this->session->set_userdata('table',$value);
			$result = $this->db->query("SELECT `COLUMN_NAME` 
			FROM `INFORMATION_SCHEMA`.`COLUMNS` 
			WHERE `TABLE_SCHEMA`='etd67140_xml'
			    AND `TABLE_NAME`='$value';");
			
			$excludes = array(	'id','sku','product_number','brand','title',/*'model',*/'description','warranty','year_warranty','doa','volumetric_weight','shipping_class','etd_title','skroutz_title','supplier_product_url','product_url','product_url_pdf','support_tel','support_url','new_item');
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

	/*public function do_upload()
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
        }*/
}

?> 