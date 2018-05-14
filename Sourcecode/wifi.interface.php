<?php
	class WifiInterface{
		private $temp_status = null;
		private $temp_SSIDlist = null;

		public function status(){
			$this->temp_status = array();

			foreach ($this->getInterfaces() as $key => $interface) {
				$status = (object)array(
					"Interface" => $interface,
					"MacAddress" => null,
					"IP" => null,
					"Gateway" => null,
					"Mask" => null,
					"IsPowerUp" => null,
					"IsConnected" => null,
					"SSID" => null
				);

				if($this->interfaceIsOn($interface)){
					$status->IsPowerUp = true;
				    $status->MacAddress = $this->getMacAddress($interface);

				    if($this->interfaceIsConnected($interface)){
				    	$status->IsConnected = true;
						$status->IP = $this->getIP($interface);
						$status->Gateway = $this->getGateway($interface);
						$status->Mask = $this->getMask($interface);
						$status->SSID = $this->getSSID($interface);
					}else{
						$status->IsConnected = false;
					}
				}else{
					$status->IsPowerUp = false;
				}

			    $this->temp_status[] = $status;
			}
			
			return $this->temp_status;
		}

		private function getInterfaces(){
			$command = ShellInterface::executeCommand("iw dev | awk '{if(($1 == \"Interface\")) print $2}'");
			return $command;
		}

		private function getMacAddress($interface){
			$command = ShellInterface::executeCommand("ifconfig -a ".$interface." | awk '{if($2 == \"Link\") print $5}'");
			return $command[0];
		}

		private function getIP($interface){
			$command = ShellInterface::executeCommand("ifconfig -a ".$interface." | awk '{if($1 == \"inet\"){  gsub(\"addr:\",\"\"); print $2 }}'");
			return sizeof($command) > 0 ? $command[0] : "0.0.0.0";
		} 

		private function interfaceIsOn($interface){
			$command = ShellInterface::executeCommand("ifconfig | awk '{if($1 == \"".$interface."\") print $1}'");
			return sizeof($command) > 0 ? true : false;
		}

		private function interfaceIsConnected($interface){
			return $this->getIP($interface) == "0.0.0.0" ? false : true;
		}

		private function getGateway($interface){
			$command = ShellInterface::executeCommand("ifconfig -a ".$interface." | awk '{if($1 == \"inet\"){  gsub(\"Bcast:\",\"\"); print $3 }}'");
			return sizeof($command) > 0 ? $command[0] : "0.0.0.0";
		} 

		private function getMask($interface){
			$command = ShellInterface::executeCommand("ifconfig -a ".$interface." | awk '{if($1 == \"inet\"){  gsub(\"Mask:\",\"\"); print $4 }}'");
			return sizeof($command) > 0 ? $command[0] : "0.0.0.0";
		} 

		private function getSSID($interface){
			$command = ShellInterface::executeCommand("iwconfig ".$interface." | awk '{if($1 == \"wlan0\"){ gsub(\"ESSID:\",\"\"); gsub(\"\\\"\",\"\"); print $4 }}'");
			return sizeof($command) > 0 ? $command[0] : null;
		} 


		public function turnInterfaceOn($interface){
			ShellInterface::executeCommand("ip link set ".$interface." up");
		}

		public function turnInterfaceOff($interface){
			ShellInterface::executeCommand("ip link set ".$interface." down");
		}

		public function scan($interfaceName){
			$this->temp_SSIDlist = array(); 

			$command = ShellInterface::executeCommand("nmcli dev wifi list | awk '{print $1\",\"$3\",\"$6}'");
			for($i = 1; $i < sizeof($command); $i++){
				$data = explode(",", $command[$i]);

				$obj = (object)array(
	  				"SSID" => trim($data[0]),
	  				"Chanel" => intval($data[1]),
	  				"Signal" => intval($data[2])
				);
				
				$this->temp_SSIDlist[] = $obj;
			}

			return $this->temp_SSIDlist;
		}

		public function connect($interfaceName, $SSID, $password){
			$this->turnOn($interfaceName);
			ShellInterface::executeCommand("nmcli dev wifi connect ".$SSID." password ".$password);
		}

		public function disconnect($interfaceName, $SSID){
			ShellInterface::executeCommand("nmcli con down ".$SSID);
		}
	}
?>