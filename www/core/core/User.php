<?php

namespace asm\core;
use asm\utils\Flags;
use asm\utils\Security;

/**
 * User session management class @singleton.
 *
 * Provides methods for user login, logout, checking of user privileges and
 * getters for user data (username, real name, e-mail, last login timestamp).
 *
 * Users must be logged in to access most of the application features. Session
 * is started on login and is valid until logout or until
 * @ref sessionTimeout "set time" runs out. Timeout can be refreshed using @ref refresh
 * method.
 */
class User
{
    const sendEmailOnSubmissionRatedStudent = "sendEmailOnSubmissionRatedStudent";
    const sendEmailOnAssignmentAvailableStudent = "sendEmailOnAssignmentAvailableStudent";
    const sendEmailOnSubmissionConfirmedTutor = "sendEmailOnSubmissionConfirmedTutor";

	/**
	 * Privilege flags
	 * Each flag stands for privilege to perform certain action. Every user's
	 * privileges are a single number - sum of privilege flags.
	 */
	//@{
	const none							=	0;				///< no privileges (dummy)
	const usersAdd						=	0x1;			///< create new active users
	const usersManage					=	0x2;			///< edit any user's data
	const usersRemove					=	0x4;			///< delete any user
	const usersExplore				=	0x8;			///< view all users' data
	const usersPrivPresets			=	0x10;			///< view, create, edit and delete user types
	const pluginsAdd					=	0x20;			///< upload new plugins
	const pluginsManage				=	0x40;			///< edit plugin data (unused)
	const pluginsTest					=	0x80;			///< run plugin tests
	const assignmentsSubmit			=	0x100;		///< upload submissions for assignments
	const groupsJoinPublic			=	0x200;		///< subscribe to public groups
	const groupsJoinPrivate			=	0x400;		///< subscribe to private groups without requesting
	const groupsRequest				=	0x800;		///< request private group subscriptions
	const groupsAdd					=	0x1000;		///< create new groups
	const groupsManageOwn			=	0x2000;		///< edit or delete own groups
	const groupsManageAll			=	0x4000;		///< edit or delete any group
	const lecturesAdd					=	0x8000;		///< create new lectures
	const lecturesManageOwn			=	0x10000;		///< edit or delete own lectures
	const lecturesManageAll			=	0x20000;		///< edit or delete any lecture
	const submissionsCorrect		=	0x40000;		///< correct (rate) submissions - has no purpose without @ref groupsAdd
	const pluginsExplore				=	0x80000;		///< view all plugins' data
	const pluginsRemove				=	0x100000;	///< delete plugins
	const otherAdministration    	=	0x200000;	///< view system log
	const submissionsViewAuthors	=	0x400000;	///< view real names of submission authors
	const submissionsModifyRated	=	0x800000;	///< modify ratings of already rated submissions
	//@}
	const sessionTimeout = 10800;	///< how long the user stays logged in [3 hours]
	const sessionSpace = 'user';	///< data is stored in $_SESSION under this key

    private static $instance;	///< singleton instance

	/**
	 * [Creates and] returns singleton instance.
	 * @return User this singleton class' instance
	 */
	public static function instance ()
	{
		if (!self::$instance)
		{
			self::$instance = new self();
		}
		return self::$instance;
	}

/* ----------------------- SINGLETON ----------------------------------------*/

    private $entity = null; // Doctrine2 entity
	protected $data = null;	///< (array) user data
	protected $privileges = null;	///< (Flags) helper for management of privileges

	/**
	 * Creates instance and initializes it from $_SESSION if possible.
	 *
	 * Also initializes privilege helper with privilege names (same as constant names).
	 */
	protected function __construct ()
	{
		if (!isset($_SESSION[self::sessionSpace]))
		{
			$_SESSION[self::sessionSpace] = null;
		}
		
		$this->data = & $_SESSION[self::sessionSpace];
		$privilegeNames = array(
			'usersAdd', 'usersManage', 'usersRemove', 'usersExplore', 'usersPrivPresets',
			'groupsJoinPublic', 'groupsJoinPrivate', 'groupsRequest',
			'pluginsAdd', 'pluginsManage', 'pluginsRemove', 'pluginsExplore', 'pluginsTest',
			'assignmentsSubmit',
			'submissionsCorrect',
			'lecturesAdd', 'lecturesManageOwn', 'lecturesManageAll',
			'groupsAdd', 'groupsManageOwn', 'groupsManageAll',
			'otherAdministration',
			'submissionsViewAuthors',
			'submissionsModifyRated'
		);
		$privileges = array();
		foreach ($privilegeNames as $name) {
			$privileges[$name] = constant('self::' . $name);
		}
		asort($privileges);
		$this->privileges = new Flags(array_keys($privileges));

        if ($this->isLogged())
        {
            $this->entity = Repositories::getEntityManager()->find('User', $this->data['id']);
        }
	}

	/**
	 *	Gets user's privileges (compact) or compacts supplied privileges array.
	 * @param array $privileges privileges to be compacted
	 * @return int compact privileges (sum of flag values)
	 */
	public function packPrivileges ($privileges = null)
	{
		return ($privileges === null)
			? $this->data['privileges']
			: $this->privileges->toInteger($privileges);
	}

	/**
	 * Gets expanded user's privileges or expands supplied compact privileges.
	 * @param int $privileges privileges to be expanded
	 * @return array expanded privileges (associative array of PRIVILEGE_NAME => true/false pairs)
	 */
	public function unpackPrivileges ($privileges = null)
	{
		if ($privileges === null)
		{
			$privileges = (isset($this->data['privileges'])) ? $this->data['privileges'] : User::none;
		}
		return $this->privileges->toArray($privileges);
	}

