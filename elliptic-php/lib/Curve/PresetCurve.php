<?php

namespace Elliptic\Curve;

include_once($_SERVER['DOCUMENT_ROOT']. '/elliptic-php/lib/Curve/ShortCurve.php');
include_once($_SERVER['DOCUMENT_ROOT']. '/elliptic-php/lib/Curve/EdwardsCurve.php');
include_once($_SERVER['DOCUMENT_ROOT']. '/elliptic-php/lib/Curve/MontCurve.php');

class PresetCurve
{
    public $curve;
    public $g;
    public $n;
    public $hash;

    function __construct($options)
    {
        if ( $options["type"] === "short" )
            $this->curve = new ShortCurve($options);
        elseif ( $options["type"] === "edwards" )
            $this->curve = new EdwardsCurve($options);
        else
            $this->curve = new MontCurve($options);

        $this->g = $this->curve->g;
        $this->n = $this->curve->n;
        $this->hash = isset($options["hash"]) ? $options["hash"] : null;
    }
}

?>
