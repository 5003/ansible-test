<?php
	/*
		About: Automate deletion of TeamSpeak 3 servers after set time
		Author: Sebastian Kraetzig <info@ts3-tools.info>
		Project: www.ts3-tools.info
		facebook: www.facebook.com/TS3Tools

		License: GNU GPLv3

		Donations: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7ZRXLSC2UBVWE
	*/
	
	/*
		You need to download the ts3admin.class and put it in the same directory as this script: http://ts3admin.info/
		The owner and developer of ts3admin.class is still Stefan Z.
	*/
	require_once("ts3admin.class.php");
	
	/****************************
		Configuration
	****************************/

	/*
		IP address of TeamSpeak 3 instance
		Example: $instance['IP'] = '192.168.2.10';
	*/
	$instance['ip'] = "{{ inventory_hostname }}";

	/*
		ServerQuery port of TeamSpeak 3 instance
		Example: $instance['serverQueryPort'] = 10011;
	*/
	$instance['serverQueryPort'] = 10011;

	/*
		ServerQuery login name, which is able to run the commands 'serverlist', 'serverinfo', 'serverstop' and 'serverdelete'
		Example: $instance['user'] = 'serveradmin';
	*/
	$instance['user'] = "serveradmin";

	/*
		Password for ServerQuery login
		Example: $instance['password'] = 'very$Secret!Password2014';
	*/
	$instance['password'] = '{{ teamspeak_3.server.password }}';

	/*
		Maximum availability type
		Example: $uptime['type'] = "hours";
		Possible values: days, hours, minutes, seconds
	*/
	$uptime['type'] = "hours";

	/*
		Maximum availability of virtual server
		Example: $uptime['duration'] = 5;
	*/
	$uptime['duration'] = 5;
	
	/*
		Exceptions: Set SIDs are ignored by the script
		Example: $ignoreSIDs = array(1, 22, 231, 450, 54);
	*/
	$ignore['SIDs'] = array();
	
	/*
		Exceptions: Set ports are ignored by the script
		Example: $ignorePorts = array(9987, 9988, 9993);
	*/
	$ignore['ports'] = array();
	
	/****************************
		Configuration END
	****************************/

	/***************************
	*						   *
	* DO NOT CHANGE FOLLOWING! *
	*						   *
	***************************/

	/* Fill empty arrays with 0 */
	if(empty($ignore['SIDs'])) {
		$ignore['SIDs'] = array(0);
	}
	if(empty($ignore['ports'])) {
		$ignore['ports'] = array(0);
	}

	$ts3admin = new ts3admin($instance['ip'], $instance['serverQueryPort']);

	if($ts3admin->getElement('success', $ts3admin->connect())) {
		$ts3admin->login($instance['user'], $instance['password']);

		/* Fetch list of virtual servers */
		$serverList = $ts3admin->serverList();

		if($serverList['success']) {
			/* Foreach virtual server */
			foreach($serverList['data'] as $key => $value) {
				/* Is virtual server in any $ignore-list? */
				if(!in_array($value['virtualserver_id'], $ignore['SIDs']) AND (!in_array($value['virtualserver_port'], $ignore['ports']))) {
					/* Select Server-ID */
					$ts3admin->selectServer($value['virtualserver_id'], 'serverId');

					/* Fetch serverInfo() */
					$serverInfo = $ts3admin->serverInfo();

					/* Current time - Creation time of virtual server */
					$uptimeTimestamp = time() - $serverInfo['data']['virtualserver_created'];

					/* Doc: http://ts3admin.info/manual/classes/ts3admin.html#method_convertSecondsToArrayTime */
					$uptimeTS = $ts3admin->convertSecondsToArrayTime($uptimeTimestamp);

					/* If virtual server availability equal or higher than set maximum availability */
					if($uptimeTS[$uptime['type']] >= $uptime['duration']) {
						/* Virtual server exists longer than set time */
						if($serverInfo['data']['virtualserver_status'] == "online") {
							if($ts3admin->serverStop($value['virtualserver_id'])) {
								if($ts3admin->serverDelete($value['virtualserver_id'])) {
									$deleteTeamSpeakServerMsg = 'The TeamSpeak server <b>' . $value['virtualserver_name'] . '</b> (SID ' . $value['virtualserver_id'] . ', Port ' . $value['virtualserver_port'] . ') was stopped and deleted successful!';
								} else {
									$deleteTeamSpeakServerMsg = 'Could not delete TeamSpeak server <b>' . $value['virtualserver_name'] . '</b> (SID ' . $value['virtualserver_id'] . ', Port ' . $value['virtualserver_port'] . ')!';
								}
							} else {
								$deleteTeamSpeakServerMsg = 'Could not stop and delete TeamSpeak server <b>' . $value['virtualserver_name'] . '</b> (SID ' . $value['virtualserver_id'] . ', Port ' . $value['virtualserver_port'] . ')!';
							}
						} else {
							if($ts3admin->serverDelete($value['virtualserver_id'])) {
								$deleteTeamSpeakServerMsg = 'The TeamSpeak server <b>' . $value['virtualserver_name'] . '</b> (SID ' . $value['virtualserver_id'] . ', Port ' . $value['virtualserver_port'] . ') was stopped and deleted successful!';
							} else {
								$deleteTeamSpeakServerMsg = 'Could not delete TeamSpeak server <b>' . $value['virtualserver_name'] . '</b> (SID ' . $value['virtualserver_id'] . ', Port ' . $value['virtualserver_port'] . ')!';
							}
						}
					} else {
						/* Virtual server is younger than set time */
						$deleteTeamSpeakServerMsg = 'Nothing happened with TeamSpeak server <b>' . $value['virtualserver_name'] . '</b> (SID ' . $value['virtualserver_id'] . ', Port ' . $value['virtualserver_port'] . ').';
					}
				} else {
					$deleteTeamSpeakServerMsg = 'Nothing happened with TeamSpeak server <b>' . $value['virtualserver_name'] . '</b> (SID ' . $value['virtualserver_id'] . ', Port ' . $value['virtualserver_port'] . ').';
				}
				/* Return status message foreach virtual server */
				echo $deleteTeamSpeakServerMsg;
			}
		} else {
			echo 'No virtual server exists on this instance! Nothing happened.';
		}
	}
	else
	{
		echo 'Could not connect to your TeamSpeak 3 server instance. Please check your configuration data!';
	}
?>
