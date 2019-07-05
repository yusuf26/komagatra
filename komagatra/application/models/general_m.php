<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class General_m extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

	//panggil data anggota untuk combo 
	function get_data_anggota_ajax($q) {
		$sql = "SELECT * FROM tbl_anggota WHERE aktif='Y' ";
		if($q !='') {
			$sql .=" AND (identitas LIKE '%{$q}%' OR nama LIKE '%{$q}%') ";
		}
		$result['count'] = $this->db->query($sql)->num_rows();
		$sql .=" ORDER BY identitas ASC ";
		$sql .=" LIMIT 50 ";
		$result['data'] = $this->db->query($sql)->result();
		return $result;
	}

	//panggil data anggota berdasarkan ID
	function get_data_anggota($id) {
		$this->db->select('*');
		$this->db->from('tbl_anggota');
		$this->db->where('id',$id);
		$query = $this->db->get();
		if($query->num_rows()>0){
			$out = $query->row();
			return $out;
		} else {
			return FALSE;
		}
	}

	//panggil data anggota tanpa ID
	function get_anggota() {
		$this->db->select('*');
		$this->db->from('tbl_anggota');
		$this->db->where('aktif','Y');
		$this->db->where('tipe_anggota','Anggota');
		$query = $this->db->get();
		if($query->num_rows()>0){
			$out = $query->result_array();
			return $out;
		} else {
			return FALSE;
		}
	}

	//hitung jumlah anggota
	function get_jml_anggota($id) {
		$this->db->select('id');
		$this->db->from('tbl_anggota');
		$this->db->where('aktif','Y');
		$query = $this->db->get();
		return $query->num_rows();
	}

	//panggil data jenis simpanan dengan id
	function get_jns_simpanan($id) {
		$this->db->select('*');
		$this->db->from('jns_simpan');
		$this->db->where('id',$id);
		$this->db->where('tampil','Y');
		$query = $this->db->get();
		if($query->num_rows()>0){
			$out = $query->row();
			return $out;
		} else {
			return FALSE;
		}
	}

	function get_jns_simpanan_all() {
		$this->db->select('*');
		$this->db->from('jns_simpan');
		$this->db->where('tampil','Y');
		$query = $this->db->get();
		if($query->num_rows()>0){
			$out = $query->result_array();
			return $out;
		} else {
			return FALSE;
		}
	}

	function get_jns_pinjaman_all() {
		$this->db->select('*');
		$this->db->from('jns_pinjaman');
		$this->db->where('aktif','Y');
		$query = $this->db->get();
		if($query->num_rows()>0){
			$out = $query->result_array();
			return $out;
		} else {
			return FALSE;
		}
	}

	//panggil data jenis simpanan
	function get_id_simpanan() {
		$this->db->select('*');
		$this->db->from('jns_simpan');
		$this->db->where('tampil', 'Y');
		$this->db->order_by('id', 'ASC');
		$query = $this->db->get();
		if($query->num_rows()>0){
			$out = $query->result();
			return $out;
		} else {
			return array();
		}
	}

	//menghitung jumlah pinjaman seluruhnya
	function get_total_pinjaman() {
		$this->db->select('SUM(tagihan) AS total');
		$this->db->from('v_hitung_pinjaman');
		$query = $this->db->get();
		return $query->row();
	}

	//menghitung jumlah yang sudah dibayar dengan id pinjam
	function get_jml_bayar($id) {
		$this->db->select('SUM(jumlah_bayar + simpan_wajib) AS total');
		$this->db->from('tbl_pinjaman_d');
		$this->db->where('pinjam_id',$id);
		$query = $this->db->get();
		return $query->row();
	}

	//menghitung jumlah yang sudah dibayar seluruhnya
	function get_total_dibayar() {
		$this->db->select('SUM(jumlah_bayar) AS total');
		$this->db->from('tbl_pinjaman_d');
		$query = $this->db->get();
		return $query->row();
	}

	//menghitung jumlah denda harus dibayar dengan ID pinjam
	function get_jml_denda($id) {
		$this->db->select('SUM(denda_rp) AS total_denda');
		$this->db->from('tbl_pinjaman_d');
		$this->db->where('pinjam_id',$id);
		$query = $this->db->get();
		return $query->row();
	}

	//menghitung jumlah   denda seluruhnya
	function get_total_denda() {
		$this->db->select('SUM(denda_rp) AS total_denda');
		$this->db->from('tbl_pinjaman_d');
		$query = $this->db->get();
		return $query->row();
	}

	//mecari banyaknya data yg diinput pinjaman detail
	function get_record_bayar($id) {
		$this->db->select('id');
		$this->db->from('tbl_pinjaman_d');
		$this->db->where('pinjam_id',$id);
		$this->db->where('ket_bayar','Angsuran');
		$query = $this->db->get();
		return $query->num_rows();
	}

	//ambil data pinjaman header berdasarkan ID
	function get_data_pinjam($id) {
		$this->db->select('v_hitung_pinjaman.*,tbl_pengajuan.jenis');
		$this->db->from('v_hitung_pinjaman');
		$this->db->join('tbl_pengajuan','tbl_pengajuan.ajuan_id = v_hitung_pinjaman.ajuan_id','left');
		$this->db->where('v_hitung_pinjaman.id',$id);
		$query = $this->db->get();
		if($query->num_rows() > 0){
			$out = $query->row();
			return $out;
		} else {
			return FALSE;
		}
	}

	//panggil data pinjaman tanpa id
	function data_pinjaman() {
		$this->db->select('*');
		$this->db->from('v_hitung_pinjaman');
		$this->db->order_by('id', 'ASC');
		$query = $this->db->get();
		if($query->num_rows()>0){
			$out = $query->result();
			return $out;
		} else {
			return FALSE;
		}
	}


	//panggil data pinjaman detail berdasarkan pinjam ID
	function get_data_pembayaran($id) {
		$this->db->select('*');
		$this->db->from('tbl_pinjaman_d');
		$this->db->where('pinjam_id', $id);
		$this->db->order_by('id', 'ASC');
		$query = $this->db->get();
		if($query->num_rows() > 0){
			$out = $query->result();
			return $out;
		} else {
			return FALSE;
		}
	}

	//panggil data pinjaman detail berdasarkan ID
	function get_data_pembayaran_by_id($id) {
		$this->db->select('*');
		$this->db->from('tbl_pinjaman_d');
		$this->db->where('id', $id);
		$query = $this->db->get();
		if($query->num_rows() > 0){
			$out = $query->row();
			return $out;
		} else {
			return FALSE;
		}
	}

	//panggil data denda dan tempo 
	function get_semua_denda_by_pinjaman($master_id) {
		$pinjam = $this->get_data_pinjam($master_id);
		$this->db->select('MAX(angsuran_ke) AS angsuran_ke');
		$this->db->from('tbl_pinjaman_d');
		$this->db->where('pinjam_id', $master_id);
		$query = $this->db->get();
		$ags = $query->row();
		$ags_ke = $ags->angsuran_ke;

		$sisa_ags_det = $pinjam->lama_angsuran - ($ags_ke) ;
		// DENDA
		$denda_semua = 0;
		$tgl_pinjam = substr($pinjam->tgl_pinjam, 0, 7) . '-01';
		$tgl_tempo = date('Y-m-d', strtotime("+".$ags_ke." months", strtotime($tgl_pinjam)));
		$tgl_bayar = date('Y-m-d');
		$data_bunga_arr = $this->bunga_m->get_key_val();
		$denda_hari = $data_bunga_arr['denda_hari'];
		$tgl_tempo = str_replace('-', '', $tgl_tempo);
		$tgl_bayar = str_replace('-', '', $tgl_bayar);
		$tgl_toleransi = $tgl_bayar - ($tgl_tempo - 1);
		if ( $tgl_toleransi > $denda_hari ) { // 20140615 - 20140600
			$denda_semua = ($data_bunga_arr['denda'] * $sisa_ags_det);
		}
		return $denda_semua;
	}	


	function update_cetak()
	{
		$tbl = $this->input->post('tbl');
		$id_txt = $this->input->post('id_txt');
		$nama_operator = $this->input->post('nama_operator');
		$nama_admin = $this->input->post('nama_admin');
		$nama_member = $this->input->post('nama_member');

		$data = array(
			'update_data' => date('Y-m-d H:i:s'),
			'nama_operator' => $nama_operator,
			'nama_admin' => $nama_admin,
			'nama_member' => $nama_member
		);

		if($tbl == 'A'){
			$query =$this->db->update('tbl_pinjaman_h',$data,array('kd_transaksi'=>$id_txt));
		}
		if($tbl == 'B'){
			$query =$this->db->update('tbl_pinjaman_d',$data,array('kd_transaksi'=>$id_txt));
		}
		if($tbl == 'C'){
			$query =$this->db->update('tbl_trans_sp',$data,array('kd_transaksi'=>$id_txt));
		}
		if($tbl == 'D'){
			$query = $this->db->update('tbl_trans_kas',$data,array('kd_transaksi'=>$id_txt));
		}

		return $id_txt;
	}

	function kocak_model($kas,$type,$q,$sort=false,$order=false,$limit=false,$offset=false)
	{
		$this->db->select('vp.*');
		$this->db->from('v_transaksi vp');
		if($type == 0){
			$this->db->where_in('vp.tbl',array('D','A','C'));
			$this->db->where('vp.debet',0);
			$this->db->join('nama_kas_tbl','nama_kas_tbl.id = vp.dari_kas','left');	
		}else {
			$this->db->join('nama_kas_tbl','nama_kas_tbl.id = vp.untuk_kas','left');
			$this->db->where_in('vp.tbl',array('B','D','C'));
			$this->db->where('vp.kredit',0);
		}
		$this->db->where('nama_kas_tbl.type',$kas);
		$this->db->where('nama_kas_tbl.aktif','Y');
		if(is_array($q)) {
			if($q['kode_transaksi'] != '') {
				$kode = substr($q['kode_transaksi'], 3);
				$kode_transaksi= $kode_transaksi * 1;
				$this->db->like('vp.id',$kode_transaksi);
			} else {
				if($q['tgl_dari'] != '' && $q['tgl_sampai'] != '') {
					$this->db->where('DATE(vp.tgl) >=',$q['tgl_dari']);
					$this->db->where('DATE(vp.tgl) <=',$q['tgl_sampai']);
				}
			}
		}

		if($sort){
			$this->db->order_by($sort,$order);
			$this->db->limit($limit,$offset);
		}
		return $this->db->get();
	}

	function lap_data_trans($type,$tbl='',$kode='',$tgl_dari='',$tgl_sampai='')
	{

		$output = false;
		$this->db->select('vp.*');
		if($tbl){
			$this->db->where('tbl',$tbl);
		}
		if($type == 0){
			$this->db->where('vp.debet',0);
		}else {
			$this->db->where('vp.kredit',0);
		}

		if($kode != '') {
			$this->db->where('vp.kd_transaksi',$kode);
		}

		if($tgl_dari != '' && $tgl_sampai != '') {
			$this->db->where('DATE(vp.tgl) >=',$tgl_dari);
			$this->db->where('DATE(vp.tgl) <=',$tgl_sampai);
		}
					
		$this->db->order_by('vp.tgl','desc');
		$query = $this->db->get('v_transaksi vp');
		if($query->num_rows() > 0){
			$output = $query->result();
		}
		return $output;
	}


	function get_first_year()
	{
		$date = new DateTime('first day of january this year');
		return $date->format('Y-m-d');
	}

	function get_last_year()
	{
		$date = new DateTime('last day of december this year');
		return $date->format('Y-m-d');
	}


	function get_departement($id,$by_name=false)
	{
		if($by_name){
			$query = $this->db->get_where('departement',array('departement'=>$id))->row_array();
			return $query['id_departement'];
		}else {
			$query = $this->db->get_where('departement',array('id_departement'=>$id))->row_array();
			if(!empty($query)){
				return $query['departement'];
			}else  {
				return '';
			}
		}
	}


	function anggota_list()
	{
		$this->db->select('anggota_id,id,nama');
		$this->db->where('aktif','Y');
		$this->db->from('tbl_anggota');
		$query = $this->db->get();
		if($query->num_rows()>0){
			$out = $query->result_array();
			return $out;
		} else {
			return FALSE;
		}
	}

	function KodeTransaksi()
	{
		$get_trans = $this->db->order_by('tgl','desc')->select('kd_transaksi')->get('v_transaksi');
		$kd_transaksi = 'KOPS/'.date('y').'/'.date('m').'/'.date('d').'/1';

		if($get_trans->num_rows() > 0){
			

			$arr_kd = explode('/', $get_trans->row_array()['kd_transaksi']);
			
			if($arr_kd[1] != date('y') || $arr_kd[2] != date('m')){
				$no =1;
			}else {
				$no = intval($arr_kd[4]) + 1;
			}
			$kd_transaksi = 'KOPS/'.date('y').'/'.date('m').'/'.date('d').'/'.$no;
		}

		return $kd_transaksi;
		
	}


	// function get_kode_transaksi()
	// {

	// }


	function get_ajuan($type)
	{
		$no_ajuan =1;

		$this->db->select_max('no_ajuan');
		$this->db->from('tbl_pengajuan');
		$this->db->where('type', $type);
		$query = $this->db->get();
		if($query->num_rows() > 0){
			$no_ajuan = $query->row()->no_ajuan + 1;
		}

		return $no_ajuan;
	}

	function get_ajuan_id($no_ajuan,$type,$kode_jenis)
	{
		$output = false;

		if(!empty($no_ajuan)){
			if($type == 1){
				$output = 'PP/';
			}else if($type == 2){
				$output = 'PS/';
			}else if($type == 3){
				$output = 'PT/';
			}else {
				$output = 'PR/';
			}
			$output .= $kode_jenis.'/';
			$output .= date("m").substr(date("Y"), 2, 2).'/';
			$output .= sprintf("%04d", $no_ajuan);
		}

		return $output; 

	}


	function get_urutan($type,$id)
	{
		$output = '-';

		$this->db->select('id');
		$this->db->where(array('type'=>$type,'status'=>0));
		$this->db->order_by('tgl_input','asc');
		$query = $this->db->get('tbl_pengajuan');
		if($query->num_rows() > 0){
			$arr = array();
			foreach ($query->result() as $key => $row) {
				$arr[$row->id] = $key + 1;
			}
			if(!empty($arr)){
				foreach ($arr as $k => $v) {
					if($id == $k){
						$output = $arr[$id];
					}
				}
			}
			 
		}
		return $output;	
	}


	function check_urutan($type)
	{
		$out = 1;

		$this->db->select('id');
		$this->db->where('type',$type);
		$this->db->where('status',0);
		$query = $this->db->get('tbl_pengajuan');
		if($query->num_rows() > 0){
			$out = $query->num_rows() + 1;
		}
		return $out;	
	}

	function get_kode_transaksi($type)
	{
		$output = false;


		$no_ajuan = 1;
		$this->db->select('MAX(SUBSTRING(kd_transaksi,10,4)) as kode',FALSE);
		$this->db->where('tbl',$type);
		$check_t = $this->db->get_where('v_transaksi');
		if($check_t->num_rows() > 0){
			$query = $check_t->row_array();

			// $arr_kd = explode('/', $query['kode']);
			$no_ajuan = intval($query['kode']) + 1;
		}
		if($type == 'A'){
			$output = 'TKP/';
		}else if($type == 'B'){
			$output = 'TDP/';
		}else if($type == 'C'){
			$output = 'TSP/';
		}else {
			$output = 'TKS/';
		}
		$output .= date("m").substr(date("Y"), 2, 2).'/';
		$output .= sprintf("%04d", $no_ajuan);

		return $output; 
	}


	function get_anggota_by_trans($type,$id)
	{

		$output =false;

		if($type == 'A'){
			$anggota_id = $this->db->select('anggota_id')->get_where('tbl_pinjaman_h',array('id'=>$id))->row_array()['anggota_id'];
			$output = $this->get_data_anggota($anggota_id)->nama;
		}else if($type == 'B'){
			$pinjam_id = $this->db->select('pinjam_id')->get_where('tbl_pinjaman_d',array('id'=>$id))->row_array()['pinjam_id'];
			$anggota_id = $this->db->select('anggota_id')->get_where('tbl_pinjaman_h',array('id'=>$pinjam_id))->row_array()['anggota_id'];
			$output = $this->get_data_anggota($anggota_id)->nama;
		}else if($type == 'C'){
			$anggota_id = $this->db->select('anggota_id')->get_where('tbl_trans_sp',array('id'=>$id))->row_array()['anggota_id'];
			$output = $this->get_data_anggota($anggota_id)->nama;
		}else {

			$anggota_id = $this->db->select('penyetor')->get_where('tbl_trans_kas',array('id'=>$id))->row_array();
			$get_name = $this->get_data_anggota($anggota_id['penyetor']);
			if($get_name){
				$output = $get_name->nama;
			}else {
				$output = $anggota_id['penyetor'];
			}
			
			
		}
		
		return $output;
	}



	function get_jenis_kas_name($id)
	{
		$output = false;
		
		$query = $this->db->select('nama')->get_where('nama_kas_tbl',array('id'=>$id));
		if($query->num_rows() > 0){
			$output = $query->row_array();
		}

		return $output;
	}


	function PostLainLain($total_akun,$tgl,$kd_transaksi,$penyetor,$keterangan,$kas_id,$username)
	{
		for ($i=1; $i < $total_akun; $i++) { 
			$jns_trans = $this->input->post('akun_'.$i);
			$dk = $this->input->post('dk_'.$i);
			$jumlah = str_replace('.', '', $this->input->post('jumlah_'.$i));

			$akun = 'Pemasukan';
			$from_kas = 'untuk_kas_id';
			$status = 1;
			if($dk == 'K'){
				$akun = 'Pengeluaran';
				$from_kas = 'dari_kas_id';
				$status = 0;
			}
			$data = array(
				'tgl_catat' 	=> $tgl.' '.date('H:i:s'),
				'kd_transaksi'	=> $kd_transaksi,
				'jumlah' 		=> $jumlah,
				'penyetor' 		=> $penyetor,
				'keterangan' 	=> $keterangan,
				'akun' 			=> $akun,
				$from_kas		=> $kas_id,
				'jns_trans'		=> $jns_trans,
				'dk' 			=> $dk,
				'user_name'		=> $username,
				'status_kas' 	=> 1,
				'status'		=> $status
			);

			$this->db->insert('tbl_trans_kas',$data);
		}
	}


}






















