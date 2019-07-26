<?php
# 
# functions.php    version 2.12
#
# functions for agent scripts
#
# Copyright (C) 2015  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
#
# CHANGES:
# 151212-0826 - First Build for customer chat, based on agc/functions.php
#

# $mysql_queries = 20

##### BEGIN validate user login credentials, check for failed lock out #####
function user_authorization($user,$pass,$user_option,$user_update,$bcrypt,$return_hash)
	{
	require("dbconnect_mysqli.php");

	#############################################
	##### START SYSTEM_SETTINGS LOOKUP #####
	$stmt = "SELECT use_non_latin,webroot_writable,pass_hash_enabled,pass_key,pass_cost,hosted_settings FROM system_settings;";
	$rslt=mysql_to_mysqli($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$qm_conf_ct = mysqli_num_rows($rslt);
	if ($qm_conf_ct > 0)
		{
		$row=mysqli_fetch_row($rslt);
		$non_latin =					$row[0];
		$SSwebroot_writable =			$row[1];
		$SSpass_hash_enabled =			$row[2];
		$SSpass_key =					$row[3];
		$SSpass_cost =					$row[4];
		$SShosted_settings =			$row[5];
		}
	##### END SETTINGS LOOKUP #####
	###########################################

	$STARTtime = date("U");
	$TODAY = date("Y-m-d");
	$NOW_TIME = date("Y-m-d H:i:s");
	$ip = getenv("REMOTE_ADDR");
	$browser = getenv("HTTP_USER_AGENT");
	$LOCK_over = ($STARTtime - 900); # failed login lockout time is 15 minutes(900 seconds)
	$LOCK_trigger_attempts = 10;
	$pass_hash='';

	$user = preg_replace("/\'|\"|\\\\|;| /","",$user);
	$pass = preg_replace("/\'|\"|\\\\|;| /","",$pass);

	$passSQL = "pass='$pass'";

	if ($SSpass_hash_enabled > 0)
		{
		if ($bcrypt < 1)
			{
			$pass_hash = exec("./bp.pl --pass=$pass");
			$pass_hash = preg_replace("/PHASH: |\n|\r|\t| /",'',$pass_hash);
			}
		else
			{$pass_hash = $pass;}
		$passSQL = "pass_hash='$pass_hash'";
		}

	$stmt="SELECT count(*) from vicidial_users where user='$user' and $passSQL and user_level > 0 and active='Y' and ( (failed_login_count < $LOCK_trigger_attempts) or (UNIX_TIMESTAMP(last_login_date) < $LOCK_over) );";
	if ($user_option == 'MGR')
		{$stmt="SELECT count(*) from vicidial_users where user='$user' and $passSQL and manager_shift_enforcement_override='1' and active='Y' and ( (failed_login_count < $LOCK_trigger_attempts) or (UNIX_TIMESTAMP(last_login_date) < $LOCK_over) );";}
	if ($DB) {echo "|$stmt|\n";}
	if ($non_latin > 0) {$rslt=mysql_to_mysqli("SET NAMES 'UTF8'", $link);}
	$rslt=mysql_to_mysqli($stmt, $link);
		if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'05009',$user,$server_ip,$session_name,$one_mysql_log);}
	$row=mysqli_fetch_row($rslt);
	$auth=$row[0];

	if ($auth < 1)
		{
		$auth_key='BAD'."|$stmt";
		$stmt="SELECT failed_login_count,UNIX_TIMESTAMP(last_login_date) from vicidial_users where user='$user';";
		if ($non_latin > 0) {$rslt=mysql_to_mysqli("SET NAMES 'UTF8'", $link);}
		$rslt=mysql_to_mysqli($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'05010',$user,$server_ip,$session_name,$one_mysql_log);}
		$cl_user_ct = mysqli_num_rows($rslt);
		if ($cl_user_ct > 0)
			{
			$row=mysqli_fetch_row($rslt);
			$failed_login_count =	$row[0];
			$last_login_date =		$row[1];

			if ($failed_login_count < $LOCK_trigger_attempts)
				{
				$stmt="UPDATE vicidial_users set failed_login_count=(failed_login_count+1),last_ip='$ip' where user='$user';";
				$rslt=mysql_to_mysqli($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'05011',$user,$server_ip,$session_name,$one_mysql_log);}
				}
			else
				{
				if ($LOCK_over > $last_login_date)
					{
					$stmt="UPDATE vicidial_users set last_login_date=NOW(),failed_login_count=1,last_ip='$ip' where user='$user';";
					$rslt=mysql_to_mysqli($stmt, $link);
						if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'05012',$user,$server_ip,$session_name,$one_mysql_log);}
					}
				else
					{$auth_key='LOCK';}
				}
			}
		if ($SSwebroot_writable > 0)
			{
			$fp = fopen ("./project_auth_entries.txt", "a");
			fwrite ($fp, "AGENT|FAIL|$NOW_TIME|$user|$auth_key|$ip|$browser|\n");
			fclose($fp);
			}
		}
	else
		{
		$login_problem=0;
		$aas_total=0;
		$ap_total=0;
		$vla_total=0;
		$mvla_total=0;
		$vla_set=0;
		$vla_on=0;

		$stmt = "SELECT count(*) FROM servers where active='Y' and active_asterisk_server='Y';";
		$rslt=mysql_to_mysqli($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$aas_ct = mysqli_num_rows($rslt);
		if ($aas_ct > 0)
			{
			$row=mysqli_fetch_row($rslt);
			$aas_total =				$row[0];
			}

	#	$stmt = "SELECT count(*) FROM phones where active='Y';";
	#	$rslt=mysql_to_mysqli($stmt, $link);
	#	if ($DB) {echo "$stmt\n";}
	#	$ap_ct = mysqli_num_rows($rslt);
	#	if ($ap_ct > 0)
	#		{
	#		$row=mysqli_fetch_row($rslt);
	#		$ap_total =					$row[0];
	#		}
		
		$stmt = "SELECT count(*) FROM vicidial_live_agents where user!='$user';";
		$rslt=mysql_to_mysqli($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$vla_ct = mysqli_num_rows($rslt);
		if ($vla_ct > 0)
			{
			$row=mysqli_fetch_row($rslt);
			$vla_total =				$row[0];
			}

		$stmt = "SELECT count(*) FROM vicidial_live_agents where user='$user';";
		$rslt=mysql_to_mysqli($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$mvla_ct = mysqli_num_rows($rslt);
		if ($mvla_ct > 0)
			{
			$row=mysqli_fetch_row($rslt);
			$mvla_total =				$row[0];
			}

		if ( (preg_match("/MXAG/",$SShosted_settings)) and ($mvla_total < 1) )
			{
			$vla_set = $SShosted_settings;
			$vla_set = preg_replace("/.*MXAG|_BUILD_|DRA| /",'',$vla_set);
			$vla_set = preg_replace('/[^0-9]/','',$vla_set);
			if (strlen($vla_set)>0)
				{$vla_on++;}
			}

		if ($aas_total < 1)
			{
			$auth_key='ERRSERVERS';
			$login_problem++;
			}
	#	if ($ap_total < 1)
	#		{
	#		$auth_key='ERRPHONES';
	#		$login_problem++;
	#		}
		if ( ($vla_total >= $vla_set) and ($vla_on > 0) )
			{
			$auth_key='ERRAGENTS';
			$login_problem++;
			}

		if ($login_problem < 1)
			{
			if ($user_update > 0)
				{
				$stmt="UPDATE vicidial_users set last_login_date=NOW(),last_ip='$ip',failed_login_count=0 where user='$user';";
				$rslt=mysql_to_mysqli($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'05013',$user,$server_ip,$session_name,$one_mysql_log);}
				}
			$auth_key='GOOD';
			if ( ($return_hash == '1') and ($SSpass_hash_enabled > 0) and (strlen($pass_hash) > 12) )
				{$auth_key .= "|$pass_hash";}
			}
		}
	return $auth_key;
	}
##### END validate user login credentials, check for failed lock out #####


##### BEGIN custom_list_fields_values - gather values for display of custom list fields for a lead #####
function custom_list_fields_values($lead_id,$list_id,$uniqueid,$user)
	{
	$STARTtime = date("U");
	$TODAY = date("Y-m-d");
	$NOW_TIME = date("Y-m-d H:i:s");

	$vicidial_list_fields = '|lead_id|vendor_lead_code|source_id|list_id|gmt_offset_now|called_since_last_reset|phone_code|phone_number|title|first_name|middle_initial|last_name|address1|address2|address3|city|state|province|postal_code|country_code|gender|date_of_birth|alt_phone|email|security_phrase|comments|called_count|last_local_call_time|rank|owner|';

	require("dbconnect_mysqli.php");

	$CFoutput='';
	$stmt="SHOW TABLES LIKE \"custom_$list_id\";";
	if ($non_latin > 0) {$rslt=mysql_to_mysqli("SET NAMES 'UTF8'", $link);}
	if ($DB>0) {echo "$stmt";}
	$rslt=mysql_to_mysqli($stmt, $link);
		if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'05002',$user,$server_ip,$session_name,$one_mysql_log);}
	$tablecount_to_print = mysqli_num_rows($rslt);
	if ($tablecount_to_print > 0) 
		{
		$stmt="SELECT count(*) from custom_$list_id;";
		if ($DB>0) {echo "$stmt";}
		$rslt=mysql_to_mysqli($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'05003',$user,$server_ip,$session_name,$one_mysql_log);}
		$fieldscount_to_print = mysqli_num_rows($rslt);
		if ($fieldscount_to_print > 0) 
			{
			$rowx=mysqli_fetch_row($rslt);
			$custom_records_count =	$rowx[0];

			$select_SQL='';
			$stmt="SELECT field_id,field_label,field_name,field_description,field_rank,field_help,field_type,field_options,field_size,field_max,field_default,field_cost,field_required,multi_position,name_position,field_order from vicidial_lists_fields where list_id='$list_id' order by field_rank,field_order,field_label;";
			$rslt=mysql_to_mysqli($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'05004',$user,$server_ip,$session_name,$one_mysql_log);}
			$fields_to_print = mysqli_num_rows($rslt);
			$fields_list='';
			$o=0;
			while ($fields_to_print > $o) 
				{
				$rowx=mysqli_fetch_row($rslt);
				$A_field_id[$o] =			$rowx[0];
				$A_field_label[$o] =		$rowx[1];
				$A_field_name[$o] =			$rowx[2];
				$A_field_description[$o] =	$rowx[3];
				$A_field_rank[$o] =			$rowx[4];
				$A_field_help[$o] =			$rowx[5];
				$A_field_type[$o] =			$rowx[6];
				$A_field_options[$o] =		$rowx[7];
				$A_field_size[$o] =			$rowx[8];
				$A_field_max[$o] =			$rowx[9];
				$A_field_default[$o] =		$rowx[10];
				$A_field_cost[$o] =			$rowx[11];
				$A_field_required[$o] =		$rowx[12];
				$A_multi_position[$o] =		$rowx[13];
				$A_name_position[$o] =		$rowx[14];
				$A_field_order[$o] =		$rowx[15];
				$A_field_value[$o] =		'';

				if (!preg_match("/\|$A_field_label[$o]\|/i",$vicidial_list_fields))
					{
					if ( ($A_field_type[$o]=='DISPLAY') or ($A_field_type[$o]=='SCRIPT') )
						{
						$select_SQL .= "8,";
						$A_field_select[$o]='----EMPTY----';
						}
					else
						{
						$select_SQL .= "$A_field_label[$o],";
						$A_field_select[$o]=$A_field_label[$o];
						}
					}
				else
					{
					$select_SQL .= "8,";
					$A_field_value[$o] = '--A--' . $A_field_label[$o] . '--B--';
					}
				$o++;
				$rank_select .= "<option>$o</option>";
				}
			$o++;
			$rank_select .= "<option>$o</option>";
			$last_rank = $o;
			$select_SQL = preg_replace("/.$/",'',$select_SQL);

			$list_lead_ct=0;
			if (strlen($select_SQL)>0)
				{
				##### BEGIN grab the data from custom table for the lead_id
				$stmt="SELECT $select_SQL FROM custom_$list_id where lead_id='$lead_id' LIMIT 1;";
				$rslt=mysql_to_mysqli($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'05005',$user,$server_ip,$session_name,$one_mysql_log);}
				if ($DB) {echo "$stmt\n";}
				$list_lead_ct = mysqli_num_rows($rslt);
				}
			if ($list_lead_ct > 0)
				{
				$row=mysqli_fetch_row($rslt);
				$o=0;
				while ($fields_to_print >= $o) 
					{
					$A_field_value[$o]		= trim("$row[$o]");
					if ($A_field_select[$o]=='----EMPTY----')
						{$A_field_value[$o]='';}
					if (preg_match("/\|$A_field_label[$o]\|/i",$vicidial_list_fields))
						{$A_field_value[$o] = '--A--' . $A_field_label[$o] . '--B--';}
					$o++;
					}
				}
			else
				{
				if ($DB) {echo _QXZ("ERROR: no custom data for this lead: ")."$lead_id\n";}
				}
			##### END grab the data from custom table for the lead_id


			$CFoutput .= "<input type=hidden name=stage id=stage value=\"SUBMIT\">\n";
			$CFoutput .= "<center><TABLE cellspacing=2 cellpadding=2>\n";
			if ($fields_to_print < 1) 
				{$CFoutput .= "<tr bgcolor=white align=center><td colspan=4><font size=1>"._QXZ("There are no custom fields for this list")."</td></tr>";}

			$o=0;
			$last_field_rank=0;
			while ($fields_to_print > $o) 
				{
				$helpHTML='';
				if (strlen($A_field_help[$o])>0)
					{$helpHTML="&nbsp; <a href=\"javascript:open_help('HELP_$A_field_label[$o]','$A_field_help[$o]');\">"._QXZ("help")."+</a>";}
				if ($last_field_rank=="$A_field_rank[$o]")
					{$CFoutput .= " &nbsp; &nbsp; &nbsp; &nbsp; ";}
				else
					{
					$CFoutput .= "</td></tr>\n";
					$CFoutput .= "<tr bgcolor=white><td align=";
					if ( ($A_name_position[$o]=='TOP') or ($A_field_type[$o]=='SCRIPT') )
						{$CFoutput .= "left colspan=2";}
					else
						{$CFoutput .= "right";}
					$CFoutput .= "><font size=2>";
					}
				if ( ($A_field_type[$o]!='SCRIPT') and ($A_field_type[$o]!='HIDDEN') and ($A_field_type[$o]!='HIDEBLOB') )
					{$CFoutput .= "<B>$A_field_name[$o]</B>";}
				if ( ($A_name_position[$o]=='TOP') or ($A_field_type[$o]=='SCRIPT') )
					{$CFoutput .= " &nbsp; <span style=\"position:static;\" id=P_HELP_$A_field_label[$o]></span><span style=\"position:static;background:white;\" id=HELP_$A_field_label[$o]> $helpHTML</span><BR>";}
				else
					{
					if ($last_field_rank=="$A_field_rank[$o]")
						{$CFoutput .= " &nbsp;";}
					else
						{$CFoutput .= "</td><td align=left><font size=2>";}
					}
				$field_HTML='';

				if ($A_field_type[$o]=='SELECT')
					{
					$field_HTML .= "<select size=1 name=$A_field_label[$o] id=$A_field_label[$o]>\n";
					}
				if ($A_field_type[$o]=='MULTI')
					{
					$field_HTML .= "<select MULTIPLE size=$A_field_size[$o] name=$A_field_label[$o][] id=$A_field_label[$o][]>\n";
					}
				if ( ($A_field_type[$o]=='SELECT') or ($A_field_type[$o]=='MULTI') or ($A_field_type[$o]=='RADIO') or ($A_field_type[$o]=='CHECKBOX') )
					{
					$field_options_array = explode("\n",$A_field_options[$o]);
					$field_options_count = count($field_options_array);
					$te=0;
					while ($te < $field_options_count)
						{
						if (preg_match("/,/",$field_options_array[$te]))
							{
							$field_selected='';
							$field_options_value_array = explode(",",$field_options_array[$te]);
							if ( ($A_field_type[$o]=='SELECT') or ($A_field_type[$o]=='MULTI') )
								{
								if (strlen($A_field_value[$o]) > 0) 
									{
									if (preg_match("/$field_options_value_array[0]/",$A_field_value[$o]))
										{$field_selected = 'SELECTED';}
									}
								else
									{
									if ($A_field_default[$o] == "$field_options_value_array[0]") {$field_selected = 'SELECTED';}
									}
								$field_HTML .= "<option value=\"$field_options_value_array[0]\" $field_selected>"._QXZ("$field_options_value_array[1]")."</option>\n";
								}
							if ( ($A_field_type[$o]=='RADIO') or ($A_field_type[$o]=='CHECKBOX') )
								{
								if ($A_multi_position[$o]=='VERTICAL') 
									{$field_HTML .= " &nbsp; ";}
								if (strlen($A_field_value[$o]) > 0) 
									{
									if (preg_match("/$field_options_value_array[0]/",$A_field_value[$o]))
										{$field_selected = 'CHECKED';}
									}
								else
									{
									if ($A_field_default[$o] == "$field_options_value_array[0]") {$field_selected = 'CHECKED';}
									}
								$field_HTML .= "<input type=$A_field_type[$o] name=$A_field_label[$o][] id=$A_field_label[$o][] value=\"$field_options_value_array[0]\" $field_selected> "._QXZ("$field_options_value_array[1]")."\n";
								if ($A_multi_position[$o]=='VERTICAL') 
									{$field_HTML .= "<BR>\n";}
								}
							}
						$te++;
						}
					}
				if ( ($A_field_type[$o]=='SELECT') or ($A_field_type[$o]=='MULTI') )
					{
					$field_HTML .= "</select>\n";
					}
				if ($A_field_type[$o]=='TEXT') 
					{
					if ($A_field_default[$o]=='NULL') {$A_field_default[$o]='';}
					if (strlen($A_field_value[$o]) < 1) {$A_field_value[$o] = $A_field_default[$o];}
					$field_HTML .= "<input type=text size=$A_field_size[$o] maxlength=$A_field_max[$o] name=$A_field_label[$o] id=$A_field_label[$o] value=\""._QXZ("$A_field_value[$o]")."\">\n";
					}
				if ($A_field_type[$o]=='AREA') 
					{
					if ($A_field_default[$o]=='NULL') {$A_field_default[$o]='';}
					if (strlen($A_field_value[$o]) < 1) {$A_field_value[$o] = $A_field_default[$o];}
					$field_HTML .= "<textarea name=$A_field_label[$o] id=$A_field_label[$o] ROWS=$A_field_max[$o] COLS=$A_field_size[$o]>$A_field_value[$o]</textarea>";
					}
				if ($A_field_type[$o]=='DISPLAY')
					{
					if ($A_field_default[$o]=='NULL') {$A_field_default[$o]='';}
					$field_HTML .= _QXZ("$A_field_default[$o]")."\n";
					}
				if ($A_field_type[$o]=='READONLY')
					{
					if ($A_field_default[$o]=='NULL') {$A_field_default[$o]='';}
				#	if (strlen($A_field_value[$o]) < 1) {$A_field_value[$o] = $A_field_default[$o];}
					$field_HTML .= "<input type=hidden name=$A_field_label[$o] id=$A_field_label[$o] value=\"$A_field_value[$o]\"> "._QXZ("$A_field_value[$o]")."\n";
					}
				if ( ($A_field_type[$o]=='HIDDEN') or ($A_field_type[$o]=='HIDEBLOB') )
					{
					if (strlen($A_field_value[$o]) < 1) {$A_field_value[$o] = $A_field_default[$o];}
					if ($A_field_default[$o]=='NULL') {$A_field_default[$o]='';}
					$field_HTML .= "<input type=hidden name=$A_field_label[$o] id=$A_field_label[$o] value=\"$A_field_value[$o]\">\n";
					}
				if ($A_field_type[$o]=='SCRIPT')
					{
					if ($A_field_default[$o]=='NULL') {$A_field_default[$o]='';}
					$field_HTML .= "$A_field_options[$o]\n";
					}
				if ($A_field_type[$o]=='DATE') 
					{
					if ( (strlen($A_field_default[$o])<1) or ($A_field_default[$o]=='NULL') ) {$A_field_default[$o]=0;}
					$day_diff = $A_field_default[$o];
					$default_date = date("Y-m-d", mktime(date("H"),date("i"),date("s"),date("m"),date("d")+$day_diff,date("Y")));
					if (strlen($A_field_value[$o]) > 0) {$default_date = $A_field_value[$o];}

					$field_HTML .= "<input type=text size=11 maxlength=10 name=$A_field_label[$o] id=$A_field_label[$o] value=\"$default_date\" onclick=\"f_tcalToggle()\">\n";
					$field_HTML .= "<script language=\"JavaScript\">\n";
					$field_HTML .= "var o_cal = new tcal ({\n";
					$field_HTML .= "	'formname': 'form_custom_fields',\n";
					$field_HTML .= "	'controlname': '$A_field_label[$o]'});\n";
					$field_HTML .= "o_cal.a_tpl.yearscroll = false;\n";
					$field_HTML .= "</script>\n";
					}
				if ($A_field_type[$o]=='TIME') 
					{
					$minute_diff = $A_field_default[$o];
					$default_time = date("H:i:s", mktime(date("H"),date("i")+$minute_diff,date("s"),date("m"),date("d"),date("Y")));
					$default_hour = date("H", mktime(date("H"),date("i")+$minute_diff,date("s"),date("m"),date("d"),date("Y")));
					$default_minute = date("i", mktime(date("H"),date("i")+$minute_diff,date("s"),date("m"),date("d"),date("Y")));
					if (strlen($A_field_value[$o]) > 2) 
						{
						$default_time = $A_field_value[$o];
						$time_field_value = explode(':',$default_time);
						$default_hour = $time_field_value[0];
						$default_minute = $time_field_value[1];
						}
					$field_HTML .= "<input type=hidden name=$A_field_label[$o] id=$A_field_label[$o] value=\"$default_time\">";
					$field_HTML .= "<SELECT name=HOUR_$A_field_label[$o] id=HOUR_$A_field_label[$o]>";
					$field_HTML .= "<option>00</option>";
					$field_HTML .= "<option>01</option>";
					$field_HTML .= "<option>02</option>";
					$field_HTML .= "<option>03</option>";
					$field_HTML .= "<option>04</option>";
					$field_HTML .= "<option>05</option>";
					$field_HTML .= "<option>06</option>";
					$field_HTML .= "<option>07</option>";
					$field_HTML .= "<option>08</option>";
					$field_HTML .= "<option>09</option>";
					$field_HTML .= "<option>10</option>";
					$field_HTML .= "<option>11</option>";
					$field_HTML .= "<option>12</option>";
					$field_HTML .= "<option>13</option>";
					$field_HTML .= "<option>14</option>";
					$field_HTML .= "<option>15</option>";
					$field_HTML .= "<option>16</option>";
					$field_HTML .= "<option>17</option>";
					$field_HTML .= "<option>18</option>";
					$field_HTML .= "<option>19</option>";
					$field_HTML .= "<option>20</option>";
					$field_HTML .= "<option>21</option>";
					$field_HTML .= "<option>22</option>";
					$field_HTML .= "<option>23</option>";
					$field_HTML .= "<OPTION value=\"$default_hour\" selected>$default_hour</OPTION>";
					$field_HTML .= "</SELECT>";
					$field_HTML .= "<SELECT name=MINUTE_$A_field_label[$o] id=MINUTE_$A_field_label[$o]>";
					$field_HTML .= "<option>00</option>";
					$field_HTML .= "<option>05</option>";
					$field_HTML .= "<option>10</option>";
					$field_HTML .= "<option>15</option>";
					$field_HTML .= "<option>20</option>";
					$field_HTML .= "<option>25</option>";
					$field_HTML .= "<option>30</option>";
					$field_HTML .= "<option>35</option>";
					$field_HTML .= "<option>40</option>";
					$field_HTML .= "<option>45</option>";
					$field_HTML .= "<option>50</option>";
					$field_HTML .= "<option>55</option>";
					$field_HTML .= "<OPTION value=\"$default_minute\" selected>$default_minute</OPTION>";
					$field_HTML .= "</SELECT>";
					}

				if ( ($A_name_position[$o]=='LEFT') and ($A_field_type[$o]!='SCRIPT') and ($A_field_type[$o]!='HIDDEN') and ($A_field_type[$o]!='HIDEBLOB') )
					{
					$CFoutput .= " $field_HTML <span style=\"position:static;\" id=P_HELP_$A_field_label[$o]></span><span style=\"position:static;background:white;\" id=HELP_$A_field_label[$o]> $helpHTML</span>";
					}
				else
					{
					$CFoutput .= " $field_HTML\n";
					}

				$last_field_rank=$A_field_rank[$o];
				$o++;
				}
			$CFoutput .= "</td></tr></table>\n";
			}
		else
			{$CFoutput .= _QXZ("ERROR: no custom list fields")."\n";}
		}
	else
		{$CFoutput .= _QXZ("ERROR: no custom list fields table")."\n";}


	##### BEGIN parsing for vicidial variables #####
	$NOTESout='';
	if (preg_match("/--A--/",$CFoutput))
		{
		if ( (preg_match('/--A--user_custom_/i',$CFoutput)) or (preg_match('/--A--fullname/i',$CFoutput)) )
			{
			$stmt = "select custom_one,custom_two,custom_three,custom_four,custom_five,full_name from vicidial_users where user='$user';";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_to_mysqli($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'05006',$user,$server_ip,$session_name,$one_mysql_log);}
			$VUC_ct = mysqli_num_rows($rslt);
			if ($VUC_ct > 0)
				{
				$row=mysqli_fetch_row($rslt);
				$user_custom_one	=		trim($row[0]);
				$user_custom_two	=		trim($row[1]);
				$user_custom_three	=		trim($row[2]);
				$user_custom_four	=		trim($row[3]);
				$user_custom_five	=		trim($row[4]);
				$fullname	=				trim($row[5]);
				}
			}

		if (preg_match('/--A--dialed_/i',$CFoutput))
			{
			$dialed_number =	$phone_number;
			$dialed_label =		_QXZ("NONE");

			### find the dialed number and label for this call
			$stmt = "SELECT phone_number,alt_dial from vicidial_log where uniqueid='$uniqueid';";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_to_mysqli($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'05008',$user,$server_ip,$session_name,$one_mysql_log);}
			$vl_dialed_ct = mysqli_num_rows($rslt);
			if ($vl_dialed_ct > 0)
				{
				$row=mysqli_fetch_row($rslt);
				$dialed_number =	$row[0];
				$dialed_label =		$row[1];
				}
			}

		if (preg_match('/--A--TABLEper_call_notes--B--/i',$CFoutput))
			{
			### BEGIN Gather Call Log and notes ###
			if ($hide_call_log_info!='Y')
				{
				if ($search != 'logfirst')
					{$NOTESout .= _QXZ("CALL LOG FOR THIS LEAD:")."<br>\n";}
				$NOTESout .= "<TABLE CELLPADDING=0 CELLSPACING=1 BORDER=0 WIDTH=$stage>";
				$NOTESout .= "<TR>";
				$NOTESout .= "<TD BGCOLOR=\"#CCCCCC\"><font style=\"font-size:10px;font-family:sans-serif;\"><B> &nbsp; # &nbsp; </font></TD>";
				$NOTESout .= "<TD BGCOLOR=\"#CCCCCC\"><font style=\"font-size:11px;font-family:sans-serif;\"><B> &nbsp; "._QXZ("DATE/TIME")." &nbsp; </font></TD>";
				$NOTESout .= "<TD BGCOLOR=\"#CCCCCC\"><font style=\"font-size:11px;font-family:sans-serif;\"><B> &nbsp; "._QXZ("AGENT")." &nbsp; </font></TD>";
				$NOTESout .= "<TD BGCOLOR=\"#CCCCCC\"><font style=\"font-size:11px;font-family:sans-serif;\"><B> &nbsp; "._QXZ("LENGTH")." &nbsp; </font></TD>";
				$NOTESout .= "<TD BGCOLOR=\"#CCCCCC\"><font style=\"font-size:11px;font-family:sans-serif;\"><B> &nbsp; "._QXZ("STATUS")." &nbsp; </font></TD>";
				$NOTESout .= "<TD BGCOLOR=\"#CCCCCC\"><font style=\"font-size:11px;font-family:sans-serif;\"><B> &nbsp; "._QXZ("PHONE")." &nbsp; </font></TD>";
				$NOTESout .= "<TD BGCOLOR=\"#CCCCCC\"><font style=\"font-size:11px;font-family:sans-serif;\"><B> &nbsp; "._QXZ("CAMPAIGN")." &nbsp; </font></TD>";
				$NOTESout .= "<TD BGCOLOR=\"#CCCCCC\"><font style=\"font-size:11px;font-family:sans-serif;\"><B> &nbsp; "._QXZ("IN/OUT")." &nbsp; </font></TD>";
				$NOTESout .= "<TD BGCOLOR=\"#CCCCCC\"><font style=\"font-size:11px;font-family:sans-serif;\"><B> &nbsp; "._QXZ("ALT")." &nbsp; </font></TD>";
				$NOTESout .= "<TD BGCOLOR=\"#CCCCCC\"><font style=\"font-size:11px;font-family:sans-serif;\"><B> &nbsp; "._QXZ("HANGUP")." &nbsp; </font></TD>";
			#	$NOTESout .= "</TR><TR>";
			#	$NOTESout .= "<TD BGCOLOR=\"#CCCCCC\" COLSPAN=9><font style=\"font-size:11px;font-family:sans-serif;\"><B> &nbsp; FULL NAME &nbsp; </font></TD>";
				$NOTESout .= "</TR>";


				$stmt="SELECT start_epoch,call_date,campaign_id,length_in_sec,status,phone_code,phone_number,lead_id,term_reason,alt_dial,comments,uniqueid,user from vicidial_log where lead_id='$lead_id' order by call_date desc limit 10000;";
				$rslt=mysql_to_mysqli($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'05014',$user,$server_ip,$session_name,$one_mysql_log);}
				$out_logs_to_print = mysqli_num_rows($rslt);
				if ($format=='debug') {$NOTESout .= "|$out_logs_to_print|$stmt|";}

				$g=0;
				$u=0;
				while ($out_logs_to_print > $u) 
					{
					$row=mysqli_fetch_row($rslt);
					$ALLsort[$g] =			"$row[0]-----$g";
					$ALLstart_epoch[$g] =	$row[0];
					$ALLcall_date[$g] =		$row[1];
					$ALLcampaign_id[$g] =	$row[2];
					$ALLlength_in_sec[$g] =	$row[3];
					$ALLstatus[$g] =		$row[4];
					$ALLphone_code[$g] =	$row[5];
					$ALLphone_number[$g] =	$row[6];
					$ALLlead_id[$g] =		$row[7];
					$ALLhangup_reason[$g] =	$row[8];
					$ALLalt_dial[$g] =		$row[9];
					$ALLuniqueid[$g] =		$row[11];
					$ALLuser[$g] =			$row[12];
					$ALLin_out[$g] =		"OUT-AUTO";
					if ($row[10] == 'MANUAL') {$ALLin_out[$g] = "OUT-MANUAL";}

					$stmtA="SELECT call_notes FROM vicidial_call_notes WHERE lead_id='$ALLlead_id[$g]' and vicidial_id='$ALLuniqueid[$g]';";
					$rsltA=mysql_to_mysqli($stmtA, $link);
						if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'05015',$user,$server_ip,$session_name,$one_mysql_log);}
					$out_notes_to_print = mysqli_num_rows($rslt);
					if ($out_notes_to_print > 0)
						{
						$rowA=mysqli_fetch_row($rsltA);
						$Allcall_notes[$g] =	$rowA[0];
						if (strlen($Allcall_notes[$g]) > 0)
							{$Allcall_notes[$g] =	"<b>"._QXZ("NOTES:")." </b> "._QXZ("$Allcall_notes[$g]");}
						}
					$stmtA="SELECT full_name FROM vicidial_users WHERE user='$ALLuser[$g]';";
					$rsltA=mysql_to_mysqli($stmtA, $link);
						if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'05016',$user,$server_ip,$session_name,$one_mysql_log);}
					$users_to_print = mysqli_num_rows($rslt);
					if ($users_to_print > 0)
						{
						$rowA=mysqli_fetch_row($rsltA);
						$ALLuser[$g] .=	" - $rowA[0]";
						}

					$Allcounter[$g] =		$g;
					$g++;
					$u++;
					}

				$stmt="SELECT start_epoch,call_date,campaign_id,length_in_sec,status,phone_code,phone_number,lead_id,term_reason,queue_seconds,uniqueid,closecallid,user from vicidial_closer_log where lead_id='$lead_id' order by call_date desc limit 10000;";
				$rslt=mysql_to_mysqli($stmt, $link);
					if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'05017',$user,$server_ip,$session_name,$one_mysql_log);}
				$in_logs_to_print = mysqli_num_rows($rslt);
				if ($format=='debug') {$NOTESout .= "|$in_logs_to_print|$stmt|";}

				$u=0;
				while ($in_logs_to_print > $u) 
					{
					$row=mysqli_fetch_row($rslt);
					$ALLsort[$g] =			"$row[0]-----$g";
					$ALLstart_epoch[$g] =	$row[0];
					$ALLcall_date[$g] =		$row[1];
					$ALLcampaign_id[$g] =	$row[2];
					$ALLlength_in_sec[$g] =	($row[3] - $row[9]);
					if ($ALLlength_in_sec[$g] < 0) {$ALLlength_in_sec[$g]=0;}
					$ALLstatus[$g] =		$row[4];
					$ALLphone_code[$g] =	$row[5];
					$ALLphone_number[$g] =	$row[6];
					$ALLlead_id[$g] =		$row[7];
					$ALLhangup_reason[$g] =	$row[8];
					$ALLuniqueid[$g] =		$row[10];
					$ALLclosecallid[$g] =	$row[11];
					$ALLuser[$g] =			$row[12];
					$ALLalt_dial[$g] =		"MAIN";
					$ALLin_out[$g] =		"IN";

					$stmtA="SELECT call_notes FROM vicidial_call_notes WHERE lead_id='$ALLlead_id[$g]' and vicidial_id='$ALLclosecallid[$g]';";
					$rsltA=mysql_to_mysqli($stmtA, $link);
						if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'05018',$user,$server_ip,$session_name,$one_mysql_log);}
					$in_notes_to_print = mysqli_num_rows($rslt);
					if ($in_notes_to_print > 0)
						{
						$rowA=mysqli_fetch_row($rsltA);
						$Allcall_notes[$g] =	$rowA[0];
						if (strlen($Allcall_notes[$g]) > 0)
							{$Allcall_notes[$g] =	"<b>"._QXZ("NOTES").": </b> "._QXZ("$Allcall_notes[$g]");}
						}
					$stmtA="SELECT full_name FROM vicidial_users WHERE user='$ALLuser[$g]';";
					$rsltA=mysql_to_mysqli($stmtA, $link);
						if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'05019',$user,$server_ip,$session_name,$one_mysql_log);}
					$users_to_print = mysqli_num_rows($rslt);
					if ($users_to_print > 0)
						{
						$rowA=mysqli_fetch_row($rsltA);
						$ALLuser[$g] .=	" - $rowA[0]";
						}

					$Allcounter[$g] =		$g;

					$g++;
					$u++;
					}

				if ($g > 0)
					{sort($ALLsort, SORT_NUMERIC);}
				else
					{$NOTESout .= "<tr bgcolor=white><td colspan=11 align=center>"._QXZ("No calls found")."</td></tr>";}

				$u=0;
				while ($g > $u) 
					{
					$sort_split = explode("-----",$ALLsort[$u]);
					$i = $sort_split[1];

					if (preg_match("/1$|3$|5$|7$|9$/i", $u))
						{$bgcolor='bgcolor="#B9CBFD"';} 
					else
						{$bgcolor='bgcolor="#9BB9FB"';}

					$phone_number_display = $ALLphone_number[$i];
					if ($disable_alter_custphone == 'HIDE')
						{$phone_number_display = 'XXXXXXXXXX';}

					$u++;
					$NOTESout .= "<tr $bgcolor>";
					$NOTESout .= "<td><font size=1>$u</td>";
					$NOTESout .= "<td align=right><font size=2>$ALLcall_date[$i]</td>";
					$NOTESout .= "<td align=right><font size=2> $ALLuser[$i]</td>\n";
					$NOTESout .= "<td align=right><font size=2> $ALLlength_in_sec[$i]</td>\n";
					$NOTESout .= "<td align=right><font size=2> $ALLstatus[$i]</td>\n";
					$NOTESout .= "<td align=right><font size=2> $ALLphone_code[$i] $phone_number_display </td>\n";
					$NOTESout .= "<td align=right><font size=2> $ALLcampaign_id[$i] </td>\n";
					$NOTESout .= "<td align=right><font size=2> $ALLin_out[$i] </td>\n";
					$NOTESout .= "<td align=right><font size=2> $ALLalt_dial[$i] </td>\n";
					$NOTESout .= "<td align=right><font size=2> $ALLhangup_reason[$i] </td>\n";
					$NOTESout .= "</TR><TR>";
					$NOTESout .= "<td></td>";
					$NOTESout .= "<TD $bgcolor COLSPAN=9 align=left><font style=\"font-size:11px;font-family:sans-serif;\"> "._QXZ("$Allcall_notes[$i]")." </font></TD>";
					$NOTESout .= "</tr>\n";
					}

				$NOTESout .= "</TABLE>";
				$NOTESout .= "<BR>";
				}
			### END Gather Call Log and notes ###
			}

		##### grab the data from vicidial_list for the lead_id
		$stmt="SELECT lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner FROM vicidial_list where lead_id='$lead_id' LIMIT 1;";
		$rslt=mysql_to_mysqli($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'05007',$user,$server_ip,$session_name,$one_mysql_log);}
		if ($DB) {echo "$stmt\n";}
		$list_lead_ct = mysqli_num_rows($rslt);
		if ($list_lead_ct > 0)
			{
			$row=mysqli_fetch_row($rslt);
			$dispo				= trim($row[3]);
			$tsr				= trim($row[4]);
			$vendor_id			= trim($row[5]);
			$vendor_lead_code	= trim($row[5]);
			$source_id			= trim($row[6]);
			$list_id			= trim($row[7]);
			$gmt_offset_now		= trim($row[8]);
			$phone_code			= trim($row[10]);
			$phone_number		= trim($row[11]);
			$title				= trim($row[12]);
			$first_name			= trim($row[13]);
			$middle_initial		= trim($row[14]);
			$last_name			= trim($row[15]);
			$address1			= trim($row[16]);
			$address2			= trim($row[17]);
			$address3			= trim($row[18]);
			$city				= trim($row[19]);
			$state				= trim($row[20]);
			$province			= trim($row[21]);
			$postal_code		= trim($row[22]);
			$country_code		= trim($row[23]);
			$gender				= trim($row[24]);
			$date_of_birth		= trim($row[25]);
			$alt_phone			= trim($row[26]);
			$email				= trim($row[27]);
			$security			= trim($row[28]);
			$comments			= trim($row[29]);
			$called_count		= trim($row[30]);
			$rank				= trim($row[32]);
			$owner				= trim($row[33]);
			}

		$CFoutput = preg_replace('/--A--lead_id--B--/i',"$lead_id",$CFoutput);
		$CFoutput = preg_replace('/--A--vendor_id--B--/i',"$vendor_id",$CFoutput);
		$CFoutput = preg_replace('/--A--vendor_lead_code--B--/i',"$vendor_lead_code",$CFoutput);
		$CFoutput = preg_replace('/--A--list_id--B--/i',"$list_id",$CFoutput);
		$CFoutput = preg_replace('/--A--gmt_offset_now--B--/i',"$gmt_offset_now",$CFoutput);
		$CFoutput = preg_replace('/--A--phone_code--B--/i',"$phone_code",$CFoutput);
		$CFoutput = preg_replace('/--A--phone_number--B--/i',"$phone_number",$CFoutput);
		$CFoutput = preg_replace('/--A--title--B--/i',"$title",$CFoutput);
		$CFoutput = preg_replace('/--A--first_name--B--/i',"$first_name",$CFoutput);
		$CFoutput = preg_replace('/--A--middle_initial--B--/i',"$middle_initial",$CFoutput);
		$CFoutput = preg_replace('/--A--last_name--B--/i',"$last_name",$CFoutput);
		$CFoutput = preg_replace('/--A--address1--B--/i',"$address1",$CFoutput);
		$CFoutput = preg_replace('/--A--address2--B--/i',"$address2",$CFoutput);
		$CFoutput = preg_replace('/--A--address3--B--/i',"$address3",$CFoutput);
		$CFoutput = preg_replace('/--A--city--B--/i',"$city",$CFoutput);
		$CFoutput = preg_replace('/--A--state--B--/i',"$state",$CFoutput);
		$CFoutput = preg_replace('/--A--province--B--/i',"$province",$CFoutput);
		$CFoutput = preg_replace('/--A--postal_code--B--/i',"$postal_code",$CFoutput);
		$CFoutput = preg_replace('/--A--country_code--B--/i',"$country_code",$CFoutput);
		$CFoutput = preg_replace('/--A--gender--B--/i',"$gender",$CFoutput);
		$CFoutput = preg_replace('/--A--date_of_birth--B--/i',"$date_of_birth",$CFoutput);
		$CFoutput = preg_replace('/--A--alt_phone--B--/i',"$alt_phone",$CFoutput);
		$CFoutput = preg_replace('/--A--email--B--/i',"$email",$CFoutput);
		$CFoutput = preg_replace('/--A--security_phrase--B--/i',"$security_phrase",$CFoutput);
		$CFoutput = preg_replace('/--A--comments--B--/i',"$comments",$CFoutput);
		$CFoutput = preg_replace('/--A--user--B--/i',"$user",$CFoutput);
		$CFoutput = preg_replace('/--A--pass--B--/i',"$pass",$CFoutput);
		$CFoutput = preg_replace('/--A--campaign--B--/i',"$campaign",$CFoutput);
		$CFoutput = preg_replace('/--A--server_ip--B--/i',"$server_ip",$CFoutput);
		$CFoutput = preg_replace('/--A--session_id--B--/i',"$session_id",$CFoutput);
		$CFoutput = preg_replace('/--A--dialed_number--B--/i',"$dialed_number",$CFoutput);
		$CFoutput = preg_replace('/--A--dialed_label--B--/i',"$dialed_label",$CFoutput);
		$CFoutput = preg_replace('/--A--source_id--B--/i',"$source_id",$CFoutput);
		$CFoutput = preg_replace('/--A--rank--B--/i',"$rank",$CFoutput);
		$CFoutput = preg_replace('/--A--owner--B--/i',"$owner",$CFoutput);
		$CFoutput = preg_replace('/--A--fullname--B--/i',"$fullname",$CFoutput);
		$CFoutput = preg_replace('/--A--uniqueid--B--/i',"$uniqueid",$CFoutput);
		$CFoutput = preg_replace('/--A--user_custom_one--B--/i',"$user_custom_one",$CFoutput);
		$CFoutput = preg_replace('/--A--user_custom_two--B--/i',"$user_custom_two",$CFoutput);
		$CFoutput = preg_replace('/--A--user_custom_three--B--/i',"$user_custom_three",$CFoutput);
		$CFoutput = preg_replace('/--A--user_custom_four--B--/i',"$user_custom_four",$CFoutput);
		$CFoutput = preg_replace('/--A--user_custom_five--B--/i',"$user_custom_five",$CFoutput);
		$CFoutput = preg_replace('/--A--preset_number_a--B--/i',"$preset_number_a",$CFoutput);
		$CFoutput = preg_replace('/--A--preset_number_b--B--/i',"$preset_number_b",$CFoutput);
		$CFoutput = preg_replace('/--A--preset_number_c--B--/i',"$preset_number_c",$CFoutput);
		$CFoutput = preg_replace('/--A--preset_number_d--B--/i',"$preset_number_d",$CFoutput);
		$CFoutput = preg_replace('/--A--preset_number_e--B--/i',"$preset_number_e",$CFoutput);
		$CFoutput = preg_replace('/--A--preset_dtmf_a--B--/i',"$preset_dtmf_a",$CFoutput);
		$CFoutput = preg_replace('/--A--preset_dtmf_b--B--/i',"$preset_dtmf_b",$CFoutput);
		$CFoutput = preg_replace('/--A--did_id--B--/i',"$did_id",$CFoutput);
		$CFoutput = preg_replace('/--A--did_extension--B--/i',"$did_extension",$CFoutput);
		$CFoutput = preg_replace('/--A--did_pattern--B--/i',"$did_pattern",$CFoutput);
		$CFoutput = preg_replace('/--A--did_description--B--/i',"$did_description",$CFoutput);
		$CFoutput = preg_replace('/--A--closecallid--B--/i',"$closecallid",$CFoutput);
		$CFoutput = preg_replace('/--A--xfercallid--B--/i',"$xfercallid",$CFoutput);
		$CFoutput = preg_replace('/--A--agent_log_id--B--/i',"$agent_log_id",$CFoutput);
		$CFoutput = preg_replace('/--A--call_id--B--/i',"$call_id",$CFoutput);
		$CFoutput = preg_replace('/--A--called_count--B--/i',"$called_count",$CFoutput);
		$CFoutput = preg_replace('/--A--did_custom_one--B--/i',"$did_custom_one",$CFoutput);
		$CFoutput = preg_replace('/--A--did_custom_two--B--/i',"$did_custom_two",$CFoutput);
		$CFoutput = preg_replace('/--A--did_custom_three--B--/i',"$did_custom_three",$CFoutput);
		$CFoutput = preg_replace('/--A--did_custom_four--B--/i',"$did_custom_four",$CFoutput);
		$CFoutput = preg_replace('/--A--did_custom_five--B--/i',"$did_custom_five",$CFoutput);

		$CFoutput = preg_replace('/--A--TABLEper_call_notes--B--/i',"$NOTESout",$CFoutput);

		# custom fields replacement
		$o=0;
		while ($fields_to_print > $o) 
			{
			$CFoutput = preg_replace("/--A--$A_field_label[$o]--B--/i","$A_field_value[$o]",$CFoutput);
			$o++;
			}

		if ($DB > 0) {echo "$CFoutput<BR>\n";}
		}
	##### END parsing for vicidial variables #####


	return $CFoutput;
	}
