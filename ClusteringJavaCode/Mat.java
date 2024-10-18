import Jama.*;
public class Mat{

	public Mat(){
		System.out.println("new mat created...");
	}
   public static void main(String args[]){
	 Mat x = new Mat();
        double[][] vals = {{1.,2.,3},{4.,5.,6.},{7.,8.,10.}};
         Matrix a = new Matrix(vals);
         Matrix b = Matrix.random(3,1);
         Matrix e = a.solve(b);
   }
 }