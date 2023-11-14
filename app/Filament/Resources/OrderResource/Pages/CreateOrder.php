<?php
		
		namespace App\Filament\Resources\OrderResource\Pages;
		
		use App\Filament\Resources\OrderResource;
		use Filament\Forms\Components\Section;
		use Filament\Forms\Components\Wizard\Step;
		use Filament\Notifications\Actions\Action;
		use Filament\Notifications\Notification;
		use Filament\Resources\Pages\CreateRecord;
		use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
		class CreateOrder extends CreateRecord
		{
				
				use HasWizard;
				protected static string $resource = OrderResource::class;
				
				
				protected function getRedirectUrl() : string
				{
						return $this->getResource()::getUrl( 'index' );
				}
				
				
				protected function afterCreate() : void
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
						            ->title( 'New order' )
						            ->icon( 'heroicon-o-shopping-bag' )
						            ->body( "**{$order->customer->name} ordered {$order->items->count()} products.**" )
						            ->actions( [
								            Action::make( 'View' )
								                  ->url( OrderResource::getUrl( 'view', [ 'record' => $order ] ) ),
						            ] )
						            ->sendToDatabase( auth()->user() );
				}
				
				protected function getSteps() : array
				{
						return [
								Step::make( 'Order Details' )
								    ->schema( [
										    Section::make()->schema( OrderResource::getFormSchema() )->columns(),
								    ] ),
								Step::make( 'Order Items' )
								    ->schema( [
										    Section::make()->schema( OrderResource::getFormSchema( 'items' ) ),
								    ] ),
						];
				}
				
		}