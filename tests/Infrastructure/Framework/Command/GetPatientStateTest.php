<?php

namespace App\Test\Infrastructure\Framework\Command;

use App\Test\EntityGenerator;
use App\Application\Patient\IPatientRepository;
use App\Infrastructure\Framework\CliResponse\GetPatientStateFailedResponse;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Infrastructure\Framework\Command\GetPatientStateCommand;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use App\Infrastructure\Framework\CliResponse\GetPatientStateSuccessResponse;
use Symfony\Component\Console\Input\ArgvInput;

class GetPatientStateTest extends KernelTestCase
{
    use EntityGenerator;

    public function testFoundById(): void
    {
        static::bootKernel();
        $container = static::$kernel->getContainer();
        $patient = $this->createPatient();

        $patientRepository = $this->createMock(IPatientRepository::class);
        $patientRepository
            ->method("getPatientById")
            ->willReturn($patient);
        $container->set(IPatientRepository::class, $patientRepository);
        $application = new Application(static::$kernel);

        $command = $application->find(GetPatientStateCommand::getDefaultName());
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'id' => $patient->getId()->getValue()
        ]);

        $input = new ArgvInput();
        $output = new ConsoleOutput();
        $io = new SymfonyStyle($input, $output);

        $expectedOutput = (new GetPatientStateSuccessResponse($io, $patient, true))->getResponse();
        $realOutput = $commandTester->getDisplay();
        $this->assertEquals($expectedOutput, $realOutput);
    }

    public function testFoundByName(): void
    {
        static::bootKernel();
        $container = static::$kernel->getContainer();
        $patient = $this->createPatient();

        $patientRepository = $this->createMock(IPatientRepository::class);
        $patientRepository
            ->method("getAllPatientsWithName")
            ->willReturn([$patient]);
        $container->set(IPatientRepository::class, $patientRepository);
        $application = new Application(static::$kernel);

        $command = $application->find(GetPatientStateCommand::getDefaultName());
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'id' => $patient->getName()->getValue()
        ]);

        $input = new ArgvInput();
        $output = new ConsoleOutput();
        $io = new SymfonyStyle($input, $output);

        $expectedOutput = (new GetPatientStateSuccessResponse($io, $patient, true))->getResponse();
        $realOutput = $commandTester->getDisplay();
        $this->assertEquals($expectedOutput, $realOutput);
    }

    public function testNotFound(): void
    {
        static::bootKernel();
        $container = static::$kernel->getContainer();
        $patient = $this->createPatient();

        $patientRepository = $this->createMock(IPatientRepository::class);
        $patientRepository
            ->method("getAllPatientsWithName")
            ->willReturn([]);
        $container->set(IPatientRepository::class, $patientRepository);
        $application = new Application(static::$kernel);

        $command = $application->find(GetPatientStateCommand::getDefaultName());
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'id' => $patient->getName()->getValue()
        ]);

        $input = new ArgvInput();
        $output = new ConsoleOutput();
        $io = new SymfonyStyle($input, $output);

        $expectedOutput = (new GetPatientStateFailedResponse($io, $patient, true))->getResponse();
        $realOutput = $commandTester->getDisplay();
        $this->assertEquals($expectedOutput, $realOutput);
    }
}
