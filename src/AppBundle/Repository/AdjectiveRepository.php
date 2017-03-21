<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class AdjectiveRepository extends EntityRepository {
    
    public function findNames(){
        $em = $this->getEntityManager();
        $result = $em->createQuery("SELECT a.name FROM AppBundle:Adjective a")->getScalarResult();
        return array_map('current', $result); 
    }

    public function deleteAll(){
        $em = $this->getEntityManager();
        $adjectives = $em->getRepository('AppBundle:Adjective')->findAll();
        foreach($adjectives as $adjective){
            $this->delete($adjective->getId());
        } 
    }

    public function delete($adjectiveId){
        $em = $this->getEntityManager();
        $this->deleteAnalysisResult($adjectiveId, $em);
        $adjective = $this->find($adjectiveId);
        $em->remove($adjective);
        $em->flush();
    }

    /*
    * Removes analysis result related to a given adjective
    */
    private function deleteAnalysisResult($adjectiveId, $em){
        $sql = "DELETE FROM reviews_analysis_results WHERE analysis_result_id 
        IN (SELECT id FROM analysis_result WHERE adjective_id = :id)";
        $params = array('id'=>$adjectiveId);
        $stmt = $em->getConnection()->prepare($sql)->execute($params);

        $sql = "DELETE FROM analysis_result WHERE adjective_id = :id" ;
        $params = array('id'=>$adjectiveId);
        $stmt = $em->getConnection()->prepare($sql)->execute($params);
    }
}