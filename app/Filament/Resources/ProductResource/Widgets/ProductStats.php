<?php
		
		namespace App\Filament\Resources\ProductResource\Widgets;
		
		use App\Enums\OrderStatus;
		use App\Filament\Resources\ProductResource\Pages\ListProducts;
		use App\Models\Order;
		use App\Models\OrderItem;
		use Filament\Widgets\Concerns\InteractsWithPageTable;
		use Filament\Widgets\StatsOverviewWidget as BaseWidget;
		use Filament\Widgets\StatsOverviewWidget\Stat;
		class ProductStats extends BaseWidget
		{
				
				use InteractsWithPageTable;
				protected static ?string $pollingInterval = null;
				
				protected function getTablePage() : string
				{
						return ListProducts::class;
				}
				
				protected function getStats() : array
				{
						return [
								Stat::make( 'Total Products', $this->getPageTableQuery()->count() ),
								Stat::make( 'Product Inventory',
										$this->getPageTableQuery()->sum( 'qty' ) - Order::with( 'items' )->where( 'status',
												OrderStatus::Delivered )->get()->sum( function( $order ) {
												return $order->items->sum( 'qty' );
										} ) ),
								Stat::make( 'Average price', number_format( $this->getPageTableQuery()->avg( 'price' ), 2 ) ),
						];
				}
				
		}