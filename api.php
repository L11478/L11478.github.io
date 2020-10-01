<?php
//API功能

$mod='blank';
$nosecu=true;
include("./includes/common.php");

if($_GET['my']=='siderr') {
	$qq=daddslashes($_GET['qq']);
	$skey=daddslashes($_GET['skey']);
	$err=daddslashes($_GET['err']);
	if($err=='skey')
	$sql="update `".DBQZ."_qq` set `status` ='0' where `qq`='$qq' and `skey`='$skey'";
	elseif($err=='superkey')
	$sql="update `".DBQZ."_qq` set `status2` ='0' where `qq`='$qq' and `skey`='$skey'";
	$sds=$DB->query($sql);
	if($sds)exit('0');
	else exit('-1');
}elseif($_GET['my']=='siteinfo') {
	$qqs=$DB->count("SELECT count(*) from ".DBQZ."_qq WHERE 1");
	$qqjobs=$DB->count("SELECT count(*) from ".DBQZ."_qqjob WHERE 1");
	$signjobs=$DB->count("SELECT count(*) from ".DBQZ."_signjob WHERE 1");
	$wzjobs=$DB->count("SELECT count(*) from ".DBQZ."_wzjob WHERE 1");
	$users=$DB->count("SELECT count(*) from ".DBQZ."_user WHERE 1");
	$zongs=$qqjobs+$signjobs+$wzjobs;
	if(function_exists("sys_getloadavg"))
		$fz=sys_getloadavg();
	else
		$fz=null;
	$siteinfo=array('name'=>$conf['sitename'],'version'=>VERSION,'users'=>$users,'zongs'=>$zongs,'qqs'=>$qqs,'times'=>$info['times'],'last'=>$info['last'],'now'=>$date,'fz'=>$fz,'app_version'=>$conf['app_version'],'app_log'=>$conf['app_log'],'app_start_is'=>$conf['app_start_is'],'app_start'=>$conf['app_start']);
	echo json_encode($siteinfo);
	exit;
}elseif($_GET['my']=='coininfo') {
	$siteinfo=array('coin_name'=>$conf['coin_name'],'coin_swich'=>$conf['jifen'],'rules_2'=>$rules[2],'rules_3'=>$rules[3],'rules_4'=>$rules[4],'rules_5'=>$rules[5],'rules_6'=>$rules[6],'vipmode'=>$conf['vipmode'],'vip_coin'=>$conf['vip_coin'],'vip_func'=>$conf['vip_func'],'coin_tovip'=>$conf['coin_tovip'],'peie_open'=>$conf['peie_open']);
	echo json_encode($siteinfo);
	exit;
}elseif($_GET['my']=='gg'){
	header("content-Type: text/html; charset=utf-8");
	$gg=$conf['gg'];
	echo $gg;
}elseif($_GET['my']=='shop'){
	header("content-Type: text/html; charset=utf-8");
	$shop=$conf['shop'];
	echo $shop;
}elseif($_GET['my']=='daigua'){
	header("content-Type: text/html; charset=utf-8");
	$qqlevel=$conf['qqlevel'];
	echo $qqlevel;
}elseif($_GET['my']=='client') {
	if($islogin==1){
		$act=daddslashes($_GET['act']);
		if($act=='syslist'){
			$result['code']=0;
			$result['count']=$conf['server_wz'];
			$show=explode('|',$conf['show']);
			for($i=1;$i<=$conf['server_wz'];$i++){
				$all_sys=$DB->count("SELECT count(*) from ".DBQZ."_wzjob WHERE sysid='$i'");
				$my_sys=$DB->count("SELECT count(*) from ".DBQZ."_wzjob WHERE sysid='$i' and uid='$uid'");
				$result['data'][]=array('id'=>$i,'all'=>$all_sys,'my'=>$my_sys,'max'=>$conf['max'],'pl'=>$show[($i-1)]);
			}
			echo json_encode($result);
		}elseif($act=='user'){
			$result=array('userid'=>$row['userid'],'user'=>$row['user'],'qqnum'=>$row['qqnum'],'qqjobnum'=>$row['qqjobnum'],'signjobnum'=>$row['signjobnum'],'wzjobnum'=>$row['wzjobnum'],'regdate'=>$row['date'],'lastdate'=>$row['last'],'regip'=>$row['zcip'],'lastip'=>$row['dlip'],'qq'=>$row['qq'],'email'=>$row['email'],'phone'=>$row['phone'],'coin'=>$row['coin'],'peie'=>$row['peie'],'vip'=>$row['vip'],'vipdate'=>$row['vipdate'],'mail_on'=>$row['mail_on'],'daili'=>$row['daili'],'daili_rmb'=>$row['daili_rmb']);
			echo json_encode($result);
		}elseif($act=='rw'){
			$jobid=isset($_GET['jobid'])?daddslashes($_GET['jobid']):null;
			if($_GET['type']=='qqtask'){
				$row=$DB->get_row("SELECT *FROM ".DBQZ."_qqjob where jobid='{$jobid}' and uid='{$uid}' limit 1");
				$qqjob=qqjob_decode($row['qq'],$row['type'],$row['method'],$row['data']);
				$row['url']=$qqjob['url'];
			}elseif($_GET['type']=='signtask'){
				$row=$DB->get_row("SELECT *FROM ".DBQZ."_signjob where jobid='{$jobid}' and uid='{$uid}' limit 1");
				$signjob=signjob_decode($row['type'],$row['data']);
				$row['url']=$signjob['url'];
			}elseif($_GET['type']=='wztask'){
				$row=$DB->get_row("SELECT *FROM ".DBQZ."_wzjob where jobid='{$jobid}' and uid='{$uid}' limit 1");
			}
			echo json_encode($row);
		}elseif($act=='list'){
			if($_GET['type']=='qqtask'){
				$qq=isset($_GET['qq'])?daddslashes($_GET['qq']):null;
				$table='qqjob';
				if($qq)$sql=" and qq='{$qq}'";
			}elseif($_GET['type']=='signtask'){
				$table='signjob';
			}elseif($_GET['type']=='wztask'){
				$table='wzjob';
			}
			$gls=$DB->count("SELECT count(*) from ".DBQZ."_{$table} WHERE uid='{$uid}'{$sql}");
			$pagesize=$conf['pagesize'];
			if (!isset($_GET['page'])) {
				$page = 1;
				$pageu = $page - 1;
			} else {
				$page = $_GET['page'];
				$pageu = ($page - 1) * $pagesize;
			}
			$s = ceil($gls / $pagesize);
			$result['code']=0;
			$result['count']=$gls;
			$result['page']=$page;
			$result['pagesize']=$pagesize;
			$result['pages']=$s;
			$rs=$DB->query("SELECT * FROM ".DBQZ."_{$table} WHERE uid='{$uid}'{$sql} order by jobid desc limit $pageu,$pagesize");
			while($myrow = $DB->fetch($rs))
			{
				if($_GET['type']=='qqtask'){
					$qqjob=qqjob_decode($myrow['qq'],$myrow['type'],$myrow['method'],$myrow['data']);
					$myrow['url']=$qqjob['url'];
					unset($qqjob);
				}elseif($_GET['type']=='signtask'){
					$signjob=signjob_decode($myrow['type'],$myrow['data']);
					$myrow['url']=$signjob['url'];
					unset($signjob);
				}
				$result['data'][]=$myrow;
			}
			echo json_encode($result);
		}elseif($act=='qqlist'){
			$pagesize=$conf['pagesize'];
			if (!isset($_GET['page'])) {
				$page = 1;
				$pageu = $page - 1;
			} else {
				$page = $_GET['page'];
				$pageu = ($page - 1) * $pagesize;
			}
			$gls=$DB->count("SELECT count(*) from ".DBQZ."_qq WHERE uid='{$uid}'");
			$s = ceil($gls / $pagesize);
			$gxsid=$DB->count("SELECT count(*) from ".DBQZ."_qq WHERE status2!='1' and uid='{$uid}'");
			$result['code']=0;
			$result['count']=$gls;
			$result['page']=$page;
			$result['pagesize']=$pagesize;
			$result['pages']=$s;
			$result['gxsid']=$gxsid;
			$rs=$DB->query("SELECT * FROM ".DBQZ."_qq WHERE uid='{$uid}' order by id desc limit $pageu,$pagesize");
			while($myrow = $DB->fetch($rs))
			{
				$myrow['pw']=authcode($myrow['pw'],'DECODE',SYS_KEY);
				$result['data'][]=$myrow;
			}
			echo json_encode($result);
		}elseif($act=='chat'){
			$pagesize=intval($_GET['pagesize']);
			if (!isset($_GET['page'])) {
				$page = 1;
				$pageu = $page - 1;
			} else {
				$page = intval($_GET['page']);
				$pageu = ($page - 1) * $pagesize;
			}
			$gls=$DB->count("SELECT count(*) from ".DBQZ."_chat WHERE 1");
			$s = ceil($gls / $pagesize);
			$result['code']=0;
			$result['count']=$gls;
			$result['page']=$page;
			$result['pagesize']=$pagesize;
			$result['pages']=$s;
			$rs=$DB->query("SELECT * FROM ".DBQZ."_chat WHERE 1 order by id desc limit $pageu,$pagesize");
			while($myrow = $DB->fetch($rs))
			{
			if($myrow['user']==$gl)
			$myrow['user']='我';
			if($myrow['to']==$gl)
			$myrow['to']='我';
			$myrow['nr']=strip_tags($myrow['nr']);
				$result['data'][]=$myrow;
			}
			echo json_encode($result);
		}else{
			$result['code']=-1;
			$result['error']='无效请求';
			echo json_encode($result);
		}
	}else{
		$result['code']=-4;
		$result['error']='登录失败，可能是密码错误或者身份失效了';
		echo json_encode($result);
	}
	exit;
}
?>