<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CommentsRelationManager extends RelationManager
{
  protected static string $relationship = 'comments';
  
  protected static ?string $recordTitleAttribute = 'title';
  
  public function form( Form $form ) : Form
  {
    return $form
      ->columns( 1 )
      ->schema( [
        TextInput::make( 'title' )
                 ->required(),
        
        Select::make( 'customer_id' )
              ->relationship( 'customer', 'name' )
              ->searchable()
              ->required(),
        
        Toggle::make( 'is_visible' )
              ->label( 'Approved for public' )
              ->default( true ),
        
        MarkdownEditor::make( 'content' )
                      ->required()
                      ->label( 'Content' ),
      ] );
  }
  
  public function infolist( Infolist $infolist ) : Infolist
  {
    return $infolist
      ->columns( 1 )
      ->schema( [
        TextEntry::make( 'title' ),
        TextEntry::make( 'customer.name' ),
        IconEntry::make( 'is_visible' )
                 ->label( 'Visibility' )
                 ->boolean(),
        TextEntry::make( 'content' )
                 ->markdown(),
      ] );
  }
  
  public function table( Table $table ) : Table
  {
    return $table
      ->columns( [
        TextColumn::make( 'title' )
                  ->label( 'Title' )
                  ->searchable()
                  ->sortable(),
        
        TextColumn::make( 'customer.name' )
                  ->label( 'Customer' )
                  ->searchable()
                  ->sortable(),
        
        IconColumn::make( 'is_visible' )
                  ->label( 'Visibility' )
                  ->boolean()
                  ->sortable(),
      ] )
      ->filters( [
        //
      ] )
      ->headerActions( [
        CreateAction::make()
                    ->after( function( $record ) {
                      Notification::make()
                                  ->title( 'New comment' )
                                  ->icon( 'heroicon-o-chat-bubble-bottom-center-text' )
                                  ->body( "**{$record->customer->name} commented on product ({$record->commentable->name}).**" )
                                  ->sendToDatabase( auth()->user() );
                    } ),
      ] )
      ->actions( [
        ViewAction::make(),
        EditAction::make(),
        DeleteAction::make(),
      ] )
      ->groupedBulkActions( [
        DeleteBulkAction::make(),
      ] );
  }
}