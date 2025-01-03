import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.FileNotFoundException;
import java.io.FileReader;
import java.io.FileWriter;
import java.io.IOException;
import java.util.Arrays;
import java.util.BitSet;
import java.util.Vector;
import Jama.Matrix;

/**
 * Aaron Vollrath
 * BMI576
 *
 * <p>Title: k-Means Clustering Program</p>
 * <p>Description: </p>
 * <p>Copyright: Copyright (c) 2003</p>
 * <p>Company: </p>
 *
 * @version 1.0
 */

public class Cluster {

    // This is the vector that is used to store the Instance objects
    // created from the input file...
    Vector dataArray;

    // This is the vector that is used to store the Instance objects for the
    // Chemicals (used in the hierarchical clustering)
    Vector chemDataArray;

    // This variable stores the number of blank ids....
    static int blankIDs;

    // Used to sort the clusters when comparing....
    QSortAlgorithm qSort;

    final int numCols = 4;
    static int k;

    final static int squareSize = 20;
    /*  change these values to account for overflow of image size
	i removed the final from squareWidth and squareHeight
	i added the variable imageHeightExceeded as a boolean check value.
    */
    static int squareWidth = 20;
    static int squareHeight = 10;
    static int xMargin = 100;
    static boolean imageHeightExceeded = false;
    static int arrays;

    static int clustAlg;
    static int svgDendroLine = 1;
    static int svgChemCol = 1;

    static Vector clusterObjVector;

    static BufferedWriter bw;

    static BufferedWriter bw2;
    static String file;

    static String filenum;
    
    static String arrayURL;
    
    static String featureURL;

    static int maxX;

    static String svgFile;
    static String tableFile;
    static String outputDirectory;

    static Vector svgFileVector;
    static Vector tableFileVector;
    static Vector geneDendrogramSVGVector;
    static Vector chemDendrogramSVGVector;

    static int colorScheme;
    static int trxClusterOption;
    static int browser; // 0 if IE or 1 if non-IE

    static Vector trxNames;

    static Vector trxIDs;

    static boolean kmeans;
    static boolean ordered;

    Matrix A;


    public Cluster() {
        // This is the vector of Instances for genes....
        dataArray = new Vector();
        clusterObjVector = new Vector();
        qSort = new QSortAlgorithm();
        trxNames = new Vector();
        trxIDs = new Vector();
        blankIDs = 0;
    }


    public static void main(String[] args) {
        if (args.length < 2) {
            args = new String[10];
            ////System.out.println("You did not enter the correct number of arguments.");
            args[0] = "/var/www/html/edge2/data2.txt";
            args[1] = "4";
            args[2] = "4";
            args[3] = "/var/www/html/edge2/output.svg";
            args[4] = "1";  // ClusterAlgo Value
            args[5] = "/var/www/html/edge2/table.html";
            args[6] = "0";
            args[7] = "2";
            args[8] = "1";
            args[9] = "3343423";
            // "java -mx512m -jar Cluster3.jar $file $numberofclusters $arrayidCount $svgFile $algo $tableFile $colorscheme 2 $browserval";
            //java -mx512m -jar edgecluster.jar /home/vollrath/workspace/data2.txt 4 4 /home/vollrath/workspace/output.svg /home/vollrath/workspace/table.html 0 2 1
        }
        ////System.out.println("Before everything...");
        //blankIDs = new Vector();
        kmeans = false;
        Cluster cluster1 = new Cluster();
        file = args[0];
        Integer arrayVal = new Integer(args[2]);
        arrays = arrayVal.intValue();
        svgFile = args[3];
        Integer clusterAlgo = new Integer(args[4]);
        tableFile = args[5];
        clustAlg = clusterAlgo.intValue();

        cluster1.getData(file);
        svgFileVector = new Vector();
        tableFileVector = new Vector();

        ////System.out.println("After getting Data");
        Double kVal = new Double(args[1]);
        k = kVal.intValue();

        Integer colorscheme = new Integer(args[6]);
        colorScheme = colorscheme.intValue();

        Integer trxClOption = new Integer(args[7]);
        trxClusterOption = trxClOption.intValue();

        Integer browserOption = new Integer(args[8]);
        browser = browserOption.intValue();
        // Setting the filenum value....
        filenum = args[9];
        outputDirectory = args[10];

        if (clustAlg == 0) {
            kmeans = true;
        }
	if (clustAlg == -1){
		ordered = true;
		System.out.println("ordered option selected");
	}
	if(ordered){
		Vector orderedCluster = new Vector();
		// Need to just display this as one unordered cluster...
		for (int count = 0; count < cluster1.dataArray.size(); count++) {
			//	//System.out.println("Before adding dataArray " + count);
			orderedCluster.add((Instance) cluster1.dataArray.elementAt(count));
			////System.out.println("After adding dataArray " + count);
		}
            //  //System.out.println("Before creating the new ClusterObject");
            ClusterObject newCluster = new ClusterObject(0, orderedCluster,
                    browser);
            ////System.out.println("After creating the new ClusterObject");
            clusterObjVector.add(newCluster);
		cluster1.displayOrderedList();
		try {

			bw = new BufferedWriter(new FileWriter(svgFile));




			for (int i = 0; i < svgFileVector.size(); i++) {
			String line = (String) svgFileVector.elementAt(i);
			bw.write(line);
			}
			bw.flush();
			bw.close();
				////System.out.println("Table File :"  + tableFile);
			bw2 = new BufferedWriter(new FileWriter(tableFile));
			for (int i = 0; i < tableFileVector.size(); i++) {
			String line = (String) tableFileVector.elementAt(i);
			bw2.write(line);
			//  //System.out.println(line);
			}
			bw2.flush();
			bw2.close();
		} catch (FileNotFoundException fnfe) {
			System.out.println("EXECUTION HALTED: svg File " + svgFile +
					" does not exist!");
			System.exit(0);
		} catch (IOException ioe) {
			System.out.println("ERROR: " + ioe);
		}
	}else{
        	if (kmeans == true) {

		////System.out.println("Before initializing clusters...");
		cluster1.initializeClusters();

		// //System.out.println("Before kMeans...");
		cluster1.k_MeansCluster();
		// //System.out.println("Before sorting...");
		cluster1.sortClusters();
		////System.out.println("Before displayClusterMembers()");
		cluster1.displayClusterMembers();

		try {

			bw = new BufferedWriter(new FileWriter(svgFile));




			for (int i = 0; i < svgFileVector.size(); i++) {
			String line = (String) svgFileVector.elementAt(i);
			bw.write(line);
			}
			bw.flush();
			bw.close();
				////System.out.println("Table File :"  + tableFile);
			bw2 = new BufferedWriter(new FileWriter(tableFile));
			for (int i = 0; i < tableFileVector.size(); i++) {
			String line = (String) tableFileVector.elementAt(i);
			bw2.write(line);
			//  //System.out.println(line);
			}
			bw2.flush();
			bw2.close();
		} catch (FileNotFoundException fnfe) {
			System.out.println("EXECUTION HALTED: svg File " + svgFile +
					" does not exist!");
			System.exit(0);
		} catch (IOException ioe) {
			System.out.println("ERROR: " + ioe);
		}
		} else {
		geneDendrogramSVGVector = new Vector();
		chemDendrogramSVGVector = new Vector();
		clusterObjVector = cluster1.avgLink_HeirarchicalCluster();

		try {
			bw = new BufferedWriter(new FileWriter(svgFile));
			for (int i = 0; i < svgFileVector.size(); i++) {
			String line = (String) svgFileVector.elementAt(i);
			bw.write(line);
			}
			bw.flush();
			bw.close();
			////System.out.println("The size of dendro vector: " + geneDendrogramSVGVector.size());
			// Output the table.....
			////System.out.println("Table File :"  + tableFile);
			bw2 = new BufferedWriter(new FileWriter(tableFile));
			for (int i = 0; i < tableFileVector.size(); i++) {
			String line = (String) tableFileVector.elementAt(i);
			bw2.write(line);
			//  //System.out.println(line);
			}
			bw2.flush();
			bw2.close();
		} catch (FileNotFoundException fnfe) {
			System.out.println("EXECUTION HALTED: svg File " + svgFile +
					" does not exist!");
			System.exit(0);
		} catch (IOException ioe) {
			System.out.println("ERROR: " + ioe);
		}

		}
	}
    }

    public void sortClusters() {

        // Members are sorted by their average expression ratio values, smallest to largest....
        double[] expArray = new double[clusterObjVector.size()];
        for (int i = 0; i < clusterObjVector.size(); i++) {
            ClusterObject thisCO = (ClusterObject) clusterObjVector.elementAt(i);
            expArray[i] = thisCO.avgValue;
        }
        try {
            qSort.sort(expArray);
        } catch (Exception e) {
            System.out.println(
                    "Error sorting in method: ClusterObject.sortMembers()");
            System.exit(0);
        }
        Vector oldClusters = (Vector) clusterObjVector.clone();
        clusterObjVector = new Vector();
        for (int i = 0; i < expArray.length; i++) {
            // find the value in oldMembers that equals the current index of expArray
            for (int index = 0; index < oldClusters.size(); index++) {
                ClusterObject thisCO = (ClusterObject) oldClusters.elementAt(
                        index);
                if (thisCO.avgValue == expArray[i]) {
                    ClusterObject newInstance = (ClusterObject) oldClusters.
                                                remove(index);
                    clusterObjVector.add(newInstance);
                }
            }
        }

    }


    public String createRatioKey(int heatmapWidth) {
        String ratioSVG = "";
        int up = 255;
        int down = 255;
        //double negMult = ( -1 / val);
        if (xMargin < 150) {
            ratioSVG = "<g id=\"legend\"  transform=\"translate(" +
                       heatmapWidth + ",25)\">";
        } else {
            ratioSVG = "<g id=\"legend\"  transform=\"translate(" + (30) +
                       ",25)\">";
        }
        int y = 0;
        int z = 0;
        for (int i = 8, j = 1; i >= 1; i--, j++) {
            double posMult = (1 / (double) i);
            // //System.out.println("Here's posMult: " + posMult);
            up = 255 - (int) (255 * posMult);
            ////System.out.println("here's up: " + up);

            ratioSVG += "<rect x=\"0\" y=\"" + (y = j * (int) (squareSize / 3)) +
                    "\" width=\"" + (int) (squareSize / 2) + "\" height=\"" +
                    (int) (squareSize / 3) + "\"";
            if (this.colorScheme == 0) {
                // For red as upregulation...
                ratioSVG += " style=\"fill: rgb(" + up + "," + 0 + ",0);\"/>";
            } else {

                ratioSVG += " style=\"fill: rgb(" + up + "," + up + ",0);\"/>";

            }
            if (i == 8) {
                ratioSVG += "<text x=\"" + (15) + "\" y=\"" + (y + 5) +
                        "\" style=\"stroke: black; font-size: 6pt;\">>= " +
                        i + "</text>\n";
            } else if (i % 2 == 0) {
                ratioSVG += "<text x=\"" + (15) + "\" y=\"" + (y + 5) +
                        "\" style=\"stroke: black; font-family: arial; font-size: 6pt;\">" +
                        i + "</text>\n";
            } else if (i == 1) {
                ratioSVG += "<text x=\"" + ( -20) + "\" y=\"" + (y + 5) +
                        "\" style=\"stroke: black; font-family: arial; font-size: 6pt;\">Key</text>\n";
            }

            up -= 64;
        }
        y -= (int) (squareSize / 3);
        for (int i = -2; i >= -8; i--) {
            double negMult = ( -1 / (double) i);
            ////System.out.println("Here's negMult: " + negMult);
            down = 255 - (int) (255 * negMult);
            ////System.out.println("here's down: " + down);

            ratioSVG += "<rect x=\"0\" y=\"" +
                    (z = y + Math.abs(i) * (int) (squareSize / 3)) +
                    "\" width=\"" +
                    (int) (squareSize / 2) + "\" height=\"" +
                    (int) (squareSize / 3) +
                    "\"";
            if (this.colorScheme == 0) {
                // For green as down regulation....
                ratioSVG += " style=\"fill: rgb(0," + down + ",0);\"/>";
            } else {
                // For blue as down-regulation....
                ratioSVG += " style=\"fill: rgb(0,0," + down + ");\"/>";
            }
            down -= 64;
            int height = y + Math.abs(i) * (int) squareSize / 3;
            if (i == -8) {
                ratioSVG += "<text x=\"" + (15) + "\" y=\"" + (z + 5) +
                        "\" style=\"stroke: black; font-family: arial; font-size: 6pt;\">&lt;= " +
                        i + "</text>\n";

            } else if (i % 2 == 0) {
                ratioSVG += "<text x=\"" + (15) + "\" y=\"" + (z + 5) +
                        "\" style=\"stroke: black; font-family: arial; font-size: 6pt;\">" +
                        i + "</text>\n";

            }

        }
	if(ordered != true){
	ratioSVG += "<rect x=\"-20\" y=\"" + (z + 15) +"\" width=\"10\" height=\"6\" style=\"fill: rgb(152,152,152);\"/><text x=\"" + (-5) + "\" y=\"" + (z + 20) +
                        "\" style=\"stroke: black; font-family: arial; font-size: 6pt;\"> Bad pValue</text>\n";
	}
	int addtoz = 0;  // just a vertical offset to accomodate the "Bad pValue" stuff
	if(ordered != true){
		addtoz = 120;

	}else{
		addtoz = 100;
	}
        ratioSVG += "<rect x=\"-25\" y=\"0\" width=\"80\" height=\"" + (addtoz) +
                "\" style=\"fill: none; stroke: red;\"/>";
		//ratioSVG += "<rect x=\"-25\" y=\"0\" width=\"70\" height=\"" + (z + 20) +
               // "\" style=\"fill: rgb(152,152,152); stroke: red;\"/>";
        ratioSVG += "</g>";
        return ratioSVG;
        //    ratioSVG += "<text x=\"" + (xMargin + 40) + "\" y=\"" + (30 + i * (squareSize/2)) +
        //         "\" style=\"stroke: black; font-family: arial; font-weight:bold; font-size: 14pt;\">" + strVal + "</text>\n";

    }


    public void displayClusterMembers() {

        this.maxX = this.arrays * squareWidth + xMargin;
        int height = dataArray.size() * squareHeight + 300;
	System.out.println("HEIGHT = "+height);
	/*  added the condition below to account for image size overflow */
	if(height > 32000){
		System.out.println("HEIGHT > 32000");
		height = height/2;
		squareHeight = 5;
		imageHeightExceeded = true;
	}
        int width = maxX + 400;
        
        
        // This is not the most efficient way of doing things, but it allows me to get the ordered treatments and display them in the 
        // html table....
           String treatmentnames = "<tr><td></td><td></td><td></td><td></td>";
            for (int i = 0; i < trxNames.size(); i++) {
                String aname = "";
                String name = (String) trxNames.elementAt(i);
                String sampval = (String) trxIDs.elementAt(i);
                Integer sampint = new Integer(sampval);
                // Check to see that this is not a blank....
                 if (sampint.intValue() != -99) {
               
                    aname = "<td>" + name + "</td>";
                
                } else {
               
                    aname = "<td></td>";
                }
                treatmentnames += aname;
            }
                 treatmentnames += "</tr>";
                // used to store the ordered treatment names....
                String treatmentnamesfull = new String(treatmentnames);
        
        // Add the header.... put in a method to allow
        // for universal changes to be made....
        printSVGHeader(svgFileVector, width, height);
        svgFileVector.add(new String(
                "<g id=\"graphic\" transform=\"translate(0,0)\">"));
        svgFileVector.add(createRatioKey(this.maxX + 50));
        svgFileVector.add(new String(
                "<g id=\"heatmap\"  cursor=\"crosshair\" transform=\"translate(" +
                xMargin + ",150)\">"));
        int colspan = numCols + this.arrays;
        ////System.out.println("<p>The number of cols to span = " + colspan + "</p>");
        ////System.out.println("<table id=\"results\">");
       
        tableFileVector.add(new String("<table id=\"results\">"));
        int clusterCount = 0;
        int dispNumber = 1;
        for (int i = 0; i < clusterObjVector.size(); i++) {
            ////System.out.println("<tr><td colspan= " + colspan + " class=\"colhead\"><h4> Cluster " + (i+1) + " </h4></td></tr>");
          
            tableFileVector.add(new String("<tr><td colspan= " + colspan +
                                           " class=\"colhead\"><h4> Cluster " +
                                           (i + 1) + " </h4></td></tr>"));
            tableFileVector.add(treatmentnamesfull);
            ClusterObject thisCluster = (ClusterObject) clusterObjVector.
                                        elementAt(i);

            thisCluster.displayCluster(svgFileVector, clusterCount, this.arrays,
                                       dispNumber, trxIDs, tableFileVector,outputDirectory);
            dispNumber++;
            clusterCount += thisCluster.currentSize + 1;
        }

        ////System.out.println("</table>");
        tableFileVector.add(new String("</table>"));
        //pw.write("</g>");
        svgFileVector.add(new String("</g>"));
        //pw.write("<g id=\"genes\" transform=\"translate(0,50)\">");
        int genesXLoc = this.maxX + 5 - (blankIDs * 15);
        svgFileVector.add(new String("<g id=\"genes\" transform=\"translate(" +
                                     (genesXLoc) + ",163)\">"));
        clusterCount = 0;
        for (int i = 0; i < clusterObjVector.size(); i++) {
            ClusterObject thisCluster = (ClusterObject) clusterObjVector.
                                        elementAt(i);
            thisCluster.displayClusterSVG(svgFileVector, clusterCount);
            //thisCluster.plotCluster(Cluster.k, i, outputDirectory); // Commented out due to issues with Flex and charting components.
            clusterCount += thisCluster.currentSize + 1;
        }
        //pw.write("</g>");
        //pw.write("</svg>");
        svgFileVector.add(new String("</g>"));

        svgFileVector.add(new String(
                "<g id=\"treatments\" transform=\"translate(" + xMargin +
                ",145)\" >"));
        int xVal = 15;
        for (int i = 0; i < trxNames.size(); i++) {
            String name = (String) trxNames.elementAt(i);
            String sampval = (String) trxIDs.elementAt(i);
            Integer sampint = new Integer(sampval);
            // Check to see that this is not a blank....
            if (sampint.intValue() != -99) {
                if (browser == 1) { // i.e. not IE...
                	 /*String xlink =
                    "<a xlink:href=\"http://edge.oncology.wisc.edu/sample.php?sampleid=" +
                    sampval +
                    "&amp;orderby=hybrids.finalratio&amp;sort=asc&amp;lcomp=lte&amp;lval=-3&amp;rcomp=gte&amp;rval=3\" target=\"_blank\" alt=\"\">";
           */
        	String xlink = "<a xlink:href=\""+ this.arrayURL + sampval + "\" target=\"_blank\" alt=\"\">";
    svgFileVector.add(xlink);

                    svgFileVector.add(new String("<text x=\"" + xVal +
                                                 "\" y=\"0\" transform=\"rotate(270, " +
                                                 xVal +
                                                 ", 0)\" style=\"font-size:8pt; font-weight:bold;\">" +
                                                 name + "</text>"));

                    svgFileVector.add("</a>");
                } else { // use javascript for ie....
                    svgFileVector.add(new String("<text x=\"" + xVal +
                                                 "\" y=\"0\" transform=\"rotate(270, " +
                                                 xVal +
                                                 ", 0)\" style=\"font-size:8pt; font-weight:bold;\" onclick=\"t(" +
                                                 sampval + ",-3,3)\">" + name +
                                                 "</text>"));

                }
                xVal += squareWidth;
            } else {
                xVal += (squareWidth - 15);
            }

        }

        svgFileVector.add(new String("</g>"));
        svgFileVector.add(new String("</g>"));
        svgFileVector.add(new String("</svg>"));
        //pw.flush ();
        /*
                //pw.close ();	/*}
                    catch (FileNotFoundException fnfe) {
          //System.out.println("EXECUTION HALTED: File "+svgFile+" does not exist!");
                   System.exit(0);
                    }
          catch (IOException ioe) { //System.out.println("ERROR: "+ioe);
                    }*/
    }


