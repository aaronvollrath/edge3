public void plotCluster(int k, int orderedNumber) {

        // The parameter, k, is the number of k-means clusters.

        try {

            String dataset = Cluster.filenum;
            	String phpFile = "./IMAGES/"+dataset +
                "cluster" + orderedNumber +
                ".php";
		String mxmlFile = "./IMAGES/" + dataset + "cluster" + orderedNumber + ".mxml";
		////System.out.println("phpFile is " + phpFile);
            BufferedWriter BWphp = new BufferedWriter(new FileWriter(phpFile));
	    BufferedWriter BWmxml = new BufferedWriter(new FileWriter(mxmlFile));
            BWmxml.write("<?xml version=\"1.0\"?>
<mx:Application xmlns:mx=\"http://www.adobe.com/2006/mxml\"
	layout=\"horizontal\"
	backgroundGradientColors=\"[#ffffff,#ffffff]\"
initialize=\"initData();\"
>
    <mx:Glow id=\"glowImage\" duration=\"1000\"
        alphaFrom=\"1.0\" alphaTo=\"0.3\"
        blurXFrom=\"0.0\" blurXTo=\"50.0\"
        blurYFrom=\"0.0\" blurYTo=\"50.0\"
        color=\"0x00FF00\"/>
    <mx:Glow id=\"unglowImage\" duration=\"1000\"
        alphaFrom="0.3" alphaTo="1.0"
        blurXFrom=\"50.0\" blurXTo=\"0.0\"
        blurYFrom=\"50.0\" blurYTo=\"0.0\"
        color=\"0x0000FF\"/>
    <mx:Script>
    <![CDATA[
	public function handleClick(aParam:String):void {

		geneLabel.htmlText = \"<b>Gene Selected</b>:\" + aParam;
		seriesdgProvider(aParam);

	}

    import mx.collections.ArrayCollection;

import mx.managers.CursorManager;
            import mx.rpc.events.InvokeEvent;
            import mx.controls.Alert;
            import mx.rpc.events.FaultEvent;
            import mx.rpc.events.ResultEvent;


    [Bindable]
	public var seriesDataGridProvider:ArrayCollection;
	public var trxIndex:Number;

	// Data initialization
	public function initData():void{
		// Create data provider for DataGrid control
		seriesDataGridProvider = new ArrayCollection;
	}

	// Fill seriesDataGridProvider with the specified items
	public function seriesdgProvider(gene:String):void{
		trxIndex=1;
		seriesDataGridProvider.removeAll();
		for(var z:int = 0; z<treatmentsAC.length; z++){
		var obj:Object = {};
		obj.Treatment = treatmentsAC.getItemAt(z).Array;
		obj.FoldChange = treatmentsAC.getItemAt(z)[gene];
		seriesDataGridProvider.addItem(obj);

		}
	}\");");

        BWmxml.write("\n private var treatmentsAC:ArrayCollection = new ArrayCollection( [");

            // How many genes are we dealing with?
            int genes = this.currentSize;
            ////System.out.println("The size of this cluster is: " + genes);
            double[][] expressionvals = new double[genes][Cluster.trxNames.size()];

	    //  { Array: "Chemical 1", Cyp1a1: 1.59, Saa2: -4.59, Cy1a2: 9.67 },
	for (int i = 1; i < Cluster.trxNames.size(); i++) {
                BWmxml.write("{Array: ,\"" + (String) Cluster.trxNames.elementAt(i) + "\", ");

            for (int i = 0; i < genes; i++) {
                Instance thisInstance = (Instance)this.clusterMembers.elementAt(
                        i);

		BWmxml.write(thisInstance.commonNameAndDescription +": ");
                Vector instvals = (Vector) thisInstance.instanceVals;
                String dval = (String) instvals.elementAt(0);

                // If we've a -9999 value, set it to 0....
                ///System.out.print(dval + " " );
                Double val = new Double(dval);
                // double val = dvalue.doubleValue();
                ////System.out.println("The number of arrays: " + instvals.size());
                double thisval = val.doubleValue();
                if (thisval < 0.0) {
                    ////System.out.println("lt0!");
                    thisval = 1.0 / Math.abs(thisval);
                }
                thisval = MathTools.log2(thisval);
                expressionvals[i][0] = thisval;
                BWmxml.write(thisval + ", ");
            } // end of     for (int i = 0; i < genes; i++) {
	    BWmxml.write("},\n");
	}  // end of for (int i = 1; i < Cluster.trxNames.size(); i++)
            BWmxml.flush();
            BWmxml.close();

        } catch (FileNotFoundException fnfe) {
            System.out.println("EXECUTION HALTED at line 1862: Table File named does not exist!");
            System.exit(0);
        } catch (IOException ioe) {
            System.out.println("ERROR: " + ioe);
        }

    }