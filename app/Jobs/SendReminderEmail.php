<?php

/**

我是一个任务类，可以在我的handle方法里写任务的逻辑
比如发个邮件，输出个内容，或者打个日志等都行。这都可以认为是一个任务
*/
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Log;

class SendReminderEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *任务的真正逻辑，就写在handle里就行了
	 我这里就打个日志而已。
     * @return void
     */
    public function handle()
    {
        //
		\Log::info('任务来了');
		return '啥意思';
    }
}
