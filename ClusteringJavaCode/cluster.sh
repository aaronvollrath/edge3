#!/bin/sh
echo "Compiling Cluster.java..."
#/home/vollrath/Borland/JBuilder2005/jdk1.4/bin/
javac -classpath ./Jama-1.0.1.jar Cluster.java
echo "Creating jar file, EdgeClustering.jar"
jar cmf MANIFEST.MF EdgeClustering.jar Cluster.class ClusterObject.class Instance.class QSortAlgorithm.class Node.class Statistics.class MathTools.class Jama-1.0.1.jar
echo "Executing...."
java -mx512m -jar EdgeClustering.jar ./data22842.txt 4 8 ./IMAGES/data22842.svg 0 ./IMAGES/tabledata22842 0 2 1 4633 ../dataoutputfiles >> garbagedump.txt

#java -mx512m -jar EdgeClustering.jar /var/www/edge2/IMAGES/data14797.txt 4 10 /var/www/edge2/IMAGES/svg14797.svg 1 /var/www/edge2/IMAGES/table14797 0 2 1 14797 >> garbagedump.txt
cp EdgeClustering.jar /var/www/edge2/EdgeClustering.jar
#cp Cluster.jar /var/www/html/Clustergraphing.jar
#cp output.svg /var/www/html/output.svg
echo "Finished..."
