@foreach ($notifications as $n)
    <x-notifications.item :n="$n" />
@endforeach
