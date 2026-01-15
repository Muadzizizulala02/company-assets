<?php

namespace App\Filament\Resources\AssetAssignmentResource\Pages;

use App\Filament\Resources\AssetAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAssetAssignment extends EditRecord
{
    protected static string $resource = AssetAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // redirect to Employee list after creating new Employee
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
