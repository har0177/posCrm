<?php

namespace App\Traits;

/**
 *
 */
trait HasPermissions
{
  
  protected array $permissions = [];
  
  /**
   * @return array|mixed
   */
  public function abilities() : mixed
  {
    if( empty( $this->permissions ) ) {
      $this->permissions = $this->role?->permissions;
    }
    
    return $this->permissions;
  }// abilities
  
  /**
   * @param $do
   * @return bool
   */
  public function hasAbilityTo( $do ) : bool
  {
    $permissions = $this->abilities();
    return in_array( $do, $permissions, true );
  }// hasAbilityTo
  
}// HasPermissions
