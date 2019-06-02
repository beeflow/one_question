<?php


namespace App\tests\Libs\ApiResponse;


use App\Libs\ApiResponse\ApiProblem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use TypeError;

class ApiProblemTest extends TestCase
{
    /**
     * @var ApiProblem
     */
    private $apiProblem;

    public function setUp(): void
    {
        $this->apiProblem = new ApiProblem();
    }

    public function testIfSetterReturnsObject()
    {
        $result = $this->apiProblem->setTitle('Some Title');

        $this->assertTrue($result instanceof ApiProblem);
    }

    public function testIfReturnsCorrectResponse()
    {
        $expectedResponseData = '{"jsonApi":{"version":"1.0"},"title":"Problem title","detail":"Problem details description"}';
        $this->apiProblem
            ->setTitle('Problem title')
            ->setDetail('Problem details description');

        $response = $this->apiProblem->getResponse();
        $this->assertTrue($response instanceof JsonResponse);
        $this->assertEquals($expectedResponseData, $response->getContent());
    }

	public function testIfReturnsCorrectResponseWithStatus()
	{
		$expectedResponseData = '{"jsonApi":{"version":"1.0"},"title":"Problem title","detail":"Problem details description"}';
		$this->apiProblem
			->setTitle('Problem title')
			->setStatus(JsonResponse::HTTP_NOT_FOUND)
			->setDetail('Problem details description');

		$response = $this->apiProblem->getResponse();
		$this->assertTrue($response instanceof JsonResponse);
		$this->assertEquals($expectedResponseData, $response->getContent());
	}

    /**
     * @expectedException TypeError
     */
    public function testIfThrowsTypeErrorWhenTriesToGetResponseWithoutTitle()
    {
        $this->apiProblem->getResponse();
    }
}