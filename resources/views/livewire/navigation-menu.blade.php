<nav class="bg-gray-800 p-4">
    <ul class="flex space-x-4 text-white">
        <li><a href="{{ route('dashboard') }}" class="hover:text-gray-300">Dashboard</a></li>
        <li><a href="{{ route('gems.index') }}" class="hover:text-gray-300">Gems</a></li>
        <li><a href="{{ route('profile.edit') }}" class="hover:text-gray-300">Profile</a></li>
        <li><a href="{{ route('logout') }}" class="hover:text-gray-300" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a></li>
    </ul>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</nav>