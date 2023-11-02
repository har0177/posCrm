<?php
		
		namespace App\Filament\Widgets;
		
		use App\Models\Customer;
		use App\Models\User;
		use Carbon\Carbon;
		use Filament\Widgets\ChartWidget;
		class CustomersChart extends ChartWidget
		{
				
				protected static ?string $heading = 'Total customers';
				
				protected static ?int $sort = 2;
				
				protected function getType() : string
				{
						return 'line';
				}
				
				protected function getData() : array
				{
						$customers = Customer::select( 'created_at' )->get()->groupBy( function( $customer ) {
								return Carbon::parse( $customer->created_at )->format( 'F' );
						} );
						$quantities = [];
						foreach( $customers as $value ) {
								array_push( $quantities, $value->count() );
						}
						
						return [
								'datasets' => [
										[
												'label' => 'Customer Joined',
												'data'  => $quantities,
										],
								],
								'labels'   => $customers->keys(),
						];
				}
				
		}