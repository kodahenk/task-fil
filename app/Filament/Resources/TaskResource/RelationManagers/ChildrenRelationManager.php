<?php

namespace App\Filament\Resources\TaskResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ChildrenRelationManager extends RelationManager
{
    protected static string $relationship = 'children';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('priority')
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                    ]),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Beklemede',
                        'in_progress' => 'İşlemde',
                        'completed' => 'Tamamlandı',
                        'rejected' => 'Yarım Bırakıldı',
                    ])->default('pending'),
                Forms\Components\DatePicker::make('due_date'),
                Forms\Components\Hidden::make('project_id')
                    ->default(fn(RelationManager $livewire) => $livewire->ownerRecord->project_id), // Ana görevin project_id'sini otomatik olarak ata
                Forms\Components\Hidden::make('parent_id')
                    ->default(fn(RelationManager $livewire) => $livewire->ownerRecord->id), // Ana görevin parent_id'sini otomatik olarak ata
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('status')
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'gray',
                        'completed' => 'success',
                        'in_progress' => 'warning',
                        'rejected' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('priority'),
                Tables\Columns\TextColumn::make('due_date'),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
