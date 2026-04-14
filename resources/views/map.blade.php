@extends('layouts.app')

@section('title', 'Bản Đồ Sân Bóng Đá - PlayGroundX')

@push('styles')
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <!-- Leaflet Routing Machine CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
    <style>
        :root {
            --map-sidebar-width: 380px;
        }

        .map-wrapper {
            display: flex;
            height: calc(100vh - 76px);
            margin-top: 76px;
            overflow: hidden;
            position: relative;
        }

        #map {
            flex-grow: 1;
            height: 100%;
            z-index: 1;
        }

        .map-sidebar {
            width: var(--map-sidebar-width);
            height: 100%;
            background: #fff;
            box-shadow: 4px 0 15px rgba(0,0,0,0.1);
            z-index: 10;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
        }

        .sidebar-header {
            padding: 1.5rem;
            background: var(--clr-primary-500);
            color: #fff;
        }

        .sidebar-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .search-box {
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.5rem;
            border-radius: 50px;
            border: none;
            background: rgba(255,255,255,0.2);
            color: #fff;
            backdrop-filter: blur(5px);
        }

        .search-box input::placeholder {
            color: rgba(255,255,255,0.7);
        }

        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.7);
        }

        .court-list {
            flex-grow: 1;
            overflow-y: auto;
            padding: 1rem;
        }

        .court-item {
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1rem;
            border: 1px solid #edf2f7;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .court-item:hover {
            border-color: var(--clr-primary-400);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transform: translateY(-2px);
        }

        .court-item.active {
            border-color: var(--clr-primary-500);
            background: rgba(16, 185, 129, 0.08);
        }

        .court-item-name {
            font-weight: 700;
            color: var(--clr-dark-900);
            margin-bottom: 0.25rem;
        }

        .court-item-location {
            font-size: 0.85rem;
            color: var(--clr-dark-500);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .court-item-footer {
            margin-top: 0.75rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .court-price {
            font-weight: 800;
            color: var(--clr-primary-600);
            font-size: 0.95rem;
        }

        .btn-direction {
            padding: 0.4rem 0.8rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            background: var(--clr-primary-500);
            color: #fff;
            border: none;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            transition: background 0.2s;
        }

        .btn-direction:hover {
            background: var(--clr-primary-600);
        }

        /* Routing Control Customization */
        .leaflet-routing-container {
            background-color: white;
            padding: 15px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            max-height: 400px;
            overflow-y: auto;
            width: 320px !important;
            border: 1px solid rgba(16, 185, 129, 0.15) !important;
        }

        .leaflet-routing-container .leaflet-routing-alternatives-container,
        .leaflet-routing-container .leaflet-routing-error {
            color: var(--clr-dark-900);
        }

        .routing-panel-toggle {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 1000;
            padding: 10px 20px;
            background: var(--clr-primary-500);
            color: #fff;
            border-radius: 50px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            font-weight: 600;
            cursor: pointer;
            display: none;
            transition: background 0.2s ease;
        }

        .routing-panel-toggle:hover {
            background: var(--clr-primary-600);
        }

        @media (max-width: 768px) {
            .map-sidebar {
                position: absolute;
                bottom: 0;
                left: 0;
                width: 100%;
                height: 40%;
                transform: translateY(100%);
            }
            .map-sidebar.open {
                transform: translateY(0);
            }
        }
    </style>
@endpush

@section('content')
    <div class="map-wrapper">
        <!-- Sidebar -->
        <aside class="map-sidebar">
            <div class="sidebar-header">
                <h2 class="sidebar-title">Tìm sân bóng gần bạn</h2>
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="courtSearch" placeholder="Nhập tên sân hoặc địa chỉ...">
                </div>
            </div>
            
            <div class="court-list" id="courtList">
                @foreach($arenas as $arena)
                    <div class="court-item" data-lat="{{ $arena->latitude }}" data-lng="{{ $arena->longitude }}" data-id="{{ $arena->id }}">
                        <div class="court-item-name">{{ $arena->name }}</div>
                        <div class="small text-primary fw-bold mb-2">{{ $arena->type }}</div>
                        <div class="court-item-location">
                            <i class="fas fa-map-marker-alt text-danger"></i>
                            <span>{{ $arena->location }}</span>
                        </div>
                        <div class="court-item-footer">
                            <div class="court-price">{{ number_format($arena->price) }}đ <small>/ giờ</small></div>
                            <button class="btn-direction" onclick="getDirections({{ $arena->latitude }}, {{ $arena->longitude }}, '{{ $arena->name }}')">
                                <i class="fas fa-route"></i>
                                Chỉ đường
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </aside>

        <!-- Map Container -->
        <div id="map"></div>
        
        <div id="routingToggle" class="routing-panel-toggle" onclick="toggleRoutingPanel()">
            <i class="fas fa-list-ol me-2"></i> Chi tiết đường đi
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <!-- Leaflet Routing Machine JS -->
    <script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>
    
    <script>
        let map;
        let userLocation = [10.7767, 106.6709]; // Default to District 10, HCM
        let markers = [];
        let routingControl = null;
        let startMarker = null;

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Map
            map = L.map('map').setView(userLocation, 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Fetch markers from backend
            const arenas = @json($arenas);
            
            arenas.forEach(arena => {
                const marker = L.marker([arena.latitude, arena.longitude])
                    .addTo(map)
                    .bindPopup(`
                        <div style="padding: 5px; min-width: 180px;">
                            <h6 style="margin-bottom: 2px; font-weight: 700; font-size: 1rem; color: #1e293b;">${arena.name}</h6>
                            <div style="color: #3b82f6; font-weight: 800; font-size: 0.75rem; margin-bottom: 8px;">${arena.type}</div>
                            <p style="margin-bottom: 12px; font-size: 0.85rem; color: #64748b; line-height: 1.4;">
                                <i class="fas fa-map-marker-alt text-danger me-1"></i>${arena.location}
                            </p>
                            <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f1f5f9; pt-2; mt-1;">
                                <span style="font-weight: 800; color: #3b82f6; font-size: 0.95rem;">${new Intl.NumberFormat('vi-VN').format(arena.price)}đ</span>
                                <button onclick="getDirections(${arena.latitude}, ${arena.longitude}, '${arena.name}')" 
                                    style="background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; border: none; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                                    <i class="fas fa-route me-1"></i>Chỉ đường
                                </button>
                            </div>
                        </div>
                    `);
                
                marker.arenaId = arena.id;
                markers.push(marker);
            });

            // Setup Start Marker (Draggable)
            const startIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });

            startMarker = L.marker(userLocation, {
                icon: startIcon,
                draggable: true
            }).addTo(map).bindPopup("<b>Vị trí xuất phát của bạn</b><br><small>Có thể kéo thả để thay đổi</small>");

            startMarker.on('dragend', function(event) {
                userLocation = [event.target.getLatLng().lat, event.target.getLatLng().lng];
            });

            // Get Real Location if available
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(position => {
                    userLocation = [position.coords.latitude, position.coords.longitude];
                    startMarker.setLatLng(userLocation);
                    map.setView(userLocation, 14);
                }, () => {
                    console.log("Geolocation access denied. Using default location.");
                    alert("Không thể lấy vị trí tự động. Bạn có thể kéo biểu tượng màu XANH LÁ trên bản đồ để chọn điểm xuất phát.");
                });
            }

            // ... (rest of search/click logic)

            // Court List click events
            document.querySelectorAll('.court-item').forEach(item => {
                item.addEventListener('click', function(e) {
                    if (e.target.closest('.btn-direction')) return;
                    
                    const lat = parseFloat(this.dataset.lat);
                    const lng = parseFloat(this.dataset.lng);
                    const id = parseInt(this.dataset.id);

                    map.flyTo([lat, lng], 16);
                    
                    markers.find(m => m.arenaId === id).openPopup();
                    
                    document.querySelectorAll('.court-item').forEach(el => el.classList.remove('active'));
                    this.classList.add('active');
                });
            });

            // Search logic
            document.getElementById('courtSearch').addEventListener('input', function() {
                const query = this.value.toLowerCase();
                document.querySelectorAll('.court-item').forEach(item => {
                    const name = item.querySelector('.court-item-name').textContent.toLowerCase();
                    const loc = item.querySelector('.court-item-location').textContent.toLowerCase();
                    
                    if (name.includes(query) || loc.includes(query)) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });

        function getDirections(destLat, destLng, destName) {
            if (!userLocation) {
                alert("Vui lòng cho phép truy cập vị trí để sử dụng chức năng này.");
                return;
            }

            if (routingControl) {
                map.removeControl(routingControl);
            }

            routingControl = L.Routing.control({
                waypoints: [
                    L.latLng(userLocation[0], userLocation[1]),
                    L.latLng(destLat, destLng)
                ],
                routeWhileDragging: true,
                lineOptions: {
                    styles: [{ color: '#3b82f6', opacity: 0.8, weight: 6 }]
                },
                createMarker: function() { return null; }, // Hide default markers
                language: 'vi'
            }).addTo(map);

            document.getElementById('routingToggle').style.display = 'block';
            
            if (window.innerWidth <= 768) {
                document.querySelector('.map-sidebar').classList.remove('open');
            }
        }

        function toggleRoutingPanel() {
            const panel = document.querySelector('.leaflet-routing-container');
            if (panel) {
                panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
            }
        }
    </script>
@endpush
