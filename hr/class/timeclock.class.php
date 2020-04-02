<?php
/* Copyright (C) 2019       Open-DSI            <support@open-dsi.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *	\file       htdocs/custom/timeclock/class/timeclock.class.php
 *	\ingroup    Time clock
 *	\brief      File of class to manage time clock
 */
require_once DOL_DOCUMENT_ROOT .'/core/class/commonobject.class.php';

/**
 *	Class of the module time clock.
 */
class Timeclock extends CommonObject
{
	public $element='timeclock';
	public $table_element='timeclock';

	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)

	var $id;

	var $fk_user;
	var $checking_arrival='';
	var $ip_arrival;
	var $checking_exit='';
	var $ip_exit;
	var $status;
	var $fk_user_modif;

	/**
	 *   Constructor
	 *
	 *   @param		DoliDB		$db      Database handler
	 */
	function __construct($db)
	{
		$this->db = $db;
	}

	/**
	 * Create object in database
	 *
	 * @param	User	$user	User that creates
	 * @return 	int				<0 if KO, >0 if OK
	 */
	function create($user)
	{
		global $conf;
		$error=0;

		// Clean parameters
		if (isset($this->fk_user)) $this->fk_user=trim($this->fk_user);
		if (isset($this->ip_arrival)) $this->ip_arrival=trim($this->ip_arrival);
		if (isset($this->ip_exit)) $this->ip_exit=trim($this->ip_exit);
		if (isset($this->status)) $this->status=trim($this->status);
		if (isset($this->fk_user_modif)) $this->fk_user_modif=trim($this->fk_user_modif);

		$this->db->begin();

		$sql = "INSERT INTO ".MAIN_DB_PREFIX."timeclock (";
		$sql.= "fk_user";
		$sql.= ", entity";
		$sql.= ", checking_arrival";
		$sql.= ", ip_arrival";
		$sql.= ", checking_exit";
		$sql.= ", ip_exit";
		$sql.= ", fk_user_modif";
		$sql.= ", status";
		$sql.= ") VALUES (";
		$sql.= (! isset($this->fk_user)?'NULL':"'".$this->fk_user."'");
		$sql.= ", ".$conf->entity;
		$sql.= ", ".(! isset($this->checking_arrival) || dol_strlen($this->checking_arrival)==0?'NULL':"'".$this->db->idate($this->checking_arrival)."'");
		$sql.= ", ".(! isset($this->ip_arrival)?'NULL':"'".$this->db->escape($this->ip_arrival)."'");
		$sql.= ", ".(! isset($this->checking_exit) || dol_strlen($this->checking_exit)==0?'NULL':"'".$this->db->idate($this->checking_exit)."'");
		$sql.= ", ".(! isset($this->ip_exit)?'NULL':"'".$this->db->escape($this->ip_exit)."'");
		$sql.= ", ".(! isset($this->status)?'NULL':"'".$this->status."'");
		$sql.= ", ".(! isset($this->fk_user_modif)?'NULL':"'".$this->fk_user_modif."'");
		$sql.= ")";

		dol_syslog(get_class($this)."::create", LOG_DEBUG);
		$result = $this->db->query($sql);
		if ($result)
		{
			$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."timeclock");

			// Call trigger
			$result=$this->call_trigger('TIMECLOCK_CREATE',$user);
			if ($result < 0)
			{
				$this->db->rollback();
				return -2;
			}
			// End call triggers

			$result=$this->update($user);
			if ($result > 0)
			{
				$this->db->commit();
				return $this->id;
			}
			else
			{
				$this->error=$this->db->error();
				$this->db->rollback();
				return $result;
			}
		}
		else
		{
			$this->error=$this->db->error()." sql=".$sql;
			$this->db->rollback();
			return -1;
		}
	}

	/**
	 * Load an object from database
	 *
	 * @param	int		$id		Id of record to load
	 * @return	int				<0 if KO, >0 if OK
	 */
	function fetch($id)
	{
		global $langs;
		$sql = "SELECT rowid, fk_user, checking_arrival, ip_arrival, checking_exit, ip_exit, status, fk_user_modif, tms";
		$sql.= " FROM ".MAIN_DB_PREFIX."timeclock";
		$sql.= " WHERE rowid = ".$id;

		dol_syslog(get_class($this)."::fetch", LOG_DEBUG);
		$result = $this->db->query($sql);
		if ( $result )
		{
			$obj = $this->db->fetch_object($result);

			$this->id				= $obj->rowid;
			$this->fk_user			= $obj->fk_user;
			$this->checking_arrival	= $this->db->jdate($obj->checking_arrival);
			$this->ip_arrival		= $obj->ip_arrival;
			$this->checking_exit	= $this->db->jdate($obj->checking_exit);
			$this->ip_exit			= $obj->ip_exit;
			$this->status			= $obj->status;
			$this->fk_user_modif	= $obj->fk_user_modif;
			$this->tms				= $this->db->jdate($obj->tms);

			return 1;
		}
		else
		{
			$this->error=$this->db->error();
			return -1;
		}
	}

	/**
	 *	Update record
	 *
	 *	@param	User	$user		User making update
	 *	@return	int					<0 if KO, >0 if OK
	 */
	function update($user=0)
	{
		global $conf, $langs;
		$error=0;

		// Clean parameters
		if (isset($this->fk_user)) $this->fk_user=trim($this->fk_user);
		if (isset($this->status)) $this->status=trim($this->status);
		if (isset($this->fk_user_modif)) $this->fk_user_modif=trim($this->fk_user_modif);
		if (isset($this->ip_arrival)) $this->ip_arrival=trim($this->ip_arrival);
		if (isset($this->ip_exit)) $this->ip_exit=trim($this->ip_exit);

		// Check parameters
		// Put here code to add control on parameters values

		// Update request
		$sql = "UPDATE ".MAIN_DB_PREFIX."timeclock SET";

		$sql.= " fk_user=".(isset($this->fk_user)?$this->fk_user:"null");
		$sql.= ", checking_arrival=".(dol_strlen($this->checking_arrival)!=0 ? "'".$this->db->idate($this->checking_arrival)."'" : 'null');
		$sql.= ", ip_arrival=".(isset($this->ip_arrival)?"'".$this->db->escape($this->ip_arrival)."'":"null");
		$sql.= ", checking_exit=".(dol_strlen($this->checking_exit)!=0 ? "'".$this->db->idate($this->checking_exit)."'" : 'null');
		$sql.= ", ip_exit=".(isset($this->ip_exit)?"'".$this->db->escape($this->ip_exit)."'":"null");
		$sql.= ", status=".(isset($this->status)?$this->status:"null");
		$sql.= ", fk_user_modif=".(isset($this->fk_user_modif)?$this->fk_user_modif:"null");
		$sql.= " WHERE rowid=".$this->id;

		$this->db->begin();

		dol_syslog(get_class($this)."::update", LOG_DEBUG);
		$result = $this->db->query($sql);
		if ($result)
		{
			$this->db->commit();
			return 1;
		}
		else
		{
			$this->error=$this->db->lasterror();
			$this->db->rollback();
			return -1;
		}
	}

	/**
	 *	Delete record
	 *
	 *	@param	int		$id		Id of record to delete
	 *	@return	int				<0 if KO, >0 if OK
	 */
	function delete($id)
	{
		$this->db->begin();

		$sql = "DELETE FROM ".MAIN_DB_PREFIX."timeclock WHERE rowid = ".$id;

		dol_syslog(get_class($this)."::delete", LOG_DEBUG);
		$result = $this->db->query($sql);
		if ($result)
		{
			$this->db->commit();
			return 1;
		}
		else
		{
			$this->error=$this->db->error();
			$this->db->rollback();
			return -1;
		}
	}

	/**
	 *	Return dol_now with a specific format
	 *
	 *	@return	timestamp			Unix format
	 */
	function getDateNow()
	{
		$now = dol_now();
		return strtotime(dol_print_date($now,'%Y-%m-%d %H:%M:%S','tzuser'));
	}
}
