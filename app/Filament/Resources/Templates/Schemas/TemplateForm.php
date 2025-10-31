<?php

namespace App\Filament\Resources\Templates\Schemas;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class TemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->maxLength(255)
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('content')
                            ->mergeTags([
                                'username',
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpan(2),
            ])
            ->columns(3);
    }
}
