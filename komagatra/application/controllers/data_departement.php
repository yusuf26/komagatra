<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Data_departement extends OperatorController {

	public function __construct() {
		parent::__construct();	
	}	
	
	public function index() {
		$this->data['judul_browser'] = 'Setting';
		$this->data['judul_utama'] = 'Setting';
		$this->data['judul_sub'] = 'Data Direktorat';

		$this->output->set_template('gc');

		$this->load->library('grocery_CRUD');
		$crud = new grocery_CRUD();
		$crud->set_table('departement');
		$crud->set_subject('Data Direktorat');
	
		$crud->fields('departement');

		// $crud->display_as('id_departement','Nama Barang');
		$crud->display_as('departement','Direktorat');
		
		$crud->required_fields('departement');
		
		$crud->unset_read();
		$output = $crud->render();

		$out['output'] = $this->data['judul_browser'];
		$this->load->section('judul_browser', 'default_v', $out);
		$out['output'] = $this->data['judul_utama'];
		$this->load->section('judul_utama', 'default_v', $out);
		$out['output'] = $this->data['judul_sub'];
		$this->load->section('judul_sub', 'default_v', $out);
		$out['output'] = $this->data['u_name'];
		$this->load->section('u_name', 'default_v', $out);

		$this->load->view('default_v', $output);

	}

}
