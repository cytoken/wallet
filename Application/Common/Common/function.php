<?php


//将用户名进行处理，中间用星号表示
function substr_cut($user_name){

    //获取字符串长度
    $strlen = mb_strlen($user_name, 'utf-8');
    //如果字符创长度小于2，不做任何处理
    if($strlen<2){
        return $user_name;
    }else{
        //mb_substr — 获取字符串的部分
        $firstStr = mb_substr($user_name, 0, 1, 'utf-8');
        $lastStr = mb_substr($user_name, -1, 1, 'utf-8');
        //str_repeat — 重复一个字符串
        return $strlen == 2 ? $firstStr . str_repeat('*', mb_strlen($user_name, 'utf-8') - 1) : $firstStr . str_repeat("*", $strlen - 2) . $lastStr;
    }
}

/**
 * 随机奖票
 * @param $totalBonus 奖金总额
 * @param $cycle 周期
 * @param $floating 上下浮动
 */
function randomBonus($totalBonus, $cycle, $floating) {
    $bonus = $totalBonus / $cycle; //每期分得奖金
    $upBonus = $bonus * $floating + $bonus ;
    $dowBonus = $bonus - $bonus * $floating;
    $case = array();
//    for ($i = 1; $i < 50; $i++) {
//        for ($j = 1; $j < $totalBonus/$upBonus; $j++){
//            for ($k = 1; $k < $totalBonus/$dowBonus; $k++) {
//                if ($i+$j+$k == 50 && ($i * $bonus + $j * $upBonus + $k * $dowBonus) == $totalBonus){
////                    echo $bonus."元的:".$i."张 ".$upBonus."元的:".$j."张 ".$dowBonus."元的:".$k."张。<br/>";
//                    array_push($case, array($bonus=>$i,$upBonus=>$j,$dowBonus=>$k)); //将结果添加入数组
//                }
//            }
//        }
//    }
//
//    shuffle($case); //将所有情况打乱
//    return $case[array_rand($case,1)]; //返回这个随机数组
    return  array($bonus=>$cycle,$upBonus=>0,$dowBonus=>0); //将结果添加入数组
}

function roleName($roleId){
   switch($roleId){
       case 0:
           return '普通投资者';
       case 1:
           return 'VIP用户';
       case 2:
           return '服务中心';
       case 3:
           return '商务中心';
       case 4:
           return '运营中心';
   }
}


/*
 * 批量向sort set中添加元素
 * $redis obj: redis连接对象
 * $key str: sort set的key
 * $redis->zAdd(key, score, val [score, val ...]);
 * $elements array:待添加元素的集合，每一项为array('val' => score)
*/
function zAddArray($redis, $key, $elements){
    if (!$redis || !$key || !is_array($elements)){
        return false;
    }

    $p[] = $key;
    foreach ($elements as $k => $v){
        $p[] = $v;
        $p[] = $k;
    }
    $res = call_user_func_array(array($redis, 'zAdd'), $p);

    return $res;
}

/*
 * 批量删除sort set元素
 * $redis obj: redis连接对象
 * $key str: sort set的key
 * $redis->zDelete(key member [member ...]);
 * $elements array:待添加元素的集合，每一项为array(member)
*/
function zDelArray($redis, $key, $elements){
    if (!$redis || !$key || !is_array($elements)){
        return false;
    }

    array_unshift($elements, $key);
    $res = call_user_func_array(array($redis, 'zDelete'), $elements);

    return $res;
}

/*
 * 批量删除Hash元素
 * $redis obj: redis连接对象
 * $key str: sort set的key
 * $redis->zDelete(key field [field ... ] );
 * $elements array:待添加元素的集合，每一项为array(member)
*/
function hDelArray($redis, $key, $elements){
    if (!$redis || !$key || !is_array($elements)){
        return false;
    }

    array_unshift($elements, $key);

    $res = call_user_func_array(array($redis, 'hDel'), $elements);
    return $res;
}

/**
 * 验证码检查
 */
function check_verify($code, $id = "") {
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}

function getSMSToken() {
    $time = time();
    $key = substr(md5($time . "YINGFANKEJI2018"), 6, 6);
    return array('key'=>$key, 'time'=>$time);
}

function checkAccessToken($accessToken, $token, $startTime) {
    if (strtotime(-15,time()) > strtotime($startTime)) {
        return false;
    }

    if ($accessToken != $token) {
        return false;
    }
    return true;
}

function success_json($msg = "请求成功!", $data = null ,$total= null,$count=null, $lock=null) {
    $result['msg'] = $msg;
    $result['code'] = 0;
    if(isset($data)) {
        $result['data'] = $data;
    }
    if(isset($total)) {
        $result['recordsTotal'] = $total;
        $result['recordsFiltered'] = $total;
    }
    if(isset($lock)){
        $result['lock'] = $lock;
    }
    if(isset($count)){
        $result['count'] = $count;
    }

    return $result;
}

function timediff( $begin_time, $end_time )
{
    if ( $begin_time < $end_time ) {
        $starttime = $begin_time;
        $endtime = $end_time;
    } else {
        $starttime = $end_time;
        $endtime = $begin_time;
    }
    $timediff = $endtime - $starttime;
    $days = intval( $timediff / 86400 );
    $remain = $timediff % 86400;
    $hours = intval( $remain / 3600 );
    $remain = $remain % 3600;
    $mins = intval( $remain / 60 );
    $secs = $remain % 60;
    $hours = $days*24 + $hours;
    return "距离活动开始时间还有：".$hours."小时".$mins."分钟".$secs."秒";
}