    public void displayOrderedList() {

        this.maxX = this.arrays * squareWidth + xMargin;
        int height = dataArray.size() * squareHeight + 300;
        int width = maxX + 400;
        // Add the header.... put in a method to allow
        // for universal changes to be made....
        printSVGHeader(svgFileVector, width, height);
        svgFileVector.add(new String(
                "<g id=\"graphic\" transform=\"translate(0,0)\">"));
        svgFileVector.add(createRatioKey(this.maxX + 50));
        svgFileVector.add(new String(
                "<g id=\"heatmap\"  cursor=\"crosshair\" transform=\"translate(" +
                xMargin + ",150)\">"));
        int colspan = numCols + this.arrays;
        ////System.out.println("<p>The number of cols to span = " + colspan + "</p>");
        ////System.out.println("<table id=\"results\">");
        tableFileVector.add(new String("<table id=\"results\"><tr><td></td><td></td><td></td><td></td>"));
         int clusterCount = 0;
        int dispNumber = 1;
        /*for (int i = 0; i < clusterObjVector.size(); i++) {
            ////System.out.println("<tr><td colspan= " + colspan + " class=\"colhead\"><h4> Cluster " + (i+1) + " </h4></td></tr>");
            tableFileVector.add(new String("<tr><td colspan= " + colspan +
                                           " class=\"colhead\"><h4> Cluster " +
                                           (i + 1) + " </h4></td></tr>"));
            ClusterObject thisCluster = (ClusterObject) clusterObjVector.
                                        elementAt(i);

            thisCluster.displayCluster(svgFileVector, clusterCount, this.arrays,
                                       dispNumber, trxIDs, tableFileVector);
            dispNumber++;
            clusterCount += thisCluster.currentSize + 1;
        }*/
        
        // Need to add the trx names at the top of the table...
        for(int i = 0; i < this.trxNames.size(); i++){
                String s2 = new String("<td>" + (String) trxNames.elementAt(i) + "</td>");
                tableFileVector.add(s2);
        }
        tableFileVector.add(new String("</tr>"));
        
            
int numArrays = this.arrays;
int currentRow = 1;
int x2 = numArrays * Cluster.squareWidth + 350;
        int y = (currentRow) * Cluster.squareHeight;
        //svgFileVector.add(new String("<line x1=\"" + (-1 * Cluster.xMargin) + "\" y1=\"" + y + "\" x2=\"" + x2 + "\" y2=\"" + y + "\" style=\"stroke: blue; stroke-width: 2;\"/>\n"));
        //int midWay = clusterMembers.size() / 2;
	ClusterObject orderedcluster = (ClusterObject) clusterObjVector.elementAt(0);
        for (int i = 0; i < orderedcluster.currentSize; i++) {
            tableFileVector.add(new String("<tr>"));
            ////System.out.println("<tr>");
            Instance thisInstance = (Instance) orderedcluster.clusterMembers.elementAt(i);
            tableFileVector.add(new String("<td>" + thisInstance.identifier +
                                           "</td><td width=\"100\"> " +
                                           thisInstance.
                                           commonNameAndDescription +
                                           "</td><td><a href=\"http://www.ncbi.nlm.nih.gov/entrez/viewer.fcgi?val=" +
                                           thisInstance.refseq +
                                           "\" target=\"_blank\">" +
                                           thisInstance.refseq + "</a></td>"));
            ////System.out.println("<td>" + thisInstance.identifier + "</td><td width=\"100\"> " + thisInstance.commonNameAndDescription + "</td><td><a href=\"http://www.ncbi.nlm.nih.gov/entrez/viewer.fcgi?val=" + thisInstance.refseq + "\" target=\"_blank\">" + thisInstance.refseq + "</a></td>");
            ////System.out.println("<td>");
            tableFileVector.add(new String("<td>"));
            Vector golist = (Vector) thisInstance.goids.clone();
            for (int j = 0; j < golist.size(); j++) {
                String id = (String) golist.elementAt(j);
                tableFileVector.add(new String(
                        "<a href=\"http://www.godatabase.org/cgi-bin/go.cgi?query=" +
                        id + "&view=query\" target=\"_blank\">" + id +
                        "</a><br>"));
                ////System.out.println("<a href=\"http://www.godatabase.org/cgi-bin/go.cgi?query=" + id + "&view=query\" target=\"_blank\">" + id + "</a><br>");
            }
            ////System.out.println("</td>");
            tableFileVector.add(new String("</td>"));

            Vector trxOrderVector = new Vector();
	    //System.out.println("before printing instance values");
            thisInstance.printInstanceVals(svgFileVector, i, currentRow,
                                           numArrays, trxIDs, tableFileVector,
                                           Cluster.colorScheme, trxOrderVector,
                                           1);
            tableFileVector.add(new String("</tr>"));
            ////System.out.println("</tr>");
        }
      /*  x2 = numArrays * Cluster.squareWidth + 350;
        int val = (int) .5 * Cluster.squareHeight;
        y = ((currentRow + clusterMembers.size()) * Cluster.squareHeight) +
            Cluster.squareHeight;
	*/


        ////System.out.println("</table>");
        tableFileVector.add(new String("</table>"));
        //pw.write("</g>");
        svgFileVector.add(new String("</g>"));
        //pw.write("<g id=\"genes\" transform=\"translate(0,50)\">");
        int genesXLoc = this.maxX + 5 - (blankIDs * 15);
        svgFileVector.add(new String("<g id=\"genes\" transform=\"translate(" +
                                     (genesXLoc) + ",173)\">"));
        clusterCount = 0;


        for (int i = 0; i < clusterObjVector.size(); i++) {
            ClusterObject thisCluster = (ClusterObject) clusterObjVector.
                                        elementAt(i);
            thisCluster.displayClusterSVG(svgFileVector, clusterCount);
            //thisCluster.plotCluster(Cluster.k, i, outputDirectory);  // Commented out due to issues with Flex and charting components.
            clusterCount += thisCluster.currentSize + 1;
        }
        //pw.write("</g>");
        //pw.write("</svg>");
        svgFileVector.add(new String("</g>"));

        svgFileVector.add(new String(
                "<g id=\"treatments\" transform=\"translate(" + xMargin +
                ",155)\" >"));
        int xVal = 15;
        for (int i = 0; i < trxNames.size(); i++) {
            String name = (String) trxNames.elementAt(i);
            String sampval = (String) trxIDs.elementAt(i);
            Integer sampint = new Integer(sampval);
            // Check to see that this is not a blank....
            if (sampint.intValue() != -99) {
                if (browser == 1) { // i.e. not IE...
                	 /*String xlink =
                    "<a xlink:href=\"http://edge.oncology.wisc.edu/sample.php?sampleid=" +
                    sampval +
                    "&amp;orderby=hybrids.finalratio&amp;sort=asc&amp;lcomp=lte&amp;lval=-3&amp;rcomp=gte&amp;rval=3\" target=\"_blank\" alt=\"\">";
           */
        	String xlink = "<a xlink:href=\""+ this.arrayURL + sampval + "\" target=\"_blank\" alt=\"\">";
    svgFileVector.add(xlink);

                    svgFileVector.add(new String("<text x=\"" + xVal +
                                                 "\" y=\"0\" transform=\"rotate(270, " +
                                                 xVal +
                                                 ", 0)\" style=\"font-size:8pt; font-weight:bold;\">" +
                                                 name + "</text>"));

                    svgFileVector.add("</a>");
                } else { // use javascript for ie....
                    svgFileVector.add(new String("<text x=\"" + xVal +
                                                 "\" y=\"0\" transform=\"rotate(270, " +
                                                 xVal +
                                                 ", 0)\" style=\"font-size:8pt; font-weight:bold;\" onclick=\"t(" +
                                                 sampval + ",-3,3)\">" + name +
                                                 "</text>"));

                }
                xVal += squareWidth;
            } else {
                xVal += (squareWidth - 15);
            }

        }

        svgFileVector.add(new String("</g>"));
        svgFileVector.add(new String("</g>"));
        svgFileVector.add(new String("</svg>"));
        //pw.flush ();
        /*
                //pw.close ();	/*}
                    catch (FileNotFoundException fnfe) {
          //System.out.println("EXECUTION HALTED: File "+svgFile+" does not exist!");
                   System.exit(0);
                    }
          catch (IOException ioe) { //System.out.println("ERROR: "+ioe);
                    }*/
    }

    public void initializeClusters() {

        // k is the number of clusters as well as the number of
        // genes initially in each cluster....
        // Unless it is a divisor where k/number of total genes < k
        ////System.out.println("In initializeClusters()");
        //clusterObject(int thisNumber, Vector initialCluster )
        int start = 0;
        int end = 0;
        // if k^2 > the initial dataArray size, we need to check to see if the
        // number of clusters is permissible....
        if ((k * 3) > dataArray.size()) {
            System.out.println("You entered a k-value that is too large!");
            System.out.println(
                    "The maximum number of clusters that can be chosen = " +
                    (dataArray.size() / 3));
            System.exit(0);
        }
        for (int i = 0; i < k; i++) {
            Vector initClusterMembers = new Vector();
            if (i == 0) {
                start = 0;
                end = 3 - 1;
            } else {
                start = (i * 3);
                end = ((i + 1) * 3) - 1;
            }
            // //System.out.println("Cluster " + i + "\t");
            int dVal = 2;
            for (int count = start; count <= end; count++) {
                //	//System.out.println("Before adding dataArray " + count);
                initClusterMembers.add((Instance) dataArray.elementAt(count));
                ////System.out.println("After adding dataArray " + count);
            }
            //  //System.out.println("Before creating the new ClusterObject");
            ClusterObject newCluster = new ClusterObject(i, initClusterMembers,
                    browser);
            ////System.out.println("After creating the new ClusterObject");
            clusterObjVector.add(newCluster);
            //   //System.out.println("After adding the new ClusterObject");
        }
    }

    public void getData(String file) {
    	// There are going to be two new lines added to this file at the
    	// beginning of it.
    	// 1st line is the array URL the 2nd is the feature URL
    	
        Vector dataVector = new Vector();
        int geneCount = 0;
        int colCount = 0;
        boolean inMatrix = false;
        try {
            BufferedReader buf = new BufferedReader(new FileReader(file));
            char[] blosumTemp;
            int counter = 0;
            boolean readarrayURL = false;
            boolean readfeatureURL = false;
            while (buf.ready()) {
                dataVector = new Vector();
                String s = new String(buf.readLine()).trim();
                ////System.out.println(s);
                if(readarrayURL == false){
                	// Read in the array URL
                	this.arrayURL = new String(s);
                	readarrayURL = true;
                	continue;
                }
                if(readfeatureURL == false){
                	// Read in the feature (or clone) URL
                	this.featureURL = new String(s);
                	 readfeatureURL = true;
                	continue;
                }
                
                if (counter < this.arrays) {
                    trxNames.add(s);
                    ////System.out.println(s);
                    counter++;
                    continue;
                }
                if (counter >= this.arrays && counter < ((2 * this.arrays))) {
                    trxIDs.add(s);
                    if (s.compareTo("-99") == 0) {
                        blankIDs++;
                    }
                    counter++;
                    continue;
                }

                ////System.out.println("line = " + s);
                geneCount++;
                boolean firstVal = true;
                //s = removeComment(s);
                blosumTemp = s.toCharArray();
                ////System.out.println(blosumTemp + "<br>");
                String token = new String();
                for (int i = 0; i < blosumTemp.length; i++) {
                    char x = blosumTemp[i];
                    //Character current = new Character(x);
                    if (x == '\t' || x == '\n') {

                        if (token.length() == 1) {
                            //token = "empty value";
                            token = "";
                        }

                        token = token.trim();
                        colCount++;
                        ////System.out.println(colCount + " " + token);
                        //if(colCount <= numCols){
                        ////System.out.println(token);
                        if (token.compareTo("-9999") == 0) {
                            ////System.out.println("hit a -9999");
                            token = "0.0";
                        }

                        dataVector.add(token);
                        ////System.out.println(token + "\t");
                        // }else{
                        // dataVector.add(new Double(token));
                        // }
                        token = new String();
                    }
                    token += x;
                }
                token = token.trim();
                ////System.out.println(token);
                if (token.compareTo("-9999") == 0) {
                    //System.out.println("hit a -9999");
                    token = "0.0";
		    System.out.println(token);
                }
                dataVector.add(new String(token));
                ////System.out.println(++colCount + " " + token);
                /*    for (int i = 0; i < dataVector.size(); i++) {
                        System.out.print((String) dataVector.elementAt(i));
                    }*/
                ////System.out.println();
                colCount = 0;
                ////System.out.println("Before creating new Instance");
                Instance newInstance = new Instance(geneCount - 1, dataVector);
                ////System.out.println("After creating new Instance");
                dataArray.add(newInstance);
            }

        } catch (FileNotFoundException fnfe) {
            System.out.println("EXECUTION HALTED: Input File " + file +
                               " does not exist!");
            System.exit(0);
        } catch (IOException ioe) {
            System.out.println("ERROR: " + ioe);
        }
        // If this is hierarchical clustering, create the chemInstances....
        if (this.clustAlg == 1) {
            chemDataArray = new Vector();
            ////System.out.println("Creating chemInstances...");
            Vector dataVectors[] = new Vector[arrays];
            // How many chemical Instances do we need to create????
            for (int chemNum = 0; chemNum < arrays; chemNum++) {
                dataVectors[chemNum] = new Vector();
            }
            for (int instVal = 0; instVal < dataArray.size(); instVal++) {
                Instance inst = (Instance) dataArray.elementAt(instVal);
                Vector instVector = (Vector) inst.instanceVals;
                for (int i = 0; i < instVector.size(); i++) {
                    String val = (String) instVector.elementAt(i);
                    Vector thisVect = (Vector) dataVectors[i];
                    thisVect.add(val);
                }
            }

            // Output the chemvals...
            for (int i = 0; i < dataVectors.length; i++) {
                Vector currentVector = (Vector) dataVectors[i];
                String name = (String) trxNames.elementAt(i);
                Instance newchemInstance = new Instance(i, currentVector, name);
                chemDataArray.add(newchemInstance);
            }

        }

    }

    public double simScore(Vector gene1, Vector gene2) {
        /*
             N = number of conditions
         Pearson's Correlation coefficient
         S(X,Y) where X = gene X and Y = gene Y taken across N conditions.
         S(X,Y) = 1/N * Sum{from 1 to N}(((Xi-Xoffset)/Xsd)((Yi-Yoffset)/Ysd))
         Xsd = Sqrt((Sum{from 1 to N}((Xi-0)^2/(N))
         Ysd = Sqrt((Sum{from 1 to N}((Yi-0)^2/(N))
         */


        int N = gene1.size();
        int N2 = gene2.size();
        if (N2 == 0) {
            ////System.out.println("Gene2 is 0!");
        }
        ////System.out.println("Gene 1 is " + N + "| Gene 2 is " + N2);
        ////System.out.println("Gene2 is " + N2);
        double meanGene1 = 0.0; // Offset for first cluster...
        double meanGene2 = 0.0; // Offset for second cluster...
        /* for(int i = 0; i < gene1.size(); i++){
           Double g1 = (Double) gene1.elementAt(i);
           Double g2 = (Double) gene2.elementAt(i);
           meanGene1 += g1.doubleValue();
           meanGene2 += g2.doubleValue();
         }
         meanGene1 = meanGene1/N;
         meanGene2 = meanGene2/N;
         ////System.out.println("\tMean Gene 1: " + meanGene1 + "\tMean Gene 2: " + meanGene2);
         */
        double xsdVal = 0.0;
        double ysdVal = 0.0;
        boolean restart = false;
        for (int i = 0; i < N; i++) {

            Double Xi = (Double) gene1.elementAt(i);
            Double Yi = (Double) gene2.elementAt(i);
            xsdVal += ((Xi.doubleValue() * Xi.doubleValue()) / N);
            ysdVal += ((Yi.doubleValue() * Yi.doubleValue()) / N);

        }
        restart = false;
        double Xsd = Math.sqrt(xsdVal);
        double Ysd = Math.sqrt(ysdVal);
        ////System.out.println("\tGene 1 s.d.: " + Xsd + "\tGene 2 s.d.: " + Ysd);

        double score = 0.0;

        for (int i = 0; i < N; i++) {

            Double Xi = (Double) gene1.elementAt(i);
            Double Yi = (Double) gene2.elementAt(i);
            score += (((Xi.doubleValue()) / Xsd) * ((Yi.doubleValue()) / Ysd));
        }

        score = score / N;

        return score;

    }

    public void printSVGHeader(Vector svgFileVector, int width, int height) {
        //svgFileVector.add(new String("<?xml version=\"1.0\" encoding=\"ISO-8859-1\" standalone=\"no\"?>\n <!DOCTYPE svg PUBLIC \"-//W3C//DTD SVG 20010904//EN\"\n"));
        //  svgFileVector.add(new String("\"http://www.w3.org/TR/2001/REC-SVG-20010904/DTD/svg10.dtd\" [\n"));
        //  svgFileVector.add(new String("<!ATTLIST svg\n xmlns:a3 CDATA #IMPLIED\n a3:scriptImplementation CDATA #IMPLIED>\n"));
        // svgFileVector.add(new String(" <!ATTLIST script \na3:scriptImplementation CDATA #IMPLIED>\n]>"));
        svgFileVector.add(new String(
                "<svg preserveAspectRatio=\"xMinYMin meet\" viewBox=\"0 0 " +
                (width) + " " + (height) +
                "\" id=\"svgObject\" xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\"" ));
        //svgFileVector.add(new String("<svg width=\"" + width + "\" height=\"" + height + "\" id=\"svgObject\" xmlns=\"http://www.w3.org/2000/svg\""));
        // svgFileVector.add(new String(" xmlns:xlink=\"http://www.w3.org/1999/xlink\" xmlns:a3=\"http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/\" "));
        //svgFileVector.add(new String(" a3:scriptImplementation=\"Adobe\"> <script type=\"text/ecmascript\" a3:scriptImplementation=\"Adobe\"><![CDATA["));
        //svgFileVector.add(new String("function viewcloneinfo(clonenumber,arraynumber){"));
        //svgFileVector.add(new String(" window.open('http://genome.oncology.wisc.edu/edge2/cloneinfo.php?cloneid=' + clonenumber + '&versionid=&arrayid=' + arraynumber+'');}"));
        //svgFileVector.add(new String("> <![CDATA[function t(num,l,r){parent.opentrx(num,l,r);}]]></script>));"));
        svgFileVector.add(new String(">"));
    }


