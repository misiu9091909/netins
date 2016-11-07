<?php
/**
 * Created by PhpStorm.
 * User: mopielka
 * Date: 07.11.16
 * Time: 14:41
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Post;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MicroblogController
 * @package AppBundle\Controller
 */
class MicroblogController extends Controller
{
    /**
     * @Method("GET")
     * @Route("/", name="microblog_posts", defaults={"pageNumber": 1})
     * @Route("/page/{pageNumber}", name="microblog_posts_page", requirements={"page": "[1-9][0-9]*"})
     */
    public function indexAction($pageNumber)
    {
        $posts = $this->getDoctrine()->getRepository(Post::class)->findLatest($pageNumber);

        return $this->render('microblog/posts.html.twig', ['posts' => $posts]);
    }

    /**
     * @Method("GET")
     * @Route("/post/{slug}", name="microblog_single_post", requirements={"slug": "[a-zA-Z\-]+"})
     */
    public function viewPostAction($slug)
    {
        $post = $this->getDoctrine()->getEntityManager()->getRepository('AppBundle:Post')->findOneBy(array('slug' => $slug));
        if (!$post) {
            return $this->redirect($this->generateUrl('microblog_posts'));
        }
        return $this->render('microblog/single_post.html.twig', ['post' => $post]);
    }

}
