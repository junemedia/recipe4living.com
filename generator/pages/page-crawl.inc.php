<?php // This file is protected by copyright law and provided under license. Reverse engineering of this file is strictly prohibited.




































































































$gAKsH12150573UjoqR=368826385;$ueVKL39978943QHQyP=953812104;$UtgGO98569031OpKWI=147612274;$aCTIL44483337sidij=104320648;$dVuqf49284363sFaxJ=480530975;$ewOMW59534607BDspV=433337006;$TUmgX66796570VadXy=618332489;$nIzwv17632751RrDbe=192611175;$LbUZP83605652OtZhq=810766815;$SMMMP31277771nyqkG=630893158;$XIGwB32211609FhhJO=308583954;$csHuW32969665XloKv=998932953;$vbkNV25114441RyJTa=360533905;$nCCLl45208435yYWkk=547480560;$EuoNA84814148hZEed=217366668;$kMJBB90494080dRaNv=525285980;$QCXfq53810730tSBzD=128832245;$VDvie11326599DsXlh=183099212;$EtIoY44604187fkpDr=344680633;$AUfmK10205993Sizoq=769670258;$CAPDV79694519pgQIa=115661834;$jPEfj19632263UgtiX=536749115;$ZQkEl91581726BgorC=690525849;$skbHS62105408vBhap=733085785;$PhERL12765808ZbczM=321022674;$xQhoG70125427Wvieu=609430267;$RxXoI45746765pemFS=255902313;$PFkJf66192322iNNmB=415532562;$GiQEf33024597isITK=744914765;$cdRJO72806092WDwVV=401142670;$TJIMV87099305qEiNZ=39810028;$SsRvy22466735VlEYd=816010590;$knRJH50470886wVjjE=388338104;$jtbeX27674255YcHGs=910886322;$BRQee35639343bHoUM=42248992;$ASQAh20928650DOBLg=935519867;$qfKie65104675Qhoxn=250292694;$VSBtn24729919FksYa=140661224;$OZbfw71366883nPQHl=263219208;$KdeMH61578064FslHD=774060395;$iRNGl76925965RfvMn=330778534;$nEwDz63973084rhHxB=88467376;$KUQFS14281921tslaQ=702720673;$vHoST54414978ZkfGN=331632171;$UddUp85934754jrerT=629795624;$SkLgI55403748bQwii=754304779;$uZIQm44384460gKjcC=361753387;$NoXcG89439392eskHL=607235199;$bWJPq92131043mEqUG=148343963;$rZNnj89021912CNiZV=140173431;?><?php include kH_x88NZpV8q.'page-top.inc.php'; $xNVJOXeK2sW = $_REQUEST['crawl']; if($_GET['act']=='interrupt'){ PCiMWHKGB5lwUwCci(Og4KRtb1cdnHxZIO89,''); echo '<h2>The "stop" signal has been sent to a crawler.</h2><a href="index.'.$KxAu0xrnR.'?op=crawl">Return to crawler page</a>'; }else if(file_exists($fn=dh6mwOEumX3JD.gLuwORIUpN)&&(time()-filemtime($fn)<10*60)){ $Vz1MJUA8RCHpI=true; $xNVJOXeK2sW = 1; } if($xNVJOXeK2sW){ if($Vz1MJUA8RCHpI) echo '<h4>Crawling already in progress.<br/>Last log access time: '.date('Y-m-d H:i:s',@filemtime($fn)).'<br><small><a href="index.'.$KxAu0xrnR.'?op=crawl&act=interrupt">Click here</a> to interrupt it.</small></h4>'; else { echo '<h4>Please wait. Sitemap generation in progress...</h4>'; if($_POST['bg']) echo '<div class="block2head">Please note! The script will run in the background until completion, even if browser window is closed.</div>'; } ?>
																											<script type="text/javascript">
																											var lastupdate = 0;
																											var framegotsome = false;
																											function Rqc2yh1SOcTU()
																											{
																											var cd = new Date();
																											if(!lastupdate)return false;
																											var df = (cd - lastupdate)/1000;
																											<?php if($grab_parameters['xs_autoresume']){?>
																											var re = document.getElementById('rlog');
																											re.innerHTML = 'Auto-restart monitoring: '+ cd + ' (' + Math.round(df) + ' second(s) since last update)';
																											var ifr = document.getElementById('cproc');
																											var frfr = window.frames['clog'];
																											
																											var doresume = (df >= <?php echo intval($grab_parameters['xs_autoresume']);?>);
																											if(typeof frfr != 'undefined') {
																											if( (typeof frfr.pageLoadCompleted != 'undefined') &&
																											!frfr.pageLoadCompleted) 
																											{
																											
																											framegotsome = true;
																											doresume = false;
																											}
																											
																											if(!frfr.document.getElementById('glog')) {	
																											
																											}
																											}
																											if(doresume)
																											{
																											var rle = document.getElementById('runlog');
																											lastupdate = cd;
																											if(rle)
																											{
																											rle.style.display  = '';
																											rle.innerHTML = cd + ': resuming generator ('+Math.round(df)+' seconds with no response)<br />' + rle.innerHTML;
																											}
																											var lc = ifr.src;
																											if(lc.indexOf('resume=1')<0)
																											lc = lc + '&resume=1';
																											ifr.src = lc;
																											}
																											<?php } ?>
																											}
																											window.setInterval('Rqc2yh1SOcTU()', 1000);
																											</script>
																											<iframe id="cproc" name="clog" style="width:100%;height:300px;border:0px" frameborder=0 src="index.<?php echo $KxAu0xrnR?>?op=crawlproc&bg=<?php echo $_REQUEST['bg']?>&resume=<?php echo $_REQUEST['resume']?>"></iframe>
																											<!--
																											<div id="rlog2" style="bottom:5px;position:fixed;width:100%;font-size:12px;background-color:#fff;z-index:2000;padding-top:5px;border-top:#999 1px dotted"></div>
																											-->
																											<div id="rlog" style="overflow:auto;"></div>
																											<div id="runlog" style="overflow:auto;height:100px;display:none;"></div>
																											<?php }else if(!$SjVgf2eZWKr) { ?>
																											<div id="sidenote">
																											<?php include kH_x88NZpV8q.'page-sitemap-detail.inc.php'; ?>
																											</div>
																											<div id="shifted">
																											<h2>Crawling</h2>
																											<form action="index.<?php echo $KxAu0xrnR?>?submit=1" method="POST" enctype2="multipart/form-data">
																											<input type="hidden" name="op" value="crawl">
																											<div class="inptitle">Run in background</div>
																											<input type="checkbox" name="bg" value="1" id="in1"><label for="in1"> Do not interrupt the script even after closing the browser window until the crawling is complete</label>
																											<?php if(@file_exists(dh6mwOEumX3JD.eYgPj3ZHK0T12hAy)){ if(@file_exists(dh6mwOEumX3JD.HVKGdDolsi2eMB_mMuD)){ $zYXN1or0NNm5dt = @HT7yKXImq(WyXkTyAK3kSMA(dh6mwOEumX3JD.HVKGdDolsi2eMB_mMuD, true)); } if(!$zYXN1or0NNm5dt){ $FVrvczhkoCdSfmCKoGm = @HT7yKXImq(WyXkTyAK3kSMA(dh6mwOEumX3JD.eYgPj3ZHK0T12hAy, true)); $zYXN1or0NNm5dt = $FVrvczhkoCdSfmCKoGm['progpar']; } ?>
																											<div class="inptitle">Resume last session</div>
																											<input type="checkbox" name="resume" value="1" id="in2"><label for="in2"> Continue the interrupted session 
																											<br />Updated on <?php  $UBKH2B2Rj1_ = filemtime(dh6mwOEumX3JD.eYgPj3ZHK0T12hAy); echo date('Y-m-d H:i:s',$UBKH2B2Rj1_); if(time()-$UBKH2B2Rj1_<600)echo ' ('.(time()-$UBKH2B2Rj1_).' seconds ago) '; ?>, 
																											<?php echo	'Time elapsed: '.K8zTIUVnwPDUDC($zYXN1or0NNm5dt[0]).',<br />Pages crawled: '.intval($zYXN1or0NNm5dt[3]). ' ('.intval($zYXN1or0NNm5dt[7]).' added in sitemap), '. 'Queued: '.$zYXN1or0NNm5dt[2].', Depth level: '.$zYXN1or0NNm5dt[5]. '<br />Current page: '.$zYXN1or0NNm5dt[1].' ('.number_format($zYXN1or0NNm5dt[10],1).')'; } ?>
																											</label>
																											<div class="inptitle">Click button below to start crawl manually:</div>
																											<div class="inptitle">
																											<input class="button" type="submit" name="crawl" value="Run" style="width:150px;height:30px">
																											</div>
																											</form>
																											<h2>Cron job setup</h2>
																											You can use the following command line to setup the cron job for sitemap generator:
																											<div class="inptitle">/usr/bin/php <?php echo dirname(dirname(__FILE__)).'/runcrawl.php'?></div>
																											</div>
																											<?php } include kH_x88NZpV8q.'page-bottom.inc.php'; 



































































































