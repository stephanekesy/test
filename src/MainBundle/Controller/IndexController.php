<?php

namespace MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;

class IndexController extends Controller
{
    public function indexAction()
    {

    	$s_username	= '';
		$s_password	= '';
    	$session 	= $this->getRequest()->getSession();
        $request 	= $this->get('request');
        if ($request->getMethod() == 'POST') {

        	$s_username	= trim($request->request->get('txt_username'));
			$s_password	= trim($request->request->get('txt_password'));
			
			if($s_username == '') {
				return $this->render('MainBundle:Index:index.html.twig',array('s_error' => 'Username is required','s_username' => $s_username));
			} else if($s_password == '') {
				return $this->render('MainBundle:Index:index.html.twig',array('s_error' => 'Password is required','s_username' => $s_username));
			}

			$a_parameters = array('username' 	=> $s_username,
								  'password'	=> $s_password);

			$doctrine 	= $this->getDoctrine()->getManager();
			$user 		= $doctrine->getRepository('MainBundle\Entity\User')->findOneBy($a_parameters);

			if($user) {

				$s_expiration_date 	= date('Y-m-d',strtotime($user->getExpirationDate()->format('Y-m-d')));
				$s_current_date 	= date('Y-m-d');

				if($s_expiration_date != '' && $s_expiration_date <= $s_current_date) {

					return $this->render('MainBundle:Index:index.html.twig',array('s_error' => 'Your account is expired','s_username' => $s_username));

				} else {

					//clear previous session
					$session->clear();

					// create the session
					$session = new Session();
					$session->set('i_id',$user->getId());
					$session->set('s_first_name',$user->getFirstName());
					$session->set('s_last_name',$user->getLastName());

					return $this->redirectToRoute('therapeutic_area_user');				
				}	
			} else {
				return $this->render('MainBundle:Index:index.html.twig',array('s_error' => 'Invalid Credentials','s_username' => $s_username));
			}
        }

        if(!$session->has('i_id')) {
			return $this->render('MainBundle:Index:index.html.twig',array('s_error' => '','s_username' => $s_username));
		} else {
			return $this->redirectToRoute('therapeutic_area_user');
		}
    }

    public function logoutAction()
    {
    	//clear previous session
		$session = $this->getRequest()->getSession();
		$session->clear();
		return $this->redirectToRoute('admin');
    }
}
