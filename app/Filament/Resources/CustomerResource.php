<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers\AddressesRelationManager;
use App\Filament\Resources\CustomerResource\RelationManagers\PaymentsRelationManager;
use App\Models\Customer;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Squire\Models\Country;

class CustomerResource extends Resource
{
  protected static ?string $model = Customer::class;
  
  // protected static ?string $slug = 'shop/customers';
  
  protected static ?string $recordTitleAttribute = 'name';
  
  protected static ?string $navigationGroup = 'Shop';
  
  protected static ?string $navigationIcon = 'heroicon-o-user-group';
  
  protected static ?int $navigationSort = 1;
  
  public static function form( Form $form ) : Form
  {
    return $form
      ->schema( [
        Section::make()
               ->schema( [
                 TextInput::make( 'name' )
                          ->maxValue( 50 )
                          ->required(),
          
                 TextInput::make( 'email' )
                          ->label( 'Email address' )
                          ->required()
                          ->email()
                          ->unique( ignoreRecord: true ),
          
                 TextInput::make( 'phone' )
                          ->maxValue( 50 )
               ] )
               ->columns( 2 )
               ->columnSpan( [ 'lg' => fn( ?Customer $record ) => $record === null ? 3 : 2 ] ),
        
        Section::make()
               ->schema( [
                 Placeholder::make( 'created_at' )
                            ->label( 'Created at' )
                            ->content( fn( Customer $record
                            ) : ?string => $record->created_at?->diffForHumans() ),
          
                 Placeholder::make( 'updated_at' )
                            ->label( 'Last modified at' )
                            ->content( fn( Customer $record
                            ) : ?string => $record->updated_at?->diffForHumans() ),
               ] )
               ->columnSpan( [ 'lg' => 1 ] )
               ->hidden( fn( ?Customer $record ) => $record === null ),
      ] )
      ->columns( 3 );
  }
  
  public static function table( Table $table ) : Table
  {
    return $table
      ->columns( [
        TextColumn::make( 'name' )
                  ->searchable( isIndividual: true )
                  ->sortable(),
        TextColumn::make( 'email' )
                  ->label( 'Email address' )
                  ->searchable( isIndividual: true, isGlobal: false )
                  ->sortable(),
        TextColumn::make( 'country' )
                  ->getStateUsing( fn( $record
                  ) : ?string => Country::find( $record->addresses->first()?->country )?->name ?? null ),
        TextColumn::make( 'phone' )
                  ->searchable()
                  ->sortable(),
      ] )
      ->filters( [
        TrashedFilter::make(),
      ] )
      ->actions( [
        EditAction::make(),
      ] )
      ->groupedBulkActions( [
        DeleteBulkAction::make()
                        ->action( function() {
                          Notification::make()
                                      ->title( 'Now, now, don\'t be cheeky, leave some records for others to play with!' )
                                      ->warning()
                                      ->send();
                        } ),
      ] );
  }
  
  public static function getEloquentQuery() : Builder
  {
    return parent::getEloquentQuery()->with( 'addresses' )->withoutGlobalScope( SoftDeletingScope::class );
  }
  
  public static function getRelations() : array
  {
    return [
      AddressesRelationManager::class,
      PaymentsRelationManager::class,
    ];
  }
  
  public static function getPages() : array
  {
    return [
      'index'  => Pages\ListCustomers::route( '/' ),
      'create' => Pages\CreateCustomer::route( '/create' ),
      'edit'   => Pages\EditCustomer::route( '/{record}/edit' ),
    ];
  }
  
  public static function getGloballySearchableAttributes() : array
  {
    return [ 'name', 'email' ];
  }
}