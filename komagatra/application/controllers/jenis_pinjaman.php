<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Jenis_pinjaman extends AdminController {

	public function __construct() {
		parent::__construct();	
	}	
	
	public function index() {
		$this->data['judul_browser'] = 'Setting';
		$this->data['judul_utama'] = 'Setting';
		$this->data['judul_sub'] = 'Jenis Pinjaman';

		$this->output->set_template('gc');

		$this->load->library('grocery_CRUD');
		$crud = new grocery_CRUD();
		$crud->set_table('jns_pinjaman');
		$crud->set_subject('Jenis Angsuran');

		//$crud->columns('ket');
		//$crud->fields('ket');
		$crud->columns('jns_pinjaman','min_pinjaman','max_pinjaman', 'anuitas', 'bunga_anuitas','tampil_urutan','aktif');
		$crud->fields('jns_pinjaman','min_pinjaman','max_pinjaman', 'anuitas', 'bunga_anuitas','tampil_urutan','aktif');
		$crud->display_as('jns_pinjaman','Jenis Pinjaman');
		$crud->display_as('min_pinjaman','Minimum');
		$crud->display_as('max_pinjaman','Maximum');
		$crud->display_as('anuitas','Anuitas');
		$crud->display_as('bunga_anuitas','Bunga Anuitas');
		$crud->display_as('tampil_urutan','Tampil Urutan');
		$crud->display_as('aktif','Aktif');

		$crud->required_fields('jns_pinjaman','min_pinjaman','max_pinjaman','aktif');


		$crud->field_type('aktif','dropdown',
		array('Y'=>'Aktif','N'=>'Tidak Aktif'));

		$crud->field_type('tampil_urutan','dropdown',
		array(1=>'Ya',0=>'Tidak'));

		$crud->field_type('anuitas','dropdown',
		array(1=>'Ya',0=>'Tidak'));

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
