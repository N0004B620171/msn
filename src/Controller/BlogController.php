<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use Doctrine\Common\Persistence\ObjectManager;
use http\Client\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Httfoundation\Response;
use function Sodium\add;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index()
    {
        $repo=$this->getDoctrine()->getRepository(Article::class);
        $articles=$repo->findAll();
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController','articles'=>$articles
        ]);
    }
    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render('blog/home.html.twig');
    }

    /**
     * @Route("/blog/new", name="blog_create")
     * @ROute("/blog/{id}/edit", name="blog_edit")
     */
    public function ajout(Article $article= null, \Symfony\Component\HttpFoundation\Request $request, ObjectManager $manager)
    {
            $article=new Article();

  //  $form=$this->createFormBuilder($article)
    //    ->add('title')
      //  ->add('content')
        //->add('image')
        //->getForm();
        $form=$this->createForm(ArticleType::class, $article);
        //return new \Symfony\Component\HttpFoundation\Response();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($article);
            $manager->flush();
            
            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
        }

        return $this->render('blog/create.html.twig',['formArticle'=>$form->createView(), 'editMode' => $article->getId() !== null
        ]);
    }

    /**
     * @Route("/blog/{id}", name="blog_show")
     */
    public function show(Article $article , \Symfony\Component\HttpFoundation\Request $request , ObjectManager $manager)
    {
        $comment= new Comment();
        $form=$this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $comment->setArticle($article);
             $manager->persist($comment);
             $manager->flush();
             return $this->redirectToRoute('blog_show',['id'=>$article->getId()]);
        }
        return $this->render('blog/show.html.twig',['article'=>$article, 'commentForm'=>$form->createView()]);
    }
}
