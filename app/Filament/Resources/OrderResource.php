<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers\PaymentsRelationManager;
use App\Filament\Resources\OrderResource\Widgets\OrderStats;
use App\Forms\Components\AddressForm;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;
use Squire\Models\Currency;

class OrderResource extends Resource
{
  protected static ?string $model = Order::class;
  
  // protected static ?string $slug = 'shop/orders';
  
  protected static ?string $recordTitleAttribute = 'number';
  
  protected static ?string $navigationGroup = 'Shop';
  
  protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
  
  protected static ?int $navigationSort = 2;
  
  public static function form( Form $form ) : Form
  {
    return $form
      ->schema( [
        Group::make()
             ->schema( [
               Section::make()
                      ->schema( static::getFormSchema() )
                      ->columns( 2 ),
          
               Section::make( 'Order items' )
                      ->schema( static::getFormSchema( 'items' ) ),
             ] )
             ->columnSpan( [ 'lg' => fn( ?Order $record ) => $record === null ? 3 : 2 ] ),
        
        Section::make()
               ->schema( [
                 Placeholder::make( 'created_at' )
                            ->label( 'Created at' )
                            ->content( fn( Order $record
                            ) : ?string => $record->created_at?->diffForHumans() ),
          
                 Placeholder::make( 'updated_at' )
                            ->label( 'Last modified at' )
                            ->content( fn( Order $record
                            ) : ?string => $record->updated_at?->diffForHumans() ),
               ] )
               ->columnSpan( [ 'lg' => 1 ] )
               ->hidden( fn( ?Order $record ) => $record === null ),
      ] )
      ->columns( 3 );
  }
  
