<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Questions
 *
 * @ORM\Table(name="questions", indexes={@ORM\Index(name="lectureId", columns={"lectureId"})})
 * @ORM\Entity
 */
class Question
{
    const TYPE_TEXT = 'text';
    const TYPE_IMAGE = 'image';
    const TYPE_CODE = 'code';
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
     * @ORM\Column(name="text", type="text", nullable=false)
     */
    private $text;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", nullable=false)
     */
    private $type = self::TYPE_TEXT;

    /**
     * @var string
     *
     * @ORM\Column(name="options", type="text", nullable=false)
     */
    private $options;

    /**
     * @var string
     *
     * @ORM\Column(name="attachments", type="text", nullable=false)
     */
    private $attachments;

    /**
     * @var \Lecture
     *
     * @ORM\ManyToOne(targetEntity="Lecture", inversedBy="questions")
     * @ORM\JoinColumn(referencedColumnName="id", name="lectureId")
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
     * Set text
     *
     * @param string $text
     * @return Question
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Question
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
     * Set options
     *
     * @param string $options
     * @return Question
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Get options
     *
     * @return string 
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set attachments
     *
     * @param string $attachments
     * @return Question
     */
    public function setAttachments($attachments)
    {
        $this->attachments = $attachments;

        return $this;
    }

    /**
     * Get attachments
     *
     * @return string 
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * Set lecture
     *
     * @param \Lecture $lecture
     * @return Question
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
