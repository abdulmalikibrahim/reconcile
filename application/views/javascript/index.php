<script>
    $("#btn-upload-mb52").click(function() {
        $("#form-upload-mb52").trigger("submit");
    });

    $("#btn-upload-delaytransaction").click(function() {
        $("#form-upload-delaytransaction").trigger("submit");
    });
    
    $('#btn-upload-wip').click(function () {
        $("#form-upload-wip").trigger("submit");
	});
    
    $("#btn-upload-juklak, #btn-upload-juklak-clear").click(function(e) {
        const idbtn = e.target.id;

        if(idbtn == "btn-upload-juklak-clear"){
            $("#form-upload-juklak").attr("data-clear","yes");
        }else{
            $("#form-upload-juklak").attr("data-clear","no");
        }
        $("#form-upload-juklak").trigger("submit");
    });

    $('#form-upload-wip, #form-upload-mb52, #form-upload-juklak, #form-upload-delaytransaction').on('submit', function(e) {
        const idform = e.target.id;
        const page = e.target.dataset.page;
        e.preventDefault(); // Hindari form submit standar

        let formData = new FormData(this);
        if(idform == "form-upload-juklak"){
            const clear = $("#form-upload-juklak").attr("data-clear");
            formData.append("clear",clear)
        }

		const url = `upload_${page}`;

        $.ajax({
            url: url, // ganti sesuai endpoint backend kamu
            type: 'POST',
            data: formData,
            contentType: false, // harus false agar jQuery tidak mengubah Content-Type
            processData: false, // harus false agar FormData tidak diproses menjadi string
            dataType:"JSON",
            beforeSend:function() {
                loading_page("Uploading...","File sedang di upload ke server");  
            },
            success: function(r) {
                d = JSON.parse(JSON.stringify(r));
                if(d.statusCode == 200){
                    Swal.fire({
                        title:"Sukses",
                        html:d.res,
                        icon:"success"
                    });

					$(`#modal-upload-${page}`).modal('hide');
					window[`load_${page}`]();
                }else{
					swal.fire('Error','Upload gagal: ' + res.res,'error');
				}
            },
            error: function(xhr, status, error) {
                console.error(error,xhr);
                const message_error = xhr.responseJSON ? xhr.responseJSON.res : xhr.responseText;
                Swal.fire("Error",message_error,"error");
            }
        });
    });
</script>
<script>
	async function sync_actual(tipe) {
		if(tipe == "manual"){
			await exec_fetch_data($("#shop").val(),"");
			load_actual();
		}else{
			const list_shop = <?= json_encode(LIST_SHOP); ?>;
			for (const shop in list_shop) {
				const value = list_shop[shop];
				try {
					await exec_fetch_data(shop,value);
				} catch (err) {
					console.error("Gagal proses " + shop, err);
					Swal.alert("Error",err,"error");
					break; // kalau mau stop semua saat error
					// atau lanjut aja ke shop berikutnya tanpa break
				}
			}
			Swal.fire("Semua proses selesai", "Mantap bro! Semua shop udah disync!", "success");
			load_actual();
		}
	}

	function exec_fetch_data(shop,name_shop="") {
		return new Promise(function(resolve, reject) {
			$.ajax({
				url: base_url + "fetch_data?shop=" + shop,
				dataType: "JSON",
				beforeSend: function () {
					const shopName = name_shop ? name_shop : shop;
					loading_page("Syncronize " + shopName, "Proses shop: " + shopName + ". Mohon tunggu...");
				},
				success: function (r) {
					const d = JSON.parse(JSON.stringify(r));
					if (d.statusCode == 200) {
						Swal.fire({
							title: "Sukses: " + shop,
							html: d.res,
							icon: "success"
						});
						resolve(); // lanjut ke shop berikutnya
					} else {
						reject("Gagal ambil data shop " + shop);
					}
				},
				error: function (xhr, status, error) {
					const message_error = xhr.responseJSON ? xhr.responseJSON.res : xhr.responseText;
					Swal.fire("Error: " + shop, message_error, "error");
					reject(error);
				}
			});
		});
	}

	function remote(method = "remote_login"){
		const url = method == "remote_login" ? `${base_url}remote_login` : `${base_url}check_remote`;
		$.ajax({
            url: url, // ganti sesuai endpoint backend kamu
            type: 'GET',
            dataType:"JSON",
            beforeSend:function() {
				$("#status-remote-login").html('<i class="fas fa-spinner fa-spin text-danger"></i> Connecting...');
            },
            success: function(r) {
				console.log(r);
                if(r.statusCode == 200){
					$("#status-remote-login").html('<i class="fas fa-circle text-success ps-2 pe-1"></i>Connected');
                }
            },
            error: function(xhr, status, error) {
				console.error(xhr.responseJSON);
				const message_error = xhr.responseJSON ? xhr.responseJSON.res : xhr.responseText;
                console.error(message_error);
				$("#status-remote-login").html('<i class="fas fa-circle text-danger ps-2 pe-1"></i>Disconnect');
            }
        });
	}
