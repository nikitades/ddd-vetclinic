<?php

namespace App\Infrastructure\Framework\Command;

use App\Domain\Patient\Entity\Patient;
use App\Application\Patient\PatientService;
use App\Application\Patient\DTO\GetPatientDTO;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use App\Application\Patient\DTO\GetPatientsByNameDTO;
use Symfony\Component\Console\Output\OutputInterface;
use App\Infrastructure\Framework\CliResponse\GetPatientStateFailedResponse;
use App\Infrastructure\Framework\CliResponse\GetPatientStateSuccessResponse;

class GetPatientStateCommand extends Command
{
    private PatientService $patientService;
    protected static $defaultName = 'clinic:patient:state';

    public function __construct(PatientService $patientService)
    {
        $this->patientService = $patientService;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Get a patient\'s current state')
            ->addArgument('id', InputArgument::REQUIRED, 'Patients\' name or id')
            // ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $id = $input->getArgument('id');

        if (is_int($id)) {
            $getPatientDTO = new GetPatientDTO();
            $getPatientDTO->patientId = $id;
            $patient = $this->patientService->getPatient($getPatientDTO);
            if (!empty($patient)) {
                $this->result($io, $patient, true);
                return 0;
            }
        }

        if (is_string($id)) {
            $getAllPatientsByNameDTO = new GetPatientsByNameDTO($id);
            $patients = $this->patientService->getPatientsByName($getAllPatientsByNameDTO);
            foreach ($patients as $patient) {
                $this->result($io, $patient, false);
                return 0;
            }
        }

        $this->badResult($io, $id);
        return 127;
    }

    private function result(SymfonyStyle $style, Patient $patient, $byId = true)
    {
        (new GetPatientStateSuccessResponse($style, $patient, $byId))->makeResponse();
    }

    /**
     * @param mixed $id
     * @return void
     */
    private function badResult(SymfonyStyle $style, $id)
    {
        (new GetPatientStateFailedResponse($style, $id))->makeResponse();
    }
}
