<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pemasukan_bank_m extends CI_Model {

	public function __construct(){
		parent::__construct();
	}

	#panggil data kas
	function get_data_kas() {
		$this->db->select('*');
		$this->db->from('nama_kas_tbl');
		$this->db->where('aktif', 'Y');
		$this->db->where('type','bank');
		$this->db->where('tmpl_pemasukan', 'Y');
		$this->db->order_by('id', 'ASC');
		$query = $this->db->get();
		if($query->num_rows()>0){
			$out = $query->result();
			return $out;
		} else {
			return array();
		}
	}

//panggil data jenis kas
	function get_jenis_kas($id) {
		$this->db->select('*');
		$this->db->from('nama_kas_tbl');
		$this->db->where('id',$id);
		$query = $this->db->get();

		if($query->num_rows()>0){
			$out = $query->row();
			return $out;
		} else {
			return FALSE;
		}
	}

	#panggil data akun
	function get_data_akun() {
		$this->db->select('*');
		$this->db->from('jns_akun');
		$this->db->where('aktif', 'Y');
		$this->db->where('pemasukan', 'Y');
		$this->db->order_by('id', 'ASC');
		$query = $this->db->get();
		if($query->num_rows()>0){
			$out = $query->result();
			return $out;
		} else {
			return array();
		}
	}

	//panggil data jenis kas
	function get_jenis_akun($id) {
		$this->db->select('*');
		$this->db->from('jns_akun');
		$this->db->where('id',$id);
		$query = $this->db->get();

		if($query->num_rows()>0){
			$out = $query->row();
			return $out;
		} else {
			return FALSE;
		}
	}

	//panggil data simpanan untuk laporan 
	function lap_data_pemasukan() {
		$kode_transaksi = isset($_REQUEST['kode_transaksi']) ? $_REQUEST['kode_transaksi'] : '';
		$tgl_dari = isset($_REQUEST['tgl_dari']) ? $_REQUEST['tgl_dari'] : '';
		$tgl_sampai = isset($_REQUEST['tgl_sampai']) ? $_REQUEST['tgl_sampai'] : '';
		$sql = '';
		$sql = " SELECT tbl_trans_kas.*  
		FROM tbl_trans_kas 
		LEFT JOIN nama_kas_tbl ON nama_kas_tbl.id = tbl_trans_kas.untuk_kas_id
		WHERE akun='Pemasukan' AND type = 'bank' ";
		$q = array('kode_transaksi' => $kode_transaksi, 
			'tgl_dari' => $tgl_dari, 
			'tgl_sampai' => $tgl_sampai);
		if(is_array($q)) {
			if($q['kode_transaksi'] != '') {
				$q['kode_transaksi'] = str_replace('TKD', '', $q['kode_transaksi']);
				$q['kode_transaksi'] = $q['kode_transaksi'] * 1;
				$sql .=" AND tbl_trans_kas.id LIKE '".$q['kode_transaksi']."' ";
			} else {
			
				if($q['tgl_dari'] != '' && $q['tgl_sampai'] != '') {
					$sql .=" AND DATE(tgl_catat) >= '".$q['tgl_dari']."' ";
					$sql .=" AND DATE(tgl_catat) <= '".$q['tgl_sampai']."' ";
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

	//panggil data simpanan untuk esyui
	function get_data_transaksi_ajax($offset, $limit, $q='', $sort, $order) {

		$this->load->model('general_m');
		$result['count'] = $this->general_m->kocak_model('bank',1,$q)->num_rows();
		$result['data'] = $this->general_m->kocak_model('bank',1,$q,$sort,$order,$limit,$offset)->result();

		return $result;


	}

	public function create() {
		if(str_replace(',', '', $this->input->post('jumlah')) <= 0) {
			return FALSE;
		}

		$jenis = $this->input->post('jenis');

		if($jenis == 'Pembayaran Angsuran'){

			$angsuran = $this->input->post('angsuran');
			$row_pinjam = $this->general_m->get_data_pinjam($angsuran); #data pinjam
			$ags_ke = $this->general_m->get_record_bayar($angsuran) + 1;

			$bunga = $row_pinjam->bunga_pinjaman;
			$jumlah_bayar = str_replace(',', '', $this->input->post('jumlah')) - $bunga;


			if($row_pinjam->lama_angsuran == $ags_ke){
				$ket_bayar = 'Pelunasan';

				$this->db->update('tbl_pinjaman_h',array('update_data'=>date('Y-m-d H:i:s'),'lunas'=>'Lunas'),array('id'=>$angsuran));
			}else {
				$ket_bayar = 'Angsuran';
			}

			$simp_anggota = array(
				'tgl_transaksi' => date('Y-m-d H:i:s'),
				'anggota_id' => $row_pinjam->anggota_id,
				'jenis_id' => 41,
				'jumlah' => $bunga,
				'keterangan' => 'Simpanan Dari Pembayaran Pinjaman',
				'akun' => 'Setoran',
				'dk' => 'D',
				'kas_id' => $this->input->post('kas_id'),
				'user_name'		=> $this->data['u_name']
			);
			$this->db->insert('tbl_trans_sp',$simp_anggota);

			$data = array(
				'tgl_bayar' 	=> $this->input->post('tgl_transaksi'),
				'pinjam_id' 	=> $angsuran,
				'angsuran_ke'	=> $ags_ke,
				'jumlah_bayar'	=> $jumlah_bayar,
				'simpan_wajib'	=> $bunga,
				'ket_bayar'		=> $ket_bayar,
				'dk'			=> 'D',
				'kas_id'		=> $this->input->post('kas_id'),
				'jns_trans'		=> 48,
				'user_name'		=> $this->data['u_name'],
				'keterangan'		=>	$this->input->post('ket'),
			);

			return $this->db->insert('tbl_pinjaman_d',$data);

		}else if($jenis == 'Setoran Sukarela'){

			$setoran = $this->input->post('setoran');
			$get_setoran = $this->db->select('anggota_id,lama_ags,ajuan_id')->get_where('tbl_pengajuan',array('id'=>$setoran))->row_array();
			$simpan_arr = array(			
				'status'		=>	1,
				'keterangan'	=> $this->input->post('ket'),
				'tgl_cair'		=>	$this->input->post('tgl_transaksi'),
				'tgl_update'	=> date('Y-m-d H:i:s'),
				'user_verif' 	=> $this->data['u_name'],
			);
			// Update Data Pengajuan
			$this->db->update('tbl_pengajuan',$simpan_arr,array('id'=>$setoran));

			$data = array(			
				'tgl_transaksi'			=> $this->input->post('tgl_transaksi'),
				'anggota_id'			=> $get_setoran['anggota_id'],
				'jenis_id'				=> 32,
				'jumlah'				=>	str_replace(',', '', $this->input->post('jumlah')),
				'keterangan'			=> $this->input->post('ket'),
				'akun' 					=> 'Setoran',
				'dk'					=>	'D',
				'kas_id'				=> $this->input->post('kas_id'),
				'user_name'				=> $this->data['u_name'],
				'ajuan_id'				=> $get_setoran['ajuan_id']
			);
			return $this->db->insert('tbl_trans_sp', $data);				

		}else {

			$akun_id = $this->input->post('akun_id');

			if(!empty($akun_id)){
				foreach ($akun_id as $key => $row) {
					if($row != 0){
						$jumlah = str_replace(',', '', $this->input->post('jumlah'));
						$data = array(			
							'tgl_catat'			=>	$this->input->post('tgl_transaksi'),
							'jumlah'			=>	$jumlah,
							'keterangan'		=>	$this->input->post('ket'),
							'dk'				=>	'D',
							'akun'				=>	'Pemasukan',
							'untuk_kas_id'		=>	$this->input->post('kas_id'),
							'jns_trans'			=>	$row,
							'user_name'			=> $this->data['u_name'],
							'jenis'				=> $this->input->post('jenis')
							);
						$this->db->insert('tbl_trans_kas', $data);	
					}
				}

				return true;
			}

		}
	}

	public function update($id)
	{
		if(str_replace(',', '', $this->input->post('jumlah')) <= 0) {
		return FALSE;
	}
		$tanggal_u = date('Y-m-d H:i');
		$this->db->where('id', $id);
		return $this->db->update('tbl_trans_kas',array(
			'tgl_catat'				=>	$this->input->post('tgl_transaksi'),
			'jumlah'					=>	str_replace(',', '', $this->input->post('jumlah')),
			'keterangan'			=>	$this->input->post('ket'),
			'untuk_kas_id'			=>	$this->input->post('kas_id'),
			'jns_trans'				=>	$this->input->post('akun_id'),
			'update_data'			=> $tanggal_u,
			'user_name'				=> $this->data['u_name'],
			'jenis'					=> $this->input->post('jenis')
			));
	}

	public function delete($id)
	{
		return $this->db->delete('tbl_trans_kas', array('id' => $id)); 
	}

}