</script>
<script>
	function tambahsloc(data) {
		const method = data.dataset.method;
		$("#modal-upload-sloc").modal("show");
		$("#btn-simpan-sloc").attr("data-method",method);
	}

	$("#btn-simpan-sloc").click(function() {
		$("#form-upload-sloc").trigger("submit");
	});

	$("#form-upload-sloc").on("submit",function(e) {
        e.preventDefault(); // Hindari form submit standar

        let formData = new FormData(this);
		formData.append('method',$("#btn-simpan-sloc").attr("data-method"));
		$.ajax({
			type:"post",
			url:`${base_url}update_sloc`,
			data:formData,
			processData: false,
			contentType: false,
			dataType:"JSON",
			beforeSend:function(){
				$("#btn-simpan-sloc").attr("disabled",true);
				$("#btn-simpan-sloc").html("Meyimpan...");
			},
			success:function(res){
				console.log(res.statusCode)
				if(res.statusCode === 200){
					$("#modal-upload-sloc").modal("hide");
					load_sloc();
					Swal.fire("Sukses","Data berhasil disimpan","success");
				}
			},
			error: function(xhr) {
				let msg = xhr.responseJSON?.res || xhr.statusText || "Terjadi kesalahan";
				Swal.fire("Gagal", msg, "error");
			},
			complete:function(){
				$("#btn-simpan-sloc").attr("disabled",false);
				$("#btn-simpan-sloc").html("Simpan");
			}
		});	
	});

	function edit_sloc(data){
		const dept = data.dataset.dept;
		const sloc = data.dataset.sloc;
		$("#modal-upload-sloc").modal("show");
		$("#btn-simpan-sloc").attr("data-method","update");
		$("#id_dept").val(dept);
		$("#input-sloc").val(sloc);
		console.log(dept,sloc)
	};

	function delete_sloc(elm) {
		const dept = $(elm).data("dept");

		Swal.fire({
			title: "Yakin mau hapus?",
			text: `Data SLOC dengan dept "${dept}" akan dihapus!`,
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: "#d33",
			cancelButtonColor: "#3085d6",
			confirmButtonText: "Ya, hapus!"
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					type: "POST",
					url: `${base_url}delete_sloc`, // ganti sesuai endpoint kamu
					data: { dept: dept },
					dataType: "json",
					beforeSend: function () {
						Swal.fire({
							title: "Menghapus...",
							didOpen: () => {
								Swal.showLoading();
							},
							allowOutsideClick: false,
							allowEscapeKey: false
						});
					},
					success: function (res) {
						if (res.statusCode == 200) {
							Swal.fire("Terhapus!", res.res, "success");
							// reload tabel setelah delete
							if (tablemastersloc) tablemastersloc.ajax.reload();
						} else {
							Swal.fire("Gagal", res.res, "error");
						}
					},
					error: function (xhr) {
						Swal.fire("Error", xhr.responseText, "error");
					}
				});
			}
		});
	}
</script>