function fail_json($msg = "非法的请求参数!") {
    $result['msg'] = $msg;
    $result['code'] = 1;
    return $result;
}

/**
* 验证手机号吗格式正确性
* @param $minLen $value
* @param $maxLen $length
* @return boolean
    */
function isPhone($value){
    // $match='/^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$/';
    $match='/^((17[0-9])|(19[0-9])|(14[0-9])|(13[0-9])|(15[^4,\D])|(18[0-9]))\d{8}$/';
    $v = trim($value);
    if(empty($v))
        return false;
    return preg_match($match,$v);
}

/**
 * 流水号
 * @return string
 */
function build_order_no() {
    return date('ds') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
    //return date('md') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 4);
}


/**
 * 生成6位随机数字
 */
function build_number($length = 4) {
    // 密码字符集，可任意添加你需要的字符
    $chars = '0123456789';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[mt_rand(0, strlen($chars) - 1)];
    }
    return $password;
}
function build_number_Code($length = 6) {
    // 密码字符集，可任意添加你需要的字符
    $arr= array_merge(range(0, 9), range('a', 'z'));
    $str= '';
    $arr_len= count($arr);
    for($i= 0; $i<$length; $i++)
    {
        $rand= mt_rand(0, $arr_len-1);
        $str.=$arr[$rand];
    }

    return$str;
}

/**
 * 平台正常运行短信
 * @param $phone
 * @return bool|mixed
 */
function sendNormalSms($phone){
    $params['token'] = getSMSToken(); //token验证
    $params['source'] = C('DOMAIN_NAME'); //来源
    $params['password'] = md5(C('PASSWORD'));//密码
    $params['phone'] = $phone; //接受短信的手机号
    $params['templateId'] = 'WEBSTAR_NET_10012';
    $params['data'] = json_encode(array("phonecaptcha"=>$phone));//将参数以键值对形式储存
    $params = array_merge($params);
    //sms.yingfankeji.net 新的域名
    return request_post('http://sms.yingfankeji.net/index.php/Home/Index/send', http_build_query($params));
}

/**
 * 预警短信
 * @param $phone
 * @param $verifyCode
 * @return bool|mixed
 */
function sendWarningSms($phone,$verifyCode){
    $params['token'] = getSMSToken(); //token验证
    $params['source'] = C('DOMAIN_NAME'); //来源
    $params['password'] = md5(C('PASSWORD'));//密码
    $params['phone'] = $phone; //接受短信的手机号
    $params['templateId'] = 'WEBSTAR_NET_10013';
    $params['args'] = array($verifyCode);//模板参数
    $params['data'] = json_encode(array("phonecaptcha"=>$verifyCode));//将参数以键值对形式储存
    $params = array_merge($params);
    //sms.yingfankeji.net 新的域名
    return request_post('http://sms.yingfankeji.net/index.php/Home/Index/send', http_build_query($params));
}

/**
 * 分页方法，生成前面分页需要的模板内容
 * @param string $count 总数
 * @param string $limit 每页个数
 * @param int    $p     当前页数
 * @param string $prev
 * @param string $nextv
 * @return array 返回page,二维数组
 */
function gtPage($count, $limit, $p = 1, $prev = "上一页", $nextv = "下一页") {
    $page = array ();
    if ($count == 0) {
        return $page;
    }
    $self = __SELF__;
    if ($pos = strpos($self, "&p=")) {
        $self = substr(__SELF__, 0, $pos);
    }

    if (!strpos($self, "?")) {
        $self .= "?";
    }

    //计算页数
    if ($count % $limit == 0) {
        $number = $count / $limit;
    } else {
        $number = floor($count / $limit) + 1;
    }
    //处理前一页和下一页

    $pre['value']  = $prev;
    $next['value'] = $nextv;
    if ($p <= 1) {
        $pre['url'] = $self . "&p=" . "1";
    } else {
        $pre['url'] = $self . "&p=" . ($p - 1);
    }
    if ($p >= $number) {
        $next['url'] = $self . "&p=" . $number;
    } else {
        $next['url'] = $self . "&p=" . ($p + 1);

    }

    if ($count > $limit) {
        $page[] = $pre;
    }

    for ($i = 1; $i <= $number; $i++) {
        $row['url']   = $self . "&p=" . $i;
        $row['value'] = $i;
        $row['p']     = $p;
        $page[]       = $row;
    }
    if ($count > $limit) {
        $page[] = $next;
    }
    return $page;
}


/**
 * 导出xls
 * @param (array) $data
 * @param string $filename 文件名
 * @param (array) $title 每列的标题
 */
