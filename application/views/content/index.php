<style>
	#table-reconcile td, #table-reconcile th {
		text-align: center;
		vertical-align: middle;
	}
	#table-wip td, #table-wip th {
		text-align: center;
		vertical-align: middle;
	}
	#table-actual td, #table-actual th {
		text-align: center;
		vertical-align: middle;
	}
	#table-mb52 td, #table-mb52 th {
		text-align: center;
		vertical-align: middle;
	}
	#table-juklak td, #table-juklak th {
		text-align: center;
		vertical-align: middle;
	}
	#dt-length-0, #dt-length-1, #dt-length-2, #dt-length-3, #dt-length-4, #dt-length-5 {
		margin-right: .5rem !important;
	}
</style>
<div class="row">
    <div class="col-12">
        <div class="card" style="background: rgba(255,255,255,0.7)">
            <div class="card-body">
                <div class="row">
					<div class="col-12">
						<div class="card">
							<div class="card-body">
								<ul class="nav nav-tabs" id="myTab" role="tablist">
									<li class="nav-item" role="presentation">
										<button class="nav-link active" id="reconcile-tab" data-bs-toggle="tab" data-bs-target="#reconcile" type="button" role="tab" aria-controls="reconcile" aria-selected="true" onclick="load_reconcile()">Reconcile</button>
									</li>
									<li class="nav-item" role="presentation">
										<button class="nav-link" id="wip-tab" data-bs-toggle="tab" data-bs-target="#wip" type="button" role="tab" aria-controls="wip" aria-selected="false" onclick="load_wip()">Data WIP</button>
									</li>
									<li class="nav-item" role="presentation">
										<button class="nav-link" id="mb52-tab" data-bs-toggle="tab" data-bs-target="#mb52" type="button" role="tab" aria-controls="mb52" aria-selected="false" onclick="load_mb52()">Data MB52</button>
									</li>
									<li class="nav-item" role="presentation">
										<button class="nav-link" id="actual-tab" data-bs-toggle="tab" data-bs-target="#actual" type="button" role="tab" aria-controls="actual" aria-selected="false" onclick="load_actual()">Data Actual</button>
									</li>
									<li class="nav-item" role="presentation">
										<button class="nav-link" id="juklak-tab" data-bs-toggle="tab" data-bs-target="#juklak" type="button" role="tab" aria-controls="juklak" aria-selected="false" onclick="load_juklak()">Master Juklak</button>
									</li>
									<li class="nav-item" role="presentation">
										<button class="nav-link" id="sloc-tab" data-bs-toggle="tab" data-bs-target="#sloc" type="button" role="tab" aria-controls="sloc" aria-selected="false" onclick="load_sloc()">Master Sloc</button>
									</li>
								</ul>
								<div class="tab-content" id="myTabContent">
									<div class="tab-pane fade show active" id="reconcile" role="tabpanel" aria-labelledby="reconcile-tab">
										<div class="table-responsive" style="overflow: auto; height: calc(100vh - 20vh);">
											<table id="table-reconcile" class="table table-bordered table-striped table-hover">
												<thead>
													<tr>
														<th rowspan="2">Part No</th>
														<th rowspan="2">Part Name</th>
														<th rowspan="2">Plant</th>
														<th rowspan="2">SLOC</th>
														<th rowspan="2">Price/Pcs</th>
														<th colspan="2">SAP</th>
														<th colspan="2">Actual</th>
														<th colspan="2">Different</th>
													</tr>
													<tr>
														<th>Qty</th>
														<th>Total Price</th>
														<th>Qty</th>
														<th>Total Price</th>
														<th>Qty</th>
														<th>Total Price</th>
													</tr>
												</thead>
											</table>
										</div>
									</div>
									<div class="tab-pane fade" id="wip" role="tabpanel" aria-labelledby="wip-tab">
                        				<div class="mt-3 mb-2 w-100">
											<button class="btn btn-sm btn-light border border-dark" data-bs-toggle="modal" data-bs-target="#modal-upload-wip"><img src="<?=base_url("assets/image/icon-upload.svg"); ?>">Upload WIP</button>
										</div>
										<div class="table-responsive" style="overflow: auto; height: calc(100vh - 20vh);">
											<table id="table-wip" class="table table-bordered table-striped table-hover" style="width: 100%">
												<thead>
													<tr>
														<th>Part No</th>
														<th>Part Name</th>
														<th>Plant</th>
														<th>Qty</th>
														<th>Last VIN</th>
													</tr>
												</thead>
											</table>
										</div>
									</div>
									<div class="tab-pane fade" id="mb52" role="tabpanel" aria-labelledby="mb52-tab">
                        				<div class="mt-3 mb-2 w-100">
                        					<button class="btn btn-sm btn-light border border-dark" data-bs-toggle="modal" data-bs-target="#modal-upload-mb52"><img src="<?=base_url("assets/image/icon-upload.svg"); ?>">Upload MB52</button>
										</div>
										<div class="table-responsive" style="overflow: auto; height: calc(100vh - 20vh);">
											<table id="table-mb52" class="table table-bordered table-striped table-hover" style="width: 100%">
												<thead>
													<tr>
														<th>Part No</th>
														<th>Part Name</th>
														<th>Plant</th>
														<th>SLOC</th>
														<th>Base Unit</th>
														<th>Qty</th>
														<th>Price/Pcs</th>
														<th>Total Price</th>
													</tr>
												</thead>
											</table>
										</div>
									</div>
									<div class="tab-pane fade" id="juklak" role="tabpanel" aria-labelledby="juklak-tab">
                        				<div class="mt-3 mb-2 w-100">
                        					<button class="btn btn-sm btn-light border border-dark" data-bs-toggle="modal" data-bs-target="#modal-upload-juklak"><img src="<?=base_url("assets/image/icon-upload.svg"); ?>">Upload Juklak</button>
										</div>
										<div class="table-responsive" style="overflow: auto; height: calc(100vh - 20vh);">
											<table id="table-masterjuklak" class="table table-bordered table-striped table-hover" style="width: 100%">
												<thead>
													<tr>
														<th>Part No</th>
														<th>SAP Part No</th>
														<th>Job No</th>
														<th>Part Name</th>
														<th>Routing</th>
														<th>Supplier</th>
														<th>Ratio</th>
														<th>Model</th>
													</tr>
												</thead>
											</table>
										</div>
									</div>
									<div class="tab-pane fade" id="sloc" role="tabpanel" aria-labelledby="sloc-tab">
                        				<div class="mt-3 mb-2 w-100">
											<button class="btn btn-sm btn-light border border-dark" onclick="tambahsloc(this)" data-method="add"><i class="fas fa-plus pe-2"></i>Tambah SLOC</button>
										</div>
										<div class="table-responsive w-50" style="overflow: auto; height: calc(100vh - 20vh);">
											<table id="table-mastersloc" class="table table-bordered table-striped table-hover" style="width: 100%">
												<thead>
													<tr>
														<th>ID Dept</th>
														<th>Departement</th>
														<th>Sloc</th>
														<th>Action</th>
													</tr>
												</thead>
											</table>
										</div>
									</div>
									<div class="tab-pane fade" id="actual" role="tabpanel" aria-labelledby="actual-tab">
										<div class="w-100 mt-3 mb-2">
											<div class="row">
												<div class="col-lg-6">
													<div class="input-group w-auto">
														<select name="shop" id="shop" class="custom-select ps-2">
															<?php
															foreach (LIST_SHOP as $key => $value) {
																echo '<option value="'.$key.'">'.$value.'</option>';
															}
															?>
														</select>
														<button class="btn btn-info" onclick="sync_actual('manual')"><i class="fas fa-cloud-arrow-down text-light me-1"></i>Sync Actual</button>
														<button class="btn btn-primary ms-2" onclick="sync_actual('all')"><i class="fas fa-cloud-arrow-down text-light me-1"></i>Sync Actual All</button>
													</div>
												</div>
												<div class="col-lg-6 text-end">
													<p class="mb-2">Status Remote : <span id="status-remote-login"></span></p>
													<button class="btn btn-info" onclick="remote('remote_login')">Connect Remote</button>
												</div>
											</div>
										</div>
										<div class="table-responsive" style="overflow: auto; height: calc(100vh - 20vh);">
											<table id="table-actual" class="table table-bordered table-striped table-hover" style="width: 100%">
												<thead>
													<tr>
														<th>Part No</th>
														<th>Part Name</th>
														<th>Plant</th>
														<th>Sloc</th>
														<th>Shop</th>
														<th>Qty</th>
													</tr>
												</thead>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view("component/formuploadwip"); ?>
<?php $this->load->view("component/formuploadmb52"); ?>
<?php $this->load->view("component/formuploadjuklak"); ?>
<?php $this->load->view("component/forminputsloc"); ?>
