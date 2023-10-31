<?php
		
		namespace App\Filament\Resources\OrderResource\Pages;
		
		use App\Enums\OrderStatus;
		use App\Filament\Resources\OrderResource;
		use App\Models\Order;
		use Filament\Actions\EditAction;
		use Filament\Pages\Actions;
		use Filament\Pages\Concerns\ExposesTableToWidgets;
		use Filament\Resources\Pages\ListRecords;
		class ListOrders extends ListRecords
		{
				
				use ExposesTableToWidgets;
				protected static string $resource = OrderResource::class;
				public function getTabs() : array
				{
						$tabs = [
								null => ListRecords\Tab::make( 'All' ),
						];
						foreach( OrderStatus::cases() as $status ) {
								$tabs[ $status->value ] = ListRecords\Tab::make()
								                                         ->label( OrderStatus::from( $status->value )->getLabel() )
								                                         ->query( fn( $query ) => $query->where( 'status', $status->value ) );
						}
						
						return $tabs;
				}
				protected function getActions() : array
				{
						return [
								Actions\CreateAction::make()
						];
				}
				protected function getHeaderWidgets() : array
				{
						return OrderResource::getWidgets();
				}
				
		}