<?php 

namespace App\Service;

use App\Entity\Invitation;

class InvitationService extends AbstractService
{

    public function acceptInvite(Invitation $invitation)
    {

        $emitter = $invitation->getEmitter();
        $receiver = $invitation->getReceiver();

        $emitter->addFriend($receiver);
        $receiver->addFriend($emitter);

        $this->manager->persist($emitter);
        $this->manager->persist($receiver);
        $this->manager->remove($invitation);
        $this->manager->flush();

    }

    public function declineInvitation(Invitation $invitation)
    {

        $this->manager->remove($invitation);
        $this->manager->flush();

    }

    public function sendInvite($emitter, $receiver)
    {

        $invitation = new Invitation();
        $invitation->setEmitter($emitter);
        $invitation->setReceiver($receiver);

        $this->manager->persist($invitation);
        $this->manager->flush();

    }

}