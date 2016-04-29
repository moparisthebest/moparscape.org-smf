<?php
/**********************************************************************************
* sphinx_config.php                                                               *
***********************************************************************************
* SMF: Simple Machines Forum                                                      *
* Open-Source Project Inspired by Zef Hemel (zef@zefhemel.com)                    *
* =============================================================================== *
* Software Version:           SMF 2.0 RC4                                         *
* Software by:                Simple Machines (http://www.simplemachines.org)     *
* Copyright 2006-2010 by:     Simple Machines LLC (http://www.simplemachines.org) *
*           2001-2006 by:     Lewis Media (http://www.lewismedia.com)             *
* Support, News, Updates at:  http://www.simplemachines.org                       *
***********************************************************************************
* This program is free software; you may redistribute it and/or modify it under   *
* the terms of the provided license as published by Simple Machines LLC.          *
*                                                                                 *
* This program is distributed in the hope that it is and will be useful, but      *
* WITHOUT ANY WARRANTIES; without even any implied warranty of MERCHANTABILITY    *
* or FITNESS FOR A PARTICULAR PURPOSE.                                            *
*                                                                                 *
* See the "license.txt" file for details of the Simple Machines license.          *
* The latest version can always be found at http://www.simplemachines.org.        *
**********************************************************************************/

$smfsite = 'http://www.simplemachines.org/smf';
if (!file_exists(dirname(__FILE__) . '/SSI.php'))
	die('Please move this file to the main SMF directory and make sure SSI.php is part of that directory.');

$sphinx_ver = '0.9.9';

require(dirname(__FILE__) . '/SSI.php');

// Kick the guests.
is_not_guest();

// Kick the non-admin
if (!$user_info['is_admin'])
	die('You need admin permission to use this tool.');

if (!isset($_REQUEST['step']))
	step_0();
else
{
	$cur_step = 'step_' . (int) $_REQUEST['step'];
	$cur_step();
}

function step_0()
{
	global $txt;

	template_sphinx_config_above('Introduction');

	echo '
	<p>
		This configuration tool is designed to guide you through the installation of the Sphinx full-text search engine, specifically for Simple Machines Forum. Following the steps in this tool will tell how to install Sphinx, will configure SMF for using Sphinx, and will create a configuration file that will be needed for Sphinx based on SMF\'s settings. Make sure you have the latest version of this tool, so that the latest improvements have been implemented.
	</p>
	<h4>What is Sphinx?</h4>
	<p>
		Sphinx is an Open Source full-text search engine. It can index texts and find documents within fractions of seconds, a lot faster than MySQL. Sphinx consists of a few components:
	</p><p>
		There\'s the <em>indexer</em> that creates the full-text index from the existing tables in MySQL. The indexer is run as a cron job each time, allowing it to update the index once in a while. Based on the configuration file, the indexer knows how to connect to MySQL and which tables it needs to query.
	</p><p>
		Another important component is the search deamon (called <em>searchd</em>). This deamon runs as a process and awaits requests for information from the fulltext indexes. External processes, like the webserver, can send a query to it. The search deamon will then consult the index and return the result to the external process.
	</p>

	<h4>When should Sphinx be used for Simple Machines Forum?</h4>
	<p>
		Basically Sphinx starts to get interesting when MySQL is unable to do the job of indexing the messages properly. In most cases, a board needs to have at least 300,000 messages before that point has been reached. Also if you want to make sure the search queries don\'t affect the database performance, you can choose to put Sphinx on a different server than the database server.
	</p>

	<h4>Requirements for Sphinx</h4>
	<ul>
		<li>Root access to the server you\'re installing Sphinx</li>
		<li>Linux 2.4.x+ / Windows 2000/XP / FreeBSD 4.x+ / NetBSD 1.6 (this tool will assume Linux as operating system)</li>
		<li>A working C++ compiler</li>
		<li>A good make program</li>
	</ul>
	<form action="' . $_SERVER['PHP_SELF'] . '?step=1" method="post">
		<div style="margin: 1ex; text-align: ', empty($txt['lang_rtl']) ? 'right' : 'left', ';">
			<input type="submit" value="Proceed" />
		</div>
	</form>
	';

	template_sphinx_config_below();
}

