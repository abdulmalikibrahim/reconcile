<?php
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
require_once FCPATH . 'vendor/autoload.php';
class API extends MY_Controller {
    function dataCapex()
    {
        $this->form_validation->set_rules('shop', 'Shop', 'trim|required');
        if ($this->form_validation->run() === FALSE) {
            $fb = ["statusCode" => 500, "res" => validation_errors()];
            $this->fb($fb);
        }
        $shop = $this->input->post("shop");

        $dataPlan = [];
        $plan = $this->getDataCapex("plan",$shop);
        foreach ($plan as $plan) {
            $dataPlan[] = floatval(round($plan->Budget,3));
        }

        $dataActual = [];
        $actual = $this->getDataCapex("actual",$shop);
        foreach ($actual as $actual) {
            $dataActual[] = floatval(round($actual->Actual,3));
        }

        $dataBFOS = [];
        $bfos = $this->getDataCapex("bfos",$shop);
        foreach ($bfos as $bfos) {
            $dataBFOS[] = floatval(round($bfos->Nominal_BFOS,3));
        }

        $dataBTOS = [];
        $btos = $this->getDataCapex("btos",$shop);
        foreach ($btos as $btos) {
            $dataBTOS[] = floatval(round($btos->Nominal_BTOS,3));
        }

        $fb = ["statusCode" => 200, "res" => ["plan" => $dataPlan, "actual" => $dataActual, "bfos" => $dataBFOS, "btos" => $dataBTOS]];
        $this->fb($fb);
    }

    private function getDataCapex($tipe,$shop)
    {
        if($tipe == "plan"){
            $columnValue = "Budget";
            $columnMonth = "Month_Plan";
        }else if($tipe == "actual"){
            $columnValue = "Actual";
            $columnMonth = "Month";
        }else if($tipe == "bfos"){
            $columnValue = "Nominal_BFOS";
            $columnMonth = "Month_BFOS";
        }else if($tipe == "btos"){
            $columnValue = "Nominal_BTOS";
            $columnMonth = "Month_BTOS";
        }

        $query = "
        WITH months AS (
            SELECT 1 AS $columnMonth UNION ALL
            SELECT 2 UNION ALL
            SELECT 3 UNION ALL
            SELECT 4 UNION ALL
            SELECT 5 UNION ALL
            SELECT 6 UNION ALL
            SELECT 7 UNION ALL
            SELECT 8 UNION ALL
            SELECT 9 UNION ALL
            SELECT 10 UNION ALL
            SELECT 11 UNION ALL
            SELECT 12
        )
        SELECT 
            m.$columnMonth, 
            COALESCE(SUM(d.$columnValue), 0) AS $columnValue
        FROM 
            months m
        LEFT JOIN `datacapex` d 
            ON m.$columnMonth = d.$columnMonth AND d.shop = '".$shop."'
        GROUP BY m.$columnMonth
        ORDER BY FIELD(m.$columnMonth, 4, 5, 6, 7, 8, 9, 10, 11, 12, 1, 2, 3);
        ";
        $data = $this->model->query_exec($query,"result");
        return $data;
    }

    function getDataTable()
    {
        $this->form_validation->set_rules('shop', 'Shop', 'trim|required');
        $this->form_validation->set_rules('tipe', 'Tipe', 'trim|required');
        if ($this->form_validation->run() === FALSE) {
            $fb = ["statusCode" => 500, "res" => validation_errors()];
            $this->fb($fb);
        }

        $shop = $this->input->post("shop");
        $tipe = $this->input->post("tipe");
        if($tipe == "plan"){
            $returnData = $this->model->gd("datacapex","Id,Category,Invest,Month_Plan as Month,Budget","shop = '$shop' AND Budget != ''","result");
        }else if($tipe == "actual"){
            $returnData = $this->model->gd("datacapex","Id,Category,Invest,Month_Plan as Month,Actual as Budget","shop = '$shop' AND Actual != '' AND BTOS = ''","result");
        }else if($tipe == "bfos"){
            $returnData = $this->model->gd("datacapex","Id,Category,Invest,BFOS as OtherShop,Month_BFOS as Month,Nominal_BFOS as Budget","shop = '$shop' AND BFOS != ''","result");
        }else if($tipe == "btos"){
            $returnData = $this->model->gd("datacapex","Id,Category,Invest,BTOS as OtherShop,Month_BTOS as Month,Nominal_BTOS as Budget","shop = '$shop' AND BTOS != ''","result");
        }
        $fb = ["statusCode" => 200, "res" => $returnData];
        $this->fb($fb);
    }

