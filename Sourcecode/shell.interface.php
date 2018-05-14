<?php
	class ShellInterface {
		public static function executeCommand($command){
			//Execute always with sudo
			exec("sudo ".$command, $output); 
			//This code is if you need to execute the sudo, you can put the password on the /var/www/html/sudopass.secret file
			//exec("sudo -u root -S ".$command." < ~/var/www/html/sudopass.secret", $output);
			return $output;
		}

		public static function prepareRegularExpression($regularExpression){
			if(substr($regularExpression, 0, 1) != "/"){
				$regularExpression = "/" . $regularExpression;
			}

			if(substr($regularExpression, -1) != "/"){
				$regularExpression = $regularExpression . "/";
			}

			return $regularExpression;
		}


		public static function commandGrep($command, $regularExpression, $onsuccess = null, $onfailure = null){
			for($i = 0; $i < sizeof($command); $i++){
				$line = $command[$i];
				if(preg_match(ShellInterface::prepareRegularExpression($regularExpression), $line)){
					if(isset($onsuccess)){
						call_user_func_array($onsuccess, array($line));
					} 
				}
			}
		}

		public static function commandSplitBlock($command, $startRegularExpression, $onsuccess = null){
			$blocks = array();
			$blockCount = -1;

			for($i = 0; $i < sizeof($command); $i++){
				$line = $command[$i];

				if(preg_match(ShellInterface::prepareRegularExpression($startRegularExpression), $line)){
					if(isset($blocks[$blockCount]) && isset($onsuccess)){
						call_user_func_array($onsuccess, array($blocks[$blockCount]));
					} 

					$blockCount++;
					$blocks[$blockCount] = array();
				}

				if(isset($blocks[$blockCount])){
					$blocks[$blockCount][] = $line;
				}
			}

			if(isset($blocks[$blockCount]) && isset($onsuccess)){
				call_user_func_array($onsuccess, array($blocks[$blockCount]));
			} 
		}
	}
?>