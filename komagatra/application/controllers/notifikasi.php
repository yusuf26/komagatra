<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Notifikasi extends OperatorController {
	public function __construct() {
		parent::__construct();	
		$this->load->helper('fungsi');
		$this->load->model('simpanan_m');
		$this->load->model('general_m');
	}	

	public function index() {
		$this->data['judul_browser'] = 'Notifikasi';
		$this->data['judul_utama'] = 'Notifikasi';
		$this->data['judul_sub'] = 'Notifikasi';

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

		$this->data['isi'] = $this->load->view('notifikasi_list_v', $this->data, TRUE);

		// Update Notif
		$this->db->update('tbl_notif',array('baca'=>1,'baca_by'=>$this->data['u_name'],'baca_at'=>date('Y-m-d H:i:s')),array('baca'=>0,'type'=>1));
		$this->load->view('themes/layout_utama_v', $this->data);
	}

	function ajax_list() {
		/*Default request pager params dari jeasyUI*/
		$offset = isset($_POST['page']) ? intval($_POST['page']) : 1;
		$limit  = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
		$sort  = isset($_POST['sort']) ? $_POST['sort'] : 'tgl_transaksi';
		$order  = isset($_POST['order']) ? $_POST['order'] : 'desc';
		$tgl_dari = isset($_POST['tgl_dari']) ? $_POST['tgl_dari'] : '';
		$tgl_sampai = isset($_POST['tgl_sampai']) ? $_POST['tgl_sampai'] : '';
		$search = array('tgl_dari' => $tgl_dari, 
			'tgl_sampai' => $tgl_sampai);
		$offset = ($offset-1)*$limit;
		$data   = $this->notif_m->get_data_notif($offset,$limit,$search,$sort,$order);
		$i	= 0;
		$rows   = array(); 

		foreach ($data['data'] as $r) {

			$tgl_bayar = explode(' ', $r->created_at);
			$txt_tanggal = jin_date_ina($tgl_bayar[0]);

			$anggota = $this->general_m->get_data_anggota($r->anggota_id);  

			$rows[$i]['id'] = $r->notif_id;
			$rows[$i]['tgl'] = $r->created_at;

			$link_aj = site_url('pengajuan');
			$ex_aj = explode('.', $r->ajuan_id);
			if($ex_aj[1] == 2){
				$link_aj = site_url('pengajuan_simpanan');
			}
			if($ex_aj[1] == 3){
				$link_aj = site_url('pengajuan_penarikan_simpanan');
			}

			$ajuan_id = '<a href="'.$link_aj.'">'.$r->ajuan_id.'</a>';
			$rows[$i]['ajuan_id'] = $ajuan_id;
			$rows[$i]['tgl_txt'] = $txt_tanggal;
			$rows[$i]['anggota'] = $anggota->nama;
			$rows[$i]['pesan'] = $r->pesan;

			$i++;
		}
		//keys total & rows wajib bagi jEasyUI
		$result = array('total'=>$data['count'],'rows'=>$rows);
		echo json_encode($result); //return nya json
	}

}
?>