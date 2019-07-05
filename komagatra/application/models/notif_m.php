<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Notif_m extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

	// tempo
	function get_data_tempo() {
		$this->db->select('v_hitung_pinjaman.tempo AS tempo, v_hitung_pinjaman.tagihan AS tagihan, tbl_anggota.nama AS nama, SUM(tbl_pinjaman_d.jumlah_bayar) AS jum_bayar, SUM(tbl_pinjaman_d.denda_rp) AS jum_denda');
		$this->db->from('v_hitung_pinjaman');
		$this->db->where('lunas','Belum');
		
		$where = " DATE(tempo) < (CURDATE() + INTERVAL 14 DAY) ";
		$this->db->where($where, false, false);
		$this->db->join('tbl_anggota', 'tbl_anggota.id = v_hitung_pinjaman.anggota_id', 'LEFT');
		$this->db->join('tbl_pinjaman_d', 'tbl_pinjaman_d.pinjam_id = v_hitung_pinjaman.id', 'LEFT');
		$this->db->order_by('v_hitung_pinjaman.tempo', 'ASC');
		$this->db->group_by('v_hitung_pinjaman.id');
		$query = $this->db->get();
		if($query->num_rows() > 0) {
			$out = $query->result();
			return $out;
		} else {
			return array();
		}
	}

	// pengajuan pinjaman
	function get_pengajuan() {
		if($this->session->userdata('level') == 'pinjaman' || $this->session->userdata('level') == 'admin') {
			$this->db->where('status', 0);
		} else {
			$this->db->where('status', 1);
		}
		$this->db->from('tbl_pengajuan');
		$query = $this->db->get();
		if($query->num_rows() > 0) {
			$out = $query->result();
			return $out;
		} else {
			return array();
		}		
	}

	function created_notif($type,$ajuan_id,$pesan,$anggota_id,$jenis=false)
	{

		$data = array(
			'type' => $type,
			'ajuan_id' => $ajuan_id,
			'pesan' =>$pesan,
			'anggota_id' => $anggota_id,	
			'baca' => 0,
			'created_at' => date('Y-m-d H:i:s'),
			'jenis_ajuan' => $jenis
		);
		return $this->db->insert('tbl_notif',$data);
	}


	function update_baca($id,$admin_id)
	{

		$this->db->where('notif_id',$id);
		return $this->db->update('tbl_notif',array('baca'=>1,'baca_by'=>$admin_id,'baca_at'=>date('Y-m-d H:i:s')));
	}


	function get_data_notif($offset, $limit, $q='', $sort, $order) {
		$sql = "SELECT * FROM tbl_notif WHERE type=1 ";
		if(is_array($q)) {
			if($q['tgl_dari'] != '' && $q['tgl_sampai'] != '') {
				$sql .=" AND DATE(created_at) >= '".$q['tgl_dari']."' ";
				$sql .=" AND DATE(created_at) <= '".$q['tgl_sampai']."' ";
			}
		}
		$result['count'] = $this->db->query($sql)->num_rows();
		$sql .=" ORDER BY {$sort} {$order} ";
		$sql .=" LIMIT {$offset},{$limit} ";
		$result['data'] = $this->db->query($sql)->result();
		return $result;
	}

}