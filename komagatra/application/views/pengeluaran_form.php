<style type="text/css">
    table.dataTable tbody tr.selected{
        color: #fff !important;
        background-color: #4a4d50 !important;
    }
     table.dataTable > tbody > tr.selected > td{
        color: #fff !important;
        background-color: #4a4d50 !important;
    }
    .modal-lg {
        width: 900px;
    }
</style>

<div class="panel panel-primary">
  <!-- Default panel contents -->
  <div class="panel-heading">
  	
  	<div class="row">
  		<div class="col-sm-2">
  			<select class="form-control" id="pilih_kas">
		  		<option value="">Pilih Kas / Bank</option>
		  		<?php
		  		if(is_array($kas_id)){
		  			foreach ($kas_id as $key => $val) {
		  				?>
						<option value="<?php echo $val->id;?>" data-name="<?php echo $val->nama;?>"><?php echo $val->nama;?></option>
		  				<?php
		  			}
		  		}
		  		?>
		  	</select>		
  		</div>
        <div class="col-sm-2">
            <button class="btn btn-default" id="daterange-btn" style="line-height:16px;border:1px solid #ccc">
                <i class="fa fa-calendar"></i> <span id="reportrange"><span>Pilih Tanggal</span></span>
                <i class="fa fa-caret-down"></i>
            </button>
        </div>
        <div class="col-sm-2">
            <select id="pilih-status" class="form-control">
                <option value="">Pilih Status</option>
                <option value="1">Setuju</option>
                <option value="3">Menunggu</option>
                <option value="2">Tolak</option>
            </select>
        </div>
        <div class="col-sm-4 text-right">
            <a href="javascript:void(0);" onclick="setujuTrans()" class="btn btn-success btn-sm"><i class="fa fa-check"></i> Setuju</a>
            <a href="javascript:void(0);" onclick="tolakTrans()" class="btn btn-danger btn-sm"><i class="fa fa-times"></i> Tolak</a>
            <a href="javascript:void(0);" onclick="cetakTable()" class="btn btn-warning btn-sm"><i class="fa fa-print"></i> Cetak</a>
            <input type="hidden" id="start_date" value="<?php echo date('Y-m-01');?>">
            <input type="hidden" id="end_date" value="<?php echo date('Y-m-t');?>">
             <input type="hidden" id="kode_table">
             <input type="hidden" id="kode_id">
        </div>
  	</div>
  	
  </div>
  <div class="panel-body">
    <?php
    if($this->session->flashdata('error_msg')){
        ?>
        <div class="alert alert-danger" role="alert">
        <?php echo $this->session->flashdata('error_msg');?>
        </div>
        <?php
    }
    ?>
    <div class="table-responsive" >
        <table class="table table-striped table-bordered" id="table-pemasukan" style="cursor:pointer">
            <thead>
                <tr>
                    <th>TBL</th>
                    <th>ID</th>
                    <th>Tgl</th>
                    <th>Kode</th>
                    <th>Kas/Bank</th>
                    <th>Jenis Transaksi</th>
                    <th>Penyetor</th>
                    <th>Jumlah</th>
                    <th>Ket</th>
                    <th>Status</th>
                    <th>Status ID</th>
                </tr>
                
            </thead>
            <tbody>
               
            </tbody>
        </table>
    </div>
        
    <br />
  <!-- List group -->
    <form id="form-submit" action="<?php echo site_url('pengeluaran');?>" method="post"  class="form-horizontal">
        <input type="hidden" name="kas_id" id="kas_id">
        <ul class="list-group">
            <li class="list-group-item">
                <div class="row">
                    <div class="col-sm-6">
                        <h4 id="label-kas">Pilih Kas / Bank</h4>
                    </div>
                </div>
            </li>
            <li class="list-group-item">
                <div class="row">
                    <div class="col-sm-6">
                        <label class="">Jenis<span class="text-danger">*</span></label>
                        <select class="form-control" id="jenis" name="jenis">
                            <option value="">-- Pilih Jenis --</option>
                            <option value="1">Pinjaman</option>
                            <option value="2">Penarikan Simpanan</option>
                            <option value="4">Lain-lain</option>
                        </select>
                    </div>
                    <div class="col-sm-5">
                        <label class="">Tanggal <span class="text-danger">*</span></label>
                        <input type="text" class="form-control tanggal" name="tgl"  autocomplete="off" value="<?php echo date('d F Y');?>" />
                        <!-- <input type="hidden" class="form-control" name="tgl" id="tgl" > -->
                    </div>
                </div>
            </li>
            <li class="list-group-item">
                <div class="row">
                    <div class="col-sm-6" id="box-nyetor">
                        <label class="">Nama Penyetor<span class="text-danger">*</span></label>
                        <select class="form-control" id="type_penyetor" name="type_penyetor" >
                            <option value="2">Bukan Anggota</option>
                            <option value="1">Anggota</option>
                        </select>
                        <input type="text" name="penyetor_lain" class="form-control" id="penyetor_lain" placeholder="Nama Penyetor">
                        <div id="penyetor_anggota" >
                            <select class="form-control select2" name="penyetor_anggota" >
                                <?php
                                foreach ($anggota_list as $key => $ag) {
                                    ?>
                                    <option value="<?php echo $ag['id'];?>"><?php echo $ag['anggota_id'].' - '.$ag['nama'];?></option>
                                    <?php
                                }
                                ?>
                            </select>       
                        </div>
                    </div>
                    <div class="col-sm-6" id="box-ajuan" style="display: none;">
                        <label class="">Pengajuan<span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Nomor Pengajuan" name="ajuan_id" readonly="" id="isi-ajuan">
                            <span class="input-group-btn"> 
                                <button class="btn btn-primary" type="button" id="btn-cari-ajuan">Cari</button>
                            </span>
                        </div>
                    </div>
                    
                    <div class="col-sm-5">
                        <label class="">Keterangan <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="keterangan" rows="3" id="keterangan"></textarea>
                    </div>
                </div>
            </li>
            <li class="list-group-item">
                <a href="javascript:void(0);" class="btn btn-primary" id="tmb-akun">Tambah Akun</a>
                <input type="hidden" name="total_akun" value="1" id="total_akun">
                <br /><br />
                <table class="table table-bordered tabled-striped">
                    <thead>
                        <tr>
                            <th>Akun</th>
                            <th width="10%">D/K</th>
                            <th width="30%">Jumlah</th>
                            <th width="5%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="content-akun">
                        
                    </tbody>
                </table>
            </li>
            <li class="list-group-item">
                <div class="row">
                    <div class="col-sm-12">
                        <button name="submit" class="btn btn-primary" id="btn-submit"><i class="fa fa-save"></i> Simpan</button> 
                        <a href="<?php echo site_url('pemasukan_bank');?>" class="btn btn-default"><i class="fa fa-sign-out"></i>  Kembali</a>
                    </div>
                </div>
            </li>
        </ul>
    </form>  
  </div>
    
