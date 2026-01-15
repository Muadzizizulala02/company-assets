<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateEmployee extends CreateRecord
{
    protected static string $resource = EmployeeResource::class;

    // redirect to Employee list after creating new Employee
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
