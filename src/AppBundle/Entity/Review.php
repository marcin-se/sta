<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ReviewRepository")
 * @ORM\Table(name="review")
 */
class Review
{
	/**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $body;
    
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $score;
    
    /**
     * @ORM\ManyToMany(targetEntity="AnalysisResult", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="reviews_analysis_results",
     *      joinColumns={@ORM\JoinColumn(name="review_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="analysis_result_id", referencedColumnName="id", unique=true)}
     *      )
     */
    private $analysisResults;

    public function __construct()
    {
        $this->analysisResults = new \Doctrine\Common\Collections\ArrayCollection();
    }


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
     * Set body
     *
     * @param string $body
     *
     * @return Review
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set score
     *
     * @param integer $score
     *
     * @return Review
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
     * Set analysisResults
     *
     * @return Review
     */
    public function setAnalysisResults($analysisResults)
    {
        $this->analysisResults = $analysisResults;

        return $this;
    }

    /**
     * Get analysisResults
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAnalysisResults()
    {
        return $this->analysisResults;
    }
}
