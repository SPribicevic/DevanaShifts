<?php
/**
 * Created by PhpStorm.
 * User: Paja
 * Date: 4.9.14.
 * Time: 13.43
 */

namespace Devana\ShiftsBundle\EventListener;


use Acme\DemoBundle\Response\Transaction;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Acl\Exception\Exception;
use JMS\Serializer\SerializerInterface;

class ExceptionSerializerListener
{

    /**
     * @var SerializerInterface
     */
    private $serializer;

    function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function onKernelResponse(GetResponseForExceptionEvent $event)
    {
        $response = $event->getException();
        $transaction = new Transaction();
        $exception = $event->getException();
        $transaction->setFail($exception->getMessage());
        $serializeResponse = $this->serializer->serialize([
            'transaction' => $transaction
        ], 'json');

        if ($exception instanceof HttpException) {
            $event->setResponse(new Response($serializeResponse, intval($exception->getStatusCode()), ['Content-Type' => 'application/json']));
        }
        else {
            $transaction->setFail('An unexpected error occured.');
            $serializeResponse = $this->serializer->serialize([
                'transaction' => $transaction,
            ], 'json');
            $event->setResponse(new Response($serializeResponse, 500, ['Content-Type' => 'application/json']));
        }
    }

} 