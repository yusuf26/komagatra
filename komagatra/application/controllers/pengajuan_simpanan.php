<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pengajuan_simpanan extends OPPController {
	public function __construct() {
		parent::__construct();	
		$this->load->helper('fungsi');
		$this->load->model('general_m');
		$this->load->model('notif_m');
		
	}	

	public function index() {
		$this->load->model('pinjaman_m');
		$this->load->model('simpanan_m');
		$this->data['judul_browser'] = 'Pengajuan Simpanan';
		$this->data['judul_utama'] = 'Pengajuan';
		$this->data['judul_sub'] = 'Simpanan';

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

		$this->data['kas_id'] = $this->simpanan_m->get_data_kas();
		
		$this->data['jenis_ags'] = $this->pinjaman_m->get_data_angsuran();

		$this->data['isi'] = $this->load->view('pengajuan_simpanan_list_v', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);
	}

	public function tambah()
	{
		$this->load->model('pinjaman_m');
		$this->load->model('simpanan_m');
		$this->load->model('general_m');
		$this->data['judul_browser'] = 'Tambah Pengajuan Simpanan';
		$this->data['judul_utama'] = 'Pengajuan Simpanan';
		$this->data['judul_sub'] = 'Tambah Pengajuan Simpanan';


		//select2
		$this->data['css_files'][] = base_url() . 'assets/extra/select2/select2.css';
		$this->data['js_files'][] = base_url() . 'assets/extra/select2/select2.min.js';

		$this->data['js_files'][] = base_url() . 'assets/extra/fungsi/number_format.js';

		$this->data['simpanan'] = $this->general_m->get_jns_simpanan_all();
		$this->data['anggota'] = $this->general_m->get_anggota();
		
		$this->data['isi'] = $this->load->view('pengajuan_simpanan_tambah_v', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);	
	}

	public function proses_tambah()
	{
		$output = array('output'=>false);

		$jenis_simpanan = $this->input->post('jenis_simpanan');
		$kode_simpanan = $this->db->select('jns_simpan,kode_simpan')->get_where('jns_simpan',array('id'=>$jenis_simpanan))->row_array();
		$anggota = $this->input->post('anggota');
		$nominal = preg_replace('/\D/', '', $this->input->post('nominal'));
		$keterangan = $this->input->post('keterangan');
		$type = 2;
		$no_ajuan = $this->general_m->get_ajuan($type);
		$ajuan_id = $this->general_m->get_ajuan_id($no_ajuan,$type,$kode_simpanan['kode_simpan']); 

		$data = array(
			'type' => $type,
			'no_ajuan' => $no_ajuan,
			'ajuan_id'	=> $ajuan_id,
			'anggota_id' => $anggota,
			'tgl_input'	=> date('Y-m-d H:i:s'),
			'jenis' => $kode_simpanan['jns_simpan'],
			'nominal' => $nominal,
			'keterangan' => $keterangan,
			'status' => 0,
		);
		if($this->db->insert('tbl_pengajuan',$data)){
			$pesan = 'Pengajuan '.$kode_simpanan['jns_simpan'].' telah dibuat';
			$this->notif_m->created_notif(1,$ajuan_id,$pesan,$anggota);
			$this->notif_m->created_notif(2,$ajuan_id,$pesan,$anggota,2);

			$output = array('output'=>true);
		}

		echo json_encode($output);
	}

	public function ajax_pengajuan() {
		$this->load->model('pinjaman_m');
		$out = $this->pinjaman_m->get_pengajuan_simpanan();
		header('Content-Type: application/json');
		echo json_encode($out);
		exit();
	}

	function aksi() {

		$out = array('output'=>false);
		$this->load->model('pengajuan_m');
		if($this->pengajuan_m->pengajuan_simpanan_aksi()) {
		
			$out = array('output'=>true);
		} else {
			$out = array('output'=>false);
		}
		echo json_encode($out);
	}

	function get_ags() {
		
		$id = $this->input->post('id');
		$out = array('output'=>false);

		$get_pengajuan = $this->db->get_where('tbl_pengajuan',array('id'=>$id))->row();

		$get_ags = $this->db->order_by('angsuran_ke','desc')->get_where('tbl_trans_sp',array('ajuan_id'=>$get_pengajuan->ajuan_id))->row();

		if($get_pengajuan->lama_ags == $get_ags->angsuran_ke){
			$out = array('output'=>true,'angsuran_ke'=>'done');
		}else {
			$out = array('output'=>true,'angsuran_ke'=>$get_ags->angsuran_ke + 1,'jumlah'=>$get_ags->jumlah);	
		}
		

		header('Content-Type: application/json');
		echo json_encode($out);
		// exit();
		
	}

	function edit() {
		$this->load->model('pinjaman_m');
		$res = $this->pinjaman_m->pengajuan_edit();
		echo $res;
	}

}
