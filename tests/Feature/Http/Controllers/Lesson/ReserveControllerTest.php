<?php

namespace Tests\Feature\Http\Controllers\Lesson;

use App\Models\Lesson;
use App\Models\Reservation;
use App\Notifications\ReservationNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\Response;
use Tests\Factories\Traits\CreatesUser;
use Tests\TestCase;

class ReserveControllerTest extends TestCase
{
    use RefreshDatabase;
    use CreatesUser;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testInvoke_正常系()
    {
        Notification::fake();

        $lesson = Lesson::factory()->create();
        $user = $this->createUser();
        $this->actingAs($user);

        $response = $this->post("/lessons/{$lesson->id}/reserve");

        // 指定するユーザーに通知が送信されたことをアサート
        Notification::assertSentTo(
            [$user], ReservationNotification::class
        );

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertRedirect("/lessons/{$lesson->id}");

        $this->assertDatabaseHas('reservations', [
            'lesson_id' => $lesson->id,
            'user_id' => $user->id,
        ]);
    }

    public function testInvoke_異常系()
    {
        $lesson = Lesson::factory()->create(['capacity' => 1]);
        $anotherUser = $this->createUser();
        $lesson->reservations()->save(Reservation::factory()->make(['user_id' => $anotherUser]));

        $user = $this->createUser();
        $this->actingAs($user);

        $response = $this->from("lessons/{$lesson->id}")
                        ->post("/lessons/{$lesson->id}/reserve");

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertRedirect("/lessons/{$lesson->id}");
        $response->assertSessionHasErrors();

        $error = session('errors')->first();
        $this->assertStringContainsString('予約できません。', $error);

        $this->assertDatabaseMissing('reservations', [
            'lesson_id' => $lesson->id,
            'user_id' => $user->id,
        ]);
    }
}
