<?php
		
		namespace App\Filament\Resources;
		
		use App\Filament\Resources\BrandResource\RelationManagers\ProductsRelationManager;
		use App\Filament\Resources\ProductResource\Pages;
		use App\Filament\Resources\ProductResource\RelationManagers\CommentsRelationManager;
		use App\Filament\Resources\ProductResource\Widgets\ProductStats;
		use App\Models\Product;
		use Filament\Forms;
		use Filament\Forms\Components\Checkbox;
		use Filament\Forms\Components\DatePicker;
		use Filament\Forms\Components\Group;
		use Filament\Forms\Components\MarkdownEditor;
		use Filament\Forms\Components\Section;
		use Filament\Forms\Components\Select;
		use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
		use Filament\Forms\Components\TextInput;
		use Filament\Forms\Components\Toggle;
		use Filament\Forms\Form;
		use Filament\Notifications\Notification;
		use Filament\Resources\Resource;
		use Filament\Tables\Actions\DeleteBulkAction;
		use Filament\Tables\Actions\EditAction;
		use Filament\Tables\Columns\IconColumn;
		use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
		use Filament\Tables\Columns\TextColumn;
		use Filament\Tables\Filters\SelectFilter;
		use Filament\Tables\Filters\TernaryFilter;
		use Filament\Tables\Table;
		use Illuminate\Database\Eloquent\Builder;
		use Illuminate\Database\Eloquent\Model;
		use Illuminate\Support\Str;
		class ProductResource extends Resource
		{
				
				protected static ?string $model = Product::class;
				
				// protected static ?string $slug = 'shop/products';
				protected static ?string $recordTitleAttribute = 'name';
				
				protected static ?string $navigationGroup = 'Shop';
				
				protected static ?string $navigationIcon = 'heroicon-o-bolt';
				
				protected static ?string $navigationLabel = 'Products';
				
				protected static ?int $navigationSort = 0;
				
				public static function form( Form $form ) : Form
				{
						return $form
								->schema( [
										Group::make()
										     ->schema( [
												     Section::make()
												            ->schema( [
														            TextInput::make( 'name' )
														                     ->required()
														                     ->live( onBlur: true )
														                     ->afterStateUpdated( function(
																                     string $operation,
																                     $state,
																                     Forms\Set $set
														                     ) {
																                     if( $operation !== 'create' ) {
																		                     return;
																                     }
																                     $set( 'slug',
																		                     Str::slug( $state ) );
														                     } ),
														            TextInput::make( 'slug' )
														                     ->disabled()
														                     ->dehydrated()
														                     ->required()
														                     ->unique( Product::class, 'slug',
																                     ignoreRecord: true ),
														            MarkdownEditor::make( 'description' )
														                          ->columnSpan( 'full' ),
												            ] )
												            ->columns( 2 ),
												     Section::make( 'Images' )
												            ->schema( [
														            SpatieMediaLibraryFileUpload::make( 'media' )
														                                        ->collection( 'product-images' )
														                                        ->multiple()
														                                        ->maxFiles( 5 )
														                                        ->disableLabel(),
												            ] )
												            ->collapsible(),
												     Section::make( 'Pricing' )
												            ->schema( [
														            TextInput::make( 'price' )
														                     ->numeric()
														                     ->rules( [ 'regex:/^\d{1,6}(\.\d{0,2})?$/' ] )
														                     ->required(),
														            TextInput::make( 'old_price' )
														                     ->label( 'Compare at price' )
														                     ->numeric()
														                     ->rules( [ 'regex:/^\d{1,6}(\.\d{0,2})?$/' ] )
														                     ->required(),
														            TextInput::make( 'cost' )
														                     ->label( 'Cost per item' )
														                     ->helperText( 'Customers won\'t see this price.' )
														                     ->numeric()
														                     ->rules( [ 'regex:/^\d{1,6}(\.\d{0,2})?$/' ] )
														                     ->required(),
												            ] )
												            ->columns( 2 ),
												     Section::make( 'Inventory' )
												            ->schema( [
														            TextInput::make( 'sku' )
														                     ->label( 'SKU (Stock Keeping Unit)' )
														                     ->unique( Product::class, 'sku',
																                     ignoreRecord: true )
														                     ->required(),
														            TextInput::make( 'barcode' )
														                     ->label( 'Barcode (ISBN, UPC, GTIN, etc.)' )
														                     ->unique( Product::class, 'barcode',
																                     ignoreRecord: true )
														                     ->required(),
														            TextInput::make( 'qty' )
														                     ->label( 'Quantity' )
														                     ->numeric()
														                     ->rules( [ 'integer', 'min:0' ] )
														                     ->required(),
														            TextInput::make( 'security_stock' )
														                     ->helperText( 'The safety stock is the limit stock for your products which alerts you if the product stock will soon be out of stock.' )
														                     ->numeric()
														                     ->rules( [ 'integer', 'min:0' ] )
														                     ->required(),
												            ] )
												            ->columns( 2 ),
												     Section::make( 'Shipping' )
												            ->schema( [
														            Checkbox::make( 'backorder' )
														                    ->label( 'This product can be returned' ),
														            Checkbox::make( 'requires_shipping' )
														                    ->label( 'This product will be shipped' ),
												            ] )
												            ->columns( 2 ),
										     ] )
										     ->columnSpan( [ 'lg' => 2 ] ),
										Group::make()
										     ->schema( [
												     Section::make( 'Status' )
												            ->schema( [
														            Toggle::make( 'is_visible' )
														                  ->label( 'Visible' )
														                  ->helperText( 'This product will be hidden from all sales channels.' )
														                  ->default( true ),
														            DatePicker::make( 'published_at' )
														                      ->label( 'Availability' )
														                      ->default( now() )
														                      ->required(),
												            ] ),
												     Section::make( 'Associations' )
												            ->schema( [
														            Select::make( 'brand_id' )
														                  ->relationship( 'brand', 'name' )
														                  ->searchable()
														                  ->preload()
														                  ->hiddenOn( ProductsRelationManager::class ),
														            Select::make( 'categories' )
														                  ->relationship( 'categories', 'name' )
														                  ->multiple()
														                  ->preload()
														                  ->required(),
												            ] ),
										     ] )
										     ->columnSpan( [ 'lg' => 1 ] ),
								] )
								->columns( 3 );
				}
				
				public static function table( Table $table ) : Table
				{
						return $table
								->columns( [
										SpatieMediaLibraryImageColumn::make( 'product-image' )
										                             ->label( 'Image' )
										                             ->collection( 'product-images' ),
										TextColumn::make( 'name' )
										          ->label( 'Name' )
										          ->searchable()
										          ->sortable(),
										TextColumn::make( 'brand.name' )
										          ->searchable()
										          ->sortable()
										          ->toggleable(),
										IconColumn::make( 'is_visible' )
										          ->label( 'Visibility' )
										          ->boolean()
										          ->sortable()
										          ->toggleable(),
										TextColumn::make( 'price' )
										          ->label( 'Price' )
										          ->searchable()
										          ->sortable(),
										TextColumn::make( 'sku' )
										          ->label( 'SKU' )
										          ->searchable()
										          ->sortable()
										          ->toggleable(),
										TextColumn::make( 'qty' )
										          ->label( 'Quantity' )
										          ->searchable()
										          ->sortable()
										          ->toggleable(),
										TextColumn::make( 'security_stock' )
										          ->searchable()
										          ->sortable()
										          ->toggleable()
										          ->toggledHiddenByDefault(),
										TextColumn::make( 'published_at' )
										          ->label( 'Publish Date' )
										          ->date()
										          ->sortable()
										          ->toggleable()
										          ->toggledHiddenByDefault(),
								] )
								->filters( [
										SelectFilter::make( 'brand' )
										            ->relationship( 'brand', 'name' )
										            ->preload()
										            ->multiple()
										            ->searchable(),
										TernaryFilter::make( 'is_visible' )
										             ->label( 'Visibility' )
										             ->boolean()
										             ->trueLabel( 'Only visible' )
										             ->falseLabel( 'Only hidden' )
										             ->native( false ),
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
								CommentsRelationManager::class,
						];
				}
				
				public static function getWidgets() : array
				{
						return [
								ProductStats::class,
						];
				}
				
				public static function getPages() : array
				{
						return [
								'index'  => Pages\ListProducts::route( '/' ),
								'create' => Pages\CreateProduct::route( '/create' ),
								'edit'   => Pages\EditProduct::route( '/{record}/edit' ),
						];
				}
				
				public static function getGloballySearchableAttributes() : array
				{
						return [ 'name', 'sku', 'brand.name' ];
				}
				
				public static function getGlobalSearchResultDetails( Model $record ) : array
				{
						/** @var Product $record */
						return [
								'Brand' => optional( $record->brand )->name,
						];
				}
				
				public static function getGlobalSearchEloquentQuery() : Builder
				{
						return parent::getGlobalSearchEloquentQuery()->with( [ 'brand' ] );
				}
				
				public static function getNavigationBadge() : ?string
				{
						return static::$model::whereColumn( 'qty', '<', 'security_stock' )->count();
				}
				
		}