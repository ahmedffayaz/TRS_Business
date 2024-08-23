<?php

namespace App\Jobs;

use App\Models\Attachment;
use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
class FilesUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected array $attachments;
    private string $filePath = 'files/tasks';
    public $taskId;
    public function __construct($attachment, $id)
    {
        $this->taskId = $id;
        $this->attachments = $attachment;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach($this->attachments as $attachment) {
            if(!array_key_exists('id', $attachment)) {
            $path = Storage::disk('public')->put($this->filePath, new File($attachment['path']));
                Attachment::create([
                    'name' => $attachment['name'],
                    'tmpFilename' =>$attachment['tmpFilename'],
                    'mimes' => $attachment['extension'],
                    'file' => $path,
                    'size' => $attachment['size'],
                    'attachmentable_id' => $this->taskId,
                    'attachmentable_type' => Task::class,
                ]);
            }
        }
    }
}
