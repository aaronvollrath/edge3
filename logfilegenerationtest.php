<?php
function utime (){
$time = explode( " ", microtime());
$usec = (double)$time[0];
$sec = (double)$time[1];
return $sec + $usec;
}
$start = utime();
require 'globalfilelocations.inc';
require 'utilityfunctions.inc';
	require 'edge_db_connect2.php';
	$filenum = rand(0, 25000);
	
	$logratiovalues = "$IMAGESdir/datalogvalues$filenum.txt";
	$rtestfile = "$IMAGESdir/Rtest$filenum.R";
	
	$command = "touch $logratiovalues";
	$str=exec($command);
	$logfd = fopen($logratiovalues,'w');
	$command = "touch $rtestfile";
	$str=exec($command);
	$fd = fopen($rtestfile,'w');
	
	// testing the speed of writing to file....
	
	$featurenumSQL = "SELECT FeatureNum, GeneName, SystematicName FROM agilentdata WHERE ControlType = 0 AND arrayid = 518 ORDER BY FeatureNum ASC";
	$dataset = 1;
	$arrayidarray = array();
	$arrayidarray[0] = 518;
	$arrayidarray[1] = 519;
	$arrayidarray[2] = 538;
	$arrayidarray[3] = 533;
	$arrayidarray[4] = 534;
	$arrayidarray[5] = 539;
	$files = "";
	$datafilecount = 0;


	fwrite($fd, "library(limma)\n");
	fwrite($fd, "library(amap)\n");
	fwrite($fd, "library(gplots)\n");
	fwrite($fd, "library(RColorBrewer)\n");
	fwrite($fd, "library(genefilter)\n");
	$filelist = "filelist<-c(";
				$logfileheader="";
				$filecount = 0;
	$colnames = "arraynames<-c(";
	$arrayidcount = 0;
				foreach($arrayidarray as $anID){
					if($anID > 0){
						
						$idSQL = "SELECT arraydesc FROM agilent_arrayinfo WHERE arrayid = $anID";
						$result = $db->Execute($idSQL);//mysql_query($idSQL, $db);
						$iddesc = $result->FetchRow();//mysql_fetch_row($result);
						$arraydesc = $iddesc[0];
						$logfileheader.="\t$iddesc[0]";
						if($arrayidcount ==0){
							$colnames .= "\"$arraydesc\"";
						}else{
							$colnames .= ",\"$arraydesc\"";
						}
						$arrayidcount++;
						$sql = "SELECT FE_data_file FROM agilent_arrayinfo WHERE arrayid = $anID";
						$fileResult = $db->Execute($sql);
						if($fileResult){
							$row2 = $fileResult->FetchRow();
							$filename = $row2[0];
							$filename = returndatafile($filename,$datafilelocation,$edgedata,TRUE,1);
							if($filename != ""){
								if($filecount == 0){
									$filelist.= "\"$filename\"";
								}else{
									$filelist .=",\"$filename\"";
								}
								$filecount++;
								$files .= $filename." ";
								
								$datafilecount++;
							}else{
								die("ERROR: File not in system.  Please contact the administrator");
							}
							
							
						}else{
							die("Error in sql: $sql");
						}
						$line = "$anID,$iddesc,$filename\n";
						#fwrite($fd,$line);
						}
					
				}
				$filelist .= ")\n";
				fwrite($fd, $filelist);
				$colnames .= ")\n";
				fwrite($fd, "dat<-read.maimages(filelist,source=\"agilent\", columns=list(G=\"gMeanSignal\", Gb=\"gBGMedianSignal\", R=\"gMeanSignal\", Rb=\"rBGMedianSignal\",logratio=\"LogRatio\",control=\"ControlType\"),annotation=c(\"FeatureNum\",\"GeneName\", \"SystematicName\"))\n");
				fwrite($fd,$colnames);
				fwrite($fd, "colnames(dat)<-arraynames\n");
	$datm = "dat.m = -dat";
	$datm.= "\$";
	$datm.= "logratio\n";
	
	fwrite($fd, $datm);
	fwrite($fd, "rownames(dat.m) <-dat\$genes\$GeneName\n");
	fwrite($fd, "#genes with 4-fold greater change\n");
	fwrite($fd, "ff<-pOverA(A=1.2,p=0.5)\n");
	fwrite($fd, "i<-genefilter(dat.m,ff)\n");
	fwrite($fd, "dat.fo<-dat.m[i,]\n");
	fwrite($fd, "i<-genefilter(-dat.m,ff)\n");
	fwrite($fd, "dat.fu<-dat.m[i,]\n");
	fwrite($fd, "dat.f<-rbind(dat.fo,dat.fu)\n");
	fwrite($fd, "clust.genes<-hcluster(x=dat.f, method=\"pearson\",link=\"average\")\n");
	fwrite($fd, "clust.arrays<-hcluster(x=t(dat.f), method=\"pearson\", link=\"average\")\n");
	fwrite($fd, "png(file=\"$filenum.myplot.png\", bg=\"white\", width=1024,height=1024, pointsize=12)\n");
	fwrite($fd, "heatmap.2(x=dat.f, Rowv=as.dendrogram(clust.genes),Colv=as.dendrogram(clust.arrays), col=heatcol)\n");
	fwrite($fd, "dev.off()\n");

				fflush($fd);
				fclose($fd);
				$logfileheader.="\n";
				fflush($logfd);
				fwrite($logfd,$logfileheader);
				#fclose($logfd);
				#die("exiting after writing log file");


			
				
					echo "$featurenumSQL <br>";
			
				


				/*

				$cloneidarray = array();
				$cloneContainer = array();
				$geneNameContainer = array();
				$systematicNameContainer = array();
				$featureNumResult = $db->Execute($featurenumSQL);//mysql_query($featurenumSQL, $db);
				$cloneCount = 0;
				//while($cloneRow = mysql_fetch_row($featureNumResult)){

				while($cloneRow = $featureNumResult->FetchRow()){
					$thisline = "";
					$cloneid=$cloneRow[0];
					array_push($cloneContainer, $cloneid);
					$cloneCount++;
					if($dataset == 1){
     						$genename = str_replace("\"","", $cloneRow[1]);
						$genename = trim(ucfirst ( $genename));
						$systematicname = trim($cloneRow[2]);
						if($systematicname == ""){
							$genename = $systematicname;
						}
					}else{ // using condensed data....
						die('condensed data not supported with this function at the moment.');
					}
					$thisline .= $cloneid."_".$systematicname;
					
					foreach($arrayidarray as $arrayidval){
						if($arrayidval != -99){
							$featuresql = "SELECT d.LogRatio, d.gProcessedSignal, d.rProcessedSignal from agilentdata as d where d.FeatureNum = $cloneid and arrayid = $arrayidval";
							$aResult = $db->Execute($featuresql);
							$pnRow = $aResult->FetchRow();
							$value = $pnRow[0];
							$thisline.= "\t$value";
						}
					}
					$thisline.= "\n";
					fwrite($logfd, $thisline);
					
				}
				*/
				fclose($logfd);
					echo $str;
	$end = utime(); $run = $end - $start;

	echo "<br><font size=\"1px\"><b>Query results returned in ";
	echo substr($run, 0, 5);
	echo " secs.</b></font>";
				die("exiting after writing log file");

?>