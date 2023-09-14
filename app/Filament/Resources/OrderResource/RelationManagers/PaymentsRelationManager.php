<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Akaunting\Money\Currency;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PaymentsRelationManager extends RelationManager
{
  protected static string $relationship = 'payments';
  
  protected static ?string $recordTitleAttribute = 'reference';
  
  public function form( Form $form ) : Form
  {
    return $form
      ->schema( [
        TextInput::make( 'reference' )
                 ->columnSpan( 'full' )
                 ->required(),
        
        TextInput::make( 'amount' )
                 ->numeric()
                 ->rules( [ 'regex:/^\d{1,6}(\.\d{0,2})?$/' ] )
                 ->required(),
        
        Select::make( 'currency' )
              ->options( collect( Currency::getCurrencies() )->mapWithKeys( fn(
                $item,
                $key
              ) => [ $key => data_get( $item, 'name' ) ] ) )
              ->searchable()
              ->required(),
        
        Select::make( 'provider' )
              ->options( [
                'stripe' => 'Stripe',
                'paypal' => 'PayPal',
              ] )
              ->required()
              ->native( false ),
        
        Select::make( 'method' )
              ->options( [
                'credit_card' => 'Credit card',
                'bank_transfer' => 'Bank transfer',
                'paypal' => 'PayPal',
              ] )
              ->required()
              ->native( false ),
      ] );
  }
  
  public function table( Table $table ) : Table
  {
    return $table
      ->columns( [
        TextColumn::make( 'reference' )
                  ->searchable(),
        
        TextColumn::make( 'amount' )
                  ->sortable()
                  ->money( fn( $record ) => $record->currency ),
        
        TextColumn::make( 'provider' )
                  ->formatStateUsing( fn( $state ) => Str::headline( $state ) ),
        
        TextColumn::make( 'method' )
                  ->formatStateUsing( fn( $state ) => Str::headline( $state ) ),
      ] )
      ->filters( [
        //
      ] )
      ->headerActions( [
        CreateAction::make(),
      ] )
      ->actions( [
        EditAction::make(),
        DeleteAction::make(),
      ] )
      ->groupedBulkActions( [
        DeleteBulkAction::make(),
      ] );
  }
}