# PHP Wifi Interface
This is an Wifi Interface for Linux and PHP. The objective was make more easy to control the wifi interface using PHP methods.


# Example
Here you can see an example of the code. In this example, i had the Wifi class that extends the Controller from [PHP Simple MVC Framework](https://github.com/alissonrubim/PHPSimpleMVCFramework), and in this class i call the WifiInterface.

```
<?php
	class Wifi extends Controller {
		public function status(){
			$obj = new WifiInterface();
			$status = $obj->status();
			echo json_encode($status);
		}

		public function scan(){ 
			$interfaceName = $this->getParameter("InterfaceName");
			$obj = new WifiInterface();
			$status = $obj->scan($interfaceName);
			echo json_encode($status);
		}

		public function connect(){
			$SSID = $this->getParameter("SSID");
			$password = $this->getParameter("password");
			$interfaceName = $this->getParameter("InterfaceName");
			$obj = new WifiInterface();
			$status = $obj->connect($interfaceName, $SSID, $password);
			echo json_encode($status);
		}

		public function disconnect(){
			$interfaceName = $this->getParameter("InterfaceName");
			$SSID = $this->getParameter("SSID");
			$obj = new WifiInterface();
			$obj->disconnect($interfaceName, $SSID);
		}

		public function wizard(){
			if(Auth::islogged()){
				$this->view("wizard",null);
			}else{
				$this->redirectTo("login", "index");
			}
		}
	}
?>

```
# The test
The test was made on the Orange Pi Zero, running Debian on it.
