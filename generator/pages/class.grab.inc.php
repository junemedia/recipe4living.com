<?php // This file is protected by copyright law and provided under license. Reverse engineering of this file is strictly prohibited.












 























































































$IFSAv82671814sBUJu=134076019;$dfqZu30519714zoykw=939544159;$XWdpE40066833uaPZJ=946983002;$xZexh12875671zLztR=811986298;$ajNLh75508728cSKHh=690647797;$FeaKy39528503wCKfu=239561249;$hlqID31497497mUBUV=613820404;$GypyC42978210TrciN=471019012;$XOPgv20533142TBIJq=966250824;$UhFFV45724792gxPAs=757109589;$mAgSc65115662BtraK=998689057;$WFcwc70268250zoFPn=348582977;$gwqRN97745057AgvLs=960885102;$GBdtY49108581uYMAU=494189178;$EniKa50921326QGqey=103588958;$UNJpH94745789GNWOe=444678192;$WxCRS37144470lSrIh=674550629;$fyVTu49679871RQIaq=449800018;$beTaI78914490oPDKr=925520112;$eYoEB26410827HmYiJ=759304657;$YLcfe18731384OYWWG=107247406;$iJOfn47438660YiIfm=623942108;$lYIPg59095154CDbsI=467482513;$DKPuN45263367aJqrQ=293462372;$WMQHY42505798lTSxX=257975433;$sDnjP42384949jQNKr=17615448;$FufJh81463318HoVBF=727476166;$VfgCA61303406WLGmx=46151336;$gjlCm18467712SFMjY=127734710;$ZxzxK34518738nTIYJ=628820038;$jUWWT56018982PKhQE=706501068;$xsNVR74530945fzZaa=17371551;$XnmFJ36617126RkMwC=715525238;$HcaGU23840027gzPNm=459555878;$Qraqj72762146KQveC=404557220;$lAGpu84945984OGdLc=207123016;$aOzcs96954041xASDF=23347015;$MCbza10348815PZeFa=508822968;$IQGkh41692810SQbRA=820644623;$aSOUE92548523UCyQR=615405731;$ISAKh19478454dKWEf=49200042;$EZIpV84045105LyHtf=776621308;$YXDoY52810974eJdwz=955763276;$AfIfF97338562oJjGD=243219696;$FNCGW74190369dFrhz=793084320;$ndCka64928894feDAa=263950897;$vhcDf16116638aoqnQ=809913178;$PkKlX99316102dfKWi=89564910;$GrRWi81089783vyvVe=256999847;$KkgnU43000183mKltm=968811738;?><?php class SiteCrawler { var $ZztXH8WWNrek2ntV = array(); var $LnCUNQ5wpr0KnV5A = false; var $wV4tMRGfV4NR = false; var $KvZMdEwl9vk7 = array(); var $GgZ_Thchlxe = ''; var $bT1vStOWyX = ''; var $BQ9LlruW7onUt1lb = ''; var $CMQaGmG6s9sCBhxq = ''; var $kgaNSprOIRzQYIY = ''; function KDdCtJPGUBu9Wq($M_05edZvR){ 
return preg_replace('#^www\.#', '', $M_05edZvR);
} function J0guAv8GXYn6AN38fxM(&$a, $GNYoUcb8ZrZah5U4D4, $gRB7sSEZuG07T2B, $c8hXHDB_uRoIpsh, $bde7EoOvSY07vuMwIoK, $KSpBBj6usrOqMsPdHh = '') { global $grab_parameters; $GyxaqkNpn = parse_url($bde7EoOvSY07vuMwIoK); if($GyxaqkNpn['scheme'] && substr($a, 0, 2) == '//') 
																												 $a = $GyxaqkNpn['scheme'].':'.$a; $RrdMupUEihGpaCVq = @parse_url($a); if($RrdMupUEihGpaCVq['scheme'] && ($RrdMupUEihGpaCVq['scheme']!='http')&& ($RrdMupUEihGpaCVq['scheme']!='https')) { $vGg1894D_y7Zt5Q1M = 1; }else { $a = str_replace(':80/', '/', $a); if($a[0]=='?')$a = preg_replace('#^([^\?]*?)([^/\?]*?)(\?.*)?$#','$2',$GNYoUcb8ZrZah5U4D4).$a; if($grab_parameters['xs_inc_ajax'] && strstr($a,'#!')){ $c8hXHDB_uRoIpsh = preg_replace('#\#.*$#', '', $c8hXHDB_uRoIpsh); if($a[0] != '/' && !strstr($a,':/')) $a = $c8hXHDB_uRoIpsh . preg_replace('#^([^\#]*?/)?([^/\#]*)?(\#.*)?$#', '$2', $GNYoUcb8ZrZah5U4D4).$a; } if(preg_match('#^https?(:|&\#58;)#is',$a)){ if(preg_match('#://[^/]*$#is',$a)) 
																												 $a .= '/'; } else if($a&&$a[0]=='/')$a = $gRB7sSEZuG07T2B.$a; else $a = $c8hXHDB_uRoIpsh.$a; if($a[0]=='/')$a = $gRB7sSEZuG07T2B.$a; $a=str_replace('/./','/',$a); $a=preg_replace('#/\.$#','/',$a); if(substr($a,-2) == '..')$a.='/'; if(strstr($a,'../')){ preg_match('#(.*?:.*?//.*?)(/.*)$#',$a,$aa); 
																												 do{ $ap = $aa[2]; $aa[2] = preg_replace('#/?[^/]*/\.\.#','',$ap,1); }while($aa[2]!=$ap); $a = $aa[1].$aa[2]; } $a = preg_replace('#/\./#','/',$a); $a = str_replace('&#38;','&',$a); $a = str_replace('&#038;','&',$a); $a = str_replace('&amp;','&',$a); $a = preg_replace('#([^&])\#'.($grab_parameters['xs_inc_ajax']?'[^\!]':'').'.*$#','$01',$a); $a = preg_replace('#^([^\?]*[^/\:]/)/+#','\\1',$a); $a = preg_replace('#[\r\n]+#s','',$a); $vGg1894D_y7Zt5Q1M = (strtolower(substr($a,0,strlen($bde7EoOvSY07vuMwIoK)) ) != strtolower($bde7EoOvSY07vuMwIoK)) ? 1 : 0; if($grab_parameters['xs_cleanurls']) $a = @preg_replace($grab_parameters['xs_cleanurls'],'',$a); if($grab_parameters['xs_cleanpar']) { do { $NoWmBUTFvN7kzhyn0o = $a; $a = @preg_replace('#[\\?\\&]('.$grab_parameters['xs_cleanpar'].')=[a-z0-9\-\.\_\=\/]+$#i','',$a); $a = @preg_replace('#([\\?\\&])('.$grab_parameters['xs_cleanpar'].')=[a-z0-9\-\.\_\=\/]+&#i','$1',$a); }while($a != $NoWmBUTFvN7kzhyn0o); $a = @preg_replace('#\?\&?$#','',$a); } 

if($vGg1894D_y7Zt5Q1M && $grab_parameters['xs_allow_subdomains']){ 
if($RrdMupUEihGpaCVq['host'] && 
//($this->KDdCtJPGUBu9Wq($GyxaqkNpn['host']) == $this->KDdCtJPGUBu9Wq($RrdMupUEihGpaCVq['host'])) )
strstr($RrdMupUEihGpaCVq['host'], $this->KDdCtJPGUBu9Wq($GyxaqkNpn['host']))
)

{ $vGg1894D_y7Zt5Q1M = 2; } } 
if($vGg1894D_y7Zt5Q1M && $KSpBBj6usrOqMsPdHh) { $SJVuJqcL0 = $this->cDdos7BIlJ($KSpBBj6usrOqMsPdHh); if($SJVuJqcL0 && preg_match('#('.$SJVuJqcL0.')#', $a)) $vGg1894D_y7Zt5Q1M = 2; } } DFJUj5XaZVoZf("<br/>($a - $vGg1894D_y7Zt5Q1M - $GNYoUcb8ZrZah5U4D4 - $c8hXHDB_uRoIpsh - $gRB7sSEZuG07T2B)<br>\n",3); return $vGg1894D_y7Zt5Q1M; } function cDdos7BIlJ($Jvs41YxAHtvXH8qzP8c){ if(!isset($this->ZztXH8WWNrek2ntV[$Jvs41YxAHtvXH8qzP8c])){ $this->ZztXH8WWNrek2ntV[$Jvs41YxAHtvXH8qzP8c] = trim($Jvs41YxAHtvXH8qzP8c) ? preg_replace("#\s*[\r\n]+\s*#",'|', (strstr($s=trim($Jvs41YxAHtvXH8qzP8c),'*')?$s:preg_quote($s,'#'))) : ''; } return $this->ZztXH8WWNrek2ntV[$Jvs41YxAHtvXH8qzP8c]; } function IIcbczWAX09NsrG(&$GNYoUcb8ZrZah5U4D4) { global $grab_parameters; if(isset($this->KvZMdEwl9vk7[$GNYoUcb8ZrZah5U4D4])) $GNYoUcb8ZrZah5U4D4 =$this->KvZMdEwl9vk7[$GNYoUcb8ZrZah5U4D4]; $f = $this->LnCUNQ5wpr0KnV5A && preg_match('#'.$grab_parameters['xs_exc_skip'].'#i',$GNYoUcb8ZrZah5U4D4); if($this->GgZ_Thchlxe&&!$f)$f=$f||@preg_match('#('.$this->GgZ_Thchlxe.')#',$GNYoUcb8ZrZah5U4D4); if($this->bT1vStOWyX && $f && $grab_parameters['xs_incl_force']) $f = !preg_match('#('.$this->bT1vStOWyX.')#',$GNYoUcb8ZrZah5U4D4); if($this->BQ9LlruW7onUt1lb&&!$f) foreach($this->BQ9LlruW7onUt1lb as $bm) { $f = $f || preg_match('#^('.$bm.')#', $this->CMQaGmG6s9sCBhxq . $GNYoUcb8ZrZah5U4D4); } $f2 = false; $LJci6KwBdfosP9p = false; if(!$f) { $f2 = $this->wV4tMRGfV4NR && preg_match('#'.$grab_parameters['xs_inc_skip'].'#i',$GNYoUcb8ZrZah5U4D4); if($this->bT1vStOWyX && !$f2) $f2 = $f2||(preg_match('#('.$this->bT1vStOWyX.')#',$GNYoUcb8ZrZah5U4D4)); if($grab_parameters['xs_parse_only'] && !$f2 && $GNYoUcb8ZrZah5U4D4!='/') { $f2 = $f2 || !preg_match('#'.str_replace(' ', '|', preg_quote($grab_parameters['xs_parse_only'],'#')).'#',$GNYoUcb8ZrZah5U4D4); } } return array('f' => $f, 'f2' => $f2);	 } function G2zCcihGLQl($Jg7UKn8jH9hzZ,&$urls_completed) { global $grab_parameters,$DSZ7onyKhTY; error_reporting(E_ALL&~E_NOTICE); @set_time_limit($grab_parameters['xs_exec_time']); if($Jg7UKn8jH9hzZ['bgexec']) { ignore_user_abort(true); } register_shutdown_function('YlnYTfdc4'); if(function_exists('ini_set')) { @ini_set("zlib.output_compression", 0); @ini_set("output_buffering", 0); } $PoOrXmr19YxJ2d = explode(" ",microtime()); $PK1tEvgTTp = $PoOrXmr19YxJ2d[0]+$PoOrXmr19YxJ2d[1]; $starttime = $FkLeMvYAd = time(); $MHgpHZ_Ll2Hbw15PI = $nettime = 0; $pZraQah5aJyN = $Jg7UKn8jH9hzZ['initurl']; $pez0n43nSLB = $Jg7UKn8jH9hzZ['maxpg']>0 ? $Jg7UKn8jH9hzZ['maxpg'] : 1E10; $uc66akk2U0X = $Jg7UKn8jH9hzZ['maxdepth'] ? $Jg7UKn8jH9hzZ['maxdepth'] : -1; $a72fjXmL4zzgMw = $Jg7UKn8jH9hzZ['progress_callback']; $this->GgZ_Thchlxe = $this->cDdos7BIlJ($grab_parameters['xs_excl_urls']); $this->bT1vStOWyX = $this->cDdos7BIlJ($grab_parameters['xs_incl_urls']); $tBvUUp_KNPkYQ04FY1 = $this->cDdos7BIlJ($grab_parameters['xs_prev_sm_incl']); $pE0deENVttSkb8Hz = $tqkt5joCT_wZgAl3z = array(); $I4xiiY8U2FMQjtAUzNB = preg_split('#[\r\n]+#', $grab_parameters['xs_ind_attr']); $ZVU_UotF9_jJp = '#200'.($grab_parameters['xs_allow_httpcode']?'|'.$grab_parameters['xs_allow_httpcode']:'').'#'; if($grab_parameters['xs_memsave']) { if(!file_exists(q0PNLQD52dm6SKSyg)) mkdir(q0PNLQD52dm6SKSyg, 0777); else if($Jg7UKn8jH9hzZ['resume']=='') vzNS8L4hV0RzWs(q0PNLQD52dm6SKSyg, '.txt'); } foreach($I4xiiY8U2FMQjtAUzNB as $ia) if($ia) { $is = explode(',', $ia); if($is[0][0]=='$') $fxUurVOagqgj = substr($is[0], 1); else $fxUurVOagqgj = str_replace(array('\\^', '\\$'), array('^','$'), preg_quote($is[0],'#')); $tqkt5joCT_wZgAl3z[] = $fxUurVOagqgj; $pE0deENVttSkb8Hz[str_replace(array('^','$'),array('',''),$is[0])] =  array('lm' => $is[1], 'f' => $is[2], 'p' => $is[3]); } if($tqkt5joCT_wZgAl3z) $aQ4ElPDMjZqlA6eve = implode('|',$tqkt5joCT_wZgAl3z); $ApWk9cNV4bVZkvPX = parse_url($pZraQah5aJyN); if(!$ApWk9cNV4bVZkvPX['path']){$pZraQah5aJyN.='/';$ApWk9cNV4bVZkvPX = parse_url($pZraQah5aJyN);} if($grab_parameters['xs_moreurls']){ $mu = preg_split('#[\r\n]+#', $grab_parameters['xs_moreurls']); foreach($mu as $mi=>$Ojt_PrWS5FBkqlSXPi7){ $Ojt_PrWS5FBkqlSXPi7 = str_replace($bde7EoOvSY07vuMwIoK, '', $Ojt_PrWS5FBkqlSXPi7); $BkrUa2m_ZoFXT = $DSZ7onyKhTY->fetch($Ojt_PrWS5FBkqlSXPi7,0,true); if($mi>3)break; } } $BkrUa2m_ZoFXT = $DSZ7onyKhTY->fetch($pZraQah5aJyN,0,true);// the first request is to skip session id 
																												 $VHkEbcZzGkWKeYH = !preg_match($ZVU_UotF9_jJp,$BkrUa2m_ZoFXT['code']); if($VHkEbcZzGkWKeYH) { $VHkEbcZzGkWKeYH = ''; foreach($BkrUa2m_ZoFXT['headers'] as $k=>$v) $VHkEbcZzGkWKeYH .= $k.': '.$v.'<br />'; return array( 'errmsg'=>'<b>There was an error while retrieving the URL specified:</b> '.$pZraQah5aJyN.''. ($BkrUa2m_ZoFXT['errormsg']?'<br><b>Error message:</b> '.$BkrUa2m_ZoFXT['errormsg']:''). '<br><b>HTTP Code:</b><br>'.$BkrUa2m_ZoFXT['protoline']. '<br><b>HTTP headers:</b><br>'.$VHkEbcZzGkWKeYH. '<br><b>HTTP output:</b><br>'.$BkrUa2m_ZoFXT['content'] , ); } $pZraQah5aJyN = $BkrUa2m_ZoFXT['last_url']; $urls_completed = array(); $urls_ext = array(); $urls_404 = array(); $gRB7sSEZuG07T2B = $ApWk9cNV4bVZkvPX['scheme'].'://'.$ApWk9cNV4bVZkvPX['host'].((!$ApWk9cNV4bVZkvPX['port'] || ($ApWk9cNV4bVZkvPX['port']=='80'))?'':(':'.$ApWk9cNV4bVZkvPX['port'])); 
																												 $pn = $tsize = $retrno = $I9twBTcW88N = $B3jrMeixYaK91I = $fetch_no = 0; $bde7EoOvSY07vuMwIoK = a_swOP2hskJvhpi2o($gRB7sSEZuG07T2B.'/', gjEB17v5UJj1SxRZ($ApWk9cNV4bVZkvPX['path'])); $Y6Sv9w6omZ = parse_url($bde7EoOvSY07vuMwIoK); $this->CMQaGmG6s9sCBhxq = preg_replace('#^.+://[^/]+#', '', $bde7EoOvSY07vuMwIoK); 
																												 $S0BtLzEJVxMK_RX = $DSZ7onyKhTY->fetch($pZraQah5aJyN,0,true,true); $PJcOGM6Tph4 = str_replace($bde7EoOvSY07vuMwIoK,'',$pZraQah5aJyN); $urls_list_full = array($PJcOGM6Tph4=>1); if(!$PJcOGM6Tph4)$PJcOGM6Tph4=''; $urls_list = array($PJcOGM6Tph4=>1); $urls_list2 = $urls_list_skipped = array(); $this->KvZMdEwl9vk7 = array(); $links_level = 0; $NqLd4rkoF6vij = $ref_links = $ref_links2 = array(); $XATMoSEjrtJR2AWn9u = 0; $CWUHIna3EKRbOn4D4cw = $pez0n43nSLB; if(!$grab_parameters['xs_progupdate'])$grab_parameters['xs_progupdate'] = 20; if(isset($grab_parameters['xs_robotstxt']) && $grab_parameters['xs_robotstxt']) { $rdWiXt4nD8 = $DSZ7onyKhTY->fetch($gRB7sSEZuG07T2B.'/robots.txt'); if($gRB7sSEZuG07T2B.'/' != $bde7EoOvSY07vuMwIoK) { $bmx3aq4xi = $DSZ7onyKhTY->fetch($bde7EoOvSY07vuMwIoK.'robots.txt'); $rdWiXt4nD8['content']  .= "\n".$bmx3aq4xi['content']; } $ra=preg_split('#user-agent:\s*#im',$rdWiXt4nD8['content']); $GIeI0ZRU_=array(); for($i=1;$i<count($ra);$i++){ preg_match('#^(\S+)(.*)$#s',$ra[$i],$Kxk2n3T4kfA); if($Kxk2n3T4kfA[1]=='*'||strstr($Kxk2n3T4kfA[1],'google')){ preg_match_all('#^disallow:\s*(\S*)#im',$Kxk2n3T4kfA[2],$rm); for($pi=0;$pi<count($rm[1]);$pi++) if($rm[1][$pi]) $GIeI0ZRU_[] =  str_replace('\\$','$', str_replace('\\*','.*', preg_quote($rm[1][$pi],'#') )); } } for($i=0;$i<count($GIeI0ZRU_);$i+=200) $this->BQ9LlruW7onUt1lb[]=implode('|', array_slice($GIeI0ZRU_, $i,200)); }else $this->BQ9LlruW7onUt1lb = array(); if($grab_parameters['xs_inc_ajax']) $grab_parameters['xs_proto_skip'] = str_replace( '\#', '\#[^\!]', $grab_parameters['xs_proto_skip']); $this->LnCUNQ5wpr0KnV5A = $grab_parameters['xs_exc_skip']!='\\.()'; $this->wV4tMRGfV4NR = $grab_parameters['xs_inc_skip']!='\\.()'; $grab_parameters['xs_inc_skip'] .= '$'; $grab_parameters['xs_exc_skip'] .= '$'; if($grab_parameters['xs_debug']) { $_GET['ddbg']=1; duBMhqfH7kGKH(); } $Btu_OfIkMLszVD = 0; $runstate = array(); $url_ind = 0; $cnu = 1; $pf = Hqm42kdaBr(dh6mwOEumX3JD.Og4KRtb1cdnHxZIO89,'w');fclose($pf); $aWjpeZhY_qaCTTKmvy = false; if($Jg7UKn8jH9hzZ['resume']!=''){ $FVrvczhkoCdSfmCKoGm = @HT7yKXImq(WyXkTyAK3kSMA(dh6mwOEumX3JD.eYgPj3ZHK0T12hAy, true)); if($FVrvczhkoCdSfmCKoGm) { $aWjpeZhY_qaCTTKmvy = true; echo 'Resuming the last session (last updated: '.date('Y-m-d H:i:s',$FVrvczhkoCdSfmCKoGm['time']).')'."\n"; extract($FVrvczhkoCdSfmCKoGm); $PK1tEvgTTp-=$ctime; $Btu_OfIkMLszVD = $ctime; unset($FVrvczhkoCdSfmCKoGm); } } $mHUAcr4HLXxz7 = 0; if(!$aWjpeZhY_qaCTTKmvy){ if($grab_parameters['xs_moreurls']){ $mu = preg_split('#[\r\n]+#', $grab_parameters['xs_moreurls']); foreach($mu as $Ojt_PrWS5FBkqlSXPi7){ $vGg1894D_y7Zt5Q1M = $this->J0guAv8GXYn6AN38fxM($Ojt_PrWS5FBkqlSXPi7, $GNYoUcb8ZrZah5U4D4, $gRB7sSEZuG07T2B, $c8hXHDB_uRoIpsh, $bde7EoOvSY07vuMwIoK); if($vGg1894D_y7Zt5Q1M != 1) $urls_list[$Ojt_PrWS5FBkqlSXPi7]++; } } if($grab_parameters['xs_prev_sm_base']){ if($sm_base = @WyXkTyAK3kSMA(dh6mwOEumX3JD.'sm_base.db',true)){ $sm_base = @unserialize($sm_base); } if(is_array($sm_base) && ($grab_parameters['xs_prev_sm_base_min']<count($sm_base)) ){ foreach($sm_base as $_u=>$_e) $urls_list[$_u]++; } else $sm_base = array(); } $mHUAcr4HLXxz7 = count($urls_list); $urls_list_full = $urls_list; $cnu = count($urls_list); } $O4zX7h7zYXsnn0 = explode('|', $grab_parameters['xs_force_inc']); $PAMsFEW9PB2RQoc3 = $GE6qcpPfzdWGB = array(); $Zl5g2LLgy3 = count($urls_completed); $h9EmToufL4h9OSGalBd = count($urls_list2); sleep(1); @tV12hsJy_(dh6mwOEumX3JD.Og4KRtb1cdnHxZIO89); if($urls_list) do { u3Aj3kpP8f7NX('pre',true); u3Aj3kpP8f7NX('pre1'); if($PAMsFEW9PB2RQoc3) { $_ul = array_shift($PAMsFEW9PB2RQoc3); }else $_ul = each($urls_list); list($GNYoUcb8ZrZah5U4D4, $e_ksHZ4mvyyb643a5V) = $_ul; $pMUfxUHOM = ($e_ksHZ4mvyyb643a5V>0 && $e_ksHZ4mvyyb643a5V<1) ? $e_ksHZ4mvyyb643a5V : 0; $url_ind++; DFJUj5XaZVoZf("\n[ $url_ind - $GNYoUcb8ZrZah5U4D4, $e_ksHZ4mvyyb643a5V] \n"); unset($urls_list[$GNYoUcb8ZrZah5U4D4]); $XSgHgDDEhTbwopK_PT = g9mWWYIteSa7dD7($GNYoUcb8ZrZah5U4D4); $sSxD4RNwg = false; $t3jvxqdXKF32_lYX = ''; u3Aj3kpP8f7NX('pre1',true); u3Aj3kpP8f7NX('pre2a'); $BkrUa2m_ZoFXT = array(); $cn = ''; $_fex = $this->IIcbczWAX09NsrG($GNYoUcb8ZrZah5U4D4); extract($_fex); u3Aj3kpP8f7NX('pre2a',true); u3Aj3kpP8f7NX('pre2b'); if(!$f && ($Zl5g2LLgy3>0) && ($LJci6KwBdfosP9p = $sm_base[$GNYoUcb8ZrZah5U4D4])){ $f2 = true; } u3Aj3kpP8f7NX('pre2b',true); do{ $iTLXwB9Qu0cTUSeI6Tl = count($urls_list) + $h9EmToufL4h9OSGalBd + $Zl5g2LLgy3;       $f3 = $O4zX7h7zYXsnn0[2] && ( ($CWUHIna3EKRbOn4D4cw*$O4zX7h7zYXsnn0[2]+1000)< ($dk5zQNTVIkQTHep1-$url_ind-$mHUAcr4HLXxz7)); if(!$f && !$f2) { $naPDZDyR2SqyxQvp = ($O4zX7h7zYXsnn0[1] &&  ( (($ctime>$O4zX7h7zYXsnn0[0]) && ($pn>$pez0n43nSLB*$O4zX7h7zYXsnn0[1])) || $f3));	 $V8LkdUr76kWKRNwEM8b = ($O4zX7h7zYXsnn0[3] && $pez0n43nSLB && (($iTLXwB9Qu0cTUSeI6Tl>$pez0n43nSLB*$O4zX7h7zYXsnn0[3]))); if($O4zX7h7zYXsnn0[3] && $pez0n43nSLB && (($pn>$pez0n43nSLB*$O4zX7h7zYXsnn0[3]))){ $urls_list = $urls_list2 = array(); $h9EmToufL4h9OSGalBd = 0; $cnu = 0; } if($uc66akk2U0X<=0 || $links_level<$uc66akk2U0X) { u3Aj3kpP8f7NX('extract'); $WuOtxQB2cfkjS = microtime(true); $EE3JSA0ZP1y1fXkP = a_swOP2hskJvhpi2o($bde7EoOvSY07vuMwIoK, $GNYoUcb8ZrZah5U4D4); if(DOqagMEXq2('xs_http_parallel')){ if(!$PAMsFEW9PB2RQoc3 && !isset($DSZ7onyKhTY->oJ5unwFQhNolkKRYs[$EE3JSA0ZP1y1fXkP])){ $PAMsFEW9PB2RQoc3 = array(); $GE6qcpPfzdWGB = array($EE3JSA0ZP1y1fXkP); $_par = DOqagMEXq2('xs_http_parallel_num', 10); for($i=0;($i<$_par*5)&&(count($GE6qcpPfzdWGB)<$_par);$i++) if($_ul = each($urls_list)) { $PAMsFEW9PB2RQoc3[] = $_ul; $_fex2 = $this->IIcbczWAX09NsrG($_ul[0]); if(!$_fex2['f'] && !$_fex2['f2']){ $_u1 = a_swOP2hskJvhpi2o($bde7EoOvSY07vuMwIoK, $_ul[0]); if(!isset($sm_base[$_u1])){ $GE6qcpPfzdWGB[] = $_u1; } } } $DSZ7onyKhTY->CUx3ZI0WzO($GE6qcpPfzdWGB); } } DFJUj5XaZVoZf("<h4> { $EE3JSA0ZP1y1fXkP } </h4>\n"); $uq2qs0IfE0=0; $I9twBTcW88N++; do { $BkrUa2m_ZoFXT = $DSZ7onyKhTY->fetch($EE3JSA0ZP1y1fXkP, 0, 0); $_to = $BkrUa2m_ZoFXT['flags']['socket_timeout']; if($_to && ($Y6Sv9w6omZ['host']!=$BkrUa2m_ZoFXT['purl']['host'])){ $BkrUa2m_ZoFXT['flags']['error'] = 'Host doesn\'t match'; } $_ic = intval($BkrUa2m_ZoFXT['code']); $fMiTRrdjIW = ($_ic == 400); $MDb4AagIsVSS6R = ($_ic == 403); $KfP5GDVb6joxvc6jc = (($_ic == 301)||($_ic==302)) && ($EE3JSA0ZP1y1fXkP == $BkrUa2m_ZoFXT['last_url']); if( !$BkrUa2m_ZoFXT['flags']['error'] &&  (($fMiTRrdjIW||$MDb4AagIsVSS6R||$KfP5GDVb6joxvc6jc) || !$BkrUa2m_ZoFXT['code'] || $_to) ) { $uq2qs0IfE0++; $sl = $grab_parameters['xs_delay_ms']?$grab_parameters['xs_delay_ms']:1; if(($_to) && $grab_parameters['xs_timeout_break']){ DFJUj5XaZVoZf("<p> # TIMEOUT - $_to #</p>\n"); if($uq2qs0IfE0==3){ if(strstr($_to,'read') ){ DFJUj5XaZVoZf("<p> read200 break?</p>\n"); break ; } if($B3jrMeixYaK91I++>5) { $VP8vAJd3fs3x1Ygo = "Too many timeouts detected"; break 2; } DFJUj5XaZVoZf("<p> # MULTI TIMEOUT - BREAK #</p>\n"); $sl = 60;	    			 $uq2qs0IfE0 = 0; } } DFJUj5XaZVoZf("<p> # RETRY - ".$BkrUa2m_ZoFXT['code']." - ".(intval($BkrUa2m_ZoFXT['code']))." - ".$BkrUa2m_ZoFXT['flags']['error']."#</p>\n"); sleep($_sl); } else  break; }while($uq2qs0IfE0<3); $fetch_no++; u3Aj3kpP8f7NX('extract', true); u3Aj3kpP8f7NX('analyze'); $HQHtKgSdG = microtime(true)-$WuOtxQB2cfkjS; $nettime += $HQHtKgSdG; DFJUj5XaZVoZf("<hr>\n[[[ ".$BkrUa2m_ZoFXT['code']." ]]] - ".number_format($HQHtKgSdG,2)."s (".number_format($DSZ7onyKhTY->Uem3Rs_JAbbzZ,2).' + '.number_format($DSZ7onyKhTY->Sjfk6GpUe,2).")\n".var_export($BkrUa2m_ZoFXT['headers'],1)); $lv3sjQRQ1U1 = is_array($BkrUa2m_ZoFXT['headers']) ? strtolower($BkrUa2m_ZoFXT['headers']['content-type']) : ''; $fGFFoS728V_xDAVeL = strstr($lv3sjQRQ1U1,'text/html') || strstr($lv3sjQRQ1U1,'/xhtml') || !$lv3sjQRQ1U1; if($lv3sjQRQ1U1 && !$fGFFoS728V_xDAVeL && (!$grab_parameters['xs_parse_swf'] || !strstr($lv3sjQRQ1U1, 'shockwave-flash')) ){ if(!$naPDZDyR2SqyxQvp){ $t3jvxqdXKF32_lYX = $lv3sjQRQ1U1; continue; } } $EFK5jcimGXb = array(); if($BkrUa2m_ZoFXT['code']==404 || ($grab_parameters['xs_force_404'] && preg_match('#'.implode('|',preg_split('#\s+#',$grab_parameters['xs_force_404'])).'#', $GNYoUcb8ZrZah5U4D4) ) ){ if($links_level>0) if(!$grab_parameters['xs_chlog_list_max'] || count($urls_404) < $grab_parameters['xs_chlog_list_max']) { $urls_404[]=array($GNYoUcb8ZrZah5U4D4,$ref_links2[$GNYoUcb8ZrZah5U4D4]); } } $cn = $BkrUa2m_ZoFXT['content']; $tsize+=strlen($cn); if($grab_parameters['xs_canonical']) if(($EE3JSA0ZP1y1fXkP == $BkrUa2m_ZoFXT['last_url']) && preg_match('#<link[^>]*rel="canonical"[^>]href="([^>]*?)"#is', $cn, $PBCaMycHB)){ $BkrUa2m_ZoFXT['last_url'] = trim($PBCaMycHB[1]); } if($BkrUa2m_ZoFXT['last_url']){ $vGg1894D_y7Zt5Q1M = $this->J0guAv8GXYn6AN38fxM($BkrUa2m_ZoFXT['last_url'], $GNYoUcb8ZrZah5U4D4, $gRB7sSEZuG07T2B, $c8hXHDB_uRoIpsh, $bde7EoOvSY07vuMwIoK); if($vGg1894D_y7Zt5Q1M == 1){ $t3jvxqdXKF32_lYX = 'lu'; continue; } } $r9vJFlZw_t = preg_replace('#^.*?'.preg_quote($bde7EoOvSY07vuMwIoK,'#').'#','',$BkrUa2m_ZoFXT['last_url']); if(($EE3JSA0ZP1y1fXkP != $BkrUa2m_ZoFXT['last_url']))// && ($EE3JSA0ZP1y1fXkP != $BkrUa2m_ZoFXT['last_url'].'/'))  
																												 { $this->KvZMdEwl9vk7[$GNYoUcb8ZrZah5U4D4]=$BkrUa2m_ZoFXT['last_url']; $io=$GNYoUcb8ZrZah5U4D4; if(strlen($r9vJFlZw_t) <= 2048) if(!isset($urls_list_full[$r9vJFlZw_t])) { $urls_list2[$r9vJFlZw_t]++; if(count($ref_links[$r9vJFlZw_t])<max(1,intval($grab_parameters['xs_maxref']))) $ref_links[$r9vJFlZw_t][] = $GNYoUcb8ZrZah5U4D4; } $t3jvxqdXKF32_lYX = 'lu - '.$BkrUa2m_ZoFXT['last_url']; if(!$naPDZDyR2SqyxQvp)continue; } if($ZVU_UotF9_jJp && !preg_match($ZVU_UotF9_jJp,$BkrUa2m_ZoFXT['code'])){ $t3jvxqdXKF32_lYX = $BkrUa2m_ZoFXT['code']; continue; } $retrno++; if($gf8uLIiBMWIqzFaD0 = preg_replace('#<!--(\[if IE\]>|.*?-->)#is', '',$cn)) $cn = $gf8uLIiBMWIqzFaD0; preg_match('#<base[^>]*?href=[\'"](.*?)[\'"]#is',$cn,$bm); if(isset($bm[1])&&$bm[1]) $c8hXHDB_uRoIpsh = gjEB17v5UJj1SxRZ($bm[1].(preg_match('#//.*/#',$bm[1])?'-':'/-')); 
																												 else $c8hXHDB_uRoIpsh = gjEB17v5UJj1SxRZ($bde7EoOvSY07vuMwIoK.$GNYoUcb8ZrZah5U4D4); if($naPDZDyR2SqyxQvp||$V8LkdUr76kWKRNwEM8b) { $fGFFoS728V_xDAVeL = false; } u3Aj3kpP8f7NX('analyze',true); if(strstr($lv3sjQRQ1U1, 'shockwave-flash') && $grab_parameters['xs_parse_swf']) { include_once kH_x88NZpV8q.'class.pfile.inc.php'; $am = new SWFParser(); $am->uW2s6L508AR($cn); $ubgR29rFB = $am->Tsr1dxbRSl36XZQSmy2(); }else if($fGFFoS728V_xDAVeL) { u3Aj3kpP8f7NX('parse'); $GkN2xe_LS5 = $grab_parameters['xs_utf8_enc'] ? 'isu':'is'; preg_match_all('#<(?:a|area|go|link)\s(?:[^>]*?\s)?href\s*=\s*(?:"([^"]*)|\'([^\']*)|([^\s\"\\\\>]+)).*?>#is'.$GkN2xe_LS5, $cn, $am);
																												
																												
																												preg_match_all('#<i?frame\s[^>]*?src\s*=\s*["\']?(.*?)("|>|\')#is', $cn, $ZwgAtu5BO_NYpaRWr);
																												
																												preg_match_all('#<meta\s[^>]*http-equiv\s*=\s*"?refresh[^>]*URL\s*=\s*["\']?(.*?)("|>|\'[>\s])#'.$GkN2xe_LS5, $cn, $ZDRxZwYGC);
																												
																												if($grab_parameters['xs_parse_swf'])
																												
																												preg_match_all('#<object[^>]*application/x-shockwave-flash[^>]*data\s*=\s*["\']([^"\'>]+).*?>#'.$GkN2xe_LS5, $cn, $ubgR29rFB);
																												
																												else $ubgR29rFB = array(array(),array());
																												
																												
																												$EFK5jcimGXb = array();
																												
																												for($i=0;$i<count($am[1]);$i++)
																												
																												{
																												
																												if( !preg_match('#rel=["\']?(nofollow|stylesheet)#i', $am[0][$i]) ) 
																												
																												$EFK5jcimGXb[] = $am[1][$i];
																												
																												}
																												
																												$EFK5jcimGXb = @array_merge(
																												
																												$EFK5jcimGXb,
																												
																												
																												$am[2],$am[3],  
																												
																												$ZwgAtu5BO_NYpaRWr[1],$ZDRxZwYGC[1],
																												
																												$ubgR29rFB[1]);
																												
																												}
																												
																												$EFK5jcimGXb = array_unique($EFK5jcimGXb);
																												
																												
																												
																												$nn = $nt = 0;
																												
																												reset($EFK5jcimGXb);
																												
																												if(preg_match('#<meta\s*name=[\'"]robots[\'"]\s*content=[\'"][^\'"]*?nofollow#is',$cn))
																												
																												$EFK5jcimGXb = array();
																												
																												if(!$runstate['charset']){
																												
																												if(preg_match('#<meta\s+http-equiv="content-type"[^>]*?charset=([^">]*)"#is',$cn, $oHXaaeWlrm8J5445))
																												
																												$runstate['charset'] = $oHXaaeWlrm8J5445[1];
																												
																												}
																												
																												u3Aj3kpP8f7NX('parse', true);
																												
																												u3Aj3kpP8f7NX('llist');
																												
																												foreach($EFK5jcimGXb as $i=>$ll)
																												
																												if($ll)
																												
																												{                    
																												
																												$a = $sa = trim($ll);
																												
																												
																												if($grab_parameters['xs_proto_skip'] && 
																												
																												(preg_match('#^'.$grab_parameters['xs_proto_skip'].'#i',$a)||
																												
																												($this->LnCUNQ5wpr0KnV5A && preg_match('#'.$grab_parameters['xs_exc_skip'].'#i',$a))||
																												
																												preg_match('#^'.$grab_parameters['xs_proto_skip'].'#i',function_exists('html_entity_decode')?html_entity_decode($a):$a)
																												
																												))
																												
																												continue;
																												
																												
																												if(strlen($a) > 4096) continue;
																												
																												$vGg1894D_y7Zt5Q1M = $this->J0guAv8GXYn6AN38fxM($a, $GNYoUcb8ZrZah5U4D4, $gRB7sSEZuG07T2B, $c8hXHDB_uRoIpsh, $bde7EoOvSY07vuMwIoK);
																												
																												if($vGg1894D_y7Zt5Q1M == 1)
																												
																												{
																												
																												if($grab_parameters['xs_extlinks'] &&
																												
																												(!$grab_parameters['xs_extlinks_excl'] || !preg_match('#'.$this->cDdos7BIlJ($grab_parameters['xs_extlinks_excl']).'#',$a)) &&
																												
																												(!$grab_parameters['xs_ext_max'] || (count($urls_ext)<$grab_parameters['xs_ext_max']))
																												
																												)
																												
																												{
																												
																												if(!$urls_ext[$a] && 
																												
																												(!$grab_parameters['xs_ext_skip'] || 
																												
																												!preg_match('#'.$grab_parameters['xs_ext_skip'].'#',$a)
																												
																												)
																												
																												)
																												
																												$urls_ext[$a] = $EE3JSA0ZP1y1fXkP;
																												
																												}
																												
																												continue;
																												
																												}
																												
																												$r9vJFlZw_t = $vGg1894D_y7Zt5Q1M ? $a : substr($a,strlen($bde7EoOvSY07vuMwIoK));
																												
																												$r9vJFlZw_t = str_replace(' ', '%20', $r9vJFlZw_t);
																												
																												if($urls_list_full[$r9vJFlZw_t] || ($r9vJFlZw_t == $GNYoUcb8ZrZah5U4D4))
																												
																												continue;
																												
																												if($grab_parameters['xs_exclude_check'])
																												
																												{
																												
																												$_f=$_f2=false;
																												
																												$_f=$this->GgZ_Thchlxe&&preg_match('#('.$this->GgZ_Thchlxe.')#',$r9vJFlZw_t);
																												
																												if($this->BQ9LlruW7onUt1lb&&!$_f)
																												
																												foreach($this->BQ9LlruW7onUt1lb as $bm)
																												
																												$_f = $_f||preg_match('#^('.$bm.')#',$this->CMQaGmG6s9sCBhxq.$r9vJFlZw_t);
																												
																												
																												
																												if($_f)continue;
																												
																												}
																												
																												DFJUj5XaZVoZf("<u>[$r9vJFlZw_t]</u><br>\n",2);//exit;
																												
																												$urls_list2[$r9vJFlZw_t]++;
																												
																												if($grab_parameters['xs_maxref'] && count($ref_links[$r9vJFlZw_t])<$grab_parameters['xs_maxref'])
																												
																												$ref_links[$r9vJFlZw_t][] = $GNYoUcb8ZrZah5U4D4;
																												
																												$nt++;
																												
																												}
																												
																												unset($EFK5jcimGXb);
																												
																												u3Aj3kpP8f7NX('llist', true);
																												
																												}
																												
																												}
																												
																												
																												$h9EmToufL4h9OSGalBd = count($urls_list2);
																												
																												u3Aj3kpP8f7NX('analyze', true);
																												
																												u3Aj3kpP8f7NX('post');
																												
																												if($grab_parameters['xs_incl_only'] && !$f){
																												
																												global $LlGUdFg6y3le5XQDj5;
																												
																												if(!isset($LlGUdFg6y3le5XQDj5)){
																												
																												$LlGUdFg6y3le5XQDj5 = $grab_parameters['xs_incl_only'];
																												
																												if(!preg_match('#[\*\$]#',$LlGUdFg6y3le5XQDj5))
																												
																												$LlGUdFg6y3le5XQDj5 = preg_quote($LlGUdFg6y3le5XQDj5,'#');
																												
																												$LlGUdFg6y3le5XQDj5 = '#'.str_replace(' ', '|', $LlGUdFg6y3le5XQDj5).'#';
																												
																												}
																												
																												$f = $f || !preg_match($LlGUdFg6y3le5XQDj5,$bde7EoOvSY07vuMwIoK.$GNYoUcb8ZrZah5U4D4);
																												
																												}
																												
																												if(!$f) {
																												
																												$f = $f||preg_match('#<meta\s*name=[\'"]robots[\'"]\s*content=[\'"][^\'"]*?noindex#is',$cn);
																												
																												if($f)$t3jvxqdXKF32_lYX = 'mrob';
																												
																												}
																												
																												if(!$f)
																												
																												{
																												
																												if(!$LJci6KwBdfosP9p) {
																												
																												$LJci6KwBdfosP9p = array(
																												
																												
																												'link' => preg_replace('#//+$#','/', 
																												
																												preg_replace('#^([^/\:\?]/)/+#','\\1', 
																												
																												(strstr($GNYoUcb8ZrZah5U4D4, '://') ? $GNYoUcb8ZrZah5U4D4 : $bde7EoOvSY07vuMwIoK . $GNYoUcb8ZrZah5U4D4)
																												
																												))
																												
																												);
																												
																												if($grab_parameters['xs_makehtml']||$grab_parameters['xs_makeror']||$grab_parameters['xs_rssinfo'])
																												
																												{
																												
																												preg_match('#<title>([^<]*?)</title>#is', $BkrUa2m_ZoFXT['content'], $lpKdJmos1WZGtLE9ez0);
																												
																												$LJci6KwBdfosP9p['t'] = strip_tags($lpKdJmos1WZGtLE9ez0[1]);
																												
																												}
																												
																												if($grab_parameters['xs_metadesc'])
																												
																												{
																												
																												preg_match('#<meta\s[^>]*(?:http-equiv|name)\s*=\s*"?description[^>]*content\s*=\s*["]?([^>\"]*)#is', $cn, $ENPAxRaZPoP3uztho);
																												
																												if($ENPAxRaZPoP3uztho[1])
																												
																												$LJci6KwBdfosP9p['d'] = $ENPAxRaZPoP3uztho[1];
																												
																												}
																												
																												if($grab_parameters['xs_makeror']||$grab_parameters['xs_autopriority'])
																												
																												$LJci6KwBdfosP9p['o'] = max(0,$links_level);
																												
																												if($pMUfxUHOM)
																												
																												$LJci6KwBdfosP9p['p'] = $pMUfxUHOM;
																												
																												if(preg_match('#('.$aQ4ElPDMjZqlA6eve.')#',$bde7EoOvSY07vuMwIoK.$GNYoUcb8ZrZah5U4D4,$os8tVH12X))
																												
																												{
																												
																												$LJci6KwBdfosP9p['clm'] = $pE0deENVttSkb8Hz[$os8tVH12X[1]]['lm'];
																												
																												$LJci6KwBdfosP9p['f'] = $pE0deENVttSkb8Hz[$os8tVH12X[1]]['f'];
																												
																												$LJci6KwBdfosP9p['p'] = $pE0deENVttSkb8Hz[$os8tVH12X[1]]['p'];
																												
																												}
																												
																												
																												
																												
																												
																												if($grab_parameters['xs_lastmod_notparsed'] && $f2)
																												
																												{
																												
																												$BkrUa2m_ZoFXT = $DSZ7onyKhTY->fetch($EE3JSA0ZP1y1fXkP, 0, 1, false, "", array('req'=>'HEAD'));
																												
																												
																												}
																												
																												if(!$LJci6KwBdfosP9p['lm'] && isset($BkrUa2m_ZoFXT['headers']['last-modified']))
																												
																												$LJci6KwBdfosP9p['lm']=$BkrUa2m_ZoFXT['headers']['last-modified'];
																												
																												}
																												
																												u3Aj3kpP8f7NX('post', true);
																												
																												u3Aj3kpP8f7NX('post-save1');
																												
																												DFJUj5XaZVoZf("\n((include ".$LJci6KwBdfosP9p['link']."))<br />\n");
																												
																												$sSxD4RNwg = true;
																												
																												if($grab_parameters['xs_memsave'])
																												
																												{
																												
																												hFdFC9FutftfbQcPCF($XSgHgDDEhTbwopK_PT, $LJci6KwBdfosP9p);
																												
																												$urls_completed[] = $XSgHgDDEhTbwopK_PT;
																												
																												}else
																												
																												$urls_completed[] = serialize($LJci6KwBdfosP9p);
																												
																												$Zl5g2LLgy3++;
																												
																												
																												u3Aj3kpP8f7NX('post-save1',true);
																												
																												u3Aj3kpP8f7NX('post-save2');
																												
																												if($grab_parameters['xs_prev_sm_base']
																												
																												&& $tBvUUp_KNPkYQ04FY1 &&
																												
																												preg_match('#('.$tBvUUp_KNPkYQ04FY1.')#',$GNYoUcb8ZrZah5U4D4)){
																												
																												$sm_base[$GNYoUcb8ZrZah5U4D4] = $LJci6KwBdfosP9p;
																												
																												}
																												
																												$CWUHIna3EKRbOn4D4cw = $pez0n43nSLB - $Zl5g2LLgy3;
																												
																												u3Aj3kpP8f7NX('post-save2',true);
																												
																												}
																												
																												}while(false);// zerowhile
																												
																												u3Aj3kpP8f7NX('post-progress1');
																												
																												if($url_ind>=$cnu)
																												
																												{
																												
																												unset($urls_list);
																												
																												$url_ind = 0;
																												
																												$urls_list = $urls_list2;
																												
																												
																												$urls_list_full += $urls_list;
																												
																												$cnu = count($urls_list);
																												
																												unset($ref_links2);
																												
																												$ref_links2 = $ref_links;
																												
																												unset($ref_links); unset($urls_list2);
																												
																												$ref_links = array();
																												
																												$urls_list2 = array();
																												
																												$links_level++;
																												
																												DFJUj5XaZVoZf("\n<br>NEXT LEVEL:$links_level<br />\n");
																												
																												}
																												
																												if(!$sSxD4RNwg){
																												
																												
																												DFJUj5XaZVoZf("\n({skipped ".$GNYoUcb8ZrZah5U4D4." - $t3jvxqdXKF32_lYX})<br />\n");
																												
																												if(!$grab_parameters['xs_chlog_list_max'] ||
																												
																												count($urls_list_skipped) < $grab_parameters['xs_chlog_list_max']) {
																												
																												$urls_list_skipped[$GNYoUcb8ZrZah5U4D4]=$t3jvxqdXKF32_lYX;
																												
																												}
																												
																												}
																												
																												u3Aj3kpP8f7NX('post-progress1',true);
																												
																												u3Aj3kpP8f7NX('post-progress2');
																												
																												$pn++;
																												
																												$PoOrXmr19YxJ2d=explode(" ",microtime());
																												
																												$ctime = $PoOrXmr19YxJ2d[0]+$PoOrXmr19YxJ2d[1] - $PK1tEvgTTp;
																												
																												CycjTbE1bsdEES4EbPq();
																												
																												$pl=min($cnu-$url_ind,$CWUHIna3EKRbOn4D4cw);
																												
																												u3Aj3kpP8f7NX('post-progress2',true);
																												
																												u3Aj3kpP8f7NX('post-progress3');
																												
																												if( ($cnu==$url_ind || $pl==0||$pn==1 || ($pn%$grab_parameters['xs_progupdate'])==0)
																												
																												|| ($ctime - $KePo16DVN0OS > 5)
																												
																												|| $Zl5g2LLgy3>=$pez0n43nSLB)
																												
																												{
																												
																												
																												$KePo16DVN0OS = $D_ezG4BPksZTM79mUvk;
																												
																												if(strstr($S0BtLzEJVxMK_RX['content'],'header'))break;
																												
																												global $m8;
																												
																												$mu = function_exists('memory_get_usage') ? memory_get_usage() : '-';
																												
																												$MHgpHZ_Ll2Hbw15PI = max($MHgpHZ_Ll2Hbw15PI, $mu);
																												
																												if($mu>$m8+1000000){
																												
																												$m8 = $mu;
																												
																												$cc = ' style="color:red"';
																												
																												}else 
																												
																												$cc='';
																												
																												if(intval($mu))
																												
																												$mu = number_format($mu/1024,1).' Kb';
																												
																												DFJUj5XaZVoZf("\n(<span".$cc.">memory".($cc?' up':'').": $mu</span>)<br>\n");
																												
																												$ugr2aG5lxR = ($Zl5g2LLgy3>=$pez0n43nSLB) || ($url_ind>=$cnu);
																												
																												$progpar = array(
																												
																												$ctime, // 0. running time
																												
																												str_replace($pZraQah5aJyN, '', $GNYoUcb8ZrZah5U4D4),  // 1. current URL
																												
																												$pl,                    // 2. urls left
																												
																												$pn,                    // 3. processed urls
																												
																												$tsize,                 // 4. bandwidth usage
																												
																												$links_level,           // 5. depth level
																												
																												$mu,                    // 6. memory usage
																												
																												$Zl5g2LLgy3, // 7. added in sitemap
																												
																												$h9EmToufL4h9OSGalBd,     // 8. in the queue
																												
																												$nettime,	// 9. network time
																												
																												$HQHtKgSdG, // 10. last net time
																												
																												$fetch_no // 11. fetched urls
																												
																												);
																												
																												if($Jg7UKn8jH9hzZ['bgexec']){
																												
																												if((time()-$FI1GZl2uOM_y)>DOqagMEXq2('xs_state_interval',5)){
																												
																												$FI1GZl2uOM_y = time();
																												
																												$progpar[] = iK6zN3FNMZ();
																												
																												PCiMWHKGB5lwUwCci(gLuwORIUpN,wNuDcYNWIWQ($progpar));
																												
																												}
																												
																												}
																												
																												if($a72fjXmL4zzgMw && !$f)
																												
																												$a72fjXmL4zzgMw($progpar);
																												
																												
																												}
																												
																												else
																												
																												{
																												
																												$a72fjXmL4zzgMw(array('cmd'=>'ping', 'bg' => $Jg7UKn8jH9hzZ['bgexec']));
																												
																												}
																												
																												u3Aj3kpP8f7NX('post-progress3',true);
																												
																												u3Aj3kpP8f7NX('post-progress4');
																												
																												if(!$VP8vAJd3fs3x1Ygo) {
																												
																												
																												if($VP8vAJd3fs3x1Ygo = bZ3jbCz403O1HU()){
																												
																												if(!@tV12hsJy_($VP8vAJd3fs3x1Ygo))
																												
																												$VP8vAJd3fs3x1Ygo=0;
																												
																												}
																												
																												}
																												
																												if($grab_parameters['xs_exec_time'] && 
																												
																												((time()-$FkLeMvYAd) > $grab_parameters['xs_exec_time']) ){
																												
																												$VP8vAJd3fs3x1Ygo = 'Time limit exceeded - '.($grab_parameters['xs_exec_time']).' - '.(time()-$FkLeMvYAd);
																												
																												}
																												
																												if($grab_parameters['xs_savestate_time']>0 &&
																												
																												( 
																												
																												($ctime-$Btu_OfIkMLszVD>$grab_parameters['xs_savestate_time'])
																												
																												|| $ugr2aG5lxR
																												
																												|| $VP8vAJd3fs3x1Ygo
																												
																												)
																												
																												)
																												
																												{
																												
																												$Btu_OfIkMLszVD = $ctime;
																												
																												DFJUj5XaZVoZf("(saving dump)<br />\n");
																												
																												$FVrvczhkoCdSfmCKoGm = compact('url_ind',
																												
																												'urls_list','urls_list2','cnu',
																												
																												'ref_links','ref_links2',
																												
																												'urls_list_full','urls_completed',
																												
																												'urls_404',
																												
																												'nt','tsize','pn','links_level','ctime', 'urls_ext','fetch_no',
																												
																												'starttime', 'retrno', 'nettime', 'urls_list_skipped',
																												
																												'imlist', 'progpar', 'runstate', 'sm_base'
																												
																												);
																												
																												$FVrvczhkoCdSfmCKoGm['time']=time();
																												
																												$jtjCCajYHG=wNuDcYNWIWQ($FVrvczhkoCdSfmCKoGm);
																												
																												PCiMWHKGB5lwUwCci(eYgPj3ZHK0T12hAy,$jtjCCajYHG,dh6mwOEumX3JD,true);
																												
																												unset($FVrvczhkoCdSfmCKoGm);
																												
																												unset($jtjCCajYHG);
																												
																												}
																												
																												if($grab_parameters['xs_delay_req'] && $grab_parameters['xs_delay_ms'] &&
																												
																												(($I9twBTcW88N%$grab_parameters['xs_delay_req'])==0))
																												
																												{
																												
																												sleep(intval($grab_parameters['xs_delay_ms']));
																												
																												}
																												
																												u3Aj3kpP8f7NX('post-progress4', true);
																												
																												}while(!$ugr2aG5lxR && !$VP8vAJd3fs3x1Ygo);
																												
																												DFJUj5XaZVoZf("\n\n<br><br>Crawling completed<br>\n");
																												
																												if($_GET['ddbgexit']){
																												
																												echo '<hr><hr><h2>Dbg exit</h2>';
																												
																												echo $DSZ7onyKhTY->zzgyJiXlgZNLUWw_.' / '.$DSZ7onyKhTY->nettime.'<hr>';
																												
																												echo iK6zN3FNMZ().'<hr>';
																												
																												exit;
																												
																												}
																												
																												return array(
																												
																												'u404'=>$urls_404,
																												
																												'starttime'=>$starttime,
																												
																												'topmu' => $MHgpHZ_Ll2Hbw15PI,
																												
																												'ctime'=>$ctime,
																												
																												'tsize'=>$tsize,
																												
																												'retrno' => $retrno,
																												
																												'nettime' => $nettime,
																												
																												'errmsg'=>'',
																												
																												'initurl'=>$pZraQah5aJyN,
																												
																												'initdir'=>$bde7EoOvSY07vuMwIoK,
																												
																												'ucount'=>$Zl5g2LLgy3,
																												
																												'crcount'=>$pn,
																												
																												'fetch_no'=>$fetch_no,
																												
																												'time'=>time(),
																												
																												'params'=>$Jg7UKn8jH9hzZ,
																												
																												'interrupt'=>$VP8vAJd3fs3x1Ygo,
																												
																												'runstate' => $runstate,
																												
																												'sm_base' => $sm_base,
																												
																												'urls_ext'=>$urls_ext,
																												
																												'urls_list_skipped' => $urls_list_skipped,
																												
																												'max_reached' => $Zl5g2LLgy3>=$pez0n43nSLB
																												
																												);
																												
																												}
																												
																												}
																												
																												$olIC3lVPm3RAAqb = new SiteCrawler();
																												
																												function YlnYTfdc4(){
																												
																												@tV12hsJy_(dh6mwOEumX3JD.HVKGdDolsi2eMB_mMuD);
																												
																												if(@file_exists(dh6mwOEumX3JD.gLuwORIUpN))
																												
																												@rename(dh6mwOEumX3JD.gLuwORIUpN,dh6mwOEumX3JD.HVKGdDolsi2eMB_mMuD);
																												
																												}
																												
																												



































































































