<?php

namespace Stancl\HasManyWithInverse\Tests;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as TestbenchTestCase;

abstract class TestCase extends TestbenchTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Schema::create('parents', function (Blueprint $table) {
            $table->bigIncrements('id');
        });

        Schema::create('children', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('parent_id');
        });
    }
}
