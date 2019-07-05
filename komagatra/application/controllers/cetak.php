<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cetak extends AdminController {

    public function __construct() {
        parent::__construct();  
        $this->load->helper('fungsi');
        $this->load->model('simpanan_m');
        $this->load->model('general_m');
    }   

function cetak($id) {
        $this->load->library('Pdf');

        $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->set_nsi_header(TRUE);
        $pdf->AddPage('P');
        $html = '
            <style>
                .h_tengah {text-align: center;}
                .h_kiri {text-align: left;}
                .h_kanan {text-align: right;}
                .txt_judul {font-size: 15pt; font-weight: bold; padding-bottom: 15px;}
                .header_kolom {background-color: #cccccc; text-align: center; font-weight: bold;}


            </style>
            '.$pdf->nsi_box($text = '<span class="txt_judul">Laporan PDF</span>', $width = '100%', $spacing = '0', $padding = '1', $border = '0', $align = 'center').'
            <table width="100%" cellspacing="0" cellpadding="1" border="1">
                <tr>
                    <td width="5%"     class="header_kolom">No</td>
                    <td width="70%"     class="header_kolom">Keterangan</td>
                    <td width="25%"     class="header_kolom">Jumlah</td>
                </tr>
                <tr>
                    <td class="h_kanan">123</td>
                    <td class="h_kiri">Percobaan</td>
                    <td class="h_kanan">5</td>
                </tr>
            </table>
        ';
        $pdf->nsi_html($html);
        $pdf->Output(date('Ymd_His') . '.pdf', 'I');

    } 

    function cetak_laporan($type,$tbl=''){

        $kode_transaksi = isset($_REQUEST['kode_transaksi']) ? $_REQUEST['kode_transaksi'] : '';
        $tgl_dari = isset($_REQUEST['tgl_dari']) ? $_REQUEST['tgl_dari'] : '';
        $tgl_sampai = isset($_REQUEST['tgl_sampai']) ? $_REQUEST['tgl_sampai'] : '';

        $data_print = $this->general_m->lap_data_trans($type,$tbl,$kode_transaksi,$tgl_dari,$tgl_sampai);

        if($data_print == FALSE) {
            redirect('pengeluaran_kas');
            exit();
        }

        $tgl_dari = $_REQUEST['tgl_dari']; 
        $tgl_sampai = $_REQUEST['tgl_sampai']; 

        $tgl_cetak = 'Periode '.jin_date_ina($tgl_dari).' - '.jin_date_ina($tgl_sampai);

        if($tgl_dari == '' && $tgl_sampai == ''){
            $tgl_cetak = 'Tanggal '.jin_date_ina(date('Y-m-d'));
        }

        if($type == 0){
            $title = 'Laporan Data Pengeluaran ';
        }else {
            $title = 'Laporan Data Pemasukan ';
        }
        

        $this->load->library('Pdf');
        $pdf = new Pdf('L', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->set_nsi_header(TRUE);
        $pdf->AddPage('L');
        $pdf->SetAuthor('Komagtra');
        $pdf->SetTitle('Cetak '.date('Ymd_His'));
        $html = '';
        $html .= '
        <style>
            .h_tengah {text-align: center;}
            .h_kiri {text-align: left;}
            .h_kanan {text-align: right;}
            .txt_judul {font-size: 12pt; font-weight: bold; padding-bottom: 12px;}
            .header_kolom {background-color: #cccccc; text-align: center; font-weight: bold;}
            .txt_content {font-size: 10pt; font-style: arial;}
            .paraf-bottom {padding-left:20px;}
        </style>
        '.$pdf->nsi_box($text = '<span class="txt_judul">'.$title.'<br></span>
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
            foreach ($data_print as $row) {
                if($type == 0){
                    $jumlah = $row->kredit;  
                }else {
                    $jumlah = $row->debet;    
                }
                 
                $tgl_bayar = explode(' ', $row->tgl);
                $txt_tanggal = jin_date_ina($tgl_bayar[0],'p');

                $jml_tot += $jumlah;
                $html .= '
                <tr>
                    <td class="h_tengah" >'.$no++.'</td>
                    <td class="h_tengah"> '.$row->kd_transaksi.'</td>
                    <td class="h_tengah"> '.$txt_tanggal.'</td>
                    <td class="h_kiri"> '.$row->ket.'</td>
                    <td class="h_kanan"> '.number_format($jumlah,0,'','.').'</td>
                </tr>';
            }
            $html .= '
            <tr>
                <td colspan="4" class="h_tengah"><strong> Jumlah Total </strong></td>
                <td class="h_kanan"> <strong>'.number_format($jml_tot,0,'','.').'</strong></td>
            </tr>
        </table>';

        if($_REQUEST['kode_transaksi']){
            $html .= '<br /><br /><br /><br /><br />';
            $html .= '<table width="100%" border="0">';
            // if($type == 0){
            //     $html .= '<tr><td align="center">Dibuat Oleh</td><td align="center">Dibayar Oleh</td><td align="center">Yang Menerima</td></tr>';
            // }else {
                
            // }
            $html .= '<tr><td align="center">Dibuat Oleh</td><td align="center">Diperiksa Oleh</td><td align="center">Disetujui Oleh</td></tr>';
            $html .= '</table>';
            $html .= '<br /><br /><br /><br /><br />';
            $html .= '<table width="100%" border="0">';
            $html .= '<tr><td align="center">'.$row->nama_member.'</td><td align="center">'.$row->nama_operator.'</td><td align="center">'.$row->nama_admin.'</td></tr>';
            $html .= '</table>';
        }
        $pdf->nsi_html($html);
        $pdf->Output('trans_k'.date('Ymd_His') . '.pdf', 'I');
    } 

    public function update_cetak()
    {
        
        $query = $this->general_m->update_cetak();
        if($query){
            echo json_encode(array('ok'=>true,'id_txt'=>$query));
        }else {
            echo json_encode(array('ok'=>false,'msg'=>'Proses Error Silahkan menghubungi Administrator'));
        }

    }

}