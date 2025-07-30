<?php
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
require_once FCPATH . 'vendor/autoload.php';
class API extends MY_Controller {

    public function upload_mb52()
    {
        $config['upload_path']   = './uploads/';
        $config['allowed_types'] = 'xls|xlsx';

        $this->upload->initialize($config);
        if (!$this->upload->do_upload('upload-mb52')) {
            // Jika upload gagal, tampilkan error
            $error = $this->upload->display_errors();
            $this->fb(["statusCode" => 500, "res" => $error]);
        }
        
        // Jika upload berhasil
        $file_data = $this->upload->data();
        $file_path = $file_data['full_path'];
        // Load PHPExcel
        $objPHPExcel = IOFactory::load($file_path);

        // Membaca sheet pertama
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        $data_excel = [];
        $this->model->delete("data_sap","id !=");
        for ($i=2; $i <= $highestRow; $i++) { 
            $sap_part_no = $sheet->getCell('A'.$i)->getValue();
            if(empty($sap_part_no)){
                continue;
            }

            $part_name = htmlentities($sheet->getCell('B'.$i)->getValue());
            $plant = $sheet->getCell('C'.$i)->getValue();
            $sloc = $sheet->getCell('D'.$i)->getValue();
            $base_unit = $sheet->getCell('K'.$i)->getValue();
            $sap_qty = $sheet->getCell('L'.$i)->getValue();
            $sap_value = $sheet->getCell('N'.$i)->getValue();
            $price = round($sap_value/$sap_qty);

            $data_excel[] = [
                "sap_part_no" => $sap_part_no,
                "part_name" => $part_name,
                "plant" => $plant,
                "sloc" => $sloc,
                "base_unit" => $base_unit,
                "sap_qty" => $sap_qty,
                "sap_value" => $sap_value,
                "price" => $price,
            ];
        }

        if(empty($data_excel)){
            $fb = ["statusCode" => 500, "res" => "Data excel kosong"];
            $this->fb($fb);
        }

        $this->model->insert_batch("data_sap",$data_excel);

        $fb = ["statusCode" => 200, "res" => "Upload success"];
        unlink($file_path);
        $this->fb($fb);
    }
    
    public function upload_juklak()
    {
        $clear = $this->input->post("clear");
        $config['upload_path']   = './uploads/';
        $config['allowed_types'] = 'xls|xlsx';

        $this->upload->initialize($config);
        if (!$this->upload->do_upload('upload-juklak')) {
            // Jika upload gagal, tampilkan error
            $error = $this->upload->display_errors();
            $this->fb(["statusCode" => 500, "res" => $error]);
        }
        
        // Jika upload berhasil
        $file_data = $this->upload->data();
        $file_path = $file_data['full_path'];
        // Load PHPExcel
        $objPHPExcel = IOFactory::load($file_path);

        // Membaca sheet pertama
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();

        if($clear == "yes"){
            $this->model->delete("master_juklak","id !=");
        }

        $data_submit = [];
        for ($i=4; $i <= $highestRow; $i++) { 
            $part_no = str_replace("#N/A","",$sheet->getCell('D'.$i)->getValue());
            if(empty($part_no)){
                continue;
            }

            $job_no = str_replace("#N/A","",$sheet->getCell('C'.$i)->getValue());
            $sap_part_no = str_replace("#N/A","",$sheet->getCell('E'.$i)->getValue());
            $part_name = str_replace("#N/A","",$sheet->getCell('F'.$i)->getValue());
            $routing = str_replace("#N/A","",$sheet->getCell('G'.$i)->getValue());
            $supplier = str_replace("#N/A","",$sheet->getCell('H'.$i)->getValue());
            $s = str_replace("#N/A","",$sheet->getCell('I'.$i)->getValue());
            $ratio = str_replace("#N/A","",$sheet->getCell('J'.$i)->getValue());
            $is_bom = str_replace("#N/A","",$sheet->getCell('K'.$i)->getValue());
            $model = str_replace("#N/A","",$sheet->getCell('L'.$i)->getValue());

            $data_submit[$part_no] = [
                "job_no" => $job_no,
                "part_no" => $part_no,
                "sap_part_no" => $sap_part_no,
                "part_name" => $part_name,
                "routing" => $routing,
                "supplier" => $supplier,
                "s" => $s,
                "ratio" => $ratio,
                "is_bom" => $is_bom,
                "model" => $model,
            ];
        }

        if(empty($data_submit)){
            $fb = ["statusCode" => 500, "res" => "Data excel kosong"];
            $this->fb($fb);
        }

        $this->model->insert_batch("master_juklak",$data_submit);

        $fb = ["statusCode" => 200, "res" => "Upload success"];
        unlink($file_path);
        $this->fb($fb);
    }