function step_1()
{
	global $sphinx_ver, $txt;

	template_sphinx_config_above('Installing Sphinx');

	echo '
	<p>
		This tool will assume you will be installing Sphinx version ', $sphinx_ver, '. A newer version might be available and, if so, would probably be better. Just understand that the steps below and the working of the search engine might be different in future versions of Sphinx. Please note that Sphinx versions prior to 0.9.9 will not work properly.
	</p>
	<h4>Retrieving and unpacking the package</h4>

	Grab the file from the Sphinx website:<br />
	<tt>[~]#  wget http://www.sphinxsearch.com/downloads/sphinx-', $sphinx_ver, '.tar.gz</tt><br />
	<br />
	Untar the package:<br />
	<tt>[~]#  tar -xzvf sphinx-', $sphinx_ver, '.tar.gz</tt><br />
	<br />
	Go to the Sphinx directory:<br />
	<tt>[~]#  cd sphinx-', $sphinx_ver, '</tt>

	<h4>Compiling Sphinx</h4>
	Configure Sphinx (generally no options are needed):<br />
	<tt>[~]#  ./configure</tt><br />
	<br />
	If everything went well, run the make tool:<br />
	<tt>[~]#  make</tt><br />
	<br />
	If that went well too, make the install:<br />
	<tt>[~]#  make install</tt><br />

	<form action="' . $_SERVER['PHP_SELF'] . '?step=2" method="post">
		<div style="margin: 1ex; text-align: ', empty($txt['lang_rtl']) ? 'right' : 'left', ';">
			<input type="submit" value="Proceed" />
		</div>
	</form>';
}


function step_2()
{
	global $context, $modSettings, $txt;

	template_sphinx_config_above('Configure SMF for Sphinx');

	echo '
		A few settings can be configured allowing to customize the search engine. Generally all options can be left untouched.<br />
		<br />
		<form action="' . $_SERVER['PHP_SELF'] . '?step=3" method="post">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 2ex;">
				<tr>
					<td width="20%" valign="top" class="textbox"><label for="sphinx_data_path_input">Index data path:</label></td>
					<td>
						<input type="text" name="sphinx_data_path" id="sphinx_data_path_input" value="', isset($modSettings['sphinx_data_path']) ? $modSettings['sphinx_data_path'] : '/var/sphinx/data', '" size="65" />
						<div style="font-size: smaller; margin-bottom: 2ex;">This is the path that will be containing the search index files used by Sphinx.</div>
					</td>
				</tr><tr>
					<td width="20%" valign="top" class="textbox"><label for="sphinx_log_path_input">Log path:</label></td>
					<td>
						<input type="text" name="sphinx_log_path" id="sphinx_log_path_input" value="', isset($modSettings['sphinx_log_path']) ? $modSettings['sphinx_log_path'] : '/var/sphinx/log', '" size="65" />
						<div style="font-size: smaller; margin-bottom: 2ex;">Server path that will contain the log files created by Sphinx.</div>
					</td>
				</tr><tr>
					<td width="20%" valign="top" class="textbox"><label for="sphinx_stopword_path_input">Stopword path:</label></td>
					<td>
						<input type="text" name="sphinx_stopword_path" id="sphinx_stopword_path_input" value="', isset($modSettings['sphinx_stopword_path']) ? $modSettings['sphinx_stopword_path'] : '', '" size="65" />
						<div style="font-size: smaller; margin-bottom: 2ex;">The server path to the stopword list (leave empty for no stopword list).</div>
					</td>
				</tr><tr>
					<td width="20%" valign="top" class="textbox"><label for="sphinx_indexer_mem_input">Memory limit indexer:</label></td>
					<td>
						<input type="text" name="sphinx_indexer_mem" id="sphinx_indexer_mem_input" value="', isset($modSettings['sphinx_indexer_mem']) ? $modSettings['sphinx_indexer_mem'] : '32', '" size="4" /> MB
						<div style="font-size: smaller; margin-bottom: 2ex;">The maximum amount of (RAM) memory the indexer is allowed to be using.</div>
					</td>
				</tr><tr>
					<td width="20%" valign="top" class="textbox"><label for="sphinx_searchd_server_input">Sphinx server:</label></td>
					<td>
						<input type="text" name="sphinx_searchd_server" id="sphinx_searchd_server_input" value="', isset($modSettings['sphinx_searchd_server']) ? $modSettings['sphinx_searchd_server'] : 'localhost', '" size="65" />
						<div style="font-size: smaller; margin-bottom: 2ex;">Server the Sphinx search deamon resides on.</div>
					</td>
				</tr><tr>
					<td width="20%" valign="top" class="textbox"><label for="sphinx_searchd_port_input">Sphinx port:</label></td>
					<td>
						<input type="text" name="sphinx_searchd_port" id="sphinx_searchd_port_input" value="', isset($modSettings['sphinx_searchd_port']) ? $modSettings['sphinx_searchd_port'] : '9312', '" size="4" />
						<div style="font-size: smaller; margin-bottom: 2ex;">Port on which the search deamon will listen.</div>
					</td>
				</tr><tr>
					<td width="20%" valign="top" class="textbox"><label for="sphinxql_searchd_port_input">SphinxQL port:</label></td>
					<td>
						<input type="text" name="sphinxql_searchd_port" id="sphinxql_searchd_port_input" value="', isset($modSettings['sphinxql_searchd_port']) ? $modSettings['sphinxql_searchd_port'] : '9306', '" size="4" />
						<div style="font-size: smaller; margin-bottom: 2ex;">Port on which the MySQL protocol search deamon will listen.</div>
					</td>
				</tr><tr>
					<td width="20%" valign="top" class="textbox"><label for="sphinx_max_results_input">Maximum # matches:</label></td>
					<td>
						<input type="text" name="sphinx_max_results" id="sphinx_max_results_input" value="', isset($modSettings['sphinx_max_results']) ? $modSettings['sphinx_max_results'] : '1000', '" size="4" />
						<div style="font-size: smaller; margin-bottom: 2ex;">Maximum amount of matches the search deamon will return.</div>
					</td>
				</tr>
			</table>
			<div style="margin: 1ex; text-align: ', empty($txt['lang_rtl']) ? 'right' : 'left', ';">
				<input type="submit" value="Proceed" />
				<input type="hidden" name="sc" value="', $context['session_id'], '" />
			</div>
		</form>';

	template_sphinx_config_below();
}

