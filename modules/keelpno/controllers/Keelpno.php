<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Keelpno extends MX_Controller {



	function __construct(){
		parent::__construct();
		$this->load->model('keelpno_model');
	} 

	public function index(){
		$data['title'] = 'ΔΤΕ';
		$data['categories'] = $this->keelpno_model->categories();


		
		if(!empty($this->input->post()) && $this->input->post('client')!=''){
			$this->session->client = $this->input->post('client');
		}

		if(!empty($this->input->post()) && $this->input->post('user')!=''){
			$this->session->user = $this->input->post('user');
		}

		if(!empty($this->input->post()) && $this->input->post('type')!=''){
			$this->session->type = $this->input->post('type');
		}



		$this->load->view('menu', $data);
		$this->load->view('home', $data);
	}

	public function reset(){
		$this->session->unset_userdata('type');
		$this->session->unset_userdata('user');
		$this->session->unset_userdata('client');

		redirect(base_url().'keelpno','refresh');

	}

	public function add(){

		if(!isset($this->session->user) || !isset($this->session->user)){
				redirect(base_url().'keelpno','refresh');
		}

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
					'client' => $this->session->client,
					'technician' =>  $this->input->post('technician'),
					'category' => $this->input->post('category'),
					'tasks_list' => json_encode($this->input->post('tasks_lists')),
					'customer_comments' => $this->input->post('customer_comments'),
					'technician_comments' => $this->input->post('technician_comments'),
					'creation_date' => date('Y-m-d H:i:s')

					);

				

					$this->db->insert('services', $insertData);
					redirect(base_url().'keelpno/add','refresh');
                }
			
			
				
			
		}

		$data['title'] = 'ΔΤΕ';
		$data['categories'] = $this->keelpno_model->categories();
		


		$this->load->view('add', $data);
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

		$data['lastTickets'] = $this->getLastDailyTickets();
		
		if(!empty($this->input->post())){
			if($result = $this->createDaily()){
				echo "<div style='color:green'>Created: ".$result->row()->ticket_nr."</div>";
				$data['ticket_nr'] = $result->row()->ticket_nr;
			}else{
				echo validation_errors('<div style="color:red" class="error">', '</div>');
			}
		}

		$this->load->view('menu');
		$this->load->view('daily',$data);
	}

	private function getLastDailyTickets(){

		

		$categories = array('Πληροφορική', 'Τηλεφωνία', 'VOIP', 'COPIERS');
		$technicians = array('Άλεξ', 'Γιώργος', 'Τάκης', 'Θανάσης');

		foreach ($categories as $category) {
			
			
			foreach ($technicians as $technician) {
				$this->db->where('daily', 1);
				$this->db->where('category', $category);
				$this->db->order_by('ticket_nr','DESC');
				$this->db->limit(1);
				$this->db->where('technician', $technician);
				$ticket=$this->db->get('services');
				if($ticket->num_rows()>0){
					$result[$technician][$category] = $ticket->result();
				}

			}
		}

		return $result;


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
					$defaultTasks = array("60","61","63");
					$defaultComments = "Έλεγχος καλής λειτουργίας τηλ κέντρου ΚΕΕΛΠΝΟ και έλεγχος ασφάλειας της επικοινωνίας. Έλεγχος καλής λειτουργίας τηλ κέντρου ΕΠΙΔΗΜΙΟΛΟΓΙΑΣ και έλεγχος ασφάλειας της επικοινωνίας. Έλεγχος καλωδίωσης στα Staff των 1,2,3 ορόφων.";
				}else if($this->input->post('category')=='VOIP'){
					$defaultTasks = array("68","74");
					$defaultComments = "Έλεγχος PRI γραμμών 2106863200 - 2105212054. Ο έλεγχος εκτροπής 2105212054 στο 2106863200 πραγματοποιήθηκε και λειτουργεί σωστά.";
				}
			}else if($theDay==2){
				if($this->input->post('category')=='Πληροφορική'){
					$defaultTasks = array("33","38");
					$defaultComments = "Έλεγχος δικτυακού εξοπλισμού και της καλωδίωσης στα STAFF 02, 0-22, 0-18.";
				}else if($this->input->post('category')=='Τηλεφωνία'){
					$defaultTasks = array("60","61","63");
					$defaultComments = "Έλεγχος καλής λειτουργίας τηλ κέντρου ΚΕΕΛΠΝΟ και έλεγχος ασφάλειας της επικοινωνίας. Έλεγχος καλής λειτουργίας τηλ κέντρου ΕΠΙΔΗΜΙΟΛΟΓΙΑΣ και έλεγχος ασφάλειας της επικοινωνίας. Έλεγχος Dataroom, 0, 0-18 Staff.";
				}else if($this->input->post('category')=='VOIP'){
					$defaultTasks = array("68","51","75");
					$defaultComments = "Έλεγχος πιστοποιητικού ασφάλειας SSL στο τηλεφωνικό κέντρο VOIP. Έλεγχος συσκευών Yealink. Έλεγχος πιστοποιητικού ασφάλειας σε Yealink. ΕΛΕΓΧΟΣ ΑΣΦΑΛΕΙΑΣ ΤΗΛΕΠΙΚΟΙΝΩΝΙΑΚΩΝ ΔΙΚΤΥΩΝ.";
				}
			}else if($theDay==3){
				if($this->input->post('category')=='Πληροφορική'){
					$defaultTasks = array("1","37","42");
					$defaultComments = "Έλεγχος του Anti-Virus Server και έλεγχος για ενημερώσεις του λογισμικού. Το Anti-Virus χρήζει άμεσης ανανέωσης συνδρομής. Έλεγχος της καλωδίωσης και του δικτυακού εξοπλισμού στο STAFF του 4ου ορόφου";
				}else if($this->input->post('category')=='Τηλεφωνία'){
					$defaultTasks = array("60","61","63","65");
					$defaultComments = "Εβδομαδιαίο BackUp του τηλ κέντρου του ΚΕΕΛΠΝΟ. Εβδομαδιαίο BackUp του τηλ κέντρου τμήμα Επιδημιολογίας.";

				}else if($this->input->post('category')=='VOIP'){
					$defaultTasks = array("71");
					$defaultComments = "Πραγματοποιήθηκε έλεγχος του ηχογραφημένου μηνύματος.";
				}
			}else if($theDay==4){
				if($this->input->post('category')=='Πληροφορική'){

				}else if($this->input->post('category')=='Τηλεφωνία'){
					$defaultTasks = array("60","61","63");
					$defaultComments = "Έλεγχος προγραμματισμού του τηλ κέντρου του ΚΕΕΛΠΝΟ. Έλεγχος προγραμματισμού του τηλ κέντρου της Επιδημιολογίας.";

				}else if($this->input->post('category')=='VOIP'){
					$defaultTasks = array("72");
					$defaultComments = "Πραγματοποιήθηκε Backup του προγραμματισμού στο τηλεφωνικό κέντρο VOIP του ΚΕΠΙΧ";
				}
			}else if($theDay==5){
				if($this->input->post('category')=='Πληροφορική'){

				}else if($this->input->post('category')=='Τηλεφωνία'){
					$defaultTasks = array("60","61","63");
					$defaultComments = "Έλεγχος PRI καρτών και Smart Media στο τηλεφωνικό κέντρο του ΚΕΕΛΠΝΟ. Έλεγχος PRI καρτών και Smart Media στο τηλεφωνικό κέντρο της ΕΠΙΔΗΜΙΟΛΟΓΙΑΣ";

				}else if($this->input->post('category')=='VOIP'){
					$defaultTasks = array("74","73","75");
					$defaultComments = " Ο έλεγχος εκτροπής 2105212054 στο 2106863200 πραγματοποιήθηκε και λειτουργεί σωστά. Έλεγχος καταγραφής κλήσεων. ΕΛΕΓΧΟΣ ΑΣΦΑΛΕΙΑΣ ΤΗΛΕΠΙΚΟΙΝΩΝΙΑΚΩΝ ΔΙΚΤΥΩΝ";
				}
			}
			$tasks = array_merge($defaultTasks, $tasks);
			$comments = $defaultComments.' '.$comments;
		}
		else if($this->input->post('technician') == 'Γιώργος'){
			$defaultTasks = array();
			$defaultComments = '';
			if($this->input->post('category')=='Πληροφορική'){
				$defaultTasks = array("1","6");
				$defaultComments = "Έλεγχος Domain Controller ΚΕΕΛΠΝΟ. Έλεγχος εφαρμογής PRTG.";
			}
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
		$this->db->where('daily','1');
		$this->db->where('category',$this->input->post('category'));
		$this->db->where('technician',$this->input->post('technician'));
		$this->db->limit(1);
		$this->db->order_by('ticket_nr', 'DESC');
		$result = $this->db->get('services');
		return $result;

	}

}




?> 