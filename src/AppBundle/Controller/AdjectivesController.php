<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Adjective;
use AppBundle\Form\AdjectiveType;
use AppBundle\CsvParser\AdjectiveCsvParser;

class AdjectivesController extends BaseController
{
	 /**
     * @Route("/adjectives")
     */
    public function showAction(Request $request) {
    	$addForm = $this->prepareAddForm($request, AdjectiveType::class, new Adjective());
    	$uploadForm = $this->prepareUploadForm($request, new AdjectiveCsvParser());		

		return $this->render('adjectives.html.twig', 
			array('uploadForm' => $uploadForm->createView(),
				  'addForm' => $addForm->createView()));
    }

    /**
     * @Route("/rest/adjectives")
     * @Method("GET")
     */
    public function getAction(Request $request){
		$adjectives = $this->getDoctrine()->getRepository('AppBundle:Adjective')->findAll();

    	$payload = array();
		foreach($adjectives as $a) {
			array_push($payload, array(
				'id' => $a->getId(), 
				'name' => $a->getName(), 
				'weight' => $a->getWeight()));
    	}

    	$response = new JsonResponse();
		$response->setData($payload);

		return $response;
    }

    /**
     * @Route("/rest/adjectives")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request){
        $rep = $this->getDoctrine()->getManager()->getRepository('AppBundle:Adjective');

        $adjectiveId = $request->get('id');
        if ($adjectiveId != null) {
            $rep->delete($adjectiveId);
        } else {
            $rep->deleteAll();
        }
        $response = new Response(null, Response::HTTP_OK);
        return $response;
    }
}