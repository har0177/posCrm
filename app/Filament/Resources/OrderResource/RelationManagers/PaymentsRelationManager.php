<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Akaunting\Money\Currency;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
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
        Forms\Components\TextInput::make( 'reference' )
                                  ->columnSpan( 'full' )
                                  ->required(),
        
        Forms\Components\TextInput::make( 'amount' )
                                  ->numeric()
                                  ->rules( [ 'regex:/^\d{1,6}(\.\d{0,2})?$/' ] )
                                  ->required(),
        
        Forms\Components\Select::make( 'currency' )
                               ->options( collect( Currency::getCurrencies() )->mapWithKeys( fn(
                                 $item,
                                 $key
                               ) => [ $key => data_get( $item, 'name' ) ] ) )
                               ->searchable()
                               ->required(),
        
        Forms\Components\Select::make( 'provider' )
                               ->options( [
                                 'stripe' => 'Stripe',
                                 'paypal' => 'PayPal',
                               ] )
                               ->required()
                               ->native( false ),
        
        Forms\Components\Select::make( 'method' )
                               ->options( [
                                 'credit_card'   => 'Credit card',
                                 'bank_transfer' => 'Bank transfer',
                                 'paypal'        => 'PayPal',
                               ] )
                               ->required()
                               ->native( false ),
      ] );
  }
  
  public function table( Table $table ) : Table
  {
    return $table
      ->columns( [
        Tables\Columns\TextColumn::make( 'reference' )
                                 ->searchable(),
        
        Tables\Columns\TextColumn::make( 'amount' )
                                 ->sortable()
                                 ->money( fn( $record ) => $record->currency ),
        
        Tables\Columns\TextColumn::make( 'provider' )
                                 ->formatStateUsing( fn( $state ) => Str::headline( $state ) ),
        
        Tables\Columns\TextColumn::make( 'method' )
                                 ->formatStateUsing( fn( $state ) => Str::headline( $state ) ),
      ] )
      ->filters( [
        //
      ] )
      ->headerActions( [
        Tables\Actions\CreateAction::make(),
      ] )
      ->actions( [
        Tables\Actions\EditAction::make(),
        Tables\Actions\DeleteAction::make(),
      ] )
      ->groupedBulkActions( [
        Tables\Actions\DeleteBulkAction::make(),
      ] );
  }
}