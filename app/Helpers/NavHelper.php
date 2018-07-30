<?php

namespace App\Helpers;

use App\Http\Requests\Request;
use HTML;

/**
 * Class NavHelper
 * @package App\Helpers
 */
class NavHelper {

    /**
     * @var \Illuminate\Http\Request
     */
    public $request;

    /**
     * NavHelper constructor.
     *
     * @param $request
     */
    public function __construct( $request ) {
        $this->request = $request;
    }

    public function setActive( $path, $classes = '', $active = 'active' ) {
        return $this->request->is( $path ) ? $classes . ' ' . $active : $classes;
    }

}