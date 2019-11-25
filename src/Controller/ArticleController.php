<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Article;

class ArticleController extends AbstractController {
    /**
     * @Route("/", name="article_list")
     * @Method({"GET"})
     */
    public function index() {
        $articles = $this->getDoctrine()->getRepository(Article::class)->findAll();
        
        return $this->render('articles/index.html.twig', [
            'articles' => $articles
        ]);
    }
    
    /**
     * @Route("/article/add", name="article_add")
     * @Method({"GET", "POST"})
     */
    public function add(Request $request) {
        $article = new Article();
        
        $form = $this->createFormBuilder($article)
                ->add('title', TextType::class, [
                    'attr' => ['class' => 'form-control']
                ])
                ->add('body', TextareaType::class, [
                    'attr' => ['class' => 'form-control'], 
                    'required' => false
                ])
                ->add('save', SubmitType::class, [
                    'attr' => ['class' => 'btn btn-primary mt-3'],
                    'label' => 'Create'
                ])
                ->getForm();
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();
            
            return $this->redirectToRoute('article_list');
        }
        
        return $this->render('articles/add.html.twig', [
            'form' => $form->createView()
        ]);
    }
    
    /**
     * @Route("/article/edit/{id}", name="article_edit")
     * @Method({"GET", "POST"})
     */
    public function edit(Request $request, $id) {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
        
        $form = $this->createFormBuilder($article)
                ->add('title', TextType::class, [
                    'attr' => ['class' => 'form-control']
                ])
                ->add('body', TextareaType::class, [
                    'attr' => ['class' => 'form-control'], 
                    'required' => false
                ])
                ->add('save', SubmitType::class, [
                    'attr' => ['class' => 'btn btn-primary mt-3'],
                    'label' => 'Update'
                ])
                ->getForm();
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            
            return $this->redirectToRoute('article_list');
        }
        
        return $this->render('articles/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
    
    /**
     * @Route("/article/{id}", name="article_show")
     */
    public function show($id) {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
        
        return $this->render('articles/show.html.twig', ['article' => $article]);
    }
    
    /**
     * @Route("/article/delete/{id}", name="article_delete")
     * @Method({"DELETE"})
     */
    public function delete(Request $request, $id) {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($article);
        $entityManager->flush();
        
        $response = new Response();
        $response->send();
    }
    
//    /**
//     * @Route("/article/save")
//     */
//    public function save() {
//        $entityManager = $this->getDoctrine()->getManager();
//        
//        $article = new Article();
//        $article->setTitle('Article 2');
//        $article->setBody('Body article 2');
//        
//        $entityManager->persist($article);
//        $entityManager->flush();
//        
//        return new Response('Saved an article with id' . $article->getId());
//    }
}