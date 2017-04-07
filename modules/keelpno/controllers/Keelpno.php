<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Keelpno extends MX_Controller {



	function __construct(){
		parent::__construct();
		$this->load->model('keelpno_model');
	} 

	public function index(){

		if(!empty($this->input->post())){
			$this->load->library('form_validation');
				
				$this->form_validation->set_rules('ticket_nr', 'Αριθμός Δελτίου', 'required|min_length[6]|max_length[6]',
	                        array('required' => 'Συμπλιρώστε τον αριθμό %s.')
	                );
				$this->form_validation->set_rules('tasks_lists[]', 'Κατηγορία', 'required',
	                        array('required' => 'Επιλέξτε τουλάχιστον μία %s.')
	                );

				$this->form_validation->set_rules('technician_comments', 'Σχόλια Τεχνικού', 'required|trim',
	                        array('required' => 'Συμπλιρώστε τα %s.')
	                );

				 if ($this->form_validation->run() == FALSE)
                {
                        
                }
                else
                {



                $d = $this->input->post('day');
				$m = $this->input->post('month');
				$y = $this->input->post('year');

				$time = strtotime($m.'/'.$d.'/'.$y. '00:00:01');

				$date = date('Y-m-d H:i:s', $time);

				$insertData = array(
					'ticket_nr' => $this->input->post('ticket_nr'),
					'ticket_date' => $date,
					'technician' => 'Alex Tisakov',
					'category' => $this->input->post('category'),
					'tasks_list' => json_encode($this->input->post('tasks_lists')),
					'customer_comments' => $this->input->post('customer_comments'),
					'technician_comments' => $this->input->post('technician_comments'),
					'creation_date' => date('Y-m-d H:i:s')

					);

				

					$this->db->insert('services', $insertData);
					redirect(base_url().'keelpno','refresh');
                }
			
			
				
			
		}

		$data['title'] = 'ΔΤΕ';
		$data['categories'] = $this->keelpno_model->categories();
		


			$this->load->view('keelpno', $data);
	}

	public function problematicWeight(){
		$this->load->model('problematic_model');
		return $this->problematic_model->weight();
	}

	



}




?> 