  public static function table( Table $table ) : Table
  {
    return $table
      ->columns( [
        TextColumn::make( 'number' )
                  ->searchable()
                  ->sortable(),
        TextColumn::make( 'customer.name' )
                  ->searchable()
                  ->sortable()
                  ->toggleable(),
        BadgeColumn::make( 'status' )
                   ->colors( [
                     'danger'  => 'cancelled',
                     'warning' => 'processing',
                     'success' => fn( $state ) => in_array( $state, [ 'delivered', 'shipped' ] ),
                   ] ),
        TextColumn::make( 'currency' )
                  ->getStateUsing( fn( $record
                  ) : ?string => Currency::find( $record->currency )?->name ?? null )
                  ->searchable()
                  ->sortable()
                  ->toggleable(),
        TextColumn::make( 'total_price' )
                  ->searchable()
                  ->sortable()
                  ->summarize( [
                    Sum::make()
                       ->money(),
                  ] ),
        TextColumn::make( 'shipping_price' )
                  ->label( 'Shipping cost' )
                  ->searchable()
                  ->sortable()
                  ->toggleable()
                  ->summarize( [
                    Sum::make()
                       ->money(),
                  ] ),
        TextColumn::make( 'created_at' )
                  ->label( 'Order Date' )
                  ->date()
                  ->toggleable(),
      ] )
      ->filters( [
        TrashedFilter::make(),
        
        Filter::make( 'created_at' )
              ->form( [
                DatePicker::make( 'created_from' )
                          ->placeholder( fn( $state
                          ) : string => 'Dec 18, ' . now()->subYear()->format( 'Y' ) ),
                DatePicker::make( 'created_until' )
                          ->placeholder( fn( $state
                          ) : string => now()->format( 'M d, Y' ) ),
              ] )
              ->query( function( Builder $query, array $data ) : Builder {
                return $query
                  ->when(
                    $data[ 'created_from' ] ?? null,
                    fn( Builder $query, $date ) : Builder => $query->whereDate( 'created_at', '>=',
                      $date ),
                  )
                  ->when(
                    $data[ 'created_until' ] ?? null,
                    fn( Builder $query, $date ) : Builder => $query->whereDate( 'created_at', '<=',
                      $date ),
                  );
              } )
              ->indicateUsing( function( array $data ) : array {
                $indicators = [];
                if( $data[ 'created_from' ] ?? null ) {
                  $indicators[ 'created_from' ] = 'Order from ' . Carbon::parse( $data[ 'created_from' ] )->toFormattedDateString();
                }
                if( $data[ 'created_until' ] ?? null ) {
                  $indicators[ 'created_until' ] = 'Order until ' . Carbon::parse( $data[ 'created_until' ] )->toFormattedDateString();
                }
          
                return $indicators;
              } ),
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
      ->groups( [
        Tables\Grouping\Group::make( 'created_at' )
                             ->label( 'Order Date' )
                             ->date()
                             ->collapsible(),
      ] );
  }
  
  public static function getRelations() : array
  {
    return [
      PaymentsRelationManager::class,
    ];
  }
  
  public static function getWidgets() : array
  {
    return [
      OrderStats::class,
    ];
  }
  
  public static function getPages() : array
  {
    return [
      'index'  => Pages\ListOrders::route( '/' ),
      'create' => Pages\CreateOrder::route( '/create' ),
      'edit'   => Pages\EditOrder::route( '/{record}/edit' ),
    ];
  }
  
  public static function getEloquentQuery() : Builder
  {
    return parent::getEloquentQuery()->withoutGlobalScope( SoftDeletingScope::class );
  }
  
  public static function getGloballySearchableAttributes() : array
  {
    return [ 'number', 'customer.name' ];
  }
  
  public static function getGlobalSearchResultDetails( Model $record ) : array
  {
    /** @var Order $record */
    
    return [
      'Customer' => optional( $record->customer )->name,
    ];
  }
  
  public static function getGlobalSearchEloquentQuery() : Builder
  {
    return parent::getGlobalSearchEloquentQuery()->with( [ 'customer', 'items' ] );
  }
  
  public static function getNavigationBadge() : ?string
  {
    return static::$model::where( 'status', 'new' )->count();
  }
  
  public static function getFormSchema( string $section = null ) : array
  {
    if( $section === 'items' ) {
      return [
        Repeater::make( 'items' )
                ->relationship()
                ->schema( [
                  Select::make( 'product_id' )
                        ->label( 'Product' )
                        ->options( Product::query()->pluck( 'name', 'id' ) )
                        ->required()
                        ->reactive()
                        ->afterStateUpdated( fn(
                          $state,
                          Forms\Set $set
                        ) => $set( 'unit_price',
                          Product::find( $state )?->price ?? 0 ) )
                        ->columnSpan( [
                          'md' => 5,
                        ] )
                        ->searchable(),
          
                  TextInput::make( 'qty' )
                           ->label( 'Quantity' )
                           ->numeric()
                           ->default( 1 )
                           ->columnSpan( [
                             'md' => 2,
                           ] )
                           ->required(),
          
                  TextInput::make( 'unit_price' )
                           ->label( 'Unit Price' )
                           ->disabled()
                           ->dehydrated()
                           ->numeric()
                           ->required()
                           ->columnSpan( [
                             'md' => 3,
                           ] ),
                ] )
                ->orderable()
                ->defaultItems( 1 )
                ->disableLabel()
                ->columns( [
                  'md' => 10,
                ] )
                ->required(),
      ];
    }
    
    return [
      TextInput::make( 'number' )
               ->default( 'OR-' . random_int( 100000, 999999 ) )
               ->disabled()
               ->dehydrated()
               ->required(),
      
      Select::make( 'customer_id' )
            ->relationship( 'customer', 'name' )
            ->searchable()
            ->required()
            ->createOptionForm( [
              TextInput::make( 'name' )
                       ->required(),
        
              TextInput::make( 'email' )
                       ->label( 'Email address' )
                       ->required()
                       ->email()
                       ->unique(),
        
              TextInput::make( 'phone' ),
        
              Select::make( 'gender' )
                    ->placeholder( 'Select gender' )
                    ->options( [
                      'male'   => 'Male',
                      'female' => 'Female',
                    ] )
                    ->required()
                    ->native( false ),
            ] )
            ->createOptionAction( function( Forms\Components\Actions\Action $action ) {
              return $action
                ->modalHeading( 'Create customer' )
                ->modalButton( 'Create customer' )
                ->modalWidth( 'lg' );
            } ),
      
      Select::make( 'status' )
            ->options( [
              'new'        => 'New',
              'processing' => 'Processing',
              'shipped'    => 'Shipped',
              'delivered'  => 'Delivered',
              'cancelled'  => 'Cancelled',
            ] )
            ->required()
            ->native( false ),
      
      Select::make( 'currency' )
            ->searchable()
            ->getSearchResultsUsing( fn( string $query ) => Currency::where( 'name', 'like',
              "%{$query}%" )->pluck( 'name', 'id' ) )
            ->getOptionLabelUsing( fn( $value
            ) : ?string => Currency::find( $value )?->getAttribute( 'name' ) )
            ->required(),
      
      AddressForm::make( 'address' )
                 ->columnSpan( 'full' ),
      
      MarkdownEditor::make( 'notes' )
                    ->columnSpan( 'full' ),
    ];
  }
}