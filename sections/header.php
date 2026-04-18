<?php 
include 'db.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Database Fetching (Logic preserved)
$h_res = mysqli_query($conn, "SELECT * FROM header_settings LIMIT 1");
$h = (mysqli_num_rows($h_res) > 0) ? mysqli_fetch_assoc($h_res) : [];
$nav_links = mysqli_query($conn, "SELECT * FROM nav_links");
$is_user = isset($_SESSION['user_id']);
$u = []; $unread_msg = 0; $unread_notif = 0;

if($is_user) {
    $u_id = $_SESSION['user_id'];
    $u_res = mysqli_query($conn, "SELECT * FROM users WHERE id='$u_id'");
    $u = (mysqli_num_rows($u_res) > 0) ? mysqli_fetch_assoc($u_res) : [];
    
    $m_count = mysqli_query($conn, "SELECT COUNT(*) as total FROM messages WHERE receiver_id='$u_id' AND is_read=0");
    $unread_msg = mysqli_fetch_assoc($m_count)['total'];
    
    $n_count = mysqli_query($conn, "SELECT COUNT(*) as total FROM notifications WHERE user_id='$u_id' AND is_read=0");
    $unread_notif = mysqli_fetch_assoc($n_count)['total'];
}
?>

<nav class="navbar navbar-expand-lg fixed-top pashu-header">
    <div class="container bg-white shadow-lg rounded-pill px-4 py-2 mt-3 navbar-island">
        <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
            <span class="brand-text">
                <b class="text-dark"><?= $h['logo_first'] ?? 'Pulse' ?></b><span class="text-primary-gradient"><?= $h['logo_second'] ?? 'Assoc' ?></span>
            </span>
        </a>

        <form action="./user/search_results.php" method="GET" class="d-none d-lg-flex mx-auto search-container">
            <div class="input-group search-pill">
                <span class="input-group-text bg-transparent border-0 text-muted ps-3">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input type="text" name="q" class="form-control border-0" placeholder="Find animals, breeds...">
                <button class="btn filter-trigger-btn border-0 text-muted" type="button" data-bs-toggle="modal" data-bs-target="#filterModal" title="Advanced Filters">
                    <i class="fa-solid fa-sliders"></i>
                </button>
                <button class="btn btn-primary rounded-pill px-4 m-1 fw-bold search-submit-btn" type="submit">Search</button>
            </div>
        </form>

        <div class="d-flex d-lg-none gap-2 ms-auto me-2 align-items-center">
            <button class="btn btn-light rounded-circle mobile-search-trigger" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="fa-solid fa-sliders text-primary"></i>
            </button>
            <?php if($is_user): ?>
                <div class="mobile-notif-dot bg-danger"></div>
            <?php endif; ?>
        </div>

        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon" style="width: 18px;"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav mx-auto gap-1">
                <?php while($link = mysqli_fetch_assoc($nav_links)): ?>
                    <li class="nav-item">
                        <a class="nav-link custom-link" href="<?= $link['link_path'] ?>"><?= $link['link_name'] ?></a>
                    </li>
                <?php endwhile; ?>
            </ul>

            <div class="d-flex align-items-center gap-2 border-start ps-lg-3 ms-lg-2">
                <?php if($is_user): ?>
                    <div class="d-flex gap-1 me-2 pe-2">
                        <a href="user/messages.php" class="nav-btn-icon" data-bs-toggle="tooltip" title="Messages">
                            <i class="fa-regular fa-paper-plane"></i>
                            <?php if($unread_msg > 0): ?><span class="badge-mini bg-primary"></span><?php endif; ?>
                        </a>
                        <a href="user/notifications.php" class="nav-btn-icon" data-bs-toggle="tooltip" title="Alerts">
                            <i class="fa-regular fa-bell"></i>
                            <?php if($unread_notif > 0): ?><span class="badge-mini bg-danger"></span><?php endif; ?>
                        </a>
                    </div>

                    <div class="dropdown">
                        <div class="profile-trigger d-flex align-items-center" data-bs-toggle="dropdown">
                            <div class="avatar-wrapper">
                                <img src="uploads/<?= !empty($u['profile_pic']) ? $u['profile_pic'] : 'default_user.png' ?>" class="nav-avatar">
                                <div class="online-status"></div>
                            </div>
                            <div class="ms-2 d-none d-xl-block">
                                <p class="u-name-top mb-0"><?= explode(' ', $u['full_name'])[0] ?></p>
                                <p class="u-role-bottom mb-0">Member</p>
                            </div>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end luxury-dropdown border-0 shadow-xl p-2 mt-3">
                            <li class="px-3 py-2 border-bottom mb-2">
                                <span class="d-block fw-bold small"><?= $u['full_name'] ?></span>
                                <span class="text-muted" style="font-size: 11px;"><?= $u['email'] ?></span>
                            </li>
                            <li><a class="dropdown-item" href="user/profile.php"><i class="fa-solid fa-fingerprint me-2"></i> Account</a></li>
                            <li><a class="dropdown-item" href="user/my_products.php"><i class="fa-solid fa-leaf me-2"></i> Listings</a></li>
                            <li><a class="dropdown-item" href="./user/orders.php"><i class="fa-solid fa-receipt me-2"></i> Orders</a></li>
                            <li><a class="dropdown-item fw-bold text-primary" href="./user/seller_orders.php"><i class="fa-solid fa-hand-holding-dollar me-2"></i> Incoming Orders</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="./user/settings.php"><i class="fa-solid fa-gear me-2"></i> Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger fw-bold" href="user/logout.php"><i class="fa-solid fa-arrow-right-from-bracket me-2"></i> Sign Out</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <div class="auth-section d-flex gap-2">
                        <a href="user/login.php" class="btn btn-login-minimal px-3">Login</a>
                        <a href="user/register.php" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm grad-btn">Join Now</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<div class="modal fade" id="filterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-5 border-0 shadow-2xl overflow-hidden">
            <div class="modal-header border-0 bg-primary text-white px-4 py-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white p-2 rounded-4 text-primary"><i class="fa-solid fa-filter fa-lg"></i></div>
                    <div>
                        <h5 class="fw-900 mb-0">Search Filters</h5>
                        <p class="small mb-0 opacity-75">Find the perfect animal for you</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="./user/search_results.php" method="GET">
                <div class="modal-body p-4 bg-light">
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="filter-label"><i class="fa-solid fa-cow me-2 text-primary"></i> Animal Category</label>
                            <select name="category" class="form-select filter-input">
                                <option value="">Select All Categories</option>
                                <option value="cow">Cow (Gai)</option>
                                <option value="goat">Goat (Bakri)</option>
                                <option value="buffalo">Buffalo (Bhains)</option>
                                <option value="sheep">Sheep (Bher)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="filter-label"><i class="fa-solid fa-dna me-2 text-primary"></i> Breed / Nasal</label>
                            <input type="text" name="breed" class="form-control filter-input" placeholder="e.g. Sahiwal">
                        </div>
                        <div class="col-md-6">
                            <label class="filter-label"><i class="fa-solid fa-location-crosshairs me-2 text-primary"></i> Location</label>
                            <input type="text" name="location" class="form-control filter-input" placeholder="Any City">
                        </div>
                        <div class="col-12">
                            <label class="filter-label"><i class="fa-solid fa-tags me-2 text-primary"></i> Budget Range (PKR)</label>
                            <div class="input-group">
                                <input type="number" name="min_p" class="form-control filter-input" placeholder="Min Price">
                                <span class="input-group-text bg-transparent border-0 fw-bold">-</span>
                                <input type="number" name="max_p" class="form-control filter-input" placeholder="Max Price">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 bg-white">
                    <button type="reset" class="btn btn-link text-muted fw-bold text-decoration-none" style="font-size: 14px;">Reset All</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-900 grad-btn shadow">Show Results</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');
    
    :root {
        --p-blue: #2563eb;
        --p-grad: linear-gradient(135deg, #2563eb 0%, #6366f1 100%);
        --p-soft: #f8fafc;
        --p-text: #0f172a;
    }

    body { font-family: 'Plus Jakarta Sans', sans-serif; padding-top: 20px; }
    .fw-900 { font-weight: 900; }
    
    /* Logo Styling */
    .text-primary-gradient {
        background: var(--p-grad);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: 800;
    }

    /* Professional Search Bar */
    .search-container { min-width: 35%; margin: 0 15px; }
    .search-pill { 
        background: #f1f5f9; border-radius: 50px; padding: 3px; 
        border: 1px solid transparent; transition: 0.4s;
    }
    .search-pill:focus-within { 
        background: #fff; border-color: var(--p-blue); 
        box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.15); 
    }
    .search-pill input { font-weight: 600; font-size: 0.9rem; background: transparent !important; }
    .filter-trigger-btn { border-left: 1px solid #e2e8f0 !important; border-radius: 0 !important; }
    .filter-trigger-btn:hover { color: var(--p-blue) !important; }
    .search-submit-btn { transition: 0.3s; }
    .search-submit-btn:hover { transform: scale(1.05); filter: brightness(1.1); }

    /* Nav Island */
    .navbar-island { border: 1px solid #f1f5f9; position: relative; z-index: 1000; }
    
    /* Icon Buttons */
    .nav-btn-icon {
        width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;
        color: #475569; border-radius: 12px; position: relative; transition: 0.2s;
        text-decoration: none; background: #f8fafc;
    }
    .nav-btn-icon:hover { background: #eff6ff; color: var(--p-blue); transform: translateY(-2px); }
    .badge-mini { position: absolute; top: 0; right: 0; width: 10px; height: 10px; border-radius: 50%; border: 2px solid white; }

    /* Profile Section */
    .profile-trigger { cursor: pointer; padding: 4px 10px 4px 4px; border-radius: 50px; transition: 0.3s; border: 1px solid #f1f5f9; }
    .profile-trigger:hover { background: #f8fafc; border-color: #e2e8f0; }
    .avatar-wrapper { position: relative; }
    .nav-avatar { width: 34px; height: 34px; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    .online-status { position: absolute; bottom: 0; right: 0; width: 10px; height: 10px; background: #10b981; border: 2px solid #fff; border-radius: 50%; }
    .u-name-top { font-size: 0.8rem; font-weight: 800; color: var(--p-text); }
    .u-role-bottom { font-size: 10px; color: #64748b; font-weight: 600; }

    /* Filter Modal Styling */
    .filter-label { font-size: 11px; font-weight: 800; text-transform: uppercase; color: #64748b; margin-bottom: 8px; display: block; letter-spacing: 0.5px; }
    .filter-input { 
        border-radius: 14px !important; border: 2px solid #e2e8f0; padding: 10px 15px;
        font-weight: 600; font-size: 0.9rem; transition: 0.3s;
    }
    .filter-input:focus { border-color: var(--p-blue); box-shadow: none; background: #fff; }
    .grad-btn { background: var(--p-grad); border: none; }
    .shadow-2xl { box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); }

    @media (max-width: 991px) {
        .navbar-island { border-radius: 20px; margin: 5px; }
        .search-container { order: 3; width: 100%; margin: 10px 0; }
    }
</style>