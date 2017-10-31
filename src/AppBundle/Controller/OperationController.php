<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Operation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OperationController extends Controller
{
    /**
     * @param Request $request
     * @Template()
     * @Route("/", name="homepage")
     * @return array
     */
    public function indexAction(Request $request)
    {
        $operations = $this->getDoctrine()->getRepository('AppBundle:Operation')->findBy([],['date' => 'DESC']);

        $balans = $this->getDoctrine()->getRepository('AppBundle:Operation')->getBalans();
        $formOperration =$this->createForm('AppBundle\Form\OperationType')
            ->add('Edit', SubmitType::class);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $operations, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );
        return ['pagination' => $pagination, 'balans' => $balans, 'form'=>$formOperration->createView()];
        // replace this example code with whatever you need

    }

    /**
     * @param Request $request
     * @Template()
     * @Route("/add", name="operation_add")
     * @return array | Response
     */
    public function addOperationAction(Request $request)
    {
        $form = $this->createForm('AppBundle\Form\OperationType')
            ->add('Add', SubmitType::class);
        $form->handleRequest($request);

        return['form' => $form->createView()];
    }

    /**
     * @param Request $request
     * @Template()
     * @Route("/interval", name="operation_interval")
     * @return array
     */
    public function intervalAction(Request $request)
    {
        $formSearch = $this->createFormBuilder()
            ->add('From', DateType::class, array( //сьогоднішня дата
                'data' => new \DateTime()),
                null, array(
                    "input" => (DateTime::class)))
            ->add('To', DateType::class, array(
                'data' => new \DateTime()),
                null, array(
                "input" => (DateTime::class)))
            ->add('Search', SubmitType::class)
            ->getForm();

        $formOperration =$this->createForm('AppBundle\Form\OperationType')
            ->add('Edit', SubmitType::class);

        $formSearch->handleRequest($request);
        if ($formSearch->isValid() && $formSearch->isSubmitted())
        {

            $info = $formSearch->getData();
            $data = $this->getDoctrine()->getRepository('AppBundle:Operation')->getSearch($info);
            if($data['operations']){
                dump($data['operations']);
                $count = count($data['operations']);
                $this->addFlash('success', "Search success. Found $count operations");
            }else{
                $this->addFlash('fail', "Search fail. Operations not found");
            }

            return['search_form' => $formSearch->createView(), 'form'=>$formOperration->createView(), 'data' => $data];

        }

        return['search_form' => $formSearch->createView(), 'form'=>$formOperration->createView(), 'data' => null];
    }


    //create and edit controller
    /**
     * @Route("/create", name = "operation_create")
     * @param Request $request
     * @return JsonResponse
     */
    public function createAction(Request $request)
    {
        dump($request);
        if($request->isMethod('POST')){
            $id = ($request->get('appbundle_operation'))['id'];
            $text = ($request->get('appbundle_operation'))['text'];
            $valueUAH = ($request->get('appbundle_operation'))['valueUAH'];
            $valueUSD = ($request->get('appbundle_operation'))['valueUSD'];
            $data = ($request->get('appbundle_operation'))['date'];
            $date = (new \DateTime()) //to datetime object format
                ->setDate($data['date']['year'],$data['date']['month'],$data['date']['day'])
                ->setTime($data['time']['hour'],$data['time']['minute']);
            if ($id){   //edit
                $operation = $this->getDoctrine()->getRepository('AppBundle:Operation')->find($id);
                if($operation){
                    $operation->setText($text)
                        ->setValueUAH($valueUAH)
                        ->setValueUSD($valueUSD)
                        ->setDate($date);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($operation);
                    $em->flush();
                    $response = new JsonResponse('Operation edit',200);
                }else{
                    $response = new JsonResponse('Operation not found',404);
                }
            }else{ //create
                if ($text && $valueUAH && $valueUSD && $date){
                    $operation = (new Operation())
                        ->setText($text)
                        ->setValueUAH($valueUAH)
                        ->setValueUSD($valueUSD)
                        ->setDate($date);

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($operation);
                    $em->flush();
                    $response = new JsonResponse('Operation add',200);
                }else{
                    $response = new JsonResponse('Form not valid',412);
                }
            }
        }else{ //not post method form
            $response = new JsonResponse('Not post method',400);
        }

        return $response;
    }

    /**
     * @Route("delete-{id}", name="operation_delete")
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteAction(Request $request)
    {
        $tag = $this->getDoctrine()->getRepository('AppBundle:Operation')->find($request->get('id'));
        if (!$tag){
            $data = "Operation not found";
            $code = 404;
        }else{
            $this->getDoctrine()->getManager()->remove($tag);
            $this->getDoctrine()->getManager()->flush();
            $data = "Operation delete";
            $code = 200;
        }
        $response = new JsonResponse($data, $code);
        return $response;
    }

}
