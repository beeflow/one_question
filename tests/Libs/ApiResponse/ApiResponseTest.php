<?php


namespace App\tests\Libs\ApiResponse;


use App\Libs\ApiResponse\ApiResponse;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiResponseTest extends TestCase
{
    /**
     * @var ApiResponse
     */
    protected $apiResponse;

    public function setUp(): void
    {
        $this->apiResponse = new ApiResponse();
    }


    public function testIfSetterReturnsCorrectObject(): void
    {
        $response = $this->apiResponse->setLinks([]);

        $this->assertTrue($response instanceof ApiResponse);
    }


    public function testIfReturnsCorrectResponse(): void
    {
        $response = $this->apiResponse->setData([
            'test' => [
                'test-1' => 'abc'
            ]
        ])
            ->setStatus(200)
            ->getResponse();

        $this->assertTrue($response instanceof JsonResponse);

        $this->assertEquals($response->getContent(), '{"jsonApi":{"version":"1.0"},"data":{"test":{"test-1":"abc"}}}');
        $this->assertEquals($response->getStatusCode(), 200);
    }
}