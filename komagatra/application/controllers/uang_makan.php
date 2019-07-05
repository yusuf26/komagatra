<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Uang_makan extends OPPController {
	public function __construct() {
		parent::__construct();	
		$this->load->helper('fungsi');
		$this->load->model('general_m');
		$this->load->model('uang_makan_m');
	}	

	public function index() {
		$this->load->model('pinjaman_m');
		$this->load->model('simpanan_m');
		$this->data['judul_browser'] = 'Uang Makan';
		$this->data['judul_utama'] = 'Uang';
		$this->data['judul_sub'] = 'Makan';

		//table
		$this->data['css_files'][] = base_url() . 'assets/extra/bootstrap-table/bootstrap-table.min.css';
		$this->data['js_files2'][] = base_url() . 'assets/extra/bootstrap-table/bootstrap-table.min.js';
		$this->data['js_files2'][] = base_url() . 'assets/extra/bootstrap-table/extensions/filter-control/bootstrap-table-filter-control.min.js';
		$this->data['js_files2'][] = base_url() . 'assets/extra/bootstrap-table/bootstrap-table-id-ID.js';

		//modal
		$this->data['css_files'][] = base_url() . 'assets/extra/bootstrap-modal/css/bootstrap-modal-bs3patch.css';
		$this->data['css_files'][] = base_url() . 'assets/extra/bootstrap-modal/css/bootstrap-modal.css';
		$this->data['js_files'][] = base_url() . 'assets/extra/bootstrap-modal/js/bootstrap-modalmanager.js';
		$this->data['js_files'][] = base_url() . 'assets/extra/bootstrap-modal/js/bootstrap-modal.js';
		$this->data['js_files'][] = base_url() . 'assets/extra/bootstrap-modal/js/nsi_modal_default.js';

		// datepicker
		$this->data['css_files'][] = base_url() . 'assets/theme_admin/css/datepicker/datepicker3.css';
		$this->data['js_files'][] = base_url() . 'assets/theme_admin/js/plugins/datepicker/bootstrap-datepicker.js';
		$this->data['js_files'][] = base_url() . 'assets/theme_admin/js/plugins/datepicker/locales/bootstrap-datepicker.id.js';
		//$this->data['barang_id'] = $this->pinjaman_m->get_id_barang();

		//daterange
		$this->data['css_files'][] = base_url() . 'assets/theme_admin/css/daterangepicker/daterangepicker-bs3.css';
		$this->data['js_files'][] = base_url() . 'assets/theme_admin/js/plugins/daterangepicker/daterangepicker.js';

		//select2
		$this->data['css_files'][] = base_url() . 'assets/extra/select2/select2.css';
		$this->data['js_files'][] = base_url() . 'assets/extra/select2/select2.min.js';

		//editable
		$this->data['css_files'][] = base_url() . 'assets/extra/bootstrap3-editable/css/bootstrap-editable.css';
		$this->data['js_files'][] = base_url() . 'assets/extra/bootstrap3-editable/js/bootstrap-editable.min.js';	

		$this->data['js_files'][] = base_url() . 'assets/extra/fungsi/number_format.js';

		$this->data['anggota'] = $this->db->select('nama,id,anggota_id')->get_where('tbl_anggota',array('aktif'=>'Y'))->result_array();
		$this->data['isi'] = $this->load->view('uang_makan_list_v', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);
	}

	public function ajax_uang_makan() {
		$this->load->model('uang_makan_m');
		$out = $this->uang_makan_m->get_uang_makan();
		header('Content-Type: application/json');
		echo json_encode($out);
		exit();
	}

	function aksi() {
		$this->load->model('uang_makan_m');
		if($this->uang_makan_m->uang_makan_aksi()) {
			echo 'OK';
		} else {
			echo 'Gagal';
		}
	}

	function get_ags() {
		
		$id = $this->input->post('id');
		$out = array('output'=>false);

		$get_pengajuan = $this->db->get_where('tbl_pengajuan',array('id'=>$id))->row();

		$get_ags = $this->db->order_by('angsuran_ke','desc')->get_where('tbl_trans_sp',array('ajuan_id'=>$get_pengajuan->ajuan_id))->row();

		if($get_pengajuan->lama_ags == $get_ags->angsuran_ke){
			$out = array('output'=>true,'angsuran_ke'=>'done');
		}else {
			$out = array('output'=>true,'angsuran_ke'=>$get_ags->angsuran_ke + 1,'jumlah'=>$get_ags->jumlah);	
		}
		

		header('Content-Type: application/json');
		echo json_encode($out);
		// exit();
		
	}

	function edit() {
		$this->load->model('pinjaman_m');
		$res = $this->pinjaman_m->pengajuan_edit();
		echo $res;
	}


	public function tambah()
	{
		$output = array('output'=>false);

		$bulan = $this->input->post('bulan');
		$anggota_id = $this->input->post('anggota_id');
		$jumlah = $this->input->post('jumlah');
		$created_by = $this->session->userdata('u_name');
		$arr_bln = explode('-', $bulan);
		
		$data = array(
			'bulan'	=> $arr_bln[1].'-'.$arr_bln[0].'-00',
			'anggota_id'	=> $anggota_id,
			'jumlah'	=> $jumlah,
			'created_by'	=> $created_by,
			'created_date' => date('Y-m-d H:i:s')
		);
		if($this->db->insert('tbl_uang_makan',$data)){
			$output = array('output'=>true);	
		}
		
		echo json_encode($output);
	}

	public function laporan()
	{

		$fr_bulan = $this->input->get('fr_bulan');
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
			.txt_judul {font-size: 15pt; font-weight: bold; padding-bottom: 12px;}
			.header_kolom {background-color: #cccccc; text-align: center; font-weight: bold;}
		</style>
		'.$pdf->nsi_box($text = '<span class="txt_judul">Laporan Uang Makan <br></span> <span> Periode </span> ', $width = '100%', $spacing = '0', $padding = '1', $border = '0', $align = 'center').'
		<table width="100%" cellspacing="0" cellpadding="3" border="1">
			<tr class="header_kolom" pagebreak="false">
				<th style="width:3%;" >No</th>
				<th style="width:22%;">Bulan Penagihan</th>
				<th style="width:40%;">Anggota</th>
				<th style="width:35%;">Jumlah</th>
			</tr>';
		$row_uang_makan = $this->uang_makan_m->get_row($fr_bulan);
		$total_makan = 0;
		if(!empty($row_uang_makan)){
			$no=1;
			foreach ($row_uang_makan as $key => $row) {
				$tgl_arr = explode(' ', $row['bulan']);
				$tgl = $tgl_arr[0];
				$bulan_txt = jin_date_ina($tgl,'full',false,true);
				$html .= '<tr class="h_tengah">';
				$html .= '<td>'.$no.'</td>';
				$html .= '<td>'.$bulan_txt.'</td>';
				$html .= '<td>'.$row['nama'].' ('.$row['departement'].') </td>';
				$html .= '<td>'.number_format($row['jumlah'],0,'.','.').'</td>';
				$html .= '</tr>';
				$no++;
				$total_makan += $row['jumlah'];
			}
		}else {
			$html .= '<tr><td style="text-align:center" colspan="3" >Tidak ada data</td></tr>';
		}
		$html .= '
		<tr>
			<td colspan="3" class="h_kanan"> <strong> Total </strong> </td>
			<td class="h_tengah"><strong>'.number_format($total_makan,0,'.','.').'</strong></td>
		</tr>';
		$html .= '</table>';

		$html .= '
		<br><br>
		<table width="97%">
		<tr>
			<td class="h_tengah" height="50px" width="40%">Dibuat oleh,</td>
			<td class="h_tengah" width="60%"> '.jin_date_ina(date('Y-m-d')).'</td>
		</tr>
		<tr>
			<td class="h_tengah"> BENDAHARA </td>
			<td class="h_tengah"> KETUA </td>
		</tr>
		</table>';

		$pdf->nsi_html($html);
		$pdf->Output('pinjam'.date('Ymd_His') . '.pdf', 'I');       
	
	}

}
