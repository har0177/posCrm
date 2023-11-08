<?php
		
		namespace App\Filament\Widgets;
		
		use App\Filament\Resources\OrderResource;
		use App\Models\Order;
		use Filament\Tables\Actions\Action;
		use Filament\Tables\Columns\BadgeColumn;
		use Filament\Tables\Columns\TextColumn;
		use Filament\Tables\Table;
		use Filament\Widgets\TableWidget as BaseWidget;
		use Squire\Models\Currency;
		class LatestOrders extends BaseWidget
		{
				
				protected int|string|array $columnSpan = 'full';
				
				protected static ?int $sort = 4;
				
				public function table( Table $table ) : Table
				{
						return $table
								->query( OrderResource::getEloquentQuery() )
								->defaultPaginationPageOption( 5 )
								->defaultSort( 'created_at', 'desc' )
								->columns( [
										TextColumn::make( 'created_at' )
										          ->label( 'Order Date' )
										          ->date()
										          ->sortable(),
										TextColumn::make( 'number' )
										          ->searchable()
										          ->sortable(),
										TextColumn::make( 'customer.name' )
										          ->searchable()
										          ->sortable(),
										BadgeColumn::make( 'status' )
										           ->badge(),
										TextColumn::make( 'currency' )
										          ->getStateUsing( fn( $record
										          ) : ?string => Currency::find( $record->currency )?->name ?? null )
										          ->searchable()
										          ->sortable(),
										TextColumn::make( 'total_price' )
										          ->searchable()
										          ->sortable(),
										TextColumn::make( 'shipping_price' )
										          ->label( 'Shipping cost' )
										          ->searchable()
										          ->sortable(),
								] )
								->actions( [
										Action::make( 'open' )
										      ->url( fn( Order $record ) : string => OrderResource::getUrl( 'edit',
												      [ 'record' => $record ] ) ),
								] );
				}
				
				public static function canView(): bool
				{
						return auth()->user()->can('view order');
				}
				
		}