<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pengajuan extends OPPController {
	public function __construct() {
		parent::__construct();	
		$this->load->helper('fungsi');
		$this->load->model('general_m');
	}	

	public function index() {
		$this->load->model('pinjaman_m');
		$this->data['judul_browser'] = 'Pengajuan Pinjaman';
		$this->data['judul_utama'] = 'Pengajuan';
		$this->data['judul_sub'] = 'Pinjaman';

		//table
		$this->data['css_files'][] = base_url() . 'assets/extra/bootstrap-table/bootstrap-table.min.css';
		$this->data['js_files2'][] = base_url() . 'assets/extra/bootstrap-table/bootstrap-table.min.js';
		$this->data['js_files2'][] = base_url() . 'assets/extra/bootstrap-table/extensions/filter-control/bootstrap-table-filter-control.min.js';
		$this->data['js_files2'][] = base_url() . 'assets/extra/bootstrap-table/bootstrap-table-id-ID.js';

		//modal
		$this->data['css_files'][] = base_url() . 'assets/extra/bootstrap-modal/css/bootstrap-modal-bs3patch.css';
		$this->data['css_files'][] = base_url() . 'assets/extra/bootstrap-modal/css/bootstrap-modal.css';
		$this->data['js_files'][] = base_url() . 'assets/extra/bootstrap-modal/js/bootstrap-modalmanager.js';
		$this->data['js_files'][] = base_url() . 'assets/extra/bootstrap-modal/js/bootstrap-modal.js';
		$this->data['js_files'][] = base_url() . 'assets/extra/bootstrap-modal/js/nsi_modal_default.js';

		// datepicker
		$this->data['css_files'][] = base_url() . 'assets/theme_admin/css/datepicker/datepicker3.css';
		$this->data['js_files'][] = base_url() . 'assets/theme_admin/js/plugins/datepicker/bootstrap-datepicker.js';
		$this->data['js_files'][] = base_url() . 'assets/theme_admin/js/plugins/datepicker/locales/bootstrap-datepicker.id.js';
		//$this->data['barang_id'] = $this->pinjaman_m->get_id_barang();

		//daterange
		$this->data['css_files'][] = base_url() . 'assets/theme_admin/css/daterangepicker/daterangepicker-bs3.css';
		$this->data['js_files'][] = base_url() . 'assets/theme_admin/js/plugins/daterangepicker/daterangepicker.js';

		//select2
		$this->data['css_files'][] = base_url() . 'assets/extra/select2/select2.css';
		$this->data['js_files'][] = base_url() . 'assets/extra/select2/select2.min.js';

		//editable
		$this->data['css_files'][] = base_url() . 'assets/extra/bootstrap3-editable/css/bootstrap-editable.css';
		$this->data['js_files'][] = base_url() . 'assets/extra/bootstrap3-editable/js/bootstrap-editable.min.js';	

		$this->data['kas_id'] = $this->pinjaman_m->get_data_kas();
		$this->data['suku_bunga'] = $this->pinjaman_m->get_data_bunga();
		$this->data['biaya'] = $this->pinjaman_m->get_biaya_adm();
		$this->data['jenis_ags'] = $this->pinjaman_m->get_data_angsuran();

		$this->data['isi'] = $this->load->view('pengajuan_list_v', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);
	}


	public function tambah()
	{
		$this->load->model('pinjaman_m');
		$this->load->model('simpanan_m');
		$this->load->model('general_m');
		$this->data['judul_browser'] = 'Tambah Pengajuan Pinjaman';
		$this->data['judul_utama'] = 'Pengajuan Pinjaman';
		$this->data['judul_sub'] = 'Tambah Pengajuan Pinjaman';

		//select2
		$this->data['css_files'][] = base_url() . 'assets/extra/select2/select2.css';
		$this->data['js_files'][] = base_url() . 'assets/extra/select2/select2.min.js';

		$this->data['js_files'][] = base_url() . 'assets/extra/fungsi/number_format.js';

		$this->data['pinjaman'] = $this->general_m->get_jns_pinjaman_all();
		$this->data['anggota'] = $this->general_m->get_anggota();
		
		$this->data['isi'] = $this->load->view('pengajuan_pinjaman_tambah_v', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);	
	}

	public function proses_tambah()
	{
		$output = array('output'=>false);

		$jenis = $this->input->post('jenis');
		$kode = $this->db->select('kode_pinjaman')->get_where('jns_pinjaman',array('jns_pinjaman'=>$jenis))->row_array();
		$anggota = $this->input->post('anggota');
		$nominal = preg_replace('/\D/', '', $this->input->post('nominal'));
		$lama_ags = $this->input->post('lama_ags');
		$keterangan = $this->input->post('keterangan');
		$type = 1;
		$no_ajuan = $this->general_m->get_ajuan($type);
		$ajuan_id = $this->general_m->get_ajuan_id($no_ajuan,$type,$kode['kode_pinjaman']); 

		$data = array(
			'type' => $type,
			'no_ajuan' => $no_ajuan,
			'ajuan_id'	=> $ajuan_id,
			'anggota_id' => $anggota,
			'tgl_input'	=> date('Y-m-d H:i:s'),
			'jenis' => $jenis,
			'nominal' => $nominal,
			'lama_ags'		=> $lama_ags,
			'keterangan' => $keterangan,
			'tgl_update'	=> date('Y-m-d H:i:s'),
			'status'		=> 0,
			'potongan'		=> preg_replace('/\D/', '', $this->input->post('potongan')),
			'uang_diterima'		=> preg_replace('/\D/', '', $this->input->post('uang_diterima')),

		);
		if($this->db->insert('tbl_pengajuan',$data)){
			$pesan = 'Pengajuan Pinjaman '.$jenis.' telah dibuat';
			$this->notif_m->created_notif(1,$ajuan_id,$pesan,$anggota);
			$this->notif_m->created_notif(2,$ajuan_id,$pesan,$anggota,1);

			$output = array('output'=>true);
		}

		echo json_encode($output);
	}

	public function ajax_pengajuan() {
		$this->load->model('pinjaman_m');
		$out = $this->pinjaman_m->get_pengajuan();
		header('Content-Type: application/json');
		echo json_encode($out);
		exit();
	}

	function aksi() {
		$this->load->model('pinjaman_m');
		if($this->pinjaman_m->pengajuan_aksi()) {
			echo 'OK';
		} else {
			echo 'Gagal';
		}
	}

	function edit() {
		$this->load->model('pinjaman_m');
		$res = $this->pinjaman_m->pengajuan_edit();
		echo $res;
	}

}
