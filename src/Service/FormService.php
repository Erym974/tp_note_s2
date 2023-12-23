<?php

namespace App\Service;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\Report;
use App\Form\PostType;
use App\Form\ReportType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FormService extends AbstractService
{


    /**
     * 
     * Create a new post
     * 
     */
    public function handleNewPost(): array
    {

        $request = $this->request;
        $referer = $request->headers->get('referer');

        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $post->setAuthor($this->getUser());
            $this->manager->persist($post);
            $this->manager->flush();
            $this->addFlash('success', 'Post created');

            if ($referer) {
                if (strpos($referer, 'post')) $referer = substr($referer, 0, strpos($referer, 'post') - 1);
                return ["response" => $this->redirect($referer)];
            }
            return ["response" => $this->redirectToRoute('profile.index', ['user' => $post->getAuthor()->getId(), 'page' => $request->query->get('page', 1)])];

        }

        return ["form" => $form];
    }


    /**
     * 
     * Report a post
     * 
     */
    public function handleReportPost(): ?array
    {

        $request = $this->request;
        $referer = $request->headers->get('referer');

        if ($request->query->get('report')) {
            $post = $this->manager->getRepository(Post::class)->find($request->query->get('report'));
            if ($post) {

                if ($this->manager->getRepository(Report::class)->findOneBy([
                    'author' => $this->getUser(),
                    'post' => $post,
                ])) {
                    $this->addFlash('danger', 'You already reported this post');
                    if ($referer) {
                        if (strpos($referer, 'report')) $referer = substr($referer, 0, strpos($referer, 'report') - 1);
                        return ["response" => $this->redirect($referer)];
                    }
                    return ["response" => $this->redirectToRoute('profile.index', ['user' => $post->getAuthor()->getId(), 'page' => $request->query->get('page', 1)])];
                }

                $report = new Report();
                $form = $this->createForm(ReportType::class, $report);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $report->setAuthor($this->getUser());
                    $report->setPost($post);
                    $this->manager->persist($post);
                    $this->manager->persist($report);
                    $this->manager->flush();
                    $this->addFlash('success', 'Post reported');

                    if ($referer) {
                        if (strpos($referer, 'report')) $referer = substr($referer, 0, strpos($referer, 'report') - 1);
                        return ["response" => $this->redirect($referer)];
                    }
                    return ["response" => $this->redirectToRoute('profile.index', ['user' => $post->getAuthor()->getId(), 'page' => $request->query->get('page', 1)])];
                }

                return ["form" => $form];
            }
        }

        return ["form" => null];
    }

    /**
     * 
     * Edit a post
     * 
     */
    public function handleEditPost(): array
    {

        $request = $this->request;
        $user = $this->getUser();

        if ($request->query->get('edit')) {
            $post = $this->manager->getRepository(Post::class)->find($request->query->get('edit'));

            if ($post && ($post->getAuthor() == $user || $this->isGranted('ROLE_MODERATOR'))) {
                $form = $this->createForm(PostType::class, $post);
                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {
                    $this->manager->persist($post);
                    $this->manager->flush();
                    $this->addFlash('success', 'Post edited');

                    $referer = $request->headers->get('referer');
                    if ($referer) {
                        if (strpos($referer, 'edit')) $referer = substr($referer, 0, strpos($referer, 'edit') - 1);
                        return ["response" => $this->redirect($referer)];
                    }
                    return ["response" => $this->redirectToRoute('profile.index', ['user' => $user->getId(), 'page' => $request->query->get('page', 1)])];
                }

                return ["form" => $form];
            }
        }

        return ["form" => null];
    }

    /**
     * 
     * Comment a post
     * 
     */
    public function handleNewComment(): bool
    {

        if ($this->request->isMethod('POST')) {
            $isComment = $this->request->request->get('comment');

            if ($isComment) {
                $content = $this->request->request->get('content');
                $post = $this->manager->getRepository(Post::class)->find($this->request->request->get('post')) ?? null;
                $reply = $this->manager->getRepository(Comment::class)->find($this->request->request->get('reply') ?? 0) ?? null;

                if ($post) {
                    $comment = (new Comment())
                        ->setAuthor($this->getUser())
                        ->setPost($post)
                        ->setReply($reply)
                        ->setContent($content);

                    $this->manager->persist($comment);
                    $this->manager->flush();

                    return true;
                }
            }
        }

        return false;
    }
}
