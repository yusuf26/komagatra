<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pengeluaran extends OperatorController {

	public function __construct() {
		parent::__construct();	
		$this->load->helper('fungsi');
		$this->load->model('pemasukan_bank_m');
		$this->load->model('pemasukan_m');
		$this->load->model('pinjaman_m');
		$this->load->model('pengeluaran_m');
		$this->load->model('general_m');
		$this->load->library('general');
	}	

	public function index()
	{
		$this->data['judul_browser'] = 'Transaksi Pengeluaran';
		$this->data['judul_utama'] = 'Transaksi Pengeluaran';
		$this->data['judul_sub'] = 'Pengeluaran';


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

		$this->data['isi'] = $this->load->view('pengeluaran_form', $this->data, TRUE);
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
		$ajuan_id = $this->input->post('ajuan_id');

		if($type_penyetor == 1){
			$penyetor = $penyetor_anggota;
		}else {
			$penyetor = $penyetor_lain;
		}

		
		if($jenis == 4){
			$tbl = 'D';
		}else if($jenis == 1){
			$tbl = 'A';
		}else {
			$tbl = 'C';
		}

		$kd_transaksi = $this->general_m->get_kode_transaksi($tbl);
		
		$this->db->trans_start();

		if($jenis == 4){
			$this->general_m->PostLainLain($total_akun,$tgl,$kd_transaksi,$penyetor,$keterangan,$kas_id,$this->data['u_name']);
		}else if($jenis == 1){
			$this->pinjaman_m->PostPinjaman($total_akun,$tgl,$kd_transaksi,$ajuan_id,$keterangan,$kas_id,$this->data['u_name']);
		}else {
			$this->pemasukan_m->PostSimpanan($tgl,$kd_transaksi,$ajuan_id,$kas_id,$this->data['u_name'],$keterangan,true);
		}
		
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE)
		{
			$output	= array('output'=>false);
		}else {

			$data_trans = $this->pemasukan_m->getTransAfterSubmit($kd_transaksi); 
			$output	= array('output'=>true,'data'=>$data_trans,'tgl'=>$this->general->tgl_indo($tgl),'kd_transaksi'=>$kd_transaksi,'tbl'=>$tbl);
		}

		echo json_encode($output);

	}


	function ajax_list()
	{	
		$this->load->model('table_pengeluaran');
		
		$list = $this->table_pengeluaran->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
            $no++;
            $row = array();
            $row[] = $field['tbl'];
            $row[] = $field['id'];
            $row[] = $this->general->tgl_indo($field['tgl']);
            $row[] = $field['kd_transaksi'];
            $jenis_kas = $this->general_m->get_jenis_kas_name($field['dari_kas']);
            $row[] = $jenis_kas['nama'];
            $jenis_trans = $this->db->select('jns_trans')->get_where('jns_akun',array('id'=>$field['transaksi']))->row_array();
            $row[] = $jenis_trans['jns_trans'];
            $row[] = $this->general_m->get_anggota_by_trans($field['tbl'],$field['id']);
            $row[] = number_format($field['kredit'],0,'','.');;
            $row[] = $field['ket'];
            $row[] = cetakStatus($field['status']);
 			$row[] = $field['status'];
            $data[] = $row;
        }
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->table_pengeluaran->count_all(),
            "recordsFiltered" => $this->table_pengeluaran->count_filtered(),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
	}


	public function table_pinjaman()
	{	
		$jenis = $this->input->post('jenis');
		$this->db->select('ajuan_id,tgl_input,anggota_id,jenis,nominal,lama_ags,keterangan');
		if($jenis == 2){
			$this->db->where('type',3);
		}else {
			$this->db->where('type',1);
		}
		$this->db->where('status',1);
		$this->db->order_by('tgl_input','desc');
		$query = $this->db->get('tbl_pengajuan');
		$list = $query->result_array();
        $data = array();
        foreach ($list as $field) {
            $row = array();
            $row[] = $field['ajuan_id'];
            $row[] = $this->general->tgl_indo($field['tgl_input']);
            $row[] = $this->general_m->get_data_anggota($field['anggota_id'])->nama;
            $row[] = $field['jenis'];
            $row[] = number_format($field['nominal'],0,'','.');
            // if($jenis == 1){
            	$row[] = $field['lama_ags'];
            // }
            	$row[] = $field['keterangan'];
        	if($jenis == 2){
        		$row[] = $this->db->select('id')->get_where('jns_akun',array('jns_trans'=>$field['jenis']))->row_array()['id'];	
            }else {
            	$row[] = 7;
            }

            
            
            $data[] = $row;
        }
        $output = array(
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
	}


	public function aksi()
	{
		$output = array('output'=>false);
		$val = $this->input->post('val');
		$data = $this->input->post('data');

		// TRANSACTIONAL DB START
		$this->db->trans_start();

		foreach ($data as $key => $row) {

			if($row[10] == 0){
				$data = array('status'=>$val);
				$where = array('id'=>$row[1]);
				if($row[0] == 'A'){
					$this->db->update('tbl_pinjaman_h',$data,$where);
				}else if($row[0] == 'C'){
					$this->db->update('tbl_trans_sp',$data,$where);
				}else {
					$this->db->update('tbl_trans_kas',$data,$where);
				}
			}else {
				$output = array('output'=>'gagal');
				echo json_encode($output);
				exit;
			}
			
			
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$output = array('output'=>false);
		} else {
			$this->db->trans_complete();
			$output = array('output'=>true);
		}
		
		echo json_encode($output);


	}
























}