<?php
namespace App\Jobs;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class OrderCompleted implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $orderData ;
    public $orderItemsData ;

    public function __construct($orderData, $orderItemsData)
    {
        $this->orderData = $orderData;
        $this->orderItemsData = $orderItemsData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = $this->orderData;
        unset($data['complete']);
        Order::create($data);

        foreach ($this->orderItemsData as $item) {
            $item['revenue'] = $item['admin_revenue'];
            unset($item['influencer_revenue'], $item['admin_revenue']);

            OrderItem::create($item);
        }
    }
}
