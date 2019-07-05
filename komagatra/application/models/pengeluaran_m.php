<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pengeluaran_m extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

	#panggil data kas
	function get_data_kas() {
		$this->db->select('*');
		$this->db->from('nama_kas_tbl');
		$this->db->where('aktif', 'Y');
		$this->db->where('type','kas');
		$this->db->where('tmpl_pengeluaran', 'Y');
		$this->db->order_by('id', 'ASC');
		$query = $this->db->get();
		if($query->num_rows()>0){
			$out = $query->result();
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
		$this->db->where('pengeluaran', 'Y');
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
	function lap_data_pengeluaran() {
		$kode_transaksi = isset($_REQUEST['kode_transaksi']) ? $_REQUEST['kode_transaksi'] : '';
		$tgl_dari = isset($_REQUEST['tgl_dari']) ? $_REQUEST['tgl_dari'] : '';
		$tgl_sampai = isset($_REQUEST['tgl_sampai']) ? $_REQUEST['tgl_sampai'] : '';
		$sql = '';
		$sql = " SELECT tbl_trans_kas.*, jns_akun.jns_trans AS nama_trans  
		FROM tbl_trans_kas 
		LEFT JOIN nama_kas_tbl ON nama_kas_tbl.id = tbl_trans_kas.dari_kas_id
		LEFT JOIN jns_akun ON jns_akun.id = tbl_trans_kas.jns_trans
		WHERE tbl_trans_kas.akun='Pengeluaran' AND nama_kas_tbl.type = 'kas' ";
		$q = array('kode_transaksi' => $kode_transaksi, 
			'tgl_dari' => $tgl_dari, 
			'tgl_sampai' => $tgl_sampai);
		if(is_array($q)) {
			if($q['kode_transaksi'] != '') {
				$q['kode_transaksi'] = str_replace('TKK', '', $q['kode_transaksi']);
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

	//hitung jumlah total 
	function get_jml_pengeluaran() {
		$this->db->select('SUM(jumlah) AS jml_total');
		$this->db->from('tbl_trans_kas');
		$this->db->where('akun','Pengeluaran');
		$query = $this->db->get();
		return $query->row();
	}

	//panggil data simpanan untuk esyui
	function get_data_transaksi_ajax($offset, $limit, $q='', $sort, $order) {

		$this->load->model('general_m');
		$result['count'] = $this->general_m->kocak_model('kas',0,$q)->num_rows();
		$result['data'] = $this->general_m->kocak_model('kas',0,$q,$sort,$order,$limit,$offset)->result();

		return $result;
	}



	public function create() {
		if(str_replace(',', '', $this->input->post('jumlah')) <= 0) {
			return FALSE;
		}

		$jenis = $this->input->post('jenis');
		$nama_member = $this->input->post('nama_member');
		if($jenis == 'Pinjaman'){

			$pengajuan_pinjaman = $this->input->post('pengajuan_pinjaman');
			$simpan_arr = array(			
				'status'			=>	1,
				'keterangan'			=> $this->input->post('ket'),
				'tgl_cair'		=>	$this->input->post('tgl_transaksi'),
				'tgl_update'	=> date('Y-m-d H:i:s'),
				'user_verif' 	=> $this->data['u_name'],
			);
			// Update Data Pengajuan
			$this->db->update('tbl_pengajuan',$simpan_arr,array('id'=>$pengajuan_pinjaman));

			// Get Data Pengajuan
			$get_pengajuan = $this->db->select('potongan,uang_diterima,anggota_id,lama_ags,ajuan_id,type,jenis')->get_where('tbl_pengajuan',array('id'=>$pengajuan_pinjaman))->row_array();
			$bunga = $this->db->get_where('suku_bunga',array('opsi_key'=>'bg_pinjam'))->row_array();

			$jumlah = str_replace(',', '', $this->input->post('jumlah'));
			$potongan = 0;
			if($get_pengajuan['type'] == 1 && $get_pengajuan['jenis'] == 'Pinjaman Jangka Pendek' || $get_pengajuan['jenis'] == 'Pinjaman Cashbone'){
				$jumlah = $get_pengajuan['uang_diterima'];
				$potongan = $get_pengajuan['potongan'];

			}
			$data = array(			
				'tgl_pinjam'			=> $this->input->post('tgl_transaksi'),
				'anggota_id'			=> $get_pengajuan['anggota_id'],
				'lama_angsuran'			=> $get_pengajuan['lama_ags'],
				'jumlah'				=>	$jumlah,
				'potongan'				=>	$potongan,
				'bunga'					=> $bunga['opsi_val'],
				'lunas' 				=> 'Belum',
				'dk'					=>	'K',
				'kas_id'				=> $this->input->post('kas_id'),
				'jns_trans'				=> 7,
				'user_name'				=> $this->data['u_name'],
				'keterangan'			=> $this->input->post('ket'),
				'type'					=> 2,
				'ajuan_id'				=> $get_pengajuan['ajuan_id'],
				'nama_member'			=> $nama_member,
				'nama_operator'			=> $this->data['u_name'],
				'status'				=> 0
			);
			return $this->db->insert('tbl_pinjaman_h', $data);			

		}else if($jenis == 'Penarikan Sukarela'){

			$pengajuan_penarikan = $this->input->post('pengajuan_penarikan');
			$get_penarikan = $this->db->select('anggota_id,lama_ags,ajuan_id')->get_where('tbl_pengajuan',array('id'=>$pengajuan_penarikan))->row_array();

			$simpan_arr = array(			
				'status'			=>	1,
				'keterangan'			=> $this->input->post('ket'),
				'tgl_cair'		=>	$this->input->post('tgl_transaksi'),
				'tgl_update'	=> date('Y-m-d H:i:s'),
				'user_verif' 	=> $this->data['u_name'],
			);
			// Update Data Pengajuan
			$this->db->update('tbl_pengajuan',$simpan_arr,array('id'=>$pengajuan_penarikan));


			$data = array(			
				'tgl_transaksi'			=> $this->input->post('tgl_transaksi'),
				'anggota_id'			=> $get_penarikan['anggota_id'],
				'jenis_id'				=> 32,
				'jumlah'				=>	str_replace(',', '', $this->input->post('jumlah')),
				'keterangan'			=> $this->input->post('ket'),
				'akun' 					=> 'Penarikan',
				'dk'					=>	'K',
				'kas_id'				=> $this->input->post('kas_id'),
				'user_name'				=> $this->data['u_name'],
				'ajuan_id'				=> $get_penarikan['ajuan_id'],
				'nama_member'			=> $nama_member,
				'nama_operator'			=> $this->data['u_name'],
				'status'				=> 0
			);
			return $this->db->insert('tbl_trans_sp', $data);	
		}else {

			$status_kas = 0;
			if($this->session->userdata('level') == 'admin'){
				$status_kas = 1;
			}

			$data = array(			
				'tgl_catat'				=>	$this->input->post('tgl_transaksi'),
				'jumlah'					=>	str_replace(',', '', $this->input->post('jumlah')),
				'keterangan'			=>	$this->input->post('ket'),
				'dk'						=>	'K',
				'akun'					=>	'Pengeluaran',
				'dari_kas_id'			=>	$this->input->post('kas_id'),
				'jns_trans'				=>	$this->input->post('akun_id'),
				'user_name'				=> $this->data['u_name'],
				'status_kas'			=> $status_kas,
				'jenis'					=> $jenis,
				'nama_member'			=> $nama_member,
				'nama_operator'			=> $this->data['u_name']
			);

			return $this->db->insert('tbl_trans_kas', $data);
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
			'dari_kas_id'			=>	$this->input->post('kas_id'),
			'jns_trans'				=>	$this->input->post('akun_id'),
			'update_data'			=> $tanggal_u,
			'user_name'				=> $this->data['u_name'],
			'jenis'					=> $this->input->post('jenis')
			));
	}

	public function delete($id){
		return $this->db->delete('tbl_trans_kas', array('id' => $id)); 
	}


	function get_pengajuan($type,$jenis=false)
	{
		$this->db->select('tbl_pengajuan.id,tbl_pengajuan.ajuan_id,nominal,tbl_anggota.nama,tbl_pengajuan.tgl_cair');
		$this->db->join('tbl_anggota','tbl_anggota.id = tbl_pengajuan.anggota_id','left');
		$this->db->where(array('tbl_pengajuan.type'=>$type,'tbl_pengajuan.status'=>1));
		if($jenis){
			$this->db->where('jenis',$jenis);
		}
		$this->db->order_by('tgl_update','desc');
		$query = $this->db->get('tbl_pengajuan');

		return $query->result_array();

	}

	function get_trans_kas($id)
	{

		$output = false;
		$this->db->select('status_kas');
		$this->db->from('tbl_trans_kas');
		$this->db->where('id',$id);
		$query = $this->db->get();
		if($query->num_rows() > 0){
			$output = $query->row();
		}
		return $output;
	}


}