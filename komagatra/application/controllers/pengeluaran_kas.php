<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pengeluaran_kas extends OperatorController {

	public function __construct() {
		parent::__construct();	
		$this->load->helper('fungsi');
		$this->load->model('pengeluaran_m');
		$this->load->model('pemasukan_m');
		$this->load->model('general_m');
		$this->load->model('notif_m');
	}	

	public function index() {
		$this->data['judul_browser'] = 'Transaksi Kas';
		$this->data['judul_utama'] = 'Transaksi Kas';
		$this->data['judul_sub'] = 'Pengeluaran Kas Tunai';

		$this->data['css_files'][] = base_url() . 'assets/easyui/themes/default/easyui.css';
		$this->data['css_files'][] = base_url() . 'assets/easyui/themes/icon.css';
		$this->data['js_files'][] = base_url() . 'assets/easyui/jquery.easyui.min.js';

		#include tanggal
		$this->data['css_files'][] = base_url() . 'assets/extra/bootstrap_date_time/css/bootstrap-datetimepicker.min.css';
		$this->data['js_files'][] = base_url() . 'assets/extra/bootstrap_date_time/js/bootstrap-datetimepicker.min.js';
		$this->data['js_files'][] = base_url() . 'assets/extra/bootstrap_date_time/js/locales/bootstrap-datetimepicker.id.js';

		#include daterange
		$this->data['css_files'][] = base_url() . 'assets/theme_admin/css/daterangepicker/daterangepicker-bs3.css';
		$this->data['js_files'][] = base_url() . 'assets/theme_admin/js/plugins/daterangepicker/daterangepicker.js';

		//number_format
		$this->data['js_files'][] = base_url() . 'assets/extra/fungsi/number_format.js';

		// Bootstrap Select
		$this->data['css_files'][] = base_url() . 'assets/extra/select2/select2.css';
		$this->data['js_files'][] = base_url() . 'assets/extra/select2/select2.min.js';

		$this->data['kas_id'] = $this->pengeluaran_m->get_data_kas();
		$this->data['akun_id'] = $this->pengeluaran_m->get_data_akun();

		$this->data['pengajuan_pinjaman'] = $this->pengeluaran_m->get_pengajuan(1);
		$this->data['pengajuan_penarikan'] = $this->pengeluaran_m->get_pengajuan(3,'Sukarela');

		$this->data['isi'] = $this->load->view('pengeluaran_list_v', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);
	}

	function ajax_list() {
		/*Default request pager params dari jeasyUI*/
		$offset = isset($_POST['page']) ? intval($_POST['page']) : 1;
		$limit  = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
		$sort  = isset($_POST['sort']) ? $_POST['sort'] : 'tgl_transaksi';
		$order  = isset($_POST['order']) ? $_POST['order'] : 'desc';
		$kode_transaksi = isset($_POST['kode_transaksi']) ? $_POST['kode_transaksi'] : '';
		$tgl_dari = isset($_POST['tgl_dari']) ? $_POST['tgl_dari'] : $this->general_m->get_first_year();
		$tgl_sampai = isset($_POST['tgl_sampai']) ? $_POST['tgl_sampai'] : $this->general_m->get_last_year();
		$search = array('kode_transaksi' => $kode_transaksi, 
			'tgl_dari' => $tgl_dari, 
			'tgl_sampai' => $tgl_sampai);
		$offset = ($offset-1)*$limit;
		$data   = $this->pengeluaran_m->get_data_transaksi_ajax($offset,$limit,$search,$sort,$order);
		$i	= 0;
		$rows   = array(); 

		foreach ($data['data'] as $r) {

			$tgl_bayar = explode(' ', $r->tgl);
			$txt_tanggal = jin_date_ina($tgl_bayar[0]);
			// $txt_tanggal .= ' - ' . substr($tgl_bayar[1], 0, 5);	

			$nama_kas = $this->pemasukan_m->get_jenis_kas($r->dari_kas);	
			$nama_akun = $this->pemasukan_m->get_jenis_akun($r->transaksi);	

			$id_txt = 'TKK' . sprintf('%05d', $r->id) . '';
			$jenis = 'Lain-lain';
			if($r->tbl == 'A'){
				$id_txt = 'TPJ' . sprintf('%05d', $r->id) . '';
				$jenis = 'Pinjaman';
			}
			if($r->tbl == 'C'){
				$id_txt = 'TRK' . sprintf('%05d', $r->id) . '';
				$jenis = 'Penarikan Sukarela';
			}


			$rows[$i]['id'] = $r->id;
			$rows[$i]['id_txt'] = $id_txt;
			$rows[$i]['tgl_transaksi'] = $r->tgl;
			$rows[$i]['tgl_transaksi_txt'] = $txt_tanggal;	
			$rows[$i]['ket'] = $r->ket;
			$rows[$i]['jenis'] = $jenis;
			$rows[$i]['jumlah'] = number_format($r->kredit);
			$rows[$i]['user'] = $r->user;
			$rows[$i]['kas_id'] = $r->untuk_kas;
			$rows[$i]['kas_id_txt'] = $nama_kas->nama;
			$rows[$i]['akun_id_txt'] = $nama_akun->jns_trans;
			$rows[$i]['nama_member'] = $r->nama_member;
			$rows[$i]['nama_admin'] = $r->nama_admin;
			$rows[$i]['nama_operator'] = $r->nama_operator;
			$rows[$i]['tbl'] = $r->tbl;

			if($r->status == 1){
				$status_kas = '<label class="label label-success">Disetujui</label>';
			}else if($r->status == 2){
				$status_kas = '<label class="label label-warning">Ditolak</label>';
			}else {
				$status_kas = '<label class="label label-primary">Menunggu Konfirmasi</label>';
			}
			$rows[$i]['status'] = $status_kas;
			if($this->session->userdata('level') == 'admin'){
				$aksi_approve = "'".site_url('pengeluaran_kas/verify/'.$r->tbl.'/1/'.$r->id)."'";
				$aksi_reject = "'".site_url('pengeluaran_kas/verify/'.$r->tbl.'/2/'.$r->id)."'";
			
				$disabled = 'disabled';
				if($r->status == 0){
					$disabled = false;	
				}
				$btn_aksi = '<a onclick="verify('.$aksi_approve.')" class="btn btn-xs btn-success" '.$disabled.'><i class="fa fa-check-circle" ></i> Setujui</a> <a onclick="verify('.$aksi_reject.')" class="btn btn-xs btn-warning" '.$disabled.'><i class="fa fa-times-circle"></i>  Tolak</a>';
				$rows[$i]['aksi'] = $btn_aksi;
			}
			

			$i++;
		}
		//keys total & rows wajib bagi jEasyUI
		$result = array('total'=>$data['count'],'rows'=>$rows);
		echo json_encode($result); //return nya json
	}

	public function verify($tbl,$status,$id)
	{
		if(!empty($status) && !empty($id)){

			if($tbl == 'A'){
				$data = array('status'=>$status,'nama_admin'=>$this->data['u_name']);
				if($this->db->update('tbl_pinjaman_h',$data,array('id'=>$id))){

					$get_pinj = $this->db->select('tgl_pinjam,potongan,anggota_id,kas_id,ajuan_id,nama_member,nama_operator')->get_where('tbl_pinjaman_h',array('id'=>$id))->row_array();
					if($get_pinj['potongan'] > 0){
						$arr_simp = array(
									'tgl_transaksi' => date('Y-m-d H:i:s'),
									'anggota_id' 	=> $get_pinj['anggota_id'],
									'jenis_id' 		=> 41,
									'jumlah' 		=> $get_pinj['potongan'],
									'keterangan' 	=> 'Potongan dari Pinjaman',
									'akun' 			=> 'Setoran',
									'dk'			=> 'D',
									'kas_id'		=> $get_pinj['kas_id'],
									'user_name' 	=> $this->data['u_name'],
									'ajuan_id'		=> $get_pinj['ajuan_id'],
									'nama_member'	=> $get_pinj['nama_member'],
									'nama_operator'	=> $get_pinj['nama_operator'],
									'status'		=> 1
						);
						$this->db->insert('tbl_trans_sp',$arr_simp);
					}

					// Notif Member
					$tgl_notif_arr = explode(' ', $get_pinj['tgl_pinjam']);
					$pesan = 'Pengajuan Pinjaman Anda dengan nomor '.$get_pinj['ajuan_id']. ' telah disetujui dengan perkiraan cair pada tanggal '. jin_date_ina($tgl_notif_arr[0]);
					$this->notif_m->created_notif(2,$get_pinj['ajuan_id'],$pesan,$get_pinj['anggota_id']);

					echo json_encode(array('ok'=>true,'msg'=>'Proses Verifikasi berhasil'));
				}
			}

			if($tbl == 'D'){
				$data = array('status_kas'=>$status,'tgl_verif'=>date('Y-m-d H:i:s'),'nama_admin'=>$this->data['u_name'],'user_verif'=> $this->data['u_name']);
				if($this->db->update('tbl_trans_kas',$data,array('id'=>$id))){
					echo json_encode(array('ok'=>true,'msg'=>'Proses Verifikasi berhasil'));
				}
			}

			if($tbl == 'C'){
				$data = array('status'=>$status,'tgl_transaksi'=>date('Y-m-d H:i:s'),'nama_admin'=>$this->data['u_name']);
				if($this->db->update('tbl_trans_sp',$data,array('id'=>$id))){

					$get_pinj = $this->db->get_where('tbl_trans_sp',array('id'=>$id))->row_array();

					$tgl_notif_arr = explode(' ', $get_pinj['tgl_transaksi']);
					$pesan = 'Pengajuan Penarikan Simpanan Anda dengan nomor '.$get_pinj['ajuan_id']. ' telah disetujui dengan perkiraan cair pada tanggal '. jin_date_ina($tgl_notif_arr[0]);
					$this->notif_m->created_notif(2,$get_pinj['ajuan_id'],$pesan,$get_pinj['anggota_id'],2);

					echo json_encode(array('ok'=>true,'msg'=>'Proses Verifikasi berhasil'));
				}
			}

		}else {
			echo json_encode(array('ok'=>false,'msg'=>'Proses Error Silahkan menghubungi Administrator'));
		}
	}

	public function create() {
		if(!isset($_POST)) {
			show_404();
		}
		if($this->pengeluaran_m->create()){
			echo json_encode(array('ok' => true, 'msg' => '<div class="text-green"><i class="fa fa-check"></i> Data berhasil disimpan </div>'));
		}else
		{
			echo json_encode(array('ok' => false, 'msg' => '<div class="text-red"><i class="fa fa-ban"></i> Gagal menyimpan data, pastikan nilai lebih dari <strong>0 (NOL)</strong>. </div>'));
		}
	}

	public function update($id=null) {
		if(!isset($_POST)) {
			show_404();
		}
		if($this->pengeluaran_m->update($id)) {
			echo json_encode(array('ok' => true, 'msg' => '<div class="text-green"><i class="fa fa-check"></i> Data berhasil diubah </div>'));
		} else {
			echo json_encode(array('ok' => false, 'msg' => '<div class="text-red"><i class="fa fa-ban"></i>  Maaf, Data gagal diubah, pastikan nilai lebih dari <strong>0 (NOL)</strong>. </div>'));
		}

	}
	public function delete() {
		if(!isset($_POST))	 {
			show_404();
		}
		$id = intval(addslashes($_POST['id']));
		if($this->pengeluaran_m->delete($id))
		{
			echo json_encode(array('ok' => true, 'msg' => '<div class="text-green"><i class="fa fa-check"></i> Data berhasil dihapus </div>'));
		} else {
			echo json_encode(array('ok' => false, 'msg' => '<div class="text-red"><i class="fa fa-ban"></i> Maaf, Data gagal dihapus </div>'));
		}
	}




}