    public function upload_wip()
    {
        $config['upload_path']   = './uploads/';
        $config['allowed_types'] = 'xls|xlsx';

        $this->upload->initialize($config);
        if (!$this->upload->do_upload('upload-wip')) {
            // Jika upload gagal, tampilkan error
            $error = $this->upload->display_errors();
            $this->fb(["statusCode" => 500, "res" => $error]);
        }
        
        // Jika upload berhasil
        $file_data = $this->upload->data();
        $file_path = $file_data['full_path'];
        // Load PHPExcel
        $objPHPExcel = IOFactory::load($file_path);

        // Membaca sheet pertama
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();

        $plant = "D105";
        $data_excel = [];
        $this->model->delete("data_wip","id !=");
        for ($i=2; $i <= $highestRow; $i++) { 
            $sap_part_no = $sheet->getCell('A'.$i)->getValue();
            if(empty($sap_part_no)){
                continue;
            }

            $part_name = htmlentities($sheet->getCell('B'.$i)->getValue());
            $qty = $sheet->getCell('C'.$i)->getValue();
            $last_vin = $sheet->getCell('D'.$i)->getValue();

            $data_excel[] = [
                "sap_part_no" => $sap_part_no,
                "part_name" => $part_name,
                "plant" => $plant,
                "wip_qty" => $qty,
                "last_vin" => $last_vin,
            ];
        }

        if(empty($data_excel)){
            $fb = ["statusCode" => 500, "res" => "Data excel kosong"];
            $this->fb($fb);
        }

        
        $this->model->insert_batch("data_wip",$data_excel);

        $fb = ["statusCode" => 200, "res" => "Upload success", "file_path" => $file_path];
        unlink($file_path);
        $this->fb($fb);
    }
	
