<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Submission
 *
 * @ORM\Table(name="submissions", indexes={@ORM\Index(name="assignmentId", columns={"assignmentId"}), @ORM\Index(name="userId", columns={"userId"})})
 * @ORM\Entity
 */
class Submission
{
    const STATUS_BEING_EVALUATED = "new";
    const STATUS_NORMAL = "normal";
    const STATUS_LATEST = "latest";
    const STATUS_REQUESTING_GRADING = "handsoff";
    const STATUS_GRADED = "graded";
    const STATUS_DELETED = "deleted";

    const SIMILARITY_STATUS_GUILTY = "guilty";
    const SIMILARITY_STATUS_INNOCENT = "innocent";
    const SIMILARITY_STATUS_CHECKED = "checked";
    const SIMILARITY_STATUS_NEW = "new";

    /**
     * @ORM\OneToMany(targetEntity="Document", mappedBy="submission")
     */
    private $documents;
    /**
     * @return Assignment
     */
    public function getAssignment()
    {
        return $this->assignment;
    }
    /**
     * @ORM\ManyToOne(targetEntity="Assignment")
     * @ORM\JoinColumn(name="assignmentId", referencedColumnName="id")
     */
    private $assignment;
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="submissions")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
     */
    private $user;

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
     * @ORM\Column(name="submissionFile", type="string", nullable=false)
     */
    private $submissionFile = '';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", nullable=false)
     */
    private $status = self::STATUS_BEING_EVALUATED;

    /**
     * @var integer
     *
     * @ORM\Column(name="success", type="integer", nullable=false)
     */
    private $success = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="info", type="text", nullable=false)
     */
    private $info = '';

    /**
     * @var string
     *
     * @ORM\Column(name="outputFile", type="string", nullable=false)
     */
    private $outputfile = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="rating", type="integer", nullable=false)
     */
    private $rating = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="explanation", type="text", nullable=false)
     */
    private $explanation = '';

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $similarityStatus = self::SIMILARITY_STATUS_NEW;


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
     * Set submissionFile
     *
     * @param string $submissionFile
     * @return Submission
     */
    public function setSubmissionFile($submissionFile)
    {
        $this->submissionFile = $submissionFile;

        return $this;
    }

    /**
     * Get submissionFile
     *
     * @return string 
     */
    public function getSubmissionFile()
    {
        return $this->submissionFile;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Submission
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Submission
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set success
     *
     * @param integer $success
     * @return Submission
     */
    public function setSuccess($success)
    {
        $this->success = $success;

        return $this;
    }

    /**
     * Get success
     *
     * @return integer
     */
    public function getSuccess()
    {
        return $this->success;
    }

    /**
     * Set info
     *
     * @param string $info
     * @return Submission
     */
    public function setInfo($info)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * Get info
     *
     * @return string 
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Set outputfile
     *
     * @param string $outputfile
     * @return Submission
     */
    public function setOutputfile($outputfile)
    {
        $this->outputfile = $outputfile;

        return $this;
    }

    /**
     * Get outputfile
     *
     * @return string 
     */
    public function getOutputfile()
    {
        return $this->outputfile;
    }

    /**
     * Set rating
     *
     * @param integer $rating
     * @return Submission
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return integer 
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set explanation
     *
     * @param string $explanation
     * @return Submission
     */
    public function setExplanation($explanation)
    {
        $this->explanation = $explanation;

        return $this;
    }

    /**
     * Get explanation
     *
     * @return string 
     */
    public function getExplanation()
    {
        return $this->explanation;
    }

    /**
     * Set assignment
     *
     * @param \Assignment $assignment
     * @return Submission
     */
    public function setAssignment(\Assignment $assignment = null)
    {
        $this->assignment = $assignment;

        return $this;
    }

    /**
     * Set user
     *
     * @param \User $user
     * @return Submission
     */
    public function setUser(\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \User 
     */
    public function getUser()
    {
        return $this->user;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->documents = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add documents
     *
     * @param \Document $documents
     * @return Submission
     */
    public function addDocument(\Document $documents)
    {
        $this->documents[] = $documents;

        return $this;
    }

    /**
     * Remove documents
     *
     * @param \Document $documents
     */
    public function removeDocument(\Document $documents)
    {
        $this->documents->removeElement($documents);
    }

    /**
     * Get documents
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * Set similarityStatus
     *
     * @param string $similarityStatus
     * @return Submission
     */
    public function setSimilarityStatus($similarityStatus)
    {
        $this->similarityStatus = $similarityStatus;
    
        return $this;
    }

    /**
     * Get similarityStatus
     *
     * @return string 
     */
    public function getSimilarityStatus()
    {
        return $this->similarityStatus;
    }
}
