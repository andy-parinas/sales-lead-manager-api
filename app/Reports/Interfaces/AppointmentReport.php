<?php

namespace App\Reports\Interfaces;

interface AppointmentReport
{
    public function getAllAppointment($queryParams);
    public function getAllAppointmentByFranchise($franchiseIds, $queryParams);
}
