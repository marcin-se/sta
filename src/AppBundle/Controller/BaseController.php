<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\FileType;

use AppBundle\Entity\Review;
use AppBundle\CsvParser\CsvParserInterface;

class BaseController extends Controller {
   
    protected function prepareAddForm(Request $request, $type, $data) {
        $addForm = $this->createForm($type, $data);

        $addForm->handleRequest($request);

        if ($addForm->isSubmitted() && $addForm->isValid()) {
            $data = $addForm->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($data);
            $em->flush();
        }
        return $addForm;
    }

    protected function prepareUploadForm(Request $request, CsvParserInterface $csvParser) {
		$uploadForm = $this->createFormBuilder()
        ->add('submitFile', FileType::class, array('label' => 'File to Submit'))
        ->getForm();

        $uploadForm->handleRequest($request);

        if ($uploadForm->isSubmitted() && $uploadForm->isValid()) {
        	$file = $uploadForm->get('submitFile');

			if (($handle = fopen($file->getData()->getRealPath(), "r")) !== FALSE) {
				$em = $this->getDoctrine()->getManager();
	            while(($row = fgetcsv($handle, 0, ";")) !== FALSE) {
					$entity = $csvParser->rowToEntity($row);
					$em->persist($entity);
	            }
				$em->flush();
	        }
        }
        return $uploadForm;
    }


}