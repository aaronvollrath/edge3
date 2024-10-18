# LOAD THE REQUIRED LIBRARIES
library(limma)
library(amap)
library(gplots)
library(RColorBrewer)
library(genefilter)
library(multtest)
targets <- readTargets(file="c:/xampp/htdocs/edge3/dataoutputfiles/targets18388.txt")
filelist<-targets$FileName
# FOR EASE OF USE, USE MAIMAGES TO LOAD THE DATA FILES
dat<-read.maimages(filelist,source="agilent", columns=list(G="gMeanSignal", Gb="gBGMedianSignal", R="gMeanSignal", Rb="rBGMedianSignal",logratio="LogRatio",control="ControlType"),annotation=c("FeatureNum","GeneName", "SystematicName"))
colnames(dat)<-targets$Labels
# EXTRACT OUT THE LOG RATIOS
array1<-dat$logratio[,1]
array2<-dat$logratio[,2]
array3<-dat$logratio[,3]
array4<-dat$logratio[,4]
array5<-dat$logratio[,5]
array6<-dat$logratio[,6]
# CREATE A DATA FRAME W/ THE LOG RATIOS AND THE CONTROLS
dat3<-data.frame(array1,array2,array3,array4,array5,array6,controls = dat$control[,1])
# EXTRACT OUT THE NON-CONTROL VALUES
array1real<-dat3$array1[dat3$controls != 1]
array2real<-dat3$array2[dat3$controls != 1]
array3real<-dat3$array3[dat3$controls != 1]
array4real<-dat3$array4[dat3$controls != 1]
array5real<-dat3$array5[dat3$controls != 1]
array6real<-dat3$array6[dat3$controls != 1]
controls<-dat3$controls[dat3$controls != 1]
# CREATE A DATA FRAME W/ THE NON-CONTROL VALUES, THE ARRAY NAMES ARE TO THE LEFT OF THE '=' SIGN
dat5 <-data.frame(array1=array1real,array2=array2real,array3=array3real,array4=array4real,array5=array5real,array6=array6real)
# ASSIGN ROW NAMES
rownames(dat5)<-paste(dat$genes$FeatureNum[dat$control[,1] != 1],dat$genes$GeneName[dat$control[,1]!=1],sep="_")
# CONVERT TO LOG-BASE 2
dat5<-dat5/log10(2)
# CREATE A DAT.M MATRIX BY CONVERTING DAT5 TO A MATRIX
dat.m = as.matrix(dat5)
colnames(dat.m)<-targets$Labels
heatcol<-rev(colorRampPalette(brewer.pal(10,"Spectral"))(1024))
# STATISTICAL ANALYSES
design<-modelMatrix(targets, ref='sample1')
# EMPERICAL BAYES W/ UNFILTERED DATA
# BUILD THE LINEAR MODEL
fit<-lmFit(dat.m,design)
contrasts.matrix<-makeContrasts(sample2,levels=design)
fit2<-contrasts.fit(fit,contrasts.matrix)
# PERFORM EMPERICAL BAYES
eb<-eBayes(fit2)
cat("<h1>Results</h1>", file="c:/xampp/htdocs/edge3/dataoutputfiles/18388.html", append=TRUE)
cat("<div id='tabs' style='width: 700px; float:left'><ul>", file="c:/xampp/htdocs/edge3/dataoutputfiles/18388.html", append=TRUE)
cat("<li><a href=\"#fragment-1\"><span>corn oil vs tcdd</span></a></li>", file="c:/xampp/htdocs/edge3/dataoutputfiles/18388.html", append=TRUE)
cat("</ul>", file="c:/xampp/htdocs/edge3/dataoutputfiles/18388.html", append=TRUE)
cat("<div id=\"fragment-1\">", file="c:/xampp/htdocs/edge3/dataoutputfiles/18388.html", append=TRUE)
# GET THE TOP TABLE FOR coef #$
options(digits=3)
tt<-topTable(eb,coef=1,n=nrow(dat.m), adjust="fdr",genelist=fit$genes)
write.table(tt,sep="	",file="c:/xampp/htdocs/edge3/dataoutputfiles/18388orderedgenes_1.txt",quote=FALSE,row.names=FALSE)
rn<-rownames(tt)[tt$P.Value<=0.01]
dat.s<-dat.m[rn,]
write.table(dat.s,sep="	",file="c:/xampp/htdocs/edge3/dataoutputfiles/18388genelist_1.txt",quote=FALSE,row.names=TRUE,col.names=TRUE)
#Colored Volcano plot
significants = tt$P.Value <=0.01
nonsignificants = tt$P.Value > 0.01
upregulated = tt$logFC > 0 & tt$P.Value <=0.01
downregulated = tt$logFC < 0 & tt$P.Value<=0.01
maxAbsM <- max(abs(tt$logFC), na.rm=TRUE)
logP <-log10(tt$P.Value)
maxY <- min(c(-min(logP, na.rm=TRUE),10))
midpoint <--log10(0.01)
# Declare the createLegend function
createLegend <- function(names, colors, location="bottomright"){
	legend(location, names, bty="n",cex=0.8, pt.bg="white",lty=1,col=colors,lwd=5)
}
# END createLegend function
bcount <- function(x,na.rm=F) {
# Performs a "boolean count", that is, counts the number of TRUE's in a vector.
# NJ Barrowman, May 24, 1993. Modified by J. Hoenig, 1 June 2007 so that, if there 
# are any NAs, the result is NA unless na.rm is set to TRUE
	if (length(x)==0) {return(0)}
if (!is.logical(x)) {stop("Not a logical vector.")  }
if (na.rm == F & sum(is.na(x))>0) {return(NA)}
return(length(x[!is.na(x) & (x==T)]))}
numberofsignificantvalues = bcount(significants)
png(file="c:/xampp/htdocs/edge3/dataoutputfiles/18388coloredvolcanoplot1.png", width=800,height=800)
plot(tt$logFC[nonsignificants], -logP[nonsignificants] , pch=16, cex=0.2, xlab="log2(fold-change)",
ylab="-log10(p-value)",ylim=c(0, maxY), xlim=c(-maxAbsM, maxAbsM), main="Volcano Plot",col="grey")
abline(midpoint,0, col="grey", lty=2)
abline(v=0,col="grey", lty=2)
points(tt$logFC[upregulated], -logP[upregulated] , pch=16, cex=0.6, col="red")
points(tt$logFC[downregulated], -logP[downregulated] , pch=16, cex=0.6, col="green")
text(maxAbsM-1,-log10(.001) + .05,"p-Value = 0.01", cex=.6,col="blue")
createLegend(c("Upregulated & Differentially Expressed","Downregulated & Differentially Expressed"), c("red","green"), location="bottomleft")
dev.off()
ttcolnames <- names(tt)
cat("The ",numberofsignificantvalues, " genes designated as being differentially expressed; sorted by B-statistics.<br> ", file="c:/xampp/htdocs/edge3/dataoutputfiles/18388.html", append=TRUE)
cat("Here is the file containing all of the ranked genes: <a href='./dataoutputfiles/18388orderedgenes_1.txt' target='_blank'>corn oil vs tcdd</a><br>", file="c:/xampp/htdocs/edge3/dataoutputfiles/18388.html", append=TRUE)
cat("Here is a file containing the differentially expressed genes: <a href='./dataoutputfiles/18388genelist_1.txt' target='_blank'>corn oil vs tcdd differentially expressed</a><br>",file="c:/xampp/htdocs/edge3/dataoutputfiles/18388.html", append=TRUE)
cat("Save the set of differentially expressed genes as a EDGE<sup>3</sup> gene list? <a href='./phpinc/importgenelistresult.inc.php?type=0&featurefilenumber=18388&querytype=1&contrastnumber=1&name=corn oil vs tcdd' target='_blank'>Save gene list</a><br>", file="c:/xampp/htdocs/edge3/dataoutputfiles/18388.html",append=TRUE)
cat("<table width=600 border='1' frame='border' rules='none'>", file="c:/xampp/htdocs/edge3/dataoutputfiles/18388.html", append=TRUE)
cat("<tr bgcolor='ddddff'>", file="c:/xampp/htdocs/edge3/dataoutputfiles/18388.html", append=TRUE)
for(j in 1:ncol(tt)){
	cat("<td align='center'><b>",ttcolnames[j],"</b></td>",file="c:/xampp/htdocs/edge3/dataoutputfiles/18388.html",append=TRUE)
	}
