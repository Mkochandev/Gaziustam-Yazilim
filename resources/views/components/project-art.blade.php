@props(['category' => 'software'])
@switch($category)
    @case('apps')
        <div class="art-mobile" aria-hidden="true">
            <span class="art-watermark">ora.</span>
            <div class="device device-back">
                <div class="device-camera"></div>
                <div class="mobile-greeting">Your daily space.</div>
                <div class="mobile-rings"><i></i><b><x-studio-star /></b></div>
                <div class="mobile-bottom"><span>Mindful moments.</span><span>↗</span></div>
            </div>
            <div class="device device-front">
                <div class="device-camera"></div>
                <div class="app-top"><b>ora.</b><span>≡</span></div>
                <div class="mobile-greeting">Make room<br>for yourself.</div>
                <div class="app-orb"></div>
                <div class="app-now"><small>YOUR NEXT MOMENT</small><b>A little more balance.</b><span>05:00 <i>▶</i></span></div>
                <div class="app-nav">⌂ <span>◎</span> ♡ ◯</div>
            </div>
            <span class="art-caption">MOBILE EXPERIENCE / CONCEPT</span>
        </div>
        @break
    @case('commerce')
        <div class="art-shop" aria-hidden="true">
            <span class="art-watermark">objects.</span>
            <div class="shop-window">
                <div class="shop-nav"><b>objects.</b><span>Collection &nbsp; About &nbsp; Bag (0)</span></div>
                <div class="shop-main">
                    <div class="shop-copy"><small>CURATED FOR EVERYDAY LIVING</small><h4>Good design.<br>Better living.</h4><span>Explore collection ↗</span></div>
                    <img src="{{ asset('images/orange-chair.webp') }}" width="1448" height="1086" alt="" loading="lazy">
                </div>
                <div class="shop-bottom"><span>01 — A NEW PERSPECTIVE</span><span>DESIGNED TO STAY.</span></div>
            </div>
            <span class="art-caption">COMMERCE EXPERIENCE / CONCEPT</span>
        </div>
        @break
    @default
        <div class="art-dashboard" aria-hidden="true">
            <div class="dashboard-window">
                <div class="dash-side">
                    <b>forma<span><x-studio-star /></span></b>
                    <span class="dash-label">WORKSPACE</span>
                    <span class="dash-active">◈ &nbsp; Overview</span><span>▥ &nbsp; Projects</span>
                    <span>◯ &nbsp; Customers</span><span>▤ &nbsp; Documents</span>
                    <span class="dash-settings">⚙ &nbsp; Settings</span>
                    <div class="dash-avatar"><i>F</i><small>Forma Studio<br>Pro workspace</small></div>
                </div>
                <div class="dash-main">
                    <div class="dash-top"><span>Workspace / Overview</span><span>⌕ &nbsp; ◉</span></div>
                    <div class="dash-heading">
                        <div><small>MONDAY, NOVEMBER 09</small><h4>A clear view.<br>A better workflow.</h4></div>
                        <span class="dash-new">+ New project</span>
                    </div>
                    <div class="dash-stats">
                        <div><small>Active projects</small><b>24 <i>↗</i></b></div>
                        <div><small>Pending reviews</small><b>08 <i>◷</i></b></div>
                        <div><small>This week</small><b>12 <i>✓</i></b></div>
                    </div>
                    <div class="dash-graph">
                        <div><b>Project activity</b><span>Weekly ↓</span></div>
                        <div class="graph-bars">
                            @foreach([26, 44, 35, 63, 52, 76, 65, 88, 72, 97, 85, 100] as $height)
                                <i style="height:{{ $height }}%"></i>
                            @endforeach
                        </div>
                        <div class="graph-axis"><span>MON</span><span>TUE</span><span>WED</span><span>THU</span><span>FRI</span></div>
                    </div>
                    <div class="dash-task"><span><i></i> Website relaunch</span><small>In review</small><span>↗</span></div>
                </div>
            </div>
            <span class="art-caption">DIGITAL WORKSPACE / CONCEPT</span>
        </div>
@endswitch
