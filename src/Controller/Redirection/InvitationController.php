<?php

namespace App\Controller\Redirection;

use App\Entity\Invitation;
use App\Entity\User;
use App\Service\InvitationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InvitationController extends AbstractController
{

    #[Route(path: '/invitation/send/{user}', name: 'invitation.send')]
    public function send(User $user, EntityManagerInterface $manager, InvitationService $invitationService): Response
    {

        /** @var User */
        $me = $this->getUser();

        $invitation = $manager->getRepository(Invitation::class)->findOneBy([
            'emitter' => $me,
            'receiver' => $user
        ]);


        if ($invitation) {
            $this->addFlash('danger', 'You have already sent an invitation to ' . $user->getUsername());
            return $this->redirectToRoute('profile.index', ['user' => $user->getId()]);
        }

        $invitation = $manager->getRepository(Invitation::class)->findOneBy([
            'emitter' => $user,
            'receiver' => $me
        ]);

        if ($invitation) {
            $invitationService->acceptInvite($invitation);
            $this->addFlash('success', 'You are now friend with ' . $user->getUsername());
            return $this->redirectToRoute('profile.index', ['user' => $user->getId()]);
        }

        $invitationService->sendInvite($me, $user);

        return $this->redirectToRoute('profile.index', ['user' => $user->getId()]);
    }

    #[Route(path: '/invitation/{invitation}/{result}', name: 'redirection.invitation')]
    public function home(Invitation $invitation, string $result, EntityManagerInterface $manager, InvitationService $invitationService, Request $request): Response
    {

        /** @var User */
        $user = $this->getUser();
        $emitter = $invitation->getEmitter();

        if ($emitter != $user && $invitation->getReceiver() != $user) {
            $this->addFlash('danger', 'You are not allowed to do this action.');
            return $this->redirectToRoute('home.index');
        }

        if ($invitation->getReceiver() === $user) {

            if ($result === 'decline') {
                $invitationService->declineInvitation($invitation);
                $this->addFlash('success', 'You have refused the invitation of ' . $emitter->getUsername());
            } else {
                $invitationService->acceptInvite($invitation);
                $this->addFlash('success', 'You are now friend with ' . $emitter->getUsername());
            }
        }

        if ($invitation->getEmitter() === $user) {
            $invitationService->declineInvitation($invitation);
            $this->addFlash('success', 'You have cancelled your invitation to ' . $invitation->getReceiver()->getUsername());
        }

        $manager->flush();

        $referer = $request->headers->get('referer');
        if ($referer) return $this->redirect($referer);

        return $this->redirectToRoute('home.index');
    }
}
