<?php
/**
 * Created by PhpStorm.
 * User: Paja
 * Date: 8.9.14.
 * Time: 13.55
 */

namespace Devana\ShiftsBundle\Controller;


use Acme\DemoBundle\Response\Transaction;
use Swift_Image;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Carbon\Carbon;
use Devana\DoctrineBundle\Entity\Demand;
use Devana\DoctrineBundle\Entity\Shift;
use Devana\DoctrineBundle\Entity\Employee;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\HttpFoundation\Request;

class DemandsController extends Controller {

    /**
     * @ParamConverter("shift", options={"id": "shift_id"})
     * @param Request $request
     * @param Shift $shift
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return Response
     */
    public function swapUserAction(Request $request,Shift $shift)
    {
        $em = $this->get('doctrine.orm.entity_manager');

        $employeeId = $request->request->get('employee_id');

        if($employeeId===null)
            throw new BadRequestHttpException("Employee ID must be provided");

        $targetEmployee = $em->getRepository('DevanaDoctrineBundle:Employee')->find($employeeId);

        if($targetEmployee===null)
            throw new NotFoundHttpException("Target Employee not found");

        $requestedShiftId = $request->request->get('requested_shift_id');

        if($requestedShiftId==null)
            throw new BadRequestHttpException("Requested Shift ID must be provided");

        $requestedShift = $em->getRepository('DevanaDoctrineBundle:Shift')->find($requestedShiftId);

        if($requestedShift===null)
            throw new NotFoundHttpException("Requested Shift not found");

        $comment = $request->request->get('comment');

        $employee = $this->getUser();

        $state = "pending";

        $time = new Carbon();
        $expirationTime = $time->copy()->addDay(1);

        $demand = new Demand();
        $demand->setEmployee($employee);
        $demand->setTargetEmployee($targetEmployee);
        $demand->setShift($shift);
        $demand->setRequestedShift($requestedShift);
        $demand->setState($state);
        $demand->setTime($time);
        $demand->setExpiresAt($expirationTime);
        $demand->setComment($comment);
        $em->persist($demand);
        $em->flush();

        $response = [
            'demand' => $demand
        ];


        $path= 'C:\xampp\htdocs\Symfony\src\Devana\ShiftsBundle\Resources\public\images\Devana.jpg';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);



        $message = \Swift_Message::newInstance();

        $cid = $message->embed(Swift_Image::fromPath($path));


        $message->setSubject('Demand for Swap')
            ->setFrom('trololo@lolotrol.com')
            ->setTo('devanashifts@gmail.com')
            ->setContentType('text/html')
            ->setBody(
               $this->renderView('DevanaShiftsBundle::swapUser.html.twig', array('demand' => $demand, 'cid'=>$cid)
            ));

        $this->get('mailer')->send($message);

        return $response;


    }

    /**
     * @Security("is_granted('disapproved', demand)")
     * @ParamConverter("demand", options={"id": "demand_id"})
     * @param Request $request
     * @param Demand $demand
     * @return Response
     */
    public function disapproveSwapAction(Request $request, Demand $demand)
    {

        $em = $this->get('doctrine.orm.entity_manager');
        $currentTime = new \DateTime();
        $expiresAt = $demand->getExpiresAt();

        if($currentTime>$expiresAt)
        {
            $transaction = new Transaction();
            $transaction->setFail("Time for response has expired");
            $response=[
                'transaction' => $transaction,
                'demand' => $demand
            ];
        }
        else
        {
            $demand->setState("disapproved");
            $demand->setRespondedAt(new Carbon());

            $em->persist($demand);
            $em->flush();

            $transaction = new Transaction();
            $transaction->setSuccess("Demand disapproved successfully");

            $response =[
                'transaction' => $transaction,
                'demand' => $demand
            ];
        }

        return $response;
    }

    /**
     * @Security("is_granted('approved', demand)")
     * @ParamConverter("demand",options={"id": "demand_id"})
     * @param Request $request
     * @param Demand $demand
     * @return Response
     */
    public function approveSwapAction(Request $request, Demand $demand)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $currentTime = new \DateTime();
        $expiresAt = $demand->getExpiresAt();

        if($currentTime>$expiresAt)
        {
            $transaction = new Transaction();
            $transaction->setFail("Time for response has expired");
            $response=[
                'transaction' => $transaction,
                'demand' => $demand
            ];
        }
        else
        {
            $demand->setState("approved");
            $demand->setRespondedAt(new Carbon());

            $employee = $demand->getEmployee();
            $targetEmployee = $demand->getTargetEmployee();

            $demand->getShift()->getEmployees()->add($targetEmployee);
            $demand->getShift()->getEmployees()->removeElement($employee);

            $demand->getRequestedShift()->getEmployees()->add($employee);
            $demand->getRequestedShift()->getEmployees()->removeElement($targetEmployee);

            $em->persist($demand);
            $em->flush();

            $transaction = new Transaction();
            $transaction->setSuccess("Demand approved successfully");

            $response =[
                'transaction' => $transaction,
                'demand' => $demand
            ];
        }

        return $response;
    }

} 