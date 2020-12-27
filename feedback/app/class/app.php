<?php
require_once 'cfg.php';
$admin = isset($_SESSION[KEY . 'admin'])?$_SESSION[KEY . 'admin']:0;
define('ADMIN',$admin);
$config = get_config();
function get_config()
{
    $db = new DbHelpClass();
    if (empty($_SESSION[KEY . 'config'])) {
        $rs = $db->getdata("select * from `config` where id=1");
        $config = $rs[0];
        $_SESSION[KEY . 'config'] = $rs[0];
    } else {
        $config = $_SESSION[KEY . 'config'];
    }
    return $config;
}
function hide(){
    if(ADMIN) echo ' d-none';
}
function ckadm(){
   if(!ADMIN) logmsg(0,'未登录');
}
function logmsg($b, $msg = '操作成功！')
{
    if ($b > 0) {
        $arr['result'] = 200;
        $arr['message'] = $msg;
    } else {
        $arr['result'] = 500;
        if (empty($msg)) {
            $arr['message'] = '操作失败！';
        } else {
            $arr['message'] = $msg;
        }
    }
    $arr['id'] = $b;
    echo json_encode($arr);
    exit;
}

function view_ip($ip,$mail=''){
    
    $reg='~(\d+)\.(\d+)\.(\d+)\.(\d+)~'; 
    return ADMIN?$ip.' <span class="mail">'.$mail.'</span>':preg_replace($reg,"$1.$2.*.*",$ip);//以上输出结果为：127.0.*.*
}

function view_content($content,$lock){
     //return ADMIN?$content:nl2br($content);
     if (ADMIN){
     	return $content;
     }else{
        return $lock==1?'留言审核中...':nl2br($content);	
     }
}

function get_ip()
{
    if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        $ip = getenv('HTTP_CLIENT_IP');
    } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $ip = getenv('REMOTE_ADDR');
    } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function get_captch()
{
    $image = imagecreatetruecolor(100, 40);
    $bgcolor = imagecolorallocate($image, 255, 255, 255);
    imagefill($image, 0, 0, $bgcolor);
    $captch_code = '';
    for ($i = 0; $i < 4; $i++) {
        $fontsize = 12;
        $fontcolor = imagecolorallocate($image, rand(0, 120), rand(0, 120), rand(0, 120));
        $data = '1234567890';
        $fontcontent = substr($data, rand(0, strlen($data) - 1), 1);
        $captch_code .= $fontcontent;
        $x = $i * 100 / 4 + rand(5, 10);
        $y = rand(5, 10);
        imagestring($image, $fontsize, $x, $y, $fontcontent, $fontcolor);
    }
    //就生成的验证码保存到session
    $_SESSION[KEY . 'captch'] = strtolower($captch_code);
    //在图片上增加点干扰元素
    for ($i = 0; $i < 200; $i++) {
        $pointcolor = imagecolorallocate($image, rand(50, 200), rand(50, 200), rand(50, 200));
        imagesetpixel($image, rand(1, 99), rand(1, 29), $pointcolor);
    }
    //在图片上增加线干扰元素
    for ($i = 0; $i < 3; $i++) {
        $linecolor = imagecolorallocate($image, rand(80, 220), rand(80, 220), rand(80, 220));
        imageline($image, rand(1, 99), rand(1, 29), rand(1, 99), rand(1, 29), $linecolor);
    }
    //设置头
    header('content-type:image/png');
    imagepng($image);
    imagedestroy($image);
}

 function timeago($ptime) {
        $ptime = strtotime($ptime);
        $etime = time() - $ptime;
        if($etime < 1) return '刚刚';
        $interval = array (
            12 * 30 * 24 * 60 * 60  =>  '年前 ('.date('Y-m-d', $ptime).')',
            30 * 24 * 60 * 60       =>  '个月前 ('.date('m-d', $ptime).')',
            7 * 24 * 60 * 60        =>  '周前 ('.date('m-d', $ptime).')',
            24 * 60 * 60            =>  '天前',
            60 * 60                 =>  '小时前',
            60                      =>  '分钟前',
            1                       =>  '秒前'
        );
        foreach ($interval as $secs => $str) {
            $d = $etime / $secs;
            if ($d >= 1) {
                $r = round($d);
                return $r . $str;
            }
        }
}

function arr_sql($tab, $run, $arr)
{
    $k = array_keys($arr);
    if ($run == 'insert') {
        $sql = "insert into `{$tab}`(" . join(',', $k) . ")values(:" . join(',:', $k) . ")";
    } else {
        foreach ($k as $v) {
            $s[] = $v . '=:' . $v;
        }
        $sql = "update `{$tab}` set " . join(',', $s) . " where id=:id";
    }
    return $sql;
}


class DbHelpClass
{
    private $conn;
    private $qxId;
    private $ret;
    function __construct()
    {
        try {
            $this->conn = new PDO('sqlite:' . DB);
        } catch (Exception $errinfo) {
            die("PDO Connection faild.(可能空间不支持pdo_sqlite，详细错误信息：)" . $errinfo);
        }
    }
    /*读取*/
    function getdata($sql, $params = array())
    {
        $bind = $this->conn->prepare($sql);
        $arrKeys = array_keys($params);
        foreach ($arrKeys as $row) {
            if (strpos($sql, "like") > -1) {
                $bind->bindValue(":" . $row, '%' . $params[$row] . '%');
            } else {
                $bind->bindValue(":" . $row, $params[$row]);
            }
        }
        $bind->execute();
        // or die('sql error:'.$sql);
        $result = $bind->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    function total($sql,$params=array())//求总记录数目
           {
             $bind = $this->conn->prepare($sql);
			 $arrKeys=array_keys($params);
             foreach($arrKeys as $row)
             {
				if(strpos($sql,"like")>-1){
				  $bind->bindValue(":".$row,'%'.$params[$row].'%');
				}else{
                  $bind->bindValue(":".$row,$params[$row]);
				}
             }
             $bind->execute();
             $result = $bind->fetchAll();
             return $result[0]['c'];
     }  
    /*添加,修改需调用此方法*/
    function runsql($sql, $params = array())
    {
        $bind = $this->conn->prepare($sql);
        $arrKeys = array_keys($params);
        foreach ($arrKeys as $row) {
            $bind->bindValue(":" . $row, $params[$row]);
        }
        $a = $bind->execute();
        //or die('sql error');
        if (strpos($sql, "insert") > -1) {
            return $this->conn->lastInsertId();
        } else {
            return $a;
        }
    }
}