</div>


<div class="modal fade" id="modalPrint" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">View Transaksi</h4>
      </div>
      <div class="modal-body">
        <label>Tanggal : <span id="tgl-trans"></span></label>
        <div class="pull-right">
            <a href="javascript:void(0);" onclick="printView()" class="btn btn-info"><i class="fa fa-print"></i> Cetak</a>
            <input type="hidden" id="status-trans">
        </div>
        <br />
        <table id="table-view" class="table table-bordered table-condensed">
            <thead>
                <tr>
                    <th>Kode Transaksi</th>
                    <th>Akun</th>
                    <th>Debet</th>
                    <th>Kredit</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
        <div id="print-trans" style="display:none;">
            <div class="row">
                <div class="col-sm-3">
                    <b>Dibuat Oleh :</b>
                </div>
                <div class="col-sm-6">
                <select id="nama_member_print" class="form-control select2" name="nama_member_print"  >
                    <?php
                    if(!empty($anggota_bendahara)){
                        foreach ($anggota_bendahara as $key => $ag) {
                            ?>
                            <option value="<?php echo $ag['nama'];?>"><?php echo $ag['nama'];?></option>
                            <?php
                        }
                    }
                    
                    ?>
                </select>  
            </div>
            <div class="col-sm-3">
                <b>Diperiksa Oleh :</b>
            </div>
            <div class="col-sm-6">
                <select id="nama_operator" class="form-control select2" name="nama_operator" >
                    <?php
                    if(!empty($anggota_bendahara)){
                        foreach ($anggota_bendahara as $key => $ag) {
                            ?>
                            <option value="<?php echo $ag['nama'];?>"><?php echo $ag['nama'];?></option>
                            <?php
                        }
                    }
                    
                    ?>
                </select>  
            </div>
            <div class="col-sm-3">
                <b>Disetujui Oleh :</b>
            </div>
            <div class="col-sm-6">
                <select id="nama_admin" class="form-control select2" name="nama_admin" >
                    <?php
                    if(!empty($anggota_ketua)){
                        foreach ($anggota_ketua as $key => $ag) {
                            ?>
                            <option value="<?php echo $ag['nama'];?>"><?php echo $ag['nama'];?></option>
                            <?php
                        }
                    }
                    
                    ?>
                </select>  
            </div>
                <div class="col-sm-4">
                    <a href="javascript:void(0);" class="btn btn-danger" onclick="save_print()"><i class="fa fa-print"></i> Cetak Sekarang</a>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="tablePrint" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Print Transaksi</h4>
      </div>
      <div class="modal-body">
        <div class="row">
            <div class="col-sm-3">
                <b>Dibuat Oleh :</b>
            </div>
            <div class="col-sm-6">
                <select id="nama_member_print" class="form-control select2" name="nama_member_print"  >
                    <?php
                    if(!empty($anggota_bendahara)){
                        foreach ($anggota_bendahara as $key => $ag) {
                            ?>
                            <option value="<?php echo $ag['nama'];?>"><?php echo $ag['nama'];?></option>
                            <?php
                        }
                    }
                    
                    ?>
                </select>  
            </div>
            <div class="col-sm-3">
                <b>Diperiksa Oleh :</b>
            </div>
            <div class="col-sm-6">
                <select id="nama_operator" class="form-control select2" name="nama_operator" >
                    <?php
                    if(!empty($anggota_bendahara)){
                        foreach ($anggota_bendahara as $key => $ag) {
                            ?>
                            <option value="<?php echo $ag['nama'];?>"><?php echo $ag['nama'];?></option>
                            <?php
                        }
                    }
                    
                    ?>
                </select>  
            </div>
            <div class="col-sm-3">
                <b>Disetujui Oleh :</b>
            </div>
            <div class="col-sm-6">
                <select id="nama_admin" class="form-control select2" name="nama_admin" >
                    <?php
                    if(!empty($anggota_ketua)){
                        foreach ($anggota_ketua as $key => $ag) {
                            ?>
                            <option value="<?php echo $ag['nama'];?>"><?php echo $ag['nama'];?></option>
                            <?php
                        }
                    }
                    
                    ?>
                </select>  
            </div>
            <input type="hidden" id="kode_transaksi">

            <div class="col-sm-4">
                <a href="javascript:void(0);" class="btn btn-danger" onclick="save_print()"><i class="fa fa-print"></i> Cetak Sekarang</a>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="tablePengajuan" tabindex="-1" role="dialog" åå>
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Tabel Pengajuan</h4>
      </div>
      <div class="modal-body">
        <table class="table table-striped table-bordered" id="ajax-table-pinjaman" style="width:100%;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tanggal</th>
                    <th>Anggota</th>
                    <th>Jenis Pinjaman</th>
                    <th>Jumlah</th>
                    <th id="tr-angsuran">Angsuran</th>
                    <th>Keterangan</th>
                    <th>Jenis Trans</th>
                    <th>Aksi</th>

                </tr>
            </thead>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
      </div>
    </div>
  </div>
