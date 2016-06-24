<?php
/**
 * Created by PhpStorm.
 * User: Paja
 * Date: 22.8.14.
 * Time: 10.36
 */
namespace Devana\ShiftsBundle\Controller;

use Acme\DemoBundle\Response\Transaction;
use Carbon\Carbon;
use Devana\DoctrineBundle\Entity\Demand;
use Devana\DoctrineBundle\Entity\Shift;
use Devana\DoctrineBundle\Entity\Employee;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\HttpFoundation\Request;

class ShiftsController extends Controller
{
    /**
     * @Security("is_granted('edit', shift)")
     * @ParamConverter("shift", options={"id": "shift_id"})
     * @param Request $request
     * @param Shift $shift
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return Response
     */
    public function addUserToShiftAction(Request $request, Shift $shift)
    {
        $em = $this->get("doctrine.orm.entity_manager");

        $userId = $request->request->get('employee_id');

        if ($userId === null) {
            throw new BadRequestHttpException("User ID must be provided.");
        }

        $user = $this->getDoctrine()->getRepository('DevanaDoctrineBundle:Employee')->find($userId);

        if (!$user) {
              throw new NotFoundHttpException("Employee not found!");
        } else {

            if ($shift->getEmployees()->contains($user)) {
               throw new NotFoundHttpException("Employee already in use!");

            } else {

                $shift->getEmployees()->add($user);

                $em->persist($shift);
                $em->flush();

                $transaction = new Transaction();
                $transaction->setSuccess("Employee added successfully");
                $response = [
                    'transaction' => $transaction,
                    'shift' => $shift
                ];
            }
        }

        return $response;

    }

    /**
     * @Security("is_granted('edit', shift)")
     * @ParamConverter("shift", options={"id": "shift_id"})
     * @ParamConverter("employee", options={"id": "employee_id"})
     * @param Employee $employee
     * @param Shift $shift
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return Response
     */
    public function removeUserFromShiftAction(Employee $employee, Shift $shift)
    {
        $em = $this->get("doctrine.orm.entity_manager");

            $employees = $shift->getEmployees();

//        if (false === $this->get('security.context')->isGranted('edit', $shift)) {
//            throw new AccessDeniedException('Unauthorised access!');
//        }

            if (!$employees->contains($employee)) {
                throw new NotFoundHttpException("Employee not found in Shift!");
            } else {
                $transaction = new Transaction();
                $transaction->setSuccess("Employee successfully removed");
                $employees->removeElement($employee);
                $em->flush();
                $response = [
                    'transaction' => $transaction,
                    'employee' => $employee,
                    'shift' => $shift
                ];
            }

        return $response;
    }

    public function getShiftsAction(Request $request)
    {
        $em = $this->get("doctrine.orm.entity_manager");

        $days = $request->query->get('days');

        if($days===null)
            throw new BadRequestHttpException("Number od days must be provided");

        $days = intval($days);
        $date = Carbon::createFromFormat("d.m.Y", $request->query->get('date'));

        if($date===null)
            throw new BadRequestHttpException("Date must be provided");

        $endDate = $date->copy()->addDays(intval($days));

        //TODO: Exception for invalid date

        $query = $em->createQuery('SELECT u FROM DevanaDoctrineBundle:Shift u WHERE u.date>=:startDate AND u.date<:endDate');
        $query->setParameter('startDate', $date);
        $query->setParameter('endDate', $endDate);
        /** @var Shift[] $shifts */
        $shifts = $query->getResult();

        $dayToShifts = [];

        foreach ($shifts as $shift) {
            $date = $shift->getDate()->format('d.m.Y');
            if (!isset($dayToShifts[$date])) {
                $dayToShifts[$date] = [];
            }
            $dayToShifts[$date][] = $shift;
        }

        $response = [];

        foreach ($dayToShifts as $day => $shifts) {
            $response[] = [
                'date' => $day,
                'shifts' => $shifts,
            ];
        }

        return $response;
    }

    /**
     * @Security("is_granted('edit', shift)")
     * @ParamConverter("shift",options={"id": "shift_id"})
     * @param Request $request
     * @param Shift $shift
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return Response
     */
    public function moveEmployeeAction(Request $request, Shift $shift)
    {
        $em = $this->get('doctrine.orm.entity_manager');

        $employeeId = $request->request->get('employee_id');

        if($employeeId===null)
            throw new BadRequestHttpException("Employee ID must be provided");

        $employee = $em->getRepository('DevanaDoctrineBundle:Employee')->find($employeeId);

        if($employee===null)
            throw new NotFoundHttpException("Employee not found");

        $sourceShiftId = $request->request->get('source_shift_id');

        if($sourceShiftId===null)
            throw new BadRequestHttpException("Source Shift ID must be provided");

        $sourceShift = $em->getRepository('DevanaDoctrineBundle:Shift')->find($sourceShiftId);

        if($sourceShift===null)
            throw new NotFoundHttpException("Source Shift not found");

        $sourceShift->getEmployees()->removeElement($employee);
        $em->persist($sourceShift);

        $shift->getEmployees()->add($employee);
        $em->persist($shift);

        $em->flush();

        $transaction = new Transaction();
        $transaction->setSuccess("Employee moved successfully");

        $response=[
            'transaction' => $transaction,
            'shift' => $shift
        ];

        return $response;
    }



    public function homePageAction()
    {
        return new Response("Hello World!");
    }

}
