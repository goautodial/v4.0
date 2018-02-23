#!/usr/bin/perl
############################################################################################
####  Name:             google_sheets.pl                                                ####
####  Type:             perl script                                                     ####
####  Version:          3.0                                                             ####
####  Build:            1366106153                                                      ####
####  Copyright:        GOAutoDial Inc. (c) 2011-2013 - <dev@goautodial.com>            ####
####  Written by:       Christopher P. Lomuntad                                         ####
####  License:          AGPLv2                                                          ####
############################################################################################

require LWP::UserAgent;
use JSON qw( decode_json );     # From CPAN
use Data::Dumper;               # Perl core module
use POSIX qw(strftime);

my $ua = LWP::UserAgent->new;

$secX=time();
# default path to astguiclient configuration file:
if ($CLIdir eq "" || ! -e "$CLIdir/astguiclient.conf")
	{$CLIdir = "/etc";}
$PATHconf =		"$CLIdir/astguiclient.conf";

open(conf, "$PATHconf") || die "can't open $PATHconf: $!\n";
@conf = <conf>;
close(conf);
$i=0;
foreach(@conf) {
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
	if ( ($line =~ /^VARDBgo_server/) && ($CLIDB_server < 1) )
		{$VARDBgo_server = $line;   $VARDBgo_server =~ s/.*=//gi;}
	if ( ($line =~ /^VARDBgo_database/) && ($CLIDBgo_database < 1) )
		{$VARDBgo_database = $line;   $VARDBgo_database =~ s/.*=//gi;}
	if ( ($line =~ /^VARDBgo_user/) && ($CLIDBgo_user < 1) )
		{$VARDBgo_user = $line;   $VARDBgo_user =~ s/.*=//gi;}
	if ( ($line =~ /^VARDBgo_pass/) && ($CLIDBgo_pass < 1) )
		{$VARDBgo_pass = $line;   $VARDBgo_pass =~ s/.*=//gi;}
	if ( ($line =~ /^VARDBgo_port/) && ($CLIDBgo_port < 1) )
		{$VARDBgo_port = $line;   $VARDBgo_port =~ s/.*=//gi;}
	$i++;
}

# Customized Variables
$Q = 0;                         # Quiet mode
$check_dup = 1;                 # Check for duplicates
$currRow = 1;                   # Current row
$server_ip = $VARserver_ip;		# Asterisk server IP

use DBI;
# DB asterisk connection
$dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
    or die "Couldn't connect to database: " . DBI->errstr;
# DB goautodial connection
$dbhG = DBI->connect("DBI:mysql:$VARDBgo_database:$VARDBgo_server:$VARDBgo_port", "$VARDBgo_user", "$VARDBgo_pass")
    or die "Couldn't connect to database: " . DBI->errstr;

if (!$Q) {print "\n -- google_sheets.pl --\n\n";}
if (!$Q) {print " This program is designed to get available leads from google sheets specified on the\n";}
if (!$Q) {print " campaign settings' google_sheet_ids and then put all leads gathered to the list_id\n";}
if (!$Q) {print " specified under vicidial_campaigns.\n\n";}


# Getting the Google API Key from the database
$stmtA = "SELECT value FROM settings WHERE setting = 'google_api_key';";
$sthA = $dbhG->prepare($stmtA) or die "preparing: ",$dbhG->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhG->errstr;
$sthArows=$sthA->rows;
if ($sthArows > 0) {
    @aryA = $sthA->fetchrow_array;
    $googleAPIKey = $aryA[0];
}
$sthA->finish();

if (!$googleAPIKey) {
    print "Google API Key NOT set.\n";
    die;
}


if (!$Q) {print "\nProcessing go_campaigns table... Gathering Google Sheet IDs...\n";}

# Gather google sheet ids
$stmtG = "SELECT campaign_id,google_sheet_ids,google_sheet_list_id FROM go_campaigns WHERE google_sheet_ids <> '';";
$sthG = $dbhG->prepare($stmtG) or die "preparing: ",$dbhG->errstr;
$sthG->execute or die "executing: $stmtG ", $dbhG->errstr;
$sthGrows = $sthG->rows;
$insertedRows = 0;
if ($sthGrows > 0) {
    while (my @row = $sthG->fetchrow_array) {
        if (!$Q) {print "campaign_id: $row[0]  google_sheet_ids: $row[1]  google_sheet_list_id: $row[2]\n";}
        my $sheet_list_id = $row[2];
        my @sheet_ids = split / /, $row[1];
        #print $_, "\n" for @sheet_ids;
        foreach (@sheet_ids) {
            #print "$_\n";
            my $response = $ua->get("https://sheets.googleapis.com/v4/spreadsheets/$_/values/Sheet1!A$currRow:Z1000?key=$googleAPIKey");
            
            if ($response->is_success) {
                if (!$Q) {print "https://sheets.googleapis.com/v4/spreadsheets/$_/values/Sheet1!A$currRow:Z1000?key=$googleAPIKey\n";}
                my $decoded_json = decode_json( $response->content );
                #print Dumper $decoded_json;
                $rowCnt = 1;
                foreach my $leadRow (@{ $decoded_json->{'values'} }) {
                    $phone_number = $leadRow->[0];
                    $isDup = "N";
                    if ( $phone_number =~ /^[0-9]+$/ ) {
                        #print "$phone_number is a number\n";
                        $phone_code = '1';
                        $title = '';
                        $first_name = $leadRow->[1];
                        $middle_initial = '';
                        $last_name = $leadRow->[2];
                        $address = $leadRow->[3];
                        $address2 = '';
                        $address3 = '';
                        $city = $leadRow->[4];
                        $state = $leadRow->[5];
                        $province = '';
                        $zip = $leadRow->[6];
                        $country_code = '';
                        $comments = $leadRow->[7];
                        $vendor_lead_code = '';
                        $gmt_offset_now = '';
                        $date_of_birth = '';
                        $alt_phone = '';
                        $email = '';
                        $security_phrase = '';
                        
                        if ($check_dup) {
                            $stmtA = "SELECT lead_id FROM vicidial_list WHERE phone_number = ?";
                            $sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
                            $sthA->execute($phone_number) or die "executing: $stmtA ", $dbhA->errstr;
                            if ($sthA->rows > 0 ) {
                                $isDup = "Y";
                            }
                            $sthA->finish();
                        }
                        
                        if (!$Q) {print "Is Duplicate?: $isDup\n";}
                        if ($isDup eq "N") {
                            my $NOWdate = strftime "%F %H:%M:%S", localtime;
                            $status = 'NEW';
                            $stmtB = "INSERT INTO vicidial_list (entry_date,status,list_id,phone_code,phone_number,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,comments,vendor_lead_code,gmt_offset_now,title,date_of_birth,alt_phone,email,security_phrase) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);";
                            if (!$Q) {print "$stmtB\n";}
                            $sthB = $dbhA->prepare($stmtB) or die "preparing: ",$dbhA->errstr;
                            $sthB->execute($NOWdate,$status,$sheet_list_id,$phone_code,$phone_number,$first_name,$middle_initial,$last_name,$address,$address2,$address3,$city,$state,$province,$zip,$country_code,$comments,$vendor_lead_code,$gmt_offset_now,$title,$date_of_birth,$alt_phone,$email,$security_phrase) or die "executing: $stmtB ", $dbhA->errstr;
                            $sthB->finish();
                            $insertedRows++;
                        }
                    } else {
                        if (!$Q) {print "$phone_number is NOT a number\n";}
                    }
                    $rowCnt++;
                }
            }
        }
    }
    if (!$Q) {print "\n$insertedRows rows inserted into vicidial_list table\n";}
}
$sthG->finish();
