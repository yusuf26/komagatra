<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Simpanan_m extends CI_Model {

	public function __construct(){
		parent::__construct();
	}

	#panggil data kas
	function get_data_kas() {
		$this->db->select('*');
		$this->db->from('nama_kas_tbl');
		$this->db->where('aktif', 'Y');
		$this->db->where('tmpl_simpan', 'Y');
		$this->db->order_by('id', 'ASC');
		$query = $this->db->get();
		if($query->num_rows()>0){
			$out = $query->result();
			return $out;
		} else {
			return FALSE;
		}
	}

	//panggil data simpanan untuk laporan 
	function lap_data_simpanan() {
		$kode_transaksi = isset($_REQUEST['kode_transaksi']) ? $_REQUEST['kode_transaksi'] : '';
		$cari_simpanan = isset($_REQUEST['cari_simpanan']) ? $_REQUEST['cari_simpanan'] : '';
		$tgl_dari = isset($_REQUEST['tgl_dari']) ? $_REQUEST['tgl_dari'] : '';
		$tgl_sampai = isset($_REQUEST['tgl_sampai']) ? $_REQUEST['tgl_sampai'] : '';
		$sql = '';
		$sql = " SELECT * FROM tbl_trans_sp WHERE dk='D' ";
		$q = array('kode_transaksi' => $kode_transaksi, 
			'cari_simpanan' => $cari_simpanan,
			'tgl_dari' => $tgl_dari, 
			'tgl_sampai' => $tgl_sampai);
		if(is_array($q)) {
			if($q['kode_transaksi'] != '') {
				$q['kode_transaksi'] = str_replace('TRD', '', $q['kode_transaksi']);
				$q['kode_transaksi'] = str_replace('AG', '', $q['kode_transaksi']);
				$q['kode_transaksi'] = $q['kode_transaksi'] * 1;
				$sql .=" AND (id LIKE '".$q['kode_transaksi']."' OR anggota_id LIKE '".$q['kode_transaksi']."') ";
			} else {
				if($q['cari_simpanan'] != '') {
					$sql .=" AND jenis_id = '".$q['cari_simpanan']."%' ";
				}
				if($q['tgl_dari'] != '' && $q['tgl_sampai'] != '') {
					$sql .=" AND DATE(tgl_transaksi) >= '".$q['tgl_dari']."' ";
					$sql .=" AND DATE(tgl_transaksi) <= '".$q['tgl_sampai']."' ";
				}
			}
		}
		$query = $this->db->query($sql);
		if($query->num_rows() > 0) {
			$out = $query->result();
			return $out;
		} else {
			return FALSE;
		}
	}

	//panggil data anggota
	function get_data_anggota($id) {
		$this->db->select('*');
		$this->db->from('tbl_anggota');
		$this->db->where('id',$id);
		$query = $this->db->get();
		if($query->num_rows()>0){
			$out = $query->row();
			return $out;
		} else {
			return FALSE;
		}
	}

	//panggil data jenis simpan
	function get_jenis_simpan($id) {
		$this->db->select('*');
		$this->db->from('jns_simpan');
		$this->db->where('id',$id);
		$query = $this->db->get();
		if($query->num_rows()>0){
			$out = $query->row();
			return $out;
		} else {
			return FALSE;
		}
	}

	//hitung jumlah total simpanan
	function get_jml_simpanan() {
		$this->db->select('SUM(jumlah) AS jml_total');
		$this->db->from('tbl_trans_sp');
		$this->db->where('dk','D');
		$query = $this->db->get();
		return $query->row();
	}

	//panggil data simpanan untuk esyui
	function get_data_transaksi_ajax($offset, $limit, $q='', $sort, $order) {
		$sql = "SELECT * FROM tbl_trans_sp WHERE dk='D' ";
		if(is_array($q)) {
			if($q['kode_transaksi'] != '') {
				$q['kode_transaksi'] = str_replace('TRD', '', $q['kode_transaksi']);
				$q['kode_transaksi'] = str_replace('AG', '', $q['kode_transaksi']);
				$q['kode_transaksi'] = $q['kode_transaksi'] * 1;
				$sql .=" AND (id LIKE '".$q['kode_transaksi']."' OR anggota_id LIKE '".$q['kode_transaksi']."') ";
			} else {
				if($q['cari_simpanan'] != '') {
					$sql .=" AND jenis_id = '".$q['cari_simpanan']."%' ";
				}

				if($q['tgl_dari'] != '' && $q['tgl_sampai'] != '') {
					$sql .=" AND DATE(tgl_transaksi) >= '".$q['tgl_dari']."' ";
					$sql .=" AND DATE(tgl_transaksi) <= '".$q['tgl_sampai']."' ";
				}
			}
		}
		$result['count'] = $this->db->query($sql)->num_rows();
		$sql .=" ORDER BY {$sort} {$order} ";
		$sql .=" LIMIT {$offset},{$limit} ";
		$result['data'] = $this->db->query($sql)->result();
		return $result;
	}


	function get_data_penarikan_simpanan($offset, $limit, $q='', $sort, $order) {
		$sql = "SELECT * FROM tbl_pengajuan WHERE type=3 ";
		if(is_array($q)) {
			if($q['kode_transaksi'] != '') {
				$sql .=" AND (ajuan_id LIKE '".$q['kode_transaksi']."') ";
			} else {

				if($q['cari_simpanan'] != '') {
					$sql .=" AND jenis = '".$q['cari_simpanan']."' ";
				}

				if($q['tgl_dari'] != '' && $q['tgl_sampai'] != '') {
					$sql .=" AND DATE(tgl_input) >= '".$q['tgl_dari']."' ";
					$sql .=" AND DATE(tgl_input) <= '".$q['tgl_sampai']."' ";
				}
			}

			if (! empty($q['cari_status']) ) {
				$sql .= " AND (";
				$no = 1;
				foreach ($q['cari_status'] as $fr) {
					if($no > 1) {
						$sql .= " OR ";
					}
					$sql .= " status = '".$fr."' ";
					$no++;
				}
				$sql  .= ") ";
			}
		}
		$result['count'] = $this->db->query($sql)->num_rows();
		$sql .=" ORDER BY {$sort} {$order} ";
		$sql .=" LIMIT {$offset},{$limit} ";
		$result['data'] = $this->db->query($sql)->result();
		return $result;
	}

	function get_data_perubahan_simpanan($offset, $limit, $q='', $sort, $order) {
		$sql = "SELECT * FROM tbl_pengajuan WHERE type=4 ";
		if(is_array($q)) {
			if($q['kode_transaksi'] != '') {
				$sql .=" AND (ajuan_id LIKE '".$q['kode_transaksi']."') ";
			} else {

				if($q['cari_simpanan'] != '') {
					$sql .=" AND jenis = '".$q['cari_simpanan']."' ";
				}

				if($q['tgl_dari'] != '' && $q['tgl_sampai'] != '') {
					$sql .=" AND DATE(tgl_input) >= '".$q['tgl_dari']."' ";
					$sql .=" AND DATE(tgl_input) <= '".$q['tgl_sampai']."' ";
				}
			}

			if (! empty($q['cari_status']) ) {
				$sql .= " AND (";
				$no = 1;
				foreach ($q['cari_status'] as $fr) {
					if($no > 1) {
						$sql .= " OR ";
					}
					$sql .= " status = '".$fr."' ";
					$no++;
				}
				$sql  .= ") ";
			}
		}
		$result['count'] = $this->db->query($sql)->num_rows();
		$sql .=" ORDER BY {$sort} {$order} ";
		$sql .=" LIMIT {$offset},{$limit} ";
		$result['data'] = $this->db->query($sql)->result();
		return $result;
	}


	public function get_macet($anggota_id)
	{
		$this->db->select('id,jumlah,ags_per_bulan,bln_sudah_angsur,lama_angsuran');
		$this->db->from('v_hitung_pinjaman');
		$thn = date('Y');			
		$bln = date('m');
		$where = "YEAR(tempo) = '".$thn."' AND  MONTH(tempo) < '".$bln."' ";
		$this->db->where($where);
		$this->db->where('anggota_id',$anggota_id);
		$this->db->where('lunas','Belum');
		$query = $this->db->get();
		if($query->num_rows()>0){
			$out = $query->result();
			return $out;
		} else {
			return array();
		}
	}

	public function create() {
		$output = false;
		if(str_replace(',', '', $this->input->post('jumlah')) <= 0) {
			return $output;
		}
		$kas_id =$this->input->post('kas_id');
		$jumlah = str_replace(',', '', $this->input->post('jumlah'));
		$total_jumlah = $jumlah;
		$anggota_id = $this->input->post('anggota_id');
		$check_macet = $this->get_macet($anggota_id);
		if(!empty($check_macet)){
			foreach ($check_macet as $key => $row) {
				$total_macet = $row->ags_per_bulan * ( $row->lama_angsuran - $row->bln_sudah_angsur);
				$get_denda = $this->db->get_where('suku_bunga',array('opsi_key'=>'denda'))->row();
				if($total_macet > $jumlah){
					$insert_bayar_macet = array(
						'pinjam_id' => $row->id,
						'angsuran_ke' => $row->bln_sudah_angsur + 1,
						'jumlah_bayar' => $jumlah,
						'denda' => $get_denda->opsi_val,
						'terlambat' => 0,
						'key_bayar' => 'Angsuran',
						'dk' => 'D',
						'kas_id' => $kas_id,
						'jns_trans' => 48,
						'user_name' => $this->data['u_name'],
						'keterangan' => 'Pembayaran dari hasil potong Simpanan'
					);	
					$this->db->insert('tbl_pinjaman_d',$insert_bayar_macet);

					$total_jumlah = 0;
				}else {

					$total_jumlah = $jumlah - $total_macet;

					$insert_bayar_macet = array(
						'pinjam_id' => $row->id,
						'angsuran_ke' => $row->bln_sudah_angsur + 1,
						'jumlah_bayar' => $total_macet,
						'denda' => $get_denda->opsi_val,
						'terlambat' => 0,
						'key_bayar' => 'Angsuran',
						'dk' => 'D',
						'kas_id' => $kas_id,
						'jns_trans' => 48,
						'user_name' => $this->data['u_name'],
						'keterangan' => 'Pembayaran dari hasil potong Simpanan'
					);	
					$this->db->insert('tbl_pinjaman_d',$insert_bayar_macet);

					$this->db->update('tbl_pinjaman_h',array('lunas'=>'Lunas'),array('id'=>$row->id));

				}
			}
		}


		$data = array(			
			'tgl_transaksi'		=>	$this->input->post('tgl_transaksi'),
			'anggota_id'			=>	$anggota_id,
			'jenis_id'				=>	$this->input->post('jenis_id'),
			'jumlah'				=>	$total_jumlah,
			'keterangan'			=> $this->input->post('ket'),
			'akun'					=>	'Setoran',
			'dk'					=>	'D',
			'kas_id'				=>	$kas_id,
			'user_name'				=> $this->data['u_name'],
			'nama_penyetor'			=> $this->input->post('nama_penyetor'),
			'no_identitas'			=> $this->input->post('no_identitas'),
			'alamat'				=> $this->input->post('alamat')
			);
		return $this->db->insert('tbl_trans_sp', $data);
	}

	public function update($id)
	{
		if(str_replace(',', '', $this->input->post('jumlah')) <= 0) {
			return FALSE;
		}
		$tanggal_u = date('Y-m-d H:i');
		$this->db->where('id', $id);
		return $this->db->update('tbl_trans_sp',array(
			'tgl_transaksi'		=>	$this->input->post('tgl_transaksi'),
			'jenis_id'				=>	$this->input->post('jenis_id'),
			'jumlah'					=>	str_replace(',', '', $this->input->post('jumlah')),
			'keterangan'			=> $this->input->post('ket'),
			'kas_id'					=>	$this->input->post('kas_id'),
			'update_data'			=> $tanggal_u,
			'user_name'				=> $this->data['u_name'],
			'nama_penyetor'		=> $this->input->post('nama_penyetor'),
			'no_identitas'			=> $this->input->post('no_identitas'),
			'alamat'					=> $this->input->post('alamat')
			));
	}

	public function delete($id) {
		return $this->db->delete('tbl_trans_sp', array('id' => $id)); 
	}
}