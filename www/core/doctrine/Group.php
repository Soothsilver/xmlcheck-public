<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * Group
 *
 * @ORM\Table(name="groups", indexes={@ORM\Index(name="lectureId", columns={"lectureId"}), @ORM\Index(name="ownerId", columns={"ownerId"})})
 * @ORM\Entity
 */
class Group
{
    const TYPE_PUBLIC = "public";
    const TYPE_PRIVATE = "private";
    /**
     * @var Lecture
     * @ORM\ManyToOne(targetEntity="Lecture",inversedBy="groups")
     * @ORM\JoinColumn(name="lectureId", referencedColumnName="id")
     */
    private $lecture;
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
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="groups")
     * @ORM\JoinColumn(name="ownerId", referencedColumnName="id")
     */
    private $owner;
    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", nullable=false)
     */
    private $type;
    /**
     * @var boolean
     * @ORM\Column(name="deleted", type="boolean")
     */
    private $deleted = false;
    /**
     * @ORM\OneToMany(targetEntity="Assignment", mappedBy="group")
     */
    private $assignments;
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
     * @return Group
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
     * @return Group
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
     * Set type
     *
     * @param string $type
     * @return Group
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set lecture
     *
     * @param \Lecture $lecture
     * @return Group
     */
    public function setLecture(\Lecture $lecture = null)
    {
        $this->lecture = $lecture;

        return $this;
    }

    /**
     * Get lecture
     *
     * @return \Lecture 
     */
    public function getLecture()
    {
        return $this->lecture;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return Group
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
     * Set owner
     *
     * @param \User $owner
     * @return Group
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
     * Constructor
     */
    public function __construct()
    {
        $this->assignments = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add assignments
     *
     * @param \Assignment $assignments
     * @return Group
     */
    public function addAssignment(\Assignment $assignments)
    {
        $this->assignments[] = $assignments;

        return $this;
    }

    /**
     * Remove assignments
     *
     * @param \Assignment $assignments
     */
    public function removeAssignment(\Assignment $assignments)
    {
        $this->assignments->removeElement($assignments);
    }

    /**
     * Get assignments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAssignments()
    {
        return $this->assignments;
    }
}
