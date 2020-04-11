<?php

namespace App\Infrastructure\Framework\Controller;

use Exception;
use App\Domain\Patient\Entity\Owner;
use App\Domain\Patient\Entity\Patient;
use App\Application\Patient\PatientService;
use Symfony\Component\HttpFoundation\Request;
use App\Application\Patient\DTO\AddPatientDTO;
use Symfony\Component\HttpFoundation\Response;
use App\Application\Patient\DTO\CreateOwnerDTO;
use Symfony\Component\Routing\Annotation\Route;
use App\Application\Patient\DTO\AttachPatientToOwnerDTO;
use App\Infrastructure\Framework\ApiResponse\AddPatientToOwnerFailedResponse;
use App\Infrastructure\Framework\ApiResponse\CreateOwnerFailedResponse;
use App\Infrastructure\Framework\ApiResponse\CreateOwnerSuccessResponse;
use App\Infrastructure\Framework\ApiResponse\CreatePatientFailedResponse;
use App\Infrastructure\Framework\ApiResponse\CreatePatientSuccessResponse;
use App\Infrastructure\Framework\ApiResponse\AddPatientToOwnerSuccessResponse;

/** @Route("/api/admin") */
class AdminApiController extends AbstractApiController
{
    /**
     * @Route("/patients", name="admin.patients.create", methods={"POST"})
     * @param Request $request
     * @param PatientService $patientService
     * @return Response
     */
    public function createPatient(Request $request, PatientService $patientService): Response
    {
        $name = $request->request->get("name");
        $species = $request->request->get("species");
        $birthDate = $request->request->get("birthDate");

        $addPatientDTO = new AddPatientDTO(
            $name,
            $species,
            $birthDate
        );
        try {
            $patient = $patientService->addPatient($addPatientDTO);
            return $this->apiResponse(new CreatePatientSuccessResponse($patient));
        } catch (Exception $e) {
            return $this->apiResponse(new CreatePatientFailedResponse());
        }
    }

    /**
     * @Route("/owners", name="admin.owners.create", methods={"POST"})
     * @param Request $request
     * @param PatientService $patientService
     * @return Response
     */
    public function createOwner(Request $request, PatientService $patientService): Response
    {
        $name = $request->request->get("name");
        $address = $request->request->get("address");
        $phone = $request->request->get("phone");
        $email = $request->request->get("email");

        $params = compact("address", "email", "name", "phone");
        $filledParams = array_filter($params);
        if (count($filledParams) !== count($params)) {
            $missingParams = array_diff($params, $filledParams);
            return $this->apiResponse(new CreateOwnerFailedResponse("Arguments missing: " . implode(", ", array_keys($missingParams))));
        }

        $createOwnerDTO = new CreateOwnerDTO();
        $createOwnerDTO->email = $email;
        $createOwnerDTO->name = $name;
        $createOwnerDTO->phone = $phone;
        $createOwnerDTO->address = $address;

        try {
            $owner = $patientService->createOwner($createOwnerDTO);
            return $this->apiResponse(new CreateOwnerSuccessResponse($owner));
        } catch (Exception $e) {
            return $this->apiResponse(new CreateOwnerFailedResponse($e->getMessage()));
        }
    }

    /**
     * @Route("/patients/{id}/addToOwner", name="admin.patient.addToOwner", methods={"PUT"})
     * @param integer $id
     * @return Response
     */
    public function attachPatientToOwner(int $id, Request $request, PatientService $patientService): Response
    {
        $ownerId = $request->request->get("ownerId");

        $attachPatientToOwnerDTO = new AttachPatientToOwnerDTO();
        $attachPatientToOwnerDTO->patientId = $id;
        $attachPatientToOwnerDTO->ownerId = $ownerId;

        try {
            $data = $patientService->attachPatientToOwner($attachPatientToOwnerDTO);
        } catch (Exception $e) {
            return $this->apiResponse(new AddPatientToOwnerFailedResponse($e->getMessage()));
        }
        /** @var Patient */
        $patient = $data[0];
        /** @var Owner */
        $owner = $data[1];

        $aptosr = new AddPatientToOwnerSuccessResponse($patient, $owner);
        return $this->apiResponse($aptosr);
    }
}
