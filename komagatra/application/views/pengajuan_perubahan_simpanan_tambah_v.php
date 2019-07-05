<div class="panel panel-primary">
  <!-- Default panel contents -->
  <div class="panel-heading">
  	Form Tambah Pengajuan Perubahan Simpanan
  </div>
  <div class="panel-body">
  	<div class="alert alert-success" id="al-suc" style="display:none;">
  		Pengajuan Simpanan Berhasil, silahkan lihat <a href="<?php echo site_url('pengajuan_perubahan_simpanan');?>">disini</a>
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

    <?php
    ?>  

  	<form id="form-submit" action="<?php echo site_url('pengajuan_perubahan_simpanan/proses_tambah');?>" method="post"  class="form-horizontal">
        <ul class="list-group">
            <li class="list-group-item">
                <div class="row">
                    <div class="col-sm-2">
                        <label class=""> Jenis Simpanan<span class="text-danger">*</span></label>
                    </div>
                    <div class="col-sm-6">
                    	<select class="form-control" id="jenis" name="jenis_simpanan" required="">
                    		<option value="">-- Pilih Jenis Simpanan --</option>
                            <?php
	                    	if(!empty($simpanan)){
	                    		foreach ($simpanan as $key => $row) {
	                    			?>
	                    			<option value="<?php echo $row['id'];?>"><?php echo $row['jns_simpan'];?></option>
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
                        <label class=""> Jumlah Simpanan<span class="text-danger">*</span></label>
                    </div>
                    <div class="col-sm-6">
                        <input type="text" id="jumlah" name="jumlah" class="form-control" required="" autocomplete="off" readonly="">
                        
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
                        <button type="submit" name="submit" class="btn btn-primary" id="btn-submit"><i class="fa fa-save"></i> Simpan</button> 
                    </div>
                </div>
            </li>

        </ul>
    </form>
  </div>
</div>



<script type="text/javascript">
	$('#nominal').on('change keyup paste', function() {
			var n = parseInt($(this).val().replace(/\D/g, ''), 10);
			$(this).val(number_format(n, 0, '', '.'));
		});
	$('#anggota').select2();

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
            $('#jumlah').val(0);
	        $('#jenis').val('');
	        // $('#anggota option').prop('selected', '');
	        $("#anggota").select2("val", "");
	        $('#keterangan').val('');				
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

    $('#anggota').on('change',function(){
        $('#jumlah').val(0);
        var jenis = $('#jenis').val();
        if(jenis == ''){
            alert('Pilih Jenis Simpanan Terlebih Dahulu !!!');
        }else {
            get_simpanan(jenis,$(this).val());
        }
    });

    $('#jenis').on('change',function(){
        $('#jumlah').val(0);
        var anggota = $('#anggota').val();
        get_simpanan($(this).val(),anggota);
    });

    function get_simpanan(jenis,anggota_id) {
        $.ajax({
            url: '<?php echo site_url('pengajuan_perubahan_simpanan/get_simpanan_bulan_by_anggota')?>',
            type: 'POST',
            dataType: 'json',
            data: {'jenis': jenis,'anggota_id':anggota_id}
        })
        .done(function(result) {
            if(result.output){
                $('#jumlah').val(number_format(result.jumlah,0,'','.'));
            }
            console.log("success");
        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            console.log("complete");
        });
    }



</script>













