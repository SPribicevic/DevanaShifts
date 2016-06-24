<?php

namespace Devana\ShiftsBundle\EventListener;

use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

class ResponseSerializerListener
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function onKernelResponse(GetResponseForControllerResultEvent $event)
    {
        $response = $event->getControllerResult();
        $serializedResponse = $this->serializer->serialize($response, 'json');

        $event->setResponse(new Response($serializedResponse, 200, ['Content-Type' => 'application/json']));
    }
} 