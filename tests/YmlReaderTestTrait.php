<?php declare(strict_types=1);

namespace rkwadriga\filereader\tests;

trait YmlReaderTestTrait
{
    protected function getTestData() : array
    {
        return [
            'Component1' => [
                'Component1Param1' => 'Component1Value1',
                'Component1Param2' => true,
                'Component1Param3' => false,
                'Component11' => [
                    'Component11Param1' => 'Component11Value1',
                    'Component11Param2' => 'Component11Value2',
                    'Component11Param3' => null
                ],
            ],
            'Component2' => [
                'Component2Param1' => 'Component2Value1',
                'Component2Param2' => 'Component2Value2'
            ],
        ];
    }

    protected function getTestFileContent() : string
    {
        return "Component1:\n  Component1Param1: Component1Value1\n  Component1Param2: true\n  Component1Param3: false\n  Component11:\n    Component11Param1: Component11Value1\n    Component11Param2: Component11Value2\n    Component11Param3: null\nComponent2:\n  Component2Param1: Component2Value1\n  Component2Param2: Component2Value2";
    }
}