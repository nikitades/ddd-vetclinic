<?php

namespace App\Test\Infrastructure\Framework\Controller;

use DateTime;
use App\Domain\Patient\Entity\Card;
use App\Domain\Patient\Entity\Owner;
use App\Domain\Patient\Entity\Patient;
use App\Domain\Patient\Entity\MedicalCase;
use App\Domain\Patient\ValueObject\CardId;
use App\Domain\Patient\ValueObject\OwnerId;
use App\Domain\Patient\ValueObject\OwnerName;
use App\Domain\Patient\ValueObject\PatientId;
use Symfony\Component\HttpFoundation\Request;
use App\Domain\Patient\ValueObject\OwnerEmail;
use App\Domain\Patient\ValueObject\OwnerPhone;
use App\Application\Patient\IPatientRepository;
use App\Domain\Patient\ValueObject\PatientName;
use App\Domain\Patient\ValueObject\OwnerAddress;
use App\Domain\Patient\ValueObject\MedicalCaseId;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use App\Domain\Patient\ValueObject\PatientSpecies;
use App\Domain\Patient\ValueObject\PatientBirthDate;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Domain\Patient\ValueObject\OwnerRegisteredAt;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use App\Domain\Patient\ValueObject\MedicalCaseTreatment;
use App\Domain\Patient\ValueObject\MedicalCaseDescription;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Infrastructure\Framework\ApiResponse\PatientStateSuccessResponse;
use App\Infrastructure\Framework\ApiResponse\PatientStateNotFoundResponse;
use App\Infrastructure\Framework\ApiResponse\RequireNotificationFailedResponse;
use App\Infrastructure\Framework\ApiResponse\RequireNotificationSuccessResponse;
use App\Test\EntityGenerator;

class ApiControllerTest extends WebTestCase
{
    use EntityGenerator;

    public function __construct()
    {
        parent::__construct(...func_get_args());
    }

    /** PREP */

    /**
     *
     * @param ResponseHeaderBag<string> $headerBag
     * @param array<string,string> $headers
     * @return void
     */
    private function checkHeaderBagContainsHeaders(ResponseHeaderBag $headerBag, array $headers): void
    {
        foreach ($headers as $name => $header) {
            $this->assertArrayHasKey($name, $headerBag->allPreserveCase());
            $this->assertEquals(reset($headerBag->allPreserveCase()[$name]), $header);
        }
    }

    /** TESTS */

    public function testPatientState(): void
    {
        $patient = $this->createPatient();
        $patientRepo = $this->createMock(IPatientRepository::class);
        array_map(
            fn (string $serviceName) => $patientRepo->expects($this->any())
                ->method($serviceName)
                ->willReturn($patient),
            [
                "getPatientByNameAndOwnerName",
                "getPatientByNameAndOwnerId",
                "getPatientById",
            ]
        );
        $container = $this->getContainer();
        $container->set(IPatientRepository::class, $patientRepo);
        /** @var KernelBrowser */
        $client = $container->get("test.client");
        $client->request(Request::METHOD_GET, "/api/patients/state", [
            "patientId" => $patient->getId()->getValue()
        ]);
        $psr = new PatientStateSuccessResponse($patient);
        $this->assertEquals($psr->getStatusCode(), $client->getResponse()->getStatusCode());
        $this->assertEquals(json_encode($psr), $client->getResponse()->getContent());
    }

    public function testPatientStateWithNoPatientFound(): void
    {

        $patientRepo = $this->createMock(IPatientRepository::class);
        array_map(
            fn (string $serviceName) => $patientRepo->expects($this->any())
                ->method($serviceName)
                ->willReturn(null),
            [
                "getPatientByNameAndOwnerName",
                "getPatientByNameAndOwnerId",
                "getPatientById",
            ]
        );
        $container = $this->getContainer();
        $container->set(IPatientRepository::class, $patientRepo);
        /** @var KernelBrowser */
        $client = $container->get("test.client");
        $client->request(Request::METHOD_GET, "/api/patients/state", [
            "patientId" => 373
        ]);
        $psnfr = new PatientStateNotFoundResponse();
        $this->assertEquals($psnfr->getStatusCode(), $client->getResponse()->getStatusCode());
        $this->assertEquals(json_encode($psnfr), $client->getResponse()->getContent());
        $this->checkHeaderBagContainsHeaders($client->getResponse()->headers, $psnfr->getHeaders());
    }

    public function testRequireNotification(): void
    {
        $patient = $this->createPatient();
        $container = $this->getContainer();

        $patientRepo = $this->createMock(IPatientRepository::class);
        array_map(
            fn (string $serviceName) => $patientRepo->expects($this->any())
                ->method($serviceName)
                ->willReturn($patient),
            [
                "getPatientByNameAndOwnerName",
                "getPatientByNameAndOwnerId",
                "getPatientById",
            ]
        );
        $container->set(IPatientRepository::class, $patientRepo);

        /** @var KernelBrowser */
        $client = $container->get("test.client");
        $owner = $patient->getOwner();
        $this->assertNotNull($owner);
        if (empty($owner)) return;
        $client->request(Request::METHOD_POST, "/api/patients/requireNotification", [
            "patientName" => $patient->getName()->getValue(),
            "ownerName" => $owner->getName()->getValue()
        ]);
        $nssr = new RequireNotificationSuccessResponse($owner);
        $this->assertEquals($nssr->getStatusCode(), $client->getResponse()->getStatusCode());
        $this->assertEquals(json_encode($nssr), $client->getResponse()->getContent());
    }

    public function testRequireNotificationWithNoPatient(): void
    {
        $container = $this->getContainer();

        $patientRepo = $this->createMock(IPatientRepository::class);
        array_map(
            fn (string $serviceName) => $patientRepo->expects($this->any())
                ->method($serviceName)
                ->willReturn(null),
            [
                "getPatientByNameAndOwnerName",
                "getPatientByNameAndOwnerId",
                "getPatientById",
            ]
        );
        $container->set(IPatientRepository::class, $patientRepo);

        /** @var KernelBrowser */
        $client = $container->get("test.client");
        $client->request(Request::METHOD_POST, "/api/patients/requireNotification", [
            "patientName" => "Chimken",
            "ownerId" => 72
        ]);
        $rnnr = new RequireNotificationFailedResponse("No patient found");
        $this->assertEquals($rnnr->getStatusCode(), $client->getResponse()->getStatusCode());
        $this->assertEquals(json_encode($rnnr), $client->getResponse()->getContent());
        $this->checkHeaderBagContainsHeaders($client->getResponse()->headers, $rnnr->getHeaders());
    }
}
