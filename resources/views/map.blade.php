@extends('layouts.app')

@section('title', 'Bản Đồ Sân Bóng Đá - PlayGroundX')

@push('styles')
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <!-- Leaflet Routing Machine CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
    <style>
        /* Hide footer on map page */
        .page-map .site-footer {
            display: none !important;
        }

        /* Prevent scroll on body for full-screen map */
        body.page-map {
            overflow: hidden;
            height: 100vh;
        }

        .map-wrapper {
            display: flex;
            height: calc(100vh - 76px);
            margin-top: 76px;
            overflow: hidden;
            position: relative;
            background: #f8fafc;
            width: 100%;
        }

        #map {
            flex-grow: 1;
            height: 100%;
            width: 100%;
            z-index: 1;
            background: #e5e7eb; /* Fallback color */
        }

        .map-sidebar {
            width: 400px;
            height: 100%;
            background: #ffffff;
            box-shadow: 10px 0 30px rgba(0,0,0,0.05);
            z-index: 10;
            display: flex;
            flex-direction: column;
            position: relative;
            flex-shrink: 0;
        }

        .sidebar-header {
            padding: 2rem 1.5rem;
            background: #ffffff;
            border-bottom: 2px solid #f1f5f9;
        }

        .sidebar-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 1.25rem;
        }

        .search-box {
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 12px 14px 12px 42px;
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            background: #f8fafc;
            color: #1e293b;
            font-size: 0.95rem;
        }

        .search-box i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
        }

        .filter-container {
            padding: 1rem 1.5rem;
            display: flex;
            gap: 0.5rem;
            background: #fff;
            overflow-x: auto;
            scrollbar-width: none;
        }

        .filter-chip {
            padding: 6px 16px;
            border-radius: 50px;
            background: #f1f5f9;
            color: #64748b;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            border: 2px solid transparent;
            white-space: nowrap;
        }

        .filter-chip.active {
            background: #10b981;
            color: #ffffff;
        }

        .court-list {
            flex-grow: 1;
            overflow-y: auto;
            padding: 1rem 1.5rem;
            background: #fbfcfe;
        }

        .court-item {
            background: #ffffff;
            border-radius: 16px;
            padding: 1.25rem;
            margin-bottom: 1rem;
            border: 2px solid #f1f5f9;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
        }

        .court-item:hover {
            border-color: #10b981;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .court-item.active {
            border-color: #10b981;
            background: #f0fdf4;
        }

        .court-badge {
            position: absolute;
            top: 1.25rem;
            right: 1.25rem;
            background: #dcfce7;
            color: #15803d;
            padding: 2px 10px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .court-name {
            font-size: 1.1rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .court-info {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-bottom: 1rem;
            color: #64748b;
            font-size: 0.85rem;
        }

        .court-info div {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .court-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid #f1f5f9;
        }

        .court-price {
            font-weight: 800;
            font-size: 1.1rem;
            color: #1e293b;
        }

        .btn-direction {
            background-color: #2563eb !important;
            background-image: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
            color: #ffffff !important;
            border: none !important;
            padding: 8px 16px !important;
            border-radius: 10px !important;
            font-weight: 700 !important;
            font-size: 0.9rem !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 8px !important;
            cursor: pointer !important;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3) !important;
            text-decoration: none !important;
        }

        .btn-direction:hover {
            background-color: #1d4ed8 !important;
            box-shadow: 0 6px 14px rgba(37, 99, 235, 0.4) !important;
            transform: scale(1.02);
        }

        .custom-marker {
            background: #fff;
            border: 2px solid #10b981;
            border-radius: 50%;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #10b981;
        }

        .custom-marker.active {
            background: #10b981;
            color: #fff;
            transform: scale(1.2);
            z-index: 1000 !important;
        }

        .user-marker {
            width: 18px;
            height: 18px;
            background: #3b82f6;
            border: 3px solid #fff;
            border-radius: 50%;
            box-shadow: 0 0 10px rgba(59, 130, 246, 0.5);
            position: relative;
        }

        .user-marker::after {
            content: '';
            position: absolute;
            top: -3px;
            left: -3px;
            right: -3px;
            bottom: -3px;
            border-radius: 50%;
            background: rgba(59, 130, 246, 0.4);
            animation: pulse-user 2s infinite;
            z-index: -1;
        }

        @keyframes pulse-user {
            0% {
                transform: scale(1);
                opacity: 1;
            }
            100% {
                transform: scale(3.5);
                opacity: 0;
            }
        }

        .route-info-card {
            position: absolute;
            top: 1.5rem;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            background: #2563eb;
            color: #fff;
            padding: 12px 24px;
            border-radius: 16px;
            display: none;
            align-items: center;
            gap: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        .route-stat {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .route-stat-label {
            font-size: 0.65rem;
            text-transform: uppercase;
            opacity: 0.8;
            font-weight: 700;
        }

        .route-stat-value {
            font-size: 1.1rem;
            font-weight: 800;
        }

        .btn-close-route {
            background: rgba(255,255,255,0.2);
            border: none;
            color: #fff;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
        }

        .btn-locate {
            position: absolute;
            bottom: 30px;
            right: 20px;
            z-index: 1000;
            background: #ffffff;
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            cursor: pointer;
            border: none;
            color: #1e293b;
            font-size: 1.25rem;
            transition: all 0.2s;
        }

        .btn-locate:hover {
            background: #f8fafc;
            color: #2563eb;
            transform: scale(1.05);
        }

        .btn-locate.loading {
            animation: rotate 1s infinite linear;
            color: #10b981;
        }

        /* Manual Location Styles */
        .manual-location-container {
            padding: 1rem 1.5rem;
            background: #f8fafc;
            border-bottom: 2px solid #f1f5f9;
        }

        .manual-location-title {
            font-size: 0.85rem;
            font-weight: 700;
            color: #64748b;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .manual-search-wrapper {
            display: flex;
            gap: 8px;
        }

        .manual-search-wrapper input {
            flex-grow: 1;
            padding: 10px 14px;
            border-radius: 10px;
            border: 2px solid #e2e8f0;
            font-size: 0.9rem;
            transition: border-color 0.2s;
        }

        .manual-search-wrapper input:focus {
            border-color: #3b82f6;
            outline: none;
        }

        .btn-find-address {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 0 16px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-find-address:hover {
            background: #2563eb;
            transform: translateY(-1px);
        }

        .btn-find-address:active {
            transform: translateY(0);
        }

        .btn-find-address.loading i {
            animation: rotate 1s infinite linear;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .leaflet-routing-container {
            display: none !important;
        }

        @media (max-width: 768px) {
            .map-sidebar {
                position: absolute;
                bottom: 0;
                left: 0;
                width: 100%;
                height: 40%;
                transform: translateY(100%);
                z-index: 20;
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
                <h2 class="sidebar-title">Khám phá sân bóng</h2>
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="courtSearch" placeholder="Tìm tên sân hoặc địa chỉ...">
                </div>
            </div>

            <!-- Manual Location Input (Fallback) -->
            <div class="manual-location-container" id="manualLocationContainer" style="display: none;">
                <div class="manual-location-title">
                    <i class="fas fa-map-marker-alt text-primary"></i>
                    <span>Vị trí của bạn</span>
                </div>
                <div class="manual-search-wrapper">
                    <input type="text" id="manualAddressInput" placeholder="Nhập địa chỉ hiện tại..." 
                           onkeypress="if(event.key === 'Enter') window.findManualLocation()">
                    <button class="btn-find-address" id="btnFindManualAddress" onclick="window.findManualLocation()">
                        <i class="fas fa-search-location"></i>
                    </button>
                </div>
                <div id="manualLocationStatus" style="font-size: 0.75rem; margin-top: 6px; display: none;"></div>
            </div>

            <div style="padding: 0 1.5rem; margin-top: 0.5rem;">
                <a href="javascript:void(0)" onclick="document.getElementById('manualLocationContainer').style.display='block'; this.style.display='none';" 
                   id="toggleManualBtn" style="font-size: 0.8rem; color: #3b82f6; text-decoration: none; font-weight: 600;">
                    <i class="fas fa-keyboard"></i> Nhập địa chỉ thủ công
                </a>
            </div>

            <!-- Filter Chips -->
            <div class="filter-container">
                <div class="filter-chip active" data-type="all">Tất cả</div>
                <div class="filter-chip" data-type="Sân 5">Sân 5</div>
                <div class="filter-chip" data-type="Sân 7">Sân 7</div>
                <div class="filter-chip" data-type="Sân 11">Sân 11</div>
                <div class="filter-chip" data-type="nearby">Gần tôi</div>
            </div>
            
            <div class="court-list" id="courtList">
                @foreach($arenas as $arena)
                    <div class="court-item" 
                         data-lat="{{ $arena->latitude }}" 
                         data-lng="{{ $arena->longitude }}" 
                         data-id="{{ $arena->id }}"
                         data-type="{{ $arena->type }}">
                        
                        <div class="d-flex gap-1 position-absolute top-0 end-0 m-3">
                            <div class="court-badge">{{ $arena->type }}</div>
                            @if($arena->status === 'maintenance')
                                <div class="court-badge" style="background: #ffedd5; color: #9a3412;"><i class="fas fa-wrench me-1"></i>Bảo trì</div>
                            @endif
                        </div>
                        <h3 class="court-name">{{ $arena->name }}</h3>
                        
                        <div class="court-info">
                            <div>
                                <i class="fas fa-star text-warning"></i>
                                <strong>4.8</strong>
                            </div>
                            <div>
                                <i class="fas fa-map-marker-alt"></i>
                                <span>{{ Str::limit($arena->location, 30) }}</span>
                            </div>
                            <div class="distance-info" style="display: none; color: #3b82f6; font-weight: 700;">
                                <i class="fas fa-location-arrow"></i>
                                <span class="distance-value">0 km</span>
                            </div>
                        </div>
                        <div class="court-footer">
                            <div class="court-price">
                                {{ number_format($arena->price, 0, ',', '.') }}đ 
                                <small class="text-muted" style="font-size: 0.7rem;">/ giờ</small>
                            </div>
                            <button class="btn-direction" type="button" 
                                    onclick="event.stopPropagation(); window.getDirections({{ $arena->latitude }}, {{ $arena->longitude }}, '{{ addslashes($arena->name) }}')">
                                <i class="fas fa-directions"></i> Chỉ đường
                            </button>
                        </div>
                    </div>
@endforeach
            </div>
        </aside>

        <!-- Map Container -->
        <div id="map">
            <button class="btn-locate" id="btnLocate" onclick="window.findUser()" title="Vị trí hiện tại">
                <i class="fas fa-location-arrow"></i>
            </button>
        </div>
        
        <!-- Route Info Card -->
        <div id="routeInfoCard" class="route-info-card">
            <div class="route-stat">
                <span class="route-stat-label">Khoảng cách</span>
                <span id="routeDistance" class="route-stat-value">0 km</span>
            </div>
            <div style="width: 1px; height: 30px; background: rgba(255,255,255,0.3);"></div>
            <div class="route-stat">
                <span class="route-stat-label">Thời gian</span>
                <span id="routeDuration" class="route-stat-value">0 phút</span>
            </div>
            <button class="btn-close-route" onclick="clearRoute()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <!-- Leaflet Routing Machine JS -->
    <script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>
    
    <script>
    (function() {
        let map;
        let userLocation = null;
        let markers = [];
        let routingControl = null;
        let userMarker = null;
        let accuracyCircle = null;
        let isFirstLocation = true;
        let isTracking = true; // Cờ kiểm soát việc camera có bám theo user không
        const defaultLocation = [21.0285, 105.8542]; // Hà Nội

        function initMap() {
            console.log("Initializing map...");
            map = L.map('map', {
                zoomControl: false
            }).setView(defaultLocation, 14);

            L.control.zoom({
                position: 'bottomright'
            }).addTo(map);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            // Tự động tắt chế độ "bám theo" nếu người dùng chủ động vuốt/kéo bản đồ
            map.on('dragstart', function() {
                isTracking = false;
                document.getElementById('btnLocate').style.color = '#1e293b'; // Đổi màu nút để báo hiệu hết track
            });

            const arenas = @json($arenas);
            arenas.forEach(arena => {
                createMarker(arena);
            });

            setTimeout(() => {
                map.invalidateSize();
            }, 100);
        }

        // ... (Giữ nguyên hàm createMarker của bạn) ...
        function createMarker(arena) {
            const icon = L.divIcon({
                className: 'custom-marker',
                html: '<i class="fas fa-futbol"></i>',
                iconSize: [40, 40],
                iconAnchor: [20, 20],
                popupAnchor: [0, -20]
            });

            const marker = L.marker([arena.latitude, arena.longitude], { icon: icon })
                .addTo(map)
                .bindPopup(`
                    <div style="padding: 10px; min-width: 200px; font-family: sans-serif;">
                        <div style="font-weight: 800; font-size: 1.1rem; color: #1e293b; margin-bottom: 4px;">${arena.name}</div>
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                            <div style="color: #10b981; font-weight: 700; font-size: 0.8rem;">${arena.type}</div>
                            ${arena.status === 'maintenance' ? `<div style="background: #ffedd5; color: #9a3412; font-weight: 700; font-size: 0.7rem; padding: 2px 8px; border-radius: 50px;"><i class="fas fa-wrench me-1"></i>BẢO TRÌ</div>` : ''}
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; border-top: 2px solid #f1f5f9; padding-top: 12px; margin-top: 8px;">
                            <div>
                                <span style="font-weight: 800; font-size: 1rem; color: #1e293b;">${new Intl.NumberFormat('vi-VN').format(arena.price)}đ</span>
                                <div style="font-size: 0.65rem; color: #64748b;">/ giờ</div>
                            </div>
                            ${arena.status === 'maintenance' ? 
                                `<button class="btn-direction" style="font-size: 0.75rem; padding: 6px 12px; background: #94a3b8 !important; cursor: not-allowed;" disabled>
                                    <i class="fas fa-tools"></i> Bảo trì
                                </button>` :
                                `<button onclick="window.getDirections(${arena.latitude}, ${arena.longitude}, '${arena.name.replace(/'/g, "\\'")}')" 
                                    class="btn-direction" style="font-size: 0.75rem; padding: 6px 12px;">
                                    <i class="fas fa-directions"></i> Chỉ đường
                                </button>`
                            }
                        </div>
                    </div>
                `);

            marker.arenaData = arena;
            markers.push(marker);

            marker.on('click', function() {
                window.setActiveCourt(arena.id);
            });
        }

        function setupGeolocation() {
            if (navigator.geolocation) {
                console.log("🚀 Initializing Automatic High-Accuracy Tracking...");
                
                const geoOptions = {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                };

                navigator.geolocation.watchPosition(
                    position => handleLocationSuccess(position, false),
                    handleLocationError,
                    geoOptions
                );

            } else {
                alert("Trình duyệt của bạn không hỗ trợ GPS.");
            }
        }

        function handleLocationSuccess(position, isManualRequest) {
            const { latitude, longitude, accuracy } = position.coords;
            userLocation = [latitude, longitude];
            
            // 1. Cập nhật marker MƯỢT MÀ, không xoá tạo lại
            updateUserMarker(false, accuracy);
            
            // 2. Tính lại khoảng cách realtime
            calculateDistances();

            // 3. Xử lý camera
            if (isFirstLocation) {
                map.flyTo(userLocation, 16, { animate: true, duration: 1.5 });
                isFirstLocation = false;
            } else if (isTracking || isManualRequest) {
                // Nếu đang trong chế độ tracking hoặc bấm nút Locate, camera sẽ đi theo
                map.panTo(userLocation, { animate: true });
            }
            
            const btn = document.getElementById('btnLocate');
            if (btn) btn.classList.remove('loading');
        }

        function handleLocationError(error) {
            // ... (Giữ nguyên hàm báo lỗi của bạn) ...
            let msg = "";
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    msg = "Vui lòng cấp quyền truy cập vị trí.";
                    break;
                case error.POSITION_UNAVAILABLE:
                    msg = "Không thể lấy tín hiệu GPS.";
                    break;
                case error.TIMEOUT:
                    msg = "Quá thời gian lấy vị trí.";
                    break;
                default:
                    msg = "Lỗi định vị: " + error.message;
            }
            console.warn("⚠️ Geolocation error:", msg);
            const btn = document.getElementById('btnLocate');
            if (btn) btn.classList.remove('loading');

            // Show manual input fallback when geolocation fails
            const manualContainer = document.getElementById('manualLocationContainer');
            if (manualContainer) {
                manualContainer.style.display = 'block';
                const status = document.getElementById('manualLocationStatus');
                status.textContent = "Không thể lấy vị trí tự động. Vui lòng nhập địa chỉ thủ công.";
                status.style.color = "#ef4444";
                status.style.display = 'block';
            }
        }

        window.findManualLocation = function() {
            const input = document.getElementById('manualAddressInput');
            const address = input.value.trim();
            const btn = document.getElementById('btnFindManualAddress');
            const status = document.getElementById('manualLocationStatus');

            if (!address) {
                alert("Vui lòng nhập địa chỉ!");
                return;
            }

            btn.classList.add('loading');
            status.textContent = "Đang tìm vị trí...";
            status.style.color = "#64748b";
            status.style.display = 'block';

            // Use Nominatim OpenStreetMap API for geocoding
            const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}&limit=1`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    btn.classList.remove('loading');
                    if (data && data.length > 0) {
                        const lat = parseFloat(data[0].lat);
                        const lon = parseFloat(data[0].lon);
                        
                        userLocation = [lat, lon];
                        isTracking = false; // Disable auto-tracking as this is manual
                        
                        // Update marker and camera
                        updateUserMarker(true, 0); // Position is manual, so set draggable=true, accuracy=0
                        map.flyTo(userLocation, 16, { animate: true, duration: 1.5 });
                        
                        // Calculate distances to arenas from this new position
                        calculateDistances();
                        
                        status.textContent = "Đã cập nhật vị trí của bạn!";
                        status.style.color = "#10b981";
                        
                        // Set the marker popup for manual position
                        if (userMarker) {
                            userMarker.getPopup().setContent(`<b>Vị trí bạn đã nhập:</b><br>${data[0].display_name}`);
                            userMarker.openPopup();
                        }
                    } else {
                        status.textContent = "Không tìm thấy địa chỉ này. Hãy thử lại.";
                        status.style.color = "#ef4444";
                    }
                })
                .catch(err => {
                    btn.classList.remove('loading');
                    console.error("Geocoding error:", err);
                    status.textContent = "Lỗi khi tìm địa chỉ. Vui lòng thử lại.";
                    status.style.color = "#ef4444";
                });
        };

        window.findUser = function() {
            const btn = document.getElementById('btnLocate');
            btn.classList.add('loading');
            
            // Khi bấm nút Locate, bật lại chế độ tự động bám theo
            isTracking = true;
            btn.style.color = '#10b981'; // Đổi màu xanh báo hiệu đang track
            
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(position => {
                    handleLocationSuccess(position, true);
                    map.flyTo(userLocation, 17, { animate: true, duration: 1 });
                }, handleLocationError, {
                    enableHighAccuracy: true, timeout: 5000
                });
            }
        };

        function updateUserMarker(isDraggable = false, accuracy = 0) {
            if (!userLocation) return;

            // NẾU ĐÃ CÓ MARKER -> CHỈ DI CHUYỂN TOẠ ĐỘ (Tránh nhấp nháy)
            if (userMarker) {
                userMarker.setLatLng(userLocation);
                if (accuracyCircle && accuracy > 0) {
                    accuracyCircle.setLatLng(userLocation);
                    accuracyCircle.setRadius(accuracy);
                }
                
                const popupContent = isDraggable ? 
                    "<b>Chế độ thủ công</b><br>Kéo marker này đến vị trí của bạn" : 
                    "<b>Vị trí thật của bạn</b><br><small>Độ chính xác: " + Math.round(accuracy) + "m</small>";
                userMarker.getPopup().setContent(popupContent);
                
            } else {
                // NẾU CHƯA CÓ MARKER -> TẠO MỚI (Lần đầu tiên)
                if (accuracy > 0) {
                    accuracyCircle = L.circle(userLocation, {
                        radius: accuracy,
                        color: '#3b82f6',
                        fillColor: '#3b82f6',
                        fillOpacity: 0.15,
                        weight: 1
                    }).addTo(map);
                }

                const icon = L.divIcon({
                    className: 'user-marker',
                    html: '',
                    iconSize: [18, 18],
                    iconAnchor: [9, 9]
                });

                userMarker = L.marker(userLocation, { 
                    icon: icon,
                    zIndexOffset: 1000,
                    draggable: isDraggable
                }).addTo(map);

                const popupContent = "<b>Vị trí thật của bạn</b><br><small>Độ chính xác: " + Math.round(accuracy) + "m</small>";
                userMarker.bindPopup(popupContent);
            }
        }

        // ... (Các hàm còn lại như calculateDistances, getDirections, clearRoute, setupSearch, setupFilters giữ nguyên hoàn toàn) ...
        
        function calculateDistances() {
            if (!userLocation) return;
            markers.forEach(marker => {
                const arena = marker.arenaData;
                const arenaLatLng = L.latLng(arena.latitude, arena.longitude);
                const dist = L.latLng(userLocation).distanceTo(arenaLatLng);
                const distKm = (dist / 1000).toFixed(1);
                
                const item = document.querySelector(`.court-item[data-id="${arena.id}"]`);
                if (item) {
                    const distInfo = item.querySelector('.distance-info');
                    const distValue = item.querySelector('.distance-value');
                    if (distValue) distValue.textContent = distKm + ' km';
                    if (distInfo) distInfo.style.display = 'flex';
                    item.dataset.distance = dist;
                }
            });
        }

        window.getDirections = function(destLat, destLng, destName) {
            if (!userLocation) {
                alert("Đang xác định vị trí của bạn, vui lòng đợi giây lát...");
                window.findUser();
                return;
            }
            if (window.currentRoute) map.removeLayer(window.currentRoute);
            
            const infoCard = document.getElementById('routeInfoCard');
            infoCard.style.display = 'none';

            const start = `${userLocation[1]},${userLocation[0]}`; 
            const end = `${destLng},${destLat}`; 
            const url = `https://router.project-osrm.org/route/v1/driving/${start};${end}?overview=full&geometries=geojson`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.code !== 'Ok' || !data.routes || data.routes.length === 0) {
                        throw new Error("Không thể tìm thấy lộ trình.");
                    }
                    const route = data.routes[0];
                    window.currentRoute = L.geoJSON(route.geometry, {
                        style: function() {
                            return { color: '#2563eb', weight: 6, opacity: 0.9, lineCap: 'round', lineJoin: 'round' };
                        }
                    }).addTo(map);

                    const glow = L.geoJSON(route.geometry, {
                        style: { color: 'white', weight: 10, opacity: 0.5, lineCap: 'round' }
                    }).addTo(window.currentRoute);
                    glow.bringToBack();

                    document.getElementById('routeDistance').textContent = (route.distance / 1000).toFixed(1) + ' km';
                    document.getElementById('routeDuration').textContent = Math.ceil(route.duration / 60) + ' phút';
                    infoCard.style.display = 'flex';

                    map.fitBounds(window.currentRoute.getBounds(), { padding: [100, 100], animate: true });
                })
                .catch(err => {
                    alert("Lỗi dẫn đường qua đường phố: " + err.message);
                });
        };

        window.clearRoute = function() {
            if (window.currentRoute) {
                map.removeLayer(window.currentRoute);
                window.currentRoute = null;
            }
            document.getElementById('routeInfoCard').style.display = 'none';
        };

        window.setActiveCourt = function(id) {
            document.querySelectorAll('.court-item').forEach(el => {
                el.classList.remove('active');
                if (parseInt(el.dataset.id) === id) {
                    el.classList.add('active');
                    el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            });

            markers.forEach(m => {
                const el = m.getElement();
                if (el) {
                    if (m.arenaData.id === id) el.classList.add('active');
                    else el.classList.remove('active');
                }
            });
        };

        function setupSearch() {
            document.getElementById('courtSearch').addEventListener('input', function() {
                const query = this.value.toLowerCase();
                document.querySelectorAll('.court-item').forEach(item => {
                    const name = item.querySelector('.court-name').textContent.toLowerCase();
                    const loc = item.textContent.toLowerCase();
                    item.style.display = (name.includes(query) || loc.includes(query)) ? 'block' : 'none';
                });
            });

            document.querySelectorAll('.court-item').forEach(item => {
                item.addEventListener('click', function(e) {
                    if (e.target.closest('.btn-direction')) return;
                    const lat = parseFloat(this.dataset.lat);
                    const lng = parseFloat(this.dataset.lng);
                    const id = parseInt(this.dataset.id);
                    isTracking = false; // Tắt bám theo khi người dùng chủ động xem sân cụ thể
                    map.flyTo([lat, lng], 16);
                    const marker = markers.find(m => m.arenaData.id === id);
                    if (marker) marker.openPopup();
                    window.setActiveCourt(id);
                });
            });
        }

        function setupFilters() {
            document.querySelectorAll('.filter-chip').forEach(chip => {
                chip.addEventListener('click', function() {
                    document.querySelectorAll('.filter-chip').forEach(c => c.classList.remove('active'));
                    this.classList.add('active');
                    const type = this.dataset.type;
                    
                    document.querySelectorAll('.court-item').forEach(item => {
                        const itemType = item.dataset.type;
                        const dist = parseFloat(item.dataset.distance || 999999);
                        let show = (type === 'all') || (type === 'nearby' && dist < 10000) || (itemType === type);
                        item.style.display = show ? 'block' : 'none';
                    });

                    markers.forEach(m => {
                        const dist = L.latLng(userLocation || defaultLocation).distanceTo([m.arenaData.latitude, m.arenaData.longitude]);
                        let show = (type === 'all') || (type === 'nearby' && dist < 10000) || (m.arenaData.type === type);
                        if (show) m.addTo(map); else m.remove();
                    });
                });
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            initMap();
            setupGeolocation();
            setupSearch();
            setupFilters();
        });
    })();
</script>
@endpush
