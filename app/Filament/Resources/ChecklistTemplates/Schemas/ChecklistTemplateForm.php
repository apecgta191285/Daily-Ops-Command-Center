<?php

namespace App\Filament\Resources\ChecklistTemplates\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ChecklistTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                Select::make('scope')
                    ->options([
                        'เปิดห้อง' => 'เปิดห้อง',
                        'ตรวจระหว่างวัน' => 'ตรวจระหว่างวัน',
                        'ปิดห้อง' => 'ปิดห้อง',
                    ])
                    ->required(),
                Toggle::make('is_active')
                    ->default(true)
                    ->required(),

                Repeater::make('items')
                    ->relationship('items')
                    ->schema([
                        TextInput::make('title')
                            ->required(),
                        Textarea::make('description')
                            ->columnSpanFull(),
                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(1)
                            ->required(),
                        Toggle::make('is_required')
                            ->default(true)
                            ->required(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
