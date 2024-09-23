<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use App\Filament\Resources\TaskResource\RelationManagers;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // $table->string('name');
                // $table->text('content')->nullable();
                // $table->foreignId('project_id')->constrained()->onDelete('cascade');
                // $table->timestamp('due_date')->nullable();
                // $table->string('priority')->nullable();
                // $table->string('status')->default('pending');
                // $table->timestamp('completed_at')->nullable();
                // $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
                // $table->timestamp('reminder')->nullable();

                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required(),
                Forms\Components\Textarea::make('content')
                    ->label('Content'),
                Forms\Components\Select::make('project_id')
                    ->label('Project')
                    ->options(fn() => \App\Models\Project::pluck('name', 'id'))
                    ->required()
                    ->reactive(), // Add this to make it reactive
                Forms\Components\Select::make('parent_id')
                    ->label('Parent Task')
                    ->options(function (callable $get) {
                        $projectId = $get('project_id');
                        // Ensure we only show tasks from the selected project
                        return \App\Models\Task::where('project_id', $projectId)
                            ->whereNull('parent_id')
                            ->pluck('name', 'id');
                    })
                    ->nullable(),
                Forms\Components\DatePicker::make('due_date')
                    ->label('Due Date'),
                Forms\Components\Select::make('priority')
                    ->label('Priority')
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                    ]),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Beklemede',
                        'in-progress' => 'İşlemde',
                        'completed' => 'Tamamlandı',
                        'rejected' => 'Yarım Bırakıldı',
                    ])
                    ->default('pending'),
                Forms\Components\DatePicker::make('completed_at')
                    ->label('Completed At'),
                Forms\Components\Select::make('assigned_to')
                    ->label('Assigned To')
                    ->options(fn() => \App\Models\User::pluck('name', 'id'))
                    ->nullable(),
                Forms\Components\DatePicker::make('reminder')
                    ->label('Reminder'),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('children')
                    ->label('Progress') // Sütun başlığı
                    ->formatStateUsing(function (Task $record) {
                        // Görevin tüm alt görevlerinin sayısını bul
                        $totalChildren = $record->children()->count();

                        // Eğer alt görev yoksa "Yorum" göster
                        // if ($totalChildren == 0) {
                        //     return 'Alt Görev Yok';
                        // }

                        $completedChildren = $record->children()->where('status', 'completed')->count();

                        return "{$completedChildren}/{$totalChildren}";
                    }),


                    // açıklamada proje adı olsun
                Tables\Columns\TextColumn::make('parent.name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    // ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'gray',
                        'completed' => 'success',
                        'in_progress' => 'warning',
                        'rejected' => 'danger',
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('project.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('priority')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('completed_at')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('assigned_to')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reminder')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->defaultGroup(Group::make('parent.name')
                ->collapsible())
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ChildrenRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
