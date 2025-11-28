<x-layouts.app>
    <h1>Hello {{ auth()->guard()->user()->name }}</h1>

    <form action="{{ route('auth.logout') }}" method="post">
        @csrf

        <button>Logout</button>
    </form>
</x-layouts.app>
