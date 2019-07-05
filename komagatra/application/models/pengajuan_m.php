<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pengajuan_m extends CI_Model {

	public function __construct() {
		parent::__construct();
		$this->load->model('general_m');
		$this->load->model('notif_m');
	}

	function pengajuan_simpanan_aksi() {
		$status = $this->input->post('aksi');
		$id = $this->input->post('id');
		$kas_id = $this->input->post('kas_id');
		$alasan = $this->input->post('alasan');
		$status_txt = 0;
		$tgl_cair = '';
		$tanggal_u = date('Y-m-d H:i');
		$get_pengajuan = $this->db->get_where('tbl_pengajuan',array('id'=>$id))->row();
		switch ($status) {
			case 'Hapus':
				
				$this->db->delete('tbl_trans_sp', array('ajuan_id' => $get_pengajuan->ajuan_id));
				return $this->db->delete('tbl_pengajuan', array('id' => $id));
			break;
			case 'Setuju':
				$status_txt = 1;
				// $this->db->trans_start();
				$simpan_arr = array(			
					'status'			=>	$status_txt,
					'alasan'			=>	$alasan,
					'tgl_update'	=> $tanggal_u,
					'tgl_cair'		=> date('Y-m-d',strtotime($tanggal_u)),
					'user_verif' 	=> $this->data['u_name']
				);


				// $this->load->model('simpanan_m');

				// $jenis = $this->db->select('id')->get_where('jns_simpan',array('jns_simpan'=>$this->input->post('jenis')))->row_array()['id'];
				// $kd_transaksi = $this->general_m->get_kode_transaksi('C');
				
				// $jumlah_simpanan = $get_pengajuan->nominal;
				// $angsuran_ke = 0;
				// if($get_pengajuan->lama_ags > 0){
				// 	$angsuran_ke = 1;
				// 	$jumlah_simpanan = $jumlah_simpanan / $get_pengajuan->lama_ags;
				// }else {

				// }
				// $data_pinjaman = array(
				// 	'tgl_transaksi' => date('Y-m-d H:i:s'),
				// 	'anggota_id' => $get_pengajuan->anggota_id,
				// 	'jumlah' => $jumlah_simpanan,
				// 	'dk' => 'D',
				// 	'akun' => 'Setoran',
				// 	'kas_id' => $kas_id,
				// 	'jenis_id' => $jenis,
				// 	'keterangan' => $alasan,
				// 	'user_name' => $this->data['u_name'],
				// 	'ajuan_id' => $get_pengajuan->ajuan_id,
				// 	'angsuran_ke' => $angsuran_ke,
				// 	'kd_transaksi' => $kd_transaksi
				// );
				// $this->db->insert('tbl_trans_sp',$data_pinjaman);

				$tgl_notif_arr = explode(' ', date('Y-m-d H:i:s'));
				$pesan = 'Pengajuan Simpanan Anda dengan nomor '.$get_pengajuan->ajuan_id. ' telah disetujui  pada tanggal '. jin_date_ina($tgl_notif_arr[0]);
				$this->notif_m->created_notif(2,$get_pengajuan->ajuan_id,$pesan,$get_pengajuan->anggota_id,2);


				// $this->db->trans_end();
			break;
			case 'Bayar':

				$simpan_arr = array(			
					'tgl_update'	=> $tanggal_u,
				);

				$jenis = 41;
				if($this->input->post('jenis') == 'Sukarela'){
					$jenis = 32;
				}

				$data_pinjaman = array(
					'tgl_transaksi' => date('Y-m-d H:i:s'),
					'anggota_id' => $get_pengajuan->anggota_id,
					'jumlah' => $this->input->post('jumlah_simp'),
					'dk' => 'D',
					'akun' => 'Setoran',
					'kas_id' => $this->input->post('kas_id'),
					'jenis_id' => $jenis,
					'keterangan' => $alasan,
					'user_name' => $this->data['u_name'],
					'ajuan_id' => $get_pengajuan->ajuan_id,
					'angsuran_ke' => $this->input->post('angsuran_ke')
				);
				$this->db->insert('tbl_trans_sp',$data_pinjaman);
				// $this->db->trans_end();
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











}
?>

