<?php

namespace Betta\Foundation\Eloquent;

use Baum\Node;
use ReflectionObject;
use Illuminate\Contracts\Support\Arrayable;

abstract class AbstractBaumModel extends Node
{

    use AbstractModelTrait;

    /**
     * Column name to store the reference to parent's node.
     *
     * @var string
     */
    protected $parentColumn = 'parent_id';

    /**
     * Column name for left index.
     *
     * @var string
     */
    protected $leftColumn = 'tree_left';

    /**
     * Column name for right index.
     *
     * @var string
     */
    protected $rightColumn = 'tree_right';

    /**
     * Column name for depth field.
     *
     * @var string
     */
    protected $depthColumn = 'depth';

    /**
     * Column to perform the default sorting
     *
     * @var string
     */
    protected $orderColumn = null;


    /**
      * Guard NestedSet fields from mass-assignment.
      *
      * @var array
      */
    protected $guarded = array('id', 'parent_id', 'tree_left', 'tree_right', 'depth');
}