//function create_xls($data,$filename='simple.xls',$title=array()){
//    ini_set('max_execution_time', '0');
//    Vendor('PHPExcel.PHPExcel');
//    $filename=str_replace('.xls', '', $filename).'.xls';
//    $phpexcel = new PHPExcel();
//    $phpexcel->getProperties()
//        ->setCreator("Maarten Balliauw")
//        ->setLastModifiedBy("Maarten Balliauw")
//        ->setTitle("Office 2007 XLSX Test Document")
//        ->setSubject("Office 2007 XLSX Test Document")
//        ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
//        ->setKeywords("office 2007 openxml php")
//        ->setCategory("Test result file");
//    $phpexcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
//    $dataArray = array( $title);
//    $phpexcel->getActiveSheet()->fromArray($dataArray, NULL, 'A1');
//    $phpexcel->getActiveSheet()->fromArray($data,null,'A2');
//    $phpexcel->getActiveSheet()->setTitle('Sheet1');
//    $phpexcel->setActiveSheetIndex(0);
//    header('Content-Type: application/vnd.ms-excel');
//    header("Content-Disposition: attachment;filename=$filename");
//    header('Cache-Control: max-age=0');
//    header('Cache-Control: max-age=1');
//    header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
//    header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
//    header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
//    header ('Pragma: public'); // HTTP/1.0
//    $objwriter = PHPExcel_IOFactory::createWriter($phpexcel, 'Excel5');
//    $objwriter->save('php://output');
//    exit;
//}
function create_xls($data, $filename = 'simple.xls', $title = array()) {
    ini_set('max_execution_time', '0');
    Vendor('PHPExcel.PHPExcel');
    $filename = str_replace('.xls', '', $filename) . '.xls';
    $phpexcel = new PHPExcel();
    $phpexcel->getProperties()
        ->setCreator("Maarten Balliauw")
        ->setLastModifiedBy("Maarten Balliauw")
        ->setTitle("Office 2007 XLSX Test Document")
        ->setSubject("Office 2007 XLSX Test Document")
        ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
        ->setKeywords("office 2007 openxml php")
        ->setCategory("Test result file");
    $dataArray = array($title);
    $phpexcel->getActiveSheet()->fromArray($dataArray, NULL, 'A1');
    $phpexcel->getActiveSheet()->fromArray($data, null, 'A2');
    //设置默认的字体和文字大小     锚：aaa
    $phpexcel->getDefaultStyle()->getFont()->setName('Arial');
    $phpexcel->getDefaultStyle()->getFont()->setSize(13);
    for ($i = 0; $i < count($data); $i++) {
//        if ($data[$i]['status']==1){
//            $data[$i]['status']="待支付";
//        }
//        if ($data[$i]['status']==2){
//            $data[$i]['status']="待发货";
//        }
//        if ($data[$i]['status']==3){
//            $data[$i]['status']="待签收";
//        }
//        if ($data[$i]['status']==4){
//            $data[$i]['status']="交易完成";
//        }
//        if ($data[$i]['status']==5){
//            $data[$i]['status']="交易取消";
//        }
//        if ($data[$i]['remarkStatus']==0){
//            $data[$i]['remarkStatus']="未绑定";
//        }
//        if ($data[$i]['remarkStatus']==1){
//            $data[$i]['remarkStatus']="已绑定";
//        }
//        if ($data[$i]['rechargeStatus']==0){
//            $data[$i]['rechargeStatus']="未充值";
//        }
//        if ($data[$i]['rechargeStatus']==1){
//            $data[$i]['rechargeStatus']="已充值";
//        }

        //设置列的宽度      锚：bbb
        $phpexcel -> getActiveSheet() -> getColumnDimension(PHPExcel_Cell::stringFromColumnIndex("A" . ($i + 1) . ":T" . ($i + 2))) -> setAutoSize(true);
        //水平方向上两端对齐
        $phpexcel->getActiveSheet()->getStyle("A" . ($i + 1) . ":T" . ($i + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //垂直方向上中间居中
        $phpexcel->getActiveSheet()->getStyle("A" . ($i + 1) . ":T" . ($i + 2))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        //设置单元格边框  锚：bbb
        $styleThinBlackBorderOutline = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,   //设置border样式
                    //'style' => PHPExcel_Style_Border::BORDER_THICK,  另一种样式
                    'color' => array('argb' => 'FF000000'),          //设置border颜色
                ),
            ),
        );
        $phpexcel->getActiveSheet()->getStyle("A" . ($i + 2) . ":T" . ($i + 2))->applyFromArray($styleThinBlackBorderOutline);

    }
    $phpexcel->getActiveSheet()->getStyle('T1:P'.(count($data)+1))->getAlignment()->setWrapText(TRUE);

    $phpexcel->getActiveSheet()->getColumnDimension('A:P')->setAutoSize(true);
//    $phpexcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
//    $phpexcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
//    $phpexcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
//    $phpexcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
//    $phpexcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);

    $phpexcel->getActiveSheet()->setTitle('Sheet1');
    $phpexcel->setActiveSheetIndex(0);
    header('Content-Type: application/vnd.ms-excel');
    header("Content-Disposition: attachment;filename=$filename");
    header('Cache-Control: max-age=0');
    header('Cache-Control: max-age=1');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0
    $objwriter = PHPExcel_IOFactory::createWriter($phpexcel, 'Excel5');
    $objwriter->save('php://output');
    exit;
}
/**
 * 导入excel文件
 * @param  string $file excel文件路径
 * @return array        excel文件内容数组
 */
function import_excel($file) {
    // 判断文件是什么格式
    $type = pathinfo($file);
    $type = strtolower($type["extension"]);
    $type=$type==='csv' ? $type : 'Excel5';
    ini_set('max_execution_time', '0');
    Vendor('PHPExcel.PHPExcel');
    // 判断使用哪种格式
    $objReader = PHPExcel_IOFactory::createReader($type);
    $objPHPExcel = $objReader->load($file);
    $sheet = $objPHPExcel->getSheet(0);
    // 取得总行数
    $highestRow = $sheet->getHighestRow();
    // 取得总列数
    $highestColumn = $sheet->getHighestColumn();
    //循环读取excel文件,读取一条,插入一条
    $data=array();
    //从第一行开始读取数据
    for($j=2;$j<=$highestRow;$j++){
        //从A列读取数据
        for($k='B';$k<=$highestColumn;$k++){
            // 读取单元格
            $data[$j-2][]=$objPHPExcel->getActiveSheet()->getCell("$k$j")->getValue();
        }
    }
    return $data;
}

