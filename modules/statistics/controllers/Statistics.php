<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Statistics extends MX_Controller {

	public function __construct()
    {
        parent::__construct();
        $this->load->model('Statistics_model');
    }

    public function index($supplier=null)
	{	
        if($supplier != null)
        {
            $data['Supplier_products'] = $this->Statistics_model->getSupplierProducts($supplier);
            $data['Supplier']= strtoupper($supplier);
        }
        $data['title'] = 'ΠΡΟΜΗΘΕΥΤΕΣ';

		$this->load->view('templates/header',$data);
		$this->load->view('statistics', $data);
		$this->load->view('templates/footer',$data);
		
	}


}