<?php 
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Carbon\Carbon;
use App\Http\Controllers\UploadController;

class UploadControllerTest extends TestCase
{
    public function test_upload_file()
    {
        Storage::fake('public');

        $this->travelTo(Carbon::make('2021-01-01 00:00:00'));
        
        $file = UploadedFile::fake()->image('test.jpg');

        $this->post(action([UploadController::class]), [
            'file' => $file
        ])
            ->assertSuccessful()
            ->assertSee('/storage/uploads/2021-01-01-00-00-00-test.jpg');

        Storage::disk('public')->assertExists('uploads/2021-01-01-00-00-00-test.jpg');
    }
}