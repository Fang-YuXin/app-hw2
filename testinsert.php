<?php
header('Content-Type: application/json; charset=UTF-8'); //設定資料類型為 json，編碼 utf-8

	//--------帳號----------//
	if(@$_POST["vid"] != "")
	{
		$vid=$_POST["vid"];		
	}
	else
	{
		echo "請輸入資料";
	}
	//--------內容----------//
	if(@$_POST["vmon"] != "")
	{
		$vmon=$_POST["vmon"];	
		$vyear = $_POST["vyear"];
		//echo $content;
	}
	else
	{
		$vmon=="";
		$vyear="";
	}
	
	//------------讀取資料庫--------------//	

	$servername = "192.168.2.200";
	$username = "fangbib1_123";
	$password = "123";
	$dbname = "fangbib1_testinvoice";
	$conn = new mysqli($servername, $username, $password, $dbname);
	

	//------------------------------------//
	// 建立資料庫連線
	$conn = new mysqli($servername, $username, $password, $dbname);
	// 確認資料是否正常連線
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}


//<--------------新增資料Start------------>
	$sqlCheckExist = "SELECT * FROM invoice WHERE vid = '$vid' ";
	$resultCheckExist = $conn->query($sqlCheckExist);

	if ($resultCheckExist->num_rows > 0) 
	{
	    echo "已新增過\n";
	} 
	else 
	{
	    // 新增資料到資料庫
	    $sqlInsert = "INSERT INTO `invoice`  VALUES ('".$vid."', '".$vmon."', ".$vyear.", 'not yet award');";
	    if ($conn->query($sqlInsert) === TRUE) {
	        echo "資料已新增\n";
	    } else {
	        echo "新增資料時發生錯誤：" . $conn->error;
	    }
	}
//<--------------新增資料End------------>	


	if ($vid !== "") 
	{
    	$sqlselect = "SELECT c.cid,c.cprice,c.cdate FROM invoice AS i JOIN checkinvoice AS c ON i.vmon = c.cmon AND i.vyear = c.cyear WHERE i.vmon = " . $vmon . " AND i.vyear = " . $vyear . ";";
		$result = $conn->query($sqlselect);
		//echo $sqlselect;
	}

	$messageArr = array();
	$dataarray = array();

	if ($result->num_rows > 0) 
	{
	    // output data of each row
	    while ($row = $result->fetch_assoc()) 
	    {
	        $cid = $row["cid"];
	        $cprice = $row["cprice"];
	        $cdate = $row["cdate"];
	        $dataarray[] = $row;
	    }
	}


//------------------------------------------------------
//echo json_encode($messageArr["data"]);


//<----------------------------------判斷多少錢--------------------------->