    private function checkInputCapex()
    {
        $tipe = $this->input->post("tipe");
        $this->form_validation
            ->set_rules('id',"ID","trim|required")
            ->set_rules('category',"Category","trim|required")
            ->set_rules('invest',"Invest","trim|required")
            ->set_rules('month',"Month","trim|required") 
            ->set_rules('budget',"Budget","trim|required")
            ->set_rules('tipe',"Tipe","trim|required")
            ->set_rules('shop',"Shop","trim|required");
        if($tipe == "bfos" || $tipe == "btos"){
            $this->form_validation->set_rules('otherShop',"Other Shop","trim");
        }
        
        if ($this->form_validation->run() === FALSE) {
            $fb = ["statusCode" => 500, "res" => validation_errors()];
            $this->fb($fb);
        }
    }

    function prosesCapex($method) //METHOD ADD OR UPDATE
    {
        $this->checkInputCapex();

        $id = $this->input->post("id");
        $category = $this->input->post("category");
        $invest = $this->input->post("invest");
        $month = $this->input->post("month");
        $otherShop = $this->input->post("otherShop");
        $budget = str_replace(",",".",$this->input->post("budget"));
        $shop = $this->input->post("shop");
        $tipe = $this->input->post("tipe");

        $input = [
            "Category" => $category,
            "Invest" => $invest,
            "shop" => $shop,
        ];
        if($tipe == "plan"){
            $input["Month_Plan"] = $month;
            $input["Budget"] = $budget;
        }else if($tipe == "actual"){
            $input["Month"] = $month;
            $input["Actual"] = $budget;
        }else if($tipe == "bfos"){
            $input["BFOS"] = $otherShop;
            $input["Month_BFOS"] = $month;
            $input["Nominal_BFOS"] = $budget;
        }else if($tipe == "btos"){
            $input["BTOS"] = $otherShop;
            $input["Month_BTOS"] = $month;
            $input["Nominal_BTOS"] = $budget;
        }

        if($method == "update"){
            $proses = $this->model->update("datacapex","Id = '$id'",$input);
        }else if($method == "add"){
            $proses = $this->model->insert("datacapex",$input);
        }
        if($proses){
            $fb = ["statusCode" => 200, "res" => "Data berhasil di update"];
        }else{
            $fb = ["statusCode" => 500, "res" => "Data gagal di update"];
        }
        $this->fb($fb);
    }

    function deleteCapex()
    {
        $this->form_validation->set_rules('id',"ID","trim|required");
        if ($this->form_validation->run() === FALSE) {
            $fb = ["statusCode" => 500, "res" => validation_errors()];
            $this->fb($fb);
        }

        $id = $this->input->post("id");
        $delete = $this->model->delete("datacapex","id = '$id'");
        if($delete){
            $fb = ["statusCode" => 200, "res" => "Data berhasil di hapus"];
        }else{
            $fb = ["statusCode" => 500, "res" => "Data gagal di hapus"];
        }
        $this->fb($fb);
    }

