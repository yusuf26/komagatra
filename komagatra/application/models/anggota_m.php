
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Anggota_m extends CI_Model {

	public function __construct(){
		parent::__construct();
	}

	function get_data_list($offset, $limit, $q='', $sort, $order) {
		$sql = "SELECT * FROM tbl_anggota ";
		if(is_array($q)) {
			if($q['kode_transaksi'] != '') {
				$sql .=" AND (ajuan_id LIKE '".$q['kode_transaksi']."') ";
			}

		}
		$result['count'] = $this->db->query($sql)->num_rows();
		$sql .=" ORDER BY {$sort} {$order} ";
		$sql .=" LIMIT {$offset},{$limit} ";
		$result['data'] = $this->db->query($sql)->result();
		return $result;
	}
}