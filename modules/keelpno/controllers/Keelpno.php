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


		
		if(!isset($this->session->client)){
			$this->session->client = $this->input->post('client');
		}else{
			if(!isset($this->session->user)){
			$this->session->user = $this->input->post('user');
			
			}

			if(!isset($this->session->type)){
				$this->session->type = $this->input->post('type');

			}

			$this->load->view('menu', $data);
			$data['tickets'] = $this->showTickets()->result();
		}

		



		
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
				if($this->input->post('category')=="UPS")
				{
					$insertData ['daily'] =1;
				}

				

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

			$ticket = $ticket->result()[0];
			$ticket_nr = ($ticket->daily=='0' ? null : $ticket->ticket_nr);

					$_POST['ticket_nr'] = $ticket_nr;
					$_POST['technician'] = $ticket->technician;
					$_POST['category'] = $ticket->category;
					$_POST['tasks_lists'] = json_decode($ticket->tasks_list);
					$_POST['customer_comments'] = $ticket->customer_comments;
					$_POST['technician_comments'] = $ticket->technician_comments;
					$_POST['day'] = date('d',strtotime($ticket->ticket_date));
					$_POST['month'] = date('m',strtotime($ticket->ticket_date));
					$_POST['year'] = date('Y',strtotime($ticket->ticket_date));
					


			
			$this->session->set_userdata('client',$ticket->client);
			$this->session->set_userdata('user' ,$ticket->technician) ;
			$this->session->set_userdata('type' ,$ticket->category) ;
		
		}else{

			$this->load->library('form_validation');
			
				$this->form_validation->set_rules('tasks_lists[]', 'Κατηγορία', 'required',
	                        array('required' => 'Επιλέξτε τουλάχιστον μία %s.')
	                );

				$this->form_validation->set_rules('technician_comments', 'Σχόλια Τεχνικού', 'required|trim',
	                        array('required' => 'Συμπληρώστε τα %s.')
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
					'technician' => $this->input->post('technician'),
					'category' => $this->input->post('category'),
					'tasks_list' => json_encode($this->input->post('tasks_lists')),
					'customer_comments' => $this->input->post('customer_comments'),
					'technician_comments' => $this->input->post('technician_comments'),
					

					);

					$this->db->where('id',$id);
					$this->db->update('services', $insertData);
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

	public function getLastDailyTickets(){

		$categories = array('Πληροφορική', 'Τηλεφωνία', 'VOIP', 'COPIERS', 'UPS');
		//$technicians = array('Άλεξ', 'Γιώργος', 'Τάκης', 'Θανάσης');

		foreach ($categories as $category) {
			
			$this->db->where('daily', 1);
			$this->db->where('client', $this->session->userdata('client'));
			$this->db->where('category', $category);
			$this->db->order_by('ticket_nr','DESC');
			$this->db->limit(1);
			$this->db->where('technician',$this->session->userdata('user'));
			$ticket=$this->db->get('services');
			if($ticket->num_rows()>0){
				$result[$category] = $ticket->result();
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
		$this->db->where('client', $this->input->post('client'));
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
		$defaultTasks = array();
		$defaultComments = '';

		if($this->input->post('technician') == 'Άλεξ' && $this->input->post('client') == 'marousi'){
			
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
			
		}
		else if($this->input->post('technician') == 'Γιώργος' && $this->input->post('client') == 'marousi'){
			$defaultTasks = array();
			$defaultComments = '';
			if($this->input->post('category')=='Πληροφορική'){
				$defaultTasks = array("1","6");
				$defaultComments = "Έλεγχος Domain Controller ΚΕΕΛΠΝΟ. Έλεγχος εφαρμογής PRTG.";
			}
		}
		else if($this->input->post('technician') == 'Θανάσης' && $this->input->post('client') == 'vari'){
			$defaultTasks = array();
			$defaultComments = '';
			if($this->input->post('category')=='Πληροφορική'){
				$defaultTasks = array("1","6","7","22");
				$defaultComments = "Έλεγχος Servers, Firewall, Routers. Έλεγχος λήψης Backup σε Backup_PC & FreeNAS. Έλεγχος εφαρμογής PRTG. Εκτύπωση αναφορών χρήσης δικτύου. ";
			}
		}

		$tasks = array_merge($defaultTasks, $tasks);
		$comments = $defaultComments.' '.$comments;

		$insertData = array(
					'ticket_nr' => $this->input->post('ticket_nr'),
					'ticket_date' => $date,
					'technician' =>  $this->input->post('technician'),
					'category' => $this->input->post('category'),
					'tasks_list' => json_encode($tasks),
					'technician_comments' => $comments,
					'creation_date' => date('Y-m-d H:i:s'),
					'client' => $this->input->post('client'),
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


	private function showTickets(){
		$technician = $this->session->userdata('user');
		$client = $this->session->userdata('client');

		$this->db->where('technician',$technician);
		$this->db->where('client',$client);
		$this->db->order_by('id','DESC');
		$this->db->limit(100);
		return $this->db->get('services');

	}

	public function ttruncat($text,$numb) {
		if (strlen($text) > $numb) { 
		  $text = substr($text, 0, $numb); 
		  $text = substr($text,0,strrpos($text," ")); 
		  $etc = " ...";  
		  $text = $text.$etc; 
		  }
		return $text; 
	}

}
?> 