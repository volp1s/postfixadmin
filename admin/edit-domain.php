<?php
// 
// Postfix Admin 
// by Mischa Peters <mischa at high5 dot net>
// Copyright (c) 2002 - 2005 High5!
// Licensed under GPL for more info check GPL-LICENSE.TXT
//
// File: edit-domain.php
//
// Template File: admin_edit-domain.tpl
//
// Template Variables:
//
// tDescription
// tAliases
// tMailboxes
// tMaxquota
// tActive
//
// Form POST \ GET Variables:
//
// fDescription
// fAliases
// fMailboxes
// fMaxquota
// fActive
//
require ("../variables.inc.php");
require ("../config.inc.php");
require ("../functions.inc.php");
include ("../languages/" . check_language () . ".lang");

$SESSID_USERNAME = check_session ();
(!check_admin($SESSID_USERNAME) ? header("Location: " . $CONF['postfix_admin_url'] . "/main.php") && exit : '1');

if ($_SERVER['REQUEST_METHOD'] == "GET")
{
   if (isset ($_GET['domain']))
   {
      $domain = escape_string ($_GET['domain']);
      $domain_properties = get_domain_properties ($domain);

      $tDescription = $domain_properties['description'];
      $tAliases = $domain_properties['aliases'];
      $tMailboxes = $domain_properties['mailboxes'];
      $tMaxquota = $domain_properties['maxquota'];
      $tTransport = $domain_properties['transport'];
      $tBackupmx = $domain_properties['backupmx'];
      $tActive = $domain_properties['active'];
   }

   include ("../templates/header.tpl");
   include ("../templates/admin_menu.tpl");
   include ("../templates/admin_edit-domain.tpl");
   include ("../templates/footer.tpl");
}

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
   if (isset ($_GET['domain'])) $domain = escape_string ($_GET['domain']);

	if (isset ($_POST['fDescription'])) $fDescription = escape_string ($_POST['fDescription']);
	if (isset ($_POST['fAliases'])) $fAliases = intval($_POST['fAliases']);
	if (isset ($_POST['fMailboxes'])) $fMailboxes = intval($_POST['fMailboxes']);
	if (isset ($_POST['fMaxquota'])) {
      $fMaxquota = intval($_POST['fMaxquota']);
   } else {
      $fMaxquota = 0;
   }
	if (isset ($_POST['fTransport'])) $fTransport = escape_string ($_POST['fTransport']);
	if (isset ($_POST['fBackupmx'])) $fBackupmx = escape_string ($_POST['fBackupmx']);
   if (isset ($_POST['fActive'])) $fActive = escape_string ($_POST['fActive']);

   if ($fBackupmx == "on")
   {
      $fAliases = -1;
      $fMailboxes = -1;
      $fMaxquota = -1;
      $fBackupmx = 1;
      $sqlBackupmx = ('pgsql'==$CONF['database_type']) ? 'true' : 1;
   }
   else
   {
      $fBackupmx = 0;
      $sqlBackupmx = ('pgsql'==$CONF['database_type']) ? 'false' : 0;
   }

   if ($fActive == "on") { $fActive = 1; }
   $sqlActive=$fActive;
   if ('pgsql'==$CONF['database_type']) $sqlActive = $fActive ? 'true':'false';
 
	$result = db_query ("UPDATE $table_domain SET description='$fDescription',aliases=$fAliases,mailboxes=$fMailboxes,maxquota=$fMaxquota,transport='$fTransport',backupmx='$sqlBackupmx',active='$sqlActive',modified=NOW() WHERE domain='$domain'");
	if ($result['rows'] == 1)
	{
      header ("Location: list-domain.php");
      exit;
	}
	else
	{
		$tMessage = $PALANG['pAdminEdit_domain_result_error'];
	}

   include ("../templates/header.tpl");
   include ("../templates/admin_menu.tpl");
   include ("../templates/admin_edit-domain.tpl");
   include ("../templates/footer.tpl");
}

/* vim: set expandtab softtabstop=3 tabstop=3 shiftwidth=3: */
?>