<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Lecture
 *
 * @ORM\Table(name="lectures", uniqueConstraints={@ORM\UniqueConstraint(name="name", columns={"name"})}, indexes={@ORM\Index(name="ownerId", columns={"ownerId"})})
 * @ORM\Entity
 */
class Lecture
{
    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="lectures")
     * @ORM\JoinColumn(name="ownerId", referencedColumnName="id")
     */
    private $owner;
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
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    private $description;

    /**
     * @var boolean
     * @ORM\Column(name="deleted", type="boolean")
     */
    private $deleted = false;

    /**
     * @var Group[]
     * @ORM\OneToMany(targetEntity="Group", mappedBy="lecture")
     */
    private $groups;


    /**
     * @var Problem[]
     * @ORM\OneToMany(targetEntity="Problem", mappedBy="lecture")
     */
    private $problems;
    /**
     * @var Attachment[]
     * @ORM\OneToMany(targetEntity="Attachment", mappedBy="lecture")
     */
    private $attachments;
    /**
     * @var Question[]
     * @ORM\OneToMany(targetEntity="Question", mappedBy="lecture")
     */
    private $questions;
    /**
     * @var Xtest[]
     * @ORM\OneToMany(targetEntity="Xtest", mappedBy="lecture")
     */
    private $xtests;


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
     * @return Lecture
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
     * Set description
     *
     * @param string $description
     * @return Lecture
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }


    /**
     * Set owner
     *
     * @param \User $owner
     * @return Lecture
     */
    public function setOwner(\User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \User 
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return Lecture
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
     * Constructor
     */
    public function __construct()
    {
        $this->problems = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add problems
     *
     * @param \Problem $problems
     * @return Lecture
     */
    public function addProblem(\Problem $problems)
    {
        $this->problems[] = $problems;

        return $this;
    }

    /**
     * Remove problems
     *
     * @param \Problem $problems
     */
    public function removeProblem(\Problem $problems)
    {
        $this->problems->removeElement($problems);
    }

    /**
     * Get problems
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProblems()
    {
        return $this->problems;
    }

    /**
     * Add groups
     *
     * @param \Group $groups
     * @return Lecture
     */
    public function addGroup(\Group $groups)
    {
        $this->groups[] = $groups;

        return $this;
    }

    /**
     * Remove groups
     *
     * @param \Group $groups
     */
    public function removeGroup(\Group $groups)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->groups->removeElement($groups);
    }

    /**
     * Get groups
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Add attachments
     *
     * @param \Attachment $attachments
     * @return Lecture
     */
    public function addAttachment(\Attachment $attachments)
    {
        $this->attachments[] = $attachments;
    
        return $this;
    }

    /**
     * Remove attachments
     *
     * @param \Attachment $attachments
     */
    public function removeAttachment(\Attachment $attachments)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->attachments->removeElement($attachments);
    }

    /**
     * Get attachments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * Add questions
     *
     * @param \Question $questions
     * @return Lecture
     */
    public function addQuestion(\Question $questions)
    {
        $this->questions[] = $questions;
    
        return $this;
    }

    /**
     * Remove questions
     *
     * @param \Question $questions
     */
    public function removeQuestion(\Question $questions)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->questions->removeElement($questions);
    }

    /**
     * Get questions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getQuestions()
    {
        return $this->questions;
    }

    /**
     * Add xtests
     *
     * @param \Xtest $xtests
     * @return Lecture
     */
    public function addXtest(\Xtest $xtests)
    {
        $this->xtests[] = $xtests;
    
        return $this;
    }

    /**
     * Remove xtests
     *
     * @param \Xtest $xtests
     */
    public function removeXtest(\Xtest $xtests)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->xtests->removeElement($xtests);
    }

    /**
     * Get xtests
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getXtests()
    {
        return $this->xtests;
    }
}
