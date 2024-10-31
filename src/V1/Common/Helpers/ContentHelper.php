<?php

namespace Src\V1\Common\Helpers;

class ContentHelper
{
    /**
     * @param \Tripteki\Helpers\Contracts\IResponse $data
     * @return mixed
     */
    public function __invoke($data)
    {
        return $data->getOriginalContent()["data"]->response()->getOriginalContent();
    }
};
