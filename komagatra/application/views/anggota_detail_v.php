
<?php
	
?>

<style type="text/css">
	.modal-body { background-color: #fff;}
	.img-rounded { border: 1px solid #ccc !important;}
	.center-block { float: none; }
	td.bs-checkbox {vertical-align: middle !important;}
	.btn {margin-top: 2px; margin-bottom: 2px;}
	.select2-choices {
		min-height: 150px;
		max-height: 150px;
		overflow-y: auto;
	}
	#table-detail td,#table-detail th{
		text-align: center;
	}
</style>

	<div class="row">
		<div class="box box-primary">
			<div class="box-body" style="min-height: 500px;">
				<div>
					<p style="text-align:center; font-size: 15pt; font-weight: bold;"> Data <?= $data_anggota->nama;?></p>
				</div>
				<div class="box box-solid box-primary">
					<div class="box-header" title="Detail Pinjaman" data-toggle="" data-original-title="Detail Pinjaman">
						<h3 class="box-title"> Informasi Anggota </h3> 
						<div class="box-tools pull-right">
							<button class="btn btn-primary btn-xs" data-widget="collapse">
								<i class="fa fa-minus"></i>
							</button>
						</div>
					</div>
					<div class="box-body">
						<table style="font-size: 13px; width:100%">
							<tr>
								<td style="width:10%; text-align:center;">
									<?php
									$photo_w = 3 * 30;
									$photo_h = 4 * 30;
									if($data_anggota->file_pic == '') {
										echo '<img src="'.base_url().'assets/theme_admin/img/photo.jpg" alt="default" width="'.$photo_w.'" height="'.$photo_h.'" />';
									}else {
										echo '<img src="'.base_url().'uploads/anggota/' . $data_anggota->file_pic . '" alt="Foto" width="'.$photo_w.'" height="'.$photo_h.'" />';
									}
									?>
								</td> 
								<td style="width: 40%;">
									<table width="100%">
										<tr>
											<th> ID Anggota</th>
											<td> : </td>
											<td> <?php echo 'AG' . sprintf('%04d', $anggota_id) . '' ?></td>
										</tr>
										<tr>
											<th> Nama Anggota </th>
											<td> : </td>
											<td> <?php echo $data_anggota->nama; ?></td>
										</tr>
										<tr>
											<th> Jenis Kelamin </th>
											<td> : </td>
											<td> <?php echo ($data_anggota->jk == 'L') ? 'Laki-Laki' : 'Perempuan'; ?></td>
										</tr>
										<tr>
											<th> Status </th>
											<td> : </td>
											<td> <?php echo $data_anggota->status; ?></td>
										</tr>
										<tr>
											<th> Agama </th>
											<td> : </td>
											<td> <?php echo $data_anggota->agama; ?></td>
										</tr>
										<tr>
											<th> Tempat, Tanggal Lahir  </th>
											<td> : </td>
											<td> <?php echo $data_anggota->tmp_lahir .', '. jin_date_ina ($data_anggota->tgl_lahir); ?></td>
										</tr>
										<tr>
											<th> Alamat </th> 
											<td style="vertical-align: top;"> : </td>
											<td> <?php echo $data_anggota->alamat.'<br />'.$data_anggota->kota;?></td>
										</tr>
										<tr>
											<th> Dept </th>
											<td> : </td>
											<td> <?php echo $data_anggota->departement; ?></td>
										</tr>
										<tr>
											<th> Pekerjaan </th>
											<td> : </td>
											<td> <?php echo $data_anggota->pekerjaan; ?></td>
										</tr>
										<tr>
											<th> No. Telepon</th>
											<td> : </td>
											<td> <?php echo $data_anggota->notelp ;?></td>
										</tr>
										
									</table>
								</td>
								<td style="width: 40%;">
									<table width="100%">
										<tr>
											<th> Rekening </th>
											<td> : </td>
											<td> <?php echo $data_anggota->nama_bank.' - '.$data_anggota->no_rekening.' a/n'.$data_anggota->nama_rekening; ?></td>
										</tr>
										<tr>
											<th> Simpanan Sukarela</th>
											<td> : </td>
											<td> <?php echo number_format($simpanan_sukarela) ;?></td>
										</tr>
										<tr>
											<th> Simpanan Pokok</th>
											<td> : </td>
											<td> <?php echo number_format($simpanan_pokok) ;?></td>
										</tr>
										<tr>
											<th> Simpanan Wajib</th>
											<td> : </td>
											<td> <?php echo number_format($simpanan_wajib) ;?></td>
										</tr>
										<tr>
											<th> Jumlah Simpanan </th>
											<td> : </td>
											<td> <?php echo number_format($simpanan_sukarela + $simpanan_pokok + $simpanan_wajib) ;?></td>
										</tr>
										<tr>
											<th> Jumlah Pinjaman </th>
											<td> : </td>
											<td> 
												<?php 
												$jml_pinj = $this->db->get_where('v_hitung_pinjaman',array('anggota_id'=>$anggota_id))->num_rows();
												echo $jml_pinj;
												;?>		
											</td>
										</tr>
										<tr>
											<th> Pinjaman Lunas </th>
											<td> : </td>
											<td>
												<?php
												$jml_pinj_lunas = $this->db->get_where('v_hitung_pinjaman',array('anggota_id'=>$anggota_id,'lunas'=>'Lunas'))->num_rows();
												echo $jml_pinj_lunas;
												?>
											</td>
										</tr>
										<tr>
											<th> Jumlah Tagihan </th>
											<td> : </td>
											<td>
												<?php
												$arr_tagihan = $this->db->get_where('v_hitung_pinjaman',array('anggota_id'=>$anggota_id,'lunas'=>'Belum'))->result();
												$total_tagihan = 0;
												if(is_array($arr_tagihan)){
													foreach ($arr_tagihan as $key => $row) {
														$total_tagihan += $row->ags_per_bulan * ($row->lama_angsuran - $row->bln_sudah_angsur);
													}
												}

												echo number_format($total_tagihan);
												?>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</div>
				</div>
				<div class="nav-tabs-custom">
		            <ul class="nav nav-tabs">
		              <li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true">Simpanan</a></li>
		              <li class=""><a href="#tab_2" data-toggle="tab" aria-expanded="false">Pinjaman</a></li>
		            </ul>
		            <div class="tab-content">
		              	<div class="tab-pane active" id="tab_1">
		                	<div class="nav-tabs-custom">
					            <ul class="nav nav-tabs">
					              	<li class="active"><a href="#tab_sukarela" data-toggle="tab" aria-expanded="true">Sukarela</a></li>
					              	<li class=""><a href="#tab_pokok" data-toggle="tab" aria-expanded="false">Pokok</a></li>
					              	<li class=""><a href="#tab_wajib" data-toggle="tab" aria-expanded="false">Wajib</a></li>
					            </ul>
					            <div class="tab-content">
					              	<div class="tab-pane active" id="tab_sukarela">
					              		<div id="tbsukarela">
											<div id="filter_tgl" class="input-group" style="display: inline;">
												<a href="javascript:void(0);" id="fm_cetak" class="btn bg-blue" onclick="tambah_simpanan(32);"><i class="fa fa-plus"></i> TAMBAH</a>
												<a href="javascript:void(0);"  class="btn bg-green" onclick="ubah_simpanan(32);"><i class="fa fa-edit"></i> UBAH SIMPANAN BULANAN</a>
											</div>
										</div>
					              		<table 
											id="tablesukarela"
											data-toggle="table"
											data-id-field="id"
											data-url="<?php echo site_url('anggota/ajax_simpanan/32/'.$anggota_id); ?>" 
											data-side-pagination="server"
											data-page-list="[5, 10, 25, 50, 100]"
											data-page-size="5"
											data-toolbar="#tbsukarela"
											data-smart-display="true"
											data-striped="true"
											data-search="false"
											data-show-refresh="true"
											data-show-columns="true"
											data-show-toggle="true"
											data-method="post"
											data-content-type="application/x-www-form-urlencoded"
											data-cache="false" >
											<thead>
												<tr>
													<th data-field="id" data-switchable="false" data-visible="false">ID</th>
													<th data-field="tgl_transaksi" data-sortable="true" data-valign="middle" data-align="center" data-halign="center" data-formatter="tgl_input_ft">Bulan</th>
													<th data-field="total_simpanan" data-sortable="true" data-valign="middle" data-align="center" data-halign="center" data-formatter="total_simp" >Total Simpanan</th>
													<th data-field="jumlah" data-sortable="true" data-valign="middle" data-align="center" data-halign="center" >Simpanan Terakhir</th>
													<th data-field="iuran" data-sortable="true" data-valign="middle" data-align="center" data-halign="center" >Simpanan Bulanan</th>
													<th data-field="nama" data-sortable="true" data-valign="middle" data-align="center" data-halign="center" >Kas Debit</th>
													<th data-field="aksi" data-sortable="true" data-valign="middle" data-align="center" data-halign="center" data-formatter="aksi_ft" >Aksi</th>
												</tr>
											</thead>
										</table>
					              	</div>
					              	<div class="tab-pane" id="tab_pokok">
					              		<div id="tbpokok">
											<div id="filter_tgl" class="input-group" style="display: inline;">
												<a href="javascript:void(0);" id="fm_cetak" class="btn bg-blue" onclick="tambah_simpanan(40);"><i class="fa fa-plus"></i> TAMBAH</a>
												<a href="javascript:void(0);"  class="btn bg-green" onclick="ubah_simpanan(32);"><i class="fa fa-edit"></i> UBAH SIMPANAN BULANAN</a>
											</div>
										</div>
					              		<table 
											id="tablepokok"
											data-toggle="table"
											data-id-field="id"
											data-url="<?php echo site_url('anggota/ajax_simpanan/40/'.$anggota_id); ?>" 
											data-side-pagination="server"
											data-page-list="[5, 10, 25, 50, 100]"
											data-page-size="5"
											data-toolbar="#tbpokok"
											data-smart-display="true"
											data-striped="true"
											data-search="false"
											data-show-refresh="true"
											data-show-columns="true"
											data-show-toggle="true"
											data-method="post"
											data-content-type="application/x-www-form-urlencoded"
											data-cache="false" >
											<thead>
												<tr>
													<th data-field="id" data-switchable="false" data-visible="false">ID</th>
													<th data-field="tgl_transaksi" data-sortable="true" data-valign="middle" data-align="center" data-halign="center" data-formatter="tgl_input_ft">Bulan</th>
													<th data-field="total_simpanan" data-sortable="true" data-valign="middle" data-align="center" data-halign="center" data-formatter="total_simp" >Total Simpanan</th>
													<th data-field="jumlah" data-sortable="true" data-valign="middle" data-align="center" data-halign="center" >Simpanan Terakhir</th>
													<th data-field="iuran" data-sortable="true" data-valign="middle" data-align="center" data-halign="center" >Simpanan Bulanan</th>
													<th data-field="nama" data-sortable="true" data-valign="middle" data-align="center" data-halign="center" >Kas Debit</th>
													<th data-field="aksi" data-sortable="true" data-valign="middle" data-align="center" data-halign="center" data-formatter="aksi_ft" >Aksi</th>
												</tr>
											</thead>
										</table>
					              	</div>
					              	<div class="tab-pane" id="tab_wajib">
					              		<div id="tbwajib">
											<div id="filter_tgl" class="input-group" style="display: inline;">
												<a href="javascript:void(0);" id="fm_cetak" class="btn bg-blue" onclick="tambah_simpanan(41);"><i class="fa fa-plus"></i> TAMBAH</a>
												<a href="javascript:void(0);"  class="btn bg-green" onclick="ubah_simpanan(32);"><i class="fa fa-edit"></i> UBAH SIMPANAN BULANAN</a>
											</div>
										</div>
					              		<table 
											id="tablewajib"
											data-toggle="table"
											data-id-field="id"
											data-url="<?php echo site_url('anggota/ajax_simpanan/41/'.$anggota_id); ?>" 
											data-side-pagination="server"
											data-page-list="[5, 10, 25, 50, 100]"
											data-page-size="5"
											data-smart-display="true"
											data-toolbar="tbwajib"
											data-striped="true"
											data-search="false"
											data-show-refresh="true"
											data-show-columns="true"
											data-show-toggle="true"
											data-method="post"
											data-content-type="application/x-www-form-urlencoded"
											data-cache="false" >
											<thead>
												<tr>
													<th data-field="id" data-switchable="false" data-visible="false">ID</th>
													<th data-field="tgl_transaksi" data-sortable="true" data-valign="middle" data-align="center" data-halign="center" data-formatter="tgl_input_ft">Bulan</th>
													<th data-field="total_simpanan" data-sortable="true" data-valign="middle" data-align="center" data-halign="center" data-formatter="total_simp">Total Simpanan</th>
													<th data-field="jumlah" data-sortable="true" data-valign="middle" data-align="center" data-halign="center" >Simpanan Terakhir</th>
													<th data-field="iuran" data-sortable="true" data-valign="middle" data-align="center" data-halign="center" >Simpanan Bulanan</th>
													<th data-field="nama" data-sortable="true" data-valign="middle" data-align="center" data-halign="center" >Kas Debit</th>
													<th data-field="aksi" data-sortable="true" data-valign="middle" data-align="center" data-halign="center" data-formatter="aksi_ft" >Aksi</th>
												</tr>
											</thead>
										</table>
					              	</div>
					          	</div>
					         </div>
		              	</div>
		              <!-- /.tab-pane -->
		              <div class="tab-pane" id="tab_2">
		               	<table 
							id="tablegrid"
							data-toggle="table"
							data-id-field="id"
							data-url="<?php echo site_url('anggota/ajax_anggota_detail/'.$anggota_id); ?>" 
							data-side-pagination="server"
							data-page-list="[5, 10, 25, 50, 100]"
							data-page-size="5"
							data-smart-display="true"
							data-striped="true"
							data-search="false"
							data-sort-order="desc"
							data-show-refresh="true"
							data-show-columns="true"
							data-show-toggle="true"
							data-method="post"
							data-content-type="application/x-www-form-urlencoded"
							data-cache="false" >
							<thead>
								<tr>
									<th data-field="id" data-switchable="false" data-visible="false">ID</th>
									<th data-field="tgl_pinjam" data-sortable="true" data-valign="middle" data-align="center" data-halign="center" data-formatter="tgl_input_ft">Tgl Pinjaman</th>
									<th data-field="jumlah" data-sortable="true" data-valign="middle" data-align="center" data-halign="center">Jumlah Pinjaman</th>
									<th data-field="ags_per_bulan" data-sortable="true" data-valign="middle" data-align="center" data-halign="center">Jumlah Angsuran</th>
									<th data-field="lama_angsuran" data-sortable="true" data-valign="middle" data-align="center" data-halign="center">Lama Angsuran</th>
									<th data-field="sisa_angsuran" data-sortable="true" data-valign="middle" data-align="center" data-halign="center">Sisa Angsuran</th>
									<th data-field="tgl_tempo" data-sortable="true" data-valign="middle" data-align="center" data-halign="center">Jatuh Tempo</th>
									<th data-field="tgl_lunas" data-sortable="true" data-valign="middle" data-align="center" data-halign="center">Tanggal Pelunasan</th>
									<th data-field="lunas" data-sortable="true" data-valign="middle" data-align="center" data-halign="center">Status Lunas</th>
								</tr>
							</thead>
						</table>
		                
		              </div>
		            </div>
		            <!-- /.tab-content -->
		          </div>
				
				<?php
					//var_dump($data_simpanan);
				?>

			</div><!--box-p -->
		</div><!--box-body -->
	</div><!--row -->

	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="myModalLabel">Tambah Simpanan</h4>
	      </div>
	      <div class="modal-body">
	        <form method="POST" id="form-simpanan" action="<?php echo site_url('anggota/tambah_simpanan');?>">
	        	<input type="hidden" id="jenis_id" name="jenis_id" />
	        	<input type="hidden" id="anggota_id" name="anggota_id" value="<?php echo $anggota_id;?>" />

	        	<div class="form-group">
				    <label >Simpanan Bulanan</label>
				    <select class="form-control" name="iuran" id="iuran">
				    	<option value="0">Tidak</option>
				    	<option value="1">Ya</option>
				    </select>
				  </div>
			  <div class="form-group">
			    <label for="exampleInputEmail1">Nominal</label>
			    <input type="text" class="form-control" id="nominal" name="nominal" placeholder="Nominal">
			  </div>
			  <div class="form-group">
			    <label for="exampleInputEmail1">Bulan</label>
			    <div class="input-group">
				  <input type="text" class="form-control dtpicker" id="bulan" name="bulan" placeholder="Bulan">
				</div>
			  </div>
			  <div class="form-group">
			    <label for="exampleInputEmail1">Kas</label>
			    <select class="form-control" name="kas" id="kas">
			    	<option value="">-- Pilih Kas --</option>
			    	<?php
			    	foreach ($kas as $key => $k) {
			    		?>
			    		<option value="<?php echo $k['id'];?>"><?php echo $k['nama'];?></option>
			    		<?php
			    	}
			    	?>
			    </select>
			  </div>
			  <div class="form-group">
			    <label for="exampleInputEmail1">Keterangan</label>
			    <div class="input-group">
				  <textarea id="keterangan" name="keterangan" class="form-control"></textarea>
				</div>
			  </div>
			</form>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        <button type="button" class="btn btn-primary" id="btn-tambah-simpanan">Save changes</button>
	      </div>
	    </div>
	  </div>
	</div>

	<div class="modal fade" id="SimpananDetail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title">Simpanan <span id="detail-simp"></span></h4>
	      </div>
	      <div class="modal-body">
			<table class="table table-bordered table-striped" id="table-detail">
				<thead>
					<tr>
						<th width="50">No</th>
						<th align="center">Tanggal</th>
						<th>Simpanan</th>
						<th>Keterangan</th>
					</tr>
				</thead>
				<tbody>
					
				</tbody>
			</table>  
	      </div>
	    </div>
	  </div>
	</div>

	<div class="modal fade" id="ModalUbah" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="myModalLabel">Ubah Simpanan</h4>
	      </div>
	      <div class="modal-body">
	        <form method="POST" id="form-ubah" action="<?php echo site_url('anggota/ubah_simpanan');?>">
	        	<input type="hidden" id="jenis_id_ubah" name="jenis_id" />
	        	<input type="hidden" id="anggota_id" name="anggota_id" value="<?php echo $anggota_id;?>" />

				<div class="form-group">
				    <label for="exampleInputEmail1">Nominal</label>
				    <input type="text" class="form-control" id="nominal_ubah" name="nominal" placeholder="Nominal">
				</div>
			</form>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        <button type="button" class="btn btn-primary" id="btn-ubah-simpanan">Save changes</button>
	      </div>
	    </div>
	  </div>
	</div>

<script type="text/javascript">


	function tgl_input_ft(value, row, index) {
		return '<span title="'+value+'">'+row.tgl_txt+'</span>';
	}

	function aksi_ft(value, row, index) {
		var bulan = "'"+row.bulan+" "+row.tahun+"'";
		return '<a href="javascript:void(0);" onclick="tambah_simpanan('+row.jenis_id+','+bulan+')" class="btn btn-primary">Tambah Simpanan</a>';
	}

	function total_simp(value, row, index) {
		var bulan = "'"+row.bulan+" "+row.tahun+"'";
		return '<a href="javascript:void(0);" onclick="lihat_simpanan('+row.anggota_id+','+row.jenis_id+','+bulan+')" >'+row.total_simpanan+'</a>';
	}

	$(function() {

		// var $table = $('#tablegrid');

		$('#nominal, #nominal_ubah').on('change keyup paste', function() {
			var n = parseInt($(this).val().replace(/\D/g, ''), 10);
			$(this).val(number_format(n, 0, '', '.'));
		});
		$(".dtpicker").datetimepicker({
			language:  'id',
			weekStart: 1,
			autoclose: true,
			todayBtn: true,
			todayHighlight: true,
			pickerPosition: 'bottom-right',
			format: "MM yyyy",
			linkField: "periode",
			linkFormat: "yyyy-mm",
			startView: 3,
			minView: 3
		});
	});


	function get_iuran(div,anggota_id,jenis_id)
	{
		$.ajax({
				type	: "POST",
				url		: '<?php echo site_url('anggota/get_iuran');?>',
				data	: {anggota_id:anggota_id,jenis_id:jenis_id},
				dataType : 'json',
				success	: function(result){
					if(result.ok){
						$(div).val(result.data);
					}
				},
				error : function (){
					alert('Error');
				}
			});
	}

	$('#iuran').change(function(){
		var jenis_id = $('#jenis_id').val();
		var anggota_id = $('#anggota_id').val();

		if($(this).val() == 1){
			get_iuran('#nominal',anggota_id,jenis_id)
			$('#nominal').attr('readonly',true);
		}
		


	});


	function tambah_simpanan(jenis_id,bulan='')
	{

		$('#myModal').modal('show');
		$('#iuran').val(0);
		$('.dtpicker').val('');
		$('#nominal').val('');

		$('#kas').val('');
		$('#jenis_id').val(jenis_id);
		$('#bulan').val(bulan);
	}

	function ubah_simpanan(jenis_id)
	{

		$('#ModalUbah').modal('show');
		get_iuran('#nominal_ubah',<?php echo $anggota_id;?>,jenis_id);

		$('#jenis_id_ubah').val(jenis_id);
	}

	function lihat_simpanan(anggota_id,jenis_id,bulan)
	{

		$('#SimpananDetail').modal('show');
		$('#detail-simp').text(bulan);
		$.ajax({
			type	: "POST",
			url		: '<?php echo site_url('anggota/detail_simpanan');?>',
			data	: {anggota_id:anggota_id,jenis_id:jenis_id,bulan:bulan},
			dataType : 'json',
			success	: function(result){
				if(result.ok){
					$('#table-detail tbody').html('');
					$.each( result.data, function( key, value ) {
					 	$('#table-detail tbody').append('<tr><td>'+(key +1)+'</td><td>'+value.tgl_transaksi+'</td><td>'+value.jumlah+'</td><td>'+value.keterangan+'</td></tr>');
					});
				}
			},
			error : function (){
				alert('Error');
			}
		});
	}

	$('#btn-tambah-simpanan').on('click',function(){

		var dt = $('.dtpicker').val();
		var nom = $('#nominal').val();
		var kas = $('#kas').val();
		if(dt == '' || nom == '' || kas == ''){
			alert('Data Harus di Isi');
		}else {
			$('#btn-tambah-simpanan').button('loading');
			$.ajax({
				type	: "POST",
				url		: $('#form-simpanan').attr('action'),
				data	: $('#form-simpanan').serialize(),
				dataType : 'json',
				success	: function(result){

					if(result.ok){
						if(result.jenis_id == 32){
							$('#tablesukarela').bootstrapTable('refresh');
						}
						if(result.jenis_id == 40){
							$('#tablepokok').bootstrapTable('refresh');
						}
						if(result.jenis_id == 41){
							$('#tablewajib').bootstrapTable('refresh');
						}
					}else {
						alert(result.msg);	
					}
					$('#btn-tambah-simpanan').button('reset');
					$('#myModal').modal('hide');
				},
				error : function (){
					alert('Error');
				}
			});  
		}
	});

	$('#btn-ubah-simpanan').on('click',function(){

		$('#btn-ubah-simpanan').button('loading');
			$.ajax({
				type	: "POST",
				url		: $('#form-ubah').attr('action'),
				data	: $('#form-ubah').serialize(),
				dataType : 'json',
				success	: function(result){

					if(result.ok){
						alert("Sukses Merubah Simapanan");	
					}else {
						alert(result.msg);	
					}
					$('#btn-ubah-simpanan').button('reset');
					$('#ModalUbah').modal('hide');
				},
				error : function (){
					alert('Error');
				}
			});  
	});


</script>