<?php
namespace Athena\Api\Response\Assertion;

use Athena\Api\Response\ResponseHolder;
use Athena\Exception\UnexpectedValueException;

class JsonStructureAssertion implements ResponseAssertionInterface
{
    private $structure;

    /**
     * JsonStructureAssertion constructor.
     * @param array $structure
     */
    public function __construct(array $structure)
    {
        $this->structure = $structure;
    }

    /**
     * @param ResponseHolder $response
     * @return mixed
     * @throws UnexpectedValueException
     */
    public function assert(ResponseHolder $response)
    {
        $result = $this->navigateTree($this->structure, $response, '$');

        if (is_array($result)) {
            throw new UnexpectedValueException(
                sprintf(
                    "JSON structure\n%s\ndoes not comply with expected\n%s\n because path %s with expected value [%s] was not found",
                    $response->getBody(),
                    json_encode($this->structure, JSON_PRETTY_PRINT),
                    $result[0],
                    $result[1]
                )
            );
        } elseif ($result !== true) {
            throw new UnexpectedValueException(
                sprintf(
                    "JSON structure\n%s\ndoes not comply with expected\n%s\n because path [%s] was not found",
                    $response->getBody(),
                    json_encode($this->structure, JSON_PRETTY_PRINT),
                    $result
                )
            );
        }

        return true;
    }

    private function navigateTree($structure, $response, $path) {
        foreach ($structure as $node => $value) {
            $node = $path . '.' . $node;

            if ($value == '*') {
                try {
                    (new JsonPathAssertion($node))->assert($response);
                } catch (UnexpectedValueException $e) {
                    return $node;
                }
            } elseif (is_array($value)) {
                $return = $this->navigateTree($value, $response, $node);
                if ($return !== true) {
                    return $return;
                }
            } else {
                try {
                    (new JsonPathValueAssertion($node, $value))->assert($response);
                } catch (UnexpectedValueException $e) {
                    return [$node, $value];
                }
            }
        }

        return true;
    }
}