    function getDataCapexAdd()
    {
        $shop = $this->input->post("shop");
        $month_3 = $this->input->post("month_3");
        $month = $month_3;
        if($month_3 == "01") {
            $month = 13;
        }else if($month_3 == "02") {
            $month = 14;
        }else if($month_3 == "03") {
            $month = 15;
        }
        $dataInvest = ["Improvement","Replacement"];

        $newDataCallBack["Improvement"] = [];
        $newDataCallBack["Replacement"] = [];
        foreach ($dataInvest as $key => $value) {
            $getData = $this->model->gd("datacapex","*","shop = '$shop' AND Budget != '' AND Invest = '$value' AND Month_Plan <= $month","result");
            foreach ($getData as $row) {
                $actual = $this->model->gd("datacapex","SUM(Actual) as total","shop = '$shop' AND (activity_BTOS = '".$row->Id."' OR activity_BFOS = '".$row->Id."')","row");
                $bfosSelf = $this->model->gd("datacapex","SUM(Nominal_BFOS) as total","BFOS = '$shop' AND activity_BFOS = '".$row->Id."'","row");
                $ActualBudget = !empty($row->Actual) ? $row->Actual : 0;
                $ActualBFOSorBTOS = $actual->total + $bfosSelf->total;
                $mPlan = $row->Month_Plan+3;
                $date = "2020-".$mPlan."-01";
                $budget = $row->Budget - $ActualBudget - $ActualBFOSorBTOS;
                $prevUsage = $ActualBudget + $ActualBFOSorBTOS;
                $MonthPlan = date("M",strtotime($date));
                $full = $budget <= 0 ? "full" : "";
                $class = $budget <= 0 ? "bg-secondary text-light" : "";
                $newDataCallBack[$value][] = [
                    "id" => $row->Id,
                    "category" => $row->Category,
                    "planBudget" => $row->Budget,
                    "prevUsage" => round($prevUsage,3),
                    "budget" => round($budget,3),
                    "monthPlan" => $MonthPlan,
                    "full" => $full,
                    "class" => $class
                ];
            }
        }
        $fb = ["statusCode" => 200, "res" => $newDataCallBack];
        $this->fb($fb);
    }

