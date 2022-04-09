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

    private $orderData ;
    private $orderItemsData ;
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->orderData = $this->data[0];
        $this->orderItemsData = $this->data[1];
        // $data = $this->orderData;
        // print_r($this->orderData);
        unset($this->orderData['complete'], $this->orderData['influencer_total'] , $this->orderData['admin_total']);

        // print_r($this->orderData);

        Order::create($this->orderData);

        foreach ($this->orderItemsData as $item) {
            $item['revenue'] = $item['admin_revenue'];
            unset($item['influencer_revenue'], $item['admin_revenue']);

            OrderItem::create($item);
        }
    }
}
