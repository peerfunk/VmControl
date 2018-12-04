Wenn bei der VM  'Vetinf' 'Spotchem  läuft nicht ' angezeigt wird Seite zuerst neu laden, manchmal erkennt er es beim reload. </br>

<?php



if($_GET){
	if($_GET['startvm']){
		echo shell_exec("sudo su root -c 'vboxmanage startvm " . $_GET['startvm'] . " --type headless'");
	}
	if($_GET['stopvm']){
		echo shell_exec("sudo su root -c 'vboxmanage controlvm " . $_GET['stopvm'] . " savestate'");
	}
	if($_GET['start_spotchem']){
		start_spotchem();
	}
	if($_GET['kill_spotchem']){
		stop_spotchem();
        }
header('Location: '.$_SERVER['PHP_SELF']);  
}


$all_vms = shell_exec("sudo su root -c 'vboxmanage list vms'");
$running_vms =shell_exec("sudo su root -c 'vboxmanage list runningvms'");
//echo $running_vms;

$all_array = explode("\n", $all_vms);
$running_array=explode("\n",$running_vms);

for($i=0; $i < (count($all_array) && $all_array[i] != '\n');$i++){
//	echo "$i)name: " . $all_array[$i] . " )</br>";
//	echo "extractuuiid:" . extract_uuid($all_array[$i]) ."</br>";
	if (is_running($all_array[$i], $running_array)){
		echo put_running_layout($all_array[$i]) . "</br>";
	}else {
		echo put_not_running_layout($all_array[$i]) . "</br>";
	}
}



function is_running($mom, $running){
$is_running=false;
	for($i=0;$i< (count($running) && !$is_running);$i++){
		$is_running=($mom==$running[$i]);
	}
return $is_running;
}

function put_running_layout($mom){
echo "<img src='gear.gif' width='60'>";
echo extract_name($mom);
echo "<a href='?stopvm=" . extract_uuid($mom) . "'> VM in Ruhezustand versetzen </a>";
if(is_vetinf($mom)){
	if(is_vetinf_running()){
		echo "SPOTCHEM LAEUFT!";
		echo "<a href='?kill_spotchem=true'>Spotchem beenden</a>";
	}else{
		echo "SPOTCHEM LAEUFT NICHT!";
		echo "<a href='?start_spotchem=true'>Spotchem starten</a>";
	}
}
echo "</br>";

}

function put_not_running_layout($mom){
echo "<img src='gear.png' width='60'>";
echo extract_name($mom);
//start vm
echo "<a href='?startvm=" . extract_uuid($mom) . "'> Starte VM </a>";
echo "</br>";
}


function is_vetinf($mom){
return extract_name($mom) == "Vetinf";
}

function is_vetinf_running(){
$isrunning = shell_exec("( sleep 0.3; printf 'username\r\n'; sleep 0.3;printf 'password\r\n';sleep 0.3;printf 'sc query spotchem|findstr /i \"STATE\" \n\r';sleep 0.3; printf 'exit \r\n' )|telnet 192.168.10.10");
if (strpos($isrunning, 'RUNNING') !== false){
return true;
}else if(strpos($isrunning, 'STOPPED') !== false){
return false;
}else {
echo "</br>ERROR!</br>";
}
}

function  start_spotchem(){
$start = shell_exec("( sleep 0.3; printf 'username\r\n'; sleep 0.3;printf 'password\r\n';sleep 0.3;printf 'cd C:\ \r\n'; sleep 0.4;  printf 'net start spotchem\n\r';sleep 1; )|telnet 192.168.10.10");
}
function stop_spotchem(){
$kill = shell_exec("( sleep 0.3; printf 'username\r\n'; sleep 0.3;printf 'password\r\n';sleep 0.3;printf 'net stop spotchem\n\r';sleep 0.3;exit )|telnet 192.168.10.10");
}

function ruhezustand($mom){
shell_exec('sudo su root -c "vboxmanage controlvm "' . $name . ' savestate"');
}

function extract_name($mom){
return explode('"',$mom)[1];
}

function extract_uuid($mom){
return substr(explode('{',$mom)[1],0,-1);
}
?>