function step_3()
{
	global $context, $modSettings, $txt;

	checkSession();

	updateSettings(array(
		'sphinx_data_path' => rtrim($_POST['sphinx_data_path'], '/'),
		'sphinx_log_path' => rtrim($_POST['sphinx_log_path'], '/'),
		'sphinx_stopword_path' => $_POST['sphinx_stopword_path'],
		'sphinx_indexer_mem' => (int) $_POST['sphinx_indexer_mem'],
		'sphinx_searchd_server' => $_POST['sphinx_searchd_server'],
		'sphinx_searchd_port' => (int) $_POST['sphinx_searchd_port'],
		'sphinxql_searchd_port' => (int) $_POST['sphinxql_searchd_port'],
		'sphinx_max_results' => (int) $_POST['sphinx_max_results'],
	));

	if (!isset($modSettings['sphinx_indexed_msg_until']))
		updateSettings(array(
			'sphinx_indexed_msg_until' => '1',
		));


	template_sphinx_config_above('Configure SMF for Sphinx');
	echo '
		Your configuration has been saved successfully. The next time you run this tool, your configuration will automatically be loaded.
		<h4>Generating a configuration file</h4>
		Based on the settings you submitted in the previous screen, this tool can generate a configuration file for you that will be used by Sphinx. Press the button below to generate the configuration file, and upload it to /usr/local/etc/sphinx.conf (default configuration).<br />
		<br />
		<form action="' . $_SERVER['PHP_SELF'] . '?step=999" method="post" target="_blank">
			<input type="submit" value="Generate sphinx.conf" />
			<input type="hidden" name="sc" value="', $context['session_id'], '" />
		</form><br />

		<h4>Some file actions</h4>
		Create directories for storing the indexes:<br />', strpos($modSettings['sphinx_data_path'], '/var/sphinx/') === false ? '' : '
		<tt>[~]#  mkdir /var/sphinx</tt><br />', '
		<tt>[~]#  mkdir ' . $modSettings['sphinx_data_path'] . '</tt><br />
		<tt>[~]#  mkdir ' . $modSettings['sphinx_log_path'] . '</tt><br />
		<br />
		Make the data and log directories writable:<br />
		<tt>[~]#  chmod 666 ' . $modSettings['sphinx_data_path'] . '</tt><br />
		<tt>[~]#  chmod 666 ' . $modSettings['sphinx_log_path'] . '</tt><br />

		<h4>Indexing time!</h4>
		It\'s time to create the full-text index:<br />
		<tt>[~]#  indexer --config /usr/local/etc/sphinx.conf --all</tt><br />
		<br />
		If that went successful, we can test run the search deamon. Start it by typing:<br />
		<tt>[~]#  searchd --config /usr/local/etc/sphinx.conf</tt><br />
		<br />
		If everything worked so far, congratulations, Sphinx has been installed and works! Next step is modifying SMF\'s search to work with Sphinx.

		<h4>Configuring SMF</h4>
		Upload the SearchAPI-Sphinxql.php file to the \'Sources\' directory.<br /><br />
		Select \'Sphinx\' as database index below and press \'Change Search Index\'. Test your search function afterwards, it should work now!<br />
		<br />
		<form action="' . $_SERVER['PHP_SELF'] . '?step=888" method="post" target="_blank">
			<select name="search_index">
				<option value=""', empty($modSettings['search_index']) ? ' selected="selected"' : '', '>(None)</option>
				<option value="fulltext"', !empty($modSettings['search_index']) && $modSettings['search_index'] === 'fulltext' ? ' selected="selected"' : '', '>Fulltext</option>
				<option value="custom"', !empty($modSettings['search_index']) && $modSettings['search_index'] === 'custom' ? ' selected="selected"' : '', '>Custom index</option>
				<option value="sphinx"', !empty($modSettings['search_index']) && $modSettings['search_index'] === 'sphinx' ? ' selected="selected"' : '', '>Sphinx</option>
				<option value="sphinxql"', !empty($modSettings['search_index']) && $modSettings['search_index'] === 'sphinxql' ? ' selected="selected"' : '', '>SphinxQL</option>
			</select>
			<input type="submit" value="Change Search Index" />
			<input type="hidden" name="sc" value="', $context['session_id'], '" />
		</form><br />
		<br />

		<h4>Creating a cron job for the indexer</h4>
		In order to keep the full-text index up to date, you need to add a cron job that will update the index from time to time. The configuration file defines two indexes: <tt>smf_delta_index</tt>, an index that only stores the recent changes and can be called frequently.  <tt>smf_base_index</tt>, an index that stores the full database and should be called less frequently.

		Adding the following lines to /etc/crontab would let the index rebuild every day (at 3 am) and update the most recently changed messages each hour:<br />
		<tt># search indexer<br />
		10 3 * * * /usr/local/bin/indexer --config /usr/local/etc/sphinx.conf --rotate smf_base_index<br />
		0 * * * * /usr/local/bin/indexer --config /usr/local/etc/sphinx.conf --rotate smf_delta_index</tt><br />


		';
	template_sphinx_config_below();
}

