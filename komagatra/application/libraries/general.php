
<?php
Class General {

	function tgl_indo($tanggal){
		$bulan = array (
			1 =>   'Januari',
			2 =>  'Februari',
			3 =>  'Maret',
			4 =>  'April',
			5 =>  'Mei',
			6 =>  'Juni',
			7 =>  'Juli',
			8 =>  'Agustus',
			9 =>  'September',
			10 =>  'Oktober',
			11 =>  'November',
			12 =>  'Desember'
		);
		$tgl = explode(' ', $tanggal);
		$pecahkan = explode('-', $tgl[0]);

		if(count($tgl) > 1){
			return $pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
		}else {
			return $pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
		}
	 
		
	}
}