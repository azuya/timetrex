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


/**
 * @package PayrollDeduction\CA
 */
class PayrollDeduction_CA_YT extends PayrollDeduction_CA {
	function getProvincialSurtax() {
		/*
			V1 =
			For YU
				Where T4 <= 6000
				V1 = 0

				Where T4 > 6000
				V1 = 0.10 * ( T4 - 6000 )
		*/

		$T4 = $this->getProvincialBasicTax();
		$V1 = 0;

		//Repealed 01-Jul-2015 retroactively to 01-Jan-2015.
		if ( $this->getDate() >= strtotime('01-Jan-2008') AND $this->getDate() < strtotime('01-Jul-2015') ) {
			if ( $T4 <= 6000 ) {
				$V1 = 0;
			} elseif ( $T4 > 6000 ) {
				$V1 = bcmul( 0.05, bcsub( $T4, 6000 ) );
			}
		}

		Debug::text('V1: '. $V1, __FILE__, __LINE__, __METHOD__, 10);

		return $V1;
	}
}
?>