    public Vector avgLink_ChemHeirarchicalCluster() {
        // Calculate all pairwise distances between each genes' vectors...
        // Use the distant metric (Pearson correlation coefficient)
        // Two vectors which point in exactly the same direction will have a
        // correlation of 1, anti-correlated results in a value of -1.
        // Uncorrelated (perpendicular) will have a value of 0.


        // Create a vector containing clusters of all instances...
        Vector clusters = new Vector();
        Vector nodes = new Vector();
        ClusterObject thisClust;
        ClusterObject joinClust;
        ClusterObject clust;
        for (int i = 0; i < chemDataArray.size(); i++) {
            Instance instance = (Instance) chemDataArray.elementAt(i);
            String name = instance.commonNameAndDescription;
            Vector x = new Vector();
            x.add(instance);
            clust = new ClusterObject(i, x, browser);
            clusters.add(clust);
            Node nodeToAdd = new Node(i, name);
            nodes.add(nodeToAdd);
        }
        int nodeCount = nodes.size();
        ////System.out.println("The # of initial clusters: " + dataArray.size());
        //int clusterCount = clusters.size();
        while (clusters.size() > 1) {
            ////System.out.println("# of Clusters: " + clusters.size());
            // Join the most similar two expression patterns into a node...
            // Get the max similarity score...
            thisClust = (ClusterObject) clusters.firstElement();
            Vector clust1 = (Vector) thisClust.meanVals;
            Vector clust2;
            double maxScore = -99; // This is definitely out of the bounds corr. coeff.
            int maxIndex = -1; // Falls outside legal index values....
            for (int i = 1; i < clusters.size(); i++) {
                //System.out.print("#");
                clust = (ClusterObject) clusters.elementAt(i);
                clust2 = (Vector) clust.meanVals;
                // Get the score for these two clusters....
                double currentScore = simScore(clust1, clust2);
                ////System.out.println("The current score is: " + currentScore);
                if (currentScore >= maxScore) {
                    maxScore = currentScore;
                    maxIndex = i;
                }
            }

            // Now we've got maxScore and index.... join the clusters involved...
            joinClust = (ClusterObject) clusters.elementAt(maxIndex);
            ClusterObject newCluster = thisClust.joinClusters(joinClust,
                    nodeCount);

            // Determine the depth of the lowest cluster (joinClust or thisClust)....
            Node thisNode = (Node) nodes.elementAt(thisClust.clusterNumber);
            Node joinNode = (Node) nodes.elementAt(joinClust.clusterNumber);

            double thisNodeHeight = thisNode.edgeLength;
            double joinNodeHeight = joinNode.edgeLength;
            double newNodeHeight = 0.0;
            if (thisNodeHeight == joinNodeHeight) {
                newNodeHeight = thisNodeHeight + 1.0;
            } else {
                if (thisNodeHeight > joinNodeHeight) {
                    newNodeHeight = thisNodeHeight + 1.0;
                } else {
                    newNodeHeight = joinNodeHeight + 1.0;
                }
            }
            // //System.out.println("Creating parent node: " + nodeCount + " W/ child1: " + thisClust.clusterNumber + " and Child2: " + joinClust.clusterNumber);
            //  Create the new internal node and add the children and set the new node as their parent...
            Node newInternalNode = new Node(nodeCount, newNodeHeight);
            newInternalNode.setKid1(thisClust.clusterNumber, maxScore);
            newInternalNode.setKid2(joinClust.clusterNumber, maxScore);

            // Set the parents of the current kids to be the newInternalNode's number....
            Node kid1 = (Node) nodes.elementAt(thisClust.clusterNumber);
            kid1.setParent(nodeCount);
            Node kid2 = (Node) nodes.elementAt(joinClust.clusterNumber);
            kid2.setParent(nodeCount);
            nodes.add(newInternalNode);

            // ... and remove them from the Vector
            clusters.remove(maxIndex);
            clusters.remove(0);

            // ... and add new ClusterObject to vector..
            clusters.add(newCluster);
            nodeCount++;
        }

	/*  added the condition below to account for image size overflow */
	 int starty = 10;
	if(imageHeightExceeded){
	 	starty = 5;
	}

        int startx = 0;
        Node root = (Node) nodes.lastElement();
        Vector orderedInstances = new Vector();
////System.out.println("@@@@@@@@@@@@@@Before traversing trx nodes@@@@@@@@@@@@@@@");
        Node leftBranch = (Node) nodes.elementAt(root.child1);
        leftBranch.traverse(nodes, chemDataArray, orderedInstances, starty,
                            startx,
                            chemDendrogramSVGVector);
        Node rightBranch = (Node) nodes.elementAt(root.child2);
        rightBranch.traverse(nodes, chemDataArray, orderedInstances, starty,
                             startx,
                             chemDendrogramSVGVector);

        chemDendrogramSVGVector.add(new String(
                "<g id=\"chemdendrogram\" transform=\"translate(" + xMargin +
                ",0)\">\n"));
        root = (Node) nodes.lastElement();
        int maxHeight = (int) root.edgeLength + 1;
        Node.printTrxDots(nodes, root, 1, chemDendrogramSVGVector, maxHeight);
        Node.connectTrxDots(nodes, root, chemDendrogramSVGVector);
        ////System.out.println("The max height is: " + maxHeight);
////System.out.println("@@@@@@@@@@@@@@After traversing trx nodes@@@@@@@@@@@@@@@");
        chemDendrogramSVGVector.add(new String("</g>"));

        // geneDendrogramSVGVector.add(new String("</svg>"));
        // if we're clustering by treatments and displaying at top or top and bottom...
        if (trxClusterOption == 1 || trxClusterOption == 3) {
            for (int i = 0; i < chemDendrogramSVGVector.size(); i++) {

                String str = (String) chemDendrogramSVGVector.elementAt(i);
                svgFileVector.add(str);

                //   //System.out.println(str);
            }
        }

        Vector orderedTrxs = new Vector();
        ////System.out.println("#########Ordered Trxs#########");
        for (int i = 0; i < orderedInstances.size(); i++) {
            Instance inst = (Instance) orderedInstances.elementAt(i);
            Integer x = new Integer(inst.instanceIndex);
            orderedTrxs.add(x);
            // //System.out.println(x.intValue());
        }
////System.out.println("###############################");
        return orderedTrxs;

    }

    public Vector avgLink_HeirarchicalCluster() {
        // Calculate all pairwise distances between each genes' vectors...
        // Use the distant metric (Pearson correlation coefficient)
        // Two vectors which point in exactly the same direction will have a
        // correlation of 1, anti-correlated results in a value of -1.
        // Uncorrelated (perpendicular) will have a value of 0.


        // Create a vector containing clusters of all instances...
        Vector clusters = new Vector();
        Vector nodes = new Vector();
        ClusterObject thisClust;
        ClusterObject joinClust;
        ClusterObject clust;

        Vector trxOrderVector = new Vector();
        for (int i = 0; i < dataArray.size(); i++) {
            Instance instance = (Instance) dataArray.elementAt(i);
            Vector x = new Vector();
            x.add(instance);
            clust = new ClusterObject(i, x, browser);
            clusters.add(clust);
            Node nodeToAdd = new Node(i, instance.commonNameAndDescription);
            nodes.add(nodeToAdd);
        }
        int nodeCount = nodes.size();
        ////System.out.println("The # of initial clusters: " + dataArray.size());
        //int clusterCount = clusters.size();
        while (clusters.size() > 1) {
            ////System.out.println("# of Clusters: " + clusters.size());
            // Join the most similar two expression patterns into a node...
            // Get the max similarity score...
            thisClust = (ClusterObject) clusters.firstElement();
            Vector clust1 = (Vector) thisClust.meanVals;
            Vector clust2;
            double maxScore = -99; // This is definitely out of the bounds corr. coeff.
            int maxIndex = -1; // Falls outside legal index values....
            for (int i = 1; i < clusters.size(); i++) {
                //System.out.print("#");
                clust = (ClusterObject) clusters.elementAt(i);
                clust2 = (Vector) clust.meanVals;
                // Get the score for these two clusters....
                double currentScore = simScore(clust1, clust2);
                ////System.out.println("The current score is: " + currentScore);
                if (currentScore >= maxScore) {
                    maxScore = currentScore;
                    maxIndex = i;
                }
            }

            // Now we've got maxScore and index.... join the clusters involved...
            joinClust = (ClusterObject) clusters.elementAt(maxIndex);
            ClusterObject newCluster = thisClust.joinClusters(joinClust,
                    nodeCount);

            // Determine the depth of the lowest cluster (joinClust or thisClust)....
            Node thisNode = (Node) nodes.elementAt(thisClust.clusterNumber);
            Node joinNode = (Node) nodes.elementAt(joinClust.clusterNumber);

            double thisNodeHeight = thisNode.edgeLength;
            double joinNodeHeight = joinNode.edgeLength;
            double newNodeHeight = 0.0;
            if (thisNodeHeight == joinNodeHeight) {
                newNodeHeight = thisNodeHeight + 1.0;
            } else {
                if (thisNodeHeight > joinNodeHeight) {
                    newNodeHeight = thisNodeHeight + 1.0;
                } else {
                    newNodeHeight = joinNodeHeight + 1.0;
                }
            }
            // //System.out.println("Creating parent node: " + nodeCount + " W/ child1: " + thisClust.clusterNumber + " and Child2: " + joinClust.clusterNumber);
            //  Create the new internal node and add the children and set the new node as their parent...
            Node newInternalNode = new Node(nodeCount, newNodeHeight);
            newInternalNode.setKid1(thisClust.clusterNumber, maxScore);
            newInternalNode.setKid2(joinClust.clusterNumber, maxScore);

            // Set the parents of the current kids to be the newInternalNode's number....
            Node kid1 = (Node) nodes.elementAt(thisClust.clusterNumber);
            kid1.setParent(nodeCount);
            Node kid2 = (Node) nodes.elementAt(joinClust.clusterNumber);
            kid2.setParent(nodeCount);
            nodes.add(newInternalNode);

            // ... and remove them from the Vector
            clusters.remove(maxIndex);
            clusters.remove(0);

            // ... and add new ClusterObject to vector..
            clusters.add(newCluster);
            nodeCount++;
        } // while(clusters.size() > 1){....
        int starty = 10;
        int startx = 0;
        Node root = (Node) nodes.lastElement();
        Vector orderedInstances = new Vector();

        Node leftBranch = (Node) nodes.elementAt(root.child1);
        leftBranch.traverse(nodes, dataArray, orderedInstances, starty, startx,
                            geneDendrogramSVGVector);
        Node rightBranch = (Node) nodes.elementAt(root.child2);
        rightBranch.traverse(nodes, dataArray, orderedInstances, starty, startx,
                             geneDendrogramSVGVector);

        xMargin = (int) root.edgeLength * 10;
        this.maxX = this.arrays * squareWidth + xMargin;
        int height = dataArray.size() * squareHeight + 300;
        int width = this.maxX + 400;

	/*  added the condition below to account for image size overflow */
	if(height > 32000){
		System.out.println("HEIGHT > 32000");
		height = height/2;
		squareHeight = 5;
		imageHeightExceeded = true;
	}
        // This method adds the svg header info....
        printSVGHeader(svgFileVector, width, height);
        svgFileVector.add(new String(
                "<g id=\"graphic\" transform=\"translate(0,0)\">"));

        // If it's been chosen to cluster by treatments...
        if (trxClusterOption != 0) {
            trxOrderVector = this.avgLink_ChemHeirarchicalCluster();
        }

        svgFileVector.add(createRatioKey(this.maxX + 50));
        svgFileVector.add(new String(
                "<g id=\"heatmap\"  cursor=\"crosshair\" transform=\"translate(" +
                xMargin + ",150)\">"));
        int colspan = numCols + this.arrays;
        tableFileVector.add(new String("<table id=\"results\">"));
        tableFileVector.add(new String("<tr><td colspan= " + colspan +
                                       " class=\"colhead\"><h4>Hierarchical Clustering </h4></td></tr>"));
     /*   tableFileVector.add(new String("<tr><td></td><td></td><td></td><td></td>"));
          for(int i = 0; i < this.trxNames.size(); i++){
                String s2 = new String("<td>" + (String) trxNames.elementAt(i) + "</td>");
                tableFileVector.add(s2);
        }
        tableFileVector.add(new String("</tr>"));
      */
        geneDendrogramSVGVector.add(new String(
                "<g id=\"dendrogram\" transform=\"translate( -5,150)\">\n"));

        int clusterCount = 0;
        int dispNumber = 1;
        int x2 = arrays * Cluster.squareWidth + 350;
        int y = Cluster.squareHeight;
        Vector orderedNodes = new Vector();
        for (int i = 0; i < orderedInstances.size(); i++) {
            tableFileVector.add(new String("<tr>"));
            Instance thisInstance = (Instance) orderedInstances.elementAt(i);
            Node oNode = (Node) nodes.elementAt(thisInstance.instanceIndex);
            orderedNodes.add(oNode);
            tableFileVector.add(new String("<td>" + thisInstance.identifier +
                                           "</td><td width=\"100\"> " +
                                           thisInstance.
                                           commonNameAndDescription +
                                           "</td><td><a href=\"http://www.ncbi.nlm.nih.gov/entrez/viewer.fcgi?val=" +
                                           thisInstance.refseq +
                                           "\" target=\"_blank\">" +
                                           thisInstance.refseq + "</a></td>"
                                ));
            tableFileVector.add(new String("<td>"));
            Vector golist = (Vector) thisInstance.goids.clone();
            for (int j = 0; j < golist.size(); j++) {
                String id = (String) golist.elementAt(j);
                tableFileVector.add(new String(
                        "<a href=\"http://www.godatabase.org/cgi-bin/go.cgi?query=" +
                        id +
                        "&view=query\" target=\"_blank\">" + id + "</a><br>"));
            }
            tableFileVector.add("</td>");

            // Print the instances ..............................................
            thisInstance.printInstanceVals(svgFileVector, i, 0, arrays,
                                           trxIDs, tableFileVector, colorScheme,
                                           trxOrderVector, browser);
            //...................................................................
            tableFileVector.add(new String("</tr>"));
        }
        x2 = arrays * Cluster.squareWidth + 350;
        tableFileVector.add(new String("</table>"));
        svgFileVector.add(new String("</g>"));
        int genesXLoc = this.maxX + 5 - (blankIDs * 15);
        svgFileVector.add(new String("<g id=\"genes\" transform=\"translate(" +
                                     (genesXLoc) + ",163)\">"));
        clusterCount = 0;
        for (int i = 0; i < orderedInstances.size(); i++) {
            Instance thisInstance = (Instance) orderedInstances.elementAt(i);
            thisInstance.printGeneNamesSVG(svgFileVector, i, browser);

        }
        svgFileVector.add(new String("</g>"));

        /*svgFileVector.add(new String("<g id=\"treatments\" transform=\"translate(" +
                                     xMargin + ",145)\" >"));

           for (int i = 0; i < trxNames.size(); i++) {
          String name = "";
              String sampval = "";
         if(trxClusterOption == 1 || trxClusterOption == 2 || trxClusterOption == 3){
            Integer x = (Integer) trxOrderVector.elementAt(i);
            name = (String) trxNames.elementAt(x.intValue());
                sampval = (String) trxIDs.elementAt(x.intValue());
          }
          else{
            name = (String) trxNames.elementAt(i);
                sampval = (String) trxIDs.elementAt(i);
          }
                               String xlink = "<a xlink:href=\"http://genome.oncology.wisc.edu/edge2/sample.php?sampleid="+sampval+
                               "&amp;orderby=hybrids.finalratio&amp;sort=asc&amp;lcomp=lte&amp;lval=-3&amp;rcomp=gte&amp;rval=3\" target=\"_blank\" alt=\"\">";
               svgFileVector.add(xlink);

          svgFileVector.add(new String("<text x=\"" + xVal +
         "\" y=\"0\" transform=\"rotate(270, " + xVal +
                                       ", 0)\" style=\"font-family: arial; font-size:8pt; font-weight:bold;\">" + name + "</text>"));
         svgFileVector.add("</a>");
          xVal += squareWidth;
           }*/

        //svgFileVector.add(new String("</g>"));
        root = (Node) nodes.lastElement();
        int maxHeight = (int) root.edgeLength + 1;
        Node.printGeneDots(nodes, root, 1, geneDendrogramSVGVector, maxHeight);
        Node.connectGeneDots(nodes, root, geneDendrogramSVGVector);

        geneDendrogramSVGVector.add(new String("</g>"));

        for (int i = 0; i < geneDendrogramSVGVector.size(); i++) {
            String str = (String) geneDendrogramSVGVector.elementAt(i);
            svgFileVector.add(str);
        }

        // If we're clustering by treatments and displaying at top and bottom
        // or at bottom only.....
        if (trxClusterOption == 1 || trxClusterOption == 2) {
            chemDendrogramSVGVector.insertElementAt(new String(
                    "<g id=\"chemdendrogram\" transform=\"scale(1, -1) translate(" +
                    xMargin + "," + ( -1 * (height)) + ")\">\n"), 0);
            chemDendrogramSVGVector.remove(1);
            for (int i = 0; i < chemDendrogramSVGVector.size(); i++) {
                String x = (String) chemDendrogramSVGVector.elementAt(i);
                svgFileVector.add(x);
            }
        }

        svgFileVector.add(new String(
                "<g id=\"treatments\" transform=\"translate(" +
                xMargin + "," + (145) + ")\" >"));
        int xVal = 15;
         String arraynamesval = "<tr><td></td><td></td><td></td><td></td>";
        for (int i = 0; i < trxNames.size(); i++) {
            String name = "";
            String sampval = "";
           
            if (trxClusterOption == 1 || trxClusterOption == 2 ||
                trxClusterOption == 3) {
                Integer x = (Integer) trxOrderVector.elementAt(i);
                name = (String) trxNames.elementAt(x.intValue());
                sampval = (String) trxIDs.elementAt(x.intValue());
            } else {
                name = (String) trxNames.elementAt(i);
                sampval = (String) trxIDs.elementAt(i);
            }

            Integer sampint = new Integer(sampval);
            // Check to see that this is not a blank....
             String aname = "";
            if (sampint.intValue() != -99) {
               
                if (browser == 1) {
                    /*String xlink =
                            "<a xlink:href=\"http://edge.oncology.wisc.edu/sample.php?sampleid=" +
                            sampval +
                            "&amp;orderby=hybrids.finalratio&amp;sort=asc&amp;lcomp=lte&amp;lval=-3&amp;rcomp=gte&amp;rval=3\" target=\"_blank\" alt=\"\">";
                   */
                	String xlink = "<a xlink:href=\""+ this.arrayURL + sampval + "\" target=\"_blank\" alt=\"\">";
                    svgFileVector.add(xlink);
                }
                svgFileVector.add(new String("<text x=\"" + xVal +
                                             "\" y=\"" + 0 +
                                             "\" transform=\"rotate(270, " +
                                             xVal +
                                             "," + 0 +
                                             ")\" style=\"font-size:8pt; font-weight:bold;\" onclick=\"t(" +
                                             sampval + ",-3,3)\">" +
                                             name + "</text>"));
                aname = "<td>" + name + "</td>";
                arraynamesval += aname;
                if (browser == 1) {
                    svgFileVector.add("</a>");
                }
                xVal += squareWidth;
            } else {
                xVal += (squareWidth - 15);
                arraynamesval += "<td></td>";
            }

        }
                
          
      
         arraynamesval += "</tr>";
        tableFileVector.add(2,new String(arraynamesval));
  
        svgFileVector.add(new String("</g>"));

        svgFileVector.add(new String("</g>"));
        svgFileVector.add(new String("</svg>"));
        return clusters;
    }

