<?php

/**
 * LOGIN - Selects users persistant login details.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org, Ross Kuyper
 *
 */
class LOGIN_selectUserPersistentQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			t1.user_id, t1.user_display_name, t1.user_password, t1.user_name, t1.user_email, t1.user_group, t1.user_role, t1.language, t1.timezone as user_timezone, t1.region,
			t2.user_group_name, t3.user_role_name
		FROM
			_db_core_users t1
		LEFT JOIN
			_db_core_user_groups t2
		ON
			t1.user_group = t2.user_group_id
		LEFT JOIN
			_db_core_user_roles t3
		ON
			t1.user_role = t3.user_role_id
		WHERE
			t1.user_id = '%s'
	";
	
	protected $singleRow = true;
}

/**
 * LOGIN - Selects users cookie data.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class LOGIN_selectCookieQuery extends PHPDS_query
{
	protected $sql = "
		SELECT
			user_id, cookie_id, pass_crypt
		FROM
			_db_core_session
		WHERE
			id_crypt = '%s'
	";
}

/**
 * LOGIN - Deletes users cookie data.
 * @author Jason Schoeman, Contact: titan [at] phpdevshell [dot] org.
 *
 */
class LOGIN_deleteCookieQuery extends PHPDS_query
{
	protected $sql = "
		DELETE FROM
			_db_core_session
		WHERE
			cookie_id = %u
	";
}

/**
 * LOGIN - Insert persistant cookie data.
 * @author Ross Kuyper
 *
 */
class LOGIN_setPersistentCookieQuery extends PHPDS_query
{
	protected $sql = "
		INSERT INTO
			_db_core_session (user_id, id_crypt, pass_crypt, timestamp)
		VALUES
			(%u, '%s', '%s', '%s')
	";

	public function invoke($parameters = null)
	{
		$user_id = $parameters[0];
		$pass_crypt = md5(uniqid(rand(), TRUE));
		$id_crypt = substr(md5(uniqid(rand(), TRUE)), 6, 6);
		$timestamp = time();

		parent::invoke(array($user_id, $id_crypt, $pass_crypt, $timestamp));

		return setcookie('pdspc', $id_crypt . $pass_crypt, $timestamp + 63113852);
	}
}

/**
 * LOGIN - Remove persistant cookie from database
 * @author Ross Kuyper
 *
 */
class LOGIN_deletePersistentCookieQuery extends PHPDS_query
{
	protected $sql = "
		DELETE FROM
			_db_core_session
		WHERE
			(id_crypt = '%s' AND user_id = '%s')
	";

    /**
     *
     * @version 1.0.1
     * @date 20121210 (1.0.1) (greg) refactor to ensure proper return
     *
     * @param array $parameters the unprotected parameters
     * @return boolean whether or not the cookie was deleted
     */
	public function invoke($parameters = null)
	{
		if (empty($_COOKIE['pdspc'])) {
            return false;
        }
        $user_id = $parameters[0];

        $id_crypt = substr($_COOKIE['pdspc'], 0, 6);

        parent::invoke(array($id_crypt, $user_id));

        return setcookie('pdspc', 'false', 0);
	}
}













