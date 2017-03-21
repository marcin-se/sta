<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ReviewRepository extends EntityRepository {
    
    public function deleteAll(){
        $em = $this->getEntityManager();

        $this->deleteAnalysisResults();

        $query = $em->createQuery('DELETE AppBundle:Review');
        $query->execute();

        $em->flush();
    }

    public function deleteAnalysisResults() {
    	$em = $this->getEntityManager();

        $sql = "DELETE FROM reviews_analysis_results";
        $stmt = $em->getConnection()->prepare($sql)->execute();

        $sql = "DELETE FROM analysis_result";
        $stmt = $em->getConnection()->prepare($sql)->execute();

        $em->flush();
    }
}