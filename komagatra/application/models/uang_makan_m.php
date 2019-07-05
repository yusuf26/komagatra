<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Uang_makan_m extends CI_Model {

	public function __construct() {
		parent::__construct();
		$this->load->model('general_m');
		$this->load->model('notif_m');
	}

	function get_uang_makan() {
		$this->load->helper('fungsi');
		//$user_id = $this->session->userdata('u_name');

		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$limit = isset($_POST['limit']) ? $_POST['limit'] : 10;
		$search = isset($_POST['search']) ? $_POST['search'] : '';
		
		$fr_bulan = isset($_POST['fr_bulan']) ? $_POST['fr_bulan'] : '';
		
		//$where = " AND anggota_id = " . $user_id;
		$where = "";
		if($fr_bulan != '') {
			$arr_bln = explode('-', $fr_bulan);
			$bln_dari =  $arr_bln[1].'-'.$arr_bln[0].'-00';
			$bln_samp =  date('Y-m-t');
			$ex_samp = explode('-', $bln_samp);
			$bln_samp = $arr_bln[1].'-'.$arr_bln[0].'-'.$ex_samp[2];
			$where .=" AND DATE(bulan) >= '".$bln_dari."' ";
			$where .=" AND DATE(bulan) <= '".$bln_samp."' ";			
		} 
		$order_by = " ORDER BY bulan DESC";
		if ( isset($_POST['sort']) && isset($_POST['order']) ) {
			$order_by = " ORDER BY ".$_POST['sort']." ".$_POST['order']." ";
		}
		$sql_limit = " LIMIT ".$offset.",".$limit." ";
		
		$sql_tampil = "SELECT 
			a.*, b.nama AS nama, b.departement AS departement
			FROM tbl_uang_makan AS a
			LEFT JOIN tbl_anggota AS b ON b.id = a.anggota_id
		 	WHERE a.status = 1 ".$where." ".$order_by." ".$sql_limit."";
		$query = $this->db->query($sql_tampil);
		$data_list = $query->result();

		$sql_total = "SELECT id FROM tbl_uang_makan AS a WHERE a.status = 1 ".$where." ";
		$query = $this->db->query($sql_total);
		$total = $query->num_rows();

		$data_list_i = array();
		$no=1;
		foreach ($data_list as $key => $val) {
			$tgl_arr = explode(' ', $val->bulan);
			$tgl = $tgl_arr[0];
			$val->bulan_txt = jin_date_ina($tgl,'full',false,true);
			$val->bulan = substr($val->bulan, 0, 16);
			$val->jumlah = number_format($val->jumlah,0,'.','.');
			$val->no = $no;
			$val->nama = $val->nama.' ('.$val->departement.')';
			$data_list_i[$key] = $val;
			$no++;
		}

		$out = array('rows' => $data_list_i, 'total' => $total);
		return $out;
	}

	function uang_makan_aksi() {
		$status = $this->input->post('aksi');
		$id = $this->input->post('id');
		$alasan = $this->input->post('alasan');
		$status_txt = 0;
		switch ($status) {
			case 'Hapus':
				return $this->db->delete('tbl_uang_makan', array('id' => $id));
			break;
			default:
				return FALSE;
			break;
		}

		return true;

	}


	function get_row($bulan='')
	{
		$output = false;

		$this->db->select('tbl_uang_makan.*,b.nama AS nama, b.departement AS departement');
		$this->db->join('tbl_anggota b','tbl_uang_makan.anggota_id = b.id','left');
		$this->db->where('tbl_uang_makan.status',1);
		if(!empty($bulan)){
			$arr_bln = explode('-', $bulan);
			$bln_dari =  $arr_bln[1].'-'.$arr_bln[0].'-00';
			$bln_samp =  date('Y-m-t');
			$ex_samp = explode('-', $bln_samp);
			$bln_samp = $arr_bln[1].'-'.$arr_bln[0].'-'.$ex_samp[2];
			$this->db->where('DATE(bulan) >=',$bln_dari);
			$this->db->where('DATE(bulan) <=',$bln_samp);
		}
		$query = $this->db->get('tbl_uang_makan');
		if($query->num_rows() > 0){
			$output	= $query->result_array();
		}

		return $output;
	}
}