##### END custom_list_fields_values - gather values for display of custom list fields for a lead #####




##### LOOKUP GMT, FINDS THE CURRENT GMT OFFSET FOR A PHONE NUMBER #####

function lookup_gmt($phone_code,$USarea,$state,$LOCAL_GMT_OFF_STD,$Shour,$Smin,$Ssec,$Smon,$Smday,$Syear,$postalgmt,$postal_code)
	{
	require("dbconnect_mysqli.php");

	$postalgmt_found=0;
	if ( (preg_match("/POSTAL/i",$postalgmt)) && (strlen($postal_code)>4) )
		{
		if (preg_match('/^1$/', $phone_code))
			{
			$stmt="select postal_code,state,GMT_offset,DST,DST_range,country,country_code from vicidial_postal_codes where country_code='$phone_code' and postal_code LIKE \"$postal_code%\";";
			$rslt=mysql_to_mysqli($stmt, $link);
			$pc_recs = mysqli_num_rows($rslt);
			if ($pc_recs > 0)
				{
				$row=mysqli_fetch_row($rslt);
				$gmt_offset =	$row[2];	 $gmt_offset = preg_replace("/\+/i","",$gmt_offset);
				$dst =			$row[3];
				$dst_range =	$row[4];
				$PC_processed++;
				$postalgmt_found++;
				$post++;
				}
			}
		}
	if ($postalgmt_found < 1)
		{
		$PC_processed=0;
		### UNITED STATES ###
		if ($phone_code =='1')
			{
			$stmt="select country_code,country,areacode,state,GMT_offset,DST,DST_range,geographic_description from vicidial_phone_codes where country_code='$phone_code' and areacode='$USarea';";
			$rslt=mysql_to_mysqli($stmt, $link);
			$pc_recs = mysqli_num_rows($rslt);
			if ($pc_recs > 0)
				{
				$row=mysqli_fetch_row($rslt);
				$gmt_offset =	$row[4];	 $gmt_offset = preg_replace("/\+/i","",$gmt_offset);
				$dst =			$row[5];
				$dst_range =	$row[6];
				$PC_processed++;
				}
			}
		### MEXICO ###
		if ($phone_code =='52')
			{
			$stmt="select country_code,country,areacode,state,GMT_offset,DST,DST_range,geographic_description from vicidial_phone_codes where country_code='$phone_code' and areacode='$USarea';";
			$rslt=mysql_to_mysqli($stmt, $link);
			$pc_recs = mysqli_num_rows($rslt);
			if ($pc_recs > 0)
				{
				$row=mysqli_fetch_row($rslt);
				$gmt_offset =	$row[4];	 $gmt_offset = preg_replace("/\+/i","",$gmt_offset);
				$dst =			$row[5];
				$dst_range =	$row[6];
				$PC_processed++;
				}
			}
		### AUSTRALIA ###
		if ($phone_code =='61')
			{
			$stmt="select country_code,country,areacode,state,GMT_offset,DST,DST_range,geographic_description from vicidial_phone_codes where country_code='$phone_code' and state='$state';";
			$rslt=mysql_to_mysqli($stmt, $link);
			$pc_recs = mysqli_num_rows($rslt);
			if ($pc_recs > 0)
				{
				$row=mysqli_fetch_row($rslt);
				$gmt_offset =	$row[4];	 $gmt_offset = preg_replace("/\+/i","",$gmt_offset);
				$dst =			$row[5];
				$dst_range =	$row[6];
				$PC_processed++;
				}
			}
		### ALL OTHER COUNTRY CODES ###
		if (!$PC_processed)
			{
			$PC_processed++;
			$stmt="select country_code,country,areacode,state,GMT_offset,DST,DST_range,geographic_description from vicidial_phone_codes where country_code='$phone_code';";
			$rslt=mysql_to_mysqli($stmt, $link);
			$pc_recs = mysqli_num_rows($rslt);
			if ($pc_recs > 0)
				{
				$row=mysqli_fetch_row($rslt);
				$gmt_offset =	$row[4];	 $gmt_offset = preg_replace("/\+/i","",$gmt_offset);
				$dst =			$row[5];
				$dst_range =	$row[6];
				$PC_processed++;
				}
			}
		}

	### Find out if DST to raise the gmt offset ###
	$AC_GMT_diff = ($gmt_offset - $LOCAL_GMT_OFF_STD);
	$AC_localtime = mktime(($Shour + $AC_GMT_diff), $Smin, $Ssec, $Smon, $Smday, $Syear);
		$hour = date("H",$AC_localtime);
		$min = date("i",$AC_localtime);
		$sec = date("s",$AC_localtime);
		$mon = date("m",$AC_localtime);
		$mday = date("d",$AC_localtime);
		$wday = date("w",$AC_localtime);
		$year = date("Y",$AC_localtime);
	$dsec = ( ( ($hour * 3600) + ($min * 60) ) + $sec );

	$AC_processed=0;
	if ( (!$AC_processed) and ($dst_range == 'SSM-FSN') )
		{
		if ($DBX) {print "     "._QXZ("Second Sunday March to First Sunday November")."\n";}
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

			$USACAN_DST=0;
			$mm = $mon;
			$dd = $mday;
			$ns = $dsec;
			$dow= $wday;

			if ($mm < 3 || $mm > 11) {
			$USACAN_DST=0;   
			} elseif ($mm >= 4 and $mm <= 10) {
			$USACAN_DST=1;   
			} elseif ($mm == 3) {
			if ($dd > 13) {
				$USACAN_DST=1;   
			} elseif ($dd >= ($dow+8)) {
				if ($timezone) {
				if ($dow == 0 and $ns < (7200+$timezone*3600)) {
					$USACAN_DST=0;   
				} else {
					$USACAN_DST=1;   
				}
				} else {
				if ($dow == 0 and $ns < 7200) {
					$USACAN_DST=0;   
				} else {
					$USACAN_DST=1;   
				}
				}
			} else {
				$USACAN_DST=0;   
			}
			} elseif ($mm == 11) {
			if ($dd > 7) {
				$USACAN_DST=0;   
			} elseif ($dd < ($dow+1)) {
				$USACAN_DST=1;   
			} elseif ($dow == 0) {
				if ($timezone) { # UTC calculations
				if ($ns < (7200+($timezone-1)*3600)) {
					$USACAN_DST=1;   
				} else {
					$USACAN_DST=0;   
				}
				} else { # local time calculations
				if ($ns < 7200) {
					$USACAN_DST=1;   
				} else {
					$USACAN_DST=0;   
				}
				}
			} else {
				$USACAN_DST=0;   
			}
			} # end of month checks
		if ($DBX) {print "     DST: $USACAN_DST\n";}
		if ($USACAN_DST) {$gmt_offset++;}
		$AC_processed++;
		}

	if ( (!$AC_processed) and ($dst_range == 'FSA-LSO') )
		{
		if ($DBX) {print "     "._QXZ("First Sunday April to Last Sunday October")."\n";}
		#**********************************************************************
		# FSA-LSO
		#     This is returns 1 if Daylight Savings Time is in effect and 0 if 
		#       Standard time is in effect.
		#     Based on first Sunday in April and last Sunday in October at 2 am.
		#**********************************************************************
			
			$USA_DST=0;
			$mm = $mon;
			$dd = $mday;
			$ns = $dsec;
			$dow= $wday;

			if ($mm < 4 || $mm > 10) {
			$USA_DST=0;
			} elseif ($mm >= 5 and $mm <= 9) {
			$USA_DST=1;
			} elseif ($mm == 4) {
			if ($dd > 7) {
				$USA_DST=1;
			} elseif ($dd >= ($dow+1)) {
				if ($timezone) {
				if ($dow == 0 and $ns < (7200+$timezone*3600)) {
					$USA_DST=0;
				} else {
					$USA_DST=1;
				}
				} else {
				if ($dow == 0 and $ns < 7200) {
					$USA_DST=0;
				} else {
					$USA_DST=1;
				}
				}
			} else {
				$USA_DST=0;
			}
			} elseif ($mm == 10) {
			if ($dd < 25) {
				$USA_DST=1;
			} elseif ($dd < ($dow+25)) {
				$USA_DST=1;
			} elseif ($dow == 0) {
				if ($timezone) { # UTC calculations
				if ($ns < (7200+($timezone-1)*3600)) {
					$USA_DST=1;
				} else {
					$USA_DST=0;
				}
				} else { # local time calculations
				if ($ns < 7200) {
					$USA_DST=1;
				} else {
					$USA_DST=0;
				}
				}
			} else {
				$USA_DST=0;
			}
			} # end of month checks

		if ($DBX) {print "     DST: $USA_DST\n";}
		if ($USA_DST) {$gmt_offset++;}
		$AC_processed++;
		}

	if ( (!$AC_processed) and ($dst_range == 'LSM-LSO') )
		{
		if ($DBX) {print "     "._QXZ("Last Sunday March to Last Sunday October")."\n";}
		#**********************************************************************
		#     This is s 1 if Daylight Savings Time is in effect and 0 if 
		#       Standard time is in effect.
		#     Based on last Sunday in March and last Sunday in October at 1 am.
		#**********************************************************************
			
			$GBR_DST=0;
			$mm = $mon;
			$dd = $mday;
			$ns = $dsec;
			$dow= $wday;

			if ($mm < 3 || $mm > 10) {
			$GBR_DST=0;
			} elseif ($mm >= 4 and $mm <= 9) {
			$GBR_DST=1;
			} elseif ($mm == 3) {
			if ($dd < 25) {
				$GBR_DST=0;
			} elseif ($dd < ($dow+25)) {
				$GBR_DST=0;
			} elseif ($dow == 0) {
				if ($timezone) { # UTC calculations
				if ($ns < (3600+($timezone-1)*3600)) {
					$GBR_DST=0;
				} else {
					$GBR_DST=1;
				}
				} else { # local time calculations
				if ($ns < 3600) {
					$GBR_DST=0;
				} else {
					$GBR_DST=1;
				}
				}
			} else {
				$GBR_DST=1;
			}
			} elseif ($mm == 10) {
			if ($dd < 25) {
				$GBR_DST=1;
			} elseif ($dd < ($dow+25)) {
				$GBR_DST=1;
			} elseif ($dow == 0) {
				if ($timezone) { # UTC calculations
				if ($ns < (3600+($timezone-1)*3600)) {
					$GBR_DST=1;
				} else {
					$GBR_DST=0;
				}
				} else { # local time calculations
				if ($ns < 3600) {
					$GBR_DST=1;
				} else {
					$GBR_DST=0;
				}
				}
			} else {
				$GBR_DST=0;
			}
			} # end of month checks
			if ($DBX) {print "     DST: $GBR_DST\n";}
		if ($GBR_DST) {$gmt_offset++;}
		$AC_processed++;
		}
	if ( (!$AC_processed) and ($dst_range == 'LSO-LSM') )
		{
		if ($DBX) {print "     "._QXZ("Last Sunday October to Last Sunday March")."\n";}
		#**********************************************************************
		#     This is s 1 if Daylight Savings Time is in effect and 0 if 
		#       Standard time is in effect.
		#     Based on last Sunday in October and last Sunday in March at 1 am.
		#**********************************************************************
			
			$AUS_DST=0;
			$mm = $mon;
			$dd = $mday;
			$ns = $dsec;
			$dow= $wday;

			if ($mm < 3 || $mm > 10) {
			$AUS_DST=1;
			} elseif ($mm >= 4 and $mm <= 9) {
			$AUS_DST=0;
			} elseif ($mm == 3) {
			if ($dd < 25) {
				$AUS_DST=1;
			} elseif ($dd < ($dow+25)) {
				$AUS_DST=1;
			} elseif ($dow == 0) {
				if ($timezone) { # UTC calculations
				if ($ns < (3600+($timezone-1)*3600)) {
					$AUS_DST=1;
				} else {
					$AUS_DST=0;
				}
				} else { # local time calculations
				if ($ns < 3600) {
					$AUS_DST=1;
				} else {
					$AUS_DST=0;
				}
				}
			} else {
				$AUS_DST=0;
			}
			} elseif ($mm == 10) {
			if ($dd < 25) {
				$AUS_DST=0;
			} elseif ($dd < ($dow+25)) {
				$AUS_DST=0;
			} elseif ($dow == 0) {
				if ($timezone) { # UTC calculations
				if ($ns < (3600+($timezone-1)*3600)) {
					$AUS_DST=0;
				} else {
					$AUS_DST=1;
				}
				} else { # local time calculations
				if ($ns < 3600) {
					$AUS_DST=0;
				} else {
					$AUS_DST=1;
				}
				}
			} else {
				$AUS_DST=1;
			}
			} # end of month checks						
		if ($DBX) {print "     DST: $AUS_DST\n";}
		if ($AUS_DST) {$gmt_offset++;}
		$AC_processed++;
		}

	if ( (!$AC_processed) and ($dst_range == 'FSO-LSM') )
		{
		if ($DBX) {print "     "._QXZ("First Sunday October to Last Sunday March")."\n";}
		#**********************************************************************
		#   TASMANIA ONLY
		#     This is s 1 if Daylight Savings Time is in effect and 0 if 
		#       Standard time is in effect.
		#     Based on first Sunday in October and last Sunday in March at 1 am.
		#**********************************************************************
			
			$AUST_DST=0;
			$mm = $mon;
			$dd = $mday;
			$ns = $dsec;
			$dow= $wday;

			if ($mm < 3 || $mm > 10) {
			$AUST_DST=1;
			} elseif ($mm >= 4 and $mm <= 9) {
			$AUST_DST=0;
			} elseif ($mm == 3) {
			if ($dd < 25) {
				$AUST_DST=1;
			} elseif ($dd < ($dow+25)) {
				$AUST_DST=1;
			} elseif ($dow == 0) {
				if ($timezone) { # UTC calculations
				if ($ns < (3600+($timezone-1)*3600)) {
					$AUST_DST=1;
				} else {
					$AUST_DST=0;
				}
				} else { # local time calculations
				if ($ns < 3600) {
					$AUST_DST=1;
				} else {
					$AUST_DST=0;
				}
				}
			} else {
				$AUST_DST=0;
			}
			} elseif ($mm == 10) {
			if ($dd > 7) {
				$AUST_DST=1;
			} elseif ($dd >= ($dow+1)) {
				if ($timezone) {
				if ($dow == 0 and $ns < (7200+$timezone*3600)) {
					$AUST_DST=0;
				} else {
					$AUST_DST=1;
				}
				} else {
				if ($dow == 0 and $ns < 3600) {
					$AUST_DST=0;
				} else {
					$AUST_DST=1;
				}
				}
			} else {
				$AUST_DST=0;
			}
			} # end of month checks						
		if ($DBX) {print "     DST: $AUST_DST\n";}
		if ($AUST_DST) {$gmt_offset++;}
		$AC_processed++;
		}

	if ( (!$AC_processed) and ($dst_range == 'FSO-FSA') )
		{
		if ($DBX) {print "     "._QXZ("Sunday in October to First Sunday in April")."\n";}
		#**********************************************************************
		# FSO-FSA
		#   2008+ AUSTRALIA ONLY (country code 61)
		#     This is returns 1 if Daylight Savings Time is in effect and 0 if 
		#       Standard time is in effect.
		#     Based on first Sunday in October and first Sunday in April at 1 am.
		#**********************************************************************
		
		$AUSE_DST=0;
		$mm = $mon;
		$dd = $mday;
		$ns = $dsec;
		$dow= $wday;

		if ($mm < 4 or $mm > 10) {
		$AUSE_DST=1;   
		} elseif ($mm >= 5 and $mm <= 9) {
		$AUSE_DST=0;   
		} elseif ($mm == 4) {
		if ($dd > 7) {
			$AUSE_DST=0;   
		} elseif ($dd >= ($dow+1)) {
			if ($timezone) {
			if ($dow == 0 and $ns < (3600+$timezone*3600)) {
				$AUSE_DST=1;   
			} else {
				$AUSE_DST=0;   
			}
			} else {
			if ($dow == 0 and $ns < 7200) {
				$AUSE_DST=1;   
			} else {
				$AUSE_DST=0;   
			}
			}
		} else {
			$AUSE_DST=1;   
		}
		} elseif ($mm == 10) {
		if ($dd >= 8) {
			$AUSE_DST=1;   
		} elseif ($dd >= ($dow+1)) {
			if ($timezone) {
			if ($dow == 0 and $ns < (7200+$timezone*3600)) {
				$AUSE_DST=0;   
			} else {
				$AUSE_DST=1;   
			}
			} else {
			if ($dow == 0 and $ns < 3600) {
				$AUSE_DST=0;   
			} else {
				$AUSE_DST=1;   
			}
			}
		} else {
			$AUSE_DST=0;   
		}
		} # end of month checks
		if ($DBX) {print "     DST: $AUSE_DST\n";}
		if ($AUSE_DST) {$gmt_offset++;}
		$AC_processed++;
		}

	if ( (!$AC_processed) and ($dst_range == 'FSO-TSM') )
		{
		if ($DBX) {print "     "._QXZ("First Sunday October to Third Sunday March")."\n";}
		#**********************************************************************
		#     This is s 1 if Daylight Savings Time is in effect and 0 if 
		#       Standard time is in effect.
		#     Based on first Sunday in October and third Sunday in March at 1 am.
		#**********************************************************************
			
			$NZL_DST=0;
			$mm = $mon;
			$dd = $mday;
			$ns = $dsec;
			$dow= $wday;

			if ($mm < 3 || $mm > 10) {
			$NZL_DST=1;
			} elseif ($mm >= 4 and $mm <= 9) {
			$NZL_DST=0;
			} elseif ($mm == 3) {
			if ($dd < 14) {
				$NZL_DST=1;
			} elseif ($dd < ($dow+14)) {
				$NZL_DST=1;
			} elseif ($dow == 0) {
				if ($timezone) { # UTC calculations
				if ($ns < (3600+($timezone-1)*3600)) {
					$NZL_DST=1;
				} else {
					$NZL_DST=0;
				}
				} else { # local time calculations
				if ($ns < 3600) {
					$NZL_DST=1;
				} else {
					$NZL_DST=0;
				}
				}
			} else {
				$NZL_DST=0;
			}
			} elseif ($mm == 10) {
			if ($dd > 7) {
				$NZL_DST=1;
			} elseif ($dd >= ($dow+1)) {
				if ($timezone) {
				if ($dow == 0 and $ns < (7200+$timezone*3600)) {
					$NZL_DST=0;
				} else {
					$NZL_DST=1;
				}
				} else {
				if ($dow == 0 and $ns < 3600) {
					$NZL_DST=0;
				} else {
					$NZL_DST=1;
				}
				}
			} else {
				$NZL_DST=0;
			}
			} # end of month checks						
		if ($DBX) {print "     DST: $NZL_DST\n";}
		if ($NZL_DST) {$gmt_offset++;}
		$AC_processed++;
		}

	if ( (!$AC_processed) and ($dst_range == 'LSS-FSA') )
		{
		if ($DBX) {print "     "._QXZ("Last Sunday in September to First Sunday in April")."\n";}
		#**********************************************************************
		# LSS-FSA
		#   2007+ NEW ZEALAND (country code 64)
		#     This is returns 1 if Daylight Savings Time is in effect and 0 if 
		#       Standard time is in effect.
		#     Based on last Sunday in September and first Sunday in April at 1 am.
		#**********************************************************************
		
		$NZLN_DST=0;
		$mm = $mon;
		$dd = $mday;
		$ns = $dsec;
		$dow= $wday;

		if ($mm < 4 || $mm > 9) {
		$NZLN_DST=1;   
		} elseif ($mm >= 5 && $mm <= 9) {
		$NZLN_DST=0;   
		} elseif ($mm == 4) {
		if ($dd > 7) {
			$NZLN_DST=0;   
		} elseif ($dd >= ($dow+1)) {
			if ($timezone) {
			if ($dow == 0 && $ns < (3600+$timezone*3600)) {
				$NZLN_DST=1;   
			} else {
				$NZLN_DST=0;   
			}
			} else {
			if ($dow == 0 && $ns < 7200) {
				$NZLN_DST=1;   
			} else {
				$NZLN_DST=0;   
			}
			}
		} else {
			$NZLN_DST=1;   
		}
		} elseif ($mm == 9) {
		if ($dd < 25) {
			$NZLN_DST=0;   
		} elseif ($dd < ($dow+25)) {
			$NZLN_DST=0;   
		} elseif ($dow == 0) {
			if ($timezone) { # UTC calculations
			if ($ns < (3600+($timezone-1)*3600)) {
				$NZLN_DST=0;   
			} else {
				$NZLN_DST=1;   
			}
			} else { # local time calculations
			if ($ns < 3600) {
				$NZLN_DST=0;   
			} else {
				$NZLN_DST=1;   
			}
			}
		} else {
			$NZLN_DST=1;   
		}
		} # end of month checks
		if ($DBX) {print "     DST: $NZLN_DST\n";}
		if ($NZLN_DST) {$gmt_offset++;}
		$AC_processed++;
		}

	if ( (!$AC_processed) and ($dst_range == 'TSO-LSF') )
		{
		if ($DBX) {print "     "._QXZ("Third Sunday October to Last Sunday February")."\n";}
		#**********************************************************************
		# TSO-LSF
		#     This is returns 1 if Daylight Savings Time is in effect and 0 if 
		#       Standard time is in effect. Brazil
		#     Based on Third Sunday October to Last Sunday February at 1 am.
		#**********************************************************************
			
			$BZL_DST=0;
			$mm = $mon;
			$dd = $mday;
			$ns = $dsec;
			$dow= $wday;

			if ($mm < 2 || $mm > 10) {
			$BZL_DST=1;   
			} elseif ($mm >= 3 and $mm <= 9) {
			$BZL_DST=0;   
			} elseif ($mm == 2) {
			if ($dd < 22) {
				$BZL_DST=1;   
			} elseif ($dd < ($dow+22)) {
				$BZL_DST=1;   
			} elseif ($dow == 0) {
				if ($timezone) { # UTC calculations
				if ($ns < (3600+($timezone-1)*3600)) {
					$BZL_DST=1;   
				} else {
					$BZL_DST=0;   
				}
				} else { # local time calculations
				if ($ns < 3600) {
					$BZL_DST=1;   
				} else {
					$BZL_DST=0;   
				}
				}
			} else {
				$BZL_DST=0;   
			}
			} elseif ($mm == 10) {
			if ($dd < 22) {
				$BZL_DST=0;   
			} elseif ($dd < ($dow+22)) {
				$BZL_DST=0;   
			} elseif ($dow == 0) {
				if ($timezone) { # UTC calculations
				if ($ns < (3600+($timezone-1)*3600)) {
					$BZL_DST=0;   
				} else {
					$BZL_DST=1;   
				}
				} else { # local time calculations
				if ($ns < 3600) {
					$BZL_DST=0;   
				} else {
					$BZL_DST=1;   
				}
				}
			} else {
				$BZL_DST=1;   
			}
			} # end of month checks
		if ($DBX) {print "     DST: $BZL_DST\n";}
		if ($BZL_DST) {$gmt_offset++;}
		$AC_processed++;
		}

	if (!$AC_processed)
		{
		if ($DBX) {print "     "._QXZ("No DST Method Found")."\n";}
		if ($DBX) {print "     DST: 0\n";}
		$AC_processed++;
		}

	return $gmt_offset;
	}





