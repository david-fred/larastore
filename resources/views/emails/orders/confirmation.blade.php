<x-mail::message>
    # Thanks for your order, {{ $order->user->name }}

    Your payment was received. Your order reference is **{{ $order->reference }}**.

    <x-mail::table>
        | Item | Qty | Amount |
        |:--|:--:|--:|
        @foreach ($order->items as $item)
            | {{ $item->title }}@if ($item->variant_label && $item->variant_label !== 'Default') ({{ $item->variant_label }})@endif | {{ $item->quantity }} | ${{ number_format($item->price * $item->quantity, 2) }} |
        @endforeach
    </x-mail::table>

    **Total: ${{ number_format($order->total, 2) }}**

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>
