<!DOCTYPE html
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<head>
<title>EDGE^3</title>
</head>
<body>
<h3><font color="blue">Instructions for more complex Limma analyses</font></h3>
Limma utilizes linear modeling to determine differentially expressed genes.<br>
Limma requires that two matrices be specified:
<ol>
<li>Design: Used for indicating which RNA samples have been applied to which arrays</li>
<li>Contrast: Used for specifying what comparisons you intend to make between RNA samples</li>
</ol>
<p style="width:600px">

In order for you to create a more complex query, you will need to have a good understanding
of the creation of these two matrices.  This can be obtained by thorough reading of the 
<a href="./Instructions/limmausersguide.pdf" target="_blank">Limma User Guide</a>.
</p>
<p style="width:600px">
The microarray design utilizing a reference sample is quite
straightforward to analyze and the interface EDGE<sup>3</sup> utilizes is fairly robust
in terms of its ability to deal with this design type.  However, more complex analyses
are more difficult to construct with a general solution.  Thus, we've made it possible
to manually create the design and contrasts matrices.  This requires more of an understanding of the statistical
aspects of the underlying algorithm, but allows for greater flexibility and control when analyzing your data.
</p>
<p style="width:600px">
There are variable assignments that we require.
First, you must fit the model using the specified variable for the data matrix, <font color='green'><strong>dat.m</strong></font>.
Second, you must utilize <font color='red'><strong>eb</strong></font> as the variable name for the Emperical Bayes step.  You can see refer to the code below to get
a better idea of where they are used as the variables are color coded there.
</p>
<p style="width:600px">
As with the reference-based designs the targets file is
still created by using the information from the table where you designate the RNA samples.  The targets file is used to
designate the RNA Samples/File associations allowing for R to load the data files.
</p>
<p style="width:600px">
    Below is code that creates the design and contrasts matrices for a microarray experiment using a
    reference design.  It has three RNA samples: sample1, sample2 and sample3.  The samples are designated with numbers on the form, but the names are prepended with '<i>sample</i>' when the R code is generated.
    Please use this convention in your code otherwise the algorithm will fail.  Also, use single quotes (') instead of double quotes (").  In this example the reference sample is designated in the creation of the design
    matrix.    The comparison of interest
    is between sample3 and sample2.  Any code you enter will replace this default code generating the comparisons for the reference
    design.  
</p>
    
# STATISTICAL ANALYSES<br>
# CREATE THE DESIGN MATRIX FROM THE TARGETS FILE<br>

design<-modelMatrix(targets, ref='sample1')<br>
# EMPERICAL BAYES W/ UNFILTERED DATA<br>
# BUILD THE LINEAR MODEL<br>
fit<-lmFit(<font color='green'><strong>dat.m</strong></font>,design)<br>
# CREATE THE CONTRASTS MATRIX.<br>
contrasts.matrix<-makeContrasts(sample3-sample2,levels=design)<br>
fit2<-contrasts.fit(fit,contrasts.matrix)<br>
# PERFORM EMPERICAL BAYES<br>
<font color='red'><strong>eb</strong></font><-eBayes(fit2)<br>
</body>
</html>