<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js" integrity="sha512-BkpSL20WETFylMrcirBahHfSnY++H2O1W+UnEEO4yNIl+jI2+zowyoGJpbtk6bx97fBXf++WJHSSK2MV4ghPcg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
<!-- <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js" integrity="sha512-BkpSL20WETFylMrcirBahHfSnY++H2O1W+UnEEO4yNIl+jI2+zowyoGJpbtk6bx97fBXf++WJHSSK2MV4ghPcg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/dataTables.bootstrap4.min.js" integrity="sha512-OQlawZneA7zzfI6B1n1tjUuo3C5mtYuAWpQdg+iI9mkDoo7iFzTqnQHf+K5ThOWNJ9AbXL4+ZDwH7ykySPQc+A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
	function renderWithSort(data, type, row) {
		const num = parseFloat(data);
		if (isNaN(num)) return data;

		if (type === 'display') {
			return num.toLocaleString('id-ID', {
				minimumFractionDigits: 0,
				maximumFractionDigits: 2
			});
		}

		// untuk sorting/filter pakai angka mentah
		return num;
	}

	let tableReconcile = null;
	function load_reconcile() {
		if (tableReconcile) {
			tableReconcile.ajax.reload(); // reload doang
		} else {
			tableReconcile = $('#table-reconcile').DataTable({
				processing: true,
				ajax: {
					url: '<?= base_url("load_data") ?>',
					dataSrc: 'data'
				},
				lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
				pageLength: 25, // default show 25 entries
				columns: [
					{ data: 'sap_part_no' },
					{ data: 'part_name' },
					{ data: 'plant' },
					{ data: 'sloc' },
					{ data: 'price', render: renderWithSort },
					{ data: 'sap_qty', render: renderWithSort },
					{ data: 'total_price_sap', render: renderWithSort },
					{ data: 'actual_qty', render: renderWithSort },
					{ data: 'total_price_act', render: renderWithSort },
					{ data: 'selisih_qty', render: renderWithSort },
					{ data: 'selisih_harga', render: renderWithSort }
				]
			});
		}
	}

	let tableWip = null;
	function load_wip() {
		if (tableWip) {
			tableWip.ajax.reload(); // reload doang
		} else {
			tableWip = $('#table-wip').DataTable({
				processing: true,
				ajax: {
					url: '<?= base_url("load_data_wip") ?>',
					dataSrc: 'data'
				},
				lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
				pageLength: 25,
				columns: [
					{ data: 'sap_part_no' },
					{ data: 'part_name' },
					{ data: 'plant' },
					{ data: 'wip_qty', render: renderWithSort },
					{ data: 'last_vin' }
				]
			});
		}
	}

	let tableactual = null;
	function load_actual() {
		remote('check_remote');
		if (tableactual) {
			tableactual.ajax.reload(); // reload doang
		} else {
			tableactual = $('#table-actual').DataTable({
				processing: true,
				ajax: {
					url: '<?= base_url("load_data_actual") ?>',
					dataSrc: 'data'
				},
				lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
				pageLength: 25,
				columns: [
					{ data: 'sap_part_no' },
					{ data: 'part_name' },
					{ data: 'plant' },
					{ data: 'sloc' },
					{ data: 'shop_id' },
					{ data: 'act_qty', render: renderWithSort }
				]
			});
		}
	}

	let tablemb52 = null;
	function load_mb52() {
		if (tablemb52) {
			tablemb52.ajax.reload(); // reload doang
		} else {
			tablemb52 = $('#table-mb52').DataTable({
				processing: true,
				ajax: {
					url: '<?= base_url("load_data_mb52") ?>',
					dataSrc: 'data'
				},
				lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
				pageLength: 25,
				columns: [
					{ data: 'sap_part_no' },
					{ data: 'part_name' },
					{ data: 'plant' },
					{ data: 'sloc' },
					{ data: 'base_unit' },
					{ data: 'sap_qty', render: renderWithSort },
					{ data: 'price', render: renderWithSort },
					{ data: 'total_price', render: renderWithSort },
				]
			});
		}
	}

	let tablemasterjuklak = null;
	function load_juklak() {
		if (tablemasterjuklak) {
			tablemasterjuklak.ajax.reload(); // reload doang
		} else {
			tablemasterjuklak = $('#table-masterjuklak').DataTable({
				processing: true,
				ajax: {
					url: '<?= base_url("load_data_juklak") ?>',
					dataSrc: 'data'
				},
				lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
				pageLength: 25,
				columns: [
					{ data: 'part_no' },
					{ data: 'sap_part_no' },
					{ data: 'job_no' },
					{ data: 'part_name' },
					{ data: 'routing' },
					{ data: 'supplier' },
					{ data: 'ratio' },
					{ data: 'model' },
				]
			});
		}
	}

	let tablemastersloc = null;
	function load_sloc() {
		if (tablemastersloc) {
			tablemastersloc.ajax.reload(); // reload data
		} else {
			tablemastersloc = $('#table-mastersloc').DataTable({
				processing: true,
				ajax: {
					url: '<?= base_url("load_data_sloc") ?>',
					dataSrc: 'data'
				},
				lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
				pageLength: 10,
				columns: [
					{ data: 'dept' },
					{ data: 'label' },
					{ data: 'sloc' },
					{ 
						data: null,
						render: function(data, type, row) {
							// Kamu bisa tambah ID untuk edit atau modal di sini
							return `<a href="javascript:void(0)" class="btn btn-sm btn-info" onclick="edit_sloc(this)" data-dept="${row.dept}" data-sloc="${row.sloc}">
										<i class="fas fa-pencil-alt pe-1"></i>Edit
									</a>
									<a href="javascript:void(0)" class="btn btn-sm btn-danger" onclick="delete_sloc(this)" data-dept="${row.dept}">
										<i class="fas fa-trash-alt pe-1"></i>Hapus
									</a>`;
						},
						orderable: false,
						searchable: false
					}
				],
				columnDefs: [
					{
						targets: 3, // Kolom ke-4 (0-indexed)
						className: 'text-center'
					}
				]
			});
		}
	}
	
	let tabledelaytransaction = null;
	function load_delaytransaction() {
		if (tabledelaytransaction) {
			tabledelaytransaction.ajax.reload(); // reload data
		} else {
			tabledelaytransaction = $('#table-delaytransaction').DataTable({
				processing: true,
				ajax: {
					url: '<?= base_url("load_data_delaytransaction") ?>',
					dataSrc: 'data'
				},
				lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
				pageLength: 25,
				columns: [
					{ data: 'part_number' },
					{ data: 'qty_problem', render: renderWithSort },
					{ data: 'price_part', render: renderWithSort },
					{ data: 'total_ammount', render: renderWithSort },
					{ data: 'problem' },
					{ data: 'pic' },
					{ data: 'sloc' },
					{ data: 'status' },
				],
				columnDefs: [
					{
						targets: [0,1,2,3,5,6,7], // Kolom ke-4 (0-indexed)
						className: 'text-center'
					}
				]
			});
		}
	}

	let tableReport = null;
	function load_report() {
		if (tableReport) {
			tableReport.ajax.reload(); // reload doang
		} else {
			tableReport = $('#table-report').DataTable({
				processing: true,
				ajax: {
					url: '<?= base_url("load_data_report") ?>',
					dataSrc: 'data'
				},
				lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
				pageLength: 25, // default show 25 entries
				columns: [
					{ data: 'sloc' },
					{ data: 'total_sap_qty', render: renderWithSort },
					{ data: 'total_sap_price', render: renderWithSort },
					{ data: 'total_actual_qty', render: renderWithSort },
					{ data: 'total_actual_price', render: renderWithSort },
					{ data: 'total_diff_qty', render: renderWithSort },
					{ data: 'total_diff_price', render: renderWithSort },
					{ data: 'percent' }
				],
				columnDefs: [
					{
						targets: [0,1,2,3,4,5,6,7], // Kolom ke-4 (0-indexed)
						className: 'text-center'
					}
				]
			});
		}
	}

	let tableReport10 = null;
	function load_report10() {
		if (tableReport10) {
			tableReport10.ajax.reload(); // reload doang
		} else {
			tableReport10 = $('#table-report10').DataTable({
				processing: true,
				ajax: {
					url: '<?= base_url("load_top_10") ?>',
					dataSrc: 'data'
				},
				lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
				pageLength: 10, // default show 25 entries
				columns: [
					{ data: 'sap_part_no' },
					{ data: 'part_name' },
					{ data: 'sloc' },
					{ data: 'price', render: renderWithSort },
					{ data: 'sap_qty', render: renderWithSort },
					{ data: 'total_price_sap', render: renderWithSort },
					{ data: 'actual_qty', render: renderWithSort },
					{ data: 'total_price_act', render: renderWithSort },
					{ data: 'selisih_qty', render: renderWithSort },
					{ data: 'selisih_harga', render: renderWithSort }
				],
				columnDefs: [
					{
						targets: [0,2,3,4,5,6,7,8,9], // Kolom ke-4 (0-indexed)
						className: 'text-center'
					}
				]
			});
		}
	}

	$(document).ready(function () {
		afterLoad();

		// Event listener untuk klik tab
		$(".nav-link").on("click", function () {
			const page = $(this).data("page");
			sessionStorage.setItem("page", page);
			callPageLoader(page);
		});
	});

	function afterLoad() {
		// Ambil page terakhir, default "reconcile"
		const page = sessionStorage.getItem('page') ?? "reconcile";

		// Set tab active sesuai page
		$(".nav-link").removeClass("active");
		$(`#${page}-tab`).addClass("active");
		$(`.tab-pane`).removeClass("show active");
		$(`#${page}`).addClass("show active");

		// Panggil loader sesuai page
		callPageLoader(page);
	}

	function callPageLoader(page) {
		// Pastikan fungsi ada sebelum dipanggil
		if (typeof window["load_" + page] === "function") {
			window["load_" + page]();
		} else {
			console.warn(`Function load_${page}() belum didefinisikan`);
		}
	}
</script>
