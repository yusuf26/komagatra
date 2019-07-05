<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pengajuan_penarikan_simpanan extends OperatorController {
	public function __construct() {
		parent::__construct();	
		$this->load->helper('fungsi');
		$this->load->model('simpanan_m');
		$this->load->model('general_m');
	}	

	public function index() {
		$this->data['judul_browser'] = 'Pengajuan Penarikan Simpanan';
		$this->data['judul_utama'] = 'Pengajuan Penarikan Simpanan';
		$this->data['judul_sub'] = 'Pengajuan Penarikan Simpanan';

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

		$this->data['kas_id'] = $this->simpanan_m->get_data_kas();
		$this->data['jenis_id'] = $this->general_m->get_id_simpanan();

		$this->data['isi'] = $this->load->view('penarikan_simpanan_list_v', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);
	}

	function ajax_list() {
		/*Default request pager params dari jeasyUI*/
		$offset = isset($_POST['page']) ? intval($_POST['page']) : 1;
		$limit  = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
		$sort  = isset($_POST['sort']) ? $_POST['sort'] : 'tgl_transaksi';
		$order  = isset($_POST['order']) ? $_POST['order'] : 'desc';
		$kode_transaksi = isset($_POST['kode_transaksi']) ? $_POST['kode_transaksi'] : '';
		$cari_simpanan = isset($_POST['cari_simpanan']) ? $_POST['cari_simpanan'] : '';
		$cari_status = isset($_POST['cari_status']) ? $_POST['cari_status'] : '';
		$tgl_dari = isset($_POST['tgl_dari']) ? $_POST['tgl_dari'] : '';
		$tgl_sampai = isset($_POST['tgl_sampai']) ? $_POST['tgl_sampai'] : '';
		$search = array('kode_transaksi' => $kode_transaksi, 
			'cari_simpanan' => $cari_simpanan,
			'cari_status' => $cari_status,
			'tgl_dari' => $tgl_dari, 
			'tgl_sampai' => $tgl_sampai);
		$offset = ($offset-1)*$limit;
		$data   = $this->simpanan_m->get_data_penarikan_simpanan($offset,$limit,$search,$sort,$order);
		$i	= 0;
		$rows   = array(); 

		foreach ($data['data'] as $r) {
			$tgl_bayar = explode(' ', $r->tgl_input);
			$txt_tanggal = jin_date_ina($tgl_bayar[0],'full',false,true);
			// $txt_tanggal .= ' - ' . substr($tgl_bayar[1], 0, 5);		

			//array keys ini = attribute 'field' di view nya
			$anggota = $this->general_m->get_data_anggota($r->anggota_id);  
			$nama = '<b>'.$anggota->nama.'</b><br />'.$this->general_m->get_departement($anggota->departement);

			$status = '<label class="label label-primary">Menunggu Konfirmasi</label>';
			if($r->status == 1){
				$status = '<label class="label label-success">Disetujui</label>';
			}else if($r->status == 2){
				$status = '<label class="label label-danger">Ditolak</label>';
			}

			$rows[$i]['id'] = $r->id;
			$rows[$i]['id_txt'] = $r->ajuan_id;
			$rows[$i]['tgl_transaksi'] = $r->tgl_input;
			$rows[$i]['tgl_transaksi_txt'] = $txt_tanggal;
			$rows[$i]['anggota_id'] = $r->anggota_id;
			$rows[$i]['anggota_id_txt'] = $anggota->identitas;
			$rows[$i]['nama'] = $nama;
			$rows[$i]['jenis_id_txt'] =$r->jenis;
			$rows[$i]['jumlah'] = number_format($r->nominal);
			$rows[$i]['ket'] = $r->keterangan;

			$rows[$i]['tgl_cair'] = '-';
			if($r->status == 1){
				$rows[$i]['tgl_cair'] = jin_date_ina($r->tgl_cair);
			}

			$rows[$i]['status'] = $status;
			$rows[$i]['status_id'] = $r->status;
			$rows[$i]['nota'] = '<p></p><p>
			<a href="'.site_url('cetak_pengajuan').'/cetak/' . $r->id . '"  title="Cetak Bukti Transaksi" target="_blank"> <i class="glyphicon glyphicon-print"></i> Nota </a></p>';
			$i++;
		}
		//keys total & rows wajib bagi jEasyUI
		$result = array('total'=>$data['count'],'rows'=>$rows);
		echo json_encode($result); //return nya json
	}


	public function tambah()
	{
		$this->load->model('pinjaman_m');
		$this->load->model('simpanan_m');
		$this->load->model('general_m');
		$this->data['judul_browser'] = 'Tambah Pengajuan Penarikan Simpanan';
		$this->data['judul_utama'] = 'Pengajuan Penarikan Simpanan';
		$this->data['judul_sub'] = 'Tambah Pengajuan Penarikan Simpanan';


		//select2
		$this->data['css_files'][] = base_url() . 'assets/extra/select2/select2.css';
		$this->data['js_files'][] = base_url() . 'assets/extra/select2/select2.min.js';

		$this->data['js_files'][] = base_url() . 'assets/extra/fungsi/number_format.js';

		$this->data['simpanan'] = $this->general_m->get_jns_simpanan_all();
		$this->data['anggota'] = $this->general_m->get_anggota();
		
		$this->data['isi'] = $this->load->view('pengajuan_penarikan_simpanan_tambah_v', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);	
	}


	public function get_simpanan_by_anggota()
	{	
		$jenis = $this->input->post('jenis');
		$anggota_id = $this->input->post('anggota_id');
		$out = array('output'=>false);
		$this->load->model('member_m');
		$result = $this->member_m->get_simpanan_by_anggota($anggota_id,$jenis);
		
		$out = array('output'=>true,'jumlah'=>$result);
		echo json_encode($out);
	}

	public function proses_tambah()
	{
		$output = array('output'=>false);

		$jenis_simpanan = $this->input->post('jenis_simpanan');
		$kode_simpanan = $this->db->select('jns_simpan,kode_simpan')->get_where('jns_simpan',array('id'=>$jenis_simpanan))->row_array();
		$anggota = $this->input->post('anggota');
		$nominal = preg_replace('/\D/', '', $this->input->post('nominal'));
		$keterangan = $this->input->post('keterangan');
		$type = 3;
		$no_ajuan = $this->general_m->get_ajuan($type);
		$ajuan_id = $this->general_m->get_ajuan_id($no_ajuan,$type,$kode_simpanan['kode_simpan']); 

		$data = array(
			'type' => $type,
			'no_ajuan' => $no_ajuan,
			'ajuan_id'	=> $ajuan_id,
			'anggota_id' => $anggota,
			'tgl_input'	=> date('Y-m-d H:i:s'),
			'jenis' => $kode_simpanan['jns_simpan'],
			'nominal' => $nominal,
			'keterangan' => $keterangan,
			'status' => 0,
		);
		if($this->db->insert('tbl_pengajuan',$data)){
			$pesan = 'Pengajuan Penarikan '.$kode_simpanan['jns_simpan'].' telah dibuat';
			$this->notif_m->created_notif(1,$ajuan_id,$pesan,$anggota);
			$this->notif_m->created_notif(2,$ajuan_id,$pesan,$anggota,3);

			$output = array('output'=>true);
		}

		echo json_encode($output);
	}


	function get_jenis_simpanan() {
		$id = $this->input->post('jenis_id');
		$jenis_simpanan = $this->general_m->get_id_simpanan();
		foreach ($jenis_simpanan as $row) {
			if($row->id == $id) {
				echo number_format($row->jumlah);
			}
		}
		exit();
	}

	public function create() {
		if(!isset($_POST)) {
			show_404();
		}
		if($this->simpanan_m->create()){
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
		if($this->simpanan_m->update($id)) {
			echo json_encode(array('ok' => true, 'msg' => '<div class="text-green"><i class="fa fa-check"></i> Data berhasil diubah </div>'));
		} else {
			echo json_encode(array('ok' => false, 'msg' => '<div class="text-red"><i class="fa fa-ban"></i>  Maaf, Data gagal diubah, pastikan nilai lebih dari <strong>0 (NOL)</strong>. </div>'));
		}

	}
	public function delete() {
		if(!isset($_POST))	 {
			show_404();
		}
		$data = $this->input->post('data');

		if(!empty($data)){
			foreach ($data as $key => $row) {
				$this->db->delete('tbl_pengajuan',array('id'=>$row['id']));
			}

			echo json_encode(array('ok' => true, 'msg' => '<div class="text-green"><i class="fa fa-check"></i> Data berhasil dihapus </div>'));
		}
	}


	function cetak_laporan() {
		$simpanan = $this->simpanan_m->lap_data_simpanan();
		if($simpanan == FALSE) {
			//redirect('simpanan');
			echo 'DATA KOSONG<br>Pastikan Filter Tanggal dengan benar.';
			exit();
		}

		$tgl_dari = $_REQUEST['tgl_dari']; 
		$tgl_sampai = $_REQUEST['tgl_sampai']; 

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
		'.$pdf->nsi_box($text = '<span class="txt_judul">Laporan Data Simpanan Anggota <br></span>
			<span> Periode '.jin_date_ina($tgl_dari).' - '.jin_date_ina($tgl_sampai).'</span> ', $width = '100%', $spacing = '0', $padding = '1', $border = '0', $align = 'center').'
		<table width="100%" cellspacing="0" cellpadding="3" border="1" border-collapse= "collapse">
		<tr class="header_kolom">
			<th class="h_tengah" style="width:5%;" > No. </th>
			<th class="h_tengah" style="width:8%;"> No Transaksi</th>
			<th class="h_tengah" style="width:7%;"> Tanggal </th>
			<th class="h_tengah" style="width:25%;"> Nama Anggota </th>
			<th class="h_tengah" style="width:13%;"> Dept </th>
			<th class="h_tengah" style="width:18%;"> Jenis Simpanan </th>
			<th class="h_tengah" style="width:13%;"> Jumlah  </th>
			<th class="h_tengah" style="width:10%;"> User </th>
		</tr>';

		$no =1;
		$jml_simpanan = 0;
		foreach ($simpanan as $row) {
			$anggota= $this->simpanan_m->get_data_anggota($row->anggota_id);
			$jns_simpan= $this->simpanan_m->get_jenis_simpan($row->jenis_id);

			$tgl_bayar = explode(' ', $row->tgl_transaksi);
			$txt_tanggal = jin_date_ina($tgl_bayar[0],'p');

			$jml_simpanan += $row->jumlah;

			// '.'AG'.sprintf('%04d', $row->anggota_id).'
			$html .= '
			<tr>
				<td class="h_tengah" >'.$no++.'</td>
				<td class="h_tengah"> '.'TRD'.sprintf('%05d', $row->id).'</td>
				<td class="h_tengah"> '.$txt_tanggal.'</td>
				<td class="h_kiri"> '.$anggota->identitas.' - '.$anggota->nama.'</td>
				<td> '.$anggota->departement.'</td>
				<td> '.$jns_simpan->jns_simpan.'</td>
				<td class="h_kanan"> '.number_format($row->jumlah).'</td>
				<td> '.$row->user_name.'</td>
			</tr>';
		}
		$html .= '
		<tr>
			<td colspan="5" class="h_tengah"><strong> Jumlah Total </strong></td>
			<td class="h_kanan"> <strong>'.number_format($jml_simpanan).'</strong></td>
		</tr>
		</table>';
		$pdf->nsi_html($html);
		$pdf->Output('trans_sp'.date('Ymd_His') . '.pdf', 'I');
	} 


	public function setuju()
	{
		$out = array('ok'=>false);		

		$data = $this->input->post('data');
		$ket = $this->input->post('ket');
		$kas = $this->input->post('kas');
		$tgl_transaksi = $this->input->post('tgl_transaksi');

		
		if(!empty($data)){
			foreach ($data as $key => $row) {
				// $jenis_id = $this->db->select('id')->get_where('jns_simpan',array('jns_simpan'=>$row['jenis_id_txt']))->row_array()['id'];

				// $kd_transaksi = $this->general_m->get_kode_transaksi('C');
				// $data_simpan = array(
				// 	'tgl_transaksi' => $tgl_transaksi,
				// 	'anggota_id' 	=> $row['anggota_id'],
				// 	'jenis_id' 		=> $jenis_id,
				// 	'jumlah'		=> str_replace(',','', $row['jumlah']),
				// 	'keterangan'	=> $ket,
				// 	'akun'			=> 'Penarikan',
				// 	'dk'			=> 'K',
				// 	'kas_id'		=> $kas,
				// 	'user_name'		=> $this->data['u_name'],
				// 	'ajuan_id'		=> $row['id_txt'],
				// 	'kd_transaksi'	=> $kd_transaksi
				// );				


				// // Simpan Data 
				// $this->db->insert('tbl_trans_sp',$data_simpan);

				// Update Data Pengajuan
				$data_ubah = array(
					'status'	=> 1,
					'alasan'	=> $ket,
					'tgl_cair'	=> $tgl_transaksi,
					'tgl_update'	=> $tgl_transaksi,
					'user_verif'		=> $this->data['u_name'],
				);

				$this->db->update('tbl_pengajuan',$data_ubah,array('ajuan_id'=>$row['id_txt']));
			}
		}

		$out = array('ok'=>true,'msg'=> 'Berhasil menyutujui ajuin ini');		
		header('Content-Type: application/json');
		echo json_encode($out);
		exit();
	}


	public function tolak()
	{
		$out = array('ok'=>false);		

		$data = $this->input->post('data');
		$ket = $this->input->post('ket');
		
		if(!empty($data)){
			foreach ($data as $key => $row) {				
				// Update Data Pengajuan
				$data_ubah = array(
					'status'	=> 2,
					'alasan'	=> $ket,
					'tgl_update'	=> date('Y-m-d H:i:s'),
					'user_verif'		=> $this->data['u_name'],
				);
				$this->db->update('tbl_pengajuan',$data_ubah,array('ajuan_id'=>$row['id_txt']));
			}
		}

		$out = array('ok'=>true,'msg'=> 'Berhasil menyutujui ajuin ini');		
		header('Content-Type: application/json');
		echo json_encode($out);
		exit();
	}


}