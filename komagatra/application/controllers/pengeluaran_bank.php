<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pengeluaran_bank extends OperatorController {

	public function __construct() {
		parent::__construct();	
		$this->load->helper('fungsi');
		$this->load->model('pengeluaran_bank_m');
		$this->load->model('pengeluaran_m');
		$this->load->model('pemasukan_m');
		$this->load->model('general_m');
	}	

	public function index() {
		$this->data['judul_browser'] = 'Transaksi Bank';
		$this->data['judul_utama'] = 'Transaksi Bank';
		$this->data['judul_sub'] = 'Pengeluaran Bank Tunai';

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

		$this->data['css_files'][] = base_url() . 'assets/extra/select2/select2.css';
		$this->data['js_files'][] = base_url() . 'assets/extra/select2/select2.min.js';

		$this->data['kas_id'] = $this->pengeluaran_bank_m->get_data_kas();
		$this->data['akun_id'] = $this->pengeluaran_bank_m->get_data_akun();

		$this->data['pengajuan_pinjaman'] = $this->pengeluaran_m->get_pengajuan(1);
		$this->data['pengajuan_penarikan'] = $this->pengeluaran_m->get_pengajuan(3,'Sukarela');

		$this->data['isi'] = $this->load->view('pengeluaran_bank_list_v', $this->data, TRUE);
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
		$kas_id = isset($_POST['kas_id']) ? $_POST['kas_id'] : '';
		$search = array('kode_transaksi' => $kode_transaksi, 
			'tgl_dari' => $tgl_dari, 
			'tgl_sampai' => $tgl_sampai,
			'kas_id' => $kas_id
		);
		$offset = ($offset-1)*$limit;
		$data   = $this->pengeluaran_bank_m->get_data_transaksi_ajax($offset,$limit,$search,$sort,$order);
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

	public function create() {
		if(!isset($_POST)) {
			show_404();
		}
		if($this->pengeluaran_bank_m->create()){
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
		if($this->pengeluaran_bank_m->update($id)) {
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
		if($this->pengeluaran_bank_m->delete($id))
		{
			echo json_encode(array('ok' => true, 'msg' => '<div class="text-green"><i class="fa fa-check"></i> Data berhasil dihapus </div>'));
		} else {
			echo json_encode(array('ok' => false, 'msg' => '<div class="text-red"><i class="fa fa-ban"></i> Maaf, Data gagal dihapus </div>'));
		}
	}


	function cetak_laporan() {

		$pengeluaran = $this->pengeluaran_bank_m->lap_data_pengeluaran();
		if($pengeluaran == FALSE) {
			redirect('pengeluaran_kas');
			exit();
		}

		$tgl_dari = $_REQUEST['tgl_dari']; 
		$tgl_sampai = $_REQUEST['tgl_sampai']; 

		$tgl_cetak = 'Periode '.jin_date_ina($tgl_dari).' - '.jin_date_ina($tgl_sampai);

		if($tgl_dari == '' && $tgl_sampai == ''){
			$tgl_cetak = 'Tanggal '.jin_date_ina(date('Y-m-d'));
		}

		$this->load->library('Pdf');
		$pdf = new Pdf('L', 'mm', 'A4', true, 'UTF-8', false);
		$pdf->set_nsi_header(TRUE);
		$pdf->AddPage('L');
		$html = '';
		$html .= '
		<style>
			.h_tengah {text-align: center;}
			.h_kiri {text-align: left;}
			.h_kanan {text-align: right;}
			.txt_judul {font-size: 12pt; font-weight: bold; padding-bottom: 12px;}
			.header_kolom {background-color: #cccccc; text-align: center; font-weight: bold;}
			.txt_content {font-size: 10pt; font-style: arial;}
		</style>
		'.$pdf->nsi_box($text = '<span class="txt_judul">Laporan Data Pengeluaran Kas<br></span>
			<span>'.$tgl_cetak.'</span>', $width = '100%', $spacing = '0', $padding = '1', $border = '0', $align = 'center').'
		<br /><br />
		<table width="100%" cellspacing="0" cellpadding="3" border="1" border-collapse= "collapse">
			<tr class="header_kolom">
				<th class="h_tengah" style="width:5%;" > No. </th>
				<th class="h_tengah" style="width:10%;"> No Transaksi</th>
				<th class="h_tengah" style="width:15%;"> Tanggal </th>
				<th class="h_tengah" style="width:50%;"> Uraian  </th>
				<th class="h_tengah" style="width:20%;"> Jumlah  </th>
			</tr>';

			$no =1;
			$jml_tot = 0;
			foreach ($pengeluaran as $row) {
				$tgl_bayar = explode(' ', $row->tgl_catat);
				$txt_tanggal = jin_date_ina($tgl_bayar[0],'p');

				$jml_tot += $row->jumlah;

				$status_kas = 'Menunggu Konfirmasi';
				if($row->status_kas == 1){
					$status_kas = 'Disetujui';
				}else if($row->status_kas == 2){
					$status_kas = 'Ditolak';
				}
				$html .= '
				<tr>
					<td class="h_tengah" >'.$no++.'</td>
					<td class="h_tengah"> '.'TKK'.sprintf('%05d', $row->id).'</td>
					<td class="h_tengah"> '.$txt_tanggal.'</td>
					<td class="h_kiri"> '.$row->keterangan.'</td>
					<td class="h_kanan"> '.number_format($row->jumlah).'</td>
				</tr>';
			}
			$html .= '
			<tr>
				<td colspan="4" class="h_tengah"><strong> Jumlah Total </strong></td>
				<td class="h_kanan"> <strong>'.number_format($jml_tot).'</strong></td>
			</tr>
		</table>';

		if($_REQUEST['kode_transaksi']){
			$html .= '<br /><br /><br /><br /><br />';
			$html .= '<table width="100%" border="0">';
			$html .= '<tr><td align="center">Dibuat Oleh</td><td align="center">Dibayar Oleh</td><td align="center">Yang Menerima</td></tr>';
			$html .= '</table>';
			$html .= '<br /><br /><br /><br /><br />';
			$html .= '<table width="100%" border="0">';
			$html .= '<tr><td align="center">'.$row->dibuat.'</td><td align="center">'.$row->dibayar.'</td><td align="center">'.$row->menerima.'</td></tr>';
			$html .= '</table>';
		}
		$pdf->nsi_html($html);
		$pdf->Output('trans_k'.date('Ymd_His') . '.pdf', 'I');
	} 

	public function update_cetak()
	{
		$id = $this->input->post('id');
		$id_txt = $this->input->post('id_txt');
		$dibayar = $this->input->post('dibayar');
		$dibuat = $this->input->post('dibuat');
		$menerima = $this->input->post('menerima');

		$data = array(
			'update_data' => date('Y-m-d H:i:s'),
			'dibayar' => $dibayar,
			'dibuat' => $dibuat,
			'menerima' => $menerima
		);
		if($this->db->update('tbl_trans_kas',$data,array('id'=>$id))){
			echo json_encode(array('ok'=>true,'id_txt'=>$id_txt));
		}else {
			echo json_encode(array('ok'=>false,'msg'=>'Proses Error Silahkan menghubungi Administrator'));
		}

	}

}