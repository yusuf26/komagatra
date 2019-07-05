<?php
if (!function_exists('jin_date_ina')) {
	function jin_date_ina($date_sql, $tipe = 'full', $time = false,$show_bulan=false) {
		$date = '';
		if($tipe == 'full') {
			$nama_bulan = array(1=>"Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
		} else {
			$nama_bulan = array(1=>"Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Ags", "Sep", "Okt", "Nov", "Des");
		}
		if($time) {
			$exp = explode(' ', $date_sql);
			$exp = explode('-', $exp[0]);
			if(count($exp) == 3) {
				$bln = $exp[1] * 1;
				$date = $exp[2].' '.$nama_bulan[$bln].' '.$exp[0];
			}		
			$exp_time = $exp = explode(' ', $date_sql);
			$date .= ' jam ' . substr($exp_time[1], 0, 5);
		} else {
			$exp = explode('-', $date_sql);
			if(count($exp) == 3) {
				$bln = $exp[1] * 1;
				if($bln > 0) {
					$date = $exp[2].' '.$nama_bulan[$bln].' '.$exp[0];
				}
			}
		}

		if($show_bulan){
			$date = $nama_bulan[$bln].' '.$exp[0];
		}
		return $date;
	}
}

if (!function_exists('jin_nama_bulan')) {
	function jin_nama_bulan($bln, $tipe='full') {
		$bln = $bln * 1;
		if($tipe == 'full') {
			$nama_bulan = array(1=>"Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
		} else {
			$nama_bulan = array(1=>"Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Ags", "Sep", "Okt", "Nov", "Des");
		}
		return $nama_bulan[$bln];
	}
}

if (!function_exists('bulan_to_date')) {
	function bulan_to_date($tgl, $tipe='full') {
		
		if($tipe == 'full') {
			$nama_bulan = array(1=>"Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
		} else {
			$nama_bulan = array(1=>"Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Ags", "Sep", "Okt", "Nov", "Des");
		}
		$tgl = explode(' ', $tgl);
		$m = $tgl[0];
		$y = $tgl[1];
		foreach ($nama_bulan as $key => $value) {
			if($m == $value){
				$m = $key;
			}
		}

		return date('Y-m-d',strtotime($m.' '.$y));
	}
}


if (!function_exists('bulan_to_date_2')) {
	function bulan_to_date_2($tgl, $tipe='full') {
		
		if($tipe == 'full') {
			$nama_bulan = array(1=>"Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
		} else {
			$nama_bulan = array(1=>"Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Ags", "Sep", "Okt", "Nov", "Des");
		}
		$tgl = explode(' ', $tgl);
		$m = $tgl[0];
		$y = $tgl[1];
		foreach ($nama_bulan as $key => $value) {
			if($m == $value){
				$m = $key;
			}
		}

		return $m;
	}
}

if (!function_exists('date_to_bulan')) {
	function date_to_bulan($tgl, $tipe='full') {
		

		if($tipe == 'full') {
			$nama_bulan = array(1=>"Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
		} else {
			$nama_bulan = array(1=>"Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Ags", "Sep", "Okt", "Nov", "Des");
		}
		foreach ($nama_bulan as $key => $value) {
			if($tgl == $key){
				$tgl = $value;
			}
		}

		return $tgl;
	}
}

if (!function_exists('nsi_round')) {
	function nsi_round($x) {
		//$x = ceil($x / 100) * 100;
		return $x;
	}
}

if (!function_exists('cetakStatus')) {
	function cetakStatus($x) {
		//$x = ceil($x / 100) * 100;
		// return $x;
		if($x == 0){
			return '<label class="label label-primary">Menunggu</label>';
		}else if($x == 1){
			return '<label class="label label-success">Setuju</label>';
		}else if($x == 2){
			return '<label class="label label-danger">Danger</label>';
		}
	}
}