##### DETERMINE IF LEAD IS DIALABLE #####
function dialable_gmt($DB,$link,$local_call_time,$gmt_offset,$state)
	{				
	require("dbconnect_mysqli.php");
	$dialable=0;

	$pzone=3600 * $gmt_offset;
	$pmin=(gmdate("i", time() + $pzone));
	$phour=( (gmdate("G", time() + $pzone)) * 100);
	$pday=gmdate("w", time() + $pzone);
	$tz = sprintf("%.2f", $p);	
	$GMT_gmt = "$tz";
	$GMT_day = "$pday";
	$GMT_hour = ($phour + $pmin);
	$YMD =  date("Y-m-d");	
	
	$stmt="SELECT call_time_id,call_time_name,call_time_comments,ct_default_start,ct_default_stop,ct_sunday_start,ct_sunday_stop,ct_monday_start,ct_monday_stop,ct_tuesday_start,ct_tuesday_stop,ct_wednesday_start,ct_wednesday_stop,ct_thursday_start,ct_thursday_stop,ct_friday_start,ct_friday_stop,ct_saturday_start,ct_saturday_stop,ct_state_call_times,ct_holidays FROM vicidial_call_times where call_time_id='$local_call_time';";
	if ($DB) {echo "$stmt\n";}
	$rslt=mysql_to_mysqli($stmt, $link);
	$rowx=mysqli_fetch_row($rslt);
	$Gct_default_start =	$rowx[3];
	$Gct_default_stop =		$rowx[4];
	$Gct_sunday_start =		$rowx[5];
	$Gct_sunday_stop =		$rowx[6];
	$Gct_monday_start =		$rowx[7];
	$Gct_monday_stop =		$rowx[8];
	$Gct_tuesday_start =	$rowx[9];
	$Gct_tuesday_stop =		$rowx[10];
	$Gct_wednesday_start =	$rowx[11];
	$Gct_wednesday_stop =	$rowx[12];
	$Gct_thursday_start =	$rowx[13];
	$Gct_thursday_stop =	$rowx[14];
	$Gct_friday_start =		$rowx[15];
	$Gct_friday_stop =		$rowx[16];
	$Gct_saturday_start =	$rowx[17];
	$Gct_saturday_stop =	$rowx[18];
	$Gct_state_call_times = $rowx[19];
	$Gct_holidays =			$rowx[20];

	### BEGIN Check for outbound holiday ###
	$holiday_id = '';
	if (strlen($Gct_holidays)>2)
		{
		$Gct_holidaysSQL = preg_replace("/\|/", "','", "$Gct_holidays");
		$Gct_holidaysSQL = "'".$Gct_holidaysSQL."'";
		
		$stmt = "SELECT holiday_id,holiday_date,holiday_name,ct_default_start,ct_default_stop from vicidial_call_time_holidays where holiday_id IN($Gct_holidaysSQL) and holiday_status='ACTIVE' and holiday_date='$YMD' order by holiday_id;";
		$rslt=mysql_to_mysqli($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$sthCrows=mysqli_num_rows($rslt);
		if ($sthCrows > 0)
			{
			$aryC=mysqli_fetch_row($rslt);
			$holiday_id =				$aryC[0];
			$holiday_date =				$aryC[1];
			$holiday_name =				$aryC[2];
			if ( ($Gct_default_start < $aryC[3]) && ($Gct_default_stop > 0) )		{$Gct_default_start = $aryC[3];}
			if ( ($Gct_default_stop > $aryC[4]) && ($Gct_default_stop > 0) )		{$Gct_default_stop = $aryC[4];}
			if ( ($Gct_sunday_start < $aryC[3]) && ($Gct_sunday_stop > 0) )			{$Gct_sunday_start = $aryC[3];}
			if ( ($Gct_sunday_stop > $aryC[4]) && ($Gct_sunday_stop > 0) )			{$Gct_sunday_stop = $aryC[4];}
			if ( ($Gct_monday_start < $aryC[3]) && ($Gct_monday_stop > 0) )			{$Gct_monday_start = $aryC[3];}
			if ( ($Gct_monday_stop >	$aryC[4]) && ($Gct_monday_stop > 0) )		{$Gct_monday_stop =	$aryC[4];}
			if ( ($Gct_tuesday_start < $aryC[3]) && ($Gct_tuesday_stop > 0) )		{$Gct_tuesday_start = $aryC[3];}
			if ( ($Gct_tuesday_stop > $aryC[4]) && ($Gct_tuesday_stop > 0) )		{$Gct_tuesday_stop = $aryC[4];}
			if ( ($Gct_wednesday_start < $aryC[3]) && ($Gct_wednesday_stop > 0) ) 	{$Gct_wednesday_start = $aryC[3];}
			if ( ($Gct_wednesday_stop > $aryC[4]) && ($Gct_wednesday_stop > 0) )	{$Gct_wednesday_stop = $aryC[4];}
			if ( ($Gct_thursday_start < $aryC[3]) && ($Gct_thursday_stop > 0) )		{$Gct_thursday_start = $aryC[3];}
			if ( ($Gct_thursday_stop > $aryC[4]) && ($Gct_thursday_stop > 0) )		{$Gct_thursday_stop = $aryC[4];}
			if ( ($Gct_friday_start < $aryC[3]) && ($Gct_friday_stop > 0) )			{$Gct_friday_start = $aryC[3];}
			if ( ($Gct_friday_stop > $aryC[4]) && ($Gct_friday_stop > 0) )			{$Gct_friday_stop = $aryC[4];}
			if ( ($Gct_saturday_start < $aryC[3]) && ($Gct_saturday_stop > 0) )		{$Gct_saturday_start = $aryC[3];}
			if ( ($Gct_saturday_stop > $aryC[4]) && ($Gct_saturday_stop > 0) )		{$Gct_saturday_stop = $aryC[4];}
			if ($DB) {echo "CALL TIME HOLIDAY FOUND!   $local_call_time|$holiday_id|$holiday_date|$holiday_name|$Gct_default_start|$Gct_default_stop|\n";}
			}
		}
	### END Check for outbound holiday ###
		if( $state != '') 
			{
			$ct_states = '';
			$ct_state_gmt_SQL = '';
			$ct_srs=0;
			$b=0;
			if (strlen($Gct_state_call_times)>2)
				{
				$state_rules = explode('|',$Gct_state_call_times);
				$ct_srs = ((count($state_rules)) - 2);
				}
				while($ct_srs >= $b)
					{
					if (strlen($state_rules[$b])>1)
						{
						$stmt = "SELECT state_call_time_id,state_call_time_state,state_call_time_name,state_call_time_comments,sct_default_start,sct_default_stop,sct_sunday_start,sct_sunday_stop,sct_monday_start,sct_monday_stop,sct_tuesday_start,sct_tuesday_stop,sct_wednesday_start,sct_wednesday_stop,sct_thursday_start,sct_thursday_stop,sct_friday_start,sct_friday_stop,sct_saturday_start,sct_saturday_stop,ct_holidays from vicidial_state_call_times where state_call_time_id='$state_rules[$b]';";
						$rslt=mysql_to_mysqli($stmt, $link);
						if ($DB) {echo "$stmt\n";}
						$sthCrows=mysqli_num_rows($rslt);
						if ($sthCrows > 0)
							{
							$aryC=mysqli_fetch_row($rslt);
							$Gstate_call_time_state =	$aryC[1];
							if ($Gstate_call_time_state == $state) 
								{
								$Gstate_call_time_id =		$aryC[0];
								$Gsct_default_start =		$aryC[4];
								$Gsct_default_stop =		$aryC[5];
								$Gsct_sunday_start =		$aryC[6];
								$Gsct_sunday_stop =			$aryC[7];
								$Gsct_monday_start =		$aryC[8];
								$Gsct_monday_stop =			$aryC[9];
								$Gsct_tuesday_start =		$aryC[10];
								$Gsct_tuesday_stop =		$aryC[11];
								$Gsct_wednesday_start =		$aryC[12];
								$Gsct_wednesday_stop =		$aryC[13];
								$Gsct_thursday_start =		$aryC[14];
								$Gsct_thursday_stop =		$aryC[15];
								$Gsct_friday_start =		$aryC[16];
								$Gsct_friday_stop =			$aryC[17];
								$Gsct_saturday_start =		$aryC[18];
								$Gsct_saturday_stop =		$aryC[19];
								$Sct_holidays =				$aryC[20];
								$ct_states .="'$Gstate_call_time_state',";
								
								### BEGIN Check for outbound state holiday ###
								$Sholiday_id = '';
								if ((strlen($Sct_holidays)>2) or ((strlen($holiday_id)>2) and (strlen($Sholiday_id)<2))) 
									{
									# Apply state holiday
									if (strlen($Sct_holidays)>2)
										{								
										$Sct_holidaysSQL = preg_replace("/\|/", "','", "$Sct_holidays");
										$Sct_holidaysSQL = "'".$Sct_holidaysSQL."'";
										$stmt = "SELECT holiday_id,holiday_date,holiday_name,ct_default_start,ct_default_stop from vicidial_call_time_holidays where holiday_id IN($Sct_holidaysSQL) and holiday_status='ACTIVE' and holiday_date='$YMD' order by holiday_id;";
										$holidaytype = "STATE CALL TIME HOLIDAY FOUND!   ";
										}
									# Apply call time wide holiday
									elseif ((strlen($holiday_id)>2) and (strlen($Sholiday_id)<2))
										{
										$stmt = "SELECT holiday_id,holiday_date,holiday_name,ct_default_start,ct_default_stop from vicidial_call_time_holidays where holiday_id='$holiday_id' and holiday_status='ACTIVE' and holiday_date='$YMD' order by holiday_id;";
										$holidaytype = "NO STATE HOLIDAY APPLYING CALL TIME HOLIDAY!   ";
										}				
									$rslt=mysql_to_mysqli($stmt, $link);
									if ($DB) {echo "$stmt\n";}
									$sthCrows=mysqli_num_rows($rslt);
									if ($sthCrows > 0)
										{
										$aryC=mysqli_fetch_row($rslt);
										$Sholiday_id =				$aryC[0];
										$Sholiday_date =			$aryC[1];
										$Sholiday_name =			$aryC[2];
										if ( ($Gsct_default_start < $aryC[3]) && ($Gsct_default_stop > 0) )		{$Gsct_default_start = $aryC[3];}
										if ( ($Gsct_default_stop > $aryC[4]) && ($Gsct_default_stop > 0) )		{$Gsct_default_stop = $aryC[4];}
										if ( ($Gsct_sunday_start < $aryC[3]) && ($Gsct_sunday_stop > 0) )		{$Gsct_sunday_start = $aryC[3];}
										if ( ($Gsct_sunday_stop > $aryC[4]) && ($Gsct_sunday_stop > 0) )		{$Gsct_sunday_stop = $aryC[4];}
										if ( ($Gsct_monday_start < $aryC[3]) && ($Gsct_monday_stop > 0) )		{$Gsct_monday_start = $aryC[3];}
										if ( ($Gsct_monday_stop > $aryC[4]) && ($Gsct_monday_stop > 0) )		{$Gsct_monday_stop = $aryC[4];}
										if ( ($Gsct_tuesday_start < $aryC[3]) && ($Gsct_tuesday_stop > 0) )		{$Gsct_tuesday_start = $aryC[3];}
										if ( ($Gsct_tuesday_stop > $aryC[4]) && ($Gsct_tuesday_stop > 0) )		{$Gsct_tuesday_stop = $aryC[4];}
										if ( ($Gsct_wednesday_start < $aryC[3]) && ($Gsct_wednesday_stop > 0) ) {$Gsct_wednesday_start = $aryC[3];}
										if ( ($Gsct_wednesday_stop > $aryC[4]) && ($Gsct_wednesday_stop > 0) )	{$Gsct_wednesday_stop = $aryC[4];}
										if ( ($Gsct_thursday_start < $aryC[3]) && ($Gsct_thursday_stop > 0) )	{$Gsct_thursday_start = $aryC[3];}
										if ( ($Gsct_thursday_stop > $aryC[4]) && ($Gsct_thursday_stop > 0) )	{$Gsct_thursday_stop = $aryC[4];}
										if ( ($Gsct_friday_start < $aryC[3]) && ($Gsct_friday_stop > 0) )		{$Gsct_friday_start = $aryC[3];}
										if ( ($Gsct_friday_stop > $aryC[4]) && ($Gsct_friday_stop > 0) )		{$Gsct_friday_stop = $aryC[4];}
										if ( ($Gsct_saturday_start < $aryC[3]) && ($Gsct_saturday_stop > 0) )	{$Gsct_saturday_start = $aryC[3];}
										if ( ($Gsct_saturday_stop > $aryC[4]) && ($Gsct_saturday_stop > 0) )	{$Gsct_saturday_stop = $aryC[4];}
										if ($DB) {echo "$holidaytype   |$Gstate_call_time_id|$Gstate_call_time_state|$Sholiday_id|$Sholiday_date|$Sholiday_name|$Gsct_default_start|$Gsct_default_stop|\n";}
										}
									}
								}
							}
						}
					$b++;
					}
				}
				### END Check for outbound state holiday ###
			if(strlen($Gstate_call_time_id)>2){
				# STATE RULES
				if ($GMT_day==0)	#### Sunday local time
					{
					if (($Gsct_sunday_start==0) && ($Gsct_sunday_stop==0))
						{
						if ( ($GMT_hour>=$Gsct_default_start) && ($GMT_hour<$Gsct_default_stop) )
							{$dialable=1;}
						}
					else
						{
						if ( ($GMT_hour>=$Gsct_sunday_start) && ($GMT_hour<$Gsct_sunday_stop) )
							{$dialable=1;}
						}
					}
				if ($GMT_day==1)	#### Monday local time
					{
					if (($Gsct_monday_start==0) && ($Gsct_monday_stop==0))
						{
						if ( ($GMT_hour>=$Gsct_default_start) && ($GMT_hour<$Gsct_default_stop) )
							{$dialable=1;}
						}
					else
						{
						if ( ($GMT_hour>=$Gsct_monday_start) && ($GMT_hour<$Gsct_monday_stop) )
							{$dialable=1;}
						}
					}
				if ($GMT_day==2)	#### Tuesday local time
					{
					if (($Gsct_tuesday_start==0) && ($Gsct_tuesday_stop==0))
						{
						if ( ($GMT_hour>=$Gsct_default_start) && ($GMT_hour<$Gsct_default_stop) )
							{$dialable=1;}
						}
					else
						{
						if ( ($GMT_hour>=$Gsct_tuesday_start) && ($GMT_hour<$Gsct_tuesday_stop) )
							{$dialable=1;}
						}
					}
				if ($GMT_day==3)	#### Wednesday local time
					{
					if (($Gsct_wednesday_start==0) && ($Gsct_wednesday_stop==0))
						{
						if ( ($GMT_hour>=$Gsct_default_start) && ($GMT_hour<$Gsct_default_stop) )
							{$dialable=1;}
						}
					else
						{
						if ( ($GMT_hour>=$Gsct_wednesday_start) && ($GMT_hour<$Gsct_wednesday_stop) )
							{$dialable=1;}
						}
					}
				if ($GMT_day==4)	#### Thursday local time
					{
					if (($Gsct_thursday_start==0) && ($Gsct_thursday_stop==0))
						{
						if ( ($GMT_hour>=$Gsct_default_start) && ($GMT_hour<$Gsct_default_stop) )
							{$dialable=1;}
						}
					else
						{
						if ( ($GMT_hour>=$Gsct_thursday_start) && ($GMT_hour<$Gsct_thursday_stop) )
							{$dialable=1;}
						}
					}
				if ($GMT_day==5)	#### Friday local time
					{
					if (($Gsct_friday_start==0) && ($Gsct_friday_stop==0))
						{
						if ( ($GMT_hour>=$Gsct_default_start) && ($GMT_hour<$Gsct_default_stop) )
							{$dialable=1;}
						}
					else
						{
						if ( ($GMT_hour>=$Gsct_friday_start) && ($GMT_hour<$Gsct_friday_stop) )
							{$dialable=1;}
						}
					}
				if ($GMT_day==6)	#### Saturday local time
					{
					if (($Gsct_saturday_start==0) && ($Gsct_saturday_stop==0))
						{
						if ( ($GMT_hour>=$Gsct_default_start) && ($GMT_hour<$Gsct_default_stop) )
							{$dialable=1;}
						}
					else
						{
						if ( ($GMT_hour>=$Gsct_saturday_start) && ($GMT_hour<$Gsct_saturday_stop) )
							{$dialable=1;}
						}
					}
		} else {		
				#NO STATE RULES
				if ($GMT_day==0)	#### Sunday local time
					{
					if (($Gct_sunday_start==0) and ($Gct_sunday_stop==0))
						{
						if ( ($GMT_hour>=$Gct_default_start) and ($GMT_hour<$Gct_default_stop) )
							{$dialable=1;}
						}
					else
						{
						if ( ($GMT_hour>=$Gct_sunday_start) and ($GMT_hour<$Gct_sunday_stop) )
							{$dialable=1;}
						}
					}
				if ($GMT_day==1)	#### Monday local time
					{
					if (($Gct_monday_start==0) and ($Gct_monday_stop==0))
						{
						if ( ($GMT_hour>=$Gct_default_start) and ($GMT_hour<$Gct_default_stop) )
							{$dialable=1;}
						}
					else
						{
						if ( ($GMT_hour>=$Gct_monday_start) and ($GMT_hour<$Gct_monday_stop) )
							{$dialable=1;}
						}
					}
				if ($GMT_day==2)	#### Tuesday local time
					{
					if (($Gct_tuesday_start==0) and ($Gct_tuesday_stop==0))
						{
						if ( ($GMT_hour>=$Gct_default_start) and ($GMT_hour<$Gct_default_stop) )
							{$dialable=1;}
						}
					else
						{
						if ( ($GMT_hour>=$Gct_tuesday_start) and ($GMT_hour<$Gct_tuesday_stop) )
							{$dialable=1;}
						}
					}
				if ($GMT_day==3)	#### Wednesday local time
					{
					if (($Gct_wednesday_start==0) and ($Gct_wednesday_stop==0))
						{
						if ( ($GMT_hour>=$Gct_default_start) and ($GMT_hour<$Gct_default_stop) )
							{$dialable=1;}
						}
					else
						{
						if ( ($GMT_hour>=$Gct_wednesday_start) and ($GMT_hour<$Gct_wednesday_stop) )
							{$dialable=1;}
						}
					}
				if ($GMT_day==4)	#### Thursday local time
					{
					if (($Gct_thursday_start==0) and ($Gct_thursday_stop==0))
						{
						if ( ($GMT_hour>=$Gct_default_start) and ($GMT_hour<$Gct_default_stop) )
							{$dialable=1;}
						}
					else
						{
						if ( ($GMT_hour>=$Gct_thursday_start) and ($GMT_hour<$Gct_thursday_stop) )
							{$dialable=1;}
						}
					}
				if ($GMT_day==5)	#### Friday local time
					{
					if (($Gct_friday_start==0) and ($Gct_friday_stop==0))
						{
						if ( ($GMT_hour>=$Gct_default_start) and ($GMT_hour<$Gct_default_stop) )
							{$dialable=1;}
						}
					else
						{
						if ( ($GMT_hour>=$Gct_friday_start) and ($GMT_hour<$Gct_friday_stop) )
							{$dialable=1;}
						}
					}
				if ($GMT_day==6)	#### Saturday local time
					{
					if (($Gct_saturday_start==0) and ($Gct_saturday_stop==0))
						{
						if ( ($GMT_hour>=$Gct_default_start) and ($GMT_hour<$Gct_default_stop) )
							{$dialable=1;}
						}
					else
						{
						if ( ($GMT_hour>=$Gct_saturday_start) and ($GMT_hour<$Gct_saturday_stop) )
							{$dialable=1;}
						}
					}
			}
	return $dialable;
	}



##### AJAX process logging #####
function vicidial_ajax_log($NOW_TIME,$startMS,$link,$ACTION,$php_script,$user,$stage,$lead_id,$session_name,$stmt)
	{
	$endMS = microtime();
	$startMSary = explode(" ",$startMS);
	$endMSary = explode(" ",$endMS);
	$runS = ($endMSary[0] - $startMSary[0]);
	$runM = ($endMSary[1] - $startMSary[1]);
	$TOTALrun = ($runS + $runM);

	$stmt = preg_replace('/;/', '', $stmt);
	$stmt = addslashes($stmt);

	$stmtA="INSERT INTO vicidial_ajax_log set user='$user',start_time='$NOW_TIME',db_time=NOW(),run_time='$TOTALrun',php_script='$php_script',action='$ACTION',lead_id='$lead_id',stage='$stage',session_name='$session_name',last_sql=\"$stmt\";";
	$rslt=mysql_to_mysqli($stmtA, $link);

#	$ajx = fopen ("./vicidial_ajax_log.txt", "a");
#	fwrite ($ajx, $stmtA . "\n");
#	fclose($ajx);

	return 1;
	}


##### MySQL Error Logging #####
function mysql_error_logging($NOW_TIME,$link,$mel,$stmt,$query_id,$user,$server_ip,$session_name,$one_mysql_log)
	{
	$NOW_TIME = date("Y-m-d H:i:s");
	#	mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00001',$user,$server_ip,$session_name,$one_mysql_log);
	$errno='';   $error='';
	if ( ($mel > 0) or ($one_mysql_log > 0) )
		{
		$errno = mysqli_errno($link);
		if ( ($errno > 0) or ($mel > 1) or ($one_mysql_log > 0) )
			{
			$error = mysqli_error($link);
			$efp = fopen ("./vicidial_mysqli_errors.txt", "a");
			fwrite ($efp, "$NOW_TIME|vdc_db_query|$query_id|$errno|$error|$stmt|$user|$server_ip|$session_name|\n");
			fclose($efp);
			}
		}
	$one_mysql_log=0;
	return $errno;
	}

function mysql_to_mysqli($stmt, $link) {
	$rslt=mysqli_query($link, $stmt);
	return $rslt;
}


# function to print/echo content, options for length, alignment and ordered internal variables are included
function _QXZ($English_text, $sprintf=0, $align="l", $v_one='', $v_two='', $v_three='', $v_four='', $v_five='', $v_six='', $v_seven='', $v_eight='', $v_nine='')
	{
	global $SSenable_languages, $SSlanguage_method, $VUselected_language, $link;

	if ($SSenable_languages == '1')
		{
		if ($SSlanguage_method != 'DISABLED')
			{
			if ( (strlen($VUselected_language) > 0) and ($VUselected_language != 'default English') )
				{
				if ($SSlanguage_method == 'MYSQL')
					{
					$stmt="SELECT translated_text from vicidial_language_phrases where english_text='$English_text' and language_id='$VUselected_language';";
					$rslt=mysql_to_mysqli($stmt, $link);
					$sl_ct = mysqli_num_rows($rslt);
					if ($sl_ct > 0)
						{
						$row=mysqli_fetch_row($rslt);
						$English_text =		$row[0];
						}
					}
				}
			}
		}

	if (preg_match("/%\ds/",$English_text))
		{
		$English_text = preg_replace("/%1s/", $v_one, $English_text);
		$English_text = preg_replace("/%2s/", $v_two, $English_text);
		$English_text = preg_replace("/%3s/", $v_three, $English_text);
		$English_text = preg_replace("/%4s/", $v_four, $English_text);
		$English_text = preg_replace("/%5s/", $v_five, $English_text);
		$English_text = preg_replace("/%6s/", $v_six, $English_text);
		$English_text = preg_replace("/%7s/", $v_seven, $English_text);
		$English_text = preg_replace("/%8s/", $v_eight, $English_text);
		$English_text = preg_replace("/%9s/", $v_nine, $English_text);
		}
	### uncomment to test output
	#	$English_text = str_repeat('*', strlen($English_text));
	#	$fp = fopen ("./QXZdebug.txt", "a");
	#	fwrite ($fp, "|$English_text\n");
	#	fclose($fp);

	if ($sprintf>0) 
		{
		if ($align=="r") 
			{
			$fmt="%".$sprintf."s";
			} 
		else 
			{
			$fmt="%-".$sprintf."s";
			}
		$English_text=sprintf($fmt, $English_text);
		}
	return $English_text;
	}

?>