    public void k_MeansCluster() {
        //  while the clusters are not stable, meaning that the assignment
        //  of instances to the clusters is dynamic, keep clustering...
        //  Basically, have to keep track of the last state of clusters.
        //  Specifically what was the composition of each cluster during
        //  the previous iteration?  If it is the same after completion of
        //  the current iteration, we'll stop the process and conclude that
        //  this is how the data set should be clustered....

        // A vector of vectors of Integer Objects to keep track of what Instances were in the
        // last iteration
        Vector previousClusterMembers = new Vector();
        // Create a default empty previousClusterMembers vector
        for (int i = 0; i < k; i++) {
            previousClusterMembers.add(new Vector());
        }
        Vector currentClusterMembers = new Vector();
        // Fill in currentClusterMembers...
        for (int i = 0; i < k; i++) {
            ClusterObject currentCluster = (ClusterObject) clusterObjVector.
                                           elementAt(i);
            //for(int j = 0; j < currentCluster.clusterMembers.size(); j++){
            currentClusterMembers.add(currentCluster.getCurrentClusterMembers());
            //}
        }

        // While the clusters are not different... keep clustering!!!!
        while (getClusterDifference(currentClusterMembers,
                                    previousClusterMembers)) {

            // Need to assign the currentClusterMembers to the previousClusterMembers...
            previousClusterMembers = (Vector) currentClusterMembers.clone();

            for (int instance = 0; instance < dataArray.size(); instance++) {
                // Create an array that will store the distances of this instances
                // to each cluster.... we will want to pick the smallest distance for
                // the assignment of an instance to a cluster...
                Instance currentInstance = (Instance) dataArray.elementAt(
                        instance);

                ////System.out.println("Dealing with " + currentInstance.commonNameAndDescription);

                int numberOfVals = currentInstance.instanceVals.size();
                // Array used to store the distance values...
                double[] distArray = new double[k];
                // Distance metric:
                // dist(instance, cluster centroid) =
                //  squareroot(Sum(feature i of current instance - feature i of cluster centroid)^2))
                Vector thisInstanceVals = (Vector) currentInstance.instanceVals;
                for (int cluster = 0; cluster < k; cluster++) {
                    ClusterObject thisCluster = (ClusterObject)
                                                clusterObjVector.elementAt(
                            cluster);
                    Vector thisClusterCentroid = (Vector) thisCluster.meanVals;
                    if (thisCluster.currentSize == 0) {
                        continue;
                    }
                    double distSum = 0.0;
                    for (int i = 0; i < numberOfVals; i++) {
                        // what is the distance.....Euclidean...
                        // distSum += (ith value of instance, instance - ith mean value of current cluster, cluster) ^ 2
                        String instStr = (String) thisInstanceVals.elementAt(i);
                        Double instanceVal = new Double(instStr); // (Double) thisInstanceVals.elementAt(i);
                        Double clusterMeanVal = (Double) thisClusterCentroid.
                                                elementAt(i);
                        distSum +=
                                (Math.pow((instanceVal.doubleValue() -
                                           clusterMeanVal.doubleValue()), 2.0));

                    }
                    // Taking the square root of the sum of squared differences...
                    distSum = Math.sqrt(distSum);
                    distArray[cluster] = distSum;
                }

                // What's the index of the smallest value in distArray???
                int smallestIndex = 0;
                double smallestVal = distArray[smallestIndex];
                for (int i = 1; i < distArray.length; i++) {
                    if (distArray[i] <= smallestVal) {
                        smallestIndex = i;
                        smallestVal = distArray[i];
                    }
                }
                // Assigning the instance to the cluster that is closest...


                ClusterObject assignedCluster = (ClusterObject)
                                                clusterObjVector.elementAt(
                        smallestIndex);
                // Need to check to see if this Instance is already in the selected Cluster!!!!
                // If it is not, remove from its old cluster (if necessary) and add to its
                // new cluster, otherwise don't do a thing!!!!
                if (!checkToSeeIfInstanceIsInCluster(currentInstance.
                        instanceIndex, assignedCluster)) {

                    // Find out if it is in any cluster and then remove it....
                    int clusterToRemoveFrom = -1;
                    for (int i = 0; i < k; i++) {
                        ClusterObject thisCO = (ClusterObject) clusterObjVector.
                                               elementAt(i);
                        if (checkToSeeIfInstanceIsInCluster(currentInstance.
                                instanceIndex, thisCO)) {
                            clusterToRemoveFrom = i;
                            break;
                        }
                    }
                    // If this instance was in a cluster previously, remove it...
                    if (clusterToRemoveFrom != -1) {
                        ClusterObject thisCO = (ClusterObject) clusterObjVector.
                                               elementAt(clusterToRemoveFrom);
                        // Now, need to find the index of this Instance...
                        thisCO.removeOldInstance(currentInstance.instanceIndex);
                    }

                    assignedCluster.addNewInstance(currentInstance);
                }
            }

            // Recalculate centroid for each cluster....
            for (int clustIndex = 0; clustIndex < k; clustIndex++) {
                ClusterObject thisCluster = (ClusterObject) clusterObjVector.
                                            elementAt(clustIndex);
                thisCluster.calculateMeans();
            }

            // Re-create the currentClusterMembers...
            currentClusterMembers = new Vector();
            // Fill in currentClusterMembers...
            for (int i = 0; i < k; i++) {
                ClusterObject currentCluster = (ClusterObject) clusterObjVector.
                                               elementAt(i);
                //for(int j = 0; j < currentCluster.clusterMembers.size(); j++){
                currentClusterMembers.add(currentCluster.
                                          getCurrentClusterMembers());
                //}
            }
            ////System.out.println("Done w/ one iteration...");
        }

        ////System.out.println("Finished k-Meaning....");

    }


    public boolean checkToSeeIfInstanceIsInCluster(int instanceIndex,
            ClusterObject currentCluster) {

        Vector clusterMembers = (Vector) currentCluster.clusterMembers;
        for (int i = 0; i < clusterMembers.size(); i++) {
            Instance thisInstance = (Instance) clusterMembers.elementAt(i);
            if (instanceIndex == thisInstance.instanceIndex) {
                return true;
            }
        }
        return false;
    }

    public boolean getClusterDifference(Vector currentClusterMembers,
                                        Vector previousClusterMembers) {

        // First check to see if each cluster has the same # of Instances...
        //boolean sizeCheck = false;
        for (int i = 0; i < k; i++) {
            Vector current = (Vector) currentClusterMembers.elementAt(i);
            Vector previous = (Vector) previousClusterMembers.elementAt(i);
            if (current.size() == previous.size()) {
                continue;
            } else {
                return true;
            }
        }
        // We've established that the current and previous clusters contain the same
        // respective number of Instances... but are they the same set of
        // respective Instances for each respective cluster
        for (int i = 0; i < k; i++) {
            Vector current = (Vector) currentClusterMembers.elementAt(i);
            Vector previous = (Vector) previousClusterMembers.elementAt(i);
            // Now we need to sort these vectors...
            int[] currentArray = new int[current.size()];
            int[] previousArray = new int[previous.size()];

            for (int index = 0; index < currentArray.length; index++) {
                Integer anInstance = (Integer) current.elementAt(index);
                currentArray[index] = anInstance.intValue();
                anInstance = (Integer) previous.elementAt(index);
                previousArray[index] = anInstance.intValue();
            }
            try {
                qSort.sort(currentArray);
                qSort.sort(previousArray);
            } catch (Exception e) {
                System.out.println("Exception while sorting...");
                System.exit(0);
            }
            // Now see if the sorted arrays are the same....
            for (int index = 0; index < currentArray.length; index++) {
                if (currentArray[index] != previousArray[index]) {
                    return true;
                }
            }

        }
        // If we've traveled this far.... There's no difference between
        // the clusters and we can stop!!!!
        ////System.out.println("They are the same...");
        return false;
    }


    /**
     * removes comments in line
     * @param s
     * @return
     */
    public String removeComment(String s) {
        int i = s.indexOf("#");
        if (i >= 0) {
            s = s.substring(0, i);
        }
        return s;
    }


}


class ClusterObject {

    // This is the centroid of this Cluster(Object)
    Vector meanVals;
    // The assigned number for this Cluster(Object)
    int clusterNumber;
    // clusterMembers is a vector of Instance objects....
    Vector clusterMembers;
    // The current number of Instance objects in this Cluster(Object)
    int currentSize;
    // The average of the meanVals vector; used for sorting....
    double avgValue;

    int browser;

    QSortAlgorithm qSort;

    ClusterObject(int thisNumber, Vector initialCluster, int browser) {

        clusterNumber = thisNumber;
        clusterMembers = new Vector();
        currentSize = initialCluster.size();
        // //System.out.println("in ClusterObject, before qSort creation");
        qSort = new QSortAlgorithm();

        // initialCluster is a vector of Instances initially
        // assigned to this cluster....
        ////System.out.println("in ClusterObject, before adding the instances...");
        for (int i = 0; i < currentSize; i++) {
            clusterMembers.add(initialCluster.elementAt(i));
        }
        // Create the mean vals vector....
        meanVals = new Vector();
        ////System.out.println("in ClusterObject, before calculating means...");
        calculateMeans();
    }

    public ClusterObject joinClusters(ClusterObject join, int clustNumber) {
        // Take this cluster and append the join clusterMembers to this
        ////System.out.println("The number before joining: " + this.currentSize);
        for (int i = 0; i < join.clusterMembers.size(); i++) {
            Instance x = (Instance) join.clusterMembers.elementAt(i);
            this.clusterMembers.add(x);
        }
        // Create a new clusterObject and return it.
        ClusterObject nCO = new ClusterObject(clustNumber, this.clusterMembers,
                                              browser);
        ////System.out.println("The number after joining: " + nCO.currentSize);
        return nCO;
    }


    public void removeOldInstance(int oldInstanceIndex) {
        for (int i = 0; i < clusterMembers.size(); i++) {
            Instance thisInstance = (Instance) clusterMembers.elementAt(i);
            if (thisInstance.instanceIndex == oldInstanceIndex) {
                // Remove this Instance....
                clusterMembers.remove(i);
                currentSize = clusterMembers.size();
                ////System.out.println("Removing Instance " + thisInstance.identifier + " from Cluster " + clusterNumber);
                return;
            }
        }
    }

    public void addNewInstance(Instance newInstance) {
        clusterMembers.add(newInstance);
        currentSize = clusterMembers.size();
    }

    /**
     * This method goes through the members of the cluster
     * and calculates the means.
     * Basically, this gives calculates the centroid.
     */
    public void calculateMeans() {
        // //System.out.println("In calculateMeans()");
        // //System.out.println("<br>");
        meanVals = new Vector();
        // Need to assign the current size for the calculation of the mean...
        currentSize = clusterMembers.size();
        // //System.out.println("The current size of the vector is: " + currentSize);
        // //System.out.println("<br>");
        // Take each of the values for the genes and calculate the mean
        for (int member = 0; member < clusterMembers.size(); member++) {
            Instance currentMember = (Instance) clusterMembers.elementAt(member);
            //  //System.out.println("Currently the index is of this Instance is: "+ currentMember.instanceIndex);
            ////System.out.println("<br>");
            int size = currentMember.instanceVals.size();
            // //System.out.println("The size of this instance is: " + size);
            ////System.out.println("<br>");
            ////System.out.println("Current Values");
            for (int val = 0; val < size; val++) {
                //Double currVal = (Double) currentMember.instanceVals.elementAt(val);
                String aVal = (String) currentMember.instanceVals.elementAt(val);
                ////System.out.println(aVal + "\t");
                Double currVal = new Double(aVal); // (Double) currentMember.instanceVals.elementAt(val);
                ////System.out.println("The double value (currVal) is: " + currVal);
                if (member == 0) {
                    // We don't need to add, just assign to meanVals vector...
                    ////System.out.println("Adding the 0th to meanVals vector...");
                    ////System.out.println("<br>");
                    meanVals.add(currVal);
                } else {
                    ////System.out.println("We're adding values together....");
                    ////System.out.println("<br>");
                    Double currMeanVal = (Double) meanVals.elementAt(val);
                    double updated = currMeanVal.doubleValue() +
                                     currVal.doubleValue();
                    currMeanVal = new Double(updated);
                    meanVals.setElementAt(currMeanVal, val);
                }
            }
        }
        // //System.out.println("In calculateMeans(), after first for loo");
        // Go through meanVals vector and divide by k... not efficient, but works...
        double avgVal = 0.0;
        for (int i = 0; i < meanVals.size(); i++) {
            Double thisVal = (Double) meanVals.elementAt(i);
            double val = thisVal.doubleValue();
            val = val / currentSize;
            thisVal = new Double(val);
            meanVals.setElementAt(thisVal, i);
            avgVal += thisVal.doubleValue();
        }
        // Set the avgValue member for this cluster!!!
        if (meanVals.size() != 0) {
            avgValue = avgVal / (double) meanVals.size();
        } else {
            avgValue = 0;
        }
    }

    public void sortMembers() {
        // Members are sorted by their average expression ratio values, smallest to largest....
        double[] expArray = new double[clusterMembers.size()];
        for (int i = 0; i < clusterMembers.size(); i++) {
            Instance thisInstance = (Instance) clusterMembers.elementAt(i);
            expArray[i] = thisInstance.avgExpressionRatio;
        }
        try {
            qSort.sort(expArray);
        } catch (Exception e) {
            System.out.println(
                    "Error sorting in method: ClusterObject.sortMembers()");
            System.exit(0);
        }
        Vector oldMembers = (Vector) clusterMembers.clone();
        clusterMembers = new Vector();
        for (int i = 0; i < expArray.length; i++) {
            // find the value in oldMembers that equals the current index of expArray
            for (int index = 0; index < oldMembers.size(); index++) {
                Instance thisInstance = (Instance) oldMembers.elementAt(index);
                if (thisInstance.avgExpressionRatio == expArray[i]) {
                    Instance newInstance = (Instance) oldMembers.remove(index);
                    clusterMembers.add(newInstance);
                }
            }
        }

    }


    public void displayCluster() {
        ////System.out.println("The number of Instances: " + currentSize);
        for (int i = 0; i < clusterMembers.size(); i++) {
            Instance thisInstance = (Instance) clusterMembers.elementAt(i);
            ////System.out.println(thisInstance.identifier + "  " + thisInstance.commonNameAndDescription);
        }

    }

    /**
     * This displays the members of this ClusterObject...
     */
    public void displayCluster(Vector svgFileVector, int currentRow,
                               int numArrays, int dispNum, Vector trxIDs,
                               Vector tableFileVector, String outputDirectory) {
        ////System.out.println("Cluster: " + clusterNumber);
        ////System.out.println("DispNum: " + dispNum);
        sortMembers();

        int x2 = numArrays * Cluster.squareWidth + 350;
        int y = (currentRow) * Cluster.squareHeight;
        //svgFileVector.add(new String("<line x1=\"" + (-1 * Cluster.xMargin) + "\" y1=\"" + y + "\" x2=\"" + x2 + "\" y2=\"" + y + "\" style=\"stroke: blue; stroke-width: 2;\"/>\n"));
        int midWay = clusterMembers.size() / 2;
        for (int i = 0; i < clusterMembers.size(); i++) {
            tableFileVector.add(new String("<tr>"));
            ////System.out.println("<tr>");
            Instance thisInstance = (Instance) clusterMembers.elementAt(i);
            tableFileVector.add(new String("<td>" + thisInstance.identifier +
                                           "</td><td width=\"100\"> " +
                                           thisInstance.
                                           commonNameAndDescription +
                                           "</td><td><a href=\"http://www.ncbi.nlm.nih.gov/entrez/viewer.fcgi?val=" +
                                           thisInstance.refseq +
                                           "\" target=\"_blank\">" +
                                           thisInstance.refseq + "</a></td>"));
            ////System.out.println("<td>" + thisInstance.identifier + "</td><td width=\"100\"> " + thisInstance.commonNameAndDescription + "</td><td><a href=\"http://www.ncbi.nlm.nih.gov/entrez/viewer.fcgi?val=" + thisInstance.refseq + "\" target=\"_blank\">" + thisInstance.refseq + "</a></td>");
            ////System.out.println("<td>");
            tableFileVector.add(new String("<td>"));
            Vector golist = (Vector) thisInstance.goids.clone();
            for (int j = 0; j < golist.size(); j++) {
                String id = (String) golist.elementAt(j);
                tableFileVector.add(new String(
                        "<a href=\"http://www.godatabase.org/cgi-bin/go.cgi?query=" +
                        id + "&view=query\" target=\"_blank\">" + id +
                        "</a><br>"));
                ////System.out.println("<a href=\"http://www.godatabase.org/cgi-bin/go.cgi?query=" + id + "&view=query\" target=\"_blank\">" + id + "</a><br>");
            }
            ////System.out.println("</td>");
            tableFileVector.add(new String("</td>"));
            if (i == midWay) {
		// Removed the link stuff, because it relies on flex SDK and charting components.  former is open source, latter is not....
		String xlink = "";//"<a xlink:href=\"./"+outputDirectory+"/" + Cluster.filenum + "cluster" + (dispNum - 1) + ".php\" target=\"_blank\" alt=\"\">";
                if (clusterMembers.size() != 1) {


                    int row = (currentRow + i) * Cluster.squareHeight;
                    String text = xlink + "<text x=\"" + ( -1 * Cluster.xMargin) +
                                  "\" y = \"" + row +
                                  "\" style=\"stroke: blue;\">Cluster #" +
                                  (dispNum - 1) + "</text>\n"; //</a>\n";
                    svgFileVector.add(text);
                } else {
                    // Deal w/ special case were only one gene in the cluster...
                    int row = (currentRow + 1) * Cluster.squareHeight;
                    //row = row - 5;
                    String text = xlink + "<text x=\"" + ( -1 * Cluster.xMargin) +
                                  "\" y = \"" + row +
                                  "\" style=\"stroke: blue;\">Cluster #" +
                                  (dispNum - 1) + "</text>\n"; //</a>\n";
                    svgFileVector.add(text);
                }
            }
            Vector trxOrderVector = new Vector();
	    //System.out.println("before printing instance values");
            thisInstance.printInstanceVals(svgFileVector, i, currentRow,
                                           numArrays, trxIDs, tableFileVector,
                                           Cluster.colorScheme, trxOrderVector,
                                           1);
            tableFileVector.add(new String("</tr>"));
            ////System.out.println("</tr>");
        }
        x2 = numArrays * Cluster.squareWidth + 350;
        int val = (int) .5 * Cluster.squareHeight;
        y = ((currentRow + clusterMembers.size()) * Cluster.squareHeight) +
            Cluster.squareHeight;
        //svgFileVector.add(new String("<line x1=\"" + (-1 * Cluster.xMargin) + "\" y1=\"" + y + "\" x2=\"" + x2 + "\" y2=\"" + y + "\" style=\"stroke: black; stroke-width: 10;\"/>\n"));
        //Instance anInstance = (Instance) clusterMembers.elementAt(0);

        /*for(int i = 0; i < anInstance.instanceVals.size(); i++){
          int x = i * Cluster.squareWidth;

         String rect = "<rect x=\"" + x + "\" y=\"" + y + "\" width=\"" +
                  Cluster.squareWidth + "\" height=\"" + Cluster.squareHeight + "\" style=\"fill: white;\"/>\n";
                  svgFileVector.add(rect);


         }*/
    }

