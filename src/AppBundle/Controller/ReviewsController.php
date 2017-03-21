<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Controller\BaseController;
use AppBundle\Form\ReviewType;
use AppBundle\Entity\Review;
use AppBundle\Entity\AnalsysisResult;
use AppBundle\CsvParser\ReviewCsvParser;
use AppBundle\TextAnalysis\AnalysisInterpreter;
use AppBundle\TextAnalysis\DependencyParserResult;


class ReviewsController extends BaseController
{	
	 /**
     * @Route("/reviews")
     */
    public function showAction(Request $request) {
    	$addForm = $this->prepareAddForm($request, ReviewType::class, new Review());
    	$uploadForm = $this->prepareUploadForm($request, new ReviewCsvParser());

		return $this->render('reviews.html.twig', 
			array('uploadForm' => $uploadForm->createView(),
				  'addForm' => $addForm->createView()));
    }

   	/**
     * @Route("/rest/reviews")
     * @Method("GET")
     */
    public function getAction(Request $request){
		$reviews = $this->getDoctrine()->getRepository('AppBundle:Review')->findAll();
		$payload = array();

		foreach ($reviews as $review) {
			$totalScore = 0;
			array_push($payload, array(
                'id' => $review->getId(),
				'review' => $review->getBody(),
				'analysisResults' => $this->prepareAnalysisResultsView($review->getAnalysisResults(), $totalScore),
				'score' => $totalScore)); 
		}
		$response = new JsonResponse();
		$response->setData($payload);
		return $response;
    }

    private function prepareAnalysisResultsView($analysisResults, &$totalScore){
    	$result = array();
    	$totalScore = 0;
    	foreach ($analysisResults as $ar) {
    		$partialScore = $ar->getAdjective()->getWeight();
            $partialScore = $ar->isNegated() ? $partialScore * -1 : $partialScore;
    		$totalScore += $partialScore;
            $topicName = ( $ar->getTopic() != null ? $ar->getTopic()->getName() : "" );
    		$result_tmp = array('topic'=> $topicName,
    							'adjective'=> $ar->getAdjective()->getName(),
    							'score'=> $partialScore,
                                'isNegated'=> $ar->isNegated());
    		array_push($result, $result_tmp);
    	}
    	return $result;
    }

    /**
     * @Route("/rest/reviews")
     * @Method("DELETE")
     */
    public function deleteAllAction(Request $request){
    	$em = $this->getDoctrine()->getManager();
		$em->getRepository('AppBundle:Review')->deleteAll();
		$response = new Response(null, Response::HTTP_OK);
		return $response;
    }

    /**
     * @Route("/reviews/calculate")
     */
    public function calculateScoringAction() {
    	$this->deleteAnalysisResultsAction();

		$reviews = $this->getDoctrine()->getRepository('AppBundle:Review')->findAll();
        $topics = $this->getDoctrine()->getRepository('AppBundle:Topic')->findAll();
        $adjectives = $this->getDoctrine()->getRepository('AppBundle:Adjective')->findAll();

        $textAnalysisService = $this->get('text_analysis');

    	foreach ($reviews as $review) {
            $analysisResults = $textAnalysisService->getAnalysisResults($review->getBody());
            $review->setAnalysisResults($analysisResults);
            $em = $this->getDoctrine()->getManager();
            $em->persist($review);
            $em->flush();
    	}

		$response = new Response(null, Response::HTTP_OK);
		return $response;
    }

    /**
    * @Route("/rest/analysis")
    * @Method("POST")
    */
    public function analysisAction(Request $request){
        $review = $request->get('review');

        $topics = $this->getDoctrine()->getRepository('AppBundle:Topic')->findAll();
        $adjectives = $this->getDoctrine()->getRepository('AppBundle:Adjective')->findAll();

        $textAnalysisService = $this->get('text_analysis');
        $result = $textAnalysisService->getDependencies($review);

        $response = new JsonResponse();
        $response->setData($result);
        return $response;
    }

    /**
     * @Route("/reviews/delete_analysis")
     */
	public function deleteAnalysisResultsAction() {
		$em = $this->getDoctrine()->getManager();
		$em->getRepository('AppBundle:Review')->deleteAnalysisResults();
		$response = new Response(null, Response::HTTP_OK);
        return $response;
    }
}