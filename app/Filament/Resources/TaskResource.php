<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use App\Filament\Resources\TaskResource\RelationManagers;
use Carbon\Carbon;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
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
                // Ana grid yapısı
                Forms\Components\Grid::make(12) // 12 sütunlu grid yapısı
                    ->schema([

                        // İlk satır: col-md-6 ve col-md-6
                        Forms\Components\TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->columnSpan(5), // col-md-6 genişlik

                        Forms\Components\Select::make('project_id')
                            ->label('Project')
                            ->options(fn() => \App\Models\Project::pluck('name', 'id'))
                            ->required()
                            ->reactive() // Seçim yapıldığında tetiklenir
                            ->columnSpan(3), // col-md-6 genişlik


                        // Üçüncü satır: col-md-6 ve col-md-6
                        Forms\Components\Select::make('parent_id')
                            ->label('Parent Task')
                            ->options(function (callable $get) {
                                $projectId = $get('project_id');
                                // Sadece seçili projedeki görevleri göster
                                return \App\Models\Task::where('project_id', $projectId)
                                    ->whereNull('parent_id')
                                    ->pluck('name', 'id');
                            })
                            ->nullable()
                            ->columnSpan(4), // col-md-6 genişlik

                        // İkinci satır: col-md-12 (tam genişlik)
                        Forms\Components\RichEditor::make('content')
                            ->label('Content')
                            ->columnSpan(12), // col-md-12 tam genişlik



                        // Dördüncü satır: col-md-4 ve col-md-4
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Beklemede',
                                'in_progress' => 'İşlemde',
                                'completed' => 'Tamamlandı',
                                'rejected' => 'Yarım Bırakıldı',
                            ])
                            ->default('pending')
                            ->columnSpan(3), // col-md-4 genişlik


                        Forms\Components\Select::make('priority')
                            ->label('Priority')
                            ->options([
                                'low' => 'Low',
                                'medium' => 'Medium',
                                'high' => 'High',
                            ])
                            ->columnSpan(3), // col-md-3 genişlik

                        Forms\Components\DateTimePicker::make('due_date')
                            ->label('Due Date')
                            ->columnSpan(3), // col-md-3 genişlik

                        Forms\Components\DateTimePicker::make('completed_at')
                            ->label('Completed At')
                            ->columnSpan(3), // col-md-4 genişlik
                    ]),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->description(
                        fn(Task $record) => $record->parent?->name
                    )
                    ->wrap()
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
                TextColumn::make('due_date')
                    ->label('Due Date')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        return
                            Carbon::parse($state)->format('Y-m-d') .
                            "<br>" .
                            Carbon::parse($state)->format('H:i');
                    })
                    ->alignment(Alignment::Center)
                    ->color(function ($state, $record) {
                        $dueDate = Carbon::parse($state);
                        $today = Carbon::today();

                        // Görev tamamlandıysa yeşil
                        if ($record->status === 'completed') {
                            return 'success'; // Yeşil renk
                        }

                        // Tarih geçmişse kırmızı
                        if ($dueDate->isPast()) {
                            return 'danger'; // Kırmızı renk
                        }

                        // Bugünün tarihi ise sarı
                        if ($dueDate->isToday()) {
                            return 'warning'; // Sarı renk
                        }

                        return null; // Varsayılan renk
                    })
                    ->weight(FontWeight::Bold)
                    ->html()
                    ->fontFamily('font-mono'),
                Tables\Columns\TextColumn::make('priority')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('completed_at')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                // proje filtrese
                Tables\Filters\SelectFilter::make('project_id')
                    ->options(fn() => \App\Models\Project::pluck('name', 'id'))
                    ->label('Project')
                    ->placeholder('Select Project'),
                // durum filtrele
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Beklemede',
                        'in_progress' => 'İşlemde',
                        'completed' => 'Tamamlandı',
                        'rejected' => 'Yarım Bırakıldı',
                    ])
                    ->label('Status')
                    ->placeholder('Select Status'),
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
            ])
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