if (empty($dataarray)) {
    echo "未開獎";

} elseif (strtotime($cdate) < strtotime('today')) {
    echo "已過期";
    $sqlupdate = "UPDATE `invoice` SET `statue` = 'expired' WHERE `invoice`.`vid` = '".$vid."';";
    $resultupdate = $conn->query($sqlupdate);
} else {
	$winning = false; // 儲存是否中獎的變數
	foreach ($dataarray as $item) {
    $cid = $item['cid'];
    $cprice = $item['cprice'];
    //echo $cid;
    //echo $cprice;
    //echo json_encode($item);

    if ($cprice === 'first award') {
        if ($vid === $cid) {
            echo '恭喜您中了特別獎！獎金為 1,000萬元。';
            $sqlupdate = "UPDATE `invoice` SET `statue` = '1000w' WHERE `invoice`.`vid` = '".$vid."';";
            $resultupdate = $conn->query($sqlupdate);
            $winning = true;
            break;
        }
    } elseif ($cprice === 'second award') {
        if ($vid === $cid) {
            echo '恭喜您中了特獎！獎金為 200萬元。';
            $sqlupdate = "UPDATE `invoice` SET `statue` = '200w' WHERE `invoice`.`vid` = '".$vid."';";
            $resultupdate = $conn->query($sqlupdate);
            $winning = true;
            break;
        }
    } elseif ($cprice === 'third award') {
        $matchingDigits = 8; // 比對的位數
        if (substr($vid, -$matchingDigits) === substr($cid, -$matchingDigits)) {
            echo '恭喜您中了頭獎！獎金為 20萬元。';
            $sqlupdate = "UPDATE `invoice` SET `statue` = '20w' WHERE `invoice`.`vid` = '".$vid."';";
            $resultupdate = $conn->query($sqlupdate);
            $winning = true;
            break;
        }

        $matchingDigits = 7; // 比對的位數
        if (substr($vid, -$matchingDigits) === substr($cid, -$matchingDigits)) {
            echo '恭喜您中了二獎！獎金為 4萬元。';
            $sqlupdate = "UPDATE `invoice` SET `statue` = '40w' WHERE `invoice`.`vid` = '".$vid."';";
            $resultupdate = $conn->query($sqlupdate);
            $winning = true;
            break;
        }

        $matchingDigits = 6; // 比對的位數
        if (substr($vid, -$matchingDigits) === substr($cid, -$matchingDigits)) {
            echo '恭喜您中了三獎！獎金為 1萬元。';
            $sqlupdate = "UPDATE `invoice` SET `statue` = '1w' WHERE `invoice`.`vid` = '".$vid."';";
            $resultupdate = $conn->query($sqlupdate);
            $winning = true;
            break;
        }

        $matchingDigits = 5; // 比對的位數
        if (substr($vid, -$matchingDigits) === substr($cid, -$matchingDigits)) {
            echo '恭喜您中了四獎！獎金為 4千元。';
            $sqlupdate = "UPDATE `invoice` SET `statue` = '4000' WHERE `invoice`.`vid` = '".$vid."';";
            $resultupdate = $conn->query($sqlupdate);
            $winning = true;
            break;
        }

        $matchingDigits = 4; // 比對的位數
        if (substr($vid, -$matchingDigits) === substr($cid, -$matchingDigits)) {
            echo '恭喜您中了五獎！獎金為 1千元。';
            $sqlupdate = "UPDATE `invoice` SET `statue` = '1000' WHERE `invoice`.`vid` = '".$vid."';";
            $resultupdate = $conn->query($sqlupdate);
            $winning = true;
            break;
        }

        $matchingDigits = 3; // 比對的位數
        if (substr($vid, -$matchingDigits) === substr($cid, -$matchingDigits)) {
            echo '恭喜您中了六獎！獎金為 2百元。';
            $sqlupdate = "UPDATE `invoice` SET `statue` = '200' WHERE `invoice`.`vid` = '".$vid."';";
            $resultupdate = $conn->query($sqlupdate);
            $winning = true;
            break;
        }
    }

	}
	 if (!$winning) {
        $sqlupdate = "UPDATE `invoice` SET `statue` = 'no award' WHERE `invoice`.`vid` = '".$vid."';";
        $resultupdate = $conn->query($sqlupdate);
        echo "未中獎";
    }
}


$conn->close();
//<-----------------------------End---------------------------->




	//送入時間格式
    $messageArr["status"] = array();
	date_default_timezone_set('America/La_Paz');
	$today = date('Y-m-d\TH:i:sP');//RFC3339格式
	$datetime= array(
	"code" => "0",
	"message" => "Success Insert Message",
	"datetime" => $today
	);	
	$messageArr["status"] = $datetime;	


	if(!empty($_POST['vid']))
	{
		http_response_code(200);
	    //echo json_encode($messageArr);	
	}
	else
	{		
		http_response_code(404);	
		$messageArr["data"] =[];//因為沒有帳號，我們就預設讓它為空陣列
		$messageArr["status"] = array();
		date_default_timezone_set('America/La_Paz');
		$today = date('Y-m-d\TH:i:sP');//RFC3339 format
		$datetime= array(
		"code" => "404",
		"message" => "Error Account is null",
		"datetime" => $today
		);	
		$messageArr["status"] = $datetime;
		echo json_encode($messageArr);	
	}

?>