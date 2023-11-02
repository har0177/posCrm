<?php
		
		namespace App\Filament\Widgets;
		
		use App\Models\Order;
		use Filament\Widgets\ChartWidget;
		use Illuminate\Support\Facades\DB;
		class OrdersChart extends ChartWidget
		{
				
				protected static ?string $heading = 'Orders per month';
				
				protected static ?int $sort = 3;
				
				protected function getType() : string
				{
						return 'line';
				}
				
				protected function getData() : array
				{
						// Query your database to get the order data dynamically
						$orders = Order::select( DB::raw( 'COUNT(*) as order_count' ), DB::raw( 'MONTH(created_at) as month' ) )
						               ->groupBy( 'month' )
						               ->get();
						// Initialize arrays for labels and data
						$labels = [];
						$data = [];
						// Loop through the results and populate the labels and data arrays
						foreach( $orders as $order ) {
								$labels[] = date( "M", mktime( 0, 0, 0, $order->month, 1 ) );
								$data[] = $order->order_count;
						}
						
						return [
								'datasets' => [
										[
												'label' => 'Orders',
												'data'  => $data,
												'fill'  => 'start',
										],
								],
								'labels'   => $labels,
						];
				}
				
				
		}