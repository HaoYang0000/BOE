<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('project_id');
            $table->string('name');
            $table->enum('month',['1月',
                                  '2月',
                                  '3月',
                                  '4月',
                                  '5月',
                                  '6月',
                                  '7月',
                                  '8月',
                                  '9月',
                                  '10月',
                                  '11月',
                                  '12月',]);
            $table->string('goal')->nullable();
            $table->string('achieve')->nullable();
            $table->string('rate')->nullable();
            $table->timestamps();
        });

        Schema::create('detail_project', function (Blueprint $table) {
            $table->integer('detail_id');
            $table->integer('project_id');
            $table->primary(['detail_id','project_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detail_project');
        Schema::dropIfExists('details');
    }
}
