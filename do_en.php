<?php
	include "lib/header.php";

	date_default_timezone_set('Asia/Shanghai');
	$showtime=date("Y-m-d");

	$formPostData = array(
		'authType'	=>	$_POST['authtype'],
		'playscene'	=>	$_POST['playscene'],
		'validDate'	=>	$_POST['valid_dt'],
		'videoName'	=>	$_POST['titlecn'],
		'authPurpose'=> $_POST['purpose'],
		'period'=> rand($_POST['timeinterval']-$_POST['randominterval'],$_POST['timeinterval']+$_POST['randominterval']),
		'time'=> rand($_POST['displaytime']-$_POST['displayrandom'],$_POST['displaytime']+$_POST['displayrandom']),
		'authFormat'=> $_POST['format']
	);
	//print_r($formPostData['authType']);
	//print_r($formPostData['playscene']);
	//print_r($formPostData);
	//print_r($_POST['authType']);

	//echo "</br>";
	//print_r($formPostData['time']);
	//exit;
	//echo "</br>";
	$formPostData['period']=round($formPostData['period']/1000);
	$formPostData['time']=round($formPostData['time']/1000);
	//echo $formPostData['period'];
	//echo "</br>";
	//echo $formPostData['time'];
	//exit;


	$dateArray = explode('-',$showtime);
	$dataYear = substr($dateArray[0], 2, 2);

	$sql = "SELECT `id` FROM `video` WHERE title_cn='{$formPostData['videoName']}'";
	$result = mysql_query($sql);
	$result_row = mysql_fetch_row($result);
	
	$videoId = $result_row[0];

	$videoName = glob('video/'.$videoId.'.*');//该函数返回一个包含有匹配文件 / 目录的数组。如果出错返回 false。
	$validPath = '/var/www/chvec_auth/'.$videoName[0];

	$sql = "SELECT `dur` FROM `video` WHERE title_cn='{$formPostData['videoName']}'";
	$result = mysql_query($sql);
	$result_row = mysql_fetch_row($result);
	$videoDur = $result_row[0];


	$sql = "SELECT COUNT(*) FROM `authen`";
	$result = mysql_query($sql);
	$result_row = mysql_fetch_row($result);
	//print_r($result_row);exit;
	$validCode = 'C'.$dataYear.$dateArray[1].$formPostData['authType'].sprintf("%05d", $result_row[0]+1);

	$sql = "INSERT INTO `authen` (`type`, `code`, `valid_dt`,`purpose`,`video_id`,`title_cn`) VALUES ('{$formPostData['authType']}', '$validCode', '{$formPostData['validDate']}','{$formPostData['authPurpose']}','$videoId','{$formPostData['videoName']}')";
	if (!mysql_query($sql)) {
		die('Could not insert data' . mysql_error());
	}
	$format=$formPostData['authFormat'];
	//print_r($validCode);
	//print_r($formPostData['validDate']);
	//print_r($formPostData['authPurpose']);
	//print_r($validPath);
    	//print_r($format);
    	//print_r("$validCode" . '.'."$format");
	//exit; 
	if ($formPostData['authType']=="F") {
		exec("/var/www/chvec_auth/auth_free.sh $validCode {$formPostData['validDate']} $videoId '{$formPostData['authPurpose']}' $validPath {$formPostData['authFormat']} {$formPostData['period']} {$formPostData['time']} > /dev/null & ");
		} elseif ($formPostData['authType']=="P"&& $formPostData['playscene']=="cinema"){	
		exec("/var/www/chvec_auth/auth_p_cinema.sh $validCode {$formPostData['validDate']} $videoId '{$formPostData['authPurpose']}' $validPath {$formPostData['authFormat']} {$formPostData['period']} {$formPostData['time']} > /dev/null & ");
		} elseif ($formPostData['authType']=="P"){
		exec("/var/www/chvec_auth/auth_p_person.sh $validCode {$formPostData['validDate']} $videoId '{$formPostData['authPurpose']}' $validPath {$formPostData['authFormat']} {$formPostData['period']} {$formPostData['time']} > /dev/null & ");
	}

	exec("/var/www/chvec_auth/all_en.sh $validCode {$formPostData['validDate']} $authId '{$formPostData['authPurpose']}' $validPath {$formPostData['authFormat']} > /dev/null & ");
