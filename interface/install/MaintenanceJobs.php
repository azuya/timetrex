<?php
/*********************************************************************************
 * TimeTrex is a Payroll and Time Management program developed by
 * TimeTrex Software Inc. Copyright (C) 2003 - 2014 TimeTrex Software Inc.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by
 * the Free Software Foundation with the addition of the following permission
 * added to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED
 * WORK IN WHICH THE COPYRIGHT IS OWNED BY TIMETREX, TIMETREX DISCLAIMS THE
 * WARRANTY OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
 * details.
 *
 * You should have received a copy of the GNU Affero General Public License along
 * with this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 *
 * You can contact TimeTrex headquarters at Unit 22 - 2475 Dobbin Rd. Suite
 * #292 Westbank, BC V4T 2E9, Canada or at email address info@timetrex.com.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU Affero General Public License
 * version 3, these Appropriate Legal Notices must retain the display of the
 * "Powered by TimeTrex" logo. If the display of the logo is not reasonably
 * feasible for technical reasons, the Appropriate Legal Notices must display
 * the words "Powered by TimeTrex".
 ********************************************************************************/

require_once('../../includes/global.inc.php');

$authenticate = FALSE;
require_once(Environment::getBasePath() .'includes/Interface.inc.php');

$smarty->assign('title', TTi18n::gettext($title = '7. Maintenance Jobs')); // See index.php

/*
 * Get FORM variables
 */
extract	(FormVariables::GetVariables(
										array	(
												'action',
												'company_id',
												'user_data',
												'external_installer',
												) ) );

$install_obj = new Install();
if ( $install_obj->isInstallMode() == FALSE ) {
	Redirect::Page( URLBuilder::getURL(NULL, 'install.php') );
}

$uf = TTnew( 'UserFactory' );

$action = Misc::findSubmitButton();
switch ($action) {
	case 'back':
		Debug::Text('Back', __FILE__, __LINE__, __METHOD__, 10);

		Redirect::Page( URLBuilder::getURL(NULL, 'User.php') );
		break;

	case 'next':
		//Debug::setVerbosity(11);
		Debug::Text('Submit!', __FILE__, __LINE__, __METHOD__, 10);

		Redirect::Page( URLBuilder::getURL( NULL, 'Done.php') );
		break;
	default:
		if ( isset($company_id) ) {
			$user_data['company_id'] = $company_id;
		}
		$smarty->assign_by_ref('user_data', $user_data);

		break;
}

$handle = @fopen('http://www.timetrex.com/'.URLBuilder::getURL( array('v' => $install_obj->getFullApplicationVersion(), 'page' => 'maintenance'), 'pre_install.php'), "r");
@fclose($handle);

if ( $install_obj->ScheduleMaintenanceJobs() == 0 ) { //Add scheduled maintenance jobs to cron/schtask, if it succeeds move to next step automatically.
	Redirect::Page( URLBuilder::getURL( NULL, 'Done.php') );
}

$smarty->assign_by_ref('uf', $uf);
$schedule_maintenance_job_command = $install_obj->getScheduleMaintenanceJobsCommand();
$smarty->assign_by_ref('schedule_maintenance_job_command', $schedule_maintenance_job_command );

$cron_file = Environment::getBasePath().'maint'. DIRECTORY_SEPARATOR .'cron.php';
$smarty->assign_by_ref('cron_file', $cron_file);

$smarty->assign_by_ref('web_server_user', $install_obj->getWebServerUser() );
$smarty->assign_by_ref('php_cli', $install_obj->getPHPCLI() );
$smarty->assign_by_ref('is_sudo_installed', $install_obj->isSUDOInstalled() );


$smarty->display('install/MaintenanceJobs.tpl');
?>