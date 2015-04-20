<?php

// namespace
namespace MainBundle\Controller;

//entiry classes
use MainBundle\Entity\TherapeuticArea;

//required classes
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class TherapeuticAreaController extends Controller
{
	public function indexAction()
	{
		$session = $this->getRequest()->getSession();
		if(!$session->has('i_id')) {
			return $this->redirectToRoute('admin');
		}
		return $this->render('MainBundle:TherapeuticArea:index.html.twig');
	}

	public function addEditAction(Request $request)
	{
		try {

			$i_therapeutic_area_id = trim($request->request->get('hid_therapeutic_area_id'));

			// edit therapeutic area
			if($i_therapeutic_area_id > 0) {

				$s_therapeutic_area_name 		= trim($request->request->get('txt_edit_therapeutic_area'));
				$s_hidden_therapeutic_area_name = trim($request->request->get('hid_edit_therapeutic_area'));

				if($s_therapeutic_area_name == '') {

					$a_response['s_status'] = 'error';
					$a_response['data']     = 'Please enter the therapeutic area name';

				} else {

					$repository = $this->getDoctrine()->getRepository('MainBundle\Entity\TherapeuticArea');

					$a_set_parameters = array(
											'id' 	=> $i_therapeutic_area_id,
											'name' 	=> $s_therapeutic_area_name
										);

					$query = $repository->createQueryBuilder('therapeutic_area')
							->where('therapeutic_area.id != :id')
							->andWhere('therapeutic_area.name = :name')
							->setParameters($a_set_parameters)
							->getQuery();
					$therapeutic_area = $query->getOneOrNullResult();

					if($therapeutic_area) {

						$a_response['s_status'] = 'error';
						$a_response['data']     = 'This therapeutic area ' . $s_therapeutic_area_name . " is already exists";

					} else {

						//update in database
						$doctrine 			= $this->getDoctrine()->getManager();
						$therapeutic_area 	= $doctrine->getRepository('MainBundle\Entity\TherapeuticArea')->find($i_therapeutic_area_id);
						$therapeutic_area->setName($s_therapeutic_area_name);
						$doctrine->persist($therapeutic_area);
						$doctrine->flush();

						// //update msl therapeuticArea column if exists
						// $repository = $this->getDoctrine()->getRepository('MainBundle\Entity\Msl');
						// $msl = $repository->findBy(array(),array('firstName' => 'ASC'));
						// foreach ($msl as $msl_row) {
						// 	if(trim($msl_row->getTherapeuticArea()) != '') {
						// 		//update msl in database
						// 		$s_edit_territory_name = '';
						// 		$msl = $repository->findOneBy(array('email' => $msl_row->getEmail()));
						// 		$s_edit_territory_name = str_replace($s_hidden_therapeutic_area_name,$s_therapeutic_area_name,$msl_row->getTherapeuticArea());
						// 		$msl->setTherapeuticArea($s_edit_territory_name);
						// 		$doctrine->persist($msl);
						// 		$doctrine->flush();
						// 	}
						// }

						$a_response['s_status'] = 'success';
						$a_response['data']     = '';

					}	
				}

			} else {	

				// add therapeutic area
				$s_therapeutic_area_name = trim($request->request->get('txt_therapeutic_area'));

				if($s_therapeutic_area_name == '') {

					$a_response['s_status'] = 'error';
					$a_response['data']     = 'Please enter the therapeutic area name';

				} else {

					$doctrine = $this->getDoctrine()->getManager();
					$therapeutic_area = $doctrine->getRepository('MainBundle\Entity\TherapeuticArea')->findBy(array('name' => $s_therapeutic_area_name));

					if(count($therapeutic_area) > 0) {

						$a_response['s_status'] = 'error';
						$a_response['data']     = 'This therapeutic area ' . $s_therapeutic_area_name . " is already exists";

					} else {

						//store in database
						//$doctrine = $this->getDoctrine()->getManager();
						$therapeutic_area = new TherapeuticArea();
						$therapeutic_area->setName($s_therapeutic_area_name);
						$doctrine->persist($therapeutic_area);
						$doctrine->flush();

						$a_response['s_status'] = 'success';
						$a_response['data']     = 'Record is added successfully';
					}
				}
			}	

		} catch(Exception $e) {

			$a_response['s_status'] = 'error';
			$a_response['data']     = $e->getMessage();

		}

		return new JsonResponse($a_response);
		die;
	}

	public function getListAction()
	{
		try {	

			$repository = $this->getDoctrine()->getRepository('MainBundle\Entity\TherapeuticArea');
			$therapeutic_area = $repository->findBy(array(),array('name' => 'ASC'));

			$template = $this->renderView('MainBundle:TherapeuticArea:list.html.twig',array('therapeutic_area'=> $therapeutic_area));

			$a_response['s_status'] = 'success';
			$a_response['data']     = $template;
			$a_response['i_record']  = count($therapeutic_area);

		} catch(Exception $e) {

			$a_response['s_status'] = 'error';
			$a_response['data']     = $e->getMessage();

		}

		return new JsonResponse($a_response);
		die;
	}

}