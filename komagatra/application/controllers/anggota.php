<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Anggota extends OperatorController {

	public function __construct() {
		parent::__construct();	
		$this->load->helper('fungsi');
		$this->load->model('anggota_m');
		$this->load->model('general_m');
	}	
	
	public function index() {
		$this->data['judul_browser'] = 'Data';
		$this->data['judul_utama'] = 'Data';
		$this->data['judul_sub'] = 'Anggota <a href="'.site_url('anggota/import').'" class="btn btn-sm btn-success">Import Data</a>';

		$this->data['css_files'][] = base_url() . 'assets/easyui/themes/default/easyui.css';
		$this->data['css_files'][] = base_url() . 'assets/easyui/themes/icon.css';
		$this->data['js_files'][] = base_url() . 'assets/easyui/jquery.easyui.min.js';
		$this->data['js_files'][] = base_url() . 'assets/easyui/datagrid-export.js';

		//number_format
		$this->data['js_files'][] = base_url() . 'assets/extra/fungsi/number_format.js';

		$this->data['css_files'][] = base_url() . 'assets/extra/select2/select2.css';
		$this->data['js_files'][] = base_url() . 'assets/extra/select2/select2.min.js';

		$this->data['isi'] = $this->load->view('anggota_list_v', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);

	}

	function ajax_list() {
		/*Default request pager params dari jeasyUI*/
		$offset = isset($_POST['page']) ? intval($_POST['page']) : 1;
		$limit  = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
		$sort  = isset($_POST['sort']) ? $_POST['sort'] : 'id';
		$order  = isset($_POST['order']) ? $_POST['order'] : 'desc';
		$kode_transaksi = isset($_POST['kode_transaksi']) ? $_POST['kode_transaksi'] : '';
		$search = array('kode_transaksi' => $kode_transaksi);
		$offset = ($offset-1)*$limit;
		$data   = $this->anggota_m->get_data_list($offset,$limit,$search,$sort,$order);
		$i	= 0;
		$rows   = array(); 

		foreach ($data['data'] as $r) {

			$rows[$i]['id'] = $r->id;
			$rows[$i]['photo'] = '<img src="'.base_url().'uploads/anggota/'.$r->file_pic.'" alt="efadd-customer-service.jpg" width="30" height="40">';

			$rows[$i]['id_anggota'] = '<a href="'.site_url('anggota/detail/'.$r->id).'">'.$r->anggota_id.'</a>';

			$anggota_baru = 'Ya';
			if($r->anggota_baru == 0){
				$anggota_baru = 'Tidak';
			}
			
			$rows[$i]['anggota_baru'] = $anggota_baru;
			$rows[$i]['username'] = $r->identitas;
			$rows[$i]['nama_lengkap'] = $r->nama;

			$jk = 'Laki-laki';
			if($r->jk == 'P'){
				$jk = 'Perempuan';
			}

			$rows[$i]['jenis_kelamin'] = $jk;
			$rows[$i]['alamat'] = $r->alamat;
			$rows[$i]['kota'] = $r->kota;

			$jabatan = 'Anggota';
			if($r->jabatan_id == 1){
				$jabatan = 'Pengurus';
			}
			$rows[$i]['jabatan'] = $jabatan;
			$rows[$i]['departement'] = $this->general_m->get_departement($r->departement);
			$rows[$i]['tgl_regis'] = jin_date_ina($r->tgl_daftar,'full');

			$aktif = 'Aktif';
			if($r->aktif == 'N'){
				$aktif = 'Tidak Aktif';
			}elseif($r->aktif == 'P') {
				$aktif = 'Pasif';
			}
			$rows[$i]['aktif'] = $aktif;
			$rows[$i]['aksi'] = '<a href="'.site_url('anggota/add/'.$r->id).'" class="btn btn-xs btn-info"><i class="fa fa-edit"></i></a> <a href="javascript:void(0);" " class="btn btn-xs  btn-danger" onclick="hapus('.$r->id.')" ><i class="fa fa-times"></i></a>';

			$i++;
		}
		//keys total & rows wajib bagi jEasyUI
		$result = array('total'=>$data['count'],'rows'=>$rows);
		echo json_encode($result); //return nya json
	}

	public function add() {
		$this->data['judul_browser'] = 'Data';
		$this->data['judul_utama'] = 'Data';
		$this->data['judul_sub'] = 'Tambah Anggota';

		$this->data['css_files'][] = base_url() . 'assets/easyui/themes/default/easyui.css';
		$this->data['css_files'][] = base_url() . 'assets/easyui/themes/icon.css';
		$this->data['js_files'][] = base_url() . 'assets/easyui/jquery.easyui.min.js';
		

		$this->data['css_files'][] = base_url() . 'assets/extra/bootstrap_date_time/css/bootstrap-datetimepicker.min.css';
		$this->data['js_files'][] = base_url() . 'assets/extra/bootstrap_date_time/js/bootstrap-datetimepicker.min.js';
		$this->data['js_files'][] = base_url() . 'assets/extra/bootstrap_date_time/js/locales/bootstrap-datetimepicker.id.js';

		//number_format
		$this->data['js_files'][] = base_url() . 'assets/extra/fungsi/number_format.js';

		$this->data['css_files'][] = base_url() . 'assets/extra/select2/select2.css';
		$this->data['js_files'][] = base_url() . 'assets/extra/select2/select2.min.js';


		$id = $this->uri->segment(3);
		$this->data['id'] = $id;
		if(!empty($id)){
			$this->data['ag'] = $this->db->get_where('tbl_anggota',array('id'=>$id))->row_array();
		}
		$this->data['isi'] = $this->load->view('anggota_form_v', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);

	}

	public function hapus($id)
	{
		$this->db->delete('tbl_anggota',array('id'=>$id));
		redirect('anggota');
	}


	public function form_process()
	{	

		$id = $this->input->post('id');

		$anggota_id = $this->input->post('anggota_id');

		$config['upload_path']          = './uploads/anggota/';
        $config['allowed_types']        = 'gif|jpg|png|jpeg';
        $this->load->library('upload', $config);

        $file_pic = '';
        if(!empty($id)){
        	$file_pic = $this->input->post('val_photo');
        	if($_FILES['userfile']['name'] && $file_pic != $_FILES['userfile']['name']){
        		if ( ! $this->upload->do_upload('userfile'))
		        {
		                $file_pic = $this->upload->display_errors();
		        }
		        else
		        {
		                $file_pic = $this->upload->data()['file_name'];
		        }
        	}

        }else {
        	if ( ! $this->upload->do_upload('userfile'))
	        {
	                $file_pic = $this->upload->display_errors();
	        }
	        else
	        {
	                $file_pic = $this->upload->data()['file_name'];
	        }
        }

        $password = sha1('nsi'.$this->input->post('password'));
        
		$arr_data = array(
				'anggota_id' => $anggota_id,
				'nama' => $this->input->post('nama'),
				'nik' => $this->input->post('nik'),
				'npwp' => $this->input->post('npwp'),
				'identitas' => $this->input->post('identitas'),
				'jk' => $this->input->post('jk'),
				'tmp_lahir' => $this->input->post('tmp_lahir'),
				'tgl_lahir' => $this->input->post('tgl_lahir'),
				'status' => $this->input->post('status'),
				'departement' => $this->input->post('departement'),
				'pekerjaan' => $this->input->post('pekerjaan'),
				'agama' => $this->input->post('agama'),
				'alamat' => $this->input->post('alamat'),
				'kota' => $this->input->post('kota'),
				'notelp' => $this->input->post('notelp'),
				'tgl_daftar' => $this->input->post('tgl_daftar'),
				'jabatan_id' => $this->input->post('jabatan_id'),
				'nama_bank' => $this->input->post('nama_bank'),
				'no_rekening' => $this->input->post('no_rekening'),
				'nama_rekening' => $this->input->post('nama_rekening'),
				'gaji' => str_replace('.', '', $this->input->post('gaji')),
				'simpanan_pokok' => str_replace('.', '', $this->input->post('simpanan_pokok')),
				'simpanan_wajib' => str_replace('.', '', $this->input->post('simpanan_wajib')),
				'simpanan_sukarela' => str_replace('.', '', $this->input->post('simpanan_sukarela')),
				'pass_word' => $password,
				'aktif' => $this->input->post('aktif'),
				'file_pic' => $file_pic,
		);


		if(!empty($id)){
			$check_id = 0;
			$get_id = $this->db->select('anggota_id')->get_where('tbl_anggota',array('id'=>$id))->row_array();
			if($anggota_id != $get_id['anggota_id']){
				$check_id = $this->db->get_where('tbl_anggota',array('anggota_id'=>$anggota_id))->num_rows();
			}
		}else {
			$check_id = $this->db->get_where('tbl_anggota',array('anggota_id'=>$anggota_id))->num_rows();
		}
		if($check_id > 0){
			$this->session->set_flashdata('error_msg', 'Maaf !!! Anggota ID sudah terdaftar');
			redirect('anggota/add');
		}else {
			if(!empty($id)){
				if($this->db->update('tbl_anggota',$arr_data,array('id'=>$id))){
					redirect('anggota');
				}else {
					$this->session->set_flashdata('error_msg', 'Maaf !!!,Terjadi kesalahan');
					redirect('anggota/add');
				}
			}else {
				if($this->db->insert('tbl_anggota',$arr_data)){
					redirect('anggota');
				}else {
					$this->session->set_flashdata('error_msg', 'Maaf !!!,Terjadi kesalahan');
					redirect('anggota/add');
				}
			}
		}
	}


	function import() {
		$this->data['judul_browser'] = 'Import Data';
		$this->data['judul_utama'] = 'Import Data';
		$this->data['judul_sub'] = 'Anggota <a href="'.site_url('anggota').'" class="btn btn-sm btn-success">Kembali</a>';

		$this->load->helper(array('form'));

		if($this->input->post('submit')) {
			$config['upload_path']   = FCPATH . 'uploads/temp/';
			$config['allowed_types'] = 'xls|xlsx';
			$this->load->library('upload', $config);

			if ( ! $this->upload->do_upload('import_anggota')) {
				$this->data['error'] = $this->upload->display_errors();
			} else {
				// ok uploaded
				$file = $this->upload->data();
				$this->data['file'] = $file;

				$this->data['lokasi_file'] = $file['full_path'];

				$this->load->library('excel');

				// baca excel
				$objPHPExcel = PHPExcel_IOFactory::load($file['full_path']);
				$no_sheet = 1;
				$header = array();
				$data_list_x = array();
				$data_list = array();
				foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
					if($no_sheet == 1) { // ambil sheet 1 saja
						$no_sheet++;
						$worksheetTitle = $worksheet->getTitle();
						$highestRow = $worksheet->getHighestRow(); // e.g. 10
						$highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
						$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

						$nrColumns = ord($highestColumn) - 64;
						//echo "File ".$worksheetTitle." has ";
						//echo $nrColumns . ' columns';
						//echo ' y ' . $highestRow . ' rows.<br />';

						$data_jml_arr = array();
						//echo 'Data: <table width="100%" cellpadding="3" cellspacing="0"><tr>';
						for ($row = 1; $row <= $highestRow; ++$row) {
						   //echo '<tr>';
							for ($col = 0; $col < $highestColumnIndex; ++$col) {
								$cell = $worksheet->getCellByColumnAndRow($col, $row);
								$val = $cell->getValue();
								$kolom = PHPExcel_Cell::stringFromColumnIndex($col);
								if($row === 1) {
									if($kolom == 'A') {
										$header[$kolom] = 'Nama';
									} else {
										$header[$kolom] = $val;
									}
								} else {
									$data_list_x[$row][$kolom] = $val;
								}
							}
						}
					}
				}

				$no = 1;
				foreach ($data_list_x as $data_kolom) {
					if((@$data_kolom['A'] == NULL || trim(@$data_kolom['A'] == '')) ) { continue; }
					foreach ($data_kolom as $kolom => $val) {
						if(in_array($kolom, array('E', 'K', 'L')) ) {
							$val = ltrim($val, "'");
						}
						$data_list[$no][$kolom] = $val;
					}
					$no++;
				}

				//$arr_data = array();
				$this->data['header'] = $header;
				$this->data['values'] = $data_list;
				/*
				$data_import = array(
					'import_anggota_header'		=> $header,
					'import_anggota_values' 	=> $data_list
					);
				$this->session->set_userdata($data_import);
				*/
			}
		}


		$this->data['isi'] = $this->load->view('anggota_import_v', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);
	}


	function import_db() {
		if($this->input->post('submit')) {
			$this->load->model('Member_m','member', TRUE);
			$data_import = $this->input->post('val_arr');
			if($this->member->import_db($data_import)) {
				$this->session->set_flashdata('import', 'OK');
			} else {
				$this->session->set_flashdata('import', 'NO');
			}
			//hapus semua file di temp
			$files = glob('uploads/temp/*');
			foreach($files as $file){ 
				if(is_file($file)) {
					@unlink($file);
				}
			}
			redirect('anggota/import');
		} else {
			$this->session->set_flashdata('import', 'NO');
			redirect('anggota/import');
		}
	}

	function import_batal() {
		//hapus semua file di temp
		$files = glob('uploads/temp/*');
		foreach($files as $file){ 
			if(is_file($file)) {
				@unlink($file);
			}
		}
		$this->session->set_flashdata('import', 'BATAL');
		redirect('anggota/import');
	}

	function _set_password_input_to_empty() {
		return "<input type='password' name='pass_word' value='' /><br />Kosongkan password jika tidak ingin ubah/isi.";
	}

	function _encrypt_password_callback($post_array) {
		if(!empty($post_array['pass_word'])) {
			$post_array['pass_word'] = sha1('nsi' . $post_array['pass_word']);
		} else {
			unset($post_array['pass_word']);
		}
		return $post_array;
	}

	function _kolom_id_cb ($value, $row) {
		$value = '<div style="text-align:center;"><a href="'.site_url('anggota/detail/'.$row->id).'" onclick="modalAnggota('.$row->id.')">AG' . sprintf('%04d', $row->id) . '</a></div>';
		return $value;
	}

	function _kolom_alamat($value, $row) {
		$value = wordwrap($value, 35, "<br />");
		return nl2br($value);
	}

	function _anggota_id() {

		$last = $this->db->order_by('id',"desc")
		->limit(1)
		->get('tbl_anggota')
		->row();
		$ag = 'AG'.sprintf('%04d', $last->id + 1);
		$value = '<input type="text" name="anggota_id" value="'.$ag.'" />';
		return $value;
	}

	function _anggota_id_edit($value,$primary_key) {
		$value = '<input type="text" name="anggota_id" value="'.$value.'" />';
		return $value;
	}

	function _aktif_edit($value,$primary_key) {

		$N = false;
		$Y = false;
		$P = false;
		if($value == 'N'){$N = 'selected';};
		if($value == 'Y'){$Y = 'selected';};
		if($value == 'P'){$P = 'selected';};
		$result = '<select id="field-aktif" name="aktif" class="chosen-select chzn-done" data-placeholder="Pilih Aktif Keanggotaan"><option value="Y"'.$Y.' >Aktif</option><option value="N" '.$N.'>Non Aktif</option><option value="P" '.$P.'>Pasive</option></select>';
		if($value == 'N'){
			$dt = $this->db->where('id',$primary_key)
					->limit(1)
					->get('tbl_anggota')
					->row();
			$result .= '<span style="display:inline-block;margin-left:10px;" class="label label-danger">'.$dt->updated_at.'</span>';
		}
		
		return $result;
	}

	function callback_column_pic($value, $row) {
		if($value) {
			return '<div style="text-align: center;"><a class="image-thumbnail" href="'.base_url().'uploads/anggota/' . $value .'"><img src="'.base_url().'uploads/anggota/' . $value . '" alt="' . $value . '" width="30" height="40" /></a></div>';
		} else {
			return '<div style="text-align: center;"><img src="'.base_url().'assets/theme_admin/img/photo.jpg" alt="default" width="30" height="40" /></div>';
		}
	}

	function callback_after_upload($uploader_response,$field_info, $files_to_upload) {
		$this->load->library('image_moo');
        //Is only one file uploaded so it ok to use it with $uploader_response[0].
		$file_uploaded = $field_info->upload_path.'/'.$uploader_response[0]->name;
		$this->image_moo->load($file_uploaded)->resize(250,250)->save($file_uploaded,true);
		return true;
	}


	public function detail($id)
	{

		$this->load->model('lap_kas_anggota_m');




		$get_anggota = $this->db->get_where('tbl_anggota',array('id'=>$id))->row();

		$this->data['judul_browser'] = 'Anggota '.$get_anggota->nama;
		$this->data['judul_utama'] = ''.$get_anggota->nama;
		$this->data['judul_sub'] = 'Detail';

		//table
		$this->data['css_files'][] = base_url() . 'assets/extra/bootstrap-table/bootstrap-table.min.css';
		$this->data['js_files2'][] = base_url() . 'assets/extra/bootstrap-table/bootstrap-table.min.js';
		$this->data['js_files2'][] = base_url() . 'assets/extra/bootstrap-table/extensions/filter-control/bootstrap-table-filter-control.min.js';
		$this->data['js_files2'][] = base_url() . 'assets/extra/bootstrap-table/bootstrap-table-id-ID.js';
		$this->data['js_files'][] = base_url() . 'assets/extra/fungsi/number_format.js';
		$this->data['css_files'][] = base_url() . 'assets/extra/bootstrap_date_time/css/bootstrap-datetimepicker.min.css';
		$this->data['js_files'][] = base_url() . 'assets/extra/bootstrap_date_time/js/bootstrap-datetimepicker.min.js';
		$this->data['js_files'][] = base_url() . 'assets/extra/bootstrap_date_time/js/locales/bootstrap-datetimepicker.id.js';
		//modal

		$this->data['anggota_id'] = $id;
		$this->data['data_anggota'] = $get_anggota;


		$this->data['kas'] = $this->db->select('id,nama')->get_where('nama_kas_tbl',array('aktif'=>'Y','tmpl_simpan'=>'Y'))->result_array();
		// Get Data Simpanan
		$nilai_s_s = $this->lap_kas_anggota_m->get_jml_simpanan(32, $id);
		$nilai_s_p = $this->lap_kas_anggota_m->get_jml_penarikan(32, $id);

		$this->data['simpanan_sukarela'] = $nilai_s_s->jml_total - $nilai_s_p->jml_total;

		$nilai_p_s = $this->lap_kas_anggota_m->get_jml_simpanan(40, $id);
		$nilai_p_p = $this->lap_kas_anggota_m->get_jml_penarikan(40, $id);

		$this->data['simpanan_pokok'] = $nilai_p_s->jml_total - $nilai_p_p->jml_total;

		$nilai_w_s = $this->lap_kas_anggota_m->get_jml_simpanan(41, $id);
		$nilai_w_p = $this->lap_kas_anggota_m->get_jml_penarikan(41, $id);

		$this->data['simpanan_wajib'] = $nilai_w_s->jml_total - $nilai_w_p->jml_total;


		$this->data['isi'] = $this->load->view('anggota_detail_v', $this->data, TRUE);
		$this->load->view('themes/layout_utama_v', $this->data);
	}


	public function ajax_anggota_detail($id)
	{

		$offset = isset($_POST['page']) ? intval($_POST['page']) : 1;
		$limit  = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
		$sort  = isset($_POST['sort']) ? $_POST['sort'] : 'tgl_pinjam';
		$order  = isset($_POST['order']) ? $_POST['order'] : 'desc';

		$this->db->where('anggota_id',$id);
		$this->db->order_by($sort,$order);

		$query = $this->db->get('v_hitung_pinjaman');
		$data_list = $query->result();		
		$total = $query->num_rows();

		// 
		$data_list_i = array();
		foreach ($data_list as $key => $val) {
			// tgl pinjam
			$tgl_arr = explode(' ', $val->tgl_pinjam);
			$tgl = $tgl_arr[0];
			$val->tgl_txt = jin_date_ina($tgl);


			// tgl tempo
			$count_temp = $val->lama_angsuran - $val->bln_sudah_angsur;
			$tgl_tempo = date('Y-m-d',strtotime($tgl.' +'.$count_temp.' month'));
			$val->tgl_tempo = jin_date_ina($tgl_tempo);
			$val->sisa_angsuran = number_format($val->ags_per_bulan * ($val->lama_angsuran - $val->bln_sudah_angsur));
			$val->jumlah = number_format($val->jumlah);
			$val->ags_per_bulan = number_format($val->ags_per_bulan);

			if($val->lunas == 'Lunas'){
				$tgl_lunas = date('Y-m-d',strtotime($val->update_data));
				$val->tgl_lunas = jin_date_ina($tgl_lunas);
			}

			$data_list_i[$key] = $val;
		}

		$out = array('rows' => $data_list_i, 'total' => $total);

		header('Content-Type: application/json');
		echo json_encode($out);
		exit();	
	}


	public function ajax_simpanan($type,$id)
	{

		$set_m = 'DATE_FORMAT(tbl_trans_sp.tgl_transaksi,"%m")';
		$this->db->select("tbl_trans_sp.iuran,tbl_trans_sp.anggota_id,tbl_trans_sp.id,tbl_trans_sp.jenis_id,tbl_trans_sp.keterangan,tbl_trans_sp.tgl_transaksi, nama_kas_tbl.nama,  year(tbl_trans_sp.tgl_transaksi) as tahun, month(tbl_trans_sp.tgl_transaksi) as bulan ,SUM(jumlah) as total_simpanan ");
		$this->db->where('jenis_id',$type);
		$this->db->where('akun','Setoran');
		$this->db->where('anggota_id',$id);
		$this->db->join('nama_kas_tbl','nama_kas_tbl.id = tbl_trans_sp.kas_id','left');
		$this->db->order_by('tbl_trans_sp.tgl_transaksi','desc');
		$this->db->group_by('MONTH(tgl_transaksi), YEAR(tgl_transaksi)');
		$query = $this->db->get('tbl_trans_sp');
		$data_list = $query->result();		
		$total = $query->num_rows();

		// 
		$data_list_i = array();
		$total_simpanan = 0;
		foreach ($data_list as $key => $val) {
			// tgl pinjam
			$tgl_arr = explode(' ', $val->tgl_transaksi);
			$tgl = $tgl_arr[0];
			$val->tgl_txt = jin_date_ina($tgl,'full',false,true);
			$val->total_simpanan = number_format($val->total_simpanan);

			if(strlen($val->bulan) == 1){
				$val->bulan = '0'.$val->bulan;
			}
			
			$iuran = 0;
			$arr_iuran = $this->db->select('jumlah')
									// ->order_by('id','desc')
									->get_where('tbl_trans_sp',array('iuran'=>1,'jenis_id'=>$type,'akun'=>'Setoran','anggota_id'=>$id,'DATE_FORMAT(tgl_transaksi,"%Y-%m")'=>$val->tahun.'-'.$val->bulan))
									->row_array();

			if(!empty($arr_iuran)){
				$iuran = number_format($arr_iuran['jumlah']);
				// $iuran = $val->tahun.'-'.$val->bulan;
			}
			$val->iuran = $iuran;
			

			$jumlah = 0;
			$last_jumlah = $this->db->select('jumlah,id')
									->order_by('id','desc')
									->get_where('tbl_trans_sp',array('iuran'=>0,'jenis_id'=>$type,'akun'=>'Setoran','anggota_id'=>$id,'DATE_FORMAT(tgl_transaksi,"%Y-%m")'=>$val->tahun.'-'.$val->bulan))
									->row_array();

			
			if(!empty($last_jumlah)){
				$jumlah = $last_jumlah['jumlah'];
			}
			$val->jumlah = number_format($jumlah);
			// $val->jumlah = $jum;

			$val->bulan = date_to_bulan($val->bulan,'full');

			


			$data_list_i[$key] = $val;
		}

		$out = array('rows' => $data_list_i, 'total' => $total);

		header('Content-Type: application/json');
		echo json_encode($out);
		exit();	
	}


	function tambah_simpanan()
	{
		$output = array('ok'=>false);

		$iuran = $this->input->post('iuran');
		$jenis_id = $this->input->post('jenis_id');
		$kas = $this->input->post('kas');
		$ex_bulan =  explode(' ', $this->input->post('bulan'));

		$bulan = bulan_to_date_2($ex_bulan[0].' '.$ex_bulan[1],'full');
		$tgl = $ex_bulan[1].'-'.$bulan.'-00';
		$nominal = str_replace('.', '', $this->input->post('nominal'));
		$anggota_id = $this->input->post('anggota_id');
		$nama_anggota = $this->db->select('nama')->get_where('tbl_anggota',array('id'=>$anggota_id))->row();

		$check_i = false;
		if($iuran == 1){
			$arr_iuran = $this->db->select('jumlah')
								->get_where('tbl_trans_sp',array('iuran'=>1,'jenis_id'=>$jenis_id,'akun'=>'Setoran','anggota_id'=>$anggota_id,'DATE_FORMAT(tgl_transaksi,"%Y-%m")'=>$ex_bulan[1].'-'.$bulan))
								->num_rows();
			if($arr_iuran > 0){
				$output = array('ok'=>false,'msg'=>'Maaf !!! Iuran '.$ex_bulan[0].' '.$ex_bulan[1].' sudah lunas');
			}else {
				$check_i = true;
			}
		}else {
			$check_i = true;
		}


		if($check_i){
			$time = date('H:i:s');
			$dt = $ex_bulan[1].'-'.$bulan.'-'.date('d').' '.$time;
			$data = array(
					'jenis_id' => $jenis_id,
					'tgl_transaksi' => $dt,
					'anggota_id' => $anggota_id,
					'kas_id' => $kas,
					'jumlah' => $nominal,
					'keterangan' => $this->input->post('keterangan'),
					'akun' => 'Setoran',
					'user_name' => $this->data['u_name'],
					'nama_member' => $nama_anggota->nama,
					'iuran' => $iuran
			);
			if($this->db->insert('tbl_trans_sp',$data)){
				$output = array('ok'=>true,'jenis_id'=>$jenis_id);
			}
		}


		
		header('Content-Type: application/json');
		echo json_encode($output);
		exit();	
	}

	function ubah_simpanan()
	{
		$output = array('ok'=>false);

		$jenis_id = $this->input->post('jenis_id');
		$nominal = str_replace('.', '', $this->input->post('nominal'));
		$anggota_id = $this->input->post('anggota_id');

		$jenis = '';
		if($jenis_id == 32){
			$jenis = 'Simpanan Sukarela';
		}
		if($jenis_id == 40){
			$jenis = 'Simpanan Pokok';
		}
		if($jenis_id == 41){
			$jenis = 'Simpanan Wajib';
		}

		$jenis = strtolower(str_replace(' ', '_', $jenis));

		if($this->db->update('tbl_anggota',array($jenis=>$nominal),array('id'=>$anggota_id))){
			$output = array('ok'=>true);
		}

		
		header('Content-Type: application/json');
		echo json_encode($output);
		exit();	
	}


	public function detail_simpanan()
	{
		$output = array('ok'=>false);
		$anggota_id = $this->input->post('anggota_id');
		$jenis_id = $this->input->post('jenis_id');
		$tgl_bulan = $this->input->post('bulan');
		$ex_bln = explode(' ', $tgl_bulan);
		$bulan = bulan_to_date_2($ex_bln[0].' '.$ex_bln[1],'full');
		$query = $this->db->select('jumlah,id,tgl_transaksi,keterangan')
									->order_by('id','desc')
									->get_where('tbl_trans_sp',array('jenis_id'=>$jenis_id,'akun'=>'Setoran','anggota_id'=>$anggota_id,'DATE_FORMAT(tgl_transaksi,"%Y-%m")'=>$ex_bln[1].'-'.$bulan))
									->result_array();

		if(!empty($query)){
			foreach ($query as $key => $value) {
				$tgl_arr = explode(' ', $value['tgl_transaksi']);
				$tgl = $tgl_arr[0];
				$value['tgl_transaksi'] = jin_date_ina($tgl);
				$value['jumlah'] = number_format($value['jumlah']);
				$query[$key] = $value;
			}
			
		}
		$output = array('ok'=>true,'data'=>$query);
		header('Content-Type: application/json');
		echo json_encode($output);
		exit();	
	}

	public function get_iuran()
	{
		$output = array('ok'=>false);
		$anggota_id = $this->input->post('anggota_id');
		$jenis_id = $this->input->post('jenis_id');

		$jenis = '';
		if($jenis_id == 32){
			$jenis = 'Simpanan Sukarela';
		}
		if($jenis_id == 40){
			$jenis = 'Simpanan Pokok';
		}
		if($jenis_id == 41){
			$jenis = 'Simpanan Wajib';
		}

		$jenis = strtolower(str_replace(' ', '_', $jenis));
		$query = $this->db->select($jenis.' as simpanan')->get_where('tbl_anggota',array('id'=>$anggota_id))->row_array();
		$output = array('ok'=>true,'data'=>number_format($query['simpanan'],0,'','.') ,'a'=>$jenis);
		header('Content-Type: application/json');
		echo json_encode($output);
		exit();	
	}

}

