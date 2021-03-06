<?php

namespace App\Infrastructure\Framework\Controller;

use InvalidArgumentException;
use App\Application\Patient\PatientService;
use Symfony\Component\HttpFoundation\Request;
use App\Application\Patient\DTO\GetPatientDTO;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Application\Patient\DTO\RequireNotificationDTO;
use App\Application\Patient\Exception\OwnerNotFoundException;
use App\Application\Patient\Exception\PatientNotFoundException;
use App\Infrastructure\Framework\ApiResponse\PatientStateSuccessResponse;
use App\Infrastructure\Framework\ApiResponse\PatientStateNotFoundResponse;
use App\Infrastructure\Framework\ApiResponse\RequireNotificationFailedResponse;
use App\Infrastructure\Framework\ApiResponse\RequireNotificationSuccessResponse;

/**
 * @Route("/api")
 */
class ApiController extends AbstractApiController
{
    /**
     * @Route("/patients/state", name="patients.state", methods={"GET"})
     */
    public function getPatientState(Request $request, PatientService $patientService): Response
    {
        $patientId = $request->query->get("patientId");
        $patientName = $request->query->get("patientName");
        $ownerId = $request->query->get("ownerId");
        $ownerName = $request->query->get("ownerName");

        $getPatientDTO = new GetPatientDTO();
        $getPatientDTO->ownerId = $ownerId;
        $getPatientDTO->ownerName = $ownerName;
        $getPatientDTO->patientId = $patientId;
        $getPatientDTO->patientName = $patientName;
        try {
            $patient = $patientService->getPatient($getPatientDTO);
        } catch (InvalidArgumentException $e) {
            $this->apiResponse(new PatientStateNotFoundResponse($e->getMessage()));
        }
        if (empty($patient)) {
            return $this->apiResponse(new PatientStateNotFoundResponse());
        }
        return $this->apiResponse(new PatientStateSuccessResponse($patient));
    }

    /**
     * @Route("/patients/requireNotification", name="patients.requireNotification", methods={"POST"})
     * @return Response
     */
    public function requireNotification(Request $request, PatientService $patientService): Response
    {
        $patientId = $request->request->get("patientId");
        $patientName = $request->request->get("patientName");
        $ownerId = $request->request->get("ownerId");
        $ownerName = $request->request->get("ownerName");

        $requireNotificationDTO = new RequireNotificationDTO();
        $requireNotificationDTO->ownerId = $ownerId;
        $requireNotificationDTO->ownerName = $ownerName;
        $requireNotificationDTO->patientId = $patientId;
        $requireNotificationDTO->patientName = $patientName;

        try {
            $owner = $patientService->requireNotification($requireNotificationDTO);
        } catch (OwnerNotFoundException $e) {
            return $this->apiResponse(new RequireNotificationFailedResponse("No owner found"));
        } catch (PatientNotFoundException $e) {
            return $this->apiResponse(new RequireNotificationFailedResponse("No patient found"));
        }
        return $this->apiResponse(new RequireNotificationSuccessResponse($owner));
    }
}