    // This displays the genes in this cluster....
    public void displayClusterSVG(Vector svgFileVector, int currentRow) {
        for (int i = 0; i < clusterMembers.size(); i++) {
            Instance thisInstance = (Instance) clusterMembers.elementAt(i);
            thisInstance.printGeneNamesSVG(svgFileVector, i + currentRow,
                                           1);
        }

    }

    /**
     * This returns a vector of Integer objects.
     * The values represent the current members...
     * @return
     */
    public Vector getCurrentClusterMembers() {
        Vector currentMembers = new Vector();
        for (int i = 0; i < clusterMembers.size(); i++) {
            Instance thisInstance = (Instance) clusterMembers.elementAt(i);
            Integer thisMember = new Integer(thisInstance.instanceIndex);
            currentMembers.add(thisMember);
        }
        return currentMembers;
    }


  public void plotCluster(int k, int orderedNumber, String outputDirectory) {

        // The parameter, k, is the number of k-means clusters.
	// This is a defunct function, but i left in just in case future needs require it.

        try {

            String dataset = Cluster.filenum;
            	String phpFile = "./"+outputDirectory+"/"+dataset +
                "cluster" + orderedNumber +
                ".php";
		String mxmlFile = "./"+outputDirectory+"/chart" + dataset + "cluster" + orderedNumber + ".mxml";
		////System.out.println("phpFile is " + phpFile);
            BufferedWriter BWphp = new BufferedWriter(new FileWriter(phpFile));
		String phpSWF = "chart" + dataset + "cluster" + orderedNumber + ".swf";
	    BWphp.write("<?php\n");
	    BWphp.write("include 'header.inc';\n");
	    BWphp.write("$command = \"../flex_sdk/bin/mxmlc chart"+dataset + "cluster"+orderedNumber +".mxml >> garbagedump.txt\";\n");
	    BWphp.write("echo \"<br>$command<br>\";\n");
	    BWphp.write("$str=passthru($command);\n");



	    BWphp.write("?>\n");
	       BWphp.write("<object width=\"1000\" height=\"1000\">\n");
	    BWphp.write("<param name=\"movie\" value=\""+phpSWF + "\">\n");
	    BWphp.write("<embed src=\""+ phpSWF + "\" width=\"1000\" height=\"1000\">\n");
	    BWphp.write("</embed>\n</object>\n");
	     BWphp.write("<?php\n$end = utime(); $run = $end - $start;\n	echo \"<br><font size=\\\"1px\\\"><b>Query results returned in \";");
		BWphp.write("echo substr($run, 0, 5);\n");
		BWphp.write("echo \" secs.</b></font>\"; \n?>\n");
	    BWphp.flush();
            BWphp.close();
	



	    BufferedWriter BWmxml = new BufferedWriter(new FileWriter(mxmlFile));
            BWmxml.write("<?xml version=\"1.0\"?>\n");
	    BWmxml.write("<mx:Application xmlns:mx=\"http://www.adobe.com/2006/mxml\"	layout=\"horizontal\"	backgroundGradientColors=\"[#ffffff,#ffffff]\" initialize=\"initData();\">\n");
	    BWmxml.write("<mx:Glow id=\"glowImage\" duration=\"1000\"        alphaFrom=\"1.0\" alphaTo=\"0.3\"   blurXFrom=\"0.0\" blurXTo=\"10.0\"        blurYFrom=\"0.0\" blurYTo=\"10.0\"        color=\"0x00FF00\"/>\n");
	    BWmxml.write("<mx:Glow id=\"unglowImage\" duration=\"1000\"        alphaFrom=\"0.3\" alphaTo=\"1.0\"        blurXFrom=\"10.0\" blurXTo=\"0.0\"        blurYFrom=\"10.0\" blurYTo=\"0.0\"        color=\"0x0000FF\"/>\n");
	    BWmxml.write("    <mx:Script>    <![CDATA[	\nimport mx.collections.ArrayCollection;\nimport mx.managers.CursorManager;\n            import mx.rpc.events.InvokeEvent;  \n        import mx.rpc.events.FaultEvent;\n            import mx.rpc.events.ResultEvent;\n  [Bindable]\n	\n");

	    BWmxml.write("public var seriesDataGridProvider:ArrayCollection;\n");
	    BWmxml.write("public var trxIndex:Number;\n");
	     BWmxml.write("public function handleClick(aParam:String):void {\n");
	    BWmxml.write("geneLabel.htmlText = \"<b>Gene Selected</b>:\" + aParam;\n");
	    BWmxml.write("seriesdgProvider(aParam);\n}\n");
	    BWmxml.write("// Data initialization\npublic function initData():void{\n// Create data provider for DataGrid control\nseriesDataGridProvider = new ArrayCollection;\n}\n");
	     BWmxml.write("// Fill seriesDataGridProvider with the specified items\npublic function seriesdgProvider(gene:String):void{\ntrxIndex=1;\nseriesDataGridProvider.removeAll();\nfor(var z:int = 0; z<treatmentsAC.length; z++){\nvar obj:Object = {};\nobj.Treatment = treatmentsAC.getItemAt(z).Array;\nobj.FoldChange = treatmentsAC.getItemAt(z)[gene];\nseriesDataGridProvider.addItem(obj);\n}\n}\n");


        BWmxml.write("\n private var treatmentsAC:ArrayCollection = new ArrayCollection( [");

            // How many genes are we dealing with?
            int genes = this.currentSize;
            ////System.out.println("The size of this cluster is: " + genes);
            //double[][] expressionvals = new double[genes][Cluster.trxNames.size()];

	    //  { Array: "Chemical 1", Cyp1a1: 1.59, Saa2: -4.59, Cy1a2: 9.67 },
	for (int i = 0; i < Cluster.trxNames.size(); i++) {
		if(i != 0){
			BWmxml.write(",\n");
		}
                BWmxml.write("{Array: \"" + (String) Cluster.trxNames.elementAt(i) + "\", ");

            for (int j = 0; j < genes; j++) {
	    	if(j != 0){
			BWmxml.write(", ");
		}
                Instance thisInstance = (Instance)this.clusterMembers.elementAt(j);
		String genename = thisInstance.commonNameAndDescription;
		genename = genename.replaceAll("-", "_");
		char numcheck = genename.charAt(0);
		//Character numcheck2 = new Character(char);
		// Check to see if it is an integer value....
		if(Character.isDigit(numcheck)){
			genename = "g"+genename;
		}

		BWmxml.write(genename +": ");
                Vector instvals = (Vector) thisInstance.instanceVals;
                String dval = (String) instvals.elementAt(i);

                // If we've a -9999 value, set it to 0....
                ///System.out.print(dval + " " );



                BWmxml.write(dval + "");
            } // end of     for (int i = 0; i < genes; i++) {
	    BWmxml.write("}");
	}  // end of for (int i = 1; i < Cluster.trxNames.size(); i++)

	BWmxml.write("]);\n]]>\n</mx:Script>\n");

	BWmxml.write("<mx:Panel id=\"leftpanel\" title=\"Cluster 1\" height=\"100%\" width=\"80%\">");

	BWmxml.write("<mx:LineChart id=\"linechart\" height=\"100%\" width=\"100%\"	paddingLeft=\"5\" paddingRight=\"5\" showDataTips=\"true\" dataProvider=\"{treatmentsAC}\">");

	BWmxml.write("<mx:horizontalAxis>\n<mx:CategoryAxis categoryField=\"Array\"/>\n</mx:horizontalAxis>\n");
	BWmxml.write("<mx:series>");
	for (int j = 0; j < genes; j++) {
                Instance thisInstance = (Instance)this.clusterMembers.elementAt(
                       j);
		String genename = thisInstance.commonNameAndDescription;
		BWmxml.write("<mx:LineSeries yField=\"" + genename + "\" form=\"curve\" displayName=\"" + genename + "\" rollOverEffect=\"{glowImage}\"       mouseUpEffect=\"{unglowImage}\"  click=\"handleClick('" + genename + "');\"/>\n");
		//BWmxml.write("<mx:lineStroke>\n<mx:Stroke weight=\"1\"/>\n</mx:lineStroke>");
		//BWmxml.write("</mx:LineSeries>\n");
	}
	BWmxml.write("</mx:series>\n");

	BWmxml.write("</mx:LineChart>\n");
	BWmxml.write("</mx:Panel>\n");
	BWmxml.write("<mx:Panel id=\"legend\" height=\"100%\" width=\"10%\">\n");
	BWmxml.write("<mx:Legend dataProvider=\"{linechart}\"/>");
	BWmxml.write("</mx:Panel>\n");
	BWmxml.write("<mx:Panel id=\"rightpanel\" title=\"Data Panel\" height=\"100%\" width=\"10%\">\n");
	BWmxml.write("<mx:Label id=\"geneLabel\" text=\"No Gene Selected\"/>\n");
	BWmxml.write("<mx:DataGrid id=\"seriesDataGrid\" dataProvider=\"{seriesDataGridProvider}\">\n");
	BWmxml.write("<mx:columns>\n<mx:DataGridColumn dataField=\"Treatment\"/>\n<mx:DataGridColumn dataField=\"FoldChange\"/>\n</mx:columns>\n");
	BWmxml.write("</mx:DataGrid>\n</mx:Panel>\n");
	BWmxml.write("</mx:Application>");

            BWmxml.flush();
            BWmxml.close();


        } catch (FileNotFoundException fnfe) {
            System.out.println("EXECUTION HALTED at line 1862: Table File named does not exist!");
            System.exit(0);
        } catch (IOException ioe) {
            System.out.println("ERROR: " + ioe);
        }

    }

}


class Instance {

    // The index of the Instance object..
    int instanceIndex;
    // The identifier of this Instance object...
    String identifier;
    // The common name and description of this Instance object...
    String commonNameAndDescription;
    // The refseq for this Instance object...
    String refseq;
    // The number of GOIDs associated w/ this
    int numgoids;
    // The GOID for this Instance object...
    Vector goids;
    // The values for this Instance object...  A vector of Double objects...
    Vector instanceVals;
    // Average expression ratio....
    double avgExpressionRatio;

    // This constructor is used for chemicals....
    Instance(int index, Vector instanceVector, String chemName) {

        instanceIndex = index;
        commonNameAndDescription = chemName;
        ////System.out.println(chemName);
        ////System.out.println("After assigning goid vector");
        // Assign the values.... A vector of Double objects...
        instanceVals = (Vector) instanceVector.clone();
        // Calculate average expression ratio.
        double expSum = 0.0;
        for (int i = 0; i < instanceVals.size(); i++) {
            String dval = (String) instanceVals.elementAt(i);
            // If we've a -9999 value, set it to 0....
	  //  System.out.println("This value = " + dval);
            Double dvalue = new Double(dval);
		Double thisVal;
            if (dvalue.intValue() == -99999999) {
	   //if (dvalue.isNaN()) {
                /*
                               Integer blankValue = new Integer(i);
                               Cluster.blankIDs.add(blankValue);
                 */
                thisVal = new Double(0.0);
                expSum += thisVal.doubleValue();
            } else {
	    if (dvalue.isNaN()) {
		thisVal = new Double(0.0);
		}
		else{
               thisVal = new Double(dval);
	      }

                expSum += thisVal.doubleValue();
            }


        }

        // //System.out.println("The size of the instanceVals array = " + instanceVals.size());
        // //System.out.println("<br>");
	if(expSum <= 0.0){
        	avgExpressionRatio = expSum / (double) instanceVals.size();
	}else{
		avgExpressionRatio = 0.0;
	}
        avgExpressionRatio = expSum / (double) instanceVals.size();

    }


    Instance(int index, Vector instanceVector) {
        ////System.out.println("Creating instance " + index);
        /* for(int i = 0; i < instanceVector.size(); i++){
              String thisVal = (String) instanceVector.elementAt(i);
              System.out.print(thisVal + "  ");
         }*/
        ////System.out.println();
        // Assign the index of this instance.  Used for keeping track of what instances
        // are in what ClusterObject...
        instanceIndex = index;
        ////System.out.println(index + "\t");
        ////System.out.println("Before assigning identifier");
        // Assign the identifier.....
        // identifier IS THE CLONEID
        identifier = (String) instanceVector.remove(0);
        ////System.out.println("instance: " + index);
        ////System.out.println(identifier + "\t");
        ////System.out.println("Before assigning name");
        // Assign the name and description...
        commonNameAndDescription = (String) instanceVector.remove(0);
        // //System.out.println(commonNameAndDescription + "\t");
        ////System.out.println("Before assigning refseq");
        // Assign the refseq....
        refseq = (String) instanceVector.remove(0);
        // //System.out.println(refseq + "\t");
        ////System.out.println("Before assigning numgoids");
        // Assign the number of goids
        String num = (String) instanceVector.remove(0);
        Integer gonum = new Integer(num);
        numgoids = gonum.intValue();
        ////System.out.println("Instance: " + instanceIndex + "  " + commonNameAndDescription + "  Go Vals = "+ numgoids);
        // Assign the goids...
        goids = new Vector();
        ////System.out.println("Before assigning goid vector");
        for (int i = 0; i < numgoids; i++) {
            goids.add((String) instanceVector.remove(0));
        }
        ////System.out.println("After assigning goid vector");
        // Assign the values.... A vector of Double objects...
        instanceVals = (Vector) instanceVector.clone();
        // Calculate average expression ratio.
        double expSum = 0.0;
        for (int i = 0; i < instanceVals.size(); i++) {
            String dval = (String) instanceVals.elementAt(i);
            Double dvalue = new Double(dval);
	Double thisVal;
           if (dvalue.intValue() == -99999999) {
                /*
                           Integer blankValue = new Integer(i);
                           Cluster.blankIDs.add(blankValue);
                 */
                thisVal = new Double(0.0);
                expSum += thisVal.doubleValue();
            } else {
	     if (dvalue.isNaN()) {
		thisVal = new Double(0.0);
		}
		else{
               thisVal = new Double(dval);
	      }
                expSum += thisVal.doubleValue();
            }
            ////System.out.println(dval + "\t");
            // Double thisVal = (Double) instanceVals.elementAt(i);
	    //System.out.println("This value = " + (String) instanceVals.elementAt(i));

        }
        // //System.out.println("The size of the instanceVals array = " + instanceVals.size());
        // //System.out.println("<br>");
	if(expSum <= 0.0){
        	avgExpressionRatio = expSum / (double) instanceVals.size();
	}else{
		avgExpressionRatio = 0.0;
	}

        ////System.out.println("Finished creating instance " + index);
    }


    public void printChemInstance() {
        //System.out.print(this.commonNameAndDescription + "   ");

        for (int i = 0; i < instanceVals.size(); i++) {
            String aVal = (String) instanceVals.elementAt(i);
        //    System.out.print(aVal + "   ");
        }
        ////System.out.println();
    }

    public void printInstanceVals(Vector svgFileVector, int line,
                                  int currentRow, int numArrays, Vector trxIDs,
                                  Vector tableFileVector, int colorScheme,
                                  Vector trxOrderVector, int browser) {
	// System.out.println("in printInstanceVals");
        int i = 0;
        int y = (currentRow + line) * Cluster.squareHeight;
        int x = 0;
        int blankSpace = 0;
        String aVal = "";
        String arrayID = "";
        for (i = 0; i < instanceVals.size(); i++) {
            // If this is not a kmeans cluster option and this is not
            // a hierarchical cluster option where clustering by
            // treatments is not turned off....
	    if(Cluster.kmeans == false && Cluster.ordered ==false){
            if (Cluster.kmeans == false && Cluster.trxClusterOption != 0) {
                Integer thisTrx = (Integer) trxOrderVector.elementAt(i);
                aVal = (String) instanceVals.elementAt(thisTrx.intValue());
                arrayID = (String) trxIDs.elementAt(thisTrx.intValue());
            } else {
                aVal = (String) instanceVals.elementAt(i);
                arrayID = (String) trxIDs.elementAt(i);
            }
	    }else{
		aVal = (String) instanceVals.elementAt(i);
                arrayID = (String) trxIDs.elementAt(i);

	    }

            Double thisVal = new Double(aVal);

            double val = thisVal.doubleValue();
            String style = "";
            boolean white = false;
	    boolean grey = false;
            if (colorScheme == 0) {
                double redValue = 0;
                double greenValue = 0;

                if (val <= 0.0) {
                    if (val != 0.0 && val != -99999999) {
                        double negMult = ( -1 / val);
                        greenValue = 255 - (255 * negMult);
                    }else {
			if(val == 0.0){
                        	white = true;
			}else{

				grey = true;
			}
                    }
                    //redValue = redValue * (1-negMult);
                } else {
                    double posMult = 1 / val;
                    redValue = 255 - (255 * posMult);
                    //greenValue = greenValue - redValue;
                }
                int red = (int) redValue;
                int green = (int) greenValue;
                if (!white && !grey) {
                    style = "style=\"fill: rgb(" + red + "," + green + "," +
                            0 +
                            " );";
                } else if(grey){
			style = "style=\"fill: rgb(152,152,152);";
		}else {
                    style = "style=\"fill: white;";
                }
            } else {
                double yellowValue = 0;
                double blueValue = 0;
                if (val <= 0.0) {
                    if (val != 0.0 && val != -99999999) {
                        double negMult = ( -1 / val);
                        blueValue = 255 - (255 * negMult);
                    } else {
		    	if(val == 0.0){
                        	white = true;
			}else{

				grey = true;
			}
                    }
                    //redValue = redValue * (1-negMult);
                } else {
                    double posMult = 1 / val;
                    yellowValue = 255 - (255 * posMult);
                    //greenValue = greenValue - redValue;
                }
                int yellow = (int) yellowValue;
                int blue = (int) blueValue;
                if (!white && !grey) {
                    style = "style=\"fill: rgb(" + yellow + "," + yellow + "," +
                            blue +
                            " );";
                } else if(grey){
			style = "style=\"fill: rgb(152,152,152);";
		}else {
                    style = "style=\"fill: white;";
                }

            }
            if (val >= 2.0) {
                //style = "style=\"fill: red; stroke: white;";
                tableFileVector.add(new String("<td class=\"gtzero\">" +
                                               thisVal.doubleValue() + "</td>"));
                ////System.out.println("<td class=\"gtzero\">" + thisVal.doubleValue() + "</td>");
            } else if (val <= -2.0) {
                //style = "style=\"fill: green; stroke: white;";
                tableFileVector.add(new String("<td class=\"ltzero\">" +
                                               thisVal.doubleValue() + "</td>"));
                ////System.out.println("<td class=\"ltzero\">" + thisVal.doubleValue() + "</td>");
            } else {
                //style = "style=\"fill: black; stroke: white;";
                tableFileVector.add(new String("<td class=\"zero\">" +
                                               thisVal.doubleValue() + "</td>"));
                ////System.out.println("<td class=\"zero\">" + thisVal.doubleValue() + "</td>");
            }
            String script = "";
            if (!white) { // This is used when there's not a 0.0 value for a blank...
                if (browser == 1) {
                   /* String xlink =
                            "<a xlink:href=\"http://edge.oncology.wisc.edu/cloneinfosvg.php?cloneid=" +
                            identifier + "_" + arrayID +
                            "\" target=\"_blank\" alt=\"\">";
                    */
                	String xlink = "<a xlink:href=\"" + Cluster.featureURL  + identifier + "_"+arrayID +
                	"\" target=\"_blank\" alt=\"\">";
                    svgFileVector.add(xlink);
                } else {
                    script = "onclick=\"ci(" + identifier + "," + arrayID +
                             ")\"";
                }
            }
            String rect = "";
            if (!white) {

                rect = "<rect x=\"" + x + "\" y=\"" + y + "\" width=\"" +
                       Cluster.squareWidth + "\" height=\"" +
                       Cluster.squareHeight + "\" " +
                       style + "\" " + script + "/>\n";

            } else {
                blankSpace = 15;

                rect = "<rect x=\"" + x + "\" y=\"" + y +
                       "\" width=\"5\" height=\"" + Cluster.squareHeight +
                       "\" " +
                       style + "\"/>\n";
                //x+=Cluster.squareWidth;
            }
            x += (Cluster.squareWidth - blankSpace);
            blankSpace = 0;
            svgFileVector.add(rect);
            if (!white) {
                if (browser == 1) {
                    String xlink = "</a>";
                    svgFileVector.add(xlink);
                }
            }
        }

        //}
        //catch (IOException ioe) { //System.out.println("ERROR: "+ioe); }
    }

