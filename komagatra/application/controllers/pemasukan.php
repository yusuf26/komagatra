<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pemasukan extends OperatorController {

	public function __construct() {
		parent::__construct();	
		$this->load->helper('fungsi');
		$this->load->model('pemasukan_bank_m');
		$this->load->model('pemasukan_m');
		$this->load->model('pengeluaran_m');
		$this->load->model('general_m');
		$this->load->library('general');
	}	

	public function index()
	{
		$this->data['judul_browser'] = 'Transaksi Pemasukan';
		$this->data['judul_utama'] = 'Transaksi Pemasukan';
		$this->data['judul_sub'] = 'Pemasukan';


		#include datatables
		$this->data['css_files'][] = base_url() . 'assets/DataTables/DataTables-1.10.18/css/jquery.dataTables.min.css';
		$this->data['js_files'][] = base_url() . 'assets/DataTables/DataTables-1.10.18/js/jquery.dataTables.min.js';
		$this->data['css_files'][] = base_url() . 'assets/DataTables/Select-1.3.0/css/select.dataTables.min.css';
		$this->data['js_files'][] = base_url() . 'assets/DataTables/Select-1.3.0/js/dataTables.select.min.js';

		#include tanggal
		$this->data['css_files'][] = base_url() . 'assets/extra/bootstrap_date_time/css/bootstrap-datetimepicker.min.css';
		$this->data['js_files'][] = base_url() . 'assets/extra/bootstrap_date_time/js/bootstrap-datetimepicker.min.js';
		$this->data['js_files'][] = base_url() . 'assets/extra/bootstrap_date_time/js/locales/bootstrap-datetimepicker.id.js';

		#include daterange
		$this->data['css_files'][] = base_url() . 'assets/theme_admin/css/daterangepicker/daterangepicker-bs3.css';
		$this->data['js_files'][] = base_url() . 'assets/theme_admin/js/plugins/daterangepicker/daterangepicker.js';

		//number_format
		$this->data['js_files'][] = base_url() . 'assets/extra/fungsi/number_format.js';

		// Bootstrap Select
		$this->data['css_files'][] = base_url() . 'assets/extra/select2/select2.css';
		$this->data['js_files'][] = base_url() . 'assets/extra/select2/select2.min.js';

		$this->data['js_files'][] = base_url() . 'assets/extra/bootbox/bootbox.min.js';


		$this->data['kas_id'] = $this->pemasukan_m->get_all_kas();
		$this->data['akun_id'] = $this->pemasukan_m->get_data_akun();
		$this->data['angsuran'] = $this->pemasukan_m->get_angsuran();
		$this->data['anggota_list'] = $this->general_m->anggota_list();
		$this->data['anggota_bendahara'] = $this->db->select('nama')->where_in('jabatan_id',array(3,4))->get('tbl_anggota')->result_array();
		$this->data['anggota_ketua'] = $this->db->select('nama')->where('jabatan_id',1)->get('tbl_anggota')->result_array();

		$this->data['isi'] = $this->load->view('pemasukan_bank_form', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);
	}

	public function submit_form()
	{
		$output	= array('output'=>false);
		$jenis = $this->input->post('jenis');
		$kas_id = $this->input->post('kas_id');
		$tgl = date('Y-m-d',strtotime($this->input->post('tgl')));
		$type_penyetor = $this->input->post('type_penyetor');
		$penyetor_lain = $this->input->post('penyetor_lain');
		$penyetor_anggota = $this->input->post('penyetor_anggota');
		$keterangan = $this->input->post('keterangan');
		$total_akun = $this->input->post('total_akun');
		$pinjaman_id = $this->input->post('pinjaman_id');
		$pengajuan_id = $this->input->post('pengajuan_id');
		
		

		if($type_penyetor == 1){
			$penyetor = $penyetor_anggota;
		}else {
			$penyetor = $penyetor_lain;
		}

		if($jenis == 4){
			$tbl = 'D';
		}else if($jenis == 1){
			$tbl = 'B';
		}else if($jenis == 2){
			$tbl = 'C';
		}else {
			$tbl = 'A';
		}

		$kd_transaksi = $this->general_m->get_kode_transaksi($tbl);
		
		$this->db->trans_start();

		if($jenis == 4){
			$this->general_m->PostLainLain($total_akun,$tgl,$kd_transaksi,$penyetor,$keterangan,$kas_id,$this->data['u_name']);
		}else if($jenis == 1){
			$this->pemasukan_m->PostAngsuran($tgl,$kd_transaksi,$pinjaman_id,$kas_id,$this->data['u_name'],$keterangan);
		}else if($jenis == 3){
			$this->pemasukan_m->PostAngsuran($tgl,$kd_transaksi,$pinjaman_id,$kas_id,$this->data['u_name'],$keterangan,true);
		}else if($jenis == 2){
			$this->pemasukan_m->PostSimpanan($tgl,$kd_transaksi,$pengajuan_id,$kas_id,$this->data['u_name'],$keterangan);
		}

		
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE)
		{
		        // generate an error... or use the log_message() function to log your error

			$output	= array('output'=>false);
		}else {

			$data_trans = $this->pemasukan_m->getTransAfterSubmit($kd_transaksi); 
			$output	= array('output'=>true,'data'=>$data_trans,'tgl'=>$this->general->tgl_indo($tgl),'kd_transaksi'=>$kd_transaksi,'tbl'=>$tbl);
		}

		echo json_encode($output);

	}


	function ajax_list()
	{	
		$this->load->model('table_pemasukan');
		
		$list = $this->table_pemasukan->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
            $no++;
            $row = array();
            $row[] = $field['tbl'];
            $row[] = $field['id'];
            $row[] = $this->general->tgl_indo($field['tgl']);
            $row[] = $field['kd_transaksi'];
            $jenis_kas = $this->db->select('nama')->get_where('nama_kas_tbl',array('id'=>$field['untuk_kas']))->row_array();
            $row[] = $jenis_kas['nama'];
            $jenis_trans = $this->db->select('jns_trans')->get_where('jns_akun',array('id'=>$field['transaksi']))->row_array();
            $row[] = $jenis_trans['jns_trans'];
            $row[] = $this->general_m->get_anggota_by_trans($field['tbl'],$field['id']);
            $row[] = number_format($field['debet'],0,'','.');;
            $row[] = $field['ket'];
 
            $data[] = $row;
        }
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->table_pemasukan->count_all(),
            "recordsFiltered" => $this->table_pemasukan->count_filtered(),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
	}


	public function table_angsuran()
	{	

		$jenis = $this->input->post('jenis');
		$this->load->library('finance');
		$this->db->select('v.*,p.jenis as jenis_pinjaman');
		$this->db->join('tbl_pengajuan p','v.ajuan_id = p.ajuan_id','left');
		$this->db->where('v.status',1);
		$this->db->where('v.lunas','Belum');
		$this->db->order_by('v.id','desc');
		$query = $this->db->get('v_hitung_pinjaman v');
		$list = $query->result_array();
        $data = array();
        foreach ($list as $field) {
            $row = array();
            $row[] = $field['id'];
 			$row[] = 'PJ' . sprintf('%05d', $field['id']) . '';
            $row[] = $this->general_m->get_data_anggota($field['anggota_id'])->nama;
            $row[] = $field['jenis_pinjaman'];
            $row[] = number_format($field['jumlah'],0,'','.');
            $row[] = $field['lama_angsuran'];
            $row[] = $field['bln_sudah_angsur'] + 1;
            $ags_pokok = $field['pokok_angsuran'];
			$bunga = $field['bunga_pinjaman'];
			$angsuran = $field['ags_per_bulan'];

			$jenis_pinjaman = $this->db->get_where('jns_pinjaman',array('jns_pinjaman'=>$field['jenis_pinjaman']))->row_array();
			if($jenis_pinjaman['anuitas'] == 1){
				if($jenis == 3){

					$get_lunas = $this->pemasukan_m->angsur_lunas($field['id'],$field['jumlah'],$field['lama_angsuran']);
					$ags_pokok = $get_lunas['angsuran'];
					$bunga = $this->finance->ipmt(0.10/12, $get_lunas['angsuran_ke'] + 1, $field['lama_angsuran'], - $field['jumlah'], 0, false);
					$angsuran = $ags_pokok + $bunga;

				}else {
					$ags_pokok = $this->finance->ppmt(0.10/12, $field['bln_sudah_angsur'] + 1, $field['lama_angsuran'], - $field['jumlah'], 0, false);
					$bunga = $this->finance->ipmt(0.10/12, $field['bln_sudah_angsur']+ 1, $field['lama_angsuran'], - $field['jumlah'], 0, false);
					$angsuran = $ags_pokok + $bunga;	
				}
				
			}
            $row[] = number_format($ags_pokok,0,'','.');
            $row[] = number_format($bunga,0,'','.');
            $row[] = number_format($angsuran,0,'','.');
           
            $data[] = $row;
        }
        $output = array(
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
	}


	public function table_pengajuan()
	{	

		$this->db->select('*');
		$this->db->where('status',1);
		$this->db->order_by('tgl_input','desc');
		$query = $this->db->get('tbl_pengajuan');
		$list = $query->result_array();
        $data = array();
        foreach ($list as $field) {
            $row = array();
            $row[] = $this->db->select('id')->get_where('jns_akun',array('jns_trans'=>$field['jenis']))->row_array()['id'];
 			$row[] = $field['ajuan_id'];
 			$row[] = $this->general->tgl_indo($field['tgl_input']);
            $row[] = $this->general_m->get_data_anggota($field['anggota_id'])->nama;
            $row[] = $field['jenis'];
            $row[] = number_format($field['nominal'],0,'','.');
            $row[] = $field['keterangan'];
           
            $data[] = $row;
        }
        $output = array(
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
	}




















}