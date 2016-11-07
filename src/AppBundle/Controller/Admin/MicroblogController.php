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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
     * @Route("/new", name="admin_post_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post)
            ->add('saveAndPostType.php', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setSlug($this->get('slugger')->slugify($post->getTitle()));
            $post->setCreatedOn(new \DateTime('now'));
            $post->setUpdatedOn(new \DateTime('now'));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();
            $this->addFlash('success', 'Post created');
            return $this->redirectToRoute('admin_index');
        }

        return $this->render('admin/microblog/new.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }
}