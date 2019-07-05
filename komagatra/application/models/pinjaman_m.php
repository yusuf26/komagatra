<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pinjaman_m extends CI_Model {

	public function __construct() {
		parent::__construct();
		$this->load->model('general_m');
		$this->load->model('notif_m');
	}


	function get_pengajuan() {
		$this->load->helper('fungsi');
		//$user_id = $this->session->userdata('u_name');

		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$limit = isset($_POST['limit']) ? $_POST['limit'] : 10;
		$search = isset($_POST['search']) ? $_POST['search'] : '';
		
		$fr_jenis = isset($_POST['fr_jenis']) ? $_POST['fr_jenis'] : array();
		$fr_status = isset($_POST['fr_status']) ? $_POST['fr_status'] : array();
		$fr_bulan = isset($_POST['fr_bulan']) ? $_POST['fr_bulan'] : '';
		$tgl_dari = isset($_POST['tgl_dari']) ? $_POST['tgl_dari'] : '';
		$tgl_sampai = isset($_POST['tgl_sampai']) ? $_POST['tgl_sampai'] : '';
		
		//$where = " AND anggota_id = " . $user_id;
		$where = "";
		if($fr_bulan != '') {
			$bln_dari = date("Y-m-d", strtotime($fr_bulan . "-01 -1 month"));
			$bln_dari = substr($bln_dari, 0, 7) . '-21';
			$bln_samp = $fr_bulan . '-20';
			$where .=" AND DATE(tgl_input) >= '".$bln_dari."' ";
			$where .=" AND DATE(tgl_input) <= '".$bln_samp."' ";			
		} else {
			if($tgl_dari != '' && $tgl_sampai != '') {
				$where .=" AND DATE(tgl_input) >= '".$tgl_dari."' ";
				$where .=" AND DATE(tgl_input) <= '".$tgl_sampai."' ";
			}
		}

		if($this->session->userdata('level') == 'operator') {
			$where .= " AND (a.status = '1' OR a.status = '3') ";
		}

		//
		if (! empty($fr_jenis) ) {
			$where .= " AND (";
			$no = 1;
			foreach ($fr_jenis as $fr) {
				if($no > 1) {
					$where .= " OR ";
				}
				$where .= " a.jenis = '".$fr."' ";
				$no++;
			}
			$where .= ") ";
		}

		//
		if (! empty($fr_status) ) {
			$where .= " AND (";
			$no = 1;
			foreach ($fr_status as $fr) {
				if($no > 1) {
					$where .= " OR ";
				}
				$where .= " a.status = '".$fr."' ";
				$no++;
			}
			$where .= ") ";
		}

		//$order_by = " ORDER BY tgl_input DESC";
		if ( isset($_POST['sort']) && isset($_POST['order']) ) {
			$order_by = " ORDER BY ".$_POST['sort']." ".$_POST['order']." ";
		}
		$sql_limit = " LIMIT ".$offset.",".$limit." ";
		
		$sql_tampil = "SELECT 
			a.id AS id, a.no_ajuan AS no_ajuan, a.ajuan_id AS ajuan_id, a.anggota_id AS anggota_id, a.tgl_input AS tgl_input, a.jenis AS jenis, a.nominal AS nominal, a.lama_ags AS lama_ags, a.keterangan AS keterangan, a.status AS status, a.alasan AS alasan, a.tgl_update AS tgl_update, a.tgl_cair AS tgl_cair,
			b.identitas AS identitas, b.nama AS nama, b.departement AS departement
			FROM tbl_pengajuan AS a
			LEFT JOIN tbl_anggota AS b ON b.id = a.anggota_id
		 	WHERE 1=1 AND type = 1 ".$where." ".$order_by." ".$sql_limit."";
		$query = $this->db->query($sql_tampil);
		$data_list = $query->result();

		$sql_total = "SELECT id FROM tbl_pengajuan AS a WHERE 1=1 ".$where." ";
		$query = $this->db->query($sql_total);
		$total = $query->num_rows();

		// 
		$data_list_i = array();
		foreach ($data_list as $key => $val) {
			$tgl_arr = explode(' ', $val->tgl_input);
			$tgl = $tgl_arr[0];
			$val->tgl_input_txt = jin_date_ina($tgl);
			$val->tgl_update_txt = jin_date_ina($tgl);
			$val->tgl_cair_txt = jin_date_ina($val->tgl_cair);
			$val->tgl_input = substr($val->tgl_input, 0, 16);
			$val->tgl_update = substr($val->tgl_update, 0, 16);
			$val->nominal = number_format($val->nominal);
			$val->departement = $this->general_m->get_departement($val->departement);
			// sisa pinjaman
			// $sisa_p = $this->get_sisa_pinjaman($val->anggota_id);
			// $val->sisa_jml = number_format($sisa_p['sisa_jml']);
			// $val->sisa_tagihan = number_format($sisa_p['sisa_tagihan']);
			// $val->sisa_ags = number_format($sisa_p['sisa_ags']);

			$data_list_i[$key] = $val;
		}

		$out = array('rows' => $data_list_i, 'total' => $total);
		return $out;
	}

	function get_pengajuan_simpanan() {
		$this->load->helper('fungsi');
		//$user_id = $this->session->userdata('u_name');

		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$limit = isset($_POST['limit']) ? $_POST['limit'] : 10;
		$search = isset($_POST['search']) ? $_POST['search'] : '';
		
		$fr_jenis = isset($_POST['fr_jenis']) ? $_POST['fr_jenis'] : array();
		$fr_status = isset($_POST['fr_status']) ? $_POST['fr_status'] : array();
		$fr_bulan = isset($_POST['fr_bulan']) ? $_POST['fr_bulan'] : '';
		$tgl_dari = isset($_POST['tgl_dari']) ? $_POST['tgl_dari'] : '';
		$tgl_sampai = isset($_POST['tgl_sampai']) ? $_POST['tgl_sampai'] : '';
		
		//$where = " AND anggota_id = " . $user_id;
		$where = "";
		if($fr_bulan != '') {
			$bln_dari = date("Y-m-d", strtotime($fr_bulan . "-01 -1 month"));
			$bln_dari = substr($bln_dari, 0, 7) . '-21';
			$bln_samp = $fr_bulan . '-20';
			$where .=" AND DATE(tgl_input) >= '".$bln_dari."' ";
			$where .=" AND DATE(tgl_input) <= '".$bln_samp."' ";			
		} else {
			if($tgl_dari != '' && $tgl_sampai != '') {
				$where .=" AND DATE(tgl_input) >= '".$tgl_dari."' ";
				$where .=" AND DATE(tgl_input) <= '".$tgl_sampai."' ";
			}
		}

		if($this->session->userdata('level') == 'operator') {
			$where .= " AND (a.status = '1' OR a.status = '3') ";
		}

		//
		if (! empty($fr_jenis) ) {
			$where .= " AND (";
			$no = 1;
			foreach ($fr_jenis as $fr) {
				if($no > 1) {
					$where .= " OR ";
				}
				$where .= " a.jenis = '".$fr."' ";
				$no++;
			}
			$where .= ") ";
		}

		//
		if (! empty($fr_status) ) {
			$where .= " AND (";
			$no = 1;
			foreach ($fr_status as $fr) {
				if($no > 1) {
					$where .= " OR ";
				}
				$where .= " a.status = '".$fr."' ";
				$no++;
			}
			$where .= ") ";
		}

		$order_by = " ORDER BY tgl_input DESC";
		if ( isset($_POST['sort']) && isset($_POST['order']) ) {
			$order_by = " ORDER BY ".$_POST['sort']." ".$_POST['order']." ";
		}
		$sql_limit = " LIMIT ".$offset.",".$limit." ";
		
		$sql_tampil = "SELECT 
			a.id AS id, a.no_ajuan AS no_ajuan, a.ajuan_id AS ajuan_id, a.anggota_id AS anggota_id, a.tgl_input AS tgl_input, a.jenis AS jenis, a.nominal AS nominal, a.lama_ags AS lama_ags, a.keterangan AS keterangan, a.status AS status, a.alasan AS alasan, a.tgl_update AS tgl_update, a.tgl_cair AS tgl_cair,
			b.identitas AS identitas, b.nama AS nama, b.departement AS departement
			FROM tbl_pengajuan AS a
			LEFT JOIN tbl_anggota AS b ON b.id = a.anggota_id
		 	WHERE 1=1 AND type = 2 ".$where." ".$order_by." ".$sql_limit."";
		$query = $this->db->query($sql_tampil);
		$data_list = $query->result();

		$sql_total = "SELECT id FROM tbl_pengajuan AS a WHERE 1=1 ".$where." ";
		$query = $this->db->query($sql_total);
		$total = $query->num_rows();

		// 
		$data_list_i = array();
		foreach ($data_list as $key => $val) {
			$tgl_arr = explode(' ', $val->tgl_input);
			$tgl = $tgl_arr[0];
			$val->tgl_input_txt = jin_date_ina($tgl,'full',false,true);
			$val->tgl_update_txt = jin_date_ina($tgl);
			$val->tgl_cair_txt = jin_date_ina($val->tgl_cair);
			$val->tgl_input = substr($val->tgl_input, 0, 16);
			$val->tgl_update = substr($val->tgl_update, 0, 16);
			$val->nominal = number_format($val->nominal);
			$val->departement = $this->general_m->get_departement($val->departement);
			$val->urutan = $this->general_m->get_urutan(2,$val->id);
			// // sisa pinjaman
			// $sisa_p = $this->get_sisa_pinjaman($val->anggota_id);
			// $val->sisa_jml = number_format($sisa_p['sisa_jml']);
			// $val->sisa_tagihan = number_format($sisa_p['sisa_tagihan']);
			// $val->sisa_ags = number_format($sisa_p['sisa_ags']);

			$data_list_i[$key] = $val;
		}

		$out = array('rows' => $data_list_i, 'total' => $total);
		return $out;
	}


	function get_pengajuan_cetak($type) {
		$this->load->helper('fungsi');
		
		$fr_jenis = isset($_REQUEST['fr_jenis']) ? explode(',', $_REQUEST['fr_jenis']) : array();
		$fr_status = isset($_REQUEST['fr_status']) ? explode(',', $_REQUEST['fr_status']) : array();
		$fr_bulan = isset($_REQUEST['fr_bulan']) ? $_REQUEST['fr_bulan'] : '';
		$tgl_dari = isset($_REQUEST['tgl_dari']) ? $_REQUEST['tgl_dari'] : '';
		$tgl_sampai = isset($_REQUEST['tgl_sampai']) ? $_REQUEST['tgl_sampai'] : '';

		$where = "AND type = ".$type."";

		if($fr_bulan != '') {
			$bln_dari = date("Y-m-d", strtotime($fr_bulan . "-01 -1 month"));
			$bln_dari = substr($bln_dari, 0, 7) . '-21';
			$bln_samp = $fr_bulan . '-20';
			$where .=" AND DATE(tgl_input) >= '".$bln_dari."' ";
			$where .=" AND DATE(tgl_input) <= '".$bln_samp."' ";			
		} else {
			if($tgl_dari != '' && $tgl_sampai != '') {
				$where .=" AND DATE(tgl_input) >= '".$tgl_dari."' ";
				$where .=" AND DATE(tgl_input) <= '".$tgl_sampai."' ";
			}
		}

		if($this->session->userdata('level') == 'operator') {
			$where .= " AND (a.status = '1' OR a.status = '3') ";
		}
		$fr_jenis = array_diff($fr_jenis, array(NULL)); // NULL / FALSE / ''
		$fr_status = array_diff($fr_status, array(NULL)); // NULL / FALSE / ''
		//return $fr_jenis;
		//
		if (! empty($fr_jenis)) {
			$where .= " AND (";
			$no = 1;
			foreach ($fr_jenis as $fr) {
				if($fr != '') {
					if($no > 1) {
						$where .= " OR ";
					}
					$where .= " a.jenis = '".$fr."' ";
					$no++;
				}
			}
			$where .= ") ";
		}

		//
		if (! empty($fr_status)) {
			$where .= " AND (";
			$no = 1;
			foreach ($fr_status as $fr) {
				if($fr != '') {
					if($no > 1) {
						$where .= " OR ";
					}
					$where .= " a.status = '".$fr."' ";
					$no++;
				}
			}
			$where .= ") ";
		}		
		//return $where;
		$order_by = " ORDER BY tgl_input ASC";
		//$sql_limit = " LIMIT ".$offset.",".$limit." ";
		
		$sql_tampil = "SELECT 
			a.id AS id, a.no_ajuan AS no_ajuan, a.ajuan_id AS ajuan_id, a.anggota_id AS anggota_id, a.tgl_input AS tgl_input, a.jenis AS jenis, a.nominal AS nominal, a.lama_ags AS lama_ags, a.keterangan AS keterangan, a.status AS status, a.alasan AS alasan, a.tgl_update AS tgl_update, a.tgl_cair AS tgl_cair,
			b.identitas AS identitas, b.nama AS nama, b.departement AS departement
			FROM tbl_pengajuan AS a
			LEFT JOIN tbl_anggota AS b ON b.id = a.anggota_id
		 	WHERE 1=1 ".$where." ".$order_by." ";
		$query = $this->db->query($sql_tampil);
		$data_list = $query->result();

		$sql_total = "SELECT id FROM tbl_pengajuan AS a WHERE 1=1 ".$where." ";
		$query = $this->db->query($sql_total);
		$total = $query->num_rows();

		// 
		$data_list_i = array();
		foreach ($data_list as $key => $val) {
			$tgl_arr = explode(' ', $val->tgl_input);
			$tgl = $tgl_arr[0];
			$val->tgl_input_txt = jin_date_ina($tgl, 'pendek');
			$val->tgl_update_txt = jin_date_ina($tgl);
			$val->tgl_cair_txt = jin_date_ina($val->tgl_cair);
			$val->tgl_input = substr($val->tgl_input, 0, 16);
			$val->tgl_update = substr($val->tgl_update, 0, 16);
			$val->nominal = number_format($val->nominal);

			// sisa pinjaman
			$sisa_p = $this->get_sisa_pinjaman($val->anggota_id);
			$val->sisa_jml = number_format($sisa_p['sisa_jml']);
			$val->sisa_tagihan = number_format($sisa_p['sisa_tagihan']);
			$val->sisa_ags = number_format($sisa_p['sisa_ags']);

			$data_list_i[$key] = $val;
		}

		$out = array('rows' => $data_list_i, 'total' => $total);
		return $out;
		//return $where;
	}


	// data sisa pinjaman
	function get_sisa_pinjaman($anggota_id) {
		$this->db->select('*');
		$this->db->from('v_hitung_pinjaman');
		$this->db->where('lunas', 'Belum');
		$this->db->where('anggota_id', $anggota_id);
		$query = $this->db->get();

		$out = array();
		$out['sisa_jml'] 			= 0;
		$out['sisa_tagihan'] = 0;
		$out['sisa_ags'] 		= 0;
		if($query->num_rows() > 0) {
			$result = $query->result();
			$item = 0;
			$sisa_tagihan = 0;
			$sisa_ags = 0;
			foreach ($result as $row) {
				$item++;
				$sisa_tagihan += $row->tagihan - $this->get_jml_bayar($row->id);
				$sisa_ags += $row->lama_angsuran - $this->get_sisa_ags($row->id);
			}
			$out['sisa_jml'] = $item;
			$out['sisa_tagihan'] = $sisa_tagihan;
			$out['sisa_ags'] = $sisa_ags;
			return $out;
		} else {
			return $out;
		}

	}

	function get_jml_bayar($pinjam_id) {
		$this->db->select('SUM(jumlah_bayar) AS total');
		$this->db->from('tbl_pinjaman_d');
		$this->db->where('pinjam_id', $pinjam_id);
		$query = $this->db->get();
		$row = $query->row();
		return $row->total;
	}

	function get_sisa_ags($pinjam_id) {
		$this->db->select('MAX(angsuran_ke) AS angsuran_ke');
		$this->db->from('tbl_pinjaman_d');
		$this->db->where('pinjam_id', $pinjam_id);
		$query = $this->db->get();
		$row = $query->row();
		return $row->angsuran_ke;
	}


	function pengajuan_aksi() {
		$status = $this->input->post('aksi');
		$id = $this->input->post('id');
		$alasan = $this->input->post('alasan');
		$status_txt = 0;
		$tgl_cair = '';
		$tanggal_u = date('Y-m-d H:i');

		switch ($status) {
			case 'Hapus':
				$get_pengajuan = $this->db->get_where('tbl_pengajuan',array('id'=>$id))->row();
				$get_p_h = $this->db->get_where('tbl_pinjaman_h',array('ajuan_id'=> $get_pengajuan->ajuan_id))->row();
				$this->db->delete('tbl_pinjaman_d', array('pinjam_id' => $get_p_h->id));
				$this->db->delete('tbl_pinjaman_h', array('ajuan_id' => $get_pengajuan->ajuan_id));
				return $this->db->delete('tbl_pengajuan', array('id' => $id));
			break;
			case 'Setuju':
				$status_txt = 1;
				$tgl_cair = $this->input->post('tgl_cair');
				$simpan_arr = array(			
					'status'			=>	$status_txt,
					'alasan'			=>	$alasan,
					'tgl_cair'		=>	$tgl_cair,
					'tgl_update'	=> $tanggal_u,
					'user_verif' 	=> $this->data['u_name'],
				);

				$get_pengajuan = $this->db->get_where('tbl_pengajuan',array('id'=>$id))->row();
				$kd_transaksi = $this->general_m->get_kode_transaksi('A');

				//Insert ke Pinjaman
				// $data_pinjaman = array(
				// 	'tgl_pinjam' => date('Y-m-d H:i:s',strtotime($tgl_cair)),
				// 	'anggota_id' => $get_pengajuan->anggota_id,
				// 	'lama_angsuran' => $get_pengajuan->lama_ags,
				// 	'jumlah' => $get_pengajuan->nominal,
				// 	'bunga' => 0,
				// 	'biaya_adm' => 0,
				// 	'lunas' => 'Belum',
				// 	'dk' => 'K',
				// 	'kas_id' => $this->input->post('kas_id'),
				// 	'jns_trans' => 7,
				// 	'keterangan' => $alasan,
				// 	'user_name' => $this->data['u_name'],
				// 	'type' => 2,
				// 	'status' => 0,
				// 	'ajuan_id' => $get_pengajuan->ajuan_id,
				// 	'kd_transaksi' => $kd_transaksi

				// );
				// $this->db->insert('tbl_pinjaman_h',$data_pinjaman);
			break;
			case 'Tolak':
				$status_txt = 2;
				$simpan_arr = array(			
					'status'			=>	$status_txt,
					'alasan'			=>	$alasan,
					'tgl_update'	=> $tanggal_u
				);
			break;
			case 'Pending':
				$status_txt = 0;
				$simpan_arr = array(			
					'status'			=>	$status_txt,
					'alasan'			=>	$alasan,
					'tgl_update'	=> $tanggal_u
				);
			break;
			case 'Batal':
				$status_txt = 4;
				$simpan_arr = array(			
					'status'			=>	$status_txt,
					'tgl_update'	=> $tanggal_u
				);
			break;
			case 'Terlaksana':
				$status_txt = 3;
				$simpan_arr = array(			
					'status'			=>	$status_txt,
					'tgl_update'	=> $tanggal_u
				);
			break;
			case 'Belum':
				$status_txt = 1;
				$simpan_arr = array(			
					'status'			=>	$status_txt,
					'tgl_update'	=> $tanggal_u
				);
			break;
			default:
				return FALSE;
			break;
		}
		
		$this->db->where('id', $id);
		return $this->db->update('tbl_pengajuan',$simpan_arr);
	}

	

	function pengajuan_edit() {
		$out = '';
		$kolom = $this->input->post('name');
		$id = $this->input->post('pk');
		$value = $this->input->post('value');
		$value_insert = $value;
		$nominal = false;
		
		if($kolom == 'nominal') {
			$value_insert = preg_replace("/[^0-9]/", "",$value);

			$get_type = $this->db->select('jenis')->get_where('tbl_pengajuan',array('id'=>$id))->row_array();
			
			if($get_type['jenis'] == 'Pinjaman Jangka Pendek' || $get_type['jenis'] == 'Pinjaman Cashbone'){
				$value_potongan = ($value_insert *1) / 100;
				$value_diterima = $value_insert - $value_potongan;
				$nominal = true;
			}

		} else if($kolom == 'keterangan') {
			// ok
		} else if($kolom == 'lama_ags') {
			// ok
			$value_insert = preg_replace("/[^0-9]/", "",$value);
		} else {
			return false;
		}

		$tanggal_u = date('Y-m-d H:i');
		if($nominal){
			$simpan_arr = array(			
				$kolom			=>	$value_insert,
				'potongan' 		=>  $value_potongan,
				'uang_diterima'	=> $value_diterima,
				'tgl_update'	=> $tanggal_u
			);
		}else {
			$simpan_arr = array(			
				$kolom			=>	$value_insert,
				'tgl_update'	=> $tanggal_u
			);
		}

		$this->db->where('id', $id);
		if($this->db->update('tbl_pengajuan', $simpan_arr)) {
			if($kolom == 'nominal') {
				$value = number_format($value_insert * 1);
			}
			return $value;
		} else {
			return 'Error';
		}
	}

	//data kas
	function get_data_kas() {
		$this->db->select('*');
		$this->db->from('nama_kas_tbl');
		$this->db->where('aktif', 'Y');
		$this->db->where('tmpl_pinjaman', 'Y');
		$this->db->order_by('id', 'ASC');
		$query = $this->db->get();
		if($query->num_rows()>0){
			$out = $query->result();
			return $out;
		} else {
			return array();
		}
	}

	//data jenis angsuran
	function get_data_angsuran() {
		$this->db->select('*');
		$this->db->from('jns_angsuran');
		$this->db->where('aktif', 'Y');
		$this->db->order_by('ket', 'ASC');
		$query = $this->db->get();
		if($query->num_rows()>0){
			$out = $query->result();
			return $out;
		} else {
			return array();
		}
	}


	//data jenis pinjaman
	function get_data_pinjaman() {
		$this->db->select('*');
		$this->db->from('jns_pinjaman');
		$this->db->where('aktif', 'Y');
		$this->db->order_by('id_jns_pinjaman', 'ASC');
		$query = $this->db->get();
		if($query->num_rows()>0){
			$out = $query->result();
			return $out;
		} else {
			return array();
		}
	}

	//data Bunga
	function get_data_bunga() {
		$this->db->select('*');
		$this->db->from('suku_bunga');
		$this->db->where('opsi_key', 'bg_pinjam');
		$this->db->order_by('id', 'ASC');
		$query = $this->db->get();
		if($query->num_rows()>0){
			$out = $query->result();
			return $out;
		} else {
			return FALSE;
		}
	}

	//data biaya adm
	function get_biaya_adm() {
		$this->db->select('*');
		$this->db->from('suku_bunga');
		$this->db->where('opsi_key', 'biaya_adm');
		$this->db->order_by('id', 'ASC');
		$query = $this->db->get();
		if($query->num_rows()>0){
			$out = $query->result();
			return $out;
		} else {
			return FALSE;
		}
	}

	//data data barang
	function get_id_barang() {
		$this->db->select('*');
		$this->db->from('tbl_barang');
		$this->db->where('jml_brg >', 0);
		$this->db->or_where('type', 'uang');
		$this->db->order_by('nm_barang', 'ASC');
		$query = $this->db->get();

		if($query->num_rows()>0){
			$out = $query->result();
			return $out;
		} else {
			return array();
		}
	}

	//data barang berdasarkan ID
	function get_data_barang($id) {
		$this->db->select('*');
		$this->db->from('tbl_barang');
		$this->db->where('id',$id);
		$query = $this->db->get();

		if($query->num_rows()>0){
			$out = $query->row();
			return $out;
		} else {
			return array();
		}
	}

	//data anggota
	function lap_data_anggota() {
		$this->db->select('*');
		$this->db->from('tbl_anggota');
		$this->db->where('aktif', 'Y');
		$this->db->order_by('id', 'ASC');
		$query = $this->db->get();
		if($query->num_rows()>0){
			$out = $query->result();
			return $out;
		} else {
			return FALSE;
		}
	}

	//ambil data pinjaman header berdasarkan ID peminjam
	function get_data_pinjam_id($id) {
		$this->db->select('*');
		$this->db->from('v_hitung_pinjaman');
		$this->db->where('anggota_id',$id);
		$query = $this->db->get();

		if($query->num_rows() > 0){
			$out = $query->row();
			return $out;
		} else {
			return FALSE;
		}
	}

	//ambil data pinjaman header berdasarkan ID
	function get_data_pinjam($id) {
		$this->db->select('*');
		$this->db->from('v_hitung_pinjaman');
		$this->db->where('id',$id);
		$query = $this->db->get();

		if($query->num_rows() > 0) {
			$out = $query->row();
			return $out;
		} else {
			return FALSE;
		}
	}

	//ambil data pengajuan berdasarkan ID
	function get_data_pengajuan($id) {
		$sql_tampil = "SELECT 
			a.id AS id, a.anggota_id AS anggota_id, a.type AS type, a.tgl_input AS tgl_input, a.jenis AS jenis, a.nominal AS nominal, a.lama_ags AS lama_ags, a.keterangan AS keterangan, a.status AS status, a.alasan AS alasan, a.tgl_update AS tgl_update, a.tgl_cair AS tgl_cair,
			b.identitas AS identitas, b.nama AS nama, b.departement AS departement
			FROM tbl_pengajuan AS a
			LEFT JOIN tbl_anggota AS b ON b.id = a.anggota_id
		 	WHERE  a.id = ".$id."";
		$query = $this->db->query($sql_tampil);
		if($query->num_rows() > 0) {
			$out = $query->row();
			return $out;
		} else {
			return FALSE;
		}
	}	


	function get_simulasi_pinjaman($pinjam_id) {
		$row = $this->get_data_pinjam($pinjam_id);
		$this->load->model('bunga_m');
		$this->load->library('finance');
		if($row) {
			$out = array();
			$conf_bunga = $this->bunga_m->get_key_val();
			$denda_hari = sprintf('%02d', $conf_bunga['denda_hari']);
			$biaya_admin = $conf_bunga['biaya_adm'];
			$tgl_tempo_next = 0;
			$get_data_p = $this->db->select('anuitas,bunga_anuitas')->get_where('jns_pinjaman',array('jns_pinjaman'=>$row->jenis_pinjam))->row_array();
			$lama_ags = $row->lama_angsuran;
			$nominal = $row->jumlah;
			for ($i=1; $i <= $row->lama_angsuran; $i++) { 
				$odat = array();
				
				/*
				if($conf_bunga['pinjaman_bunga_tipe'] == 'C') {
					$odat['bunga_pinjaman'] = ($row->lama_angsuran - ($i - 1)) * ($row->pokok_angsuran * $row->bunga) / 100;
					$odat['jumlah_ags'] = $row->pokok_angsuran + $odat['bunga_pinjaman'];
				} else {
					$odat['bunga_pinjaman'] = $row->bunga_pinjaman;
					$odat['jumlah_ags'] = $row->ags_per_bulan;
				}
				*/

				if($get_data_p['anuitas'] == 0){
					$odat['angsuran_pokok'] = $row->pokok_angsuran * 1;
					
					$odat['bunga_pinjaman'] = $row->bunga_pinjaman;
					$odat['jumlah_ags'] = $row->ags_per_bulan;
				}else {

					$bunga_a = $get_data_p['bunga_anuitas'] / 100;
					$cicil = $this->finance->ppmt($bunga_a/12, $i, $lama_ags, - $nominal, 0, false);
					$margin = $this->finance->ipmt($bunga_a/12, $i, $lama_ags, - $nominal, 0, false);
					$odat['angsuran_pokok'] =  $cicil;
					$odat['bunga_pinjaman'] =  $margin;
					$odat['jumlah_ags'] = $cicil + $margin;
				}
				$odat['tgl_pinjam'] = substr($row->tgl_pinjam, 0, 10);
				$odat['biaya_adm'] = $row->biaya_adm;

				$odat['check_bayar'] = $this->db->select('id')->get_where('tbl_pinjaman_d',array('pinjam_id'=>$pinjam_id,'angsuran_ke'=>$i))->num_rows();
				
				$tgl_tempo_var = substr($row->tgl_pinjam, 0, 7) . '-01';
				$tgl_tempo = date("Y-m-d", strtotime($tgl_tempo_var . " +".$i." month"));
				$tgl_tempo = substr($tgl_tempo, 0, 7) . '-' . $denda_hari;
				$odat['tgl_tempo'] = $tgl_tempo;
				$out[] = $odat;
			}
			return $out;
		} else {
			return FALSE;
		}
	}

	function get_data_transaksi_ajax($offset, $limit, $q='', $sort, $order) {
		$sql = "SELECT v_hitung_pinjaman.* FROM v_hitung_pinjaman ";
		$where = " WHERE dk = 'K' AND status = 1 ";
		if(is_array($q)) {
			if($q['kode_transaksi'] != '') {
					$q['kode_transaksi'] = str_replace('PJ', '', $q['kode_transaksi']);
					$q['kode_transaksi'] = str_replace('AG', '', $q['kode_transaksi']);
					$q['kode_transaksi'] = $q['kode_transaksi'] * 1;
					$where .=" AND id LIKE '%".$q['kode_transaksi']."%' OR anggota_id LIKE '%".$q['kode_transaksi']."%' ";
				} else {
					if($q['cari_nama'] != '') {
						$where .=" AND tbl_anggota.nama LIKE '%".$q['cari_nama']."%' ";
						$sql .= " LEFT JOIN tbl_anggota ON (v_hitung_pinjaman.anggota_id = tbl_anggota.id) ";
					}					
					if($q['cari_status'] != '') {
						$where .=" AND lunas LIKE '%".$q['cari_status']."%' ";
					}
					if($q['tgl_dari'] != '' && $q['tgl_sampai'] != '') {
						$where .=" AND DATE(tgl_pinjam) >= '".$q['tgl_dari']."' ";
						$where .=" AND DATE(tgl_pinjam) <= '".$q['tgl_sampai']."' ";
					}
			}
		}
		$sql .= $where;
		$result['count'] = $this->db->query($sql)->num_rows();
		$sql .= " ORDER BY {$sort} {$order} ";
		$sql .= " LIMIT {$offset},{$limit} ";
		$result['data'] = $this->db->query($sql)->result();
		return $result;
	}

	//panggil data simpanan untuk laporan 
	function lap_data_pinjaman() {
		$kode_transaksi = isset($_REQUEST['kode_transaksi']) ? $_REQUEST['kode_transaksi'] : '';
		$cari_status = isset($_REQUEST['cari_status']) ? $_REQUEST['cari_status'] : '';
		$tgl_dari = isset($_REQUEST['tgl_dari']) ? $_REQUEST['tgl_dari'] : '';
		$tgl_sampai = isset($_REQUEST['tgl_sampai']) ? $_REQUEST['tgl_sampai'] : '';
		$sql = '';
		$sql = " SELECT * FROM v_hitung_pinjaman WHERE dk = 'K' ";
		$q = array('kode_transaksi' => $kode_transaksi, 
			'cari_status'	=> $cari_status,
			'tgl_dari' 		=> $tgl_dari, 
			'tgl_sampai' 	=> $tgl_sampai);
		if(is_array($q)) {
			if($q['kode_transaksi'] != '') {
				$q['kode_transaksi'] = str_replace('PJ', '', $q['kode_transaksi']);
				$q['kode_transaksi'] = str_replace('AG', '', $q['kode_transaksi']);
				$q['kode_transaksi'] = $q['kode_transaksi'] * 1;
				$sql .=" AND (id LIKE '".$q['kode_transaksi']."' OR anggota_id LIKE '".$q['kode_transaksi']."') ";
			} else {
				if($q['cari_status'] != '') {
					$sql .=" AND lunas LIKE '%".$q['cari_status']."%' ";
				}

				if($q['tgl_dari'] != '' && $q['tgl_sampai'] != '') {
					$sql .=" AND DATE(tgl_pinjam) >= '".$q['tgl_dari']."' ";
					$sql .=" AND DATE(tgl_pinjam) <= '".$q['tgl_sampai']."' ";
				}
			}
		}
		$sql .=" ORDER BY tgl_pinjam ASC ";
		$query = $this->db->query($sql);
		if($query->num_rows() > 0) {
			$out = $query->result();
			return $out;
		} else {
			return FALSE;
		}
	}

	public function create() {
		if (str_replace(',', '', $this->input->post('jumlah')) <= 0) {
			return FALSE;
		}

		// TRANSACTIONAL DB COMMIT
		$this->db->trans_start();

		// update stok barang berkurang
		if($this->input->post('barang_id')){
			$this->db->where('id', $this->input->post('barang_id'));
			$this->db->where('type <>', 'uang');
			$this->db->set('jml_brg', 'jml_brg - 1', FALSE);
			$this->db->update('tbl_barang');
		}

		$data = array(			
			'tgl_pinjam'			=>	$this->input->post('tgl_pinjam'),
			'anggota_id'			=>	$this->input->post('anggota_id'),
			'barang_id'				=>	$this->input->post('barang_id'),
			'lama_angsuran'		=>	$this->input->post('lama_angsuran'),
			'jumlah'					=>	str_replace(',', '', $this->input->post('jumlah')),
			'bunga'					=>	$this->input->post('bunga'),
			'biaya_adm'				=>	str_replace(',', '', $this->input->post('biaya_adm')),
			'dk'						=>	'K',
			'jns_trans'				=>	'7',
			'kas_id'					=>	$this->input->post('kas_id'),
			'keterangan'			=> $this->input->post('ket'),
			'user_name'				=> $this->data['u_name'],
			'type' 					=> $this->input->post('tipe')
			);
		
		$this->db->insert('tbl_pinjaman_h', $data);

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return FALSE;
		} else {
			$this->db->trans_complete();
			return TRUE;
		}
	}


	public function update($id){
		if(str_replace(',', '', $this->input->post('jumlah')) <= 0) {
			return FALSE;
		}

		$tanggal_u = date('Y-m-d H:i');
		$this->db->where('id', $id);
		return $this->db->update('tbl_pinjaman_h',array(
			'tgl_pinjam'			=>	$this->input->post('tgl_pinjam'),
			'lama_angsuran'		=>	$this->input->post('lama_angsuran'),
			'jumlah'					=>	str_replace(',', '', $this->input->post('jumlah')),
			'bunga'					=>	$this->input->post('bunga'),
			'biaya_adm'				=>	str_replace(',', '', $this->input->post('biaya_adm')),
			'kas_id'					=>	$this->input->post('kas_id'),
			'update_data'			=> $tanggal_u,
			'keterangan'			=> $this->input->post('ket'),
			'user_name'				=> $this->data['u_name'],
			'type' 					=> $this->input->post('tipe')
			));
	}

	public function delete($id) {
		// TRANSACTIONAL DB START
		$this->db->trans_start();

		// update stok barang bertambah
		$this->db->select('barang_id');
		$this->db->from('tbl_pinjaman_h');
		$this->db->where('id', $id);
		$query = $this->db->get();
		$row = $query->row();
		$barang_id = $row->barang_id;

		$this->db->where('id', $barang_id);
		$this->db->set('jml_brg', 'jml_brg + 1', FALSE);
		$this->db->update('tbl_barang');
		$this->db->delete('tbl_pinjaman_h', array('id' => $id));

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return FALSE;
		} else {
			$this->db->trans_complete();
			return TRUE;
		}
		// TRANSACTIONAL DB END
	}


	function PostPinjaman($total_akun,$tgl,$kd_transaksi,$ajuan_id,$keterangan,$kas_id,$username)
	{


		


		$get_pengajuan = $this->db->get_where('tbl_pengajuan',array('ajuan_id'=>$ajuan_id))->row();
		for ($i=1; $i < $total_akun; $i++) { 

			$jns_trans = $this->input->post('akun_'.$i);
			$dk = $this->input->post('dk_'.$i);
			$jumlah = str_replace('.', '', $this->input->post('jumlah_'.$i));

			$data_pinjaman = array(
				'tgl_pinjam' => date('Y-m-d H:i:s',strtotime($get_pengajuan->tgl_cair)),
				'anggota_id' => $get_pengajuan->anggota_id,
				'lama_angsuran' => $get_pengajuan->lama_ags,
				'jumlah' => $get_pengajuan->nominal,
				'jenis_pinjam' => $get_pengajuan->jenis,
				'bunga' => 0,
				'biaya_adm' => 0,
				'lunas' => 'Belum',
				'dk' => 'K',
				'kas_id' => $kas_id,
				'jns_trans' => 7,
				'keterangan' => $keterangan,
				'user_name' => $username,
				'type' => 2,
				'status' => 0,
				'ajuan_id' => $get_pengajuan->ajuan_id,
				'kd_transaksi' => $kd_transaksi,
				'update_data' => date('Y-m-d H:i:s',strtotime($tgl))

			);
			$this->db->insert('tbl_pinjaman_h',$data_pinjaman);

			$simpan_arr = array(			
				'status'			=>	3,
				'tgl_update'	=> date('Y-m-d H:i:s'),
				'user_verif' 	=> $username,
			);
			$this->db->where('ajuan_id', $ajuan_id);
			$this->db->update('tbl_pengajuan',$simpan_arr);
		}

		
	}















}