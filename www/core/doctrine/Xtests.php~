<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Xtests
 *
 * @ORM\Table(name="xtests")
 * @ORM\Entity
 */
class Xtests
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
     * @ORM\Column(name="description", type="string", length=50, nullable=false)
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
     * @var integer
     *
     * @ORM\Column(name="lectureId", type="integer", nullable=false)
     */
    private $lectureid;



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
     * @return Xtests
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
     * @return Xtests
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
     * @return Xtests
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
     * @return Xtests
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
     * Set lectureid
     *
     * @param integer $lectureid
     * @return Xtests
     */
    public function setLectureid($lectureid)
    {
        $this->lectureid = $lectureid;

        return $this;
    }

    /**
     * Get lectureid
     *
     * @return integer 
     */
    public function getLectureid()
    {
        return $this->lectureid;
    }
}