/**
 * 判断是否键存在，并不为空值
 *
 * @param $must  array 需要存在的键
 * @param $no    array 不能存在的键
 * @param $array array
 * @return string null
 */
function haskey($array, $must = array (), $no = array ()) {
    $re = NULL;
    foreach ($must as $value) {
        if ($array[$value] == "" || $array[$value] == NULL) {
            $re .= $value . " is necessary;";
        }
    }
    foreach ($no as $value) {
        if (array_key_exists($no, $array)) {
            $re .= $value . " is refused;";
        }
    }
    if ($re != NULL) {
        $result['info']  = $re;
        $result['state'] = "0";
        $result          = json_encode($result);
        return $result;
    } else {
        return NULL;
    }
}

function getIPaddress() {
    $IPaddress = '';
    if (isset($_SERVER)) {
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $IPaddress = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else {
            if (isset($_SERVER["HTTP_CLIENT_IP"])) {
                $IPaddress = $_SERVER["HTTP_CLIENT_IP"];
            } else {
                $IPaddress = $_SERVER["REMOTE_ADDR"];
            }
        }
    } else {
        if (getenv("HTTP_X_FORWARDED_FOR")) {
            $IPaddress = getenv("HTTP_X_FORWARDED_FOR");
        } else {
            if (getenv("HTTP_CLIENT_IP")) {
                $IPaddress = getenv("HTTP_CLIENT_IP");
            } else {
                $IPaddress = getenv("REMOTE_ADDR");
            }
        }
    }
    return $IPaddress;
}

function taobaoIP($clientIP) {
    $taobaoIP = 'http://ip.taobao.com/service/getIpInfo.php?ip=' . $clientIP;
    $IPinfo   = json_decode(file_get_contents($taobaoIP));
    //$province = $IPinfo->data->region;
    $city = $IPinfo->data->city;
    if (empty($city)) {
        $data = '威海市';
    } else {
        $data = $city;
    }
    return $data;
}

//判断数组中是否有空值
function hasNull($arr) {
    foreach ($arr as $key => $value) {
        if (empty($value)) {
            return TRUE;
        }
    }
    return FALSE;
}

/**
 * 是否移动端访问访问
 *
 * @return bool
 */
function isMobile() {
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])) {
        return TRUE;
    }
    // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset ($_SERVER['HTTP_VIA'])) {
        // 找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], "wap") ? TRUE : FALSE;
    }
    // 脑残法，判断手机发送的客户端标志,兼容性有待提高
    if (isset ($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array ('nokia',
                                 'sony',
                                 'ericsson',
                                 'mot',
                                 'samsung',
                                 'htc',
                                 'sgh',
                                 'lg',
                                 'sharp',
                                 'sie-',
                                 'philips',
                                 'panasonic',
                                 'alcatel',
                                 'lenovo',
                                 'iphone',
                                 'ipod',
                                 'blackberry',
                                 'meizu',
                                 'android',
                                 'netfront',
                                 'symbian',
                                 'ucweb',
                                 'windowsce',
                                 'palm',
                                 'operamini',
                                 'operamobi',
                                 'openwave',
                                 'nexusone',
                                 'cldc',
                                 'midp',
                                 'wap',
                                 'mobile'
        );
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return TRUE;
        }
    }
    // 协议法，因为有可能不准确，放到最后判断
    if (isset ($_SERVER['HTTP_ACCEPT'])) {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== FALSE) && (strpos($_SERVER['HTTP_ACCEPT'],
            'text/html') === FALSE || (strpos($_SERVER['HTTP_ACCEPT'],
            'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'],
               'text/html')))
        ) {
            return TRUE;
        }
    }
    return FALSE;
}


//导出Excel表格
function export($data, $excelFileName, $sheetTitle, $firstrow) {

    /* 实例化类 */
    import('Vendor.phpExcel.PHPExcel');
    $objPHPExcel = new PHPExcel();

    /* 设置输出的excel文件为2007兼容格式 */
    //$objWriter=new PHPExcel_Writer_Excel5($objPHPExcel);//非2007格式
    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);

    $csvWriter = new PHPExcel_Writer_CSV($objPHPExcel);
    /* 设置当前的sheet */
    $objPHPExcel->setActiveSheetIndex(0);

    $objActSheet = $objPHPExcel->getActiveSheet();

    /*设置宽度*/
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);


    /* sheet标题 */
    $objActSheet->setTitle($sheetTitle);
    $j = 'A';
    foreach ($firstrow as $value) {
        $objActSheet->setCellValue($j . '1', $value);
        $j++;
    }

    $i = 2;
    foreach ($data as $value) {
        /* excel文件内容 */
        $j = 'A';
        foreach ($value as $value2) {
            //            $value2=iconv("gbk","utf-8",$value2);
            $objActSheet->setCellValue($j . $i, $value2);
            $j++;
        }
        $i++;
    }


    /* 生成到浏览器，提供下载 */
    ob_end_clean();  //清空缓存
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
    header("Content-Type:application/force-download");
    header("Content-Type:application/vnd.ms-execl");
    header("Content-Type:application/octet-stream");
    header("Content-Type:application/download");
    header('Content-Disposition:attachment;filename="' . $excelFileName . '.xlsx"');
    header("Content-Transfer-Encoding:binary");
    $objWriter->save('php://output');
}

