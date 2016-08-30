#!/usr/bin/perl
#
# bp.pl    version 2.8
# 
# Bcrypt password hashing script to be used for authentication
#
# IMPORTANT !!!!!!!!!!!!!
# The Crypt::Eksblowfish::Bcrypt perl module is REQUIRED for this script
#
# Copyright (C) 2013  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
#
# CHANGES
# 
# 130630-1044 - First build
#
# Added document root via use Apache2::RequestUtil ();

$DB=0;
$DBX=0;
$bypassDB=0;

use DBI;
use Crypt::Eksblowfish::Bcrypt qw(en_base64);  

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
		print "allowed run time options:\n";
		print "  [--pass=XXX] = password input\n";
		print "  [--salt=XXX] = overide the system salt\n";
		print "  [--cost=XX] = overide the system cost\n";
		print "  [--debug] = enable debugging output\n";
		print "  [--debugX] = enable extra debugging output\n";
		print "  [--help] = this help screen\n";
		print "\n";

		exit;
		}
	else
		{
		if ($args =~ /--debug/i)
			{$DB=1;}
		if ($args =~ /--debugX/i)
			{$DBX=1;}
		if ($args =~ /--pass=/i)
			{
			@data_in = split(/--pass=/,$args);
			$pass = $data_in[1];
			$pass =~ s/ .*//gi;
			if ($DB > 0) 
				{print "\n----- PASS: $pass -----\n\n";}
			}
		if ($args =~ /--salt=/i)
			{
			@data_in = split(/--salt=/,$args);
			$CLIsalt = $data_in[1];
			$CLIsalt =~ s/ .*//gi;
			if (length($CLIsalt) eq 16)
				{
				$newCLIsalt = en_base64($CLIsalt);
				if ($DB > 0) 
					{print "\n----- ENCRYPTING SALT OVERRIDE: $CLIsalt -----\n";}
				$CLIsalt = $newCLIsalt;
				}
			if (length($CLIsalt) ne 22) 
				{
				if ($DB > 0) 
					{print "\n----- INVALID SALT OVERRIDE, USING DEFAULT: $CLIsalt -----\n\n";}
				$CLIsalt = '';
				}
			else
				{
				if ($DB > 0) 
					{print "\n----- SALT OVERRIDE: $CLIsalt -----\n\n";}
				}
            $bypassDB = 1;
			}
		if ($args =~ /--cost=/i)
			{
			@data_in = split(/--cost=/,$args);
			$CLIcost = $data_in[1];
			$CLIcost =~ s/ .*//gi;
			if ($DB > 0) 
				{print "\n----- COST OVERRIDE: $CLIcost -----\n\n";}
            $bypassDB = 1;
			}
		}
	}
else
	{
	print "NO INPUT, NOTHING TO DO, EXITING...\n";
	exit;
	}
if (length($pass) < 1)
	{
	print "NO PASSWORD INPUT, NOTHING TO DO, EXITING...\n";
	exit;
	}
### end parsing run-time options ###


# default path to astguiclient configuration file:
use Cwd 'abs_path';
my $HTMLroot = abs_path($0);
$HTMLroot =~ s/\/bin\/bp.pl//g;
$PATHconf = "$HTMLroot/astguiclient.conf";

if ( $bypassDB < 1) {
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
    
    if (!$VARDB_port) {$VARDB_port='3306';}
    
    $dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
     or die "Couldn't connect to database: " . DBI->errstr;
    
    ##### Get the settings from system_settings #####
    $stmtA = "SELECT pass_hash_enabled,pass_key,pass_cost FROM system_settings;";
    #	print "$stmtA\n";
    $sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
    $sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
    $sthArows=$sthA->rows;
    if ($sthArows > 0)
        {
        @aryA = $sthA->fetchrow_array;
        $pass_hash_enabled =	$aryA[0];
        $pass_key =				$aryA[1];
        $pass_cost =			$aryA[2];
        if (length($pass_key) eq 16)
            {$newpass_key = en_base64($pass_key);}
        }
    $sthA->finish();
    if ($DBX > 0) {print "SYSTEM SETTINGS:     |$pass_hash_enabled|$pass_key|$newpass_key|$pass_cost|\n";}
}

if (length($CLIsalt) > 0)
	{
	if ($DBX > 0) {print "SALT OVERRIDDEN:     |$pass_key|$newpass_key|$CLIsalt|\n";}
	$salt = $CLIsalt;
	}
else
	{$salt = $newpass_key;}

if (length($CLIcost) > 0)
	{
	if ($DBX > 0) {print "COST OVERRIDDEN:     |$pass_cost|$CLIcost|\n";}
	$cost = $CLIcost;
	}
else
	{$cost = $pass_cost;}
while (length($cost) < 2) 
	{$cost = "0$cost";}


use Time::HiRes ('gettimeofday','usleep','sleep');  # necessary to have perl timing of less than one second
($START_s_hires, $START_usec) = gettimeofday();

 
# Set the cost to $cost and append a NUL
$settings = '$2a$'.$cost.'$'.$salt;
 
# Encrypt it
$pass_hash = Crypt::Eksblowfish::Bcrypt::bcrypt($pass, $settings);

$pass_hash_length = length($pass_hash);

$only_pass_hash = substr($pass_hash,29,31);

if ($DB > 0) {print "PASS HASH:     |$pass_hash_length|$pass_hash|$only_pass_hash|\n";}

($END_s_hires, $END_usec) = gettimeofday();
$START_time = $START_s_hires . '.' . sprintf("%06s", $START_usec);
$END_time = $END_s_hires . '.' . sprintf("%06s", $END_usec);
$RUN_time = ($END_time - $START_time);
$RUN_time = sprintf("%.6f", $RUN_time);
if ($DBX > 0) 
	{print "bcrypt time: |$RUN_time ($END_time - $START_time)|\n";}

print "PHASH: $only_pass_hash\n";

exit;
