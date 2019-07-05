<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lap_trans_kas_m extends CI_Model {
	public function __construct() {
		parent::__construct();
	}

	//panggil data simpanan
	function get_data_simpanan($limit, $start,$type=false) {		
		if(isset($_REQUEST['tgl_dari']) && isset($_REQUEST['tgl_samp'])) {
			$tgl_dari = $_REQUEST['tgl_dari'];
			$tgl_samp = $_REQUEST['tgl_samp'];
		} else {
			$tgl_dari = date('Y') . '-01-01';
			$tgl_samp = date('Y') . '-12-31';
		}
		$this->db->select('v_transaksi.*');
		$this->db->from('v_transaksi');
		// yusuf
		$this->db->join('tbl_trans_kas','tbl_trans_kas.id = v_transaksi.id','left');

		if(!empty($type)){
			$this->db->join('nama_kas_tbl','nama_kas_tbl.id = v_transaksi.dari_kas OR nama_kas_tbl.id = v_transaksi.untuk_kas','left');
			$this->db->where('nama_kas_tbl.type',$type);
		}
		$this->db->where('DATE(tgl) >= ', ''.$tgl_dari.'');
		$this->db->where('DATE(tgl) <= ', ''.$tgl_samp.'');

		if(isset($_REQUEST['cari_bank'])){
			$this->db->where_in('dari_kas',$_REQUEST['cari_bank']);
			$this->db->or_where_in('untuk_kas',$_REQUEST['cari_bank']);
		}

		// yusuf
		$this->db->order_by('tgl', 'ASC');
		$this->db->limit($limit, $start);
		$query = $this->db->get();
		if($query->num_rows() > 0) {
			$out = $query->result();
			return $out;
		} else {
			return array();
		}
	}


	function get_jml_data_kas() {
		if(isset($_REQUEST['tgl_dari']) && isset($_REQUEST['tgl_samp'])) {
			$tgl_dari = $_REQUEST['tgl_dari'];
			$tgl_samp = $_REQUEST['tgl_samp'];
		} else {
			$tgl_dari = date('Y') . '-01-01';
			$tgl_samp = date('Y') . '-12-31';
		}

		// yusuf
		$this->db->join('tbl_trans_kas','tbl_trans_kas.id = v_transaksi.id','left');
		$this->db->where('DATE(tgl) >= ', ''.$tgl_dari.'');
		$this->db->where('DATE(tgl) <= ', ''.$tgl_samp.'');

		// yusuf
		$this->db->where('status_kas',1);
		return $this->db->count_all_results('v_transaksi');
	}

	function get_saldo_sblm($type=false) {
		// SALDO SEBELUM NYA
		if(isset($_REQUEST['tgl_dari']) && isset($_REQUEST['tgl_samp'])) {
			$tgl_dari = $_REQUEST['tgl_dari'];
		} else {
			$tgl_dari = date('Y') . '-01-01';
		}
		$this->db->select('SUM(debet) AS jum_debet, SUM(kredit) AS jum_kredit');
		$this->db->from('v_transaksi');
		// yusuf
		$this->db->join('tbl_trans_kas','tbl_trans_kas.id = v_transaksi.id','left');
		
		if(!empty($type)){
			$this->db->join('nama_kas_tbl','nama_kas_tbl.id = v_transaksi.dari_kas OR nama_kas_tbl.id = v_transaksi.untuk_kas','left');
			$this->db->where('nama_kas_tbl.type',$type);
		}
		// yusuf
		$this->db->where('status_kas',1);
		$this->db->where('DATE(tgl) < ', ''.$tgl_dari.'');		
		$query_sblm = $this->db->get();
		$saldo_sblm = 0;
		if($query_sblm->num_rows() > 0) {
			$row_sblm = $query_sblm->row();
			$saldo_sblm = ($row_sblm->jum_debet - $row_sblm->jum_kredit);
		}
		return $saldo_sblm;
	}

	function get_saldo_awal($limit, $start,$type=false) {
		$this->db->select('debet, kredit');
		$this->db->from('v_transaksi');
		
		if(isset($_REQUEST['tgl_dari']) && isset($_REQUEST['tgl_samp'])) {
			$tgl_dari = $_REQUEST['tgl_dari'];
			$tgl_samp = $_REQUEST['tgl_samp'];
		} else {
			$tgl_dari = date('Y') . '-01-01';
			$tgl_samp = date('Y') . '-12-31';
		}
		
		// yusuf
		$this->db->join('tbl_trans_kas','tbl_trans_kas.id = v_transaksi.id','left');
		$this->db->where('DATE(tgl) >= ', ''.$tgl_dari.'');
		$this->db->where('DATE(tgl) <= ', ''.$tgl_samp.'');

		if(!empty($type)){
			$this->db->join('nama_kas_tbl','nama_kas_tbl.id = v_transaksi.dari_kas OR nama_kas_tbl.id = v_transaksi.untuk_kas','left');
			$this->db->where('nama_kas_tbl.type',$type);
		}

		// yusuf
		$this->db->where('status_kas',1);

		$this->db->order_by('tgl', 'ASC');
		$this->db->limit($start, 0);
		$query = $this->db->get();
		if($query->num_rows() > 0) {
			$res = $query->result();
			$saldo = 0;
			foreach ($res as $row) {
				$saldo += ($row->debet - $row->kredit);
			}
			return $saldo;
		} else {
			return 0;
		}		
	}

//panggil nama kas
	function get_nama_kas_id($id) {
		$this->db->select('*');
		$this->db->from('nama_kas_tbl');
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

	//panggil transaksi kas  untuk laporan
	function lap_trans_kas($type=false) {

		if(isset($_REQUEST['tgl_dari']) && isset($_REQUEST['tgl_samp'])) {
			$tgl_dari = $_REQUEST['tgl_dari'];
			$tgl_samp = $_REQUEST['tgl_samp'];
		} else {
			$tgl_dari = date('Y') . '-01-01';
			$tgl_samp = date('Y') . '-12-31';
		}

		$this->db->select('*');
		$this->db->from('v_transaksi');
		// yusuf
		$this->db->join('tbl_trans_kas','tbl_trans_kas.id = v_transaksi.id','left');
		$this->db->where('DATE(tgl) >= ', ''.$tgl_dari.'');
		$this->db->where('DATE(tgl) <= ', ''.$tgl_samp.'');

		if(!empty($type)){
			$this->db->join('nama_kas_tbl','nama_kas_tbl.id = v_transaksi.dari_kas OR nama_kas_tbl.id = v_transaksi.untuk_kas','left');
			$this->db->where('nama_kas_tbl.type',$type);
		}
		// yusuf
		$this->db->where('status_kas',1);
		$this->db->order_by('tgl', 'ASC');

		$query = $this->db->get();

		if($query->num_rows()>0){
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