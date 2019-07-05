<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bayar extends OperatorController {
	public function __construct() {
		parent::__construct();	
		$this->load->helper('fungsi');
		$this->load->model('bayar_m');
		$this->load->model('general_m');
		$this->load->model('angsuran_lunas_m');
	}	

	public function index() {
		$this->data['judul_browser'] = 'Pinjaman';
		$this->data['judul_utama'] = 'Transaksi';
		$this->data['judul_sub'] = 'Pembayaran Angsuran';

		$this->data['css_files'][] = base_url() . 'assets/easyui/themes/default/easyui.css';
		$this->data['css_files'][] = base_url() . 'assets/easyui/themes/icon.css';
		$this->data['js_files'][] = base_url() . 'assets/easyui/jquery.easyui.min.js';
		//$this->data['js_files'][] = base_url() . 'assets/easyui/datagrid-detailview.js';

		#include tanggal
		$this->data['css_files'][] = base_url() . 'assets/extra/bootstrap_date_time/css/bootstrap-datetimepicker.min.css';
		$this->data['js_files'][] = base_url() . 'assets/extra/bootstrap_date_time/js/bootstrap-datetimepicker.min.js';
		$this->data['js_files'][] = base_url() . 'assets/extra/bootstrap_date_time/js/locales/bootstrap-datetimepicker.id.js';
		#include seach
		$this->data['css_files'][] = base_url() . 'assets/theme_admin/css/daterangepicker/daterangepicker-bs3.css';
		$this->data['js_files'][] = base_url() . 'assets/theme_admin/js/plugins/daterangepicker/daterangepicker.js';

		$this->data['js_files'][] = base_url() . 'assets/extra/fungsi/number_format.js';
		$this->data['kas_id'] = $this->angsuran_lunas_m->get_data_kas();

		$this->data['isi'] = $this->load->view('bayar_list_v', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);
	}


	function ajax_list() {
		$this->load->library('finance');
		$this->load->model('bunga_m');
		/*Default request pager params dari jeasyUI*/
		$offset = isset($_POST['page']) ? intval($_POST['page']) : 1;
		$limit  = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
		$sort  = isset($_POST['sort']) ? $_POST['sort'] : 'tgl_pinjam';
		$order  = isset($_POST['order']) ? $_POST['order'] : 'desc';
		$kode_transaksi = isset($_POST['kode_transaksi']) ? $_POST['kode_transaksi'] : '';
		$cari_nama = isset($_POST['cari_nama']) ? $_POST['cari_nama'] : '';
		$tgl_dari = isset($_POST['tgl_dari']) ? $_POST['tgl_dari'] : '';
		$tgl_sampai = isset($_POST['tgl_sampai']) ? $_POST['tgl_sampai'] : '';
		$search = array(
			'kode_transaksi' => $kode_transaksi, 
			'cari_nama' => $cari_nama, 
			'tgl_dari' => $tgl_dari, 
			'tgl_sampai' => $tgl_sampai
			);
		$offset = ($offset-1)*$limit;
		$data   = $this->bayar_m->get_data_transaksi_ajax($offset,$limit,$search,$sort,$order);
		$i	= 0;
		$rows   = array(); 

		$data_bunga_arr = $this->bunga_m->get_key_val();

		foreach ($data['data'] as $r) {
			$tgl_pinjam = explode(' ', $r->tgl_pinjam);
			$txt_tanggal = jin_date_ina($tgl_pinjam[0],'p');		

			//array keys ini = attribute 'field' di view nya
			$anggota = $this->general_m->get_data_anggota($r->anggota_id);   
			$jenis = '';
			if(!empty($r->ajuan_id)){
				$jenis = $this->db->select('jenis')->get_where('tbl_pengajuan',array('ajuan_id'=>$r->ajuan_id))->row_array();
				$jenis = $jenis['jenis'];
			}
			

			$rows[$i]['id'] = $r->id;
			$rows[$i]['id_txt'] ='PJ' . sprintf('%05d', $r->id) . '';

			$rows[$i]['tgl_pinjam_txt'] = $txt_tanggal;
			//$rows[$i]['anggota_id'] ='AG' . sprintf('%04d', $r->anggota_id) . '';
			$rows[$i]['anggota_id'] = $anggota->anggota_id;
			$rows[$i]['anggota_id_txt'] = $anggota->nama.' - '.$this->general_m->get_departement($anggota->departement);
			$rows[$i]['jenis'] = $jenis;
			$rows[$i]['lama_angsuran_txt'] = $r->lama_angsuran.' Bulan';

			$rows[$i]['jumlah'] = number_format($r->jumlah);

			$ags_pokok = $r->pokok_angsuran;
			$bunga = $r->bunga_pinjaman;
			$angsuran = $r->ags_per_bulan;

			$jenis_pinjaman = $this->db->get_where('jns_pinjaman',array('jns_pinjaman'=>$jenis))->row_array();
			if($jenis_pinjaman['anuitas'] == 1){
				$ags_pokok = $this->finance->ppmt(0.10/12, $r->bln_sudah_angsur + 1, $r->lama_angsuran, -$r->jumlah, 0, false);
				$bunga = $this->finance->ipmt(0.10/12, $r->bln_sudah_angsur + 1, $r->lama_angsuran, -$r->jumlah, 0, false);
				$angsuran = $ags_pokok + $bunga;
			}

			$rows[$i]['ags_pokok'] = number_format($ags_pokok);

			$rows[$i]['bunga'] = number_format($bunga);
			$rows[$i]['biaya_adm'] = number_format($r->biaya_adm);

			$rows[$i]['angsuran_bln'] = number_format(nsi_round($angsuran));
			$rows[$i]['angsuran'] = $angsuran;

			// Jatuh Tempo
			$sdh_ags_ke = $r->bln_sudah_angsur;
			$ags_ke = $r->bln_sudah_angsur + 1;

			$rows[$i]['angsuran_ke'] = $ags_ke;
			$rows[$i]['total_angsuran'] = $r->lama_angsuran;
			

			$denda_hari = $data_bunga_arr['denda_hari'];
			$tgl_pinjam = substr($r->tgl_pinjam, 0, 7) . '-01';
			$tgl_tempo = date('Y-m-d', strtotime("+".$ags_ke." months", strtotime($tgl_pinjam)));
			$tgl_tempo = substr($tgl_tempo, 0, 7) . '-' . sprintf("%02d", $denda_hari);
			$txt_status = '';
			$txt_status_tip = 'Ags Ke: ' . $ags_ke . ' Tempo: ' . $tgl_tempo;
			if($tgl_tempo < date('Y-m-d')) {
				$rows[$i]['merah'] = 1;
				$txt_status .= '<span title="'.$txt_status_tip.'" class="text-red"><i class="fa fa-warning"></i></span>';
			} else {
				$rows[$i]['merah'] = 0;
				$txt_status .= '<span title="'.$txt_status_tip.'" class="text-green"><i class="fa fa-check-circle" title="'.$txt_status_tip.'"></i></span>';
			}
			//$rows[$i]['status'] = $txt_status;

			$rows[$i]['bayar'] = '<br><p>'.$txt_status.' 
			<a href="'.site_url('angsuran').'/index/' . $r->id . '" title="Bayar Angsuran"> <i class="fa fa-money"></i> Bayar </a></p>';
			$i++;
		}
		//keys total & rows wajib bagi jEasyUI
		$result = array('total'=>$data['count'],'rows'=>$rows);
		echo json_encode($result); //return nya json
	}


	public function lunasAll()
	{

		$output = array('output'=>false);

		$data = $this->input->post('data');
		$tgl_transaksi = $this->input->post('tgl_transaksi');
		$kas = $this->input->post('kas');
		$ket = $this->input->post('ket');
		$check_lunas = $this->input->post('check_lunas');


		if($check_lunas == 'lunas'){
			foreach ($data as $key => $value) {
				$arr_data = array(
					'tgl_bayar' => date('Y-m-d H:i:s',strtotime($tgl_transaksi)),
					'pinjam_id' => $value['id'],
					'angsuran_ke' => $value['angsuran_ke'],
					'jumlah_bayar' => $value['angsuran'] * ($value['total_angsuran'] - $value['angsuran_ke'] + 1),
					'ket_bayar' => 'Pelunasan',
					'dk' => 'D',
					'kas_id' => $kas,
					'jns_trans' => 48,
					'user_name' => $this->data['u_name'],
					'keterangan' => $ket
				);

				if($this->db->insert('tbl_pinjaman_d',$arr_data)){
					$this->db->update('tbl_pinjaman_h',array('lunas'=>'Lunas','update_data'=>date('Y-m-d H:i:s')),array('id'=>$value['id']));
					$output = array('output'=>true);
				}
			}
		}else {

			foreach ($data as $key => $value) {
				$arr_data = array(
					'tgl_bayar' => date('Y-m-d H:i:s',strtotime($tgl_transaksi)),
					'pinjam_id' => $value['id'],
					'angsuran_ke' => $value['angsuran_ke'],
					'jumlah_bayar' => $value['angsuran'],
					'ket_bayar' => 'Angsuran',
					'dk' => 'D',
					'kas_id' => $kas,
					'jns_trans' => 48,
					'user_name' => $this->data['u_name'],
					'keterangan' => $ket
				);

				if($this->db->insert('tbl_pinjaman_d',$arr_data)){

					$check_pinjam = $this->db->get_where('tbl_pinjaman_d',array('pinjam_id'=>$value['id']))->num_rows();
					if($check_pinjam == $value['total_angsuran']){
						$this->db->update('tbl_pinjaman_h',array('lunas'=>'Lunas','update_data'=>date('Y-m-d H:i:s')),array('id'=>$value['id']));
					}else {
						$this->db->update('tbl_pinjaman_h',array('update_data'=>date('Y-m-d H:i:s')),array('id'=>$value['id']));
					}

					$output = array('output'=>true);
				}
			}

		}

		echo json_encode($output);
	}

}
