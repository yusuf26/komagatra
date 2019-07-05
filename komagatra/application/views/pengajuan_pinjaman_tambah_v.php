<div class="panel panel-primary">
  <!-- Default panel contents -->
  <div class="panel-heading">
  	Form Tambah Pengajuan Simpanan
  </div>
  <div class="panel-body">
  	<div class="alert alert-success" id="al-suc" style="display:none;">
  		Pengajuan Simpanan Berhasil, silahkan lihat <a href="<?php echo site_url('pengajuan');?>">disini</a>
  		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
		  <span aria-hidden="true">&times;</span>
		</button>
  	</div>
  	<div class="alert alert-danger" id="al-dang" style="display:none;">
  		Pengajuan Simpanan Gagal, Coba Lagi
  		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
		  <span aria-hidden="true">&times;</span>
		</button>
  	</div>

  	<form id="form-submit" action="<?php echo site_url('pengajuan/proses_tambah');?>" method="post"  class="form-horizontal">
        <ul class="list-group">
            <li class="list-group-item">
                <div class="row">
                    <div class="col-sm-2">
                        <label class=""> Anggota<span class="text-danger">*</span></label>
                    </div>
                    <div class="col-sm-6">
                        <select id="anggota" name="anggota" style="width:100%;" required="">
                            <option value="">-- Pilih Anggota --</option>
                            <?php
                            if(!empty($anggota)){
                                foreach ($anggota as $key => $row) {
                                    ?>
                                    <option value="<?php echo $row['id'];?>"><?php echo $row['anggota_id'].' - '.$row['nama'];?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                        
                    </div>
                </div>
            </li>

            
            <li class="list-group-item">
                <div class="row">
                    <div class="col-sm-2">
                        <label class=""> Jenis Pinjaman<span class="text-danger">*</span></label>
                    </div>
                    <div class="col-sm-6">
                    	<select class="form-control" id="jenis" name="jenis" required="">
                    		<option value="" data-min="0" data-max="0">-- Pilih Jenis Pinjaman --</option>
                            <?php
	                    	if(!empty($pinjaman)){
	                    		foreach ($pinjaman as $key => $row) {
	                    			?>
                                    <option value="<?php echo $row['jns_pinjaman'];?>" data-urutan="<?php echo $row['tampil_urutan']?>" data-anuitas="<?php echo $row['anuitas'];?>" data-min="<?php echo $row['min_pinjaman'];?>" data-max="<?php echo $row['max_pinjaman'];?>" ><?php echo $row['jns_pinjaman'];?></option>
	                    			<?php
	                    		}
	                    	}
	                    	?>
                        </select>
                    	
                    </div>
                </div>
            </li>

            <li class="list-group-item" id="box-urutan">
                <div class="row">
                    <div class="col-sm-2">
                        <label class=""> Urutan</label>
                    </div>
                    <div class="col-sm-6">
                         <input type="number" name="urutan" id="urutan" class="form-control" min="0" readonly="" />
                        
                    </div>
                </div>
            </li>
            
            <li class="list-group-item">
                <div class="row">
                    <div class="col-sm-2">
                        <label class=""> Nominal<span class="text-danger">*</span></label>
                    </div>
                    <div class="col-sm-6">
                    	<input type="text" id="nominal" name="nominal" class="form-control" required="" autocomplete="off">
                    	<input type="hidden" id="nominal_val" />
                    </div>
                </div>
            </li>

            <li class="list-group-item">
                <div class="row">
                    <div class="col-sm-2">
                        <label class=""> Lama Angsuran<span class="text-danger">*</span></label>
                    </div>
                    <div class="col-sm-2">
                       <input type="number" id="lama_ags" min="2" max="10" class="form-control text-center" name="lama_ags" placeholder="Bln" >
                    </div>
                </div>
            </li>

            <li class="list-group-item" id="bunga-tahun-box">
                <div class="row">
                    <div class="col-sm-2">
                        <label class=""> Bunga per Tahun<span class="text-danger">*</span></label>
                    </div>
                    <div class="col-sm-3">
                       <input type="text" id="bunga_tahun" readonly="" class="form-control text-center" value="10%" >
                    </div>
                </div>
            </li>

            <li class="list-group-item" id="potongan_box">
                <div class="row">
                    <div class="col-sm-2">
                        <label class=""> Potongan ke Simpanan Wajib<span class="text-danger">*</span></label>
                    </div>
                    <div class="col-sm-6">
                       <input type="text" id="potongan" class="form-control text-number" name="potongan"   readonly="" />
                    </div>
                </div>
            </li>

            <li class="list-group-item" id="uang_diterima_box">
                <div class="row">
                    <div class="col-sm-2">
                        <label class=""> Uang Diterima<span class="text-danger">*</span></label>
                    </div>
                    <div class="col-sm-6">
                       <input type="text" id="uang_diterima" class="form-control text-number" name="uang_diterima"  readonly="" />
                    </div>
                </div>
            </li>
            <li class="list-group-item">
                <div class="row">
                    <div class="col-sm-2">
                        <label class=""> Keterangan<span class="text-danger">*</span></label>
                    </div>
                    <div class="col-sm-6">
                        <textarea class="form-control" name="keterangan" id="keterangan" required=""></textarea>
                        
                    </div>
                </div>
            </li>

            <li class="list-group-item">
                <div class="row">
                    <div class="col-sm-12">
                       <div id="div_simulasi"></div>
                    </div>
                </div>
            </li>
                    

            
            <li class="list-group-item">
                <div class="row">
                    <div class="col-sm-12">
                        <button type="submit" name="submit" class="btn btn-primary" id="btn-submit"><i class="fa fa-save"></i> Simpan</button> 
                    </div>
                </div>
            </li>

        </ul>
    </form>
  </div>
</div>



<script type="text/javascript">

	$('#anggota').select2();

	

    $(function() {
        $('#bunga-tahun-box').hide();
        $('#potongan_box').hide();
        $('#uang_diterima_box').hide();
        $('#box-urutan').hide();
        $('#nominal').on('change keyup paste', function() {
            var n = parseInt($(this).val().replace(/\D/g, ''), 10);
            $('#nominal_val').val(n);
            $(this).val(number_format(n, 0, '', '.'));
        });

        $('.text-number').on('change keyup paste', function() {
            var n = parseInt($(this).val().replace(/\D/g, ''), 10);
            // $('#nominal_val').val(n);
            $(this).val(number_format(n, 0, '', '.'));
        });

        $('#jenis').on('change', function() {
            oc_lama_ags();

        });

        $('#jenis, #nominal, #lama_ags').on('change', function() {
            simulasikan();
        });


    });

    function simulasikan() {
        var jenis = $('#jenis').val();
        var var_nominal = $('#nominal').val();
        var var_lama_ags = $('#lama_ags').val();
        var min_ags = $('#lama_ags').attr('min');
        var max_ags = $('#lama_ags').attr('max');

        var anuitas = $('option:selected', '#jenis').attr('data-anuitas');

        $('#potongan').val('');
        $('#uang_diterima').val('');
        $('#div_simulasi').html('');
        if(var_nominal != 0 && var_nominal != '' && var_lama_ags != '' && var_lama_ags != 0){
            console.log(var_lama_ags+'>='+max_ags+'dan'+var_lama_ags+'<='+min_ags);
            if( var_lama_ags >= min_ags ||  var_lama_ags <= max_ags ){
                if(anuitas == 0){
                    var potongan = ($('#nominal_val').val() * 1) / 100;
                    $('#potongan').val(number_format(potongan, 0, '', '.'));
                    var uang_diterima = $('#nominal_val').val() - potongan;
                    $('#uang_diterima').val(number_format(uang_diterima, 0, '', '.'));
                }

                $('#submit').button('loading');
                $.ajax({
                    url: '<?php echo site_url('member/simulasi')?>',
                    type: 'POST',
                    dataType: 'html',
                    data: {'nominal': var_nominal, 'lama_ags': var_lama_ags, 'jenis': jenis}
                })
                .done(function(result) {
                    $('#div_simulasi').html(result);
                    console.log("success");
                    $('#submit').button('reset');
                })
                .fail(function() {
                    console.log("error");
                })
                .always(function() {
                    console.log("complete");
                });
            }else{
                alert('Mohon maaf, lama angsuran tidak sesuai !!!');
                
            }
        }
    }

    function oc_lama_ags() {
        var jenis = $('#jenis').val();
        var min = $('option:selected', '#jenis').attr('data-min');
        var max = $('option:selected', '#jenis').attr('data-max');
        var anuitas = $('option:selected', '#jenis').attr('data-anuitas');
        var urutan = $('option:selected', '#jenis').attr('data-urutan');

        $('#lama_ags').attr('placeholder',''+min+' - '+max+' Bln');
        $('#lama_ags').attr('min',min);
        $('#lama_ags').attr('max',max);

        $('#potongan_box').hide();
        $('#uang_diterima_box').hide();
        
        $('#nominal').val('');
        $('#nominal_val').val('');
        $('#lama_ags').val('');

        
        if(anuitas == 0){
            $('#potongan_box').show();
            $('#uang_diterima_box').show();
        }else {
            $('#bunga-tahun-box').show();
        }

        if(urutan == 1){
            $('#box-urutan').show();
            check_urutan(jenis);
        }else {
            $('#box-urutan').hide();
            $('#urutan').val(0);
        }
    }

    function check_urutan(jenis)
    {

        $.ajax({
            url: '<?php echo site_url('member/check_urutan')?>',
            type: 'POST',
            dataType: 'json',
            data: {jenis:1}
        })
        .done(function(result) {
            $('#urutan').val(result.output);
        });
    }

    $("#form-submit").submit(function(){
        $.ajax({
          url:$(this).attr("action"),
          data:$(this).serialize(),
          type:$(this).attr("method"),
          dataType: 'json',
          beforeSend: function() {
            $('#btn-submit').button('loading');
          },
          complete:function() {
            $('#btn-submit').button('reset');       
            $('#nominal').val('');
            $('#jenis').val('');
            $("#anggota").select2("val", "");
            $('#keterangan').val('');        
            oc_lama_ags();
            $('#div_simulasi').html('');
            $('#bunga-tahun-box').hide();  

          },
          success:function(hasil) {
            if(hasil.output){
                $('#al-suc').show();
            }else {
                $('#al-dang').show();
            }
          }
        })
        return false;
    });


</script>


