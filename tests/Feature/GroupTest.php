<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\GroupService;
use App\Models\User;

class GroupTest extends TestCase
{
    use RefreshDatabase;

    protected $groupService;

    protected function setUp(): void
    {
        parent::setUp();
        
        // إعداد خدمة المجموعة
        $this->groupService = app(GroupService::class);

        // إنشاء مستخدم لاختبار عملية إنشاء المجموعة
        $this->actingAs(User::factory()->create());

    }

     /** @test */
     public function it_can_create_group_with_valid_data()
     {
         $data = [
             'name' => 'Test Group',
         ];
 
         // استدعاء دالة createGroup واختبار الناتج
         $group = $this->groupService->createGroup($data);
 
         $this->assertDatabaseHas('groups', [
             'name' => 'Test Group',
             'creator_id' => auth()->id(),
         ]);
 
         $this->assertEquals('Test Group', $group->name);
     }

      /** @test */
    public function it_fails_to_create_group_without_name()
    {
        $this->expectException(\App\Exceptions\CreateObjectException::class);

        // تجربة إنشاء مجموعة بدون اسم
        $data = [];

        $this->groupService->createGroup($data);
    }
    
}
