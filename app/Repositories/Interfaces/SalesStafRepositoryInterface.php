<?php


namespace App\Repositories\Interfaces;


interface SalesStafRepositoryInterface
{
    public function getAll(array $params);
    public function searchAllActive($search);
    public function searchAll($search);
    public function searchAllActiveByFranchise(array $franchiseIds, array $params);
    public function getAllByFranchise(array $franchiseIds, array $params);
    public function searchAllByFranchise(array $franchiseIds, $search);
    public function getAllSalesStaff();
    public function getAllSalesStaffByFranchise(array $franchiseId);
}
