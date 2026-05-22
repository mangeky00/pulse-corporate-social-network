@if ($errors->any())
    <section class="flash flash--error">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </section>
@endif

@if (session('message'))
    <section class="flash flash--success">
        {{ session('message') }}
    </section>
@endif
