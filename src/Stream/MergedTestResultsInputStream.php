<?php
namespace Athena\Stream;

class MergedTestResultsInputStream implements InputStreamInterface
{
    /**
     * @var \Athena\Stream\InputStreamInterface
     */
    private $inputStream;

    /**
     * @var float
     */
    private $totalExecTime;

    /**
     * MergedJsonInputStream constructor.
     *
     * @param \Athena\Stream\InputStreamInterface $inputStream
     * @param float $totalExecTime
     */
    public function __construct(InputStreamInterface $inputStream, $totalExecTime = null)
    {
        $this->inputStream   = $inputStream;
        $this->totalExecTime = $totalExecTime;
    }

    /**
     * @inheritDoc
     */
    public function valid()
    {
        return $this->inputStream->valid();
    }

    /**
     * @return string
     */
    public function read()
    {
        $mergedBuffer = [];

        while ($this->inputStream->valid()) {
            $buffer = $this->inputStream->read();

            // we only want to parse suites
            if (empty($buffer) || $buffer['type'] != 'suite') {
                continue;
            }

            if (!isset($mergedBuffer[$buffer['title']])) {
                $mergedBuffer[$buffer['title']]['successful'] = true;
                $mergedBuffer[$buffer['title']]['features_failed'] = 0;
                $mergedBuffer[$buffer['title']]['features_passed'] = 0;
                $mergedBuffer[$buffer['title']]['failures'] = 0;
                $mergedBuffer[$buffer['title']]['skipped'] = 0;
                $mergedBuffer[$buffer['title']]['errors'] = 0;
                $mergedBuffer[$buffer['title']]['passed'] = 0;
                $mergedBuffer[$buffer['title']]['total'] = 0;
                $mergedBuffer[$buffer['title']]['failures_percentage'] = 0;
                $mergedBuffer[$buffer['title']]['passed_percentage'] = 0;
                $mergedBuffer[$buffer['title']]['total_time'] = 0;
                $mergedBuffer[$buffer['title']]['children'] = [];
            }

            $mergedBuffer[$buffer['title']]['type'] = $this->getValueOrDefault($buffer, 'type', null);
            $mergedBuffer[$buffer['title']]['title'] = $this->getValueOrDefault($buffer, 'title', null);
            $mergedBuffer[$buffer['title']]['features_failed'] += $this->getValueOrDefault($buffer, 'features_failed', 0);
            $mergedBuffer[$buffer['title']]['features_passed'] += $this->getValueOrDefault($buffer, 'features_passed', 0);
            $mergedBuffer[$buffer['title']]['successful'] = $mergedBuffer[$buffer['title']]['successful'] && $this->getValueOrDefault($buffer, 'successful', false);
            $mergedBuffer[$buffer['title']]['failures'] += $this->getValueOrDefault($buffer, 'failures', 0);
            $mergedBuffer[$buffer['title']]['skipped'] += $this->getValueOrDefault($buffer, 'skipped', 0);
            $mergedBuffer[$buffer['title']]['errors'] += $this->getValueOrDefault($buffer, 'errors', 0);
            $mergedBuffer[$buffer['title']]['passed'] += $this->getValueOrDefault($buffer, 'passed', 0);
            $mergedBuffer[$buffer['title']]['total'] += $this->getValueOrDefault($buffer, 'total', 0);
            $mergedBuffer[$buffer['title']]['total_time'] = microtime(true) - $this->totalExecTime;
            $mergedBuffer[$buffer['title']]['directory'] = $this->getValueOrDefault($buffer, 'directory', null);
            $mergedBuffer[$buffer['title']]['children'] = array_merge_recursive((array)$mergedBuffer[$buffer['title']]['children'], (array)@$buffer['children']);

            if ($mergedBuffer[$buffer['title']]['total'] > 0) {
                $mergedBuffer[$buffer['title']]['passed_percentage']   = ceil(($mergedBuffer[$buffer['title']]['passed'] / $mergedBuffer[$buffer['title']]['total']) * 100);;
                $mergedBuffer[$buffer['title']]['failures_percentage'] = 100 - $mergedBuffer[$buffer['title']]['passed_percentage'];
            } else {
                $mergedBuffer[$buffer['title']]['passed_percentage']   = 0;
                $mergedBuffer[$buffer['title']]['failures_percentage'] = 100;
            }
        }

        return $mergedBuffer;
    }

    /**
     * @return boolean
     */
    public function close()
    {
        $this->inputStream->close();
    }

    /**
     * @param array $haystack
     * @param       $key
     * @param       $default
     *
     * @return mixed
     */
    private function getValueOrDefault(array $haystack, $key, $default)
    {
        if (array_key_exists($key, $haystack)) {
            return $haystack[$key];
        }
        return $default;
    }
}

