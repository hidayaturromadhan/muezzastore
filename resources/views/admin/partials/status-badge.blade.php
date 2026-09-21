@php
    $s = strtolower((string) $status);

    $map = [
        'pending_payment' => ['Pending', 'secondary'],
        'paid'            => ['Paid', 'success'],
        'processing'      => ['Processing', 'warning'],
        'fulfilled'       => ['Fulfilled', 'primary'],
        'failed'          => ['Failed', 'danger'],
    ];

    [$label, $color] = $map[$s] ?? [ucfirst($s), 'dark'];
@endphp

<span class="badge text-bg-{{ $color }}">{{ $label }}</span>