cat("</tr>",file="c:/xampp/htdocs/edge3/dataoutputfiles/18388.html",append=TRUE)
#Get the rownames (i.e., genes and feature numbers) from tt
ttrownames = rownames(tt)
for(i in 1:numberofsignificantvalues){
cat("<tr bgcolor=''>", file="c:/xampp/htdocs/edge3/dataoutputfiles/18388.html", append=TRUE)
featurevals <- unlist(strsplit(ttrownames[i], "_"))
featurenum <- featurevals[1]
featurename <- featurevals[2]
		cat("<td><a href='./agilentfeatureinfo.php?featurenum=
",featurenum,"' target='_blank'>",featurename,"</a></td>",file="c:/xampp/htdocs/edge3/dataoutputfiles/18388.html",append=TRUE)
for(j in 1:ncol(tt)){
		cat("<td>",tt[i,j],"</td>",file="c:/xampp/htdocs/edge3/dataoutputfiles/18388.html",append=TRUE)
}
cat("</tr>",file="c:/xampp/htdocs/edge3/dataoutputfiles/18388.html",append=TRUE)
}
cat("</table>", file="c:/xampp/htdocs/edge3/dataoutputfiles/18388.html", append=TRUE)
cat("<h2>Volcano Plot</h2>", file="c:/xampp/htdocs/edge3/dataoutputfiles/18388.html", append=TRUE)
cat("<img src='./dataoutputfiles/18388coloredvolcanoplot1.png'><p>", file="c:/xampp/htdocs/edge3/dataoutputfiles/18388.html", append=TRUE)
cat("</div>", file="c:/xampp/htdocs/edge3/dataoutputfiles/18388.html", append=TRUE)
