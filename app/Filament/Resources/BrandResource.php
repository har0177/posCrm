<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BrandResource\Pages;
use App\Filament\Resources\BrandResource\RelationManagers\AddressesRelationManager;
use App\Filament\Resources\BrandResource\RelationManagers\ProductsRelationManager;
use App\Models\Brand;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class BrandResource extends Resource
{
  protected static ?string $model = Brand::class;
  
  //protected static ?string $slug = 'shop/brands';
  
  protected static ?string $recordTitleAttribute = 'name';
  
  protected static ?string $navigationGroup = 'Shop';
  
  protected static ?string $navigationIcon = 'heroicon-o-bookmark-square';
  
  protected static ?int $navigationSort = 4;
  
  public static function form( Form $form ) : Form
  {
    return $form
      ->schema( [
        Section::make()
               ->schema( [
                 Grid::make()
                     ->schema( [
                       TextInput::make( 'name' )
                                ->required()
                                ->live( onBlur: true )
                                ->afterStateUpdated( fn(
                                  string    $operation,
                                            $state,
                                  Forms\Set $set
                                ) => $operation === 'create' ? $set( 'slug',
                                  Str::slug( $state ) ) : null ),
            
                       TextInput::make( 'slug' )
                                ->disabled()
                                ->dehydrated()
                                ->required()
                                ->unique( Brand::class, 'slug',
                                  ignoreRecord: true ),
                     ] ),
                 TextInput::make( 'website' )
                          ->required()
                          ->url(),
          
                 Toggle::make( 'is_visible' )
                       ->label( 'Visible to customers.' )
                       ->default( true ),
          
                 MarkdownEditor::make( 'description' )
                               ->label( 'Description' ),
               ] )
               ->columnSpan( [ 'lg' => fn( ?Brand $record ) => $record === null ? 3 : 2 ] ),
        Section::make()
               ->schema( [
                 Placeholder::make( 'created_at' )
                            ->label( 'Created at' )
                            ->content( fn( Brand $record
                            ) : ?string => $record->created_at?->diffForHumans() ),
          
                 Placeholder::make( 'updated_at' )
                            ->label( 'Last modified at' )
                            ->content( fn( Brand $record
                            ) : ?string => $record->updated_at?->diffForHumans() ),
               ] )
               ->columnSpan( [ 'lg' => 1 ] )
               ->hidden( fn( ?Brand $record ) => $record === null ),
      ] )
      ->columns( 3 );
  }
  
  public static function table( Table $table ) : Table
  {
    return $table
      ->columns( [
        TextColumn::make( 'name' )
                  ->label( 'Name' )
                  ->searchable()
                  ->sortable(),
        TextColumn::make( 'website' )
                  ->label( 'Website' )
                  ->searchable()
                  ->sortable(),
        IconColumn::make( 'is_visible' )
                  ->label( 'Visibility' )
                  ->boolean()
                  ->sortable(),
        TextColumn::make( 'updated_at' )
                  ->label( 'Updated Date' )
                  ->date()
                  ->sortable(),
      ] )
      ->filters( [
        //
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
      ] )
      ->defaultSort( 'sort' )
      ->reorderable( 'sort' );
  }
  
  public static function getRelations() : array
  {
    return [
      ProductsRelationManager::class,
      AddressesRelationManager::class,
    ];
  }
  
  public static function getPages() : array
  {
    return [
      'index'  => Pages\ListBrands::route( '/' ),
      'create' => Pages\CreateBrand::route( '/create' ),
      'edit'   => Pages\EditBrand::route( '/{record}/edit' ),
    ];
  }
}