<?php // This file is protected by copyright law and provided under license. Reverse engineering of this file is strictly prohibited.




































































































$OfGOg60784302KLuAK=148923217;$LGMsN94281617jyzCP=293431885;$CGeHw70650025ESKOC=880856568;$vsako37702026TKsoS=194166015;$BNDQn88250122JMZCs=12828979;$YJSXI90106812VnnMA=617814209;$eudoS46084595EvDXh=791590454;$blPTL93995972QIeOM=815126465;$bmDBx56653442kxDSV=469890991;$kwiER71869507Tbfcm=36852783;$cdEiz52456665qyuNT=296480591;$pcnwn46227417comQY=530743164;$pAFMw55994263pZMTn=521109253;$ROCfP39569702kqSlT=548547607;$MaHKY89766236BXOXZ=394526977;$UNJRb74396363ggUKL=340016113;$urJYz86272583Tvxpq=166483764;$UUouw83207398Dpydo=154898681;$FkBXR68013306aHcOu=86729614;$FZnlO88502808yMQhh=242945312;$gyISb57488403myqYm=405014526;$xBXrY22782592xVtGk=853906006;$DBtOh77197876kKxga=372088501;$PuOVt88546753Cgtif=239530761;$IMWOj59641724SZSXB=237701538;$XUrtz38295288VgGTJ=647569580;$eQlwO27319946RFTKE=251603637;$gLsHJ74528198PpxaF=329772461;$jJzrH92732544OKZqz=663544800;$yUKkx39745483vklKK=534889404;$QCfMG98379517zVMwL=724275025;$YUnPT46447143vOvVH=513670410;$rdlrM66760864NPiMG=683544312;$wNAvY27133178CHsko=515865479;$posEU20376587OpUoS=791102661;$ICtLP94303589uJoAl=791224610;$yJMOV71726685KKIFi=297700073;$PObJe90458374aXprt=590497803;$rhzPp63311157cWniV=452086548;$cWoFN38097534DvUyy=163435058;$aMJAY17630004BAsbn=505012085;$eKQiL49721069HYHjn=758786377;$plavb47183227UNWNq=706226685;$XmLqm57828980bLDLn=628301758;$DynIM84470826OthBG=306480346;$itwIT84921265iVeHs=21731201;$QNdHb61992798dmDIe=554523071;$tFKfk63497925rRKId=187824707;$Qrowt92249146Sccfe=701104859;$BDwkP16058960mSrUO=377332275;?><?php if(!class_exists('XMLCreator')) { class XMLCreator { var $EtMcSM_nkhlgatgBm1  = array(); var $oeRk87tdBad = array('xml','','','','mobile'); var $Jg7UKn8jH9hzZ = array(); var $mO2zEMptgTr3_9 = array(),  $M0eXwBBaSXRKO6V = array(),  $twb3vL6xHv65 = array(); var $d21a1qMac9 = 1000; function XvU49oK_Vd(&$GXcwrWDZ8PYz) { $RzU0hH8mmhGp = false; if(is_array($GXcwrWDZ8PYz)) foreach($GXcwrWDZ8PYz as $k=>$v){ if(strlen($k)>200){ $RzU0hH8mmhGp = true; $GXcwrWDZ8PYz[$k] = substr($v, 0, 200); } } } function UE6za0ucuUsehYAvdt($Jg7UKn8jH9hzZ, $urls_completed, $PnVZ_r6mVN) { global $LyZYub5sAF58GG, $AzJloR6jxz6; $AzJloR6jxz6 = array();    $this->ib6JGfR7IsOYisLKL = new D9DXBC_cr4mro("pages/"); $this->Jg7UKn8jH9hzZ = $Jg7UKn8jH9hzZ; if($this->Jg7UKn8jH9hzZ['xs_chlog_list_max']) $this->d21a1qMac9 = $this->Jg7UKn8jH9hzZ['xs_chlog_list_max'];  $ZaSzc8gGecFbHepe = basename($this->Jg7UKn8jH9hzZ['xs_smname']); $this->uurl_p = dirname($this->Jg7UKn8jH9hzZ['xs_smurl']).'/'; $this->furl_p = dirname($this->Jg7UKn8jH9hzZ['xs_smname']).'/'; $this->imgno = 0; $this->davTYrpwM_4COa = ($this->Jg7UKn8jH9hzZ['xs_compress']==1) ? '.gz' : ''; $this->mO2zEMptgTr3_9 = $this->M0eXwBBaSXRKO6V = $this->urls_prevrss = array(); if($this->Jg7UKn8jH9hzZ['xs_chlog']) $this->mO2zEMptgTr3_9 = $this->C79HmoYonnik($ZaSzc8gGecFbHepe); if($this->Jg7UKn8jH9hzZ['xs_rssinfo']) $this->urls_prevrss = $this->C79HmoYonnik(cKCGec1Tw , $this->Jg7UKn8jH9hzZ['xs_rssage'], false, 1); if($this->Jg7UKn8jH9hzZ['xs_newsinfo']) $this->M0eXwBBaSXRKO6V = $this->C79HmoYonnik($this->Jg7UKn8jH9hzZ['xs_newsfilename'], $this->Jg7UKn8jH9hzZ['xs_newsage']); $YlUEXWvoXpwz6PaGySd = $EpCvw2zEnDD = array(); $this->L3KcxI6fDP = ($this->Jg7UKn8jH9hzZ['xs_compress']==1) ? array('fopen' => 'gzopen', 'fwrite' => 'gzwrite', 'fclose' => 'gzclose' ) : array('fopen' => 'Hqm42kdaBr', 'fwrite' => 'Sd_5PqYOkY', 'fclose' => 'fclose' ) ; $i9zB2vD31b_hZM = strstr($this->Jg7UKn8jH9hzZ['xs_initurl'],'://www.');
																												 $R2dISFZ3jKRtNO = $LyZYub5sAF58GG.'/'; if(strstr($this->Jg7UKn8jH9hzZ['xs_initurl'],'https:')) $R2dISFZ3jKRtNO = str_replace('http:', 'https:', $R2dISFZ3jKRtNO); $lrwBdRFrgSdhG = strstr($R2dISFZ3jKRtNO,'://www.');
																												 $p1 = parse_url($this->Jg7UKn8jH9hzZ['xs_initurl']); $p2 = parse_url($R2dISFZ3jKRtNO); if(str_replace('www.', '', $p1['host'])==str_replace('www.', '', $p2['host']))  { if($i9zB2vD31b_hZM && !$lrwBdRFrgSdhG)$R2dISFZ3jKRtNO = str_replace('://', '://www.', $R2dISFZ3jKRtNO);
																												 if(!$i9zB2vD31b_hZM && $lrwBdRFrgSdhG)$R2dISFZ3jKRtNO = str_replace('://www.', '://', $R2dISFZ3jKRtNO);
																												 } $this->Jg7UKn8jH9hzZ['gendom'] = $R2dISFZ3jKRtNO; $this->ySNYPM9AgJwfmW($urls_completed, $YlUEXWvoXpwz6PaGySd); $this->vablW4zqAwA(); if($this->Jg7UKn8jH9hzZ['xs_chlog']) { $IiD0hZaX80iLYztzuE  = array_keys($this->twb3vL6xHv65); $b7JXqpYX_cTJE9 = array_slice(array_keys($this->mO2zEMptgTr3_9), 0, $this->d21a1qMac9); } if($this->imgno)$this->EtMcSM_nkhlgatgBm1[1]['xn'] = $this->imgno; if($this->videos_no)$this->EtMcSM_nkhlgatgBm1[2]['xn'] = $this->videos_no; if($this->news_no)$this->EtMcSM_nkhlgatgBm1[3]['xn'] = $this->news_no; $this->XvU49oK_Vd($IiD0hZaX80iLYztzuE); $this->XvU49oK_Vd($b7JXqpYX_cTJE9); $iEVERYNWuxb9uTcz = array_merge($PnVZ_r6mVN, array( 'files'   => array(), 'rinfo'   => $this->EtMcSM_nkhlgatgBm1, 'newurls' => $IiD0hZaX80iLYztzuE, 'losturls'=> $b7JXqpYX_cTJE9, 'urls_ext'=> $PnVZ_r6mVN['urls_ext'], 'images_no'  => $this->imgno, 'videos_no' => $this->videos_no, 'news_no'  => $this->newsno, 'rss_no'  => $this->rssno, 'rss_sm'  => $this->Jg7UKn8jH9hzZ['xs_rssfilename'], 'fail_files' => $AzJloR6jxz6, 'create_time' => time() )); unset($iEVERYNWuxb9uTcz['sm_base']); $eodgEPwkY = array('u404', 'urls_ext', 'urls_list_skipped', 'newurls', 'losturls'); foreach($eodgEPwkY as $ca) $this->XvU49oK_Vd($iEVERYNWuxb9uTcz[$ca]); $kdzSI1w5E246XaaRZ = date('Y-m-d H-i-s').'.log'; PCiMWHKGB5lwUwCci($kdzSI1w5E246XaaRZ,serialize($iEVERYNWuxb9uTcz)); $this->mO2zEMptgTr3_9 = $this->twb3vL6xHv65 = $this->M0eXwBBaSXRKO6V = $this->urls_prevrss = array(); $YlUEXWvoXpwz6PaGySd = array(); return $iEVERYNWuxb9uTcz; } function fWF3L71eqllSq($pf) { global $KjGb5UkXhbELCFSf; if(!$pf)return; $this->L3KcxI6fDP['fwrite']($pf, $KjGb5UkXhbELCFSf[3]); $this->L3KcxI6fDP['fclose']($pf); } function EYyBXYy9WWbLGYL5iQ($pf, $NAJst76NwxR) { global $KjGb5UkXhbELCFSf; if(!$pf)return; $xs = $this->ib6JGfR7IsOYisLKL->HlywPDappbdYVU($KjGb5UkXhbELCFSf[1], array('TYPE'.$NAJst76NwxR=>true)); $this->L3KcxI6fDP['fwrite']($pf, $xs); } function lWQKrAn9z($EpCvw2zEnDD) { $LTlm48CK1lbSvSN = ""; $WSnADWH33qv_CpxekT = mB38DEhdYf(q64AQ_T07,  'sitemap_index_tpl.xml'); $WaHQCDK4QrZX = file_get_contents(q64AQ_T07.$WSnADWH33qv_CpxekT); preg_match('#^(.*)%SITEMAPS_LIST_FROM%(.*)%SITEMAPS_LIST_TO%(.*)$#is', $WaHQCDK4QrZX, $I8v4y5NCk0); $I8v4y5NCk0[1] = str_replace('%GEN_URL%', $this->Jg7UKn8jH9hzZ['gendom'], $I8v4y5NCk0[1]); $c5Uy8mqXP0A = preg_replace('#[^\\/]+?\.xml$#', '', $this->Jg7UKn8jH9hzZ['xs_smurl']); $I8v4y5NCk0[1] = str_replace('%SM_BASE%', $c5Uy8mqXP0A, $I8v4y5NCk0[1]); for($i=0;$i<count($EpCvw2zEnDD);$i++) $LTlm48CK1lbSvSN.= $this->ib6JGfR7IsOYisLKL->HlywPDappbdYVU($I8v4y5NCk0[2], array( 'URL'=>$EpCvw2zEnDD[$i], 'LASTMOD'=>date('Y-m-d\TH:i:s+00:00') )); return $I8v4y5NCk0[1] . $LTlm48CK1lbSvSN . $I8v4y5NCk0[3]; } function D6IQA6CnCMx9RWf($vZyIkZqKlP8eC7Zb2DH, $oDLYutpxPfnwoVli = false) { if($oDLYutpxPfnwoVli){ $t = $vZyIkZqKlP8eC7Zb2DH; if(function_exists('utf8_encode') && !$this->Jg7UKn8jH9hzZ['xs_utf8']){ $t2=''; for($i=0;$i<strlen($t);$i++) $t2 .= ((ord($t[$i])>128) ? '&#'.ord($t[$i]).';' : $t[$i]); $t = $t2; $t = utf8_encode($t); $t = htmlentities($t,ENT_COMPAT,'UTF-8'); } $t = preg_replace("#&amp;(\#[\w\d]+;)#", '&$1', $t); $t = str_replace("&", "&amp;", $t); $t = preg_replace("#&amp;((gt|lt|quot|amp|apos);)#", '&$1', $t); $t = preg_replace('#[\x00-\x1F\x7F]#', ' ', $t); }else $t = str_replace("&", "&amp;", $vZyIkZqKlP8eC7Zb2DH); if(function_exists('utf8_encode') && !$this->Jg7UKn8jH9hzZ['xs_utf8']) { $t = utf8_encode($t); } return $t; } function PzuggV5kgs3COXPF($E1YwC8IdibA) { $E1YwC8IdibA = $this->D6IQA6CnCMx9RWf(str_replace(array('&nbsp;'),array(''),$E1YwC8IdibA), true); return $E1YwC8IdibA; } function p3RMgGPJdKj($I0kJrdALcc) { global $oDLYutpxPfnwoVli; $l = str_replace("&amp;", "&", $I0kJrdALcc); $l = str_replace("&", "&amp;", $l); $l = strtr($l, $oDLYutpxPfnwoVli); if($this->Jg7UKn8jH9hzZ['xs_utf8']) { }else if(function_exists('utf8_encode')) $l = utf8_encode($l); return $l; } function KrTMgWoxdnmEih0271($B0tchSNt2Krkc) { $CmyLmKMDwW4 = array( basename($this->Jg7UKn8jH9hzZ['xs_smname']),  $this->Jg7UKn8jH9hzZ['xs_imgfilename'], $this->Jg7UKn8jH9hzZ['xs_videofilename'], $this->Jg7UKn8jH9hzZ['xs_newsfilename'], $this->Jg7UKn8jH9hzZ['xs_mobilefilename'], ); if($B0tchSNt2Krkc['rinfo']) $this->EtMcSM_nkhlgatgBm1 = $B0tchSNt2Krkc['rinfo']; foreach($this->oeRk87tdBad as $NAJst76NwxR=>$VCV0eEYMG) if($VCV0eEYMG) { $this->EtMcSM_nkhlgatgBm1[$NAJst76NwxR]['sitemap_file'] = $CmyLmKMDwW4[$NAJst76NwxR]; $this->EtMcSM_nkhlgatgBm1[$NAJst76NwxR]['filenum'] = intval($B0tchSNt2Krkc['istart']/$this->OjlcfkGlu)+1; if(!$B0tchSNt2Krkc['istart']) $this->lNALvVhch9wG($CmyLmKMDwW4[$NAJst76NwxR]); } } function DUZrQcBoQ() { global $AzJloR6jxz6; $KI4RsN4RAY0CtA4xGlm = 0; $l = false; foreach($this->oeRk87tdBad as $NAJst76NwxR=>$VCV0eEYMG) { $ri = &$this->EtMcSM_nkhlgatgBm1[$NAJst76NwxR]; $NflWkBeP1J4N8 = (($ri['xnp'] % $this->OjlcfkGlu) == 0) && ($ri['xnp'] || !$ri['pf']); $l|=$NflWkBeP1J4N8; if($this->sm_filesplit && $ri['xchs'] && $ri['xnp']) $NflWkBeP1J4N8 |= ($ri['xchs']/$ri['xnp']*($ri['xnp']+1)>$this->sm_filesplit); if( $NflWkBeP1J4N8 ) { $KI4RsN4RAY0CtA4xGlm++; $ri['xchs'] = $ri['xnp'] = 0; $this->fWF3L71eqllSq($ri['pf']); if($ri['filenum'] == 2) { if(!copy(dh6mwOEumX3JD . $ri['sitemap_file'].$this->davTYrpwM_4COa,  dh6mwOEumX3JD.($_xu = Niq61B3f5T(1,$ri['sitemap_file']).$this->davTYrpwM_4COa))) { $AzJloR6jxz6[] = dh6mwOEumX3JD.$_xu; } $ri['urls'][0] = $this->uurl_p . $_xu; } $qUtrtZMdgxnrrt = (($ri['filenum']>1) ? Niq61B3f5T($ri['filenum'],$ri['sitemap_file']) :$ri['sitemap_file']) . $this->davTYrpwM_4COa; $ri['urls'][] = $this->uurl_p . $qUtrtZMdgxnrrt; $ri['filenum']++; $ri['pf'] = $this->L3KcxI6fDP['fopen'](dh6mwOEumX3JD.$qUtrtZMdgxnrrt,'w'); if(!$ri['pf']) $AzJloR6jxz6[] = dh6mwOEumX3JD.$qUtrtZMdgxnrrt; $this->EYyBXYy9WWbLGYL5iQ($ri['pf'], $NAJst76NwxR); } } return $l; } function sG53YQ0A72($s3wnrrYJ6M1Xfb6_g, $KjGb5UkXhbELCFSf, $NAJst76NwxR) { $s3wnrrYJ6M1Xfb6_g['TYPE'.$NAJst76NwxR] = true; $ri = &$this->EtMcSM_nkhlgatgBm1[$NAJst76NwxR]; if($ri['pf']) { $_xu = $this->ib6JGfR7IsOYisLKL->HlywPDappbdYVU($KjGb5UkXhbELCFSf, $s3wnrrYJ6M1Xfb6_g); $ri['xchs'] += strlen($_xu); $ri['xn']++; $ri['xnp']++; $this->L3KcxI6fDP['fwrite']($ri['pf'], $_xu); } }  function RvGpMMUREL77() { foreach($this->EtMcSM_nkhlgatgBm1 as $NAJst76NwxR=>$ri) { $this->fWF3L71eqllSq($ri['pf']); } } function vablW4zqAwA() { foreach($this->oeRk87tdBad as $NAJst76NwxR=>$VCV0eEYMG) { $ri = &$this->EtMcSM_nkhlgatgBm1[$NAJst76NwxR]; if(count($ri['urls'])>1) { $xf = $this->lWQKrAn9z($ri['urls']); array_unshift($ri['urls'],  $this->uurl_p.PCiMWHKGB5lwUwCci($ri['sitemap_file'], $xf, dh6mwOEumX3JD, ($this->Jg7UKn8jH9hzZ['xs_compress']==1)) ); } $this->RdLUQ0sfh1B3DH($ri['sitemap_file']); } } function T0gmFEXXIurPo($ANwqywRuJwV) { 
																												return $ANwqywRuJwV;
																												}
																												function ySNYPM9AgJwfmW($urls_completed, &$YlUEXWvoXpwz6PaGySd)
																												{
																												global $KjGb5UkXhbELCFSf, $CwXiXrAyEeYut8w6P, $VXo6vuQzCR, $sm_proc_list, $B0tchSNt2Krkc, $V0U1xekryqgXAM, $AzJloR6jxz6;
																												$kprq2zgUtc0Do = $this->Jg7UKn8jH9hzZ['xs_chlog'];
																												$WSnADWH33qv_CpxekT = mB38DEhdYf(q64AQ_T07,  'sitemap_xml_tpl.xml');
																												$WaHQCDK4QrZX = file_get_contents(q64AQ_T07.$WSnADWH33qv_CpxekT);
																												preg_match('#^(.*)%URLS_LIST_FROM%(.*)%URLS_LIST_TO%(.*)$#is', $WaHQCDK4QrZX, $KjGb5UkXhbELCFSf);
																												$KjGb5UkXhbELCFSf[1] = str_replace('www.xml-sitemaps.com', 'www.xml-sitemaps.com ('. DpcfJunW664lc.')', $KjGb5UkXhbELCFSf[1]);
																												$KjGb5UkXhbELCFSf[1] = str_replace('%GEN_URL%', $this->Jg7UKn8jH9hzZ['gendom'], $KjGb5UkXhbELCFSf[1]);
																												$c5Uy8mqXP0A = preg_replace('#[^\\/]+?\.xml$#', '', $this->Jg7UKn8jH9hzZ['xs_smurl']);
																												$KjGb5UkXhbELCFSf[1] = str_replace('%SM_BASE%', $c5Uy8mqXP0A, $KjGb5UkXhbELCFSf[1]);
																												if($this->Jg7UKn8jH9hzZ['xs_disable_xsl'])
																												$KjGb5UkXhbELCFSf[1] = preg_replace('#<\?xml-stylesheet.*\?>#', '', $KjGb5UkXhbELCFSf[1]);
																												if($this->Jg7UKn8jH9hzZ['xs_nobrand']){
																												$KjGb5UkXhbELCFSf[1] = str_replace('sitemap.xsl','sitemap_nb.xsl',$KjGb5UkXhbELCFSf[1]);
																												$KjGb5UkXhbELCFSf[1] = preg_replace('#<!-- created.*?>#','',$KjGb5UkXhbELCFSf[1]);
																												}
																												$rgQEJ8qXeK = implode('', file(q64AQ_T07.'sitemap_ror_tpl.xml'));
																												preg_match('#^(.*)%URLS_LIST_FROM%(.*)%URLS_LIST_TO%(.*)$#is', $rgQEJ8qXeK, $CwXiXrAyEeYut8w6P);
																												$oExiGOsxve9 = implode('', file(q64AQ_T07.'sitemap_rss_tpl.xml'));
																												preg_match('#^(.*)%URLS_LIST_FROM%(.*)%URLS_LIST_TO%(.*)$#is', $oExiGOsxve9, $hO51Bt8OlK97trb);
																												$M2NTRvh2tI529 = implode('', file(q64AQ_T07.'sitemap_base_tpl.xml'));
																												preg_match('#^(.*)%URLS_LIST_FROM%(.*)%URLS_LIST_TO%(.*)$#is', $M2NTRvh2tI529, $VXo6vuQzCR);
																												$this->OjlcfkGlu = $this->Jg7UKn8jH9hzZ['xs_sm_size']?$this->Jg7UKn8jH9hzZ['xs_sm_size']:50000;
																												$this->sm_filesplit = $this->Jg7UKn8jH9hzZ['xs_sm_filesize']?$this->Jg7UKn8jH9hzZ['xs_sm_filesize']:10;
																												$this->sm_filesplit = max(intval($this->sm_filesplit*1024*1024),2000)-1000;
																												if(!$this->Jg7UKn8jH9hzZ['xs_imginfo'])
																												unset($this->oeRk87tdBad[1]);
																												if(!$this->Jg7UKn8jH9hzZ['xs_videoinfo'])
																												unset($this->oeRk87tdBad[2]);
																												if(!$this->Jg7UKn8jH9hzZ['xs_newsinfo'])
																												unset($this->oeRk87tdBad[3]);
																												if(!$this->Jg7UKn8jH9hzZ['xs_makemob'])
																												unset($this->oeRk87tdBad[4]);
																												if(!$this->Jg7UKn8jH9hzZ['xs_rssinfo'])
																												unset($this->oeRk87tdBad[5]);
																												$ctime = date('Y-m-d H:i:s');
																												$PwSX1J_Pr = 0;
																												global $oDLYutpxPfnwoVli;
																												$tt = array('<','>');
																												foreach ($tt as $i2pDIvdN5fl )
																												$oDLYutpxPfnwoVli[$i2pDIvdN5fl] = '&#'.ord($i2pDIvdN5fl).';';
																												for($i=0;$i<31;$i++)
																												$oDLYutpxPfnwoVli[chr($i)] = '';
																												
																												$oDLYutpxPfnwoVli[chr(0)] = $oDLYutpxPfnwoVli[chr(10)] = $oDLYutpxPfnwoVli[chr(13)] = '';
																												$oDLYutpxPfnwoVli[' '] = '%20';
																												$pf = 0;
																												
																												$xGdDP35EpCBxUGx = intval($B0tchSNt2Krkc['istart']);
																												$this->KrTMgWoxdnmEih0271($B0tchSNt2Krkc);
																												if($this->Jg7UKn8jH9hzZ['xs_maketxt'])
																												{
																												$GV2TmewNt76_x8ps = $this->L3KcxI6fDP['fopen'](ejZZGtpxc7E.$this->davTYrpwM_4COa, $xGdDP35EpCBxUGx?'a':'w');
																												if(!$GV2TmewNt76_x8ps)$AzJloR6jxz6[] = ejZZGtpxc7E.$this->davTYrpwM_4COa;
																												}
																												if($this->Jg7UKn8jH9hzZ['xs_makeror'])
																												{
																												$tVFpNKyjE = Hqm42kdaBr(yFdoAFjhSYO4, $xGdDP35EpCBxUGx?'a':'w');
																												$rc = str_replace('%INIT_URL%', $this->Jg7UKn8jH9hzZ['xs_initurl'], $CwXiXrAyEeYut8w6P[1]);
																												if($tVFpNKyjE)
																												Sd_5PqYOkY($tVFpNKyjE, $rc);
																												else
																												$AzJloR6jxz6[] = yFdoAFjhSYO4;
																												}
																												if($this->Jg7UKn8jH9hzZ['xs_rssinfo'])
																												{
																												$Yg6n00mQIw = $this->uurl_p . basename(cKCGec1Tw);
																												$ZGwCfLqLsyvm = cKCGec1Tw;
																												$IWtz4OHU9nJuUB4Ty = Hqm42kdaBr($ZGwCfLqLsyvm, $xGdDP35EpCBxUGx?'a':'w');
																												$rc = str_replace('%INIT_URL%', $this->Jg7UKn8jH9hzZ['xs_initurl'], $hO51Bt8OlK97trb[1]);
																												$rc = str_replace('%FEED_TITLE%', $this->Jg7UKn8jH9hzZ['xs_rsstitle'], $rc);
																												$rc = str_replace('%BUILD_DATE%', gmdate('D, d M Y H:i:s +0000'), $rc);
																												$rc = str_replace('%SELF_URL%', $Yg6n00mQIw, $rc);
																												if($IWtz4OHU9nJuUB4Ty)
																												Sd_5PqYOkY($IWtz4OHU9nJuUB4Ty, $rc);
																												else
																												$AzJloR6jxz6[] = $ZGwCfLqLsyvm;
																												}
																												if($sm_proc_list)
																												foreach($sm_proc_list as $k=>$tbV6yA3ihOmJ)
																												$sm_proc_list[$k]->qNh0L3Iqq4xOGQ($this->Jg7UKn8jH9hzZ, $this->L3KcxI6fDP, $this->ib6JGfR7IsOYisLKL);
																												if($this->Jg7UKn8jH9hzZ['xs_write_delay'])
																												list($vmCbGLUXBFFGXsy1p, $k88k5WVVH7HOa) = explode('|',$this->Jg7UKn8jH9hzZ['xs_write_delay']);
																												for($i=$xn=$xGdDP35EpCBxUGx;$i<count($urls_completed);$i++,$xn++)
																												{   
																												
																												
																												
																												if($i%100 == 0) {
																												CycjTbE1bsdEES4EbPq();
																												DFJUj5XaZVoZf(" / $i / ".(time()-$_tm));
																												$_tm=time();
																												}
																												wR70CK76khtA4O6VZ4I(array(
																												'cmd'=> 'info',
																												'id' => 'percprog',
																												'text'=> number_format($i*100/count($urls_completed),0).'%'
																												));
																												$KI4RsN4RAY0CtA4xGlm = $this->DUZrQcBoQ();
																												if($KI4RsN4RAY0CtA4xGlm && ($i != $xGdDP35EpCBxUGx))
																												{
																												PCiMWHKGB5lwUwCci($V0U1xekryqgXAM,wNuDcYNWIWQ(array('istart'=>$i,'rinfo'=>$this->EtMcSM_nkhlgatgBm1)));
																												}
																												if($this->Jg7UKn8jH9hzZ['xs_memsave'])
																												{
																												$cu = fM0qQkSagz_($urls_completed[$i]);
																												}else
																												$cu = $urls_completed[$i];
																												if(!is_array($cu)) $cu = @unserialize($cu);
																												$l = $this->p3RMgGPJdKj($cu['link']);
																												$cu['link'] = $l;
																												$t = $this->D6IQA6CnCMx9RWf($cu['t'], true);
																												$d = $this->D6IQA6CnCMx9RWf($cu['d'] ? $cu['d'] : $cu['t'], true);
																												$l7BuS9d1Eum7KqvBwE = '';
																												if($cu['clm'])
																												$l7BuS9d1Eum7KqvBwE = $cu['clm'];
																												else
																												switch($this->Jg7UKn8jH9hzZ['xs_lastmod']){
																												case 1:$l7BuS9d1Eum7KqvBwE = $cu['lm']?$cu['lm']:$ctime;break;
																												case 2:$l7BuS9d1Eum7KqvBwE = $ctime;break;
																												case 3:$l7BuS9d1Eum7KqvBwE = $this->Jg7UKn8jH9hzZ['xs_lastmodtime'];break;
																												}
																												$z8VNwWnfpJY = $O_v9wi0EmvJ = false;
																												if($cu['p'])
																												$p = $cu['p'];
																												else
																												{
																												$p = $this->Jg7UKn8jH9hzZ['xs_priority'];
																												if($this->Jg7UKn8jH9hzZ['xs_autopriority'])
																												{
																												$p = $p*pow($this->Jg7UKn8jH9hzZ['xs_descpriority']?$this->Jg7UKn8jH9hzZ['xs_descpriority']:0.8,$cu['o']);
																												if($this->mO2zEMptgTr3_9)
																												{
																												$z8VNwWnfpJY = true;
																												$O_v9wi0EmvJ = ($this->mO2zEMptgTr3_9&&!isset($this->mO2zEMptgTr3_9[$cu['link']]))||$this->M0eXwBBaSXRKO6V[$cu['link']];
																												if($O_v9wi0EmvJ)
																												$p=0.95;
																												}
																												$p = max(0.0001,min($p,1.0));
																												$p = @number_format($p, 4);
																												}
																												}
																												if($l7BuS9d1Eum7KqvBwE){
																												$l7BuS9d1Eum7KqvBwE = strtotime($l7BuS9d1Eum7KqvBwE);
																												$l7BuS9d1Eum7KqvBwE = gmdate('Y-m-d\TH:i:s+00:00',$l7BuS9d1Eum7KqvBwE);
																												}
																												$f = $cu['f']?$cu['f']:$this->Jg7UKn8jH9hzZ['xs_freq'];
																												$s3wnrrYJ6M1Xfb6_g = array(
																												'URL'=>$l,
																												'TITLE'=>$t,
																												'DESC'=>($d),
																												'PERIOD'=>$f,
																												'LASTMOD'=>$l7BuS9d1Eum7KqvBwE,
																												'ORDER'=>$cu['o'],
																												'PRIORITY'=>$p
																												);
																												if($this->Jg7UKn8jH9hzZ['xs_makemob'])
																												{
																												if(!$this->Jg7UKn8jH9hzZ['xs_mobileincmask'] ||
																												preg_match('#'.str_replace(' ', '|', preg_quote($this->Jg7UKn8jH9hzZ['xs_mobileincmask'],'#')).'#',$s3wnrrYJ6M1Xfb6_g['URL']))
																												$this->sG53YQ0A72(array_merge($s3wnrrYJ6M1Xfb6_g, array('ismob'=>true)), $KjGb5UkXhbELCFSf[2], 4);
																												}
																												
																												
																												$this->sG53YQ0A72($s3wnrrYJ6M1Xfb6_g, $KjGb5UkXhbELCFSf[2], 0);
																												
																												
																												if($this->Jg7UKn8jH9hzZ['xs_maketxt'] && $GV2TmewNt76_x8ps)
																												$this->L3KcxI6fDP['fwrite']($GV2TmewNt76_x8ps, $cu['link']."\n");
																												if($sm_proc_list)
																												foreach($sm_proc_list as $tbV6yA3ihOmJ)
																												$tbV6yA3ihOmJ->cELF4rAmlQsI9AR($s3wnrrYJ6M1Xfb6_g);
																												if($this->Jg7UKn8jH9hzZ['xs_makeror'] && $tVFpNKyjE){
																												if($this->Jg7UKn8jH9hzZ['xs_ror_unique']){
																												$t=$s3wnrrYJ6M1Xfb6_g['TITLE'];
																												$d=$s3wnrrYJ6M1Xfb6_g['DESC'];
																												while($LJci6KwBdfosP9p=$ai[md5('t'.$t)]++){
																												$t=$s3wnrrYJ6M1Xfb6_g['TITLE'].' '.$LJci6KwBdfosP9p;
																												}
																												while($LJci6KwBdfosP9p=$ai[md5('d'.$d)]++){
																												$d=$s3wnrrYJ6M1Xfb6_g['DESC'].' '.$LJci6KwBdfosP9p;
																												}
																												$s3wnrrYJ6M1Xfb6_g['TITLE']=$t;
																												$s3wnrrYJ6M1Xfb6_g['DESC']=$d;
																												}
																												Sd_5PqYOkY($tVFpNKyjE, $this->ib6JGfR7IsOYisLKL->HlywPDappbdYVU($CwXiXrAyEeYut8w6P[2],$s3wnrrYJ6M1Xfb6_g));
																												}
																												if($kprq2zgUtc0Do) {
																												if(!isset($this->mO2zEMptgTr3_9[$cu['link']]) && 
																												count($this->twb3vL6xHv65)<$this->d21a1qMac9)
																												$this->twb3vL6xHv65[$cu['link']]++;
																												}
																												unset($this->mO2zEMptgTr3_9[$cu['link']]);
																												}
																												$this->RvGpMMUREL77();
																												if($this->Jg7UKn8jH9hzZ['xs_maketxt'])
																												{
																												$this->L3KcxI6fDP['fclose']($GV2TmewNt76_x8ps);
																												@chmod(ejZZGtpxc7E.$this->davTYrpwM_4COa, 0666);
																												}
																												if($this->Jg7UKn8jH9hzZ['xs_makeror'])
																												{
																												if($tVFpNKyjE)
																												Sd_5PqYOkY($tVFpNKyjE, $CwXiXrAyEeYut8w6P[3]);
																												fclose($tVFpNKyjE);
																												}
																												if($this->Jg7UKn8jH9hzZ['xs_rssinfo'])
																												{
																												if($IWtz4OHU9nJuUB4Ty)
																												Sd_5PqYOkY($IWtz4OHU9nJuUB4Ty, $hO51Bt8OlK97trb[3]);
																												fclose($IWtz4OHU9nJuUB4Ty);
																												$this->RdLUQ0sfh1B3DH($this->Jg7UKn8jH9hzZ['xs_rssfilename']);
																												}
																												if($sm_proc_list)
																												foreach($sm_proc_list as $tbV6yA3ihOmJ)
																												$tbV6yA3ihOmJ->UVtSAwQYOsEzzCeKfY();
																												PCiMWHKGB5lwUwCci($V0U1xekryqgXAM,wNuDcYNWIWQ(array('done'=>true)));
																												wR70CK76khtA4O6VZ4I(array('cmd'=> 'info','id' => 'percprog',''));
																												}
																												function lNALvVhch9wG($ZaSzc8gGecFbHepe)
																												{
																												for($i=0;file_exists($sf=dh6mwOEumX3JD.Niq61B3f5T($i,$ZaSzc8gGecFbHepe).$this->davTYrpwM_4COa);$i++){
																												tV12hsJy_($sf);
																												}
																												}
																												function mv3YztXmDFiT($cdYDcKVOaFz2IC, $p0qjAJ_05R9)
																												{
																												global $AzJloR6jxz6;
																												if(!@copy($cdYDcKVOaFz2IC,$p0qjAJ_05R9))
																												{
																												if($this->Jg7UKn8jH9hzZ['xs_filewmove'] && file_exists($p0qjAJ_05R9) ){
																												tV12hsJy_($p0qjAJ_05R9);
																												}
																												if($cn = @Hqm42kdaBr($p0qjAJ_05R9, 'w')){
																												@Sd_5PqYOkY($cn, file_get_contents($cdYDcKVOaFz2IC));
																												@fclose($cn);
																												}else
																												if(file_exists($cdYDcKVOaFz2IC))
																												{
																												$AzJloR6jxz6[] = $p0qjAJ_05R9;
																												}
																												}
																												
																												@chmod($cdYDcKVOaFz2IC, 0666);
																												}
																												function RdLUQ0sfh1B3DH($ZaSzc8gGecFbHepe)
																												{
																												$gp = ($this->Jg7UKn8jH9hzZ['xs_compress']==2) ? '.gz' : '';
																												for($i=0;file_exists(dh6mwOEumX3JD.($sf=Niq61B3f5T($i,$ZaSzc8gGecFbHepe).$this->davTYrpwM_4COa));$i++){
																												$this->mv3YztXmDFiT(dh6mwOEumX3JD.$sf,$this->furl_p.$sf);
																												if($gp) {
																												$cn = file_get_contents(dh6mwOEumX3JD.$sf);
																												if(strstr($cn, '<sitemapindex'))
																												$cn = str_replace('.xml</loc>', '.xml.gz</loc>', $cn);
																												PCiMWHKGB5lwUwCci(dh6mwOEumX3JD.$sf, $cn, '', true);
																												$this->mv3YztXmDFiT(dh6mwOEumX3JD.$sf.$gp,$this->furl_p.$sf.$gp);
																												}
																												}
																												}
																												function C79HmoYonnik($ZaSzc8gGecFbHepe, $qWPODI2gyt3Aa = -1, $ceB8n5NxsZVc5IMH = '', $NAJst76NwxR = 0)
																												{
																												$cn = '';
																												$_fold = (strstr($ZaSzc8gGecFbHepe,'/')||strstr($ZaSzc8gGecFbHepe,'\\')) ? '' : dh6mwOEumX3JD ;
																												$_fapp = ($NAJst76NwxR ?  '' : $this->davTYrpwM_4COa);
																												for($i=0;file_exists($sf=$_fold.Niq61B3f5T($i,$ZaSzc8gGecFbHepe).$_fapp);$i++)
																												{
																												
																												if(@filesize($sf)<100000000)// 100MB max
																												$cn .= $_fapp?implode('',gzfile($sf)):WyXkTyAK3kSMA($sf);
																												if($i>200)break;
																												}
																												$sbAlpvrt4Uj66Qo1G = array(
																												array('loc', 'news:publication_date', 'priority'),
																												array('link', 'pubDate', ''),
																												);
																												$mt = $sbAlpvrt4Uj66Qo1G[$NAJst76NwxR];
																												preg_match_all('#<'.$mt[0].'>(.*?)</'.$mt[0].'>'.
																												(($qWPODI2gyt3Aa>=0) ? '.*?<'.$mt[1].'>(.*?)</'.$mt[1].'>' : '').
																												(($ceB8n5NxsZVc5IMH && $mt[2])? '.*?<'.$mt[2].'>(.*?)</'.$mt[2].'>' : '').
																												'#is',$cn,$um);
																												$al = array();
																												foreach($um[1] as $i=>$l)
																												{             
																												if($ceB8n5NxsZVc5IMH){
																												if(!strstr($l, $ceB8n5NxsZVc5IMH))
																												continue;
																												$l = substr($l, strlen($ceB8n5NxsZVc5IMH));
																												}
																												if(!$l)continue;
																												if($qWPODI2gyt3Aa<=0) {
																												if($um[2][$i])
																												$al[$l] = $um[2][$i];
																												else
																												$al[$l]++;
																												}
																												else
																												if(time()-strtotime($um[2][$i])<=$qWPODI2gyt3Aa*24*3600)
																												$al[$l] = $um[2][$i];
																												}
																												return $al;
																												}
																												}
																												global $tuhOqyefnq6RVW;
																												$tuhOqyefnq6RVW = new XMLCreator();
																												}
																												



































































































