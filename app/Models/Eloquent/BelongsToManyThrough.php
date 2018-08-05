<?php namespace Betta\Models\Eloquent;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BelongsToManyThrough extends BelongsToMany
{

	/**
	 * Create a new has many relationship instance.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $query
	 * @param  \Illuminate\Database\Eloquent\Model  $parent
	 * @param  string  $table
	 * @param  string  $foreignKey
	 * @param  string  $otherKey
	 * @param  string  $relationName
	 * @return void
	 */
	public function __construct(Builder $query, Model $parent, $table, $secondaryKey, $foreignKey, $otherKey, $relationName = null)
	{
		$this->table        = $table;
		$this->otherKey     = $otherKey;
		$this->foreignKey   = $foreignKey;
		$this->relationName = $relationName;
		$this->secondaryKey = $secondaryKey;
		# reset key in model to the value of the secondary key
		$parent->setKeyName($secondaryKey);

		parent::__construct($query, $parent, $table, $foreignKey, $otherKey, $relationName);
	}


	/**
	 * Set the constraints for an eager load of the relation.
	 *
	 * @param  array  $models
	 * @return void
	 */
	public function addEagerConstraints(array $models)
	{
		$this->query->whereIn( $this->getForeignKey(), $this->getKeys($models, $this->secondaryKey) );
	}


	/**
	 * Match the eagerly loaded results to their parents.
	 *
	 * @param  array   $models
	 * @param  \Illuminate\Database\Eloquent\Collection  $results
	 * @param  string  $relation
	 * @return array
	 */
	public function match(array $models, Collection $results, $relation)
	{
		$dictionary = $this->buildDictionary($results);
		// Once we have an array dictionary of child objects we can easily match the
		// children back to their parent using the dictionary and the keys on the
		// the parent models. Then we will return the hydrated models back out.
		foreach ($models as $model)
		{
			# Overload key name on each model with the secondary key
			$model->setKeyName($this->secondaryKey);

			if (isset($dictionary[$key = $model->getKey()]))
			{
				$collection = $this->related->newCollection($dictionary[$key]);

				$model->setRelation($relation, $collection);
			}
		}

		return $models;
	}
}