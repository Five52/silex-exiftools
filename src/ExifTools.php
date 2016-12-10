<?php
namespace ExifTools;
/**
* 
*/
class ExifTools
{
	
	public static function getImgDetails(string $imgName) 
	{
		$command = "exiftool -g ../web/files/" . $imgName;
		exec($command, $output, $return_var);

		//creating associativ array of the exiftool response:
		$arrayData = [];
		$key = "";
		foreach ($output as $line) {
			if (preg_match("/-{4} \w* -{4}/", $line)) {
				$key = trim(preg_replace("/-/", "", $line));
				$arrayData[$key] = [];
			} else {
				$data = explode(':', $line);
				$arrayData[$key][trim(preg_replace("/ /", "", $data[0]))] = trim($data[1]);
			}
		}

		//generating json file from the array:
		$jsonData = json_encode($arrayData);
		var_dump($jsonData);
		$name = preg_replace("/\.\w*/", "", $imgName);
		if ($arrayData) {
			$file = fopen($name.".json", "w");
			fwrite($file, $jsonData);
			fclose($file);
		} else {
			//error
			var_dump('error enconding json');
		}

		return $arrayData;
	}

}