    function saveDataActivity()
    {
        // Ambil raw JSON dari body request
        $jsonData = file_get_contents("php://input");

        // Ubah JSON menjadi array asosiatif
        $data = json_decode($jsonData, true);

        // Cek apakah JSON valid
        if (!is_array($data)) {
            $fb = ["statusCode" => 500, "res" => "Invalid request JSON"];
            $this->fb($fb);
        }

        $monthActual = (date("m")*1);
        $ia = $data["ia"] ?? null;
        if(empty($ia)){
            $fb = ["statusCode" => 500, "res" => "IA tidak boleh kosong"];
            $this->fb($fb);
        }
        $shop = $data["shop"] ?? null;
        $investment = $data["investment"] ?? null;
        $description = $data["description"] ?? null;
        $useBudget = isset($data["useBudget"]) ? json_decode($data["useBudget"], true) : [];
        $useBudgetOther = isset($data["useBudgetOther"]) ? json_decode($data["useBudgetOther"], true) : [];

        $getIANumber = $this->model->gd("datacapex","Id","No_IA = '$ia' AND shop = '$shop'","row");
        $getCategory = $this->model->gd("datacapex","Id,Category,shop","Category = '$description' AND shop = '$shop'","row");
        if(!empty($useBudget) && is_array($useBudget)){
            foreach ($useBudget as $key => $value) {
                if($getCategory->Id == $key){
                    //UPDATE ACTUAL
                    $submitActual = [
                        "Actual" => $value,
                        "Month" => $monthActual,
                        "No_IA" => $ia,
                    ];
                    $proses = $this->model->update("datacapex","id = '$key'",$submitActual);
                    if(!$proses){
                        $fb = ["statusCode" => 500, "res" => "Gagal update data actual"];
                        $this->fb($fb);
                    }
                }else{
                    //UPDATE BFOS
                    $datasubmit = [
                        "Category" => $description,
                        "Invest" => $investment,
                        "BFOS" => $shop,
                        "activity_BFOS" => $key,
                        "Nominal_BFOS" => $value,
                        "Month_BFOS" => $monthActual,
                        "No_IA" => $ia,
                        "shop" => $shop
                    ];
                    if(!empty($getIANumber->Id)){
                        //GET DATA ID BY ACTIVITY BFOS
                        $idBFOS = $this->model->gd("datacapex","Id","activity_BFOS = '$key' AND shop = '$shop' AND No_IA = '$ia'","row");
                        $proses = $this->model->update("datacapex","Id = '$idBFOS->Id'",$datasubmit);
                    }else{
                        $proses = $this->model->insert("datacapex",$datasubmit);
                    }
                    if(!$proses){
                        $fb = ["statusCode" => 500, "res" => "Gagal input data BFOS"];
                        $this->fb($fb);
                    }
                }
            }
        }

        if(!empty($useBudgetOther) && is_array($useBudgetOther)){
            foreach ($useBudgetOther as $key => $value) {
                $getDetail = $this->model->gd("datacapex","id,shop","id = '$key'","row");
                //INPUT BTOS
                $dataSubmitBTOS = [
                    "Category" => $description,
                    "Invest" => $investment,
                    "BTOS" => $shop,
                    "Actual" => $value,
                    "Nominal_BTOS" => $value,
                    "activity_BTOS" => $key,
                    "Month_BTOS" => $monthActual,
                    "No_IA" => $ia,
                    "shop" => $getDetail->shop,
                ];
                
                //INPUT BFOS
                $dataSubmitBFOS = [
                    "Category" => $description,
                    "Invest" => $investment,
                    "BFOS" => $getDetail->shop,
                    "Nominal_BFOS" => $value,
                    "activity_BFOS" => $key,
                    "Month_BFOS" => $monthActual,
                    "No_IA" => $ia,
                    "shop" => $shop,
                ];
                
                if(!empty($getIANumber->Id)){
                    //GET DATA ID BY ACTIVITY BTOS
                    $idBTOS = $this->model->gd("datacapex","Id","activity_BTOS = '$key' AND shop = '$getDetail->shop' AND No_IA = '$ia'","row");
                    $proses = $this->model->update("datacapex","Id = '$idBTOS->Id'",$dataSubmitBTOS);
                    //GET DATA ID BY ACTIVITY BFOS
                    $idBFOS = $this->model->gd("datacapex","Id","activity_BFOS = '$key' AND shop = '$shop' AND No_IA = '$ia'","row");
                    $proses = $this->model->update("datacapex","Id = '$idBFOS->Id'",$dataSubmitBFOS);
                }else{
                    $proses = $this->model->insert("datacapex",$dataSubmitBFOS);
                    $proses = $this->model->insert("datacapex",$dataSubmitBTOS);
                }
                if(!$proses){
                    $fb = ["statusCode" => 500, "res" => "Gagal input data BTOS"];
                    $this->fb($fb);
                }
            }
        }

        $fb = ["statusCode" => 200, "res" => "Data berhasil diupdate"];
        $this->fb($fb);
    }

    function getShop($return = false)
    {
        $shop = $this->model->gd("shop","shop,plant","id !=","result");
        $fb = ["statusCode" => 200, "res" => $shop];
        if($return){
            return $fb; 
        }else{
            $this->fb($fb); 
        }
    }

    private function formatingNumber($number)
    {
        $format = str_replace(",00","",number_format(round($number,3),3,",","."));
        return $format;
    }

