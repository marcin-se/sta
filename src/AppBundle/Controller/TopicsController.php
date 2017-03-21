<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Topic;
use AppBundle\Form\TopicType;
use AppBundle\CsvParser\TopicCsvParser;

class TopicsController extends BaseController
{
	 /**
     * @Route("/topics")
     */
    public function showAction(Request $request) {
    	$addForm = $this->prepareAddForm($request, TopicType::class, new Topic());
    	$uploadForm = $this->prepareUploadForm($request, new TopicCsvParser());

		return $this->render('topics.html.twig', 
			array('uploadForm' => $uploadForm->createView(),
				  'addForm' => $addForm->createView()));
    }
	
    /**
     * @Route("/rest/topics")
     * @Method("GET")
     */
    public function getAction(Request $request) {
    	$topics = $this->getDoctrine()->getRepository('AppBundle:Topic')->findAll();

        $payload = array();
        foreach($topics as $t) {
            array_push($payload, array('id' => $t->getId(), 'name' => $t->getName()));
        }
        
        $response = new JsonResponse();
        $response->setData($payload);

        return $response;
    }

    /**
     * @Route("/rest/topics")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request){
        $rep = $this->getDoctrine()->getManager()->getRepository('AppBundle:Topic');

        $topicId = $request->get('id');
        if ($topicId != null) {
            $rep->delete($topicId);
        } else {
            $rep->deleteAll();
        }
        $response = new Response(null, Response::HTTP_OK);
        return $response;
    }
}