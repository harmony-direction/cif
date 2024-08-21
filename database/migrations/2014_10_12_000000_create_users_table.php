<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            // Add new field avatar
            $table->string('avatar', 250)->nullable()->comment('avatar');
            $table->string('employee_no',50)->unique()->comment('รหัสพนักงาน');
            $table->string('username')->nullable()->unique()->comment('ชื่อผู้ใช้');
            $table->unsignedBigInteger('prefix_id')->nullable()->comment('คำนำหน้าชื่อ');
            $table->string('name',50)->comment('ชื่อ');
            $table->string('lastname',50)->nullable()->comment('นามสกุล');
            $table->unsignedBigInteger('company_department_id')->nullable()->comment('แผนกทำงาน');
            $table->unsignedBigInteger('user_position_id')->nullable()->comment('ตำแหน่งงาน');
            $table->unsignedBigInteger('employee_type_id')->nullable()->comment('ประเภทพนักงาน');
            $table->unsignedBigInteger('nationality_id')->nullable()->comment('สัญชาติ');
            $table->unsignedBigInteger('ethnicity_id')->nullable()->comment('เชื้อชาติ');
            $table->string('address',250)->nullable()->comment('ที่อยู่');
            // Add new field address
            $table->string('district',250)->nullable()->comment('แขวง/อำเภอ');
            $table->string('subdistrict',250)->nullable()->comment('เขต/ตำบล');
            $table->string('zip',250)->nullable()->comment('รหัสไปรษณีย์');
            $table->string('city',250)->nullable()->comment('จังหวัด');
            $table->string('country',250)->nullable()->comment('ประเทศ');
            // Add new field others
            $table->string('education',250)->nullable()->comment('การศึกษาสูงสุด');
            $table->string('edu_department',250)->nullable()->comment('สาขาวิชา');
            $table->string('relationship',250)->nullable()->comment('สถานภาพสมรส');

            $table->char('phone',50)->nullable()->comment('เบอร์โทรศัพท์');
            $table->string('hid',13)->nullable()->comment('เลขที่บัตรประจำตัวประชาชน');
            $table->string('passport',20)->nullable()->comment('พาสพอร์ต');
            $table->string('work_permit',20)->nullable()->comment('เอกสารอนุญาตทำงาน');
            $table->date('start_work_date')->nullable()->comment('วันที่เริ่มทำงาน');
            $table->date('birth_date')->nullable()->comment('วันเดือนปี เกิด');
            $table->date('visa_expiry_date')->nullable()->comment('วันหมดอายุวีซ่า');
            $table->date('permit_expiry_date')->nullable()->comment('วันหมดอายุใบอนุญาตทำงาน');
            $table->string('email',50)->unique()->comment('อีเมล');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('tax')->nullable();
            $table->string('social_security_number')->nullable();
            $table->string('bank')->nullable()->comment('บัญชีธนาคาร');
            $table->string('bank_account')->nullable()->comment('เลขที่บัญชีธนาคาร');
            $table->char('is_admin',1)->default(0);
            $table->boolean('is_foreigner')->default(false);
            $table->char('time_record_require',1)->default(1);
            $table->char('diligence_allowance_id',1)->nullable()->default(1);
            $table->unsignedBigInteger('work_schedule_id')->nullable();
            $table->char('status',1)->default(1);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
