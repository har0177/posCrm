<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages\ManageRoles;
use App\Models\Role;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RoleResource extends Resource
{
  protected static ?string $model = Role::class;
  
  protected static ?string $navigationIcon  = 'heroicon-o-list-bullet';
  protected static ?string $navigationGroup = 'Roles & Users';
  
  public static function form( Form $form ) : Form
  {
    return $form
      ->schema( [
        TextInput::make( 'name' )
                 ->required()
                 ->maxLength( 255 ),
        // Replace the Textarea field with a SelectMultiple field
        Select::make( 'permissions' )->multiple()->searchable()
              ->options( static::formatPermissions() )
      ] );
  }
  
  public static function table( Table $table ) : Table
  {
    return $table
      ->columns( [
        TextColumn::make( 'name' )
                  ->searchable(),
        TextColumn::make( 'slug' )
                  ->searchable(),
        TextColumn::make( 'created_at' )
                  ->dateTime()
                  ->sortable()
                  ->toggleable( isToggledHiddenByDefault: true ),
        TextColumn::make( 'updated_at' )
                  ->dateTime()
                  ->sortable()
                  ->toggleable( isToggledHiddenByDefault: true ),
      ] )
      ->filters( [
        //
      ] )
      ->actions( [
        EditAction::make(),
        DeleteAction::make(),
      ] )
      ->bulkActions( [
        BulkActionGroup::make( [
          DeleteBulkAction::make(),
        ] ),
      ] )
      ->emptyStateActions( [
        CreateAction::make(),
      ] );
  }
  
  protected static function formatPermissions()
  {
    // Retrieve and format your permissions as needed
    $permissions = config( 'permissions' );
    
    return collect( $permissions )->flatMap( function( $data, $key ) {
      return collect( $data )->map( function( $value, $key ) {
        return $value;
      } );
    } )->toArray();
  }
  
  public static function getPages() : array
  {
    return [
      'index' => ManageRoles::route( '/' ),
    ];
  }
}
