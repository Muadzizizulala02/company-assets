<?php

namespace App\Filament\Resources\AssetAssignmentResource\Pages;

use App\Filament\Resources\AssetAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAssetAssignment extends CreateRecord
{
    protected static string $resource = AssetAssignmentResource::class;

    // redirect to Employee list after creating new Employee
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
