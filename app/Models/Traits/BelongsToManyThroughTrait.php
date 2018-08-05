<?php namespace Betta\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Betta\Models\Eloquent\BelongsToManyThrough;
use Betta\Models\SpeakerClassificationGroup;

trait BelongsToManyThroughTrait
{

  /**
   * Define a many-to-many relationship.
   *
   * @param  string  $related
   * @param  string  $secondaryKey
   * @param  string  $table
   * @param  string  $foreignKey
   * @param  string  $otherKey
   * @param  string  $relation
   * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
   */
  public function belongsToManyThrough($related, $secondaryKey, $table = null, $foreignKey = null, $otherKey = null, $relation = null)
  {
    // If no relationship name was passed, we will pull backtraces to get the
    // name of the calling function. We will use that function name as the
    // title of this relation since that is a great convention to apply.
    if (is_null($relation))
    {
      $relation = $this->getBelongsToManyThroughCaller();
    }

    // First, we'll need to determine the foreign key and "other key" for the
    // relationship. Once we have determined the keys we'll make the query
    // instances as well as the relationship instances we need for this.
    $foreignKey = $foreignKey ?: $this->getForeignKey();

    $instance = new $related;

    $otherKey = $otherKey ?: $instance->getForeignKey();

    // If no table name was provided, we can guess it by concatenating the two
    // models using underscores in alphabetical order. The two model names
    // are transformed to snake case from their default CamelCase also.
    if (is_null($table))
    {
      $table = $this->joiningTable($related);
    }

    // Now we're ready to create a new query builder for the related model and
    // the relationship instances for the relation. The relations will set
    // appropriate query constraint and entirely manages the hydrations.
    $query = $instance->newQuery();

    return new BelongsToManyThrough($query, $this, $table, $secondaryKey, $foreignKey, $otherKey, $relation);
  }


  /**
   * Get the relationship name of the belongs to many.
   *
   * @return  string
   */
  protected function getBelongsToManyThroughCaller()
  {
    $self = __FUNCTION__;

    $caller = array_first(debug_backtrace(false), function($key, $trace) use ($self)
    {
      $caller = $trace['function'];

      return ( ! in_array($caller, Model::$manyMethods + ['belongsToManyThrough'] ) && $caller != $self);
    });

    return ! is_null($caller) ? $caller['function'] : null;
  }
  
}