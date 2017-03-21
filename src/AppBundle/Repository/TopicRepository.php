<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class TopicRepository extends EntityRepository {

    public function findNames(){
        $em = $this->getEntityManager();
        $result = $em->createQuery("SELECT t.name FROM AppBundle:Topic t")->getScalarResult();
        return array_map('current', $result); 
    }

    public function deleteAll(){
        $em = $this->getEntityManager();
        $topics = $em->getRepository('AppBundle:Topic')->findAll();
        foreach($topics as $topic){
            $this->delete($topic->getId());
        } 
    }

    public function delete($topicId){
		$em = $this->getEntityManager();
		$this->deleteAnalysisResult($topicId, $em);
        $topic = $this->find($topicId);
        $em->remove($topic);
        $em->flush();
    }

    /*
    * Removes analysis result related to a given topic
    */
    private function deleteAnalysisResult($topicId, $em){
        $sql = "DELETE FROM reviews_analysis_results WHERE analysis_result_id 
        IN (SELECT id FROM analysis_result WHERE topic_id = :id)";
        $params = array('id'=>$topicId);
        $stmt = $em->getConnection()->prepare($sql)->execute($params);

        $sql = "DELETE FROM analysis_result WHERE topic_id = :id" ;
        $params = array('id'=>$topicId);
        $stmt = $em->getConnection()->prepare($sql)->execute($params);
    }
}