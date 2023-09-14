<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers\ProductsRelationManager;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
  protected static ?string $model = Category::class;
  
  //protected static ?string $slug = 'shop/categories';
  
  protected static ?string $recordTitleAttribute = 'name';
  
  protected static ?string $navigationGroup = 'Shop';
  
  protected static ?string $navigationIcon = 'heroicon-o-tag';
  
  protected static ?int $navigationSort = 3;
  
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
                                ->maxValue( 50 )
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
                                ->unique( Category::class, 'slug',
                                  ignoreRecord: true ),
                     ] ),
          
                 Select::make( 'parent_id' )
                       ->label( 'Parent' )
                       ->relationship( 'parent', 'name',
                         fn( Builder $query ) => $query->where( 'parent_id', null ) )
                       ->searchable()
                       ->placeholder( 'Select parent category' ),
          
                 Toggle::make( 'is_visible' )
                       ->label( 'Visible to customers.' )
                       ->default( true ),
          
                 MarkdownEditor::make( 'description' )
                               ->label( 'Description' ),
               ] )
               ->columnSpan( [ 'lg' => fn( ?Category $record ) => $record === null ? 3 : 2 ] ),
        Section::make()
               ->schema( [
                 Placeholder::make( 'created_at' )
                            ->label( 'Created at' )
                            ->content( fn( Category $record
                            ) : ?string => $record->created_at?->diffForHumans() ),
          
                 Placeholder::make( 'updated_at' )
                            ->label( 'Last modified at' )
                            ->content( fn( Category $record
                            ) : ?string => $record->updated_at?->diffForHumans() ),
               ] )
               ->columnSpan( [ 'lg' => 1 ] )
               ->hidden( fn( ?Category $record ) => $record === null ),
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
        TextColumn::make( 'parent.name' )
                  ->label( 'Parent' )
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
      ] );
  }
  
  public static function getRelations() : array
  {
    return [
      ProductsRelationManager::class,
    ];
  }
  
  public static function getPages() : array
  {
    return [
      'index'  => Pages\ListCategories::route( '/' ),
      'create' => Pages\CreateCategory::route( '/create' ),
      'edit'   => Pages\EditCategory::route( '/{record}/edit' ),
    ];
  }
}