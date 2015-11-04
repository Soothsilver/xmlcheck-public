<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Xtest
 *
 * @ORM\Table(name="xtests")
 * @ORM\Entity
 */
class Xtest
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
     * @ORM\Column(name="description", type="string",  nullable=false)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="template", type="text", nullable=false)
     */
    private $template;

    /**
     * @var integer
     *
     * @ORM\Column(name="count", type="integer", nullable=false)
     */
    private $count;

    /**
     * @var string
     *
     * @ORM\Column(name="generated", type="text", nullable=false)
     */
    private $generated;

    /**
     * @var \Lecture
     *
     * @ORM\ManyToOne(targetEntity="Lecture", inversedBy="xtests")
     * @ORM\JoinColumn(name="lectureId", referencedColumnName="id")
     */
    private $lecture;



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
     * Set description
     *
     * @param string $description
     * @return Xtest
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
     * Set template
     *
     * @param string $template
     * @return Xtest
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get template
     *
     * @return string 
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Set count
     *
     * @param integer $count
     * @return Xtest
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }

    /**
     * Get count
     *
     * @return integer 
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Set generated
     *
     * @param string $generated
     * @return Xtest
     */
    public function setGenerated($generated)
    {
        $this->generated = $generated;

        return $this;
    }

    /**
     * Get generated
     *
     * @return string 
     */
    public function getGenerated()
    {
        return $this->generated;
    }

    /**
     * Set lecture
     *
     * @param \Lecture $lecture
     * @return Xtest
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
}
