<?php

namespace ACFBentveld\Parser;

use Illuminate\Support\Facades\Facade;

/**
 * @see \ACFBentveld\Parser\Parser
 */
class ParserFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Parser';
    }
}
