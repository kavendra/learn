<?php

namespace Betta\Services\Generator\Interfaces;

interface ExcelFormatsInterface
{
    /**
     * USD format
     */
    const AS_CURRENCY     = '_($* #,##0.00_);_($* (#,##0.00);_($* "-"??_);_(@_)';

    /**
     * US Short Date format
     */
    const AS_DATE         = '[$-409]m/d/yy';

    /**
     * US - Long Date format
     */
    const AS_DATE_LONG    = '[$-409]m/d/yy h:mm AM/PM;@';

    /**
     * US-formatted time
     */
    const AS_TIME         = '[$-409]h:mm AM/PM;@';

    /**
     * USPS ZIP Code
     */
    const AS_ZIP_CODE     = '00000';

    /**
     * Nice integer with decimal group separation
     */
    const AS_NICE_INTEGER = '_(* #,##0_);_(* (#,##0);_(* "-"??_);_(@_)';

    /**
     * Format the Phone numbers
     */
    const AS_PHONE        = '[<=9999999]###-####;[>9999999999]"+"# (###) ###-####; (###) ###-####';

    /**
     * Format the Percentage
     */
    const AS_PERCENTAGE   = '0.00##\%;[Red](0.00##\%)';
}
