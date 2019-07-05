<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lap_buku_besar_m extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

	//panggil data jenis kas untuk laporan
	function get_cari_akun() {
		$this->db->select('jns_trans,id,kd_aktiva');
		$this->db->from('jns_akun');
		$this->db->where('aktif','Y');
		$this->db->order_by('kd_aktiva','asc');
		$query = $this->db->get();
		if($query->num_rows()>0){
			$out = $query->result();
			return $out;
		} else {
			return array();
		}
	}

	function get_nama_kas() {
		$this->db->select('*');
		$this->db->from('nama_kas_tbl');
		$this->db->where('aktif','Y');
		$query = $this->db->get();
		if($query->num_rows()>0){
			$out = $query->result();
			return $out;
		} else {
			return array();
		}
	}

	//panggil data jenis kas untuk laporan
	function get_transaksi_kas($kas_id) {
		$this->db->select('*');
		$this->db->from('v_transaksi');
		
		// yusuf
		$this->db->join('tbl_trans_kas','tbl_trans_kas.id = v_transaksi.id','left');

		if(isset($_REQUEST['cari_akun'])){
			$this->db->where_in('transaksi',$_REQUEST['cari_akun']);
		}
		// $date = new DateTime("now");
		if(isset($_REQUEST['tgl_dari']) && isset($_REQUEST['tgl_samp'])){
			$this->db->where('DATE(tgl) >=',$_REQUEST['tgl_dari']);
			$this->db->where('DATE(tgl) <=',$_REQUEST['tgl_samp']);
		}
		// yusuf
		$this->db->where('status_kas',1);
		// $this->db->where($where);
		$this->db->order_by('tgl', 'ASC');
		$query = $this->db->get();

		if($query->num_rows()>0) {
			$out = $query->result();
			return $out;
		} else {
			return array();
		}
	}


	function get_nama_akun_id($id) {
		$this->db->select('*');
		$this->db->from('jns_akun');
		$this->db->where('id', $id);
		$query = $this->db->get();

		if($query->num_rows() > 0) {
			$out = $query->row();
			return $out;
		} else {
			$out = (object) array('nama' => '');
			return $out;
		}
	}	
}