    public void printGeneNamesSVG(Vector svgFileVector, int line, int browser) {
        // This prints out the gene names....
	int y = 0;
	if(! Cluster.imageHeightExceeded){
      		y = (line) * Cluster.squareHeight + -5;
	}else{
		y = (line) * Cluster.squareHeight + -8;
	}
        int x = 0;
        //String rect = "<rect x=\"" + x + "\" y=\""+y+"\" width=\"20\" height=\"20\" " + style + "\"/>";
        int length = commonNameAndDescription.length();
        String name = "";
        if (length < 50) {
            name = commonNameAndDescription;
        } else {
            name = commonNameAndDescription.substring(0, 50);
        }
        String script = "";
        String xlink = "";
        String id = this.identifier;
        if (browser == 1) {
            /*xlink =
                    "<a xlink:href=\"http://edge.oncology.wisc.edu/cloneinfosvgname.php?cloneid=" +
                    id + "\" target=\"_blank\" alt=\"\">";
            */
        	xlink = "<a xlink:href=\"" + Cluster.featureURL  + identifier +    	"\" target=\"_blank\" alt=\"\">";

            svgFileVector.add(xlink);
        } else {
            script = " onclick=\"g(" + id + ")\"";
        }
		String text = "";
	if(! Cluster.imageHeightExceeded){
        	text = "<text x=\"" + x + "\" y = \"" + y +
                      "\" style=\"font-family: arial; font-size:6pt;\"" +
                      script + ">" + name + "</text>\n";
	}else{
		text = "<text x=\"" + x + "\" y = \"" + y +"\" style=\"font-family: arial; font-size:4pt;\"" + script + ">" + name + "</text>\n";

	}
        //pw.write (text + "\n");
        svgFileVector.add(text);
        if (browser == 1) {
            xlink = "</a>";
            svgFileVector.add(xlink);
        }

    }


}


/*
 * @(#)QSortAlgorithm.java	1.3   29 Feb 1996 James Gosling
 *
 * Copyright (c) 1994-1996 Sun Microsystems, Inc. All Rights Reserved.
 *
 * Permission to use, copy, modify, and distribute this software
 * and its documentation for NON-COMMERCIAL or COMMERCIAL purposes and
 * without fee is hereby granted.
 * Please refer to the file http://www.javasoft.com/copy_trademarks.html
 * for further important copyright and trademark information and to
 * http://www.javasoft.com/licensing.html for further important
 * licensing information for the Java (tm) Technology.
 *
 * SUN MAKES NO REPRESENTATIONS OR WARRANTIES ABOUT THE SUITABILITY OF
 * THE SOFTWARE, EITHER EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED
 * TO THE IMPLIED WARRANTIES OF MERCHANTABILITY, FITNESS FOR A
 * PARTICULAR PURPOSE, OR NON-INFRINGEMENT. SUN SHALL NOT BE LIABLE FOR
 * ANY DAMAGES SUFFERED BY LICENSEE AS A RESULT OF USING, MODIFYING OR
 * DISTRIBUTING THIS SOFTWARE OR ITS DERIVATIVES.
 *
 * THIS SOFTWARE IS NOT DESIGNED OR INTENDED FOR USE OR RESALE AS ON-LINE
 * CONTROL EQUIPMENT IN HAZARDOUS ENVIRONMENTS REQUIRING FAIL-SAFE
 * PERFORMANCE, SUCH AS IN THE OPERATION OF NUCLEAR FACILITIES, AIRCRAFT
 * NAVIGATION OR COMMUNICATION SYSTEMS, AIR TRAFFIC CONTROL, DIRECT LIFE
 * SUPPORT MACHINES, OR WEAPONS SYSTEMS, IN WHICH THE FAILURE OF THE
 * SOFTWARE COULD LEAD DIRECTLY TO DEATH, PERSONAL INJURY, OR SEVERE
 * PHYSICAL OR ENVIRONMENTAL DAMAGE ("HIGH RISK ACTIVITIES").  SUN
 * SPECIFICALLY DISCLAIMS ANY EXPRESS OR IMPLIED WARRANTY OF FITNESS FOR
 * HIGH RISK ACTIVITIES.
 */

/**
 * A quick sort demonstration algorithm
 * SortAlgorithm.java
 *
 * @author James Gosling
 * @author Kevin A. Smith
 * @version 	@(#)QSortAlgorithm.java	1.3, 29 Feb 1996
 */
class QSortAlgorithm {
    /** This is a generic version of C.A.R Hoare's Quick Sort
     * algorithm.  This will handle arrays that are already
     * sorted, and arrays with duplicate keys.<BR>
     *
     * If you think of a one dimensional array as going from
     * the lowest index on the left to the highest index on the right
     * then the parameters to this function are lowest index or
     * left and highest index or right.  The first time you call
     * this function it will be withthis.normalizerelativeprobs(relpopsum); the parameters 0, a.length - 1.
     *
     * @param a       an integer array
     * @param lo0     left boundary of array partition
     * @param hi0     right boundary of array partition
     */
    void QuickSort(int a[], int lo0, int hi0) throws Exception {
        int lo = lo0;
        int hi = hi0;
        int mid;

        // pause for redraw

        if (hi0 > lo0) {

            /* Arbitrarily establishing partition element as the midpoint of
             * the array.
             */
            mid = a[(lo0 + hi0) / 2];

            // loop through the array until indices cross
            while (lo <= hi) {
                /* find the first element that is greater than or equal to
                 * the partition element starting from the left Index.
                 */
                while ((lo < hi0) && (a[lo] < mid)) {
                    ++lo;
                }

                /* find an element that is smaller than or equal to
                 * the partition element starting from the right Index.
                 */
                while ((hi > lo0) && (a[hi] > mid)) {
                    --hi;
                }

                // if the indexes have not crossed, swap
                if (lo <= hi) {
                    swap(a, lo, hi);

                    ++lo;
                    --hi;
                }
            }

            /* If the right index has not reached the left side of array
             * must now sort the left partition.
             */
            if (lo0 < hi) {
                QuickSort(a, lo0, hi);
            }

            /* If the left index has not reached the right side of array
             * must now sort the right partition.
             */
            if (lo < hi0) {
                QuickSort(a, lo, hi0);
            }

        }
    }

    /**
     * NOTE: I added the ability to sort arrays of double(s)
     **/
    void QuickSortDouble(double a[], int lo0, int hi0) throws Exception {
        int lo = lo0;
        int hi = hi0;
        double mid;

        // pause for redraw

        if (hi0 > lo0) {

            /* Arbitrarily establishing partition element as the midpoint of
             * the array.
             */
            mid = a[(lo0 + hi0) / 2];

            // loop through the array until indices cross
            while (lo <= hi) {
                /* find the first element that is greater than or equal to
                 * the partition element starting from the left Index.
                 */
                while ((lo < hi0) && (a[lo] < mid)) {
                    ++lo;
                }

                /* find an element that is smaller than or equal to
                 * the partition element starting from the right Index.
                 */
                while ((hi > lo0) && (a[hi] > mid)) {
                    --hi;
                }

                // if the indexes have not crossed, swap
                if (lo <= hi) {
                    swap(a, lo, hi);

                    ++lo;
                    --hi;
                }
            }

            /* If the right index has not reached the left side of array
             * must now sort the left partition.
             */
            if (lo0 < hi) {
                QuickSortDouble(a, lo0, hi);
            }

            /* If the left index has not reached the right side of array
             * must now sort the right partition.
             */
            if (lo < hi0) {
                QuickSortDouble(a, lo, hi0);
            }

        }
    }

    /**
     * NOTE: I ADDED THIS MODIFIED swap method
     **/
    private void swap(double a[], int i, int j) {
        double T;
        T = a[i];
        a[i] = a[j];
        a[j] = T;

    }

    private void swap(int a[], int i, int j) {
        int T;
        T = a[i];
        a[i] = a[j];
        a[j] = T;

    }

    public void sort(int a[]) throws Exception {
        QuickSort(a, 0, a.length - 1);
    }

    /**
     * NOTE: I ADDED THIS MODIFIED sort method
     **/
    public void sort(double a[]) throws Exception {
        QuickSortDouble(a, 0, a.length - 1);
    }

}


class Node {

    public int nodeVal;
    public String nodeName;
    public int nodeParent;
    public double edgeLength;
    public int mtxVal;
    public int child1;
    public double child1EdgeLength;
    public int child2;
    public double child2EdgeLength;
    boolean visited;

    public int x1;
    public int y1;
    public int x2;
    public int y2;

    Node(int val) {
        nodeVal = val;
        mtxVal = val;
        nodeName = "";
        visited = false;
    }

    // This is the default constructor for all leaf nodes...
    Node(int val, String name) {
        edgeLength = 1.0;
        nodeVal = val;
        // Set the children to -1
        // When both child1 and child2 are -1, then we know this is
        // a leaf node....
        child1 = -1;
        child2 = -1;
        nodeParent = -1;
        visited = false;
        nodeName = name;
    }

    // This is the default constructor for all interior nodes...
    Node(int val, double height) {
        edgeLength = height;
        nodeVal = val;
        nodeName = "";
        // Set the children to -1
        // When both child1 and child2 are -1, then we know this is
        // a leaf node....
        child1 = -1;
        child2 = -1;
        nodeParent = -1;
        visited = false;
    }


    Node(int val, String name, int parent, double length) {
        nodeVal = val;
        nodeName = name;
        nodeParent = parent;
        edgeLength = length;
        visited = false;
    }

    public void setParent(int parent) {
        nodeParent = parent;
    }

    public void setEdgeLength(double length) {
        edgeLength = length;
    }

    public void setKid1(int childOne, double c1EdgeLength) {
        child1 = childOne;
        child1EdgeLength = c1EdgeLength;
    }

    public void setKid2(int childTwo, double c2EdgeLength) {
        child2 = childTwo;
        child2EdgeLength = c2EdgeLength;
    }

    public String toString() {
        String node = "Node: " + this.nodeVal + " Name: " + this.nodeName +
                      "   parent: " + this.nodeParent + "    height: " +
                      this.edgeLength;
        node = node + "   left: " + this.child1 + "   right: " + this.child2;
        return node;
    }


    public void traverse(Vector nodes, Vector dataArray,
                         Vector orderedInstances, int x, int y,
                         Vector dendroSVG) {
        //  //System.out.println("searching at node " + this.nodeVal);
        Node leftChild = null;
        Node rightChild = null;
        if (this.child1 != -1) {
            leftChild = (Node) nodes.elementAt(this.child1);
        }
        if (this.child2 != -1) {
            rightChild = (Node) nodes.elementAt(this.child2);
        }
        if (leftChild == null && rightChild == null) {
            Instance thisInstance = (Instance) dataArray.elementAt(this.nodeVal);
            orderedInstances.add(thisInstance);
            return;
        }
        boolean searchleft = false;
        boolean searchright = false;
        if (leftChild != null) {
            // Now check to see if these child nodes have any kids....
            if (leftChild.child1 == -1 && leftChild.child2 == -1) {
                //  //System.out.println("The left child of " + this.nodeVal + " is " +
                //                    leftChild.nodeVal);
                Instance left = (Instance) dataArray.elementAt(leftChild.
                        nodeVal);
                orderedInstances.add(left);
                //  //System.out.println(left.instanceIndex + "  " +
                //                  left.commonNameAndDescription);

                searchleft = false;
            } else {
                searchleft = true;
            }
        }
        if (rightChild != null) {
            if (rightChild.child1 == -1 && rightChild.child2 == -1) {
                ////System.out.println("The right child of " + this.nodeVal + " is " +
                //                 rightChild.nodeVal);
                Instance right = (Instance) dataArray.elementAt(rightChild.
                        nodeVal);
                orderedInstances.add(right);
                // //System.out.println(right.instanceIndex + "  " +
                //           right.commonNameAndDescription);
                searchright = false;
            } else {
                searchright = true;
            }
        }
        if (searchleft == true) {
            leftChild.traverse(nodes, dataArray, orderedInstances, x, y,
                               dendroSVG);
        }
        if (searchright == true) {
            rightChild.traverse(nodes, dataArray, orderedInstances, x, y,
                                dendroSVG);
        }

    }

    static void printLevel(int k) {
        for (int j = 0; j < k; j++) {
            //System.out.print("\t");
        }
    }


    static void printGeneDots(Vector nodes, Node node, int level,
                              Vector svgVector, int maxHeight) {

        if (null != node) {
            Node right = null;
            Node left = null;

            if (node.child1 != -1) {
                left = (Node) nodes.elementAt(node.child1);
                //level = (int) (Math.abs(left.edgeLength-node.edgeLength));
            }
            printGeneDots(nodes, left, level + 1, svgVector, maxHeight);
            int thisLevel = level * ((int) Cluster.squareHeight / 2);

            int x = (int) ((maxHeight - node.edgeLength) * 10) + 5;

            node.x1 = x;
            int y = Cluster.svgDendroLine++ * ((int) Cluster.squareHeight / 2);
            node.y1 = y;
            String svg = "";
            if (level == maxHeight) {
                svg = "<circle cx=\"" + x + "\" cy=\"" + y +
                      "\" r=\"5\" style=\"stroke: black; fill: red;\"/>\n";
            } else {
                svg = "<g id=\"" + x + "_" + y + "\">\n";
                svg += "<circle cx=\"" + x + "\" cy=\"" + y +
                        "\" r=\"2.5\" style=\"stroke: black; fill: blue;\"/>\n</g>\n";
            }
            svgVector.add(svg);

            ////System.out.println(node.nodeVal+ "node length: " + node.edgeLength);


            if (node.child2 != -1) {
                right = (Node) nodes.elementAt(node.child2);

                //level = (int) (node.edgeLength - right.edgeLength);
            }
            printGeneDots(nodes, right, level, svgVector, maxHeight);
        }
    }

    static void connectGeneDots(Vector nodes, Node node, Vector svgVector) {

        if (null != node) {
            ////System.out.println(node.toString());
            Node right = null;
            Node left = null;
            if (node.child1 != -1) {
                left = (Node) nodes.elementAt(node.child1);
            }
            connectGeneDots(nodes, left, svgVector);

            Node parent = null;
            boolean drawGraphLines = false;
            if (node.nodeParent != -1) {
                parent = (Node) nodes.elementAt(node.nodeParent);
                drawGraphLines = true;
            }
            if (drawGraphLines == true) {
                if (parent.x1 != 0) {
                    String graphLine = "<line x1=\"" + node.x1 + "\" y1=\"" +
                                       node.y1 +
                                       "\" x2=\"" +
                                       parent.x1 + "\" y2=\"" + node.y1 +
                                       "\" style=\"stroke-width: 1; stroke: black;\"/>\n";
                    svgVector.add(graphLine);
                    graphLine = "<line x1=\"" + parent.x1 + "\" y1=\"" +
                                node.y1 +
                                "\" x2=\"" +
                                parent.x1 + "\" y2=\"" + parent.y1 +
                                "\" style=\"stroke-width: 1; stroke: black;\"/>\n";
                    svgVector.add(graphLine);

                }
            }
            if (node.child2 != -1) {
                right = (Node) nodes.elementAt(node.child2);
            }
            connectGeneDots(nodes, right, svgVector);

        }
    }

    static void printTrxDots(Vector nodes, Node node, int level,
                             Vector svgVector, int maxHeight) {

        if (null != node) {
            Node right = null;
            Node left = null;

            if (node.child1 != -1) {
                left = (Node) nodes.elementAt(node.child1);
                //level = (int) (Math.abs(left.edgeLength-node.edgeLength));
            }
            printTrxDots(nodes, left, level + 1, svgVector, maxHeight);
            int thisLevel = level * ((int) Cluster.squareHeight / 2);
            int y = 0;
            if (node.child1 == -1) {
                y = 150;
            } else {
                y = (int) ((maxHeight - node.edgeLength) * 10) + 5;
            }

            node.y1 = y;
            int x = Cluster.svgChemCol++ * ((int) Cluster.squareWidth / 2);
            node.x1 = x;
            String svg = "";
            if (level == maxHeight) {
                svg = "<circle cx=\"" + x + "\" cy=\"" + y +
                      "\" r=\"5\" style=\"stroke: black; fill: red;\"/>\n";
            } else {
                svg = "<g id=\"" + x + "_" + y + "\">\n";
                svg += "<circle cx=\"" + x + "\" cy=\"" + y +
                        "\" r=\"2.5\" style=\"stroke: blue; fill: blue;\"/>\n</g>\n";
            }
            svgVector.add(svg);

            ////System.out.println(node.nodeVal+ "node length: " + node.edgeLength);


            if (node.child2 != -1) {
                right = (Node) nodes.elementAt(node.child2);

                //level = (int) (node.edgeLength - right.edgeLength);
            }
            printTrxDots(nodes, right, level, svgVector, maxHeight);
        }
    }

