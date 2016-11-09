<?php
/**
 * Created by PhpStorm.
 * User: misiu9091909
 * Date: 11/7/16
 * Time: 9:19 PM
 */

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Post;
use AppBundle\Form\PostType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin")
 */
class MicroblogController extends Controller
{
    /**
     * @Route("/", name="admin_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $posts = $entityManager->getRepository(Post::class)->findBy([], ['created_on' => 'DESC']);

        return $this->render('admin/microblog/index.html.twig', ['posts' => $posts]);
    }

    /**
     * @param Request $request
     * @Route("/edit/{postId}", name="admin_post_edit", requirements={"postId": "[1-9][0-9]*"})
     * @Route("/new", name="admin_post_new")
     */
    public function editAction(Request $request, $postId = null)
    {

        $title = '';
        if ($postId) {
            $post = $this->getDoctrine()->getEntityManager()->find(Post::class, $postId);
        }
        if (!isset($post)) {
            $post = new Post();
            $title = 'New post';
        } else {
            $title = 'Edit post ' . $post->getTitle();
        }
        $form = $this->createForm(PostType::class, $post)
            ->add('save', SubmitType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $post->setSlug($this->get('slugger')->slugify($post->getTitle()));
            $post->setCreatedOn(new \DateTime('now'));
            $post->setUpdatedOn(new \DateTime('now'));
            $file = $post->getImage();
            if ($file) {
                $fileName = $this->get('uploader')->upload($file);
                $post->setImageName($fileName);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();
            $this->addFlash('success', 'Post created');
            return $this->redirectToRoute('admin_index');
        }

        return $this->render('admin/microblog/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
            'title' => $title
        ]);
    }

    /**
     * @Route("/delete/{postId}", name="admin_post_delete", requirements={"postId": "[1-9][0-9]*"})
     *
     */
    public function deleteAction(Request $request, $postId)
    {
        $post = $this->getDoctrine()->getEntityManager()->find('AppBundle:Post', $postId);
        if ($post) {
            $this->getDoctrine()->getEntityManager()->remove($post);
            $this->getDoctrine()->getEntityManager()->flush();
            $this->addFlash('success', 'Post removed');
        } else {
            $this->addFlash('error', 'Error, post was not found');
        }
        return $this->redirectToRoute('admin_index');
    }

    /**
     * @param Request $request
     * @param $postId
     * @Route("/removeImage/{postId}", name="admin_post_removeimage", requirements={"postId": "[1-9][0-9]*"})
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeImage(Request $request, $postId)
    {
        $post = $this->getDoctrine()->getEntityManager()->find('AppBundle:Post', $postId);
        $post->setImageName('');
        $this->getDoctrine()->getEntityManager()->persist($post);
        $this->getDoctrine()->getEntityManager()->flush();
        $this->addFlash('success', 'Post\'s image removed');
        return $this->redirectToRoute('admin_index');
    }
}