function exportCSV($data, $excelFileName, $sheetTitle, $firstrow) {

    /* 实例化类 */
    import('Vendor.phpExcel.PHPExcel');
    $objPHPExcel = new PHPExcel();

    /* 设置输出的excel文件为2007兼容格式 */
    //$objWriter=new PHPExcel_Writer_Excel5($objPHPExcel);//非2007格式

    $csvWriter = new PHPExcel_Writer_CSV($objPHPExcel, 'CSV');
    /* 设置当前的sheet */
    $objPHPExcel->setActiveSheetIndex(0);

    $objActSheet = $objPHPExcel->getActiveSheet();

    /*设置宽度*/
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);


    /* sheet标题 */
    $objActSheet->setTitle($sheetTitle);
    $j = 'A';
    foreach ($firstrow as $value) {
        $objActSheet->setCellValue($j . '1', $value);
        $j++;
    }

    $i = 2;
    foreach ($data as $value) {
        /* excel文件内容 */
        $j = 'A';
        foreach ($value as $value2) {
            //            $value2=iconv("gbk","utf-8",$value2);
            $objActSheet->setCellValue($j . $i, $value2);
            $j++;
        }
        $i++;
    }


    /* 生成到浏览器，提供下载 */
    ob_end_clean();  //清空缓存
    header("Pragma: public");
    header("Expires: 0");
    header('Content-Type: application/vnd.ms-excel;charset=gbk');
    header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
    header("Content-Type:application/force-download");
    header("Content-Type:application/vnd.ms-execl");
    header("Content-Type:application/octet-stream");
    header("Content-Type:application/download");
    header('Content-Disposition:attachment;filename="' . $excelFileName . '.csv"');
    header("Content-Transfer-Encoding:binary");
    $csvWriter->save('php://output');
}

function exportCSVFile($data, $excelFileName, $firstrow) {
    $content = "";

    foreach ($firstrow as $value) {
        $content .= $value . ',';
    }
    substr($content, 0, count($content) - 1);
    $content .= "\r\n";

    foreach ($data as $row) {
        foreach ($row as $value) {
            $content .= $value . ',';
        }
        substr($content, 0, count($content) - 1);

        $content .= "\r\n";
    }

    $file = APP_PATH . '../Public/export/' . $excelFileName . ".csv";
    $re   = file_put_contents($file, $content);
    if ($re) {
        header("Location: http://www.luobojianzhi.com/Public/export/".$excelFileName.".csv");
    }
}

function CalculationValidate(){
     session_start();
      $w=100;
      $h=30;
      $img = imagecreate($w,$h);
      $gray = imagecolorallocate($img,255,255,255);
      $black = imagecolorallocate($img,rand(0,200),rand(0,200),rand(0,200));
      $red = imagecolorallocate($img, 255, 0, 0);
      $white = imagecolorallocate($img, 255, 255, 255);
      $green = imagecolorallocate($img, 0, 255, 0);
      $blue = imagecolorallocate($img, 0, 0, 255);
      imagefilledrectangle($img, 0, 0, $w, $h, $black);


      for($i = 0;$i < 80;$i++){
        imagesetpixel($img, rand(0,$w), rand(0,$h), $gray);
      }

      $num1 = rand(1,99);
      $num2 = rand(1,99);
      $res=0;
      $code=rand(0,1);
       switch ($code) {
        case 0:
          $res=$num1+$num2;
          $symbol="+";
          break;
        case 1:
          $res=$num1-$num2;
          $symbol="-";
          break;
        default:
          # code...
          break;
        }
      imagestring($img, 5, 5, rand(1,10), $num1, $red);
      imagestring($img,5,30,rand(1,10),$symbol, $white);
      imagestring($img,5,45,rand(1,10),$num2, $green);
      imagestring($img,5,65,rand(1,10),"=", $blue);
      imagestring($img,5,80,rand(1,10),"?", $red);

      $_SESSION['res']=$res;

      header("content-type:image/png");
      imagepng($img);
      imagedestroy($img);
}


//获取月份的最后一天
function getMonthLastDay($year, $month){
    $t = mktime(0, 0, 0, $month + 1, 1, $year);
    $t = $t - 60 * 60 * 24;
    return $t;
}


function request_get($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_URL, $url);

    $res = curl_exec($curl);
    curl_close($curl);

    return $res;
}

/**
 * 发送post请求
 * @param string $url
 * @param string $param
 * @return bool|mixed
 */
function request_post($url = '', $param = '') {
    if (empty($url) || empty($param)) {
        return false;
    }
    $postUrl = $url;
    $curlPost = $param;
    $ch = curl_init(); //初始化curl
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSLVERSION, 1);
    curl_setopt($ch, CURLOPT_URL, $postUrl); //抓取指定网页
    curl_setopt($ch, CURLOPT_HEADER, 0); //设置header
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_POST, 1); //post提交方式
    curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
    $data = curl_exec($ch); //运行curl
    //$error =curl_error($ch);
    curl_close($ch);
    return $data;
}


