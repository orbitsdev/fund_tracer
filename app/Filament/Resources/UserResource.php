<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Program;
use App\Models\Project;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Group;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Rawilk\FilamentPasswordInput\Password;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('User Information') // Add a title for the user information
                ->icon('heroicon-m-user') // Add an icon for the user section
                ->description('Provide user details below for authentication and access control.') // Add a description for the user section
                ->columns([
                    'sm' => 3,
                    'xl' => 6,
                    '2xl' => 8,
                ])
                ->schema([
                   
                    TextInput::make('first_name')
                        ->required()
                        ->maxLength(191)
                        ->columnSpan(4)
                        ,
                    TextInput::make('last_name')
                        ->required()
                        ->columnSpan(4)

                        ->maxLength(191),
                  
                    TextInput::make('email')
                    // ->prefix('@')
                        ->email()
                        ->columnSpanFull()
                        ->columnSpan(4)
                        ->required(),
                    Password::make('password')
                    ->showPasswordIcon('heroicon-m-eye-slash')
                    ->hidePasswordIcon('heroicon-m-eye')
                        ->label(fn (string $operation) => $operation =='create' ? 'Password' : 'New Password')
                        ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                        ->dehydrated(fn (?string $state): bool => filled($state))
                        ->required(fn (string $operation): bool => $operation === 'create')
                        ->columnSpan(4),

                        

                        Select::make('role')
                        ->label('Set this user as')
                        ->options([
                            'Admin' => 'Admin',
                            'Project Manager' => 'Project Manager',
                            'Finance Manager' => 'Finance Manager',
                        ])
                        ->searchable()
                        ->live()
                        ->default('Project Manager')
                        ->columnSpanFull()
                        ->native(false),
                        // Group::make()
                        // ->columnSpanFull()
                        // ->relationship('managerProject')
                        // ->schema([
                        //     Select::make('user_id')
                        //     ->label('Assigned to')
                        //     ->required()
                        //     ->options(Project::whereDoesntHave('manager')->pluck('title', 'id'))
                        //     ->native(false)
                        //     ->preload()
                        //     ->searchable()
                        //     ->columnSpanFull()
                        //     ->visible(function(Get $get){
                        //         $role = $get('../role');
                        //         if(!empty($role)){
                        //             return $role == 'Project Manager';
                        //         }
                        //     })
                        //     ,
                        // ]),

                  
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            TextColumn::make('first_name')
                    ->searchable(),
            TextColumn::make('last_name')
                    ->searchable(),
            TextColumn::make('role')
            ->badge()
            ->color('gray')
            // ->color(fn (string $state): string => match ($state) {
            //     'Admin' => 'info',
            //     'Project Manager' => 'success',
            //     'Finance Manager' => 'gray',
            //     default=> 'gray',
            // }) 
                    ->searchable(),
            TextColumn::make('email')
                    ->searchable(),
          
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->groups([
                'role', 
            ]);
            ;
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
