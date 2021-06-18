<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Form\PinType;
use App\Repository\PinRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


class MonController extends AbstractController
{
    private $em;

    // public function __construct(EntityManagerInterface $em)
    // {
    //     $this->em = $em;
    // }
  
    /*
    public function index(EntityManagerInterface $em):Response
    {
        // $pin = new Pin();
        // $pin->setTitle('Title 5');
        // $pin->setDescription('Description 5');
        // $em = ($this->getDoctrine()->getManager());
        // $em->persist($pin);
        // $em->flush();
        //dump($pin);

        // $repo = $em->getRepository('App\Entity\Pin');
        
        // $repo = $em->getRepository('App:Pin');
        $repo = $em->getRepository(Pin::class);
       $pins = $repo->findAll();

        //return $this->render('pins/index.html.twig', ['pins' => $pins]);

        return $this->render('pins/index.html.twig', compact('pins'));

    }
    */

    /**
     * @Route("/", name="app_home", methods={"GET"})
     */

    public function index(PinRepository $repo):Response
    {

        $pins = $repo->findBy([], ['createdAt' => 'DESC']);

        return $this->render('pins/index.html.twig', ['pins'=> $pins]);

        // return $this->render('pins/index.html.twig', ['pins'=> $repo->findAll()]);
    }

    /**
     * @Route("/pins/{id<[0-9]+>}", name="app_pins_show", methods={"GET"})
     */

    // public function show(PinRepository $repo, int $id):Response {
    public function show(Pin $pin):Response {

        
        //  $pin = $repo->find($id);
        //  if(!$pin){
        //      throw $this->createNotFoundException('Pin #'. $id . ' not foud ! ');
        //  }
         
         return $this->render('pins/show.html.twig', compact('pin'));

    }

     /**
     * @Route("/pins/{id<[0-9]+>}/edit", name="app_pins_edit", methods={"GET", "POST"})
     */

    public function edit(Pin $pin, Request $request, EntityManagerInterface $em):Response
    
    {


        $form = $this->createForm(PinType::class, $pin);

        //     ->add('title', TextType::class,[
        //     'attr' => ['autofocus' => true]])
        //     ->add('description', TextareaType::class, ['attr' => ['rows' => 7, 'col' => 60]])
        //     ->getForm()
        // ;

          $form->handleRequest($request);

           if($form->isSubmitted() && $form->isValid()){
        
            $em->flush();

            // return $this->redirectToRoute('app_home');
            return $this->redirectToRoute('app_home');
           }

         return $this->render('pins/edit.html.twig', [
             
            'pin' => $pin,
            'form' => $form->createView()]);
    }

       /**
        * @Method("DELETE")
     * @Route("/pins/{id<[0-9]+>}/delete", name="app_pins_delete")
     */

    public function delete(Request $request, Pin $pin, EntityManagerInterface $em):Response
    
    {


        if($this->isCsrfTokenValid('pins_deletion_'.$pin->getId(), $request->request->get('csrf_token'))){

                 $em->remove($pin);
                 $em->flush();

        }
       
         return $this->redirectToRoute('app_home');
    }

    /**
     * @Route("/pins/create",  methods={"GET", "POST"}, name="app_pins_create"))
     */
    public function create(Request $request, EntityManagerInterface $em): Response

    {

        /*
        if($request->isMethod('POST')){
            $data = $request->request->all();

            if($this->isCsrfTokenValid('pins_create', $data['_token'])){

                  $pin = new Pin;
            $pin->setTitle( $data['title']);
            $pin->setDescription( $data['description']);
            $em->persist($pin);
            $em->flush();

            }
          
           
              // return $this->redirect('/');
              // return $this->redirect($this->generateUrl('app_home'));
              return $this->redirectToRoute('app_home');

        }
        */

        $pin = new Pin;

        $form = $this->createForm(PinType::class, $pin);

        //     ->add('title', TextType::class,
        //     [
        //     //'required' => false,
        //     'attr' => ['autofocus' => true]])

        //     ->add('description', TextareaType::class, ['attr' => ['rows' => 7, 'col' => 60]])
        //     //->add('submit',SubmitType::class, ['label' => 'Créer un pin'] )
        //     ->getForm()
        // ;

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
           
            // $data_title = $form->get('title')->getData();
            //$data = $form->getData();

            // $pin = new Pin;
            // $pin->setTitle($data['title']);
            // $pin->setDescription($data['description']);

            // $em->persist($pin);
            $em->persist($pin);
            $em->flush();

            // return $this->redirectToRoute('app_home');
            return $this->redirectToRoute('app_pins_show', ['id'=> $pin->getId()]);
        }
       
        return $this->render('pins/create.html.twig', ['form'=> $form->createView()]);
    }
}