?>
<style type="text/css">
	.authBody p {
		font-family:Microsoft Hei;
	}
	.authInfo {
		margin-top:81px;
	}
	.authBody a {
		position: absolute;
		right: 273px;
		bottom: 121px;
	}
	#authButton {
		right: 441px;
		bottom: 121px;
	}
</style>
<div align="center">
	<img src="img/logo2.jpg" />
</div>
<div class="authBody" style="position:relative;">
	<h1 style="font-size:22.5px">ChinaVEC 微视频授权拷贝生成器 VideoAuth 1.0.1
</h1>
	<div class="authInfo">
		<p>
			您所授权的视频：<?php echo $validPath;?>&nbsp;&nbsp;&nbsp;视频总时长：<?php echo $videoDur.'s';?>
		</p>
		<p>
			授权号：<?php echo $validCode;?>
		</p>
		<p>
			视频有效期：<?php echo $formPostData['validDate'];?>
		</p>
		
	</div>
	<!--a id="backToIndex" class="btn btn-primary">返回</a-->
	<a id="authButton" class="btn btn-danger"><img src="img/loading.gif" width="12" style="margin-right:5px;" />正在授权</a>
</div>
<?php

$filesize =  filesize("$validPath");
$size 	  =  intval(floor($filesize/1048576));
$differ	  =  $videoDur-$size;
if((abs($differ)<=130&&$size>=100)||$size>=1024){
	$ti =$videoDur;
	$tim=intval(floor($ti/0.3));
}
else if(abs($differ)>130){
	$ti =intval(floor($videoDur/2));
	$tim=intval(floor($ti/0.3));
}
//$tim=intval(floor($ti/0.3));
//exit;
/*
						$("#authButton").addClass("btn btn-success").html('点击下载').attr('href', 'fdown.php?name=' + "<?php echo $validCode;?>" + '.' + "<?php echo $format;?>").css({"margin-top":"48px","margin-left":"320px","float":"left"});
						*/
?>

<script type="text/javascript" src="js/jquery-1.9.1.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.10.3.custom.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script type="text/javascript">
window.onload=function(){
	   var idiv=document.getElementById('iLoading');
	   var ibox=document.getElementById('ibox');
	   var timer=null;

	   timer=setInterval(function(){
	           var iWidth=idiv.offsetWidth/ibox.offsetWidth*100;
	           idiv.style.width=idiv.offsetWidth+1+'px';//灰色长度增1px；
			   idiv.innerHTML=Math.round(iWidth)+"%";//数字%；
			   if(iWidth==100){
	              clearInterval(timer);	
	     	    }
			},"<?php echo $tim;?>");
}
</script>
<script type="text/javascript">
jQuery(function($){
	var Timer1 = setInterval(function(){
		$.post(
			'file_exist.php',
			{
				'fileName'	: "<?php echo "$validCode" . '.' . "$format";?>"
			},
			function (data) {
				if (data == "") {
					clearInterval(Timer1);

					setTimeout(function () {
						$.post('jiami.php',{'fileName'	: "<?php echo "$validCode" . '.' . "$format";?>"});		
						$("#ibox").remove();
						$("#iLoading").remove();
						$("#authButton").remove();
						$('<a style="right:550px;"/>').addClass("btn btn-success").attr('href', 'fdown.php?name=' + "<?php echo $validCode;?>" + '.' + "<?php echo $format;?>").text('点击下载').appendTo("#authvideodown");
						
						$("#backToIndex").attr('href', 'index.php');
						$('<a style="right:300px;"/>').addClass("btn btn-primary").attr('href', 'index_en.php').text('返回').appendTo(".authBody #back");
					}, 10000);
				};
			}
		);
	}, 15000);
});
</script>
<?php include 'lib/footer.php'; ?>
