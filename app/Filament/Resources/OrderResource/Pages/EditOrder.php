<?php
		
		namespace App\Filament\Resources\OrderResource\Pages;
		
		use App\Filament\Resources\OrderResource;
		use App\Models\Order;
		use Filament\Actions\EditAction;
		use Filament\Notifications\Actions\Action;
		use Filament\Notifications\Notification;
		use Filament\Pages\Actions;
		use Filament\Resources\Pages\EditRecord;
		class EditOrder extends EditRecord
		{
				
				protected static string $resource = OrderResource::class;
				
				
				protected function getRedirectUrl() : string
				{
						return $this->getResource()::getUrl( 'index' );
				}
				
				protected function afterSave() : void
				{
						$order = $this->record;
						// Use the `map` function to calculate the total price based on related items
						$totalPrice = $order->items->map( function( $item ) {
								return $item->qty * $item->unit_price;
						} )->sum();
						// Update the `total_price` field in the Order model
						$order->total_price = $totalPrice;
						$order->save();
						Notification::make()
						            ->title( 'Update order' )
						            ->icon( 'heroicon-o-shopping-bag' )
						            ->body( "**{$order->customer->name} updated ordered {$order->items->count()} products.**" )
						            ->actions( [
								            Action::make( 'View' )
								                  ->url( OrderResource::getUrl( 'view', [ 'record' => $order ] ) ),
						            ] )
						            ->sendToDatabase( auth()->user() );
				}
				
				
				protected function getActions() : array
				{
						return [
								Actions\DeleteAction::make(),
								Actions\RestoreAction::make(),
								Actions\ForceDeleteAction::make(),
						];
				}
				
		}