    function getDataReporting()
    {
        $getShop = $this->getShop(true);
        $dataShop = $getShop["res"];
        $invest = ["Improvement","Replacement"];
        $dataResult = [];
        $dataResultSummary = [];
        $summaryTotal = [];

        foreach ($invest as $kInvest => $vInvest) {
            $grandTotalPlan = 0;
            $grandTotalUsage = 0;
            $grandTotalRemain = 0;
            for ($i=1; $i <= 12; $i++) { 
                $grandTotalMonth = 0;
                $totalMonth = $this->model->gd(
                    "datacapex",
                    "SUM(COALESCE(Actual, 0) + COALESCE(Nominal_BFOS, 0)) as total",
                    "Invest = '$vInvest' AND (Month = '$i' OR Month_BFOS = '$i')",
                    "row"
                );
                $totalPerMonth = isset($totalMonth->total) ? $totalMonth->total : 0;
                $grandTotalMonth += $totalPerMonth;
                $dataResultSummary[$vInvest]["gtm"][$i] = $this->formatingNumber($grandTotalMonth);
                $summaryTotal[$vInvest]["gtm"][$i] = $grandTotalMonth;
            }

            foreach ($dataShop as $key => $value) {
                $shop = $value->shop;
                //CARI UNTUK ROWSPAN TABLE SHOP TITLE
                $countData = $this->model->gd("datacapex","COUNT(id) as rowData","shop = '$shop' AND Invest = '$vInvest' AND (Actual != '' OR BFOS != '')","row");
                $rowSpan = ($countData->rowData + 1); //+3 UNTUK COVER ROW YANG LAIN
                $totalPlan = $this->model->gd("datacapex","SUM(Budget) as total","shop = '$shop' AND Invest = '$vInvest'","row");
                $totalUsage = $this->model->gd(
                    "datacapex",
                    "SUM(COALESCE(Actual, 0) + COALESCE(Nominal_BFOS, 0)) as total",
                    "shop = '$shop' AND Invest = '$vInvest' AND (Month != '' OR Month_BFOS != '')",
                    "row"
                );
                $totalUsage = $totalUsage->total;
    
                $dataSummaryMonth = [];
                for ($i=1; $i <= 12; $i++) { 
                    $totalMonth = $this->model->gd(
                        "datacapex",
                        "SUM(COALESCE(Actual, 0) + COALESCE(Nominal_BFOS, 0)) as total",
                        "shop = '$shop' AND Invest = '$vInvest' AND (Month = '$i' OR Month_BFOS = '$i')",
                        "row"
                    );
                    $totalPerMonth = isset($totalMonth->total) ? $totalMonth->total : 0;
                    $dataSummaryMonth[$i] = $this->formatingNumber($totalPerMonth);
                }
    
                $remainBudget = $totalPlan->total - $totalUsage;
                $dataResult[$shop][$vInvest] = [
                    "rowSpan" => ($rowSpan+1),
                    "totalPlan" => $this->formatingNumber($totalPlan->total),
                    "totalUsage" => $this->formatingNumber($totalUsage),
                    "totalRemainBudget" => $this->formatingNumber($remainBudget),
                    "data" => [],
                    "dataSummaryMonth" => $dataSummaryMonth
                ];

                $grandTotalPlan += $totalPlan->total;
                $dataResultSummary[$vInvest]["gtp"] = $this->formatingNumber($grandTotalPlan);
                $summaryTotal[$vInvest]["gtp"] = $grandTotalPlan;

                $grandTotalUsage += $totalUsage;
                $dataResultSummary[$vInvest]["gtu"] = $this->formatingNumber($grandTotalUsage);
                $summaryTotal[$vInvest]["gtu"] = $grandTotalUsage;

                $grandTotalRemain += $remainBudget;
                $dataResultSummary[$vInvest]["gtr"] = $this->formatingNumber($grandTotalRemain);
                $summaryTotal[$vInvest]["gtr"] = $grandTotalRemain;
    
                $data = $this->model->gd("datacapex","*","shop = '$shop' AND Invest = '$vInvest' AND (Actual != '' OR BFOS != '')","result");
                if(empty($data)){
                    continue;
                }
    
                $dataUsage = []; //MENGENOLKAN NILAI DATA USAGE
                foreach ($data as $row) {
                    $type = "";
                    $usage = "";
                    $month = "";
                    $keterangan = "";
                    if(!empty($row->Actual) && !empty(!empty($row->Month))){
                        $type = "Actual";
                        $usage = $row->Actual;
                        $month = $row->Month;
                    }else if(!empty($row->BFOS)){
                        $type = "BFOS";
                        $usage = $row->Nominal_BFOS;
                        $month = $row->Month_BFOS;
                        $getDataBFOS = $this->model->gd("datacapex","Category,Invest","Id = '$row->activity_BFOS'","row");
                        $keterangan = "Budget From : ".$row->BFOS." (".$getDataBFOS->Invest." : ".$getDataBFOS->Category.")";
                    }else if(!empty($row->BTOS)){
                        $type = "BTOS";
                        $usage = $row->Nominal_BTOS;
                        $month = $row->Month_BTOS;
                        $getDataBTOS = $this->model->gd("datacapex","Category,Invest","Id = '$row->activity_BTOS'","row");
                        $keterangan = "Budget For : ".$row->BTOS." (".$getDataBFOS->Invest." : ".$getDataBTOS->Category.")";
                    }
                    $dataBFOS = $this->model->gd("datacapex","SUM(Nominal_BFOS) as total","activity_BFOS = '$row->Id'","row");
                    $remainBudget = (empty($row->Budget) ? 0 : $row->Budget) - ((empty($row->Actual) ? 0 : $row->Actual) + (empty($dataBFOS->total) ? 0 : $dataBFOS->total));
                    $dataUsage[] = [
                        "type" => $type,
                        "category" => $row->Category,
                        "plan" => $row->Budget,
                        "remainBudget" => $remainBudget > 0 ? $this->formatingNumber($remainBudget) : "",
                        "month" => intval($month),
                        "usage" => $this->formatingNumber($usage),
                        "ia" => $row->No_IA,
                        "keterangan" => $keterangan
                    ];
    
                    $dataResult[$shop][$vInvest]["data"] = $dataUsage;
                }
            }
        }
        
        for ($i=1; $i <= 12; $i++) { 
            $totalgtm = $summaryTotal["Improvement"]["gtm"][$i] + $summaryTotal["Replacement"]["gtm"][$i];
            $dataResultSummary["total"]["gtm"][$i] = $this->formatingNumber($totalgtm,2);
        }
        $dataResultSummary["total"]["gtp"] = $this->formatingNumber($summaryTotal["Improvement"]["gtp"] + $summaryTotal["Replacement"]["gtp"],2);
        $dataResultSummary["total"]["gtu"] = $this->formatingNumber($summaryTotal["Improvement"]["gtu"] + $summaryTotal["Replacement"]["gtu"],2);
        $dataResultSummary["total"]["gtr"] = $this->formatingNumber($summaryTotal["Improvement"]["gtr"] + $summaryTotal["Replacement"]["gtr"],2);

        $fb = ["statusCode" => 200, "res" => $dataResult, "summary" => $dataResultSummary];
        $this->fb($fb);
    }

