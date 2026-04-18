<?php
// 1. Query for products - humne image ke saath video column bhi select kiya hai check ke liye
$query = "SELECT a.*, u.full_name, u.address as user_city, a.created_at 
          FROM animals a JOIN users u ON a.user_id = u.id 
          ORDER BY a.id DESC LIMIT 8";
$products = mysqli_query($conn, $query);

// 2. Time Elapsed Helper Function
if (!function_exists('time_elapsed_string')) {
    function time_elapsed_string($datetime, $full = false) {
        $now = new DateTime; $ago = new DateTime($datetime);
        $diff = $now->diff($ago);
        $diff->w = floor($diff->d / 7); $diff->d -= $diff->w * 7;
        $string = array('y' => 'year', 'm' => 'month', 'w' => 'week', 'd' => 'day', 'h' => 'hour', 'i' => 'minute', 's' => 'second');
        foreach ($string as $k => &$v) {
            if ($diff->$k) { $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : ''); } 
            else { unset($string[$k]); }
        }
        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }
}
?>

<section id="products" class="py-5" style="background: #f8fafc;">
    <div class="container py-4">
        
        <div class="row mb-5 align-items-end">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="pulse-indicator"></span>
                    <span class="text-primary fw-800 text-uppercase small" style="letter-spacing: 2px;">Live Marketplace</span>
                </div>
                <h2 class="fw-900 display-5 text-dark mb-0">Fresh <span class="text-primary-gradient">Recommendations</span></h2>
                <p class="text-muted mt-2 fs-5">Hand-picked livestock from verified sellers across the country.</p>
            </div>
            <div class="col-4 text-end">
                <a href="./user/my_products.php" class="btn btn-white shadow-sm rounded-pill px-4 fw-bold border-0">
                    See All <i class="fa-solid fa-chevron-right ms-2 small"></i>
                </a>
            </div>
        </div>

        <div class="row g-4">
            <?php while($row = mysqli_fetch_assoc($products)): 
                // Wishlist Check Logic
                $is_fav = false;
                if(isset($_SESSION['user_id'])) {
                    $uid = $_SESSION['user_id'];
                    $aid = $row['id'];
                    $fav_check = mysqli_query($conn, "SELECT id FROM wishlist WHERE user_id='$uid' AND animal_id='$aid'");
                    if($fav_check && mysqli_num_rows($fav_check) > 0) { $is_fav = true; }
                }

                // Multiple Images Logic
                $imgs = explode(',', $row['image']);
                $display_img = !empty($imgs[0]) ? $imgs[0] : 'default.jpg';
            ?>
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="p-card">
                    <div class="p-media">
                        <img src="uploads/<?= $display_img ?>" class="p-img" alt="<?= $row['title'] ?>">
                        
                        <div class="p-badges">
                            <?php if(!empty($row['video'])): ?>
                                <span class="badge-vid"><i class="fa fa-play"></i> Video</span>
                            <?php endif; ?>
                            <span class="badge-ver">Verified</span>
                        </div>
                        
                        <button class="fav-btn <?= $is_fav ? 'active' : '' ?>" 
                                onclick="event.preventDefault(); event.stopPropagation(); toggleWishlist(this, <?= $row['id'] ?>)">
                            <i class="<?= $is_fav ? 'fa-solid fa-heart' : 'fa-regular fa-heart' ?>"></i>
                        </button>

                        <div class="p-hover-overlay">
                            <i class="fa-solid fa-magnifying-glass-plus text-white mb-2 fs-4"></i>
                            <span class="fw-bold text-white small">Quick View</span>
                        </div>
                    </div>

                    <div class="p-body">
                        <div class="price-row mb-1">
                            <span class="currency">Rs.</span>
                            <span class="amount"><?= number_format($row['price']) ?></span>
                        </div>
                        <h6 class="title"><?= htmlspecialchars($row['title']) ?></h6>
                        
                        <div class="footer-meta">
                            <div class="loc">
                                <i class="fa-solid fa-location-dot"></i>
                                <span><?= htmlspecialchars($row['location'] ?? $row['user_city']) ?></span>
                            </div>
                            <div class="time">
                                <?= time_elapsed_string($row['created_at']) ?>
                            </div>
                        </div>
                    </div>
                    
                    <a href="user/product_details.php?id=<?= $row['id'] ?>" class="stretched-link"></a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<style>
    /* Professional Styling Variables */
    :root {
        --teal: #14b8a6;
        --dark-blue: #0f172a;
        --slate-text: #64748b;
    }

    .fw-800 { font-weight: 800; }
    .text-teal { color: var(--teal); }

    /* Live Tag Animation */
    .live-tag {
        background: #ef4444; color: white; padding: 2px 10px;
        border-radius: 6px; font-size: 0.65rem; font-weight: 900;
        animation: pulse-red 1.5s infinite;
    }
    @keyframes pulse-red {
        0% { opacity: 1; }
        50% { opacity: 0.6; }
        100% { opacity: 1; }
    }

    /* Professional Card Design */
    .p-card {
        background: white; border-radius: 24px; padding: 10px;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid #eef2f6; position: relative;
    }
    .p-card:hover {
        transform: translateY(-12px);
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.08);
        border-color: var(--teal);
    }

    /* Media Styling */
    .p-media {
        position: relative; border-radius: 18px; 
        overflow: hidden; aspect-ratio: 1/1;
        background: #f8fafc;
    }
    .p-img { width: 100%; height: 100%; object-fit: cover; transition: 0.6s; }
    .p-card:hover .p-img { transform: scale(1.1); }

    .p-badges { position: absolute; top: 10px; left: 10px; display: flex; flex-direction: column; gap: 5px; z-index: 2; }
    .badge-vid { background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); color: white; padding: 4px 10px; border-radius: 8px; font-size: 0.65rem; font-weight: 700; }
    .badge-ver { background: rgba(20, 184, 166, 0.85); color: white; padding: 4px 10px; border-radius: 8px; font-size: 0.65rem; font-weight: 700; }

    /* Wishlist Button */
    .fav-btn {
        position: absolute; top: 10px; right: 10px; z-index: 5;
        width: 38px; height: 38px; border-radius: 12px;
        background: white; border: none; display: flex; align-items: center; justify-content: center;
        box-shadow: 0 8px 15px rgba(0,0,0,0.1); transition: 0.3s;
    }
    .fav-btn:hover { transform: scale(1.1); }
    .fav-btn.active { color: #ef4444; }

    /* Content Styling */
    .p-body { padding: 15px 10px 5px 10px; }
    .price-row { display: flex; align-items: baseline; gap: 4px; }
    .currency { color: var(--teal); font-weight: 800; font-size: 0.85rem; }
    .amount { font-size: 1.4rem; font-weight: 900; color: var(--dark-blue); }
    
    .title { 
        color: var(--slate-text); font-weight: 600; font-size: 0.95rem;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        margin-bottom: 15px;
    }

    .footer-meta {
        display: flex; justify-content: space-between; align-items: center;
        padding-top: 12px; border-top: 1px dashed #e2e8f0;
        font-size: 0.75rem; font-weight: 700; color: #94a3b8;
    }
    .loc { display: flex; align-items: center; gap: 5px; }
    .loc i { color: #ef4444; }

    /* Hover Overlay */
    .p-hover-overlay {
        position: absolute; inset: 0; background: rgba(0,0,0,0.3);
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        opacity: 0; transition: 0.3s;
    }
    .p-card:hover .p-hover-overlay { opacity: 1; }
</style>

<script>
// Wishlist Logic Update
function toggleWishlist(btn, animalId) {
    const icon = btn.querySelector('i');
    
    fetch('toggle_wishlist.php?animal_id=' + animalId)
    .then(response => response.json())
    .then(data => {
        if(data.status === 'added') {
            btn.classList.add('active');
            icon.classList.replace('fa-regular', 'fa-solid');
        } else if(data.status === 'removed') {
            btn.classList.remove('active');
            icon.classList.replace('fa-solid', 'fa-regular');
        } else if(data.status === 'login_required') {
            alert('Aapko pehle login karna hoga!');
            window.location.href = 'login.php';
        }
    })
    .catch(err => console.error('Error:', err));
}
</script>