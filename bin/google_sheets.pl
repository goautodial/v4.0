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
use Time::Local;
use POSIX qw(strftime);
use POSIX qw(tzset);
use File::Basename;

my $ua = LWP::UserAgent->new;
my $CLIdir = dirname(__FILE__);
$CLIdir =~ s/\/bin//;

### begin parsing run-time options ###
$Q = 1;
$QQ = 1;
$check_dup = 1;
if (length($ARGV[0])>1) {
	$i=0;
	while ($#ARGV >= $i)
		{
		$args = "$args $ARGV[$i]";
		$i++;
		}

	if ($args =~ /--help/i)
		{
		print "allowed run time options:\n";
		print "  [--nodupcheck] = disable duplicate checks.\n";
		print "  [--debug] = enable debugging output\n";
		print "  [--help] = this help screen\n";
		print "\n";

		exit;
		}
	else
		{
		if ($args =~ /--debug/i)
			{$Q = 0;}
		if ($args =~ /--nodupcheck/i) {
			$check_dup = 0;
		}
		if ($args =~ /--xdebug/i)
			{$QQ = 0;}
	}
}

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
$currRow = 1;                   # Current row
$server_ip = $VARserver_ip;		# Asterisk server IP

use DBI;
# DB asterisk connection
$dbhA = DBI->connect("DBI:mysql:$VARDB_database:$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass")
    or die "Couldn't connect to database: " . DBI->errstr;
# DB goautodial connection
$dbhG = DBI->connect("DBI:mysql:$VARDBgo_database:$VARDBgo_server:$VARDBgo_port", "$VARDBgo_user", "$VARDBgo_pass")
    or die "Couldn't connect to database: " . DBI->errstr;

if (!$Q) {print "\n---- google_sheets.pl by Chris Lomuntad ----\n\n";}
if (!$Q) {print " This program is designed to get available leads from google sheets specified on the\n";}
if (!$Q) {print " campaign settings' google_sheet_ids and then put all leads gathered to the list_id\n";}
if (!$Q) {print " specified under vicidial_campaigns.\n\n";}


# Getting the Google API Key from the database
$stmtA = "SELECT value FROM settings WHERE setting = 'google_api_key';";
$sthA = $dbhG->prepare($stmtA) or die "preparing: ",$dbhG->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhG->errstr;
$sthArows = $sthA->rows;
if ($sthArows > 0) {
    @aryA = $sthA->fetchrow_array;
    $googleAPIKey = $aryA[0];
}
$sthA->finish();

# Getting System Settings
$serverGMT = 0;

$stmtA = "SELECT value FROM settings WHERE setting='timezone' AND context='creamy';";
$sthA = $dbhG->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
$sthA->execute or die "executing: $stmtA ", $dbhA->errstr;
$sthArows = $sthA->rows;
if ($sthArows > 0) {
	@aryS = $sthA->fetchrow_array;
	$DBserverGMT = $aryS[0];
	if (length($DBserverGMT) > 0) {
		$ENV{TZ} = "$DBserverGMT";
		tzset;
	}
}

($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime();
$serverGMT = strftime "%z", localtime;
$serverGMT =~ s/\+//g;
$serverGMT = ($serverGMT + 0);
$serverGMT = sprintf("%.2f", ($serverGMT / 100));

my $LOCAL_GMT_OFF = $serverGMT;
my $LOCAL_GMT_OFF_STD = $serverGMT;
$sthA->finish();

if (!$googleAPIKey) {
    print "Google API Key NOT set.\n";
    die;
}


if (!$Q) {print "\n---- Processing go_campaigns table... Gathering Google Sheet IDs... ----\n";}
if (!$Q) {
	if ($check_dup) {
		print "\n---- Checking for Duplicates is Enabled ----\n\n";
	} else {
		print "\n---- Checking for Duplicates is Disabled ----\n";
	}
}

# Gather google sheet ids
$stmtG = "SELECT campaign_id,google_sheet_ids,google_sheet_list_id FROM go_campaigns WHERE google_sheet_ids <> '';";
$sthG = $dbhG->prepare($stmtG) or die "preparing: ",$dbhG->errstr;
$sthG->execute or die "executing: $stmtG ", $dbhG->errstr;
$sthGrows = $sthG->rows;
$insertedRows = 0;
if ($sthGrows > 0) {
    while (my @row = $sthG->fetchrow_array) {
        if (!$Q) {print "\ncampaign_id: $row[0]  google_sheet_ids: $row[1]  google_sheet_list_id: $row[2]\n\n\n";}
        my $sheet_list_id = $row[2];
        my @sheet_ids = split / /, $row[1];
        #print $_, "\n" for @sheet_ids;
        foreach (@sheet_ids) {
            #print "$_\n";
            my $response = $ua->get("https://sheets.googleapis.com/v4/spreadsheets/$_/values/Sheet1!A$currRow:Z1000?key=$googleAPIKey");
            
            if ($response->is_success) {
                if (!$Q) {print "---- Gathering Leads per Sheet IDs ----\n";}
                if (!$Q) {print "Sheet ID: $_\n";}
                if (!$Q) {print "Google Sheet URL: https://sheets.googleapis.com/v4/spreadsheets/$_/values/Sheet1!A$currRow:Z1000?key=$googleAPIKey\n\n";}
                my $decoded_json = decode_json( $response->content );
                #print Dumper $decoded_json;
                $rowCnt = 1;
                foreach my $leadRow (@{ $decoded_json->{'values'} }) {
                    $phone_number = $leadRow->[0];
                    $isDup = "N";
                    if ( $phone_number =~ /^[0-9]+$/ ) {
                        #print "$phone_number is a number\n";
                        $vendor_lead_code = $leadRow->[1];
                        $phone_code = length($leadRow->[2]) > 0 ? '1' : $leadRow->[2];
                        $title = $leadRow->[3];
                        $first_name = $leadRow->[4];
                        $middle_initial = $leadRow->[5];
                        $last_name = $leadRow->[6];
                        $address = $leadRow->[7];
                        $address2 = $leadRow->[8];
                        $address3 = $leadRow->[9];
                        $city = $leadRow->[10];
                        $state = $leadRow->[11];
                        $province = $leadRow->[12];
                        $postal_code = $leadRow->[13];
                        $country_code = $leadRow->[14];
                        $gender = $leadRow->[15];
                        $date_of_birth = $leadRow->[16];
                        $alt_phone = $leadRow->[17];
                        $email = $leadRow->[18];
                        $security_phrase = $leadRow->[19];
                        $comments = $leadRow->[20];
                        $gmt_offset_now = '';
                        
                        if ($check_dup) {
                            $stmtA = "SELECT lead_id FROM vicidial_list WHERE phone_number = ? AND list_id = ?";
                            $sthA = $dbhA->prepare($stmtA) or die "preparing: ",$dbhA->errstr;
                            $sthA->execute($phone_number,$sheet_list_id) or die "executing: $stmtA ", $dbhA->errstr;
                            if ($sthA->rows > 0 ) {
                                $isDup = "Y";
                            }
                            $sthA->finish();
                        }
                        
                        if (!$Q and $check_dup) {print "Is '$phone_number' Duplicate?: $isDup\n";}
                        if ($isDup eq "N") {
                            my $NOWdate = strftime "%F %H:%M:%S", localtime;
                            $USarea = substr $phone_number, 0, 3;
                            $gmt_offset_now = lookup_gmt($phone_code, $USarea, $state, $LOCAL_GMT_OFF_STD, $hour, $min, $sec, $mon, $mday, $year, $tz_method, $postal_code);
                            $status = 'NEW';
                            $stmtB = "INSERT INTO vicidial_list (entry_date,status,list_id,phone_code,phone_number,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,comments,vendor_lead_code,gmt_offset_now,title,date_of_birth,alt_phone,email,security_phrase,gender) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);";
                            $sthB = $dbhA->prepare($stmtB) or die "preparing: ",$dbhA->errstr;
                            $sthB->execute($NOWdate,$status,$sheet_list_id,$phone_code,$phone_number,$first_name,$middle_initial,$last_name,$address,$address2,$address3,$city,$state,$province,$postal_code,$country_code,$comments,$vendor_lead_code,$gmt_offset_now,$title,$date_of_birth,$alt_phone,$email,$security_phrase,$gender) or die "executing: $stmtB ", $dbhA->errstr;
                            $sthB->finish();
                            if (!$Q) {printf("INSERT INTO vicidial_list (entry_date,status,list_id,phone_code,phone_number,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,comments,vendor_lead_code,gmt_offset_now,title,date_of_birth,alt_phone,email,security_phrase,gender) VALUES('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s');\n",$NOWdate,$status,$sheet_list_id,$phone_code,$phone_number,$first_name,$middle_initial,$last_name,$address,$address2,$address3,$city,$state,$province,$postal_code,$country_code,$comments,$vendor_lead_code,$gmt_offset_now,$title,$date_of_birth,$alt_phone,$email,$security_phrase,$gender);}
                            $insertedRows++;
                        }
                    } else {
                        if (!$Q) {print "'$phone_number' is NOT a number\n";}
                    }
                    $rowCnt++;
                }
            }
        }
    }
    if (!$Q) {print "\n---- Done with loading leads from Google Sheets ----\n";}
    if (!$Q) {print "$insertedRows row(s) inserted into vicidial_list table\n\n";}
	
	sub lookup_gmt {
		my ($Sphone_code, $SUSarea, $Sstate, $SLOCAL_GMT_OFF_STD, $Shour, $Smin, $Ssec, $Smon, $Smday, $Syear, $Spostalgmt, $Spostal_code) = @_;
		
        $postalgmt_found = 0;
        if ( ($Spostalgmt =~ m/POSTAL/i) && (length($Spostal_code) > 4) ) {
            if ($Sphone_code =~ m/^1$/) {
                $stmtL = "SELECT postal_code,state,GMT_offset,DST,DST_range,country,country_code FROM vicidial_postal_codes WHERE country_code='$Sphone_code' AND postal_code LIKE \"$Spostal_code%\";";
				$sthL = $dbhA->prepare($stmtL) or die "preparing: ",$dbhA->errstr;
				$sthL->execute or die "executing: $stmtL ", $dbhA->errstr;
                $pc_recs = $sthL->rows;
                if ($pc_recs > 0) {
					my @rowL = $sthL->fetchrow_array;
                    $gmt_offset =	$rowL[2];
                    $gmt_offset =~	s/\+//g;
                    $dst =			$rowL[3];
                    $dst_range =	$rowL[4];
                    $PC_processed++;
                    $postalgmt_found++;
                    $post++;
                }
				$sthL->finish();
            }
        }

        if ($postalgmt_found < 1) {
            $PC_processed = 0;
            ### UNITED STATES or MEXICO or AUSTRALIA ###
            if ($Sphone_code == '1' or $Sphone_code == '52' or $Sphone_code == '61') {
                $stmtL = "SELECT country_code,country,areacode,state,GMT_offset,DST,DST_range,geographic_description FROM vicidial_phone_codes WHERE country_code='$Sphone_code' AND areacode='$SUSarea';";
				$sthL = $dbhA->prepare($stmtL) or die "preparing: ",$dbhA->errstr;
				$sthL->execute or die "executing: $stmtL ", $dbhA->errstr;
                $pc_recs = $sthL->rows;
                if ($pc_recs > 0) {
                    my @rowL = $sthL->fetchrow_array;
                    $gmt_offset =	$rowL[4];
                    $gmt_offset =~	s/\+//g;
                    $dst =			$rowL[5];
                    $dst_range =	$rowL[6];
                    $PC_processed++;
				}
				$sthL->finish();
			}

            ### ALL OTHER COUNTRY CODES ###
            if (!$PC_processed) {
                $PC_processed++;
                $stmtL = "SELECT country_code,country,areacode,state,GMT_offset,DST,DST_range,geographic_description FROM vicidial_phone_codes WHERE country_code='$Sphone_code';";
				$sthL = $dbhA->prepare($stmtL) or die "preparing: ",$dbhA->errstr;
				$sthL->execute or die "executing: $stmtL ", $dbhA->errstr;
                $pc_recs = $sthL->rows;
                if ($pc_recs > 0) {
                    my @rowL = $sthL->fetchrow_array;
                    $gmt_offset =	$rowL[4];
                    $gmt_offset =~	s/\+//g;
                    $dst =			$rowL[5];
                    $dst_range =	$rowL[6];
                    $PC_processed++;
				}
				$sthL->finish();
			}
		}

        ### Find out if DST to raise the gmt offset ###
        $AC_GMT_diff = ($gmt_offset - $SLOCAL_GMT_OFF_STD);

        if (!$QQ) {
            print "$gmt_offset\n";
            print "$SLOCAL_GMT_OFF_STD\n";
            print "$AC_GMT_diff\n";
            print "$Shour\n";
            print (23 + $AC_GMT_diff);
            print "\n";
            print "Sec: $Ssec  Min: $Smin Hour: $Shour Day: $Smday Mon: $Smon Year: $Syear\n";
            print "\n\n";
        }
        
        $Thour = ($Shour + $AC_GMT_diff);
        $Tmday = $Smday;
        $Tmon = $Smon;
        $Tyear = ($Syear + 1900);
        if ($Thour > 23) {
            $Thour -= 23;
            $Tmday += 1;
        } elsif ($Thour < 0) {
            $Thour += 23;
            $Tmday -= 1;
        }
        
        $isLeapYear = IsLeapYear($Tyear) ? 29 : 28;
        if ($Tmon == 3 || $Tmon == 5 || $Tmon == 8 || $Tmon == 10) {
            if ($Tmday > 30) {
                $Tmday -= 30;
                $Tmon += 1;
            } elsif ($Tmday < 1) {
                $Tmday += 31;
                $Tmon -= 1;
            }
        } elsif ($Tmon == 1) {
            if ($Tmday > $isLeapYear) {
                $Tmday -= $isLeapYear;
                $Tmon += 1;
            } elsif ($Tmday < 1) {
                $Tmday += 31;
                $Tmon -= 1;
            }
        }

        if (!$QQ) {
            print "$Thour\n";
            print "$Tmday\n\n";
        }
        
	$AC_localtime = timelocal($Ssec,$Smin,$Thour,$Tmday,$Tmon,$Tyear);
        $Xhour = strftime "%H", localtime($AC_localtime);
        $Xmin = strftime "%M", localtime($AC_localtime);
        $Xsec = strftime "%S", localtime($AC_localtime);
        $Xmon = strftime "%m", localtime($AC_localtime);
        $Xmday = strftime "%d", localtime($AC_localtime);
        $Xwday = strftime "%w", localtime($AC_localtime);
        $Xyear = strftime "%Y", localtime($AC_localtime);
        $dsec = ( ( ($Xhour * 3600) + ($Xmin * 60) ) + $Xsec );

        $AC_processed = 0;
        if ( (!$AC_processed) and ($dst_range == 'SSM-FSN') ) {
            #**********************************************************************
            # SSM-FSN
            #     This is returns 1 if Daylight Savings Time is in effect and 0 if 
            #       Standard time is in effect.
            #     Based on Second Sunday March to First Sunday November at 2 am.
            #     INPUTS:
            #       mm              INTEGER       Month.
            #       dd              INTEGER       Day of the month.
            #       ns              INTEGER       Seconds into the day.
            #       dow             INTEGER       Day of week (0=Sunday, to 6=Saturday)
            #     OPTIONAL INPUT:
            #       timezone        INTEGER       hour difference UTC - local standard time
            #                                      (DEFAULT is blank)
            #                                     make calculations based on UTC time, 
            #                                     which means shift at 10:00 UTC in April
            #                                     and 9:00 UTC in October
            #     OUTPUT: 
            #                       INTEGER       1 = DST, 0 = not DST
            #
            # S  M  T  W  T  F  S
            # 1  2  3  4  5  6  7
            # 8  9 10 11 12 13 14
            #15 16 17 18 19 20 21
            #22 23 24 25 26 27 28
            #29 30 31
            # 
            # S  M  T  W  T  F  S
            #    1  2  3  4  5  6
            # 7  8  9 10 11 12 13
            #14 15 16 17 18 19 20
            #21 22 23 24 25 26 27
            #28 29 30 31
            # 
            #**********************************************************************

			$USACAN_DST = 0;
			$mm = $Xmon;
			$dd = $Xmday;
			$ns = $dsec;
			$dow= $Xwday;

			if ($mm < 3 || $mm > 11) {
                $USACAN_DST = 0;   
			} elsif ($mm >= 4 and $mm <= 10) {
                $USACAN_DST = 1;   
			} elsif ($mm == 3) {
                if ($dd > 13) {
                    $USACAN_DST = 1;   
                } elsif ($dd >= ($dow+8)) {
                    if ($timezone) {
                        if ($dow == 0 and $ns < (7200+$timezone*3600)) {
                            $USACAN_DST = 0;   
                        } else {
                            $USACAN_DST = 1;   
                        }
                    } else {
                        if ($dow == 0 and $ns < 7200) {
                            $USACAN_DST = 0;   
                        } else {
                            $USACAN_DST = 1;   
                        }
                    }
                } else {
                    $USACAN_DST = 0;   
                }
			} elsif ($mm == 11) {
                if ($dd > 7) {
                    $USACAN_DST = 0;   
                } elsif ($dd < ($dow+1)) {
                    $USACAN_DST = 1;   
                } elsif ($dow == 0) {
                    if ($timezone) { # UTC calculations
                        if ($ns < (7200+($timezone-1)*3600)) {
                            $USACAN_DST = 1;   
                        } else {
                            $USACAN_DST = 0;   
                        }
                    } else { # local time calculations
                        if ($ns < 7200) {
                            $USACAN_DST = 1;   
                        } else {
                            $USACAN_DST = 0;   
                        }
                    }
                } else {
                    $USACAN_DST = 0;   
                }
			} # end of month checks
			
            if ($USACAN_DST) {$gmt_offset++;}
            $AC_processed++;
		}

        if ( (!$AC_processed) and ($dst_range == 'FSA-LSO') ) {
            #**********************************************************************
            # FSA-LSO
            #     This is returns 1 if Daylight Savings Time is in effect and 0 if 
            #       Standard time is in effect.
            #     Based on first Sunday in April and last Sunday in October at 2 am.
            #**********************************************************************
			
			$USA_DST = 0;
			$mm = $Xmon;
			$dd = $Xmday;
			$ns = $dsec;
			$dow= $Xwday;

			if ($mm < 4 || $mm > 10) {
                $USA_DST = 0;
			} elsif ($mm >= 5 and $mm <= 9) {
                $USA_DST = 1;
			} elsif ($mm == 4) {
                if ($dd > 7) {
                    $USA_DST = 1;
                } elsif ($dd >= ($dow+1)) {
                    if ($timezone) {
                        if ($dow == 0 and $ns < (7200+$timezone*3600)) {
                            $USA_DST = 0;
                        } else {
                            $USA_DST = 1;
                        }
                    } else {
                        if ($dow == 0 and $ns < 7200) {
                            $USA_DST = 0;
                        } else {
                            $USA_DST = 1;
                        }
                    }
                } else {
                    $USA_DST = 0;
                }
			} elsif ($mm == 10) {
                if ($dd < 25) {
                    $USA_DST = 1;
                } elsif ($dd < ($dow+25)) {
                    $USA_DST = 1;
                } elsif ($dow == 0) {
                    if ($timezone) { # UTC calculations
                        if ($ns < (7200+($timezone-1)*3600)) {
                            $USA_DST = 1;
                        } else {
                            $USA_DST = 0;
                        }
                    } else { # local time calculations
                        if ($ns < 7200) {
                            $USA_DST = 1;
                        } else {
                            $USA_DST = 0;
                        }
                    }
                } else {
                    $USA_DST = 0;
                }
			} # end of month checks

            if ($USA_DST) {$gmt_offset++;}
            $AC_processed++;
		}

        if ( (!$AC_processed) and ($dst_range == 'LSM-LSO') ) {
            #**********************************************************************
            #     This is s 1 if Daylight Savings Time is in effect and 0 if 
            #       Standard time is in effect.
            #     Based on last Sunday in March and last Sunday in October at 1 am.
            #**********************************************************************
			
			$GBR_DST = 0;
			$mm = $Xmon;
			$dd = $Xmday;
			$ns = $dsec;
			$dow= $Xwday;

			if ($mm < 3 || $mm > 10) {
                $GBR_DST = 0;
			} elsif ($mm >= 4 and $mm <= 9) {
                $GBR_DST = 1;
			} elsif ($mm == 3) {
                if ($dd < 25) {
                    $GBR_DST = 0;
                } elsif ($dd < ($dow+25)) {
                    $GBR_DST = 0;
                } elsif ($dow == 0) {
                    if ($timezone) { # UTC calculations
                        if ($ns < (3600+($timezone-1)*3600)) {
                            $GBR_DST = 0;
                        } else {
                            $GBR_DST = 1;
                        }
                    } else { # local time calculations
                        if ($ns < 3600) {
                            $GBR_DST = 0;
                        } else {
                            $GBR_DST = 1;
                        }
                    }
                } else {
                    $GBR_DST = 1;
                }
			} elsif ($mm == 10) {
                if ($dd < 25) {
                    $GBR_DST = 1;
                } elsif ($dd < ($dow+25)) {
                    $GBR_DST = 1;
                } elsif ($dow == 0) {
                    if ($timezone) { # UTC calculations
                        if ($ns < (3600+($timezone-1)*3600)) {
                            $GBR_DST = 1;
                        } else {
                            $GBR_DST = 0;
                        }
                    } else { # local time calculations
                        if ($ns < 3600) {
                            $GBR_DST = 1;
                        } else {
                            $GBR_DST = 0;
                        }
                    }
                } else {
                    $GBR_DST = 0;
                }
			} # end of month checks
			
            if ($GBR_DST) {$gmt_offset++;}
            $AC_processed++;
		}

        if ( (!$AC_processed) and ($dst_range == 'LSO-LSM') ) {
            #**********************************************************************
            #     This is s 1 if Daylight Savings Time is in effect and 0 if 
            #       Standard time is in effect.
            #     Based on last Sunday in October and last Sunday in March at 1 am.
            #**********************************************************************
			
			$AUS_DST = 0;
			$mm = $Xmon;
			$dd = $Xmday;
			$ns = $dsec;
			$dow= $Xwday;

			if ($mm < 3 || $mm > 10) {
                $AUS_DST = 1;
			} elsif ($mm >= 4 and $mm <= 9) {
                $AUS_DST = 0;
			} elsif ($mm == 3) {
                if ($dd < 25) {
                    $AUS_DST = 1;
                } elsif ($dd < ($dow+25)) {
                    $AUS_DST = 1;
                } elsif ($dow == 0) {
                    if ($timezone) { # UTC calculations
                        if ($ns < (3600+($timezone-1)*3600)) {
                            $AUS_DST = 1;
                        } else {
                            $AUS_DST = 0;
                        }
                    } else { # local time calculations
                        if ($ns < 3600) {
                            $AUS_DST = 1;
                        } else {
                            $AUS_DST = 0;
                        }
                    }
                } else {
                    $AUS_DST = 0;
                }
			} elsif ($mm == 10) {
                if ($dd < 25) {
                    $AUS_DST = 0;
                } elsif ($dd < ($dow+25)) {
                    $AUS_DST = 0;
                } elsif ($dow == 0) {
                    if ($timezone) { # UTC calculations
                        if ($ns < (3600+($timezone-1)*3600)) {
                            $AUS_DST = 0;
                        } else {
                            $AUS_DST = 1;
                        }
                    } else { # local time calculations
                        if ($ns < 3600) {
                            $AUS_DST = 0;
                        } else {
                            $AUS_DST = 1;
                        }
                    }
                } else {
                    $AUS_DST = 1;
                }
			} # end of month checks
			
            if ($AUS_DST) {$gmt_offset++;}
            $AC_processed++;
		}

        if ( (!$AC_processed) and ($dst_range == 'FSO-LSM') ) {
            #**********************************************************************
            #   TASMANIA ONLY
            #     This is s 1 if Daylight Savings Time is in effect and 0 if 
            #       Standard time is in effect.
            #     Based on first Sunday in October and last Sunday in March at 1 am.
            #**********************************************************************
			
			$AUST_DST = 0;
			$mm = $Xmon;
			$dd = $Xmday;
			$ns = $dsec;
			$dow= $Xwday;

			if ($mm < 3 || $mm > 10) {
                $AUST_DST = 1;
			} elsif ($mm >= 4 and $mm <= 9) {
                $AUST_DST = 0;
			} elsif ($mm == 3) {
                if ($dd < 25) {
                    $AUST_DST = 1;
                } elsif ($dd < ($dow+25)) {
                    $AUST_DST = 1;
                } elsif ($dow == 0) {
                    if ($timezone) { # UTC calculations
                        if ($ns < (3600+($timezone-1)*3600)) {
                            $AUST_DST = 1;
                        } else {
                            $AUST_DST = 0;
                        }
                    } else { # local time calculations
                        if ($ns < 3600) {
                            $AUST_DST = 1;
                        } else {
                            $AUST_DST = 0;
                        }
                    }
                } else {
                    $AUST_DST = 0;
                }
			} elsif ($mm == 10) {
                if ($dd > 7) {
                    $AUST_DST = 1;
                } elsif ($dd >= ($dow+1)) {
                    if ($timezone) {
                        if ($dow == 0 and $ns < (7200+$timezone*3600)) {
                            $AUST_DST = 0;
                        } else {
                            $AUST_DST = 1;
                        }
                    } else {
                        if ($dow == 0 and $ns < 3600) {
                            $AUST_DST = 0;
                        } else {
                            $AUST_DST = 1;
                        }
                    }
                } else {
                    $AUST_DST = 0;
                }
			} # end of month checks
			
            if ($AUST_DST) {$gmt_offset++;}
            $AC_processed++;
		}

        if ( (!$AC_processed) and ($dst_range == 'FSO-FSA') ) {
            #**********************************************************************
            # FSO-FSA
            #   2008+ AUSTRALIA ONLY (country code 61)
            #     This is returns 1 if Daylight Savings Time is in effect and 0 if 
            #       Standard time is in effect.
            #     Based on first Sunday in October and first Sunday in April at 1 am.
            #**********************************************************************
		
            $AUSE_DST = 0;
            $mm = $Xmon;
            $dd = $Xmday;
            $ns = $dsec;
            $dow= $Xwday;
    
            if ($mm < 4 or $mm > 10) {
                $AUSE_DST = 1;   
            } elsif ($mm >= 5 and $mm <= 9) {
                $AUSE_DST = 0;   
            } elsif ($mm == 4) {
                if ($dd > 7) {
                    $AUSE_DST = 0;   
                } elsif ($dd >= ($dow+1)) {
                    if ($timezone) {
                        if ($dow == 0 and $ns < (3600+$timezone*3600)) {
                            $AUSE_DST = 1;   
                        } else {
                            $AUSE_DST = 0;   
                        }
                    } else {
                        if ($dow == 0 and $ns < 7200) {
                            $AUSE_DST = 1;   
                        } else {
                            $AUSE_DST = 0;   
                        }
                    }
                } else {
                    $AUSE_DST = 1;   
                }
            } elsif ($mm == 10) {
                if ($dd >= 8) {
                    $AUSE_DST = 1;   
                } elsif ($dd >= ($dow+1)) {
                    if ($timezone) {
                        if ($dow == 0 and $ns < (7200+$timezone*3600)) {
                            $AUSE_DST = 0;   
                        } else {
                            $AUSE_DST = 1;   
                        }
                    } else {
                        if ($dow == 0 and $ns < 3600) {
                            $AUSE_DST = 0;   
                        } else {
                            $AUSE_DST = 1;   
                        }
                    }
                } else {
                    $AUSE_DST = 0;   
                }
            } # end of month checks
            
            if ($AUSE_DST) {$gmt_offset++;}
            $AC_processed++;
		}

        if ( (!$AC_processed) and ($dst_range == 'FSO-TSM') ) {
            #**********************************************************************
            #     This is s 1 if Daylight Savings Time is in effect and 0 if 
            #       Standard time is in effect.
            #     Based on first Sunday in October and third Sunday in March at 1 am.
            #**********************************************************************
			
			$NZL_DST = 0;
			$mm = $Xmon;
			$dd = $Xmday;
			$ns = $dsec;
			$dow= $Xwday;

			if ($mm < 3 || $mm > 10) {
                $NZL_DST = 1;
			} elsif ($mm >= 4 and $mm <= 9) {
                $NZL_DST = 0;
			} elsif ($mm == 3) {
                if ($dd < 14) {
                    $NZL_DST = 1;
                } elsif ($dd < ($dow+14)) {
                    $NZL_DST = 1;
                } elsif ($dow == 0) {
                    if ($timezone) { # UTC calculations
                        if ($ns < (3600+($timezone-1)*3600)) {
                            $NZL_DST = 1;
                        } else {
                            $NZL_DST = 0;
                        }
                    } else { # local time calculations
                        if ($ns < 3600) {
                            $NZL_DST = 1;
                        } else {
                            $NZL_DST = 0;
                        }
                    }
                } else {
                    $NZL_DST = 0;
                }
			} elsif ($mm == 10) {
                if ($dd > 7) {
                    $NZL_DST = 1;
                } elsif ($dd >= ($dow+1)) {
                    if ($timezone) {
                        if ($dow == 0 and $ns < (7200+$timezone*3600)) {
                            $NZL_DST = 0;
                        } else {
                            $NZL_DST = 1;
                        }
                    } else {
                        if ($dow == 0 and $ns < 3600) {
                            $NZL_DST = 0;
                        } else {
                            $NZL_DST = 1;
                        }
                    }
                } else {
                    $NZL_DST = 0;
                }
			} # end of month checks
			
            if ($NZL_DST) {$gmt_offset++;}
            $AC_processed++;
		}

        if ( (!$AC_processed) and ($dst_range == 'LSS-FSA') ) {
            #**********************************************************************
            # LSS-FSA
            #   2007+ NEW ZEALAND (country code 64)
            #     This is returns 1 if Daylight Savings Time is in effect and 0 if 
            #       Standard time is in effect.
            #     Based on last Sunday in September and first Sunday in April at 1 am.
            #**********************************************************************
            
            $NZLN_DST = 0;
            $mm = $Xmon;
            $dd = $Xmday;
            $ns = $dsec;
            $dow= $Xwday;
    
            if ($mm < 4 || $mm > 9) {
                $NZLN_DST = 1;   
            } elsif ($mm >= 5 && $mm <= 9) {
                $NZLN_DST = 0;   
            } elsif ($mm == 4) {
                if ($dd > 7) {
                    $NZLN_DST = 0;   
                } elsif ($dd >= ($dow+1)) {
                    if ($timezone) {
                        if ($dow == 0 && $ns < (3600+$timezone*3600)) {
                            $NZLN_DST = 1;   
                        } else {
                            $NZLN_DST = 0;   
                        }
                    } else {
                        if ($dow == 0 && $ns < 7200) {
                            $NZLN_DST = 1;   
                        } else {
                            $NZLN_DST = 0;   
                        }
                    }
                } else {
                    $NZLN_DST = 1;   
                }
            } elsif ($mm == 9) {
                if ($dd < 25) {
                    $NZLN_DST = 0;   
                } elsif ($dd < ($dow+25)) {
                    $NZLN_DST = 0;   
                } elsif ($dow == 0) {
                    if ($timezone) { # UTC calculations
                        if ($ns < (3600+($timezone-1)*3600)) {
                            $NZLN_DST = 0;   
                        } else {
                            $NZLN_DST = 1;   
                        }
                    } else { # local time calculations
                        if ($ns < 3600) {
                            $NZLN_DST = 0;   
                        } else {
                            $NZLN_DST = 1;   
                        }
                    }
                } else {
                    $NZLN_DST = 1;   
                }
            } # end of month checks
            
            if ($NZLN_DST) {$gmt_offset++;}
            $AC_processed++;
		}

        if ( (!$AC_processed) and ($dst_range == 'TSO-LSF') ) {
            #**********************************************************************
            # TSO-LSF
            #     This is returns 1 if Daylight Savings Time is in effect and 0 if 
            #       Standard time is in effect. Brazil
            #     Based on Third Sunday October to Last Sunday February at 1 am.
            #**********************************************************************
			
			$BZL_DST = 0;
			$mm = $Xmon;
			$dd = $Xmday;
			$ns = $dsec;
			$dow= $Xwday;

			if ($mm < 2 || $mm > 10) {
                $BZL_DST = 1;   
			} elsif ($mm >= 3 and $mm <= 9) {
                $BZL_DST = 0;   
			} elsif ($mm == 2) {
                if ($dd < 22) {
                    $BZL_DST = 1;   
                } elsif ($dd < ($dow+22)) {
                    $BZL_DST = 1;   
                } elsif ($dow == 0) {
                    if ($timezone) { # UTC calculations
                        if ($ns < (3600+($timezone-1)*3600)) {
                            $BZL_DST = 1;   
                        } else {
                            $BZL_DST = 0;   
                        }
                    } else { # local time calculations
                        if ($ns < 3600) {
                            $BZL_DST = 1;   
                        } else {
                            $BZL_DST = 0;   
                        }
                    }
                } else {
                    $BZL_DST = 0;   
                }
			} elsif ($mm == 10) {
                if ($dd < 22) {
                    $BZL_DST = 0;   
                } elsif ($dd < ($dow+22)) {
                    $BZL_DST = 0;   
                } elsif ($dow == 0) {
                    if ($timezone) { # UTC calculations
                        if ($ns < (3600+($timezone-1)*3600)) {
                            $BZL_DST = 0;   
                        } else {
                            $BZL_DST = 1;   
                        }
                    } else { # local time calculations
                        if ($ns < 3600) {
                            $BZL_DST = 0;   
                        } else {
                            $BZL_DST = 1;   
                        }
                    }
                } else {
                    $BZL_DST = 1;   
                }
			} # end of month checks
			
            if ($BZL_DST) {$gmt_offset++;}
            $AC_processed++;
		}

        if (!$AC_processed) {
            #if ($DBX) {print "     No DST Method Found\n";}
            #if ($DBX) {print "     DST: 0\n";}
            $AC_processed++;
		}
		
		return sprintf("%.2f", $gmt_offset);
	}

	sub IsLeapYear {
	    my $year = shift;
	    return 0 if $year % 4;
	    return 1 if $year % 100;
	    return 0 if $year % 400;
	    return 1;
	}
}
$sthG->finish();