    function getSummaryReport()
    {
        $getShop = $this->getShop(true);
        $dataShop = $getShop["res"];
        $invest = ["Improvement","Replacement"];
        $result = [];
        $resultNum = [];
        $resultTotal = [];
        
        $totalPlanImprovement = 0;
        $totalPlanReplacement = 0;
        $totalBFOSImprovement = 0;
        $totalBFOSReplacement = 0;
        $totalBTOSImprovement = 0;
        $totalBTOSReplacement = 0;
        $totalActualImprovement = 0;
        $totalActualReplacement = 0;
        $totalActualxPlanImprovement = 0;
        $totalActualxPlanReplacement = 0;
        $totalRemainImprovement = 0;
        $totalRemainReplacement = 0;
        $grandTotalRemain = 0;
        foreach ($dataShop as $key => $value) {
            $shop = $value->shop;
            foreach ($invest as $kinvest => $vinvest) {
                $plan = $this->model->gd("datacapex","SUM(Budget) as total","shop = '$shop' AND Invest = '$vinvest'","row");
                $bfos = $this->model->gd("datacapex","SUM(Nominal_BFOS) as total","shop = '$shop' AND Invest = '$vinvest' AND BFOS != '$shop'","row");
                $btos = $this->model->gd("datacapex","SUM(Nominal_BTOS) as total","shop = '$shop' AND Invest = '$vinvest'","row");
                $actual = $this->model->gd("datacapex","SUM(Actual + Nominal_BFOS - Nominal_BTOS) as total","shop = '$shop' AND Invest = '$vinvest'","row");
                $actualxplan = $actual->total - $plan->total;
                $remain = round(($plan->total + $bfos->total) - ($actual->total + $btos->total),3);
                $result[$shop][$vinvest] = [
                    "plan" => $this->formatingNumber($plan->total),
                    "bfos" => $this->formatingNumber($bfos->total),
                    "btos" => $this->formatingNumber($btos->total),
                    "actual" => $this->formatingNumber($actual->total),
                    "actualxplan" => $this->formatingNumber($actualxplan),
                    "remain" => $this->formatingNumber($remain),
                ];
                
                $resultNum[$shop][$vinvest] = [
                    "plan" => $plan->total,
                    "bfos" => $bfos->total,
                    "btos" => $btos->total,
                    "actual" => $actual->total,
                    "actualxplan" => $actualxplan,
                    "remain" => $remain
                ];

                if($vinvest == "Improvement"){
                    $totalPlanImprovement += $plan->total;
                    $totalBFOSImprovement += $bfos->total;
                    $totalBTOSImprovement += $btos->total;
                    $totalActualImprovement += $actual->total;
                    $totalActualxPlanImprovement += $actualxplan;
                    $totalRemainImprovement += $resultNum[$shop]["Improvement"]["remain"];
                }else if($vinvest == "Replacement"){
                    $totalPlanReplacement += $plan->total;
                    $totalBFOSReplacement += $bfos->total;
                    $totalBTOSReplacement += $btos->total;
                    $totalActualReplacement += $actual->total;
                    $totalActualxPlanReplacement += $actualxplan;
                    $totalRemainReplacement += $resultNum[$shop]["Replacement"]["remain"];
                }
            }
            $result[$shop]["remain_total"] = $this->formatingNumber($resultNum[$shop]["Improvement"]["remain"] + $resultNum[$shop]["Replacement"]["remain"]);
            $grandTotalRemain += $resultNum[$shop]["Improvement"]["remain"] + $resultNum[$shop]["Replacement"]["remain"];
        }
        $result["GRAND TOTAL"]["Improvement"]["plan"] = $this->formatingNumber($totalPlanImprovement);
        $result["GRAND TOTAL"]["Replacement"]["plan"] = $this->formatingNumber($totalPlanReplacement);
        $result["GRAND TOTAL"]["Improvement"]["bfos"] = $this->formatingNumber($totalBFOSImprovement);
        $result["GRAND TOTAL"]["Replacement"]["bfos"] = $this->formatingNumber($totalBFOSReplacement);
        $result["GRAND TOTAL"]["Improvement"]["btos"] = $this->formatingNumber($totalBTOSImprovement);
        $result["GRAND TOTAL"]["Replacement"]["btos"] = $this->formatingNumber($totalBTOSReplacement);
        $result["GRAND TOTAL"]["Improvement"]["actual"] = $this->formatingNumber($totalActualImprovement);
        $result["GRAND TOTAL"]["Replacement"]["actual"] = $this->formatingNumber($totalActualReplacement);
        $result["GRAND TOTAL"]["Improvement"]["actualxplan"] = $this->formatingNumber($totalActualxPlanImprovement);
        $result["GRAND TOTAL"]["Replacement"]["actualxplan"] = $this->formatingNumber($totalActualxPlanReplacement);
        $result["GRAND TOTAL"]["Improvement"]["remain"] = $this->formatingNumber($totalRemainImprovement);
        $result["GRAND TOTAL"]["Replacement"]["remain"] = $this->formatingNumber($totalRemainReplacement);
        $result["GRAND TOTAL"]["remain_total"] = $this->formatingNumber($grandTotalRemain);

        $fb = ["statusCode" => 200, "res" => $result, "resTotal" => $resultTotal];
        $this->fb($fb);
    }

