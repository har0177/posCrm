<?php
		
		namespace App\Enums;
		
		use Filament\Support\Contracts\HasColor;
		use Filament\Support\Contracts\HasLabel;
		enum ProductType: string implements HasLabel, HasColor
		{
				
				case Deliverable = 'deliverable';
				
				case Downloadable = 'downloadable';
				
				public function getLabel() : string
				{
						return match ( $this ) {
								self::Deliverable => 'Deliverable',
								self::Downloadable => 'Downloadable'
						};
				}
				
				public function getColor() : string|array|null
				{
						return match ( $this ) {
								self::Deliverable => 'gray',
								self::Downloadable => 'warning'
						};
				}
				
		}