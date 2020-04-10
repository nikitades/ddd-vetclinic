<?php

namespace App\Infrastructure\Framework\Controller;

use App\Application\Patient\PatientService;
use Symfony\Component\HttpFoundation\Request;
use App\Application\Patient\DTO\GetPatientDTO;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api")
 */
class ApiController extends AbstractController
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
        $patient = $patientService->getPatient($getPatientDTO);
        if (empty($patient)) {
            return $this->json([], 400);
        }
        return $this->json([
            'patient' => $patient,
            'released' => $patient->isCured()
        ]);
        /**
         * TODO: делаем апи с классами-ответами
         * 1. Create AdminApiController
         * 2. Decide on how to describe the answer classes and where to store
         * 3. TDD all the methods 
         *      - API
         *          - GET /patients/state
         *          - POST /patients/requireNotification
         *      - API/ADMIN
         *          - POST /patients/register
         *          - PUT /patients/attach
         *          - GET /doctors/available
         *          - GET /doctors/all
         *          - PUT /patients/release
         *          - GET /patients/onTreatment
         */
    }
}
