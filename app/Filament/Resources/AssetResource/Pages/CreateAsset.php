<?php

namespace App\Filament\Resources\AssetResource\Pages;

use App\Filament\Resources\AssetResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAsset extends CreateRecord
{
    protected static string $resource = AssetResource::class;

    // redirect to Employee list after creating new Employee
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
