<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class UsersChart extends ChartWidget
{
  protected static ?string $heading = 'Total Users';
		
		protected static ?int $sort = 1;
  protected function getData() : array
  {
    $users = User::select( 'created_at' )->get()->groupBy( function( $user ) {
      return Carbon::parse( $user->created_at )->format( 'F' );
    } );
    $quantities = [];
    foreach( $users as $value ) {
      array_push( $quantities, $value->count() );
    }
    
    return [
      'datasets' => [
        [
          'label' => 'User Joined',
          'data'  => $quantities,
        ],
      ],
      'labels'   => $users->keys(),
    ];
  }
  
  protected function getType() : string
  {
    return 'bar';
  }
		
		
		public static function canView(): bool
		{
				return auth()->user()->can('view user');
		}
		
}
