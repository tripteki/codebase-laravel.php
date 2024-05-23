<?php

namespace Src\V1\Post\Contracts\Repositories;

interface PostContract
{
    /**
     * @param array $querystring|[]
     * @return \Src\V1\Post\Http\Resources\PostResource
     */
    public function all($querystring = []);

    /**
     * @param int|string $identifier
     * @param array $querystring|[]
     * @return \Src\V1\Post\Http\Resources\PostResource
     */
    public function get($identifier, $querystring = []);

    /**
     * @param int|string $identifier
     * @param array $data
     * @return \Src\V1\Post\Http\Resources\PostResource
     */
    public function update($identifier, $data);

    /**
     * @param array $data
     * @return \Src\V1\Post\Http\Resources\PostResource
     */
    public function create($data);

    /**
     * @param int|string $identifier
     * @return \Src\V1\Post\Http\Resources\PostResource
     */
    public function delete($identifier);
};
