<?php

namespace App\Filament\Resources\BrandResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\DetachBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Squire\Models\Country;

class AddressesRelationManager extends RelationManager
{
  protected static string $relationship = 'addresses';
  
  protected static ?string $recordTitleAttribute = 'full_address';
  
  public function form( Form $form ) : Form
  {
    return $form
      ->schema( [
        TextInput::make( 'street' ),
        
        TextInput::make( 'zip' ),
        
        TextInput::make( 'city' ),
        
        TextInput::make( 'state' ),
        
        Forms\Components\Select::make( 'country' )
                               ->searchable()
                               ->getSearchResultsUsing( fn( string $query ) => Country::where( 'name', 'like',
                                 "%{$query}%" )->pluck( 'name', 'id' ) )
                               ->getOptionLabelUsing( fn( $value
                               ) : ?string => Country::find( $value )?->getAttribute( 'name' ) ),
      ] );
  }
  
  public function table( Table $table ) : Table
  {
    return $table
      ->columns( [
        TextColumn::make( 'street' ),
        
        TextColumn::make( 'zip' ),
        
        TextColumn::make( 'city' ),
        
        TextColumn::make( 'country' )
                  ->formatStateUsing( fn( $state ) : ?string => Country::find( $state )?->name ?? null ),
      ] )
      ->filters( [
        //
      ] )
      ->headerActions( [
        AttachAction::make(),
        CreateAction::make(),
      ] )
      ->actions( [
        EditAction::make(),
        DetachAction::make(),
        DeleteAction::make(),
      ] )
      ->groupedBulkActions( [
        DetachBulkAction::make(),
        DeleteBulkAction::make(),
      ] );
  }
}