</div>




<script type="text/javascript">
	

   var table = $('#table-pemasukan').DataTable({ 
 
            "processing": true, 
            "serverSide": true, 
            "order": [], 
            "ajax": {
                "url": "<?php echo site_url('pengeluaran/ajax_list')?>",
                "type": "POST",
                "data": function ( d ) {
                    d.kas_id = $('#pilih_kas').val();
                    d.tgl_dari =  $('input[name=daterangepicker_start]').val();
                    d.tgl_sampai = $('input[name=daterangepicker_end]').val();
                    d.status = $('#pilih-status').val();
                }
            },
            "select" : true,
            "columnDefs": [
                {
                    "targets": [0],
                    "visible": false
                },
                {
                    "targets": [1],
                    "visible": false
                },
                {
                    "targets": [6],
                    "orderable": false
                },
                {
                    "targets": [7],
                    "orderable": false
                },
                {
                    "targets": [8],
                    "orderable": false
                },
                {
                    "targets": [9],
                    "orderable": false
                },
                {
                    "targets": -1,
                    "visible": false
                },

            ]
 
        });

        var table_pinjaman = $('#ajax-table-pinjaman').DataTable({ 
                "ajax": {
                    "url": "<?php echo site_url('pengeluaran/table_pinjaman')?>",
                    "type": "POST",
                    "data": function ( d ) {
                        d.jenis = $('#jenis').val();
                    }
                },
                "columnDefs": [ {
                    "targets": -1,
                    "data": null,
                    "defaultContent": "<button class='btn btn-primary btn-sm'>Pilih</button>"
                },
                {
                    "targets": -2,
                    "visible" : false
                } ]
            });

        function load_table_pinjaman()
        {
            
            table_pinjaman.ajax.reload();
            $('#ajax-table-pinjaman tbody').on( 'click', 'button', function () {
                var table_data_pinjaman = table_pinjaman.row( $(this).parents('tr') ).data();
                // console.log(table_data_pinjaman[0]);
                $('#isi-ajuan').val(table_data_pinjaman[0]);
                $('#content-akun').html('');
                addAkun(table_data_pinjaman[7],'K',table_data_pinjaman[4]);
                $('#tablePengajuan').modal('hide');
            } );
        }



   $('#pilih-status').change(function(){
    table.ajax.reload();
   });


        function cetakTable()
        {
            var table_data = table.rows( { selected: true }).data();
            
            if(table_data.length > 0){
                if(table_data.length > 1){
                    bootbox.alert("Maaf, Cetak Transaksi pilihan, tidak bisa lebih dari satu");
                }else {
                    $('#tablePrint').modal('show');
                    $('#kode_table').val(table_data[0][0]);
                    $('#kode_id').val(table_data[0][1]);
                    $('#kode_transaksi').val(table_data[0][3]);    
                }
                
            }else {
                var start_date = $('#start_date').val();
                var end_date = $('#end_date').val();

                var win = window.open('<?php echo site_url('cetak/cetak_laporan/1');?>?kode_transaksi=&tgl_dari='+start_date+'&tgl_sampai='+end_date+'','_blank');
                if (win) {
                    win.focus();
                } else {
                    alert('Popup jangan di block');

                }
            }
        
        
        }

       function setujuTrans()
       {
        var table_data = table.rows( { selected: true }).data();
        
        if(table_data.length > 0){
            bootbox.confirm("Apakah anda yakin melakukan aksi ini?", function(result){ 
                // console.log('This was logged in the callback: ' + result); 
                if(result){
                    ajaxAksi(1,table_data.toArray());
                }
                
            });
            
            
        }else {
            bootbox.alert("Maaf, Pilih salah satu transaksi terlebih dahulu");
        }
        
        
       }

        function tolakTrans()
        {
            var table_data = table.rows( { selected: true }).data();
            
            if(table_data.length > 0){

                // console.log(table_data);
                bootbox.confirm("Apakah anda yakin melakukan aksi ini?", function(result){ 
                        // console.log('This was logged in the callback: ' + result); 
                        if(result){
                            ajaxAksi(2,table_data.toArray());
                        }
                        
                    });
               
                
                
            }else {
                bootbox.alert("Maaf, Pilih salah satu transaksi terlebih dahulu");
            }
        
        
        }

        function ajaxAksi(val,data)
        {
            // console.log(data);
            $.ajax({
                type    : "POST",
                url     : '<?php echo site_url('pengeluaran/aksi');?>',
                dataType : 'json',
                data : {val:val,data:data},
                success : function(result){
                    if(result.output == true){
                        bootbox.alert("Transaksi Berhasil DiProses");
                        table.ajax.reload();
                    }else if(result.output == 'gagal'){
                        bootbox.alert("Maaf Transaksi sudah di proses");
                    }else {
                        bootbox.alert("Error Transaksi sudah di proses");
                    }
                    

                }
            });
        }


    function save_print()
    {
        var kode_transaksi  = $('#kode_transaksi').val();   
        var kode_table  = $('#kode_table').val();
        var nama_operator   = $('#nama_operator').val();    
        var nama_admin  = $('#nama_admin').val();   
        var nama_member_print   = $('#nama_member_print').val();    
        $.ajax({
            type    : "POST",
            url     : '<?php echo site_url('cetak/update_cetak');?>',
            dataType : 'json',
            data : {tbl:kode_table, id_txt:kode_transaksi, nama_operator:nama_operator, nama_admin:nama_admin , nama_member:nama_member_print},
            success : function(result){
                if(result.ok) {
                    location.reload();
                    kode_transaksi = result.id_txt;
                    var win = window.open('<?php echo site_url('cetak/cetak_laporan/1');?>/'+kode_table+'?kode_transaksi='+kode_transaksi+'&tgl_dari=&tgl_sampai=','_blank');
                    if (win) {
                        win.focus();
                    } else {
                        alert('Popup jangan di block');

                    }
                }
            },
            error : function (){
                bootbox.alert("Maaf, Terjadi kesalahan koneksi, silahkan muat ulang");
            }
        });
    }


   function printView()
   {
    if($('#status-trans').val() == 0){
        bootbox.alert("Maaf, Transaksi ini belum di setujui oleh ketua.Silahkan menghubungi ketua !!");
    }else {
        $('#print-trans').toggle();    
    }
    
   }


	$('#type_penyetor').hide();
	$('#penyetor_anggota').hide();

	$('#pilih_kas').change(function(){
		$('#kas_id').val($(this).val());

        $('#label-kas').text($('option:selected', this).attr('data-name'));
        table.ajax.reload();
	});

	$('#jenis').change(function(){
		var val = $(this).val();
        
		if(val == 4){
			$('#type_penyetor').show();
			// $('#penyetor_anggota').show();
            $('#box-ajuan').hide();
            $('#box-nyetor').show();
            $('#tmb-akun').show();
		}else {
			$('#type_penyetor').hide();
            $('#box-ajuan').show();
            $('#box-nyetor').hide();
            $('#tmb-akun').hide();
            $('#tr-angsuran').show();
		}
	});

	$('#type_penyetor').change(function(){
		if($(this).val() == 1){
			$('#penyetor_anggota').show();
			$('#penyetor_lain').hide();
		}else {
			$('#penyetor_anggota').hide();
			$('#penyetor_lain').show();
		}
	});

    $('.select2').select2();

    $('.numberformat').keyup(function(){
        var n = parseInt($(this).val().replace(/\D/g, ''), 10);
        $(this).val(number_format(n, 0, '', '.'));
    });

    function formatNumber(id){
    	var n = parseInt($('#jumlah_'+id).val().replace(/\D/g, ''), 10);
        $('#jumlah_'+id).val(number_format(n, 0, '', '.'));
    }

     $(".tanggal").datetimepicker({
    
        weekStart: 1,
        autoclose: true,
        todayBtn: true,
        todayHighlight: true,
        pickerPosition: 'bottom-right',
        format: 'dd MM yyyy',
        linkField: "tgl",
        linkFormat: "yyyy-mm-dd",
        minView : 2,
    }); 

    $('#tmb-akun').click(function(){
    	addAkun();
    });

    function addAkun(akun=false,dk=false,jumlah=false)
    {
        var data_akun = [];
        
        var total_akun = $('#total_akun').val();
        $('#content-akun').append('<tr id="box-akun-'+total_akun+'">'+
        '<td>'+
            '<select  name="akun_'+total_akun+'" id="akun_'+total_akun+'" style="width:100%;">'+
               
            '</select>'+
        '</td>'+
        '<td>'+
            '<select name="dk_'+total_akun+'" id="dk_'+total_akun+'" class="form-control">'+
                '<option value="D">D</option>'+
                '<option value="K" selected>K</option>'+
            '</select>'+
        '</td>'+
        '<td><input type="text" id="jumlah_'+total_akun+'" name="jumlah_'+total_akun+'" class="form-control" onkeyup="formatNumber('+total_akun+')" autocomplete="off" /></td>'+
        '<td><a href="javascript:void(0);" onclick="HapusAkun('+total_akun+')" class="btn btn-danger">Hapus</a></td>'+
        '</tr>');

        if(akun){
           get_akun(total_akun,akun);
        }else {
            get_akun(total_akun);
            $('#akun_'+total_akun).select2({
                placeholder: "Select a state"
            });
        }
        
        if(dk){
            $('#dk_'+total_akun).val(dk);
        }
        if(jumlah){
            $('#jumlah_'+total_akun).val(jumlah);
        }
        $('#total_akun').val(parseInt(total_akun) + 1);
    }

    function HapusAkun(id)
    {
    	$('#box-akun-'+id).remove();
    }	


    function get_akun(id,akun=false)
    {
    	$.ajax({
			type	: "GET",
			url		: "<?php echo site_url('pemasukan_bank/get_data_akun'); ?>",
            data    : {akun:akun},
			dataType : 'json',
			success	: function(result){
				$.each( result, function( key, val ) {
				 	$('#akun_'+id).append('<option value="'+val.id+'">'+val.jns_trans+'</option>');
				});
				
			}
		});
    }
    fm_filter_tgl();
    function fm_filter_tgl() {
        $('#daterange-btn').daterangepicker({
            ranges: {
                'Hari ini': [moment(), moment()],
                'Kemarin': [moment().subtract('days', 1), moment().subtract('days', 1)],
                '7 Hari yang lalu': [moment().subtract('days', 6), moment()],
                '30 Hari yang lalu': [moment().subtract('days', 29), moment()],
                'Bulan ini': [moment().startOf('month'), moment().endOf('month')],
                'Bulan kemarin': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')],
                'Tahun ini': [moment().startOf('year').startOf('month'), moment().endOf('year').endOf('month')],
                'Tahun kemarin': [moment().subtract('year', 1).startOf('year').startOf('month'), moment().subtract('year', 1).endOf('year').endOf('month')]
            },
            showDropdowns: true,
            format: 'YYYY-MM-DD',
            startDate: moment().startOf('year').startOf('month'),
            endDate: moment().endOf('year').endOf('month')
        },

        function(start, end) {
            $('#reportrange span').html(start.format('D MMM YYYY') + ' - ' + end.format('D MMM YYYY'));
            table.ajax.reload();
        });
    }

    $('#modalPrint').on('hidden.bs.modal', function () {
        $('#jenis').val('');
        $('#content-akun').html('');
        $('#box-nyetor').show();
        $('#box-ajuan').hide();
        $('#pinjaman_id').val('');
        $('#penyetor_lain').val('');
        $('#keterangan').val('');
        $('#isi-ajuan').val('');    
        $('#type_penyetor').val(2);
        $('#penyetor_lain').show();
        $('#penyetor_lain').val('');
        $('#penyetor_anggota').hide(); 
    });

    $('#btn-cari-ajuan').click(function(){
        $('#tablePengajuan').modal('show');
        var jenis = $('#jenis').val();
        if(jenis == 2){
            table_pinjaman.column( 5 ).visible( false );
        }else {
            table_pinjaman.column( 5 ).visible();
        }
        
        load_table_pinjaman();
    });



    $('#form-submit').submit(function(event){
        $('#btn-submit').button('loading');
    	var kas_id = $('#kas_id').val();
    	if(kas_id != ''){
    		$.ajax({
				type	: "POST",
				url		: "<?php echo site_url('pengeluaran/submit_form'); ?>",
				dataType : 'json',
				data: $(this).serialize(),
				success	: function(result){
                    $('#btn-submit').button('reset');
					if(result.output){
                        table.ajax.reload();
						$('#modalPrint').modal('show');

                        $('#kode_transaksi').val(result.kd_transaksi);   
                        $('#kode_table').val(result.tbl);

                        $('#tgl-trans').text(result.tgl);
                        $('#status-trans').val(result.data[0].status);
                        $.each( result.data, function( key, val ) {
                            
                            $('#table-view tbody').append('<tr><td>'+val.kd_transaksi+'</td><td>'+val.transaksi+'</td><td>'+number_format(val.debet, 0, '', '.')+'</td><td>'+number_format(val.kredit, 0, '', '.')+'</td></tr>');
                        });
					}
				}
			});
    	}else {
    		bootbox.alert("Tolong Pilih Bank atau Kas Terlebih Dahulu");
    	}
    	

    	event.preventDefault();
    });
</script>