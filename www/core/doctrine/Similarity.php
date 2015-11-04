<?php
use Doctrine\ORM\Mapping as ORM;
/**
 * Similarity
 *
 * @ORM\Table(name="similarities")
 * @ORM\Entity
 */
class Similarity {
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="Submission")
     * @ORM\JoinColumn(name="oldSubmissionId", referencedColumnName="id")
     */
    private $oldSubmission;
    /**
     * @ORM\ManyToOne(targetEntity="Submission")
     * @ORM\JoinColumn(name="newSubmissionId", referencedColumnName="id")
     */
    private $newSubmission;
    /**
     * @ORM\Column(type="integer")
     */
    private $score;
    /**
     * @ORM\Column(type="text")
     */
    private $details;
    /**
     * @ORM\Column(type="boolean")
     */
    private $suspicious;

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
     * Set score
     *
     * @param integer $score
     * @return Similarity
     */
    public function setScore($score)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get score
     *
     * @return integer 
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Set details
     *
     * @param string $details
     * @return Similarity
     */
    public function setDetails($details)
    {
        $this->details = $details;

        return $this;
    }

    /**
     * Get details
     *
     * @return string 
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Set oldSubmission
     *
     * @param \Submission $oldSubmission
     * @return Similarity
     */
    public function setOldSubmission(\Submission $oldSubmission = null)
    {
        $this->oldSubmission = $oldSubmission;

        return $this;
    }

    /**
     * Get oldSubmission
     *
     * @return \Submission 
     */
    public function getOldSubmission()
    {
        return $this->oldSubmission;
    }

    /**
     * Set newSubmission
     *
     * @param \Submission $newSubmission
     * @return Similarity
     */
    public function setNewSubmission(\Submission $newSubmission = null)
    {
        $this->newSubmission = $newSubmission;

        return $this;
    }

    /**
     * Get newSubmission
     *
     * @return \Submission 
     */
    public function getNewSubmission()
    {
        return $this->newSubmission;
    }

    /**
     * Set suspicious
     *
     * @param boolean $suspicious
     * @return Similarity
     */
    public function setSuspicious($suspicious)
    {
        $this->suspicious = $suspicious;

        return $this;
    }

    /**
     * Get suspicious
     *
     * @return boolean 
     */
    public function getSuspicious()
    {
        return $this->suspicious;
    }
}