    static void connectTrxDots(Vector nodes, Node node, Vector svgVector) {

        if (null != node) {
            ////System.out.println(node.toString());
            Node right = null;
            Node left = null;
            if (node.child1 != -1) {
                left = (Node) nodes.elementAt(node.child1);
            }
            connectTrxDots(nodes, left, svgVector);

            Node parent = null;
            boolean drawGraphLines = false;
            if (node.nodeParent != -1) {
                parent = (Node) nodes.elementAt(node.nodeParent);
                drawGraphLines = true;
            }
            if (drawGraphLines == true) {
                if (parent.x1 != 0) {
                    String horzLine = "<line x1=\"" + node.x1 + "\" y1=\"" +
                                      parent.y1 +
                                      "\" x2=\"" +
                                      parent.x1 + "\" y2=\"" + parent.y1 +
                                      "\" style=\"stroke-width: 1; stroke: black;\"/>\n";
                    svgVector.add(horzLine);

                    String vertLine = "<line x1=\"" + node.x1 + "\" y1=\"" +
                                      parent.y1 +
                                      "\" x2=\"" +
                                      node.x1 + "\" y2=\"" + node.y1 +
                                      "\" style=\"stroke-width: 1; stroke: gray;\"/>\n";

                    svgVector.add(vertLine);

                }
            }
            if (node.child2 != -1) {
                right = (Node) nodes.elementAt(node.child2);
            }
            connectTrxDots(nodes, right, svgVector);

        }
    }

}


/*
 *   Statistics  -- A collection of useful statistical methods.
 *
 *   Copyright (C) 2001-2004 by Joseph A. Huwaldt
 *   All rights reserved.
 *
 *   This library is free software; you can redistribute it and/or
 *   modify it under the terms of the GNU Lesser General Public
 *   License as published by the Free Software Foundation; either
 *   version 2 of the License, or (at your option) any later version.
 *
 *   This library is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 *   Lesser General Public License for more details.
 *
 *   You should have received a copy of the GNU Lesser General Public License
 *   along with this program; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *   Or visit:  http://www.gnu.org/licenses/lgpl.html
 **/
//package jahuwaldt.tools.math;




/**
 *  A utility class containing collection of useful static routines for calculating
 *  certain statistical properties.
 *
 *  <p>  Modified by:  Joseph A. Huwaldt  </p>
 *
 *  @author   Joseph A. Huwaldt   Date:  March 7, 2001
 *  @version  November 19, 2004
 **/
class Statistics {


    //-----------------------------------------------------------------------------------

    /**
     *  Prevent the user from instantiating this class.
     **/
    private Statistics() {}


    /**
     *  Returns the minimum value in an array of sample data.
     *  The minimum value is defined as the most negative value.
     *
     *  @param   arr  An array of sample data values.
     *  @return  The minimum value in the sample data array.
     **/
    public static final double min(double[] arr) {
        double min = Double.MAX_VALUE;
        int length = arr.length;

        for (int i = 0; i < length; ++i) {
            if (arr[i] < min) {
                min = arr[i];
            }
        }

        return min;
    }

    /**
     *  Returns the minimum value in an array of sample data.
     *  The minimum value is defined as the most negative value.
     *
     *  @param   arr  An array of sample data values.
     *  @return  The minimum value in the sample data array.
     **/
    public static final float min(float[] arr) {
        float min = Float.MAX_VALUE;
        int length = arr.length;

        for (int i = 0; i < length; ++i) {
            if (arr[i] < min) {
                min = arr[i];
            }
        }

        return min;
    }

    /**
     *  Returns the maximum value in an array of sample data.
     *  The maximum value is defined as the most positive value.
     *
     *  @param   arr  An array of sample data values.
     *  @return  The maximum value in the sample data array.
     **/
    public static final double max(double[] arr) {
        double max = -Double.MAX_VALUE;
        int length = arr.length;

        for (int i = 0; i < length; ++i) {
            if (arr[i] > max) {
                max = arr[i];
            }
        }

        return max;
    }

    /**
     *  Returns the maximum value in an array of sample data.
     *  The maximum value is defined as the most positive value.
     *
     *  @param   arr  An array of sample data values.
     *  @return  The maximum value in the sample data array.
     **/
    public static final float max(float[] arr) {
        float max = -Float.MAX_VALUE;
        int length = arr.length;

        for (int i = 0; i < length; ++i) {
            if (arr[i] > max) {
                max = arr[i];
            }
        }

        return max;
    }

    /**
     *  Returns the range of the data in the specified array.
     *  Range is the difference between the maximum and minimum
     *  values in the data set.
     *
     *  @param  arr  An array of sample data values.
     *  @return The range of the data in the input array.
     **/
    public static final double range(double[] arr) {
        return max(arr) - min(arr);
    }

    /**
     *  Returns the range of the data in the specified array.
     *  Range is the difference between the maximum and minimum
     *  values in the data set.
     *
     *  @param  arr  An array of sample data values.
     *  @return The range of the data in the input array.
     **/
    public static final float range(float[] arr) {
        return max(arr) - min(arr);
    }

    /**
     *  Returns the sum of all the elements in the specified data array.
     *
     *  @param  arr  An array of sample data values to be added together.
     *  @return The sum of all the sample data values in arr.
     **/
    public static final double sum(double[] arr) {
        double sum = 0;
        int size = arr.length;

        for (int i = 0; i < size; ++i) {
            sum += arr[i];
        }

        return sum;
    }

    /**
     *  Returns the sum of all the elements in the specified data array.
     *
     *  @param  arr  An array of sample data values to be added together.
     *  @return The sum of all the sample data values in arr.
     **/
    public static final float sum(float[] arr) {
        float sum = 0;
        int size = arr.length;

        for (int i = 0; i < size; ++i) {
            sum += arr[i];
        }

        return sum;
    }

    /**
     *  Returns the mean or average of an array of data values.
     *
     *  @param   arr  An array of sample data values.
     *  @return  The mean or average of the input data values.
     **/
    public static final double mean(double[] arr) {
        int size = arr.length;
        double sum = sum(arr);
        return sum / size;
    }

    /**
     *  Returns the mean or average of an array of data values.
     *
     *  @param   arr  An array of sample data values.
     *  @return  The mean or average of the input data values.
     **/
    public static final float mean(float[] arr) {
        int size = arr.length;
        float sum = sum(arr);
        return sum / size;
    }

    /**
     *  Returns the p-th percentile of values in an array. You can use this
     *  function to establish a threshold of acceptance. For example, you can
     *  decide to examine candidates who score above the 90th percentile (0.9).
     *  The elements of the input array are modified (sorted) by this method.
     *
     *  @param   arr  An array of sample data values that define relative standing.
     *                The contents of the input array are sorted by this method.
     *  @param   p    The percentile value in the range 0..1, inclusive.
     *  @return  The p-th percentile of values in an array.  If p is not a multiple
     *           of 1/(n - 1), this method interpolates to determine the value at
     *           the p-th percentile.
     **/
    public static final double percentile(double[] arr, double p) {

        if (p < 0 || p > 1) {
            throw new IllegalArgumentException("Percentile out of range.");
        }

        //	Sort the array in ascending order.
        Arrays.sort(arr);

        //	Calculate the percentile.
        double t = p * (arr.length - 1);
        int i = (int) t;

        return ((i + 1 - t) * arr[i] + (t - i) * arr[i + 1]);
    }

    /**
     *  Returns the p-th percentile of values in an array. You can use this
     *  function to establish a threshold of acceptance. For example, you can
     *  decide to examine candidates who score above the 90th percentile (0.9).
     *  The elements of the input array are modified (sorted) by this method.
     *
     *  @param   arr  An array of sample data values that define relative standing.
     *                The contents of the input array are sorted by this method.
     *  @param   p    The percentile value in the range 0..1, inclusive.
     *  @return  The p-th percentile of values in an array.  If p is not a multiple
     *           of 1/(n - 1), this method interpolates to determine the value at
     *           the p-th percentile.
     **/
    public static final float percentile(float[] arr, float p) {

        if (p < 0 || p > 1) {
            throw new IllegalArgumentException("Percentile out of range.");
        }

        //	Sort the array in ascending order.
        Arrays.sort(arr);

        //	Calculate the percentile.
        float t = p * (arr.length - 1);
        int i = (int) t;

        return ((i + 1 - t) * arr[i] + (t - i) * arr[i + 1]);
    }

    /**
     *  Returns the median of the values in an array. The median is the same
     *  as the 50th percentile.
     *
     *  @param   arr  An array of sample data values that define relative standing.
     *                The contents of the input array are sorted by this method.
     *  @return  The median (50th percentile) of values in an array.
     *           Interpolation is used if the number of array elements is odd.
     **/
    public static final double median(double[] arr) {
        return percentile(arr, 0.5);
    }

    /**
     *  Returns the median of the values in an array. The median is the same
     *  as the 50th percentile.
     *
     *  @param   arr  An array of sample data values that define relative standing.
     *                The contents of the input array are sorted by this method.
     *  @return  The median (50th percentile) of values in an array.
     *           Interpolation is used if the number of array elements is odd.
     **/
    public static final float median(float[] arr) {
        return percentile(arr, 0.5f);
    }

    /**
     *  Returns the first quartile of the values in an array. The 1st quartile
     *   is the same as the 25th percentile.
     *
     *  @param   arr  An array of sample data values that define relative standing.
     *                The contents of the input array are sorted by this method.
     *  @return  The first quartile (25th percentile) of values in an array.
     *           Interpolation is used if necissary.
     **/
    public static final double quartile1(double[] arr) {
        return percentile(arr, 0.25);
    }

    /**
     *  Returns the first quartile of the values in an array. The 1st quartile
     *   is the same as the 25th percentile.
     *
     *  @param   arr  An array of sample data values that define relative standing.
     *                The contents of the input array are sorted by this method.
     *  @return  The first quartile (25th percentile) of values in an array.
     *           Interpolation is used if necissary.
     **/
    public static final float quartile1(float[] arr) {
        return percentile(arr, 0.25f);
    }

    /**
     *  Returns the third quartile of the values in an array. The 3rd quartile
     *   is the same as the 75th percentile.
     *
     *  @param   arr  An array of sample data values that define relative standing.
     *                The contents of the input array are sorted by this method.
     *  @return  The third quartile (75th percentile) of values in an array.
     *           Interpolation is used if necissary.
     **/
    public static final double quartile3(double[] arr) {
        return percentile(arr, 0.75);
    }

    /**
     *  Returns the third quartile of the values in an array. The 3rd quartile
     *   is the same as the 75th percentile.
     *
     *  @param   arr  An array of sample data values that define relative standing.
     *                The contents of the input array are sorted by this method.
     *  @return  The third quartile (75th percentile) of values in an array.
     *           Interpolation is used if necissary.
     **/
    public static final float quartile3(float[] arr) {
        return percentile(arr, 0.75f);
    }

    /**
     *  Returns the root mean square of an array of sample data.
     *
     *  @param   arr  An array of sample data values.
     *  @return  The root mean square of the sample data.
     **/
    public static final double rms(double[] arr) {
        int size = arr.length;
        double sum = 0;
        for (int i = 0; i < size; ++i) {
            sum += arr[i] * arr[i];
        }

        return Math.sqrt(sum / size);
    }

    /**
     *  Returns the root mean square of an array of sample data.
     *
     *  @param   arr  An array of sample data values.
     *  @return  The root mean square of the sample data.
     **/
    public static final float rms(float[] arr) {
        int size = arr.length;
        float sum = 0;
        for (int i = 0; i < size; ++i) {
            sum += arr[i] * arr[i];
        }

        return (float) Math.sqrt(sum / size);
    }

    /**
     *  Returns the variance of an array of sample data.
     *
     *  @param  arr  An array of sample data values.
     *  @return The variance of the sample data.
     **/
    public static final double variance(double[] arr) {
        int n = arr.length;
        if (n < 2) {
            throw new IllegalArgumentException(
                    "Must be at least 2 elements in array.");
        }

        //	1st get the average of the data.
        double ave = mean(arr);

        double var = 0;
        double ep = 0;
        for (int i = 0; i < n; ++i) {
            double s = arr[i] - ave;
            ep += s;
            var += s * s;
        }

        var = (var - ep * ep / n) / (n - 1);

        return var;
    }

    /**
     *  Returns the variance of an array of sample data.
     *
     *  @param  arr  An array of sample data values.
     *  @return The variance of the sample data.
     **/
    public static final float variance(float[] arr) {
        int n = arr.length;
        if (n < 2) {
            throw new IllegalArgumentException(
                    "Must be at least 2 elements in array.");
        }

        //	1st get the average of the data.
        float ave = mean(arr);

        float var = 0;
        float ep = 0;
        for (int i = 0; i < n; ++i) {
            float s = arr[i] - ave;
            ep += s;
            var += s * s;
        }

        var = (var - ep * ep / n) / (n - 1);

        return var;
    }

    /**
     *  Returns the standard deviation of an array of sample data.
     *
     *  @param  arr  An array of sample data values.
     *  @return The standard deviation of the sample data.
     **/
    public static final double sdev(double[] arr) {
        return Math.sqrt(variance(arr));
    }

    /**
     *  Returns the standard deviation of an array of sample data.
     *
     *  @param  arr  An array of sample data values.
     *  @return The standard deviation of the sample data.
     **/
    public static final float sdev(float[] arr) {
        return (float) Math.sqrt(variance(arr));
    }

    /**
     *  Returns the skewness of an array of sample data.
     *  Skewness characterises the degree of asymmetry of a distribution
     *  of data around it's mean.
     *
     *  @param  arr  An array of sample data values.
     *  @return The skewness of the sample data.
     **/
    public static final double skew(double[] arr) throws Exception {
        int n = arr.length;
        if (n < 2) {
            throw new IllegalArgumentException(
                    "Must be at least 2 elements in array.");
        }

        //	1st get the average of the data.
        double ave = mean(arr);

        double var = 0;
        double skew = 0;
        double ep = 0;
        for (int i = 0; i < n; ++i) {
            double s = arr[i] - ave;
            ep += s;
            double p = s * s;
            var += p;
            p *= s;
            skew += p;
        }

        var = (var - ep * ep / n) / (n - 1);
        double sdev = Math.sqrt(var);

        if (var == 0) {
            throw new Exception("No skew when variance = 0.");
        }

        skew /= n * var * sdev;

        return skew;
    }

    /**
     *  Returns the skewness of an array of sample data.
     *  Skewness characterises the degree of asymmetry of a distribution
     *  of data around it's mean.
     *
     *  @param  arr  An array of sample data values.
     *  @return The skewness of the sample data.
     **/
    public static final float skew(float[] arr) throws Exception {
        int n = arr.length;
        if (n < 2) {
            throw new IllegalArgumentException(
                    "Must be at least 2 elements in array.");
        }

        //	1st get the average of the data.
        float ave = mean(arr);

        float var = 0;
        float skew = 0;
        float ep = 0;
        for (int i = 0; i < n; ++i) {
            float s = arr[i] - ave;
            ep += s;
            float p = s * s;
            var += p;
            p *= s;
            skew += p;
        }

        var = (var - ep * ep / n) / (n - 1);
        float sdev = (float) Math.sqrt(var);

        if (var == 0) {
            throw new Exception("No skew when variance = 0.");
        }

        skew /= n * var * sdev;

        return skew;
    }


    /**
     *  Returns the kurtosis of an array of sample data.
     *  Kurtosis measures the relative peakedness or flatness of a distribution
     *  relative to a normal distribution.
     *
     *  @param  arr  An array of sample data values.
     *  @return The kurtosis of the sample data.
     **/
    public static final double kurtosis(double[] arr) throws Exception {
        int n = arr.length;
        if (n < 2) {
            throw new IllegalArgumentException(
                    "Must be at least 2 elements in array.");
        }

        //	1st get the average of the data.
        double ave = mean(arr);

        double var = 0;
        double curt = 0;
        double ep = 0;
        for (int i = 0; i < n; ++i) {
            double s = arr[i] - ave;
            ep += s;
            double p = s * s;
            var += p;
            curt = p * p;
        }

        var = (var - ep * ep / n) / (n - 1);

        if (var == 0) {
            throw new Exception("No kurtosis when the variance = 0.");
        }

        curt = curt / (n * var * var) - 3.;

        return curt;
    }

    /**
     *  Returns the kurtosis of an array of sample data.
     *  Kurtosis measures the relative peakedness or flatness of a distribution
     *  relative to a normal distribution.
     *
     *  @param  arr  An array of sample data values.
     *  @return The kurtosis of the sample data.
     **/
    public static final float kurtosis(float[] arr) throws Exception {
        int n = arr.length;
        if (n < 2) {
            throw new IllegalArgumentException(
                    "Must be at least 2 elements in array.");
        }

        //	1st get the average of the data.
        float ave = mean(arr);

        float var = 0;
        float curt = 0;
        float ep = 0;
        for (int i = 0; i < n; ++i) {
            float s = arr[i] - ave;
            ep += s;
            float p = s * s;
            var += p;
            curt = p * p;
        }

        var = (var - ep * ep / n) / (n - 1);

        if (var == 0) {
            throw new Exception("No kurtosis when the variance = 0.");
        }

        curt = curt / (n * var * var) - 3;

        return curt;
    }


    /**
     *  Used to test out the methods in this class.
     **/
    public static void main(String args[]) {

        double[] arr = {1, 23, 2, 87, 56, 33, 10, 9, 25, 89, 99, 26, 43, 48, 55,
                       77, 15, 19};

        try {

            System.out.println();
            System.out.println("Testing Statistics...");

            System.out.println("  min(arr) = " + min(arr) + ", should be 1.");
            System.out.println("  max(arr) = " + max(arr) + ", should be 99.");
            System.out.println("  range(arr) = " + range(arr) +
                               ", should be 98.");
            System.out.println("  sum(arr) = " + sum(arr) + ", should be 717.");
            System.out.println("  mean(arr) = " + mean(arr) +
                               ", should be 39.8333...");
            System.out.println("  percentile(arr, 0.95) = " +
                               percentile(arr, 0.95) + ", should be 90.5");
            System.out.println("  median(arr) = " + median(arr) +
                               ", should be 29.5.");
            System.out.println("  rms(arr) = " + rms(arr) +
                               ", should be 50.1248...");
            System.out.println("  variance(arr) = " + variance(arr) +
                               ", should be 980.2647...");
            System.out.println("  sdev(arr) = " + sdev(arr) +
                               ", should be 31.309...");
            System.out.println("  skew(arr) = " + skew(arr) +
                               ", should be 0.51879...");
            System.out.println("  kurtosis(arr) = " + kurtosis(arr) +
                               ", should be -2.29148...");

        } catch (Exception e) {
            e.printStackTrace();
        }

    }


}


/*
 *   MathTools  -- A collection of useful math utility routines.
 *
 *   Copyright (C) 1999-2004 by Joseph A. Huwaldt
 *   All rights reserved.
 *
 *   This library is free software; you can redistribute it and/or
 *   modify it under the terms of the GNU Lesser General Public
 *   License as published by the Free Software Foundation; either
 *   version 2 of the License, or (at your option) any later version.
 *
 *   This library is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 *   Lesser General Public License for more details.
 *
 *   You should have received a copy of the GNU Lesser General Public License
 *   along with this program; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *   Or visit:  http://www.gnu.org/licenses/lgpl.html
 **/




/**
 *  A collection of useful static routines of a general
 *  mathematical nature.  This file includes functions that
 *  accomplish all types of wonderous mathematical stuff.
 *
 *  <p>  Modified by:  Joseph A. Huwaldt  </p>
 *
 *  @author   Joseph A. Huwaldt   Date:  September 29, 1997
 *  @version July 24, 2004
 **/
