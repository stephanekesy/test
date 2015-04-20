<?php

// namespace
namespace MainBundle\Controller;

//entiry classes
use MainBundle\Entity\Msl;

//required classes
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class MslController extends Controller
{

	public $a_gender 	= array('Female', 'Male');

	public function indexAction()
	{
		return $this->render('MainBundle:Msl:index.html.twig');
	}

	public function addEditAction(Request $request)
	{
		try {	

			$i_msl_id = trim($request->request->get('hid_msl_id'));

			// edit msl
			if($i_msl_id > 0) {

				$s_edit_first_name 		= trim($request->request->get('txt_edit_first_name'));
				$s_edit_last_name 		= trim($request->request->get('txt_edit_last_name'));
				$s_edit_email 			= trim($request->request->get('txt_edit_email'));
				$s_edit_gender 			= trim($request->request->get('slt_edit_gender'));
				$s_edit_role 			= trim($request->request->get('slt_edit_role'));
				$s_edit_territory_name 	= trim($request->request->get('txt_edit_territory_name'));

				//email address validation
				if(!filter_var($s_edit_email, FILTER_VALIDATE_EMAIL)){	

					$a_response['s_status'] = 'error';
					$a_response['data']     = 'Email address is not valid';

				} else {	
					$repository = $this->getDoctrine()->getRepository('MainBundle\Entity\Msl');

					$a_set_parameters = array(
											'id' 	=> $i_msl_id,
											'email' => $s_edit_email
										);

					$query = $repository->createQueryBuilder('msl')
							->where('msl.id != :id')
							->andWhere('msl.email = :email')
							->setParameters($a_set_parameters)
							->getQuery();
					$msl = $query->getOneOrNullResult();

					if($msl) {

						$a_response['s_status'] = 'error';
						$a_response['data']     = 'This email ' . $s_edit_email . " is already exists";

					} else {

						//Therapeutical Areas Assigning
						$a_therapeutical_areas = $request->request->get('chk_therapeutic_area');
						$s_therapeutical_areas = '';

						if($a_therapeutical_areas) {
							$s_therapeutical_areas = implode(',', $a_therapeutical_areas);
						}
						
						//update in database
						$doctrine = $this->getDoctrine()->getManager();
						$msl = $doctrine->getRepository('MainBundle\Entity\Msl')->find($i_msl_id);
						$msl->setFirstName($s_edit_first_name);
						$msl->setLastName($s_edit_last_name);
						$msl->setEmail($s_edit_email);
						$msl->setGender($s_edit_gender);
						$msl->setRole($s_edit_role);
						$msl->setMslTerritory($s_edit_territory_name);
						$msl->setTherapeuticArea($s_therapeutical_areas);
						$doctrine->persist($msl);
						$doctrine->flush();

						$a_response['s_status'] = 'success';
						$a_response['data']     = '';

					}
				}	

			} else {	

				// add msl
				$s_msl_first_name 	= trim($request->request->get('txt_first_name'));
				$s_msl_last_name 	= trim($request->request->get('txt_last_name'));
				$s_msl_email 		= trim($request->request->get('txt_email'));

				if($s_msl_first_name == '') {

					$a_response['s_status'] = 'error';
					$a_response['data']     = 'Please enter the first name';

				} else if($s_msl_last_name == '') {

					$a_response['s_status'] = 'error';
					$a_response['data']     = 'Please enter the last name';

				} else if($s_msl_email == '') {

					$a_response['s_status'] = 'error';
					$a_response['data']     = 'Please enter the email';

				} else if(!filter_var($s_msl_email, FILTER_VALIDATE_EMAIL)){	
					//email address validation
					$a_response['s_status'] = 'error';
					$a_response['data']     = 'Email address is not valid';

				} else {

					$doctrine = $this->getDoctrine()->getManager();
					$msl = $doctrine->getRepository('MainBundle\Entity\Msl')->findBy(array('email' => $s_msl_email));

					if(count($msl) > 0) {

						$a_response['s_status'] = 'error';
						$a_response['data']     = 'This email ' . $s_msl_email . " is already exists";

					} else {

						//store in database
						$doctrine = $this->getDoctrine()->getManager();
						$msl = new Msl();
						$msl->setFirstName($s_msl_first_name);
						$msl->setLastName($s_msl_last_name);
						$msl->setEmail($s_msl_email);
						$msl->setGender('Male');
						$msl->setRole('MSL');
						$msl->setMslTerritory('');
						$msl->setTherapeuticArea('');
						$doctrine->persist($msl);
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

			$repository = $this->getDoctrine()->getRepository('MainBundle\Entity\Msl');
			$msl = $repository->findBy(array(),array('firstName' => 'ASC'));

			$template = $this->renderView('MainBundle:Msl:list.html.twig',array('msl'=> $msl));

			$a_response['s_status'] = 'success';
			$a_response['data']     = $template;
			$a_response['i_record']  = count($msl);

		} catch(Exception $e) {

			$a_response['s_status'] = 'error';
			$a_response['data']     = $e->getMessage();

		}

		return new JsonResponse($a_response);
		die;
	}

	public function getMslDetailsAction(Request $request)
	{
		try {	

			$i_msl_id = trim($request->request->get('msl_id'));

			$a_set_parameters = array(
									'id' 	=> $i_msl_id
								);
			$msl = $this->getDoctrine()->getRepository('MainBundle\Entity\Msl')->find($i_msl_id);

			//get msl role list
			$repository = $this->getDoctrine()->getRepository('MainBundle\Entity\MslRole');
			$msl_role = $repository->findBy(array(),array('id' => 'ASC'));

			// get therapeutic area lis
			$repository = $this->getDoctrine()->getRepository('MainBundle\Entity\TherapeuticArea');
			$therapeutic_area = $repository->findBy(array(),array('name' => 'ASC'));

			$a_user_therapeutic_area = array();
			if($msl->getTherapeuticArea() != '') {
				$a_user_therapeutic_area = explode(',', $msl->getTherapeuticArea());
			}

			$template = $this->renderView('MainBundle:Msl:edit.html.twig',
						array('msl'						=> $msl,
							  'gender'					=> $this->a_gender,
							  'msl_role'				=> $msl_role,
							  'therapeutic_area'		=> $therapeutic_area,
							  'a_user_therapeutic_area'	=> $a_user_therapeutic_area
						));

			$a_response['s_status'] = 'success';
			$a_response['data']     = $template;

		} catch(Exception $e) {

			$a_response['s_status'] = 'error';
			$a_response['data']     = $e->getMessage();

		}

		return new JsonResponse($a_response);
		die;
	}

	public function deleteMslAction(Request $request)
	{
		try {	

			$i_msl_id = trim($request->request->get('msl_id'));

			$doctrine = $this->getDoctrine()->getManager();
			$msl = $this->getDoctrine()->getRepository('MainBundle\Entity\Msl')->find($i_msl_id);
			$doctrine->remove($msl);
			$doctrine->flush();

			$a_response['s_status'] = 'success';
			$a_response['data']     = '';

		} catch(Exception $e) {

			$a_response['s_status'] = 'error';
			$a_response['data']     = $e->getMessage();

		}

		return new JsonResponse($a_response);
		die;
	}

}