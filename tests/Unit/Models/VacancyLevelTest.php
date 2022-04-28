<?php

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\VacancyLevel;


class VacancyLevelTest extends TestCase
{
    /**
     * @param int $remainingCount
     * @param string $expectedMark
     * @dataProvider dataMark
     */
    public function testMark(int $remainingCount, string $expectedMark)
    {
        $level = new VacancyLevel($remainingCount);
        $this->assertSame($expectedMark, $level->mark());
    }

    public function dataMark() {
        return [
            '空きなし' => [
                'remainingCount' => 0,
                'expectedMark' => '×',
            ],
            '残りわずか' => [
                'remainingCount' => 4,
                'expectedMark' => '△',
            ],
            '空き十分' => [
                'remainingCount' => 5,
                'expectedMark' => '◎',
            ],
        ];
    }

    /**
     * @param int $remainingCount
     * @param string $expectedClassName
     * @dataProvider dataSlug
     */
    public function testSlug(int $remainingCount, string $expectedClassName)
    {
        $level = new VacancyLevel($remainingCount);
        $this->assertSame($expectedClassName, $level->slug());
    }

    public function dataSlug() {
        return [
            '空きなし' => [
                'remainingCount' => 0,
                'expectedClassName' => 'empty',
            ],
            '残りわずか' => [
                'remainingCount' => 4,
                'expectedClassName' => 'few',
            ],
            '空き十分' => [
                'remainingCount' => 5,
                'expectedClassName' => 'enough',
            ],
        ];
    }
}