    function uploadData()
    {
        $shop = $this->input->post("shop");
        // Konfigurasi upload file
        $config['upload_path']   = './uploads/';
        $config['allowed_types'] = 'xls|xlsx';

        $this->upload->initialize($config);
        if (!$this->upload->do_upload('upload-file')) {
            // Jika upload gagal, tampilkan error
            $error = $this->upload->display_errors();
            $this->fb(["statusCode" => 500, "res" => $error]);
        }
        
        // Jika upload berhasil
        $file_data = $this->upload->data();
        $file_path = $file_data['full_path'];
        // Load PHPExcel
        $objPHPExcel = IOFactory::load($file_path);

        $clear_data = $this->model->delete("datacapex", "shop = '$shop'");
        // Membaca sheet pertama
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        $data = [];
        for ($i=10; $i <= 29; $i++) { 
            $category = $sheet->getCell('D'.$i)->getValue();
            if(empty($category)){
                continue;
            }

            $budget = $sheet->getCell('G'.$i)->getCalculatedValue();
            if(!empty($sheet->getCell('H'.$i)->getValue())){
                $monthPlan = 4;
            }else if(!empty($sheet->getCell('I'.$i)->getValue())){
                $monthPlan = 5;
            }else if(!empty($sheet->getCell('J'.$i)->getValue())){
                $monthPlan = 6;
            }else if(!empty($sheet->getCell('K'.$i)->getValue())){
                $monthPlan = 7;
            }else if(!empty($sheet->getCell('L'.$i)->getValue())){
                $monthPlan = 8;
            }else if(!empty($sheet->getCell('M'.$i)->getValue())){
                $monthPlan = 9;
            }else if(!empty($sheet->getCell('N'.$i)->getValue())){
                $monthPlan = 10;
            }else if(!empty($sheet->getCell('O'.$i)->getValue())){
                $monthPlan = 11;
            }else if(!empty($sheet->getCell('P'.$i)->getValue())){
                $monthPlan = 12;
            }else if(!empty($sheet->getCell('Q'.$i)->getValue())){
                $monthPlan = 1;
            }else if(!empty($sheet->getCell('R'.$i)->getValue())){
                $monthPlan = 2;
            }else if(!empty($sheet->getCell('S'.$i)->getValue())){
                $monthPlan = 3;
            }

            $data[] = [
                "Category" => $category,
                "Budget" => $budget,
                "Month_Plan" => $monthPlan,
                "Invest" => "Improvement",
                "shop" => $shop
            ];
        }

        for ($i=34; $i <= 40; $i++) { 
            $category = $sheet->getCell('D'.$i)->getValue();
            if(empty($category)){
                continue;
            }

            $budget = $sheet->getCell('G'.$i)->getCalculatedValue();
            if(!empty($sheet->getCell('H'.$i)->getValue())){
                $monthPlan = 4;
            }else if(!empty($sheet->getCell('I'.$i)->getValue())){
                $monthPlan = 5;
            }else if(!empty($sheet->getCell('J'.$i)->getValue())){
                $monthPlan = 6;
            }else if(!empty($sheet->getCell('K'.$i)->getValue())){
                $monthPlan = 7;
            }else if(!empty($sheet->getCell('L'.$i)->getValue())){
                $monthPlan = 8;
            }else if(!empty($sheet->getCell('M'.$i)->getValue())){
                $monthPlan = 9;
            }else if(!empty($sheet->getCell('N'.$i)->getValue())){
                $monthPlan = 10;
            }else if(!empty($sheet->getCell('O'.$i)->getValue())){
                $monthPlan = 11;
            }else if(!empty($sheet->getCell('P'.$i)->getValue())){
                $monthPlan = 12;
            }else if(!empty($sheet->getCell('Q'.$i)->getValue())){
                $monthPlan = 1;
            }else if(!empty($sheet->getCell('R'.$i)->getValue())){
                $monthPlan = 2;
            }else if(!empty($sheet->getCell('S'.$i)->getValue())){
                $monthPlan = 3;
            }

            $data[] = [
                "Category" => $category,
                "Budget" => $budget,
                "Month_Plan" => $monthPlan,
                "Invest" => "Replacement",
                "shop" => $shop
            ];
        }
        
        $insert = $this->model->insert_batch("datacapex",$data);
        if($insert){
            $fb = ["statusCode" => 200, "res" => "Upload success"];
        }else{
            $fb = ["statusCode" => 500, "res" => "Upload failed"];
        }
        unlink($file_path);
        $this->fb($fb);
    }
}