	public function fetch_data()
	{
		$cookie = $this->session->userdata('ci3_cookie');
		if (!$cookie) {
			$this->fb(["statusCode" => 500, "res" => "Belum login ke remote server. Silakan login dulu."]);
		}

		$shop = $this->input->get("shop");
		if(empty($shop)){
			$this->fb(["statusCode" => 500, "res" => "Shop tidak boleh kosong"]);
		}

		$group = $this->input->get("group") ?? "ALL";
		$url = URL_GET_DATA;

		$post_data = http_build_query([
			'shop'   => $shop,
			'group'  => $group,
			'submit' => 'Show Final Data'
		]);

		$getSloc = $this->model->gd("master_sloc","sloc","dept = '$shop'","row");
		if(empty($getSloc)){
			$this->fb(["statusCode" => 500, $shop." belum memiliki code Sloc silahkan tambahkan terlebih dahulu di menu Master Sloc"]);
		}
		$sloc = $getSloc->sloc;

		$phpsessid   = $cookie["PHPSESSID"];
		$ci_session  = $cookie["ci_session"]; // hasil login remote
		$cookie_header = "PHPSESSID={$phpsessid}; ci_session={$ci_session}";
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
			'Accept-Encoding: gzip, deflate',
			'Accept-Language: id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7',
			'Cache-Control: max-age=0',
			'Connection: keep-alive',
			'Content-Type: application/x-www-form-urlencoded',
			'Origin: http://10.59.114.111:8080',
			'Referer: http://10.59.114.111:8080/stoweb/index.php/entrylist/dataFinal',
			'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36',
			'Cookie: ' . $cookie_header,
		]);
		curl_setopt($ch, CURLOPT_ENCODING, '');

		$response = curl_exec($ch);
		curl_close($ch);

		if ($response === false) {
			$error_msg = curl_error($ch);         // ambil pesan error
			$error_code = curl_errno($ch);        // ambil kode error
			curl_close($ch);                      // tutup curl

			show_error("Gagal ambil data dari target. CURL Error $error_code: $error_msg");
		}

		// Ambil <table id="example1">
		$dom = new DOMDocument();
		libxml_use_internal_errors(true);
		$dom->loadHTML($response);
		libxml_clear_errors();

		$table = $dom->getElementById('example1');

		if (!$table) {
			show_error("Table dengan id='example1' tidak ditemukan.");
		}
		
		$rows = $table->getElementsByTagName('tr');
		$data = [];

		//DELETE DATA ALL BY SHOP ID
		$this->model->delete('data_actual','shop_id = "'.$shop.'"');
		
		foreach ($rows as $row) {
			$cells = $row->getElementsByTagName('td');

			// Kalau gak ada <td>, skip (mungkin header)
			if ($cells->length == 0) continue;

			// Ambil data berdasarkan index kolom
			$part_no   = trim($cells->item(2)->textContent ?? '');
			$part_name = trim($cells->item(4)->textContent ?? '');
			$pcs_ok    = trim($cells->item(16)->textContent ?? '');
			$pcs_ng    = trim($cells->item(17)->textContent ?? '');
			$pcs_total = trim($cells->item(18)->textContent ?? '');

			if($pcs_ok <= 0 && $pcs_ng <= 0 && $pcs_total <= 0){
				continue;
			}

			$sap_part_no = $part_no;
			if($sloc == "D100"){
				$getSapAktif = $this->model->gd("master_juklak","sap_part_no","part_no = '$part_no'","row");
				$sap_part_no = empty($getSapAktif->sap_part_no) ? $part_no : $getSapAktif->sap_part_no;
			}
			$data[] = [
				'sap_part_no'   => $sap_part_no,
				'part_name' 	=> $part_name,
				'plant' 		=> 'D105',
				'shop_id' 		=> $shop,
				'act_qty' 		=> $pcs_total,
				'sloc'			=> $sloc,
			];
		}

		$this->model->insert_batch('data_actual',$data);
		$this->fb(["statusCode" => 200, "res" => "Syncronize ".number_format(count($data),0,"",".")." data berhasil"]);
		// header('Content-Type: application/json');
		// echo json_encode($data);
	}

	public function load_data()
	{
		$query = $this->db->query("
			SELECT 
				sap_part_no,
				part_name,
				plant,
				sloc,
				price,
				sap_qty,
				actual_qty,
				total_price_sap,
				total_price_act,
				selisih_qty,
				selisih_harga
			FROM (
				-- Part yang ada di data_sap
				SELECT 
					ds.sap_part_no,
					MAX(ds.part_name) AS part_name,
					MAX(ds.plant) AS plant,
					COALESCE(MAX(ds.sloc), MAX(da.sloc)) AS sloc,  -- ambil dari ds.sloc dulu, kalau ga ada ambil da.sloc
					MAX(ds.price) AS price,
					SUM(ds.sap_qty) AS sap_qty,
					IFNULL(SUM(da.total_act_qty), 0) - IFNULL(SUM(dw.total_wip_qty), 0) AS actual_qty,
					SUM(ds.sap_qty * ds.price) AS total_price_sap,
					MAX(ds.price) * (IFNULL(SUM(da.total_act_qty), 0) - IFNULL(SUM(dw.total_wip_qty), 0)) AS total_price_act,
					SUM(ds.sap_qty) - (IFNULL(SUM(da.total_act_qty), 0) - IFNULL(SUM(dw.total_wip_qty), 0)) AS selisih_qty,
					SUM(ds.sap_qty * ds.price) - MAX(ds.price) * (IFNULL(SUM(da.total_act_qty), 0) - IFNULL(SUM(dw.total_wip_qty), 0)) AS selisih_harga
				FROM data_sap ds
				LEFT JOIN (
					SELECT sap_part_no, sloc, SUM(act_qty) AS total_act_qty
					FROM data_actual
					GROUP BY sap_part_no, sloc
				) da ON da.sap_part_no = ds.sap_part_no
				LEFT JOIN (
					SELECT sap_part_no, SUM(wip_qty) AS total_wip_qty
					FROM data_wip
					GROUP BY sap_part_no
				) dw ON dw.sap_part_no = ds.sap_part_no
				GROUP BY ds.sap_part_no

				UNION ALL

				-- Part yang cuma ada di data_actual (gak ada di data_sap)
				SELECT 
					da.sap_part_no,
					MAX(da.part_name),
					MAX(da.plant),
					MAX(da.sloc), -- ambil sloc dari data_actual
					0,
					0,
					SUM(da.act_qty) - IFNULL(SUM(dw.wip_qty), 0),
					0,
					0,
					-(SUM(da.act_qty) - IFNULL(SUM(dw.wip_qty), 0)),
					0
				FROM data_actual da
				LEFT JOIN data_sap ds ON ds.sap_part_no = da.sap_part_no
				LEFT JOIN (
					SELECT sap_part_no, SUM(wip_qty) AS wip_qty
					FROM data_wip
					GROUP BY sap_part_no
				) dw ON dw.sap_part_no = da.sap_part_no
				WHERE ds.sap_part_no IS NULL
				GROUP BY da.sap_part_no
			) AS result
			ORDER BY sap_part_no;
		");

		$result = $query->result_array();
        foreach ($result as &$row) {
            $row['price'] = isset($row['price']) ? (int)round($row['price'], 2) : '0.00';
            $row['sap_qty'] = (int)$row['sap_qty'];
            $row['actual_qty'] = (int)$row['actual_qty'];
            $row['selisih_qty'] = (int)$row['selisih_qty'];
            $row['total_price_act'] = (int)round($row['total_price_act'], 2);
            $row['total_price_sap'] = (int)round($row['total_price_sap'], 2);
            $row['selisih_harga'] = (int)round($row['selisih_harga'], 2);
        }
		echo json_encode([
			"data" => $result
		]);
	}
	
	public function load_data_wip()
	{
		$query = $this->db->query("
			SELECT 
				sap_part_no,
				part_name,
				plant,
				SUM(wip_qty) AS wip_qty,
				last_vin
			FROM data_wip
			GROUP BY sap_part_no
			ORDER BY sap_part_no;
		");

		$result = $query->result_array();
        foreach ($result as &$row) {
            $row['wip_qty'] = (int)$row['wip_qty'];
        }
		echo json_encode([
			"data" => $result
		]);
	}
	
	public function load_data_juklak()
	{
		$query = $this->db->query("
			SELECT 
				part_no,
				sap_part_no,
				job_no,
				part_name,
				routing,
				supplier,
				ratio,
				model
			FROM master_juklak
			ORDER BY sap_part_no;
		");

		$result = $query->result_array();

		echo json_encode([
			"data" => $result
		]);
	}
	
	public function load_data_sloc()
	{
		$query = $this->db->query("
			SELECT 
				dept,
				sloc,
				label
			FROM master_sloc
			ORDER BY dept;
		");

		$result = $query->result_array();

		echo json_encode([
			"data" => $result
		]);
	}
	
	public function load_data_actual()
	{
		$query = $this->db->query("
			SELECT 
				da.sap_part_no,
				da.part_name,
				da.plant,
				da.shop_id,
				da.sloc,
				da.act_qty
			FROM data_actual da
			ORDER BY sap_part_no;
		");

		$result = $query->result_array();
        foreach ($result as &$row) {
            $row['act_qty'] = (int)$row['act_qty'];
        }
		echo json_encode([
			"data" => $result
		]);
	}
	
	public function load_data_mb52()
	{
		$query = $this->db->query("
			SELECT 
				sap_part_no,
				part_name,
				plant,
				sloc,
				base_unit,
				sap_qty,
				price,
				(sap_qty*price) as total_price
			FROM data_sap
			ORDER BY sap_part_no;
		");

		$result = $query->result_array();
        foreach ($result as &$row) {
            $row['sap_qty'] = (int)$row['sap_qty'];
            $row['price'] = (int)round($row['price']);
            $row['total_price'] = (int)round($row['total_price']);
        }
		echo json_encode([
			"data" => $result
		]);
	}

	public function update_sloc()
	{
		$this->form_validation
			->set_rules("method","Method","required|trim|in_list[add,update]")
			->set_rules("id_dept","Departement","required|trim")
			->set_rules("input-sloc","Sloc","required|trim");
		if($this->form_validation->run() === FALSE){
			$this->fb(["statusCode" => 500, "res" => validation_errors()]);
		}

		$method = $this->input->post("method");
		$dept = $this->input->post("id_dept");
		$sloc = $this->input->post("input-sloc");

		$data_shop = LIST_SHOP;

		$data_submit = [
			"dept" => $dept,
			"sloc" => $sloc,
			"label" => $data_shop[$dept],
		];
		if($method == "add"){
			//CHECK DOUBLE DATA
			$validasi = $this->model->gd("master_sloc","dept","dept = '$dept'","row");
			if(!empty($validasi)){
				$this->fb(["statusCode" => 500, "res" => "Dept sudah ada"]);
			}

			$this->model->insert("master_sloc",$data_submit);
		}else{
			$this->model->update("master_sloc","dept = '$dept'",$data_submit);
		}
		
		$this->fb(["statusCode" => 200]);
	}

	public function delete_sloc()
	{
		$this->form_validation->set_rules("dept","Departement","required|trim");
		if($this->form_validation->run() === FALSE){
			$this->fb(["statusCode" => 500, "res" => validation_errors()]);
		}

		$dept = $this->input->post("dept");
		
		$this->model->delete("master_sloc","dept = '$dept'");
		
		$this->fb(["statusCode" => 200]);
	}
	
	public function remote_login()
	{
		$url = URL_LOGIN_GET_DATA; // Ini URL Flask Python lu

		$ch = curl_init($url);

		curl_setopt_array($ch, [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER => false,
			CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
			CURLOPT_TIMEOUT => 10
		]);

		$response = curl_exec($ch);

		if ($response === false) {
			$err = curl_error($ch);
			curl_close($ch);
			$this->fb(["statusCode" => 500, "res" => "Disconnect", "message" => "Curl error ke Python: " . $err]);
		}

		curl_close($ch);

		$data = json_decode($response, true);
		if (!isset($data['cookies']['ci_session'])) {
			$this->fb(["statusCode" => 500, "res" => "Disconnect", "message" => "Gagal ambil session CI3 dari Python."]);
		}

		// Simpan ke session lokal
		$this->session->set_userdata('ci3_cookie', $data['cookies']);
		$this->session->set_userdata('remote_session_raw', $data['cookies']); // Optional

		$this->fb(["statusCode" => 200, "res" => "Connected", "cookie" => $data['cookies']]);

		// Optional: print hasil cookie-nya
		// echo "<pre>"; print_r($data['cookies']); echo "</pre>";
	}

	function check_remote()
	{
		$ci3_cookie = $this->session->userdata("ci3_cookie");
		if(!empty($ci3_cookie)){
			$this->fb(["statusCode" => 200, "res" => "Connected", "cookie" => $ci3_cookie]);
		}else{
			$this->fb(["statusCode" => 500, "res" => "Disconnect", "cookie" => $ci3_cookie]);
		}
	}
}
