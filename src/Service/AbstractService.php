<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use \Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\CacheInterface;

class AbstractService
{

    protected ?Request $request;

    public function __construct(
        RequestStack $requestStack,
        private Security $security,
        protected EntityManagerInterface $manager,
        private ParameterBagInterface $params,
        private SerializerInterface $serializer,
        protected CacheInterface $cache,
    ){
        $this->request = $requestStack->getCurrentRequest();
    }

    protected function getParameter(string $name)
    {
        return $this->params->get($name);
    }

    protected function getUser() : ?User
    {
        return $this->security->getUser();
    }

    protected function json(mixed $data, int $status = 200, array $headers = [], array $context = []): JsonResponse
    {

        if ($this->serializer) {
            $json = $this->serializer->serialize($data, 'json', array_merge([
                'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
            ], $context));

            return new JsonResponse($json, $status, $headers, true);
        }

        return new JsonResponse($data, $status, $headers);
    }

    protected function addFlash(string $type, string $message): void
    {
        $this->request->getSession()->getFlashBag()->add($type, $message);
    }

}