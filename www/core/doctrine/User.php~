<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Users
 *
 * @ORM\Table(name="users", uniqueConstraints={@ORM\UniqueConstraint(name="name", columns={"name"})}, indexes={@ORM\Index(name="type", columns={"type"})})
 * @ORM\Entity
 */
class User
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    private $name;

    /**
     * @var \UserType
     * @ORM\ManyToOne(targetEntity="UserType")
     * @ORM\JoinColumn(name="type", referencedColumnName="id")
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="pass", type="string", nullable=false)
     */
    private $pass;

    /**
     * @var string
     *
     * @ORM\Column(name="realName", type="string",  nullable=false)
     */
    private $realName;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string",  nullable=false)
     */
    private $email;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastAccess", type="datetime", nullable=false)
     */
    private $lastAccess;

    /**
     * @var string
     *
     * @ORM\Column(name="activationCode", type="string",  nullable=false)
     */
    private $activationCode;

    /**
     * @var string
     *
     * @ORM\Column(name="encryptionType", type="string", nullable=false)
     */
    private $encryptionType = 'md5';

    /**
     * @var string
     *
     * @ORM\Column(name="resetLink", type="string",  nullable=true)
     */
    private $resetLink;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="resetLinkExpiry", type="datetime", nullable=true)
     */
    private $resetLinkExpiry;
    /**
     * @var boolean
     *
     * @ORM\Column(name="send_email_on_submission_rated", type="boolean", nullable=false)
     */
    private $sendEmailOnSubmissionRated = '1';

    /**
     * @var boolean
     *
     * @ORM\Column(name="send_email_on_new_assignment", type="boolean", nullable=false)
     */
    private $sendEmailOnNewAssignment = '1';

    /**
     * @var boolean
     *
     * @ORM\Column(name="send_email_on_new_submission", type="boolean", nullable=false)
     */
    private $sendEmailOnNewSubmission = '1';

    /**
     * @var  boolean
     * @ORM\Column(name="deleted", type="boolean", nullable=false)
     * */
    private $deleted = false;

    /**
     * @var Group[]
     * @ORM\OneToMany(targetEntity="Group", mappedBy="owner")
     */
    private $groups;
    /**
     * @var Lecture[]
     * @ORM\OneToMany(targetEntity="Lecture", mappedBy="owner")
     */
    private $lectures;
    /**
     * @var Submission[]
     * @ORM\OneToMany(targetEntity="Submission", mappedBy="user")
     */
    private $submissions;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }



    /**
     * Set pass
     *
     * @param string $pass
     * @return User
     */
    public function setPass($pass)
    {
        $this->pass = $pass;

        return $this;
    }

    /**
     * Get pass
     *
     * @return string 
     */
    public function getPass()
    {
        return $this->pass;
    }

    /**
     * Set realName
     *
     * @param string $realName
     * @return User
     */
    public function setRealName($realName)
    {
        $this->realName = $realName;

        return $this;
    }

    /**
     * Get realName
     *
     * @return string 
     */
    public function getRealName()
    {
        return $this->realName;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set lastAccess
     *
     * @param \DateTime $lastAccess
     * @return User
     */
    public function setLastAccess($lastAccess)
    {
        $this->lastAccess = $lastAccess;

        return $this;
    }

    /**
     * Get lastAccess
     *
     * @return \DateTime 
     */
    public function getLastAccess()
    {
        return $this->lastAccess;
    }

    /**
     * Set activationCode
     *
     * @param string $activationCode
     * @return User
     */
    public function setActivationCode($activationCode)
    {
        $this->activationCode = $activationCode;

        return $this;
    }

    /**
     * Get activationCode
     *
     * @return string 
     */
    public function getActivationCode()
    {
        return $this->activationCode;
    }

    /**
     * Set encryptionType
     *
     * @param string $encryptionType
     * @return User
     */
    public function setEncryptionType($encryptionType)
    {
        $this->encryptionType = $encryptionType;

        return $this;
    }

    /**
     * Get encryptionType
     *
     * @return string 
     */
    public function getEncryptionType()
    {
        return $this->encryptionType;
    }

    /**
     * Set resetLink
     *
     * @param string $resetLink
     * @return User
     */
    public function setResetLink($resetLink)
    {
        $this->resetLink = $resetLink;

        return $this;
    }

    /**
     * Get resetLink
     *
     * @return string 
     */
    public function getResetLink()
    {
        return $this->resetLink;
    }

    /**
     * Set resetLinkExpiry
     *
     * @param \DateTime $resetLinkExpiry
     * @return User
     */
    public function setResetLinkExpiry($resetLinkExpiry)
    {
        $this->resetLinkExpiry = $resetLinkExpiry;

        return $this;
    }

    /**
     * Get resetLinkExpiry
     *
     * @return \DateTime 
     */
    public function getResetLinkExpiry()
    {
        return $this->resetLinkExpiry;
    }

    /**
     * Set sendEmailOnSubmissionRated
     *
     * @param boolean $sendEmailOnSubmissionRated
     * @return User
     */
    public function setSendEmailOnSubmissionRated($sendEmailOnSubmissionRated)
    {
        $this->sendEmailOnSubmissionRated = $sendEmailOnSubmissionRated;

        return $this;
    }

    /**
     * Get sendEmailOnSubmissionRated
     *
     * @return boolean 
     */
    public function getSendEmailOnSubmissionRated()
    {
        return $this->sendEmailOnSubmissionRated;
    }

    /**
     * Set sendEmailOnNewAssignment
     *
     * @param boolean $sendEmailOnNewAssignment
     * @return User
     */
    public function setSendEmailOnNewAssignment($sendEmailOnNewAssignment)
    {
        $this->sendEmailOnNewAssignment = $sendEmailOnNewAssignment;

        return $this;
    }

    /**
     * Get sendEmailOnNewAssignment
     *
     * @return boolean 
     */
    public function getSendEmailOnNewAssignment()
    {
        return $this->sendEmailOnNewAssignment;
    }

    /**
     * Set sendEmailOnNewSubmission
     *
     * @param boolean $sendEmailOnNewSubmission
     * @return User
     */
    public function setSendEmailOnNewSubmission($sendEmailOnNewSubmission)
    {
        $this->sendEmailOnNewSubmission = $sendEmailOnNewSubmission;

        return $this;
    }

    /**
     * Get sendEmailOnNewSubmission
     *
     * @return boolean 
     */
    public function getSendEmailOnNewSubmission()
    {
        return $this->sendEmailOnNewSubmission;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return User
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean 
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set type
     *
     * @param \UserType $type
     * @return User
     */
    public function setType(\UserType $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \UserType
     */
    public function getType()
    {
        return $this->type;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
        $this->lectures = new \Doctrine\Common\Collections\ArrayCollection();
        $this->submissions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->lastAccess = new DateTime();
    }

    /**
     * Add groups
     *
     * @param \Group $group
     * @return User
     */
    public function addGroup(\Group $group)
    {
        $this->groups[] = $group;

        return $this;
    }

    /**
     * Remove groups
     *
     * @param \Group $group
     */
    public function removeGroup(\Group $group)
    {
        $this->groups->removeElement($group);
    }

    /**
     * Get groups
     *
     * @return Group[]
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Add lecture
     *
     * @param Lecture $lecture
     * @return User
     */
    public function addLecture(\Lecture $lecture)
    {
        $this->lectures[] = $lecture;

        return $this;
    }

    /**
     * Remove lecture
     *
     * @param \Lecture $lecture
     */
    public function removeLecture(\Lecture $lecture)
    {
        $this->lectures->removeElement($lecture);
    }

    /**
     * Get lectures
     *
     * @return Lecture[]
     */
    public function getLectures()
    {
        return $this->lectures;
    }

    /**
     * Add submission
     *
     * @param \Submission $submission
     * @return User
     */
    public function addSubmission(\Submission $submission)
    {
        $this->submissions[] = $submission;

        return $this;
    }

    /**
     * Remove submission
     *
     * @param \Submission $submission
     */
    public function removeSubmission(\Submission $submission)
    {
        $this->submissions->removeElement($submission);
    }

    /**
     * Get submissions
     *
     * @return Submission[]
     */
    public function getSubmissions()
    {
        return $this->submissions;
    }
}
