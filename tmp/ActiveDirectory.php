<?php

/**
 * Project: ActiveDirectory :: control Active Directory with ldap or ldaps
 * File:    ActiveDirectory.php
 *
 * PHP version 5
 *
 * Copyright (c) 2009, JoungKyun.Kim <http://oops.org>
 *
 * LICENSE: LGPL2
 *
 * ActiveDirectory pear package support to control Microsoft Active Directory
 * with ldap or ldaps protocol.
 *
 * @category   System
 * @package    ActiveDirectory
 * @author     JoungKyun.Kim <http://oops.org>
 * @copyright  1997-2009 OOPS.ORG
 * @license    LGPL2
 * @version    $Id: $
 * @link       http://pear.oops.org/package/ActiveDirectory
 * @since      File available since Release 0.1
 */

/**
 * Main Class that control Active Directory
 * @package ActiveDirectory
 */
Class ActiveDirectory
{
	// {{{ properties
	/**
	 * Active Directory 인증서 경로
	 * @access  public
	 * @var     string
	 */
	static public $cert = '/etc/pki/CA/certs/tmoncorp.pem';

	/**
	 * Active Directory 기본 도메인 이름 설정
	 * @access  public
	 * @var     string
	 */
	static public $domain = 'tmoncorp.com';

	/**
	 * 기본으로 탐색할 경로
	 * @access  public
	 * @var     string
	 */
	static public $rdn = 'ou=TMON,dc=tmoncorp,dc=com';

	/**
	 * ldap 에러 메시지 저장
	 * @access  public
	 * @var     string
	 */
	static public $error;
	// }}}

	// {{{ (void) ActiveDirectory::__construct ($cert = null)
	/**
	 * @access  public
	 * @return  void
	 * @param   string  (optional) Set AD public certificate file.
	 */
	function __construct ($cert = null) {
		$this->rdn    = &self::$rdn;
		$this->domain = &self::$domain;
		$this->error  = &self::$error;

		self::init ($cert);
	}
	// }}}

	// {{{ (void) ActiveDirectory::init ($cert = null)
	/**
	 * Initialize ActiveDirectory class
	 *
	 * @access  public
	 * @return  void
	 * @param   string  (optional) Set AD public certificate file.
	 */
	function init ($cert = null) {
		putenv ('LDAPTLS_REQCERT=never');
		if ( $cert !== null && file_exists ($cert) )
			putenv ('LDAPTLS_CACERT=' . $cert);
	}
	// }}}

	// {{{ (resource) connect ($account, $password, $host)
	/**
	 * Connect Active Directory
	 * @access  public
	 * @return  object   {status, error, info}
	 * @param   string   Active Directory account name
	 *          string   Password or Account
	 *          string   hostname for connecting
	 */
	function connect ($account, $pass, $host) {
		$host = sprintf ('ldaps://%s', $host);
		$ldap = ldap_connect ($host, 636);

		ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

		$rdn = sprintf ('%s@%s', $account, self::$domain);

		$r = (object) array (
			'status' => false,
			'link' => false,
			'error'  => null,
			'info'   => null
		);

		if ( ($r->status = @ldap_bind ($ldap, $rdn, $pass)) === false ) {
			$r->error = ldap_error ($ldap);
		} else {
			$r->link = $ldap;
			$r->error = self::$error = null;
			$filter = sprintf ('(samaccountname=%s)', $account);

			$r->info = self::search ($ldap, $filter, $attr);

			if ( $r->info === false ) {
				$r->status = false;
				$r->error = self::$error;
				@ldap_unbind ($r->link);
			}
		}

		return $r;
	}
	// }}}

	// {{{ (array) get_member ($link, $group)
	/**
	 * Get group member
	 * @access  public
	 * @return  array or false
	 * @param   string   Active Directory account name
	 *          string   Group name
	 */
	function get_member ($link, $group) {
		#$filter = sprintf ("(&(objectCategory=group)(cn=%s))", $group);
		$filter = sprintf ("(&(objectCategory=group)(displayname=%s*))", $group);

		if ( ($r = self::search ($link, $filter, array ('member'))) === false )
			return false;

		return $r[0]->members;
	}
	// }}}

	// {{{ search ($link, $dn, $filter, $attr)
	/**
	 * Search Active Directory
	 * @access  public
	 * @return  array or object
	 * @param   resource  ldap link
	 *          string   bind dn
	 *          filter   ldap filter
	 *          attr     (optional) array that wnated retrun attribute
	 */
	function search ($link, $filter, $attr = null) {
		if ( ! $attr ) {
			$attr = array (
				'cn', 'sn', 'givenname', 'displayname', 'distinguishedname',
				'department', 'title', 'description', 'company',
				'mail', 'ipphone', 'mobile', 'memberof', 'member',
				'mssfu30name', 'uidnumber', 'gidnumber', 'unixhomedirectory', 'loginshell',
				'whencreated', 'whenchanged', 'lastlogontimestamp', 'lastlogon',
				'pwdlastset', 'badpasswordtime', 'accountexpires',
			);
		}

		if ( ($entries = @ldap_search ($link, self::$rdn, $filter, $attr, 0, 0, 30)) === false ) {
			self::$error = ldap_error ($link);
			return false;
		}

		ldap_sort ($link, $entries, 'sn');

		$entry = ldap_get_entries ($link, $entries);

		if ( ! is_array ($entry) || $entry['count'] == 0 ) {
			self::$error = sprintf ("%s condition don't exists", $filter);
			return false;
		}

		$ignore_pattern = '/^(object|msexchmailboxguid|msexchmailboxsecuritydescriptor|count)/';

		foreach ($entry as $key => $value) {
			if ( $key === 'count' )
				continue;

			if ( ! is_array ($value) )
				$value = array ();

			foreach ($value as $k => $v) {
				if ( is_numeric ($k) || preg_match ($ignore_pattern, $k) )
					continue;

				if ( $v['count'] == 1 ) {
					if ( preg_match ('/0Z$/', $v[0]) )
						self::convert_to_0Ztime ($v[0]);

					@$r[$key]->$k = $v[0];
				} else
					@$r[$key]->$k = $v;
			}

			self::convert_to_unixtime ($r[$key]->badpasswordtime);
			self::convert_to_unixtime ($r[$key]->lastlogon);
			self::convert_to_unixtime ($r[$key]->pwdlastset);
			self::convert_to_unixtime ($r[$key]->accountexpires);
			self::convert_to_unixtime ($r[$key]->lastlogontimestamp);

			if ( $r[$key]->member['count'] )
				$member = $r[$key]->member;
			else
				$member = $r[$key]->memberof;

			for ( $i = 0; $i<$member['count']; $i++ )
				@$r[$key]->members[$i] = preg_replace ('/(CN=|,.*)/i', '', $member[$i]);
		}

		if ( $ent['count'] == 1 )
			return $r[0];

		return $r;
	}
	// }}}

	// {{{ public convert_to_unixtime ($sec)
	/**
	 * Convert to unix timestamp from nt timestamp
	 *
	 * @access  public
	 * @param   int      NT timestamp
	 */
	function convert_to_unixtime (&$sec) {
		$sec = (integer) (($sec / 10000000) - 11644473600);
	}
	// }}}

	// {{{ public convert_to_0Ztime ($sec)
	/**
	 * Convert to unix timestamp from nt 0Z times
	 *
	 * @access  public
	 * @return  NT timestamp (Integer)
	 * @param   int      Unix timestamp
	 */
	function convert_to_0Ztime (&$time) {
		$time = mktime (
			substr ($time, 8, 2),
			substr ($time, 10, 2),
			substr ($time, 12, 2),
			substr ($time, 4, 2),
			substr ($time, 6, 2),
			substr ($time, 0, 4)
		);
	}
	// }}}

	// {{{ (void) close ($link = null) 
	/**
	 * Disconnect Active Directory
	 * @access	public
	 * @return  void
	 * @param   resource  (optional) ldap link
	 */
	function close ($link) {
		if ( is_resource ($link) )
			ldap_unbind ($link);
	}
	// }}}
}

?>
