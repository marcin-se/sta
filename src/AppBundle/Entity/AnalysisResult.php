<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="analysis_result")
 */
class AnalysisResult
{
	/**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Topic")
     * @ORM\JoinColumn(name="topic_id", referencedColumnName="id")
     */
    private $topic;

	/**
     * @ORM\ManyToOne(targetEntity="Adjective")
     * @ORM\JoinColumn(name="adjective_id", referencedColumnName="id")
     */
    private $adjective;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $negated;
    

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
     * Set topic
     *
     * @param \AppBundle\Entity\Topic $topic
     *
     * @return AnalysisResult
     */
    public function setTopic(\AppBundle\Entity\Topic $topic = null)
    {
        $this->topic = $topic;

        return $this;
    }

    /**
     * Get topic
     *
     * @return \AppBundle\Entity\Topic
     */
    public function getTopic()
    {
        return $this->topic;
    }

    /**
     * Set adjective
     *
     * @param \AppBundle\Entity\Adjective $adjective
     *
     * @return AnalysisResult
     */
    public function setAdjective(\AppBundle\Entity\Adjective $adjective = null)
    {
        $this->adjective = $adjective;

        return $this;
    }

    /**
     * Get adjective
     *
     * @return \AppBundle\Entity\Adjective
     */
    public function getAdjective()
    {
        return $this->adjective;
    }

    /**
     * Set isNegated
     *
     * @return boolean
     */
    public function setNegated($negated)
    {
        $this->negated = $negated;

        return $this;
    }

    /**
     * Get isNegated
     *
     * @return boolean
     */
    public function isNegated()
    {
        return $this->negated;
    }
}
