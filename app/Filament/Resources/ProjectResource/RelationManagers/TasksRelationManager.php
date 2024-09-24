<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

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
                    ])->default('medium'),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                    ])->default('pending'),
                Forms\Components\DatePicker::make('due_date'),
                Forms\Components\Select::make('parent_id')
                    ->label('Parent Task')
                    ->options(fn() => $this->ownerRecord->tasks->pluck('name', 'id')->toArray())
                    ->placeholder('Select a parent task'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->description(
                        fn(Task $record) => $record->parent?->name
                    ),
                Tables\Columns\TextColumn::make('priority'),
                Tables\Columns\TextColumn::make('status')
                ->color(fn(string $state): string => match ($state) {
                    'pending' => 'gray',
                    'completed' => 'success',
                    'in_progress' => 'warning',
                })
                ,
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
