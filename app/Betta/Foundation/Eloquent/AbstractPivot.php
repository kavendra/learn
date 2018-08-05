<?php
namespace Betta\Foundation\Eloquent;

use Illuminate\Database\Eloquent\Relations\Pivot;

class AbstractPivot extends Pivot
{
    /**
     * Class uses traits
     */
    use ObserverableBootTrait;
}
