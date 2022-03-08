* [Introduction](#introduction)

## Introduction {#introduction}

Livewire pairs nicely with Laravel Echo to provide real-time functionality on your web-pages using WebSockets.

@component('components.warning')
This feature assumes you have installed Laravel Echo and the `window.Echo` object is globally available. For more info on this, check out the <a href="https://laravel.com/docs/9.x/broadcasting#client-side-installation">docs</a>.
@endcomponent

Consider the following Laravel Event:

@component('components.code-component')
@slot('class')
class OrderShipped implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function broadcastOn()
    {
        return new Channel('orders');
    }
}
@endslot
@endcomponent

Let's say you fire this event with Laravel's broadcasting system like this:

@component('components.code', ['lang' => 'php'])
event(new OrderShipped);
@endcomponent

Normally, you would listen for this event in Laravel Echo like so:

@component('components.code', ['lang' => 'js'])
    Echo.channel('orders')
        .listen('OrderShipped', (e) => {
            console.log(e.order.name);
        });
@endcomponent

With Livewire however, all you have to do is register it in your `$listeners` property, with some special syntax to designate that it originates from Echo.

@component('components.code-component')
@slot('class')
class OrderTracker extends Component
{
    public $showNewOrderNotification = false;

    // Special Syntax: ['echo:{channel},{event}' => '{method}']
    protected $listeners = ['echo:orders,OrderShipped' => 'notifyNewOrder'];

    public function notifyNewOrder()
    {
        $this->showNewOrderNotification = true;
    }
}
@endslot
@endcomponent

Now, Livewire will intercept the received event from Pusher, and act accordingly. In a similar way, you can also listen to events broadcasted to private/presence channels:

@component('components.warning')
    Make sure you have your <a href="https://laravel.com/docs/master/broadcasting#defining-authorization-callbacks">Authentication Callbacks</a> properly defined.
@endcomponent

@component('components.code-component')
@slot('class')
class OrderTracker extends Component
{
    public $showNewOrderNotification = false;
    public $orderId;

    public function mount($orderId)
    {
        $this->orderId = $orderId;
    }

    public function getListeners()
    {
        return [
            "echo-private:orders.{$this->orderId},OrderShipped" => 'notifyNewOrder',
            // Or:
            "echo-presence:orders.{$this->orderId},OrderShipped" => 'notifyNewOrder',
        ];
    }

    public function notifyNewOrder()
    {
        $this->showNewOrderNotification = true;
    }
}
@endslot
@endcomponent
