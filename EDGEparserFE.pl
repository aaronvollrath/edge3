print "Welcome to Kevin's Wonderful World of Perl!\n\n";
print "This file is to use data from Agilent Feature Extraction.\n";
print "This is the single file version of the chip parser.\n";
print "All of the data should be contained in a single genepix .gpr file\n";
print "What is the base name of the experiment to be done?\n";
print "";

print "\n		Basename.txt\n";


$arraynumber = $ARGV[0];
$dyeswap = $ARGV[1];


print "\n the array number is: $arraynumber\n";
print "\n the dyeswap is: $dyeswap\n";
#print "\nNow please type the Basename here:  . ";



$filename = "featureextraction"; #<STDIN>;

chomp $filename;




open (FILEINL, "< $filename".".txt") || die "cannot open $filename";

#open(FILEOUT,"> $filename"."_out.txt") || die "cannot create $filename_out.txt";
open(FILEOUT1,"> $filename"."_edgedata.txt") || die "cannot create $filename_out.txt";



$count = 0;

while (<FILEINL>) {

chomp;
if($count > 9){
	@split = split (/	/, $_);
	
	$id = $split[6];

	#print "$id\n";
      $split[0] =  $arraynumber;
	foreach $argnum (0 .. $#split) {


  	 print FILEOUT1 "$split[$argnum]\t";

	}
	print FILEOUT1 "\n";
	

	$data[0] = $split[75];
	$data[1] = $split[76];
	$data[2] = $split[10];	

	$data[3] = $split[11];
	$data[4] = $split[12];

	$sorter[$count] = $id;
	$datacall = "$id"."_"."$count";
	#print "$datacall\n";
	$sorter[$count] = $datacall;
	$dataholder1{$datacall}= $data[0];
	$dataholder2{$datacall}= $data[1];
	$dataholder3{$datacall}= $data[2];


	$dataholder4{$datacall}= $data[3];
	$dataholder5{$datacall}= $data[4];

	$firstin = $data[0];
	$secondin = $data[1];
	$thirdin = $data[2];
	$fourthin = $data[3];
	$fifthin = $data[4];

	$dataout{$id} = "$dataout{$id}\t$firstin\t$secondin\t$thirdin\t$fourthin\t$fifthin\t";
	}

$count = $count + 1;
		}
	

close FILEOUT1;

open(FILEOUTTWO,"> $filename"."_Final_out.txt") || die "cannot create $filename_out.txt";

$secondcount = 1;


foreach $keys (keys %dataout) {

	#print "$dataout{$keys}\n";
	print FILEOUTTWO "$keys\t";
	print FILEOUTTWO "$dataout{$keys}\n";


}



