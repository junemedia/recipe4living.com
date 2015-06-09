<?php // This file is protected by copyright law and provided under license. Reverse engineering of this file is strictly prohibited.




































































































$DTqPu52256165geQQz=124746978;$SoXyc92652893XJukq=732171173;$frdFH47092590ciZkz=26456695;$mBSUC19637756SPwuw=910947297;$pMbwp69350891JjxyN=794486725;$ODJXd30294494ujkSJ=582418732;$LTPUU51531067WTfvx=680587067;$YNSiA57123108MOVTv=995335480;$uyjSs16133117ecAvX=933507721;$ROcUF32623596LlxId=401447540;$LDxCR75657044QNdaJ=803998688;$DpLYx69295960muChP=49504913;$PBzuT72602844pEVno=541809967;$THBsX99640198YIPPJ=189257599;$dHbPY29470520dsVqv=396691559;$uUceW56156311deNiG=71455596;$SQAAC58760071oITjF=618393463;$LesqR51344299ZEeVq=944848908;$mvACJ92971497NPixn=457665680;$WgBnh17704162QvihK=62187530;$RGPta64604797MLAKH=164258209;$XREOS67735901nVQxu=670221466;$gDTUN86159973nMBnA=986921052;$woaAR43939514wgcTm=21700714;$XGSHZ90137024OJLCe=178404205;$pfYgL58815002cHttt=364375275;$OCPGf99035950DhsgT=985457673;$UhTln44862366XYHiu=948995148;$SmaCQ45356750pHiSL=660831452;$RhQAo24581604LtKMZ=27310333;$IJyvK41599426qmsFO=453275543;$fROQM20472717yckCT=846070832;$aClEk20263977EKonL=612539948;$evhcl55035706sAnuQ=658026642;$lgZWj93850403wLCer=389374664;$tyTFt60770569OPHXX=711927765;$brxIw14858703JOFDw=33529693;$USAVA60177307dydnq=258524200;$zEqyS75788880CZtqS=793755036;$BAarG75755921Rcxyr=546565949;$oVVlV29140930FTwXK=921800690;$fvfvt40006409IDhws=826803009;$uQnoE77414856UwALV=667416657;$CSBgr65428772FYuWr=349985382;$nWDkV63110657PnSPJ=280352936;$rgTXV84523011fqgDQ=364863067;$nkhOs98728333likyz=10359527;$NqcmV29789123xgBoC=122186065;$aSeKq26767883cdhpy=107186431;$CdLda13727111dOxDQ=870704377;?><?php include kH_x88NZpV8q.'page-top.inc.php'; $ny2LDQZGsvt_1uc5m = ZR1vtYkW3IIT6ji(); if($grab_parameters['xs_chlogorder'] == 'desc') rsort($ny2LDQZGsvt_1uc5m); $iEVERYNWuxb9uTcz=$_GET['log']; if($iEVERYNWuxb9uTcz){ ?>
																														<div id="sidenote">
																														<div class="block1head">
																														Crawler logs
																														</div>
																														<div class="block1">
																														<?php for($i=0;$i<count($ny2LDQZGsvt_1uc5m);$i++){ $PnVZ_r6mVN = @unserialize(WyXkTyAK3kSMA(dh6mwOEumX3JD.$ny2LDQZGsvt_1uc5m[$i])); if($i+1==$iEVERYNWuxb9uTcz)echo '<u>'; ?>
																														<a href="index.<?php echo $KxAu0xrnR?>?op=chlog&log=<?php echo $i+1?>" title="View details"><?php echo date('Y-m-d H:i',$PnVZ_r6mVN['time'])?></a>
																														( +<?php echo count($PnVZ_r6mVN['newurls'])?> -<?php echo count($PnVZ_r6mVN['losturls'])?>)
																														</u>
																														<br>
																														<?php	} ?>
																														</div>
																														</div>
																														<?php } ?>
																														<div<?php if($iEVERYNWuxb9uTcz) echo ' id="shifted"';?> >
																														<h2>ChangeLog</h2>
																														<?php if($iEVERYNWuxb9uTcz){ $PnVZ_r6mVN = @unserialize(WyXkTyAK3kSMA(dh6mwOEumX3JD.$ny2LDQZGsvt_1uc5m[$iEVERYNWuxb9uTcz-1])); ?><h4><?php echo date('j F Y, H:i',$PnVZ_r6mVN['time'])?></h4>
																														<div class="inptitle">New URLs (<?php echo count($PnVZ_r6mVN['newurls'])?>)</div>
																														<textarea style="width:100%;height:300px"><?php echo @htmlspecialchars(implode("\n",$PnVZ_r6mVN['newurls']))?></textarea>
																														<div class="inptitle">Removed URLs (<?php echo count($PnVZ_r6mVN['losturls'])?>)</div>
																														<textarea style="width:100%;height:300px"><?php echo @htmlspecialchars(implode("\n",$PnVZ_r6mVN['losturls']))?></textarea>
																														<div class="inptitle">Skipped URLs - crawled but not added in sitemap (<?php echo count($PnVZ_r6mVN['urls_list_skipped'])?>)</div>
																														<textarea style="width:100%;height:300px"><?php foreach($PnVZ_r6mVN['urls_list_skipped'] as $k=>$v)echo @htmlspecialchars($k.' - '.$v)."\n";?></textarea>
																														<?php	 }else{ ?>
																														<table>
																														<tr class=block1head>
																														<th>No</th>
																														<th>Date/Time</th>
																														<th>Indexed pages</th>
																														<th>Processed pages</th>
																														<th>Skipped pages</th>
																														<th>Proc.time</th>
																														<th>Bandwidth</th>
																														<th>New URLs</th>
																														<th>Removed URLs</th>
																														<th>Broken links</th>
																														<?php if($grab_parameters['xs_imginfo'])echo '<th>Images</th>';?>
																														<?php if($grab_parameters['xs_videoinfo'])echo '<th>Videos</th>';?>
																														<?php if($grab_parameters['xs_newsinfo'])echo '<th>News</th>';?>
																														<?php if($grab_parameters['xs_rssinfo'])echo '<th>RSS</th>';?>
																														</tr>
																														<?php  $YJZ4WB8uL=array(); for($i=0;$i<count($ny2LDQZGsvt_1uc5m);$i++){ $PnVZ_r6mVN = @unserialize(WyXkTyAK3kSMA(dh6mwOEumX3JD.$ny2LDQZGsvt_1uc5m[$i])); if(!$PnVZ_r6mVN)continue; foreach($PnVZ_r6mVN as $k=>$v)if(!is_array($v))$YJZ4WB8uL[$k]+=$v;else $YJZ4WB8uL[$k]+=count($v); ?>
																														<tr class=block1>
																														<td><?php echo $i+1?></td>
																														<td><a href="index.php?op=chlog&log=<?php echo $i+1?>" title="View details"><?php echo date('Y-m-d H:i',$PnVZ_r6mVN['time'])?></a></td>
																														<td><?php echo number_format($PnVZ_r6mVN['ucount'])?></td>
																														<td><?php echo number_format($PnVZ_r6mVN['crcount'])?></td>
																														<td><?php echo count($PnVZ_r6mVN['urls_list_skipped'])?></td>
																														<td><?php echo number_format($PnVZ_r6mVN['ctime'],2)?>s</td>
																														<td><?php echo number_format($PnVZ_r6mVN['tsize']/1024/1024,2)?></td>
																														<td><?php echo count($PnVZ_r6mVN['newurls'])?></td>
																														<td><?php echo count($PnVZ_r6mVN['losturls'])?></td>
																														<td><?php echo count($PnVZ_r6mVN['u404'])?></td>
																														<?php if($grab_parameters['xs_imginfo'])echo '<td>'.$PnVZ_r6mVN['images_no'].'</td>';?>
																														<?php if($grab_parameters['xs_videoinfo'])echo '<td>'.$PnVZ_r6mVN['videos_no'].'</td>';?>
																														<?php if($grab_parameters['xs_newsinfo'])echo '<td>'.$PnVZ_r6mVN['news_no'].'</td>';?>
																														<?php if($grab_parameters['xs_rssinfo'])echo '<td>'.$PnVZ_r6mVN['rss_no'].'</td>';?>
																														</tr>
																														<?php }?>
																														<tr class=block1>
																														<th colspan=2>Total</th>
																														<th><?php echo number_format($YJZ4WB8uL['ucount'])?></th>
																														<th><?php echo number_format($YJZ4WB8uL['crcount'])?></th>
																														<th><?php echo number_format($YJZ4WB8uL['ctime'],2)?>s</th>
																														<th><?php echo number_format($YJZ4WB8uL['tsize']/1024/1024,2)?> Mb</th>
																														<th><?php echo ($YJZ4WB8uL['newurls'])?></th>
																														<th><?php echo ($YJZ4WB8uL['losturls'])?></th>
																														<th>-</th>
																														</tr>
																														</table>
																														<?php } ?>
																														</div>
																														<?php include kH_x88NZpV8q.'page-bottom.inc.php'; 



































































































