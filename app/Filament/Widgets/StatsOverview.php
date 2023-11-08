<?php
		
		namespace App\Filament\Widgets;
		
		use App\Enums\OrderStatus;
		use App\Models\Customer;
		use App\Models\Order;
		use Filament\Widgets\StatsOverviewWidget as BaseWidget;
		use Filament\Widgets\StatsOverviewWidget\Stat;
		class StatsOverview extends BaseWidget
		{
				
				protected static ?int $sort = 0;
				
				protected function getStats() : array
				{
						// Retrieve revenue data
						$revenueData = Order::where( 'status', OrderStatus::Delivered )->sum( 'total_price' );
						// Retrieve new customers data
						$newCustomersData = Customer::where( 'created_at', '>=', now()->subMonth() )->count();
						// Retrieve new orders data
						$newOrdersData = Order::where( 'created_at', '>=', now()->subMonth() )->count();
						// Calculate the increase in revenue
						$previousMonthRevenue = $this->getPreviousMonthRevenue();
						$revenueIncrease = $revenueData - $previousMonthRevenue;
						// Calculate the increase or decrease in new customers and new orders
						$previousMonthNewCustomers = $this->getPreviousMonthNewCustomers();
						$previousMonthNewOrders = $this->getPreviousMonthNewOrders();
						$newCustomersIncrease = $newCustomersData - $previousMonthNewCustomers;
						$newOrdersIncrease = $newOrdersData - $previousMonthNewOrders;
						$revenueGraph = $this->generateDescription( $revenueIncrease, $previousMonthRevenue );
						$customerGraph = $this->generateDescription( $newCustomersIncrease, $previousMonthNewCustomers );
						$orderGraph = $this->generateDescription( $newOrdersIncrease, $previousMonthNewOrders );
						
						return [
								Stat::make( 'Revenue', '$' . number_format( $revenueData, 2 ) )
								    ->description( $revenueGraph )
								    ->descriptionIcon( 'heroicon-m-arrow-trending-up' )
								    ->chart( $this->getChartDataForRevenue() )
								    ->color( $revenueGraph > 0 ? 'success' : 'danger' ),
								Stat::make( 'New customers', $newCustomersData )
								    ->description( $customerGraph )
								    ->descriptionIcon( 'heroicon-m-arrow-trending-up' )
								    ->chart( $this->getChartDataForNewCustomers() )
								    ->color( $customerGraph > 0 ? 'success' : 'danger' ),
								Stat::make( 'New orders', $newOrdersData )
								    ->description( $orderGraph )
								    ->descriptionIcon( 'heroicon-m-arrow-trending-up' )
								    ->chart( $this->getChartDataForNewOrders() )
								    ->color( $orderGraph > 0 ? 'success' : 'danger' ),
						];
				}
				
				// Function to calculate the increase in revenue
				protected function generateDescription( $increase, $previousValue )
				{
						if( $previousValue === 0 ) {
								return 0; // Handle the case where the previous value is zero to avoid division by zero
						}
						$percentageChange = ( ( $increase / $previousValue ) * 100 );
						if( $increase > 0 ) {
								return number_format( $percentageChange, 2 ) . '% increase';
						}
						if( $increase < 0 ) {
								return number_format( abs( $percentageChange ), 2 ) . '% decrease';
						}
						
						return 'No change';
				}
				
				
				// Function to fetch the revenue of the previous month
				protected function getPreviousMonthRevenue()
				{
						$startOfPreviousMonth = now()->subMonth( 1 )->startOfMonth();
						$endOfPreviousMonth = now()->subMonth( 1 )->endOfMonth();
						
						return Order::where( 'status', OrderStatus::Delivered )
						            ->whereBetween( 'created_at', [ $startOfPreviousMonth, $endOfPreviousMonth ] )
						            ->sum( 'total_price' );
						
				}
				
				// Function to fetch the number of new customers in the previous month
				protected function getPreviousMonthNewCustomers()
				{
						$startOfPreviousMonth = now()->subMonth( 1 )->startOfMonth();
						$endOfPreviousMonth = now()->subMonth( 1 )->endOfMonth();
						
						return Customer::whereBetween( 'created_at', [ $startOfPreviousMonth, $endOfPreviousMonth ] )
						               ->count();
				}
				
				// Function to fetch the number of new orders in the previous month
				protected function getPreviousMonthNewOrders()
				{
						$startOfPreviousMonth = now()->subMonth( 1 )->startOfMonth();
						$endOfPreviousMonth = now()->subMonth( 1 )->endOfMonth();
						
						return Order::whereBetween( 'created_at', [ $startOfPreviousMonth, $endOfPreviousMonth ] )
						            ->count();
						
				}
				
				// Function to fetch revenue data for the chart
				protected function getChartDataForRevenue()
				{
						$startOfMonth = now()->startOfMonth();
						$endOfMonth = now()->endOfMonth();
						$revenueData = Order::selectRaw( 'DATE_FORMAT(created_at, "%Y-%m") as month' )
						                    ->selectRaw( 'SUM(total_price) as revenue' )
						                    ->whereBetween( 'created_at', [ $startOfMonth, $endOfMonth ] )
						                    ->where( 'status', OrderStatus::Delivered )
						                    ->groupBy( \DB::raw( 'DATE_FORMAT(created_at, "%Y-%m")' ) )
						                    ->orderBy( \DB::raw( 'DATE_FORMAT(created_at, "%Y-%m")' ) )
						                    ->get()
						                    ->pluck( 'revenue' )
						                    ->all();
						
						return $revenueData;
				}
				
				// Function to fetch new customers data for the chart
				protected function getChartDataForNewCustomers()
				{
						$startOfMonth = now()->startOfMonth();
						$endOfMonth = now()->endOfMonth();
						$newCustomersData = Customer::whereBetween( 'created_at', [ $startOfMonth, $endOfMonth ] )
						                            ->count();
						
						return [ $newCustomersData ];
				}
				
				// Function to fetch new orders data for the chart
				protected function getChartDataForNewOrders()
				{
						$startOfMonth = now()->startOfMonth();
						$endOfMonth = now()->endOfMonth();
						$newOrdersData = Order::whereBetween( 'created_at', [ $startOfMonth, $endOfMonth ] )
						                      ->count();
						
						return [ $newOrdersData ];
				}
				
				
		}