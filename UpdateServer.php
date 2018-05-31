<?php  
include 'DatabaseConn.php';
$Date=date("d-m-Y");
$UserName=$_GET['UserName'];
$fileData=array();
$numArray=array();

if(is_connected()){//if Network availble or not

	//table updated in locally
	$Queery="insert into user_details values('','$UserName')";
	mysql_query($Queery,$conn);

	//check file already exits or not
	if(file_exists("$Date.txt")){
		$i=0;
		//retrive file data to array
		foreach (file("$Date.txt") as $line){
		$fileData[$i]=$line;
		$i++;
		}
		//remove duplicate value
		$fileDataUnique=array_unique($fileData);
		$i=0;

		//it contain some null values so it will be remove ot
		foreach($fileDataUnique as $key=>$value)
		{
		if(is_null($value) || $value == '')
		unset($fileDataUnique[$key]);
		}

		//arrange index normally
		$reIndexArray=array_values($fileDataUnique);
		$size=count($reIndexArray);

		while($i<$size){
			$numArray[]=$i;
			$i++;
		}
		//combine data in transmit type
		$urlArray=array_combine($numArray, $reIndexArray);
		//$url="http://localhost/RealTimeDatabase/Rest.php";
		$url="https://kisnami.000webhostapp.com/Rest.php";
		//convert http-UTF format

		var_dump($urlArray);
		$value=http_build_query($urlArray);

		//sending packets
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $value);
		curl_exec($ch);
		if (curl_errno ( $ch )) {
		echo curl_error ( $ch );
		curl_close ( $ch );
		exit ();
		}

		curl_close($ch);

		//update orginal data to database
		while($i<$size){
		$Queery="insert into user_details values('','$reIndexArray[$i]')";
		//echo "$Queery<br>";
		mysql_query($Queery,$conn);
		$i++;
		}	
		//remove file
		unlink("$Date.txt");
		
	}

}
else{

	if (!file_exists("$Date.txt")) {
		$TodayFile=fopen("$Date.txt", "a+");	
	}
		$TodayFile=fopen("$Date.txt", "a+");	
		fwrite($TodayFile, $UserName."\r\n");
		fclose($TodayFile);
				
}
//To check the network Status
function is_connected()
{
    $connected = @fsockopen("www.google.com", 80);  //website, port  (try 80 or 443)
    if ($connected){
        $is_conn = true; //action when connected
        fclose($connected);
    }else{
        $is_conn = false; //action in connection failure
    }
    return $is_conn;

}

?>