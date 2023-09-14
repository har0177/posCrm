<?php

namespace App\Filament\Resources\CategoryResource\RelationManagers;

use App\Filament\Resources\ProductResource;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Table;

class ProductsRelationManager extends RelationManager
{
  protected static string $relationship = 'products';
  
  protected static ?string $recordTitleAttribute = 'name';
  
  public function form( Form $form ) : Form
  {
    return ProductResource::form( $form );
  }
  
  public function table( Table $table ) : Table
  {
    return ProductResource::table( $table )
                          ->headerActions( [
                            CreateAction::make(),
                          ] )
                          ->actions( [
                            DeleteAction::make(),
                          ] )
                          ->groupedBulkActions( [
                            DeleteBulkAction::make(),
                          ] );
  }
}