<!-- Menu -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="{{ url('/') }}" class="app-brand-link"><x-app-logo /></a>
  </div>

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
    <!-- Dashboards -->
    <li class="menu-item {{ request()->is('dashboard') ? 'active' : '' }}">
      <a class="menu-link small-menu-text" href="{{ route('dashboard') }}" wire:navigate>
        <i class="menu-icon tf-icons bx bx-home-circle"></i>
        <div class="text-truncate">{{ __('Dashboard') }}</div>
      </a>
    </li>

    <!-- User Management -->
    <li class="menu-item {{ request()->is('users') ? 'active' : '' }}">
      <a class="menu-link small-menu-text" href="{{ route('users') }}" wire:navigate>
        <i class="menu-icon tf-icons bx bx-user"></i>
        <div class="text-truncate">{{ __('Manajemen Pengguna') }}</div>
      </a>
    </li>

    <!-- Manajemen Standar Mutu -->
    <li class="menu-item {{ request()->is('standar-mutu*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle small-menu-text">
        <i class="menu-icon tf-icons bx bx-check-circle"></i>
        <div class="text-truncate">{{ __('Manajemen Standar Mutu') }}</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ request()->routeIs('standar-mutu') ? 'active' : '' }}">
          <a class="menu-link small-menu-text" href="{{ route('standar-mutu') }}" wire:navigate>{{ __('Daftar Standar Mutu') }}</a>
        </li>
      </ul>
    </li>

    <!-- Referensi -->
    <li class="menu-item {{ request()->is('tahun') || request()->is('lembaga-akreditasi') || request()->is('standar-nasional') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle small-menu-text">
        <i class="menu-icon tf-icons bx bx-book"></i>
        <div class="text-truncate">{{ __('Manajemen Referensi') }}</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ request()->routeIs('tahun') ? 'active' : '' }}">
          <a class="menu-link small-menu-text" href="{{ route('tahun') }}" wire:navigate>{{ __('Manajemen Tahun') }}</a>
        </li>
        <li class="menu-item {{ request()->routeIs('lembaga-akreditasi') ? 'active' : '' }}">
          <a class="menu-link small-menu-text" href="{{ route('lembaga-akreditasi') }}" wire:navigate>{{ __('Manajemen Lembaga Akreditasi') }}</a>
        </li>
        <li class="menu-item {{ request()->routeIs('standar-nasional') ? 'active' : '' }}">
          <a class="menu-link small-menu-text" href="{{ route('standar-nasional') }}" wire:navigate>{{ __('Standar Nasional') }}</a>
        </li>
      </ul>
    </li>

    <!-- Data Master -->
    <li class="menu-item {{ request()->is('prodi') || request()->is('jabatan') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle small-menu-text">
        <i class="menu-icon tf-icons bx bx-cog"></i>
        <div class="text-truncate">{{ __('Data Master') }}</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item {{ request()->routeIs('prodi') ? 'active' : '' }}">
          <a class="menu-link small-menu-text" href="{{ route('prodi') }}" wire:navigate>{{ __('Manajemen Prodi') }}</a>
        </li>
        <li class="menu-item {{ request()->routeIs('jabatan') ? 'active' : '' }}">
          <a class="menu-link small-menu-text" href="{{ route('jabatan') }}" wire:navigate>{{ __('Manajemen Jabatan') }}</a>
        </li>
      </ul>
    </li>

  </ul>
</aside>
<!-- / Menu -->

<style>
.small-menu-text {
  font-size: 0.7rem !important;
}

.small-menu-text .text-truncate {
  font-size: 0.7rem !important;
}

/* Untuk submenu agar lebih kecil */
.menu-sub .menu-link.small-menu-text {
  font-size: 0.75rem !important;
}
</style>

<script>
  // Toggle the 'open' class when the menu-toggle is clicked
  document.querySelectorAll('.menu-toggle').forEach(function(menuToggle) {
    menuToggle.addEventListener('click', function() {
      const menuItem = menuToggle.closest('.menu-item');
      // Toggle the 'open' class on the clicked menu-item
      menuItem.classList.toggle('open');
    });
  });
</script>