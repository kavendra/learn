<?php

namespace Betta\Foundation\Interfaces;

interface ComposerInterface
{
    /**
     * Set the values in the View
     *
     * @access public
     * @param  View $view
     * @return Void
     */
    public function compose($view);
}