function json_fail($data, $msg="fail") {
    $result = array();
    $result['code'] =0;
    $result['msg'] = $msg;
    $result['data'] = $data;
    return $result;
}

function json_success($data, $msg="success") {
    $result = array();
    $result['code'] = 1;
    $result['msg'] = $msg;
    $result['data'] = $data;
    return $result;
}

/**
 * 系统加密方法
 * @param string $data 要加密的字符串
 * @param string $key  加密密钥
 * @param int $expire  过期时间 单位 秒
 * @return string
 */
function think_encrypt($data, $key = '', $expire = 0) {
    $key  = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $data = base64_encode($data);
    $x    = 0;
    $len  = strlen($data);
    $l    = strlen($key);
    $char = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    $str = sprintf('%010d', $expire ? $expire + time():0);

    for ($i = 0; $i < $len; $i++) {
        $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1)))%256);
    }
    return str_replace(array('+','/','='),array('-','_',''),base64_encode($str));
}

/**
 * 系统解密方法
 * @param  string $data 要解密的字符串 （必须是think_encrypt方法加密的字符串）
 * @param  string $key  加密密钥
 * @return string
 */
function think_decrypt($data, $key = '') {
    $key    = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $data   = str_replace(array('-','_'),array('+','/'),$data);
    $mod4   = strlen($data) % 4;
    if ($mod4) {
        $data .= substr('====', $mod4);
    }
    $data   = base64_decode($data);
    $expire = substr($data,0,10);
    $data   = substr($data,10);

    if($expire > 0 && $expire < time()) {
        return '';
    }
    $x      = 0;
    $len    = strlen($data);
    $l      = strlen($key);
    $char   = $str = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    for ($i = 0; $i < $len; $i++) {
        if (ord(substr($data, $i, 1))<ord(substr($char, $i, 1))) {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        }else{
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }
    return base64_decode($str);
}

/**
 * 判断电话号码是否合法
 * @param $phone_mob
 */
function checkMobile($phone_mob) {
    $pattern = '/^1[34578][0-9]{9}$/';
    if (!preg_match($pattern, $phone_mob)) {
        return false;
    }
    return true;
}

/**
 * 第三方支付拼装信息
 * @param $pickupUrl 付款结果跳转地址
 * @param $receiveUrl 付款回调地址
 * @param $merchantId 商户号
 * @param $payerName 付款人
 * @param $payerTelephone 付款人联系方式
 * @param $orderNo 订单编号
 * @param $orderAmount 订单金额
 * @param $orderDatetime 订单时间
 * @param $productName 商品名称
 */
function thirdPay ($pickupUrl, $receiveUrl, $merchantId, $orderNo, $orderAmount, $orderDatetime, $productName) {
    $bufSignSrc=""; //签名字符串
    $inputCharset = 1; //字符集
    $version = "v1.0"; //网关接口版本
    $language = 1; //语言
    $signType = 1; //签名类型
    $orderCurrency = 156; //币种
    $payType = 0; //支付类型
    $key = "1234567890";
    $orderDatetime = date('YmdHis',strtotime($orderDatetime));
    if($inputCharset != "") {
        $bufSignSrc=$bufSignSrc."inputCharset=".$inputCharset;
    }
    if ($pickupUrl != "") {
        $bufSignSrc=$bufSignSrc."&pickupUrl=".$pickupUrl;
    }
    $bufSignSrc=$bufSignSrc."&receiveUrl=".$receiveUrl.
                "&version=".$version."&language=".$language."&signType=".$signType."&merchantId=".$merchantId.
                "&orderNo=".$orderNo."&orderAmount=".$orderAmount. "&orderCurrency=".$orderCurrency."&orderDatetime=".
                $orderDatetime."&productName=".$productName."&payType=".$payType."&key=".$key;
    $signMsg = strtoupper(md5($bufSignSrc));

    $data = array("inputCharset" => $inputCharset,
                  "pickupUrl" => $pickupUrl,
                  "receiveUrl" => $receiveUrl,
                  "version" => $version,
                  "language" => $language,
                  "signType" => $signType,
                  "merchantId" => $merchantId,
                  "orderNo" => $orderNo,
                  "orderAmount" => $orderAmount,
                  "orderCurrency" => $orderCurrency,
                  "orderDatetime" => $orderDatetime,
                  "productName" => $productName,
                  "payType" => $payType,
                  "key" => $key,
                  "signMsg" => $signMsg
        );

    return $data;

}

/******************************************/

/*
* Functions that are meant to be used by the user of this PHP module.
*
* Notes:
* - $key and $modulus should be numbers in (decimal) string format
* - $message is expected to be binary data
* - $keylength should be a multiple of 8, and should be in bits
* - For rsa_encrypt/rsa_sign, the length of $message should not exceed
* ($keylength / 8) - 11 (as mandated by [4]).
* - rsa_encrypt and rsa_sign will automatically add padding to the message.
* For rsa_encrypt, this padding will consist of random values; for rsa_sign,
* padding will consist of the appropriate number of 0xFF values (see [4])
* - rsa_decrypt and rsa_verify will automatically remove message padding.
* - Blocks for decoding (rsa_decrypt, rsa_verify) should be exactly
* ($keylength / 8) bytes long.
* - rsa_encrypt and rsa_verify expect a public key; rsa_decrypt and rsa_sign
* expect a private key.
*/

/*
 * johnwinner  modify list
 *  1)suport digest method in rsa_sign and rsa_sign (sha1 or md5 )
 *  2)rsa_sign return signature in base64 format
 *  3)modify rsa_verify define
 *    support source documnet and base64 signatire input, boolean output
 *
 * all above base on Edsko de Vries' work
 *
 */

/**
 *
 * @param $message     // $message is expected to be binary data
 * @param $public_key  // $modulus should be numbers in (decimal) string format
 * @param $modulus     // $modulus should be numbers in (decimal) string format
 * @param $keylength   // int
 * @return  $result     // result is binary data
 */
function rsa_encrypt($message, $public_key, $modulus, $keylength)
{
    $padded = add_PKCS1_padding($message, true, $keylength / 8);
    $number = binary_to_number($padded);
    $encrypted = pow_mod($number, $public_key, $modulus);
    $result = number_to_binary($encrypted, $keylength / 8);
    return $result;

}

/**
 *
 * @param $message     // $message is expected to be binary data
 * @param $private_key // $modulus should be numbers in (decimal) string format
 * @param $modulus     // $modulus should be numbers in (decimal) string format
 * @param $keylength   // int
 */
function rsa_decrypt($message, $private_key, $modulus, $keylength)
{
    $number = binary_to_number($message);
    $decrypted = pow_mod($number, $private_key, $modulus);
    $result = number_to_binary($decrypted, $keylength / 8);
    return remove_PKCS1_padding($result, $keylength / 8);
}

/**
 *
 * @param $message     // $message is expected to be binary data
 * @param $private_key // $modulus should be numbers in (decimal) string format
 * @param $modulus     // $modulus should be numbers in (decimal) string format
 * @param $keylength   // int
 * @param $hash_func   // name of hash function, which will be used during signing
 * @return $result     // signature String in Base64 format
 */
function rsa_sign($message, $private_key, $modulus, $keylength,$hash_func)
{
    //only suport sha1 or md5 digest now
    if (!function_exists($hash_func) && (strcmp($hash_func ,'sha1') == 0 || strcmp($hash_func,'md5') == 0))
        return false;
    $mssage_digest_info_hex = $hash_func($message);
    $mssage_digest_info_bin = hexTobin($mssage_digest_info_hex);
    $padded = add_PKCS1_padding($mssage_digest_info_bin, false, $keylength / 8);
    $number = binary_to_number($padded);
    $signed = pow_mod($number, $private_key, $modulus);
    $result = base64_encode($signed);
    return $result;
}

/**
 *
 * @param $message     // $message is expected to be binary data
 * @param $private_key // $modulus should be numbers in (decimal) string format
 * @param $modulus     // $modulus should be numbers in (decimal) string format
 * @param $keylength   // int
 * @param $hash_func   // name of hash function, which will be used during signing
 * @return boolean     // true or false
 */
function rsa_verify($document, $signature, $public_key, $modulus, $keylength,$hash_func)
{
    //only suport sha1 or md5 digest now
    if (!function_exists($hash_func) && (strcmp($hash_func ,'sha1') == 0 || strcmp($hash_func,'md5') == 0))
        return false;
    $document_digest_info = $hash_func($document);

    $number    = binary_to_number(base64_decode($signature));
    $decrypted = pow_mod($number, $public_key, $modulus);
    $decrypted_bytes    = number_to_binary($decrypted, $keylength / 8);
    if($hash_func == "sha1" )
    {
        $result = remove_PKCS1_padding_sha1($decrypted_bytes, $keylength / 8);
    }
    else
    {
        $result = remove_PKCS1_padding_md5($decrypted_bytes, $keylength / 8);
    }
    return(hexTobin($document_digest_info) == $result);
}


/*
* Some constants
*/


define("BCCOMP_LARGER", 1);


/*
* The actual implementation.
* Requires BCMath support in PHP (compile with --enable-bcmath)
*/


//--
// Calculate (p ^ q) mod r
//
// We need some trickery to [2]:
// (a) Avoid calculating (p ^ q) before (p ^ q) mod r, because for typical RSA
// applications, (p ^ q) is going to be _WAY_ too large.
// (I mean, __WAY__ too large - won't fit in your computer's memory.)
// (b) Still be reasonably efficient.
//
// We assume p, q and r are all positive, and that r is non-zero.
//
// Note that the more simple algorithm of multiplying $p by itself $q times, and
// applying "mod $r" at every step is also valid, but is O($q), whereas this
// algorithm is O(log $q). Big difference.
//
// As far as I can see, the algorithm I use is optimal; there is no redundancy
// in the calculation of the partial results.
//--

function pow_mod($p, $q, $r)
{
    // Extract powers of 2 from $q
    $factors = array();
    $div = $q;
    $power_of_two = 0;
    while(bccomp($div, "0") == BCCOMP_LARGER)
    {
        $rem = bcmod($div, 2);
        $div = bcdiv($div, 2);

        if($rem) array_push($factors, $power_of_two);
        $power_of_two++;
    }

    // Calculate partial results for each factor, using each partial result as a
    // starting point for the next. This depends of the factors of two being
    // generated in increasing order.

    $partial_results = array();
    $part_res = $p;
    $idx = 0;

    foreach($factors as $factor)
    {
        while($idx < $factor)
        {
            $part_res = bcpow($part_res, "2");
            $part_res = bcmod($part_res, $r);
            $idx++;
        }
        array_push($partial_results, $part_res);
    }

    // Calculate final result
    $result = "1";
    foreach($partial_results as $part_res)
    {
        $result = bcmul($result, $part_res);
        $result = bcmod($result, $r);
    }
    return $result;
}

//--
// Function to add padding to a decrypted string
// We need to know if this is a private or a public key operation [4]
//--

function add_PKCS1_padding($data, $isPublicKey, $blocksize)
{
    $pad_length = $blocksize - 3 - strlen($data);
    if($isPublicKey)
    {
        $block_type = "\x02";
        $padding = "";
        for($i = 0; $i < $pad_length; $i++)
        {
            $rnd = mt_rand(1, 255);
            $padding .= chr($rnd);
        }
    }
    else
    {
        $block_type = "\x01";
        $padding = str_repeat("\xFF", $pad_length);
    }

    return "\x00" . $block_type . $padding . "\x00" . $data;
}

//--
// Remove padding from a decrypted string
// See [4] for more details.
//--

function remove_PKCS1_padding($data, $blocksize)
{
    //assert(strlen($data) == $blocksize);
    $data = substr($data, 1);

    // We cannot deal with block type 0
    if($data{0} == '\0')
        die("Block type 0 not implemented.");

    // Then the block type must be 1 or 2
    //assert(($data{0} == "\x01") || ($data{0} == "\x02"));

    // Remove the padding
    $offset = strpos($data, "\0", 1);
    return substr($data, $offset + 1);
}

function remove_PKCS1_padding_sha1($data, $blocksize) {
    $digestinfo = remove_PKCS1_padding($data, $blocksize);
    $digestinfo_length = strlen($digestinfo);
    //sha1 digestinfo length not less than 20
    //assert($digestinfo_length >= 20);

    return substr($digestinfo, $digestinfo_length-20);
}

function remove_PKCS1_padding_md5($data, $blocksize) {
    $digestinfo = remove_PKCS1_padding($data, $blocksize);
    $digestinfo_length = strlen($digestinfo);
    //md5 digestinfo length not less than 16
    //assert($digestinfo_length >= 16);

    return substr($digestinfo, $digestinfo_length-16);
}

//--
// Convert binary data to a decimal number
//--

function binary_to_number($data)
{
    $base = "256";
    $radix = "1";
    $result = "0";

    for($i = strlen($data) - 1; $i >= 0; $i--)
    {
        $digit = ord($data{$i});
        $part_res = bcmul($digit, $radix);
        $result = bcadd($result, $part_res);
        $radix = bcmul($radix, $base);
    }
    return $result;
}

//--
// Convert a number back into binary form
//--
function number_to_binary($number, $blocksize)
{
    $base = "256";
    $result = "";
    $div = $number;
    while($div > 0)
    {
        $mod = bcmod($div, $base);
        $div = bcdiv($div, $base);
        $result = chr($mod) . $result;
    }
    return str_pad($result, $blocksize, "\x00", STR_PAD_LEFT);
}
//
//Convert hexadecimal format data into  binary
//
function hexTobin($data) {
    $len = strlen($data);
    $newdata='';
    for($i=0;$i<$len;$i+=2) {
        $newdata .= pack("C",hexdec(substr($data,$i,2)));
    }
    return $newdata;
}

/**
 * 发送邮件方法
 * @param $to
 * @param $name
 * @param string $subject
 * @param string $body
 * @param null $attachment
 * @return bool|string
 */
function think_send_mail($to, $name, $subject = '', $body = '', $attachment = null){


    $config = C('THINK_EMAIL');

    //从PHPMailer目录导class.phpmailer.php类文件
    Vendor('PHPMailerAutoload', VENDOR_PATH . 'PHPMailer/');

    //PHPMailer对象
    $mail = new PHPMailer();

    //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
    $mail->CharSet    = 'UTF-8';

    // 设定使用SMTP服务
    $mail->IsSMTP();

    // 关闭SMTP调试功能
    // 1 = errors and messages
    // 2 = messages only
    $mail->SMTPDebug  = 0;

    // 启用 SMTP 验证功能
    $mail->SMTPAuth   = true;

    // 使用安全协议
    $mail->SMTPSecure = 'ssl';

    // SMTP 服务器
    $mail->Host       = $config['SMTP_HOST'];

    // SMTP服务器的端口号
    $mail->Port       = $config['SMTP_PORT'];

    // SMTP服务器用户名
    $mail->Username   = $config['SMTP_USER'];

    // SMTP服务器密码
    $mail->Password   = $config['SMTP_PASS'];

    //发件人，发件人名称
    $mail->SetFrom($config['FROM_EMAIL'], $config['FROM_NAME']);

    //回复EMAIL
    $replyEmail       = $config['REPLY_EMAIL']?$config['REPLY_EMAIL']:$config['FROM_EMAIL'];

    //回复名称
    $replyName        = $config['REPLY_NAME']?$config['REPLY_NAME']:$config['FROM_NAME'];

    $mail->AddReplyTo($replyEmail, $replyName);

    //主题
    $mail->Subject    = $subject;

    //内容
    $mail->MsgHTML($body);

    //发送邮件
    $mail->AddAddress($to,$name);

    if(is_array($attachment)){ // 添加附件
        foreach ($attachment as $file){
            is_file($file) && $mail->AddAttachment($file);
        }
    }

    return $mail->Send() ? true : $mail->ErrorInfo;
}
/******************************************/