class MathTools {

    /**
     *  The natural logarithm of 10.
     **/
    public static final double LOG10 = Math.log(10);

    /**
     *  The natural logarithm of 2.
     **/
    public static final double LOG2 = Math.log(2);

    /**
     *  The natural logarithm of the maximum double value:  log(MAX_VALUE).
     **/
    public static final double MAX_LOG = Math.log(Double.MAX_VALUE);

    /**
     *  The natural logarithm of the minimum double value:  log(MIN_VALUE).
     **/
    public static final double MIN_LOG = Math.log(Double.MIN_VALUE);

    /**
     *  Prevent anyone from instantiating this utiltity class.
     **/
    private MathTools() {}

    //-----------------------------------------------------------------------------------
    /**
     *  Test to see if a given long integer is even.
     *
     *  @param   n  Integer number to be tested.
     *  @return  True if the number is even, false if it is odd.
     **/
    public static final boolean even(long n) {
        return (n & 1) == 0;
    }

    /**
     *  Test to see if a given long integer is odd.
     *
     *  @param   n  Integer number to be tested.
     *  @return  True if the number is odd, false if it is even.
     **/
    public static final boolean odd(long n) {
        return (n & 1) != 0;
    }

    /**
     *  Calculates the square (x^2) of the argument.
     *
     *  @param   x  Argument to be squared.
     *  @return  Returns the square (x^2) of the argument.
     **/
    public static final double sqr(double x) {
        if (x == 0.) {
            return 0.;
        } else {
            return x * x;
        }
    }

    /**
     *  Computes the cube root of the specified real number.
     *  If the argument is negative, then the cube root is negative.
     *
     *  @param   x  Argument for which the cube root is to be found.
     *  @return  The cube root of the argument is returned.
     **/
    public static final double cubeRoot(double x) {
        double value = 0;

        if (x < 0.) {
            value = -Math.exp(Math.log( -x) / 3.);
        } else {
            value = Math.exp(Math.log(x) / 3.);
        }

        return value;
    }

    /**
     *  Returns a number "a" raised to the power "b".  A "long" version
     *  of Math.pow().  This is much faster than using Math.pow() if
     *  the operands are integers.
     *
     *  @param   a  Number to be raised to the power "b".
     *  @param   b  Power to raise number "a" to.
     *  @return  A long integer "a" raised to the integer power "b".
     *  @throws  ArithmeticException if "b" is negative.
     **/
    public static final long pow(long a, long b) throws ArithmeticException {
        if (b < 0) {
            throw new ArithmeticException("Exponent must be positive.");
        }

        long r = 1;
        while (b != 0) {
            if (odd(b)) {
                r *= a;
            }

            b >>>= 1;
            a *= a;
        }
        return r;
    }

    /**
     *  Raises 2 to the small integer power indicated (eg:  2^3 = 8).
     *  This is MUCH faster than calling Math.pow(2, x).
     *
     *  @param   x  Amount to raise 2 to the power of.
     *  @return  Returns 2 raised to the power indicated.
     **/
    public static final long pow2(long x) {
        long value = 1;
        for (long i = 0; i < x; ++i) {
            value *= 2;
        }
        return value;
    }

    /**
     *  Raises 10 to the small integer power indicated (eg: 10^5 = 100000).
     *  This is faster than calling Math.pow(10, x).
     *
     *  @param   x  Amount to raise 10 to the power of.
     *  @return  Returns 10 raised to the power indicated.
     **/
    public static final double pow10(int x) {
        double pow10 = 10.;

        if (x != 0) {
            boolean neg = false;
            if (x < 0) {
                x *= -1;
                neg = true;
            }

            for (int i = 1; i < x; ++i) {
                pow10 *= 10.;
            }

            if (neg) {
                pow10 = 1. / pow10;
            }

        } else {
            pow10 = 1.;
        }

        return (pow10);
    }

    /**
     *  Find the base 10 logarithm of the given double.
     *
     *  @param   x  Value to find the base 10 logarithm of.
     *  @return  The base 10 logarithm of x.
     **/
    public static final double log10(double x) {
        return Math.log(x) / LOG10;
    }

    /**
     *  Find the base 2 logarithm of the given double.
     *
     *  @param   x  Value to find the base 2 logarithm of.
     *  @return  The base 2 logarithm of x.
     **/
    public static final double log2(double x) {
        return Math.log(x) / LOG2;
    }

    /**
     *  Rounds a floating point number to the desired decimal place.
     *  Example:  1346.4667 rounded to the 2nd place = 1300.
     *
     *  @param  value  The value to be rounded.
     *  @param  place  Number of decimal places to round value to.
     *                 A place of 1 rounds to 10's place, 2 to 100's
     *                 place, -2 to 1/100th place, et cetera.
     **/
    public static final double roundToPlace(double value, int place) {

        //	If the value is zero, just pass the number back out.
        if (value != 0.) {

            //  If the place is zero, round to the one's place.
            if (place == 0) {
                value = Math.floor(value + 0.5);
            }

            else {
                double pow10 = MathTools.pow10(place); //	= 10 ^ place
                double holdvalue = value / pow10;

                value = Math.floor(holdvalue + 0.5); // Round number to nearest integer
                value *= pow10;
            }
        }

        return value;
    }

    /**
     *  Rounds a floating point number up to the desired decimal place.
     *  Example:  1346.4667 rounded up to the 2nd place = 1400.
     *
     *  @param  value  The value to be rounded up.
     *  @param  place  Number of decimal places to round value to.
     *                 A place of 1 rounds to 10's place, 2 to 100's
     *                 place, -2 to 1/100th place, et cetera.
     **/
    public static final double roundUpToPlace(double value, int place) {

        //	If the value is zero, just pass the number back out.
        if (value != 0.) {

            //  If the place is zero, round to the one's place.
            if (place == 0) {
                value = Math.ceil(value);
            }

            else {
                double pow10 = MathTools.pow10(place); //	= 10 ^ place
                double holdvalue = value / pow10;

                value = Math.ceil(holdvalue); // Round number up to nearest integer
                value *= pow10;
            }
        }

        return value;
    }

    /**
     *  Rounds a floating point number down to the desired decimal place.
     *  Example:  1346.4667 rounded down to the 1st place = 1340.
     *
     *  @param  value  The value to be rounded down.
     *  @param  place  Number of decimal places to round value to.
     *                 A place of 1 rounds to 10's place, 2 to 100's
     *                 place, -2 to 1/100th place, et cetera.
     **/
    public static final double roundDownToPlace(double value, int place) {

        //	If the value is zero, just pass the number back out.
        if (value != 0.) {

            //  If the place is zero, round to the one's place.
            if (place == 0) {
                value = Math.floor(value);
            }

            else {
                double pow10 = MathTools.pow10(place); //	= 10 ^ place
                double holdvalue = value / pow10;

                value = Math.floor(holdvalue); // Round number down to nearest integer
                value *= pow10;
            }
        }

        return value;
    }


    /**
     *  Calculates the greatest common divisor between two input
     *  integers.  The GCD is the largest number that can be
     *  divided into both input numbers.  Uses Euler's method.
     *
     *  @param   xval  First integer
     *  @param   yval  Second integer
     *  @return  The largest number that can be divided into both input
     *           values.
     **/
    public static final long greatestCommonDivisor(long xval, long yval) {
        long value = 0;
        while (value != xval) {
            if (xval < yval) {
                yval = yval - xval;
            }

            else {
                if (xval > yval) {
                    xval = xval - yval;
                } else {
                    value = xval;
                }
            }
        }
        return (value);
    }

    /**
     *  Returns the fractional part of a floating point number
     *  (removes the integer part).
     *
     *  @param   x  Argument for which the fractional part is to be returned.
     *  @return  The fractional part of the argument is returned.
     **/
    public static final double frac(double x) {
        x = x - (long) x;
        if (x < 0.) {
            ++x;
        }

        return x;
    }

    /**
     *  Straight linear 1D interpolation between two points.
     *
     *  @param   x1,y1  Coordinates of the 1st point (the high point).
     *  @param   x2,y2  Coordinates of the 2nd point (the low point).
     *  @param   x      The X coordinate of the point for which we want to
     *                  interpolate to determine a Y coordinate.  Will
     *                  extrapolate if X is outside of the bounds of the
     *                  point arguments.
     *  @return  The interpolated Y value corresponding to the input X
     *           value is returned.
     **/
    public static final double lineInterp(double x1, double y1, double x2,
                                          double y2, double x) {
        return ((y2 - y1) / (x2 - x1) * (x - x1) + y1);
    }

    /**
     *  Converts a positive decimal number to it's binary
     *  equivelant.
     *
     *  @param  decimal  The positive decimal number to be encoded in
     *                   binary.
     *  @param  bits     The bitset to encode the number in.
     **/
    public static final void dec2bin(int decimal, BitSet bits) {
        if (decimal < 0) {
            throw new IllegalArgumentException(
                    "Cannot convert a negative number to binary.");
        }

        int i = 0;
        int value = decimal;
        while (value > 0) {

            if (value % 2 > 0) {
                bits.set(i);
            } else {
                bits.clear(i);
            }

            value /= 2;
            ++i;
        }

        for (i = i; i < bits.size(); ++i) {
            bits.clear(i);
        }
    }

    /**
     *  Converts binary number to it's base 10 decimal equivelant.
     *
     *  @param   bits  The bitset that encodes the number to be converted.
     *  @return  Returns the decimal equivelent of the given binary number.
     **/
    public static final long bin2dec(BitSet bits) {
        long value = 0;
        int length = bits.size();

        for (int i = 0; i < length; ++i) {
            if (bits.get(i)) {
                value += pow2(i);
            }
        }
        return value;
    }

    /**
     *  Return the hyperbolic cosine of the specified argument
     *  in the range MIN_LOG to MAX_LOG.
     *  The hyperbolic cosine is defined as:
     *      cosh(x) = (exp(x) + exp(-x))/2
     *
     *  @param  x  Value to determine hyperbolic cosine of.
     **/
    public static final double cosh(double x) {
        if (Double.isNaN(x)) {
            return Double.NaN;
        }
        if (x < 0) {
            x = -x;
        }
        if (x > (MAX_LOG + LOG2)) {
            return Double.POSITIVE_INFINITY;
        }

        double y = 0;
        if (x >= (MAX_LOG - LOG2)) {
            y = Math.exp(0.5 * x);
            y = (0.5 * y) * y;

        } else {
            y = Math.exp(x);
            y = 0.5 * y + 0.5 / y;
        }

        return y;
    }

    /**
     *  Return the hyperbolic sine of the specified argument
     *  in the range MIN_LOG to MAX_LOG.
     *  The hyperbolic sine is defined as:
     *      sinh(x) = (exp(x) - exp(-x))/2
     *
     *  @param  x  Value to determine hyperbolic sine of.
     **/
    public static final double sinh(double x) {
        if (Double.isNaN(x)) {
            return Double.NaN;
        }
        if (x == 0) {
            return 0;
        }
        if ((x > (MAX_LOG + LOG2)) || (x > -(MIN_LOG - LOG2))) {
            if (x > 0) {
                return Double.POSITIVE_INFINITY;
            } else {
                return Double.NEGATIVE_INFINITY;
            }
        }

        double a = Math.abs(x);
        if (a >= (MAX_LOG - LOG2)) {
            a = Math.exp(0.5 * a);
            a = (0.5 * a) * a;

        } else {
            a = Math.exp(a);
            a = 0.5 * a + 0.5 / a;
        }
        if (x < 0) {
            a = -a;
        }

        return a;
    }

    /**
     *  Returns the hyperbolic tangent of the specified argument
     *  in the range MIN_LOG to MAX_LOG.
     *  The hyperbolic tangent is defined as:
     *      tanh(x) = sinh(x)/cosh(x) = 1 - 2/(exp(2*x) + 1)
     *
     *  @param  x  Value to determine the hyperbolic tangent of.
     **/
    public static final double tanh(double x) {
        if (Double.isNaN(x)) {
            return Double.NaN;
        }
        if (x == 0) {
            return 0;
        }
        double z = Math.abs(x);
        if (z > 0.5 * MAX_LOG) {
            if (x > 0) {
                return 1.0;
            } else {
                return -1.0;
            }
        }

        double s = Math.exp(2 * z);
        z = 1.0 - 2.0 / (s + 1.0);
        if (x < 0) {
            z = -z;
        }

        return z;
    }

    /**
     *  Returns the inverse hyperbolic cosine of the specified argument.
     *  The inverse hyperbolic cosine is defined as:
     *      acosh(x) = log(x + sqrt( (x-1)(x+1) )
     *
     *  @param x  Value to return inverse hyperbolic cosine of.
     *  @throws IllegalArgumentException if x is less than 1.0.
     **/
    public static final double acosh(double x) {
        if (Double.isNaN(x)) {
            return Double.NaN;
        }
        if (Double.isInfinite(x)) {
            return x;
        }
        if (x < 1.0) {
            throw new IllegalArgumentException("x less than 1.0");
        }

        double y = 0;
        if (x > 1.0E8) {
            y = Math.log(x) + LOG2;

        } else {
            double a = Math.sqrt((x - 1.0) * (x + 1.0));
            y = Math.log(x + a);
        }

        return y;
    }

    /**
     *  Returns the inverse hyperbolic sine of the specified argument.
     *  The inverse hyperbolic sine is defined as:
     *      asinh(x) = log( x + sqrt(1 + x*x) )
     *
     *  @param xx  Value to return inverse hyperbolic cosine of.
     **/
    public static final double asinh(double xx) {
        if (Double.isNaN(xx)) {
            return Double.NaN;
        }
        if (Double.isInfinite(xx)) {
            return xx;
        }
        if (xx == 0) {
            return 0;
        }

        int sign = 1;
        double x = xx;
        if (xx < 0) {
            sign = -1;
            x = -xx;
        }

        double y = 0;
        if (x > 1.0E8) {
            y = sign * (Math.log(x) + LOG2);

        } else {
            double a = Math.sqrt(x * x + 1.0);
            y = sign * Math.log(x + a);
        }

        return y;
    }

    /**
     *  Returns the inverse hyperbolic tangent of the specified argument.
     *  The inverse hyperbolic tangent is defined as:
     *      atanh(x) = 0.5 * log( (1 + x)/(1 - x) )
     *
     *  @param x  Value to return inverse hyperbolic cosine of.
     *  @throws IllegalArgumentException if x is outside the range -1, to +1.
     **/
    public static final double atanh(double x) {
        if (Double.isNaN(x)) {
            return Double.NaN;
        }
        if (x == 0) {
            return 0;
        }

        double z = Math.abs(x);
        if (z >= 1.0) {
            if (x == 1.0) {
                return Double.POSITIVE_INFINITY;
            }
            if (x == -1.0) {
                return Double.NEGATIVE_INFINITY;
            }

            throw new IllegalArgumentException("x outside of range -1 to +1");
        }

        if (z < 1.0E-7) {
            return x;
        }

        double y = 0.5 * Math.log((1.0 + x) / (1.0 - x));

        return y;
    }


    /**
     *  Returns the absolute value of "a" times the sign of "b".
     **/
    public static final double sign(double a, double b) {
        return Math.abs(a) * (b < 0 ? -1 : 1);
    }


    /**
     *  Returns the absolute value of "a" times the sign of "b".
     **/
    public static final float sign(float a, double b) {
        return Math.abs(a) * (b < 0 ? -1 : 1);
    }


    /**
     *  Returns the absolute value of "a" times the sign of "b".
     **/
    public static final long sign(long a, double b) {
        return Math.abs(a) * (b < 0 ? -1 : 1);
    }


    /**
     *  Returns the absolute value of "a" times the sign of "b".
     **/
    public static final int sign(int a, double b) {
        return Math.abs(a) * (b < 0 ? -1 : 1);
    }


    //  Used by "lnGamma()".
    private static double[] gamCoef = {76.18009172947146, -86.50532032941677,
                                      24.01409824083091,
                                      -1.231739572450155, 0.1208650973866179e-2,
                                      -0.5395239384953e-5};
    /**
     *  Returns the natural log "ln" of the Gamma Function defined by the integral:
     *      Gamma(z) = integral from 0 to infinity of t^(z-1)*e^-t dt.
     *  It is better to implement ln(Gamma(x)) rather than Gamma(x) since the latter
     *  will overflow many computer's floating point representations at quite modest
     *  values of x.
     **/
    public double lnGamma(double xx) {
        double x = xx, y = xx;
        double tmp = x + 5.5;
        tmp -= (x + 0.5) * Math.log(tmp);
        double ser = 1.000000000190015;
        for (int j = 0; j <= 5; ++j) {
            ser += gamCoef[j] / ++y;
        }
        return -tmp + Math.log(2.5066282746310005 * ser / x);
    }


    /**
     *  Used to test out the methods in this class.
     **/
    public static void main(String args[]) {

        System.out.println();
        System.out.println("Testing MathTools...");

        System.out.println("  2 is an " + (even(2) ? "even" : "odd") +
                           " number.");
        System.out.println("  3 is an " + (odd(3) ? "odd" : "even") +
                           " number.");
        System.out.println("  The square of 3.8 is " + sqr(3.8) + ".");
        System.out.println("  The cube root of 125 is " + cubeRoot(125) + ".");
        System.out.println("  The integer 3^7 is " + pow(3, 7) + ".");
        System.out.println("  The integer 2^8 is " + pow2(8) + ".");
        System.out.println("  The double 10^-3 is " + pow10( -3) + ".");
        System.out.println("  The base 10 logarithm of 8 is " + log10(8) + ".");
        System.out.println("  The base 2 logarithm of 8 is " + log2(8) + ".");
        System.out.println("  1346.4667 rounded to the nearest 100 is " +
                           roundToPlace(1346.4667, 2) + ".");
        System.out.println("  1346.4667 rounded up to the nearest 100 is " +
                           roundUpToPlace(1346.4667, 2) + ".");
        System.out.println("  1346.4667 rounded down to the nearest 10 is " +
                           roundDownToPlace(1346.4667, 1) + ".");
        System.out.println("  The GCD of 125 and 45 is " +
                           greatestCommonDivisor(125, 45) + ".");
        System.out.println("  The fractional part of 3.141593 is " +
                           frac(3.141593) + ".");
        double x = 5;
        System.out.println("  The hyperbolic sine of " + (float) x + " = " +
                           (float) sinh(x) + ".");
        System.out.println("  The hyperbolic cosine of " + (float) x + " = " +
                           (float) cosh(x) + ".");
        x = -.25;
        System.out.println("  The hyperbolic tangent of " + (float) x + " = " +
                           (float) tanh(x) + ".");
        x = cosh(5);
        System.out.println("  The inv. hyperbolic cosine of " + (float) x +
                           " = " + (float) acosh(x) + ".");
        System.out.println("  The inv. hyperbolic sine of " + (float) x + " = " +
                           (float) asinh(x) + ".");
        x = tanh( -0.25);
        System.out.println("  The inv. hyperbolic tangent of " + (float) x +
                           " = " + (float) atanh(x) + ".");

        System.out.println("  4.56 with the sign of -6.33 is " +
                           sign(4.56F, -6.33));

    }

}