function step_888()
{
	global $modSettings;

	checkSession();

	if (in_array($_REQUEST['search_index'], array('', 'fulltext', 'custom', 'sphinx', 'sphinxql')))
		updateSettings(array(
			'search_index' => $_REQUEST['search_index'],
		));

	echo 'Setting has been saved. This window can be closed.';
}



function step_999()
{
	global $context, $db_server, $db_name, $db_user, $db_passwd, $db_prefix;
	global $db_character_set, $modSettings;

	$humungousTopicPosts = 200;

	ob_end_clean();
	header('Pragma: ');
	if (!$context['browser']['is_gecko'])
		header('Content-Transfer-Encoding: binary');
	header('Connection: close');
	header('Content-Disposition: attachment; filename="sphinx.conf"');
	header('Content-Type: application/octet-stream');

	$weight_factors = array(
		'age',
		'length',
		'first_message',
		'sticky',
	);
	$weight = array();
	$weight_total = 0;
	foreach ($weight_factors as $weight_factor)
	{
		$weight[$weight_factor] = empty($modSettings['search_weight_' . $weight_factor]) ? 0 : (int) $modSettings['search_weight_' . $weight_factor];
		$weight_total += $weight[$weight_factor];
	}

	if ($weight_total === 0)
	{
		$weight = array(
			'age' => 25,
			'length' => 25,
			'first_message' => 25,
			'sticky' => 25,
		);
		$weight_total = 100;
	}


	echo '#
# Sphinx configuration file (sphinx.conf), configured for SMF 1.1
#
# By default the location of this file would probably be:
# /usr/local/etc/sphinx.conf

source smf_source
{
	type 		= mysql
	sql_host 	= ', $db_server, '
	sql_user 	= ', $db_user, '
	sql_pass 	= ', $db_passwd, '
	sql_db 		= ', $db_name, '
	sql_port 	= 3306', empty($db_character_set) ? '' : '
	sql_query_pre = SET NAMES ' . $db_character_set, '
	sql_query_pre =	\
		REPLACE INTO ', $db_prefix, 'settings (variable, value) \
		SELECT \'sphinx_indexed_msg_until\', MAX(id_msg) \
		FROM ', $db_prefix, 'messages
	sql_query_range = \
		SELECT 1, value \
		FROM ', $db_prefix, 'settings \
		WHERE variable = \'sphinx_indexed_msg_until\'
	sql_range_step = 1000
	sql_query =	\
		SELECT \
			m.id_msg, m.id_topic, m.id_board, IF(m.id_member = 0, 4294967295, m.id_member) AS id_member, m.poster_time, m.body, m.subject, \
			t.num_replies + 1 AS num_replies, CEILING(1000000 * ( \
				IF(m.id_msg < 0.7 * s.value, 0, (m.id_msg - 0.7 * s.value) / (0.3 * s.value)) * ' . $weight['age'] . ' + \
				IF(t.num_replies < 200, t.num_replies / 200, 1) * ' . $weight['length'] . ' + \
				IF(m.id_msg = t.id_first_msg, 1, 0) * ' . $weight['first_message'] . ' + \
				IF(t.is_sticky = 0, 0, 1) * ' . $weight['sticky'] . ' \
			) / ' . $weight_total . ') AS relevance \
		FROM ', $db_prefix, 'messages AS m, ', $db_prefix, 'topics AS t, ', $db_prefix, 'settings AS s \
		WHERE t.id_topic = m.id_topic \
			AND s.variable = \'maxMsgID\' \
			AND m.id_msg BETWEEN $start AND $end
	sql_attr_uint = id_topic
	sql_attr_uint = id_board
	sql_attr_uint = id_member
	sql_attr_timestamp = poster_time
	sql_attr_timestamp = relevance
	sql_attr_timestamp = num_replies
	sql_query_info = \
		SELECT * \
		FROM ', $db_prefix, 'messages \
		WHERE id_msg = $id
}

source smf_delta_source : smf_source
{
	sql_query_pre = ', isset($db_character_set) ? 'SET NAMES ' . $db_character_set : '', '
	sql_query_range = \
		SELECT s1.value, s2.value \
		FROM ', $db_prefix, 'settings AS s1, ', $db_prefix, 'settings AS s2 \
		WHERE s1.variable = \'sphinx_indexed_msg_until\' \
			AND s2.variable = \'maxMsgID\'
}

index smf_base_index
{
	html_strip 		= 1
	source 			= smf_source
	path 			= ', $modSettings['sphinx_data_path'], '/smf_sphinx_base.index', empty($modSettings['sphinx_stopword_path']) ? '' : '
	stopwords 		= ' . $modSettings['sphinx_stopword_path'], '
	min_word_len 	= 2
	charset_type 	= ', isset($db_character_set) && $db_character_set === 'utf8' ? 'utf-8' : 'sbcs', '
	charset_table 	= 0..9, A..Z->a..z, _, a..z
}

index smf_delta_index : smf_base_index
{
	source 			= smf_delta_source
	path 			= ', $modSettings['sphinx_data_path'], '/smf_sphinx_delta.index
}

index smf_index
{
	type			= distributed
	local			= smf_base_index
	local			= smf_delta_index
}

indexer
{
	mem_limit 		= ', (int) $modSettings['sphinx_indexer_mem'], 'M
}

searchd
{
	listen 			= ', (int) $modSettings['sphinx_searchd_port'], '
	listen 			= ', (int) $modSettings['sphinxql_searchd_port'], ':mysql41
	log 			= ', $modSettings['sphinx_log_path'], '/searchd.log
	query_log 		= ', $modSettings['sphinx_log_path'], '/query.log
	read_timeout 	= 5
	max_children 	= 30
	pid_file 		= ', $modSettings['sphinx_data_path'], '/searchd.pid
	max_matches 	= 1000
}
';

	flush();
}





function template_sphinx_config_above($title)
{
	global $smfsite, $settings;

	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title>SMF Sphinx Configuration Utility</title>
		<script language="JavaScript" type="text/javascript" src="', $settings['default_theme_url'], '/script.js?fin21"></script>
		<link rel="stylesheet" type="text/css" href="', $smfsite, '/style.css" />
	</head>
	<body>
		<div id="header">
			<a href="http://www.simplemachines.org/" target="_blank"><img src="', $smfsite, '/smflogo.gif" style=" float: right;" alt="Simple Machines" border="0" /></a>
			<div title="Building the pyramids with Simple Machines">SMF Sphinx Configuration Utility</div>
		</div>
		<div id="content">
			<table width="100%" border="0" cellpadding="0" cellspacing="0" style="padding-top: 1ex;">
			<tr>
				<td width="100%" valign="top">
					<div class="panel">
						<h2>', $title, '</h2>';
}

function template_sphinx_config_below()
{

	echo '
					</div>
				</td>
			</tr>
		</table>
		</div>
	</body>
</html>';
}

?>