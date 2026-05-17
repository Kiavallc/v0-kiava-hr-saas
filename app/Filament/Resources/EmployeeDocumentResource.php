<?php

namespace App\Filament\Resources;

use App\Models\EmployeeDocument;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EmployeeDocumentResource extends Resource
{
    protected static ?string $model = EmployeeDocument::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-check';
    protected static ?string $navigationLabel = 'Document Approvals';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Document Information')
                    ->schema([
                        Forms\Components\Select::make('employee_id')
                            ->relationship('employee', 'id')
                            ->required()
                            ->searchable(),
                        Forms\Components\Select::make('document_requirement_id')
                            ->relationship('requirement', 'name')
                            ->required(),
                        Forms\Components\DatePicker::make('expiration_date')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->required(),
                        Forms\Components\TextArea::make('rejection_reason')
                            ->nullable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('requirement.name')->label('Document Type'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ]),
                Tables\Columns\TextColumn::make('expiration_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('approve')
                    ->action(function (EmployeeDocument $record) {
                        $record->update(['status' => 'approved']);
                    })
                    ->requiresConfirmation()
                    ->visible(fn(EmployeeDocument $record) => $record->status === 'pending'),
                Tables\Actions\Action::make('reject')
                    ->form([
                        Forms\Components\TextArea::make('rejection_reason')
                            ->required(),
                    ])
                    ->action(function (EmployeeDocument $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                        ]);
                    })
                    ->requiresConfirmation()
                    ->visible(fn(EmployeeDocument $record) => $record->status === 'pending'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\EmployeeDocumentResource\Pages\ListEmployeeDocuments::class,
            'create' => \App\Filament\Resources\EmployeeDocumentResource\Pages\CreateEmployeeDocument::class,
            'edit' => \App\Filament\Resources\EmployeeDocumentResource\Pages\EditEmployeeDocument::class,
        ];
    }
}
