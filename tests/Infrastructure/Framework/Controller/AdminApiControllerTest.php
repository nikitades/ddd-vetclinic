<?php

namespace App\Test\Infrastructure\Framework\Controller;

use App\Test\EntityGenerator;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use App\Domain\Patient\ValueObject\OwnerEmail;
use App\Application\Patient\IPatientRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use App\Application\Patient\Exception\OwnerNotFoundException;
use App\Infrastructure\Framework\ApiResponse\CreateOwnerFailedResponse;
use App\Infrastructure\Framework\ApiResponse\CreateOwnerSuccessResponse;
use App\Infrastructure\Framework\ApiResponse\CreatePatientSuccessResponse;
use App\Infrastructure\Framework\ApiResponse\AddPatientToOwnerFailedResponse;
use App\Infrastructure\Framework\ApiResponse\AddPatientToOwnerSuccessResponse;

class AdminApiControllerTest extends WebTestCase
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

    public function testCreatePatient(): void
    {
        $patient = $this->createPatient();
        $container = $this->getContainer();

        $patientRepo = $this->createMock(IPatientRepository::class);
        $patientRepo->expects($this->any())
            ->method("createPatient")
            ->willReturn($patient);
        $container = $this->getContainer();
        $container->set(IPatientRepository::class, $patientRepo);

        /** @var KernelBrowser */
        $client = $container->get("test.client");
        $client->request(Request::METHOD_POST, "/api/admin/patients", [
            'name' => $patient->getName()->getValue(),
            'species' => $patient->getSpecies()->getValue(),
            'birthDate' => $patient->getBirthDate()->getValue()->format("Y-m-d H:i:s")
        ]);
        $cpsr = new CreatePatientSuccessResponse($patient);
        $this->assertEquals($cpsr->getStatusCode(), $client->getResponse()->getStatusCode());
        $this->assertEquals(json_encode($cpsr), $client->getResponse()->getContent());
    }

    public function testCreateOwner(): void
    {
        $owner = $this->createOwner();
        $container = $this->getContainer();

        $patientRepo = $this->createMock(IPatientRepository::class);
        $patientRepo->expects($this->any())
            ->method("createOwner")
            ->willReturn($owner);
        $container->set(IPatientRepository::class, $patientRepo);

        /** @var KernelBrowser */
        $client = $container->get("test.client");
        $client->request(Request::METHOD_POST, "/api/admin/owners", [
            'name' => $owner->getName()->getValue(),
            'phone' => $owner->getPhone()->getValue(),
            'address' => $owner->getAddress()->getValue(),
            'email' => $owner->getEmail()->getValue()
        ]);

        $cosr = new CreateOwnerSuccessResponse($owner);
        $this->assertEquals($cosr->getStatusCode(), $client->getResponse()->getStatusCode());
        $this->assertEquals(json_encode($cosr), $client->getResponse()->getContent());
    }

    public function testCreateOwnerWithNotSufficientParametersCount(): void
    {
        $owner = $this->createOwner();
        $container = $this->getContainer();

        $patientRepo = $this->createMock(IPatientRepository::class);
        $patientRepo->expects($this->any())
            ->method("createOwner")
            ->willReturn($owner);
        $container->set(IPatientRepository::class, $patientRepo);

        /** @var KernelBrowser */
        $client = $container->get("test.client");
        $client->request(Request::METHOD_POST, "/api/admin/owners", [
            'name' => $owner->getName()->getValue(),
            'email' => $owner->getEmail()->getValue()
        ]);

        $cofr = new CreateOwnerFailedResponse("Arguments missing: address, phone");
        $this->assertEquals($cofr->getStatusCode(), $client->getResponse()->getStatusCode());
        $this->assertEquals(json_encode($cofr), $client->getResponse()->getContent());
        $this->checkHeaderBagContainsHeaders($client->getResponse()->headers, $cofr->getHeaders());
    }

    public function testCreateOwnerWithSomeEmptyParams(): void
    {
        $owner = $this->createOwner();
        $container = $this->getContainer();

        $patientRepo = $this->createMock(IPatientRepository::class);
        $patientRepo->expects($this->any())
            ->method("createOwner")
            ->willReturn($owner);
        $container->set(IPatientRepository::class, $patientRepo);

        /** @var KernelBrowser */
        $client = $container->get("test.client");
        $client->request(Request::METHOD_POST, "/api/admin/owners", [
            'name' => $owner->getName()->getValue(),
            'phone' => $owner->getPhone()->getValue(),
            'address' => $owner->getAddress()->getValue(),
            'email' => "hehe"
        ]);


        $correctErrMsg = "";
        try {
            new OwnerEmail("hehe");
        } catch (InvalidArgumentException $e) {
            $correctErrMsg = $e->getMessage();
        }
        static::assertNotEmpty($correctErrMsg);
        if (empty($correctErrMsg)) return;

        $cofr = new CreateOwnerFailedResponse($correctErrMsg);
        $this->assertEquals($cofr->getStatusCode(), $client->getResponse()->getStatusCode());
        $this->assertEquals(json_encode($cofr), $client->getResponse()->getContent());
        $this->checkHeaderBagContainsHeaders($client->getResponse()->headers, $cofr->getHeaders());
    }

    public function testAttachPatientToOwner(): void
    {
        $patient = $this->createPatient();
        $patientId = $patient->getId();
        if (empty($patientId)) return;
        $patientId = $patientId->getValue();
        $owner = $patient->getOwner();
        if (empty($owner)) return;
        $container = $this->getContainer();

        $patientRepo = $this->createMock(IPatientRepository::class);
        $patientRepo->expects($this->any())
            ->method("getPatientById")
            ->willReturn($patient);
        $patientRepo->expects($this->any())
            ->method("getOwnerById")
            ->willReturn($owner);
        $container->set(IPatientRepository::class, $patientRepo);

        /** @var KernelBrowser */
        $client = $container->get("test.client");
        $ownerId = $owner->getId();
        $this->assertNotEmpty($ownerId);
        if (empty($ownerId)) return;
        $client->request(Request::METHOD_PUT, "/api/admin/patients/$patientId/attachToOwner", [
            'ownerId' => $ownerId->getValue()
        ]);

        $aptosr = new AddPatientToOwnerSuccessResponse($patient, $owner);
        $this->assertEquals($aptosr->getStatusCode(), $client->getResponse()->getStatusCode());
        $this->assertEquals(json_encode($aptosr), $client->getResponse()->getContent());
    }

    public function testAttachPatientToOwnerWithNotExistingOwner(): void
    {
        $patient = $this->createPatient();
        $patientId = $patient->getId();
        if (empty($patientId)) return;
        $patientId = $patientId->getValue();
        $container = $this->getContainer();

        $patientRepo = $this->createMock(IPatientRepository::class);
        $patientRepo->expects($this->any())
            ->method("getPatientById")
            ->willReturn($patient);
        $container->set(IPatientRepository::class, $patientRepo);

        /** @var KernelBrowser */
        $client = $container->get("test.client");
        $client->request(Request::METHOD_PUT, "/api/admin/patients/$patientId/attachToOwner", [
            'ownerId' => 4538
        ]);

        $msg = (new OwnerNotFoundException((string) 4538))->getMessage();
        $aptofr = new AddPatientToOwnerFailedResponse($msg);
        $this->assertEquals($aptofr->getStatusCode(), $client->getResponse()->getStatusCode());
        $this->assertEquals(json_encode($aptofr), $client->getResponse()->getContent());
        $this->checkHeaderBagContainsHeaders($client->getResponse()->headers, $aptofr->getHeaders());
    }
}
