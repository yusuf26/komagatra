<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Member_m extends CI_Model {


	public function __construct() {
		parent::__construct();
		$this->load->model('notif_m');
	}

	public function validasi() {
		$form_rules = array(
			array(
				'field' => 'u_name',
				'label' => 'username',
				'rules' => 'required'
				),
			array(
				'field' => 'pass_word',
				'label' => 'password',
				'rules' => 'required'
				),
			);
		$this->form_validation->set_rules($form_rules);

		if ($this->form_validation->run()) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

    // cek status user, login atau tidak?
	public function cek_user() {
		$u_name = $this->input->post('u_name');
		$pass_word = sha1('nsi' . $this->input->post('pass_word'));

		$this->db->where('identitas', $u_name);
		$this->db->where('pass_word', $pass_word);
		$this->db->where('aktif', 'Y');
		$this->db->limit(1);
		$query = $this->db->get('tbl_anggota');
		if ($query->num_rows() == 1) {
			$row = $query->row();
			//$level = $row->level;
			$data = array(
				'login'		=> TRUE,
				'u_name' 	=> $row->id, 
				'level'		=> 'member'
				);
			// simpan data session jika login benar
			$this->session->set_userdata($data);
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function get_data_anggota($id) {
		$out = array();
		$sql = "SELECT * FROM tbl_anggota WHERE aktif='Y'";
		$sql .=" AND (id = '".$id."') ";
		$query = $this->db->query($sql);
		if($query->num_rows() > 0) {
			$out = $query->row();
			return $out;
		}
	}


	// UBAH PASS
	public function validasi_ubah_pass() {
		$form_rules = array(
			array(
				'field' => 'password_lama',
				'label' => 'Password Lama',
				'rules' => 'required'
				), array(
				'field' => 'password_baru',
				'label' => 'Password Baru',
				'rules' => 'required'
				), array(
				'field' => 'ulangi_password_baru',
				'label' => 'Ulangi Password Baru',
				'rules' => 'required'
				)
			);
		$this->form_validation->set_rules($form_rules);
		if ($this->form_validation->run()) {
			return TRUE;
		} else {
			return FALSE;
		}
	}


	function cek_pass_lama($user_id) {
		$out = array();
		$pass_word = sha1('nsi' . $this->input->post('password_lama'));
		$this->db->select('id,pass_word');
		$this->db->from('tbl_anggota');
		$this->db->where('id', $user_id);
		$this->db->where('pass_word', $pass_word);
		$this->db->limit('1');
		$query = $this->db->get();
		if($query->num_rows()>0){
			$out = $query->result();
			return $out;
		} else {
			return FALSE;
		}
	}

	function simpan() {
		$user_id = $this->session->userdata('u_name');
		$data_user = $this->cek_pass_lama($user_id);
		if($data_user){
			$pass_word = sha1('nsi' . $this->input->post('password_baru'));
			$data = array ('pass_word'=> $pass_word);
			$this->db->where('id', $user_id);
			if($this->db->update('tbl_anggota', $data)) {
				// ok
				return TRUE;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	// PENGAJUAN
	public function validasi_pengajuan() {
		$form_rules = array(
			array(
				'field' => 'nominal',
				'label' => 'Nominal',
				'rules' => 'required'
				), array(
				'field' => 'jenis',
				'label' => 'Jenis',
				'rules' => 'required'
				)
			);
		$this->form_validation->set_rules($form_rules);
		if ($this->form_validation->run()) {
			return TRUE;
		} else {
			return FALSE;
		}
	}	

	function pengajuan_simpan() {
		$user_id = $this->session->userdata('u_name');
		// last no
		$jenis = $this->input->post('jenis');
		$lama_ags = $this->input->post('lama_ags');
		$nominal = preg_replace('/\D/', '', $this->input->post('nominal'));
		// if(date("d") >= 21) {
		// 	$bln_1 = date("Y-m") . '-21';
		// 	$bln_2 = date("Y-m", strtotime("+1 month")) . '-20';
		// } else {
		// 	$bln_1 = date("Y-m", strtotime("-1 month")) . '-21';
		// 	$bln_2 = date("Y-m") . '-20';
		// }
		$this->db->select_max('no_ajuan');
		$this->db->select('urutan,status');
		$this->db->from('tbl_pengajuan');
		// $this->db->where('DATE(tgl_input) >=', $bln_1);
		// $this->db->where('DATE(tgl_input) <=', $bln_2);
		$this->db->where('jenis', $jenis);
		$this->db->where('type', 1);
		$query = $this->db->get();
		$no_ajuan = 1;
		$urutan = 1;
		if($query->num_rows() > 0) {
			$row = $query->row();
			$no_ajuan = $row->no_ajuan + 1;
		}
		// ajuan_id
		$get_data_p = $this->db->get_where('jns_pinjaman',array('jns_pinjaman'=>$jenis))->row_array();
		$ajuan_id = '';
		
		$ajuan_id .= $get_data_p['kode_pinjaman'];
		

		$ajuan_id .= '.1';
		// if(date("d") >= 21) {
		// 	$ajuan_id .= '.' . substr(date("Y", strtotime("+1 month")), 2, 2);
		// 	$ajuan_id .= '.' . date("m", strtotime("+1 month"));
		// } else {
		// 	$ajuan_id .= '.' . substr(date("Y"), 2, 2);
		// 	$ajuan_id .= '.' . date("m");
		// }
		$ajuan_id .= '.' . substr(date("Y"), 2, 2);
		$ajuan_id .= '.' . date("m");
		$ajuan_id .= '.' . sprintf("%03d", $no_ajuan);

		$data = array (
			'no_ajuan'		=> $no_ajuan,
			'ajuan_id'		=> $ajuan_id,
			'anggota_id'	=> $user_id,
			'nominal'		=> $nominal,
			'jenis'			=> $jenis,
			'lama_ags'		=> $lama_ags,
			'keterangan'	=> $this->input->post('keterangan'),
			'tgl_input'		=> date('Y-m-d H:i:s'),
			'tgl_update'	=> date('Y-m-d H:i:s'),
			'status'		=> 0,
			'potongan'		=> preg_replace('/\D/', '', $this->input->post('potongan')),
			'uang_diterima'		=> preg_replace('/\D/', '', $this->input->post('uang_diterima')),
			);
		if($this->db->insert('tbl_pengajuan', $data)) {

			$pesan = 'Pengajuan '.$jenis.' telah dibuat';
			$this->notif_m->created_notif(1,$ajuan_id,$pesan,$user_id);
			$this->notif_m->created_notif(2,$ajuan_id,$pesan,$user_id);

			// ok
			return TRUE;
		} else {
			return FALSE;
		}
	}

	function pengajuan_simpanan_simpan() {
		$user_id = $this->session->userdata('u_name');
		// last no
		$jenis = $this->input->post('jenis');
		$lama_ags = $this->input->post('lama_ags');
		if(empty($lama_ags)){
			$lama_ags = 0;
		}
		$nominal = preg_replace('/\D/', '', $this->input->post('nominal'));
		if(date("d") >= 21) {
			$bln_1 = date("Y-m") . '-21';
			$bln_2 = date("Y-m", strtotime("+1 month")) . '-20';
		} else {
			$bln_1 = date("Y-m", strtotime("-1 month")) . '-21';
			$bln_2 = date("Y-m") . '-20';
		}
		$this->db->select_max('no_ajuan');
		$this->db->from('tbl_pengajuan');
		$this->db->where('DATE(tgl_input) >=', $bln_1);
		$this->db->where('DATE(tgl_input) <=', $bln_2);
		$this->db->where('jenis', $jenis);
		$this->db->where('type', 2);
		$query = $this->db->get();
		$no_ajuan = 1;
		if($query->num_rows() > 0) {
			$row = $query->row();
			$no_ajuan = $row->no_ajuan + 1;
		}
		// ajuan_id
		$ajuan_id = '';
		if($jenis == 'Sukarela') {
			$ajuan_id .= 'S';
		}
		if($jenis == 'Wajib') {
			$ajuan_id .= 'W';
		}
		$ajuan_id .= '.2';
		if(date("d") >= 21) {
			$ajuan_id .= '.' . substr(date("Y", strtotime("+1 month")), 2, 2);
			$ajuan_id .= '.' . date("m", strtotime("+1 month"));
		} else {
			$ajuan_id .= '.' . substr(date("Y"), 2, 2);
			$ajuan_id .= '.' . date("m");
		}
		$ajuan_id .= '.' . sprintf("%03d", $no_ajuan);

		$data = array (
			'no_ajuan'		=> $no_ajuan,
			'ajuan_id'		=> $ajuan_id,
			'anggota_id'	=> $user_id,
			'nominal'		=> $nominal,
			'jenis'			=> $jenis,
			'lama_ags'		=> $lama_ags,
			'keterangan'	=> $this->input->post('keterangan'),
			'tgl_input'		=> date('Y-m-d H:i:s'),
			'tgl_update'	=> date('Y-m-d H:i:s'),
			'status'			=> 0,
			'type' 			=> 2
			);
		if($this->db->insert('tbl_pengajuan', $data)) {


			$pesan = 'Pengajuan Simpanan '.$jenis.' telah dibuat';
			$this->notif_m->created_notif(1,$ajuan_id,$pesan,$user_id);
			$this->notif_m->created_notif(2,$ajuan_id,$pesan,$user_id,2);

			// ok
			return TRUE;
		} else {
			return FALSE;
		}
	}


	function pengajuan_penarikan_simpanan_simpan() {
		$user_id = $this->session->userdata('u_name');
		// last no
		$jenis = $this->input->post('jenis');
		if(empty($lama_ags)){
			$lama_ags = 0;
		}
		$nominal = preg_replace('/\D/', '', $this->input->post('nominal'));
		if(date("d") >= 21) {
			$bln_1 = date("Y-m") . '-21';
			$bln_2 = date("Y-m", strtotime("+1 month")) . '-20';
		} else {
			$bln_1 = date("Y-m", strtotime("-1 month")) . '-21';
			$bln_2 = date("Y-m") . '-20';
		}
		$this->db->select_max('no_ajuan');
		$this->db->from('tbl_pengajuan');
		$this->db->where('DATE(tgl_input) >=', $bln_1);
		$this->db->where('DATE(tgl_input) <=', $bln_2);
		$this->db->where('type', 3);
		$this->db->where('jenis', $jenis);
		$query = $this->db->get();
		$no_ajuan = 1;
		if($query->num_rows() > 0) {
			$row = $query->row();
			$no_ajuan = $row->no_ajuan + 1;
		}
		// ajuan_id
		$ajuan_id = '';
		if($jenis == 'Sukarela') {
			$ajuan_id .= 'S';
		}
		if($jenis == 'Wajib') {
			$ajuan_id .= 'W';
		}
		$ajuan_id .= '.3';
		if(date("d") >= 21) {
			$ajuan_id .= '.' . substr(date("Y", strtotime("+1 month")), 2, 2);
			$ajuan_id .= '.' . date("m", strtotime("+1 month"));
		} else {
			$ajuan_id .= '.' . substr(date("Y"), 2, 2);
			$ajuan_id .= '.' . date("m");
		}
		$ajuan_id .= '.' . sprintf("%03d", $no_ajuan);

		$data = array (
			'no_ajuan'		=> $no_ajuan,
			'ajuan_id'		=> $ajuan_id,
			'anggota_id'	=> $user_id,
			'nominal'		=> $nominal,
			'jenis'			=> $jenis,
			'keterangan'	=> $this->input->post('keterangan'),
			'tgl_input'		=> date('Y-m-d H:i:s'),
			'tgl_update'	=> date('Y-m-d H:i:s'),
			'status'			=> 0,
			'type' 			=> 3
			);
		if($this->db->insert('tbl_pengajuan', $data)) {


			$pesan = 'Pengajuan Penarikan Simpanan '.$jenis.' telah dibuat';
			$this->notif_m->created_notif(1,$ajuan_id,$pesan,$user_id);
			$this->notif_m->created_notif(2,$ajuan_id,$pesan,$user_id,2);

			// ok
			return TRUE;
		} else {
			return FALSE;
		}
	}

	function pengajuan_perubahan_simpanan_simpan() {
		$user_id = $this->session->userdata('u_name');
		// last no
		$jenis = $this->input->post('jenis');
		if(empty($lama_ags)){
			$lama_ags = 0;
		}
		$nominal = preg_replace('/\D/', '', $this->input->post('nominal'));
		if(date("d") >= 21) {
			$bln_1 = date("Y-m") . '-21';
			$bln_2 = date("Y-m", strtotime("+1 month")) . '-20';
		} else {
			$bln_1 = date("Y-m", strtotime("-1 month")) . '-21';
			$bln_2 = date("Y-m") . '-20';
		}
		$this->db->select_max('no_ajuan');
		$this->db->from('tbl_pengajuan');
		$this->db->where('DATE(tgl_input) >=', $bln_1);
		$this->db->where('DATE(tgl_input) <=', $bln_2);
		$this->db->where('type', 4);
		$this->db->where('jenis', $jenis);
		$query = $this->db->get();
		$no_ajuan = 1;
		if($query->num_rows() > 0) {
			$row = $query->row();
			$no_ajuan = $row->no_ajuan + 1;
		}
		// ajuan_id
		$ajuan_id = '';
		if($jenis == 'Sukarela') {
			$ajuan_id .= 'S';
		}
		if($jenis == 'Wajib') {
			$ajuan_id .= 'W';
		}
		if($jenis == 'Pokok') {
			$ajuan_id .= 'P';
		}
		$ajuan_id .= '.4';
		if(date("d") >= 21) {
			$ajuan_id .= '.' . substr(date("Y", strtotime("+1 month")), 2, 2);
			$ajuan_id .= '.' . date("m", strtotime("+1 month"));
		} else {
			$ajuan_id .= '.' . substr(date("Y"), 2, 2);
			$ajuan_id .= '.' . date("m");
		}
		$ajuan_id .= '.' . sprintf("%03d", $no_ajuan);

		$data = array (
			'no_ajuan'		=> $no_ajuan,
			'ajuan_id'		=> $ajuan_id,
			'anggota_id'	=> $user_id,
			'nominal'		=> $nominal,
			'jenis'			=> $jenis,
			'keterangan'	=> $this->input->post('keterangan'),
			'tgl_input'		=> date('Y-m-d H:i:s'),
			'tgl_update'	=> date('Y-m-d H:i:s'),
			'status'			=> 0,
			'type' 			=> 4
			);
		if($this->db->insert('tbl_pengajuan', $data)) {


			$pesan = 'Pengajuan Perubahan '.$jenis.' telah dibuat';
			$this->notif_m->created_notif(1,$ajuan_id,$pesan,$user_id);
			$this->notif_m->created_notif(2,$ajuan_id,$pesan,$user_id,2);

			// ok
			return TRUE;
		} else {
			return FALSE;
		}
	}

	function pengajuan_batal($id) {
		$user_id = $this->session->userdata('u_name');
		$data = array('status' => 4);
		$this->db->where('id', $id);
		$this->db->where('status', 0);
		$this->db->where('anggota_id', $user_id);
		if($this->db->update('tbl_pengajuan', $data)) {
			// ok
			return TRUE;
		} else {
			return FALSE;
		}
	}

	function get_last_pengajuan($type=false) {
		$user_id = $this->session->userdata('u_name');
		$this->db->from('tbl_pengajuan');
		$this->db->where('anggota_id', $user_id);
		if($type){
			$this->db->where('type',$type);
		}
		$this->db->order_by('tgl_update', 'desc');
		$this->db->limit(1);
		$query = $this->db->get();
		if($query->num_rows() > 0){
			$out = $query->row();
			return $out;
		} else {
			return FALSE;
		}
	}

	public function import_db($data) {
		$this->load->model('general_m');

		if(is_array($data)) {

			$pair_arr = array();
			foreach ($data as $rows) {
				//if(trim($rows['A']) == '') { continue; }
				// per baris
				$pair = array();
				foreach ($rows as $key => $val) {
					if($key == 'A') { $pair['anggota_id'] = $val; }
					if($key == 'B') { 
						if($val == 'Ya'){
							$pair['anggota_baru'] = 1; 
						}else {
							$pair['anggota_baru'] = 0; 
						}
					}
					if($key == 'C') { $pair['nama'] = $val; }
					if($key == 'D') { $pair['nik'] = $val; }
					if($key == 'E') { $pair['npwp'] = $val; }
					if($key == 'F') { $pair['identitas'] = $val; }
					if($key == 'G') { $pair['jk'] = $val; }
					if($key == 'H') { $pair['tmp_lahir'] = $val; }
					if($key == 'I') { $pair['tgl_lahir'] = date('Y-m-d',strtotime($val)); }
					if($key == 'J') { $pair['status'] = $val; }
					if($key == 'K') { 
						$pair['departement'] = $this->general_m->get_departement($val,true);
					}
					if($key == 'L') { $pair['pekerjaan'] = $val; }
					if($key == 'M') { $pair['agama'] = $val; }
					if($key == 'N') { $pair['alamat'] = $val; }
					if($key == 'O') { $pair['kota'] = $val; }
					if($key == 'P') { $pair['notelp'] = $val; }
					if($key == 'Q') { $pair['tgl_daftar'] = date('Y-m-d',strtotime($val)); }
					if($key == 'R') {
						if($val == 'Anggota'){
						 	$pair['tgl_daftar'] = 2; 
						}else {
							$pair['tgl_daftar'] = 1; 
						}
					}
					if($key == 'S') { $pair['nama_bank'] = $val; }
					if($key == 'T') { $pair['no_rekening'] = $val; }
					if($key == 'U') { $pair['nama_rekening'] = $val; }
					if($key == 'V') { $pair['gaji'] = $val; }
					if($key == 'W') { $pair['simpanan_pokok'] = $val; }
					if($key == 'X') { $pair['simpanan_wajib'] = $val; }
					if($key == 'Y') { $pair['simpanan_sukarela'] = $val; }
					if($key == 'Z') { $pair['pass_word'] = sha1('nsi' . $val); }
					if($key == 'AA') { 
						if($val == 'Aktif'){
							$pair['aktif'] = 'Y'; 
						}else if($val == 'Pasif'){
							$pair['aktif'] = 'P'; 
						}else {
							$pair['aktif'] = 'N'; 
						}
					}
				}
				// $pair['jabatan_id'] = 2;
				$pair_arr[] = $pair;
			}
			//var_dump($pair_arr);
			//return 1;
			return $this->db->insert_batch('tbl_anggota', $pair_arr);
		} else {
			return FALSE;
		}
	}


	public function ubah_pic() {
		$out = array('error' => '', 'success' => '');
		$user_id = $this->session->userdata('u_name');
		$this->db->select('file_pic');
		$this->db->from('tbl_anggota');
		$this->db->where('id', $user_id);
		$query = $this->db->get();
		$row = $query->row();

		$file_lama = $row->file_pic;

		$config['upload_path'] = FCPATH . 'uploads/anggota/';
		$config['file_name'] = uniqid();
		$config['overwrite'] = FALSE;
		$config["allowed_types"] = 'jpg|jpeg|png|gif';
		$config["max_size"] = 1024;
		$config["max_width"] = 2000;
		$config["max_height"] = 2000;
		$this->load->library('upload', $config);

		if(!$this->upload->do_upload()) {
			$out['error'] = $this->upload->display_errors();
		} else {
			$config['image_library'] = 'gd2';
			$config['source_image'] = $this->upload->upload_path.$this->upload->file_name;
			$config['maintain_ratio'] = TRUE;
			$config['width'] = 250;
			$config['height'] = 250;
			$config['overwrite'] = TRUE;
			$this->load->library('image_lib',$config); 

			if ( !$this->image_lib->resize()){
				$out['error'] = $this->image_lib->display_errors();
			} else {
				//success
				$data = array('file_pic' => $this->upload->file_name);
				$this->db->where('id', $user_id);
				$this->db->update('tbl_anggota', $data);

				// hapus file lama
				if($file_lama != '') {
					$file_lama_f = FCPATH . '/uploads/anggota/'.$file_lama;
					if(file_exists($file_lama_f)) {
						if(unlink($file_lama_f)) {
							// DELETED
						} else {
							// NOT DELETED
						}
					}
				}
				$out['success'] = 'OK';
			}
		}
		return $out;
	}

	public function logout() {
		$this->session->unset_userdata(array('u_name' => '', 'login' => FALSE));
		$this->session->sess_destroy();
	}

	function get_pengajuan() {
		$this->load->helper('fungsi');
		$user_id = $this->session->userdata('u_name');

		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$limit = isset($_POST['limit']) ? $_POST['limit'] : 10;
		$search = isset($_POST['search']) ? $_POST['search'] : '';
		
		$where = " AND anggota_id = " . $user_id." AND type = 1";
		$order_by = " ORDER BY tgl_input DESC";
		$sql_limit = " LIMIT ".$offset.",".$limit." ";
		
		$sql_tampil = "SELECT * FROM tbl_pengajuan WHERE 1=1 ".$where." ".$order_by." ".$sql_limit."";
		$query = $this->db->query($sql_tampil);
		$data_list = $query->result();

		$sql_total = "SELECT id FROM tbl_pengajuan WHERE 1=1 ".$where." ";
		$query = $this->db->query($sql_total);
		$total = $query->num_rows();

		// 
		$data_list_i = array();
		foreach ($data_list as $key => $val) {
			$tgl_arr = explode(' ', $val->tgl_input);
			$tgl = $tgl_arr[0];
			$val->tgl_input_txt = jin_date_ina($tgl);
			$val->tgl_update_txt = jin_date_ina($tgl);
			$val->tgl_cair_txt = jin_date_ina($val->tgl_cair);
			$val->tgl_input = substr($val->tgl_input, 0, 16);
			$val->tgl_update = substr($val->tgl_update, 0, 16);
			$val->nominal = number_format($val->nominal);


			$check_cair = $this->db->select('tgl_pinjam')->get_where('tbl_pinjaman_h',array('ajuan_id'=>$val->ajuan_id))->row_array();
			$val->tgl_cair = '-';
			if(!empty($check_cair)){
				$val->tgl_cair = jin_date_ina(date('Y-m-d',strtotime($check_cair['tgl_pinjam'])));
			}


			$data_list_i[$key] = $val;
		}

		$out = array('rows' => $data_list_i, 'total' => $total);
		return $out;
	}

	function get_pengajuan_simpanan() {
		$this->load->helper('fungsi');
		$user_id = $this->session->userdata('u_name');

		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$limit = isset($_POST['limit']) ? $_POST['limit'] : 10;
		$search = isset($_POST['search']) ? $_POST['search'] : '';
		
		$where = " AND anggota_id = " . $user_id." AND type = 2";
		$order_by = " ORDER BY tgl_input DESC";
		$sql_limit = " LIMIT ".$offset.",".$limit." ";
		
		$sql_tampil = "SELECT * FROM tbl_pengajuan WHERE 1=1 ".$where." ".$order_by." ".$sql_limit."";
		$query = $this->db->query($sql_tampil);
		$data_list = $query->result();

		$sql_total = "SELECT id FROM tbl_pengajuan WHERE 1=1 ".$where." ";
		$query = $this->db->query($sql_total);
		$total = $query->num_rows();

		// 
		$data_list_i = array();
		foreach ($data_list as $key => $val) {
			$tgl_arr = explode(' ', $val->tgl_input);
			$tgl = $tgl_arr[0];
			$val->tgl_input_txt = jin_date_ina($tgl);
			$val->tgl_update_txt = jin_date_ina($tgl);
			$val->tgl_cair_txt = jin_date_ina($val->tgl_cair);
			$val->tgl_input = substr($val->tgl_input, 0, 16);
			$val->tgl_update = substr($val->tgl_update, 0, 16);
			$val->nominal = number_format($val->nominal);
			$data_list_i[$key] = $val;
		}

		$out = array('rows' => $data_list_i, 'total' => $total);
		return $out;
	}


	function get_pengajuan_penarikan_simpanan() {
		$this->load->helper('fungsi');
		$user_id = $this->session->userdata('u_name');

		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$limit = isset($_POST['limit']) ? $_POST['limit'] : 10;
		$search = isset($_POST['search']) ? $_POST['search'] : '';
		
		$where = " AND anggota_id = " . $user_id." AND type = 3";
		$order_by = " ORDER BY tgl_input DESC";
		$sql_limit = " LIMIT ".$offset.",".$limit." ";
		
		$sql_tampil = "SELECT * FROM tbl_pengajuan WHERE 1=1 ".$where." ".$order_by." ".$sql_limit."";
		$query = $this->db->query($sql_tampil);
		$data_list = $query->result();

		$sql_total = "SELECT id FROM tbl_pengajuan WHERE 1=1 ".$where." ";
		$query = $this->db->query($sql_total);
		$total = $query->num_rows();

		// 
		$data_list_i = array();
		foreach ($data_list as $key => $val) {
			$tgl_arr = explode(' ', $val->tgl_input);
			$tgl = $tgl_arr[0];
			$val->tgl_input_txt = jin_date_ina($tgl);
			$val->tgl_update_txt = jin_date_ina($tgl);
			$val->tgl_cair_txt = jin_date_ina($val->tgl_cair);
			$val->tgl_input = substr($val->tgl_input, 0, 16);
			$val->tgl_update = substr($val->tgl_update, 0, 16);
			$val->nominal = number_format($val->nominal);
			$data_list_i[$key] = $val;
		}

		$out = array('rows' => $data_list_i, 'total' => $total);
		return $out;
	}


	function get_pengajuan_perubahan_simpanan() {
		$this->load->helper('fungsi');
		$user_id = $this->session->userdata('u_name');

		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$limit = isset($_POST['limit']) ? $_POST['limit'] : 10;
		$search = isset($_POST['search']) ? $_POST['search'] : '';
		
		$where = " AND anggota_id = " . $user_id." AND type = 4";
		$order_by = " ORDER BY tgl_input DESC";
		$sql_limit = " LIMIT ".$offset.",".$limit." ";
		
		$sql_tampil = "SELECT * FROM tbl_pengajuan WHERE 1=1 ".$where." ".$order_by." ".$sql_limit."";
		$query = $this->db->query($sql_tampil);
		$data_list = $query->result();

		$sql_total = "SELECT id FROM tbl_pengajuan WHERE 1=1 ".$where." ";
		$query = $this->db->query($sql_total);
		$total = $query->num_rows();

		// 
		$data_list_i = array();
		foreach ($data_list as $key => $val) {
			$tgl_arr = explode(' ', $val->tgl_input);
			$tgl = $tgl_arr[0];
			$val->tgl_input_txt = jin_date_ina($tgl);
			$val->tgl_update_txt = jin_date_ina($tgl);
			$val->tgl_cair_txt = jin_date_ina($val->tgl_cair);
			$val->tgl_input = substr($val->tgl_input, 0, 16);
			$val->tgl_update = substr($val->tgl_update, 0, 16);
			$val->nominal = number_format($val->nominal);
			$data_list_i[$key] = $val;
		}

		$out = array('rows' => $data_list_i, 'total' => $total);
		return $out;
	}

	function get_simpanan() {
		$this->load->helper('fungsi');
		$user_id = $this->session->userdata('u_name');

		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$limit = isset($_POST['limit']) ? $_POST['limit'] : 10;
		$search = isset($_POST['search']) ? $_POST['search'] : '';
		
		$where = " AND anggota_id = " . $user_id;
		$order_by = " ORDER BY tgl_transaksi DESC";
		$sql_limit = " LIMIT ".$offset.",".$limit." ";
		
		$sql_tampil = "SELECT * FROM tbl_trans_sp WHERE 1=1 ".$where." ".$order_by." ".$sql_limit."";
		$query = $this->db->query($sql_tampil);
		$data_list = $query->result();

		$sql_total = "SELECT id FROM tbl_trans_sp WHERE 1=1 ".$where." ";
		$query = $this->db->query($sql_total);
		$total = $query->num_rows();

		// 
		$data_list_i = array();
		foreach ($data_list as $key => $val) {
			$tgl_arr = explode(' ', $val->tgl_transaksi);
			$tgl = $tgl_arr[0];
			$val->tgl_transaksi = jin_date_ina($tgl);
			$val->jumlah = number_format($val->jumlah);
			$data_list_i[$key] = $val;
		}

		$out = array('rows' => $data_list_i, 'total' => $total);
		return $out;
	}

	function get_pinjaman() {
		$this->load->helper('fungsi');
		$user_id = $this->session->userdata('u_name');

		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$limit = isset($_POST['limit']) ? $_POST['limit'] : 10;
		$search = isset($_POST['search']) ? $_POST['search'] : '';
		
		$where = " AND anggota_id = " . $user_id;
		$order_by = " ORDER BY tgl_pinjam DESC";
		$sql_limit = " LIMIT ".$offset.",".$limit." ";
		
		$sql_tampil = "SELECT * FROM v_hitung_pinjaman WHERE 1=1 ".$where." ".$order_by." ".$sql_limit."";
		$query = $this->db->query($sql_tampil);
		$data_list = $query->result();

		$sql_total = "SELECT id FROM v_hitung_pinjaman WHERE 1=1 ".$where." ";
		$query = $this->db->query($sql_total);
		$total = $query->num_rows();

		// 
		$data_list_i = array();
		foreach ($data_list as $key => $val) {
			$tgl_arr = explode(' ', $val->tgl_pinjam);
			$tgl = $tgl_arr[0];
			$val->tgl_pinjam = jin_date_ina($tgl, 'pendek');
			$tgl_arr = explode(' ', $val->tempo);
			$tgl = $tgl_arr[0];
			$val->tempo = jin_date_ina($tgl, 'pendek');
			$val->jumlah = number_format($val->jumlah);
			$val->biaya_adm = number_format($val->biaya_adm);
			$val->pokok_angsuran = number_format($val->pokok_angsuran);
			$val->bunga_pinjaman = number_format($val->bunga_pinjaman);
			$val->ags_per_bulan = number_format($val->ags_per_bulan);
			$val->tagihan = number_format($val->tagihan);
			$data_list_i[$key] = $val;
		}

		$out = array('rows' => $data_list_i, 'total' => $total);
		return $out;

	}


	function get_notif($jenis) {
		$this->load->helper('fungsi');
		$user_id = $this->session->userdata('u_name');

		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$limit = isset($_POST['limit']) ? $_POST['limit'] : 10;
		$search = isset($_POST['search']) ? $_POST['search'] : '';
		
		$where = " AND anggota_id = " . $user_id;
		$order_by = " ORDER BY created_at DESC";
		$sql_limit = " LIMIT ".$offset.",".$limit." ";
		
		$sql_tampil = "SELECT * FROM tbl_notif WHERE type=2 AND jenis_ajuan=".$jenis." ".$where." ".$order_by." ".$sql_limit."";
		$query = $this->db->query($sql_tampil);
		$data_list = $query->result();

		$sql_total = "SELECT notif_id FROM tbl_notif WHERE type=2 AND jenis_ajuan=".$jenis." ".$where." ";
		$query = $this->db->query($sql_total);
		$total = $query->num_rows();

		// 
		$data_list_i = array();
		foreach ($data_list as $key => $val) {

			$tgl_arr = explode(' ', $val->created_at);
			$tgl = $tgl_arr[0];
			$val->tgl = jin_date_ina($tgl);

			$data_list_i[$key] = $val;
		}

		$out = array('rows' => $data_list_i, 'total' => $total);
		return $out;
	}

	function get_bayar() {
		$this->load->helper('fungsi');
		$user_id = $this->session->userdata('u_name');

		$offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
		$limit = isset($_POST['limit']) ? $_POST['limit'] : 10;
		$search = isset($_POST['search']) ? $_POST['search'] : '';
		
		$where = " AND tbl_pinjaman_h.anggota_id = " . $user_id;
		$order_by = " ORDER BY tbl_pinjaman_d.tgl_bayar DESC";
		$sql_limit = " LIMIT ".$offset.",".$limit." ";
		
		$sql_tampil = "SELECT 
				tbl_pinjaman_d.tgl_bayar AS tgl_bayar,
				tbl_pinjaman_d.angsuran_ke AS angsuran_ke,
				tbl_pinjaman_d.jumlah_bayar AS jumlah_bayar,
				tbl_pinjaman_d.denda_rp AS denda_rp,
				tbl_pinjaman_d.ket_bayar AS ket_bayar,
				tbl_pinjaman_d.keterangan AS keterangan
			 FROM tbl_pinjaman_d 
			 LEFT JOIN tbl_pinjaman_h ON tbl_pinjaman_h.id = tbl_pinjaman_d.pinjam_id
			 WHERE 1=1 
			 ".$where." ".$order_by." ".$sql_limit."";
		$query = $this->db->query($sql_tampil);
		$data_list = $query->result();

		$sql_total = "SELECT tbl_pinjaman_d.id 
			FROM tbl_pinjaman_d 
			LEFT JOIN tbl_pinjaman_h ON tbl_pinjaman_h.id = tbl_pinjaman_d.pinjam_id
			WHERE 1=1 ".$where." ";
		$query = $this->db->query($sql_total);
		$total = $query->num_rows();

		// 
		$data_list_i = array();
		foreach ($data_list as $key => $val) {
			$tgl_arr = explode(' ', $val->tgl_bayar);
			$tgl = $tgl_arr[0];
			$val->tgl_bayar = jin_date_ina($tgl, 'pendek');
			$val->jumlah_bayar = number_format($val->jumlah_bayar);
			$val->denda_rp = number_format($val->denda_rp);
			$data_list_i[$key] = $val;
		}

		$out = array('rows' => $data_list_i, 'total' => $total);
		return $out;		

	}


	function get_simpanan_by_anggota($anggota_id,$jenis_id)
	{
		$out = 0;

		
		
		$query = $this->db->select('jumlah, dk')
						->where(array('anggota_id'=>$anggota_id,'jenis_id'=>$jenis_id))
						->get('tbl_trans_sp');
		if($query->num_rows() > 0){
			$setor = 0;
			$tarik = 0;
			foreach ($query->result() as $key => $row) {
				if($row->dk == 'K'){
					$tarik += $row->jumlah;
				}
				if($row->dk == 'D'){
					$setor += $row->jumlah;
				}
			}
			$out = $setor - $tarik;
		}

		return $out;

	}

	function get_simpanan_bulan_by_anggota($anggota_id,$jenis)
	{
		$out = 0;

		
		if(strpos($jenis, 'Sukarela') !== false ){
			$jenis_id = 'simpanan_sukarela';
		}else if(strpos($jenis, 'Wajib') !== false ){
			$jenis_id = 'simpanan_wajib';
		}else {
			$jenis_id = 'simpanan_pokok';
		}

		$query = $this->db->select($jenis_id.' as simpanan')
						->where(array('id'=>$anggota_id))
						->get('tbl_anggota')->row_array();

		if(!empty($query)){
			$out = $query['simpanan'];
		}
		return $out;

	}

}