    /**
     * @return \User
     */
    public function getEntity()
    {
        return $this->entity;
    }

	/**
	 * Tries to log user in with supplied credentials.
	 * @param string $name username
	 * @param string $pass password
	 * @return bool true if login was successful
	 */
	public function login ($name, $pass)
	{
		if ($this->data != null)
		{
			$this->logout();
		}

		/// Username is case-insensitive.
		$name = strtolower($name);


		$users = Repositories::getRepository(Repositories::User)->findBy(['name' => $name]);
		if (!empty($users))
		{
			/**
			 * @var $user \User
			 */
			$user = $users[0];

            if ($user->getActivationCode() !== '')
            {
                // Non-empty activation code means the account is not yet activated.
                return false;
            }
            $authenticationSuccess = Security::check($pass, $user->getPass(), $user->getEncryptionType());
            if ($authenticationSuccess)
			{
				$this->data = array(
					'id' => $user->getId(),
					'name' => $user->getName(),
					'privileges' => $user->getType()->getPrivileges(),
					'realName' => $user->getRealName(),
					'email' => $user->getEmail(),
					'lastAccess' => $user->getLastAccess()->format("Y-m-d H:i:s"),
                    'applicationVersion' => implode('.', Config::get('version')),
                    // Here, 1 and 0 are used instead of booleans because of legacy code
                    User::sendEmailOnAssignmentAvailableStudent => $user->getSendEmailOnNewAssignment() ? 1 : 0,
                    User::sendEmailOnSubmissionConfirmedTutor => $user->getSendEmailOnNewSubmission() ? 1 : 0,
                    User::sendEmailOnSubmissionRatedStudent => $user->getSendEmailOnSubmissionRated() ? 1 : 0
				);
				$this->refresh();
				$user->setLastAccess(new \DateTime());
				Repositories::persistAndFlush($user);
                $this->entity = $user;
				return true;
			}
			else
			{
				return false;
			}
		}
		return false;
	}

	/**
	 * Logs user out.
	 * @return bool true if logout was successful (always)
	 */
	public function logout ()
	{
		if ($this->data != null) $this->data = null;
		return true;
	}

	/**
	 * Refreshes timeout for current user session.
	 */
	public function refresh () {
		$this->data['timeout'] = time() + self::sessionTimeout;
	}

    public function getData($key)
    {
        if (isset($this->data[$key]))
        {
            return $this->data[$key];
        }
        else
        {
            return false;
        }
    }
    public function setData($key, $value)
    {
        $this->data[$key] = $value;
    }
	/**
	 * Gets user's id.
	 * @return mixed int with id of current user or false if not logged in
	 */
	public function getId ()
	{
		if (isset($this->data['id'])) return $this->data['id'];
		else return false;
	}

	/**
	 * Gets username.
	 * @return mixed string with username of current user or false if not logged in
	 */
	public function getName ()
	{
		if (isset($this->data['name'])) return $this->data['name'];
		else return false;
	}

	/**
	 * Gets user's real name.
	 * @return mixed string with real name of current user or false if not logged in
	 */
	public function getRealName ()
	{
		if (isset($this->data['realName'])) return $this->data['realName'];
		else return false;
	}

	/**
	 * Gets user's e-mail.
	 * @return mixed string with email of current user or false if not logged in
	 */
	public function getEmail ()
	{
		if (isset($this->data['email'])) return $this->data['email'];
		else return false;
	}

	/**
	 * Gets time of last login.
	 * @return mixed last login timestamp of current user (int) or false if not logged in
	 */
	public function getLastAccess ()
	{
		if (isset($this->data['lastAccess'])) return $this->data['lastAccess'];
		else return false;
	}

	/**
	 *	Gets time remaining to user session expiration.
	 * @return int time before user session expires
	 */
	public function getTimeout ()
	{
		return (isset($this->data['timeout']) ? $this->data['timeout'] : 0);
	}

	/**
	 * Checks whether user has supplied privileges.
	 * @param int [...] sets of privileges to check
	 * @return bool true if no privileges are supplied or if at least
	 * one supplied set of privileges is matched with user's privileges
	 */
	public function hasPrivileges (...$setsOfPrivileges)
	{
		if ($this->isLogged())
		{
            return Flags::match($this->data['privileges'], ...$setsOfPrivileges);
		}
		return false;
	}

	/**
	 * Checks whether user session is valid (== user is logged in).
	 * @return bool true if user is logged in and session hasn't expired
	 */
	public function isLogged ()
	{
		if ($this->data !== null)
		{
			if ($this->data['timeout'] > time())
			{
				return true;
			}
			else
			{
				$this->logout();
			}
		}
		return false;
	}

    public function isSessionValid (&$reason)
    {
        if ($this->data !== null)
        {
            if ($this->data['timeout'] < time())
            {
                $this->logout();
                $reason = "You have been inactive for a long time. <b>Logout, then log in again.</b>";
                return false;
            }
            elseif (!array_key_exists("applicationVersion", $this->data) || $this->data['applicationVersion'] !== implode('.', Config::get('version')))
            {
                // User is using an out-of-date application version. He must logout and login again.
                $this->logout();
                $reason = "Application was upgraded to a new version. <b>Logout, refresh the page, then log in again.</b>";
                return false;
            }
            return true;
        }
        // The user is not logged in at all, but this is not because the session is invalid.
        $reason = "You are no longer logged in. Perhaps you were inactive for too long or the server was upgraded. <b>Logout, refresh the page, then log in again.</b>";
        return false;
    }

}

