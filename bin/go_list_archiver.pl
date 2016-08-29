#!/usr/bin/perl
############################################################################################
####  Name:             go_list_archiver.pl                                             ####
####  Type:             perl script                                                     ####
####  Version:          3.0                                                             ####
####  Build:            1366106153                                                      ####
####  Copyright:        GOAutoDial Inc. (c) 2011-2013 - <dev@goautodial.com>            ####
####  Written by:       Christopher P. Lomuntad                                         ####
####  License:          AGPLv2                                                          ####
############################################################################################

### begin parsing run-time options ###
if (length($ARGV[0])>1)
	{
	$i=0;
	while ($#ARGV >= $i)
		{
		$args = "$args $ARGV[$i]";
		$i++;
		}

	if ($args =~ /--help/i)
		{
		print "Allowed run time options:\n";
		print "  [--dir=XX] = Include the directory where astguiclient.conf is located. default is /etc\n";
		print "  [--listid=XX] = List ID to archive. you can specify more than 1 list id by separating them with comma\n";
		print "  [--action=XX] = what action to do with the List ID provided (activate|deactivate). default is activate\n";
		print "  [--quiet] = quiet mode\n";
		print "  [-t] = test mode\n\n";
		exit;
		}
	else
		{
		if ($args =~ /-quiet/i)
			{
			$q=1;   $Q=1;
			}
		if ($args =~ /-t/i)
			{
			$T=1;   $TEST=1;
			print "\n----- TESTING -----\n\n";
			}
		if ($args =~ /--dir=/i)
			{
			@data_in = split(/--dir=/,$args);
			$CLIdir = $data_in[1];
			$CLIdir =~ s/ .*$//gi;

			if ($Q < 1)
				{print "\n----- Web Root Folder: $CLIdir -----\n";}
			}
		if ($args =~ /--listid=/i)
			{
			@data_in = split(/--listid=/,$args);
			$CLIlistID = $data_in[1];
			$CLIlistID =~ s/ .*$//gi;
                        @CLIlistIDs = split(/,/,$CLIlistID);
                        $CLIlistIDs = join("','",@CLIlistIDs);
                        
			if ($Q < 1) 
				{print "\n----- LIST ID(s): $CLIlistID -----\n";}
			}
		if ($args =~ /--action=/i)
			{
			@data_in = split(/--action=/,$args);
			$CLIaction = $data_in[1];
			$CLIaction =~ s/ .*$//gi;
                        
			if ($Q < 1) 
				{print "\n----- ACTION: ",uc($CLIaction)," -----\n\n";}
			}
		}
	}
else
	{
	print "No command line options set\n";
	}
### end parsing run-time options ###

$secX=time();
# default path to astguiclient configuration file:
if ($CLIdir eq "" || ! -e "$CLIdir/astguiclient.conf")
	{$CLIdir = "/etc";}
$PATHconf =		"$CLIdir/astguiclient.conf";

open(conf, "$PATHconf") || die "can't open $PATHconf: $!\n";
@conf = <conf>;
close(conf);
$i=0;
foreach(@conf)
	{
	$line = $conf[$i];
	$line =~ s/ |>|\n|\r|\t|\#.*|;.*//gi;
	if ( ($line =~ /^PATHhome/) && ($CLIhome < 1) )
		{$PATHhome = $line;   $PATHhome =~ s/.*=//gi;}
	if ( ($line =~ /^PATHlogs/) && ($CLIlogs < 1) )
		{$PATHlogs = $line;   $PATHlogs =~ s/.*=//gi;}
	if ( ($line =~ /^PATHagi/) && ($CLIagi < 1) )
		{$PATHagi = $line;   $PATHagi =~ s/.*=//gi;}
	if ( ($line =~ /^PATHweb/) && ($CLIweb < 1) )
		{$PATHweb = $line;   $PATHweb =~ s/.*=//gi;}
	if ( ($line =~ /^PATHsounds/) && ($CLIsounds < 1) )
		{$PATHsounds = $line;   $PATHsounds =~ s/.*=//gi;}
	if ( ($line =~ /^PATHmonitor/) && ($CLImonitor < 1) )
		{$PATHmonitor = $line;   $PATHmonitor =~ s/.*=//gi;}
	if ( ($line =~ /^VARserver_ip/) && ($CLIserver_ip < 1) )
		{$VARserver_ip = $line;   $VARserver_ip =~ s/.*=//gi;}
	if ( ($line =~ /^VARDB_server/) && ($CLIDB_server < 1) )
		{$VARDB_server = $line;   $VARDB_server =~ s/.*=//gi;}
	if ( ($line =~ /^VARDB_database/) && ($CLIDB_database < 1) )
		{$VARDB_database = $line;   $VARDB_database =~ s/.*=//gi;}
	if ( ($line =~ /^VARDB_user/) && ($CLIDB_user < 1) )
		{$VARDB_user = $line;   $VARDB_user =~ s/.*=//gi;}
	if ( ($line =~ /^VARDB_pass/) && ($CLIDB_pass < 1) )
		{$VARDB_pass = $line;   $VARDB_pass =~ s/.*=//gi;}
	if ( ($line =~ /^VARDB_port/) && ($CLIDB_port < 1) )
		{$VARDB_port = $line;   $VARDB_port =~ s/.*=//gi;}
	$i++;
	}

# Customized Variables
$server_ip = $VARserver_ip;		# Asterisk server IP

use DBI;
$dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
 or die "Couldn't connect to database: " . DBI->errstr;


if (!$Q) {print "\n\n-- go_list_archiver.pl --\n\n";}
if (!$Q) {print " This program is designed to put all records of the specified list ids ( $CLIlistID )\n";}
if (!$Q) {print " from vicidial_list in relevant _archive tables and delete records in original tables\n";}
if (!$Q) {print " when set to inactive and will be put back when the given list id is set to active.\n\n";}

if (!$T) 
	{
	##### activate
        if ($CLIaction =~ /^activate/)
                {
                $stmtA = "SELECT count(*) from vicidial_list_archive WHERE list_id IN ('$CLIlistIDs');";
                $sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
                $sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
                $sthArows=$sthA->rows;
                if ($sthArows > 0)
                        {
                        @aryA = $sthA->fetchrow_array;
                        $vicidial_list_count =	$aryA[0];
                        }
                $sthA->finish();
                
                if (!$Q) {print "\nProcessing vicidial_list_archive table...  ($vicidial_list_count)\n";}
                $stmtA = "INSERT IGNORE INTO vicidial_list SELECT * from vicidial_list_archive WHERE list_id IN ('$CLIlistIDs');";
                $sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
                $sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
                $sthArows = $sthA->rows;
                if (!$Q) {print "$sthArows rows inserted into vicidial_list table\n";}
                
                $rv = $sthA->err();
                if (!$rv)
                        {
                        $stmtA = "DELETE FROM vicidial_list_archive WHERE list_id IN ('$CLIlistIDs');";
                        $sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
                        $sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
                        $sthArows = $sthA->rows;
                        if (!$Q) {print "$sthArows rows deleted from vicidial_list_archive table \n";}
                
                        #$stmtA = "optimize table vicidial_list;";
                        #$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
                        #$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
                        
                        #$stmtA = "optimize table vicidial_list_archive;";
                        #$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
                        #$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
                        
                        print "List ID(s) $CLIlistID successfully activated.\n";
                        }
                }
        
	##### deactivate
        if ($CLIaction =~ /^deactivate/)
                {
                $stmtA = "SELECT count(*) from vicidial_list WHERE list_id IN ('$CLIlistIDs');";
                $sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
                $sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
                $sthArows=$sthA->rows;
                if ($sthArows > 0)
                        {
                        @aryA = $sthA->fetchrow_array;
                        $vicidial_list_count =	$aryA[0];
                        }
                $sthA->finish();
                
                if (!$Q) {print "\nProcessing vicidial_list table...  ($vicidial_list_count)\n";}
                $stmtA = "INSERT IGNORE INTO vicidial_list_archive SELECT * from vicidial_list WHERE list_id IN ('$CLIlistIDs');";
                $sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
                $sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
                $sthArows = $sthA->rows;
                if (!$Q) {print "$sthArows rows inserted into vicidial_list_archive table\n";}
                
                $rv = $sthA->err();
                if (!$rv)
                        {
                        $stmtA = "DELETE FROM vicidial_list WHERE list_id IN ('$CLIlistIDs');";
                        $sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
                        $sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
                        $sthArows = $sthA->rows;
                        if (!$Q) {print "$sthArows rows deleted from vicidial_list table \n";}
                
                        #$stmtA = "optimize table vicidial_list;";
                        #$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
                        #$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
                        
                        #$stmtA = "optimize table vicidial_list_archive;";
                        #$sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
                        #$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
                        
                        print "List ID(s) $CLIlistID successfully deactivated.\n";
                        }
                }
        }


#$dbhA->disconnect();
#print "$del_time\n\n";


### calculate time to run script ###
$secY = time();
$secZ = ($secY - $secX);
$secZm = ($secZ /60);
if (!$Q) {print "\nscript execution time in seconds: $secZ     minutes: $secZm\n";}

exit;
