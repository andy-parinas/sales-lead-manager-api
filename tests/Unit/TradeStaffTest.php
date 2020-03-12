<?php

namespace Tests\Unit;

use App\Branch;
use App\TradeStaff;
use App\TradeType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class TradeStaffTest extends TestCase
{

    use RefreshDatabase;

    public function testTradeStaffBelongsToTradeType()
    {
        $tradeType = factory(TradeType::class)->create();
        $tradeStaff = factory(TradeStaff::class)->create(['trade_type_id' => $tradeType->id]);

        $this->assertInstanceOf(TradeType::class, $tradeStaff->tradeType);

    }

    public function testTradeStaffHasFullNameAttribute()
    {
        $firstName = 'Sheldon';
        $lastName = 'Cooper';
        $fullName = $firstName . ' ' . $lastName;

        $tradeStaff = factory(TradeStaff::class)->create([
            'first_name' => $firstName,
            'last_name' => $lastName
        ]);

        $this->assertEquals($fullName, $tradeStaff->full_name);

    }

    public function testTradeStaffBelongsToBranch()
    {
        $branch = factory(Branch::class)->create();
        $tradeStaff = factory(TradeStaff::class)->create(['branch_id' => $branch->id]);


        $this->assertInstanceOf(Branch::class, $tradeStaff->branch);


    }
}
