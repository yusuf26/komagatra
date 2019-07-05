<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pemasukan_m extends CI_Model {

	public function __construct(){
		parent::__construct();
	}

	#panggil data kas
	function get_data_kas() {
		$this->db->select('*');
		$this->db->from('nama_kas_tbl');
		$this->db->where('aktif', 'Y');
		$this->db->where('type','kas');
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

	function get_all_kas() {
		$this->db->select('*');
		$this->db->from('nama_kas_tbl');
		$this->db->where('aktif', 'Y');
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

	function get_data_akun_2() {
		$this->db->select('id,jns_trans');
		$this->db->from('jns_akun');
		$this->db->where('aktif', 'Y');
		if($this->input->get('akun') != 'false'){
			$this->db->where('id',$this->input->get('akun'));
		}
		$this->db->order_by('id', 'ASC');
		$query = $this->db->get();
		if($query->num_rows()>0){
			$out = $query->result_array();
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
		WHERE akun='Pemasukan' AND type = 'kas' ";
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
		$result['count'] = $this->general_m->kocak_model('kas',1,$q)->num_rows();
		$result['data'] = $this->general_m->kocak_model('kas',1,$q,$sort,$order,$limit,$offset)->result();

		return $result;


	}

	public function create() {
		$this->load->model('general_m');
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



	function get_angsuran()
	{
		$query = $this->db->select('tp.id, ta.nama, tp.ags_per_bulan')
						->join('tbl_anggota ta','ta.id = tp.anggota_id','left')
						->where('tp.lunas','Belum')
						->where('tp.type',2)
						->order_by('tgl_pinjam','desc')
						->get('v_hitung_pinjaman tp')
						->result_array();
		return $query;

	}


	public function getTransAfterSubmit($kd_transaksi)
	{
		$output = false;
		$this->db->select('a.id,a.kd_transaksi,a.kredit,a.debet,a.status,b.jns_trans as transaksi');
		$this->db->join('jns_akun b','a.transaksi = b.id','left');
		$this->db->where('kd_transaksi',$kd_transaksi);
		$query = $this->db->get('v_transaksi a');
		if($query->num_rows() > 0){
			$output = $query->result_array();
			

		}

		return $output;
	}


	function PostAngsuran($tgl,$kd_transaksi,$id,$kas_id,$username,$ket,$lunas=false)
	{

		$jns_trans = $this->input->post('akun_1');
		$dk = $this->input->post('dk_1');
		$jumlah = str_replace('.', '', $this->input->post('jumlah_1'));

		$pinjam = $this->db->select('bln_sudah_angsur,lama_angsuran')->get_where('v_hitung_pinjaman',array('id'=>$id));
		$data_pinjam = $pinjam->row_array();
		$ket_bayar = 'Angsuran';
		if($lunas){
			$ket_bayar = 'Pelunasan';
		}
		$arr_data = array(
			'tgl_bayar' => date('Y-m-d H:i:s',strtotime($tgl)),
			'pinjam_id' => $id,
			'angsuran_ke' => $data_pinjam['bln_sudah_angsur'] + 1,
			'jumlah_bayar' => $jumlah,
			'ket_bayar' => $ket_bayar,
			'dk' => $dk,
			'kas_id' => $kas_id,
			'jns_trans' =>$jns_trans,
			'user_name' => $username,
			'keterangan' => $ket,
			'status'	=> 1,
			'kd_transaksi' => $kd_transaksi
		);

		if($this->db->insert('tbl_pinjaman_d',$arr_data)){

			if($lunas){
				$this->db->update('tbl_pinjaman_h',array('lunas'=>'Lunas','update_data'=>date('Y-m-d H:i:s')),array('id'=>$id));
			}else {
				$check_pinjam = $this->db->get_where('tbl_pinjaman_d',array('pinjam_id'=>$id))->num_rows();
				if($check_pinjam == $data_pinjam['lama_angsuran']){
					$this->db->update('tbl_pinjaman_h',array('lunas'=>'Lunas','update_data'=>date('Y-m-d H:i:s')),array('id'=>$id));
				}else {
					$this->db->update('tbl_pinjaman_h',array('update_data'=>date('Y-m-d H:i:s')),array('id'=>$id));
				}	
			}
			
		}
	}

	function angsur_lunas($id,$jumlah,$lama_angsuran)
	{	
		$output = array('angsuran'=>0,'angsuran_ke'=>0);
		$this->load->library('finance');
		$row = $this->db->select('id,angsuran_ke')->get_where('tbl_pinjaman_d',array('pinjam_id'=>$id));

		if($row->num_rows() > 0){
			$total_bayar = 0;
			foreach ($row->result_array() as $key => $value) {
				$total_bayar += $this->finance->ppmt(0.10/12, $value['angsuran_ke'], $lama_angsuran, - $jumlah, 0, false);
			}
			$angsuran = $jumlah - $total_bayar;
			$output = array('angsuran'=>$angsuran,'angsuran_ke'=>$row->num_rows());
		}else {
			$angsuran = $jumlah;
			$output = array('angsuran'=>$angsuran,'angsuran_ke'=>0);
		}
		return $output;
	}


	function PostSimpanan($tgl,$kd_transaksi,$id,$kas_id,$username,$keterangan,$type=false)
	{
		$jns_trans = $this->input->post('akun_1');
		$dk = $this->input->post('dk_1');
		$jumlah = str_replace('.', '', $this->input->post('jumlah_1'));

		$get_pengajuan = $this->db->get_where('tbl_pengajuan',array('ajuan_id'=>$id))->row();

		$dk = 'D';
		$akun = 'Setoran';
		if($type){
			$dk = 'K';
			$akun = 'Penarikan';
		}
		$data_pinjaman = array(
			'tgl_transaksi' => date('Y-m-d H:i:s'),
			'anggota_id' => $get_pengajuan->anggota_id,
			'jumlah' => $jumlah,
			'dk' => $dk,
			'akun' => $akun,
			'kas_id' => $kas_id,
			'jenis_id' => $jns_trans,
			'keterangan' => $keterangan,
			'user_name' => $username,
			'ajuan_id' => $id,
			'angsuran_ke' => 0,
			'kd_transaksi' => $kd_transaksi,
			'status' => 0
		);
		$this->db->insert('tbl_trans_sp',$data_pinjaman);


		$simpan_arr = array(			
			'status'			=>	3,
			'alasan'			=>	$keterangan,
			'tgl_update'	=> date('Y-m-d H:i:s'),
			'tgl_cair'		=> date('Y-m-d',strtotime($tgl)),
			'user_verif' 	=> $username
		);

		$this->db->where('ajuan_id', $id);
		return $this->db->update('tbl_pengajuan',$simpan_arr);
	}





















}