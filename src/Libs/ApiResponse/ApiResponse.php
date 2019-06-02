<?php

/**
 * @copyright 2019 Beeflow Ltd
 * @author    Rafal Przetakowski <rafal.p@beeflow.co.uk>
 */

namespace App\Libs\ApiResponse;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @see     https://jsonapi.org/format/ (v1.0)
 * @package App\Libs\ApiResponse
 */
class ApiResponse
{
    /**
     * @see https://jsonapi.org/format/#document-jsonapi-object
     *
     * @var string
     */
    private $apiVersion = '1.0';

    /**
     * The HTTP status code ([RFC7231], Section 6) generated by the origin server for this occurrence of the response.
     *
     * @var int
     */
    private $status = 200;

    /**
     *
     * @var array
     */
    private $data = [];

    /**
     * @see https://jsonapi.org/format/#document-meta
     *
     * @var array
     */
    private $meta = [];

    /**
     * @see https://jsonapi.org/format/#document-links
     *
     * @var array
     */
    private $links = [];

    /**
     * @var array
     */
    private $included = [];

    /**
     * @param string $version
     *
     * @return ApiResponse
     */
    public function setVersion(string $version): ApiResponse
    {
        $this->apiVersion = $version;

        return $this;
    }

    /**
     * @param array $links
     *
     * @return ApiResponse
     */
    public function setLinks(array $links): ApiResponse
    {
        $this->links = $links;

        return $this;
    }

    /**
     * @param array $link ['key' => 'url']
     *
     * @return ApiResponse
     */
    public function addLink(array $link): ApiResponse
    {
        $this->links[] = $link;

        return $this;
    }

    /**
     * @param array $includedList
     *
     * @return ApiResponse
     */
    public function setIncluded(array $includedList): ApiResponse
    {
        $this->included = $includedList;

        return $this;
    }

    /**
     * @param string $included
     *
     * @return ApiResponse
     */
    public function addIncluded(string $included): ApiResponse
    {
        $this->included[] = $included;

        return $this;
    }

    /**
     * @param array $metaData
     *
     * @return ApiResponse
     */
    public function setMeta(array $metaData): ApiResponse
    {
        $this->meta = $metaData;

        return $this;
    }

    /**
     * @param string $key
     * @param        $value
     *
     * @return ApiResponse
     */
    public function addMeta(string $key, $value): ApiResponse
    {
        $this->meta[ $key ] = $value;

        return $this;
    }

    /**
     * @param int $status
     *
     * @return ApiResponse
     */
    public function setStatus(int $status): ApiResponse
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @param array $data
     *
     * @return ApiResponse
     */
    public function setData(array $data): ApiResponse
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return JsonResponse
     */
    public function getResponse(): JsonResponse
    {
        $response = [
            'jsonApi' => [
                'version' => $this->apiVersion
            ],
            'data'    => $this->data
        ];

        if (!empty($this->links)) {
            $response['links'] = $this->links;
        }

        if (!empty($this->included)) {
            $response['included'] = $this->included;
        }

        if (!empty($this->meta)) {
            $response['meta'] = $this->meta;
        }

        return new JsonResponse($response, $this->status, ['Content-Type' => 'application/vnd.api+json']);
    }
}