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
				
			/*	$this->form_validation->set_rules('ticket_nr', 'Αριθμός Δελτίου', 'required|min_length[6]|max_length[6]',
	                        array('required' => 'Συμπλιρώστε τον αριθμό %s.')
	                );*/
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
					'technician' =>  $this->input->post('technician'),
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

	public function edit($id){

		if(empty($this->input->post())){
			$where = array('id' => $id);
			$ticket = $this->db->get_where('services',$where);
			
			if($ticket->num_rows()<1){
				exit('The ticket does not exist');
			}

			$data['ticket'] = $ticket->result_array();
		}

		

		if(!empty($this->input->post())){
			$this->load->library('form_validation');
				
				/*$this->form_validation->set_rules('ticket_nr', 'Αριθμός Δελτίου', 'required|min_length[6]|max_length[6]',
	                        array('required' => 'Συμπλιρώστε τον αριθμό %s.')
	                );*/
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
		


			$this->load->view('edit_keelpno', $data);
	}

	public function daily(){

		if(!empty($this->input->post())){
			if($this->createDaily()){
				echo "<div style='color:green'>Created</div>";
			}else{
				echo validation_errors('<div style="color:red" class="error">', '</div>');
			}
		}


		$this->load->view('daily');
	}

	private function createDaily(){

		$d = $this->input->post('day');
		$m = $this->input->post('month');
		$y = $this->input->post('year');

		$time = strtotime($m.'/'.$d.'/'.$y. '00:00:01');

		$date = date('Y-m-d', $time);

		$this->db->where('ticket_date',$date);
		$this->db->where('category',$this->input->post('category'));
		$this->db->where('technician',$this->input->post('technician'));
		$this->db->where('daily', '0');
		$services = $this->db->get('services');
		//print_r($services->result());

		$tasks = array();
		$comments = '';

		foreach ($services->result() as $service) {
			$tmpTasks = json_decode($service->tasks_list);
			$tasks = array_merge($tmpTasks, $tasks);
			
			$comments .= ' '.$service->technician_comments;
		}
		
		$theDay = $dayofweek = date('N', strtotime($date));
		

		if($this->input->post('technician') == 'Άλεξ'){
			$defaultTasks = array();
			$defaultComments = '';
			if($theDay==1){
				if($this->input->post('category')=='Πληροφορική'){
					$defaultTasks = array("34","35","36","39","40","41");
					$defaultComments = "Έλεγχος δικτυακού εξοπλισμού και της καλωδίωσης στα STAFF του 1ου, 2ου και 3ου ορόφων.";
				}else if($this->input->post('category')=='Τηλεφωνία'){

				}else if($this->input->post('category')=='VOIP'){

				}
			}else if($theDay==2){
				if($this->input->post('category')=='Πληροφορική'){
					$defaultTasks = array("33","38");
					$defaultComments = "Έλεγχος δικτυακού εξοπλισμού και της καλωδίωσης στα STAFF 02, 0-22, 0-18.";
				}else if($this->input->post('category')=='Τηλεφωνία'){

				}else if($this->input->post('category')=='VOIP'){

				}
			}else if($theDay==3){
				if($this->input->post('category')=='Πληροφορική'){
					$defaultTasks = array("1","37","42");
					$defaultComments = "Έλεγχος του Anti-Virus Server και έλεγχος για ενημερώσεις του λογισμικού. Έλεγχος της καλωδίωσης και του δικτυακού εξοπλισμού στο STAFF του 4ου ορόφου";
				}else if($this->input->post('category')=='Τηλεφωνία'){

				}else if($this->input->post('category')=='VOIP'){

				}
			}
			$tasks = array_merge($defaultTasks, $tasks);
			$comments = $defaultComments.' '.$comments;
		}

		$insertData = array(
					'ticket_nr' => $this->input->post('ticket_nr'),
					'ticket_date' => $date,
					'technician' =>  $this->input->post('technician'),
					'category' => $this->input->post('category'),
					'tasks_list' => json_encode($tasks),
					'technician_comments' => $comments,
					'creation_date' => date('Y-m-d H:i:s'),
					'daily' => '1'

					);

				

		$this->db->insert('services', $insertData);

	}

}




?> 