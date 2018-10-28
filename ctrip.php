<?php  
	// 设置起始页面 id
	$id = 1600101;
	// 设置抓取页面个数
	$num = 10000;
	// 记录有效抓取数
	$count = 0;
	for($i = 0; $i < $num; $i++){
		$id = $id + $i;
		$url = "http://hotels.ctrip.com/hotel/$id.html?isFull=F#ctm_ref=hod_sr_lst_dl_i_1_1";
	    $infos = cUrl($url);
	    if ($infos[0] == null){
			echo "$id : name 数据为空</br>";
		}if ($infos[1] == null){
			echo "$id : tel 数据为空</br>";
		}else{
		    echo $id;
		    database($infos,$id);
		    $count ++;
		}
	}
	$tatio = $count / $num;
	echo "本次访问 $num 个页面，共计抓取有效数据 $count, 效率为 $tatio";

  	
    function cUrl($url){
	    $ch = curl_init();   
	    curl_setopt($ch,CURLOPT_HEADER,0);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    $result = curl_exec($ch);
	    $content = curl_multi_getcontent($ch);
	    curl_close($ch);
	    preg_match('/(?<=itemprop="name">).*?(?=<)/m', $content, $name);
	    $name = preg_replace('/<.*?>/m', '', $name[0]);
	    preg_match('/(?<=data-real="电话).*(?=<a)/m', $content, $tel);
	    $tel = preg_replace('/<.*?>/m', '', $tel[0]);
	    $tel = str_replace('&nbsp;', '', $tel);
	    $tel = preg_replace('/ 传真.*/m','',$tel);
	    $infos = array($name,$tel);
	    return $infos;
    }

    function database($infos, $id){
    	$host = '127.0.0.1';
		$user = 'root';
		$password = '';
		$db = 'test';
		$charset = 'utf8';
		if(!$conn = mysqli_connect($host, $user, $password)){
			die('连接数据库失败 : '.mysqli_error());
		}
		if(!mysqli_set_charset($conn, $charset)){
			die('设置字符集失败 : '.mysqli_error());
		}
		if(!mysqli_select_db($conn, $db)){
			die('查询数据库失败 : '.mysqli_error());
		}
		$sql = "insert into hotels(name, tel, source_id) values('$infos[0]', '$infos[1]', $id)";
		// $db -> set_charset('utf8');
		$r = mysqli_query($conn ,$sql);
		if($r){
			echo "执行成功</br>";
		}else{
			echo "执行失败</br>";
		}
		mysqli_close($conn);
    }
?>