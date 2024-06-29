<?php

namespace App\Controller;

use App\Entity\Person;
use App\Form\HelloType;
use App\Form\PersonType;
use App\Repository\PersonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloController extends AbstractController
{
    /**
     * @var PersonRepository
     */
    private $personRepository;

    public function __construct(PersonRepository $personRepository)
    {
        $this->personRepository = $personRepository;
    }

    /**
     * @Route("/hello", name="hello")
     */
    public function index(Request $request): Response
    {
        $formobj = new HelloForm();
        $form = $this->createForm(HelloType::class, $formobj);
        $form->handleRequest($request);

        if ($request->getMethod() == 'POST') {
            $formobj = $form->getData();
            $this->addFlash('info.mail', $formobj);
            $msg = 'Hello, ' . $formobj->getName() . '!!';
        } else {
            $msg = 'Send Form';
        }

        return $this->render('hello/index.html.twig', [
            'title' => 'Hello',
            'message' => $msg,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/find", name="find")
     */
    public function find(Request $request)
    {
        $formobj = new FindForm();
        $form = $this->createFormBuilder($formobj)
            ->add('find', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Click'])
            ->getForm();

        $result = null;

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            $findstr = $form->getData()->getFind();

            $result = $this->personRepository->findByAge($findstr);
        }

        return $this->render('hello/find.html.twig', [
            'title' => 'Hello',
            'form' => $form->createView(),
            'data' => $result,
        ]);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request)
    {
        $person = new Person();
        $form = $this->createForm(PersonType::class, $person);
        $form->handleRequest($request);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            $person = $form->getData();
            $manager = $this->getDoctrine()->getManager();

            $manager->persist($person);
            $manager->flush();

            return $this->redirect('/hello');
        }

        return $this->render('hello/create.html.twig', [
            'title' => 'Hello',
            'message' => 'Create Entity',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/update/{id}", name="update")
     */
    public function update(Request $request, Person $person)
    {
        $form = $this->createFormBuilder($person)
            ->add('name', TextType::class)
            ->add('mail', TextType::class)
            ->add('age', IntegerType::class)
            ->add('save', SubmitType::class, ['label' => 'Click'])
            ->getForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            $person = $form->getData();
            $manager = $this->getDoctrine()->getManager();

            $manager->persist($person);
            $manager->flush();

            return $this->redirect('/hello');
        }

        return $this->render('hello/create.html.twig', [
            'title' => 'Hello',
            'message' => 'Update Entity id=' . $person->getId(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Request $request, Person $person)
    {
        $form = $this->createFormBuilder($person)
            ->add('name', TextType::class)
            ->add('mail', TextType::class)
            ->add('age', IntegerType::class)
            ->add('save', SubmitType::class, ['label' => 'Click'])
            ->getForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            $person = $form->getData();
            $manager = $this->getDoctrine()->getManager();

            $manager->remove($person);
            $manager->flush();

            return $this->redirect('/hello');
        }

        return $this->render('hello/create.html.twig', [
            'title' => 'Hello',
            'message' => 'Delete Entity id=' . $person->getId(),
            'form' => $form->createView(),
        ]);
    }
}

class FindForm
{
    private $find;

    public function getFind()
    {
        return $this->find;
    }

    public function setFind($find)
    {
        $this->find = $find;
    }
}

class HelloForm
{
    private $name;

    private $mail;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getMail()
    {
        return $this->mail;
    }

    public function setMail($mail)
    {
        $this->mail = $mail;
    }

    public function __toString()
    {
        return '*** ' . $this->name . '[' . $this->mail . '] ***';
    }
}
