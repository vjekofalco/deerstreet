<?php

/*
| Last time modified by: Vjeko Babic
| Date: 28.10.2015.
*/

class HomeController extends BaseController {

	/*
	|
	|-------------------------------------------------------------------------------------
	|	1. Function "getExcel" is reading all xls/xlsx file from "public/excelPLace"     |
	|	   and making an array from file names and their extensions.                     |
	|-------------------------------------------------------------------------------------
	|
	|	*********** There are no routes or APPI defined for this function. **************
	|	
	|-------------------------------------------------------------------------------------
	|	2. Function "testing" is reading requested xls/xlsx file and returning generated |
	|	   JSON to the client.                                                           |
	|-------------------------------------------------------------------------------------	
	|
	|	Route::get('/{file}',['uses' => 'HomeController@testing']);
	|	Route::post('/{file}', ['uses' => 'HomeController@testing']);
	|	API: http://localhost:8888/deerstreet-dashboard-server/public/<file-name>
	|	
	*/

	/* ------------------------------------------- CODE STARTS HERE ----------------------------------------------------- */

	public function getExcel(){
		
		$projects = [];

		$files = File::allFiles( base_path() . '/excelPlace/');
		//$files = file('http://localhost:8888/excelPlace/');

		//return $files;

		foreach ($files as $file)
		{

    		if(pathinfo($file)['extension'] == "xlsx" || pathinfo($file)['extension'] == "xls"){

    			$projects[pathinfo($file)['filename']] = pathinfo($file)['basename'];

    		}

		}

		return $projects;
	}

	public function firstLoad(){

		$getFiles = $this->getExcel();

		return Redirect::route('company', array_rand($getFiles));

	}

	public function testing($file){

		$getFiles = $this->getExcel();

		$fileName = $getFiles[$file];

		if($fileName == false){

			$error = ["error_Message" => "You don't have any projects in folder!"];
			return Response::json($error);

		}
		else{

		$testingExcel = Excel::excel2Array( base_path() . '/excelPlace/' . $fileName );


			$months = ["January",  "February",  "March",  "April",  "May",  "June",  "July",  "August",  "September",  "October",  "November", "December"]; // The Month array, used to check if the current page is month page. Little bit hard cooded but what can you do :D

			$currentMonth = ""; // Current month flag. According to its value we are parsing the data to the correct array key

			$chartData = ["ContractData" => [], "calendar" => [], "projects"=>$getFiles]; // Initial Array structure.

			$role = "";

			for($i=0; $i<count($testingExcel); $i++){

				for($j=0; $j<count($testingExcel[$i]); $j++){
					
					$currentMonth = $testingExcel[$i][$j][0][0];
					
					/*
					|------------------------------------------------------------------------------------
					|	****************	PREPARING DATA FOR THE LINE CHART !!!	******************	|
					|------------------------------------------------------------------------------------
					*/

					if(in_array($currentMonth, $months)){ // Checking if the Excel file page is the Month page 

						$chartData["calendar"][$currentMonth]["roles"]=[]; 
						$chartData["calendar"][$currentMonth]["Days_in_month"]=[];

						
						for($k=0; $k<count($testingExcel[$i]); $k++){

							if(isset($testingExcel[$i][$k+2][0][0])){ // Setting up the array structure

								$role = $testingExcel[$i][$k+2][0][0];

								//$chartData["calendar"][$currentMonth]["roles"][$role]["Hourly_Rate"]="";
								$chartData["calendar"][$currentMonth]["roles"][$role]["Log"]=[];
								$chartData["calendar"][$currentMonth]["roles"][$role]["Used_HRS_This_Month"]=0;

							}

							if(isset($testingExcel[$i][$k+2][0][1]) && isset($role)){ // Inserting the "Hourly Rate" value in array

								//$chartData["calendar"][$currentMonth]["roles"][$role]["Hourly_Rate"]=$testingExcel[$i][$k+2][0][1];

								// Looping trough the every line to get the log data
								$count = count($testingExcel[$i][$k+2][0]);

								for($t=0; $t<$count; $t++){

									$index = count($chartData["calendar"][$currentMonth]["roles"][$role]["Log"]); 

									if(isset($testingExcel[$i][$k+2][0][$t+1]) && isset($role)){

										$chartData["calendar"][$currentMonth]["roles"][$role]["Log"][$index] = $testingExcel[$i][$k+2][0][$t+1];

										$chartData["calendar"][$currentMonth]["roles"][$role]["Used_HRS_This_Month"] = $chartData["calendar"][$currentMonth]["roles"][$role]["Used_HRS_This_Month"] + $testingExcel[$i][$k+2][0][$t+1];

									}

								}

							}

						}
						/*
						|
						|	Counting the logs for each day and determinating how manny days in month we have.
						|	If the last reccord was "25.Decembar" the chart X label will be X = [1 , 25] and so on.
						|  
						*/

						$daysNr = count($chartData["calendar"][$currentMonth]["roles"][$role]["Log"]);
										
						for($d = 1; $d<=$daysNr; $d++){

							$dayIndex = count($chartData["calendar"][$currentMonth]["Days_in_month"]);

							$chartData["calendar"][$currentMonth]["Days_in_month"][$dayIndex] = $d;

						}
						
					}
					else{ // If current page is not the Month page

						/*
						|------------------------------------------------------------------------------------
						|	********************	 PREPARING CONTRACT DATA!!!	     *******************	|
						|------------------------------------------------------------------------------------
						*/

						if(array_filter($testingExcel[0][1][0])){

							if(array_key_exists($j+2, $testingExcel[0]) && array_unique($testingExcel[0][$j+2][0]) != array(null)){

								for($x=0; $x<count($testingExcel[0][$j+2][0]); $x++){

									$contractDataKey = str_replace(" ", "_", $testingExcel[0][1][0][$x]);		
									
									$chartData["ContractData"][$contractDataKey] = [];

									for($l=0; $l<count($testingExcel[0]); $l++){

										$chartDataKey = count($chartData["ContractData"][$contractDataKey]);

										if(array_key_exists($l+2, $testingExcel[0]) && array_unique($testingExcel[0][$l+2][0]) != array(null)){

											if(!is_null($testingExcel[0][$l+2][0][$x])){
											
												$chartData["ContractData"][$contractDataKey][$chartDataKey] = $testingExcel[0][$l+2][0][$x];
											
											}

										}

									}

								}

							}
		
						}
						else{

							$error = ["error_Message" => "You have mest up something in the 'Master' table. Check out the 'Conventions' for the 'Master'!"];
							return Response::json($error);

						}

					}
		
				}

			}

			$chartData["ContractData"]["PDO"] = array_combine($chartData["ContractData"]["Role"], $chartData["ContractData"]["PDO"]);
			$chartData["ContractData"]["PDM"] = array_combine($chartData["ContractData"]["Role"], $chartData["ContractData"]["PDM"]);
			$chartData["ContractData"]["Hourly_rate"] = array_combine($chartData["ContractData"]["Role"], $chartData["ContractData"]["Hourly_rate"]);
			$chartData["ContractData"]["Daily_rate"] = array_combine($chartData["ContractData"]["Role"], $chartData["ContractData"]["Daily_rate"]);

			return View::make('index', array('data' => json_encode($chartData)));

	}

	}

}
